<?php
namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm439;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;

class MstFrm441Controller extends Controller{

    protected $form_id  =   441;
    protected $vtid_ref =   511;
    protected $view     =   "masters.PreSales.ProspectMaster.mstfrm";
       
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        $objRights = DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first(); 

        $FormId         =   $this->form_id;

        $objDataList    =   DB::table('TBL_MST_PROSPECT')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->orderBy('PID','desc')
                            ->get();

        return view($this->view.$FormId,compact(['objRights','objDataList','FormId']));

    }
   
    
    public function add(){ 

        $FormId     =   $this->form_id;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $country    =   $this->country();

        $objDD = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('STATUS','=','A')
        ->select('TBL_MST_DOCNO_DEFINITION.*')
        ->first();

        $objDOCNO ='';
        if(!empty($objDD)){
            if($objDD->SYSTEM_GRSR == "1")
            {
                if($objDD->PREFIX_RQ == "1")
                {
                    $objDOCNO = $objDD->PREFIX;
                }        
                if($objDD->PRE_SEP_RQ == "1")
                {
                    if($objDD->PRE_SEP_SLASH == "1")
                    {
                    $objDOCNO = $objDOCNO.'/';
                    }
                    if($objDD->PRE_SEP_HYPEN == "1")
                    {
                    $objDOCNO = $objDOCNO.'-';
                    }
                }        
                if($objDD->NO_MAX)
                {   
                    $objDOCNO = $objDOCNO.str_pad($objDD->LAST_RECORDNO+1, $objDD->NO_MAX, "0", STR_PAD_LEFT);
                }
                
                if($objDD->NO_SEP_RQ == "1")
                {
                    if($objDD->NO_SEP_SLASH == "1")
                    {
                    $objDOCNO = $objDOCNO.'/';
                    }
                    if($objDD->NO_SEP_HYPEN == "1")
                    {
                    $objDOCNO = $objDOCNO.'-';
                    }
                }
                if($objDD->SUFFIX_RQ == "1")
                {
                    $objDOCNO = $objDOCNO.$objDD->SUFFIX;
                }
            }
        }  

        $POC    =   [0];
        return view($this->view.$FormId.'add',compact(['objDD','objDOCNO','country','FormId','POC']));
    }

   
    public function save(Request $request){

        $PCODE          =   trim($request['PCODE']);
        $NAME           =   trim($request['NAME']); 
        $REGADDL1       =   trim($request['REGADDL1']);
        $REGADDL2       =   trim($request['REGADDL2']);
        $REGCTRYID_REF  =   trim($request['REGCTRYID_REF']);
        $REGSTID_REF    =   trim($request['REGSTID_REF']);
        $REGCITYID_REF  =   trim($request['REGCITYID_REF']);
        $REGPIN         =   trim($request['REGPIN']);
        $CORPADDL1      =   trim($request['CORPADDL1']);
        $CORPADDL2      =   trim($request['CORPADDL2']);
        $CORPCTRYID_REF =   trim($request['CORPCTRYID_REF']);
        $CORPSTID_REF   =   trim($request['CORPSTID_REF']);
        $CORPCITYID_REF =   trim($request['CORPCITYID_REF']);
        $CORPPIN        =   trim($request['CORPPIN']);
        $EMAILID        =   trim($request['EMAILID']);
        $WEBSITE        =   trim($request['WEBSITE']);
        $PHNO           =   trim($request['PHNO']);
        $MONO           =   trim($request['MONO']);
        $CPNAME         =   trim($request['CPNAME']);
        $SKYPEID        =   trim($request['SKYPEID']);
        $DEACTIVATED    =   NULL;  
        $DODEACTIVATED  =   NULL;  

        $data   =   array();
        if(isset($_REQUEST['PNAME']) && !empty($_REQUEST['PNAME'])){
            foreach($_REQUEST['PNAME'] as $key=>$val){

                if(trim($_REQUEST['PNAME'][$key]) !=''){
                    $data[] =   array(
                                    'NAME'      =>  trim($_REQUEST['PNAME'][$key]),
                                    'DESIG'     =>  trim($_REQUEST['DESIG'][$key]),
                                    'MONO'      =>  trim($_REQUEST['PMONO'][$key]),
                                    'EMAIL'     =>  trim($_REQUEST['EMAIL'][$key]),
                                    'LLNO'      =>  trim($_REQUEST['LLNO'][$key]),
                                    'AUTHLEVEL' =>  trim($_REQUEST['AUTHLEVEL'][$key]),
                                    'DOB'       =>  trim($_REQUEST['DOB'][$key]),
                                );
                }
            }
        }
      
    
        if(!empty($data)){
            $wrapped_links["POINTOFCONTACT"] = $data; 
            $XML = ArrayToXml::convert($wrapped_links);
        }
        else{
            $XML = NULL; 
        }

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');    
        $USERID         =   Auth::user()->USERID;   
        $VTID_REF       =   $this->vtid_ref;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();

        $log_data = [ 
            $PCODE,$NAME,$REGADDL1,$REGADDL2,$REGCTRYID_REF,
            $REGSTID_REF,$REGCITYID_REF,$REGPIN,$CORPADDL1,$CORPADDL2,
            $CORPCTRYID_REF,$CORPSTID_REF,$CORPCITYID_REF,$CORPPIN,$EMAILID,
            $WEBSITE,$PHNO,$MONO,$CPNAME,$SKYPEID,
            $DEACTIVATED,$DODEACTIVATED,$CYID_REF,$BRID_REF,$FYID_REF,
            $XML,$VTID_REF,$USERID,$UPDATE, $UPTIME,
            $ACTION,$IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_PROSPECT_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?', $log_data);       
        
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');

        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();         
    }

    public function edit($id){
        return $this->showRecord($id,'edit','');
    }
    
    public function view($id){
        return $this->showRecord($id,'view','disabled');
    }

    public function update(Request $request){
        return  $this->updateRecord($request,'update');        
    } 
    
    public function Approve(Request $request){
        return  $this->updateRecord($request,'approve');    
    }

    public function showRecord($id,$type,$ActionStatus){

        $id =   urldecode(base64_decode($id));

        if(!is_null($id)){
        
            $FormId     =   $this->form_id;
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');  
            
            $objRights  =   DB::table('TBL_MST_USERROLMAP')
                            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                            ->first();

            $country    =   $this->country();
            $state      =   $this->state();
            $city       =   $this->city();
            $HDR        =   DB::table('TBL_MST_PROSPECT')
                            ->leftJoin('TBL_MST_COUNTRY', 'TBL_MST_PROSPECT.REGCTRYID_REF','=','TBL_MST_COUNTRY.CTRYID')
                            ->leftJoin('TBL_MST_STATE', 'TBL_MST_PROSPECT.REGSTID_REF','=','TBL_MST_STATE.STID')
                            ->leftJoin('TBL_MST_CITY', 'TBL_MST_PROSPECT.REGCITYID_REF','=','TBL_MST_CITY.CITYID')
                            ->where('PID','=',$id)
                            ->select('TBL_MST_PROSPECT.*', 'TBL_MST_COUNTRY.NAME AS CTNAME','TBL_MST_STATE.NAME AS STNAME','TBL_MST_CITY.NAME AS CITNAME')
                            ->first();

            $MAT        =   DB::table('TBL_MST_PROSPECT')
                            ->leftJoin('TBL_MST_COUNTRY', 'TBL_MST_PROSPECT.CORPCTRYID_REF','=','TBL_MST_COUNTRY.CTRYID')
                            ->leftJoin('TBL_MST_STATE', 'TBL_MST_PROSPECT.CORPSTID_REF','=','TBL_MST_STATE.STID')
                            ->leftJoin('TBL_MST_CITY', 'TBL_MST_PROSPECT.CORPCITYID_REF','=','TBL_MST_CITY.CITYID')
                            ->where('PID','=',$id)
                            ->select('TBL_MST_PROSPECT.*', 'TBL_MST_COUNTRY.NAME AS CORCTNAME','TBL_MST_STATE.NAME AS CORSTNAME','TBL_MST_CITY.NAME AS CORCITNAME')
                            ->first();

                            //DD($HDR);


            $POC        =   DB::table('TBL_MST_PROSPECTPOC')->where('PID_REF','=',$id)->get();
            $POC        =   count($POC) > 0?$POC:[0];

            return view($this->view.$FormId.$type,compact(['HDR','objRights','ActionStatus','FormId','country','POC','state','city','MAT']));
        }
    }

    public function updateRecord($request,$type){

        $FormId     =   $this->form_id;
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
            foreach ($sp_listing_result as $key=>$salesenquiryitem){  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$salesenquiryitem->LAVELS;
            }
        }

        $Approvallevel  =   $type =='update'?'EDIT':$Approvallevel;

        $PCODE          =   trim($request['PCODE']);
        $NAME           =   trim($request['NAME']); 
        $REGADDL1       =   trim($request['REGADDL1']);
        $REGADDL2       =   trim($request['REGADDL2']);
        $REGCTRYID_REF  =   trim($request['REGCTRYID_REF']);
        $REGSTID_REF    =   trim($request['REGSTID_REF']);
        $REGCITYID_REF  =   trim($request['REGCITYID_REF']);
        $REGPIN         =   trim($request['REGPIN']);
        $CORPADDL1      =   trim($request['CORPADDL1']);
        $CORPADDL2      =   trim($request['CORPADDL2']);
        $CORPCTRYID_REF =   trim($request['CORPCTRYID_REF']);
        $CORPSTID_REF   =   trim($request['CORPSTID_REF']);
        $CORPCITYID_REF =   trim($request['CORPCITYID_REF']);
        $CORPPIN        =   trim($request['CORPPIN']);
        $EMAILID        =   trim($request['EMAILID']);
        $WEBSITE        =   trim($request['WEBSITE']);
        $PHNO           =   trim($request['PHNO']);
        $MONO           =   trim($request['MONO']);
        $CPNAME         =   trim($request['CPNAME']);
        $SKYPEID        =   trim($request['SKYPEID']);
        $DEACTIVATED    =   (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
        $newDateString  =   NULL;
        $newdt          =   !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt) ){
            $newdt = str_replace( "/", "-",  $newdt ) ;
            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }

        $DODEACTIVATED  =   $newDateString;

        $data   =   array();
        if(isset($_REQUEST['PNAME']) && !empty($_REQUEST['PNAME'])){
            foreach($_REQUEST['PNAME'] as $key=>$val){

                if(trim($_REQUEST['PNAME'][$key]) !=''){
                    $data[] =   array(
                                    'NAME'      =>  trim($_REQUEST['PNAME'][$key]),
                                    'DESIG'     =>  trim($_REQUEST['DESIG'][$key]),
                                    'MONO'      =>  trim($_REQUEST['PMONO'][$key]),
                                    'EMAIL'     =>  trim($_REQUEST['EMAIL'][$key]),
                                    'LLNO'      =>  trim($_REQUEST['LLNO'][$key]),
                                    'AUTHLEVEL' =>  trim($_REQUEST['AUTHLEVEL'][$key]),
                                    'DOB'       =>  trim($_REQUEST['DOB'][$key]),
                                );
                }
            }
        }
      
    
        if(!empty($data)){
            $wrapped_links["POINTOFCONTACT"] = $data; 
            $XML = ArrayToXml::convert($wrapped_links);
        }
        else{
            $XML = NULL; 
        }

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID_REF   =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   $Approvallevel;
        $IPADDRESS  =   $request->getClientIp();

        $log_data = [ 
            $PCODE,$NAME,$REGADDL1,$REGADDL2,$REGCTRYID_REF,
            $REGSTID_REF,$REGCITYID_REF,$REGPIN,$CORPADDL1,$CORPADDL2,
            $CORPCTRYID_REF,$CORPSTID_REF,$CORPCITYID_REF,$CORPPIN,$EMAILID,
            $WEBSITE,$PHNO,$MONO,$CPNAME,$SKYPEID,
            $DEACTIVATED,$DODEACTIVATED,$CYID_REF,$BRID_REF,$FYID_REF,
            $XML,$VTID_REF,$USERID,$UPDATE, $UPTIME,
            $ACTION,$IPADDRESS
        ];


        //dd($log_data);
        

        $sp_result = DB::select('EXEC SP_PROSPECT_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?', $log_data);       
        
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');

        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
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
        $TABLE      =   "TBL_MST_PROSPECT";
        $FIELD      =   "PID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_MST_PROSPECT',
            'NT'  => 'TBL_MST_PROSPECTPOC',
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

        if(!is_null($id))
        {
            $FormId      =   $this->form_id;
            $objCondition = DB::table('TBL_MST_PROSPECT')
            ->where('PID','=',$id)
            ->first();

            $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
            ->where('VTID','=',$this->vtid_ref)
            ->select('VTID','VCODE','DESCRIPTIONS')
            ->get()->toArray();
            
            $objAttachments = DB::table('TBL_MST_ATTACHMENT')                    
            ->where('TBL_MST_ATTACHMENT.VTID_REF','=',$this->vtid_ref)
            ->where('TBL_MST_ATTACHMENT.ATTACH_DOCNO','=',$id)
            ->where('TBL_MST_ATTACHMENT.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_ATTACHMENT.BRID_REF','=',Session::get('BRID_REF'))
            ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
            ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
            ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
            ->get()->toArray();

            return view($this->view.$FormId.'attachment',compact(['objCondition','objMstVoucherType','objAttachments','FormId']));
        }

    }

    
   public function docuploads(Request $request){

        $FormId   =   $this->form_id;
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
        
        $image_path         =   "docs/company".$CYID_REF."/ProspectMaster";     
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
            return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","The file is already exist");
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

    public function country(){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $ObjData    =   DB::select("SELECT * 
                        FROM TBL_MST_COUNTRY 
                        WHERE CYID_REF='$CYID_REF' AND STATUS='A' AND (DEACTIVATED IS NULL  OR DEACTIVATED = 0)"
                        );

        return $ObjData; 
    
    }

    public function state(){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $ObjData    =   DB::select("SELECT * 
                        FROM TBL_MST_STATE 
                        WHERE CYID_REF='$CYID_REF' AND STATUS='A' AND (DEACTIVATED IS NULL  OR DEACTIVATED = 0)"
                        );

        return $ObjData; 
    
    }

    public function city(){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $ObjData    =   DB::select("SELECT * 
                        FROM TBL_MST_CITY 
                        WHERE CYID_REF='$CYID_REF' AND STATUS='A' AND (DEACTIVATED IS NULL  OR DEACTIVATED = 0)"
                        );

        return $ObjData; 
    
    }

    public function getstate(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $id         =   $request['id'];
        $rowid      =   $request['rowid'];
        $ObjData    =   DB::select("SELECT * 
                        FROM TBL_MST_STATE 
                        WHERE CYID_REF='$CYID_REF' AND CTRYID_REF='$id' AND STATUS='A' AND (DEACTIVATED IS NULL  OR DEACTIVATED = 0)"
                        );

        echo'<option value="">Select</option>';  
        if(isset($ObjData) && !empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){
                $selected   =   $rowid==$dataRow->STID?'selected="selected"':'';
                echo'<option '.$selected.' value="'.$dataRow->STID.'">'.$dataRow->NAME.'</option>';  
            }
        }
        exit();
    }

    public function getcity(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $id         =   $request['id'];
        $rowid      =   $request['rowid'];

        $ObjData    =   DB::select("SELECT * 
                        FROM TBL_MST_CITY 
                        WHERE CYID_REF='$CYID_REF' AND STID_REF='$id' AND STATUS='A' AND (DEACTIVATED IS NULL  OR DEACTIVATED = 0)"
                        );

        echo'<option value="">Select</option>';     
        if(isset($ObjData) && !empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){
                $selected   =   $rowid==$dataRow->CITYID?'selected="selected"':'';
                echo '<option '.$selected.' value="'.$dataRow->CITYID.'">'.$dataRow->NAME.'</option>';            
            }
        }
        exit();
    }

    public function checkDuplicate(Request $request){
        $data = DB::table('TBL_MST_PROSPECT')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('PCODE','=',$request->PCODE)
        ->count();

        echo $data;
        exit();
    }

}
