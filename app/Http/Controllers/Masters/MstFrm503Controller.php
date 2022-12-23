<?php
namespace App\Http\Controllers\Masters;

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
use App\Helpers\Helper;
use App\Helpers\Utils;

class MstFrm503Controller extends Controller{

    protected $form_id  =   503;
    protected $vtid_ref =   573;
    protected $view     =   "masters.Accounts.BudgetMaster.mstfrm";
   
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){   
        $FormId         =   $this->form_id; 
        $objRights      =   DB::table('TBL_MST_USERROLMAP')
                            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                            ->first();
        
        $objDataList    =   DB::table('TBL_MST_BUDGET_HDR')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('BRID_REF','=',Session::get('BRID_REF'))
                            ->orderBy('BUGID','desc')
                            ->get();

        return view($this->view.$FormId,compact(['FormId','objRights','objDataList']));
    }

    public function add(){
        $Status   = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
       
        $objSON = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('FYID_REF','=',$FYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_DOCNO_DEFINITION.*')
        ->first();

        $objDataNo   =   NULL;

        if( isset($objSON->SYSTEM_GRSR) && $objSON->SYSTEM_GRSR == "1")
        {
            if($objSON->PREFIX_RQ == "1")
            {
                $objDataNo = $objSON->PREFIX;
            }        
            if($objSON->PRE_SEP_RQ == "1")
            {
                if($objSON->PRE_SEP_SLASH == "1")
                {
                $objDataNo = $objDataNo.'/';
                }
                if($objSON->PRE_SEP_HYPEN == "1")
                {
                $objDataNo = $objDataNo.'-';
                }
            }        
            if($objSON->NO_MAX)
            {   
                $objDataNo = $objDataNo.str_pad($objSON->LAST_RECORDNO+1, $objSON->NO_MAX, "0", STR_PAD_LEFT);
            }
            
            if($objSON->NO_SEP_RQ == "1")
            {
                if($objSON->NO_SEP_SLASH == "1")
                {
                $objDataNo = $objDataNo.'/';
                }
                if($objSON->NO_SEP_HYPEN == "1")
                {
                $objDataNo = $objDataNo.'-';
                }
            }
            if($objSON->SUFFIX_RQ == "1")
            {
                $objDataNo = $objDataNo.$objSON->SUFFIX;
            }
        }
   
        $FormId     =   $this->form_id;
       
        return view($this->view.$FormId.'add',compact(['FormId','objSON','objDataNo']));       
    }

    public function save(Request $request){

        $materialArray  = array();
        if(isset($_REQUEST['GLID_REF']) && !empty($_REQUEST['GLID_REF'])){
            foreach($_REQUEST['GLID_REF'] as $key=>$val){

                $materialArray[] = array(
                    'GLID_REF'      => trim($_REQUEST['GLID_REF'][$key]),
                    'CCID_REF'      => trim($_REQUEST['CCID_REF'][$key]),
                    'MONTH1'        => trim($_REQUEST['MONTH1'][$key]) !=''?trim($_REQUEST['MONTH1'][$key]):0,
                    'MONTH2'        => trim($_REQUEST['MONTH2'][$key]) !=''?trim($_REQUEST['MONTH2'][$key]):0,
                    'MONTH3'        => trim($_REQUEST['MONTH3'][$key]) !=''?trim($_REQUEST['MONTH3'][$key]):0,
                    'MONTH4'        => trim($_REQUEST['MONTH4'][$key]) !=''?trim($_REQUEST['MONTH4'][$key]):0,
                    'MONTH5'        => trim($_REQUEST['MONTH5'][$key]) !=''?trim($_REQUEST['MONTH5'][$key]):0,
                    'MONTH6'        => trim($_REQUEST['MONTH6'][$key]) !=''?trim($_REQUEST['MONTH6'][$key]):0,
                    'MONTH7'        => trim($_REQUEST['MONTH7'][$key]) !=''?trim($_REQUEST['MONTH7'][$key]):0,
                    'MONTH8'        => trim($_REQUEST['MONTH8'][$key]) !=''?trim($_REQUEST['MONTH8'][$key]):0,
                    'MONTH9'        => trim($_REQUEST['MONTH9'][$key]) !=''?trim($_REQUEST['MONTH9'][$key]):0,
                    'MONTH10'       => trim($_REQUEST['MONTH10'][$key]) !=''?trim($_REQUEST['MONTH10'][$key]):0,
                    'MONTH11'       => trim($_REQUEST['MONTH11'][$key]) !=''?trim($_REQUEST['MONTH11'][$key]):0,
                    'MONTH12'       => trim($_REQUEST['MONTH12'][$key]) !=''?trim($_REQUEST['MONTH12'][$key]):0,
                    'TOTAL_AMOUNT'  => trim($_REQUEST['TOTAL_AMOUNT'][$key]) !=''?trim($_REQUEST['TOTAL_AMOUNT'][$key]):0,
                );
            }
        }

        if(!empty($materialArray)){
            $XML_MAT_DATA["MAT"] = $materialArray; 
            $XMLMAT = ArrayToXml::convert($XML_MAT_DATA);
        }
        else{
            $XMLMAT = NULL; 
        } 

        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $BUG_NO          =   $request['BUG_NO'];
        $BUG_DATE        =   $request['BUG_DATE'];
        $BUG_FYID        =   $request['BUG_FYID'];
       
        $log_data = [ 
            $BUG_NO,$BUG_DATE,$BUG_FYID,$CYID_REF,$BRID_REF,
            $FYID_REF,$VTID_REF,$XMLMAT,$USERID,Date('Y-m-d'),
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];  

        $sp_result = DB::select('EXEC SP_BUG_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);
        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
    }


    public function edit($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        $id         =   urldecode(base64_decode($id));
        
        if(!is_null($id)){
            $objRights  =   DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $objResponse    =   DB::table('TBL_MST_BUDGET_HDR AS T1')
            ->leftJoin('TBL_MST_FYEAR AS T2', 'T1.BUG_FYID','=','T2.FYID')
            ->where('T1.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('T1.BRID_REF','=',Session::get('BRID_REF'))
            ->where('T1.FYID_REF','=',Session::get('FYID_REF'))
            ->where('T1.BUGID','=',$id)
            ->select('T1.*','T2.*')
            ->first();

            $months=[];
            if(isset($objResponse) && !empty($objResponse)){
                $startDate  =   $objResponse->FYSTYEAR.'-'.$objResponse->FYSTMONTH.'-01';
                $endDate    =   $objResponse->FYENDYEAR.'-'.$objResponse->FYENDMONTH.'-01';
                $months     =   $this->getDateWiseMonth($startDate,$endDate);
            }

            $objMAT =   DB::select("SELECT 
            T1.*,T2.GLCODE,T2.GLNAME,T3.CCCODE,T3.NAME
            FROM TBL_MST_BUDGET_MAT AS T1
            LEFT JOIN TBL_MST_GENERALLEDGER AS T2 ON T1.GLID_REF=T2.GLID
            LEFT JOIN TBL_MST_COSTCENTER AS T3 ON T1.CCID_REF=T3.CCID
            WHERE T1.BUGID_REF='$id'");

            $FormId         =   $this->form_id;
            $ActionStatus   =   "";

            return view($this->view.$FormId.'edit',compact(['FormId','objRights','objResponse','objMAT','ActionStatus','months']));      
        }
     
    }

    public function update(Request $request){
        
        $materialArray  = array();
        if(isset($_REQUEST['GLID_REF']) && !empty($_REQUEST['GLID_REF'])){
            foreach($_REQUEST['GLID_REF'] as $key=>$val){

                $materialArray[] = array(
                    'GLID_REF'      => trim($_REQUEST['GLID_REF'][$key]),
                    'CCID_REF'      => trim($_REQUEST['CCID_REF'][$key]),
                    'MONTH1'        => trim($_REQUEST['MONTH1'][$key]) !=''?trim($_REQUEST['MONTH1'][$key]):0,
                    'MONTH2'        => trim($_REQUEST['MONTH2'][$key]) !=''?trim($_REQUEST['MONTH2'][$key]):0,
                    'MONTH3'        => trim($_REQUEST['MONTH3'][$key]) !=''?trim($_REQUEST['MONTH3'][$key]):0,
                    'MONTH4'        => trim($_REQUEST['MONTH4'][$key]) !=''?trim($_REQUEST['MONTH4'][$key]):0,
                    'MONTH5'        => trim($_REQUEST['MONTH5'][$key]) !=''?trim($_REQUEST['MONTH5'][$key]):0,
                    'MONTH6'        => trim($_REQUEST['MONTH6'][$key]) !=''?trim($_REQUEST['MONTH6'][$key]):0,
                    'MONTH7'        => trim($_REQUEST['MONTH7'][$key]) !=''?trim($_REQUEST['MONTH7'][$key]):0,
                    'MONTH8'        => trim($_REQUEST['MONTH8'][$key]) !=''?trim($_REQUEST['MONTH8'][$key]):0,
                    'MONTH9'        => trim($_REQUEST['MONTH9'][$key]) !=''?trim($_REQUEST['MONTH9'][$key]):0,
                    'MONTH10'       => trim($_REQUEST['MONTH10'][$key]) !=''?trim($_REQUEST['MONTH10'][$key]):0,
                    'MONTH11'       => trim($_REQUEST['MONTH11'][$key]) !=''?trim($_REQUEST['MONTH11'][$key]):0,
                    'MONTH12'       => trim($_REQUEST['MONTH12'][$key]) !=''?trim($_REQUEST['MONTH12'][$key]):0,
                    'TOTAL_AMOUNT'  => trim($_REQUEST['TOTAL_AMOUNT'][$key]) !=''?trim($_REQUEST['TOTAL_AMOUNT'][$key]):0,
                );
            }
        }

        if(!empty($materialArray)){
            $XML_MAT_DATA["MAT"] = $materialArray; 
            $XMLMAT = ArrayToXml::convert($XML_MAT_DATA);
        }
        else{
            $XMLMAT = NULL; 
        }
        
        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $BUGID      =   $request['BUGID'];
        $BUG_NO     =   $request['BUG_NO'];
        $BUG_DATE   =   $request['BUG_DATE'];
        $BUG_FYID   =   $request['BUG_FYID'];
       
        $log_data = [ 
            $BUGID,$BUG_NO,$BUG_DATE,$BUG_FYID,$CYID_REF,$BRID_REF,
            $FYID_REF,$VTID_REF,$XMLMAT,$USERID,Date('Y-m-d'),
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];  

        $sp_result = DB::select('EXEC SP_BUG_UP ?,?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $BUG_NO. ' Sucessfully Updated.']);
        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
    }

    public function view($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        $id         =   urldecode(base64_decode($id));
        
        if(!is_null($id)){
            $objRights  =   DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $objResponse    =   DB::table('TBL_MST_BUDGET_HDR AS T1')
            ->leftJoin('TBL_MST_FYEAR AS T2', 'T1.BUG_FYID','=','T2.FYID')
            ->where('T1.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('T1.BRID_REF','=',Session::get('BRID_REF'))
            ->where('T1.FYID_REF','=',Session::get('FYID_REF'))
            ->where('T1.BUGID','=',$id)
            ->select('T1.*','T2.*')
            ->first();

            $months=[];
            if(isset($objResponse) && !empty($objResponse)){
                $startDate  =   $objResponse->FYSTYEAR.'-'.$objResponse->FYSTMONTH.'-01';
                $endDate    =   $objResponse->FYENDYEAR.'-'.$objResponse->FYENDMONTH.'-01';
                $months     =   $this->getDateWiseMonth($startDate,$endDate);
            }

            $objMAT =   DB::select("SELECT 
            T1.*,T2.GLCODE,T2.GLNAME,T3.CCCODE,T3.NAME
            FROM TBL_MST_BUDGET_MAT AS T1
            LEFT JOIN TBL_MST_GENERALLEDGER AS T2 ON T1.GLID_REF=T2.GLID
            LEFT JOIN TBL_MST_COSTCENTER AS T3 ON T1.CCID_REF=T3.CCID
            WHERE T1.BUGID_REF='$id'");

            $FormId         =   $this->form_id;
            $ActionStatus   =   "disabled";

            return view($this->view.$FormId.'view',compact(['FormId','objRights','objResponse','objMAT','ActionStatus','months']));      
        }
     
    }

    public function Approve(Request $request){

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');   

        $sp_Approvallevel = [
            $USERID_REF, $VTID_REF, $CYID_REF,$BRID_REF,
            $FYID_REF
        ];
        
        $sp_listing_result = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?, ?', $sp_Approvallevel);

        if(!empty($sp_listing_result)){
            foreach ($sp_listing_result as $key=>$valueitem){  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$valueitem->LAVELS;
            }
        }
   
        $materialArray  = array();
        if(isset($_REQUEST['GLID_REF']) && !empty($_REQUEST['GLID_REF'])){
            foreach($_REQUEST['GLID_REF'] as $key=>$val){

                $materialArray[] = array(
                    'GLID_REF'      => trim($_REQUEST['GLID_REF'][$key]),
                    'CCID_REF'      => trim($_REQUEST['CCID_REF'][$key]),
                    'MONTH1'        => trim($_REQUEST['MONTH1'][$key]) !=''?trim($_REQUEST['MONTH1'][$key]):0,
                    'MONTH2'        => trim($_REQUEST['MONTH2'][$key]) !=''?trim($_REQUEST['MONTH2'][$key]):0,
                    'MONTH3'        => trim($_REQUEST['MONTH3'][$key]) !=''?trim($_REQUEST['MONTH3'][$key]):0,
                    'MONTH4'        => trim($_REQUEST['MONTH4'][$key]) !=''?trim($_REQUEST['MONTH4'][$key]):0,
                    'MONTH5'        => trim($_REQUEST['MONTH5'][$key]) !=''?trim($_REQUEST['MONTH5'][$key]):0,
                    'MONTH6'        => trim($_REQUEST['MONTH6'][$key]) !=''?trim($_REQUEST['MONTH6'][$key]):0,
                    'MONTH7'        => trim($_REQUEST['MONTH7'][$key]) !=''?trim($_REQUEST['MONTH7'][$key]):0,
                    'MONTH8'        => trim($_REQUEST['MONTH8'][$key]) !=''?trim($_REQUEST['MONTH8'][$key]):0,
                    'MONTH9'        => trim($_REQUEST['MONTH9'][$key]) !=''?trim($_REQUEST['MONTH9'][$key]):0,
                    'MONTH10'       => trim($_REQUEST['MONTH10'][$key]) !=''?trim($_REQUEST['MONTH10'][$key]):0,
                    'MONTH11'       => trim($_REQUEST['MONTH11'][$key]) !=''?trim($_REQUEST['MONTH11'][$key]):0,
                    'MONTH12'       => trim($_REQUEST['MONTH12'][$key]) !=''?trim($_REQUEST['MONTH12'][$key]):0,
                    'TOTAL_AMOUNT'  => trim($_REQUEST['TOTAL_AMOUNT'][$key]) !=''?trim($_REQUEST['TOTAL_AMOUNT'][$key]):0,
                );
            }
        }

        if(!empty($materialArray)){
            $XML_MAT_DATA["MAT"] = $materialArray; 
            $XMLMAT = ArrayToXml::convert($XML_MAT_DATA);
        }
        else{
            $XMLMAT = NULL; 
        }


        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $BUGID      =   $request['BUGID'];
        $BUG_NO     =   $request['BUG_NO'];
        $BUG_DATE   =   $request['BUG_DATE'];
        $BUG_FYID   =   $request['BUG_FYID'];
       
        $log_data = [ 
            $BUGID,$BUG_NO,$BUG_DATE,$BUG_FYID,$CYID_REF,$BRID_REF,
            $FYID_REF,$VTID_REF,$XMLMAT,$USERID,Date('Y-m-d'),
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];  

        $sp_result = DB::select('EXEC SP_BUG_UP ?,?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $BUG_NO. ' Sucessfully Approved.']);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
    }

    public function MultiApprove(Request $request){

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');   

        $sp_Approvallevel = [
            $USERID_REF, $VTID_REF, $CYID_REF,$BRID_REF,
            $FYID_REF
        ];
        
        $sp_listing_result = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?, ?', $sp_Approvallevel);

        if(!empty($sp_listing_result)){
            foreach ($sp_listing_result as $key=>$valueitem){  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$valueitem->LAVELS;
            }
        }
               
        $req_data =  json_decode($request['ID']);

        $wrapped_links = $req_data; 
        $multi_array = $wrapped_links;
        $iddata = [];
        
        foreach($multi_array as $index=>$row){
            $m_array[$index] = $row->ID;
            $iddata['APPROVAL'][]['ID'] =  $row->ID;
        }

        $xml = ArrayToXml::convert($iddata);
                
        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_BUDGET_HDR";
        $FIELD      =   "BUGID";
        $ACTIONNAME     = $Approvallevel;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
            
        $log_data = [ 
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_MST_MULTIAPPROVAL ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
        
        if($sp_result[0]->RESULT=="All records approved"){

        return Response::json(['approve' =>true,'msg' => 'Record successfully Approved.']);

        }elseif($sp_result[0]->RESULT=="NO RECORD FOR APPROVAL"){
        
        return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','salesenquiry'=>'norecord']);
        
        }else{
        return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','salesenquiry'=>'Some Error']);
        }
        
        exit();    
    }

    public function cancel(Request $request){

        $id         =   $request->{0};    
        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_BUDGET_HDR";
        $FIELD      =   "BUGID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_MST_BUDGET_HDR',
            'NT'  => 'TBL_MST_BUDGET_MAT',
        ];
      
        $wrapped_links["TABLES"] = $req_data; 
        
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$XMLTAB];

        $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);

        if($sp_result[0]->RESULT=="CANCELED"){  
          return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
        
            return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
            
        }else{
               return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'invalid'=>'invalid']);
        }
        
        exit(); 
    }

    public function attachment($id){

        if(!is_null($id)){
        
            $FormId     =   $this->form_id;

            $objResponse = DB::table('TBL_MST_BUDGET_HDR')->where('BUGID','=',$id)->first();

            $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
            ->where('VTID','=',$this->vtid_ref)
                ->select('VTID','VCODE','DESCRIPTIONS')
            ->get()
            ->toArray();

            $objAttachments = DB::table('TBL_MST_ATTACHMENT')                    
            ->where('TBL_MST_ATTACHMENT.VTID_REF','=',$this->vtid_ref)
            ->where('TBL_MST_ATTACHMENT.ATTACH_DOCNO','=',$id)
            ->where('TBL_MST_ATTACHMENT.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_ATTACHMENT.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_ATTACHMENT.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
            ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
            ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
            ->get()->toArray();

            $dirname =   'BudgetMaster';

            return view($this->view.$FormId.'attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments','dirname']));
        }

    }    
  
    public function docuploads(Request $request){

        $FormId     =   $this->form_id;

        $formData = $request->all();

        $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size = config("erpconst.attachments.max_size") * 1020 * 1024;

       
        $VTID           =   $formData["VTID_REF"]; 
        $ATTACH_DOCNO   =   $formData["ATTACH_DOCNO"]; 
        $ATTACH_DOCDT   =   $formData["ATTACH_DOCDT"]; 
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');       
       
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();
        
		$image_path         =   "docs/company".$CYID_REF."/BudgetMaster";     
        $destinationPath    =   str_replace('\\', '/', public_path($image_path));
		
        if ( !is_dir($destinationPath) ) {
            mkdir($destinationPath, 0777, true);
        }

        $uploaded_data = [];
        $invlid_files = "";

        $duplicate_files="";

        foreach($formData["REMARKS"] as $index=>$row_val){

                if(isset($formData["FILENAME"][$index])){

                    $uploadedFile = $formData["FILENAME"][$index]; 
                    
                   

                    $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
                    $filesize               =   $uploadedFile ->getSize();  
                    $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );
 
                    $filenametostore        =  $VTID.$ATTACH_DOCNO.date('YmdHis')."_".str_replace(' ', '', $filenamewithextension);  

                    if ($uploadedFile->isValid()) {

                        if(in_array($extension,$allow_extnesions)){
                            
                            if($filesize < $allow_size){

                                $filename = $destinationPath."/".$filenametostore;

                                if (!file_exists($filename)) {

                                   $uploadedFile->move($destinationPath, $filenametostore);  
                                   $uploaded_data[$index]["FILENAME"] =$filenametostore;
                                   $uploaded_data[$index]["LOCATION"] = $image_path."/";
                                   $uploaded_data[$index]["REMARKS"] = is_null($row_val) ? '' : trim($row_val);

                                }else{

                                    $duplicate_files = " ". $duplicate_files.$filenamewithextension. " ";
                                }
                                

                                
                            }else{
                                
                                $invlid_files = $invlid_files.$filenamewithextension." (invalid size)  "; 
                            } 
                            
                        }else{

                            $invlid_files = $invlid_files.$filenamewithextension." (invalid extension)  ";                             
                        }
                    
                    }else{
                            
                        $invlid_files = $invlid_files.$filenamewithextension." (invalid)"; 
                    }

                }

        }

      
        if(empty($uploaded_data)){
            return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
        }
     

        $wrapped_links["ATTACHMENT"] = $uploaded_data;     
        $ATTACHMENTS_XMl = ArrayToXml::convert($wrapped_links);

        $attachment_data = [

            $VTID, 
            $ATTACH_DOCNO, 
            $ATTACH_DOCDT,
            $CYID_REF,
            
            $BRID_REF,
            $FYID_REF,
            $ATTACHMENTS_XMl,
            $USERID,

            $UPDATE,
            $UPTIME,
            $ACTION,
            $IPADDRESS
        ];
        

        $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);

     
        if($sp_result[0]->RESULT=="SUCCESS"){

            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }

            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }

            return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            
            return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
       
    }
   

    public function codeduplicate(Request $request){

        $BUG_NO =   trim($request['BUG_NO']);
        $data   =   DB::table('TBL_MST_BUDGET_HDR')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('BRID_REF','=',Session::get('BRID_REF'))
                    ->where('FYID_REF','=',Session::get('FYID_REF'))
                    ->where('BUG_NO','=',$BUG_NO)
                    ->count();

        if($data > 0){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }
        else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }

        exit();
    }

    public function getFinancialYear(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $data       =   DB::select("SELECT * 
        FROM TBL_MST_FYEAR 
        WHERE CYID_REF='$CYID_REF' AND STATUS='A' AND (DEACTIVATED=0 OR DEACTIVATED IS NULL)"); 

        return Response::json($data);
    }

    public function getGlMaster(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $data       =   DB::select("SELECT 
        T1.GLID,T1.GLCODE,T1.GLNAME 
        FROM TBL_MST_GENERALLEDGER T1
        INNER JOIN TBL_MST_ACCOUNTSUBGROUP T2 ON T1.ASGID_REF=T2.ASGID
        INNER JOIN TBL_MST_ACCOUNTGROUP T3 ON T2.AGID_REF=T3.AGID
        INNER JOIN TBL_MST_NATUREOFGROUP T4 ON T3.NOGID_REF=T4.NOGID
        WHERE T4.NOG_TYPE='4' AND T1.CYID_REF='$CYID_REF' AND T1.STATUS='A' AND (T1.DEACTIVATED=0 OR T1.DEACTIVATED IS NULL)"); 

        return Response::json($data);
    }

    public function getMonthsInRange(Request $request){

        $startDate  =   trim($request['start_date']);
        $endDate    =   trim($request['end_date']);
        $months     =   $this->getDateWiseMonth($startDate,$endDate);
        
        return Response::json($months);
    }


    public function getDateWiseMonth($startDate,$endDate){
        $months     =   array();
        while (strtotime($startDate) <= strtotime($endDate)){
            $months[]   =   array('month' => date('M', strtotime($startDate)));
            $startDate  =   date('01 M Y', strtotime($startDate . '+ 1 month'));
        }
        return $months;
    }

    public function getCostMaster(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $data       =   DB::select("SELECT * FROM TBL_MST_COSTCENTER T1 WHERE T1.CYID_REF='$CYID_REF' AND T1.STATUS='A' AND (T1.DEACTIVATED=0 OR T1.DEACTIVATED IS NULL)"); 

        return Response::json($data);
    }


}
