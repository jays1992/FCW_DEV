<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm153;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;

class MstFrm153Controller extends Controller{
   
    protected $form_id  = 153;
    protected $vtid_ref = 120;
    protected $view     = "masters.Common.CustomerCodingDefinition.mstfrm";

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        $FormId     =   $this->form_id;

        $objRights = DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first(); 

        $objDataList    =   DB::table('TBL_MST_CUSTOMERCODE')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->get();

        return view($this->view.$FormId,compact(['objRights','objDataList','FormId']));

    }

    public function add(){ 

        $FormId     =   $this->form_id;
        return view($this->view.$FormId.'add',compact(['FormId',]));
    }

    public function codeduplicate(Request $request){
                
        $objLabel = DB::table('TBL_MST_CUSTOMERCODE')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('STATUS','!=','C')
        ->where('DEACTIVATE','!=',1)
        ->count();   

        if($objLabel > 0){  

            return Response::json(['exists' =>true,'msg' => 'Customer coding definition already created.']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }
    
    public function save(Request $request){
 
        $MANUAL_SR          =   (isset($request['MANUAL_SR']) && trim($request['MANUAL_SR']) !="" )? trim($request['MANUAL_SR']) : 0 ;
        $SYSTEM_GRSR        =   (isset($request['SYSTEM_GRSR']) && trim($request['SYSTEM_GRSR']) !="" )? trim($request['SYSTEM_GRSR']) : 0 ;
        $MANUAL_MAXLENGTH   =   (isset($request['MANUAL_MAXLENGTH']) && trim($request['MANUAL_MAXLENGTH']) !="" )? trim($request['MANUAL_MAXLENGTH']) : NULL ;
        $MAX_DIGIT          =   (isset($request['MAX_DIGIT']) && trim($request['MAX_DIGIT']) !="" )? trim($request['MAX_DIGIT']) : NULL ;
        $NO_START           =   (isset($request['NO_START']) && trim($request['NO_START']) !="" )? trim($request['NO_START']) : NULL ;
        $PREFIX             =   (isset($request['PREFIX']) && trim($request['PREFIX']) !="" )? strtoupper(trim($request['PREFIX'])) : NULL ;
       
        $DEACTIVATED        =   0;  
        $DODEACTIVATED      =   NULL;  

        $CYID_REF           =   Auth::user()->CYID_REF;
        $BRID_REF           =   Session::get('BRID_REF');
        $FYID_REF           =   Session::get('FYID_REF');       
        $VTID               =   $this->vtid_ref;
        $USERID             =   Auth::user()->USERID;
        $UPDATE             =   Date('Y-m-d');
        $UPTIME             =   Date('h:i:s.u');
        $ACTION             =   "ADD";
        $IPADDRESS          =   $request->getClientIp();
        
        $array_data   = [
                        $MANUAL_SR, $SYSTEM_GRSR, $MANUAL_MAXLENGTH,$MAX_DIGIT, $NO_START,
                        $DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF,$FYID_REF, 
                        $VTID, $USERID, $UPDATE,$UPTIME, $ACTION, 
                        $IPADDRESS,$PREFIX
                    ];

        try {

            $sp_result = DB::select('EXEC SP_CUSTOMERCODE_DEFINITION_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?', $array_data);
    
        } catch (\Throwable $th) {
        
            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);

        }
    
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
        
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','exist'=>'duplicate']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }
        
        exit();    
    }

    public function edit($id){

        if(!is_null($id)){

            $FormId     =   $this->form_id;
        
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objResponse = DB::table('TBL_MST_CUSTOMERCODE')->where('CCODEDEFIID','=',$id)->first();
            if(strtoupper($objResponse->STATUS)=="A" || strtoupper($objResponse->STATUS)=="C"){
                exit("Sorry, Only Un Approved record can edit.");
            }
        
            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first(); 

            return view($this->view.$FormId.'edit',compact(['FormId','objResponse','user_approval_level','objRights']));
              
        }

    }

     
    public function update(Request $request){

        $CCODEDEFIID        =   trim($request['CCODEDEFIID']); 

        $MANUAL_SR          =   (isset($request['MANUAL_SR']) && trim($request['MANUAL_SR']) !="" )? trim($request['MANUAL_SR']) : 0 ;
        $SYSTEM_GRSR        =   (isset($request['SYSTEM_GRSR']) && trim($request['SYSTEM_GRSR']) !="" )? trim($request['SYSTEM_GRSR']) : 0 ;
        $MANUAL_MAXLENGTH   =   (isset($request['MANUAL_MAXLENGTH']) && trim($request['MANUAL_MAXLENGTH']) !="" )? trim($request['MANUAL_MAXLENGTH']) : NULL ;
        $MAX_DIGIT          =   (isset($request['MAX_DIGIT']) && trim($request['MAX_DIGIT']) !="" )? trim($request['MAX_DIGIT']) : NULL ;
        $NO_START           =   (isset($request['NO_START']) && trim($request['NO_START']) !="" )? trim($request['NO_START']) : NULL ;
        $PREFIX             =   (isset($request['PREFIX']) && trim($request['PREFIX']) !="" )? strtoupper(trim($request['PREFIX'])) : NULL ;
       
        $DEACTIVATED        =   (isset($request['DEACTIVATE']) )? 1 : 0 ;
        $DODEACTIVATED      =   isset($request['DODEACTIVATE']) && $request['DODEACTIVATE'] !=''? date('Y-m-d',strtotime($request['DODEACTIVATE'])):NULL;

        $objLabel           =   DB::table('TBL_MST_CUSTOMERCODE')
                                ->where('CCODEDEFIID','!=',$CCODEDEFIID)
                                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('STATUS','!=','C')
                                ->where('DEACTIVATE','=',0)
                                ->first(); 

        if($DEACTIVATED ==0 && !empty($objLabel)){
            return Response::json(['errors'=>true,'msg' => 'Active data already exist in database.','save'=>'invalid']);
        }


        $CYID_REF           =   Auth::user()->CYID_REF;
        $BRID_REF           =   Session::get('BRID_REF');
        $FYID_REF           =   Session::get('FYID_REF');       
        $VTID               =   $this->vtid_ref;
        $USERID             =   Auth::user()->USERID;
        $UPDATE             =   Date('Y-m-d');
        
        $UPTIME             =   Date('h:i:s.u');
        $ACTION             =   "EDIT";
        $IPADDRESS          =   $request->getClientIp();

        $array_data   = [
            $CCODEDEFIID,$MANUAL_SR, $SYSTEM_GRSR, $MANUAL_MAXLENGTH,$MAX_DIGIT, $NO_START,
            $DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF,$FYID_REF, 
            $VTID, $USERID, $UPDATE,$UPTIME, $ACTION, 
            $IPADDRESS,$PREFIX
        ];

        try {

        $sp_result = DB::select('EXEC SP_CUSTOMERCODE_DEFINITION_UP ?,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?', $array_data);

        } catch (\Throwable $th) {

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);

        }    

        if($sp_result[0]->RESULT=="SUCCESS"){  

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOUND"){
        
            return Response::json(['errors'=>true,'msg' => 'No record found.','exist'=>'norecord']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);
        }
        
        exit();            
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
        
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/CustomerCodingDefinition";
		
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

                   

                    $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."#_".$filenamewithextension;  

                    if ($uploadedFile->isValid()) {

                        if(in_array($extension,$allow_extnesions)){
                            
                            if($filesize < $allow_size){

                                $filename = $destinationPath."/".$filenametostore;

                                if (!file_exists($filename)) {

                                   $uploadedFile->move($destinationPath, $filenametostore);  
                                   $uploaded_data[$index]["FILENAME"] =$filenametostore;
                                   $uploaded_data[$index]["LOCATION"] = $destinationPath."/";
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

    public function singleapprove(Request $request){
      
        $CCODEDEFIID        =   trim($request['CCODEDEFIID']); 

        $MANUAL_SR          =   (isset($request['MANUAL_SR']) && trim($request['MANUAL_SR']) !="" )? trim($request['MANUAL_SR']) : 0 ;
        $SYSTEM_GRSR        =   (isset($request['SYSTEM_GRSR']) && trim($request['SYSTEM_GRSR']) !="" )? trim($request['SYSTEM_GRSR']) : 0 ;
        $MANUAL_MAXLENGTH   =   (isset($request['MANUAL_MAXLENGTH']) && trim($request['MANUAL_MAXLENGTH']) !="" )? trim($request['MANUAL_MAXLENGTH']) : NULL ;
        $MAX_DIGIT          =   (isset($request['MAX_DIGIT']) && trim($request['MAX_DIGIT']) !="" )? trim($request['MAX_DIGIT']) : NULL ;
        $NO_START           =   (isset($request['NO_START']) && trim($request['NO_START']) !="" )? trim($request['NO_START']) : NULL ;
        $PREFIX             =   (isset($request['PREFIX']) && trim($request['PREFIX']) !="" )? strtoupper(trim($request['PREFIX'])) : NULL ;
       
        $DEACTIVATED        =   (isset($request['DEACTIVATE']) )? 1 : 0 ;
        $DODEACTIVATED      =   isset($request['DODEACTIVATE']) && $request['DODEACTIVATE'] !=''? date('Y-m-d',strtotime($request['DODEACTIVATE'])):NULL;

        $objLabel           =   DB::table('TBL_MST_CUSTOMERCODE')
                                ->where('CCODEDEFIID','!=',$CCODEDEFIID)
                                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('STATUS','!=','C')
                                ->where('DEACTIVATE','=',0)
                                ->first(); 

        if($DEACTIVATED ==0 && !empty($objLabel)){
            return Response::json(['errors'=>true,'msg' => 'Active data already exist in database.','save'=>'invalid']);
        }

        $CYID_REF           =   Auth::user()->CYID_REF;
        $BRID_REF           =   Session::get('BRID_REF');
        $FYID_REF           =   Session::get('FYID_REF');       
        $VTID               =   $this->vtid_ref;
        $USERID             =   Auth::user()->USERID;
        $UPDATE             =   Date('Y-m-d');
         
        $UPTIME             =   Date('h:i:s.u');
        $ACTION             =   trim($request['user_approval_level']); 
        $IPADDRESS          =   $request->getClientIp();
         
        $array_data   = [
            $CCODEDEFIID,$MANUAL_SR, $SYSTEM_GRSR, $MANUAL_MAXLENGTH,$MAX_DIGIT, $NO_START,
            $DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF,$FYID_REF, 
            $VTID, $USERID, $UPDATE,$UPTIME, $ACTION, 
            $IPADDRESS,$PREFIX
        ];

        try {

            $sp_result = DB::select('EXEC SP_CUSTOMERCODE_DEFINITION_UP ?,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?', $array_data);

        } catch (\Throwable $th) {

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);

        }    
                    
        if($sp_result[0]->RESULT=="SUCCESS"){  
 
             return Response::json(['success' =>true,'msg' => 'Record successfully approved.']);
         
         }elseif($sp_result[0]->RESULT=="NO RECORD FOUND"){
         
             return Response::json(['errors'=>true,'msg' => 'No record found.','exist'=>'norecord']);
             
         }else{
 
             return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);
         }
         
         exit();  

     }


    public function view($id){

        if(!is_null($id)){

            $FormId     =   $this->form_id;
        
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objResponse = DB::table('TBL_MST_CUSTOMERCODE')->where('CCODEDEFIID','=',$id)->first();
        
            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first(); 

            return view($this->view.$FormId.'view',compact(['FormId','objResponse','user_approval_level','objRights']));
              
        }

    }
  
    public function attachment($id){

        if(!is_null($id)){
        
            $FormId     =   $this->form_id;

            $objResponse = DB::table('TBL_MST_CUSTOMERCODE')->where('CCODEDEFIID','=',$id)->first();

            $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
                    ->where('VTID','=',$this->vtid_ref)
                     ->select('VTID','VCODE','DESCRIPTIONS')
                    ->get()
                    ->toArray();

                    $objAttachments = DB::table('TBL_MST_ATTACHMENT')                    
                        ->where('TBL_MST_ATTACHMENT.VTID_REF','=',$this->vtid_ref)
                        ->where('TBL_MST_ATTACHMENT.ATTACH_DOCNO','=',$id)
                        ->where('TBL_MST_ATTACHMENT.CYID_REF','=',Auth::user()->CYID_REF)
                        ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                        ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                        ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                        ->get()->toArray();

                return view($this->view.$FormId.'attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments']));
        }

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

        if(!empty($sp_listing_result))
            {
                foreach ($sp_listing_result as $key=>$valueitem)
            {  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$valueitem->LAVELS;
            }
            }
        

            
            $req_data =  json_decode($request['ID']);

            $wrapped_links = $req_data; 
            $multi_array = $wrapped_links;
            $iddata = [];
            
            foreach($multi_array as $index=>$row)
            {
                $m_array[$index] = $row->ID;
                $iddata['APPROVAL'][]['ID'] =  $row->ID;
            }
            $xml = ArrayToXml::convert($iddata);
            
            $USERID_REF =   Auth::user()->USERID;
            $VTID_REF   =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');
            $FYID_REF   =   Session::get('FYID_REF');       
            $TABLE      =   "TBL_MST_CUSTOMERCODE";
            $FIELD      =   "CCODEDEFIID";
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
        
        return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','exist'=>'norecord']);
        
        }else{
        return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','exist'=>'Some Error']);
        }
        
        exit();    
        }


    public function cancel(Request $request){

       $id = $request->{0};

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_CUSTOMERCODE";
        $FIELD      =   "CCODEDEFIID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
        
        $canceldata[0]=[
            'NT'  => 'TBL_MST_CUSTOMERCODE',
       ];        
       $links["TABLES"] = $canceldata; 
       $cancelxml = ArrayToXml::convert($links);

        $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$cancelxml];
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


}
