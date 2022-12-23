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

class TrnFrm483Controller extends Controller{

    protected $form_id    = 483;
    protected $vtid_ref   = 553;
    protected $view       = "transactions.PreSales.FollowUp.trnfrm";
       
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


            $MAT = DB::select("SELECT T1.*,T2.*,T3.*,T4.* FROM TBL_TRN_LEAD_ACTIVITY T1
            LEFT JOIN TBL_MST_ACTIVITY_TYPE T2 ON T1.ACTIVITYID_REF=T2.ID
            LEFT JOIN TBL_MST_RESPONSE_TYPE T3 ON T1.RESPONSEID_REF=T3.ID
            LEFT JOIN TBL_MST_EMPLOYEE      T4 ON T1.ALERT_TO=T4.EMPID
            WHERE T1.LEADID_REF='$id' ORDER BY T1.ACTIVITY_ID DESC");

            if(isset($MAT) && !empty($MAT)){
                foreach($MAT as $key=>$val){

                    $ADDITIONAL_EMPLOYEE_ID       =   $val->ADDITIONAL_EMPLOYEE_ID;

                    if($ADDITIONAL_EMPLOYEE_ID !=""){
                        $LEADACT_DATA = DB::select("select distinct stuff((select ',' + t.[FNAME] from TBL_MST_EMPLOYEE t where EMPID in($ADDITIONAL_EMPLOYEE_ID) order by t.[FNAME] for xml path('') ),1,1,'') as CODE_NAME from TBL_MST_EMPLOYEE t1 where EMPID in($ADDITIONAL_EMPLOYEE_ID)"); 
                        $CODE_NAME =   isset($LEADACT_DATA[0]->CODE_NAME) && $LEADACT_DATA[0]->CODE_NAME !=""?$LEADACT_DATA[0]->CODE_NAME:NULL; 
                        $MAT[$key]->CODE_NAME=$CODE_NAME;
                    }
                }
            }
            $MAT    = count($MAT) > 0 ?$MAT:[0];

        return view($this->view.$FormId.'add',compact(['objDD','objResponse','FormId','MAT']));
    }

   
    public function save(Request $request){

        $ACTIVITYID_REF            =   trim($request['ACTIVITYID_REF'])?trim($request['ACTIVITYID_REF']):NULL;
        $ACTIVITY_DATE             =   trim($request['ACTITITY_DATE'])?trim($request['ACTITITY_DATE']):NULL;
        $CONTACT_PERSON            =   trim($request['CONTACTPERSON'])?trim($request['CONTACTPERSON']):NULL;
        $REMINDER_DETAIL           =   trim($request['REMNDETAIL_REF'])?trim($request['REMNDETAIL_REF']):NULL;
        $ADDITIONAL_EMPLOYEE_ID    =   trim($request['ADDMEMBERVISIT_REF'])?trim($request['ADDMEMBERVISIT_REF']):NULL;
        $RESPONSEID_REF            =   trim($request['RESPONSEID_REF'])?trim($request['RESPONSEID_REF']):NULL;
        $ACTIVITY_DETAILS          =   trim($request['ACTYDETAIL'])?trim($request['ACTYDETAIL']):NULL;
        $ACTION_PLAN               =   trim($request['ACTYNAMEPLAN'])?trim($request['ACTYNAMEPLAN']):NULL;
        $LEAD_ID                   =   trim($request['LEAD_ID'])?trim($request['LEAD_ID']):NULL;
        
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');    
        $USERID_REF     =   Auth::user()->USERID;   
        $VTID_REF       =   $this->vtid_ref;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();

        $array_data     = [$LEAD_ID,            $ACTIVITYID_REF,    $ACTIVITY_DATE,                 $CONTACT_PERSON,   $ACTIVITY_DETAILS,  $RESPONSEID_REF,
                            $ACTION_PLAN,       $REMINDER_DETAIL,   $ADDITIONAL_EMPLOYEE_ID,        $CYID_REF,         $BRID_REF,           $FYID_REF,
                            $VTID_REF,          $USERID_REF,        $UPDATE,                        $UPTIME,           $ACTION,             $IPADDRESS ];

                            //DD($array_data);

        $sp_result = DB::select('EXEC SP_LEAD_ACTIVITY_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $array_data);

        //dd($sp_result);

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

            

            $MAT = DB::select("SELECT T1.*,T2.*,T3.*,T4.* FROM TBL_TRN_LEAD_ACTIVITY T1
            LEFT JOIN TBL_MST_ACTIVITY_TYPE T2 ON T1.ACTIVITYID_REF=T2.ID
            LEFT JOIN TBL_MST_RESPONSE_TYPE T3 ON T1.RESPONSEID_REF=T3.ID
            LEFT JOIN TBL_MST_EMPLOYEE      T4 ON T1.ALERT_TO=T4.EMPID
            WHERE T1.LEADID_REF='$id' ORDER BY T1.ACTIVITY_ID DESC");

            if(isset($MAT) && !empty($MAT)){
                foreach($MAT as $key=>$val){

                    $ADDITIONAL_EMPLOYEE_ID       =   $val->ADDITIONAL_EMPLOYEE_ID;

                    if($ADDITIONAL_EMPLOYEE_ID !=""){
                        $LEADACT_DATA = DB::select("select distinct stuff((select ',' + t.[FNAME] from TBL_MST_EMPLOYEE t where EMPID in($ADDITIONAL_EMPLOYEE_ID) order by t.[FNAME] for xml path('') ),1,1,'') as CODE_NAME from TBL_MST_EMPLOYEE t1 where EMPID in($ADDITIONAL_EMPLOYEE_ID)"); 
                        $CODE_NAME =   isset($LEADACT_DATA[0]->CODE_NAME) && $LEADACT_DATA[0]->CODE_NAME !=""?$LEADACT_DATA[0]->CODE_NAME:NULL; 
                        $MAT[$key]->CODE_NAME=$CODE_NAME;
                    }
                }
            }
            $MAT    = count($MAT) > 0 ?$MAT:[0];

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
            
            return view($this->view.$FormId.$type,compact(['objResponse','objDataList','objRights','ActionStatus','FormId','MAT']));
        }
    }


    public function updateRecord($request,$type){

        $FormId     =   $this->form_id;
        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref; 
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');   

        $sp_Approvallevel = [
            $USERID_REF, $VTID_REF, $CYID_REF,$BRID_REF,
            $FYID_REF
        ];
        
        $data = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?, ?', $sp_Approvallevel);

        if(!empty($data)){
            foreach ($data as $key=>$val){  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$val->LAVELS;
            }
        }

        $requestType    =   $request->requestType;
        $Approvallevel  =   $requestType =='update'?'EDIT':$Approvallevel;
        $msgTxt         =   $requestType =='update'?'updated':'approved';

        $ACTIVITYID_REF            =   trim($request['ACTIVITY_REF']);
        $ACTIVITY_DATE             =   trim($request['FLOUP_DT']);
        $CONTACT_PERSON            =   trim($request['CONTACTPERSON']);
        $REMINDER_DETAIL           =   trim($request['REMNDETAILID_REF']);
        $ADDITIONAL_EMPLOYEE_ID    =   trim($request['ADDMEMBERVISIT_REF']);
        $RESPONSEID_REF            =   trim($request['RESPONSE_REF']);
        $ACTIVITY_DETAILS          =   trim($request['ACTYDETAIL']);
        $ACTION_PLAN               =   trim($request['ACTYPLAN']);
        $LEAD_ID                =    trim($request['LEAD_ID']);
        $ACTIVITY_ID               =    trim($request['ACTIVITY_ID']);
        
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');    
        $USERID_REF     =   Auth::user()->USERID;   
        $VTID_REF       =   $this->vtid_ref;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   $Approvallevel;
        $IPADDRESS      =   $request->getClientIp();

        $array_data     = [$LEAD_ID,            $ACTIVITYID_REF,    $ACTIVITY_DATE,                 $CONTACT_PERSON,   $ACTIVITY_DETAILS,  $RESPONSEID_REF,
                            $ACTION_PLAN,       $REMINDER_DETAIL,   $ADDITIONAL_EMPLOYEE_ID,        $CYID_REF,         $BRID_REF,           $FYID_REF,
                            $VTID_REF,          $USERID_REF,        $UPDATE,                        $UPTIME,           $ACTION,             $IPADDRESS ];

        $sp_result = DB::select('EXEC SP_LEAD_ACTIVITY_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $array_data);

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

            $objRes = DB::table('TBL_TRN_LEAD_GENERATION')                    
            ->where('TBL_TRN_LEAD_ACTIVITY.LEADID_REF','=',$id)
            ->leftJoin('TBL_TRN_LEAD_ACTIVITY', 'TBL_TRN_LEAD_GENERATION.LEAD_ID','=','TBL_TRN_LEAD_ACTIVITY.LEADID_REF')
            ->select('TBL_TRN_LEAD_GENERATION.*', 'TBL_TRN_LEAD_ACTIVITY.*')
            ->orderBy('TBL_TRN_LEAD_ACTIVITY.LEADID_REF','DESC')
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

                 
/*************************************   Activity Type Code    ****************************************************** */

        public function getActivityType(Request $request){

            $listid =   isset($request['listid']) && $request['listid'] !=''?explode(',',$request['listid']):[];
            
            $ObjData = DB::table('TBL_MST_ACTIVITY_TYPE')
            ->where('STATUS','=','A')
            ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
            ->get();

            if(isset($ObjData) && !empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){

                    $checked    =   in_array($dataRow->ID,$listid)?'checked':'';

                echo'
                <tr>
                    <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->ID .'" class="clsacttype" value="'.$dataRow->ID.'" '.$checked.' ></td>
                    <td class="ROW2">'.$dataRow->ACTIVITYCODE.'</td>
                    <td class="ROW3">'.$dataRow->ACTIVITYNAME.'</td>
                    <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->ID.'" data-desc="'.$dataRow->ACTIVITYCODE.'-'.$dataRow->ACTIVITYNAME.'" data-ccname="'.$dataRow->ACTIVITYNAME.'" value="'.$dataRow->ID.'"/></td>
                </tr>
                ';
                }
            }
            else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
        exit();
    }


                   
/*************************************   Additonal Member Visit Code    ****************************************************** */
        
        public function getAddMemberVisitCode(Request $request){

            $listid =   isset($request['listid']) && $request['listid'] !=''?explode(',',$request['listid']):[];

            $ObjData = DB::table('TBL_MST_EMPLOYEE')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
            ->where('STATUS','=','A')
            ->get();

            if(isset($ObjData) && !empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){

                    $checked    =   in_array($dataRow->EMPID,$listid)?'checked':'';

                echo'
                <tr>
                    <td class="ROW1"><input type="checkbox" name="subgl[]" id="subgl_'.$dataRow->EMPID .'" class="clsaddmeb" data-desc1="'.$dataRow->FNAME .'" value="'.$dataRow->EMPID.'" '.$checked.'></td>
                    <td class="ROW2">'.$dataRow->EMPCODE.'</td>
                    <td class="ROW3">'.$dataRow->FNAME.'</td>
                    <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->EMPID.'" data-desc="'.$dataRow->EMPCODE .'" data-ccname="'.$dataRow->FNAME.'" data-desckey="'.$index.'" value="'.$dataRow->EMPID.'"/></td>
                </tr>
                ';
                }
            }
            else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
        exit();
        }

                 
/*************************************   Response Code    ****************************************************** */

        public function getResponseCode(Request $request){

            $listid =   isset($request['listid']) && $request['listid'] !=''?explode(',',$request['listid']):[];

            $ObjData = DB::table('TBL_MST_RESPONSE_TYPE')
            ->where('STATUS','=','A')
            ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
            ->get();

            if(isset($ObjData) && !empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){

                    $checked    =   in_array($dataRow->ID,$listid)?'checked':'';

                echo'
                <tr>
                    <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->ID .'" class="clsres" value="'.$dataRow->ID.'" '.$checked.'></td>
                    <td class="ROW2">'.$dataRow->RESPONSECODE.'</td>
                    <td class="ROW3">'.$dataRow->RESPONSENAME.'</td>
                    <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->ID.'" data-desc="'.$dataRow->RESPONSECODE.'-'.$dataRow->RESPONSENAME.'" data-ccname="'.$dataRow->RESPONSENAME.'" value="'.$dataRow->ID.'"/></td>
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