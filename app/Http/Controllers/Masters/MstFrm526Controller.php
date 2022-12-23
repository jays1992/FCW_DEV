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

class MstFrm526Controller extends Controller
{
   
    protected $form_id = 526;
    protected $vtid_ref  = 596;  //voucher type id

    
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

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_MST_PRICE_LEVEL',
            'HDR_ID'=>'PLID',
            'HDR_DOC_NO'=>'PLCODE',
            'HDR_DOC_DT'=>date('Y-m-d'),
            'HDR_DOC_TYPE'=>'master'
        );

        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
        // dd($docarray);
        
        $ObjUnionUDF = DB::table("TBL_MST_UDF")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDFID')->from('TBL_MST_UDF')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('VOUCHER_TYPE','=',596)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                     
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                
                   

        $objUdf  = DB::table('TBL_MST_UDF')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('VOUCHER_TYPE','=',596)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdf);

    return view('masters.Sales.PriceLevel.mstfrm526add', compact(['objUdf','objCountUDF','doc_req','docarray']));
       
   }

    // public function codeduplicate(Request $request){

    //     $CYID_REF = Auth::user()->CYID_REF;
    //     $BRID_REF = Session::get('BRID_REF');
    //     $FYID_REF = Session::get('FYID_REF');
    //     $PL_NO =  trim($request['PL_NO']);
        
    //     $objLabel = DB::table('TBL_MST_PRICELIST_HDR')
    //         ->where('CYID_REF','=',Auth::user()->CYID_REF)
    //         ->where('BRID_REF','=',Session::get('BRID_REF'))
    //         ->where('PL_NO','=',$PL_NO)
    //         ->select('PL_NO')
    //         ->first();
        
    //     if($objLabel){  

    //         return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
    //     }else{

    //         return Response::json(['not exists'=>true,'msg' => 'Ok']);
    //     }
    //     exit();
    // }

    public function save(Request $request){

        $r_count3 = $request['Row_Count3'];
        $udffield_Data = [];      
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFPLID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           }
            
        }
        if(count($udffield_Data)>0){
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
        }else{
            $XMLUDF = NULL;
        }

            $VTID_REF     =   $this->vtid_ref;
            
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');

            if (!empty($request['PLCODE'])) {
                $PLCODE = $request['PLCODE'];
            }
            else{
                $PLCODE = 0;
            }

            // $PLCODE = $request['PLCODE'];
            $PLNAME = $request['PLNAME'];
            $REMARKS = trim($request['REMARKS']);

            $UPDATE = "";            
            $UPTIME = "";

            $log_data = [ 
                $PLCODE, $PLNAME, $REMARKS, $CYID_REF, $BRID_REF, $FYID_REF, $XMLUDF, $VTID_REF, $USERID, $UPDATE, $UPTIME, $ACTIONNAME, $IPADDRESS
            ];

            $sp_result = DB::select('EXEC SP_PL_IN ?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);
        
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
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
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');  

        $objDataList=DB::select("SELECT * FROM TBL_MST_PRICE_LEVEL WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' ORDER BY PLID DESC");


       return view('masters.Sales.PriceLevel.mstfrm526',compact(['objRights','objDataList']));
        
    }

     
     


    // display attachments form
    public function attachment($id){
        // dd(Auth::user()->CYID_REF);

        if(!is_null($id))
        {
            $objMst = DB::table("TBL_MST_PRICE_LEVEL")
                        ->where('PLID','=',$id)
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
                            ->where('TBL_MST_ATTACHMENT.BRID_REF','=',Session::get('BRID_REF'))
                            ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                            ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                            ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                            ->get()->toArray();

                return view('masters.Sales.PriceLevel.mstfrm526attachment',compact(['objMst','objMstVoucherType','objAttachments']));
        }

    }




    //uploads attachments files
    public function docuploads(Request $request){

        $FormId     =   $this->form_id;

        $formData = $request->all();

        $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size = config("erpconst.attachments.max_size") * 1020 * 1024;

       
        $VTID           =   $formData["VTID_REF"]; 
        $ATTACH_DOCNO   =   $formData["ATTACH_DOCNO"]; 
        $ATTACH_DOCDT   =   $formData["ATTACH_DOCDT"]; 
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');       
       
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();
        
        $image_path         =   "docs/company".$CYID_REF."/vendormst";     
        $destinationPath    =   str_replace('\\', '/', public_path($image_path));
        // dd($destinationPath);
        
        if ( !is_dir($destinationPath) ) {
            mkdir($destinationPath, 0777, true);
        }

        $uploaded_data = [];
        $invlid_files = "";

        $duplicate_files="";

        foreach($formData["REMARKS"] as $index=>$row_val){

                if(isset($formData["FILENAME"][$index])){

                    $uploadedFile = $formData["FILENAME"][$index]; 
                    
                   

                    $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
                    $filesize               =   $uploadedFile ->getSize();  
                    $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );

                   

                    $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".$filenamewithextension;  

                    if ($uploadedFile->isValid()) {

                        if(in_array($extension,$allow_extnesions)){
                            
                            if($filesize < $allow_size){

                                $filename = $destinationPath."/".$filenametostore;

                                if (!file_exists($filename)) {

                                   $uploadedFile->move($destinationPath, $filenametostore);  
                                   $uploaded_data[$index]["FILENAME"] =$filenametostore;
                                   $uploaded_data[$index]["LOCATION"] = $image_path."/";
                                   $uploaded_data[$index]["REMARKS"] = is_null($row_val) ? '' : trim($row_val);

                                }else{

                                    $duplicate_files = " ". $duplicate_files.$filenamewithextension. " ";
                                }
                                

                                
                            }else{
                                
                                $invlid_files = $invlid_files.$filenamewithextension." (invalid size)  "; 
                            } 
                            
                        }else{

                            $invlid_files = $invlid_files.$filenamewithextension." (invalid extension)  ";                             
                        }
                    
                    }else{
                            
                        $invlid_files = $invlid_files.$filenamewithextension." (invalid)"; 
                    }

                }

        }

      
        if(empty($uploaded_data)){
            return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
        }
     

        $wrapped_links["ATTACHMENT"] = $uploaded_data;     
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
        

        $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);

     
        if($sp_result[0]->RESULT=="SUCCESS"){

            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }

            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }

            return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            
            return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   


   
   public function edit($id)
   {
        if(!is_null($id)){

            $FormId = $this->form_id;
            $Status = "A";
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $USERID = Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

          
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objRights = DB::table('TBL_MST_USERROLMAP')
                ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
                ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
                ->where('TBL_MST_USERROLMAP.BRID_REF','=',$BRID_REF)
                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID)
                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                ->first();
            
            $objMstResponse = DB::table('TBL_MST_PRICE_LEVEL')
                // ->where('FYID_REF','=',Session::get('FYID_REF'))
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('PLID','=',$id)
                ->select('*')
                ->first();
                // dd($objMstResponse);
            
            $ObjUnionUDF = DB::table("TBL_MST_UDF")->select('*')
                ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                            {       
                            $query->select('UDFID')->from('TBL_MST_UDF')
                                            ->where('STATUS','=','A')
                                            ->where('PARENTID','=',0)
                                            ->where('VOUCHER_TYPE','=',596)
                                            ->where('DEACTIVATED','=',0)
                                            ->where('CYID_REF','=',$CYID_REF);
                                                                
                })->where('DEACTIVATED','=',0)
                ->where('STATUS','<>','C')                    
                ->where('CYID_REF','=',$CYID_REF);

                // dd($ObjUnionUDF);
         
            $objUdf  = DB::table('TBL_MST_UDF')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('VOUCHER_TYPE','=',596)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray();   
            $objCountUDF = count($objUdf);

            // dd($objUdf);

            $objtempUdf = $objUdf;
            // dd($objtempUdf);
            foreach ($objtempUdf as $index => $udfvalue) {

                $objSavedUDF =  DB::table('TBL_MST_PRICE_LEVEL_UDF')
                ->where('PLID_REF','=',$id)
                ->where('UDFPLID_REF','=',$udfvalue->UDFID)
                ->select('UDF_VALUE')
                ->get()->toArray();

                if(!empty($objSavedUDF)){
                    $objUdf[$index]->UDF_VALUE = $objSavedUDF[0]->UDF_VALUE;
                }
                else{
                    $objUdf[$index]->UDF_VALUE = NULL; 
                }
            }
            $objtempUdf = [];

            $ActionStatus   =   "";

        return view('masters.Sales.PriceLevel.mstfrm526edit', compact(['FormId','objRights','objtempUdf','objUdf','objCountUDF','objMstResponse','ActionStatus']));    
        }

    }//edit function

    public function update(Request $request)
    {

        $r_count3 = $request['Row_Count3'];
        $udffield_Data = [];      
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFPLID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           }
            
        }
        if(count($udffield_Data)>0){
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
        }else{
            $XMLUDF = NULL;
        }

            $VTID_REF     =   $this->vtid_ref;
            
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');

            if (!empty($request['PLCODE'])) {
                $PLCODE = $request['PLCODE'];
            }
            else{
                $PLCODE = 0;
            }

            // $PLCODE = $request['PLCODE'];
            $PLNAME = $request['PLNAME'];
            $REMARKS = trim($request['REMARKS']);

            $UPDATE = "";            
            $UPTIME = "";

            $DEACTIVATED = $request['DEACTIVATED'];
            $DODEACTIVATED = $request['DODEACTIVATED'];

            $log_data = [ 
                $PLCODE, $PLNAME, $REMARKS, $DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF, $FYID_REF, $XMLUDF, $VTID_REF, $USERID, $UPDATE, $UPTIME, $ACTIONNAME, $IPADDRESS
            ];

            // dd($log_data);

            $sp_result = DB::select('EXEC SP_PL_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);
        
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
        // dd("RAHUL");

        $VTID_REF  = $this->vtid_ref;
        $USERID = Auth::user()->USERID;
        $CYID_REF = Auth::user()->CYID_REF;
        $IPADDRESS = $request->getClientIp();
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF'); 

        $sp_Approvallevel = [
            $USERID, $VTID_REF, $CYID_REF,$BRID_REF,
            $FYID_REF
            
        ];
       
        $sp_listing_result = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?, ?', $sp_Approvallevel);
        // dd($sp_listing_result);
    
        if(!empty($sp_listing_result))
        {
            foreach ($sp_listing_result as $key=>$approw)
            {  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$approw->LAVELS;
            }
        }
 
        $ACTIONNAME     =  $Approvallevel;
        $r_count3 = $request['Row_Count3'];
        $udffield_Data = [];      
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFPLID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           }
            
        }
        if(count($udffield_Data)>0){
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
        }else{
            $XMLUDF = NULL;
        }

            if (!empty($request['PLCODE'])) {
                $PLCODE = $request['PLCODE'];
            }
            else{
                $PLCODE = 0;
            }

            $PLNAME = $request['PLNAME'];
            $REMARKS = trim($request['REMARKS']);

            $UPDATE = "";            
            $UPTIME = "";

            $DEACTIVATED = $request['DEACTIVATED'];
            $DODEACTIVATED = $request['DODEACTIVATED'];

            $log_data = [ 
                $PLCODE, $PLNAME, $REMARKS, $DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF, $FYID_REF, $XMLUDF, $VTID_REF, $USERID, $UPDATE, $UPTIME, $ACTIONNAME, $IPADDRESS
            ];

            $sp_result = DB::select('EXEC SP_PL_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);
        
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
        if(!is_null($id)){

        $FormId = $this->form_id;
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $USERID = Auth::user()->USERID;
        $VTID       =   $this->vtid_ref;

        $sp_user_approval_req = [
            $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
        ];        

      
        $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);

        $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

        $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',$BRID_REF)
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();
        
        $objMstResponse = DB::table('TBL_MST_PRICE_LEVEL')
            ->where('FYID_REF','=',Session::get('FYID_REF'))
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('PLID','=',$id)
            ->select('*')
            ->first();

            // dd($objMstResponse);
        
        $ObjUnionUDF = DB::table("TBL_MST_UDF")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDFID')->from('TBL_MST_UDF')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('VOUCHER_TYPE','=',596)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                    
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
     
        $objUdf  = DB::table('TBL_MST_UDF')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('VOUCHER_TYPE','=',596)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdf);

        // dd($objUdf);

        $objtempUdf = $objUdf;
        foreach ($objtempUdf as $index => $udfvalue) {

            $objSavedUDF =  DB::table('TBL_MST_PRICE_LEVEL_UDF')
            ->where('PLID_REF','=',$id)
            ->where('UDFPLID_REF','=',$udfvalue->UDFID)
            ->select('UDF_VALUE')
            ->get()->toArray();
            // dd($objSavedUDF);

            if(!empty($objSavedUDF)){
                $objUdf[$index]->UDF_VALUE = $objSavedUDF[0]->UDF_VALUE;
            }
            else{
                $objUdf[$index]->UDF_VALUE = NULL; 
            }
            // dd($objUdf[$index]->UDF_VALUE);
        }

        $objtempUdf = [];

        $objUdfSOData2 =  DB::table('TBL_MST_UDF')
        ->where('STATUS','=','A')
        ->where('PARENTID','=',0)
        ->where('VOUCHER_TYPE','=',596)
        ->where('DEACTIVATED','=',0)
        ->where('CYID_REF','=',$CYID_REF)
        ->union($ObjUnionUDF)
        ->get()->toArray();  
        // dd($objUdfSOData2);
        
        $objSOUDF = DB::table('TBL_MST_PRICE_LEVEL_UDF')                    
        ->where('PLID_REF','=',$id)
        ->select('*')
        ->orderBy('PL_UDFID','ASC')
        ->get()->toArray();
        // dd($objSOUDF);
        $objCount3 = count($objSOUDF);

        $AlpsStatus =   $this->AlpsStatus();

        $ActionStatus   =   "disabled";

        $TabSetting =   Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        // dd($TabSetting);

    return view('masters.Sales.PriceLevel.mstfrm526view', compact(['AlpsStatus','FormId','objRights','objUdf','objCountUDF','objMstResponse','objSOUDF','objUdfSOData2','ActionStatus','TabSetting']));     
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
            $TABLE      =   "TBL_MST_PRICE_LEVEL";
            $FIELD      =   "PLID";
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
        // dd("RJ");

        $id = $request->{0};

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_PRICE_LEVEL";
        $FIELD      =   "PLID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
        $canceldata[0]=[
            'NT'  => 'TBL_MST_PRICE_LEVEL_UDF',
       ];    
       $links["TABLES"] = $canceldata; 
       $cancelxml = ArrayToXml::convert($links);
       // dd($cancelxml);
        
        $udf_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$cancelxml ];

        // dd($udf_cancel_data);

        $sp_result = DB::select('EXEC SP_MST_CANCEL ?,?,?,?,?,?,?,?,?,?,?,?', $udf_cancel_data);
        // dd($sp_result);

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
