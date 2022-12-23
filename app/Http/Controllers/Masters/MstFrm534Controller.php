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

class MstFrm534Controller extends Controller{

    protected $form_id  =   534;
    protected $vtid_ref =   604;
    protected $view     =   "masters.Sales.SearchValueCard.mstfrm534";
   
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $FormId     =   $this->form_id;
        return view($this->view.'index', compact(['FormId']));       
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
