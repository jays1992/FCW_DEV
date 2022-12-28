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

class TrnFrm543Controller extends Controller{

    protected $form_id  =   543;
    protected $vtid_ref =   613;
    protected $view     =   "transactions.sales.ValueCardSales.trnfrm543";
   
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
        T2.DESCRIPTIONS AS CREATEDBY,D.CARD_NO AS CARDNO,C.NAME  
        FROM TBL_TRN_VALUECARD_SALE_HDR T1
        LEFT JOIN TBL_MST_USER T2 ON T2.USERID=T1.CREATED_BY
		LEFT JOIN TBL_MST_V_MASTER_DETAILS D ON D.DETAIL_ID=T1.CARDID_REF
		LEFT JOIN TBL_MST_CUSTOMER C ON C.SLID_REF=T1.CUSTOMER_ID
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' ORDER BY VCS_ID DESC");

        return view($this->view,compact(['FormId','objRights','objDataList']));
    }

    public function add(){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $FormId     =   $this->form_id;

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_VALUECARD_SALE_HDR',
            'HDR_ID'=>'VCS_ID',
            'HDR_DOC_NO'=>'DOC_NO',
            'HDR_DOC_DT'=>'DOC_DATE',
            'HDR_DOC_TYPE'=>'transaction'
        );

        $country_state_city =   $this->country_state_city();

        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req); 
        $objUdf     =   $this->getUdf(['VTID_REF'=>$this->vtid_ref]);

        $GSTdata = ['GSTID','GSTCODE','DESCRIPTIONS'];
        $objGstTypeList       = Helper::getTableData('TBL_MST_GSTTYPE',$GSTdata,NULL, NULL, NULL,'GSTCODE','ASC');

        return view($this->view.'add', compact(['FormId','doc_req','docarray','objUdf','objGstTypeList','country_state_city']));       
    }


    public function save(Request $request){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $VTID_REF       =   $this->vtid_ref;
        $USERID_REF     =   Auth::user()->USERID;   
        $ACTIONNAME     =   'ADD';
        $IPADDRESS      =   $request->getClientIp();
        
      
     

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


        $DOC_NO                     =   $request['DOC_NO'];
        $DOC_DATE                   =   $request['DOC_DATE'];
        $CUSTOMER_TYPE              =   $request['CUSTOMER_TYPE'];
        $CUSTOMER_NAME              =   $request['CUSTOMER_NAME'];
        $CUSTOMER_ID                =   $request['CUSTOMER_ID'];
        $DOB                        =   $request['DOB'];
        $EMAIL_ID                   =   $request['EMAIL_ID'];
        $MOBILE_NO                  =   $request['MOBILE_NO'];
        $ADDRESS                    =   $request['ADDRESS'];
        $ANNIVERSARY_DATE           =   $request['ANNIVERSARY_DATE'];
        $COUNTRY_ID                 =   $request['COUNTRY_ID'];
        $STATE_ID                   =   $request['STATE_ID'];
        $CITY_ID                    =   $request['CITY_ID'];
        $PINCODE                    =   $request['PINCODE'];
        $GST_TYPE                   =   $request['GST_TYPE'];
        $GST_IN                     =   $request['GST_IN'];
        $LANDLINE_NO                =   $request['LANDLINE_NO'];
        $TOTAL_CARD_AMOUNT          =   $request['TOTAL_CARD_AMOUNT'] !=''?$request['TOTAL_CARD_AMOUNT']:0;
        $TOTAL_DISCOUONT_AMOUNT     =   $request['TOTAL_DISCOUONT_AMOUNT'] !=''?$request['TOTAL_DISCOUONT_AMOUNT']:0;
        $TOTAL_TAX_AMOUNT           =   $request['TOTAL_TAX_AMOUNT'] !=''?$request['TOTAL_TAX_AMOUNT']:0;
        $TOTAL_NET_AMOUNT           =   $request['TOTAL_NET_AMOUNT'] !=''?$request['TOTAL_NET_AMOUNT']:0;
        $TOTAL_PAID_AMOUNT          =   $request['TOTAL_PAID_AMOUNT'] !=''?$request['TOTAL_PAID_AMOUNT']:0;
        $CARDID_REF                 =   $request['CARD_ID'];
        $VALIDITY_MONTH             =   $request['VALIDITY_MONTH'];
        $VALIDITY_FROM              =   $request['VALIDITY_START_FROM'];
        $VALIDITY_TO                =   $request['VALIDITY_START_TO'];
        $CUSTOMER_NAME              =   $request['CUSTOMER_NAME'];

       



       
        $log_data = [
            $DOC_NO,                            $DOC_DATE,                          $CUSTOMER_TYPE,                     $CUSTOMER_ID,   $DOB,
            $EMAIL_ID,                          $MOBILE_NO,                         $ADDRESS,                           $ANNIVERSARY_DATE,
            $COUNTRY_ID,                        $STATE_ID,                          $CITY_ID,                           $PINCODE,
            $GST_TYPE,                          $GST_IN,                            $LANDLINE_NO,                       $CARDID_REF, 
            $VALIDITY_MONTH,                    $VALIDITY_FROM,                     $VALIDITY_TO,                       $TOTAL_CARD_AMOUNT,
            $TOTAL_TAX_AMOUNT,                  $TOTAL_DISCOUONT_AMOUNT,            $TOTAL_NET_AMOUNT,                  $TOTAL_PAID_AMOUNT,
            $XML_TAX,                           $XML_PAY,                           $XML_UDF,                           $CYID_REF,                          
            $BRID_REF,                          $FYID_REF,                          $VTID_REF,                          $USERID_REF,                        
            Date('Y-m-d'),                      Date('h:i:s.u'),                    $ACTIONNAME,                        $IPADDRESS,
            $CUSTOMER_NAME
        ];

        //dd($log_data); 

        $sp_result  =   DB::select('EXEC SP_VALUECARD_SALE_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,? ', $log_data);  
       //  dd($sp_result); 
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
                                CONCAT(T2.CCODE,' - ',T2.NAME) AS CUSTOMER_NAME,V.CARD_NO AS CARDNO,
                                CONCAT(C.CTRYCODE,' - ',C.NAME) AS COUNTRY_NAME,
								CONCAT(S.STCODE,' - ',S.NAME) AS STATE_NAME,
								CONCAT(CT.CITYCODE,' - ',CT.NAME) AS CITY_NAME
                                FROM TBL_TRN_VALUECARD_SALE_HDR T1
                                LEFT JOIN TBL_MST_CUSTOMER T2 ON T1.CUSTOMER_ID=T2.SLID_REF
								LEFT JOIN TBL_MST_V_MASTER_DETAILS V ON V.DETAIL_ID=T1.CARDID_REF
                                LEFT JOIN TBL_MST_COUNTRY C ON C.CTRYID=T1.COUNTRY_ID
								LEFT JOIN TBL_MST_STATE S ON S.STID=T1.STATE_ID
								LEFT JOIN TBL_MST_CITY CT ON CT.CITYID=T1.CITY_ID
                                WHERE T1.VCS_ID='$id'
                                ");

                                //DD($HDR); 
                  
            $HDR            =   count($HDR) > 0?$HDR[0]:[];

            $TAX            =   DB::select("SELECT * FROM TBL_TRN_VALUECARD_SALE_TAX WHERE VCS_ID_REF='$id'");
            $PAY            =   DB::select("SELECT * FROM TBL_TRN_VALUECARD_SALE_PAY WHERE VCS_ID_REF='$id'");


            $objUdf         =   $this->getUdf(['VTID_REF'=>$this->vtid_ref]);
            $objtempUdf     =   $objUdf;
            foreach ($objtempUdf as $index => $udfvalue) {

                $objSavedUDF =  DB::table('TBL_TRN_VALUECARD_SALE_UDF')
                ->where('VCS_ID_REF','=',$id)
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

            return view($this->view.'edit',compact(['FormId','objRights','ActionStatus','HDR','TAX','PAY','objUdf','objGstTypeList']));      
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
                                CONCAT(T2.CCODE,' - ',T2.NAME) AS CUSTOMER_NAME,V.CARD_NO AS CARDNO,
                                CONCAT(C.CTRYCODE,' - ',C.NAME) AS COUNTRY_NAME,
								CONCAT(S.STCODE,' - ',S.NAME) AS STATE_NAME,
								CONCAT(CT.CITYCODE,' - ',CT.NAME) AS CITY_NAME
                                FROM TBL_TRN_VALUECARD_SALE_HDR T1
                                LEFT JOIN TBL_MST_CUSTOMER T2 ON T1.CUSTOMER_ID=T2.SLID_REF
								LEFT JOIN TBL_MST_V_MASTER_DETAILS V ON V.DETAIL_ID=T1.CARDID_REF
                                LEFT JOIN TBL_MST_COUNTRY C ON C.CTRYID=T1.COUNTRY_ID
								LEFT JOIN TBL_MST_STATE S ON S.STID=T1.STATE_ID
								LEFT JOIN TBL_MST_CITY CT ON CT.CITYID=T1.CITY_ID
                                WHERE T1.VCS_ID='$id'
                                ");
                  
            $HDR            =   count($HDR) > 0?$HDR[0]:[];

            $TAX            =   DB::select("SELECT * FROM TBL_TRN_VALUECARD_SALE_TAX WHERE VCS_ID_REF='$id'");
            $PAY            =   DB::select("SELECT * FROM TBL_TRN_VALUECARD_SALE_PAY WHERE VCS_ID_REF='$id'");


            $objUdf         =   $this->getUdf(['VTID_REF'=>$this->vtid_ref]);
            $objtempUdf     =   $objUdf;
            foreach ($objtempUdf as $index => $udfvalue) {

                $objSavedUDF =  DB::table('TBL_TRN_VALUECARD_SALE_UDF')
                ->where('VCS_ID_REF','=',$id)
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

            return view($this->view.'view',compact(['FormId','objRights','ActionStatus','HDR','TAX','PAY','objUdf','objGstTypeList']));      
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

        $DOC_ID                     =   $request['DOC_ID'];
        $DOC_NO                     =   $request['DOC_NO'];
        $DOC_DATE                   =   $request['DOC_DATE'];
        $CUSTOMER_TYPE              =   $request['CUSTOMER_TYPE'];
        $CUSTOMER_NAME              =   $request['CUSTOMER_NAME'];
        $CUSTOMER_ID                =   $request['CUSTOMER_ID'];
        $DOB                        =   $request['DOB'];
        $EMAIL_ID                   =   $request['EMAIL_ID'];
        $MOBILE_NO                  =   $request['MOBILE_NO'];
        $ADDRESS                    =   $request['ADDRESS'];
        $ANNIVERSARY_DATE           =   $request['ANNIVERSARY_DATE'];
        $COUNTRY_ID                 =   $request['COUNTRY_ID'];
        $STATE_ID                   =   $request['STATE_ID'];
        $CITY_ID                    =   $request['CITY_ID'];
        $PINCODE                    =   $request['PINCODE'];
        $GST_TYPE                   =   $request['GST_TYPE'];
        $GST_IN                     =   $request['GST_IN'];
        $LANDLINE_NO                =   $request['LANDLINE_NO'];
        $TOTAL_CARD_AMOUNT          =   $request['TOTAL_CARD_AMOUNT'] !=''?$request['TOTAL_CARD_AMOUNT']:0;
        $TOTAL_DISCOUONT_AMOUNT     =   $request['TOTAL_DISCOUONT_AMOUNT'] !=''?$request['TOTAL_DISCOUONT_AMOUNT']:0;
        $TOTAL_TAX_AMOUNT           =   $request['TOTAL_TAX_AMOUNT'] !=''?$request['TOTAL_TAX_AMOUNT']:0;
        $TOTAL_NET_AMOUNT           =   $request['TOTAL_NET_AMOUNT'] !=''?$request['TOTAL_NET_AMOUNT']:0;
        $TOTAL_PAID_AMOUNT          =   $request['TOTAL_PAID_AMOUNT'] !=''?$request['TOTAL_PAID_AMOUNT']:0;
        $CARDID_REF                 =   $request['CARD_ID'];
        $VALIDITY_MONTH             =   $request['VALIDITY_MONTH'];
        $VALIDITY_FROM              =   $request['VALIDITY_START_FROM'];
        $VALIDITY_TO                =   $request['VALIDITY_START_TO'];

       



       
        $log_data = [
            $DOC_ID,$DOC_NO,                    $DOC_DATE,                          $CUSTOMER_TYPE,                     $CUSTOMER_ID,   $DOB,
            $EMAIL_ID,                          $MOBILE_NO,                         $ADDRESS,                           $ANNIVERSARY_DATE,
            $COUNTRY_ID,                        $STATE_ID,                          $CITY_ID,                           $PINCODE,
            $GST_TYPE,                          $GST_IN,                            $LANDLINE_NO,                       $CARDID_REF, 
            $VALIDITY_MONTH,                    $VALIDITY_FROM,                     $VALIDITY_TO,                       $TOTAL_CARD_AMOUNT,
            $TOTAL_TAX_AMOUNT,                  $TOTAL_DISCOUONT_AMOUNT,            $TOTAL_NET_AMOUNT,                  $TOTAL_PAID_AMOUNT,
            $XML_TAX,                           $XML_PAY,                           $XML_UDF,                           $CYID_REF,                          
            $BRID_REF,                          $FYID_REF,                          $VTID_REF,                          $USERID_REF,                        
            Date('Y-m-d'),                      Date('h:i:s.u'),                    $ACTIONNAME,                        $IPADDRESS
        ];

        //dd($log_data); 

        $sp_result  =   DB::select('EXEC SP_VALUECARD_SALE_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,? ', $log_data);  

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

        //DD($sp_listing_result);

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

        $DOC_ID                     =   $request['DOC_ID'];
        $DOC_NO                     =   $request['DOC_NO'];
        $DOC_DATE                   =   $request['DOC_DATE'];
        $CUSTOMER_TYPE              =   $request['CUSTOMER_TYPE'];
        $CUSTOMER_NAME              =   $request['CUSTOMER_NAME'];
        $CUSTOMER_ID                =   $request['CUSTOMER_ID'];
        $DOB                        =   $request['DOB'];
        $EMAIL_ID                   =   $request['EMAIL_ID'];
        $MOBILE_NO                  =   $request['MOBILE_NO'];
        $ADDRESS                    =   $request['ADDRESS'];
        $ANNIVERSARY_DATE           =   $request['ANNIVERSARY_DATE'];
        $COUNTRY_ID                 =   $request['COUNTRY_ID'];
        $STATE_ID                   =   $request['STATE_ID'];
        $CITY_ID                    =   $request['CITY_ID'];
        $PINCODE                    =   $request['PINCODE'];
        $GST_TYPE                   =   $request['GST_TYPE'];
        $GST_IN                     =   $request['GST_IN'];
        $LANDLINE_NO                =   $request['LANDLINE_NO'];
        $TOTAL_CARD_AMOUNT          =   $request['TOTAL_CARD_AMOUNT'] !=''?$request['TOTAL_CARD_AMOUNT']:0;
        $TOTAL_DISCOUONT_AMOUNT     =   $request['TOTAL_DISCOUONT_AMOUNT'] !=''?$request['TOTAL_DISCOUONT_AMOUNT']:0;
        $TOTAL_TAX_AMOUNT           =   $request['TOTAL_TAX_AMOUNT'] !=''?$request['TOTAL_TAX_AMOUNT']:0;
        $TOTAL_NET_AMOUNT           =   $request['TOTAL_NET_AMOUNT'] !=''?$request['TOTAL_NET_AMOUNT']:0;
        $TOTAL_PAID_AMOUNT          =   $request['TOTAL_PAID_AMOUNT'] !=''?$request['TOTAL_PAID_AMOUNT']:0;
        $CARDID_REF                 =   $request['CARD_ID'];
        $VALIDITY_MONTH             =   $request['VALIDITY_MONTH'];
        $VALIDITY_FROM              =   $request['VALIDITY_START_FROM'];
        $VALIDITY_TO                =   $request['VALIDITY_START_TO'];

       



       
        $log_data = [
            $DOC_ID,$DOC_NO,                    $DOC_DATE,                          $CUSTOMER_TYPE,                     $CUSTOMER_ID,   $DOB,
            $EMAIL_ID,                          $MOBILE_NO,                         $ADDRESS,                           $ANNIVERSARY_DATE,
            $COUNTRY_ID,                        $STATE_ID,                          $CITY_ID,                           $PINCODE,
            $GST_TYPE,                          $GST_IN,                            $LANDLINE_NO,                       $CARDID_REF, 
            $VALIDITY_MONTH,                    $VALIDITY_FROM,                     $VALIDITY_TO,                       $TOTAL_CARD_AMOUNT,
            $TOTAL_TAX_AMOUNT,                  $TOTAL_DISCOUONT_AMOUNT,            $TOTAL_NET_AMOUNT,                  $TOTAL_PAID_AMOUNT,
            $XML_TAX,                           $XML_PAY,                           $XML_UDF,                           $CYID_REF,                          
            $BRID_REF,                          $FYID_REF,                          $VTID_REF,                          $USERID_REF,                        
            Date('Y-m-d'),                      Date('h:i:s.u'),                    $ACTIONNAME,                        $IPADDRESS
        ];

        //dd($log_data); 

        $sp_result  =   DB::select('EXEC SP_VALUECARD_SALE_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,? ', $log_data);  

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
        $TABLE      =   "TBL_TRN_VALUECARD_SALE_HDR";
        $FIELD      =   "VCS_ID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_VALUECARD_SALE_TAX',
            'NT'  => 'TBL_TRN_VALUECARD_SALE_PAY',
            'NT'  => 'TBL_TRN_VALUECARD_SALE_UDF',
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
            $objMst =   DB::table("TBL_TRN_VALUECARD_SALE_HDR")
            ->where('VCS_ID','=',$id)
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

            $dirname =   'ValueCardSale';
                
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
        
		$image_path         =   "docs/company".$CYID_REF."/ValueCardSale";     
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



    public function searchCard(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        
        // echo "SELECT D.DETAIL_ID AS DOC_ID,D.CARD_NO AS DOC_NO,D.NET_AMOUNT AS DOC_DATE,D.NET_AMOUNT,D.VALIDITY_MON,D.DISCOUNT_AMT,D.AMOUNT FROM TBL_MST_V_MASTER_DETAILS D
		// LEFT JOIN TBL_MST_V_MASTER H ON H.DOC_ID=D.DOC_ID_REF
        // WHERE  H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF' AND H.STATUS='A' AND (H.DEACTIVATED=0 OR H.DEACTIVATED IS NULL)
        // ";die;
       
        $data   =   DB::select("SELECT D.DETAIL_ID AS DOC_ID,D.CARD_NO AS DOC_NO,D.NET_AMOUNT AS DOC_DATE,D.NET_AMOUNT,D.VALIDITY_MON,D.DISCOUNT_AMT,D.AMOUNT FROM TBL_MST_V_MASTER_DETAILS D
		LEFT JOIN TBL_MST_V_MASTER H ON H.DOC_ID=D.DOC_ID_REF
        WHERE  H.CYID_REF='$CYID_REF' AND H.FRANCHISE_ID='$BRID_REF' AND H.STATUS='A' AND (H.DEACTIVATED=0 OR H.DEACTIVATED IS NULL)
        "); 

        return Response::json($data);
    }


    public function loadTax(){

        $CYID_REF           =   Auth::user()->CYID_REF;
        $BRID_REF           =   Session::get('BRID_REF');
        $FYID_REF           =   Session::get('FYID_REF');
        $CUSTOMER_STATEID   =   $_REQUEST['CUSTOMER_STATEID'];

       // $CUSTOMER_STATE =   DB::table('TBL_MST_CUSTOMER')->where('SLID_REF','=',$CUSTOMER_ID)->select('REGSTID_REF')->first();
        $CUSTOMER_STATE =   $CUSTOMER_STATEID;


        $BRANCH_STATE   =   DB::table('TBL_MST_BRANCH')->where('BRID','=',$BRID_REF)->select('STID_REF')->first();
        $WHERE_STATE    =   $CUSTOMER_STATE === $BRANCH_STATE->STID_REF?" T1.WITHINSTATE='1'":" T1.OUTOFSTATE='1'";

        $data   =   DB::select("SELECT 
        DISTINCT 0 AS TAX_RATE,
        T1.TAX_TYPE
        FROM TBL_MST_TAXTYPE T1    
        WHERE $WHERE_STATE AND T1.TAX_TYPE IS NOT NULL
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
    
     
}
