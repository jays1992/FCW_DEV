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
use App\Exports\StockLedger;
use App\Exports\StockLedgerWithoutStore;
use Maatwebsite\Excel\Facades\Excel;

class RptFrm298Controller extends Controller
{
    protected $form_id = 298;
    protected $vtid_ref   = 388;  //voucher type id
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

        $CYID_REF=Auth::user()->CYID_REF; 
        $BRID_REF=Auth::user()->BRID_REF; 
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

        $ObjItemGrp = DB::table('TBL_MST_ITEMGROUP')
        ->where('TBL_MST_ITEMGROUP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_ITEMGROUP.BRID_REF','=',Auth::user()->BRID_REF)
        ->where('TBL_MST_ITEMGROUP.STATUS','=','A')
        ->select('TBL_MST_ITEMGROUP.ITEMGID','TBL_MST_ITEMGROUP.GROUPNAME')
        ->distinct('TBL_MST_ITEMGROUP.ITEMGID')
        ->get();

        $ObjStore = DB::select("SELECT DISTINCT STID,STCODE, NAME
        FROM            TBL_MST_STORE
        WHERE       STATUS = 'A' AND CYID_REF=$CYID_REF AND BRID_REF=$BRID_REF AND (DEACTIVATED=0 OR DEACTIVATED IS NULL)");





        $ObjItem = DB::table('TBL_MST_ITEM')
		->leftJoin('TBL_MST_BUSINESSUNIT', 'TBL_MST_ITEM.BUID_REF','=','TBL_MST_BUSINESSUNIT.BUID')
        ->where('TBL_MST_ITEM.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_ITEM.BRID_REF','=',Auth::user()->BRID_REF)
        ->where('TBL_MST_ITEM.STATUS','=','A')
        ->where('TBL_MST_ITEM.ITEM_TYPE','=','I-Inventory')
        ->select('TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ALPS_PART_NO','TBL_MST_BUSINESSUNIT.BUCODE','TBL_MST_BUSINESSUNIT.BUNAME')
        ->distinct('TBL_MST_ITEM.ITEMID')
        ->get();


        $company_check=$this->AlpsStatus();



        return view('reports.inventory.Stock_Ledger.rptfrm298',compact(['objRights','objBranchGroup','objBranch',
                    'ObjItemGrp','ObjItem','company_check','ObjStore']));        
    }  

    
   public function ViewReport($request) {

    $box = $request;   
    $myValue=  array();
    parse_str($box, $myValue);
    
    if($myValue['Flag'] == 'H')
    {
        $From_Date       = $myValue['From_Date'];
        $To_Date         = $myValue['To_Date'];
        $BranchGroup     = $myValue['BranchGroup'];
        $BranchName      = $myValue['BranchName'];
        $ITEMGID         = $myValue['ITEMGID'];
        $ITEMID          = $myValue['ITEMID'];
        $Flag            = $myValue['Flag'];
        $Store           = $myValue['STOREID'];
        $ReportType      = $myValue['ReportType'];
        $CYID_REF        = Auth::user()->CYID_REF;
    }
    else
    {
    
        $From_Date       = Session::get('From_Date');
        $To_Date         = Session::get('To_Date');
        $BranchGroup     = Session::get('BranchGroup');
        $BranchName      = Session::get('BranchName');
        $ITEMGID         = Session::get('ITEMGID');
        $ITEMID          = Session::get('ITEMID');
        $Store           = Session::get('Store');
        $ReportType      = Session::get('ReportType');
        $Flag            = $myValue['Flag'];
        $CYID_REF        = Auth::user()->CYID_REF;
    }

    //dd($ReportType); 
        
        
 
    $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
   
    $result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/StockLedgerReportAmount');

        
        $reportParameters = array(
            'CYID'                      => Auth::user()->CYID_REF,
            'UID'                       => Auth::user()->USERID,
            'FROMDATE'                  => $From_Date,
            'TODATE'                    => $To_Date,
            'BGID'                      => $BranchGroup,
            'BRID'                      => $BranchName,
            'ITEMGROUP'                 => $ITEMGID,
            'ITEM'                      => $ITEMID,
            'Store'                     => $Store,
            'ReportType'                => $ReportType,
               
        );
       
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        $CYID_REF          = Auth::user()->CYID_REF;
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
            ->setExecutionParameters($parameters);

            if($Flag == 'H')
            {

                Session::put('From_Date', $From_Date);
                Session::put('To_Date', $To_Date);
                Session::put('BranchGroup', $BranchGroup);
                Session::put('BranchName', $BranchName);
                Session::put('ITEMGID', $ITEMGID);
                Session::put('ITEMID', $ITEMID);
                Session::put('Store', $Store);
                Session::put('ReportType', $ReportType);
             
                
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
                if($ReportType=="WithStore"){                
                    return Excel::download(new StockLedger($From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMGID,$ITEMID,$CYID_REF,$Store), 'StockLedger.xlsx');
                }else if($ReportType=="WithoutStore"){
                    return Excel::download(new StockLedgerWithoutStore($From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMGID,$ITEMID,$CYID_REF), 'StockLedger.xlsx');
                }
        }
         
     }

     public function AlpsStatus(){
        $COMPANY_NAME   =   DB::table('TBL_MST_COMPANY')->where('STATUS','=','A')->where('CYID','=',Auth::user()->CYID_REF)->select('TBL_MST_COMPANY.NAME')->first()->NAME;
      //  $COMPANY_NAME="ALPS"; 
        return $hidden         =   strpos($COMPANY_NAME,"ALPS")!== false?'show':'hide'; 
    }

    
}
