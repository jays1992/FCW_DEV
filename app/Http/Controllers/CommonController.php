<?php
namespace App\Http\Controllers;

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

class CommonController extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }

    public function check_approval_level(Request $request){

        $REQUEST_DATA   =   $request['REQUEST_DATA'];
        $RECORD_ID      =   $request['RECORD_ID'];
        $result         =   Helper::check_approval_level($REQUEST_DATA,$RECORD_ID);

        echo $result;
        exit();
    }

    public function checkPeriodClosing(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $form_id    =   $request['form_id'];
        $doc_date   =   $request['doc_date'];
        $flag       =   1;

        $data   =   DB::select("SELECT MAX(T1.PERIODCL_MAT_TO_DATE) AS CLOSING_DATE
        FROM TBL_MST_PERIOD_CLOSING_MAT AS T1
        LEFT JOIN TBL_MST_PERIOD_CLOSING_HRD AS T2 ON T1.PERIODCLID_REF=T2.PERIODCLID
        WHERE T1.PERIODCL_FORM_NAME='$form_id' AND T2.CYID_REF='$CYID_REF' AND T2.BRID_REF='$BRID_REF' AND T2.FYID_REF='$FYID_REF' AND T2.STATUS='A'"); 

        if(isset($data[0]->CLOSING_DATE) && $data[0]->CLOSING_DATE !=''){

            $closing_date   =   $data[0]->CLOSING_DATE;

            if(strtotime($closing_date) >= strtotime($doc_date)){
                $flag  =   0;
            }
        }

        echo $flag;
        exit();
    }

    public function getDocNoByEvent(Request $request){

        $REQUEST        =   $request['doc_req'];
        $DATE           =   $request['REQUEST_DATA'];
        $MONTH          =   date('m',strtotime($DATE));
        $YEAR           =   date('Y',strtotime($DATE));
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF'); 
        $VTID_REF       =   $REQUEST['VTID_REF'];
        $HDR_TABLE      =   $REQUEST['HDR_TABLE'];
        $HDR_ID         =   $REQUEST['HDR_ID'];
        $HDR_DOC_NO     =   $REQUEST['HDR_DOC_NO'];
        $HDR_DOC_DT     =   $REQUEST['HDR_DOC_DT'];
        $docarray      =   $this->getManualAutoDocNo($DATE,$REQUEST);

        return Response::json($docarray);
        exit();
    }

    public function GetConvFector(Request $request){
        $ToCurrency =$request['ToCurrency'];
        $Status='A';        
        $d_currency = DB::table('TBL_MST_COMPANY')
        ->where('STATUS','=',$Status)
        ->where('CYID','=',Auth::user()->CYID_REF)
        ->select('TBL_MST_COMPANY.CRID_REF')
        ->first();
    
        $dcurrency = isset($d_currency->CRID_REF) ? $d_currency->CRID_REF:'';
    
        $objCurrencyconverter = DB::table('TBL_MST_CRCONVERSION')
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_CRCONVERSION.*')
        ->get()
        ->toArray();
    
        $ConvFact='';
    
        if(!empty($objCurrencyconverter)){
            foreach($objCurrencyconverter as $key=>$CurrencyCon){
    
                 $FromDate = $CurrencyCon->EFFDATE;
                 $ToDate = $CurrencyCon->ENDDATE;
                 $Today=    date('Y-m-d'); 
                
                if ($ToCurrency == $CurrencyCon->TOCRID_REF && $dcurrency == $CurrencyCon->FROMCRID_REF && $FromDate <= $Today /*&& $ToDate >= $Today*/)
                {
                $ConvFact=  $CurrencyCon->TOAMOUNT;
    
                }
                else
                {
                $ConvFact=''; 
                }
    
            }
        }
    
       echo $ConvFact;
      exit(); 
    
    }

    public function getItemCost(Request $request){
   
        $ITEMID_REF =   trim($request['ITEMID_REF']);
        $DOC_DATE   =   trim($request['DOC_DATE']);
        $TYPE       =   trim($request['TYPE']);

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        
        $ObjData1   =   DB::select("SELECT TOP 1 M.$TYPE AS COST
        FROM TBL_MST_PRICELIST_MAT M   
        LEFT JOIN TBL_MST_PRICELIST_HDR H ON H.PLID=M.PLID_REF
        WHERE '$DOC_DATE' BETWEEN H.PERIOD_FRDT AND H.PERIOD_TODT
        AND H.PERIOD_FRDT IS NOT NULL AND H.PERIOD_TODT IS NOT NULL AND M.ITEMID_REF=$ITEMID_REF AND H.CYID_REF=$CYID_REF AND H.BRID_REF=$BRID_REF AND H.STATUS='A'");

        $PRICE  =   0;
        if(count($ObjData1) > 0){
            $PRICE  =   isset($ObjData1[0]->COST) && $ObjData1[0]->COST !=''?$ObjData1[0]->COST : 0;
        }

        return Response::json($PRICE);
       
    }

    public function exist_customer_by_mobile_no(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $MOBILE_NO  =   trim($request['MOBILE_NO']);
        $SLID_REF   =   trim($request['SLID_REF']);
        
        if($SLID_REF !=""){
            $data       =   DB::select("SELECT T1.*
            FROM TBL_MST_CUSTOMER T1 
            WHERE T1.MONO='$MOBILE_NO' AND T1.SLID_REF !='$SLID_REF' AND T1.CYID_REF='$CYID_REF' AND T1.TYPE='CUSTOMER'  AND T1.STATUS='A' AND (T1.DEACTIVATED=0 OR T1.DEACTIVATED IS NULL) 
            ");
        }
        else{
            $data       =   DB::select("SELECT T1.*
            FROM TBL_MST_CUSTOMER T1 
            WHERE T1.MONO='$MOBILE_NO' AND T1.CYID_REF='$CYID_REF' AND T1.TYPE='CUSTOMER'  AND T1.STATUS='A' AND (T1.DEACTIVATED=0 OR T1.DEACTIVATED IS NULL) 
            ");
        }

        return Response::json(count($data));
       
    }

    public function read_notification(Request $request){

        $USERID_REF     =   Auth::user()->USERID;
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $DATE           =   date('Y-m-d');
        $DAY            =   date('d'); 
        $MONTH          =   date('m');  

        $TABLE_NAME     =   $request['TABLE_NAME'];
        $COLUMN_NAME    =   $request['COLUMN_NAME'];
        $DOC_ID         =   $request['DOC_ID'];
         

        DB::update("UPDATE $TABLE_NAME SET NOTIFY_STATUS='0' WHERE $COLUMN_NAME='$DOC_ID'");

        $query="SELECT PPLM_ID AS DOC_ID,PPLM_NO AS DOC_NO,PPLM_DATE AS DOC_DATE ,'product price' as FORM_NAME,'TBL_MST_PPLM' as TABLE_NAME,'PPLM_ID' AS COLUMN_NAME FROM TBL_MST_PPLM WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND NOTIFY_STATUS='1'
        union
        SELECT SCHEMEID AS DOC_ID,SCHEME_NO AS DOC_NO,SCHEME_DATE AS DOC_DATE ,'scheme' as FORM_NAME,'TBL_MST_SCHEME_HDR' as TABLE_NAME,'SCHEMEID' AS COLUMN_NAME FROM TBL_MST_SCHEME_HDR WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND NOTIFY_STATUS='1'
        union
        SELECT DISID AS DOC_ID,DISCODE AS DOC_NO,DOC_DATE AS DOC_DATE ,'discount' as FORM_NAME,'TBL_MST_DIS' as TABLE_NAME,'DISID' AS COLUMN_NAME FROM TBL_MST_DIS WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND NOTIFY_STATUS='1'
        union
        SELECT PKMID AS DOC_ID,PKMCODE AS DOC_NO,PKMDATE AS DOC_DATE ,'package' as FORM_NAME,'TBL_MST_PACKAGE_MASTER' as TABLE_NAME,'PKMID' AS COLUMN_NAME FROM TBL_MST_PACKAGE_MASTER WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND NOTIFY_STATUS='1'
        union
        SELECT 
        T1.APPOINTMENT_TRNID AS DOC_ID,
        T2.CCODE AS DOC_NO,
        T1.DATE AS DOC_DATE ,
        'Appointment of day' as FORM_NAME,
        'TBL_TRN_APPOINTMENT' as TABLE_NAME,
        'APPOINTMENT_TRNID' AS COLUMN_NAME 
        FROM TBL_TRN_APPOINTMENT T1 
        LEFT JOIN TBL_MST_CUSTOMER T2 ON T1.SLID_REF=T2.SLID_REF
        WHERE T1.BRID_REF='$BRID_REF' AND T1.NOTIFY_STATUS='1' AND T1.[DATE] <= '$DATE'
        UNION
        SELECT
        CID AS DOC_ID,
        NAME AS DOC_NO,
        DOB AS DOC_DATE,
        'Birthday' as FORM_NAME,
        'TBL_MST_CUSTOMER' as TABLE_NAME,
        'CID' AS COLUMN_NAME 
        FROM TBL_MST_CUSTOMER 
        WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND DAY(DOB) ='$DAY' AND MONTH(DOB)='$MONTH' AND NOTIFY_STATUS='1'
        UNION
        SELECT
        CID AS DOC_ID,
        NAME AS DOC_NO,
        DOB AS DOC_DATE,
        'Marriage' as FORM_NAME,
        'TBL_MST_CUSTOMER' as TABLE_NAME,
        'CID' AS COLUMN_NAME 
        FROM TBL_MST_CUSTOMER 
        WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND DAY(DOA) ='$DAY' AND MONTH(DOA)='$MONTH' AND NOTIFY_STATUS='1'
        "; 

        $data_array = DB::select($query);

        echo count($data_array);die;  
    }

}
