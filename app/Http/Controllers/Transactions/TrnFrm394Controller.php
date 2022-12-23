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

class TrnFrm394Controller extends Controller{

    protected $form_id  = 394;
    protected $vtid_ref = 478;
    protected $view     = "transactions.Accounts.BankReconcilation.trnfrm";
   
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];

    public function __construct(){
        $this->middleware('auth');
    }

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
		$CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');     

        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList	=	DB::select("select hdr.BANK_RECONCILEID,hdr.BANK_RECONCILE_CODE,hdr.BANK_RECONCILE_DATE,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.BANK_RECONCILEID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF='$this->vtid_ref' AND AUD.ACTIONNAME='ADD'       
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
                            inner join TBL_TRN_BANK_RECONCILATION hdr
                            on a.VID = hdr.BANK_RECONCILEID 
                            
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.BANK_RECONCILEID DESC ");

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

    public function add(){  

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $objlastdt  =   $this->getLastdt();
        $bankMaster =   $this->bankMaster();
		

        $objSON = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('FYID_REF','=',$FYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_DOCNO_DEFINITION.*')
        ->first();

        $objDataNo   =   NULL;

        if( isset($objSON->SYSTEM_GRSR) && $objSON->SYSTEM_GRSR == "1")
        {
            if($objSON->PREFIX_RQ == "1")
            {
                $objDataNo = $objSON->PREFIX;
            }        
            if($objSON->PRE_SEP_RQ == "1")
            {
                if($objSON->PRE_SEP_SLASH == "1")
                {
                $objDataNo = $objDataNo.'/';
                }
                if($objSON->PRE_SEP_HYPEN == "1")
                {
                $objDataNo = $objDataNo.'-';
                }
            }        
            if($objSON->NO_MAX)
            {   
                $objDataNo = $objDataNo.str_pad($objSON->LAST_RECORDNO+1, $objSON->NO_MAX, "0", STR_PAD_LEFT);
            }
            
            if($objSON->NO_SEP_RQ == "1")
            {
                if($objSON->NO_SEP_SLASH == "1")
                {
                $objDataNo = $objDataNo.'/';
                }
                if($objSON->NO_SEP_HYPEN == "1")
                {
                $objDataNo = $objDataNo.'-';
                }
            }
            if($objSON->SUFFIX_RQ == "1")
            {
                $objDataNo = $objDataNo.$objSON->SUFFIX;
            }
        }
   
        $FormId     =   $this->form_id;
		
		
        
        return view($this->view.$FormId.'add',compact(['FormId','objSON','objDataNo','objlastdt','bankMaster']));       
    }

    public function save(Request $request) {

        $req_data=array();
        if(isset($_REQUEST['BANK_DATE'])){
            foreach($_REQUEST['BANK_DATE'] as $key=>$value){
                if($value !=""){
                    $req_data[] = [
                        'PAYMENT_RECEIPT_ID_REF'    =>  $request['PAYMENT_RECEIPT_ID_REF'][$key],
                        'CUSTOMER_VENDOR_ID_REF'    =>  $request['CUSTOMER_VENDOR_ID_REF'][$key],
                        'VOUCHER_TYPE'              =>  $request['VOUCHER_TYPE'][$key],
                        'BANK_DATE'                 =>  $request['BANK_DATE'][$key],
                        'DEBIT_AMOUNT'              =>  $request['DEBIT_AMOUNT'][$key],
                        'CREDIT_AMOUNT'             =>  $request['CREDIT_AMOUNT'][$key],
                    ];
                }
            }
        }
        
        if(!empty($req_data)){
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $XMLMAT = NULL;
        }    
        
        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $BANK_RECONCILE_CODE    =   $request['DOC_NO'];
        $BANK_RECONCILE_DATE    =   $request['DOC_DT'];
        $CASH_BANK_ID           =   $request['BID_REF'];
        $BANK_RECONCILE_MODE    =   $request['MODE'];
      
        $log_data = [ 
            $BANK_RECONCILE_CODE,$BANK_RECONCILE_DATE,$CASH_BANK_ID,$BANK_RECONCILE_MODE,$XMLMAT,
            $CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$USERID, 
            Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];  

        $sp_result  =   DB::select('EXEC SP_BANK_RECONCILE_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $log_data);  
        $contains   =   Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
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

            $objResponse   =   DB::select("SELECT
            T1.*,CONCAT(T2.NAME,'-',T2.ACNO) AS BANK_NAME,T2.GLID_REF 
            FROM TBL_TRN_BANK_RECONCILATION T1
            LEFT JOIN TBL_MST_BANK T2 ON T1.CASH_BANK_ID=T2.BID
            WHERE BANK_RECONCILEID='$id'")[0];

            $objlastdt  =   $this->getLastdt();
            $bankMaster =   $this->bankMaster();
			
            $FormId         =   $this->form_id;
            $ActionStatus   =   "";

            return view($this->view.$FormId.'edit',compact(['FormId','objRights','objResponse','objlastdt','bankMaster','ActionStatus']));      


        }
     
    }

    public function update(Request $request){

        $req_data=array();
        if(isset($_REQUEST['BANK_DATE'])){
            foreach($_REQUEST['BANK_DATE'] as $key=>$value){
                if($value !=""){
                    $req_data[] = [
                        'PAYMENT_RECEIPT_ID_REF'    =>  $request['PAYMENT_RECEIPT_ID_REF'][$key],
                        'CUSTOMER_VENDOR_ID_REF'    =>  $request['CUSTOMER_VENDOR_ID_REF'][$key],
                        'VOUCHER_TYPE'              =>  $request['VOUCHER_TYPE'][$key],
                        'BANK_DATE'                 =>  $request['BANK_DATE'][$key],
                        'DEBIT_AMOUNT'              =>  $request['DEBIT_AMOUNT'][$key],
                        'CREDIT_AMOUNT'             =>  $request['CREDIT_AMOUNT'][$key],
                    ];
                }
            }
        }

        if(!empty($req_data)){
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $XMLMAT = NULL;
        }
        
        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $BANK_RECONCILEID       =   $request['BANK_RECONCILEID'];
        $BANK_RECONCILE_CODE    =   $request['DOC_NO'];
        $BANK_RECONCILE_DATE    =   $request['DOC_DT'];
        $CASH_BANK_ID           =   $request['BID_REF'];
        $BANK_RECONCILE_MODE    =   $request['MODE'];
      
        $log_data = [ 
            $BANK_RECONCILEID,$BANK_RECONCILE_CODE,$BANK_RECONCILE_DATE,$CASH_BANK_ID,$BANK_RECONCILE_MODE,
            $XMLMAT,$CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,
            $USERID,Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];  

        $sp_result  =   DB::select('EXEC SP_BANK_RECONCILE_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $log_data);  
        $contains   =   Str::contains($sp_result[0]->RESULT, 'SUCCESS');
        
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);
        }else{
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

            $objResponse   =   DB::select("SELECT
            T1.*,CONCAT(T2.NAME,'-',T2.ACNO) AS BANK_NAME,T2.GLID_REF 
            FROM TBL_TRN_BANK_RECONCILATION T1
            LEFT JOIN TBL_MST_BANK T2 ON T1.CASH_BANK_ID=T2.BID
            WHERE BANK_RECONCILEID='$id'")[0];

            $objlastdt  =   $this->getLastdt();
            $bankMaster =   $this->bankMaster();
			

            $FormId         =   $this->form_id;
            $ActionStatus   =   "disabled";

            return view($this->view.$FormId.'view',compact(['FormId','objRights','objResponse','objlastdt','bankMaster','ActionStatus']));      


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

        if(!empty($sp_listing_result)){
            foreach ($sp_listing_result as $key=>$valueitem){  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$valueitem->LAVELS;
            }
        }
   
        $req_data=array();
        if(isset($_REQUEST['BANK_DATE'])){
            foreach($_REQUEST['BANK_DATE'] as $key=>$value){
                if($value !=""){
                    $req_data[] = [
                        'PAYMENT_RECEIPT_ID_REF'    =>  $request['PAYMENT_RECEIPT_ID_REF'][$key],
                        'CUSTOMER_VENDOR_ID_REF'    =>  $request['CUSTOMER_VENDOR_ID_REF'][$key],
                        'VOUCHER_TYPE'              =>  $request['VOUCHER_TYPE'][$key],
                        'BANK_DATE'                 =>  $request['BANK_DATE'][$key],
                        'DEBIT_AMOUNT'              =>  $request['DEBIT_AMOUNT'][$key],
                        'CREDIT_AMOUNT'             =>  $request['CREDIT_AMOUNT'][$key],
                    ];
                }
            }
        }

        if(!empty($req_data)){
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $XMLMAT = NULL;
        }
            
        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $BANK_RECONCILEID       =   $request['BANK_RECONCILEID'];
        $BANK_RECONCILE_CODE    =   $request['DOC_NO'];
        $BANK_RECONCILE_DATE    =   $request['DOC_DT'];
        $CASH_BANK_ID           =   $request['BID_REF'];
        $BANK_RECONCILE_MODE    =   $request['MODE'];
      
        $log_data = [ 
            $BANK_RECONCILEID,$BANK_RECONCILE_CODE,$BANK_RECONCILE_DATE,$CASH_BANK_ID,$BANK_RECONCILE_MODE,
            $XMLMAT,$CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,
            $USERID,Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ]; 
        
        $sp_result  =   DB::select('EXEC SP_BANK_RECONCILE_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $log_data);  
        $contains   =   Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
    }

    public function MultiApprove(Request $request){

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

        if(!empty($sp_listing_result)){
            foreach ($sp_listing_result as $key=>$valueitem){  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$valueitem->LAVELS;
            }
        }
               
        $req_data =  json_decode($request['ID']);

        $wrapped_links = $req_data; 
        $multi_array = $wrapped_links;
        $iddata = [];
        
        foreach($multi_array as $index=>$row){
            $m_array[$index] = $row->ID;
            $iddata['APPROVAL'][]['ID'] =  $row->ID;
        }

        $xml = ArrayToXml::convert($iddata);
                
        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_TRN_BANK_RECONCILATION";
        $FIELD      =   "BANK_RECONCILEID";
        $ACTIONNAME     = $Approvallevel;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
            
        $log_data = [ 
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
        
        if($sp_result[0]->RESULT=="All records approved"){

        return Response::json(['approve' =>true,'msg' => 'Record successfully Approved.']);

        }elseif($sp_result[0]->RESULT=="NO RECORD FOR APPROVAL"){
        
        return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','salesenquiry'=>'norecord']);
        
        }else{
        return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','salesenquiry'=>'Some Error']);
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
        $TABLE      =   "TBL_TRN_BANK_RECONCILATION";
        $FIELD      =   "BANK_RECONCILEID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
         'NT'  => 'TBL_TRN_BANK_RECONCILATION',
        ];
        $req_data[1]=[
        'NT'  => 'TBL_TRN_BANK_RECONCILATION_DETAIL',
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

        if(!is_null($id)){
        
            $FormId     =   $this->form_id;

            $objResponse = DB::table('TBL_TRN_BANK_RECONCILATION')->where('BANK_RECONCILEID','=',$id)->first();

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
        
		$image_path         =   "docs/company".$CYID_REF."/BankReconcilation";     
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

                   

                    $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".$filenamewithextension;  

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
   

    public function codeduplicate(Request $request){

        $BANK_RECONCILE_CODE  =   trim($request['DOC_NO']);
        $objLabel = DB::table('TBL_TRN_BANK_RECONCILATION')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('BANK_RECONCILE_CODE','=',$BANK_RECONCILE_CODE)
        ->select('BANK_RECONCILE_CODE')->first();

        if($objLabel){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }

    public function getLastdt(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        return  DB::select('SELECT MAX(BANK_RECONCILE_DATE) BANK_RECONCILE_DATE FROM TBL_TRN_BANK_RECONCILATION  
        WHERE  CYID_REF = ? AND BRID_REF = ?    AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  'A' ]);
    }

    public function getDataArray(Request $request){
		//dd($request->all());
        $CYID_REF           =   Auth::user()->CYID_REF;
        $BRID_REF           =   Session::get('BRID_REF');
        $FYID_REF           =   Session::get('FYID_REF');
        $MODE               =   $request['MODE'] =="ALL"?'1':'0';
        $BID_REF            =   $request['BID_REF'];
        $GLID_REF           =   $request['GLID_REF'];
        $BANK_RECONCILEID   =   $request['BANK_RECONCILEID'];
        $ACTION_TYPE        =   $request['ACTION_TYPE'] =='VIEW'?'disabled':'';
        $DATE               =   $request['DOC_DT'];
        $WHERE_MODE         =   $MODE=='0' && $request['ACTION_TYPE'] !='VIEW'?"AND C.RECONCILE='$MODE'":"";
		//DD($DATE);
        $material_data      =   [];

        if($BANK_RECONCILEID !=""){
            $material       =   DB::select("SELECT * FROM TBL_TRN_BANK_RECONCILATION_DETAIL WHERE BANK_RECONCILEID_REF='$BANK_RECONCILEID'");
            foreach($material as $key=>$value){
                $key_data                   =   $value->PAYMENT_RECEIPT_ID_REF.'_'.$value->VOUCHER_TYPE;
                $material_data[$key_data]   =   $value->BANK_DATE;
            }
        }
		
		 if($BANK_RECONCILEID !="")
		 {
				$payment    =   DB::select("SELECT  C.PAYMENTID AS  PAYMENT_RECEIPT_ID_REF,C.PAYMENT_DT AS DOC_DATE,C.PAYMENT_NO AS DOC_NUM,
				C.PAYMENT_FOR AS PARTICULAR_TYPE,C.CUSTMER_VENDOR_ID AS PARTICULAR_ID, B.SOURCE_DOCTYPE  AS VOUCHER_TYPE,C.INSTRUMENT_TYPE AS TRANSACTION_TYPE,
				C.INSTRUMENT_NO,C.TRANSACTION_DT AS INSTRUMENT_DATE,A.DR_AMT AS DEBIT_AMOUNT,A.CR_AMT AS CREDIT_AMOUNT,C.RECONCILE
				FROM TBL_TRN_FJRV01_ACC A LEFT JOIN TBL_TRN_FJRV01_HDR B ON A.JVID_REF=B.JVID
				LEFT JOIN TBL_TRN_PAYMENT_HDR C ON B.SOURCE_DOCNO = C.PAYMENT_NO AND B.SOURCE_DOCTYPE = 'PAYMENT' AND B.CYID_REF = C.CYID_REF 
				AND B.BRID_REF = C.BRID_REF AND B.FYID_REF = C.FYID_REF INNER JOIN TBL_MST_BANK D (NOLOCK) ON A.GLID_REF = D.GLID_REF
				where B.CYID_REF = '$CYID_REF' AND B.BRID_REF = '$BRID_REF' AND B.FYID_REF='$FYID_REF' AND D.BID ='$BID_REF' AND C.STATUS = 'A'
				AND B.JV_DT <= '$DATE' AND A.GLID_REF IN (SELECT GLID_REF FROM  TBL_MST_BANK WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND BID = '$BID_REF')
				AND C.PAYMENTID IN (SELECT PAYMENT_RECEIPT_ID_REF FROM TBL_TRN_BANK_RECONCILATION_DETAIL WHERE VOUCHER_TYPE = 'PAYMENT' 
				AND  BANK_RECONCILEID_REF IN (SELECT BANK_RECONCILEID FROM TBL_TRN_BANK_RECONCILATION WHERE  BANK_RECONCILEID_REF = $BANK_RECONCILEID))");

				$receipt    =   DB::select("SELECT  C.RECEIPTID AS  PAYMENT_RECEIPT_ID_REF,C.RECEIPT_DT AS DOC_DATE,C.RECEIPT_NO AS DOC_NUM,
				C.RECEIPT_FOR AS PARTICULAR_TYPE,C.CUSTMER_VENDOR_ID AS PARTICULAR_ID, B.SOURCE_DOCTYPE  AS VOUCHER_TYPE,C.INSTRUMENT_TYPE AS TRANSACTION_TYPE,
				C.INSTRUMENT_NO,C.TRANSACTION_DT AS INSTRUMENT_DATE,A.DR_AMT AS DEBIT_AMOUNT, A.CR_AMT AS CREDIT_AMOUNT,C.RECONCILE
				FROM TBL_TRN_FJRV01_ACC A LEFT JOIN TBL_TRN_FJRV01_HDR B ON A.JVID_REF=B.JVID
				LEFT JOIN TBL_TRN_RECEIPT_HDR C ON B.SOURCE_DOCNO = C.RECEIPT_NO AND B.SOURCE_DOCTYPE = 'RECEIPT' AND B.CYID_REF = C.CYID_REF 
				AND B.BRID_REF = C.BRID_REF AND B.FYID_REF = C.FYID_REF INNER JOIN TBL_MST_BANK D (NOLOCK) ON A.GLID_REF = D.GLID_REF
				where B.CYID_REF = '$CYID_REF' AND B.BRID_REF = '$BRID_REF' AND B.FYID_REF='$FYID_REF' AND D.BID ='$BID_REF' AND C.STATUS = 'A' 
				AND B.JV_DT <= '$DATE' AND A.GLID_REF IN (SELECT GLID_REF FROM  TBL_MST_BANK WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND BID = '$BID_REF')
				AND C.RECEIPTID IN (SELECT PAYMENT_RECEIPT_ID_REF FROM TBL_TRN_BANK_RECONCILATION_DETAIL WHERE VOUCHER_TYPE = 'RECEIPT' 
				AND  BANK_RECONCILEID_REF IN (SELECT BANK_RECONCILEID FROM TBL_TRN_BANK_RECONCILATION WHERE  BANK_RECONCILEID_REF = $BANK_RECONCILEID))");
				
				$MJV    =   DB::select("SELECT H.MJVID AS PAYMENT_RECEIPT_ID_REF,H.MJV_DT AS DOC_DATE, H.MJV_NO AS DOC_NUM, NULL AS PARTICULAR_TYPE,
					NULL AS PARTICULAR_ID,'Manual JV' AS VOUCHER_TYPE,NULL AS TRANSACTION_TYPE,NULL AS INSTRUMENT_NO,
					NULL AS INSTRUMENT_DATE,SUM(A.DR_AMT) AS DEBIT_AMOUNT, SUM(A.CR_AMT) AS CREDIT_AMOUNT, 0 AS RECONCILE
					FROM TBL_TRN_MJRV01_ACC A LEFT JOIN TBL_TRN_MJRV01_HDR H ON A.MJVID_REF=H.MJVID                   
					WHERE A.SGLID_REF='G' AND H.STATUS = 'A' AND H.MJV_DT <= '$DATE'     
					AND A.GLID_REF IN (SELECT GLID_REF FROM  TBL_MST_BANK WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND BID = '$BID_REF')
					AND H.MJVID IN (SELECT PAYMENT_RECEIPT_ID_REF FROM TBL_TRN_BANK_RECONCILATION_DETAIL WHERE VOUCHER_TYPE = 'Manual JV' 
					AND  BANK_RECONCILEID_REF IN (SELECT BANK_RECONCILEID FROM TBL_TRN_BANK_RECONCILATION WHERE  BANK_RECONCILEID_REF = $BANK_RECONCILEID)) 
					GROUP BY H.MJVID,H.MJV_DT,H.MJV_NO");
		}
		else
		{
				$payment    =   DB::select("SELECT  C.PAYMENTID AS  PAYMENT_RECEIPT_ID_REF,C.PAYMENT_DT AS DOC_DATE,C.PAYMENT_NO AS DOC_NUM,
				C.PAYMENT_FOR AS PARTICULAR_TYPE,C.CUSTMER_VENDOR_ID AS PARTICULAR_ID, B.SOURCE_DOCTYPE  AS VOUCHER_TYPE,C.INSTRUMENT_TYPE AS TRANSACTION_TYPE,
				C.INSTRUMENT_NO,C.TRANSACTION_DT AS INSTRUMENT_DATE,A.DR_AMT AS DEBIT_AMOUNT,A.CR_AMT AS CREDIT_AMOUNT,C.RECONCILE
				FROM TBL_TRN_FJRV01_ACC A LEFT JOIN TBL_TRN_FJRV01_HDR B ON A.JVID_REF=B.JVID
				LEFT JOIN TBL_TRN_PAYMENT_HDR C ON B.SOURCE_DOCNO = C.PAYMENT_NO AND B.SOURCE_DOCTYPE = 'PAYMENT' AND B.CYID_REF = C.CYID_REF 
				AND B.BRID_REF = C.BRID_REF AND B.FYID_REF = C.FYID_REF INNER JOIN TBL_MST_BANK D (NOLOCK) ON A.GLID_REF = D.GLID_REF
				where B.CYID_REF = '$CYID_REF' AND B.BRID_REF = '$BRID_REF' AND B.FYID_REF='$FYID_REF' AND D.BID ='$BID_REF' AND C.STATUS = 'A'
				AND B.JV_DT <= '$DATE' AND A.GLID_REF IN (SELECT GLID_REF FROM  TBL_MST_BANK WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND BID = '$BID_REF')
				AND C.PAYMENTID NOT IN (SELECT PAYMENT_RECEIPT_ID_REF FROM TBL_TRN_BANK_RECONCILATION_DETAIL WHERE VOUCHER_TYPE = 'PAYMENT' 
				AND  BANK_RECONCILEID_REF IN (SELECT BANK_RECONCILEID FROM TBL_TRN_BANK_RECONCILATION WHERE STATUS = 'A' AND CASH_BANK_ID = '$BID_REF'))");

				$receipt    =   DB::select("SELECT  C.RECEIPTID AS  PAYMENT_RECEIPT_ID_REF,C.RECEIPT_DT AS DOC_DATE,C.RECEIPT_NO AS DOC_NUM,
				C.RECEIPT_FOR AS PARTICULAR_TYPE,C.CUSTMER_VENDOR_ID AS PARTICULAR_ID, B.SOURCE_DOCTYPE  AS VOUCHER_TYPE,C.INSTRUMENT_TYPE AS TRANSACTION_TYPE,
				C.INSTRUMENT_NO,C.TRANSACTION_DT AS INSTRUMENT_DATE,A.DR_AMT AS DEBIT_AMOUNT, A.CR_AMT AS CREDIT_AMOUNT,C.RECONCILE
				FROM TBL_TRN_FJRV01_ACC A LEFT JOIN TBL_TRN_FJRV01_HDR B ON A.JVID_REF=B.JVID
				LEFT JOIN TBL_TRN_RECEIPT_HDR C ON B.SOURCE_DOCNO = C.RECEIPT_NO AND B.SOURCE_DOCTYPE = 'RECEIPT' AND B.CYID_REF = C.CYID_REF 
				AND B.BRID_REF = C.BRID_REF AND B.FYID_REF = C.FYID_REF INNER JOIN TBL_MST_BANK D (NOLOCK) ON A.GLID_REF = D.GLID_REF
				where B.CYID_REF = '$CYID_REF' AND B.BRID_REF = '$BRID_REF' AND B.FYID_REF='$FYID_REF' AND D.BID ='$BID_REF' AND C.STATUS = 'A' 
				AND B.JV_DT <= '$DATE' AND A.GLID_REF IN (SELECT GLID_REF FROM  TBL_MST_BANK WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND BID = '$BID_REF')
				AND C.RECEIPTID NOT IN (SELECT PAYMENT_RECEIPT_ID_REF FROM TBL_TRN_BANK_RECONCILATION_DETAIL WHERE VOUCHER_TYPE = 'RECEIPT' 
				AND  BANK_RECONCILEID_REF IN (SELECT BANK_RECONCILEID FROM TBL_TRN_BANK_RECONCILATION WHERE STATUS = 'A' AND CASH_BANK_ID = '$BID_REF'))");
				
				$MJV    =   DB::select("SELECT H.MJVID AS PAYMENT_RECEIPT_ID_REF,H.MJV_DT AS DOC_DATE, H.MJV_NO AS DOC_NUM, NULL AS PARTICULAR_TYPE,
				NULL AS PARTICULAR_ID,'Manual JV' AS VOUCHER_TYPE,NULL AS TRANSACTION_TYPE,NULL AS INSTRUMENT_NO,
				NULL AS INSTRUMENT_DATE,SUM(A.DR_AMT) AS DEBIT_AMOUNT, SUM(A.CR_AMT) AS CREDIT_AMOUNT, 0 AS RECONCILE
				FROM TBL_TRN_MJRV01_ACC A LEFT JOIN TBL_TRN_MJRV01_HDR H ON A.MJVID_REF=H.MJVID                   
				WHERE A.SGLID_REF='G' AND H.STATUS = 'A' AND H.MJV_DT <= '$DATE'     
				AND A.GLID_REF IN (SELECT GLID_REF FROM  TBL_MST_BANK WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND BID = '$BID_REF')
				AND H.MJVID NOT IN (SELECT PAYMENT_RECEIPT_ID_REF FROM TBL_TRN_BANK_RECONCILATION_DETAIL WHERE VOUCHER_TYPE = 'Manual JV' 
				AND  BANK_RECONCILEID_REF IN (SELECT BANK_RECONCILEID FROM TBL_TRN_BANK_RECONCILATION WHERE STATUS = 'A' AND CASH_BANK_ID = '$BID_REF')) 
				GROUP BY H.MJVID,H.MJV_DT,H.MJV_NO");
			
		}

        $data   =   array_merge($payment,$receipt,$MJV);
		$DEBITOPENING = $this->DEBITOPENING($GLID_REF);
		$CREDITOPENING = $this->CREDITOPENING($GLID_REF);

        $row    =   '';
        if(!empty($data)){

            $TOTAL_DR   =   0;
            $TOTAL_CR   =   0;
            foreach($data as $key=>$value){
				
               
                $text_disabled  =   $value->RECONCILE =="1"?'':'';
                $key_data       =   $value->PAYMENT_RECEIPT_ID_REF.'_'.$value->VOUCHER_TYPE;
                $BANK_DATE      =   array_key_exists($key_data,$material_data)?$material_data[$key_data]:'';

                $PARTICULAR     =   $this->getParticular($value->PARTICULAR_TYPE,$value->PARTICULAR_ID);
                $DEBIT_AMOUNT   =   floatval($value->DEBIT_AMOUNT);
                $CREDIT_AMOUNT  =   floatval($value->CREDIT_AMOUNT);

                $TOTAL_DR       =    $TOTAL_DR+$DEBIT_AMOUNT;
                $TOTAL_CR       =    $TOTAL_CR+$CREDIT_AMOUNT;


                if($MODE =='1'){
                    $BANK_DATE_ARR  =   DB::select("SELECT TOP 1 BANK_DATE FROM TBL_TRN_BANK_RECONCILATION_DETAIL 
					WHERE PAYMENT_RECEIPT_ID_REF='$value->PAYMENT_RECEIPT_ID_REF' AND VOUCHER_TYPE='$value->VOUCHER_TYPE'");
					//dd($BANK_DATE_ARR);
                    if(!empty($BANK_DATE_ARR) && $BANK_DATE_ARR[0]->BANK_DATE !=""){
                        $BANK_DATE  =   $BANK_DATE_ARR[0]->BANK_DATE;
                    }
                    else{
                        $BANK_DATE='';
                    } 
                }


                

                    $row    .='
                    <tr class="participantRow">
                        <td hidden><input type="text" name="PAYMENT_RECEIPT_ID_REF[]" value="'.$value->PAYMENT_RECEIPT_ID_REF.'" class="form-control"  autocomplete="off" style="width:130px;" readonly '.$text_disabled.' '.$ACTION_TYPE.' /></td>
                        <td hidden><input type="text" name="CUSTOMER_VENDOR_ID_REF[]" value="'.$value->PARTICULAR_ID.'" class="form-control"  autocomplete="off" style="width:130px;" readonly '.$text_disabled.' '.$ACTION_TYPE.' /></td>
                        <td><input type="text" name="DOC_DATE[]" id="DOC_DATE_'.$key.'" value="'.$value->DOC_DATE.'" class="form-control"  autocomplete="off" style="width:130px;" readonly '.$text_disabled.' '.$ACTION_TYPE.' /></td>
                        <td><input type="text" name="DOC_NUM[]" id="DOC_NUM_'.$key.'" value="'.$value->DOC_NUM.'" class="form-control"  autocomplete="off" style="width:130px;" readonly '.$text_disabled.' '.$ACTION_TYPE.' /></td>
                        <td><input type="text" name="PARTICULAR[]" id="PARTICULAR_'.$key.'" value="'.$PARTICULAR.'" class="form-control"  autocomplete="off" style="width:130px;" readonly '.$text_disabled.' '.$ACTION_TYPE.' /></td>
                        <td><input type="text" name="VOUCHER_TYPE[]" id="VOUCHER_TYPE_'.$key.'" value="'.$value->VOUCHER_TYPE.'" class="form-control"  autocomplete="off" style="width:130px;" readonly '.$text_disabled.' '.$ACTION_TYPE.' /></td>
                        <td><input type="text" name="TRANSACTION_TYPE[]" id="TRANSACTION_TYPE_'.$key.'" value="'.$value->TRANSACTION_TYPE.'" class="form-control"  autocomplete="off" style="width:130px;" readonly '.$text_disabled.' '.$ACTION_TYPE.' /></td>
                        <td><input type="text" name="INSTRUMENT_NO[]" id="INSTRUMENT_NO_'.$key.'" value="'.$value->INSTRUMENT_NO.'" class="form-control"  autocomplete="off" style="width:130px;" readonly '.$text_disabled.' '.$ACTION_TYPE.' /></td>
                        <td><input type="text" name="INSTRUMENT_DATE[]" id="INSTRUMENT_DATE_'.$key.'" value="'.$value->INSTRUMENT_DATE.'" class="form-control"  autocomplete="off" style="width:130px;" readonly '.$text_disabled.' '.$ACTION_TYPE.' /></td>
                        <td><input type="date" name="BANK_DATE[]" id="BANK_DATE_'.$key.'" value="'.$BANK_DATE.'" class="form-control"  autocomplete="off" style="width:130px;" onchange="balanceCalculation()" '.$text_disabled.' '.$ACTION_TYPE.'  /></td>
                        <td><input type="text" name="DEBIT_AMOUNT[]" id="DEBIT_AMOUNT_'.$key.'" value="'.number_format($DEBIT_AMOUNT, 2, '.', '').'" class="form-control"  autocomplete="off" style="width:130px;text-align:right;" readonly '.$text_disabled.' '.$ACTION_TYPE.' /></td>
                        <td><input type="text" name="CREDIT_AMOUNT[]" id="CREDIT_AMOUNT_'.$key.'" value="'.number_format($CREDIT_AMOUNT, 2, '.', '').'" class="form-control"  autocomplete="off" style="width:130px;text-align:right;" readonly '.$text_disabled.' '.$ACTION_TYPE.' /></td>
                    </tr>
                    ';

                    if($request['ACTION_TYPE'] =='VIEW' && !array_key_exists($key_data,$material_data)){
                        $row    .='';
                    }
            }
			$DEBITOPENING1 = 0;
			$CREDITOPENING1 = 0;
			
			$MAXDATE = DB::select("SELECT MAX(A.BANK_RECONCILE_DATE) BANK_RECONCILE_DATE FROM TBL_TRN_BANK_RECONCILATION A  INNER JOIN TBL_TRN_BANK_RECONCILATION_DETAIL B ON A.BANK_RECONCILEID = B.BANK_RECONCILEID_REF 
			WHERE A.CASH_BANK_ID = '$BID_REF' AND A.CYID_REF = '$CYID_REF' AND (B.BANK_DATE != '' OR B.BANK_DATE IS NOT NULL) AND B.BANK_DATE < '$DATE'")[0]->BANK_RECONCILE_DATE;

            $GLODBL     =   DB::select("SELECT DBO.FN_GLODBL1('$GLID_REF','$DATE') AS GLODBL")[0]->GLODBL;
            $GLOCBL     =   DB::select("SELECT DBO.FN_GLOCBL1('$GLID_REF','$DATE') AS GLOCBL")[0]->GLOCBL;
			$DEBITOPENING = DB::select("SELECT DBO.FN_GLODBL2('$GLID_REF','$DATE') AS GLODBL")[0]->GLODBL;
			$CREDITOPENING = DB::select("SELECT DBO.FN_GLOCBL2('$GLID_REF','$DATE') AS GLOCBL")[0]->GLOCBL;
			$DEBITOPENING1 = DB::select("SELECT SUM(B.DEBIT_AMOUNT) AS DEBIT_AMOUNT FROM TBL_TRN_BANK_RECONCILATION A 
			INNER JOIN TBL_TRN_BANK_RECONCILATION_DETAIL B ON A.BANK_RECONCILEID = B.BANK_RECONCILEID_REF
			WHERE  A.CASH_BANK_ID = '$BID_REF' AND A.CYID_REF = '$CYID_REF' AND (B.BANK_DATE != '' OR B.BANK_DATE IS NOT NULL)
			AND A.BRID_REF = '$BRID_REF' AND A.FYID_REF = '$FYID_REF' AND B.BANK_DATE <= '$MAXDATE'")[0]->DEBIT_AMOUNT;
			$CREDITOPENING1 = DB::select("SELECT SUM(B.CREDIT_AMOUNT) AS CREDIT_AMOUNT FROM TBL_TRN_BANK_RECONCILATION A 
			INNER JOIN TBL_TRN_BANK_RECONCILATION_DETAIL B ON A.BANK_RECONCILEID = B.BANK_RECONCILEID_REF
			WHERE  A.CASH_BANK_ID = '$BID_REF' AND A.CYID_REF = '$CYID_REF' AND (B.BANK_DATE != '' OR B.BANK_DATE IS NOT NULL)
			AND A.BRID_REF = '$BRID_REF' AND A.FYID_REF = '$FYID_REF' AND B.BANK_DATE <= '$MAXDATE'")[0]->CREDIT_AMOUNT;
			
            $TOTAL_DR   =   floatval($DEBITOPENING1)+floatval($GLODBL);
            $TOTAL_CR   =   floatval($CREDITOPENING1)+floatval($GLOCBL);
            $TOTAL_DF   =   floatval($CREDITOPENING) - floatval($DEBITOPENING);
			$TOTAL_DF2   =  $TOTAL_CR - $TOTAL_DR;
			$TOTAL_DF3   =  floatval($GLOCBL) - floatval($GLODBL);
			//DD($TOTAL_DF2);
            $row    .='<tr hidden><td colspan="9"></td><td><input type="text" id="TOTAL_DF" value="'.$TOTAL_DF.'" >
						<td colspan="9"></td><td><input type="text" id="TOTAL_DF2" value="'.$TOTAL_DF2.'" >
						<td colspan="9"></td><td><input type="text" id="TOTAL_DF3" value="'.$TOTAL_DF3.'" >
						<input type="text" id="DEBIT_OPENING" value="'.$DEBITOPENING1.'" >
						<input type="text" id="CREDIT_OPENING" value="'.$CREDITOPENING1.'" ></td></tr>';
                  
        }
        else{
            $row    .='<tr hidden><td colspan="9"></td><td><input type="text" id="TOTAL_DF" value="0.00" >
						<input type="text" id="DEBIT_OPENING" value="0.00" >
						<input type="text" id="CREDIT_OPENING" value="0.00" ></td></tr>';
            $row    .=' <tr class="participantRow"><td colspan="10" style="text-align:center;" >No data available in table</td></tr>';
        }
       //dd($row);
        echo $row;

        exit();
    }

    public function getParticular($PARTICULAR_TYPE,$PARTICULAR_ID){

        if($PARTICULAR_TYPE =="Vendor" && $PARTICULAR_ID !=""){
            $data   =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$PARTICULAR_ID)->select('NAME')->first();
        }
        else if($PARTICULAR_TYPE =="Customer" && $PARTICULAR_ID !=""){
            $data   =   DB::table('TBL_MST_CUSTOMER')->where('SLID_REF','=',$PARTICULAR_ID)->select('NAME')->first();
        }
        else{
            $data    =  [];
        }

        return !empty($data)?$data->NAME:'';
  
    }

    public function bankMaster(){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $bank   =   DB::select("SELECT * 
        FROM TBL_MST_BANK
        WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF'  AND (DEACTIVATED IS NULL OR DEACTIVATED='0')");

        return $bank;
    }
	
	public function DEBITOPENING($GLID_REF){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $GLDRBALANCE   =   DB::select("SELECT GLDRBALANCE 
        FROM TBL_MST_GLOPENING_LEDGER
        WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF = '$FYID_REF' AND GLID_REF = '$GLID_REF'  ");

        return $GLDRBALANCE;
    }
	
	public function CREDITOPENING($GLID_REF){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $GLCRBALANCE   =   DB::select("SELECT GLCRBALANCE 
        FROM TBL_MST_GLOPENING_LEDGER
        WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF = '$FYID_REF' AND GLID_REF = '$GLID_REF'  ");

        return $GLCRBALANCE;
    }
}
