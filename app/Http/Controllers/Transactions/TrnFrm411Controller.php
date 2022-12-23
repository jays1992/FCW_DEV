<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Admin\TblMstUser;
use Auth;
use DB;
use Session;
use Response;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use App\Helpers\Utils;
use Carbon\Carbon;

class TrnFrm411Controller extends Controller
{
   
    protected $form_id = 411;
    protected $vtid_ref   = 266;  //voucher type id
    protected $view= "transactions.Asset.AssetTransfer.trnfrm411";

       
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
        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');     

        $objDataList = DB::table('TBL_TRN_ASSET_TRANSFER')
            ->orderBy('ASSETTRANID', 'DESC')
            ->get();

        return view($this->view,compact(['FormId','objRights','objDataList']));

    }

    public function add(){

        $FormId         =   $this->form_id;
        $Status = "A"; 
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        
        $objDataList    =   DB::table('TBL_MST_ASSETSUBLOCATION')
        ->get();
        $objEmp    =   DB::table('TBL_MST_EMPLOYEE')
        ->where('STATUS','=','A')
        ->select('TBL_MST_EMPLOYEE.EMPID','TBL_MST_EMPLOYEE.EMPCODE','TBL_MST_EMPLOYEE.FNAME')
        ->get();

        

        $objDDNO = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('FYID_REF','=',$FYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_DOCNO_DEFINITION.*')
        ->first();
       
    $objDPDOCNO ='';
    if(!empty($objDDNO)){
        if($objDDNO->SYSTEM_GRSR == "1")
        {
            if($objDDNO->PREFIX_RQ == "1")
            {
                $objDPDOCNO = $objDDNO->PREFIX;
            }        
            if($objDDNO->PRE_SEP_RQ == "1")
            {
                if($objDDNO->PRE_SEP_SLASH == "1")
                {
                $objDPDOCNO = $objDPDOCNO.'/';
                }
                if($objDDNO->PRE_SEP_HYPEN == "1")
                {
                $objDPDOCNO = $objDPDOCNO.'-';
                }
            }        
            if($objDDNO->NO_MAX)
            {   
                $objDPDOCNO = $objDPDOCNO.str_pad($objDDNO->LAST_RECORDNO+1, $objDDNO->NO_MAX, "0", STR_PAD_LEFT);
            }
            
            if($objDDNO->NO_SEP_RQ == "1")
            {
                if($objDDNO->NO_SEP_SLASH == "1")
                {
                $objDPDOCNO = $objDPDOCNO.'/';
                }
                if($objDDNO->NO_SEP_HYPEN == "1")
                {
                $objDPDOCNO = $objDPDOCNO.'-';
                }
            }
            if($objDDNO->SUFFIX_RQ == "1")
            {
                $objDPDOCNO = $objDPDOCNO.$objDDNO->SUFFIX;
            }
        }
    }   

        return view($this->view.'add',compact(['FormId','objDDNO','objEmp','objDataList','objDPDOCNO']));
    }

   public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $ATTCODE =   $request['ATTCODE'];
        
        $objLabel = DB::table('TBL_MST_ATTRIBUTE')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('ATTCODE','=',$ATTCODE)
        ->select('ATTCODE')
        ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }

   public function save(Request $request)
   {             

        $ASSETTRANCODE      =   trim($request['LEAVE_APP_NO']);
        $ASSETTRANDATE      =   trim($request['LEAVE_APP_DT']);
        $ASSETID_REF        =   trim($request['ASSETID_REF']);
        $ASSETNOID_REF      =   trim($request['ASTID_REF']);
        $ALID_REF_FROM      =   trim($request['ALID_REF_FROM']);
        $WITH_EMPLOYEE      =   trim($request['ASASTCATID']);
        $ALID_REF_TO        =   trim($request['ASTSUBLOCTION']);
        $ASLID_REF_TO       =   trim($request['ASTSUBLTION']);
        $TO_EMPLOYEE        =   trim($request['ASTEMPLYEE']);
        $REASON             =   trim($request['REASON_TRANFR']);
        $REMARKS            =   trim($request['REMARKS']);
        $ASLID_REF_FROM     =   NULL;


        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       = Session::get('FYID_REF');
        $VTID           =   $this->vtid_ref;
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();
        
        $array_data   = [
                    $ASSETTRANCODE,    $ASSETTRANDATE,     $ASSETID_REF,      $ASSETNOID_REF,
                    $ALID_REF_FROM,    $ASLID_REF_FROM,    $WITH_EMPLOYEE,    $ALID_REF_TO,
                    $ASLID_REF_TO,     $TO_EMPLOYEE,       $REASON,           $REMARKS,
                    $CYID_REF,         $BRID_REF,          $FYID_REF,         $VTID,
                    $USERID,           $UPDATE,            $UPTIME,           $ACTION,
                    $IPADDRESS          
                    ];

            //dd($array_data);

        $sp_result = DB::select('EXEC SP_ASSET_TRANSFER_IN ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?', $array_data);

        return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);

        exit();    
    }

    public function edit($id){

        if(!is_null($id))
        {
        
            $FormId     =   $this->form_id;
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

            $objResponse = DB::table('TBL_TRN_ASSET_TRANSFER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('ASSETTRANID','=',$id)
            ->select('*')
            ->first();         
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
            
            $objHRD = DB::table('TBL_TRN_ASSET_TRANSFER')
            ->where('TBL_TRN_ASSET_TRANSFER.ASSETTRANID','=',$id)
            ->leftJoin('TBL_MST_ASSET', 'TBL_TRN_ASSET_TRANSFER.ASSETID_REF','=','TBL_MST_ASSET.ASSETID')
            ->leftJoin('TBL_MST_ASSETLOCATION', 'TBL_TRN_ASSET_TRANSFER.ASSETNOID_REF','=','TBL_MST_ASSETLOCATION.ALID')
            ->leftJoin('TBL_MST_ASSETSUBLOCATION', 'TBL_TRN_ASSET_TRANSFER.ALID_REF_FROM','=','TBL_MST_ASSETSUBLOCATION.ASLID')
            ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_TRN_ASSET_TRANSFER.WITH_EMPLOYEE','=','TBL_MST_EMPLOYEE.EMPID')
            ->select('TBL_TRN_ASSET_TRANSFER.*','TBL_MST_ASSET.*','TBL_MST_ASSET.DESCRIPTIONS AS ASTDESCRIPTIONS','TBL_MST_ASSETLOCATION.ALCODE AS ASTCODE','TBL_MST_ASSETLOCATION.DESCRIPTIONS AS ASTDES','TBL_MST_ASSETLOCATION.ALID AS ASTID','TBL_MST_ASSETSUBLOCATION.ASLID AS ASTSUBID','TBL_MST_ASSETSUBLOCATION.ASLCODE AS ASTSUBCODE','TBL_MST_ASSETSUBLOCATION.DESCRIPTIONS AS ASTSUBDES','TBL_MST_EMPLOYEE.*')
            ->first();

            $objDataList    =   DB::table('TBL_MST_ASSETSUBLOCATION')
            ->get();
            $objEmp    =   DB::table('TBL_MST_EMPLOYEE')
            ->where('STATUS','=','A')
            ->select('TBL_MST_EMPLOYEE.EMPID','TBL_MST_EMPLOYEE.EMPCODE','TBL_MST_EMPLOYEE.FNAME')
            ->get();

            return view($this->view.'edit',compact(['FormId','objHRD','objResponse','objEmp','user_approval_level','objRights','objDataList']));
        }

    }

     
    public function update(Request $request)
    {

        $ASSETTRANCODE      =   trim($request['LEAVE_APP_NO']);
        $ASSETTRANDATE      =   trim($request['LEAVE_APP_DT']);
        $ASSETID_REF        =   trim($request['ASSETID_REF']);
        $ASSETNOID_REF      =   trim($request['ASTID_REF']);
        $ALID_REF_FROM      =   trim($request['ALID_REF_FROM']);
        $WITH_EMPLOYEE      =   trim($request['ASASTCATID']);
        $ALID_REF_TO        =   trim($request['ASTSUBLOCTION']);
        $ASLID_REF_TO       =   trim($request['ASTSUBLTION']);
        $TO_EMPLOYEE        =   trim($request['ASTEMPLYEE']);
        $REASON             =   trim($request['REASON_TRANFR']);
        $REMARKS            =   trim($request['REMARKS']);
        $ASLID_REF_FROM     =   NULL;


        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       = Session::get('FYID_REF');
        $VTID           =   $this->vtid_ref;
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "EDIT";
        $IPADDRESS      =   $request->getClientIp();
        
        $array_data   = [
                    $ASSETTRANCODE,    $ASSETTRANDATE,     $ASSETID_REF,      $ASSETNOID_REF,
                    $ALID_REF_FROM,    $ASLID_REF_FROM,    $WITH_EMPLOYEE,    $ALID_REF_TO,
                    $ASLID_REF_TO,     $TO_EMPLOYEE,       $REASON,           $REMARKS,
                    $CYID_REF,         $BRID_REF,          $FYID_REF,         $VTID,
                    $USERID,           $UPDATE,            $UPTIME,           $ACTION,
                    $IPADDRESS          
                    ];

            //dd($array_data);

        $sp_result = DB::select('EXEC SP_ASSET_TRANSFER_UP ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?', $array_data);

        return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);
        
        exit();            
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
        
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/QualityInspectionGRN";
		
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

                   

                    $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."#_".$filenamewithextension;  

                    if ($uploadedFile->isValid()) {

                        if(in_array($extension,$allow_extnesions)){
                            
                            if($filesize < $allow_size){

                                $filename = $destinationPath."/".$filenametostore;

                                if (!file_exists($filename)) {

                                   $uploadedFile->move($destinationPath, $filenametostore);  
                                   $uploaded_data[$index]["FILENAME"] =$filenametostore;
                                   $uploaded_data[$index]["LOCATION"] = $destinationPath."/";
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
            return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
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

            return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);
            


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            
            return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
       
    }
  


    public function Approve(Request $request){

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref; 
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
           
            $ASSETTRANCODE      =   trim($request['LEAVE_APP_NO']);
            $ASSETTRANDATE      =   trim($request['LEAVE_APP_DT']);
            $ASSETID_REF        =   trim($request['ASSETID_REF']);
            $ASSETNOID_REF      =   trim($request['ASTID_REF']);
            $ALID_REF_FROM      =   trim($request['ALID_REF_FROM']);
            $WITH_EMPLOYEE      =   trim($request['ASASTCATID']);
            $ALID_REF_TO        =   trim($request['ASTSUBLOCTION']);
            $ASLID_REF_TO       =   trim($request['ASTSUBLTION']);
            $TO_EMPLOYEE        =   trim($request['ASTEMPLYEE']);
            $REASON             =   trim($request['REASON_TRANFR']);
            $REMARKS            =   trim($request['REMARKS']);
            $ASLID_REF_FROM     =   NULL;
    

            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');
            $FYID_REF   =   Session::get('FYID_REF');
            $VTID       =   $this->vtid_ref;
            $USERID     =   Auth::user()->USERID;
            $UPDATE     =   Date('Y-m-d');
            
            $UPTIME     =   Date('h:i:s.u');
            $ACTION = $Approvallevel;
            $IPADDRESS  =   $request->getClientIp();          

            $array_data   = [
                $ASSETTRANCODE,    $ASSETTRANDATE,     $ASSETID_REF,      $ASSETNOID_REF,
                $ALID_REF_FROM,    $ASLID_REF_FROM,    $WITH_EMPLOYEE,    $ALID_REF_TO,
                $ASLID_REF_TO,     $TO_EMPLOYEE,       $REASON,           $REMARKS,
                $CYID_REF,         $BRID_REF,          $FYID_REF,         $VTID,
                $USERID,           $UPDATE,            $UPTIME,           $ACTION,
                $IPADDRESS          
                ];

            //dd($array_data);

            $sp_result = DB::select('EXEC SP_ASSET_TRANSFER_UP ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?', $array_data);

            return Response::json(['success' =>true,'msg' => 'Record successfully Approved.']);               

            exit();     
    }

    public function view($id){

        if(!is_null($id))
        {
            $FormId     =   $this->form_id;
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');          

            $objResponse = DB::table('TBL_TRN_ASSET_TRANSFER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('ASSETTRANID','=',$id)
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
            
            $objHRD = DB::table('TBL_TRN_ASSET_TRANSFER')
            ->where('TBL_TRN_ASSET_TRANSFER.ASSETTRANID','=',$id)
            ->leftJoin('TBL_MST_ASSET', 'TBL_TRN_ASSET_TRANSFER.ASSETID_REF','=','TBL_MST_ASSET.ASSETID')
            ->leftJoin('TBL_MST_ASSETLOCATION', 'TBL_TRN_ASSET_TRANSFER.ASSETNOID_REF','=','TBL_MST_ASSETLOCATION.ALID')
            ->leftJoin('TBL_MST_ASSETSUBLOCATION', 'TBL_TRN_ASSET_TRANSFER.ALID_REF_FROM','=','TBL_MST_ASSETSUBLOCATION.ASLID')
            ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_TRN_ASSET_TRANSFER.WITH_EMPLOYEE','=','TBL_MST_EMPLOYEE.EMPID')
            ->select('TBL_TRN_ASSET_TRANSFER.*','TBL_MST_ASSET.*','TBL_MST_ASSET.DESCRIPTIONS AS ASTDESCRIPTIONS','TBL_MST_ASSETLOCATION.ALCODE AS ASTCODE','TBL_MST_ASSETLOCATION.DESCRIPTIONS AS ASTDES','TBL_MST_ASSETLOCATION.ALID AS ASTID','TBL_MST_ASSETSUBLOCATION.ASLID AS ASTSUBID','TBL_MST_ASSETSUBLOCATION.ASLCODE AS ASTSUBCODE','TBL_MST_ASSETSUBLOCATION.DESCRIPTIONS AS ASTSUBDES','TBL_MST_EMPLOYEE.*')
            ->first();

            $objDataList    =   DB::table('TBL_MST_ASSETSUBLOCATION')
            ->get();
            $objEmp    =   DB::table('TBL_MST_EMPLOYEE')
            ->where('STATUS','=','A')
            ->select('TBL_MST_EMPLOYEE.EMPID','TBL_MST_EMPLOYEE.EMPCODE','TBL_MST_EMPLOYEE.FNAME')
            ->get();

            return view($this->view.'view',compact(['FormId','objResponse','objRights','objHRD','objDataList','objEmp']));
        }

    }
  
    public function printdata(Request $request){
        //
        $FormId         =   $this->form_id;
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }

        $objResponse = TblMstFrm411::whereIn('ATTID',$ids_data)->get();
        
        return view($this->view.'print',compact(['FormId','objResponse']));
   }//print

    
    //display attachments form
     
    public function attachment($id){

        if(!is_null($id)){
        
            $FormId     =   $this->form_id;

            $objResponse = DB::table('TBL_TRN_ASSET_TRANSFER')->where('ASSETTRANID','=',$id)->first();

            $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
            ->where('VTID','=',$this->vtid_ref)
                ->select('VTID','VCODE','DESCRIPTIONS')
            ->get()
            ->toArray();

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

            return view($this->view.'attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments']));
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
            $TABLE      =   "TBL_MST_ATTRIBUTE";
            $FIELD      =   "ATTID";
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
   
           $USERID =   Auth::user()->USERID;
            $VTID   =   $this->vtid_ref;  //voucher type id
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');
            $FYID_REF   =   Session::get('FYID_REF');  
            $TABLE      =   "TBL_TRN_ASSET_TRANSFER";
            $FIELD      =   "ASSETTRANID";
            $ID         =   $id;
            $UPDATE     =   Date('Y-m-d');
            $UPTIME     =   Date('h:i:s.u');
            $IPADDRESS  =   $request->getClientIp();
    
            $req_data[0]=[
                'NT'  => 'TBL_TRN_ASSET_TRANSFER',
            ];
        
            $wrapped_links["TABLES"] = $req_data; 
            
            $XMLTAB = ArrayToXml::convert($wrapped_links);
            
            $mst_cancel_data = [ $USERID, $VTID, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$XMLTAB];
            $sp_result = DB::select('EXEC SP_TRN_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);
   
            if($sp_result[0]->RESULT=="CANCELED"){  
              
              return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
            
            }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
            
                
                return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
                
            }else{
               
                   return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'invalid'=>'invalid']);
            }
            
            exit(); 
   
   
       }


       public function getemplCode(Request $request){

        $Status = "A";
        $VTID   =   $this->vtid_ref;  //voucher type id
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');  

        $ObjData = DB::table('TBL_MST_ASSET')
        ->select('TBL_MST_ASSET.*','TBL_MST_ASSET.DESCRIPTIONS AS ASTDESCRIPTIONS')
        ->get();

        //dd($ObjData);

        if(!empty($ObjData)){
        foreach ($ObjData as $index=>$dataRow){

            $row = '';
            $row = $row.'<tr >
            <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_MACHINEID_REF[]" id="subgl_'.$dataRow->ASSETID .'"  class="clsemp" value="'.$dataRow->ASSETID.'" ></td>
            <td width="39%" class="ROW2">'.$dataRow->ASSETCODE;
            $row = $row.'<input type="hidden" id="txtsubgl_'.$dataRow->ASSETID.'" data-desc="'.$dataRow->ASSETCODE .'" data-ccname="'.$dataRow->ASTDESCRIPTIONS.'" data-asgid="'.$dataRow->ASGID_REF.'" data-astid="'.$dataRow->ASTID_REF.'" value="'.$dataRow->ASSETID.'"/></td>';
            $row = $row.'<td width="39%" class="ROW3">'.$dataRow->ASTDESCRIPTIONS.'</td>';
            $row = $row.'</tr>';
            echo $row;
        }
        }else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
        }
        exit();

    }

   
    


    public function getassetno(Request $request){

        $Status = "A";
        $VTID   =   $this->vtid_ref;  //voucher type id
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');  

        $ObjData = DB::table('TBL_MST_ASSETSUBLOCATION')
        ->leftJoin('TBL_MST_ASSETLOCATION', 'TBL_MST_ASSETSUBLOCATION.ALID_REF','=','TBL_MST_ASSETLOCATION.ALID')
        ->select('TBL_MST_ASSETLOCATION.ALCODE AS ASTCODE','TBL_MST_ASSETLOCATION.DESCRIPTIONS AS ASTDES','TBL_MST_ASSETLOCATION.ALID AS ASTID','TBL_MST_ASSETSUBLOCATION.ASLID AS ASTSUBID','TBL_MST_ASSETSUBLOCATION.ASLCODE AS ASTSUBCODE','TBL_MST_ASSETSUBLOCATION.DESCRIPTIONS AS ASTSUBDES')
        ->get();

        //dd($ObjData);

        if(!empty($ObjData)){
        foreach ($ObjData as $index=>$dataRow){

            $row = '';
            $row = $row.'<tr >
            <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECTID_REF[]" id="subgl_'.$dataRow->ASTID .'"  class="astclick" value="'.$dataRow->ASTID.'" ></td>
            <td width="39%" class="ROW2">'.$dataRow->ASTCODE;
            $row = $row.'<input type="hidden" id="txtsubgl_'.$dataRow->ASTID.'" data-desc="'.$dataRow->ASTCODE .'" data-ccdes="'.$dataRow->ASTSUBCODE.'" data-ccsubdes="'.$dataRow->ASTSUBDES.'" data-astrefid="'.$dataRow->ASTSUBID.'" value="'.$dataRow->ASTID.'"/></td>';
            $row = $row.'<td width="39%" class="ROW3">'.$dataRow->ASTDES.'</td>';
            $row = $row.'</tr>';
            echo $row;
        }
        }else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
        }
        exit();

    }

    public function getastempCode(Request $request){

        $Status = "A";
        $VTID   =   $this->vtid_ref;  //voucher type id
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');  

        $ObjData = DB::table('TBL_MST_EMPLOYEE')
        ->select('TBL_MST_EMPLOYEE.EMPID','TBL_MST_EMPLOYEE.EMPCODE','TBL_MST_EMPLOYEE.FNAME')
        ->get();

        //dd($ObjData);

        if(!empty($ObjData)){
        foreach ($ObjData as $index=>$dataRow){

            $row = '';
            $row = $row.'<tr >
            <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECTID_REF[]" id="subgl_'.$dataRow->EMPID .'"  class="astclick" value="'.$dataRow->EMPID.'" ></td>
            <td width="39%" class="ROW2">'.$dataRow->EMPCODE;
            $row = $row.'<input type="hidden" id="txtsubgl_'.$dataRow->EMPID.'" data-desc="'.$dataRow->EMPCODE .'" value="'.$dataRow->EMPID.'"/></td>';
            $row = $row.'<td width="39%" class="ROW3">'.$dataRow->FNAME.'</td>';
            $row = $row.'</tr>';
            echo $row;
        }
        }else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
        }
        exit();

    }


    


}
