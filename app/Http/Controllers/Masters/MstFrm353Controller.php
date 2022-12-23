<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm353;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;


class MstFrm353Controller extends Controller
{
   
    protected $form_id = 353;
    protected $vtid_ref   = 214;  //voucher type id    
    
    
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

        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');     

        $objDataList	=	DB::select("SELECT * FROM TBL_MST_DEFINITION WHERE CYID_REF='$CYID_REF'  ORDER BY DEFINID DESC");


        return view('masters.Payroll.Definition.mstfrm353',compact(['objRights','objDataList']));

    }   

    
    public function add(){ 

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');


        $objHeadCodeList    =   DB::table('TBL_MST_DEDUCTION_HEAD')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->get();

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

        return view('masters.Payroll.Definition.mstfrm353add',compact(['objDD','objHeadCodeList','objDOCNO']));
    }  

   public function save(Request $request){    

        $r_count = $request['Row_Count'];
        for ($i=0; $i<=$r_count; $i++)
        {
            if(isset($request['PARTICULAR_'.$i]))
            {
                $data[$i] = [
                    'PARTICULAR'    => strtoupper($request['PARTICULAR_'.$i]),
                    'EARNING_DEDUCTION' => $request['EARNING_DEDUCTION_'.$i],
                    'EARNING_DEDUCTION_HEAD' => $request['EARNING_DEDUCTION_HEAD_'.$i],
                ];
            }
        }

        //dd($data);

        if(!empty($data)){ 
            $wrapped_links["DEFINITION"] = $data; 
            $xml = ArrayToXml::convert($wrapped_links);
        }
        else{
            $xml = NULL; 
        }  

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
            $CYID_REF, $BRID_REF, $FYID_REF, $xml,$VTID,$USERID,$UPDATE,$UPTIME,$ACTION,$IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_DEFINITION_INUPDE ?,?,?,?,?,?,?,?,?,?', $array_data);

        return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);

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

            $objResponse = DB::table('TBL_MST_DEFINITION')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('DEFINID','=',$id)
                ->select('*')
                ->first();

                $objHeadCodeList    =   DB::table('TBL_MST_DEDUCTION_HEAD')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->get();

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

            $objDataResponse = DB::table('TBL_MST_DEFINITION')                    
                             ->where('TBL_MST_DEFINITION.DEFINID','=',$id)
                             ->select('TBL_MST_DEFINITION.*')
                             ->get()->toArray();

            $objCount = count($objDataResponse);

            return view('masters.Payroll.Definition.mstfrm353edit',compact(['objResponse','user_approval_level','objHeadCodeList','objRights','objDataResponse','objCount']));
        }

    }

     
    public function update(Request $request)
    {

        // dd($request->all());

        $r_count = $request['Row_Count'];
        for ($i=0; $i<=$r_count; $i++)
        {
            if(isset($request['PARTICULAR_'.$i]))
            {
                $data[$i] = [
                    'PARTICULAR'    => strtoupper($request['PARTICULAR_'.$i]),
                    'EARNING_DEDUCTION' => $request['EARNING_DEDUCTION_'.$i],
                    'EARNING_DEDUCTION_HEAD' => $request['EARNING_DEDUCTION_HEAD_'.$i],
                ];
            }
        }

            //dd($data);

            if(!empty($data)){ 
                $wrapped_links["DEFINITION"] = $data; 
                $xml = ArrayToXml::convert($wrapped_links);
            }
            else{
                $xml = NULL; 
            }  

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
                    $CYID_REF, $BRID_REF, $FYID_REF, $xml,$VTID,$USERID,$UPDATE,$UPTIME,$ACTION,$IPADDRESS
                ];

                //dd($array_data);

            $sp_result = DB::select('EXEC SP_DEFINITION_INUPDE ?,?,?,?,?,?,?,?,?,?', $array_data);

            return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);

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
 
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/Definition";

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
            return redirect()->route("master",[353,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
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
          
             $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);
     
        if($sp_result[0]->RESULT=="SUCCESS"){

            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }

            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }

            return redirect()->route("master",[353,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[353,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("master",[353,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
            return redirect()->route("master",[353,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   


    //singleApprove begin
    
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
           
            $r_count = $request['Row_Count'];
                for ($i=0; $i<=$r_count; $i++)
                {
                    if(isset($request['PARTICULAR_'.$i]))
                    {
                        $data[$i] = [
                            'PARTICULAR'    => strtoupper($request['PARTICULAR_'.$i]),
                            'EARNING_DEDUCTION' => $request['EARNING_DEDUCTION_'.$i],
                            'EARNING_DEDUCTION_HEAD' => $request['EARNING_DEDUCTION_HEAD_'.$i],
                        ];
                    }
                }

                if(!empty($data)){ 
                    $wrapped_links["DEFINITION"] = $data; 
                    $xml = ArrayToXml::convert($wrapped_links);
                }
                else{
                    $xml = NULL; 
                }


                $CYID_REF       =   Auth::user()->CYID_REF;
                $BRID_REF       =   Session::get('BRID_REF');
                $FYID_REF       =   Session::get('FYID_REF');       
                $VTID           =   $this->vtid_ref;
                $USERID         =   Auth::user()->USERID;
                $UPDATE         =   Date('Y-m-d');
                $UPTIME         =   Date('h:i:s.u');
                $ACTION = $Approvallevel;
                $IPADDRESS      =   $request->getClientIp();
                
                
                $array_data   = [
                    $CYID_REF, $BRID_REF, $FYID_REF, $xml,$VTID,$USERID,$UPDATE,$UPTIME,$ACTION,$IPADDRESS
                ];              
               
                
                $sp_result = DB::select('EXEC SP_DEFINITION_INUPDE ?,?,?,?,?,?,?,?,?,?', $array_data);
                
                //dd($sp_result);
                    
                return Response::json(['success' =>true,'msg' => 'Record successfully approved.']);
                exit();            


        }
    
    


    public function view($id){

        if(!is_null($id))
        {

            $objDataResponse = DB::table('TBL_MST_DEFINITION')                    
                ->where('TBL_MST_DEFINITION.DEFINID','=',$id)
                ->select('TBL_MST_DEFINITION.*')
                ->get()->toArray();

                $objHeadCodeList    =   DB::table('TBL_MST_DEDUCTION_HEAD')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->get();        

            return view('masters.Payroll.Definition.mstfrm353view',compact(['objDataResponse','objHeadCodeList']));
        }

    }
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }

        $objResponse = TblMstFrm353::whereIn('ATTID',$ids_data)->get();
        
        return view('masters.Payroll.Definition.mstfrm353print',compact(['objResponse']));
   }//print


    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objResponse = DB::table('TBL_MST_DEFINITION')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('DEFINID','=',$id)
                ->select('*')
                ->first();

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

            return view('masters.Payroll.Definition.mstfrm353attachment',compact(['objResponse','objMstVoucherType','objAttachments']));
        }

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
        $TABLE      =   "TBL_MST_DEFINITION";
        $FIELD      =   "DEFINID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
        
        $cancelData[0]= ['NT' =>'TBL_MST_DEFINITION'];
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


}
