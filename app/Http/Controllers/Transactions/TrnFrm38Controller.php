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
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Helpers\Utils;

class TrnFrm38Controller extends Controller
{
    protected $form_id = 38;
    protected $vtid_ref   = 38;  
  
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];
    
    public function __construct()
    {
        $this->middleware('auth');
    }

   
    public function index(){

        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF'); 

        $objRights = $this->getUserRights(['VTID_REF'=>$this->vtid_ref]);

        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList	=	DB::select("select hdr.SOID,hdr.SONO,hdr.SODT,hdr.CUSTOMERPONO,hdr.OVFDT,hdr.OVTDT,hdr.INDATE,
                        (
                        SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                        LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                        WHERE  AUD.VID=hdr.SOID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                        AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                        ) AS CREATED_BY,

                        hdr.STATUS, sl.SLNAME,
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
                        inner join TBL_TRN_SLSO01_HDR hdr
                        on a.VID = hdr.SOID 
                        and a.VTID_REF = hdr.VTID_REF 
                        and a.CYID_REF = hdr.CYID_REF 
                        and a.BRID_REF = hdr.BRID_REF
                        inner join TBL_MST_SUBLEDGER sl ON hdr.SLID_REF = sl.SGLID  
                        where a.VTID_REF = '$this->vtid_ref'
                        and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                        and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                        ORDER BY hdr.SOID DESC ");

                       
        $REQUEST_DATA   =   array(
            'FORMID'    =>  $this->form_id,
            'VTID_REF'  =>  $this->vtid_ref,
            'USERID'    =>  Auth::user()->USERID,
            'CYID_REF'  =>  Auth::user()->CYID_REF,
            'BRID_REF'  =>  Session::get('BRID_REF'),
            'FYID_REF'  =>  Session::get('FYID_REF'),
        );

        return view('transactions.sales.SalesOrder.trnfrm38',compact(['REQUEST_DATA','objRights','objDataList']));        
    }

    public function ViewReport($request) 
    {
        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
       // dd($myValue);  
        $SOID       =   $myValue['SONO'];
        $Flag       =   $myValue['Flag'];

        $objSalesOrder = DB::table('TBL_TRN_SLSO01_HDR')
        ->where('TBL_TRN_SLSO01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLSO01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        ->where('TBL_TRN_SLSO01_HDR.SOID','=',$SOID)
        ->select('TBL_TRN_SLSO01_HDR.*')
        ->first();
        
        
        $ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
		$result = $ssrs->loadReport(Session::get('ssrs_config')['INSTANCE_NAME'].'/SOPrint');
        
        $reportParameters = array(
            'SONo' => $objSalesOrder->SONO,
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

    public function add(){    

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        
      
        $objothcurrency = $this->GetCurrencyMaster(); 

        $objSON = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',38)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('FYID_REF','=',$FYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_DOCNO_DEFINITION.*')
        ->first();
        
        $objSONO=NULL;
        if(isset($objSON) && !empty($objSON)){

        if($objSON->SYSTEM_GRSR == "1")
        {
            if($objSON->PREFIX_RQ == "1")
            {
                $objSONO = $objSON->PREFIX;
            }        
            if($objSON->PRE_SEP_RQ == "1")
            {
                if($objSON->PRE_SEP_SLASH == "1")
                {
                $objSONO = $objSONO.'/';
                }
                if($objSON->PRE_SEP_HYPEN == "1")
                {
                $objSONO = $objSONO.'-';
                }
            }        
            if($objSON->NO_MAX)
            {   
                $objSONO = $objSONO.str_pad($objSON->LAST_RECORDNO+1, $objSON->NO_MAX, "0", STR_PAD_LEFT);
            }
            
            if($objSON->NO_SEP_RQ == "1")
            {
                if($objSON->NO_SEP_SLASH == "1")
                {
                $objSONO = $objSONO.'/';
                }
                if($objSON->NO_SEP_HYPEN == "1")
                {
                $objSONO = $objSONO.'-';
                }
            }
            if($objSON->SUFFIX_RQ == "1")
            {
                $objSONO = $objSONO.$objSON->SUFFIX;
            }
        }

        }
        

        $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
            WHERE  CYID_REF = ?    AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by TNC_CODE ASC', [$CYID_REF,  'A' ]);

        $objCalculationHeader	=   Helper::getCalculationHeader(array(
                                    'CYID_REF'=>Auth::user()->CYID_REF,
                                    'BRID_REF'=>Session::get('BRID_REF'),
                                    'USERID'=>Auth::user()->USERID,
                                    'HEADING'=>'Transactions',
                                    'VTID_REF'=>$this->vtid_ref,
                                    'FORMID'=>$this->form_id
                                    ));
                                    //dd($objCalculationHeader); 
        
        $ObjUnionUDF = DB::table("TBL_MST_UDFFORSO")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                                {       
                                $query->select('UDFID')->from('TBL_MST_UDFFORSO')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF)
                                                ->where('BRID_REF','=',$BRID_REF);
                                                                     
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF);
                                   
                   

        $objUdfSOData = DB::table('TBL_MST_UDFFORSO')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdfSOData);
    

        $objSalesPerson = $this->get_employee_mapping([]);


        $ObjSalesQuotationData = DB::table("TBL_TRN_SLQT01_HDR")->select('*')
                    ->whereNotIn('SQID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('SQID_REF')->from('TBL_TRN_SLQT02_HDR')
                                                ->where('STATUS','=','A')
                                                ->where('CYID_REF','=',$CYID_REF)
                                               // ->where('BRID_REF','=',$BRID_REF)
                                                ->where('FYID_REF','=',$FYID_REF);                       
                    })->where('STATUS','=','A')                    
                    ->where('CYID_REF','=',$CYID_REF)
                    //->where('BRID_REF','=',$BRID_REF)
                    ->where('FYID_REF','=',$FYID_REF) ->get() ->toArray();                    
                   

        $objSalesQuotationAData = DB::table('TBL_TRN_SLQT02_HDR')->select('*')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',$CYID_REF)
            //->where('BRID_REF','=',$BRID_REF)
            ->where('FYID_REF','=',$FYID_REF)
            ->get() ->toArray();  


        $objlastdt  =   DB::select('SELECT MAX(SODT) SODT FROM TBL_TRN_SLSO01_HDR  
                        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
                        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);

        $FormId = $this->form_id;

        
        $AlpsStatus =   $this->AlpsStatus();

        $objTemplateMaster  =$this->getTemplateMaster("SALES");

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        //dd($objcurrency); 
   
    return view('transactions.sales.SalesOrder.trnfrm38add',
    compact(['objCalculationHeader','objUdfSOData','objTNCHeader','objothcurrency','objSON','objSalesPerson','objSalesQuotationAData','ObjSalesQuotationData','objCountUDF','objSONO','FormId','AlpsStatus','objlastdt','objTemplateMaster','TabSetting']));       
   }

   public function getsubledger(Request $request){
    $Status = "A";
    $CYID_REF = Auth::user()->CYID_REF;
    $BRID_REF = Session::get('BRID_REF');
    $CODE = $request['CODE'];
    $NAME = $request['NAME'];

    $sp_popup = [
        $CYID_REF, $BRID_REF,$CODE,$NAME
    ]; 
    
        $ObjData = DB::select('EXEC SP_GET_DEALER_CUSTOMER_SQ ?,?,?,?', $sp_popup);

        if(!empty($ObjData)){

        foreach ($ObjData as $index=>$dataRow){
        
            $row = '';
            $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_SLID_REF[]" id="subgl_'.$index.'" class="clssubgl" value="'.$dataRow-> SGLID.'" ></td>';
            $row = $row.'<td class="ROW2">'.$dataRow->SGLCODE;
            $row = $row.'<input type="hidden" id="txtsubgl_'.$index.'" data-desc="'.$dataRow->SGLCODE .' - ';
            $row = $row.$dataRow->SLNAME. '" data-desc2="'.$dataRow->GLID_REF.'" data-desc3="'.$dataRow->CUSTOMER_TYPE.'" value="'.$dataRow->SGLID.'"/></td><td class="ROW3">'.$dataRow->SLNAME.'</td></tr>';


            echo $row;
        }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();

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
        $id         =   implode(',',$request['id']);
        $ObjData    =   DB::select("SELECT T1.TID,T1.COMPONENT,T1.SQNO,T1.BASIS,T1.RATEPERCENTATE,T1.AMOUNT,T1.FORMULA,T1.GST,T1.ACTUAL,T2.TYPE
        FROM TBL_MST_CALCULATIONTEMPLATE T1
        INNER JOIN TBL_MST_CALCULATION T2 ON T1.CTID_REF=T2.CTID
        WHERE T1.CTID_REF IN($id)");

        
            if(!empty($ObjData)){
                $row = '';
                foreach ($ObjData as $index=>$dataRow){
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
          
        if(isset($request['id']) && !empty($request['id'])){

            $id         =   implode(',',$request['id']);

            $ObjData    =   DB::select("SELECT T1.CTID_REF,T1.TID,T1.COMPONENT,T1.SQNO,T1.BASIS,T1.RATEPERCENTATE,T1.AMOUNT,T1.FORMULA,T1.GST,T1.ACTUAL,T2.CTCODE,T2.TYPE
            FROM TBL_MST_CALCULATIONTEMPLATE T1
            INNER JOIN TBL_MST_CALCULATION T2 ON T1.CTID_REF=T2.CTID
            WHERE T1.CTID_REF IN($id)");

            if(isset($ObjData) && !empty($ObjData)){


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
                    <td><input type="text"  class="form-control" autocomplete="off" value="'.$dataRow->CTCODE.'" readonly  /></td>
                    <td hidden><input type="hidden" name="CTID_REF_'.$dindex.'" id="CTID_REF_'.$dindex.'" value="'.$dataRow->CTID_REF.'" /></td>
                    <td hidden><input type="hidden" name="CT_TYPE_'.$dindex.'" id="CT_TYPE_'.$dindex.'" value="'.$dataRow->TYPE.'" /></td>
                    <td><input type="text" name="popupTID_'.$dindex.'" id="popupTID_'.$dindex.'" class="form-control"  autocomplete="off" value="'.$dataRow->COMPONENT.'"  readonly/></td>
                    <td hidden><input type="hidden" name="TID_REF_'.$dindex.'" id="TID_REF_'.$dindex.'" class="form-control" value="'.$dataRow->TID.'" autocomplete="off" /></td>
                    <td><input type="text" name="RATE_'.$dindex.'" id="RATE_'.$dindex.'" class="form-control four-digits"  value="'.$dataRow->RATEPERCENTATE.'" maxlength="6" autocomplete="off"  readonly/></td>
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
            
            }
            else{
                echo '
                <tr  class="participantRow5">
                <td><input type="text" class="form-control" autocomplete="off" readonly  /></td>
                <td hidden><input type="hidden" name="CTID_REF_0" id="CTID_REF_0"/></td>
                <td hidden><input type="hidden" name="CT_TYPE_0" id="CT_TYPE_0" /></td>
                <td><input type="text" name="popupTID_0" id="popupTID_0" class="form-control"  autocomplete="off"  readonly/></td>
                <td hidden><input type="hidden" name="TID_REF_0" id="TID_REF_0" class="form-control" autocomplete="off" /></td>
                <td><input type="text" name="RATE_0" id="RATE_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                <td hidden><input type="hidden" name="BASIS_0" id="BASIS_0" class="form-control" autocomplete="off" /></td>
                <td hidden><input type="hidden" name="SQNO_0" id="SQNO_0" class="form-control" autocomplete="off" /></td>
                <td><input type="text" name="VALUE_0" id="VALUE_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                <td style="text-align:center;" ><input type="checkbox" class="filter-none" name="calGST_0" id="calGST_0" value="" ></td>
                <td><input type="text" name="calIGST_0" id="calIGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                <td><input type="text" name="AMTIGST_0" id="AMTIGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                <td><input type="text" name="calCGST_0" id="calCGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                <td><input type="text" name="AMTCGST_0" id="AMTCGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                <td><input type="text" name="calSGST_0" id="calSGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                <td><input type="text" name="AMTSGST_0" id="AMTSGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                <td><input type="text" name="TOTGSTAMT_0" id="TOTGSTAMT_0" class="form-control two-digits"  maxlength="15" autocomplete="off"  readonly/></td>
                <td style="text-align:center;"><input type="checkbox" class="filter-none" name="calACTUAL_0" id="calACTUAL_0" value=""   ></td>
                <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                </tr>
                ';
                }
        }
        else{
            echo '
            <tr  class="participantRow5">
            <td><input type="text" class="form-control" autocomplete="off" readonly  /></td>
            <td hidden><input type="hidden" name="CTID_REF_0" id="CTID_REF_0"/></td>
            <td hidden><input type="hidden" name="CT_TYPE_0" id="CT_TYPE_0" /></td>
            <td><input type="text" name="popupTID_0" id="popupTID_0" class="form-control"  autocomplete="off"  readonly/></td>
            <td hidden><input type="hidden" name="TID_REF_0" id="TID_REF_0" class="form-control" autocomplete="off" /></td>
            <td><input type="text" name="RATE_0" id="RATE_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
            <td hidden><input type="hidden" name="BASIS_0" id="BASIS_0" class="form-control" autocomplete="off" /></td>
            <td hidden><input type="hidden" name="SQNO_0" id="SQNO_0" class="form-control" autocomplete="off" /></td>
            <td><input type="text" name="VALUE_0" id="VALUE_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
            <td style="text-align:center;" ><input type="checkbox" class="filter-none" name="calGST_0" id="calGST_0" value="" ></td>
            <td><input type="text" name="calIGST_0" id="calIGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
            <td><input type="text" name="AMTIGST_0" id="AMTIGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
            <td><input type="text" name="calCGST_0" id="calCGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
            <td><input type="text" name="AMTCGST_0" id="AMTCGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
            <td><input type="text" name="calSGST_0" id="calSGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
            <td><input type="text" name="AMTSGST_0" id="AMTSGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
            <td><input type="text" name="TOTGSTAMT_0" id="TOTGSTAMT_0" class="form-control two-digits"  maxlength="15" autocomplete="off"  readonly/></td>
            <td style="text-align:center;"><input type="checkbox" class="filter-none" name="calACTUAL_0" id="calACTUAL_0" value=""   ></td>
            <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
            </tr>
            ';
        }
        exit();
        
    }

    public function getcalculationdetails3(Request $request){

        if(isset($request['id']) && !empty($request['id'])){
            $id         =   implode(',',$request['id']);
            $ObjData    =   DB::select("SELECT T1.TID,T1.COMPONENT,T1.SQNO,T1.BASIS,T1.RATEPERCENTATE,T1.AMOUNT,T1.FORMULA,T1.GST,T1.ACTUAL,T2.TYPE
            FROM TBL_MST_CALCULATIONTEMPLATE T1
            INNER JOIN TBL_MST_CALCULATION T2 ON T1.CTID_REF=T2.CTID
            WHERE T1.CTID_REF IN($id)");

            $ObjDataCount = count($ObjData);
        }
        else{
            $ObjDataCount = 0;
        }

        echo $ObjDataCount;            
        exit();
    }

    public function getsalesquotation(Request $request){
        $Status = "A";
        $id = $request['id'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $SP_PARAMETERS = [$CYID_REF,$BRID_REF,$FYID_REF,$id];

        $ObjData =  DB::select('EXEC SP_SQ_GETLIST ?,?,?,?', $SP_PARAMETERS);
            if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){

                $row = '';
                $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_SQID[]" id="sqcode_'.$index.'" class="clssqid" value="'.$dataRow-> SQID.'" ></td>';
                $row = $row.'<td class="ROW2">'.$dataRow->SQNO;
                $row = $row.'<input type="hidden" id="txtsqcode_'.$index.'" data-desc="'.$dataRow->SQNO .'" data-leadno="'.$dataRow->LEAD_NO .'" data-leaddt="'.$dataRow->LEAD_DT .'"   value="'.$dataRow->SQID.'"/></td><td class="ROW3">'.$dataRow->SQDT.'</td></tr>';

                echo $row;
            }
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    
        }

    public function getItemDetailsQuotationwise(Request $request){
        $Status = "A";
        $id = $request['id'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $taxstate = $request['taxstate'];         
        $contains = Str::contains($id, 'A');

        $AlpsStatus =   $this->AlpsStatus();

        

        if($contains)
        {
           
            $QuoteID = DB::select('SELECT * FROM TBL_TRN_SLQT02_HDR
                        WHERE SQANO = ? AND CYID_REF = ? AND BRID_REF = ? AND FYID_REF = ?
                        AND ',
                        [$id,$CYID_REF,$BRID_REF,$FYID_REF]);
            
            $SQAID = $QuoteID[0]->SQAID;


            //$Objquote =  DB::select('SELECT * FROM TBL_TRN_SLQT02_MAT  WHERE  PENDING_QTY > ? AND SQAID_REF = ? order by ITEMID_REF ASC', ['0.000',$SQAID]);

            $Objquote =  DB::select("SELECT * FROM (
                SELECT * FROM TBL_TRN_SLQT02_MAT  B
                WHERE B.PENDING_QTY > '0.000' AND B.SQAID_REF = ".$SQAID."
                AND B.ITEMID_REF NOT IN (SELECT A.ITEMID_REF FROM TBL_TRN_SLSO01_MAT A (NOLOCK) WHERE A.SQA = SQAID_REF)
                UNION 
                SELECT * FROM TBL_TRN_SLQT02_MAT  B
                WHERE B.SQAID_REF = ".$SQAID."
                AND B.ITEMID_REF IN (SELECT A.ITEMID_REF FROM TBL_TRN_SLSO01_MAT A (NOLOCK) WHERE A.SQA = SQAID_REF)
                ) C
                order by C.ITEMID_REF ASC");


             if(!empty($Objquote)){

                foreach ($Objquote as $index=>$dataRow){

                    $ObjEnquiry = DB::select('SELECT TOP 1 * FROM TBL_TRN_SLEQ01_HDR
                                        WHERE SEQID=?',[$dataRow->SEQID_REF]);
                    
                    $ObjItem =  DB::select('SELECT top 1 ITEMID,ICODE, NAME,ITEMGID_REF,ICID_REF,BUID_REF,STDCOST,ITEM_SPECI,ALPS_PART_NO,
                                CUSTOMER_PART_NO,OEM_PART_NO,MAIN_UOMID_REF,ALT_UOMID_REF,OFFER_STATUS FROM TBL_MST_ITEM WHERE ITEMID = ? ', 
                                [$dataRow->ITEMID_REF]);
                                
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
                                                WHERE STATUS= ? AND ITEMID = ? ', ['A',$dataRow->ITEMID]);
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
                                                WHERE STATUS= ? AND ITEMID = ? ', ['A',$dataRow->ITEMID]);
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
                                            $StdCost = $dataRow->RATEPUOM;
                                        }
                    
                    $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ?  AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $dataRow->MAIN_UOMID_REF, $Status ]);

                    $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ?  AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $dataRow->ALT_UOMID_REF, $Status ]);
                    
                    $ObjAltQTY =  DB::select('SELECT TOP 1  * FROM TBL_MST_ITEM_UOMCONV  
                                WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                                [$dataRow->ITEMID_REF,$dataRow->ALT_UOMID_REF ]);
                    
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


                    
                    if(!is_null($ObjItem[0]->BUID_REF)){
                        $ObjBusinessUnit =  DB::select('SELECT TOP 1  * FROM TBL_MST_BUSINESSUNIT  
                        WHERE  CYID_REF = ? AND BRID_REF = ?  AND BUID = ?', 
                        [$CYID_REF, $BRID_REF, $ObjItem[0]->BUID_REF]);
                    }
                    else
                    {
                        $ObjBusinessUnit = NULL;
                    }
                    
                    $BusinessUnit       =   isset($ObjBusinessUnit) && $ObjBusinessUnit != NULL ? $ObjBusinessUnit[0]->BUCODE.'-'.$ObjBusinessUnit[0]->BUNAME : '';
                    $ALPS_PART_NO       =   $ObjItem[0]->ALPS_PART_NO;
                    $CUSTOMER_PART_NO   =   $ObjItem[0]->CUSTOMER_PART_NO;
                    $OEM_PART_NO        =   $ObjItem[0]->OEM_PART_NO;
                    $offer_status       =   $ObjItem[0]->OFFER_STATUS;

                    
                    
                    $row = '';
                    if($taxstate != "OutofState"){
                        $row = $row.'<tr id="item_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td style="width:10%;">'.$ObjItem[0]->ICODE;
                        $row = $row.'<input type="hidden" id="txtitem_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ICODE.'"  data-desc1="'.$dataRow->DISCPER.'" data-desc2="'.$dataRow->DISCOUNT_AMT.'" data-desc10="'.$offer_status.'"
                        value="'.$ObjItem[0]->ITEMID.'"/></td><td style="width:10%;" id="itemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" >'.$ObjItem[0]->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ITEM_SPECI.'"
                        value="'.$ObjItem[0]->NAME.'"/></td>';
                        $row = $row.'<td style="width:8%;" id="itemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtitemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                        data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td style="width:8%;" id="uomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtuomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$TOQTY.'"
                        value="'.$dataRow->ALT_UOMID_REF.'"/>'.$dataRow->SQ_QTY.'</td>';
                        $row = $row.'<td style="width:8%;" id="irate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtirate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$FROMQTY.'"
                        value="'.$dataRow->RATEPUOM.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                        $row = $row.'<td style="width:8%;" id="itax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$Taxid[0].'"
                        value="'.$Taxid[1].'"/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                        
                        <td style="width:8%;">'.$BusinessUnit.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                        <td style="width:8%;" id="ise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"  data-desc="'.$dataRow->SEQID_REF.'"
                        value="'.$QuoteID[0]->SQNO.'"/>Authorized</td>
                        </tr>';
                        }
                        else
                        {
                        $row = $row.'<tr id="item_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td style="width:10%;">'.$ObjItem[0]->ICODE;
                        $row = $row.'<input type="hidden" id="txtitem_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ICODE.'"  data-desc1="'.$dataRow->DISCPER.'" data-desc2="'.$dataRow->DISCOUNT_AMT.'" data-desc10="'.$offer_status.'"
                        value="'.$ObjItem[0]->ITEMID.'"/></td><td style="width:10%;" id="itemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" >'.$ObjItem[0]->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ITEM_SPECI.'"
                        value="'.$ObjItem[0]->NAME.'"/></td>';
                        $row = $row.'<td style="width:8%;" id="itemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtitemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                        data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td style="width:8%;" id="uomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtuomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$TOQTY.'"
                        value="'.$dataRow->ALT_UOMID_REF.'"/>'.$dataRow->SQ_QTY.'</td>';
                        $row = $row.'<td style="width:8%;" id="irate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtirate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$FROMQTY.'"
                        value="'.$dataRow->RATEPUOM.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                        $row = $row.'<td style="width:8%;" id="itax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$Taxid[0].'"
                        value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                        
                        <td style="width:8%;">'.$BusinessUnit.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                        <td style="width:8%;" id="ise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$dataRow->SEQID_REF.'"
                        value="'.$QuoteID[0]->SQNO.'"/>Authorized</td>
                        </tr>';
                        }
         
                 echo $row;
                }
         
             }else{
                 echo '<tr><td> Record not found.</td></tr>';
             }


        }
        else{
           

            $QuoteID = DB::select('SELECT * FROM TBL_TRN_SLQT01_HDR
                    WHERE SQNO = ? AND CYID_REF = ? AND BRID_REF = ? AND FYID_REF = ?',
                    [$id,$CYID_REF,$BRID_REF,$FYID_REF]);
            
            $SQAID = $QuoteID[0]->SQID;


                   // $Objquote =  DB::select('SELECT * FROM TBL_TRN_SLQT01_MAT  WHERE PENDING_QTY > ? AND SQID_REF = ? order by SQMATID ASC', ['0.000',$SQAID]);

                    $Objquote =  DB::select("SELECT * FROM (
                        SELECT * FROM TBL_TRN_SLQT01_MAT  B
                        WHERE B.PENDING_QTY > '0.000' AND B.SQID_REF = ".$SQAID."
                        AND B.ITEMID_REF NOT IN (SELECT A.ITEMID_REF FROM TBL_TRN_SLSO01_MAT A (NOLOCK) WHERE A.SQA = SQID_REF)
                        UNION 
                        SELECT * FROM TBL_TRN_SLQT01_MAT  B
                        WHERE B.SQID_REF = ".$SQAID."
                        AND B.ITEMID_REF IN (SELECT A.ITEMID_REF FROM TBL_TRN_SLSO01_MAT A (NOLOCK) WHERE A.SQA = SQID_REF)
                        ) C
                        order by C.ITEMID_REF ASC");

                    

                    if(!empty($Objquote)){

                        foreach ($Objquote as $index=>$dataRow){

                            $ObjEnquiry = DB::select('SELECT TOP 1 * FROM TBL_TRN_SLEQ01_HDR
                                        WHERE SEQID=?',[$dataRow->SEQID_REF]);
                            
                            $ObjItem =  DB::select('SELECT top 1 ITEMID,ICODE, NAME,ITEMGID_REF,ICID_REF,BUID_REF,STDCOST,
                            ITEM_SPECI,ALPS_PART_NO, CUSTOMER_PART_NO,OEM_PART_NO,MAIN_UOMID_REF,ALT_UOMID_REF,OFFER_STATUS 
                            FROM TBL_MST_ITEM WHERE ITEMID = ? ', [$dataRow->ITEMID_REF]);

                          

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
                                                                        })->where('HSNID_REF','=',isset($ObjHSN[0]->HSNID_REF) ? $ObjHSN[0]->HSNID_REF :'' )
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
                                                    $StdCost = $dataRow->RATEPUOM;
                                                }
                        
                            $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                        WHERE  CYID_REF = ?  AND UOMID = ? 
                                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                        [$CYID_REF, $dataRow->MAIN_UOMID_REF, $Status ]);

                            $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                        WHERE  CYID_REF = ?  AND UOMID = ? 
                                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                        [$CYID_REF, $dataRow->ALT_UOMID_REF, $Status ]);
                            
                            $ObjAltQTY =  DB::select('SELECT TOP 1  * FROM TBL_MST_ITEM_UOMCONV  
                                        WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                                        [$dataRow->ITEMID_REF,$dataRow->ALT_UOMID_REF ]);
                            
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



                            

                            if(!is_null($ObjItem[0]->BUID_REF)){
                                $ObjBusinessUnit =  DB::select('SELECT TOP 1  * FROM TBL_MST_BUSINESSUNIT  
                                WHERE  CYID_REF = ? AND BRID_REF = ?  AND BUID = ?', 
                                [$CYID_REF, $BRID_REF, $ObjItem[0]->BUID_REF]);
                            }
                            else
                            {
                                $ObjBusinessUnit = NULL;
                            }
                            
                            $BusinessUnit       =   isset($ObjBusinessUnit) && $ObjBusinessUnit != NULL ? $ObjBusinessUnit[0]->BUCODE.'-'.$ObjBusinessUnit[0]->BUNAME : '';
                            $ALPS_PART_NO       =   $ObjItem[0]->ALPS_PART_NO;
                            $CUSTOMER_PART_NO   =   $ObjItem[0]->CUSTOMER_PART_NO;
                            $OEM_PART_NO        =   $ObjItem[0]->OEM_PART_NO;
                            $offer_status       =   $ObjItem[0]->OFFER_STATUS;
        
                            $row = '';
                            if($taxstate != "OutofState"){
                            $row = $row.'<tr id="item_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                            $row = $row.'<td style="width:10%;">'.$ObjItem[0]->ICODE;
                            $row = $row.'<input type="hidden" id="txtitem_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ICODE.'"  data-desc1="'.$dataRow->DISCPER.'" data-desc2="'.$dataRow->DISCOUNT_AMT.'" data-desc10="'.$offer_status.'"
                            value="'.$ObjItem[0]->ITEMID.'"/></td><td style="width:10%;" id="itemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" >'.$ObjItem[0]->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ITEM_SPECI.'"
                            value="'.$ObjItem[0]->NAME.'"/></td>';
                            $row = $row.'<td style="width:8%;" id="itemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtitemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                            data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                            $row = $row.'<td style="width:8%;" id="uomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtuomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$TOQTY.'"
                            value="'.$dataRow->ALT_UOMID_REF.'"/>'.$dataRow->SQ_QTY.'</td>';
                            $row = $row.'<td style="width:8%;" id="irate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtirate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$FROMQTY.'"
                            value="'.$dataRow->RATEPUOM.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                            $row = $row.'<td style="width:8%;" id="itax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc=""
                            value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                            
                            <td style="width:8%;">'.$BusinessUnit.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                            <td style="width:8%;" id="ise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"  data-desc="'.$dataRow->SEQID_REF.'"
                            value="'.$QuoteID[0]->SQNO.'"/>Authorized</td>
                            </tr>';
                            }
                            else
                            {
                            $row = $row.'<tr id="item_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                            $row = $row.'<td style="width:10%;">'.$ObjItem[0]->ICODE;
                            $row = $row.'<input type="hidden" id="txtitem_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ICODE.'" data-desc1="'.$dataRow->DISCPER.'" data-desc2="'.$dataRow->DISCOUNT_AMT.'" data-desc10="'.$offer_status.'"
                            value="'.$ObjItem[0]->ITEMID.'"/></td><td style="width:10%;" id="itemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" >'.$ObjItem[0]->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ITEM_SPECI.'"
                            value="'.$ObjItem[0]->NAME.'"/></td>';
                            $row = $row.'<td style="width:8%;" id="itemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtitemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                            data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                            $row = $row.'<td style="width:8%;" id="uomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtuomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$TOQTY.'"
                            value="'.$dataRow->ALT_UOMID_REF.'"/>'.$dataRow->SQ_QTY.'</td>';
                            $row = $row.'<td style="width:8%;" id="irate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtirate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$FROMQTY.'"
                            value="'.$dataRow->RATEPUOM.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                            $row = $row.'<td style="width:8%;" id="itax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$Taxid[0].'"
                            value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                            
                            <td style="width:8%;">'.$BusinessUnit.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                            <td style="width:8%;" id="ise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$dataRow->SEQID_REF.'"
                            value="'.$QuoteID[0]->SQNO.'"/>Authorized</td>
                            </tr>';
                            }

                        echo $row;
                        }

                    }else{
                        echo '<tr><td> Record not found.</td></tr>';
                    }
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

        $AlpsStatus =   $this->AlpsStatus();

        $CODE = $request['CODE'];
        $NAME = $request['NAME'];
        $MUOM = $request['MUOM'];
        $GROUP = $request['GROUP'];
        $CTGRY = $request['CTGRY'];
        $BUNIT = $request['BUNIT'];
        $APART = $request['APART'];
        $CPART = $request['CPART'];
        $OPART = $request['OPART'];

        $sp_popup = [
            $CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART,$taxstate
        ]; 

        //dd($sp_popup); 
        
            $ObjItem = DB::select('EXEC sp_get_items_popup_withtax_SO ?,?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);
        
        
      
        
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
                        $offer_status       =   isset($dataRow->OFFER_STATUS)?$dataRow->OFFER_STATUS:0;

                        $row = '';
                        $row.=' <tr id="item_'.$ITEMID.'" class="clsitemid">
                                <td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1"  ></td>
                                <td style="width:10%;">'.$ICODE.'<input type="hidden" id="txtitem_'.$ITEMID.'" data-desc="'.$ICODE.'" data-desc10="'.$offer_status.'" value="'.$ITEMID.'"/></td>
                                <td style="width:10%;" id="itemname_'.$ITEMID.'" >'.$NAME.'<input type="hidden" id="txtitemname_'.$ITEMID.'" data-desc="'.$ITEM_SPECI.'" value="'.$NAME.'"/></td>
                                <td style="width:8%;" id="itemuom_'.$ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ITEMID.'" data-desc="'.$Alt_UOM.'" 
                                data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>
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

    public function getmainuomqty(Request $request){
            $id = $request['id'];
            $itemid = $request['itemid'];
            $aqty = $request['aqty'];
    
        
            $ObjData =  DB::select('SELECT top 1 TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV  
                        WHERE  ITEMID_REF = ? AND TO_UOMID_REF =?', [$itemid,$id]);
        
             
                    if(!empty($ObjData)){
                    $muomqty = ($aqty*$ObjData[0]->FROM_QTY)/($ObjData[0]->TO_QTY);
                    $muomqty = round($muomqty, 3);
                 //   ($mqty/$ObjData[0]->FROM_QTY)*($ObjData[0]->TO_QTY);
                    echo($muomqty);
        
                    }else{
                        echo '0';
                    }
                    exit();
        
            }

    public function getAltUOM(Request $request){
        $id = $request['id'];

        $ObjData =  DB::select('SELECT TO_UOMID_REF FROM TBL_MST_ITEM_UOMCONV  
                WHERE ITEMID_REF= ?  order by IUCID ASC', [$id]);

        if(!empty($ObjData)){

        foreach ($ObjData as $index=>$dataRow){

            $ObjAltUOM =  DB::select('SELECT top 1 UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                WHERE UOMID= ?  ', [$dataRow->TO_UOMID_REF]);        
            
            $row = '';
            $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_TO_UOMID_REF[]" id="altuom_'.$index.'" class="clsaltuom" value="'.$dataRow-> TO_UOMID_REF.'" ></td>';
            $row = $row.'<td class="ROW2">'.$ObjAltUOM[0]->UOMCODE;
            $row = $row.'<input type="hidden" id="txtaltuom_'.$index.'" data-desc="'.$ObjAltUOM[0]->UOMCODE .' - ';
            $row = $row.$ObjAltUOM[0]->DESCRIPTIONS. '" value="'.$dataRow->TO_UOMID_REF.'"/></td><td class="ROW3">'.$ObjAltUOM[0]->DESCRIPTIONS.'</td></tr>';


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

        public function getShipTo(Request $request){
            $Status = "A";
            $id = $request['id'];
            $BRID_REF = Session::get('BRID_REF');
            

            $ObjCust =  DB::select('SELECT top 1 CID,TAX_CALCULATION FROM TBL_MST_CUSTOMER WHERE STATUS= ? AND SLID_REF = ? ', [$Status,$id]);
            $cid     = $ObjCust[0]->CID;

            $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_CUSTOMERLOCATION WHERE DEFAULT_SHIPTO= ? AND CID_REF = ? ', [1,$cid]);

        
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
                        $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_CLID[]" id="billto_'.$index.'" class="clsbillto" value="'.$dataRow-> CLID.'" ></td>';
                        $row = $row.'<td class="ROW2">'.$dataRow->NAME;
                        $row = $row.'<input type="hidden" id="txtbillto_'.$index.'" data-desc="'.$objAddress.'" data-desc2="'.$TAXSTATE.'" data-desc3="'.$ObjCust[0]->TAX_CALCULATION.'" value="'.$dataRow->CLID.'"/></td><td class="ROW3">'.$objAddress.'</td></tr>';

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
                $ObjCust =  DB::select('SELECT top 1 CID,TAX_CALCULATION FROM TBL_MST_CUSTOMER  
                        WHERE STATUS= ? AND SLID_REF = ? ', [$Status,$id]);
        
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
                        $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_CLID[]" id="shipto_'.$index.'" class="clsshipto" value="'.$dataRow-> CLID.'" ></td>';
                        $row = $row.'<td class="ROW2">'.$dataRow->NAME;
                        $row = $row.'<input type="hidden" id="txtshipto_'.$index.'" data-desc="'.$objAddress.'" data-desc2="'.$TAXSTATE.'" data-desc3="'.$ObjCust[0]->TAX_CALCULATION.'" value="'.$dataRow->CLID.'"/></td><td class="ROW3">'.$objAddress.'</td></tr>';

                        echo $row;
                    }
            
                    }else{
                        echo '<tr><td colspan="2">Record not found.</td></tr>';
                    }
                    exit();
                }
            }


   
   public function attachment($id){

    if(!is_null($id))
    {
        $objSalesorder = DB::table("TBL_TRN_SLSO01_HDR")
                        ->where('SOID','=',$id)
                        ->select('TBL_TRN_SLSO01_HDR.*')
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

               

            return view('transactions.sales.SalesOrder.trnfrm38attachment',compact(['objSalesorder','objMstVoucherType','objAttachments']));
    }

}

    
   public function save(Request $request) {

    
     
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];
        $r_count5 = $request['Row_Count5'];
        $r_count6 = $request['Row_Count6'];

        $GROSS_TOTAL    =   0; 
        $NET_TOTAL = $request['TotalValue'];
        $CGSTAMT        =   0; 
        $SGSTAMT        =   0; 
        $IGSTAMT        =   0; 
        $DISCOUNT       =   0; 
        $OTHER_CHARGES  =   0; 
        $TDS_AMOUNT     =   0; 
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i]))
            {
                $GROSS_TOTAL+= $request['DISAFTT_AMT_'.$i]; 
                $CGSTAMT+= $request['CGSTAMT_'.$i]; 
                $SGSTAMT+= $request['SGSTAMT_'.$i]; 
                $IGSTAMT+= $request['IGSTAMT_'.$i]; 
                $DISCOUNT+= $request['DISCOUNT_AMT_'.$i]; 

                $req_data[$i] = [
                    'SRNO' => $i+1,
                    'SQA' => (!empty($request['SQA_'.$i])) == 'true' ? $request['SQA_'.$i] : "0" ,
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'ITEMSPECI' => $request['Itemspec_'.$i],
                    'MAIN_UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'SO_QTY' => $request['SO_QTY_'.$i],
                    'ALT_UOMID_REF' => $request['ALT_UOMID_REF_'.$i],
                    'ALT_QTY' => $request['ALT_UOMID_QTY_'.$i],
                    'RATEPUOM' => $request['RATEPUOM_'.$i],
                    'DISCPER'    => (!empty($request['DISCPER_'.$i])) == 'true' ? $request['DISCPER_'.$i] : 0,
                    'DISCOUNT_AMT' => (!empty($request['DISCOUNT_AMT_'.$i])) == 'true' ? $request['DISCOUNT_AMT_'.$i] : 0,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'SEQID_REF'=> (!empty($request['SEQID_REF_'.$i])) == 'true' ? $request['SEQID_REF_'.$i] : "0" ,
                    'SCHEMEID_REF'       => $request['SCHEMEID_REF_'.$i]!='' ? $request['SCHEMEID_REF_'.$i] :  NULL,
                    'ITEM_TYPE'          => $request['ITEM_TYPE_'.$i] !="" ? $request['ITEM_TYPE_'.$i] : "OTHER",   
                    'SCHEMEQTY' => (!empty($request['SCHEMEQTY_'.$i]) ? $request['SCHEMEQTY_'.$i] : 0),           
                ];

               

                $tsp_data    =   array();
                if(isset($request['TSID_REF_'.$i]) && $request['TSID_REF_'.$i] !=''){
                    $tspid_array =   explode(',',$request['TSID_REF_'.$i]);
                    foreach($tspid_array as $tsid){
                        $tsp_data[] = [
                            'SERIALNO'      =>  $i+1,
                            'TSID_REF'      =>  $tsid,
                            'ITEMID_REF'      =>  $request['ITEMID_REF_'.$i]
                        ];
                    }
                }
                
                $req_data[$i]['TSP']=$tsp_data;
                
            }
        }



        //dd($req_data);

  
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
                if(isset($request['UDFSOID_REF_'.$i]) && !is_null($request['UDFSOID_REF_'.$i]))
                {
                    $reqdata3[$i] = [
                        'UDFSOID_REF'   => $request['UDFSOID_REF_'.$i],
                        'SOUVALUE'      => $request['udfvalue_'.$i],
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
                    if(isset($request['TID_REF_'.$i]))
                    {
                        if($request['CT_TYPE_'.$i]=="DISCOUNT"){
                          
                            $DISCOUNT      += $request['VALUE_'.$i]; 
                        }else{
                            $OTHER_CHARGES += $request['VALUE_'.$i];   
                        }
                        $reqdata4[$i] = [
                            'CTID_REF'      => $request['CTID_REF_'.$i] ,
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
        
        for ($i=0; $i<=$r_count5; $i++)
        {
                if(isset($request['PAY_DAYS_'.$i]) && !is_null($request['PAY_DAYS_'.$i]))
                {
                    $reqdata5[$i] = [
                        'PAY_DAYS'      => $request['PAY_DAYS_'.$i],
                        'DUE'           => $request['DUE_'.$i],
                        'REMARKS'       => $request['PSREMARKS_'.$i],
                        'DUE_DATE'      => $request['DUE_DATE_'.$i],
                    ];
                }
            
        }
            if(isset($reqdata5))
            { 
                $wrapped_links5["PSLB"] = $reqdata5; 
                $XMLPSLB = ArrayToXml::convert($wrapped_links5);
            }
            else
            {
                $XMLPSLB = NULL; 
            }

            for ($i=0; $i<=$r_count6; $i++){
                if(isset($request['TDSID_REF_'.$i]) && $request['TDSID_REF_'.$i] !=''){
                    if(isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0==1){
                        $TDS_AMOUNT      += $request['TDS_AMT_'.$i]; 
                        }
                    $reqdata6[$i] = [
                        'TDSID_REF'                 => $request['TDSID_REF_'.$i],
                        'ASSESSABLE_VL_TDS'         => (!is_null($request['ASSESSABLE_VL_TDS_'.$i]) ? $request['ASSESSABLE_VL_TDS_'.$i] : 0),
                        'TDS_RATE'                  => (!is_null($request['TDS_RATE_'.$i]) ? $request['TDS_RATE_'.$i] : 0),
                        'ASSESSABLE_VL_SURCHARGE'   => (!is_null($request['ASSESSABLE_VL_SURCHARGE_'.$i]) ? $request['ASSESSABLE_VL_SURCHARGE_'.$i] : 0),
                        'SURCHARGE_RATE'            => (!is_null($request['SURCHARGE_RATE_'.$i]) ? $request['SURCHARGE_RATE_'.$i] : 0),
                        'ASSESSABLE_VL_CESS'        => (!is_null($request['ASSESSABLE_VL_CESS_'.$i]) ? $request['ASSESSABLE_VL_CESS_'.$i] : 0),
                        'CESS_RATE'                 => (!is_null($request['CESS_RATE_'.$i]) ? $request['CESS_RATE_'.$i] : 0),
                        'ASSESSABLE_VL_SPCESS'      => (!is_null($request['ASSESSABLE_VL_SPCESS_'.$i]) ? $request['ASSESSABLE_VL_SPCESS_'.$i] : 0),
                        'SPCESS_RATE'               => (!is_null($request['SPCESS_RATE_'.$i]) ? $request['SPCESS_RATE_'.$i] : 0),
                    ];
                }
            }
            
            if(isset($reqdata6)){ 
                $wrapped_links6["TDSD"] = $reqdata6; 
                $XMLTDSD = ArrayToXml::convert($wrapped_links6);
            }
            else{
                $XMLTDSD = NULL; 
            }


     
            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $SONO = $request['SONO'];
            $SODT = $request['SODT'];
            $GLID_REF = $request['GLID_REF'];
            $SLID_REF = $request['SLID_REF'];
            $SOFC = (isset($request['SOFC'])!="true" ? 0 : 1);
            $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
            $CONVFACT = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
            $OVFDT = $request['OVFDT'];
            $OVTDT = $request['OVTDT'];
            $CUSTOMERPONO = $request['CUSTOMERPONO'];
            $CUSTOMERDT = $request['CUSTOMERDT'];
            $SPID_REF = $request['SPID_REF'];
            $REFNO = $request['REFNO'];
            $CREDITDAYS = $request['CREDITDAYS'];
            $BILLTO = $request['BILLTO'];
            $SHIPTO = $request['SHIPTO'];

             $REMARKS = $request['REMARKS'];
             $GST_N_Avail = (isset($request['GST_N_Avail'])!="true" ? 0 : 1);
             $GST_Reverse = (isset($request['GST_Reverse'])!="true" ? 0 : 1);
             $EXE_GST = (isset($request['EXE_GST'])!="true" ? 0 : 1);
             $Template_Description = $request['Template_Description'];
             $TDS   = (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);

             $DEALERID_REF           = $request['DEALERID_REF'];
             $PROJECTID_REF          = $request['PROJECTID_REF'];
             $DEALER_COMMISSION_AMT  = $request['DEALER_COMMISSION_AMT'];
             $PRICE_BASED_ON         = $request['PRICE_BASED_ON'] ? $request['PRICE_BASED_ON'] : NULL;
             $BILLTO_SHIPTO         = $request['Tax_State'] ? $request['Tax_State'] : NULL;

            $log_data = [ 
                $SONO,$SODT,$GLID_REF,$SLID_REF,$SOFC,$CRID_REF,$CONVFACT,$OVFDT,$OVTDT,$CUSTOMERPONO,$CUSTOMERDT,
                $SPID_REF,$REFNO,$CREDITDAYS,$BILLTO,$SHIPTO,$REMARKS,$CYID_REF, $BRID_REF, $FYID_REF,$VTID_REF,
                $XMLMAT, $XMLTNC,$XMLUDF,$XMLCAL,$XMLPSLB, $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, 
                $IPADDRESS, $GST_N_Avail,$GST_Reverse,$EXE_GST,$Template_Description,$XMLTDSD,$TDS,$DEALERID_REF,$PROJECTID_REF,$DEALER_COMMISSION_AMT
                ,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT,$PRICE_BASED_ON,$BILLTO_SHIPTO
            ];   

            

                      
            $sp_result = DB::select('EXEC SP_SO_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $log_data);       
            
        
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            exit();   
     }

    public function getTechSpecId($id){
        $data_array=[];
        if($id !=''){
            $data = DB::table('TBL_TRN_SLSO01_TSP')->where('SOMATID_REF','=',$id)->select('TSID_REF')->get();
            if(isset($data) && count($data) > 0){
                foreach($data as $key=>$val){
                    $data_array[]=$val->TSID_REF;
                } 
            }  
        }

        $result =   !empty($data_array)?implode(',',$data_array):'';
        return $result;
    }

    public function edit($id=NULL){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){

            $objSO = DB::table('TBL_TRN_SLSO01_HDR')
                             ->where('TBL_TRN_SLSO01_HDR.FYID_REF','=',Session::get('FYID_REF'))
                             ->where('TBL_TRN_SLSO01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_TRN_SLSO01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                             ->where('TBL_TRN_SLSO01_HDR.SOID','=',$id)
                             ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_SLSO01_HDR.DEALERID_REF','=','TBL_MST_CUSTOMER.SLID_REF')
                             ->leftJoin('TBL_MST_PROJECT', 'TBL_TRN_SLSO01_HDR.PROJECTID_REF','=','TBL_MST_PROJECT.PID')
                             ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_SLSO01_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
                             ->select('TBL_TRN_SLSO01_HDR.*','TBL_MST_CUSTOMER.NAME AS CUSTOMER_NAME','TBL_MST_CUSTOMER.COMMISION','TBL_MST_PROJECT.DESCRIPTIONS AS PROJECT_NAME','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
                             ->first();

     
            $log_data = [ 
                $id
            ];

            $objScheme  =     DB::select("SELECT DISTINCT M.SCHEMEID_REF,S.SCHEME_NAME FROM TBL_TRN_SLSO01_MAT M 
            LEFT JOIN TBL_MST_SCHEME_HDR S ON S.SCHEMEID=M.SCHEMEID_REF
            WHERE M.SOID_REF=$id"); 
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

            $objSOMAT   =   array();
            if(isset($objSO) && !empty($objSO)){

                $objSOMAT = DB::select('EXEC sp_get_sales_order_material ?', $log_data);
            }

          // dd($objSOMAT); 






            if(isset($objSOMAT) && !empty($objSOMAT)){
                foreach($objSOMAT as $key=>$val){ 
                    $data=$this->get_lead($val->SQA);  
                        $objSOMAT[$key]->LEAD_NO    =  $data["LEAD_NO"];
                        $objSOMAT[$key]->LEAD_DT    =  $data["LEAD_DT"]; 

                        $objSOMAT[$key]->TSID_REF    =  $this->getTechSpecId($val->SOMATID);
                }
            }

          
  
            
            $objCount1 = count($objSOMAT);
           

            $objSOTNC = DB::table('TBL_TRN_SLSO01_TNC')                    
                             ->where('TBL_TRN_SLSO01_TNC.SOID_REF','=',$id)
                             ->select('TBL_TRN_SLSO01_TNC.*')
                             ->orderBy('TBL_TRN_SLSO01_TNC.SOTNCID','ASC')
                             ->get()->toArray();
            $objCount2 = count($objSOTNC);

            $objSOUDF = DB::table('TBL_TRN_SLSO01_UDF')                    
                             ->where('TBL_TRN_SLSO01_UDF.SOID_REF','=',$id)
                             ->select('TBL_TRN_SLSO01_UDF.*')
                             ->orderBy('TBL_TRN_SLSO01_UDF.SOUDFID','ASC')
                             ->get()->toArray();
            $objCount3 = count($objSOUDF);

           

            $objSOCAL = DB::table('TBL_TRN_SLSO01_CAL')                    
                             ->where('TBL_TRN_SLSO01_CAL.SOID_REF','=',$id)
                             ->select('TBL_TRN_SLSO01_CAL.*')
                             ->orderBy('TBL_TRN_SLSO01_CAL.SOCALID','ASC')
                             ->get()->toArray();

            $objSOCAL   =   DB::select("SELECT T1.*,T2.CTCODE,T2.TYPE
                            FROM TBL_TRN_SLSO01_CAL T1 
                            INNER JOIN TBL_MST_CALCULATION T2 ON T1.CTID_REF=T2.CTID
                            WHERE SOID_REF='$id'");
            $objCount4  =   count($objSOCAL);

            $objSOPSLB = DB::table('TBL_TRN_SLSO01_PSLB')                    
                             ->where('TBL_TRN_SLSO01_PSLB.SOID_REF','=',$id)
                             ->select('TBL_TRN_SLSO01_PSLB.*')
                             ->orderBy('TBL_TRN_SLSO01_PSLB.PSLBID','ASC')
                             ->get()->toArray();
            $objCount5 = count($objSOPSLB);
     
            $objRights = $this->getUserRights(['VTID_REF'=>$this->vtid_ref]);
                             $TAXSTATE=[];
                             $objShpAddress=[] ;
                             $objBillAddress=[];

                            if(isset($objSO->SHIPTO) && $objSO->SHIPTO !=""){

                             $sid = $objSO->SHIPTO;
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


                            if(isset($objSO->BILLTO) && $objSO->BILLTO !=""){
                            
                            $bid = $objSO->BILLTO;
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
            
            
            $objSPID=array();

            if(isset($objSO->SPID_REF) && $objSO->SPID_REF !=""){

                $objEMP = DB::table('TBL_MST_EMPLOYEE')
                ->where('TBL_MST_EMPLOYEE.EMPID','=',$objSO->SPID_REF)
                ->select('TBL_MST_EMPLOYEE.*')
                ->first();
                $objSPID[] = $objEMP->EMPCODE.'-'.$objEMP->FNAME.' '.$objEMP->MNAME.' '.$objEMP->LNAME;
            }
            
            $objsubglcode=array();
            if(isset($objSO->GLID_REF) && $objSO->GLID_REF !=""){

                $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('GLID_REF','=',$objSO->GLID_REF)
                ->where('SGLID','=',$objSO->SLID_REF)
                ->select('TBL_MST_SUBLEDGER.*')
                ->first();


            }

      

          

            $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
            WHERE  CYID_REF = ? 
            order by CTCODE ASC', [$CYID_REF]);
    
            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
                WHERE  CYID_REF = ?    AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                order by TNC_CODE ASC', [$CYID_REF,  'A' ]);
    
            $objCalculationHeader	=   Helper::getCalculationHeader(array(
                                        'CYID_REF'=>Auth::user()->CYID_REF,
                                        'BRID_REF'=>Session::get('BRID_REF'),
                                        'USERID'=>Auth::user()->USERID,
                                        'HEADING'=>'Transactions',
                                        'VTID_REF'=>$this->vtid_ref,
                                        'FORMID'=>$this->form_id
                                        ));


            
            $ObjUnionUDF = DB::table("TBL_MST_UDFFORSO")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                                    {       
                                    $query->select('UDFID')->from('TBL_MST_UDFFORSO')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF)
                                                    ->where('BRID_REF','=',$BRID_REF);
                                                                       
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF);
                                    
                    
    
            $objUdfSOData = DB::table('TBL_MST_UDFFORSO')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFORSO")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                        {       
                        $query->select('UDFID')->from('TBL_MST_UDFFORSO')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF)
                                        ->where('BRID_REF','=',$BRID_REF);
                                                          
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF);
                   
            

            $objUdfSOData2 = DB::table('TBL_MST_UDFFORSO')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
    
         
    

        
    
            $objSalesPerson = $this->get_employee_mapping([]);



    
            $ObjSalesQuotationData = DB::table("TBL_TRN_SLQT01_HDR")->select('*')
                        ->where('STATUS','=','A')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        //->where('BRID_REF','=',$BRID_REF)
                        ->where('FYID_REF','=',$FYID_REF) ->get() ->toArray();                    
                    
      
            $objSQMAT = DB::table('TBL_TRN_SLQT01_MAT')->select('*')
            ->get() ->toArray();
            
            $objUOM = DB::table('TBL_MST_UOM')->select('*')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',$CYID_REF)
            ->get() ->toArray(); 

            $objItemUOMConv = DB::table('TBL_MST_ITEM_UOMCONV')->select('*')
            ->get() ->toArray(); 

            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
            ->get() ->toArray(); 

            $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')
            ->get() ->toArray();
            
            $FormId = $this->form_id;

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
            if($objAttachments)
            {
                $objCountAttachment = count($objAttachments);
            }
            else
            {
                $objCountAttachment = "0";
            }
            
            
            $AlpsStatus =   $this->AlpsStatus();

            $objSOTDS = DB::select('EXEC SP_GET_SO_TDS ?', $log_data);
              
            $objCount6 = count($objSOTDS);
            $objTemplateMaster  =$this->getTemplateMaster("SALES");
            $ActionStatus   =   "";

            $Template = DB::table('TBL_TRN_SLSO01_ADD_INFO')
            ->where('SOID_REF','=',$id)
            ->select('TBL_TRN_SLSO01_ADD_INFO.TEMPLATE')
            ->first();

            $objlastdt  =   DB::select('SELECT MAX(SODT) SODT FROM TBL_TRN_SLSO01_HDR  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
            [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
            $objothcurrency = $this->GetCurrencyMaster(); 


        return view('transactions.sales.SalesOrder.trnfrm38edit',compact(['objSO','objRights','objCount1','objSPID',
           'objCount2','objCount3','objCount4','objCount5','objSOMAT','objSOCAL','objSOTNC','objSOUDF','objSOPSLB',
           'objCalculationHeader','objUdfSOData','objTNCHeader','objothcurrency','objSalesPerson','objsubglcode','FormId','objCountAttachment',
           'objShpAddress','objBillAddress','objUOM','objItemUOMConv','objTNCDetails','objUdfSOData2',
           'objCalHeader','objCalDetails','TAXSTATE','AlpsStatus','objSOTDS','objCount6','objTemplateMaster','ActionStatus','Template','objlastdt','TabSetting','SchemeId','SchemeName']));
        }
     
    }

     



    public function view($id=NULL){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){
            $objSO = DB::table('TBL_TRN_SLSO01_HDR')
                    ->where('TBL_TRN_SLSO01_HDR.FYID_REF','=',Session::get('FYID_REF'))
                    ->where('TBL_TRN_SLSO01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('TBL_TRN_SLSO01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                    ->where('TBL_TRN_SLSO01_HDR.SOID','=',$id)
                    ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_SLSO01_HDR.DEALERID_REF','=','TBL_MST_CUSTOMER.SLID_REF')
                    ->leftJoin('TBL_MST_PROJECT', 'TBL_TRN_SLSO01_HDR.PROJECTID_REF','=','TBL_MST_PROJECT.PID')
                    ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_SLSO01_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
                    ->select('TBL_TRN_SLSO01_HDR.*','TBL_MST_CUSTOMER.NAME AS CUSTOMER_NAME','TBL_MST_CUSTOMER.COMMISION','TBL_MST_PROJECT.DESCRIPTIONS AS PROJECT_NAME','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
                    ->first();
            $log_data = [ 
                $id
            ];

            $objScheme  =     DB::select("SELECT DISTINCT M.SCHEMEID_REF,S.SCHEME_NAME FROM TBL_TRN_SLSO01_MAT M 
            LEFT JOIN TBL_MST_SCHEME_HDR S ON S.SCHEMEID=M.SCHEMEID_REF
            WHERE M.SOID_REF=$id"); 
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


            $objSOMAT   =   array();
            if(isset($objSO) && !empty($objSO)){

                $objSOMAT = DB::select('EXEC sp_get_sales_order_material ?', $log_data);
            }

            if(isset($objSOMAT) && !empty($objSOMAT)){
                foreach($objSOMAT as $key=>$val){ 
                    $data=$this->get_lead($val->SQA);  
                        $objSOMAT[$key]->LEAD_NO    =  $data["LEAD_NO"];
                        $objSOMAT[$key]->LEAD_DT    =  $data["LEAD_DT"];  
                        $objSOMAT[$key]->TSID_REF    =  $this->getTechSpecId($val->SOMATID);
                }
            }
            
            $objCount1 = count($objSOMAT);
           

            $objSOTNC = DB::table('TBL_TRN_SLSO01_TNC')                    
                             ->where('TBL_TRN_SLSO01_TNC.SOID_REF','=',$id)
                             ->select('TBL_TRN_SLSO01_TNC.*')
                             ->orderBy('TBL_TRN_SLSO01_TNC.SOTNCID','ASC')
                             ->get()->toArray();
            $objCount2 = count($objSOTNC);

            $objSOUDF = DB::table('TBL_TRN_SLSO01_UDF')                    
                             ->where('TBL_TRN_SLSO01_UDF.SOID_REF','=',$id)
                             ->select('TBL_TRN_SLSO01_UDF.*')
                             ->orderBy('TBL_TRN_SLSO01_UDF.SOUDFID','ASC')
                             ->get()->toArray();
            $objCount3 = count($objSOUDF);

           

            $objSOCAL = DB::table('TBL_TRN_SLSO01_CAL')                    
                             ->where('TBL_TRN_SLSO01_CAL.SOID_REF','=',$id)
                             ->select('TBL_TRN_SLSO01_CAL.*')
                             ->orderBy('TBL_TRN_SLSO01_CAL.SOCALID','ASC')
                             ->get()->toArray();
            $objCount4 = count($objSOCAL);

            $objSOPSLB = DB::table('TBL_TRN_SLSO01_PSLB')                    
                             ->where('TBL_TRN_SLSO01_PSLB.SOID_REF','=',$id)
                             ->select('TBL_TRN_SLSO01_PSLB.*')
                             ->orderBy('TBL_TRN_SLSO01_PSLB.PSLBID','ASC')
                             ->get()->toArray();
            $objCount5 = count($objSOPSLB);
     
            $objRights = $this->getUserRights(['VTID_REF'=>$this->vtid_ref]);
                             $TAXSTATE=[];
                             $objShpAddress=[] ;
                             $objBillAddress=[];

                            if(isset($objSO->SHIPTO) && $objSO->SHIPTO !=""){

                             $sid = $objSO->SHIPTO;
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


                            if(isset($objSO->BILLTO) && $objSO->BILLTO !=""){
                            
                            $bid = $objSO->BILLTO;
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
            
            
            $objSPID=array();

            if(isset($objSO->SPID_REF) && $objSO->SPID_REF !=""){

                $objEMP = DB::table('TBL_MST_EMPLOYEE')
                ->where('TBL_MST_EMPLOYEE.EMPID','=',$objSO->SPID_REF)
                ->select('TBL_MST_EMPLOYEE.*')
                ->first();
                $objSPID[] = $objEMP->EMPCODE.'-'.$objEMP->FNAME.' '.$objEMP->MNAME.' '.$objEMP->LNAME;
            }
            
            $objsubglcode=array();
            if(isset($objSO->GLID_REF) && $objSO->GLID_REF !=""){

                $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('GLID_REF','=',$objSO->GLID_REF)
                ->where('SGLID','=',$objSO->SLID_REF)
                ->select('TBL_MST_SUBLEDGER.*')
                ->first();

            }

          

            $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
            WHERE  CYID_REF = ?  
            order by CTCODE ASC', [$CYID_REF ]);
    
            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
                WHERE  CYID_REF = ?    AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                order by TNC_CODE ASC', [$CYID_REF, 'A' ]);
    
            $objCalculationHeader	=   Helper::getCalculationHeader(array(
                                        'CYID_REF'=>Auth::user()->CYID_REF,
                                        'BRID_REF'=>Session::get('BRID_REF'),
                                        'USERID'=>Auth::user()->USERID,
                                        'HEADING'=>'Transactions',
                                        'VTID_REF'=>$this->vtid_ref,
                                        'FORMID'=>$this->form_id
                                        ));


            
            $ObjUnionUDF = DB::table("TBL_MST_UDFFORSO")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                                    {       
                                    $query->select('UDFID')->from('TBL_MST_UDFFORSO')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF)
                                                    ->where('BRID_REF','=',$BRID_REF);
                                                                       
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF);
                                     
                    
    
            $objUdfSOData = DB::table('TBL_MST_UDFFORSO')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFORSO")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                        {       
                        $query->select('UDFID')->from('TBL_MST_UDFFORSO')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF)
                                        ->where('BRID_REF','=',$BRID_REF);
                                                      
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF);
                            
            

            $objUdfSOData2 = DB::table('TBL_MST_UDFFORSO')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
    
         
    

    
            $objSalesPerson = $this->get_employee_mapping([]);
    
            $ObjSalesQuotationData = DB::table("TBL_TRN_SLQT01_HDR")->select('*')
                        ->where('STATUS','=','A')                    
                        ->where('CYID_REF','=',$CYID_REF)
                       // ->where('BRID_REF','=',$BRID_REF)
                        ->where('FYID_REF','=',$FYID_REF) ->get() ->toArray();                    
                    
      
            $objSQMAT = DB::table('TBL_TRN_SLQT01_MAT')->select('*')
            ->get() ->toArray();
            
            $objUOM = DB::table('TBL_MST_UOM')->select('*')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',$CYID_REF)
            ->get() ->toArray(); 

            $objItemUOMConv = DB::table('TBL_MST_ITEM_UOMCONV')->select('*')
            ->get() ->toArray(); 

            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
            ->get() ->toArray(); 

            $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')
            ->get() ->toArray();
            
            $FormId = $this->form_id;

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
            if($objAttachments)
            {
                $objCountAttachment = count($objAttachments);
            }
            else
            {
                $objCountAttachment = "0";
            }
            
            
            $AlpsStatus =   $this->AlpsStatus();

            $objSOTDS = DB::select('EXEC SP_GET_SO_TDS ?', $log_data);
                
            $objCount6 = count($objSOTDS);
            $objTemplateMaster  =$this->getTemplateMaster("SALES");
            $ActionStatus   =   "disabled";

            $Template = DB::table('TBL_TRN_SLSO01_ADD_INFO')
            ->where('SOID_REF','=',$id)
            ->select('TBL_TRN_SLSO01_ADD_INFO.TEMPLATE')
            ->first();

            $objlastdt  =   DB::select('SELECT MAX(SODT) SODT FROM TBL_TRN_SLSO01_HDR  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
            [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            $objSOCAL   =   DB::select("SELECT T1.*,T2.CTCODE,T2.TYPE
            FROM TBL_TRN_SLSO01_CAL T1 
            INNER JOIN TBL_MST_CALCULATION T2 ON T1.CTID_REF=T2.CTID
            WHERE SOID_REF='$id'");

        $objothcurrency = $this->GetCurrencyMaster(); 

//dd($objCalculationHeader); 
        return view('transactions.sales.SalesOrder.trnfrm38view',compact(['objSO','objRights','objCount1','objSPID',
           'objCount2','objCount3','objCount4','objCount5','objSOMAT','objSOCAL','objSOTNC','objSOUDF','objSOPSLB',
           'objCalculationHeader','objUdfSOData','objTNCHeader','objothcurrency',
           'objSalesPerson','objsubglcode','FormId','objCountAttachment',
           'objShpAddress','objBillAddress','objUOM','objItemUOMConv','objTNCDetails','objUdfSOData2',
           'objCalHeader','objCalDetails','TAXSTATE','AlpsStatus','objSOTDS','objCount6','objTemplateMaster','ActionStatus','Template','objlastdt','TabSetting','SchemeId','SchemeName']));
        }
     
    }
     
  
   public function update(Request $request){

    $r_count1 = $request['Row_Count1'];
    $r_count2 = $request['Row_Count2'];
    $r_count3 = $request['Row_Count3'];
    $r_count4 = $request['Row_Count4'];
    $r_count5 = $request['Row_Count5'];
    $r_count6 = $request['Row_Count6'];

    $GROSS_TOTAL    =   0; 
    $NET_TOTAL = $request['TotalValue'];
    $CGSTAMT        =   0; 
    $SGSTAMT        =   0; 
    $IGSTAMT        =   0; 
    $DISCOUNT       =   0; 
    $OTHER_CHARGES  =   0; 
    $TDS_AMOUNT     =   0; 
    
    for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i]))
            {
                $GROSS_TOTAL+= $request['DISAFTT_AMT_'.$i]; 
                $CGSTAMT+= $request['CGSTAMT_'.$i]; 
                $SGSTAMT+= $request['SGSTAMT_'.$i]; 
                $IGSTAMT+= $request['IGSTAMT_'.$i]; 
                $DISCOUNT+= $request['DISCOUNT_AMT_'.$i]; 
                

                $req_data[$i] = [
                    'SRNO' => $i,
                    'SQA' => (!empty($request['SQA_'.$i])) == 'true' ? $request['SQA_'.$i] : "0" ,
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'ITEMSPECI' => $request['Itemspec_'.$i],
                    'MAIN_UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'SO_QTY' => $request['SO_QTY_'.$i],
                    'ALT_UOMID_REF' => $request['ALT_UOMID_REF_'.$i],
                    'ALT_QTY' => $request['ALT_UOMID_QTY_'.$i],
                    'RATEPUOM' => $request['RATEPUOM_'.$i],
                    'DISCPER'    => (!empty($request['DISCPER_'.$i])) == 'true' ? $request['DISCPER_'.$i] : 0,
                    'DISCOUNT_AMT' => (!empty($request['DISCOUNT_AMT_'.$i])) == 'true' ? $request['DISCOUNT_AMT_'.$i] : 0,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'SEQID_REF'=> (!empty($request['SEQID_REF_'.$i])) == 'true' ? $request['SEQID_REF_'.$i] : "0" ,
                    'SCHEMEID_REF'       => $request['SCHEMEID_REF_'.$i]!='' ? $request['SCHEMEID_REF_'.$i] :  NULL,
                    'ITEM_TYPE'          => $request['ITEM_TYPE_'.$i] !="" ? $request['ITEM_TYPE_'.$i] : "OTHER",   
                    'SCHEMEQTY' => (!empty($request['SCHEMEQTY_'.$i]) ? $request['SCHEMEQTY_'.$i] : 0),               
                ];


                $tsp_data    =   array();
                if(isset($request['TSID_REF_'.$i]) && $request['TSID_REF_'.$i] !=''){
                    $tspid_array =   explode(',',$request['TSID_REF_'.$i]);
                    foreach($tspid_array as $tsid){
                        $tsp_data[] = [
                            'TSID_REF'      =>  $tsid,
                            'ITEMID_REF'      =>  $request['ITEMID_REF_'.$i]
                        ];
                    }
                }

                $req_data[$i]['TSP']=$tsp_data;



                
            }
        }


        //dd($SGSTAMT); 

  
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
                if(isset($request['UDFSOID_REF_'.$i]) && !is_null($request['UDFSOID_REF_'.$i]))
                {
                    $reqdata3[$i] = [
                        'UDFSOID_REF'   => $request['UDFSOID_REF_'.$i],
                        'SOUVALUE'      => $request['udfvalue_'.$i],
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
                    if(isset($request['TID_REF_'.$i]))
                    {
                        if($request['CT_TYPE_'.$i]=="DISCOUNT"){
                          
                            $DISCOUNT      += $request['VALUE_'.$i]; 
                        }else{
                            $OTHER_CHARGES += $request['VALUE_'.$i];   
                        }

                        $reqdata4[$i] = [
                            'CTID_REF'      => $request['CTID_REF_'.$i] ,
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
        
        for ($i=0; $i<=$r_count5; $i++)
        {
                if(isset($request['PAY_DAYS_'.$i]) && !is_null($request['PAY_DAYS_'.$i]))
                {
                    $reqdata5[$i] = [
                        'PAY_DAYS'      => $request['PAY_DAYS_'.$i],
                        'DUE'           => $request['DUE_'.$i],
                        'REMARKS'       => $request['PSREMARKS_'.$i],
                        'DUE_DATE'      => $request['DUE_DATE_'.$i],
                    ];
                }
            
        }
            if(isset($reqdata5))
            { 
                $wrapped_links5["PSLB"] = $reqdata5; 
                $XMLPSLB = ArrayToXml::convert($wrapped_links5);
            }
            else
            {
                $XMLPSLB = NULL; 
            }

            for ($i=0; $i<=$r_count6; $i++){
   
                if(isset($request['TDSID_REF_'.$i]) && $request['TDSID_REF_'.$i] !=''){
                    if(isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0==1){
                    $TDS_AMOUNT      += $request['TDS_AMT_'.$i]; 
                    }
                    $reqdata6[$i] = [
                        'TDSID_REF'                 => $request['TDSID_REF_'.$i],
                        'ASSESSABLE_VL_TDS'         => (!is_null($request['ASSESSABLE_VL_TDS_'.$i]) ? $request['ASSESSABLE_VL_TDS_'.$i] : 0),
                        'TDS_RATE'                  => (!is_null($request['TDS_RATE_'.$i]) ? $request['TDS_RATE_'.$i] : 0),
                        'ASSESSABLE_VL_SURCHARGE'   => (!is_null($request['ASSESSABLE_VL_SURCHARGE_'.$i]) ? $request['ASSESSABLE_VL_SURCHARGE_'.$i] : 0),
                        'SURCHARGE_RATE'            => (!is_null($request['SURCHARGE_RATE_'.$i]) ? $request['SURCHARGE_RATE_'.$i] : 0),
                        'ASSESSABLE_VL_CESS'        => (!is_null($request['ASSESSABLE_VL_CESS_'.$i]) ? $request['ASSESSABLE_VL_CESS_'.$i] : 0),
                        'CESS_RATE'                 => (!is_null($request['CESS_RATE_'.$i]) ? $request['CESS_RATE_'.$i] : 0),
                        'ASSESSABLE_VL_SPCESS'      => (!is_null($request['ASSESSABLE_VL_SPCESS_'.$i]) ? $request['ASSESSABLE_VL_SPCESS_'.$i] : 0),
                        'SPCESS_RATE'               => (!is_null($request['SPCESS_RATE_'.$i]) ? $request['SPCESS_RATE_'.$i] : 0),
                    ];
                }
            }

         
            
            if(isset($reqdata6)){ 
                $wrapped_links6["TDSD"] = $reqdata6; 
                $XMLTDSD = ArrayToXml::convert($wrapped_links6);
            }
            else{
                $XMLTDSD = NULL; 
            }

        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $SONO = $request['SONO'];
        $SODT = $request['SODT'];
        $GLID_REF = $request['GLID_REF'];
        $SLID_REF = $request['SLID_REF'];
        $SOFC = (isset($request['SOFC'])!="true" ? 0 : 1);
        $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
        $OVFDT = $request['OVFDT'];
        $OVTDT = $request['OVTDT'];
        $CUSTOMERPONO = $request['CUSTOMERPONO'];
        $TDS   = (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);
        $CUSTOMERDT = $request['CUSTOMERDT'];
        $SPID_REF = $request['SPID_REF'];
        $REFNO = $request['REFNO'];
        $CREDITDAYS = (isset($request['CREDITDAYS'])) ? $request['CREDITDAYS'] : 0;
        $BILLTO = $request['BILLTO'];
        $SHIPTO = $request['SHIPTO'];
        $REMARKS = $request['REMARKS'];

        $GST_N_Avail = (isset($request['GST_N_Avail'])!="true" ? 0 : 1);
        $GST_Reverse = (isset($request['GST_Reverse'])!="true" ? 0 : 1);
        $EXE_GST = (isset($request['EXE_GST'])!="true" ? 0 : 1);
        $Template_Description = $request['Template_Description'];
    

        $DEALERID_REF           = $request['DEALERID_REF'];
        $PROJECTID_REF          = $request['PROJECTID_REF'];
        $DEALER_COMMISSION_AMT  = $request['DEALER_COMMISSION_AMT'];
        $PRICE_BASED_ON         = $request['PRICE_BASED_ON'] ? $request['PRICE_BASED_ON'] : NULL;
        $BILLTO_SHIPTO         = $request['Tax_State'] ? $request['Tax_State'] : NULL;



        $log_data = [ 
            $SONO,$SODT,$GLID_REF,$SLID_REF,$SOFC,$CRID_REF,$CONVFACT,$OVFDT,$OVTDT,$CUSTOMERPONO,$CUSTOMERDT,
            $SPID_REF,$REFNO,$CREDITDAYS,$BILLTO,$SHIPTO,$REMARKS,$CYID_REF, $BRID_REF, $FYID_REF,$VTID_REF,
            $XMLMAT, $XMLTNC,$XMLUDF,$XMLCAL,$XMLPSLB, $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, 
            $IPADDRESS,$GST_N_Avail,$GST_Reverse,$EXE_GST,$Template_Description,$XMLTDSD,$TDS,$DEALERID_REF,$PROJECTID_REF,$DEALER_COMMISSION_AMT,
            $GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT,$PRICE_BASED_ON,$BILLTO_SHIPTO
        ];

    // dd($log_data); 

       
        $sp_result = DB::select('EXEC SP_SO_UP ?,?,?,?,? ,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $log_data);       
    // dd($sp_result); 
    
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

        $Approvallevel      =   NULL;
        if(!empty($sp_listing_result)){
            foreach ($sp_listing_result as $key=>$salesenquiryitem){  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$salesenquiryitem->LAVELS;
            }
        }
   
            $r_count1 = $request['Row_Count1'];
            $r_count2 = $request['Row_Count2'];
            $r_count3 = $request['Row_Count3'];
            $r_count4 = $request['Row_Count4'];
            $r_count5 = $request['Row_Count5'];
            $r_count6 = $request['Row_Count6'];

            $GROSS_TOTAL    =   0; 
            $NET_TOTAL = $request['TotalValue'];
            $CGSTAMT        =   0; 
            $SGSTAMT        =   0; 
            $IGSTAMT        =   0; 
            $DISCOUNT       =   0; 
            $OTHER_CHARGES  =   0; 
            $TDS_AMOUNT     =   0; 
            
            for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i]))
            {
                $GROSS_TOTAL+= $request['DISAFTT_AMT_'.$i]; 
                $CGSTAMT+= $request['CGSTAMT_'.$i]; 
                $SGSTAMT+= $request['SGSTAMT_'.$i]; 
                $IGSTAMT+= $request['IGSTAMT_'.$i]; 
                $DISCOUNT+= $request['DISCOUNT_AMT_'.$i]; 

                $req_data[$i] = [
                    'SRNO' => $i+1,
                    'SQA' => (!empty($request['SQA_'.$i])) == 'true' ? $request['SQA_'.$i] : "0" ,
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'ITEMSPECI' => $request['Itemspec_'.$i],
                    'MAIN_UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'SO_QTY' => $request['SO_QTY_'.$i],
                    'ALT_UOMID_REF' => $request['ALT_UOMID_REF_'.$i],
                    'ALT_QTY' => $request['ALT_UOMID_QTY_'.$i],
                    'RATEPUOM' => $request['RATEPUOM_'.$i],
                    'DISCPER'    => (!empty($request['DISCPER_'.$i])) == 'true' ? $request['DISCPER_'.$i] : 0,
                    'DISCOUNT_AMT' => (!empty($request['DISCOUNT_AMT_'.$i])) == 'true' ? $request['DISCOUNT_AMT_'.$i] : 0,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'SEQID_REF'=> (!empty($request['SEQID_REF_'.$i])) == 'true' ? $request['SEQID_REF_'.$i] : "0" ,
                    'SCHEMEID_REF'       => $request['SCHEMEID_REF_'.$i]!='' ? $request['SCHEMEID_REF_'.$i] :  NULL,
                    'ITEM_TYPE'          => $request['ITEM_TYPE_'.$i] !="" ? $request['ITEM_TYPE_'.$i] : "OTHER",   
                    'SCHEMEQTY' => (!empty($request['SCHEMEQTY_'.$i]) ? $request['SCHEMEQTY_'.$i] : 0),           
                ];

               

                $tsp_data    =   array();
                if(isset($request['TSID_REF_'.$i]) && $request['TSID_REF_'.$i] !=''){
                    $tspid_array =   explode(',',$request['TSID_REF_'.$i]);
                    foreach($tspid_array as $tsid){
                        $tsp_data[] = [
                            'SERIALNO'      =>  $i+1,
                            'TSID_REF'      =>  $tsid,
                            'ITEMID_REF'      =>  $request['ITEMID_REF_'.$i]
                        ];
                    }
                }
                
                $req_data[$i]['TSP']=$tsp_data;
                
            }
        }



        //dd($req_data);

  
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
                    if(isset($request['UDFSOID_REF_'.$i]) && !is_null($request['UDFSOID_REF_'.$i]))
                    {
                        $reqdata3[$i] = [
                            'UDFSOID_REF'   => $request['UDFSOID_REF_'.$i],
                            'SOUVALUE'      => $request['udfvalue_'.$i],
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
                        if(isset($request['TID_REF_'.$i]))
                        {
                            if($request['CT_TYPE_'.$i]=="DISCOUNT"){
                          
                                $DISCOUNT      += $request['VALUE_'.$i]; 
                            }else{
                                $OTHER_CHARGES += $request['VALUE_'.$i];   
                            }

                            $reqdata4[$i] = [
                                'CTID_REF'      => $request['CTID_REF_'.$i] ,
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
            
            for ($i=0; $i<=$r_count5; $i++)
            {
                    if(isset($request['PAY_DAYS_'.$i]) && !is_null($request['PAY_DAYS_'.$i]))
                    {
                        $reqdata5[$i] = [
                            'PAY_DAYS'      => $request['PAY_DAYS_'.$i],
                            'DUE'           => $request['DUE_'.$i],
                            'REMARKS'       => $request['PSREMARKS_'.$i],
                            'DUE_DATE'      => $request['DUE_DATE_'.$i],
                        ];
                    }
                
            }
                if(isset($reqdata5))
                { 
                    $wrapped_links5["PSLB"] = $reqdata5; 
                    $XMLPSLB = ArrayToXml::convert($wrapped_links5);
                }
                else
                {
                    $XMLPSLB = NULL; 
                }

                for ($i=0; $i<=$r_count6; $i++){
                    if(isset($request['TDSID_REF_'.$i]) && $request['TDSID_REF_'.$i] !=''){
                        if(isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0==1){
                            $TDS_AMOUNT      += $request['TDS_AMT_'.$i]; 
                            }

                        $reqdata6[$i] = [
                            'TDSID_REF'                 => $request['TDSID_REF_'.$i],
                            'ASSESSABLE_VL_TDS'         => (!is_null($request['ASSESSABLE_VL_TDS_'.$i]) ? $request['ASSESSABLE_VL_TDS_'.$i] : 0),
                            'TDS_RATE'                  => (!is_null($request['TDS_RATE_'.$i]) ? $request['TDS_RATE_'.$i] : 0),
                            'ASSESSABLE_VL_SURCHARGE'   => (!is_null($request['ASSESSABLE_VL_SURCHARGE_'.$i]) ? $request['ASSESSABLE_VL_SURCHARGE_'.$i] : 0),
                            'SURCHARGE_RATE'            => (!is_null($request['SURCHARGE_RATE_'.$i]) ? $request['SURCHARGE_RATE_'.$i] : 0),
                            'ASSESSABLE_VL_CESS'        => (!is_null($request['ASSESSABLE_VL_CESS_'.$i]) ? $request['ASSESSABLE_VL_CESS_'.$i] : 0),
                            'CESS_RATE'                 => (!is_null($request['CESS_RATE_'.$i]) ? $request['CESS_RATE_'.$i] : 0),
                            'ASSESSABLE_VL_SPCESS'      => (!is_null($request['ASSESSABLE_VL_SPCESS_'.$i]) ? $request['ASSESSABLE_VL_SPCESS_'.$i] : 0),
                            'SPCESS_RATE'               => (!is_null($request['SPCESS_RATE_'.$i]) ? $request['SPCESS_RATE_'.$i] : 0),
                        ];
                    }
                }
                
                if(isset($reqdata6)){ 
                    $wrapped_links6["TDSD"] = $reqdata6; 
                    $XMLTDSD = ArrayToXml::convert($wrapped_links6);
                }
                else{
                    $XMLTDSD = NULL; 
                }

                $VTID_REF     =   $this->vtid_ref;
                $VID = 0;
                $USERID = Auth::user()->USERID;   
                $ACTIONNAME = $Approvallevel;
                $IPADDRESS = $request->getClientIp();
                $CYID_REF = Auth::user()->CYID_REF;
                $BRID_REF = Session::get('BRID_REF');
                $FYID_REF = Session::get('FYID_REF');
                $SONO = $request['SONO'];
                $SODT = $request['SODT'];
                $GLID_REF = $request['GLID_REF'];
                $SLID_REF = $request['SLID_REF'];
                $SOFC = (isset($request['SOFC'])!="true" ? 0 : 1);
                $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
                $CONVFACT = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
                $OVFDT = $request['OVFDT'];
                $OVTDT = $request['OVTDT'];
                $CUSTOMERPONO = $request['CUSTOMERPONO'];
                $CUSTOMERDT = $request['CUSTOMERDT'];
                $SPID_REF = $request['SPID_REF'];
                $REFNO = $request['REFNO'];
                $CREDITDAYS = (isset($request['CREDITDAYS'])) ? $request['CREDITDAYS'] : 0;
                $BILLTO = $request['BILLTO'];
                $SHIPTO = $request['SHIPTO'];
                $REMARKS = $request['REMARKS'];





                $GST_N_Avail = (isset($request['GST_N_Avail'])!="true" ? 0 : 1);
                $GST_Reverse = (isset($request['GST_Reverse'])!="true" ? 0 : 1);
                $EXE_GST = (isset($request['EXE_GST'])!="true" ? 0 : 1);
                $Template_Description = $request['Template_Description'];
                $TDS   = (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);

                $DEALERID_REF           = $request['DEALERID_REF'];
                $PROJECTID_REF          = $request['PROJECTID_REF'];
                $DEALER_COMMISSION_AMT  = $request['DEALER_COMMISSION_AMT'];

                $PRICE_BASED_ON         = isset($request['PRICE_BASED_ON'])?trim($request['PRICE_BASED_ON']):NULL;
                $BILLTO_SHIPTO         = $request['Tax_State'] ? $request['Tax_State'] : NULL;


                $log_data = [ 
                    $SONO,$SODT,$GLID_REF,$SLID_REF,$SOFC,$CRID_REF,$CONVFACT,$OVFDT,$OVTDT,$CUSTOMERPONO,$CUSTOMERDT,
                    $SPID_REF,$REFNO,$CREDITDAYS,$BILLTO,$SHIPTO,$REMARKS,$CYID_REF, $BRID_REF, $FYID_REF,$VTID_REF,
                    $XMLMAT, $XMLTNC,$XMLUDF,$XMLCAL,$XMLPSLB, $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, 
                    $IPADDRESS,$GST_N_Avail,$GST_Reverse,$EXE_GST,$Template_Description,$XMLTDSD,$TDS,$DEALERID_REF,$PROJECTID_REF,$DEALER_COMMISSION_AMT
                    ,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT,$PRICE_BASED_ON,$BILLTO_SHIPTO
                ];

                
                $sp_result = DB::select('EXEC SP_SO_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $log_data);       
                
            
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
                $TABLE      =   "TBL_TRN_SLSO01_HDR";
                $FIELD      =   "SOID";
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
        $TABLE      =   "TBL_TRN_SLSO01_HDR";
        $FIELD      =   "SOID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
        
        $req_data[0]=[
            'NT'  => 'TBL_TRN_SLSO01_MAT',
           ];
           $req_data[1]=[
           'NT'  => 'TBL_TRN_SLSO01_TNC',
           ];
           $req_data[2]=[
           'NT'  => 'TBL_TRN_SLSO01_UDF',
           ];
           $req_data[3]=[
            'NT'  => 'TBL_TRN_SLSO01_CAL',
            ];
            $req_data[4]=[
            'NT'  => 'TBL_TRN_SLSO01_PSLB',
            ];
            $req_data[5]=[
                'NT'  => 'TBL_TRN_SLSO02_HDR',
            ];
    
           
           $wrapped_links["TABLES"] = $req_data; 
           $XMLTAB = ArrayToXml::convert($wrapped_links);
           
           $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];
   
           $sp_result = DB::select('EXEC SP_TRN_CANCEL_SO  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);
   

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
    
    $image_path         =   "docs/company".$CYID_REF."/SalesOrder";     
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
        return redirect()->route("transaction",[38,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
    
       return redirect()->route("transaction",[38,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("transaction",[38,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("transaction",[38,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("transaction",[38,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

    public function checkso(Request $request){

      
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $SONO = $request->SONO;
        
        $objSO = DB::table('TBL_TRN_SLSO01_HDR')
        ->where('TBL_TRN_SLSO01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLSO01_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_SLSO01_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_SLSO01_HDR.SONO','=',$SONO)
        ->select('TBL_TRN_SLSO01_HDR.SOID')
        ->first();
        
        if($objSO){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate SONO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }

    public function checkcustomerpono(Request $request){

        
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $CUSTOMERPONO = $request->CUSTOMERPONO;
        
        $objSO = DB::table('TBL_TRN_SLSO01_HDR')
        ->where('TBL_TRN_SLSO01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLSO01_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_SLSO01_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_SLSO01_HDR.CUSTOMERPONO','=',$CUSTOMERPONO)
        ->select('TBL_TRN_SLSO01_HDR.CUSTOMERPONO')
        ->first();
        
        if($objSO){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate Customer PO No']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }

    
   

    
    public function amendment($id){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id))
        {
            $objSO = DB::table('TBL_TRN_SLSO01_HDR')
                    ->where('TBL_TRN_SLSO01_HDR.FYID_REF','=',Session::get('FYID_REF'))
                    ->where('TBL_TRN_SLSO01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('TBL_TRN_SLSO01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                    ->where('TBL_TRN_SLSO01_HDR.SOID','=',$id)
                    ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_SLSO01_HDR.DEALERID_REF','=','TBL_MST_CUSTOMER.CID')
                    ->leftJoin('TBL_MST_PROJECT', 'TBL_TRN_SLSO01_HDR.PROJECTID_REF','=','TBL_MST_PROJECT.PID')
                    ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_SLSO01_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
                    ->select('TBL_TRN_SLSO01_HDR.*','TBL_MST_CUSTOMER.NAME AS CUSTOMER_NAME','TBL_MST_CUSTOMER.COMMISION','TBL_MST_PROJECT.DESCRIPTIONS AS PROJECT_NAME','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
                    ->first();

           


            $log_data = [ 
                $id
            ];

            $objScheme  =     DB::select("SELECT DISTINCT M.SCHEMEID_REF,S.SCHEME_NAME FROM TBL_TRN_SLSO01_MAT M 
            LEFT JOIN TBL_MST_SCHEME_HDR S ON S.SCHEMEID=M.SCHEMEID_REF
            WHERE M.SOID_REF=$id"); 
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

     

            $objSOMAT=array();
            if(isset($objSO) && !empty($objSO)){
                $objSOMAT = DB::select('EXEC sp_get_sales_order_material ?', $log_data);
            }

            if(isset($objSOMAT) && !empty($objSOMAT)){
                foreach($objSOMAT as $key=>$val){ 
                    $data=$this->get_lead($val->SQA);  
                        $objSOMAT[$key]->LEAD_NO    =  $data["LEAD_NO"];
                        $objSOMAT[$key]->LEAD_DT    =  $data["LEAD_DT"];  
                        $objSOMAT[$key]->TSID_REF    =  $this->getTechSpecId($val->SOMATID);
                }
            }

            $objCount1 = count($objSOMAT);

            $MAXSOANO=NULL;
            if(isset($objSO->SOID) && $objSO->SOID !=""){

                $objSOANO = DB::SELECT("select  MAX(isnull(ANO,0))+1  AS ANO from TBL_TRN_SLSO02_HDR  WHERE SOID_REF=? AND SOANO=?",[$objSO->SOID,$objSO->SONO]);
                $MAXSOANO = $objSOANO[0]->ANO;
            }
        
            $objSOTNC = DB::table('TBL_TRN_SLSO01_TNC')                    
                             ->where('TBL_TRN_SLSO01_TNC.SOID_REF','=',$id)
                             ->select('TBL_TRN_SLSO01_TNC.*')
                             ->orderBy('TBL_TRN_SLSO01_TNC.SOTNCID','ASC')
                             ->get()->toArray();
            $objCount2 = count($objSOTNC);

            $objSOUDF = DB::table('TBL_TRN_SLSO01_UDF')                    
                             ->where('TBL_TRN_SLSO01_UDF.SOID_REF','=',$id)
                             ->select('TBL_TRN_SLSO01_UDF.*')
                             ->orderBy('TBL_TRN_SLSO01_UDF.SOUDFID','ASC')
                             ->get()->toArray();
            $objCount3 = count($objSOUDF);

            $objSOCAL   =   DB::select("SELECT T1.*,T2.CTCODE,T2.TYPE
                            FROM TBL_TRN_SLSO01_CAL T1 
                            INNER JOIN TBL_MST_CALCULATION T2 ON T1.CTID_REF=T2.CTID
                            WHERE SOID_REF='$id'");
            $objCount4 = count($objSOCAL);

            $objSOPSLB = DB::table('TBL_TRN_SLSO01_PSLB')                    
                             ->where('TBL_TRN_SLSO01_PSLB.SOID_REF','=',$id)
                             ->select('TBL_TRN_SLSO01_PSLB.*')
                             ->orderBy('TBL_TRN_SLSO01_PSLB.PSLBID','ASC')
                             ->get()->toArray();
            $objCount5 = count($objSOPSLB);
     
            $objRights = $this->getUserRights(['VTID_REF'=>$this->vtid_ref]);
                             $TAXSTATE=[];
                             $objShpAddress=[] ;
                             $objBillAddress=[];

                             if(isset($objSO->SHIPTO) && $objSO->SHIPTO !=""){
                             $sid = $objSO->SHIPTO;
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
                            
                            if(isset($objSO->BILLTO) && $objSO->BILLTO !=""){
                            $bid = $objSO->BILLTO;
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
            
            $objglcode2=array();
            if(isset($objSO->GLID_REF) && $objSO->GLID_REF !=""){ 
                $objglcode2 = DB::table('TBL_MST_GENERALLEDGER')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('GLID','=',$objSO->GLID_REF)
                ->select('TBL_MST_GENERALLEDGER.*')
                ->first();
            }

            $objSPID=array();
            if(isset($objSO->SPID_REF) && $objSO->SPID_REF !=""){
                $objEMP = DB::table('TBL_MST_EMPLOYEE')
                ->where('TBL_MST_EMPLOYEE.EMPID','=',$objSO->SPID_REF)
                ->select('TBL_MST_EMPLOYEE.*')
                ->first();
                $objSPID[] = $objEMP->FNAME.' '.$objEMP->MNAME.' '.$objEMP->LNAME;

            }


            $objglcode = DB::table('TBL_MST_GENERALLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('STATUS','=',$Status)
            ->where('SUBLEDGER','=','1')
            ->select('TBL_MST_GENERALLEDGER.*')
            ->get()
            ->toArray();

            $objsubglcode=array();
            if(isset($objSO->GLID_REF) && $objSO->GLID_REF !=""){
                $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('GLID_REF','=',$objSO->GLID_REF)
                ->where('SGLID','=',$objSO->SLID_REF)
                ->select('TBL_MST_SUBLEDGER.*')
                ->first();
            }


           

            }

           

            $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
            WHERE  CYID_REF = ?   
            order by CTCODE ASC', [$CYID_REF ]);
    
            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
                WHERE  CYID_REF = ?    AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                order by TNC_CODE ASC', [$CYID_REF, 'A' ]);
    
            $objCalculationHeader	=   Helper::getCalculationHeader(array(
                                        'CYID_REF'=>Auth::user()->CYID_REF,
                                        'BRID_REF'=>Session::get('BRID_REF'),
                                        'USERID'=>Auth::user()->USERID,
                                        'HEADING'=>'Transactions',
                                        'VTID_REF'=>$this->vtid_ref,
                                        'FORMID'=>$this->form_id
                                        ));
            
            $ObjUnionUDF = DB::table("TBL_MST_UDFFORSO")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                                    {       
                                    $query->select('UDFID')->from('TBL_MST_UDFFORSO')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF)
                                                    ->where('BRID_REF','=',$BRID_REF);
                                                               
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF);
                                     
                    
    
            $objUdfSOData = DB::table('TBL_MST_UDFFORSO')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFORSO")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                        {       
                        $query->select('UDFID')->from('TBL_MST_UDFFORSO')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF)
                                        ->where('BRID_REF','=',$BRID_REF);
                                                         
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF);
                         
            

            $objUdfSOData2 = DB::table('TBL_MST_UDFFORSO')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
    
        
    

    
            $objSalesPerson = $this->get_employee_mapping([]);
    
            $ObjSalesQuotationData = DB::table("TBL_TRN_SLQT01_HDR")->select('*')
                        ->where('STATUS','=','A')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        //->where('BRID_REF','=',$BRID_REF)
                        ->where('FYID_REF','=',$FYID_REF) ->get() ->toArray();                    
                    
            $objSalesQuotationAData = DB::table('TBL_TRN_SLQT02_HDR')->select('*')
                ->where('STATUS','=','A')
                ->where('CYID_REF','=',$CYID_REF)
               // ->where('BRID_REF','=',$BRID_REF)
                ->where('FYID_REF','=',$FYID_REF)
                ->get() ->toArray();  

        
            $objItems=array();

            $objSQMAT = DB::table('TBL_TRN_SLQT01_MAT')->select('*')
            ->get() ->toArray();

            
            foreach($objSOMAT as $index=>$row1){
                  
                $objConsumed = DB::select("select ISNULL(sum(table1.CHALLAN_MAINQTY),0) as TOTAL_CONSUMED_QTY from tbl_trn_slsc01_mat table1 
                left join tbl_trn_slsc01_hdr table2 on table1.SCID_REF = table2.SCID 
                WHERE table1.SO=? AND table1.ITEMID_REF=? AND table1.SQID_REF=? AND table1.SEID_REF=? AND table2.STATUS<>? " , [$id, $row1->ITEMID_REF,$row1->SQA,$row1->SEQID_REF,'C']);

                $CONSUMED_TOTAL = number_format(floatVal($objConsumed[0]->TOTAL_CONSUMED_QTY), 3, '.', '');  
              
                $objSOMAT[$index]->TOTAL_CONSUMED = $CONSUMED_TOTAL;
                $objSOMAT[$index]->CAN_DELETE = $CONSUMED_TOTAL > 0.000 ? FALSE : TRUE ;
            }
            
            
         
            
            $objUOM = DB::table('TBL_MST_UOM')->select('*')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',$CYID_REF)
            ->get() ->toArray(); 


            $objItemUOMConv=array();

            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
            ->get() ->toArray(); 

            $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')
            ->get() ->toArray(); 
        
            $FormId = $this->form_id;

            $AlpsStatus =   $this->AlpsStatus();



   

            $objSOTDS = DB::select('EXEC SP_GET_SO_TDS ?', $log_data);
                
            $objCount6 = count($objSOTDS);
            $objTemplateMaster  =$this->getTemplateMaster("SALES");
            $ActionStatus   =   "";

            $Template = DB::table('TBL_TRN_SLSO01_ADD_INFO')
            ->where('SOID_REF','=',$id)
            ->select('TBL_TRN_SLSO01_ADD_INFO.TEMPLATE')
            ->first();

            $objlastdt  =   DB::select('SELECT MAX(SODT) SODT FROM TBL_TRN_SLSO01_HDR  
            WHERE  CYID_REF = ? AND BRID_REF = ?  AND VTID_REF = ? AND STATUS = ?', 
            [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);

            $objothcurrency = $this->GetCurrencyMaster(); 


        return view('transactions.sales.SalesOrder.trnfrm38amendment',compact(['objSO','objRights','objCount1','objSPID',
           'objCount2','objCount3','objCount4','objCount5','objSOMAT','objSOCAL','objSOTNC','objSOUDF','objSOPSLB',
           'objglcode','objCalculationHeader','objUdfSOData','objTNCHeader','objothcurrency','objglcode2','objSalesPerson','objSalesQuotationAData','ObjSalesQuotationData','objsubglcode',
           'objShpAddress','objBillAddress','objItems','objSQMAT','objUOM','objItemUOMConv','objTNCDetails','objUdfSOData2',
           'objCalHeader','objCalDetails','TAXSTATE','MAXSOANO','FormId','AlpsStatus','objSOTDS','objCount6','objTemplateMaster','ActionStatus','Template','objlastdt','SchemeId','SchemeName']));
        }
     
    
    

    public function saveamendment(Request $request){
        $USERID_REF = Auth::user()->USERID;


        $VTID_REF     =   39; //SOA VT ID
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];
        $r_count5 = $request['Row_Count5'];
        $r_count6 = $request['Row_Count6'];

        $GROSS_TOTAL    =   0; 
        $NET_TOTAL = $request['TotalValue'];
        $CGSTAMT        =   0; 
        $SGSTAMT        =   0; 
        $IGSTAMT        =   0; 
        $DISCOUNT       =   0; 
        $OTHER_CHARGES  =   0; 
        $TDS_AMOUNT     =   0; 
		
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
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i]))
            {
                $GROSS_TOTAL+= $request['DISAFTT_AMT_'.$i]; 
                $CGSTAMT+= $request['CGSTAMT_'.$i]; 
                $SGSTAMT+= $request['SGSTAMT_'.$i]; 
                $IGSTAMT+= $request['IGSTAMT_'.$i]; 
                $DISCOUNT+= $request['DISCOUNT_AMT_'.$i]; 

                $req_data[$i] = [
                    'SRNO' => $i+1,
                    'SQA' => (!empty($request['SQA_'.$i])) == 'true' ? $request['SQA_'.$i] : "0" ,
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'ITEMSPECI' => $request['Itemspec_'.$i],
                    'MAIN_UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'SO_QTY' => $request['SO_QTY_'.$i],
                    'ALT_UOMID_REF' => $request['ALT_UOMID_REF_'.$i],
                    'ALT_QTY' => $request['ALT_UOMID_QTY_'.$i],
                    'RATEPUOM' => $request['RATEPUOM_'.$i],
                    'DISCPER'    => (!empty($request['DISCPER_'.$i])) == 'true' ? $request['DISCPER_'.$i] : 0,
                    'DISCOUNT_AMT' => (!empty($request['DISCOUNT_AMT_'.$i])) == 'true' ? $request['DISCOUNT_AMT_'.$i] : 0,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'SEQID_REF'=> (!empty($request['SEQID_REF_'.$i])) == 'true' ? $request['SEQID_REF_'.$i] : "0" ,
                    'SCHEMEID_REF'       => $request['SCHEMEID_REF_'.$i]!='' ? $request['SCHEMEID_REF_'.$i] :  NULL,
                    'ITEM_TYPE'          => $request['ITEM_TYPE_'.$i] !="" ? $request['ITEM_TYPE_'.$i] : "OTHER",   
                    'SCHEMEQTY' => (!empty($request['SCHEMEQTY_'.$i]) ? $request['SCHEMEQTY_'.$i] : 0),           
                ];

               

                $tsp_data    =   array();
                if(isset($request['TSID_REF_'.$i]) && $request['TSID_REF_'.$i] !=''){
                    $tspid_array =   explode(',',$request['TSID_REF_'.$i]);
                    foreach($tspid_array as $tsid){
                        $tsp_data[] = [
                            'SERIALNO'      =>  $i+1,
                            'TSID_REF'      =>  $tsid,
                            'ITEMID_REF'      =>  $request['ITEMID_REF_'.$i]
                        ];
                    }
                }
                
                $req_data[$i]['TSP']=$tsp_data;
                
            }
        }



        //dd($req_data);

  
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
                if(isset($request['UDFSOID_REF_'.$i]) && !is_null($request['UDFSOID_REF_'.$i]))
                {
                    $reqdata3[$i] = [
                        'UDFSOID_REF'   => $request['UDFSOID_REF_'.$i],
                        'SOUVALUE'      => $request['udfvalue_'.$i],
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
                    if(isset($request['TID_REF_'.$i]))
                    {
                        if($request['CT_TYPE_'.$i]=="DISCOUNT"){
                          
                            $DISCOUNT      += $request['VALUE_'.$i]; 
                        }else{
                            $OTHER_CHARGES += $request['VALUE_'.$i];   
                        }

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
        
        for ($i=0; $i<=$r_count5; $i++)
        {
                if(isset($request['PAY_DAYS_'.$i]) && !is_null($request['PAY_DAYS_'.$i]))
                {
                    $reqdata5[$i] = [
                        'PAY_DAYS'      => $request['PAY_DAYS_'.$i],
                        'DUE'           => $request['DUE_'.$i],
                        'REMARKS'       => $request['PSREMARKS_'.$i],
                        'DUE_DATE'      => $request['DUE_DATE_'.$i],
                    ];
                }
            
        }
        if(isset($reqdata5))
        { 
            $wrapped_links5["PSLB"] = $reqdata5; 
            $XMLPSLB = ArrayToXml::convert($wrapped_links5);
        }
        else
        {
            $XMLPSLB = NULL; 
        }


        for ($i=0; $i<=$r_count6; $i++){
            if(isset($request['TDSID_REF_'.$i]) && $request['TDSID_REF_'.$i] !=''){
                if(isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0==1){
                    $TDS_AMOUNT      += $request['TDS_AMT_'.$i]; 
                    }

                $reqdata6[$i] = [
                    'TDSID_REF'                 => $request['TDSID_REF_'.$i],
                    'ASSESSABLE_VL_TDS'         => (!is_null($request['ASSESSABLE_VL_TDS_'.$i]) ? $request['ASSESSABLE_VL_TDS_'.$i] : 0),
                    'TDS_RATE'                  => (!is_null($request['TDS_RATE_'.$i]) ? $request['TDS_RATE_'.$i] : 0),
                    'ASSESSABLE_VL_SURCHARGE'   => (!is_null($request['ASSESSABLE_VL_SURCHARGE_'.$i]) ? $request['ASSESSABLE_VL_SURCHARGE_'.$i] : 0),
                    'SURCHARGE_RATE'            => (!is_null($request['SURCHARGE_RATE_'.$i]) ? $request['SURCHARGE_RATE_'.$i] : 0),
                    'ASSESSABLE_VL_CESS'        => (!is_null($request['ASSESSABLE_VL_CESS_'.$i]) ? $request['ASSESSABLE_VL_CESS_'.$i] : 0),
                    'CESS_RATE'                 => (!is_null($request['CESS_RATE_'.$i]) ? $request['CESS_RATE_'.$i] : 0),
                    'ASSESSABLE_VL_SPCESS'      => (!is_null($request['ASSESSABLE_VL_SPCESS_'.$i]) ? $request['ASSESSABLE_VL_SPCESS_'.$i] : 0),
                    'SPCESS_RATE'               => (!is_null($request['SPCESS_RATE_'.$i]) ? $request['SPCESS_RATE_'.$i] : 0),
                ];
            }
        }
        
        if(isset($reqdata6)){ 
            $wrapped_links6["TDSD"] = $reqdata6; 
            $XMLTDSD = ArrayToXml::convert($wrapped_links6);
        }
        else{
            $XMLTDSD = NULL; 
        }

        $VTID_REF     =   39; //SOA VT ID
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $SONO = $request['SONO'];
        $SOID_REF = $request['SOID_REF'];

        $SODT = $request['SODT'];
        $GLID_REF = $request['GLID_REF'];
        $SLID_REF = $request['SLID_REF'];
        $SOAFC = (isset($request['SOFC'])!="true" ? 0 : 1);
        $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
        $OVFDT = $request['OVFDT'];
        $OVTDT = $request['OVTDT'];
        $CUSTOMERPONO = $request['CUSTOMERPONO'];
        $CUSTOMERPODT = $request['CUSTOMERDT'];
        $SPID_REF = $request['SPID_REF'];
     
        $CREDITDAYS = $request['CREDITDAYS'];
        $BILLTO = $request['BILLTO'];
        $SHIPTO = $request['SHIPTO'];
        $REMARKS = $request['REMARKS'];

        $SOA_DT = $request['SOA_DT'];
        $CUSTOMERAREFNO = trim($request['CUSTOMERAREFNO']);
        $CUSTOMERAREFDT = $request['CUSTOMERAREFDT'];
        $REASONOFSOA = trim($request['REASONOFSOA']);



        $GST_N_Avail = (isset($request['GST_N_Avail'])!="true" ? 0 : 1);
        $GST_Reverse = (isset($request['GST_Reverse'])!="true" ? 0 : 1);
        $EXE_GST = (isset($request['EXE_GST'])!="true" ? 0 : 1);
        $Template_Description = $request['Template_Description'];
        $TDS   = (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);

        $DEALERID_REF           = $request['DEALERID_REF'];
        $PROJECTID_REF          = $request['PROJECTID_REF'];
        $DEALER_COMMISSION_AMT  = $request['DEALER_COMMISSION_AMT'];

        $PRICE_BASED_ON         = isset($request['PRICE_BASED_ON'])?trim($request['PRICE_BASED_ON']):NULL;

      
        $log_data = [ 
            $SOID_REF,          $GLID_REF,      $SLID_REF,      $SPID_REF,  $SONO,
            $SOA_DT,            $SOAFC,         $CRID_REF,      $CONVFACT,  $CUSTOMERAREFNO,
            $CUSTOMERAREFDT,    $REASONOFSOA,   $OVFDT,         $OVTDT,     $CUSTOMERPONO,
            $CUSTOMERPODT,      $BILLTO,        $SHIPTO,        $REMARKS,   $CREDITDAYS,
            $CYID_REF,          $BRID_REF,      $FYID_REF,      $VTID_REF,  $XMLMAT,
            $XMLTNC,            $XMLUDF,        $XMLCAL,        $XMLPSLB,   $USERID,
            Date('Y-m-d'),      Date('h:i:s.u'), $ACTIONNAME,    $IPADDRESS,$GST_N_Avail,
            $GST_Reverse,       $EXE_GST,       $Template_Description,      $XMLTDSD,$TDS,
            $DEALERID_REF,      $PROJECTID_REF, $DEALER_COMMISSION_AMT
            ,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT,$PRICE_BASED_ON
        ];

     
        
        $sp_result = DB::select('EXEC SP_SOA_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?, ?,?,?,?,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $log_data);       
      
 
       $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();  
    
       
    }

    public function getItemDetailsQuotationwiseAmend(Request $request){

        
        $Status = "A";
        $id = $request['id'];
        $soidRef = $request['SOID_REF'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $taxstate = $request['taxstate'];  
        $contains = Str::contains($id, 'A');

        $AlpsStatus =   $this->AlpsStatus();

            $QuoteID = DB::select('SELECT * FROM TBL_TRN_SLQT01_HDR
                    WHERE SQNO = ? AND CYID_REF = ? AND BRID_REF = ? AND FYID_REF = ?',
                    [$id,$CYID_REF,$BRID_REF,$FYID_REF]);
            
            $SQAID = $QuoteID[0]->SQID;

                    $Objquote =  DB::select('SELECT * FROM TBL_TRN_SLQT01_MAT  
                    WHERE PENDING_QTY > ? AND SQID_REF = ? order by SQMATID ASC', ['0.000',$SQAID]);

                    


                  
                    if(!empty($Objquote)){

                        foreach ($Objquote as $index=>$dataRow){

                            $ObjEnquiry = DB::select('SELECT TOP 1 * FROM TBL_TRN_SLEQ01_HDR
                                        WHERE SEQID=?',[$dataRow->SEQID_REF]);
                            
                            $ObjItem =  DB::select('SELECT top 1 * FROM TBL_MST_ITEM  
                                        WHERE ITEMID = ? ', [$dataRow->ITEMID_REF]);

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
                                                    $StdCost = $dataRow->RATEPUOM;
                                                }
                        
                            $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                        WHERE  CYID_REF = ?  AND UOMID = ? 
                                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                        [$CYID_REF,$dataRow->MAIN_UOMID_REF, $Status ]);

                            $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                        WHERE  CYID_REF = ?  AND UOMID = ? 
                                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                        [$CYID_REF, $dataRow->ALT_UOMID_REF, $Status ]);
                            
                            $ObjAltQTY =  DB::select('SELECT TOP 1  * FROM TBL_MST_ITEM_UOMCONV  
                                        WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                                        [$dataRow->ITEMID_REF,$dataRow->ALT_UOMID_REF ]);
                            
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
                                        

                        
                          
                            $objConsumed = DB::select("select ISNULL(sum(table1.CHALLAN_MAINQTY),0) as TOTAL_CONSUMED_QTY from tbl_trn_slsc01_mat table1 
                            left join tbl_trn_slsc01_hdr table2 on table1.SCID_REF = table2.SCID 
                            WHERE table1.SO=? AND table1.ITEMID_REF=? AND table1.SQID_REF=? AND table1.SEID_REF=? AND table2.STATUS<>? " , [$soidRef, $dataRow->ITEMID_REF,$dataRow->SQID_REF,$dataRow->SEQID_REF,'C']);
                
                            $CONSUMED_TOTAL = number_format(floatVal($objConsumed[0]->TOTAL_CONSUMED_QTY), 3, '.', '');  
                        

                            $row = '';
                            if($taxstate != "OutofState"){
                            $row = $row.'<tr id="item_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                            $row = $row.'<td style="width:10%;">'.$ObjItem[0]->ICODE;
                            $row = $row.'<input type="hidden" id="txtitem_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ICODE.'" data-desc1="'.$dataRow->DISCPER.'" data-desc2="'.$dataRow->DISCOUNT_AMT.'"
                            value="'.$ObjItem[0]->ITEMID.'" data-totalconsumed="'.$CONSUMED_TOTAL.'"/></td><td style="width:10%;" id="itemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" >'.$ObjItem[0]->NAME ;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ITEM_SPECI.'"
                            value="'.$ObjItem[0]->NAME.'"/></td>';
                            $row = $row.'<td style="width:8%;" id="itemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtitemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                            data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                            $row = $row.'<td style="width:8%;" id="uomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtuomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$TOQTY.'"
                            value="'.$dataRow->ALT_UOMID_REF.'"/>'.$dataRow->SQ_QTY.'</td>';
                            $row = $row.'<td style="width:8%;" id="irate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtirate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$FROMQTY.'"
                            value="'.$dataRow->RATEPUOM.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                            $row = $row.'<td style="width:8%;" id="itax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$Taxid[0].'"
                            value="'.$Taxid[1].'"/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                            
                            <td style="width:8%;">'.$BusinessUnit.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                            <td style="width:8%;" id="ise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"  data-desc="'.$dataRow->SEQID_REF.'"
                            value="'.$QuoteID[0]->SQNO.'"/>Authorized</td>
                            </tr>';
                            }
                            else
                            {
                            $row = $row.'<tr id="item_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                            $row = $row.'<td style="width:10%;">'.$ObjItem[0]->ICODE;
                            $row = $row.'<input type="hidden" id="txtitem_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ICODE.'" data-desc1="'.$dataRow->DISCPER.'" data-desc2="'.$dataRow->DISCOUNT_AMT.'"    value="'.$ObjItem[0]->ITEMID.'"  data-totalconsumed="'.$CONSUMED_TOTAL.'"/></td><td style="width:10%;" id="itemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" >'.$ObjItem[0]->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ITEM_SPECI.'"
                            value="'.$ObjItem[0]->NAME.'"/></td>';
                            $row = $row.'<td style="width:8%;" id="itemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtitemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                            data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                            $row = $row.'<td style="width:8%;" id="uomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtuomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$TOQTY.'"
                            value="'.$dataRow->ALT_UOMID_REF.'"/>'.$dataRow->SQ_QTY.'</td>';
                            $row = $row.'<td style="width:8%;" id="irate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtirate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$FROMQTY.'"
                            value="'.$dataRow->RATEPUOM.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                            $row = $row.'<td style="width:8%;" id="itax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$Taxid[0].'"
                            value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                            
                            <td style="width:8%;">'.$BusinessUnit.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                            <td style="width:8%;" id="ise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$dataRow->SEQID_REF.'"
                            value="'.$QuoteID[0]->SQNO.'"/>Authorized</td>
                            </tr>';
                            }

                        echo $row;
                        }

                    }else{
                        echo '<tr><td> Record not found.</td></tr>';
                    }
        
           
        exit();
    
    }


    public function getItemDetailswithoutQuotationAmend(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $taxstate = $request['taxstate'];
        $StdCost = 0;

        $AlpsStatus =   $this->AlpsStatus();

        $soidRef = $request['SOID_REF'];
        
                
        $ObjItem =  DB::select('SELECT * FROM TBL_MST_ITEM  
                    WHERE CYID_REF = ? 
                    AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                    [$CYID_REF, $Status ]);
       
                if(!empty($ObjItem)){

                    foreach ($ObjItem as $index=>$dataRow){
                    
                        $ObjLIST =   DB::table('TBL_MST_PRICELIST_MAT')  
                        ->select('TBL_MST_PRICELIST_MAT.*')
                        ->where('TBL_MST_PRICELIST_MAT.ITEMID_REF','=',$dataRow->ITEMID)
                        ->first();
                       
                                   
                            if(($ObjLIST)){
                                $ObjInTax = $ObjLIST->GST_IN_LP; 
                                
                                    if ($ObjInTax == 1){
                                        $Taxid = [];
                                        $ObjHSN =   DB::select('SELECT top 1 HSNID_REF FROM TBL_MST_ITEM  
                                                WHERE STATUS= ? AND ITEMID = ? ', ['A',$dataRow->ITEMID]);
                                        
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
                                        $Taxid = [];
                                        $ObjHSN =   DB::select('SELECT top 1 HSNID_REF FROM TBL_MST_ITEM  
                                                WHERE STATUS= ? AND ITEMID = ? ', ['A',$dataRow->ITEMID]);
                                        
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
                                        foreach ($ObjTax as $tindex=>$tRow)
                                        {   
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
                                                WHERE STATUS= ? AND ITEMID = ? ', ['A',$dataRow->ITEMID]);
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
                                    foreach ($ObjTax as $tindex=>$tRow)
                                    {
                                           if($tRow->NRATE !== '')
                                            {
                                            array_push($Taxid,$tRow->NRATE);
                                            }
                                        }
                                    $StdCost = $dataRow->STDCOST;
                                }
                    
                    
                    $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ?  AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $dataRow->MAIN_UOMID_REF, 'A' ]);

                    $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ?  AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $dataRow->ALT_UOMID_REF, $Status ]);
                    
                    $ObjAltQTY =  DB::select('SELECT TOP 1  * FROM TBL_MST_ITEM_UOMCONV  
                                WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                                [$dataRow->ITEMID,$dataRow->ALT_UOMID_REF ]);

                    $TOQTY =  isset($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 0;
                    $FROMQTY =  isset($ObjAltQTY[0]->FROM_QTY)? $ObjAltQTY[0]->FROM_QTY : 0;

                    $ObjItemGroup =  DB::select('SELECT TOP 1 GROUPCODE, GROUPNAME FROM TBL_MST_ITEMGROUP  
                                WHERE  CYID_REF = ?  AND ITEMGID = ?
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                [$CYID_REF, $dataRow->ITEMGID_REF, 'A' ]);

                    $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS FROM TBL_MST_ITEMCATEGORY  
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

                    
               
                    $objConsumed = DB::select("select ISNULL(sum(table1.CHALLAN_MAINQTY),0) as TOTAL_CONSUMED_QTY from tbl_trn_slsc01_mat table1 
                    left join tbl_trn_slsc01_hdr table2 on table1.SCID_REF = table2.SCID 
                    WHERE table1.SO=? AND table1.ITEMID_REF=? AND table2.STATUS<>? " , [$soidRef, $dataRow->ITEMID,'C']);
        
                    $CONSUMED_TOTAL = number_format(floatVal($objConsumed[0]->TOTAL_CONSUMED_QTY), 3, '.', '');  
                  
                    

                        $row = '';
                        if($taxstate != "OutofState"){
                        $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                        $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'" data-desc1="'.$dataRow->DISCPER.'" data-desc2="'.$dataRow->DISCOUNT_AMT.'"
                        value="'.$dataRow->ITEMID.'" data-totalconsumed="'.$CONSUMED_TOTAL.'"/></td>
                        <td style="width:10%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'"
                        value="'.$dataRow->NAME.'"/></td>';
                        $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                        data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td style="width:8%;" id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'"
                        value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                        $row = $row.'<td style="width:8%;" id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="'.$FROMQTY.'"
                        value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                        $row = $row.'<td style="width:8%;" id="itax_'.$dataRow->ITEMID.'"><input type="hidden" id="txtitax_'.$dataRow->ITEMID.'" data-desc="'.$Taxid[0].'"
                        value="'.$Taxid[1].'"/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                        
                        <td style="width:8%;">'.$BusinessUnit.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                        <td style="width:8%;">Authorized</td>
                        </tr>';
                        }
                        else
                        {
                            $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                            $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                            $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'" data-desc1="'.$dataRow->DISCPER.'" data-desc2="'.$dataRow->DISCOUNT_AMT.'"
                            value="'.$dataRow->ITEMID.'" data-totalconsumed="'.$CONSUMED_TOTAL.'"/></td>
                            <td style="width:10%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'"
                            value="'.$dataRow->NAME.'"/></td>';
                            $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                            data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                            $row = $row.'<td style="width:8%;" id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'"
                            value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                            $row = $row.'<td style="width:8%;" id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="'.$FROMQTY.'"
                            value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                            $row = $row.'<td style="width:8%;" id="itax_'.$dataRow->ITEMID.'"><input type="hidden" id="txtitax_'.$dataRow->ITEMID.'" data-desc="'.$Taxid[0].'"
                            value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                            
                            <td style="width:8%;">'.$BusinessUnit.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                            <td style="width:8%;">Authorized</td>
                            </tr>';   
                        }
                        echo $row;    
                    } 
                    
                }           
                else{
                 echo '<tr><td> Record not found.</td></tr>';
                }
        exit();
    }

    
   public function getTDSApplicability(Request $request){
	$Status = "A";
	$SLID_REF   =   $request['id'];

	$ObjVendor  =   DB::table('TBL_MST_CUSTOMER')
					->where('STATUS','=',$Status)
					->where('SLID_REF','=',$SLID_REF)
					->select('TDS_APPLICABLE')
					->first();

	if($ObjVendor->TDS_APPLICABLE =="1"){
		echo '1';
	}
	else{
		echo '0';
	}
}
    
public function getTDSDetails(Request $request){
	$Status = "A";
	$SLID_REF   =   $request['id'];	
	$BRID_REF = Session::get('BRID_REF');
	
	$sp_param = [ 
		$SLID_REF,$BRID_REF
	];  

  

	$sp_result = DB::select('EXEC SP_GET_CUSTOMER_TDSDETAILS ?,?', $sp_param);


	if(!empty($sp_result))
	{
		foreach ($sp_result as $index=>$dataRow){
		
			$row = '';
			$row = $row.'<tr class="participantRow7">
			<td style="text-align:center;">
			<input type="text" name="txtTDS_'.$index.'" id="txtTDS_'.$index.'" class="form-control" value="'.$dataRow->CODE.'"  autocomplete="off"  readonly/></td>
			<td hidden><input type="hidden" name="TDSID_REF_'.$index.'" id="TDSID_REF_'.$index.'" class="form-control" value="'.$dataRow->HOLDINGID.'" autocomplete="off" /></td>
			<td><input type="text" name="TDSLedger_'.$index.'" id="TDSLedger_'.$index.'" value="'.$dataRow->CODE_DESC.'" class="form-control"  autocomplete="off"  readonly/></td>
			<td style="text-align:center;"><input type="checkbox" name="TDSApplicable_'.$index.'" id="TDSApplicable_'.$index.'" /></td>
			<td><input type="text" name="ASSESSABLE_VL_TDS_'.$index.'" id="ASSESSABLE_VL_TDS_'.$index.'"  class="form-control two-digits" maxlength="15"  autocomplete="off"  /></td>
			<td><input type="text" name="TDS_RATE_'.$index.'" id="TDS_RATE_'.$index.'" value="'.$dataRow->TDS_RATE.'" class="form-control four-digits" maxlength="12"  autocomplete="off"  readonly/></td>
			<td hidden><input type="hidden" name="TDS_EXEMPT_'.$index.'" id="TDS_EXEMPT_'.$index.'" class="form-control two-digits" value="0.00" /></td>
			<td><input type="text" name="TDS_AMT_'.$index.'" id="TDS_AMT_'.$index.'" class="form-control two-digits" maxlength="15"  autocomplete="off" readonly /></td>
			<td><input type="text" name="ASSESSABLE_VL_SURCHARGE_'.$index.'" id="ASSESSABLE_VL_SURCHARGE_'.$index.'"  class="form-control two-digits" maxlength="15"  autocomplete="off"  /></td>
			<td><input type="text" name="SURCHARGE_RATE_'.$index.'" id="SURCHARGE_RATE_'.$index.'" value="'.$dataRow->SURCHARGE_RAGE.'" class="form-control four-digits" maxlength="12"  autocomplete="off" readonly /></td>
			<td hidden><input type="hidden" name="SURCHARGE_EXEMPT_'.$index.'" id="SURCHARGE_EXEMPT_'.$index.'" class="form-control two-digits" value="0.00" /></td>
			<td><input type="text" name="SURCHARGE_AMT_'.$index.'" id="SURCHARGE_AMT_'.$index.'" class="form-control two-digits" maxlength="15"  autocomplete="off" readonly /></td>
			<td><input type="text" name="ASSESSABLE_VL_CESS_'.$index.'" id="ASSESSABLE_VL_CESS_'.$index.'"  class="form-control two-digits" maxlength="15"  autocomplete="off"  /></td>
			<td><input type="text" name="CESS_RATE_'.$index.'" id="CESS_RATE_'.$index.'" value="'.$dataRow->CESS_RATE.'" class="form-control four-digits" maxlength="12"  autocomplete="off"  readonly/></td>
			<td hidden><input type="hidden" name="CESS_EXEMPT_'.$index.'" id="CESS_EXEMPT_'.$index.'" class="form-control two-digits" value="0.00" /></td>
			<td><input type="text" name="CESS_AMT_'.$index.'" id="CESS_AMT_'.$index.'" class="form-control two-digits" maxlength="15"  autocomplete="off" readonly /></td>
			<td><input type="text" name="ASSESSABLE_VL_SPCESS_'.$index.'" id="ASSESSABLE_VL_SPCESS_'.$index.'"  class="form-control two-digits" maxlength="15"  autocomplete="off"  /></td>
			<td><input type="text" name="SPCESS_RATE_'.$index.'" id="SPCESS_RATE_'.$index.'" value="'.$dataRow->SP_CESS_RATE.'" class="form-control four-digits" maxlength="12"  autocomplete="off" readonly /></td>
			<td hidden><input type="hidden" name="SPCESS_EXEMPT_'.$index.'" id="SPCESS_EXEMPT_'.$index.'" class="form-control two-digits" value="0.00" /></td>
			<td><input type="text" name="SPCESS_AMT_'.$index.'" id="SPCESS_AMT_'.$index.'" class="form-control two-digits" maxlength="15"  autocomplete="off" readonly /></td>
			<td><input type="text" name="TOT_TD_AMT_'.$index.'" id="TOT_TD_AMT_'.$index.'" class="form-control two-digits" maxlength="15"  autocomplete="off" readonly /></td>
			<td style="min-width: 100px;"><button class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
			<button class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
		  </tr>
		  <tr></tr>';

			echo $row;
		}

		}else{
			echo '<tr  class="participantRow7">
			<td style="text-align:center;" >
			<input type="text" name="txtTDS_0" id="txtTDS_0" class="form-control"  autocomplete="off"  readonly/></td>
			<td hidden><input type="hidden" name="TDSID_REF_0" id="TDSID_REF_0" class="form-control" autocomplete="off" /></td>
			<td><input type="text" name="TDSLedger_0" id="TDSLedger_0" class="form-control"  autocomplete="off"  readonly/></td>
			<td  align="center" style="text-align:center;" ><input type="checkbox" name="TDSApplicable_0" id="TDSApplicable_0" /></td>
			<td><input type="text" name="ASSESSABLE_VL_TDS_0" id="ASSESSABLE_VL_TDS_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  /></td>
			<td><input type="text" name="TDS_RATE_0" id="TDS_RATE_0" class="form-control four-digits" maxlength="12"  autocomplete="off"  /></td>
			<td hidden><input type="hidden" name="TDS_EXEMPT_0" id="TDS_EXEMPT_0" class="form-control two-digits" /></td>
			<td><input type="text" name="TDS_AMT_0" id="TDS_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly/></td>
			<td><input type="text" name="ASSESSABLE_VL_SURCHARGE_0" id="ASSESSABLE_VL_SURCHARGE_0" class="form-control two-digits" maxlength="15"  autocomplete="off" /></td>
			<td><input type="text" name="SURCHARGE_RATE_0" id="SURCHARGE_RATE_0" class="form-control four-digits" maxlength="12"  autocomplete="off"  /></td>
			<td hidden><input type="hidden" name="SURCHARGE_EXEMPT_0" id="SURCHARGE_EXEMPT_0" class="form-control two-digits" /></td>
			<td><input type="text" name="SURCHARGE_AMT_0" id="SURCHARGE_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly/></td>
			<td><input type="text" name="ASSESSABLE_VL_CESS_0" id="ASSESSABLE_VL_CESS_0" class="form-control two-digits" maxlength="15"  autocomplete="off" /></td>
			<td><input type="text" name="CESS_RATE_0" id="CESS_RATE_0" class="form-control four-digits" maxlength="12"  autocomplete="off"  /></td>
			<td hidden><input type="hidden" name="CESS_EXEMPT_0" id="CESS_EXEMPT_0" class="form-control two-digits" /></td>
			<td><input type="text" name="CESS_AMT_0" id="CESS_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly/></td>
			<td><input type="text" name="ASSESSABLE_VL_SPCESS_0" id="ASSESSABLE_VL_SPCESS_0" class="form-control two-digits" maxlength="15"  autocomplete="off" /></td>
			<td><input type="text" name="SPCESS_RATE_0" id="SPCESS_RATE_0" class="form-control four-digits" maxlength="12"  autocomplete="off"  /></td>
			<td hidden><input type="hidden" name="SPCESS_EXEMPT_0" id="SPCESS_EXEMPT_0" class="form-control two-digits" /></td>
			<td><input type="text" name="SPCESS_AMT_0" id="SPCESS_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly/></td>
			<td><input type="text" name="TOT_TD_AMT_0" id="TOT_TD_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly/></td>
			<td style="min-width: 100px;"><button class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
			<button class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
		</tr>
		<tr></tr>';
		}
	exit();
}

public function getTaxStatus(Request $request){
	$Status     =   "A";
	$SLID_REF   =   $request['id'];
	$CYID_REF   =   Auth::user()->CYID_REF;
	$BRID_REF   =   Session::get('BRID_REF');
	
	$TaxStatus  =   DB::table('TBL_MST_CUSTOMER')
					->where('CYID_REF','=',$CYID_REF)
					->where('BRID_REF','=',$BRID_REF)
					->where('SLID_REF','=',$SLID_REF)
					->select('EXE_GST')->first()->EXE_GST;

	echo $TaxStatus;
}


   public function getTemplateMaster($VOUCHER_TYPE){

	$CYID_REF = Auth::user()->CYID_REF;
	$BRID_REF = Session::get('BRID_REF');
		
	$objTemplateMaster  =   DB::select('SELECT TEMPLATEID, TEMPLATE_NAME, TEMPLATE,INDATE FROM TBL_MST_TEMPLATE  
	WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ? AND TEMPLATE_FOR=?
	order by TEMPLATEID ASC', [$CYID_REF, $BRID_REF, 'A',$VOUCHER_TYPE ]);

	return $objTemplateMaster;

}

public function AlpsStatus(){

    $COMPANY_NAME   =   DB::table('TBL_MST_COMPANY')->where('STATUS','=','A')->where('CYID','=',Auth::user()->CYID_REF)->select('TBL_MST_COMPANY.NAME')->first()->NAME;
    
    
    $disabled       =   strpos($COMPANY_NAME,"ALPS")!== false?'disabled':'';
    $hidden         =   strpos($COMPANY_NAME,"ALPS")!== false?'':'hidden';
    $readonly         =   strpos($COMPANY_NAME,"ALPS")!== false?'readonly':'';
    $colspan         =   strpos($COMPANY_NAME,"ALPS")!== false?9:6;
 
    return  $ALPS_STATUS=array(
        'hidden'=>$hidden,
        'disabled'=>$disabled,
        'colspan'=>$colspan,
        'readonly'=>$readonly,
    );

}


public function get_lead($SQID_REF)
{
    if($SQID_REF != ''){
    $objLeadData=DB::select("SELECT L.LEAD_NO,L.LEAD_DT FROM TBL_TRN_SLQT01_HDR H (NOLOCK)      
    LEFT JOIN TBL_TRN_LEAD_GENERATION L ON H.LEADID_REF=L.LEAD_ID
    WHERE H.SQID=$SQID_REF"); 
    }
    $objLead=array("LEAD_NO"=>isset($objLeadData[0]->LEAD_NO) ? $objLeadData[0]->LEAD_NO:'' ,"LEAD_DT"=>isset($objLeadData[0]->LEAD_DT) ? $objLeadData[0]->LEAD_DT:'');
    return $objLead;        
}



public function get_Dealer(Request $request) {   
    $Status = "A";
    $CYID_REF = Auth::user()->CYID_REF;
    $BRID_REF = Session::get('BRID_REF');
    $FYID_REF = Session::get('FYID_REF');        
    $objDealer = DB::table('TBL_MST_CUSTOMER')
    ->where('CYID_REF','=',Auth::user()->CYID_REF)
    //->where('BRID_REF','=',Session::get('BRID_REF'))
    ->where('STATUS','=',$Status)     
    ->where('TYPE','=','DEALER')     
    ->select('CID AS DOCID','CCODE AS DOCNO','NAME AS DESC','COMMISION')
    ->orderBy('NAME')
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
    ->orderBy('DESCRIPTIONS')
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
    $SODT   =   $request['SODT'];  
    $objScheme = DB::select("SELECT SCHEMEID AS DOCID,SCHEME_NO AS DOCNO,SCHEME_NAME AS DESCRIPTIONS FROM TBL_MST_SCHEME_HDR 
	WHERE CYID_REF=$CYID_REF AND BRID_REF=$BRID_REF AND STATUS='$Status' AND '$SODT' BETWEEN EFF_FROM_DATE AND EFF_TO_DATE");
 
    //dd($objScheme); 



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



   
   public function GetSchemeMaterialItems(Request $request){
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
                'txtSQ_popup'          =>   $request['txtSQ_popup_'.$i],
                'SQA'                  =>   $request['SQA_'.$i],
                'SEQID_REF'            =>   $request['SEQID_REF_'.$i],
                'LEADNO'               =>   $request['LEADNO_'.$i],
                'LEADDT'               =>   $request['LEADDT_'.$i],
                'popupITEMID'          =>   $request['popupITEMID_'.$i],
                'ITEMID_REF'           =>   $request['ITEMID_REF_'.$i],                      
                'TSID_REF'             =>   $request['TSID_REF_'.$i],
                'ItemName'             =>   $request['ItemName_'.$i],
                'Itemspec'             =>   $request['Itemspec_'.$i],
                'Alpspartno'           =>   $request['Alpspartno_'.$i],
                'Custpartno'           =>   $request['Custpartno_'.$i],
                'OEMpartno'            =>   $request['OEMpartno_'.$i],
                'SQMUOM'               =>   $request['SQMUOM_'.$i],
                'SQMUOMQTY'            =>   $request['SQMUOMQTY_'.$i],
                'SQAUOM'               =>   $request['SQAUOM_'.$i],                 
                'SQAUOMQTY'            =>   $request['SQAUOMQTY_'.$i],                   
                'popupMUOM'            =>   $request['popupMUOM_'.$i],                  
                'MAIN_UOMID_REF'       =>   $request['MAIN_UOMID_REF_'.$i],                  
                'SO_QTY'               =>   $request['SO_QTY_'.$i],                  
                'SO_FQTY'              =>   $request['SO_FQTY_'.$i],               
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
                    'txtSQ_popup'          =>   $request['txtSQ_popup_'.$i],
                    'SQA'                  =>   $request['SQA_'.$i],
                    'SEQID_REF'            =>   $request['SEQID_REF_'.$i],
                    'LEADNO'               =>   $request['LEADNO_'.$i],
                    'LEADDT'               =>   $request['LEADDT_'.$i],
                    'popupITEMID'          =>   $request['popupITEMID_'.$i],
                    'ITEMID_REF'           =>   $request['ITEMID_REF_'.$i],                      
                    'TSID_REF'             =>   $request['TSID_REF_'.$i],
                    'ItemName'             =>   $request['ItemName_'.$i],
                    'Itemspec'             =>   $request['Itemspec_'.$i],
                    'Alpspartno'           =>   $request['Alpspartno_'.$i],
                    'Custpartno'           =>   $request['Custpartno_'.$i],
                    'OEMpartno'            =>   $request['OEMpartno_'.$i],
                    'SQMUOM'               =>   $request['SQMUOM_'.$i],
                    'SQMUOMQTY'            =>   $request['SQMUOMQTY_'.$i],
                    'SQAUOM'               =>   $request['SQAUOM_'.$i],                 
                    'SQAUOMQTY'            =>   $request['SQAUOMQTY_'.$i],                   
                    'popupMUOM'            =>   $request['popupMUOM_'.$i],                  
                    'MAIN_UOMID_REF'       =>   $request['MAIN_UOMID_REF_'.$i],                  
                    'SO_QTY'               =>   $request['SO_QTY_'.$i],                  
                    'SO_FQTY'              =>   $request['SO_FQTY_'.$i],               
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
                'txtSQ_popup'          =>   $request['txtSQ_popup_'.$i],
                'SQA'                  =>   $request['SQA_'.$i],
                'SEQID_REF'            =>   $request['SEQID_REF_'.$i],
                'LEADNO'               =>   $request['LEADNO_'.$i],
                'LEADDT'               =>   $request['LEADDT_'.$i],
                'popupITEMID'          =>   $request['popupITEMID_'.$i],
                'ITEMID_REF'           =>   $request['ITEMID_REF_'.$i],                      
                'TSID_REF'             =>   $request['TSID_REF_'.$i],
                'ItemName'             =>   $request['ItemName_'.$i],
                'Itemspec'             =>   $request['Itemspec_'.$i],
                'Alpspartno'           =>   $request['Alpspartno_'.$i],
                'Custpartno'           =>   $request['Custpartno_'.$i],
                'OEMpartno'            =>   $request['OEMpartno_'.$i],
                'SQMUOM'               =>   $request['SQMUOM_'.$i],
                'SQMUOMQTY'            =>   $request['SQMUOMQTY_'.$i],
                'SQAUOM'               =>   $request['SQAUOM_'.$i],                 
                'SQAUOMQTY'            =>   $request['SQAUOMQTY_'.$i],                   
                'popupMUOM'            =>   $request['popupMUOM_'.$i],                  
                'MAIN_UOMID_REF'       =>   $request['MAIN_UOMID_REF_'.$i],                  
                'SO_QTY'               =>   $request['SO_QTY_'.$i],                  
                'SO_FQTY'              =>   $request['SO_FQTY_'.$i],               
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
        'txtSQ_popup'          =>   '',
        'SQA'                  =>   '',
        'SEQID_REF'            =>   '',
        'LEADNO'               =>   '',
        'LEADDT'               =>   '',
        'popupITEMID'          =>   $row_data->ICODE,
        'ITEMID_REF'           =>   $row_data->ITEMID,                
        'TSID_REF'             =>   '',
        'ItemName'             =>   $row_data->ITEM_NAME,
        'Itemspec'             =>   '',
        'Alpspartno'           =>   $row_data->ALPS_PART_NO,
        'Custpartno'           =>   $row_data->CUSTOMER_PART_NO,
        'OEMpartno'            =>   $row_data->OEM_PART_NO,
        'SQMUOM'               =>   '',
        'SQMUOMQTY'            =>   '',
        'SQAUOM'               =>   '',                 
        'SQAUOMQTY'            =>   '',                   
        'popupMUOM'            =>   $row_data->MAIN_UOM,                
        'MAIN_UOMID_REF'       =>   $row_data->MAIN_UOMID_REF,               
        'SO_QTY'               =>   $row_data->ITEM_QTY,                 
        'SO_FQTY'              =>   $row_data->ITEM_QTY,               
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
        'txtSQ_popup'          =>   '',
        'SQA'                  =>   '',
        'SEQID_REF'            =>   '',
        'LEADNO'               =>   '',
        'LEADDT'               =>   '',
        'popupITEMID'          =>   $row_data->ICODE,
        'ITEMID_REF'           =>   $row_data->ITEMID,                
        'TSID_REF'             =>   '',
        'ItemName'             =>   $row_data->ITEM_NAME,
        'Itemspec'             =>   '',
        'Alpspartno'           =>   $row_data->ALPS_PART_NO,
        'Custpartno'           =>   $row_data->CUSTOMER_PART_NO,
        'OEMpartno'            =>   $row_data->OEM_PART_NO,
        'SQMUOM'               =>   '',
        'SQMUOMQTY'            =>   '',
        'SQAUOM'               =>   '',                 
        'SQAUOMQTY'            =>   '',                   
        'popupMUOM'            =>   $row_data->MAIN_UOM,                
        'MAIN_UOMID_REF'       =>   $row_data->MAIN_UOMID_REF,               
        'SO_QTY'               =>   $row_data->ITEM_QTY,                 
        'SO_FQTY'              =>   $row_data->ITEM_QTY,               
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
        <table id="example2" class="display nowrap table table-striped table-bordered itemlist"  style="width:100%;height:auto !important;">
        <thead id="thead1"  style="position: sticky;top: 0">
            <tr>
                <th colspan="'.$AlpsStatus['colspan'].'"></th>
                <th colspan="4">Sales Quotation / SQ Amendment</th>
                <th colspan="4">Sales Order</th>
                <th colspan="17"></th>
            </tr>
            <tr>
                <th rowspan="2" >SQ / SQA No<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="'.$Row_Count1.'">
                </th>
                <th rowspan="2" >Lead No</th>
                <th rowspan="2" >Lead Date</th>
                <th rowspan="2" >Item Code</th>
                <th rowspan="2">Technical Specification</th>
                <th rowspan="2" >Item Name</th>
                <th rowspan="2" >Item Specification</th>

                <th rowspan="2"  '.$AlpsStatus['hidden'].' >'.$COLUMN1.'</th>
                <th rowspan="2"  '.$AlpsStatus['hidden'].' >'.$COLUMN2.'</th>
                <th rowspan="2"  '.$AlpsStatus['hidden'].' >'.$COLUMN3.'</th>

                <th rowspan="2" >Main UOM</th>
                <th rowspan="2" >Qty(Main UOM)</th>
                <th rowspan="2" >ALT UOM</th>
                <th rowspan="2" >Qty(Alt UOM)</th>
                <th rowspan="2" >Main UOM</th>
                <th rowspan="2" >Qty(Main UOM)</th>
                <th rowspan="2" >ALT UOM</th>
                <th rowspan="2" >Qty(Alt UOM)</th>
                <th rowspan="2" >Rate Per UoM</th>
                <th colspan="2" >Discount</th>
                <th rowspan="2" >Amount after discount</th>
                <th rowspan="2" >IGST Rate %</th>
                <th rowspan="2" >IGST Amount</th>
                <th rowspan="2" >CGST Rate %</th>
                <th rowspan="2" >CGST Amount</th>
                <th rowspan="2" >SGST Rate %</th>
                <th rowspan="2" >SGST Amount</th>
                <th rowspan="2" >Total GST Amount</th>
                <th rowspan="2" >Total after GST</th>
                <th rowspan="2" >Action</th>
            </tr>
            <tr>
                <th>%</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody> 
               
        ';

        

       // dd($final_data);

                foreach($final_data as $index=>$row_data){

                    echo '<tr  class="participantRow">';
                    echo '<td>
                    <input  type="text" name="txtSQ_popup_'.$index.'"  id="txtSQ_popup_'.$index.'" class="form-control"  autocomplete="off" readonly  disabled style="width:130px;" value="'.$row_data['txtSQ_popup'].'"/></td>';
                    echo '<td hidden><input type="hidden" name="SQA_'.$index.'" id="SQA_'.$index.'" class="form-control" autocomplete="off" style="width:130px;" value="'.$row_data['SQA'].'" /></td>'; 
                    echo '<td hidden><input type="hidden"  name="SCHEMEID_REF_'.$index.'" id="SCHEMEID_REF_'.$index.'" class="form-control" autocomplete="off" style="width:130px;" value="'.$row_data['SCHEMEID_REF'].'" /></td>'; 
                    echo '<td hidden><input type="hidden" name="ITEM_TYPE_'.$index.'" id="ITEM_TYPE_'.$index.'" class="form-control '.$row_data['ITEM_TYPE'].'" autocomplete="off" style="width:130px;" value="'.$row_data['ITEM_TYPE'].'" /></td>'; 

                    echo '<td hidden><input type="hidden" name="SEQID_REF_'.$index.'" id="SEQID_REF_'.$index.'" class="form-control" autocomplete="off" value="'.$row_data['SEQID_REF'].'" /></td>';
                    echo '<td><input type="text" name="LEADNO_'.$index.'" id="LEADNO_'.$index.'" class="form-control"  autocomplete="off"  readonly style="width:130px;" value="'.$row_data['LEADNO'].'"/></td>';
                    echo '<td><input type="text" name="LEADDT_'.$index.'" id="LEADDT_'.$index.'" class="form-control"  autocomplete="off"  readonly style="width:130px;" value="'.$row_data['LEADDT'].'"/></td>'
                    ;
                    echo '<td><input type="text" name="popupITEMID_'.$index.'" id="popupITEMID_'.$index.'" class="form-control"  autocomplete="off"  readonly style="width:130px;" value="'.$row_data['popupITEMID'].'" /></td>';
                    echo '<td hidden><input type="hidden" name="ITEMID_REF_'.$index.'" id="ITEMID_REF_'.$index.'" class="form-control" autocomplete="off" value="'.$row_data['ITEMID_REF'].'" /></td>';


                    echo '<td><button id="TECHSPEC_'.$index.'" onclick="getTechnicalSpecification(this.id)" class="btn" type="button" ><i class="fa fa-clone"></i></button></td>';
                    echo '<td hidden ><input type="hidden" name="TSID_REF_'.$index.'" id="TSID_REF_'.$index.'" class="form-control" value="'.$row_data['TSID_REF'].'" autocomplete="off" /></td>'; 
                                                        


                    echo '<td><input type="text" name="ItemName_'.$index.'" id="ItemName_'.$index.'" class="form-control"  autocomplete="off"  readonly style="width:200px;" value="'.$row_data['ItemName'].'"/></td>';
                    echo '<td><input type="text" name="Itemspec_'.$index.'" id="Itemspec_'.$index.'" class="form-control"  autocomplete="off"  style="width:200px;" value="'.$row_data['Itemspec'].'"/></td>';
                    echo '<td '.$AlpsStatus['hidden'].' ><input type="text" name="Alpspartno_'.$index.'" id="Alpspartno_'.$index.'" class="form-control"  autocomplete="off"  readonly style="width:130px;" value="'.$row_data['Alpspartno'].'"/></td>';
                    echo '<td '.$AlpsStatus['hidden'].' ><input type="text" name="Custpartno_'.$index.'" id="Custpartno_'.$index.'" class="form-control"  autocomplete="off"  readonly style="width:130px;" value="'.$row_data['Custpartno'].'"/></td>';
                    echo '<td '.$AlpsStatus['hidden'].' ><input type="text" name="OEMpartno_'.$index.'" id="OEMpartno_'.$index.'" class="form-control"  autocomplete="off"  readonly style="width:130px;" value="'.$row_data['OEMpartno'].'"/></td>';
                    echo '<td><input type="text" name="SQMUOM_'.$index.'" id="SQMUOM_'.$index.'" class="form-control"  autocomplete="off"  readonly style="width:130px;" value="'.$row_data['SQMUOM'].'"/></td>';
                    echo '<td><input type="text" name="SQMUOMQTY_'.$index.'" id="SQMUOMQTY_'.$index.'" class="form-control" maxlength="13"  autocomplete="off"  readonly style="width:130px;text-align:right;" value="'.$row_data['SQMUOMQTY'].'"/></td>';
                    echo '<td ><input type="text" name="SQAUOM_'.$index.'" id="SQAUOM_'.$index.'" class="form-control"  autocomplete="off"  readonly style="width:130px;text-align:right;" value="'.$row_data['SQAUOM'].'"/></td>';
                    echo '<td><input type="text" name="SQAUOMQTY_'.$index.'" id="SQAUOMQTY_'.$index.'" class="form-control" maxlength="13" autocomplete="off"  readonly style="width:130px;text-align:right;" value="'.$row_data['SQAUOMQTY'].'"/></td>';
                    echo '<td ><input type="text" name="popupMUOM_'.$index.'" id="popupMUOM_'.$index.'" class="form-control"  autocomplete="off"  readonly style="width:130px;" value="'.$row_data['popupMUOM'].'"/></td>';
                    echo '<td hidden><input type="hidden" name="MAIN_UOMID_REF_'.$index.'" id="MAIN_UOMID_REF_'.$index.'" class="form-control"  autocomplete="off"  value="'.$row_data['MAIN_UOMID_REF'].'"/></td>';
                    echo '<td><input type="text" name="SO_QTY_'.$index.'" id="SO_QTY_'.$index.'" class="form-control three-digits '.$row_data['ITEM_TYPE'].'SCHEME'.$row_data['SCHEMEID_REF'].'" maxlength="13"  autocomplete="off"  style="width:130px;text-align:right;" onkeyup="dataCal(this.id)" onfocusout="dataDec(this,"2")" value="'.$row_data['SO_QTY'].'" /></td>';

                    echo '<td hidden><input type="hidden" name="SCHEMEQTY_'.$index.'" id="SCHEMEQTY_'.$index.'" class="form-control three-digits" maxlength="13"  autocomplete="off"  style="width:130px;text-align:right;"  onfocusout="dataDec(this,"2")" value="'.$row_data['SCHEMEQTY'].'" /></td>';


                    echo '<td hidden><input type="hidden" name="SO_FQTY_'.$index.'" id="SO_FQTY_'.$index.'" class="form-control three-digits" maxlength="13"  autocomplete="off"  value="'.$row_data['SO_FQTY'].'" readonly/></td>';
                    echo '<td><input type="text" name="popupAUOM_'.$index.'" id="popupAUOM_'.$index.'" class="form-control"  autocomplete="off"  readonly style="width:130px;" value="'.$row_data['popupAUOM'].'"/></td>';
                    echo '<td hidden><input type="hidden" name="ALT_UOMID_REF_'.$index.'" id="ALT_UOMID_REF_'.$index.'" class="form-control"  autocomplete="off"  readonly value="'.$row_data['ALT_UOMID_REF'].'" /></td>';
                    echo '<td><input type="text" name="ALT_UOMID_QTY_'.$index.'" id="ALT_UOMID_QTY_'.$index.'" class="form-control three-digits" maxlength="13" autocomplete="off"   style="width:130px;text-align:right;"  onkeyup="dataCal(this.id)"  onfocusout="dataCalculation(this.id)" value="'.$row_data['ALT_UOMID_QTY'].'"/></td>';
                                        
                    echo '<td><input type="text" name="RATEPUOM_'.$index.'" id="RATEPUOM_'.$index.'" onkeyup=dataCal(this.id),get_delear_customer_price(this.id,"change")" class="form-control five-digits blurRate" maxlength="13"  autocomplete="off" style="width:130px;text-align:right;" onkeyup="dataCal(this.id)" onfocusout="dataDec(this,"5")" value="'.$row_data['RATEPUOM'].'" /></td>';
                                        
                    echo '<td><input  type="text" name="DISCPER_'.$index.'" id="DISCPER_'.$index.'" class="form-control four-digits" maxlength="8"  autocomplete="off" style="width:130px;text-align:right;" onkeyup="dataCal(this.id)" onfocusout="dataDec(this,"2")"  value="'.$row_data['DISCPER'].'"/></td>';
                    echo '<td><input  type="text" name="DISCOUNT_AMT_'.$index.'" id="DISCOUNT_AMT_'.$index.'" class="form-control two-digits" maxlength="15"  autocomplete="off"  style="width:130px;text-align:right;" onkeyup="dataCal(this.id)" onfocusout="dataDec(this,"2")" value="'.$row_data['DISCOUNT_AMT'].'" /></td>';
                  echo '<td><input type="text" name="DISAFTT_AMT_'.$index.'" id="DISAFTT_AMT_'.$index.'" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly style="width:130px;text-align:right;" value="'.$row_data['DISAFTT_AMT'].'"
  /></td>';
                  echo '<td><input type="text" name="IGST_'.$index.'" id="IGST_'.$index.'" class="form-control four-digits" maxlength="8"  autocomplete="off" style="width:130px;text-align:right;"  readonly onkeyup="dataCal(this.id)" value="'.$row_data['IGST'].'"/></td>';
                  echo '<td><input type="text" name="IGSTAMT_'.$index.'" id="IGSTAMT_'.$index.'" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly style="width:130px;text-align:right;" value="'.$row_data['IGSTAMT'].'"
  /></td>';
                  echo '<td><input type="text" name="CGST_'.$index.'" id="CGST_'.$index.'" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly style="width:130px;text-align:right;"
                  onkeyup="dataCal(this.id)" value="'.$row_data['CGST'].'"/></td>';
                  echo '<td><input type="text" name="CGSTAMT_'.$index.'" id="CGSTAMT_'.$index.'" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly style="width:130px;text-align:right;" value="'.$row_data['CGSTAMT'].'"
  /></td>';
                  echo '<td><input type="text" name="SGST_'.$index.'" id="SGST_'.$index.'" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly  style="width:130px;text-align:right;"
                  onkeyup="dataCal(this.id)" value="'.$row_data['SGST'].'" /></td>';
                  echo '<td><input type="text" name="SGSTAMT_'.$index.'" id="SGSTAMT_'.$index.'" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly style="width:130px;text-align:right;" value="'.$row_data['SGSTAMT'].'"
  /></td>';
                  echo '<td><input type="text" name="TGST_AMT_'.$index.'" id="TGST_AMT_'.$index.'" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly style="width:130px;text-align:right;" value="'.$row_data['TGST_AMT'].'"
  /></td>';
                  echo '<td><input type="text" name="TOT_AMT_'.$index.'" id="TOT_AMT_'.$index.'" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly style="width:130px;text-align:right;"value="'.$row_data['TOT_AMT'].'"
  /></td>';
                  echo '<td align="center"  >
                  <div style="width: 84px;"><button     class="btn add material" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                  <button class="btn remove dmaterial" title="Delete" id="remove_'.$index.'" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>  </div></td>';
                
                    
                }
                 echo '</tr>
                                          
                                              </tbody>
                                              <tr  class="participantRowFotter">
                                                  <td colspan="6" style="text-align:center;font-weight:bold;">TOTAL</td>    
                                                  <td '.$AlpsStatus['hidden'].'></td>
                                                  <td '.$AlpsStatus['hidden'].'></td>
                                                  <td '.$AlpsStatus['hidden'].'></td>                               
                                                  <td></td>
                                                  <td></td>
                                                  <td></td>
                                                  <td></td>
                                                  <td></td>
                                                  <td></td>
                                                  <td id="SO_QTY_total"   style="text-align:right;font-weight:bold;"></td>
                                                  <td></td>
                                                  <td id="ALT_UOMID_QTY_total"   style="text-align:right;font-weight:bold;"></td>
                                                  <td id="RATEPUOM_total"       style="text-align:right;font-weight:bold;"></td>
                                                  <td></td>
                                                  <td id="DISCOUNT_AMT_total"   style="text-align:right;font-weight:bold;"></td>
                                                  <td id="DISAFTT_AMT_total"    style="text-align:right;font-weight:bold;"></td>
                                                  <td></td>
                                                  <td id="IGSTAMT_total"        style="text-align:right;font-weight:bold;"></td>
                                                  <td></td>
                                                  <td id="CGSTAMT_total"        style="text-align:right;font-weight:bold;"></td>
                                                  <td></td>
                                                  <td id="SGSTAMT_total"        style="text-align:right;font-weight:bold;"></td>
                                                  <td id="TGST_AMT_total"       style="text-align:right;font-weight:bold;"></td>
                                                  <td id="TOT_AMT_total"        style="text-align:right;font-weight:bold;"></td>
                                                  <td></td>                                                
                                            </tr>';
                
        echo '</tbody>';
        echo'</table>';
    }
    else{
        echo $request['hdnmaterial_Scheme'];
    }

    
    exit();
}









public function getItemPrice($ITEMID_REF,$SODT){
    $CYID_REF           =   Auth::user()->CYID_REF;
    $BRID_REF           =   Session::get('BRID_REF');
    $FYID_REF           =   Session::get('FYID_REF');
    $ObjData1   =   DB::select("SELECT TOP 1 M.CUSTOMER_PRICE,M.DEALER_PRICE,M.MRP,M.MSP, H.INDATE FROM TBL_MST_PRICELIST_MAT M   
                    LEFT JOIN TBL_MST_PRICELIST_HDR H ON H.PLID=M.PLID_REF
                    WHERE '$SODT' BETWEEN H.PERIOD_FRDT AND H.PERIOD_TODT
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
                        $COMMISSION =  $CPRICE >= $DPRICE?($CPRICE-$DPRICE):0; 
                    }
                    else if($TYPE == 'DEALER'){
                        $PRICE      =   $DPRICE;
                        $COMMISSION =   0;
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


public function getTechnicalSpecification(Request $request){

    $CYID_REF       =   Auth::user()->CYID_REF;
    $BRID_REF       =   Session::get('BRID_REF');
    $FYID_REF       =   Session::get('FYID_REF');
    $ITEMID_REF     =   $request['ITEMID_REF'];
    $TSID_REF       =   $request['TSID_REF'] !=''?explode(',',$request['TSID_REF']):[];
    $STORE_QTYS       =   $request['STORE_QTYS'] !=''?explode(',',$request['STORE_QTYS']):[];
    $CHECK_STATUS   =   'checked';

    $query_data =   DB::SELECT("SELECT * FROM TBL_MST_TECH_SPECIFICATION WHERE ITEMID_REF='$ITEMID_REF'");

    $data_array=[];
    if(isset($query_data) && !empty($query_data)){
        foreach($query_data as $key=>$data){

            if(!empty($TSID_REF)){
                $CHECK_STATUS   =   in_array($data->TSID,$TSID_REF)?'checked':'';
            }

            $data_array[]=array(
                'TSID'=>$data->TSID,
                'ITEMID_REF'=>$data->ITEMID_REF,
                'TSTYPE'=>$data->TSTYPE,
                'VALUE'=>$data->VALUE,
                'CHECK_STATUS'=>$CHECK_STATUS,
            );
        }
    }

    return Response::json($data_array);
}



public function getItemwiseStoreDetails(Request $request){

    $CYID_REF       =   Auth::user()->CYID_REF;
    $BRID_REF       =   Session::get('BRID_REF');
    $FYID_REF       =   Session::get('FYID_REF');
    $Status         =   'A';
    $ITEMID_REF     =   $request['ITEMID_REF'];
    $MAINUOMID_REF  =   $request['MAINUOMID_REF'];
    $ALTUOMID_REF   =   $request['ALTUOMID_REF'];
    $RawID          =   $request['RawID'];
    $BATCHID_REF    =   $request['BATCHID_REF'] !=''?explode(',',$request['BATCHID_REF']):[];
    $STORE_QTYS     =   $request['STORE_QTYS'] !=''?explode(',',$request['STORE_QTYS']):[];
    $STORE_DATA     =   array_combine($BATCHID_REF,$STORE_QTYS);
    $ACTION_TYPE    =   $request['ACTION_TYPE'] =="VIEW"?'disabled':'';

    //if($request['ACTION_TYPE'] =="ADD"){

        $ObjData    =   DB::table("TBL_MST_BATCH")->select('*')
                        ->where('STATUS','=',$Status)                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF)
                        ->where('ITEMID_REF','=',$ITEMID_REF) 
                        ->where('UOMID_REF','=',$MAINUOMID_REF) 
                       ->where('CURRENT_QTY','>',0) 
                        ->get() ->toArray(); 
    //}
    //else{

    //     $ObjData    =   DB::table("TBL_MST_BATCH")->select('*')
    //                     ->where('STATUS','=',$Status)                    
    //                     ->where('CYID_REF','=',$CYID_REF)
    //                     ->where('BRID_REF','=',$BRID_REF)
    //                     ->where('ITEMID_REF','=',$ITEMID_REF) 
    //                     ->where('UOMID_REF','=',$MAINUOMID_REF) 
    //                     ->get() ->toArray(); 
    // }


    if(!empty($ObjData)){

        $row1 = '';
        $row2 = '<input type="hidden" id="RawID" name="RawID" value="'.$RawID.'" />';
        $row3 = '';

    foreach ($ObjData as $dindex=>$dRow){

        $ObjStore = DB::select('SELECT TOP 1 * FROM TBL_MST_STORE WHERE STATUS = ?
                    AND STID = ?',[$Status,$dRow->STID_REF]);
        
        $ObjMUOM = DB::select('SELECT TOP 1 * FROM TBL_MST_UOM WHERE STATUS = ?
                    AND UOMID = ?',[$Status,$dRow->UOMID_REF]);

        $ObjAUOM = DB::select('SELECT TOP 1 * FROM TBL_MST_UOM WHERE STATUS = ?
                    AND UOMID = ?',[$Status,$ALTUOMID_REF]);

        $ObjConv = DB::select('SELECT TOP 1 * FROM TBL_MST_ITEM_UOMCONV WHERE ITEMID_REF = ?
                    AND TO_UOMID_REF = ?',[$ITEMID_REF,$ALTUOMID_REF]);

         $qtyvalue   =   array_key_exists($dRow->BATCHID, $STORE_DATA)?$STORE_DATA[$dRow->BATCHID]:'';
        

         $mqty       =   $ObjConv[0]->FROM_QTY;
         $aqty       =   $ObjConv[0]->TO_QTY;
         $daltqty       =  $qtyvalue;

        if($qtyvalue !=""){
            $daltqty    =   ($qtyvalue * $aqty)/$mqty;
        }
        else{
            $daltqty    =   "";
        }

        if($request['ACTION_TYPE'] =="ADD"){

            $CURRENT_QTY=$dRow->CURRENT_QTY;
        }
        else{
            $CURRENT_QTY=(floatval($dRow->CURRENT_QTY)+floatval($qtyvalue));
        }

        
        
        $row = '';
        $row = $row.' <tr class="clsstrid">';
        $row = $row.'<td hidden><input type="text" name= "STORE_NAME_'.$dindex.'" id= "STORE_NAME_'.$dindex.'" class="form-control" value="'.$ObjStore[0]->NAME.'" /></td>';
        $row = $row.'<td hidden><input type="text" name= "strITEMID_REF_'.$dindex.'" id= "strITEMID_REF_'.$dindex.'" class="form-control" value="'.$ITEMID_REF.'" /></td>';
        $row = $row.'<td><input '.$ACTION_TYPE.' type="text" name= "strBATCHNO_'.$dindex.'" id= "strBATCHNO_'.$dindex.'" class="form-control" value="'.$dRow->BATCH_CODE.'" readonly /></td>';
        $row = $row.'<td hidden><input type="text" name= "strBATCHID_'.$dindex.'" id= "strBATCHID_'.$dindex.'" class="form-control" value="'.$dRow->BATCHID.'"  /></td>';
        $row = $row.'<td><input '.$ACTION_TYPE.' type="text" name= "STORE_REF_'.$dindex.'" id= "STORE_REF_'.$dindex.'" class="form-control" value="'.$ObjStore[0]->NAME.'" readonly /></td>';
           
        $row = $row.'<td hidden><input type="text" name= "strSTID_REF_'.$dindex.'" id= "strSTID_REF_'.$dindex.'" class="form-control" value="'.$dRow->STID_REF.'" /></td>';
        $row = $row.'<td hidden><input type="text" name= "MUOM_REF_'.$dindex.'" id= "MUOM_REF_'.$dindex.'" class="form-control" value="'.$dRow->UOMID_REF.'" /></td>';
        $row = $row.'<td><input '.$ACTION_TYPE.' type="text" name= "strMAINUOMID_REF_'.$dindex.'" id= "strMAINUOMID_REF_'.$dindex.'" value="'.$ObjMUOM[0]->UOMCODE.'-'.$ObjMUOM[0]->DESCRIPTIONS.'" class="form-control" readonly /></td>';
        $row = $row.'<td><input '.$ACTION_TYPE.' type="text" name= "strSOTCK_'.$dindex.'" id= "strSOTCK_'.$dindex.'" class="form-control three-digits" value="'.$CURRENT_QTY.'" style="text-align:right;" readonly /></td>';
        $row = $row.'<td hidden><input '.$ACTION_TYPE.' type="text" name= "strDISPATCH_MAIN_QTY_'.$dindex.'" style="text-align:right;" id= "strDISPATCH_MAIN_QTY_'.$dindex.'"  class="form-control three-digits" maxlength="13" onkeypress="return isNumberDecimalKey(event,this)" autocomplete="off"  value="'.$qtyvalue.'"  /></td>';
        $row = $row.'<td hidden><input '.$ACTION_TYPE.' type="text" name= "strRATE_'.$dindex.'" style="text-align:right;" id= "strRATE_'.$dindex.'" class="form-control three-digits" value="'.$dRow->RATE.'" readonly /></td>';
        $row = $row.'<td hidden><input type="text" name= "CONV_MAIN_QTY_'.$dindex.'" id= "CONV_MAIN_QTY_'.$dindex.'" class="form-control three-digits" value="'.$ObjConv[0]->FROM_QTY.'"  maxlength="13"   /></td>';
        $row = $row.'<td hidden><input type="text" name= "CONV_ALT_QTY_'.$dindex.'" id= "CONV_ALT_QTY_'.$dindex.'" class="form-control three-digits" value="'.$ObjConv[0]->TO_QTY.'"  maxlength="13"   /></td>';
        $row = $row.'<td hidden><input type="text" name= "AUOM_REF_'.$dindex.'" id= "AUOM_REF_'.$dindex.'" class="form-control" value="'.$ALTUOMID_REF.'" /></td>';
        $row = $row.'<td hidden><input '.$ACTION_TYPE.' type="text" name= "strALTUOMID_REF_'.$dindex.'" style="text-align:right;" id= "strALTUOMID_REF_'.$dindex.'" value="'.$ObjAUOM[0]->UOMCODE.'-'.$ObjAUOM[0]->DESCRIPTIONS.'" class="form-control" readonly /></td>';
        $row = $row.'<td hidden><input '.$ACTION_TYPE.' style="text-align:right;" type="text" name= "DISPATCH_ALT_QTY_'.$dindex.'" id= "DISPATCH_ALT_QTY_'.$dindex.'" class="form-control three-digits"  value="'.$daltqty.'" readonly /></td>';
        $row = $row.'</tr>';
        $row1 = $row1.$row;
        
    }

    $row3 = $row1.$row2;
    echo $row3;



    }else{
        echo '<tr><td colspan="7">Record not found.</td></tr>';
    }
    exit();

}




public function getaltqty(Request $request){

    $Status = "A";
    $itemid = $request['itemid'];
    $auomid = $request['auomid'];

    $ObjData =  DB::table('TBL_MST_ITEM_UOMCONV')
    ->where('TBL_MST_ITEM_UOMCONV.ITEMID_REF','=',$itemid)
    ->where('TBL_MST_ITEM_UOMCONV.TO_UOMID_REF','=',$auomid)
    ->select('TBL_MST_ITEM_UOMCONV.*')
    ->first();
       
        if(!empty($ObjData)){
            echo $ObjData->TO_QTY ;
        }else{
            echo '0';
        }
        exit();

    }


}
