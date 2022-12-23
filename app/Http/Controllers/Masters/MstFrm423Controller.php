<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Master\TblMstFrm423;
use App\Models\Admin\TblMstUser;
use Auth;
use DB;
use Session;
use Response;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class MstFrm423Controller extends Controller
{
   
    protected $form_id = 423;
    protected $vtid_ref   = 495;  //voucher type id
    protected $view     = "masters.Payroll.CompanyWorkingDay.mstfrm";

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

        $FormId    = $this->form_id;
        $objRights = DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();

        $objDataList    =   DB::table('TBL_MST_COMPANY_WORKING')->get();
        
       return view($this->view.$FormId,compact(['objRights','objDataList','FormId']));  
    }

    

    //uploads attachments files
    public function docuploads(Request $request){

        $formData = $request->all();

        $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size = config("erpconst.attachments.max_size") * 1020 * 1024;

        //get data
        $FormId         =   $this->form_id;
        $VTID           =   $formData["VTID_REF"]; 
        $ATTACH_DOCNO   =   $formData["ATTACH_DOCNO"]; 
        $ATTACH_DOCDT   =   $formData["ATTACH_DOCDT"]; 
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');       
        // @XML	xml
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();
        
        $destinationPath = storage_path()."/docs/company".$CYID_REF."/calculationtemplate";

        if ( !is_dir($destinationPath) ) {
            mkdir($destinationPath, 0777, true);
        }

        $uploaded_data = [];
        $invlid_files = "";

        $duplicate_files="";

        foreach($formData["REMARKS"] as $index=>$row_val){

                if(isset($formData["FILENAME"][$index])){

                    $uploadedFile = $formData["FILENAME"][$index]; 
                    
                    //$filenamewithextension  = $formData["FILENAME"][$index]->getClientOriginalName();

                    $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
                    $filesize               =   $uploadedFile ->getSize();  
                    $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );

                    //$filenametostore        =   $filenamewithextension; 

                    $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."#_".$filenamewithextension;  
                    
                    echo $filenametostore ;

                    if ($uploadedFile->isValid()) {

                        if(in_array($extension,$allow_extnesions)){
                            
                            if($filesize < $allow_size){

                                $custfilename = $destinationPath."/".$filenametostore;

                                if (!file_exists($custfilename)) {

                                   $uploadedFile->move($destinationPath, $filenametostore);  //upload in dir if not exists
                                   $uploaded_data[$index]["FILENAME"] =$filenametostore;
                                   $uploaded_data[$index]["LOCATION"] = $destinationPath."/";
                                   $uploaded_data[$index]["REMARKS"] = is_null($row_val) ? '' : trim($row_val);

                                }else{

                                    $duplicate_files = " ". $duplicate_files.$filenamewithextension. " ";
                                }
                                

                                
                            }else{
                                
                                $invlid_files = $invlid_files.$filenamewithextension." (invalid size)  "; 
                            } //invalid size
                            
                        }else{

                            $invlid_files = $invlid_files.$filenamewithextension." (invalid extension)  ";                             
                        }// invalid extension
                    
                    }else{
                            
                        $invlid_files = $invlid_files.$filenamewithextension." (invalid)"; 
                    }//invalid

                }

        }//foreach

      
        if(empty($uploaded_data)){
            return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
        }
     
        $wrapped_links["ATTACHMENT"] = $uploaded_data;     //root node: <ATTACHMENT>
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
        
          
       // try {

             //save data
             $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);

      //  } catch (\Throwable $th) {
        
        //    return redirect()->route("master",[4,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");
    
      //  }
     
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


    public function add(){

        $FormId   = $this->form_id;
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $objSON = DB::table('TBL_MST_DOCNO_DEFINITION')
            ->where('VTID_REF','=',$this->vtid_ref)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
           // ->where('FYID_REF','=',$FYID_REF)
            ->where('STATUS','=','A')
            ->select('TBL_MST_DOCNO_DEFINITION.*')
            ->first();

            //dd($objSON);
            
        $objAutoGenNo='';
        if(!empty($objSON )){

            if($objSON->SYSTEM_GRSR == "1")
            {
                if($objSON->PREFIX_RQ == "1")
                {
                    $objAutoGenNo = $objSON->PREFIX;
                }        
                if($objSON->PRE_SEP_RQ == "1")
                {
                    if($objSON->PRE_SEP_SLASH == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'/';
                    }
                    if($objSON->PRE_SEP_HYPEN == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'-';
                    }
                }        
                if($objSON->NO_MAX)
                {   
                    $objAutoGenNo = $objAutoGenNo.str_pad($objSON->LAST_RECORDNO+1, $objSON->NO_MAX, "0", STR_PAD_LEFT);
                }
                
                if($objSON->NO_SEP_RQ == "1")
                {
                    if($objSON->NO_SEP_SLASH == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'/';
                    }
                    if($objSON->NO_SEP_HYPEN == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'-';
                    }
                }
                if($objSON->SUFFIX_RQ == "1")
                {
                    $objAutoGenNo = $objAutoGenNo.$objSON->SUFFIX;
                }
            }
        } 

        
        $AlpsStatus =   $this->AlpsStatus();
       
        return view($this->view.$FormId.'add',compact(['objAutoGenNo','AlpsStatus','objSON','FormId']));
   }

   public function codeduplicate(Request $request){
        
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $COMPANY_HOLIDAY_CODE =   strtoupper($request['COMPANY_HOLIDAY_CODE']);
        
        $objLabel = DB::table('TBL_MST_COMPANY_WORKING ')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('COMPANY_HOLIDAY_CODE','=',$COMPANY_HOLIDAY_CODE)
        ->select('COMPANY_HOLIDAY_CODE')
        ->first();
        // dd($objLabel);
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }

    
   public function save(Request $request){
    
        $data = array();
        if(isset($_REQUEST['Weeklyoff']) && !empty($_REQUEST['Weeklyoff'])){
            foreach($_REQUEST['Weeklyoff'] as $key=>$val){
				if($val == true)
				{
                $data[] = array(
                'WORIDAY_DATE' => trim($_REQUEST['popupMENU'][$key]),
                );
				}
            }
        }

        //dd($data);

        if(!empty($data)){ 
            $wrapped_links["MAT"] = $data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $XMLMAT = NULL; 
        }  
       
        $COMPANY_WORKING_CODE    = $request['COMPANY_WORK_CODE'];
        $COMPANY_WORKING_DATE    = $request['COMPANY_WORK_DATE']; 
        $YRID_REF                = $request['YRID_REF']; 
        $MTID_REF                = $request['MTID_REF']; 
        
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID       =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "ADD";
        $IPADDRESS  =   $request->getClientIp();
        
        $condition_data = [
            $COMPANY_WORKING_CODE, $COMPANY_WORKING_DATE, $YRID_REF,    $MTID_REF,  $FYID_REF, $XMLMAT, 
            $CYID_REF, $BRID_REF,$VTID, $USERID,$UPDATE, $UPTIME, $ACTION, $IPADDRESS
        ];

           $sp_result = DB::select('EXEC SP_COMPANY_WORKING_IN ?,?,?,?,?,?, ?,?,?,?,?,?,?,?', $condition_data);
      
            return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);
        
            exit();    
        }

    public function edit($id){

        if(!is_null($id))
        {
            $FormId     =   $this->form_id;
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');
            $Status     =   "A";

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $HDR = DB::table('TBL_MST_COMPANY_WORKING ')
            ->where('TBL_MST_COMPANY_WORKING .CP_WORKINGID','=',$id)
            ->leftJoin('TBL_MST_MONTH', 'TBL_MST_COMPANY_WORKING.MTID_REF','=','TBL_MST_MONTH.MTID')
			->leftJoin('TBL_MST_YEAR', 'TBL_MST_COMPANY_WORKING.YRID_REF','=','TBL_MST_YEAR.YRID')
            ->select('TBL_MST_COMPANY_WORKING .*','TBL_MST_MONTH.*','TBL_MST_YEAR.YRDESCRIPTION')
            ->first();

            $MAT = DB::table('TBL_MST_COMPANY_WORKING_DETAIL ')
            ->where('TBL_MST_COMPANY_WORKING_DETAIL .CP_WORKINGID_REF','=',$id)
            ->select('TBL_MST_COMPANY_WORKING_DETAIL .*')
            ->get()
            ->toArray();
            $objCount = count($MAT);
           
            return view($this->view.$FormId.'edit',compact(['HDR','MAT','user_approval_level','objRights','FormId','objCount']));
        }

    }//edit function

    //update the data
   public function update(Request $request)
   {
        $data = array();
        if(isset($_REQUEST['Weeklyoff']) && !empty($_REQUEST['Weeklyoff'])){
            foreach($_REQUEST['Weeklyoff'] as $key=>$val){
				if($val == true)
				{
                $data[] = array(
                'WORIDAY_DATE' => trim($_REQUEST['popupMENU'][$key]),
                );
				}
            }
        }
        //dd($data);

        if(!empty($data)){ 
            $wrapped_links["MAT"] = $data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $XMLMAT = NULL; 
        }  
       
        $CP_WORKINGID            = $request['CP_WORKINGID'];
        $COMPANY_WORKING_CODE    = $request['COMPANY_WORKING_CODE'];
        $COMPANY_WORKING_DATE    = $request['COMPANY_WORKING_DATE']; 
        $YRID_REF                = $request['YRID_REF']; 
        $MTID_REF                = $request['MTID_REF']; 
        
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID       =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();
        
        $condition_data = [
            $CP_WORKINGID, $COMPANY_WORKING_CODE, $COMPANY_WORKING_DATE, $YRID_REF,    $MTID_REF,  $FYID_REF, $XMLMAT, 
            $CYID_REF, $BRID_REF,$VTID, $USERID,$UPDATE, $UPTIME, $ACTION, $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_COMPANY_WORKING_UP ?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?', $condition_data);

        return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);
        
        exit();           
    } // update function
    

    //singleApprove begin
    
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

    if(!empty($sp_listing_result))
        {
            foreach ($sp_listing_result as $key=>$salesenquiryitem)
        {  
            $record_status = 0;
            $Approvallevel = "APPROVAL".$salesenquiryitem->LAVELS;
        }
        }
       
        $data = array();
        if(isset($_REQUEST['Weeklyoff']) && !empty($_REQUEST['Weeklyoff'])){
            foreach($_REQUEST['Weeklyoff'] as $key=>$val){
				if($val == true)
				{
                $data[] = array(
                'WORIDAY_DATE' => trim($_REQUEST['popupMENU'][$key]),
                );
				}
            }
        }
        //dd($data);
        if(!empty($data)){ 
            $wrapped_links["MAT"] = $data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $XMLMAT = NULL; 
        }  
       
        $CP_WORKINGID            = $request['CP_WORKINGID'];
        $COMPANY_WORKING_CODE    = $request['COMPANY_WORKING_CODE'];
        $COMPANY_WORKING_DATE    = $request['COMPANY_WORKING_DATE']; 
        $YRID_REF                = $request['YRID_REF']; 
        $MTID_REF                = $request['MTID_REF']; 
        
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID       =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   $Approvallevel;
        $IPADDRESS  =   $request->getClientIp();
        
        $condition_data = [
            $CP_WORKINGID, $COMPANY_WORKING_CODE, $COMPANY_WORKING_DATE, $YRID_REF,    $MTID_REF,  $FYID_REF, $XMLMAT, 
            $CYID_REF, $BRID_REF,$VTID, $USERID,$UPDATE, $UPTIME, $ACTION, $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_COMPANY_WORKING_UP ?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?', $condition_data);

        return Response::json(['success' =>true,'msg' => 'Record Successfully Approved.']);
    
        exit();     
    }

    public function view($id){

        if(!is_null($id))
        {
            $FormId     =   $this->form_id;
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');
            $Status     =   "A";

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();
            

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $HDR = DB::table('TBL_MST_COMPANY_WORKING ')
            ->where('TBL_MST_COMPANY_WORKING .CP_WORKINGID','=',$id)
            ->leftJoin('TBL_MST_MONTH', 'TBL_MST_COMPANY_WORKING.MTID_REF','=','TBL_MST_MONTH.MTID')
			->leftJoin('TBL_MST_YEAR', 'TBL_MST_COMPANY_WORKING.YRID_REF','=','TBL_MST_YEAR.YRID')
            ->select('TBL_MST_COMPANY_WORKING .*','TBL_MST_MONTH.*','TBL_MST_YEAR.YRDESCRIPTION')
            ->first();

            $MAT = DB::table('TBL_MST_COMPANY_WORKING_DETAIL ')
            ->where('TBL_MST_COMPANY_WORKING_DETAIL .CP_WORKINGID_REF','=',$id)
            ->select('TBL_MST_COMPANY_WORKING_DETAIL .*')
            ->get()
            ->toArray();
            $objCount = count($MAT);

            return view($this->view.$FormId.'view',compact(['HDR','MAT','user_approval_level','objRights','FormId']));
        }

    }//view function
  

    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objCondition = DB::table('TBL_MST_COMPANY_WORKING')
            ->where('TBL_MST_COMPANY_WORKING.CP_WORKINGID','=',$id)
            ->select('TBL_MST_COMPANY_WORKING.*')
            ->first();

                $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
                ->where('VTID','=',$this->vtid_ref)
                ->select('VTID','VCODE','DESCRIPTIONS','INDATE')
                ->get()
                ->toArray();
            
                //uplaoded docs
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
                 $FormId         =   $this->form_id;
                 return view($this->view.$FormId.'attachment',compact(['objCondition','objMstVoucherType','objAttachments','FormId']));
        }

    }
    

    public function cancel(Request $request){

        $id = $request->{0};

        $objResponse = DB::table('TBL_MST_COMPANY_WORKING ')->where('CP_WORKINGID','=',$id)->select('*')->first();

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_COMPANY_WORKING ";
        $FIELD      =   "CP_WORKINGID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_MST_COMPANY_WORKING_DETAIL ',
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

    public function MultiApprove(Request $request){

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
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
                foreach ($sp_listing_result as $key=>$salesenquiryitem)
            {  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$salesenquiryitem->LAVELS;
            }
            }
        
                       
            $req_data =  json_decode($request['ID']);

            // dd($req_data);
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
            $VTID_REF   =   $this->vtid_ref;  //voucher type id
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');
            $FYID_REF   =   Session::get('FYID_REF');       
            $TABLE      =   "TBL_MST_TNC";
            $FIELD      =   "TNCID";
            $ACTIONNAME =   $Approvallevel;
            $UPDATE     =   Date('Y-m-d');
            $UPTIME     =   Date('h:i:s.u');
            $IPADDRESS  =   $request->getClientIp();
      
        // dd($xml);
        
        $log_data = [ 
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];
            
        $sp_result = DB::select('EXEC SP_MST_MULTIAPPROVAL ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       

        if($sp_result[0]->RESULT=="All records approved"){

        return Response::json(['approve' =>true,'msg' => 'Record successfully Approved.']);

        }elseif($sp_result[0]->RESULT=="NO RECORD FOR APPROVAL"){
        
        return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','termscondition'=>'norecord']);
        
        }else{
        return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','termscondition'=>'Some Error']);
        }
        
        exit();    
    }


    public function AlpsStatus(){
        $COMPANY_NAME   =   DB::table('TBL_MST_COMPANY')->where('STATUS','=','A')->where('CYID','=',Auth::user()->CYID_REF)->select('TBL_MST_COMPANY.NAME')->first()->NAME;
        $disabled       =   strpos($COMPANY_NAME,"ALPS")!== false?'disabled':'';
        $hidden         =   strpos($COMPANY_NAME,"ALPS")!== false?'':'hidden';
        
        return  $ALPS_STATUS=array(
            'hidden'=>$hidden,
            'disabled'=>$disabled
        );
    }
   

    public function getyearCode(Request $request){
        $ObjData = DB::table('TBL_MST_YEAR')->get();
        if(!empty($ObjData)){
        foreach ($ObjData as $index=>$dataRow){
            $row = '';
            $row = $row.'<tr >
            <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_MACHINEID_REF[]" id="subgl_'.$dataRow->YRID .'"  class="clsyear" value="'.$dataRow->YRID.'" ></td>
            <td width="39%" class="ROW2">'.$dataRow->YRCODE;
            $row = $row.'<input type="hidden" id="txtsubgl_'.$dataRow->YRID.'" data-desc="'.$dataRow->YRCODE .'" value="'.$dataRow->YRID.'"/></td>';
            $row = $row.'<td width="39%" class="ROW3">'.$dataRow->YRDESCRIPTION.'</td>';
            $row = $row.'</tr>';
            echo $row;
        }
        }else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
        }
        exit();

    }


    public function getMTID(Request $request){
        $Status = "A";
        $ObjData =  DB::select('SELECT * FROM TBL_MST_MONTH  
        WHERE  CYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by MTCODE ASC', [Auth::user()->CYID_REF, 'A' ]);
    
            if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){
                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_MTID_REF[]" id="MTID_'.$dataRow->MTID .'"  class="clsMTID" value="'.$dataRow->MTID.'" ></td>
                <td class="ROW2">'.$dataRow->MTCODE.' - '.$dataRow->MTDESCRIPTION.'';
                $row = $row.'<input type="hidden" id="txtMTID_'.$dataRow->MTID.'" data-desc="'.$dataRow->MTCODE.' - '.$dataRow->MTDESCRIPTION.'" 
                value="'.$dataRow->MTID.'"/></td>
                <td class="ROW3" >'.$dataRow->INDATE.'</td></tr>';
    
                echo $row;
            }
    
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    }


    public function getMTIDMaterial(Request $request){
        $year = $request->year;
        $month = $request->month;
		$ObjData =  DB::select('SELECT * FROM TBL_MST_YEAR  
        WHERE  CYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ? AND YRID = ?', [Auth::user()->CYID_REF, 'A' ,$year]);
        if($month > 12){
            echo '<tr><td colspan="2">Invalid Month.</td></tr>';
        }else{
          $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $ObjData[0]->YRDESCRIPTION); 
            for( $i=1; $i<= $totalDays; $i++){
           echo $dates[]= str_pad($i,2,'0', STR_PAD_LEFT);
        }
        
        if(!empty($month)){
          
            foreach ($dates as $key=>$date){

                $result = $ObjData[0]->YRDESCRIPTION."-".$month."-".$date;

                echo'<tr class="participantRow"> 
                        <td><input type="text" name="popupMENU['.$key.']" id="popupMENU_'.$date.'" class="form-control" style="text-align:left;" value="'.$result.'" autocomplete="off" readonly></td>
                        <td><input type="checkbox" name="Weeklyoff['.$key.']" id="Weeklyoff_'.$date.'" autocomplete="off" checked style="align-items: center"></td>
                    <tr/>';
                }

                }else{
                    echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
                exit();
            }

        }










































































        
}
