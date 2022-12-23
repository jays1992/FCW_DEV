<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm3;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;


class MstFrm3Controller extends Controller
{
   
    protected $form_id = 3;
    protected $vtid_ref   = 115;  //voucher type id

    //validation messages
    protected   $messages = [
					'CTRYID_REF.required' => 'Required field',
                    'STCODE.required' => 'Required field',
                    'STCODE.unique' => 'Duplicate Code',
                    //'STCODE.min' => 'min 3 char',
                    'STATE_NAME.required' => 'Name is required',
					'STTYPE.required' => 'Required field'
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
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();

        //dd($objRights);

        
        $objDataList=DB::select("SELECT STID,STCODE,NAME,STDCODE,LANG,NEWSC,CAPITAL,STTYPE,DEACTIVATED,DODEACTIVATED,INDATE,STATUS FROM TBL_MST_STATE");

       return view('masters.Common.State.mstfrm3',compact(['objRights','objDataList']));
        
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
        
        $destinationPath = storage_path()."/company".$CYID_REF."/statemst";
       
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
            return redirect()->route("master",[3,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
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
        
        //  echo "<pre>";
        // // print_r($uploaded_data);
        // dump($attachment_data);
        
        // echo "</pre>";

       


        // echo "<pre>";
        // print_r($attachment_data);
        // dump($ATTACHMENTS_XMl);
        
        // echo "</pre>";
          
       // try {

             //save data
             $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);

           //  dd($sp_result[0]->RESULT);
      
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

            return redirect()->route("master",[3,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[3,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("master",[4,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
            return redirect()->route("master",[3,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   

    public function add(){


        $objRights = DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();

		
		$CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');    
        $FYID_REF   =   Session::get('FYID_REF');
		
		$objCountryList = DB::table('TBL_MST_COUNTRY')
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('CTRYID','CTRYCODE','NAME')
        ->get();

        $objDD = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('STATUS','=','A')
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
		     
      return view('masters.Common.State.mstfrm3add',compact(['objCountryList','objRights','objDD','objDOCNO']));
       
    }

 
   
    public function getCountryName(Request $request){
        
        $CTRYID          =   $request['CTRYID'];
		
		$objCountryName = DB::table('TBL_MST_COUNTRY')
        ->where('CTRYID','=', $CTRYID )
        ->where('STATUS','=','A')
        ->select('NAME')
        ->first();
		
		if(!empty($objCountryName)){
			echo $objCountryName->NAME;
		}
		else{
			echo "";
		}
        exit();
    }
	
	  public function getCountryCode(Request $request){
        
        $CTRYID          =   $request['CTRYID'];
		
		$objCountryName = DB::table('TBL_MST_COUNTRY')
        ->where('CTRYID','=', $CTRYID )
        ->where('STATUS','=','A')
        ->select('CTRYCODE')
        ->first();
		
		if(!empty($objCountryName)){
			echo $objCountryName->CTRYCODE;
		}
		else{
			echo "";
		}
        exit();
    }
   
   

   public function codeduplicate(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $STCODE     =   $request['STCODE'];
        $CTRYID_REF =   $request['CTRYID_REF'];
        
        
        $objLabel = DB::table('TBL_MST_STATE')
        ->where('STCODE','=',$STCODE)
        ->where('CTRYID_REF','=',$CTRYID_REF)
        ->select('STCODE')
        ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate code']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }

    
   public function save(Request $request){

	//dd($request->all());
        //validation rules
    $rules = [
			'CTRYID_REF' => 'required', 
            'STCODE' => 'required',
            'STATE_NAME' => 'required', 
			'STTYPE' => 'required'
        ];
		
        $req_data = [
			'CTRYID_REF'     =>   $request['CTRYID_REF'],
            'STCODE'     =>    $request['STCODE'],
            'STATE_NAME' =>   $request['STATE_NAME'],
			'STTYPE' =>   $request['STTYPE']
        ]; 


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
           return Response::json(['errors' => $validator->errors()]);	
        }
     
        //get data
        $STCODE   	=   strtoupper(trim($request['STCODE']) );
        $STATE_NAME =   trim($request['STATE_NAME']); 
		$CTRYID 	=   trim($request['CTRYID_REF']); 
        $STDCODE    =   (isset($request['STDCODE']) && trim($request['STDCODE']) )? trim($request['STDCODE']) : NULL ;
        $LANG       =   (isset($request['LANG']) && trim($request['LANG']) )? trim($request['LANG']) : NULL ;
		$NEWSC      =  (isset($request['NEWSC']) && trim($request['NEWSC']) )? trim($request['NEWSC']) : NULL ;
		$CAPITAL    =   (isset($request['CAPITAL']) && trim($request['CAPITAL']) )? trim($request['CAPITAL']) : NULL ; 
        $STTYPE     =  (isset($request['STTYPE']) && trim($request['STTYPE']) )? trim($request['STTYPE']) : NULL ;
        
		$CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID       =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "ADD";
        $IPADDRESS  =   $request->getClientIp();
        
		
        $state_data = [
                        $STCODE,$STATE_NAME,$CTRYID,$STDCODE,
						$LANG,$NEWSC, $CAPITAL,$STTYPE,
						$CYID_REF, $BRID_REF,$FYID_REF, $VTID, 
						$USERID, $UPDATE,$UPTIME, $ACTION, 
						$IPADDRESS
                    ];
					
	    //print_r($state_data);die;
					

     //  DB::enableQueryLog();
        try {

            //save data
           $sp_result = DB::select('EXEC SP_STATE_IN ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?', $state_data);
      

        } catch (\Throwable $th) {
        
			//return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
    
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
            
            $objRights = DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();


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

            $objCountry = TblMstFrm3::where('STID','=',$id)->first();


          
			
			$objCountryList = DB::table('TBL_MST_COUNTRY')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('CTRYID','CTRYCODE','NAME')
			->get();
			
			
			
            return view('masters.Common.State.mstfrm3edit',compact(['objCountry','user_approval_level','objCountryList','objRights']));
        }

    }//edit function

    //update the data
   public function update(Request $request)
   {
      
     
     //Carbon::parse($request['DODEACTIVATED'])->format('Y-m-d');

    // dd( $newDateString );
    // dd( date("Y-m-d",  $request['DODEACTIVATED'] ));
     
        $update_rules = [
			'CTRYID_REF' => 'required', 
            'STATE_NAME' => 'required', 
			'STTYPE' => 'required'
        ];

        $req_update_data = [
            'CTRYID_REF'     =>    $request['CTRYID_REF'],
            'STATE_NAME' =>   $request['STATE_NAME'],
			'STTYPE' =>   $request['STTYPE']
        ]; 
		
		
        $validator = Validator::make( $req_update_data, $update_rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }
    
        //update data
        //$CTRYID   =    ($request['CTRYID']);
		
		$STCODE   	=   trim($request['STCODE']);
        $STATE_NAME =   trim($request['STATE_NAME']); 
		$CTRYID 	=   trim($request['CTRYID_REF']); 
        $STDCODE    =   (isset($request['STDCODE']) && trim($request['STDCODE']) )? trim($request['STDCODE']) : NULL ;
        $LANG       =   (isset($request['LANG']) && trim($request['LANG']) )? trim($request['LANG']) : NULL ;
		$NEWSC      =  (isset($request['NEWSC']) && trim($request['NEWSC']) )? trim($request['NEWSC']) : NULL ;
		$CAPITAL    =   (isset($request['CAPITAL']) && trim($request['CAPITAL']) )? trim($request['CAPITAL']) : NULL ; 
        $STTYPE     =  (isset($request['STTYPE']) && trim($request['STTYPE']) )? trim($request['STTYPE']) : NULL ;
		

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
        							
		$state_update_data = [
                        $STCODE,$STATE_NAME,$CTRYID,$STDCODE,
						$LANG,$NEWSC, $CAPITAL,$STTYPE,
						$DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF,
						$FYID_REF, $VTID, $USERID, $UPDATE,
						$UPTIME, $ACTION, $IPADDRESS
                    ];

       // dd($state_update_data);       


        try {

                $sp_result = DB::select('EXEC SP_STATE_UP  ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?', $state_update_data);
               

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
 
            'CTRYID_REF' => 'required', 
            'STATE_NAME' => 'required', 
			'STTYPE' => 'required'        
         ];
 
         $req_approv_data = [
 
            'CTRYID_REF'     =>    $request['CTRYID_REF'],
            'STATE_NAME' =>   $request['STATE_NAME'],
			'STTYPE' =>   $request['STTYPE']
         ]; 
 
 
         $validator = Validator::make( $req_approv_data, $approv_rules, $this->messages);
 
         if ($validator->fails())
         {
         return Response::json(['errors' => $validator->errors()]);	
         }
     
         //update data
         //$CTRYID   =    ($request['CTRYID']);
	
		$STCODE   	=   trim($request['STCODE']);
        $STATE_NAME =   trim($request['STATE_NAME']); 
		$CTRYID 	=   trim($request['CTRYID_REF']); 
        $STDCODE    =   (isset($request['STDCODE']) && trim($request['STDCODE']) )? trim($request['STDCODE']) : NULL ;
        $LANG       =   (isset($request['LANG']) && trim($request['LANG']) )? trim($request['LANG']) : NULL ;
		$NEWSC      =  (isset($request['NEWSC']) && trim($request['NEWSC']) )? trim($request['NEWSC']) : NULL ;
		$CAPITAL    =   (isset($request['CAPITAL']) && trim($request['CAPITAL']) )? trim($request['CAPITAL']) : NULL ; 
        $STTYPE     =  (isset($request['STTYPE']) && trim($request['STTYPE']) )? trim($request['STTYPE']) : NULL ;
		 
		 
		 

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
         					 
		$state_approved_data = [
                        $STCODE,$STATE_NAME,$CTRYID,$STDCODE,
						$LANG,$NEWSC, $CAPITAL,$STTYPE,
						$DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF,
						$FYID_REF, $VTID, $USERID, $UPDATE,
						$UPTIME, $ACTION, $IPADDRESS
                    ];

        //dd($country_approved_data );
 
         try {
 
                 $sp_result = DB::select('EXEC SP_STATE_UP  ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?', $state_approved_data);
 
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
            $objCountry = TblMstFrm3::where('STID','=',$id)->first();
            return view('masters.Common.State.mstfrm3view',compact(['objCountry']));
        }

    }//view function
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }

        $objCountries = TblMstFrm3::whereIn('STID',$ids_data)->get();
        
        return view('masters.Common.State.mstfrm3print',compact(['objCountries']));
   }//print


   
   

    
    //display attachments form
    public function attachment($id){

       

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objCountry = TblMstFrm3::where('STID','=',$id)->first();

            //select * from TBL_MST_VOUCHERTYPE where VTID=11

            $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
                    ->where('VTID','=',$this->vtid_ref)
                    ->select('VTID','VCODE','DESCRIPTIONS')
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

            return view('masters.Common.State.mstfrm3attachment',compact(['objCountry','objMstVoucherType','objAttachments']));
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
            $TABLE      =   "TBL_MST_STATE";
            $FIELD      =   "STID";
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

          

   //save data

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_STATE";
        $FIELD      =   "STID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $candata = [
            'NT' => "TBL_MST_STATE",
            ];
         
        $links["TABLES"] = $candata; 
        $cancelxml = ArrayToXml::convert($links);
        
        $mststate_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS, $cancelxml  ];

        
        $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mststate_cancel_data);
        
        
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


    


}
