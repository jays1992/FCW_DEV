<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
use Response;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;


class MstFrm161Controller extends Controller
{
   
    protected $form_id = 161;
    protected $vtid_ref   = 302;  //voucher type id

    //validation messages
    protected   $messages = [
                    'MOULD_CODE.required' => 'Required field',
                    'MOULD_CODE.unique' => 'Duplicate Code',
                    'MOULD_DESC.required' => 'Required field'
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
        $objDataList    =   DB::table('TBL_MST_MACHINE_WISE_MOULDINFO')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->get();
                            
        return view('masters.Production.MachineWiseMouldInfo.mstfrm161',compact(['objRights','objDataList','FormId']));

    }



   

    
    public function add(){ 
    
		
		$CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');    
        $FYID_REF   =   Session::get('FYID_REF');

        $Status = 'A';
		
        
         $cur_date = Date('Y-m-d');
         $ObjFuelType = DB::select('select   * from TBL_MST_FUEL_TYPE  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?)  and STATUS = ? and CYID_REF=? order by FUEL_CODE', [$cur_date, $Status, $CYID_REF]);

         $ObjMainUOM = DB::select('select   * from TBL_MST_UOM  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?)  and STATUS = ? and CYID_REF=? order by UOMCODE', [$cur_date, $Status, $CYID_REF]);

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

       
		     
      return view('masters.Production.MachineWiseMouldInfo.mstfrm161add',compact(['ObjFuelType','ObjMainUOM','objDD','objDOCNO']));        
    }

   public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $MOULD_CODE =   $request['MOULD_CODE'];
        
        $objLabel = DB::table('TBL_MST_MACHINE_WISE_MOULDINFO')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('MOULD_CODE','=',$MOULD_CODE)
        ->select('MOULD_CODE')
        ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }

   public function save(Request $request){

       
        $rules = [
            'MOULD_CODE' => 'required',          
            'MOULD_DESC' => 'required',          
        ];

        $req_data = [

            'MOULD_CODE' =>  strtoupper(trim($request['MOULD_CODE']) ),
            'MOULD_DESC' =>  $request['MOULD_DESC'],
           
        ]; 


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
            return Response::json(['errors' => $validator->errors()]);	
        }


        $MACHINEID_REF   =   trim($request['MACHINEID_REF']);  
        
        $MOULD_CODE   =   strtoupper(trim($request['MOULD_CODE']) );
        $MOULD_DESC   =   trim($request['MOULD_DESC']);  
        
        $PRODUCE_ITEMID_REF     =   trim($request['PRODUCE_ITEMID_REF']);  

        $EXP_PRODUCE_QTY        =   trim($request['EXP_PRODUCE_QTY']);  
        $EXP_PRODUCE_UOMID_REF  =   trim($request['EXP_PRODUCE_UOMID_REF']);  

        $PRODUCE_QTY            =   trim($request['PRODUCE_QTY']);  
        $PRODUCE_UOMID_REF      =   trim($request['PRODUCE_UOMID_REF']);  

        $DEACTIVATED    =   0;  
        $DODEACTIVATED  =   null;  

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
                            $MACHINEID_REF,         $MOULD_CODE,    $MOULD_DESC,        $PRODUCE_ITEMID_REF,    $EXP_PRODUCE_QTY,                          
                            $EXP_PRODUCE_UOMID_REF, $PRODUCE_QTY,   $PRODUCE_UOMID_REF, $DEACTIVATED,           $DODEACTIVATED,
                            $CYID_REF,              $BRID_REF,      $FYID_REF,          $VTID,                  $USERID,
                            $UPDATE,                $UPTIME,        $ACTION,            $IPADDRESS                        
                    ];
                    
       
        try {

            $sp_result = DB::select('EXEC SP_MACHINE_WISE_MOULDINFO_IN  ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $array_data);

        
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
            $Status = 'A';        
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

            $objResponse = DB::table('TBL_MST_MACHINE_WISE_MOULDINFO')
                            ->where('MWMOULDID','=',$id)
                            ->select('*')
                            ->first();

            if(!empty($objResponse)){
                if(strtoupper($objResponse->STATUS)=="A" || strtoupper($objResponse->STATUS)=="C"){
                    exit("Sorry, Only Un Approved record can edit.");
                }
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

            $cur_date = Date('Y-m-d');
               
            $ObjTBLMWIH = DB::select('SELECT MWITEMID,MACHINEID_REF FROM TBL_MST_MACHINE_WISE_ITEMINFO_HDR 
                where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?) and CYID_REF = ?  and STATUS = ? AND  MACHINEID_REF=?',  [$cur_date,$CYID_REF,'A',$objResponse->MACHINEID_REF]);    

            $ObjMachine=[];
            if(!empty($ObjTBLMWIH)){
                $ObjMachine =  DB::select('SELECT TOP 1 MACHINEID, MACHINE_NO, MACHINE_DESC FROM TBL_MST_MACHINE  
                        WHERE (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?) and CYID_REF = ? and status=?  AND MACHINEID = ? ', [$cur_date, $CYID_REF, 'A' ,$ObjTBLMWIH[0]->MACHINEID_REF]);    
            }

            $ObjProdItem =  DB::select('SELECT TOP 1 ITEMID,ICODE,NAME FROM TBL_MST_ITEM  
                WHERE  CYID_REF = ? AND ITEMID = ? ', [$CYID_REF, $objResponse->PRODUCE_ITEMID_REF]);    
            

            $ObjEXPUOM =  DB::select('SELECT TOP 1 UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                             WHERE  CYID_REF = ? AND UOMID = ? ', [$CYID_REF, $objResponse->EXP_PRODUCE_UOMID_REF]);
           
            $ObjPRODUOM =  DB::select('SELECT TOP 1 UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                             WHERE  CYID_REF = ? AND UOMID = ? ', [$CYID_REF, $objResponse->PRODUCE_UOMID_REF]);
           
            return view('masters.Production.MachineWiseMouldInfo.mstfrm161edit',compact(['objResponse','user_approval_level','objRights','ObjTBLMWIH','ObjMachine','ObjProdItem','ObjEXPUOM','ObjPRODUOM']));
        }

    }

     
    public function update(Request $request)
    {
            
        $rules = [
            'MOULD_CODE' => 'required',
            'MOULD_CODE' => 'required',          
            'MOULD_DESC' => 'required',          
        ];

        $req_data = [

            'MOULD_CODE'  => strtoupper(trim($request['MOULD_CODE']) ),
            'MOULD_CODE' =>  strtoupper(trim($request['MOULD_CODE']) ),
            'MOULD_DESC' =>  $request['MOULD_DESC'],
        
        ]; 


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
            return Response::json(['errors' => $validator->errors()]);	
        }


        $MACHINEID_REF   =   trim($request['MACHINEID_REF']);  
        
        $MOULD_CODE   =   strtoupper(trim($request['MOULD_CODE']) );
        $MOULD_DESC   =   trim($request['MOULD_DESC']);  
        
        $PRODUCE_ITEMID_REF     =   trim($request['PRODUCE_ITEMID_REF']);  

        $EXP_PRODUCE_QTY        =   trim($request['EXP_PRODUCE_QTY']);  
        $EXP_PRODUCE_UOMID_REF  =   trim($request['EXP_PRODUCE_UOMID_REF']);  

        $PRODUCE_QTY            =   trim($request['PRODUCE_QTY']);  
        $PRODUCE_UOMID_REF      =   trim($request['PRODUCE_UOMID_REF']);  


        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
        $newDateString5 = NULL;
        $newdt5 = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt5) ){
            
            $newdt5 = str_replace( "/", "-",  $newdt5 ) ;
            $newDateString5 = Carbon::parse($newdt5)->format('Y-m-d');        
        }
        $DODEACTIVATED = $newDateString5;

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');    
        $USERID         =   Auth::user()->USERID;   
        $VTID           =   $this->vtid_ref;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "EDIT";
        $IPADDRESS      =   $request->getClientIp();

        $array_data   = [
                $MACHINEID_REF,         $MOULD_CODE,    $MOULD_DESC,        $PRODUCE_ITEMID_REF,    $EXP_PRODUCE_QTY,                          
                $EXP_PRODUCE_UOMID_REF, $PRODUCE_QTY,   $PRODUCE_UOMID_REF, $DEACTIVATED,           $DODEACTIVATED,
                $CYID_REF,              $BRID_REF,      $FYID_REF,          $VTID,                  $USERID,
                $UPDATE,                $UPTIME,        $ACTION,            $IPADDRESS                        
        ];

       
        try {            
            $sp_result =  DB::select('EXEC SP_MACHINE_WISE_MOULDINFO_UP  ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $array_data);
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
        
        $destinationPath = storage_path()."/docs/company".$CYID_REF."/MachinewiseMouldInfo";

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
            return redirect()->route("master",[161,"attachment",$ATTACH_DOCNO])->with("success","The file is already exist");
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

             //save data
             $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);       

     
        if($sp_result[0]->RESULT=="SUCCESS"){

            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }

            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }

            return redirect()->route("master",[161,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[161,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("master",[161,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
            return redirect()->route("master",[161,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   

  


    //singleApprove begin
    public function singleapprove(Request $request)
    {
        
        $rules = [
            'MOULD_CODE' => 'required',
            'MOULD_CODE' => 'required',          
            'MOULD_DESC' => 'required',          
        ];

        $req_data = [

            'MOULD_CODE'  => strtoupper(trim($request['MOULD_CODE']) ),
            'MOULD_CODE' =>  strtoupper(trim($request['MOULD_CODE']) ),
            'MOULD_DESC' =>  $request['MOULD_DESC'],
        
        ]; 


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
            return Response::json(['errors' => $validator->errors()]);	
        }


        $MACHINEID_REF   =   trim($request['MACHINEID_REF']);  
        
        $MOULD_CODE   =   strtoupper(trim($request['MOULD_CODE']) );
        $MOULD_DESC   =   trim($request['MOULD_DESC']);  
        
        $PRODUCE_ITEMID_REF     =   trim($request['PRODUCE_ITEMID_REF']);  

        $EXP_PRODUCE_QTY        =   trim($request['EXP_PRODUCE_QTY']);  
        $EXP_PRODUCE_UOMID_REF  =   trim($request['EXP_PRODUCE_UOMID_REF']);  

        $PRODUCE_QTY            =   trim($request['PRODUCE_QTY']);  
        $PRODUCE_UOMID_REF      =   trim($request['PRODUCE_UOMID_REF']);  


        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
        $newDateString5 = NULL;
        $newdt5 = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt5) ){
            
            $newdt5 = str_replace( "/", "-",  $newdt5 ) ;
            $newDateString5 = Carbon::parse($newdt5)->format('Y-m-d');        
        }
        $DODEACTIVATED = $newDateString5;

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');    
        $USERID         =   Auth::user()->USERID;   
        $VTID           =   $this->vtid_ref;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   trim($request['user_approval_level']);   // user approval level value
        $IPADDRESS      =   $request->getClientIp();

        $array_data   = [
                $MACHINEID_REF,         $MOULD_CODE,    $MOULD_DESC,        $PRODUCE_ITEMID_REF,    $EXP_PRODUCE_QTY,                          
                $EXP_PRODUCE_UOMID_REF, $PRODUCE_QTY,   $PRODUCE_UOMID_REF, $DEACTIVATED,           $DODEACTIVATED,
                $CYID_REF,              $BRID_REF,      $FYID_REF,          $VTID,                  $USERID,
                $UPDATE,                $UPTIME,        $ACTION,            $IPADDRESS                        
        ];


        try {            

            $sp_result =  DB::select('EXEC SP_MACHINE_WISE_MOULDINFO_UP  ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $array_data);    
       
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
            $Status = 'A';        
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

            $objResponse = DB::table('TBL_MST_MACHINE_WISE_MOULDINFO')
                            ->where('MWMOULDID','=',$id)
                            ->select('*')
                            ->first();

            $objRights = DB::table('TBL_MST_USERROLMAP')
                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                ->first();

            $cur_date = Date('Y-m-d');
               

            $ObjMachine =  DB::select('SELECT TOP 1 MACHINEID, MACHINE_NO, MACHINE_DESC FROM TBL_MST_MACHINE  
                        WHERE (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?) and CYID_REF = ? and status=?  AND MACHINEID = ? ', [$cur_date, $CYID_REF, 'A' ,$objResponse->MACHINEID_REF]);    


            $ObjProdItem =  DB::select('SELECT TOP 1 ITEMID,ICODE,NAME FROM TBL_MST_ITEM  
                WHERE  CYID_REF = ? AND ITEMID = ? ', [$CYID_REF, $objResponse->PRODUCE_ITEMID_REF]);    
            

            $ObjEXPUOM =  DB::select('SELECT TOP 1 UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                             WHERE  CYID_REF = ? AND UOMID = ? ', [$CYID_REF, $objResponse->EXP_PRODUCE_UOMID_REF]);
           
            $ObjPRODUOM =  DB::select('SELECT TOP 1 UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                             WHERE  CYID_REF = ? AND UOMID = ? ', [$CYID_REF, $objResponse->PRODUCE_UOMID_REF]);
           
            return view('masters.Production.MachineWiseMouldInfo.mstfrm161view',compact(['objResponse','user_approval_level','objRights','ObjMachine','ObjProdItem','ObjEXPUOM','ObjPRODUOM']));
        }
        

    }
  
    public function printdata(Request $request){
        //
       
   }//print




        //display attachments form
        public function attachment($id){

            if(!is_null($id))
            {
                //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
                
                $objResponse = DB::table('TBL_MST_MACHINE_WISE_MOULDINFO')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('MWMOULDID','=',$id)
                    ->select('*')
                    ->first();                

                
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
    
                     
    
                return view('masters.Production.MachineWiseMouldInfo.mstfrm161attachment',compact(['objResponse','objMstVoucherType','objAttachments']));
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
            $TABLE      =   "TBL_MST_MACHINE_WISE_MOULDINFO";
            $FIELD      =   "MWMOULDID";
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


        //Cancel the data
   public function cancel(Request $request){
     
      
    $id = $request->{0};    
        

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_MACHINE_WISE_MOULDINFO";
        $FIELD      =   "MWMOULDID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $cancelData[0]= ['NT' =>'TBL_MST_MACHINE_WISE_MOULDINFO'];
        $cancel_links["TABLES"] = $cancelData;
        $cancelxml = ArrayToXml::convert($cancel_links);

        $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$cancelxml];

        $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);

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

    public function getmachines(Request $request){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;

        $cur_date = Date('Y-m-d');
                        
        $ObjData = DB::select('
            SELECT TABLE1.*,TABLE2.MACHINEID,TABLE2.MACHINE_NO,TABLE2.MACHINE_DESC from TBL_MST_MACHINE_WISE_ITEMINFO_HDR TABLE1
            LEFT JOIN TBL_MST_MACHINE TABLE2 
            ON TABLE1.MACHINEID_REF = TABLE2.MACHINEID
            where (TABLE1.DEACTIVATED=0 or TABLE1.DEACTIVATED is null or TABLE1.DEACTIVATED=1) AND (TABLE1.DODEACTIVATED is null or TABLE1.DODEACTIVATED>=?) and TABLE1.CYID_REF = ?   and TABLE1.STATUS = ?',  [$cur_date,$CYID_REF,'A']);
                        
        if(!empty($ObjData)){

        foreach ($ObjData as $index=>$dataRow){
            // $row = '';
            // $row = $row.'<tr id="machrow_'.$dataRow->MACHINEID .'"  class="clsmachine"><td width="50%">'.$dataRow->MACHINE_NO;
            // $row = $row.'<input type="hidden" id="txtmachrow_'.$dataRow->MACHINEID.'" data-desc="'.$dataRow->MACHINE_NO .'" data-ccname="'.$dataRow->MACHINE_DESC.'"  data-mwitemid="'.$dataRow->MWITEMID .'" value="'.$dataRow->MACHINEID.'"/></td>';
            // $row = $row.'<td width="50%">'.$dataRow->MACHINE_DESC.'</td>';
            // $row = $row.'</tr>';
            // echo $row;

            $row = '';
            $row = $row.'<tr >
            <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_MACHINEID_REF[]" id="machrow_'.$dataRow->MACHINEID .'"  class="clsmachine" value="'.$dataRow->MACHINEID.'" ></td>
            <td width="39%" class="ROW2">'.$dataRow->MACHINE_NO;
            $row = $row.'<input type="hidden" id="txtmachrow_'.$dataRow->MACHINEID.'" data-desc="'.$dataRow->MACHINE_NO .'" data-ccname="'.$dataRow->MACHINE_DESC.'"  data-mwitemid="'.$dataRow->MWITEMID .'" value="'.$dataRow->MACHINEID.'"/></td>';
            $row = $row.'<td width="39%" class="ROW3">'.$dataRow->MACHINE_DESC.'</td>';
            $row = $row.'</tr>';
            echo $row;
        }
        }else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
        }
        exit();
    }
    public function getMainUOM(Request $request){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $cur_date = Date('Y-m-d');
                        
        $ObjData = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?) and CYID_REF = ? and STATUS = ?', [ $cur_date, $CYID_REF, 'A']);
                        
        if(!empty($ObjData)){

        foreach ($ObjData as $index=>$dataRow){
                // $row = '';
                // $row = $row.'<tr id="MainUOMrow_'.$dataRow->UOMID .'"  class="clsMainUOM"><td width="50%">'.$dataRow->UOMCODE;
                // $row = $row.'<input type="hidden" id="txtMainUOMrow_'.$dataRow->UOMID.'" data-desc="'.$dataRow->UOMCODE."-".$dataRow->DESCRIPTIONS.'"  value="'.$dataRow->UOMID.'"/></td>';
                // $row = $row.'<td width="50%">'.$dataRow->DESCRIPTIONS.'</td>';
                // $row = $row.'</tr>';
                // echo $row;

            $row = '';
            $row = $row.'<tr >
            <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_UOMID_REF[]" id="MainUOMrow_'.$dataRow->UOMID .'"  class="clsMainUOM" value="'.$dataRow->UOMID.'" ></td>
            <td width="39%" class="ROW2">'.$dataRow->UOMCODE;
            $row = $row.'<input type="hidden" id="txtMainUOMrow_'.$dataRow->UOMID.'" data-desc="'.$dataRow->UOMCODE."-".$dataRow->DESCRIPTIONS.'"  value="'.$dataRow->UOMID.'"/></td>';
            $row = $row.'<td width="39%" class="ROW3">'.$dataRow->DESCRIPTIONS.'</td>';
            $row = $row.'</tr>';
            echo $row;
        }
        }else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
        }
        exit();
    }

    public function getproditems(Request $request){

       
        $MWITEMID = $request["MWITEMID"];

        $Status  = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $cur_date = Date('Y-m-d');

        $ObjData = DB::select('SELECT TABLE1.MWITEM_MATID, TABLE1.MWITEMID_REF, TABLE1.PRODUCE_QTY, TABLE2.ITEMID,TABLE2.ICODE,TABLE2.NAME AS ITEM_NAME, TABLE3.UOMID,TABLE3.UOMCODE, TABLE3.DESCRIPTIONS AS UOM_DESC  from  TBL_MST_MACHINE_WISE_ITEMINFO_MAT TABLE1
                    LEFT JOIN TBL_MST_ITEM TABLE2 ON TABLE1.ITEMID_REF = TABLE2.ITEMID
                    LEFT JOIN TBL_MST_UOM  TABLE3 ON TABLE1.UOMID_REF = TABLE3.UOMID
                    where TABLE1.MWITEMID_REF=?  AND (TABLE2.DEACTIVATED=0 or  TABLE2.DEACTIVATED is null or  TABLE2.DEACTIVATED=1) AND (TABLE2.DODEACTIVATED is null or TABLE2.DODEACTIVATED>=?) and TABLE2.CYID_REF = ?   and TABLE2.STATUS =? order by TABLE1.MWITEM_MATID',  [$MWITEMID, $cur_date, $CYID_REF, $Status]);
       

            if(!empty($ObjData)){

            foreach ($ObjData as $index=>$dataRow){

                
                // $row = '';
                // $row = $row.'<tr id="PRODITEMPOPcode_'.$dataRow->ITEMID .'"  class="clsPRODITEMPOP"><td width="50%">'.$dataRow->ICODE;
                // $row = $row.'<input type="hidden" id="txtPRODITEMPOPcode_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'"  data-descdate="'.$dataRow->ITEM_NAME.'"
                // value="'.$dataRow->ITEMID.'" 
                // data-produce_qty="'.$dataRow->PRODUCE_QTY.'" 
                // data-uom_id="'.$dataRow->UOMID.'" 
                // data-uom_desc="'.$dataRow->UOMCODE.'-'.$dataRow->UOM_DESC.'" /></td><td>'.$dataRow->ITEM_NAME.'</td></tr>';
                // echo $row;

                $row = '';
                $row = $row.'<tr >
                <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_PDITEMID_REF[]" id="PRODITEMPOPcode_'.$dataRow->ITEMID .'"  class="clsPRODITEMPOP" value="'.$dataRow->ITEMID.'" ></td>
                <td width="39%" class="ROW2">'.$dataRow->ICODE;
                $row = $row.'<input type="hidden" id="txtPRODITEMPOPcode_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'"  data-descdate="'.$dataRow->ITEM_NAME.'"
                value="'.$dataRow->ITEMID.'" 
                data-produce_qty="'.$dataRow->PRODUCE_QTY.'" 
                data-uom_id="'.$dataRow->UOMID.'" 
                data-uom_desc="'.$dataRow->UOMCODE.'-'.$dataRow->UOM_DESC.'" /></td><td width="39%" class="ROW3">'.$dataRow->ITEM_NAME.'</td></tr>';
                echo $row;

            }
            }else{
                echo '<tr><td colspan="3">Record not found.</td></tr>';
            }
            exit();
    }

} //class