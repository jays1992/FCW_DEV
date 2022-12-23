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

class TrnFrm501Controller extends Controller{
    protected $form_id  =   501;
    protected $vtid_ref =   571;
    protected $view     =   "transactions.Accounts.FixedDeposit.trnfrm501";

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
        hdr.FDID,hdr.FD_CODE,hdr.FD_DATE,hdr.INDATE,hdr.STATUS,
        (
        SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
        LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
        WHERE  AUD.VID=hdr.FDID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
        inner join TBL_TRN_FIXED_DEPOSIT hdr
        on a.VID = hdr.FDID 
        
        and a.CYID_REF = hdr.CYID_REF 
        and a.BRID_REF = hdr.BRID_REF
        where a.VTID_REF = '$this->vtid_ref'
        and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
        and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
        ORDER BY hdr.FDID DESC ");
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

    public function getAccountMaster(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        if($request['ACTYPE'] ==='FD'){
            $data   =   DB::select("SELECT * 
            FROM TBL_MST_BANK 
            WHERE CYID_REF='$CYID_REF' AND STATUS='A' AND ACTYPE ='FD' AND (DEACTIVATED=0 OR DEACTIVATED IS NULL)"); 
        }
        else{
            $data   =   DB::select("SELECT * 
            FROM TBL_MST_BANK 
            WHERE CYID_REF='$CYID_REF' AND STATUS='A' AND ACTYPE !='FD' AND (DEACTIVATED=0 OR DEACTIVATED IS NULL)"); 
        }

        return Response::json($data);
    }

    public function add(){

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_FIXED_DEPOSIT',
            'HDR_ID'=>'FDID',
            'HDR_DOC_NO'=>'FD_CODE',
            'HDR_DOC_DT'=>'FD_DATE'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req); 
        
        $lastDocDate    =   $this->LastApprovedDocDate();    
        $FormId         =   $this->form_id;

        return view($this->view.'add', compact(['FormId','lastDocDate','doc_req','docarray']));       
    }

    public function save(Request $request){

        $VTID_REF       =   $this->vtid_ref;
        $USERID_REF     =   Auth::user()->USERID;   
        $ACTIONNAME     =   'ADD';
        $IPADDRESS      =   $request->getClientIp();
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $FD_CODE                =   $request['FD_CODE'];
        $FD_DATE                =   $request['FD_DATE'];
        $BANK_AC                =   $request['BANK_AC'];
        $BANK_AC_NO             =   $request['BANK_AC_NO'];
        $FD_BANK_AC             =   $request['FD_BANK_AC'];
        $FD_BANK_AC_NO          =   $request['FD_BANK_AC_NO'];
        $MATURITY_DATE          =   $request['MATURITY_DATE'];
        $IN_FAVOUR              =   $request['IN_FAVOUR'];
        $BG_NO_APPLICABLE       =   $request['BG_NO_APPLICABLE'];
        $BG_NO                  =   $request['BG_NO'];
        $PRINCIPLE_AMOUNT       =   $request['PRINCIPLE_AMOUNT'];
        $RATE_OF_INTEREST_BASE  =   $request['RATE_OF_INTEREST_BASE'];
        $RATE_OF_INTEREST       =   $request['RATE_OF_INTEREST'];
        $REMARKS                =   $request['REMARKS'];
       
        $log_data = [
            $FD_CODE,$FD_DATE,$BANK_AC,$BANK_AC_NO,$FD_BANK_AC,
            $FD_BANK_AC_NO,$MATURITY_DATE,$IN_FAVOUR,$BG_NO_APPLICABLE,$BG_NO,
            $PRINCIPLE_AMOUNT,$RATE_OF_INTEREST_BASE,$RATE_OF_INTEREST,$REMARKS,$CYID_REF,  
            $BRID_REF,$FYID_REF,$VTID_REF,$USERID_REF,Date('Y-m-d'), 
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];

        $sp_result  =   DB::select('EXEC SP_FIXED_DEPOSIT_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  
        
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

            $lastDocDate    =   $this->LastApprovedDocDate();    
      
            $HDR            =   DB::select("SELECT 
            T1.*,T2.BCODE AS BANK_CODE,T2.NAME AS BANK_NAME,T3.BCODE AS FD_BANK_CODE,T3.NAME AS FD_BANK_NAME
            FROM TBL_TRN_FIXED_DEPOSIT T1
            LEFT JOIN TBL_MST_BANK T2 ON T1.BANK_AC=T2.BID
            LEFT JOIN TBL_MST_BANK T3 ON T1.FD_BANK_AC=T3.BID
            WHERE T1.FDID='$id'"); 

            $HDR            =   count($HDR) > 0?$HDR[0]:[];
            $FormId         =   $this->form_id;
            $ActionStatus   =   "";

            return view($this->view.'edit',compact(['FormId','objRights','ActionStatus','HDR','lastDocDate']));      

        }
     
    }

    public function update(Request $request){

        $VTID_REF       =   $this->vtid_ref;
        $USERID_REF     =   Auth::user()->USERID;   
        $ACTIONNAME     =   'EDIT';
        $IPADDRESS      =   $request->getClientIp();
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $FD_CODE                =   $request['FD_CODE'];
        $FD_DATE                =   $request['FD_DATE'];
        $BANK_AC                =   $request['BANK_AC'];
        $BANK_AC_NO             =   $request['BANK_AC_NO'];
        $FD_BANK_AC             =   $request['FD_BANK_AC'];
        $FD_BANK_AC_NO          =   $request['FD_BANK_AC_NO'];
        $MATURITY_DATE          =   $request['MATURITY_DATE'];
        $IN_FAVOUR              =   $request['IN_FAVOUR'];
        $BG_NO_APPLICABLE       =   $request['BG_NO_APPLICABLE'];
        $BG_NO                  =   $request['BG_NO'];
        $PRINCIPLE_AMOUNT       =   $request['PRINCIPLE_AMOUNT'];
        $RATE_OF_INTEREST_BASE  =   $request['RATE_OF_INTEREST_BASE'];
        $RATE_OF_INTEREST       =   $request['RATE_OF_INTEREST'];
        $REMARKS                =   $request['REMARKS'];
       
        $log_data = [
            $FD_CODE,$FD_DATE,$BANK_AC,$BANK_AC_NO,$FD_BANK_AC,
            $FD_BANK_AC_NO,$MATURITY_DATE,$IN_FAVOUR,$BG_NO_APPLICABLE,$BG_NO,
            $PRINCIPLE_AMOUNT,$RATE_OF_INTEREST_BASE,$RATE_OF_INTEREST,$REMARKS,$CYID_REF,  
            $BRID_REF,$FYID_REF,$VTID_REF,$USERID_REF,Date('Y-m-d'), 
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];

        $sp_result  =   DB::select('EXEC SP_FIXED_DEPOSIT_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  
        $contains   =   Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $FD_CODE. ' Sucessfully Updated.']);
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

            $lastDocDate    =   $this->LastApprovedDocDate();    
      
            $HDR            =   DB::select("SELECT 
            T1.*,T2.BCODE AS BANK_CODE,T2.NAME AS BANK_NAME,T3.BCODE AS FD_BANK_CODE,T3.NAME AS FD_BANK_NAME
            FROM TBL_TRN_FIXED_DEPOSIT T1
            LEFT JOIN TBL_MST_BANK T2 ON T1.BANK_AC=T2.BID
            LEFT JOIN TBL_MST_BANK T3 ON T1.FD_BANK_AC=T3.BID
            WHERE T1.FDID='$id'"); 

            $HDR            =   count($HDR) > 0?$HDR[0]:[];
            $FormId         =   $this->form_id;
            $ActionStatus   =   "disabled";

            return view($this->view.'view',compact(['FormId','objRights','ActionStatus','HDR','lastDocDate']));      

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
        $ACTIONNAME     =   $Approvallevel;
        $IPADDRESS      =   $request->getClientIp();
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
    
        $FD_CODE                =   $request['FD_CODE'];
        $FD_DATE                =   $request['FD_DATE'];
        $BANK_AC                =   $request['BANK_AC'];
        $BANK_AC_NO             =   $request['BANK_AC_NO'];
        $FD_BANK_AC             =   $request['FD_BANK_AC'];
        $FD_BANK_AC_NO          =   $request['FD_BANK_AC_NO'];
        $MATURITY_DATE          =   $request['MATURITY_DATE'];
        $IN_FAVOUR              =   $request['IN_FAVOUR'];
        $BG_NO_APPLICABLE       =   $request['BG_NO_APPLICABLE'];
        $BG_NO                  =   $request['BG_NO'];
        $PRINCIPLE_AMOUNT       =   $request['PRINCIPLE_AMOUNT'];
        $RATE_OF_INTEREST_BASE  =   $request['RATE_OF_INTEREST_BASE'];
        $RATE_OF_INTEREST       =   $request['RATE_OF_INTEREST'];
        $REMARKS                =   $request['REMARKS'];
       
        $log_data = [
            $FD_CODE,$FD_DATE,$BANK_AC,$BANK_AC_NO,$FD_BANK_AC,
            $FD_BANK_AC_NO,$MATURITY_DATE,$IN_FAVOUR,$BG_NO_APPLICABLE,$BG_NO,
            $PRINCIPLE_AMOUNT,$RATE_OF_INTEREST_BASE,$RATE_OF_INTEREST,$REMARKS,$CYID_REF,  
            $BRID_REF,$FYID_REF,$VTID_REF,$USERID_REF,Date('Y-m-d'), 
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];

        $sp_result  =   DB::select('EXEC SP_FIXED_DEPOSIT_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  
        $contains   =   Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $FD_CODE. ' Sucessfully Approved.']);
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
        $TABLE      =   "TBL_TRN_FIXED_DEPOSIT";
        $FIELD      =   "FDID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_FIXED_DEPOSIT',
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
            $objMst =   DB::table("TBL_TRN_FIXED_DEPOSIT")
            ->where('FDID','=',$id)
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

            $dirname =   'FixedDeposit';
                
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
        
		$image_path         =   "docs/company".$CYID_REF."/FixedDeposit";     
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

    public function LastApprovedDocDate(){
        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $VTID_REF   =   $this->vtid_ref;

        return $objlastDocDate = DB::select('SELECT MAX(FD_DATE) FD_DATE FROM TBL_TRN_FIXED_DEPOSIT  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $VTID_REF, $Status ]);

    }

    public function mature($id=NULL){

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

            $lastDocDate    =   $this->LastApprovedDocDate();    
      
            $HDR            =   DB::select("SELECT 
            T1.*,T2.BCODE AS BANK_CODE,T2.NAME AS BANK_NAME,T3.BCODE AS FD_BANK_CODE,T3.NAME AS FD_BANK_NAME
            FROM TBL_TRN_FIXED_DEPOSIT T1
            LEFT JOIN TBL_MST_BANK T2 ON T1.BANK_AC=T2.BID
            LEFT JOIN TBL_MST_BANK T3 ON T1.FD_BANK_AC=T3.BID
            WHERE T1.FDID='$id'"); 

            $HDR            =   count($HDR) > 0?$HDR[0]:[];
            $FormId         =   $this->form_id;
            $ActionStatus   =   "disabled";

            if(strtoupper($HDR->STATUS)=="A" && $HDR->MATURE_TYPE =='PRE MATURITY'){
                exit("Sorry, Your record already mature.");
            }

            return view($this->view.'mature',compact(['FormId','objRights','ActionStatus','HDR','lastDocDate']));      

        }
     
    }

    public function updateMature(Request $request){

        $VTID_REF               =   $this->vtid_ref;
        $USERID_REF             =   Auth::user()->USERID;   
        $ACTIONNAME             =   'MATURE';
        $IPADDRESS              =   $request->getClientIp();
        $CYID_REF               =   Auth::user()->CYID_REF;
        $BRID_REF               =   Session::get('BRID_REF');
        $FYID_REF               =   Session::get('FYID_REF');

        $FD_CODE                =   $request['FD_CODE'];
        $MATURE_TYPE            =   $request['MATURE_TYPE'];
        $PRE_MATURITY_DATE      =   $request['PRE_MATURITY_DATE'];
        $INTEREST_CALCULATOR    =   $request['INTEREST_CALCULATOR'];
        $GROSS_FD_VALUE         =   $request['GROSS_FD_VALUE'];
       
        $log_data = [
            $FD_CODE,$CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,
            $USERID_REF,Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,
            $MATURE_TYPE,$PRE_MATURITY_DATE,$INTEREST_CALCULATOR,$GROSS_FD_VALUE
        ];

        $sp_result  =   DB::select('EXEC SP_FIXED_DEPOSIT_MATURE ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $log_data);  
        $contains   =   Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $FD_CODE. ' Sucessfully Updated.']);
        }
        else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();
    }
    
}
