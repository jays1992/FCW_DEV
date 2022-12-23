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

class TrnFrm439Controller extends Controller{

    protected $form_id    = 439;
    protected $vtid_ref   = 509;
    protected $view       = "transactions.PreSales.LeadGeneration.trnfrm";
       
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
        $USERID_REF     =   Auth::user()->USERID; 

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

        $objDataList	=	DB::select("select a.*, hdr.LEAD_ID,hdr.LEAD_NO,hdr.LEAD_DT,CUSTOMER_TYPE,hdr.INDATE,hdr.COMPANY_NAME,hdr.CONTACT_PERSON,hdr.MOBILE_NUMBER,T1.CID,T1.NAME AS CUSTNAME,
                            T2.PID,T2.NAME AS PROSPTNAME,T3.FNAME,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.LEAD_ID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_LEAD_GENERATION hdr
                            INNER JOIN TBL_MST_CUSTOMER T1 ON hdr.CUSTOMER_PROSPECT=T1.CID
                            INNER JOIN TBL_MST_PROSPECT T2 ON hdr.CUSTOMER_PROSPECT=T2.PID
                            INNER JOIN TBL_MST_EMPLOYEE T3 ON hdr.LEADOWNERID_REF=T3.EMPID
                            on a.VID = hdr.LEAD_ID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.LEAD_ID DESC ");
                            

                $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );

                $objDataUser = DB::table('TBL_MST_USERDETAILS')
                                    ->leftJoin('TBL_MST_EMPLOYEE_HIERARCHY_HDR', 'TBL_MST_USERDETAILS.EMPID_REF','=','TBL_MST_EMPLOYEE_HIERARCHY_HDR.REPORTING_TOID_REF')
                                    ->leftJoin('TBL_MST_EMPLOYEE_HIERARCHY_MAT', 'TBL_MST_USERDETAILS.EMPID_REF','=','TBL_MST_EMPLOYEE_HIERARCHY_MAT.EMPID_REF')
                                    ->where('USERID_REF','=',$USERID_REF)
                                    ->select('TBL_MST_USERDETAILS.USERID_REF','TBL_MST_USERDETAILS.EMPID_REF','TBL_MST_USERDETAILS.SUPPERUSER','TBL_MST_EMPLOYEE_HIERARCHY_MAT.SR_NO','TBL_MST_EMPLOYEE_HIERARCHY_HDR.EMPHIERCHYID','TBL_MST_EMPLOYEE_HIERARCHY_HDR.REPORTING_TOID_REF')
                                    ->first();

                $leadUser = DB::table('TBL_TRN_LEAD_GENERATION')->where('USERID_REF','=',$USERID_REF)->select('USERID_REF')->first();

                $LEADUSRID =  isset($leadUser->USERID_REF) && $leadUser->USERID_REF !=""?$leadUser->USERID_REF:NULL;

                $EMPMATID =  isset($objDataUser->EMPHIERCHYID) && $objDataUser->EMPHIERCHYID !=""?$objDataUser->EMPHIERCHYID:NULL;
                
                $EmpHiechyMat = DB::table('TBL_MST_EMPLOYEE_HIERARCHY_MAT')
                                ->where('EMPHIERCHYID_REF','=',$EMPMATID)
                                ->select('EMPID_REF','EMPHIERCHYID_REF')
                                ->get();

                $EMPID_REF   =   array();

                    if(isset($EmpHiechyMat) && !empty($EmpHiechyMat)){
                        foreach($EmpHiechyMat as $index=>$row){

                            if($row->EMPID_REF !=''){
                                $EMPID_REF[]=$row->EMPID_REF;
                                $EMPHIERCHYID_REF=$row->EMPHIERCHYID_REF;
                            }
                        }
                    }

                $EMPHI_REFF = isset($EMPHIERCHYID_REF) && $EMPHIERCHYID_REF !=""?$EMPHIERCHYID_REF:NULL;

                $sprUsr =  isset($objDataUser->SUPPERUSER) && $objDataUser->SUPPERUSER !=""?$objDataUser->SUPPERUSER:NULL;               

                if($sprUsr == 1){
                    
                    $objDataList = DB::table('TBL_TRN_LEAD_GENERATION')
                                ->leftJoin('TBL_MST_USERDETAILS', 'TBL_TRN_LEAD_GENERATION.LEADOWNERID_REF','=','TBL_MST_USERDETAILS.EMPID_REF')
                                ->leftJoin('TBL_MST_EMPLOYEE_HIERARCHY_MAT', 'TBL_MST_USERDETAILS.EMPID_REF','=','TBL_MST_EMPLOYEE_HIERARCHY_MAT.EMPID_REF')
                                ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_LEAD_GENERATION.CUSTOMER_PROSPECT','=','TBL_MST_CUSTOMER.CID')
                                ->leftJoin('TBL_MST_PROSPECT', 'TBL_TRN_LEAD_GENERATION.CUSTOMER_PROSPECT','=','TBL_MST_PROSPECT.PID')
                                ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_TRN_LEAD_GENERATION.LEADOWNERID_REF','=','TBL_MST_EMPLOYEE.EMPID')
                                ->select('TBL_TRN_LEAD_GENERATION.*','TBL_MST_USERDETAILS.*','TBL_MST_EMPLOYEE_HIERARCHY_MAT.*','TBL_MST_CUSTOMER.CID','TBL_MST_CUSTOMER.NAME AS CUSTNAME',
                                'TBL_MST_PROSPECT.PID','TBL_MST_PROSPECT.NAME AS PROSPTNAME','TBL_MST_EMPLOYEE.FNAME')
                                ->get();

                }else if($EMPMATID == $EMPHI_REFF){
                    
                    $objDataList = DB::table('TBL_TRN_LEAD_GENERATION')
                                ->leftJoin('TBL_MST_USERDETAILS', 'TBL_TRN_LEAD_GENERATION.LEADOWNERID_REF','=','TBL_MST_USERDETAILS.EMPID_REF')
                                ->leftJoin('TBL_MST_EMPLOYEE_HIERARCHY_MAT', 'TBL_MST_USERDETAILS.EMPID_REF','=','TBL_MST_EMPLOYEE_HIERARCHY_MAT.EMPID_REF')
                                ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_LEAD_GENERATION.CUSTOMER_PROSPECT','=','TBL_MST_CUSTOMER.CID')
                                ->leftJoin('TBL_MST_PROSPECT', 'TBL_TRN_LEAD_GENERATION.CUSTOMER_PROSPECT','=','TBL_MST_PROSPECT.PID')
                                ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_TRN_LEAD_GENERATION.LEADOWNERID_REF','=','TBL_MST_EMPLOYEE.EMPID')
                                ->where('TBL_TRN_LEAD_GENERATION.VTID_REF','=',$this->vtid_ref)
                                ->whereIn('LEADOWNERID_REF', $EMPID_REF)
                                ->select('TBL_TRN_LEAD_GENERATION.*','TBL_MST_USERDETAILS.*','TBL_MST_EMPLOYEE_HIERARCHY_MAT.*','TBL_MST_CUSTOMER.CID','TBL_MST_CUSTOMER.NAME AS CUSTNAME',
                                'TBL_MST_PROSPECT.PID','TBL_MST_PROSPECT.NAME AS PROSPTNAME','TBL_MST_EMPLOYEE.FNAME')
                                ->get();

                }else{
                    
                $objDataList = DB::table('TBL_TRN_LEAD_GENERATION')
                                ->leftJoin('TBL_MST_USERDETAILS', 'TBL_TRN_LEAD_GENERATION.LEADOWNERID_REF','=','TBL_MST_USERDETAILS.EMPID_REF')
                                ->leftJoin('TBL_MST_EMPLOYEE_HIERARCHY_MAT', 'TBL_MST_USERDETAILS.EMPID_REF','=','TBL_MST_EMPLOYEE_HIERARCHY_MAT.EMPID_REF')
                                ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_LEAD_GENERATION.CUSTOMER_PROSPECT','=','TBL_MST_CUSTOMER.CID')
                                ->leftJoin('TBL_MST_PROSPECT', 'TBL_TRN_LEAD_GENERATION.CUSTOMER_PROSPECT','=','TBL_MST_PROSPECT.PID')
                                ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_TRN_LEAD_GENERATION.LEADOWNERID_REF','=','TBL_MST_EMPLOYEE.EMPID')
                                ->where('TBL_TRN_LEAD_GENERATION.VTID_REF','=',$this->vtid_ref)
                                ->where('TBL_TRN_LEAD_GENERATION.USERID_REF','=',$USERID_REF)
                                ->select('TBL_TRN_LEAD_GENERATION.*','TBL_MST_USERDETAILS.*','TBL_MST_EMPLOYEE_HIERARCHY_MAT.*',
                                'TBL_MST_CUSTOMER.CID','TBL_MST_CUSTOMER.NAME AS CUSTNAME','TBL_MST_PROSPECT.PID','TBL_MST_PROSPECT.NAME AS PROSPTNAME','TBL_MST_EMPLOYEE.FNAME')
                                ->get();
                }

                if($USERID_REF == $LEADUSRID){
                    
                    $objDataList = DB::table('TBL_TRN_LEAD_GENERATION')
                                ->leftJoin('TBL_MST_USERDETAILS', 'TBL_TRN_LEAD_GENERATION.LEADOWNERID_REF','=','TBL_MST_USERDETAILS.EMPID_REF')
                                ->leftJoin('TBL_MST_EMPLOYEE_HIERARCHY_MAT', 'TBL_MST_USERDETAILS.EMPID_REF','=','TBL_MST_EMPLOYEE_HIERARCHY_MAT.EMPID_REF')
                                ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_LEAD_GENERATION.CUSTOMER_PROSPECT','=','TBL_MST_CUSTOMER.CID')
                                ->leftJoin('TBL_MST_PROSPECT', 'TBL_TRN_LEAD_GENERATION.CUSTOMER_PROSPECT','=','TBL_MST_PROSPECT.PID')
                                ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_TRN_LEAD_GENERATION.LEADOWNERID_REF','=','TBL_MST_EMPLOYEE.EMPID')
                                ->where('TBL_TRN_LEAD_GENERATION.VTID_REF','=',$this->vtid_ref)
                                ->where('TBL_TRN_LEAD_GENERATION.USERID_REF','=',$USERID_REF)
                                ->select('TBL_TRN_LEAD_GENERATION.*','TBL_MST_USERDETAILS.*','TBL_MST_EMPLOYEE_HIERARCHY_MAT.*',
                                'TBL_MST_CUSTOMER.CID','TBL_MST_CUSTOMER.NAME AS CUSTNAME','TBL_MST_PROSPECT.PID','TBL_MST_PROSPECT.NAME AS PROSPTNAME','TBL_MST_EMPLOYEE.FNAME')
                                ->get();
    
                    }


        return view($this->view.$FormId,compact(['REQUEST_DATA','objRights','objDataList','FormId']));

    }
   
    
    public function add(){ 

        $FormId   = $this->form_id;
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');

        $country        = $this->country();

        $design         = $this->Designation();
        $emplyee        = $this->AssignedToEmplyee();
        $activitytype   = $this->ActivityType();
        $resp           = $this->Response();
        $tasktype       = $this->TaskType();
        $leadsource     = $this->LeadSource();
        $leadstatus     = $this->LeadStatus();
        $priorty        = $this->Priority();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_LEAD_GENERATION',
            'HDR_ID'=>'LEAD_ID',
            'HDR_DOC_NO'=>'LEAD_NO',
            'HDR_DOC_DT'=>'LEAD_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
  

        return view($this->view.$FormId.'add',compact(['country','design','emplyee',
        'activitytype','resp','tasktype','leadsource','leadstatus','priorty','FormId','doc_req','docarray']));
    }

   
    public function save(Request $request){

        $USER_IDREF             =   Auth::user()->USERID;
        $LEAD_NO                =   trim($request['LEAD_NO'])?trim($request['LEAD_NO']):NULL;
        $LEAD_DT                =   trim($request['LEAD_DT'])?trim($request['LEAD_DT']):NULL;
        $CUSTOMER_PROSPECT      =   trim($request['CUSTOMER_PROSPECT'])?trim($request['CUSTOMER_PROSPECT']):NULL;
        $CUSTOMER_TYPE          =   trim($request['CUSTOMER_TYPE'])?trim($request['CUSTOMER_TYPE']):NULL;
        $OPPORTUNITY_TYPE_ID    =   trim($request['OPPRTYPEID_REF'])?trim($request['OPPRTYPEID_REF']):NULL;
        $OPPORTUNITY_STAGE_ID   =   trim($request['OPPRSTAGEID_REF'])?trim($request['OPPRSTAGEID_REF']):NULL;
        $EXPECTED_DATE          =   trim($request['EXPECTED_DT'])?trim($request['EXPECTED_DT']):NULL;
        $OPPORTUNITY_DATE       =   trim($request['OPPORTUNITY_DT'])?trim($request['OPPORTUNITY_DT']):NULL;
        $COMPANY_NAME           =   trim($request['COMPANY_NAME'])?trim($request['COMPANY_NAME']):NULL;
        $FIRST_NAME             =   trim($request['FNAME'])?trim($request['FNAME']):NULL;
        $LAST_NAME              =   trim($request['LNAME'])?trim($request['LNAME']):NULL;
        $LEADOWNERID_REF        =   trim($request['LOWNERID_REF'])?trim($request['LOWNERID_REF']):NULL;
        $INDSID_REF             =   trim($request['INTYPEID_REF'])?trim($request['INTYPEID_REF']):NULL;
        $DESGINATION            =   trim($request['DESIGNID_REF'])?trim($request['DESIGNID_REF']):NULL;
        $CONTACT_PERSON         =   trim($request['CONTACT_PERSON'])?trim($request['CONTACT_PERSON']):NULL;
        $LEAD_DETAILS           =   trim($request['LEAD_DETAILS'])?trim($request['LEAD_DETAILS']):NULL;
        $LANDLINE_NUMBER        =   trim($request['LANDNUMBER'])?trim($request['LANDNUMBER']):NULL;
        $MOBILE_NUMBER          =   trim($request['MOBILENUMBER'])?trim($request['MOBILENUMBER']):NULL;
        $EMAIL                  =   trim($request['EMAIL'])?trim($request['EMAIL']):NULL;
        $LEAD_SOURCE            =   trim($request['LSOURCEID_REF'])?trim($request['LSOURCEID_REF']):NULL;
        $LEAD_STATUS            =   trim($request['LSTATUSID_REF'])?trim($request['LSTATUSID_REF']):NULL;
        $ASSIGNTO_REF           =   trim($request['ASSIGTOID_REF'])?trim($request['ASSIGTOID_REF']):NULL;
        $ADDRESS                =   trim($request['ADDRESS'])?trim($request['ADDRESS']):NULL;
        $CTRYID_REF             =   trim($request['COUNTRYID_REF'])?trim($request['COUNTRYID_REF']):NULL;
        $STID_REF               =   trim($request['STATEID_REF'])?trim($request['STATEID_REF']):NULL;
        $CITYID_REF             =   trim($request['CITYID_REF'])?trim($request['CITYID_REF']):NULL;
        $PINCODE                =   trim($request['PINCODE'])?trim($request['PINCODE']):NULL;
        $CONVERT_STATUS         =   trim($request['CONVERTSTATUS'])?trim($request['CONVERTSTATUS']):NULL;
        $WEBSITE                =   trim($request['WEBSITENAME'])?trim($request['WEBSITENAME']):NULL;
        $LEAD_CLOSURE           =   trim($request['LCLOSUR'])?trim($request['LCLOSUR']):0;
        $REMARKS                =   trim($request['REMARKS'])?trim($request['REMARKS']):NULL;
        $DEALERID_REF           =   trim($request['DEALERIDREF'])?trim($request['DEALERIDREF']):0;
        $USERID_IDREF           =   trim($USER_IDREF)?trim($USER_IDREF):0;
        

        $ProductDetails  = array();
        if(isset($_REQUEST['PRODUCTID_REF']) && !empty($_REQUEST['PRODUCTID_REF'])){
            foreach($_REQUEST['PRODUCTID_REF'] as $key=>$val){

                $ProductDetails[] = array(
                'ITEMID_REF'      => trim($_REQUEST['PRODUCTID_REF'][$key])?trim($_REQUEST['PRODUCTID_REF'][$key]):NULL,
                'QUANTITY'        => trim($_REQUEST['PRODUCT_QTY'][$key])?trim($_REQUEST['PRODUCT_QTY'][$key]):0,
                'RATE'            => trim($_REQUEST['PRODUCT_PRICE'][$key])?trim($_REQUEST['PRODUCT_PRICE'][$key]):0,
                'AMOUNT'          => trim($_REQUEST['PRODUCT_AMOUNT'][$key])?trim($_REQUEST['PRODUCT_AMOUNT'][$key]):0,
                );
            }
        }

        //dd($ProductDetails);
    
        if(!empty($ProductDetails)){
            $wrapped_linkspd["PRODUCT"] = $ProductDetails; 
            $XMLPRODUCT = ArrayToXml::convert($wrapped_linkspd);
        }
        else{
            $XMLPRODUCT = NULL; 
        }

        $XMLBLOCK        =   NULL;  
        $XMLSITE         =   NULL;
        $XMLACTIVITY     =   NULL;
        $XMLTASK         =   NULL;

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');    
        $USERID_REF     =   Auth::user()->USERID;   
        $VTID_REF       =   $this->vtid_ref;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();

        $array_data     = [$LEAD_NO,                $LEAD_DT,                   $CUSTOMER_TYPE,         $CUSTOMER_PROSPECT,     $COMPANY_NAME,      $FIRST_NAME,    $LAST_NAME,     $LEADOWNERID_REF,   $INDSID_REF,        $DESGINATION,
                           $LANDLINE_NUMBER,        $MOBILE_NUMBER, $EMAIL,     $LEAD_SOURCE,           $LEAD_STATUS,           $ASSIGNTO_REF,      $ADDRESS,       $CITYID_REF,    $STID_REF,          $CTRYID_REF,        $PINCODE,
                           $OPPORTUNITY_TYPE_ID,    $OPPORTUNITY_STAGE_ID,      $CYID_REF,              $BRID_REF,              $FYID_REF,          $VTID_REF,      $XMLBLOCK,      $XMLSITE,           $XMLACTIVITY,       $XMLTASK,
                           $USERID_REF,             $UPDATE,                    $UPTIME,                $ACTION,                $IPADDRESS,         $XMLPRODUCT,    $EXPECTED_DATE, $OPPORTUNITY_DATE,  $CONVERT_STATUS,    $CONTACT_PERSON,
                           $LEAD_DETAILS,           $WEBSITE,                   $REMARKS,               $LEAD_CLOSURE,          $DEALERID_REF,      $USERID_IDREF ];

                           //dd($array_data);

        $sp_result = DB::select('EXEC SP_LEAD_GEN_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $array_data);

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
            
            $design         = $this->Designation();
            $country        = $this->country();
            $state          = $this->State();
            $city           = $this->City();
            $activitytype   = $this->ActivityType();
            $resp           = $this->Response();
            $emplyee        = $this->AssignedToEmplyee();
            $tasktype       = $this->TaskType();
            $priorty        = $this->Priority();
            $conctprsn      = $this->ContactPerson();            

            $objResponse = DB::table('TBL_TRN_LEAD_GENERATION')
            ->where('TBL_TRN_LEAD_GENERATION.CYID_REF','=',$CYID_REF)
            ->where('TBL_TRN_LEAD_GENERATION.BRID_REF','=',$BRID_REF)
            ->where('LEAD_ID','=',$id)
            ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_LEAD_GENERATION.CUSTOMER_PROSPECT','=','TBL_MST_CUSTOMER.SLID_REF')
            ->leftJoin('TBL_MST_PROSPECT', 'TBL_TRN_LEAD_GENERATION.CUSTOMER_PROSPECT','=','TBL_MST_PROSPECT.PID')
            ->leftJoin('TBL_MST_COUNTRY', 'TBL_TRN_LEAD_GENERATION.CTRYID_REF','=','TBL_MST_COUNTRY.CTRYID')
            ->leftJoin('TBL_MST_STATE', 'TBL_TRN_LEAD_GENERATION.STID_REF','=','TBL_MST_STATE.STID')
            ->leftJoin('TBL_MST_CITY', 'TBL_TRN_LEAD_GENERATION.CITYID_REF','=','TBL_MST_CITY.CITYID')
            ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_TRN_LEAD_GENERATION.LEADOWNERID_REF','=','TBL_MST_EMPLOYEE.EMPID')
            ->leftJoin('TBL_MST_INDUSTRYTYPE', 'TBL_TRN_LEAD_GENERATION.INDSID_REF','=','TBL_MST_INDUSTRYTYPE.INDSID')
            ->leftJoin('TBL_MST_LEAD_SOURCE', 'TBL_TRN_LEAD_GENERATION.LEAD_SOURCE','=','TBL_MST_LEAD_SOURCE.ID')
            ->leftJoin('TBL_MST_LEAD_STATUS', 'TBL_TRN_LEAD_GENERATION.LEAD_STATUS','=','TBL_MST_LEAD_STATUS.ID')
            ->leftJoin('TBL_MST_OPPORTUNITY_TYPE', 'TBL_TRN_LEAD_GENERATION.OPPORTUNITY_TYPE_ID','=','TBL_MST_OPPORTUNITY_TYPE.ID')
            ->leftJoin('TBL_MST_OPPORTUNITY_STAGE', 'TBL_TRN_LEAD_GENERATION.OPPORTUNITY_STAGE_ID','=','TBL_MST_OPPORTUNITY_STAGE.ID')
            ->select('TBL_TRN_LEAD_GENERATION.*','TBL_MST_OPPORTUNITY_TYPE.ID AS OPPTYTYPEID','TBL_MST_OPPORTUNITY_TYPE.OPPORTUNITY_TYPECODE','TBL_MST_OPPORTUNITY_TYPE.OPPORTUNITY_TYPENAME','TBL_MST_OPPORTUNITY_STAGE.ID','TBL_MST_OPPORTUNITY_STAGE.OPPORTUNITY_STAGECODE',
            'TBL_MST_OPPORTUNITY_STAGE.OPPORTUNITY_STAGENAME','TBL_MST_OPPORTUNITY_STAGE.COMPLETE_PERCENT','TBL_MST_CUSTOMER.SLID_REF','TBL_MST_CUSTOMER.CCODE','TBL_MST_CUSTOMER.NAME AS CUSTNAME','TBL_MST_PROSPECT.PID','TBL_MST_PROSPECT.PCODE','TBL_MST_PROSPECT.NAME AS PROSNAME',
            'TBL_MST_COUNTRY.CTRYID','TBL_MST_COUNTRY.CTRYCODE','TBL_MST_COUNTRY.NAME AS CONTRYNAME','TBL_MST_STATE.STID','TBL_MST_STATE.STCODE','TBL_MST_STATE.NAME AS STATENAME','TBL_MST_CITY.CITYID','TBL_MST_CITY.CITYCODE','TBL_MST_CITY.NAME AS CITYNAME',
            'TBL_MST_EMPLOYEE.EMPID','TBL_MST_EMPLOYEE.EMPCODE','TBL_MST_EMPLOYEE.FNAME', 'TBL_MST_INDUSTRYTYPE.INDSID','TBL_MST_INDUSTRYTYPE.INDSCODE','TBL_MST_INDUSTRYTYPE.DESCRIPTIONS', 
            'TBL_MST_LEAD_SOURCE.ID AS LEADSID','TBL_MST_LEAD_SOURCE.LEAD_SOURCECODE','TBL_MST_LEAD_SOURCE.LEAD_SOURCENAME','TBL_MST_LEAD_STATUS.ID AS LEADSTUSID','TBL_MST_LEAD_STATUS.LEAD_STATUSCODE','TBL_MST_LEAD_STATUS.LEAD_STATUSNAME')
            ->first();

            $objResp = DB::table('TBL_TRN_LEAD_GENERATION')
            ->where('TBL_TRN_LEAD_GENERATION.CYID_REF','=',$CYID_REF)
            ->where('TBL_TRN_LEAD_GENERATION.BRID_REF','=',$BRID_REF)
            ->where('LEAD_ID','=',$id)
            ->where('TBL_MST_CUSTOMER.TYPE','=','DEALER')
            ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_TRN_LEAD_GENERATION.ASSIGNTO_REF','=','TBL_MST_EMPLOYEE.EMPID')
            ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_LEAD_GENERATION.DEALERID_REF','=','TBL_MST_CUSTOMER.CID')
            ->select('TBL_TRN_LEAD_GENERATION.*','TBL_MST_EMPLOYEE.EMPID AS ASSGNTOID','TBL_MST_EMPLOYEE.EMPCODE AS ASSGNTOCODE','TBL_MST_EMPLOYEE.FNAME AS ASSGNTOFNAME',
            'TBL_MST_CUSTOMER.CID AS DOCID','TBL_MST_CUSTOMER.CCODE AS DOCNO','TBL_MST_CUSTOMER.NAME AS DEALERNAME')
            ->first();

            $MAT = DB::table('TBL_TRN_LEAD_BLOCK_DET')                  
            ->where('TBL_TRN_LEAD_BLOCK_DET.LEADID_REF','=',$id)
            ->get();
            $MAT    = count($MAT) > 0 ?$MAT:[0];

            $MATSITE = DB::table('TBL_TRN_LEAD_SITE_DET') 
            ->leftJoin('TBL_MST_STATE', 'TBL_TRN_LEAD_SITE_DET.STID_REF','=','TBL_MST_STATE.STID')
            ->leftJoin('TBL_MST_CITY', 'TBL_TRN_LEAD_SITE_DET.CITYID_REF','=','TBL_MST_CITY.CITYID')
            ->where('TBL_TRN_LEAD_SITE_DET.LEADID_REF','=',$id)
            ->select('TBL_TRN_LEAD_SITE_DET.*', 'TBL_MST_STATE.STID','TBL_MST_STATE.STCODE','TBL_MST_STATE.NAME','TBL_MST_CITY.CITYID','TBL_MST_CITY.CITYCODE','TBL_MST_CITY.NAME AS CITYNAME')
            ->get();
            $MATSITE    = count($MATSITE) > 0 ?$MATSITE:[0];

            $MATNEWCALL = DB::select("SELECT T1.*,T2.*,T3.* FROM TBL_TRN_LEAD_ACTIVITY T1
            LEFT JOIN TBL_MST_ACTIVITY_TYPE T2 ON T1.ACTIVITYID_REF=T2.ID
            LEFT JOIN TBL_MST_RESPONSE_TYPE T3 ON T1.RESPONSEID_REF=T3.ID
            WHERE T1.LEADID_REF='$id' ORDER BY T1.ACTIVITY_ID ASC");

            if(isset($MATNEWCALL) && !empty($MATNEWCALL)){
                foreach($MATNEWCALL as $key=>$val){

                    $ADDITIONAL_EMPLOYEE_ID       =   $val->ADDITIONAL_EMPLOYEE_ID;
                    $ALERT_TO                     =   $val->ALERT_TO;

                    if($ADDITIONAL_EMPLOYEE_ID !=""){
                        $LEADACT_DATA = DB::select("select distinct stuff((select ',' + t.[FNAME] from TBL_MST_EMPLOYEE t where EMPID in($ADDITIONAL_EMPLOYEE_ID) order by t.[FNAME] for xml path('') ),1,1,'') as FNAME from TBL_MST_EMPLOYEE t1 where EMPID in($ADDITIONAL_EMPLOYEE_ID)"); 
                        $FNAME =   isset($LEADACT_DATA[0]->FNAME) && $LEADACT_DATA[0]->FNAME !=""?$LEADACT_DATA[0]->FNAME:NULL; 
                        $MATNEWCALL[$key]->FNAME=$FNAME;
                    }

                    if($ALERT_TO !=""){
                        $LEADALTO_DATA = DB::select("select distinct stuff((select ',' + t.[FNAME] from TBL_MST_EMPLOYEE t where EMPID in($ALERT_TO) order by t.[FNAME] for xml path('') ),1,1,'') as ALTFNAME from TBL_MST_EMPLOYEE t1 where EMPID in($ALERT_TO)"); 
                        $ALTFNAME =   isset($LEADALTO_DATA[0]->ALTFNAME) && $LEADALTO_DATA[0]->ALTFNAME !=""?$LEADALTO_DATA[0]->ALTFNAME:NULL; 
                        $MATNEWCALL[$key]->ALTFNAME=$ALTFNAME;
                    }
                }
            }
            $MATNEWCALL    = count($MATNEWCALL) > 0 ?$MATNEWCALL:[0];

            //dd($MATNEWCALL);

            $NEWTASKS = DB::table('TBL_TRN_LEAD_TASK')
            ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_TRN_LEAD_TASK.TASK_ASSIGNTO_REF','=','TBL_MST_EMPLOYEE.EMPID')
            ->where('TBL_TRN_LEAD_TASK.LEADID_REF','=',$id)
            ->select('TBL_TRN_LEAD_TASK.*', 'TBL_MST_EMPLOYEE.EMPID','TBL_MST_EMPLOYEE.EMPCODE','TBL_MST_EMPLOYEE.FNAME')
            ->get();
            $NEWTASKS    = count($NEWTASKS) > 0 ?$NEWTASKS:[0];

            $PRODUCTDETAILS = DB::table('TBL_TRN_LEAD_PRODUCT') 
            ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_LEAD_PRODUCT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
            ->where('TBL_TRN_LEAD_PRODUCT.LEADID_REF','=',$id)
            ->select('TBL_TRN_LEAD_PRODUCT.*', 'TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME')
            ->get();
            $PRODUCTDETAILS    = count($PRODUCTDETAILS) > 0 ?$PRODUCTDETAILS:[0];

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();
            
            return view($this->view.$FormId.$type,compact(['objResponse','objRights','design','country','state','city','activitytype','resp','emplyee','tasktype','priorty','ActionStatus','FormId','MAT','objResp','MATSITE','MATNEWCALL','NEWTASKS','PRODUCTDETAILS','conctprsn']));
        }
    }



    public function updateRecord($request,$type){

        $FormId     =   $this->form_id;
        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref; 
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $USER_IDREF =   Auth::user()->USERID;   

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

        $LEAD_NO                =   trim($request['LEAD_NO'])?trim($request['LEAD_NO']):NULL;
        $LEAD_DT                =   trim($request['LEAD_DT'])?trim($request['LEAD_DT']):NULL;
        $CUSTOMER_PROSPECT      =   trim($request['CUSTOMER_PROSPECT'])?trim($request['CUSTOMER_PROSPECT']):NULL;
        $CUSTOMER_TYPE          =   trim($request['CUSTOMER_TYPE'])?trim($request['CUSTOMER_TYPE']):NULL;
        $OPPORTUNITY_TYPE_ID    =   trim($request['OPPRTYPEID_REF'])?trim($request['OPPRTYPEID_REF']):NULL;
        $OPPORTUNITY_STAGE_ID   =   trim($request['OPPRSTAGEID_REF'])?trim($request['OPPRSTAGEID_REF']):NULL;
        $EXPECTED_DATE          =   trim($request['EXPECTED_DT'])?trim($request['EXPECTED_DT']):NULL;
        $OPPORTUNITY_DATE       =   trim($request['OPPORTUNITY_DT'])?trim($request['OPPORTUNITY_DT']):NULL;
        $COMPANY_NAME           =   trim($request['COMPANY_NAME'])?trim($request['COMPANY_NAME']):NULL;
        $FIRST_NAME             =   trim($request['FNAME'])?trim($request['FNAME']):NULL;
        $LAST_NAME              =   trim($request['LNAME'])?trim($request['LNAME']):NULL;
        $LEADOWNERID_REF        =   trim($request['LOWNERID_REF'])?trim($request['LOWNERID_REF']):NULL;
        $INDSID_REF             =   trim($request['INTYPEID_REF'])?trim($request['INTYPEID_REF']):NULL;
        $DESGINATION            =   trim($request['DESIGNID_REF'])?trim($request['DESIGNID_REF']):NULL;
        $CONTACT_PERSON         =   trim($request['CONTACT_PERSON'])?trim($request['CONTACT_PERSON']):NULL;
        $LEAD_DETAILS           =   trim($request['LEAD_DETAILS'])?trim($request['LEAD_DETAILS']):NULL;
        $LANDLINE_NUMBER        =   trim($request['LANDNUMBER'])?trim($request['LANDNUMBER']):NULL;
        $MOBILE_NUMBER          =   trim($request['MOBILENUMBER'])?trim($request['MOBILENUMBER']):NULL;
        $EMAIL                  =   trim($request['EMAIL'])?trim($request['EMAIL']):NULL;
        $LEAD_SOURCE            =   trim($request['LSOURCEID_REF'])?trim($request['LSOURCEID_REF']):NULL;
        $LEAD_STATUS            =   trim($request['LSTATUSID_REF'])?trim($request['LSTATUSID_REF']):NULL;
        $ASSIGNTO_REF           =   trim($request['ASSIGTOID_REF'])?trim($request['ASSIGTOID_REF']):NULL;
        $ADDRESS                =   trim($request['ADDRESS'])?trim($request['ADDRESS']):NULL;
        $CTRYID_REF             =   trim($request['COUNTRYID_REF'])?trim($request['COUNTRYID_REF']):NULL;
        $STID_REF               =   trim($request['STATEID_REF'])?trim($request['STATEID_REF']):NULL;
        $CITYID_REF             =   trim($request['CITYID_REF'])?trim($request['CITYID_REF']):NULL;
        $PINCODE                =   trim($request['PINCODE'])?trim($request['PINCODE']):NULL;
        $CONVERT_STATUS         =   trim($request['CONVERTSTATUS'])?trim($request['CONVERTSTATUS']):NULL;
        $WEBSITE                =   trim($request['WEBSITENAME'])?trim($request['WEBSITENAME']):NULL;
        $LEAD_CLOSURE           =   trim($request['LCLOSUR'])?trim($request['LCLOSUR']):0;
        $REMARKS                =   trim($request['REMARKS'])?trim($request['REMARKS']):NULL;
        $DEALERID_REF           =   trim($request['DEALERIDREF'])?trim($request['DEALERIDREF']):0;
        $REASONOF_CLOSURE       =   trim($request['REASONOF_CLOSURE'])?trim($request['REASONOF_CLOSURE']):NULL;
        $USERID_IDREF           =   trim($USER_IDREF)?trim($USER_IDREF):0;


        $DetailBlock  = array();
        if(isset($_REQUEST['TITAL']) && !empty($_REQUEST['TITAL'])){
            foreach($_REQUEST['TITAL'] as $key=>$val){

                $DetailBlock[] = array(
                'TITLE'                 => trim($_REQUEST['TITAL'][$key])?trim($_REQUEST['TITAL'][$key]):NULL,
                'FIRST_NAME'            => trim($_REQUEST['FIRSTNAME'][$key])?trim($_REQUEST['FIRSTNAME'][$key]):NULL,
                'LAST_NAME'             => trim($_REQUEST['LASTNAME'][$key])?trim($_REQUEST['LASTNAME'][$key]):NULL,
                'DESGINATION'           => trim($_REQUEST['DESIG'][$key])?trim($_REQUEST['DESIG'][$key]):NULL,
                'MOBILE_NUMBER'         => trim($_REQUEST['MOBILE'][$key])?trim($_REQUEST['MOBILE'][$key]):NULL,
                'EMAIL'                 => trim($_REQUEST['EMAILS'][$key])?trim($_REQUEST['EMAILS'][$key]):NULL,
                );
            }
        }

        //dd($DetailBlock);
    
        if(!empty($DetailBlock)){
            $wrapped_linksdb["BLOCK"] = $DetailBlock; 
            $XMLBLOCK = ArrayToXml::convert($wrapped_linksdb);
        }
        else{
            $XMLBLOCK = NULL; 
        }


        $SiteDetails  = array();
        if(isset($_REQUEST['SITENAME']) && !empty($_REQUEST['SITENAME'])){
            foreach($_REQUEST['SITENAME'] as $key=>$val){

                $SiteDetails[] = array(
                'SITENAME'               => trim($_REQUEST['SITENAME'][$key])?trim($_REQUEST['SITENAME'][$key]):NULL,
                'ADDRESS1'               => trim($_REQUEST['ADDRESS1'][$key])?trim($_REQUEST['ADDRESS1'][$key]):NULL,
                'ADDRESS2'               => trim($_REQUEST['ADDRESS2'][$key])?trim($_REQUEST['ADDRESS2'][$key]):NULL,
                'CTRYID_REF'             => trim($_REQUEST['CTRYID_REF'][$key])?trim($_REQUEST['CTRYID_REF'][$key]):NULL,
                'STID_REF'               => trim($_REQUEST['STATEIDREF'][$key])?trim($_REQUEST['STATEIDREF'][$key]):NULL,
                'CITYID_REF'             => trim($_REQUEST['CITYIDREF'][$key])?trim($_REQUEST['CITYIDREF'][$key]):NULL,
                'PINCODE'                => trim($_REQUEST['PINCODES'][$key])?trim($_REQUEST['PINCODES'][$key]):NULL,
                'CONTACT_NUMBER'         => trim($_REQUEST['PHONENO'][$key])?trim($_REQUEST['PHONENO'][$key]):NULL,
                'MOBILE_NUMBER'          => trim($_REQUEST['MOBILENO'][$key])?trim($_REQUEST['MOBILENO'][$key]):NULL,
                );
            }
        }

        //dd($SiteDetails);
    
        if(!empty($SiteDetails)){
            $wrapped_linkssd["SITE"] = $SiteDetails; 
            $XMLSITE = ArrayToXml::convert($wrapped_linkssd);
        }
        else{
            $XMLSITE = NULL; 
        }

        $NewCall  = array();
        if(isset($_REQUEST['ACTIVITYIDREF']) && !empty($_REQUEST['ACTIVITYIDREF'])){
            foreach($_REQUEST['ACTIVITYIDREF'] as $key=>$val){

                $NewCall[] = array(
                'ACTIVITYID_REF'            => trim($_REQUEST['ACTIVITYIDREF'][$key])?trim($_REQUEST['ACTIVITYIDREF'][$key]):0,
                'ACTIVITY_DATE'             => trim($_REQUEST['ACTYDATE'][$key])?trim($_REQUEST['ACTYDATE'][$key]):NULL,
                'CONTACT_PERSON'            => trim($_REQUEST['CONTACTPERSON'][$key])?trim($_REQUEST['CONTACTPERSON'][$key]):NULL,
                'ACTIVITY_DETAILS'          => trim($_REQUEST['ACTYDETAIL'][$key])?trim($_REQUEST['ACTYDETAIL'][$key]):NULL,
                'RESPONSEID_REF'            => trim($_REQUEST['RESPONSE'][$key])?trim($_REQUEST['RESPONSE'][$key]):0,
                'ACTION_PLAN'               => trim($_REQUEST['ACTPLAN'][$key])?trim($_REQUEST['ACTPLAN'][$key]):NULL,
                'REMINDER_DETAIL'           => trim($_REQUEST['REMNDETAILID_REF'][$key])?trim($_REQUEST['REMNDETAILID_REF'][$key]):NULL,
                'REMINDER_DATE'             => trim($_REQUEST['REMNDDATE'][$key])?trim($_REQUEST['REMNDDATE'][$key]):NULL,
                'ALERT_MESSAGE'             => trim($_REQUEST['ALERTMSG'][$key])?trim($_REQUEST['ALERTMSG'][$key]):NULL,
                'ALERT_TO'                  => trim($_REQUEST['ALERTTOID_REF'][$key])?trim($_REQUEST['ALERTTOID_REF'][$key]):NULL,
                'ACTIVITY_TIME'             => trim(date('H:i:s',strtotime($_REQUEST['ACTIVITYTIME'][$key])))?trim(date('H:i:s',strtotime($_REQUEST['ACTIVITYTIME'][$key]))):NULL,
                'REMINDER_TIME'             => trim(date('H:i:s',strtotime($_REQUEST['REMINDERTIME'][$key])))?trim(date('H:i:s',strtotime($_REQUEST['REMINDERTIME'][$key]))):NULL,
                'ADDITIONAL_EMPLOYEE_ID'    => trim($_REQUEST['ADDMEMBERVISITID_REF'][$key])?trim($_REQUEST['ADDMEMBERVISITID_REF'][$key]):NULL,
                'EXPENSE_DETAILS'           => trim($_REQUEST['EXPDETAILS'][$key])?trim($_REQUEST['EXPDETAILS'][$key]):NULL,
                'TENTATIVE_EXPENSES'        => trim($_REQUEST['TENTEXPAMT'][$key])?trim($_REQUEST['TENTEXPAMT'][$key]):0,
                );
            }
        }

        //dd($NewCall);
    
        if(!empty($NewCall)){
            $wrapped_linksnc["ACTIVITY"] = $NewCall; 
            $XMLACTIVITY = ArrayToXml::convert($wrapped_linksnc);
        }
        else{
            $XMLACTIVITY = NULL; 
        }


        $NewTasks  = array();
        if(isset($_REQUEST['TASKTYPEID_REF']) && !empty($_REQUEST['TASKTYPEID_REF'])){
            foreach($_REQUEST['TASKTYPEID_REF'] as $key=>$val){

                $NewTasks[] = array(
                'TASKID_REF'           => trim($_REQUEST['TASKTYPEID_REF'][$key])?trim($_REQUEST['TASKTYPEID_REF'][$key]):0,
                'TASK_ASSIGNTO_REF'    => trim($_REQUEST['ASSGNDTOID_REF'][$key])?trim($_REQUEST['ASSGNDTOID_REF'][$key]):NULL,
                'SUBJECT'              => trim($_REQUEST['SUBJECT'][$key])?trim($_REQUEST['SUBJECT'][$key]):NULL,
                'PRIORITYID_REF'       => trim($_REQUEST['PRIORITYID_REF'][$key])?trim($_REQUEST['PRIORITYID_REF'][$key]):NULL,
                'TASK_DETAIL'          => trim($_REQUEST['TASKDETAIL'][$key])?trim($_REQUEST['TASKDETAIL'][$key]):NULL,
                'DUE_DATE'             => trim($_REQUEST['DUEDATE'][$key])?trim($_REQUEST['DUEDATE'][$key]):NULL,
                'TASK_STATUS'          => trim($_REQUEST['STATUSID_REF'][$key])?trim($_REQUEST['STATUSID_REF'][$key]):NULL,
                'TASK_REMINDER_DATE'   => trim($_REQUEST['REMINDER'][$key])?trim($_REQUEST['REMINDER'][$key]):NULL,
                );
            }
        }

        //dd($NewTasks);
    
        if(!empty($NewTasks)){
            $wrapped_linksnt["TASK"] = $NewTasks; 
            $XMLTASK = ArrayToXml::convert($wrapped_linksnt);
        }
        else{
            $XMLTASK = NULL; 
        }


        $ProductDetails  = array();
        if(isset($_REQUEST['PRODUCTID_REF']) && !empty($_REQUEST['PRODUCTID_REF'])){
            foreach($_REQUEST['PRODUCTID_REF'] as $key=>$val){

                $ProductDetails[] = array(
                'ITEMID_REF'      => trim($_REQUEST['PRODUCTID_REF'][$key])?trim($_REQUEST['PRODUCTID_REF'][$key]):NULL,
                'QUANTITY'        => trim($_REQUEST['PRODUCT_QTY'][$key])?trim($_REQUEST['PRODUCT_QTY'][$key]):0,
                'RATE'            => trim($_REQUEST['PRODUCT_PRICE'][$key])?trim($_REQUEST['PRODUCT_PRICE'][$key]):0,
                'AMOUNT'          => trim($_REQUEST['PRODUCT_AMOUNT'][$key])?trim($_REQUEST['PRODUCT_AMOUNT'][$key]):0,
                );
            }
        }

        //dd($ProductDetails);
    
        if(!empty($ProductDetails)){
            $wrapped_linkspd["PRODUCT"] = $ProductDetails; 
            $XMLPRODUCT = ArrayToXml::convert($wrapped_linkspd);
        }
        else{
            $XMLPRODUCT = NULL; 
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
            

        $array_data     = [$LEAD_NO,                $LEAD_DT,                   $CUSTOMER_TYPE,         $CUSTOMER_PROSPECT,     $COMPANY_NAME,      $FIRST_NAME,    $LAST_NAME,     $LEADOWNERID_REF,   $INDSID_REF,        $DESGINATION,
                           $LANDLINE_NUMBER,        $MOBILE_NUMBER, $EMAIL,     $LEAD_SOURCE,           $LEAD_STATUS,           $ASSIGNTO_REF,      $ADDRESS,       $CITYID_REF,    $STID_REF,          $CTRYID_REF,        $PINCODE,
                           $OPPORTUNITY_TYPE_ID,    $OPPORTUNITY_STAGE_ID,      $CYID_REF,              $BRID_REF,              $FYID_REF,          $VTID_REF,      $XMLBLOCK,      $XMLSITE,           $XMLACTIVITY,       $XMLTASK,
                           $USERID_REF,             $UPDATE,                    $UPTIME,                $ACTION,                $IPADDRESS,         $XMLPRODUCT,    $EXPECTED_DATE, $OPPORTUNITY_DATE,  $CONVERT_STATUS,    $CONTACT_PERSON,
                           $LEAD_DETAILS,           $WEBSITE,                   $REMARKS,               $LEAD_CLOSURE,          $DEALERID_REF,      $REASONOF_CLOSURE,  $USERID_IDREF];

        
            $sp_result = DB::select('EXEC SP_LEAD_GEN_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $array_data);

            //dd($sp_result);

            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);
    
            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
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
        $TABLE      =   "TBL_TRN_LEAD_GENERATION";
        $FIELD      =   "LEAD_ID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_LEAD_GENERATION',
        ];

        $req_data[1]=[
            'NT'  => 'TBL_TRN_LEAD_BLOCK_DET',
        ];

        $req_data[2]=[
            'NT'  => 'TBL_TRN_LEAD_SITE_DET',
        ];

        $req_data[3]=[
            'NT'  => 'TBL_TRN_LEAD_TASK',
        ];

        $req_data[4]=[
            'NT'  => 'TBL_TRN_LEAD_ACTIVITY',
        ];

        $req_data[5]=[
            'NT'  => 'TBL_TRN_LEAD_PRODUCT',
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

        if(!is_null($id))
        {
            $FormId      =   $this->form_id;
            $objResponse = DB::table('TBL_TRN_LEAD_GENERATION')
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('LEAD_ID','=',$id)
            ->first();

            $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
            ->where('VTID','=',$this->vtid_ref)
            ->select('VTID','VCODE','DESCRIPTIONS')
            ->get()->toArray();
            
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

            return view($this->view.$FormId.'attachment',compact(['objResponse','objMstVoucherType','objAttachments','FormId']));
        }

    }

    
   public function docuploads(Request $request){

    $FormId   =   $this->form_id;
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
    
    $destinationPath = storage_path()."/docs/company".$CYID_REF."/LeadGeneration";

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
        return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","The file is already exist");
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

        public function country(){

            $CYID_REF   =   Auth::user()->CYID_REF;
            $ObjData    =   DB::select("SELECT * 
                            FROM TBL_MST_COUNTRY 
                            WHERE CYID_REF='$CYID_REF' AND STATUS='A' AND (DEACTIVATED IS NULL  OR DEACTIVATED = 0)"
                            );

            return $ObjData; 

        }

        public function getstate(Request $request){

            $CYID_REF   =   Auth::user()->CYID_REF;
            $id         =   $request['id'];
            $ObjData    =   DB::select("SELECT * 
                            FROM TBL_MST_STATE 
                            WHERE CYID_REF='$CYID_REF' AND CTRYID_REF='$id' AND STATUS='A' AND (DEACTIVATED IS NULL  OR DEACTIVATED = 0)"
                            );
                            
            echo'<option value="">Select</option>';  
            if(isset($ObjData) && !empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){
                    echo'<option value="'.$dataRow->STID.'">'.$dataRow->STCODE.'-'.$dataRow->NAME.'</option>';  
                }
            }
            exit();
        }

        public function getcity(Request $request){

            $CYID_REF   =   Auth::user()->CYID_REF;
            $id         =   $request['id'];
            $ObjData    =   DB::select("SELECT * 
                            FROM TBL_MST_CITY 
                            WHERE CYID_REF='$CYID_REF' AND STID_REF='$id' AND STATUS='A' AND (DEACTIVATED IS NULL  OR DEACTIVATED = 0)"
                            );

            echo'<option value="">Select</option>';     
            if(isset($ObjData) && !empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){
                    echo '<option value="'.$dataRow->CITYID.'">'.$dataRow->CITYCODE.'-'.$dataRow->NAME.'</option>';            
                }
            }
            exit();
        }



        public function Designation(){

            $CYID_REF       =   Auth::user()->CYID_REF;
            $BRID_REF       =   Session::get('BRID_REF');
        
            $indtype = DB::table('TBL_MST_DESIGNATION')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
            ->where('STATUS','=','A')
            ->get();
            return $indtype; 
            
            }

            public function ContactPerson(){
                $leadgen = DB::table('TBL_TRN_LEAD_GENERATION')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('STATUS','=','A')
                ->get();
                return $leadgen; 
                
                }

            public function AssignedToEmplyee(){

                $CYID_REF       =   Auth::user()->CYID_REF;
                $BRID_REF       =   Session::get('BRID_REF');
            
                $AssignEmp = DB::table('TBL_MST_EMPLOYEE')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                ->where('STATUS','=','A')
                ->get();
                return $AssignEmp; 
                
                }

                public function IndustryType(){

                    $CYID_REF       =   Auth::user()->CYID_REF;
                
                    $indtype = DB::table('TBL_MST_INDUSTRYTYPE')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                    ->where('STATUS','=','A')
                    ->get();
                    return $indtype; 
                    
                    }

                public function ActivityType(){
                    $ActiType = DB::table('TBL_MST_ACTIVITY_TYPE')
                    ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                    ->where('STATUS','=','A')
                    ->get();
                    return $ActiType; 
                   }

                   public function Customer(){
                    $cust = DB::table('TBL_MST_CUSTOMER')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                    ->where('STATUS','=','A')
                    ->get();
                    return $cust; 
                    }

                    public function Prospect(){
                        $Pros = DB::table('TBL_MST_PROSPECT')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                        ->where('STATUS','=','A')
                        ->get();
                        return $Pros; 
                       }

                public function Response(){
                    $Resp = DB::table('TBL_MST_RESPONSE_TYPE')
                    ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                    ->where('STATUS','=','A')
                    ->get();
                    return $Resp; 
                   }

                   public function TaskType(){
                    $taskType = DB::table('TBL_MST_TASK_TYPE_TYPE')
                    ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                    ->where('STATUS','=','A')
                    ->get();
                    return $taskType; 
                   }

                   public function LeadSource(){
                    $ldSource = DB::table('TBL_MST_LEAD_SOURCE')
                    ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                    ->where('STATUS','=','A')
                    ->get();
                       return $ldSource; 
                   }

                   public function LeadStatus(){
                    $ldstatus = DB::table('TBL_MST_LEAD_STATUS')
                    ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                    ->where('STATUS','=','A')
                    ->get();
                       return $ldstatus; 
                   }

                   public function Priority(){

                    $CYID_REF       =   Auth::user()->CYID_REF;
                    $BRID_REF       =   Session::get('BRID_REF');

                    $ldstatus = DB::table('TBL_MST_PAY_PERIOD')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('BRID_REF','=',Session::get('BRID_REF'))
                    ->where('STATUS','=','A')
                    ->get();
                       return $ldstatus; 
                   }

                   public function State(){
                    $CYID_REF       =   Auth::user()->CYID_REF;
                    $states = DB::table('TBL_MST_STATE')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('STATUS','=','A')
                    ->get();
                       return $states; 
                   }

                   public function City(){
                    $CYID_REF       =   Auth::user()->CYID_REF;
                    $citys = DB::table('TBL_MST_CITY')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('STATUS','=','A')
                    ->get();
                       return $citys; 
                   }

         
    

                 
/*************************************   Opportunity Type Code    ****************************************************** */

        public function getOpportunityTypeCode(Request $request){
            $ObjData = DB::table('TBL_MST_OPPORTUNITY_TYPE')
            ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
            ->where('STATUS','=','A')
            ->get();

            if(isset($ObjData) && !empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){

                echo'
                <tr>
                    <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->ID .'" class="clsopptype" value="'.$dataRow->ID.'" ></td>
                    <td class="ROW2">'.$dataRow->OPPORTUNITY_TYPECODE.'</td>
                    <td class="ROW3">'.$dataRow->OPPORTUNITY_TYPENAME.'</td>
                    <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->ID.'" data-desc="'.$dataRow->OPPORTUNITY_TYPECODE.'-'.$dataRow->OPPORTUNITY_TYPENAME.'" data-ccname="'.$dataRow->OPPORTUNITY_TYPENAME.'" value="'.$dataRow->ID.'"/></td>
                </tr>
                ';
                }
            }
            else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
        exit();
    }



                   
/*************************************   Opportunity Stage Code    ****************************************************** */

        public function getOpportunityStageCode(Request $request){
            $ObjData = DB::table('TBL_MST_OPPORTUNITY_STAGE')
            ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
            ->where('STATUS','=','A')
            ->get();

            if(isset($ObjData) && !empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){

                echo'
                <tr>
                    <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->ID .'" class="clsoppstage" value="'.$dataRow->ID.'" ></td>
                    <td class="ROW2">'.$dataRow->OPPORTUNITY_STAGECODE.'</td>
                    <td class="ROW3">'.$dataRow->OPPORTUNITY_STAGENAME.'</td>
                    <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->ID.'" data-desc="'.$dataRow->OPPORTUNITY_STAGECODE.'-'.$dataRow->OPPORTUNITY_STAGENAME.'" data-ccpert="'.$dataRow->COMPLETE_PERCENT.'" data-ccname="'.$dataRow->OPPORTUNITY_STAGENAME.'" value="'.$dataRow->ID.'"/></td>
                </tr>
                ';
                }
            }
            else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
        exit();
    }



                   
/*************************************   Additonal Member Visit Code    ****************************************************** */

        public function getAddMemberVisitCode(Request $request){

            $ObjData = DB::table('TBL_MST_EMPLOYEE')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
            ->where('STATUS','=','A')
            ->get();

            if(isset($ObjData) && !empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){

                echo'
                <tr>
                    <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->EMPID .'" class="clsaddmeb" data-desc1="'.$dataRow->FNAME .'" value="'.$dataRow->EMPID.'" multiple="multiple"></td>
                    <td class="ROW2">'.$dataRow->EMPCODE.'</td>
                    <td class="ROW3">'.$dataRow->FNAME.'</td>
                    <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->EMPID.'" data-desc="'.$dataRow->EMPCODE .'" data-ccname="'.$dataRow->FNAME.'" data-desckey="'.$index.'" value="'.$dataRow->EMPID.'"/></td>
                </tr>
                ';
                }
            }
            else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
        exit();
        }

                          
/*************************************   Customer Code    ****************************************************** */

        public function getCustomerCode(Request $request){

            $type   =   $request['type'];

            if($type ==="Customer"){
                $ObjData    =   DB::table('TBL_MST_CUSTOMER')
                                ->leftJoin('TBL_MST_COUNTRY', 'TBL_MST_CUSTOMER.REGCTRYID_REF','=','TBL_MST_COUNTRY.CTRYID')
                                ->leftJoin('TBL_MST_STATE', 'TBL_MST_CUSTOMER.REGSTID_REF','=','TBL_MST_STATE.STID')
                                ->leftJoin('TBL_MST_CITY', 'TBL_MST_CUSTOMER.REGCITYID_REF','=','TBL_MST_CITY.CITYID')
                                ->where('TBL_MST_CUSTOMER.CYID_REF','=',Auth::user()->CYID_REF)
                                ->whereRaw('(TBL_MST_CUSTOMER.DEACTIVATED IS NULL  OR TBL_MST_CUSTOMER.DEACTIVATED = 0)')
                                ->where('TBL_MST_CUSTOMER.STATUS','=','A')
                                ->select('TBL_MST_CUSTOMER.SLID_REF','TBL_MST_CUSTOMER.CCODE','TBL_MST_CUSTOMER.NAME','TBL_MST_CUSTOMER.REGADDL1','TBL_MST_CUSTOMER.REGADDL2','TBL_MST_CUSTOMER.REGPIN','TBL_MST_COUNTRY.NAME AS CNTRY_NAME','TBL_MST_STATE.NAME AS STATE_NAME','TBL_MST_CITY.NAME AS CITY_NAME','TBL_MST_COUNTRY.CTRYID','TBL_MST_STATE.STID','TBL_MST_CITY.CITYID')
                                ->get();
            }
            else{
                $ObjData    =   DB::table('TBL_MST_PROSPECT')
                                ->leftJoin('TBL_MST_COUNTRY', 'TBL_MST_PROSPECT.REGCTRYID_REF','=','TBL_MST_COUNTRY.CTRYID')
                                ->leftJoin('TBL_MST_STATE', 'TBL_MST_PROSPECT.REGSTID_REF','=','TBL_MST_STATE.STID')
                                ->leftJoin('TBL_MST_CITY', 'TBL_MST_PROSPECT.REGCITYID_REF','=','TBL_MST_CITY.CITYID')
                                ->where('TBL_MST_PROSPECT.CYID_REF','=',Auth::user()->CYID_REF)
                                ->whereRaw('(TBL_MST_PROSPECT.DEACTIVATED IS NULL  OR TBL_MST_PROSPECT.DEACTIVATED = 0)')
                                ->where('TBL_MST_PROSPECT.STATUS','=','A')
                                ->select('TBL_MST_PROSPECT.PID AS SLID_REF','TBL_MST_PROSPECT.PCODE AS CCODE','TBL_MST_PROSPECT.NAME','TBL_MST_PROSPECT.REGADDL1','TBL_MST_PROSPECT.REGADDL2','TBL_MST_PROSPECT.REGPIN','TBL_MST_COUNTRY.NAME AS CNTRY_NAME','TBL_MST_STATE.NAME AS STATE_NAME','TBL_MST_CITY.NAME AS CITY_NAME','TBL_MST_COUNTRY.CTRYID','TBL_MST_STATE.STID','TBL_MST_CITY.CITYID')
                                ->get();
            }

            //DD($ObjData);

            if(isset($ObjData) && !empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){

                echo'
                <tr>
                    <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->SLID_REF .'" class="cls'.$type.'" value="'.$dataRow->SLID_REF.'" ></td>
                    <td class="ROW2">'.$dataRow->CCODE.'</td>
                    <td class="ROW3">'.$dataRow->NAME.'</td>
                    <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->SLID_REF.'" 
                    data-desc="'.$dataRow->CCODE.'-'.$dataRow->NAME.'" 
                    data-ccname="'.$dataRow->NAME.'"
                    data-cregadd1="'.$dataRow->REGADDL1.'"
                    data-cregadd2="'.$dataRow->REGADDL2.'"
                    data-cregpin="'.$dataRow->REGPIN.'"
                    data-ccntry="'.$dataRow->CNTRY_NAME.'"                    
                    data-ccstate="'.$dataRow->STATE_NAME.'"
                    data-ccity="'.$dataRow->CITY_NAME.'"
                    
                    data-ccntryid="'.$dataRow->CTRYID.'"
                    data-ccstateid="'.$dataRow->STID.'"
                    data-cccityid="'.$dataRow->CITYID.'"
                    value="'.$dataRow->SLID_REF.'"/></td>
                </tr>
                ';
                }
            }
            else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
        exit();
        }

                                       
/*************************************   Lead Owner Code    ****************************************************** */

        public function getLeadOwnerCode(Request $request){
            $ObjData = DB::table('TBL_MST_EMPLOYEE')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
            ->where('STATUS','=','A')
            ->get();

            if(isset($ObjData) && !empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){

                echo'
                <tr>
                    <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->EMPID .'" class="clsemp" value="'.$dataRow->EMPID.'" ></td>
                    <td class="ROW2">'.$dataRow->EMPCODE.'</td>
                    <td class="ROW3">'.$dataRow->FNAME.'</td>
                    <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->EMPID.'" data-desc="'.$dataRow->EMPCODE.'-'.$dataRow->FNAME.'" data-ccname="'.$dataRow->FNAME.'" value="'.$dataRow->EMPID.'"/></td>
                </tr>
                ';
                }
            }
            else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
        exit();
        }



                                               
/*************************************   LeTransfer Leads Code    ****************************************************** */

        public function getTransferLeadsCode(Request $request){
            $ObjData = DB::table('TBL_MST_EMPLOYEE')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
            ->where('STATUS','=','A')
            ->get();

            if(isset($ObjData) && !empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){

                echo'
                <tr>
                    <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->EMPID .'" class="clsassigto" value="'.$dataRow->EMPID.'" ></td>
                    <td class="ROW2">'.$dataRow->EMPCODE.'</td>
                    <td class="ROW3">'.$dataRow->FNAME.'</td>
                    <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->EMPID.'" data-desc="'.$dataRow->EMPCODE.'-'.$dataRow->FNAME.'" data-ccname="'.$dataRow->FNAME.'" value="'.$dataRow->EMPID.'"/></td>
                </tr>
                ';
                }
            }
            else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
        exit();
        }


                                              
/*************************************   Industry Type Code    ****************************************************** */

        public function getIndustryTypeCode(Request $request){
            $ObjData = DB::table('TBL_MST_INDUSTRYTYPE')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
            ->where('STATUS','=','A')
            ->get();

            if(isset($ObjData) && !empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){

                echo'
                <tr>
                    <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->INDSID .'" class="clsindtype" value="'.$dataRow->INDSID.'" ></td>
                    <td class="ROW2">'.$dataRow->INDSCODE.'</td>
                    <td class="ROW3">'.$dataRow->DESCRIPTIONS.'</td>
                    <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->INDSID.'" data-desc="'.$dataRow->INDSCODE.'-'.$dataRow->DESCRIPTIONS.'" data-ccname="'.$dataRow->DESCRIPTIONS.'" value="'.$dataRow->INDSID.'"/></td>
                </tr>
                ';
                }
            }
            else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
        exit();
        }

                                                      
/*************************************   Lead Source Code    ****************************************************** */

        public function getLeadSourceCode(Request $request){
            $ObjData = DB::table('TBL_MST_LEAD_SOURCE')
            ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
            ->where('STATUS','=','A')
            ->get();

            if(isset($ObjData) && !empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){

                echo'
                <tr>
                    <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->ID .'" class="clsldsce" value="'.$dataRow->ID.'" ></td>
                    <td class="ROW2">'.$dataRow->LEAD_SOURCECODE.'</td>
                    <td class="ROW3">'.$dataRow->LEAD_SOURCENAME.'</td>
                    <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->ID.'" data-desc="'.$dataRow->LEAD_SOURCECODE.'-'.$dataRow->LEAD_SOURCENAME.'" data-ccname="'.$dataRow->LEAD_SOURCENAME.'" value="'.$dataRow->ID.'"/></td>
                </tr>
                ';
                }
            }
            else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
        exit();
        }



                                                            
/*************************************   Dealer Code    ****************************************************** */

            public function getDealerCode(Request $request){

                $Status = "A";
                $CYID_REF = Auth::user()->CYID_REF;
                $BRID_REF = Session::get('BRID_REF');
                $FYID_REF = Session::get('FYID_REF');

                $ObjData = DB::table('TBL_MST_CUSTOMER')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('BRID_REF','=',Session::get('BRID_REF'))
                            ->where('STATUS','=',$Status)     
                            ->where('TYPE','=','DEALER')     
                            ->select('CID AS DOCID','CCODE AS DOCNO','NAME AS DEALERNAME')
                            ->get();

                if(isset($ObjData) && !empty($ObjData)){
                    foreach ($ObjData as $index=>$dataRow){

                    echo'
                    <tr>
                        <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->DOCID .'" class="clsldlr" value="'.$dataRow->DOCID.'" ></td>
                        <td class="ROW2">'.$dataRow->DOCNO.'</td>
                        <td class="ROW3">'.$dataRow->DEALERNAME.'</td>
                        <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->DOCID.'" data-desc="'.$dataRow->DOCNO.'-'.$dataRow->DEALERNAME.'" data-ccname="'.$dataRow->DEALERNAME.'" value="'.$dataRow->DOCID.'"/></td>
                    </tr>
                    ';
                    }
                }
                else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
            exit();
            }

                                                            
/*************************************   Lead Status Code    ****************************************************** */

        public function getLeadStatusCode(Request $request){
            $ObjData = DB::table('TBL_MST_LEAD_STATUS')
            //->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
            //->where('STATUS','=','A')
            ->get();

            if(isset($ObjData) && !empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){

                echo'
                <tr>
                    <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->ID .'" class="clsldst" value="'.$dataRow->ID.'" ></td>
                    <td class="ROW2">'.$dataRow->LEAD_STATUSCODE.'</td>
                    <td class="ROW3">'.$dataRow->LEAD_STATUSNAME.'</td>
                    <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->ID.'" data-desc="'.$dataRow->LEAD_STATUSCODE.'-'.$dataRow->LEAD_STATUSNAME.'" data-ccname="'.$dataRow->LEAD_STATUSNAME.'" value="'.$dataRow->ID.'"/></td>
                </tr>
                ';
                }
            }
            else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
        exit();
        }


                                                                  
/*************************************   Country Code    ****************************************************** */

            public function getCountryCode(Request $request){

                $CYID_REF           =   Auth::user()->CYID_REF;
                $TBL_MST_COUNTRY    =   DB::select("SELECT CTRYID,CTRYCODE,NAME
                                        FROM TBL_MST_COUNTRY 
                                        WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) 
                                        ");

                if(isset($TBL_MST_COUNTRY) && count($TBL_MST_COUNTRY) > 0){
                    foreach ($TBL_MST_COUNTRY as $index=>$dataRow){

                    echo'
                    <tr>
                        <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->CTRYID .'" class="clscontry" value="'.$dataRow->CTRYID.'" ></td>
                        <td class="ROW2">'.$dataRow->CTRYCODE.'</td>
                        <td class="ROW3">'.$dataRow->NAME.'</td>
                        <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->CTRYID.'" data-desc="'.$dataRow->CTRYCODE .'" data-ccname="'.$dataRow->NAME.'" value="'.$dataRow->CTRYID.'"/></td>
                    </tr>
                    ';
                    }
                }
                else{
                    echo '<tr><td colspan="3">Record not found.</td></tr>';
                }
                exit();
            }



  /*************************************   State Code    ****************************************************** */
        public function getCountryWiseState(Request $request){

            $objStateList = DB::table('TBL_MST_STATE')
            ->where('STATUS','=','A')
            ->where('CTRYID_REF','=',$request['CTRYID_REF'])
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('STID','NAME','STCODE')
            ->get();
        
            if(isset($objStateList) && count($objStateList) > 0){
                foreach($objStateList as $state){
                
                    echo '<tr>
                    <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_STID_REF[]" id="stidref_'.$state->STID.'" class="cls_stidref" value="'.$state->STID.'" ></td>
                    <td width="39%" class="ROW2">'.$state->STCODE.'
                    <input type="hidden" id="txtstidref_'.$state->STID.'" data-desc="'.$state->STCODE.'-'.$state->NAME.'" data-descname="'.$state->NAME.'" value="'.$state->STID.'" />
                    </td>
                    <td width="39%" class="ROW3">'.$state->NAME.'</td>
                    </tr>';
                }
            }
            else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
        }



 /*************************************   City Code    ****************************************************** */
        public function getStateWiseCity(Request $request){
        
            $objCityList = DB::table('TBL_MST_CITY')
            ->where('STATUS','=','A')
            ->where('CTRYID_REF','=',$request['CTRYID_REF'])
            ->where('STID_REF','=',$request['STID_REF'])
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('CITYID','CITYCODE','NAME')
            ->get();

            if(isset($objCityList) && count($objCityList) > 0){
                foreach($objCityList as $city){
                
                    echo '<tr >
                    <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_CITYID_REF[]" id="cityidref_'.$city->CITYID.'" class="cls_cityidref" value="'.$city->CITYID.'" ></td>
                    <td width="39%" class="ROW2">'.$city->CITYCODE.'
                    <input type="hidden" id="txtcityidref_'.$city->CITYID.'"  data-desc="'.$city->CITYCODE.'-'.$city->NAME.'" data-descname="'.$city->NAME.'"  value="'.$city->CITYID.'" />
                    </td>
                    <td width="39%" class="ROW3">'.$city->NAME.'</td>
                    </tr>';
                }
            }
            else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
    
            exit();
        }



                 
/*************************************   Product Code Code    ****************************************************** */

            public function getItemDetails2(Request $request){

                $CYID_REF   =   Auth::user()->CYID_REF;
                $BRID_REF   =   Session::get('BRID_REF');
                $FYID_REF   =   Session::get('FYID_REF');

                $CODE       =   $request['CODE'];
                $NAME       =   $request['NAME'];
                $MUOM       =   $request['MUOM'];
                $GROUP      =   $request['GROUP'];
                $CTGRY      =   $request['CTGRY'];
                $BUNIT      =   $request['BUNIT'];
                $APART      =   $request['APART'];
                $CPART      =   $request['CPART'];
                $OPART      =   $request['OPART'];

                $sp_popup = [
                    $CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART
                ]; 
                
                $ObjItem = DB::select('EXEC sp_get_items_popup_enquiry ?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);
                
                $row        =   '';

                if(!empty($ObjItem)){
                    foreach ($ObjItem as $index=>$dataRow){
                    
                            $ITEMID             =   isset($dataRow->ITEMID)?$dataRow->ITEMID:NULL;
                            $ICODE              =   isset($dataRow->ICODE)?$dataRow->ICODE:NULL;
                            $NAME               =   isset($dataRow->NAME)?$dataRow->NAME:NULL;
                            $ITEM_SPECI         =   isset($dataRow->ITEM_SPECI)?$dataRow->ITEM_SPECI:NULL;
                            $MAIN_UOMID_REF     =   isset($dataRow->MAIN_UOMID_REF)?$dataRow->MAIN_UOMID_REF:NULL;
                            $Main_UOM           =   isset($dataRow->Main_UOM)?$dataRow->Main_UOM:NULL;
                            $ALT_UOMID_REF      =   isset($dataRow->ALT_UOMID_REF)?$dataRow->ALT_UOMID_REF:NULL;
                            $Alt_UOM            =   isset($dataRow->Alt_UOM)?$dataRow->Alt_UOM:NULL;
                            $FROMQTY            =   isset($dataRow->FROMQTY)?$dataRow->FROMQTY:NULL;
                            $TOQTY              =   isset($dataRow->TOQTY)?$dataRow->TOQTY:NULL;
                            $STDCOST            =   isset($dataRow->STDCOST)?$dataRow->STDCOST:NULL;
                            $GroupName          =   isset($dataRow->GroupName)?$dataRow->GroupName:NULL;
                            $Categoryname       =   isset($dataRow->Categoryname)?$dataRow->Categoryname:NULL;
                            $BusinessUnit       =   isset($dataRow->BusinessUnit)?$dataRow->BusinessUnit:NULL;
                            $ALPS_PART_NO       =   isset($dataRow->ALPS_PART_NO)?$dataRow->ALPS_PART_NO:NULL;
                            $CUSTOMER_PART_NO   =   isset($dataRow->CUSTOMER_PART_NO)?$dataRow->CUSTOMER_PART_NO:NULL;
                            $OEM_PART_NO        =   isset($dataRow->OEM_PART_NO)?$dataRow->OEM_PART_NO:NULL;

                            $MATERIAL_TYPE      =   isset($dataRow->MATERIAL_TYPE)?$dataRow->MATERIAL_TYPE:NULL;

                            
                            $SLID_REF           =   NULL;
                            $SOID_REF           =   NULL;
                            $SQA                =   NULL;
                            $SEQID_REF          =   NULL;
                            $item_unique_row_id =   $SLID_REF."_".$SOID_REF."_".$SQA."_".$SEQID_REF."_".$ITEMID;
            
                            $row.=' <tr id="item_'.$index.'"  class="clsitemid">
                            <td style="width:8%;text-align:center;"><input type="checkbox" id="item_'.$dataRow->ITEMID .'" class="js-selectall1" value="'.$dataRow->ITEMID.'" ></td>
                            <td style="width:10%;">'.$ICODE.'&nbsp;&nbsp;'.$MATERIAL_TYPE.'</td>
                            <td style="width:10%;">'.$NAME.'</td>
                            <td style="width:8%;">'.$Main_UOM.'</td>
                            <td style="width:8%;">'.$FROMQTY.'</td>
                            <td style="width:8%;">'.$GroupName.'</td>
                            <td style="width:8%;">'.$Categoryname.'</td>
                            <td style="width:8%;">'.$BusinessUnit.'</td>
                            <td style="width:8%;">'.$ALPS_PART_NO.'</td>
                            <td style="width:8%;">'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:8%;">'.$OEM_PART_NO.'</td>
                            <td style="width:8%;">Authorized</td>
                            <td hidden>
                                    <input type="text" id="txtitem_'.$dataRow->ITEMID .'" 
                                        data-desc1="'.$ITEMID.'" 
                                        data-desc2="'.$ICODE.'" 
                                        data-desc3="'.$NAME.'" 
                                        data-desc4="'.$MAIN_UOMID_REF.'" 
                                        data-desc5="'.$Main_UOM.'" 
                                        data-desc6="'.$FROMQTY.'" 
                                        data-desc7="'.$item_unique_row_id.'" 
                                        data-desc8="'.$SQA.'"
                                        data-desc9="'.$SEQID_REF.'"
                                        data-desc10="'.$SLID_REF.'"
                                        data-desc11="'.$SOID_REF.'"
                                        data-desc12=""
                                        data-desc13=""
                                        data-desc14=""
                                        data-desc15=""
                                    />
                                </td>
                                <td hidden><input type="hidde" id="addinfoitem_'.$index.'"  data-desc101="'.$ALPS_PART_NO.'" data-desc102="'.$CUSTOMER_PART_NO.'" data-desc103="'.$OEM_PART_NO.'" ></td>
                            </tr>';
                } 
    
                echo $row;
                                   
            }           
            else{
                echo '<tr><td colspan="12"> Record not found.</td></tr>';
            }
            exit();

                }

                
/*************************************   Alert To Code    ****************************************************** */

            public function getAlertTo(Request $request){
                        
                $ObjData = DB::table('TBL_MST_EMPLOYEE')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                ->where('STATUS','=','A')
                ->get();

                if(isset($ObjData) && !empty($ObjData)){
                    foreach ($ObjData as $index=>$dataRow){

                        echo'
                            <tr>
                                <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->EMPID .'" class="clsalertto" data-desc1="'.$dataRow->FNAME .'" value="'.$dataRow->EMPID.'" multiple="multiple"></td>
                                <td class="ROW2">'.$dataRow->EMPCODE.'</td>
                                <td class="ROW3">'.$dataRow->FNAME.'</td>
                                <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->EMPID.'" data-desc="'.$dataRow->EMPCODE .'" data-ccname="'.$dataRow->FNAME.'" data-desckey="'.$index.'" value="'.$dataRow->EMPID.'"/></td>
                            </tr>
                            ';
                    }
                }
                else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
            exit();
            }

                           
/*************************************   Alert To Code    ****************************************************** */

            public function getAssignedTo(Request $request){
                                    
                $ObjData = DB::table('TBL_MST_EMPLOYEE')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                ->where('STATUS','=','A')
                ->get();

                if(isset($ObjData) && !empty($ObjData)){
                    foreach ($ObjData as $index=>$dataRow){

                    echo'
                    <tr>
                        <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->EMPID .'" class="clsassignto" value="'.$dataRow->EMPID.'" ></td>
                        <td class="ROW2">'.$dataRow->EMPCODE.'</td>
                        <td class="ROW3">'.$dataRow->FNAME.'</td>
                        <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->EMPID.'" data-desc="'.$dataRow->EMPCODE.'-'.$dataRow->FNAME.'" data-ccname="'.$dataRow->FNAME.'" value="'.$dataRow->EMPID.'"/></td>
                    </tr>
                    ';
                    }
                }
                else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
            exit();
            }



                                       
/*************************************  Assigned To Code    ****************************************************** */

            public function getAssignedToHrd(Request $request){
                                                
                $ObjData = DB::table('TBL_MST_EMPLOYEE')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')
                ->where('STATUS','=','A')
                ->get();

                if(isset($ObjData) && !empty($ObjData)){
                    foreach ($ObjData as $index=>$dataRow){

                    echo'
                    <tr>
                        <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->EMPID .'" class="clsassigntohrd" value="'.$dataRow->EMPID.'" ></td>
                        <td class="ROW2">'.$dataRow->EMPCODE.'</td>
                        <td class="ROW3">'.$dataRow->FNAME.'</td>
                        <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->EMPID.'" data-desc="'.$dataRow->EMPCODE.'-'.$dataRow->FNAME.'" data-ccname="'.$dataRow->FNAME.'" value="'.$dataRow->EMPID.'"/></td>
                    </tr>
                    ';
                    }
                }
                else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
            exit();
            }






}