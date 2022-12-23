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

use App\Exports\QualityInspectionRegister;
use Maatwebsite\Excel\Facades\Excel;

class RptFrm408Controller extends Controller
{
    protected $form_id = 408;
    protected $vtid_ref   = 486;  //voucher type id
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
        $objRights = DB::table('TBL_MST_USERROLMAP')
                        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Auth::user()->BRID_REF)
                        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Auth::user()->FYID_REF)
                        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                        ->first();

        $objBranchGroup = DB::table('TBL_MST_BRANCH_GROUP')
                        ->where('TBL_MST_BRANCH_GROUP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_BRANCH_GROUP.STATUS','=','A')
                        ->leftJoin('TBL_MST_BRANCH', 'TBL_MST_BRANCH_GROUP.BRID_REF','=','TBL_MST_BRANCH.BRID')
                        ->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_BRANCH.BRID','=','TBL_MST_USER_BRANCH_MAP.MAPBRID_REF')
                        ->where('TBL_MST_USER_BRANCH_MAP.USERID_REF','=',Auth::user()->USERID)
                        ->select('TBL_MST_BRANCH_GROUP.*')
                        ->distinct('TBL_MST_BRANCH_GROUP.BGID')
                        ->get();

        $objBranch = DB::table('TBL_MST_BRANCH')
                        ->where('TBL_MST_BRANCH.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_BRANCH.STATUS','=','A')
                        ->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_BRANCH.BRID','=','TBL_MST_USER_BRANCH_MAP.MAPBRID_REF')
                        ->where('TBL_MST_USER_BRANCH_MAP.USERID_REF','=',Auth::user()->USERID)
                        ->select('TBL_MST_BRANCH.*')
                        ->distinct('TBL_MST_BRANCH.BRID')    
                        ->get(); 


        $ObjVendor = DB::table('TBL_TRN_IGRN02_HDR')
                        ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_MST_SUBLEDGER.SGLID','=','TBL_TRN_IGRN02_HDR.VID_REF')
                        ->where('TBL_TRN_IGRN02_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_TRN_IGRN02_HDR.BRID_REF','=',Auth::user()->BRID_REF)
                        ->select('TBL_MST_SUBLEDGER.SGLID','TBL_MST_SUBLEDGER.SGLCODE','TBL_MST_SUBLEDGER.SLNAME')
                        ->distinct('TBL_MST_SUBLEDGER.SGLID')
                        ->get();  





        $objGRNNo =  DB::table('TBL_TRN_IGRN02_HDR')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('BRID_REF','=',Auth::user()->BRID_REF)                        
                        ->select('GRNID','GRN_NO')
                        ->distinct('GRNID')
                        ->get();  


        return view('reports.inventory.QualityInspection.rptfrm408',compact(['objRights','objBranchGroup','objBranch','ObjVendor','objGRNNo']));        
    }  

    
   public function ViewReport($request) {

    $box = $request;        
    $myValue=  array();
    parse_str($box, $myValue);
    
    if($myValue['Flag'] == 'H')
    {
        $SGLID           =      $myValue['SGLID'];
        $From_Date       =      $myValue['From_Date'];
        $To_Date         =      $myValue['To_Date'];
        $BranchGroup     =      $myValue['BranchGroup'];
        $BranchName      =      $myValue['BranchName'];
        $GRNID           =      $myValue['GRNID']; 
        $Flag            =      $myValue['Flag'];
        $STATUS          =      $myValue['STATUS'];
        $Quantity        =      $myValue['Quantity'];
        $CYID_REF        =      Auth::user()->CYID_REF;
    }
    else
    {
        $SGLID           =      Session::get('SGLID');
        $From_Date       =      Session::get('From_Date');
        $To_Date         =      Session::get('To_Date');
        $BranchGroup     =      Session::get('BranchGroup');
        $BranchName      =      Session::get('BranchName');
        $GRNID           =      Session::get('GRNID');
        $STATUS          =      Session::get('STATUS');
        $Quantity        =      Session::get('Quantity');
        $Flag            =      $myValue['Flag'];
        $CYID_REF        =      Auth::user()->CYID_REF;
    }



            $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
   
            $result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/QualityInspection');
      
        
        $reportParameters = array(
            'p_cyid'                      => Auth::user()->CYID_REF,
            'p_userid'                    => Auth::user()->USERID,
            'FromDate'                    => $From_Date,
            'To_Date'                     => $To_Date,
            'p_branchgroup'               => $BranchGroup,
            'p_branch'                    => $BranchName,
            'p_vendor'                    => $SGLID,
            'p_grnno'                     => $GRNID,
            'STATUS'                      => $STATUS,
            'Quantity'                    => $Quantity
       
        );
       
        $CYID_REF          = Auth::user()->CYID_REF;
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
            ->setExecutionParameters($parameters);

            if($Flag == 'H')
            {
               
                Session::put('SGLID', $SGLID);
                Session::put('From_Date', $From_Date);
                Session::put('To_Date', $To_Date);
                Session::put('BranchGroup', $BranchGroup);
                Session::put('BranchName', $BranchName);
                Session::put('GRNID', $GRNID);
                Session::put('Quantity', $Quantity);
                Session::put('STATUS', $STATUS);

                
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
                return Excel::download(new QualityInspectionRegister($SGLID,$From_Date,$To_Date,$BranchGroup,$BranchName,$GRNID,$STATUS,$CYID_REF), 'QualityInspectionRegister.xlsx');
            }
         
     }

    
}
