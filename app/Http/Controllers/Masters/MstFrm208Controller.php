<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm208;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;


class MstFrm208Controller extends Controller
{
   
    protected $form_id = 208;
    protected $vtid_ref   = 249;  //voucher type id

    
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

       // dd($objRights);

        $objDataList = DB::table('TBL_MST_MODULE_VOUCHER_MAP')
            ->where('TBL_MST_MODULE_VOUCHER_MAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_MODULE_VOUCHER_MAP.BRID_REF','=',Session::get('BRID_REF'))
            ->leftJoin('TBL_MST_MODULE',   'TBL_MST_MODULE.MODULEID','=',   'TBL_MST_MODULE_VOUCHER_MAP.MODULEID_REF')
            ->leftJoin('TBL_MST_VOUCHERTYPE',   'TBL_MST_VOUCHERTYPE.VTID','=',   'TBL_MST_MODULE_VOUCHER_MAP.VTID_REF')
            ->select('TBL_MST_MODULE_VOUCHER_MAP.*', 'TBL_MST_MODULE.MODULEID','TBL_MST_MODULE.MODULECODE','TBL_MST_MODULE.MODULENAME','TBL_MST_VOUCHERTYPE.VTID','TBL_MST_VOUCHERTYPE.VCODE','TBL_MST_VOUCHERTYPE.DESCRIPTIONS')
            ->orderBy('TBL_MST_MODULE.MODULENAME', 'ASC')
            ->get();    

          // dd($objDataList);

        return view('masters.Module.ModuleVoucherMapping.mstfrm208',compact(['objRights','objDataList']));

    }

      
    public function add(){ 

        return view('masters.Module.ModuleVoucherMapping.mstfrm208add');   
    }


   public function save(Request $request){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');       
        $VTID           =   $this->vtid_ref;

        //dump($request->all());

        $data     =array();
        $existData=array();
        $r_count = $request['Row_Count'];


        for ($i=0; $i<=$r_count; $i++){

            if((isset($request['LISTPOP1ID_'.$i]) && $request['LISTPOP1ID_'.$i] !="")){

                $idval = trim($request['LISTPOP1ID_'.$i]);
                $headingval = trim($request['VHEADING_'.$i]);

                $data[$i] = [
                    'VTID_REF' => $idval,
                    'HEADING' =>  $headingval,
                    'DEACTIVATED' => 0,
                    'DODEACTIVATED' =>NULL,
                    'REMARKS' => trim($request['REMARKS_'.$i]),
                ];

                $existData[$i]=strtoupper($idval.'-'.$headingval);

            }
        }

        if(!empty($existData)){
            $counts     = array_count_values($existData);
            $NumVal     = max($counts);

            if( $NumVal > 1){
                return Response::json(['errors'=>true,'msg' => 'Duplicate row. Please check.','save'=>'invalid']);
            }
        }

        
        if(!empty($data)){ 
            $wrapped_links["MVMAP"] = $data; 
            $xml = ArrayToXml::convert($wrapped_links);
        }
        else{
            $xml = NULL; 
        }  

       
       // dump($xml);
       
        $MODID_REF      =  trim($request['MODID_REF']);
      
        $DEACTIVATED    =   0;  
        $DODEACTIVATED  =   NULL;  
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();


        $array_data   = [
                        $MODID_REF, $CYID_REF, $BRID_REF, $FYID_REF, 
                        $xml,         $VTID,     $USERID,   $UPDATE,
                        $UPTIME,      $ACTION,      $IPADDRESS
                    ];
       // dump($array_data);
        //try {

            $sp_result = DB::select('EXEC SP_MODULE_VT_MAPPING_IN ?,?,?,?, ?,?,?,?, ?,?,?', $array_data);
           // dd($sp_result);
        
        // } catch (\Throwable $th) {
            
        //      return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);

        // }
    
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


            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first(); 

            $objModData =  DB::select("SELECT top 1 MODULEID_REF FROM TBL_MST_MODULE_VOUCHER_MAP WHERE BRID_REF=$BRID_REF AND VTID_REF=$id");
           //dump($objModData);

            $objResponse = DB::table('TBL_MST_MODULE_VOUCHER_MAP')
            ->where('TBL_MST_MODULE_VOUCHER_MAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_MODULE_VOUCHER_MAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_MODULE_VOUCHER_MAP.MODULEID_REF','=',$objModData[0]->MODULEID_REF)
            ->leftJoin('TBL_MST_MODULE',   'TBL_MST_MODULE.MODULEID','=',   'TBL_MST_MODULE_VOUCHER_MAP.MODULEID_REF')
            ->leftJoin('TBL_MST_VOUCHERTYPE',   'TBL_MST_VOUCHERTYPE.VTID','=',   'TBL_MST_MODULE_VOUCHER_MAP.VTID_REF')
            ->select('TBL_MST_MODULE_VOUCHER_MAP.*', 'TBL_MST_MODULE.MODULEID','TBL_MST_MODULE.MODULECODE','TBL_MST_MODULE.MODULENAME','TBL_MST_VOUCHERTYPE.VTID','TBL_MST_VOUCHERTYPE.VCODE','TBL_MST_VOUCHERTYPE.DESCRIPTIONS')
            ->orderBy('TBL_MST_VOUCHERTYPE.DESCRIPTIONS', 'ASC')
            ->get();    

            //dump($objResponse);
           
            $objCount = count($objResponse);

            return view('masters.Module.ModuleVoucherMapping.mstfrm208edit',compact(['objResponse','user_approval_level','objRights','objCount']));
        }

    }

     
    public function update(Request $request)
    {

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID     =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();

        $MODID_REF      =  trim($request['MODID_REF']);
     
        
        $data     =array();
        $existData=array();
        $r_count = $request['Row_Count'];

        for ($i=0; $i<=$r_count; $i++){

            if((isset($request['LISTPOP1ID_'.$i]) && $request['LISTPOP1ID_'.$i] !="")){

                $idval = trim($request['LISTPOP1ID_'.$i]);
                $headingval = trim($request['VHEADING_'.$i]);

                $MVMID = (isset($request['MVMID_'.$i]) &&  trim($request['MVMID_'.$i])!="" )? $request['MVMID_'.$i] : 0 ;
                
                $DEACTIVATED = (isset($request['DEACTIVATED_'.$i]) )? 1 : 0 ;
                
                $newDateString = NULL;
                $newdt = !(is_null($request['DODEACTIVATED_'.$i]) || empty($request['DODEACTIVATED_'.$i]) )=="true" ? $request['DODEACTIVATED_'.$i] : NULL; 

               

                if(is_null($newdt) && $DEACTIVATED==1 ){
                    return Response::json(['errors'=>true,'msg' => 'Please select Date of De-Activated of selected De-Activated box.','save'=>'invalid']);
                }

                if(!is_null($newdt) && $DEACTIVATED==1 ){
                    $newdt = str_replace( "/", "-",  $newdt ) ;
                    $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
                }

                $data[$i] = [
                    'MVMID' => $MVMID,                 
                    'VTID_REF' => $idval,
                    'HEADING' =>  $headingval,
                    'DEACTIVATED' => $DEACTIVATED,
                    'DODEACTIVATED' =>$newDateString,
                    'REMARKS' => trim($request['REMARKS_'.$i]),
                ];

                $existData[$i]=strtoupper($idval.'-'.$headingval);

            }
        }

        if(!empty($existData)){
            $counts     = array_count_values($existData);
            $NumVal     = max($counts);

            if( $NumVal > 1){
                return Response::json(['errors'=>true,'msg' => 'Duplicate row. Please check.','save'=>'invalid']);
            }
        }
        
        
        if(!empty($data)){ 
            $wrapped_links["MVMAP"] = $data; 
            $xml = ArrayToXml::convert($wrapped_links);
        }
        else{
            $xml = NULL; 
        }  
       
       
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "EDIT";
        $IPADDRESS      =   $request->getClientIp();


        $array_data   = [
                        $MODID_REF,   $CYID_REF, $BRID_REF, $FYID_REF, 
                        $xml,         $VTID,     $USERID,   $UPDATE,
                        $UPTIME,      $ACTION,      $IPADDRESS
                    ];
        
       //dump($xml);
      
    //    try {

            $sp_result = DB::select('EXEC SP_MODULE_VT_MAPPING_UP ?,?,?,?, ?,?,?,?, ?,?,?', $array_data);
           // dd($sp_result);
        
        //  } catch (\Throwable $th) {
          
        //       return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);

        //  }
    

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
        
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/ModuleVoucherMapping";
		$image_path         =   "docs/company".$CYID_REF."/ModuleVoucherMapping";     
        $destinationPath    =   str_replace('\\', '/', public_path($image_path));

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
                                   $uploaded_data[$index]["LOCATION"] = $image_path."/";
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
            return redirect()->route("master",[208,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
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
        
       
          
       // try {

             //save data
             $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);

           //  dd($sp_result[0]->RESULT);
      
      //  } catch (\Throwable $th) {
        
        //    return redirect()->route("master",[208,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");
    
      //  }
     
        if($sp_result[0]->RESULT=="SUCCESS"){

            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }

            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }

            return redirect()->route("master",[208,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[208,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("master",[208,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
            return redirect()->route("master",[208,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   

  
    //singleApprove begin
    public function singleapprove(Request $request)
    {
      
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID     =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   trim($request['user_approval_level']);   // user approval level value
        $IPADDRESS  =   $request->getClientIp();

        $MODID_REF      =  trim($request['MODID_REF']);

        $data     =array();
        $existData=array();
        $r_count = $request['Row_Count'];

        for ($i=0; $i<=$r_count; $i++){

            if((isset($request['LISTPOP1ID_'.$i]) && $request['LISTPOP1ID_'.$i] !="")){

                $idval = trim($request['LISTPOP1ID_'.$i]);
                $headingval = trim($request['VHEADING_'.$i]);

                $MVMID = (isset($request['MVMID_'.$i]) &&  trim($request['MVMID_'.$i])!="" )? $request['MVMID_'.$i] : 0 ;
                
                $DEACTIVATED = (isset($request['DEACTIVATED_'.$i]) )? 1 : 0 ;
                
                $newDateString = NULL;
                $newdt = !(is_null($request['DODEACTIVATED_'.$i]) || empty($request['DODEACTIVATED_'.$i]) )=="true" ? $request['DODEACTIVATED_'.$i] : NULL; 

               

                if(is_null($newdt) && $DEACTIVATED==1 ){
                    return Response::json(['errors'=>true,'msg' => 'Please select Date of De-Activated of selected De-Activated box.','save'=>'invalid']);
                }

                if(!is_null($newdt) && $DEACTIVATED==1 ){
                    $newdt = str_replace( "/", "-",  $newdt ) ;
                    $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
                }

                $data[$i] = [
                    'MVMID' => $MVMID,                 
                    'VTID_REF' => $idval,
                    'HEADING' =>  $headingval,
                    'DEACTIVATED' => $DEACTIVATED,
                    'DODEACTIVATED' =>$newDateString,
                    'REMARKS' => trim($request['REMARKS_'.$i]),
                ];

                $existData[$i]=strtoupper($idval.'-'.$headingval);

            }
        }

        if(!empty($existData)){
            $counts     = array_count_values($existData);
            $NumVal     = max($counts);

            if( $NumVal > 1){
                return Response::json(['errors'=>true,'msg' => 'Duplicate row. Please check.','save'=>'invalid']);
            }
        }
        
        
        if(!empty($data)){ 
            $wrapped_links["MVMAP"] = $data; 
            $xml = ArrayToXml::convert($wrapped_links);
        }
        else{
            $xml = NULL; 
        }  
       
        $array_data   = [
                        $MODID_REF, $CYID_REF, $BRID_REF, $FYID_REF, 
                        $xml,         $VTID,     $USERID,   $UPDATE,
                        $UPTIME,      $ACTION,      $IPADDRESS
                    ];
        
      // dump($xml);
      
        // try {

            $sp_result = DB::select('EXEC SP_MODULE_VT_MAPPING_UP ?,?,?,?, ?,?,?,?, ?,?,?', $array_data);
            //dd($sp_result);

        // } catch (\Throwable $th) {

        //     return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);

        // }    
                    
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


            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first(); 

            $objModData =  DB::select("SELECT top 1 MODULEID_REF FROM TBL_MST_MODULE_VOUCHER_MAP WHERE BRID_REF=$BRID_REF AND VTID_REF=$id");
           
            $objResponse = DB::table('TBL_MST_MODULE_VOUCHER_MAP')
            ->where('TBL_MST_MODULE_VOUCHER_MAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_MODULE_VOUCHER_MAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_MODULE_VOUCHER_MAP.MODULEID_REF','=',$objModData[0]->MODULEID_REF)
            ->leftJoin('TBL_MST_MODULE',   'TBL_MST_MODULE.MODULEID','=',   'TBL_MST_MODULE_VOUCHER_MAP.MODULEID_REF')
            ->leftJoin('TBL_MST_VOUCHERTYPE',   'TBL_MST_VOUCHERTYPE.VTID','=',   'TBL_MST_MODULE_VOUCHER_MAP.VTID_REF')
            ->select('TBL_MST_MODULE_VOUCHER_MAP.*', 'TBL_MST_MODULE.MODULEID','TBL_MST_MODULE.MODULECODE','TBL_MST_MODULE.MODULENAME','TBL_MST_VOUCHERTYPE.VTID','TBL_MST_VOUCHERTYPE.VCODE','TBL_MST_VOUCHERTYPE.DESCRIPTIONS')
            ->orderBy('TBL_MST_VOUCHERTYPE.DESCRIPTIONS', 'ASC')
            ->get();    

            $objCount = count($objResponse);

            return view('masters.Module.ModuleVoucherMapping.mstfrm208view',compact(['objResponse','user_approval_level','objRights','objCount']));
        }

    }
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }

        $objResponse = TblMstFrm208::whereIn('ITEMGID',$ids_data)->get();
        
        return view('masters.Module.ModuleVoucherMapping.mstfrm208print',compact(['objResponse']));
   }//print


   
    

    
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objResponse = TblMstFrm208::where('VTID','=',$id)->first();

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

            return view('masters.Module.ModuleVoucherMapping.mstfrm208attachment',compact(['objResponse','objMstVoucherType','objAttachments']));
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
            $m_array = [];
          
            foreach($req_data  as $index=>$row)
            {
                if (!in_array($row->ID, $m_array)){
                    $m_array[$index] = $row->ID;                  
                }
            }
            
            $recordIds = implode(',', $m_array);
           
            $ObjData2 =  DB::select("SELECT distinct MODULEID_REF FROM TBL_MST_MODULE_VOUCHER_MAP WHERE BRID_REF=$BRID_REF AND VTID_REF in($recordIds)");

             //DUMP($ObjData2);

            $iddata = [];
            foreach($ObjData2 as $cindex=>$crow)
            {
                $iddata['APPROVAL'][]['ID'] =  $crow->MODULEID_REF;
            }

            $xml = ArrayToXml::convert($iddata);

            $USERID_REF =   Auth::user()->USERID;
            $VTID_REF   =   $this->vtid_ref;  //voucher type id
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');
            $FYID_REF   =   Session::get('FYID_REF');       
            $TABLE      =   "TBL_MST_MODULE_VOUCHER_MAP";
            $FIELD      =   "MODULEID_REF";
            $ACTIONNAME     = $Approvallevel;
            $UPDATE     =   Date('Y-m-d');
            $UPTIME     =   Date('h:i:s.u');
            $IPADDRESS  =   $request->getClientIp();
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];

           // DUMP($log_data);

            $sp_result = DB::select('EXEC SP_MST_MULTIAPPROVAL_MVM ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);     
            //DD($sp_result);  
     
            if($sp_result[0]->RESULT=="All records approved"){

                return Response::json(['approve' =>true,'msg' => 'Records successfully Approved.']);

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
        
        $objData2 =  DB::select("SELECT top 1 MODULEID_REF FROM TBL_MST_COSTCENTER WHERE BRID_REF=$BRID_REF AND VTID_REF=$id");    
        
        $cancel_id = $objData2[0]->MODULEID_REF;
        
        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_MODULE_VOUCHER_MAP";
        $FIELD      =   "MODULEID_REF";
        $ID         =   $cancel_id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
        
        $cancelData[0]= ['NT' =>'TBL_MST_MODULE_VOUCHER_MAP'];

        $cancel_links["TABLES"] = $cancelData;
        $cancelxml = ArrayToXml::convert($cancel_links);

        $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$cancelxml];

        $sp_result = DB::select('EXEC SP_MST_CANCEL_MVM  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);

       // DD($sp_result);
       
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


    public function getmodules(Request $request){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        
        $ObjData = DB::select('SELECT * FROM TBL_MST_MODULE  where (DEACTIVATED=0 or DEACTIVATED is null)  and STATUS = ? ',
                        ['A']);

            if(!empty($ObjData)){

            foreach ($ObjData as $index=>$dataRow){
                $row = '';
                $row = $row.'<tr >
                <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_MODULEID_REF[]" id="subgl_'.$dataRow->MODULEID .'"  class="clssubgl" value="'.$dataRow->MODULEID.'" ></td>
                <td width="39%" class="ROW2">'.$dataRow->MODULECODE;
                $row = $row.'<input type="hidden" id="txtsubgl_'.$dataRow->MODULEID.'" data-desc="'.$dataRow->MODULECODE .'" data-ccname="'.$dataRow->MODULENAME.'" value="'.$dataRow->MODULEID.'"/></td>';
                $row = $row.'<td width="39%" class="ROW3">'.$dataRow->MODULENAME.'</td>';
                $row = $row.'</tr>';
                echo $row;
            }
            }else{
                echo '<tr><td colspan="3">Record not found.</td></tr>';
            }
            exit();
    }
    public function getvouchers(Request $request){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        
        $ObjData = DB::select('SELECT * FROM TBL_MST_VOUCHERTYPE  where (DEACTIVATED=0 or DEACTIVATED is null)  and STATUS = ? ',
                        ['A']);

            if(!empty($ObjData)){

            foreach ($ObjData as $index=>$dataRow){
               

                $row = '';
                $row = $row.'<tr>
                <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_VTID_REF[]" id="LISTPOP1code_'.$dataRow->VTID .'"  class="clsLISTPOP1id" value="'.$dataRow->VTID.'" ></td>
                <td width="39%" class="ROW2">'.$dataRow->VCODE;
                $row = $row.'<input type="hidden" id="txtLISTPOP1code_'.$dataRow->VTID.'" data-desc="'.$dataRow->VCODE.'"  data-descdate="'.$dataRow->DESCRIPTIONS.'"
                value="'.$dataRow->VTID.'"/></td><td width="39%" class="ROW3">'.$dataRow->DESCRIPTIONS.'</td></tr>';
                echo $row;

            }
            }else{
                echo '<tr><td colspan="3">Record not found.</td></tr>';
            }
            exit();
    }


}
