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

class TrnFrm365Controller extends Controller{
    protected $form_id  =   365;
    protected $vtid_ref =   450;
    protected $view     =   "transactions.PlantMaintenance.Break_down_Solution.trnfrm";
   
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

        // $objDataList	=	DB::select("SELECT * FROM TBL_TRN_JWC_HDR WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF'  ORDER BY JWCID DESC");

        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList	=	DB::select("select hdr.BDSL_ID,hdr.BDSL_NO,hdr.BDSL_DATE,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.BDSL_ID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                            ) AS CREATED_BY,
                            hdr.STATUS,hdr.BREAKDOWN_STATUS, sl.BDCL_NO,sl.BDCL_DATE,sl.COMPLAINT_BY,
                            sl.COMPLAINT_TO,
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
                            inner join TBL_TRN_BD_SOLUTION hdr
                            on a.VID = hdr.BDSL_ID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_TRN_BD_COMPLAINT_LOG sl ON hdr.BDCL_ID_REF = sl.BDCL_ID  
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.BDSL_ID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );

                            // /dd($objDataList); 

        return view($this->view.$FormId,compact(['REQUEST_DATA','objRights','objDataList','FormId']));
    }


    public function getComplaintNo(){

        return DB::table('TBL_TRN_BD_COMPLAINT_LOG')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('STATUS','=','A')
        ->select('BDCL_ID','BDCL_NO','BDCL_DATE','BDCL_TIME')
        ->get();
    }

    public function getDetailsforComplaintNo(Request $request){

        return DB::table('TBL_TRN_BD_COMPLAINT_LOG')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('STATUS','=','A')
        ->where('BDCL_ID','=',$request['BDCL_ID'])
        ->select('*')
        ->get();
    }

    public function getChecklistNo(Request $request){

        $Status     =   "A";
        $CYID_REF               =   Auth::user()->CYID_REF;
        $BRID_REF               =   Session::get('BRID_REF');
        $MACHINEID_REFE         =   $request['MACHINEID_REF'];
    
        $sp_popup = [
            $CYID_REF, $BRID_REF,$MACHINEID_REF
        ]; 
        
        $ObjData = DB::select('EXEC SP_TRN_GET_CHECKLIST ?,?,?', $sp_popup);
    
        if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){
    
                $CKLISTID           =   $dataRow->CKLISTID;
                $CHECKLIST_NO       =   $dataRow->CHECKLIST_NO;
                $CHECKLIST_DT       =   $dataRow->CHECKLIST_DT;
                $CHECKLIST_DESC     =   $dataRow->CHECKLIST_DESC;
                
               
                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_CKLISTID_REF[]" id="cklistidcode_'.$index.'"  class="clscklistid" value="'.$CKLISTID.'" ></td>
                <td class="ROW2">'.$CHECKLIST_NO.'-'.$CHECKLIST_DESC.'<input type="hidden" id="txtvendoridcode_'.$index.'" data-desc="'.$CHECKLIST_NO.'" value="'.$CKLISTID.'" > </td>
                <td class="ROW3">'.$CHECKLIST_DT.'</td>
                </tr>';
    
                echo $row;
    
            }
    
        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();
    
    }

    public function add(){  

        $FormId     =   $this->form_id;
        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');  
        
        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_BD_SOLUTION',
            'HDR_ID'=>'BDSL_ID',
            'HDR_DOC_NO'=>'BDSL_NO',
            'HDR_DOC_DT'=>'BDSL_DATE'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

          
        
        $objBDComplaints = DB::table('TBL_TRN_BD_COMPLAINT_LOG')            
        ->where('TBL_TRN_BD_COMPLAINT_LOG.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_BD_COMPLAINT_LOG.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_BD_COMPLAINT_LOG.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_BD_COMPLAINT_LOG.STATUS','=',$Status)
        ->leftJoin('TBL_MST_DEPARTMENT', 'TBL_TRN_BD_COMPLAINT_LOG.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID')
        ->leftJoin('TBL_MST_PRIORITY', 'TBL_TRN_BD_COMPLAINT_LOG.PRIORITYID_REF','=','TBL_MST_PRIORITY.PRIORITYID')
        ->leftJoin('TBL_MST_MACHINE', 'TBL_TRN_BD_COMPLAINT_LOG.MACHINEID_REF','=','TBL_MST_MACHINE.MACHINEID')
        ->leftJoin('TBL_MST_BREAKDOWN_REASON AS REASON1', 'TBL_TRN_BD_COMPLAINT_LOG.BD_REASONID_REF1','=','REASON1.BD_REASONID')
        ->leftJoin('TBL_MST_BREAKDOWN_REASON AS REASON2', 'TBL_TRN_BD_COMPLAINT_LOG.BD_REASONID_REF2','=','REASON2.BD_REASONID')
        ->select('TBL_TRN_BD_COMPLAINT_LOG.*', 'TBL_MST_DEPARTMENT.DCODE AS DEPARTMENT_CODE','TBL_MST_DEPARTMENT.NAME AS DEPARTMENT_NAME','TBL_MST_PRIORITY.PRIORITYCODE','TBL_MST_PRIORITY.DESCRIPTIONS AS PRIORITY_DESC','TBL_MST_MACHINE.MACHINE_NO','TBL_MST_MACHINE.MACHINE_DESC','REASON1.BD_REASON_CODE AS REASON1_CODE','REASON1.BD_REASON_DESC AS REASON1_DESC','REASON2.BD_REASON_CODE AS REASON2_CODE','REASON2.BD_REASON_DESC AS REASON2_DESC')
        ->get();

       

        $objlastdt          =   $this->getLastdt();


        $objEmployee = DB::table('TBL_MST_EMPLOYEE')
        ->where('STATUS','=',$Status)
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Auth::user()->BRID_REF)
        ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
        ->select('TBL_MST_EMPLOYEE.*')
        ->get();

        
      

        return view($this->view.$FormId.'add',compact(['FormId','objBDComplaints','objlastdt','objEmployee','doc_req','docarray']));

    }

    public function getchecklists(Request $request){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;

        $cur_date = Date('Y-m-d');
        $ObjData = DB::select('select * from TBL_MST_MAINTENANCE_CHECKLIST  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?) and CYID_REF = ?   and STATUS = ? ',  [$cur_date,$CYID_REF,'A']);
                        
        if(!empty($ObjData)){

        foreach ($ObjData as $index=>$dataRow){


            $row = '';
            $row = $row.'<tr >
            <td style="width:10%;" align="center"> <input type="checkbox" name="SELECT_CKLISTID_REF[]" id="chklist_'.$dataRow->CKLISTID .'"  class="clschecklist" value="'.$dataRow->CKLISTID.'" ></td>
            <td style="width:40%;">'.$dataRow->CHECKLIST_NO;
            $row = $row.'<input type="hidden" id="txtchklist_'.$dataRow->CKLISTID.'" data-desc="'.$dataRow->CHECKLIST_NO .'" data-ccname="'.$dataRow->CHECKLIST_DESC.'" value="'.$dataRow->CKLISTID.'"/></td>';
            $row = $row.'<td style="width:50%;">'.$dataRow->CHECKLIST_DESC.'</td>';
            $row = $row.'</tr>';
            echo $row;

        }
        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();

    }

    public function get_employee(Request $request){

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');

        $EMP_TYPE       =   $request['EMP_TYPE'];
    


        $ObjData = DB::table('TBL_MST_EMPLOYEE')
        ->where('STATUS','=',$Status)
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Auth::user()->BRID_REF)
        ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
        ->select('TBL_MST_EMPLOYEE.*')
        ->get();

        if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){

              
               
                $row = '';
                $row = $row.'<tr >
                <td style="width:10%; text-align:center;"> <input type="checkbox" name="'.$EMP_TYPE.'[]" id="empcode'.$index.'"  class="clsspid_priority" value="'.$dataRow->EMPID.'" ></td>
                <td style="width:30%;">'.$dataRow->EMPCODE.'<input type="hidden" id="txtempcode'.$index.'" data-desc="'.$dataRow->EMPCODE.'-'.$dataRow->FNAME.'" value="'.$dataRow->EMPID.'" > </td>
                <td style="width:60%;">'.$dataRow->FNAME.'</td>
                </tr>';

                echo $row;

            }
    
        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();
    

    }


    public function get_checklist_data(Request $request){

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $id       =   $request['id'];

        $ObjData = DB::table('TBL_MST_MACHINE_CHECKLIST_DETAILS')
        ->where('TBL_MST_MACHINE_CHECKLIST_DETAILS.MCKLISTID_REF','=',$id)
        ->leftJoin('TBL_MST_MAINTENANCE_PARAMETER',   'TBL_MST_MAINTENANCE_PARAMETER.MPID','=',   'TBL_MST_MACHINE_CHECKLIST_DETAILS.MPID_REF')
        ->leftJoin('TBL_MST_MAINTENANCE_SUB_PARAMETER',   'TBL_MST_MAINTENANCE_SUB_PARAMETER.MSPID','=',   'TBL_MST_MACHINE_CHECKLIST_DETAILS.MSPID_REF')
        ->select('TBL_MST_MACHINE_CHECKLIST_DETAILS.*', 
        'TBL_MST_MAINTENANCE_PARAMETER.MPID','TBL_MST_MAINTENANCE_PARAMETER.MP_CODE','TBL_MST_MAINTENANCE_PARAMETER.MP_DESC',        
        'TBL_MST_MAINTENANCE_SUB_PARAMETER.MSPID','TBL_MST_MAINTENANCE_SUB_PARAMETER.MSP_CODE','TBL_MST_MAINTENANCE_SUB_PARAMETER.MSP_DESC')
        ->orderBy('TBL_MST_MACHINE_CHECKLIST_DETAILS.MCKLIST_DID', 'ASC')
        ->get();    

       // dd($ObjData); 



        if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){            
       
               // $tbody = '';
                   $tbody= '<tr  class="participantRow" >
                         <td hidden><input type="text" name="checklistcount[]"  > </td>
           
                         <td hidden><input type="text" name="MCKLIST_DID[]" id="MCKLISTID_REF_'.$index.'" value="'.$dataRow->MCKLISTID_REF.'" > </td>
                                                                 
                         <td><input type="text" name="MP_CODE[]"  class="form-control" value="'.$dataRow->MP_CODE.'"  autocomplete="off"  readonly  /></td>
          
                         <td><input type="text" name="MP_DESC[]"  class="form-control" value="'.$dataRow->MP_DESC.'"  autocomplete="off"  readonly  /></td>
          
                         <td><input type="text" name="MSP_CODE[]"  class="form-control" value="'.$dataRow->MSP_CODE.'"  autocomplete="off"  readonly  /></td>
          
                         <td><input type="text" name="MSP_DESC[]"  class="form-control" value="'.$dataRow->MSP_DESC.'"  autocomplete="off"  readonly  /></td>      
           
                       <td><input type="text" name="STANDARD_VALUE[]"   value="'.$dataRow->VALUE.'" class="form-control three-digits" maxlength="15"  autocomplete="off"  readonly/></td>

                       <td><input type="text" name="ACTUAL_VALUE[]" id="ACTUAL_VALUE_'.$index.'"   value="" class="form-control three-digits" maxlength="15"  autocomplete="off"   /></td>

                       <td><input type="text" name="REMARKS[]"  class="form-control" value=""  autocomplete="off"    /></td>


                   
                 
                         </tr>';

                         echo $tbody;

            }
    
        }else{
           
        }
        exit();
    
    }

    public function codeduplicate(Request $request){

        $ST_ADJUST_DOCNO  =   trim($request['BDSL_NO']);
        $objLabel = DB::table('TBL_TRN_BD_SOLUTION')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('BDSL_NO','=',$ST_ADJUST_DOCNO)
        ->select('BDSL_ID')->first();

        if($objLabel){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }












    public function save(Request $request) {
        


        $r_count_checklist = count($request['checklistcount']);

        $r_count = $request['Row_Count'];
        $r_count1 = $request['Row_Count1'];

       
   
       
        for ($i=0; $i<=$r_count_checklist; $i++)
        {
            if(isset($request['MCKLIST_DID'][$i]))
            {
                $req_data[$i] = [
                    'MCKLIST_DID_REF'    => $request['MCKLIST_DID'][$i],
                    'STANDARD_VALUE' => $request['STANDARD_VALUE'][$i],
                    'ACTUAL_VALUE' => $request['ACTUAL_VALUE'][$i],                    
                    'Chk_Remarks' => $request['REMARKS'][$i],                    
                 
                ];
            }
        }

        $wrapped_links["CHKLIST"] = $req_data; 
        $XMLCHKLIST = ArrayToXml::convert($wrapped_links);


        
        for ($i=0; $i<=$r_count; $i++){
            if(isset($request['SPARE_PARTS_CONSUMED_'.$i]) && !is_null($request['SPARE_PARTS_CONSUMED_'.$i]))
            {
                if(isset($request['SPARE_PARTS_CONSUMED_'.$i]))
                {
                    $reqdata2[$i] = [
                        'SPARE_PART_NAME'    => $request['SPARE_PARTS_CONSUMED_'.$i],          
                    ];
                }
            }
            
        }
  

        if(isset($reqdata2)) { 
            $wrapped_links2["CONSUME"] = $reqdata2;
            $XMLCONSUME = ArrayToXml::convert($wrapped_links2);
        }
        else {
            $XMLCONSUME = NULL; 
        } 

        
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['SPARE_PART_NAME_'.$i]) && !is_null($request['SPARE_PART_NAME_'.$i]))
            {
                if(isset($request['SPARE_PART_NAME_'.$i]))
                {
                    $reqdata3[$i] = [
                        'SPARE_PART_NAME'    => $request['SPARE_PART_NAME_'.$i],
                        'SPARE_RETURN_TO'         => $request['SPARE_RETURN_TO_'.$i],
                    ];
                }
            }
            
        }
       // dd($reqdata3);
        if(isset($reqdata3)) { 
            $wrapped_links3["RETURN"] = $reqdata3;
            $XMLRETURN = ArrayToXml::convert($wrapped_links3);
        }
        else {
            $XMLRETURN = NULL; 
        } 
        
     



        
        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $BDSL_NO               =   $request['BDSL_NO'];
        $BDSL_DATE             =   $request['BDSL_DATE'];
        $BDCL_ID_REF           =   $request['BDCLID_REF'];    

        $EMP_TYPE     = $request['EMPLOYEE_TYPE'];
        if($EMP_TYPE=='own_employee'){
        $OWN_EMPLOYEE=1;
        $OUTSIDE_RESOURCE=0;
        }else{
        $OWN_EMPLOYEE=0;
        $OUTSIDE_RESOURCE=1;
        }


        $OUTSIDE_RNAME                  =   $request['Outsider_Name'];
        $OUTSIDE_COMPANY                =   $request['Company_Name'];
        $OUTSIDE_CONTACT_NO             =   $request['Contact_Number'];
        $MAINTENANCE_EMPID_REF1         =   $request['EMP_REF1'];
        $MAINTENANCE_EMPID_REF2         =   $request['EMP_REF2'];
        $MAINTENANCE_EMPID_REF3         =   $request['EMP_REF3'];
        $MAINTENANCE_START_DATE         =   $request['Main_Start_Date'];
        $MAINTENANCE_START_TIME         =   $request['Main_Start_Time'];
        $FAULT_DETECT1                  =   $request['Fault_Detect_1'];
        $FAULT_DETECT2                  =   $request['Fault_Detect_2'];
        $MCKLISTID_REF                  =   $request['CHECKLIST_REF'];
        $ACTION_TAKEN1                  =   $request['Action_Taken1'];
        $ACTION_TAKEN2                  =   $request['Action_Taken2'];
        $BREAKDOWN_STATUS               =   $request['drpstatus'];
        $MAINTENANCE_END_DATE           =   $request['Main_End_Date'];
        $MAINTENANCE_END_TIME           =   $request['Main_End_Time'];
        $REMARKS_PENDING                =   $request['REMARKS_PENDING'];


       
        $log_data = [ 
            $BDSL_NO,                                      $BDSL_DATE,                              $BDCL_ID_REF,                    $OWN_EMPLOYEE,                  $OUTSIDE_RESOURCE,       
            $OUTSIDE_RNAME,                                $OUTSIDE_COMPANY,                        $OUTSIDE_CONTACT_NO,             $MAINTENANCE_EMPID_REF1,        $MAINTENANCE_EMPID_REF2,
            $MAINTENANCE_EMPID_REF3,                       $MAINTENANCE_START_DATE,                 $MAINTENANCE_START_TIME,         $FAULT_DETECT1,                 $FAULT_DETECT2,
            $MCKLISTID_REF,                                $ACTION_TAKEN1,                          $ACTION_TAKEN2,                  $BREAKDOWN_STATUS,              $MAINTENANCE_END_DATE,
            $MAINTENANCE_END_TIME,                         $REMARKS_PENDING,                        $XMLCONSUME,                     $XMLRETURN,                     $XMLCHKLIST,
            $CYID_REF,                                     $BRID_REF,                               $FYID_REF,                       $VTID_REF,                      $USERID,
            Date('Y-m-d'),                                 Date('h:i:s.u'),                         $ACTIONNAME,                     $IPADDRESS
        ];


        //dd($log_data); 

        
        $sp_result = DB::select('EXEC SP_TRN_BD_COMPLAINT_SOLUTION_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,? ', $log_data); 
        
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


            $FormId     =   $this->form_id;
   
    
            $objBDS =   DB::table('TBL_MST_DOCNO_DEFINITION')
            ->where('VTID_REF','=',$this->vtid_ref)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('FYID_REF','=',$FYID_REF)
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_DOCNO_DEFINITION.*')
            ->first();
            
            if($objBDS->SYSTEM_GRSR == "1")
            {
                if($objBDS->PREFIX_RQ == "1")
                {
                    $objBDSO = $objBDS->PREFIX;
                }        
                if($objBDS->PRE_SEP_RQ == "1")
                {
                    if($objBDS->PRE_SEP_SLASH == "1")
                    {
                    $objBDSO = $objBDSO.'/';
                    }
                    if($objBDS->PRE_SEP_HYPEN == "1")
                    {
                    $objBDSO = $objBDSO.'-';
                    }
                }        
                if($objBDS->NO_MAX)
                {   
                    $objBDSO = $objBDSO.str_pad($objBDS->LAST_RECORDNO+1, $objBDS->NO_MAX, "0", STR_PAD_LEFT);
                }
                
                if($objBDS->NO_SEP_RQ == "1")
                {
                    if($objBDS->NO_SEP_SLASH == "1")
                    {
                    $objBDSO = $objBDSO.'/';
                    }
                    if($objBDS->NO_SEP_HYPEN == "1")
                    {
                    $objBDSO = $objBDSO.'-';
                    }
                }
                if($objBDS->SUFFIX_RQ == "1")
                {
                    $objBDSO = $objBDSO.$objBDS->SUFFIX;
                }
            }      



            $objResponse = DB::table('TBL_TRN_BD_SOLUTION')            
            ->where('TBL_TRN_BD_SOLUTION.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_BD_SOLUTION.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_BD_SOLUTION.FYID_REF','=',Session::get('FYID_REF'))
			->where('TBL_TRN_BD_SOLUTION.BDSL_ID','=',$id)            
            ->leftJoin('TBL_MST_EMPLOYEE AS EMP1', 'TBL_TRN_BD_SOLUTION.MAINTENANCE_EMPID_REF1','=','EMP1.EMPID')
            ->leftJoin('TBL_MST_EMPLOYEE AS EMP2', 'TBL_TRN_BD_SOLUTION.MAINTENANCE_EMPID_REF2','=','EMP2.EMPID')
            ->leftJoin('TBL_MST_EMPLOYEE AS EMP3', 'TBL_TRN_BD_SOLUTION.MAINTENANCE_EMPID_REF3','=','EMP3.EMPID')
           // ->leftJoin(' TBL_MST_MAINTENANCE_CHECKLIST', 'TBL_TRN_BD_SOLUTION.MCKLISTID_REF','=','TBL_MST_MAINTENANCE_CHECKLIST.CKLISTID')
      ->select('TBL_TRN_BD_SOLUTION.*','EMP1.EMPCODE AS EMP1_CODE','EMP1.FNAME AS EMP1_NAME','EMP2.EMPCODE AS EMP2_CODE','EMP2.FNAME AS EMP2_NAME','EMP3.EMPCODE AS EMP3_CODE','EMP3.FNAME AS EMP3_NAME')
            ->first();
            //dd($objResponse);

            if(isset($objResponse->MAINTENANCE_START_TIME)){
            $TIME_DATA=explode(':',$objResponse->MAINTENANCE_START_TIME);
            $START_TIME=$TIME_DATA[0].':'.$TIME_DATA[1];
            }else{
            $START_TIME='';
            }

            if(isset($objResponse->MAINTENANCE_END_TIME)){
            $TIME_DATA=explode(':',$objResponse->MAINTENANCE_END_TIME);
            $END_TIME=$TIME_DATA[0].':'.$TIME_DATA[1];
            }else{
            $END_TIME='';
            }



            $objResponsechecklists = DB::table('TBL_MST_MAINTENANCE_CHECKLIST')            
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('FYID_REF','=',Session::get('FYID_REF'))
			->where('CKLISTID','=',$objResponse->MCKLISTID_REF)         
            ->select('CHECKLIST_NO','CHECKLIST_DESC')
            ->first();

            $CHECKLIST= DB::table('TBL_TRN_BD_SOLUTION_CHECKLIST')
            ->where('TBL_TRN_BD_SOLUTION_CHECKLIST.BDSL_ID_REF','=',$id)
            ->leftJoin('TBL_MST_MACHINE_CHECKLIST_DETAILS', 'TBL_TRN_BD_SOLUTION_CHECKLIST.MCKLIST_DID_REF','=','TBL_MST_MACHINE_CHECKLIST_DETAILS.MCKLISTID_REF')
            ->leftJoin('TBL_MST_MAINTENANCE_PARAMETER',   'TBL_MST_MAINTENANCE_PARAMETER.MPID','=',   'TBL_MST_MACHINE_CHECKLIST_DETAILS.MPID_REF')
            ->leftJoin('TBL_MST_MAINTENANCE_SUB_PARAMETER',   'TBL_MST_MAINTENANCE_SUB_PARAMETER.MSPID','=',   'TBL_MST_MACHINE_CHECKLIST_DETAILS.MSPID_REF')
            ->select('TBL_MST_MACHINE_CHECKLIST_DETAILS.*','TBL_TRN_BD_SOLUTION_CHECKLIST.*', 'TBL_MST_MAINTENANCE_PARAMETER.MPID','TBL_MST_MAINTENANCE_PARAMETER.MP_CODE',
                     'TBL_MST_MAINTENANCE_PARAMETER.MP_DESC','TBL_MST_MAINTENANCE_SUB_PARAMETER.MSPID','TBL_MST_MAINTENANCE_SUB_PARAMETER.MSP_CODE','TBL_MST_MAINTENANCE_SUB_PARAMETER.MSP_DESC')
             ->get();
            // dd($CHECKLIST); 

            $RETURNLIST= DB::table('TBL_TRN_BD_SOLUTION_RETURN')
            ->where('BDSL_ID_REF','=',$id)
            ->select('TBL_TRN_BD_SOLUTION_RETURN.*')
            ->get()->toArray();

         //  dd($RETURNLIST); 

            $CONSUMELIST= DB::table('TBL_TRN_BD_SOLUTION_CONSUMED')
            ->where('BDSL_ID_REF','=',100)
            ->select('TBL_TRN_BD_SOLUTION_CONSUMED.*')
            ->get()->toArray();

           // dd($CONSUMELIST); 
            
            $objResponseBDComplaints = DB::table('TBL_TRN_BD_COMPLAINT_LOG')            
            ->where('TBL_TRN_BD_COMPLAINT_LOG.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_BD_COMPLAINT_LOG.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_BD_COMPLAINT_LOG.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_BD_COMPLAINT_LOG.STATUS','=',$Status)
            ->where('TBL_TRN_BD_COMPLAINT_LOG.BDCL_ID','=',$objResponse->BDCL_ID_REF)
            ->leftJoin('TBL_MST_DEPARTMENT', 'TBL_TRN_BD_COMPLAINT_LOG.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID')
            ->leftJoin('TBL_MST_PRIORITY', 'TBL_TRN_BD_COMPLAINT_LOG.PRIORITYID_REF','=','TBL_MST_PRIORITY.PRIORITYID')
            ->leftJoin('TBL_MST_MACHINE', 'TBL_TRN_BD_COMPLAINT_LOG.MACHINEID_REF','=','TBL_MST_MACHINE.MACHINEID')
            ->leftJoin('TBL_MST_BREAKDOWN_REASON AS REASON1', 'TBL_TRN_BD_COMPLAINT_LOG.BD_REASONID_REF1','=','REASON1.BD_REASONID')
            ->leftJoin('TBL_MST_BREAKDOWN_REASON AS REASON2', 'TBL_TRN_BD_COMPLAINT_LOG.BD_REASONID_REF2','=','REASON2.BD_REASONID')
            ->select('TBL_TRN_BD_COMPLAINT_LOG.*', 'TBL_MST_DEPARTMENT.DCODE AS DEPARTMENT_CODE','TBL_MST_DEPARTMENT.NAME AS DEPARTMENT_NAME','TBL_MST_PRIORITY.PRIORITYCODE','TBL_MST_PRIORITY.DESCRIPTIONS AS PRIORITY_DESC','TBL_MST_MACHINE.MACHINE_NO','TBL_MST_MACHINE.MACHINE_DESC','REASON1.BD_REASON_CODE AS REASON1_CODE','REASON1.BD_REASON_DESC AS REASON1_DESC','REASON2.BD_REASON_CODE AS REASON2_CODE','REASON2.BD_REASON_DESC AS REASON2_DESC')
            ->first();


        //  dd($objResponseBDComplaints); 
    
            $objBDComplaints = DB::table('TBL_TRN_BD_COMPLAINT_LOG')            
            ->where('TBL_TRN_BD_COMPLAINT_LOG.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_BD_COMPLAINT_LOG.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_BD_COMPLAINT_LOG.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_BD_COMPLAINT_LOG.STATUS','=',$Status)
            ->leftJoin('TBL_MST_DEPARTMENT', 'TBL_TRN_BD_COMPLAINT_LOG.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID')
            ->leftJoin('TBL_MST_PRIORITY', 'TBL_TRN_BD_COMPLAINT_LOG.PRIORITYID_REF','=','TBL_MST_PRIORITY.PRIORITYID')
            ->leftJoin('TBL_MST_MACHINE', 'TBL_TRN_BD_COMPLAINT_LOG.MACHINEID_REF','=','TBL_MST_MACHINE.MACHINEID')
            ->leftJoin('TBL_MST_BREAKDOWN_REASON AS REASON1', 'TBL_TRN_BD_COMPLAINT_LOG.BD_REASONID_REF1','=','REASON1.BD_REASONID')
            ->leftJoin('TBL_MST_BREAKDOWN_REASON AS REASON2', 'TBL_TRN_BD_COMPLAINT_LOG.BD_REASONID_REF2','=','REASON2.BD_REASONID')
            ->select('TBL_TRN_BD_COMPLAINT_LOG.*', 'TBL_MST_DEPARTMENT.DCODE AS DEPARTMENT_CODE','TBL_MST_DEPARTMENT.NAME AS DEPARTMENT_NAME','TBL_MST_PRIORITY.PRIORITYCODE','TBL_MST_PRIORITY.DESCRIPTIONS AS PRIORITY_DESC','TBL_MST_MACHINE.MACHINE_NO','TBL_MST_MACHINE.MACHINE_DESC','REASON1.BD_REASON_CODE AS REASON1_CODE','REASON1.BD_REASON_DESC AS REASON1_DESC','REASON2.BD_REASON_CODE AS REASON2_CODE','REASON2.BD_REASON_DESC AS REASON2_DESC')
            ->get();

           // DD($objBDComplaints); 
    
           
    
            $objlastdt          =   $this->getLastdt();
    
    
            $objEmployee = DB::table('TBL_MST_EMPLOYEE')
            ->where('STATUS','=',$Status)
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Auth::user()->BRID_REF)
            ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
            ->select('TBL_MST_EMPLOYEE.*')
            ->get();
    
            
          
    
            return view($this->view.$FormId.'edit',compact(['START_TIME','END_TIME','objResponsechecklists','FormId','objBDS','objBDSO','objBDComplaints','objlastdt','objEmployee','objRights','objResponseBDComplaints','objResponse','CONSUMELIST','RETURNLIST','CHECKLIST']));
        }
     
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


            $FormId     =   $this->form_id;
   
    
            $objBDS =   DB::table('TBL_MST_DOCNO_DEFINITION')
            ->where('VTID_REF','=',$this->vtid_ref)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('FYID_REF','=',$FYID_REF)
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_DOCNO_DEFINITION.*')
            ->first();
            
            if($objBDS->SYSTEM_GRSR == "1")
            {
                if($objBDS->PREFIX_RQ == "1")
                {
                    $objBDSO = $objBDS->PREFIX;
                }        
                if($objBDS->PRE_SEP_RQ == "1")
                {
                    if($objBDS->PRE_SEP_SLASH == "1")
                    {
                    $objBDSO = $objBDSO.'/';
                    }
                    if($objBDS->PRE_SEP_HYPEN == "1")
                    {
                    $objBDSO = $objBDSO.'-';
                    }
                }        
                if($objBDS->NO_MAX)
                {   
                    $objBDSO = $objBDSO.str_pad($objBDS->LAST_RECORDNO+1, $objBDS->NO_MAX, "0", STR_PAD_LEFT);
                }
                
                if($objBDS->NO_SEP_RQ == "1")
                {
                    if($objBDS->NO_SEP_SLASH == "1")
                    {
                    $objBDSO = $objBDSO.'/';
                    }
                    if($objBDS->NO_SEP_HYPEN == "1")
                    {
                    $objBDSO = $objBDSO.'-';
                    }
                }
                if($objBDS->SUFFIX_RQ == "1")
                {
                    $objBDSO = $objBDSO.$objBDS->SUFFIX;
                }
            }      



            $objResponse = DB::table('TBL_TRN_BD_SOLUTION')            
            ->where('TBL_TRN_BD_SOLUTION.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_BD_SOLUTION.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_BD_SOLUTION.FYID_REF','=',Session::get('FYID_REF'))
			->where('TBL_TRN_BD_SOLUTION.BDSL_ID','=',$id)            
            ->leftJoin('TBL_MST_EMPLOYEE AS EMP1', 'TBL_TRN_BD_SOLUTION.MAINTENANCE_EMPID_REF1','=','EMP1.EMPID')
            ->leftJoin('TBL_MST_EMPLOYEE AS EMP2', 'TBL_TRN_BD_SOLUTION.MAINTENANCE_EMPID_REF2','=','EMP2.EMPID')
            ->leftJoin('TBL_MST_EMPLOYEE AS EMP3', 'TBL_TRN_BD_SOLUTION.MAINTENANCE_EMPID_REF3','=','EMP3.EMPID')
           // ->leftJoin(' TBL_MST_MAINTENANCE_CHECKLIST', 'TBL_TRN_BD_SOLUTION.MCKLISTID_REF','=','TBL_MST_MAINTENANCE_CHECKLIST.CKLISTID')
      ->select('TBL_TRN_BD_SOLUTION.*','EMP1.EMPCODE AS EMP1_CODE','EMP1.FNAME AS EMP1_NAME','EMP2.EMPCODE AS EMP2_CODE','EMP2.FNAME AS EMP2_NAME','EMP3.EMPCODE AS EMP3_CODE','EMP3.FNAME AS EMP3_NAME')
            ->first();
            //dd($objResponse);

            if(isset($objResponse->MAINTENANCE_START_TIME)){
            $TIME_DATA=explode(':',$objResponse->MAINTENANCE_START_TIME);
            $START_TIME=$TIME_DATA[0].':'.$TIME_DATA[1];
            }else{
            $START_TIME='';
            }

            if(isset($objResponse->MAINTENANCE_END_TIME)){
            $TIME_DATA=explode(':',$objResponse->MAINTENANCE_END_TIME);
            $END_TIME=$TIME_DATA[0].':'.$TIME_DATA[1];
            }else{
            $END_TIME='';
            }



            $objResponsechecklists = DB::table('TBL_MST_MAINTENANCE_CHECKLIST')            
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('FYID_REF','=',Session::get('FYID_REF'))
			->where('CKLISTID','=',$objResponse->MCKLISTID_REF)         
      ->select('CHECKLIST_NO','CHECKLIST_DESC')
            ->first();

            $CHECKLIST= DB::table('TBL_TRN_BD_SOLUTION_CHECKLIST')
            ->where('TBL_TRN_BD_SOLUTION_CHECKLIST.BDSL_ID_REF','=',$id)
            ->leftJoin('TBL_MST_MACHINE_CHECKLIST_DETAILS', 'TBL_TRN_BD_SOLUTION_CHECKLIST.MCKLIST_DID_REF','=','TBL_MST_MACHINE_CHECKLIST_DETAILS.MCKLISTID_REF')
            ->leftJoin('TBL_MST_MAINTENANCE_PARAMETER',   'TBL_MST_MAINTENANCE_PARAMETER.MPID','=',   'TBL_MST_MACHINE_CHECKLIST_DETAILS.MPID_REF')
            ->leftJoin('TBL_MST_MAINTENANCE_SUB_PARAMETER',   'TBL_MST_MAINTENANCE_SUB_PARAMETER.MSPID','=',   'TBL_MST_MACHINE_CHECKLIST_DETAILS.MSPID_REF')
            ->select('TBL_MST_MACHINE_CHECKLIST_DETAILS.*','TBL_TRN_BD_SOLUTION_CHECKLIST.*', 'TBL_MST_MAINTENANCE_PARAMETER.MPID','TBL_MST_MAINTENANCE_PARAMETER.MP_CODE',
                     'TBL_MST_MAINTENANCE_PARAMETER.MP_DESC','TBL_MST_MAINTENANCE_SUB_PARAMETER.MSPID','TBL_MST_MAINTENANCE_SUB_PARAMETER.MSP_CODE','TBL_MST_MAINTENANCE_SUB_PARAMETER.MSP_DESC')
             ->get();
            // dd($CHECKLIST); 

            $RETURNLIST= DB::table('TBL_TRN_BD_SOLUTION_RETURN')
            ->where('BDSL_ID_REF','=',$id)
            ->select('TBL_TRN_BD_SOLUTION_RETURN.*')
            ->get()->toArray();

            $CONSUMELIST= DB::table('TBL_TRN_BD_SOLUTION_CONSUMED')
            ->where('BDSL_ID_REF','=',$id)
            ->select('TBL_TRN_BD_SOLUTION_CONSUMED.*')
            ->get()->toArray();
            
            $objResponseBDComplaints = DB::table('TBL_TRN_BD_COMPLAINT_LOG')            
            ->where('TBL_TRN_BD_COMPLAINT_LOG.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_BD_COMPLAINT_LOG.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_BD_COMPLAINT_LOG.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_BD_COMPLAINT_LOG.STATUS','=',$Status)
            ->where('TBL_TRN_BD_COMPLAINT_LOG.BDCL_ID','=',$objResponse->BDCL_ID_REF)
            ->leftJoin('TBL_MST_DEPARTMENT', 'TBL_TRN_BD_COMPLAINT_LOG.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID')
            ->leftJoin('TBL_MST_PRIORITY', 'TBL_TRN_BD_COMPLAINT_LOG.PRIORITYID_REF','=','TBL_MST_PRIORITY.PRIORITYID')
            ->leftJoin('TBL_MST_MACHINE', 'TBL_TRN_BD_COMPLAINT_LOG.MACHINEID_REF','=','TBL_MST_MACHINE.MACHINEID')
            ->leftJoin('TBL_MST_BREAKDOWN_REASON AS REASON1', 'TBL_TRN_BD_COMPLAINT_LOG.BD_REASONID_REF1','=','REASON1.BD_REASONID')
            ->leftJoin('TBL_MST_BREAKDOWN_REASON AS REASON2', 'TBL_TRN_BD_COMPLAINT_LOG.BD_REASONID_REF2','=','REASON2.BD_REASONID')
            ->select('TBL_TRN_BD_COMPLAINT_LOG.*', 'TBL_MST_DEPARTMENT.DCODE AS DEPARTMENT_CODE','TBL_MST_DEPARTMENT.NAME AS DEPARTMENT_NAME','TBL_MST_PRIORITY.PRIORITYCODE','TBL_MST_PRIORITY.DESCRIPTIONS AS PRIORITY_DESC','TBL_MST_MACHINE.MACHINE_NO','TBL_MST_MACHINE.MACHINE_DESC','REASON1.BD_REASON_CODE AS REASON1_CODE','REASON1.BD_REASON_DESC AS REASON1_DESC','REASON2.BD_REASON_CODE AS REASON2_CODE','REASON2.BD_REASON_DESC AS REASON2_DESC')
            ->first();


        //  dd($objResponseBDComplaints); 
    
            $objBDComplaints = DB::table('TBL_TRN_BD_COMPLAINT_LOG')            
            ->where('TBL_TRN_BD_COMPLAINT_LOG.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_BD_COMPLAINT_LOG.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_BD_COMPLAINT_LOG.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_BD_COMPLAINT_LOG.STATUS','=',$Status)
            ->leftJoin('TBL_MST_DEPARTMENT', 'TBL_TRN_BD_COMPLAINT_LOG.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID')
            ->leftJoin('TBL_MST_PRIORITY', 'TBL_TRN_BD_COMPLAINT_LOG.PRIORITYID_REF','=','TBL_MST_PRIORITY.PRIORITYID')
            ->leftJoin('TBL_MST_MACHINE', 'TBL_TRN_BD_COMPLAINT_LOG.MACHINEID_REF','=','TBL_MST_MACHINE.MACHINEID')
            ->leftJoin('TBL_MST_BREAKDOWN_REASON AS REASON1', 'TBL_TRN_BD_COMPLAINT_LOG.BD_REASONID_REF1','=','REASON1.BD_REASONID')
            ->leftJoin('TBL_MST_BREAKDOWN_REASON AS REASON2', 'TBL_TRN_BD_COMPLAINT_LOG.BD_REASONID_REF2','=','REASON2.BD_REASONID')
            ->select('TBL_TRN_BD_COMPLAINT_LOG.*', 'TBL_MST_DEPARTMENT.DCODE AS DEPARTMENT_CODE','TBL_MST_DEPARTMENT.NAME AS DEPARTMENT_NAME','TBL_MST_PRIORITY.PRIORITYCODE','TBL_MST_PRIORITY.DESCRIPTIONS AS PRIORITY_DESC','TBL_MST_MACHINE.MACHINE_NO','TBL_MST_MACHINE.MACHINE_DESC','REASON1.BD_REASON_CODE AS REASON1_CODE','REASON1.BD_REASON_DESC AS REASON1_DESC','REASON2.BD_REASON_CODE AS REASON2_CODE','REASON2.BD_REASON_DESC AS REASON2_DESC')
            ->get();

           // DD($objBDComplaints); 
    
           
    
            $objlastdt          =   $this->getLastdt();
    
    
            $objEmployee = DB::table('TBL_MST_EMPLOYEE')
            ->where('STATUS','=',$Status)
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Auth::user()->BRID_REF)
            ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
            ->select('TBL_MST_EMPLOYEE.*')
            ->get();
    
            
          
    
            return view($this->view.$FormId.'view',compact(['START_TIME','END_TIME','objResponsechecklists','FormId','objBDS','objBDSO','objBDComplaints','objlastdt','objEmployee','objRights','objResponseBDComplaints','objResponse','CONSUMELIST','RETURNLIST','CHECKLIST']));
        }
     
    }
     


    
    public function update(Request $request){

      
        $r_count_checklist = count($request['checklistcount']);

        $r_count = $request['Row_Count'];
        $r_count1 = $request['Row_Count1'];

       
   
       
        for ($i=0; $i<=$r_count_checklist; $i++)
        {
            if(isset($request['MCKLIST_DID'][$i]))
            {
                $req_data[$i] = [
                    'MCKLIST_DID_REF'    => $request['MCKLIST_DID'][$i],
                    'STANDARD_VALUE' => $request['STANDARD_VALUE'][$i],
                    'ACTUAL_VALUE' => $request['ACTUAL_VALUE'][$i],                    
                    'Chk_Remarks' => $request['REMARKS'][$i],                    
                 
                ];
            }
        }

        $wrapped_links["CHKLIST"] = $req_data; 
        $XMLCHKLIST = ArrayToXml::convert($wrapped_links);


        
        for ($i=0; $i<=$r_count; $i++){
            if(isset($request['SPARE_PARTS_CONSUMED_'.$i]) && !is_null($request['SPARE_PARTS_CONSUMED_'.$i]))
            {
                if(isset($request['SPARE_PARTS_CONSUMED_'.$i]))
                {
                    $reqdata2[$i] = [
                        'SPARE_PART_NAME'    => $request['SPARE_PARTS_CONSUMED_'.$i],          
                    ];
                }
            }
            
        }
  

        if(isset($reqdata2)) { 
            $wrapped_links2["CONSUME"] = $reqdata2;
            $XMLCONSUME = ArrayToXml::convert($wrapped_links2);
        }
        else {
            $XMLCONSUME = NULL; 
        } 

        
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['SPARE_PART_NAME_'.$i]) && !is_null($request['SPARE_PART_NAME_'.$i]))
            {
                if(isset($request['SPARE_PART_NAME_'.$i]))
                {
                    $reqdata3[$i] = [
                        'SPARE_PART_NAME'    => $request['SPARE_PART_NAME_'.$i],
                        'SPARE_RETURN_TO'         => $request['SPARE_RETURN_TO_'.$i],
                    ];
                }
            }
            
        }
       // dd($reqdata3);
        if(isset($reqdata3)) { 
            $wrapped_links3["RETURN"] = $reqdata3;
            $XMLRETURN = ArrayToXml::convert($wrapped_links3);
        }
        else {
            $XMLRETURN = NULL; 
        } 
        
     



        
        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $BDSL_NO               =   $request['BDSL_NO'];
        $BDSL_DATE             =   $request['BDSL_DATE'];
        $BDCL_ID_REF           =   $request['BDCLID_REF'];    

        $EMP_TYPE     = $request['EMPLOYEE_TYPE'];
        if($EMP_TYPE=='own_employee'){
        $OWN_EMPLOYEE=1;
        $OUTSIDE_RESOURCE=0;
        }else{
        $OWN_EMPLOYEE=0;
        $OUTSIDE_RESOURCE=1;
        }


        $OUTSIDE_RNAME                  =   $request['Outsider_Name'];
        $OUTSIDE_COMPANY                =   $request['Company_Name'];
        $OUTSIDE_CONTACT_NO             =   $request['Contact_Number'];
        $MAINTENANCE_EMPID_REF1         =   $request['EMP_REF1'];
        $MAINTENANCE_EMPID_REF2         =   $request['EMP_REF2'];
        $MAINTENANCE_EMPID_REF3         =   $request['EMP_REF3'];
        $MAINTENANCE_START_DATE         =   $request['Main_Start_Date'];
        $MAINTENANCE_START_TIME         =   $request['Main_Start_Time'];
        $FAULT_DETECT1                  =   $request['Fault_Detect_1'];
        $FAULT_DETECT2                  =   $request['Fault_Detect_2'];
        $MCKLISTID_REF                  =   $request['CHECKLIST_REF'];
        $ACTION_TAKEN1                  =   $request['Action_Taken1'];
        $ACTION_TAKEN2                  =   $request['Action_Taken2'];
        $BREAKDOWN_STATUS               =   $request['drpstatus'];
        $MAINTENANCE_END_DATE           =   $request['Main_End_Date'];
        $MAINTENANCE_END_TIME           =   $request['Main_End_Time'];
        $REMARKS_PENDING                =   $request['REMARKS_PENDING'];


       
        $log_data = [ 
            $BDSL_NO,                                      $BDSL_DATE,                              $BDCL_ID_REF,                    $OWN_EMPLOYEE,                  $OUTSIDE_RESOURCE,       
            $OUTSIDE_RNAME,                                $OUTSIDE_COMPANY,                        $OUTSIDE_CONTACT_NO,             $MAINTENANCE_EMPID_REF1,        $MAINTENANCE_EMPID_REF2,
            $MAINTENANCE_EMPID_REF3,                       $MAINTENANCE_START_DATE,                 $MAINTENANCE_START_TIME,         $FAULT_DETECT1,                 $FAULT_DETECT2,
            $MCKLISTID_REF,                                $ACTION_TAKEN1,                          $ACTION_TAKEN2,                  $BREAKDOWN_STATUS,              $MAINTENANCE_END_DATE,
            $MAINTENANCE_END_TIME,                         $REMARKS_PENDING,                        $XMLCONSUME,                     $XMLRETURN,                     $XMLCHKLIST,
            $CYID_REF,                                     $BRID_REF,                               $FYID_REF,                       $VTID_REF,                      $USERID,
            Date('Y-m-d'),                                 Date('h:i:s.u'),                         $ACTIONNAME,                     $IPADDRESS
        ];


        //dd($log_data); 

        
        $sp_result = DB::select('EXEC SP_TRN_BD_COMPLAINT_SOLUTION_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,? ', $log_data); 
       // dd($sp_result); 
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $BDSL_NO. ' Sucessfully Updated.']);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
    }

    //update the data
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
                foreach ($sp_listing_result as $key=>$salesenquiryitem)
            {  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$salesenquiryitem->LAVELS;
            }
            }

            $r_count_checklist = count($request['checklistcount']);

            $r_count = $request['Row_Count'];
            $r_count1 = $request['Row_Count1'];
    
           
       
           
            for ($i=0; $i<=$r_count_checklist; $i++)
            {
                if(isset($request['MCKLIST_DID'][$i]))
                {
                    $req_data[$i] = [
                        'MCKLIST_DID_REF'    => $request['MCKLIST_DID'][$i],
                        'STANDARD_VALUE' => $request['STANDARD_VALUE'][$i],
                        'ACTUAL_VALUE' => $request['ACTUAL_VALUE'][$i],                    
                        'Chk_Remarks' => $request['REMARKS'][$i],                    
                     
                    ];
                }
            }
    
            $wrapped_links["CHKLIST"] = $req_data; 
            $XMLCHKLIST = ArrayToXml::convert($wrapped_links);
    
    
            
            for ($i=0; $i<=$r_count; $i++){
                if(isset($request['SPARE_PARTS_CONSUMED_'.$i]) && !is_null($request['SPARE_PARTS_CONSUMED_'.$i]))
                {
                    if(isset($request['SPARE_PARTS_CONSUMED_'.$i]))
                    {
                        $reqdata2[$i] = [
                            'SPARE_PART_NAME'    => $request['SPARE_PARTS_CONSUMED_'.$i],          
                        ];
                    }
                }
                
            }
      
    
            if(isset($reqdata2)) { 
                $wrapped_links2["CONSUME"] = $reqdata2;
                $XMLCONSUME = ArrayToXml::convert($wrapped_links2);
            }
            else {
                $XMLCONSUME = NULL; 
            } 
    
            
            for ($i=0; $i<=$r_count1; $i++){
                if(isset($request['SPARE_PART_NAME_'.$i]) && !is_null($request['SPARE_PART_NAME_'.$i]))
                {
                    if(isset($request['SPARE_PART_NAME_'.$i]))
                    {
                        $reqdata3[$i] = [
                            'SPARE_PART_NAME'    => $request['SPARE_PART_NAME_'.$i],
                            'SPARE_RETURN_TO'         => $request['SPARE_RETURN_TO_'.$i],
                        ];
                    }
                }
                
            }
           // dd($reqdata3);
            if(isset($reqdata3)) { 
                $wrapped_links3["RETURN"] = $reqdata3;
                $XMLRETURN = ArrayToXml::convert($wrapped_links3);
            }
            else {
                $XMLRETURN = NULL; 
            } 
            
         
    
    
    
            
            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = $Approvallevel;
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
    
            $BDSL_NO               =   $request['BDSL_NO'];
            $BDSL_DATE             =   $request['BDSL_DATE'];
            $BDCL_ID_REF           =   $request['BDCLID_REF'];    
    
            $EMP_TYPE     = $request['EMPLOYEE_TYPE'];
            if($EMP_TYPE=='own_employee'){
            $OWN_EMPLOYEE=1;
            $OUTSIDE_RESOURCE=0;
            }else{
            $OWN_EMPLOYEE=0;
            $OUTSIDE_RESOURCE=1;
            }
    
    
            $OUTSIDE_RNAME                  =   $request['Outsider_Name'];
            $OUTSIDE_COMPANY                =   $request['Company_Name'];
            $OUTSIDE_CONTACT_NO             =   $request['Contact_Number'];
            $MAINTENANCE_EMPID_REF1         =   $request['EMP_REF1'];
            $MAINTENANCE_EMPID_REF2         =   $request['EMP_REF2'];
            $MAINTENANCE_EMPID_REF3         =   $request['EMP_REF3'];
            $MAINTENANCE_START_DATE         =   $request['Main_Start_Date'];
            $MAINTENANCE_START_TIME         =   $request['Main_Start_Time'];
            $FAULT_DETECT1                  =   $request['Fault_Detect_1'];
            $FAULT_DETECT2                  =   $request['Fault_Detect_2'];
            $MCKLISTID_REF                  =   $request['CHECKLIST_REF'];
            $ACTION_TAKEN1                  =   $request['Action_Taken1'];
            $ACTION_TAKEN2                  =   $request['Action_Taken2'];
            $BREAKDOWN_STATUS               =   $request['drpstatus'];
            $MAINTENANCE_END_DATE           =   $request['Main_End_Date'];
            $MAINTENANCE_END_TIME           =   $request['Main_End_Time'];
            $REMARKS_PENDING                =   $request['REMARKS_PENDING'];
    
    
           
            $log_data = [ 
                $BDSL_NO,                                      $BDSL_DATE,                              $BDCL_ID_REF,                    $OWN_EMPLOYEE,                  $OUTSIDE_RESOURCE,       
                $OUTSIDE_RNAME,                                $OUTSIDE_COMPANY,                        $OUTSIDE_CONTACT_NO,             $MAINTENANCE_EMPID_REF1,        $MAINTENANCE_EMPID_REF2,
                $MAINTENANCE_EMPID_REF3,                       $MAINTENANCE_START_DATE,                 $MAINTENANCE_START_TIME,         $FAULT_DETECT1,                 $FAULT_DETECT2,
                $MCKLISTID_REF,                                $ACTION_TAKEN1,                          $ACTION_TAKEN2,                  $BREAKDOWN_STATUS,              $MAINTENANCE_END_DATE,
                $MAINTENANCE_END_TIME,                         $REMARKS_PENDING,                        $XMLCONSUME,                     $XMLRETURN,                     $XMLCHKLIST,
                $CYID_REF,                                     $BRID_REF,                               $FYID_REF,                       $VTID_REF,                      $USERID,
                Date('Y-m-d'),                                 Date('h:i:s.u'),                         $ACTIONNAME,                     $IPADDRESS
            ];
    
    
            //dd($log_data); 
    
            
            $sp_result = DB::select('EXEC SP_TRN_BD_COMPLAINT_SOLUTION_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,? ', $log_data); 


        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $BDSL_NO. ' Sucessfully Approved.']);

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
                $TABLE      =   "TBL_TRN_BD_SOLUTION";
                $FIELD      =   "BDSL_ID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
            
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_JWR ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
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
        $TABLE      =   "TBL_TRN_BD_SOLUTION";
        $FIELD      =   "BDSL_ID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_BD_SOLUTION_CHECKLIST',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_BD_SOLUTION_RETURN',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_BD_SOLUTION_CONSUMED',
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

            $objResponse = DB::table('TBL_TRN_BD_SOLUTION')->where('BDSL_ID','=',$id)->first();

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
        
		//$destinationPath = storage_path()."/docs/company".$CYID_REF."/JobWorkOrder";
        $image_path         =   "docs/company".$CYID_REF."/BreakdownSolution";     
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






    public function getLastdt(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        return  DB::select('SELECT MAX(BDSL_DATE) BDSL_DATE FROM TBL_TRN_BD_SOLUTION  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
    }


    
}
