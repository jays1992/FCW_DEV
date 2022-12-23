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

class TrnFrm36Controller extends Controller{
    protected $form_id = 36;
    protected $vtid_ref   = 36;  
   
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(){    
        $objRights = DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
		->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
		->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();

        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');     
        
        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList	=	DB::select("select hdr.SQID,HDR.SQNO,hdr.SQDT,hdr.QVFDT,hdr.QVTDT,hdr.INDATE,QUOTATTION_TYPE,
                        (
                        SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                        LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                        WHERE  AUD.VID=hdr.SQID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                        AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                        ) AS CREATED_BY,
                        hdr.STATUS,ISNULL(sl.SLNAME,P.NAME) AS SLNAME,
                        case when a.ACTIONNAME = '$FANO' then 'Final Approved' 
                        else case when a.ACTIONNAME = 'ADD' then 'Added'  
                            when a.ACTIONNAME = 'EDIT' then 'Edited'
                            when a.ACTIONNAME = 'APPROVAL1' then 'First Level Approved'
                            when a.ACTIONNAME = 'APPROVAL2' then 'Second Level Approved'
                            when a.ACTIONNAME = 'APPROVAL3' then 'Third Level Approved'
                            when a.ACTIONNAME = 'APPROVAL4' then 'Fourth Level Approved'
                            when a.ACTIONNAME = 'APPROVAL5' then 'Final Approved' 
                            when a.ACTIONNAME = 'CANCEL' then 'Cancelled'
                            when a.ACTIONNAME = 'CLOSE' then 'Closed'
                        end end as STATUS_DESC
                        from TBL_TRN_AUDITTRAIL a 
                        inner join TBL_TRN_SLQT01_HDR hdr
                        on a.VID = hdr.SQID 
                        and a.VTID_REF = hdr.VTID_REF 
                        and a.CYID_REF = hdr.CYID_REF 
                        and a.BRID_REF = hdr.BRID_REF
                        left join TBL_MST_SUBLEDGER sl ON hdr.SLID_REF = sl.SGLID AND hdr.QUOTATTION_TYPE <> 'PROSPECT'
                        left join TBL_TRN_LEAD_GENERATION L ON hdr.LEADID_REF = L.LEAD_ID  
                        left join TBL_MST_PROSPECT P ON L.CUSTOMER_PROSPECT = P.PID  AND L.CUSTOMER_TYPE='PROSPECT'
                        where a.VTID_REF = '$this->vtid_ref'
                        and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                        and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                        ORDER BY hdr.SQID DESC ");

                    //($objDataList); 

                        $REQUEST_DATA   =   array(
                            'FORMID'    =>  $this->form_id,
                            'VTID_REF'  =>  $this->vtid_ref,
                            'USERID'    =>  Auth::user()->USERID,
                            'CYID_REF'  =>  Auth::user()->CYID_REF,
                            'BRID_REF'  =>  Session::get('BRID_REF'),
                            'FYID_REF'  =>  Session::get('FYID_REF'),
                        );
        
        return view('transactions.sales.SalesQuotation.trnfrm36',compact(['REQUEST_DATA','objRights','objDataList']));        
    }

    public function ViewReport($request) {

        $box = $request;   
        $myValue=  array();
        parse_str($box, $myValue);
        
        if($myValue['Flag'] == 'H')
        {
            $SQNO       = $myValue['SQID'];
            $Flag       = $myValue['Flag'];
          
        }
        else
        {  
            $SQNO          = Session::get('SQNO');
            $Flag          = $myValue['Flag'];

        }
    
            
            
        $ssrs = new \SSRS\Report('http://103.139.58.23:8181//ReportServer/', array('username' => 'App', 'password' => 'admin@123'));  
          
          
        $result = $ssrs->loadReport('/ERP/SQPrint');
    
            //dd(Auth::user()->CYID_REF);
            $reportParameters = array(
                'SQNO'                  => $SQNO,
            );
           
            $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
            
            $ssrs->setSessionId($result->executionInfo->ExecutionID)
                ->setExecutionParameters($parameters);
    
                if($Flag == 'H')
                {
                    Session::put('SQNO', $SQNO);  
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
                    $output = $ssrs->render('HTML4.0'); // PDF | XML | CSV
                    echo $output;
                }
             
         }

    public function add(){       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        


      

        $objSQN = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('FYID_REF','=',$FYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_DOCNO_DEFINITION.*')
        ->first();

        $objSQNO="";
        if(isset($objSQN) && $objSQN->SYSTEM_GRSR == "1")
        {
            if($objSQN->PREFIX_RQ == "1")
            {
                $objSQNO = $objSQN->PREFIX;
            }        
            if($objSQN->PRE_SEP_RQ == "1")
            {
                if($objSQN->PRE_SEP_SLASH == "1")
                {
                $objSQNO = $objSQNO.'/';
                }
                if($objSQN->PRE_SEP_HYPEN == "1")
                {
                $objSQNO = $objSQNO.'-';
                }
            }        
            if($objSQN->NO_MAX)
            {   
                $objSQNO = $objSQNO.str_pad($objSQN->LAST_RECORDNO+1, $objSQN->NO_MAX, "0", STR_PAD_LEFT);
            }
            
            if($objSQN->NO_SEP_RQ == "1")
            {
                if($objSQN->NO_SEP_SLASH == "1")
                {
                $objSQNO = $objSQNO.'/';
                }
                if($objSQN->NO_SEP_HYPEN == "1")
                {
                $objSQNO = $objSQNO.'-';
                }
            }
            if($objSQN->SUFFIX_RQ == "1")
            {
                $objSQNO = $objSQNO.$objSQN->SUFFIX;
            }
        }
        

        $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by TNC_CODE ASC', [$CYID_REF, $BRID_REF, 'A' ]);

        $objCalculationHeader	=   Helper::getCalculationHeader(array(
                                    'CYID_REF'=>Auth::user()->CYID_REF,
                                    'BRID_REF'=>Session::get('BRID_REF'),
                                    'USERID'=>Auth::user()->USERID,
                                    'HEADING'=>'Transactions',
                                    'VTID_REF'=>$this->vtid_ref,
                                    'FORMID'=>$this->form_id
                                    ));
        
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_SQ")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                                {$query->select('UDFSQID')->from('TBL_MST_UDFFORSO')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF)
                                                ->where('BRID_REF','=',$BRID_REF);
                                                                
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF);
                   
        $objUdfSQData = DB::table('TBL_MST_UDFFOR_SQ')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdfSQData);

        $objCurrencyconverter = DB::table('TBL_MST_CRCONVERSION')
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_CRCONVERSION.*')
        ->get()
        ->toArray();

        $objlastSQDT = $this->LastApprovedDocDate(); 
    
  
        $objSalesPerson = DB::table('TBL_MST_EMPLOYEE')
        ->where('STATUS','=',$Status)
        ->where('SALES_PERSON','=','1')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->select('EMPID','EMPCODE','FNAME','MNAME','LNAME')
        ->get()
        ->toArray();

        $ObjSalesEnquiryData = DB::table("TBL_TRN_SLEQ01_HDR")->select('*')
                                ->where('STATUS','=','A')                    
                                ->where('CYID_REF','=',$CYID_REF)
                                ->where('BRID_REF','=',$BRID_REF)
                                ->get() 
                                ->toArray(); 

        $FormId = $this->form_id;
        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        $objothcurrency = $this->GetCurrencyMaster(); 
       
    return view('transactions.sales.SalesQuotation.trnfrm36add',
    compact(['AlpsStatus','objCalculationHeader','objUdfSQData','objothcurrency','objTNCHeader','objSQN',
    'objSalesPerson','ObjSalesEnquiryData','objCountUDF','objSQNO','objlastSQDT','FormId','TabSetting']));       
   }

   public function gettncdetails(Request $request){
    $Status = "A";
    $id = $request['id'];

    $ObjData =  DB::select('SELECT TNCDID, TNC_NAME, VALUE_TYPE, DESCRIPTIONS,IS_MANDATORY FROM TBL_MST_TNC_DETAILS  
                WHERE TNCID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) 
                order by TNCDID ASC', [$id]);

        if(!empty($ObjData)){

        foreach ($ObjData as $index=>$dataRow){
        
            $row = '';
            $row = $row.'<tr id="tncdet_'.$dataRow->TNCDID .'"  class="clstncdet"><td width="50%">'.$dataRow->TNC_NAME;
            $row = $row.'<input type="hidden" id="txttncdet_'.$dataRow->TNCDID.'" data-desc="'.$dataRow->TNC_NAME .'" 
            value="'.$dataRow->TNCDID.'"/></td><td id="tncvalue_'.$dataRow->TNCDID .'">'.$dataRow->VALUE_TYPE.'
            <input type="hidden" id="txttncvalue_'.$dataRow->TNCDID.'" data-desc="'.$dataRow->DESCRIPTIONS .'" 
            value="'.$dataRow->IS_MANDATORY.'"/></td></tr>';

            echo $row;
        }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();

    }
    public function gettncdetails2(Request $request){
        $Status = "A";
        $id = $request['id'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
    
        $ObjData =  DB::select('SELECT TNCDID, TNC_NAME, VALUE_TYPE, DESCRIPTIONS,IS_MANDATORY FROM TBL_MST_TNC_DETAILS  
                    WHERE TNCID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) 
                    order by TNCDID ASC', [$id]);
      
            if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){
                $dynamicid = "tncdetvalue_".$index;
                $txtvaluetype = $dataRow->VALUE_TYPE; 
                $chkvaltype =  strtolower($txtvaluetype);
                $txtdescription = $dataRow->DESCRIPTIONS; 
                echo($txtdescription);
               
                if($chkvaltype=="date"){        
                    $strinp = ' <input type="date" placeholder="dd/mm/yyyy" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" /> ';
                }
                else if($chkvaltype=="time"){
                    $strinp = ' <input type="time" placeholder="h:i" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" />';
                }
                else if($chkvaltype=="numeric"){
                    $strinp = '     <input type="text" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" />';
                }
                else if($chkvaltype=="text"){        
                    $strinp = '     <input type="text" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" />';
                }
                else if($chkvaltype=="boolean"){        
                    $strinp = '     <input type="checkbox" name="'.$dynamicid.'" id="'.$dynamicid.'" />';
                }
                else if($chkvaltype=="combobox"){     
                  
                    if($txtdescription)
                    {
                        $strarray = explode(',', $txtdescription);
                        $opts = '';
                        $strinp1 = '<select name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" required>';
                        for ($i = 0; $i < count($strarray); $i++) {
                            $opts = $opts.'<option value="'.$strarray[$i].'">'.$strarray[$i].'</option>';
                        }
                        $strinp2 = '</select>' ;
                        $strinp = $strinp1.$opts.$strinp2;
                    }
                }                
                $row = '';
                $row = $row.'<tr  class="participantRow3">
                <td><input type="text" name="popupTNCDID_'.$index.'" id="popupTNCDID_'.$index.'" class="form-control"  
                autocomplete="off" value="'.$dataRow->TNC_NAME.'"  readonly/></td> <td hidden><input type="hidden" 
                name="TNCDID_REF_'.$index.'" id="TNCDID_REF_'.$index.'" class="form-control" 
                value="'.$dataRow->TNCDID.'"  autocomplete="off" /></td> <td hidden><input type="hidden" 
                name="TNCismandatory_'.$index.'" id="TNCismandatory_'.$index.'" value="'.$dataRow->IS_MANDATORY.'"
                class="form-control" autocomplete="off" /></td>
                <td id="tdinputid_'.$index.'">
                    '.$strinp.'
                </td>
                   <td align="center" ><button class="btn add TNC" title="add" data-toggle="tooltip"  type="button" disabled>
                   <i class="fa fa-plus"></i></button>
                   <button class="btn remove DTNC" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i>
                   </button>
                </td>
                </tr>
                ';
    
                echo $row;
            }
    
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    
        }
        public function gettncdetails3(Request $request){
            $Status = "A";
            $id = $request['id'];
        
            $ObjData =  DB::select('SELECT TNCDID, TNC_NAME, VALUE_TYPE, DESCRIPTIONS,IS_MANDATORY FROM TBL_MST_TNC_DETAILS  
                        WHERE TNCID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) 
                        order by TNCDID ASC', [$id]);
            $ObjDataCount = count($ObjData);
            echo($ObjDataCount);
                exit();
        
            }

    public function getcalculationdetails(Request $request){
        $Status = "A";
        $id = $request['id'];
    
        $ObjData =  DB::select('SELECT TID, COMPONENT,SQNO,BASIS, RATEPERCENTATE, AMOUNT,FORMULA,GST,ACTUAL FROM TBL_MST_CALCULATIONTEMPLATE  
                    WHERE CTID_REF = ?  
                    order by TID ASC', [$id]);
    
            if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){
            
                $row = '';
                $row = $row.'<tr id="ctiddet_'.$dataRow->TID .'"  class="clsctiddet"><td width="50%">'.$dataRow->COMPONENT;
                $row = $row.'<input type="hidden" id="txtctiddet_'.$dataRow->TID.'" data-desc="'.$dataRow->COMPONENT .'" 
                value="'.$dataRow->TID.'"/></td><td id="ctidbasis_'.$dataRow->TID .'">'.$dataRow->BASIS.'
                <input type="hidden" id="txtctidbasis_'.$dataRow->TID.'" data-desc="'.$dataRow->GST .'" 
                value="'.$dataRow->ACTUAL.'"/></td><td id="ctidformula_'.$dataRow->TID .'">'.$dataRow->RATEPERCENTATE.'
                <input type="hidden" id="txtctidformula_'.$dataRow->TID.'" data-desc="'.$dataRow->FORMULA.'" 
                value="'.$dataRow->SQNO.'"/></td><td id="ctidamount_'.$dataRow->TID .'">'.$dataRow->AMOUNT.'</td><td>'.$dataRow->FORMULA.'</td></tr>';
    
                echo $row;
            }
    
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    
        }
    
    public function getcalculationdetails2(Request $request){
            $Status = "A";
            $id = $request['id'];
        
            $ObjData =  DB::select('SELECT TID, COMPONENT,SQNO,BASIS, RATEPERCENTATE, AMOUNT,FORMULA,GST,ACTUAL FROM TBL_MST_CALCULATIONTEMPLATE  
                        WHERE CTID_REF = ?  
                        order by TID ASC', [$id]);

                
        
                if(!empty($ObjData)){
        
                foreach ($ObjData as $dindex=>$dataRow){
                
                    $row = '';
                    $row2 = '';
                    $row3 = '';
                    if($dataRow->GST == 1){
                        $row2 =    '<td style="text-align:center;" ><input type="checkbox" class="filter-none" name="calGST_'.$dindex.'" id="calGST_'.$dindex.'" checked ></td>';
                    }
                    else{
                        $row2 =    '<td style="text-align:center;" ><input type="checkbox" class="filter-none" name="calGST_'.$dindex.'" id="calGST_'.$dindex.'"  ></td>';
                    }

                    if($dataRow->ACTUAL == 1){
                        $row3 =    '<td style="text-align:center;" ><input type="checkbox" class="filter-none" name="calACTUAL_'.$dindex.'" id="calACTUAL_'.$dindex.'" checked ></td>';
                    }
                    else{
                        $row3 =    '<td style="text-align:center;" ><input type="checkbox" class="filter-none" name="calACTUAL_'.$dindex.'" id="calACTUAL_'.$dindex.'"  ></td>';
                    }
                    if($dataRow->RATEPERCENTATE == '.0000'){
                        $row4 =    '<td><input type="text" name="VALUE_'.$dindex.'" id="VALUE_'.$dindex.'" class="form-control two-digits"  value="'.$dataRow->AMOUNT.'" maxlength="15" autocomplete="off" onkeyup="bindGSTCalTemplate()" /></td>';
                    }
                    else{
                        $row4 =    '<td><input type="text" name="VALUE_'.$dindex.'" id="VALUE_'.$dindex.'" class="form-control two-digits"  value="'.$dataRow->AMOUNT.'" maxlength="15" autocomplete="off"  readonly/></td>';
                    }

                    $row = $row.'<tr  class="participantRow5">
                    <td><input type="text" name="popupTID_'.$dindex.'" id="popupTID_'.$dindex.'" class="form-control"  autocomplete="off" value="'.$dataRow->COMPONENT.'"  readonly/></td>
                    <td hidden><input type="hidden" name="TID_REF_'.$dindex.'" id="TID_REF_'.$dindex.'" class="form-control" value="'.$dataRow->TID.'" autocomplete="off" /></td>
                    <td><input type="text" name="RATE_'.$dindex.'" id="RATE_'.$dindex.'" class="form-control four-digits"  value="'.$dataRow->RATEPERCENTATE.'" maxlength="8" autocomplete="off"  readonly/></td>
                    <td hidden><input type="hidden" name="BASIS_'.$dindex.'" id="BASIS_'.$dindex.'" class="form-control"  value="'.$dataRow->BASIS.'" autocomplete="off" /></td>
                    <td hidden><input type="hidden" name="SQNO_'.$dindex.'" id="SQNO_'.$dindex.'" class="form-control"  value="'.$dataRow->SQNO.'" autocomplete="off" /></td>
                    <td hidden><input type="hidden" name="FORMULA_'.$dindex.'" id="FORMULA_'.$dindex.'" class="form-control"  value="'.$dataRow->FORMULA.'" autocomplete="off" /></td>
                    '.$row4.$row2.'<td><input type="text" name="calIGST_'.$dindex.'" id="calIGST_'.$dindex.'" class="form-control four-digits" maxlength="8"   autocomplete="off"  readonly/></td>
                    <td><input type="text" name="AMTIGST_'.$dindex.'" id="AMTIGST_'.$dindex.'" class="form-control two-digits" maxlength="15"   autocomplete="off"  readonly/></td>
                    <td><input type="text" name="calCGST_'.$dindex.'" id="calCGST_'.$dindex.'" class="form-control four-digits" maxlength="8"   autocomplete="off"  readonly/></td>
                    <td><input type="text" name="AMTCGST_'.$dindex.'" id="AMTCGST_'.$dindex.'" class="form-control two-digits" maxlength="15"   autocomplete="off"  readonly/></td>
                    <td><input type="text" name="calSGST_'.$dindex.'" id="calSGST_'.$dindex.'" class="form-control four-digits" maxlength="8"   autocomplete="off"  readonly/></td>
                    <td><input type="text" name="AMTSGST_'.$dindex.'" id="AMTSGST_'.$dindex.'" class="form-control two-digits" maxlength="15"   autocomplete="off"  readonly/></td>
                    <td><input type="text" name="TOTGSTAMT_'.$dindex.'" id="TOTGSTAMT_'.$dindex.'" class="form-control two-digits"  maxlength="15"   autocomplete="off"  readonly/></td>
                    '.$row3.'<td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                    </tr>
                    <tr></tr>';
        
                    echo $row;
                }
        
                }else{
                    echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
                exit();
        
    }
    public function getcalculationdetails3(Request $request){
        $Status = "A";
        $id = $request['id'];
    
        $ObjData =  DB::select('SELECT TID, COMPONENT,SQNO,BASIS, RATEPERCENTATE, AMOUNT,FORMULA,GST,ACTUAL FROM TBL_MST_CALCULATIONTEMPLATE  
                    WHERE CTID_REF = ?  
                    order by TID ASC', [$id]);

        $ObjDataCount = count($ObjData);
        echo $ObjDataCount;            
        exit();
    
    }

    public function getsubledger(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $CODE = $request['CODE'];
        $NAME = $request['NAME'];

        $CUSTPRSCT = $request['CUSTPRSCT'];
    
        $sp_popup = [
            $CYID_REF, $BRID_REF,$CODE,$NAME
        ];

        if($CUSTPRSCT ==="CUSTOMER"){

            $ObjData = DB::select('EXEC SP_GET_DEALER_CUSTOMER_SQ ?,?,?,?', $sp_popup);

                if(!empty($ObjData)){
    
                    foreach ($ObjData as $index=>$dataRow){
                    
                        $row = '';
                        $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_SLID_REF[]" id="subgl_'.$index.'" class="clssubgl" value="'.$dataRow-> SGLID.'" ></td>';
                        $row = $row.'<td class="ROW2">'.$dataRow->SGLCODE;
                        $row = $row.'<input type="hidden" id="txtsubgl_'.$index.'" data-desc="'.$dataRow->SGLCODE .' - ';
                        $row = $row.$dataRow->SLNAME. '" data-desc2="'.$dataRow->GLID_REF. '" data-desc3="'.$dataRow->CUSTOMER_TYPE. '" value="'.$dataRow->SGLID.'"/></td><td class="ROW3">'.$dataRow->SLNAME.'</td></tr>';
            
                        echo $row;
                    }
            
                    }else{
                        echo '<tr><td colspan="2">Record not found.</td></tr>';
                    }

            }
            else{

                $ObjData1    =   DB::table('TBL_MST_PROSPECT')->where('CYID_REF','=',Auth::user()->CYID_REF)->whereRaw('(DEACTIVATED IS NULL  OR DEACTIVATED = 0)')->where('STATUS','=','A')->select('PID AS SLID_REF','PCODE AS CCODE','NAME AS PROSPECT_NAME')->get();
    
                if(!empty($ObjData1)){
    
                    foreach ($ObjData1 as $index=>$dataRow){
                    
                        $row = '';
                        $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_SLID_REF[]" id="subgl_'.$index.'" class="clssubgl" value="'.$dataRow-> SLID_REF.'" ></td>';
                        $row = $row.'<td class="ROW2">'.$dataRow->CCODE;
                        $row = $row.'<input type="hidden" id="txtsubgl_'.$index.'" data-desc="'.$dataRow->CCODE .' - ';
                        $row = $row.$dataRow->PROSPECT_NAME. '" data-desc2="" data-desc3="'.$CUSTPRSCT. '" value="'.$dataRow->SLID_REF.'"/></td><td class="ROW3">'.$dataRow->PROSPECT_NAME.'</td></tr>';
            
                        echo $row;
                    }
            
                    }else{
                        echo '<tr><td colspan="2">Record not found.</td></tr>';
                    }

                }

            exit();
        }


    public function getsalesenquiry(Request $request){
        $Status = "A";
        $id = $request['id'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $SP_PARAMETERS = [$CYID_REF,$BRID_REF,$FYID_REF,$id];
        $ObjData =  DB::select('EXEC SP_SE_GETLIST ?,?,?,?', $SP_PARAMETERS);
    
            if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){
                $row = '';
                $row = $row.'<tr >
                
                <td style="text-align:center; width:10%">';
                $row = $row.'<input type="checkbox" name="salesenquiry[]" id="secode_'.$dataRow->SEQID .'"  class="clsseid" 
                value="'.$dataRow->SEQID.'"/>   
                
                
                <td width="30%">'.$dataRow->ENQNO;
                $row = $row.'<input type="hidden" id="txtsecode_'.$dataRow->SEQID.'" data-desc="'.$dataRow->ENQNO.'" 
                value="'.$dataRow->SEQID.'"/></td><td width="60%" >'.$dataRow->ENQDT.'</td></tr>';
                echo $row;
            }
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    
        }

    public function getItemDetailsEnquirywise(Request $request){
        $Status = "A";
        $id = $request['id'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $taxstate = $request['taxstate'];  
        $SQDT = $request['SQDT'];  
        $AlpsStatus =   $this->AlpsStatus();

        $ObjSEID = DB::select('SELECT * FROM TBL_TRN_SLEQ01_HDR  
        WHERE ENQNO = ? ', [$id]);

            /*
            $Objquote =  DB::select('SELECT * FROM TBL_TRN_SLEQ01_MAT  
                    WHERE PENDING_QTY > ? AND SEQID_REF = ? order by ITEMID_REF ASC', ['0.000',$ObjSEID[0]->SEQID]);
            */        

          


$Objquote =  DB::select("SELECT * FROM (
    SELECT * FROM TBL_TRN_SLEQ01_MAT  B
    WHERE B.PENDING_QTY > '0.000' AND B.SEQID_REF = ".$ObjSEID[0]->SEQID."
    AND B.ITEMID_REF NOT IN (SELECT A.ITEMID_REF FROM TBL_TRN_SLQT01_MAT A (NOLOCK) WHERE A.SEQID_REF = SEQID_REF)
    UNION 
    SELECT * FROM TBL_TRN_SLEQ01_MAT  B
    WHERE B.SEQID_REF = ".$ObjSEID[0]->SEQID."
    AND B.ITEMID_REF IN (SELECT A.ITEMID_REF FROM TBL_TRN_SLQT01_MAT A (NOLOCK) WHERE A.SEQID_REF = SEQID_REF)
    ) C
    order by C.ITEMID_REF ASC");


          

                    if(!empty($Objquote)){
                        foreach ($Objquote as $index=>$dataRow){
                            $ObjItem =  DB::select('SELECT top 1 * FROM TBL_MST_ITEM  
                                        WHERE ITEMID = ? AND MATERIAL_TYPE  != ?', [$dataRow->ITEMID_REF,'RM-Raw Material']);
                                        $ObjLIST =   DB::table('TBL_MST_PRICELIST_MAT')  
                                        ->select('TBL_MST_PRICELIST_MAT.*')
                                        ->where('TBL_MST_PRICELIST_MAT.ITEMID_REF','=',$dataRow->ITEMID_REF)
                                        ->first();
                                            if(($ObjLIST)){
                                                $Taxid = [];
                                                $ObjInTax = $ObjLIST->GST_IN_LP; 
                                                    if ($ObjInTax == 1){
                                                        $ObjHSN =   DB::select('SELECT top 1 HSNID_REF FROM TBL_MST_ITEM  
                                                                WHERE STATUS= ? AND ITEMID = ? ', ['A',$dataRow->ITEMID_REF]);
                                                        if($taxstate == "OutofState"){
                                                            $ObjTax =  DB::table('TBL_MST_HSNNORMAL')  
                                                                    ->select('NRATE')
                                                                    ->whereIn('TAXID_REF',function($query) 
                                                                                {       
                                                                                $query->select('TAXID')->from('TBL_MST_TAXTYPE')
                                                                                                ->where('STATUS','=','A')
                                                                                                ->where('OUTOFSTATE','=',1);                       
                                                                    })->where('HSNID_REF','=',$ObjHSN[0]->HSNID_REF) 
                                                                    ->get()->toArray();
                                                        }
                                                        else{
                                                            $ObjTax =  DB::table('TBL_MST_HSNNORMAL')  
                                                                    ->select('NRATE')
                                                                    ->whereIn('TAXID_REF',function($query) 
                                                                                {       
                                                                                $query->select('TAXID')->from('TBL_MST_TAXTYPE')
                                                                                                ->where('STATUS','=','A')
                                                                                                ->where('WITHINSTATE','=',1);                       
                                                                    })->where('HSNID_REF','=',$ObjHSN[0]->HSNID_REF)
                                                                    ->get()->toArray();
                                                        }
                                                        $ObjTaxR = 0;
                                                        foreach ($ObjTax as $tindex=>$tRow){
                                                        $ObjTaxR += $tRow->NRATE;
                                                        if($tRow->NRATE !== '')
                                                            {
                                                            array_push($Taxid,$tRow->NRATE);
                                                            }
                                                        }
                                                        $ObjTaxDet = 100 + $ObjTaxR;
                                                        $ObjStdCost =  ($ObjLIST->LISTPRICE*100)/$ObjTaxDet;
                                                        $StdCost = $ObjStdCost;
                                                       
                                                    }
                                                    else
                                                    {
                                                        
                                                        $ObjHSN =   DB::select('SELECT top 1 HSNID_REF FROM TBL_MST_ITEM  
                                                        WHERE STATUS= ? AND ITEMID = ? ', ['A',$dataRow->ITEMID_REF]);
                                                            if($taxstate == "OutofState"){
                                                                $ObjTax =  DB::table('TBL_MST_HSNNORMAL')  
                                                                        ->select('NRATE')
                                                                        ->whereIn('TAXID_REF',function($query) 
                                                                                    {       
                                                                                    $query->select('TAXID')->from('TBL_MST_TAXTYPE')
                                                                                                    ->where('STATUS','=','A')
                                                                                                    ->where('OUTOFSTATE','=',1);                       
                                                                        })->where('HSNID_REF','=',$ObjHSN[0]->HSNID_REF) 
                                                                        ->get()->toArray();
                                                            }
                                                            else{
                                                                $ObjTax =  DB::table('TBL_MST_HSNNORMAL')  
                                                                        ->select('NRATE')
                                                                        ->whereIn('TAXID_REF',function($query) 
                                                                                    {       
                                                                                    $query->select('TAXID')->from('TBL_MST_TAXTYPE')
                                                                                                    ->where('STATUS','=','A')
                                                                                                    ->where('WITHINSTATE','=',1);                       
                                                                        })->where('HSNID_REF','=',$ObjHSN[0]->HSNID_REF)
                                                                        ->get()->toArray();
                                                            }
                                                        foreach ($ObjTax as $tindex=>$tRow){
                                                            if($tRow->NRATE !== '')
                                                                {
                                                                array_push($Taxid,$tRow->NRATE);
                                                                }
                                                            }
                                                        $StdCost = $ObjLIST->LISTPRICE;
                                                       
                                                    }
                                                }
                                                else
                                                {
                                                    $Taxid = [];
                                                    $ObjHSN =   DB::select('SELECT top 1 HSNID_REF FROM TBL_MST_ITEM  
                                                        WHERE STATUS= ? AND ITEMID = ? ', ['A',$dataRow->ITEMID_REF]);
                                                            if($taxstate == "OutofState"){
                                                                $ObjTax =  DB::table('TBL_MST_HSNNORMAL')  
                                                                        ->select('NRATE')
                                                                        ->whereIn('TAXID_REF',function($query) 
                                                                                    {       
                                                                                    $query->select('TAXID')->from('TBL_MST_TAXTYPE')
                                                                                                    ->where('STATUS','=','A')
                                                                                                    ->where('OUTOFSTATE','=',1);                       
                                                                        })->where('HSNID_REF','=',$ObjHSN[0]->HSNID_REF) 
                                                                        ->get()->toArray();
                                                            }
                                                            else{
                                                                $ObjTax =  DB::table('TBL_MST_HSNNORMAL')  
                                                                        ->select('NRATE')
                                                                        ->whereIn('TAXID_REF',function($query) 
                                                                                    {       
                                                                                    $query->select('TAXID')->from('TBL_MST_TAXTYPE')
                                                                                                    ->where('STATUS','=','A')
                                                                                                    ->where('WITHINSTATE','=',1);                       
                                                                        })->where('HSNID_REF','=',$ObjHSN[0]->HSNID_REF)
                                                                        ->get()->toArray();
                                                            }
                                                        foreach ($ObjTax as $tindex=>$tRow){
                                                            if($tRow->NRATE !== '')
                                                                {
                                                                array_push($Taxid,$tRow->NRATE);
                                                                }
                                                            }
                                                    $StdCost = $ObjItem[0]->STDCOST;
                                                }
                        
                            $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                        WHERE  CYID_REF = ?  AND UOMID = ? 
                                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                        [$CYID_REF, $ObjItem[0]->MAIN_UOMID_REF, $Status ]);

                            $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                        WHERE  CYID_REF = ?  AND UOMID = ? 
                                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                        [$CYID_REF, $dataRow->ALTUOMID_REF, $Status ]);

                            
                            $ObjAltQTY =  DB::select('SELECT TOP 1  TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV  
                                        WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                                        [$dataRow->ITEMID_REF,$dataRow->ALTUOMID_REF ]);
        
                            $TOQTY =  isset($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 0;
                            $FROMQTY =  isset($ObjAltQTY[0]->FROM_QTY)? $ObjAltQTY[0]->FROM_QTY : 0;

                            $ObjItemGroup =  DB::select('SELECT TOP 1 GROUPCODE, GROUPNAME FROM TBL_MST_ITEMGROUP  
                                        WHERE  CYID_REF = ?  AND ITEMGID = ?
                                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                        [$CYID_REF, $ObjItem[0]->ITEMGID_REF, $Status ]);

                            $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS FROM TBL_MST_ITEMCATEGORY  
                                        WHERE  CYID_REF = ?  AND ICID = ?
                                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                        [$CYID_REF, $ObjItem[0]->ICID_REF, $Status ]);


                            $ItemRowData =  DB::select('SELECT top 1 * FROM TBL_MST_ITEM  WHERE ITEMID = ? ', [$ObjItem[0]->ITEMID]);

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

                        


                            $objItemCost=$this->getItemPrice($ObjItem[0]->ITEMID,$SQDT);
                           if(isset($objItemCost['MRP']) && $objItemCost['MRP'] > 0){
                            $StdCost    =   $objItemCost['MRP'];                             
                            }else{
                            $StdCost        =   $StdCost;
                            }

                            $row = '';
                            if($taxstate != "OutofState"){
                            $row = $row.'<tr id="item_'.$ObjItem[0]->ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                            $row = $row.'<td style="width:10%;">'.$ObjItem[0]->ICODE;
                            $row = $row.'<input type="hidden" id="txtitem_'.$ObjItem[0]->ITEMID.'" data-desc="'.$ObjItem[0]->ICODE.'"
                            value="'.$ObjItem[0]->ITEMID.'"/></td><td style="width:10%;" id="itemname_'.$ObjItem[0]->ITEMID.'" >'.$ObjItem[0]->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$ObjItem[0]->ITEMID.'" data-desc="'.$ObjItem[0]->ITEM_SPECI.'"
                            value="'.$ObjItem[0]->NAME.'"/></td>';
                            $row = $row.'<td style="width:10%;" id="itemuom_'.$ObjItem[0]->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ObjItem[0]->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                            value="'.$ObjItem[0]->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                            $row = $row.'<td style="width:8%;" id="uomqty_'.$ObjItem[0]->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$ObjItem[0]->ITEMID.'" data-desc="'.$TOQTY.'"
                            value="'.$dataRow->ALTUOMID_REF.'"/>'.$dataRow->MAIN_QTY.'</td>';
                            $row = $row.'<td style="width:8%;" id="irate_'.$ObjItem[0]->ITEMID.'"><input type="hidden" id="txtirate_'.$ObjItem[0]->ITEMID.'" data-desc="'.$FROMQTY.'"
                            value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                            $row = $row.'<td style="width:8%;" id="itax_'.$ObjItem[0]->ITEMID.'"><input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'" data-desc="'.$Taxid[0].'"
                            value="'.$Taxid[1].'"/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                            
                            <td style="width:8%;">'.$BusinessUnit.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                            <td style="width:8%;" id="ise_'.$ObjItem[0]->ITEMID.'"><input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'" 
                            value="'.$ObjSEID[0]->ENQNO.'"/>Authorized</td>
                            </tr>';
                            }
                            else
                            {
                                $row = $row.'<tr id="item_'.$ObjItem[0]->ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                            $row = $row.'<td style="width:10%;">'.$ObjItem[0]->ICODE;
                            $row = $row.'<input type="hidden" id="txtitem_'.$ObjItem[0]->ITEMID.'" data-desc="'.$ObjItem[0]->ICODE.'"
                            value="'.$ObjItem[0]->ITEMID.'"/></td><td style="width:10%;" id="itemname_'.$ObjItem[0]->ITEMID.'" >'.$ObjItem[0]->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$ObjItem[0]->ITEMID.'" data-desc="'.$ObjItem[0]->ITEM_SPECI.'"
                            value="'.$ObjItem[0]->NAME.'"/></td>';
                            $row = $row.'<td style="width:10%;" id="itemuom_'.$ObjItem[0]->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ObjItem[0]->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                            value="'.$ObjItem[0]->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                            $row = $row.'<td style="width:8%;" id="uomqty_'.$ObjItem[0]->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$ObjItem[0]->ITEMID.'" data-desc="'.$TOQTY.'"
                            value="'.$dataRow->ALTUOMID_REF.'"/>'.$dataRow->MAIN_QTY.'</td>';
                            $row = $row.'<td style="width:8%;" id="irate_'.$ObjItem[0]->ITEMID.'"><input type="hidden" id="txtirate_'.$ObjItem[0]->ITEMID.'" data-desc="'.$FROMQTY.'"
                            value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                            $row = $row.'<td style="width:8%;" id="itax_'.$ObjItem[0]->ITEMID.'"><input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'" data-desc="'.$Taxid[0].'"
                            value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                            
                            <td style="width:8%;">'.$BusinessUnit.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                            <td style="width:8%;" id="ise_'.$ObjItem[0]->ITEMID.'"><input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'" 
                            value="'.$ObjSEID[0]->ENQNO.'"/>Authorized</td>
                            </tr>';
                            }
                        echo $row;

                        }

                    }else{
                        echo '<tr><td> Record not found.</td></tr>';
                    }
        exit();
    }

    public function getItemDetails(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $taxstate = $request['taxstate'];
        $StdCost = 0;
        $CODE = $request['CODE'];
        $NAME = $request['NAME'];
        $MUOM = $request['MUOM'];
        $GROUP = $request['GROUP'];
        $CTGRY = $request['CTGRY'];
        $BUNIT = $request['BUNIT'];
        $APART = $request['APART'];
        $CPART = $request['CPART'];
        $OPART = $request['OPART'];
        $SQDT = $request['SQDT'];
        //dd($taxstate); 

        $AlpsStatus =   $this->AlpsStatus();

        $sp_popup = [
            $CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART,$taxstate,$SQDT
        ]; 
        
            $ObjItem = DB::select('EXEC sp_get_items_popup_withtax_SQ ?,?,?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);
        
        
      
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
                        $Taxid1             =   isset($dataRow->Taxid1)?$dataRow->Taxid1:NULL;
                        $Taxid2             =   isset($dataRow->Taxid2)?$dataRow->Taxid2:NULL;
                    
                        
                        $row = '';
                        $row.=' <tr id="item_'.$ITEMID.'" class="clsitemid">
                                <td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1"  ></td>
                                <td style="width:10%;">'.$ICODE.'<input type="hidden" id="txtitem_'.$ITEMID.'" data-desc="'.$ICODE.'" value="'.$ITEMID.'"/></td>
                                <td style="width:10%;" id="itemname_'.$ITEMID.'" >'.$NAME.'<input type="hidden" id="txtitemname_'.$ITEMID.'" data-desc="'.$ITEM_SPECI.'" value="'.$NAME.'"/></td>
                                <td style="width:8%;" id="itemuom_'.$ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ITEMID.'" data-desc="'.$Alt_UOM.'" value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>
                                <td style="width:8%;" id="uomqty_'.$ITEMID.'" ><input type="hidden" id="txtuomqty_'.$ITEMID.'" data-desc="'.$TOQTY.'" value="'.$ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>
                                <td style="width:8%;" id="irate_'.$ITEMID.'"><input type="hidden" id="txtirate_'.$ITEMID.'" data-desc="'.$FROMQTY.'" value="'.$STDCOST.'"/>'.$GroupName.'</td>
                                <td style="width:8%;" id="itax_'.$ITEMID.'"><input type="hidden" id="txtitax_'.$ITEMID.'" data-desc="'.$Taxid1.'"  value="'.$Taxid2.'" />'.$Categoryname.'</td>
                                <td style="width:8%;">'.$BusinessUnit.'</td>
                                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                                <td style="width:8%;">Authorized</td>
                                </tr>'; 
                        echo $row;                          
                    } 
                    
                }           
                else{
                 echo '<tr><td> Record not found.</td></tr>';
                }
        exit();
    }


    public function getItemDetails_lead(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $taxstate = $request['taxstate'];
        $SQDT = $request['SQDT'];

        //dd($taxstate); 

        $StdCost = 0;
        $CODE = $request['CODE'];
        $NAME = $request['NAME'];
        $MUOM = $request['MUOM'];
        $GROUP = $request['GROUP'];
        $CTGRY = $request['CTGRY'];
        $BUNIT = $request['BUNIT'];
        $APART = $request['APART'];
        $CPART = $request['CPART'];
        $OPART = $request['OPART'];
        $LEADID_REF = $request['LEADID_REF']; 
        $AlpsStatus =   $this->AlpsStatus();

        $sp_popup = [
            $CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART,$taxstate,$LEADID_REF,$SQDT
        ]; 
        
            $ObjItem = DB::select('EXEC SP_GET_ITEMS_POPUP_WITHTAX_LEAD ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);
        
       //dd($ObjItem);
      
                if(!empty($ObjItem)){

                    foreach ($ObjItem as $index=>$dataRow){
                        $ITEMID             =   isset($dataRow->ITEMID)?$dataRow->ITEMID:NULL;
                        $ICODE              =   isset($dataRow->ICODE)?$dataRow->ICODE:NULL;
                        $NAME               =   isset($dataRow->NAME)?$dataRow->NAME:NULL;
                        $ITEM_SPECI         =   isset($dataRow->ITEM_SPECI)?$dataRow->ITEM_SPECI:NULL;
                        $MAIN_UOMID_REF     =   isset($dataRow->MAIN_UOMID_REF)?$dataRow->MAIN_UOMID_REF:NULL;
                        $Main_UOM           =   isset($dataRow->MAIN_UOM)?$dataRow->MAIN_UOM:NULL;
                        $ALT_UOMID_REF      =   isset($dataRow->ALT_UOMID_REF)?$dataRow->ALT_UOMID_REF:NULL;
                        $Alt_UOM            =   isset($dataRow->ALT_UOM)?$dataRow->ALT_UOM:NULL;
                        $FROMQTY            =   isset($dataRow->FROMQTY)?$dataRow->FROMQTY:NULL;
                        $TOQTY              =   isset($dataRow->TOQTY)?$dataRow->TOQTY:NULL;
                        $STDCOST            =   isset($dataRow->STDCOST)?$dataRow->STDCOST:NULL;
                        $GroupName          =   isset($dataRow->GroupName)?$dataRow->GroupName:NULL;
                        $Categoryname       =   isset($dataRow->Categoryname)?$dataRow->Categoryname:NULL;
                        $BusinessUnit       =   isset($dataRow->BusinessUnit)?$dataRow->BusinessUnit:NULL;
                        $ALPS_PART_NO       =   isset($dataRow->ALPS_PART_NO)?$dataRow->ALPS_PART_NO:NULL;
                        $CUSTOMER_PART_NO   =   isset($dataRow->CUSTOMER_PART_NO)?$dataRow->CUSTOMER_PART_NO:NULL;
                        $OEM_PART_NO        =   isset($dataRow->OEM_PART_NO)?$dataRow->OEM_PART_NO:NULL;
                        $Taxid1             =   isset($dataRow->Taxid1)?$dataRow->Taxid1:NULL;
                        $Taxid2             =   isset($dataRow->Taxid2)?$dataRow->Taxid2:NULL;
                    
                        
                        $row = '';
                        $row.=' <tr id="item_'.$ITEMID.'" class="clsitemid">
                                <td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1"  ></td>
                                <td style="width:10%;">'.$ICODE.'<input type="hidden" id="txtitem_'.$ITEMID.'" data-desc="'.$ICODE.'" value="'.$ITEMID.'"/></td>
                                <td style="width:10%;" id="itemname_'.$ITEMID.'" >'.$NAME.'<input type="hidden" id="txtitemname_'.$ITEMID.'" data-desc="'.$ITEM_SPECI.'" value="'.$NAME.'"/></td>
                                <td style="width:8%;" id="itemuom_'.$ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ITEMID.'" data-desc="'.$Alt_UOM.'" value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>
                                <td style="width:8%;" id="uomqty_'.$ITEMID.'" ><input type="hidden" id="txtuomqty_'.$ITEMID.'" data-desc="'.$TOQTY.'" value="'.$ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>
                                <td style="width:8%;" id="irate_'.$ITEMID.'"><input type="hidden" id="txtirate_'.$ITEMID.'" data-desc="'.$FROMQTY.'" value="'.$STDCOST.'"/>'.$GroupName.'</td>
                                <td style="width:8%;" id="itax_'.$ITEMID.'"><input type="hidden" id="txtitax_'.$ITEMID.'" data-desc="'.$Taxid1.'"  value="'.$Taxid2.'" />'.$Categoryname.'</td>
                                <td style="width:8%;">'.$BusinessUnit.'</td>
                                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                                <td style="width:8%;">Authorized</td>
                                </tr>'; 
                        echo $row;                          
                    } 
                    
                }           
                else{
                 echo '<tr><td> Record not found.</td></tr>';
                }
        exit();
    }

    public function getcreditdays(Request $request){
    $Status = "A";
    $id = $request['id'];

    $ObjData =  DB::select('SELECT top 1 CREDITDAY FROM TBL_MST_CUSTOMER  
                WHERE STATUS= ? AND SLID_REF = ? ', [$Status,$id]);

     
            if(!empty($ObjData)){

            echo($ObjData[0]->CREDITDAY);

            }else{
                echo '0';
            }
            exit();

    }

    // 

    public function getitemwisetax1(Request $request){
        $Status = "A";
        $itemid = $request['itemid'];
        $taxstate = $request['taxstate'];

        $ObjHSN =   DB::select('SELECT top 1 HSNID_REF FROM TBL_MST_ITEM  
                    WHERE STATUS= ? AND ITEMID = ? ', [$Status,$itemid]);

        if($taxstate == "OutofState")
        {
            $ObjTax =  DB::table('TBL_MST_HSNNORMAL')  
                    ->select('*')
                    ->whereIn('TAXID_REF',function($query) 
                                {       
                                $query->select('TAXID')->from('TBL_MST_TAXTYPE')
                                                ->where('STATUS','=','A')
                                                ->where('OUTOFSTATE','=',1);                       
                    })->where('HSNID_REF','=',$ObjHSN[0]->HSNID_REF)
                    ->first(); 
        }
        else
        {
            $ObjTax =  DB::table('TBL_MST_HSNNORMAL')  
                    ->select('*')
                    ->whereIn('TAXID_REF',function($query) 
                                {       
                                $query->select('TAXID')->from('TBL_MST_TAXTYPE')
                                                ->where('STATUS','=','A')
                                                ->where('WITHINSTATE','=',1);                       
                    })->where('HSNID_REF','=',$ObjHSN[0]->HSNID_REF)
                    ->first(); 
        }
        
    
         
                if($ObjTax){    
                echo($ObjTax->NRATE);    
                }else{
                    echo '0';
                }
                exit();
    
    }

    public function getaltuomqty(Request $request){
        $id = $request['id'];
        $itemid = $request['itemid'];
        $mqty = $request['mqty'];

    
        $ObjData =  DB::select('SELECT top 1 TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV  
                    WHERE  ITEMID_REF = ? AND TO_UOMID_REF =?', [$itemid,$id]);
    
         
                if(!empty($ObjData)){
                $auomqty = ($mqty/$ObjData[0]->FROM_QTY)*($ObjData[0]->TO_QTY);
                echo($auomqty);
    
                }else{
                    echo '0';
                }
                exit();
    
        }

    public function getAltUOM(Request $request){
        $id = $request['id'];
        $fieldid    = $request['fieldid'];

        $ObjData =  DB::select('SELECT TO_UOMID_REF FROM TBL_MST_ITEM_UOMCONV  
                WHERE ITEMID_REF= ?  order by IUCID ASC', [$id]);

        if(!empty($ObjData)){

        foreach ($ObjData as $index=>$dataRow){

            $ObjAltUOM =  DB::select('SELECT top 1 UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                WHERE UOMID= ?  ', [$dataRow->TO_UOMID_REF]);
        
            $row = '';
            $row = $row.'<tr >
            <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="altuom_'.$dataRow->TO_UOMID_REF .'"  class="clsaltuom" value="'.$dataRow->TO_UOMID_REF.'" ></td>
            <td class="ROW2">'.$ObjAltUOM[0]->UOMCODE;
            $row = $row.'<input type="hidden" id="txtaltuom_'.$dataRow->TO_UOMID_REF.'" data-desc="'.$ObjAltUOM[0]->UOMCODE .' - ';
            $row = $row.$ObjAltUOM[0]->DESCRIPTIONS. '" value="'.$dataRow->TO_UOMID_REF.'"/></td>
            <td class="ROW3">'.$ObjAltUOM[0]->DESCRIPTIONS.'</td></tr>';

            echo $row;
        }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();
    
    }

    public function getBillTo(Request $request){
        $Status = "A";
        $id = $request['id']; 
        $BRID_REF = Session::get('BRID_REF');
        $quotation_type = isset($request['type']) ? $request['type'] :"";
        $leadid_ref = isset($request['leadid_ref']) ? $request['leadid_ref'] :"";   

        if($quotation_type=="PROSPECT")
        {
            $ObjBillTo =  DB::select("SELECT CITYID_REF,STID_REF,CTRYID_REF,ADDRESS FROM TBL_TRN_LEAD_GENERATION WHERE LEAD_ID=$leadid_ref");

            $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  
            WHERE BRID= ? ', [$BRID_REF]);

            if($ObjBillTo[0]->STID_REF == $ObjBranch[0]->STID_REF)
            {
                $TAXSTATE = 'WithinState';
            }
            else
            {
                $TAXSTATE = 'OutofState';
            }

         

            $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                        WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                        [$Status,$ObjBillTo[0]->CITYID_REF,$ObjBillTo[0]->CTRYID_REF,$ObjBillTo[0]->STID_REF]);
                      

            $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                        WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBillTo[0]->STID_REF,$ObjBillTo[0]->CTRYID_REF]);
                        

            $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                        WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBillTo[0]->CTRYID_REF]);
                        

            $ObjBillTo[0]->CADD = $ObjBillTo[0]->ADDRESS;
            $ObjAddressID = "";
           

        }else{

        $ObjCust =  DB::select('SELECT top 1 CID,TAX_CALCULATION FROM TBL_MST_CUSTOMER  
                    WHERE STATUS= ? AND SLID_REF = ? ', [$Status,$id]);


        $cid = $ObjCust[0]->CID;

        $ObjBillTo =  DB::select('SELECT top 1 * FROM TBL_MST_CUSTOMERLOCATION WHERE DEFAULT_BILLTO= ? AND CID_REF = ? ', [1,$cid]);

        $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH WHERE BRID= ? ', [$BRID_REF]);

        if($ObjBillTo[0]->STID_REF == $ObjBranch[0]->STID_REF)
        {
            $TAXSTATE = 'WithinState';
        }
        else
        {
            $TAXSTATE = 'OutofState';
        }
                   

        $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                    WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                    [$Status,$ObjBillTo[0]->CITYID_REF,$ObjBillTo[0]->CTRYID_REF,$ObjBillTo[0]->STID_REF]);

        $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                    WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBillTo[0]->STID_REF,$ObjBillTo[0]->CTRYID_REF]);

        $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                    WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBillTo[0]->CTRYID_REF]);

        $ObjAddressID = $ObjBillTo[0]->CLID;

        }

                if(!empty($ObjBillTo)){
                    
                $objAddress = $ObjBillTo[0]->CADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                
                $row = '';
                $row = $row.'<input type="text" name="txtBILLTO" id="txtBILLTO" class="form-control"  autocomplete="off" value="'. $objAddress.'" readonly/>';
                $row = $row.'<input type="hidden" name="BILLTO" id="BILLTO" class="form-control" autocomplete="off" value="'. $ObjAddressID.'" readonly/>';

                if(isset($ObjCust[0]->TAX_CALCULATION) && $ObjCust[0]->TAX_CALCULATION =='BILL TO'){
                    $row = $row.'<input type="hidden" name="Tax_State" id="Tax_State" class="form-control" autocomplete="off" value="'. $TAXSTATE.'" readonly/>';
                }
                
                echo $row;
                }else{
                    echo '';
                }
                exit();
    
        }

        public function getBillAddress(Request $request){
            $Status = "A";
            $id = $request['id'];
            $BRID_REF = Session::get('BRID_REF');
            if(!is_null($id))
            {
            $ObjCust =  DB::select('SELECT top 1 CID,TAX_CALCULATION FROM TBL_MST_CUSTOMER  
                    WHERE STATUS= ? AND SLID_REF = ? ', [$Status,$id]);
    
            $cid = $ObjCust[0]->CID;
            $ObjBillTo =  DB::select('SELECT  * FROM TBL_MST_CUSTOMERLOCATION  
                        WHERE BILLTO= ? AND CID_REF = ? ', [1,$cid]);
        
                if(!empty($ObjBillTo)){
        
                foreach ($ObjBillTo as $index=>$dataRow){

                    $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH WHERE BRID= ? ', [$BRID_REF]);
    
                    if($dataRow->STID_REF == $ObjBranch[0]->STID_REF)
                    {
                        $TAXSTATE = 'WithinState';
                    }
                    else
                    {
                        $TAXSTATE = 'OutofState';
                    }

                    $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                    WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                    [$Status,$dataRow->CITYID_REF,$dataRow->CTRYID_REF,$dataRow->STID_REF]);

                    $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                                WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$dataRow->STID_REF,$dataRow->CTRYID_REF]);

                    $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                                WHERE STATUS= ? AND CTRYID = ? ', [$Status,$dataRow->CTRYID_REF]);
                    $objAddress = $dataRow->CADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;

                    $row = '';
                    $row = $row.'<tr ><td style="text-align:center; width:10%">';
                    $row = $row.'<input type="checkbox" name="billto[]" id="billto_'.$dataRow->CLID .'"  class="clsbillto" 
                    value="'.$dataRow->CLID.'"/>                    
                    
                    </td>
                    <td style="width:30%;">'.$dataRow->NAME;
                    $row = $row.'<input type="hidden" id="txtbillto_'.$dataRow->CLID.'" data-desc="'.$objAddress.'" data-desc2="'.$TAXSTATE.'" data-desc3="'.$ObjCust[0]->TAX_CALCULATION.'" value="'.$dataRow->CLID.'"/></td>
                    <td style="width:60%;">'.$objAddress.'</td></tr>';
                    echo $row;
                }
        
                }else{
                    echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
                exit();
            }
        }

        public function getShipAddress(Request $request){
            $Status = "A";
            $id = $request['id'];
            $BRID_REF = Session::get('BRID_REF');
            if(!is_null($id))
            {
            $ObjCust =  DB::select('SELECT top 1 CID,TAX_CALCULATION FROM TBL_MST_CUSTOMER WHERE STATUS= ? AND SLID_REF = ? ', [$Status,$id]);
    
            $cid = $ObjCust[0]->CID;

            $ObjShipTo =  DB::select('SELECT  * FROM TBL_MST_CUSTOMERLOCATION WHERE SHIPTO= ? AND CID_REF = ? ', [1,$cid]);         
        
                if(!empty($ObjShipTo)){
        
                foreach ($ObjShipTo as $index=>$dataRow){

                    $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  
                        WHERE BRID= ? ', [$BRID_REF]);

                        if($dataRow->STID_REF == $ObjBranch[0]->STID_REF)
                        {
                            $TAXSTATE = 'WithinState';
                        }
                        else
                        {
                            $TAXSTATE = 'OutofState';
                        }

                    $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                    WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                    [$Status,$dataRow->CITYID_REF,$dataRow->CTRYID_REF,$dataRow->STID_REF]);

                    $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                                WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$dataRow->STID_REF,$dataRow->CTRYID_REF]);

                    $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                                WHERE STATUS= ? AND CTRYID = ? ', [$Status,$dataRow->CTRYID_REF]);
                    $objAddress = $dataRow->CADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;

                    $row = '';
                    $row = $row.'<tr >
                    
                    <td style="text-align:center; width:10%">';
                    $row = $row.'<input type="checkbox" name="shipto[]" id="shipto_'.$dataRow->CLID .'"  class="clsshipto" 
                    value="'.$dataRow->CLID.'"/>                    
                    
                    </td>
                    <td width="50%">'.$dataRow->NAME;
                    $row = $row.'<input type="hidden" id="txtshipto_'.$dataRow->CLID.'" data-desc="'.$objAddress.'" data-desc2="'.$TAXSTATE.'" data-desc3="'.$ObjCust[0]->TAX_CALCULATION.'" value="'.$dataRow->CLID.'"/></td><td id="txtshipadd_'.$dataRow->CLID.'" >'.$objAddress.'</td></tr>';
                    echo $row;
                }
        
                }else{
                    echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
                exit();
            }
        }

        public function getShipTo(Request $request){
            $Status = "A";
            $id   = $request['id'];
            $BRID_REF = Session::get('BRID_REF');
            $quotation_type = isset($request['type']) ? $request['type'] :"";
            $leadid_ref = isset($request['leadid_ref']) ? $request['leadid_ref'] :"";

            if($quotation_type=="PROSPECT")
            {
                $ObjSHIPTO =  DB::select("SELECT CITYID_REF,STID_REF,CTRYID_REF,ADDRESS FROM TBL_TRN_LEAD_GENERATION WHERE LEAD_ID=$leadid_ref");

                $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  
                WHERE BRID= ? ', [$BRID_REF]);

                if($ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF)
                {
                    $TAXSTATE = 'WithinState';
                }
                else
                {
                    $TAXSTATE = 'OutofState';
                }

             

                $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                            WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                            [$Status,$ObjSHIPTO[0]->CITYID_REF,$ObjSHIPTO[0]->CTRYID_REF,$ObjSHIPTO[0]->STID_REF]);
                          

                $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                            WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjSHIPTO[0]->STID_REF,$ObjSHIPTO[0]->CTRYID_REF]);
                            

                $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                            WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjSHIPTO[0]->CTRYID_REF]);
                            

                $ObjSHIPTO[0]->CADD = $ObjSHIPTO[0]->ADDRESS;
                $ObjAddressID = "";
               

            }else{
                $ObjCust =  DB::select('SELECT top 1 CID,TAX_CALCULATION FROM TBL_MST_CUSTOMER  
                WHERE STATUS= ? AND SLID_REF = ? ', [$Status,$id]);

                $cid = $ObjCust[0]->CID;
                $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_CUSTOMERLOCATION  
                            WHERE DEFAULT_SHIPTO= ? AND CID_REF = ? ', [1,$cid]);

                $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  
                WHERE BRID= ? ', [$BRID_REF]);

                if($ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF)
                {
                    $TAXSTATE = 'WithinState';
                }
                else
                {
                    $TAXSTATE = 'OutofState';
                }

                $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                            WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                            [$Status,$ObjSHIPTO[0]->CITYID_REF,$ObjSHIPTO[0]->CTRYID_REF,$ObjSHIPTO[0]->STID_REF]);

                $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                            WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjSHIPTO[0]->STID_REF,$ObjSHIPTO[0]->CTRYID_REF]);

                $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                            WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjSHIPTO[0]->CTRYID_REF]);

                $ObjAddressID = $ObjSHIPTO[0]->CLID;

            }

                    if(!empty($ObjSHIPTO)){
                        
                    $objAddress = $ObjSHIPTO[0]->CADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                   
                
                    $row = '';
                    $row = $row.'<input type="text" name="txtSHIPTO" id="txtSHIPTO" class="form-control"  autocomplete="off" value="'. $objAddress.'" readonly/>';
                    $row = $row.'<input type="hidden" name="SHIPTO" id="SHIPTO" class="form-control" autocomplete="off" value="'. $ObjAddressID.'" readonly/>';

                    if(isset($ObjCust[0]->TAX_CALCULATION) && $ObjCust[0]->TAX_CALCULATION !='BILL TO'){
                        $row = $row.'<input type="hidden" name="Tax_State" id="Tax_State" class="form-control" autocomplete="off" value="'. $TAXSTATE.'" readonly/>';
                    }
                    
                    echo $row;
                    }else{
                        echo '';
                    }
                    exit();
        
            }


  
   public function attachment($id){

    if(!is_null($id))
    {
        $objSalesQuotation = DB::table("TBL_TRN_SLQT01_HDR")
                        ->where('SQID','=',$id)
                        ->select('TBL_TRN_SLQT01_HDR.*')
                        ->first(); 

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

                

            return view('transactions.sales.SalesQuotation.trnfrm36attachment',compact(['objSalesQuotation','objMstVoucherType','objAttachments']));
    }

}

    
   public function save(Request $request) {
    
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];

        $GROSS_TOTAL    =   0; 
        $NET_TOTAL      = $request['TotalValue'];
        $CGSTAMT        =   0; 
        $SGSTAMT        =   0; 
        $IGSTAMT        =   0; 
        $DISCOUNT       =   0; 
        $OTHER_CHARGES  =   0; 
        $TDS_AMOUNT     =   0; 


        
       
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]))
            {
                $GROSS_TOTAL+= $request['DISAFTT_AMT_'.$i]; 
                $CGSTAMT+= $request['CGSTAMT_'.$i]; 
                $SGSTAMT+= $request['SGSTAMT_'.$i]; 
                $IGSTAMT+= $request['IGSTAMT_'.$i]; 
                $DISCOUNT+= $request['DISCOUNT_AMT_'.$i]; 

                $req_data[$i] = [
                    'SEQID_REF'         => (!Empty($request['SEQID_REF_'.$i]) ? $request['SEQID_REF_'.$i] : "0") ,
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'MAIN_UOMID_REF'    => $request['MAIN_UOMID_REF_'.$i],
                    'SQ_QTY'            => $request['SQ_QTY_'.$i],
                    'ALT_UOMID_REF'     => $request['ALT_UOMID_REF_'.$i],
                    'RATEPUOM'          => $request['RATEPUOM_'.$i],
                    'DISCPER'           => (!Empty($request['DISCPER_'.$i]) ? $request['DISCPER_'.$i] : 0),
                    'DISCOUNT_AMT'      => (!Empty($request['DISCOUNT_AMT_'.$i]) ? $request['DISCOUNT_AMT_'.$i] : 0),
                    'IGST'              => (!Empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST'              => (!Empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST'              => (!Empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'SCHEMEID_REF'       => $request['SCHEMEID_REF_'.$i]!='' ? $request['SCHEMEID_REF_'.$i] :  NULL,
                    'ITEM_TYPE'          => $request['ITEM_TYPE_'.$i] !="" ? $request['ITEM_TYPE_'.$i] : "OTHER",   
                    'SCHEMEQTY' => (!empty($request['SCHEMEQTY_'.$i]) ? $request['SCHEMEQTY_'.$i] : 0),   
                ];
            }
        }
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        for ($i=0; $i<=$r_count2; $i++)
        {
                if(isset($request['TNCID_REF']) && !is_null($request['TNCID_REF']))
                {
                    if(isset($request['TNCDID_REF_'.$i]))
                    {
                        $reqdata2[$i] = [
                            'TNCID_REF'     => $request['TNCID_REF'] ,
                            'TNCDID_REF'    => $request['TNCDID_REF_'.$i],
                            'VALUE'         => $request['tncdetvalue_'.$i],
                        ];
                    }
                }
            
        }
           if(isset($reqdata2))
           { 
            $wrapped_links2["TNC"] = $reqdata2;
            $XMLTNC = ArrayToXml::convert($wrapped_links2);
           }
           else
           {
            $XMLTNC = NULL; 
           }   
            
        
        for ($i=0; $i<=$r_count3; $i++)
        {
                if(isset($request['UDFSQID_REF_'.$i]) || !is_null($request['UDFSQID_REF_'.$i]))
                {
                    $reqdata3[$i] = [
                        'UDFSQID_REF'   => $request['UDFSQID_REF_'.$i],
                        'SQUVALUE'      => $request['udfvalue_'.$i],
                    ];
                }
            
        }
        if(isset($reqdata3))
        { 
            $wrapped_links3["UDF"] = $reqdata3; 
            $XMLUDF = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $XMLUDF = NULL; 
        }
            
        
        for ($i=0; $i<=$r_count4; $i++)
        {
                if(isset($request['CTID_REF']) && !is_null($request['CTID_REF']))
                {
                    if(isset($request['CT_TYPE_'.$i]) && $request['CT_TYPE_'.$i]=="DISCOUNT"){
                          
                        $DISCOUNT      += $request['VALUE_'.$i]; 
                    }else{
                        $OTHER_CHARGES += $request['VALUE_'.$i];   
                    }

                    if(isset($request['TID_REF_'.$i]))
                    {
                        $reqdata4[$i] = [
                            'CTID_REF'      => $request['CTID_REF'] ,
                            'TID_REF'       => $request['TID_REF_'.$i],
                            'RATE'          => $request['RATE_'.$i],
                            'VALUE'         => $request['VALUE_'.$i],
                            'GST'           => (isset($request['calGST_'.$i])!="true" ? 0 : 1) ,
                            'IGST'          => (isset($request['calIGST_'.$i]) && !empty($request['calIGST_'.$i]) ? $request['calIGST_'.$i] : 0),
                            'CGST'          => (isset($request['calCGST_'.$i]) && !empty($request['calCGST_'.$i]) ? $request['calCGST_'.$i] : 0),
                            'SGST'          => (isset($request['calSGST_'.$i]) && !empty($request['calSGST_'.$i]) ? $request['calSGST_'.$i] : 0),
                            'ACTUAL'        => (isset($request['calACTUAL_'.$i])!="true" ? 0 : 1) ,
                        ];
                    }
                }
        }
        if(isset($reqdata4))
        { 
            $wrapped_links4["CAL"] = $reqdata4; 
            $XMLCAL = ArrayToXml::convert($wrapped_links4);
        }
        else
        {
            $XMLCAL = NULL; 
        }
            
        
        

            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $SQNO = $request['SQNO'];
            $SQDT = $request['SQDT'];
            $GLID_REF = trim($request['GLID_REF'])?trim($request['GLID_REF']):0;
            $SLID_REF = $request['SLID_REF'];
            $FC = (isset($request['FC'])!="true" ? 0 : 1);
            $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
            $CONVFACT = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
            $QVFDT = $request['QVFDT'];
            $QVTDT = $request['QVTDT'];
            $SPID_REF = $request['SPID_REF'];
            $REFNO = $request['REFNO'];
            $BILLTO = $request['BILLTO'];
            $SHIPTO = $request['SHIPTO'];
            $REMARKS = $request['REMARKS'];
            $LEAD_REF = $request['LEAD_REF'];
            $QUOTATIONTYPE = strtoupper($request['QUOTATIONTYPE']);
            $DEALERID_REF           = $request['DEALERID_REF'];
            $PROJECTID_REF          = $request['PROJECTID_REF'];
            $DEALER_COMMISSION_AMT  = $request['DEALER_COMMISSION_AMT'];
            $CUSTOMER_PROSPECT_TYPE = trim($request['CUSTOMER_TYPE'])?trim($request['CUSTOMER_TYPE']):NULL;

            $log_data = [ 
                $SQNO,$SQDT,$GLID_REF,$SLID_REF,$FC,$CRID_REF,$CONVFACT,$QVFDT,$QVTDT,$SPID_REF,$REFNO,$BILLTO,
                $SHIPTO,$REMARKS,$FYID_REF,$CYID_REF, $BRID_REF, $VTID_REF,$XMLMAT,$XMLTNC,$XMLUDF,$XMLCAL,
                $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS,$QUOTATIONTYPE,$LEAD_REF,$DEALERID_REF,$PROJECTID_REF,$DEALER_COMMISSION_AMT,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT,$CUSTOMER_PROSPECT_TYPE
            ];

           //dd($GLID_REF); 
            
            $sp_result = DB::select('EXEC SP_SQ_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $log_data);       
            
        
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            exit();  
     }


   

     
    public function edit($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){

            $objSQ = DB::table('TBL_TRN_SLQT01_HDR')
            ->where('TBL_TRN_SLQT01_HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_SLQT01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_SLQT01_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_SLQT01_HDR.SQID','=',$id)
            ->leftJoin('TBL_TRN_LEAD_GENERATION', 'TBL_TRN_SLQT01_HDR.LEADID_REF','=','TBL_TRN_LEAD_GENERATION.LEAD_ID')
            ->leftJoin('TBL_MST_PROSPECT', 'TBL_TRN_SLQT01_HDR.SLID_REF','=','TBL_MST_PROSPECT.PID')                
            ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_SLQT01_HDR.DEALERID_REF','=','TBL_MST_CUSTOMER.CID')
            ->leftJoin('TBL_MST_PROJECT', 'TBL_TRN_SLQT01_HDR.PROJECTID_REF','=','TBL_MST_PROJECT.PID')
            ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_SLQT01_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
            ->select('TBL_TRN_SLQT01_HDR.*','TBL_TRN_LEAD_GENERATION.LEAD_NO','TBL_MST_PROSPECT.PCODE','TBL_MST_PROSPECT.NAME','TBL_MST_CUSTOMER.NAME AS CUSTOMER_NAME','TBL_MST_CUSTOMER.COMMISION','TBL_MST_PROJECT.DESCRIPTIONS AS PROJECT_NAME','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
            ->first();

            // dd($objSQ); 
                           $objScheme  =     DB::select("SELECT DISTINCT M.SCHEMEID_REF,S.SCHEME_NAME FROM TBL_TRN_SLQT01_MAT M 
                           LEFT JOIN TBL_MST_SCHEME_HDR S ON S.SCHEMEID=M.SCHEMEID_REF
                           WHERE M.SQID_REF=$id"); 
                          
                           $SchemeId       =   NULL;
                           $SchemeName     =   NULL; 
                           $objSchemeId    =array();
                           $objSchemeName    =array();
               
                           if(!empty($objScheme)){
                               foreach($objScheme as $key=>$SchemeList){
                                   
                                   if($SchemeList->SCHEMEID_REF!=0 && $SchemeList->SCHEMEID_REF!=""){
                                   $objSchemeId[]=$SchemeList->SCHEMEID_REF;
                                   $objSchemeName[]=$SchemeList->SCHEME_NAME;
                                   }
                               }
               
               
                             
                               if(!empty($objSchemeId) && !empty($objSchemeName)){
                               $SchemeId =implode(",",$objSchemeId);
                               $SchemeName =implode(",",$objSchemeName);
                               }
                           }
               
                                  // dd($SchemeName); 
                        

            $log_data = [ 
                $id
            ];

            $objSQMAT   =   array();
            if(isset($objSQ) && !empty($objSQ)){
                $objSQMAT = DB::select('EXEC sp_get_sales_quotation_material ?', $log_data);
               
            }
            $objCount1 = count($objSQMAT);

            $objSQTNC = DB::table('TBL_TRN_SLQT01_TNC')                    
                        ->where('TBL_TRN_SLQT01_TNC.SQID_REF','=',$id)
                        ->select('TBL_TRN_SLQT01_TNC.*')
                        ->orderBy('TBL_TRN_SLQT01_TNC.SQTNCID','ASC')
                        ->get()->toArray();
            $objCount2 = count($objSQTNC);

            $objSQUDF = DB::table('TBL_TRN_SLQT01_UDF')                    
                             ->where('TBL_TRN_SLQT01_UDF.SQID_REF','=',$id)
                             ->select('TBL_TRN_SLQT01_UDF.*')
                             ->orderBy('TBL_TRN_SLQT01_UDF.SQUDFID','ASC')
                             ->get()->toArray();
            $objCount3 = count($objSQUDF);

            $objSQCAL = DB::table('TBL_TRN_SLQT01_CAL')                    
                        ->where('TBL_TRN_SLQT01_CAL.SQID_REF','=',$id)
                        ->select('TBL_TRN_SLQT01_CAL.*')
                        ->orderBy('TBL_TRN_SLQT01_CAL.SQCALID','ASC')
                        ->get()->toArray();
            $objCount4 = count($objSQCAL);

            
     
            $objRights = DB::table('TBL_MST_USERROLMAP')
			->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
			->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
			->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
			->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
			->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
			->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
			->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
			->first();

                             $TAXSTATE=[];
                             $objShpAddress=[] ;
                             $objBillAddress=[];

                            if(isset($objSQ->SHIPTO) && $objSQ->SHIPTO !=""){

                                $sid = $objSQ->SHIPTO;
                                $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_CUSTOMERLOCATION  
                                            WHERE  SHIPTO= ? AND CLID = ? ', [1,$sid]);
                    
                                $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  
                                WHERE BRID= ? ', [$BRID_REF]);
                                if($ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF)
                                {
                                    $TAXSTATE[] = 'WithinState';
                                }
                                else
                                {
                                    $TAXSTATE[] = 'OutofState';
                                }
                        
                                $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                                            WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                                            [$Status,$ObjSHIPTO[0]->CITYID_REF,$ObjSHIPTO[0]->CTRYID_REF,$ObjSHIPTO[0]->STID_REF]);
                        
                                $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                                            WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjSHIPTO[0]->STID_REF,$ObjSHIPTO[0]->CTRYID_REF]);
                        
                                $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                                            WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjSHIPTO[0]->CTRYID_REF]);
                        
                                $ObjAddressID = $ObjSHIPTO[0]->CLID;

                                if(!empty($ObjSHIPTO)){
                                $objShpAddress[] = $ObjSHIPTO[0]->CADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                                }

                            }

                            if(isset($objSQ->BILLTO) && $objSQ->BILLTO !=""){
                            
                                $bid = $objSQ->BILLTO;
                                $ObjBILLTO =  DB::select('SELECT top 1 * FROM TBL_MST_CUSTOMERLOCATION  
                                            WHERE BILLTO= ? AND CLID = ? ', [1,$bid]);
                    
                                
                                $ObjCity2 =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                                            WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                                            [$Status,$ObjBILLTO[0]->CITYID_REF,$ObjBILLTO[0]->CTRYID_REF,$ObjBILLTO[0]->STID_REF]);
                        
                                $ObjState2 =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                                            WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBILLTO[0]->STID_REF,$ObjBILLTO[0]->CTRYID_REF]);
                        
                                $ObjCountry2 =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                                            WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBILLTO[0]->CTRYID_REF]);
                        
                                $ObjAddressID = $ObjBILLTO[0]->CLID;

                                if(!empty($ObjBILLTO)){
                                    $objBillAddress[] = $ObjBILLTO[0]->CADD .' '.$ObjCity2[0]->NAME .' '.$ObjState2[0]->NAME .' '.$ObjCountry2[0]->NAME;
                                }

                            }

            $objSPID    =  array(); 
                                 
            if(isset($objSQ->SPID_REF) && $objSQ->SPID_REF !="" ){
            
                $objEMP = DB::table('TBL_MST_EMPLOYEE')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('STATUS','=',$Status)
                ->where('SALES_PERSON','=',1)
                ->where('EMPID','=',$objSQ->SPID_REF)
                ->select('TBL_MST_EMPLOYEE.*')
                ->first();
                $objSPID[] = $objEMP->EMPCODE.'-'.$objEMP->FNAME.' '.$objEMP->MNAME.' '.$objEMP->LNAME;

            }
            
         

            $objsubglcode=array();
            if(isset($objSQ->GLID_REF) && $objSQ->GLID_REF !=""){
                $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('GLID_REF','=',$objSQ->GLID_REF)
                ->where('SGLID','=',$objSQ->SLID_REF)
                ->select('TBL_MST_SUBLEDGER.*')
                ->first();
            }

            

      
        
           
           
            $objlastSQDT = $this->LastApprovedDocDate(); 

           

            $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
            WHERE  CYID_REF = ? AND BRID_REF = ? 
            order by CTCODE ASC', [$CYID_REF, $BRID_REF ]);
    
            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
                WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                order by TNC_CODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);
    
            $objCalculationHeader	=   Helper::getCalculationHeader(array(
                                        'CYID_REF'=>Auth::user()->CYID_REF,
                                        'BRID_REF'=>Session::get('BRID_REF'),
                                        'USERID'=>Auth::user()->USERID,
                                        'HEADING'=>'Transactions',
                                        'VTID_REF'=>$this->vtid_ref,
                                        'FORMID'=>$this->form_id
                                        ));
            
            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_SQ")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                                    {       
                                    $query->select('UDFSQID')->from('TBL_MST_UDFFOR_SQ')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF)
                                                    ->where('BRID_REF','=',$BRID_REF);
                                                                       
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF);
                                      
                    
    
            $objUdfSQData = DB::table('TBL_MST_UDFFOR_SQ')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_SQ")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                        {       
                        $query->select('UDFSQID')->from('TBL_MST_UDFFOR_SQ')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF)
                                        ->where('BRID_REF','=',$BRID_REF);
                                                        
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF);            
            

            $objUdfSQData2 = DB::table('TBL_MST_UDFFOR_SQ')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF2)
                ->get()->toArray(); 
    
        
    
            $objCurrencyconverter = DB::table('TBL_MST_CRCONVERSION')
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_CRCONVERSION.*')
            ->get()
            ->toArray();
    
            $objSalesPerson = DB::table('TBL_MST_EMPLOYEE')
            ->where('STATUS','=',$Status)
            ->where('SALES_PERSON','=','1')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->select('TBL_MST_EMPLOYEE.*')
            ->get()
            ->toArray();

            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
            ->get() ->toArray(); 

            $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')
            ->get() ->toArray(); 

            $FormId = $this->form_id;
        
            $AlpsStatus =   $this->AlpsStatus();

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
            $ActionStatus ="";
            $objothcurrency = $this->GetCurrencyMaster();
            
            $objSQCUST = DB::table('TBL_TRN_SLQT01_HDR')
            ->where('TBL_TRN_SLQT01_HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_SLQT01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_SLQT01_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_SLQT01_HDR.SQID','=',$id)
            ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_TRN_SLQT01_HDR.SLID_REF','=','TBL_MST_SUBLEDGER.SGLID')
            ->select('TBL_TRN_SLQT01_HDR.*','TBL_MST_SUBLEDGER.SGLID','TBL_MST_SUBLEDGER.SGLCODE AS CCODE','TBL_MST_SUBLEDGER.SLNAME AS NAME')
            ->first();


        return view('transactions.sales.SalesQuotation.trnfrm36edit',compact(['AlpsStatus','objSQ','objRights','objCount1','objSPID',
           'objCount2','objCount3','objCount4','objSQMAT','objSQCAL','objSQTNC','objSQUDF','objCalculationHeader',
           'objUdfSQData','objTNCHeader','objothcurrency','objSalesPerson',
           'objsubglcode','objShpAddress','objBillAddress',
           'objTNCDetails','objUdfSQData2','objCalHeader','objCalDetails','TAXSTATE','objlastSQDT','FormId','TabSetting','ActionStatus','SchemeId','SchemeName','objSQCUST']));
        }
     
       }

    public function view($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){

            $objSQ = DB::table('TBL_TRN_SLQT01_HDR')
                        ->where('TBL_TRN_SLQT01_HDR.FYID_REF','=',Session::get('FYID_REF'))
                        ->where('TBL_TRN_SLQT01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_TRN_SLQT01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_TRN_SLQT01_HDR.SQID','=',$id)
                        ->leftJoin('TBL_TRN_LEAD_GENERATION', 'TBL_TRN_SLQT01_HDR.LEADID_REF','=','TBL_TRN_LEAD_GENERATION.LEAD_ID')
                        ->leftJoin('TBL_MST_PROSPECT', 'TBL_TRN_SLQT01_HDR.SLID_REF','=','TBL_MST_PROSPECT.PID')                
                        ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_SLQT01_HDR.DEALERID_REF','=','TBL_MST_CUSTOMER.CID')
                        ->leftJoin('TBL_MST_PROJECT', 'TBL_TRN_SLQT01_HDR.PROJECTID_REF','=','TBL_MST_PROJECT.PID')
                        ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_SLQT01_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
                        ->select('TBL_TRN_SLQT01_HDR.*','TBL_TRN_LEAD_GENERATION.LEAD_NO','TBL_MST_PROSPECT.PCODE','TBL_MST_PROSPECT.NAME','TBL_MST_CUSTOMER.NAME AS CUSTOMER_NAME','TBL_MST_CUSTOMER.COMMISION','TBL_MST_PROJECT.DESCRIPTIONS AS PROJECT_NAME','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
                        ->first();


                        $objScheme  =     DB::select("SELECT DISTINCT M.SCHEMEID_REF,S.SCHEME_NAME FROM TBL_TRN_SLQT01_MAT M 
                        LEFT JOIN TBL_MST_SCHEME_HDR S ON S.SCHEMEID=M.SCHEMEID_REF
                        WHERE M.SQID_REF=$id"); 
                       
                        $SchemeId       =   NULL;
                        $SchemeName     =   NULL; 
                        $objSchemeId    =array();
                        $objSchemeName    =array();
            
                        if(!empty($objScheme)){
                            foreach($objScheme as $key=>$SchemeList){
                                
                                if($SchemeList->SCHEMEID_REF!=0 && $SchemeList->SCHEMEID_REF!=""){
                                $objSchemeId[]=$SchemeList->SCHEMEID_REF;
                                $objSchemeName[]=$SchemeList->SCHEME_NAME;
                                }
                            }
            
            
                          
                            if(!empty($objSchemeId) && !empty($objSchemeName)){
                            $SchemeId =implode(",",$objSchemeId);
                            $SchemeName =implode(",",$objSchemeName);
                            }
                        }
            
                               // dd($SchemeName); 
                        

            $log_data = [ 
                $id
            ];

            $objSQMAT   =   array();
            if(isset($objSQ) && !empty($objSQ)){
                $objSQMAT = DB::select('EXEC sp_get_sales_quotation_material ?', $log_data);
               
            }
            $objCount1 = count($objSQMAT);

            $objSQTNC = DB::table('TBL_TRN_SLQT01_TNC')                    
                        ->where('TBL_TRN_SLQT01_TNC.SQID_REF','=',$id)
                        ->select('TBL_TRN_SLQT01_TNC.*')
                        ->orderBy('TBL_TRN_SLQT01_TNC.SQTNCID','ASC')
                        ->get()->toArray();
            $objCount2 = count($objSQTNC);

            $objSQUDF = DB::table('TBL_TRN_SLQT01_UDF')                    
                             ->where('TBL_TRN_SLQT01_UDF.SQID_REF','=',$id)
                             ->select('TBL_TRN_SLQT01_UDF.*')
                             ->orderBy('TBL_TRN_SLQT01_UDF.SQUDFID','ASC')
                             ->get()->toArray();
            $objCount3 = count($objSQUDF);

            $objSQCAL = DB::table('TBL_TRN_SLQT01_CAL')                    
                        ->where('TBL_TRN_SLQT01_CAL.SQID_REF','=',$id)
                        ->select('TBL_TRN_SLQT01_CAL.*')
                        ->orderBy('TBL_TRN_SLQT01_CAL.SQCALID','ASC')
                        ->get()->toArray();
            $objCount4 = count($objSQCAL);

            
     
            $objRights = DB::table('TBL_MST_USERROLMAP')
			->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
			->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
			->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
			->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
			->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
			->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
			->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
			->first();

                             $TAXSTATE=[];
                             $objShpAddress=[] ;
                             $objBillAddress=[];

                            if(isset($objSQ->SHIPTO) && $objSQ->SHIPTO !=""){

                                $sid = $objSQ->SHIPTO;
                                $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_CUSTOMERLOCATION  
                                            WHERE  SHIPTO= ? AND CLID = ? ', [1,$sid]);
                    
                                $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  
                                WHERE BRID= ? ', [$BRID_REF]);
                                if($ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF)
                                {
                                    $TAXSTATE[] = 'WithinState';
                                }
                                else
                                {
                                    $TAXSTATE[] = 'OutofState';
                                }
                        
                                $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                                            WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                                            [$Status,$ObjSHIPTO[0]->CITYID_REF,$ObjSHIPTO[0]->CTRYID_REF,$ObjSHIPTO[0]->STID_REF]);
                        
                                $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                                            WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjSHIPTO[0]->STID_REF,$ObjSHIPTO[0]->CTRYID_REF]);
                        
                                $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                                            WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjSHIPTO[0]->CTRYID_REF]);
                        
                                $ObjAddressID = $ObjSHIPTO[0]->CLID;

                                if(!empty($ObjSHIPTO)){
                                $objShpAddress[] = $ObjSHIPTO[0]->CADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                                }

                            }

                            if(isset($objSQ->BILLTO) && $objSQ->BILLTO !=""){
                            
                                $bid = $objSQ->BILLTO;
                                $ObjBILLTO =  DB::select('SELECT top 1 * FROM TBL_MST_CUSTOMERLOCATION  
                                            WHERE BILLTO= ? AND CLID = ? ', [1,$bid]);
                    
                                
                                $ObjCity2 =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                                            WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                                            [$Status,$ObjBILLTO[0]->CITYID_REF,$ObjBILLTO[0]->CTRYID_REF,$ObjBILLTO[0]->STID_REF]);
                        
                                $ObjState2 =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                                            WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBILLTO[0]->STID_REF,$ObjBILLTO[0]->CTRYID_REF]);
                        
                                $ObjCountry2 =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                                            WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBILLTO[0]->CTRYID_REF]);
                        
                                $ObjAddressID = $ObjBILLTO[0]->CLID;

                                if(!empty($ObjBILLTO)){
                                    $objBillAddress[] = $ObjBILLTO[0]->CADD .' '.$ObjCity2[0]->NAME .' '.$ObjState2[0]->NAME .' '.$ObjCountry2[0]->NAME;
                                }

                            }

            $objSPID    =  array(); 
                                 
            if(isset($objSQ->SPID_REF) && $objSQ->SPID_REF !="" ){
            
                $objEMP = DB::table('TBL_MST_EMPLOYEE')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('STATUS','=',$Status)
                ->where('SALES_PERSON','=',1)
                ->where('EMPID','=',$objSQ->SPID_REF)
                ->select('TBL_MST_EMPLOYEE.*')
                ->first();
                $objSPID[] = $objEMP->EMPCODE.'-'.$objEMP->FNAME.' '.$objEMP->MNAME.' '.$objEMP->LNAME;

            }
            
            $objsubglcode=array();            
            if(isset($objSQ->GLID_REF) && $objSQ->GLID_REF !=""){
                $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('GLID_REF','=',$objSQ->GLID_REF)
                ->where('SGLID','=',$objSQ->SLID_REF)
                ->select('TBL_MST_SUBLEDGER.*')
                ->first();
            }

          


           
            
           
           
            $objlastSQDT = $this->LastApprovedDocDate(); 

           

            $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
            WHERE  CYID_REF = ? AND BRID_REF = ? 
            order by CTCODE ASC', [$CYID_REF, $BRID_REF ]);
    
            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
                WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                order by TNC_CODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);
    
            $objCalculationHeader	=   Helper::getCalculationHeader(array(
                                        'CYID_REF'=>Auth::user()->CYID_REF,
                                        'BRID_REF'=>Session::get('BRID_REF'),
                                        'USERID'=>Auth::user()->USERID,
                                        'HEADING'=>'Transactions',
                                        'VTID_REF'=>$this->vtid_ref,
                                        'FORMID'=>$this->form_id
                                        ));
            
            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_SQ")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                                    {       
                                    $query->select('UDFSQID')->from('TBL_MST_UDFFOR_SQ')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF)
                                                    ->where('BRID_REF','=',$BRID_REF);
                                                                       
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF);
                                      
                    
    
            $objUdfSQData = DB::table('TBL_MST_UDFFOR_SQ')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_SQ")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                        {       
                        $query->select('UDFSQID')->from('TBL_MST_UDFFOR_SQ')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF)
                                        ->where('BRID_REF','=',$BRID_REF);
                                                        
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF);            
            

            $objUdfSQData2 = DB::table('TBL_MST_UDFFOR_SQ')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF2)
                ->get()->toArray(); 
    
        
    
            $objCurrencyconverter = DB::table('TBL_MST_CRCONVERSION')
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_CRCONVERSION.*')
            ->get()
            ->toArray();
    
            $objSalesPerson = DB::table('TBL_MST_EMPLOYEE')
            ->where('STATUS','=',$Status)
            ->where('SALES_PERSON','=','1')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->select('TBL_MST_EMPLOYEE.*')
            ->get()
            ->toArray();

            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
            ->get() ->toArray(); 

            $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')
            ->get() ->toArray(); 

            $FormId = $this->form_id;
        
            $AlpsStatus =   $this->AlpsStatus();

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
            $ActionStatus ="disabled";


            $objothcurrency = $this->GetCurrencyMaster(); 

            $objSQCUST = DB::table('TBL_TRN_SLQT01_HDR')
            ->where('TBL_TRN_SLQT01_HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_SLQT01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_SLQT01_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_SLQT01_HDR.SQID','=',$id)
            ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_TRN_SLQT01_HDR.SLID_REF','=','TBL_MST_SUBLEDGER.SGLID')
            ->select('TBL_TRN_SLQT01_HDR.*','TBL_MST_SUBLEDGER.SGLID','TBL_MST_SUBLEDGER.SGLCODE AS CCODE','TBL_MST_SUBLEDGER.SLNAME AS NAME')
            ->first();
            
        return view('transactions.sales.SalesQuotation.trnfrm36edit',compact(['AlpsStatus','objSQ','objRights','objCount1','objSPID',
           'objCount2','objCount3','objCount4','objSQMAT','objSQCAL','objSQTNC','objSQUDF','objCalculationHeader',
           'objUdfSQData','objTNCHeader','objothcurrency','objSalesPerson',
           'objsubglcode','objShpAddress','objBillAddress',
           'objTNCDetails','objUdfSQData2','objCalHeader','objCalDetails','TAXSTATE','objlastSQDT','FormId','TabSetting','ActionStatus','SchemeId','SchemeName','objSQCUST']));
        }
     
       }
            
  
   public function update(Request $request){

        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];

        $GROSS_TOTAL    =   0; 
        $NET_TOTAL 		= $request['TotalValue'];
        $CGSTAMT        =   0; 
        $SGSTAMT        =   0; 
        $IGSTAMT        =   0; 
        $DISCOUNT       =   0; 
        $OTHER_CHARGES  =   0; 
        $TDS_AMOUNT     =   0; 
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]))
            {

                $GROSS_TOTAL+= $request['DISAFTT_AMT_'.$i]; 
                $CGSTAMT+= $request['CGSTAMT_'.$i]; 
                $SGSTAMT+= $request['SGSTAMT_'.$i]; 
                $IGSTAMT+= $request['IGSTAMT_'.$i]; 
                $DISCOUNT+= $request['DISCOUNT_AMT_'.$i]; 


                $req_data[$i] = [
                    'SEQID_REF' => (isset($request['SEQID_REF_'.$i]) ? $request['SEQID_REF_'.$i] : "0") ,
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'MAIN_UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'SQ_QTY' => $request['SQ_QTY_'.$i],
                    'ALT_UOMID_REF' => $request['ALT_UOMID_REF_'.$i],
                    'RATEPUOM' => $request['RATEPUOM_'.$i],
                    'DISCPER'    => (!Empty($request['DISCPER_'.$i]) ? $request['DISCPER_'.$i] : 0),
                    'DISCOUNT_AMT' => (!Empty($request['DISCOUNT_AMT_'.$i]) ? $request['DISCOUNT_AMT_'.$i] : 0),
                    'IGST' => (!Empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!Empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!Empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'SCHEMEID_REF'       => $request['SCHEMEID_REF_'.$i]!='' ? $request['SCHEMEID_REF_'.$i] :  NULL,
                    'ITEM_TYPE'          => $request['ITEM_TYPE_'.$i] !="" ? $request['ITEM_TYPE_'.$i] : "OTHER",   
                    'SCHEMEQTY' => (!empty($request['SCHEMEQTY_'.$i]) ? $request['SCHEMEQTY_'.$i] : 0),   
                ];
            }
        }
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        
            for ($i=0; $i<=$r_count2; $i++)
            {
                    if(isset($request['TNCID_REF']) && !is_null($request['TNCID_REF']))
                    {
                        if(isset($request['TNCDID_REF_'.$i]))
                        {
                            $reqdata2[$i] = [
                                'TNCID_REF'     => $request['TNCID_REF'] ,
                                'TNCDID_REF'    => $request['TNCDID_REF_'.$i],
                                'VALUE'         => $request['tncdetvalue_'.$i],
                            ];
                        }
                    }
                
            }
               if(isset($reqdata2))
               { 
                $wrapped_links2["TNC"] = $reqdata2;
                $XMLTNC = ArrayToXml::convert($wrapped_links2);
               }
               else
               {
                $XMLTNC = NULL; 
               }   
                
            
            for ($i=0; $i<=$r_count3; $i++)
            {
                    if(isset($request['UDFSQID_REF_'.$i]) || !is_null($request['UDFSQID_REF_'.$i]))
                    {
                        $reqdata3[$i] = [
                            'UDFSQID_REF'   => $request['UDFSQID_REF_'.$i],
                            'SQUVALUE'      => $request['udfvalue_'.$i],
                        ];
                    }
                
            }
            if(isset($reqdata3))
            { 
                $wrapped_links3["UDF"] = $reqdata3; 
                $XMLUDF = ArrayToXml::convert($wrapped_links3);
            }
            else
            {
                $XMLUDF = NULL; 
            }
                
            
            for ($i=0; $i<=$r_count4; $i++)
            {
                    if(isset($request['CTID_REF']) && !is_null($request['CTID_REF']))
                    { 
                        if(isset($request['CT_TYPE_'.$i]) && $request['CT_TYPE_'.$i]=="DISCOUNT"){
                          
                            $DISCOUNT      += $request['VALUE_'.$i]; 
                        }else{
                            $OTHER_CHARGES += $request['VALUE_'.$i];   
                        }

                        if(isset($request['TID_REF_'.$i]))
                        {
                            $reqdata4[$i] = [
                                'CTID_REF'      => $request['CTID_REF'] ,
                                'TID_REF'       => $request['TID_REF_'.$i],
                                'RATE'          => $request['RATE_'.$i],
                                'VALUE'         => $request['VALUE_'.$i],
                                'GST'           => (isset($request['calGST_'.$i])!="true" ? 0 : 1) ,
                                'IGST'          => (isset($request['calIGST_'.$i]) && !empty($request['calIGST_'.$i]) ? $request['calIGST_'.$i] : 0),
                                'CGST'          => (isset($request['calCGST_'.$i]) && !empty($request['calCGST_'.$i]) ? $request['calCGST_'.$i] : 0),
                                'SGST'          => (isset($request['calSGST_'.$i]) && !empty($request['calSGST_'.$i]) ? $request['calSGST_'.$i] : 0),
                                'ACTUAL'        => (isset($request['calACTUAL_'.$i])!="true" ? 0 : 1) ,
                            ];
                        }
                    }
                
            }
            if(isset($reqdata4))
            { 
                $wrapped_links4["CAL"] = $reqdata4; 
                $XMLCAL = ArrayToXml::convert($wrapped_links4);
            }
            else
            {
                $XMLCAL = NULL; 
            }
        
        

            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'EDIT';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $SQNO = $request['SQNO'];
            $SQDT = $request['SQDT'];
            $GLID_REF = trim($request['GLID_REF'])?trim($request['GLID_REF']):0;
            $SLID_REF = $request['SLID_REF'];
            $FC = (isset($request['FC'])!="true" ? 0 : 1);
            $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
            $CONVFACT = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
            $QVFDT = $request['QVFDT'];
            $QVTDT = $request['QVTDT'];
            $SPID_REF = $request['SPID_REF'];
            $REFNO = $request['REFNO'];
            $BILLTO = $request['BILLTO'];
            $SHIPTO = $request['SHIPTO'];
            $REMARKS = $request['REMARKS'];
            $LEAD_REF = $request['LEAD_REF'];
            $QUOTATIONTYPE = strtoupper($request['QUOTATIONTYPE']);

            $DEALERID_REF           = $request['DEALERID_REF'];
            $PROJECTID_REF          = $request['PROJECTID_REF'];
            $DEALER_COMMISSION_AMT  = $request['DEALER_COMMISSION_AMT'];
            $CUSTOMER_PROSPECT_TYPE = trim($request['CUSTOMER_TYPE'])?trim($request['CUSTOMER_TYPE']):NULL;

            $log_data = [ 
                $SQNO,$SQDT,$GLID_REF,$SLID_REF,$FC,$CRID_REF,$CONVFACT,$QVFDT,$QVTDT,$SPID_REF,$REFNO,$BILLTO,
                $SHIPTO,$REMARKS,$FYID_REF,$CYID_REF, $BRID_REF, $VTID_REF,$XMLMAT,$XMLTNC,$XMLUDF,$XMLCAL,
                $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS,$QUOTATIONTYPE,$LEAD_REF,$DEALERID_REF,$PROJECTID_REF,$DEALER_COMMISSION_AMT,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT,$CUSTOMER_PROSPECT_TYPE
            ];

            
            $sp_result = DB::select('EXEC SP_SQ_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $log_data);       
            
        //dd($sp_result); 
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

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
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];

        $GROSS_TOTAL    =   0; 
        $NET_TOTAL 		= $request['TotalValue'];
        $CGSTAMT        =   0; 
        $SGSTAMT        =   0; 
        $IGSTAMT        =   0; 
        $DISCOUNT       =   0; 
        $OTHER_CHARGES  =   0; 
        $TDS_AMOUNT     =   0; 
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]))
            {
                $GROSS_TOTAL+= $request['DISAFTT_AMT_'.$i]; 
                $CGSTAMT+= $request['CGSTAMT_'.$i]; 
                $SGSTAMT+= $request['SGSTAMT_'.$i]; 
                $IGSTAMT+= $request['IGSTAMT_'.$i]; 
                $DISCOUNT+= $request['DISCOUNT_AMT_'.$i]; 

                $req_data[$i] = [
                    'SEQID_REF' => (isset($request['SEQID_REF_'.$i]) ? $request['SEQID_REF_'.$i] : "0") ,
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'MAIN_UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'SQ_QTY' => $request['SQ_QTY_'.$i],
                    'ALT_UOMID_REF' => $request['ALT_UOMID_REF_'.$i],
                    'RATEPUOM' => $request['RATEPUOM_'.$i],
                    'DISCPER'    => (!Empty($request['DISCPER_'.$i]) ? $request['DISCPER_'.$i] : 0),
                    'DISCOUNT_AMT' => (!Empty($request['DISCOUNT_AMT_'.$i]) ? $request['DISCOUNT_AMT_'.$i] : 0),
                    'IGST' => (!Empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!Empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!Empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'SCHEMEID_REF'       => $request['SCHEMEID_REF_'.$i]!='' ? $request['SCHEMEID_REF_'.$i] :  NULL,
                    'ITEM_TYPE'          => $request['ITEM_TYPE_'.$i] !="" ? $request['ITEM_TYPE_'.$i] : "OTHER",   
                    'SCHEMEQTY' => (!empty($request['SCHEMEQTY_'.$i]) ? $request['SCHEMEQTY_'.$i] : 0),   
                ];
            }
        }
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        
            for ($i=0; $i<=$r_count2; $i++)
            {
                    if(isset($request['TNCID_REF']) && !is_null($request['TNCID_REF']))
                    {
                        if(isset($request['TNCDID_REF_'.$i]))
                        {
                            $reqdata2[$i] = [
                                'TNCID_REF'     => $request['TNCID_REF'] ,
                                'TNCDID_REF'    => $request['TNCDID_REF_'.$i],
                                'VALUE'         => $request['tncdetvalue_'.$i],
                            ];
                        }
                    }
                
            }
               if(isset($reqdata2))
               { 
                $wrapped_links2["TNC"] = $reqdata2;
                $XMLTNC = ArrayToXml::convert($wrapped_links2);
               }
               else
               {
                $XMLTNC = NULL; 
               }   
                
            
            for ($i=0; $i<=$r_count3; $i++)
            {
                    if(isset($request['UDFSQID_REF_'.$i]) || !is_null($request['UDFSQID_REF_'.$i]))
                    {
                        $reqdata3[$i] = [
                            'UDFSQID_REF'   => $request['UDFSQID_REF_'.$i],
                            'SQUVALUE'      => $request['udfvalue_'.$i],
                        ];
                    }
                
            }
            if(isset($reqdata3))
            { 
                $wrapped_links3["UDF"] = $reqdata3; 
                $XMLUDF = ArrayToXml::convert($wrapped_links3);
            }
            else
            {
                $XMLUDF = NULL; 
            }
                
            
            for ($i=0; $i<=$r_count4; $i++)
            {
                    if(isset($request['CTID_REF']) && !is_null($request['CTID_REF']))
                    {
                        if(isset($request['CT_TYPE_'.$i]) && $request['CT_TYPE_'.$i]=="DISCOUNT"){
                          
                            $DISCOUNT      += $request['VALUE_'.$i]; 
                        }else{
                            $OTHER_CHARGES += $request['VALUE_'.$i];   
                        }


                        if(isset($request['TID_REF_'.$i]))
                        {
                            $reqdata4[$i] = [
                                'CTID_REF'      => $request['CTID_REF'] ,
                                'TID_REF'       => $request['TID_REF_'.$i],
                                'RATE'          => $request['RATE_'.$i],
                                'VALUE'         => $request['VALUE_'.$i],
                                'GST'           => (isset($request['calGST_'.$i])!="true" ? 0 : 1) ,
                                'IGST'          => (isset($request['calIGST_'.$i]) && !empty($request['calIGST_'.$i]) ? $request['calIGST_'.$i] : 0),
                                'CGST'          => (isset($request['calCGST_'.$i]) && !empty($request['calCGST_'.$i]) ? $request['calCGST_'.$i] : 0),
                                'SGST'          => (isset($request['calSGST_'.$i]) && !empty($request['calSGST_'.$i]) ? $request['calSGST_'.$i] : 0),
                                'ACTUAL'        => (isset($request['calACTUAL_'.$i])!="true" ? 0 : 1) ,
                            ];
                        }
                    }
                
            }
            if(isset($reqdata4))
            { 
                $wrapped_links4["CAL"] = $reqdata4; 
                $XMLCAL = ArrayToXml::convert($wrapped_links4);
            }
            else
            {
                $XMLCAL = NULL; 
            }
        
        

            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = $Approvallevel;
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $SQNO = $request['SQNO'];
            $SQDT = $request['SQDT'];
            $GLID_REF = trim($request['GLID_REF'])?trim($request['GLID_REF']):0;
            $SLID_REF = $request['SLID_REF'];
            $FC = (isset($request['FC'])!="true" ? 0 : 1);
            $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
            $CONVFACT = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
            $QVFDT = $request['QVFDT'];
            $QVTDT = $request['QVTDT'];
            $SPID_REF = $request['SPID_REF'];
            $REFNO = $request['REFNO'];
            $BILLTO = $request['BILLTO'];
            $SHIPTO = $request['SHIPTO'];
            $REMARKS = $request['REMARKS'];
            $LEAD_REF = $request['LEAD_REF'];
            $QUOTATIONTYPE = strtoupper($request['QUOTATIONTYPE']);
            $DEALERID_REF           = $request['DEALERID_REF'];
            $PROJECTID_REF          = $request['PROJECTID_REF'];
            $DEALER_COMMISSION_AMT  = $request['DEALER_COMMISSION_AMT'];
            $CUSTOMER_PROSPECT_TYPE = trim($request['CUSTOMER_TYPE'])?trim($request['CUSTOMER_TYPE']):NULL;

            $log_data = [ 
                $SQNO,$SQDT,$GLID_REF,$SLID_REF,$FC,$CRID_REF,$CONVFACT,$QVFDT,$QVTDT,$SPID_REF,$REFNO,$BILLTO,
                $SHIPTO,$REMARKS,$FYID_REF,$CYID_REF, $BRID_REF, $VTID_REF,$XMLMAT,$XMLTNC,$XMLUDF,$XMLCAL,
                $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS,$QUOTATIONTYPE,$LEAD_REF,$DEALERID_REF,$PROJECTID_REF,$DEALER_COMMISSION_AMT,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT,$CUSTOMER_PROSPECT_TYPE
            ];

            
            $sp_result = DB::select('EXEC SP_SQ_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $log_data);       
            
        
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

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
                $TABLE      =   "TBL_TRN_SLQT01_HDR";
                $FIELD      =   "SQID";
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
        $TABLE      =   "TBL_TRN_SLQT01_HDR";
        $FIELD      =   "SQID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
        
        $req_data[0]=[
            'NT'  => 'TBL_TRN_SLQT01_MAT',
           ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_SLQT01_TNC',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_SLQT01_UDF',
            ];
        $req_data[3]=[
            'NT'  => 'TBL_TRN_SLQT01_CAL',
        ];
        $req_data[4]=[
            'NT'  => 'TBL_TRN_SLQT02_HDR',
        ];

        
           
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);

      

        $SalesQuotation_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];
       
       

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_SQ  ?,?,?,?, ?,?,?,?, ?,?,?,?', $SalesQuotation_cancel_data);

       
        if($sp_result[0]->RESULT=="CANCELED"){  

            return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
        
            return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'invalid'=>'invalid']);
        }
        
        exit(); 
    }

  
  

   

   public function docuploads(Request $request){

    $formData = $request->all();

    $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
    $allow_size = config("erpconst.attachments.max_size") * 1024 * 1024;

  
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
    
    $image_path         =   "docs/company".$CYID_REF."/SalesQuotation";     
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
                
                echo $filenametostore ;

                if ($uploadedFile->isValid()) {

                    if(in_array($extension,$allow_extnesions)){
                        
                        if($filesize < $allow_size){

                            $custfilename = $destinationPath."/".$filenametostore;

                            if (!file_exists($custfilename)) {

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
        return redirect()->route("transaction",[36,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
    
      
   try {

       
         $sp_result = DB::select('EXEC SP_TRN_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);

   } catch (\Throwable $th) {
    
       return redirect()->route("transaction",[36,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("transaction",[36,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("transaction",[36,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("transaction",[36,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

    public function checksq(Request $request){

       
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $SQNO = $request->SQNO;
        
        $objSO = DB::table('TBL_TRN_SLQT01_HDR')
        ->where('TBL_TRN_SLQT01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLQT01_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_SLQT01_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_SLQT01_HDR.SQNO','=',$SQNO)
        ->select('TBL_TRN_SLQT01_HDR.SQID')
        ->first();
        
        if($objSO){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate SONO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }

  

    public function amendment($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){
            $objSQ = DB::table('TBL_TRN_SLQT01_HDR')
            ->where('TBL_TRN_SLQT01_HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_SLQT01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_SLQT01_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_SLQT01_HDR.SQID','=',$id)
            ->leftJoin('TBL_TRN_LEAD_GENERATION', 'TBL_TRN_SLQT01_HDR.LEADID_REF','=','TBL_TRN_LEAD_GENERATION.LEAD_ID')
            ->leftJoin('TBL_MST_PROSPECT', 'TBL_TRN_SLQT01_HDR.SLID_REF','=','TBL_MST_PROSPECT.PID')                
            ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_SLQT01_HDR.DEALERID_REF','=','TBL_MST_CUSTOMER.CID')
            ->leftJoin('TBL_MST_PROJECT', 'TBL_TRN_SLQT01_HDR.PROJECTID_REF','=','TBL_MST_PROJECT.PID')
            ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_SLQT01_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
            ->select('TBL_TRN_SLQT01_HDR.*','TBL_TRN_LEAD_GENERATION.LEAD_NO','TBL_MST_PROSPECT.PCODE','TBL_MST_PROSPECT.NAME','TBL_MST_CUSTOMER.NAME AS CUSTOMER_NAME','TBL_MST_CUSTOMER.COMMISION','TBL_MST_PROJECT.DESCRIPTIONS AS PROJECT_NAME','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
            ->first();



            $objScheme  =     DB::select("SELECT DISTINCT M.SCHEMEID_REF,S.SCHEME_NAME FROM TBL_TRN_SLQT01_MAT M 
            LEFT JOIN TBL_MST_SCHEME_HDR S ON S.SCHEMEID=M.SCHEMEID_REF
            WHERE M.SQID_REF=$id"); 
           
            $SchemeId       =   NULL;
            $SchemeName     =   NULL; 
            $objSchemeId    =array();
            $objSchemeName    =array();

            if(!empty($objScheme)){
                foreach($objScheme as $key=>$SchemeList){
                    
                    if($SchemeList->SCHEMEID_REF!=0 && $SchemeList->SCHEMEID_REF!=""){
                    $objSchemeId[]=$SchemeList->SCHEMEID_REF;
                    $objSchemeName[]=$SchemeList->SCHEME_NAME;
                    }
                }


              
                if(!empty($objSchemeId) && !empty($objSchemeName)){
                $SchemeId =implode(",",$objSchemeId);
                $SchemeName =implode(",",$objSchemeName);
                }
            }

                   // dd($SchemeName); 

            $log_data = [ 
                $id
            ];



            $objSQMAT   =   array();
            if(isset($objSQ) && !empty($objSQ)){
                $objSQMAT = DB::select('EXEC sp_get_sales_quotation_material ?', $log_data);
               
            }
            $objCount1 = count($objSQMAT);

            $MAXSQANO   =   "";
            if(isset($objSQ) && !empty($objSQ)){
            
                $objSQANO = DB::SELECT("select  MAX(isnull(ANO,0))+1  AS ANO from TBL_TRN_SLQT02_HDR  WHERE SQID_REF=? AND SQANO=?",[$objSQ->SQID,$objSQ->SQNO]);
                $MAXSQANO = $objSQANO[0]->ANO;

            }

            $objSQTNC = DB::table('TBL_TRN_SLQT01_TNC')                    
                        ->where('TBL_TRN_SLQT01_TNC.SQID_REF','=',$id)
                        ->select('TBL_TRN_SLQT01_TNC.*')
                        ->orderBy('TBL_TRN_SLQT01_TNC.SQTNCID','ASC')
                        ->get()->toArray();
            $objCount2 = count($objSQTNC);

            $objSQUDF = DB::table('TBL_TRN_SLQT01_UDF')                    
                             ->where('TBL_TRN_SLQT01_UDF.SQID_REF','=',$id)
                             ->select('TBL_TRN_SLQT01_UDF.*')
                             ->orderBy('TBL_TRN_SLQT01_UDF.SQUDFID','ASC')
                             ->get()->toArray();
            $objCount3 = count($objSQUDF);

            $objSQCAL = DB::table('TBL_TRN_SLQT01_CAL')                    
                        ->where('TBL_TRN_SLQT01_CAL.SQID_REF','=',$id)
                        ->select('TBL_TRN_SLQT01_CAL.*')
                        ->orderBy('TBL_TRN_SLQT01_CAL.SQCALID','ASC')
                        ->get()->toArray();
            $objCount4 = count($objSQCAL);

            
     
            $objRights = DB::table('TBL_MST_USERROLMAP')
			->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
			->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
			->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
			->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
			->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
			->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
			->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
			->first();

                             $TAXSTATE=[];
                             $objShpAddress=[] ;
                             $objBillAddress=[];

                            if(isset($objSQ->SHIPTO) && $objSQ->SHIPTO !=""){

                                $sid = $objSQ->SHIPTO;
                                $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_CUSTOMERLOCATION  
                                            WHERE  SHIPTO= ? AND CLID = ? ', [1,$sid]);
                    
                                $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  
                                WHERE BRID= ? ', [$BRID_REF]);
                                if($ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF)
                                {
                                    $TAXSTATE[] = 'WithinState';
                                }
                                else
                                {
                                    $TAXSTATE[] = 'OutofState';
                                }
                        
                                $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                                            WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                                            [$Status,$ObjSHIPTO[0]->CITYID_REF,$ObjSHIPTO[0]->CTRYID_REF,$ObjSHIPTO[0]->STID_REF]);
                        
                                $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                                            WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjSHIPTO[0]->STID_REF,$ObjSHIPTO[0]->CTRYID_REF]);
                        
                                $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                                            WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjSHIPTO[0]->CTRYID_REF]);
                        
                                $ObjAddressID = $ObjSHIPTO[0]->CLID;

                                if(!empty($ObjSHIPTO)){
                                $objShpAddress[] = $ObjSHIPTO[0]->CADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                                }

                            }

                            if(isset($objSQ->BILLTO) && $objSQ->BILLTO !=""){
                            
                                $bid = $objSQ->BILLTO;
                                $ObjBILLTO =  DB::select('SELECT top 1 * FROM TBL_MST_CUSTOMERLOCATION  
                                            WHERE BILLTO= ? AND CLID = ? ', [1,$bid]);
                    
                                
                                $ObjCity2 =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                                            WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                                            [$Status,$ObjBILLTO[0]->CITYID_REF,$ObjBILLTO[0]->CTRYID_REF,$ObjBILLTO[0]->STID_REF]);
                        
                                $ObjState2 =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                                            WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBILLTO[0]->STID_REF,$ObjBILLTO[0]->CTRYID_REF]);
                        
                                $ObjCountry2 =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                                            WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBILLTO[0]->CTRYID_REF]);
                        
                                $ObjAddressID = $ObjBILLTO[0]->CLID;

                                if(!empty($ObjBILLTO)){
                                    $objBillAddress[] = $ObjBILLTO[0]->CADD .' '.$ObjCity2[0]->NAME .' '.$ObjState2[0]->NAME .' '.$ObjCountry2[0]->NAME;
                                }

                            }

            $objSPID    =  array(); 
                                 
            if(isset($objSQ->SPID_REF) && $objSQ->SPID_REF !="" ){
            
                $objEMP = DB::table('TBL_MST_EMPLOYEE')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('STATUS','=',$Status)
                ->where('SALES_PERSON','=',1)
                ->where('EMPID','=',$objSQ->SPID_REF)
                ->select('TBL_MST_EMPLOYEE.*')
                ->first();
                $objSPID[] = $objEMP->EMPCODE.'-'.$objEMP->FNAME.' '.$objEMP->MNAME.' '.$objEMP->LNAME;

            }
            
            $objsubglcode=array();
            if(isset($objSQ->GLID_REF) && $objSQ->GLID_REF !=""){

                $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('GLID_REF','=',$objSQ->GLID_REF)
                ->where('SGLID','=',$objSQ->SLID_REF)
                ->select('TBL_MST_SUBLEDGER.*')
                ->first();

            }

          


           
            
           
           
            $objlastSQDT = $this->LastApprovedDocDate(); 

           

            $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
            WHERE  CYID_REF = ? AND BRID_REF = ? 
            order by CTCODE ASC', [$CYID_REF, $BRID_REF ]);
    
            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
                WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                order by TNC_CODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);
    
            $objCalculationHeader	=   Helper::getCalculationHeader(array(
                                        'CYID_REF'=>Auth::user()->CYID_REF,
                                        'BRID_REF'=>Session::get('BRID_REF'),
                                        'USERID'=>Auth::user()->USERID,
                                        'HEADING'=>'Transactions',
                                        'VTID_REF'=>$this->vtid_ref,
                                        'FORMID'=>$this->form_id
                                        ));
            
            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_SQ")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                                    {       
                                    $query->select('UDFSQID')->from('TBL_MST_UDFFOR_SQ')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF)
                                                    ->where('BRID_REF','=',$BRID_REF);
                                                                   
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF);
                                       
                    
    
            $objUdfSQData = DB::table('TBL_MST_UDFFOR_SQ')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_SQ")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                        {       
                        $query->select('UDFSQID')->from('TBL_MST_UDFFOR_SQ')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF)
                                        ->where('BRID_REF','=',$BRID_REF);
                                              
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF);               
            

            $objUdfSQData2 = DB::table('TBL_MST_UDFFOR_SQ')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF2)
                ->get()->toArray(); 
    
        
    
            $objCurrencyconverter = DB::table('TBL_MST_CRCONVERSION')
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_CRCONVERSION.*')
            ->get()
            ->toArray();
    
            $objSalesPerson = DB::table('TBL_MST_EMPLOYEE')
            ->where('STATUS','=',$Status)
            ->where('SALES_PERSON','=','1')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->select('TBL_MST_EMPLOYEE.*')
            ->get()
            ->toArray();

            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
            ->get() ->toArray(); 

            $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')
            ->get() ->toArray(); 

            $FormId = $this->form_id;
        
            $AlpsStatus =   $this->AlpsStatus();

            $objothcurrency = $this->GetCurrencyMaster(); 
            $ActionStatus ="";

            $objSQCUST = DB::table('TBL_TRN_SLQT01_HDR')
            ->where('TBL_TRN_SLQT01_HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_SLQT01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_SLQT01_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_SLQT01_HDR.SQID','=',$id)
            ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_TRN_SLQT01_HDR.SLID_REF','=','TBL_MST_SUBLEDGER.SGLID')
            ->select('TBL_TRN_SLQT01_HDR.*','TBL_MST_SUBLEDGER.SGLID','TBL_MST_SUBLEDGER.SGLCODE AS CCODE','TBL_MST_SUBLEDGER.SLNAME AS NAME')
            ->first();
        
            return view('transactions.sales.SalesQuotation.trnfrm36amendment',compact(['AlpsStatus','objSQ','objRights','objCount1','objSPID',
           'objCount2','objCount3','objCount4','objSQMAT','objSQCAL','objSQTNC','objSQUDF','objCalculationHeader',
           'objUdfSQData','objTNCHeader','objothcurrency','objSalesPerson',
           'objsubglcode','objShpAddress','objBillAddress','objTNCDetails','objUdfSQData2','objCalHeader','objCalDetails',
           'TAXSTATE','objlastSQDT','FormId','MAXSQANO','ActionStatus','SchemeId','SchemeName','objSQCUST']));
        }
     
    }

    
    public function saveamendment(Request $request){
       // dd($request->all()); 

        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];

        $GROSS_TOTAL    =   0; 
        $NET_TOTAL 		= $request['TotalValue'];
        $CGSTAMT        =   0; 
        $SGSTAMT        =   0; 
        $IGSTAMT        =   0; 
        $DISCOUNT       =   0; 
        $OTHER_CHARGES  =   0; 
        $TDS_AMOUNT     =   0; 
		
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
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]))
            {
                $GROSS_TOTAL+= $request['DISAFTT_AMT_'.$i]; 
                $CGSTAMT+= $request['CGSTAMT_'.$i]; 
                $SGSTAMT+= $request['SGSTAMT_'.$i]; 
                $IGSTAMT+= $request['IGSTAMT_'.$i]; 
                $DISCOUNT+= $request['DISCOUNT_AMT_'.$i]; 

                $req_data[$i] = [
                    'SEQID_REF' => (isset($request['SEQID_REF_'.$i]) ? $request['SEQID_REF_'.$i] : "0") ,
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'MAIN_UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'SQA_QTY' => $request['SQ_QTY_'.$i],
                    'ALT_UOMID_REF' => $request['ALT_UOMID_REF_'.$i],
                    'RATEPUOM' => $request['RATEPUOM_'.$i],
                    'DISCPER'    => (!Empty($request['DISCPER_'.$i]) ? $request['DISCPER_'.$i] : 0),
                    'DISCOUNT_AMT' => (!Empty($request['DISCOUNT_AMT_'.$i]) ? $request['DISCOUNT_AMT_'.$i] : 0),
                    'IGST' => (!Empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!Empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!Empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'SCHEMEID_REF'       => $request['SCHEMEID_REF_'.$i]!='' ? $request['SCHEMEID_REF_'.$i] :  NULL,
                    'ITEM_TYPE'          => $request['ITEM_TYPE_'.$i] !="" ? $request['ITEM_TYPE_'.$i] : "OTHER",   
                    'SCHEMEQTY' => (!empty($request['SCHEMEQTY_'.$i]) ? $request['SCHEMEQTY_'.$i] : 0),   
                ];
            }
        }
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        
            for ($i=0; $i<=$r_count2; $i++)
            {
                    if(isset($request['TNCID_REF']) && !is_null($request['TNCID_REF']))
                    {
                        if(isset($request['TNCDID_REF_'.$i]))
                        {
                            $reqdata2[$i] = [
                                'TNCID_REF'     => $request['TNCID_REF'] ,
                                'TNCDID_REF'    => $request['TNCDID_REF_'.$i],
                                'VALUE'         => $request['tncdetvalue_'.$i],
                            ];
                        }
                    }
                
            }
               if(isset($reqdata2))
               { 
                $wrapped_links2["TNC"] = $reqdata2;
                $XMLTNC = ArrayToXml::convert($wrapped_links2);
               }
               else
               {
                $XMLTNC = NULL; 
               }   
                
            
            for ($i=0; $i<=$r_count3; $i++)
            {
                    if(isset($request['UDFSQID_REF_'.$i]) || !is_null($request['UDFSQID_REF_'.$i]))
                    {
                        $reqdata3[$i] = [
                            'UDFSQID_REF'   => $request['UDFSQID_REF_'.$i],
                            'SQAUVALUE'      => $request['udfvalue_'.$i],
                        ];
                    }
                
            }
            if(isset($reqdata3))
            { 
                $wrapped_links3["UDF"] = $reqdata3; 
                $XMLUDF = ArrayToXml::convert($wrapped_links3);
            }
            else
            {
                $XMLUDF = NULL; 
            }
                
            
            for ($i=0; $i<=$r_count4; $i++)
            {
                    if(isset($request['CTID_REF']) && !is_null($request['CTID_REF']))
                    {

                        if(isset($request['CT_TYPE_'.$i]) && $request['CT_TYPE_'.$i]=="DISCOUNT"){
                          
                            $DISCOUNT      += $request['VALUE_'.$i]; 
                        }else{
                            $OTHER_CHARGES += $request['VALUE_'.$i];   
                        }

                        if(isset($request['TID_REF_'.$i]))
                        {
                            $reqdata4[$i] = [
                                'CTID_REF'      => $request['CTID_REF'] ,
                                'TID_REF'       => $request['TID_REF_'.$i],
                                'RATE'          => $request['RATE_'.$i],
                                'VALUE'         => $request['VALUE_'.$i],
                                'GST'           => (isset($request['calGST_'.$i])!="true" ? 0 : 1) ,
                                'IGST'          => (isset($request['calIGST_'.$i]) && !empty($request['calIGST_'.$i]) ? $request['calIGST_'.$i] : 0),
                                'CGST'          => (isset($request['calCGST_'.$i]) && !empty($request['calCGST_'.$i]) ? $request['calCGST_'.$i] : 0),
                                'SGST'          => (isset($request['calSGST_'.$i]) && !empty($request['calSGST_'.$i]) ? $request['calSGST_'.$i] : 0),
                                'ACTUAL'        => (isset($request['calACTUAL_'.$i])!="true" ? 0 : 1) ,
                            ];
                        }
                    }
                
            }
            if(isset($reqdata4))
            { 
                $wrapped_links4["CAL"] = $reqdata4; 
                $XMLCAL = ArrayToXml::convert($wrapped_links4);
            }
            else
            {
                $XMLCAL = NULL; 
            }
        
        

            $VTID_REF     =   36;  
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = $Approvallevel;
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');

            $SQID_REF = $request['SQID_REF'];
            $SQNO = $request['SQNO'];   
            $SQADT = $request['SQADT'];
            $REASONTOSQA = trim($request['REASONTOSQA']); 
                    
            $SQDT = $request['SQDT'];
            $GLID_REF = $request['GLID_REF'];
            $SLID_REF = $request['SLID_REF'];
            $FC = (isset($request['FC'])!="true" ? 0 : 1);
            $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
            $CONVFACT = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
            $QVFDT = $request['QVFDT'];
            $QVTDT = $request['QVTDT'];
            $SPID_REF = $request['SPID_REF'];
            $REFNO = $request['REFNO'];
            $BILLTO = $request['BILLTO'];
            $SHIPTO = $request['SHIPTO'];
            $REMARKS = trim($request['REMARKS']);

            $LEAD_REF = $request['LEAD_REF'];
            $QUOTATIONTYPE = strtoupper($request['QUOTATIONTYPE']);

            $DEALERID_REF           = $request['DEALERID_REF'];
            $PROJECTID_REF          = $request['PROJECTID_REF'];
            $DEALER_COMMISSION_AMT  = $request['DEALER_COMMISSION_AMT'];

            $log_data = [ $SQID_REF,                    $SQNO,                  $SQADT,                 $GLID_REF, 
                          $SLID_REF,                    $FC,                    $CRID_REF,              $CONVFACT,
                          $QVFDT,                       $QVTDT,                 $SPID_REF,              $REFNO,
                          $BILLTO,                      $SHIPTO,                $REASONTOSQA,           $FYID_REF,
                          $CYID_REF,                    $BRID_REF,              $VTID_REF,              $XMLMAT,
                          $XMLTNC,                      $XMLUDF,                $XMLCAL,                $USERID, 
                          Date('Y-m-d'),                Date('h:i:s.u'),        $ACTIONNAME,            $IPADDRESS,
                          $QUOTATIONTYPE,               $LEAD_REF,              $DEALERID_REF,          $PROJECTID_REF,
                          $DEALER_COMMISSION_AMT,       $GROSS_TOTAL,           $NET_TOTAL,             $CGSTAMT,
                          $SGSTAMT,                     $IGSTAMT,               $DISCOUNT,              $OTHER_CHARGES,
                          $TDS_AMOUNT
            ];

           // dd($log_data); 
            $sp_result = DB::select('EXEC SP_SQA_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,? ,?,?,?,?,? ,?,?,?,?,? ,?,?,?,?,? ,?', $log_data);   
            
         

            
           
           
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            exit();  
    }

    public function LastApprovedDocDate(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $VTID_REF     =   $this->vtid_ref;
        return $objlastDocDate = DB::select('SELECT MAX(SQDT) SQDT FROM TBL_TRN_SLQT01_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $VTID_REF, $Status ]);

    }

    
      
    public function get_Lead(Request $request) {   
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');  
         
        
        $CUSTOMER_TYPE  = $request['CUSTOMER_TYPE'];
        $CUSTPROSCTID   = $request['CUSTPROSCTID'];        
              
        $objLead = DB::select("SELECT L.LEAD_ID AS DOCID,L.LEAD_NO AS DOCNO,L.LEAD_DT AS DESCRIPTION, L.CUSTOMER_TYPE,ISNULL(C.CCODE,P.PCODE) AS CODE,CONCAT(C.NAME,P.NAME) AS NAME,ISNULL(C.SLID_REF,P.PID) AS CUSTOMER_PROSPECTID,ISNULL(C.GLID_REF,'') AS GLID_REF FROM TBL_TRN_LEAD_GENERATION L 
		LEFT JOIN TBL_MST_CUSTOMER C ON C.SLID_REF=L.CUSTOMER_PROSPECT AND L.CUSTOMER_TYPE='CUSTOMER'	
        LEFT JOIN TBL_MST_PROSPECT P ON P.PID=L.CUSTOMER_PROSPECT AND L.CUSTOMER_TYPE='PROSPECT'
        WHERE L.CYID_REF=$CYID_REF AND L.STATUS='$Status' AND L.BRID_REF=$BRID_REF AND L.CUSTOMER_TYPE='$CUSTOMER_TYPE' AND L.CUSTOMER_PROSPECT='$CUSTPROSCTID'
        ");       

        // $objLead = DB::select("SELECT L.LEAD_ID AS DOCID,L.LEAD_NO AS DOCNO,L.LEAD_DT AS DESCRIPTION, L.CUSTOMER_TYPE,ISNULL(C.CCODE,P.PCODE) AS CODE,CONCAT(C.NAME,P.NAME) AS NAME,ISNULL(C.SLID_REF,P.PID) AS CUSTOMER_PROSPECTID,ISNULL(C.GLID_REF,'') AS GLID_REF FROM TBL_TRN_LEAD_GENERATION L 
		// LEFT JOIN TBL_MST_CUSTOMER C ON C.SLID_REF=L.CUSTOMER_PROSPECT AND L.CUSTOMER_TYPE='CUSTOMER'	
        // LEFT JOIN TBL_MST_PROSPECT P ON P.PID=L.CUSTOMER_PROSPECT AND L.CUSTOMER_TYPE='PROSPECT'
        // WHERE L.CYID_REF=$CYID_REF AND L.STATUS='$Status' AND L.BRID_REF=$BRID_REF
        // ");
       // dd($objLead); 

        if(!empty($objLead)){        
            foreach ($objLead as $index=>$dataRow){   
                $row = '';
                $row = $row.'<tr ><td style="text-align:center; width:10%">';
                $row = $row.'<input type="checkbox" name="lead[]"  id="leadcode_'.$dataRow->DOCID.'" class="clsspid_lead" 
                value="'.$dataRow->DOCID.'"/>             
                </td>           
                <td style="width:40%;">'.$dataRow->DOCNO;
                $row = $row.'<input type="hidden" id="txtleadcode_'.$dataRow->DOCID.'" data-code="'.$dataRow->DOCNO.'"   data-desc="'.$dataRow->DESCRIPTION.'"  data-custtype="'.$dataRow->CUSTOMER_TYPE.'"  data-slid_ref="'.$dataRow->CUSTOMER_PROSPECTID.'" data-glid_ref="'.$dataRow->GLID_REF.'"   data-slname="'.$dataRow->CODE.'-'.$dataRow->NAME.'" 
                value="'.$dataRow->DOCID.'"/></td>    
                <td style="width:40%;">'.$dataRow->DESCRIPTION.'</td>   
               </tr>';
                echo $row;
            }
    
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
    
            exit();
    
    
    
       }



       public function get_Price(Request $request){
        $CYID_REF           =   Auth::user()->CYID_REF;
        $BRID_REF           =   Session::get('BRID_REF');
        $FYID_REF           =   Session::get('FYID_REF');
        $fieldid            =   $request['fieldid'];
        $class_name         =   $request['class_name'];
        $ITEMID_REF         =   $request['ITEMID_REF'];    

        $ObjData1   =   DB::select("SELECT TOP 1 M.CUSTOMER_PRICE,M.DEALER_PRICE,M.MRP,M.MSP, H.INDATE FROM TBL_MST_PRICELIST_MAT M   
                        LEFT JOIN TBL_MST_PRICELIST_HDR H ON H.PLID=M.PLID_REF
                        WHERE M.ITEMID_REF=$ITEMID_REF AND H.CYID_REF=$CYID_REF AND H.BRID_REF=$BRID_REF AND H.STATUS='A'");
        $PriceList=array(); 
        if($ObjData1) {

            $PriceList=array(
                "CUSTOMERPRICE"=>isset($ObjData1[0]->CUSTOMER_PRICE) ? $ObjData1[0]->CUSTOMER_PRICE : 0, 
                "DEALERPRICE"=>isset($ObjData1[0]->DEALER_PRICE) ? $ObjData1[0]->DEALER_PRICE : 0, 
                "MRP"=>isset($ObjData1[0]->MRP) ? $ObjData1[0]->MRP : 0, 
                "MSP"=>isset($ObjData1[0]->MSP) ? $ObjData1[0]->MSP : 0
            );
   

      
    }

       // dd($PriceList); 

        if(!empty($PriceList)){
           foreach ($PriceList as $index=>$dataRow){ 
            $PriceType=NULL;
            if($index=="CUSTOMERPRICE")
            {                
                $PriceType="Customer Price";
            }
            else if($index=="DEALERPRICE")
            {
                $PriceType="Dealer Price";
            }else{
                $PriceType=$index;
            }

               $row =   '';
               $row = $row.'<tr >
               <td style="width:10%"> <input type="checkbox" name="SELECT[]" id="socode_'.$index .'"  class="'.$class_name.'" value="'.$PriceType.'" ></td>
               <td style="width:40%">'.$PriceType;
               $row = $row.'<input type="hidden" id="txtsocode_'.$index.'" data-desc="'.$dataRow.'"  value="'.$index.'"/></td>
               <td style="width:40%">'.$dataRow.'</td>
               </tr>';
               echo $row;                       
           }                
        }else{
            echo '<tr><td>Record not found.</td></tr>';
        }
        exit();   
        }
    

       

public function getItemPrice($ITEMID_REF,$SQDT){


        $CYID_REF           =   Auth::user()->CYID_REF;
        $BRID_REF           =   Session::get('BRID_REF');
        $FYID_REF           =   Session::get('FYID_REF');
        $ObjData1   =   DB::select("SELECT TOP 1 M.CUSTOMER_PRICE,M.DEALER_PRICE,M.MRP,M.MSP, H.INDATE FROM TBL_MST_PRICELIST_MAT M   
                        LEFT JOIN TBL_MST_PRICELIST_HDR H ON H.PLID=M.PLID_REF
                        WHERE '$SQDT' BETWEEN H.PERIOD_FRDT AND H.PERIOD_TODT
						AND H.PERIOD_FRDT IS NOT NULL AND H.PERIOD_TODT IS NOT NULL AND M.ITEMID_REF=$ITEMID_REF AND H.CYID_REF=$CYID_REF AND H.BRID_REF=$BRID_REF AND H.STATUS='A'");
        $PriceList  = array(); 
        if($ObjData1){
            return $PriceList=array(
                "CUSTOMER PRICE"=>isset($ObjData1[0]->CUSTOMER_PRICE) ? $ObjData1[0]->CUSTOMER_PRICE : 0, 
                "DEALER PRICE"=>isset($ObjData1[0]->DEALER_PRICE) ? $ObjData1[0]->DEALER_PRICE : 0, 
                "MRP"=>isset($ObjData1[0]->MRP) ? $ObjData1[0]->MRP : 0, 
                "MSP"=>isset($ObjData1[0]->MSP) ? $ObjData1[0]->MSP : 0
            );

        }else{
            return $PriceList; 

        }
   
        exit();   
        }


        public function get_Dealer(Request $request) {   
            $Status = "A";
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');        
            $objDealer = DB::table('TBL_MST_CUSTOMER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('STATUS','=',$Status)     
            ->where('TYPE','=','DEALER')     
            ->select('CID AS DOCID','CCODE AS DOCNO','NAME AS DESC','COMMISION')
            ->get()    
            ->toArray();
            //dd($objDealer); 
    
     
            if(!empty($objDealer)){        
                foreach ($objDealer as $index=>$dataRow){   
                    $row = '';
                    $row = $row.'<tr ><td style="text-align:center; width:10%">';
                    $row = $row.'<input type="checkbox" name="dealer[]"  id="dealercode_'.$dataRow->DOCID.'" class="clsspid_dealer" 
                    value="'.$dataRow->DOCID.'"/>             
                    </td>           
                    <td style="width:40%;">'.$dataRow->DOCNO;
                    $row = $row.'<input type="hidden" id="txtdealercode_'.$dataRow->DOCID.'" data-code="'.$dataRow->DESC.'"   data-desc="'.$dataRow->DESC.'" 
                    data-desc1="'.$dataRow->COMMISION.'"  value="'.$dataRow->DOCID.'"/></td>
        
                    <td style="width:40%;">'.$dataRow->DESC.'</td>
           
        
                   </tr>';
                    echo $row;
                }
        
                }else{
                    echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
        
                exit();
        
        
        
           }




           public function get_Project(Request $request) {   
            $Status = "A";
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');        
            $objProject = DB::table('TBL_MST_PROJECT')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('STATUS','=',$Status)     
            ->select('PID AS DOCID','PCODE AS DOCNO','DESCRIPTIONS AS DESC')
            ->get()    
            ->toArray();
            //dd($objProject); 
    
     
            if(!empty($objProject)){        
                foreach ($objProject as $index=>$dataRow){   
                    $row = '';
                    $row = $row.'<tr ><td style="text-align:center; width:10%">';
                    $row = $row.'<input type="checkbox" name="project[]"  id="projectcode_'.$dataRow->DOCID.'" class="clsspid_project" 
                    value="'.$dataRow->DOCID.'"/>             
                    </td>           
                    <td style="width:40%;">'.$dataRow->DOCNO;
                    $row = $row.'<input type="hidden" id="txtprojectcode_'.$dataRow->DOCID.'" data-code="'.$dataRow->DESC.'"   data-desc="'.$dataRow->DESC.'" 
                    value="'.$dataRow->DOCID.'"/></td>
        
                    <td style="width:40%;">'.$dataRow->DESC.'</td>
           
        
                   </tr>';
                    echo $row;
                }
        
                }else{
                    echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
        
                exit();
        
        
        
           }


           public function get_Scheme(Request $request) {   
            $Status = "A";
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');      
            $DOCDT   =   $request['DOCDT'];  
            $objScheme = DB::select("SELECT SCHEMEID AS DOCID,SCHEME_NO AS DOCNO,SCHEME_NAME AS DESCRIPTIONS FROM TBL_MST_SCHEME_HDR 
            WHERE CYID_REF=$CYID_REF AND BRID_REF=$BRID_REF AND STATUS='$Status' AND '$DOCDT' BETWEEN EFF_FROM_DATE AND EFF_TO_DATE");
         
        
            $EXIST_SCHEME   =   $request['SCHEMEID_REF'];
            if($EXIST_SCHEME != ''){   
             $objScheme_exist     =    preg_split ("/\,/", $EXIST_SCHEME);      
            }
        
        
            if(!empty($objScheme)){        
                foreach ($objScheme as $index=>$dataRow){   
                   // dd($dataRow); 
        
                    $check=isset($objScheme_exist) && in_array($dataRow->DOCID,$objScheme_exist) ? "checked":"";
                   // dd($check); 
                    $row = '';
                    $row = $row.'<tr class="participantRow10"><td style="text-align:center; width:10%">';
                    $row = $row.'<input type="checkbox" '.$check.' name="scheme[]"  id="schemecode_'.$dataRow->DOCID.'" class="clsspid_scheme" 
                    value="'.$dataRow->DOCID.'"/>             
                    </td>           
                    <td style="width:40%;">'.$dataRow->DOCNO;
                    $row = $row.'<input type="hidden" id="txtschemecode_'.$dataRow->DOCID.'" data-code="'.$dataRow->DESCRIPTIONS.'"   data-desc="'.$dataRow->DESCRIPTIONS.'" 
                    value="'.$dataRow->DOCID.'"/></td>
        
                    <td hidden style="width:40%;">'.$dataRow->DESCRIPTIONS;
                    $row = $row.'<input type="hidden" id="txtschemename_'.$dataRow->DESCRIPTIONS.'" data-code="'.$dataRow->DESCRIPTIONS.'"   data-desc="'.$dataRow->DESCRIPTIONS.'" 
                    value="'.$dataRow->DESCRIPTIONS.'"/></td>
        
                    <td style="width:40%;" >'.$dataRow->DESCRIPTIONS.'</td>
           
        
                   </tr>';
                    echo $row;
                }
        
                }else{
                    echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
        
                exit();
        
        
        
           }


          
 function GetSchemeMaterialItems(Request $request){
    $final_data                 =       array(); 
    $Exist_SchemeId             =       array(); 
    $objSchemeData              =       array(); 
    $r_count1                   =       $request['Row_Count1'];
    $SCHEMEID_REF               =       $request['SCHEMEID_REF'];
    if($SCHEMEID_REF != ''){   
    $objSchemeData             =    preg_split ("/\,/", $SCHEMEID_REF);      
    }
  //  dd($objSchemeData); 

    
    for ($i=0; $i<=$r_count1; $i++)
    {
        if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i]))
        {
            if(!empty($objSchemeData) && in_array($request['SCHEMEID_REF_'.$i],$objSchemeData)){
            $final_data[] = array(    
                'SCHEMEID_REF'         =>   $request['SCHEMEID_REF_'.$i],
                'SCHEMEQTY'            =>   $request['SCHEMEQTY_'.$i],
                'ITEM_TYPE'            =>   $request['ITEM_TYPE_'.$i],
                'txtSE_popup'          =>   $request['txtSE_popup_'.$i],
                'SEQID_REF'            =>   $request['SEQID_REF_'.$i],
                'popupITEMID'          =>   $request['popupITEMID_'.$i],
                'ITEMID_REF'           =>   $request['ITEMID_REF_'.$i],
                'ItemName'             =>   $request['ItemName_'.$i], 
                'SEMUOM'           =>   $request['SEMUOM_'.$i],
                'SEMUOMQTY'           =>   $request['SEMUOMQTY_'.$i],
                'SEAUOM'            =>   $request['SEAUOM_'.$i],
                'SEAUOMQTY'            =>   $request['SEAUOMQTY_'.$i],
                'popupMUOM'            =>   $request['popupMUOM_'.$i],                  
                'MAIN_UOMID_REF'       =>   $request['MAIN_UOMID_REF_'.$i],                  
                'SQ_QTY'               =>   $request['SQ_QTY_'.$i], 
                'SQ_FQTY'              =>   $request['SQ_FQTY_'.$i],    

                'popupAUOM'            =>   $request['popupAUOM_'.$i],             
                'ALT_UOMID_REF'        =>   $request['ALT_UOMID_REF_'.$i],  
                'ALT_UOMID_QTY'        =>   $request['ALT_UOMID_QTY_'.$i],                  
                'RATEPUOM'             =>   $request['RATEPUOM_'.$i],  
                'DISCPER'              =>   $request['DISCPER_'.$i],                   
                'DISCOUNT_AMT'         =>   $request['DISCOUNT_AMT_'.$i],   
                'DISAFTT_AMT'          =>   $request['DISAFTT_AMT_'.$i],
                'IGST'                 =>   $request['IGST_'.$i], 
                'IGSTAMT'              =>   $request['IGSTAMT_'.$i],  
                'CGST'                 =>   $request['CGST_'.$i],
                'CGSTAMT'              =>   $request['CGSTAMT_'.$i],
                'SGST'                 =>   $request['SGST_'.$i],
                'SGSTAMT'              =>   $request['SGSTAMT_'.$i],
                'TGST_AMT'             =>   $request['TGST_AMT_'.$i],
                'TOT_AMT'              =>   $request['TOT_AMT_'.$i]   
            );   

            if($request['SCHEMEID_REF_'.$i]!=""){
            $Exist_SchemeId[] =$request['SCHEMEID_REF_'.$i];
            }


        }else if(empty($objSchemeData)){
                if($request['SCHEMEID_REF_'.$i]==""){
                $final_data[] = array(    
                    'SCHEMEID_REF'         =>   $request['SCHEMEID_REF_'.$i],
                    'SCHEMEQTY'            =>   $request['SCHEMEQTY_'.$i],
                    'ITEM_TYPE'            =>   $request['ITEM_TYPE_'.$i],
                    'txtSE_popup'          =>   $request['txtSE_popup_'.$i],
                    'SQA'                  =>   $request['SQA_'.$i],
                    'SEQID_REF'            =>   $request['SEQID_REF_'.$i],
                    'popupITEMID'          =>   $request['popupITEMID_'.$i],
                    'ITEMID_REF'           =>   $request['ITEMID_REF_'.$i],   
                    'ItemName'             =>   $request['ItemName_'.$i],
                    'SEMUOM'               =>   $request['SEMUOM_'.$i],
                    'SEMUOMQTY'            =>   $request['SEMUOMQTY_'.$i],
                    'SEAUOM'               =>   $request['SEAUOM_'.$i],
                    'SEAUOMQTY'            =>   $request['SEAUOMQTY_'.$i],
                    'popupMUOM'            =>   $request['popupMUOM_'.$i],                  
                    'MAIN_UOMID_REF'       =>   $request['MAIN_UOMID_REF_'.$i],                  
                    'SQ_QTY'               =>   $request['SQ_QTY_'.$i],  
                    'SQ_FQTY'              =>   $request['SQ_FQTY_'.$i],  

                    'popupAUOM'            =>   $request['popupAUOM_'.$i],             
                    'ALT_UOMID_REF'        =>   $request['ALT_UOMID_REF_'.$i],  
                    'ALT_UOMID_QTY'        =>   $request['ALT_UOMID_QTY_'.$i],                  
                    'RATEPUOM'             =>   $request['RATEPUOM_'.$i],  
                    'DISCPER'              =>   $request['DISCPER_'.$i],                   
                    'DISCOUNT_AMT'         =>   $request['DISCOUNT_AMT_'.$i],   
                    'DISAFTT_AMT'          =>   $request['DISAFTT_AMT_'.$i],
                    'IGST'                 =>   $request['IGST_'.$i], 
                    'IGSTAMT'              =>   $request['IGSTAMT_'.$i],  
                    'CGST'                 =>   $request['CGST_'.$i],
                    'CGSTAMT'              =>   $request['CGSTAMT_'.$i],
                    'SGST'                 =>   $request['SGST_'.$i],
                    'SGSTAMT'              =>   $request['SGSTAMT_'.$i],
                    'TGST_AMT'             =>   $request['TGST_AMT_'.$i],
                    'TOT_AMT'              =>   $request['TOT_AMT_'.$i]   
                );    
            }  
    

        }else{
            if($request['SCHEMEID_REF_'.$i]==""){
            $final_data[] = array(    
                'SCHEMEID_REF'         =>   $request['SCHEMEID_REF_'.$i],
                'SCHEMEQTY'            =>   $request['SCHEMEQTY_'.$i],
                'ITEM_TYPE'            =>   $request['ITEM_TYPE_'.$i],
                'txtSE_popup'          =>   $request['txtSE_popup_'.$i],               
                'SEQID_REF'            =>   $request['SEQID_REF_'.$i],
                'popupITEMID'          =>   $request['popupITEMID_'.$i],
                'ITEMID_REF'           =>   $request['ITEMID_REF_'.$i],     
                'ItemName'             =>   $request['ItemName_'.$i],
                'SEMUOM'           =>   $request['SEMUOM_'.$i],
                'SEMUOMQTY'           =>   $request['SEMUOMQTY_'.$i],
                'SEAUOM'            =>   $request['SEAUOM_'.$i],
                'SEAUOMQTY'            =>   $request['SEAUOMQTY_'.$i],  
                'popupMUOM'            =>   $request['popupMUOM_'.$i],                  
                'MAIN_UOMID_REF'       =>   $request['MAIN_UOMID_REF_'.$i],                  
                'SQ_QTY'               =>   $request['SQ_QTY_'.$i],    
                'SQ_FQTY'              =>   $request['SQ_FQTY_'.$i],  

                'popupAUOM'            =>   $request['popupAUOM_'.$i],             
                'ALT_UOMID_REF'        =>   $request['ALT_UOMID_REF_'.$i],  
                'ALT_UOMID_QTY'        =>   $request['ALT_UOMID_QTY_'.$i],                  
                'RATEPUOM'             =>   $request['RATEPUOM_'.$i],  
                'DISCPER'              =>   $request['DISCPER_'.$i],                   
                'DISCOUNT_AMT'         =>   $request['DISCOUNT_AMT_'.$i],   
                'DISAFTT_AMT'          =>   $request['DISAFTT_AMT_'.$i],
                'IGST'                 =>   $request['IGST_'.$i], 
                'IGSTAMT'              =>   $request['IGSTAMT_'.$i],  
                'CGST'                 =>   $request['CGST_'.$i],
                'CGSTAMT'              =>   $request['CGSTAMT_'.$i],
                'SGST'                 =>   $request['SGST_'.$i],
                'SGSTAMT'              =>   $request['SGSTAMT_'.$i],
                'TGST_AMT'             =>   $request['TGST_AMT_'.$i],
                'TOT_AMT'              =>   $request['TOT_AMT_'.$i]   
            );     
        }
        }            
    }
}


//dd($final_data); 
    
    $CYID_REF               =   Auth::user()->CYID_REF;
    $BRID_REF               =   Session::get('BRID_REF');
    $FYID_REF               =   Session::get('FYID_REF');  
    $ModuleType             =   1; 
    $material_array         =   array(); 
    $material_array_final=array(); 
    if($SCHEMEID_REF !="")
    {
    foreach($objSchemeData as $key=>$SchemeId){
    $material_array[] =     DB::select("SELECT I.ITEMID,
                                        I.MAIN_UOMID_REF,
                                        I.ALT_UOMID_REF,
                                        I.ITEMGID_REF,
                                        I.ICODE,
                                        I.NAME AS ITEM_NAME,
                                        I.ITEM_SPECI,
                                        I.ALPS_PART_NO,
                                        I.CUSTOMER_PART_NO,
                                        I.OEM_PART_NO,
                                        CONCAT(U.UOMCODE,'-',U.DESCRIPTIONS) AS MAIN_UOM,
                                        CONCAT(AU.UOMCODE,'-',AU.DESCRIPTIONS) AS ALT_UOM,
                                        H.QTY AS ITEM_QTY,
                                        'NA' COST,
                                        0 AS DISCOUNT_PERCENTAGE,
                                        'NA' AS PERCENTAGE_BASEDON,
                                        H.SCHEMEID,
                                        'MAIN' AS ITEM_TYPE
                                        FROM TBL_MST_SCHEME_HDR H
                                        LEFT JOIN TBL_MST_ITEM I ON I.ITEMID=H.ITEMID_REF
                                        LEFT JOIN TBL_MST_UOM U ON U.UOMID=I.MAIN_UOMID_REF
                                        LEFT JOIN TBL_MST_UOM AU ON AU.UOMID=I.ALT_UOMID_REF
                                        WHERE H.SCHEMEID =$SchemeId
                                        UNION 
                                        SELECT I.ITEMID,
                                        I.MAIN_UOMID_REF,
                                        I.ALT_UOMID_REF,
                                        I.ITEMGID_REF,
                                        I.ICODE,
                                        I.NAME AS ITEM_NAME,
                                        I.ITEM_SPECI,
                                        I.ALPS_PART_NO,
                                        I.CUSTOMER_PART_NO,
                                        I.OEM_PART_NO,
                                        CONCAT(U.UOMCODE,'-',U.DESCRIPTIONS) AS MAIN_UOM,
                                        CONCAT(AU.UOMCODE,'-',AU.DESCRIPTIONS) AS ALT_UOM,
                                        M.ITEM_QTY,
                                        M.COST,
                                        M.PER AS DISCOUNT_PERCENTAGE,
                                        M.PER_BASE AS PERCENTAGE_BASEDON,
                                        M.SCHEMEID_REF AS SCHEMEID,
                                        'SUB' AS ITEM_TYPE
                                        FROM TBL_MST_SCHEME_MAT M
                                        LEFT JOIN TBL_MST_SCHEME_HDR H ON H.SCHEMEID=M.SCHEMEID_REF
                                        LEFT JOIN TBL_MST_ITEM I ON I.ITEMID=M.ITEMID_REF
                                        LEFT JOIN TBL_MST_UOM U ON U.UOMID=I.MAIN_UOMID_REF
                                        LEFT JOIN TBL_MST_UOM AU ON AU.UOMID=I.ALT_UOMID_REF
                                        WHERE M.SCHEMEID_REF =$SchemeId
                                        ORDER BY ITEM_TYPE");  
    }
}


foreach ($material_array as $key=>$ItemData) {
    $material_array_final = array_merge($material_array_final, $ItemData);
}


foreach($material_array_final as $index=>$row_data){
          
    $SODT                       =   $request['SODT'];
    $DISCOUNT_AMOUNT            =  0;   
    $DISCOUNT_PERCENTAGE        =  $row_data->DISCOUNT_PERCENTAGE;  


    $objItemCost=$this->getItemPrice($row_data->ITEMID,$SODT);
    if(isset($objItemCost['MRP']) && $objItemCost['MRP'] > 0){
    $ItemRate    =   $objItemCost['MRP'];                             
    }else{
    $ItemRate        =   0;                       
    }

    if($row_data->COST=="DISC"){
        $PERCENTAGE_BASEDON= $row_data->PERCENTAGE_BASEDON;        
        $objItemCost=$this->getItemPrice($row_data->ITEMID,$SODT);
        if(isset($objItemCost['MRP']) && $objItemCost['MRP'] && $PERCENTAGE_BASEDON=="MRP"){
        $ItemRate    =   $objItemCost['MRP'];                             
        }else if(isset($objItemCost['MSP']) && $objItemCost['MSP'] && $PERCENTAGE_BASEDON=="MSP"){
        $ItemRate    =   $objItemCost['MRP'];                        
        }else if(isset($objItemCost['CUSTOMER PRICE']) && $objItemCost['CUSTOMER PRICE'] && $PERCENTAGE_BASEDON=="CP"){
        $ItemRate    =   $objItemCost['MRP']; 
        }else if(isset($objItemCost['DEALER PRICE']) && $objItemCost['DEALER PRICE'] && $PERCENTAGE_BASEDON=="DP"){
        $ItemRate    =   $objItemCost['MRP'];   

        $DISCOUNT_AMOUNT              =  ($ItemRate*$PERCENTAGE_BASEDON)/100;   
        }
        }


    $Tax_State           =      isset($request['Tax_State']) ? trim($request['Tax_State']): "" ;                        
    $TaxRate             =      $this->GetTaXRate($row_data->ITEMID,$Tax_State,$ModuleType);               
    $IGST                =      $TaxRate['IGST'];
    $CGST                =      $TaxRate['CGST'];
    $SGST                =      $TaxRate['SGST'];
    $AMOUNT              =      $ItemRate*$row_data->ITEM_QTY;                 ; 
    if($row_data->COST=="FREE"){
    $AMOUNT              =      0;   
    $ItemRate            =      0;   
    }



    $IGST_AMOUNT         =  number_format((($AMOUNT-$DISCOUNT_AMOUNT)*$TaxRate['IGST']/100),2,".",""); 
    $CGST_AMOUNT         =  number_format((($AMOUNT-$DISCOUNT_AMOUNT)*$TaxRate['CGST']/100),2,".",""); 
    $SGST_AMOUNT         =  number_format((($AMOUNT-$DISCOUNT_AMOUNT)*$TaxRate['SGST']/100),2,".",""); 
    $TOTAL_TAX           =  number_format(($IGST_AMOUNT+$CGST_AMOUNT+$SGST_AMOUNT),2,".","");   
    $TAXABLE_AMOUNT      =  number_format(($AMOUNT-$DISCOUNT_AMOUNT),2,".","");   
    $AMOUNT_WITHTAX      =  number_format(($TAXABLE_AMOUNT+$TOTAL_TAX),2,".","");   


    $ObjAltQTY =  DB::select('SELECT TOP 1  * FROM TBL_MST_ITEM_UOMCONV  
    WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
    [$row_data->ITEMID,$row_data->ALT_UOMID_REF ]);

    $TOQTY =  isset($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 0;


    if(!empty($Exist_SchemeId) && !in_array($row_data->SCHEMEID,$Exist_SchemeId)){
    $final_data[] = array(    
        'SCHEMEID_REF'         =>   $row_data->SCHEMEID, 
        'SCHEMEQTY'            =>   $row_data->ITEM_QTY,
        'ITEM_TYPE'            =>   $row_data->ITEM_TYPE,
        'txtSE_popup'          =>   '',   
        'SEQID_REF'            =>   '',    
        'popupITEMID'          =>   $row_data->ICODE,
        'ITEMID_REF'           =>   $row_data->ITEMID,    
        'ItemName'             =>   $row_data->ITEM_NAME,
        'SEMUOM'               =>   '',
        'SEMUOMQTY'            =>   '',
        'SEAUOM'               =>   '',
        'SEAUOMQTY'            =>   '',   
        'popupMUOM'            =>   $row_data->MAIN_UOM,                
        'MAIN_UOMID_REF'       =>   $row_data->MAIN_UOMID_REF,               
        'SQ_QTY'               =>   $row_data->ITEM_QTY,  
        'SQ_FQTY'              =>   $row_data->ITEM_QTY,  

        'popupAUOM'            =>   $row_data->ALT_UOM,            
        'ALT_UOMID_REF'        =>   $row_data->ALT_UOMID_REF,
        'ALT_UOMID_QTY'        =>   "",                
        'RATEPUOM'             =>   $ItemRate,  
        'DISCPER'              =>   $DISCOUNT_PERCENTAGE,                   
        'DISCOUNT_AMT'         =>   $DISCOUNT_AMOUNT,   
        'DISAFTT_AMT'          =>   $TAXABLE_AMOUNT,
        'IGST'                 =>   $IGST, 
        'IGSTAMT'              =>   $IGST_AMOUNT,  
        'CGST'                 =>   $CGST,
        'CGSTAMT'              =>   $CGST_AMOUNT,
        'SGST'                 =>   $SGST,
        'SGSTAMT'              =>   $SGST_AMOUNT,
        'TGST_AMT'             =>   $TOTAL_TAX,
        'TOT_AMT'              =>   $AMOUNT_WITHTAX   
    );   
}else if(empty($Exist_SchemeId)){
    $final_data[] = array(    
        'SCHEMEID_REF'         =>   $row_data->SCHEMEID, 
        'SCHEMEQTY'            =>   $row_data->ITEM_QTY,
        'ITEM_TYPE'            =>   $row_data->ITEM_TYPE,
        'txtSE_popup'          =>   '',        
        'SEQID_REF'            =>   '',
        'popupITEMID'          =>   $row_data->ICODE,
        'ITEMID_REF'           =>   $row_data->ITEMID,     
        'ItemName'             =>   $row_data->ITEM_NAME,
        'SEMUOM'               =>   '',
        'SEMUOMQTY'            =>   '',
        'SEAUOM'               =>   '',
        'SEAUOMQTY'               =>   '',


                      
        'popupMUOM'            =>   $row_data->MAIN_UOM,                
        'MAIN_UOMID_REF'       =>   $row_data->MAIN_UOMID_REF,               
        'SQ_QTY'               =>   $row_data->ITEM_QTY,  
        'SQ_FQTY'              =>   $row_data->ITEM_QTY,  

        'popupAUOM'            =>   $row_data->ALT_UOM,            
        'ALT_UOMID_REF'        =>   $row_data->ALT_UOMID_REF,
        'ALT_UOMID_QTY'        =>   "",                
        'RATEPUOM'             =>   $ItemRate,  
        'DISCPER'              =>   $DISCOUNT_PERCENTAGE,                   
        'DISCOUNT_AMT'         =>   $DISCOUNT_AMOUNT,   
        'DISAFTT_AMT'          =>   $TAXABLE_AMOUNT,
        'IGST'                 =>   $IGST, 
        'IGSTAMT'              =>   $IGST_AMOUNT,  
        'CGST'                 =>   $CGST,
        'CGSTAMT'              =>   $CGST_AMOUNT,
        'SGST'                 =>   $SGST,
        'SGSTAMT'              =>   $SGST_AMOUNT,
        'TGST_AMT'             =>   $TOTAL_TAX,
        'TOT_AMT'              =>   $AMOUNT_WITHTAX   
    );   

}



 




}

 
    $AlpsStatus     =   $this->AlpsStatus();
    
    $TabSetting	    =	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
    $COLUMN1        =   isset($TabSetting->FIELD8) ? $TabSetting->FIELD8 : "Add. Info Part No"; 
    $COLUMN2        =   isset($TabSetting->FIELD9) ? $TabSetting->FIELD9 : "Add. Info Customer Part No"; 
    $COLUMN3        =   isset($TabSetting->FIELD10) ? $TabSetting->FIELD10 : "Add. Info OEM Part No."; 
    


    if(!empty($final_data)){
        $Row_Count1 =   count($final_data);
        echo' 
        <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
        <thead id="thead1"  style="position: sticky;top: 0">
                
                <tr>
                    <th colspan="3"></th>
                    <th colspan="4">Sales Enquiry</th>
                    <th colspan="4">Sales Quotation</th>
                    <th colspan="13"></th>
                    
                </tr>
            <tr>
                <th rowspan="2">SE No<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="'.$Row_Count1.'"></th>
                <th rowspan="2">Item Code</th>
                <th rowspan="2">Item Name</th>
                <th rowspan="2">Main UOM</th>
                <th rowspan="2">Qty (Main UOM)</th>
                <th rowspan="2">ALT UOM</th>
                <th rowspan="2">Qty (Alt UOM)</th>
                <th rowspan="2">Main UOM</th>
                <th rowspan="2">Qty (Main UOM)</th>
                <th rowspan="2">ALT UOM</th>
                <th rowspan="2">Qty (Alt UOM)</th>
                <th rowspan="2">Rate Per UoM</th>
                <th colspan="2">Discount</th>
                <th rowspan="2">Amount after discount</th>
                <th rowspan="2">IGST Rate %</th>
                <th rowspan="2">IGST Amount</th>
                <th rowspan="2">CGST Rate %</th>
                <th rowspan="2">CGST Amount</th>
                <th rowspan="2">SGST Rate %</th>
                <th rowspan="2">SGST Amount</th>
                <th rowspan="2">Total GST Amount</th>
                <th rowspan="2">Total after GST</th>
                <th rowspan="2" width="3%">Action</th>
            </tr>
            
                <tr>
                    <th>%</th>
                    <th>Amount</th>
                </tr>
        </thead>
        <tbody>               
        ';



        

       //dd($final_data);

                foreach($final_data as $index=>$row_data){

                    echo '<tr  class="participantRow">';
                    echo '<td hidden><input type="text" name="SCHEMEID_REF_'.$index.'"  id="SCHEMEID_REF_'.$index.'"   class="form-control" autocomplete="off" style="width:130px;" value="'.$row_data['SCHEMEID_REF'].'"  /></td>'; 
                    echo '<td hidden><input type="text" name="ITEM_TYPE_'.$index.'"  id="ITEM_TYPE_'.$index.'"  class="form-control '.$row_data['ITEM_TYPE'].'"  autocomplete="off" style="width:130px;" value="'.$row_data['ITEM_TYPE'].'" /></td>';             
                      echo '<td hidden><input type="text" name="SCHEMEQTY_'.$index.'"  id="SCHEMEQTY_'.$index.'" class="form-control three-digits" maxlength="13"  autocomplete="off"  style="width:130px;text-align:right;" value="'.$row_data['SCHEMEQTY'].'"   /></td>';

                    echo '<td style="text-align:center;" >
                        <input style="width:130px;" name="txtSE_popup_'.$index.'"  id="txtSE_popup_'.$index.'" type="text"  class="form-control"  autocomplete="off" value="'.$row_data['txtSE_popup'].'" readonly/></td>';
                        echo '<td hidden><input type="hidden" name="SEQID_REF_'.$index.'"  id="SEQID_REF_'.$index.'"  class="form-control" autocomplete="off" value="'.$row_data['SEQID_REF'].'" /></td>';
                        echo '<td><input style="width:130px;" type="text" name="popupITEMID_'.$index.'"  id="popupITEMID_'.$index.'" class="form-control"  autocomplete="off" value="'.$row_data['popupITEMID'].'"  readonly/></td>';
                        echo '<td hidden><input type="hidden" name="ITEMID_REF_'.$index.'"  id="ITEMID_REF_'.$index.'"  class="form-control" autocomplete="off" value="'.$row_data['ITEMID_REF'].'" /></td>'; 
                        echo '<td><input type="text" style="width:130px;" name="ItemName_'.$index.'"  id="ItemName_'.$index.'" class="form-control"  autocomplete="off" value="'.$row_data['ItemName'].'"  readonly/></td>';
                        echo '<td><input type="text" style="width:130px;" name="SEMUOM_'.$index.'"  id="SEMUOM_'.$index.'" class="form-control"  autocomplete="off" value="'.$row_data['SEMUOM'].'"  readonly/></td>';
                        echo '<td><input type="text" style="width:130px;" name="SEMUOMQTY_'.$index.'"  id="SEMUOMQTY_'.$index.'"  class="form-control" maxlength="13"  autocomplete="off" value="'.$row_data['SEMUOMQTY'].'"  readonly/></td>';
                        echo '<td><input type="text" style="width:130px;" name="SEAUOM_'.$index.'"  id="SEAUOM_'.$index.'" class="form-control"  autocomplete="off" value="'.$row_data['SEAUOM'].'"  readonly/></td>';
                        echo '<td><input type="text" style="width:130px;" name="SEAUOMQTY_'.$index.'"  id="SEAUOMQTY_'.$index.'"  class="form-control" maxlength="13" autocomplete="off" value="'.$row_data['SEAUOMQTY'].'"  readonly/></td>';
                        echo '<td><input type="text" style="width:130px;" name="popupMUOM_'.$index.'"  id="popupMUOM_'.$index.'"  class="form-control"  autocomplete="off" value="'.$row_data['popupMUOM'].'" readonly/></td>';
                        echo '<td hidden><input type="hidden" name="MAIN_UOMID_REF_'.$index.'"  id="MAIN_UOMID_REF_'.$index.'"  class="form-control"  autocomplete="off" value="'.$row_data['MAIN_UOMID_REF'].'" /></td>';
                        echo '<td><input type="text" style="width:130px;" name="SQ_QTY_'.$index.'" onkeyup="dataCal(this.id)"   id="SQ_QTY_'.$index.'"  class="form-control three-digits '.$row_data['ITEM_TYPE'].'SCHEME'.$row_data['SCHEMEID_REF'].'" maxlength="13"  autocomplete="off"  value="'.$row_data['SQ_QTY'].'" /></td>'; 
                        echo '<td hidden><input type="hidden" name="SQ_FQTY_'.$index.'"  id="SQ_FQTY_'.$index.'" class="form-control three-digits" maxlength="13"  autocomplete="off"  value="'.$row_data['SQ_FQTY'].'" readonly/></td>';
                        echo '<td><input type="text" style="width:130px;" name="popupAUOM_'.$index.'"  id="popupAUOM_'.$index.'"  class="form-control"  autocomplete="off" value="'.$row_data['popupAUOM'].'"  readonly/></td>';
                        echo '<td hidden><input type="hidden" name="ALT_UOMID_REF_'.$index.'"  id="ALT_UOMID_REF_'.$index.'"  class="form-control"  autocomplete="off"  value="'.$row_data['ALT_UOMID_REF'].'"  readonly/></td>';
                        echo '<td><input type="text" style="width:130px;" name="ALT_UOMID_QTY_'.$index.'"  id="ALT_UOMID_QTY_'.$index.'"  class="form-control three-digits" maxlength="13" autocomplete="off" value="'.$row_data['ALT_UOMID_QTY'].'"  readonly/></td>'; 
                        
                        echo '<td ><input type="text" style="width:130px;" name="RATEPUOM_'.$index.'" onkeyup=dataCal(this.id),get_delear_customer_price(this.id,"change")"   class="form-control five-digits blurRate"       id="RATEPUOM_'.$index.'" maxlength="13" value="'.$row_data['RATEPUOM'].'"   /></td>';

                        echo '<td><input  '.$AlpsStatus['disabled'].' onkeyup="dataCal(this.id)"  style="width:130px;text-align:right;" type="text" name="DISCPER_'.$index.'"  id="DISCPER_'.$index.'"  class="form-control four-digits" maxlength="8"  autocomplete="off" style="width: 50px;" value="'.$row_data['DISCPER'].'" /></td>';
                        echo '<td><input '.$AlpsStatus['disabled'].' onkeyup="dataCal(this.id)"  style="width:130px;text-align:right;" type="text" name="DISCOUNT_AMT_'.$index.'"  id="DISCOUNT_AMT_'.$index.'"  class="form-control two-digits" maxlength="15"  autocomplete="off"  value="'.$row_data['DISCOUNT_AMT'].'" /></td>';
                        echo '<td><input type="text" name="DISAFTT_AMT_'.$index.'" style="width:130px;text-align:right;"  id="DISAFTT_AMT_'.$index.'" class="form-control two-digits" maxlength="15" autocomplete="off" value="'.$row_data['DISAFTT_AMT'].'" readonly/></td>';
                        echo '<td><input type="text" name="IGST_'.$index.'"  onkeyup="dataCal(this.id)"  style="width:130px;text-align:right;"  id="IGST_'.$index.'"  class="form-control four-digits" maxlength="8"  autocomplete="off"  value="'.$row_data['IGST'].'" readonly/></td>';
                        echo '<td><input type="text" name="IGSTAMT_'.$index.'" style="width:130px;text-align:right;"  id="IGSTAMT_'.$index.'"  class="form-control two-digits" maxlength="15" autocomplete="off" value="'.$row_data['IGSTAMT'].'"  readonly/></td>';
                        echo '<td><input type="text" name="CGST_'.$index.'" onkeyup="dataCal(this.id)"  style="width:130px;text-align:right;" id="CGST_'.$index.'" class="form-control four-digits" maxlength="8" autocomplete="off" value="'.$row_data['CGST'].'" readonly/></td>';
                        echo '<td><input type="text" name="CGSTAMT_'.$index.'" style="width:130px;text-align:right;" id="CGSTAMT_'.$index.'" class="form-control two-digits" maxlength="15" autocomplete="off" value="'.$row_data['CGSTAMT'].'"  readonly/></td>';
                        echo '<td><input type="text" name="SGST_'.$index.'"  onkeyup="dataCal(this.id)"  style="width:130px;text-align:right;" id="SGST_'.$index.'"  class="form-control four-digits" maxlength="8" autocomplete="off" value="'.$row_data['SGST'].'"  readonly/></td>';
                        echo '<td><input type="text" name="SGSTAMT_'.$index.'" style="width:130px;text-align:right;" id="SGSTAMT_'.$index.'" class="form-control two-digits" maxlength="15" autocomplete="off" value="'.$row_data['SGSTAMT'].'"  readonly/></td>';
                        echo '<td><input type="text" name="TGST_AMT_'.$index.'" style="width:130px;text-align:right;" id="TGST_AMT_'.$index.'"  class="form-control two-digits" maxlength="15" autocomplete="off" value="'.$row_data['TGST_AMT'].'"  readonly/></td>';
                        echo '<td><input type="text" name="TOT_AMT_'.$index.'" style="width:130px;text-align:right;" id="TOT_AMT_'.$index.'"  class="form-control two-digits" maxlength="15" autocomplete="off" value="'.$row_data['TOT_AMT'].'" readonly/></td>';
                        echo '<td align="center" >
                        <div style="width: 84px;"><button class="btn add material" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button class="btn remove dmaterial" title="Delete" id="remove_'.$index.'" data-toggle="tooltip"  type="button"><i class="fa fa-trash" ></i></button></div></td>';
                        echo '</tr>';  
                   
                    }
                    echo '</tbody>';
                    echo'</table>';   
    }
    else{
        echo $request['hdnmaterial_Scheme'];
    }

    
    exit();
}




public function GetTaXRate($ITEMID_REF,$Tax_State,$ModuleType){
    $CYID_REF           =   Auth::user()->CYID_REF;
    $BRID_REF           =   Session::get('BRID_REF');
    $FYID_REF           =   Session::get('FYID_REF');
    $ObjData1   =   DB::select("SELECT HSNNORMAL.NRATE,TTYPE.TAX_TYPE FROM TBL_MST_ITEM I
    LEFT JOIN TBL_MST_HSN HSN ON HSN.HSNID=I.HSNID_REF
    LEFT JOIN TBL_MST_HSNNORMAL HSNNORMAL ON HSNNORMAL.HSNID_REF=HSN.HSNID
    LEFT JOIN TBL_MST_TAXTYPE TTYPE ON TTYPE.TAXID=HSNNORMAL.TAXID_REF
    WHERE I.ITEMID=$ITEMID_REF AND TTYPE.FOR_SALE=$ModuleType AND I.CYID_REF=$CYID_REF AND I.BRID_REF =$BRID_REF");
//dd($ObjData1);
    $objTax  = array(
        'IGST'=>0,
        'CGST'=>0,
        'SGST'=>0
    ); 

    if(count($ObjData1) > 0){
        foreach($ObjData1 as $key=>$TaxType){     
        if($TaxType->TAX_TYPE=='IGST' ) {
            $objTax['IGST']=$Tax_State =="OutofState" ? $TaxType->NRATE :0;
        };
        if($TaxType->TAX_TYPE=='CGST' ) {
            $objTax['CGST']=$Tax_State =="WithinState" ? $TaxType->NRATE:0;
        };
        if($TaxType->TAX_TYPE=='SGST' ) {
            $objTax['SGST']=$Tax_State =="WithinState" ? $TaxType->NRATE:0;
        }; 
       
    }
    }

    return $objTax;
}

public function get_delear_customer_price(Request $request){

    $CYID_REF   =   Auth::user()->CYID_REF;
    $BRID_REF   =   Session::get('BRID_REF');
    $FYID_REF   =   Session::get('FYID_REF');

    $ITEMID_REF =   trim($request['ITEMID_REF']);
    $DOC_DATE   =   $request['DOC_DATE'] !=''?date('Y-m-d',strtotime($request['DOC_DATE'])):NULL;
    $TYPE       =   trim($request['TYPE']);
    $item_array =   $request['item_array'];
    $action_type=   $request['action_type'];
    $rate       =   $request['rate'];
    $data_array =   [];

    if(isset($item_array) && !empty($item_array)){

        foreach($item_array as $key=>$val){
            $exp        =   explode('#',$val);
            $TEXT_ID    =   isset($exp[0]) && $exp[0] !=''?$exp[0]:NULL;
            $ITEMID_REF =   isset($exp[1]) && $exp[0] !=''?$exp[1]:NULL;

            if($ITEMID_REF !=''){

                $data   =   DB::select("SELECT TOP 1 
                M.DEALER_PRICE AS DPRICE,
                M.MRP AS CPRICE
                FROM TBL_MST_PRICELIST_MAT M
                INNER JOIN TBL_MST_PRICELIST_HDR H ON H.PLID=M.PLID_REF
                WHERE H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF' AND H.STATUS='A' AND '$DOC_DATE' BETWEEN H.PERIOD_FRDT AND H.PERIOD_TODT AND M.ITEMID_REF='$ITEMID_REF'");

                if(count($data) > 0){

                    if($action_type =='direct'){
                        $CPRICE =   isset($data[0]->CPRICE)?$data[0]->CPRICE:0;
                    }
                    else{
                        $CPRICE =   isset($rate)?$rate:0;
                    }

                    $DPRICE =   isset($data[0]->DPRICE)?$data[0]->DPRICE:0;
                    
                    if($TYPE == 'CUSTOMER'){
                        $PRICE      =   $CPRICE;
                        $COMMISSION =   0;
                    }
                    else if($TYPE == 'DEALER'){
                        $PRICE      =   $DPRICE;
                        $COMMISSION =  $CPRICE >= $DPRICE?($CPRICE-$DPRICE):0;
                    }
                    else{
                        $PRICE      =   0;
                        $COMMISSION =   0;
                    }

                    $data_array[]=array(
                        'RATE'=>$PRICE,
                        'COMMISSION'=>$COMMISSION,
                        'TEXT_ID'=>$TEXT_ID,
                    );
                }
            } 
             
        }
    }

    return Response::json($data_array);
    
}

    
}
