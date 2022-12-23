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
use App\Exports\RGPRegister;
use Maatwebsite\Excel\Facades\Excel;


class RptFrm452Controller extends Controller
{
    protected $form_id    = 452;
    protected $vtid_ref   = 522;  //voucher type id
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
        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');     

        $objRights = DB::table('TBL_MST_USERROLMAP')
                        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Auth::user()->BRID_REF)
                        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Auth::user()->FYID_REF)
                        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                        ->first();
        

        $ObjPayPeriod           =       DB::select("SELECT PAYPERIODID,PAY_PERIOD_CODE,PAY_PERIOD_DESC FROM TBL_MST_PAY_PERIOD
                                        WHERE CYID_REF=$CYID_REF AND STATUS='A' AND BRID_REF=$BRID_REF AND 
                                        (DEACTIVATED IS NULL  OR DEACTIVATED = 0)");

        $ObjEmployee            =       DB::select("SELECT EMPID,EMPCODE,
                                        dbo.fn_titlecase(FNAME+ ISNULL(' '+MNAME,'')+ISNULL(' '+LNAME,'')) AS EMP_NAME   FROM TBL_MST_EMPLOYEE 
										WHERE CYID_REF=$CYID_REF AND STATUS='A' AND BRID_REF=$BRID_REF AND (DEACTIVATED IS NULL  OR DEACTIVATED = 0)");


        return view('reports.Payroll.Payslip.rptfrm452',compact([
        'objRights',
        'ObjPayPeriod',
        'ObjEmployee'
        ]));        
    }  

    
   public function ViewReport($request) {

    //dd($request->all()); 

    $box = $request;        
    $myValue=  array();
    parse_str($box, $myValue);
    
    if($myValue['Flag'] == 'H')
    {
        
        $Flag            = $myValue['Flag'];     
        $PAYPERIODID     = $myValue['PAYPERIODID'];;   
        $EMPID           = $myValue['EMPID'];
    }
    else
    {  
        $PAYPERIODID     = Session::get('PAYPERIODID');
        $EMPID           = Session::get('EMPID');
        $Flag            = $myValue['Flag'];
    }

        
        
          $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
   
            $result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/SalarySlip');
      
        
        $reportParameters = array(
            'CYID'                         => Auth::user()->CYID_REF,
            'PAYPERIODID_REF'              => $PAYPERIODID,
            'EMPID_REF'                    => $EMPID,
        );

        

        $CYID_REF          = Auth::user()->CYID_REF;
        
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
            ->setExecutionParameters($parameters);
         
            if($Flag == 'H')
            {
                Session::put('PAYPERIODID', $PAYPERIODID);  
                Session::put('EMPID', $EMPID);
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
