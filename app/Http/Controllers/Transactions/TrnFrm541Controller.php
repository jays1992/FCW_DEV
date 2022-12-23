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

class TrnFrm541Controller extends Controller{

    protected $form_id  =   541;
    protected $vtid_ref =   611;
    protected $view     =   "transactions.Sales.AccessoryInvoice.trnfrm541";
   
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
        FROM TBL_TRN_ACCESSORY_INVOICE_HDR T1
        LEFT JOIN TBL_MST_USER T2 ON T2.USERID=T1.CREATED_BY
        LEFT JOIN TBL_MST_CUSTOMER CU ON T1.CUSTOMER_ID=CU.SLID_REF
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' ORDER BY ACCID DESC");
        //dd($objDataList); 

        return view($this->view,compact(['FormId','objRights','objDataList']));
    }

    public function add(){

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $FormId     =   $this->form_id;

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_ACCESSORY_INVOICE_HDR',
            'HDR_ID'=>'ACCID',
            'HDR_DOC_NO'=>'DOC_NO',
            'HDR_DOC_DT'=>date('Y-m-d'),
            'HDR_DOC_TYPE'=>'transaction'
        );

        $country_state_city =   $this->country_state_city();

        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req); 
        $objUdf     =   $this->getUdf(['VTID_REF'=>$this->vtid_ref]);

        $GSTdata = ['GSTID','GSTCODE','DESCRIPTIONS'];
        $objGstTypeList       = Helper::getTableData('TBL_MST_GSTTYPE',$GSTdata,NULL, NULL, NULL,'GSTCODE','ASC');


        $objlastdt  =   DB::select('SELECT MAX(DOC_DATE) DOC_DATE FROM TBL_TRN_ACCESSORY_INVOICE_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);

        

        return view($this->view.'add', compact(['FormId','doc_req','docarray','objUdf','objGstTypeList','country_state_city','objlastdt']));       
    }
	
	public function ViewReport($request) 
    {
        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
		
        $ACCID      =   $myValue['ACCID'];
        $Flag       =   $myValue['Flag'];
        
        
        $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
		$result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/Accessory_Invoice_Print');
        
        $reportParameters = array(
            'ACCID' => $ACCID,
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


    public function save(Request $request){

        $VTID_REF       =   $this->vtid_ref;
        $USERID_REF     =   Auth::user()->USERID;   
        $ACTIONNAME     =   'ADD';
        $IPADDRESS      =   $request->getClientIp();
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $details  = array();
        if(isset($_REQUEST['ITEMID_REF']) && !empty($_REQUEST['ITEMID_REF'])){
            foreach($_REQUEST['ITEMID_REF'] as $key=>$val){

                $details[] = array(
                'ITEMID_REF'        => trim($_REQUEST['ITEMID_REF'][$key])?trim($_REQUEST['ITEMID_REF'][$key]):NULL,
                'UOMID_REF'         => trim($_REQUEST['MAIN_UOMID_REF'][$key])?trim($_REQUEST['MAIN_UOMID_REF'][$key]):NULL,
                'QTY'               => trim($_REQUEST['QTY'][$key])?trim($_REQUEST['QTY'][$key]):0,
                'RATEPUOM'          => $_REQUEST['RATEPUOM'][$key],
                'DISCPER'           => (!empty($_REQUEST['DISCPER'][$key])) == 'true' ? $_REQUEST['DISCPER'][$key] : 0,
                'DISCOUNT_AMT'      => (!empty($_REQUEST['DISCOUNT_AMT'][$key])) == 'true' ? $_REQUEST['DISCOUNT_AMT'][$key] : 0,
                'IGST'              => (!empty($_REQUEST['IGST'][$key]) ? $_REQUEST['IGST'][$key] : 0),
                'CGST'              => (!empty($_REQUEST['CGST'][$key]) ? $_REQUEST['CGST'][$key] : 0),
                'SGST'              => (!empty($_REQUEST['SGST'][$key]) ? $_REQUEST['SGST'][$key] : 0),                
                'CGSTAMT'           => (!empty($_REQUEST['CGSTAMT'][$key]) ? $_REQUEST['CGSTAMT'][$key] : 0),
                'SGSTAMT'           => (!empty($_REQUEST['SGSTAMT'][$key]) ? $_REQUEST['SGSTAMT'][$key] : 0),
                'IGSTAMT'           => (!empty($_REQUEST['IGSTAMT'][$key]) ? $_REQUEST['IGSTAMT'][$key] : 0),                
                'GROSS_TOTAL'       => (!empty($_REQUEST['DISAFTT_AMT'][$key]) ? $_REQUEST['DISAFTT_AMT'][$key] : 0),
                'TGST_AMT'          => (!empty($_REQUEST['TGST_AMT'][$key]) ? $_REQUEST['TGST_AMT'][$key] : 0),
                'TOT_AMT'           => (!empty($_REQUEST['TOT_AMT'][$key]) ? $_REQUEST['TOT_AMT'][$key] : 0),
                );
            }
        }


        if(!empty($details)){
            $wrapped_link["MAT"] = $details; 
            $XML_DETAILS = ArrayToXml::convert($wrapped_link);
        }
        else{
            $XML_DETAILS = NULL; 
        }



        $payment  = array();

        if(isset($_REQUEST['PAYMENT_TYPE']) && !empty($_REQUEST['PAYMENT_TYPE'])){
            foreach($_REQUEST['PAYMENT_TYPE'] as $key=>$val){
                $payment[] = array(
                'PAYMENT_MODE'      => trim($_REQUEST['PAYMENT_TYPE'][$key])?trim($_REQUEST['PAYMENT_TYPE'][$key]):NULL,
                'DESCRIPTION'       => trim($_REQUEST['DESCRIPTION'][$key])?trim($_REQUEST['DESCRIPTION'][$key]):NULL,
                'VCID_REF'          => $_REQUEST['VALUEID_REF'][$key],
                'AMOUNT'            => (!empty($_REQUEST['PAID_AMT'][$key]) ? $_REQUEST['PAID_AMT'][$key] : 0),                
                );
            }
        }


        if(!empty($payment)){
            $wrapped_link2["PAYMENT"] = $payment; 
            $XML_PAYMENT = ArrayToXml::convert($wrapped_link2);
        }
        else{
            $XML_PAYMENT = NULL; 
        }


   


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
        $TAX_AMT            =   $request['TotalValue_tax'];
        $NET_AMT            =   $request['TotalValue'];
        $PAID_AMT           =   $request['TotalValue_paid'];
        $CUSTOMER_NAME      =   $request['CUSTOMER_NAME'];

        $log_data = [
            $DOC_NO,$DOC_DATE,$CUSTOMER_TYPE,$CUSTOMER_ID,$DOB,
            $EMAIL_ID,$MOBILE_NO,$ADDRESS,$ANNIVERSARY_DATE,$COUNTRY_ID,
            $STATE_ID,$CITY_ID,$PINCODE,$GST_TYPE,$GST_IN,
            $LANDLINE_NO,$VEHICLE_REG_NO,$VEHICLE_MAKE_ID,$TAX_AMT,$NET_AMT,$PAID_AMT,$CYID_REF,$BRID_REF,$FYID_REF,
            $XML_DETAILS,$XML_PAYMENT,$VTID_REF,$USERID_REF,Date('Y-m-d'),
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$CUSTOMER_NAME
        ];

        //dd($log_data); 
        $sp_result  =   DB::select('EXEC SP_ACCESSORY_INVOICE_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  
        //dd($sp_result); 
        $contains   =   Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);
        }
        else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
    }

    public function view($id=NULL){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF'); 
        $FormId         =   $this->form_id;
        $ActionStatus   =   "disabled";
        
        if(!is_null($id)){

            $id        =   urldecode(base64_decode($id));

            $objRights = $this->getUserRights(['VTID_REF'=>$this->vtid_ref]);

           
            $GSTdata = ['GSTID','GSTCODE','DESCRIPTIONS'];
            $objGstTypeList       = Helper::getTableData('TBL_MST_GSTTYPE',$GSTdata,NULL, NULL, NULL,'GSTCODE','ASC');

            
      
             $HDR            =   DB::select("SELECT 
                                T1.*,
								VM.VM_NO AS VEHICLE_MAKE_NAME,
								CONCAT(C.CTRYCODE,' - ',C.NAME) AS COUNTRY_NAME,
								CONCAT(S.STCODE,' - ',S.NAME) AS STATE_NAME,
								CONCAT(CT.CITYCODE,' - ',CT.NAME) AS CITY_NAME,
								CONCAT(CUST.CCODE,' - ',CUST.NAME) AS CUSTOMER_NAME
                                FROM TBL_TRN_ACCESSORY_INVOICE_HDR T1
								LEFT JOIN TBL_MST_VEHICLE_MASTER VM ON VM.VM_ID=T1.VEHICLE_MAKE_ID
								LEFT JOIN TBL_MST_COUNTRY C ON C.CTRYID=T1.COUNTRY_ID
								LEFT JOIN TBL_MST_STATE S ON S.STID=T1.STATE_ID
								LEFT JOIN TBL_MST_CITY CT ON CT.CITYID=T1.CITY_ID
								LEFT JOIN TBL_MST_CUSTOMER CUST ON CUST.SLID_REF=T1.CUSTOMER_ID
                               WHERE T1.ACCID='$id'"
                               );

              
                            

            $HDR            =   count($HDR) > 0?$HDR[0]:[];
    
            $MAT        =   DB::select("SELECT 
                                T1.*,
                                CONCAT(T2.UOMCODE,' - ',T2.DESCRIPTIONS) AS UOM_DESC,
                                T3.ICODE AS ITEM_CODE,
                                T3.NAME AS ITEM_NAME
                                FROM TBL_TRN_ACCESSORY_INVOICE_MAT T1 
                                LEFT JOIN TBL_MST_UOM T2 ON T2.UOMID=T1.UOMID_REF
                                LEFT JOIN TBL_MST_ITEM T3 ON T3.ITEMID=T1.ITEMID_REF
                                WHERE ACCID_REF='$id'
                                "); 

                          

            $PAYMENT        =   DB::select("SELECT 
                                T1.*
                                FROM TBL_TRN_ACCESSORY_INVOICE_PAYMENT T1 
                                WHERE ACCID_REF='$id'
                                "); 

           // dd($MAT); 
         

            return view($this->view.'view',compact(['FormId','objRights','ActionStatus','HDR','MAT','PAYMENT','objGstTypeList']));      
        }
     
    }
    
    public function edit($id=NULL){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF'); 
        $FormId         =   $this->form_id;
        $ActionStatus   =   "";
        
        if(!is_null($id)){

            $id        =   urldecode(base64_decode($id));

            $objRights = $this->getUserRights(['VTID_REF'=>$this->vtid_ref]);

           
            $GSTdata = ['GSTID','GSTCODE','DESCRIPTIONS'];
            $objGstTypeList       = Helper::getTableData('TBL_MST_GSTTYPE',$GSTdata,NULL, NULL, NULL,'GSTCODE','ASC');

            
      
             $HDR            =   DB::select("SELECT 
                                T1.*,
								VM.VM_NO AS VEHICLE_MAKE_NAME,
								CONCAT(C.CTRYCODE,' - ',C.NAME) AS COUNTRY_NAME,
								CONCAT(S.STCODE,' - ',S.NAME) AS STATE_NAME,
								CONCAT(CT.CITYCODE,' - ',CT.NAME) AS CITY_NAME,
								CONCAT(CUST.CCODE,' - ',CUST.NAME) AS CUSTOMER_NAME
                                FROM TBL_TRN_ACCESSORY_INVOICE_HDR T1
								LEFT JOIN TBL_MST_VEHICLE_MASTER VM ON VM.VM_ID=T1.VEHICLE_MAKE_ID
								LEFT JOIN TBL_MST_COUNTRY C ON C.CTRYID=T1.COUNTRY_ID
								LEFT JOIN TBL_MST_STATE S ON S.STID=T1.STATE_ID
								LEFT JOIN TBL_MST_CITY CT ON CT.CITYID=T1.CITY_ID
								LEFT JOIN TBL_MST_CUSTOMER CUST ON CUST.SLID_REF=T1.CUSTOMER_ID
                               WHERE T1.ACCID='$id'"
                               );

              
                            

            $HDR            =   count($HDR) > 0?$HDR[0]:[];
    
            $MAT        =   DB::select("SELECT 
                                T1.*,
                                CONCAT(T2.UOMCODE,' - ',T2.DESCRIPTIONS) AS UOM_DESC,
                                T3.ICODE AS ITEM_CODE,
                                T3.NAME AS ITEM_NAME
                                FROM TBL_TRN_ACCESSORY_INVOICE_MAT T1 
                                LEFT JOIN TBL_MST_UOM T2 ON T2.UOMID=T1.UOMID_REF
                                LEFT JOIN TBL_MST_ITEM T3 ON T3.ITEMID=T1.ITEMID_REF
                                WHERE ACCID_REF='$id'
                                "); 

                          

            $PAYMENT        =   DB::select("SELECT 
                                T1.*
                                FROM TBL_TRN_ACCESSORY_INVOICE_PAYMENT T1 
                                WHERE ACCID_REF='$id'
                                "); 

           // dd($MAT); 
           $objlastdt  =   DB::select('SELECT MAX(DOC_DATE) DOC_DATE FROM TBL_TRN_ACCESSORY_INVOICE_HDR  
           WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
           [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);

            return view($this->view.'edit',compact(['FormId','objRights','ActionStatus','HDR','MAT','PAYMENT','objGstTypeList','objlastdt']));      
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

        $details  = array();
        if(isset($_REQUEST['ITEMID_REF']) && !empty($_REQUEST['ITEMID_REF'])){
            foreach($_REQUEST['ITEMID_REF'] as $key=>$val){

                $details[] = array(
                'ITEMID_REF'        => trim($_REQUEST['ITEMID_REF'][$key])?trim($_REQUEST['ITEMID_REF'][$key]):NULL,
                'UOMID_REF'         => trim($_REQUEST['MAIN_UOMID_REF'][$key])?trim($_REQUEST['MAIN_UOMID_REF'][$key]):NULL,
                'QTY'               => trim($_REQUEST['QTY'][$key])?trim($_REQUEST['QTY'][$key]):0,
                'RATEPUOM'          => $_REQUEST['RATEPUOM'][$key],
                'DISCPER'           => (!empty($_REQUEST['DISCPER'][$key])) == 'true' ? $_REQUEST['DISCPER'][$key] : 0,
                'DISCOUNT_AMT'      => (!empty($_REQUEST['DISCOUNT_AMT'][$key])) == 'true' ? $_REQUEST['DISCOUNT_AMT'][$key] : 0,
                'IGST'              => (!empty($_REQUEST['IGST'][$key]) ? $_REQUEST['IGST'][$key] : 0),
                'CGST'              => (!empty($_REQUEST['CGST'][$key]) ? $_REQUEST['CGST'][$key] : 0),
                'SGST'              => (!empty($_REQUEST['SGST'][$key]) ? $_REQUEST['SGST'][$key] : 0),                
                'CGSTAMT'           => (!empty($_REQUEST['CGSTAMT'][$key]) ? $_REQUEST['CGSTAMT'][$key] : 0),
                'SGSTAMT'           => (!empty($_REQUEST['SGSTAMT'][$key]) ? $_REQUEST['SGSTAMT'][$key] : 0),
                'IGSTAMT'           => (!empty($_REQUEST['IGSTAMT'][$key]) ? $_REQUEST['IGSTAMT'][$key] : 0),                
                'GROSS_TOTAL'       => (!empty($_REQUEST['DISAFTT_AMT'][$key]) ? $_REQUEST['DISAFTT_AMT'][$key] : 0),
                'TGST_AMT'          => (!empty($_REQUEST['TGST_AMT'][$key]) ? $_REQUEST['TGST_AMT'][$key] : 0),
                'TOT_AMT'           => (!empty($_REQUEST['TOT_AMT'][$key]) ? $_REQUEST['TOT_AMT'][$key] : 0),
                );
            }
        }


        if(!empty($details)){
            $wrapped_link["MAT"] = $details; 
            $XML_DETAILS = ArrayToXml::convert($wrapped_link);
        }
        else{
            $XML_DETAILS = NULL; 
        }



        $payment  = array();

        if(isset($_REQUEST['PAYMENT_TYPE']) && !empty($_REQUEST['PAYMENT_TYPE'])){
            foreach($_REQUEST['PAYMENT_TYPE'] as $key=>$val){
                $payment[] = array(
                'PAYMENT_MODE'      => trim($_REQUEST['PAYMENT_TYPE'][$key])?trim($_REQUEST['PAYMENT_TYPE'][$key]):NULL,
                'DESCRIPTION'       => trim($_REQUEST['DESCRIPTION'][$key])?trim($_REQUEST['DESCRIPTION'][$key]):NULL,
                'VCID_REF'          => $_REQUEST['VALUEID_REF'][$key],
                'AMOUNT'            => (!empty($_REQUEST['PAID_AMT'][$key]) ? $_REQUEST['PAID_AMT'][$key] : 0),                
                );
            }
        }


        if(!empty($payment)){
            $wrapped_link2["PAYMENT"] = $payment; 
            $XML_PAYMENT = ArrayToXml::convert($wrapped_link2);
        }
        else{
            $XML_PAYMENT = NULL; 
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
        $TAX_AMT            =   $request['TotalValue_tax'];
        $NET_AMT            =   $request['TotalValue'];
        $PAID_AMT           =   $request['TotalValue_paid'];

        $log_data = [
            $DOC_ID,$DOC_NO,$DOC_DATE,$CUSTOMER_TYPE,$CUSTOMER_ID,$DOB,
            $EMAIL_ID,$MOBILE_NO,$ADDRESS,$ANNIVERSARY_DATE,$COUNTRY_ID,
            $STATE_ID,$CITY_ID,$PINCODE,$GST_TYPE,$GST_IN,
            $LANDLINE_NO,$VEHICLE_REG_NO,$VEHICLE_MAKE_ID,$TAX_AMT,$NET_AMT,$PAID_AMT,$CYID_REF,$BRID_REF,$FYID_REF,
            $XML_DETAILS,$XML_PAYMENT,$VTID_REF,$USERID_REF,Date('Y-m-d'),
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];

        //dd($log_data); 
        $sp_result  =   DB::select('EXEC SP_ACCESSORY_INVOICE_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  

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
        if(isset($_REQUEST['ITEMID_REF']) && !empty($_REQUEST['ITEMID_REF'])){
            foreach($_REQUEST['ITEMID_REF'] as $key=>$val){

                $details[] = array(
                'ITEMID_REF'        => trim($_REQUEST['ITEMID_REF'][$key])?trim($_REQUEST['ITEMID_REF'][$key]):NULL,
                'UOMID_REF'         => trim($_REQUEST['MAIN_UOMID_REF'][$key])?trim($_REQUEST['MAIN_UOMID_REF'][$key]):NULL,
                'QTY'               => trim($_REQUEST['QTY'][$key])?trim($_REQUEST['QTY'][$key]):0,
                'RATEPUOM'          => $_REQUEST['RATEPUOM'][$key],
                'DISCPER'           => (!empty($_REQUEST['DISCPER'][$key])) == 'true' ? $_REQUEST['DISCPER'][$key] : 0,
                'DISCOUNT_AMT'      => (!empty($_REQUEST['DISCOUNT_AMT'][$key])) == 'true' ? $_REQUEST['DISCOUNT_AMT'][$key] : 0,
                'IGST'              => (!empty($_REQUEST['IGST'][$key]) ? $_REQUEST['IGST'][$key] : 0),
                'CGST'              => (!empty($_REQUEST['CGST'][$key]) ? $_REQUEST['CGST'][$key] : 0),
                'SGST'              => (!empty($_REQUEST['SGST'][$key]) ? $_REQUEST['SGST'][$key] : 0),                
                'CGSTAMT'           => (!empty($_REQUEST['CGSTAMT'][$key]) ? $_REQUEST['CGSTAMT'][$key] : 0),
                'SGSTAMT'           => (!empty($_REQUEST['SGSTAMT'][$key]) ? $_REQUEST['SGSTAMT'][$key] : 0),
                'IGSTAMT'           => (!empty($_REQUEST['IGSTAMT'][$key]) ? $_REQUEST['IGSTAMT'][$key] : 0),                
                'GROSS_TOTAL'       => (!empty($_REQUEST['DISAFTT_AMT'][$key]) ? $_REQUEST['DISAFTT_AMT'][$key] : 0),
                'TGST_AMT'          => (!empty($_REQUEST['TGST_AMT'][$key]) ? $_REQUEST['TGST_AMT'][$key] : 0),
                'TOT_AMT'           => (!empty($_REQUEST['TOT_AMT'][$key]) ? $_REQUEST['TOT_AMT'][$key] : 0),
                );
            }
        }


        if(!empty($details)){
            $wrapped_link["MAT"] = $details; 
            $XML_DETAILS = ArrayToXml::convert($wrapped_link);
        }
        else{
            $XML_DETAILS = NULL; 
        }



        $payment  = array();

        if(isset($_REQUEST['PAYMENT_TYPE']) && !empty($_REQUEST['PAYMENT_TYPE'])){
            foreach($_REQUEST['PAYMENT_TYPE'] as $key=>$val){
                $payment[] = array(
                'PAYMENT_MODE'      => trim($_REQUEST['PAYMENT_TYPE'][$key])?trim($_REQUEST['PAYMENT_TYPE'][$key]):NULL,
                'DESCRIPTION'       => trim($_REQUEST['DESCRIPTION'][$key])?trim($_REQUEST['DESCRIPTION'][$key]):NULL,
                'VCID_REF'          => $_REQUEST['VALUEID_REF'][$key],
                'AMOUNT'            => (!empty($_REQUEST['PAID_AMT'][$key]) ? $_REQUEST['PAID_AMT'][$key] : 0),                
                );
            }
        }


        if(!empty($payment)){
            $wrapped_link2["PAYMENT"] = $payment; 
            $XML_PAYMENT = ArrayToXml::convert($wrapped_link2);
        }
        else{
            $XML_PAYMENT = NULL; 
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
        $TAX_AMT            =   $request['TotalValue_tax'];
        $NET_AMT            =   $request['TotalValue'];
        $PAID_AMT           =   $request['TotalValue_paid'];

        $log_data = [
            $DOC_ID,$DOC_NO,$DOC_DATE,$CUSTOMER_TYPE,$CUSTOMER_ID,$DOB,
            $EMAIL_ID,$MOBILE_NO,$ADDRESS,$ANNIVERSARY_DATE,$COUNTRY_ID,
            $STATE_ID,$CITY_ID,$PINCODE,$GST_TYPE,$GST_IN,
            $LANDLINE_NO,$VEHICLE_REG_NO,$VEHICLE_MAKE_ID,$TAX_AMT,$NET_AMT,$PAID_AMT,$CYID_REF,$BRID_REF,$FYID_REF,
            $XML_DETAILS,$XML_PAYMENT,$VTID_REF,$USERID_REF,Date('Y-m-d'),
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];

        //dd($log_data); 
        $sp_result  =   DB::select('EXEC SP_ACCESSORY_INVOICE_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data); 

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $DOC_NO. ' Sucessfully Approved.']);

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

        if(!empty($sp_listing_result)){
            foreach ($sp_listing_result as $key=>$valueitem){  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$valueitem->LAVELS;
            }
        }
               
        $req_data =  json_decode($request['ID']);

        $wrapped_links = $req_data; 
        $multi_array = $wrapped_links;
        $iddata = [];
        
        foreach($multi_array as $index=>$row){
            $m_array[$index] = $row->ID;
            $iddata['APPROVAL'][]['ID'] =  $row->ID;
        }

        $xml = ArrayToXml::convert($iddata);
                
        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_TRN_ACCESSORY_INVOICE_HDR";
        $FIELD      =   "ACCID";
        $ACTIONNAME = $Approvallevel;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
            
        $log_data = [ 
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_MST_MULTIAPPROVAL ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
        
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

        $id         =   $request->{0};    
        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_TRN_ACCESSORY_INVOICE_HDR";
        $FIELD      =   "ACCID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_ACCESSORY_INVOICE_HDR',
            'NT'  => 'TBL_TRN_ACCESSORY_INVOICE_HDR_UDF',
        ];
      
        $wrapped_links["TABLES"] = $req_data; 
        
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$XMLTAB];

        $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_ACCESSORY_INVOICE_HDR')->where('ACCID','=',$id)->first();

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

            $dirname =   'AccessoryInvoice';

            return view($this->view.'attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments','dirname']));
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
        
		$image_path         =   "docs/company".$CYID_REF."/AccessoryInvoice";     
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
        AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF' 
        AND H.FYID_REF='$FYID_REF' AND H.STATUS='A' 
        AND (H.DEACTIVATED=0 OR H.DEACTIVATED IS NULL)        
        "); 

        return Response::json($data);
    }

    public function getSacMaster(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $data   =   DB::select("SELECT 
        HSNID AS DATA_ID,
        HSNCODE AS DATA_CODE,
        HSNDESCRIPTION AS DATA_DESC
        FROM TBL_MST_HSN 
        WHERE  CYID_REF='$CYID_REF' AND STATUS='A' AND (DEACTIVATED=0 OR DEACTIVATED IS NULL)"); 

        return Response::json($data);
    }

    public function loadItem(Request $request){
        
        return $this->loadItemMaster($request);
    } 

    public function getUomMaster(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $ITEMID_REF  =   $_REQUEST['ITEMID_REF'];

        $data       =   DB::select("SELECT 
        UOMID AS DATA_ID,
        UOMCODE AS DATA_CODE,
        DESCRIPTIONS AS DATA_DESC
        FROM TBL_MST_UOM 
        WHERE CYID_REF='6' AND (UOMID IN (SELECT FROM_UOMID_REF FROM TBL_MST_ITEM_UOMCONV WHERE ITEMID_REF = '$ITEMID_REF') OR
        UOMID IN (SELECT TO_UOMID_REF FROM TBL_MST_ITEM_UOMCONV WHERE ITEMID_REF = '$ITEMID_REF'))
        "); 

        return Response::json($data);
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
        WHERE  T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.TYPE='CUSTOMER' AND T1.STATUS='A' AND (T1.DEACTIVATED=0 OR T1.DEACTIVATED IS NULL)
        "); 

        return Response::json($data);
    }


    public function Get_Card_Balance(Request $request) { 
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $CARDID_REF     =   $request['CARDID_REF'];  
      
        $Balance =DB::select("SELECT D.DETAIL_ID AS DATA_ID,D.CARD_NO AS DATA_CODE,ISNULL(HS.CURRENT_BALANCE,0) AS DATA_DESC  FROM TBL_MST_V_MASTER_DETAILS D
        LEFT JOIN TBL_MST_V_MASTER H ON H.DOC_ID=D.DOC_ID_REF
        LEFT JOIN TBL_TRN_VALUECARD_SALE_HDR HS ON H.DOC_ID=HS.CARDID_REF
        WHERE H.DOC_ID = '$CARDID_REF' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF' AND H.FYID_REF='$FYID_REF' AND H.STATUS='A' AND (H.DEACTIVATED=0 OR H.DEACTIVATED IS NULL)
        "); 

        $Current_Balance=isset($Balance[0]->DATA_DESC) ? $Balance[0]->DATA_DESC:0;

        echo $Current_Balance;           
    }



    public function GetTaxType(Request $request) { 
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $CustId         =   $request['CustId'];  
        $CustType       =   $request['CustType'];  
        if($CustType=="EXISTING"){
      
        $ObjCust =  DB::select('SELECT top 1 CID,TAX_CALCULATION FROM TBL_MST_CUSTOMER  
        WHERE STATUS= ? AND SLID_REF = ? ', ['A',$CustId]);

        $cid = $ObjCust[0]->CID;
        $ObjBillTo =  DB::select('SELECT top 1  * FROM TBL_MST_CUSTOMERLOCATION  
            WHERE BILLTO= ? AND CID_REF = ? ', [1,$cid]);   

        $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH WHERE BRID= ? ', [$BRID_REF]);

        if(isset($ObjBillTo[0]->STID_REF) && $ObjBillTo[0]->STID_REF == $ObjBranch[0]->STID_REF)
        {
            $TAXSTATE = 'WithinState';
        }
        else
        {
            $TAXSTATE = 'OutofState';
        }

    }else{

        $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH WHERE BRID= ? ', [$BRID_REF]);

        if(isset($CustId) && $CustId == $CustId)
        {
            $TAXSTATE = 'WithinState';
        }
        else
        {
            $TAXSTATE = 'OutofState';
        }

    } 
        echo $TAXSTATE;         
    }


    public function getRatePerUoM(Request $request){

        $ITEMIDREF   =   $request['ITEMIDREF'];

        $objRATE =  DB::select("SELECT P.ITEMID_REF, P.SALE_PRICE FROM TBL_MST_PPLM_DETAILS P WHERE SALE_PRICE = (SELECT max(SALE_PRICE) FROM TBL_MST_PPLM_DETAILS SP WHERE SP.ITEMID_REF=$ITEMIDREF)");

        if(!empty($objRATE)) {
            
            $ratepuom = isset($objRATE[0]->SALE_PRICE) && $objRATE[0]->SALE_PRICE !='' ? $objRATE[0]->SALE_PRICE:0;
            
        } else {
                $ratepuom = 0.00;
            }
            
        return $ratepuom; 

        exit();
    }










     
  
}
