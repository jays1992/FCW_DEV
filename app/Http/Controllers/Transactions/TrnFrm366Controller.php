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

class TrnFrm366Controller extends Controller{

    protected $form_id  = 366;
    protected $vtid_ref = 451;
    protected $view     = "transactions.PlantMaintenance.Preventive_Maintenance_Schedule.trnfrm366";
   
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];
    
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

        $REQUEST_DATA   =   array(
            'FORMID'    =>  $this->form_id,
            'VTID_REF'  =>  $this->vtid_ref,
            'USERID'    =>  Auth::user()->USERID,
            'CYID_REF'  =>  Auth::user()->CYID_REF,
            'BRID_REF'  =>  Session::get('BRID_REF'),
            'FYID_REF'  =>  Session::get('FYID_REF'),
        );
        
        $DATA_STATUS    =	Helper::get_user_level($REQUEST_DATA);
        $USER_LEVEL     =   $DATA_STATUS['USER_LEVEL'];
        
        $objDataList    =	DB::select("SELECT '$USER_LEVEL' AS USER_LEVEL,T1.*,T2.ACTIONNAME,
        (
        SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
        LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
        WHERE  AUD.VID=T1.PMSL_ID AND  AUD.CYID_REF=T1.CYID_REF AND  AUD.BRID_REF=T1.BRID_REF AND  
        AUD.FYID_REF=T1.FYID_REF AND  AUD.VTID_REF=T1.VTID_REF AND AUD.ACTIONNAME='ADD'       
        ) AS CREATED_BY
        FROM TBL_TRN_PM_SCHEDULE T1
        INNER JOIN TBL_TRN_AUDITTRAIL T2 ON T1.PMSL_ID=T2.VID AND T1.VTID_REF=T2.VTID_REF AND T1.CYID_REF=T2.CYID_REF AND T1.BRID_REF=T2.BRID_REF 
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' AND T2.VTID_REF = '$this->vtid_ref' AND T2.ACTID IN (SELECT max(ACTID) FROM TBL_TRN_AUDITTRAIL A WHERE T2.VTID_REF = A.VTID_REF AND T2.VID = A.VID)
        ORDER BY T1.PMSL_ID DESC 
        ");
        
        return view($this->view,compact(['REQUEST_DATA','FormId','objRights','objDataList','DATA_STATUS']));
    }

    public function add(){       
        
        $Status         =   "A";
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $objlastdt      =   $this->getLastdt();
        $objMachineList =   $this->getMachineList();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_PM_SCHEDULE',
            'HDR_ID'=>'PMSL_ID',
            'HDR_DOC_NO'=>'PMSL_NO',
            'HDR_DOC_DT'=>'PMSL_DATE'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
       
         
                    
        $FormId  = $this->form_id;

        return view($this->view.'add', compact(['FormId','objlastdt','objMachineList','doc_req','docarray']));       
    }

    public function getLastdt(){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        return  DB::select('SELECT MAX(PMSL_DATE) PMSL_DATE FROM TBL_TRN_PM_SCHEDULE  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
    }

    public function save(Request $request) {

        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
  
        $req_data=array();
        for ($i=0; $i<=$r_count1; $i++){

            if(isset($request['PMSL_FROM_DATE_'.$i])){

                $req_data[$i] = [
                    'PMSL_FROM_DATE'    => $request['PMSL_FROM_DATE_'.$i],
                    'PMSL_TO_DATE'      => $request['PMSL_TO_DATE_'.$i],
                    'SPECIAL_INST'      => $request['SPECIAL_INST_'.$i],
                ];

            }
        }

        if(isset($req_data)) { 
            $wrapped_links["SCH"] = $req_data; 
            $XMLSCH = ArrayToXml::convert($wrapped_links);
        }
        else {
            $XMLSCH = NULL; 
        } 


        $req_dat2=array();
        for ($i=0; $i<=$r_count2; $i++){
           
            if(isset($request['SPARE_PART_NAME_'.$i])){
                $req_dat2[$i] = [
                    'SPARE_PART_NAME'     => $request['SPARE_PART_NAME_'.$i],
                ];
            }  
        }

        if(isset($req_dat2)) { 
            $wrapped_links2["CONSUME"] = $req_dat2;
            $XMLCONSUME = ArrayToXml::convert($wrapped_links2);
        }
        else {
            $XMLCONSUME = NULL; 
        } 


        $VTID_REF     =   $this->vtid_ref;
        
        $USERID_REF = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $PMSL_NO            =   $request['PMSL_NO'] !=""?$request['PMSL_NO']:NULL;
        $PMSL_DATE          =   $request['PMSL_DATE'] !=""?$request['PMSL_DATE']:NULL;
        $MACHINEID_REF      =   $request['MACHINEID_REF'] !=""?$request['MACHINEID_REF']:NULL;
        $REMARKS            =   $request['REMARKS'] !=""?$request['REMARKS']:NULL;
        $FLEXIBLE_SCHEDULE  =   $request['FLEXIBLE_SCHEDULE'] !=""?$request['FLEXIBLE_SCHEDULE']:NULL;

        $log_data = [ 
            $PMSL_NO,               $PMSL_DATE,     $MACHINEID_REF,     $REMARKS,           $FLEXIBLE_SCHEDULE,   
            $XMLCONSUME,            $XMLSCH,        $CYID_REF,          $BRID_REF,          $FYID_REF,      
            $VTID_REF,              $USERID_REF,    Date('Y-m-d'),      Date('h:i:s.u'),    $ACTIONNAME,            
            $IPADDRESS  
        ];


        $sp_result = DB::select('EXEC SP_TRN_PM_SCHEDULE_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?', $log_data);     
        
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');

        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        
        exit();   
     }

    public function edit($id=NULL){  
         
        if(!is_null($id)){
        
            $Status         =   "A";
            $CYID_REF       =   Auth::user()->CYID_REF;
            $BRID_REF       =   Session::get('BRID_REF');
            $FYID_REF       =   Session::get('FYID_REF');

            $objRights      =   DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                                
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();

            $objlastdt      =   $this->getLastdt();
            $objMachineList =   $this->getMachineList();

            $HDR            =   DB::select("SELECT 
                                T1.*,
                                T2.MACHINE_NO,T2.MACHINE_DESC
                                FROM TBL_TRN_PM_SCHEDULE T1
                                LEFT JOIN TBL_MST_MACHINE T2 ON T1.MACHINEID_REF=T2.MACHINEID
                                WHERE T1.PMSL_ID='$id' ")[0];

            $SCH            =   DB::select("SELECT * 
                                FROM TBL_TRN_PM_SCHEDULE_DATE
                                WHERE PMSL_ID_REF='$id' ORDER BY PMSL_ID_REF ASC ");

            $CON            =   DB::select("SELECT * 
                                FROM TBL_TRN_PM_SCHEDULE_CONSUMED
                                WHERE PMSL_ID_REF='$id' ORDER BY PMSL_ID_REF ASC ");

            $FormId  = $this->form_id;

            return view($this->view.'edit', compact(['FormId','objlastdt','objMachineList','objRights','HDR','SCH','CON']));
        
        }
    }

    public function view($id=NULL){  
         
        if(!is_null($id)){
        
            $Status         =   "A";
            $CYID_REF       =   Auth::user()->CYID_REF;
            $BRID_REF       =   Session::get('BRID_REF');
            $FYID_REF       =   Session::get('FYID_REF');

            $objRights      =   DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                                
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();

            $objlastdt      =   $this->getLastdt();
            $objMachineList =   $this->getMachineList();

            $HDR            =   DB::select("SELECT 
                                T1.*,
                                T2.MACHINE_NO,T2.MACHINE_DESC
                                FROM TBL_TRN_PM_SCHEDULE T1
                                LEFT JOIN TBL_MST_MACHINE T2 ON T1.MACHINEID_REF=T2.MACHINEID
                                WHERE T1.PMSL_ID='$id' ")[0];

            $SCH            =   DB::select("SELECT * 
                                FROM TBL_TRN_PM_SCHEDULE_DATE
                                WHERE PMSL_ID_REF='$id' ORDER BY PMSL_ID_REF ASC ");

            $CON            =   DB::select("SELECT * 
                                FROM TBL_TRN_PM_SCHEDULE_CONSUMED
                                WHERE PMSL_ID_REF='$id' ORDER BY PMSL_ID_REF ASC ");

            $FormId  = $this->form_id;

            return view($this->view.'view', compact(['FormId','objlastdt','objMachineList','objRights','HDR','SCH','CON']));
        
        }
    }

    

    public function update(Request $request){

        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
  
        $req_data=array();
        for ($i=0; $i<=$r_count1; $i++){

            if(isset($request['PMSL_FROM_DATE_'.$i])){

                $req_data[$i] = [
                    'PMSL_FROM_DATE'    => $request['PMSL_FROM_DATE_'.$i],
                    'PMSL_TO_DATE'      => $request['PMSL_TO_DATE_'.$i],
                    'SPECIAL_INST'      => $request['SPECIAL_INST_'.$i],
                ];

            }
        }

        if(isset($req_data)) { 
            $wrapped_links["SCH"] = $req_data; 
            $XMLSCH = ArrayToXml::convert($wrapped_links);
        }
        else {
            $XMLSCH = NULL; 
        } 


        $req_dat2=array();
        for ($i=0; $i<=$r_count2; $i++){
           
            if(isset($request['SPARE_PART_NAME_'.$i])){
                $req_dat2[$i] = [
                    'SPARE_PART_NAME'     => $request['SPARE_PART_NAME_'.$i],
                ];
            }  
        }

        if(isset($req_dat2)) { 
            $wrapped_links2["CONSUME"] = $req_dat2;
            $XMLCONSUME = ArrayToXml::convert($wrapped_links2);
        }
        else {
            $XMLCONSUME = NULL; 
        } 
        
       

        $VTID_REF     =   $this->vtid_ref;
        
        $USERID_REF = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $PMSL_NO            =   $request['PMSL_NO'] !=""?$request['PMSL_NO']:NULL;
        $PMSL_DATE          =   $request['PMSL_DATE'] !=""?$request['PMSL_DATE']:NULL;
        $MACHINEID_REF      =   $request['MACHINEID_REF'] !=""?$request['MACHINEID_REF']:NULL;
        $REMARKS            =   $request['REMARKS'] !=""?$request['REMARKS']:NULL;
        $FLEXIBLE_SCHEDULE  =   $request['FLEXIBLE_SCHEDULE'] !=""?$request['FLEXIBLE_SCHEDULE']:NULL;

        $log_data = [ 
            $PMSL_NO,               $PMSL_DATE,     $MACHINEID_REF,     $REMARKS,           $FLEXIBLE_SCHEDULE,   
            $XMLCONSUME,            $XMLSCH,        $CYID_REF,          $BRID_REF,          $FYID_REF,      
            $VTID_REF,              $USERID_REF,    Date('Y-m-d'),      Date('h:i:s.u'),    $ACTIONNAME,            
            $IPADDRESS  
        ];


        $sp_result = DB::select('EXEC SP_TRN_PM_SCHEDULE_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?', $log_data);  
        
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $PMSL_NO. ' Sucessfully Updated.']);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();  
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
           
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
  
        $req_data=array();
        for ($i=0; $i<=$r_count1; $i++){

            if(isset($request['PMSL_FROM_DATE_'.$i])){

                $req_data[$i] = [
                    'PMSL_FROM_DATE'    => $request['PMSL_FROM_DATE_'.$i],
                    'PMSL_TO_DATE'      => $request['PMSL_TO_DATE_'.$i],
                    'SPECIAL_INST'      => $request['SPECIAL_INST_'.$i],
                ];

            }
        }

        if(isset($req_data)) { 
            $wrapped_links["SCH"] = $req_data; 
            $XMLSCH = ArrayToXml::convert($wrapped_links);
        }
        else {
            $XMLSCH = NULL; 
        } 


        $req_dat2=array();
        for ($i=0; $i<=$r_count2; $i++){
           
            if(isset($request['SPARE_PART_NAME_'.$i])){
                $req_dat2[$i] = [
                    'SPARE_PART_NAME'     => $request['SPARE_PART_NAME_'.$i],
                ];
            }  
        }

        if(isset($req_dat2)) { 
            $wrapped_links2["CONSUME"] = $req_dat2;
            $XMLCONSUME = ArrayToXml::convert($wrapped_links2);
        }
        else {
            $XMLCONSUME = NULL; 
        } 
        
        

        $VTID_REF     =   $this->vtid_ref;
        
        $USERID_REF = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');


        $PMSL_NO            =   $request['PMSL_NO'] !=""?$request['PMSL_NO']:NULL;
        $PMSL_DATE          =   $request['PMSL_DATE'] !=""?$request['PMSL_DATE']:NULL;
        $MACHINEID_REF      =   $request['MACHINEID_REF'] !=""?$request['MACHINEID_REF']:NULL;
        $REMARKS            =   $request['REMARKS'] !=""?$request['REMARKS']:NULL;
        $FLEXIBLE_SCHEDULE  =   $request['FLEXIBLE_SCHEDULE'] !=""?$request['FLEXIBLE_SCHEDULE']:NULL;

        $log_data = [ 
            $PMSL_NO,               $PMSL_DATE,     $MACHINEID_REF,     $REMARKS,           $FLEXIBLE_SCHEDULE,   
            $XMLCONSUME,            $XMLSCH,        $CYID_REF,          $BRID_REF,          $FYID_REF,      
            $VTID_REF,              $USERID_REF,    Date('Y-m-d'),      Date('h:i:s.u'),    $ACTIONNAME,            
            $IPADDRESS  
        ];


        $sp_result = DB::select('EXEC SP_TRN_PM_SCHEDULE_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?', $log_data);      
        
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $PMSL_NO. ' Sucessfully Approved.']);

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
    
            if(!empty($sp_listing_result))
                {
                    foreach ($sp_listing_result as $key=>$salesenquiryitem)
                {  
                    $record_status = 0;
                    $Approvallevel = "APPROVAL".$salesenquiryitem->LAVELS;
                }
                }
            


                
                $req_data =  json_decode($request['ID']);

              
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
                $VTID_REF   =   $this->vtid_ref; 
                $CYID_REF   =   Auth::user()->CYID_REF;
                $BRID_REF   =   Session::get('BRID_REF');
                $FYID_REF   =   Session::get('FYID_REF');       
                $TABLE      =   "TBL_TRN_PM_SCHEDULE";
                $FIELD      =   "PMSL_ID";
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
        $TABLE      =   "TBL_TRN_PM_SCHEDULE";
        $FIELD      =   "PMSL_ID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_PM_SCHEDULE_DATE',
        ];

        $req_data[1]=[
            'NT'  => 'TBL_TRN_PM_SCHEDULE_CONSUMED',
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

            $objResponse = DB::table('TBL_TRN_PM_SCHEDULE')->where('PMSL_ID','=',$id)->first();

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

            return view($this->view.'attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments']));
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
        
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/Preventive_Maintenance_Schedule";
		
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
  
    public function checkExist(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $PMSL_NO    =   $request->PMSL_NO;
        
        $objExit    =   DB::table('TBL_TRN_PM_SCHEDULE')
                        ->where('TBL_TRN_PM_SCHEDULE.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_TRN_PM_SCHEDULE.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_TRN_PM_SCHEDULE.FYID_REF','=',Session::get('FYID_REF'))
                        ->where('TBL_TRN_PM_SCHEDULE.PMSL_NO','=',$PMSL_NO)
                        ->select('TBL_TRN_PM_SCHEDULE.PMSL_NO')
                        ->first();
        
        if($objExit){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate Schedule NO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }

    public function getMachineList(){
        return DB::table('TBL_MST_MACHINE')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('MACHINEID','MACHINE_NO','MACHINE_DESC')
            ->get();
    }



    

    
}
