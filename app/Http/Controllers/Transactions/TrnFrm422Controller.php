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

class TrnFrm422Controller extends Controller
{
   
    protected $form_id = 422;
    protected $vtid_ref   = 239;  //voucher type id
    protected $view     = "transactions.Payroll.EmployeeDeclaration.trnfrm";
  
       
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

        $objDataList = DB::table('TBL_MST_EMPLOYEE_DECLARATION')
        ->select('TBL_MST_EMPLOYEE_DECLARATION.*')
        ->orderBy('TBL_MST_EMPLOYEE_DECLARATION.EMP_DECLAREID', 'DESC')
        ->get();
        return view($this->view.$FormId,compact(['objRights','objDataList','FormId']));
    }

    public function add(){ 

        $FormId   =   $this->form_id;
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $objList      = $this->payperiod();
        $objDataList  = $this->employee();
        $objEarnHead  = $this->EaringHead();
        $objFYear     = $this->FinancialYear();
        $objGnder     = $this->Gender();
        $objSection   = $this->Section();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_MST_EMPLOYEE_DECLARATION',
            'HDR_ID'=>'EMP_DECLAREID',
            'HDR_DOC_NO'=>'DOCNO',
            'HDR_DOC_DT'=>'DOCDT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
       
        
         

        return view($this->view.$FormId.'add',compact(['FormId','objList','objSection','objEarnHead','objDataList','objFYear','objGnder','doc_req','docarray'])); 
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

   public function save(Request $request){

        $data = array();
        if(isset($_REQUEST['PAYPERIOD_REF']) && !empty($_REQUEST['PAYPERIOD_REF'])){
            foreach($_REQUEST['PAYPERIOD_REF'] as $key=>$val){

                $data[] = array(
                'PAYPERIOD'     => trim($_REQUEST['PAYPERIOD_REF'][$key]),
                'RENT'        => trim($_REQUEST['AMOUNT'][$key]),
                );
            }
        }
        //dd($data);

        if(!empty($data)){
            $wrapped_linkss["RRENT"] = $data; 
            $XMLRENT = ArrayToXml::convert($wrapped_linkss);
        }
        else{
            $XMLRENT = NULL; 
        }

        $datasaveing = array();
        if(isset($_REQUEST['SECTION_REF']) && !empty($_REQUEST['SECTION_REF'])){
            foreach($_REQUEST['SECTION_REF'] as $key=>$val){

                $datasaveing[] = array(
                'SECTION'     => trim($_REQUEST['SECTION_REF'][$key]),
                'AMOUNT'       => trim($_REQUEST['AMOUNT'][$key]),
                'REMARKS'       => trim($_REQUEST['REMARKS'][$key]),
                );
            }
        }
        //dd($datasaveing);

        if(!empty($datasaveing)){
            $wrapped_links["SAVING"] = $datasaveing; 
            $XMLSAVING = ArrayToXml::convert($wrapped_links);
        }
        else{
            $XMLSAVING = NULL; 
        }


        $DOCNO              =   trim($request['DOC_NO']);
        $DOCDT              =   trim($request['REMB_DT']);
        $FYID               =   trim($request['FYID_REF']);
        $EMPID_REF          =   trim($request['EMPID_REF']);
        $FLAT               =   trim($request['FLAT']);
        $RESIDENCE          =   trim($request['RESIDENCE']);
        $SENIOR_CITIZEN     =   trim($request['SENIORCITIZEN']);
        $EX_DEFENCE         =   trim($request['EXDEFENCEPER']);
        $GENDER             =   trim($request['GENDER_REF']);
        $PANNO              =   trim($request['PANNO']);
        $HANDICAPPED        =   trim($request['HANDICAPPED']);
        $CAST_CATEGORY      =   trim($request['CAST_CATEGORY']);
        $MARITAL_STATUS     =   trim($request['MARITAL_STATUS']);
        $KIDS_NO            =   trim($request['KIDSNO']);
        $OTHER_INCOME       =   trim($request['OTHER_INCOME']);
        $FLAT_OWNERNAME     =   trim($request['NAMEOFLATOWNER']);
        $FLAT_FATHERNAME    =   trim($request['FLAT_FATHERNAME']);


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
                    $DOCNO,             $DOCDT,             $FYID,          $EMPID_REF, $FLAT,         $RESIDENCE,
                    $FLAT_OWNERNAME,    $SENIOR_CITIZEN,    $EX_DEFENCE,    $GENDER,    $PANNO,        $HANDICAPPED,  $CAST_CATEGORY,
                    $MARITAL_STATUS,    $KIDS_NO,           $OTHER_INCOME,  $FLAT_FATHERNAME,          $CYID_REF,     $BRID_REF,     $FYID_REF,     $XMLRENT, 
                    $XMLSAVING,         $VTID,              $USERID,        $UPDATE,    $UPTIME,       $ACTION,       $IPADDRESS 
                             
                    ];

        $sp_result = DB::select('EXEC SP_EMPLOYEE_DECLARATION_IN  ?,?,?,?,?,?, ?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?', $array_data);

        if($sp_result){
            return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);

        }else{
            return Response::json(['errors' =>true,'msg' => 'Record Error.']);
        }
        
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

            $objResponse = DB::table('TBL_MST_EMPLOYEE_DECLARATION')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('EMP_DECLAREID','=',$id)
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

            $objList      = $this->payperiod();
            $objDataList  = $this->employee();
            $objEarnHead  = $this->EaringHead();
            $objFYear     = $this->FinancialYear();
            $objGnder     = $this->Gender();
            $objSection   = $this->Section();
            
            $HDR = DB::table('TBL_MST_EMPLOYEE_DECLARATION')
            ->where('TBL_MST_EMPLOYEE_DECLARATION.EMP_DECLAREID','=',$id)
            ->leftJoin('TBL_MST_FYEAR', 'TBL_MST_EMPLOYEE_DECLARATION.FYID_REF','=','TBL_MST_FYEAR.FYID')
            ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_MST_EMPLOYEE_DECLARATION.EMPID_REF','=','TBL_MST_EMPLOYEE.EMPID')
            ->leftJoin('TBL_MST_GENDER', 'TBL_MST_EMPLOYEE_DECLARATION.GENDER','=','TBL_MST_GENDER.GID')
            ->select('TBL_MST_EMPLOYEE_DECLARATION.*','TBL_MST_FYEAR.*','TBL_MST_EMPLOYEE.*','TBL_MST_GENDER.*')
            ->first();

            $MAT = DB::table('TBL_MST_EMPLOYEE_DECLARATION_RENT')                    
            ->where('TBL_MST_EMPLOYEE_DECLARATION_RENT.EMP_DECLAREID_REF','=',$id)
            ->leftJoin('TBL_MST_PAY_PERIOD', 'TBL_MST_EMPLOYEE_DECLARATION_RENT.PAYPID_REF','=','TBL_MST_PAY_PERIOD.PAYPERIODID')
            ->select('TBL_MST_EMPLOYEE_DECLARATION_RENT.*','TBL_MST_PAY_PERIOD.*')
            ->get()->toArray();

            $objCount = count($MAT); 

            $MAT1 = DB::table('TBL_MST_EMPLOYEE_DECLARATION_SAVING')                    
            ->where('TBL_MST_EMPLOYEE_DECLARATION_SAVING.EMP_DECLAREID_REF','=',$id)
            ->leftJoin('TBL_MST_SECTION', 'TBL_MST_EMPLOYEE_DECLARATION_SAVING.SECTIONID_REF','=','TBL_MST_SECTION.SECTIONID')
            ->select('TBL_MST_EMPLOYEE_DECLARATION_SAVING.*','TBL_MST_SECTION.*')
            ->get()->toArray();

            $objCount1 = count($MAT1); 

            return view($this->view.$FormId.'edit',compact(['FormId','objResponse','HDR','objList','MAT1','objFYear','objGnder','objSection','objDataList','objEarnHead','user_approval_level','objCount1','objRights','MAT','objCount']));
        }

    }

     
    public function update(Request $request)
    {

        $data = array();
        if(isset($_REQUEST['PAYPERIOD_REF']) && !empty($_REQUEST['PAYPERIOD_REF'])){
            foreach($_REQUEST['PAYPERIOD_REF'] as $key=>$val){

                $data[] = array(
                'PAYPERIOD'     => trim($_REQUEST['PAYPERIOD_REF'][$key]),
                'RENT'        => trim($_REQUEST['AMOUNT'][$key]),
                );
            }
        }
        //dd($data);

        if(!empty($data)){
            $wrapped_linkss["RRENT"] = $data; 
            $XMLRENT = ArrayToXml::convert($wrapped_linkss);
        }
        else{
            $XMLRENT = NULL; 
        }

        $datasaveing = array();
        if(isset($_REQUEST['SECTION_REF']) && !empty($_REQUEST['SECTION_REF'])){
            foreach($_REQUEST['SECTION_REF'] as $key=>$val){

                $datasaveing[] = array(
                'SECTION'     => trim($_REQUEST['SECTION_REF'][$key]),
                'AMOUNT'       => trim($_REQUEST['AMOUNT'][$key]),
                'REMARKS'       => trim($_REQUEST['REMARKS'][$key]),
                );
            }
        }
        //dd($datasaveing);

        if(!empty($datasaveing)){
            $wrapped_links["SAVING"] = $datasaveing; 
            $XMLSAVING = ArrayToXml::convert($wrapped_links);
        }
        else{
            $XMLSAVING = NULL; 
        }


        $DOCNO              =   trim($request['DOC_NO']);
        $DOCDT              =   trim($request['REMB_DT']);
        $FYID               =   trim($request['FYID_REF']);
        $EMPID_REF          =   trim($request['EMPID_REF']);
        $FLAT               =   trim($request['FLAT']);
        $RESIDENCE          =   trim($request['RESIDENCE']);
        $SENIOR_CITIZEN     =   trim($request['SENIORCITIZEN']);
        $EX_DEFENCE         =   trim($request['EXDEFENCEPER']);
        $GENDER             =   trim($request['GENDER_REF']);
        $PANNO              =   trim($request['PANNO']);
        $HANDICAPPED        =   trim($request['HANDICAPPED']);
        $CAST_CATEGORY      =   trim($request['CAST_CATEGORY']);
        $MARITAL_STATUS     =   trim($request['MARITAL_STATUS']);
        $KIDS_NO            =   trim($request['KIDSNO']);
        $OTHER_INCOME       =   trim($request['OTHER_INCOME']);
        $FLAT_OWNERNAME     =   trim($request['NAMEOFLATOWNER']);
        $FLAT_FATHERNAME    =   trim($request['FLAT_FATHERNAME']);


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
                    $DOCNO,             $DOCDT,             $FYID,          $EMPID_REF, $FLAT,         $RESIDENCE,
                    $FLAT_OWNERNAME,    $SENIOR_CITIZEN,    $EX_DEFENCE,    $GENDER,    $PANNO,        $HANDICAPPED,  $CAST_CATEGORY,
                    $MARITAL_STATUS,    $KIDS_NO,           $OTHER_INCOME,  $FLAT_FATHERNAME,          $CYID_REF,     $BRID_REF,     $FYID_REF,     $XMLRENT, 
                    $XMLSAVING,         $VTID,              $USERID,        $UPDATE,    $UPTIME,       $ACTION,       $IPADDRESS 
                             
                    ];

        $sp_result = DB::select('EXEC SP_EMPLOYEE_DECLARATION_UP  ?,?,?,?,?,?, ?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?', $array_data);

        if($sp_result){
            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

        }else{
            return Response::json(['errors' =>true,'msg' => 'Record Error.']);
        }
        
        exit();
                  
    } 

    //uploads attachments files
    public function docuploads(Request $request){

        $formData = $request->all();
        $FormId     =   $this->form_id;

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
 
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/AssignLeave";

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
            return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Already file uploaded");
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

            return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
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
           


            $data = array();
            if(isset($_REQUEST['PAYPERIOD_REF']) && !empty($_REQUEST['PAYPERIOD_REF'])){
                foreach($_REQUEST['PAYPERIOD_REF'] as $key=>$val){

                    $data[] = array(
                    'PAYPERIOD'     => trim($_REQUEST['PAYPERIOD_REF'][$key]),
                    'RENT'        => trim($_REQUEST['AMOUNT'][$key]),
                    );
                }
            }
            //dd($data);

            if(!empty($data)){
                $wrapped_linkss["RRENT"] = $data; 
                $XMLRENT = ArrayToXml::convert($wrapped_linkss);
            }
            else{
                $XMLRENT = NULL; 
            }

            $datasaveing = array();
            if(isset($_REQUEST['SECTION_REF']) && !empty($_REQUEST['SECTION_REF'])){
                foreach($_REQUEST['SECTION_REF'] as $key=>$val){

                    $datasaveing[] = array(
                    'SECTION'     => trim($_REQUEST['SECTION_REF'][$key]),
                    'AMOUNT'       => trim($_REQUEST['AMOUNT'][$key]),
                    'REMARKS'       => trim($_REQUEST['REMARKS'][$key]),
                    );
                }
            }
            //dd($datasaveing);

            if(!empty($datasaveing)){
                $wrapped_links["SAVING"] = $datasaveing; 
                $XMLSAVING = ArrayToXml::convert($wrapped_links);
            }
            else{
                $XMLSAVING = NULL; 
            }


            $DOCNO              =   trim($request['DOC_NO']);
            $DOCDT              =   trim($request['REMB_DT']);
            $FYID               =   trim($request['FYID_REF']);
            $EMPID_REF          =   trim($request['EMPID_REF']);
            $FLAT               =   trim($request['FLAT']);
            $RESIDENCE          =   trim($request['RESIDENCE']);
            $SENIOR_CITIZEN     =   trim($request['SENIORCITIZEN']);
            $EX_DEFENCE         =   trim($request['EXDEFENCEPER']);
            $GENDER             =   trim($request['GENDER_REF']);
            $PANNO              =   trim($request['PANNO']);
            $HANDICAPPED        =   trim($request['HANDICAPPED']);
            $CAST_CATEGORY      =   trim($request['CAST_CATEGORY']);
            $MARITAL_STATUS     =   trim($request['MARITAL_STATUS']);
            $KIDS_NO            =   trim($request['KIDSNO']);
            $OTHER_INCOME       =   trim($request['OTHER_INCOME']);
            $FLAT_OWNERNAME     =   trim($request['NAMEOFLATOWNER']);
            $FLAT_FATHERNAME    =   trim($request['FLAT_FATHERNAME']);


            $CYID_REF       =   Auth::user()->CYID_REF;
            $BRID_REF       =   Session::get('BRID_REF');
            $FYID_REF       =   Session::get('FYID_REF');
            $VTID           =   $this->vtid_ref;
            $USERID         =   Auth::user()->USERID;
            $UPDATE         =   Date('Y-m-d');
            $UPTIME         =   Date('h:i:s.u');
            $ACTION         =   $Approvallevel;
            $IPADDRESS      =   $request->getClientIp();
            
            $array_data   = [
                        $DOCNO,             $DOCDT,             $FYID,          $EMPID_REF, $FLAT,         $RESIDENCE,
                        $FLAT_OWNERNAME,    $SENIOR_CITIZEN,    $EX_DEFENCE,    $GENDER,    $PANNO,        $HANDICAPPED,  $CAST_CATEGORY,
                        $MARITAL_STATUS,    $KIDS_NO,           $OTHER_INCOME,  $FLAT_FATHERNAME,          $CYID_REF,     $BRID_REF,     $FYID_REF,     $XMLRENT, 
                        $XMLSAVING,         $VTID,              $USERID,        $UPDATE,    $UPTIME,       $ACTION,       $IPADDRESS 
                                
                        ];

            $sp_result = DB::select('EXEC SP_EMPLOYEE_DECLARATION_UP  ?,?,?,?,?,?, ?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?', $array_data);
            
            if($sp_result){
                return Response::json(['success' =>true,'msg' => 'Record successfully Approved.']);
    
            }else{
                return Response::json(['errors' =>true,'msg' => 'Record Error.']);
            }
            
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

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objResponse = DB::table('TBL_MST_EMPLOYEE_DECLARATION')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('EMP_DECLAREID','=',$id)
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

            $objList      = $this->payperiod();
            $objDataList  = $this->employee();
            $objEarnHead  = $this->EaringHead();
            $objFYear     = $this->FinancialYear();
            $objGnder     = $this->Gender();
            $objSection   = $this->Section();
            
            $HDR = DB::table('TBL_MST_EMPLOYEE_DECLARATION')
            ->where('TBL_MST_EMPLOYEE_DECLARATION.EMP_DECLAREID','=',$id)
            ->leftJoin('TBL_MST_FYEAR', 'TBL_MST_EMPLOYEE_DECLARATION.FYID_REF','=','TBL_MST_FYEAR.FYID')
            ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_MST_EMPLOYEE_DECLARATION.EMPID_REF','=','TBL_MST_EMPLOYEE.EMPID')
            ->leftJoin('TBL_MST_GENDER', 'TBL_MST_EMPLOYEE_DECLARATION.GENDER','=','TBL_MST_GENDER.GID')
            ->select('TBL_MST_EMPLOYEE_DECLARATION.*','TBL_MST_FYEAR.*','TBL_MST_EMPLOYEE.*','TBL_MST_GENDER.*')
            ->first();

            $MAT = DB::table('TBL_MST_EMPLOYEE_DECLARATION_RENT')                    
            ->where('TBL_MST_EMPLOYEE_DECLARATION_RENT.EMP_DECLAREID_REF','=',$id)
            ->leftJoin('TBL_MST_PAY_PERIOD', 'TBL_MST_EMPLOYEE_DECLARATION_RENT.PAYPID_REF','=','TBL_MST_PAY_PERIOD.PAYPERIODID')
            ->select('TBL_MST_EMPLOYEE_DECLARATION_RENT.*','TBL_MST_PAY_PERIOD.*')
            ->get()->toArray();

            $objCount = count($MAT); 

            $MAT1 = DB::table('TBL_MST_EMPLOYEE_DECLARATION_SAVING')                    
            ->where('TBL_MST_EMPLOYEE_DECLARATION_SAVING.EMP_DECLAREID_REF','=',$id)
            ->leftJoin('TBL_MST_SECTION', 'TBL_MST_EMPLOYEE_DECLARATION_SAVING.SECTIONID_REF','=','TBL_MST_SECTION.SECTIONID')
            ->select('TBL_MST_EMPLOYEE_DECLARATION_SAVING.*','TBL_MST_SECTION.*')
            ->get()->toArray();

            $objCount1 = count($MAT1); 

            return view($this->view.$FormId.'view',compact(['FormId','objResponse','HDR','objList','MAT1','objFYear','objGnder','objSection','objDataList','objEarnHead','user_approval_level','objCount1','objRights','MAT','objCount']));
        }

    }
      
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objResponse = DB::table('TBL_MST_EMPLOYEE_DECLARATION')->where('EMP_DECLAREID','=',$id)->select('*')->first();

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
                 $FormId         =   $this->form_id;

                 return view($this->view.$FormId.'attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments']));
        }

    }
    
        //Cancel the data
        public function cancel(Request $request){

            $id = $request->{0};
   
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;  //voucher type id
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');
            $FYID_REF   =   Session::get('FYID_REF');  
            $TABLE      =   "TBL_MST_EMPLOYEE_DECLARATION";
            $FIELD      =   "EMP_DECLAREID";
            $ID         =   $id;
            $UPDATE     =   Date('Y-m-d');
            $UPTIME     =   Date('h:i:s.u');
            $IPADDRESS  =   $request->getClientIp();
    
            $req_data[0]=[
                'NT'  => 'TBL_MST_EMPLOYEE_DECLARATION_RENT',
            ];
            $req_data[1]=[
                'NT'  => 'TBL_MST_EMPLOYEE_DECLARATION_SAVING',
            ];
            
        
            $wrapped_links["TABLES"] = $req_data; 
            
            $XMLTAB = ArrayToXml::convert($wrapped_links);
            
            $mst_cancel_data = [ $USERID, $VTID, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$XMLTAB];
            $sp_result = DB::select('EXEC SP_TRN_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);
   
            //dd($sp_result);

            if($sp_result[0]->RESULT=="CANCELED"){  
              
              return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
            
            }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
            
                
                return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
                
            }else{
               
                   return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'invalid'=>'invalid']);
            }
            
            exit(); 
   
   
       }

        public function payperiod(){
        $objpayperiod = DB::table('TBL_MST_PAY_PERIOD')
        ->select('PAYPERIODID','PAY_PERIOD_CODE','PAY_PERIOD_DESC')
        ->get();
        return $objpayperiod; 
        }
        public function employee(){
        $objemp    =   DB::table('TBL_MST_EMPLOYEE')
        ->where('STATUS','=','A')
        ->get();
        return $objemp; 
        }
        public function  EaringHead(){
        $EarnHead    =   DB::table('TBL_MST_EARNING_HEAD')
        ->where('STATUS','=','A')
        ->get();
        return $EarnHead; 
        }
        public function FinancialYear(){
            $objfYear = DB::table('TBL_MST_FYEAR')
            ->select('FYID','FYCODE','FYDESCRIPTION')
            ->get();
            return $objfYear; 
            }
        public function  Gender(){
            $Gendr    =   DB::table('TBL_MST_GENDER')
            ->where('STATUS','=','A')
            ->get();
            return $Gendr; 
            }

        public function  Section(){
            $Section    =   DB::table('TBL_MST_SECTION')->get();
            return $Section; 
            }
    
            public function getFYearName(Request $request){
    
                $FYID          =   $request['FYID'];
                
                $objFyear = DB::table('TBL_MST_FYEAR')
                ->where('FYID','=', $FYID )
                ->select('FYDESCRIPTION')
                ->first();
                
                if(!empty($objFyear)){
                    echo $objFyear->FYDESCRIPTION;
                }
                else{
                    echo "";
                }
                exit();
            }
            
        public function getEmpName(Request $request){
            
            $EMPID          =   $request['EMPID'];
            $objEmpName = DB::table('TBL_MST_EMPLOYEE')
            ->where('EMPID','=', $EMPID )
            ->select('FNAME')
            ->first();
            
            if(!empty($objEmpName)){
                echo $objEmpName->FNAME;
            }
            else{
                echo "";
            }
            exit();
        }
            
            
        public function getEearHeadName(Request $request){
            
            $EARNING_HEADID          =   $request['EARNING_HEADID'];
            $objEarnName = DB::table('TBL_MST_EARNING_HEAD')
            ->where('EARNING_HEADID','=', $EARNING_HEADID )
            ->select('EARNING_HEAD_DESC')
            ->first();
            
            if(!empty($objEarnName)){
                echo $objEarnName->EARNING_HEAD_DESC;
            }
            else{
                echo "";
            }
            exit();
        }
            
            
        public function getLeaveTyName(Request $request){
            
            $LTID          =   $request['LTID'];
            
            $objLeaveTyName = DB::table('TBL_MST_LEAVE_TYPE')
            ->where('LTID','=', $LTID )
            ->select('LEAVETYPE_DESC')
            ->first();
            
            if(!empty($objLeaveTyName)){
                echo $objLeaveTyName->LEAVETYPE_DESC;
            }
            else{
                echo "";
            }
            exit();
        }
  
}
