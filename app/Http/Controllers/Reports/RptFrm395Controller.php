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

use App\Exports\TDS_Vendor_Detail;
use App\Exports\TDS_Customer_Detail;
use Maatwebsite\Excel\Facades\Excel;

class RptFrm395Controller extends Controller
{
    protected $form_id = 395;
    protected $vtid_ref   = 479;  //voucher type id

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
                        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
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
                        ->distinct('TBL_MST_BRANCH.BID')
                        ->get(); 

        $ObjVendorGroup = DB::table('TBL_MST_VENDORGROUP')
                        ->where('TBL_MST_VENDORGROUP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_VENDORGROUP.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_MST_VENDORGROUP.STATUS','=','A')
                        ->select('TBL_MST_VENDORGROUP.VGID','TBL_MST_VENDORGROUP.VGCODE','TBL_MST_VENDORGROUP.DESCRIPTIONS')
                        ->distinct('TBL_MST_VENDORGROUP.VGID')
                        ->get(); 

                        

        $CYID_REF = Auth::user()->CYID_REF;
		$BRID_REF = Auth::user()->BRID_REF;

        $ObjVendor = DB::select("SELECT        
        DISTINCT SGLID, SGLCODE, SLNAME
        FROM            TBL_MST_SUBLEDGER
        WHERE       STATUS = 'A' AND CYID_REF=$CYID_REF AND(DEACTIVATED=0 OR DEACTIVATED IS NULL) AND BELONGS_TO='Customer' AND BRID_REF=$BRID_REF");
		
                        

                       // dd($ObjVendor);                       
        


        return view('reports.Accounts.TDS.rptfrm395',compact(['objRights','objBranchGroup','objBranch','ObjVendorGroup','ObjVendor']));        
    }  

    
    public function get_customervendor(Request $request){    
        $CYID_REF = Auth::user()->CYID_REF;
		$BRID_REF = Auth::user()->BRID_REF;    

        $mode =   $request['mode'];
       

        if($mode=='VENDOR'){

        $objVendorCustomer = DB::select("SELECT        
        DISTINCT SGLID, SGLCODE, SLNAME
        FROM            TBL_MST_SUBLEDGER
        WHERE       STATUS = 'A' AND CYID_REF=$CYID_REF AND(DEACTIVATED=0 OR DEACTIVATED IS NULL) AND BELONGS_TO='Vendor' AND BRID_REF=$BRID_REF");
        }else{
            $objVendorCustomer = DB::select("SELECT        
            DISTINCT SGLID, SGLCODE, SLNAME
            FROM            TBL_MST_SUBLEDGER
            WHERE       STATUS = 'A' AND CYID_REF=$CYID_REF AND(DEACTIVATED=0 OR DEACTIVATED IS NULL) AND BELONGS_TO='Customer' AND BRID_REF=$BRID_REF");
        }

//dd($objVendorCustomer); 

       

        if(!empty($objVendorCustomer)){
           
            foreach ($objVendorCustomer as $cindex=>$cRow)
            { 
               echo '<option value="'.$cRow->SGLID.'" selected>'.$cRow->SGLCODE.'-'.$cRow->SLNAME.'</option>';
            }
     
            }
            else
            {
                echo '<option value="">No record found</option>';
            }
            exit();
    }



    
   public function ViewReport($request) {

    $box = $request;        
    $myValue=  array();
    parse_str($box, $myValue);
    
    if($myValue['Flag'] == 'H')
    {
        $MODE             = $myValue['MODE'];
        $VD              = $myValue['VD'];
        $From_Date       = $myValue['From_Date'];
        $To_Date         = $myValue['To_Date'];
        $BranchGroup     = $myValue['BranchGroup'];
        $BranchName      = $myValue['BranchName'];
        $Flag            = $myValue['Flag'];
        $CYID_REF          = Auth::user()->CYID_REF;
    }
    else
    {
        $MODE             = Session::get('MODE');
        $VD              = Session::get('VD');
        $From_Date       = Session::get('From_Date');
        $To_Date         = Session::get('To_Date');
        $BranchGroup     = Session::get('BranchGroup');
        $BranchName      = Session::get('BranchName');
        $Flag            = $myValue['Flag'];
        $CYID_REF          = Auth::user()->CYID_REF;
    }

        

            $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
   
            $result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/TDS_VendorCustomer');

                     
        
        $reportParameters = array(
            'CYID'                      => Auth::user()->CYID_REF,
            'USERID'                    => Auth::user()->USERID,
            'FROMDATE'                  => $From_Date,
            'TODATE'                    => $To_Date,
            'BRANCHGROUP'               => $BranchGroup,
            'BRID'                      => $BranchName,
            'MODE'               => $MODE,          
            'Vendor'                    => $VD,          
        );
        // dd($reportParameters);
        $CYID_REF          = Auth::user()->CYID_REF;
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
            ->setExecutionParameters($parameters);

            if($Flag == 'H')
            {
                Session::put('MODE', $MODE);
                Session::put('VD', $VD);
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
                if($MODE=='VENDOR'){
                return Excel::download(new TDS_Vendor_Detail($VD,$From_Date,$To_Date,$BranchName,$CYID_REF), 'TDS_Vendor_Detail.xlsx');
                }else if($MODE=='CUSTOMER'){
                return Excel::download(new TDS_Customer_Detail($VD,$From_Date,$To_Date,$BranchName,$CYID_REF), 'TDS_Customer_Detail.xlsx');
                }
            }
         
     } 
    
}
