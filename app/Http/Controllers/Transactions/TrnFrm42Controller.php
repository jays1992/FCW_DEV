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

class TrnFrm42Controller extends Controller
{
    protected $form_id = 42;
    protected $vtid_ref   = 42; 
    protected $view         = "transactions.sales.DailySalesPlan.trnfrm42";

  
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

        $objDataList	=	DB::select("select hdr.DSPID,hdr.DSPNO,hdr.DSPDT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.DSPID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_SLDP01_HDR hdr
                            on a.VID = hdr.DSPID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_SUBLEDGER sl ON hdr.SLID_REF = sl.SGLID
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.DSPID DESC ");

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
       
        $objglcode = DB::table('TBL_MST_GENERALLEDGER')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('STATUS','=',$Status)
        ->where('SUBLEDGER','=',1)
        ->select('TBL_MST_GENERALLEDGER.*')
        ->get()
        ->toArray();

        
        $objlastDSPDT = DB::select('SELECT MAX(DSPDT) DSPDT FROM TBL_TRN_SLDP01_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
        
        $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
        WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by UOMCODE ASC', [$CYID_REF, 'A' ]);  
        
        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_SLDP01_HDR',
            'HDR_ID'=>'DSPID',
            'HDR_DOC_NO'=>'DSPNO',
            'HDR_DOC_DT'=>'DSPDT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

           

        
        $objUdf     = Helper::getUdfForDSP( $CYID_REF);
        $objudfCount = count($objUdf);                
        if($objudfCount==0){
            $objudfCount=1;
        }
        
       

        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
      
    return view($this->view.'add', compact(['FormId','objglcode','objlastDSPDT','objUdf','objudfCount','AlpsStatus','TabSetting','doc_req','docarray']));       
   }

   

   public function getsubledger(Request $request){
    $Status = "A";
    $CYID_REF = Auth::user()->CYID_REF;
    $BRID_REF = Session::get('BRID_REF');
    $CODE = $request['CODE'];
    $NAME = $request['NAME'];

    $sp_popup = [
        $CYID_REF, $BRID_REF,$CODE,$NAME
    ]; 
    
        $ObjData = DB::select('EXEC sp_get_customer_popup_enquiry ?,?,?,?', $sp_popup);

        if(!empty($ObjData)){

            foreach ($ObjData as $index=>$dataRow){
            
                $row = '';
                $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_SLID_REF[]" id="subgl_'.$index.'" class="clssubgl" value="'.$dataRow-> SGLID.'" ></td>';
                $row = $row.'<td class="ROW2">'.$dataRow->SGLCODE;
                $row = $row.'<input type="hidden" id="txtsubgl_'.$index.'" data-desc="'.$dataRow->SGLCODE .' - ';
                $row = $row.$dataRow->SLNAME. '" data-desc2="'.$dataRow->GLID_REF. '"value="'.$dataRow->SGLID.'"/></td><td class="ROW3">'.$dataRow->SLNAME.'</td></tr>';
    
    
                echo $row;
            }
    
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    
    }
   

    public function getItemDetails(Request $request){
        $SOID = $request['soid'];
        $Status = 'A';
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $StdCost = 0;

        $AlpsStatus =   $this->AlpsStatus();
     
                
       
        $ObjItem = DB::table('TBL_TRN_SLSO01_MAT')                    
            ->leftJoin('TBL_MST_ITEM','TBL_MST_ITEM.ITEMID','=','TBL_TRN_SLSO01_MAT.ITEMID_REF')                
            ->leftJoin('TBL_MST_UOM','TBL_MST_UOM.UOMID','=','TBL_TRN_SLSO01_MAT.MAIN_UOMID_REF')                
            ->select( 
                'TBL_TRN_SLSO01_MAT.*',
                'TBL_MST_ITEM.ITEMID',
                'TBL_MST_ITEM.ICODE',
                'TBL_MST_ITEM.NAME',
                'TBL_MST_ITEM.ITEM_DESC',
                'TBL_TRN_SLSO01_MAT.ITEMSPECI',
                'TBL_MST_UOM.UOMCODE',
                'TBL_MST_UOM.DESCRIPTIONS',
            )
            ->where('TBL_TRN_SLSO01_MAT.SOID_REF','=',$SOID)
            ->where('TBL_MST_ITEM.ITEMID','<>',1)
            ->orderBy('TBL_TRN_SLSO01_MAT.SOMATID','ASC')
            ->get();

        

            $ObjItem2 = $ObjItem;
            $STATUS = 'A';
            foreach($ObjItem as $index=>$dataRow){
               
                $tmpSOID_REF = 0;
                $tmpSQA_REF = 0 ;
                $tmpITEMID_REF = 0;
                $tmpSEQID_REF = 0;

                $tmpSOID_REF = $dataRow->SOID_REF;
                $tmpSQA_REF = (empty($dataRow->SQA) || is_null($dataRow->SQA)) ? 0 : $dataRow->SQA ;
                $tmpITEMID_REF = $dataRow->ITEMID_REF;
                $tmpSEQID_REF = (empty($dataRow->SEQID_REF) || is_null($dataRow->SEQID_REF))? 0 : $dataRow->SEQID_REF ;
                
          

               $objQty = DB::select(" select ISNULL(sum(table1.EXPDPQTY),0) as BAL_SOQTY from TBL_TRN_SLDP01_MAT table1
               left join TBL_TRN_SLDP01_HDR table2 on table1.DSPID_REF = table2.DSPID
               WHERE table1.SOID_REF=? AND table1.ITEMID_REF=? AND table1.SEID_REF=? AND table1.SQID_REF=? " ,[$tmpSOID_REF,$tmpITEMID_REF,$tmpSEQID_REF,$tmpSQA_REF]);

                $ObjItem2[$index]->BAL_SOQTY = $objQty[0]->BAL_SOQTY;
                
            }
    


        if(!empty($ObjItem2)){

            foreach ($ObjItem2 as $index=>$dataRow){

                $tmpSOID_REF = $dataRow->SOID_REF;
                $tmpSQA_REF = (empty($dataRow->SQA) || is_null($dataRow->SQA)) ? 0 : $dataRow->SQA ;
                $tmpITEMID_REF = $dataRow->ITEMID_REF;
                $tmpSEQID_REF = (empty($dataRow->SEQID_REF) || is_null($dataRow->SEQID_REF))? 0 : $dataRow->SEQID_REF ;

                $CustomId = $tmpSOID_REF.$tmpSQA_REF.$tmpITEMID_REF.$tmpSEQID_REF;  

                $EXPDPQTY =  number_format(floatVal($dataRow->SO_QTY) -  floatval($dataRow->BAL_SOQTY), 3, '.', '');   
                $total_balance_qty = number_format(floatVal($dataRow->SO_QTY) -  floatval($dataRow->BAL_SOQTY), 3, '.', '');   
            
                $itemSpec = trim($dataRow->ITEMSPECI)!="" ? $dataRow->ITEMSPECI : $dataRow->ITEM_DESC;


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

                
                if($total_balance_qty>"0.000"){
                    $row = '';
                    $row = $row.'<tr id="item_'.$CustomId .'"  class="clsitemid"><td  style="width:8%; text-align: center;">
                        <input type="checkbox" id="chkId'.$CustomId.'"  value="'.$CustomId.'" class="js-selectall1"  >
                        <input type="hidden" id="txtrecordId_'.$CustomId.'"  value="'.$CustomId.'"   >
                        <input type="hidden" name="itemcode_'.$CustomId.'" id="txtitemcode_'.$dataRow->ICODE.'"  value="'.$dataRow->ICODE.'" data-desc="'.$dataRow->ICODE.' "   >
                        <input type="hidden" name="SQA_REF_'.$CustomId.'" id="txtSQA_REF_'.$CustomId.'"  value="'.$tmpSQA_REF.'"  >
                        <input type="hidden" name="SEQID_REF_'.$CustomId.'" id="txtSEQID_REF_'.$CustomId.'"  value="'.$tmpSEQID_REF.'"  >
                        </td>';
                    $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                    $row = $row.'<input type="hidden" id="txtitem_'.$CustomId.'" data-desc="'.$dataRow->ICODE.'" value="'.$dataRow->ITEMID_REF.'"/></td>
                    <td style="width:10%;" id="itemname_'.$CustomId.'" >'.$dataRow->NAME;
                    $row = $row.'<input type="hidden" id="txtitemname_'.$CustomId.'" data-desc="'.$dataRow->NAME.'"
                        value="'.$dataRow->NAME.'"/></td>';
                    $row = $row.'<td style="width:8%;" id="itemuom_'.$CustomId.'" ><input type="hidden" id="txtitemuom_'.$CustomId.'" data-desc="'.$dataRow->UOMCODE.'-'.$dataRow->DESCRIPTIONS.'" data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$dataRow->UOMCODE.'-'.$dataRow->DESCRIPTIONS.'</td>';
                    $row = $row.'<td style="width:8%;" id="itemspec_'.$CustomId.'" ><input type="hidden" id="txtitemspec_'.$CustomId.'" data-desc="'.$dataRow->ITEMSPECI.'" value="'.$dataRow->ITEMSPECI.'"/>'.$dataRow->ITEMSPECI.'</td>';
                    $row = $row.'<td style="width:8%;" id="soqty_'.$CustomId.'" ><input type="hidden" id="txtsoqty_'.$CustomId.'" data-desc="'.$dataRow->SO_QTY.'"
                    value="'.$dataRow->SO_QTY.'"/>'.$dataRow->SO_QTY.'</td>';
                    $row = $row.'<td style="width:8%;" id="balsoqty_'.$CustomId.'"><input type="hidden" id="txtbalsoqty_'.$CustomId.'" data-desc="'.$total_balance_qty.'" value="'.$total_balance_qty.'"/>'.$total_balance_qty.'</td>';
                    $row = $row.'<td style="width:8%;" id="expdpqty_'.$CustomId.'"><input type="hidden" id="txtexpdpqty_'.$CustomId.'" value="'.$EXPDPQTY.'" />'.$EXPDPQTY.'</td>

                    <td style="width:8%;">'.$BusinessUnit.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                    </tr>';
                    echo $row; 
                }    
                   
            } 
            
            
        }           
        else{
            echo '<tr><td> Record not found.</td></tr>';
        }  
        exit();                      
        
    }


    public function getSODetails(Request $request){
        $Status = $request['status'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $StdCost = 0;
        $Taxid = [];
                
        $ObjItem =  DB::select('SELECT * FROM TBL_MST_ITEM  
                    WHERE CYID_REF = ? 
                    AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                    [$CYID_REF,  $Status ]);
      
                if(!empty($ObjItem)){

                    foreach ($ObjItem as $index=>$dataRow){
                    
                        
                    $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ? AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $dataRow->MAIN_UOMID_REF, 'A' ]);

                    $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ? AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $dataRow->ALT_UOMID_REF, $Status ]);
                    
                    $ObjAltQTY =  DB::select('SELECT TOP 1  TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV  
                                WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                                [$dataRow->ITEMID,$dataRow->ALT_UOMID_REF ]);

                    $TOQTY =  isset($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 0;
                    $FROMQTY =  isset($ObjAltQTY[0]->FROM_QTY)? $ObjAltQTY[0]->FROM_QTY : 0;

                    $ObjItemGroup =  DB::select('SELECT TOP 1 GROUPCODE, GROUPNAME FROM TBL_MST_ITEMGROUP  
                                WHERE  CYID_REF = ? AND ITEMGID = ?
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                [$CYID_REF, $dataRow->ITEMGID_REF, 'A' ]);

                    $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS FROM TBL_MST_ITEMCATEGORY  
                                WHERE  CYID_REF = ? AND ICID = ?
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                [$CYID_REF, $dataRow->ICID_REF, 'A' ]);
                    
                        
                        $row = '';
                        $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:6.5%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td>'.$dataRow->ICODE;
                        $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'"
                        value="'.$dataRow->ITEMID.'"/></td>
                        <td id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'"
                        value="'.$dataRow->NAME.'"/></td>';
                        $row = $row.'<td id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                        value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'"
                        value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                        $row = $row.'<td id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="'.$FROMQTY.'"
                        value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                        $row = $row.'<td id="itax_'.$ObjItem[0]->ITEMID.'"><input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'" />'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                        <td>Authorized</td>
                        </tr>';
                        echo $row;    
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
        $objMst = DB::table("TBL_TRN_SLDP01_HDR")
                    ->where('DSPID','=',$id)
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
                    'SEID_REF'        => $request['SEQID_REF_'.$i],
                    'SQID_REF'        => $request['SQA_REF_'.$i],
                    'SOID_REF'        => $request['SOrdID_'.$i],
                    'ITEMID_REF'      => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'       => $request['MAIN_UOMID_REF_'.$i],
                    'ITEMSPECI'       => $request['Itemspec_'.$i],
                    'BALSOQTY'        => $request['BAL_SOQTY_'.$i],
                    'EXPDPQTY'        => $request['EXP_DIS_QTY_'.$i],
                ];
            }
        }
        
        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
    
        $r_count2 = $request['Row_Count2'];  
        $udffield_Data = [];      
        for ($i=0; $i<=$r_count2; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDF_DSPID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
            }
            
        }
           
        if(count($udffield_Data)>0){

            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        

        }else{
        
            $XMLUDF = NULL;
        }
        
     

            $VTID_REF     =   $this->vtid_ref;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();

            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');

            $DSPNO = $request['DSPNO'];
            $DSPDT = $request['DSPDT'];
            $GLID_REF = $request['GLID_REF'];
            $SLID_REF = $request['SLID_REF'];

       


            $log_data = [ 
                $DSPNO,     $DSPDT,         $GLID_REF,      $SLID_REF,      $CYID_REF, 
                $BRID_REF,  $FYID_REF,      $VTID_REF,      $XMLMAT,        $XMLUDF,
                $USERID,    Date('Y-m-d'),  Date('h:i:s.u'),$ACTIONNAME,    $IPADDRESS
            ];

          
            
            $sp_result = DB::select('EXEC SP_DSP_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
            
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);
    
            }else{
                return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
            }
            exit();   
     }

     public function getSalesOrder(Request $request){
        $Status = "A";
        $id = $request['id'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        
        $ObjData =  DB::select('SELECT * FROM TBL_TRN_SLSO01_HDR  
                    WHERE CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? 
                     AND STATUS = ? AND SLID_REF=?', 
                    [$CYID_REF, $BRID_REF, $FYID_REF, $Status, $id]);
       
            if(!empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){

                    $row = '';
                    $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_SOID[]" id="sordcode_'.$index.'" class="clssordid" value="'.$dataRow-> SOID.'" ></td>';
                    $row = $row.'<td class="ROW2">'.$dataRow->SONO;
                    $row = $row.'<input type="hidden" id="txtsordcode_'.$index.'" data-desc="'.$dataRow->SONO .'" value="'.$dataRow->SOID.'"/></td><td class="ROW3">'.$dataRow->SODT.'</td></tr>';

                    echo $row;
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

       
        $objglcode = DB::table('TBL_MST_GENERALLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('STATUS','=',$Status)
            ->where('SUBLEDGER','=',1)
            ->select('TBL_MST_GENERALLEDGER.*')
            ->get()
            ->toArray();

        $objMstResponse = DB::table('TBL_TRN_SLDP01_HDR')
                            ->where('FYID_REF','=',Session::get('FYID_REF'))
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('BRID_REF','=',Session::get('BRID_REF'))
                            ->where('DSPID','=',$id)
                            ->select('*')
                            ->first();


        $objglcode2 =[];            
        if(isset($objMstResponse->GLID_REF) && $objMstResponse->GLID_REF !=""){
        $objglcode2 = DB::table('TBL_MST_GENERALLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('GLID','=',$objMstResponse->GLID_REF)
            ->select('GLID','GLCODE','GLNAME')
            ->first();
        }

        $objslcode2=[];
        if(isset($objMstResponse->SLID_REF) && $objMstResponse->SLID_REF !=""){
        $objslcode2 = DB::table('TBL_MST_SUBLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('SGLID','=',$objMstResponse->SLID_REF)
            ->select('SGLID','SGLCODE','SLNAME')
            ->first();
        }
           

        $objList1 = DB::table('TBL_TRN_SLDP01_MAT')                    
            ->where('TBL_TRN_SLDP01_MAT.DSPID_REF','=',$id)
            ->leftJoin('TBL_TRN_SLSO01_HDR','TBL_TRN_SLDP01_MAT.SOID_REF','=','TBL_TRN_SLSO01_HDR.SOID')                
            ->leftJoin('TBL_MST_ITEM','TBL_TRN_SLDP01_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                
            ->leftJoin('TBL_MST_UOM','TBL_TRN_SLDP01_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                
            ->select( 
                'TBL_TRN_SLDP01_MAT.*',
                'TBL_TRN_SLSO01_HDR.SOID',
                'TBL_TRN_SLSO01_HDR.SONO',
                'TBL_TRN_SLSO01_HDR.SODT',
                'TBL_MST_ITEM.ITEMID',
                'TBL_MST_ITEM.ICODE',
                'TBL_MST_ITEM.NAME',
                'TBL_MST_ITEM.ALPS_PART_NO',
                'TBL_MST_ITEM.CUSTOMER_PART_NO',
                'TBL_MST_ITEM.OEM_PART_NO',
                'TBL_MST_UOM.UOMID',
                'TBL_MST_UOM.UOMCODE',
                'TBL_MST_UOM.DESCRIPTIONS'
            )
            ->orderBy('TBL_TRN_SLDP01_MAT.DSPMATID','ASC')
            ->get()->toArray();

           
        $STATUS='A';
        foreach($objList1 as $key=>$row){
            
            $tmpSOID_REF = 0;
            $tmpSQID_REF = 0 ;
            $tmpITEMID_REF = 0;
            $tmpSEID_REF = 0;

            $tmpSOID_REF = $row->SOID_REF;
            $tmpSQID_REF = (empty($row->SQID_REF) || is_null($row->SQID_REF)) ? 0 : $row->SQID_REF ;
            $tmpITEMID_REF = $row->ITEMID_REF;
            $tmpSEID_REF = (empty($row->SEID_REF) || is_null($row->SEID_REF))? 0 : $row->SEID_REF ;

            
            $objQty = DB::select("select SO_QTY from TBL_TRN_SLSO01_MAT where SOID_REF=? AND ITEMID_REF=? AND (SQA IS NULL OR SQA=?) AND (SEQID_REF IS NULL OR SEQID_REF=?)" ,[$tmpSOID_REF,$tmpITEMID_REF,$tmpSQID_REF,$tmpSEID_REF]);
           
            if(!empty($objQty)) {
                $objList1[$key]->SO_QTY = $objQty[0]->SO_QTY;
            }   

                
               $objDispatchQty = DB::select(" select ISNULL(sum(table1.EXPDPQTY),0) as TOTAL_DIS_QTY from TBL_TRN_SLDP01_MAT table1
                  left join TBL_TRN_SLDP01_HDR table2 on table1.DSPID_REF = table2.DSPID
                  WHERE table1.SOID_REF=? AND table1.ITEMID_REF=? AND table1.SEID_REF=? AND table1.SQID_REF=? " ,[$tmpSOID_REF,$tmpITEMID_REF,$tmpSEID_REF,$tmpSQID_REF]);

               $total_balance_qty = number_format(floatVal($objQty[0]->SO_QTY) -  floatval($objDispatchQty[0]->TOTAL_DIS_QTY), 3, '.', '');
               $calculated_bal_qty = number_format(floatVal($total_balance_qty) +  floatval($row->EXPDPQTY), 3, '.', '');

                $objList1[$key]->BAL_SOQTY = $calculated_bal_qty;
                
            
        }    

       

        $objList1Count = count($objList1);
        if($objList1Count==0){
            $objList1Count=1;
        }

        $objSEMAT = DB::table('TBL_TRN_SLDP01_MAT')                    
                            ->where('DSPID_REF','=',$id)
                            ->select('*')
                            ->orderBy('DSPMATID','ASC')
                            ->get()->toArray();
        $objCount1 = count($objSEMAT);  

        
        $objlastDSPDT = DB::select('SELECT MAX(DSPDT) DSPDT FROM TBL_TRN_SLDP01_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
        
        $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
        WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by UOMCODE ASC', [$CYID_REF,  'A' ]);        

        $objSON = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('FYID_REF','=',$FYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_DOCNO_DEFINITION.*')
        ->first();
        if($objSON->SYSTEM_GRSR == "1")
        {
            if($objSON->PREFIX_RQ == "1")
            {
                $objDSPNO = $objSON->PREFIX;
            }        
            if($objSON->PRE_SEP_RQ == "1")
            {
                if($objSON->PRE_SEP_SLASH == "1")
                {
                $objDSPNO = $objDSPNO.'/';
                }
                if($objSON->PRE_SEP_HYPEN == "1")
                {
                $objDSPNO = $objDSPNO.'-';
                }
            }        
            if($objSON->NO_MAX)
            {   
                $objDSPNO = $objDSPNO.str_pad($objSON->LAST_RECORDNO+1, $objSON->NO_MAX, "0", STR_PAD_LEFT);
            }
            
            if($objSON->NO_SEP_RQ == "1")
            {
                    if($objSON->NO_SEP_SLASH == "1")
                    {
                    $objDSPNO = $objDSPNO.'/';
                    }
                    if($objSON->NO_SEP_HYPEN == "1")
                    {
                    $objDSPNO = $objDSPNO.'-';
                    }
                }
                if($objSON->SUFFIX_RQ == "1")
                {
                    $objDSPNO = $objDSPNO.$objSON->SUFFIX;
                }
            }
            
            
             $objUDF = DB::table('TBL_TRN_SLDP01_UDF')                    
                 ->where('TBL_TRN_SLDP01_UDF.DSPID_REF','=',$id)
                 ->leftJoin('TBL_MST_UDF_DSP','TBL_MST_UDF_DSP.UDF_DSPID','=','TBL_TRN_SLDP01_UDF.UDF_DSPID_REF')                
                 ->select('TBL_TRN_SLDP01_UDF.*','TBL_MST_UDF_DSP.*')
                 ->orderBy('TBL_TRN_SLDP01_UDF.DSPUDFID','ASC')
            ->get()->toArray();

            $objudfCount = count($objUDF);

                if($objudfCount==0){
                    $objudfCount=1;
                }
     
           

           $AlpsStatus =   $this->AlpsStatus();
           $InputStatus=   "";

           $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
      
            return view($this->view.'edit', compact(['FormId','objRights','user_approval_level','objMstResponse','objglcode','objglcode2','objslcode2','objSON','objDSPNO','objlastDSPDT','objList1','objList1Count','objUDF','objudfCount','AlpsStatus','InputStatus','TabSetting'])); 

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
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',$FYID_REF)
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

       
        $objglcode = DB::table('TBL_MST_GENERALLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('STATUS','=',$Status)
            ->where('SUBLEDGER','=',1)
            ->select('TBL_MST_GENERALLEDGER.*')
            ->get()
            ->toArray();

        $objMstResponse = DB::table('TBL_TRN_SLDP01_HDR')
                            ->where('FYID_REF','=',Session::get('FYID_REF'))
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('BRID_REF','=',Session::get('BRID_REF'))
                            ->where('DSPID','=',$id)
                            ->select('*')
                            ->first();


        $objglcode2 =[];            
        if(isset($objMstResponse->GLID_REF) && $objMstResponse->GLID_REF !=""){
        $objglcode2 = DB::table('TBL_MST_GENERALLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('GLID','=',$objMstResponse->GLID_REF)
            ->select('GLID','GLCODE','GLNAME')
            ->first();
        }

        $objslcode2=[];
        if(isset($objMstResponse->SLID_REF) && $objMstResponse->SLID_REF !=""){
        $objslcode2 = DB::table('TBL_MST_SUBLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('SGLID','=',$objMstResponse->SLID_REF)
            ->select('SGLID','SGLCODE','SLNAME')
            ->first();
        }
           

        $objList1 = DB::table('TBL_TRN_SLDP01_MAT')                    
            ->where('TBL_TRN_SLDP01_MAT.DSPID_REF','=',$id)
            ->leftJoin('TBL_TRN_SLSO01_HDR','TBL_TRN_SLDP01_MAT.SOID_REF','=','TBL_TRN_SLSO01_HDR.SOID')                
            ->leftJoin('TBL_MST_ITEM','TBL_TRN_SLDP01_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                
            ->leftJoin('TBL_MST_UOM','TBL_TRN_SLDP01_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                
            ->select( 
                'TBL_TRN_SLDP01_MAT.*',
                'TBL_TRN_SLSO01_HDR.SOID',
                'TBL_TRN_SLSO01_HDR.SONO',
                'TBL_TRN_SLSO01_HDR.SODT',
                'TBL_MST_ITEM.ITEMID',
                'TBL_MST_ITEM.ICODE',
                'TBL_MST_ITEM.NAME',
                'TBL_MST_ITEM.ALPS_PART_NO',
                'TBL_MST_ITEM.CUSTOMER_PART_NO',
                'TBL_MST_ITEM.OEM_PART_NO',
                'TBL_MST_UOM.UOMID',
                'TBL_MST_UOM.UOMCODE',
                'TBL_MST_UOM.DESCRIPTIONS'
            )
            ->orderBy('TBL_TRN_SLDP01_MAT.DSPMATID','ASC')
            ->get()->toArray();

           
        $STATUS='A';
        foreach($objList1 as $key=>$row){
            
            $tmpSOID_REF = 0;
            $tmpSQID_REF = 0 ;
            $tmpITEMID_REF = 0;
            $tmpSEID_REF = 0;

            $tmpSOID_REF = $row->SOID_REF;
            $tmpSQID_REF = (empty($row->SQID_REF) || is_null($row->SQID_REF)) ? 0 : $row->SQID_REF ;
            $tmpITEMID_REF = $row->ITEMID_REF;
            $tmpSEID_REF = (empty($row->SEID_REF) || is_null($row->SEID_REF))? 0 : $row->SEID_REF ;

            
            $objQty = DB::select("select SO_QTY from TBL_TRN_SLSO01_MAT where SOID_REF=? AND ITEMID_REF=? AND (SQA IS NULL OR SQA=?) AND (SEQID_REF IS NULL OR SEQID_REF=?)" ,[$tmpSOID_REF,$tmpITEMID_REF,$tmpSQID_REF,$tmpSEID_REF]);
           
            if(!empty($objQty)) {
                $objList1[$key]->SO_QTY = $objQty[0]->SO_QTY;
            }   

                
               $objDispatchQty = DB::select(" select ISNULL(sum(table1.EXPDPQTY),0) as TOTAL_DIS_QTY from TBL_TRN_SLDP01_MAT table1
                  left join TBL_TRN_SLDP01_HDR table2 on table1.DSPID_REF = table2.DSPID
                  WHERE table1.SOID_REF=? AND table1.ITEMID_REF=? AND table1.SEID_REF=? AND table1.SQID_REF=? " ,[$tmpSOID_REF,$tmpITEMID_REF,$tmpSEID_REF,$tmpSQID_REF]);

               $total_balance_qty = number_format(floatVal($objQty[0]->SO_QTY) -  floatval($objDispatchQty[0]->TOTAL_DIS_QTY), 3, '.', '');
               $calculated_bal_qty = number_format(floatVal($total_balance_qty) +  floatval($row->EXPDPQTY), 3, '.', '');

                $objList1[$key]->BAL_SOQTY = $calculated_bal_qty;
                
            
        }    

     

        $objList1Count = count($objList1);
        if($objList1Count==0){
            $objList1Count=1;
        }

        $objSEMAT = DB::table('TBL_TRN_SLDP01_MAT')                    
                            ->where('DSPID_REF','=',$id)
                            ->select('*')
                            ->orderBy('DSPMATID','ASC')
                            ->get()->toArray();
        $objCount1 = count($objSEMAT);  

        
        $objlastDSPDT = DB::select('SELECT MAX(DSPDT) DSPDT FROM TBL_TRN_SLDP01_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
        
        $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
        WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by UOMCODE ASC', [$CYID_REF,  'A' ]);        

        $objSON = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('FYID_REF','=',$FYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_DOCNO_DEFINITION.*')
        ->first();
        if($objSON->SYSTEM_GRSR == "1")
        {
            if($objSON->PREFIX_RQ == "1")
            {
                $objDSPNO = $objSON->PREFIX;
            }        
            if($objSON->PRE_SEP_RQ == "1")
            {
                if($objSON->PRE_SEP_SLASH == "1")
                {
                $objDSPNO = $objDSPNO.'/';
                }
                if($objSON->PRE_SEP_HYPEN == "1")
                {
                $objDSPNO = $objDSPNO.'-';
                }
            }        
            if($objSON->NO_MAX)
            {   
                $objDSPNO = $objDSPNO.str_pad($objSON->LAST_RECORDNO+1, $objSON->NO_MAX, "0", STR_PAD_LEFT);
            }
            
            if($objSON->NO_SEP_RQ == "1")
            {
                    if($objSON->NO_SEP_SLASH == "1")
                    {
                    $objDSPNO = $objDSPNO.'/';
                    }
                    if($objSON->NO_SEP_HYPEN == "1")
                    {
                    $objDSPNO = $objDSPNO.'-';
                    }
                }
                if($objSON->SUFFIX_RQ == "1")
                {
                    $objDSPNO = $objDSPNO.$objSON->SUFFIX;
                }
            }
            
            
             $objUDF = DB::table('TBL_TRN_SLDP01_UDF')                    
                 ->where('TBL_TRN_SLDP01_UDF.DSPID_REF','=',$id)
                 ->leftJoin('TBL_MST_UDF_DSP','TBL_MST_UDF_DSP.UDF_DSPID','=','TBL_TRN_SLDP01_UDF.UDF_DSPID_REF')                
                 ->select('TBL_TRN_SLDP01_UDF.*','TBL_MST_UDF_DSP.*')
                 ->orderBy('TBL_TRN_SLDP01_UDF.DSPUDFID','ASC')
            ->get()->toArray();

            $objudfCount = count($objUDF);

                if($objudfCount==0){
                    $objudfCount=1;
                }
     
           

           $AlpsStatus =   $this->AlpsStatus();
           $InputStatus=   "disabled";

           $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
      
            return view($this->view.'view', compact(['FormId','objRights','user_approval_level','objMstResponse','objglcode','objglcode2','objslcode2','objSON','objDSPNO','objlastDSPDT','objList1','objList1Count','objUDF','objudfCount','AlpsStatus','InputStatus','TabSetting'])); 

        }
     
    }

  
   public function update(Request $request){
        
        $r_count1 = $request['Row_Count1'];
      
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]))
            {
                $req_data[$i] = [
                    'SEID_REF'        => $request['SEQID_REF_'.$i],
                    'SQID_REF'        => $request['SQA_REF_'.$i],
                    'SOID_REF'        => $request['SOrdID_'.$i],
                    'ITEMID_REF'      => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'       => $request['MAIN_UOMID_REF_'.$i],
                    'ITEMSPECI'       => $request['Itemspec_'.$i],
                    'BALSOQTY'        => $request['BAL_SOQTY_'.$i],
                    'EXPDPQTY'        => $request['EXP_DIS_QTY_'.$i],
                ];
            }
        }
        
     

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
    
        $r_count2 = $request['Row_Count2'];  
        $udffield_Data = [];      
        for ($i=0; $i<=$r_count2; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDF_DSPID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
            }
            
        }
            
          
        if(count($udffield_Data)>0){

            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        

        }else{
        
            $XMLUDF = NULL;
        }
        
        

            $VTID_REF     =   $this->vtid_ref;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'EDIT';
            $IPADDRESS = $request->getClientIp();

          
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');

            $DSPNO = $request['DSPNO'];
            $DSPDT = $request['DSPDT'];
            $GLID_REF = $request['GLID_REF'];
            $SLID_REF = $request['SLID_REF'];

            $log_data = [ 
                $DSPNO,     $DSPDT,         $GLID_REF,      $SLID_REF,      $CYID_REF, 
                $BRID_REF,  $FYID_REF,      $VTID_REF,      $XMLMAT,        $XMLUDF,
                $USERID,    Date('Y-m-d'),  Date('h:i:s.u'),$ACTIONNAME,    $IPADDRESS
            ];

          
            
            $sp_result = DB::select('EXEC SP_DSP_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
            
        
    
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
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
                    $req_data[$i] = [
                        'SEID_REF'        => $request['SEQID_REF_'.$i],
                        'SQID_REF'        => $request['SQA_REF_'.$i],
                        'SOID_REF'        => $request['SOrdID_'.$i],
                        'ITEMID_REF'      => $request['ITEMID_REF_'.$i],
                        'UOMID_REF'       => $request['MAIN_UOMID_REF_'.$i],
                        'ITEMSPECI'       => $request['Itemspec_'.$i],
                        'BALSOQTY'        => $request['BAL_SOQTY_'.$i],
                        'EXPDPQTY'        => $request['EXP_DIS_QTY_'.$i],
                    ];
                }
            }
            
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        
            $r_count2 = $request['Row_Count2'];  
            $udffield_Data = [];      
            for ($i=0; $i<=$r_count2; $i++)
            {
                if(isset( $request['udffie_'.$i]))
                {
                    $udffield_Data[$i]['UDF_DSPID_REF'] = $request['udffie_'.$i]; 
                    $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
                }
                
            }
                
               
            if(count($udffield_Data)>0){
    
                $udffield_wrapped["UDF"] = $udffield_Data;  
                $udffield__xml = ArrayToXml::convert($udffield_wrapped);
                $XMLUDF = $udffield__xml;        
    
            }else{
            
                $XMLUDF = NULL;
            }
            
            
    
                $DSPNO = $request['DSPNO'];
                $DSPDT = $request['DSPDT'];
                $GLID_REF = $request['GLID_REF'];
                $SLID_REF = $request['SLID_REF'];
    
                $log_data = [ 
                    $DSPNO,     $DSPDT,         $GLID_REF,      $SLID_REF,      $CYID_REF, 
                    $BRID_REF,  $FYID_REF,      $VTID_REF,      $XMLMAT,        $XMLUDF,
                    $USERID,    Date('Y-m-d'),  Date('h:i:s.u'),$ACTIONNAME,    $IPADDRESS
                ];
    
               
                
                $sp_result = DB::select('EXEC SP_DSP_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);  
            
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
                $TABLE      =   "TBL_TRN_SLDP01_HDR";
                $FIELD      =   "DSPID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
           
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
    
            if($sp_result[0]->RESULT=="All records approved"){
    
            return Response::json(['approve' =>true,'msg' => 'Record successfully Approved.']);
    
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
        $TABLE      =   "TBL_TRN_SLDP01_HDR";
        $FIELD      =   "DSPID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
         'NT'  => 'TBL_TRN_SLDP01_MAT',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_SLDP01_UDF',
           ];
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $sp_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_DSP  ?,?,?,?, ?,?,?,?, ?,?,?,?', $sp_cancel_data);

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
    
    $image_path         =   "docs/company".$CYID_REF."/DailySalesPlan";     
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
                        } 
                        
                    }else{

                        $invlid_files = $invlid_files.$filenamewithextension." (invalid extension)  ";                             
                    }
                
                }else{
                        
                    $invlid_files = $invlid_files.$filenamewithextension." (invalid)"; 
                }

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
