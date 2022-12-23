<?php
namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Master\TblMstFrm11;
use App\Models\Admin\TblMstUser;
use Auth;
use DB;
use Session;
use Response;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MstFrm528Controller extends Controller{

    protected $form_id  =   528;
    protected $vtid_ref =   598;
    protected $view     =   "masters.UDF.mstfrm528";
    
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){  
        $FormId     =   $this->form_id;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       

        $objRights  =   DB::table('TBL_MST_USERROLMAP')
                        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                        ->first();

        $objDataList=   DB::select("SELECT 
        T1.*,
        T2.DESCRIPTIONS AS VOUCHER_TYPE_NAME
        FROM TBL_MST_UDF T1
        INNER JOIN TBL_MST_VOUCHERTYPE T2 ON T2.VTID=T1.VOUCHER_TYPE
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND PARENTID='0' ");
 
        return view($this->view,compact(['FormId','objRights','objDataList']));        
    }

    public function add(){

        $FormId         =   $this->form_id;
        $voucher_type   =   $this->get_voucher_type();
        
        return view($this->view.'add',compact(['FormId','voucher_type']));       
    }


    public function save(Request $request) {

        $VOUCHER_TYPE   =   $request['VTID_REF'];
        $r_count        =   $request['Row_Count'];
        
        $data = [
            'UDFID' => "0",
            'LABEL'    => strtoupper($request['LABEL_0']),
            'VALUETYPE' => $request['VALUETYPE_0'],
            'DESCRIPTIONS' => (isset($request->DESCRIPTIONS_0)? $request->DESCRIPTIONS_0 : ""),
            'ISMANDATORY' => (isset($request['ISMANDATORY_0'])!="true" ? 0 : 1) ,
            'DEACTIVATED' => (isset($request['DEACTIVATED_0'])!="true" ? 0 : 1) ,
            'DODEACTIVATED' => !(is_null($request['DODEACTIVATED_0'])||empty($request['DODEACTIVATED_0']))=="true"? $request['DODEACTIVATED_0'] : NULL,
        ];
         
        $links["UDF"] = $data; 
        $parentxml = ArrayToXml::convert($links);
        
        for ($i=0; $i<=$r_count; $i++)
        {
            if((isset($request['LABEL_'.$i])))
            {
                $req_data[$i] = [
                    'UDFID' => (isset($request['UDFID_'.$i]) ? $request['UDFID_'.$i] : "0"),
                    'LABEL'    => strtoupper($request['LABEL_'.$i]),
                    'VALUETYPE' => $request['VALUETYPE_'.$i],
                    'DESCRIPTIONS' => !(is_null($request['DESCRIPTIONS_'.$i]))=="true"? $request['DESCRIPTIONS_'.$i] : "",
                    'ISMANDATORY' => (isset($request['ISMANDATORY_'.$i])!="true" ? 0 : 1) ,
                    'DEACTIVATED' => (isset($request['DEACTIVATED_'.$i])!="true" ? 0 : 1) ,
                    'DODEACTIVATED' => !(is_null($request['DODEACTIVATED_'.$i])||empty($request['DODEACTIVATED_'.$i]))=="true"? $request['DODEACTIVATED_'.$i] : NULL,
                ];
            }
        }
            $wrapped_links["UDF"] = $req_data; 

            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            
            
            $xml = ArrayToXml::convert($wrapped_links);
            
            $log_data = [ 
                $CYID_REF,$BRID_REF,$FYID_REF,$parentxml,$xml,
                $VTID_REF,$USERID, Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,
                $IPADDRESS,$VOUCHER_TYPE
            ];

            
            $sp_result = DB::select('EXEC SP_UDF ?,?,?,?,?, ?,?,?,?,?, ?,?',  $log_data);       
            
        
            if($sp_result[0]->RESULT=="SUCCESS"){
    
                return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);
    
            }else{
                return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
            }
            exit();   
    }

    public function edit($id){

        if(!is_null($id)){

            $id             =   urldecode(base64_decode($id));
            $CYID_REF       =   Auth::user()->CYID_REF;
            $BRID_REF       =   Session::get('BRID_REF');
            $FYID_REF       =   Session::get('FYID_REF');
            $FormId         =   $this->form_id;
            $voucher_type   =   $this->get_voucher_type();
            $ActionStatus   =   "";
            
            $objUdfResponse =   DB::table('TBL_MST_UDF')                    
                                ->where('TBL_MST_UDF.UDFID','=',$id)
                                ->orwhere('TBL_MST_UDF.PARENTID','=',$id)
                                ->select('TBL_MST_UDF.*')
                                ->orderBy('TBL_MST_UDF.UDFID','ASC')
                                ->get()->toArray();
            $objCount       =   count($objUdfResponse);

            $voucher_details=array();
            if($objCount > 0){
                $voucher_details=DB::select("SELECT 
                T1.VOUCHER_TYPE,
                CONCAT(T2.VCODE,' - ',T2.DESCRIPTIONS) AS VOUCHER_TYPE_NAME
                FROM TBL_MST_UDF T1
                INNER JOIN TBL_MST_VOUCHERTYPE T2 ON T2.VTID=T1.VOUCHER_TYPE
                WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND UDFID='$id'");
            }

            if(strtoupper($objUdfResponse[0]->STATUS)!="N"){
                exit("Sorry, Only Un Approved record can edit.");
            }
     
            $objRights      =   DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();

            

           return view($this->view.'edit',compact(['FormId','objUdfResponse','objRights','objCount','voucher_type','voucher_details','ActionStatus']));
        }
     
    }
     
    public function view($id){

        if(!is_null($id)){

            $id             =   urldecode(base64_decode($id));
            $CYID_REF       =   Auth::user()->CYID_REF;
            $BRID_REF       =   Session::get('BRID_REF');
            $FYID_REF       =   Session::get('FYID_REF');
            $FormId         =   $this->form_id;
            $voucher_type   =   $this->get_voucher_type();
            $ActionStatus   =   "disabled";
            
            $objUdfResponse =   DB::table('TBL_MST_UDF')                    
                                ->where('TBL_MST_UDF.UDFID','=',$id)
                                ->orwhere('TBL_MST_UDF.PARENTID','=',$id)
                                ->select('TBL_MST_UDF.*')
                                ->orderBy('TBL_MST_UDF.UDFID','ASC')
                                ->get()->toArray();
            $objCount       =   count($objUdfResponse);

            $voucher_details=array();
            if($objCount > 0){
                $voucher_details=DB::select("SELECT 
                T1.VOUCHER_TYPE,
                CONCAT(T2.VCODE,' - ',T2.DESCRIPTIONS) AS VOUCHER_TYPE_NAME
                FROM TBL_MST_UDF T1
                INNER JOIN TBL_MST_VOUCHERTYPE T2 ON T2.VTID=T1.VOUCHER_TYPE
                WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND UDFID='$id'");
            }

            $objRights      =   DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();

           return view($this->view.'view',compact(['FormId','objUdfResponse','objRights','objCount','voucher_type','voucher_details','ActionStatus']));
        }
     
    }

    public function update(Request $request){

        $VOUCHER_TYPE   =   $request['VTID_REF'];
       $r_count = $request['Row_Count'];
       
        $data = [
            'UDFID' => (isset($request->UDFID_0) ? $request->UDFID_0 : "0"),
            'LABEL'    => strtoupper($request->LABEL_0),
            'VALUETYPE' => $request->VALUETYPE_0,
            'DESCRIPTIONS' => (isset($request->DESCRIPTIONS_0)? $request->DESCRIPTIONS_0 : ""),
            'ISMANDATORY' => (isset($request->ISMANDATORY_0) ? 1 : 0) ,
            'DEACTIVATED' => (isset($request->DEACTIVATED_0) ? 1 : 0) ,
            'DODEACTIVATED' => (isset($request->DODEACTIVATED_0)? $request->DODEACTIVATED_0 : NULL),
            ];
        $links["UDF"] = $data; 
        $parentxml = ArrayToXml::convert($links);
        
        for ($i=0; $i<=$r_count; $i++)
        {
            if((isset($request['UDFID_'.$i])) && (isset($request['LABEL_'.$i])))
            {
                $req_data[$i] = [
                    'UDFID' => (isset($request['UDFID_'.$i]) ? $request['UDFID_'.$i] : "0") ,
                    'LABEL'    => strtoupper($request['LABEL_'.$i]),
                    'VALUETYPE' => $request['VALUETYPE_'.$i],
                    'DESCRIPTIONS' => !(is_null($request['DESCRIPTIONS_'.$i]))=="true"? $request['DESCRIPTIONS_'.$i] : "",
                    'ISMANDATORY' => (isset($request['ISMANDATORY_'.$i])!="true" ? 0 : 1) ,
                    'DEACTIVATED' => (isset($request['DEACTIVATED_'.$i])!="true" ? 0 : 1) ,
                    'DODEACTIVATED' => !(is_null($request['DODEACTIVATED_'.$i])||empty($request['DODEACTIVATED_'.$i]))=="true"? $request['DODEACTIVATED_'.$i] : NULL,
                ];
            }
        }
            
        // dd($req_data);
        $wrapped_links["UDF"] = $req_data; 
        $xml = ArrayToXml::convert($wrapped_links);
        //  dd($xml); 
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID_REF     =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $ACTIONNAME     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();


        $log_data = [ 
            $CYID_REF,$BRID_REF,$FYID_REF,$parentxml,$xml,
            $VTID_REF,$USERID, Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,
            $IPADDRESS,$VOUCHER_TYPE
        ];

        
        $sp_result = DB::select('EXEC SP_UDF ?,?,?,?,?, ?,?,?,?,?, ?,?',  $log_data);       

    
        

            if($sp_result[0]->RESULT=="SUCCESS"){

                return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

            }elseif($sp_result[0]->RESULT=="Some cancel records in input records"){
                            
                return Response::json(['cancel'=>true,'msg' => 'Already cancel record exists with same data.']);
                
            }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
                
                return Response::json(['duplicate'=>true,'msg' => 'Duplicate record.','reqdata'=>'duplicate']);
                
            }else{
                return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','reqdata'=>'Some Error']);
            }
            
            exit();    
    }

    
    public function Approve(Request $request){

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
           
            $VOUCHER_TYPE   =   $request['VTID_REF'];
            $r_count = $request['Row_Count'];

            $data = [
                'UDFID' => (isset($request->UDFID_0)? $request->UDFID_0 : "0"),
                'LABEL'    => strtoupper($request->LABEL_0),
                'VALUETYPE' => $request->VALUETYPE_0,
                'DESCRIPTIONS' => (isset($request->DESCRIPTIONS_0)? $request->DESCRIPTIONS_0 : ""),
                'ISMANDATORY' => (isset($request->ISMANDATORY_0) ? 1 : 0) ,
                'DEACTIVATED' => (isset($request->DEACTIVATED_0) ? 1 : 0) ,
                'DODEACTIVATED' => (isset($request->DODEACTIVATED_0)? $request->DODEACTIVATED_0 : NULL),
                ];
            // dd($r_count);   
            $links["UDF"] = $data; 
            $parentxml = ArrayToXml::convert($links);
        
            for ($i=0; $i<=$r_count; $i++)
            {
                if((isset($request['UDFID_'.$i])) && (isset($request['LABEL_'.$i])))
                {
                    $req_data[$i] = [
                        'UDFID' => (isset($request['UDFID_'.$i]) ? $request['UDFID_'.$i] : "0") ,
                        'LABEL'    => strtoupper($request['LABEL_'.$i]),
                        'VALUETYPE' => $request['VALUETYPE_'.$i],
                        'DESCRIPTIONS' => !(is_null($request['DESCRIPTIONS_'.$i]))=="true"? $request['DESCRIPTIONS_'.$i] : "",
                        'ISMANDATORY' => (isset($request['ISMANDATORY_'.$i])!="true" ? 0 : 1) ,
                        'DEACTIVATED' => (isset($request['DEACTIVATED_'.$i])!="true" ? 0 : 1) ,
                        'DODEACTIVATED' => !(is_null($request['DODEACTIVATED_'.$i])||empty($request['DODEACTIVATED_'.$i]))=="true"? $request['DODEACTIVATED_'.$i] : NULL,
                    ];
                }
            }
            
            $wrapped_links["UDF"] = $req_data; 
            $xml = ArrayToXml::convert($wrapped_links);

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID_REF     =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $ACTIONNAME     = $Approvallevel;
        $IPADDRESS  =   $request->getClientIp();

        $log_data = [ 
            $CYID_REF,$BRID_REF,$FYID_REF,$parentxml,$xml,
            $VTID_REF,$USERID, Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,
            $IPADDRESS,$VOUCHER_TYPE
        ];

        $sp_result = DB::select('EXEC SP_UDF ?,?,?,?,?, ?,?,?,?,?, ?,?',  $log_data);  


        if($sp_result[0]->RESULT=="SUCCESS"){

        return Response::json(['success' =>true,'msg' => 'Record successfully Approved.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
        
        return Response::json(['duplicate'=>true,'msg' => 'Duplicate record.','reqdata'=>'duplicate']);
        
        }else{
        return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','reqdata'=>'Some Error']);
        }
        
        exit();    
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
            

                // $LABEL          =   $request['LABEL'];
                // $VALUETYPE      =   $request['VALUETYPE'];
                // $DESCRIPTIONS    =   $request['DESCRIPTIONS'];
                // $DEACTIVATED    =   $request['DEACTIVATED'];
                // $ISMANDATORY    =   $request['ISMANDATORY'];     
                // $DODEACTIVATED  =   $request['DODEACTIVATED'];  
                
                // $r_count = $request['ID']->length();
                
                $req_data =  json_decode($request['ID']);

                // dd($req_data);
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
                $TABLE      =   "TBL_MST_UDF";
                $FIELD      =   "UDFID";
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
            
            return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','reqdata'=>'norecord']);
            
            }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','reqdata'=>'Some Error']);
            }
            
            exit();    
    }

   
    public function cancel(Request $request){
   
        $id = $request->{0};

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_UDF";
        $FIELD      =   "UDFID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $canceldata[0]=[
            'NT'  => 'TBL_MST_UDF',
       ];        
       $links["TABLES"] = $canceldata; 
       $cancelxml = ArrayToXml::convert($links);
        
        $udf_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$cancelxml ];

        $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $udf_cancel_data);

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

            $objResponse = DB::table('TBL_MST_UDF')->where('UDFID','=',$id)->first();

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

            $dirname =   'UDF';

            return view($this->view.'attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments','dirname']));
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
        
		$image_path         =   "docs/company".$CYID_REF."/UDF";     
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

    public function checkLabel(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $LABEL = $request->LABEL_0;
        $VOUCHER_TYPE = $request->VTID_REF;
        
        $objLabel = DB::table('TBL_MST_UDF')
        ->where('TBL_MST_UDF.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_UDF.LABEL','=',$LABEL)
        ->where('TBL_MST_UDF.VOUCHER_TYPE','=',$VOUCHER_TYPE)
        ->select('TBL_MST_UDF.UDFID')
        ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate Label']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }

    public function get_voucher_type(){
        $CYID_REF   =   Auth::user()->CYID_REF;

        $data       =   DB::select("SELECT 
        T1.VTID AS VTID,
        T1.VCODE AS VTCODE,
        T1.DESCRIPTIONS AS VTDESCRIPTIONS 
        FROM TBL_MST_VOUCHERTYPE T1
        INNER JOIN TBL_MST_MODULE_VOUCHER_MAP T2 ON T1.VTID=T2.VTID_REF
        WHERE T2.CYID_REF='$CYID_REF' AND T2.[STATUS]='A' 
        AND (T2.DEACTIVATED=0 OR T2.DEACTIVATED IS NULL)");

        return $data;
    }
    
}
