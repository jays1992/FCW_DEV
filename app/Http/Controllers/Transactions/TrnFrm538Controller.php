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

class TrnFrm538Controller extends Controller{

    protected $form_id  =   538;
    protected $vtid_ref =   608;
    protected $view     =   "transactions.sales.JobEstimation.trnfrm538";
   
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){  
        
        $objRights  =   $this->getUserRights(['VTID_REF'=>$this->vtid_ref]);
  
        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');   
        $FormId         =   $this->form_id;

        $objDataList    =   DB::select("SELECT 
        T1.*,
        T2.DESCRIPTIONS AS CREATEDBY,SI.JEID_REF,CU.NAME AS CUSTOMER_NAME  
        FROM TBL_TRN_JOB_ESTIMATION_HDR T1
		LEFT JOIN TBL_TRN_SERVICE_INVOICE_HDR SI ON T1.JEID=SI.JEID_REF  AND SI.STATUS='A'
        LEFT JOIN TBL_MST_CUSTOMER CU ON T1.CUSTOMER_ID=CU.SLID_REF
        LEFT JOIN TBL_MST_USER T2 ON T2.USERID=T1.CREATED_BY
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' ORDER BY JEID DESC");

        return view($this->view,compact(['FormId','objRights','objDataList']));
    }
	
	public function ViewReport($request) 
    {
        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
		
        $JEID       =   $myValue['JEID'];
        $Flag       =   $myValue['Flag'];
        
        
        $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
		$result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/Job Card Print');
        
        $reportParameters = array(
            'JEID' => $JEID,
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
	 
	 public function ViewReport_Blank($request) 
    {
        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
		
        $JEID       =   $myValue['JEID'];
        $Flag       =   $myValue['Flag'];
        
        
        $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
		$result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/Job_Card_Blank_Print');
        
        $reportParameters = array(
            'JEID' => $JEID,
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

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $FormId     =   $this->form_id;

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_JOB_ESTIMATION_HDR',
            'HDR_ID'=>'JEID',
            'HDR_DOC_NO'=>'JOB_NO',
            'HDR_DOC_DT'=>'JOB_DATE',
            'HDR_DOC_TYPE'=>'transaction'
        );

        $country_state_city =   $this->country_state_city();

        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req); 
        $objUdf     =   $this->getUdf(['VTID_REF'=>$this->vtid_ref]);

        $GSTdata = ['GSTID','GSTCODE','DESCRIPTIONS'];
        $objGstTypeList       = Helper::getTableData('TBL_MST_GSTTYPE',$GSTdata,NULL, NULL, NULL,'GSTCODE','ASC');

        $objothcurrency = $this->GetCurrencyMaster(); 

        return view($this->view.'add', compact(['FormId','doc_req','docarray','objUdf','objGstTypeList','country_state_city','objothcurrency']));       
    }


    public function save(Request $request){
        $VTID_REF       =   $this->vtid_ref;
        $USERID_REF     =   Auth::user()->USERID;         
        $IPADDRESS      =   $request->getClientIp();
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $sp_Approvallevel = [
            $USERID_REF, $VTID_REF, $CYID_REF,$BRID_REF,
            $FYID_REF
        ];
        
        $sp_listing_result = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?, ?', $sp_Approvallevel);

        if(!empty($sp_listing_result)){
            foreach ($sp_listing_result as $key=>$valueitem){  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$valueitem->LAVELS;
            }
        }

        //dd($Approvallevel); 

       

        $details  = array();
        if(isset($_REQUEST['PACKAGE_ID']) && !empty($_REQUEST['PACKAGE_ID'])){
            foreach($_REQUEST['PACKAGE_ID'] as $key=>$val){

                $details[] = array(
                'PAMID_REF'     => trim($_REQUEST['PACKAGE_ID'][$key])?trim($_REQUEST['PACKAGE_ID'][$key]):NULL,
                'AMOUNT'        => trim($_REQUEST['AMOUNT'][$key])?trim($_REQUEST['AMOUNT'][$key]):0,
                );
            }
        }

        if(!empty($details)){
            $wrapped_link["DETAIL"] = $details; 
            $XML_DETAILS = ArrayToXml::convert($wrapped_link);
        }
        else{
            $XML_DETAILS = NULL; 
        }

        $udffield_Data  =   [];      
        for ($i=0; $i<=$request['Row_Count3']; $i++){
            if(isset( $request['udffie_'.$i])){
                $udffield_Data[$i]['UDFID_REF']   = $request['udffie_'.$i]; 
                $udffield_Data[$i]['UDF_VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           } 
        }

        if(count($udffield_Data) > 0 ){
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
        }
        else{
            $XMLUDF = NULL;
        }
        $ACTIONNAME         =   $Approvallevel;
        $DOC_NO             =   $request['DOC_NO'];
        $DOC_DATE           =   $request['DOC_DATE'];
        $CUSTOMER_TYPE      =   $request['CUSTOMER_TYPE'];
        $CUSTOMER_NAME      =   $request['CUSTOMER_NAME'];
        $CUSTOMER_ID        =   $request['CUSTOMER_ID'];
        $DOB                =   $request['DOB'];
        $EMAIL_ID           =   $request['EMAIL_ID'];
        $MOBILE_NO          =   $request['MOBILE_NO'];
        $ADDRESS            =   $request['ADDRESS'];
        $ANNIVERSARY_DATE   =   $request['ANNIVERSARY_DATE'];
        $COUNTRY_ID         =   $request['COUNTRY_ID'];
        $STATE_ID           =   $request['STATE_ID'];
        $CITY_ID            =   $request['CITY_ID'];
        $PINCODE            =   $request['PINCODE'];
        $GST_TYPE           =   $request['GST_TYPE'];
        $GST_IN             =   $request['GST_IN'];
        $LANDLINE_NO        =   $request['LANDLINE_NO'];
        $VEHICLE_REG_NO     =   $request['VEHICLE_REG_NO'];
        $VEHICLE_MAKE_ID    =   $request['VEHICLE_MAKE_ID'];
        $REG_YEAR           =   $request['REG_YEAR'];
        $SUPERVISOR_NAME    =   $request['SUPERVISOR_NAME'];
        $IN_TIME            =   $request['IN_TIME'];
        $OUT_TIME           =   $request['OUT_TIME'];
        $TOTAL              =   $request['TOTAL'];
        $FC = (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
       
        $log_data = [
            $DOC_NO,$DOC_DATE,$CUSTOMER_TYPE,$CUSTOMER_ID,$DOB,
            $EMAIL_ID,$MOBILE_NO,$ADDRESS,$ANNIVERSARY_DATE,$COUNTRY_ID,
            $STATE_ID,$CITY_ID,$PINCODE,$GST_TYPE,$GST_IN,
            $LANDLINE_NO,$VEHICLE_REG_NO,$VEHICLE_MAKE_ID,$REG_YEAR,$SUPERVISOR_NAME,
            $IN_TIME,$OUT_TIME,$TOTAL,$CYID_REF,$BRID_REF,$FYID_REF,
            $XML_DETAILS,$XMLUDF,$VTID_REF,$USERID_REF,Date('Y-m-d'),
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$CUSTOMER_NAME,$FC,$CRID_REF,$CONVFACT
        ];

        $sp_result  =   DB::select('EXEC SP_JOB_ESTIMATION_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  
        
        $contains   =   Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);
        }
        else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
    }

    public function edit($id=NULL){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF'); 
        $FormId         =   $this->form_id;
        $ActionStatus   =   "";
        
        if(!is_null($id)){

            $objRights = $this->getUserRights(['VTID_REF'=>$this->vtid_ref]);

            $GSTdata = ['GSTID','GSTCODE','DESCRIPTIONS'];
            $objGstTypeList       = Helper::getTableData('TBL_MST_GSTTYPE',$GSTdata,NULL, NULL, NULL,'GSTCODE','ASC');

            $HDR            =   DB::select("SELECT 
                                T1.*,
                                CONCAT(T2.CCODE,' - ',T2.NAME) AS CUSTOMER_NAME,
                                CONCAT(T3.CTRYCODE,' - ',T3.NAME) AS COUNTRY_NAME,
                                CONCAT(T4.STCODE,' - ',T4.NAME) AS STATE_NAME,
                                CONCAT(T5.CITYCODE,' - ',T5.NAME) AS CITY_NAME,
                                CONCAT(T6.VM_NO,' - ',T6.VM_NAME) AS VEHICLE_MAKE_NAME,
                                T7.CRDESCRIPTION,T7.CRCODE
                                FROM TBL_TRN_JOB_ESTIMATION_HDR T1
                                LEFT JOIN TBL_MST_CUSTOMER T2 ON T1.CUSTOMER_ID=T2.SLID_REF
                                LEFT JOIN TBL_MST_COUNTRY T3 ON T1.COUNTRY_ID=T3.CTRYID
                                LEFT JOIN TBL_MST_STATE T4 ON T1.STATE_ID=T4.STID
                                LEFT JOIN TBL_MST_CITY T5 ON T1.CITY_ID=T5.CITYID
                                LEFT JOIN TBL_MST_VEHICLE_MASTER T6 ON T1.VEHICLE_MAKE_ID=T6.VM_ID
                                LEFT JOIN TBL_MST_CURRENCY T7 ON T1.CRID_REF=T7.CRID
                                WHERE T1.JEID='$id'
                                ");


                                
            $HDR            =   count($HDR) > 0?$HDR[0]:[];
            $objUdf         =   $this->getUdf(['VTID_REF'=>$this->vtid_ref]);

            $DETAILS        =   DB::select("SELECT
                                T1.*,
                                CONCAT(T2.PKMCODE,' - ',T2.PKMNAME) AS PACKAGE_NAME
                                FROM TBL_TRN_JOB_ESTIMATION_DET T1
                                LEFT JOIN TBL_MST_PACKAGE_MASTER T2 ON T1.PAMID_REF=T2.PKMID
                                WHERE JEID_REF='$id'
                                "); 

            $objtempUdf     =   $objUdf;
            foreach ($objtempUdf as $index => $udfvalue) {

                $objSavedUDF =  DB::table('TBL_TRN_JOB_ESTIMATION_UDF')
                ->where('JEID_REF','=',$id)
                ->where('UDFID_REF','=',$udfvalue->UDFID)
                ->select('UDF_VALUE')
                ->get()->toArray();

                if(!empty($objSavedUDF)){
                    $objUdf[$index]->UDF_VALUE = $objSavedUDF[0]->UDF_VALUE;
                }
                else{
                    $objUdf[$index]->UDF_VALUE = NULL; 
                }
            }

            $objtempUdf     = [];

            $objothcurrency = $this->GetCurrencyMaster(); 

            return view($this->view.'edit',compact(['FormId','objRights','ActionStatus','HDR','DETAILS','objUdf','objGstTypeList','objothcurrency']));      
        }
     
    }
    
    public function view($id=NULL){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF'); 
        $FormId         =   $this->form_id;
        $ActionStatus   =   "disabled";
        
        if(!is_null($id)){

            $objRights = $this->getUserRights(['VTID_REF'=>$this->vtid_ref]);

            $GSTdata = ['GSTID','GSTCODE','DESCRIPTIONS'];
            $objGstTypeList       = Helper::getTableData('TBL_MST_GSTTYPE',$GSTdata,NULL, NULL, NULL,'GSTCODE','ASC');

            $HDR            =   DB::select("SELECT 
                                T1.*,
                                CONCAT(T2.CCODE,' - ',T2.NAME) AS CUSTOMER_NAME,
                                CONCAT(T3.CTRYCODE,' - ',T3.NAME) AS COUNTRY_NAME,
                                CONCAT(T4.STCODE,' - ',T4.NAME) AS STATE_NAME,
                                CONCAT(T5.CITYCODE,' - ',T5.NAME) AS CITY_NAME,
                                CONCAT(T6.VM_NO,' - ',T6.VM_NAME) AS VEHICLE_MAKE_NAME,
                                T7.CRDESCRIPTION,T7.CRCODE
                                FROM TBL_TRN_JOB_ESTIMATION_HDR T1
                                LEFT JOIN TBL_MST_CUSTOMER T2 ON T1.CUSTOMER_ID=T2.SLID_REF
                                LEFT JOIN TBL_MST_COUNTRY T3 ON T1.COUNTRY_ID=T3.CTRYID
                                LEFT JOIN TBL_MST_STATE T4 ON T1.STATE_ID=T4.STID
                                LEFT JOIN TBL_MST_CITY T5 ON T1.CITY_ID=T5.CITYID
                                LEFT JOIN TBL_MST_VEHICLE_MASTER T6 ON T1.VEHICLE_MAKE_ID=T6.VM_ID
                                LEFT JOIN TBL_MST_CURRENCY T7 ON T1.CRID_REF=T7.CRID
                                WHERE T1.JEID='$id'
                                ");

                                
            $HDR            =   count($HDR) > 0?$HDR[0]:[];
            $objUdf         =   $this->getUdf(['VTID_REF'=>$this->vtid_ref]);

            $DETAILS        =   DB::select("SELECT
                                T1.*,
                                CONCAT(T2.PKMCODE,' - ',T2.PKMNAME) AS PACKAGE_NAME
                                FROM TBL_TRN_JOB_ESTIMATION_DET T1
                                LEFT JOIN TBL_MST_PACKAGE_MASTER T2 ON T1.PAMID_REF=T2.PKMID
                                WHERE JEID_REF='$id'
                                "); 

            $objtempUdf     =   $objUdf;
            foreach ($objtempUdf as $index => $udfvalue) {

                $objSavedUDF =  DB::table('TBL_TRN_JOB_ESTIMATION_UDF')
                ->where('JEID_REF','=',$id)
                ->where('UDFID_REF','=',$udfvalue->UDFID)
                ->select('UDF_VALUE')
                ->get()->toArray();

                if(!empty($objSavedUDF)){
                    $objUdf[$index]->UDF_VALUE = $objSavedUDF[0]->UDF_VALUE;
                }
                else{
                    $objUdf[$index]->UDF_VALUE = NULL; 
                }
            }
            $objtempUdf     = [];
            $objothcurrency = $this->GetCurrencyMaster(); 

            return view($this->view.'view',compact(['FormId','objRights','ActionStatus','HDR','DETAILS','objUdf','objGstTypeList','objothcurrency']));      
        }
     
    }

    public function update(Request $request){
 
        $VTID_REF   =   $this->vtid_ref;
        $USERID_REF =   Auth::user()->USERID;   
        $ACTIONNAME =   'EDIT';
        $IPADDRESS  =   $request->getClientIp();
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $sp_Approvallevel = [
            $USERID_REF, $VTID_REF, $CYID_REF,$BRID_REF,
            $FYID_REF
        ];
        
        $sp_listing_result = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?, ?', $sp_Approvallevel);

        if(!empty($sp_listing_result)){
            foreach ($sp_listing_result as $key=>$valueitem){  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$valueitem->LAVELS;
            }
        }


        $ACTIONNAME =   $Approvallevel;

        

        $details  = array();
        if(isset($_REQUEST['PACKAGE_ID']) && !empty($_REQUEST['PACKAGE_ID'])){
            foreach($_REQUEST['PACKAGE_ID'] as $key=>$val){

                $details[] = array(
                'PAMID_REF'     => trim($_REQUEST['PACKAGE_ID'][$key])?trim($_REQUEST['PACKAGE_ID'][$key]):NULL,
                'AMOUNT'        => trim($_REQUEST['AMOUNT'][$key])?trim($_REQUEST['AMOUNT'][$key]):0,
                );
            }
        }

        if(!empty($details)){
            $wrapped_link["DETAIL"] = $details; 
            $XML_DETAILS = ArrayToXml::convert($wrapped_link);
        }
        else{
            $XML_DETAILS = NULL; 
        }

        $udffield_Data  =   [];      
        for ($i=0; $i<=$request['Row_Count3']; $i++){
            if(isset( $request['udffie_'.$i])){
                $udffield_Data[$i]['UDFID_REF']   = $request['udffie_'.$i]; 
                $udffield_Data[$i]['UDF_VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           } 
        }

        if(count($udffield_Data) > 0 ){
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
        }
        else{
            $XMLUDF = NULL;
        }

        $DOC_ID             =   $request['DOC_ID'];
        $DOC_NO             =   $request['DOC_NO'];
        $DOC_DATE           =   $request['DOC_DATE'];
        $CUSTOMER_TYPE      =   $request['CUSTOMER_TYPE'];
        $CUSTOMER_NAME      =   $request['CUSTOMER_NAME'];
        $CUSTOMER_ID        =   $request['CUSTOMER_ID'];
        $DOB                =   $request['DOB'];
        $EMAIL_ID           =   $request['EMAIL_ID'];
        $MOBILE_NO          =   $request['MOBILE_NO'];
        $ADDRESS            =   $request['ADDRESS'];
        $ANNIVERSARY_DATE   =   $request['ANNIVERSARY_DATE'];
        $COUNTRY_ID         =   $request['COUNTRY_ID'];
        $STATE_ID           =   $request['STATE_ID'];
        $CITY_ID            =   $request['CITY_ID'];
        $PINCODE            =   $request['PINCODE'];
        $GST_TYPE           =   $request['GST_TYPE'];
        $GST_IN             =   $request['GST_IN'];
        $LANDLINE_NO        =   $request['LANDLINE_NO'];
        $VEHICLE_REG_NO     =   $request['VEHICLE_REG_NO'];
        $VEHICLE_MAKE_ID    =   $request['VEHICLE_MAKE_ID'];
        $REG_YEAR           =   $request['REG_YEAR'];
        $SUPERVISOR_NAME    =   $request['SUPERVISOR_NAME'];
        $IN_TIME            =   $request['IN_TIME'];
        $OUT_TIME           =   $request['OUT_TIME'];
        $TOTAL              =   $request['TOTAL'];
        $FC = (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
       
        $log_data = [
            $DOC_ID,$DOC_NO,$DOC_DATE,$CUSTOMER_TYPE,$CUSTOMER_ID,$DOB,
            $EMAIL_ID,$MOBILE_NO,$ADDRESS,$ANNIVERSARY_DATE,$COUNTRY_ID,
            $STATE_ID,$CITY_ID,$PINCODE,$GST_TYPE,$GST_IN,
            $LANDLINE_NO,$VEHICLE_REG_NO,$VEHICLE_MAKE_ID,$REG_YEAR,$SUPERVISOR_NAME,
            $IN_TIME,$OUT_TIME,$TOTAL,$CYID_REF,$BRID_REF,$FYID_REF,
            $XML_DETAILS,$XMLUDF,$VTID_REF,$USERID_REF,Date('Y-m-d'),
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$FC,$CRID_REF,$CONVFACT
        ];
        //dd($log_data); 

        $sp_result  =   DB::select('EXEC SP_JOB_ESTIMATION_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $DOC_NO. ' Sucessfully Updated.']);
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

        if(!empty($sp_listing_result)){
            foreach ($sp_listing_result as $key=>$valueitem){  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$valueitem->LAVELS;
            }
        }
   
        $VTID_REF   =   $this->vtid_ref;
        $USERID_REF = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS  = $request->getClientIp();
        $CYID_REF   = Auth::user()->CYID_REF;
        $BRID_REF   = Session::get('BRID_REF');
        $FYID_REF   = Session::get('FYID_REF');

        $details  = array();
        if(isset($_REQUEST['PACKAGE_ID']) && !empty($_REQUEST['PACKAGE_ID'])){
            foreach($_REQUEST['PACKAGE_ID'] as $key=>$val){

                $details[] = array(
                'PAMID_REF'     => trim($_REQUEST['PACKAGE_ID'][$key])?trim($_REQUEST['PACKAGE_ID'][$key]):NULL,
                'AMOUNT'        => trim($_REQUEST['AMOUNT'][$key])?trim($_REQUEST['AMOUNT'][$key]):0,
                );
            }
        }

        if(!empty($details)){
            $wrapped_link["DETAIL"] = $details; 
            $XML_DETAILS = ArrayToXml::convert($wrapped_link);
        }
        else{
            $XML_DETAILS = NULL; 
        }

        $udffield_Data  =   [];      
        for ($i=0; $i<=$request['Row_Count3']; $i++){
            if(isset( $request['udffie_'.$i])){
                $udffield_Data[$i]['UDFID_REF']   = $request['udffie_'.$i]; 
                $udffield_Data[$i]['UDF_VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           } 
        }

        if(count($udffield_Data) > 0 ){
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
        }
        else{
            $XMLUDF = NULL;
        }

        $DOC_ID             =   $request['DOC_ID'];
        $DOC_NO             =   $request['DOC_NO'];
        $DOC_DATE           =   $request['DOC_DATE'];
        $CUSTOMER_TYPE      =   $request['CUSTOMER_TYPE'];
        $CUSTOMER_NAME      =   $request['CUSTOMER_NAME'];
        $CUSTOMER_ID        =   $request['CUSTOMER_ID'];
        $DOB                =   $request['DOB'];
        $EMAIL_ID           =   $request['EMAIL_ID'];
        $MOBILE_NO          =   $request['MOBILE_NO'];
        $ADDRESS            =   $request['ADDRESS'];
        $ANNIVERSARY_DATE   =   $request['ANNIVERSARY_DATE'];
        $COUNTRY_ID         =   $request['COUNTRY_ID'];
        $STATE_ID           =   $request['STATE_ID'];
        $CITY_ID            =   $request['CITY_ID'];
        $PINCODE            =   $request['PINCODE'];
        $GST_TYPE           =   $request['GST_TYPE'];
        $GST_IN             =   $request['GST_IN'];
        $LANDLINE_NO        =   $request['LANDLINE_NO'];
        $VEHICLE_REG_NO     =   $request['VEHICLE_REG_NO'];
        $VEHICLE_MAKE_ID    =   $request['VEHICLE_MAKE_ID'];
        $REG_YEAR           =   $request['REG_YEAR'];
        $SUPERVISOR_NAME    =   $request['SUPERVISOR_NAME'];
        $IN_TIME            =   $request['IN_TIME'];
        $OUT_TIME           =   $request['OUT_TIME'];
        $TOTAL              =   $request['TOTAL'];
        $FC                 =   (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF           =   (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT           =   (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
       
        $log_data = [
            $DOC_ID,$DOC_NO,$DOC_DATE,$CUSTOMER_TYPE,$CUSTOMER_ID,$DOB,
            $EMAIL_ID,$MOBILE_NO,$ADDRESS,$ANNIVERSARY_DATE,$COUNTRY_ID,
            $STATE_ID,$CITY_ID,$PINCODE,$GST_TYPE,$GST_IN,
            $LANDLINE_NO,$VEHICLE_REG_NO,$VEHICLE_MAKE_ID,$REG_YEAR,$SUPERVISOR_NAME,
            $IN_TIME,$OUT_TIME,$TOTAL,$CYID_REF,$BRID_REF,$FYID_REF,
            $XML_DETAILS,$XMLUDF,$VTID_REF,$USERID_REF,Date('Y-m-d'),
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$FC,$CRID_REF,$CONVFACT
        ];
  

        $sp_result  =   DB::select('EXEC SP_JOB_ESTIMATION_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $DOC_NO. ' Sucessfully Approved.']);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
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
        $TABLE      =   "TBL_TRN_JOB_ESTIMATION_HDR";
        $FIELD      =   "JEID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_JOB_ESTIMATION_DET',
            'NT'  => 'TBL_TRN_JOB_ESTIMATION_UDF',
        ];

        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

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

        $FormId = $this->form_id;
        if(!is_null($id)){
            $objMst =   DB::table("TBL_TRN_JOB_ESTIMATION_HDR")
            ->where('JEID','=',$id)
            ->select('*')
            ->first();        

            $objMstVoucherType  =   DB::table("TBL_MST_VOUCHERTYPE")
            ->where('VTID','=',$this->vtid_ref)
            ->select('VTID','VCODE','DESCRIPTIONS','INDATE')
            ->get()
            ->toArray();
                        
            $objAttachments =   DB::table('TBL_MST_ATTACHMENT')                    
            ->where('TBL_MST_ATTACHMENT.VTID_REF','=',$this->vtid_ref)
            ->where('TBL_MST_ATTACHMENT.ATTACH_DOCNO','=',$id)
            ->where('TBL_MST_ATTACHMENT.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_ATTACHMENT.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_ATTACHMENT.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
            ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
            ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
            ->get()->toArray();

            $dirname =   'JobEstimation';
                
            return view($this->view.'attachment',compact(['FormId','objMst','objMstVoucherType','objAttachments','dirname']));
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
        
		$image_path         =   "docs/company".$CYID_REF."/JobEstimation";     
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

                    $filenametostore        =  $VTID.$ATTACH_DOCNO.date('YmdHis')."_".str_replace(' ', '', $filenamewithextension);   

                    if ($uploadedFile->isValid()) {

                        if(in_array($extension,$allow_extnesions)){
                            
                            if($filesize < $allow_size){

                                $filename = $destinationPath."/".$filenametostore;

                                if (!file_exists($filename)) {

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

    public function getCountryMaster(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $data   =   DB::select("SELECT 
        CTRYID AS DATA_ID,
        CTRYCODE AS DATA_CODE,
        NAME AS DATA_DESC
        FROM TBL_MST_COUNTRY 
        WHERE  CYID_REF='$CYID_REF' AND STATUS='A' AND (DEACTIVATED=0 OR DEACTIVATED IS NULL)
        "); 

        return Response::json($data);
    }

    public function getStateMaster(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $COUNTRY_ID =   $_REQUEST['COUNTRY_ID'];

        $data   =   DB::select("SELECT 
        STID AS DATA_ID,
        STCODE AS DATA_CODE,
        NAME AS DATA_DESC
        FROM TBL_MST_STATE 
        WHERE  CYID_REF='$CYID_REF' AND CTRYID_REF='$COUNTRY_ID' AND STATUS='A' AND (DEACTIVATED=0 OR DEACTIVATED IS NULL)
        "); 

        return Response::json($data);
    }

    public function getCityMaster(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $COUNTRY_ID =   $_REQUEST['COUNTRY_ID'];
        $STATE_ID   =   $_REQUEST['STATE_ID'];
        
        $data   =   DB::select("SELECT 
        CITYID AS DATA_ID,
        CITYCODE AS DATA_CODE,
        NAME AS DATA_DESC
        FROM TBL_MST_CITY 
        WHERE  CYID_REF='$CYID_REF' AND CTRYID_REF='$COUNTRY_ID' AND STID_REF='$STATE_ID' AND STATUS='A' AND (DEACTIVATED=0 OR DEACTIVATED IS NULL)
        "); 

        return Response::json($data);
    }

    public function getVehicleMakeMaster(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $data   =   DB::select("SELECT 
        VM_ID AS DATA_ID,
        VM_NO AS DATA_CODE,
        VM_NAME AS DATA_DESC
        FROM TBL_MST_VEHICLE_MASTER 
        WHERE  CYID_REF='$CYID_REF' AND STATUS='A' AND (DEACTIVATED=0 OR DEACTIVATED IS NULL)         
        "); 

        return Response::json($data);
    }

    public function getPackageMaster(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $PRICE      =   "'0.00' AS PRICE";

        $procust_price_list_mst =   DB::select("SELECT TOP 1 PPLM_ID 
        FROM TBL_MST_BRANCH BR
        INNER JOIN TBL_MST_PPLM PLM ON PLM.PLID_REF=BR.PRICE_LEVEL_REF
        WHERE BR.BRID='$BRID_REF' AND PLM.CYID_REF='$CYID_REF' AND PLM.STATUS='A' AND (PLM.DEACTIVATED=0 OR PLM.DEACTIVATED IS NULL)  ORDER BY PPLM_ID DESC
        ");

        if(isset($procust_price_list_mst[0]->PPLM_ID) && $procust_price_list_mst[0]->PPLM_ID !=''){
            $PPLM_ID_REF    =   $procust_price_list_mst[0]->PPLM_ID;
            $PRICE="(SELECT  SUM(PPLM.SALE_PRICE) FROM TBL_MST_PACKAGE_MASTER_MAT PKM INNER JOIN TBL_MST_PPLM_DETAILS PPLM ON PPLM.ITEMID_REF=PKM.ITEMID_REF WHERE PKM.PKMID_REF=T1.PKMID AND PPLM.PPLM_ID_REF='$PPLM_ID_REF') AS PRICE";
        }

        $data       =   DB::select("SELECT 
        T1.PKMID AS DATA_ID,
        T1.PKMCODE AS DATA_CODE,
        T1.PKMNAME AS DATA_DESC,
        $PRICE
        FROM TBL_MST_PACKAGE_MASTER T1 
        WHERE  T1.CYID_REF='$CYID_REF' AND T1.STATUS='A' AND (T1.DEACTIVATED=0 OR T1.DEACTIVATED IS NULL) 
        "); 

        return Response::json($data);
    }
    
    public function searchCustomer(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
              
        $data   =   DB::select("SELECT 
        T1.SLID_REF AS DATA_ID,
        T1.CCODE AS DATA_CODE,
        T1.NAME AS DATA_DESC,
        T1.REGADDL1,
        T1.REGCTRYID_REF AS COUNTRY_ID,
        T1.REGSTID_REF AS STATE_ID,
        T1.REGCITYID_REF AS CITY_ID,
        T1.REGPIN,
        T1.EMAILID,
        T1.PHNO,
        T1.MONO,
        T1.GSTTYPE,
        T1.GSTIN,
        T2.NAME AS COUNTRY_NAME,
        T3.NAME AS STATE_NAME,
        T4.NAME AS CITY_NAME
        FROM TBL_MST_CUSTOMER T1 
        LEFT JOIN TBL_MST_COUNTRY T2 ON T2.CTRYID=T1.REGCTRYID_REF 
        LEFT JOIN TBL_MST_STATE T3 ON T3.STID=T1.REGSTID_REF
        LEFT JOIN TBL_MST_CITY T4 ON T4.CITYID=T1.REGCITYID_REF
        WHERE  T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.TYPE='CUSTOMER'  AND T1.STATUS='A' AND (T1.DEACTIVATED=0 OR T1.DEACTIVATED IS NULL)
        "); 

        return Response::json($data);
    }

    public function get_package_amount(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $PACKAGE_REF =   $_REQUEST['PACKAGE_REF'];

        $data   =   DB::select("SELECT TOP 1 T3.PRICE
        FROM TBL_MST_BRANCH T1 
        INNER JOIN TBL_MST_PACKAGE_PRICE_LIST_HDR T2 ON T1.PRICE_LEVEL_REF=T2.PRICE_LEVEL_REF
        INNER JOIN TBL_MST_PACKAGE_PRICE_LIST_MAT T3 ON T2.PPL_ID=T3.PPL_ID_REF
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID='$BRID_REF' AND T3.PACKAGE_REF='$PACKAGE_REF' AND T2.STATUS='A'
        ");

       echo  $PRICE  =   isset($data[0]->PRICE) && $data[0]->PRICE !=''?$data[0]->PRICE:0;
       echo  $PRICE  =   1542;
   
    }
     
}
