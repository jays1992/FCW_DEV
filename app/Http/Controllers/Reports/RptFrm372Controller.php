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

class RptFrm372Controller extends Controller
{
    protected $form_id = 372;
    protected $vtid_ref   = 458;  //voucher type id
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
        
            $ObjIPO = DB::table('TBL_TRN_PROR01_HDR')
            ->where('TBL_TRN_PROR01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_PROR01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
            ->select('TBL_TRN_PROR01_HDR.*')
            ->OrderBy('TBL_TRN_PROR01_HDR.POID')
            ->get();

        return view('reports.purchase.IPOPrint.rptfrm372', compact(['objRights','ObjIPO']));        
    }
	
	public function POAmendment(Request $request){
		
		$POID = $request['POID'];
		
		$ObjData =  DB::select('SELECT ANO FROM TBL_TRN_PROR02_HDR WHERE POID_REF = ? order by ANO ASC', [$POID]);
		
			$row1 = '<select name="ANO" id="ANO" class="form-control mandatory"> <option value="">select</option>';			
			$row2 = '</select>';			
			$row = '';
			$row3 = '';
        if(!empty($ObjData)){
			foreach ($ObjData as $index=>$dataRow){
				$row = $row.'<option value="'.$dataRow->ANO.'" >'.$dataRow->ANO.'</option>';			
			}
        }else{
            $row = '';
        }
		
		$row3 = $row1.$row.$row2;
		echo $row3;
		
        exit();
		
	}

    
    
   public function ViewReport($request) {

        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
           
        $POID         =   $myValue['IPO_NO'];
		$ANO          =   $myValue['ANO'];
        $Flag         =   $myValue['Flag'];

        

        $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
   
        $result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/POPrint - UnicornAmnd');
        
        $reportParameters = array(
            'POID'  => $POID,
			'ANO'   => $ANO,
        );
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
        ->setExecutionParameters($parameters);
        if($Flag == 'H')
        {
            $output = $ssrs->render('HTML4.0'); 
            echo $output;
        }
        else if($Flag == 'P')
        {
            $output = $ssrs->render('PDF'); 
            return $output->download('Report.pdf');
        }
        else if($Flag == 'E')
        {
            $output = $ssrs->render('EXCEL'); 
            return $output->download('Report.xls');
        }
         
     }    
    
}
