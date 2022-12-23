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

class TrnFrm404Controller extends Controller
{
   
    protected $form_id = 404;
    protected $vtid_ref   = 230;  //voucher type id
    protected $view     = "transactions.Payroll.LeaveApproval.trnfrm";

       
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

        $objRights = DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first(); 

        $FormId         =   $this->form_id;
        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');     

        $objDataList = DB::table('TBL_MST_LEAVE_APPROVAL')
             ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_MST_LEAVE_APPROVAL.EMPID_REF','=','TBL_MST_EMPLOYEE.EMPID')   
            ->select('TBL_MST_LEAVE_APPROVAL.*','TBL_MST_EMPLOYEE.EMPCODE')
            ->orderBy('TBL_MST_LEAVE_APPROVAL.APPROVALID', 'DESC')
            ->get();

        return view($this->view.$FormId,compact(['objRights','objDataList','FormId']));

    }

    public function add(){ 

        $FormId         =   $this->form_id;
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');       

        $objEmpList    =   DB::table('TBL_MST_EMPLOYEE')
        ->select('EMPCODE','FNAME','EMPID')
        ->get();
        $objLvtypList    =   DB::table('TBL_MST_LEAVE_TYPE')->get();

        $objAptnNoList    =   DB::table('TBL_MST_LEAVE_APPLY')->get();

        

        $objDD = DB::table('TBL_MST_DOCNO_DEFINITION')
                ->where('VTID_REF','=',$this->vtid_ref)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->where('FYID_REF','=',$FYID_REF)
                ->where('STATUS','=','A')
                ->select('TBL_MST_DOCNO_DEFINITION.*')
                ->first();

        //dump($objDD);
        $objDOCNO ='';
        if(!empty($objDD)){
            if($objDD->SYSTEM_GRSR == "1")
            {
                if($objDD->PREFIX_RQ == "1")
                {
                    $objDOCNO = $objDD->PREFIX;
                }        
                if($objDD->PRE_SEP_RQ == "1")
                {
                    if($objDD->PRE_SEP_SLASH == "1")
                    {
                    $objDOCNO = $objDOCNO.'/';
                    }
                    if($objDD->PRE_SEP_HYPEN == "1")
                    {
                    $objDOCNO = $objDOCNO.'-';
                    }
                }        
                if($objDD->NO_MAX)
                {   
                    $objDOCNO = $objDOCNO.str_pad($objDD->LAST_RECORDNO+1, $objDD->NO_MAX, "0", STR_PAD_LEFT);
                }
                
                if($objDD->NO_SEP_RQ == "1")
                {
                    if($objDD->NO_SEP_SLASH == "1")
                    {
                    $objDOCNO = $objDOCNO.'/';
                    }
                    if($objDD->NO_SEP_HYPEN == "1")
                    {
                    $objDOCNO = $objDOCNO.'-';
                    }
                }
                if($objDD->SUFFIX_RQ == "1")
                {
                    $objDOCNO = $objDOCNO.$objDD->SUFFIX;
                }
            }
        }   

        return view($this->view.$FormId.'add',compact(['FormId','objDD','objAptnNoList','objEmpList','objLvtypList','objDOCNO'])); 
    }
  


    public function getItemDetails_prod_code(Request $request){   

        $taxstate = $request['taxstate'];
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $StdCost = 0;
        $Taxid = [];

        $ObjItem    =   DB::table('TBL_MST_LEAVE_APPLY')
        ->leftJoin('TBL_MST_PAY_PERIOD', 'TBL_MST_LEAVE_APPLY.PAYPERIODID_REF','=','TBL_MST_PAY_PERIOD.PAYPERIODID')
        ->orderBy('LEAVE_APPID', 'DESC')
        ->get();

        //dd($ObjItem);
            
                if(!empty($ObjItem)){

                    foreach ($ObjItem as $index=>$dataRow){

                            $LEAVE_APPID             =   isset($dataRow->LEAVE_APPID)?$dataRow->LEAVE_APPID:NULL;
                            $LEAVE_APP_NO              =   isset($dataRow->LEAVE_APP_NO)?$dataRow->LEAVE_APP_NO:NULL;
                            $LEAVE_APP_DT               =   isset($dataRow->LEAVE_APP_DT)?$dataRow->LEAVE_APP_DT:NULL;
                            $PAYPERIODID_REF               =   isset($dataRow->PAYPERIODID_REF)?$dataRow->PAYPERIODID_REF:NULL;
                            $LEAVE_APP_FRDT               =   isset($dataRow->LEAVE_APP_FRDT)?$dataRow->LEAVE_APP_FRDT:NULL;
                            $LEAVE_APP_TODT               =   isset($dataRow->LEAVE_APP_TODT)?$dataRow->LEAVE_APP_TODT:NULL;
                            $REASON_LEAVE               =   isset($dataRow->REASON_LEAVE)?$dataRow->REASON_LEAVE:NULL;
                            $ADDRESS_LEAVE               =   isset($dataRow->ADDRESS_LEAVE)?$dataRow->ADDRESS_LEAVE:NULL;
                            $MONO_INLEAVE1               =   isset($dataRow->MONO_INLEAVE1)?$dataRow->MONO_INLEAVE1:NULL;
                            $PAY_PERIOD_CODE               =   isset($dataRow->PAY_PERIOD_CODE)?$dataRow->PAY_PERIOD_CODE:NULL;
                            $PAY_PERIOD_DESC               =   isset($dataRow->PAY_PERIOD_DESC)?$dataRow->PAY_PERIOD_DESC:NULL;
                            
                            
                            $row = '';
                            $row .='<tr id="glidcode_'.$LEAVE_APPID.'" class="clsglid" >
                                    <td style="width:5%;text-align:center;" ><input type="checkbox" id="chkIdProdCode'.$LEAVE_APPID.'"  value="'.$LEAVE_APPID.'" class="js-selectall1ProdCode"  > </td>
                                    <td style="width:10%;">'.$LEAVE_APP_NO.'
                                    <input type="hidden" id="txtglidcode_'.$LEAVE_APPID.'" data-code="'.$LEAVE_APP_NO.'" data-lappdt="'.$LEAVE_APP_DT.'" data-paycode="'.$PAY_PERIOD_CODE.'" data-payid="'.$PAY_PERIOD_DESC.'" data-lappfdt="'.$LEAVE_APP_FRDT.'" data-lapptdt="'.$LEAVE_APP_TODT.'" data-relv="'.$REASON_LEAVE.'" data-addlv="'.$ADDRESS_LEAVE.'" data-monoinlv1="'.$MONO_INLEAVE1.'" value="'.$LEAVE_APPID.'"/>
                                    </td>
                                    <td style="width:10%;">'.$LEAVE_APP_DT.'</td>
                                </tr>';
                        
                            echo $row;    
                        
                    } 
                    
                    // return Response::json($ObjItem);
                }           
                else{
                    echo '<tr><td colspan="12"> Record not found.</td></tr>';
                }
        exit();
    }



    public function getALeaveNoMaterial(Request $request){
        $Status = "A";
        $id = $request['id'];

        $row1 = '';
    
        // $ObjData =  DB::select('SELECT * FROM TBL_MST_LEAVE_APPLY  
        //             WHERE LEAVE_APPID = ? order by LEAVE_APPID ASC', [$id]);

        $ObjData    =   DB::table('TBL_MST_LEAVE_APPLY')
        ->leftJoin('TBL_MST_PAY_PERIOD', 'TBL_MST_LEAVE_APPLY.PAYPERIODID_REF','=','TBL_MST_PAY_PERIOD.PAYPERIODID')
        ->leftJoin('TBL_MST_LEAVE_OPENING_DETAIL', 'TBL_MST_LEAVE_APPLY.EMPID_REF','=','TBL_MST_LEAVE_OPENING_DETAIL.EMPID_REF')
        ->leftJoin('TBL_MST_LEAVE_TYPE', 'TBL_MST_LEAVE_OPENING_DETAIL.LTID_REF','=','TBL_MST_LEAVE_TYPE.LTID')
        ->where('LEAVE_APPID','=', $id )
        ->get();

    
            if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){

                $LEAVE_OPBL = $dataRow->LEAVE_OPBL;
                $LEAVE_APPFRDT = $dataRow->LEAVE_APP_FRDT;
                $LEAVE_APTODT = $dataRow->LEAVE_APP_TODT; 
                $LEAVE_APP_FRDT = isset($dataRow->LEAVE_APP_FRDT) && $dataRow->LEAVE_APP_FRDT !=""?strtotime($LEAVE_APPFRDT):'';
                $LEAVE_APP_TODT = isset($dataRow->LEAVE_APP_TODT) && $dataRow->LEAVE_APP_TODT !=""?strtotime($LEAVE_APTODT):'';
                $datediff = $LEAVE_APP_TODT - $LEAVE_APP_FRDT;
                $numberDays= (round($datediff / (60 * 60 * 24))+1);
                $totleave = $LEAVE_OPBL - $numberDays;
               
                $row = '';

                $row = $row.' <tr class="participantRow"> <td><input type="text" name="LEAVETYPE_'.$index.'" id="LEAVETYPE_'.$index.'" class="form-control"  autocomplete="off" value="'.$dataRow->LEAVETYPE_DESC.'"  readonly>
                <input type="hidden" name="LEAVETYPE_'.$index.'" id="LEAVETYPE_'.$index.'" class="form-control"  autocomplete="off" value="'.$dataRow->LTID.'"  readonly>
                
                </td>';
                
                $row = $row.' <td hidden><input type="hidden" name="LTID_REF_'.$index.'" id="LTID_REF_'.$index.'" class="form-control"  autocomplete="off" value="'.$dataRow->LTID.'" /></td>';
                $row = $row.' <td><input type="date" name="LEAVE_APP_FRDT_'.$index.'" id="LEAVE_APP_FRDT_'.$index.'" class="form-control"  autocomplete="off" value="'.$dataRow->LEAVE_APP_FRDT.'" onchange="getTotalDays(this.id,this.value)" ></td>';
                
                $row = $row.' <td><input type="date" name="LEAVE_APP_TODT_'.$index.'" id="LEAVE_APP_TODT_'.$index.'" class="form-control"  autocomplete="off" value="'.$dataRow->LEAVE_APP_TODT.'" onchange="getTotalDays(this.id,this.value)"></td>';
                $row = $row.' <td><input type="text" name="ITEMSPECI_'.$index.'" id="ITEMSPECI_'.$index.'" class="form-control"  autocomplete="off" value="'.$numberDays.'" readonly ></td>';
                $row = $row.' <td><input type="text" name="REMARKS_'.$index.'" id="REMARKS_'.$index.'" class="form-control"  autocomplete="off" value=""> </td>';
                $row = $row.' <td><input type="text" name="TOTAL_DAYS_'.$index.'" id="TOTAL_DAYS_'.$index.'" class="form-control"  autocomplete="off" value="'.$totleave.'"  readonly> </td></tr><tr></tr>';
                
                $row1 = $row1.$row;
            }
            echo $row1;
          
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    
    }

    public function getemplCode(Request $request){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;

        $ObjData = DB::table('TBL_MST_EMPLOYEE')
        ->leftJoin('TBL_MST_LEAVE_OPENING_DETAIL', 'TBL_MST_EMPLOYEE.EMPID','=','TBL_MST_LEAVE_OPENING_DETAIL.EMPID_REF')
        ->select('TBL_MST_EMPLOYEE.*', 'TBL_MST_LEAVE_OPENING_DETAIL.LEAVE_OPBL')
        ->distinct('TBL_MST_LEAVE_OPENING_DETAIL.EMPID_REF')
        ->get();

        if(!empty($ObjData)){
        foreach ($ObjData as $index=>$dataRow){

            $row = '';
            $row = $row.'<tr >
            <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_MACHINEID_REF[]" id="subgl_'.$dataRow->EMPID .'"  class="clsemp" value="'.$dataRow->EMPID.'" ></td>
            <td width="39%" class="ROW2">'.$dataRow->EMPCODE;
            $row = $row.'<input type="hidden" id="txtsubgl_'.$dataRow->EMPID.'" data-desc="'.$dataRow->EMPCODE .'" data-ccname="'.$dataRow->FNAME.'" data-lvopbl="'.$dataRow->LEAVE_OPBL.'" value="'.$dataRow->EMPID.'"/></td>';
            $row = $row.'<td width="39%" class="ROW3">'.$dataRow->FNAME.'</td>';
            $row = $row.'</tr>';
            echo $row;
        }
        }else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
        }
        exit();

    }


    public function gelvtypeCode(Request $request){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;

        $ObjData = DB::table('TBL_MST_LEAVE_TYPE')->get();

        if(!empty($ObjData)){
        foreach ($ObjData as $index=>$dataRow){

            $row = '';
            $row = $row.'<tr >
            <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_LAVETYPEID_REF[]" id="subglv_'.$dataRow->LTID .'"  class="clslvtype" value="'.$dataRow->LTID.'" ></td>
            <td width="39%" class="ROW2">'.$dataRow->LEAVETYPE_CODE;
            $row = $row.'<input type="hidden" id="txtsubglv_'.$dataRow->LTID.'" data-desc="'.$dataRow->LEAVETYPE_CODE .'" data-ccname="'.$dataRow->LEAVETYPE_DESC.'" value="'.$dataRow->LTID.'"/></td>';
            $row = $row.'<td width="39%" class="ROW3"><input type="hidden" id="txtsubglv_'.$dataRow->LTID.'" value="'.$dataRow->LTID.'"/>'.$dataRow->LEAVETYPE_DESC.'</td>';
            $row = $row.'</tr>';
            echo $row;
        }
        }else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
        }
        exit();

    }

    public function getEmpCode(Request $request){
        
        $EMPID          =   $request['EMPID'];
		
		$objEmpName = DB::table('TBL_MST_EMPLOYEE')
        ->where('EMPID','=', $EMPID )
        ->leftJoin('TBL_MST_LEAVE_OPENING_DETAIL', 'TBL_MST_EMPLOYEE.EMPID','=','TBL_MST_LEAVE_OPENING_DETAIL.EMPID_REF')
        ->select('*')
        ->first();


        dd($objEmpName);
		
		if(!empty($objEmpName)){
			echo $objEmpName->FNAME;
            echo $objEmpName->LEAVE_OPBL;
            
		}
		else{
			echo "";
		}
        exit();
    }

    public function getAplnNoName(Request $request){
        
        $LEAVE_APPID          =   $request['LEAVE_APPID'];
		$objAplnNo = DB::table('TBL_MST_LEAVE_APPLY')
        ->where('LEAVE_APPID','=', $LEAVE_APPID )
        ->select('TBL_MST_LEAVE_APPLY.*')
        ->first();
		
		if(!empty($objAplnNo)){
			echo $objAplnNo->LEAVE_APP_DT;
            echo $objAplnNo->PAYPERIODID_REF;
            echo $objAplnNo->LEAVE_APP_FRDT;
            echo $objAplnNo->LEAVE_APP_TODT;
            echo $objAplnNo->REASON_LEAVE;
            echo $objAplnNo->ADDRESS_LEAVE;
            echo $objAplnNo->MONO_INLEAVE1;
		}
		else{
			echo "";
		}
        exit();
    }


    public function getLeaveTyName(Request $request){
        
        $LTID          =   $request['LTID'];
		
		$objLeaveTyName = DB::table('TBL_MST_LEAVE_TYPE')
        ->where('LTID','=', $LTID )
        ->select('LEAVETYPE_DESC')
        ->first();
		
		if(!empty($objLeaveTyName)){
			echo $objLeaveTyName->LEAVETYPE_DESC;
		}
		else{
			echo "";
		}
        exit();
    }


   public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $ATTCODE =   $request['ATTCODE'];
        
        $objLabel = DB::table('TBL_MST_ATTRIBUTE')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('ATTCODE','=',$ATTCODE)
        ->select('ATTCODE')
        ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }

   public function save(Request $request){

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
                //$Approvallevel = "APPROVAL".$salesenquiryitem->LAVELS;
                $Approvallevel = $salesenquiryitem->LAVELS;
            }
            }

        $r_count = $request['Row_Count'];
        for ($i=0; $i<=$r_count; $i++)
        {
            if(isset($request['LTID_REF_'.$i]))
            {
                $data[$i] = [
                    'TOYPEOFLEAVE'          => strtoupper($request['LTID_REF_'.$i]),
                    'FROMDATE'            => $request['LEAVE_APP_FRDT_'.$i],
                    'TODATE'     => $request['LEAVE_APP_TODT_'.$i],
                    'REMARKS' => $request['REMARKS_'.$i],
                    'LEAVEBALANCE'       => $request['TOTAL_DAYS_'.$i],
                ];
            }
        }

        //dd($data);       

        $wrapped_links["LEAVEAPPROVAL"] = $data; 
        $XML = ArrayToXml::convert($wrapped_links);

        $EMPID_REF      =   trim($request['EMPID_REF']);
        $LEAVE_APPID_REF      =   trim($request['LEAVE_APPIDCODE']);

        $APPROVAL1_BY      =   trim($request['APPROVAL1_BY']);
        $APPROVAL1_DT      =   trim($request['APPROVAL1_DT']);
        $APPROVAL1_ST      =   trim($request['APPROVAL1_ST']);
        $APPROVAL1_PARTIALDAYS      =   trim($request['APPROVAL1_PARTIALDAYS']);
        $APPROVAL1_REASON      =   trim($request['APPROVAL1_REASON']);
        $APPROVAL_LEVEL     = $Approvallevel;
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       = Session::get('FYID_REF');
        $VTID           =   $this->vtid_ref;
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();
        
        $array_data   = [
                    $LEAVE_APPID_REF,           $EMPID_REF,         $APPROVAL1_BY,      $APPROVAL1_DT,  $APPROVAL1_ST,   
                    $APPROVAL1_PARTIALDAYS,     $APPROVAL1_REASON,  $APPROVAL_LEVEL,    $CYID_REF,      $BRID_REF, 
                    $FYID_REF,                  $XML,               $VTID,              $USERID,        $UPDATE,    
                    $UPTIME,                    $ACTION,            $IPADDRESS          
                    ];

            //dd($array_data);

        $sp_result = DB::select('EXEC SP_LEAVE_APPROVAL_IN ?,?,?,?,?,   ?,?,?,?,?,  ?,?,?,?,?,  ?,?,?', $array_data);

        return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);
        
        exit();    
    }

    public function edit($id){

        if(!is_null($id))
        {
        
            $FormId         =   $this->form_id;
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objResponse = DB::table('TBL_MST_LEAVE_APPROVAL')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('APPROVALID','=',$id)
            ->select('*')
            ->first();
            if(strtoupper($objResponse->STATUS)=="A" || strtoupper($objResponse->STATUS)=="C"){
                exit("Sorry, Only Un Approved record can edit.");
            }
            $objLvtypList    =   DB::table('TBL_MST_LEAVE_TYPE')->get();

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first(); 

             $objDataResponse = DB::table('TBL_MST_LEAVE_APPROVAL_DETAILS')                    
            ->where('APPROVALID_REF','=',$id)
            ->leftJoin('TBL_MST_LEAVE_TYPE', 'TBL_MST_LEAVE_APPROVAL_DETAILS.LTID_REF','=','TBL_MST_LEAVE_TYPE.LTID') 
            ->get()->toArray();

            $objCount = count($objDataResponse);

            $objLvDesList = DB::table('TBL_MST_LEAVE_APPROVAL')
            ->where('TBL_MST_LEAVE_APPROVAL.APPROVALID','=',$id)
             ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_MST_LEAVE_APPROVAL.EMPID_REF','=','TBL_MST_EMPLOYEE.EMPID')   
            ->select('TBL_MST_LEAVE_APPROVAL.*','TBL_MST_EMPLOYEE.FNAME')
            ->first();

            $ObjItem = DB::table('TBL_MST_LEAVE_APPROVAL')
            ->where('TBL_MST_LEAVE_APPROVAL.APPROVALID','=',$id)
             ->leftJoin('TBL_MST_LEAVE_APPLY', 'TBL_MST_LEAVE_APPROVAL.LEAVE_APPID_REF','=','TBL_MST_LEAVE_APPLY.LEAVE_APPID')
             ->leftJoin('TBL_MST_PAY_PERIOD', 'TBL_MST_LEAVE_APPLY.PAYPERIODID_REF','=','TBL_MST_PAY_PERIOD.PAYPERIODID')   
            ->select('TBL_MST_LEAVE_APPROVAL.*','TBL_MST_LEAVE_APPLY.*','TBL_MST_PAY_PERIOD.*')
            ->first();
            
            $objEmpList = DB::table('TBL_MST_EMPLOYEE')
            ->where('STATUS','=','A')
            ->select('EMPID','EMPCODE')
            ->get();

            return view($this->view.$FormId.'edit',compact(['FormId','objResponse','ObjItem','objLvtypList','objEmpList','objLvDesList','user_approval_level','objRights','objDataResponse','objCount']));
        }

    }

     
    public function update(Request $request)
    {

      // dd($request->all());
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
                //$Approvallevel = "APPROVAL".$salesenquiryitem->LAVELS;
                $Approvallevel = $salesenquiryitem->LAVELS;
            }
            }

      $r_count = $request['Row_Count'];
        for ($i=0; $i<=$r_count; $i++)
        {
            if(isset($request['LTID_REF_'.$i]))
            {
                $data[$i] = [
                    'TOYPEOFLEAVE'          => strtoupper($request['LTID_REF_'.$i]),
                    'FROMDATE'            => $request['FROM_DT_'.$i],
                    'TODATE'     => $request['TO_DT_'.$i],
                    'REMARKS' => $request['REMARKS_'.$i],
                    'LEAVEBALANCE'       => $request['LEAVE_BLANCE_'.$i],
                ];
            }
        }

        //dd($data);       

        $wrapped_links["LEAVEAPPROVAL"] = $data; 
        $XML = ArrayToXml::convert($wrapped_links);

        $EMPID_REF      =   trim($request['EMPID_REF']);
        $LEAVE_APPID_REF      =   trim($request['LEAVE_APPID']);

        $APPROVAL1_BY      =   trim($request['APPROVAL1_BY']);
        $APPROVAL1_DT      =   trim($request['APPROVAL1_DT']);
        $APPROVAL1_ST      =   trim($request['APPROVAL1_ST']);
        $APPROVAL1_PARTIALDAYS      =   trim($request['APPROVAL1_PARTIALDAYS']);
        $APPROVAL1_REASON      =   trim($request['APPROVAL1_REASON']);
        $APPROVAL_LEVEL     = $Approvallevel;
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       = Session::get('FYID_REF');
        $VTID           =   $this->vtid_ref;
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();
        
        $array_data   = [
            $LEAVE_APPID_REF,           $EMPID_REF,         $APPROVAL1_BY,      $APPROVAL1_DT,  $APPROVAL1_ST,   
            $APPROVAL1_PARTIALDAYS,     $APPROVAL1_REASON,  $APPROVAL_LEVEL,    $CYID_REF,      $BRID_REF, 
            $FYID_REF,                  $XML,               $VTID,              $USERID,        $UPDATE,    
            $UPTIME,                    $ACTION,            $IPADDRESS          
            ];

        //dd($array_data);

            $sp_result = DB::select('EXEC SP_LEAVE_APPROVAL_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $array_data);

             return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);
        
        exit();            
    } 

    //uploads attachments files
    public function docuploads(Request $request){

        $formData = $request->all();

        $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size = config("erpconst.attachments.max_size") * 1020 * 1024;

        //echo '<br> c='."--".Config("erpconst.attachments.max_size");
        
        //get data
        $VTID           =   $formData["VTID_REF"]; 
        $ATTACH_DOCNO   =   $formData["ATTACH_DOCNO"]; 
        $ATTACH_DOCDT   =   $formData["ATTACH_DOCDT"]; 
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');       
        // @XML	xml
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();
 
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/AssignLeave";

        if ( !is_dir($destinationPath) ) {
            mkdir($destinationPath, 0777, true);
        }

        $uploaded_data = [];
        $invlid_files = "";

        $duplicate_files="";

        foreach($formData["REMARKS"] as $index=>$row_val){

                if(isset($formData["FILENAME"][$index])){

                    $uploadedFile = $formData["FILENAME"][$index]; 
                    
                    //$filenamewithextension  = $formData["FILENAME"][$index]->getClientOriginalName();

                    $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
                    $filesize               =   $uploadedFile ->getSize();  
                    $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );

                    //$filenametostore        =   $filenamewithextension; 

                    $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."#_".$filenamewithextension;  

                    if ($uploadedFile->isValid()) {

                        if(in_array($extension,$allow_extnesions)){
                            
                            if($filesize < $allow_size){

                                $filename = $destinationPath."/".$filenametostore;

                                if (!file_exists($filename)) {

                                   $uploadedFile->move($destinationPath, $filenametostore);  //upload in dir if not exists
                                   $uploaded_data[$index]["FILENAME"] =$filenametostore;
                                   $uploaded_data[$index]["LOCATION"] = $destinationPath."/";
                                   $uploaded_data[$index]["REMARKS"] = is_null($row_val) ? '' : trim($row_val);

                                }else{

                                    $duplicate_files = " ". $duplicate_files.$filenamewithextension. " ";
                                }
                                

                                
                            }else{
                                
                                $invlid_files = $invlid_files.$filenamewithextension." (invalid size)  "; 
                            } //invalid size
                            
                        }else{

                            $invlid_files = $invlid_files.$filenamewithextension." (invalid extension)  ";                             
                        }// invalid extension
                    
                    }else{
                            
                        $invlid_files = $invlid_files.$filenamewithextension." (invalid)"; 
                    }//invalid

                }

        }//foreach

      
        if(empty($uploaded_data)){
            return redirect()->route("transaction",[404,"attachment",$ATTACH_DOCNO])->with("success","No file duplicate uploaded");
        }
     //  dd($uploaded_data);

        $wrapped_links["ATTACHMENT"] = $uploaded_data;     //root node: <ATTACHMENT>
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
       
             //save data
             $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);
     
        if($sp_result[0]->RESULT=="SUCCESS"){

            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }

            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }

            return redirect()->route("transaction",[404,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("transaction",[404,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("transaction",[404,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
            return redirect()->route("transaction",[404,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
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
        
        $sp_listing_result = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?, ?', $sp_Approvallevel);

        if(!empty($sp_listing_result))
            {
                foreach ($sp_listing_result as $key=>$salesenquiryitem)
            {  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$salesenquiryitem->LAVELS;
            }
            }
           
        $r_count = $request['Row_Count'];
            for ($i=0; $i<=$r_count; $i++)
            {
                if(isset($request['LTID_REF_'.$i]))
                {
                    $data[$i] = [
                        'TOYPEOFLEAVE'          => strtoupper($request['LTID_REF_'.$i]),
                        'FROMDATE'            => $request['FROM_DT_'.$i],
                        'TODATE'     => $request['TO_DT_'.$i],
                        'REMARKS' => $request['REMARKS_'.$i],
                        'LEAVEBALANCE'       => $request['LEAVE_BLANCE_'.$i],
                    ];
                }
            }

            //dd($data);       

        $wrapped_links["LEAVEAPPROVAL"] = $data; 
        $XML = ArrayToXml::convert($wrapped_links);

        $EMPID_REF      =   trim($request['EMPID_REF']);
        $LEAVE_APPID_REF      =   trim($request['LEAVE_APPID']);

        $APPROVAL1_BY      =   trim($request['APPROVAL1_BY']);
        $APPROVAL1_DT      =   trim($request['APPROVAL1_DT']);
        $APPROVAL1_ST      =   trim($request['APPROVAL1_ST']);
        $APPROVAL1_PARTIALDAYS      =   trim($request['APPROVAL1_PARTIALDAYS']);
        $APPROVAL1_REASON      =   trim($request['APPROVAL1_REASON']);
        $APPROVAL_LEVEL     = 1;
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       = Session::get('FYID_REF');
        $VTID           =   $this->vtid_ref;
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION     = $Approvallevel;
        $IPADDRESS  =   $request->getClientIp();
            
        $array_data   = [
            $LEAVE_APPID_REF,           $EMPID_REF,         $APPROVAL1_BY,      $APPROVAL1_DT,  $APPROVAL1_ST,   
            $APPROVAL1_PARTIALDAYS,     $APPROVAL1_REASON,  $APPROVAL_LEVEL,    $CYID_REF,      $BRID_REF, 
            $FYID_REF,                  $XML,               $VTID,              $USERID,        $UPDATE,    
            $UPTIME,                    $ACTION,            $IPADDRESS          
            ];

        //dd($array_data);
    
        $sp_result = DB::select('EXEC SP_LEAVE_APPROVAL_UP ?,?,?,?,?,   ?,?,?,?,?,  ?,?,?,?,?,  ?,?,?', $array_data);

        return Response::json(['success' =>true,'msg' => 'Record successfully Approved.']);               

        exit();     
    }


    public function view($id){

        if(!is_null($id))
        {
            $FormId         =   $this->form_id;
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objResponse = DB::table('TBL_MST_LEAVE_APPROVAL')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('APPROVALID','=',$id)
            ->select('*')
            ->first();
            if(strtoupper($objResponse->STATUS)=="A" || strtoupper($objResponse->STATUS)=="C"){
                exit("Sorry, Only Un Approved record can edit.");
            }
            $objLvtypList    =   DB::table('TBL_MST_LEAVE_TYPE')->get();

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first(); 

             $objDataResponse = DB::table('TBL_MST_LEAVE_APPROVAL_DETAILS')                    
            ->where('APPROVALID_REF','=',$id)
            ->leftJoin('TBL_MST_LEAVE_TYPE', 'TBL_MST_LEAVE_APPROVAL_DETAILS.LTID_REF','=','TBL_MST_LEAVE_TYPE.LTID') 
            ->get()->toArray();

            $objCount = count($objDataResponse);

            $objLvDesList = DB::table('TBL_MST_LEAVE_APPROVAL')
            ->where('TBL_MST_LEAVE_APPROVAL.APPROVALID','=',$id)
             ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_MST_LEAVE_APPROVAL.EMPID_REF','=','TBL_MST_EMPLOYEE.EMPID')   
            ->select('TBL_MST_LEAVE_APPROVAL.*','TBL_MST_EMPLOYEE.FNAME')
            ->first();

            $ObjItem = DB::table('TBL_MST_LEAVE_APPROVAL')
            ->where('TBL_MST_LEAVE_APPROVAL.APPROVALID','=',$id)
             ->leftJoin('TBL_MST_LEAVE_APPLY', 'TBL_MST_LEAVE_APPROVAL.LEAVE_APPID_REF','=','TBL_MST_LEAVE_APPLY.LEAVE_APPID')
             ->leftJoin('TBL_MST_PAY_PERIOD', 'TBL_MST_LEAVE_APPLY.PAYPERIODID_REF','=','TBL_MST_PAY_PERIOD.PAYPERIODID')   
            ->select('TBL_MST_LEAVE_APPROVAL.*','TBL_MST_LEAVE_APPLY.*','TBL_MST_PAY_PERIOD.*')
            ->first();
            
            $objEmpList = DB::table('TBL_MST_EMPLOYEE')
            ->where('STATUS','=','A')
            ->select('EMPID','EMPCODE')
            ->get();

            return view($this->view.$FormId.'view',compact(['FormId','objResponse','ObjItem','objLvtypList','objEmpList','objLvDesList','user_approval_level','objRights','objDataResponse','objCount']));

        }

    }
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }

        $objResponse = TblMstFrm404::whereIn('ATTID',$ids_data)->get();
        
        return view('transactions.Payroll.AssignLeave.trnfrm404print',compact(['objResponse']));
   }//print

    
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $FormId         =   $this->form_id;
            $objResponse = DB::table('TBL_MST_LEAVE_APPROVAL')
            ->where('APPROVALID','=',$id)
            ->leftJoin('TBL_MST_LEAVE_APPLY', 'TBL_MST_LEAVE_APPROVAL.LEAVE_APPID_REF','=','TBL_MST_LEAVE_APPLY.LEAVE_APPID')
            ->select('TBL_MST_LEAVE_APPROVAL.*','TBL_MST_LEAVE_APPLY.*')
            ->first();

            //select * from TBL_MST_VOUCHERTYPE where VTID=114

            $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
                    ->where('VTID','=',$this->vtid_ref)
                     ->select('VTID','VCODE','DESCRIPTIONS')
                    ->get()
                    ->toArray();

            
                    //uplaoded docs
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

                 // dump( $objAttachments);
                 return view($this->view.$FormId.'attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments']));

        }

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
            $TABLE      =   "TBL_MST_ATTRIBUTE";
            $FIELD      =   "ATTID";
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
        
        return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','exist'=>'norecord']);
        
        }else{
        return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','exist'=>'Some Error']);
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
            $TABLE      =   "TBL_MST_LEAVE_APPROVAL";
            $FIELD      =   "APPROVALID";
            $ID         =   $id;
            $UPDATE     =   Date('Y-m-d');
            $UPTIME     =   Date('h:i:s.u');
            $IPADDRESS  =   $request->getClientIp();
    
            $req_data[0]=[
                'NT'  => 'TBL_MST_LEAVE_APPROVAL_DETAILS',
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

   


}
