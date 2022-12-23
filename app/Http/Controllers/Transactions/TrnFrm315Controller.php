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

class TrnFrm315Controller extends Controller
{
    protected $form_id = 315;
    protected $vtid_ref   = 403; 
   
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];
   
    public function __construct()
    {
        $this->middleware('auth');
    }

   
    public function index(){    
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

        $objDataList	=	DB::select("select hdr.ROID,hdr.RONO,hdr.RODT,hdr.OVFRM_DT,hdr.OVTOM_DT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.ROID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_SLRO01_HDR hdr
                            on a.VID = hdr.ROID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_SUBLEDGER sl ON hdr.SLID_REF = sl.SGLID 
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.ROID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );
   
            
        return view('transactions.sales.ReleaseOrder.trnfrm315',compact(['REQUEST_DATA','objRights','objDataList']));        
    }
	
	public function ViewReport($request) 
    {
        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
       // dd($myValue);  
        $ROID       =   $myValue['ROID'];
        $Flag       =   $myValue['Flag'];

        
        
        
        $ssrs = new \SSRS\Report('http://103.139.58.23:8181//ReportServer/', array('username' => 'App', 'password' => 'admin@123'));
        $result = $ssrs->loadReport('/ERP/ROPrint');
        
        $reportParameters = array(
            'ROID' => $ROID,
        );
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
        ->setExecutionParameters($parameters);
        if($Flag == 'H')
        {
            $output = $ssrs->render('HTML4.0'); // PDF | XML | CSV
            echo $output;
        }
        else if($Flag == 'P')
        {
            $output = $ssrs->render('PDF'); // PDF | XML | CSV | HTML4.0
            return $output->download('Report.pdf');
        }
        else if($Flag == 'E')
        {
            $output = $ssrs->render('EXCEL'); // PDF | XML | CSV | HTML4.0
            return $output->download('Report.xls');
        }
        else if($Flag == 'R')
        {
            $output = $ssrs->render('HTML4.0'); // PDF | XML | CSV | HTML4.0
            echo $output;

        }
         
     }

    public function add(){       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        
        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_SLRO01_HDR',
            'HDR_ID'=>'ROID',
            'HDR_DOC_NO'=>'RONO',
            'HDR_DOC_DT'=>'RODT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        
        

        
        
        $ObjUnionUDF = DB::table("TBL_MST_UDF_FOR_RO")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDFROID')->from('TBL_MST_UDF_FOR_RO')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF)
                                                ->where('BRID_REF','=',$BRID_REF);
                                                               
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF);
                                
                   

        $objUdfROData = DB::table('TBL_MST_UDF_FOR_RO')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
          
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdfROData);
    

        
        $FormId = $this->form_id;

        $lastdt=$this->LastApprovedDocDate(); 

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
            
       
    return view('transactions.sales.ReleaseOrder.trnfrm315add',
    compact(['objUdfROData','objCountUDF','FormId','lastdt','TabSetting','doc_req','docarray']));       
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




    public function getsalesorder(Request $request){
        $Status = "A";
        $SLID_REF = $request['SLID_REF'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $SP_PARAMETERS = [$CYID_REF,$BRID_REF,$SLID_REF];

        $ObjData =  DB::select('EXEC SP_SO_GETLIST_RO ?,?,?', $SP_PARAMETERS);
    
            if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){

                $row = '';
                $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_SOID[]" id="socode_'.$index.'" class="clssoid" value="'.$dataRow-> SOID.'" ></td>';
                $row = $row.'<td class="ROW2">'.$dataRow->SONO;
                $row = $row.'<input type="hidden" id="txtsocode_'.$index.'" data-desc="'.$dataRow->SONO .'"value="'.$dataRow->SOID.'"/></td><td class="ROW3">'.$dataRow->SODT.'</td></tr>';

                echo $row;
            }
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    
    }

    
    public function getsodata(Request $request){
        $Status = "A";
        $id = $request['id'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');

        $objSO = DB::table('TBL_TRN_SLSO01_HDR')
        ->leftJoin('TBL_MST_CURRENCY','TBL_MST_CURRENCY.CRID','=','TBL_TRN_SLSO01_HDR.CRID_REF')
        ->where('TBL_TRN_SLSO01_HDR.CYID_REF','=',$CYID_REF)
        ->where('TBL_TRN_SLSO01_HDR.BRID_REF','=',$BRID_REF)
        ->where('TBL_TRN_SLSO01_HDR.SOID','=',$id)
        ->select('TBL_TRN_SLSO01_HDR.*','TBL_MST_CURRENCY.CRCODE')
        ->first();
    
        if(!empty($objSO))
        {   
            $data[] = $objSO; 
            $json_data = array("data"=> $data);            
            echo json_encode($json_data);
        }else{
            echo '';
        }
        exit();    
    }
    
    public function getBillTo(Request $request){
        $Status = "A";
        $id = $request['id'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');

        $objSO = DB::table('TBL_TRN_SLSO01_HDR')
        ->where('TBL_TRN_SLSO01_HDR.CYID_REF','=',$CYID_REF)
        ->where('TBL_TRN_SLSO01_HDR.BRID_REF','=',$BRID_REF)
        ->where('TBL_TRN_SLSO01_HDR.SOID','=',$id)
        ->select('TBL_TRN_SLSO01_HDR.*')
        ->first();

        $SLID_REF = $objSO->SLID_REF;
        $BILLTO = $objSO->BILLTO;
        
        
        $ObjBillTo =  DB::select('SELECT top 1 * FROM TBL_MST_CUSTOMERLOCATION  
                    WHERE CLID= ? ', [$BILLTO]);

        $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                    WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                    [$Status,$ObjBillTo[0]->CITYID_REF,$ObjBillTo[0]->CTRYID_REF,$ObjBillTo[0]->STID_REF]);

        $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                    WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBillTo[0]->STID_REF,$ObjBillTo[0]->CTRYID_REF]);

        $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                    WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBillTo[0]->CTRYID_REF]);

        $ObjAddressID = $ObjBillTo[0]->CLID;
                if(!empty($ObjBillTo)){
                    
                $objAddress = $ObjBillTo[0]->CADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                
                $row = '';
                $row = $row.'<input type="text" name="txtBILLTO" id="txtBILLTO" class="form-control"  autocomplete="off" value="'. $objAddress.'" readonly/>';
                $row = $row.'<input type="hidden" name="BILLTO" id="BILLTO" class="form-control" autocomplete="off" value="'. $ObjAddressID.'" readonly/>';
                
                echo $row;
                }else{
                    echo '';
                }
                exit();
    
    }

    public function getShipTo(Request $request){
        $Status = "A";
        $id = $request['id'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');

        $objSO = DB::table('TBL_TRN_SLSO01_HDR')
        ->where('TBL_TRN_SLSO01_HDR.CYID_REF','=',$CYID_REF)
        ->where('TBL_TRN_SLSO01_HDR.BRID_REF','=',$BRID_REF)
        ->where('TBL_TRN_SLSO01_HDR.SOID','=',$id)
        ->select('TBL_TRN_SLSO01_HDR.*')
        ->first();

        $SLID_REF = $objSO->SLID_REF;
        $SHIPTO = $objSO->SHIPTO;

        $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_CUSTOMERLOCATION  
                    WHERE CLID= ? ', [$SHIPTO]);

        $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  
        WHERE BRID= ? ', [$BRID_REF]);

        if($ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF)
        {
            $TAXSTATE = 'WithinState';
        }
        else
        {
            $TAXSTATE = 'OutofState';
        }

        $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                    WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                    [$Status,$ObjSHIPTO[0]->CITYID_REF,$ObjSHIPTO[0]->CTRYID_REF,$ObjSHIPTO[0]->STID_REF]);

        $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                    WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjSHIPTO[0]->STID_REF,$ObjSHIPTO[0]->CTRYID_REF]);

        $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                    WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjSHIPTO[0]->CTRYID_REF]);

        $ObjAddressID = $ObjSHIPTO[0]->CLID;
                if(!empty($ObjSHIPTO)){
                    
                $objAddress = $ObjSHIPTO[0]->CADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                
                $row = '';
                $row = $row.'<input type="text" name="txtSHIPTO" id="txtSHIPTO" class="form-control"  autocomplete="off" value="'. $objAddress.'" readonly/>';
                $row = $row.'<input type="hidden" name="SHIPTO" id="SHIPTO" class="form-control" autocomplete="off" value="'. $ObjAddressID.'" readonly/>';
                $row = $row.'<input type="hidden" name="Tax_State" id="Tax_State" class="form-control" autocomplete="off" value="'. $TAXSTATE.'" readonly/>';
                
                echo $row;
                }else{
                    echo '';
                }
                exit();
    
    }


    public function getItemDetails(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $id = $request['id']; 
        $ROID = $request['ROID']; 
        
        $sp_popup = [
            $CYID_REF, $BRID_REF, $id
        ]; 
        
        $ObjItem = DB::select('EXEC sp_get_items_popup_rowise ?,?,?', $sp_popup);

                if(!empty($ObjItem)){

                    foreach ($ObjItem as $index=>$dataRow){
                        $ITEMID_REF         =   isset($dataRow->ITEMID_REF)?$dataRow->ITEMID_REF:NULL;
                        $ICODE              =   isset($dataRow->ICODE)?$dataRow->ICODE:NULL;
                        $NAME               =   isset($dataRow->NAME)?$dataRow->NAME:NULL;
                        $GROUPCODE          =   isset($dataRow->GROUPCODE)?$dataRow->GROUPCODE:NULL;
                        $GROUPNAME          =   isset($dataRow->GROUPNAME)?$dataRow->GROUPNAME:NULL;
                        $ITEMSPECI          =   isset($dataRow->ITEMSPECI)?$dataRow->ITEMSPECI:NULL;
                        $MAIN_UOMID_REF     =   isset($dataRow->MAIN_UOMID_REF)?$dataRow->MAIN_UOMID_REF:NULL;
                        $Main_UOM           =   isset($dataRow->Main_UOM)?$dataRow->Main_UOM:NULL;
                        $ALT_UOMID_REF      =   isset($dataRow->ALT_UOMID_REF)?$dataRow->ALT_UOMID_REF:NULL;
                        $Alt_UOM            =   isset($dataRow->Alt_UOM)?$dataRow->Alt_UOM:NULL;
                        $SO_QTY             =   isset($dataRow->SO_QTY)?$dataRow->SO_QTY:NULL;
                        $TOQTY              =   isset($dataRow->TOQTY)?$dataRow->TOQTY:NULL;
                        $SOID_REF           =   isset($dataRow->SOID_REF)?$dataRow->SOID_REF:NULL;
                        $SQA                =   isset($dataRow->SQA)?$dataRow->SQA:NULL;
                        $SEQID_REF          =   isset($dataRow->SEQID_REF)?$dataRow->SEQID_REF:NULL;

                        $IDSPF_VAL='';
                        if($ROID !=""){
                            $DataArr=[];
                            $IDSPF_VAL='';
                            $ROID_REF = DB::select("select * from TBL_TRN_SLRO01_ISP WHERE ROID_REF='$ROID' AND ITEMID_REF='$ITEMID_REF'");

                            if(!empty($ROID_REF)){

                                foreach($ROID_REF as $val){

                                    $DataArr[]=array(
                                        'SPFID'=>trim($val->Specification_ID),
                                        'SPFNAME'=>trim($val->Specification_Name),
                                        'SPFDES'=>trim($val->ITEM_SPECI)
                                    );
                                }

                                $IDSPF_VAL=json_encode($DataArr);
                            }
                        }

                       $IDSPF_VALUE="value='$IDSPF_VAL'";

                        $row = '';
                        $row.=' <tr class="participantRow">
                                <td style="width:15%;"><input type="text" name="popupITEMID_'.$index.'" id="popupITEMID_'.$index.'" class="form-control" value="'.$ICODE.'" autocomplete="off"  readonly/></td></td>
                                <td hidden><input type="hidden" name="ITEMID_REF_'.$index.'" id="ITEMID_REF_'.$index.'" class="form-control" value="'.$ITEMID_REF.'" autocomplete="off" /></td>
                                <td hidden><input type="hidden" name="SEID_REF_'.$index.'" id="SEID_REF_'.$index.'" class="form-control" value="'.$SEQID_REF.'" autocomplete="off" /></td>
                                <td hidden><input type="hidden" name="SQID_REF_'.$index.'" id="SQID_REF_'.$index.'" class="form-control" value="'.$SQA.'" autocomplete="off" /></td>
                                <td hidden><input type="hidden" name="SOID_REF_'.$index.'" id="SOID_REF_'.$index.'" class="form-control" value="'.$SOID_REF.'" autocomplete="off" /></td>
                                <td style=" width:15%;"><input type="text" name="ItemName_'.$index.'" id="ItemName_'.$index.'" class="form-control" value="'.$NAME.'" autocomplete="off"  readonly/></td>
                                <input type="hidden" name="grpCode_'.$index.'" id="grpCode_'.$index.'" value="'.$GROUPCODE.'-'.$GROUPNAME.'">
                                <td style=" width:10%; align:center;" ><button class="btn" name="BtnItemspec_'.$index.'" id="BtnItemspec_'.$index.'" type="button"><i class="fa fa-clone"></i></button></td>
                                <td style=" width:15%;"><input type="text" name="MAIN_UOM_'.$index.'" id="MAIN_UOM_'.$index.'" class="form-control" value="'.$Main_UOM.'" autocomplete="off"  readonly/></td>
                                <td style=" width:15%;" hidden><input type="text" name="MAIN_UOMID_REF_'.$index.'" id="MAIN_UOMID_REF_'.$index.'" class="form-control" value="'.$MAIN_UOMID_REF.'" autocomplete="off"  readonly/></td>
                                <td style=" width:15%;"><input type="text" name="SOQTYM_'.$index.'" id="SOQTYM_'.$index.'" class="form-control" value="'.$SO_QTY.'" maxlength="13"  autocomplete="off"  readonly/></td>
                                <td style=" width:15%;"><input type="text" name="ALT_UOM_'.$index.'" id="ALT_UOM_'.$index.'" class="form-control" value="'.$Alt_UOM.'" autocomplete="off"  readonly/></td>
                                <td style=" width:15%;" hidden><input type="text" name="ALT_UOMID_REF_'.$index.'" id="ALT_UOMID_REF_'.$index.'" value="'.$ALT_UOMID_REF.'" class="form-control"  autocomplete="off"  readonly/></td>
                                <td style=" width:15%;"><input type="text" name="SOQTYA_'.$index.'" id="SOQTYA_'.$index.'" class="form-control" value="'.$TOQTY.'" maxlength="13" autocomplete="off"  readonly/></td>
                                <td hidden><input type="text" name="IDSPF_'.$index.'" id="IDSPF_'.$index.'" '.$IDSPF_VALUE.'  /></td>
                                
                                </tr>'; 
                        echo $row;                          
                    } 
                    
                } 
                         
                else{
                 echo '<tr><td> Record not found.</td></tr>';
                }
        exit();
    }

    public function getItemDetailsEdit(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $id = $request['id']; 
        $ROID = $request['ROID']; 
        
        $sp_popup = [
            $CYID_REF, $BRID_REF, $id
        ]; 
        
        //$ObjItem = DB::select('EXEC sp_get_items_popup_rowise ?,?,?', $sp_popup);

        $ObjItem = DB::select("SELECT  
        a.ITEMID_REF,a.SOID_REF,NULL as SQA,NULL as SEQID_REF,a.MAIN_UOMID_REF,a.ALT_UOMID_REF, 
        c.ICODE, c.NAME, a.ITEM_SPECI ,a.SOQTYM AS SO_QTY, G.GROUPCODE, G.GROUPNAME,    
        isnull((select UOMCODE+'-'+DESCRIPTIONS from TBL_MST_UOM where UOMID = a.MAIN_UOMID_REF),'') as Main_UOM,                   
        isnull((select UOMCODE+'-'+DESCRIPTIONS from TBL_MST_UOM where UOMID = a.ALT_UOMID_REF),'') as Alt_UOM,                  
        convert(numeric(12,3),isnull(isnull(a.SOQTYM,0)*isnull((select TO_QTY from TBL_MST_ITEM_UOMCONV where ITEMID_REF = a.ITEMID_REF and TO_UOMID_REF = a.ALT_UOMID_REF),0),0)) as TOQTY,                  
        a.SEID_REF, a.SQID_REF,a.RO_MATID    
        from TBL_TRN_SLRO01_MAT a(nolock)               
        left outer join TBL_MST_ITEM c(nolock) on a.ITEMID_REF = c.ITEMID  
        INNER JOIN TBL_MST_ITEMGROUP G(NOLOCK) ON C.ITEMGID_REF = G.ITEMGID   
        where a.ROID_REF = '$ROID'");

      
                if(!empty($ObjItem)){

                    foreach ($ObjItem as $index=>$dataRow){
                        $ITEMID_REF         =   isset($dataRow->ITEMID_REF)?$dataRow->ITEMID_REF:NULL;
                        $ICODE              =   isset($dataRow->ICODE)?$dataRow->ICODE:NULL;
                        $NAME               =   isset($dataRow->NAME)?$dataRow->NAME:NULL;
                        $GROUPCODE          =   isset($dataRow->GROUPCODE)?$dataRow->GROUPCODE:NULL;
                        $GROUPNAME          =   isset($dataRow->GROUPNAME)?$dataRow->GROUPNAME:NULL;
                        $ITEMSPECI          =   isset($dataRow->ITEMSPECI)?$dataRow->ITEMSPECI:NULL;
                        $MAIN_UOMID_REF     =   isset($dataRow->MAIN_UOMID_REF)?$dataRow->MAIN_UOMID_REF:NULL;
                        $Main_UOM           =   isset($dataRow->Main_UOM)?$dataRow->Main_UOM:NULL;
                        $ALT_UOMID_REF      =   isset($dataRow->ALT_UOMID_REF)?$dataRow->ALT_UOMID_REF:NULL;
                        $Alt_UOM            =   isset($dataRow->Alt_UOM)?$dataRow->Alt_UOM:NULL;
                        $SO_QTY             =   isset($dataRow->SO_QTY)?$dataRow->SO_QTY:NULL;
                        $TOQTY              =   isset($dataRow->TOQTY)?$dataRow->TOQTY:NULL;
                        $SOID_REF           =   isset($dataRow->SOID_REF)?$dataRow->SOID_REF:NULL;
                        $SQA                =   isset($dataRow->SQA)?$dataRow->SQA:NULL;
                        $SEQID_REF          =   isset($dataRow->SEQID_REF)?$dataRow->SEQID_REF:NULL;

                        $IDSPF_VAL='';
                        if($ROID !=""){
                            $DataArr=[];
                            $IDSPF_VAL='';
                            $ROID_REF = DB::select("select * from TBL_TRN_SLRO01_ISP WHERE ROID_REF='$ROID' AND ITEMID_REF='$ITEMID_REF'");

                            if(!empty($ROID_REF)){

                                foreach($ROID_REF as $val){

                                    $DataArr[]=array(
                                        'SPFID'=>trim($val->Specification_ID),
                                        'SPFNAME'=>trim($val->Specification_Name),
                                        'SPFDES'=>trim($val->ITEM_SPECI)
                                    );
                                }

                                $IDSPF_VAL=json_encode($DataArr);
                            }
                        }

                       $IDSPF_VALUE="value='$IDSPF_VAL'";

                        $row = '';
                        $row.=' <tr class="participantRow">
                                <td style="width:15%;"><input type="text" name="popupITEMID_'.$index.'" id="popupITEMID_'.$index.'" class="form-control" value="'.$ICODE.'" autocomplete="off"  readonly/></td></td>
                                <td hidden><input type="hidden" name="ITEMID_REF_'.$index.'" id="ITEMID_REF_'.$index.'" class="form-control" value="'.$ITEMID_REF.'" autocomplete="off" /></td>
                                <td hidden><input type="hidden" name="SEID_REF_'.$index.'" id="SEID_REF_'.$index.'" class="form-control" value="'.$SEQID_REF.'" autocomplete="off" /></td>
                                <td hidden><input type="hidden" name="SQID_REF_'.$index.'" id="SQID_REF_'.$index.'" class="form-control" value="'.$SQA.'" autocomplete="off" /></td>
                                <td hidden><input type="hidden" name="SOID_REF_'.$index.'" id="SOID_REF_'.$index.'" class="form-control" value="'.$SOID_REF.'" autocomplete="off" /></td>
                                <td style=" width:15%;"><input type="text" name="ItemName_'.$index.'" id="ItemName_'.$index.'" class="form-control" value="'.$NAME.'" autocomplete="off"  readonly/></td>
                                <input type="hidden" name="grpCode_'.$index.'" id="grpCode_'.$index.'" value="'.$GROUPCODE.'-'.$GROUPNAME.'">
                                <td style=" width:10%; align:center;" ><button class="btn" name="BtnItemspec_'.$index.'" id="BtnItemspec_'.$index.'" type="button"><i class="fa fa-clone"></i></button></td>
                                <td style=" width:15%;"><input type="text" name="MAIN_UOM_'.$index.'" id="MAIN_UOM_'.$index.'" class="form-control" value="'.$Main_UOM.'" autocomplete="off"  readonly/></td>
                                <td style=" width:15%;" hidden><input type="text" name="MAIN_UOMID_REF_'.$index.'" id="MAIN_UOMID_REF_'.$index.'" class="form-control" value="'.$MAIN_UOMID_REF.'" autocomplete="off"  readonly/></td>
                                <td style=" width:15%;"><input type="text" name="SOQTYM_'.$index.'" id="SOQTYM_'.$index.'" class="form-control" value="'.$SO_QTY.'" maxlength="13"  autocomplete="off"  readonly/></td>
                                <td style=" width:15%;"><input type="text" name="ALT_UOM_'.$index.'" id="ALT_UOM_'.$index.'" class="form-control" value="'.$Alt_UOM.'" autocomplete="off"  readonly/></td>
                                <td style=" width:15%;" hidden><input type="text" name="ALT_UOMID_REF_'.$index.'" id="ALT_UOMID_REF_'.$index.'" value="'.$ALT_UOMID_REF.'" class="form-control"  autocomplete="off"  readonly/></td>
                                <td style=" width:15%;"><input type="text" name="SOQTYA_'.$index.'" id="SOQTYA_'.$index.'" class="form-control" value="'.$TOQTY.'" maxlength="13" autocomplete="off"  readonly/></td>
                                <td hidden><input type="text" name="IDSPF_'.$index.'" id="IDSPF_'.$index.'" '.$IDSPF_VALUE.'  /></td>
                                
                                </tr>'; 
                        echo $row;                          
                    } 
                    
                } 
                         
                else{
                 echo '<tr><td> Record not found.</td></tr>';
                }
        exit();
    }

    public function getItemCount(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $id = $request['id'];

        $sp_popup = [
            $CYID_REF, $BRID_REF, $id
        ]; 
        
            $ObjItem = DB::select('EXEC sp_get_items_popup_rowise ?,?,?', $sp_popup);
        
            if(!empty($ObjItem))
            {
                $objCount = count($ObjItem);
                echo $objCount;   
            }           
            else
            {
                echo '1';
            }
        exit();
    }

    public function getTNCData(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $id = $request['id'];

        $objTNC = DB::table('TBL_TRN_SLSO01_TNC')
        ->Join('TBL_MST_TNC','TBL_MST_TNC.TNCID','=','TBL_TRN_SLSO01_TNC.TNCID_REF')
        ->Join('TBL_TRN_SLSO01_HDR','TBL_TRN_SLSO01_HDR.SOID','=','TBL_TRN_SLSO01_TNC.SOID_REF')
        ->where('TBL_TRN_SLSO01_HDR.CYID_REF','=',$CYID_REF)
        ->where('TBL_TRN_SLSO01_HDR.BRID_REF','=',$BRID_REF)
        ->where('TBL_TRN_SLSO01_HDR.SOID','=',$id)
        ->where('TBL_MST_TNC.FOR_SALE','=','1')
        ->select('TBL_TRN_SLSO01_TNC.TNCID_REF','TBL_MST_TNC.TNC_CODE','TBL_MST_TNC.TNC_DESC')
        ->first();
    
        if(!empty($objTNC))
        {   
            $data[] = $objTNC; 
            $json_data = array("data"=> $data);            
            echo json_encode($json_data);
        }else{
            echo '';
        }
        exit();
    }

    public function getTNCDetails(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $id = $request['id'];

        $sp_popup = [
            $CYID_REF, $BRID_REF, $id
        ]; 
        
            $ObjTNC = DB::select('EXEC sp_get_tnc_sowise ?,?,?', $sp_popup);
        
                if(!empty($ObjTNC)){

                    foreach ($ObjTNC as $index=>$dataRow){
                        $popupTNCDID         =   isset($dataRow->TNC_NAME)?$dataRow->TNC_NAME:NULL;
                        $TNCDID_REF          =   isset($dataRow->TNCDID_REF)?$dataRow->TNCDID_REF:NULL;
                        $TNCismandatory      =   isset($dataRow->IS_MANDATORY)?$dataRow->IS_MANDATORY:0;
                        $VALUE_TYPE          =   isset($dataRow->VALUE_TYPE)?$dataRow->VALUE_TYPE:NULL;
                        $VALUE               =   isset($dataRow->VALUE)?$dataRow->VALUE:NULL;
                    
                        
                        $row = '';
                        $row.=' <tr class="participantRow3">
                                <td><input type="text" name="popupTNCDID_'.$index.'" id="popupTNCDID_'.$index.'" class="form-control" value="'.$popupTNCDID.'"  autocomplete="off"  readonly/></td>
                                <td hidden><input type="hidden" name="TNCDID_REF_'.$index.'" id="TNCDID_REF_'.$index.'" value="'.$TNCDID_REF.'" class="form-control" autocomplete="off" /></td>
                                <td hidden><input type="hidden" name="TNCismandatory_'.$index.'" id="TNCismandatory_'.$index.'" value="'.$TNCismandatory.'" class="form-control" autocomplete="off" /></td>
                                <td id="tdinputid_'.$index.'">';
                        if($VALUE_TYPE == 'Text')
                        {
                            $row = $row.'<input type="text"  name="tncdetvalue_'.$index.'" id="tncdetvalue_'.$index.'" value="'.$VALUE.'" class="form-control" readonly />';
                        }
                        else if($VALUE_TYPE == 'Date')
                        {
                            $row = $row.'<input type="date" placeholder="dd/mm/yyyy"  name="tncdetvalue_'.$index.'" id="tncdetvalue_'.$index.'" value="'.$VALUE.'" class="form-control" readonly />';
                        }
                        else if($VALUE_TYPE == 'Time')
                        {
                            $row = $row.'<input type="time" placeholder="h:i"  name="tncdetvalue_'.$index.'" id="tncdetvalue_'.$index.'" value="'.$VALUE.'" class="form-control" readonly />';
                        }
                        else
                        {
                            $row = $row.'<input type="text"  name="tncdetvalue_'.$index.'" id="tncdetvalue_'.$index.'" value="'.$VALUE.'" class="form-control" readonly />';
                        }
                                    
                        $row = $row.'</td>
                                <td align="center" ><button class="btn add TNC" title="add" data-toggle="tooltip"  type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DTNC" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                </tr>'; 
                        echo $row;                          
                    } 
                    
                }           
                else{
                 echo '<tr><td> Record not found.</td></tr>';
                }
        exit();
    }

    public function getTNCCount(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $id = $request['id'];

        $sp_popup = [
            $CYID_REF, $BRID_REF, $id
        ]; 
        
            $ObjTNC = DB::select('EXEC sp_get_tnc_sowise ?,?,?', $sp_popup);
        
            if(!empty($ObjTNC)){

                $objCount = count($ObjTNC);
                echo $objCount;   
            }           
            else
            {
                echo '1';
            }
        exit();
    }

    

    


 
   public function attachment($id){

    if(!is_null($id))
    {
        $objReleaseorder = DB::table("TBL_TRN_SLRO01_HDR")
                        ->where('ROID','=',$id)
                        ->select('TBL_TRN_SLRO01_HDR.*')
                        ->first(); 

        $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
                    ->where('VTID','=',$this->vtid_ref)
                    ->select('VTID','VCODE','DESCRIPTIONS')
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

                

            return view('transactions.sales.ReleaseOrder.trnfrm315attachment',compact(['objReleaseorder','objMstVoucherType','objAttachments']));
    }

}

    
   public function save(Request $request) {
    
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];       
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i]))
            {
                $req_data[$i] = [
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'ITEMSPECI' => '',
                    'MAIN_UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'SOQTYM' => $request['SOQTYM_'.$i],
                    'ALT_UOMID_REF' => $request['ALT_UOMID_REF_'.$i],
                    'SOID_REF'=> (!empty($request['SOID_REF_'.$i])) == 'true' ? $request['SOID_REF_'.$i] : "0" ,
                    'SQID_REF'=> (!empty($request['SQID_REF_'.$i])) == 'true' ? $request['SQID_REF_'.$i] : "0" ,
                    'SEID_REF'=> (!empty($request['SEID_REF_'.$i])) == 'true' ? $request['SEID_REF_'.$i] : "0" ,
                ];                 

            }
        }
            
        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);

        //////////specification
        for ($i=0; $i<=$r_count1; $i++)
        {        
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['IDSPF_'.$i])){

                $spefction_data = $request['IDSPF_'.$i];
                $json_decode_data = json_decode($spefction_data);

                foreach ($json_decode_data as $key=>$decode_data){      
                $req_data1[] = [
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'MAIN_UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'SOQTYM' => $request['SOQTYM_'.$i], 
                    'SOID_REF'=> (!empty($request['SOID_REF_'.$i])) == 'true' ? $request['SOID_REF_'.$i] : "0" ,
                    'SQID_REF'=> (!empty($request['SQID_REF_'.$i])) == 'true' ? $request['SQID_REF_'.$i] : "0" ,
                    'SEID_REF'=> (!empty($request['SEID_REF_'.$i])) == 'true' ? $request['SEID_REF_'.$i] : "0" ,
                    'Specification_ID'    => $decode_data->SPFID,
                    'Specification_Name'    => $decode_data->SPFNAME,
                    'ITEM_SPECI'    => $decode_data->SPFDES,                   
                
                ];             
             }                 

            }           
        }     
       
        if(isset($req_data1))
            { 
                $wrapped_links1["ISP"] = $req_data1; 
                $XMLISP = ArrayToXml::convert($wrapped_links1);
            }
            else
            {
                $XMLISP = NULL; 
            }

        // $wrapped_links["ISP"] = $req_data1; 
        // $XMLISP = ArrayToXml::convert($wrapped_links);

        for ($i=0; $i<=$r_count2; $i++)
        {
            if(isset($request['TNCID_REF']) && !is_null($request['TNCID_REF']))
            {
                if(isset($request['TNCDID_REF_'.$i]))
                {
                    $reqdata2[$i] = [
                        'TNCID_REF'     => $request['TNCID_REF'] ,
                        'TNCDID_REF'    => $request['TNCDID_REF_'.$i],
                        'VALUE'         => $request['tncdetvalue_'.$i],
                    ];
                }
            }
            
        }
            if(isset($reqdata2))
            { 
            $wrapped_links2["TNC"] = $reqdata2;
            $XMLTNC = ArrayToXml::convert($wrapped_links2);
            }
            else
            {
            $XMLTNC = NULL; 
            }  
        
        for ($i=0; $i<=$r_count3; $i++)
        {
                if(isset($request['UDFROID_REF_'.$i]) && !is_null($request['UDFROID_REF_'.$i]))
                {
                    $reqdata3[$i] = [
                        'UDFROID_REF'   => $request['UDFROID_REF_'.$i],
                        'VALUE'         => $request['udfvalue_'.$i],
                    ];
                }
            
        }
            if(isset($reqdata3))
            { 
                $wrapped_links3["UDF"] = $reqdata3; 
                $XMLUDF = ArrayToXml::convert($wrapped_links3);
            }
            else
            {
                $XMLUDF = NULL; 
            }


            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $RONO = $request['RONO'];
            $RODT = $request['RODT'];
            $SLID_REF = $request['SLID_REF'];
            $SOID_REF = $request['SOID_REF'];
            $FC = (isset($request['hdn_FC']) && $request['hdn_FC']=="0" ? 0 : 1);
            $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : NULL;
            $CONVERSION_FACT = (isset($request['CONVERSION_FACT'])) ? $request['CONVERSION_FACT'] : 0;
            $OVFRM_DT = $request['OVFRM_DT'];
            $OVTOM_DT = $request['OVTOM_DT'];
            $CUSTOMERPONO = $request['CUSTOMERPONO'];
            $CUSTOMERDT = $request['CUSTOMERDT'];
            $BILLTO = $request['BILLTO'];
            $SHIPTO = $request['SHIPTO'];
            $REMARKS = $request['REMARKS'];
            $DELIVER_DATE = $request['DELIVER_DATE'];
            

            $log_data = [ 
                $RONO,$RODT,$SLID_REF,$SOID_REF,$OVFRM_DT,$OVTOM_DT,$FC,$CRID_REF,$CONVERSION_FACT,
                $BILLTO,$SHIPTO,$REMARKS,$CYID_REF, $BRID_REF, $FYID_REF,$VTID_REF,
                $XMLMAT, $XMLISP,$XMLTNC,$XMLUDF,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, 
                $IPADDRESS,$CUSTOMERDT, $CUSTOMERPONO,$DELIVER_DATE
            ];

            //dd($log_data);
          
            
            $sp_result = DB::select('EXEC SP_RO_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
            
        
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            exit();   
     }

     

    public function edit($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';

        $DOCNO_TYPE = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('FYID_REF','=',$FYID_REF)
        ->where('STATUS','=',$Status)
        ->select('MANUAL_SR')
        ->first();
        
        if(!is_null($id))
        {
            $objRO = DB::table('TBL_TRN_SLRO01_HDR')
                             ->where('TBL_TRN_SLRO01_HDR.FYID_REF','=',Session::get('FYID_REF'))
                             ->where('TBL_TRN_SLRO01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_TRN_SLRO01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                             ->where('TBL_TRN_SLRO01_HDR.ROID','=',$id)
                             ->select('TBL_TRN_SLRO01_HDR.*')
                             ->first();
            $log_data = [ 
                $id
            ];

            $objROMAT = [];
            if(isset($objRO) && !empty($objRO)){
                $objROMAT = DB::select('EXEC sp_get_release_order_material ?', $log_data);
            }

            $objCount1 = count($objROMAT);
          
            $objROTNC = DB::select('EXEC sp_get_release_order_TNC ?', $log_data);
            $objCount2 = count($objROTNC);

            $objROUDF = DB::table('TBL_TRN_SLSO01_UDF')                    
                             ->where('TBL_TRN_SLSO01_UDF.SOID_REF','=',$id)
                             ->select('TBL_TRN_SLSO01_UDF.*')
                             ->orderBy('TBL_TRN_SLSO01_UDF.SOUDFID','ASC')
                             ->get()->toArray();
            $objCount3 = count($objROUDF);

           

            $objROISP = DB::table('TBL_TRN_SLRO01_ISP')                    
                             ->where('TBL_TRN_SLRO01_ISP.ROID_REF','=',$id)
                             ->select('TBL_TRN_SLRO01_ISP.*')
                             ->orderBy('TBL_TRN_SLRO01_ISP.RO_ISID','ASC')
                             ->get()->toArray();
            $objCount4 = count($objROISP);

           
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
                             $TAXSTATE=[];
                             $objShpAddress=[] ;
                             $objBillAddress=[];

                             if(isset($objRO->SHIPTO_CLID_REF) && $objRO->SHIPTO_CLID_REF !=""){
                             $sid = $objRO->SHIPTO_CLID_REF;
                             $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_CUSTOMERLOCATION  
                                         WHERE  SHIPTO= ? AND CLID = ? ', [1,$sid]);
                 
                             $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  
                             WHERE BRID= ? ', [$BRID_REF]);
                             if($ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF)
                             {
                                 $TAXSTATE[] = 'WithinState';
                             }
                             else
                             {
                                 $TAXSTATE[] = 'OutofState';
                             }
                     
                             $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                                         WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                                         [$Status,$ObjSHIPTO[0]->CITYID_REF,$ObjSHIPTO[0]->CTRYID_REF,$ObjSHIPTO[0]->STID_REF]);
                     
                             $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                                         WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjSHIPTO[0]->STID_REF,$ObjSHIPTO[0]->CTRYID_REF]);
                     
                             $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                                         WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjSHIPTO[0]->CTRYID_REF]);
                     
                             $ObjAddressID = $ObjSHIPTO[0]->CLID;
                                     if(!empty($ObjSHIPTO)){
                                        $objShpAddress[] = $ObjSHIPTO[0]->CADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                                     }

                            }
                            

                            if(isset($objRO->BILLTO_CLID_REF) && $objRO->BILLTO_CLID_REF !=""){
                            $bid = $objRO->BILLTO_CLID_REF;
                            $ObjBILLTO =  DB::select('SELECT top 1 * FROM TBL_MST_CUSTOMERLOCATION  
                                        WHERE BILLTO= ? AND CLID = ? ', [1,$bid]);
                
                            
                            $ObjCity2 =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                                        WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                                        [$Status,$ObjBILLTO[0]->CITYID_REF,$ObjBILLTO[0]->CTRYID_REF,$ObjBILLTO[0]->STID_REF]);
                    
                            $ObjState2 =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                                        WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBILLTO[0]->STID_REF,$ObjBILLTO[0]->CTRYID_REF]);
                    
                            $ObjCountry2 =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                                        WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBILLTO[0]->CTRYID_REF]);
                    
                            $ObjAddressID = $ObjBILLTO[0]->CLID;
                                    if(!empty($ObjBILLTO)){
                                    $objBillAddress[] = $ObjBILLTO[0]->CADD .' '.$ObjCity2[0]->NAME .' '.$ObjState2[0]->NAME .' '.$ObjCountry2[0]->NAME;
                                    }

                                }

            $objsocode =[]; 
            if(isset($objRO->SOID_REF) && $objRO->SOID_REF !=""){
                $objsocode = DB::table('TBL_TRN_SLSO01_HDR')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('SOID','=',$objRO->SOID_REF)
                ->select('TBL_TRN_SLSO01_HDR.*')
                ->first();
            }
            
            $objsubglcode =[];
            if(isset($objRO->SLID_REF) && $objRO->SLID_REF !=""){
                $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('SGLID','=',$objRO->SLID_REF)
                ->select('TBL_MST_SUBLEDGER.*')
                ->first();
            }
            
            $ObjUnionUDF = DB::table("TBL_MST_UDF_FOR_RO")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                    {       
                                    $query->select('UDFROID')->from('TBL_MST_UDF_FOR_RO')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF)
                                                    ->where('BRID_REF','=',$BRID_REF);
                                                                         
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF);
                            
                    
    
            $objUdfROData = DB::table('TBL_MST_UDF_FOR_RO')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
               
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDF_FOR_RO")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                        {       
                        $query->select('UDFROID')->from('TBL_MST_UDF_FOR_RO')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF)
                                        ->where('BRID_REF','=',$BRID_REF);
                                                     
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF);
                       
            

        
                $Status = "A";
               
                
                
                $ObjSpc = DB::table('TBL_MST_SPECIFICATION_MASTER')
                //->where('VTID_REF','=',$this->vtid_ref)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->where('STATUS','=',$Status)
                ->get();                       
   

            $objUdfROData2 = DB::table('TBL_MST_UDF_FOR_RO')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
               
                ->union($ObjUnionUDF)
                ->get()->toArray();
            
            $objrocurrency=[];
            

           if(isset($objRO->CRID_REF) && $objRO->CRID_REF !=""){
                $objcurrency = DB::table('TBL_MST_CURRENCY')
                ->where('CRID','=',$objRO->CRID_REF)
                ->select('TBL_MST_CURRENCY.*')
                ->first();
                if($objcurrency)
                {
                $objrocurrency[] = $objcurrency->CRCODE;
                }
            }
    
        
            
            $FormId = $this->form_id;
        
            $InputStatus =   "";
            $lastdt=$this->LastApprovedDocDate(); 

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view('transactions.sales.ReleaseOrder.trnfrm315edit',compact(['objRO','ObjSpc','objRights','objCount1',
            'objCount2','objCount3','objCount4','objROMAT','objROISP','objROTNC','objROUDF','objUdfROData','objrocurrency',
            'objsocode','objsubglcode','FormId','objShpAddress','objBillAddress','objUdfROData2','TAXSTATE','InputStatus','lastdt',
            'DOCNO_TYPE','TabSetting'
            ]));
            }
     
       }

       public function view($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id))
        {
            $objRO = DB::table('TBL_TRN_SLRO01_HDR')
                             ->where('TBL_TRN_SLRO01_HDR.FYID_REF','=',Session::get('FYID_REF'))
                             ->where('TBL_TRN_SLRO01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_TRN_SLRO01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                             ->where('TBL_TRN_SLRO01_HDR.ROID','=',$id)
                             ->select('TBL_TRN_SLRO01_HDR.*')
                             ->first();
            $log_data = [ 
                $id
            ];

            $objROMAT = [];
            if(isset($objRO) && !empty($objRO)){
                $objROMAT = DB::select('EXEC sp_get_release_order_material ?', $log_data);
            }

            $objCount1 = count($objROMAT);
          
            $objROTNC = DB::select('EXEC sp_get_release_order_TNC ?', $log_data);
            $objCount2 = count($objROTNC);

            $objROUDF = DB::table('TBL_TRN_SLSO01_UDF')                    
                             ->where('TBL_TRN_SLSO01_UDF.SOID_REF','=',$id)
                             ->select('TBL_TRN_SLSO01_UDF.*')
                             ->orderBy('TBL_TRN_SLSO01_UDF.SOUDFID','ASC')
                             ->get()->toArray();
            $objCount3 = count($objROUDF);

           

            $objROISP = DB::table('TBL_TRN_SLRO01_ISP')                    
                             ->where('TBL_TRN_SLRO01_ISP.ROID_REF','=',$id)
                             ->select('TBL_TRN_SLRO01_ISP.*')
                             ->orderBy('TBL_TRN_SLRO01_ISP.RO_ISID','ASC')
                             ->get()->toArray();
            $objCount4 = count($objROISP);

           
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
                             $TAXSTATE=[];
                             $objShpAddress=[] ;
                             $objBillAddress=[];

                             if(isset($objRO->SHIPTO_CLID_REF) && $objRO->SHIPTO_CLID_REF !=""){
                             $sid = $objRO->SHIPTO_CLID_REF;
                             $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_CUSTOMERLOCATION  
                                         WHERE  SHIPTO= ? AND CLID = ? ', [1,$sid]);
                 
                             $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  
                             WHERE BRID= ? ', [$BRID_REF]);
                             if($ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF)
                             {
                                 $TAXSTATE[] = 'WithinState';
                             }
                             else
                             {
                                 $TAXSTATE[] = 'OutofState';
                             }
                     
                             $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                                         WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                                         [$Status,$ObjSHIPTO[0]->CITYID_REF,$ObjSHIPTO[0]->CTRYID_REF,$ObjSHIPTO[0]->STID_REF]);
                     
                             $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                                         WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjSHIPTO[0]->STID_REF,$ObjSHIPTO[0]->CTRYID_REF]);
                     
                             $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                                         WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjSHIPTO[0]->CTRYID_REF]);
                     
                             $ObjAddressID = $ObjSHIPTO[0]->CLID;
                                     if(!empty($ObjSHIPTO)){
                                        $objShpAddress[] = $ObjSHIPTO[0]->CADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                                     }

                            }
                            

                            if(isset($objRO->BILLTO_CLID_REF) && $objRO->BILLTO_CLID_REF !=""){
                            $bid = $objRO->BILLTO_CLID_REF;
                            $ObjBILLTO =  DB::select('SELECT top 1 * FROM TBL_MST_CUSTOMERLOCATION  
                                        WHERE BILLTO= ? AND CLID = ? ', [1,$bid]);
                
                            
                            $ObjCity2 =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                                        WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                                        [$Status,$ObjBILLTO[0]->CITYID_REF,$ObjBILLTO[0]->CTRYID_REF,$ObjBILLTO[0]->STID_REF]);
                    
                            $ObjState2 =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                                        WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBILLTO[0]->STID_REF,$ObjBILLTO[0]->CTRYID_REF]);
                    
                            $ObjCountry2 =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                                        WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBILLTO[0]->CTRYID_REF]);
                    
                            $ObjAddressID = $ObjBILLTO[0]->CLID;
                                    if(!empty($ObjBILLTO)){
                                    $objBillAddress[] = $ObjBILLTO[0]->CADD .' '.$ObjCity2[0]->NAME .' '.$ObjState2[0]->NAME .' '.$ObjCountry2[0]->NAME;
                                    }

                                }

            $objsocode =[]; 
            if(isset($objRO->SOID_REF) && $objRO->SOID_REF !=""){
                $objsocode = DB::table('TBL_TRN_SLSO01_HDR')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('SOID','=',$objRO->SOID_REF)
                ->select('TBL_TRN_SLSO01_HDR.*')
                ->first();
            }
            
            $objsubglcode =[];
            if(isset($objRO->SLID_REF) && $objRO->SLID_REF !=""){
                $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('SGLID','=',$objRO->SLID_REF)
                ->select('TBL_MST_SUBLEDGER.*')
                ->first();
            }
            
            $ObjUnionUDF = DB::table("TBL_MST_UDF_FOR_RO")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                    {       
                                    $query->select('UDFROID')->from('TBL_MST_UDF_FOR_RO')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF)
                                                    ->where('BRID_REF','=',$BRID_REF);
                                                                     
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF);
                                         
                    
    
            $objUdfROData = DB::table('TBL_MST_UDF_FOR_RO')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDF_FOR_RO")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                        {       
                        $query->select('UDFROID')->from('TBL_MST_UDF_FOR_RO')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF)
                                        ->where('BRID_REF','=',$BRID_REF);
                                                       
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF);
                         
            

        
                $Status = "A";
               
                
                
                $ObjSpc = DB::table('TBL_MST_SPECIFICATION_MASTER')
                //->where('VTID_REF','=',$this->vtid_ref)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->where('STATUS','=',$Status)
                ->get();                       
   

            $objUdfROData2 = DB::table('TBL_MST_UDF_FOR_RO')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray();
            
            $objrocurrency=[];
            

           if(isset($objRO->CRID_REF) && $objRO->CRID_REF !=""){
                $objcurrency = DB::table('TBL_MST_CURRENCY')
                ->where('CRID','=',$objRO->CRID_REF)
                ->select('TBL_MST_CURRENCY.*')
                ->first();
                if($objcurrency)
                {
                $objrocurrency[] = $objcurrency->CRCODE;
                }
            }
    
         
            
            $FormId = $this->form_id;
        
            $InputStatus =   "disabled";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view('transactions.sales.ReleaseOrder.trnfrm315view',compact(['objRO','ObjSpc','objRights','objCount1',
            'objCount2','objCount3','objCount4','objROMAT','objROISP','objROTNC','objROUDF','objUdfROData','objrocurrency',
            'objsocode','objsubglcode','FormId','objShpAddress','objBillAddress','objUdfROData2','TAXSTATE','InputStatus','TabSetting']));
            }
     
       }
     
       

  
   public function update(Request $request){

        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i]))
            {
                $req_data[$i] = [
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'ITEMSPECI' => '',
                    'MAIN_UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'SOQTYM' => $request['SOQTYM_'.$i],
                    'ALT_UOMID_REF' => $request['ALT_UOMID_REF_'.$i],
                    'SOID_REF'=> (!empty($request['SOID_REF_'.$i])) == 'true' ? $request['SOID_REF_'.$i] : "0" ,
                    'SQID_REF'=> (!empty($request['SQID_REF_'.$i])) == 'true' ? $request['SQID_REF_'.$i] : "0" ,
                    'SEID_REF'=> (!empty($request['SEID_REF_'.$i])) == 'true' ? $request['SEID_REF_'.$i] : "0" ,
                ];
            }
        }
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);


    //////////specification
    $req_data1=[];
    for ($i=0; $i<=$r_count1; $i++){        
        if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['IDSPF_'.$i])){

            $spefction_data = $request['IDSPF_'.$i];
            $json_decode_data = json_decode($spefction_data);

            if(!empty($json_decode_data)){

                foreach ($json_decode_data as $key=>$decode_data){      
                $req_data1[] = [
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'MAIN_UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'SOQTYM' => $request['SOQTYM_'.$i], 
                    'SOID_REF'=> (!empty($request['SOID_REF_'.$i])) == 'true' ? $request['SOID_REF_'.$i] : "0" ,
                    'SQID_REF'=> (!empty($request['SQID_REF_'.$i])) == 'true' ? $request['SQID_REF_'.$i] : "0" ,
                    'SEID_REF'=> (!empty($request['SEID_REF_'.$i])) == 'true' ? $request['SEID_REF_'.$i] : "0" ,
                    'Specification_ID'    => $decode_data->SPFID,
                    'Specification_Name'    => $decode_data->SPFNAME,
                    'ITEM_SPECI'    => $decode_data->SPFDES,                   
                
                ];             
            } 
        }                

        }           
    }  
    
    
       
        if(isset($req_data1) && !empty($req_data1))
            { 
                $wrapped_links1["ISP"] = $req_data1; 
                $XMLISP = ArrayToXml::convert($wrapped_links1);
            }
            else
            {
                $XMLISP = NULL; 
            }



        
        for ($i=0; $i<=$r_count2; $i++)
        {
            if(isset($request['TNCID_REF']) && !is_null($request['TNCID_REF']))
            {
                if(isset($request['TNCDID_REF_'.$i]))
                {
                    $reqdata2[$i] = [
                        'TNCID_REF'     => $request['TNCID_REF'] ,
                        'TNCDID_REF'    => $request['TNCDID_REF_'.$i],
                        'VALUE'         => $request['tncdetvalue_'.$i],
                    ];
                }
            }
            
        }
            if(isset($reqdata2))
            { 
            $wrapped_links2["TNC"] = $reqdata2;
            $XMLTNC = ArrayToXml::convert($wrapped_links2);
            }
            else
            {
            $XMLTNC = NULL; 
            }  
        
        for ($i=0; $i<=$r_count3; $i++)
        {
                if(isset($request['UDFROID_REF_'.$i]) && !is_null($request['UDFROID_REF_'.$i]))
                {
                    $reqdata3[$i] = [
                        'UDFROID_REF'   => $request['UDFROID_REF_'.$i],
                        'VALUE'         => $request['udfvalue_'.$i],
                    ];
                }
            
        }
            if(isset($reqdata3))
            { 
                $wrapped_links3["UDF"] = $reqdata3; 
                $XMLUDF = ArrayToXml::convert($wrapped_links3);
            }
            else
            {
                $XMLUDF = NULL; 
            }
        
            // for ($i=0; $i<=$r_count4; $i++)
            // {
            //     if(isset($request['IITEMID_REF_'.$i]) && !is_null($request['IITEMID_REF_'.$i]))
            //     {
            //         $reqdata4[] = [
            //             'ITEMID_REF'    => $request['IITEMID_REF_'.$i] ,
            //             'Specification_ID'    => $request['Specification_ID_'.$i] ,
            //             'Specification_Name'    => $request['Specification_Name_'.$i] ,
            //             'ITEM_SPECI'    => $request['Itemspec_'.$i] ,
            //             'SOID_REF'      => (!empty($request['ISOID_REF_'.$i])) == 'true' ? $request['ISOID_REF_'.$i] : "0" ,
            //             'SQID_REF'      => (!empty($request['ISQID_REF_'.$i])) == 'true' ? $request['ISQID_REF_'.$i] : "0" ,
            //             'SEID_REF'      => (!empty($request['ISEID_REF_'.$i])) == 'true' ? $request['ISEID_REF_'.$i] : "0" ,                        
                    
            //             'Specif_ID'    => $request['Specif_ID_'.$i] ,
            //             'Specif_Name'    => $request['Specif_Name_'.$i] ,
            //             'ItemSpecif'    => $request['ItemSpecif_'.$i] ,
            //         ];
                    
            //     }
                
            // }
            
            // //dd($reqdata4);
            
            // if(isset($reqdata4))
            // { 
            //     $wrapped_links4["ISP"] = $reqdata4; 
            //     $XMLISP = ArrayToXml::convert($wrapped_links4);
            // }
            // else
            // {
            //     $XMLISP = NULL; 
            // }

    // ////////////////////////////////

    //     for ($i=0; $i<=$r_count3; $i++)
    //         {
    //             if(isset($request['IITEMID_REF_'.$i]) && !is_null($request['IITEMID_REF_'.$i]))
    //             {
    //                 $reqdata3[] = [
    //                     'ITEMID_REF'    => $request['IITEMID_REF_'.$i] ,
    //                     'Specification_ID'    => $request['Specif_ID_'.$i] ,
    //                     'Specification_Name'    => $request['Specif_Name_'.$i] ,
    //                     'ITEM_SPECI'    => $request['ItemSpecif_'.$i] ,
    //                     'SOID_REF'      => (!empty($request['ISOID_REF_'.$i])) == 'true' ? $request['ISOID_REF_'.$i] : "0" ,
    //                     'SQID_REF'      => (!empty($request['ISQID_REF_'.$i])) == 'true' ? $request['ISQID_REF_'.$i] : "0" ,
    //                     'SEID_REF'      => (!empty($request['ISEID_REF_'.$i])) == 'true' ? $request['ISEID_REF_'.$i] : "0" ,                        

    //                 ];
                    
    //             }
                
    //         }
            
    //         //dd($reqdata3);
            
    //         if(isset($reqdata3))
    //         { 
    //             $wrapped_links4["ISP"] = $reqdata3; 
    //             $XMLISP = ArrayToXml::convert($wrapped_links4);
    //         }
    //         else
    //         {
    //             $XMLISP = NULL; 
    //         }


    //         //////////////////////////



        

            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'EDIT';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $ROID = $request['ROID'];
            $RONO = $request['RONO'];
            $RODT = $request['RODT'];
            $SLID_REF = $request['SLID_REF'];
            $SOID_REF = $request['SOID_REF'];
            $FC = (isset($request['hdn_FC']) && $request['hdn_FC']=="0" ? 0 : 1);
            $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : NULL;
            $CONVERSION_FACT = (isset($request['CONVERSION_FACT'])) ? $request['CONVERSION_FACT'] : 0;
            $OVFRM_DT = $request['OVFRM_DT'];
            $OVTOM_DT = $request['OVTOM_DT'];
            $CUSTOMERPONO = $request['CUSTOMERPONO'];
            $CUSTOMERDT = $request['CUSTOMERDT'];
            $BILLTO = $request['BILLTO'];
            $SHIPTO = $request['SHIPTO'];
            $REMARKS = $request['REMARKS'];
            $DELIVER_DATE = $request['DELIVER_DATE'];

            

            $log_data = [ 
                $ROID,$RONO,$RODT,$SLID_REF,$SOID_REF,$OVFRM_DT,$OVTOM_DT,$FC,$CRID_REF,$CONVERSION_FACT,
                $BILLTO,$SHIPTO,$REMARKS,$CYID_REF, $BRID_REF, $FYID_REF,$VTID_REF,
                $XMLMAT, $XMLISP,$XMLTNC,$XMLUDF,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, 
                $IPADDRESS,$CUSTOMERDT,$CUSTOMERPONO,$DELIVER_DATE
            ];
            
           
            
            $sp_result = DB::select('EXEC SP_RO_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
            
          
                                                    
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => 'Sucessfully Updated']);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            exit();   
    }

  
   public function Approve(Request $request){

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
           
            $r_count1 = $request['Row_Count1'];
            $r_count2 = $request['Row_Count2'];
            $r_count3 = $request['Row_Count3'];
            $r_count4 = $request['Row_Count4'];
            
            for ($i=0; $i<=$r_count1; $i++){
                if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i])){
                    $req_data[$i] = [
                        'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                        'ITEMSPECI' => '',
                        'MAIN_UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                        'SOQTYM' => $request['SOQTYM_'.$i],
                        'ALT_UOMID_REF' => $request['ALT_UOMID_REF_'.$i],
                        'SOID_REF'=> (!empty($request['SOID_REF_'.$i])) == 'true' ? $request['SOID_REF_'.$i] : "0" ,
                        'SQID_REF'=> (!empty($request['SQID_REF_'.$i])) == 'true' ? $request['SQID_REF_'.$i] : "0" ,
                        'SEID_REF'=> (!empty($request['SEID_REF_'.$i])) == 'true' ? $request['SEID_REF_'.$i] : "0" ,
                    ];
                }
            }

            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);


            //////////specification
            $req_data1=[];
            for ($i=0; $i<=$r_count1; $i++){        
                if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['IDSPF_'.$i])){

                    $spefction_data = $request['IDSPF_'.$i];
                    $json_decode_data = json_decode($spefction_data);

                    if(!empty($json_decode_data)){

                        foreach ($json_decode_data as $key=>$decode_data){      
                        $req_data1[] = [
                            'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                            'MAIN_UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                            'SOQTYM' => $request['SOQTYM_'.$i], 
                            'SOID_REF'=> (!empty($request['SOID_REF_'.$i])) == 'true' ? $request['SOID_REF_'.$i] : "0" ,
                            'SQID_REF'=> (!empty($request['SQID_REF_'.$i])) == 'true' ? $request['SQID_REF_'.$i] : "0" ,
                            'SEID_REF'=> (!empty($request['SEID_REF_'.$i])) == 'true' ? $request['SEID_REF_'.$i] : "0" ,
                            'Specification_ID'    => $decode_data->SPFID,
                            'Specification_Name'    => $decode_data->SPFNAME,
                            'ITEM_SPECI'    => $decode_data->SPFDES,                   
                        
                        ];             
                    } 
                }                

                }           
            }  
    
    
            if(isset($req_data1) && !empty($req_data1)){ 
                $wrapped_links1["ISP"] = $req_data1; 
                $XMLISP = ArrayToXml::convert($wrapped_links1);
            }
            else{
                $XMLISP = NULL; 
            }

            
            for ($i=0; $i<=$r_count2; $i++){
                if(isset($request['TNCID_REF']) && !is_null($request['TNCID_REF'])){
                    if(isset($request['TNCDID_REF_'.$i]))
                    {
                        $reqdata2[$i] = [
                            'TNCID_REF'     => $request['TNCID_REF'] ,
                            'TNCDID_REF'    => $request['TNCDID_REF_'.$i],
                            'VALUE'         => $request['tncdetvalue_'.$i],
                        ];
                    }
                }
            
            }

            if(isset($reqdata2))
            { 
            $wrapped_links2["TNC"] = $reqdata2;
            $XMLTNC = ArrayToXml::convert($wrapped_links2);
            }
            else
            {
            $XMLTNC = NULL; 
            }  
        
        for ($i=0; $i<=$r_count3; $i++)
        {
                if(isset($request['UDFROID_REF_'.$i]) && !is_null($request['UDFROID_REF_'.$i]))
                {
                    $reqdata3[$i] = [
                        'UDFROID_REF'   => $request['UDFROID_REF_'.$i],
                        'VALUE'         => $request['udfvalue_'.$i],
                    ];
                }
            
        }
            if(isset($reqdata3))
            { 
                $wrapped_links3["UDF"] = $reqdata3; 
                $XMLUDF = ArrayToXml::convert($wrapped_links3);
            }
            else
            {
                $XMLUDF = NULL; 
            }
            
            /*
            for ($i=0; $i<=$r_count4; $i++)
            {
                if(isset($request['IITEMID_REF_'.$i]) && !is_null($request['IITEMID_REF_'.$i]))
                {
                    $reqdata4[$i] = [
                        'ITEMID_REF'    => $request['IITEMID_REF_'.$i] ,
                        'ITEM_SPECI'    => $request['Itemspec_'.$i] ,
                        'SOID_REF'      => (!empty($request['ISOID_REF_'.$i])) == 'true' ? $request['ISOID_REF_'.$i] : "0" ,
                        'SQID_REF'      => (!empty($request['ISQID_REF_'.$i])) == 'true' ? $request['ISQID_REF_'.$i] : "0" ,
                        'SEID_REF'      => (!empty($request['ISEID_REF_'.$i])) == 'true' ? $request['ISEID_REF_'.$i] : "0" ,                        
                    ];
                }
                
            }
                if(isset($reqdata4))
                { 
                    $wrapped_links4["ISP"] = $reqdata4; 
                    $XMLISP = ArrayToXml::convert($wrapped_links4);
                }
                else
                {
                    $XMLISP = NULL; 
                }
                */

                $VTID_REF     =   $this->vtid_ref;
                $VID = 0;
                $USERID = Auth::user()->USERID;   
                $ACTIONNAME = $Approvallevel;
                $IPADDRESS = $request->getClientIp();
                $CYID_REF = Auth::user()->CYID_REF;
                $BRID_REF = Session::get('BRID_REF');
                $FYID_REF = Session::get('FYID_REF');
                $ROID = $request['ROID'];
                $RONO = $request['RONO'];
                $RODT = $request['RODT'];
                $SLID_REF = $request['SLID_REF'];
                $SOID_REF = $request['SOID_REF'];
                $FC = (isset($request['hdn_FC']) && $request['hdn_FC']=="0" ? 0 : 1);
                $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : NULL;
                $CONVERSION_FACT = (isset($request['CONVERSION_FACT'])) ? $request['CONVERSION_FACT'] : 0;
                $OVFRM_DT = $request['OVFRM_DT'];
                $OVTOM_DT = $request['OVTOM_DT'];
                $CUSTOMERPONO = $request['CUSTOMERPONO'];
                $CUSTOMERDT = $request['CUSTOMERDT'];
                $BILLTO = $request['BILLTO'];
                $SHIPTO = $request['SHIPTO'];
                $REMARKS = $request['REMARKS'];
                $DELIVER_DATE = $request['DELIVER_DATE'];
 
                
                $log_data = [ 
                    $ROID,$RONO,$RODT,$SLID_REF,$SOID_REF,$OVFRM_DT,$OVTOM_DT,$FC,$CRID_REF,$CONVERSION_FACT,
                    $BILLTO,$SHIPTO,$REMARKS,$CYID_REF, $BRID_REF, $FYID_REF,$VTID_REF,
                    $XMLMAT, $XMLISP,$XMLTNC,$XMLUDF,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, 
                    $IPADDRESS,$CUSTOMERDT,$CUSTOMERPONO,$DELIVER_DATE
                ];
                
                //dd($log_data);
                
                $sp_result = DB::select('EXEC SP_RO_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data); 
                
            
                $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
        
                if($contains){
                    return Response::json(['success' =>true,'msg' => 'Sucessfully Approved']);

                }else{
                    return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
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
                $TABLE      =   "TBL_TRN_SLRO01_HDR";
                $FIELD      =   "ROID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
         
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_RO ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
    
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
        $TABLE      =   "TBL_TRN_SLRO01_HDR";
        $FIELD      =   "ROID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
        
        $req_data[0]=[
            'NT'  => 'TBL_TRN_SLRO01_MAT',
           ];
           $req_data[1]=[
           'NT'  => 'TBL_TRN_SLRO01_TNC',
           ];
           $req_data[2]=[
           'NT'  => 'TBL_TRN_SLRO01_UDF',
           ];
           $req_data[3]=[
            'NT'  => 'TBL_TRN_SLRO01_ISP',
            ];
            $req_data[4]=[
            'NT'  => 'TBL_TRN_SLRO01_HDR',
            ];
    
           
           $wrapped_links["TABLES"] = $req_data; 
           $XMLTAB = ArrayToXml::convert($wrapped_links);
           
           $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];
   
           $sp_result = DB::select('EXEC SP_TRN_CANCEL_RO  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);
   

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
    
    
    $image_path         =   "docs/company".$CYID_REF."/ReleaseOrder";     
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
        return redirect()->route("transaction",[315,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
    
       return redirect()->route("transaction",[315,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("transaction",[315,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("transaction",[315,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("transaction",[315,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

    public function checkso(Request $request){

    
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $RONO = $request->RONO;
        
        $objSO = DB::table('TBL_TRN_SLRO01_HDR')
        ->where('TBL_TRN_SLRO01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLRO01_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_SLRO01_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_SLRO01_HDR.RONO','=',$RONO)
        ->select('TBL_TRN_SLRO01_HDR.ROID')
        ->first();
        
        if($objSO){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate RONO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }

    
       
    public function getItesmSpection(Request $request){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $HID_SPC = $request->IDSPF;
        $Specification_ID = $request->Specification_ID;

        $IDSPF_VAL = json_decode($request->IDSPF_VAL);

        $SpcCheckId=[];
        $SpcCheckDs=[];
        if(isset($IDSPF_VAL) && !empty($IDSPF_VAL)){

            foreach($IDSPF_VAL as $val){
                $SpcCheckId[]=$val->SPFID;
                $SpcCheckDs[$val->SPFID]=$val->SPFDES;
            }
        }

        $ObjItem = DB::table('TBL_MST_SPECIFICATION_MASTER')
                //->where('VTID_REF','=',$this->vtid_ref)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->where('STATUS','=',$Status)
                ->get();
                

        if(!empty($ObjItem)){
            foreach ($ObjItem as $index=>$dataRow){

                $SPECIFICATIONID  =  (isset($dataRow->SPECIFICATIONID)?$dataRow->SPECIFICATIONID:NULL);
                $SPECIFICATIONNAME  =  (isset($dataRow->SPECIFICATIONNAME)?$dataRow->SPECIFICATIONNAME:NULL); 
                
                if(!empty($SpcCheckId) && in_array($SPECIFICATIONID,$SpcCheckId)){
                    $SPECIFICATIONDESC =    $SpcCheckDs[$SPECIFICATIONID];
                }
                else{
                    $SPECIFICATIONDESC = (isset($dataRow->SPECIFICATIONDESC)?$dataRow->SPECIFICATIONDESC:NULL);
                }

                $checked= !empty($SpcCheckId) && in_array($SPECIFICATIONID,$SpcCheckId)?'checked':'';
                
                $row = '';
                $row.='<tr class="participantRow5">
                        <td class="ROW1" ><input type="checkbox" name="spcfchecked" id="spcfchecked" value="'.$SPECIFICATIONID.'" '.$checked.' ></td>          
                        <td hidden><input type="hidden" name="SPFICTIONID_'.$index.'" id="SPFICTIONID_'.$index.'" class="form-control" value="'.$SPECIFICATIONID.'" autocomplete="off"  readonly/></td>
                        <td class="ROW2" ><input type="text" name="SPFICTIONNAME_'.$index.'" id="SPFICTIONNAME_'.$index.'" class="form-control" value="'.$SPECIFICATIONNAME.'" autocomplete="off" readonly /></td>
                        <td class="ROW3" ><input type="text" name="SPFICTIONDES_'.$index.'" id="SPFICTIONDES_'.$index.'" class="form-control" value="'.$SPECIFICATIONDESC.'" autocomplete="off"/></td>
                        <td hidden><input type="hidden" name="HID_SPC_'.$index.'" id="HID_SPC_'.$index.'" class="form-control" value="'.$HID_SPC.'" autocomplete="off"/>
                        <td hidden><input type="text" name="Specification_ID_'.$index.'" id="Specification_ID_'.$index.'" class="form-control" value="'.$HID_SPC.'" autocomplete="off"/></td>
                    </tr>'; 

                echo $row;                          
            } 
            
        }           
        else{
            echo '<tr><td> Record not found.</td></tr>';
        }
    exit();
    }


        



    public function LastApprovedDocDate(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $VTID_REF     =   $this->vtid_ref;
        return $objlastDocDate = DB::select('SELECT MAX(RODT) RODT FROM TBL_TRN_SLRO01_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $VTID_REF, $Status ]);

    }

    public function codeduplicate(Request $request){

        $ROID       =   trim($request['ROID']);
        $RONO       =   trim($request['RONO']);

        $objLabel   =   DB::table('TBL_TRN_SLRO01_HDR')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('BRID_REF','=',Session::get('BRID_REF'))
                        ->where('RONO','=',$RONO)
                        ->where('ROID','!=',$ROID)
                        ->select('RONO')
                        ->first();
        
        if($objLabel){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();  
    }

}
