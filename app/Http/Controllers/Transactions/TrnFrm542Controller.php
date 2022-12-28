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

class TrnFrm542Controller extends Controller{

    protected $form_id  =   542;
    protected $vtid_ref =   612;
    protected $view     =   "transactions.sales.ServiceInvoice.trnfrm542";
   
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
        T2.DESCRIPTIONS AS CREATEDBY,
        CU.NAME AS CUSTOMER_NAME    
        FROM TBL_TRN_SERVICE_INVOICE_HDR T1
        LEFT JOIN TBL_MST_USER T2 ON T2.USERID=T1.CREATED_BY
        LEFT JOIN TBL_MST_CUSTOMER CU ON T1.CUSTOMER_ID=CU.SLID_REF
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' ORDER BY SIID DESC");

        return view($this->view,compact(['FormId','objRights','objDataList']));
    }
	
	public function ViewReport($request) 
    {
        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
		
        $SIID      =   $myValue['SIID'];
        $Flag       =   $myValue['Flag'];
        
        
        $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
		$result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/Service_Invoice_Print');
        
        $reportParameters = array(
            'SIID' => $SIID,
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

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $FormId     =   $this->form_id;

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_SERVICE_INVOICE_HDR',
            'HDR_ID'=>'SIID',
            'HDR_DOC_NO'=>'SI_NO',
            'HDR_DOC_DT'=>'SI_DATE',
            'HDR_DOC_TYPE'=>'transaction'
        );

        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req); 
        $objUdf     =   $this->getUdf(['VTID_REF'=>$this->vtid_ref]);
        return view($this->view.'add', compact(['FormId','doc_req','docarray','objUdf']));       
    }


    public function save(Request $request){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $VTID_REF       =   $this->vtid_ref;
        $USERID_REF     =   Auth::user()->USERID;   
        $ACTIONNAME     =   'ADD';
        $IPADDRESS      =   $request->getClientIp();
        
        $REQ_PKG        =   array();
        $XML_PKG        =   NULL;
        if(isset($_REQUEST['PACKAGE_NAME']) && !empty($_REQUEST['PACKAGE_NAME'])){
            foreach($_REQUEST['PACKAGE_NAME'] as $key=>$val){
                if(trim($_REQUEST['PACKAGE_NAME'][$key]) !=''){
                    $REQ_PKG[] = array(
                        'PACKAGE_NAME'     => trim($_REQUEST['PACKAGE_NAME'][$key])?trim($_REQUEST['PACKAGE_NAME'][$key]):NULL,
                        'PACKAGE_ID'     => trim($_REQUEST['PACKAGE_ID'][$key])?trim($_REQUEST['PACKAGE_ID'][$key]):NULL,
                        'AMOUNT'        => trim($_REQUEST['AMOUNT'][$key])?trim($_REQUEST['AMOUNT'][$key]):0
                    );
                }
            }   
        }

        if(!empty($REQ_PKG)){
            $ARR_PKG["PKG"]     =   $REQ_PKG; 
            $XML_PKG        =   ArrayToXml::convert($ARR_PKG);
        }


        $REQ_DIS    =   array();
        $XML_DIS    =   NULL;
        if(isset($_REQUEST['DISCOUNT_NAME']) && !empty($_REQUEST['DISCOUNT_NAME'])){
            foreach($_REQUEST['DISCOUNT_NAME'] as $key=>$val){
                if(trim($_REQUEST['DISCOUNT_NAME'][$key]) !=''){
                    $REQ_DIS[] = array(
                        'DISCOUNT_NAME'     => trim($_REQUEST['DISCOUNT_NAME'][$key])?trim($_REQUEST['DISCOUNT_NAME'][$key]):NULL,
                        'DISID_REF'     => trim($_REQUEST['DISID_REF'][$key])?trim($_REQUEST['DISID_REF'][$key]):NULL,
                        'DISCOUNT_TYPE'     => trim($_REQUEST['DISCOUNT_TYPE'][$key])?trim($_REQUEST['DISCOUNT_TYPE'][$key]):NULL,
                        'DISCOUNT_VALUE'     => trim($_REQUEST['DISCOUNT_VALUE'][$key])?trim($_REQUEST['DISCOUNT_VALUE'][$key]):NULL,
                        'DISCOUNT_AMOUNT'        => trim($_REQUEST['DISCOUNT_AMOUNT'][$key])?trim($_REQUEST['DISCOUNT_AMOUNT'][$key]):0
                    );
                }
            }

        }

        if(!empty($REQ_DIS)){
            $ARR_DIS["DIS"] =   $REQ_DIS; 
            $XML_DIS        =   ArrayToXml::convert($ARR_DIS);
        }


        $REQ_TAX    =   array();
        $XML_TAX    =   NULL;
        if(isset($_REQUEST['TAX_NAME']) && !empty($_REQUEST['TAX_NAME'])){
            foreach($_REQUEST['TAX_NAME'] as $key=>$val){
                if(trim($_REQUEST['TAX_NAME'][$key]) !=''){
                    $REQ_TAX[] = array(
                        'TAX_NAME'     => trim($_REQUEST['TAX_NAME'][$key])?trim($_REQUEST['TAX_NAME'][$key]):NULL,
                        'TAX_PER'        => trim($_REQUEST['TAX_PER'][$key])?trim($_REQUEST['TAX_PER'][$key]):0,
                        'TAX_AMOUNT'        => trim($_REQUEST['TAX_AMOUNT'][$key])?trim($_REQUEST['TAX_AMOUNT'][$key]):0
                    );
                }
            }
        }

        if(!empty($REQ_TAX)){
            $ARR_TAX["TAX"] =   $REQ_TAX; 
            $XML_TAX        =   ArrayToXml::convert($ARR_TAX);
        }


        $REQ_PAY    =   array();
        $XML_PAY    =   NULL;
        if(isset($_REQUEST['PAYMENT_TYPE']) && !empty($_REQUEST['PAYMENT_TYPE'])){
            foreach($_REQUEST['PAYMENT_TYPE'] as $key=>$val){
                if(trim($_REQUEST['PAYMENT_TYPE'][$key]) !=''){
                    $REQ_PAY[] = array(
                        'PAYMENT_TYPE'     => trim($_REQUEST['PAYMENT_TYPE'][$key])?trim($_REQUEST['PAYMENT_TYPE'][$key]):NULL,
                        'DESCRIPTION'     => trim($_REQUEST['DESCRIPTION'][$key])?trim($_REQUEST['DESCRIPTION'][$key]):NULL,
                        'VALUEID_REF'     => trim($_REQUEST['VALUEID_REF'][$key])?trim($_REQUEST['VALUEID_REF'][$key]):NULL,
                        'PAID_AMT'        => trim($_REQUEST['PAID_AMT'][$key])?trim($_REQUEST['PAID_AMT'][$key]):0
                    );
                }
            }  
        }

        if(!empty($REQ_PAY)){
            $ARR_PAY["PAY"] =   $REQ_PAY; 
            $XML_PAY        =   ArrayToXml::convert($ARR_PAY);
        }


        $REQ_UDF    =   array(); 
        $XML_UDF    =   NULL;  
        for ($i=0; $i<=$request['Row_Count3']; $i++){
            if(isset( $request['udffie_'.$i]) && trim($request['udfvalue_'.$i]) !=''){
                $REQ_UDF[$i]['UDFID_REF']   = $request['udffie_'.$i]; 
                $REQ_UDF[$i]['UDF_VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           } 
        }

        if(!empty($REQ_UDF)){
            $ARR_UDF["UDF"] = $REQ_UDF;  
            $XML_UDF        = ArrayToXml::convert($ARR_UDF); 
        }
        

        $DOC_NO                 =   $request['DOC_NO'];
        $DOC_DATE               =   $request['DOC_DATE'];
        $JOB_NO                 =   $request['JOB_NO'];
        $JEID_REF               =   $request['JEID_REF'];
        $JOB_DATE               =   $request['JOB_DATE'];
        $CUSTOMER_NAME          =   $request['CUSTOMER_NAME'];
        $CUSTOMER_ID            =   $request['CUSTOMER_ID'];
        $HSNID_REF              =   $request['HSNID_REF'];
        $MOBILE_NO              =   $request['MOBILE_NO'];
        $ADDRESS                =   $request['ADDRESS'];
        $LANDLINE_NO            =   $request['LANDLINE_NO'];
        $TOTAL_PACKAGE_AMOUNT   =   $request['TOTAL_PACKAGE_AMOUNT'] !=''?$request['TOTAL_PACKAGE_AMOUNT']:0;
        $TOTAL_DISCOUONT_AMOUNT =   $request['TOTAL_DISCOUONT_AMOUNT'] !=''?$request['TOTAL_DISCOUONT_AMOUNT']:0;
        $TOTAL_TAX_AMOUNT       =   $request['TOTAL_TAX_AMOUNT'] !=''?$request['TOTAL_TAX_AMOUNT']:0;
        $TOTAL_NET_AMOUNT       =   $request['TOTAL_NET_AMOUNT'] !=''?$request['TOTAL_NET_AMOUNT']:0;
        $TOTAL_PAID_AMOUNT      =   $request['TOTAL_PAID_AMOUNT'] !=''?$request['TOTAL_PAID_AMOUNT']:0;
       
        $log_data = [
            $DOC_NO,$DOC_DATE,$JOB_NO,$JEID_REF,$JOB_DATE,
            $CUSTOMER_NAME,$CUSTOMER_ID,$HSNID_REF,$MOBILE_NO,$ADDRESS,
            $LANDLINE_NO,$TOTAL_PACKAGE_AMOUNT,$TOTAL_DISCOUONT_AMOUNT,$TOTAL_TAX_AMOUNT,$TOTAL_NET_AMOUNT,
            $TOTAL_PAID_AMOUNT,$XML_PKG,$XML_DIS,$XML_TAX,$XML_PAY,
            $XML_UDF,$CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,
            $USERID_REF,Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];

        $sp_result  =   DB::select('EXEC SP_SERVICE_INVOICE_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $log_data);  
        
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
                                CONCAT(T2.CCODE,' - ',T2.NAME) AS CUSTOMER_NAME
                                FROM TBL_TRN_SERVICE_INVOICE_HDR T1
                                LEFT JOIN TBL_MST_CUSTOMER T2 ON T1.CUSTOMER_ID=T2.SLID_REF
                                WHERE T1.SIID='$id'
                                ");
                  
            $HDR            =   count($HDR) > 0?$HDR[0]:[];
            $PKG            =   DB::select("SELECT * FROM TBL_TRN_SERVICE_INVOICE_PKG WHERE SIID_REF='$id'");
            $DIS            =   DB::select("SELECT * FROM TBL_TRN_SERVICE_INVOICE_DIS WHERE SIID_REF='$id'");
            $TAX            =   DB::select("SELECT * FROM TBL_TRN_SERVICE_INVOICE_TAX WHERE SIID_REF='$id'");
            $PAY            =   DB::select("SELECT * FROM TBL_TRN_SERVICE_INVOICE_PAY WHERE SIID_REF='$id'");


            $objUdf         =   $this->getUdf(['VTID_REF'=>$this->vtid_ref]);
            $objtempUdf     =   $objUdf;
            foreach ($objtempUdf as $index => $udfvalue) {

                $objSavedUDF =  DB::table('TBL_TRN_SERVICE_INVOICE_UDF')
                ->where('SIID_REF','=',$id)
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

            return view($this->view.'edit',compact(['FormId','objRights','ActionStatus','HDR','PKG','DIS','TAX','PAY','objUdf','objGstTypeList']));      
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
                                CONCAT(T2.CCODE,' - ',T2.NAME) AS CUSTOMER_NAME
                                FROM TBL_TRN_SERVICE_INVOICE_HDR T1
                                LEFT JOIN TBL_MST_CUSTOMER T2 ON T1.CUSTOMER_ID=T2.SLID_REF
                                WHERE T1.SIID='$id'
                                ");
                  
            $HDR            =   count($HDR) > 0?$HDR[0]:[];
            $PKG            =   DB::select("SELECT * FROM TBL_TRN_SERVICE_INVOICE_PKG WHERE SIID_REF='$id'");
            $DIS            =   DB::select("SELECT * FROM TBL_TRN_SERVICE_INVOICE_DIS WHERE SIID_REF='$id'");
            $TAX            =   DB::select("SELECT * FROM TBL_TRN_SERVICE_INVOICE_TAX WHERE SIID_REF='$id'");
            $PAY            =   DB::select("SELECT * FROM TBL_TRN_SERVICE_INVOICE_PAY WHERE SIID_REF='$id'");


            $objUdf         =   $this->getUdf(['VTID_REF'=>$this->vtid_ref]);
            $objtempUdf     =   $objUdf;
            foreach ($objtempUdf as $index => $udfvalue) {

                $objSavedUDF =  DB::table('TBL_TRN_SERVICE_INVOICE_UDF')
                ->where('SIID_REF','=',$id)
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

            return view($this->view.'view',compact(['FormId','objRights','ActionStatus','HDR','PKG','DIS','TAX','PAY','objUdf','objGstTypeList']));      
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

        $REQ_PKG        =   array();
        $XML_PKG        =   NULL;
        if(isset($_REQUEST['PACKAGE_NAME']) && !empty($_REQUEST['PACKAGE_NAME'])){
            foreach($_REQUEST['PACKAGE_NAME'] as $key=>$val){
                if(trim($_REQUEST['PACKAGE_NAME'][$key]) !=''){
                    $REQ_PKG[] = array(
                        'PACKAGE_NAME'     => trim($_REQUEST['PACKAGE_NAME'][$key])?trim($_REQUEST['PACKAGE_NAME'][$key]):NULL,
                        'PACKAGE_ID'     => trim($_REQUEST['PACKAGE_ID'][$key])?trim($_REQUEST['PACKAGE_ID'][$key]):NULL,
                        'AMOUNT'        => trim($_REQUEST['AMOUNT'][$key])?trim($_REQUEST['AMOUNT'][$key]):0
                    );
                }
            }   
        }

        if(!empty($REQ_PKG)){
            $ARR_PKG["PKG"]     =   $REQ_PKG; 
            $XML_PKG        =   ArrayToXml::convert($ARR_PKG);
        }


        $REQ_DIS    =   array();
        $XML_DIS    =   NULL;
        if(isset($_REQUEST['DISCOUNT_NAME']) && !empty($_REQUEST['DISCOUNT_NAME'])){
            foreach($_REQUEST['DISCOUNT_NAME'] as $key=>$val){
                if(trim($_REQUEST['DISCOUNT_NAME'][$key]) !=''){
                    $REQ_DIS[] = array(
                        'DISCOUNT_NAME'     => trim($_REQUEST['DISCOUNT_NAME'][$key])?trim($_REQUEST['DISCOUNT_NAME'][$key]):NULL,
                        'DISID_REF'     => trim($_REQUEST['DISID_REF'][$key])?trim($_REQUEST['DISID_REF'][$key]):NULL,
                        'DISCOUNT_TYPE'     => trim($_REQUEST['DISCOUNT_TYPE'][$key])?trim($_REQUEST['DISCOUNT_TYPE'][$key]):NULL,
                        'DISCOUNT_VALUE'     => trim($_REQUEST['DISCOUNT_VALUE'][$key])?trim($_REQUEST['DISCOUNT_VALUE'][$key]):NULL,
                        'DISCOUNT_AMOUNT'        => trim($_REQUEST['DISCOUNT_AMOUNT'][$key])?trim($_REQUEST['DISCOUNT_AMOUNT'][$key]):0
                    );
                }
            }

        }

        if(!empty($REQ_DIS)){
            $ARR_DIS["DIS"] =   $REQ_DIS; 
            $XML_DIS        =   ArrayToXml::convert($ARR_DIS);
        }


        $REQ_TAX    =   array();
        $XML_TAX    =   NULL;
        if(isset($_REQUEST['TAX_NAME']) && !empty($_REQUEST['TAX_NAME'])){
            foreach($_REQUEST['TAX_NAME'] as $key=>$val){
                if(trim($_REQUEST['TAX_NAME'][$key]) !=''){
                    $REQ_TAX[] = array(
                        'TAX_NAME'     => trim($_REQUEST['TAX_NAME'][$key])?trim($_REQUEST['TAX_NAME'][$key]):NULL,
                        'TAX_PER'        => trim($_REQUEST['TAX_PER'][$key])?trim($_REQUEST['TAX_PER'][$key]):0,
                        'TAX_AMOUNT'        => trim($_REQUEST['TAX_AMOUNT'][$key])?trim($_REQUEST['TAX_AMOUNT'][$key]):0
                    );
                }
            }
        }

        if(!empty($REQ_TAX)){
            $ARR_TAX["TAX"] =   $REQ_TAX; 
            $XML_TAX        =   ArrayToXml::convert($ARR_TAX);
        }


        $REQ_PAY    =   array();
        $XML_PAY    =   NULL;
        if(isset($_REQUEST['PAYMENT_TYPE']) && !empty($_REQUEST['PAYMENT_TYPE'])){
            foreach($_REQUEST['PAYMENT_TYPE'] as $key=>$val){
                if(trim($_REQUEST['PAYMENT_TYPE'][$key]) !=''){
                    $REQ_PAY[] = array(
                        'PAYMENT_TYPE'     => trim($_REQUEST['PAYMENT_TYPE'][$key])?trim($_REQUEST['PAYMENT_TYPE'][$key]):NULL,
                        'DESCRIPTION'     => trim($_REQUEST['DESCRIPTION'][$key])?trim($_REQUEST['DESCRIPTION'][$key]):NULL,
                        'VALUEID_REF'     => trim($_REQUEST['VALUEID_REF'][$key])?trim($_REQUEST['VALUEID_REF'][$key]):NULL,
                        'PAID_AMT'        => trim($_REQUEST['PAID_AMT'][$key])?trim($_REQUEST['PAID_AMT'][$key]):0
                    );
                }
            }  
        }

        if(!empty($REQ_PAY)){
            $ARR_PAY["PAY"] =   $REQ_PAY; 
            $XML_PAY        =   ArrayToXml::convert($ARR_PAY);
        }


        $REQ_UDF    =   array(); 
        $XML_UDF    =   NULL;  
        for ($i=0; $i<=$request['Row_Count3']; $i++){
            if(isset( $request['udffie_'.$i]) && trim($request['udfvalue_'.$i]) !=''){
                $REQ_UDF[$i]['UDFID_REF']   = $request['udffie_'.$i]; 
                $REQ_UDF[$i]['UDF_VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           } 
        }

        if(!empty($REQ_UDF)){
            $ARR_UDF["UDF"] = $REQ_UDF;  
            $XML_UDF        = ArrayToXml::convert($ARR_UDF); 
        }

        $DOC_ID                 =   $request['DOC_ID'];
        $DOC_NO                 =   $request['DOC_NO'];
        $DOC_DATE               =   $request['DOC_DATE'];
        $JOB_NO                 =   $request['JOB_NO'];
        $JEID_REF               =   $request['JEID_REF'];
        $JOB_DATE               =   $request['JOB_DATE'];
        $CUSTOMER_NAME          =   $request['CUSTOMER_NAME'];
        $CUSTOMER_ID            =   $request['CUSTOMER_ID'];
        $HSNID_REF              =   $request['HSNID_REF'];
        $MOBILE_NO              =   $request['MOBILE_NO'];
        $ADDRESS                =   $request['ADDRESS'];
        $LANDLINE_NO            =   $request['LANDLINE_NO'];
        $TOTAL_PACKAGE_AMOUNT   =   $request['TOTAL_PACKAGE_AMOUNT'] !=''?$request['TOTAL_PACKAGE_AMOUNT']:0;
        $TOTAL_DISCOUONT_AMOUNT =   $request['TOTAL_DISCOUONT_AMOUNT'] !=''?$request['TOTAL_DISCOUONT_AMOUNT']:0;
        $TOTAL_TAX_AMOUNT       =   $request['TOTAL_TAX_AMOUNT'] !=''?$request['TOTAL_TAX_AMOUNT']:0;
        $TOTAL_NET_AMOUNT       =   $request['TOTAL_NET_AMOUNT'] !=''?$request['TOTAL_NET_AMOUNT']:0;
        $TOTAL_PAID_AMOUNT      =   $request['TOTAL_PAID_AMOUNT'] !=''?$request['TOTAL_PAID_AMOUNT']:0;

        $log_data = [
            $DOC_ID,$DOC_NO,$DOC_DATE,$JOB_NO,$JEID_REF,$JOB_DATE,
            $CUSTOMER_NAME,$CUSTOMER_ID,$HSNID_REF,$MOBILE_NO,$ADDRESS,
            $LANDLINE_NO,$TOTAL_PACKAGE_AMOUNT,$TOTAL_DISCOUONT_AMOUNT,$TOTAL_TAX_AMOUNT,$TOTAL_NET_AMOUNT,
            $TOTAL_PAID_AMOUNT,$XML_PKG,$XML_DIS,$XML_TAX,$XML_PAY,
            $XML_UDF,$CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,
            $USERID_REF,Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];
       
        $sp_result  =   DB::select('EXEC SP_SERVICE_INVOICE_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?', $log_data); 

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

        $REQ_PKG        =   array();
        $XML_PKG        =   NULL;
        if(isset($_REQUEST['PACKAGE_NAME']) && !empty($_REQUEST['PACKAGE_NAME'])){
            foreach($_REQUEST['PACKAGE_NAME'] as $key=>$val){
                if(trim($_REQUEST['PACKAGE_NAME'][$key]) !=''){
                    $REQ_PKG[] = array(
                        'PACKAGE_NAME'     => trim($_REQUEST['PACKAGE_NAME'][$key])?trim($_REQUEST['PACKAGE_NAME'][$key]):NULL,
                        'PACKAGE_ID'     => trim($_REQUEST['PACKAGE_ID'][$key])?trim($_REQUEST['PACKAGE_ID'][$key]):NULL,
                        'AMOUNT'        => trim($_REQUEST['AMOUNT'][$key])?trim($_REQUEST['AMOUNT'][$key]):0
                    );
                }
            }   
        }

        if(!empty($REQ_PKG)){
            $ARR_PKG["PKG"]     =   $REQ_PKG; 
            $XML_PKG        =   ArrayToXml::convert($ARR_PKG);
        }


        $REQ_DIS    =   array();
        $XML_DIS    =   NULL;
        if(isset($_REQUEST['DISCOUNT_NAME']) && !empty($_REQUEST['DISCOUNT_NAME'])){
            foreach($_REQUEST['DISCOUNT_NAME'] as $key=>$val){
                if(trim($_REQUEST['DISCOUNT_NAME'][$key]) !=''){
                    $REQ_DIS[] = array(
                        'DISCOUNT_NAME'     => trim($_REQUEST['DISCOUNT_NAME'][$key])?trim($_REQUEST['DISCOUNT_NAME'][$key]):NULL,
                        'DISID_REF'     => trim($_REQUEST['DISID_REF'][$key])?trim($_REQUEST['DISID_REF'][$key]):NULL,
                        'DISCOUNT_TYPE'     => trim($_REQUEST['DISCOUNT_TYPE'][$key])?trim($_REQUEST['DISCOUNT_TYPE'][$key]):NULL,
                        'DISCOUNT_VALUE'     => trim($_REQUEST['DISCOUNT_VALUE'][$key])?trim($_REQUEST['DISCOUNT_VALUE'][$key]):NULL,
                        'DISCOUNT_AMOUNT'        => trim($_REQUEST['DISCOUNT_AMOUNT'][$key])?trim($_REQUEST['DISCOUNT_AMOUNT'][$key]):0
                    );
                }
            }

        }

        if(!empty($REQ_DIS)){
            $ARR_DIS["DIS"] =   $REQ_DIS; 
            $XML_DIS        =   ArrayToXml::convert($ARR_DIS);
        }


        $REQ_TAX    =   array();
        $XML_TAX    =   NULL;
        if(isset($_REQUEST['TAX_NAME']) && !empty($_REQUEST['TAX_NAME'])){
            foreach($_REQUEST['TAX_NAME'] as $key=>$val){
                if(trim($_REQUEST['TAX_NAME'][$key]) !=''){
                    $REQ_TAX[] = array(
                        'TAX_NAME'     => trim($_REQUEST['TAX_NAME'][$key])?trim($_REQUEST['TAX_NAME'][$key]):NULL,
                        'TAX_PER'        => trim($_REQUEST['TAX_PER'][$key])?trim($_REQUEST['TAX_PER'][$key]):0,
                        'TAX_AMOUNT'        => trim($_REQUEST['TAX_AMOUNT'][$key])?trim($_REQUEST['TAX_AMOUNT'][$key]):0
                    );
                }
            }
        }

        if(!empty($REQ_TAX)){
            $ARR_TAX["TAX"] =   $REQ_TAX; 
            $XML_TAX        =   ArrayToXml::convert($ARR_TAX);
        }


        $REQ_PAY    =   array();
        $XML_PAY    =   NULL;
        if(isset($_REQUEST['PAYMENT_TYPE']) && !empty($_REQUEST['PAYMENT_TYPE'])){
            foreach($_REQUEST['PAYMENT_TYPE'] as $key=>$val){
                if(trim($_REQUEST['PAYMENT_TYPE'][$key]) !=''){
                    $REQ_PAY[] = array(
                        'PAYMENT_TYPE'     => trim($_REQUEST['PAYMENT_TYPE'][$key])?trim($_REQUEST['PAYMENT_TYPE'][$key]):NULL,
                        'DESCRIPTION'     => trim($_REQUEST['DESCRIPTION'][$key])?trim($_REQUEST['DESCRIPTION'][$key]):NULL,
                        'VALUEID_REF'     => trim($_REQUEST['VALUEID_REF'][$key])?trim($_REQUEST['VALUEID_REF'][$key]):NULL,
                        'PAID_AMT'        => trim($_REQUEST['PAID_AMT'][$key])?trim($_REQUEST['PAID_AMT'][$key]):0
                    );
                }
            }  
        }

        if(!empty($REQ_PAY)){
            $ARR_PAY["PAY"] =   $REQ_PAY; 
            $XML_PAY        =   ArrayToXml::convert($ARR_PAY);
        }


        $REQ_UDF    =   array(); 
        $XML_UDF    =   NULL;  
        for ($i=0; $i<=$request['Row_Count3']; $i++){
            if(isset( $request['udffie_'.$i]) && trim($request['udfvalue_'.$i]) !=''){
                $REQ_UDF[$i]['UDFID_REF']   = $request['udffie_'.$i]; 
                $REQ_UDF[$i]['UDF_VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           } 
        }

        if(!empty($REQ_UDF)){
            $ARR_UDF["UDF"] = $REQ_UDF;  
            $XML_UDF        = ArrayToXml::convert($ARR_UDF); 
        }

        $DOC_ID                 =   $request['DOC_ID'];
        $DOC_NO                 =   $request['DOC_NO'];
        $DOC_DATE               =   $request['DOC_DATE'];
        $JOB_NO                 =   $request['JOB_NO'];
        $JEID_REF               =   $request['JEID_REF'];
        $JOB_DATE               =   $request['JOB_DATE'];
        $CUSTOMER_NAME          =   $request['CUSTOMER_NAME'];
        $CUSTOMER_ID            =   $request['CUSTOMER_ID'];
        $HSNID_REF              =   $request['HSNID_REF'];
        $MOBILE_NO              =   $request['MOBILE_NO'];
        $ADDRESS                =   $request['ADDRESS'];
        $LANDLINE_NO            =   $request['LANDLINE_NO'];
        $TOTAL_PACKAGE_AMOUNT   =   $request['TOTAL_PACKAGE_AMOUNT'] !=''?$request['TOTAL_PACKAGE_AMOUNT']:0;
        $TOTAL_DISCOUONT_AMOUNT =   $request['TOTAL_DISCOUONT_AMOUNT'] !=''?$request['TOTAL_DISCOUONT_AMOUNT']:0;
        $TOTAL_TAX_AMOUNT       =   $request['TOTAL_TAX_AMOUNT'] !=''?$request['TOTAL_TAX_AMOUNT']:0;
        $TOTAL_NET_AMOUNT       =   $request['TOTAL_NET_AMOUNT'] !=''?$request['TOTAL_NET_AMOUNT']:0;
        $TOTAL_PAID_AMOUNT      =   $request['TOTAL_PAID_AMOUNT'] !=''?$request['TOTAL_PAID_AMOUNT']:0;

        $log_data = [
            $DOC_ID,$DOC_NO,$DOC_DATE,$JOB_NO,$JEID_REF,$JOB_DATE,
            $CUSTOMER_NAME,$CUSTOMER_ID,$HSNID_REF,$MOBILE_NO,$ADDRESS,
            $LANDLINE_NO,$TOTAL_PACKAGE_AMOUNT,$TOTAL_DISCOUONT_AMOUNT,$TOTAL_TAX_AMOUNT,$TOTAL_NET_AMOUNT,
            $TOTAL_PAID_AMOUNT,$XML_PKG,$XML_DIS,$XML_TAX,$XML_PAY,
            $XML_UDF,$CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,
            $USERID_REF,Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];
       
        $sp_result  =   DB::select('EXEC SP_SERVICE_INVOICE_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?', $log_data); 

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
        $TABLE      =   "TBL_TRN_SERVICE_INVOICE_HDR";
        $FIELD      =   "SIID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_SERVICE_INVOICE_PKG',
            'NT'  => 'TBL_TRN_SERVICE_INVOICE_DIS',
            'NT'  => 'TBL_TRN_SERVICE_INVOICE_TAX',
            'NT'  => 'TBL_TRN_SERVICE_INVOICE_PAY',
            'NT'  => 'TBL_TRN_SERVICE_INVOICE_UDF',
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
            $objMst =   DB::table("TBL_TRN_SERVICE_INVOICE_HDR")
            ->where('SIID','=',$id)
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

            $dirname =   'ServiceInvoice';
                
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
        
		$image_path         =   "docs/company".$CYID_REF."/ServiceInvoice";     
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

    public function getDiscountMaster(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
      
        $data       =   DB::select("SELECT 
        DISID AS DATA_ID,
        DISCODE AS DATA_CODE,
        DESCRIPTION AS DATA_DESC,
        DIS_OPT,
        DIS_PERCENT,
        DIS_AMT
        FROM TBL_MST_DIS 
        WHERE  CYID_REF='$CYID_REF' AND STATUS='A' AND (DEACTIVATED=0 OR DEACTIVATED IS NULL) 
        "); 

        return Response::json($data);
    }
    
    public function getJobCard(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
       
        $data   =   DB::select("SELECT 
        T1.JEID AS DATA_ID,
        T1.JOB_NO AS DATA_CODE,
        T1.JOB_DATE AS DATA_DESC,
        T1.CUSTOMER_ID,
        T1.MOBILE_NO,
        T1.ADDRESS,
        T1.LANDLINE_NO,
        T1.TOTAL,
        CONCAT(T2.CCODE,' - ',T2.NAME) AS CUSTOMER_NAME
        FROM TBL_TRN_JOB_ESTIMATION_HDR T1
        LEFT JOIN TBL_MST_CUSTOMER T2 ON T1.CUSTOMER_ID=T2.SLID_REF
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' AND T1.STATUS='A' 
		AND T1.JEID NOT IN (SELECT JEID_REF FROM TBL_TRN_SERVICE_INVOICE_HDR WHERE CYID_REF = '$CYID_REF' AND BRID_REF = '$BRID_REF' AND FYID_REF='$FYID_REF')
		ORDER BY T1.JEID DESC 
        "); 

        return Response::json($data);
    }

    public function loadPackage(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $JEID_REF   =   $_REQUEST['JEID_REF'];
       
        $data   =   DB::select("SELECT
        T1.*,
        CONCAT(T2.PKMCODE,' - ',T2.PKMNAME) AS PACKAGE_NAME,
        T2.HSNID_REF
        FROM TBL_TRN_JOB_ESTIMATION_DET T1
        LEFT JOIN TBL_MST_PACKAGE_MASTER T2 ON T1.PAMID_REF=T2.PKMID
        WHERE JEID_REF='$JEID_REF'
        ");

        return Response::json($data);
    }

    public function loadTax(){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $CUSTOMER_ID    =   $_REQUEST['CUSTOMER_ID'];
        $HSNID_REF      =   $_REQUEST['HSNID_REF'];

        $CUSTOMER_STATE =   DB::table('TBL_MST_CUSTOMER')->where('SLID_REF','=',$CUSTOMER_ID)->select('REGSTID_REF')->first();
        $BRANCH_STATE   =   DB::table('TBL_MST_BRANCH')->where('BRID','=',$BRID_REF)->select('STID_REF')->first();
        $WHERE_STATE    =   $CUSTOMER_STATE->REGSTID_REF === $BRANCH_STATE->STID_REF?"AND T2.WITHINSTATE='1'":"AND T2.OUTOFSTATE='1'";

        $data   =   DB::select("SELECT 
        T1.NRATE AS TAX_RATE,
        T2.TAX_TYPE
        FROM TBL_MST_HSNNORMAL T1
        INNER JOIN TBL_MST_TAXTYPE T2 ON T2.TAXID=T1.TAXID_REF AND T2.FOR_SALE='1'
        WHERE T1.HSNID_REF='$HSNID_REF' $WHERE_STATE 
        "); 

        return Response::json($data);
    }

    public function getValueCardMaster(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $DOC_DATE       =   $request['DOC_DATE'];  
        $CUSTOMER_ID    =   $request['CUSTOMER_ID'];  
     
        $data   =   DB::select("SELECT HS.VALIDITY_FROM ,HS.VALIDITY_TO ,D.DETAIL_ID AS DATA_ID,D.CARD_NO AS DATA_CODE,ISNULL(HS.CURRENT_BALANCE,0) AS DATA_DESC  
        FROM TBL_MST_V_MASTER_DETAILS D
        LEFT JOIN TBL_MST_V_MASTER H ON H.DOC_ID=D.DOC_ID_REF
        LEFT JOIN TBL_TRN_VALUECARD_SALE_HDR HS ON D.DETAIL_ID=HS.CARDID_REF
        WHERE HS.CUSTOMER_ID='$CUSTOMER_ID' AND ('$DOC_DATE' BETWEEN HS.VALIDITY_FROM AND HS.VALIDITY_TO) 
        AND H.CYID_REF='$CYID_REF' AND H.FRANCHISE_ID='$BRID_REF' 
        AND H.STATUS='A' 
        AND (H.DEACTIVATED=0 OR H.DEACTIVATED IS NULL)        
        "); 

        return Response::json($data);
    }


    public function Get_Card_Balance(Request $request) { 
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $CARDID_REF     =   $request['CARDID_REF'];  

        $Balance =DB::select("SELECT D.DETAIL_ID AS DATA_ID,D.CARD_NO AS DATA_CODE,ISNULL(HS.CURRENT_BALANCE,0) AS DATA_DESC 
        FROM TBL_MST_V_MASTER_DETAILS D 
        LEFT JOIN TBL_MST_V_MASTER H ON H.DOC_ID=D.DOC_ID_REF 
        LEFT JOIN TBL_TRN_VALUECARD_SALE_HDR HS ON D.DETAIL_ID=HS.CARDID_REF 
        WHERE D.DETAIL_ID = '$CARDID_REF' AND H.CYID_REF='$CYID_REF' AND H.FRANCHISE_ID='$BRID_REF' AND H.STATUS='A' AND (H.DEACTIVATED=0 OR H.DEACTIVATED IS NULL)
        "); 

        $Current_Balance=isset($Balance[0]->DATA_DESC) ? $Balance[0]->DATA_DESC:0;

        echo $Current_Balance;           
                   
    }
    
     
}
