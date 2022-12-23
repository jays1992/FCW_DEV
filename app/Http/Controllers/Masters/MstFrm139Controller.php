<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm139;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;


class MstFrm139Controller extends Controller
{
   
    protected $form_id = 139;
    protected $vtid_ref   = 152;  //voucher type id

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

        $objDataList=DB::select("SELECT TAXID,TTCODE,TTDESCRIPTION,INDATE,DEACTIVATED,DODEACTIVATED,STATUS 
        FROM TBL_MST_TAXTYPE 
        WHERE CYID_REF='$CYID_REF' ORDER BY TAXID DESC");

        return view('masters.Common.TaxType.mstfrm139',compact(['objRights','objDataList']));

    }

    
    public function add(){ 
        
        $objGSTSLList = DB::table('TBL_MST_GENERALLEDGER')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('STATUS','=','A')
                        ->where('GST','=',1)
                        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                        ->select('GLID','GLCODE','GLNAME')
                        ->orderBy('GLCODE','ASC')
                        ->get();

        $objREVSLList = DB::table('TBL_MST_GENERALLEDGER')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('STATUS','=','A')
                        ->where('GST','=',1)
                        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                        ->select('GLID','GLCODE','GLNAME')
                        ->orderBy('GLCODE','ASC')
                        ->get();

        $objSALEGLList = DB::table('TBL_MST_GENERALLEDGER')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('STATUS','=','A')
                        ->where('SALE','=',1)
                        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                        ->select('GLID','GLCODE','GLNAME')
                        ->orderBy('GLCODE','ASC')
                        ->get();

        $objPURGLList = DB::table('TBL_MST_GENERALLEDGER')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('STATUS','=','A')
                        ->where('PURCHASE','=',1)
                        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                        ->select('GLID','GLCODE','GLNAME')
                        ->orderBy('GLCODE','ASC')
                        ->get();

        return view('masters.Common.TaxType.mstfrm139add',compact(['objGSTSLList','objSALEGLList','objPURGLList','objREVSLList']));
    }

    

   public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $TTCODE =   $request['TTCODE'];

        $objLabel = DB::table('TBL_MST_TAXTYPE')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TTCODE','=',$TTCODE)
        ->select('TTCODE')
        ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }

   public function save(Request $request){

       // dd($request->all());

        $TTCODE          = strtoupper(trim($request['TTCODE']) );
        $TTDESCRIPTION   = trim($request['TTDESCRIPTION']);  

        $WITHINSTATE = 0 ;
        $OUTOFSTATE = 0 ;
        $EXPORT = 0 ;
        $FOR_SALE =  0;
        $FOR_PURCHASE = 0;  
        $GST_GLID_REF     = trim($request['GSTGLID_REF']);
        $REVGLID_REF      = trim($request['REVGLID_REF']);
        $TAX_TYPE    = $request["TAX_TYPE"];

        $FOR_TAX = $request["TAX_FOR"];

        if($FOR_TAX == 'SALE_WITHINSTATE' || $FOR_TAX == 'SALE_OUTOFSTATE' || $FOR_TAX == 'SALE_EXPORT'){
            $FOR_SALE = 1;

        }else if($FOR_TAX == 'PURCHASE_WITHINSTATE' || $FOR_TAX ==  'PURCHASE_OUTOFSTATE' || $FOR_TAX == 'PURCHASE_IMPORT'){
            $FOR_PURCHASE = 1;
        }

        switch ($FOR_TAX) {
            case 'SALE_WITHINSTATE':
            case 'PURCHASE_WITHINSTATE':
                $WITHINSTATE=1 ;
                break;
            
            case 'SALE_OUTOFSTATE':
            case 'PURCHASE_OUTOFSTATE':
                $OUTOFSTATE = 1 ;
                break;

            case 'SALE_EXPORT':
            case 'PURCHASE_IMPORT':
                $EXPORT = 1 ;
                break;  
        } //switch

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
                        $TTCODE, $TTDESCRIPTION, $WITHINSTATE, $OUTOFSTATE,
                        $EXPORT, $FOR_SALE, $FOR_PURCHASE, $GST_GLID_REF,
                        $TAX_TYPE,
                        $DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF,
                        $FYID_REF, $VTID, $USERID, $UPDATE,
                        $UPTIME, $ACTION, $IPADDRESS,$REVGLID_REF
                    ];  
                    
        //dd($array_data);            
        //try {

            $sp_result = DB::select('EXEC SP_TAXTYPE_IN ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?,?', $array_data);
           
          //  } catch (\Throwable $th) {
            
          //      return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);

          //  }
    
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

            $status  ='A';
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

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $objGSTSLList = DB::table('TBL_MST_GENERALLEDGER')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('STATUS','=','A')
                        ->where('GST','=',1)
                        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                        ->select('GLID','GLCODE','GLNAME')
                        ->orderBy('GLCODE','ASC')
                        ->get();
            
            $objREVSLList = DB::table('TBL_MST_GENERALLEDGER')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('STATUS','=','A')
                        ->where('GST','=',1)
                        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                        ->select('GLID','GLCODE','GLNAME')
                        ->orderBy('GLCODE','ASC')
                        ->get();

            $objSALEGLList = DB::table('TBL_MST_GENERALLEDGER')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('STATUS','=','A')
                            ->where('SALE','=',1)
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('GLID','GLCODE','GLNAME')
                            ->orderBy('GLCODE','ASC')
                            ->get();

            $objPURGLList = DB::table('TBL_MST_GENERALLEDGER')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('STATUS','=','A')
                            ->where('PURCHASE','=',1)
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('GLID','GLCODE','GLNAME')
                            ->orderBy('GLCODE','ASC')
                            ->get();

            $objResponse = TblMstFrm139::where('TAXID','=',$id)->first();
            if(strtoupper($objResponse->STATUS)=="A" || strtoupper($objResponse->STATUS)=="C"){
                exit("Sorry, Only Un Approved record can edit.");
            }

            $FOR_SALE     = is_null($objResponse->FOR_SALE) || $objResponse->FOR_SALE==0 ? 0 : $objResponse->FOR_SALE;
            $FOR_PURCHASE = is_null($objResponse->FOR_PURCHASE) || $objResponse->FOR_PURCHASE==0 ? 0 : $objResponse->FOR_PURCHASE;

            $WITHINSTATE    = is_null($objResponse->WITHINSTATE) || $objResponse->WITHINSTATE==0 ? 0 : $objResponse->WITHINSTATE;
            $OUTOFSTATE     = is_null($objResponse->OUTOFSTATE) || $objResponse->OUTOFSTATE==0 ? 0 : $objResponse->OUTOFSTATE;
            $EXPORT         = is_null($objResponse->EXPORT) || $objResponse->EXPORT==0 ? 0 : $objResponse->EXPORT;

           
            $objOldGSTSLList  = DB::table('TBL_MST_GENERALLEDGER')
                                    ->where('GLID','=',$objResponse->GST_GLID_REF)
                                    ->select('GLID','GLCODE','GLNAME')
                                    ->first();
            
            $objOldREVSLList  = DB::table('TBL_MST_GENERALLEDGER')
                                    ->where('GLID','=',$objResponse->REVERSE_GLID_REF)
                                    ->select('GLID','GLCODE','GLNAME')
                                    ->first();

            $SALE_GLID_REF_LABEL  = "";
            $SALE_GLID_REF_ID  = "";

            $SALERETURN_GLID_REF_LABEL = "";
            $SALERETURN_GLID_REF_ID = "";

            $PURCHASE_GLID_REF_LABEL = "";
            $PURCHASE_GLID_REF_ID = "";

            $PURCHASERETURN_GLID_REF_LABEL = "";
            $PURCHASERETURN_GLID_REF_ID = "";

            if($FOR_SALE==1){
            
                $OBJ_SALE = DB::table('TBL_MST_GENERALLEDGER')
                                    ->where('GLID','=',$objResponse->SALE_GLID_REF)
                                    ->select('GLID','GLCODE','GLNAME')
                                    ->first();
                if(!empty($OBJ_SALE)){
                    $SALE_GLID_REF_LABEL  = $OBJ_SALE->GLCODE.' - '.$OBJ_SALE->GLNAME;
                    $SALE_GLID_REF_ID  = $OBJ_SALE->GLID;
                }                       

                $OBJ_SALERETURN = DB::table('TBL_MST_GENERALLEDGER')
                                    ->where('GLID','=',$objResponse->SALERETURN_GLID_REF)
                                    ->select('GLID','GLCODE','GLNAME')
                                    ->first();

                if(!empty($OBJ_SALERETURN)){
                    $SALERETURN_GLID_REF_LABEL  = $OBJ_SALERETURN->GLCODE.' - '.$OBJ_SALERETURN->GLNAME;
                    $SALERETURN_GLID_REF_ID  = $OBJ_SALERETURN->GLID;
                }
            }//sale

            if($FOR_PURCHASE==1){
                     $OBJ_PURCHASE = DB::table('TBL_MST_GENERALLEDGER')
                                ->where('GLID','=',$objResponse->PURCHASE_GLID_REF)
                                ->select('GLID','GLCODE','GLNAME')
                                ->first();

                    if(!empty($OBJ_PURCHASE)){
                            $PURCHASE_GLID_REF_LABEL  = $OBJ_PURCHASE->GLCODE.' - '.$OBJ_PURCHASE->GLNAME;
                            $PURCHASE_GLID_REF_ID  = $OBJ_PURCHASE->GLID;
                    }                       

                    $OBJ_PURCHASERETURN = DB::table('TBL_MST_GENERALLEDGER')
                                    ->where('GLID','=',$objResponse->PURCHASERETURN_GLID_REF)
                                    ->select('GLID','GLCODE','GLNAME')
                                    ->first();

                    if(!empty($OBJ_PURCHASERETURN)){
                            $PURCHASERETURN_GLID_REF_LABEL  = $OBJ_PURCHASERETURN->GLCODE.' - '.$OBJ_PURCHASERETURN->GLNAME;
                            $PURCHASERETURN_GLID_REF_ID  = $OBJ_PURCHASERETURN->GLID;
                    }

            }//purchase

            //dump($objOldGSTSLList);
            
            return view('masters.Common.TaxType.mstfrm139edit',compact([
                        'user_approval_level', 'objRights', 'objGSTSLList', 'objSALEGLList', 'objPURGLList','objResponse', 'FOR_SALE', 'FOR_PURCHASE', 'WITHINSTATE','OUTOFSTATE','EXPORT','objOldGSTSLList',
                        'SALE_GLID_REF_LABEL','SALE_GLID_REF_ID','objREVSLList','objOldREVSLList',
                        'SALERETURN_GLID_REF_LABEL','SALERETURN_GLID_REF_ID',
                        'PURCHASE_GLID_REF_LABEL','PURCHASE_GLID_REF_ID',
                        'PURCHASERETURN_GLID_REF_LABEL','PURCHASERETURN_GLID_REF_ID'
                        ]));
        }

    }

     
    public function update(Request $request)
    {
      
        $TTCODE          = strtoupper(trim($request['TTCODE']) );
        $TTDESCRIPTION   = trim($request['TTDESCRIPTION']);  

        $WITHINSTATE = 0 ;
        $OUTOFSTATE = 0 ;
        $EXPORT = 0 ;
        $FOR_SALE =  0;
        $FOR_PURCHASE = 0;  
        $GST_GLID_REF     = trim($request['GSTGLID_REF']);
        $REVGLID_REF      = trim($request['REVGLID_REF']);
        $SALE_GLID_REF    = (isset($request['SALEGLID_REF']) && !is_null($request['SALEGLID_REF']) ) ? $request['SALEGLID_REF'] :NULL;
        $SALERETURN_GLID_REF = ( isset($request['SALERETGLID_REF']) && !is_null($request['SALERETGLID_REF']) ) ?$request['SALERETGLID_REF']:NULL;
        $PURCHASE_GLID_REF    = (isset($request['PURGLID_REF']) && !is_null($request['PURGLID_REF']) ) ? $request['PURGLID_REF'] :NULL;
        $PURCHASERETURN_GLID_REF = ( isset($request['PURRETGLID_REF']) && !is_null($request['PURRETGLID_REF']) ) ?$request['PURRETGLID_REF']:NULL;
        $TAX_TYPE    = $request["TAX_TYPE"];
        $FOR_TAX = $request["TAX_FOR"];

        if($FOR_TAX == 'SALE_WITHINSTATE' || $FOR_TAX == 'SALE_OUTOFSTATE' || $FOR_TAX == 'SALE_EXPORT'){
            $FOR_SALE = 1;

        }else if($FOR_TAX == 'PURCHASE_WITHINSTATE' || $FOR_TAX ==  'PURCHASE_OUTOFSTATE' || $FOR_TAX == 'PURCHASE_IMPORT'){
            $FOR_PURCHASE = 1;
        }

        switch ($FOR_TAX) {
            case 'SALE_WITHINSTATE':
            case 'PURCHASE_WITHINSTATE':
                $WITHINSTATE=1 ;
                break;
            
            case 'SALE_OUTOFSTATE':
            case 'PURCHASE_OUTOFSTATE':
                $OUTOFSTATE = 1 ;
                break;

            case 'SALE_EXPORT':
            case 'PURCHASE_IMPORT':
                $EXPORT = 1 ;
                break;  
        } //switch

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
                        $TTCODE, $TTDESCRIPTION, $WITHINSTATE, $OUTOFSTATE,
                        $EXPORT, $FOR_SALE, $FOR_PURCHASE, $GST_GLID_REF,$TAX_TYPE,
                        $DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF,
                        $FYID_REF, $VTID, $USERID, $UPDATE,
                        $UPTIME, $ACTION, $IPADDRESS,$REVGLID_REF
                    ];  
       //dd($array_data);   

            $sp_result = DB::select('EXEC SP_TAXTYPE_UP ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?,?', $array_data);
           
    
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
        
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','exist'=>'duplicate']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }
        
        exit();           
    } 

    //uploads attachments files
    public function docuploads(Request $request){

        $formData = $request->all();

        $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size = config("erpconst.attachments.max_size") * 1020 * 1024;

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
        
        $destinationPath = storage_path()."/docs/company".$CYID_REF."/TaxType";

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
            return redirect()->route("master",[139,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
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

            return redirect()->route("master",[139,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[139,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("master",[139,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
            return redirect()->route("master",[139,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   

  


    //singleApprove begin
    public function singleapprove(Request $request)
    {
      
        $TTCODE          = strtoupper(trim($request['TTCODE']) );
        $TTDESCRIPTION   = trim($request['TTDESCRIPTION']);  

        $WITHINSTATE = 0 ;
        $OUTOFSTATE = 0 ;
        $EXPORT = 0 ;
        $FOR_SALE =  0;
        $FOR_PURCHASE = 0;  
        $GST_GLID_REF     = trim($request['GSTGLID_REF']);
        $REVGLID_REF      = trim($request['REVGLID_REF']);
        $SALE_GLID_REF    = (isset($request['SALEGLID_REF']) && !is_null($request['SALEGLID_REF']) ) ? $request['SALEGLID_REF'] :NULL;
        $SALERETURN_GLID_REF = ( isset($request['SALERETGLID_REF']) && !is_null($request['SALERETGLID_REF']) ) ?$request['SALERETGLID_REF']:NULL;
        $PURCHASE_GLID_REF    = (isset($request['PURGLID_REF']) && !is_null($request['PURGLID_REF']) ) ? $request['PURGLID_REF'] :NULL;
        $PURCHASERETURN_GLID_REF = ( isset($request['PURRETGLID_REF']) && !is_null($request['PURRETGLID_REF']) ) ?$request['PURRETGLID_REF']:NULL;
        $TAX_TYPE    = $request["TAX_TYPE"];
        $FOR_TAX = $request["TAX_FOR"];

        if($FOR_TAX == 'SALE_WITHINSTATE' || $FOR_TAX == 'SALE_OUTOFSTATE' || $FOR_TAX == 'SALE_EXPORT'){
            $FOR_SALE = 1;

        }else if($FOR_TAX == 'PURCHASE_WITHINSTATE' || $FOR_TAX ==  'PURCHASE_OUTOFSTATE' || $FOR_TAX == 'PURCHASE_IMPORT'){
            $FOR_PURCHASE = 1;
        }

        switch ($FOR_TAX) {
            case 'SALE_WITHINSTATE':
            case 'PURCHASE_WITHINSTATE':
                $WITHINSTATE=1 ;
                break;
            
            case 'SALE_OUTOFSTATE':
            case 'PURCHASE_OUTOFSTATE':
                $OUTOFSTATE = 1 ;
                break;

            case 'SALE_EXPORT':
            case 'PURCHASE_IMPORT':
                $EXPORT = 1 ;
                break;  
        } //switch

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

        $sp_Approvallevel = [
            $USERID, $VTID, $CYID_REF,$BRID_REF,
            $FYID_REF
            
        ];
        
        $sp_listing_result = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?, ?', $sp_Approvallevel);
    
        if(!empty($sp_listing_result))
        {
            foreach ($sp_listing_result as $key=>$approw)
            {  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$approw->LAVELS;
            }
        }
 
        $ACTION     =  $Approvallevel;
        $IPADDRESS  =  $request->getClientIp();

        $array_data   = [
                        $TTCODE, $TTDESCRIPTION, $WITHINSTATE, $OUTOFSTATE,
                        $EXPORT, $FOR_SALE, $FOR_PURCHASE, $GST_GLID_REF,$TAX_TYPE,
                        $DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF,
                        $FYID_REF, $VTID, $USERID, $UPDATE,
                        $UPTIME, $ACTION, $IPADDRESS,$REVGLID_REF
                    ];  
       //dd($array_data);   

        $sp_result = DB::select('EXEC SP_TAXTYPE_UP ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?,?', $array_data);  
                    
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully approved.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
        
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','exist'=>'duplicate']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }

        exit();  

     }  //singleApprove end


    
    public function view($id){
        if(!is_null($id))
        {

            $status  ='A';
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

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $objGSTSLList = DB::table('TBL_MST_GENERALLEDGER')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('STATUS','=','A')
                        ->where('GST','=',1)
                        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                        ->select('GLID','GLCODE','GLNAME')
                        ->orderBy('GLCODE','ASC')
                        ->get();
            
            $objREVSLList = DB::table('TBL_MST_GENERALLEDGER')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('STATUS','=','A')
                        ->where('GST','=',1)
                        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                        ->select('GLID','GLCODE','GLNAME')
                        ->orderBy('GLCODE','ASC')
                        ->get();

            $objSALEGLList = DB::table('TBL_MST_GENERALLEDGER')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('STATUS','=','A')
                            ->where('SALE','=',1)
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('GLID','GLCODE','GLNAME')
                            ->orderBy('GLCODE','ASC')
                            ->get();

            $objPURGLList = DB::table('TBL_MST_GENERALLEDGER')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('STATUS','=','A')
                            ->where('PURCHASE','=',1)
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('GLID','GLCODE','GLNAME')
                            ->orderBy('GLCODE','ASC')
                            ->get();

            $objResponse = TblMstFrm139::where('TAXID','=',$id)->first();

            $FOR_SALE     = is_null($objResponse->FOR_SALE) || $objResponse->FOR_SALE==0 ? 0 : $objResponse->FOR_SALE;
            $FOR_PURCHASE = is_null($objResponse->FOR_PURCHASE) || $objResponse->FOR_PURCHASE==0 ? 0 : $objResponse->FOR_PURCHASE;

            $WITHINSTATE    = is_null($objResponse->WITHINSTATE) || $objResponse->WITHINSTATE==0 ? 0 : $objResponse->WITHINSTATE;
            $OUTOFSTATE     = is_null($objResponse->OUTOFSTATE) || $objResponse->OUTOFSTATE==0 ? 0 : $objResponse->OUTOFSTATE;
            $EXPORT         = is_null($objResponse->EXPORT) || $objResponse->EXPORT==0 ? 0 : $objResponse->EXPORT;

           
            $objOldGSTSLList  = DB::table('TBL_MST_GENERALLEDGER')
                                    ->where('GLID','=',$objResponse->GST_GLID_REF)
                                    ->select('GLID','GLCODE','GLNAME')
                                    ->first();

            $objOldREVSLList  = DB::table('TBL_MST_GENERALLEDGER')
                                    ->where('GLID','=',$objResponse->REVERSE_GLID_REF)
                                    ->select('GLID','GLCODE','GLNAME')
                                    ->first();

            $SALE_GLID_REF_LABEL  = "";
            $SALE_GLID_REF_ID  = "";

            $SALERETURN_GLID_REF_LABEL = "";
            $SALERETURN_GLID_REF_ID = "";

            $PURCHASE_GLID_REF_LABEL = "";
            $PURCHASE_GLID_REF_ID = "";

            $PURCHASERETURN_GLID_REF_LABEL = "";
            $PURCHASERETURN_GLID_REF_ID = "";

            if($FOR_SALE==1){
            
                $OBJ_SALE = DB::table('TBL_MST_GENERALLEDGER')
                                    ->where('GLID','=',$objResponse->SALE_GLID_REF)
                                    ->select('GLID','GLCODE','GLNAME')
                                    ->first();
                if(!empty($OBJ_SALE)){
                    $SALE_GLID_REF_LABEL  = $OBJ_SALE->GLCODE.' - '.$OBJ_SALE->GLNAME;
                    $SALE_GLID_REF_ID  = $OBJ_SALE->GLID;
                }                       

                $OBJ_SALERETURN = DB::table('TBL_MST_GENERALLEDGER')
                                    ->where('GLID','=',$objResponse->SALERETURN_GLID_REF)
                                    ->select('GLID','GLCODE','GLNAME')
                                    ->first();

                if(!empty($OBJ_SALERETURN)){
                    $SALERETURN_GLID_REF_LABEL  = $OBJ_SALERETURN->GLCODE.' - '.$OBJ_SALERETURN->GLNAME;
                    $SALERETURN_GLID_REF_ID  = $OBJ_SALERETURN->GLID;
                }
            }//sale

            if($FOR_PURCHASE==1){
                     $OBJ_PURCHASE = DB::table('TBL_MST_GENERALLEDGER')
                                ->where('GLID','=',$objResponse->PURCHASE_GLID_REF)
                                ->select('GLID','GLCODE','GLNAME')
                                ->first();

                    if(!empty($OBJ_PURCHASE)){
                            $PURCHASE_GLID_REF_LABEL  = $OBJ_PURCHASE->GLCODE.' - '.$OBJ_PURCHASE->GLNAME;
                            $PURCHASE_GLID_REF_ID  = $OBJ_PURCHASE->GLID;
                    }                       

                    $OBJ_PURCHASERETURN = DB::table('TBL_MST_GENERALLEDGER')
                                    ->where('GLID','=',$objResponse->PURCHASERETURN_GLID_REF)
                                    ->select('GLID','GLCODE','GLNAME')
                                    ->first();

                    if(!empty($OBJ_PURCHASERETURN)){
                            $PURCHASERETURN_GLID_REF_LABEL  = $OBJ_PURCHASERETURN->GLCODE.' - '.$OBJ_PURCHASERETURN->GLNAME;
                            $PURCHASERETURN_GLID_REF_ID  = $OBJ_PURCHASERETURN->GLID;
                    }

            }//purchase

            //dump($objOldGSTSLList);
            
            return view('masters.Common.TaxType.mstfrm139view',compact([
                        'user_approval_level', 'objRights', 'objGSTSLList', 'objSALEGLList', 'objPURGLList','objResponse', 'FOR_SALE', 'FOR_PURCHASE', 'WITHINSTATE','OUTOFSTATE','EXPORT','objOldGSTSLList',
                        'SALE_GLID_REF_LABEL','SALE_GLID_REF_ID','objOldREVSLList','objREVSLList',
                        'SALERETURN_GLID_REF_LABEL','SALERETURN_GLID_REF_ID',
                        'PURCHASE_GLID_REF_LABEL','PURCHASE_GLID_REF_ID',
                        'PURCHASERETURN_GLID_REF_LABEL','PURCHASERETURN_GLID_REF_ID'
                        ]));
        }        

    }//view
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }

        $objResponse = TblMstFrm139::whereIn('CITYID',$ids_data)->get();
        
        return view('masters.Common.TaxType.mstfrm139print',compact(['objResponse']));
   }//print


   
    

    
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objResponse = TblMstFrm139::where('TAXID','=',$id)->first();

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

            return view('masters.Common.TaxType.mstfrm139attachment',compact(['objResponse','objMstVoucherType','objAttachments']));
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
            $TABLE      =   "TBL_MST_TAXTYPE";
            $FIELD      =   "TAXID";
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
        $TABLE      =   "TBL_MST_TAXTYPE";
        $FIELD      =   "TAXID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
        
        $canceldata[0]=[
            'NT'  => 'TBL_MST_TAXTYPE',
       ];        
       $links["TABLES"] = $canceldata; 
       $cancelxml = ArrayToXml::convert($links);
        
        
        $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME,$IPADDRESS,$cancelxml ];

        
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
