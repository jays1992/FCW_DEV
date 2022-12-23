<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Admin\TblMstUser;
use Auth;
use DB;
use Session;
use Response;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Helpers\Utils;

class TrnFrm60Controller extends Controller
{
    protected $form_id = 60;
    protected $vtid_ref   = 60;  
    protected $view         = "transactions.Purchase.RequestForQuotation.trnfrm60";

   
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

   
    public function index(){    

        $FormId = $this->form_id;

        $objRights = DB::table('TBL_MST_USERROLMAP')
                        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                        
                        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                        ->first();

        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF'); 
        
        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList	=	DB::select("select hdr.RFQID,hdr.RFQ_NO,hdr.RFQ_DT,hdr.REMARKS,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.RFQID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                            ) AS CREATED_BY,
                            hdr.STATUS, sl.SLNAME,
                            case when a.ACTIONNAME = '$FANO' then 'Final Approved' 
                            else case when a.ACTIONNAME = 'ADD' then 'Added'  
                                when a.ACTIONNAME = 'EDIT' then 'Edited'
                                when a.ACTIONNAME = 'APPROVAL1' then 'First Level Approved'
                                when a.ACTIONNAME = 'APPROVAL2' then 'Second Level Approved'
                                when a.ACTIONNAME = 'APPROVAL3' then 'Third Level Approved'
                                when a.ACTIONNAME = 'APPROVAL4' then 'Fourth Level Approved'
                                when a.ACTIONNAME = 'APPROVAL5' then 'Final Approved'
                                when a.ACTIONNAME = 'CANCEL' then 'Cancelled'
                                when a.ACTIONNAME = 'CLOSE' then 'Closed'
                            end end as STATUS_DESC
                            from TBL_TRN_AUDITTRAIL a 
                            inner join TBL_TRN_RQFQ01_HDR hdr
                            on a.VID = hdr.RFQID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_SUBLEDGER sl ON hdr.VID_REF = sl.SGLID  
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.RFQID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );
                            
        return view($this->view,compact(['REQUEST_DATA','objRights','FormId','objDataList']));        
    }

    public function getVendor(Request $request){

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $CODE       =   $request['CODE'];
        $NAME       =   $request['NAME'];
    
        $sp_popup = [
            $CYID_REF, $BRID_REF,$CODE,$NAME
        ]; 
        
        $ObjData = DB::select('EXEC sp_get_vendor_popup_enquiry ?,?,?,?', $sp_popup);

   
        if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){

                $VID    =   $dataRow->SGLID;
                $VCODE  =   $dataRow->SGLCODE;
                $NAME   =   $dataRow->SLNAME;
                
               
                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_VID_REF[]" id="vendoridcode_'.$index.'"  class="clsvendorid" value="'.$VID.'" ></td>
                <td class="ROW2">'.$VCODE.'<input type="hidden" id="txtvendoridcode_'.$index.'" data-desc="'.$VCODE.'-'.$NAME.'" value="'.$VID.'" > </td>
                <td class="ROW3">'.$NAME.'</td>
                </tr>';

                echo $row;

            }
    
        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();
    
    }

   
    public function add(){   
          
        $FormId = $this->form_id;
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $today = Date('Y-m-d');

        
        $objlastRFQ_DT = DB::select('SELECT MAX(RFQ_DT) RFQ_DT FROM TBL_TRN_RQFQ01_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_RQFQ01_HDR',
            'HDR_ID'=>'RFQID',
            'HDR_DOC_NO'=>'RFQ_NO',
            'HDR_DOC_DT'=>'RFQ_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
        
        
        $objCOMPANY = DB::table('TBL_MST_COMPANY')
        ->where('CYID','=',$CYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_COMPANY.*')
        ->first();   

        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
      
    return view($this->view.'add', compact(['AlpsStatus','FormId','objlastRFQ_DT','objCOMPANY','TabSetting','doc_req','docarray']));       
   }

   

   public function getdepartment(Request $request){

    $Status = "A";
    $CYID_REF = Auth::user()->CYID_REF;
    $BRID_REF = Session::get('BRID_REF');
    $FYID_REF = Session::get('FYID_REF');

    
    
                
        $ObjData =  DB::select('SELECT DEPID, DCODE, NAME FROM TBL_MST_DEPARTMENT  
                WHERE STATUS= ?  AND (DEACTIVATED=0 OR DEACTIVATED IS NULL) order by DCODE ASC', [ $Status]);

       
 

        if(!empty($ObjData)){

            foreach ($ObjData as $index=>$dataRow){
            
                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_DEPID_REF[]" id="dept_'.$dataRow->DEPID .'"  class="clsdept" value="'.$dataRow->DEPID.'" ></td>
                <td class="ROW2">'.$dataRow->DCODE;
                $row = $row.'<input type="hidden" id="txtdept_'.$dataRow->DEPID.'" data-desc="'.$dataRow->DCODE .' - ';
                $row = $row.$dataRow->NAME. '" value="'.$dataRow->DEPID.'"/></td>
                <td class="ROW3">'.$dataRow->NAME.'</td></tr>';

                echo $row;
            }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();

    }


    public function getMstItems(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $AlpsStatus =   $this->AlpsStatus();
      
        $CODE       =   $request['CODE'];
        $NAME       =   $request['NAME'];
        $MUOM       =   $request['MUOM'];
        $GROUP      =   $request['GROUP'];
        $CTGRY      =   $request['CTGRY'];
        $BUNIT      =   $request['BUNIT'];
        $APART      =   $request['APART'];
        $CPART      =   $request['CPART'];
        $OPART      =   $request['OPART'];
      
        $sp_popup = [
            $CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART
        ]; 
        
        $ObjItem = DB::select('EXEC sp_get_items_popup_enquiry ?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);
                
        $row        =   '';
      
        if(!empty($ObjItem)){
            foreach ($ObjItem as $index=>$dataRow){
            
                $ITEMID             =   isset($dataRow->ITEMID)?$dataRow->ITEMID:NULL;
                $ICODE              =   isset($dataRow->ICODE)?$dataRow->ICODE:NULL;
                $NAME               =   isset($dataRow->NAME)?$dataRow->NAME:NULL;
                $ITEM_SPECI         =   isset($dataRow->ITEM_SPECI)?$dataRow->ITEM_SPECI:NULL;
                $MAIN_UOMID_REF     =   isset($dataRow->MAIN_UOMID_REF)?$dataRow->MAIN_UOMID_REF:NULL;
                $Main_UOM           =   isset($dataRow->Main_UOM)?$dataRow->Main_UOM:NULL;
                $ALT_UOMID_REF      =   isset($dataRow->ALT_UOMID_REF)?$dataRow->ALT_UOMID_REF:NULL;
                $Alt_UOM            =   isset($dataRow->Alt_UOM)?$dataRow->Alt_UOM:NULL;
                $FROMQTY            =   isset($dataRow->FROMQTY)?$dataRow->FROMQTY:NULL;
                $TOQTY              =   isset($dataRow->TOQTY)?$dataRow->TOQTY:NULL;
                $STDCOST            =   isset($dataRow->STDCOST)?$dataRow->STDCOST:NULL;
                $GroupName          =   isset($dataRow->GroupName)?$dataRow->GroupName:NULL;
                $Categoryname       =   isset($dataRow->Categoryname)?$dataRow->Categoryname:NULL;
                $BusinessUnit       =   isset($dataRow->BusinessUnit)?$dataRow->BusinessUnit:NULL;
                $ALPS_PART_NO       =   isset($dataRow->ALPS_PART_NO)?$dataRow->ALPS_PART_NO:NULL;
                $CUSTOMER_PART_NO   =   isset($dataRow->CUSTOMER_PART_NO)?$dataRow->CUSTOMER_PART_NO:NULL;
                $OEM_PART_NO        =   isset($dataRow->OEM_PART_NO)?$dataRow->OEM_PART_NO:NULL;

                
               

            $tmpMRSNO="";
            $total_balance_qty  =   '0.000';
            $itemSpec           =   trim($ITEM_SPECI);


            $row = '';
            $row = $row.'<tr id="item_'.$ITEMID .'"  class="clsitemid"><td  style="width:10%; text-align: center;">
                <input type="hidden" id="MRSNOitem_'.$ITEMID.'"  value="'.$tmpMRSNO.'"  >
                <input type="checkbox" id="chkId'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1"  >
                <input type="hidden" id="txtrecordId_'.$ITEMID.'"  value="'.$ITEMID.'"   >
                <input type="hidden" name="itemcode_'.$ITEMID.'" id="txtitemcode_'.$ICODE.'"  value="'.$ICODE.'" data-desc="'.$ICODE.' "   >
                </td>';
            $row = $row.'<td style="width:10%;">'.$ICODE;
            $row = $row.'<input type="hidden" id="txtitem_'.$ITEMID.'" data-desc="'.$ICODE.'" value="'.$ITEMID.'"/></td>
            <td style="width:10%;" id="itemname_'.$ITEMID.'" >'.$NAME;
            $row = $row.'<input type="hidden" id="txtitemname_'.$ITEMID.'" data-desc="'.$NAME.'"
            value="'.$NAME.'"/></td>';
            $row = $row.'<td style="width:10%;" id="itemuom_'.$ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ITEMID.'" data-desc="'.$Main_UOM.'" 
            data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>';
            $row = $row.'<td style="width:10%;" id="itemspec_'.$ITEMID.'" ><input type="hidden" id="txtitemspec_'.$ITEMID.'" data-desc="'.$itemSpec.'" value="'.$itemSpec.'"/>'.$itemSpec.'</td>';
           
            $row = $row.'<td style="width:10%;" id="balpiqty_'.$ITEMID.'"><input type="hidden" id="txtbalpiqty_'.$ITEMID.'" data-desc="'.$total_balance_qty.'" value="'.$total_balance_qty.'"/>'.$total_balance_qty.'</td>
            
            <td style="width:10%;">'.$BusinessUnit.'</td>
            <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
            <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
            <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
            
            ';
            $row = $row.'</tr>';
            echo $row; 
            
        }

                               
        }           
        else{
            echo '<tr><td colspan="12"> Record not found.</td></tr>';
        }
      
        exit();
      }

    
   

   public function getItemDetails(Request $request){

    $PI_ID = $request['piid'];
    $Status = 'A';
    $CYID_REF = Auth::user()->CYID_REF;
    $BRID_REF = Session::get('BRID_REF');
    $FYID_REF = Session::get('FYID_REF');
    $DEPARTMENTID = $request["deptid"];

    $AlpsStatus =   $this->AlpsStatus();
            
   
    $ObjItem = DB::table('TBL_TRN_PRIN02_MAT')                    
        ->leftJoin('TBL_MST_ITEM','TBL_MST_ITEM.ITEMID','=','TBL_TRN_PRIN02_MAT.ITEMID_REF')                
        ->leftJoin('TBL_MST_UOM','TBL_MST_UOM.UOMID','=','TBL_TRN_PRIN02_MAT.UOMID_REF')                
        ->select( 
            'TBL_TRN_PRIN02_MAT.*',
            'TBL_MST_ITEM.ITEMID',
            'TBL_MST_ITEM.ICODE',
            'TBL_MST_ITEM.NAME',
            'TBL_MST_ITEM.ITEM_SPECI AS MST_ITEM_SPECI',
            'TBL_TRN_PRIN02_MAT.ITEMSPECI',
            'TBL_MST_UOM.UOMID',
            'TBL_MST_UOM.UOMCODE',
            'TBL_MST_UOM.DESCRIPTIONS'
        )
        ->where('TBL_TRN_PRIN02_MAT.PIID_REF','=',$PI_ID)
        ->where('TBL_MST_ITEM.DEACTIVATED','<>',1)
        ->orderBy('TBL_TRN_PRIN02_MAT.PIMATID','ASC')
        ->get();

    //dd($ObjItem);

      

        $ObjItem2 = $ObjItem;

        foreach($ObjItem as $index=>$dataRow){


            if($dataRow->MRSNO =="" || $dataRow->MRSNO ==NULL || $dataRow->MRSNO ==0){
                $objQty2 = DB::select(" select  ISNULL(sum(INDENT_QTY),0) AS TOTAL_QTY from  TBL_TRN_PRIN02_MAT
                WHERE PIID_REF=? AND ITEMID_REF=? AND UOMID_REF=?", [$dataRow->PIID_REF,$dataRow->ITEMID_REF,$dataRow->UOMID_REF]);
            }
            else{
                $objQty2 = DB::select(" select  ISNULL(sum(INDENT_QTY),0) AS TOTAL_QTY from  TBL_TRN_PRIN02_MAT
                WHERE PIID_REF=? AND ITEMID_REF=? AND UOMID_REF=? AND MRSNO=?  ", [$dataRow->PIID_REF,$dataRow->ITEMID_REF,$dataRow->UOMID_REF,$dataRow->MRSNO]);
            }

            $ObjItem2[$index]->BAL_PIQTY = $objQty2[0]->TOTAL_QTY;   
             
        }


        
  
        
      
        foreach($ObjItem2 as $index=>$dataRow){

            if($dataRow->MRSNO =="" || $dataRow->MRSNO ==NULL || $dataRow->MRSNO ==0){
                $objConsumed = DB::select("select ISNULL(sum(table1.RFQ_QTY),0) as TOTAL_CONSUMED_QTY from TBL_TRN_RQFQ01_MAT table1
                left join TBL_TRN_RQFQ01_HDR table2 on table1.RFQID_REF = table2.RFQID 
                WHERE table1.PINO=? AND table1.ITEMID_REF=? AND table1.UOMID_REF=?
                " , [$dataRow->PIID_REF, $dataRow->ITEMID_REF,$dataRow->UOMID_REF]);
            }
            else{
                $objConsumed = DB::select("select ISNULL(sum(table1.RFQ_QTY),0) as TOTAL_CONSUMED_QTY from TBL_TRN_RQFQ01_MAT table1
                left join TBL_TRN_RQFQ01_HDR table2 on table1.RFQID_REF = table2.RFQID 
                WHERE table1.PINO=? AND table1.ITEMID_REF=? AND table1.UOMID_REF=? AND table1.MRSID_REF=?
                " , [$dataRow->PIID_REF, $dataRow->ITEMID_REF,$dataRow->UOMID_REF,$dataRow->MRSNO]);
            }
           
            $total_balance_qty = number_format(floatVal($dataRow->BAL_PIQTY ) -  floatval($objConsumed[0]->TOTAL_CONSUMED_QTY), 3, '.', '');
           
            $ObjItem2[$index]->BAL_PIQTY = $total_balance_qty ;   

         }


       

    

    if(!empty($ObjItem2)){

        foreach ($ObjItem2 as $index=>$dataRow){

            $tmpPIID_REF = $dataRow->PIID_REF;
            $tmpITEMID_REF = $dataRow->ITEMID_REF;
            $tmpUOMID_REF = $dataRow->UOMID_REF ;
            $tmpMRSNO   = $dataRow->MRSNO ;


            $ItemRowData =  DB::select('SELECT top 1 * FROM TBL_MST_ITEM  WHERE ITEMID = ? ', [$dataRow->ITEMID_REF]);

            if(!is_null($ItemRowData[0]->BUID_REF)){
                $ObjBusinessUnit =  DB::select('SELECT TOP 1  * FROM TBL_MST_BUSINESSUNIT  
                WHERE  CYID_REF = ? AND BRID_REF = ?  AND BUID = ?', 
                [$CYID_REF, $BRID_REF, $ItemRowData[0]->BUID_REF]);
            }
            else
            {
                $ObjBusinessUnit = NULL;
            }

            $BusinessUnit       =   isset($ObjBusinessUnit) && $ObjBusinessUnit != NULL ? $ObjBusinessUnit[0]->BUCODE.'-'.$ObjBusinessUnit[0]->BUNAME : '';
            $ALPS_PART_NO       =   $ItemRowData[0]->ALPS_PART_NO;
            $CUSTOMER_PART_NO   =   $ItemRowData[0]->CUSTOMER_PART_NO;
            $OEM_PART_NO        =   $ItemRowData[0]->OEM_PART_NO;


           
            $CustomId = $tmpPIID_REF.'-'.$tmpITEMID_REF.'-'.$tmpUOMID_REF.'-'.$tmpMRSNO;  
            

            //$total_balance_qty = number_format(floatval($dataRow->BAL_PIQTY), 3, '.', '');  

            $total_balance_qty = number_format(floatval($dataRow->PENDING_QTY), 3, '.', '');  


            //dd($dataRow);
            
            
            
            
        
            $itemSpec = trim($dataRow->ITEMSPECI)=="" ? trim($dataRow->MST_ITEM_SPECI) : trim($dataRow->ITEMSPECI) ;
            if(floatval($total_balance_qty)>floatval(0.000)){
                $row = '';
                $row = $row.'<tr id="item_'.$CustomId .'"  class="clsitemid"><td  style="width:10%; text-align: center;">
                    <input type="hidden" id="MRSNOitem_'.$CustomId.'"  value="'.$tmpMRSNO.'"  >
                    <input type="checkbox" id="chkId'.$CustomId.'"  value="'.$CustomId.'" class="js-selectall1"  >
                    <input type="hidden" id="txtrecordId_'.$CustomId.'"  value="'.$CustomId.'"   >
                    <input type="hidden" name="itemcode_'.$CustomId.'" id="txtitemcode_'.$dataRow->ICODE.'"  value="'.$dataRow->ICODE.'" data-desc="'.$dataRow->ICODE.' "   >
                    </td>';
                $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                $row = $row.'<input type="hidden" id="txtitem_'.$CustomId.'" data-desc="'.$dataRow->ICODE.'" value="'.$dataRow->ITEMID.'"/></td>
                <td style="width:10%;" id="itemname_'.$CustomId.'" >'.$dataRow->NAME;
                $row = $row.'<input type="hidden" id="txtitemname_'.$CustomId.'" data-desc="'.$dataRow->NAME.'"
                    value="'.$dataRow->NAME.'"/></td>';
                $row = $row.'<td style="width:10%;" id="itemuom_'.$CustomId.'" ><input type="hidden" id="txtitemuom_'.$CustomId.'" data-desc="'.$dataRow->UOMCODE.'-'.$dataRow->DESCRIPTIONS.'" 
                data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" value="'.$dataRow->UOMID.'"/>'.$dataRow->UOMCODE.'-'.$dataRow->DESCRIPTIONS.'</td>';
                $row = $row.'<td style="width:10%;" id="itemspec_'.$CustomId.'" ><input type="hidden" id="txtitemspec_'.$CustomId.'" data-desc="'.$itemSpec.'" value="'.$itemSpec.'"/>'.$itemSpec.'</td>';
               
                $row = $row.'<td style="width:10%;" id="balpiqty_'.$CustomId.'"><input type="hidden" id="txtbalpiqty_'.$CustomId.'" data-desc="'.$total_balance_qty.'" value="'.$total_balance_qty.'"/>'.$total_balance_qty.'</td>
                
                <td style="width:10%;">'.$BusinessUnit.'</td>
                <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                
                ';
                $row = $row.'</tr>';
                echo $row;  

            }
            else{
                echo '<tr><td> Record not found.</td></tr>';
            }    
        } 
        
        
    }           
    else{
        echo '<tr><td> Record not found.</td></tr>';
    }  
    exit();                      
    
}


 
   public function attachment($id){
    $FormId = $this->form_id;

    if(!is_null($id))
    {
        $objMst = DB::table("TBL_TRN_RQFQ01_HDR")
                    ->where('RFQID','=',$id)
                    ->select('*')
                    ->first();        

        $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
                    ->where('VTID','=',$this->vtid_ref)
                    ->select('VTID','VCODE','DESCRIPTIONS','INDATE')
                    ->get()
                    ->toArray();
            
                  
                    $objAttachments = DB::table('TBL_MST_ATTACHMENT')                    
                        ->where('TBL_MST_ATTACHMENT.VTID_REF','=',$this->vtid_ref)
                        ->where('TBL_MST_ATTACHMENT.ATTACH_DOCNO','=',$id)
                        ->where('TBL_MST_ATTACHMENT.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_ATTACHMENT.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_MST_ATTACHMENT.FYID_REF','=',Session::get('FYID_REF'))
                        ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                        ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                        ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                        ->get()->toArray();
                        
            return view($this->view.'attachment',compact(['FormId','objMst','objMstVoucherType','objAttachments']));
    }

}

    
   public function save(Request $request) {
    
       
        
        $r_count1 = $request['Row_Count1'];

 
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]))
            {

                $req_data[$i] = [
                    
                    'PINO'      => isset( $request['PIID_'.$i]) &&  (!is_null($request['PIID_'.$i]) ) ? $request['PIID_'.$i] : 0,
                    'ITEMID_REF' => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'  => $request['MAIN_UOMID_REF_'.$i],
                    'ITEMSPECI'  => $request['Itemspec_'.$i],
                    'PI_QTY'    => $request['BAL_PIQTY_'.$i],
                    'RFQ_QTY' => $request['RFQ_QTY_'.$i],
                    'REMARKS'    => $request['REMARKS_'.$i],
                    'MRSID_REF'    => $request['MRSNO_'.$i],
                ];
            }
        }
        
        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
    
       

            $VTID_REF     =   $this->vtid_ref;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();

            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');

            $RFQ_NO = $request['RFQ_NO'];
            $RFQ_DT = $request['RFQ_DT'];
            $DEPID_REF = $request['DEPID_REF'];
            $VID_REF = $request['VID_REF'];
            $REMARKS = $request['REMARKS'];
            $DIRECT_RFQ = (isset($request['DIRECT_RFQ']) )? 1 : 0 ; 

    

            $log_data = [ 
                $RFQ_NO,     $RFQ_DT,   $DEPID_REF,    $VID_REF,    $REMARKS, $DIRECT_RFQ,
                $CYID_REF,  $BRID_REF,  $FYID_REF,     $VTID_REF,   $XMLMAT,        
                $USERID,    Date('Y-m-d'),  Date('h:i:s.u'),$ACTIONNAME,    $IPADDRESS
            ];

            
            
            $sp_result = DB::select('EXEC SP_RFQ_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
            

            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            exit();   
     }

    public function getPI(Request $request){

        $Status = "A";
        $id = $request['id'];
        $fieldid    = $request['fieldid'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $data_array1 = [];
        $ObjData =  DB::select('SELECT * FROM TBL_TRN_PRIN02_HDR  
                    WHERE CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? 
                     AND STATUS = ? AND DEPID_REF=?', 
                    [$CYID_REF, $BRID_REF, $FYID_REF, $Status, $id]);



     

            $found_array1 = [];
            if(!empty($ObjData)){
                
                foreach ($ObjData as $index=>$dataRow){
                   
                    $objPIMat =  DB::select("select table1.PIMATID,	table1.PIID_REF,	table1.ITEMID_REF,	table1.UOMID_REF,	table1.INDENT_QTY AS QTY from TBL_TRN_PRIN02_MAT table1 left join TBL_MST_ITEM table2 on table1.ITEMID_REF = table2.ITEMID WHERE (table2.DEACTIVATED is NULL OR table2.DEACTIVATED=0) AND table1.PIID_REF=?" ,[$dataRow->PIID]);

                    $data_array1=[];
                    

                    if(!empty($objPIMat) ){
                       
                        $data_array1=[];

                        foreach($objPIMat as $index=>$row1){

                            $TOTAL_QTY = number_format(floatVal($row1->QTY), 3, '.', '');  
                            
                            $objConsumed = DB::select("select ISNULL(sum(table1.RFQ_QTY),0) as TOTAL_CONSUMED_QTY
                            from TBL_TRN_RQFQ01_MAT table1 left join TBL_TRN_RQFQ01_HDR table2 on table1.RFQID_REF = table2.RFQID 
                            WHERE table1.PINO=? AND table1.ITEMID_REF=? AND table1.UOMID_REF=? AND table2.STATUS=? " , [$row1->PIID_REF, $row1->ITEMID_REF,$row1->UOMID_REF,'A']);
        
                            $CONSUMED_TOTAL = number_format(floatVal($objConsumed[0]->TOTAL_CONSUMED_QTY), 3, '.', '');  
                            
                            if($TOTAL_QTY > $CONSUMED_TOTAL ){
                                $data_array1[$index]= true;
                            }else{
                                $data_array1[$index] = false;   
                            }
                         

                        }
                      
                        if (in_array(true, $data_array1)) 
                        { 
                            $row = '';
                            $row = $row.'<tr >
                            <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="picode_'.$dataRow->PIID .'"  class="clsspiid" value="'.$dataRow->PIID.'" ></td>
                            <td class="ROW2">'.$dataRow->PI_NO;
                            $row = $row.'<input type="hidden" id="txtpicode_'.$dataRow->PIID.'" data-desc="'.$dataRow->PI_NO.'" value="'.$dataRow->PIID.'"/></td>
                            <td class="ROW3">'.$dataRow->PI_DT.'</td></tr>';
                            echo $row;
                        } 

                    }
                    
                }
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    
    }

    


    



    public function edit($id=NULL){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id))
        {
        
        $FormId = $this->form_id;
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $USERID = Auth::user()->USERID;
        $VTID       =   $this->vtid_ref;

        $sp_user_approval_req = [
            $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
        ];        

        //get user approval data
        $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
        $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

        $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',$BRID_REF)
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

       
        $objDepartment = DB::table('TBL_MST_DEPARTMENT')
                ->where('DEACTIVATED','=',NULL)
                ->orWhere('DEACTIVATED','<>',1)
                ->where('STATUS','=',$Status)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->select('*')
                ->get()
                ->toArray();
        
        
        $objMstResponse = DB::table('TBL_TRN_RQFQ01_HDR')
                            ->where('FYID_REF','=',Session::get('FYID_REF'))
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('BRID_REF','=',Session::get('BRID_REF'))
                            ->where('RFQID','=',$id)
                            ->select('*')
                            ->first();

        $objvendorcode2 =array();
        if(isset($objMstResponse->VID_REF) && $objMstResponse->VID_REF !=""){
            $objvendorcode2 = DB::table('TBL_MST_SUBLEDGER')
            ->where('BELONGS_TO','=','Vendor')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('SGLID','=',$objMstResponse->VID_REF)    
            ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
            ->first();
        }





        $objdeptcode2 =[];
        if(isset($objMstResponse->DEPID_REF) && $objMstResponse->DEPID_REF !=""){
            $objdeptcode2 = DB::table('TBL_MST_DEPARTMENT')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('DEPID','=',$objMstResponse->DEPID_REF)
                ->select('DEPID','DCODE','NAME')
                ->first();  
        }  


        $objList1 = DB::table('TBL_TRN_RQFQ01_MAT')                    
            ->where('TBL_TRN_RQFQ01_MAT.RFQID_REF','=',$id)
            ->leftJoin('TBL_TRN_RQFQ01_HDR','TBL_TRN_RQFQ01_MAT.RFQID_REF','=','TBL_TRN_RQFQ01_HDR.RFQID')                
            ->leftJoin('TBL_TRN_PRIN02_HDR','TBL_TRN_RQFQ01_MAT.PINO','=','TBL_TRN_PRIN02_HDR.PIID')                
            ->leftJoin('TBL_MST_ITEM','TBL_TRN_RQFQ01_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                
            ->leftJoin('TBL_MST_UOM','TBL_TRN_RQFQ01_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                
            ->select( 
                'TBL_TRN_RQFQ01_MAT.*',
                'TBL_TRN_RQFQ01_HDR.RFQID',
                'TBL_TRN_RQFQ01_HDR.RFQ_NO',
                'TBL_TRN_RQFQ01_HDR.RFQ_DT',
                'TBL_TRN_RQFQ01_HDR.DIRECT_RFQ',
                'TBL_TRN_PRIN02_HDR.PI_NO',
                'TBL_MST_ITEM.ITEMID',
                'TBL_MST_ITEM.ICODE',
                'TBL_MST_ITEM.NAME',
                'TBL_MST_UOM.UOMID',
                'TBL_MST_UOM.UOMCODE',
                'TBL_MST_UOM.DESCRIPTIONS',
                'TBL_MST_ITEM.ALPS_PART_NO',
                'TBL_MST_ITEM.CUSTOMER_PART_NO',
                'TBL_MST_ITEM.OEM_PART_NO',
            )
            ->orderBy('TBL_TRN_RQFQ01_MAT.RFQMATID','ASC')
            ->get()->toArray();

            
            $ObjItem2 = $objList1;
            foreach($objList1 as $index=>$dataRow){
               

                if($dataRow->MRSID_REF =="" || $dataRow->MRSID_REF ==NULL || $dataRow->MRSID_REF ==0){
                    $WhereMrs="";
                }
                else{
                    $WhereMrs="AND	MRSNO=$dataRow->MRSID_REF";
                }

                if($dataRow->PINO =="" || $dataRow->PINO ==NULL || $dataRow->PINO ==0){
                    $WherePIID="";
                }
                else{
                    $WherePIID="AND PIID_REF=$dataRow->PINO";
                }

                $ObjActPenQTY =  DB::select("select TOP 1 PENDING_QTY from TBL_TRN_PRIN02_MAT where 
                ITEMID_REF=$dataRow->ITEMID_REF $WherePIID AND	UOMID_REF=$dataRow->UOMID_REF $WhereMrs");
                if(!empty($ObjActPenQTY)){
                    $total_pen_qty = 0;
                    $total_pen_qty = number_format(floatVal($dataRow->RFQ_QTY ) +  floatval($ObjActPenQTY[0]->PENDING_QTY), 3, '.', '');
                    $ObjItem2[$index]->BAL_PIQTY = $total_pen_qty;
                }

            }

            

        $objList1Count = count($ObjItem2);
        if($objList1Count==0){
            $objList1Count=1;
        }

        $objRFQMAT = DB::table('TBL_TRN_RQFQ01_MAT')                    
                            ->where('RFQID_REF','=',$id)
                            ->select('*')
                            ->orderBy('RFQMATID','ASC')
                            ->get()->toArray();
        $objCount1 = count($objRFQMAT);  
        
        $objCOMPANY = DB::table('TBL_MST_COMPANY')
        ->where('CYID','=',$CYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_COMPANY.*')
        ->first(); 
        
  
        $objlastRFQ_DT = DB::select('SELECT MAX(RFQ_DT) RFQ_DT FROM TBL_TRN_RQFQ01_HDR WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
        
        $AlpsStatus =   $this->AlpsStatus();
        $ActionStatus="";

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        return view($this->view.'edit', compact(['AlpsStatus','FormId','objRights','user_approval_level','objCOMPANY',
        'objMstResponse','objvendorcode2','objlastRFQ_DT','ObjItem2','objList1Count','objdeptcode2','ActionStatus','TabSetting'])); 

        }
     
    } 
     
    public function view($id=NULL){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id))
        {
        
        $FormId = $this->form_id;
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $USERID = Auth::user()->USERID;
        $VTID       =   $this->vtid_ref;

        $sp_user_approval_req = [
            $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
        ];        

        //get user approval data
        $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
        $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

        $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',$BRID_REF)
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

       
        $objDepartment = DB::table('TBL_MST_DEPARTMENT')
                ->where('DEACTIVATED','=',NULL)
                ->orWhere('DEACTIVATED','<>',1)
                ->where('STATUS','=',$Status)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->select('*')
                ->get()
                ->toArray();
        
        
        $objMstResponse = DB::table('TBL_TRN_RQFQ01_HDR')
                            ->where('FYID_REF','=',Session::get('FYID_REF'))
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('BRID_REF','=',Session::get('BRID_REF'))
                            ->where('RFQID','=',$id)
                            ->select('*')
                            ->first();

        $objvendorcode2 =array();
        if(isset($objMstResponse->VID_REF) && $objMstResponse->VID_REF !=""){
            $objvendorcode2 = DB::table('TBL_MST_SUBLEDGER')
            ->where('BELONGS_TO','=','Vendor')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('SGLID','=',$objMstResponse->VID_REF)    
            ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
            ->first();
        }





        $objdeptcode2 =[];
        if(isset($objMstResponse->DEPID_REF) && $objMstResponse->DEPID_REF !=""){
            $objdeptcode2 = DB::table('TBL_MST_DEPARTMENT')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('DEPID','=',$objMstResponse->DEPID_REF)
                ->select('DEPID','DCODE','NAME')
                ->first();  
        }  


        $objList1 = DB::table('TBL_TRN_RQFQ01_MAT')                    
            ->where('TBL_TRN_RQFQ01_MAT.RFQID_REF','=',$id)
            ->leftJoin('TBL_TRN_RQFQ01_HDR','TBL_TRN_RQFQ01_MAT.RFQID_REF','=','TBL_TRN_RQFQ01_HDR.RFQID')                
            ->leftJoin('TBL_TRN_PRIN02_HDR','TBL_TRN_RQFQ01_MAT.PINO','=','TBL_TRN_PRIN02_HDR.PIID')                
            ->leftJoin('TBL_MST_ITEM','TBL_TRN_RQFQ01_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                
            ->leftJoin('TBL_MST_UOM','TBL_TRN_RQFQ01_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                
            ->select( 
                'TBL_TRN_RQFQ01_MAT.*',
                'TBL_TRN_RQFQ01_HDR.RFQID',
                'TBL_TRN_RQFQ01_HDR.RFQ_NO',
                'TBL_TRN_RQFQ01_HDR.RFQ_DT',
                'TBL_TRN_RQFQ01_HDR.DIRECT_RFQ',
                'TBL_TRN_PRIN02_HDR.PI_NO',
                'TBL_MST_ITEM.ITEMID',
                'TBL_MST_ITEM.ICODE',
                'TBL_MST_ITEM.NAME',
                'TBL_MST_UOM.UOMID',
                'TBL_MST_UOM.UOMCODE',
                'TBL_MST_UOM.DESCRIPTIONS',
                'TBL_MST_ITEM.ALPS_PART_NO',
                'TBL_MST_ITEM.CUSTOMER_PART_NO',
                'TBL_MST_ITEM.OEM_PART_NO',
            )
            ->orderBy('TBL_TRN_RQFQ01_MAT.RFQMATID','ASC')
            ->get()->toArray();

            
            $ObjItem2 = $objList1;
            foreach($objList1 as $index=>$dataRow){
               

                if($dataRow->MRSID_REF =="" || $dataRow->MRSID_REF ==NULL || $dataRow->MRSID_REF ==0){
                    $WhereMrs="";
                }
                else{
                    $WhereMrs="AND	MRSNO=$dataRow->MRSID_REF";
                }

                if($dataRow->PINO =="" || $dataRow->PINO ==NULL || $dataRow->PINO ==0){
                    $WherePIID="";
                }
                else{
                    $WherePIID="AND PIID_REF=$dataRow->PINO";
                }

                $ObjActPenQTY =  DB::select("select TOP 1 PENDING_QTY from TBL_TRN_PRIN02_MAT where 
                ITEMID_REF=$dataRow->ITEMID_REF $WherePIID AND	UOMID_REF=$dataRow->UOMID_REF $WhereMrs");
                if(!empty($ObjActPenQTY)){
                    $total_pen_qty = 0;
                    $total_pen_qty = number_format(floatVal($dataRow->RFQ_QTY ) +  floatval($ObjActPenQTY[0]->PENDING_QTY), 3, '.', '');
                    $ObjItem2[$index]->BAL_PIQTY = $total_pen_qty;
                }

            }

            

        $objList1Count = count($ObjItem2);
        if($objList1Count==0){
            $objList1Count=1;
        }

        $objRFQMAT = DB::table('TBL_TRN_RQFQ01_MAT')                    
                            ->where('RFQID_REF','=',$id)
                            ->select('*')
                            ->orderBy('RFQMATID','ASC')
                            ->get()->toArray();
        $objCount1 = count($objRFQMAT);  
        
        $objCOMPANY = DB::table('TBL_MST_COMPANY')
        ->where('CYID','=',$CYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_COMPANY.*')
        ->first(); 
        
  
        $objlastRFQ_DT = DB::select('SELECT MAX(RFQ_DT) RFQ_DT FROM TBL_TRN_RQFQ01_HDR WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
        
        $AlpsStatus =   $this->AlpsStatus();
        $ActionStatus="disabled";

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        return view($this->view.'view', compact(['AlpsStatus','FormId','objRights','user_approval_level','objCOMPANY',
        'objMstResponse','objvendorcode2','objlastRFQ_DT','ObjItem2','objList1Count','objdeptcode2','ActionStatus','TabSetting'])); 

        }
     
    }

   
   public function update(Request $request){
        
            
            $r_count1 = $request['Row_Count1'];
        
            for ($i=0; $i<=$r_count1; $i++)
            {
                if(isset($request['ITEMID_REF_'.$i]))
                {
    
                    $req_data[$i] = [
                        
                        'PINO'      => isset( $request['PIID_'.$i]) &&  (!is_null($request['PIID_'.$i]) ) ? $request['PIID_'.$i] : 0,
                        'ITEMID_REF' => $request['ITEMID_REF_'.$i],
                        'UOMID_REF'  => $request['MAIN_UOMID_REF_'.$i],
                        'ITEMSPECI'  => $request['Itemspec_'.$i],
                        'PI_QTY'    => $request['BAL_PIQTY_'.$i],
                        'RFQ_QTY' => $request['RFQ_QTY_'.$i],
                        'REMARKS'    => $request['REMARKS_'.$i],
                        'MRSID_REF'    => $request['MRSNO_'.$i],
                    ];
                }
            }
            
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        
         
    
                $VTID_REF     =   $this->vtid_ref;
                $USERID = Auth::user()->USERID;   
                $ACTIONNAME = 'EDIT';
                $IPADDRESS = $request->getClientIp();
    
                $CYID_REF = Auth::user()->CYID_REF;
                $BRID_REF = Session::get('BRID_REF');
                $FYID_REF = Session::get('FYID_REF');
    
                $RFQ_NO = $request['RFQ_NO'];
                $RFQ_DT = $request['RFQ_DT'];
                $DEPID_REF = $request['DEPID_REF'];
                $VID_REF = $request['VID_REF'];
                $REMARKS = $request['REMARKS'];
                $DIRECT_RFQ = (isset($request['DIRECT_RFQ']) )? 1 : 0 ; 
    
               
    
                $log_data = [ 
                    $RFQ_NO,     $RFQ_DT,   $DEPID_REF,    $VID_REF,    $REMARKS, $DIRECT_RFQ,
                    $CYID_REF,  $BRID_REF,  $FYID_REF,     $VTID_REF,   $XMLMAT,        
                    $USERID,    Date('Y-m-d'),  Date('h:i:s.u'),$ACTIONNAME,    $IPADDRESS
                ];
    
              
                
                $sp_result = DB::select('EXEC SP_RFQ_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
                
             
        
              $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
              if($contains){
                  return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);
  
              }else{
                  return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
              }
            exit();   
    }


    public function Approve(Request $request){
       
  
          $USERID =   Auth::user()->USERID;
          $VTID_REF   =   $this->vtid_ref;  //voucher type id
          $CYID_REF = Auth::user()->CYID_REF;
          $BRID_REF = Session::get('BRID_REF');
          $FYID_REF = Session::get('FYID_REF');        
          
          $IPADDRESS = $request->getClientIp();
  
         
          if($request['DIRECT_RFQ']==0){

                $r_count1 = $request['Row_Count1'];
        
                for ($i=0; $i<=$r_count1; $i++)
                {
                    if(isset($request['ITEMID_REF_'.$i]))
                    {
        
                        $reqData[$i] = [
                            
                            'PINO'      => isset( $request['PIID_'.$i]) &&  (!is_null($request['PIID_'.$i]) ) ? $request['PIID_'.$i] : 0,
                            'ITEMID_REF' => $request['ITEMID_REF_'.$i],
                            'UOMID_REF'  => $request['MAIN_UOMID_REF_'.$i],
                            'ITEMSPECI'  => $request['Itemspec_'.$i],
                            'PI_QTY'    => $request['BAL_PIQTY_'.$i],
                            'RFQ_QTY' => $request['RFQ_QTY_'.$i],
                            'REMARKS'    => $request['REMARKS_'.$i],
                            'MRSID_REF'    => $request['MRSNO_'.$i],
                            'INFO_LABEL'      => isset( $request['PI_popup_'.$i]) &&  (!is_null($request['PI_popup_'.$i]) ) ? $request['PI_popup_'.$i] : "",
                        ];
                    }
                }
  
                
                foreach($reqData as $index=>$dataRow){
        
                    $objQty = DB::select(" select ISNULL(sum(INDENT_QTY),0) as ACTUAL_QTY FROM TBL_TRN_PRIN02_MAT WHERE PIID_REF=? AND ITEMID_REF=? AND UOMID_REF=?" , [$dataRow["PINO"], $dataRow["ITEMID_REF"],$dataRow["UOMID_REF"] ]);
        
                    $reqData[$index]["ACTUAL_QTY"] = number_format(floatval($objQty[0]->ACTUAL_QTY), 3, '.', '');    
                    
                    $objConsumed = DB::select("select ISNULL(sum(table1.RFQ_QTY),0) as TOTAL_CONSUMED_QTY from TBL_TRN_RQFQ01_MAT table1 left join TBL_TRN_RQFQ01_HDR table2 on table1.RFQID_REF = table2.RFQID  WHERE table1.PINO=? AND table1.ITEMID_REF=? AND table1.UOMID_REF=? AND table2.STATUS=? " , [$dataRow["PINO"], $dataRow["ITEMID_REF"],$dataRow["UOMID_REF"],'A']);
        
                    $reqData[$index]["TOTAL_CONSUMED_QTY"] = number_format(floatval($objConsumed[0]->TOTAL_CONSUMED_QTY), 3, '.', '');    
        
                }   
  
               

                foreach($reqData as $index=>$row1){
        
                    $ACTUAL_QTY = number_format(floatVal($row1["ACTUAL_QTY"]), 3, '.', '');
                    $TOTAL_NEW_QTY = number_format(floatVal($row1["TOTAL_CONSUMED_QTY"]) + floatVal($row1["RFQ_QTY"]) , 3, '.', '');
                    
                    if($TOTAL_NEW_QTY > $ACTUAL_QTY){
                        return Response::json(['errors'=>true,'save'=>'invalid','msg' => 'RFQ Qty is greater than Balance PI Qty. Please check '.$row1["INFO_LABEL"]]);
                    }   
                }   

        }
          
  
  
          $sp_Approvallevel = [
              $USERID, $VTID_REF, $CYID_REF,$BRID_REF,
              $FYID_REF            
          ];
         
          $sp_listing_result = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?, ?', $sp_Approvallevel);
      
          if(!empty($sp_listing_result))
          {
              foreach ($sp_listing_result as $key=>$approw)
              {  
                  $record_status = 0;
                  $Approvallevel = "APPROVAL".$approw->LAVELS;
              }
          }
   
          $ACTIONNAME     =  $Approvallevel;        
         
          
             
              $r_count1 = $request['Row_Count1'];
        
              for ($i=0; $i<=$r_count1; $i++)
              {
                  if(isset($request['ITEMID_REF_'.$i]))
                  {
      
                      $req_data[$i] = [
                          
                          'PINO'      => isset( $request['PIID_'.$i]) &&  (!is_null($request['PIID_'.$i]) ) ? $request['PIID_'.$i] : 0,
                          'ITEMID_REF' => $request['ITEMID_REF_'.$i],
                          'UOMID_REF'  => $request['MAIN_UOMID_REF_'.$i],
                          'ITEMSPECI'  => $request['Itemspec_'.$i],
                          'PI_QTY'    => $request['BAL_PIQTY_'.$i],
                          'RFQ_QTY' => $request['RFQ_QTY_'.$i],
                          'REMARKS'    => $request['REMARKS_'.$i],
                          'MRSID_REF'    => $request['MRSNO_'.$i],
                      ];
                  }
              }
              
              $wrapped_links["MAT"] = $req_data; 
              $XMLMAT = ArrayToXml::convert($wrapped_links);
          
            
      
                  $RFQ_NO = $request['RFQ_NO'];
                  $RFQ_DT = $request['RFQ_DT'];
                  $DEPID_REF = $request['DEPID_REF'];
                  $VID_REF = $request['VID_REF'];
                  $REMARKS = $request['REMARKS'];
                  $DIRECT_RFQ = (isset($request['DIRECT_RFQ']) )? 1 : 0 ; 
      
                  
      
                  $log_data = [ 
                      $RFQ_NO,     $RFQ_DT,   $DEPID_REF,    $VID_REF,    $REMARKS, $DIRECT_RFQ,
                      $CYID_REF,  $BRID_REF,  $FYID_REF,     $VTID_REF,   $XMLMAT,        
                      $USERID,    Date('Y-m-d'),  Date('h:i:s.u'),$ACTIONNAME,    $IPADDRESS
                  ];
      
                  $sp_result = DB::select('EXEC SP_RFQ_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);    
          
                  if($sp_result[0]->RESULT=="SUCCESS"){
  
                      return Response::json(['success' =>true,'msg' => 'Record successfully Approved.']);
  
                  }else{
                      return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
                  }
                  exit();      
        }

    public function MultiApprove(Request $request){

            $USERID_REF =   Auth::user()->USERID;
            $VTID_REF   =   $this->vtid_ref; 
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');
            $FYID_REF   =   Session::get('FYID_REF');   
    
            $sp_Approvallevel = [
                $USERID_REF, $VTID_REF, $CYID_REF,$BRID_REF,
                $FYID_REF
            ];
            
            $sp_listing_result = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?, ?', $sp_Approvallevel);
    
            if(!empty($sp_listing_result))
                {
                    foreach ($sp_listing_result as $key=>$salesenquiryitem)
                {  
                    $record_status = 0;
                    $Approvallevel = "APPROVAL".$salesenquiryitem->LAVELS;
                }
                }
            
                
                $req_data =  json_decode($request['ID']);

               
                $wrapped_links = $req_data; 
                $multi_array = $wrapped_links;
                $iddata = [];
                
                foreach($multi_array as $index=>$row)
                {
                    $m_array[$index] = $row->ID;
                    $iddata['APPROVAL'][]['ID'] =  $row->ID;
                }
                $xml = ArrayToXml::convert($iddata);
                
                $USERID_REF =   Auth::user()->USERID;
                $VTID_REF   =   $this->vtid_ref; 
                $CYID_REF   =   Auth::user()->CYID_REF;
                $BRID_REF   =   Session::get('BRID_REF');
                $FYID_REF   =   Session::get('FYID_REF');       
                $TABLE      =   "TBL_TRN_RQFQ01_HDR";
                $FIELD      =   "RFQID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_RFQ ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);    
            
            
            
    
            if($sp_result[0]->RESULT=="All records approved"){
    
            return Response::json(['approve' =>true,'msg' => 'Record successfully Approved.']);
    
            }elseif($sp_result[0]->RESULT=="RFQ QTY must be less or equale than Indent balance QTY."){
            
                return Response::json(['errors'=>true,'msg' => 'RFQ QTY must be less or equale than Indent balance QTY.','save'=>'invalid']);
            
            }elseif($sp_result[0]->RESULT=="NO RECORD FOR APPROVAL"){
            
                return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','salesenquiry'=>'norecord']);
            
            }else{

                return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','salesenquiry'=>'Some Error']);
            }
            
            exit();    
            }

    
   public function cancel(Request $request){

        $id = $request->{0};

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_TRN_RQFQ01_HDR";
        $FIELD      =   "RFQID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
         'NT'  => 'TBL_TRN_RQFQ01_MAT',
        ];
       
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $sp_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_RFQ  ?,?,?,?, ?,?,?,?, ?,?,?,?', $sp_cancel_data);

        if($sp_result[0]->RESULT=="CANCELED"){  

            return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
        
            return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'invalid'=>'invalid']);
        }
        
        exit(); 
    }

  
  

   

   public function docuploads(Request $request){

    $formData = $request->all();

    $FormId = $this->form_id;

    $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
    $allow_size = config("erpconst.attachments.max_size") * 1024 * 1024;

   
    $VTID           =   $formData["VTID_REF"]; 
    $ATTACH_DOCNO   =   $formData["ATTACH_DOCNO"]; 
    $ATTACH_DOCDT   =   $formData["ATTACH_DOCDT"]; 
    $CYID_REF       =   Auth::user()->CYID_REF;
    $BRID_REF       =   Session::get('BRID_REF');
    $FYID_REF       =   Session::get('FYID_REF');       
   
    $USERID         =   Auth::user()->USERID;
    $UPDATE         =   Date('Y-m-d');
    $UPTIME         =   Date('h:i:s.u');
    $ACTION         =   "ADD";
    $IPADDRESS      =   $request->getClientIp();
    
    $image_path         =   "docs/company".$CYID_REF."/RequestForQuotation";     
		$destinationPath    =   str_replace('\\', '/', public_path($image_path));

    if ( !is_dir($destinationPath) ) {
        mkdir($destinationPath, 0777, true);
    }

    $uploaded_data = [];
    $invlid_files = "";

    $duplicate_files="";

    foreach($formData["REMARKS"] as $index=>$row_val){

            if(isset($formData["FILENAME"][$index])){

                $uploadedFile = $formData["FILENAME"][$index]; 
                
                

                $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
                $filesize               =   $uploadedFile ->getSize();  
                $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );

               

                $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".$filenamewithextension;  
                
                echo $filenametostore ;

                if ($uploadedFile->isValid()) {

                    if(in_array($extension,$allow_extnesions)){
                        
                        if($filesize < $allow_size){

                            $custfilename = $destinationPath."/".$filenametostore;

                            if (!file_exists($custfilename)) {

                               $uploadedFile->move($destinationPath, $filenametostore);  //upload in dir if not exists
                               $uploaded_data[$index]["FILENAME"] =$filenametostore;
                               $uploaded_data[$index]["LOCATION"] = $image_path."/";
                               $uploaded_data[$index]["REMARKS"] = is_null($row_val) ? '' : trim($row_val);

                            }else{

                                $duplicate_files = " ". $duplicate_files.$filenamewithextension. " ";
                            }
                            

                            
                        }else{
                            
                            $invlid_files = $invlid_files.$filenamewithextension." (invalid size)  "; 
                        } //invalid size
                        
                    }else{

                        $invlid_files = $invlid_files.$filenamewithextension." (invalid extension)  ";                             
                    }// invalid extension
                
                }else{
                        
                    $invlid_files = $invlid_files.$filenamewithextension." (invalid)"; 
                }//invalid

            }

    }//foreach

  
    if(empty($uploaded_data)){
        return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
    }
 
    $wrapped_links["ATTACHMENT"] = $uploaded_data;     
    $ATTACHMENTS_XMl = ArrayToXml::convert($wrapped_links);

    $attachment_data = [

        $VTID, 
        $ATTACH_DOCNO, 
        $ATTACH_DOCDT,
        $CYID_REF,
        
        $BRID_REF,
        $FYID_REF,
        $ATTACHMENTS_XMl,
        $USERID,

        $UPDATE,
        $UPTIME,
        $ACTION,
        $IPADDRESS
    ];
    
      
   try {

        
         $sp_result = DB::select('EXEC SP_TRN_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);
         

   } catch (\Throwable $th) {
    
       return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

    public function checkse(Request $request){

       
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $ENQNO = $request->ENQNO;
        
        $objSE = DB::table('TBL_TRN_SLEQ01_HDR')
        ->where('TBL_TRN_SLEQ01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLEQ01_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_SLEQ01_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_SLEQ01_HDR.ENQNO','=',$ENQNO)
        ->select('TBL_TRN_SLEQ01_HDR.SEQID')
        ->first();
        
        if($objSE){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate Enquiry No.']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }

    
    
}
