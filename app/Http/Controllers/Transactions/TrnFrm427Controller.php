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

class TrnFrm427Controller extends Controller
{
   
    protected $form_id = 427;
    protected $vtid_ref   = 497;  //voucher type id
    protected $view     = "transactions.Payroll.SalaryProcess.trnfrm";

       
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

        $objDataList = DB::table('TBL_TRN_SALARY_PROCESS')
            ->orderBy('SALARYPRID', 'DESC')
            ->get();

        return view($this->view.$FormId,compact(['objRights','objDataList','FormId']));

    }

    public function add(){

        $FormId         =   $this->form_id;
        $Status = "A"; 
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $monthList = $this->objMonth();
        $yearList = $this->objYear();
        $department =   $this->department();
        $employee =   $this->employee();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_SALARY_PROCESS',
            'HDR_ID'=>'SALARYPRID',
            'HDR_DOC_NO'=>'DOCNO',
            'HDR_DOC_DT'=>'DOCDATE'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

          

            return view($this->view.$FormId.'add',compact(['FormId','monthList','yearList','department','employee','doc_req','docarray'])); 
            }
  
        public function codeduplicate(Request $request){

                $CYID_REF = Auth::user()->CYID_REF;
                $BRID_REF = Session::get('BRID_REF');
                $FYID_REF = Session::get('FYID_REF');
                $LEAVE_APP_NO =   $request['LEAVE_APP_NO'];
                
                $objLabel = DB::table('TBL_MST_LOAN_DISBURSEMENT')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('LEAVE_APP_NO','=',$LEAVE_APP_NO)
                ->select('LEAVE_APP_NO')
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

                $SNP_NO         =   trim($request['DOCNO']);
                $SNP_DT         =   trim($request['DOCDT']);
                $MTID_REF       =   trim($request['MONTH_REF']);
                $YRID_REF       =   trim($request['YEAR_REF']);
                $PROCESS_TYPE   =   (isset($request['TEXT_DID_REF'])=="true" ? "DEPARTMENT" : "EMPLOYEE");
                $PROCESS_LIST   =   (isset($request['TEXT_DID_REF'])=="true" ? trim($request['DID_REF']) : trim($request['EMPID_REF']));
                
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
                            $SNP_NO,$SNP_DT,$MTID_REF,$YRID_REF,$CYID_REF,$BRID_REF,$FYID_REF,$VTID,$USERID,$UPDATE,            
                            $UPTIME,$ACTION,$IPADDRESS,$PROCESS_TYPE,$PROCESS_LIST         
                            ];

                    //dd($array_data);

                $sp_result = DB::select('EXEC SP_SNP_IN ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?', $array_data);

                $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
                if($contains){
                    return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

                }else{
                    return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
                }
                exit();   
            }

            public function edit($id){

                if(!is_null($id))
                {
                
                    $FormId         =   $this->form_id;
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

                    $objResponse = DB::table('TBL_MST_LOAN_DISBURSEMENT')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('LOAN_DISBURSEID','=',$id)
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

                    $monthList = $this->objMonth();
                    $yearList = $this->objYear();

                    return view($this->view.$FormId.'edit',compact(['FormId','objResponse','monthList','yearList','user_approval_level','objRights']));
                }

            }

     
            public function update(Request $request)
            {



                $LOAN_DISB_DOCNO            =   trim($request['LOAN_DISB_DOCNO']);
                $LOAN_DISB_DOCDT            =   trim($request['LOAN_DISB_DOCDT']);
                $PAYPID_REF                 =   trim($request['PAYPID_REF']);
                $EMPID_REF                  =   trim($request['EMPID_REF']);
                $LOANTYPEID_REF             =   trim($request['LOANTYPEID_REF']);
                $LOAN_DISB_AMT              =   trim($request['LOAN_DISB_AMT']);
                $NO_OF_INSTALL              =   trim($request['NO_OF_INSTALL']);
                $EMI_AMT                    =   trim($request['EMI_AMT']);
                $START_DEDUCT_PPID_REF      =   trim($request['START_DEDUCT_PPID_REF']);
                $REMARKS                    =   trim($request['REMARKS']);

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
                    $LOAN_DISB_DOCNO,       $LOAN_DISB_DOCDT,     $PAYPID_REF,      $EMPID_REF,
                    $LOANTYPEID_REF,        $LOAN_DISB_AMT,       $NO_OF_INSTALL,   $EMI_AMT,
                    $START_DEDUCT_PPID_REF, $REMARKS,             $CYID_REF,        $BRID_REF,
                    $FYID_REF,              $VTID,                $USERID,          $UPDATE,            
                    $UPTIME,                $ACTION,              $IPADDRESS          
                    ];

                //dd($array_data);

                $sp_result = DB::select('EXEC SP_LOAN_DISBURSEMENT_UP ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?', $array_data);

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
                
                    $LOAN_DISB_DOCNO            =   trim($request['LOAN_DISB_DOCNO']);
                    $LOAN_DISB_DOCDT            =   trim($request['LOAN_DISB_DOCDT']);
                    $PAYPID_REF                 =   trim($request['PAYPID_REF']);
                    $EMPID_REF                  =   trim($request['EMPID_REF']);
                    $LOANTYPEID_REF             =   trim($request['LOANTYPEID_REF']);
                    $LOAN_DISB_AMT              =   trim($request['LOAN_DISB_AMT']);
                    $NO_OF_INSTALL              =   trim($request['NO_OF_INSTALL']);
                    $EMI_AMT                    =   trim($request['EMI_AMT']);
                    $START_DEDUCT_PPID_REF      =   trim($request['START_DEDUCT_PPID_REF']);
                    $REMARKS                    =   trim($request['REMARKS']);
            

                    $CYID_REF   =   Auth::user()->CYID_REF;
                    $BRID_REF   =   Session::get('BRID_REF');
                    $FYID_REF   =   Session::get('FYID_REF');
                    $VTID       =   $this->vtid_ref;
                    $USERID     =   Auth::user()->USERID;
                    $UPDATE     =   Date('Y-m-d');
                    
                    $UPTIME     =   Date('h:i:s.u');
                    $ACTION     = $Approvallevel;
                    $IPADDRESS  =   $request->getClientIp();
                    
                    $array_data   = [
                        $LOAN_DISB_DOCNO,       $LOAN_DISB_DOCDT,     $PAYPID_REF,      $EMPID_REF,
                        $LOANTYPEID_REF,        $LOAN_DISB_AMT,       $NO_OF_INSTALL,   $EMI_AMT,
                        $START_DEDUCT_PPID_REF, $REMARKS,             $CYID_REF,        $BRID_REF,
                        $FYID_REF,              $VTID,                $USERID,          $UPDATE,            
                        $UPTIME,                $ACTION,              $IPADDRESS          
                        ];
            
                    //dd($array_data);
            
                    $sp_result = DB::select('EXEC SP_LOAN_DISBURSEMENT_UP ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?', $array_data);

                return Response::json(['success' =>true,'msg' => 'Record successfully Approved.']);               

                exit();     
            }

                public function view($id)
                {

                    $FormId         =   $this->form_id;
                    if(!is_null($id))
                    {
                        $objSP = DB::table('TBL_TRN_SALARY_PROCESS')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('SALARYPRID','=',$id)
                        ->select('*')
                        ->first();

                        $monthList = $this->objMonth();
                        $yearList = $this->objYear();

                        $log_data = [ 
                            $id
                        ];
            
                        $objSPMAT   =   array();
                        if(isset($objSP) && !empty($objSP)){
            
                            $objSPMAT = DB::select('EXEC SP_GET_SALARY_PROCESS_MATERIAL ?', $log_data);
                        }
                        
                        $objCount1 = count($objSPMAT);

                    return view($this->view.$FormId.'view',compact(['FormId','objSP','monthList','yearList','objSPMAT']));
                    
                    }
                }
  
                public function printdata(Request $request){
                    //
                    $ids_data = [];
                    if(isset($request->records_ids)){
                        
                        $ids_data = explode(",",$request->records_ids);
                    }

                    $objResponse = TblMstFrm402::whereIn('ATTID',$ids_data)->get();
                    
                    return view('transactions.Payroll.LeaveApply.trnfrm402print',compact(['objResponse']));
            }//print

    
                //display attachments form
                
                public function attachment($id){

                    if(!is_null($id)){
                    
                        $FormId     =   $this->form_id;

                        $objResponse = DB::table('TBL_MST_LOAN_DISBURSEMENT')->where('LOAN_DISBURSEID','=',$id)->first();

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

                        return view($this->view.$FormId.'attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments']));
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
                        $TABLE      =   "TBL_TRN_SALARY_PROCESS";
                        $FIELD      =   "SALARYPRID";
                        $ACTIONNAME     = $Approvallevel;
                        $UPDATE     =   Date('Y-m-d');
                        $UPTIME     =   Date('h:i:s.u');
                        $IPADDRESS  =   $request->getClientIp();
                    
                    
                    
                    // dd($xml);
                    
                    $log_data = [ 
                        $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
                    ];

                        
                    $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
                    
                    

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
                        $TABLE      =   "TBL_MST_LOAN_DISBURSEMENT";
                        $FIELD      =   "LOAN_DISBURSEID";
                        $ID         =   $id;
                        $UPDATE     =   Date('Y-m-d');
                        $UPTIME     =   Date('h:i:s.u');
                        $IPADDRESS  =   $request->getClientIp();
                
                        $req_data[0]=[
                            'NT'  => 'TBL_MST_LOAN_DISBURSEMENT',
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

        public function objMonth(){
            $month = DB::table('TBL_MST_MONTH')
            ->select('*')
            ->get();
            return $month; 
            }

        public function objYear(){
            $year = DB::table('TBL_MST_YEAR')
            ->select('*')
            ->get();
            return $year; 
            }


            public function department(){

                $CYID_REF   =   Auth::user()->CYID_REF;
                $dept   =   DB::select("SELECT * FROM TBL_MST_DEPARTMENT WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND (DEACTIVATED IS NULL OR DEACTIVATED='0')");
                return $dept;
            }

            public function getDataArray(Request $request){

                $CYID_REF    =   Auth::user()->CYID_REF;
                $DID_REF     =   $request['DID_REF'];
        
                $data       =   DB::select("SELECT * FROM TBL_MST_DEPARTMENT WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND DEPID='$DID_REF'");
        
                $row    =   '';
                if(isset($data) && !empty($data)){
                    foreach($data as $key=>$value){
                        
                        $row    .='
                        <tr class="participantRow">
                            <td hidden><input type="text" name="DEPID[]" value="'.$value->DEPID.'" class="form-control"  autocomplete="off" readonly /></td>
                            <td><input type="text" name="DCODE[]" id="DCODE_'.$key.'" value="'.$value->DCODE.'" class="form-control"  autocomplete="off" readonly /></td>
                            <td><input type="text" name="NAME[]" id="NAME_'.$key.'" value="'.$value->NAME.'" class="form-control"  autocomplete="off" readonly /></td>

                            <td align="center" ><button class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                        </tr>
                        ';
                    }
                }

                else{

                    $row    .=' <tr class="participantRow"><td colspan="10" style="text-align:center;" >No data available in table</td></tr>';
                }
                
                echo $row;
        
                exit();
            }


            public function getMaterialData(Request $request){
        
                $CYID_REF   =   Auth::user()->CYID_REF;
                $Status     =   'A';
                $item_array =   $request['item_array'];
                $type       =   $request['type'];
                
                $data       =   DB::select("SELECT * FROM TBL_MST_DEPARTMENT WHERE STATUS='A' AND CYID_REF='$CYID_REF'");

                $row        =   '';

                if(isset($data) && !empty($data)){
                    foreach($data as $key=>$value){

                        $row    .='
                        <tr class="participantRow">
                            <td hidden><input type="text" name="DEPID[]" id="DEPID_'.$key.'" value="'.$value->DEPID.'"  class="form-control"  autocomplete="off" readonly /></td>
                            <td style="width:20%;"><input type="text" name="DCODE[]" id="DCODE_'.$key.'"  value="'.$value->DCODE.'"  class="form-control"  autocomplete="off" readonly /></td>
                            <td style="width:40%;"><input type="text" name="NAME[]" id="NAME_'.$key.'" value="'.$value->NAME.'"   class="form-control"  autocomplete="off" readonly /></td>

                            <td align="center" style="width:10%;" ><button class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                        </tr>
                        ';
                    }
                }

                else{

                    $row    .=' <tr class="participantRow"><td colspan="10" style="text-align:center;" >No data available in table</td></tr>';
                }
                
                echo $row;
        
                exit();

            }
    
            public function employee(){

                $CYID_REF   =   Auth::user()->CYID_REF;
                $emp   =   DB::select("SELECT * FROM TBL_MST_EMPLOYEE WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND (DEACTIVATED IS NULL OR DEACTIVATED='0')");
                return $emp;
            }

            public function getEmployee(Request $request){

                $CYID_REF    =   Auth::user()->CYID_REF;
                $EMPID_REF     =   $request['EMPID_REF'];
        
                $data       =   DB::select("SELECT * FROM TBL_MST_EMPLOYEE WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND EMPID='$EMPID_REF'");
        
                $row    =   '';
                if(isset($data) && !empty($data)){
                    foreach($data as $key=>$value){
                        
                        $row    .='
                        <tr class="participantRow">
                            <td hidden><input type="text" name="EMPID[]" value="'.$value->EMPID.'" class="form-control"  autocomplete="off" readonly /></td>
                            <td><input type="text" name="EMPCODE[]" id="EMPCODE_'.$key.'" value="'.$value->EMPCODE.'" class="form-control"  autocomplete="off" readonly /></td>
                            <td><input type="text" name="FNAME[]" id="FNAME_'.$key.'" value="'.$value->FNAME.'" class="form-control"  autocomplete="off" readonly /></td>

                            <td align="center" ><button class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                        </tr>
                        ';
                    }
                }

                else{

                    $row    .=' <tr class="participantRow"><td colspan="10" style="text-align:center;" >No data available in table</td></tr>';
                }
                
                echo $row;
        
                exit();
            }


            public function getAllMaterialData(Request $request){
        
                $CYID_REF   =   Auth::user()->CYID_REF;
                $Status     =   'A';
                $item_array =   $request['item_array'];
                $type       =   $request['type'];
                
                $data       =   DB::select("SELECT * FROM TBL_MST_EMPLOYEE WHERE STATUS='A' AND CYID_REF='$CYID_REF'");

                $row        =   '';

                if(isset($data) && !empty($data)){
                    foreach($data as $key=>$value){

                        $row    .='
                        <tr class="participantRow">
                            <td hidden><input type="text" name="EMPID[]" id="EMPID_'.$key.'" value="'.$value->EMPID.'"  class="form-control"  autocomplete="off" readonly /></td>
                            <td style="width:20%;"><input type="text" name="EMPCODE[]" id="EMPCODE_'.$key.'"  value="'.$value->EMPCODE.'"  class="form-control"  autocomplete="off" readonly /></td>
                            <td style="width:60%;"><input type="text" name="FNAME[]" id="FNAME_'.$key.'" value="'.$value->FNAME.'"   class="form-control"  autocomplete="off" readonly /></td>

                            <td align="center" style="width:10%;"><button class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                        </tr>
                        ';
                    }
                }

                else{

                    $row    .=' <tr class="participantRow"><td colspan="10" style="text-align:center;" >No data available in table</td></tr>';
                }
                
                echo $row;
        
                exit();

            }


            

   


}
