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

class TrnFrm425Controller extends Controller
{
   
    protected $form_id = 425;
    protected $vtid_ref   = 220;  //voucher type id
    protected $view     = "transactions.Payroll.AttendanceTimeWiseDateWise.trnfrm";

       
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
        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;    

        $objDataList = DB::select("select hdr.ATTTNDID,hdr.DOCNO,hdr.DOCDATE,hdr.PAYPERIODID_REF,hdr.SHIFTID_REF,pl.PAY_PERIOD_CODE,pl.PAY_PERIOD_DESC,sl.SHIFT_CODE,sl.SHIFT_NAME,
        left(sl.START_TIME,8) START_TIME, left(sl.END_TIME,8) END_TIME,(SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                                    LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                                    WHERE  AUD.VID=hdr.ATTTNDID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                                    AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                                    ) AS CREATED_BY,
                                    hdr.STATUS, 
                                    case when a.ACTIONNAME = '$FANO' then 'Final Approved' 
                                    else case when a.ACTIONNAME = 'ADD' then 'Added'  
                                        when a.ACTIONNAME = 'EDIT' then 'Edited'
                                        when a.ACTIONNAME = 'APPROVAL1' then 'First Level Approved'
                                        when a.ACTIONNAME = 'APPROVAL2' then 'Second Level Approved'
                                        when a.ACTIONNAME = 'APPROVAL3' then 'Third Level Approved'
                                        when a.ACTIONNAME = 'APPROVAL4' then 'Fourth Level Approved'
                                        when a.ACTIONNAME = 'APPROVAL5' then 'Final Approved' 
                                    when a.ACTIONNAME = 'CANCEL' then 'Cancelled'
                                    end end as STATUS_DESC
                                    from TBL_TRN_AUDITTRAIL a 
                                    inner join TBL_TRN_ATTTND_HDR hdr
                                    on a.VID = hdr.ATTTNDID 
                                    and a.VTID_REF = hdr.VTID_REF 
                                    and a.CYID_REF = hdr.CYID_REF 
                                    and a.BRID_REF = hdr.BRID_REF
                                    left join TBL_MST_PAY_PERIOD pl ON hdr.PAYPERIODID_REF = pl.PAYPERIODID
                                    left join TBL_MST_SHIFT sl ON hdr.SHIFTID_REF = sl.SHIFTID
                                    where a.VTID_REF = '$this->vtid_ref'
                                    and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                                    and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                                    ORDER BY hdr.ATTTNDID DESC");

            return view($this->view.$FormId,compact(['objRights','objDataList','FormId']));


    }

    public function add(){ 

        $FormId         =   $this->form_id;
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $objList      = $this->payperiod();
        $objDataList  = $this->employee();
        $objAttendance  = $this->attendance();

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

        return view($this->view.$FormId.'add',compact(['FormId','objDD','objList','objDataList','objDOCNO','objAttendance'])); 
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
        $DOC_NO =   $request['DOC_NO'];
        
        $objLabel = DB::table('TBL_TRN_ATTTND_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('DOCNO','=',$DOC_NO)
        ->select('DOCNO')
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
        if(isset($_REQUEST['EMPID_REF']) && !empty($_REQUEST['EMPID_REF'])){
            foreach($_REQUEST['EMPID_REF'] as $key=>$val){

                $data[] = array(
                'EMPID_REF'              => trim($_REQUEST['EMPID_REF'][$key]),
                'TIME_FROM'              => trim($_REQUEST['FROMTIME'][$key]),
                'TIME_TO'                => trim($_REQUEST['TOTIME'][$key]),
                'ATTENDANCE_STATUS'      => trim($_REQUEST['ASID_REF'][$key]),
                'REMARKS'                => trim($_REQUEST['REMARKS'][$key]),
                );
            }
        }
        
        if(!empty($data)){
            $wrapped_links["MAT"] = $data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $XMLMAT = NULL; 
        }

        $DOC_NO            =   trim($request['DOC_NO']);
        $DOC_DT            =   trim($request['DOC_DT']);
        $PAYPERIODID_REF   =   trim($request['PAYPERIODID_REF']);
        $SHIFTID_REF       =   trim($request['SHIFTID_REF']);
        $DEFROMTIME        =   trim($request['DEFROMTIME']);
        $DEFTOTIME         =   trim($request['DEFTOTIME']);
        $CYID_REF          =   Auth::user()->CYID_REF;
        $BRID_REF          =   Session::get('BRID_REF');
        $FYID_REF          =   Session::get('FYID_REF');
        $VTID              =   $this->vtid_ref;
        $USERID            =   Auth::user()->USERID;
        $UPDATE            =   Date('Y-m-d');
        $UPTIME            =   Date('h:i:s.u');
        $ACTION            =   "ADD";
        $IPADDRESS         =   $request->getClientIp();
        
        $array_data   = [
                    $DOC_NO,        $DOC_DT,        $PAYPERIODID_REF,   $SHIFTID_REF,  $DEFROMTIME,  $DEFTOTIME,    $CYID_REF,
                    $BRID_REF,      $FYID_REF,      $VTID,              $XMLMAT,       $USERID,      $UPDATE,       $UPTIME,      
                    $ACTION,        $IPADDRESS         
                    ];

      //  dd($array_data);

        $sp_result = DB::select('EXEC SP_ATTTND_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $array_data);

        return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);
        
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

            $objResponse = DB::table('TBL_TRN_ATTTND_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('ATTTNDID','=',$id)
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
            $objAttendance  = $this->attendance();
            
            $HDR = DB::table('TBL_TRN_ATTTND_HDR')
            ->where('TBL_TRN_ATTTND_HDR.ATTTNDID','=',$id)
            ->leftJoin('TBL_MST_PAY_PERIOD', 'TBL_TRN_ATTTND_HDR.PAYPERIODID_REF','=','TBL_MST_PAY_PERIOD.PAYPERIODID')
            ->leftJoin('TBL_MST_SHIFT', 'TBL_TRN_ATTTND_HDR.SHIFTID_REF','=','TBL_MST_SHIFT.SHIFTID')
            ->select('TBL_TRN_ATTTND_HDR.*','TBL_MST_PAY_PERIOD.*','TBL_MST_SHIFT.*')
            ->first();

            $mat = [
                $id
            ];

            $MAT =  DB::select('EXEC SP_TRN_GET_ATTNENDANCE_MAT ?', $mat);
                

            $objCount = count($MAT);         

            return view($this->view.$FormId.'edit',compact(['FormId','objResponse','HDR','objList','objDataList','objAttendance','user_approval_level','objRights','MAT','objCount']));
        }

    }

     
    public function update(Request $request)
    {

      // dd($request->all());

      $data = array();
      if(isset($_REQUEST['EMPID_REF']) && !empty($_REQUEST['EMPID_REF'])){
          foreach($_REQUEST['EMPID_REF'] as $key=>$val){

              $data[] = array(
              'EMPID_REF'              => trim($_REQUEST['EMPID_REF'][$key]),
              'TIME_FROM'              => trim($_REQUEST['FROMTIME'][$key]),
              'TIME_TO'                => trim($_REQUEST['TOTIME'][$key]),
              'ATTENDANCE_STATUS'      => trim($_REQUEST['ASID_REF'][$key]),
              'REMARKS'                => trim($_REQUEST['REMARKS'][$key]),
              );
          }
      }
      
      if(!empty($data)){
          $wrapped_links["MAT"] = $data; 
          $XMLMAT = ArrayToXml::convert($wrapped_links);
      }
      else{
          $XMLMAT = NULL; 
      }

      $DOC_NO            =   trim($request['DOC_NO']);
      $DOC_DT            =   trim($request['DOC_DT']);
      $PAYPERIODID_REF   =   trim($request['PAYPERIODID_REF']);
      $SHIFTID_REF       =   trim($request['SHIFTID_REF']);
      $DEFROMTIME        =   trim($request['DEFROMTIME']);
      $DEFTOTIME         =   trim($request['DEFTOTIME']);
      $CYID_REF          =   Auth::user()->CYID_REF;
      $BRID_REF          =   Session::get('BRID_REF');
      $FYID_REF          =   Session::get('FYID_REF');
      $VTID              =   $this->vtid_ref;
      $USERID            =   Auth::user()->USERID;
      $UPDATE            =   Date('Y-m-d');
      $UPTIME            =   Date('h:i:s.u');
      $ACTION            =   "EDIT";
      $IPADDRESS         =   $request->getClientIp();
      
      $array_data   = [
                  $DOC_NO,        $DOC_DT,        $PAYPERIODID_REF,   $SHIFTID_REF,  $DEFROMTIME,  $DEFTOTIME,    $CYID_REF,
                  $BRID_REF,      $FYID_REF,      $VTID,              $XMLMAT,       $USERID,      $UPDATE,       $UPTIME,      
                  $ACTION,        $IPADDRESS         
                  ];

    //  dd($array_data);

      $sp_result = DB::select('EXEC SP_ATTTND_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $array_data);

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
            if(isset($_REQUEST['EMPID_REF']) && !empty($_REQUEST['EMPID_REF'])){
                foreach($_REQUEST['EMPID_REF'] as $key=>$val){

                    $data[] = array(
                    'EMPID_REF'              => trim($_REQUEST['EMPID_REF'][$key]),
                    'TIME_FROM'              => trim($_REQUEST['FROMTIME'][$key]),
                    'TIME_TO'                => trim($_REQUEST['TOTIME'][$key]),
                    'ATTENDANCE_STATUS'      => trim($_REQUEST['ASID_REF'][$key]),
                    'REMARKS'                => trim($_REQUEST['REMARKS'][$key]),
                    );
                }
            }
            
            if(!empty($data)){
                $wrapped_links["MAT"] = $data; 
                $XMLMAT = ArrayToXml::convert($wrapped_links);
            }
            else{
                $XMLMAT = NULL; 
            }

            $DOC_NO            =   trim($request['DOC_NO']);
            $DOC_DT            =   trim($request['DOC_DT']);
            $PAYPERIODID_REF   =   trim($request['PAYPERIODID_REF']);
            $SHIFTID_REF       =   trim($request['SHIFTID_REF']);
            $DEFROMTIME        =   trim($request['DEFROMTIME']);
            $DEFTOTIME         =   trim($request['DEFTOTIME']);
            $CYID_REF          =   Auth::user()->CYID_REF;
            $BRID_REF          =   Session::get('BRID_REF');
            $FYID_REF          =   Session::get('FYID_REF');
            $VTID              =   $this->vtid_ref;
            $USERID            =   Auth::user()->USERID;
            $UPDATE            =   Date('Y-m-d');
            $UPTIME            =   Date('h:i:s.u');
            $ACTION = $Approvallevel;
            $IPADDRESS  =   $request->getClientIp();
            
            $array_data   = [
                        $DOC_NO,        $DOC_DT,        $PAYPERIODID_REF,   $SHIFTID_REF,  $DEFROMTIME,  $DEFTOTIME,    $CYID_REF,
                        $BRID_REF,      $FYID_REF,      $VTID,              $XMLMAT,       $USERID,      $UPDATE,       $UPTIME,      
                        $ACTION,        $IPADDRESS         
                        ];

            $sp_result = DB::select('EXEC SP_ATTTND_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $array_data);
                
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
            $objAttendance  = $this->attendance();
            
            $HDR = DB::table('TBL_TRN_ATTTND_HDR')
            ->where('TBL_TRN_ATTTND_HDR.ATTTNDID','=',$id)
            ->leftJoin('TBL_MST_PAY_PERIOD', 'TBL_TRN_ATTTND_HDR.PAYPERIODID_REF','=','TBL_MST_PAY_PERIOD.PAYPERIODID')
            ->leftJoin('TBL_MST_SHIFT', 'TBL_TRN_ATTTND_HDR.SHIFTID_REF','=','TBL_MST_SHIFT.SHIFTID')
            ->select('TBL_TRN_ATTTND_HDR.*','TBL_MST_PAY_PERIOD.*','TBL_MST_SHIFT.*')
            ->first();

            $mat = [
                $id
            ];

            $MAT =  DB::select('EXEC SP_TRN_GET_ATTNENDANCE_MAT ?', $mat);
                

            $objCount = count($MAT);        
            $ActionStatus   =   "disabled";
            return view($this->view.$FormId.'view',compact(['FormId','objResponse','HDR','objList','objDataList',
            'objAttendance','user_approval_level','objRights','MAT','objCount','ActionStatus']));
        }

    }
    
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

       public function attendance(){
        $objattendance = DB::table('TBL_MST_ATTENDANCE_CODE')
        ->select('ATTENDANCE_CODEID','ATTENDANCE_CODE','ATTENDANCE_CODE_DESC')
        ->where('STATUS','=','A')
        ->get();
        return $objattendance; 
        }


        public function payperiod(){
        $objpayperiod = DB::table('TBL_MST_PAY_PERIOD')
        ->select('PAYPERIODID','PAY_PERIOD_CODE','PAY_PERIOD_DESC')
        ->where('STATUS','=','A')
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

        public function getShiftDetails(Request $request){   

            $taxstate = $request['taxstate'];
            $Status = "A";
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $StdCost = 0;
            $Taxid = [];
    
            $ObjItem    =   DB::table('TBL_MST_SHIFT')
            //->leftJoin('TBL_MST_DESIGNATION', 'TBL_MST_EMPLOYEE.DESGID_REF','=','TBL_MST_DESIGNATION.DESGID')
            //->leftJoin('TBL_MST_DEPARTMENT', 'TBL_MST_EMPLOYEE.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID')
            //->orderBy('EMPID', 'DESC')
            ->get();
    
            //dd($ObjItem);
                
                    if(!empty($ObjItem)){
    
                        foreach ($ObjItem as $index=>$dataRow){
    
                                $SHIFTID        =   isset($dataRow->SHIFTID)?$dataRow->SHIFTID:NULL;
                                $SHIFT_CODE     =   isset($dataRow->SHIFT_CODE)?$dataRow->SHIFT_CODE:NULL;
                                $SHIFT_NAME     =   isset($dataRow->SHIFT_NAME)?$dataRow->SHIFT_NAME:NULL;
                                $STARTTIME      =   isset($dataRow->START_TIME)?$dataRow->START_TIME:NULL;
                                $ENDTIME        =   isset($dataRow->END_TIME)?$dataRow->END_TIME:NULL;
                                $MIN_HOURS_FULL =   isset($dataRow->MIN_HOURS_FULL)?$dataRow->MIN_HOURS_FULL:NULL;
                                $START_TIME     = substr($STARTTIME, 0, 5);
                                $END_TIME       = substr($ENDTIME, 0, 5);

                                $row = '';
                                $row .='<tr id="glidcode_'.$SHIFTID.'" class="clsglid" >
                                        <td style="width:5%;text-align:center;" ><input type="checkbox" id="chkIdProdCode'.$SHIFTID.'"  value="'.$SHIFTID.'" class="js-selectall1ProdCode"  > </td>
                                        <td style="width:10%;">'.$SHIFT_CODE.'</td>
                                        <input type="hidden" id="txtglidcode_'.$SHIFTID.'" data-code="'.$SHIFT_NAME.'" value="'.$SHIFTID.'"/>
                                        <td hidden id="stimeid_'.$SHIFTID.'"><input type="hidden" id="txtstimeid_'.$SHIFTID.'" data-stime="'.$START_TIME.'" value="'.$START_TIME.'"/>'.$START_TIME.'</td>
                                        <td hidden id="endtimeid_'.$SHIFTID.'"><input type="hidden" id="txtendtimeid_'.$SHIFTID.'" data-endtime="'.$END_TIME.'" value="'.$END_TIME.'"/>'.$END_TIME.'</td>
                                        <td style="width:10%;">'.$SHIFT_NAME.'</td>
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

        public function getEmpDetails111(Request $request){   

            $taxstate = $request['taxstate'];
            $Status = "A";
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $StdCost = 0;
            $Taxid = [];
    
            $ObjItem    =   DB::table('TBL_MST_EMPLOYEE')->get();
    
            //dd($ObjItem);
                
                    if(!empty($ObjItem)){
    
                        foreach ($ObjItem as $index=>$dataRow){
    
                                $EMPID        =   isset($dataRow->EMPID)?$dataRow->EMPID:NULL;
                                $EMPCODE     =   isset($dataRow->EMPCODE)?$dataRow->EMPCODE:NULL;
                                $FNAME     =   isset($dataRow->FNAME)?$dataRow->FNAME:NULL;

                                $row = '';
                                $row .='<tr id="glidcode_'.$EMPID.'" class="clsglid" >
                                        <td style="width:5%;text-align:center;" ><input type="checkbox" id="chkIdProdCode'.$EMPID.'"  value="'.$EMPID.'" class="js-selectall1ProdCode"  > </td>
                                        <td style="width:10%;">'.$EMPCODE.'</td>
                                        <input type="hidden" id="txtglidcode_'.$EMPID.'" data-code="'.$EMPCODE.'" value="'.$EMPID.'"/>

                                        <td hidden id="fnameid_'.$EMPID.'"><input type="hidden" id="txtfnameid_'.$EMPID.'" data-fname="'.$FNAME.'" value="'.$FNAME.'"/>'.$FNAME.'</td>

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




    public function getEmpDetails(Request $request){

        $rowid      =   $request['rowid'];
        $objempHead =   DB::table('TBL_MST_EMPLOYEE')->get();

        if(isset($objempHead) && !empty($objempHead)){
            foreach ($objempHead as $key=>$dataRow){

                $EMPID      =   isset($dataRow->EMPID)?$dataRow->EMPID:NULL;
                $EMPCODE    =   isset($dataRow->EMPCODE)?$dataRow->EMPCODE:NULL;
                $FNAME      =   isset($dataRow->FNAME)?$dataRow->FNAME:NULL;

                //echo $EMPCODE;die;

                echo'
                <tr>
                    <td class="ROW1"><input type="checkbox" onchange="selectEmployee('.$rowid.','.$key.',this.value)" value="'.$EMPID.'" ></td>
                    <td class="ROW2">'.$dataRow->EMPCODE.'</td>
                    <td class="ROW3">'.$dataRow->FNAME.'</td>
                    <td hidden><input type="text" id="empinfo'.$key.'"  data-desc101="'.$EMPCODE.'" data-desc102="'.$FNAME.'"></td>
                </tr>
                ';
            }
    
        }
        else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();
    }



    public function importdata(){
        
        $FormId         =   $this->form_id;
        $objMstVoucherType  =   DB::table("TBL_MST_VOUCHERTYPE")
                                ->where('VTID','=',$this->vtid_ref)
                                ->select('VTID','VCODE','DESCRIPTIONS','INDATE')
                                ->get()
                                ->toArray();

        return view($this->view.$FormId.'importexcel',compact(['FormId','objMstVoucherType']));
        
    }

    public function importexcelindb(Request $request){

        ini_set('memory_limit', '-1');

        $FormId             =   $this->form_id;

        $formData           =   $request->all();
        $allow_extnesions   =   explode(",",$formData["allow_extensions"]);
        $allow_size         =   (int)$formData["allow_max_size"] * 1024 * 1024;

    
        $VTID_REF   =   $this->vtid_ref;
        $VID        =   0;
        $USERID     =   Auth::user()->USERID;   
        $ACTIONNAME =   'ADD';
        $IPADDRESS  =   $request->getClientIp();
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        if(isset($formData["FILENAME"])){

            $uploadedFile = $formData["FILENAME"]; 


            
            if($uploadedFile->isValid()){

                $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
                $filesize               =   $uploadedFile ->getSize();  
                $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );
                $inputFileType          =   ucfirst($extension); //as per API Xls or Xlsx: first charter in upper case

                $filenametostore        =   $VTID_REF.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".Date('YmdHis')."_".$filenamewithextension;
                $file_name              =   pathinfo($filenamewithextension, PATHINFO_FILENAME); // fetch only file name
                $logfile_name           =   "LOG_".$VTID_REF.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".Date('YmdHis')."_".$file_name.".txt";

                $excelfile_path         =   "docs/company".$CYID_REF."/AttendanceTime/importexcel";     
                $destinationPath        =   str_replace('\\', '/', public_path($excelfile_path));

                if ( !is_dir($destinationPath) ) {
                    mkdir($destinationPath, 0777, true);
                }


                if(in_array($extension,$allow_extnesions)){
                    
                    if($filesize < $allow_size){

                        $custfilename = $destinationPath."/".$filenametostore;

                        if ( !is_dir($destinationPath) ) {
                            mkdir($destinationPath, 0777, true);
                        }                                    
                       
                        $uploadedFile->move($destinationPath, $filenametostore); //upload file in dir if not exists

                        if (file_exists($custfilename)) {

                            try {

                                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                                $reader->setReadDataOnly(true);
                                $spreadsheet = $reader->load($custfilename);
                                $worksheet = $spreadsheet->getActiveSheet();
                            
                                $excelHeaderdata    =  [];
                                $excelAlldata       =  [];

                                foreach ($worksheet->getRowIterator() as $rowindex=>$row) {
                            
                                    $cellIterator = $row->getCellIterator();
                                
                                    $cellIterator->setIterateOnlyExistingCells(false);

                                    foreach ($cellIterator as $index=>$cell) {
                                        if($rowindex==1){
                                            $excelHeaderdata[$index] = trim(strtolower($cell->getValue()) ); // fetch value for making header data
                                        }else{
                                            $excelAlldata[$rowindex-1][str_replace(' ', '', $excelHeaderdata[$index]) ]= trim($cell->getValue() );
                                        }
                                    }                        
                                }
                            }
                            catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                                
                                return redirect()->route("transaction",[$FormId,"importdata"])->with("error","Error loading file: ".$e->getMessage());

                            }

                        }
                        else{
                            return redirect()->route("transaction",[$FormId,"importdata"])->with("error","There is some file uploading error. Please try again.");
                        }
                         
                    }else{
                        return redirect()->route("transaction",[$FormId,"importdata"])->with("error","Invalid size - Please check."); //invalid size
                    } 
                    
                }else{

                    return redirect()->route("transaction",[$FormId,"importdata"])->with("error","Invalid file extension - Please check."); // invalid extension                      
                }
            
            }else{
                    
                return redirect()->route("transaction",[$FormId,"importdata"])->with("error","Invalid file - Please check."); //invalid 
            }

        }else{
            return redirect()->route("transaction",[$FormId,"importdata"])->with("error","File not found. - Please check.");  
        }

        $logfile_path = $excelfile_path."/".$logfile_name;     
        
        
        if(!$logfile = fopen($logfile_path, "a") ){

            return redirect()->route("transaction",[$FormId,"importdata"])->with("error","Log creating file error."); //create or open log file
        }

        $validationErr  =   false;
        $headerArr      =   []; 

        $exit_array     =   array();


       

        foreach($excelAlldata as $eIndex=>$eRowData){

            //dd($eRowData);

            $doc_no                =   trim($eRowData["doc_no"]);
            $doc_date              =   trim($eRowData["doc_date"]);
            $pay_period            =   trim($eRowData["pay_period"]);
            $shift                 =   trim($eRowData["shift"]);

            $employee_code         =   trim($eRowData["employee_code"]);
            $from_time             =   trim($eRowData["from_time"]);
            $to_time               =   trim($eRowData["to_time"]);
            $attendance_status     =   trim($eRowData["attendance_status"]);
            $remarks               =   trim($eRowData["remarks"]);


            $exist_data =   $doc_no.'###'.$pay_period.'###'.$employee_code;

            if($doc_no ==""){
                $this->appendLogData($logfile,"Invalid: Blank purchasing cocument. check row no ".$eIndex);
                $validationErr=true;
            }

            if(!empty($this->exist_doc_no($doc_no))){  
                $this->appendLogData($logfile,"Invalid: Already exist purchasing document in database. check row no ".$eIndex);
                $validationErr=true;
            }

            if($doc_date ==""){
                $this->appendLogData($logfile,"Invalid: Blank order input date. check row no ".$eIndex);
                $validationErr=true;
            }

            if($pay_period ==""){
                $this->appendLogData($logfile,"Invalid: Blank pay_period. check row no ".$eIndex);
                $validationErr=true;
            }

            if($employee_code ==""){
                $this->appendLogData($logfile,"Invalid: Blank Customer material number. check row no ".$eIndex);
                $validationErr=true;
            }


            if($from_time ==""){
                $this->appendLogData($logfile,"Invalid: Blank order total quantity. check row no ".$eIndex);
                $validationErr=true;
            }

            if($to_time ==""){
                $this->appendLogData($logfile,"Invalid: Allow only number in order total quantity. check row no ".$eIndex);
                $validationErr=true;
            }

            if($to_time ==""){
                $this->appendLogData($logfile,"Invalid: Blank sales unit. check row no ".$eIndex);
                $validationErr=true;
            }

            if($attendance_status ==""){
                $this->appendLogData($logfile,"Invalid: Blank sales price unit price. check row no ".$eIndex);
                $validationErr=true;
            }

            // if(!is_numeric($to_time)){
            //     $this->appendLogData($logfile,"Invalid: Allow only number sales price unit price. check row no ".$eIndex);
            //     $validationErr=true;
            // }

            if(is_null($this->get_paypreriod_id($pay_period))){
                $this->appendLogData($logfile,"Invalid: pay_period is not exist in database. check row no ".$eIndex);
                $validationErr=true;
            }         

            if(is_null($this->get_employee($employee_code)['EMPID_REF'])){
                $this->appendLogData($logfile,"Invalid: Material/Customer material number is not exist in database. check row no ".$eIndex);
                $validationErr=true;
            } 
            
            if(is_null($this->get_shift($shift)['SHIFTID_REF'])){
                $this->appendLogData($logfile,"Invalid: Material/SHIFT material number is not exist in database. check row no ".$eIndex);
                $validationErr=true;
            } 

        
            if($validationErr ==false){

                $hkey = trim($eRowData["doc_no"]);
                
                if($hkey!=""){

                    if(!array_key_exists($hkey, $headerArr)) {

                        $headerArr[$eRowData["doc_no"]]["header"]["DOC_NO"]                         =   $doc_no;
                        $headerArr[$eRowData["doc_no"]]["header"]["DOC_DT"]                         =   $doc_date;
                        $headerArr[$eRowData["doc_no"]]["header"]["PAYPERIODID_REF"]                =   $pay_period;
                        $headerArr[$eRowData["doc_no"]]["header"]["SHIFTID_REF"]                    =   $shift;
                        
                        $headerArr[$eRowData["doc_no"]]["material"][$eIndex]["EMPID_REF"]           =   $employee_code;
                        $headerArr[$eRowData["doc_no"]]["material"][$eIndex]["FROMTIME"]            =   $from_time;
                        $headerArr[$eRowData["doc_no"]]["material"][$eIndex]["TOTIME"]              =   $to_time;
                        $headerArr[$eRowData["doc_no"]]["material"][$eIndex]["ASID_REF"]            =   $attendance_status;
                        $headerArr[$eRowData["doc_no"]]["material"][$eIndex]["remarks"]             =   $remarks;
                        
                        //$headerArr[$DOC_NO]["material"][$eIndex]["EMPID_REF"]      =   $EMPID_REF;
                        
                        //dd($headerArr);                        
                       
                    }
                    else{
                            
                        $dif_result=array_diff($headerArr[$eRowData["doc_no"]]["header"], $eRowData);
                        if(!empty($dif_result)){
                            foreach($dif_result as $dfkey=>$dfval){
                                
                                $this->appendLogData($logfile,"Column Name=".strtoupper($dfkey)." value is different. Data must be same for same purchasing document (".$hkey.")");
                                $validationErr=true;

                            }

                            break 1;
                        }
                        else{

                            if(!in_array($exist_data, $exit_array)){
 
                                $headerArr[$eRowData["doc_no"]]["material"][$eIndex]["EMPID_REF"]   =   $customermaterialnumber;
                                $headerArr[$eRowData["doc_no"]]["material"][$eIndex]["MAIN_UOM"]    =   $sales_unit;
                                $headerArr[$eRowData["doc_no"]]["material"][$eIndex]["FROMTIME"]    =   $from_time;
                                $headerArr[$eRowData["doc_no"]]["material"][$eIndex]["TOTIME"]      =   $to_time;
                                $headerArr[$eRowData["doc_no"]]["material"][$eIndex]["ASID_REF"]    =   $attendance_status;
                                $headerArr[$eRowData["doc_no"]]["material"][$eIndex]["remarks"]     =   $remarks;
                                
                            }

                        }
                                     
                    }

                }
                else{
                    echo "<br>Invalid Row or Blank Purchasing Document in Row no ".$eIndex++;
                    $this->appendLogData($logfile,"Invalid Row or Blank Purchasing Document in Row no",$eIndex++);
                    $validationErr=true;
                    break 1;
                }
            
            }

            $exit_array[]=$exist_data;
                   
        }

        if($validationErr){
            fclose($logfile);
            
            return redirect()->route("transaction",[$FormId,"importdata"])->with("logerror",$logfile_path);  
        }

       

        foreach($headerArr as $hIndex=>$hRowData){

            $DOC_NO              =   $hRowData["header"]["DOC_NO"];
            $DOCDATE             =   $this->changeDateFormate($hRowData["header"]["DOC_DT"]);
            $PAYPERIODID_REF     =   $this->get_paypreriod_id($hRowData["header"]["PAYPERIODID_REF"]);
            $SHIFTID_REFARRAY    =   $this->get_shift($hRowData["header"]["SHIFTID_REF"]);
            $SHIFTID_REF         =   $SHIFTID_REFARRAY["SHIFTID_REF"];
            $START_TIME          =   $SHIFTID_REFARRAY["START_TIME"];
            $END_TIME            =   $SHIFTID_REFARRAY["END_TIME"];

            $hdr_data            =   array(
                'DOCNO'          =>  $DOC_NO,
                'DOCDATE'        =>  $DOCDATE,
                'PAYPERIODID_REF'=>  $PAYPERIODID_REF,
                'SHIFTID_REF'    =>  $SHIFTID_REF,                
                'DEF_TIME_FROM'  =>  $START_TIME,
                'DEF_TIME_TO'    =>  $END_TIME,                
                'CYID_REF'       =>  $CYID_REF,
                'BRID_REF'       =>  $BRID_REF,
                'FYID_REF'       =>  $FYID_REF,
                'VTID_REF'       =>  $VTID_REF,
            );

            $sp_result  =   DB::table('TBL_TRN_ATTTND_HDR')->insert($hdr_data);

            if($sp_result){

                $ATTTNDID_REF =   DB::getPdo()->lastInsertId();

                $audit_trail_data   =   array(
                    'VTID_REF'      =>  $VTID_REF,
                    'VID'           =>  $ATTTNDID_REF,
                    'USERID'        =>  $USERID,
                    'ACTIONNAME'    =>  $ACTIONNAME,
                    'DATE'          =>  Date('Y-m-d'),
                    'TIME'          =>  Date('h:i:s'),
                    'IPADDRESS'     =>  $IPADDRESS,
                    'CYID_REF'      =>  $CYID_REF,
                    'BRID_REF'      =>  $BRID_REF,
                    'FYID_REF'      =>  $FYID_REF,  
                );

                DB::table('TBL_TRN_AUDITTRAIL')->insert($audit_trail_data);

                foreach($hRowData["material"] as $pindex=>$prow){

                    $EMPID_REFARRAY         =   $this->get_employee($prow["EMPID_REF"]);
                    $EMPID_REF              =   $EMPID_REFARRAY["EMPID_REF"];
                    $FNAME                  =   $EMPID_REFARRAY["FNAME"];
                    $TIME_FROM              =   $prow["FROMTIME"];
                    $TIME_TO                =   $prow["TOTIME"];
                    $ATTENDANCE_STATUS      =   $prow["ASID_REF"];
                    $REMARKS                =   $prow["remarks"];

                    $FromTime = floor($TIME_FROM);
                    $time_format = $TIME_FROM;
                    $strTime = ($FromTime > 0) ? ( $FromTime - 25569 ) * 86400 + $time_format * 86400 :    $time_format * 86400;
                    $FromTimeFormat = date("H:i:s", $strTime);

                    $ToTime = floor($TIME_TO);
                    $totime_format = $TIME_TO;
                    $strToTime = ($ToTime > 0) ? ( $ToTime - 25569 ) * 86400 + $totime_format * 86400 :    $totime_format * 86400;
                    $ToTimeFormat = date("H:i:s", $strToTime);

                    $mat_data= [
                        'ATTTNDID_REF'      =>  $ATTTNDID_REF,
                        'EMPID_REF'         =>  $EMPID_REF,
                        'TIME_FROM'         =>  $FromTimeFormat,
                        'TIME_TO'           =>  $ToTimeFormat,
                        'ATTENDANCE_STATUS' =>  $ATTENDANCE_STATUS,
                        'REMARKS'           =>  $REMARKS,
                    ];

                    DB::table('TBL_TRN_ATTTND_MAT')->insert($mat_data);
                }  

                $ACTION_data =   array( 'VTID_REF'      =>   $VTID_REF,                                
                                        'VID'           =>   $ATTTNDID_REF,
                                        'ADDUSER_ID'    =>   $USERID,
                                        'ADDUSER_DT'    =>   Date('Y-m-d'),
                                        'ADDUSER_TM'    =>   Date('h:i:s'),
                                        'CYID_REF'      =>   $CYID_REF,
                                        'BRID_REF'      =>   $BRID_REF,
                                        'FYID_REF'      =>   $FYID_REF,  
                                    );

                DB::table('TBL_TRN_ACTION')->insert($ACTION_data);             

                $this->appendLogData($logfile," Purchasing Document ".$hIndex.": Record successfully inserted.","",1 ); 
            }
            else{

                 $this->appendLogData($logfile," Purchasing Document ".$hIndex.": Record not inserted. ".$sp_result );
                fclose($logfile);
                 return redirect()->route("transaction",[$FormId,"importdata"])->with("logerror",$logfile_path); 
            }

        }
             
        fclose($logfile);
        return redirect()->route("transaction",[$FormId,"importdata"])->with("logsuccess",$logfile_path);

    } 

    public function appendLogData($logfile, $label, $cellval="",$removeError=0){
        if($removeError==0){
            $txtstring = "Error:".$label." ".$cellval."\n"; 
        }else{
            $txtstring = $label." ".$cellval."\n"; 
        }
             
        echo "<br>".$txtstring;
        fwrite($logfile, $txtstring);
    }

    public function get_paypreriod_id($pay_period){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $data       =   DB::select("SELECT PAYPERIODID FROM TBL_MST_PAY_PERIOD where PAYPERIODID = '$pay_period' AND CYID_REF='$CYID_REF'");
        if(!empty($data)){
            $PAYPERIODID  =   $data[0]->PAYPERIODID;
        }
        else{
            $PAYPERIODID  =   NULL;
        }
        return $PAYPERIODID;
    }

    public function get_employee($EMPID_REF){

        $CYID_REF   =   Auth::user()->CYID_REF;        
        $data       =   DB::select("SELECT EMPID,FNAME FROM TBL_MST_EMPLOYEE where EMPID='$EMPID_REF' AND CYID_REF='$CYID_REF'");
        $item_array =   array();
        if(!empty($data)){           
            $item_array=array(
                'EMPID_REF'    =>$data[0]->EMPID,
                'FNAME'=>$data[0]->FNAME,
            );
        }
        else{
            $item_array=array(
                'EMPID_REF'    =>NULL,
                'FNAME'=>NULL,
            );
        }
        return $item_array;
    }


    public function get_shift($SHIFTID_REF){

        $CYID_REF   =   Auth::user()->CYID_REF;        
        $data       =   DB::select("SELECT SHIFTID,START_TIME,END_TIME FROM TBL_MST_SHIFT where SHIFTID='$SHIFTID_REF' AND CYID_REF='$CYID_REF'");
        $item_array =   array();
        if(!empty($data)){           
            $item_array=array(
                'SHIFTID_REF'    =>$data[0]->SHIFTID,
                'START_TIME'=>$data[0]->START_TIME,
                'END_TIME'=>$data[0]->END_TIME,
            );
        }
        else{
            $item_array=array(
                'SHIFTID_REF'    =>NULL,
                'START_TIME'=>NULL,
                'END_TIME'=>NULL,
            );
        }
        return $item_array;
    }
    

    public function exist_doc_no($DOC_NO){

        $data   =   DB::table('TBL_TRN_ATTTND_HDR')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('BRID_REF','=',Session::get('BRID_REF'))
                    ->where('FYID_REF','=',Session::get('FYID_REF'))
                    ->where('DOCNO','=',$DOC_NO)
                    ->select('DOCNO')->first();

        return $data;
    }

    public function changeDateFormate($date){

        $Date_Val   =   \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date);
        $Date_Data  =   json_decode(json_encode($Date_Val), true);
        $newDate    =   date("Y-m-d",strtotime($Date_Data['date']));

        return $newDate;
    }


    public function downloadExcelFormate(){

        $excelfile_path =   "docs/importsamplefiles/AttendanceTime/import_attendance_time.xlsx";   
        $custfilename   =   str_replace('\\', '/', public_path($excelfile_path));
       
        $reader         =   \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet    =   $reader->load($custfilename);
        
        $writer         =   new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="import_attendance_time.xlsx"');
        ob_end_clean();
        $writer->save("php://output");
        return redirect()->back();
    }



































































}
