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

class RptFrm251Controller extends Controller
{
    protected $form_id = 251;
    protected $vtid_ref   = 341;  //voucher type id
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
        $ObjCustomer = DB::table('TBL_TRN_SLDP01_HDR')
        ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_MST_SUBLEDGER.SGLID','=','TBL_TRN_SLDP01_HDR.SLID_REF')
        ->where('TBL_TRN_SLDP01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLDP01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        ->select('TBL_MST_SUBLEDGER.SGLID','TBL_MST_SUBLEDGER.SGLCODE','TBL_MST_SUBLEDGER.SLNAME')
        ->distinct('TBL_MST_SUBLEDGER.SGLID')
        ->get();     
        $ObjDailySalesPlan = DB::table('TBL_TRN_SLDP01_MAT')
        ->leftJoin('TBL_TRN_SLDP01_HDR', 'TBL_TRN_SLDP01_HDR.DSPID','=','TBL_TRN_SLDP01_MAT.DSPID_REF')
        ->where('TBL_TRN_SLDP01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLDP01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        ->select('TBL_TRN_SLDP01_HDR.DSPID','TBL_TRN_SLDP01_HDR.DSPNO')
        ->distinct('TBL_TRN_SLDP01_HDR.DSPID')
        ->get();
        $ObjSalesOrer = DB::table('TBL_TRN_SLDP01_MAT')
        ->leftJoin('TBL_TRN_SLDP01_HDR', 'TBL_TRN_SLDP01_HDR.DSPID','=','TBL_TRN_SLDP01_MAT.DSPID_REF')
        ->leftJoin('TBL_TRN_SLSO01_HDR', 'TBL_TRN_SLSO01_HDR.SOID','=','TBL_TRN_SLDP01_MAT.SOID_REF')
        ->where('TBL_TRN_SLDP01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLDP01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        ->select('TBL_TRN_SLSO01_HDR.SOID','TBL_TRN_SLSO01_HDR.SONO')
        ->distinct('TBL_TRN_SLSO01_HDR.SOID')
        ->get(); 
        $ObjItemGrp = DB::table('TBL_MST_ITEMGROUP')
        ->where('TBL_MST_ITEMGROUP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_ITEMGROUP.BRID_REF','=',Auth::user()->BRID_REF)
        ->where('TBL_MST_ITEMGROUP.STATUS','=','A')
        ->select('TBL_MST_ITEMGROUP.ITEMGID','TBL_MST_ITEMGROUP.GROUPNAME')
        ->distinct('TBL_MST_ITEMGROUP.ITEMGID')
        ->get();
        $ObjItem = DB::table('TBL_TRN_SLDP01_HDR')
        ->leftJoin('TBL_TRN_SLDP01_MAT', 'TBL_TRN_SLDP01_MAT.DSPID_REF','=','TBL_TRN_SLDP01_HDR.DSPID')
        ->leftJoin('TBL_MST_ITEM', 'TBL_MST_ITEM.ITEMID','=','TBL_TRN_SLDP01_MAT.ITEMID_REF')
		->leftJoin('TBL_MST_BUSINESSUNIT', 'TBL_MST_ITEM.BUID_REF','=','TBL_MST_BUSINESSUNIT.BUID')
        ->where('TBL_TRN_SLDP01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLDP01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        ->select('TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ALPS_PART_NO','TBL_MST_BUSINESSUNIT.BUCODE','TBL_MST_BUSINESSUNIT.BUNAME')
        ->distinct('TBL_MST_ITEM.ITEMID')
        ->get();

        $company_check=$this->AlpsStatus();

        return view('reports.sales.DSPRegister.rptfrm251',compact(['objRights','objBranchGroup','objBranch','ObjCustomer',
                    'ObjItemGrp','ObjItem','ObjDailySalesPlan','ObjSalesOrer','company_check']));        
    }
    
    
   public function ViewReport($request) {

    $box = $request;        
    $myValue=  array();
    parse_str($box, $myValue);
    
    

    if($myValue['Flag'] == 'H')
    {
        $SGLID           = $myValue['SGLID'];
        $FROMDATE        = $myValue['From_Date'];
        $TODATE          = $myValue['To_Date'];
        $BRANCHGROUP     = $myValue['BranchGroup'];
        $BRID            = $myValue['BranchName'];
        $DSPNO           = $myValue['DSPNO'];
        $ITEMGID         = $myValue['ITEMGID'];
        $ITEMID          = $myValue['ITEMID'];
        $SONO            = $myValue['SONO'];
        $Flag            = $myValue['Flag'];
        $STATUS          = $myValue['STATUS'];
    }
    else
    {
        $SGLID           = Session::get('SGLID');
        $FROMDATE        = Session::get('FROMDATE');
        $TODATE          = Session::get('TODATE');
        $BRANCHGROUP     = Session::get('BRANCHGROUP');
        $BRID            = Session::get('BRID');
        $DSPNO           = Session::get('DSPNO');
        $ITEMGID         = Session::get('ITEMGID');
        $ITEMID          = Session::get('ITEMID');
        $SONO            = Session::get('SONO');
        $STATUS            = Session::get('STATUS');
        $Flag            = $myValue['Flag'];
    }

        
        
       $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
   
        $result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/DailySalesPlanRegister');
       
        $reportParameters = array(
            'CYID'                      => Auth::user()->CYID_REF,
            'USERID'                    => Auth::user()->USERID,
            'FROMDATE'                  => $FROMDATE,
            'TODATE'                    => $TODATE,
            'BRANCHGROUP'               => $BRANCHGROUP,
            'BRID'                      => $BRID,
            'CUSTOMERNO'                => $SGLID,
            'DSPNo'                     => $DSPNO,
            'ITEMGROUP'                 => $ITEMGID,
            'ITEM'                      => $ITEMID,
            'SONO'                      => $SONO,           
            'STATUS'                    => $STATUS,           
        );
       
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
            ->setExecutionParameters($parameters);

            if($Flag == 'H')
            {
                Session::put('SGLID', $SGLID);
                Session::put('FROMDATE', $FROMDATE);
                Session::put('TODATE', $TODATE);
                Session::put('BRANCHGROUP', $BRANCHGROUP);
                Session::put('BRID', $BRID);
                Session::put('DSPNO',$DSPNO);
                Session::put('ITEMGID', $ITEMGID);
                Session::put('ITEMID', $ITEMID);
                Session::put('SONO', $SONO);
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
