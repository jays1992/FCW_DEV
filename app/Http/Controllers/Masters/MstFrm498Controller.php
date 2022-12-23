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

class MstFrm498Controller extends Controller{

    protected $form_id  =   498;
    protected $vtid_ref =   568;
    protected $view     =   "masters.Sales.SchemeMaster.mstfrm";
   
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
        
        $objDataList    =   DB::table('TBL_MST_SCHEME_HDR')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('BRID_REF','=',Session::get('BRID_REF'))
                            ->orderBy('SCHEMEID','desc')
                            ->get();

        return view($this->view.$FormId,compact(['objRights','objDataList','FormId']));
    }

    public function add(){       
        $Status   = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
       
        $objlastdt          =   $this->getLastdt();
        
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
        $AlpsStatus =   $this->AlpsStatus();
        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
        return view($this->view.$FormId.'add',compact(['AlpsStatus','FormId','objSON','objDataNo','objlastdt','TabSetting']));       
    }

    public function save(Request $request){

        $materialArray  = array();
        if(isset($_REQUEST['ITEMID_REF']) && !empty($_REQUEST['ITEMID_REF'])){
            foreach($_REQUEST['ITEMID_REF'] as $key=>$val){

                $materialArray[] = array(
                    'ITEMID_REF'    => trim($_REQUEST['ITEMID_REF'][$key]) !=''?trim($_REQUEST['ITEMID_REF'][$key]):NULL,
                    'UOMID_REF'     => trim($_REQUEST['MAIN_UOMID_REF'][$key]) !=''?trim($_REQUEST['MAIN_UOMID_REF'][$key]):NULL,
                    'ITEM_QTY'      => trim($_REQUEST['QTY'][$key]) !=''?trim($_REQUEST['QTY'][$key]):NULL,
                    'COST'          => trim($_REQUEST['COST'][$key]) !=''?trim($_REQUEST['COST'][$key]):NULL,
                    'PER'           => trim($_REQUEST['PER'][$key]) !=''?trim($_REQUEST['PER'][$key]):NULL,
                    'PER_BASE'      => trim($_REQUEST['PERCENTAGE_BASED_ON'][$key]) !=''?trim($_REQUEST['PERCENTAGE_BASED_ON'][$key]):NULL,
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

        $SCHEME_NO          =   $request['SCHEME_NO'];
        $SCHEME_DATE        =   $request['SCHEME_DATE'];
        $SCHEME_NAME        =   $request['SCHEME_NAME'];
        $SCHEME_TYPE        =   $request['SCHEME_TYPE'];
        $EFF_FROM_DATE      =   $request['EFF_FROM_DATE'];
        $EFF_TO_DATE        =   $request['EFF_TO_DATE'];
        $MAIN_ITEMID_REF    =   $request['MAIN_ITEMID_REF'];
        $MAIN_UOM_REF       =   $request['MAIN_UOM_REF'];
        $MAIN_QTY           =   $request['MAIN_QTY'];
        $REMARKS            =   $request['REMARKS'];
        
        $log_data = [ 
            $SCHEME_NO,$SCHEME_DATE,$SCHEME_NAME,$SCHEME_TYPE,$EFF_FROM_DATE,
            $EFF_TO_DATE,$MAIN_ITEMID_REF,$MAIN_UOM_REF,$MAIN_QTY,$REMARKS,
            $CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT,
            $USERID,Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];  

        $sp_result = DB::select('EXEC SP_SCHEME_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $log_data);  

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
            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $objResponse    =   DB::table('TBL_MST_SCHEME_HDR')
            ->leftJoin('TBL_MST_ITEM', 'TBL_MST_SCHEME_HDR.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
            ->leftJoin('TBL_MST_UOM', 'TBL_MST_SCHEME_HDR.UOMID_REF','=','TBL_MST_UOM.UOMID')
            ->where('TBL_MST_SCHEME_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_SCHEME_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_SCHEME_HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_MST_SCHEME_HDR.SCHEMEID','=',$id)
            ->select('TBL_MST_SCHEME_HDR.*','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME AS ITEM_NAME','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS')
            ->first();

            $objlastdt      =   $this->getLastdt();
           
            $objMAT         =   DB::select("SELECT T1.*,T2.ICODE,T2.NAME AS ITEM_NAME,T2.ITEM_SPECI,T3.UOMCODE,T3.DESCRIPTIONS
            FROM TBL_MST_SCHEME_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            WHERE T1.SCHEMEID_REF='$id' ORDER BY T1.SCHMATID ASC");  

            $FormId         =   $this->form_id;
            $AlpsStatus     =   $this->AlpsStatus();
            $ActionStatus   =   "";
            $TabSetting	    =   Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
            return view($this->view.$FormId.'edit',compact(['AlpsStatus','FormId','objRights','objlastdt','objResponse','objMAT','ActionStatus','TabSetting']));      

        }
     
    }

    public function update(Request $request){
        
        $materialArray  = array();
        if(isset($_REQUEST['ITEMID_REF']) && !empty($_REQUEST['ITEMID_REF'])){
            foreach($_REQUEST['ITEMID_REF'] as $key=>$val){

                $materialArray[] = array(
                    'ITEMID_REF'    => trim($_REQUEST['ITEMID_REF'][$key]) !=''?trim($_REQUEST['ITEMID_REF'][$key]):NULL,
                    'UOMID_REF'     => trim($_REQUEST['MAIN_UOMID_REF'][$key]) !=''?trim($_REQUEST['MAIN_UOMID_REF'][$key]):NULL,
                    'ITEM_QTY'      => trim($_REQUEST['QTY'][$key]) !=''?trim($_REQUEST['QTY'][$key]):NULL,
                    'COST'          => trim($_REQUEST['COST'][$key]) !=''?trim($_REQUEST['COST'][$key]):NULL,
                    'PER'           => trim($_REQUEST['PER'][$key]) !=''?trim($_REQUEST['PER'][$key]):NULL,
                    'PER_BASE'      => trim($_REQUEST['PERCENTAGE_BASED_ON'][$key]) !=''?trim($_REQUEST['PERCENTAGE_BASED_ON'][$key]):NULL,
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

        $SCHEMEID          =   $request['SCHEMEID'];
        $SCHEME_NO          =   $request['SCHEME_NO'];
        $SCHEME_DATE        =   $request['SCHEME_DATE'];
        $SCHEME_NAME        =   $request['SCHEME_NAME'];
        $SCHEME_TYPE        =   $request['SCHEME_TYPE'];
        $EFF_FROM_DATE      =   $request['EFF_FROM_DATE'];
        $EFF_TO_DATE        =   $request['EFF_TO_DATE'];
        $MAIN_ITEMID_REF    =   $request['MAIN_ITEMID_REF'];
        $MAIN_UOM_REF       =   $request['MAIN_UOM_REF'];
        $MAIN_QTY           =   $request['MAIN_QTY'];
        $REMARKS            =   $request['REMARKS'];

        $log_data = [ 
            $SCHEMEID,$SCHEME_NO,$SCHEME_DATE,$SCHEME_NAME,$SCHEME_TYPE,$EFF_FROM_DATE,
            $EFF_TO_DATE,$MAIN_ITEMID_REF,$MAIN_UOM_REF,$MAIN_QTY,$REMARKS,
            $CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT,
            $USERID,Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];  

        $sp_result = DB::select('EXEC SP_SCHEME_UP ?,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $log_data); 

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $SCHEME_NO. ' Sucessfully Updated.']);
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
            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $objResponse    =   DB::table('TBL_MST_SCHEME_HDR')
            ->leftJoin('TBL_MST_ITEM', 'TBL_MST_SCHEME_HDR.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
            ->leftJoin('TBL_MST_UOM', 'TBL_MST_SCHEME_HDR.UOMID_REF','=','TBL_MST_UOM.UOMID')
            ->where('TBL_MST_SCHEME_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_SCHEME_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_SCHEME_HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_MST_SCHEME_HDR.SCHEMEID','=',$id)
            ->select('TBL_MST_SCHEME_HDR.*','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME AS ITEM_NAME','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS')
            ->first();

            $objlastdt      =   $this->getLastdt();
           
            $objMAT         =   DB::select("SELECT T1.*,T2.ICODE,T2.NAME AS ITEM_NAME,T2.ITEM_SPECI,T3.UOMCODE,T3.DESCRIPTIONS
            FROM TBL_MST_SCHEME_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            WHERE T1.SCHEMEID_REF='$id' ORDER BY T1.SCHMATID ASC");  

            $FormId         =   $this->form_id;
            $AlpsStatus     =   $this->AlpsStatus();
            $ActionStatus   =   "disabled";
            $TabSetting	    =   Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
            return view($this->view.$FormId.'view',compact(['AlpsStatus','FormId','objRights','objlastdt','objResponse','objMAT','ActionStatus','TabSetting']));      

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
        if(isset($_REQUEST['ITEMID_REF']) && !empty($_REQUEST['ITEMID_REF'])){
            foreach($_REQUEST['ITEMID_REF'] as $key=>$val){

                $materialArray[] = array(
                    'ITEMID_REF'    => trim($_REQUEST['ITEMID_REF'][$key]) !=''?trim($_REQUEST['ITEMID_REF'][$key]):NULL,
                    'UOMID_REF'     => trim($_REQUEST['MAIN_UOMID_REF'][$key]) !=''?trim($_REQUEST['MAIN_UOMID_REF'][$key]):NULL,
                    'ITEM_QTY'      => trim($_REQUEST['QTY'][$key]) !=''?trim($_REQUEST['QTY'][$key]):NULL,
                    'COST'          => trim($_REQUEST['COST'][$key]) !=''?trim($_REQUEST['COST'][$key]):NULL,
                    'PER'           => trim($_REQUEST['PER'][$key]) !=''?trim($_REQUEST['PER'][$key]):NULL,
                    'PER_BASE'      => trim($_REQUEST['PERCENTAGE_BASED_ON'][$key]) !=''?trim($_REQUEST['PERCENTAGE_BASED_ON'][$key]):NULL,
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

        $SCHEMEID          =   $request['SCHEMEID'];
        $SCHEME_NO          =   $request['SCHEME_NO'];
        $SCHEME_DATE        =   $request['SCHEME_DATE'];
        $SCHEME_NAME        =   $request['SCHEME_NAME'];
        $SCHEME_TYPE        =   $request['SCHEME_TYPE'];
        $EFF_FROM_DATE      =   $request['EFF_FROM_DATE'];
        $EFF_TO_DATE        =   $request['EFF_TO_DATE'];
        $MAIN_ITEMID_REF    =   $request['MAIN_ITEMID_REF'];
        $MAIN_UOM_REF       =   $request['MAIN_UOM_REF'];
        $MAIN_QTY           =   $request['MAIN_QTY'];
        $REMARKS            =   $request['REMARKS'];
       
        $log_data = [ 
            $SCHEMEID,$SCHEME_NO,$SCHEME_DATE,$SCHEME_NAME,$SCHEME_TYPE,$EFF_FROM_DATE,
            $EFF_TO_DATE,$MAIN_ITEMID_REF,$MAIN_UOM_REF,$MAIN_QTY,$REMARKS,
            $CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT,
            $USERID,Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];  

        $sp_result = DB::select('EXEC SP_SCHEME_UP ?,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $log_data); 

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $SCHEME_NO. ' Sucessfully Approved.']);

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
        $TABLE      =   "TBL_MST_SCHEME_HDR";
        $FIELD      =   "SCHEMEID";
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
        $TABLE      =   "TBL_MST_SCHEME_HDR";
        $FIELD      =   "SCHEMEID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_MST_SCHEME_HDR',
            'NT'  => 'TBL_MST_SCHEME_MAT',
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

            $objResponse = DB::table('TBL_MST_SCHEME_HDR')->where('SCHEMEID','=',$id)->first();

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

            $dirname =   'SchemeMaster';

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
        
		$image_path         =   "docs/company".$CYID_REF."/SchemeMaster";     
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

        $SCHEME_NO  =   trim($request['SCHEME_NO']);
        $data       =   DB::table('TBL_MST_SCHEME_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('SCHEME_NO','=',$SCHEME_NO)
        ->count();

        if($data > 0){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }
        else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }

        exit();
    }

    public function getLastdt(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        return  DB::select('SELECT MAX(SCHEME_DATE) SCHEME_DATE FROM TBL_MST_SCHEME_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
    }

    

    public function loadItem(Request $request){
        return $this->loadItemMaster($request);
    } 
}
