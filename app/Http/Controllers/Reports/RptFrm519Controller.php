<?php
namespace App\Http\Controllers\Reports;

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
use Chartblocks;

use App\Exports\GRN_Against_GE_Register;
use Maatwebsite\Excel\Facades\Excel;

class RptFrm519Controller extends Controller{

    protected $form_id  =   519;
    protected $vtid_ref =   589;  
    protected $view_ref =   "ChequePrintingReport";
    protected $repo_nam =   "Cheque Printing Report";
    
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){ 

        $FORM_ID        =   $this->form_id;
        $VTID_REF       =   $this->vtid_ref;
        $REPO_NAM       =   $this->repo_nam;

        $USERID         =   Auth::user()->USERID;
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');    

        $objRights      =   DB::table('TBL_MST_USERROLMAP')
                            ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
                            ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
                            ->where('TBL_MST_USERROLMAP.BRID_REF','=',$BRID_REF)
                            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID_REF)
                            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                            ->first();

        $objBranchGroup =   DB::table('TBL_MST_BRANCH_GROUP')
                            ->where('TBL_MST_BRANCH_GROUP.CYID_REF','=',$CYID_REF)
                            ->where('TBL_MST_BRANCH_GROUP.STATUS','=','A')
                            ->leftJoin('TBL_MST_BRANCH', 'TBL_MST_BRANCH_GROUP.BRID_REF','=','TBL_MST_BRANCH.BRID')
                            ->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_BRANCH.BRID','=','TBL_MST_USER_BRANCH_MAP.MAPBRID_REF')
                            ->where('TBL_MST_USER_BRANCH_MAP.USERID_REF','=',$USERID)
                            ->select('TBL_MST_BRANCH_GROUP.*')
                            ->distinct('TBL_MST_BRANCH_GROUP.BGID')
                            ->get();

        $objBranch      =   DB::table('TBL_MST_BRANCH')
                            ->where('TBL_MST_BRANCH.CYID_REF','=',$CYID_REF)
                            ->where('TBL_MST_BRANCH.STATUS','=','A')
                            ->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_BRANCH.BRID','=','TBL_MST_USER_BRANCH_MAP.MAPBRID_REF')
                            ->where('TBL_MST_USER_BRANCH_MAP.USERID_REF','=',$USERID)
                            ->select('TBL_MST_BRANCH.*')
                            ->distinct('TBL_MST_BRANCH.BRID')    
                            ->get();
                            
        $VouList   =   DB::select("SELECT 
        PAYMENTID AS VOUCHER_ID,
        PAYMENT_NO AS VOUCHER_NO
        FROM TBL_TRN_PAYMENT_HDR
        WHERE CYID_REF='$CYID_REF' AND STATUS='A' AND INSTRUMENT_TYPE='Cheque'
        ");

        return view('reports.Accounts.'.$this->view_ref.'.rptfrm'.$FORM_ID,compact(['FORM_ID','REPO_NAM','objRights','objBranchGroup','objBranch','VouList']));        
    }  

    public function getVoucherDetails(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $PAYMENTID  =   $request['PAYMENTID'];

        $query_data =   DB::SELECT("SELECT 
        FORMAT(T1.PAYMENT_DT,'dd-MM-yyyy') AS VOUCHER_DATE,
        T1.INSTRUMENT_NO AS CHEQUE_NO,
        FORMAT(T1.TRANSACTION_DT,'dd-MM-yyyy') AS CHEQUE_DATE,
        T1.TOAL_AMOUNT AS CHEQUE_AMOUNT,
        FORMAT(T1.TRANSACTION_DT,'ddMMyyyy') AS CHEQUE_PRINT_DATE,
        case 
        when T1.PAYMENT_FOR = 'Customer' then 
        (SELECT NAME FROM TBL_MST_CUSTOMER WHERE SLID_REF=T1.CUSTMER_VENDOR_ID AND TYPE='CUSTOMER')
        when T1.PAYMENT_FOR = 'Vendor' then 
        (SELECT NAME FROM TBL_MST_VENDOR WHERE SLID_REF=T1.CUSTMER_VENDOR_ID)
        when T1.PAYMENT_FOR = 'Account' then 
        (SELECT 
        TOP 1
        TBL_MST_GENERALLEDGER.GLNAME
        FROM TBL_TRN_PAYMENT_ACCOUNT
        LEFT JOIN TBL_MST_GENERALLEDGER ON TBL_MST_GENERALLEDGER.GLID=TBL_TRN_PAYMENT_ACCOUNT.GLID_REF
        WHERE PAYMENTID_REF=T1.PAYMENTID)
        end AS PAYEE
        FROM TBL_TRN_PAYMENT_HDR T1
        WHERE T1.PAYMENTID='$PAYMENTID'
        ");

        return Response::json($query_data);
    }

    
    public function ViewReport($request) {

        $FORM_ID        =   $this->form_id;
        $VTID_REF       =   $this->vtid_ref;

        $USERID         =   Auth::user()->USERID;
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF'); 

        $box            =   $request;        
        $myValue        =   array();
        parse_str($box, $myValue);
    
        if($myValue['Flag'] == 'H'){
            $From_Date      =   $myValue['From_Date'];
            $To_Date        =   $myValue['To_Date'];
            $BranchGroup    =   $myValue['BranchGroup'];
            $BranchName     =   $myValue['BranchName'];
            $STATUS         =   $myValue['STATUS'];
            $PAYMENTID      =   $myValue['PAYMENTID'];
            $Flag           =   $myValue['Flag']; 
        }
        else{
        
            $From_Date      =   Session::get('From_Date');
            $To_Date        =   Session::get('To_Date');
            $BranchGroup    =   Session::get('BranchGroup');
            $BranchName     =   Session::get('BranchName');
            $STATUS         =   Session::get('STATUS');
            $PAYMENTID      =   Session::get('PAYMENTID');
            $Flag           =   $myValue['Flag'];
        }
        
        $ssrs               =   new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
        $result             =   $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/'.$this->view_ref);
      
        $reportParameters = array(
            'p_cyid'        =>  $CYID_REF,
            'p_userid'      =>  $USERID,
            'FinancialYear' =>  $FYID_REF, 
            'p_branchgroup' =>  $BranchGroup,
            'p_branch'      =>  $BranchName,
            'FromDate'      =>  $From_Date,
            'ToDate'        =>  $To_Date,
            'PAYMENTID'     =>  $PAYMENTID        
        );


        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        $ssrs->setSessionId($result->executionInfo->ExecutionID)->setExecutionParameters($parameters);

        if($Flag == 'H'){
            Session::put('From_Date', $From_Date);
            Session::put('To_Date', $To_Date);
            Session::put('BranchGroup', $BranchGroup);
            Session::put('BranchName', $BranchName);
            Session::put('STATUS', $STATUS);
            Session::put('PAYMENTID', $PAYMENTID);
            $output = $ssrs->render('HTML4.0'); // PDF | XML | CSV
            echo $output;
        }
        else if($Flag == 'P'){
            $output = $ssrs->render('PDF'); // PDF | XML | CSV | HTML4.0
            return $output->download('Report.pdf');
        }
        else if($Flag == 'E'){
            $output = $ssrs->render('EXCEL'); // PDF | XML | CSV | HTML4.0
            return $output->download('Report.xls'); 
        }
         
    }
    


    
}
