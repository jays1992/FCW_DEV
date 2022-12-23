<?php
namespace App\Http\Controllers\Masters;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm505;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;

class MstFrm505Controller extends Controller{

    protected $form_id    = 505;
    protected $vtid_ref   = 575;
    protected $view       = "masters.PreSales.EmployeeHierarchyMaster.mstfrm";       
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

        $objRights      =   DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))                                
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();
        
        $FormId         =   $this->form_id;

        $objDataList = DB::table('TBL_MST_EMPLOYEE_HIERARCHY_HDR')
                        ->where('TBL_MST_EMPLOYEE_HIERARCHY_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                        ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_MST_EMPLOYEE_HIERARCHY_HDR.EMPHIERCHY_TEAMID_REF','=','TBL_MST_EMPLOYEE.EMPID')   
                        ->select('TBL_MST_EMPLOYEE_HIERARCHY_HDR.*','TBL_MST_EMPLOYEE.EMPID','TBL_MST_EMPLOYEE.EMPCODE','TBL_MST_EMPLOYEE.FNAME')
                        ->orderBy('TBL_MST_EMPLOYEE_HIERARCHY_HDR.EMPHIERCHYID','DESC')
                        ->get();
                            
        return view($this->view.$FormId,compact(['objRights','objDataList','FormId']));
    }
   
    
    public function add(){ 

        $FormId   =   $this->form_id;
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');

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

        return view($this->view.$FormId.'add',compact(['objDD','objDOCNO','FormId']));
    }

   
    public function save(Request $request){

        $EMPHIERCHY_NO           =   trim($request['DOC_NO'])?trim($request['DOC_NO']):NULL;
        $EMPHIERCHY_DATE         =   trim($request['DOC_DT'])?trim($request['DOC_DT']):NULL;
        $EMPHIERCHY_TEAMID_REF   =   trim($request['TEAM_MSTID_REF'])?trim($request['TEAM_MSTID_REF']):NULL;
        $REPORTING_TOID_REF      =   trim($request['REPORTING_TOID_REF'])?trim($request['REPORTING_TOID_REF']):NULL;

        $MatDetails  = array();
        if(isset($request['SRNo']) && !empty($_REQUEST['SRNo'])){
            foreach($request['SRNo'] as $key=>$val){

                $MatDetails[] = array(
                'SR_NO'              => trim($request['SRNo'][$key])?trim($request['SRNo'][$key]):NULL,
                'EMPID_REF'         => trim($request['EMPID_REF'][$key])?trim($request['EMPID_REF'][$key]):NULL,
                'REPORTTOID_REF'    => trim($request['REPORTTOID_REF'][$key])?trim($request['REPORTTOID_REF'][$key]):NULL,
                'EMPACTIVE'         => trim($request['EMPACTIVE'][$key])?trim($request['EMPACTIVE'][$key]):NULL,
                );
            }
        }
        //dd($MatDetails);

        if(!empty($MatDetails)){
            $wrapped_links["MAT"] = $MatDetails; 
            $MAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $MAT = NULL; 
        } 

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');    
        $USERID_REF     =   Auth::user()->USERID;   
        $VTID_REF       =   $this->vtid_ref;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();

        $array_data     = [$EMPHIERCHY_NO, $EMPHIERCHY_DATE, $EMPHIERCHY_TEAMID_REF, $CYID_REF, $BRID_REF, $FYID_REF, $VTID_REF,  $MAT, $USERID_REF, $UPDATE,$UPTIME, $ACTION, $IPADDRESS,$REPORTING_TOID_REF];
        
        try {

            $sp_result = DB::select('EXEC SP_EMPLOYEE_HIERARCHY_IN ?,?,?,?,?,   ?,?,?,?,?,  ?,?,?,?', $array_data);

            } catch (\Throwable $th) {
            
                return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);

            }
    
            if(Str::contains(strtoupper($sp_result[0]->RESULT), 'SUCCESS')){

                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);
    
            }elseif(Str::contains(strtoupper($sp_result[0]->RESULT), 'DUPLICATE RECORD')){
            
                return Response::json(['errors'=>true,'msg' => $sp_result[0]->RESULT,'exist'=>'duplicate']);
                
            }else{
    
                return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
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

        if(!is_null($id)){
        
            $FormId     =   $this->form_id;
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF'); 

            $objResponse =   DB::select("SELECT T1.*,
                                CONCAT(T2.EMPCODE,'-',T2.FNAME) AS EMPCODE_NAME,    T2.EMPID,
                                CONCAT(T3.EMPCODE,'-',T3.FNAME) AS REPORTTOCODE_NAME, T3.EMPID AS REPORTTO_EMPID
                            
                                FROM TBL_MST_EMPLOYEE_HIERARCHY_HDR T1
                                LEFT JOIN TBL_MST_EMPLOYEE T2 ON T1.EMPHIERCHY_TEAMID_REF=T2.EMPID
                                LEFT JOIN TBL_MST_EMPLOYEE T3 ON T1.REPORTING_TOID_REF=T3.EMPID
                                WHERE T1.EMPHIERCHYID='$id' ")[0];

            $MAT            =   DB::select("SELECT T1.*,
                                CONCAT(T2.EMPCODE,'-',T2.FNAME) AS EMPCODE_NAME,    T2.EMPID,
                                CONCAT(T3.EMPCODE,'-',T3.FNAME) AS REPORTCODE_NAME, T3.EMPID AS REPORT_EMPID
                            
                                FROM TBL_MST_EMPLOYEE_HIERARCHY_MAT T1
                                LEFT JOIN TBL_MST_EMPLOYEE T2 ON T1.EMPID_REF=T2.EMPID
                                LEFT JOIN TBL_MST_EMPLOYEE T3 ON T1.REPORTTOID_REF=T3.EMPID
                                WHERE T1.EMPHIERCHYID_REF='$id' ");
                                $MAT    = count($MAT) > 0 ?$MAT:[0];

            $objRights = DB::table('TBL_MST_USERROLMAP')
                            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
                            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                            ->first();
            
            return view($this->view.$FormId.$type,compact(['objResponse','objRights','ActionStatus','FormId','MAT']));
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

        $requestType    = $request->requestType;
        $Approvallevel  =   $requestType =='update'?'EDIT':$Approvallevel;
        $msgTxt         =   $requestType =='update'?'updated':'approved';
         
        $EMPHIERCHY_NO           =   trim($request['DOC_NO'])?trim($request['DOC_NO']):NULL;
        $EMPHIERCHY_DATE         =   trim($request['DOC_DT'])?trim($request['DOC_DT']):NULL;
        $EMPHIERCHY_TEAMID_REF   =   trim($request['TEAM_MSTID_REF'])?trim($request['TEAM_MSTID_REF']):NULL;
        $REPORTING_TOID_REF      =   trim($request['REPORTING_TOID_REF'])?trim($request['REPORTING_TOID_REF']):NULL;

        $MatDetails  = array();
        if(isset($request['SRNo']) && !empty($_REQUEST['SRNo'])){
            foreach($request['SRNo'] as $key=>$val){

                $MatDetails[] = array(
                'SR_NO'              => trim($request['SRNo'][$key])?trim($request['SRNo'][$key]):NULL,
                'EMPID_REF'         => trim($request['EMPID_REF'][$key])?trim($request['EMPID_REF'][$key]):NULL,
                'REPORTTOID_REF'    => trim($request['REPORTTOID_REF'][$key])?trim($request['REPORTTOID_REF'][$key]):NULL,
                'EMPACTIVE'         => trim($request['EMPACTIVE'][$key])?trim($request['EMPACTIVE'][$key]):NULL,
                );
            }
        }
        //dd($MatDetails);

        if(!empty($MatDetails)){
            $wrapped_links["MAT"] = $MatDetails; 
            $MAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $MAT = NULL; 
        }

        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
        $newDateString = NULL;

        $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt) ){
            
            $newdt = str_replace( "/", "-",  $newdt ) ;

            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }

        $DODEACTIVATED = $newDateString;

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID_REF   =   $this->vtid_ref;
        $USERID_REF =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   $Approvallevel;
        $IPADDRESS  =   $request->getClientIp();

        $array_data = [$EMPHIERCHY_NO, $EMPHIERCHY_DATE, $EMPHIERCHY_TEAMID_REF, $CYID_REF, $BRID_REF, $FYID_REF, $VTID_REF, $MAT, $USERID_REF, $UPDATE,$UPTIME, $ACTION, $IPADDRESS,$DEACTIVATED,  $DODEACTIVATED,$REPORTING_TOID_REF ];   

       //DD($array_data);

        try {

            $sp_result = DB::select('EXEC SP_EMPLOYEE_HIERARCHY_UP ?,?,?,?,?,   ?,?,?,?,?,  ?,?,?,?,?,?', $array_data);

            } catch (\Throwable $th) {

                return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);
            }    

            if($sp_result[0]->RESULT=="SUCCESS"){  

            return Response::json(['success' =>true,'msg' => 'Record successfully '.$msgTxt]);
            
            }elseif($sp_result[0]->RESULT=="RECORD NOT FOUND"){
            
                return Response::json(['errors'=>true,'exist'=>'norecord']);
                
            }else{

                return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);
            }
            
            exit();     
        }


        public function codeduplicate(Request $request){

            $FormId          = $this->form_id;
            $CYID_REF        = Auth::user()->CYID_REF;
            $BRID_REF        = Session::get('BRID_REF');
            $DOC_NO          = $request['DOC_NO'];
            $TEAM_MSTID_REF  = $request['TEAM_MSTID_REF'];
    
            $objLabel = DB::table('TBL_MST_EMPLOYEE_HIERARCHY_HDR')
            ->where('EMPHIERCHY_NO','=',$DOC_NO)
            ->first();

            $objLabel1 = DB::table('TBL_MST_EMPLOYEE_HIERARCHY_HDR')
            ->where('EMPHIERCHY_TEAMID_REF','=',$TEAM_MSTID_REF)
            ->first();
            if($objLabel){  
    
                return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
            
            }elseif($objLabel1){
                
                return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
            }           
            
            else{
    
                return Response::json(['not exists'=>true,'msg' => 'Ok']);
            }
            
            exit();
       }

    //Cancel the data
     public function cancel(Request $request){

        $id = $request->{0};
        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');      
        $TABLE      =   "TBL_MST_EMPLOYEE_HIERARCHY_HDR";
        $FIELD      =   "EMPHIERCHYID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_MST_EMPLOYEE_HIERARCHY_MAT',
        ];
    
        $wrapped_links["TABLES"] = $req_data; 
        
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$XMLTAB];
        $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);

        //dd($sp_result);


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
            $objResponse = DB::table('TBL_MST_EMPLOYEE_HIERARCHY_HDR')
                            ->where('EMPHIERCHYID','=',$id)
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
                              ->where('TBL_MST_ATTACHMENT.FYID_REF','=',Session::get('FYID_REF'))
                              ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                              ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                              ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                              ->get()->toArray();

            return view($this->view.$FormId.'attachment',compact(['objResponse','objMstVoucherType','objAttachments','FormId']));
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
    
    $destinationPath = storage_path()."/docs/company".$CYID_REF."/EmployeeHierarchyMaster";

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
        return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","The file is already exist");
    }

    $wrapped_links["ATTACHMENT"] = $uploaded_data;
    $ATTACHMENTS_XMl = ArrayToXml::convert($wrapped_links);

    $attachment_data = [$VTID, $ATTACH_DOCNO, $ATTACH_DOCDT, $CYID_REF, $BRID_REF, $FYID_REF, $ATTACHMENTS_XMl, $USERID, $UPDATE, $UPTIME, $ACTION, $IPADDRESS ];

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

/*************************************   Team Name Code    ****************************************************** */

        public function getTeamMstName(Request $request){
                $ObjData = DB::table('TBL_MST_TEAM_MASTER_HRD')
                            ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_MST_TEAM_MASTER_HRD.EMPID_REF','=','TBL_MST_EMPLOYEE.EMPID')
                            ->where('TBL_MST_TEAM_MASTER_HRD.CYID_REF','=',Auth::user()->CYID_REF)
                            ->whereRaw('(TBL_MST_TEAM_MASTER_HRD.DEACTIVATED IS NULL  OR TBL_MST_TEAM_MASTER_HRD.DEACTIVATED = 0)')
                            ->where('TBL_MST_TEAM_MASTER_HRD.STATUS','=','A')
                            ->select('TBL_MST_TEAM_MASTER_HRD.*', 'TBL_MST_EMPLOYEE.EMPID','TBL_MST_EMPLOYEE.EMPCODE','TBL_MST_EMPLOYEE.FNAME')
                            ->get();

                if(isset($ObjData) && !empty($ObjData)){
                    foreach ($ObjData as $index=>$dataRow){
                    echo'<tr>
                     <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->EMPID_REF .'" class="clsteam" value="'.$dataRow->EMPID_REF.'" ></td>
                     <td class="ROW2">'.$dataRow->EMPCODE.'</td>
                     <td class="ROW3">'.$dataRow->FNAME.'</td>
                     <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->EMPID_REF.'" data-desc="'.$dataRow->EMPCODE.'-'.$dataRow->FNAME.'" data-ccname="'.$dataRow->FNAME.'" value="'.$dataRow->EMPID_REF.'"/></td>
                    </tr>';
                    }
                }
                else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
            exit();

        }


        
/*************************************   Reporting To Code    ****************************************************** */

            public function getReportngTo(Request $request){


                $ObjData = DB::table('TBL_MST_EMPLOYEE')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                            ->where('STATUS','=','A')
                            ->get();

                if(isset($ObjData) && !empty($ObjData)){
                    foreach ($ObjData as $index=>$dataRow){

                        echo'<tr>
                        <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->EMPID .'" class="clsreprtng" value="'.$dataRow->EMPID.'" ></td>
                        <td class="ROW2">'.$dataRow->EMPCODE.'</td>
                        <td class="ROW3">'.$dataRow->FNAME.'</td>
                        <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->EMPID.'" data-desc="'.$dataRow->EMPCODE.'-'.$dataRow->FNAME.'" data-ccname="'.$dataRow->FNAME.'" value="'.$dataRow->EMPID.'"/></td>
                        </tr>';
                        }
                }
                else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
            exit();
            }

 
 
 /*************************************   Employee Name Code    ****************************************************** */ 
 
            public function getEmpCode(Request $request){

                $ObjData = DB::table('TBL_MST_EMPLOYEE')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            //->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                            //->where('STATUS','=','A')
                            ->get();

                if(isset($ObjData) && !empty($ObjData)){
                    foreach ($ObjData as $index=>$dataRow){

                        echo'<tr>
                        <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->EMPID .'" class="clsemp" value="'.$dataRow->EMPID.'" ></td>
                        <td class="ROW2">'.$dataRow->EMPCODE.'</td>
                        <td class="ROW3">'.$dataRow->FNAME.'</td>
                        <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->EMPID.'" data-desc="'.$dataRow->EMPCODE.'-'.$dataRow->FNAME.'" data-ccname="'.$dataRow->FNAME.'" value="'.$dataRow->EMPID.'"/></td>
                        </tr>';
                        }
                }
                else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
            exit();
            }


/*************************************   Employee Name Code    ****************************************************** */ 
 
             public function getReportToCode(Request $request){

                $EMPID_REF         = $request->EMPID_REF;

                $ObjData = DB::table('TBL_MST_EMPLOYEE')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                            ->where('STATUS','=','A')
                            ->where('EMPID','!=',$EMPID_REF)
                            ->get();

                if(isset($ObjData) && !empty($ObjData)){
                    foreach ($ObjData as $index=>$dataRow){

                        echo'<tr>
                        <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->EMPID .'" class="clsreprt" value="'.$dataRow->EMPID.'" ></td>
                        <td class="ROW2">'.$dataRow->EMPCODE.'</td>
                        <td class="ROW3">'.$dataRow->FNAME.'</td>
                        <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->EMPID.'" data-desc="'.$dataRow->EMPCODE.'-'.$dataRow->FNAME.'" data-ccname="'.$dataRow->FNAME.'" value="'.$dataRow->EMPID.'"/></td>
                        </tr>';
                        }
                }
                else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
            exit();
            }

            




}
