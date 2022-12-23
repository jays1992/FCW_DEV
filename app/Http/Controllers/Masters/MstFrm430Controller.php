<?php

namespace App\Http\Controllers\Masters;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Master\TblMstFrm430;
use App\Models\Admin\TblMstUser;
use Auth;
use DB;
use Session;
use Response;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class MstFrm430Controller extends Controller
{
   
    protected $form_id = 430;
    protected $vtid_ref   = 500;
    protected $view     = "masters.Payroll.EmployeeShiftMapping.mstfrm";

    //validation messages
    protected   $messages = [
                    'CTCODE.required' => 'Required field',
                    'CTCODE.unique' => 'Duplicate Code'
                ];
    
    
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

        $FormId   = $this->form_id;
        $objRights = DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();

        $objDataList    =   DB::table('TBL_MST_EMPLOYEE_SHIFT_MAP')
        ->get();
        
       return view($this->view.$FormId,compact(['objRights','objDataList','FormId'])); 
        
    }

    
    public function add(){

        $FormId   = $this->form_id;
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');

        $objshift    =   $this->shift();

        $objSON = DB::table('TBL_MST_DOCNO_DEFINITION')
            ->where('VTID_REF','=',$this->vtid_ref)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_DOCNO_DEFINITION.*')
            ->first();
            
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
        
        return view($this->view.$FormId.'add',compact(['objSON','objAutoGenNo','objshift','FormId']));
       
   }

   public function codeduplicate(Request $request){

        $FormId   = $this->form_id;
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $DOC_NO =   strtoupper($request['DOC_NO']);
        
        $objLabel = DB::table('TBL_MST_EMPLOYEE_SHIFT_MAP')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('DOC_NO','=',$DOC_NO)
        ->select('DOC_NO')
        ->first();
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }

    
   public function save(Request $request){
    
        $data = array();
        if(isset($_REQUEST['EMPID_REF']) && !empty($_REQUEST['EMPID_REF'])){
            foreach($_REQUEST['EMPID_REF'] as $key=>$val){

                $data[] = array(
                'EMPID_REF'     => trim($_REQUEST['EMPID_REF'][$key]),
                'SHIFTID_REF'       => trim($_REQUEST['SHIFTID_REF'][$key]),
                );
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

        $DOC_CODE         = $request['DOC_NO'];
        $DOC_DATE         = $request['DOC_DT']; 
        $DEPID_REF        = $request['DEPTID_REF']; 
        
        $DEACTIVATED =  ($request['DE_ACTIVATED'] == "on"  ? 1 : 0) ;
        $DODEACTIVATED =  (isset($request->DO_DEACTIVATED)? $request->DO_DEACTIVATED : NULL) ;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $VTID        =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "ADD";
        $IPADDRESS  =   $request->getClientIp();
        
        $condition_data = [
            $DOC_CODE, $DOC_DATE, $DEPID_REF, $XMLMAT, 
            $CYID_REF, $BRID_REF,$FYID_REF,$VTID, $USERID,$UPDATE, $UPTIME, $ACTION, $IPADDRESS
        ];
           
        try {

            //save data
            $sp_result = DB::select('EXEC SP_EMPLOYEE_SHIFT_MAP_IN ?,?,?,?,  ?,?,?,?,?,?,?,?,?', $condition_data);
      
        } catch (\Throwable $th) {
        
            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
    
        }
     
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
           
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','country'=>'duplicate']);
            
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

        if(!is_null($id))
        {
            $FormId   = $this->form_id;
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $Status = "A";

            $objshift    =   $this->shift();

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $objCondition = DB::table('TBL_MST_EMPLOYEE_SHIFT_MAP')
            ->leftJoin('TBL_MST_DEPARTMENT', 'TBL_MST_EMPLOYEE_SHIFT_MAP.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID')
            ->where('TBL_MST_EMPLOYEE_SHIFT_MAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_EMPLOYEE_SHIFT_MAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_EMPLOYEE_SHIFT_MAP.EMP_SHIFTMAPID','=',$id)
            ->select('TBL_MST_EMPLOYEE_SHIFT_MAP.*','TBL_MST_DEPARTMENT.DEPID','TBL_MST_DEPARTMENT.DCODE')
            ->first();

            $objConditiontemp = DB::table('TBL_MST_EMPLOYEE_SHIFT_MAP_DETAIL')
            ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_MST_EMPLOYEE_SHIFT_MAP_DETAIL.EMPID_REF','=','TBL_MST_EMPLOYEE.EMPID')
            ->leftJoin('TBL_MST_SHIFT', 'TBL_MST_EMPLOYEE_SHIFT_MAP_DETAIL.SHIFTID_REF','=','TBL_MST_SHIFT.SHIFTID')
            ->where('TBL_MST_EMPLOYEE_SHIFT_MAP_DETAIL.EMP_SHIFTMAPID_REF','=',$id)
            ->select('TBL_MST_EMPLOYEE_SHIFT_MAP_DETAIL.*','TBL_MST_EMPLOYEE.EMPID','TBL_MST_EMPLOYEE.EMPCODE','TBL_MST_SHIFT.SHIFTID','TBL_MST_SHIFT.SHIFT_NAME')
            ->get();

            return view($this->view.$FormId.$type,compact(['objCondition','objshift','objConditiontemp','objRights','FormId','ActionStatus']));
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
        $msgTxt         =   $type =='update'?'updated':'approved';

        $data = array();
        if(isset($_REQUEST['EMPID_REF']) && !empty($_REQUEST['EMPID_REF'])){
            foreach($_REQUEST['EMPID_REF'] as $key=>$val){

                $data[] = array(
                'EMPID_REF'     => trim($_REQUEST['EMPID_REF'][$key]),
                'SHIFTID_REF'       => trim($_REQUEST['SHIFTID_REF'][$key]),
                );
            }
        }
       

        if(!empty($data)){
            $wrapped_links["MAT"] = $data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $XMLMAT = NULL; 
        }

        $DOC_CODE         = $request['DOC_NO'];
        $DOC_DATE         = $request['DOC_DT']; 
        $DEPID_REF        = $request['DEPTID_REF']; 

        $DEACTIVATED =  ($request['DE_ACTIVATED'] == "on"  ? 1 : 0) ;
        $DODEACTIVATED =  (isset($request->DO_DEACTIVATED)? $request->DO_DEACTIVATED : NULL) ;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID        =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   $Approvallevel;
        $IPADDRESS  =   $request->getClientIp();
            
        $condition_data = [
            $DOC_CODE, $DOC_DATE, $DEPID_REF, $XMLMAT, 
            $CYID_REF, $BRID_REF,$FYID_REF,$VTID, $USERID,$UPDATE, $UPTIME, $ACTION, $IPADDRESS
        ];  
             
        $sp_result = DB::select('EXEC SP_EMPLOYEE_SHIFT_MAP_UP ?,?,?,?,  ?,?,?,?,?,?,?,?,?', $condition_data);
            
        
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully '.$msgTxt]);

        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
        }

    }
  
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            $FormId   = $this->form_id;
            $objCondition = DB::table('TBL_MST_EMPLOYEE_SHIFT_MAP')
            ->where('TBL_MST_EMPLOYEE_SHIFT_MAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_EMPLOYEE_SHIFT_MAP.BRID_REF','=',Session::get('BRID_REF'))
            //->where('TBL_MST_EMPLOYEE_SHIFT_MAP.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_MST_EMPLOYEE_SHIFT_MAP.EMP_SHIFTMAPID','=',$id)
            ->select('TBL_MST_EMPLOYEE_SHIFT_MAP.*')
            ->first();
            // dd($objCalculation);
            //select * from TBL_MST_VOUCHERTYPE where VTID=114
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

                 // dump( $objAttachments);

                 return view($this->view.$FormId.'attachment',compact(['objCondition','objMstVoucherType','objAttachments','FormId']));
        }

    }
    
    

    
    //uploads attachments files
    public function docuploads(Request $request){

        $FormId   = $this->form_id;
        $formData = $request->all();

        $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size = config("erpconst.attachments.max_size") * 1020 * 1024;

        //get data
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

    public function cancel(Request $request){

        $id = $request->{0};

        $objResponse = DB::table('TBL_MST_EMPLOYEE_SHIFT_MAP')->where('EMP_SHIFTMAPID','=',$id)->select('*')->first();
        $FYID_REF = $objResponse->FYID_REF;

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   $FYID_REF;       
        $TABLE      =   "TBL_MST_EMPLOYEE_SHIFT_MAP";
        $FIELD      =   "EMP_SHIFTMAPID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_MST_EMPLOYEE_SHIFT_MAP_DETAIL',
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


    function shift(){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        
        $shifts = DB::table('TBL_MST_SHIFT')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->get();
        return $shifts;
        }
    

                    public function getDeptDetails(Request $request){   
                
                        $objDept    =   DB::table('TBL_MST_EMPLOYEE')
                        ->leftJoin('TBL_MST_DEPARTMENT', 'TBL_MST_EMPLOYEE.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID')
                        //->orderBy('EMPID', 'DESC')
                        ->get();

                        if(isset($objDept) && !empty($objDept)){
        
                            foreach ($objDept as $index=>$dataRow){

                                $DEPID      =   isset($dataRow->DEPID)?$dataRow->DEPID:NULL;
                                $DCODE      =   isset($dataRow->DCODE)?$dataRow->DCODE:NULL;
                                $NAME       =   isset($dataRow->NAME)?$dataRow->NAME:NULL;

                                $EMPID      =   isset($dataRow->EMPID)?$dataRow->EMPID:NULL;
                                $EMPCODE    =   isset($dataRow->EMPCODE)?$dataRow->EMPCODE:NULL;

                                
                                echo'
                                <tr id="glidcode_'.$DEPID.'" class="clsglid" >
                                <td style="width:5%;text-align:center;" ><input type="checkbox" id="chkIdDeptCode'.$DEPID.'"  value="'.$DEPID.'"> </td>
                                <td style="width:10%;">'.$DCODE.'</td>
                                <input type="hidden" id="txtglidcode_'.$DEPID.'" data-deptcode="'.$EMPCODE.'" value="'.$DEPID.'"/>
                                <td hidden id="empid_'.$EMPID.'"><input type="hidden" id="txtempid_'.$EMPID.'" data-empcode="'.$EMPCODE.'" value="'.$EMPCODE.'"/></td>
                                <td style="width:10%;">'.$NAME.'</td>
                                </tr>';
                                } 
                            }           
                            else{
                                echo '<tr><td colspan="12"> Record not found.</td></tr>';
                            }
                            exit();
                        }


    public function getMaterial(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $id         =   $request['id'];
        $ObjData    =   DB::table('TBL_MST_EMPLOYEE')
                        ->join('TBL_MST_DEPARTMENT', 'TBL_MST_EMPLOYEE.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID')
                        ->where('TBL_MST_EMPLOYEE.CYID_REF','=',$CYID_REF)
                        ->where('TBL_MST_EMPLOYEE.BRID_REF','=',$BRID_REF)
                        ->where('TBL_MST_EMPLOYEE.STATUS','=','A')
                        ->where('TBL_MST_DEPARTMENT.DEPID','=',$id)
                        ->get();


        if(isset($ObjData) && !empty($ObjData)){

            foreach ($ObjData as $index=>$dataRow){

                $shiftData    = DB::table('TBL_MST_SHIFT')
                                ->where('CYID_REF','=',$CYID_REF)
                                ->where('BRID_REF','=',$BRID_REF)
                                ->where('STATUS','=','A')
                                ->get();

                $EMPID      =   isset($dataRow->EMPID)?$dataRow->EMPID:NULL;
                $EMPCODE    =   isset($dataRow->EMPCODE)?$dataRow->EMPCODE:NULL;
                
                echo'<tr class="participantRow">';

                echo'<td><input type="text" name="popupITEMID_'.$index.'" id="popupITEMID_'.$index.'" class="form-control"  autocomplete="off" value="'.$EMPCODE.'"  readonly></td>';
                echo'<td hidden><input type="hidden" name="EMPID_REF[]" id="EMPID_REF_'.$index.'" class="form-control"  autocomplete="off" value="'.$EMPID.'" /></td>';

                echo'<td>';
                echo'<select class="form-control" name="SHIFTID_REF[]" id="SHIFTID_REF_'.$index.'" >';
                foreach ($shiftData as $key=>$dataRow1){
                echo'<option value="'.$dataRow1->SHIFTID.'">'.$dataRow1->SHIFT_NAME.'</option>';
                }
                echo'</select>';
                echo'</td>';
                
                }

            }
            else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }

        exit();
        
    }





}
