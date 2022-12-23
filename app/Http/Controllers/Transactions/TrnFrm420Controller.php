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

class TrnFrm420Controller extends Controller
{
   
    protected $form_id = 420;
    protected $vtid_ref   = 494;  //voucher type id
    protected $view     = "transactions.Payroll.AdjustmentVoucherEmployeeWise.trnfrm";

       
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

        $objDataList = DB::table('TBL_MST_EMP_VOUCHER_ADJUSTMENT')
            ->select('TBL_MST_EMP_VOUCHER_ADJUSTMENT.*')
            ->orderBy('TBL_MST_EMP_VOUCHER_ADJUSTMENT.EMP_VAID', 'DESC')
            ->get();

            return view($this->view.$FormId,compact(['objRights','objDataList','FormId']));


    }

    public function add(){ 

        $FormId         =   $this->form_id;
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $objList      = $this->payperiod();
        $objDataList  = $this->employee();
        $objEarnHead  = $this->EaringHead();
        $objDedhedList  = $this->DedHead();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_MST_EMP_VOUCHER_ADJUSTMENT',
            'HDR_ID'=>'EMP_VAID',
            'HDR_DOC_NO'=>'EMPVA_DOCNO',
            'HDR_DOC_DT'=>'EMPVA_DOCDT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

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

        return view($this->view.$FormId.'add',compact(['FormId','objDD','objList','objEarnHead','objDedhedList','objDataList','objDOCNO','doc_req','docarray'])); 
    }
  
    public function getPayPrName(Request $request){
        
        $PAYPERIODID          =   $request['PAYPERIODID'];
		
		$objPayPrName = DB::table('TBL_MST_PAY_PERIOD')
        ->where('PAYPERIODID','=', $PAYPERIODID )
        ->select('PAY_PERIOD_DESC')
        ->first();
		
		if(!empty($objPayPrName)){
			echo $objPayPrName->PAY_PERIOD_DESC;
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


    public function getEarHeadName(Request $request){
        
        $EARNINGVALUE          =   $request['EARNINGVALUE'];

        if($EARNINGVALUE=='Earning Head'){

            $objEarnName = DB::table('TBL_MST_EARNING_HEAD')->get();
           
            foreach ($objEarnName as $index=>$dataRow){

                echo '<option value="'.$dataRow->EARNING_HEADID.'">'.$dataRow->EARNING_HEADCODE.'</option>';

            }
            
        }else{
            $objDedctName = DB::table('TBL_MST_DEDUCTION_HEAD')->get();
           
            foreach ($objDedctName as $index=>$dataRow){

                echo '<option value="'.$dataRow->DEDUCTION_HEADID.'">'.$dataRow->DEDUCTION_HEADCODE.'</option>';

            }
            
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
        if(isset($_REQUEST['EARNIGHEAD_REF']) && !empty($_REQUEST['EARNIGHEAD_REF'])){
            foreach($_REQUEST['EARNIGHEAD_REF'] as $key=>$val){

                $data[] = array(
                'ADJUSTMENT_TYPE'     => trim($_REQUEST['ADJUSTMENT_TYPE'][$key]),
                'EARNINGHEADCODE'     => trim($_REQUEST['EARNIGHEAD_REF'][$key]),
                'AMOUNTPLUS'       => trim($_REQUEST['AMOUNTPLUS'][$key]),
                'AMOUNTMINUS'       => trim($_REQUEST['AMOUNTSUB'][$key]),
                'REEMARKS'       => trim($_REQUEST['REMARKS'][$key]),
                );
            }
        }
        //dd($data);

        if(!empty($data)){
            $wrapped_links["EARNING"] = $data; 
            $XMLEARNING = ArrayToXml::convert($wrapped_links);
        }
        else{
            $XMLEARNING = NULL; 
        }

        $EMPVA_DOCNO      =   trim($request['DOC_NO']);
        $EMPVA_DOCDT      =   trim($request['REMB_DT']);
        $PAYPID_REF           =   trim($request['PAYPERIODID_REF']);
        $EMPID_REF            =   trim($request['EMPID_REF']);

        $CYID_REF             =   Auth::user()->CYID_REF;
        $BRID_REF             =   Session::get('BRID_REF');
        $FYID_REF             =   Session::get('FYID_REF');
        $VTID             =   $this->vtid_ref;
        $USERID           =   Auth::user()->USERID;
        $UPDATE               =   Date('Y-m-d');
        $UPTIME               =   Date('h:i:s.u');
        $ACTION               =   "ADD";
        $IPADDRESS            =   $request->getClientIp();
        
        $array_data   = [
                    $EMPVA_DOCNO,   $EMPVA_DOCDT, $PAYPID_REF,  $EMPID_REF,  $CYID_REF,
                    $BRID_REF,      $FYID_REF,    $XMLEARNING,  $VTID,       $USERID,      
                    $UPDATE,        $UPTIME,      $ACTION,      $IPADDRESS         
                    ];

           // dd($array_data);

        $sp_result = DB::select('EXEC SP_EMP_VOUCHER_ADJUSTMENT_IN ?,?,?,?,?, ?,?,?,?,?,   ?,?,?,?', $array_data);

        return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);
        
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

            $objResponse = DB::table('TBL_MST_EMP_VOUCHER_ADJUSTMENT')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('EMP_VAID','=',$id)
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
            $objDedhedList  = $this->DedHead();
            
            $HDR = DB::table('TBL_MST_EMP_VOUCHER_ADJUSTMENT')
            ->where('TBL_MST_EMP_VOUCHER_ADJUSTMENT.EMP_VAID','=',$id)
            ->leftJoin('TBL_MST_PAY_PERIOD', 'TBL_MST_EMP_VOUCHER_ADJUSTMENT.PAYPID_REF','=','TBL_MST_PAY_PERIOD.PAYPERIODID')
            ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_MST_EMP_VOUCHER_ADJUSTMENT.EMPID_REF','=','TBL_MST_EMPLOYEE.EMPID')
            ->select('TBL_MST_EMP_VOUCHER_ADJUSTMENT.*','TBL_MST_PAY_PERIOD.*','TBL_MST_EMPLOYEE.*')
            ->first();

            $MAT = DB::table('TBL_MST_EMP_VOUCHER_ADJUSTMENT_EARNING')                    
            ->where('TBL_MST_EMP_VOUCHER_ADJUSTMENT_EARNING.EMP_VAID_REF','=',$id)
            ->leftJoin('TBL_MST_EMP_VOUCHER_ADJUSTMENT', 'TBL_MST_EMP_VOUCHER_ADJUSTMENT_EARNING.EMP_VAID_REF','=','TBL_MST_EMP_VOUCHER_ADJUSTMENT.EMP_VAID')
            ->leftJoin('TBL_MST_EARNING_HEAD', 'TBL_MST_EMP_VOUCHER_ADJUSTMENT_EARNING.EARNING_HEADID_REF','=','TBL_MST_EARNING_HEAD.EARNING_HEADID')
            ->leftJoin('TBL_MST_DEDUCTION_HEAD', 'TBL_MST_EMP_VOUCHER_ADJUSTMENT_EARNING.EARNING_HEADID_REF','=','TBL_MST_DEDUCTION_HEAD.DEDUCTION_HEADID')
            ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_MST_EMP_VOUCHER_ADJUSTMENT.EMPID_REF','=','TBL_MST_EMPLOYEE.EMPID')
            ->leftJoin('TBL_MST_DESIGNATION', 'TBL_MST_EMPLOYEE.DESGID_REF','=','TBL_MST_DESIGNATION.DESGID')
            ->leftJoin('TBL_MST_DEPARTMENT', 'TBL_MST_EMPLOYEE.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID')
            ->select('TBL_MST_EMP_VOUCHER_ADJUSTMENT_EARNING.*','TBL_MST_EMP_VOUCHER_ADJUSTMENT.*',
            'TBL_MST_EARNING_HEAD.*','TBL_MST_DEDUCTION_HEAD.*','TBL_MST_EMPLOYEE.*','TBL_MST_DESIGNATION.*','TBL_MST_DEPARTMENT.*')
            ->get()->toArray();

            $objCount = count($MAT);         

            return view($this->view.$FormId.'edit',compact(['FormId','objResponse','HDR','objList','objDataList','objEarnHead','objDedhedList','user_approval_level','objRights','MAT','objCount']));
        }

    }

     
    public function update(Request $request)
    {

      // dd($request->all());

        $data = array();
        if(isset($_REQUEST['EARNIGHEAD_REF']) && !empty($_REQUEST['EARNIGHEAD_REF'])){
            foreach($_REQUEST['EARNIGHEAD_REF'] as $key=>$val){

                $data[] = array(
                'ADJUSTMENT_TYPE'     => trim($_REQUEST['ADJUSTMENT_TYPE'][$key]),
                'EARNINGHEADCODE'     => trim($_REQUEST['EARNIGHEAD_REF'][$key]),
                'AMOUNTPLUS'       => trim($_REQUEST['AMOUNTPLUS'][$key]),
                'AMOUNTMINUS'       => trim($_REQUEST['AMOUNTSUB'][$key]),
                'REEMARKS'       => trim($_REQUEST['REMARKS'][$key]),
                );
            }
        }
        //dd($data);

        if(!empty($data)){
            $wrapped_links["EARNING"] = $data; 
            $XMLEARNING = ArrayToXml::convert($wrapped_links);
        }
        else{
            $XMLEARNING = NULL; 
        }

        $EMPVA_DOCNO      =   trim($request['DOC_NO']);
        $EMPVA_DOCDT      =   trim($request['REMB_DT']);
        $PAYPID_REF           =   trim($request['PAYPERIODID_REF']);
        $EMPID_REF            =   trim($request['EMPID_REF']);

        $CYID_REF             =   Auth::user()->CYID_REF;
        $BRID_REF             =   Session::get('BRID_REF');
        $FYID_REF             =   Session::get('FYID_REF');
        $VTID             =   $this->vtid_ref;
        $USERID           =   Auth::user()->USERID;
        $UPDATE               =   Date('Y-m-d');
        $UPTIME               =   Date('h:i:s.u');
        $ACTION     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();
        
        $array_data   = [
            $EMPVA_DOCNO,   $EMPVA_DOCDT, $PAYPID_REF,  $EMPID_REF,  $CYID_REF,
            $BRID_REF,      $FYID_REF,    $XMLEARNING,  $VTID,       $USERID,      
            $UPDATE,        $UPTIME,      $ACTION,      $IPADDRESS         
            ];

        // dd($array_data);

        $sp_result = DB::select('EXEC SP_EMP_VOUCHER_ADJUSTMENT_UP ?,?,?,?,?, ?,?,?,?,?,   ?,?,?,?', $array_data);

        return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);
        
        exit();            
    } 

    //uploads attachments files
    public function docuploads(Request $request){

        $formData = $request->all();

        $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size = config("erpconst.attachments.max_size") * 1020 * 1024;

        //echo '<br> c='."--".Config("erpconst.attachments.max_size");
        
        //get data
        $FormId         =   $this->form_id;
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
            return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
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
            if(isset($_REQUEST['EARNIGHEAD_REF']) && !empty($_REQUEST['EARNIGHEAD_REF'])){
                foreach($_REQUEST['EARNIGHEAD_REF'] as $key=>$val){
    
                    $data[] = array(
                    'ADJUSTMENT_TYPE'     => trim($_REQUEST['ADJUSTMENT_TYPE'][$key]),
                    'EARNINGHEADCODE'     => trim($_REQUEST['EARNIGHEAD_REF'][$key]),
                    'AMOUNTPLUS'       => trim($_REQUEST['AMOUNTPLUS'][$key]),
                    'AMOUNTMINUS'       => trim($_REQUEST['AMOUNTSUB'][$key]),
                    'REEMARKS'       => trim($_REQUEST['REMARKS'][$key]),
                    );
                }
            }
            //dd($data);
    
            if(!empty($data)){
                $wrapped_links["EARNING"] = $data; 
                $XMLEARNING = ArrayToXml::convert($wrapped_links);
            }
            else{
                $XMLEARNING = NULL; 
            }
    
            $EMPVA_DOCNO      =   trim($request['DOC_NO']);
            $EMPVA_DOCDT      =   trim($request['REMB_DT']);
            $PAYPID_REF           =   trim($request['PAYPERIODID_REF']);
            $EMPID_REF            =   trim($request['EMPID_REF']);
    
            $CYID_REF             =   Auth::user()->CYID_REF;
            $BRID_REF             =   Session::get('BRID_REF');
            $FYID_REF             =   Session::get('FYID_REF');
            $VTID             =   $this->vtid_ref;
            $USERID           =   Auth::user()->USERID;
            $UPDATE               =   Date('Y-m-d');
            $UPTIME               =   Date('h:i:s.u');
            $ACTION = $Approvallevel;
            $IPADDRESS  =   $request->getClientIp();
            
            $array_data   = [
                $EMPVA_DOCNO,   $EMPVA_DOCDT, $PAYPID_REF,  $EMPID_REF,  $CYID_REF,
                $BRID_REF,      $FYID_REF,    $XMLEARNING,  $VTID,       $USERID,      
                $UPDATE,        $UPTIME,      $ACTION,      $IPADDRESS         
                ];
    
            // dd($array_data);
    
            $sp_result = DB::select('EXEC SP_EMP_VOUCHER_ADJUSTMENT_UP ?,?,?,?,?, ?,?,?,?,?,   ?,?,?,?', $array_data);

            return Response::json(['success' =>true,'msg' => 'Record successfully Approved.']);               

        exit();     
    }

    public function view($id){

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

            $objResponse = DB::table('TBL_MST_EMP_VOUCHER_ADJUSTMENT')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('EMP_VAID','=',$id)
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
            $objDedhedList  = $this->DedHead();
            
            $HDR = DB::table('TBL_MST_EMP_VOUCHER_ADJUSTMENT')
            ->where('TBL_MST_EMP_VOUCHER_ADJUSTMENT.EMP_VAID','=',$id)
            ->leftJoin('TBL_MST_PAY_PERIOD', 'TBL_MST_EMP_VOUCHER_ADJUSTMENT.PAYPID_REF','=','TBL_MST_PAY_PERIOD.PAYPERIODID')
            ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_MST_EMP_VOUCHER_ADJUSTMENT.EMPID_REF','=','TBL_MST_EMPLOYEE.EMPID')
            ->select('TBL_MST_EMP_VOUCHER_ADJUSTMENT.*','TBL_MST_PAY_PERIOD.*','TBL_MST_EMPLOYEE.*')
            ->first();

            $MAT = DB::table('TBL_MST_EMP_VOUCHER_ADJUSTMENT_EARNING')                    
            ->where('TBL_MST_EMP_VOUCHER_ADJUSTMENT_EARNING.EMP_VAID_REF','=',$id)
            ->leftJoin('TBL_MST_EMP_VOUCHER_ADJUSTMENT', 'TBL_MST_EMP_VOUCHER_ADJUSTMENT_EARNING.EMP_VAID_REF','=','TBL_MST_EMP_VOUCHER_ADJUSTMENT.EMP_VAID')
            ->leftJoin('TBL_MST_EARNING_HEAD', 'TBL_MST_EMP_VOUCHER_ADJUSTMENT_EARNING.EARNING_HEADID_REF','=','TBL_MST_EARNING_HEAD.EARNING_HEADID')
            ->leftJoin('TBL_MST_DEDUCTION_HEAD', 'TBL_MST_EMP_VOUCHER_ADJUSTMENT_EARNING.EARNING_HEADID_REF','=','TBL_MST_DEDUCTION_HEAD.DEDUCTION_HEADID')
            ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_MST_EMP_VOUCHER_ADJUSTMENT.EMPID_REF','=','TBL_MST_EMPLOYEE.EMPID')
            ->leftJoin('TBL_MST_DESIGNATION', 'TBL_MST_EMPLOYEE.DESGID_REF','=','TBL_MST_DESIGNATION.DESGID')
            ->leftJoin('TBL_MST_DEPARTMENT', 'TBL_MST_EMPLOYEE.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID')
            ->select('TBL_MST_EMP_VOUCHER_ADJUSTMENT_EARNING.*','TBL_MST_EMP_VOUCHER_ADJUSTMENT.*',
            'TBL_MST_EARNING_HEAD.*','TBL_MST_DEDUCTION_HEAD.*','TBL_MST_EMPLOYEE.*','TBL_MST_DESIGNATION.*','TBL_MST_DEPARTMENT.*')
            ->get()->toArray();

            $objCount = count($MAT);         

            return view($this->view.$FormId.'view',compact(['FormId','objResponse','HDR','objList','objDataList','objEarnHead','user_approval_level','objRights','MAT','objCount']));
        }

    }
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }

        $objResponse = TblMstFrm399::whereIn('ATTID',$ids_data)->get();
        
        return view('transactions.Payroll.AssignLeave.trnfrm399print',compact(['objResponse']));
   }//print

    
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objResponse = DB::table('TBL_MST_EMP_VOUCHER_ADJUSTMENT')->where('EMP_VAID','=',$id)->select('*')->first();

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

            //dd($req_data);
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
            $TABLE      =   "TBL_MST_EMP_VOUCHER_ADJUSTMENT";
            $FIELD      =   "EMP_VAID";
            $ID         =   $id;
            $UPDATE     =   Date('Y-m-d');
            $UPTIME     =   Date('h:i:s.u');
            $IPADDRESS  =   $request->getClientIp();
    
            $req_data[0]=[
                'NT'  => 'TBL_MST_EMP_VOUCHER_ADJUSTMENT_EARNING',
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
                public function  DedHead(){
                    $DedHeads    =   DB::table('TBL_MST_DEDUCTION_HEAD')
                    ->where('STATUS','=','A')
                    ->get();
                    return $DedHeads; 
                    }

                    public function getItemDetails_prod_code(Request $request){   

                        $taxstate = $request['taxstate'];
                        $Status = "A";
                        $CYID_REF = Auth::user()->CYID_REF;
                        $BRID_REF = Session::get('BRID_REF');
                        $FYID_REF = Session::get('FYID_REF');
                        $StdCost = 0;
                        $Taxid = [];
                
                        $ObjItem    =   DB::table('TBL_MST_EMPLOYEE')
                        ->leftJoin('TBL_MST_DESIGNATION', 'TBL_MST_EMPLOYEE.DESGID_REF','=','TBL_MST_DESIGNATION.DESGID')
                        ->leftJoin('TBL_MST_DEPARTMENT', 'TBL_MST_EMPLOYEE.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID')
                        //->orderBy('EMPID', 'DESC')
                        ->get();
                
                        //dd($ObjItem);
                            
                                if(!empty($ObjItem)){
                
                                    foreach ($ObjItem as $index=>$dataRow){
                
                                            $EMPID      =   isset($dataRow->EMPID)?$dataRow->EMPID:NULL;
                                            $EMPCODE    =   isset($dataRow->EMPCODE)?$dataRow->EMPCODE:NULL;
                                            $FNAME      =   isset($dataRow->FNAME)?$dataRow->FNAME:NULL;
                                            $DESGID_REF =   isset($dataRow->DESGID_REF)?$dataRow->DESGID_REF:NULL;
                                            $DESGCODE   =   isset($dataRow->DESGCODE)?$dataRow->DESGCODE:NULL;
                                            $DCODE      =   isset($dataRow->DCODE)?$dataRow->DCODE:NULL;
                                            
                                            $row = '';
                                            $row .='<tr id="glidcode_'.$EMPID.'" class="clsglid" >
                                                    <td style="width:5%;text-align:center;" ><input type="checkbox" id="chkIdProdCode'.$EMPID.'"  value="'.$EMPID.'" class="js-selectall1ProdCode"  > </td>
                                                    <td style="width:10%;">'.$EMPCODE.'</td>
                                                    <input type="hidden" id="txtglidcode_'.$EMPID.'" data-code="'.$EMPCODE.'" value="'.$EMPID.'"/>
                                                    <td hidden id="fnameid_'.$EMPID.'"><input type="hidden" id="txtfnameid_'.$EMPID.'" data-fname="'.$FNAME.'" value="'.$FNAME.'"/>'.$FNAME.'</td>
                                                    <td hidden id="desgid_'.$EMPID.'"><input type="hidden" id="txtdesgid_'.$EMPID.'" data-desg="'.$DESGCODE.'" value="'.$DESGCODE.'"/>'.$DESGCODE.'</td>
                                                    <td hidden id="deptid_'.$EMPID.'"><input type="hidden" id="txtdeptid_'.$EMPID.'" data-dept="'.$DCODE.'" value="'.$DCODE.'"/>'.$DCODE.'</td>
                                                    <td style="width:10%;">'.$FNAME.'</td>
                                                </tr>';
                                        
                                            echo $row;    
                                        
                                    } 
                                    //return Response::json($ObjItem);
                                }           
                                else{
                                    echo '<tr><td colspan="12"> Record not found.</td></tr>';
                                }
                        exit();
                    }


}
