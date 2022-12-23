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

class TrnFrm499Controller extends Controller{

    protected $form_id    = 499;
    protected $vtid_ref   = 569;
    protected $view       = "transactions.sales.SalesInvoiceAllocation.trnfrm";
       
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

        $FormId         =   $this->form_id;
		$CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF'); 

        $objRights      =   DB::table('TBL_MST_USERROLMAP')
                            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))                            
                            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                            ->first();
        
        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList	=	DB::select("select hdr.SALINVALID,hdr.SALINVAL_NO,hdr.SALINVAL_DATE,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.SALINVALID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_SALESINVOICEALLOCATION_HDR hdr
                            on a.VID = hdr.SALINVALID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.SALINVALID DESC ");

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

        $FormId   = $this->form_id;
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_SALESINVOICEALLOCATION_HDR',
            'HDR_ID'=>'SALINVALID',
            'HDR_DOC_NO'=>'SALINVAL_NO',
            'HDR_DOC_DT'=>'SALINVAL_DATE'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        $objDD = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('STATUS','=','A')
        ->select('TBL_MST_DOCNO_DEFINITION.*')
        ->first();

       

        return view($this->view.$FormId.'add',compact(['FormId','doc_req','docarray']));
    }

   
    public function save(Request $request){

        $SALINVAL_NO                =   trim($request['DOC_NO'])?trim($request['DOC_NO']):NULL;
        $SALINVAL_DATE                =   trim($request['DOC_DT'])?trim($request['DOC_DT']):NULL;
        $SALINVAL_FROM_DATE             =   trim($request['FROM_DATE'])?trim($request['FROM_DATE']):NULL;
        $SALINVAL_TO_DATE               =   trim($request['TO_DATE'])?trim($request['TO_DATE']):NULL;     

        $MatData  = array();
        if(isset($_REQUEST['SALESINVID_REF']) && !empty($_REQUEST['SALESINVID_REF'])){
            foreach($_REQUEST['SALESINVID_REF'] as $key=>$val){

                $MatData[] = array(
                'SALINVOICE_NO'      => trim($_REQUEST['SALESINVID_REF'][$key])?trim($_REQUEST['SALESINVID_REF'][$key]):NULL,                
                'EMPID_REF'           => trim($_REQUEST['EMPID_REF'][$key])?trim($_REQUEST['EMPID_REF'][$key]):NULL,
                'SALINVOICE_AMT'     => trim($_REQUEST['SALESINVOICEAMT'][$key])?trim($_REQUEST['SALESINVOICEAMT'][$key]):0,
                'BIFURCATED_AMT'     => trim($_REQUEST['BIFURCATEAMOUNT'][$key])?trim($_REQUEST['BIFURCATEAMOUNT'][$key]):0,
                );
            }
        }

        //dd($MatData);
    
        if(!empty($MatData)){
            $wrapped_links["MAT"] = $MatData; 
            $MAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $MAT = NULL; 
        }

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');    
        $USERID_REF     =   Auth::user()->USERID;   
        $VTID_REF       =   $this->vtid_ref;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();

        $array_data     = [$SALINVAL_NO,    $SALINVAL_DATE,    $SALINVAL_FROM_DATE,   $SALINVAL_TO_DATE,     $CYID_REF,      $BRID_REF,
                           $FYID_REF,       $VTID_REF,         $MAT,                  $USERID_REF,           $UPDATE,        $UPTIME,
                           $ACTION,         $IPADDRESS ];

        $sp_result = DB::select('EXEC SALES_INVOICE_ALLOCATION_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?', $array_data);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');

        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();    
    }



        public function edit($id){
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

            $HRD = DB::table('TBL_TRN_SALESINVOICEALLOCATION_HDR')
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('BRID_REF','=',$BRID_REF)
                            ->where('SALINVALID','=',$id)
                            ->first();

            $MAT        = DB::table('TBL_TRN_SALESINVOICEALLOCATION_MAT') 
                            ->leftJoin('TBL_TRN_SLSI01_HDR', 'TBL_TRN_SALESINVOICEALLOCATION_MAT.SALINVOICE_NO','=','TBL_TRN_SLSI01_HDR.SIID')
                            ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_SLSI01_HDR.SLID_REF','=','TBL_MST_CUSTOMER.SLID_REF')
                            ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_TRN_SALESINVOICEALLOCATION_MAT.EMPID_REF','=','TBL_MST_EMPLOYEE.EMPID')
                            ->where('TBL_TRN_SALESINVOICEALLOCATION_MAT.SALINVALID_REF','=',$id)
                            ->select('TBL_TRN_SALESINVOICEALLOCATION_MAT.*', 'TBL_TRN_SLSI01_HDR.SIID','TBL_TRN_SLSI01_HDR.SINO','TBL_TRN_SLSI01_HDR.SIDT',
                            'TBL_MST_CUSTOMER.NAME','TBL_MST_EMPLOYEE.EMPID','TBL_MST_EMPLOYEE.EMPCODE','TBL_MST_EMPLOYEE.FNAME')
                            ->get();
                            
            $MAT        = count($MAT) > 0 ?$MAT:[0];

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();
            
            return view($this->view.$FormId.$type,compact(['HRD','objRights','FormId','MAT','ActionStatus']));
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

        $SALINVAL_NO                =   trim($request['DOC_NO'])?trim($request['DOC_NO']):NULL;
        $SALINVAL_DATE                =   trim($request['DOC_DT'])?trim($request['DOC_DT']):NULL;
        $SALINVAL_FROM_DATE             =   trim($request['FROM_DATE'])?trim($request['FROM_DATE']):NULL;
        $SALINVAL_TO_DATE               =   trim($request['TO_DATE'])?trim($request['TO_DATE']):NULL; 

        $MatData  = array();
        if(isset($_REQUEST['SALESINVID_REF']) && !empty($_REQUEST['SALESINVID_REF'])){
            foreach($_REQUEST['SALESINVID_REF'] as $key=>$val){

                $MatData[] = array(
                'SALINVOICE_NO'      => trim($_REQUEST['SALESINVID_REF'][$key])?trim($_REQUEST['SALESINVID_REF'][$key]):NULL,                
                'EMPID_REF'           => trim($_REQUEST['EMPID_REF'][$key])?trim($_REQUEST['EMPID_REF'][$key]):NULL,
                'SALINVOICE_AMT'     => trim($_REQUEST['SALESINVOICEAMT'][$key])?trim($_REQUEST['SALESINVOICEAMT'][$key]):0,
                'BIFURCATED_AMT'     => trim($_REQUEST['BIFURCATEAMOUNT'][$key])?trim($_REQUEST['BIFURCATEAMOUNT'][$key]):0,
                );
            }
        }

        //dd($MatData);
    
        if(!empty($MatData)){
            $wrapped_links["MAT"] = $MatData; 
            $MAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $MAT = NULL; 
        }

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID_REF       =   $this->vtid_ref;
        $USERID_REF     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   $Approvallevel;
        $IPADDRESS  =   $request->getClientIp();
            

        $array_data     = [$SALINVAL_NO,    $SALINVAL_DATE,    $SALINVAL_FROM_DATE,   $SALINVAL_TO_DATE,     $CYID_REF,      $BRID_REF,
                           $FYID_REF,       $VTID_REF,         $MAT,                  $USERID_REF,           $UPDATE,        $UPTIME,
                           $ACTION,         $IPADDRESS ];

        $sp_result = DB::select('EXEC SALES_INVOICE_ALLOCATION_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?', $array_data);

            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);
    
            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            exit();     
    }


    public function cancel(Request $request){

        $id = $request->{0};

       $USERID =   Auth::user()->USERID;
        $VTID   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');  
        $TABLE      =   "TBL_TRN_SALESINVOICEALLOCATION_HDR";
        $FIELD      =   "SALINVALID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_SALESINVOICEALLOCATION_MAT',
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


   public function attachment($id){

    if(!is_null($id)){
    
        $FormId     =   $this->form_id;

        $objResponse = DB::table('TBL_TRN_SALESINVOICEALLOCATION_HDR')->where('SALINVALID','=',$id)->first();

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
    
    $image_path         =   "docs/company".$CYID_REF."/SalesInvoiceAllocation";     
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
        return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","File Already Uploaded");
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


                 
/*************************************   Sales Invoice Code    ****************************************************** */

            public function getINVDetails(Request $request){

                $CYID_REF   =   Auth::user()->CYID_REF;
                $BRID_REF   =   Session::get('BRID_REF');
                $FYID_REF   =   Session::get('FYID_REF');

                $INVFROMDATE     =  $request['INVFROMDATE'];
                $INVTODATE       =  $request['INVTODATE'];

                $ObjItem    =   DB::select("SELECT T1.*, T2.*
                                FROM TBL_TRN_SLSI01_HDR T1
                                LEFT JOIN TBL_MST_CUSTOMER T2 ON T1.SLID_REF=T2.SLID_REF 
                                WHERE SIDT BETWEEN '$INVFROMDATE' AND '$INVTODATE' AND T1.STATUS='A'");

                if(!empty($ObjItem)){
                    foreach ($ObjItem as $index=>$dataRow){ 

                        $SIID   =   $dataRow->SIID;
                        $data	=   DB::select("SELECT DISTINCT T2.SCNO AS SCNO FROM TBL_TRN_SLSI01_MAT T1 
                        LEFT JOIN TBL_TRN_SLSC01_HDR T2 ON T1.SCID_REF=T2.SCID
                        WHERE T1.SIID_REF='$SIID'");

                        $SCNO   =   array();

                        if(isset($data) && !empty($data)){
                            foreach($data as $index=>$row){

                                if($row->SCNO !=''){
                                    $SCNO[]=$row->SCNO;
                                }

                            }
                        }

                        $TOTAL_MAT_AMT  =   $this->getTotalMaterialAmount($SIID);
                        $TOTAL_CAL_AMT  =   $this->getTotalCalculationAmount($SIID);
                        $TOTAL_TDS_AMT  =   $this->getTotalTdsAmount($SIID);
                        $TOTAL_AMOUNT   =   ($TOTAL_MAT_AMT+$TOTAL_CAL_AMT)-$TOTAL_TDS_AMT;

                        echo'                            
                        <tr>
                        <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->SIID .'" class="clsinvno" value="'.$dataRow->SIID.'" ></td>
                        <td class="ROW2">'.$dataRow->SINO.'</td>
                        <td class="ROW3">'.$dataRow->SIDT.'</td>                            
                        <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->SIID.'" data-desc="'.$dataRow->SINO.'" data-cdate="'.$dataRow->SIDT.'" data-cname="'.$dataRow->NAME.'" data-csiamt="'.$TOTAL_AMOUNT.'" value="'.$dataRow->SIID.'"/></td>                       
                        </tr>';
                        
                        } 
                    die;        
                }           
                else{
                    echo '<tr><td> Record not found.</td></tr>';
                }
                exit();
            }

    /*************************************   Sales Person Code    ****************************************************** */

            public function getEmplyDetails(Request $request){

                $CYID_REF   =   Auth::user()->CYID_REF;
                $BRID_REF   =   Session::get('BRID_REF');
                $FYID_REF   =   Session::get('FYID_REF');

            $objData    =   DB::table('TBL_MST_EMPLOYEE')
                                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('BRID_REF','=',Session::get('BRID_REF'))
                                ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                                ->where('SALES_PERSON','=',1)
                                ->where('STATUS','=','A')
                                ->select('EMPID','EMPCODE','FNAME')
                                ->get();                        
                                              
                if(!empty($objData)){
                    foreach ($objData as $index=>$dataRow){ 

                        echo'                            
                        <tr>
                        <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->EMPID .'" class="clsemp" value="'.$dataRow->EMPID.'" ></td>
                        <td class="ROW2">'.$dataRow->EMPCODE.'</td>
                        <td class="ROW3">'.$dataRow->FNAME.'</td>                            
                        <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->EMPID.'" data-desc="'.$dataRow->EMPCODE.'-'.$dataRow->FNAME.'" value="'.$dataRow->EMPID.'"/></td>                       
                        </tr>';
                        
                        } 
                    die;        
                }           
                else{
                    echo '<tr><td> Record not found.</td></tr>';
                }
                exit();
            }


    /*************************************   Sales Invoice Amount Code    ****************************************************** */

            
    function getTotalMaterialAmount($SIID_REF){

        $TOTAL_MAT_AMOUNT   =   0;
        $data               =   DB::select("SELECT * FROM TBL_TRN_SLSI01_MAT	WHERE SIID_REF	='$SIID_REF'");

        if(isset($data) && !empty($data)){
            foreach($data as $key=>$val){
                $QTY        =   $val->SIMAIN_QTY !=""?floatval($val->SIMAIN_QTY):0;
                $RATE       =   $val->RATEPUOM !=""?floatval($val->RATEPUOM):0;
                $DIS_PER    =   $val->DISPER !=""?floatval($val->DISPER):0;
                $DIS_AMT    =   $val->DISCOUNT_AMT !=""?floatval($val->DISCOUNT_AMT):0;
                $IGST       =   $val->IGST !=""?floatval($val->IGST):0;
                $CGST       =   $val->CGST !=""?floatval($val->CGST):0;
                $SGST       =   $val->SGST !=""?floatval($val->SGST):0;

                $TOTAL_AMOUNT   =   $QTY*$RATE;
                
                if($DIS_PER > 0){
                    $TOTAL_DISCOUNT   =   ($TOTAL_AMOUNT*$DIS_PER)/100;
                }
                else if($DIS_AMT > 0){
                    $TOTAL_DISCOUNT   =   $DIS_AMT;
                }
                else{
                    $TOTAL_DISCOUNT   =   0;
                }

                $TOTAL_AMOUNT       =   $TOTAL_AMOUNT-$TOTAL_DISCOUNT;

                $IGST_AMOUNT        =   ($TOTAL_AMOUNT*$IGST)/100;
                $CGST_AMOUNT        =   ($TOTAL_AMOUNT*$CGST)/100;
                $SGST_AMOUNT        =   ($TOTAL_AMOUNT*$SGST)/100;
                $TOTAL_TAX_AMOUNT   =   ($IGST_AMOUNT+$CGST_AMOUNT+$SGST_AMOUNT);
                $TOTAL_AMOUNT       =   $TOTAL_AMOUNT+$TOTAL_TAX_AMOUNT;

                $TOTAL_MAT_AMOUNT   =   $TOTAL_MAT_AMOUNT+$TOTAL_AMOUNT;
            }
        }

        return $TOTAL_MAT_AMOUNT;
    }

    function getTotalCalculationAmount($SIID_REF){

        $TOTAL_CAL_AMOUNT   =   0;
        $data               =   DB::select("SELECT * FROM TBL_TRN_SLSI01_CAL	WHERE SIID_REF	='$SIID_REF'");

        if(isset($data) && !empty($data)){
            foreach($data as $key=>$val){

                $VALUE              =   $val->VALUE !=""?floatval($val->VALUE):0;
                $IGST               =   $val->IGST !=""?floatval($val->IGST):0;
                $CGST               =   $val->CGST !=""?floatval($val->CGST):0;
                $SGST               =   $val->SGST !=""?floatval($val->SGST):0;

                $TOTAL_AMOUNT       =   $VALUE;

                $IGST_AMOUNT        =   ($TOTAL_AMOUNT*$IGST)/100;
                $CGST_AMOUNT        =   ($TOTAL_AMOUNT*$CGST)/100;
                $SGST_AMOUNT        =   ($TOTAL_AMOUNT*$SGST)/100;
                $TOTAL_TAX_AMOUNT   =   ($IGST_AMOUNT+$CGST_AMOUNT+$SGST_AMOUNT);

                $TOTAL_AMOUNT       =   $TOTAL_AMOUNT+$TOTAL_TAX_AMOUNT;
                $TOTAL_CAL_AMOUNT   =   $TOTAL_CAL_AMOUNT+$TOTAL_AMOUNT;
            }
        }

        return $TOTAL_CAL_AMOUNT;
    }

    function getTotalTdsAmount($SIID_REF){

        $TOTAL_TDS_AMOUNT   =   0;
        $data               =   DB::select("SELECT * FROM TBL_TRN_SLSI01_TDS	WHERE SIID_REF	='$SIID_REF'");

        if(isset($data) && !empty($data)){
            foreach($data as $key=>$val){

                $ASSESSABLE_VL_TDS  =   $val->ASSESSABLE_VL_TDS !=""?floatval($val->ASSESSABLE_VL_TDS):0;
                $TDS_RATE           =   $val->TDS_RATE !=""?floatval($val->TDS_RATE):0;
                $TOTAL_AMOUNT       =   ($ASSESSABLE_VL_TDS*$TDS_RATE)/100;
                $TOTAL_TDS_AMOUNT   =   $TOTAL_TDS_AMOUNT+$TOTAL_AMOUNT;
            }
        }

        return $TOTAL_TDS_AMOUNT;
    }

        
        


  























                   





















}