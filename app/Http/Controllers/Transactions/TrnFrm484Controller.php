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

class TrnFrm484Controller extends Controller{

    protected $form_id    = 484;
    protected $vtid_ref   = 554;
    protected $view       = "transactions.PreSales.TaskAllocation.trnfrm";
       
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

        $objRights      =   DB::table('TBL_MST_USERROLMAP')
                            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                            ->first();
        
        $FormId         =   $this->form_id;
        $ldvtid_ref     =   509;
		$CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');     


        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList	=	DB::select("select hdr.LEAD_ID,hdr.LEAD_NO,hdr.LEAD_DT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.LEAD_ID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_LEAD_GENERATION hdr
                            on a.VID = hdr.LEAD_ID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            where a.VTID_REF = '$ldvtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.LEAD_ID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );

        return view($this->view.$FormId,compact(['REQUEST_DATA','objRights','objDataList','FormId']));

    }
   
    
    public function add($id=NULL){ 

        $id = urldecode(base64_decode($id));

        $FormId   = $this->form_id;
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_LEAD_GENERATION',
            'HDR_ID'=>'LEAD_ID',
            'HDR_DOC_NO'=>'LEAD_NO',
            'HDR_DOC_DT'=>'LEAD_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        $objDD = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('STATUS','=','A')
        ->select('TBL_MST_DOCNO_DEFINITION.*')
        ->first();

          

        $objResponse = DB::table('TBL_TRN_LEAD_GENERATION')
                        ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_LEAD_GENERATION.CUSTOMER_PROSPECT','=','TBL_MST_CUSTOMER.SLID_REF')
                        ->leftJoin('TBL_MST_PROSPECT', 'TBL_TRN_LEAD_GENERATION.CUSTOMER_PROSPECT','=','TBL_MST_PROSPECT.PID')
                        ->where('TBL_TRN_LEAD_GENERATION.CYID_REF','=',$CYID_REF)
                        ->where('LEAD_ID','=',$id)
                        ->select('TBL_TRN_LEAD_GENERATION.*','TBL_MST_CUSTOMER.SLID_REF','TBL_MST_CUSTOMER.CCODE','TBL_MST_CUSTOMER.NAME AS CUSTNAME','TBL_MST_PROSPECT.PID','TBL_MST_PROSPECT.PCODE','TBL_MST_PROSPECT.NAME AS PROSNAME')
                        ->first();


            $MAT = DB::select("SELECT T1.*,T2.*,T3.*,T4.* FROM TBL_TRN_LEAD_TASK T1
            LEFT JOIN TBL_MST_TASK_TYPE_TYPE T2 ON T1.TASKID_REF=T2.ID            
            LEFT JOIN TBL_MST_EMPLOYEE       T4 ON T1.TASK_ASSIGNTO_REF=T4.EMPID
            LEFT JOIN TBL_MST_PRIORITY       T3 ON T1.PRIORITYID_REF=T3.PRIORITYID
            WHERE T1.LEADID_REF='$id' ORDER BY T1.TASK_ID DESC");
            
            $MAT    = count($MAT) > 0 ?$MAT:[0];

        return view($this->view.$FormId.'add',compact(['objDD','objResponse','FormId','MAT','doc_req','docarray']));
    }

   
    public function save(Request $request){

        $LEAD_ID                   =   trim($request['LEAD_ID']);
        $TASKID_REF                =   trim($request['TASKID_REF']);
        $TASK_ASSIGNTO_REF         =   trim($request['ASSIGNTOID_REF']);
        $PRIORITYID_REF            =   trim($request['PRIORITYID_REF']);
        $DUE_DATE                  =   trim($request['DUEDATE']);
        $TASK_STATUS               =   trim($request['STATUSNAME']);
        $TASK_REMINDER_DATE        =   trim($request['REMINDERDATE']);
        $SUBJECT                   =   trim($request['TASKSUBJECT']);
        $TASK_DETAIL               =   trim($request['TASKDETAILS']);
        
        $CYID_REF                  =   Auth::user()->CYID_REF;
        $BRID_REF                  =   Session::get('BRID_REF');
        $FYID_REF                  =   Session::get('FYID_REF');    
        $USERID_REF                =   Auth::user()->USERID;   
        $VTID_REF                  =   $this->vtid_ref;
        $UPDATE                    =   Date('Y-m-d');
        $UPTIME                    =   Date('h:i:s.u');
        $ACTION                    =   "ADD";
        $IPADDRESS                 =   $request->getClientIp();

        $array_data                = [$LEAD_ID,      $TASKID_REF,    $TASK_ASSIGNTO_REF,    $SUBJECT,   $PRIORITYID_REF,  $TASK_DETAIL,
                                        $DUE_DATE,   $TASK_STATUS,   $TASK_REMINDER_DATE,   $CYID_REF,  $BRID_REF,        $FYID_REF,
                                        $VTID_REF,   $USERID_REF,    $UPDATE,               $UPTIME,    $ACTION,          $IPADDRESS ];

        $sp_result = DB::select('EXEC SP_LEAD_TASK_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $array_data);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');

        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();    
    }



        public function edit($id=NULL){
            return $this->showRecord($id,'edit','');
        }
        public function view($id){
            return $this->showRecord($id,'view','disabled');
        }
    
        public function update(Request $request){
            return  $this->updateRecord($request,'update');        
        } 
        
        public function Approve(Request $request){
          return  $this->updateRecord($request,'approve');    
        }



    public function showRecord($id,$type,$ActionStatus){

        $id = urldecode(base64_decode($id));

        if(!is_null($id)){
        
            $FormId     =   $this->form_id;
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');
            

            $objResponse = DB::table('TBL_TRN_LEAD_GENERATION')
                            ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_LEAD_GENERATION.CUSTOMER_PROSPECT','=','TBL_MST_CUSTOMER.SLID_REF')
                            ->leftJoin('TBL_MST_PROSPECT', 'TBL_TRN_LEAD_GENERATION.CUSTOMER_PROSPECT','=','TBL_MST_PROSPECT.PID')
                            ->where('TBL_TRN_LEAD_GENERATION.CYID_REF','=',$CYID_REF)
                            ->where('LEAD_ID','=',$id)
                            ->select('TBL_TRN_LEAD_GENERATION.*','TBL_MST_CUSTOMER.SLID_REF','TBL_MST_CUSTOMER.CCODE','TBL_MST_CUSTOMER.NAME AS CUSTNAME','TBL_MST_PROSPECT.PID','TBL_MST_PROSPECT.PCODE','TBL_MST_PROSPECT.NAME AS PROSNAME')
                            ->first();

            $MAT = DB::select("SELECT T1.*,T2.*,T3.*,T4.* FROM TBL_TRN_LEAD_TASK T1
                    LEFT JOIN TBL_MST_TASK_TYPE_TYPE T2 ON T1.TASKID_REF=T2.ID            
                    LEFT JOIN TBL_MST_EMPLOYEE T4 ON T1.TASK_ASSIGNTO_REF=T4.EMPID
                    LEFT JOIN TBL_MST_PRIORITY T3 ON T1.PRIORITYID_REF=T3.PRIORITYID
                    WHERE T1.LEADID_REF='$id' ORDER BY T1.TASK_ID DESC");
            
            $MAT    = count($MAT) > 0 ?$MAT:[0];

            $TASKALL = DB::table('TBL_TRN_LEAD_TASK')
                        ->leftJoin('TBL_MST_TASK_TYPE_TYPE', 'TBL_TRN_LEAD_TASK.TASKID_REF','=','TBL_MST_TASK_TYPE_TYPE.ID')
                        ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_TRN_LEAD_TASK.TASK_ASSIGNTO_REF','=','TBL_MST_EMPLOYEE.EMPID')
                        ->leftJoin('TBL_MST_PRIORITY', 'TBL_TRN_LEAD_TASK.PRIORITYID_REF','=','TBL_MST_PRIORITY.PRIORITYID')
                        ->where('TBL_TRN_LEAD_TASK.LEADID_REF','=',$id)
                        ->orderBy('TBL_TRN_LEAD_TASK.TASK_ID','DESC')
                        ->select('TBL_TRN_LEAD_TASK.*','TBL_MST_TASK_TYPE_TYPE.ID','TBL_MST_TASK_TYPE_TYPE.TASK_TYPECODE','TBL_MST_TASK_TYPE_TYPE.TASK_TYPENAME',
                        'TBL_MST_EMPLOYEE.EMPID','TBL_MST_EMPLOYEE.EMPCODE','TBL_MST_EMPLOYEE.FNAME','TBL_MST_PRIORITY.PRIORITYID','TBL_MST_PRIORITY.PRIORITYCODE','TBL_MST_PRIORITY.DESCRIPTIONS')
                        ->first();

            $objRights = DB::table('TBL_MST_USERROLMAP')
                            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
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
            $objDataList	=	DB::select("select hdr.LEAD_ID,hdr.LEAD_NO,hdr.LEAD_DT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.LEAD_ID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_LEAD_GENERATION hdr
                            on a.VID = hdr.LEAD_ID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.LEAD_ID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );
            
            return view($this->view.$FormId.$type,compact(['objResponse','objDataList','objRights','ActionStatus','FormId','MAT','TASKALL']));
        }
    }


    public function updateRecord($request,$type){        

        $LEAD_ID                   =   trim($request['LEAD_ID']);
        $TASKID_REF                =   trim($request['TASKID_REF']);
        $TASK_ASSIGNTO_REF         =   trim($request['ASSIGNTOID_REF']);
        $PRIORITYID_REF            =   trim($request['PRIORITYID_REF']);
        $DUE_DATE                  =   trim($request['DUEDATE']);
        $TASK_STATUS               =   trim($request['STATUSNAME']);
        $TASK_REMINDER_DATE        =   trim($request['REMINDERDATE']);
        $SUBJECT                   =   trim($request['TASKSUBJECT']);
        $TASK_DETAIL               =   trim($request['TASKDETAILS']);

        $CYID_REF                  =   Auth::user()->CYID_REF;
        $BRID_REF                  =   Session::get('BRID_REF');
        $FYID_REF                  =   Session::get('FYID_REF');    
        $USERID_REF                =   Auth::user()->USERID;   
        $VTID_REF                  =   $this->vtid_ref;
        $UPDATE                    =   Date('Y-m-d');
        $UPTIME                    =   Date('h:i:s.u');
        $ACTION                    =   "EDIT";
        $IPADDRESS                 =   $request->getClientIp();

        $array_data                = [$LEAD_ID,      $TASKID_REF,    $TASK_ASSIGNTO_REF,    $SUBJECT,   $PRIORITYID_REF,  $TASK_DETAIL,
                                        $DUE_DATE,   $TASK_STATUS,   $TASK_REMINDER_DATE,   $CYID_REF,  $BRID_REF,        $FYID_REF,
                                        $VTID_REF,   $USERID_REF,    $UPDATE,               $UPTIME,    $ACTION,          $IPADDRESS ];

        $sp_result = DB::select('EXEC SP_LEAD_TASK_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $array_data);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');

        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
       
        exit(); 
     
    }


    public function attachment($id){

        if(!is_null($id))
        {
            $FormId      =   $this->form_id;

            $objRes = DB::table('TBL_TRN_LEAD_TASK')
                        ->leftJoin('TBL_TRN_LEAD_GENERATION', 'TBL_TRN_LEAD_TASK.LEADID_REF','=','TBL_TRN_LEAD_GENERATION.LEAD_ID')
                        ->where('TBL_TRN_LEAD_TASK.LEADID_REF','=',$id)
                        ->orderBy('TBL_TRN_LEAD_TASK.TASK_ID','DESC')
                        ->select('TBL_TRN_LEAD_TASK.*','TBL_TRN_LEAD_GENERATION.*')
                        ->first();

            $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
                                    ->where('VTID','=',$this->vtid_ref)
                                    ->select('VTID','VCODE','DESCRIPTIONS')
                                    ->get()->toArray();
            
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

            return view($this->view.$FormId.'attachment',compact(['objRes','objMstVoucherType','objAttachments','FormId']));
        }

    }

    
   public function docuploads(Request $request){

    $FormId   =   $this->form_id;
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
    
    $destinationPath = storage_path()."/docs/company".$CYID_REF."/LeadGeneration";

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
        return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","The file is already exist");
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
                       
/*************************************   Customer Code    ****************************************************** */

            public function getCustomerCode(Request $request){

                $type   =   $request['type'];

                if($type ==="Customer"){
                    $ObjData    =   DB::table('TBL_MST_CUSTOMER')
                                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                                    ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                                    ->where('STATUS','=','A')
                                    ->select('SLID_REF','CCODE','NAME')
                                    ->get();
                }
                else{
                    $ObjData    =   DB::table('TBL_MST_PROSPECT')
                                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                                    ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                                    ->where('STATUS','=','A')
                                    ->select('PID AS SLID_REF','PCODE AS CCODE','NAME')
                                    ->get();
                }

                if(isset($ObjData) && !empty($ObjData)){
                    foreach ($ObjData as $index=>$dataRow){

                    echo'
                    <tr>
                        <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->SLID_REF .'" class="cls'.$type.'" value="'.$dataRow->SLID_REF.'" ></td>
                        <td class="ROW2">'.$dataRow->CCODE.'</td>
                        <td class="ROW3">'.$dataRow->NAME.'</td>
                        <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->SLID_REF.'" data-desc="'.$dataRow->CCODE.'-'.$dataRow->NAME.'" data-ccname="'.$dataRow->NAME.'" value="'.$dataRow->SLID_REF.'"/></td>
                    </tr>
                    ';
                    }
                }
                else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
            exit();
            }
                 
/*************************************   Task Type Code    ****************************************************** */

        public function getTaskType(Request $request){

            $listid =   isset($request['listid']) && $request['listid'] !=''?explode(',',$request['listid']):[];
            
            $ObjData = DB::table('TBL_MST_TASK_TYPE_TYPE')
                        ->where('STATUS','=','A')
                        ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                        ->get();

            if(isset($ObjData) && !empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){

                    $checked    =   in_array($dataRow->ID,$listid)?'checked':'';

                echo'
                <tr>
                    <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->ID .'" class="clsacttype" value="'.$dataRow->ID.'" '.$checked.' ></td>
                    <td class="ROW2">'.$dataRow->TASK_TYPECODE.'</td>
                    <td class="ROW3">'.$dataRow->TASK_TYPENAME.'</td>
                    <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->ID.'" data-desc="'.$dataRow->TASK_TYPECODE.'-'.$dataRow->TASK_TYPENAME.'" data-ccname="'.$dataRow->TASK_TYPENAME.'" value="'.$dataRow->ID.'"/></td>
                </tr>
                ';
                }
            }
            else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
        exit();
    }

/*************************************   Assigned To Code    ****************************************************** */

            public function getAssignedTo(Request $request){

                $listid =   isset($request['listid']) && $request['listid'] !=''?explode(',',$request['listid']):[];
                
                $ObjData = DB::table('TBL_MST_EMPLOYEE')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('STATUS','=','A')
                            ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                            ->get();

                if(isset($ObjData) && !empty($ObjData)){
                    foreach ($ObjData as $index=>$dataRow){

                        $checked    =   in_array($dataRow->EMPID,$listid)?'checked':'';

                    echo'
                    <tr>
                        <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->EMPID .'" class="clsassgnto" value="'.$dataRow->EMPID.'" '.$checked.' ></td>
                        <td class="ROW2">'.$dataRow->EMPCODE.'</td>
                        <td class="ROW3">'.$dataRow->FNAME.'</td>
                        <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->EMPID.'" data-desc="'.$dataRow->EMPCODE.'-'.$dataRow->FNAME.'" data-ccname="'.$dataRow->FNAME.'" value="'.$dataRow->EMPID.'"/></td>
                    </tr>
                    ';
                    }
                }
                else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
            exit();
            }

/*************************************   Priority Code    ****************************************************** */

        public function getPriority(Request $request){

            $listid =   isset($request['listid']) && $request['listid'] !=''?explode(',',$request['listid']):[];
            
            $ObjData = DB::table('TBL_MST_PRIORITY')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('STATUS','=','A')
                        ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                        ->get();

            if(isset($ObjData) && !empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){

                    $checked    =   in_array($dataRow->PRIORITYID,$listid)?'checked':'';

                echo'
                <tr>
                    <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->PRIORITYID .'" class="clspriort" value="'.$dataRow->PRIORITYID.'" '.$checked.' ></td>
                    <td class="ROW2">'.$dataRow->PRIORITYCODE.'</td>
                    <td class="ROW3">'.$dataRow->DESCRIPTIONS.'</td>
                    <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->PRIORITYID.'" data-desc="'.$dataRow->PRIORITYCODE.'-'.$dataRow->DESCRIPTIONS.'" data-ccname="'.$dataRow->DESCRIPTIONS.'" value="'.$dataRow->PRIORITYID.'"/></td>
                </tr>
                ';
                }
            }
            else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
        exit();
        }

  
        










































































}