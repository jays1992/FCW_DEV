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
use App\Helpers\Helper;
use App\Helpers\Utils;

class TrnFrm355Controller extends Controller{

    protected $form_id  = 355;
    protected $vtid_ref = 441;
    protected $view     = "transactions.JobWork.JobWorkChallan.trnfrm355";
   
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];
    
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){  
        
        $objRights  =   DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
        
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();
  
        $FormId         =   $this->form_id;
       
        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');     

        $REQUEST_DATA   =   array(
            'FORMID'    =>  $this->form_id,
            'VTID_REF'  =>  $this->vtid_ref,
            'USERID'    =>  Auth::user()->USERID,
            'CYID_REF'  =>  Auth::user()->CYID_REF,
            'BRID_REF'  =>  Session::get('BRID_REF'),
            'FYID_REF'  =>  Session::get('FYID_REF'),
        );
        
        $DATA_STATUS    =	Helper::get_user_level($REQUEST_DATA);
        $USER_LEVEL     =   $DATA_STATUS['USER_LEVEL'];
        
        $objDataList    =	DB::select("SELECT '$USER_LEVEL' AS USER_LEVEL,T1.*,T2.ACTIONNAME,T3.SLNAME,
        (
        SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
        LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
        WHERE  AUD.VID=T1.JWCID AND  AUD.CYID_REF=T1.CYID_REF AND  AUD.BRID_REF=T1.BRID_REF AND  
        AUD.FYID_REF=T1.FYID_REF AND  AUD.VTID_REF=T1.VTID_REF AND AUD.ACTIONNAME='ADD'       
        ) AS CREATED_BY
        FROM TBL_TRN_JWC_HDR T1
        INNER JOIN TBL_TRN_AUDITTRAIL T2 ON T1.JWCID=T2.VID AND T1.VTID_REF=T2.VTID_REF AND T1.CYID_REF=T2.CYID_REF AND T1.BRID_REF=T2.BRID_REF 
        INNER JOIN TBL_MST_SUBLEDGER T3 ON T1.VID_REF = T3.SGLID  
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' AND T2.VTID_REF = '$this->vtid_ref' AND T2.ACTID IN (SELECT max(ACTID) FROM TBL_TRN_AUDITTRAIL A WHERE T2.VTID_REF = A.VTID_REF AND T2.VID = A.VID)
        ORDER BY T1.JWCID DESC 
        ");

        return view($this->view,compact(['REQUEST_DATA','FormId','objRights','objDataList','FormId','DATA_STATUS']));
    }
	
	public function ViewReport($request) {

        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
           
        $JWCID       =   $myValue['JWCID'];
        $Flag       =   $myValue['Flag'];

        // $objSalesOrder = DB::table('TBL_TRN_PROR01_HDR')
        // ->where('TBL_TRN_PROR01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        // ->where('TBL_TRN_PROR01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        // ->where('TBL_TRN_PROR01_HDR.POID','=',$POID)
        // ->select('TBL_TRN_PROR01_HDR.*')
        // ->first();
        
        
        $ssrs = new \SSRS\Report('http://103.139.58.23:8181//ReportServer/', array('username' => 'App', 'password' => 'admin@123'));
        $result = $ssrs->loadReport('/ERP/JWChallanPrint');
		//$result = $ssrs->loadReport('/ERP/POPrint -ZEP');
        
        $reportParameters = array(
            'JWCID' => $JWCID,
        );
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
        ->setExecutionParameters($parameters);
        if($Flag == 'H')
        {
            $output = $ssrs->render('HTML4.0'); 
            echo $output;
        }
        else if($Flag == 'P')
        {
            $output = $ssrs->render('PDF'); 
            return $output->download('Report.pdf');
        }
        else if($Flag == 'E')
        {
            $output = $ssrs->render('EXCEL'); 
            return $output->download('Report.xls');
        }
		else if($Flag == 'R')
        {
            $output = $ssrs->render('HTML4.0'); // PDF | XML | CSV | HTML4.0
            echo $output;

        }
         
     }

    public function add(){       
        
        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $objlastdt  =   $this->getLastdt();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_JWC_HDR',
            'HDR_ID'=>'JWCID',
            'HDR_DOC_NO'=>'JWCNO',
            'HDR_DOC_DT'=>'JWCDT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        
        
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_JWC")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDFJWCID')->from('TBL_MST_UDFFOR_JWC')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                     
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                
                   

        $objUdf  = DB::table('TBL_MST_UDFFOR_JWC')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdf); 
                    
        $FormId  = $this->form_id;
        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        return view($this->view.'add', compact(
            ['AlpsStatus','FormId','objUdf','objCountUDF','objlastdt','TabSetting','doc_req','docarray']
        ));       
    }

    public function getLastdt(){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        return  DB::select('SELECT MAX(JWCDT) JWCDT FROM TBL_TRN_JWC_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
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

    public function getBillTo(Request $request){

        $Status     =   "A";
        $SLID_REF   =   $request['id'];
        $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
        $id         =   $ObVID->VID;

        $ObjBillTo =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                    WHERE DEFAULT_BILLING= ? AND VID_REF = ? ', [1,$id]);

        $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                    WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                    [$Status,$ObjBillTo[0]->CITYID_REF,$ObjBillTo[0]->CTRYID_REF,$ObjBillTo[0]->STID_REF]);

        $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                    WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBillTo[0]->STID_REF,$ObjBillTo[0]->CTRYID_REF]);

        $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                    WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBillTo[0]->CTRYID_REF]);

        $ObjAddressID = $ObjBillTo[0]->LID;
                if(!empty($ObjBillTo)){
                    
                $objAddress = $ObjBillTo[0]->LADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                
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
        $Status     =   "A";
        $SLID_REF   =   $request['id'];
        $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
        $id         =   $ObVID->VID;
        $BRID_REF   =   Session::get('BRID_REF');
        

        
        $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                    WHERE DEFAULT_SHIPPING= ? AND VID_REF = ? ', [1,$id]);

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

        $ObjAddressID = $ObjSHIPTO[0]->LID;
                if(!empty($ObjSHIPTO)){
                    
                $objAddress = $ObjSHIPTO[0]->LADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                
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

    public function getBillAddress(Request $request){
        $Status     =   "A";
        $SLID_REF   =   $request['id'];
        $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
        $id         =   $ObVID->VID;

        if(!is_null($id))
        {
        
        $ObjBillTo =  DB::select('SELECT  * FROM TBL_MST_VENDORLOCATION  
                    WHERE BILLTO= ? AND VID_REF = ? ', [1,$id]);
    
            if(!empty($ObjBillTo)){
    
            foreach ($ObjBillTo as $index=>$dataRow){

                $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                [$Status,$dataRow->CITYID_REF,$dataRow->CTRYID_REF,$dataRow->STID_REF]);

                $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                            WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$dataRow->STID_REF,$dataRow->CTRYID_REF]);

                $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                            WHERE STATUS= ? AND CTRYID = ? ', [$Status,$dataRow->CTRYID_REF]);
                $objAddress = $dataRow->LADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;

                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_BILLTO[]" id="billto_'.$dataRow->LID .'"  class="clsbillto" value="'.$dataRow->LID.'" ></td>
                <td class="ROW2">'.$dataRow->NAME;
                $row = $row.'<input type="hidden" id="txtbillto_'.$dataRow->LID.'" data-desc="'.$objAddress.'" 
                value="'.$dataRow->LID.'"/></td>
                <td class="ROW3">'.$objAddress.'</td>
                </tr>';
                echo $row;

            }
    
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
        }
    }
    
    public function getShipAddress(Request $request){

        $Status     =   "A";
        $SLID_REF   =   $request['id'];
        $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
        $id         =   $ObVID->VID;
        $BRID_REF   =   Session::get('BRID_REF');

        if(!is_null($id))
        {
        
        $ObjShipTo =  DB::select('SELECT  * FROM TBL_MST_VENDORLOCATION  
                    WHERE SHIPTO= ? AND VID_REF = ? ', [1,$id]);
    
            if(!empty($ObjShipTo)){
    
            foreach ($ObjShipTo as $index=>$dataRow){

                $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  
                    WHERE BRID= ? ', [$BRID_REF]);

                    if($dataRow->STID_REF == $ObjBranch[0]->STID_REF)
                    {
                        $TAXSTATE = 'WithinState';
                    }
                    else
                    {
                        $TAXSTATE = 'OutofState';
                    }

                $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                [$Status,$dataRow->CITYID_REF,$dataRow->CTRYID_REF,$dataRow->STID_REF]);

                $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                            WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$dataRow->STID_REF,$dataRow->CTRYID_REF]);

                $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                            WHERE STATUS= ? AND CTRYID = ? ', [$Status,$dataRow->CTRYID_REF]);
                $objAddress = $dataRow->LADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;

                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_SHIPTO[]" id="shipto_'.$dataRow->LID .'"  class="clsshipto" value="'.$dataRow->LID.'" ></td>
                <td class="ROW2">'.$dataRow->NAME;
                $row = $row.'<input type="hidden" id="txtshipto_'.$dataRow->LID.'" data-desc="'.$TAXSTATE.'" 
                value="'.$dataRow->LID.'"/></td>
                <td class="ROW3" id="txtshipadd_'.$dataRow->LID.'" >'.$objAddress.'</td></tr>';
                echo $row;
            }
    
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
        }
    }

    public function save(Request $request) {
    
        $r_count1 = $request['Row_Count1'];
        $r_count3 = $request['Row_Count3'];
        $r_count5 = $request['Row_Count5'];
        
        $req_data=array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $req_data[$i] = [
                    'JWOID_REF'         => $request['JWOID_REF_'.$i],
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'         => $request['MAIN_UOMID_REF_'.$i],
                    'JW_PRODUCE_QTY'    => $request['PD_OR_QTY_'.$i],
                    'EDA'               => $request['EDA_'.$i],
                    'PROID_REF'         => $request['PROID_REF_'.$i],
                    'SOID_REF'          => $request['SOID_REF_'.$i],
                    'SQID_REF'          => $request['SQID_REF_'.$i],
                    'SEID_REF'          => $request['SEID_REF_'.$i]   
                ];

            }
        }

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);

        $req_data5=array();
        for ($i=0; $i<=$r_count5; $i++){
            if(isset($request['REQ_ITEMID_REF_'.$i])){

                $StoreArr   =   array();
                $ITEMID_REF =   $request['REQ_ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $exp        =   explode(",",$ITEMROWID);

                foreach($exp as $val){
                    $keyid              =   explode("_",$val);
                    $batchid            =   $keyid[0];

                    $objStore =  DB::table('TBL_MST_BATCH')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('BATCHID','=',$batchid)
                        ->where('STATUS','=',"A")
                        ->select('STID_REF')
                        ->first();

                    $StoreArr[]=$objStore->STID_REF;
                }

                if(!empty($StoreArr)){
                    $StoreId    =   array_unique($StoreArr);
                    $STID_REF   =   implode(",",$StoreId);
                }
                else{
                    $STID_REF   =   NULL;
                }

                $req_data5[$i] = [
                    'ITEMID_REF'    	=> $request['REQ_ITEMID_REF_'.$i],
                    'UOMID_REF'    	    => $request['REQ_UOMID_REF_'.$i],
                    'STID_REF'    	    => $STID_REF,
                    'JWC_QTY'	        => $request['REQ_JWC_QTY_'.$i],
                    'MAIN_ITEMID_REF'   => $request['REQ_SOITEMID_REF_'.$i],
                    'JWOID_REF'      	=> $request['REQ_JWOID_REF_'.$i],
                    'PROID_REF'      	=> $request['REQ_PROID_REF_'.$i],
                    'SOID_REF'      	=> $request['REQ_SOID_REF_'.$i],
                    'SQID_REF'     		=> $request['REQ_SQID_REF_'.$i],  
                    'SEID_REF'     		=> $request['REQ_SEID_REF_'.$i],
                    'BATCH_QTY_REF'     => $request['HiddenRowId_'.$i],
                    'JWC_RATE'     		=> $request['JWC_RATE_'.$i],
                    'IGST'     		=> $request['IGST_'.$i],
                    'CGST'     		=> $request['CGST_'.$i],
                    'SGST'     		=> $request['SGST_'.$i],
                ];

            }
        }


		if($r_count5 > 0){
            $wrapped_links5["DIS"] = $req_data5; 
			$XMLREQ = ArrayToXml::convert($wrapped_links5);
        }
        else{
            $XMLREQ=NULL;
        }
        
        
        $req_data55=array();
        for ($i=0; $i<=$r_count5; $i++){
            if(isset($request['REQ_ITEMID_REF_'.$i])){

                $dataArr    =   array();
                $ITEMID_REF =   $request['REQ_ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];

                if($ITEMROWID !=""){
                    $exp        =   explode(",",$ITEMROWID);

                    foreach($exp as $val){
                        $keyid              =   explode("_",$val);
                        $batchid            =   $keyid[0];
                        $qty                =   $keyid[1];
                        $dataArr[$batchid]  =   $qty;
                    }
                }

                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){
                 
                        $objBatch =  DB::table('TBL_MST_BATCH')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('BATCHID','=',$key)
                        ->where('STATUS','=',"A")
                        ->select('BATCHID','BATCH_CODE','ITEMID_REF','STID_REF','SERIALNO','UOMID_REF','CURRENT_QTY')
                        ->first();

                        $req_data55[$i][] = [
                            'ITEMID_REF'    => $ITEMID_REF,
                            'UOMID_REF'     => $objBatch->UOMID_REF,
                            'STID_REF'      => $objBatch->STID_REF,
                            'BATCHID_REF'   => $objBatch->BATCHID,
                            'DIS_QTY'       => $val,
                            'MAIN_ITEMID_REF'   => $request['REQ_SOITEMID_REF_'.$i],
                            'JWOID_REF'      	=> $request['REQ_JWOID_REF_'.$i],
                            'PROID_REF'      	=> $request['REQ_PROID_REF_'.$i],
                            'SOID_REF'      	=> $request['REQ_SOID_REF_'.$i],
                            'SQID_REF'     		=> $request['REQ_SQID_REF_'.$i],  
                            'SEID_REF'     		=> $request['REQ_SEID_REF_'.$i],        
                        ];

                    }
                }

            }
        }
        
		if($r_count5 > 0){
            $wrapped_links55["STORE"] = $req_data55; 
			$XMLSTORE = ArrayToXml::convert($wrapped_links55);
        }
        else{
            $XMLSTORE=NULL;
        }


        $udffield_Data = [];      
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFJWCID_REF'] = $request['udffie_'.$i]; 
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
        
        $USERID_REF = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $JWCNO = $request['JWCNO'];
        $JWCDT = $request['JWCDT'];
        $VID_REF = $request['VID_REF'];
        $BILLTO = $request['BILLTO'];
        $SHIPTO = $request['SHIPTO'];
        $REMARKS = $request['REMARKS'];

        
        
        $log_data = [ 
            $JWCNO,             $JWCDT,         $VID_REF,       $BILLTO,       $SHIPTO,       
            $CYID_REF,          $BRID_REF,      $FYID_REF,      $VTID_REF,     $XMLMAT,        
            $XMLREQ,            $XMLSTORE,      $XMLUDF,        $USERID_REF,    Date('Y-m-d'),  
            Date('h:i:s.u'),    $ACTIONNAME,    $IPADDRESS,     $REMARKS
        ];

        
        $sp_result = DB::select('EXEC SP_JWC_IN ?,?,?,?,? ,?,?,?,?,? ,?,?,?,?,? ,?,?,?,?', $log_data);     
        
    
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');

        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        
        exit();   
     }


     public function edit($id){       
        
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

            $objMstResponse = DB::table('TBL_TRN_JWC_HDR')
                ->where('FYID_REF','=',Session::get('FYID_REF'))
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('JWCID','=',$id)
                ->select('*')
                ->first();


            $objMAT = DB::select("SELECT T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,
            T3.UOMCODE,T3.DESCRIPTIONS,
            T5.JWONO
            FROM TBL_TRN_JWC_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            LEFT JOIN TBL_TRN_JWO_HDR T5 ON T1.JWOID_REF=T5.JWOID
            WHERE T1.JWCID_REF='$id' ORDER BY T1.JWCID_REF ASC"); 

            $objREQ = DB::select("SELECT T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,
            T3.ICODE AS SOITEMID_CODE,
            CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS MAIN_UOMCODE
            FROM TBL_TRN_JWC_DIS T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_ITEM T3 ON T1.MAIN_ITEMID_REF=T3.ITEMID
            LEFT JOIN TBL_MST_UOM T4 ON T1.UOMID_REF=T4.UOMID
            WHERE T1.JWCID_REF='$id' ORDER BY T1.JWCID_REF ASC");

            
			$material_array=array();

			if(isset($objREQ) && !empty($objREQ)){
				foreach($objREQ as $row){
          
                    $mitem_id = $row->JWOID_REF."_".$row->PROID_REF."_".$row->SOID_REF."_".$row->SQID_REF."_".$row->SEID_REF."_".$row->MAIN_ITEMID_REF; 

                    $material_array[]=array(
                        'JWO_REQID'=>NULL,
                        'ITEMID_REF'=>$row->ITEMID_REF,
                        'ICODE'=>$row->ICODE,
                        'NAME'=>$row->ITEM_NAME,
                        'MAIN_UOMCODE'=>$row->MAIN_UOMCODE,
                        'MAIN_UOMID_REF'=>$row->UOMID_REF,
                        'MAIN_JWOID'=>$row->JWOID_REF,
                        'MAIN_PROID'=>$row->PROID_REF,
                        'MAIN_SOID'=>$row->SOID_REF,
                        'MAIN_SQID'=>$row->SQID_REF,
                        'MAIN_SEID'=>$row->SEID_REF,
                        'MAIN_ITEMID'=>$row->MAIN_ITEMID_REF,
                        'STD_BOM_QTY'=>NULL,
                        'JWC_QTY'=>$row->JWC_QTY,
                        'BATCH_QTY_REF'=>$row->BATCH_QTY_REF,
                        'MAIN_ITEM_ROWID'=>$mitem_id,
                        'IGST'=>$row->IGST,
                        'CGST'=>$row->CGST,
                        'SGST'=>$row->SGST,
                        'JWC_RATE'=>$row->JWC_RATE,
                    );

				}
						
			}

            $objvendorcode2 = DB::table('TBL_MST_SUBLEDGER')
                ->where('BELONGS_TO','=','Vendor')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('SGLID','=',$objMstResponse->VID_REF)    
                ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                ->first();

            
            $ObjBranch =  [];
            $TAXSTATE = [];
            $objShpAddress=[] ;
            $objBillAddress=[];

            $sid        =   $objMstResponse->BILLTO;
            $SLID_REF   =   $objMstResponse->VID_REF;
            $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
            $VID        =   $ObVID->VID;


            $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                    WHERE SHIPTO= ? AND VID_REF = ? ', [$sid,$VID]);

            

            $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  
                            WHERE BRID= ? ', [$BRID_REF]);

            


            if(isset($ObjSHIPTO) && $ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF)
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
    
            $ObjAddressID = $ObjSHIPTO[0]->LID;
            
            if(!empty($ObjSHIPTO)){
            $objShpAddress[] = $ObjSHIPTO[0]->LADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
            }

            
            
            $bid = $objMstResponse->BILLTO;
            

            $ObjBillTo =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                WHERE BILLTO= ? AND VID_REF = ? ', [$bid,$VID]);

            

            $ObjCity2 =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                        WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                        [$Status,$ObjBillTo[0]->CITYID_REF,$ObjBillTo[0]->CTRYID_REF,$ObjBillTo[0]->STID_REF]);

                


            $ObjState2 =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                        WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBillTo[0]->STID_REF,$ObjBillTo[0]->CTRYID_REF]);

            $ObjCountry2 =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                        WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBillTo[0]->CTRYID_REF]);

            $ObjAddressID = $ObjBillTo[0]->LID;
            if(!empty($ObjBillTo)){
                $objBillAddress[] = $ObjBillTo[0]->LADD .' '.$ObjCity2[0]->NAME .' '.$ObjState2[0]->NAME .' '.$ObjCountry2[0]->NAME;
            } 


            $ObjUnionUDF    =   DB::table("TBL_MST_UDFFOR_JWC")->select('*')
                                ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF){       
                                    $query->select('UDFJWCID')->from('TBL_MST_UDFFOR_JWC')
                                    ->where('STATUS','=','A')
                                    ->where('PARENTID','=',0)
                                    ->where('DEACTIVATED','=',0)
                                    ->where('CYID_REF','=',$CYID_REF);                                  
                                    }
                                )
                                ->where('DEACTIVATED','=',0)
                                ->where('STATUS','<>','C')                    
                                ->where('CYID_REF','=',$CYID_REF);
                                    
            $objUdf         =   DB::table('TBL_MST_UDFFOR_JWC')
                                ->where('STATUS','=','A')
                                ->where('PARENTID','=',0)
                                ->where('DEACTIVATED','=',0)
                                ->where('CYID_REF','=',$CYID_REF)
                                ->union($ObjUnionUDF)
                                ->get()->toArray();  

            $objCountUDF    =   count($objUdf);
        
        
            $objtempUdf     =   $objUdf;
            foreach ($objtempUdf as $index => $udfvalue) {

                $objSavedUDF =  DB::table('TBL_TRN_JWC_UDF')
                                ->where('JWCID_REF','=',$id)
                                ->where('UDFJWCID_REF','=',$udfvalue->UDFJWCID)
                                ->select('VALUE')
                                ->get()->toArray();

                if(!empty($objSavedUDF)){
                    $objUdf[$index]->UDF_VALUE = $objSavedUDF[0]->VALUE;
                }
                else{
                    $objUdf[$index]->UDF_VALUE = NULL;
                }
            }

            $objtempUdf = [];
        

            $FormId  = $this->form_id;
            $AlpsStatus =   $this->AlpsStatus();

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view($this->view.'edit', compact([
                'AlpsStatus','FormId','objRights','objMAT','objUdf','objCountUDF','objMstResponse','objvendorcode2',
                'material_array','objShpAddress','objBillAddress','TAXSTATE','TabSetting'
                ]));   

        }
    
    }

    public function view($id){
     
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

            $objMstResponse = DB::table('TBL_TRN_JWC_HDR')
                ->where('FYID_REF','=',Session::get('FYID_REF'))
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('JWCID','=',$id)
                ->select('*')
                ->first();


            $objMAT = DB::select("SELECT T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,
            T3.UOMCODE,T3.DESCRIPTIONS,
            T5.JWONO
            FROM TBL_TRN_JWC_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            LEFT JOIN TBL_TRN_JWO_HDR T5 ON T1.JWOID_REF=T5.JWOID
            WHERE T1.JWCID_REF='$id' ORDER BY T1.JWCID_REF ASC"); 

            $objREQ = DB::select("SELECT T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,
            T3.ICODE AS SOITEMID_CODE,
            CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS MAIN_UOMCODE
            FROM TBL_TRN_JWC_DIS T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_ITEM T3 ON T1.MAIN_ITEMID_REF=T3.ITEMID
            LEFT JOIN TBL_MST_UOM T4 ON T1.UOMID_REF=T4.UOMID
            WHERE T1.JWCID_REF='$id' ORDER BY T1.JWCID_REF ASC");

            
			$material_array=array();

			if(isset($objREQ) && !empty($objREQ)){
				foreach($objREQ as $row){
          
                    $mitem_id = $row->JWOID_REF."_".$row->PROID_REF."_".$row->SOID_REF."_".$row->SQID_REF."_".$row->SEID_REF."_".$row->MAIN_ITEMID_REF; 

                    $material_array[]=array(
                        'JWO_REQID'=>NULL,
                        'ITEMID_REF'=>$row->ITEMID_REF,
                        'ICODE'=>$row->ICODE,
                        'NAME'=>$row->ITEM_NAME,
                        'MAIN_UOMCODE'=>$row->MAIN_UOMCODE,
                        'MAIN_UOMID_REF'=>$row->UOMID_REF,
                        'MAIN_JWOID'=>$row->JWOID_REF,
                        'MAIN_PROID'=>$row->PROID_REF,
                        'MAIN_SOID'=>$row->SOID_REF,
                        'MAIN_SQID'=>$row->SQID_REF,
                        'MAIN_SEID'=>$row->SEID_REF,
                        'MAIN_ITEMID'=>$row->MAIN_ITEMID_REF,
                        'STD_BOM_QTY'=>NULL,
                        'JWC_QTY'=>$row->JWC_QTY,
                        'BATCH_QTY_REF'=>$row->BATCH_QTY_REF,
                        'MAIN_ITEM_ROWID'=>$mitem_id ,
                        'IGST'=>$row->IGST,
                        'CGST'=>$row->CGST,
                        'SGST'=>$row->SGST,
                        'JWC_RATE'=>$row->JWC_RATE,
                    );

				}
						
			}

            
            $objvendorcode2 = DB::table('TBL_MST_SUBLEDGER')
                ->where('BELONGS_TO','=','Vendor')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('SGLID','=',$objMstResponse->VID_REF)    
                ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                ->first();

            
            $ObjBranch =  [];
            $TAXSTATE = [];
            $objShpAddress=[] ;
            $objBillAddress=[];

            $sid        =   $objMstResponse->BILLTO;
            $SLID_REF   =   $objMstResponse->VID_REF;
            $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
            $VID        =   $ObVID->VID;


            $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                    WHERE SHIPTO= ? AND VID_REF = ? ', [$sid,$VID]);

            

            $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  
                            WHERE BRID= ? ', [$BRID_REF]);

            


            if(isset($ObjSHIPTO) && $ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF)
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
    
            $ObjAddressID = $ObjSHIPTO[0]->LID;
            
            if(!empty($ObjSHIPTO)){
            $objShpAddress[] = $ObjSHIPTO[0]->LADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
            }

            
            
            $bid = $objMstResponse->BILLTO;
            

            $ObjBillTo =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                WHERE BILLTO= ? AND VID_REF = ? ', [$bid,$VID]);

            

            $ObjCity2 =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                        WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                        [$Status,$ObjBillTo[0]->CITYID_REF,$ObjBillTo[0]->CTRYID_REF,$ObjBillTo[0]->STID_REF]);

                


            $ObjState2 =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                        WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBillTo[0]->STID_REF,$ObjBillTo[0]->CTRYID_REF]);

            $ObjCountry2 =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                        WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBillTo[0]->CTRYID_REF]);

            $ObjAddressID = $ObjBillTo[0]->LID;
            if(!empty($ObjBillTo)){
                $objBillAddress[] = $ObjBillTo[0]->LADD .' '.$ObjCity2[0]->NAME .' '.$ObjState2[0]->NAME .' '.$ObjCountry2[0]->NAME;
            } 


            $ObjUnionUDF    =   DB::table("TBL_MST_UDFFOR_JWC")->select('*')
                                ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF){       
                                    $query->select('UDFJWCID')->from('TBL_MST_UDFFOR_JWC')
                                    ->where('STATUS','=','A')
                                    ->where('PARENTID','=',0)
                                    ->where('DEACTIVATED','=',0)
                                    ->where('CYID_REF','=',$CYID_REF);                                  
                                    }
                                )
                                ->where('DEACTIVATED','=',0)
                                ->where('STATUS','<>','C')                    
                                ->where('CYID_REF','=',$CYID_REF);
                                    
            $objUdf         =   DB::table('TBL_MST_UDFFOR_JWC')
                                ->where('STATUS','=','A')
                                ->where('PARENTID','=',0)
                                ->where('DEACTIVATED','=',0)
                                ->where('CYID_REF','=',$CYID_REF)
                                ->union($ObjUnionUDF)
                                ->get()->toArray();  

            $objCountUDF    =   count($objUdf);
        
        
            $objtempUdf     =   $objUdf;
            foreach ($objtempUdf as $index => $udfvalue) {

                $objSavedUDF =  DB::table('TBL_TRN_JWC_UDF')
                                ->where('JWCID_REF','=',$id)
                                ->where('UDFJWCID_REF','=',$udfvalue->UDFJWCID)
                                ->select('VALUE')
                                ->get()->toArray();

                if(!empty($objSavedUDF)){
                    $objUdf[$index]->UDF_VALUE = $objSavedUDF[0]->VALUE;
                }
                else{
                    $objUdf[$index]->UDF_VALUE = NULL;
                }
            }

            $objtempUdf = [];
        

            $FormId  = $this->form_id;
            $AlpsStatus =   $this->AlpsStatus();

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view($this->view.'view', compact([
                'AlpsStatus','FormId','objRights','objMAT','objUdf','objCountUDF','objMstResponse','objvendorcode2',
                'material_array','objShpAddress','objBillAddress','TAXSTATE','TabSetting'
                ]));   

        }
      
    }
     
    public function update(Request $request){

        $r_count1 = $request['Row_Count1'];
        $r_count3 = $request['Row_Count3'];
        $r_count5 = $request['Row_Count5'];
        
        $req_data=array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $req_data[$i] = [
                    'JWOID_REF'         => $request['JWOID_REF_'.$i],
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'         => $request['MAIN_UOMID_REF_'.$i],
                    'JW_PRODUCE_QTY'    => $request['PD_OR_QTY_'.$i],
                    'EDA'               => $request['EDA_'.$i],
                    'PROID_REF'         => $request['PROID_REF_'.$i],
                    'SOID_REF'          => $request['SOID_REF_'.$i],
                    'SQID_REF'          => $request['SQID_REF_'.$i],
                    'SEID_REF'          => $request['SEID_REF_'.$i]   
                ];

            }
        }

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);

        $req_data5=array();
        for ($i=0; $i<=$r_count5; $i++){
            if(isset($request['REQ_ITEMID_REF_'.$i])){

                $StoreArr   =   array();
                $ITEMID_REF =   $request['REQ_ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $exp        =   explode(",",$ITEMROWID);

                foreach($exp as $val){
                    $keyid              =   explode("_",$val);
                    $batchid            =   $keyid[0];

                    $objStore =  DB::table('TBL_MST_BATCH')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('BATCHID','=',$batchid)
                        ->where('STATUS','=',"A")
                        ->select('STID_REF')
                        ->first();

                    $StoreArr[]=$objStore->STID_REF;
                }

                if(!empty($StoreArr)){
                    $StoreId    =   array_unique($StoreArr);
                    $STID_REF   =   implode(",",$StoreId);
                }
                else{
                    $STID_REF   =   NULL;
                }

                $req_data5[$i] = [
                    'ITEMID_REF'    	=> $request['REQ_ITEMID_REF_'.$i],
                    'UOMID_REF'    	    => $request['REQ_UOMID_REF_'.$i],
                    'STID_REF'    	    => $STID_REF,
                    'JWC_QTY'	        => $request['REQ_JWC_QTY_'.$i],
                    'MAIN_ITEMID_REF'   => $request['REQ_SOITEMID_REF_'.$i],
                    'JWOID_REF'      	=> $request['REQ_JWOID_REF_'.$i],
                    'PROID_REF'      	=> $request['REQ_PROID_REF_'.$i],
                    'SOID_REF'      	=> $request['REQ_SOID_REF_'.$i],
                    'SQID_REF'     		=> $request['REQ_SQID_REF_'.$i],  
                    'SEID_REF'     		=> $request['REQ_SEID_REF_'.$i],
                    'BATCH_QTY_REF'     => $request['HiddenRowId_'.$i],  
                    'JWC_RATE'     		=> $request['JWC_RATE_'.$i],
                    'IGST'     		=> $request['IGST_'.$i],
                    'CGST'     		=> $request['CGST_'.$i],
                    'SGST'     		=> $request['SGST_'.$i],
                ];

            }
        }


		if($r_count5 > 0){
            $wrapped_links5["DIS"] = $req_data5; 
			$XMLREQ = ArrayToXml::convert($wrapped_links5);
        }
        else{
            $XMLREQ=NULL;
        }
        
        
        $req_data55=array();
        for ($i=0; $i<=$r_count5; $i++){
            if(isset($request['REQ_ITEMID_REF_'.$i])){

                $dataArr    =   array();
                $ITEMID_REF =   $request['REQ_ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];

                if($ITEMROWID !=""){
                    $exp        =   explode(",",$ITEMROWID);

                    foreach($exp as $val){
                        $keyid              =   explode("_",$val);
                        $batchid            =   $keyid[0];
                        $qty                =   $keyid[1];
                        $dataArr[$batchid]  =   $qty;
                    }
                }

                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){
                 
                        $objBatch =  DB::table('TBL_MST_BATCH')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('BATCHID','=',$key)
                        ->where('STATUS','=',"A")
                        ->select('BATCHID','BATCH_CODE','ITEMID_REF','STID_REF','SERIALNO','UOMID_REF','CURRENT_QTY')
                        ->first();

                        $req_data55[$i][] = [
                            'ITEMID_REF'    => $ITEMID_REF,
                            'UOMID_REF'     => $objBatch->UOMID_REF,
                            'STID_REF'      => $objBatch->STID_REF,
                            'BATCHID_REF'   => $objBatch->BATCHID,
                            'DIS_QTY'       => $val,
                            'MAIN_ITEMID_REF'   => $request['REQ_SOITEMID_REF_'.$i],
                            'JWOID_REF'      	=> $request['REQ_JWOID_REF_'.$i],
                            'PROID_REF'      	=> $request['REQ_PROID_REF_'.$i],
                            'SOID_REF'      	=> $request['REQ_SOID_REF_'.$i],
                            'SQID_REF'     		=> $request['REQ_SQID_REF_'.$i],  
                            'SEID_REF'     		=> $request['REQ_SEID_REF_'.$i],        
                        ];

                    }
                }

            }
        }
        
		if($r_count5 > 0){
            $wrapped_links55["STORE"] = $req_data55; 
			$XMLSTORE = ArrayToXml::convert($wrapped_links55);
        }
        else{
            $XMLSTORE=NULL;
        }


        $udffield_Data = [];      
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFJWCID_REF'] = $request['udffie_'.$i]; 
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
        
        $USERID_REF = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $JWCNO = $request['JWCNO'];
        $JWCDT = $request['JWCDT'];
        $VID_REF = $request['VID_REF'];
        $BILLTO = $request['BILLTO'];
        $SHIPTO = $request['SHIPTO'];
        $REMARKS = $request['REMARKS'];
        
        $log_data = [ 
            $JWCNO,             $JWCDT,         $VID_REF,       $BILLTO,       $SHIPTO,       
            $CYID_REF,          $BRID_REF,      $FYID_REF,      $VTID_REF,     $XMLMAT,        
            $XMLREQ,            $XMLSTORE,      $XMLUDF,        $USERID_REF,    Date('Y-m-d'),  
            Date('h:i:s.u'),    $ACTIONNAME,    $IPADDRESS,     $REMARKS
        ];

        
        $sp_result = DB::select('EXEC SP_JWC_UP ?,?,?,?,? ,?,?,?,?,? ,?,?,?,?,? ,?,?,?,?', $log_data);      
        
    
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
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
        $r_count3 = $request['Row_Count3'];
        $r_count5 = $request['Row_Count5'];
        
        $req_data=array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $req_data[$i] = [
                    'JWOID_REF'         => $request['JWOID_REF_'.$i],
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'         => $request['MAIN_UOMID_REF_'.$i],
                    'JW_PRODUCE_QTY'    => $request['PD_OR_QTY_'.$i],
                    'EDA'               => $request['EDA_'.$i],
                    'PROID_REF'         => $request['PROID_REF_'.$i],
                    'SOID_REF'          => $request['SOID_REF_'.$i],
                    'SQID_REF'          => $request['SQID_REF_'.$i],
                    'SEID_REF'          => $request['SEID_REF_'.$i]   
                ];

            }
        }

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);

        $req_data5=array();
        for ($i=0; $i<=$r_count5; $i++){
            if(isset($request['REQ_ITEMID_REF_'.$i])){

                $StoreArr   =   array();
                $ITEMID_REF =   $request['REQ_ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $exp        =   explode(",",$ITEMROWID);

                foreach($exp as $val){
                    $keyid              =   explode("_",$val);
                    $batchid            =   $keyid[0];

                    $objStore =  DB::table('TBL_MST_BATCH')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('BATCHID','=',$batchid)
                        ->where('STATUS','=',"A")
                        ->select('STID_REF')
                        ->first();

                    $StoreArr[]=$objStore->STID_REF;
                }

                if(!empty($StoreArr)){
                    $StoreId    =   array_unique($StoreArr);
                    $STID_REF   =   implode(",",$StoreId);
                }
                else{
                    $STID_REF   =   NULL;
                }

                $req_data5[$i] = [
                    'ITEMID_REF'    	=> $request['REQ_ITEMID_REF_'.$i],
                    'UOMID_REF'    	    => $request['REQ_UOMID_REF_'.$i],
                    'STID_REF'    	    => $STID_REF,
                    'JWC_QTY'	        => $request['REQ_JWC_QTY_'.$i],
                    'MAIN_ITEMID_REF'   => $request['REQ_SOITEMID_REF_'.$i],
                    'JWOID_REF'      	=> $request['REQ_JWOID_REF_'.$i],
                    'PROID_REF'      	=> $request['REQ_PROID_REF_'.$i],
                    'SOID_REF'      	=> $request['REQ_SOID_REF_'.$i],
                    'SQID_REF'     		=> $request['REQ_SQID_REF_'.$i],  
                    'SEID_REF'     		=> $request['REQ_SEID_REF_'.$i],
                    'BATCH_QTY_REF'     => $request['HiddenRowId_'.$i], 
                    'JWC_RATE'     		=> $request['JWC_RATE_'.$i],
                    'IGST'     		=> $request['IGST_'.$i],
                    'CGST'     		=> $request['CGST_'.$i],
                    'SGST'     		=> $request['SGST_'.$i], 
                ];

            }
        }


		if($r_count5 > 0){
            $wrapped_links5["DIS"] = $req_data5; 
			$XMLREQ = ArrayToXml::convert($wrapped_links5);
        }
        else{
            $XMLREQ=NULL;
        }
        
        
        $req_data55=array();
        for ($i=0; $i<=$r_count5; $i++){
            if(isset($request['REQ_ITEMID_REF_'.$i])){

                $dataArr    =   array();
                $ITEMID_REF =   $request['REQ_ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];

                if($ITEMROWID !=""){
                    $exp        =   explode(",",$ITEMROWID);

                    foreach($exp as $val){
                        $keyid              =   explode("_",$val);
                        $batchid            =   $keyid[0];
                        $qty                =   $keyid[1];
                        $dataArr[$batchid]  =   $qty;
                    }
                }

                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){
                 
                        $objBatch =  DB::table('TBL_MST_BATCH')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('BATCHID','=',$key)
                        ->where('STATUS','=',"A")
                        ->select('BATCHID','BATCH_CODE','ITEMID_REF','STID_REF','SERIALNO','UOMID_REF','CURRENT_QTY')
                        ->first();

                        $req_data55[$i][] = [
                            'ITEMID_REF'    => $ITEMID_REF,
                            'UOMID_REF'     => $objBatch->UOMID_REF,
                            'STID_REF'      => $objBatch->STID_REF,
                            'BATCHID_REF'   => $objBatch->BATCHID,
                            'DIS_QTY'       => $val,
                            'MAIN_ITEMID_REF'   => $request['REQ_SOITEMID_REF_'.$i],
                            'JWOID_REF'      	=> $request['REQ_JWOID_REF_'.$i],
                            'PROID_REF'      	=> $request['REQ_PROID_REF_'.$i],
                            'SOID_REF'      	=> $request['REQ_SOID_REF_'.$i],
                            'SQID_REF'     		=> $request['REQ_SQID_REF_'.$i],  
                            'SEID_REF'     		=> $request['REQ_SEID_REF_'.$i],        
                        ];

                    }
                }

            }
        }
        
		if($r_count5 > 0){
            $wrapped_links55["STORE"] = $req_data55; 
			$XMLSTORE = ArrayToXml::convert($wrapped_links55);
        }
        else{
            $XMLSTORE=NULL;
        }


        $udffield_Data = [];      
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFJWCID_REF'] = $request['udffie_'.$i]; 
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
        
        $USERID_REF = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $JWCNO = $request['JWCNO'];
        $JWCDT = $request['JWCDT'];
        $VID_REF = $request['VID_REF'];
        $BILLTO = $request['BILLTO'];
        $SHIPTO = $request['SHIPTO'];
        $REMARKS = $request['REMARKS'];
        
        $log_data = [ 
            $JWCNO,             $JWCDT,         $VID_REF,       $BILLTO,       $SHIPTO,       
            $CYID_REF,          $BRID_REF,      $FYID_REF,      $VTID_REF,     $XMLMAT,        
            $XMLREQ,            $XMLSTORE,      $XMLUDF,        $USERID_REF,    Date('Y-m-d'),  
            Date('h:i:s.u'),    $ACTIONNAME,    $IPADDRESS,     $REMARKS
        ];

        
        $sp_result = DB::select('EXEC SP_JWC_UP ?,?,?,?,? ,?,?,?,?,? ,?,?,?,?,? ,?,?,?,?', $log_data);     
        
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
                $TABLE      =   "TBL_TRN_JWC_HDR";
                $FIELD      =   "JWCID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_JWC ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
    
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
        $TABLE      =   "TBL_TRN_JWC_HDR";
        $FIELD      =   "JWCID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_JWC_MAT',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_JWC_DIS',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_JWC_STORE',
        ];
        $req_data[3]=[
            'NT'  => 'TBL_TRN_JWC_UDF',
        ];
           
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_JWC  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

        if($sp_result[0]->RESULT=="CANCELED"){  

            return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
        
            return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'invalid'=>'invalid']);
        }
        
        exit(); 
    }


    public function attachment($id){

        if(!is_null($id)){
        
            $FormId     =   $this->form_id;

            $objResponse = DB::table('TBL_TRN_JWC_HDR')->where('JWCID','=',$id)->first();

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

            return view($this->view.'attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments']));
        }

    }
  
    public function docuploads(Request $request){

        $FormId     =   $this->form_id;

        $formData = $request->all();

        $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size = config("erpconst.attachments.max_size") * 1020 * 1024;

       
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
        
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/JobWorkChallan";
		
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

                   

                    $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."#_".$filenamewithextension;  

                    if ($uploadedFile->isValid()) {

                        if(in_array($extension,$allow_extnesions)){
                            
                            if($filesize < $allow_size){

                                $filename = $destinationPath."/".$filenametostore;

                                if (!file_exists($filename)) {

                                   $uploadedFile->move($destinationPath, $filenametostore);  
                                   $uploaded_data[$index]["FILENAME"] =$filenametostore;
                                   $uploaded_data[$index]["LOCATION"] = $destinationPath."/";
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
            return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
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
        

        $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);

     
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
  
    
    public function checkExist(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $JWCNO      =   $request->JWCNO;
        
        $objExit    =   DB::table('TBL_TRN_JWC_HDR')
                        ->where('TBL_TRN_JWC_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_TRN_JWC_HDR.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_TRN_JWC_HDR.FYID_REF','=',Session::get('FYID_REF'))
                        ->where('TBL_TRN_JWC_HDR.JWCNO','=',$JWCNO)
                        ->select('TBL_TRN_JWC_HDR.JWCNO')
                        ->first();
        
        if($objExit){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate JWO NO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }

    public function getItemDetails(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $Status     =   $request['status'];
        $VID_REF    =   $request['VID_REF'];
        $JWOID_REF  =   $request['JWOID_REF'];
        $StdCost    =   0;

        $AlpsStatus =   $this->AlpsStatus();

        $ObjItem =  DB::select("SELECT 
        T1.UOMID_REF AS MAIN_UOMID_REF,T1.PROD_QTY AS ORDER_QTY,T1.PENDING_QTY AS Qty,T1.JWOID_REF,T1.PROID_REF,T1.SOID_REF,T1.SEID_REF,T1.SQID_REF,
        T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI
        FROM TBL_TRN_JWO_MAT T1
        LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
        WHERE T1.JWOID_REF='$JWOID_REF' 
        ");

        if(!empty($ObjItem)){

            foreach ($ObjItem as $index=>$dataRow){

                $SOQTY      =   isset($dataRow->ORDER_QTY)? $dataRow->ORDER_QTY : 0;   
                $FROMQTY    =   isset($dataRow->Qty)? $dataRow->Qty : 0;   
                     
                $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS 
                FROM TBL_MST_UOM  
                WHERE  CYID_REF = ?  AND UOMID = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                [$CYID_REF, $dataRow->MAIN_UOMID_REF, 'A' ]);          
                
                $ObjItemGroup =  DB::select('SELECT TOP 1 GROUPCODE, GROUPNAME 
                FROM TBL_MST_ITEMGROUP  
                WHERE  CYID_REF = ?  AND ITEMGID = ?
                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                [$CYID_REF, $dataRow->ITEMGID_REF, 'A' ]);

                $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS 
                FROM TBL_MST_ITEMCATEGORY  
                WHERE  CYID_REF = ?  AND ICID = ?
                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                [$CYID_REF, $dataRow->ICID_REF, 'A' ]);


                $ItemRowData =  DB::select('SELECT top 1 * FROM TBL_MST_ITEM  WHERE ITEMID = ? ', [$dataRow->ITEMID]);

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

                $item_unique_row_id =   $JWOID_REF."_".$dataRow->PROID_REF."_".$dataRow->SOID_REF."_".$dataRow->SQID_REF."_".$dataRow->SEID_REF."_".$dataRow->ITEMID;
               
               
                $row = '';

                $row = $row.'
                <tr id="item_'.$index.'"  class="clsitemid">
                    <td style="width:8%;text-align:center;"><input type="checkbox" id="chkId'.$index.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>
                    <td style="width:10%;">'.$dataRow->ICODE.'</td>
                    <td style="width:10%;">'.$dataRow->NAME.'</td>
                    <td style="width:8%;">'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>
                    <td style="width:8%;">'.$FROMQTY.'</td>
                    <td style="width:8%;">'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>
                    <td style="width:8%;">'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                    <td style="width:8%;">'.$BusinessUnit.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                    <td style="width:8%;">Authorized</td>
                    <td hidden>
                        <input type="text" id="txtitem_'.$index.'" 
                            data-desc1="'.$dataRow->ITEMID.'" 
                            data-desc2="'.$dataRow->ICODE.'" 
                            data-desc3="'.$dataRow->NAME.'" 
                            data-desc4="'.$dataRow->MAIN_UOMID_REF.'" 
                            data-desc5="'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'" 
                            data-desc6="'.$FROMQTY.'" 
                            data-desc7="'.$item_unique_row_id.'" 
                            data-desc8="'.$dataRow->SQID_REF.'"
                            data-desc9="'.$dataRow->SEID_REF.'"
                            data-desc10="'.$JWOID_REF.'"
                            data-desc11="'.$dataRow->SOID_REF.'"
                            data-desc12="'.$SOQTY.'"
                            data-desc13="'.$dataRow->PROID_REF.'"
                            data-desc14="'.$dataRow->ORDER_QTY.'"
                        />
                    </td>
                </tr>';
                echo $row;    
            }         
        }           
        else{
            echo '<tr><td> Record not found.</td></tr>';
        }
        exit();
    }
        
    public function getDocNo(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $VTID_REF       =   $request['id'];
        $fieldid        =   $request['fieldid'];

        $ObjData        =   DB::select("SELECT JWOID,JWONO,JWODT 
                            FROM TBL_TRN_JWO_HDR 
                            WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF' 
                            AND VID_REF='$VTID_REF' AND STATUS='A'"); 

        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){

                $row            =   '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="socode_'.$dataRow->JWOID .'"  class="clssJWOID" value="'.$dataRow->JWOID.'" ></td>
                <td class="ROW2">'.$dataRow->JWONO;
                $row = $row.'<input type="hidden" id="txtsocode_'.$dataRow->JWOID.'" data-desc="'.$dataRow->JWONO.'"  value="'.$dataRow->JWOID.'"/></td>
                <td class="ROW3" >'.$dataRow->JWODT.'</td></tr>';
                echo $row;
                
            }

        }else{
            echo '<tr><td>Record not found.</td></tr>';
        }
        exit();   
    }
    
    public function get_materital_item(Request $request){
        
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $item_array     =   $request['item_array'];
        $taxstate       =   $request['taxstate']; 

        
       
        $material_array=array();
        foreach($item_array as $key=>$val){

            $exp        =   explode("_",$val);
            $JWOID      =   $exp[0];
            $SOID       =   $exp[1];
            $ITEMID     =   $exp[2];
            $ITEMCODE   =   $exp[3];
            $JWC_QTY    =   $exp[4];
            $SQID       =   $exp[5];
            $SEID       =   $exp[6];
            $PROID      =   $exp[7];

            $mitem_id   =   $JWOID."_".$PROID."_".$SOID."_".$SQID."_".$SEID."_".$ITEMID;

            $WHERE_SQID_REF= $SQID !=""?" AND SQID_REF='$SQID' ":"";
            $WHERE_SEID_REF= $SEID !=""?" AND SEID_REF='$SEID' ":"";
            $WHERE_SOID_REF= $SOID !=""?" AND SOID_REF='$SOID' ":"";
            $WHERE_PROID_REF= $PROID !=""?" AND PROID_REF='$PROID' ":"";

            $JWO_REQ    =   DB::select("SELECT T1.*,T2.ICODE,T2.NAME,T2.MAIN_UOMID_REF,T2.HSNID_REF,CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOMCODE, T1.AFT_CHANGES_QTY
            FROM TBL_TRN_JWO_REQ T1 
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T2.MAIN_UOMID_REF=T3.UOMID
            WHERE T1.JWOID_REF='$JWOID' AND MAIN_ITEMID_REF='$ITEMID' $WHERE_PROID_REF 
            $WHERE_SOID_REF $WHERE_SQID_REF $WHERE_SEID_REF");

           
            if(isset($JWO_REQ) && !empty($JWO_REQ)){
                foreach($JWO_REQ as $row){

                
                    $Tax            =   $this->getTaxData($taxstate,$row->HSNID_REF);
                    $JWC_RATE_ARR   =   DB::select("SELECT TOP 1 RATE FROM TBL_MST_BATCH WHERE ITEMID_REF='$row->ITEMID_REF' AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF'");
                    $JWC_RATE       =   isset($JWC_RATE_ARR) && !empty($JWC_RATE_ARR)?$JWC_RATE_ARR[0]->RATE:'0.00000';

                    $material_array[]=array(
                        'JWO_REQID'=>$row->JWO_REQID,
                        'ITEMID_REF'=>$row->ITEMID_REF,
                        'ICODE'=>$row->ICODE,
                        'NAME'=>$row->NAME,
                        'MAIN_UOMCODE'=>$row->MAIN_UOMCODE,
                        'MAIN_UOMID_REF'=>$row->MAIN_UOMID_REF,
                        'AFT_CHANGES_QTY'=>$row->AFT_CHANGES_QTY,
                        'MAIN_JWOID'=>$JWOID,
                        'MAIN_PROID'=>$PROID,
                        'MAIN_SOID'=>$SOID,
                        'MAIN_ITEMID'=>$ITEMID,
                        'STD_BOM_QTY'=>$row->STD_BOM_QTY,
                        'MAIN_SQID'=>$SQID,
                        'MAIN_SEID'=>$SEID,
                        'MAIN_ITEM_ROWID'=>$mitem_id ,
                        'IGST'=>$taxstate =="OutofState" && !empty($Tax) ?$Tax[0]:'0.0000',
                        'CGST'=>$taxstate =="WithinState" && !empty($Tax) ?$Tax[0]:'0.0000',
                        'SGST'=>$taxstate =="WithinState" && !empty($Tax) ?$Tax[1]:'0.0000',
                        'JWC_RATE'=>$JWC_RATE,
                    );

                    
                }
            }
            
        }

        if(!empty($material_array)){
            $Row_Count5 =   count($material_array);
            echo'<table id="example8" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                        <tr>
                            <th hidden ><input class="form-control" type="hidden" name="Row_Count5" id ="Row_Count5" value="'.$Row_Count5.'"></th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>UOM</th>
                            <th>Store</th>
                            <th hidden>Standard BOM Qty</th>
                            <th>JWC Qty</th>
                            <th>JWC Rate</th>
                            <th>JWC Amount</th>
                            <th>IGST</th>
                            <th>IGST Amount</th>
                            <th>CGST</th>
                            <th>CGST Amount</th>
                            <th>SGST</th>
                            <th>SGST Amount</th>
                            <th>Total GST Amount</th>
                            <th>Total after GST</th>
                        </tr>
                    </thead>
                    <tbody>';

                    foreach($material_array as $index=>$row_data){

                        $sta_qty     = number_format(round(($row_data['STD_BOM_QTY']), 3),3,".","")  ;
                        // if($PROID == "")
                        // {
                           $jwc_qty     = number_format(round(($row_data['AFT_CHANGES_QTY']), 3),3,".","")  ;
                        // }
                        // else
                        // {
                            // $jwc_qty     = number_format(round(($row_data['STD_BOM_QTY']*$JWC_QTY), 3),3,".","")  ;
                        // }

                        echo '<tr  class="participantRow8">';

                        echo '<td><input type="text" id="txtSUBITEM_popup_'.$index.'" value="'.$row_data['ICODE'].'" class="form-control" readonly style="width:130px;" /></td>';
                        echo '<td><input type="text" id="SUBITEM_NAME_'.$index.'"     value="'.$row_data['NAME'].'"  class="form-control" readonly style="width:130px;" /></td>';

                        echo '<td hidden><input type="hidden" name="REQ_JWO_REQID_'.$index.'"    id="REQ_JWO_REQID_'.$index.'"    value="'.$row_data['JWO_REQID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_JWOID_REF_'.$index.'"    id="REQ_JWOID_REF_'.$index.'"    value="'.$row_data['MAIN_JWOID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_PROID_REF_'.$index.'"    id="REQ_PROID_REF_'.$index.'"    value="'.$row_data['MAIN_PROID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SOID_REF_'.$index.'"     id="REQ_SOID_REF_'.$index.'"     value="'.$row_data['MAIN_SOID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SOITEMID_REF_'.$index.'" id="REQ_SOITEMID_REF_'.$index.'" value="'.$row_data['MAIN_ITEMID'].'" /></td>';
                        echo '<td hidden><input type="text"   name="REQ_ITEMID_REF_'.$index.'"   id="REQ_ITEMID_REF_'.$index.'"   value="'.$row_data['ITEMID_REF'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SQID_REF_'.$index.'"     id="REQ_SQID_REF_'.$index.'"     value="'.$row_data['MAIN_SQID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SEID_REF_'.$index.'"     id="REQ_SEID_REF_'.$index.'"     value="'.$row_data['MAIN_SEID'].'" /></td>';
                        
                        echo '<td><input    type="text" name="REQ_MAIN_UOM_'.$index.'"           id="REQ_MAIN_UOM_'.$index.'"     value="'.$row_data['MAIN_UOMCODE'].'"   class="form-control" readonly style="width:130px;"  /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_UOMID_REF_'.$index.'"    id="REQ_UOMID_REF_'.$index.'"    value="'.$row_data['MAIN_UOMID_REF'].'" /></td>';
                       
                        echo '<td align="center"><a class="btn checkstore" onclick="getStore(this.id,'.$row_data['ITEMID_REF'].')"  id="'.$index.'" ><i class="fa fa-clone"></i></a></td>';
                        echo '<td hidden ><input type="hidden" name="TotalHiddenQty_'.$index.'" id="TotalHiddenQty_'.$index.'" ></td>';
                        echo '<td hidden ><input type="hidden" name="HiddenRowId_'.$index.'" id="HiddenRowId_'.$index.'" ></td>';

                        echo '<td hidden><input    type="text" name="REQ_STD_BOM_QTY_'.$index.'" id="REQ_STD_BOM_QTY_'.$index.'" value="'.$sta_qty.'" class="form-control" readonly /></td>';
                        echo '<td><input    type="text" name="REQ_JWC_QTY_'.$index.'" id="REQ_JWC_QTY_'.$index.'"     value="'.$jwc_qty.'" readonly     class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:130px;text-align:right;" /></td>';
                        
                        echo '<td hidden><input id="main_item_rowid_'.$index.'" value="'.$row_data['MAIN_ITEM_ROWID'].'"  /></td>';


                        echo '
                        <td><input type="text" name="JWC_RATE_'.$index.'" id="JWC_RATE_'.$index.'" value="'.$row_data['JWC_RATE'].'" class="form-control five-digits blurRate" maxlength="13" autocomplete="off" style="width:130px;text-align:right;" onkeyup="calculateItemTax()" ></td>
                        <td><input type="text" name="JWC_AMOUNT_'.$index.'" id="JWC_AMOUNT_'.$index.'"  class="form-control two-digits" maxlength="15" autocomplete="off" readonly="" style="width:130px;text-align:right;"></td>
                        <td><input type="text" name="IGST_'.$index.'" id="IGST_'.$index.'" value="'.$row_data['IGST'].'"  class="form-control four-digits" maxlength="12" autocomplete="off" readonly="" style="width:130px;text-align:right;"></td>
                        <td><input type="text" name="IGST_AMT_'.$index.'" id="IGST_AMT_'.$index.'" class="form-control two-digits" maxlength="15" autocomplete="off" readonly="" style="width:130px;text-align:right;"></td>
                        <td><input type="text" name="CGST_'.$index.'" id="CGST_'.$index.'" value="'.$row_data['CGST'].'" class="form-control four-digits" maxlength="12" autocomplete="off" readonly="" style="width:130px;text-align:right;"></td>
                        <td><input type="text" name="CGST_AMT_'.$index.'" id="CGST_AMT_'.$index.'" class="form-control two-digits" maxlength="15" autocomplete="off" readonly="" style="width:130px;text-align:right;"></td>
                        <td><input type="text" name="SGST_'.$index.'" id="SGST_'.$index.'" value="'.$row_data['SGST'].'" class="form-control four-digits" maxlength="12" autocomplete="off" readonly="" style="width:130px;text-align:right;"></td>
                        <td><input type="text" name="SGST_AMT_'.$index.'" id="SGST_AMT_'.$index.'" class="form-control two-digits" maxlength="15" autocomplete="off" readonly="" style="width:130px;text-align:right;"></td>
                        <td><input type="text" name="TGST_AMT_'.$index.'" id="TGST_AMT_'.$index.'" class="form-control two-digits" maxlength="15" autocomplete="off" readonly="" style="width:130px;text-align:right;"></td>
                        <td><input type="text" name="TT_AMT_'.$index.'" id="TT_AMT_'.$index.'" class="form-control two-digits" maxlength="15" autocomplete="off" readonly="" style="width:130px;text-align:right;"></td>
                        ';
                        
                        echo '</tr>';
                    }
                    
            echo '</tbody>';

            echo '<tr  class="participantRow8">
                    <td colspan="4" style="text-align:center;font-weight:bold;font-size: 13px;">TOTAL</td>    
                    <td id="REQ_JWC_QTY_total" style="text-align:right;font-weight:bold;font-size: 13px;"></td>
                    <td id="JWC_RATE_total" style="text-align:right;font-weight:bold;font-size: 13px;"></td>
                    <td id="JWC_AMOUNT_total" style="text-align:right;font-weight:bold;font-size: 13px;"></td>
                    <td style="text-align:right;font-weight:bold;font-size: 13px;"></td>
                    <td id="IGST_AMT_total" style="text-align:right;font-weight:bold;font-size: 13px;"></td>
                    <td style="text-align:right;font-weight:bold;font-size: 13px;"></td>
                    <td id="CGST_AMT_total" style="text-align:right;font-weight:bold;font-size: 13px;"></td>
                    <td style="text-align:right;font-weight:bold;font-size: 13px;"></td>
                    <td id="SGST_AMT_total" style="text-align:right;font-weight:bold;font-size: 13px;"></td>
                    <td id="TGST_AMT_total" style="text-align:right;font-weight:bold;font-size: 13px;"></td>
                    <td id="TT_AMT_total" style="text-align:right;font-weight:bold;font-size: 13px;"></td>
                </tr>';

                
            echo'</table>';
        }
        else{
            echo "Record not found.";
        }
        
        exit();
    }

    public function getTaxData($taxstate,$HSNID_REF){

        $TaxArray           =   [];
        $WHERE_INOUT_STATE  =   $taxstate =="OutofState"?" AND OUTOFSTATE = 1":" AND WITHINSTATE = 1";

        $Tax    =   DB::select("SELECT NRATE 
                        FROM TBL_MST_HSNNORMAL 
                        WHERE HSNID_REF = $HSNID_REF 
                        AND (DEACTIVATED=0 or DEACTIVATED is null) 
                        AND TAXID_REF IN (SELECT TAXID FROM TBL_MST_TAXTYPE WHERE FOR_PURCHASE = 1 $WHERE_INOUT_STATE)");

        if(!empty($Tax)){
            foreach($Tax as $key=>$val){
                $TaxArray[$key]=$val->NRATE;
            }
        }

        return $TaxArray;

    }

    public function getStoreDetails(Request $request){
        //dd($request->all());
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $ITEMID_REF = $request['ITEMID_REF'];
        $UOMID_REF = $request['UOMID_REF'];
        $ROW_ID     = $request['ROW_ID'];
        $ITEMROWID  = $request['ITEMROWID'];
        $ACTION_TYPE= $request['ACTION_TYPE'] =="VIEW"?'disabled':'';
        $SRNOA      =   NULL;
        $BATCHNOA   =   NULL;

        

        $dataArr    =   array();

        if($ITEMROWID !=""){
            $exp        =   explode(",",$ITEMROWID);

            foreach($exp as $val){
                $keyid      =   explode("_",$val);
                $batchid    =   $keyid[0];
                $qty        =   $keyid[1];
                $dataArr[$batchid]  =   $qty;
            }
        }

        
        $objResponse =  DB::table('TBL_MST_ITEMCHECKFLAG')
            ->where('ITEMID_REF','=',$ITEMID_REF)
            ->select('SRNOA','BATCHNOA')
            ->first();

        if(!empty($objResponse)){
            $SRNOA      =   $objResponse->SRNOA;
            $BATCHNOA   =   $objResponse->BATCHNOA;
        }

        $objBatch =  DB::SELECT("SELECT T1.BATCHID,T1.BATCH_CODE,T1.ITEMID_REF,T1.STID_REF,T1.SERIALNO,T1.UOMID_REF,
        T1.CURRENT_QTY,T2.STCODE,T2.NAME AS STNAME,T3.UOMCODE,T3.DESCRIPTIONS AS UOMDESCRIPTIONS 
        FROM TBL_MST_BATCH T1
        LEFT JOIN TBL_MST_STORE T2 ON T1.STID_REF=T2.STID
        LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
        WHERE T1.STATUS='A' AND T1.ITEMID_REF='$ITEMID_REF' AND T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' 
         AND T1.UOMID_REF='$UOMID_REF'
        ");
     
        echo '<thead>';
        echo '<tr>';
        echo $BATCHNOA =='1'?'<th>Batch / Lot No</th>':'';
        echo '<th>Store</th>';
        echo $SRNOA =='1'?'<th>Serial No</th>':'';
        echo '<th>Main UoM</th>';
        echo '<th>Stock-in-hand</th>';
        echo '<th>Dispatch Qty</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach($objBatch as $key=>$val){

            $qtyvalue   =   array_key_exists($val->BATCHID, $dataArr)?$dataArr[$val->BATCHID]:'';

            if($request['ACTION_TYPE'] =="ADD"){
                $CURRENT_QTY=$val->CURRENT_QTY;
            }
            else{
                $CURRENT_QTY=(floatval($val->CURRENT_QTY)+floatval($qtyvalue));
            }

            echo '<tr  class="participantRow33">';
            echo $BATCHNOA =='1'?'<td>'.$val->BATCH_CODE.'</td>':'';
            echo '<td>'.$val->STCODE.' - '.$val->STNAME.'</td>';
            echo $SRNOA =='1'?'<td>'.$val->SERIALNO.'</td>':'';
            echo '<td>'.$val->UOMCODE.' - '.$val->UOMDESCRIPTIONS.'</td>';
            echo '<td>'.$CURRENT_QTY.'</td>';
            echo '<td><input '.$ACTION_TYPE.' type="text" name="UserQty_'.$key.'" id="UserQty_'.$key.'" value="'.$qtyvalue.'" class="form-control qtytext" onkeyup="checkStoreQty('.$ROW_ID.','.$CURRENT_QTY.',this.value,'.$key.')" onkeypress="return isNumberDecimalKey(event,this)" autocomplete="off"  ></td>';
            echo '<td hidden><input type="hidden" name="BATCHID_'.$key.'" id="BATCHID_'.$key.'" value="'.$val->BATCHID.'" class="qtytext" ></td>';
            echo '</tr>';
        }

        echo '</tbody>';
        exit();
    }

   

    
}
