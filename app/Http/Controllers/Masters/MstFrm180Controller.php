<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm180;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;


class MstFrm180Controller extends Controller
{
   
    protected $form_id = 180;
    protected $vtid_ref   = 195;  //voucher type id

    //validation messages
    protected   $messages = [
                    'SHIFT_CODE.required' => 'Required field',
                    'PAY_PERIOD_DESC.required' => 'Required field',
                    'MTID_REF.required' => 'Required field',
                    'YRID_REF.required' => 'Required field'
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
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first(); 

        $FormId         =   $this->form_id;
       
        $objDataList = DB::table('TBL_MST_SHIFT')
            ->where('TBL_MST_SHIFT.CYID_REF','=',Auth::user()->CYID_REF)
            ->select('TBL_MST_SHIFT.*')
            ->get();    

        return view('masters.Payroll.ShiftMaster.mstfrm180',compact(['objRights','objDataList','FormId']));

    }

    
    
    public function add(){ 

        $objMonth = DB::select('SELECT * FROM TBL_MST_MONTH  
                    WHERE  CYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                    order by MTCODE ASC', [Auth::user()->CYID_REF, 'A' ]);

        $objYear = DB::select('SELECT * FROM TBL_MST_YEAR  
                    WHERE  CYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                    order by YRCODE ASC', [Auth::user()->CYID_REF, 'A' ]);

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $objDD = DB::table('TBL_MST_DOCNO_DEFINITION')
                ->where('VTID_REF','=',$this->vtid_ref)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->where('FYID_REF','=',$FYID_REF)
                ->where('STATUS','=','A')
                ->select('TBL_MST_DOCNO_DEFINITION.*')
                ->first();

        //dump($objDD);
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


        return view('masters.Payroll.ShiftMaster.mstfrm180add',compact(['objMonth','objYear','objDD','objDOCNO']));
    }

   public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $SHIFT_CODE =   $request['SHIFT_CODE'];
        
        $objLabel = DB::table('TBL_MST_SHIFT')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('SHIFT_CODE','=',$SHIFT_CODE)
        ->select('SHIFT_CODE')
        ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }

   public function save(Request $request){

       
        $SHIFT_CODE =   strtoupper(trim($request['SHIFT_CODE']) );
        $SHIFT_NAME =  trim($request['SHIFT_NAME']);  
        $START_TIME        =  trim($request['START_TIME']);  
        $END_TIME       =   trim($request['END_TIME']);  

        $MIN_HOURS_FULL2      =  trim($request['MIN_HOURS_FULL']);  
        $MIN_MINS_FULL2     =  trim($request['MIN_MINS_FULL']);  
        $MIN_HOURS_FULL      = $MIN_HOURS_FULL2.':'.$MIN_MINS_FULL2.':00';  

        $MIN_HOURS_HALF2      =  trim($request['MIN_HOURS_HALF']);  
        $MIN_MINS_HALF2     =  trim($request['MIN_MINS_HALF']);  
        $MIN_HOURS_HALF      = $MIN_HOURS_HALF2.':'.$MIN_MINS_HALF2.':00';  
       
        $GRACE_TIME_IN_HOURS2      =  trim($request['GRACE_TIME_IN_HOURS']);  
        $GRACE_TIME_IN_MINS2     =  trim($request['GRACE_TIME_IN_MINS']);  
        $GRACE_TIME_IN      = $GRACE_TIME_IN_HOURS2.':'.$GRACE_TIME_IN_MINS2.':00';  
        
        $BREAK_TIME_HOURS2      =  trim($request['BREAK_TIME_HOURS']);  
        $BREAK_TIME_MINS2     =  trim($request['BREAK_TIME_MINS']);  
        $BREAK_TIME      = $BREAK_TIME_HOURS2.':'.$BREAK_TIME_MINS2.':00';  

        $MIN_HOURS_HALF       =  $MIN_HOURS_HALF;  
        $GRACE_TIME_IN       =   $GRACE_TIME_IN ;  
        $BREAK_TIME       =   $BREAK_TIME;  

        $SHIFT_DTCHANGE_YES  =  trim($request['SHIFT_DTCHANGE_YES']);  
        $START_TIME_OT       =  trim($request['START_TIME_OT']);  

        $DEACTIVATED    =   0;  
        $DODEACTIVATED  =   NULL;  

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');    
        $USERID         =   Auth::user()->USERID;   
        $VTID           =   $this->vtid_ref;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();
        
        
        $array_data   = [
                        $SHIFT_CODE, $SHIFT_NAME, $START_TIME, $END_TIME,
                        $MIN_HOURS_FULL, $MIN_HOURS_HALF,$GRACE_TIME_IN,$BREAK_TIME,
                        $SHIFT_DTCHANGE_YES, $START_TIME_OT, $DEACTIVATED, $DODEACTIVATED, 
                        $CYID_REF, $BRID_REF, $FYID_REF, $VTID, 
                        $USERID, $UPDATE, $UPTIME, $ACTION, 
                        $IPADDRESS
                    ];

       // @SHIFT_CODE VARCHAR(50),@SHIFT_NAME VARCHAR(100),@START_TIME TIME,@END_TIME TIME,@MIN_HOURS_FULL TIME,    
        // @MIN_HOURS_HALF TIME,@GRACE_TIME_IN TIME,@BREAK_TIME TIME,@SHIFT_DTCHANGE_YES BIT,    
        // @START_TIME_OT TIME,@DEACTIVATED BIT,@DODEACTIVATED DATE,@CYID_REF INT,@BRID_REF INT,@FYID_REF INT,@VTID int,  
        // @USERID int,@UPDATE date,@UPTIME time,@ACTION varchar(30),@IPADDRESS varchar(30)   

            
                    
        try {
        
            $sp_result = DB::select('EXEC SP_SHIFT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?', $array_data);

            
        
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

            $objResponse = TblMstFrm180::where('SHIFTID','=',$id)->first();
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

            return view('masters.Payroll.ShiftMaster.mstfrm180edit',compact(['objResponse','user_approval_level','objRights']));
        }

    }

     
    public function update(Request $request)
    {

        $SHIFT_CODE =   strtoupper(trim($request['SHIFT_CODE']) );
        $SHIFT_NAME =  trim($request['SHIFT_NAME']);  
        $START_TIME        =  trim($request['START_TIME']);  
        $END_TIME       =   trim($request['END_TIME']);  

        $MIN_HOURS_FULL2      =  trim($request['MIN_HOURS_FULL']);  
        $MIN_MINS_FULL2     =  trim($request['MIN_MINS_FULL']);  
        $MIN_HOURS_FULL      = $MIN_HOURS_FULL2.':'.$MIN_MINS_FULL2.':00';  

        $MIN_HOURS_HALF2      =  trim($request['MIN_HOURS_HALF']);  
        $MIN_MINS_HALF2     =  trim($request['MIN_MINS_HALF']);  
        $MIN_HOURS_HALF      = $MIN_HOURS_HALF2.':'.$MIN_MINS_HALF2.':00';  
       
        $GRACE_TIME_IN_HOURS2      =  trim($request['GRACE_TIME_IN_HOURS']);  
        $GRACE_TIME_IN_MINS2     =  trim($request['GRACE_TIME_IN_MINS']);  
        $GRACE_TIME_IN      = $GRACE_TIME_IN_HOURS2.':'.$GRACE_TIME_IN_MINS2.':00';  
        
        $BREAK_TIME_HOURS2      =  trim($request['BREAK_TIME_HOURS']);  
        $BREAK_TIME_MINS2     =  trim($request['BREAK_TIME_MINS']);  
        $BREAK_TIME      = $BREAK_TIME_HOURS2.':'.$BREAK_TIME_MINS2.':00';  

        $MIN_HOURS_HALF       =  $MIN_HOURS_HALF;  
        $GRACE_TIME_IN       =   $GRACE_TIME_IN ;  
        $BREAK_TIME       =   $BREAK_TIME;  

        $SHIFT_DTCHANGE_YES  =  trim($request['SHIFT_DTCHANGE_YES']);  
        $START_TIME_OT       =  trim($request['START_TIME_OT']);  

        $DEACTIVATED    =   0;  
        $DODEACTIVATED  =   NULL;  

       
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
        
        $array_data   = [
            $SHIFT_CODE, $SHIFT_NAME, $START_TIME, $END_TIME,
            $MIN_HOURS_FULL, $MIN_HOURS_HALF,$GRACE_TIME_IN,$BREAK_TIME,
            $SHIFT_DTCHANGE_YES, $START_TIME_OT, $DEACTIVATED, $DODEACTIVATED, 
            $CYID_REF, $BRID_REF, $FYID_REF, $VTID, 
            $USERID, $UPDATE, $UPTIME, $ACTION, 
            $IPADDRESS
        ];

       
        try {

            $sp_result = DB::select('EXEC SP_SHIFT_UP ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?', $array_data);
           

        } catch (\Throwable $th) {

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);

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
        
        $destinationPath = storage_path()."/docs/company".$CYID_REF."/ShiftMaster";

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
            return redirect()->route("master",[180,"attachment",$ATTACH_DOCNO])->with("success","The file is already exist");
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

            return redirect()->route("master",[180,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[180,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("master",[180,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
            return redirect()->route("master",[180,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   

  
    //singleApprove begin
    public function singleapprove(Request $request)
    {
      
        $SHIFT_CODE =   strtoupper(trim($request['SHIFT_CODE']) );
        $SHIFT_NAME =  trim($request['SHIFT_NAME']);  
        $START_TIME        =  trim($request['START_TIME']);  
        $END_TIME       =   trim($request['END_TIME']);  

        $MIN_HOURS_FULL2      =  trim($request['MIN_HOURS_FULL']);  
        $MIN_MINS_FULL2     =  trim($request['MIN_MINS_FULL']);  
        $MIN_HOURS_FULL      = $MIN_HOURS_FULL2.':'.$MIN_MINS_FULL2.':00';  

        $MIN_HOURS_HALF2      =  trim($request['MIN_HOURS_HALF']);  
        $MIN_MINS_HALF2     =  trim($request['MIN_MINS_HALF']);  
        $MIN_HOURS_HALF      = $MIN_HOURS_HALF2.':'.$MIN_MINS_HALF2.':00';  
       
        $GRACE_TIME_IN_HOURS2      =  trim($request['GRACE_TIME_IN_HOURS']);  
        $GRACE_TIME_IN_MINS2     =  trim($request['GRACE_TIME_IN_MINS']);  
        $GRACE_TIME_IN      = $GRACE_TIME_IN_HOURS2.':'.$GRACE_TIME_IN_MINS2.':00';  
        
        $BREAK_TIME_HOURS2      =  trim($request['BREAK_TIME_HOURS']);  
        $BREAK_TIME_MINS2     =  trim($request['BREAK_TIME_MINS']);  
        $BREAK_TIME      = $BREAK_TIME_HOURS2.':'.$BREAK_TIME_MINS2.':00';  

        $MIN_HOURS_HALF       =  $MIN_HOURS_HALF;  
        $GRACE_TIME_IN       =   $GRACE_TIME_IN ;  
        $BREAK_TIME       =   $BREAK_TIME;  

        $SHIFT_DTCHANGE_YES  =  trim($request['SHIFT_DTCHANGE_YES']);  
        $START_TIME_OT       =  trim($request['START_TIME_OT']);  

        $DEACTIVATED    =   0;  
        $DODEACTIVATED  =   NULL;  

       
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
        $ACTION     =   trim($request['user_approval_level']);   // user approval level value
        $IPADDRESS  =   $request->getClientIp();
        
        $array_data   = [
            $SHIFT_CODE, $SHIFT_NAME, $START_TIME, $END_TIME,
            $MIN_HOURS_FULL, $MIN_HOURS_HALF,$GRACE_TIME_IN,$BREAK_TIME,
            $SHIFT_DTCHANGE_YES, $START_TIME_OT, $DEACTIVATED, $DODEACTIVATED, 
            $CYID_REF, $BRID_REF, $FYID_REF, $VTID, 
            $USERID, $UPDATE, $UPTIME, $ACTION, 
            $IPADDRESS
        ];

        
        try {

            $sp_result = DB::select('EXEC SP_SHIFT_UP ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?', $array_data);
           
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

     }  //singleApprove end


    public function view($id){

        if(!is_null($id))
        {
        
            $objResponse = TblMstFrm180::where('SHIFTID','=',$id)->first();

            return view('masters.Payroll.ShiftMaster.mstfrm180view',compact(['objResponse']));
        }

    }
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }
        $objResponse = TblMstFrm180::whereIn('SHIFTID',$ids_data)->get();
        
        return view('masters.Payroll.ShiftMaster.mstfrm180print',compact(['objResponse']));
   }//print


   
    

    
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objResponse = TblMstFrm180::where('SHIFTID','=',$id)->first();

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

                  //dd( $objResponse );
                  


            return view('masters.Payroll.ShiftMaster.mstfrm180attachment',compact(['objResponse','objMstVoucherType','objAttachments']));
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
            $TABLE      =   "TBL_MST_SHIFT";
            $FIELD      =   "SHIFTID";
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

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_SHIFT";
        $FIELD      =   "SHIFTID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_MST_SHIFT',
        ];
        
      
        $wrapped_links["TABLES"] = $req_data; 
        
        $XMLTAB = ArrayToXml::convert($wrapped_links);

        $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

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
