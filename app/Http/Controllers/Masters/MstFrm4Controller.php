<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm4;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;


class MstFrm4Controller extends Controller
{
   
    protected $form_id = 4;
    protected $vtid_ref   = 114;  //voucher type id

    //validation messages
    protected   $messages = [
                    'CTRYCODE.required' => 'Required field',
                    'CTRYCODE.unique' => 'Duplicate Code',
                    'CTRYCODE.min' => 'min 3 character',
                    'COUNTRY_NAME.required' => 'Name is required'
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
        $objRights = DB::table('TBL_MST_USERROLMAP')
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');    

        $objDataList=DB::select("SELECT * FROM TBL_MST_COUNTRY ORDER BY NAME ");

       return view('masters.country.mstfrm4',compact(['objRights','objDataList']));
        
    }

    //uploads attachments files
    public function docuploads(Request $request){

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
        
        $destinationPath = "E:/company".$CYID_REF."/countrymst";

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
            return redirect()->route("master",[4,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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

            return redirect()->route("master",[4,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[4,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            return redirect()->route("master",[4,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   


    public function add(){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $objDD = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('STATUS','=',$Status)
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
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

      return view('masters.country.mstfrm4add',compact(['objDD','objDOCNO']));
       
   }

   public function codeduplicate(Request $request){

        
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $CTRYCODE =   $request['CTRYCODE'];
        
        $objLabel = DB::table('TBL_MST_COUNTRY')
        ->where('CTRYCODE','=',$CTRYCODE)
        ->select('CTRYCODE')
        ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }

    
   public function save(Request $request){

        //validation rules
    $rules = [
            'CTRYCODE' => 'required|min:3',
            'COUNTRY_NAME' => 'required',          
        ];


        $req_data = [

            'CTRYCODE'     =>   $request['CTRYCODE'],
            'COUNTRY_NAME' =>   $request['COUNTRY_NAME']
        ]; 


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
           return Response::json(['errors' => $validator->errors()]);	
        }
     
        //get data
        $CTRYCODE   =   strtoupper(trim($request['CTRYCODE']) );
        $COUNTRY_NAME =   trim($request['COUNTRY_NAME']);  
        $ISD        =   (isset($request['ISDCODE']) && trim($request['ISDCODE']) )? trim($request['ISDCODE']) : NULL ;
        $LANG       =   (isset($request['LANG']) && trim($request['LANG']) )? trim($request['LANG']) : NULL ;

        $CONTI      =  (isset($request['CONTINENTAL']) && trim($request['CONTINENTAL']) )? trim($request['CONTINENTAL']) : NULL ;
        $CAPITAL    =   (isset($request['CAPITAL']) && trim($request['CAPITAL']) )? trim($request['CAPITAL']) : NULL ; 
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');

        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID        =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "ADD";
        $IPADDRESS  =   $request->getClientIp();
        
        $coutry_data = [
                        $CTRYCODE, $COUNTRY_NAME, $ISD, $LANG,
                        $CONTI, $CAPITAL, $CYID_REF, $BRID_REF,
                        $FYID_REF, $VTID, $USERID, $UPDATE,
                        $UPTIME, $ACTION, $IPADDRESS
                    ];

        
     //  DB::enableQueryLog();
       try {

            //save data
           $sp_result = DB::select('EXEC SP_CTRY_IN ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?', $coutry_data);
      
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

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
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

            $objCountry = TblMstFrm4::where('CTRYID','=',$id)->first();
            if(strtoupper($objCountry->STATUS)=="A" || strtoupper($objCountry->STATUS)=="C"){
                 exit("Sorry, Only Un Approved record can edit.");
             }


            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            return view('masters.country.mstfrm4edit',compact(['objCountry','user_approval_level','objRights']));
        }

    }//edit function

    //update the data
   public function update(Request $request)
   {
      
     
     //Carbon::parse($request['DODEACTIVATED'])->format('Y-m-d');

    // dd( $newDateString );
    // dd( date("Y-m-d",  $request['DODEACTIVATED'] ));
     
        $update_rules = [

            'COUNTRY_NAME' => 'required',          
        ];

        $req_update_data = [

            'COUNTRY_NAME' => $request['COUNTRY_NAME']
        ]; 


        $validator = Validator::make( $req_update_data, $update_rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }
    
        //update data
        //$CTRYID   =    ($request['CTRYID']);
        $CTRYCODE   =   strtoupper(trim($request['CTRYCODE']) );
        $COUNTRY_NAME =   trim($request['COUNTRY_NAME']);  
        $ISD        =   (isset($request['ISDCODE']) && trim($request['ISDCODE']) )? trim($request['ISDCODE']) : NULL ;
        $LANG       =   (isset($request['LANG']) && trim($request['LANG']) )? trim($request['LANG']) : NULL ;

        $CONTI      =  (isset($request['CONTINENTAL']) && trim($request['CONTINENTAL']) )? trim($request['CONTINENTAL']) : NULL ;
        $CAPITAL    =   (isset($request['CAPITAL']) && trim($request['CAPITAL']) )? trim($request['CAPITAL']) : NULL ; 

      //  @DEACTIVATED BIT,@DODEACTIVATED DATE,  

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
        $VTID     =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();
        
        $country_update_data = [ $CTRYCODE, $COUNTRY_NAME, $ISD, $LANG,  
                                $CONTI, $CAPITAL,$DEACTIVATED, $DODEACTIVATED, 
                                $CYID_REF, $BRID_REF, $FYID_REF, $VTID, 
                                $USERID, $UPDATE, $UPTIME, $ACTION, 
                                $IPADDRESS
                            ];

                    // dd($country_update_data);       


        try {

                $sp_result = DB::select('EXEC SP_CTRY_UP  ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?', $country_update_data);
               

            } catch (\Throwable $th) {
        
            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);
        }
    

        if($sp_result[0]->RESULT=="SUCCESS"){  

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOUND"){
        
            return Response::json(['errors'=>true,'msg' => 'No record found.','country'=>'norecord']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);
        }
        
        exit();            
    } // update function

    //singleApprove begin
    public function singleapprove(Request $request)
    {
      
         $approv_rules = [
 
             'COUNTRY_NAME' => 'required',          
         ];
 
         $req_approv_data = [
 
             'COUNTRY_NAME' =>   $request['COUNTRY_NAME']
         ]; 
 
 
         $validator = Validator::make( $req_approv_data, $approv_rules, $this->messages);
 
         if ($validator->fails())
         {
			return Response::json(['errors' => $validator->errors()]);	
         }
     
         //update data
         //$CTRYID   =    ($request['CTRYID']);
         $CTRYCODE   =    trim($request['CTRYCODE']);
         $COUNTRY_NAME =   trim($request['COUNTRY_NAME']);  
         $ISD        =   (isset($request['ISDCODE']) && trim($request['ISDCODE']) )? trim($request['ISDCODE']) : NULL ;
         $LANG       =   (isset($request['LANG']) && trim($request['LANG']) )? trim($request['LANG']) : NULL ;
 
         $CONTI      =  (isset($request['CONTINENTAL']) && trim($request['CONTINENTAL']) )? trim($request['CONTINENTAL']) : NULL ;
         $CAPITAL    =   (isset($request['CAPITAL']) && trim($request['CAPITAL']) )? trim($request['CAPITAL']) : NULL ; 

               //  @DEACTIVATED BIT,@DODEACTIVATED DATE,  

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
         $VTID       =   $this->vtid_ref;
         $USERID     =   Auth::user()->USERID;
         $UPDATE     =   Date('Y-m-d');
         
         $UPTIME     =   Date('h:i:s.u');
         $ACTION     =   trim($request['user_approval_level']);   // user approval level value
         $IPADDRESS  =   $request->getClientIp();
         
         $country_approved_data = [ $CTRYCODE, $COUNTRY_NAME, $ISD, $LANG,  
                                    $CONTI, $CAPITAL,$DEACTIVATED, $DODEACTIVATED, 
                                    $CYID_REF, $BRID_REF, $FYID_REF, $VTID, 
                                    $USERID, $UPDATE, $UPTIME, $ACTION, 
                                    $IPADDRESS
                             ];

        //dd($country_approved_data );
 
         try {
 
                 $sp_result = DB::select('EXEC SP_CTRY_UP  ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?', $country_approved_data);
 
             } catch (\Throwable $th) {
         
             return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);
         }
     
 
         if($sp_result[0]->RESULT=="SUCCESS"){  
 
             return Response::json(['success' =>true,'msg' => 'Record successfully approved.']);
         
         }elseif($sp_result[0]->RESULT=="NO RECORD FOUND"){
         
             return Response::json(['errors'=>true,'msg' => 'No record found.','country'=>'norecord']);
             
         }else{
 
             return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);
         }
         
         exit();  

     }  //singleApprove end


    public function view($id){

        if(!is_null($id))
        {
            $objCountry = TblMstFrm4::where('CTRYID','=',$id)->first();
            return view('masters.country.mstfrm4view',compact(['objCountry']));
        }

    }//view function
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }

        $objCountries = TblMstFrm4::whereIn('CTRYID',$ids_data)->get();
        
        return view('masters.country.mstfrm4print',compact(['objCountries']));
   }//print


   
   

    
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objCountry = TblMstFrm4::where('CTRYID','=',$id)->first();

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
                        ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                        ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                        ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                        ->get()->toArray();

                 // dump( $objAttachments);

            return view('masters.country.mstfrm4attachment',compact(['objCountry','objMstVoucherType','objAttachments']));
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
            $TABLE      =   "TBL_MST_COUNTRY";
            $FIELD      =   "CTRYID";
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
        
        return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','salesenquiry'=>'norecord']);
        
        }else{
        return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','salesenquiry'=>'Some Error']);
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
        $TABLE      =   "TBL_MST_COUNTRY";
        $FIELD      =   "CTRYID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $candata = [
            'NT' => "TBL_MST_COUNTRY",
            ];
         
        $links["TABLES"] = $candata; 
        $cancelxml = ArrayToXml::convert($links);
        
        $mstcountry_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$cancelxml ];

        
        $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mstcountry_cancel_data);
        
        //dump($sp_result);
        if($sp_result[0]->RESULT=="CANCELED"){  
          //  echo 'in cancel';
          return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
        
            //echo "NO RECORD FOR CANCEL";
            return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
            
        }else{
            //echo "--else--";
               return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'invalid'=>'invalid']);
        }
        
        exit(); 
    }


    //--------------not using
    public function getcountriesOld(Request $request){

        $columns = array( 
            0 =>'NO', 
            1 =>'CTRYCODE',
            2 =>'NAME',
            3 =>'ISDCODE',
            4 =>'LANG',
            5 =>'CONTINENTAL',
            6 =>'CAPITAL',
            7 =>'STATUS',
        );  

        $totalRows = TblMstFrm4::count();      
        $totalFiltered = $totalRows;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        if(empty($request->input('search.value')))
        {            
            $countrydata = TblMstFrm4::offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
        }else {

            $search = $request->input('search.value'); 
            $filtercolumn = $request->input('filtercolumn');

            //ALL COLUMN
            if($filtercolumn =='ALL'){

                $countrydata =  TblMstFrm4::where("CTRYCODE","LIKE","%$search%")
                    ->orWhere("NAME","LIKE","%$search%")
                    ->orWhere("ISDCODE","LIKE","%$search%")
                    ->orWhere("LANG","LIKE","%$search%")
                    ->orWhere("CONTINENTAL","LIKE","%$search%")
                    ->orWhere("CAPITAL","LIKE","%$search%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

                $totalFiltered = TblMstFrm4::where("CTRYCODE","LIKE","%$search%")
                    ->orWhere("NAME","LIKE","%$search%")
                    ->orWhere("ISDCODE","LIKE","%$search%")
                    ->orWhere("LANG","LIKE","%$search%")
                    ->orWhere("CONTINENTAL","LIKE","%$search%")
                    ->orWhere("CAPITAL","LIKE","%$search%")
                    ->count();

            }else{
               
                if($filtercolumn=='STATUS'){
                    if(strtolower($search)=='approved'){
                        $search =  1;
                    }
                    elseif(strtolower($search)=='unapproved'){
                        $search =  0;
                    }
                }

                $countrydata =  TblMstFrm4::where("$filtercolumn","LIKE","%$search%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();

                $totalFiltered = TblMstFrm4::where("$filtercolumn","LIKE","%$search%")
                ->count();

            }         

            
        }

        $data = array();
        if(!empty($countrydata))
        {
            foreach ($countrydata as $key=>$countryitem)
            {
                $record_status = (!Empty($countryitem->STATUS) && $countryitem->STATUS==1) ? 1 : 0;

                $nestedData['NO'] = '<input type="checkbox" id="chkId'.$countryitem->CTRYID.'" value="'.$countryitem->CTRYID.'" class="js-selectall1" data-rcdstatus="'. $record_status.'" >';

                $nestedData['NAME'] = $countryitem->NAME;
                $nestedData['CTRYCODE'] = $countryitem->CTRYCODE;
                $nestedData['ISDCODE'] = $countryitem->ISDCODE;
                $nestedData['LANG'] = $countryitem->LANG;
                $nestedData['CONTINENTAL'] = $countryitem->CONTINENTAL;
                $nestedData['CAPITAL'] = $countryitem->CAPITAL;
                $nestedData['STATUS'] = ($record_status==1)?'Approved':'Unapproved';
                // $nestedData['action'] = '<a href="#" class="del"><span class="glyphicon glyphicon-trash"></span> 
                // </a><a href="#" class="edit"><span class="glyphicon glyphicon-edit"></span></a>';
                $data[] = $nestedData;
            }

        }
        $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalRows),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data   
        );            
        echo json_encode($json_data); 

    }//getcountriesOld


}
