<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Transation\TblTrnFrm545;
use App\Models\Admin\TblMstUser;
use Auth;
use DB;
use Session;
use Response;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Nexmo\Client;
use Nexmo\Client\Credentials;
use Nexmo\Laravel\Facade\Nexmo;
use OpenTok\OpenTok;
use OpenTok\MediaMode;
use OpenTok\ArchiveMode;
use OpenTok\Role;
use PHPMailer\PHPMailer;
use PHPMailer\Exception;

class TrnFrm545Controller extends Controller
{
   
    protected $form_id = 545;
	protected $vtid_ref =   615;
    protected $MODULEID_REF   = 5;  //Module type id

    // require_once __DIR__ . '/../config.php';
    // require_once __DIR__ . '/../vendor/autoload.php';

    
    
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
        $USERID =   Session::get('USERID');
       // $GOID =  Session::get('GOID');
		
		 $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');   
        $FormId         =   $this->form_id;

        $sp_listing_data = 
        [
            $BRID_REF
        ];
        // dd($sp_listing_data);
        $objfacility = DB::select('EXEC SP_TRN_GET_GROUPOFFICE ?', $sp_listing_data);
        //dd($objfacility);
		
		$sp_provider = DB::select('EXEC SP_TRN_GET_BILLINGPROFILE');

        $objAppointment = DB::table('TBL_MST_APPOINTMENT')
       ->where('DEACTIVATED','=','0')
       ->select('TBL_MST_APPOINTMENT.*')
       ->get()
       ->toArray();     

       return view('transactions.appointment.CreateAppointment.trnfrm545',compact(['objfacility','FormId','objAppointment','sp_provider']));
    }

    public function create($id=NULL){

        $FormId     =   $this->form_id;
        $PatientId     =   $id;
        $USERID =   Session::get('USERID');
        $GOID =  Session::get('GOID');

        $objResponse = DB::table('TBL_MST_PATIENT')
        ->where('PATIENTID','=',$PatientId)
        ->select('PAYMENT_MODE','PATIENTID')
        ->first();
        $objPatient = DB::table('TBL_MST_PATIENT')
            ->where('PATIENTID','=',$objResponse->PATIENTID)
            ->first();

        $PAYMENT_MODE = $objResponse->PAYMENT_MODE;

        if($PAYMENT_MODE !=""){
            if(strtolower($PAYMENT_MODE) =="cash"){
                $res  = "2";
            }
            else if(strtolower($PAYMENT_MODE) =="insurance"){

                $insResponse = DB::table('TBL_MST_PATIENT_INSURANCE')
                ->where('INSURANCE_TYPES','=','PRIMARY')
                ->where('PATIENTID_REF','=',$PatientId)
                ->count();

                if($insResponse > 0){
                    $res  = "2";
                }
                else{
                    $res  = "1";
                }

            }   
        }
        else{
            $res  = "0";
        }

        $objfacility =  DB::table('TBL_MST_GROUPOFFICE')
        ->where('GOID','=',$GOID)
        ->select('TBL_MST_GROUPOFFICE.*')
        ->get()
       ->toArray();

        $visit_type = DB::table('TBL_MST_APPOINTMENT')
       ->where('DEACTIVATED','=','0')
       ->select('TBL_MST_APPOINTMENT.*')
       ->get()
       ->toArray();

        return view('transactions.appointment.CreateAppointment.trnfrm545create',compact(['objfacility','FormId','res','visit_type','objPatient']));
    }
    

    public function view(){

        $FormId         =   $this->form_id;
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $VTID_REF       =   $this->vtid_ref;
        $USERID     	=   Auth::user()->USERID;
		
        $ObjUser = DB::table('TBL_MST_USER')
        ->where('USERID','=',$USERID)
        ->where('DEACTIVATED','=','0')
        ->select('*')
        ->first();

        // if(is_null($PROVIDERID))
        // {
            
        // }
        // if(is_null($DATE))
        // {
            $DATE = NULL;
        // }
        // if(is_null($STATUS))
        // {
       
        // }
        $sp_listing_data = 
        [
            $BRID_REF, $DATE
        ];
        // dd($sp_listing_data);
        $sp_provider = DB::select('EXEC SP_TRN_GET_APPOINTMENT ?,?', $sp_listing_data);
        // dd($sp_provider);
        if($sp_provider)
        {
        $DATE = $sp_provider[0]->DATE;
        }
        else
        {
            $DATE =   Date('Y-m-d');
        }
        return view('transactions.appointment.CreateAppointment.trnfrm545view',compact(['sp_provider','FormId','DATE']));
    }

    public function viewfilter($request){
        // $data = Crypt::decrypt($request);    
        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
       
       // $PROVIDERID = $myValue['PROVIDERID_REF'];
        $hdndate = $myValue['hdndate'];
    //   $STATUS = $myValue['fiterStatus'];
        // dd($PROVIDERID);
        $FormId         =   $this->form_id;
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $VTID_REF       =   $this->vtid_ref;
        $USERID     	=   Auth::user()->USERID;
        
        
        
        $sp_listing_data = 
        [
            $BRID_REF, $hdndate
        ];
        
        $sp_provider = DB::select('EXEC SP_TRN_GET_APPOINTMENT ?,?', $sp_listing_data);
        // dd($sp_provider);
        if($sp_provider)
        {
        $DATE = $sp_provider[0]->DATE;
        }
        else
        {
            $DATE =   $hdndate;
        }
        // dd($DATE);
        return view('transactions.appointment.CreateAppointment.trnfrm545view',compact(['sp_provider','FormId','DATE']));
    }

    public function edit($id=NULL){

        if(!is_null($id)){

            $FormId     =   $this->form_id;
            // $USERID     =   Auth::user()->PATIENTID;
            $GOID       =   Session::get('GOID');

            $objResponse = DB::table('TBL_TRN_APPOINTMENT')
            ->where('APPOINTMENT_TRNID','=',$id)
            ->first();

            $objfacility =  DB::select("select GOID,PRACTICE_GROUPID,PRACTICE_GROUPNAME 
            from TBL_MST_GROUPOFFICE WHERE DEACTIVATED=0 OR DEACTIVATED IS NULL 
            ORDER BY PRACTICE_GROUPNAME");

            $sp_listing_data = [
                $GOID
            ];
           
            $objProvider = DB::select('EXEC SP_TRN_GET_PROVIDER ?', $sp_listing_data);

            $sp_listing_data = [
                $objResponse->VISIT_TYPE
            ];
           
            $objBillingProfile = DB::select('EXEC SP_TRN_GET_BILLINGPROFILE2 ?', $sp_listing_data);
            // dd($objBillingProfile);

            $objPatient = DB::table('TBL_MST_PATIENT')
            ->where('PATIENTID','=',$objResponse->PATIENTID_REF)
            ->first();

            $visit_type = DB::table('TBL_MST_APPOINTMENT')
            ->where('DEACTIVATED','=','0')
            ->select('TBL_MST_APPOINTMENT.*')
            ->get()
            ->toArray();

            $objRoom = DB::table('TBL_MST_GROUPOFFICE_ROOMS')
            ->where('GOID_REF','=',$GOID)
            ->select('*')
            ->get()
            ->toArray();

            return view('transactions.appointment.CreateAppointment.trnfrm545edit',compact(['objfacility','FormId','objResponse',
                    'visit_type','objProvider','objBillingProfile','objPatient','objRoom']));

        }

    }

    public function approval($id=NULL){

        if(!is_null($id)){

            $FormId     =   $this->form_id;
            // $USERID     =   Auth::user()->PATIENTID;
            $GOID       =   Session::get('GOID');

            $objResponse = DB::table('TBL_TRN_APPOINTMENT')
            ->where('APPOINTMENT_TRNID','=',$id)
            ->first();

            $objfacility =  DB::select("select GOID,PRACTICE_GROUPID,PRACTICE_GROUPNAME 
            from TBL_MST_GROUPOFFICE WHERE GOID=$GOID AND (DEACTIVATED=0 OR DEACTIVATED IS NULL)
            ORDER BY PRACTICE_GROUPNAME");

            //dd($objfacility); 

            $sp_listing_data = [
                $GOID
            ];
           
            $objProvider = DB::select('EXEC SP_TRN_GET_PROVIDER ?', $sp_listing_data);

            $sp_listing_data = [
                $objResponse->VISIT_TYPE
            ];
           
            $objBillingProfile = DB::select('EXEC SP_TRN_GET_BILLINGPROFILE2 ?', $sp_listing_data);
            // dd($objBillingProfile);

            $objPatient = DB::table('TBL_MST_PATIENT')
            ->where('PATIENTID','=',$objResponse->PATIENTID_REF)
            ->first();

            $visit_type = DB::table('TBL_MST_APPOINTMENT')
            ->where('DEACTIVATED','=','0')
            ->select('TBL_MST_APPOINTMENT.*')
            ->get()
            ->toArray();

            $objRoom = DB::table('TBL_MST_GROUPOFFICE_ROOMS')
            ->where('GOID_REF','=',$GOID)
            ->select('*')
            ->get()
            ->toArray();

            return view('transactions.appointment.CreateAppointment.trnfrm545approval',compact(['objfacility','FormId','objResponse',
                    'visit_type','objProvider','objBillingProfile','objPatient','objRoom']));

        }

    }

    public function list(){

        $FormId     =   $this->form_id;        
        $GOID       =   Session::get('GOID');
        $DATE       =   Date('Y-m-d');

        $sp_provider= DB::select("SELECT 
        T1.PATIENTID_REF,T1.PROVIDERID_REF,T1.APPOINTMENT_TRNID,T1.STATUS,T1.APPOINTMENT_TYPE,T1.DATE,T1.TIME,T1.NOTES,T1.TOTAL_AMT,T1.VISIT_TYPE,
        T2.FIRST_NAME,T2.LAST_NAME,T2.CELL_PHONE,T2.DOB,
        CONCAT(T3.FIRST_NAME,' ',T3.LAST_NAME) AS DoctorName,
        T4.CONSTENT_FORM_REQUESTED,PROFILE_TYPE,
        T5.CONSSENT_TRNID
        FROM TBL_TRN_APPOINTMENT T1 
        LEFT JOIN TBL_MST_PATIENT T2 ON T1.PATIENTID_REF=T2.PATIENTID
        LEFT JOIN TBL_MST_PROVIDER T3 ON T1.PROVIDERID_REF=T3.PROVIDERID
        LEFT JOIN TBL_MST_APPOINTMENT T4 ON T1.VISIT_TYPE=T4.APPOINTMENTID
        LEFT JOIN TBL_TRN_CONSENT T5 ON T1.APPOINTMENT_TRNID=T5.APPOINTMENT_TRNID_REF AND T1.PATIENTID_REF=T5.PATIENTID_REF
        WHERE T1.GOID_REF='$GOID' AND T1.BPID_REF is NULL 
        ");

        //DD($sp_provider);

        return view('transactions.appointment.CreateAppointment.trnfrm545list',compact(['sp_provider','FormId','DATE']));
    }


    public function GetAppoint(Request $request)
    {
        $AppId = $request['Appid'];
        $sp_listing_data = [ $AppId ];
        $sp_provider = DB::select('EXEC SP_TRN_GET_APPOINT ?', $sp_listing_data);
        $data = array();
        if(!empty($sp_provider))
        {
            foreach ($sp_provider as $key=>$listrow)
            {
                $nestedData['AppSlno'] = $listrow->AppSlno;
                $nestedData['Patient_Name'] = $listrow->Patient_Name;
                $nestedData['AppointmentDate'] = $listrow->AppointmentDate;
                $nestedData['AppointmentTime'] = $listrow->AppointmentTime;
                $nestedData['AptDateTime'] = $listrow->AptDateTime;
                $nestedData['PatientSlno'] = $listrow->PatientSlno;
                $nestedData['Notes'] = $listrow->Notes;
                $nestedData['Clinic_Name'] = $listrow->Clinic_Name ; 
                $nestedData['AptType'] = $listrow->AptType ;
				$nestedData['ITEMID_REF'] = $listrow->ITEMID_REF ;
                $data[] = $nestedData;
            }
        }
        // dd($data);
        echo json_encode($data);     
        exit();
    }

    public function GetLastConsultingDoctor(Request $request)
    {
        $PatientId = $request['PatientId'];
        $sp_listing_data = [ $PatientId ];
        $sp_provider = DB::select('EXEC SP_TRN_GET_LASTCONSULTINGDOCTOR ?', $sp_listing_data);
        $data = array();
        if(!empty($sp_provider))
        {
            foreach ($sp_provider as $key=>$listrow)
            {
                $nestedData['Doctor'] = $listrow->Doctor;
                $nestedData['cnt'] = $listrow->cnt;
                $data[] = $nestedData;
            }
        }
        echo json_encode($data);     
        exit();
    }

    public function GetProviderSchedule(Request $request)
    {
        $PROVIDERID_REF = $request['PROVIDERID_REF'];
        $GOID =  Session::get('GOID');
        $dt = str_replace("/","-",$request['Appoint_Date']);
        $AppointDate =   Carbon::parse($dt)->format('Y-m-d');
        $Appoint_Time =   $request['Appoint_Time'];
        $sp_listing_data = [ $PROVIDERID_REF,$GOID,$AppointDate,$Appoint_Time ];
       
        $sp_provider = DB::select('EXEC SP_TRN_GET_PROVIDER_SCHEDULE_STATUS ?,?,?,?', $sp_listing_data);
        if (!($sp_provider)){
            
            $sp_listing = [ $PROVIDERID_REF,$GOID,$AppointDate ];
       
            $sp_providershift = DB::select('EXEC SP_TRN_GET_PROVIDER_SCHEDULE ?,?,?', $sp_listing);
            $row = '';
            if($sp_providershift)
            {   
                $row = 'The Provider choosen by You available in Shift ';

                foreach ($sp_providershift as $key=>$listrow)
                {                    
                    $row = $row.'<br> From : '.$listrow->TIME_FR.' & To: '.$listrow->TIME_TO.' ';
                }
                return Response::json(['exists' =>true,'msg' => 'Provider Not Available. Kindly change the Appointment Date & Time. '.$row]);
            }
            else
            {
                return Response::json(['exists' =>true,'msg' => 'Provider Not Available for the Selected Date. Kindly change.']);
            }

        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }     
        exit();
    }

    public function GetPatientVisitStatus(Request $request)
    {
        $PatientId = $request['PATIENTID_REF'];
        $GOID =  Session::get('GOID');
        $dt = str_replace("/","-",$request['AppointDate']);
        $AppointDate =   Carbon::parse($dt)->format('Y-m-d');
        $sp_listing_data = [ $PatientId,$GOID,$AppointDate ];
        $sp_provider = DB::select('EXEC SP_TRN_GET_PATIIENT_VISIT_STATUS ?,?,?', $sp_listing_data);
        
        $objAppointment = DB::table('TBL_MST_APPOINTMENT')
       ->where('DEACTIVATED','=','0')
       ->select('TBL_MST_APPOINTMENT.*')
       ->get()
       ->toArray();
        
       $row='';

       if($objAppointment)
       {
        foreach($objAppointment as $ckey => $crow)
        {
            if($crow->PROFILE_TYPE == $sp_provider[0]->VISIT_STATUS)
            {
                $row.='<div class="col-sm-3"> 
                <label>'.$crow->PROFILE_TYPE.'</label>
                <input type="checkbox" id= "VISIT_TYPE_'.$ckey.'" name="VISIT_TYPE_'.$ckey.'" class="check" value="'. $crow->APPOINTMENTID .'" checked/>
                </div>';
            }
            else
            {
                $row.='<div class="col-sm-3"> 
                <label>'.$crow->PROFILE_TYPE.'</label>
                <input type="checkbox" id= "VISIT_TYPE_'.$ckey.'" name="VISIT_TYPE_'.$ckey.'" class="check" value="'. $crow->APPOINTMENTID .'" />
                </div>';
            }          

        }
       }       
        echo $row;     
        exit();
    }

    /* public function GetPatientVisitStatusAppointmentWise(Request $request)
    {
        $AppointID = $request['AppointID'];
        
        $sp_provider = DB::table('TBL_TRN_APPOINTMENT')
       ->where('APPOINTMENT_TRNID','=',$AppointID)
       ->select('TBL_TRN_APPOINTMENT.*')
       ->first();
        
        $objAppointment = DB::table('TBL_MST_APPOINTMENT')
       ->where('DEACTIVATED','=','0')
       ->select('TBL_MST_APPOINTMENT.*')
       ->get()
       ->toArray();
        
       $row='';

       if($objAppointment)
       {
        foreach($objAppointment as $ckey => $crow)
        {
            if($crow->APPOINTMENTID == $sp_provider->VISIT_TYPE)
            {
                $row.='<div class="col-sm-3"> 
                <label>'.$crow->PROFILE_TYPE.'</label>
                <input type="checkbox" id= "VISIT_TYPE_'.$ckey.'" name="VISIT_TYPE_'.$ckey.'" class="check" value="'. $crow->APPOINTMENTID .'" checked/>
                </div>';
            }
            else
            {
                $row.='<div class="col-sm-3"> 
                <label>'.$crow->PROFILE_TYPE.'</label>
                <input type="checkbox" id= "VISIT_TYPE_'.$ckey.'" name="VISIT_TYPE_'.$ckey.'" class="check" value="'. $crow->APPOINTMENTID .'" />
                </div>';
            }          

        }
       }       
        echo $row;     
        exit();
    } */

    

    public function getpatientdetails(Request $request)
    {
     //$GOID =  Session::get('GOID');
	  $CYID_REF   	=   Auth::user()->CYID_REF;
	  $BRID_REF   	=   Session::get('BRID_REF');
	  $FYID_REF   	=   Session::get('FYID_REF');
     
     if($request->get('query'))
     {
      $query = $request->get('query');
      $data =  DB::select("SELECT 
        T1.* FROM TBL_MST_CUSTOMER T1
        LEFT JOIN TBL_MST_CUSTOMER_BRANCH_MAP T2 ON T2.CID_REF =T1.CID
        WHERE T1.CYID_REF='$CYID_REF' AND T2.MAPBRID_REF='$BRID_REF' AND T1.STATUS='A' AND (T2.DEACTIVATED = 0 OR T2.DEACTIVATED IS NULL) ORDER BY T1.CID");
      
      $output = '<ul class="dropdown-menu" style="display:block; position:relative;font-size:13px;">';

      foreach($data as $row)
      {
        if((Str::contains(strtolower($row->NAME), $query))||(Str::contains(strtolower($row->EMAILID), $query))||(Str::contains(strtolower($row->MONO), $query)))
        {
            $output .= '
            <li class="targetLI" style="width: 400px;"><a href="#"><span class="autcname" id="pname">'.$row->NAME.'</span>
            <span class="autcname" id="pid" hidden>'.$row->SLID_REF.'</span><span class="autcname">'.$row->MONO.'</span><span class="autcname">'.$row->EMAILID.'</span>
            </a></li>';
        }
      }
      $output .= '</ul>';
      echo $output;
     }
    }

    public function GetResources(){

        $USERID     =   Auth::user()->USERID;   
        //$GOID =  Session::get('GOID');
	  $CYID_REF   	=   Auth::user()->CYID_REF;
	  $BRID_REF   	=   Session::get('BRID_REF');
	  $FYID_REF   	=   Session::get('FYID_REF');

        $ObjProvider = NULL;
        $ObjUser = DB::table('TBL_MST_USER')
        ->where('USERID','=',$USERID)
        ->where('DEACTIVATED','=','0')
        ->select('*')
        ->first();

        $sp_listing_data = [
            $GOID
        ];
       
        $sp_provider = DB::select('EXEC SP_TRN_GET_PROVIDER ?', $sp_listing_data);
        $data = array();
        if(!empty($sp_provider))
        {
            foreach ($sp_provider as $key=>$listrow)
            {
                if($ObjProvider == "Provider")
                {
                    if($listrow->PROVIDERID == $ObjUser->STAFFID_PROVIDERID)
                    {
                        $nestedData['id'] = $listrow->PROVIDERID;
                        $nestedData['title'] = $listrow->FIRST_NAME;
                        $nestedData['lname'] = $listrow->LAST_NAME;
                        $nestedData['eventColor'] = $listrow->COLOR ; 
                        $data[] = $nestedData;
                    }
                }
                else 
                {
                    $nestedData['id'] = $listrow->PROVIDERID;
                    $nestedData['title'] = $listrow->FIRST_NAME;
                    $nestedData['lname'] = $listrow->LAST_NAME;
                    $nestedData['eventColor'] = $listrow->COLOR ; 
                    $data[] = $nestedData;
                }
                
            }

        }
        echo json_encode($data);     
        exit();
    }

    public function GetEvents(){

        $USERID     =   Auth::user()->USERID;   
        //$GOID =  Session::get('GOID');
	  $CYID_REF   	=   Auth::user()->CYID_REF;
	  $BRID_REF   	=   Session::get('BRID_REF');
	  $FYID_REF   	=   Session::get('FYID_REF');

        $ObjUser = DB::table('TBL_MST_USER')
        ->where('USERID','=',$USERID)
        ->where('DEACTIVATED','=','0')
        ->select('*')
        ->first();

        
         
        $sp_listing_data = 
        [
            $BRID_REF
        ];

        $sp_provider = DB::select('EXEC SP_TRN_GET_EVENTS_TEST ?', $sp_listing_data);
        
        
        $data = array();
        if(!empty($sp_provider))
        {
            foreach ($sp_provider as $key=>$listrow)
            {
                $nestedData['id'] = $listrow->id;
                $nestedData['title'] = $listrow->title;
                $nestedData['start'] = $listrow->start;
                $nestedData['end'] = $listrow->end ;
                $nestedData['DoctorName'] = $listrow->DoctorName;
                $nestedData['Phone'] = $listrow->EMAIL;
                $nestedData['Mobile'] = $listrow->Mobile ; 
				$nestedData['ITEMID_REF'] = $listrow->ITEMID_REF ; 
                $data[] = $nestedData;
            }
        }
        
        // $json_data = array($data);  
        echo json_encode($data);     
        exit();
    }

    public function GetAllClinic(Request $request){

        $sp_provider = DB::table('TBL_MST_GROUPOFFICE')
                        ->whereRaw('DEACTIVATED = 0 OR DEACTIVATED IS NULL')              
                        ->select('TBL_MST_GROUPOFFICE.*')
                        ->get();
        $data = array();
        if(!empty($sp_provider))
        {
            foreach ($sp_provider as $key=>$listrow)
            {
                $nestedData['Clinic_Slno'] = $listrow->GOID;
                $nestedData['Clinic_Name'] = $listrow->PRACTICE_GROUPNAME;
                $data[] = $nestedData;
            }
        }
        // $json_data = array("data" => $data);  
        echo json_encode($data);     
        exit();
    }  
    
    public function getBillingProfile(Request $request){
		
		$USERID     =   Auth::user()->USERID;   
		//$GOID =  Session::get('GOID');
		$CYID_REF   	=   Auth::user()->CYID_REF;
		$BRID_REF   	=   Session::get('BRID_REF');
		$FYID_REF   	=   Session::get('FYID_REF');

        
        $dt = str_replace("/","-",$request['AppointDate']);
        $AppointDate =   Carbon::parse($dt)->format('Y-m-d');
        /* $sp_listing_data = [ $PatientId,$GOID,$AppointDate ];
        $sp_provider = DB::select('EXEC SP_TRN_GET_PATIIENT_VISIT_STATUS ?,?,?', $sp_listing_data); 
        $VISIT_TYPE =   $sp_provider[0]->VISIT_STATUS;
        $sp_listing_data = [
            $VISIT_TYPE
        ];*/
       
        $sp_provider = DB::select('EXEC SP_TRN_GET_BILLINGPROFILE');
        $row1 = '';
        $row1 = $row1.'<select id="BPID_REF" name="BPID_REF"  class="form-control" tabindex="6"> <option>Select</option>';
        $row2 = '';
        $row2 = $row2.'</select>';
        $row4 = '';
        if(!empty($sp_provider)){
            foreach ($sp_provider as $index=>$Row)
            {
            $row3 = '';
            $row3 = $row3.'<option value="'.$Row->ITEMID.'">'.$Row->NAME.'</option>';
            $row4 =  $row4 .$row3;
            }
            $row = '';
            $row = $row1 . $row4 . $row2; 
            echo $row;
            }else{
                echo '<select id="BPID_REF" name="BPID_REF" class="form-control" tabindex="6" > <option>Select</option></select>';
            }
            exit();
        }
        public function getBillingTotal(Request $request){

            $BPID =   $request['BPID'];
           
            $sp_provider = DB::table('TBL_MST_BILLING_PROFILE')
            ->where('BPID','=',$BPID)
            ->select('*')
            ->first();
            if(!empty($sp_provider)){
                echo $sp_provider->TOTAL_AMT;
            }else{
                echo '0.00';
            }
                exit();
        }

   public function getProvider(Request $request){

    $GOID =   $request['GOID'];
    $sp_listing_data = [
        $GOID
    ];
   
    $sp_provider = DB::select('EXEC SP_TRN_GET_PROVIDER ?', $sp_listing_data);
    $row1 = '';
    $row1 = $row1.'<select id="PROVIDERID_REF" name="PROVIDERID_REF"  class="form-control" tabindex="12"> <option>Select</option>';
    $row2 = '';
    $row2 = $row2.'</select>';
    $row4 = '';
    if(!empty($sp_provider)){
        foreach ($sp_provider as $index=>$Row)
        {
        $row3 = '';
        $row3 = $row3.'<option value="'.$Row->BRID.'">'.$Row->BRNAME.'</option>';
        $row4 =  $row4 .$row3;
        }
        $row = '';
        $row = $row1 . $row4 . $row2; 
        echo $row;
        }else{
            echo '<select id="PROVIDERID_REF" name="PROVIDERID_REF" tabindex="12" > <option>Select</option></select>';
        }
        exit();
    }

    public function getRooms(Request $request){

        $GOID =   $request['GOID'];
     
        $objRoom = DB::table('TBL_MST_GROUPOFFICE_ROOMS')
        ->where('GOID_REF','=',$GOID)
        ->select('*')
        ->get()
        ->toArray();
        $row1 = '';
        $row1 = $row1.'<select id="ROOMID_REF" name="ROOMID_REF"  tabindex="13" class="form-control"><option>Select</option>';
        $row2 = '';
        $row2 = $row2.'</select>';
        $row4 = '';
        if(!empty($objRoom)){
            foreach ($objRoom as $cindex=>$cRow)
            {
            $row3 = '';
            $row3 = $row3.'<option value="'.$cRow->ROOMID.'">'.$cRow->EXAM_NAME.'</option>';
            $row4 =  $row4 .$row3;
            }
            $row = '';
            $row = $row1 . $row4 . $row2; 
            echo $row;
            }else{
                echo '<select id="ROOMID_REF" name="ROOMID_REF" tabindex="13" class="form-control" >  <option>Select</option></select>';
            }
            exit();
        }

   public function codeduplicate(Request $request){

        $PATIENTID_REF =   $request['PATIENTID_REF'];
        $dt = str_replace("/","-",$request['Appoint_Date']);
        $Appoint_Date =   Carbon::parse($dt)->format('Y-m-d');
        $Appoint_Time =   $request['Appoint_Time'];
        
        $sp_listing_data = 
        [
            $PATIENTID_REF, $Appoint_Date, $Appoint_Time
        ];

        $sp_provider = DB::select('EXEC sp_check_duplicate_appointment ?,?,?', $sp_listing_data);
        // dd($sp_provider[0]->Message);
        if($sp_provider[0]->Message == 'True'){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }

   public function codeduplicate2(Request $request){

        $PROVIDERID_REF =   $request['PROVIDERID_REF'];
        $dt = str_replace("/","-",$request['Appoint_Date']);
        $Appoint_Date =   Carbon::parse($dt)->format('Y-m-d');
        $Appoint_Time =   $request['Appoint_Time'];
        
        $sp_listing_data = 
        [
            $PROVIDERID_REF, $Appoint_Date, $Appoint_Time
        ];

        $sp_provider = DB::select('EXEC sp_check_duplicate_appointment2 ?,?,?', $sp_listing_data);
        
        if($sp_provider[0]->Message == 'True'){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }


    public function resendsmsemail(Request $request){

       $APPOINTMENT_TRNID  =   $request['APPOINTMENT_TRNID'];

        $Appointment = DB::table('TBL_TRN_APPOINTMENT')    
        ->where('APPOINTMENT_TRNID','=',$APPOINTMENT_TRNID)           
        ->select('SLID_REF','BRID_REF','DATE','TIME','APPOINTMENT_TYPE')
        ->first();

        $SLID_REF      		=   $Appointment->SLID_REF;
        $BRID_REF           =   $Appointment->BRID_REF;
        $DATE               =   $Appointment->DATE;
        $TIME               =   $Appointment->TIME;
        $APPOINTMENT_TYPE   =   $Appointment->APPOINTMENT_TYPE;

        

        $res    =   $this->NewAppointmentMail($SLID_REF,$BRID_REF,$DATE,$TIME);
         
        if($res){
            return Response::json(['success' =>true,'msg' => 'Email send successfully.']);
        }
        else{
            return Response::json(['success' =>true,'msg' => 'Sorry Email does not send. please try again later.']);
        }

        exit();    
    }

   
    public function save(Request $request){

     //   dd($request->all());

        
    
        
        $APPOINTMENT_TRNID =   $request['txtAppId']; 
        $APPOINTMENT_TYPE   =   strtoupper(trim($request['APPOINTMENT_TYPE']) );
        $SLID_REF =   $request['PATIENTID_REF']; 
    
        $dt = str_replace("/","-",$request['Appoint_Date']);
        $DATE           =   Carbon::parse($dt)->format('Y-m-d');
        $TIME           =   $request['Appoint_Time'];
        $BRID_REF           =   $request['GOID_REF'];
        $NOTES           =   $request['NOTES'];
		$ITEMID           =   $request['BPID_REF'];
        $CREATED_BY            =   Auth::user()->USERID;


        if($APPOINTMENT_TRNID != ''){

            $objApp = DB::table('TBL_TRN_APPOINTMENT')    
            ->where('APPOINTMENT_TRNID','=',$APPOINTMENT_TRNID)    
            ->where('SLID_REF','=',$SLID_REF)            
            ->select('DATE','TIME')
            ->first();

           
            $PreDATE         =   date('m/d/Y',strtotime($objApp->DATE));
            $PreDATE_compare =   date('Y-m-d',strtotime($objApp->DATE));
            $PreTIME         =   date('H:i',strtotime($objApp->TIME));

            $appDate         =   date('m/d/Y',strtotime($DATE));
            $appDate_compare =   date('Y-m-d',strtotime($DATE));
            $appTime         =   $TIME; 
            $data=explode(' ',$TIME); 
            $time_compare=$data[0];        

                $ReseduleApp    =   "";

            if($appDate_compare == $PreDATE_compare && $time_compare ==$PreTIME){

                $ReseduleApp    =   "No";
            }
            else{
                $ReseduleApp    =   "Yes";
            }
        
            $Appointment_data = [$APPOINTMENT_TRNID,
                $APPOINTMENT_TYPE, $SLID_REF, $DATE,$TIME,$BRID_REF,$NOTES,$CREATED_BY,$ITEMID 
            ];
        
            
            try {
            
                $sp_result = DB::select('EXEC SP_APPOINTMENT_TRN_UP ?,?,?,?,?,?,?,?,?', $Appointment_data);
                
            }
            catch (\Throwable $th) {
                
                return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
            }

            if($sp_result[0]->RESULT=="SUCCESS"){
                
                if($ReseduleApp =="Yes"){
                
                    
            
                    $Appointment = DB::table('TBL_TRN_APPOINTMENT')
                    ->where('APPOINTMENT_TRNID','=',$APPOINTMENT_TRNID)
                    ->select('TBL_TRN_APPOINTMENT.*')
                    ->first();

                    /* $this->UpdateAppointmentMail($SLID_REF,$BRID_REF,$appDate,$appTime,$PreDATE,$PreTIME);
                    $this->UpdateAppointmentMailProvider($SLID_REF,$BRID_REF,$appDate,$appTime,$PreDATE,$PreTIME); */
					
					$this->NewAppointmentMail($SLID_REF,$BRID_REF,$DATE,$TIME);
					$this->NewAppointmentMailProvider($SLID_REF,$BRID_REF,$DATE,$TIME);

                    

                   
                    
                }
                
                return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);
                
            }
            else if($sp_result[0]->RESULT=="DUPLICATE RECORD"){
                return Response::json(['errors'=>true,'msg' => 'Duplicate record.','country'=>'duplicate']);
            }
            else{
                return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
            }
        }
        else{ 
        
            $Appointment_data = [
                 $APPOINTMENT_TYPE, $SLID_REF, $DATE,$TIME,$BRID_REF,$NOTES,$CREATED_BY,$ITEMID 
            ];
                    
        
            try {

                
                $sp_result = DB::select('EXEC SP_APPOINTMENT_TRN_IN ?,?,?,?,?,?,?,?', $Appointment_data);
        
            }
            catch (\Throwable $th) {
            
                return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        
            }
        
            if($sp_result[0]->RESULT=="SUCCESS"){
                
                
            
                $Appointment = DB::table('TBL_TRN_APPOINTMENT')
                ->where('SLID_REF','=',$request['PATIENTID_REF'])
                ->select('TBL_TRN_APPOINTMENT.*')
                ->orderBy('TBL_TRN_APPOINTMENT.APPOINTMENT_TRNID','DESC')
                ->first();
                
                $this->NewAppointmentMail($SLID_REF,$BRID_REF,$DATE,$TIME);
                $this->NewAppointmentMailProvider($SLID_REF,$BRID_REF,$DATE,$TIME);

                

                
                return Response::json(['success' =>true,'msg' => 'Appointment successfully created.']);
                
            }
            else if($sp_result[0]->RESULT=="DUPLICATE RECORD"){
            
                return Response::json(['errors'=>true,'msg' => 'Duplicate record.','country'=>'duplicate']);
                
            }
            else{

                return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
            }
        }

        exit();    
    }


    public function update(Request $request){
        
        $APPOINTMENT_TRNID 	=   $request['APPOINTMENT_TRNID']; 
        $APPOINTMENT_TYPE   =   strtoupper(trim($request['APPOINTMENT_TYPE']) );
        $SLID_REF 			=   $request['PATIENTID_REF']; 
    
        $dt 				= 	str_replace("/","-",$request['Appoint_Date']);
        $DATE           	=   Carbon::parse($dt)->format('Y-m-d');
        $TIME           	=   $request['Appoint_Time'];
        $BRID_REF           =   $request['GOID_REF'];
        $NOTES           	=   $request['NOTES'];
        $CREATED_BY         =   Auth::user()->USERID;
        
        

        
        if($APPOINTMENT_TRNID != '')
        {

            $objApp = DB::table('TBL_TRN_APPOINTMENT')    
            ->where('APPOINTMENT_TRNID','=',$APPOINTMENT_TRNID)    
            ->where('PATIENTID_REF','=',$PATIENTID_REF)            
            ->select('TOTAL_AMT','DATE','TIME')
            ->first();

            $PreDATE        =   date('m/d/Y',strtotime($objApp->DATE));
            $PreTIME        =   date('H:i',strtotime($objApp->TIME));

            $appDate        =   date('m/d/Y',strtotime($DATE));
            $appTime        =   date('H:i',strtotime($TIME)); 

            $ReseduleApp    =   "";

            if($appDate == $PreDATE && $appTime ==$PreTIME){

                $ReseduleApp    =   "No";
            }
            else{
                $ReseduleApp    =   "Yes";
            }

            $AcceptApp    =   "";
            if($objApp->TOTAL_AMT == "" && $TOTAL_AMT !=""){
                $AcceptApp    =   "Yes";
            }
            else{
                $AcceptApp    =   "No";
            }

            $Appointment_data = [$APPOINTMENT_TRNID,
                $APPOINTMENT_TYPE, $SLID_REF, $DATE,$TIME,$BRID_REF,$NOTES,$CREATED_BY 
            ];
        
            
            try {
            
                $sp_result = DB::select('EXEC SP_APPOINTMENT_TRN_UP ?,?,?,?,?,?,?,?', $Appointment_data);
                
            }
            catch (\Throwable $th) {
                
                return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
            }

            if($sp_result[0]->RESULT=="SUCCESS"){
				
				/* $objVideoApp = DB::table('TBL_TRN_APPOINTMENT')
				->where('APPOINTMENT_TYPE','=','VIDEO')
				->where('APPOINTMENT_TRNID','=',$APPOINTMENT_TRNID)
				->where('PATIENTID_REF','=',$PATIENTID_REF)
				->where('PROVIDERID_REF','=',$PROVIDERID_REF)
				->select('APPOINTMENT_TRNID')
				->first();

				$VideoLink	=	"";
				$VideoUrl	=	"";
				if(!empty($objVideoApp)){
					$VideoUrl   =   'https://yourclinic.app/Norton/hmsapi/vc.php?sid='.base64_encode($objVideoApp->APPOINTMENT_TRNID); 
					$VideoLink='
								<p>Please click on this link to video appointment.</p>
								<p><a href="'.$VideoUrl.'" target="_blank">'.$VideoUrl.'</a></p>
								';
				}
				
                $Appointment = DB::table('TBL_TRN_APPOINTMENT')
                ->where('PATIENTID_REF','=',$request['PATIENTID_REF'])
                ->select('TBL_TRN_APPOINTMENT.*')
                ->orderBy('TBL_TRN_APPOINTMENT.APPOINTMENT_TRNID','DESC')
                ->first(); */

				if($ReseduleApp =="Yes"){

					$this->NewAppointmentMail($SLID_REF,$BRID_REF,$DATE,$TIME);
					$this->NewAppointmentMailProvider($SLID_REF,$BRID_REF,$DATE,$TIME);
                    
                    /* $client->message()->send([
                        'to' => $PATIENT_PHONE_NO,
                        'from' => '18332367190',
                        'text' => "Appointment rescheduled ".date('m-d-Y', strtotime($Appointment->DATE))."   ".date('h:i A', strtotime($Appointment->TIME))." With Dr.".$Provider->LAST_NAME
                    ]);

                    $client->message()->send([
                        'to' => $PROVIDER_PHONE_NO,
                        'from' => '18332367190',
                        'text' => "Appointment rescheduled ".date('m-d-Y', strtotime($Appointment->DATE))."   ".date('h:i A', strtotime($Appointment->TIME))." With Pa.".$objPhone->FIRST_NAME
                    ]);
                
                    if($VideoUrl !=""){
                        
                        $client->message()->send([
                            'to' => $PATIENT_PHONE_NO,
                            'from' => '18332367190',
                            'text' => $VideoUrl
                        ]);

                        $client->message()->send([
                            'to' => $PROVIDER_PHONE_NO,
                            'from' => '18332367190',
                            'text' => $VideoUrl
                        ]);
                    } */

				}

				/* if($AcceptApp =="Yes"){

					$this->AcceptAppointmentMail($PATIENTID_REF,$PROVIDERID_REF,$GOID_REF,$DATE,$TIME,$VideoLink);
                    $this->AcceptAppointmentMailProvider($PATIENTID_REF,$PROVIDERID_REF,$GOID_REF,$DATE,$TIME,$VideoLink);
                    
                    $client->message()->send([
                        'to' => $PATIENT_PHONE_NO,
                        'from' => '18332367190',
                        'text' => "Appointment Approved ".date('m-d-Y', strtotime($Appointment->DATE))."   ".date('h:i A', strtotime($Appointment->TIME))." With Dr.".$Provider->LAST_NAME
                    ]);
    
                    $client->message()->send([
                        'to' => $PROVIDER_PHONE_NO,
                        'from' => '18332367190',
                        'text' => "Appointment Approved ".date('m-d-Y', strtotime($Appointment->DATE))."   ".date('h:i A', strtotime($Appointment->TIME))." With Pa.".$objPhone->FIRST_NAME
                    ]);
                    
                    if($VideoUrl !=""){
                        
                        $client->message()->send([
                            'to' => $PATIENT_PHONE_NO,
                            'from' => '18332367190',
                            'text' => $VideoUrl
                        ]);
    
                        $client->message()->send([
                            'to' => $PROVIDER_PHONE_NO,
                            'from' => '18332367190',
                            'text' => $VideoUrl
                        ]);
                        
                    }

				} */
                
                
				          
				return Response::json(['success' =>true,'msg' => 'Appointment successfully Updated.']);
				
            }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
            
                return Response::json(['errors'=>true,'msg' => 'Duplicate record.','country'=>'duplicate']);
                
            }else{

                return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
            }
        }
        exit();    
    }


    public function approval_confirmation(Request $request){
       // dd($request->all());
        
        $basic  = new \Nexmo\Client\Credentials\Basic('e6c46f9d', 'xEDx7fqFPcR4MxRu');
        $client = new \Nexmo\Client($basic);
        
        $objPhone= DB::table('TBL_MST_PATIENT')
        ->where('ISACTIVE','=', 1)      
        ->where('PATIENTID','=',$request['PATIENTID_REF'])        
        ->select('TBL_MST_PATIENT.*')
        ->first();

        $PATIENT_PHONE_NO =   $objPhone->COUNTRY_CODE.$objPhone->CELL_PHONE;

        $Provider = DB::table('TBL_MST_PROVIDER')
        ->where('PROVIDERID','=',$request['PROVIDERID_REF'])
        ->select('TBL_MST_PROVIDER.*')
        ->first();

        $PROVIDER_PHONE_NO =   $Provider->COUNTRY_CODE.$Provider->CELLNO;

        $objAppointment = DB::table('TBL_MST_APPOINTMENT')
        ->where('DEACTIVATED','=', 0)        
        ->select('TBL_MST_APPOINTMENT.*')
        ->count();

        for ($i=0; $i<=$objAppointment; $i++)
        {
            if(isset($request['VISIT_TYPE_'.$i]))
            {
                $VISIT_TYPE          = $request['VISIT_TYPE_'.$i];
            }
        }

        
        $APPOINTMENT_TRNID =   $request['APPOINTMENT_TRNID']; 
        $APPOINTMENT_TYPE   =   strtoupper(trim($request['APPOINTMENT_TYPE']) );
        $PATIENTID_REF =   $request['PATIENTID_REF']; 
        $VISIT_TYPE          = $request['VISIT_TYPE'];
        $REASON          =   $request['REASON'];
        $dt = str_replace("/","-",$request['Appoint_Date']);
        $DATE           =   Carbon::parse($dt)->format('Y-m-d');
        $TIME           =   $request['Appoint_Time'];
        $DURATION         =   $request['drpEndtime'];
        $BPID_REF           =   $request['BPID_REF'];
        $GOID_REF           =   $request['GOID_REF'];
        $PROVIDERID_REF           =   $request['PROVIDERID_REF'];
        if($request['ROOMID_REF'] != 'Select')
        {
        $ROOMID_REF            =   $request['ROOMID_REF'];
        }
        else
        {
            $ROOMID_REF            =   NULL;
        }
        $NOTES           =   $request['NOTES'];
        $CREATED_BY            =   Auth::user()->USERID;
        $TOTAL_AMT           =   $request['TOTAL_AMT'];

        
        if($APPOINTMENT_TRNID != '')
        {

            $objApp = DB::table('TBL_TRN_APPOINTMENT')    
            ->where('APPOINTMENT_TRNID','=',$APPOINTMENT_TRNID)    
            ->where('PATIENTID_REF','=',$PATIENTID_REF)            
            ->select('TOTAL_AMT','DATE','TIME')
            ->first();

        

            $PreDATE        =   date('m/d/Y',strtotime($objApp->DATE));
            $PreDATE_compare =   date('Y-m-d',strtotime($objApp->DATE));
            $PreTIME        =   date('H:i',strtotime($objApp->TIME));

            $appDate        =   date('m/d/Y',strtotime($DATE));
            $appDate_compare  =   date('Y-m-d',strtotime($DATE));
            $appTime        =   $TIME; 
            $data=explode(' ',$TIME); 
            $time_compare=$data[0];        

                $ReseduleApp    =   "";

            if($appDate_compare == $PreDATE_compare && $time_compare ==$PreTIME){

                $ReseduleApp    =   "No";
            }
            else{
                $ReseduleApp    =   "Yes";
            }


            //dd($ReseduleApp);

            $AcceptApp    =   "";
            if($objApp->TOTAL_AMT == "" && $TOTAL_AMT !=""){
                $AcceptApp    =   "Yes";
            }
            else{
                $AcceptApp    =   "No";
            }

            $objpaymentmode = DB::table('TBL_MST_PATIENT')    
            ->where('PATIENTID','=',$PATIENTID_REF)                     
            ->select('PAYMENT_MODE')
            ->first();

            if($objpaymentmode->PAYMENT_MODE=='Cash'){
                $PAY_TYPE='SELF_PAY';
                }else{
                $PAY_TYPE='INSURANCE_PAY';                    
                }

            $ADJUST_ADVANCE=0;

            $Appointment_data = [$APPOINTMENT_TRNID,
                $APPOINTMENT_TYPE, $PATIENTID_REF, $VISIT_TYPE, $REASON,$DATE,$TIME,
                $DURATION, $GOID_REF,$PROVIDERID_REF,$ROOMID_REF, $NOTES,$TOTAL_AMT,$BPID_REF, $CREATED_BY,$ADJUST_ADVANCE,$PAY_TYPE
            ];
            ($Appointment_data);
        
            try {

                $sp_result = DB::select('EXEC SP_APPOINTMENT_TRN_APPROVAL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $Appointment_data);
                //dd($sp_result); 

            } catch (\Throwable $th) {

                return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);

            }

            if($sp_result[0]->RESULT=="SUCCESS"){
				
				$objVideoApp = DB::table('TBL_TRN_APPOINTMENT')
				->where('APPOINTMENT_TYPE','=','VIDEO')
				->where('APPOINTMENT_TRNID','=',$APPOINTMENT_TRNID)
				->where('PATIENTID_REF','=',$PATIENTID_REF)
				->where('PROVIDERID_REF','=',$PROVIDERID_REF)
				->select('APPOINTMENT_TRNID')
				->first();

				$VideoLink	=	"";
				$VideoUrl	=	"";
				if(!empty($objVideoApp)){
					$VideoUrl   =   'https://yourclinic.app/Norton/hmsapi/vc.php?sid='.base64_encode($objVideoApp->APPOINTMENT_TRNID); 
					$VideoLink='
								<p>Please click on this link to video appointment.</p>
								<p><a href="'.$VideoUrl.'" target="_blank">'.$VideoUrl.'</a></p>
								';
				}
				
                $Appointment = DB::table('TBL_TRN_APPOINTMENT')
                ->where('PATIENTID_REF','=',$request['PATIENTID_REF'])
                ->where('APPOINTMENT_TRNID','=',$APPOINTMENT_TRNID)
                ->select('TBL_TRN_APPOINTMENT.*')
                ->orderBy('TBL_TRN_APPOINTMENT.APPOINTMENT_TRNID','DESC')
                ->first();

				if($ReseduleApp =="Yes"){

					$this->UpdateAppointmentMail($PATIENTID_REF,$PROVIDERID_REF,$GOID_REF,$appDate,$appTime,$PreDATE,$PreTIME,$VideoLink);
                    $this->UpdateAppointmentMailProvider($PATIENTID_REF,$PROVIDERID_REF,$GOID_REF,$appDate,$appTime,$PreDATE,$PreTIME,$VideoLink); 
                    
                    $client->message()->send([
                        'to' => $PATIENT_PHONE_NO,
                        'from' => '18332367190',
                        'text' => "Appointment rescheduled ".date('m-d-Y', strtotime($Appointment->DATE))."   ".date('h:i A', strtotime($Appointment->TIME))." With Dr.".$Provider->LAST_NAME
                    ]);

                    $client->message()->send([
                        'to' => $PROVIDER_PHONE_NO,
                        'from' => '18332367190',
                        'text' => "Appointment rescheduled ".date('m-d-Y', strtotime($Appointment->DATE))."   ".date('h:i A', strtotime($Appointment->TIME))." With Pa.".$objPhone->FIRST_NAME
                    ]);
                
                    if($VideoUrl !=""){
                        
                        $client->message()->send([
                            'to' => $PATIENT_PHONE_NO,
                            'from' => '18332367190',
                            'text' => $VideoUrl
                        ]);

                        $client->message()->send([
                            'to' => $PROVIDER_PHONE_NO,
                            'from' => '18332367190',
                            'text' => $VideoUrl
                        ]);
                    }

				}

				if($AcceptApp =="Yes"){

					$this->AcceptAppointmentMail($PATIENTID_REF,$PROVIDERID_REF,$GOID_REF,$DATE,$TIME,$VideoLink);
                    $this->AcceptAppointmentMailProvider($PATIENTID_REF,$PROVIDERID_REF,$GOID_REF,$DATE,$TIME,$VideoLink);



             
                    $client->message()->send([
                        'to' => $PATIENT_PHONE_NO,
                        'from' => '18332367190',
                        'text' => "Your appointment with Dr". ".$Provider->LAST_NAME." .date('m-d-Y', strtotime($Appointment->DATE))."   ".date('h:i A', strtotime($Appointment->TIME))." is confirmed. Kindly login to yourclinic.app and make payment."
                    ]);
    
                    $client->message()->send([
                        'to' => $PROVIDER_PHONE_NO,
                        'from' => '18332367190',
                        'text' => "Your appointment with ". ".$objPhone->FIRST_NAME.".date('m-d-Y', strtotime($Appointment->DATE))."   ".date('h:i A', strtotime($Appointment->TIME))." is confirmed."
                    ]);
                    
                    if($VideoUrl !=""){
                        
                        $client->message()->send([
                            'to' => $PATIENT_PHONE_NO,
                            'from' => '18332367190',
                            'text' => $VideoUrl
                        ]);
    
                        $client->message()->send([
                            'to' => $PROVIDER_PHONE_NO,
                            'from' => '18332367190',
                            'text' => $VideoUrl
                        ]);
                        
                    }

				}
                
                
				          
				return Response::json(['success' =>true,'msg' => 'Appointment successfully approved.']);
				
            }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
            
                return Response::json(['errors'=>true,'msg' => 'Duplicate record.','country'=>'duplicate']);
                
            }else{

                return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
            }
        }
        exit();    
    }


    public function cancel(Request $request){

        $basic  = new \Nexmo\Client\Credentials\Basic('e6c46f9d', 'xEDx7fqFPcR4MxRu');
        $client = new \Nexmo\Client($basic);
         
        $APPOINTMENT_TRNID  =   $request['APPOINTMENT_TRNID']; 
        $GOID_REF           =   Session::get('GOID');
        $CREATED_BY         =   Auth::user()->USERID;

        $Appointment_data = [
            $APPOINTMENT_TRNID, $GOID_REF,$CREATED_BY 
        ];
      
        try {
        $sp_result = DB::select('EXEC SP_APPOINTMENT_TRN_CANCEL ?,?,?', $Appointment_data);
        } catch (\Throwable $th) {
            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }

        if($sp_result[0]->RESULT=="SUCCESS"){

            $this->CancelAppointmentMail($APPOINTMENT_TRNID);
            $this->CancelAppointmentMailProvider($APPOINTMENT_TRNID);


            $Appointment = DB::table('TBL_TRN_APPOINTMENT')    
            ->where('APPOINTMENT_TRNID','=',$APPOINTMENT_TRNID)           
            ->select('PATIENTID_REF','PROVIDERID_REF','GOID_REF','DATE','TIME')
            ->first();

            $objPhone= DB::table('TBL_MST_PATIENT')
            ->where('ISACTIVE','=', 1)      
            ->where('PATIENTID','=',$Appointment->PATIENTID_REF)        
            ->select('TBL_MST_PATIENT.*')
            ->first();
    
            $PATIENT_PHONE_NO =   $objPhone->COUNTRY_CODE.$objPhone->CELL_PHONE;
    
            $Provider = DB::table('TBL_MST_PROVIDER')
            ->where('PROVIDERID','=',$Appointment->PROVIDERID_REF)
            ->select('TBL_MST_PROVIDER.*')
            ->first();
    
            $PROVIDER_PHONE_NO =   $Provider->COUNTRY_CODE.$Provider->CELLNO;


            $client->message()->send([
                'to' => $PATIENT_PHONE_NO,
                'from' => '18332367190',
                'text' => "Appointment Cancel ".date('m-d-Y', strtotime($Appointment->DATE))."   ".date('h:i A', strtotime($Appointment->TIME))." With Dr.".$Provider->LAST_NAME
            ]);

            $client->message()->send([
                'to' => $PROVIDER_PHONE_NO,
                'from' => '18332367190',
                'text' => "Appointment Cancel ".date('m-d-Y', strtotime($Appointment->DATE))."   ".date('h:i A', strtotime($Appointment->TIME))." With Pa.".$objPhone->FIRST_NAME
            ]);


            return Response::json(['success' =>true,'msg' => 'Appointment Successfully Cancelled.']);

        }elseif($sp_result[0]->RESULT!="SUCCESS"){

            return Response::json(['errors'=>true,'msg' => $sp_result[0]->RESULT]);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }
        
        exit();    
   }


    
   public function savePMP(Request $request){
         
        //get data
        $APPOINTMENT_TRNID =   $request['appid']; 
        $PMP   =  $request['ispmp']; 
        $PMP_REMARKS =   $request['txtremarks']; 
        $GOID_REF       =   Session::get('GOID');
        $CREATED_BY            =   Auth::user()->USERID;

        $Appointment_data = [
            $APPOINTMENT_TRNID, $PMP, $PMP_REMARKS, $GOID_REF,$CREATED_BY 
        ];
      
            try {

                //save data
            $sp_result = DB::select('EXEC SP_APPOINTMENT_TRN_PMP ?,?,?,?,?', $Appointment_data);

            } catch (\Throwable $th) {

                return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);

            }

            if($sp_result[0]->RESULT=="SUCCESS"){

                return Response::json(['success' =>true,'msg' => 'PMP successfully updated.']);

            }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){

                return Response::json(['errors'=>true,'msg' => 'Duplicate record.','country'=>'duplicate']);
                
            }else{

                return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
            }
        
        exit();    
   }

   public function saveConfirmation(Request $request){
         
    //get data
    $APPOINTMENT_TRNID =   $request['appid']; 
    $CONFORMATION   =  $request['isconfirm']; 
    $CONFORMATION_NOTE =   $request['txtnotes']; 
    $GOID_REF       =   Session::get('GOID');
    $CREATED_BY            =   Auth::user()->USERID;

    $Appointment_data = [
        $APPOINTMENT_TRNID, $CONFORMATION, $CONFORMATION_NOTE, $GOID_REF,$CREATED_BY 
    ];

    //dd($Appointment_data); 
  
        try {

            //save data
        $sp_result = DB::select('EXEC SP_APPOINTMENT_TRN_CONFORMATION ?,?,?,?,?', $Appointment_data);

        } catch (\Throwable $th) {

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);

        }

        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Conformation successfully updated.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){

            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','country'=>'duplicate']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }
    
    exit();    
    }   

    public function saveNoShow(Request $request){
         
        //get data
        $APPOINTMENT_TRNID  =   $request['appid']; 
        $PATIENTID_REF      =   $request['patientid']; 
        $LATE_FEE           =   $request['trncost']; 
        $LATE_FEE_REMARKS   =   $request['NoShowRemarks']; 
        $GOID_REF           =   Session::get('GOID');
        $CREATED_BY         =   Auth::user()->USERID;
    
        $Appointment_data = [
            $APPOINTMENT_TRNID, $LATE_FEE, $CREATED_BY, $PATIENTID_REF,$GOID_REF, $LATE_FEE_REMARKS
        ];
      
            try {
    
                //save data
            $sp_result = DB::select('EXEC SP_TRN_APPOINTMENT_LATE_FEE ?,?,?,?,?,?', $Appointment_data);
    
            } catch (\Throwable $th) {
    
                return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
    
            }
    
            if($sp_result[0]->RESULT=="SUCCESS"){
    
                return Response::json(['success' =>true,'msg' => 'No Show / Late Fee successfully updated.']);
    
            }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
    
                return Response::json(['errors'=>true,'msg' => 'Duplicate record.','country'=>'duplicate']);
                
            }else{
    
                return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
            }
        
        exit();    
    } 



   
public function MakeVoiceCall($request){
    $box = $request;        
    $myValue=  array();
    parse_str($box, $myValue);
    $Mobile = $myValue['Mobile'];
   
    $keypair = new \Vonage\Client\Credentials\Keypair(
        file_get_contents('file://C:/HostingSpaces/aktar1/yourclinic.app/HMS_20201217/HMS/storage/private.key'),
        "2c3bc675-5b55-419e-a8b8-e62547843dd6"
    );
    $client = new \Vonage\Client($keypair);
    // $ncco = [
    //     [
    //       'action' => 'talk',
    //       'voiceName' => 'Kendra',
    //       'text' => 'This is a text-to-speech test message.'
    //     ]
    //   ];
    
    //   $call = new \Nexmo\Call\Call();
    //   $call->setTo($Mobile)
    //     ->setFrom('919810889086')
    //     ->setNcco($ncco);
      
    //   $response = $client->calls()->create($call);
    //   echo $response->getId();

    $outboundCall = new \Vonage\Voice\OutboundCall(
        new \Vonage\Voice\Endpoint\Phone($Mobile),
        new \Vonage\Voice\Endpoint\Phone('919810889086')
    );
    $outboundCall->setAnswerWebhook(
        new \Vonage\Voice\Webhook(
            'http://example.com/webhooks/answer',
            \Vonage\Voice\Webhook::METHOD_GET
        )
    );
    $response = $client->voice()->createOutboundCall($outboundCall);
      
        return redirect('http://example.com/webhooks/answer');
        
    }
   
    public function VideoCall(){

        $openTokAPI = new OpenTok('47120994', '0d2492a35124468378eff287ae22434a5c863801');

        // dd($openTokAPI);
           
            
            //$session = $openTokAPI->createSession(array('mediaMode' => MediaMode::ROUTED));
           
            $session        =   $openTokAPI->createSession(['mediaMode' => MediaMode::ROUTED]);
            $session_token  = $session->getSessionId();

           

            // $session_token = \Cache::remember('open_tok_session_key', 60, function () use ($openTokAPI) {             
            //         return $openTokAPI->createSession(['mediaMode' => MediaMode::ROUTED]);
            // });

            $opentok_token = $openTokAPI->generateToken($session_token, [
                    'exerciseireTime' => time()+60,
                    'data'       => "Some sample metadata to pass"
            ]);

        return view('transactions.appointment.CreateAppointment.trnfrm545Video',compact(['session_token','opentok_token']));
    }
    

    public function NewAppointmentMail($SLID_REF,$BRID_REF,$Appoint_Date,$Appoint_Time){

        $objPat = DB::table('TBL_MST_CUSTOMER')
                ->where('SLID_REF','=',$SLID_REF)
                ->select('NAME','EMAILID')
                ->first();

               
                $objCli = DB::table('TBL_MST_BRANCH')
                ->where('BRID','=',$BRID_REF)
                ->select('BRNAME','EMAILID','WEBSITE')
                ->first();

                $Clinic     =   $objCli->BRNAME;
                $appDate    =   date('m-d-Y',strtotime($Appoint_Date));
               // $appTime    =   date('H:i A',strtotime($Appoint_Time)); 
                $appTime    =   $Appoint_Time; 
                $email      =   trim($objPat->EMAILID);
                $name       =   $objPat->NAME;

                $subject    =   'Booking appointment with '.$Clinic;

                $body=' <!DOCTYPE html>
                <html lang="en">
                <head>
                <meta charset="utf-8">
                <title>'.$Clinic.'</title>
                </head>
                <body style="font-family:arial">
                <div style="width:700px; background:#fff; box-shadow: 0 0 25px #111;box-shadow: 1px 1px 40px #b7b7b7;border-radius: 10px;margin: auto;">
                
                <div style="background:#273f5b; border-radius:10px 10px 0 0; padding:20px">

                



                <center></center>
                </div>
                
                <div style="padding:30px 30px 50px 30px">
                <p style="font-size:14px; color:#6c6b6b"><strong>Hello '.$name.',</strong></p>
                
                
                
                <p style="font-size:14px; color:#6c6b6b">We have received your request for an appointment with us at '.$Clinic.'  on '.$appDate.' at '.$appTime.'.</p>
                
                <p><a href="#" style="font-size:14px; color:#ffa600;text-decoration:none; font-weight:600"></a></p>
                
                <p style="font-size:14px; color:#6c6b6b">Thanks for choosing '.$Clinic.'.</p>
                
                <p style="font-size:14px; color:#6c6b6b">For cancellation or rescheduling kindly visit <a href="'.$objCli->WEBSITE.'">'.$objCli->WEBSITE.'</a></p>
                
                </div>
                
                <div style="background:#273f5b; border-radius:0 0 10px 10px; padding:20px">
                <p style="font-size:14px; color:#fff"   >If you have any questions, health concerns or general inquiries Please contact our team at  <a style="color: white">'.$objCli->EMAILID.'</a></p>
                
                <p style="font-size:14px; color:#fff">Regards,<br>
                Team  '.$Clinic.'</p>
                </div>
                
                </div><!--main-div-->
                </body>
                </html>  
            ';



        return  $this->sendmail($email,$name,$subject,$body);

    }

    public function NewAppointmentMailProvider($SLID_REF,$BRID_REF,$Appoint_Date,$Appoint_Time){

        $objPat = DB::table('TBL_MST_CUSTOMER')
                ->where('SLID_REF','=',$SLID_REF)
                ->select('NAME','EMAILID')
                ->first();

               
               $objCli = DB::table('TBL_MST_BRANCH')
                ->where('BRID','=',$BRID_REF)
                ->select('BRNAME','EMAILID','WEBSITE')
                ->first();

                $Clinic     =   $objCli->BRNAME;
                $appDate    =   date('m-d-Y',strtotime($Appoint_Date));
               // $appTime    =   date('H:i A',strtotime($Appoint_Time)); 
                $appTime    =   $Appoint_Time; 
                $email      =   trim($objPat->EMAILID);
                $name       =   $objPat->NAME;
                $subject    =   'Booking appointment with '.$Clinic;

                
            $body=' <!DOCTYPE html>
            <html lang="en">
            <head>
            <meta charset="utf-8">
            <title>'.$Clinic.'</title>
            </head>
            <body style="font-family:arial">
            <div style="width:700px; background:#fff; box-shadow: 0 0 25px #111;box-shadow: 1px 1px 40px #b7b7b7;border-radius: 10px;margin: auto;">
            
            <div style="background:#273f5b; border-radius:10px 10px 0 0; padding:20px">
            <center></center>
            </div>
            
            <div style="padding:30px 30px 50px 30px">
            <p style="font-size:14px; color:#6c6b6b"><strong>Hello '.$Clinic.',</strong></p>
            
            
            
            <p style="font-size:14px; color:#6c6b6b">We have received your request for an appointment with Customer '.$name.' at '.$Clinic.'  on '.$appDate.' at '.$appTime.'.</p>
            
            <p><a href="#" style="font-size:14px; color:#ffa600;text-decoration:none; font-weight:600"></a></p>
            
            <p style="font-size:14px; color:#6c6b6b">Thanks for choosing '.$Clinic.'.</p>
            
            <p style="font-size:14px; color:#6c6b6b">For cancellation or rescheduling kindly visit <a href="'.$objCli->WEBSITE.'">'.$objCli->WEBSITE.'</a></p>
            
            </div>
            
            <div style="background:#273f5b; border-radius:0 0 10px 10px; padding:20px">
            <p style="font-size:14px; color:#fff">If you have any questions, health concerns or general inquiries Please contact our team at  <a style="color: white">'.$objCli->EMAILID.'</a></p>
            
            <p style="font-size:14px; color:#fff">Regards,<br>
            Team  '.$Clinic.'</p>
            </div>
            
            </div><!--main-div-->
            </body>
            </html>  
            ';

        return  $this->sendmail($email,$name,$subject,$body);

    }

    public function UpdateAppointmentMail($SLID_REF,$BRID_REF,$Appoint_Date,$Appoint_Time,$PreDATE,$PreTIME){


        $objPat = DB::table('TBL_MST_CUSTOMER')
        ->where('SLID_REF','=',$SLID_REF)
        ->select('NAME','EMAILID')
        ->first();


        $objCli = DB::table('TBL_MST_GROUPOFFICE')
        ->where('GOID','=',$GOID_REF)
        ->select('PRACTICE_GROUPNAME')
        ->first();

        $Clinic     =   $objCli->PRACTICE_GROUPNAME;
        $Droctor    =   $objPro->LAST_NAME;
        $appDate    =   date('m-d-Y',strtotime($Appoint_Date));
        $appTime    =   $Appoint_Time; 
        //$appTime    =   date('H:i A',strtotime($Appoint_Time)); 
        $preDate    =   date('m-d-Y',strtotime($PreDATE));
        $preTime    =   $PreTIME;
       // $preTime    =   date('H:i A',strtotime($PreTIME));
        $email      =   trim($objPat->EMAIL);
        $name       =   $objPat->FIRST_NAME;


        $subject    =   'Rescheduled appointment with '.$this->groupoffice_details()->PRACTICE_GROUPNAME;

        
        $body=' <!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="utf-8">
        <title>'.$this->groupoffice_details()->PRACTICE_GROUPNAME.'</title>
        </head>
        <body style="font-family:arial">
        <div style="width:700px; background:#fff; box-shadow: 0 0 25px #111;box-shadow: 1px 1px 40px #b7b7b7;border-radius: 10px;margin: auto;">
        
        <div style="background:#273f5b; border-radius:10px 10px 0 0; padding:20px">
        <center><img src="'.asset($this->groupoffice_details()->LOGO).'" style="border-radius:5px"></center>
        </div>
        
        <div style="padding:30px 30px 50px 30px">
        <p style="font-size:14px; color:#6c6b6b"><strong>Hello '.$name.',</strong></p>
        
        
        
        <p style="font-size:14px; color:#6c6b6b">We have received your request for an appointment with Dr. '.$Droctor.' at '.$Clinic.'  on '.$preDate.' at '.$preTime.'  has been rescheduled to '.$appDate.' at '.$appTime.'..</p>
        
        <p><a href="#" style="font-size:14px; color:#ffa600;text-decoration:none; font-weight:600"></a></p>
        
        <p style="font-size:14px; color:#6c6b6b">Kindly make the  online payment after login to your panel from view appointment section .</p>

       
                                                        
        '.$VideoLink.'
        
        
        
        <p style="font-size:14px; color:#6c6b6b">Thanks for choosing '.$this->groupoffice_details()->PRACTICE_GROUPNAME.'.</p>
        
        <p style="font-size:14px; color:#6c6b6b">For cancellation or rescheduling kindly visit <a href="'.$this->groupoffice_details()->WEBSITE.'">'.$this->groupoffice_details()->WEBSITE.'</a></p>
        
        </div>
        
        <div style="background:#273f5b; border-radius:0 0 10px 10px; padding:20px">
        <p style="font-size:14px; color:#fff">If you have any questions, health concerns or general inquiries Please contact our team at <a style="color: white">'.$this->groupoffice_details()->EMAIL_ID.'</a></p>
        
        <p style="font-size:14px; color:#fff">Regards,<br>
        Team  '.$this->groupoffice_details()->PRACTICE_GROUPNAME.'</p>
        </div>
        
        </div><!--main-div-->
        </body>
        </html>  
    ';



        return  $this->sendmail($email,$name,$subject,$body);

    }

    public function UpdateAppointmentMailProvider($PATIENTID_REF,$PROVIDERID_REF,$GOID_REF,$Appoint_Date,$Appoint_Time,$PreDATE,$PreTIME,$VideoLink){


        $objPat = DB::table('TBL_MST_PATIENT')
        ->where('PATIENTID','=',$PATIENTID_REF)
        ->select('FIRST_NAME','LAST_NAME','EMAIL')
        ->first();

        $objPro = DB::table('TBL_MST_PROVIDER')
        ->where('PROVIDERID','=',$PROVIDERID_REF)
        ->select('FIRST_NAME','LAST_NAME','EMAIL')
        ->first();

        $objCli = DB::table('TBL_MST_GROUPOFFICE')
        ->where('GOID','=',$GOID_REF)
        ->select('PRACTICE_GROUPNAME')
        ->first();

        $Clinic     =   $objCli->PRACTICE_GROUPNAME;
        $Patient    =   $objPat->FIRST_NAME.' '.$objPat->LAST_NAME;
        $appDate    =   date('m-d-Y',strtotime($Appoint_Date));
        $appTime    =   date('H:i A',strtotime($Appoint_Time)); 
        $preDate    =   date('m-d-Y',strtotime($PreDATE));
        $preTime    =   date('H:i A',strtotime($PreTIME));
        $email      =   trim($objPro->EMAIL);
        $name       =   $objPro->LAST_NAME;
       
        $subject    =   'Rescheduled appointment with '.$this->groupoffice_details()->PRACTICE_GROUPNAME;

        $body=' <!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="utf-8">
        <title>'.$this->groupoffice_details()->PRACTICE_GROUPNAME.'</title>
        </head>
        <body style="font-family:arial">
        <div style="width:700px; background:#fff; box-shadow: 0 0 25px #111;box-shadow: 1px 1px 40px #b7b7b7;border-radius: 10px;margin: auto;">
        
        <div style="background:#273f5b; border-radius:10px 10px 0 0; padding:20px">
        <center><img src="'.asset($this->groupoffice_details()->LOGO).'" style="border-radius:5px"></center>
        </div>
        
        <div style="padding:30px 30px 50px 30px">
        <p style="font-size:14px; color:#6c6b6b"><strong>Hello . '.$name.',</strong></p>
        
        
        
        <p style="font-size:14px; color:#6c6b6b">Your  request for an appointment with Patient '.$Patient.' at '.$Clinic.' Clinic on '.$appDate.' at '.$appTime.' has been Cancelled.</p>
        <p>For new booking / rescheduling kindly visit <a href="'.$this->groupoffice_details()->WEBSITE.'">'.$this->groupoffice_details()->WEBSITE.'</a></p>
        
                                                               
        '.$VideoLink.'
        
        
        
        <p style="font-size:14px; color:#6c6b6b">Thanks for choosing '.$this->groupoffice_details()->PRACTICE_GROUPNAME.'.</p>
        
        <p style="font-size:14px; color:#6c6b6b">For cancellation or rescheduling kindly visit <a href="'.$this->groupoffice_details()->WEBSITE.'">'.$this->groupoffice_details()->WEBSITE.'</a></p>
        
        </div>
        
        <div style="background:#273f5b; border-radius:0 0 10px 10px; padding:20px">
        <p style="font-size:14px; color:#fff">If you have any questions, health concerns or general inquiries Please contact our team at <a style="color: white">'.$this->groupoffice_details()->EMAIL_ID.'</a></p>
        
        <p style="font-size:14px; color:#fff">Regards,<br>
        Team  '.$this->groupoffice_details()->PRACTICE_GROUPNAME.'</p>
        </div>
        
        </div><!--main-div-->
        </body>
        </html>  
    ';
        return  $this->sendmail($email,$name,$subject,$body);

    }

    public function CancelAppointmentMail($APPOINTMENT_TRNID){

        $objApp = DB::table('TBL_TRN_APPOINTMENT')    
        ->where('APPOINTMENT_TRNID','=',$APPOINTMENT_TRNID)           
        ->select('PATIENTID_REF','PROVIDERID_REF','GOID_REF','DATE','TIME')
        ->first();

        $PATIENTID_REF  =   $objApp->PATIENTID_REF;
        $PROVIDERID_REF =   $objApp->PROVIDERID_REF;
        $GOID_REF       =   $objApp->GOID_REF;
        $Appoint_Date   =   $objApp->DATE;
        $Appoint_Time   =   $objApp->TIME;

        $objPat = DB::table('TBL_MST_PATIENT')
                ->where('PATIENTID','=',$PATIENTID_REF)
                ->select('FIRST_NAME','EMAIL')
                ->first();

                $objPro = DB::table('TBL_MST_PROVIDER')
                ->where('PROVIDERID','=',$PROVIDERID_REF)
                ->select('FIRST_NAME','LAST_NAME')
                ->first();

                $objCli = DB::table('TBL_MST_GROUPOFFICE')
                ->where('GOID','=',$GOID_REF)
                ->select('PRACTICE_GROUPNAME')
                ->first();

                $Clinic     =   $objCli->PRACTICE_GROUPNAME;
                $Droctor    =   $objPro->LAST_NAME;
                $appDate    =   date('m/d/Y',strtotime($Appoint_Date));
                $appTime    =   date('H:i A',strtotime($Appoint_Time)); 
                $email      =   trim($objPat->EMAIL);
                $name       =   $objPat->FIRST_NAME;
                $subject    =   'Cancelled appointment with '.$this->groupoffice_details()->PRACTICE_GROUPNAME;



                $body=' <!DOCTYPE html>
                <html lang="en">
                <head>
                <meta charset="utf-8">
                <title>'.$this->groupoffice_details()->PRACTICE_GROUPNAME.'</title>
                </head>
                <body style="font-family:arial">
                <div style="width:700px; background:#fff; box-shadow: 0 0 25px #111;box-shadow: 1px 1px 40px #b7b7b7;border-radius: 10px;margin: auto;">
                
                <div style="background:#273f5b; border-radius:10px 10px 0 0; padding:20px">
                <center><img src="'.asset($this->groupoffice_details()->LOGO).'" style="border-radius:5px"></center>
                </div>
                
                <div style="padding:30px 30px 50px 30px">
                <p style="font-size:14px; color:#6c6b6b"><strong>Hello  '.$name.',</strong></p>
                
                
                
                <p style="font-size:14px; color:#6c6b6b">Your  request for an appointment with Dr. '.$Droctor.' at '.$Clinic.' Clinic on '.$appDate.' at '.$appTime.' has been Cancelled.</p>
                <p>For new booking / rescheduling kindly visit <a href="'.$this->groupoffice_details()->WEBSITE.'">'.$this->groupoffice_details()->WEBSITE.'</a></p>
                
                                                                       
           
                
                
                
                <p style="font-size:14px; color:#6c6b6b">Thanks for choosing '.$this->groupoffice_details()->PRACTICE_GROUPNAME.'.</p>
                
                <p style="font-size:14px; color:#6c6b6b">For cancellation or rescheduling kindly visit <a href="'.$this->groupoffice_details()->WEBSITE.'">'.$this->groupoffice_details()->WEBSITE.'</a></p>
                
                </div>
                
                <div style="background:#273f5b; border-radius:0 0 10px 10px; padding:20px">
                <p style="font-size:14px; color:#fff">If you have any questions, health concerns or general inquiries Please contact our team at  <a style="color: white">'.$this->groupoffice_details()->EMAIL_ID.'</a></p>
                
                <p style="font-size:14px; color:#fff">Regards,<br>
                Team  '.$this->groupoffice_details()->PRACTICE_GROUPNAME.'</p>
                </div>
                
                </div><!--main-div-->
                </body>
                </html>  
            ';


               





        return  $this->sendmail($email,$name,$subject,$body);

    }

    public function CancelAppointmentMailProvider($APPOINTMENT_TRNID){

        $objApp = DB::table('TBL_TRN_APPOINTMENT')    
        ->where('APPOINTMENT_TRNID','=',$APPOINTMENT_TRNID)           
        ->select('PATIENTID_REF','PROVIDERID_REF','GOID_REF','DATE','TIME')
        ->first();

        $PATIENTID_REF  =   $objApp->PATIENTID_REF;
        $PROVIDERID_REF =   $objApp->PROVIDERID_REF;
        $GOID_REF       =   $objApp->GOID_REF;
        $Appoint_Date   =   $objApp->DATE;
        $Appoint_Time   =   $objApp->TIME;

        $objPat = DB::table('TBL_MST_PATIENT')
                ->where('PATIENTID','=',$PATIENTID_REF)
                ->select('FIRST_NAME','LAST_NAME','EMAIL')
                ->first();

                $objPro = DB::table('TBL_MST_PROVIDER')
                ->where('PROVIDERID','=',$PROVIDERID_REF)
                ->select('FIRST_NAME','LAST_NAME','EMAIL')
                ->first();

                $objCli = DB::table('TBL_MST_GROUPOFFICE')
                ->where('GOID','=',$GOID_REF)
                ->select('PRACTICE_GROUPNAME')
                ->first();

                $Clinic     =   $objCli->PRACTICE_GROUPNAME;
                $Patient    =   $objPat->FIRST_NAME.' '.$objPat->LAST_NAME;
                $appDate    =   date('m/d/Y',strtotime($Appoint_Date));
                $appTime    =   date('H:i A',strtotime($Appoint_Time)); 
                $email      =   trim($objPro->EMAIL);
                $name       =   $objPro->LAST_NAME;



      
                
                $subject    =   'Cancelled appointment with '.$this->groupoffice_details()->PRACTICE_GROUPNAME;



                $body=' <!DOCTYPE html>
                <html lang="en">
                <head>
                <meta charset="utf-8">
                <title>'.$this->groupoffice_details()->PRACTICE_GROUPNAME.'</title>
                </head>
                <body style="font-family:arial">
                <div style="width:700px; background:#fff; box-shadow: 0 0 25px #111;box-shadow: 1px 1px 40px #b7b7b7;border-radius: 10px;margin: auto;">
                
                <div style="background:#273f5b; border-radius:10px 10px 0 0; padding:20px">
                <center><img src="'.asset($this->groupoffice_details()->LOGO).'" style="border-radius:5px"></center>
                </div>
                
                <div style="padding:30px 30px 50px 30px">
                <p style="font-size:14px; color:#6c6b6b"><strong>Hello Dr. . '.$name.',</strong></p>
                
                
                
                <p style="font-size:14px; color:#6c6b6b">Your  request for an appointment with Patient'.$Patient.' at '.$Clinic.' Clinic on '.$appDate.' at '.$appTime.' has been Cancelled.</p>
                <p>For new booking / rescheduling kindly visit <a href="'.$this->groupoffice_details()->WEBSITE.'">'.$this->groupoffice_details()->WEBSITE.'</a></p>
                
                                                                       
           
                
                
                
                <p style="font-size:14px; color:#6c6b6b">Thanks for choosing '.$this->groupoffice_details()->PRACTICE_GROUPNAME.'.</p>
                
                <p style="font-size:14px; color:#6c6b6b">For cancellation or rescheduling kindly visit <a href="'.$this->groupoffice_details()->WEBSITE.'">'.$this->groupoffice_details()->WEBSITE.'</a></p>
                
                </div>
                
                <div style="background:#273f5b; border-radius:0 0 10px 10px; padding:20px">
                <p style="font-size:14px; color:#fff">If you have any questions, health concerns or general inquiries Please contact our team at  <a style="color: white">'.$this->groupoffice_details()->EMAIL_ID.'</a></p>
                
                <p style="font-size:14px; color:#fff">Regards,<br>
                Team  '.$this->groupoffice_details()->PRACTICE_GROUPNAME.'</p>
                </div>
                
                </div><!--main-div-->
                </body>
                </html>  
            ';









        return  $this->sendmail($email,$name,$subject,$body);

    }


    public function AcceptAppointmentMail($PATIENTID_REF,$PROVIDERID_REF,$GOID_REF,$Appoint_Date,$Appoint_Time,$VideoLink){

        $objPat = DB::table('TBL_MST_PATIENT')
                ->where('PATIENTID','=',$PATIENTID_REF)
                ->select('FIRST_NAME','EMAIL')
                ->first();

                $objPro = DB::table('TBL_MST_PROVIDER')
                ->where('PROVIDERID','=',$PROVIDERID_REF)
                ->select('FIRST_NAME','LAST_NAME')
                ->first();

                $objCli = DB::table('TBL_MST_GROUPOFFICE')
                ->where('GOID','=',$GOID_REF)
                ->select('PRACTICE_GROUPNAME')
                ->first();

                $Clinic     =   $objCli->PRACTICE_GROUPNAME;
                $Droctor    =   $objPro->LAST_NAME;
                $appDate    =   date('m/d/Y',strtotime($Appoint_Date));
                $appTime    =   date('H:i A',strtotime($Appoint_Time)); 
                $email      =   trim($objPat->EMAIL);
                $name       =   $objPat->FIRST_NAME;
        
                
                $subject    =   'Booking appointment with '.$this->groupoffice_details()->PRACTICE_GROUPNAME;

              

                $body=' <!DOCTYPE html>
                <html lang="en">
                <head>
                <meta charset="utf-8">
                <title>'.$this->groupoffice_details()->PRACTICE_GROUPNAME.'</title>
                </head>
                <body style="font-family:arial">
                <div style="width:700px; background:#fff; box-shadow: 0 0 25px #111;box-shadow: 1px 1px 40px #b7b7b7;border-radius: 10px;margin: auto;">
                
                <div style="background:#273f5b; border-radius:10px 10px 0 0; padding:20px">
                <center><img src="'.asset($this->groupoffice_details()->LOGO).'" style="border-radius:5px"></center>
                </div>
                
                <div style="padding:30px 30px 50px 30px">
                <p style="font-size:14px; color:#6c6b6b"><strong>Hello '.$name.',</strong></p>
                
                
                
                <p style="font-size:14px; color:#6c6b6b">Your  request for an appointment with  Dr. '.$Droctor.' at '.$Clinic.' Clinic on '.$appDate.' at '.$appTime.' has been accepted.</p>
                <p>Kindly make the  online payment after login to your panel from view appointment section .</p>

                '.$VideoLink.'

                <p>For new booking / rescheduling kindly visit <a href="'.$this->groupoffice_details()->WEBSITE.'">'.$this->groupoffice_details()->WEBSITE.'</a></p>
                
                                                                       
           
                
                
                
                <p style="font-size:14px; color:#6c6b6b">Thanks for choosing '.$this->groupoffice_details()->PRACTICE_GROUPNAME.'.</p>
                
                <p style="font-size:14px; color:#6c6b6b">For cancellation or rescheduling kindly visit <a href="'.$this->groupoffice_details()->WEBSITE.'">'.$this->groupoffice_details()->WEBSITE.'</a></p>
                
                </div>
                
                <div style="background:#273f5b; border-radius:0 0 10px 10px; padding:20px">
                <p style="font-size:14px; color:#fff">If you have any questions, health concerns or general inquiries Please contact our team at  <a style="color: white">'.$this->groupoffice_details()->EMAIL_ID.'</a></p>
                
                <p style="font-size:14px; color:#fff">Regards,<br>
                Team  '.$this->groupoffice_details()->PRACTICE_GROUPNAME.'</p>
                </div>
                
                </div><!--main-div-->
                </body>
                </html>  
            ';






        return  $this->sendmail($email,$name,$subject,$body);

    }

    

    public function AcceptAppointmentMailProvider($PATIENTID_REF,$PROVIDERID_REF,$GOID_REF,$Appoint_Date,$Appoint_Time,$VideoLink){

        $objPat = DB::table('TBL_MST_PATIENT')
                ->where('PATIENTID','=',$PATIENTID_REF)
                ->select('FIRST_NAME','LAST_NAME','EMAIL')
                ->first();

                $objPro = DB::table('TBL_MST_PROVIDER')
                ->where('PROVIDERID','=',$PROVIDERID_REF)
                ->select('FIRST_NAME','LAST_NAME','EMAIL')
                ->first();

                $objCli = DB::table('TBL_MST_GROUPOFFICE')
                ->where('GOID','=',$GOID_REF)
                ->select('PRACTICE_GROUPNAME')
                ->first();

                $Clinic     =   $objCli->PRACTICE_GROUPNAME;
                $Patient    =   $objPat->FIRST_NAME.' '.$objPat->LAST_NAME;
                $appDate    =   date('m/d/Y',strtotime($Appoint_Date));
                $appTime    =   date('H:i A',strtotime($Appoint_Time)); 
                $email      =   trim($objPro->EMAIL);
                $name       =   $objPro->LAST_NAME;
                $subject    =   "Booking appointment with YourClinic.app";

                

              

                $body=' <!DOCTYPE html>
                <html lang="en">
                <head>
                <meta charset="utf-8">
                <title>'.$this->groupoffice_details()->PRACTICE_GROUPNAME.'</title>
                </head>
                <body style="font-family:arial">
                <div style="width:700px; background:#fff; box-shadow: 0 0 25px #111;box-shadow: 1px 1px 40px #b7b7b7;border-radius: 10px;margin: auto;">
                
                <div style="background:#273f5b; border-radius:10px 10px 0 0; padding:20px">
                <center><img src="'.asset($this->groupoffice_details()->LOGO).'" style="border-radius:5px"></center>
                </div>
                
                <div style="padding:30px 30px 50px 30px">
                <p style="font-size:14px; color:#6c6b6b"><strong>Hello Dr. '.$name.',</strong></p>
                
                
                
                <p style="font-size:14px; color:#6c6b6b">Your  request for an appointment with  Patient. '.$Patient.' at '.$Clinic.' Clinic on '.$appDate.' at '.$appTime.' has been accepted.</p>
                <p>Kindly make the  online payment after login to your panel from view appointment section .</p>

                '.$VideoLink.'

                <p>For new booking / rescheduling kindly visit <a href="'.$this->groupoffice_details()->WEBSITE.'">'.$this->groupoffice_details()->WEBSITE.'</a></p>
                
                                                                       
           
                
                
                
                <p style="font-size:14px; color:#6c6b6b">Thanks for choosing YourClinic.app.</p>
                
                <p style="font-size:14px; color:#6c6b6b">For cancellation or rescheduling kindly visit <a href="'.$this->groupoffice_details()->WEBSITE.'">'.$this->groupoffice_details()->WEBSITE.'</a></p>
                </div>
                
                <div style="background:#273f5b; border-radius:0 0 10px 10px; padding:20px">
                <p style="font-size:14px; color:#fff">If you have any questions, health concerns or general inquiries Please contact our team at <a style="color: white">'.$this->groupoffice_details()->EMAIL_ID.'</a></p>
                
                <p style="font-size:14px; color:#fff">Regards,<br>
                Team  '.$this->groupoffice_details()->PRACTICE_GROUPNAME.'</p>
                </div>
                
                </div><!--main-div-->
                </body>
                </html>  
            ';




        return  $this->sendmail($email,$name,$subject,$body);

    }

    

    public function sendmail($email,$name,$subject,$body){
        $mail                   =   new PHPMailer\PHPMailer();
        //$mail->SMTPDebug        =   1;
        $mail->SMTPAuth         =   true;
        $mail-> isSMTP();
        $mail->Host             =   Session::get('smtp_config')['host'];
        $mail->Port             =   Session::get('smtp_config')['port'];
        $mail->IsHTML(true);
        $mail->Username         =   Session::get('smtp_config')['username'];
        $mail->Password         =  Session::get('smtp_config')['password'];
        $mail->SetFrom(Session::get('smtp_config')['from']);
        $mail->Subject  =   $subject;
        $mail->Body     =   $body;
        $mail->AddAddress($email, $name);
        return $mail->Send();
    }


    public function ViewReport($request){

        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
		
        $APPOINTMENT_TRNID       =   $myValue['APPOINTMENT_TRNID'];
        $Flag       =   $myValue['Flag'];
        
        $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
		$result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/Appoitnment_Booking_Print');
        
        $reportParameters = array(
            'APPOINTMENT_TRNID' => $APPOINTMENT_TRNID,
        );
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
        ->setExecutionParameters($parameters);
        if($Flag == 'H')
        {
            $output = $ssrs->render('HTML4.0'); // PDF | XML | CSV
            echo $output;
        }
        else if($Flag == 'P')
        {
            $output = $ssrs->render('PDF'); // PDF | XML | CSV | HTML4.0
            return $output->download('Report.pdf');
        }
        else if($Flag == 'E')
        {
            $output = $ssrs->render('EXCEL'); // PDF | XML | CSV | HTML4.0
            return $output->download('Report.xls');
        }
        else if($Flag == 'R')
        {
            $output = $ssrs->render('HTML4.0'); // PDF | XML | CSV | HTML4.0
            echo $output;

        }
         
     }
    

}

