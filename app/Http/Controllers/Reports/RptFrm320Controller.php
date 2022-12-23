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
use App\Exports\GLTrailBalance;
use Maatwebsite\Excel\Facades\Excel;

class RptFrm320Controller extends Controller
{
    protected $form_id = 320;
    protected $vtid_ref   = 408;  //voucher type id
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
        //$ObjGeneralLedger = DB::table('TBL_MST_GENERALLEDGER')
        //->where('TBL_MST_GENERALLEDGER.CYID_REF','=',Auth::user()->CYID_REF)
       // ->where('TBL_MST_GENERALLEDGER.STATUS','=','A')
        //->select('TBL_MST_GENERALLEDGER.GLID','TBL_MST_GENERALLEDGER.GLCODE','TBL_MST_GENERALLEDGER.GLNAME')
       // ->distinct('TBL_MST_GENERALLEDGER.GLID')
       // ->get();
		
		$CYID_REF = Auth::user()->CYID_REF;
		//$BRID_REF = Auth::user()->BRID_REF;

        /* $ObjGeneralLedger = DB::select('//SELECT DISTINCT C.GLID, C.GLCODE, C.GLNAME
								FROM TBL_TRN_FJRV01_ACC AS A LEFT OUTER JOIN TBL_TRN_FJRV01_HDR AS H ON A.JVID_REF = H.JVID LEFT OUTER JOIN
              TBL_MST_GENERALLEDGER AS C ON A.GLID_REF = C.GLID  WHERE H.CYID_REF = ? AND A.SGLID_REF = ? AND H.STATUS = ?
				UNION
			  SELECT DISTINCT C.GLID, C.GLCODE, C.GLNAME FROM TBL_MST_GENERALLEDGER AS C  JOIN TBL_MST_GLOPENING_LEDGER AS O
			  ON C.GLID  = O.GLID_REF WHERE C.GLID NOT IN (SELECT GLID_REF FROM  TBL_TRN_FJRV01_ACC A LEFT JOIN TBL_TRN_FJRV01_HDR H ON A.JVID_REF=H.JVID 
			 AND A.SGLID_REF= ?  AND H.CYID_REF = C.CYID_REF AND H.BRID_REF = C.BRID_REF WHERE H.CYID_REF = ? AND A.SGLID_REF = ? AND H.STATUS = ?) 
			 AND  C.CYID_REF = ? AND C.Status =  ?  AND (C.DEACTIVATED = 0 or C.DEACTIVATED IS NULL)',[$CYID_REF,'G','A','G',$CYID_REF,'G','A',$CYID_REF,'A']); */
		$ObjGeneralLedger = DB::select('SELECT DISTINCT C.GLID, C.GLCODE, C.GLNAME FROM TBL_MST_GENERALLEDGER C WHERE C.CYID_REF = ? AND C.Status =  ?  AND (C.DEACTIVATED = 0 or C.DEACTIVATED IS NULL)',[$CYID_REF,'A']);
        
        $ObjAccountGroup = DB::table('TBL_MST_ACCOUNTGROUP')
        ->Join('TBL_MST_ACCOUNTSUBGROUP', 'TBL_MST_ACCOUNTGROUP.AGID','=','TBL_MST_ACCOUNTSUBGROUP.AGID_REF')
        ->where('TBL_MST_ACCOUNTGROUP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_ACCOUNTGROUP.BRID_REF','=',Auth::user()->BRID_REF)
        ->where('TBL_MST_ACCOUNTGROUP.STATUS','=','A')
        ->select('TBL_MST_ACCOUNTSUBGROUP.ASGID AS AGID','TBL_MST_ACCOUNTSUBGROUP.ASGCODE AS AGCODE','TBL_MST_ACCOUNTSUBGROUP.ASGNAME AS AGNAME')
        ->distinct('TBL_MST_ACCOUNTSUBGROUP.ASGID')
        ->get();

        return view('reports.Accounts.TrailBalance.rptfrm320',compact(['objRights','objBranchGroup','objBranch','ObjGeneralLedger','ObjAccountGroup']));        
    }  

    
   public function ViewReport($request) {

    $box = $request;        
    $myValue=  array();
    parse_str($box, $myValue);
    
    if($myValue['Flag'] == 'H')
    {
        $GLID            = $myValue['GLID'];
        $AGID            = $myValue['AGID'];
        $From_Date       = $myValue['From_Date'];
        $To_Date         = $myValue['To_Date'];
        $BranchGroup     = $myValue['BranchGroup'];
        $BranchName      = $myValue['BranchName'];
        $Flag            = $myValue['Flag'];
		$LEVELID         = $myValue['LEVELID'];       
        $CYID_REF        = Auth::user()->CYID_REF;
    }
    else
    {
        $GLID            = Session::get('GLID');
        $AGID            = Session::get('AGID');
        $From_Date       = Session::get('From_Date');
        $To_Date         = Session::get('To_Date');
        $BranchGroup     = Session::get('BranchGroup');
        $BranchName      = Session::get('BranchName');
		$LEVELID      	 = Session::get('LEVELID');
        $Flag            = $myValue['Flag'];
        $CYID_REF        = Auth::user()->CYID_REF;
    }

        
        
        $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
   
        $result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/GLTRAILBLANCE_4');
        
        $reportParameters = array(
            'p_cyid'                    => Auth::user()->CYID_REF,
            'p_userid'                  => Auth::user()->USERID,
            'p_frdt'                    => $From_Date,
            'p_todt'                    => $To_Date,
            'p_branchgroup'             => $BranchGroup,
            'p_brid'                    => $BranchName,
            'p_ACCOUNTGROUP'            => $AGID, 
            'p_gl'                      => $GLID,
			'p_level'                   => $LEVELID,
        );
        // dd($reportParameters);
        $CYID_REF          = Auth::user()->CYID_REF;
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
            ->setExecutionParameters($parameters);

            if($Flag == 'H')
            {
                Session::put('GLID', $GLID);
                Session::put('AGID', $AGID);
                Session::put('From_Date', $From_Date);
                Session::put('To_Date', $To_Date);
                Session::put('BranchGroup', $BranchGroup);
                Session::put('BranchName', $BranchName);
				Session::put('LEVELID', $LEVELID);

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
					//dd($reportParameters);
                return Excel::download(new GLTrailBalance($GLID,$AGID,$From_Date,$To_Date,$BranchName,$CYID_REF), 'GLTrailBalance.xlsx');
            }
         
     } 
    
}
