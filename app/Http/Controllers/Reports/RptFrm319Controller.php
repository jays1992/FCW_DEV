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
use App\Exports\VendorLedger;
use Maatwebsite\Excel\Facades\Excel;

class RptFrm319Controller extends Controller
{
    protected $form_id = 319;
    protected $vtid_ref   = 407;  //voucher type id
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
        $ObjVendorGroup = DB::table('TBL_MST_VENDORGROUP')
        ->where('TBL_MST_VENDORGROUP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_VENDORGROUP.BRID_REF','=',Auth::user()->BRID_REF)
        ->where('TBL_MST_VENDORGROUP.STATUS','=','A')
        ->select('TBL_MST_VENDORGROUP.VGID','TBL_MST_VENDORGROUP.VGCODE','TBL_MST_VENDORGROUP.DESCRIPTIONS')
        ->distinct('TBL_MST_VENDORGROUP.VGID')
        ->get(); 
		
		/* $ObjVendor = DB::table('TBL_TRN_FJRV01_HDR')
        ->where('TBL_TRN_FJRV01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_FJRV01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        ->where('TBL_TRN_FJRV01_HDR.STATUS','=','A')
        ->where('TBL_MST_SUBLEDGER.SGLID','!=','')
		->where('TBL_TRN_FJRV01_ACC.SGLID_REF','=','S')
        ->where('TBL_MST_SUBLEDGER.BELONGS_TO','=','Vendor')
        ->leftJoin('TBL_TRN_FJRV01_ACC', 'TBL_TRN_FJRV01_HDR.JVID','=','TBL_TRN_FJRV01_ACC.JVID_REF')
        ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_TRN_FJRV01_ACC.GLID_REF','=','TBL_MST_SUBLEDGER.SGLID')
        ->select('TBL_MST_SUBLEDGER.SGLID','TBL_MST_SUBLEDGER.SGLCODE','TBL_MST_SUBLEDGER.SLNAME')
        ->distinct()
        ->get(); */
		
		$CYID_REF = Auth::user()->CYID_REF;
		$BRID_REF = Auth::user()->BRID_REF;

        $ObjVendor = DB::select('SELECT DISTINCT C.SGLID, C.SGLCODE, C.SLNAME
								FROM TBL_TRN_FJRV01_ACC AS A LEFT OUTER JOIN TBL_TRN_FJRV01_HDR AS H ON A.JVID_REF = H.JVID LEFT OUTER JOIN
              TBL_MST_SUBLEDGER AS C ON A.GLID_REF = C.SGLID AND A.SGLID_REF = ? WHERE H.CYID_REF = ? AND H.BRID_REF = ? AND C.BELONGS_TO = ?
				UNION
			  SELECT DISTINCT C.SGLID, C.SGLCODE, C.SLNAME FROM TBL_MST_SUBLEDGER AS C  JOIN TBL_MST_SLOPENING_LEDGER AS O
			  ON C.SGLID  = O.SGLID_REF WHERE C.SGLID NOT IN (SELECT GLID_REF FROM  TBL_TRN_FJRV01_ACC A LEFT JOIN TBL_TRN_FJRV01_HDR H ON A.JVID_REF=H.JVID 
			 AND A.SGLID_REF= ?  AND H.CYID_REF = C.CYID_REF AND H.BRID_REF = C.BRID_REF) 
			 AND  C.CYID_REF = ? AND C.BRID_REF = ? AND C.Status =  ?  AND (C.DEACTIVATED = 0 or C.DEACTIVATED IS NULL)',['S',$CYID_REF,$BRID_REF,'Vendor','S',$CYID_REF,$BRID_REF,'A']);
		

        return view('reports.Accounts.VendorLedger.rptfrm319',compact(['objRights','objBranchGroup','objBranch','ObjVendorGroup','ObjVendor']));        
    }  

    
   public function ViewReport($request) {

    $box = $request;        
    $myValue=  array();
    parse_str($box, $myValue);
    
    if($myValue['Flag'] == 'H')
    {
        $VDG             = $myValue['VDG'];
		$VENDOR          = $myValue['VENDOR'];
        $From_Date       = $myValue['From_Date'];
        $To_Date         = $myValue['To_Date'];
        $BranchGroup     = $myValue['BranchGroup'];
        $BranchName      = $myValue['BranchName'];
        $Flag            = $myValue['Flag'];
        $CYID_REF          = Auth::user()->CYID_REF;
    }
    else
    {
        $VDG             = Session::get('VDG');
		$VENDOR          = Session::get('VENDOR');
        $From_Date       = Session::get('From_Date');
        $To_Date         = Session::get('To_Date');
        $BranchGroup     = Session::get('BranchGroup');
        $BranchName      = Session::get('BranchName');
        $Flag            = $myValue['Flag'];
        $CYID_REF          = Auth::user()->CYID_REF;

    }

        
        
        $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
   
        $result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/VendorLedgerSummary1');
        
        $reportParameters = array(
            'CYID'                      => Auth::user()->CYID_REF,
            'USERID'                    => Auth::user()->USERID,
            'FROMDATE'                  => $From_Date,
            'TODATE'                    => $To_Date,
            'BRANCHGROUP'               => $BranchGroup,
            'BRID'                      => $BranchName,
            'VendorGroup'               => $VDG, 
			'Vendor'               		=> $VENDOR, 
        );
        // dd($reportParameters);
        $CYID_REF          = Auth::user()->CYID_REF;
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
            ->setExecutionParameters($parameters);

            if($Flag == 'H')
            {
                Session::put('VDG', $VDG);
				Session::put('VENDOR', $VENDOR);
                Session::put('From_Date', $From_Date);
                Session::put('To_Date', $To_Date);
                Session::put('BranchGroup', $BranchGroup);
                Session::put('BranchName', $BranchName);

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
                return Excel::download(new VendorLedger($VENDOR,$VDG,$From_Date,$To_Date,$BranchGroup,$BranchName,$CYID_REF), 'VendorLedger.xlsx');
            }
         
     } 
    
}
