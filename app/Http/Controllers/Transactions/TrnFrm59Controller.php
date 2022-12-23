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

class TrnFrm59Controller extends Controller
{
    protected $form_id = 59;
    protected $vtid_ref   = 59;
    protected $view         = "transactions.Purchase.PurchaseIndent.trnfrm59";

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

        $objDataList	=	DB::select("select hdr.PIID,hdr.PI_NO,hdr.PI_DT,hdr.remarks,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.PIID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                            ) AS CREATED_BY,
                            hdr.STATUS,
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
                            inner join TBL_TRN_PRIN02_HDR hdr
                            on a.VID = hdr.PIID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.PIID DESC ");

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

    public function add(){   
          
        $FormId = $this->form_id;
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $today = Date('Y-m-d');
        
       
        $objStore = DB::table('TBL_MST_STORE')
            ->where('DEACTIVATED','=',NULL)
            ->orWhere('DEACTIVATED','<>',1)
            ->where('STATUS','=',$Status)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->select('TBL_MST_STORE.*')
            ->get()
            ->toArray();

      
        $objlastPIDT = DB::select('SELECT MAX(PI_DT) PI_DT FROM TBL_TRN_PRIN02_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_PRIN02_HDR',
            'HDR_ID'=>'PIID',
            'HDR_DOC_NO'=>'PI_NO',
            'HDR_DOC_DT'=>'PI_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
        
         
        $objCOMPANY = DB::table('TBL_MST_COMPANY')
        ->where('CYID','=',$CYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_COMPANY.*')
        ->first();  
        
        $AlpsStatus =   $this->AlpsStatus();
        
        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

      
    return view($this->view.'add', compact(['AlpsStatus','FormId','objStore','objlastPIDT','objCOMPANY','TabSetting','doc_req','docarray']));       
   }

   

   public function getdepartment(Request $request){
    $Status = "A";
    

    $ObjData =  DB::select('SELECT DEPID, DCODE, NAME FROM TBL_MST_DEPARTMENT  
                WHERE STATUS= ? AND (DEACTIVATED=0 OR DEACTIVATED IS NULL) order by DCODE ASC', [$Status]);

        if(!empty($ObjData)){

        foreach ($ObjData as $index=>$dataRow){
        
            $row = '';
            $row = $row.'<tr >
            <td class="ROW1"> <input type="checkbox" name="SELECT_DEPID_REF[]" id="dept_'.$dataRow->DEPID .'"  class="clsdept" value="'.$dataRow->DEPID.'" ></td>
            <td class="ROW2">'.$dataRow->DCODE;
            $row = $row.'<input type="hidden" id="txtdept_'.$dataRow->DEPID.'" data-desc="'.$dataRow->DCODE .' - ';
            $row = $row.$dataRow->NAME. '" value="'.$dataRow->DEPID.'"/></td><td class="ROW3">'.$dataRow->NAME.'</td></tr>';

            echo $row;
        }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();

    }


    public function getMstItems(Request $request){        
        $taxstate = $request['taxstate'];
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $StdCost = 0;
        $Taxid = [];
        $CODE = $request['CODE'];
        $NAME = $request['NAME'];
        $MUOM = $request['MUOM'];
        $GROUP = $request['GROUP'];
        $CTGRY = $request['CTGRY'];
        $BUNIT = $request['BUNIT'];
        $APART = $request['APART'];
        $CPART = $request['CPART'];
        $OPART = $request['OPART'];

        $STID_REF = $request['STID_REF'];

        $AlpsStatus =   $this->AlpsStatus();
        

        $sp_popup = [
            $CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART
        ]; 
        
            $ObjItem = DB::select('EXEC sp_get_items_popup_enquiry ?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);

           
            
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
                        
                        
                        $row = '';
                        
                        $CustomId           =   $ITEMID;  
                        $total_balance_qty  =   '0.000';   
                        $itemSpec           =   trim($ITEM_SPECI);
 

                        $FROMQTY = DB::select("SELECT 
                        ISNULL(SUM(CURRENT_QTY),0) AS CURRENT_QTY 
                        FROM TBL_MST_STOCK 
                        WHERE STID_REF='$STID_REF' AND ITEMID_REF='$ITEMID' AND UOMID_REF='$MAIN_UOMID_REF' 
                        AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF'  AND STATUS='A'")[0]->CURRENT_QTY;

                        
                        $row = '';
                        $row = $row.'<tr id="item_'.$CustomId .'"  class="clsitemid"><td  style="width:10%; text-align: center;">
                            <input type="checkbox" id="chkId'.$CustomId.'"  value="'.$CustomId.'" class="js-selectall1"  >
                            <input type="hidden" id="txtrecordId_'.$CustomId.'"  value="'.$CustomId.'"   >
                            <input type="hidden" name="itemcode_'.$CustomId.'" id="txtitemcode_'.$ICODE.'"  value="'.$ICODE.'" data-desc="'.$ICODE.' "   >
                            </td>';
                        $row = $row.'<td style="width:9%;">'.$ICODE;
                        $row = $row.'<input type="hidden" id="txtitem_'.$CustomId.'" data-desc="'.$ICODE.'" value="'.$ITEMID.'"/></td>
                        <td style="width:9%;" id="itemname_'.$CustomId.'" >'.$NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$CustomId.'" data-desc="'.$NAME.'"
                            value="'.$NAME.'"/></td>';
                        $row = $row.'<td style="width:9%;" id="itemuom_'.$CustomId.'" ><input type="hidden" id="txtitemuom_'.$CustomId.'" data-desc="'.$Main_UOM.'"
                        data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'"  data-desc4="'.$OEM_PART_NO.'" value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>';
                        $row = $row.'<td style="width:9%;" id="itemspec_'.$CustomId.'" ><input type="hidden" id="txtitemspec_'.$CustomId.'" data-desc="'.$ITEM_SPECI.'" value="'.$ITEM_SPECI.'"/>'.$ITEM_SPECI.'</td>';
                        
                        $row = $row.'<td style="width:9%;" id="stockqty_'.$CustomId.'" ><input type="hidden" id="txtstockqty_'.$CustomId.'" data-desc="'.$FROMQTY.'"   value="'.$FROMQTY.'"/>'.$FROMQTY.'</td>';
                        $row = $row.'<td style="width:9%;" id="balmrsqty_'.$CustomId.'"><input type="hidden" id="txtbalmrsqty_'.$CustomId.'" data-desc="'.$total_balance_qty.'" value="'.$total_balance_qty.'"/>'.$total_balance_qty.'</td>
                        
                        <td style="width:9%;">'.$BusinessUnit.'</td>
                        <td style="width:9%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                        <td style="width:9%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                        <td style="width:9%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                        ';
                        
                        $row = $row.'</tr>';
                        echo $row;


                    } 
                    
                    
                }           
                else{
                    echo '<tr><td> Record not found.</td></tr>';
                }
        exit();
    }
    
   

    public function getItemDetails(Request $request){

        $MRS_ID = $request['mrsid'];
        $Status = 'A';
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $STOREID = $request["storeid"];

        $AlpsStatus =   $this->AlpsStatus();
       
                
       
        $ObjItem = DB::table('TBL_TRN_MRQS01_MAT')                    
            ->leftJoin('TBL_MST_ITEM','TBL_MST_ITEM.ITEMID','=','TBL_TRN_MRQS01_MAT.ITEMID_REF')                
            ->leftJoin('TBL_MST_UOM','TBL_MST_UOM.UOMID','=','TBL_TRN_MRQS01_MAT.MAIN_UOMID_REF')                
            ->select( 
                'TBL_TRN_MRQS01_MAT.*',
                'TBL_MST_ITEM.ITEMID',
                'TBL_MST_ITEM.ICODE',
                'TBL_MST_ITEM.NAME',
                'TBL_MST_ITEM.ITEM_SPECI AS MST_ITEM_SPECI',
                'TBL_TRN_MRQS01_MAT.ITEM_SPECI',
                'TBL_MST_UOM.UOMCODE',
                'TBL_MST_UOM.DESCRIPTIONS'
            )
            ->where('TBL_TRN_MRQS01_MAT.MRSID_REF','=',$MRS_ID)
            ->where('TBL_MST_ITEM.DEACTIVATED','<>',1)
            ->orderBy('TBL_TRN_MRQS01_MAT.MRS_MATID','ASC')
            ->get();

          

            $ObjItem2 = $ObjItem;
            foreach($ObjItem as $index=>$dataRow){
               
               $objQty = DB::select(" select ISNULL(sum(CURRENT_QTY),0) as CURRENT_QTY from TBL_MST_STOCK 
               WHERE STID_REF=? AND ITEMID_REF=? AND UOMID_REF=? AND CYID_REF=? AND BRID_REF=?  AND STATUS=?" ,
               [$STOREID, $dataRow->ITEMID,$dataRow->MAIN_UOMID_REF,$CYID_REF,$BRID_REF,$Status]);

               $ObjItem2[$index]->STOCK_QTY =number_format(floatval($objQty[0]->CURRENT_QTY), 3, '.', '');

              



                
            }    

          
            foreach($ObjItem as $index=>$dataRow){
               
               $objMRSQty = DB::select(" select  ISNULL(sum(QTY),0) AS TOTAL_QTY from  TBL_TRN_MRQS01_MAT
                    WHERE MRSID_REF=? AND ITEMID_REF=? AND MAIN_UOMID_REF=?  ", [$dataRow->MRSID_REF,$dataRow->ITEMID_REF,$dataRow->MAIN_UOMID_REF]);

                $ObjItem2[$index]->BAL_MRSQTY = $objMRSQty[0]->TOTAL_QTY;                
            }
      
         
            foreach($ObjItem2 as $index=>$dataRow){
               

                $objConsumed = DB::select("select ISNULL(sum(table1.INDENT_QTY),0) as TOTAL_CONSUMED_QTY from TBL_TRN_PRIN02_MAT table1 left join TBL_TRN_PRIN02_HDR table2 on table1.PIID_REF = table2.PIID  WHERE table1.MRSNO=? AND table1.ITEMID_REF=? AND table1.UOMID_REF=? AND table2.STATUS=? " , [$dataRow->MRSID_REF, $dataRow->ITEMID_REF,$dataRow->MAIN_UOMID_REF,'A']);
                
                $total_balance_qty = number_format(floatVal($ObjItem2[$index]->BAL_MRSQTY ) -  floatval($objConsumed[0]->TOTAL_CONSUMED_QTY), 3, '.', '');
               
                $ObjItem2[$index]->BAL_MRSQTY = $total_balance_qty ;   


             }
       
      


        if(!empty($ObjItem2)){

            foreach ($ObjItem2 as $index=>$dataRow){

                $tmpMRSID_REF = $dataRow->MRSID_REF;
                $tmpITEMID_REF = $dataRow->ITEMID_REF;
                $tmpMAIN_UOMID_REF = $dataRow->MAIN_UOMID_REF ;

                $CustomId = '0'.$tmpMRSID_REF.$tmpITEMID_REF.$tmpMAIN_UOMID_REF; 
                

                $total_balance_qty = number_format(floatval($dataRow->BAL_MRSQTY), 3, '.', '');  
                
                


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

                
            
                $itemSpec = trim($dataRow->ITEM_SPECI)=="" ? trim($dataRow->MST_ITEM_SPECI) : trim($dataRow->ITEM_SPECI) ;
                if(floatval($total_balance_qty)>floatval(0.000)){
                    $row = '';
                    $row = $row.'<tr id="item_'.$CustomId .'"  class="clsitemid"><td  style="width:10%; text-align: center;">
                        <input type="checkbox" id="chkId'.$CustomId.'"  value="'.$CustomId.'" class="js-selectall1"  >
                        <input type="hidden" id="txtrecordId_'.$CustomId.'"  value="'.$CustomId.'"   >
                        <input type="hidden" name="itemcode_'.$CustomId.'" id="txtitemcode_'.$dataRow->ICODE.'"  value="'.$dataRow->ICODE.'" data-desc="'.$dataRow->ICODE.' "   >
                        </td>';
                    $row = $row.'<td style="width:9%;">'.$dataRow->ICODE;
                    $row = $row.'<input type="hidden" id="txtitem_'.$CustomId.'" data-desc="'.$dataRow->ICODE.'" value="'.$dataRow->ITEMID.'"/></td>
                    <td style="width:9%;" id="itemname_'.$CustomId.'" >'.$dataRow->NAME;
                    $row = $row.'<input type="hidden" id="txtitemname_'.$CustomId.'" data-desc="'.$dataRow->NAME.'"
                        value="'.$dataRow->NAME.'"/></td>';
                    $row = $row.'<td style="width:9%;" id="itemuom_'.$CustomId.'" ><input type="hidden" id="txtitemuom_'.$CustomId.'" data-desc="'.$dataRow->UOMCODE.'-'.$dataRow->DESCRIPTIONS.'" 
                    data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'"  data-desc4="'.$OEM_PART_NO.'" 
                    value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$dataRow->UOMCODE.'-'.$dataRow->DESCRIPTIONS.'</td>';
                    $row = $row.'<td style="width:9%;" id="itemspec_'.$CustomId.'" ><input type="hidden" id="txtitemspec_'.$CustomId.'" data-desc="'.$itemSpec.'" value="'.$itemSpec.'"/>'.$itemSpec.'</td>';
                    
                    $row = $row.'<td style="width:9%;" id="stockqty_'.$CustomId.'" ><input type="hidden" id="txtstockqty_'.$CustomId.'" data-desc="'.$dataRow->STOCK_QTY.'"   value="'.$dataRow->STOCK_QTY.'"/>'.$dataRow->STOCK_QTY.'</td>';
                    $row = $row.'<td style="width:9%;" id="balmrsqty_'.$CustomId.'"><input type="hidden" id="txtbalmrsqty_'.$CustomId.'" data-desc="'.$total_balance_qty.'" value="'.$total_balance_qty.'"/>'.$total_balance_qty.'</td>
                    
                    <td style="width:9%;">'.$BusinessUnit.'</td>
                    <td style="width:9%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                    <td style="width:9%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                    <td style="width:9%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                    ';
                    $row = $row.'</tr>';
                    echo $row;  
                }    
            } 
            
            
        }           
        else{
            echo '<tr><td> Record not found.</td></tr>';
        }  
        exit();                      
        
    }


    public function getaltuomqty(Request $request){
        $id = $request['id'];
        $itemid = $request['itemid'];
        $mqty = $request['mqty'];

    
        $ObjData =  DB::select('SELECT top 1 TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV  
                    WHERE  ITEMID_REF = ? AND TO_UOMID_REF =?', [$itemid,$id]);
    
         
                if(!empty($ObjData)){
                $auomqty = ($mqty/$ObjData[0]->FROM_QTY)*($ObjData[0]->TO_QTY);
                echo($auomqty);
    
                }else{
                    echo '0';
                }
                exit();
    
        }

    public function getAltUOM(Request $request){
        $id = $request['id'];

        $ObjData =  DB::select('SELECT TO_UOMID_REF FROM TBL_MST_ITEM_UOMCONV  
                WHERE ITEMID_REF= ?  order by IUCID ASC', [$id]);

        if(!empty($ObjData)){

        foreach ($ObjData as $index=>$dataRow){

            $ObjAltUOM =  DB::select('SELECT top 1 UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                WHERE UOMID= ?  ', [$dataRow->TO_UOMID_REF]);
        
            $row = '';
            $row = $row.'<tr id="altuom_'.$dataRow->TO_UOMID_REF .'"  class="clsaltuom"><td width="50%">'.$ObjAltUOM[0]->UOMCODE;
            $row = $row.'<input type="hidden" id="txtaltuom_'.$dataRow->TO_UOMID_REF.'" data-desc="'.$ObjAltUOM[0]->UOMCODE .' - ';
            $row = $row.$ObjAltUOM[0]->DESCRIPTIONS. '" value="'.$dataRow->TO_UOMID_REF.'"/></td><td>'.$ObjAltUOM[0]->DESCRIPTIONS.'</td></tr>';

            echo $row;
        }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();
    
    }

    
   public function attachment($id){
    $FormId = $this->form_id;

    if(!is_null($id))
    {
        $objMst = DB::table("TBL_TRN_PRIN02_HDR")
                    ->where('PIID','=',$id)
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
    
    

        $Status = 'A';

        
        $r_count1 = $request['Row_Count1'];
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]))
            {
                $newdt2=!(is_null($request['EDA_'.$i]) || empty($request['EDA_'.$i]) )=="true" ? $request['EDA_'.$i] : NULL;                 
                if(!is_null($newdt2) ){
                    $newdt2 = str_replace( "/", "-",  $newdt2 ) ;
                    $newDateString2 = Carbon::parse($newdt2)->format('Y-m-d');        
                    $EDA_DT = $newDateString2;
                }else{
                    $EDA_DT = NULL;
                }

                $req_data[$i] = [
                    
                    'MRSNO'      => isset( $request['MrsID_'.$i]) &&  (!is_null($request['MrsID_'.$i]) ) ? $request['MrsID_'.$i] : 0,
                    'ITEMID_REF' => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'  => $request['MAIN_UOMID_REF_'.$i],
                    'ITEMSPECI'  => $request['Itemspec_'.$i],
                    'SIH'        => $request['STOCK_QTY_'.$i],
                    'MRS_QTY'    => $request['BAL_MRSQTY_'.$i],
                    'INDENT_QTY' => $request['INDENT_QTY_'.$i],
                    'EDA'        => $EDA_DT,
                    'APPROX_VL'  => $request['APPROX_VAL_'.$i],
                    'REMARKS'    => $request['REMARKS_'.$i],
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

            $PI_NO = $request['PI_NO'];
            $PI_DT = $request['PI_DT'];
            $STID_REF = $request['STID_REF'];
            $DEPID_REF = $request['DEPID_REF'];
            $REMARKS = $request['REMARKS'];
            $DIRECT_PI = (isset($request['DIRECT_PI']) )? 1 : 0 ;  


            $log_data = [ 
                $PI_NO,     $PI_DT,         $STID_REF,      $DEPID_REF,    $REMARKS, 
                $DIRECT_PI, $CYID_REF,      $BRID_REF,      $FYID_REF,      $VTID_REF,
                $XMLMAT,    $USERID,    Date('Y-m-d'),  Date('h:i:s.u'),    $ACTIONNAME,
                $IPADDRESS
            ];

          
            
            $sp_result = DB::select('EXEC SP_PI_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
            
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            exit();   
     }

     public function getMRS(Request $request){
    

                $Status = "A";
                $id = $request['id'];
                $fieldid    = $request['fieldid'];
                $CYID_REF = Auth::user()->CYID_REF;
                $BRID_REF = Session::get('BRID_REF');
                $FYID_REF = Session::get('FYID_REF');

                $data_array1 = [];
                $ObjData =  DB::select('SELECT * FROM TBL_TRN_MRQS01_HDR  
                            WHERE CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? 
                            AND STATUS = ? AND STID_REF=?', [$CYID_REF, $BRID_REF, $FYID_REF, $Status, $id]);

              
                    $found_array1 = [];

                    if(!empty($ObjData)){
                        foreach ($ObjData as $index=>$dataRow){
                          
                            $objMRSMat =  DB::select("select table1.MRS_MATID,	table1.MRSID_REF,	table1.ITEMID_REF,	table1.MAIN_UOMID_REF,	table1.QTY from TBL_TRN_MRQS01_MAT table1 left join TBL_MST_ITEM table2 on table1.ITEMID_REF = table2.ITEMID WHERE (table2.DEACTIVATED is NULL OR table2.DEACTIVATED=0) AND table1.MRSID_REF=?" ,[$dataRow->MRSID]);

                            $data_array1=[];
                           

                            if(!empty($objMRSMat) ){
                               
                                $data_array1=[];

                                foreach($objMRSMat as $index=>$row1){

                                    $TOTAL_QTY = number_format(floatVal($row1->QTY), 3, '.', '');  
                                    
                                 
                                    $objConsumed = DB::select("select ISNULL(sum(table1.INDENT_QTY),0) as TOTAL_CONSUMED_QTY from TBL_TRN_PRIN02_MAT table1 left join TBL_TRN_PRIN02_HDR table2 on table1.PIID_REF = table2.PIID  WHERE table1.MRSNO=? AND table1.ITEMID_REF=? AND table1.UOMID_REF=? AND table2.STATUS=? " , [$row1->MRSID_REF, $row1->ITEMID_REF,$row1->MAIN_UOMID_REF,'A']);
                
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
                                    <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="mrscode_'.$dataRow->MRSID .'"  class="clssmrsid" value="'.$dataRow->MRSID.'" ></td>
                                    <td class="ROW2">'.$dataRow->MRS_NO;
                                    $row = $row.'<input type="hidden" id="txtmrscode_'.$dataRow->MRSID.'" data-desc="'.$dataRow->MRS_NO.'" 
                                    value="'.$dataRow->MRSID.'"/></td><td class="ROW3">'.$dataRow->MRS_DT.'</td></tr>';
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
        
        if(!is_null($id)){
        
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

       
        $objStore = DB::table('TBL_MST_STORE')
                ->where('DEACTIVATED','=',NULL)
                ->orWhere('DEACTIVATED','<>',1)
                ->where('STATUS','=',$Status)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->select('TBL_MST_STORE.*')
                ->get()
                ->toArray();


        $objMstResponse = DB::table('TBL_TRN_PRIN02_HDR')
                            ->where('FYID_REF','=',Session::get('FYID_REF'))
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('BRID_REF','=',Session::get('BRID_REF'))
                            ->where('PIID','=',$id)
                            ->select('*')
                            ->first();
        
        $objstorecode2 =[];
        if(isset($objMstResponse->STID_REF) && $objMstResponse->STID_REF !=""){
        $objstorecode2 = DB::table('TBL_MST_STORE')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('STID','=',$objMstResponse->STID_REF)
            ->select('STID','STCODE','NAME')
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

        $objMstResponse = DB::table('TBL_TRN_PRIN02_HDR')
                ->where('FYID_REF','=',Session::get('FYID_REF'))
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('PIID','=',$id)
                ->select('*')
                ->first();

        $STOREID =[];
        if(isset($objMstResponse->STID_REF) && $objMstResponse->STID_REF !=""){
            $STOREID = $objMstResponse->STID_REF;
        }

        $objList1 = DB::table('TBL_TRN_PRIN02_MAT')                    
            ->where('TBL_TRN_PRIN02_MAT.PIID_REF','=',$id)
            ->leftJoin('TBL_TRN_PRIN02_HDR','TBL_TRN_PRIN02_MAT.PIID_REF','=','TBL_TRN_PRIN02_HDR.PIID')                
            ->leftJoin('TBL_TRN_MRQS01_HDR','TBL_TRN_PRIN02_MAT.MRSNO','=','TBL_TRN_MRQS01_HDR.MRSID')                
            ->leftJoin('TBL_MST_ITEM','TBL_TRN_PRIN02_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                
            ->leftJoin('TBL_MST_UOM','TBL_TRN_PRIN02_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                
            ->select( 
                'TBL_TRN_PRIN02_MAT.*',
                'TBL_TRN_PRIN02_HDR.PIID',
                'TBL_TRN_PRIN02_HDR.PI_NO',
                'TBL_TRN_PRIN02_HDR.PI_DT',
                'TBL_TRN_PRIN02_HDR.DIRECT_PI',
                'TBL_TRN_MRQS01_HDR.MRS_NO',
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
            ->orderBy('TBL_TRN_PRIN02_MAT.PIMATID','ASC')
            ->get()->toArray();


            $ObjItem2 = $objList1;
            foreach($objList1 as $index=>$dataRow){
 
               $objQty = DB::select(" select ISNULL(sum(CURRENT_QTY),0) as CURRENT_QTY from TBL_MST_STOCK 
               WHERE  STID_REF=? AND  ITEMID_REF=? AND UOMID_REF=? AND CYID_REF=? AND BRID_REF=?  AND STATUS=?" ,[$STOREID, $dataRow->ITEMID,$dataRow->UOMID_REF,$CYID_REF,$BRID_REF,$Status]);

               $ObjItem2[$index]->STOCK_QTY = number_format(floatval($objQty[0]->CURRENT_QTY), 3, '.', '');
                
                $objMRSQty = DB::select(" select  ISNULL(sum(QTY),0) AS TOTAL_QTY from  TBL_TRN_MRQS01_MAT
                        WHERE  MRSID_REF=? AND ITEMID_REF=? AND MAIN_UOMID_REF=?  ", [$dataRow->MRSNO,$dataRow->ITEMID_REF,$dataRow->UOMID_REF]);

                $ObjItem2[$index]->BAL_MRSQTY = $objMRSQty[0]->TOTAL_QTY;                
              

                $objConsumed = DB::select("select ISNULL(sum(table1.INDENT_QTY),0) as TOTAL_CONSUMED_QTY from TBL_TRN_PRIN02_MAT table1 left join TBL_TRN_PRIN02_HDR table2 on table1.PIID_REF = table2.PIID  WHERE table1.MRSNO=? AND table1.ITEMID_REF=? AND table1.UOMID_REF=? AND table2.STATUS=? " , [$dataRow->MRSNO, $dataRow->ITEMID_REF,$dataRow->UOMID_REF,'A']);
                
                $total_balance_qty = number_format(floatVal($ObjItem2[$index]->BAL_MRSQTY ) -  floatval($objConsumed[0]->TOTAL_CONSUMED_QTY), 3, '.', '');
               
                $ObjItem2[$index]->BAL_MRSQTY = ($dataRow->DIRECT_PI==1) ? "0.000" : $total_balance_qty ;   
            }

           
           
        

        $objList1Count = count($ObjItem2);
        if($objList1Count==0){
            $objList1Count=1;
        }

        

        $objPIMAT = DB::table('TBL_TRN_PRIN02_MAT')                    
                            ->where('PIID_REF','=',$id)
                            ->select('*')
                            ->orderBy('PIMATID','ASC')
                            ->get()->toArray();
        $objCount1 = count($objPIMAT);    
        
        $objCOMPANY = DB::table('TBL_MST_COMPANY')
        ->where('CYID','=',$CYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_COMPANY.*')
        ->first();
  
        $objlastPIDT = DB::select('SELECT MAX(PI_DT) PI_DT FROM TBL_TRN_PRIN02_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);

        $AlpsStatus =   $this->AlpsStatus();
        $ActionStatus   =   "";

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
        return view($this->view.'edit', compact(['AlpsStatus','FormId','objRights','user_approval_level','objMstResponse',
                'objstorecode2','objStore','objlastPIDT','ObjItem2','objList1Count','objdeptcode2','objCOMPANY','ActionStatus','TabSetting'])); 

        }
     
    }
     
    public function view($id=NULL){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){
        
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

       
        $objStore = DB::table('TBL_MST_STORE')
                ->where('DEACTIVATED','=',NULL)
                ->orWhere('DEACTIVATED','<>',1)
                ->where('STATUS','=',$Status)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->select('TBL_MST_STORE.*')
                ->get()
                ->toArray();


        $objMstResponse = DB::table('TBL_TRN_PRIN02_HDR')
                            ->where('FYID_REF','=',Session::get('FYID_REF'))
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('BRID_REF','=',Session::get('BRID_REF'))
                            ->where('PIID','=',$id)
                            ->select('*')
                            ->first();
        
        $objstorecode2 =[];
        if(isset($objMstResponse->STID_REF) && $objMstResponse->STID_REF !=""){
        $objstorecode2 = DB::table('TBL_MST_STORE')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('STID','=',$objMstResponse->STID_REF)
            ->select('STID','STCODE','NAME')
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

        $objMstResponse = DB::table('TBL_TRN_PRIN02_HDR')
                ->where('FYID_REF','=',Session::get('FYID_REF'))
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('PIID','=',$id)
                ->select('*')
                ->first();

        $STOREID =[];
        if(isset($objMstResponse->STID_REF) && $objMstResponse->STID_REF !=""){
            $STOREID = $objMstResponse->STID_REF;
        }

        $objList1 = DB::table('TBL_TRN_PRIN02_MAT')                    
            ->where('TBL_TRN_PRIN02_MAT.PIID_REF','=',$id)
            ->leftJoin('TBL_TRN_PRIN02_HDR','TBL_TRN_PRIN02_MAT.PIID_REF','=','TBL_TRN_PRIN02_HDR.PIID')                
            ->leftJoin('TBL_TRN_MRQS01_HDR','TBL_TRN_PRIN02_MAT.MRSNO','=','TBL_TRN_MRQS01_HDR.MRSID')                
            ->leftJoin('TBL_MST_ITEM','TBL_TRN_PRIN02_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                
            ->leftJoin('TBL_MST_UOM','TBL_TRN_PRIN02_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                
            ->select( 
                'TBL_TRN_PRIN02_MAT.*',
                'TBL_TRN_PRIN02_HDR.PIID',
                'TBL_TRN_PRIN02_HDR.PI_NO',
                'TBL_TRN_PRIN02_HDR.PI_DT',
                'TBL_TRN_PRIN02_HDR.DIRECT_PI',
                'TBL_TRN_MRQS01_HDR.MRS_NO',
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
            ->orderBy('TBL_TRN_PRIN02_MAT.PIMATID','ASC')
            ->get()->toArray();


            $ObjItem2 = $objList1;
            foreach($objList1 as $index=>$dataRow){
 
               $objQty = DB::select(" select ISNULL(sum(CURRENT_QTY),0) as CURRENT_QTY from TBL_MST_STOCK 
               WHERE  STID_REF=? AND  ITEMID_REF=? AND UOMID_REF=? AND CYID_REF=? AND BRID_REF=?  AND STATUS=?" ,[$STOREID, $dataRow->ITEMID,$dataRow->UOMID_REF,$CYID_REF,$BRID_REF,$Status]);

               $ObjItem2[$index]->STOCK_QTY = number_format(floatval($objQty[0]->CURRENT_QTY), 3, '.', '');
                
                $objMRSQty = DB::select(" select  ISNULL(sum(QTY),0) AS TOTAL_QTY from  TBL_TRN_MRQS01_MAT
                        WHERE  MRSID_REF=? AND ITEMID_REF=? AND MAIN_UOMID_REF=?  ", [$dataRow->MRSNO,$dataRow->ITEMID_REF,$dataRow->UOMID_REF]);

                $ObjItem2[$index]->BAL_MRSQTY = $objMRSQty[0]->TOTAL_QTY;                
              

                $objConsumed = DB::select("select ISNULL(sum(table1.INDENT_QTY),0) as TOTAL_CONSUMED_QTY from TBL_TRN_PRIN02_MAT table1 left join TBL_TRN_PRIN02_HDR table2 on table1.PIID_REF = table2.PIID  WHERE table1.MRSNO=? AND table1.ITEMID_REF=? AND table1.UOMID_REF=? AND table2.STATUS=? " , [$dataRow->MRSNO, $dataRow->ITEMID_REF,$dataRow->UOMID_REF,'A']);
                
                $total_balance_qty = number_format(floatVal($ObjItem2[$index]->BAL_MRSQTY ) -  floatval($objConsumed[0]->TOTAL_CONSUMED_QTY), 3, '.', '');
               
                $ObjItem2[$index]->BAL_MRSQTY = ($dataRow->DIRECT_PI==1) ? "0.000" : $total_balance_qty ;   
            }

           
           
        

        $objList1Count = count($ObjItem2);
        if($objList1Count==0){
            $objList1Count=1;
        }

        

        $objPIMAT = DB::table('TBL_TRN_PRIN02_MAT')                    
                            ->where('PIID_REF','=',$id)
                            ->select('*')
                            ->orderBy('PIMATID','ASC')
                            ->get()->toArray();
        $objCount1 = count($objPIMAT);    
        
        $objCOMPANY = DB::table('TBL_MST_COMPANY')
        ->where('CYID','=',$CYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_COMPANY.*')
        ->first();
  
        $objlastPIDT = DB::select('SELECT MAX(PI_DT) PI_DT FROM TBL_TRN_PRIN02_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);

        $AlpsStatus =   $this->AlpsStatus();
        $ActionStatus   =   "disabled";

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
        return view($this->view.'view', compact(['AlpsStatus','FormId','objRights','user_approval_level','objMstResponse',
                'objstorecode2','objStore','objlastPIDT','ObjItem2','objList1Count','objdeptcode2','objCOMPANY','ActionStatus','TabSetting'])); 

        }
     
    }

    
   public function update(Request $request){
        
           
            $r_count1 = $request['Row_Count1'];
        
            for ($i=0; $i<=$r_count1; $i++)
            {
                if(isset($request['ITEMID_REF_'.$i]))
                {
                    $newdt2=!(is_null($request['EDA_'.$i]) || empty($request['EDA_'.$i]) )=="true" ? $request['EDA_'.$i] : NULL;                 
                    if(!is_null($newdt2) ){
                        $newdt2 = str_replace( "/", "-",  $newdt2 ) ;
                        $newDateString2 = Carbon::parse($newdt2)->format('Y-m-d');        
                        $EDA_DT = $newDateString2;
                    }else{
                        $EDA_DT = NULL;
                    }
    
                    $req_data[$i] = [
                        
                        'MRSNO'      => isset( $request['MrsID_'.$i]) &&  (!is_null($request['MrsID_'.$i]) ) ? $request['MrsID_'.$i] : 0,
                        'ITEMID_REF' => $request['ITEMID_REF_'.$i],
                        'UOMID_REF'  => $request['MAIN_UOMID_REF_'.$i],
                        'ITEMSPECI'  => $request['Itemspec_'.$i],
                        'SIH'        => $request['STOCK_QTY_'.$i],
                        'MRS_QTY'    => $request['BAL_MRSQTY_'.$i],
                        'INDENT_QTY' => $request['INDENT_QTY_'.$i],
                        'EDA'        => $EDA_DT,
                        'APPROX_VL'  => $request['APPROX_VAL_'.$i],
                        'REMARKS'    => $request['REMARKS_'.$i],
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
    
                $PI_NO = $request['PI_NO'];
                $PI_DT = $request['PI_DT'];
                $STID_REF = $request['STID_REF'];
                $DEPID_REF = $request['DEPID_REF'];
                $REMARKS = $request['REMARKS'];
                $DIRECT_PI = (isset($request['DIRECT_PI']) )? 1 : 0 ; 
    
    
                $log_data = [ 
                    $PI_NO,     $PI_DT,         $STID_REF,      $DEPID_REF,    $REMARKS, $DIRECT_PI,
                    $CYID_REF,  $BRID_REF,  $FYID_REF,      $VTID_REF,      $XMLMAT,        
                    $USERID,    Date('Y-m-d'),  Date('h:i:s.u'),$ACTIONNAME,    $IPADDRESS
                ];
    
              
                
                $sp_result = DB::select('EXEC SP_PI_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);     
        

    
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
        $VTID_REF   =   $this->vtid_ref;  
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');         
        
        $IPADDRESS = $request->getClientIp();

        $DIRECT_PI = (isset($request['DIRECT_PI']) )? 1 : 0 ; 

       
        if($DIRECT_PI==0){
            $r_count1 = $request['Row_Count1'];
            
            for ($i=0; $i<=$r_count1; $i++)
            {
                if(isset($request['ITEMID_REF_'.$i]))
                {
                
                    $reqData[$i] = [
                        
                        'MRSNO'      => isset( $request['MrsID_'.$i]) &&  (!is_null($request['MrsID_'.$i]) ) ? $request['MrsID_'.$i] : 0,
                        'MRS_LABEL'      => isset( $request['MRS_popup_'.$i]) &&  (!is_null($request['MRS_popup_'.$i]) ) ? $request['MRS_popup_'.$i] : "",
                        'ITEMID_REF' => $request['ITEMID_REF_'.$i],
                        'UOMID_REF'  => $request['MAIN_UOMID_REF_'.$i],
                        'ItemName'  => $request['ItemName_'.$i],
                        'INDENT_QTY' => $request['INDENT_QTY_'.$i],
                    ];
                }
            }

      
            foreach($reqData as $index=>$dataRow){

                $objQty = DB::select(" select ISNULL(sum(QTY),0) as ACTUAL_QTY FROM TBL_TRN_MRQS01_MAT WHERE MRSID_REF=? AND ITEMID_REF=? AND MAIN_UOMID_REF=?" , [$dataRow["MRSNO"], $dataRow["ITEMID_REF"],$dataRow["UOMID_REF"] ]);
    
                $reqData[$index]["ACTUAL_QTY"] = number_format(floatval($objQty[0]->ACTUAL_QTY), 3, '.', '');    
                
                $objConsumed = DB::select("select ISNULL(sum(table1.INDENT_QTY),0) as TOTAL_CONSUMED_QTY from TBL_TRN_PRIN02_MAT table1 left join TBL_TRN_PRIN02_HDR table2 on table1.PIID_REF = table2.PIID  WHERE table1.MRSNO=? AND table1.ITEMID_REF=? AND table1.UOMID_REF=? AND table2.STATUS=? " , [$dataRow["MRSNO"], $dataRow["ITEMID_REF"],$dataRow["UOMID_REF"],'A']);

                $reqData[$index]["TOTAL_CONSUMED_QTY"] = number_format(floatval($objConsumed[0]->TOTAL_CONSUMED_QTY), 3, '.', '');    

            }   

            
            foreach($reqData as $index=>$row1){

                $ACTUAL_QTY = number_format(floatVal($row1["ACTUAL_QTY"]), 3, '.', '');
                $TOTAL_NEW_QTY = number_format(floatVal($row1["TOTAL_CONSUMED_QTY"]) + floatVal($row1["INDENT_QTY"]) , 3, '.', '');
            
                if($TOTAL_NEW_QTY > $ACTUAL_QTY){
                    return Response::json(['errors'=>true,'save'=>'invalid','msg' => 'Indent Qty is greater than Balance MRS Qty. Please check '.$row1["MRS_LABEL"]]);
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
        $UPDATE =  Date('Y-m-d');
        $UPTIME = Date('h:i:s.u');
        $IPADDRESS  =  $request->getClientIp();

        
           
            $r_count1 = $request['Row_Count1'];
        
            for ($i=0; $i<=$r_count1; $i++)
            {
                if(isset($request['ITEMID_REF_'.$i]))
                {
                    $newdt2=!(is_null($request['EDA_'.$i]) || empty($request['EDA_'.$i]) )=="true" ? $request['EDA_'.$i] : NULL;                 
                    if(!is_null($newdt2) ){
                        $newdt2 = str_replace( "/", "-",  $newdt2 ) ;
                        $newDateString2 = Carbon::parse($newdt2)->format('Y-m-d');        
                        $EDA_DT = $newDateString2;
                    }else{
                        $EDA_DT = NULL;
                    }
    
                    $req_data[$i] = [
                        
                        'MRSNO'      => isset( $request['MrsID_'.$i]) &&  (!is_null($request['MrsID_'.$i]) ) ? $request['MrsID_'.$i] : 0,
                        'ITEMID_REF' => $request['ITEMID_REF_'.$i],
                        'UOMID_REF'  => $request['MAIN_UOMID_REF_'.$i],
                        'ITEMSPECI'  => $request['Itemspec_'.$i],
                        'SIH'        => $request['STOCK_QTY_'.$i],
                        'MRS_QTY'    => $request['BAL_MRSQTY_'.$i],
                        'INDENT_QTY' => $request['INDENT_QTY_'.$i],
                        'EDA'        => $EDA_DT,
                        'APPROX_VL'  => $request['APPROX_VAL_'.$i],
                        'REMARKS'    => $request['REMARKS_'.$i],
                    ];
                }
            }
            
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        
          
    
                $VTID_REF     =   $this->vtid_ref;
                $USERID = Auth::user()->USERID;   
               
                $CYID_REF = Auth::user()->CYID_REF;
                $BRID_REF = Session::get('BRID_REF');
                $FYID_REF = Session::get('FYID_REF');
    
                $PI_NO = $request['PI_NO'];
                $PI_DT = $request['PI_DT'];
                $STID_REF = $request['STID_REF'];
                $DEPID_REF = $request['DEPID_REF'];
                $REMARKS = $request['REMARKS'];
               
    
                
                $log_data = [ 
                    $PI_NO,     $PI_DT,         $STID_REF,      $DEPID_REF,    $REMARKS, $DIRECT_PI,
                    $CYID_REF,  $BRID_REF,  $FYID_REF,      $VTID_REF,      $XMLMAT,        
                    $USERID,    Date('Y-m-d'),  Date('h:i:s.u'),    $ACTIONNAME,    $IPADDRESS
                ];
    
             
                
               $sp_result = DB::select('EXEC SP_PI_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);     
        
   
            
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
                $TABLE      =   "TBL_TRN_PRIN02_HDR";
                $FIELD      =   "PIID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
           
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_PI ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
    
            if($sp_result[0]->RESULT=="All records approved"){

                return Response::json(['approve' =>true,'msg' => 'Record successfully Approved.']);
                
            }elseif($sp_result[0]->RESULT=="Indent QTY must be less or equale than MRS balance QTY."){
            
                return Response::json(['errors'=>true,'msg' => 'Indent QTY must be less or equale than MRS balance QTY.','save'=>'invalid']);

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
        $TABLE      =   "TBL_TRN_PRIN02_HDR";
        $FIELD      =   "PIID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
         'NT'  => 'TBL_TRN_PRIN02_MAT',
        ];
       
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $sp_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_PI  ?,?,?,?, ?,?,?,?, ?,?,?,?', $sp_cancel_data);

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
    
    $image_path         =   "docs/company".$CYID_REF."/PurchaseIndent";     
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

                               $uploadedFile->move($destinationPath, $filenametostore);  
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

    }

  
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
