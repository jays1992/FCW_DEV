<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm150;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;


class MstFrm150Controller extends Controller
{
   
    protected $form_id = 150;
    protected $vtid_ref   = 111;  //voucher type id

    //validation messages
    protected   $messages = [
                    'UCODE.required' => 'Required field',
                    'UCODE.unique' => 'Duplicate Code',
                    'DESCRIPTIONS.required' => 'Required field',
                    'PASSWORD.required' => 'Required field',
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



        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $objRights = DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first(); 

        // $objDataList=DB::select("SELECT USERID,UCODE,DESCRIPTIONS,INDATE,STATUS 
        // FROM TBL_MST_USER
        // WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF'");

        $objDataList=DB::select("SELECT T1.USERID,T1.UCODE,T1.DESCRIPTIONS,T1.DEACTIVATED,T1.DODEACTIVATED,T1.INDATE,T1.STATUS 
        FROM TBL_MST_USER T1
        INNER JOIN TBL_MST_USER_BRANCH_MAP T2
        ON T1.CYID_REF=T2.CYID_REF AND T1.USERID=T2.USERID_REF
        WHERE T2.MAPBRID_REF='$BRID_REF' AND T1.CYID_REF='$CYID_REF' ORDER BY T1.USERID DESC");

        return view('masters.Common.UserMaster.mstfrm150',compact(['objRights','objDataList']));

    }
    
    public function add(){ 

        $objEmployee=$this->getEmployee();

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');    
        $FYID_REF   =   Session::get('FYID_REF');

        $objDD = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
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


        return view('masters.Common.UserMaster.mstfrm150add',compact(['objEmployee','objDD','objDOCNO']));
    }

    public function getEmployee(){
        // return $objEmployeeList = DB::table('TBL_MST_EMPLOYEE')
        //     ->where('CYID_REF','=',Auth::user()->CYID_REF)
        //     ->where('BRID_REF','=',Session::get('BRID_REF'))
        //     ->where('STATUS','=','A')
        //     ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        //     ->select('EMPID','EMPCODE','FNAME','MNAME','LNAME')
        //     ->get();

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        return DB::select("SELECT T1.* FROM TBL_MST_EMPLOYEE T1
        INNER JOIN TBL_MST_EMP_BRANCH_MAP T2 ON T1.EMPID=T2.EMPID_REF AND T1.BRID_REF=T2.MAPBRID_REF AND T1.CYID_REF=T2.CYID_REF 
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.STATUS='A'  AND (T2.DEACTIVATED=0 or T2.DEACTIVATED is null)");

    }

    


   public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $UCODE =   $request['UCODE'];
        
        $objLabel = DB::table('TBL_MST_USER')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('UCODE','=',$UCODE)
        ->select('UCODE')
        ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }

   public function existData($id,$type,$uid){
    
    if($type =="add"){
        return $data_json  =   DB::table('TBL_MST_USERDETAILS')                 
                    ->where('EMPID_REF','=',$id)
                    ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                    ->select('UDID')
                    ->count();
    }
    else{
        return $data_json  =   DB::table('TBL_MST_USERDETAILS')  
                    ->where('UDID','!=',$uid)               
                    ->where('EMPID_REF','=',$id)
                    ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                    ->select('UDID')
                    ->count();
    }

    }
    

   public function save(Request $request){

        $rules = [
            'UCODE' => 'required',
            'DESCRIPTIONS' => 'required',   
            'PASSWORD' => 'required',       
        ];
        
        $req_data = [

            'UCODE'     =>    $request['UCODE'],
            'DESCRIPTIONS' =>   $request['DESCRIPTIONS'],
            'PASSWORD' =>   $request['PASSWORD']
        ]; 

        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }

        $data     =array();
        $existData=array();
        $r_count = $request['Row_Count'];

        for ($i=0; $i<=$r_count; $i++){

            if((isset($request['EMPID_REF_'.$i]) && $request['EMPID_REF_'.$i] !="")){

                $data[$i] = [
                    'EMPID_REF' => trim($request['EMPID_REF_'.$i]),
                    'STARTPD' => trim($request['STARTPD_'.$i]),
                    'ENDPD' => isset($request['ENDPD_'.$i]) && $request['ENDPD_'.$i] !=""?trim($request['ENDPD_'.$i]):NULL,
                    'DEACTIVATED' => isset($request['EMPDEACTIVATED_'.$i]) && $request['EMPDEACTIVATED_'.$i] !=""?trim($request['EMPDEACTIVATED_'.$i]):0,
                    'DODEACTIVATED' => isset($request['EMPDODEACTIVATED_'.$i]) && $request['EMPDODEACTIVATED_'.$i] !=""?trim($request['EMPDODEACTIVATED_'.$i]):NULL,
                    'SUPPERUSER' => isset($request['SUPPERUSER_'.$i]) && $request['SUPPERUSER_'.$i] !=""?trim($request['SUPPERUSER_'.$i]):0,
                ];

                $existData[$i]=trim($request['EMPID_REF_'.$i]);

                if($this->existData(trim($request['EMPID_REF_'.$i]),'add','') > 0){
                    return Response::json(['errors'=>true,'msg' => 'Error:This employee allready allocated.','save'=>'invalid']);
                }

            }
        }

        if(!empty($existData)){
            $counts     = array_count_values($existData);
            $NumVal     = max($counts);

            if( $NumVal > 1){
                return Response::json(['errors'=>true,'msg' => 'Error:Duplicate data','save'=>'invalid']);
            }
        }

        if(!empty($data)){ 
            $wrapped_links["EMPLOYEE"] = $data; 
            $xml = ArrayToXml::convert($wrapped_links);
        }
        else{
            $xml = NULL; 
        } 
        
        $UCODE          =   strtoupper(trim($request['UCODE']) );
        $DESCRIPTIONS   =   trim($request['DESCRIPTIONS']);  
        $DEACTIVATED    =   0;  
        $DODEACTIVATED  =   NULL;  
        $PASSWORD	    =   trim($request['PASSWORD']);

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');       
        $VTID           =   $this->vtid_ref;
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();
        
        $array_data   = [
                        $UCODE, $DESCRIPTIONS,
                        $DEACTIVATED, $DODEACTIVATED,$PASSWORD,$CYID_REF, 
                        $BRID_REF,$FYID_REF,$VTID, $USERID, 
                        $UPDATE,$UPTIME, $ACTION, $IPADDRESS,
                        $xml
                    ];

        try {

            $sp_result = DB::select('EXEC SP_USER_IN ?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?', $array_data);
        
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

            $objResponse = TblMstFrm150::where('USERID','=',$id)->first();

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first(); 

            $objDataResponse = DB::table('TBL_MST_USERDETAILS')                    
                             ->where('TBL_MST_USERDETAILS.USERID_REF','=',$objResponse->USERID)
                             ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_MST_EMPLOYEE.EMPID','=','TBL_MST_USERDETAILS.EMPID_REF')
                             ->select(
                                 'TBL_MST_USERDETAILS.*',
                                 'TBL_MST_EMPLOYEE.EMPID',
                                 'TBL_MST_EMPLOYEE.EMPCODE',
                                 'TBL_MST_EMPLOYEE.FNAME',
                                 'TBL_MST_EMPLOYEE.MNAME',
                                 'TBL_MST_EMPLOYEE.LNAME'
                                 )
                             ->orderBy('TBL_MST_USERDETAILS.UDID','ASC')
                             ->get()->toArray();

                            
            /* if(strtoupper($objResponse->STATUS)=="A" || strtoupper($objResponse->STATUS)=="C"){
                exit("Sorry, Only Un Approved record can edit.");
            } */
            $objCount = count($objDataResponse);

            $objEmployee=$this->getEmployee();

            
            return view('masters.Common.UserMaster.mstfrm150edit',compact(['objResponse','user_approval_level','objRights','objDataResponse','objCount','objEmployee']));
        }

    }

     
    public function update(Request $request)
    {

      // dd($request->all());

        $update_rules = [

            'DESCRIPTIONS' => 'required',
            'PASSWORD' => 'required',          
        ];

        $req_update_data = [

            'DESCRIPTIONS' =>   $request['DESCRIPTIONS'],
            'PASSWORD' =>   $request['PASSWORD']
        ]; 

       
        $validator = Validator::make( $req_update_data, $update_rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }


        $data     =array();
        $existData=array();
        $deactiveData=array();
        $r_count = $request['Row_Count'];

        for ($i=0; $i<=$r_count; $i++){

            if((isset($request['EMPID_REF_'.$i]) && $request['EMPID_REF_'.$i] !="")){

                $data[$i] = [
                    'EMPID_REF' => trim($request['EMPID_REF_'.$i]),
                    'STARTPD' => trim($request['STARTPD_'.$i]),
                    'ENDPD' => isset($request['ENDPD_'.$i]) && $request['ENDPD_'.$i] !=""?trim($request['ENDPD_'.$i]):NULL,
                    'DEACTIVATED' => isset($request['EMPDEACTIVATED_'.$i]) && $request['EMPDEACTIVATED_'.$i] !=""?trim($request['EMPDEACTIVATED_'.$i]):0,
                    'DODEACTIVATED' => isset($request['EMPDODEACTIVATED_'.$i]) && $request['EMPDODEACTIVATED_'.$i] !=""?trim($request['EMPDODEACTIVATED_'.$i]):NULL,
                    'SUPPERUSER' => isset($request['SUPPERUSER_'.$i]) && $request['SUPPERUSER_'.$i] !=""?trim($request['SUPPERUSER_'.$i]):0,
                ];

                $existData[$i]=trim($request['EMPID_REF_'.$i]);

                if($request['EMPDEACTIVATED_'.$i] !="1"){
                    $deactiveData[]=1;
                }
                
                if($this->existData(trim($request['EMPID_REF_'.$i]),'edit',$request['UDID_'.$i]) > 0){
                    return Response::json(['errors'=>true,'msg' => 'Error:This employee allready allocated.','save'=>'invalid']);
                }

            }
        }

        if(!empty($existData)){
            $counts     = array_count_values($existData);
            $NumVal     = max($counts);

            if( $NumVal > 1){
                return Response::json(['errors'=>true,'msg' => 'Error:Duplicate data','save'=>'invalid']);
            }
        }
        
        if(count($deactiveData) > 1){
            return Response::json(['errors'=>true,'msg' => 'Error:Allow only one active employee.','save'=>'invalid']);
        }
        

        if(!empty($data)){ 
            $wrapped_links["EMPLOYEE"] = $data; 
            $xml = ArrayToXml::convert($wrapped_links);
        }
        else{
            $xml = NULL; 
        } 


        $UCODE       =   strtoupper(trim($request['UCODE']) );
        $DESCRIPTIONS   =   trim($request['DESCRIPTIONS']); 
        $PASSWORD	    =   trim($request['PASSWORD']);

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
            $UCODE, $DESCRIPTIONS,
            $DEACTIVATED, $DODEACTIVATED,$PASSWORD,$CYID_REF, 
            $BRID_REF,$FYID_REF,$VTID, $USERID, 
            $UPDATE,$UPTIME, $ACTION, $IPADDRESS,
            $xml
        ];
        

        //$sp_result = DB::select('EXEC SP_USER_UP ?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?', $array_data);
        //dd($sp_result);

        try {

        $sp_result = DB::select('EXEC SP_USER_UP ?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?', $array_data);

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
        
		
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/UserMaster";

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
            return redirect()->route("master",[150,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
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
        
        //    return redirect()->route("master",[150,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");
    
      //  }
     
        if($sp_result[0]->RESULT=="SUCCESS"){

            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }

            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }

            return redirect()->route("master",[150,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[150,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("master",[150,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
            return redirect()->route("master",[150,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   

  


   



    //singleApprove begin
    public function singleapprove(Request $request)
    {
      
         $approv_rules = [
 
            'DESCRIPTIONS' => 'required',
            'PASSWORD' => 'required',          
         ];
 
         $req_approv_data = [
 
            'DESCRIPTIONS' =>   $request['DESCRIPTIONS'],
            'PASSWORD' =>   $request['PASSWORD']
         ]; 
 
 
         $validator = Validator::make( $req_approv_data, $approv_rules, $this->messages);
 
         if ($validator->fails())
         {
         return Response::json(['errors' => $validator->errors()]);	
         }


        $data     =array();
        $existData=array();
        $deactiveData=array();
        $r_count = $request['Row_Count'];

        for ($i=0; $i<=$r_count; $i++){

            if((isset($request['EMPID_REF_'.$i]) && $request['EMPID_REF_'.$i] !="")){

                $data[$i] = [
                    'EMPID_REF' => trim($request['EMPID_REF_'.$i]),
                    'STARTPD' => trim($request['STARTPD_'.$i]),
                    'ENDPD' => isset($request['ENDPD_'.$i]) && $request['ENDPD_'.$i] !=""?trim($request['ENDPD_'.$i]):NULL,
                    'DEACTIVATED' => isset($request['EMPDEACTIVATED_'.$i]) && $request['EMPDEACTIVATED_'.$i] !=""?trim($request['EMPDEACTIVATED_'.$i]):0,
                    'DODEACTIVATED' => isset($request['EMPDODEACTIVATED_'.$i]) && $request['EMPDODEACTIVATED_'.$i] !=""?trim($request['EMPDODEACTIVATED_'.$i]):NULL,
                    'SUPPERUSER' => isset($request['SUPPERUSER_'.$i]) && $request['SUPPERUSER_'.$i] !=""?trim($request['SUPPERUSER_'.$i]):0,
                ];

                $existData[$i]=trim($request['EMPID_REF_'.$i]);

                if($request['EMPDEACTIVATED_'.$i] !="1"){
                    $deactiveData[]=1;
                }
                
                if($this->existData(trim($request['EMPID_REF_'.$i]),'edit',$request['UDID_'.$i]) > 0){
                    return Response::json(['errors'=>true,'msg' => 'Error:This employee allready allocated.','save'=>'invalid']);
                }

            }
        }

        if(!empty($existData)){
            $counts     = array_count_values($existData);
            $NumVal     = max($counts);

            if( $NumVal > 1){
                return Response::json(['errors'=>true,'msg' => 'Error:Duplicate data','save'=>'invalid']);
            }
        }
        
        if(count($deactiveData) > 1){
            return Response::json(['errors'=>true,'msg' => 'Error:Allow only one active employee.','save'=>'invalid']);
        }

        if(!empty($data)){ 
            $wrapped_links["EMPLOYEE"] = $data; 
            $xml = ArrayToXml::convert($wrapped_links);
        }
        else{
            $xml = NULL; 
        } 

         $UCODE       =   strtoupper(trim($request['UCODE']) );
         $DESCRIPTIONS   =   trim($request['DESCRIPTIONS']);  
         $PASSWORD	    =   trim($request['PASSWORD']);

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
         
       
            $array_data   = [
                $UCODE, $DESCRIPTIONS,
                $DEACTIVATED, $DODEACTIVATED,$PASSWORD,$CYID_REF, 
                $BRID_REF,$FYID_REF,$VTID, $USERID, 
                $UPDATE,$UPTIME, $ACTION, $IPADDRESS,
                $xml
             ];


        try {

        $sp_result = DB::select('EXEC SP_USER_UP ?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?', $array_data);

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
            $USERID     =   Auth::user()->USERID;
            $objResponse = TblMstFrm150::where('USERID','=',$id)->first();

            $objDataResponse = DB::table('TBL_MST_USERDETAILS')                    
            ->where('TBL_MST_USERDETAILS.USERID_REF','=',$objResponse->USERID)
            ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_MST_EMPLOYEE.EMPID','=','TBL_MST_USERDETAILS.EMPID_REF')
            ->select(
                'TBL_MST_USERDETAILS.*',
                'TBL_MST_EMPLOYEE.EMPID',
                'TBL_MST_EMPLOYEE.EMPCODE',
                'TBL_MST_EMPLOYEE.FNAME',
                'TBL_MST_EMPLOYEE.MNAME',
                'TBL_MST_EMPLOYEE.LNAME'
                )
            ->orderBy('TBL_MST_USERDETAILS.UDID','ASC')
            ->get()->toArray();

            $objCount = count($objDataResponse);


            return view('masters.Common.UserMaster.mstfrm150view',compact(['objResponse','objDataResponse','objCount']));
        }

    }
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }

        $objResponse = TblMstFrm150::whereIn('USERID',$ids_data)->get();
        
        return view('masters.Common.UserMaster.mstfrm150print',compact(['objResponse']));
   }//print


   
    

    
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objResponse = TblMstFrm150::where('USERID','=',$id)->first();

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

            return view('masters.Common.UserMaster.mstfrm150attachment',compact(['objResponse','objMstVoucherType','objAttachments']));
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
            $TABLE      =   "TBL_MST_USER";
            $FIELD      =   "USERID";
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

          

   //save data

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_USER";
        $FIELD      =   "USERID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();


        $cancelData[0]= ['NT' =>'TBL_MST_USERDETAILS'];
        $cancel_links["TABLES"] = $cancelData;
        $cancelxml = ArrayToXml::convert($cancel_links);

        $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$cancelxml];

        $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);

        
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


}
