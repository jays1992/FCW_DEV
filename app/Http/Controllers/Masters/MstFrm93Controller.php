<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm93;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;


class MstFrm93Controller extends Controller
{
   
    protected $form_id = 93;
    protected $vtid_ref   = 137;  //voucher type id

    //validation messages
    protected   $messages = [
                    'GLCODE.required' => 'Required field',
                    'GLCODE.unique' => 'Duplicate Code',
                    'GLNAME.required' => 'Required field'
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
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first(); 

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Auth::user()->BRID_REF;
        $FYID_REF   =   Auth::user()->FYID_REF;  

        $objDataList=DB::select("SELECT A.GLID,A.GLCODE,A.GLNAME,A.ALIAS,A.INDATE,A.DEACTIVATED,A.DODEACTIVATED,A.STATUS,
                                B.ASGNAME,C.AGNAME,D.NOGNAME,E.NOG_TYPE
                                FROM TBL_MST_GENERALLEDGER A LEFT JOIN TBL_MST_ACCOUNTSUBGROUP B ON A.ASGID_REF = B.ASGID
                                LEFT JOIN TBL_MST_ACCOUNTGROUP C ON B.AGID_REF = C.AGID
                                LEFT JOIN TBL_MST_NATUREOFGROUP D ON C.NOGID_REF = D.NOGID
                                LEFT JOIN TBL_MST_NATUREOFGROUP_TYPE E ON D.NOG_TYPE = E.NOGTID
                                WHERE A.CYID_REF='$CYID_REF'");

        return view('masters.Accounts.GENERALLEDGER.mstfrm93',compact(['objRights','objDataList']));

    }
    
    public function add(){ 

        $objAccountSubGroupList = DB::table('TBL_MST_ACCOUNTSUBGROUP')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('ASGID','ASGCODE','ASGNAME')
        ->get();
		     
        return view('masters.Accounts.GENERALLEDGER.mstfrm93add',compact(['objAccountSubGroupList']));

    }

   public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $GLCODE =   $request['GLCODE'];
        
        $objLabel = DB::table('TBL_MST_GENERALLEDGER')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('GLCODE','=',$GLCODE)
        ->select('GLCODE')
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
            'GLCODE' => 'required|unique:TBL_MST_GENERALLEDGER',
            'GLNAME' => 'required',  
            'ASGID_REF' => 'required'        
        ];

        $req_data = [

            'GLCODE'     =>    $request['GLCODE'],
            'GLNAME' =>   $request['GLNAME'],
            'ASGID_REF' =>   $request['ASGID_REF']
        ]; 


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }
 
        $GLCODE         =   strtoupper(trim($request['GLCODE']) );
        $GLNAME         =   trim($request['GLNAME']);
        $ALIAS          =   (isset($request['ALIAS']) && trim($request['ALIAS']) !=""  )? trim($request['ALIAS']) : NULL ;
        $ASGID_REF       =   trim($request['ASGID_REF']);
        $CC             =   (isset($request['CC']) && trim($request['CC']) !="" )? trim($request['CC']) : NULL ;
        $SUBLEDGER      =   (isset($request['SUBLEDGER']) && trim($request['SUBLEDGER']) !=""  )? trim($request['SUBLEDGER']) : NULL ;
        $BANKAC         =   (isset($request['BANKAC']) && trim($request['BANKAC']) !=""  )? trim($request['BANKAC']) : NULL ;
        $GST            =   (isset($request['GST']) && trim($request['GST']) !=""  )? trim($request['GST']) : NULL ;
        $GST_ON_THISGL  =   (isset($request['GST_ON_THISGL']) && trim($request['GST_ON_THISGL']) !=""  )? trim($request['GST_ON_THISGL']) : NULL ;
        $TDS            =   (isset($request['TDS']) && trim($request['TDS']) !=""  )? trim($request['TDS']) : NULL ;
        $IVAFFECTED     =   (isset($request['IVAFFECTED']) && trim($request['IVAFFECTED']) !=""  )? trim($request['IVAFFECTED']) : NULL ;
        $ICALCULATION   =   (isset($request['ICALCULATION']) && trim($request['ICALCULATION']) !=""  )? trim($request['ICALCULATION']) : NULL ;
        $UPAYROLL       =   (isset($request['UPAYROLL']) && trim($request['UPAYROLL']) !=""  )? trim($request['UPAYROLL']) : NULL ;
        $VAT            =   (isset($request['VAT']) && trim($request['VAT']) !=""  )? trim($request['VAT']) : NULL ;
        $TAX            =   (isset($request['TAX']) && trim($request['TAX']) !=""  )? trim($request['TAX']) : NULL ;
        $SALE           =   (isset($request['SALE']) && trim($request['SALE']) !=""  )? trim($request['SALE']) : NULL ;
        $PURCHASE       =   (isset($request['PURCHASE']) && trim($request['PURCHASE']) !=""  )? trim($request['PURCHASE']) : NULL ;
        $TCS            =   (isset($request['TCS']) && trim($request['TCS']) !=""  )? trim($request['TCS']) : NULL ;
     

        $DEACTIVATED    =   0;  
        $DODEACTIVATED  =   NULL;  

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
                        $GLCODE, $GLNAME,$ALIAS,$ASGID_REF,
                        $DEACTIVATED, $DODEACTIVATED,$CC,$SUBLEDGER,
                        $BANKAC, $GST,$GST_ON_THISGL,$TDS,
                        $IVAFFECTED, $ICALCULATION,$UPAYROLL,$VAT,
                        $TAX,$SALE,$PURCHASE, $TCS,  
                        $CYID_REF, $BRID_REF,$FYID_REF,$VTID,
                        $USERID, $UPDATE,$UPTIME, $ACTION, 
                        $IPADDRESS
                        
                    ];

        try {

            $sp_result = DB::select('EXEC SP_GENERALLEDGER_in ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?,  ?,?,?,?, ?,?,?,?, ?', $array_data);
        
             } catch (\Throwable $th) {
            
                 return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);

           }
           
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
        
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','exist'=>'duplicate']);
            
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

            $objResponse = TblMstFrm93::where('GLID','=',$id)->first();
            if(strtoupper($objResponse->STATUS)=="A" || strtoupper($objResponse->STATUS)=="C"){
                exit("Sorry, Only Un Approved record can edit.");
            }

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first(); 

            
            $objAccountSubGroupList = DB::table('TBL_MST_ACCOUNTSUBGROUP')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('ASGID','ASGCODE','ASGNAME')
            ->get();

            $objAsgName = DB::table('TBL_MST_ACCOUNTSUBGROUP')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('STATUS','=','A')
            ->where('ASGID','=',$objResponse->ASGID_REF)
            ->select('ASGCODE','ASGNAME')
            ->first();
            
            return view('masters.Accounts.GENERALLEDGER.mstfrm93edit',
                    compact(['objResponse','user_approval_level','objRights','objAccountSubGroupList','objAsgName']));
        }

    }

     
    public function update(Request $request)
    {

      // dd($request->all());

        $update_rules = [

            'GLNAME' => 'required',  
            'ASGID_REF' => 'required'
                   
        ];

        $req_update_data = [

            'GLNAME' =>   $request['GLNAME'],
            'ASGID_REF' =>   $request['ASGID_REF']
        ]; 

       
        $validator = Validator::make( $req_update_data, $update_rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }
    
        $GLCODE         =   strtoupper(trim($request['GLCODE']) );
        $GLNAME         =   trim($request['GLNAME']); 
        $ALIAS          =   (isset($request['ALIAS']) && trim($request['ALIAS']) !=""  )? trim($request['ALIAS']) : NULL ;
        $ASGID_REF       =   trim($request['ASGID_REF']);
        $CC             =   (isset($request['CC']) && trim($request['CC']) !="" )? trim($request['CC']) : NULL ;
        $SUBLEDGER      =   (isset($request['SUBLEDGER']) && trim($request['SUBLEDGER']) !=""  )? trim($request['SUBLEDGER']) : NULL ;
        $BANKAC         =   (isset($request['BANKAC']) && trim($request['BANKAC']) !=""  )? trim($request['BANKAC']) : NULL ;
        $GST            =   (isset($request['GST']) && trim($request['GST']) !=""  )? trim($request['GST']) : NULL ;
        $GST_ON_THISGL  =   (isset($request['GST_ON_THISGL']) && trim($request['GST_ON_THISGL']) !=""  )? trim($request['GST_ON_THISGL']) : NULL ;
        $TDS            =   (isset($request['TDS']) && trim($request['TDS']) !=""  )? trim($request['TDS']) : NULL ;
        $IVAFFECTED     =   (isset($request['IVAFFECTED']) && trim($request['IVAFFECTED']) !=""  )? trim($request['IVAFFECTED']) : NULL ;
        $ICALCULATION   =   (isset($request['ICALCULATION']) && trim($request['ICALCULATION']) !=""  )? trim($request['ICALCULATION']) : NULL ;
        $UPAYROLL       =   (isset($request['UPAYROLL']) && trim($request['UPAYROLL']) !=""  )? trim($request['UPAYROLL']) : NULL ;
        $VAT            =   (isset($request['VAT']) && trim($request['VAT']) !=""  )? trim($request['VAT']) : NULL ;
        $TAX            =   (isset($request['TAX']) && trim($request['TAX']) !=""  )? trim($request['TAX']) : NULL ;
        $SALE           =   (isset($request['SALE']) && trim($request['SALE']) !=""  )? trim($request['SALE']) : NULL ;
        $PURCHASE       =   (isset($request['PURCHASE']) && trim($request['PURCHASE']) !=""  )? trim($request['PURCHASE']) : NULL ;
        $TCS            =   (isset($request['TCS']) && trim($request['TCS']) !=""  )? trim($request['TCS']) : NULL ;
     

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
            $GLCODE, $GLNAME,$ALIAS,$ASGID_REF,
            $DEACTIVATED, $DODEACTIVATED,$CC,$SUBLEDGER,
            $BANKAC, $GST,$GST_ON_THISGL,$TDS,
            $IVAFFECTED, $ICALCULATION,$UPAYROLL,$VAT,
            $TAX,$SALE,$PURCHASE, $TCS,  
            $CYID_REF, $BRID_REF,$FYID_REF,$VTID,
            $USERID, $UPDATE,$UPTIME, $ACTION, 
            $IPADDRESS
            
        ];


        try {

        $sp_result = DB::select('EXEC SP_GENERALLEDGER_up ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?,  ?,?,?,?, ?,?,?,?, ?', $array_data);

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
        
        $destinationPath = "E:/company".$CYID_REF."/generalledgermst";

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
            return redirect()->route("master",[93,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
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

            return redirect()->route("master",[93,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[93,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            return redirect()->route("master",[93,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   

  


   



    //singleApprove begin
    public function singleapprove(Request $request)
    {
      
         $approv_rules = [
 
            'GLNAME' => 'required',  
            'ASGID_REF' => 'required'         
         ];
 
         $req_approv_data = [
 
            'GLNAME' =>   $request['GLNAME'],
            'ASGID_REF' =>   $request['ASGID_REF']
         ]; 
 
 
         $validator = Validator::make( $req_approv_data, $approv_rules, $this->messages);
 
         if ($validator->fails())
         {
         return Response::json(['errors' => $validator->errors()]);	
         }
     
        $GLCODE         =   strtoupper(trim($request['GLCODE']) );
        $GLNAME         =   trim($request['GLNAME']); 
        $ALIAS          =   (isset($request['ALIAS']) && trim($request['ALIAS']) !=""  )? trim($request['ALIAS']) : NULL ;
        $ASGID_REF       =   trim($request['ASGID_REF']);
        $CC             =   (isset($request['CC']) && trim($request['CC']) !="" )? trim($request['CC']) : NULL ;
        $SUBLEDGER      =   (isset($request['SUBLEDGER']) && trim($request['SUBLEDGER']) !=""  )? trim($request['SUBLEDGER']) : NULL ;
        $BANKAC         =   (isset($request['BANKAC']) && trim($request['BANKAC']) !=""  )? trim($request['BANKAC']) : NULL ;
        $GST            =   (isset($request['GST']) && trim($request['GST']) !=""  )? trim($request['GST']) : NULL ;
        $GST_ON_THISGL  =   (isset($request['GST_ON_THISGL']) && trim($request['GST_ON_THISGL']) !=""  )? trim($request['GST_ON_THISGL']) : NULL ;
        $TDS            =   (isset($request['TDS']) && trim($request['TDS']) !=""  )? trim($request['TDS']) : NULL ;
        $IVAFFECTED     =   (isset($request['IVAFFECTED']) && trim($request['IVAFFECTED']) !=""  )? trim($request['IVAFFECTED']) : NULL ;
        $ICALCULATION   =   (isset($request['ICALCULATION']) && trim($request['ICALCULATION']) !=""  )? trim($request['ICALCULATION']) : NULL ;
        $UPAYROLL       =   (isset($request['UPAYROLL']) && trim($request['UPAYROLL']) !=""  )? trim($request['UPAYROLL']) : NULL ;
        $VAT            =   (isset($request['VAT']) && trim($request['VAT']) !=""  )? trim($request['VAT']) : NULL ;
        $TAX            =   (isset($request['TAX']) && trim($request['TAX']) !=""  )? trim($request['TAX']) : NULL ;
        $SALE           =   (isset($request['SALE']) && trim($request['SALE']) !=""  )? trim($request['SALE']) : NULL ;
        $PURCHASE       =   (isset($request['PURCHASE']) && trim($request['PURCHASE']) !=""  )? trim($request['PURCHASE']) : NULL ;
        $TCS            =   (isset($request['TCS']) && trim($request['TCS']) !=""  )? trim($request['TCS']) : NULL ;
     
         
         


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
                $GLCODE, $GLNAME,$ALIAS,$ASGID_REF,
                $DEACTIVATED, $DODEACTIVATED,$CC,$SUBLEDGER,
                $BANKAC, $GST,$GST_ON_THISGL,$TDS,
                $IVAFFECTED, $ICALCULATION,$UPAYROLL,$VAT,
                $TAX,$SALE,$PURCHASE, $TCS,  
                $CYID_REF, $BRID_REF,$FYID_REF,$VTID,
                $USERID, $UPDATE,$UPTIME, $ACTION, 
                $IPADDRESS
                
            ];
    

        try {

        $sp_result = DB::select('EXEC SP_GENERALLEDGER_up ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?,  ?,?,?,?, ?,?,?,?, ?', $array_data);

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
            $objResponse = TblMstFrm93::where('GLID','=',$id)->first();
            $objAccountSubGroupList = DB::table('TBL_MST_ACCOUNTSUBGROUP')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('ASGID','ASGCODE','ASGNAME')
            ->get();

            $objAsgName = DB::table('TBL_MST_ACCOUNTSUBGROUP')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('STATUS','=','A')
            ->where('ASGID','=',$objResponse->ASGID_REF)
            ->select('ASGCODE','ASGNAME')
            ->first();
		     

            return view('masters.Accounts.GENERALLEDGER.mstfrm93view',compact(['objResponse','objAccountSubGroupList','objAsgName']));
        }

    }
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }

        $objResponse = TblMstFrm93::whereIn('GLID',$ids_data)->get();
        
        return view('masters.Accounts.GENERALLEDGER.mstfrm93print',compact(['objResponse']));
   }//print


   
    

    
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objResponse = TblMstFrm93::where('GLID','=',$id)->first();

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
                        ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                        ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                        ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                        ->get()->toArray();

                 // dump( $objAttachments);

            return view('masters.Accounts.GENERALLEDGER.mstfrm93attachment',compact(['objResponse','objMstVoucherType','objAttachments']));
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
            $TABLE      =   "TBL_MST_GENERALLEDGER";
            $FIELD      =   "GLID";
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
        $TABLE      =   "TBL_MST_GENERALLEDGER";
        $FIELD      =   "GLID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $canceldata[0]=[
            'NT'  => 'TBL_MST_GENERALLEDGER',
       ];        
       $links["TABLES"] = $canceldata; 
       $cancelxml = ArrayToXml::convert($links);
        
        
        $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS, $cancelxml ];

        
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
