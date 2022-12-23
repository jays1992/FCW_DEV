<?php

namespace App\Http\Controllers\Transactions;

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
use Carbon\Carbon;

class TrnFrm412Controller extends Controller
{
   
    protected $form_id = 412;
    protected $vtid_ref   = 487;  //voucher type id
    protected $view = "transactions.Payroll.ClaimForm.trnfrm";

       
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
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first(); 

        $FormId  = $this->form_id;
        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');     

        $objDataList = DB::table('TBL_TRN_CLAIM_HDR')
       ->select('TBL_TRN_CLAIM_HDR.*')
       ->orderBy('TBL_TRN_CLAIM_HDR.CLAIMID', 'DESC')
       ->get();

        return view($this->view.$FormId,compact(['objRights','FormId','objDataList']));

    }

    public function add(){ 

        $FormId  = $this->form_id;
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $DataDepat      = $this->getDepartName();
        $objDataList    = $this->getEmplyName();
        $objAttnceList    = $this->getAttenName();
        $objLeaveList     =  $this->getLeaveName();
        $objgeneralledger   =   $this->getgl();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_CLAIM_HDR',
            'HDR_ID'=>'CLAIMID',
            'HDR_DOC_NO'=>'CLAIM_DOC_NO',
            'HDR_DOC_DT'=>'CLAIM_DOC_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

          
        
        return view($this->view.$FormId.'add',compact(['FormId','DataDepat','objDataList','objgeneralledger','objAttnceList','objLeaveList','doc_req','docarray']));
    }
  

   public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $ATTCODE =   $request['ATTCODE'];
        
        $objLabel = DB::table('TBL_MST_ATTRIBUTE')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('ATTCODE','=',$ATTCODE)
        ->select('ATTCODE')
        ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }

   public function save(Request $request){

    //$formData = $request->all();
    
    //dd($formData);
    $CYID_REF = Auth::user()->CYID_REF;
    $VTID       =   $this->vtid_ref;
    $USERID     =   Auth::user()->USERID;
    $BRID_REF = Session::get('BRID_REF');
    $FYID_REF = Session::get('FYID_REF');

    $allow_extnesions   =   explode(",",config("erpconst.attachments.allow_extensions"));
    $allow_size         =   config("erpconst.attachments.max_size") * 1020 * 1024;
    $ATTACH_DOCNO       =   ""; 
    $ATTACH_DOCDT       =   ""; 
    
    $image_path         =   "docs/company".$CYID_REF."/CompanyMaster/Logo";     
    $destinationPath    =   str_replace('\\', '/', public_path($image_path));
    if ( !is_dir($destinationPath) ) {
        mkdir($destinationPath, 0777, true);
    }
    //if(isset($request["FILENAME"]) && !empty($destinationPath)){

    //foreach($request["FILENAME"] as $index=>$row_val){
  
        if(isset($request["FILENAME"])){

        $uploadedFile = $request["FILENAME"]; 
    
        $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
        $filesize               =   $uploadedFile ->getSize();  
        $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );
        $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".$filenamewithextension;  

        if ($uploadedFile->isValid()) {

            if(in_array($extension,$allow_extnesions)){
                
                if($filesize < $allow_size){

                    $filename = $destinationPath."/".$filenametostore;
                    $uploadedFile->move($destinationPath, $filenametostore);  //upload in dir if not exists
                    $uploaded_data[$index]["FILENAME"] =$filenametostore;
                    $uploaded_data[$index]["LOCATION"] = $destinationPath."/";
                   
                }else{
                    
                    $invlid_files = $invlid_files.$filenamewithextension." (invalid size)  "; 
                } //invalid size
                
            }else{

                $invlid_files = $invlid_files.$filenamewithextension." (invalid extension)  ";                             
            }// invalid extension
        
        }else{
                
            $invlid_files = $invlid_files.$filenamewithextension." (invalid)"; 
            }//invalid
        //}
        $FILENAME = $request["FILENAME"];
        $DOCPATH = $destinationPath;
    }else{
        $FILENAME = NULL;
        $DOCPATH = NULL;

    }
    //$FILENAME = $request["FILENAME"];
    //$DOCPATH = $destinationPath;
    //}else{
        //$FILENAME = NULL;
        //$DOCPATH = NULL;

    $Row_Count = $request['Row_Count'];
 
    for ($i=0; $i<=$Row_Count; $i++)
    {
        if(isset($request['GLID_REF_'.$i]))
        {

            $data[$i] = [
                
                'GLID_REF' => $request['GLID_REF_'.$i],
                'REMARKS'  => $request['REMARKS_'.$i],
                'CLAIM_AMT'  => $request['CLAIM_AMT_'.$i],
                'SANCTION_AMT'    => $request['SANCTION_AMT_'.$i],
                'ANY_ATTACHMENT' => $request['ANY_ATTACHMENT_'.$i],
                'FILE_NAMES'      => $FILENAME,
                'FILE_LAOCATION'  => $DOCPATH,
            ];
        }
    }

    //dd($data);  
    
        if(!empty($data)){
            $wrapped_links["MAT"] = $data; 
            $MAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $MAT = NULL; 
        }

        $CLAIM_DOC_NO      =   trim($request['CLAIM_DOC_NO']);
        $CLAIM_DOC_DT      =   trim($request['CLAIM_DOC_DT']);
        $EMPID_REF      =   trim($request['EMPID_REF']);
        $PERIOD_FR_DT      =   trim($request['PERIOD_FR_DT']);
        $PERIOD_TO_DT      =   trim($request['PERIOD_TO_DT']);
        $DEPID_REF      =   trim($request['DEPID_REF']);
        $DESGID_REF      =   trim($request['DEPID_REF']);

        $VTID_REF           =   $this->vtid_ref;
        $USERID_REF         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();
        
        $array_data   = [
                    $CLAIM_DOC_NO,   $CLAIM_DOC_DT, $EMPID_REF, $PERIOD_FR_DT,  $PERIOD_TO_DT,  $DEPID_REF,     $DESGID_REF,
                    $CYID_REF,       $BRID_REF,     $FYID_REF,  $VTID_REF,      $MAT,           $USERID_REF,    $UPDATE,
                    $UPTIME,    $ACTION,    $IPADDRESS          
                    ];

            //dd($array_data);
           
        $sp_result = DB::select('EXEC SP_CLAIM_IN ?,?,?,?,?,?,?, ?,?,?,?,?,?,?, ?,?,?', $array_data);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
    //}
        
        exit();    
    }

    public function edit($id){

        if(!is_null($id))
        {
        
            $FormId  = $this->form_id;
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objResponse = DB::table('TBL_TRN_CLAIM_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('CLAIMID','=',$id)
            ->select('*')
            ->first();         
            if(strtoupper($objResponse->STATUS)=="A" || strtoupper($objResponse->STATUS)=="C"){
                exit("Sorry, Only Un Approved record can edit.");
            }

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first(); 

            $objDataResponse = DB::table('TBL_TRN_CLAIM_MAT')                    
            ->where('TBL_TRN_CLAIM_MAT.CLAIMID_REF','=',$id)
            ->leftJoin('TBL_TRN_CLAIM_HDR', 'TBL_TRN_CLAIM_MAT.CLAIMID_REF','=','TBL_TRN_CLAIM_HDR.CLAIMID')
            ->leftJoin('TBL_MST_GENERALLEDGER', 'TBL_TRN_CLAIM_MAT.GLID_REF','=','TBL_MST_GENERALLEDGER.GLID')
            ->select('TBL_TRN_CLAIM_MAT.*','TBL_TRN_CLAIM_HDR.*','TBL_MST_GENERALLEDGER.*')
            ->get()->toArray();
                             
            $objCount = count($objDataResponse);

            $objLvDesList = DB::table('TBL_TRN_CLAIM_HDR')
            ->where('TBL_TRN_CLAIM_HDR.CLAIMID','=',$id)
             ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_TRN_CLAIM_HDR.EMPID_REF','=','TBL_MST_EMPLOYEE.EMPID')   
            ->select('TBL_TRN_CLAIM_HDR.*','TBL_MST_EMPLOYEE.*')
            ->first();

            $objDptList = DB::table('TBL_TRN_CLAIM_HDR')
            ->where('TBL_TRN_CLAIM_HDR.CLAIMID','=',$id)
             ->leftJoin('TBL_MST_DEPARTMENT', 'TBL_TRN_CLAIM_HDR.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID')   
            ->select('TBL_TRN_CLAIM_HDR.*','TBL_MST_DEPARTMENT.*')
            ->first();

            $DataDepat      = $this->getDepartName();
            $objDataList    = $this->getEmplyName();
            $objAttnceList    = $this->getAttenName();
            $objLeaveList     =  $this->getLeaveName();
            $objgeneralledger   =   $this->getgl();
    

            return view($this->view.$FormId.'edit',compact(['objResponse','FormId','objDataList','DataDepat','objgeneralledger','objAttnceList','objLeaveList','objDptList','objLvDesList','user_approval_level','objRights','objDataResponse','objCount']));

        }

    }

     
    public function update(Request $request)
    {

    //dd($request->all());

    $CYID_REF = Auth::user()->CYID_REF;
    $VTID       =   $this->vtid_ref;
    $USERID     =   Auth::user()->USERID;
    $BRID_REF = Session::get('BRID_REF');
    $FYID_REF = Session::get('FYID_REF');

    $allow_extnesions   =   explode(",",config("erpconst.attachments.allow_extensions"));
    $allow_size         =   config("erpconst.attachments.max_size") * 1020 * 1024;
    $ATTACH_DOCNO       =   ""; 
    $ATTACH_DOCDT       =   ""; 

    //$destinationPath    =   storage_path()."/docs/company".$CYID_REF."/CompanyMaster/Logo";
    $image_path         =   "docs/company".$CYID_REF."/CompanyMaster/Logo";     
    $destinationPath    =   str_replace('\\', '/', public_path($image_path));
    
    if ( !is_dir($destinationPath) ) {
        mkdir($destinationPath, 0777, true);
    }

    foreach($request["FILENAME"] as $index=>$row_val){
  
    if(isset($request["FILENAME"][$index])){

        $uploadedFile = $request["FILENAME"][$index]; 
    
        $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
        $filesize               =   $uploadedFile ->getSize();  
        $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );
        $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".$filenamewithextension;  

        if ($uploadedFile->isValid()) {

            if(in_array($extension,$allow_extnesions)){
                
                if($filesize < $allow_size){

                    $filename = $destinationPath."/".$filenametostore;
                   
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
}

    $FILENAME = $filename;
    $FILELAOCATION = $destinationPath;

    $Row_Count = $request['Row_Count'];
 
    for ($i=0; $i<=$Row_Count; $i++)
    {
        if(isset($request['GLID_REF_'.$i]))
        {

            $data[$i] = [
                
                'GLID_REF' => $request['GLID_REF_'.$i],
                'REMARKS'  => $request['REMARKS_'.$i],
                'CLAIM_AMT'  => $request['CLAIM_AMT_'.$i],
                'SANCTION_AMT'    => $request['SANCTION_AMT_'.$i],
                'ANY_ATTACHMENT' => $request['ANY_ATTACHMENT_'.$i],
                'FILE_NAMES'      => $FILENAME,
                'FILE_LAOCATION'  => $FILELAOCATION,
            ];
        }
    }

    //dd($data);  
    
        if(!empty($data)){
            $wrapped_links["MAT"] = $data; 
            $MAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $MAT = NULL; 
        }

        //dd($MAT);


        $CLAIM_DOC_NO      =   trim($request['CLAIM_DOC_NO']);
        $CLAIM_DOC_DT      =   trim($request['CLAIM_DOC_DT']);
        $EMPID_REF      =   trim($request['EMPID_REF']);
        $PERIOD_FR_DT      =   trim($request['PERIOD_FR_DT']);
        $PERIOD_TO_DT      =   trim($request['PERIOD_TO_DT']);
        $DEPID_REF      =   trim($request['DEPID_REF']);
        $DESGID_REF      =   trim($request['DEPID_REF']);

        $VTID_REF           =   $this->vtid_ref;
        $USERID_REF         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "EDIT";
        $IPADDRESS      =   $request->getClientIp();
        
        $array_data   = [
                    $CLAIM_DOC_NO,   $CLAIM_DOC_DT, $EMPID_REF, $PERIOD_FR_DT,  $PERIOD_TO_DT,  $DEPID_REF,     $DESGID_REF,
                    $CYID_REF,       $BRID_REF,     $FYID_REF,  $VTID_REF,      $MAT,           $USERID_REF,    $UPDATE,
                    $UPTIME,    $ACTION,    $IPADDRESS          
                    ];
           
        $sp_result = DB::select('EXEC SP_CLAIM_UP ?,?,?,?,?,?,?, ?,?,?,?,?,?,?, ?,?,?', $array_data);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }

        exit();            
    } 

    //uploads attachments files
    public function docuploads(Request $request){

        $formData = $request->all();

        $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size = config("erpconst.attachments.max_size") * 1020 * 1024;

        //echo '<br> c='."--".Config("erpconst.attachments.max_size");
        
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
 
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/AssignLeave";

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

                    if ($uploadedFile->isValid()) {

                        if(in_array($extension,$allow_extnesions)){
                            
                            if($filesize < $allow_size){

                                $filename = $destinationPath."/".$filenametostore;

                                if (!file_exists($filename)) {

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
            return redirect()->route("transaction",[412,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
        }
     //  dd($uploaded_data);

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
       
             //save data
             $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);
     
        if($sp_result[0]->RESULT=="SUCCESS"){

            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }

            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }

            return redirect()->route("transaction",[412,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("transaction",[412,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("transaction",[412,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
            return redirect()->route("transaction",[412,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
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

        if(!empty($sp_listing_result))
            {
                foreach ($sp_listing_result as $key=>$salesenquiryitem)
            {  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$salesenquiryitem->LAVELS;
            }
            }
           
            $CYID_REF = Auth::user()->CYID_REF;
        $VTID       =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $allow_extnesions   =   explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size         =   config("erpconst.attachments.max_size") * 1020 * 1024;
        $ATTACH_DOCNO       =   ""; 
        $ATTACH_DOCDT       =   ""; 

        //$destinationPath    =   storage_path()."/docs/company".$CYID_REF."/CompanyMaster/Logo";
        $image_path         =   "docs/company".$CYID_REF."/CompanyMaster/Logo";     
        $destinationPath    =   str_replace('\\', '/', public_path($image_path));

        if ( !is_dir($destinationPath) ) {
            mkdir($destinationPath, 0777, true);
        }

        foreach($request["FILENAME"] as $index=>$row_val){

        if(isset($request["FILENAME"][$index])){

            $uploadedFile = $request["FILENAME"][$index]; 

            $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
            $filesize               =   $uploadedFile ->getSize();  
            $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );
            $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".$filenamewithextension;  

            if ($uploadedFile->isValid()) {

                if(in_array($extension,$allow_extnesions)){
                    
                    if($filesize < $allow_size){

                        $filename = $destinationPath."/".$filenametostore;
                        
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
        }

        $FILENAME = $filename;
        $FILELAOCATION = $destinationPath;

        $Row_Count = $request['Row_Count'];

        for ($i=0; $i<=$Row_Count; $i++)
        {
            if(isset($request['GLID_REF_'.$i]))
            {

                $data[$i] = [
                    
                    'GLID_REF' => $request['GLID_REF_'.$i],
                    'REMARKS'  => $request['REMARKS_'.$i],
                    'CLAIM_AMT'  => $request['CLAIM_AMT_'.$i],
                    'SANCTION_AMT'    => $request['SANCTION_AMT_'.$i],
                    'ANY_ATTACHMENT' => $request['ANY_ATTACHMENT_'.$i],
                    'FILE_NAMES'      => $FILENAME,
                    'FILE_LAOCATION'  => $FILELAOCATION,
                ];
            }
        }

        //dd($data);  

            if(!empty($data)){
                $wrapped_links["MAT"] = $data; 
                $MAT = ArrayToXml::convert($wrapped_links);
            }
            else{
                $MAT = NULL; 
            }

            //dd($MAT);


            $CLAIM_DOC_NO      =   trim($request['CLAIM_DOC_NO']);
            $CLAIM_DOC_DT      =   trim($request['CLAIM_DOC_DT']);
            $EMPID_REF      =   trim($request['EMPID_REF']);
            $PERIOD_FR_DT      =   trim($request['PERIOD_FR_DT']);
            $PERIOD_TO_DT      =   trim($request['PERIOD_TO_DT']);
            $DEPID_REF      =   trim($request['DEPID_REF']);
            $DESGID_REF      =   trim($request['DEPID_REF']);
            
            $VTID_REF           =   $this->vtid_ref;
            $USERID_REF         =   Auth::user()->USERID;
            $UPDATE         =   Date('Y-m-d');
            $UPTIME         =   Date('h:i:s.u');
            $ACTION         =   $Approvallevel;
            $IPADDRESS      =   $request->getClientIp();
            
            $array_data   = [
                        $CLAIM_DOC_NO,   $CLAIM_DOC_DT, $EMPID_REF, $PERIOD_FR_DT,  $PERIOD_TO_DT,  $DEPID_REF,     $DESGID_REF,
                        $CYID_REF,       $BRID_REF,     $FYID_REF,  $VTID_REF,      $MAT,           $USERID_REF,    $UPDATE,
                        $UPTIME,    $ACTION,    $IPADDRESS          
                        ];
                
            $sp_result = DB::select('EXEC SP_CLAIM_UP ?,?,?,?,?,?,?, ?,?,?,?,?,?,?, ?,?,?', $array_data);

            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');

            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }              

        exit();     
    }

    public function view($id){

        if(!is_null($id))
        {
            $FormId  = $this->form_id;
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objResponse = DB::table('TBL_TRN_CLAIM_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('CLAIMID','=',$id)
            ->select('*')
            ->first();         
            if(strtoupper($objResponse->STATUS)=="A" || strtoupper($objResponse->STATUS)=="C"){
                exit("Sorry, Only Un Approved record can edit.");
            }

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first(); 

            $objDataResponse = DB::table('TBL_TRN_CLAIM_MAT')                    
            ->where('TBL_TRN_CLAIM_MAT.CLAIMID_REF','=',$id)
            ->leftJoin('TBL_TRN_CLAIM_HDR', 'TBL_TRN_CLAIM_MAT.CLAIMID_REF','=','TBL_TRN_CLAIM_HDR.CLAIMID')
            ->leftJoin('TBL_MST_GENERALLEDGER', 'TBL_TRN_CLAIM_MAT.GLID_REF','=','TBL_MST_GENERALLEDGER.GLID')
            ->select('TBL_TRN_CLAIM_MAT.*','TBL_TRN_CLAIM_HDR.*','TBL_MST_GENERALLEDGER.*')
            ->get()->toArray();
                             
            $objCount = count($objDataResponse);

            $objLvDesList = DB::table('TBL_TRN_CLAIM_HDR')
            ->where('TBL_TRN_CLAIM_HDR.CLAIMID','=',$id)
             ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_TRN_CLAIM_HDR.EMPID_REF','=','TBL_MST_EMPLOYEE.EMPID')   
            ->select('TBL_TRN_CLAIM_HDR.*','TBL_MST_EMPLOYEE.*')
            ->first();

            $objDptList = DB::table('TBL_TRN_CLAIM_HDR')
            ->where('TBL_TRN_CLAIM_HDR.CLAIMID','=',$id)
             ->leftJoin('TBL_MST_DEPARTMENT', 'TBL_TRN_CLAIM_HDR.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID')   
            ->select('TBL_TRN_CLAIM_HDR.*','TBL_MST_DEPARTMENT.*')
            ->first();

            $DataDepat      = $this->getDepartName();
            $objDataList    = $this->getEmplyName();
            $objAttnceList    = $this->getAttenName();
            $objLeaveList     =  $this->getLeaveName();
            $objgeneralledger   =   $this->getgl();
    

            return view($this->view.$FormId.'view',compact(['objResponse','FormId','objDataList','DataDepat','objgeneralledger','objAttnceList','objLeaveList','objDptList','objLvDesList','user_approval_level','objRights','objDataResponse','objCount']));

        }

    }
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }

        $objResponse = TblMstFrm412::whereIn('ATTID',$ids_data)->get();
        
        return view('transactions.Payroll.AssignLeave.trnfrm412print',compact(['objResponse']));
   }//print

    
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objResponse = DB::table('TBL_TRN_CLAIM_HDR')->where('CLAIMID','=',$id)->select('*')->first();

            //select * from TBL_MST_VOUCHERTYPE where VTID=114

            $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
                    ->where('VTID','=',$this->vtid_ref)
                     ->select('VTID','VCODE','DESCRIPTIONS')
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
                 $FormId  = $this->form_id;
                 return view($this->view.$FormId.'attachment',compact(['objResponse','FormId','objMstVoucherType','objAttachments']));
        }

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
                foreach ($sp_listing_result as $key=>$valueitem)
            {  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$valueitem->LAVELS;
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
            $TABLE      =   "TBL_MST_ATTRIBUTE";
            $FIELD      =   "ATTID";
            $ACTIONNAME     = $Approvallevel;
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
        
        return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','exist'=>'norecord']);
        
        }else{
        return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','exist'=>'Some Error']);
        }
        
        exit();    
        }


        //Cancel the data
        public function cancel(Request $request){

            $id = $request->{0};
   
           $USERID =   Auth::user()->USERID;
            $VTID   =   $this->vtid_ref;  //voucher type id
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');
            $FYID_REF   =   Session::get('FYID_REF');  
            $TABLE      =   "TBL_TRN_CLAIM_HDR";
            $FIELD      =   "CLAIMID";
            $ID         =   $id;
            $UPDATE     =   Date('Y-m-d');
            $UPTIME     =   Date('h:i:s.u');
            $IPADDRESS  =   $request->getClientIp();
    
            $req_data[0]=[
                'NT'  => 'TBL_TRN_CLAIM_MAT',
            ];
        
            $wrapped_links["TABLES"] = $req_data; 
            
            $XMLTAB = ArrayToXml::convert($wrapped_links);
            
            $mst_cancel_data = [ $USERID, $VTID, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$XMLTAB];
            $sp_result = DB::select('EXEC SP_TRN_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);
   
            if($sp_result[0]->RESULT=="CANCELED"){  
              
              return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
            
            }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
            
                
                return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
                
            }else{
               
                   return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'invalid'=>'invalid']);
            }
            
            exit(); 
       }

       public function getDepartName(){
        $objData    =   DB::table('TBL_MST_DEPARTMENT')
        ->where('STATUS','=','A')
        ->get();
        return $objData;
       }
       public function getEmplyName(){
        $DataList    =   DB::table('TBL_MST_EMPLOYEE')
        ->where('STATUS','=','A')
        ->get();
        return $DataList;
       }
       public function getAttenName(){
       $objAtt    =   DB::table('TBL_MST_ATTENDANCE_STATUS')
            ->where('TBL_MST_ATTENDANCE_CODE.CYID_REF','=',Auth::user()->CYID_REF)
            ->leftJoin('TBL_MST_ATTENDANCE_CODE', 'TBL_MST_ATTENDANCE_CODE.ATTENDANCE_CODEID','=','TBL_MST_ATTENDANCE_STATUS.ATTENDANCE_NAME')
            ->select('TBL_MST_ATTENDANCE_STATUS.*', 'TBL_MST_ATTENDANCE_CODE.ATTENDANCE_CODE_DESC')
            ->get();
            return $objAtt;
        }
        public function getLeaveName(){
        $objLeave    =   DB::table('TBL_MST_LEAVE_TYPE')
        ->where('STATUS','=','A')
        ->get();
        return $objLeave;
    }

    public function getEmpName(Request $request){        
        $EMPID          =   $request['EMPID'];
		$objEmpName = DB::table('TBL_MST_EMPLOYEE')
        ->where('EMPID','=', $EMPID )
        ->select('FNAME')
        ->first();		
		if(!empty($objEmpName)){
			echo $objEmpName->FNAME;
		}
		else{
			echo "";
		}
        exit();
    }
    
    public function getGlCodeName(Request $request){        
        $GLID          =   $request['GLID'];		
		$objGlName = DB::table('TBL_MST_GENERALLEDGER')
        ->where('GLID','=', $GLID )
        ->select('GLNAME')
        ->first();		
		if(!empty($objGlName)){
			echo $objGlName->GLNAME;
		}
		else{
			echo "";
		}
        exit();
    }

    public function getDepartDestion(Request $request){        
        $DEPID          =   $request['DEPID'];		
		$objDptmtDest = DB::table('TBL_MST_DEPARTMENT')
        ->where('DEPID','=', $DEPID )
        ->select('NAME')
        ->first();		
		if(!empty($objDptmtDest)){
			echo $objDptmtDest->NAME;
		}
		else{
			echo "";
		}
        exit();
    }


    public function getgl(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        return  DB::select('SELECT GLID, GLCODE, GLNAME, ALIAS FROM TBL_MST_GENERALLEDGER  
                    WHERE STATUS= ? AND SUBLEDGER = ? AND CYID_REF = ? order by GLCODE ASC', 
                    [$Status,'0',$CYID_REF]);
        }

    
   


}
