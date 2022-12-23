<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm502;
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

class MstFrm502Controller extends Controller{
    protected $form_id  =   502;
    protected $vtid_ref =   572;
    protected $view     =   "masters.Common.PeriodClosing.mstfrm502";

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){  
        
        $objRights  =   DB::table('TBL_MST_USERROLMAP')
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

        $objDataList	=	DB::select("select 
        hdr.PERIODCLID,hdr.PERIODCL_NO,hdr.PERIODCL_DATE,hdr.INDATE,hdr.STATUS,
        (
        SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
        LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
        WHERE  AUD.VID=hdr.PERIODCLID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
        AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
        ) AS CREATED_BY,
        
        case when a.ACTIONNAME = '$FANO' then 'Final Approved' 
        else case when a.ACTIONNAME = 'ADD' then 'Added'  
            when a.ACTIONNAME = 'EDIT' then 'Edited'
            when a.ACTIONNAME = 'APPROVAL1' then 'First Level Approved'
            when a.ACTIONNAME = 'APPROVAL2' then 'Second Level Approved'
            when a.ACTIONNAME = 'APPROVAL3' then 'Third Level Approved'
            when a.ACTIONNAME = 'APPROVAL4' then 'Fourth Level Approved'
            when a.ACTIONNAME = 'APPROVAL5' then 'Final Approved' 
            when a.ACTIONNAME = 'CANCEL' then 'Cancelled'
            when a.ACTIONNAME = 'CLOSE' then 'Closed'
        end end as STATUS_DESC
        from TBL_TRN_AUDITTRAIL a 
        inner join TBL_MST_PERIOD_CLOSING_HRD hdr
        on a.VID = hdr.PERIODCLID 
        
        and a.CYID_REF = hdr.CYID_REF 
        and a.BRID_REF = hdr.BRID_REF
        where a.VTID_REF = '$this->vtid_ref'
        and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
        and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
        ORDER BY hdr.PERIODCLID DESC ");
        // and a.VTID_REF = hdr.VTID_REF 

        $REQUEST_DATA   =   array(
            'FORMID'    =>  $this->form_id,
            'VTID_REF'  =>  $this->vtid_ref,
            'USERID'    =>  Auth::user()->USERID,
            'CYID_REF'  =>  Auth::user()->CYID_REF,
            'BRID_REF'  =>  Session::get('BRID_REF'),
            'FYID_REF'  =>  Session::get('FYID_REF'),
        );

        return view($this->view,compact(['REQUEST_DATA','FormId','objRights','objDataList','FormId']));
    }

    public function add(){

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $IPO_HDR    =   $this->getIPOList();

        $objSON     =   DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('FYID_REF','=',$FYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_DOCNO_DEFINITION.*')
        ->first();

        $fyear    =   $this->getFYearsDetails();  

        $objAutoGenNo   =   '';
        if(!empty($objSON )){

            if(isset($objSON->SYSTEM_GRSR) && $objSON->SYSTEM_GRSR == "1"){
                if($objSON->PREFIX_RQ == "1")
                {
                    $objAutoGenNo = $objSON->PREFIX;
                }        
                if($objSON->PRE_SEP_RQ == "1")
                {
                    if($objSON->PRE_SEP_SLASH == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'/';
                    }
                    if($objSON->PRE_SEP_HYPEN == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'-';
                    }
                }        
                if($objSON->NO_MAX)
                {   
                    $objAutoGenNo = $objAutoGenNo.str_pad($objSON->LAST_RECORDNO+1, $objSON->NO_MAX, "0", STR_PAD_LEFT);
                }
                
                if($objSON->NO_SEP_RQ == "1")
                {
                    if($objSON->NO_SEP_SLASH == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'/';
                    }
                    if($objSON->NO_SEP_HYPEN == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'-';
                    }
                }
                if($objSON->SUFFIX_RQ == "1")
                {
                    $objAutoGenNo = $objAutoGenNo.$objSON->SUFFIX;
                }
            }
        }  
        
        //$lastDocDate    =   $this->LastApprovedDocDate();    
        $FormId         =   $this->form_id;

        return view($this->view.'add', compact(['FormId','objSON','objAutoGenNo','IPO_HDR','fyear']));       
    }

    public function save(Request $request){

        $VTID_REF       =   $this->vtid_ref;
        $USERID_REF     =   Auth::user()->USERID;   
        $ACTION         =   'ADD';
        $IPADDRESS      =   $request->getClientIp();
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $UPDATE         =   Date('Y-m-d');        
        $UPTIME         =   Date('h:i:s.u');

        $PERIODCL_NO          =   $request['DOC_NO'];
        $PERIODCL_DATE        =   $request['DOC_DT'];
        $PERIODCL_FROM_DATE   =   $request['FROMDT'];
        $PERIODCL_TO_DATE     =   $request['TODT'];
        $PERIODCL_MODULE      =   $request['MODULEIDREF'];
        $PERIODCL_MONTH       =   $request['MONTHID_REF'];
        

        //dd($request->all());

        $MatDetails  = array();
        if(isset($request['PDCLNAME_REF']) && !empty($_REQUEST['PDCLNAME_REF'])){
            foreach($request['PDCLNAME_REF'] as $key=>$val){

                $MatDetails[] = array(
                'PERIODCL_FORM_NAME'     => trim($request['PDCLNAME_REF'][$key])?trim($request['PDCLNAME_REF'][$key]):NULL,
                'PERIODCL_PDLOCK_TYPE'   => trim($request['LTID_REF'][$key])?trim($request['LTID_REF'][$key]):NULL,
                'PERIODCL_MAT_FROM_DATE' => trim($request['PDCLFROMDT'][$key])?trim($request['PDCLFROMDT'][$key]):NULL,
                'PERIODCL_MAT_TO_DATE'   => trim($request['PDCLTODT'][$key])?trim($request['PDCLTODT'][$key]):NULL,
                'PERIODCL_DAYS'          => trim($request['PDCLDAYS'][$key])?trim($request['PDCLDAYS'][$key]):NULL,
                );
            }
        }

        //dd($MatDetails);


        if(!empty($MatDetails)){
            $wrapped_links["MAT"] = $MatDetails; 
            $MAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $MAT = NULL; 
        }  

        $log_data = [ $PERIODCL_NO,        $PERIODCL_DATE,    $PERIODCL_FROM_DATE,  $PERIODCL_TO_DATE,  $PERIODCL_MODULE,
                    $PERIODCL_MONTH,       $CYID_REF,         $BRID_REF,            $FYID_REF,          $VTID_REF,       
                    $MAT,                  $USERID_REF,       $UPDATE,              $UPTIME,            $ACTION,    $IPADDRESS ]; 

        $sp_result = DB::select('EXEC SP_PERIOD_CLOSING_IN ?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?', $log_data);  
        
        $contains   =   Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);
        }
        else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
    }

    public function edit($id=NULL){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))            
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $fyear    =   $this->getFYearsDetails();
            //$lastDocDate    =   $this->LastApprovedDocDate();    
      
            $HDR            =   DB::select("SELECT 
                                    T.*,T1.*,T2.* 
                                    FROM TBL_MST_PERIOD_CLOSING_HRD T
                                    LEFT JOIN TBL_MST_MODULE T1 ON T.PERIODCL_MODULE=T1.MODULEID
                                    LEFT JOIN TBL_MST_MONTH T2 ON T.PERIODCL_MONTH=T2.MTID
                                    WHERE T.PERIODCLID='$id'"); 

            $HDR            =   count($HDR) > 0?$HDR[0]:[];
            //dd($HDR);                                               
                                    
            $MAT = DB::select("SELECT T1.*,T2.*
                        FROM TBL_MST_PERIOD_CLOSING_MAT T1
                        LEFT JOIN TBL_MST_VOUCHERTYPE   T2 ON T1.PERIODCL_FORM_NAME=T2.VTID
                        WHERE T1.PERIODCLID_REF='$id'");

            //$MAT            =   count($MAT) > 0?$MAT[0]:[];
            //dd($MAT);

            $FormId         =   $this->form_id;
            $ActionStatus   =   "";

            $checkAttachment    =   $this->checkAttachment($id);

            return view($this->view.'edit',compact(['FormId','objRights','ActionStatus','HDR','MAT','checkAttachment','fyear']));      

        }
     
    }

    public function update(Request $request){

        $VTID_REF       =   $this->vtid_ref;
        $USERID_REF     =   Auth::user()->USERID;   
        $ACTION         =   'EDIT';
        $IPADDRESS      =   $request->getClientIp();
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $UPDATE         =   Date('Y-m-d');        
        $UPTIME         =   Date('h:i:s.u');
    
        $PERIODCL_NO          =   $request['DOC_NO'];
        $PERIODCL_DATE        =   $request['DOC_DT'];
        $PERIODCL_FROM_DATE   =   $request['FROMDT'];
        $PERIODCL_TO_DATE     =   $request['TODT'];
        $PERIODCL_MODULE      =   $request['MODULEIDREF'];
        $PERIODCL_MONTH       =   $request['MONTHID_REF'];
        

        //dd($request->all());

        $MatDetails  = array();
        if(isset($request['PDCLNAME_REF']) && !empty($_REQUEST['PDCLNAME_REF'])){
            foreach($request['PDCLNAME_REF'] as $key=>$val){

                $MatDetails[] = array(
                'PERIODCL_FORM_NAME'     => trim($request['PDCLNAME_REF'][$key])?trim($request['PDCLNAME_REF'][$key]):NULL,
                'PERIODCL_PDLOCK_TYPE'   => trim($request['LTID_REF'][$key])?trim($request['LTID_REF'][$key]):NULL,
                'PERIODCL_MAT_FROM_DATE' => trim($request['PDCLFROMDT'][$key])?trim($request['PDCLFROMDT'][$key]):NULL,
                'PERIODCL_MAT_TO_DATE'   => trim($request['PDCLTODT'][$key])?trim($request['PDCLTODT'][$key]):NULL,
                'PERIODCL_DAYS'          => trim($request['PDCLDAYS'][$key])?trim($request['PDCLDAYS'][$key]):NULL,
                );
            }
        }

        //dd($MatDetails);


        if(!empty($MatDetails)){
            $wrapped_links["MAT"] = $MatDetails; 
            $MAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $MAT = NULL; 
        }  

        $log_data = [ $PERIODCL_NO,        $PERIODCL_DATE,    $PERIODCL_FROM_DATE,  $PERIODCL_TO_DATE,  $PERIODCL_MODULE,
                    $PERIODCL_MONTH,       $CYID_REF,         $BRID_REF,            $FYID_REF,          $VTID_REF,       
                    $MAT,                  $USERID_REF,       $UPDATE,              $UPTIME,            $ACTION,    $IPADDRESS ];
                    
        $sp_result = DB::select('EXEC SP_PERIOD_CLOSING_UP ?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?', $log_data);
         
        $contains   =   Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){

            return Response::json(['success' =>true,'msg' => 'Record Successfully Updated.']);
        }
        else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();
    } 
   
       
    public function view($id=NULL){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))            
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $fyear    =   $this->getFYearsDetails();
            //$lastDocDate    =   $this->LastApprovedDocDate();    
      
            $HDR            =   DB::select("SELECT 
                                    T.*,T1.*,T2.* 
                                    FROM TBL_MST_PERIOD_CLOSING_HRD T
                                    LEFT JOIN TBL_MST_MODULE T1 ON T.PERIODCL_MODULE=T1.MODULEID
                                    LEFT JOIN TBL_MST_MONTH T2 ON T.PERIODCL_MONTH=T2.MTID
                                    WHERE T.PERIODCLID='$id'"); 

            $HDR            =   count($HDR) > 0?$HDR[0]:[];
            //dd($HDR);                                               
                                    
            $MAT = DB::select("SELECT T1.*,T2.*
                        FROM TBL_MST_PERIOD_CLOSING_MAT T1
                        LEFT JOIN TBL_MST_VOUCHERTYPE   T2 ON T1.PERIODCL_FORM_NAME=T2.VTID
                        WHERE T1.PERIODCLID_REF='$id'");

            //$MAT            =   count($MAT) > 0?$MAT[0]:[];
            //dd($MAT);

            $FormId         =   $this->form_id;
            $ActionStatus   =   "disabled";

            $checkAttachment    =   $this->checkAttachment($id);

            return view($this->view.'view',compact(['FormId','objRights','ActionStatus','HDR','MAT','checkAttachment','fyear'])); 

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
        
        $sp_listing_result  = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?, ?', $sp_Approvallevel);
        $Approvallevel      =   NULL;
        if(!empty($sp_listing_result)){
            foreach ($sp_listing_result as $key=>$salesenquiryitem){  
            $record_status = 0;
            $Approvallevel = "APPROVAL".$salesenquiryitem->LAVELS;
            }
        }

           
        $VTID_REF       =   $this->vtid_ref;
        $USERID_REF     =   Auth::user()->USERID;   
        $ACTION         =   $Approvallevel;
        $IPADDRESS      =   $request->getClientIp();
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $UPDATE         =   Date('Y-m-d');        
        $UPTIME         =   Date('h:i:s.u');
    
        $PERIODCL_NO          =   $request['DOC_NO'];
        $PERIODCL_DATE        =   $request['DOC_DT'];
        $PERIODCL_FROM_DATE   =   $request['FROMDT'];
        $PERIODCL_TO_DATE     =   $request['TODT'];
        $PERIODCL_MODULE      =   $request['MODULEIDREF'];
        $PERIODCL_MONTH       =   $request['MONTHID_REF'];
        

        //dd($request->all());

        $MatDetails  = array();
        if(isset($request['PDCLNAME_REF']) && !empty($_REQUEST['PDCLNAME_REF'])){
            foreach($request['PDCLNAME_REF'] as $key=>$val){

                $MatDetails[] = array(
                'PERIODCL_FORM_NAME'     => trim($request['PDCLNAME_REF'][$key])?trim($request['PDCLNAME_REF'][$key]):NULL,
                'PERIODCL_PDLOCK_TYPE'   => trim($request['LTID_REF'][$key])?trim($request['LTID_REF'][$key]):NULL,
                'PERIODCL_MAT_FROM_DATE' => trim($request['PDCLFROMDT'][$key])?trim($request['PDCLFROMDT'][$key]):NULL,
                'PERIODCL_MAT_TO_DATE'   => trim($request['PDCLTODT'][$key])?trim($request['PDCLTODT'][$key]):NULL,
                'PERIODCL_DAYS'          => trim($request['PDCLDAYS'][$key])?trim($request['PDCLDAYS'][$key]):NULL,
                );
            }
        }

        //dd($MatDetails);

        if(!empty($MatDetails)){
            $wrapped_links["MAT"] = $MatDetails; 
            $MAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $MAT = NULL; 
        }  

        $log_data = [ $PERIODCL_NO,        $PERIODCL_DATE,    $PERIODCL_FROM_DATE,  $PERIODCL_TO_DATE,  $PERIODCL_MODULE,
                    $PERIODCL_MONTH,       $CYID_REF,         $BRID_REF,            $FYID_REF,          $VTID_REF,       
                    $MAT,                  $USERID_REF,       $UPDATE,              $UPTIME,            $ACTION,    $IPADDRESS ];
                    
        $sp_result = DB::select('EXEC SP_PERIOD_CLOSING_UP ?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?', $log_data);
         
        $contains   =   Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => 'Record Successfully Approved.']);
        }
        else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();      
    }
 
    public function cancel(Request $request){

        $id = $request->{0};

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_PERIOD_CLOSING_HRD";
        $FIELD      =   "PERIODCLID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_MST_PERIOD_CLOSING_MAT',
        ];

        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

        if($sp_result[0]->RESULT=="CANCELED"){  

            return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
        
            return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'invalid'=>'invalid']);
        }
        
        exit(); 
    }

    public function attachment($id){

        $FormId = $this->form_id;
        if(!is_null($id)){
            $objMst =   DB::table("TBL_MST_PERIOD_CLOSING_HRD")
            ->where('PERIODCLID','=',$id)
            ->select('*')
            ->first();        

            $objMstVoucherType  =   DB::table("TBL_MST_VOUCHERTYPE")
            ->where('VTID','=',$this->vtid_ref)
            ->select('VTID','VCODE','DESCRIPTIONS','INDATE')
            ->get()
            ->toArray();
                        
            $objAttachments =   DB::table('TBL_MST_ATTACHMENT')                    
            ->where('TBL_MST_ATTACHMENT.VTID_REF','=',$this->vtid_ref)
            ->where('TBL_MST_ATTACHMENT.ATTACH_DOCNO','=',$id)
            ->where('TBL_MST_ATTACHMENT.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_ATTACHMENT.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_ATTACHMENT.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
            ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
            ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
            ->get()->toArray();

            $dirname =   'PeriodClosing';
                
            return view($this->view.'attachment',compact(['FormId','objMst','objMstVoucherType','objAttachments','dirname']));
        }

    }
  
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
        
		$image_path         =   "docs/company".$CYID_REF."/PeriodClosing";     
		$destinationPath    =   str_replace('\\', '/', public_path($image_path));
		
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

                    $filenametostore        =  $VTID.$ATTACH_DOCNO.date('YmdHis')."_".str_replace(' ', '', $filenamewithextension);   

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

    // public function LastApprovedDocDate(){
    //     $Status     =   "A";
    //     $CYID_REF   =   Auth::user()->CYID_REF;
    //     $BRID_REF   =   Session::get('BRID_REF');
    //     $FYID_REF   =   Session::get('FYID_REF');
    //     $VTID_REF   =   $this->vtid_ref;

    //     return $objlastDocDate = DB::select('SELECT MAX(CINV_DT) CINV_DT FROM TBL_TRN_COM_INV_HDR  
    //     WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
    //     [$CYID_REF, $BRID_REF,  $VTID_REF, $Status ]);

    // }

    public function getIPOList(){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        return  DB::select("SELECT 
        T1.IPO_ID,T1.IPO_NO,T1.IPO_DT,T1.VID_REF,T2.SGLID,T2.SGLCODE,T2.SLNAME
        FROM TBL_TRN_IPO_HDR T1 
        LEFT JOIN TBL_MST_SUBLEDGER T2 ON T1.VID_REF=T2.SGLID
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' AND T1.STATUS='A'");
    }

    public function checkAttachment($id){
        $count  =   DB::table('TBL_MST_ATTACHMENT')                    
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('ATTACH_DOCNO','=',$id)
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->count();
     
        if($count > 0){
            $result =   1;
        }
        else{
            $result =   0;
        }
        
        return $result;
    }

/*************************************   Module Code    ****************************************************** */

    public function getModuleDetails(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $objData    =   DB::table('TBL_MST_MODULE')
                        ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                        ->where('STATUS','=','A')
                        ->select('MODULEID','MODULECODE','MODULENAME')
                        ->get();  
                        
        return Response::json($objData);                                     
    }


/*************************************  Month Code    ****************************************************** */

    public function getMonthDetails(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $objData    =   DB::table('TBL_MST_MONTH')
                        //->where('CYID_REF','=',Auth::user()->CYID_REF)
                        //->where('BRID_REF','=',Session::get('BRID_REF'))
                        //->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                        ->where('STATUS','=','A')
                        ->select('MTID','MTCODE','MTDESCRIPTION')
                        ->get();

        return Response::json($objData);                      
        
    }

/*************************************   Form Name Code    ****************************************************** */

    public function getFormNameDetails(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $ModuleId   =   $request['ids'];

        $objData = DB::select("SELECT T1.*,T2.*,T3.*
                FROM TBL_MST_MODULE_VOUCHER_MAP T1
                LEFT JOIN TBL_MST_MODULE        T2 ON T1.MODULEID_REF=T2.MODULEID
                LEFT JOIN TBL_MST_VOUCHERTYPE   T3 ON T1.VTID_REF=T3.VTID
                WHERE T1.MODULEID_REF IN ($ModuleId) AND T1.STATUS='A' AND T1.HEADING='Transactions'"); 
                                    
        if(!empty($objData)){
            foreach ($objData as $index=>$dataRow){ 

                echo'                            
                <tr>
                <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->VTID .'" name="VTID_REF[]" value="'.$dataRow->VTID.'" data-desc="'.$dataRow->DESCRIPTIONS.'"  class="clsfname" onchange="appentFormName()"  ></td>
                <td class="ROW2">'.$dataRow->MODULENAME.'</td>
                <td class="ROW3">'.$dataRow->DESCRIPTIONS.'</td>                            
                <td hidden><input type="hidden" name="MODULE_NAME[]" value="'.$dataRow->DESCRIPTIONS.'"/></td>                       
                </tr>';                
            }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }          
        
        exit();
    }

    public function getFYearsDetails(){

        $objData = DB::table('TBL_MST_FYEAR')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('FYID','=',Session::get('FYID_REF'))
                //->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                //->where('STATUS','=','A')
                ->first();

        return $objData; 
    }



    public function getYear(Request $request){

        $startDate  =   trim($request['FROM_DT']);
        $endDate    =   trim($request['TO_DT']);
        $month      =   trim($request['month_code']);
        $year       =   '';

        while (strtotime($startDate) <= strtotime($endDate)){
            $m  =   date('m', strtotime($startDate));
            $y  =   date('Y', strtotime($startDate));

            if($month ==$m){
                $year = $y;
            }
                
            $startDate = date('01 M Y', strtotime($startDate . '+ 1 month'));
        }

        return Response::json($year);
    }






















    
}
