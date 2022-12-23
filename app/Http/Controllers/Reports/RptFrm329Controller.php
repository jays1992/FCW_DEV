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
use App\Exports\IPORegister;
use Maatwebsite\Excel\Facades\Excel;

class RptFrm329Controller extends Controller
{
    protected $form_id = 329;
    protected $vtid_ref   = 417;  //voucher type id
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


        $ObjVendor = DB::table('TBL_TRN_IPO_HDR')
                        ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_MST_SUBLEDGER.SGLID','=','TBL_TRN_IPO_HDR.VID_REF')
                        ->where('TBL_TRN_IPO_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_TRN_IPO_HDR.BRID_REF','=',Auth::user()->BRID_REF)
                       // ->where('TBL_MST_SUBLEDGER.BELONGS_TO','=','Vendor')
                        ->select('TBL_MST_SUBLEDGER.SGLID','TBL_MST_SUBLEDGER.SGLCODE','TBL_MST_SUBLEDGER.SLNAME')
                        ->distinct('TBL_MST_SUBLEDGER.SGLID')
                        ->get();  

                      //  dd($ObjVendor); 

        $ObjItemGrp = DB::table('TBL_MST_ITEMGROUP')
                        ->where('TBL_MST_ITEMGROUP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_ITEMGROUP.BRID_REF','=',Auth::user()->BRID_REF)
                        ->where('TBL_MST_ITEMGROUP.STATUS','=','A')
                        ->select('TBL_MST_ITEMGROUP.ITEMGID','TBL_MST_ITEMGROUP.GROUPNAME')
                        ->distinct('TBL_MST_ITEMGROUP.ITEMGID')
                        ->get();

        $ObjItem = DB::table('TBL_TRN_IPO_HDR')
                        ->leftJoin('TBL_TRN_IPO_MAT', 'TBL_TRN_IPO_MAT.IPO_ID_REF','=','TBL_TRN_IPO_HDR.IPO_ID')
                        ->leftJoin('TBL_MST_ITEM', 'TBL_MST_ITEM.ITEMID','=','TBL_TRN_IPO_MAT.ITEMID_REF')
                        ->leftJoin('TBL_MST_BUSINESSUNIT', 'TBL_MST_BUSINESSUNIT.BUID','=','TBL_MST_ITEM.BUID_REF')
                        ->where('TBL_TRN_IPO_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_TRN_IPO_HDR.BRID_REF','=',Auth::user()->BRID_REF)
                        ->select('TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ALPS_PART_NO','TBL_MST_BUSINESSUNIT.BUCODE','TBL_MST_BUSINESSUNIT.BUNAME')
                        ->distinct('TBL_MST_ITEM.ITEMID')
                        ->get();

        $company_check=$this->AlpsStatus();

        return view('reports.purchase.IPO_Register.rptfrm329',compact(['objRights','objBranchGroup','objBranch',
                    'ObjItemGrp','ObjItem','ObjVendor','company_check']));        
    }  

    
   public function ViewReport($request) {

    $box = $request;        
    $myValue=  array();
    parse_str($box, $myValue);
    
    if($myValue['Flag'] == 'H')
    {
        $SGLID           = $myValue['SGLID'];
        $From_Date       = $myValue['From_Date'];
        $To_Date         = $myValue['To_Date'];
        $BranchGroup     = $myValue['BranchGroup'];
        $BranchName      = $myValue['BranchName'];
        $ITEMGID         = $myValue['ITEMGID'];
        $ITEMID          = $myValue['ITEMID'];
        $Flag            = $myValue['Flag'];
        $Quantity        = $myValue['Quantity'];
        $STATUS            = $myValue['STATUS'];        
        $CYID_REF          = Auth::user()->CYID_REF;
    }
    else
    {
        $SGLID           = Session::get('SGLID');
        $From_Date       = Session::get('From_Date');
        $To_Date         = Session::get('To_Date');
        $BranchGroup     = Session::get('BranchGroup');
        $BranchName      = Session::get('BranchName');
        $ITEMGID         = Session::get('ITEMGID');
        $ITEMID          = Session::get('ITEMID');
        $Quantity        = Session::get('Quantity');
        $STATUS          = Session::get('STATUS');
        $Flag            = $myValue['Flag'];        
        $CYID_REF          = Auth::user()->CYID_REF;
    }

       // dd($QUANTITY); 
        
            $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
   
            $result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/IPO_register');
      
        
        $reportParameters = array(
            'CYID'                      => Auth::user()->CYID_REF,
            'USERID'                    => Auth::user()->USERID,
            'FROMDATE'                  => $From_Date,
            'TODATE'                    => $To_Date,
            'BRANCHGROUP'               => $BranchGroup,
            'BRID'                      => $BranchName,
            'VENDORNO'                  => $SGLID,
            'ITEMGROUP'                 => $ITEMGID,
            'ITEM'                      => $ITEMID,
            'Quantity'                  => $Quantity,
            'STATUS'                    => $STATUS,
        );
        


       // dd($reportParameters); 
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
                Session::put('ITEMGID', $ITEMGID);
                Session::put('ITEMID', $ITEMID);
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
                return Excel::download(new IPORegister($SGLID,$From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMGID,$ITEMID,$STATUS,$CYID_REF), 'IPORegister.xlsx');
            }
         
     }

     public function AlpsStatus(){
        $COMPANY_NAME   =   DB::table('TBL_MST_COMPANY')->where('STATUS','=','A')->where('CYID','=',Auth::user()->CYID_REF)->select('TBL_MST_COMPANY.NAME')->first()->NAME;
      //  $COMPANY_NAME="ALPS"; 
        return $hidden         =   strpos($COMPANY_NAME,"ALPS")!== false?'show':'hide'; 
    }

    
}
