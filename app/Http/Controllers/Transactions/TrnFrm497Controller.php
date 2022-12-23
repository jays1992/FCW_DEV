<?php
namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use DB;
use Facade\Ignition\DumpRecorder\Dump;
use Session;
use Response;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use App\Helpers\Utils;


class TrnFrm497Controller extends Controller{

    protected $form_id  = 497;
    protected $vtid_ref = 567;
    protected $view     = "transactions.sales.ExtendedWarranty.trnfrm";
   
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

        $objDataList	=	DB::select("select hdr.EWID,hdr.EW_NO,hdr.EW_DT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.EWID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_EXTWARRANTY_HDR hdr
                            on a.VID = hdr.EWID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.EWID DESC ");

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

        $Status         =   "A";
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $FormId         =   $this->form_id;       
        $objCUST        =   $this->getCUST();
        $AlpsStatus     =   $this->AlpsStatus();   
        
        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_EXTWARRANTY_HDR',
            'HDR_ID'=>'EWID',
            'HDR_DOC_NO'=>'EW_NO',
            'HDR_DOC_DT'=>'EW_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
        
               
         
        return view($this->view.$FormId.'add',compact(['objCUST','FormId','AlpsStatus','doc_req','docarray']));      
    }

    public function save(Request $request) {       
       
        //DUMP($request->all());

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $VTID_REF       =   $this->vtid_ref;
        $USERID_REF     =   Auth::user()->USERID;   
        $ACTION         =   'ADD';        
        $UPDATE         =   Date('Y-m-d');        
        $UPTIME         =   Date('h:i:s.u');
        $IPADDRESS      =   $request->getClientIp();

        $EW_NO          =   strtoupper(trim($request['DOC_NO']));
        $EW_DT          =   $request['DOC_DT'];
        $CUSTOMER_REF   =   $request['CUSTID_REF'];
       
		$MatDetails  = array();
        if(isset($_REQUEST['INVNOID_REF']) && !empty($_REQUEST['INVNOID_REF'])){
            foreach($_REQUEST['INVNOID_REF'] as $key=>$val){

                $MatDetails[] = array(
                'EWINVNOID_REF'     => trim($_REQUEST['INVNOID_REF'][$key])?trim($_REQUEST['INVNOID_REF'][$key]):NULL,
                'ITEMID_REF'        => trim($_REQUEST['ITEMID_REF'][$key])?trim($_REQUEST['ITEMID_REF'][$key]):NULL,
                'EXTWA_MONTH'       => trim($_REQUEST['EXTENDEDWNTMONTH'][$key])?trim($_REQUEST['EXTENDEDWNTMONTH'][$key]):NULL,
                'EW_STARTFROM'      => trim($_REQUEST['STARTFROM'][$key])?trim($_REQUEST['STARTFROM'][$key]):NULL,
                'EW_STARTTO'        => trim($_REQUEST['STARTTO'][$key])?trim($_REQUEST['STARTTO'][$key]):NULL,
                'EXTWA_AMOUNT'      => trim($_REQUEST['WARRANTYAMT'][$key])?trim($_REQUEST['WARRANTYAMT'][$key]):NULL,
                'EXTOTAL_AMOUNT'    => trim($_REQUEST['TOTALAMT'][$key])?trim($_REQUEST['TOTALAMT'][$key]):NULL,
                'EXTW_TAX'          => trim($_REQUEST['TOTALTAX'][$key])?trim($_REQUEST['TOTALTAX'][$key]):NULL,
                );
            }
        }

        if(!empty($MatDetails)){
            $wrapped_links["MAT"] = $MatDetails; 
            $MAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $MAT = NULL; 
        }      
       
        $log_data = [ $EW_NO,        $EW_DT,    $CUSTOMER_REF,  $CYID_REF,  $BRID_REF,  $FYID_REF,
                    $VTID_REF,       $MAT,      $USERID_REF,    $UPDATE,    $UPTIME,    $ACTION,    $IPADDRESS ]; 

        $sp_result = DB::select('EXEC SP_EXT_WARRANTY_IN ?,?,?,?,?,?, ?,?,?,?,?,?,?', $log_data); 
        
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
    }


    public function edit($id){

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

            $objCUST            =   $this->getCUST();

            $objResponse =  DB::table('TBL_TRN_EXTWARRANTY_HDR')
                                ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_EXTWARRANTY_HDR.CUSTOMER_REF','=','TBL_MST_CUSTOMER.SLID_REF')
                                ->where('TBL_TRN_EXTWARRANTY_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_TRN_EXTWARRANTY_HDR.BRID_REF','=',Session::get('BRID_REF'))
                                ->where('TBL_TRN_EXTWARRANTY_HDR.EWID','=',$id)
                                ->select('TBL_TRN_EXTWARRANTY_HDR.*', 'TBL_MST_CUSTOMER.SLID_REF','TBL_MST_CUSTOMER.CCODE','TBL_MST_CUSTOMER.NAME','TBL_MST_CUSTOMER.CGID_REF','TBL_MST_CUSTOMER.GLID_REF','TBL_MST_CUSTOMER.SLID_REF')
                                ->first();   
            
            if(strtoupper($objResponse->STATUS)=="A"){
               // exit("Sorry, Approved record can not edit.");
            }

            $MAT = DB::select("SELECT T1.*,T2.*,T3.*      
                        FROM TBL_TRN_EXTWARRANTY_MAT T1                
                        LEFT JOIN TBL_TRN_SLSI01_HDR T2 ON T1.EWINVNOID_REF=T2.SIID
                        LEFT JOIN TBL_MST_ITEM       T3 ON T1.ITEMID_REF=T3.ITEMID
                        WHERE T1.EWID_REF='$id'");                       


            $FormId         =   $this->form_id;
            $AlpsStatus     =   $this->AlpsStatus();
            $ActionStatus   =   "";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
            return view($this->view.$FormId.'edit',compact(['AlpsStatus','FormId','objRights','objResponse','MAT','ActionStatus','TabSetting','objCUST']));      

        }
     
    }

    public function update(Request $request){
        return  $this->updateRecord($request,'update');        
    } 
    
    public function Approve(Request $request){
      return  $this->updateRecord($request,'approve');    
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

        $EW_NO          =   strtoupper(trim($request['DOC_NO']));
        $EW_DT          =   $request['DOC_DT'];
        $CUSTOMER_REF   =   $request['CUSTID_REF']; 
        
        $MatDetails  = array();
        if(isset($_REQUEST['INVNOID_REF']) && !empty($_REQUEST['INVNOID_REF'])){
            foreach($_REQUEST['INVNOID_REF'] as $key=>$val){

                $MatDetails[] = array(
                'EWINVNOID_REF'     => trim($_REQUEST['INVNOID_REF'][$key])?trim($_REQUEST['INVNOID_REF'][$key]):NULL,
                'ITEMID_REF'        => trim($_REQUEST['ITEMID_REF'][$key])?trim($_REQUEST['ITEMID_REF'][$key]):NULL,
                'EXTWA_MONTH'       => trim($_REQUEST['EXTENDEDWNTMONTH'][$key])?trim($_REQUEST['EXTENDEDWNTMONTH'][$key]):NULL,
                'EW_STARTFROM'      => trim($_REQUEST['STARTFROM'][$key])?trim($_REQUEST['STARTFROM'][$key]):NULL,
                'EW_STARTTO'        => trim($_REQUEST['STARTTO'][$key])?trim($_REQUEST['STARTTO'][$key]):NULL,
                'EXTWA_AMOUNT'      => trim($_REQUEST['WARRANTYAMT'][$key])?trim($_REQUEST['WARRANTYAMT'][$key]):NULL,
                'EXTOTAL_AMOUNT'    => trim($_REQUEST['TOTALAMT'][$key])?trim($_REQUEST['TOTALAMT'][$key]):NULL,
                'EXTW_TAX'          => trim($_REQUEST['TOTALTAX'][$key])?trim($_REQUEST['TOTALTAX'][$key]):NULL,
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

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID_REF   =   $this->vtid_ref;
        $USERID_REF =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   $Approvallevel;
        $IPADDRESS  =   $request->getClientIp();
               
        $log_data = [ $EW_NO,        $EW_DT,    $CUSTOMER_REF,  $CYID_REF,  $BRID_REF,  $FYID_REF,
                      $VTID_REF,     $MAT,      $USERID_REF,    $UPDATE,    $UPTIME,    $ACTION,    $IPADDRESS ];


        $sp_result = DB::select('EXEC SP_EXT_WARRANTY_UP ?,?,?,?,?,?, ?,?,?,?,?,?,?', $log_data);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => 'Record successfully '.$msgTxt]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit(); 
     
    }    

    public function view($id){

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

            $objCUST            =   $this->getCUST();

            $objResponse =  DB::table('TBL_TRN_EXTWARRANTY_HDR')
                                ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_EXTWARRANTY_HDR.CUSTOMER_REF','=','TBL_MST_CUSTOMER.SLID_REF')
                                ->where('TBL_TRN_EXTWARRANTY_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_TRN_EXTWARRANTY_HDR.BRID_REF','=',Session::get('BRID_REF'))
                                ->where('TBL_TRN_EXTWARRANTY_HDR.EWID','=',$id)
                                ->select('TBL_TRN_EXTWARRANTY_HDR.*', 'TBL_MST_CUSTOMER.SLID_REF','TBL_MST_CUSTOMER.CCODE','TBL_MST_CUSTOMER.NAME','TBL_MST_CUSTOMER.CGID_REF','TBL_MST_CUSTOMER.GLID_REF','TBL_MST_CUSTOMER.SLID_REF')
                                ->first();   
            
            if(strtoupper($objResponse->STATUS)=="A"){
               // exit("Sorry, Approved record can not edit.");
            }

            $MAT = DB::select("SELECT T1.*,T2.*,T3.*      
                        FROM TBL_TRN_EXTWARRANTY_MAT T1                
                        LEFT JOIN TBL_TRN_SLSI01_HDR T2 ON T1.EWINVNOID_REF=T2.SIID
                        LEFT JOIN TBL_MST_ITEM       T3 ON T1.ITEMID_REF=T3.ITEMID
                        WHERE T1.EWID_REF='$id'");                       


            $FormId         =   $this->form_id;
            $AlpsStatus     =   $this->AlpsStatus();
            $ActionStatus   =   "disabled";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
            return view($this->view.$FormId.'view',compact(['AlpsStatus','FormId','objRights','objResponse','MAT','ActionStatus','TabSetting','objCUST']));      

        }
     
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
        $TABLE      =   "TBL_TRN_PDRPR_HDR";
        $FIELD      =   "RPRID";
        $ACTIONNAME     = $Approvallevel;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
            
        $log_data = [ 
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_RPR ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
        
        if($sp_result[0]->RESULT=="All records approved"){

        return Response::json(['approve' =>true,'msg' => 'Record successfully Approved.']);

        }elseif($sp_result[0]->RESULT=="NO RECORD FOR APPROVAL"){
        
        return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','save'=>'invalid']);
        
        }else{
        return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','save'=>'invalid']);
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
        $TABLE      =   "TBL_TRN_EXTWARRANTY_HDR";
        $FIELD      =   "EWID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_EXTWARRANTY_MAT',
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

            $objResponse = DB::table('TBL_TRN_EXTWARRANTY_HDR')->where('EWID','=',$id)->first();

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
        
		$image_path         =   "docs/company".$CYID_REF."/ExtendedWarranty";     
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
   

    public function codeduplicate(Request $request){
        
        $DOC_NO  =   strtoupper(trim($request['DOC_NO']));
        $objLabel = DB::table('TBL_TRN_EXTWARRANTY_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('EW_NO','=',$DOC_NO)
        ->select('EWID')->first();

        if($objLabel){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }    

    public function getCUST(){
        
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');      

        $ObjData        =   DB::select("SELECT 
                            SLID_REF,CCODE,NAME,CGID_REF,GLID_REF,SLID_REF 
                            FROM TBL_MST_CUSTOMER  
                            WHERE  CYID_REF ='$CYID_REF' AND STATUS ='A' AND TYPE ='CUSTOMER'");

        $objNewRPRO= [];

            if(!empty($ObjData)){
                
                foreach ($ObjData as $index=>$dataRow){

                    $total_count =  DB::table('TBL_TRN_SLSI01_HDR')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('BRID_REF','=',Session::get('BRID_REF'))
                    ->where('STATUS','=','A')
                    ->where('SLID_REF','=',$dataRow->SLID_REF)
                    ->COUNT();

                    $objNewCust[$index]=$dataRow;                                     
                // if($total_count == 0){
                //     $objNewCust[$index]=$dataRow;
                // }
                }
                return $objNewCust;
            }else{
               return $objNewCust;            
            }

            

    } //function

    public function getINVDetails(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $CUSTID_REF   =   $request['CUSTID_REF'];

        $ObjItem    =   DB::select("SELECT * FROM TBL_TRN_SLSI01_HDR WHERE SLID_REF='$CUSTID_REF' ");

        if(!empty($ObjItem)){
            foreach ($ObjItem as $index=>$dataRow){                       
               echo'
                <tr id="item_'.$index.'"  class="clsinvid">
                <td class="ROW1"><input type="checkbox" id="chkinvId'.$index.'"  value="'.$dataRow->SIID.'" class="invjs-selectall1"  ></td>
                <td class="ROW2">'.$dataRow->SINO.'</td>
                <td class="ROW3">'.$dataRow->SIDT.'</td>
                <td hidden><input type="text" id="txtitem_'.$index.'"data-desc1="'.$dataRow->SIID.'"data-desc2="'.$dataRow->SINO.'"data-desc3="'.$dataRow->SIDT.'"/></td>                        
                </tr>';
            }      
        }           
        else{
            echo '<tr><td> Record not found.</td></tr>';
        }
        exit();
    }
    
    public function getItemDetails(Request $request){
        
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $Status     =   $request['status'];
        $SQID_REF    =   trim($request['SQID_REF'])==""?NULL:$request['SQID_REF'];
        $SEID_REF    =   trim($request['SEID_REF'])==""?NULL:$request['SEID_REF'];
        $SOID_REF    =   trim($request['SOID_REF'])==""?NULL:$request['SOID_REF'];
        $RPRID      =   isset($request['RPRID'])?$request['RPRID']:0;    
        $INVNOID_REF    =   $request['INVNOID_REF'];   
        $AlpsStatus =   $this->AlpsStatus();

        $PROID_REF  =   $request['INVNOID_REF'];

        $ObjItem =   DB::table('TBL_TRN_SLSI01_MAT')
                        ->where('TBL_TRN_SLSI01_MAT.SIID_REF','=',$INVNOID_REF)
                        ->where('TBL_TRN_SLSI01_MAT.SQID_REF','=',$SQID_REF)
                        ->where('TBL_TRN_SLSI01_MAT.SEID_REF','=',$SEID_REF)
                        ->leftJoin('TBL_MST_ITEM','TBL_MST_ITEM.ITEMID','=','TBL_TRN_SLSI01_MAT.ITEMID_REF') 
                        ->select('TBL_TRN_SLSI01_MAT.*','TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ITEMGID_REF','TBL_MST_ITEM.ICID_REF','TBL_MST_ITEM.ITEM_SPECI','TBL_MST_ITEM.MAIN_UOMID_REF','TBL_MST_ITEM.MATERIAL_TYPE')
                        ->orderBy('TBL_TRN_SLSI01_MAT.SIMATID', 'DESC')
                        ->get();

                    
        if(!empty($ObjItem)){

            foreach ($ObjItem as $index=>$dataRow){  
                
                $check_exist =   DB::table('TBL_TRN_EXTWARRANTY_MAT')
                                    ->where('EWINVNOID_REF','=',$INVNOID_REF)
                                    ->where('ITEMID_REF','=',$dataRow->ITEMID)
                                    ->count();

                if($check_exist <= 0){
                  
                $TOTALTAX = $dataRow->IGST+$dataRow->CGST+$dataRow->SGST;
                $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS 
                FROM TBL_MST_UOM  
                WHERE  CYID_REF = ?  AND UOMID = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                [$CYID_REF, $dataRow->MAIN_SIUOMID_REF, 'A' ]);

                $ObjItemGroup =  DB::select('SELECT TOP 1 GROUPCODE, GROUPNAME 
                FROM TBL_MST_ITEMGROUP  
                WHERE  CYID_REF = ?  AND ITEMGID = ?
                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                [$CYID_REF, $dataRow->ITEMGID_REF, 'A' ]);

                $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS 
                FROM TBL_MST_ITEMCATEGORY  
                WHERE  CYID_REF = ?  AND ICID = ?
                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                [$CYID_REF, $dataRow->ICID_REF, 'A' ]);

                $ItemRowData =  DB::select('SELECT top 1 * FROM TBL_MST_ITEM  WHERE ITEMID = ? ', [$dataRow->ITEMID]);

                if(!is_null($ItemRowData[0]->BUID_REF)){
                    $ObjBusinessUnit =  DB::select('SELECT TOP 1  * FROM TBL_MST_BUSINESSUNIT  
                    WHERE  CYID_REF = ? AND BRID_REF = ?  AND BUID = ?', 
                    [$CYID_REF, $BRID_REF, $ItemRowData[0]->BUID_REF]);
                }
                else
                {
                    $ObjBusinessUnit = NULL;
                }

                $BusinessUnit       =   isset($ObjBusinessUnit) && $ObjBusinessUnit != NULL ? $ObjBusinessUnit[0]->BUCODE.'-'.$ObjBusinessUnit[0]->BUNAME : '';
                $ALPS_PART_NO       =   $ItemRowData[0]->ALPS_PART_NO;
                $CUSTOMER_PART_NO   =   $ItemRowData[0]->CUSTOMER_PART_NO;
                $OEM_PART_NO        =   $ItemRowData[0]->OEM_PART_NO;
                $item_unique_row_id  =  $PROID_REF."_".$dataRow->ITEMID;                              
                $INVIDNO = $PROID_REF;


                    echo'
                    <tr id="item_'.$index.'"  class="clsitemid">
                        <td style="width:8%;text-align:center;"><input type="checkbox" id="chkId'.$index.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>
                        <td style="width:10%;">'.$dataRow->ICODE.'</td>
                        <td style="width:10%;">'.$dataRow->NAME.'</td>
                        <td style="width:8%;">'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>
                        <td style="width:8%;">'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>
                        <td style="width:8%;">'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                        <td style="width:8%;">'.$BusinessUnit.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                        <td style="width:8%;">Authorized</td>
                        <td hidden>
                            <input type="text" id="txtitem_'.$index.'" 
                                data-desc1="'.$dataRow->ITEMID.'" 
                                data-desc2="'.$dataRow->ICODE.'" 
                                data-desc3="'.$dataRow->NAME.'" 
                                data-desc4="'.$dataRow->MAIN_SIUOMID_REF.'" 
                                data-desc5="'.$ObjMainUOM[0]->DESCRIPTIONS.'" 
                                data-desc6="'.$TOTALTAX.'" 
                                data-desc7="'.$item_unique_row_id.'" 
                                data-desc8="'.$INVIDNO.'"
                                data-desc9="'.$dataRow->SEID_REF.'"
                                data-desc10=""
                                data-iteminvid="'.$dataRow->SIID_REF.'"                                
                            />
                        </td>
                        <td hidden><input type="hidde" id="addinfoitem_'.$index.'"  data-desc101="'.$ALPS_PART_NO.'" data-desc102="'.$CUSTOMER_PART_NO.'" data-desc103="'.$OEM_PART_NO.'" ></td>
                    </tr>';

                }
            }         
        }           
        else{
            echo '<tr><td> Record not found.</td></tr>';
        }
        exit();
    }


    
    public function getAltUmQty($id,$itemid,$mqty){

        $ObjData =  DB::select('SELECT top 1 TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV WHERE  ITEMID_REF = ? AND TO_UOMID_REF =?', [$itemid,$id]);
    
        if(!empty($ObjData)){
            $auomqty = ($mqty/$ObjData[0]->FROM_QTY)*($ObjData[0]->TO_QTY);
            return $auomqty;
        }else{
           return 0;
        }
    }

    public function changeAltUm(Request $request){

        $id       = $request['altumid'];
        $itemid   = $request['itemid'];
        $mqty     = $request['mqty'];

        $ObjData =  DB::select('SELECT top 1 TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV WHERE  ITEMID_REF = ? AND TO_UOMID_REF =?', [$itemid,$id]);
    
        if(!empty($ObjData)){
            $auomqty = ($mqty/$ObjData[0]->FROM_QTY)*($ObjData[0]->TO_QTY);
            echo $auomqty;
        }else{
            echo '0';
        }
        exit();
    }

    
    

   

    
}
