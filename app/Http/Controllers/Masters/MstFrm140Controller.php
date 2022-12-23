<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm140;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;


class MstFrm140Controller extends Controller
{
   
    protected $form_id = 140;
    protected $vtid_ref   = 151;  //voucher type id

    //validation messages
    protected   $messages = [
                    'EFFDATE.required' => 'Required field',
                    'ENDDATE.required' => 'Required field',
                    'FROMCRID_REF.required' => 'Required field',
                    'TOCRID_REF.required' => 'Required field',
                    'FRAMOUNT.required' => 'Required field',
                    'TOAMOUNT.required' => 'Required field'
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
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');  

        $objDataList=DB::select("SELECT * FROM TBL_MST_CRCONVERSION WHERE CYID_REF='$CYID_REF'");

        return view('masters.Accounts.CurrencyConversion.mstfrm140',compact(['objRights','objDataList']));

    }

    

    
    public function add(){
        
     

        $d_currency = DB::table('TBL_MST_COMPANY')
        ->where('STATUS','=','A')
        ->where('CYID','=',Auth::user()->CYID_REF)
        ->select('TBL_MST_COMPANY.CRID_REF')
        ->first();

        $dcurrency = isset($d_currency->CRID_REF) ? $d_currency->CRID_REF:'';
        

        $objCurList = DB::table('TBL_MST_CURRENCY')
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('CRID','CRCODE','CRDESCRIPTION')
        ->get();
		
		$objCurList1 = DB::table('TBL_MST_CURRENCY')
        ->where('STATUS','=','A')
		->where('CRID','!=',$dcurrency)
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('CRID','CRCODE','CRDESCRIPTION')
        ->get();
    
     


        return view('masters.Accounts.CurrencyConversion.mstfrm140add',compact(['objCurList','dcurrency','objCurList1']));
    }

   public function save(Request $request){

        $rules = [
            'EFFDATE' => 'required',
            'FROMCRID_REF' => 'required',
            'TOCRID_REF' => 'required',  
            'FRAMOUNT' => 'required',
            'TOAMOUNT' => 'required',           
        ];

        $req_data = [

            'EFFDATE'     =>    $request['EFFDATE'],
            'ENDDATE' =>   $request['ENDDATE'],
            'FROMCRID_REF' =>   $request['FROMCRID_REF'],
            'TOCRID_REF' =>   $request['TOCRID_REF'],
            'FRAMOUNT' =>   $request['FRAMOUNT'],
            'TOAMOUNT' =>   $request['TOAMOUNT']
        ]; 
       // dd($req_data);


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }
 
        $EFFDATE        =   strtoupper(trim($request['EFFDATE']) );
        $ENDDATE        =   trim($request['ENDDATE']);  
        $FROMCRID_REF   =   trim($request['FROMCRID_REF']);  
        $TOCRID_REF     =   trim($request['TOCRID_REF']);  
        $FRAMOUNT       =   trim($request['FRAMOUNT']);  
        $TOAMOUNT       =   trim($request['TOAMOUNT']);  


        $DEACTIVATED    =   NULL;  
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

        $ExistCRID  =   DB::select("SELECT COUNT(*) AS RECORD
        FROM TBL_MST_CRCONVERSION WHERE TOCRID_REF=$TOCRID_REF AND STATUS <> 'C' ");
        $Record=isset($ExistCRID[0]->RECORD)? $ExistCRID[0]->RECORD:0;
      
        if($Record != "0"){
            return Response::json(['errors'=>true,'msg' => 'Sorry! Conversion for the Currency Type already exist, Kindly cancel and create again','save'=>'invalid']);
        }
        $array_data   = [
                        $EFFDATE, $ENDDATE,
                        $DEACTIVATED, $DODEACTIVATED,$FROMCRID_REF, $TOCRID_REF,
                        $FRAMOUNT, $TOAMOUNT,$CYID_REF, $BRID_REF,
                        $FYID_REF, $VTID, $USERID, $UPDATE,
                        $UPTIME, $ACTION, $IPADDRESS
                    ];
        try {

            $sp_result = DB::select('EXEC SP_CRCONV_IN ?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?', $array_data);
        
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

            $objResponse = TblMstFrm140::where('CRCOID','=',$id)->first();

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first(); 

            $objCurList = DB::table('TBL_MST_CURRENCY')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('CRID','CRCODE','CRDESCRIPTION')
            ->get();
			
			$d_currency = DB::table('TBL_MST_COMPANY')
			->where('STATUS','=','A')
			->where('CYID','=',Auth::user()->CYID_REF)
			->select('TBL_MST_COMPANY.CRID_REF')
			->first();

			$dcurrency = isset($d_currency->CRID_REF) ? $d_currency->CRID_REF:'';
			
			$objCurList1 = DB::table('TBL_MST_CURRENCY')
			->where('STATUS','=','A')
			->where('CRID','!=',$dcurrency)
			->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
			->select('CRID','CRCODE','CRDESCRIPTION')
			->get();
            
            return view('masters.Accounts.CurrencyConversion.mstfrm140edit',compact(['objResponse','user_approval_level','objRights','objCurList','objCurList1']));
        }

    }

     
    public function update(Request $request)
    {

      // dd($request->all());

        $update_rules = [

            'EFFDATE' => 'required',
            'FROMCRID_REF' => 'required',
            'TOCRID_REF' => 'required',  
            'FRAMOUNT' => 'required',
            'TOAMOUNT' => 'required',      
        ];

        $req_update_data = [

            'EFFDATE'     =>    $request['EFFDATE'],
            'ENDDATE' =>   $request['ENDDATE'],
            'FROMCRID_REF' =>   $request['FROMCRID_REF'],
            'TOCRID_REF' =>   $request['TOCRID_REF'],
            'FRAMOUNT' =>   $request['FRAMOUNT'],
            'TOAMOUNT' =>   $request['TOAMOUNT']
        ]; 

       
        $validator = Validator::make( $req_update_data, $update_rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }
    
        $EFFDATE        =   strtoupper(trim($request['EFFDATE']) );
        $ENDDATE        =   trim($request['ENDDATE']);  
        $FROMCRID_REF   =   trim($request['FROMCRID_REF']);  
        $TOCRID_REF     =   trim($request['TOCRID_REF']);  
        $FRAMOUNT       =   trim($request['FRAMOUNT']);  
        $TOAMOUNT       =   trim($request['TOAMOUNT']);  

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
            $EFFDATE, $ENDDATE,
            $DEACTIVATED, $DODEACTIVATED,$FROMCRID_REF, $TOCRID_REF,
            $FRAMOUNT, $TOAMOUNT,$CYID_REF, $BRID_REF,
            $FYID_REF, $VTID, $USERID, $UPDATE,
            $UPTIME, $ACTION, $IPADDRESS
        ];

        try {

            $sp_result = DB::select('EXEC SP_CRCONV_UP ?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?', $array_data);

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
        
        $destinationPath = storage_path()."/docs/company".$CYID_REF."/CurrencyConversionMst";

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
            return redirect()->route("master",[140,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
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
        
        //    return redirect()->route("master",[140,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");
    
      //  }
     
        if($sp_result[0]->RESULT=="SUCCESS"){

            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }

            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }

            return redirect()->route("master",[140,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[140,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("master",[140,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
            return redirect()->route("master",[140,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   

  


   



    //singleApprove begin
    public function singleapprove(Request $request)
    {
      
         $approv_rules = [
 
            'EFFDATE' => 'required',
            'FROMCRID_REF' => 'required',
            'TOCRID_REF' => 'required',  
            'FRAMOUNT' => 'required',
            'TOAMOUNT' => 'required',           
         ];
 
         $req_approv_data = [
 
            'EFFDATE'     =>    $request['EFFDATE'],
            'ENDDATE' =>   $request['ENDDATE'],
            'FROMCRID_REF' =>   $request['FROMCRID_REF'],
            'TOCRID_REF' =>   $request['TOCRID_REF'],
            'FRAMOUNT' =>   $request['FRAMOUNT'],
            'TOAMOUNT' =>   $request['TOAMOUNT']
         ]; 
 
 
         $validator = Validator::make( $req_approv_data, $approv_rules, $this->messages);
 
         if ($validator->fails())
         {
         return Response::json(['errors' => $validator->errors()]);	
         }
     
        $EFFDATE        =   strtoupper(trim($request['EFFDATE']) );
        $ENDDATE        =   trim($request['ENDDATE']);  
        $FROMCRID_REF   =   trim($request['FROMCRID_REF']);  
        $TOCRID_REF     =   trim($request['TOCRID_REF']);  
        $FRAMOUNT       =   trim($request['FRAMOUNT']);  
        $TOAMOUNT       =   trim($request['TOAMOUNT']);  

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
            $EFFDATE, $ENDDATE,
            $DEACTIVATED, $DODEACTIVATED,$FROMCRID_REF, $TOCRID_REF,
            $FRAMOUNT, $TOAMOUNT,$CYID_REF, $BRID_REF,
            $FYID_REF, $VTID, $USERID, $UPDATE,
            $UPTIME, $ACTION, $IPADDRESS
        ];
    
        try {

            $sp_result = DB::select('EXEC SP_CRCONV_UP ?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?', $array_data);

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

            $objResponse = TblMstFrm140::where('CRCOID','=',$id)->first();

            $objCurList = DB::table('TBL_MST_CURRENCY')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('CRID','CRCODE','CRDESCRIPTION')
            ->get();
			
			$d_currency = DB::table('TBL_MST_COMPANY')
			->where('STATUS','=','A')
			->where('CYID','=',Auth::user()->CYID_REF)
			->select('TBL_MST_COMPANY.CRID_REF')
			->first();

			$dcurrency = isset($d_currency->CRID_REF) ? $d_currency->CRID_REF:'';
			
			$objCurList1 = DB::table('TBL_MST_CURRENCY')
			->where('STATUS','=','A')
			->where('CRID','!=',$dcurrency)
			->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
			->select('CRID','CRCODE','CRDESCRIPTION')
			->get();
            

            return view('masters.Accounts.CurrencyConversion.mstfrm140view',compact(['objResponse','objCurList','user_approval_level','objCurList1']));
        }

    }
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }

        $objResponse = TblMstFrm140::whereIn('CRCOID',$ids_data)->get();
        
        return view('masters.Accounts.CurrencyConversion.mstfrm140print',compact(['objResponse']));
   }//print


   
    

    
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objResponse = TblMstFrm140::where('CRCOID','=',$id)->first();

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

            return view('masters.Accounts.CurrencyConversion.mstfrm140attachment',compact(['objResponse','objMstVoucherType','objAttachments']));
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
            $TABLE      =   "TBL_MST_CRCONVERSION";
            $FIELD      =   "CRCOID";
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
        $TABLE      =   "TBL_MST_CRCONVERSION";
        $FIELD      =   "CRCOID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
        
        $canceldata[0]=[
            'NT'  => 'TBL_MST_CRCONVERSION',
       ];        
       $links["TABLES"] = $canceldata; 
       $cancelxml = ArrayToXml::convert($links);

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
