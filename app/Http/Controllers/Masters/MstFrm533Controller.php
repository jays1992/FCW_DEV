<?php
namespace App\Http\Controllers\Masters;

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

class MstFrm533Controller extends Controller{

    protected $form_id  =   533;
    protected $vtid_ref =   603;
    protected $view     =   "masters.Sales.ValueCardActiveInactive.mstfrm533";
   
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $FormId     =   $this->form_id;
        return view($this->view.'index', compact(['FormId']));       
    }


    public function save(Request $request){

        $VTID_REF       =   $this->vtid_ref;
        $USERID_REF     =   Auth::user()->USERID;   
        $ACTIONNAME     =   'EDIT';
        $IPADDRESS      =   $request->getClientIp();
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $details  = array();
        if(isset($_REQUEST['selectAll']) && !empty($_REQUEST['selectAll'])){
            foreach($_REQUEST['selectAll'] as $key=>$val){
                $details[] = array(
                'DETAIL_ID'         => $_REQUEST['selectAll'][$key],
                'CARD_NO'           => $_REQUEST['CARD_NO'][$key],
                'ACTIVE_DEACTIVE'   => $_REQUEST['DEACTIVATED'][$key]
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

        $log_data = [
            $CYID_REF,$BRID_REF,$FYID_REF,$XML_DETAILS,$VTID_REF,
            $USERID_REF,Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];

        $sp_result  =   DB::select('EXEC SP_VCACTIVEDEACTIVE_IN ?,?,?,?,?, ?,?,?,?,?', $log_data);  
        
        $contains   =   Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);
        }
        else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
    }

    public function getBranchMaster(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $data       =   DB::select("SELECT 
        DISTINCT
        T2.BRID AS DATA_ID,
        T2.BRCODE AS DATA_CODE,
        T2.BRNAME AS DATA_DESC
        FROM TBL_MST_V_MASTER T1
        INNER JOIN TBL_MST_BRANCH T2 ON T2.BRID=T1.FRANCHISE_ID
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.STATUS='A'"); 

        return Response::json($data);
    }

    public function getCardMaster(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $data   =   DB::select("SELECT 
        DISTINCT
        T2.DETAIL_ID AS DATA_ID,
        T2.CARD_NO AS DATA_CODE,
        T1.DOC_DATE AS DATA_DESC
        FROM TBL_MST_V_MASTER T1
        INNER JOIN TBL_MST_V_MASTER_DETAILS T2 ON T2.DOC_ID_REF=T1.DOC_ID
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.STATUS='A'"); 

        return Response::json($data);
    }

    public function getCardDetails(Request $request){

        $CYID_REF           =   Auth::user()->CYID_REF;
        $BRID_REF           =   Session::get('BRID_REF');
        $FYID_REF           =   Session::get('FYID_REF');
        $BRANCH_ID          =   $_REQUEST['BRANCH_ID'];
        $CARD_NO            =   $_REQUEST['CARD_NO'];

        $WHERE_BRANCH_ID    =   $BRANCH_ID !=''?"AND T1.FRANCHISE_ID='$BRANCH_ID'":"";
        $WHERE_CARD_NO      =   $CARD_NO !=''?"AND T2.CARD_NO='$CARD_NO'":"";

        $data       =   DB::select("SELECT
        T2.DETAIL_ID,
        T2.CARD_NO,
        T2.AMOUNT,
        T2.NET_AMOUNT,
        T2.ACTIVE_DEACTIVE,
        FORMAT (T2.VALIDITY_TILL, 'dd-MM-yyyy') AS VALIDITY_TILL,
        T3.BRNAME AS FRANCHISE_NAME
        FROM TBL_MST_V_MASTER T1
        INNER JOIN TBL_MST_V_MASTER_DETAILS T2 ON T2.DOC_ID_REF=T1.DOC_ID
        LEFT JOIN TBL_MST_BRANCH T3 ON T3.BRID=T1.FRANCHISE_ID
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' $WHERE_BRANCH_ID $WHERE_CARD_NO 
        ORDER BY T2.DETAIL_ID DESC
        "); 

        return Response::json($data);
    }
     
}
