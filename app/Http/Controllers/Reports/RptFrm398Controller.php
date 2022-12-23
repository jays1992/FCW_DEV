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

use App\Exports\Vendor_Aging;
use Maatwebsite\Excel\Facades\Excel;

class RptFrm398Controller extends Controller
{
    protected $form_id = 398;
    protected $vtid_ref   = 482;  //voucher type id

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
        


        return view('reports.Accounts.Vendor_Aging.rptfrm398',compact(['objRights','objBranchGroup','objBranch','ObjVendorGroup','ObjVendor']));        
    }  


    function filterCustomerData(&$str) {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
        }
    
  

    
   public function ViewReport($request) {

    $box = $request;        
    $myValue=  array();
    parse_str($box, $myValue);
    
    if($myValue['Flag'] == 'H')
    {       
        $VD               = $myValue['VD'];
        $VDG              = $myValue['VDG'];
        $From_Date        = $myValue['From_Date'];        
        $BranchGroup      = $myValue['BranchGroup'];
        $BranchName       = $myValue['BranchName'];
        $Flag             = $myValue['Flag'];
        $REPORT_TYPE      = $myValue['REPORT_TYPE'];
        $REPORT_BASIS     = $myValue['REPORT_BASIS'];
        $CYID_REF         = Auth::user()->CYID_REF;
    }
    else
    { 
        $VD                = Session::get('VD');
        $VDG               = Session::get('VDG');
        $From_Date         = Session::get('From_Date');       
        $BranchGroup       = Session::get('BranchGroup');
        $BranchName        = Session::get('BranchName');
        $REPORT_TYPE       = Session::get('REPORT_TYPE');
        $REPORT_BASIS      = Session::get('REPORT_BASIS');
        $Flag              = $myValue['Flag'];
        $CYID_REF          = Auth::user()->CYID_REF;
    }




            $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
   
            $result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/VendorAging');

                     
        
        $reportParameters = array(
            'CYID'                      => Auth::user()->CYID_REF,
            'USERID'                    => Auth::user()->USERID,
            'FROMDATE'                  => $From_Date,
            'BRANCHGROUP'               => $BranchGroup,
            'BRID'                      => $BranchName,
            'REPORT_TYPE'               => $REPORT_TYPE,          
            'REPORT_BASIS'              => $REPORT_BASIS,          
            'Vendor'                    => $VD,          
            'VendorGroup'               => $VDG,          
        );
        // dd($reportParameters);
        $CYID_REF          = Auth::user()->CYID_REF;
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
            ->setExecutionParameters($parameters);

            if($Flag == 'H')
            {
       
             
                Session::put('VD', $VD);
                Session::put('VDG', $VDG);
                Session::put('From_Date', $From_Date);              
                Session::put('BranchGroup', $BranchGroup);
                Session::put('BranchName', $BranchName);
                Session::put('REPORT_TYPE', $REPORT_TYPE);
                Session::put('REPORT_BASIS', $REPORT_BASIS);

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

        //         $BGID_REF=NULL;
        //         $BRID_REF='4';
        //         $CYID_REF='6';
        //         //$ID="8";
        //         $ASONDATE='2022-01-31';
        //         $REPORTBASIS='DUE_DATE';
        //         $REPORTTYPE='DetailedA';
        //         $VGID_REF='1';




        //         $count=count($VD); 
        //         $ID = '';
        //         if(!empty($VD)){
        //             foreach ($VD as $key=>$cRow)
        //             {          
        //                 if($key==$count-1){
        //                   $sep=''; 
        //                 }else{
        //                   $sep=','; 
        //                 }           
          
        //                     $row3 = "'";
          
        //                     $row3 = $row3.$cRow.$row3.$sep;
        //                     $ID =  $ID .$row3;   
          
          
        //             }
        //         }   


        //         //dd($row4);   
        //        // dd($ID); 
         
        //         $log_data = [ 
        //           $CYID_REF,$BGID_REF,$BRID_REF,$ID,$ASONDATE,$REPORTBASIS,$REPORTTYPE,$VGID_REF
        //       ];
          
              
        //       $sp_result = DB::select('EXEC SP_VENDOR_AGEING ?,?,?,?,?,?,?,?', $log_data);  
                
        //       dd($sp_result); 
        
        //     $file_name = "Vendor Aging Report";
        //     $args = array( 'role' => 'client',
        //        'meta_query' => array( array(
        //            'key' => '_dt_transaction_archived',
        //            'compare' => 'NOT EXISTS'
        //        ) ),
        //        'order' => 'DESC',
        //        'orderby' => 'ID'
        //     );
        //     //$users = get_users( $args );
        //     $file_ending = "xls";  
        //     header( "Content-Type: application/xls" );
        //     header( "Content-Disposition: attachment; filename=$file_name.$file_ending" );
        //     header( "Pragma: no-cache" );
        //     header( "Expires: 0" );
            
        //     /*******Start of Formatting for Excel*******/
            
        //     // define separator (defines columns in excel & tabs in word)
        //     $sep = "\t"; //tabbed character
        //     // start of printing column names as names of MySQL fields
            
        //     print( "SOURCETYPE" . $sep );
        //     print( "DOCNO" . $sep );
        //     print( "DOCDT" . $sep );
        //     print( "DOCAMT" . $sep );
        //     print( "PAIDAMT" . $sep );
        //     print( "CREDITDAY" . $sep );
        //     print( "VCODE" . $sep );
        //     print( "VENDORNAME" . $sep );
        //     print( "SAP_VENDOR_CODE" . $sep );
        //     print( "SAP_VENDOR_NAME1" . $sep );
        //     print( "BRANCHGROUP" . $sep );
        //     print( "BRANCH" . $sep );
        //     print( "BALANCEAMT" . $sep );
        //     print( "RECEIVEDAMT" . $sep );
        //     print( "OVERDUEAMT" . $sep );
        //     print( "LESSTHAN30" . $sep );
        //     print( "LESSTHAN60" . $sep );
        //     print( "LESSTHAN90" . $sep );
        //     print( "LESSTHAN120" . $sep );
        //     print( "LESSTHAN180" . $sep );
        //     print( "LESSTHAN240" . $sep );
        //     print( "LESSTHAN300" . $sep );
        //     print( "GREATERTHAN300" . $sep );
        
        //   //  print( "\n" );
        //     // end of printing column names
            
        //     // start foreach loop to get data
        //     $schema_insert = "";
            
        //     foreach ($sp_result as $user) {
        //             if ( $user ) {
        //                 if($user->SOURCETYPE!=''){
        //                 $schema_insert = "$user->SOURCETYPE" . $sep;
        //             }else{
        //                 $schema_insert = "-" . $sep;
        //             }
        //             $schema_insert .= "$user->DOCNO" . $sep;
        //             $schema_insert .= "$user->DOCDT" . $sep;
        //             $schema_insert .= "$user->DOCAMT" . $sep;
        //             $schema_insert .= "$user->PAIDAMT" . $sep;
        //             $schema_insert .= "$user->CREDITDAY" . $sep;
        //             $schema_insert .= "$user->VCODE" . $sep;
        //             $schema_insert .= "$user->VENDORNAME" . $sep;
        //             $schema_insert .= "$user->SAP_VENDOR_CODE" . $sep;
        //             $schema_insert .= "$user->SAP_VENDOR_NAME1" . $sep;
        //             $schema_insert .= "$user->BRANCHGROUP" . $sep;
        //             $schema_insert .= "$user->BRANCH" . $sep;
        //             $schema_insert .= "$user->BALANCEAMT" . $sep;
        //             $schema_insert .= "$user->RECEIVEDAMT" . $sep;
        //             $schema_insert .= "$user->OVERDUEAMT" . $sep;
        //             $schema_insert .= "$user->LESSTHAN30" . $sep;
        //             $schema_insert .= "$user->LESSTHAN60" . $sep;
        //             $schema_insert .= "$user->LESSTHAN90" . $sep;
        //             $schema_insert .= "$user->LESSTHAN120" . $sep;
        //             $schema_insert .= "$user->LESSTHAN180" . $sep;
        //             $schema_insert .= "$user->LESSTHAN240" . $sep;
        //             $schema_insert .= "$user->LESSTHAN300" . $sep;
        //             $schema_insert .= "$user->GREATERTHAN300" . $sep;
            
        //             print "\n";
        //             $schema_insert = str_replace( $sep . "$", "", $schema_insert );
        //             $schema_insert = preg_replace( "/\r\n|\n\r|\n|\r/", " ", $schema_insert );
        //             $schema_insert .= "\t";
        //             print( trim( $schema_insert ) );
        //         }
        //     }
        
        // exit(); 
               
            }
         
     } 
    
}
