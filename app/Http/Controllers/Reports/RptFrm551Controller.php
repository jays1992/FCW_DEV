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
use App\Exports\ValueCardSalesList;
use Maatwebsite\Excel\Facades\Excel;

class RptFrm551Controller extends Controller
{
    protected $form_id = 551;
    protected $vtid_ref   = 621;  //voucher type id
    protected $view     = "reports.purchase.ValueCardSalesList.rptfrm";
    // //validation messages  
    // 
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){  
        $FormId         =   $this->form_id;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $objRights = DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Auth::user()->BRID_REF)
        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Auth::user()->FYID_REF)
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();

        $objBranch = DB::table('TBL_MST_BRANCH')
        ->where('TBL_MST_BRANCH.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_BRANCH.STATUS','=','A')
        ->where('TBL_MST_BRANCH.DEACTIVATED','=','0')
        ->where('TBL_MST_BRANCH.DODEACTIVATED','=',NULL)
        ->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_BRANCH.BRID','=','TBL_MST_USER_BRANCH_MAP.MAPBRID_REF')
        ->where('TBL_MST_USER_BRANCH_MAP.USERID_REF','=',Auth::user()->USERID)
        ->select('TBL_MST_BRANCH.*')
        ->distinct('TBL_MST_BRANCH.BRID')   
        ->get(); 

        $objCard   =   DB::select("SELECT 
        DISTINCT
        T2.DETAIL_ID AS DATA_ID,
        T2.CARD_NO AS DATA_CODE,
        T1.DEACTIVATED AS ACTIVE,
        T1.DODEACTIVATED AS INACTIVE,
        T1.DOC_DATE AS DATA_DESC
       
        FROM TBL_MST_V_MASTER T1
        INNER JOIN TBL_MST_V_MASTER_DETAILS T2 ON T2.DOC_ID_REF=T1.DOC_ID
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.STATUS='A' OR T1.DODEACTIVATED=NULL AND T1.DEACTIVATED='0'"); 

       //dd($objCard);

        $company_check=$this->AlpsStatus(); 
                    
        return view($this->view.$FormId,compact(['objRights','objBranch','objCard','company_check','FormId']));
    }  

    
    public function ViewReport($request) {

        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
        
        if($myValue['Flag'] == 'H')
        {
            
            $From_Date       = $myValue['From_Date'];
            $To_Date         = $myValue['To_Date'];
            $BranchName      = $myValue['BranchName'];
            $CardStatus          = $myValue['CardStatus'];
            $Flag            = $myValue['Flag'];
            $CYID_REF          = Auth::user()->CYID_REF;
        }
        else
        {
            
            $From_Date       = Session::get('From_Date');
            $To_Date         = Session::get('To_Date');
            $BranchName      = Session::get('BranchName');
            $CardStatus          = Session::get('CardStatus');
            $Flag            = $myValue['Flag'];
            $CYID_REF        = Session::get('CYID_REF');
        }

        $reportParameters = array(
            'CYID'                         => Auth::user()->CYID_REF,
            'USERID'                       => Auth::user()->USERID,
            'FROMDATE'                     => $From_Date,
            'TODATE'                       => $To_Date,
            'BRID'                         => $BranchName, 
            'CARDNO'                       => $CardStatus, 
        );

        dd($reportParameters);
  
        $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
    
        $result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/ValueCardSalesList');

        $reportParameters = array(
            'CYID'                         => Auth::user()->CYID_REF,
            'USERID'                       => Auth::user()->USERID,
            'FROMDATE'                     => $From_Date,
            'TODATE'                       => $To_Date,
            'BRID'                         => $BranchName, 
            'CARDNO'                       => $CardStatus, 
        );
        $CYID_REF = Auth::user()->CYID_REF;
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
            ->setExecutionParameters($parameters);

        if($Flag == 'H')
        {
            
            Session::put('From_Date', $From_Date);
            Session::put('To_Date', $To_Date);
            Session::put('BranchName', $BranchName);
            Session::put('CardStatus', $CardStatus);
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
           // return Excel::download(new ValueCardSalesList($From_Date,$To_Date,$BranchName,$CardStatus,$CYID_REF), 'ValueCardSalesList.xlsx');
            
            $output = $ssrs->render('EXCEL'); // PDF | XML | CSV | HTML4.0
            return $output->download('Report.xls');
        }
        
    }

    public function AlpsStatus(){
        $COMPANY_NAME   =   DB::table('TBL_MST_COMPANY')->where('STATUS','=','A')->where('CYID','=',Auth::user()->CYID_REF)->select('TBL_MST_COMPANY.NAME')->first()->NAME;
      //  $COMPANY_NAME="ALPS"; 
        return $hidden         =   strpos($COMPANY_NAME,"ALPS")!== false?'show':'hide'; 
    }    
}
