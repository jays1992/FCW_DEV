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

class RptFrm353Controller extends Controller
{
    protected $form_id = 353;
    protected $vtid_ref   = 439;  //voucher type id
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


        $ObjSQNO = DB::table('TBL_TRN_SLQT01_HDR')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('BRID_REF','=',Auth::user()->BRID_REF)
                    ->where('STATUS','=','A')
                    ->select('SQID','SQNO')
                    ->distinct('SQID')
                    ->get();


        return view('reports.sales.Sales_Quotation_Print.rptfrm353',compact(['objRights','ObjSQNO']));        
    }  

    
   public function ViewReport($request) {

    $box = $request;   
    $myValue=  array();
    parse_str($box, $myValue);
    
    if($myValue['Flag'] == 'H')
    {
        $SQNO       = $myValue['SQID'];
        $Flag            = $myValue['Flag'];
      
    }
    else
    {    

        $SQNO          = Session::get('SQNO');
        $Flag            = $myValue['Flag'];
    }

        


    $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
   
    $result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/SQPrint');

        
        $reportParameters = array(
          //  'CYID'                      => Auth::user()->CYID_REF,
         //   'UID'                    => Auth::user()->USERID,
            'SQNO'                  => $SQNO,          
               
        );
       
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
            ->setExecutionParameters($parameters);

            if($Flag == 'H')
            {

                Session::put('SQNO', $SQNO);              
             
                
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

    
}
