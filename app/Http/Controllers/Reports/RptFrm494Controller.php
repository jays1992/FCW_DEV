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
use App\Exports\NonMovingReportQty;
use Maatwebsite\Excel\Facades\Excel;

class RptFrm494Controller extends Controller{

    protected $form_id  =   494;
    protected $vtid_ref =   564;  
    protected $view_ref =   "NonMovingReportQty";
    protected $repo_nam =   "Non Moving Report-Qty";
    
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){ 

        $FORM_ID        =   $this->form_id;
        $VTID_REF       =   $this->vtid_ref;
        $REPO_NAM       =   $this->repo_nam;

        $USERID         =   Auth::user()->USERID;
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');    

        $objRights      =   DB::table('TBL_MST_USERROLMAP')
                            ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
                            ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
                            ->where('TBL_MST_USERROLMAP.BRID_REF','=',$BRID_REF)
                            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID_REF)
                            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                            ->first();

        $objBranchGroup =   DB::table('TBL_MST_BRANCH_GROUP')
                            ->where('TBL_MST_BRANCH_GROUP.CYID_REF','=',$CYID_REF)
                            ->where('TBL_MST_BRANCH_GROUP.STATUS','=','A')
                            ->leftJoin('TBL_MST_BRANCH', 'TBL_MST_BRANCH_GROUP.BRID_REF','=','TBL_MST_BRANCH.BRID')
                            ->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_BRANCH.BRID','=','TBL_MST_USER_BRANCH_MAP.MAPBRID_REF')
                            ->where('TBL_MST_USER_BRANCH_MAP.USERID_REF','=',$USERID)
                            ->select('TBL_MST_BRANCH_GROUP.*')
                            ->distinct('TBL_MST_BRANCH_GROUP.BGID')
                            ->get();

        $objBranch      =   DB::table('TBL_MST_BRANCH')
                            ->where('TBL_MST_BRANCH.CYID_REF','=',$CYID_REF)
                            ->where('TBL_MST_BRANCH.STATUS','=','A')
                            ->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_BRANCH.BRID','=','TBL_MST_USER_BRANCH_MAP.MAPBRID_REF')
                            ->where('TBL_MST_USER_BRANCH_MAP.USERID_REF','=',$USERID)
                            ->select('TBL_MST_BRANCH.*')
                            ->distinct('TBL_MST_BRANCH.BRID')    
                            ->get();
                            
        $StoreList          =   DB::select("SELECT STID,STCODE,NAME FROM TBL_MST_STORE
                                WHERE CYID_REF='$CYID_REF' AND STATUS='A' AND (DEACTIVATED IS NULL  OR DEACTIVATED = 0)
                                ");
        $ItemCatList        =   DB::select("SELECT ICID,ICCODE,DESCRIPTIONS FROM TBL_MST_ITEMCATEGORY
                                WHERE CYID_REF=$CYID_REF AND STATUS='A' AND (DEACTIVATED IS NULL  OR DEACTIVATED = 0) AND BRID_REF IN($BRID_REF)
                                ");

        $ItemGroupList      =   DB::select("SELECT ITEMGID,GROUPCODE,GROUPNAME FROM TBL_MST_ITEMGROUP
                                WHERE CYID_REF=$CYID_REF AND STATUS='A' AND (DEACTIVATED IS NULL  OR DEACTIVATED = 0) AND BRID_REF IN($BRID_REF)
                                ");
        $ItemSubGroupList   =   DB::select("SELECT DISTINCT S.ISGID,S.ISGCODE,S.DESCRIPTIONS FROM TBL_MST_ITEMSUBGROUP S
                                LEFT JOIN TBL_MST_ITEMGROUP G ON G.ITEMGID=S.ITEMGID_REF
                                WHERE S.DESCRIPTIONS <> '' AND G.CYID_REF=$CYID_REF AND G.STATUS='A' AND (DEACTIVATED IS NULL  OR DEACTIVATED = 0) AND G.BRID_REF IN($BRID_REF)
                                ");
                         //  dd($ItemSubGroupList); 

                         


        return view('reports.inventory.'.$this->view_ref.'.rptfrm'.$FORM_ID,compact(['FORM_ID','REPO_NAM','objRights','objBranchGroup','objBranch','StoreList','ItemCatList','ItemGroupList','ItemSubGroupList']));        
    }  

    
    public function ViewReport($request) {

        $FORM_ID        =   $this->form_id;
        $VTID_REF       =   $this->vtid_ref;

        $USERID         =   Auth::user()->USERID;
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF'); 

        $box            =   $request;        
        $myValue        =   array();
        parse_str($box, $myValue);
    
        if($myValue['Flag'] == 'H'){
            $From_Date      =   $myValue['From_Date'];
            $To_Date        =   $myValue['To_Date'];
            $BranchGroup    =   $myValue['BranchGroup'];
            $BranchName     =   $myValue['BranchName'];
            $ITEMGROUP      =   $myValue['ITEMGROUP'];
            $ITEMSUBGROUP   =   $myValue['ITEMSUBGROUP'];
            $ITEMCATEGORY   =   $myValue['ITEMCATEGORY'];
            $ITEMCATEGORY   =   $myValue['ITEMCATEGORY'];
            $STORE          =   $myValue['STORE'];
            $Flag           =   $myValue['Flag']; 
        }
        else{
        
            $From_Date      =   Session::get('From_Date');
            $To_Date        =   Session::get('To_Date');
            $BranchGroup    =   Session::get('BranchGroup');
            $BranchName     =   Session::get('BranchName');
            $ITEMGROUP      =   Session::get('ITEMGROUP');
            $ITEMSUBGROUP   =   Session::get('ITEMSUBGROUP');
            $ITEMCATEGORY   =   Session::get('ITEMCATEGORY');
            $STORE          =   Session::get('STORE');     
            $Flag           =   $myValue['Flag'];
        }

        
        
        
        $ssrs               =   new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
        $result             =   $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/'.$this->view_ref);
      
        $reportParameters = array(
            'p_cyid'        =>  $CYID_REF,
            'p_userid'      =>  $USERID,          
            'p_branchgroup' =>  $BranchGroup,
            'p_branch'      =>  $BranchName,
            'ITEMGROUP'     =>  $ITEMGROUP,              
            'ITEMSUBGROUP'  =>  $ITEMSUBGROUP,              
            'ITEMCATEGORY'  =>  $ITEMCATEGORY,              
            'STORE'         =>  $STORE,    
            'FROMDATE'      =>  $From_Date,
            'TODATE'        =>  $To_Date,          
        );

        
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        $ssrs->setSessionId($result->executionInfo->ExecutionID)->setExecutionParameters($parameters);
        $CYID_REF       =   Auth::user()->CYID_REF;
        if($Flag == 'H'){
            Session::put('BranchGroup', $BranchGroup);
            Session::put('BranchName', $BranchName);
            Session::put('ITEMGROUP', $ITEMGROUP);
            Session::put('ITEMSUBGROUP', $ITEMSUBGROUP);
            Session::put('ITEMCATEGORY', $ITEMCATEGORY);
            Session::put('STORE', $STORE);
            Session::put('From_Date', $From_Date);
            Session::put('To_Date', $To_Date);
            $output = $ssrs->render('HTML4.0'); // PDF | XML | CSV
            echo $output;
        }
        else if($Flag == 'P'){
            $output = $ssrs->render('PDF'); // PDF | XML | CSV | HTML4.0
            return $output->download('Report.pdf');
        }
        else if($Flag == 'E'){
            return Excel::download(new NonMovingReportQty($ITEMGROUP,$ITEMSUBGROUP,$ITEMCATEGORY,$STORE,$From_Date,$To_Date,$BranchGroup,$BranchName,$CYID_REF), 'NonMovingReportQty.xlsx');
            //$output = $ssrs->render('EXCEL'); // PDF | XML | CSV | HTML4.0
           // return $output->download('Report.xls'); 
        }
         
    }


    
}
