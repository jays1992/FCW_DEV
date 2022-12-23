<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Helpers\Utils;

class MstFrm144Controller extends Controller
{
   
    protected $form_id = 144;
    protected $vtid_ref   = 153;  //voucher type id

    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

       
    }

   

    public function add(){

        $cyidRef =  Auth::user()->CYID_REF;
        $bridRef = Session::get('BRID_REF');
        $fyidRef = Session::get('FYID_REF');
        $status  ='A';
       
		$objTaxTypeList = DB::table('TBL_MST_TAXTYPE')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('DEACTIVATED','!=',1)
            ->where('STATUS','=','A')
            ->select('TAXID','TTCODE','TTDESCRIPTION')
            ->orderBy('TTCODE','ASC')
            ->get();


      return view('masters.Accounts.HSN.mstfrm144add', compact([
             'objTaxTypeList'
          ]));
       
   }

    public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $HSNCODE =  trim($request['HSNCODE']);
        
        $objLabel = DB::table('TBL_MST_HSN')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('HSNCODE','=',$HSNCODE)
            ->select('HSNCODE')
            ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        exit();
    }

    public function save(Request $request){
       
        $HSNCODE          		=	strtoupper(trim($request['HSNCODE']));     
        $HSNDESCRIPTION         =   trim($request['HSNDESCRIPTION']); 
		
		
        $DEACTIVATED 			= 	'0';
        $DODEACTIVATED 			= 	NULL;
        
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF'); 
          
        $r_count3 = $request['Row_Count3'];
        $normalData = [];
        for ($i=0; $i<=$r_count3; $i++){
            if(isset($request['HDNNOR_TAXID_REF_'.$i]) && trim($request['HDNNOR_TAXID_REF_'.$i])!=""){
                $normalData[$i]['TAXID_REF']   =  $request['HDNNOR_TAXID_REF_'.$i]; 
                $normalData[$i]['NRATE']       = isset( $request['NOR_RATE_'.$i]) &&  (!is_null($request['NOR_RATE_'.$i]) && is_numeric($request['NOR_RATE_'.$i]))? $request['NOR_RATE_'.$i] : '0.00000'; 
                $normalData[$i]['DEACTIVATED']     =  0;
                $normalData[$i]['DODEACTIVATED']       = NULL;
               
            }  
        }

        if(count($normalData)>0){            
            $normalwrapped["NORMAL"] = $normalData;    
            $normal_xml = ArrayToXml::convert($normalwrapped);
            $XMLNORMAL = $normal_xml;
        }else{
            $XMLNORMAL = NULL;
        }

        $r_count4 = $request['Row_Count4'];
        $exceptionalData = [];
        for ($i=0; $i<=$r_count4; $i++){
            if(isset($request['HDNEXC_TAXID_REF_'.$i]) && trim($request['HDNEXC_TAXID_REF_'.$i])!=""){
                $exceptionalData[$i]['TAXID_REF']   =  $request['HDNEXC_TAXID_REF_'.$i]; 
                $exceptionalData[$i]['ERATE']       = isset( $request['EXC_RATE_'.$i]) &&  (!is_null($request['EXC_RATE_'.$i]) && is_numeric($request['EXC_RATE_'.$i]))? $request['EXC_RATE_'.$i] : '0.0'; 
                $exceptionalData[$i]['DEACTIVATED']     =  0;
                $exceptionalData[$i]['DODEACTIVATED']       = NULL;               
            }  
        }

        if(count($exceptionalData)>0){            
            $exceptionalwrapped["EXCEPTIONAL"] = $exceptionalData;    
            $exceptional_xml = ArrayToXml::convert($exceptionalwrapped);
            $XMLEXCEPTIONAL = $exceptional_xml;
        }else{
            $XMLEXCEPTIONAL = NULL;
        }

 
        $VTID 	= $this->vtid_ref;
        $USERID = Auth::user()->USERID;
        $UPDATE ="ADD";
        $UPDATE =  Date('Y-m-d');
        $UPTIME = Date('h:i:s.u');
 
        $ACTION     =   "ADD";
        $IPADDRESS  =   $request->getClientIp();
 
        // @HSNCODE VARCHAR(20),@HSNDESCRIPTION VARCHAR(200),@DEACTIVATED BIT,@DODEACTIVATED DATE,@CYID_REF INT,@BRID_REF INT,    
        // @FYID_REF INT,@XMLNORMAL XML,@XMLEXCEPTIONAL XML,@VTID int,@USERID int,@UPDATE date,@UPTIME time,@ACTION varchar(30),  
        // @IPADDRESS varchar(30)  
		
		$save_data = [
            $HSNCODE,  $HSNDESCRIPTION, $DEACTIVATED,   $DODEACTIVATED,     $CYID_REF, 
            $BRID_REF, $FYID_REF,       $XMLNORMAL,     $XMLEXCEPTIONAL,    $VTID,
            $USERID,   $UPDATE,         $UPTIME,        $ACTION,            $IPADDRESS
        ];
        
       // dump($request->all());
       // dUMP($save_data);
		
		$sp_result = DB::select('EXEC SP_HSN_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,? ', $save_data);
				
    // dd($sp_result);

        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
           
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','resp'=>'duplicate']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }
        
        exit();    
    }
    


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){

        $objRights = DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();

        $CYID_REF   =   Auth::user()->CYID_REF;

        $objDataList=DB::select("SELECT * FROM TBL_MST_HSN 
        WHERE CYID_REF='$CYID_REF' ORDER BY HSNID ");

       return view('masters.Accounts.HSN.mstfrm144',compact(['objRights','objDataList']));
        
    }

     

    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            $objMst = DB::table("TBL_MST_HSN")
                        ->where('HSNID','=',$id)
                        ->select('*')
                        ->first();        

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
                            ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                            ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                            ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                            ->get()->toArray();

                return view('masters.Accounts.HSN.mstfrm144attachment',compact(['objMst','objMstVoucherType','objAttachments']));
        }

    }




    //uploads attachments files
    public function docuploads(Request $request){

        $formData = $request->all();

        $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size = config("erpconst.attachments.max_size") * 1024 * 1024;

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
        
        $destinationPath = "C:/company".$CYID_REF."/vendormst";

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
            return redirect()->route("master",[144,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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

            return redirect()->route("master",[144,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[144,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            return redirect()->route("master",[144,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   


   
   public function edit($id)
   {
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


                $objTaxTypeList = DB::table('TBL_MST_TAXTYPE')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('DEACTIVATED','!=',1)
                    ->where('STATUS','=','A')
                    ->select('TAXID','TTCODE','TTDESCRIPTION')
                    ->orderBy('TTCODE','ASC')
                    ->get();
        

                $objMstResponse = DB::table('TBL_MST_HSN')                    
                    ->where('HSNID','=',$id)
                    ->where('CYID_REF','=',$CYID_REF)
                    ->select('*')
                    ->first();
                if(strtoupper($objMstResponse->STATUS)=="A" || strtoupper($objMstResponse->STATUS)=="C"){
                    exit("Sorry, Only Un Approved record can edit.");
                }    
               
                $objList1 = DB::table('TBL_MST_HSNNORMAL')                    
                ->where('TBL_MST_HSNNORMAL.HSNID_REF','=',$id)
                ->leftJoin('TBL_MST_TAXTYPE','TBL_MST_TAXTYPE.TAXID','=','TBL_MST_HSNNORMAL.TAXID_REF')                
                ->select( 
                    'TBL_MST_HSNNORMAL.HSNNID',
                    'TBL_MST_HSNNORMAL.HSNID_REF',
                    'TBL_MST_HSNNORMAL.TAXID_REF',
                    'TBL_MST_HSNNORMAL.NRATE',
                    'TBL_MST_HSNNORMAL.DEACTIVATED',
                    'TBL_MST_HSNNORMAL.DODEACTIVATED',
                    'TBL_MST_HSNNORMAL.ADDITIONAL1',
                    'TBL_MST_HSNNORMAL.ADDITIONAL2',
                    'TBL_MST_HSNNORMAL.YESNO',
                    'TBL_MST_HSNNORMAL.AMOUNT',
                    'TBL_MST_HSNNORMAL.MEMO',
                    'TBL_MST_TAXTYPE.TAXID',
                    'TBL_MST_TAXTYPE.TTCODE',
                    'TBL_MST_TAXTYPE.TTDESCRIPTION',
                )
                ->orderBy('TBL_MST_HSNNORMAL.HSNNID','ASC')
                ->get()->toArray();
                $objList1Count = count($objList1);
                if($objList1Count==0){
                    $objList1Count=1;
                }


                $objList2 = DB::table('TBL_MST_HSNEXCEPTIONAL')                    
                ->where('TBL_MST_HSNEXCEPTIONAL.HSNID_REF','=',$id)
                ->leftJoin('TBL_MST_TAXTYPE','TBL_MST_TAXTYPE.TAXID','=','TBL_MST_HSNEXCEPTIONAL.TAXID_REF')                
                ->select( 
                    'TBL_MST_HSNEXCEPTIONAL.HSNEID',
                    'TBL_MST_HSNEXCEPTIONAL.HSNID_REF',
                    'TBL_MST_HSNEXCEPTIONAL.TAXID_REF',
                    'TBL_MST_HSNEXCEPTIONAL.ERATE',
                    'TBL_MST_HSNEXCEPTIONAL.DEACTIVATED',
                    'TBL_MST_HSNEXCEPTIONAL.DODEACTIVATED',
                    'TBL_MST_HSNEXCEPTIONAL.ADDITIONAL1',
                    'TBL_MST_HSNEXCEPTIONAL.ADDITIONAL2',
                    'TBL_MST_HSNEXCEPTIONAL.YESNO',
                    'TBL_MST_HSNEXCEPTIONAL.AMOUNT',
                    'TBL_MST_HSNEXCEPTIONAL.MEMO',
                    'TBL_MST_TAXTYPE.TAXID',
                    'TBL_MST_TAXTYPE.TTCODE',
                    'TBL_MST_TAXTYPE.TTDESCRIPTION',
                )
                ->orderBy('TBL_MST_HSNEXCEPTIONAL.HSNEID','ASC')
                ->get()->toArray();
                $objList2Count = count($objList2);
                if($objList2Count==0){
                    $objList2Count=1;
                }
               
                return view('masters.Accounts.HSN.mstfrm144edit', compact(['objMstResponse','objRights','user_approval_level','objTaxTypeList','objList1','objList1Count','objList2','objList2Count' ]));
            }

    }//edit function



    public function update(Request $request)
    {

        $HSNCODE          		=	strtoupper(trim($request['HSNCODE']));     
        $HSNDESCRIPTION         =   trim($request['HSNDESCRIPTION']); 		
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF'); 
          
        $r_count3 = $request['Row_Count3'];
        $normalData = [];
        for ($i=0; $i<=$r_count3; $i++){
            if(isset($request['HDNNOR_TAXID_REF_'.$i]) && trim($request['HDNNOR_TAXID_REF_'.$i])!=""){
                $normalData[$i]['TAXID_REF']   =  $request['HDNNOR_TAXID_REF_'.$i]; 
                $normalData[$i]['NRATE']       = isset( $request['NOR_RATE_'.$i]) &&  (!is_null($request['NOR_RATE_'.$i]) && is_numeric($request['NOR_RATE_'.$i]))? $request['NOR_RATE_'.$i] : '0.00000'; 
                $normalData[$i]['DEACTIVATED']     = isset( $request['NOR_DEACTIVATED_'.$i]) &&  (!is_null($request['NOR_DEACTIVATED_'.$i]) )? $request['NOR_DEACTIVATED_'.$i] : 0;
                
                $newdt = !(is_null($request['NOR_DODEACTIVATED_'.$i]) || empty($request['NOR_DODEACTIVATED_'.$i]) )=="true" ? $request['NOR_DODEACTIVATED_'.$i] : NULL;                 
                if(!is_null($newdt) ){
                    $newdt = str_replace( "/", "-",  $newdt ) ;
                    $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
                    $normalData[$i]['DODEACTIVATED'] = $newDateString;
                }else{
                    $normalData[$i]['DODEACTIVATED'] = NULL;
                }
            }  
        }

        if(count($normalData)>0){            
            $normalwrapped["NORMAL"] = $normalData;    
            $normal_xml = ArrayToXml::convert($normalwrapped);
            $XMLNORMAL = $normal_xml;
        }else{
            $XMLNORMAL = NULL;
        }

        $r_count4 = $request['Row_Count4'];
        $exceptionalData = [];
        for ($i=0; $i<=$r_count4; $i++){
            if(isset($request['HDNEXC_TAXID_REF_'.$i]) && trim($request['HDNEXC_TAXID_REF_'.$i])!=""){
                $exceptionalData[$i]['TAXID_REF']   =  $request['HDNEXC_TAXID_REF_'.$i]; 
                $exceptionalData[$i]['ERATE']       = isset( $request['EXC_RATE_'.$i]) &&  (!is_null($request['EXC_RATE_'.$i]) && is_numeric($request['EXC_RATE_'.$i]))? $request['EXC_RATE_'.$i] : '0.0'; 
                $exceptionalData[$i]['DEACTIVATED'] =  isset( $request['EXC_DEACTIVATED_'.$i]) &&  (!is_null($request['EXC_DEACTIVATED_'.$i]) )? $request['EXC_DEACTIVATED_'.$i] : 0;;

                $newdt = !(is_null($request['EXC_DODEACTIVATED_'.$i]) || empty($request['EXC_DODEACTIVATED_'.$i]) )=="true" ? $request['EXC_DODEACTIVATED_'.$i] : NULL;                 
                if(!is_null($newdt) ){
                    $newdt = str_replace( "/", "-",  $newdt ) ;
                    $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
                    $exceptionalData[$i]['DODEACTIVATED'] = $newDateString;
                }else{
                    $exceptionalData[$i]['DODEACTIVATED'] = NULL;
                }
            }  
        }

        if(count($exceptionalData)>0){            
            $exceptionalwrapped["EXCEPTIONAL"] = $exceptionalData;    
            $exceptional_xml = ArrayToXml::convert($exceptionalwrapped);
            $XMLEXCEPTIONAL = $exceptional_xml;
        }else{
            $XMLEXCEPTIONAL = NULL;
        }

        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;       

        $newDateString = NULL;
        $newdt = !(is_null($request['DODEACTIVATED']) || empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 
        if(!is_null($newdt) ){
            $newdt = str_replace( "/", "-",  $newdt ) ;
            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        $DODEACTIVATED = $newDateString;
        
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF'); 
          
        
        $VTID 	= $this->vtid_ref;
        $USERID = Auth::user()->USERID;
        $UPDATE =  Date('Y-m-d');
        $UPTIME = Date('h:i:s.u');
 
        $ACTION     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();
		
		$save_data = [
            $HSNCODE,  $HSNDESCRIPTION, $DEACTIVATED,   $DODEACTIVATED,     $CYID_REF, 
            $BRID_REF, $FYID_REF,       $XMLNORMAL,     $XMLEXCEPTIONAL,    $VTID,
            $USERID,   $UPDATE,         $UPTIME,        $ACTION,            $IPADDRESS
        ];
        
       // dump($request->all());
       // dump($save_data);
	
       $sp_result = DB::select('EXEC SP_HSN_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,? ', $save_data);
        
        //dd($sp_result );
				
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
           
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','resp'=>'duplicate']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }
        
        exit();    
              
    } // update function


     //singleApprove begin
    public function singleapprove(Request $request)
    {
        $VTID 	= $this->vtid_ref;
        $USERID = Auth::user()->USERID;
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF'); 

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
 
        $HSNCODE          		=	strtoupper(trim($request['HSNCODE']));     
        $HSNDESCRIPTION         =   trim($request['HSNDESCRIPTION']); 		
       
          
        $r_count3 = $request['Row_Count3'];
        $normalData = [];
        for ($i=0; $i<=$r_count3; $i++){
            if(isset($request['HDNNOR_TAXID_REF_'.$i]) && trim($request['HDNNOR_TAXID_REF_'.$i])!=""){
                $normalData[$i]['TAXID_REF']   =  $request['HDNNOR_TAXID_REF_'.$i]; 
                $normalData[$i]['NRATE']       = isset( $request['NOR_RATE_'.$i]) &&  (!is_null($request['NOR_RATE_'.$i]) && is_numeric($request['NOR_RATE_'.$i]))? $request['NOR_RATE_'.$i] : '0.00000'; 
                $normalData[$i]['DEACTIVATED']     = isset( $request['NOR_DEACTIVATED_'.$i]) &&  (!is_null($request['NOR_DEACTIVATED_'.$i]) )? $request['NOR_DEACTIVATED_'.$i] : 0;
                
                $newdt = !(is_null($request['NOR_DODEACTIVATED_'.$i]) || empty($request['NOR_DODEACTIVATED_'.$i]) )=="true" ? $request['NOR_DODEACTIVATED_'.$i] : NULL;                 
                if(!is_null($newdt) ){
                    $newdt = str_replace( "/", "-",  $newdt ) ;
                    $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
                    $normalData[$i]['DODEACTIVATED'] = $newDateString;
                }else{
                    $normalData[$i]['DODEACTIVATED'] = NULL;
                }
            }  
        }

        if(count($normalData)>0){            
            $normalwrapped["NORMAL"] = $normalData;    
            $normal_xml = ArrayToXml::convert($normalwrapped);
            $XMLNORMAL = $normal_xml;
        }else{
            $XMLNORMAL = NULL;
        }

        $r_count4 = $request['Row_Count4'];
        $exceptionalData = [];
        for ($i=0; $i<=$r_count4; $i++){
            if(isset($request['HDNEXC_TAXID_REF_'.$i]) && trim($request['HDNEXC_TAXID_REF_'.$i])!=""){
                $exceptionalData[$i]['TAXID_REF']   =  $request['HDNEXC_TAXID_REF_'.$i]; 
                $exceptionalData[$i]['ERATE']       = isset( $request['EXC_RATE_'.$i]) &&  (!is_null($request['EXC_RATE_'.$i]) && is_numeric($request['EXC_RATE_'.$i]))? $request['EXC_RATE_'.$i] : '0.0'; 
                $exceptionalData[$i]['DEACTIVATED'] =  isset( $request['EXC_DEACTIVATED_'.$i]) &&  (!is_null($request['EXC_DEACTIVATED_'.$i]) )? $request['EXC_DEACTIVATED_'.$i] : 0;;

                $newdt = !(is_null($request['EXC_DODEACTIVATED_'.$i]) || empty($request['EXC_DODEACTIVATED_'.$i]) )=="true" ? $request['EXC_DODEACTIVATED_'.$i] : NULL;                 
                if(!is_null($newdt) ){
                    $newdt = str_replace( "/", "-",  $newdt ) ;
                    $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
                    $exceptionalData[$i]['DODEACTIVATED'] = $newDateString;
                }else{
                    $exceptionalData[$i]['DODEACTIVATED'] = NULL;
                }
            }  
        }

        if(count($exceptionalData)>0){            
            $exceptionalwrapped["EXCEPTIONAL"] = $exceptionalData;    
            $exceptional_xml = ArrayToXml::convert($exceptionalwrapped);
            $XMLEXCEPTIONAL = $exceptional_xml;
        }else{
            $XMLEXCEPTIONAL = NULL;
        }

        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;       

        $newDateString = NULL;
        $newdt = !(is_null($request['DODEACTIVATED']) || empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 
        if(!is_null($newdt) ){
            $newdt = str_replace( "/", "-",  $newdt ) ;
            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        $DODEACTIVATED = $newDateString;
        
        
        $UPDATE =  Date('Y-m-d');
        $UPTIME = Date('h:i:s.u');
 
      	
		$save_data = [
            $HSNCODE,  $HSNDESCRIPTION, $DEACTIVATED,   $DODEACTIVATED,     $CYID_REF, 
            $BRID_REF, $FYID_REF,       $XMLNORMAL,     $XMLEXCEPTIONAL,    $VTID,
            $USERID,   $UPDATE,         $UPTIME,        $ACTION,            $IPADDRESS
        ];
        
       // dump($request->all());
       // dump($save_data);
	
       $sp_result = DB::select('EXEC SP_HSN_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,? ', $save_data);
        
       // dd($sp_result );
                
       // dd($sp_result);
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully approved.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
           
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','resp'=>'duplicate']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }
        
        exit();   

    }//singleApprove end
 
 
    public function view($id){
        if(!is_null($id))
        {
            $status  ='A';
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');

            $objMstResponse = DB::table('TBL_MST_HSN')                    
                ->where('HSNID','=',$id)
                ->where('CYID_REF','=',$CYID_REF)
                ->select('*')
                ->first();
           
            $objList1 = DB::table('TBL_MST_HSNNORMAL')                    
            ->where('TBL_MST_HSNNORMAL.HSNID_REF','=',$id)
            ->leftJoin('TBL_MST_TAXTYPE','TBL_MST_TAXTYPE.TAXID','=','TBL_MST_HSNNORMAL.TAXID_REF')                
            ->select( 
                'TBL_MST_HSNNORMAL.HSNNID',
                'TBL_MST_HSNNORMAL.HSNID_REF',
                'TBL_MST_HSNNORMAL.TAXID_REF',
                'TBL_MST_HSNNORMAL.NRATE',
                'TBL_MST_HSNNORMAL.DEACTIVATED',
                'TBL_MST_HSNNORMAL.DODEACTIVATED',
                'TBL_MST_HSNNORMAL.ADDITIONAL1',
                'TBL_MST_HSNNORMAL.ADDITIONAL2',
                'TBL_MST_HSNNORMAL.YESNO',
                'TBL_MST_HSNNORMAL.AMOUNT',
                'TBL_MST_HSNNORMAL.MEMO',
                'TBL_MST_TAXTYPE.TAXID',
                'TBL_MST_TAXTYPE.TTCODE',
                'TBL_MST_TAXTYPE.TTDESCRIPTION',
            )
            ->orderBy('TBL_MST_HSNNORMAL.HSNNID','ASC')
            ->get()->toArray();
            $objList1Count = count($objList1);
            if($objList1Count==0){
                $objList1Count=1;
            }


            $objList2 = DB::table('TBL_MST_HSNEXCEPTIONAL')                    
            ->where('TBL_MST_HSNEXCEPTIONAL.HSNID_REF','=',$id)
            ->leftJoin('TBL_MST_TAXTYPE','TBL_MST_TAXTYPE.TAXID','=','TBL_MST_HSNEXCEPTIONAL.TAXID_REF')                
            ->select( 
                'TBL_MST_HSNEXCEPTIONAL.HSNEID',
                'TBL_MST_HSNEXCEPTIONAL.HSNID_REF',
                'TBL_MST_HSNEXCEPTIONAL.TAXID_REF',
                'TBL_MST_HSNEXCEPTIONAL.ERATE',
                'TBL_MST_HSNEXCEPTIONAL.DEACTIVATED',
                'TBL_MST_HSNEXCEPTIONAL.DODEACTIVATED',
                'TBL_MST_HSNEXCEPTIONAL.ADDITIONAL1',
                'TBL_MST_HSNEXCEPTIONAL.ADDITIONAL2',
                'TBL_MST_HSNEXCEPTIONAL.YESNO',
                'TBL_MST_HSNEXCEPTIONAL.AMOUNT',
                'TBL_MST_HSNEXCEPTIONAL.MEMO',
                'TBL_MST_TAXTYPE.TAXID',
                'TBL_MST_TAXTYPE.TTCODE',
                'TBL_MST_TAXTYPE.TTDESCRIPTION',
            )
            ->orderBy('TBL_MST_HSNEXCEPTIONAL.HSNEID','ASC')
            ->get()->toArray();
            $objList2Count = count($objList2);
            if($objList2Count==0){
                $objList2Count=1;
            }
           
            return view('masters.Accounts.HSN.mstfrm144view', compact(['objMstResponse','objList1','objList1Count','objList2','objList2Count' ]));
        }       
             
        
    }//view function 
    
  
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

           // dump($req_data);
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
            $TABLE      =   "TBL_MST_HSN";
            $FIELD      =   "HSNID";
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
         $TABLE      =   "TBL_MST_HSN";
         $FIELD      =   "HSNID";
         $ID         =   $id;
         $UPDATE     =   Date('Y-m-d');
         $UPTIME     =   Date('h:i:s.u');
         $IPADDRESS  =   $request->getClientIp();

         $canceldata[0]=[
            'NT'  => 'TBL_MST_HSNNORMAL',
       ];        
         $canceldata[1]=[
            'NT'  => 'TBL_MST_HSNEXCEPTIONAL',
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



    public function amendment($id)  {

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


                $objTaxTypeList = DB::table('TBL_MST_TAXTYPE')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('DEACTIVATED','!=',1)
                    ->where('STATUS','=','A')
                    ->select('TAXID','TTCODE','TTDESCRIPTION')
                    ->orderBy('TTCODE','ASC')
                    ->get();
        

                $objMstResponse = DB::table('TBL_MST_HSN')                    
                    ->where('HSNID','=',$id)
                    ->where('CYID_REF','=',$CYID_REF)
                    ->select('*')
                    ->first();          
               
                $objList1 = DB::table('TBL_MST_HSNNORMAL')                    
                ->where('TBL_MST_HSNNORMAL.HSNID_REF','=',$id)
                ->leftJoin('TBL_MST_TAXTYPE','TBL_MST_TAXTYPE.TAXID','=','TBL_MST_HSNNORMAL.TAXID_REF')                
                ->select( 
                    'TBL_MST_HSNNORMAL.HSNNID',
                    'TBL_MST_HSNNORMAL.HSNID_REF',
                    'TBL_MST_HSNNORMAL.TAXID_REF',
                    'TBL_MST_HSNNORMAL.NRATE',
                    'TBL_MST_HSNNORMAL.DEACTIVATED',
                    'TBL_MST_HSNNORMAL.DODEACTIVATED',
                    'TBL_MST_HSNNORMAL.ADDITIONAL1',
                    'TBL_MST_HSNNORMAL.ADDITIONAL2',
                    'TBL_MST_HSNNORMAL.YESNO',
                    'TBL_MST_HSNNORMAL.AMOUNT',
                    'TBL_MST_HSNNORMAL.MEMO',
                    'TBL_MST_TAXTYPE.TAXID',
                    'TBL_MST_TAXTYPE.TTCODE',
                    'TBL_MST_TAXTYPE.TTDESCRIPTION',
                )
                ->orderBy('TBL_MST_HSNNORMAL.HSNNID','ASC')
                ->get()->toArray();
                $objList1Count = count($objList1);
                if($objList1Count==0){
                    $objList1Count=1;
                }


                $objList2 = DB::table('TBL_MST_HSNEXCEPTIONAL')                    
                ->where('TBL_MST_HSNEXCEPTIONAL.HSNID_REF','=',$id)
                ->leftJoin('TBL_MST_TAXTYPE','TBL_MST_TAXTYPE.TAXID','=','TBL_MST_HSNEXCEPTIONAL.TAXID_REF')                
                ->select( 
                    'TBL_MST_HSNEXCEPTIONAL.HSNEID',
                    'TBL_MST_HSNEXCEPTIONAL.HSNID_REF',
                    'TBL_MST_HSNEXCEPTIONAL.TAXID_REF',
                    'TBL_MST_HSNEXCEPTIONAL.ERATE',
                    'TBL_MST_HSNEXCEPTIONAL.DEACTIVATED',
                    'TBL_MST_HSNEXCEPTIONAL.DODEACTIVATED',
                    'TBL_MST_HSNEXCEPTIONAL.ADDITIONAL1',
                    'TBL_MST_HSNEXCEPTIONAL.ADDITIONAL2',
                    'TBL_MST_HSNEXCEPTIONAL.YESNO',
                    'TBL_MST_HSNEXCEPTIONAL.AMOUNT',
                    'TBL_MST_HSNEXCEPTIONAL.MEMO',
                    'TBL_MST_TAXTYPE.TAXID',
                    'TBL_MST_TAXTYPE.TTCODE',
                    'TBL_MST_TAXTYPE.TTDESCRIPTION',
                )
                ->orderBy('TBL_MST_HSNEXCEPTIONAL.HSNEID','ASC')
                ->get()->toArray();
                $objList2Count = count($objList2);
                if($objList2Count==0){
                    $objList2Count=1;
                }

                $objHSN = DB::table('TBL_MST_HSN')
                             ->where('CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('BRID_REF','=',Session::get('BRID_REF'))
                             ->where('HSNID','=',$id)
                             ->first();

                $MAXHSN_NO=NULL;
                if(isset($objHSN->HSNID) && $objHSN->HSNID !=""){
                    $objHSNNO = DB::SELECT("select  MAX(isnull(ANO,0))+1  AS ANO from TBL_MST_HSN  WHERE HSNID=? AND HSNCODE=?",[$objHSN->HSNID,$objHSN->HSNCODE]);
                    $MAXHSN_NO = $objHSNNO[0]->ANO;
                }
               
                return view('masters.Accounts.HSN.mstfrm144amendment', compact(['objMstResponse','objHSN','MAXHSN_NO','objRights','user_approval_level','objTaxTypeList','objList1','objList1Count','objList2','objList2Count' ]));
            }

    }




    
    public function saveamendment(Request $request){
       
        $HSNCODE          		=	strtoupper(trim($request['HSNCODE']))?trim($request['HSNCODE']):NULL;
        $HSNDESCRIPTION         =   trim($request['HSNDESCRIPTION'])?trim($request['HSNDESCRIPTION']):NULL;
        $ANO                    =   trim($request['HSN_NO'])?trim($request['HSN_NO']):NULL;
        $AMENDMENT_DATE         =   trim($request['HSN_DT'])?trim($request['HSN_DT']):NULL;
        $AMENDMENT_REASON       =   trim($request['CUSTOMERAREFNO'])?trim($request['CUSTOMERAREFNO']):NULL;
		
        $DEACTIVATED 			= 	'0';
        $DODEACTIVATED 			= 	NULL;
        
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF'); 
          
        $r_count3 = $request['Row_Count3'];
        $normalData = [];
        for ($i=0; $i<=$r_count3; $i++){
            if(isset($request['HDNNOR_TAXID_REF_'.$i]) && trim($request['HDNNOR_TAXID_REF_'.$i])!=""){
                $normalData[$i]['TAXID_REF']   =  $request['HDNNOR_TAXID_REF_'.$i]; 
                $normalData[$i]['NRATE']       = isset( $request['NOR_RATE_'.$i]) &&  (!is_null($request['NOR_RATE_'.$i]) && is_numeric($request['NOR_RATE_'.$i]))? $request['NOR_RATE_'.$i] : '0.00000'; 
                $normalData[$i]['DEACTIVATED']     =  0;
                $normalData[$i]['DODEACTIVATED']       = NULL;
               
            }  
        }

        if(count($normalData)>0){            
            $normalwrapped["NORMAL"] = $normalData;    
            $normal_xml = ArrayToXml::convert($normalwrapped);
            $XMLNORMAL = $normal_xml;
        }else{
            $XMLNORMAL = NULL;
        }

        $r_count4 = $request['Row_Count4'];
        $exceptionalData = [];
        for ($i=0; $i<=$r_count4; $i++){
            if(isset($request['HDNEXC_TAXID_REF_'.$i]) && trim($request['HDNEXC_TAXID_REF_'.$i])!=""){
                $exceptionalData[$i]['TAXID_REF']   =  $request['HDNEXC_TAXID_REF_'.$i]; 
                $exceptionalData[$i]['ERATE']       = isset( $request['EXC_RATE_'.$i]) &&  (!is_null($request['EXC_RATE_'.$i]) && is_numeric($request['EXC_RATE_'.$i]))? $request['EXC_RATE_'.$i] : '0.0'; 
                $exceptionalData[$i]['DEACTIVATED']     =  0;
                $exceptionalData[$i]['DODEACTIVATED']       = NULL;               
            }  
        }

        if(count($exceptionalData)>0){            
            $exceptionalwrapped["EXCEPTIONAL"] = $exceptionalData;    
            $exceptional_xml = ArrayToXml::convert($exceptionalwrapped);
            $XMLEXCEPTIONAL = $exceptional_xml;
        }else{
            $XMLEXCEPTIONAL = NULL;
        }

 
        $VTID 	= $this->vtid_ref;
        $USERID = Auth::user()->USERID;
        $UPDATE ="ADD";
        $UPDATE =  Date('Y-m-d');
        $UPTIME = Date('h:i:s.u');
 
        $ACTION     =   "ADD";
        $IPADDRESS  =   $request->getClientIp();
 
        // @HSNCODE VARCHAR(20),@HSNDESCRIPTION VARCHAR(200),@DEACTIVATED BIT,@DODEACTIVATED DATE,@CYID_REF INT,@BRID_REF INT,    
        // @FYID_REF INT,@XMLNORMAL XML,@XMLEXCEPTIONAL XML,@VTID int,@USERID int,@UPDATE date,@UPTIME time,@ACTION varchar(30),  
        // @IPADDRESS varchar(30)  
		
		$save_data = [
            $HSNCODE,  $HSNDESCRIPTION, $DEACTIVATED,   $DODEACTIVATED,     $CYID_REF, 
            $BRID_REF, $FYID_REF,       $XMLNORMAL,     $XMLEXCEPTIONAL,    $VTID,
            $USERID,   $UPDATE,         $UPTIME,        $ACTION,            $IPADDRESS,
            $ANO,      $AMENDMENT_DATE, $AMENDMENT_REASON
        ];       

        //dd($save_data);

       // dump($request->all());
       // dUMP($save_data);
		
		$sp_result = DB::select('EXEC SP_HSN_AMENDMENT ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $save_data);
				
        //dd($sp_result);

        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
           
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','resp'=>'duplicate']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }
        
        exit();    
    }

   


}
