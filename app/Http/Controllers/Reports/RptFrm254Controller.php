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

class RptFrm254Controller extends Controller
{
    protected $form_id = 254;
    protected $vtid_ref   = 344;  //voucher type id
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

        $objFYear = DB::table('TBL_MST_FYEAR')
        ->where('TBL_MST_FYEAR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_FYEAR.STATUS','=','A')
        ->select('TBL_MST_FYEAR.*')
        ->get();

        $objEnquiry = DB::table('TBL_TRN_SLEQ01_HDR')
        ->where('TBL_TRN_SLEQ01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLEQ01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        ->where('TBL_TRN_SLEQ01_HDR.STATUS','=','A')
        ->select('TBL_TRN_SLEQ01_HDR.*')
        ->get();

        $objItemGrp = DB::table('TBL_MST_ITEMGROUP')
        ->where('TBL_MST_ITEMGROUP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_ITEMGROUP.BRID_REF','=',Auth::user()->BRID_REF)
        ->where('TBL_MST_ITEMGROUP.STATUS','=','A')
        ->select('TBL_MST_ITEMGROUP.*')
        ->get();

        $objItem = DB::table('TBL_MST_ITEM')
		->leftJoin('TBL_MST_BUSINESSUNIT', 'TBL_MST_ITEM.BUID_REF','=','TBL_MST_BUSINESSUNIT.BUID')
        ->where('TBL_MST_ITEM.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_ITEM.BRID_REF','=',Auth::user()->BRID_REF)
        ->where('TBL_MST_ITEM.STATUS','=','A')
        ->select('TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ALPS_PART_NO','TBL_MST_BUSINESSUNIT.BUCODE','TBL_MST_BUSINESSUNIT.BUNAME')
        ->get();

        $objSubGL = DB::table('TBL_MST_CUSTOMER')
        ->where('TBL_MST_CUSTOMER.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_CUSTOMER.BRID_REF','=',Auth::user()->BRID_REF)
        ->where('TBL_MST_CUSTOMER.STATUS','=','A')
        ->select('TBL_MST_CUSTOMER.*')
        ->get();

        $company_check=$this->AlpsStatus();

        return view('reports.sales.SalesEnquiryRegister.rptfrm254',compact(['objRights','objBranchGroup','objBranch','objFYear','objEnquiry',
        'objItemGrp','objItem','objSubGL','company_check']));        
    }

    // public function GetState(Request $request)
    // {
    //     $Fromdate = $request['From_Date']; $Todate = $request['To_Date'];
    //     $ObjGL = DB::table('TBL_TRN_SLSI01_HDR')
    //     ->leftJoin('TBL_MST_CUSTOMERLOCATION', 'TBL_MST_CUSTOMERLOCATION.CLID','=','TBL_TRN_SLSI01_HDR.SHIPTO')
    //     ->leftJoin('TBL_MST_STATE', 'TBL_MST_CUSTOMERLOCATION.STID_REF','=','TBL_MST_STATE.STID')
    //     ->where('TBL_TRN_SLSI01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
    //     ->where('TBL_TRN_SLSI01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
    //     ->where('TBL_TRN_SLSI01_HDR.STATUS','=','A')
    //     ->where('TBL_TRN_SLSI01_HDR.SIDT','>=',$Fromdate)
    //     ->where('TBL_TRN_SLSI01_HDR.SIDT','<=',$Todate)
    //     ->select('TBL_MST_STATE.STID','TBL_MST_STATE.STCODE','TBL_MST_STATE.NAME')
    //     ->distinct('TBL_MST_CITY.STID')
    //     ->get();
        
    //         $row1 = '';
    //         $row1 = $row1.'<select name="STID[]" id="STID" class="form-control selectpicker" multiple data-live-search="true"  >';
    //         $row2 = '';
    //         $row2 = $row2.'</select>';
    //         $row4 = '';
    //     if(!empty($ObjGL)){
    //         foreach ($ObjGL as $index=>$Row)
    //         {
    //         $row3 = '';
    //         $row3 = $row3.'<option value="'.$Row->STID.'">'.$Row->STCODE."-".$Row->NAME.'</option>';
    //         $row4 =  $row4 .$row3;
    //         }
    //         $row = '';
    //         $row = $row1 . $row4 . $row2; 
    //         echo $row;
    //         }else{
    //             echo '<select id="STID" name="STID[]" class="form-control selectpicker" multiple data-live-search="true" ></select>';
    //         }
    //         exit();
            
    // }
    
   public function ViewReport($request) {

    $box = $request;        
    $myValue=  array();
    parse_str($box, $myValue);
    
    $SUBGL          = $myValue['SUBGL'];
    $From_Date      = $myValue['From_Date'];
    $To_Date        = $myValue['To_Date'];
    $BranchGroup    = $myValue['BranchGroup'];
    $BranchName     = $myValue['BranchName'];
    $FinancialYear  = $myValue['FinancialYear'];
    $Format         = $myValue['Format'];
    $ENQUIRY        = $myValue['ENQUIRY'];
    $ITEMGRP        = $myValue['ITEMGRP'];
    $ITEM           = $myValue['ITEM'];

        
        $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
   
        $result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/Sales_Enquiry');
        
        $reportParameters = array(
            'p_cyid'            => Auth::user()->CYID_REF,
            'p_userid'          => Auth::user()->USERID,
            'FrDate'            => $From_Date,
            'ToDate'            => $To_Date,
            'SL'                => $SUBGL,
            'p_branchgrpup'     => $BranchGroup,
            'p_branch'          => $BranchName,
            'p_fyear'           => $FinancialYear,   
            'EnqNo'             => $ENQUIRY,
            'Item'              => $ITEM,
            'Itemgrp'           => $ITEMGRP, 
        );
       
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
            ->setExecutionParameters($parameters);

            if($Format == 'HTML')
            {
                $output = $ssrs->render('HTML4.0'); // PDF | XML | CSV
                echo $output;
            }
            else if($Format == 'PDF')
            {
                $output = $ssrs->render('PDF'); // PDF | XML | CSV | HTML4.0
                return $output->download('Report.pdf');
            }
            else if($Format == 'EXCEL')
            {
                $output = $ssrs->render('XLS'); // PDF | XML | CSV | HTML4.0
                return $output->download('Report.xls');
            }
            else
            {
                $output = $ssrs->render('HTML4.0'); // PDF | XML | CSV
                echo $output;
            }
         
     }  
     
     
     public function AlpsStatus(){
        $COMPANY_NAME   =   DB::table('TBL_MST_COMPANY')->where('STATUS','=','A')->where('CYID','=',Auth::user()->CYID_REF)->select('TBL_MST_COMPANY.NAME')->first()->NAME;
      //  $COMPANY_NAME="ALPS"; 
        return $hidden         =   strpos($COMPANY_NAME,"ALPS")!== false?'show':'hide'; 
    }
    
}
