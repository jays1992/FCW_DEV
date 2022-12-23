<?php
namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Helpers\Utils;

class MstFrm179Controller extends Controller{

    protected $form_id  = 179;
    protected $vtid_ref = 193;
    protected $view     = "masters.Payroll.EmployeeMaster.mstfrm";
    
    protected   $messages = [
            'EMPCODE.required'   => 'Required field',
            'EMPCODE.unique'     => 'Duplicate Code',
            'FNAME.required'   => 'Required field',
        ];

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        $objRights      =   DB::table('TBL_MST_USERROLMAP')
                            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
                            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                            ->first();
        
        $FormId         =   $this->form_id;

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $objDataList    =   DB::select("SELECT  T1.* FROM TBL_MST_EMPLOYEE T1
        INNER JOIN TBL_MST_EMP_BRANCH_MAP T2 ON T1.EMPID=T2.EMPID_REF AND T1.BRID_REF=T2.MAPBRID_REF AND T1.CYID_REF=T2.CYID_REF 
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND (T2.DEACTIVATED=0 or T2.DEACTIVATED is null) ORDER BY T1.EMPID DESC");
                         
        return view($this->view.$FormId,compact(['objRights','objDataList','FormId']));

    }

    public function add(){
        $FormId                 =   $this->form_id;
        $getGender              =   $this->getGender();
        $getDesignation         =   $this->getDesignation();
        $getSalutation          =   $this->getSalutation();
        $getSalutation          =   $this->getSalutation();
        $getDepartment          =   $this->getDepartment();
        $getDivision            =   $this->getDivision();
        $getPostLevel           =   $this->getPostLevel();
        $getEmployeeCategory    =   $this->getEmployeeCategory();
        $getEmployeeType        =   $this->getEmployeeType();
        $getCostCentre          =   $this->getCostCentre();
        $getBloodGroup          =   $this->getBloodGroup();
        $getGrade               =   $this->getGrade();
        $getBranch              =   $this->getBranch();
        $getRelationShip        =   $this->getRelationShip();
        $objCountryList         =   $this->getCountryList();
        
        //------------
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $objDD = DB::table('TBL_MST_EMPLOYEECODE')
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->where('FYID_REF','=',$FYID_REF)
                ->where('STATUS','=','A')
                ->select('TBL_MST_EMPLOYEECODE.*')
                ->first();

        $objDOCNO ='';
        if(!empty($objDD)){
            if($objDD->SYSTEM_GRSR == "1")
            {               
                $objDOCNO = $objDD->PREFIX;              
                $objDOCNO = $objDOCNO.str_pad($objDD->LAST_RECORDNO+1, $objDD->MAX_DIGIT, "0", STR_PAD_LEFT);               
            }
        }   

       //------------

        return view($this->view.$FormId.'add',compact([
            'FormId',
            'getGender',
            'getDesignation',
            'getSalutation',
            'getDepartment',
            'getDivision',
            'getPostLevel',
            'getEmployeeCategory',
            'getEmployeeType',
            'getCostCentre',
            'getBloodGroup',
            'getGrade',
            'getBranch',
            'getRelationShip',
            'objCountryList',
            'objDD','objDOCNO'
        ])); 
    }

    public function save(Request $request){

        $rules = [
            'EMPCODE' => 'required',
            'FNAME' => 'required',      
        ];

        $req_data = [
            'EMPCODE'     =>    $request['EMPCODE'],
            'FNAME' =>   $request['FNAME']
        ]; 

        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails()){
            return Response::json(['errors' => $validator->errors()]);	
        }

        $EMPCODE             =   strtoupper(trim($request['EMPCODE']) );
        $FNAME               =   trim($request['FNAME']);  
        $MNAME             =   (isset($request['MNAME']) && trim($request['MNAME']) !="" )? trim($request['MNAME']) : NULL ;
        $LNAME             =   (isset($request['LNAME']) && trim($request['LNAME']) !="" )? trim($request['LNAME']) : NULL ;
        $OLDREFNO          =   (isset($request['OLDREFNO']) && trim($request['OLDREFNO']) !="" )? trim($request['OLDREFNO']) : NULL ;
        $GID_REF             =   (isset($request['GID_REF']) && trim($request['GID_REF']) !="" )? trim($request['GID_REF']) : NULL ;
        $DESGID_REF             =   (isset($request['DESGID_REF']) && trim($request['DESGID_REF']) !="" )? trim($request['DESGID_REF']) : NULL ;
        $SAID_REF             =   (isset($request['SAID_REF']) && trim($request['SAID_REF']) !="" )? trim($request['SAID_REF']) : NULL ;
        $DEPID_REF             =   (isset($request['DEPID_REF']) && trim($request['DEPID_REF']) !="" )? trim($request['DEPID_REF']) : NULL ;
        $DIVID_REF             =   (isset($request['DIVID_REF']) && trim($request['DIVID_REF']) !="" )? trim($request['DIVID_REF']) : NULL ;
        
        $PLID_REF             =   (isset($request['PLID_REF']) && trim($request['PLID_REF']) !="" )? trim($request['PLID_REF']) : NULL ;
        $DOJ             =   (isset($request['DOJ']) && trim($request['DOJ']) !="" )? date("Y-m-d",strtotime($request['DOJ'])) : NULL ;
        $CATID_REF             =   (isset($request['CATID_REF']) && trim($request['CATID_REF']) !="" )? trim($request['CATID_REF']) : NULL ;
        $ETYPEID_REF             =   (isset($request['ETYPEID_REF']) && trim($request['ETYPEID_REF']) !="" )? trim($request['ETYPEID_REF']) : NULL ;
        $FATHERNAME             =   (isset($request['FATHERNAME']) && trim($request['FATHERNAME']) !="" )? trim($request['FATHERNAME']) : NULL ;
        $CCID_REF             =   (isset($request['CCID_REF']) && trim($request['CCID_REF']) !="" )? trim($request['CCID_REF']) : NULL ;
        $DOB             =   (isset($request['DOB']) && trim($request['DOB']) !="" )? date("Y-m-d",strtotime($request['DOB'])) : NULL ;
        $DOBPLACE             =   (isset($request['DOBPLACE']) && trim($request['DOBPLACE']) !="" )? trim($request['DOBPLACE']) : NULL ;
        $BGID_REF             =   (isset($request['BGID_REF']) && trim($request['BGID_REF']) !="" )? trim($request['BGID_REF']) : NULL ;

        $GRADEID_REF             =   (isset($request['GRADEID_REF']) && trim($request['GRADEID_REF']) !="" )? trim($request['GRADEID_REF']) : NULL ;
        //$BRID_REF             =   (isset($request['BRID_REF']) && trim($request['BRID_REF']) !="" )? trim($request['BRID_REF']) : NULL ;
        $SALES_PERSON             =   (isset($request['SALES_PERSON']) && trim($request['SALES_PERSON']) !="" )? trim($request['SALES_PERSON']) : NULL ;


        $CRESIDENCE             =   (isset($request['CRESIDENCE']) )? 1 : 0 ;
        $CROWN                  =   (isset($request['CROWN']) )? 1 : 0 ;
        $CRRENTED               =   (isset($request['CRRENTED']) )? 1 : 0 ;
        $CRADD1                 =   (isset($request['CRADD1']) && trim($request['CRADD1']) !="" )? trim($request['CRADD1']) : NULL ;
        $CRADD2                 =   (isset($request['CRADD2']) && trim($request['CRADD2']) !="" )? trim($request['CRADD2']) : NULL ;
        $CRCITYID_REF           =   (isset($request['CITYID_REF']) && trim($request['CITYID_REF']) !="" )? trim($request['CITYID_REF']) : NULL ;
        $CRSTID_REF             =   (isset($request['STID_REF']) && trim($request['STID_REF']) !="" )? trim($request['STID_REF']) : NULL ;
        $CRPIN                  =   (isset($request['CRPIN']) && trim($request['CRPIN']) !="" )? trim($request['CRPIN']) : NULL ;
        $CRDISTID_REF           =   (isset($request['DISTID_REF']) && trim($request['DISTID_REF']) !="" )? trim($request['DISTID_REF']) : NULL ;
        $CRCTRYID_REF           =   (isset($request['CTRYID_REF']) && trim($request['CTRYID_REF']) !="" )? trim($request['CTRYID_REF']) : NULL ;
        $CRLM                   =   (isset($request['CRLM']) && trim($request['CRLM']) !="" )? trim($request['CRLM']) : NULL ;

        $PRRESIDENCE             =   (isset($request['PRRESIDENCE']) )? 1 : 0 ;
        $PROWN                  =   (isset($request['PROWN']) )? 1 : 0 ;
        $PRRENTED               =   (isset($request['PRRENTED']) )? 1 : 0 ;
        $PRADD1                 =   (isset($request['PRADD1']) && trim($request['PRADD1']) !="" )? trim($request['PRADD1']) : NULL ;
        $PRADD2                 =   (isset($request['PRADD2']) && trim($request['PRADD2']) !="" )? trim($request['PRADD2']) : NULL ;
        $PRCITYID_REF           =   (isset($request['CITYID_REF1']) && trim($request['CITYID_REF1']) !="" )? trim($request['CITYID_REF1']) : NULL ;
        $PRSTID_REF             =   (isset($request['STID_REF1']) && trim($request['STID_REF1']) !="" )? trim($request['STID_REF1']) : NULL ;
        $PRPIN                  =   (isset($request['PRPIN']) && trim($request['PRPIN']) !="" )? trim($request['PRPIN']) : NULL ;
        $PRDISTID_REF           =   (isset($request['DISTID_REF1']) && trim($request['DISTID_REF1']) !="" )? trim($request['DISTID_REF1']) : NULL ;
        $PRCTRYID_REF           =   (isset($request['CTRYID_REF1']) && trim($request['CTRYID_REF1']) !="" )? trim($request['CTRYID_REF1']) : NULL ;
        
        $PRLM                   =   (isset($request['PRLM']) && trim($request['PRLM']) !="" )? trim($request['PRLM']) : NULL ;
        $PANNO                   =   (isset($request['PANNO']) && trim($request['PANNO']) !="" )? trim($request['PANNO']) : NULL ;
        $AADHARNO                   =   (isset($request['AADHARNO']) && trim($request['AADHARNO']) !="" )? trim($request['AADHARNO']) : NULL ;
        $ELECTIONCNO                   =   (isset($request['ELECTIONCNO']) && trim($request['ELECTIONCNO']) !="" )? trim($request['ELECTIONCNO']) : NULL ;
        $ELECTIONCPOI                   =   (isset($request['ELECTIONCPOI']) && trim($request['ELECTIONCPOI']) !="" )? trim($request['ELECTIONCPOI']) : NULL ;
        $DLNO                   =   (isset($request['DLNO']) && trim($request['DLNO']) !="" )? trim($request['DLNO']) : NULL ;
        $DLPOI                   =   (isset($request['DLPOI']) && trim($request['DLPOI']) !="" )? trim($request['DLPOI']) : NULL ;
        $DLVALIDUPTO                   =   (isset($request['DLVALIDUPTO']) && trim($request['DLVALIDUPTO']) !="" )? trim($request['DLVALIDUPTO']) : NULL ;
        $PASSPORTNO                   =   (isset($request['PASSPORTNO']) && trim($request['PASSPORTNO']) !="" )? trim($request['PASSPORTNO']) : NULL ;
        $PASSPORTPOI                   =   (isset($request['PASSPORTPOI']) && trim($request['PASSPORTPOI']) !="" )? trim($request['PASSPORTPOI']) : NULL ;
       
        $PASSPORTVUPTO                   =   (isset($request['PASSPORTVUPTO']) && trim($request['PASSPORTVUPTO']) !="" )? trim($request['PASSPORTVUPTO']) : NULL ;
        $PFNO                   =   (isset($request['PFNO']) && trim($request['PFNO']) !="" )? trim($request['PFNO']) : NULL ;
        $ESINO                   =   (isset($request['ESINO']) && trim($request['ESINO']) !="" )? trim($request['ESINO']) : NULL ;
        $MSHIPNAME                   =   (isset($request['MSHIPNAME']) && trim($request['MSHIPNAME']) !="" )? trim($request['MSHIPNAME']) : NULL ;
        $MSHIPNO                   =   (isset($request['MSHIPNO']) && trim($request['MSHIPNO']) !="" )? trim($request['MSHIPNO']) : NULL ;
        $CONTRACTRNAME                   =   (isset($request['CONTRACTRNAME']) && trim($request['CONTRACTRNAME']) !="" )? trim($request['CONTRACTRNAME']) : NULL ;
        $NOMINEENAME                   =   (isset($request['NOMINEENAME']) && trim($request['NOMINEENAME']) !="" )? trim($request['NOMINEENAME']) : NULL ;
        $RSID_REF                   =   (isset($request['RSID_REF']) && trim($request['RSID_REF']) !="" )? trim($request['RSID_REF']) : NULL ;
        $BANK                   =   (isset($request['BANK']) && trim($request['BANK']) !="" )? trim($request['BANK']) : NULL ;
        $IFSC                   =   (isset($request['IFSC']) && trim($request['IFSC']) !="" )? trim($request['IFSC']) : NULL ;

        $BRANCH                   =   (isset($request['BRANCH']) && trim($request['BRANCH']) !="" )? trim($request['BRANCH']) : NULL ;
        $ACTYPE                   =   (isset($request['ACTYPE']) && trim($request['ACTYPE']) !="" )? trim($request['ACTYPE']) : NULL ;
        $ACNO                   =   (isset($request['ACNO']) && trim($request['ACNO']) !="" )? trim($request['ACNO']) : NULL ;
        $BRANCHCODE                   =   (isset($request['BRANCHCODE']) && trim($request['BRANCHCODE']) !="" )? trim($request['BRANCHCODE']) : NULL ;
        $LLNO                   =   (isset($request['LLNO']) && trim($request['LLNO']) !="" )? trim($request['LLNO']) : NULL ;
        $MONO1                   =   (isset($request['MONO1']) && trim($request['MONO1']) !="" )? trim($request['MONO1']) : NULL ;
        $MONO2                   =   (isset($request['MONO2']) && trim($request['MONO2']) !="" )? trim($request['MONO2']) : NULL ;
        $EMAIL                   =   (isset($request['EMAIL']) && trim($request['EMAIL']) !="" )? trim($request['EMAIL']) : NULL ;
        $PEMAIL                   =   (isset($request['PEMAIL']) && trim($request['PEMAIL']) !="" )? trim($request['PEMAIL']) : NULL ;
        $EMERGCPNAME                   =   (isset($request['EMERGCPNAME']) && trim($request['EMERGCPNAME']) !="" )? trim($request['EMERGCPNAME']) : NULL ;

        $EMERGCPMONO                   =   (isset($request['EMERGCPMONO']) && trim($request['EMERGCPMONO']) !="" )? trim($request['EMERGCPMONO']) : NULL ;
        $CRDISEASE1                   =   (isset($request['CRDISEASE1']) && trim($request['CRDISEASE1']) !="" )? trim($request['CRDISEASE1']) : NULL ;
        $CRDISEASE2                   =   (isset($request['CRDISEASE2']) && trim($request['CRDISEASE2']) !="" )? trim($request['CRDISEASE2']) : NULL ;
        $CRDISEASE3                   =   (isset($request['CRDISEASE3']) && trim($request['CRDISEASE3']) !="" )? trim($request['CRDISEASE3']) : NULL ;
        $ALLERGY                   =   (isset($request['ALLERGY']) && trim($request['ALLERGY']) !="" )? trim($request['ALLERGY']) : NULL ;
        $HEIGHT                   =   (isset($request['HEIGHT']) && trim($request['HEIGHT']) !="" )? trim($request['HEIGHT']) : NULL ;
        $WEIGHT                   =   (isset($request['WEIGHT']) && trim($request['WEIGHT']) !="" )? trim($request['WEIGHT']) : NULL ;
        $COLOR                   =   (isset($request['COLOR']) && trim($request['COLOR']) !="" )? trim($request['COLOR']) : NULL ;
        $RELIGION                   =   (isset($request['RELIGION']) && trim($request['RELIGION']) !="" )? trim($request['RELIGION']) : NULL ;
        $NANTIONALITY                   =   (isset($request['NANTIONALITY']) && trim($request['NANTIONALITY']) !="" )? trim($request['NANTIONALITY']) : NULL ;
       
        $HOBBIES                   =   (isset($request['HOBBIES']) && trim($request['HOBBIES']) !="" )? trim($request['HOBBIES']) : NULL ;
        $VEGETARIAN                   =   (isset($request['VEGETARIAN']) && trim($request['VEGETARIAN']) !="" )? trim($request['VEGETARIAN']) : NULL ;
        
        
        $Row_Count2 = $request['Row_Count2'];
        $data2 = array();
        for ($i=0; $i<=$Row_Count2; $i++){

            if((isset($request['FAMILY_NAME_'.$i]) && $request['FAMILY_NAME_'.$i] !="")){
                $data2[$i] = [
                    'NAME' => strtoupper(trim($request['FAMILY_NAME_'.$i])),
                    'DOB' => strtoupper(trim($request['FAMILY_DOB_'.$i])),
                    'GID_REF' => strtoupper(trim($request['FAMILY_GID_REF_'.$i])),
                    'RSID_REF' => strtoupper(trim($request['FAMILY_RSID_REF_'.$i])),
                    'EARNING' => strtoupper(trim($request['FAMILY_EARNING_'.$i])),
                    'CONTACTNO' => strtoupper(trim($request['FAMILY_CONTACTNO_'.$i])),
                    'EMAIL' => strtoupper(trim($request['FAMILY_EMAIL_'.$i])),
                   
                ];
            }
        }

        if(!empty($data2)){     
            $wrapped_links2["FAMILYMEMBERS"] = $data2; 
            $FAMILYXML = ArrayToXml::convert($wrapped_links2);
        }else{
            $FAMILYXML = NULL;
        }


        $Row_Count3 = $request['Row_Count3'];
        $data3 = array();
        for ($i=0; $i<=$Row_Count3; $i++){

            if((isset($request['EDUCATION_DEGREE_'.$i]) && $request['EDUCATION_DEGREE_'.$i] !="")){
                $data3[$i] = [
                    'DEGREE' => strtoupper(trim($request['EDUCATION_DEGREE_'.$i])),
                    'YOP' => strtoupper(trim($request['EDUCATION_YOP_'.$i])),
                    'UNIVERSITY' => strtoupper(trim($request['EDUCATION_UNIVERSITY_'.$i])),
                    'RESULT' => strtoupper(trim($request['EDUCATION_RESULT_'.$i])),
                    'REMARKS' => strtoupper(trim($request['EDUCATION_REMARKS_'.$i])),
                ];
            }
        }

        if(!empty($data3)){     
            $wrapped_links3["EDUCATION"] = $data3; 
            $EDUCATIONXML = ArrayToXml::convert($wrapped_links3);
        }else{
            $EDUCATIONXML = NULL;
        }


        $Row_Count4 = $request['Row_Count4'];
        $data4 = array();
        for ($i=0; $i<=$Row_Count4; $i++){

            if((isset($request['EXPERIENCE_CNAME_'.$i]) && $request['EXPERIENCE_CNAME_'.$i] !="")){
                $data4[$i] = [
                    'CNAME' => strtoupper(trim($request['EXPERIENCE_CNAME_'.$i])),
                    'FROMPD' => strtoupper(trim($request['EXPERIENCE_FROMPD_'.$i])),
                    'TOPD' => strtoupper(trim($request['EXPERIENCE_TOPD_'.$i])),
                    'LASTDESIG' => strtoupper(trim($request['EXPERIENCE_LASTDESIG_'.$i])),
                    'CTCPA' => strtoupper(trim($request['EXPERIENCE_CTCPA_'.$i])),
                    'REASONOFLEAVING' => strtoupper(trim($request['EXPERIENCE_REMARKS_'.$i])),
                ];
            }
        }

        if(!empty($data4)){     
            $wrapped_links4["EXPERIENCE"] = $data4; 
            $EXPERIENCEXML = ArrayToXml::convert($wrapped_links4);
        }else{
            $EXPERIENCEXML = NULL;
        }


        $Row_Count5 = $request['Row_Count5'];
        $data5 = array();
        for ($i=0; $i<=$Row_Count5; $i++){

            if((isset($request['REFERENCE_RNAME_'.$i]) && $request['REFERENCE_RNAME_'.$i] !="")){
                $data5[$i] = [
                    'RNAME' => strtoupper(trim($request['REFERENCE_RNAME_'.$i])),
                    'GID_REF' => strtoupper(trim($request['REFERENCE_GID_REF_'.$i])),
                    'COMPANY' => strtoupper(trim($request['REFERENCE_COMPANY_'.$i])),
                    'DESIG' => strtoupper(trim($request['REFERENCE_DESIG_'.$i])),
                    'MONO' => strtoupper(trim($request['REFERENCE_MONO_'.$i])),
                    'EMAIL' => strtoupper(trim($request['REFERENCE_EMAIL_'.$i])),
                ];
            }
        }

        if(!empty($data5)){     
            $wrapped_links5["REFERENCE"] = $data5; 
            $REFRENCEXML = ArrayToXml::convert($wrapped_links5);
        }else{
            $REFRENCEXML = NULL;
        }

        $Row_Count6 = $request['Row_Count6'];
        $data6 = array();
        for ($i=0; $i<=$Row_Count6; $i++){

            if((isset($request['EXTRACUR_NAME_'.$i]) && $request['EXTRACUR_NAME_'.$i] !="")){
                $data6[$i] = [
                    'NAME' => strtoupper(trim($request['EXTRACUR_NAME_'.$i])),
                    'PERIOD' => strtoupper(trim($request['EXTRACUR_PERIOD_'.$i])),
                    'LEVEL' => strtoupper(trim($request['EXTRACUR_LEVELS_'.$i])),
                    'ACHIEVEMNET' => strtoupper(trim($request['EXTRACUR_ACHIEVEMENT_'.$i])),
                ];
            }
        }

        if(!empty($data6)){     
            $wrapped_links6["EXTRACURRICULARACTIVITY"] = $data6; 
            $ECACTIVITYXML = ArrayToXml::convert($wrapped_links6);
        }else{
            $ECACTIVITYXML = NULL;
        }

        $DEACTIVATED    =   0;  
        $DODEACTIVATED  =   NULL;  

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');       
        $VTID           =   $this->vtid_ref;
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();

        $array_data   = [
            $EMPCODE,$FNAME, $MNAME,$LNAME,$OLDREFNO, $GID_REF, $DESGID_REF,$SAID_REF, $DEPID_REF,$DIVID_REF,
            $PLID_REF,$DOJ, $CATID_REF,$ETYPEID_REF,$FATHERNAME, $CCID_REF, $DOB,$DOBPLACE, $BGID_REF,$CRESIDENCE,
            $CROWN,$CRRENTED, $CRADD1,$CRADD2,$CRCITYID_REF, $CRSTID_REF, $CRPIN,$CRDISTID_REF, $CRCTRYID_REF,$CRLM,
            $PRRESIDENCE,$PROWN, $PRRENTED,$PRADD1,$PRADD2, $PRCITYID_REF, $PRSTID_REF,$PRPIN, $PRDISTID_REF,$PRCTRYID_REF,
            $PRLM,$PANNO, $AADHARNO,$ELECTIONCNO,$ELECTIONCPOI, $DLNO, $DLPOI,$DLVALIDUPTO, $PASSPORTNO,$PASSPORTPOI,
            $PASSPORTVUPTO,$PFNO, $ESINO,$MSHIPNAME,$MSHIPNO, $CONTRACTRNAME, $NOMINEENAME,$RSID_REF, $BANK,$IFSC,
            $BRANCH,$ACTYPE, $ACNO,$BRANCHCODE,$LLNO, $MONO1, $MONO2,$EMAIL, $PEMAIL,$EMERGCPNAME,
            $EMERGCPMONO,$CRDISEASE1, $CRDISEASE2,$CRDISEASE3,$ALLERGY, $HEIGHT, $WEIGHT,$COLOR, $RELIGION,$NANTIONALITY,
            $HOBBIES,$VEGETARIAN, $DEACTIVATED, $DODEACTIVATED,$GRADEID_REF,$SALES_PERSON,$CYID_REF,$BRID_REF,$FYID_REF,$FAMILYXML,
            $EDUCATIONXML,$EXPERIENCEXML,$REFRENCEXML,$ECACTIVITYXML,$VTID,$USERID,$UPDATE,$UPTIME, $ACTION, $IPADDRESS   
        ];

        //$sp_result = DB::select('EXEC SP_EMPLOYEE_IN ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?', $array_data);

        //dd($sp_result);

        try {

            $sp_result = DB::select('EXEC SP_EMPLOYEE_IN ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?', $array_data);
            //dd($sp_result);
        } catch (\Throwable $th) {
            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }

        if(Str::contains(strtoupper($sp_result[0]->RESULT), 'SUCCESS')){

            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }elseif(Str::contains(strtoupper($sp_result[0]->RESULT), 'DUPLICATE RECORD')){
        
            return Response::json(['errors'=>true,'msg' => $sp_result[0]->RESULT,'exist'=>'duplicate']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }
		
    
        // if($sp_result[0]->RESULT=="SUCCESS"){

        //     return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);

        // }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
        
        //     return Response::json(['errors'=>true,'msg' => 'Duplicate record.','exist'=>'duplicate']);
            
        // }else{

        //     return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        // }
        
        exit();    
    }

    public function edit($id){

        if(!is_null($id)){
        
            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $objResponse =  DB::table('TBL_MST_EMPLOYEE')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('EMPID','=',$id)
            ->first();

            if(strtoupper($objResponse->STATUS)=="A" || strtoupper($objResponse->STATUS)=="C"){
                exit("Sorry, Only Un Approved record can edit.");
            }

            $objTab1 =  DB::table('TBL_MST_EMPLOYEE_ADDRESS')
            ->where('EMPID_REF','=',$id)
            ->first();

            $objTab2 =  DB::table('TBL_MST_EMPLOYEE_FAMILY')
            ->where('EMPID_REF','=',$id)
            ->get()->toArray(); 

            $objTab3 =  DB::table('TBL_MST_EMPLOYEE_EDUCATION')
            ->where('EMPID_REF','=',$id)
            ->get()->toArray(); 

            $objTab4 =  DB::table('TBL_MST_EMPLOYEE_EXPERIENCE')
            ->where('EMPID_REF','=',$id)
            ->get()->toArray();
            
            $objTab5 =  DB::table('TBL_MST_EMPLOYEE_REFERENCE')
            ->where('EMPID_REF','=',$id)
            ->get()->toArray(); 
            
            $objTab6 =  DB::table('TBL_MST_EMPLOYEE_ACTIVITY')
            ->where('EMPID_REF','=',$id)
            ->get()->toArray();

            $objTab7 =  DB::table('TBL_MST_EMPLOYEE_LEGAL')
            ->where('EMPID_REF','=',$id)
            ->first();

            $objTab8 =  DB::table('TBL_MST_EMPLOYEE_CONTACT')
            ->where('EMPID_REF','=',$id)
            ->first();

            $objTab9 =  DB::table('TBL_MST_EMPLOYEE_OTHER')
            ->where('EMPID_REF','=',$id)
            ->first();

            $getGender              =   $this->getGender();
            $getDesignation         =   $this->getDesignation();
            $getSalutation          =   $this->getSalutation();
            $getSalutation          =   $this->getSalutation();
            $getDepartment          =   $this->getDepartment();
            $getDivision            =   $this->getDivision();
            $getPostLevel           =   $this->getPostLevel();
            $getEmployeeCategory    =   $this->getEmployeeCategory();
            $getEmployeeType        =   $this->getEmployeeType();
            $getCostCentre          =   $this->getCostCentre();
            $getBloodGroup          =   $this->getBloodGroup();
            $getGrade               =   $this->getGrade();
            $getBranch              =   $this->getBranch();
            $getRelationShip        =   $this->getRelationShip();
            $objCountryList         =   $this->getCountryList();

            $objDisticName  = [];
            if(isset($objTab1->CRDISTID_REF)){
                $objDisticName          =   $this->getDisticName($objTab1->CRDISTID_REF);
            }
           
            $objCityName =[];
            if(isset($objTab1->CRCITYID_REF)){
                $objCityName            =   $this->getCityName($objTab1->CRCITYID_REF);
            }

            $objStateName =[];
            if(isset($objTab1->CRSTID_REF)){
                $objStateName           =   $this->getStateName($objTab1->CRSTID_REF);
            }

            $objCountryName = [];
            if(isset($objTab1->CRCTRYID_REF)){
                $objCountryName         =   $this->getCountryName($objTab1->CRCTRYID_REF);
            }

           

            $FormId         =   $this->form_id;

            return view($this->view.$FormId.'edit',compact([
                    'FormId',
                    'objRights',
                    'objResponse',
                    'getGender',
                    'getDesignation',
                    'getSalutation',
                    'getDepartment',
                    'getDivision',
                    'getPostLevel',
                    'getEmployeeCategory',
                    'getEmployeeType',
                    'getCostCentre',
                    'getBloodGroup',
                    'getGrade',
                    'getBranch',
                    'getRelationShip',
                    'objCountryList',
                    'objDisticName',
                    'objCityName',
                    'objStateName',
                    'objCountryName',
                    'objTab1',
                    'objTab2',
                    'objTab3',
                    'objTab4',
                    'objTab5',
                    'objTab6',
                    'objTab7',
                    'objTab8',
                    'objTab9',
            ]));      


        }

    }

    public function view($id){

        if(!is_null($id)){
        
            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $objResponse =  DB::table('TBL_MST_EMPLOYEE')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('EMPID','=',$id)
            ->first();

            $objTab1 =  DB::table('TBL_MST_EMPLOYEE_ADDRESS')
            ->where('EMPID_REF','=',$id)
            ->first();

            $objTab2 =  DB::table('TBL_MST_EMPLOYEE_FAMILY')
            ->where('EMPID_REF','=',$id)
            ->get()->toArray(); 

            $objTab3 =  DB::table('TBL_MST_EMPLOYEE_EDUCATION')
            ->where('EMPID_REF','=',$id)
            ->get()->toArray(); 

            $objTab4 =  DB::table('TBL_MST_EMPLOYEE_EXPERIENCE')
            ->where('EMPID_REF','=',$id)
            ->get()->toArray();
            
            $objTab5 =  DB::table('TBL_MST_EMPLOYEE_REFERENCE')
            ->where('EMPID_REF','=',$id)
            ->get()->toArray(); 
            
            $objTab6 =  DB::table('TBL_MST_EMPLOYEE_ACTIVITY')
            ->where('EMPID_REF','=',$id)
            ->get()->toArray();

            $objTab7 =  DB::table('TBL_MST_EMPLOYEE_LEGAL')
            ->where('EMPID_REF','=',$id)
            ->first();

            $objTab8 =  DB::table('TBL_MST_EMPLOYEE_CONTACT')
            ->where('EMPID_REF','=',$id)
            ->first();

            $objTab9 =  DB::table('TBL_MST_EMPLOYEE_OTHER')
            ->where('EMPID_REF','=',$id)
            ->first();
            

            $getGender              =   $this->getGender();
            $getDesignation         =   $this->getDesignation();
            $getSalutation          =   $this->getSalutation();
            $getSalutation          =   $this->getSalutation();
            $getDepartment          =   $this->getDepartment();
            $getDivision            =   $this->getDivision();
            $getPostLevel           =   $this->getPostLevel();
            $getEmployeeCategory    =   $this->getEmployeeCategory();
            $getEmployeeType        =   $this->getEmployeeType();
            $getCostCentre          =   $this->getCostCentre();
            $getBloodGroup          =   $this->getBloodGroup();
            $getGrade               =   $this->getGrade();
            $getBranch              =   $this->getBranch();
            $getRelationShip        =   $this->getRelationShip();
            $objCountryList         =   $this->getCountryList();

            // $objDisticName          =   $this->getDisticName($objTab1->CRDISTID_REF);
            // $objCityName            =   $this->getCityName($objTab1->CRCITYID_REF);
            // $objStateName           =   $this->getStateName($objTab1->CRSTID_REF);
            // $objCountryName         =   $this->getCountryName($objTab1->CRCTRYID_REF);
            $objDisticName  = [];
            if(isset($objTab1->CRDISTID_REF)){
                $objDisticName          =   $this->getDisticName($objTab1->CRDISTID_REF);
            }
           
            $objCityName =[];
            if(isset($objTab1->CRCITYID_REF)){
                $objCityName            =   $this->getCityName($objTab1->CRCITYID_REF);
            }

            $objStateName =[];
            if(isset($objTab1->CRSTID_REF)){
                $objStateName           =   $this->getStateName($objTab1->CRSTID_REF);
            }

            $objCountryName = [];
            if(isset($objTab1->CRCTRYID_REF)){
                $objCountryName         =   $this->getCountryName($objTab1->CRCTRYID_REF);
            }

            $FormId         =   $this->form_id;

            return view($this->view.$FormId.'view',compact([
                    'FormId',
                    'objRights',
                    'objResponse',
                    'getGender',
                    'getDesignation',
                    'getSalutation',
                    'getDepartment',
                    'getDivision',
                    'getPostLevel',
                    'getEmployeeCategory',
                    'getEmployeeType',
                    'getCostCentre',
                    'getBloodGroup',
                    'getGrade',
                    'getBranch',
                    'getRelationShip',
                    'objCountryList',
                    'objDisticName',
                    'objCityName',
                    'objStateName',
                    'objCountryName',
                    'objTab1',
                    'objTab2',
                    'objTab3',
                    'objTab4',
                    'objTab5',
                    'objTab6',
                    'objTab7',
                    'objTab8',
                    'objTab9',
            ]));      


        }

    }
 
    public function update(Request $request){

        $rules = [
            'FNAME' => 'required',      
        ];

        $req_data = [
            'FNAME' =>   $request['FNAME']
        ]; 

        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails()){
            return Response::json(['errors' => $validator->errors()]);	
        }

        $EMPCODE             =   strtoupper(trim($request['EMPCODE']) );
        $FNAME               =   trim($request['FNAME']);  
        $MNAME             =   (isset($request['MNAME']) && trim($request['MNAME']) !="" )? trim($request['MNAME']) : NULL ;
        $LNAME             =   (isset($request['LNAME']) && trim($request['LNAME']) !="" )? trim($request['LNAME']) : NULL ;
        $OLDREFNO          =   (isset($request['OLDREFNO']) && trim($request['OLDREFNO']) !="" )? trim($request['OLDREFNO']) : NULL ;
        $GID_REF             =   (isset($request['GID_REF']) && trim($request['GID_REF']) !="" )? trim($request['GID_REF']) : NULL ;
        $DESGID_REF             =   (isset($request['DESGID_REF']) && trim($request['DESGID_REF']) !="" )? trim($request['DESGID_REF']) : NULL ;
        $SAID_REF             =   (isset($request['SAID_REF']) && trim($request['SAID_REF']) !="" )? trim($request['SAID_REF']) : NULL ;
        $DEPID_REF             =   (isset($request['DEPID_REF']) && trim($request['DEPID_REF']) !="" )? trim($request['DEPID_REF']) : NULL ;
        $DIVID_REF             =   (isset($request['DIVID_REF']) && trim($request['DIVID_REF']) !="" )? trim($request['DIVID_REF']) : NULL ;
        
        $PLID_REF             =   (isset($request['PLID_REF']) && trim($request['PLID_REF']) !="" )? trim($request['PLID_REF']) : NULL ;
        $DOJ             =   (isset($request['DOJ']) && trim($request['DOJ']) !="" )? date("Y-m-d",strtotime($request['DOJ'])) : NULL ;
        $CATID_REF             =   (isset($request['CATID_REF']) && trim($request['CATID_REF']) !="" )? trim($request['CATID_REF']) : NULL ;
        $ETYPEID_REF             =   (isset($request['ETYPEID_REF']) && trim($request['ETYPEID_REF']) !="" )? trim($request['ETYPEID_REF']) : NULL ;
        $FATHERNAME             =   (isset($request['FATHERNAME']) && trim($request['FATHERNAME']) !="" )? trim($request['FATHERNAME']) : NULL ;
        $CCID_REF             =   (isset($request['CCID_REF']) && trim($request['CCID_REF']) !="" )? trim($request['CCID_REF']) : NULL ;
        $DOB             =   (isset($request['DOBHRD']) && trim($request['DOBHRD']) !="" )? date("Y-m-d",strtotime($request['DOBHRD'])) : NULL ;
        $DOBPLACE             =   (isset($request['DOBPLACE']) && trim($request['DOBPLACE']) !="" )? trim($request['DOBPLACE']) : NULL ;
        $BGID_REF             =   (isset($request['BGID_REF']) && trim($request['BGID_REF']) !="" )? trim($request['BGID_REF']) : NULL ;

        $GRADEID_REF             =   (isset($request['GRADEID_REF']) && trim($request['GRADEID_REF']) !="" )? trim($request['GRADEID_REF']) : NULL ;
        //$BRID_REF             =   (isset($request['BRID_REF']) && trim($request['BRID_REF']) !="" )? trim($request['BRID_REF']) : NULL ;
        $SALES_PERSON             =   (isset($request['SALES_PERSON']) && trim($request['SALES_PERSON']) !="" )? trim($request['SALES_PERSON']) : NULL ;


        $CRESIDENCE             =   (isset($request['CRESIDENCE']) )? 1 : 0 ;
        $CROWN                  =   (isset($request['CROWN']) )? 1 : 0 ;
        $CRRENTED               =   (isset($request['CRRENTED']) )? 1 : 0 ;
        $CRADD1                 =   (isset($request['CRADD1']) && trim($request['CRADD1']) !="" )? trim($request['CRADD1']) : NULL ;
        $CRADD2                 =   (isset($request['CRADD2']) && trim($request['CRADD2']) !="" )? trim($request['CRADD2']) : NULL ;
        $CRCITYID_REF           =   (isset($request['CITYID_REF']) && trim($request['CITYID_REF']) !="" )? trim($request['CITYID_REF']) : NULL ;
        $CRSTID_REF             =   (isset($request['STID_REF']) && trim($request['STID_REF']) !="" )? trim($request['STID_REF']) : NULL ;
        $CRPIN                  =   (isset($request['CRPIN']) && trim($request['CRPIN']) !="" )? trim($request['CRPIN']) : NULL ;
        $CRDISTID_REF           =   (isset($request['DISTID_REF']) && trim($request['DISTID_REF']) !="" )? trim($request['DISTID_REF']) : NULL ;
        $CRCTRYID_REF           =   (isset($request['CTRYID_REF']) && trim($request['CTRYID_REF']) !="" )? trim($request['CTRYID_REF']) : NULL ;
        $CRLM                   =   (isset($request['CRLM']) && trim($request['CRLM']) !="" )? trim($request['CRLM']) : NULL ;

        $PRRESIDENCE             =   (isset($request['PRRESIDENCE']) )? 1 : 0 ;
        $PROWN                  =   (isset($request['PROWN']) )? 1 : 0 ;
        $PRRENTED               =   (isset($request['PRRENTED']) )? 1 : 0 ;
        $PRADD1                 =   (isset($request['PRADD1']) && trim($request['PRADD1']) !="" )? trim($request['PRADD1']) : NULL ;
        $PRADD2                 =   (isset($request['PRADD2']) && trim($request['PRADD2']) !="" )? trim($request['PRADD2']) : NULL ;
        $PRCITYID_REF           =   (isset($request['CITYID_REF1']) && trim($request['CITYID_REF1']) !="" )? trim($request['CITYID_REF1']) : NULL ;
        $PRSTID_REF             =   (isset($request['STID_REF1']) && trim($request['STID_REF1']) !="" )? trim($request['STID_REF1']) : NULL ;
        $PRPIN                  =   (isset($request['PRPIN']) && trim($request['PRPIN']) !="" )? trim($request['PRPIN']) : NULL ;
        $PRDISTID_REF           =   (isset($request['DISTID_REF1']) && trim($request['DISTID_REF1']) !="" )? trim($request['DISTID_REF1']) : NULL ;
        $PRCTRYID_REF           =   (isset($request['CTRYID_REF1']) && trim($request['CTRYID_REF1']) !="" )? trim($request['CTRYID_REF1']) : NULL ;
        
        $PRLM                   =   (isset($request['PRLM']) && trim($request['PRLM']) !="" )? trim($request['PRLM']) : NULL ;
        $PANNO                   =   (isset($request['PANNO']) && trim($request['PANNO']) !="" )? trim($request['PANNO']) : NULL ;
        $AADHARNO                   =   (isset($request['AADHARNO']) && trim($request['AADHARNO']) !="" )? trim($request['AADHARNO']) : NULL ;
        $ELECTIONCNO                   =   (isset($request['ELECTIONCNO']) && trim($request['ELECTIONCNO']) !="" )? trim($request['ELECTIONCNO']) : NULL ;
        $ELECTIONCPOI                   =   (isset($request['ELECTIONCPOI']) && trim($request['ELECTIONCPOI']) !="" )? trim($request['ELECTIONCPOI']) : NULL ;
        $DLNO                   =   (isset($request['DLNO']) && trim($request['DLNO']) !="" )? trim($request['DLNO']) : NULL ;
        $DLPOI                   =   (isset($request['DLPOI']) && trim($request['DLPOI']) !="" )? trim($request['DLPOI']) : NULL ;
        $DLVALIDUPTO                   =   (isset($request['DLVALIDUPTO']) && trim($request['DLVALIDUPTO']) !="" )? trim($request['DLVALIDUPTO']) : NULL ;
        $PASSPORTNO                   =   (isset($request['PASSPORTNO']) && trim($request['PASSPORTNO']) !="" )? trim($request['PASSPORTNO']) : NULL ;
        $PASSPORTPOI                   =   (isset($request['PASSPORTPOI']) && trim($request['PASSPORTPOI']) !="" )? trim($request['PASSPORTPOI']) : NULL ;
       
        $PASSPORTVUPTO                   =   (isset($request['PASSPORTVUPTO']) && trim($request['PASSPORTVUPTO']) !="" )? trim($request['PASSPORTVUPTO']) : NULL ;
        $PFNO                   =   (isset($request['PFNO']) && trim($request['PFNO']) !="" )? trim($request['PFNO']) : NULL ;
        $ESINO                   =   (isset($request['ESINO']) && trim($request['ESINO']) !="" )? trim($request['ESINO']) : NULL ;
        $MSHIPNAME                   =   (isset($request['MSHIPNAME']) && trim($request['MSHIPNAME']) !="" )? trim($request['MSHIPNAME']) : NULL ;
        $MSHIPNO                   =   (isset($request['MSHIPNO']) && trim($request['MSHIPNO']) !="" )? trim($request['MSHIPNO']) : NULL ;
        $CONTRACTRNAME                   =   (isset($request['CONTRACTRNAME']) && trim($request['CONTRACTRNAME']) !="" )? trim($request['CONTRACTRNAME']) : NULL ;
        $NOMINEENAME                   =   (isset($request['NOMINEENAME']) && trim($request['NOMINEENAME']) !="" )? trim($request['NOMINEENAME']) : NULL ;
        $RSID_REF                   =   (isset($request['RSID_REF']) && trim($request['RSID_REF']) !="" )? trim($request['RSID_REF']) : NULL ;
        $BANK                   =   (isset($request['BANK']) && trim($request['BANK']) !="" )? trim($request['BANK']) : NULL ;
        $IFSC                   =   (isset($request['IFSC']) && trim($request['IFSC']) !="" )? trim($request['IFSC']) : NULL ;

        $BRANCH                   =   (isset($request['BRANCH']) && trim($request['BRANCH']) !="" )? trim($request['BRANCH']) : NULL ;
        $ACTYPE                   =   (isset($request['ACTYPE']) && trim($request['ACTYPE']) !="" )? trim($request['ACTYPE']) : NULL ;
        $ACNO                   =   (isset($request['ACNO']) && trim($request['ACNO']) !="" )? trim($request['ACNO']) : NULL ;
        $BRANCHCODE                   =   (isset($request['BRANCHCODE']) && trim($request['BRANCHCODE']) !="" )? trim($request['BRANCHCODE']) : NULL ;
        $LLNO                   =   (isset($request['LLNO']) && trim($request['LLNO']) !="" )? trim($request['LLNO']) : NULL ;
        $MONO1                   =   (isset($request['MONO1']) && trim($request['MONO1']) !="" )? trim($request['MONO1']) : NULL ;
        $MONO2                   =   (isset($request['MONO2']) && trim($request['MONO2']) !="" )? trim($request['MONO2']) : NULL ;
        $EMAIL                   =   (isset($request['EMAIL']) && trim($request['EMAIL']) !="" )? trim($request['EMAIL']) : NULL ;
        $PEMAIL                   =   (isset($request['PEMAIL']) && trim($request['PEMAIL']) !="" )? trim($request['PEMAIL']) : NULL ;
        $EMERGCPNAME                   =   (isset($request['EMERGCPNAME']) && trim($request['EMERGCPNAME']) !="" )? trim($request['EMERGCPNAME']) : NULL ;

        $EMERGCPMONO                   =   (isset($request['EMERGCPMONO']) && trim($request['EMERGCPMONO']) !="" )? trim($request['EMERGCPMONO']) : NULL ;
        $CRDISEASE1                   =   (isset($request['CRDISEASE1']) && trim($request['CRDISEASE1']) !="" )? trim($request['CRDISEASE1']) : NULL ;
        $CRDISEASE2                   =   (isset($request['CRDISEASE2']) && trim($request['CRDISEASE2']) !="" )? trim($request['CRDISEASE2']) : NULL ;
        $CRDISEASE3                   =   (isset($request['CRDISEASE3']) && trim($request['CRDISEASE3']) !="" )? trim($request['CRDISEASE3']) : NULL ;
        $ALLERGY                   =   (isset($request['ALLERGY']) && trim($request['ALLERGY']) !="" )? trim($request['ALLERGY']) : NULL ;
        $HEIGHT                   =   (isset($request['HEIGHT']) && trim($request['HEIGHT']) !="" )? trim($request['HEIGHT']) : NULL ;
        $WEIGHT                   =   (isset($request['WEIGHT']) && trim($request['WEIGHT']) !="" )? trim($request['WEIGHT']) : NULL ;
        $COLOR                   =   (isset($request['COLOR']) && trim($request['COLOR']) !="" )? trim($request['COLOR']) : NULL ;
        $RELIGION                   =   (isset($request['RELIGION']) && trim($request['RELIGION']) !="" )? trim($request['RELIGION']) : NULL ;
        $NANTIONALITY                   =   (isset($request['NANTIONALITY']) && trim($request['NANTIONALITY']) !="" )? trim($request['NANTIONALITY']) : NULL ;
       
        $HOBBIES                   =   (isset($request['HOBBIES']) && trim($request['HOBBIES']) !="" )? trim($request['HOBBIES']) : NULL ;
        $VEGETARIAN                   =   (isset($request['VEGETARIAN']) && trim($request['VEGETARIAN']) !="" )? trim($request['VEGETARIAN']) : NULL ;

        $Row_Count2 = $request['Row_Count2'];
        $data2 = array();
        for ($i=0; $i<=$Row_Count2; $i++){

            if((isset($request['FAMILY_NAME_'.$i]) && $request['FAMILY_NAME_'.$i] !="")){
                $data2[$i] = [
                    'NAME' => strtoupper(trim($request['FAMILY_NAME_'.$i])),
                    'DOB' => strtoupper(trim($request['FAMILY_DOB_'.$i])),
                    'GID_REF' => strtoupper(trim($request['FAMILY_GID_REF_'.$i])),
                    'RSID_REF' => strtoupper(trim($request['FAMILY_RSID_REF_'.$i])),
                    'EARNING' => strtoupper(trim($request['FAMILY_EARNING_'.$i])),
                    'CONTACTNO' => strtoupper(trim($request['FAMILY_CONTACTNO_'.$i])),
                    'EMAIL' => strtoupper(trim($request['FAMILY_EMAIL_'.$i])),
                   
                ];
            }
        }

        if(!empty($data2)){     
            $wrapped_links2["FAMILYMEMBERS"] = $data2; 
            $FAMILYXML = ArrayToXml::convert($wrapped_links2);
        }else{
            $FAMILYXML = NULL;
        }


        $Row_Count3 = $request['Row_Count3'];
        $data3 = array();
        for ($i=0; $i<=$Row_Count3; $i++){

            if((isset($request['EDUCATION_DEGREE_'.$i]) && $request['EDUCATION_DEGREE_'.$i] !="")){
                $data3[$i] = [
                    'DEGREE' => strtoupper(trim($request['EDUCATION_DEGREE_'.$i])),
                    'YOP' => strtoupper(trim($request['EDUCATION_YOP_'.$i])),
                    'UNIVERSITY' => strtoupper(trim($request['EDUCATION_UNIVERSITY_'.$i])),
                    'RESULT' => strtoupper(trim($request['EDUCATION_RESULT_'.$i])),
                    'REMARKS' => strtoupper(trim($request['EDUCATION_REMARKS_'.$i])),
                ];
            }
        }

        if(!empty($data3)){     
            $wrapped_links3["EDUCATION"] = $data3; 
            $EDUCATIONXML = ArrayToXml::convert($wrapped_links3);
        }else{
            $EDUCATIONXML = NULL;
        }


        $Row_Count4 = $request['Row_Count4'];
        $data4 = array();
        for ($i=0; $i<=$Row_Count4; $i++){

            if((isset($request['EXPERIENCE_CNAME_'.$i]) && $request['EXPERIENCE_CNAME_'.$i] !="")){
                $data4[$i] = [
                    'CNAME' => strtoupper(trim($request['EXPERIENCE_CNAME_'.$i])),
                    'FROMPD' => strtoupper(trim($request['EXPERIENCE_FROMPD_'.$i])),
                    'TOPD' => strtoupper(trim($request['EXPERIENCE_TOPD_'.$i])),
                    'LASTDESIG' => strtoupper(trim($request['EXPERIENCE_LASTDESIG_'.$i])),
                    'CTCPA' => strtoupper(trim($request['EXPERIENCE_CTCPA_'.$i])),
                    'REASONOFLEAVING' => strtoupper(trim($request['EXPERIENCE_REMARKS_'.$i])),
                ];
            }
        }

        if(!empty($data4)){     
            $wrapped_links4["EXPERIENCE"] = $data4; 
            $EXPERIENCEXML = ArrayToXml::convert($wrapped_links4);
        }else{
            $EXPERIENCEXML = NULL;
        }


        $Row_Count5 = $request['Row_Count5'];
        $data5 = array();
        for ($i=0; $i<=$Row_Count5; $i++){

            if((isset($request['REFERENCE_RNAME_'.$i]) && $request['REFERENCE_RNAME_'.$i] !="")){
                $data5[$i] = [
                    'RNAME' => strtoupper(trim($request['REFERENCE_RNAME_'.$i])),
                    'GID_REF' => strtoupper(trim($request['REFERENCE_GID_REF_'.$i])),
                    'COMPANY' => strtoupper(trim($request['REFERENCE_COMPANY_'.$i])),
                    'DESIG' => strtoupper(trim($request['REFERENCE_DESIG_'.$i])),
                    'MONO' => strtoupper(trim($request['REFERENCE_MONO_'.$i])),
                    'EMAIL' => strtoupper(trim($request['REFERENCE_EMAIL_'.$i])),
                ];
            }
        }

        if(!empty($data5)){     
            $wrapped_links5["REFERENCE"] = $data5; 
            $REFRENCEXML = ArrayToXml::convert($wrapped_links5);
        }else{
            $REFRENCEXML = NULL;
        }

        $Row_Count6 = $request['Row_Count6'];
        $data6 = array();
        for ($i=0; $i<=$Row_Count6; $i++){

            if((isset($request['EXTRACUR_NAME_'.$i]) && $request['EXTRACUR_NAME_'.$i] !="")){
                $data6[$i] = [
                    'NAME' => strtoupper(trim($request['EXTRACUR_NAME_'.$i])),
                    'PERIOD' => strtoupper(trim($request['EXTRACUR_PERIOD_'.$i])),
                    'LEVEL' => strtoupper(trim($request['EXTRACUR_LEVELS_'.$i])),
                    'ACHIEVEMNET' => strtoupper(trim($request['EXTRACUR_ACHIEVEMENT_'.$i])),
                ];
            }
        }

        if(!empty($data6)){     
            $wrapped_links6["EXTRACURRICULARACTIVITY"] = $data6; 
            $ECACTIVITYXML = ArrayToXml::convert($wrapped_links6);
        }else{
            $ECACTIVITYXML = NULL;
        }


        $DEACTIVATED    = (isset($request['DEACTIVATED']) )? 1 : 0 ;
        $DODEACTIVATED  = isset($request['DODEACTIVATED']) && $request['DODEACTIVATED'] !=""?date("Y-m-d",strtotime($request['DODEACTIVATED'])):NULL;

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID       =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();
 
        $array_data   = [
            $EMPCODE,$FNAME, $MNAME,$LNAME,$OLDREFNO, $GID_REF, $DESGID_REF,$SAID_REF, $DEPID_REF,$DIVID_REF,
            $PLID_REF,$DOJ, $CATID_REF,$ETYPEID_REF,$FATHERNAME, $CCID_REF, $DOB,$DOBPLACE, $BGID_REF,$CRESIDENCE,
            $CROWN,$CRRENTED, $CRADD1,$CRADD2,$CRCITYID_REF, $CRSTID_REF, $CRPIN,$CRDISTID_REF, $CRCTRYID_REF,$CRLM,
            $PRRESIDENCE,$PROWN, $PRRENTED,$PRADD1,$PRADD2, $PRCITYID_REF, $PRSTID_REF,$PRPIN, $PRDISTID_REF,$PRCTRYID_REF,
            $PRLM,$PANNO, $AADHARNO,$ELECTIONCNO,$ELECTIONCPOI, $DLNO, $DLPOI,$DLVALIDUPTO, $PASSPORTNO,$PASSPORTPOI,
            $PASSPORTVUPTO,$PFNO, $ESINO,$MSHIPNAME,$MSHIPNO, $CONTRACTRNAME, $NOMINEENAME,$RSID_REF, $BANK,$IFSC,
            $BRANCH,$ACTYPE, $ACNO,$BRANCHCODE,$LLNO, $MONO1, $MONO2,$EMAIL, $PEMAIL,$EMERGCPNAME,
            $EMERGCPMONO,$CRDISEASE1, $CRDISEASE2,$CRDISEASE3,$ALLERGY, $HEIGHT, $WEIGHT,$COLOR, $RELIGION,$NANTIONALITY,
            $HOBBIES,$VEGETARIAN, $DEACTIVATED, $DODEACTIVATED,$GRADEID_REF,$SALES_PERSON,$CYID_REF,$BRID_REF,$FYID_REF,$FAMILYXML,
            $EDUCATIONXML,$EXPERIENCEXML,$REFRENCEXML,$ECACTIVITYXML,$VTID,$USERID,$UPDATE,$UPTIME, $ACTION, $IPADDRESS   
        ];
        

        try {
            $sp_result = DB::select('EXEC SP_EMPLOYEE_UP  ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?', $array_data);

        } catch (\Throwable $th) {
            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }

        if($sp_result[0]->RESULT=="SUCCESS"){  

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOUND"){
        
            return Response::json(['errors'=>true,'msg' => 'No record found.','exist'=>'norecord']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);
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

        if(!empty($sp_listing_result)){
            foreach ($sp_listing_result as $key=>$valueitem){  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$valueitem->LAVELS;
            }
        }
   
        $EMPCODE             =   strtoupper(trim($request['EMPCODE']) );
        $FNAME               =   trim($request['FNAME']);  
        $MNAME             =   (isset($request['MNAME']) && trim($request['MNAME']) !="" )? trim($request['MNAME']) : NULL ;
        $LNAME             =   (isset($request['LNAME']) && trim($request['LNAME']) !="" )? trim($request['LNAME']) : NULL ;
        $OLDREFNO          =   (isset($request['OLDREFNO']) && trim($request['OLDREFNO']) !="" )? trim($request['OLDREFNO']) : NULL ;
        $GID_REF             =   (isset($request['GID_REF']) && trim($request['GID_REF']) !="" )? trim($request['GID_REF']) : NULL ;
        $DESGID_REF             =   (isset($request['DESGID_REF']) && trim($request['DESGID_REF']) !="" )? trim($request['DESGID_REF']) : NULL ;
        $SAID_REF             =   (isset($request['SAID_REF']) && trim($request['SAID_REF']) !="" )? trim($request['SAID_REF']) : NULL ;
        $DEPID_REF             =   (isset($request['DEPID_REF']) && trim($request['DEPID_REF']) !="" )? trim($request['DEPID_REF']) : NULL ;
        $DIVID_REF             =   (isset($request['DIVID_REF']) && trim($request['DIVID_REF']) !="" )? trim($request['DIVID_REF']) : NULL ;
        
        $PLID_REF             =   (isset($request['PLID_REF']) && trim($request['PLID_REF']) !="" )? trim($request['PLID_REF']) : NULL ;
        $DOJ             =   (isset($request['DOJ']) && trim($request['DOJ']) !="" )? date("Y-m-d",strtotime($request['DOJ'])) : NULL ;
        $CATID_REF             =   (isset($request['CATID_REF']) && trim($request['CATID_REF']) !="" )? trim($request['CATID_REF']) : NULL ;
        $ETYPEID_REF             =   (isset($request['ETYPEID_REF']) && trim($request['ETYPEID_REF']) !="" )? trim($request['ETYPEID_REF']) : NULL ;
        $FATHERNAME             =   (isset($request['FATHERNAME']) && trim($request['FATHERNAME']) !="" )? trim($request['FATHERNAME']) : NULL ;
        $CCID_REF             =   (isset($request['CCID_REF']) && trim($request['CCID_REF']) !="" )? trim($request['CCID_REF']) : NULL ;
        $DOB             =   (isset($request['DOB']) && trim($request['DOB']) !="" )? date("Y-m-d",strtotime($request['DOB'])) : NULL ;
        $DOBPLACE             =   (isset($request['DOBPLACE']) && trim($request['DOBPLACE']) !="" )? trim($request['DOBPLACE']) : NULL ;
        $BGID_REF             =   (isset($request['BGID_REF']) && trim($request['BGID_REF']) !="" )? trim($request['BGID_REF']) : NULL ;

        $GRADEID_REF             =   (isset($request['GRADEID_REF']) && trim($request['GRADEID_REF']) !="" )? trim($request['GRADEID_REF']) : NULL ;
        //$BRID_REF             =   (isset($request['BRID_REF']) && trim($request['BRID_REF']) !="" )? trim($request['BRID_REF']) : NULL ;
        $SALES_PERSON             =   (isset($request['SALES_PERSON']) && trim($request['SALES_PERSON']) !="" )? trim($request['SALES_PERSON']) : NULL ;


        $CRESIDENCE             =   (isset($request['CRESIDENCE']) )? 1 : 0 ;
        $CROWN                  =   (isset($request['CROWN']) )? 1 : 0 ;
        $CRRENTED               =   (isset($request['CRRENTED']) )? 1 : 0 ;
        $CRADD1                 =   (isset($request['CRADD1']) && trim($request['CRADD1']) !="" )? trim($request['CRADD1']) : NULL ;
        $CRADD2                 =   (isset($request['CRADD2']) && trim($request['CRADD2']) !="" )? trim($request['CRADD2']) : NULL ;
        $CRCITYID_REF           =   (isset($request['CITYID_REF']) && trim($request['CITYID_REF']) !="" )? trim($request['CITYID_REF']) : NULL ;
        $CRSTID_REF             =   (isset($request['STID_REF']) && trim($request['STID_REF']) !="" )? trim($request['STID_REF']) : NULL ;
        $CRPIN                  =   (isset($request['CRPIN']) && trim($request['CRPIN']) !="" )? trim($request['CRPIN']) : NULL ;
        $CRDISTID_REF           =   (isset($request['DISTID_REF']) && trim($request['DISTID_REF']) !="" )? trim($request['DISTID_REF']) : NULL ;
        $CRCTRYID_REF           =   (isset($request['CTRYID_REF']) && trim($request['CTRYID_REF']) !="" )? trim($request['CTRYID_REF']) : NULL ;
        $CRLM                   =   (isset($request['CRLM']) && trim($request['CRLM']) !="" )? trim($request['CRLM']) : NULL ;

        $PRRESIDENCE             =   (isset($request['PRRESIDENCE']) )? 1 : 0 ;
        $PROWN                  =   (isset($request['PROWN']) )? 1 : 0 ;
        $PRRENTED               =   (isset($request['PRRENTED']) )? 1 : 0 ;
        $PRADD1                 =   (isset($request['PRADD1']) && trim($request['PRADD1']) !="" )? trim($request['PRADD1']) : NULL ;
        $PRADD2                 =   (isset($request['PRADD2']) && trim($request['PRADD2']) !="" )? trim($request['PRADD2']) : NULL ;
        $PRCITYID_REF           =   (isset($request['CITYID_REF1']) && trim($request['CITYID_REF1']) !="" )? trim($request['CITYID_REF1']) : NULL ;
        $PRSTID_REF             =   (isset($request['STID_REF1']) && trim($request['STID_REF1']) !="" )? trim($request['STID_REF1']) : NULL ;
        $PRPIN                  =   (isset($request['PRPIN']) && trim($request['PRPIN']) !="" )? trim($request['PRPIN']) : NULL ;
        $PRDISTID_REF           =   (isset($request['DISTID_REF1']) && trim($request['DISTID_REF1']) !="" )? trim($request['DISTID_REF1']) : NULL ;
        $PRCTRYID_REF           =   (isset($request['CTRYID_REF1']) && trim($request['CTRYID_REF1']) !="" )? trim($request['CTRYID_REF1']) : NULL ;
        
        $PRLM                   =   (isset($request['PRLM']) && trim($request['PRLM']) !="" )? trim($request['PRLM']) : NULL ;
        $PANNO                   =   (isset($request['PANNO']) && trim($request['PANNO']) !="" )? trim($request['PANNO']) : NULL ;
        $AADHARNO                   =   (isset($request['AADHARNO']) && trim($request['AADHARNO']) !="" )? trim($request['AADHARNO']) : NULL ;
        $ELECTIONCNO                   =   (isset($request['ELECTIONCNO']) && trim($request['ELECTIONCNO']) !="" )? trim($request['ELECTIONCNO']) : NULL ;
        $ELECTIONCPOI                   =   (isset($request['ELECTIONCPOI']) && trim($request['ELECTIONCPOI']) !="" )? trim($request['ELECTIONCPOI']) : NULL ;
        $DLNO                   =   (isset($request['DLNO']) && trim($request['DLNO']) !="" )? trim($request['DLNO']) : NULL ;
        $DLPOI                   =   (isset($request['DLPOI']) && trim($request['DLPOI']) !="" )? trim($request['DLPOI']) : NULL ;
        $DLVALIDUPTO                   =   (isset($request['DLVALIDUPTO']) && trim($request['DLVALIDUPTO']) !="" )? trim($request['DLVALIDUPTO']) : NULL ;
        $PASSPORTNO                   =   (isset($request['PASSPORTNO']) && trim($request['PASSPORTNO']) !="" )? trim($request['PASSPORTNO']) : NULL ;
        $PASSPORTPOI                   =   (isset($request['PASSPORTPOI']) && trim($request['PASSPORTPOI']) !="" )? trim($request['PASSPORTPOI']) : NULL ;
       
        $PASSPORTVUPTO                   =   (isset($request['PASSPORTVUPTO']) && trim($request['PASSPORTVUPTO']) !="" )? trim($request['PASSPORTVUPTO']) : NULL ;
        $PFNO                   =   (isset($request['PFNO']) && trim($request['PFNO']) !="" )? trim($request['PFNO']) : NULL ;
        $ESINO                   =   (isset($request['ESINO']) && trim($request['ESINO']) !="" )? trim($request['ESINO']) : NULL ;
        $MSHIPNAME                   =   (isset($request['MSHIPNAME']) && trim($request['MSHIPNAME']) !="" )? trim($request['MSHIPNAME']) : NULL ;
        $MSHIPNO                   =   (isset($request['MSHIPNO']) && trim($request['MSHIPNO']) !="" )? trim($request['MSHIPNO']) : NULL ;
        $CONTRACTRNAME                   =   (isset($request['CONTRACTRNAME']) && trim($request['CONTRACTRNAME']) !="" )? trim($request['CONTRACTRNAME']) : NULL ;
        $NOMINEENAME                   =   (isset($request['NOMINEENAME']) && trim($request['NOMINEENAME']) !="" )? trim($request['NOMINEENAME']) : NULL ;
        $RSID_REF                   =   (isset($request['RSID_REF']) && trim($request['RSID_REF']) !="" )? trim($request['RSID_REF']) : NULL ;
        $BANK                   =   (isset($request['BANK']) && trim($request['BANK']) !="" )? trim($request['BANK']) : NULL ;
        $IFSC                   =   (isset($request['IFSC']) && trim($request['IFSC']) !="" )? trim($request['IFSC']) : NULL ;

        $BRANCH                   =   (isset($request['BRANCH']) && trim($request['BRANCH']) !="" )? trim($request['BRANCH']) : NULL ;
        $ACTYPE                   =   (isset($request['ACTYPE']) && trim($request['ACTYPE']) !="" )? trim($request['ACTYPE']) : NULL ;
        $ACNO                   =   (isset($request['ACNO']) && trim($request['ACNO']) !="" )? trim($request['ACNO']) : NULL ;
        $BRANCHCODE                   =   (isset($request['BRANCHCODE']) && trim($request['BRANCHCODE']) !="" )? trim($request['BRANCHCODE']) : NULL ;
        $LLNO                   =   (isset($request['LLNO']) && trim($request['LLNO']) !="" )? trim($request['LLNO']) : NULL ;
        $MONO1                   =   (isset($request['MONO1']) && trim($request['MONO1']) !="" )? trim($request['MONO1']) : NULL ;
        $MONO2                   =   (isset($request['MONO2']) && trim($request['MONO2']) !="" )? trim($request['MONO2']) : NULL ;
        $EMAIL                   =   (isset($request['EMAIL']) && trim($request['EMAIL']) !="" )? trim($request['EMAIL']) : NULL ;
        $PEMAIL                   =   (isset($request['PEMAIL']) && trim($request['PEMAIL']) !="" )? trim($request['PEMAIL']) : NULL ;
        $EMERGCPNAME                   =   (isset($request['EMERGCPNAME']) && trim($request['EMERGCPNAME']) !="" )? trim($request['EMERGCPNAME']) : NULL ;

        $EMERGCPMONO                   =   (isset($request['EMERGCPMONO']) && trim($request['EMERGCPMONO']) !="" )? trim($request['EMERGCPMONO']) : NULL ;
        $CRDISEASE1                   =   (isset($request['CRDISEASE1']) && trim($request['CRDISEASE1']) !="" )? trim($request['CRDISEASE1']) : NULL ;
        $CRDISEASE2                   =   (isset($request['CRDISEASE2']) && trim($request['CRDISEASE2']) !="" )? trim($request['CRDISEASE2']) : NULL ;
        $CRDISEASE3                   =   (isset($request['CRDISEASE3']) && trim($request['CRDISEASE3']) !="" )? trim($request['CRDISEASE3']) : NULL ;
        $ALLERGY                   =   (isset($request['ALLERGY']) && trim($request['ALLERGY']) !="" )? trim($request['ALLERGY']) : NULL ;
        $HEIGHT                   =   (isset($request['HEIGHT']) && trim($request['HEIGHT']) !="" )? trim($request['HEIGHT']) : NULL ;
        $WEIGHT                   =   (isset($request['WEIGHT']) && trim($request['WEIGHT']) !="" )? trim($request['WEIGHT']) : NULL ;
        $COLOR                   =   (isset($request['COLOR']) && trim($request['COLOR']) !="" )? trim($request['COLOR']) : NULL ;
        $RELIGION                   =   (isset($request['RELIGION']) && trim($request['RELIGION']) !="" )? trim($request['RELIGION']) : NULL ;
        $NANTIONALITY                   =   (isset($request['NANTIONALITY']) && trim($request['NANTIONALITY']) !="" )? trim($request['NANTIONALITY']) : NULL ;
       
        $HOBBIES                   =   (isset($request['HOBBIES']) && trim($request['HOBBIES']) !="" )? trim($request['HOBBIES']) : NULL ;
        $VEGETARIAN                   =   (isset($request['VEGETARIAN']) && trim($request['VEGETARIAN']) !="" )? trim($request['VEGETARIAN']) : NULL ;

        $Row_Count2 = $request['Row_Count2'];
        $data2 = array();
        for ($i=0; $i<=$Row_Count2; $i++){

            if((isset($request['FAMILY_NAME_'.$i]) && $request['FAMILY_NAME_'.$i] !="")){
                $data2[$i] = [
                    'NAME' => strtoupper(trim($request['FAMILY_NAME_'.$i])),
                    'DOB' => strtoupper(trim($request['FAMILY_DOB_'.$i])),
                    'GID_REF' => strtoupper(trim($request['FAMILY_GID_REF_'.$i])),
                    'RSID_REF' => strtoupper(trim($request['FAMILY_RSID_REF_'.$i])),
                    'EARNING' => strtoupper(trim($request['FAMILY_EARNING_'.$i])),
                    'CONTACTNO' => strtoupper(trim($request['FAMILY_CONTACTNO_'.$i])),
                    'EMAIL' => strtoupper(trim($request['FAMILY_EMAIL_'.$i])),
                   
                ];
            }
        }

        if(!empty($data2)){     
            $wrapped_links2["FAMILYMEMBERS"] = $data2; 
            $FAMILYXML = ArrayToXml::convert($wrapped_links2);
        }else{
            $FAMILYXML = NULL;
        }


        $Row_Count3 = $request['Row_Count3'];
        $data3 = array();
        for ($i=0; $i<=$Row_Count3; $i++){

            if((isset($request['EDUCATION_DEGREE_'.$i]) && $request['EDUCATION_DEGREE_'.$i] !="")){
                $data3[$i] = [
                    'DEGREE' => strtoupper(trim($request['EDUCATION_DEGREE_'.$i])),
                    'YOP' => strtoupper(trim($request['EDUCATION_YOP_'.$i])),
                    'UNIVERSITY' => strtoupper(trim($request['EDUCATION_UNIVERSITY_'.$i])),
                    'RESULT' => strtoupper(trim($request['EDUCATION_RESULT_'.$i])),
                    'REMARKS' => strtoupper(trim($request['EDUCATION_REMARKS_'.$i])),
                ];
            }
        }

        if(!empty($data3)){     
            $wrapped_links3["EDUCATION"] = $data3; 
            $EDUCATIONXML = ArrayToXml::convert($wrapped_links3);
        }else{
            $EDUCATIONXML = NULL;
        }


        $Row_Count4 = $request['Row_Count4'];
        $data4 = array();
        for ($i=0; $i<=$Row_Count4; $i++){

            if((isset($request['EXPERIENCE_CNAME_'.$i]) && $request['EXPERIENCE_CNAME_'.$i] !="")){
                $data4[$i] = [
                    'CNAME' => strtoupper(trim($request['EXPERIENCE_CNAME_'.$i])),
                    'FROMPD' => strtoupper(trim($request['EXPERIENCE_FROMPD_'.$i])),
                    'TOPD' => strtoupper(trim($request['EXPERIENCE_TOPD_'.$i])),
                    'LASTDESIG' => strtoupper(trim($request['EXPERIENCE_LASTDESIG_'.$i])),
                    'CTCPA' => strtoupper(trim($request['EXPERIENCE_CTCPA_'.$i])),
                    'REASONOFLEAVING' => strtoupper(trim($request['EXPERIENCE_REMARKS_'.$i])),
                ];
            }
        }

        if(!empty($data4)){     
            $wrapped_links4["EXPERIENCE"] = $data4; 
            $EXPERIENCEXML = ArrayToXml::convert($wrapped_links4);
        }else{
            $EXPERIENCEXML = NULL;
        }


        $Row_Count5 = $request['Row_Count5'];
        $data5 = array();
        for ($i=0; $i<=$Row_Count5; $i++){

            if((isset($request['REFERENCE_RNAME_'.$i]) && $request['REFERENCE_RNAME_'.$i] !="")){
                $data5[$i] = [
                    'RNAME' => strtoupper(trim($request['REFERENCE_RNAME_'.$i])),
                    'GID_REF' => strtoupper(trim($request['REFERENCE_GID_REF_'.$i])),
                    'COMPANY' => strtoupper(trim($request['REFERENCE_COMPANY_'.$i])),
                    'DESIG' => strtoupper(trim($request['REFERENCE_DESIG_'.$i])),
                    'MONO' => strtoupper(trim($request['REFERENCE_MONO_'.$i])),
                    'EMAIL' => strtoupper(trim($request['REFERENCE_EMAIL_'.$i])),
                ];
            }
        }

        if(!empty($data5)){     
            $wrapped_links5["REFERENCE"] = $data5; 
            $REFRENCEXML = ArrayToXml::convert($wrapped_links5);
        }else{
            $REFRENCEXML = NULL;
        }

        $Row_Count6 = $request['Row_Count6'];
        $data6 = array();
        for ($i=0; $i<=$Row_Count6; $i++){

            if((isset($request['EXTRACUR_NAME_'.$i]) && $request['EXTRACUR_NAME_'.$i] !="")){
                $data6[$i] = [
                    'NAME' => strtoupper(trim($request['EXTRACUR_NAME_'.$i])),
                    'PERIOD' => strtoupper(trim($request['EXTRACUR_PERIOD_'.$i])),
                    'LEVEL' => strtoupper(trim($request['EXTRACUR_LEVELS_'.$i])),
                    'ACHIEVEMNET' => strtoupper(trim($request['EXTRACUR_ACHIEVEMENT_'.$i])),
                ];
            }
        }

        if(!empty($data6)){     
            $wrapped_links6["EXTRACURRICULARACTIVITY"] = $data6; 
            $ECACTIVITYXML = ArrayToXml::convert($wrapped_links6);
        }else{
            $ECACTIVITYXML = NULL;
        }

        $DEACTIVATED    = (isset($request['DEACTIVATED']) )? 1 : 0 ;
        $DODEACTIVATED  = isset($request['DODEACTIVATED']) && $request['DODEACTIVATED'] !=""?date("Y-m-d",strtotime($request['DODEACTIVATED'])):NULL;

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID       =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     = $Approvallevel;
        $IPADDRESS  =   $request->getClientIp();

        $array_data   = [
            $EMPCODE,$FNAME, $MNAME,$LNAME,$OLDREFNO, $GID_REF, $DESGID_REF,$SAID_REF, $DEPID_REF,$DIVID_REF,
            $PLID_REF,$DOJ, $CATID_REF,$ETYPEID_REF,$FATHERNAME, $CCID_REF, $DOB,$DOBPLACE, $BGID_REF,$CRESIDENCE,
            $CROWN,$CRRENTED, $CRADD1,$CRADD2,$CRCITYID_REF, $CRSTID_REF, $CRPIN,$CRDISTID_REF, $CRCTRYID_REF,$CRLM,
            $PRRESIDENCE,$PROWN, $PRRENTED,$PRADD1,$PRADD2, $PRCITYID_REF, $PRSTID_REF,$PRPIN, $PRDISTID_REF,$PRCTRYID_REF,
            $PRLM,$PANNO, $AADHARNO,$ELECTIONCNO,$ELECTIONCPOI, $DLNO, $DLPOI,$DLVALIDUPTO, $PASSPORTNO,$PASSPORTPOI,
            $PASSPORTVUPTO,$PFNO, $ESINO,$MSHIPNAME,$MSHIPNO, $CONTRACTRNAME, $NOMINEENAME,$RSID_REF, $BANK,$IFSC,
            $BRANCH,$ACTYPE, $ACNO,$BRANCHCODE,$LLNO, $MONO1, $MONO2,$EMAIL, $PEMAIL,$EMERGCPNAME,
            $EMERGCPMONO,$CRDISEASE1, $CRDISEASE2,$CRDISEASE3,$ALLERGY, $HEIGHT, $WEIGHT,$COLOR, $RELIGION,$NANTIONALITY,
            $HOBBIES,$VEGETARIAN, $DEACTIVATED, $DODEACTIVATED,$GRADEID_REF,$SALES_PERSON,$CYID_REF,$BRID_REF,$FYID_REF,$FAMILYXML,
            $EDUCATIONXML,$EXPERIENCEXML,$REFRENCEXML,$ECACTIVITYXML,$VTID,$USERID,$UPDATE,$UPTIME, $ACTION, $IPADDRESS   
        ];

        try {
            $sp_result = DB::select('EXEC SP_EMPLOYEE_UP  ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?', $array_data);
            //dd($sp_result);

        } catch (\Throwable $th) {
            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }

        if($sp_result[0]->RESULT=="SUCCESS"){  

            return Response::json(['success' =>true,'msg' => 'Record successfully approved.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOUND"){
        
            return Response::json(['errors'=>true,'msg' => 'No record found.','exist'=>'norecord']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);
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
        $TABLE      =   "TBL_MST_EMPLOYEE";
        $FIELD      =   "EMPID";
        $ACTIONNAME     = $Approvallevel;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
            
        $log_data = [ 
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_MST_MULTIAPPROVAL ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);
        
        
        if($sp_result[0]->RESULT=="All records approved"){

        return Response::json(['approve' =>true,'msg' => 'Record successfully Approved.']);

        }elseif($sp_result[0]->RESULT=="NO RECORD FOR APPROVAL"){
        
        return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','salesenquiry'=>'norecord']);
        
        }else{
        return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','salesenquiry'=>'Some Error']);
        }
        
        exit();    
    }
    
     //Cancel the data
     public function cancel(Request $request){

        $id = $request->{0};

       $USERID =   Auth::user()->USERID;
        $VTID   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');  
        $TABLE      =   "TBL_MST_EMPLOYEE";
        $FIELD      =   "EMPID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_MST_EMPLOYEE',
        ];

        $req_data[1]=[
            'NT'  => 'TBL_MST_EMPLOYEE_ADDRESS',
        ];

        $req_data[2]=[
            'NT'  => 'TBL_MST_EMPLOYEE_FAMILY',
        ];

        $req_data[3]=[
            'NT'  => 'TBL_MST_EMPLOYEE_EDUCATION',
        ];

        $req_data[4]=[
            'NT'  => 'TBL_MST_EMPLOYEE_EXPERIENCE',
        ];

        $req_data[5]=[
            'NT'  => 'TBL_MST_EMPLOYEE_REFERENCE',
        ];

        $req_data[6]=[
            'NT'  => 'TBL_MST_EMPLOYEE_ACTIVITY',
        ];
        $req_data[7]=[
            'NT'  => 'TBL_MST_EMPLOYEE_LEGAL',
        ];
        $req_data[8]=[
            'NT'  => 'TBL_MST_EMPLOYEE_CONTACT',
        ];
        $req_data[9]=[
            'NT'  => 'TBL_MST_EMPLOYEE_OTHER',
        ];

        $req_data[10]=[
            'NT'  => 'TBL_MST_EMP_BRANCH_MAP',
        ];
        
        $wrapped_links["TABLES"] = $req_data; 
        
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $mst_cancel_data = [ $USERID, $VTID, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$XMLTAB];
        $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);

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

            $objResponse = DB::table('TBL_MST_EMPLOYEE')->where('EMPID','=',$id)->first();

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
        
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/EmployeeMaster";
		
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



    public function getGender(){
        return  DB::select("SELECT GID AS FID, CONCAT('',DESCRIPTIONS) FNAME
                FROM TBL_MST_GENDER
                WHERE STATUS='A' AND (DEACTIVATED=0 or DEACTIVATED is null)");
    }

    public function getDesignation(){
        $CYID_REF   =   Auth::user()->CYID_REF;

        return  DB::select("SELECT DESGID AS FID, CONCAT('',DESCRIPTIONS) FNAME
        FROM TBL_MST_DESIGNATION
        WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND (DEACTIVATED=0 or DEACTIVATED is null)");
    }

    public function getSalutation(){
        $CYID_REF   =   Auth::user()->CYID_REF;

        return  DB::select("SELECT SAID AS FID, CONCAT('',DESCRIPTIONS) FNAME
        FROM TBL_MST_SALUTATION
        WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND (DEACTIVATED=0 or DEACTIVATED is null)");
    }

    public function getDepartment(){
        $CYID_REF   =   Auth::user()->CYID_REF;

        return  DB::select("SELECT DEPID AS FID, CONCAT('',NAME) FNAME
        FROM TBL_MST_DEPARTMENT
        WHERE STATUS='A' AND (DEACTIVATED=0 or DEACTIVATED is null)");
    }

    public function getDivision(){
        $CYID_REF   =   Auth::user()->CYID_REF;

        return  DB::select("SELECT DIVID AS FID, CONCAT('',NAME) FNAME
        FROM TBL_MST_DIVISON
        WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND (DEACTIVATED=0 or DEACTIVATED is null)");
    }

    public function getPostLevel(){
        $CYID_REF   =   Auth::user()->CYID_REF;

        return  DB::select("SELECT PLID AS FID, CONCAT('',DESCRIPTIONS) FNAME
        FROM TBL_MST_POSTLEVEL
        WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND (DEACTIVATED=0 or DEACTIVATED is null)");
    }

    public function getEmployeeCategory(){
        $CYID_REF   =   Auth::user()->CYID_REF;

        return  DB::select("SELECT CATID AS FID, CONCAT('',NAME) FNAME
        FROM TBL_MST_EMPCATEGORY
        WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND (DEACTIVATED=0 or DEACTIVATED is null)");
    }

    public function getEmployeeType(){
        $CYID_REF   =   Auth::user()->CYID_REF;

        return  DB::select("SELECT ETYPEID AS FID, CONCAT('',NAME) FNAME
        FROM TBL_MST_EMPLOYEETYPE
        WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND (DEACTIVATED=0 or DEACTIVATED is null)");
    }

    public function getCostCentre(){
        $CYID_REF   =   Auth::user()->CYID_REF;

        return  DB::select("SELECT CCID AS FID, CONCAT('',NAME) FNAME
        FROM TBL_MST_COSTCENTER
        WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND (DEACTIVATED=0 or DEACTIVATED is null)");
    }

    public function getBloodGroup(){
        $CYID_REF   =   Auth::user()->CYID_REF;

        return  DB::select("SELECT BGID AS FID, CONCAT('',NAME) FNAME
        FROM TBL_MST_BLOODGROUP
        WHERE STATUS='A' AND (DEACTIVATED=0 or DEACTIVATED is null)");
    }

    public function getGrade(){
        $CYID_REF   =   Auth::user()->CYID_REF;

        return  DB::select("SELECT GRADEID AS FID, CONCAT('',GRADE_DESC) FNAME
        FROM TBL_MST_GRADE
        WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND (DEACTIVATED=0 or DEACTIVATED is null)");
    }

    public function getBranch(){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');

        return  DB::select("SELECT BRID AS FID, CONCAT('',BRNAME) FNAME
        FROM TBL_MST_BRANCH
        WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND BRID='$BRID_REF' AND (DEACTIVATED=0 or DEACTIVATED is null)");
    }

    public function getRelationShip(){
        $CYID_REF   =   Auth::user()->CYID_REF;

        return  DB::select("SELECT RSID AS FID, CONCAT('',DESCRIPTIONS) FNAME
        FROM TBL_MST_RELATIONSHIP
        WHERE STATUS='A' AND (DEACTIVATED=0 or DEACTIVATED is null)");
    }

    public function getCountryList(){
        return DB::table('TBL_MST_COUNTRY')
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('CTRYID','CTRYCODE','NAME')
        ->get();
    }
    
    public function getDisticName($id){
        return DB::table('TBL_MST_DISTT')
        ->where('STATUS','=','A')
        ->where('DISTID','=',$id)
        ->select('DISTCODE','NAME')
        ->first();
    }

    public function getCityName($id){
        return DB::table('TBL_MST_CITY')
        ->where('STATUS','=','A')
        ->where('CITYID','=',$id)
        ->select('CITYCODE','NAME')
        ->first();
    }
    
    public function getStateName($id){
        return DB::table('TBL_MST_STATE')
        ->where('STATUS','=','A')
        ->where('STID','=',$id)
        ->select('STCODE','NAME')
        ->first();
    }

    public function getCountryName($id){
        return DB::table('TBL_MST_COUNTRY')
        ->where('STATUS','=','A')
        ->where('CTRYID','=',$id)
        ->select('CTRYCODE','NAME')
        ->first();
    }
   
    public function getCountryWiseState(Request $request){

        $objStateList = DB::table('TBL_MST_STATE')
        ->where('STATUS','=','A')
        ->where('CTRYID_REF','=',$request['CTRYID_REF'])
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('STID','NAME','STCODE')
        ->get();
    
        if(!empty($objStateList)){
            foreach($objStateList as $state){
            
                echo '<tr >
                <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_STID_REF[]" id="stidref_'.$state->STID.'" class="cls_stidref" value="'.$state->STID.'" ></td>
                <td  width="39%" class="ROW2">'.$state->STCODE.'
                <input type="hidden" id="txtstidref_'.$state->STID.'" data-desc="'.$state->STCODE.'-'.$state->NAME.'" value="'.$state->STID.'" />
                </td>
                <td  width="39%" class="ROW3">'.$state->NAME.'</td>
                </tr>';
            }
        }
        else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
        }
        exit();
    }

    public function getStateWiseCity(Request $request){
        
        $objCityList = DB::table('TBL_MST_CITY')
        ->where('STATUS','=','A')
        ->where('CTRYID_REF','=',$request['CTRYID_REF'])
        ->where('STID_REF','=',$request['STID_REF'])
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('CITYID','CITYCODE','NAME')
        ->get();
        
        if(!empty($objCityList)){
            foreach($objCityList as $city){
            
                echo '<tr >
                <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_CITYID_REF[]" id="cityidref_'.$city->CITYID.'" class="cls_cityidref" value="'.$city->CITYID.'" ></td>
                <td width="39%" class="ROW2">'.$city->CITYCODE.'
                <input type="hidden" id="txtcityidref_'.$city->CITYID.'" data-desc="'.$city->CITYCODE.'-'.$city->NAME.'" value="'.$city->CITYID.'" />
                </td>
                <td width="39%" class="ROW3">'.$city->NAME.'</td>
                </tr>';
            }
        }
        else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
        }

        exit();
    }

    public function getCityWiseDist(Request $request){
        
        $objDistList = DB::table('TBL_MST_DISTT')
        ->where('STATUS','=','A')
        ->where('CTRYID_REF','=',$request['CTRYID_REF'])
        ->where('STID_REF','=',$request['STID_REF'])
        ->where('CITYID_REF','=',$request['CITYID_REF'])
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('DISTID','DISTCODE','NAME')
        ->get();
        
        if(!empty($objDistList)){
            foreach($objDistList as $dist){
            
                echo '<tr >
                <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_DISTID_REF[]" id="distidref_'.$dist->DISTID.'" class="cls_distidref" value="'.$dist->DISTID.'" ></td>
                <td width="39%" class="ROW2">'.$dist->DISTCODE.'
                <input type="hidden" id="txtdistidref_'.$dist->DISTID.'" data-desc="'.$dist->DISTCODE.'-'.$dist->NAME.'" value="'.$dist->DISTID.'" />
                </td>
                <td width="39%" class="ROW3">'.$dist->NAME.'</td>
                </tr>';
            }
        }
        else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
        }

        exit();
    }

    public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $EMPCODE =   $request['EMPCODE'];
        
        $objLabel = DB::table('TBL_MST_EMPLOYEE')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        //->where('BRID_REF','=',Session::get('BRID_REF'))
        //->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('EMPCODE','=',$EMPCODE)
        ->select('EMPCODE')
        ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }


}
