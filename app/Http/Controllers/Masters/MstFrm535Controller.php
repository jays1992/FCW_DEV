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

class MstFrm535Controller extends Controller{

    protected $form_id  =   535;
    protected $vtid_ref =   605;
    protected $view     =   "masters.Sales.ProductPriceListMaster.mstfrm535";
   
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){  
        
        $objRights  =   DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();
  
        $FormId         =   $this->form_id;
       
        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');   

        $objDataList    =   DB::select("SELECT 
        T1.*,
        T2.DESCRIPTIONS AS CREATEDBY,
        T3.PLCODE  
        FROM TBL_MST_PPLM T1
        LEFT JOIN TBL_MST_USER T2 ON T2.USERID=T1.CREATED_BY
        LEFT JOIN TBL_MST_PRICE_LEVEL T3 ON T3.PLID=T1.PLID_REF
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' ORDER BY PPLM_ID DESC");

        return view($this->view,compact(['FormId','objRights','objDataList']));
    }

    public function add(){

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $FormId     =   $this->form_id;

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_MST_PPLM',
            'HDR_ID'=>'PPLM_ID',
            'HDR_DOC_NO'=>'PPLM_NO',
            'HDR_DOC_DT'=>date('Y-m-d'),
            'HDR_DOC_TYPE'=>'master'
        );

        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req); 
        $objUdf     =   $this->getUdf(['VTID_REF'=>$this->vtid_ref]);

     return view($this->view.'add', compact(['FormId','doc_req','docarray','objUdf']));       
    }


    public function save(Request $request){

        $VTID           =   $this->vtid_ref;
        $USERID         =   Auth::user()->USERID;   
        $ACTION         =   'ADD';
        $IPADDRESS      =   $request->getClientIp();
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $details  = array();
        if(isset($_REQUEST['ITEM_REF']) && !empty($_REQUEST['ITEM_REF'])){
            foreach($_REQUEST['ITEM_REF'] as $key=>$val){

                $details[] = array(
                'ITEMID_REF'           => trim($_REQUEST['ITEM_REF'][$key])?trim($_REQUEST['ITEM_REF'][$key]):0,
                'SALE_PRICE'            => trim($_REQUEST['SALES_PRICE'][$key])?trim($_REQUEST['SALES_PRICE'][$key]):0,
                'PURCHASE_PRICE'         => trim($_REQUEST['PURCHASE_PRICE'][$key])?trim($_REQUEST['PURCHASE_PRICE'][$key]):0,
                );
            }
        }

        if(!empty($details)){
            $wrapped_link["DETAIL"] = $details; 
            $DETAIL = ArrayToXml::convert($wrapped_link);
        }
        else{
            $DETAIL = NULL; 
        }

        $udffield_Data  =   [];      
        for ($i=0; $i<=$request['Row_Count3']; $i++){
            if(isset( $request['udffie_'.$i])){
                $udffield_Data[$i]['UDFVMID_REF']   = $request['udffie_'.$i]; 
                $udffield_Data[$i]['UDF_VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           } 
        }

        if(count($udffield_Data) > 0 ){
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $UDF = $udffield__xml;        
        }
        else{
            $UDF = NULL;
        }

        $PPLM_NO    =   $request['DOC_NO'];
        $PPLM_DATE  =   $request['DOC_DATE'];
        $PLID_REF   =   $request['PRICE_LEVEL_REF'];
       
        $log_data = [
            $PPLM_NO,$PPLM_DATE,$PLID_REF,$CYID_REF,$BRID_REF,
            $FYID_REF,$DETAIL,$UDF,$VTID,$USERID,
            Date('Y-m-d'),Date('h:i:s.u'),$ACTION,$IPADDRESS
        ];


        $sp_result  =   DB::select('EXEC SP_PPLM_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $log_data);  
        
        $contains   =   Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);
        }
        else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
    }

    public function edit($id=NULL){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF'); 
        $FormId         =   $this->form_id;
        $ActionStatus   =   "";
        
        if(!is_null($id)){

            $id        =   urldecode(base64_decode($id));

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))            
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();
      
            $HDR            =   DB::select("SELECT T1.*,T2.PLID,T2.PLCODE FROM TBL_MST_PPLM T1 LEFT JOIN TBL_MST_PRICE_LEVEL T2 ON T2.PLID=T1.PLID_REF WHERE T1.PPLM_ID='$id'"); 

            $HDR            =   count($HDR) > 0?$HDR[0]:[];
            $objUdf         =   $this->getUdf(['VTID_REF'=>$this->vtid_ref]);

            $DETAILS        =   DB::select("SELECT T1.*,T2.ITEMID,T2.ICODE,T2.NAME FROM TBL_MST_PPLM_DETAILS T1 LEFT JOIN TBL_MST_ITEM T2 ON T2.ITEMID=T1.ITEMID_REF WHERE PPLM_ID_REF='$id'"); 

            $objtempUdf     =   $objUdf;
            foreach ($objtempUdf as $index => $udfvalue) {

                $objSavedUDF =  DB::table('TBL_MST_PPLM_UDF')
                ->where('PPLM_ID_REF','=',$id)
                ->where('UDFPPLMID_REF','=',$udfvalue->UDFID)
                ->select('UDF_VALUE')
                ->get()->toArray();

                if(!empty($objSavedUDF)){
                    $objUdf[$index]->UDF_VALUE = $objSavedUDF[0]->UDF_VALUE;
                }
                else{
                    $objUdf[$index]->UDF_VALUE = NULL; 
                }
            }
            $objtempUdf     = [];

            return view($this->view.'edit',compact(['FormId','objRights','ActionStatus','HDR','DETAILS','objUdf']));      
        }
     
    }
    
    public function view($id=NULL){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF'); 
        $FormId         =   $this->form_id;
        $ActionStatus   =   "disabled";
        
        if(!is_null($id)){

            $id        =   urldecode(base64_decode($id));

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))            
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();
      
            $HDR            =   DB::select("SELECT T1.*,T2.PLID,T2.PLCODE FROM TBL_MST_PPLM T1 LEFT JOIN TBL_MST_PRICE_LEVEL T2 ON T2.PLID=T1.PLID_REF WHERE T1.PPLM_ID='$id'"); 

            $HDR            =   count($HDR) > 0?$HDR[0]:[];
            $objUdf         =   $this->getUdf(['VTID_REF'=>$this->vtid_ref]);

            $DETAILS        =   DB::select("SELECT T1.*,T2.ITEMID,T2.ICODE,T2.NAME FROM TBL_MST_PPLM_DETAILS T1 LEFT JOIN TBL_MST_ITEM T2 ON T2.ITEMID=T1.ITEMID_REF WHERE PPLM_ID_REF='$id'"); 

            $objtempUdf     =   $objUdf;
            foreach ($objtempUdf as $index => $udfvalue) {

                $objSavedUDF =  DB::table('TBL_MST_PPLM_UDF')
                ->where('PPLM_ID_REF','=',$id)
                ->where('UDFPPLMID_REF','=',$udfvalue->UDFID)
                ->select('UDF_VALUE')
                ->get()->toArray();

                if(!empty($objSavedUDF)){
                    $objUdf[$index]->UDF_VALUE = $objSavedUDF[0]->UDF_VALUE;
                }
                else{
                    $objUdf[$index]->UDF_VALUE = NULL; 
                }
            }
            $objtempUdf     = [];

            return view($this->view.'view',compact(['FormId','objRights','ActionStatus','HDR','DETAILS','objUdf']));      
        }
     
    }

    public function update(Request $request){
        
        $VTID       =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;   
        $ACTION     =   'EDIT';
        $IPADDRESS  =   $request->getClientIp();
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        
        $details  = array();
        if(isset($_REQUEST['ITEM_REF']) && !empty($_REQUEST['ITEM_REF'])){
            foreach($_REQUEST['ITEM_REF'] as $key=>$val){

                $details[] = array(
                'ITEMID_REF'           => trim($_REQUEST['ITEM_REF'][$key])?trim($_REQUEST['ITEM_REF'][$key]):0,
                'SALE_PRICE'            => trim($_REQUEST['SALES_PRICE'][$key])?trim($_REQUEST['SALES_PRICE'][$key]):0,
                'PURCHASE_PRICE'         => trim($_REQUEST['PURCHASE_PRICE'][$key])?trim($_REQUEST['PURCHASE_PRICE'][$key]):0,
                );
            }
        }

        if(!empty($details)){
            $wrapped_link["DETAIL"] = $details; 
            $DETAIL = ArrayToXml::convert($wrapped_link);
        }
        else{
            $DETAIL = NULL; 
        }

        $udffield_Data  =   [];      
        for ($i=0; $i<=$request['Row_Count3']; $i++){
            if(isset( $request['udffie_'.$i])){
                $udffield_Data[$i]['UDFVMID_REF']   = $request['udffie_'.$i]; 
                $udffield_Data[$i]['UDF_VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           } 
        }

        if(count($udffield_Data) > 0 ){
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $UDF = $udffield__xml;        
        }
        else{
            $UDF = NULL;
        }

        $PPLM_ID    =   $request['PPLM_ID'];
        $PPLM_NO    =   $request['DOC_NO'];
        $PPLM_DATE  =   $request['DOC_DATE'];
        $PLID_REF   =   $request['PRICE_LEVEL_REF'];
        $DEACTIVATED    =   (isset($request['DEACTIVATED']) )? 1 : 0 ;
        $DODEACTIVATED  =   isset($request['DODEACTIVATED']) && $request['DODEACTIVATED'] !=''?date('Y-m-d',strtotime($request['DODEACTIVATED'])):NULL;

        $log_data = [
            $PPLM_ID,$PPLM_NO,$PPLM_DATE,$PLID_REF,$DEACTIVATED,$DODEACTIVATED,$CYID_REF,$BRID_REF,
            $FYID_REF,$DETAIL,$UDF,$VTID,$USERID,
            Date('Y-m-d'),Date('h:i:s.u'),$ACTION,$IPADDRESS
        ];

        $sp_result  =   DB::select('EXEC SP_PPLM_UP ?,?,?,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $log_data); 

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $PPLM_NO. ' Sucessfully Updated.']);
        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
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

        $VTID       =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;   
        $ACTION     =   $Approvallevel;
        $IPADDRESS  =   $request->getClientIp();
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        
        $details  = array();
        if(isset($_REQUEST['ITEM_REF']) && !empty($_REQUEST['ITEM_REF'])){
            foreach($_REQUEST['ITEM_REF'] as $key=>$val){

                $details[] = array(
                'ITEMID_REF'           => trim($_REQUEST['ITEM_REF'][$key])?trim($_REQUEST['ITEM_REF'][$key]):0,
                'SALE_PRICE'            => trim($_REQUEST['SALES_PRICE'][$key])?trim($_REQUEST['SALES_PRICE'][$key]):0,
                'PURCHASE_PRICE'         => trim($_REQUEST['PURCHASE_PRICE'][$key])?trim($_REQUEST['PURCHASE_PRICE'][$key]):0,
                );
            }
        }

        if(!empty($details)){
            $wrapped_link["DETAIL"] = $details; 
            $DETAIL = ArrayToXml::convert($wrapped_link);
        }
        else{
            $DETAIL = NULL; 
        }

        $udffield_Data  =   [];      
        for ($i=0; $i<=$request['Row_Count3']; $i++){
            if(isset( $request['udffie_'.$i])){
                $udffield_Data[$i]['UDFVMID_REF']   = $request['udffie_'.$i]; 
                $udffield_Data[$i]['UDF_VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           } 
        }

        if(count($udffield_Data) > 0 ){
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $UDF = $udffield__xml;        
        }
        else{
            $UDF = NULL;
        }

        $PPLM_ID    =   $request['PPLM_ID'];
        $PPLM_NO    =   $request['DOC_NO'];
        $PPLM_DATE  =   $request['DOC_DATE'];
        $PLID_REF   =   $request['PRICE_LEVEL_REF'];
        $DEACTIVATED    =   (isset($request['DEACTIVATED']) )? 1 : 0 ;
        $DODEACTIVATED  =   isset($request['DODEACTIVATED']) && $request['DODEACTIVATED'] !=''?date('Y-m-d',strtotime($request['DODEACTIVATED'])):NULL;

        $log_data = [
            $PPLM_ID,$PPLM_NO,$PPLM_DATE,$PLID_REF,$DEACTIVATED,$DODEACTIVATED,$CYID_REF,$BRID_REF,
            $FYID_REF,$DETAIL,$UDF,$VTID,$USERID,
            Date('Y-m-d'),Date('h:i:s.u'),$ACTION,$IPADDRESS
        ];

        $sp_result  =   DB::select('EXEC SP_PPLM_UP ?,?,?,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $log_data);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $PPLM_NO. ' Sucessfully Approved.']);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
    }

    
    public function cancel(Request $request){

        $id         =   $request->{0};    
        $USERID =   Auth::user()->USERID;
        $VTID   =   $this->vtid_ref;
        $CYID   =   Auth::user()->CYID_REF;
        $BRID   =   Session::get('BRID_REF');
        $FYID   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_PPLM";
        $FIELD      =   "PPLM_ID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_MST_PPLM',
            'NT'  => 'TBL_MST_PPLM_DETAILS',
            'NT'  => 'TBL_MST_PPLM_UDF',
        ];
      
        $wrapped_links["TABLES"] = $req_data; 
        
        $XML = ArrayToXml::convert($wrapped_links);
        
        $mst_cancel_data = [ $USERID, $VTID, $TABLE, $FIELD, $ID, $CYID, $BRID,$FYID,$UPDATE,$UPTIME, $IPADDRESS ,$XML];

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

            $objResponse = DB::table('TBL_MST_PPLM')->where('PPLM_ID','=',$id)->first();

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

            $dirname =   'ProductPriceListMaster';

            return view($this->view.'attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments','dirname']));
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
        
		$image_path         =   "docs/company".$CYID_REF."/ProductPriceListMaster";     
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

    public function getPriceLevel(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $data   =   DB::select("SELECT 
        PLID AS DATA_ID,
        PLCODE AS DATA_CODE,
        PLNAME AS DATA_DESC
        FROM TBL_MST_PRICE_LEVEL 
        WHERE  CYID_REF='$CYID_REF' AND STATUS='A' AND (DEACTIVATED=0 OR DEACTIVATED IS NULL)"); 

        return Response::json($data);
    }

    public function getProduct(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $data   =   DB::select("SELECT TOP 3 * FROM TBL_MST_ITEM  WHERE  CYID_REF='$CYID_REF' AND STATUS='A' AND (DEACTIVATED=0 OR DEACTIVATED IS NULL)"); 

        return Response::json($data);
    }

    public function getListingData(Request $request){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $data   =   DB::select("SELECT
        T1.PPLM_ID,
        T1.PPLM_NO,
        FORMAT (T1.PPLM_DATE, 'dd-MM-yyyy') AS DOC_DATE,
        FORMAT (T1.CREATED_DATE, 'dd-MM-yyyy') AS CREATED_DATE,
        T2.ITEMID_REF,
        T2.SALE_PRICE,
        T2.PURCHASE_PRICE,
        T3.DESCRIPTIONS AS CREATEDBY,
        T1.STATUS
        FROM TBL_MST_PPLM T1
        INNER JOIN TBL_MST_PPLM_DETAILS T2 ON T2.PPLM_ID_REF=T1.PPLM_ID
        LEFT JOIN TBL_MST_USER T3 ON T3.USERID=T1.CREATED_BY
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF'  
        ORDER BY T1.PPLM_ID DESC
        "); 

        return Response::json($data);
    }

    public function getListing(Request $request){

        

        $columns = array( 
            0 =>'NO',
            1 =>'LABEL',
            2 =>'VALUETYPE',
            3 =>'DESCRIPTIONS',
            4 =>'ISMANDATORY',
            5 =>'DEACTIVATED',
            6 =>'DODEACTIVATED',
            7 =>'STATUS',
        );  
        

        $COL_APP_STATUS =   'STATUS';  //never change value, value must be 'APPROVED_STATUS' as per stored procedure;
      
            $USERID_REF    =   Auth::user()->USERID;
            $CYID_REF      =   Auth::user()->CYID_REF;
            $BRID_REF      =   Session::get('BRID_REF');
            $FYID_REF      =   Session::get('FYID_REF');       
            $TABLE1        =   "TBL_MST_UDFFORSO";
            $PK_COL        =   "UDFID";
            $SELECT_COL    =   "UDFID,LABEL,VALUETYPE,DESCRIPTIONS,ISMANDATORY,DEACTIVATED,DODEACTIVATED";    
            $WHERE_COL     =   " WHERE PARENTID = 0";
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if(!empty($request->input('search.value')))
            {

                $search_text = $request->input('search.value'); 
                $filtercolumn = $request->input('filtercolumn');

                $search_text = "'". $search_text ."'";
                //ALL COLUMN
                if($filtercolumn =='ALL'){

                    $WHERE_COL =  " WHERE PARENTID = 0 AND UDFID LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR PARENTID = 0 AND LABEL LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR PARENTID = 0 AND VALUETYPE LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR PARENTID = 0 AND DESCRIPTIONS LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR PARENTID = 0 AND ISMANDATORY LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR PARENTID = 0 AND DEACTIVATED LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR PARENTID = 0 AND DODEACTIVATED LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR PARENTID = 0 AND ".$COL_APP_STATUS." LIKE  ". $search_text;


                }else{

                    $WHERE_COL =  " WHERE PARENTID = 0 AND ".$filtercolumn." LIKE ". $search_text;

                }         
                
            }
           
            $ORDER_BY_COL   =  $order. " ". $dir;
            $OFFSET_COL     =   " offset ".$start." rows fetch next ".$limit." rows only ";
           
            $sp_listing_data = [
                $USERID_REF, $CYID_REF,$BRID_REF, $FYID_REF, $TABLE1, $PK_COL,
                $SELECT_COL,$WHERE_COL, $ORDER_BY_COL, $OFFSET_COL

            ];

            
            
            $sp_listing_result = DB::select('EXEC SP_LISTINGDATA_UDF ?,?,?,?, ?,?,?,?, ?,?', $sp_listing_data);

            $totalRows = 0;       //total no of records
            $totalFiltered = 0;   // total filtered count

            $data = array();
            
            
            if(!empty($sp_listing_result))
            {
                foreach ($sp_listing_result as $key=>$reqdataitem)
                {
                    $totalRows      = $reqdataitem->TotalRows;
                    $totalFiltered  = $reqdataitem->FilteredRows;

                    if (!Empty($reqdataitem->STATUS) && $reqdataitem->STATUS=="Approved") 
                    { $app_status = 1 ;} 
                    elseif($reqdataitem->STATUS=="Cancel")
                    { $app_status = 2 ;}
                    else{ $app_status = 0 ;}

                    if (!Empty($reqdataitem->ISMANDATORY) && $reqdataitem->ISMANDATORY=="1") 
                    { $ISMANDATORY = "Yes" ;} 
                    else{ $ISMANDATORY = "No" ;}
                    if (!Empty($reqdataitem->DEACTIVATED) && $reqdataitem->DEACTIVATED=="1") 
                    { $DEACTIVATED = "Yes" ;} 
                    else{ $DEACTIVATED = "No" ;}

                    $nestedData['NO']           = '<input type="checkbox" id="chkId'.$reqdataitem->UDFID.'"  value="'.$reqdataitem->UDFID.'" class="js-selectall1" data-rcdstatus="'.$app_status.'">';
                    $nestedData['LABEL']         = strtoupper($reqdataitem->LABEL);
                    $nestedData['VALUETYPE']     = $reqdataitem->VALUETYPE;
                    $nestedData['DESCRIPTIONS']      = $reqdataitem->DESCRIPTIONS;
                    $nestedData['ISMANDATORY']         = $ISMANDATORY;
                    $nestedData['DEACTIVATED']  = $DEACTIVATED;
                    $nestedData['DODEACTIVATED']      = $reqdataitem->DODEACTIVATED =="1900-01-01"?"":$reqdataitem->DODEACTIVATED;
                    $nestedData['STATUS']       = $reqdataitem->STATUS;
                    // $nestedData['action'] = '<a href="#" class="del"><span class="glyphicon glyphicon-trash"></span> 
                    // </a><a href="#" class="edit"><span class="glyphicon glyphicon-edit"></span></a>';
                    $data[] = $nestedData;
                    
                    
                }

            }
            // dd($data);
            $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalRows),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
            );            
            echo json_encode($json_data); 

            
            exit(); 

    }
     
}
