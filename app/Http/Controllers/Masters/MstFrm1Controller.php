<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Master\TblMstFrm1;
use App\Models\Admin\TblMstUser;
use Auth;
use DB;
use Session;
use Response;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class MstFrm1Controller extends Controller
{
   
    protected $form_id = 1;
    protected $vtid_ref   = 154;  //voucher type id

    //validation messages
    protected   $messages = [
                    'CTCODE.required' => 'Required field',
                    'CTCODE.unique' => 'Duplicate Code'
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

        $objDataList=DB::select("SELECT * FROM TBL_MST_CALCULATION 
        WHERE CYID_REF='$CYID_REF' ORDER BY CTID ");
        
       return view('masters.Common.CalculationTemplate.mstfrm1',compact(['objRights','objDataList']));
        
    }


    //uploads attachments files
    public function docuploads(Request $request){

        $formData = $request->all();

        $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size = config("erpconst.attachments.max_size") * 1020 * 1024;

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
        
        $destinationPath = storage_path()."/docs/company".$CYID_REF."/calculationtemplate";

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
            return redirect()->route("master",[1,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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

            return redirect()->route("master",[1,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[1,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            return redirect()->route("master",[1,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   


    public function add(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $objglcode = DB::table('TBL_MST_GENERALLEDGER')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('STATUS','=',$Status)
        ->where('SUBLEDGER','=',0)
        ->select('TBL_MST_GENERALLEDGER.*')
        ->get()
        ->toArray();

        $module =   $this->getModule();  

        return view('masters.Common.CalculationTemplate.mstfrm1add',compact(['objglcode','module']));
       
   }

    public function getModule(){
        return  DB::select("SELECT MODULEID,MODULECODE,MODULENAME FROM TBL_MST_MODULE WHERE STATUS='A'"); 
    }

   public function codeduplicate(Request $request){

        
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $CTCODE =   strtoupper($request['CTCODE']);
        
        $objLabel = DB::table('TBL_MST_CALCULATION')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('CTCODE','=',$CTCODE)
        ->select('CTCODE')
        ->first();
        // dd($objLabel);
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }

    
   public function save(Request $request){
    
        //validation rules
    $rules = [
            'CTCODE' => 'required',         
        ];


        $req_data = [

            'CTCODE'     =>    $request['CTCODE']
        ]; 


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
           return Response::json(['errors' => $validator->errors()]);	
        }
       
        $r_count = $request['Row_Count'];
        for ($i=0; $i<=$r_count; $i++)
        {
            if((isset($request['COMPONENT_'.$i])))
            {
                $data[$i] = [
                    'CALCULATIONCOMPONENTNAME'    => strtoupper($request['COMPONENT_'.$i]),
                    'SQNO' => $request['SQNO_'.$i],
                    'BASIS' => $request['BASIS_'.$i],
                    'GL' => $request['GLID_REF_'.$i],
                    'FORMULAYESNO' => (isset($request['FORMULAYESNO_'.$i])!="true" ? 0 : 1) ,
                    'RATE' => (is_null($request['RATEPERCENTATE_'.$i]) ? 0.0000 : $request['RATEPERCENTATE_'.$i]),
                    'FORMULA' => $request['FORMULA_'.$i],
                    'AMOUNT' => (isset($request['AMOUNT_'.$i])!="true" ? 0.00 : $request['AMOUNT_'.$i]),
                    'GST' => (isset($request['GST_'.$i])!="true" ? 0 : 1) ,
                    'ACTUAL' => (isset($request['ACTUAL_'.$i])!="true" ? 0 : 1) ,
                    'LANDEDCOST' => (isset($request['LANDEDCOST_'.$i])!="true" ? 0 : 1) ,
                ];
            }
        }
        $wrapped_links["TEMPLATE"] = $data; 

        $xml = ArrayToXml::convert($wrapped_links);
        // dd($xml);
        //get data
        $CTCODE         =   strtoupper(trim($request['CTCODE']) );
        $CTDESCRIPTION  =   trim($request['CTDESCRIPTION']); 
        $MODULE         =   trim($request['MODULE']); 
        $TYPE           =   trim($request['TYPE']); 
        $DEACTIVATED =  (isset($request->DEACTIVATED)? $request->DEACTIVATED : 0) ;
        $DODEACTIVATED =  (isset($request->DODEACTIVATED)? $request->DODEACTIVATED : NULL) ;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID        =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "ADD";
        $IPADDRESS  =   $request->getClientIp();
        
        $calculation_data = [
                        $CTCODE, $CTDESCRIPTION, $DEACTIVATED, $DODEACTIVATED,
                        $CYID_REF, $BRID_REF,$FYID_REF,$USERID, $xml, $VTID,$UPDATE,
                        $UPTIME, $ACTION, $IPADDRESS,$MODULE,$TYPE
                    ];
                    // dd($calculation_data);
     //  DB::enableQueryLog();
       try {

            //save data
           $sp_result = DB::select('EXEC SP_CALCULATION_IN ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?', $calculation_data);
      
        } catch (\Throwable $th) {
        
            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
    
        }
     
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
           
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','country'=>'duplicate']);
            
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
            $Status = "A";

            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
            $objglcode = DB::table('TBL_MST_GENERALLEDGER')
                             ->where('CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('STATUS','=',$Status)
                             ->where('SUBLEDGER','=',0)
                             ->select('TBL_MST_GENERALLEDGER.*')
                             ->get()
                             ->toArray();

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        
            
            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objCalculation =   DB::table('TBL_MST_CALCULATION')
                                ->where('TBL_MST_CALCULATION.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_CALCULATION.CTID','=',$id)
                                ->select('TBL_MST_CALCULATION.*')
                                ->first();

            if(isset($objCalculation->MODULEID_REF) && $objCalculation->MODULEID_REF !=""){
                $MODULEID       =   $objCalculation->MODULEID_REF;
                $module_array   =   array();
                $module_data    =   DB::select("SELECT MODULECODE FROM TBL_MST_MODULE WHERE STATUS='A' AND MODULEID IN($MODULEID)");

                if(!empty($module_data)){
                    foreach($module_data as $key=>$val){
                        $module_array[]=$val->MODULECODE;
                    }
                }

                if(!empty($module_array)){
                    $module_name    =   implode(',',$module_array);
                }

                $objCalculation->MODULE_NAME    =   $module_name;
            }

            $module =   $this->getModule();  
 

            if(strtoupper($objCalculation->STATUS)=="A" || strtoupper($objCalculation->STATUS)=="C"){
                exit("Sorry, Only Un Approved record can edit.");
            }

            $objCalculationtemp = DB::table('TBL_MST_CALCULATIONTEMPLATE')
                            ->where('TBL_MST_CALCULATIONTEMPLATE.CTID_REF','=',$id)
                            ->select('TBL_MST_CALCULATIONTEMPLATE.*')
                            ->get()
                            ->toArray();
            $objCount = count($objCalculationtemp);
            
            
            
            return view('masters.Common.CalculationTemplate.mstfrm1edit',compact(['module','objCalculation','objCalculationtemp','user_approval_level','objRights','objglcode','objCount']));
        }

    }//edit function

    //update the data
   public function update(Request $request)
   {
     
    $update_rules = [
        'CTCODE' => 'required',         
    ];


    $req_update_data = [

        'CTCODE'     =>    $request['CTCODE']
    ]; 


        $validator = Validator::make( $req_update_data, $update_rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }
    
        $r_count = $request['Row_Count'];
       
        for ($i=0; $i<=$r_count; $i++)
        {
            if((isset($request['COMPONENT_'.$i])) && $request['COMPONENT_'.$i] !="")
            {
                $data[$i] = [
                    'TID' => (isset($request['TID_'.$i])!="true" ? 0 : $request['TID_'.$i]) ,
                    'CALCULATIONCOMPONENTNAME'    => strtoupper($request['COMPONENT_'.$i]),
                    'SQNO' => $request['SQNO_'.$i],
                    'BASIS' => $request['BASIS_'.$i],
                    'GL' => $request['GLID_REF_'.$i],
                    'FORMULAYESNO' => (isset($request['FORMULAYESNO_'.$i])!="true" ? 0 : 1) ,
                    'RATE' => (is_null($request['RATEPERCENTATE_'.$i]) ? 0.0000 : $request['RATEPERCENTATE_'.$i]),
                    'FORMULA' => $request['FORMULA_'.$i],
                    'AMOUNT' => (isset($request['AMOUNT_'.$i])!="true" ? 0 : $request['AMOUNT_'.$i]),
                    'GST' => (isset($request['GST_'.$i])!="true" ? 0 : 1) ,
                    'ACTUAL' => (isset($request['ACTUAL_'.$i])!="true" ? 0 : 1) ,
                    'LANDEDCOST' => (isset($request['LANDEDCOST_'.$i])!="true" ? 0 : 1) ,
                ];
            }
        }
        // dd($data);
        $wrapped_links["TEMPLATE"] = $data; 

        $xml = ArrayToXml::convert($wrapped_links);
        // dd($xml);
        //get data
        $CTCODE   =   strtoupper(trim($request['CTCODE']) );
        $CTDESCRIPTION =   trim($request['CTDESCRIPTION']); 
        $MODULE         =   trim($request['MODULE']); 
        $TYPE           =   trim($request['TYPE']); 
        $DEACTIVATED =  (isset($request->DEACTIVATED)? 1 : 0) ;
        $DODEACTIVATED =  (isset($request->DODEACTIVATED)? $request->DODEACTIVATED : NULL) ;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID        =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();
        
        $calculation_data = [
                        $CTCODE, $CTDESCRIPTION, $DEACTIVATED, $DODEACTIVATED,
                        $CYID_REF, $BRID_REF,$FYID_REF,$USERID, $xml, $VTID,$UPDATE,
                        $UPTIME, $ACTION, $IPADDRESS,$MODULE,$TYPE
                    ];
                    //  dd($calculation_data);
     //  DB::enableQueryLog();
    //    try {

            //save data
           $sp_result = DB::select('EXEC SP_CALCULATION_UP ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?', $calculation_data);
      
        // } catch (\Throwable $th) {
        
        //     return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
    
        // }
     
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
           
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','country'=>'duplicate']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }
        
        exit();           
    } // update function

    //singleApprove begin
    public function Approve(Request $request)
    {
      
         $approv_rules = [
 
             'CTCODE' => 'required',          
         ];
 
         $req_approv_data = [
 
             'CTCODE' =>   $request['CTCODE']
         ]; 
 
 
         $validator = Validator::make( $req_approv_data, $approv_rules, $this->messages);
 
         if ($validator->fails())
         {
         return Response::json(['errors' => $validator->errors()]);	
         }

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
     
         $r_count = $request['Row_Count'];
       
         for ($i=0; $i<=$r_count; $i++)
        {
            if((isset($request['COMPONENT_'.$i])) && $request['COMPONENT_'.$i] !="")
            {
                $data[$i] = [
                    'TID' => (isset($request['TID_'.$i])!="true" ? 0 : $request['TID_'.$i]) ,
                    'CALCULATIONCOMPONENTNAME'    => strtoupper($request['COMPONENT_'.$i]),
                    'SQNO' => $request['SQNO_'.$i],
                    'BASIS' => $request['BASIS_'.$i],
                    'GL' => $request['GLID_REF_'.$i],
                    'FORMULAYESNO' => (isset($request['FORMULAYESNO_'.$i])!="true" ? 0 : 1) ,
                    'RATE' => (is_null($request['RATEPERCENTATE_'.$i]) ? 0.0000 : $request['RATEPERCENTATE_'.$i]),
                    'FORMULA' => $request['FORMULA_'.$i],
                    'AMOUNT' => (isset($request['AMOUNT_'.$i])!="true" ? 0 : $request['AMOUNT_'.$i]),
                    'GST' => (isset($request['GST_'.$i])!="true" ? 0 : 1) ,
                    'ACTUAL' => (isset($request['ACTUAL_'.$i])!="true" ? 0 : 1) ,
                    'LANDEDCOST' => (isset($request['LANDEDCOST_'.$i])!="true" ? 0 : 1) ,
                ];
            }
        }
        $wrapped_links["TEMPLATE"] = $data; 

        $xml = ArrayToXml::convert($wrapped_links);
        // dd($xml);
        //get data
        $CTCODE   =   strtoupper(trim($request['CTCODE']) );
        $CTDESCRIPTION =   trim($request['CTDESCRIPTION']); 
        $MODULE         =   trim($request['MODULE']); 
        $TYPE           =   trim($request['TYPE']); 
        $DEACTIVATED =  (isset($request->DEACTIVATED)? 1 : 0) ;
        $DODEACTIVATED =  (isset($request->DODEACTIVATED)? $request->DODEACTIVATED : NULL) ;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID        =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     = $Approvallevel;
        $IPADDRESS  =   $request->getClientIp();
        
        $calculation_data = [
                        $CTCODE, $CTDESCRIPTION, $DEACTIVATED, $DODEACTIVATED,
                        $CYID_REF, $BRID_REF,$FYID_REF,$USERID, $xml, $VTID,$UPDATE,
                        $UPTIME, $ACTION, $IPADDRESS,$MODULE,$TYPE
                    ];
                    // dd($calculation_data);
     //  DB::enableQueryLog();
       try {

            //save data
           $sp_result = DB::select('EXEC SP_CALCULATION_UP ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?', $calculation_data);
      
        } catch (\Throwable $th) {
        
            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
    
        }
     
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully approved.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
           
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','calculation'=>'duplicate']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
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
            $Status = "A";

            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
            $objglcode = DB::table('TBL_MST_GENERALLEDGER')
                             ->where('CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('STATUS','=',$Status)
                             ->where('SUBLEDGER','=',0)
                             ->select('TBL_MST_GENERALLEDGER.*')
                             ->get()
                             ->toArray();

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objCalculation = DB::table('TBL_MST_CALCULATION')
                            ->where('TBL_MST_CALCULATION.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_CALCULATION.CTID','=',$id)
                            ->select('TBL_MST_CALCULATION.*')
                            ->first();

            if(isset($objCalculation->MODULEID_REF) && $objCalculation->MODULEID_REF !=""){
                $MODULEID       =   $objCalculation->MODULEID_REF;
                $module_array   =   array();
                $module_data    =   DB::select("SELECT MODULECODE FROM TBL_MST_MODULE WHERE STATUS='A' AND MODULEID IN($MODULEID)");

                if(!empty($module_data)){
                    foreach($module_data as $key=>$val){
                        $module_array[]=$val->MODULECODE;
                    }
                }

                if(!empty($module_array)){
                    $module_name    =   implode(',',$module_array);
                }

                $objCalculation->MODULE_NAME    =   $module_name;
            }

            $module =   $this->getModule(); 

            $objCalculationtemp = DB::table('TBL_MST_CALCULATIONTEMPLATE')
                            ->where('TBL_MST_CALCULATIONTEMPLATE.CTID_REF','=',$id)
                            ->select('TBL_MST_CALCULATIONTEMPLATE.*')
                            ->get()
                            ->toArray();
            $objCount = count($objCalculationtemp);

            return view('masters.Common.CalculationTemplate.mstfrm1view',compact(['module','objCalculation','objCalculationtemp','user_approval_level','objRights','objglcode','objCount']));
        }

    }//view function
  
   

   
  
    
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objCalculation = DB::table('TBL_MST_CALCULATION')
            ->where('TBL_MST_CALCULATION.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_CALCULATION.CTID','=',$id)
            ->select('TBL_MST_CALCULATION.*')
            ->first();
            // dd($objCalculation);
            //select * from TBL_MST_VOUCHERTYPE where VTID=114
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

                 // dump( $objAttachments);

            return view('masters.Common.CalculationTemplate.mstfrm1attachment',compact(['objCalculation','objMstVoucherType','objAttachments']));
        }

    }
    
    //Cancel the data
   public function cancel(Request $request){
    //  dd($request->{0});  

   //save data
        $id = $request->{0};

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_CALCULATION";
        $FIELD      =   "CTID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $canceldata[0]=[
            'NT'  => 'TBL_MST_CALCULATION',
       ];        
       $links["TABLES"] = $canceldata; 
       $cancelxml = ArrayToXml::convert($links);
        
        $udfforse_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$cancelxml ];

        $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $udfforse_cancel_data);

        if($sp_result[0]->RESULT=="CANCELED"){  

            return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
        
            return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'invalid'=>'invalid']);
        }
        
        exit(); 
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
            $TABLE      =   "TBL_MST_CALCULATION";
            $FIELD      =   "CTID";
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
        
        return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','calculationtemplate'=>'norecord']);
        
        }else{
        return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','calculationtemplate'=>'Some Error']);
        }
        
        exit();    
    }

    


}
