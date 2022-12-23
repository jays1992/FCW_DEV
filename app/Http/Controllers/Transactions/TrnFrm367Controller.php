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

class TrnFrm367Controller extends Controller{
    protected $form_id  =   367;
    protected $vtid_ref =   452;
    protected $view     =   "transactions.PlantMaintenance.Preventive_Maintenance_Actual.trnfrm367";
   
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
        WHERE  AUD.VID=T1.PMAL_ID AND  AUD.CYID_REF=T1.CYID_REF AND  AUD.BRID_REF=T1.BRID_REF AND  
        AUD.FYID_REF=T1.FYID_REF AND  AUD.VTID_REF=T1.VTID_REF AND AUD.ACTIONNAME='ADD'       
        ) AS CREATED_BY
        FROM TBL_TRN_PM_ACTUAL T1
        INNER JOIN TBL_TRN_AUDITTRAIL T2 ON T1.PMAL_ID=T2.VID AND T1.VTID_REF=T2.VTID_REF AND T1.CYID_REF=T2.CYID_REF AND T1.BRID_REF=T2.BRID_REF 
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' AND T2.VTID_REF = '$this->vtid_ref' AND T2.ACTID IN (SELECT max(ACTID) FROM TBL_TRN_AUDITTRAIL A WHERE T2.VTID_REF = A.VTID_REF AND T2.VID = A.VID)
        ORDER BY T1.PMAL_ID DESC 
        ");

        return view($this->view,compact(['REQUEST_DATA','FormId','objRights','objDataList','DATA_STATUS']));
    }

    public function add(){  

        $FormId     =   $this->form_id;
        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');        

        $objlastdt      =   $this->getLastdt();
        $objMachineList =   $this->getMachineList();
        $objEmployee    =   $this->getEmployee();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_PM_ACTUAL',
            'HDR_ID'=>'PMAL_ID',
            'HDR_DOC_NO'=>'PMAL_NO',
            'HDR_DOC_DT'=>'PMAL_DATE'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
       
           
       
        return view($this->view.'add',compact(['FormId','objlastdt','objMachineList','objEmployee','doc_req','docarray']));

    }

    public function edit($id=NULL){

        $FormId     =   $this->form_id;
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

            $objlastdt      =   $this->getLastdt();
            $objMachineList =   $this->getMachineList();
            $objEmployee    =   $this->getEmployee();

            $objResponse = DB::table('TBL_TRN_PM_ACTUAL')            
            ->where('TBL_TRN_PM_ACTUAL.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_PM_ACTUAL.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_PM_ACTUAL.FYID_REF','=',Session::get('FYID_REF'))
			->where('TBL_TRN_PM_ACTUAL.PMAL_ID','=',$id)    
            ->leftJoin('TBL_MST_MACHINE AS T2', 'TBL_TRN_PM_ACTUAL.MACHINEID_REF','=','T2.MACHINEID')        
            ->leftJoin('TBL_MST_EMPLOYEE AS EMP1', 'TBL_TRN_PM_ACTUAL.MAINTENANCE_EMPID_REF1','=','EMP1.EMPID')
            ->leftJoin('TBL_MST_EMPLOYEE AS EMP2', 'TBL_TRN_PM_ACTUAL.MAINTENANCE_EMPID_REF2','=','EMP2.EMPID')
            ->leftJoin('TBL_MST_EMPLOYEE AS EMP3', 'TBL_TRN_PM_ACTUAL.MAINTENANCE_EMPID_REF3','=','EMP3.EMPID')
            ->select(
                'TBL_TRN_PM_ACTUAL.*','EMP1.EMPCODE AS EMP1_CODE','EMP1.FNAME AS EMP1_NAME',
                'EMP2.EMPCODE AS EMP2_CODE','EMP2.FNAME AS EMP2_NAME','EMP3.EMPCODE AS EMP3_CODE',
                'EMP3.FNAME AS EMP3_NAME',
                'T2.MACHINE_NO','T2.MACHINE_DESC'
                )
            ->first();
           
            $objResponsechecklists = DB::table('TBL_MST_MAINTENANCE_CHECKLIST')            
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('FYID_REF','=',Session::get('FYID_REF'))
			->where('CKLISTID','=',$objResponse->MCKLISTID_REF)         
            ->select('CHECKLIST_NO','CHECKLIST_DESC')
            ->first();

            $CHECKLIST= DB::table('TBL_TRN_PM_ACTUAL_CHECKLIST')
            ->where('TBL_TRN_PM_ACTUAL_CHECKLIST.PMAL_ID_REF','=',$id)
            ->leftJoin('TBL_MST_MACHINE_CHECKLIST_DETAILS', 'TBL_TRN_PM_ACTUAL_CHECKLIST.MCKLIST_DID_REF','=','TBL_MST_MACHINE_CHECKLIST_DETAILS.MCKLISTID_REF')
            ->leftJoin('TBL_MST_MAINTENANCE_PARAMETER',   'TBL_MST_MAINTENANCE_PARAMETER.MPID','=',   'TBL_MST_MACHINE_CHECKLIST_DETAILS.MPID_REF')
            ->leftJoin('TBL_MST_MAINTENANCE_SUB_PARAMETER',   'TBL_MST_MAINTENANCE_SUB_PARAMETER.MSPID','=',   'TBL_MST_MACHINE_CHECKLIST_DETAILS.MSPID_REF')
            ->select(
                'TBL_MST_MACHINE_CHECKLIST_DETAILS.*','TBL_TRN_PM_ACTUAL_CHECKLIST.*', 'TBL_MST_MAINTENANCE_PARAMETER.MPID',
                'TBL_MST_MAINTENANCE_PARAMETER.MP_CODE','TBL_MST_MAINTENANCE_PARAMETER.MP_DESC',
                'TBL_MST_MAINTENANCE_SUB_PARAMETER.MSPID','TBL_MST_MAINTENANCE_SUB_PARAMETER.MSP_CODE',
                'TBL_MST_MAINTENANCE_SUB_PARAMETER.MSP_DESC'
                )
             ->get();

            $RETURNLIST= DB::table('TBL_TRN_PM_ACTUAL_RETURN')
            ->where('PMAL_ID_REF','=',$id)
            ->select('TBL_TRN_PM_ACTUAL_RETURN.*')
            ->get();

            $CONSUMELIST= DB::table('TBL_TRN_PM_ACTUAL_CONSUMED')
            ->where('PMAL_ID_REF','=',$id)
            ->select('TBL_TRN_PM_ACTUAL_CONSUMED.*')
            ->get();

            return view($this->view.'edit',compact(['FormId','objRights','objlastdt','objMachineList','objEmployee','objResponse','objResponsechecklists','CONSUMELIST','RETURNLIST','CHECKLIST']));
        }
     
    }

    public function view($id=NULL){

        $FormId     =   $this->form_id;
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

            $objlastdt      =   $this->getLastdt();
            $objMachineList =   $this->getMachineList();
            $objEmployee    =   $this->getEmployee();

            $objResponse = DB::table('TBL_TRN_PM_ACTUAL')            
            ->where('TBL_TRN_PM_ACTUAL.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_PM_ACTUAL.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_PM_ACTUAL.FYID_REF','=',Session::get('FYID_REF'))
			->where('TBL_TRN_PM_ACTUAL.PMAL_ID','=',$id)    
            ->leftJoin('TBL_MST_MACHINE AS T2', 'TBL_TRN_PM_ACTUAL.MACHINEID_REF','=','T2.MACHINEID')        
            ->leftJoin('TBL_MST_EMPLOYEE AS EMP1', 'TBL_TRN_PM_ACTUAL.MAINTENANCE_EMPID_REF1','=','EMP1.EMPID')
            ->leftJoin('TBL_MST_EMPLOYEE AS EMP2', 'TBL_TRN_PM_ACTUAL.MAINTENANCE_EMPID_REF2','=','EMP2.EMPID')
            ->leftJoin('TBL_MST_EMPLOYEE AS EMP3', 'TBL_TRN_PM_ACTUAL.MAINTENANCE_EMPID_REF3','=','EMP3.EMPID')
            ->select(
                'TBL_TRN_PM_ACTUAL.*','EMP1.EMPCODE AS EMP1_CODE','EMP1.FNAME AS EMP1_NAME',
                'EMP2.EMPCODE AS EMP2_CODE','EMP2.FNAME AS EMP2_NAME','EMP3.EMPCODE AS EMP3_CODE',
                'EMP3.FNAME AS EMP3_NAME',
                'T2.MACHINE_NO','T2.MACHINE_DESC'
                )
            ->first();
           
            $objResponsechecklists = DB::table('TBL_MST_MAINTENANCE_CHECKLIST')            
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            
			->where('CKLISTID','=',$objResponse->MCKLISTID_REF)         
            ->select('CHECKLIST_NO','CHECKLIST_DESC')
            ->first();

            $CHECKLIST= DB::table('TBL_TRN_PM_ACTUAL_CHECKLIST')
            ->where('TBL_TRN_PM_ACTUAL_CHECKLIST.PMAL_ID_REF','=',$id)
            ->leftJoin('TBL_MST_MACHINE_CHECKLIST_DETAILS', 'TBL_TRN_PM_ACTUAL_CHECKLIST.MCKLIST_DID_REF','=','TBL_MST_MACHINE_CHECKLIST_DETAILS.MCKLISTID_REF')
            ->leftJoin('TBL_MST_MAINTENANCE_PARAMETER',   'TBL_MST_MAINTENANCE_PARAMETER.MPID','=',   'TBL_MST_MACHINE_CHECKLIST_DETAILS.MPID_REF')
            ->leftJoin('TBL_MST_MAINTENANCE_SUB_PARAMETER',   'TBL_MST_MAINTENANCE_SUB_PARAMETER.MSPID','=',   'TBL_MST_MACHINE_CHECKLIST_DETAILS.MSPID_REF')
            ->select(
                'TBL_MST_MACHINE_CHECKLIST_DETAILS.*','TBL_TRN_PM_ACTUAL_CHECKLIST.*', 'TBL_MST_MAINTENANCE_PARAMETER.MPID',
                'TBL_MST_MAINTENANCE_PARAMETER.MP_CODE','TBL_MST_MAINTENANCE_PARAMETER.MP_DESC',
                'TBL_MST_MAINTENANCE_SUB_PARAMETER.MSPID','TBL_MST_MAINTENANCE_SUB_PARAMETER.MSP_CODE',
                'TBL_MST_MAINTENANCE_SUB_PARAMETER.MSP_DESC'
                )
             ->get();

            $RETURNLIST= DB::table('TBL_TRN_PM_ACTUAL_RETURN')
            ->where('PMAL_ID_REF','=',$id)
            ->select('TBL_TRN_PM_ACTUAL_RETURN.*')
            ->get();

            $CONSUMELIST= DB::table('TBL_TRN_PM_ACTUAL_CONSUMED')
            ->where('PMAL_ID_REF','=',$id)
            ->select('TBL_TRN_PM_ACTUAL_CONSUMED.*')
            ->get();

            return view($this->view.'view',compact(['FormId','objRights','objlastdt','objMachineList','objEmployee','objResponse','objResponsechecklists','CONSUMELIST','RETURNLIST','CHECKLIST']));
        }
     
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

    

    public function getEmployee(){

        $objEmployee    =   DB::table('TBL_MST_EMPLOYEE')
                            ->where('STATUS','=','A')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('BRID_REF','=',Auth::user()->BRID_REF)
                            ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
                            ->select('TBL_MST_EMPLOYEE.*')
                            ->get();

        return $objEmployee;
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
        'TBL_MST_MAINTENANCE_PARAMETER.MPID','TBL_MST_MAINTENANCE_PARAMETER.MP_CODE','TBL_MST_MAINTENANCE_PARAMETER.MP_DESC',               'TBL_MST_MAINTENANCE_SUB_PARAMETER.MSPID','TBL_MST_MAINTENANCE_SUB_PARAMETER.MSP_CODE','TBL_MST_MAINTENANCE_SUB_PARAMETER.MSP_DESC')
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

    public function save(Request $request) {
        
        $r_count_checklist  =   count($request['checklistcount']);
        $r_count1           =   $request['Row_Count1'];
        $r_count2           =   $request['Row_Count2'];

        for ($i=0; $i<=$r_count_checklist; $i++)
        {
            if(isset($request['MCKLIST_DID'][$i]))
            {
                $req_data[$i] = [
                    'MCKLIST_DID_REF'    => $request['MCKLIST_DID'][$i],
                    'STANDARD_VALUE' => $request['STANDARD_VALUE'][$i],
                    'ACTUAL_VALUE' => $request['ACTUAL_VALUE'][$i],                    
                    'CHK_REMARKS' => $request['REMARKS'][$i],                    
                 
                ];
            }
        }


        $wrapped_links["CHKLIST"] = $req_data; 
        $XMLCHKLIST = ArrayToXml::convert($wrapped_links);

        for ($i=0; $i<=$r_count1; $i++){

                if(isset($request['SPARE_PARTS_CONSUMED_'.$i]))
                {
                    $reqdata2[$i] = [
                        'SPARE_PART_NAME'    => $request['SPARE_PARTS_CONSUMED_'.$i],          
                    ];
                }
        }

       

        if(isset($reqdata2)) { 
            $wrapped_links2["CONSUME"] = $reqdata2;
            $XMLCONSUME = ArrayToXml::convert($wrapped_links2);
        }
        else {
            $XMLCONSUME = NULL; 
        } 

        for ($i=0; $i<=$r_count2; $i++){
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

        $PMAL_NO               =   $request['PMAL_NO'];
        $PMAL_DATE             =   $request['PMAL_DATE'];
        $PMSL_ID_REF           =   $request['PMSL_ID_REF'];  
        $MACHINEID_REF         =   $request['MACHINEID_REF']; 
        $PMSL_FROM_DATE        =   $request['PMSL_FROM_DATE'];
        $PMSL_TO_DATE          =   $request['PMSL_TO_DATE']; 
        $SPECIAL_INST          =   $request['SPECIAL_INST'];
        $ACTUAL_FROM_DATE      =   $request['ACTUAL_FROM_DATE'];
        $ACTUAL_TO_DATE        =   $request['ACTUAL_TO_DATE'];
        $TOTAL_NO_DAYS         =   $request['TOTAL_NO_DAYS'];

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
        $MCKLISTID_REF                  =   $request['CHECKLIST_REF'];
        $ACTION_TAKEN1                  =   $request['Action_Taken1'];
        $ACTION_TAKEN2                  =   $request['Action_Taken2'];
        $BREAKDOWN_STATUS               =   $request['drpstatus'];
        $REMARKS                        =   $request['HDR_REMARKS'];

        $log_data = [ 
            $PMAL_NO,                   $PMAL_DATE,                 $PMSL_ID_REF,               $MACHINEID_REF,         $PMSL_FROM_DATE, 
            $PMSL_TO_DATE,              $SPECIAL_INST,              $ACTUAL_FROM_DATE,          $ACTUAL_TO_DATE,        $TOTAL_NO_DAYS, 
            $OWN_EMPLOYEE,              $OUTSIDE_RESOURCE,          $OUTSIDE_RNAME,             $OUTSIDE_COMPANY,       $OUTSIDE_CONTACT_NO,             
            $MAINTENANCE_EMPID_REF1,    $MAINTENANCE_EMPID_REF2,    $MAINTENANCE_EMPID_REF3,    $MCKLISTID_REF,         $ACTION_TAKEN1,                          
            $ACTION_TAKEN2,             $BREAKDOWN_STATUS,          $REMARKS,                   $XMLCONSUME,            $XMLRETURN,                     
            $XMLCHKLIST,                $CYID_REF,                  $BRID_REF,                  $FYID_REF,              $VTID_REF,                      
            $USERID,                    Date('Y-m-d'),              Date('h:i:s.u'),            $ACTIONNAME,            $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_TRN_PM_ACTUAL_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,? ', $log_data); 
        
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }

        exit();   
    }

    public function update(Request $request){

        $r_count_checklist  =   count($request['checklistcount']);
        $r_count1           =   $request['Row_Count1'];
        $r_count2           =   $request['Row_Count2'];

        for ($i=0; $i<=$r_count_checklist; $i++)
        {
            if(isset($request['MCKLIST_DID'][$i]))
            {
                $req_data[$i] = [
                    'MCKLIST_DID_REF'    => $request['MCKLIST_DID'][$i],
                    'STANDARD_VALUE' => $request['STANDARD_VALUE'][$i],
                    'ACTUAL_VALUE' => $request['ACTUAL_VALUE'][$i],                    
                    'CHK_REMARKS' => $request['REMARKS'][$i],                    
                 
                ];
            }
        }


        $wrapped_links["CHKLIST"] = $req_data; 
        $XMLCHKLIST = ArrayToXml::convert($wrapped_links);

        for ($i=0; $i<=$r_count1; $i++){

                if(isset($request['SPARE_PARTS_CONSUMED_'.$i]))
                {
                    $reqdata2[$i] = [
                        'SPARE_PART_NAME'    => $request['SPARE_PARTS_CONSUMED_'.$i],          
                    ];
                }
        }

       

        if(isset($reqdata2)) { 
            $wrapped_links2["CONSUME"] = $reqdata2;
            $XMLCONSUME = ArrayToXml::convert($wrapped_links2);
        }
        else {
            $XMLCONSUME = NULL; 
        } 

        for ($i=0; $i<=$r_count2; $i++){
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

        $PMAL_NO               =   $request['PMAL_NO'];
        $PMAL_DATE             =   $request['PMAL_DATE'];
        $PMSL_ID_REF           =   $request['PMSL_ID_REF'];  
        $MACHINEID_REF         =   $request['MACHINEID_REF']; 
        $PMSL_FROM_DATE        =   $request['PMSL_FROM_DATE'];
        $PMSL_TO_DATE          =   $request['PMSL_TO_DATE']; 
        $SPECIAL_INST          =   $request['SPECIAL_INST'];
        $ACTUAL_FROM_DATE      =   $request['ACTUAL_FROM_DATE'];
        $ACTUAL_TO_DATE        =   $request['ACTUAL_TO_DATE'];
        $TOTAL_NO_DAYS         =   $request['TOTAL_NO_DAYS'];

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
        $MCKLISTID_REF                  =   $request['CHECKLIST_REF'];
        $ACTION_TAKEN1                  =   $request['Action_Taken1'];
        $ACTION_TAKEN2                  =   $request['Action_Taken2'];
        $BREAKDOWN_STATUS               =   $request['drpstatus'];
        $REMARKS                        =   $request['HDR_REMARKS'];

        $log_data = [ 
            $PMAL_NO,                   $PMAL_DATE,                 $PMSL_ID_REF,               $MACHINEID_REF,         $PMSL_FROM_DATE, 
            $PMSL_TO_DATE,              $SPECIAL_INST,              $ACTUAL_FROM_DATE,          $ACTUAL_TO_DATE,        $TOTAL_NO_DAYS, 
            $OWN_EMPLOYEE,              $OUTSIDE_RESOURCE,          $OUTSIDE_RNAME,             $OUTSIDE_COMPANY,       $OUTSIDE_CONTACT_NO,             
            $MAINTENANCE_EMPID_REF1,    $MAINTENANCE_EMPID_REF2,    $MAINTENANCE_EMPID_REF3,    $MCKLISTID_REF,         $ACTION_TAKEN1,                          
            $ACTION_TAKEN2,             $BREAKDOWN_STATUS,          $REMARKS,                   $XMLCONSUME,            $XMLRETURN,                     
            $XMLCHKLIST,                $CYID_REF,                  $BRID_REF,                  $FYID_REF,              $VTID_REF,                      
            $USERID,                    Date('Y-m-d'),              Date('h:i:s.u'),            $ACTIONNAME,            $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_TRN_PM_ACTUAL_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,? ', $log_data); 
        
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $PMAL_NO. ' Sucessfully Updated.']);

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
           
            $r_count_checklist  =   count($request['checklistcount']);
        $r_count1           =   $request['Row_Count1'];
        $r_count2           =   $request['Row_Count2'];

        for ($i=0; $i<=$r_count_checklist; $i++)
        {
            if(isset($request['MCKLIST_DID'][$i]))
            {
                $req_data[$i] = [
                    'MCKLIST_DID_REF'    => $request['MCKLIST_DID'][$i],
                    'STANDARD_VALUE' => $request['STANDARD_VALUE'][$i],
                    'ACTUAL_VALUE' => $request['ACTUAL_VALUE'][$i],                    
                    'CHK_REMARKS' => $request['REMARKS'][$i],                    
                 
                ];
            }
        }


        $wrapped_links["CHKLIST"] = $req_data; 
        $XMLCHKLIST = ArrayToXml::convert($wrapped_links);

        for ($i=0; $i<=$r_count1; $i++){

                if(isset($request['SPARE_PARTS_CONSUMED_'.$i]))
                {
                    $reqdata2[$i] = [
                        'SPARE_PART_NAME'    => $request['SPARE_PARTS_CONSUMED_'.$i],          
                    ];
                }
        }

       

        if(isset($reqdata2)) { 
            $wrapped_links2["CONSUME"] = $reqdata2;
            $XMLCONSUME = ArrayToXml::convert($wrapped_links2);
        }
        else {
            $XMLCONSUME = NULL; 
        } 

        for ($i=0; $i<=$r_count2; $i++){
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

        $PMAL_NO               =   $request['PMAL_NO'];
        $PMAL_DATE             =   $request['PMAL_DATE'];
        $PMSL_ID_REF           =   $request['PMSL_ID_REF'];  
        $MACHINEID_REF         =   $request['MACHINEID_REF']; 
        $PMSL_FROM_DATE        =   $request['PMSL_FROM_DATE'];
        $PMSL_TO_DATE          =   $request['PMSL_TO_DATE']; 
        $SPECIAL_INST          =   $request['SPECIAL_INST'];
        $ACTUAL_FROM_DATE      =   $request['ACTUAL_FROM_DATE'];
        $ACTUAL_TO_DATE        =   $request['ACTUAL_TO_DATE'];
        $TOTAL_NO_DAYS         =   $request['TOTAL_NO_DAYS'];

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
        $MCKLISTID_REF                  =   $request['CHECKLIST_REF'];
        $ACTION_TAKEN1                  =   $request['Action_Taken1'];
        $ACTION_TAKEN2                  =   $request['Action_Taken2'];
        $BREAKDOWN_STATUS               =   $request['drpstatus'];
        $REMARKS                        =   $request['HDR_REMARKS'];

        $log_data = [ 
            $PMAL_NO,                   $PMAL_DATE,                 $PMSL_ID_REF,               $MACHINEID_REF,         $PMSL_FROM_DATE, 
            $PMSL_TO_DATE,              $SPECIAL_INST,              $ACTUAL_FROM_DATE,          $ACTUAL_TO_DATE,        $TOTAL_NO_DAYS, 
            $OWN_EMPLOYEE,              $OUTSIDE_RESOURCE,          $OUTSIDE_RNAME,             $OUTSIDE_COMPANY,       $OUTSIDE_CONTACT_NO,             
            $MAINTENANCE_EMPID_REF1,    $MAINTENANCE_EMPID_REF2,    $MAINTENANCE_EMPID_REF3,    $MCKLISTID_REF,         $ACTION_TAKEN1,                          
            $ACTION_TAKEN2,             $BREAKDOWN_STATUS,          $REMARKS,                   $XMLCONSUME,            $XMLRETURN,                     
            $XMLCHKLIST,                $CYID_REF,                  $BRID_REF,                  $FYID_REF,              $VTID_REF,                      
            $USERID,                    Date('Y-m-d'),              Date('h:i:s.u'),            $ACTIONNAME,            $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_TRN_PM_ACTUAL_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,? ', $log_data); 
        
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $PMAL_NO. ' Sucessfully Approved.']);

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
                $TABLE      =   "TBL_TRN_PM_ACTUAL";
                $FIELD      =   "PMAL_ID";
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
        $TABLE      =   "TBL_TRN_PM_ACTUAL";
        $FIELD      =   "PMAL_ID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_PM_ACTUAL_CONSUMED',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_PM_ACTUAL_RETURN',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_PM_ACTUAL_CHECKLIST',
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

            $objResponse = DB::table('TBL_TRN_PM_ACTUAL')->where('PMAL_ID','=',$id)->first();

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
        
		//$destinationPath = storage_path()."/docs/company".$CYID_REF."/JobWorkOrder";
        $image_path         =   "docs/company".$CYID_REF."/Preventive_Maintenance_Actual";     
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

 

    public function getTax(Request $request){

        $CYID_REF   = Auth::user()->CYID_REF;
        $BRID_REF   = Session::get('BRID_REF');
        $FYID_REF   = Session::get('FYID_REF');
        $ITEMID_REF = $request->ITEMID_REF;
        $Tax_State  = $request->Tax_State;

        if($Tax_State == "OutofState"){
            $StateType  =   "T3.OUTOFSTATE='1'";
        }
        else{
            $StateType  =   "T3.WITHINSTATE='1'";
        }

        $objTax =   DB::select("SELECT T2.NRATE FROM TBL_MST_ITEM T1 
            LEFT JOIN TBL_MST_HSNNORMAL T2 ON T1.HSNID_REF=T2.HSNID_REF
            LEFT JOIN TBL_MST_TAXTYPE T3 ON T2.TAXID_REF=T3.TAXID
            WHERE T1.ITEMID='$ITEMID_REF' AND T3.STATUS='A' AND T3.CYID_REF='$CYID_REF' AND $StateType");

        if(!empty($objTax)){
            foreach($objTax as $val){
                $TaxArr[]=$val->NRATE;
            }
        }
        else{
            $TaxArr[0]=NULL;
            $TaxArr[1]=NULL;
        }

        echo json_encode($TaxArr);
        exit();

    }


    public function getAltUmQty($id,$itemid,$mqty){
        $ObjData =  DB::select('SELECT top 1 TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV WHERE  ITEMID_REF = ? AND TO_UOMID_REF =?', [$itemid,$id]);
    
        if(!empty($ObjData)){
            $auomqty = ($mqty/$ObjData[0]->FROM_QTY)*($ObjData[0]->TO_QTY);
            return $auomqty;
        }else{
            return '0';
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

    public function getStockQty($GRN_NO,$STID_REF,$ITEMID_REF,$UOMID_REF){


        $CYID_REF   = Auth::user()->CYID_REF;
        $BRID_REF   = Session::get('BRID_REF');
        $FYID_REF   = Session::get('FYID_REF');

        $ObjData =  DB::table('TBL_MST_BATCH')
        ->where('DOC_ID','=',$GRN_NO)
        ->where('DOC_TYPE','=','GRN AGAINST GE')
        ->where('STID_REF','=',$STID_REF)
        ->where('ITEMID_REF','=',$ITEMID_REF)
        ->where('UOMID_REF','=',$UOMID_REF)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('STATUS','=',"A")
        ->select('BATCH_CODE','CURRENT_QTY')
        ->first();

        return $ObjData;

    }


    public function getStoreDetails(Request $request){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $ITEMID_REF     =   $request['ITEMID_REF'];
        $UOMID_REF      =   $request['UOMID_REF'];
        $ROW_ID         =   $request['ROW_ID'];
        $ITEMROWID      =   $request['ITEMROWID'];
        $ACTION_TYPE    =   $request['ACTION_TYPE'] =="VIEW"?'disabled':'';
        $SRNOA          =   NULL;
        $BATCHNOA       =   NULL;

        

        $dataArr    =   array();

        if($ITEMROWID !=""){
            $exp        =   explode(",",$ITEMROWID);

            foreach($exp as $val){
                $keyid      =   explode("_",$val);
                $batchid    =   $keyid[0];
                $qty        =   $keyid[1];
                $dataArr[$batchid]  =   $qty;
            }
        }

        
        $objResponse =  DB::table('TBL_MST_ITEMCHECKFLAG')
            ->where('ITEMID_REF','=',$ITEMID_REF)
            ->select('SRNOA','BATCHNOA')
            ->first();

        if(!empty($objResponse)){
            $SRNOA      =   $objResponse->SRNOA;
            $BATCHNOA   =   $objResponse->BATCHNOA;
        }

        $objBatch =  DB::SELECT("SELECT T1.BATCHID,T1.BATCH_CODE,T1.ITEMID_REF,T1.STID_REF,T1.SERIALNO,T1.UOMID_REF,
        T1.CURRENT_QTY,T2.STCODE,T2.NAME AS STNAME,T3.UOMCODE,T3.DESCRIPTIONS AS UOMDESCRIPTIONS 
        FROM TBL_MST_BATCH T1
        LEFT JOIN TBL_MST_STORE T2 ON T1.STID_REF=T2.STID
        LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
        WHERE T1.STATUS='A' AND T1.ITEMID_REF='$ITEMID_REF' AND T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' 
         AND T1.UOMID_REF='$UOMID_REF'
        ");
     
        echo '<thead>';
        echo '<tr>';
        echo $BATCHNOA =='1'?'<th>Batch / Lot No</th>':'';
        echo '<th>Store</th>';
        echo $SRNOA =='1'?'<th>Serial No</th>':'';
        echo '<th>Main UoM</th>';
        echo '<th>Stock-in-hand</th>';
        echo '<th>Dispatch Qty</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach($objBatch as $key=>$val){

            $qtyvalue   =   array_key_exists($val->BATCHID, $dataArr)?$dataArr[$val->BATCHID]:'';

            if($request['ACTION_TYPE'] =="ADD"){
                $CURRENT_QTY=$val->CURRENT_QTY;
            }
            else{
                $CURRENT_QTY=(floatval($val->CURRENT_QTY)+floatval($qtyvalue));
            }

            echo '<tr  class="participantRow33">';
            echo $BATCHNOA =='1'?'<td>'.$val->BATCH_CODE.'</td>':'';
            echo '<td>'.$val->STCODE.' - '.$val->STNAME.'</td>';
            echo $SRNOA =='1'?'<td>'.$val->SERIALNO.'</td>':'';
            echo '<td>'.$val->UOMCODE.' - '.$val->UOMDESCRIPTIONS.'</td>';
            echo '<td>'.$CURRENT_QTY.'</td>';
            echo '<td><input '.$ACTION_TYPE.' type="text" name="UserQty_'.$key.'" id="UserQty_'.$key.'" value="'.$qtyvalue.'" class="form-control qtytext" onkeyup="checkStoreQty('.$ROW_ID.','.$CURRENT_QTY.',this.value,'.$key.')" onkeypress="return isNumberDecimalKey(event,this)" autocomplete="off"  ></td>';
            echo '<td hidden><input type="hidden" name="BATCHID_'.$key.'" id="BATCHID_'.$key.'" value="'.$val->BATCHID.'" class="qtytext" ></td>';
            echo '<td hidden><input type="hidden" name="STORENAME_'.$key.'" id="STORENAME_'.$key.'" value="'.$val->STNAME.'" ></td>';
            echo '</tr>';
        }

        echo '</tbody>';
        exit();
    }

   

    public function codeduplicate(Request $request){

        $PMAL_NO  =   trim($request['PMAL_NO']);
        $objLabel = DB::table('TBL_TRN_PM_ACTUAL')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('PMAL_NO','=',$PMAL_NO)
        ->select('PMAL_NO')->first();

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

        return  DB::select('SELECT MAX(PMAL_DATE) PMAL_DATE FROM TBL_TRN_PM_ACTUAL  
        WHERE  CYID_REF = ? AND BRID_REF = ?  AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
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

    public function getMaintenanceSchedule(Request $request){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $MACHINEID_REF  =   $request['MACHINEID_REF'];
        
        $ObjData = DB::select("SELECT T1.PMSL_ID,T2.PMSL_DATE_ID,T2.PMSL_FROM_DATE,T2.PMSL_TO_DATE,T2.SPECIAL_INST
        FROM TBL_TRN_PM_SCHEDULE T1
        LEFT JOIN TBL_TRN_PM_SCHEDULE_DATE T2 ON T1.PMSL_ID=T2.PMSL_ID_REF
        WHERE T1.CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF' AND MACHINEID_REF='$MACHINEID_REF' AND STATUS='A'");
    
        if(isset($ObjData) && !empty($ObjData)){

            foreach ($ObjData as $index=>$dataRow){
    
                $PMSL_ID        =   $dataRow->PMSL_ID;
                $PMSL_FROM_DATE =   $dataRow->PMSL_FROM_DATE;
                $PMSL_TO_DATE   =   $dataRow->PMSL_TO_DATE;
                $SPECIAL_INST   =   $dataRow->SPECIAL_INST;

                $PM_ACTUAL      =   DB::select("SELECT PMSL_FROM_DATE 
                                    FROM TBL_TRN_PM_ACTUAL 
                                    WHERE PMSL_ID_REF='$PMSL_ID' AND PMSL_FROM_DATE='$PMSL_FROM_DATE'");

                 $row = '';
                if(empty($PM_ACTUAL)){
                    $row = $row.'<tr >
                    <td class="ROW1"> <input type="checkbox" name="SELECT_PMSL_FROM_DATE[]" id="ROW_PMSL_FROM_DATE_ID_'.$index.'"  class="CLASS_PMSL_FROM_DATE_ID" value="'.$PMSL_ID.'" ></td>
                    <td class="ROW2">'.$PMSL_FROM_DATE.'<input type="hidden" id="txtROW_PMSL_FROM_DATE_ID_'.$index.'" data-desc="'.$PMSL_FROM_DATE.'" data-desc1="'.$PMSL_TO_DATE.'" data-desc2="'.$SPECIAL_INST.'" value="'.$PMSL_ID.'" > </td>
                    <td class="ROW3">'.$PMSL_TO_DATE.'</td>
                    </tr>';
                }

                echo $row;

            }
    
        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();
    
    }

    public function getTotalDays(Request $request){

        $date1  =   $request['ACTUAL_FROM_DATE'];
        $date2  =   $request['ACTUAL_TO_DATE'];

        $diff   =   strtotime($date2) - strtotime($date1);
        $tdays  =   abs(round($diff / 86400));
    
        echo $tdays;
        
        exit();
    }
    
}
