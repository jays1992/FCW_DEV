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

class TrnFrm45Controller extends Controller
{
    protected $form_id = 45;
    protected $vtid_ref   = 45;
    protected $view     = "transactions.sales.SalesReturn.trnfrm";

    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];
   
    public function __construct()
    {
        $this->middleware('auth');
    }

    

    public function index(){  
        
        $objRights  =   DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
		->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
		->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();
  
        $FormId         =   $this->form_id;

        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');    

        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList	=	DB::select("select hdr.SRID,hdr.SRNO,hdr.SRDT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.SRID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                            ) AS CREATED_BY,
                            hdr.STATUS, sl.SLNAME,hdr.REASONOFSR,
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
                            inner join TBL_TRN_SLSR01_HDR hdr
                            on a.VID = hdr.SRID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_SUBLEDGER sl ON hdr.SLID_REF = sl.SGLID 
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.SRID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );

 

        return view($this->view.$FormId,compact(['REQUEST_DATA','objRights','objDataList','FormId']));
    }

    public function ViewReport($request) {
        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);

    if($myValue['Flag'] == 'H')
    {    
        $SRID        =   $myValue['SRNO'];
        $Flag        =   $myValue['Flag'];
    }
    else
    {
        $SRID        =   Session::get('SRID');
        $Flag        =   $myValue['Flag'];
    }

    $objSalesReturn = DB::table('TBL_TRN_SLSR01_HDR')
        ->where('TBL_TRN_SLSR01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLSR01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        ->where('TBL_TRN_SLSR01_HDR.SRID','=',$SRID)
        ->select('TBL_TRN_SLSR01_HDR.*')
        ->first();

       
        $ssrs = new \SSRS\Report('http://103.139.58.23:8181//ReportServer/', array('username' => 'Administrator', 'password' => 'VRt+wDPuDYLwxxC'));
        $result = $ssrs->loadReport('/ERP/SalesReturnPrint');
        
        $reportParameters = array(
            'SRNO' => $objSalesReturn->SRNO,
        );
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
        ->setExecutionParameters($parameters);
        if($Flag == 'H')
        {
            Session::put('SRID', $SRID);
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
         
     } 

    public function add(){       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_SLSR01_HDR',
            'HDR_ID'=>'SRID',
            'HDR_DOC_NO'=>'SRNO',
            'HDR_DOC_DT'=>'SRDT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        
        

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
        
        $ObjUnionUDF = DB::table("TBL_MST_UDF_SRETURN")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDF_SRID')->from('TBL_MST_UDF_SRETURN')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                     
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                
                   

        $objUdfSOData = DB::table('TBL_MST_UDF_SRETURN')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdfSOData);
    

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

        $ObjSalesQuotationData = DB::table("TBL_TRN_SLQT01_HDR")->select('*')
                    ->whereNotIn('SQID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('SQID_REF')->from('TBL_TRN_SLQT02_HDR')
                                                ->where('STATUS','=','A')
                                                ->where('CYID_REF','=',$CYID_REF)
                                                ->where('BRID_REF','=',$BRID_REF)
                                                ->where('FYID_REF','=',$FYID_REF);                       
                    })->where('STATUS','=','A')                    
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF)
                    ->where('FYID_REF','=',$FYID_REF) ->get() ->toArray();                    
                   

        $objSalesQuotationAData = DB::table('TBL_TRN_SLQT02_HDR')->select('*')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('FYID_REF','=',$FYID_REF)
            ->get() ->toArray();  
      
            

    $FormId = $this->form_id;

    $AlpsStatus =   $this->AlpsStatus();

    $InputStatus=   "disabled";

    $lastdt=$this->LastApprovedDocDate(); 

    $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
    $objothcurrency = $this->GetCurrencyMaster(); 

       
    return view('transactions.sales.SalesReturn.trnfrm45add',
    compact(['FormId','objCalculationHeader','objUdfSOData','objTNCHeader','objothcurrency',
    'objSalesPerson','objSalesQuotationAData','ObjSalesQuotationData','objCountUDF',
    'AlpsStatus','InputStatus','lastdt','TabSetting','doc_req','docarray']));       
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
                        $row3 =    '<td  style="text-align:center;" ><input type="checkbox" class="filter-none" name="calACTUAL_'.$dindex.'" id="calACTUAL_'.$dindex.'" checked ></td>';
                    }
                    else{
                        $row3 =    '<td  style="text-align:center;" ><input type="checkbox" class="filter-none" name="calACTUAL_'.$dindex.'" id="calACTUAL_'.$dindex.'"  ></td>';
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
    
        $sp_popup = [
            $CYID_REF, $BRID_REF,$CODE,$NAME
        ]; 
        
            $ObjData = DB::select('EXEC sp_get_customer_popup_enquiry ?,?,?,?', $sp_popup);
    
            if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){
            
                $row = '';
                $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_SLID_REF[]" id="subgl_'.$index.'" class="clssubgl" value="'.$dataRow-> SGLID.'" ></td>';
                $row = $row.'<td class="ROW2">'.$dataRow->SGLCODE;
                $row = $row.'<input type="hidden" id="txtsubgl_'.$index.'" data-desc="'.$dataRow->SGLCODE .' - ';
                $row = $row.$dataRow->SLNAME. '" data-desc2="'.$dataRow->GLID_REF. '"value="'.$dataRow->SGLID.'"/></td><td class="ROW3">'.$dataRow->SLNAME.'</td></tr>';
    
    
                echo $row;
            }
    
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    
    }

    public function getCodeNo(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $SLID_REF       =   $request['id'];
        $BILLTO_REF     =   $request['BILLTO_REF'];
        $SHIPTO_REF     =   $request['SHIPTO_REF'];
        $fieldid    = $request['fieldid'];

        $ObjData =  DB::select("SELECT SIID,SINO,SIDT FROM TBL_TRN_SLSI01_HDR 
        WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF' 
        AND SLID_REF='$SLID_REF' AND BILLTO='$BILLTO_REF' AND SHIPTO='$SHIPTO_REF' AND STATUS='A'
		AND SIID  IN (SELECT A.SIID_REF FROM TBL_TRN_SLSI01_MAT A(NOLOCK)           
		GROUP BY A.SIID_REF HAVING SUM(A.CURRENT_QTY) > 0      
		) ");

        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){
                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="sqcode_'.$dataRow->SIID .'"  class="clssqid" value="'.$dataRow->SIID.'" ></td>
                <td class="ROW2">'.$dataRow->SINO;
                $row = $row.'<input type="hidden" id="txtsqcode_'.$dataRow->SIID.'" data-desc="'.$dataRow->SINO.'"  data-descdate="'.$dataRow->SIDT.'"
                value="'.$dataRow->SIID.'"/></td>
                <td class="ROW3">'.$dataRow->SIDT.'</td></tr>';
                echo $row;
            }
        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
    }

    public function getItemList(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $taxstate = $request['taxstate'];
        $CodeNoId = $request['id'];
        $StdCost = 0;

        $AlpsStatus =   $this->AlpsStatus();

        $ObjItem =  DB::select("SELECT * FROM TBL_MST_ITEM T1
        INNER JOIN TBL_TRN_SLSI01_MAT T2 ON T1.ITEMID=T2.ITEMID_REF
        WHERE T1.CYID_REF = '$CYID_REF' 
        AND ( T1.DEACTIVATED IS NULL OR T1.DEACTIVATED = 0 ) AND T1.STATUS ='$Status' AND T2.SIID_REF='$CodeNoId'");

        
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
                  
                    $FROMQTY  =   isset($dataRow->CURRENT_QTY)?$dataRow->CURRENT_QTY:0;

                


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

                                
                        $desc6  =   $CodeNoId.'-'.$dataRow->ITEMID.'-'.$dataRow->SEID_REF.'-'.$dataRow->SQID_REF.'-'.$dataRow->SOID.'-'.$dataRow->SCID_REF;
                     
                        $row = '';
                        if($taxstate != "OutofState"){
                            
                        $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                        $row = $row.'<input type="hidden" id="uniquerowid_'.$desc6.'"  data-desc6="'.$desc6.'" />';
                        $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'" data-desc1="'.$FROMQTY.'" data-desc2="'.$dataRow->RATEPUOM.'" data-desc3="'.$dataRow->DISPER.'" data-desc4="'.$dataRow->DISCOUNT_AMT.'" data-desc5="'.$CodeNoId.'-'.$dataRow->ITEMID.'"   value="'.$dataRow->ITEMID.'"/></td> <td style="width:10%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'" value="'.$dataRow->NAME.'"/></td>';
                        $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'" data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'"  value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td style="width:8%;" id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'" value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                        $row = $row.'<td style="width:8%;" id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->RATEPUOM.'" value="'.$dataRow->RATEPUOM.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                        $row = $row.'<td style="width:8%;" id="itax_'.$dataRow->ITEMID.'"><input type="hidden" id="txtitax_'.$dataRow->ITEMID.'" data-desc="'.$Taxid[0].'" value="'.$Taxid[1].'"/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td> 
                        
                        <td style="width:8%;">'.$BusinessUnit.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                        <td style="width:8%;">Authorized</td></tr>';
                        }
                        else
                        {
                            $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                            $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                            $row = $row.'<input type="hidden" id="uniquerowid_'.$desc6.'"  data-desc6="'.$desc6.'" />';
                            $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'" data-desc1="'.$FROMQTY.'" data-desc2="'.$dataRow->RATEPUOM.'" data-desc3="'.$dataRow->DISPER.'" data-desc4="'.$dataRow->DISCOUNT_AMT.'" data-desc5="'.$CodeNoId.'-'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'"/></td> <td style="width:10%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'" value="'.$dataRow->NAME.'"/></td>';
                            $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'" data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'"  value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                            $row = $row.'<td style="width:8%;" id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'" value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                            $row = $row.'<td style="width:8%;" id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="1" value="'.$dataRow->RATEPUOM.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                            $row = $row.'<td style="width:8%;" id="itax_'.$dataRow->ITEMID.'"><input type="hidden" id="txtitax_'.$dataRow->ITEMID.'" data-desc="'.$Taxid[0].'" value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                            
                            <td style="width:8%;">'.$BusinessUnit.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                            <td style="width:8%;">Authorized</td></tr>';   
                        }

                        echo $row; 
                        
                        





                    } 
                    
                }           
                else{
                 echo '<tr><td> Record not found.</td></tr>';
                }
        exit();
    }


    

    public function getItemDetailswithoutQuotation(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $taxstate = $request['taxstate'];
        $StdCost = 0;
        
                
        $ObjItem =  DB::select('SELECT * FROM TBL_MST_ITEM  
                    WHERE CYID_REF = ?  
                    AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                    [$CYID_REF,  $Status ]);
    
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
                    
                        
                        $row = '';
                        if($taxstate != "OutofState"){
                        $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:6.5%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td>'.$dataRow->ICODE;
                        $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'"
                        value="'.$dataRow->ITEMID.'"/></td>
                        <td id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'"
                        value="'.$dataRow->NAME.'"/></td>';
                        $row = $row.'<td id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                        value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'"
                        value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                        $row = $row.'<td id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="'.$FROMQTY.'"
                        value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                        $row = $row.'<td id="itax_'.$dataRow->ITEMID.'"><input type="hidden" id="txtitax_'.$dataRow->ITEMID.'" data-desc="'.$Taxid[0].'"
                        value="'.$Taxid[1].'"/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                        <td>Authorized</td>
                        </tr>';
                        }
                        else
                        {
                            $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:6.5%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                            $row = $row.'<td>'.$dataRow->ICODE;
                            $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'"
                            value="'.$dataRow->ITEMID.'"/></td>
                            <td id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'"
                            value="'.$dataRow->NAME.'"/></td>';
                            $row = $row.'<td id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                            value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                            $row = $row.'<td id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'"
                            value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                            $row = $row.'<td id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="'.$FROMQTY.'"
                            value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                            $row = $row.'<td id="itax_'.$dataRow->ITEMID.'"><input type="hidden" id="txtitax_'.$dataRow->ITEMID.'" data-desc="'.$Taxid[0].'"
                            value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                            <td>Authorized</td>
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

        $ObjData =  DB::select('SELECT TO_UOMID_REF FROM TBL_MST_ITEM_UOMCONV  
                WHERE ITEMID_REF= ?  order by IUCID ASC', [$id]);

        if(!empty($ObjData)){

        foreach ($ObjData as $index=>$dataRow){

            $ObjAltUOM =  DB::select('SELECT top 1 UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                WHERE UOMID= ?  ', [$dataRow->TO_UOMID_REF]);
        
            $row = '';
            $row = $row.'<tr id="altuom_'.$dataRow->TO_UOMID_REF .'"  class="clsaltuom"><td width="50%">'.$ObjAltUOM[0]->UOMCODE;
            $row = $row.'<input type="hidden" id="txtaltuom_'.$dataRow->TO_UOMID_REF.'" data-desc="'.$ObjAltUOM[0]->UOMCODE .' - ';
            $row = $row.$ObjAltUOM[0]->DESCRIPTIONS. '" value="'.$dataRow->TO_UOMID_REF.'"/></td><td>'.$ObjAltUOM[0]->DESCRIPTIONS.'</td></tr>';

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
        
        $ObjCust =  DB::select('SELECT top 1 CID FROM TBL_MST_CUSTOMER  
                    WHERE STATUS= ? AND SLID_REF = ? ', [$Status,$id]);
    
        $cid = $ObjCust[0]->CID;
        $ObjBillTo =  DB::select('SELECT top 1 * FROM TBL_MST_CUSTOMERLOCATION  
                    WHERE DEFAULT_BILLTO= ? AND CID_REF = ? ', [1,$cid]);

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
            

            $ObjCust =  DB::select('SELECT top 1 CID FROM TBL_MST_CUSTOMER  
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
                    if(!empty($ObjSHIPTO)){
                        
                    $objAddress = $ObjSHIPTO[0]->CADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                    
                    $row = '';
                    $row = $row.'<input type="text" name="txtSHIPTO" id="txtSHIPTO" class="form-control"  autocomplete="off" value="'. $objAddress.'" readonly/>';
                    $row = $row.'<input type="hidden" name="SHIPTO" id="SHIPTO" class="form-control" autocomplete="off" value="'. $ObjAddressID.'" readonly/>';
                    $row = $row.'<input type="hidden" name="Tax_State" id="Tax_State" class="form-control" autocomplete="off" value="'. $TAXSTATE.'" readonly/>';
                    
                    echo $row;
                    }else{
                        echo '';
                    }
                    exit();
        
            }

            public function getBillAddress(Request $request){
                $Status = "A";
                $id = $request['id'];
                if(!is_null($id))
                {
                $ObjCust =  DB::select('SELECT top 1 CID FROM TBL_MST_CUSTOMER  
                        WHERE STATUS= ? AND SLID_REF = ? ', [$Status,$id]);
        
                $cid = $ObjCust[0]->CID;
                $ObjBillTo =  DB::select('SELECT  * FROM TBL_MST_CUSTOMERLOCATION  
                            WHERE BILLTO= ? AND CID_REF = ? ', [1,$cid]);
            
                    if(!empty($ObjBillTo)){
            
                    foreach ($ObjBillTo as $index=>$dataRow){
    
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
                        <td class="ROW1"> <input type="checkbox" name="SELECT_BILLTO[]" id="billto_'.$dataRow->CLID .'"  class="clsbillto" value="'.$dataRow->CLID.'" ></td>
                        <td class="ROW2">'.$dataRow->NAME;
                        $row = $row.'<input type="hidden" id="txtbillto_'.$dataRow->CLID.'" data-desc="'.$objAddress.'" 
                        value="'.$dataRow->CLID.'"/></td>
                        <td class="ROW3" >'.$objAddress.'</td></tr>';
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
                $ObjCust =  DB::select('SELECT top 1 CID FROM TBL_MST_CUSTOMER  
                        WHERE STATUS= ? AND SLID_REF = ? ', [$Status,$id]);
        
                $cid = $ObjCust[0]->CID;
                $ObjShipTo =  DB::select('SELECT  * FROM TBL_MST_CUSTOMERLOCATION  
                            WHERE SHIPTO= ? AND CID_REF = ? ', [1,$cid]);
            
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
                        <td class="ROW1"> <input type="checkbox" name="SELECT_SHIPTO[]" id="shipto_'.$dataRow->CLID .'"  class="clsshipto" value="'.$dataRow->CLID.'" ></td>
                        <td class="ROW2">'.$dataRow->NAME;
                        $row = $row.'<input type="hidden" id="txtshipto_'.$dataRow->CLID.'" data-desc="'.$TAXSTATE.'" 
                        value="'.$dataRow->CLID.'"/></td>
                        <td class="ROW3" id="txtshipadd_'.$dataRow->CLID.'" >'.$objAddress.'</td></tr>';
                        echo $row;
                    }
            
                    }else{
                        echo '<tr><td colspan="2">Record not found.</td></tr>';
                    }

                    exit();
                }
            }


            

    
   public function save(Request $request) {
    
        $r_count1 = $request['Row_Count1'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];

        $GROSS_TOTAL    =   0; 
        $NET_TOTAL 		= $request['TotalValue'];
        $CGSTAMT        =   0; 
        $SGSTAMT        =   0; 
        $IGSTAMT        =   0; 
        $DISCOUNT       =   0; 
        $OTHER_CHARGES  =   0; 
       
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i]))
            {
                $GROSS_TOTAL+= $request['DISAFTT_AMT_'.$i]; 
                $CGSTAMT+= $request['CGSTAMT_'.$i]; 
                $SGSTAMT+= $request['SGSTAMT_'.$i]; 
                $IGSTAMT+= $request['IGSTAMT_'.$i]; 
                $DISCOUNT+= $request['DISCOUNT_AMT_'.$i]; 


                $StoreArr   =   array();
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];

                if($ITEMROWID !=""){

                    $exp        =   explode(",",$ITEMROWID);

                    foreach($exp as $val){
                        $keyid              =   explode("_",$val);
                        $batchid            =   $keyid[0];

                        $objStore1 =  DB::table('TBL_TRN_SLSC01_STORE')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('STORE_MATID','=',$batchid)
                        ->select('BATCHID_REF')
                        ->first();
						
						$objStore =  DB::table('TBL_MST_BATCH')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('BATCHID','=',$objStore1->BATCHID_REF)
                        ->select('STID_REF')
                        ->first();

                        $StoreArr[]=$objStore->STID_REF;
                    }
                }

                if(!empty($StoreArr)){
                    $StoreId    =   array_unique($StoreArr);
                    $STID_REF   =   implode(",",$StoreId);
                }
                else{
                    $STID_REF   =   NULL;
                }

                $WhereId    =   $request['exist_'.$i];
                $Field_Id   =   explode("-",$WhereId);
                $SEID_REF   =   intval($Field_Id[2]);
                $SQID_REF   =   intval($Field_Id[3]);
                $SO         =   intval($Field_Id[4]);
                $SCID_REF   =   intval($Field_Id[5]);

                $req_data[$i] = [
                    'SIID_REF' => $request['SQA_'.$i] ,
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'ITEMDESC'    => $request['ItemName_'.$i],
                    'MAIN_SRUOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'SRQTY' => $request['SO_QTY_'.$i],
                    'ALT_SRUOMID_REF' => $request['ALT_UOMID_REF_'.$i],
                    'STID_REF' => $STID_REF,
                    'SRRATE' => $request['RATEPUOM_'.$i],
                    'SRAMT' => $request['DISAFTT_AMT_'.$i],
                    'GST' =>  (isset($request['flagtype_'.$i])!="true" ? 0 : 1) ,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'BATCH_QTY_REF'   => $request['HiddenRowId_'.$i],
                    'SEID_REF'     => $SEID_REF,
                    'SQID_REF'     => $SQID_REF,
                    'SOID_REF'     => $SO,
                    'SCID_REF'     => $SCID_REF,
                ];
            }
        }


        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        $reqdata3=array();
        for ($i=0; $i<=$r_count3; $i++)
        {
                if(isset($request['UDFSOID_REF_'.$i]) && !is_null($request['UDFSOID_REF_'.$i]))
                {
                    $reqdata3[$i] = [
                        'UDF_SRID_REF'   => $request['UDFSOID_REF_'.$i],
                        'VALUE'      => $request['udfvalue_'.$i],
                    ];
                }
            
        }

        if(!empty($reqdata3))
        { 
            $wrapped_links3["UDF"] = $reqdata3; 
            $XMLUDF = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $XMLUDF = NULL; 
        }

        $reqdata4=array();
        for ($i=0; $i<=$r_count4; $i++)
        {
                if(isset($request['CTID_REF']) && !is_null($request['CTID_REF']))
                {
                    if(isset($request['TID_REF_'.$i]))
                    {
                        if(isset($request['CT_TYPE_'.$i]) && $request['CT_TYPE_'.$i]=="DISCOUNT"){
                          
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

        if(!empty($reqdata4))
        { 
            $wrapped_links4["CAL"] = $reqdata4; 
            $XMLCAL = ArrayToXml::convert($wrapped_links4);
        }
        else
        {
            $XMLCAL = NULL; 
        }

        
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $dataArr    =   array();
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];

                if($ITEMROWID !=""){
                    $exp        =   explode(",",$ITEMROWID);

                    foreach($exp as $val){
                        $keyid              =   explode("_",$val);
                        $batchid            =   $keyid[0];
                        $qty                =   $keyid[1];
                        $dataArr[$batchid]  =   $qty;
                    }
                }

                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        $objBatch =  DB::table('TBL_TRN_SLSC01_STORE')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('STORE_MATID','=',$key)
                        ->select('STORE_MATID','BATCHID_REF','ITEMID_REF','SERIALNO','DISPATCH_MAIN_QTY','SCID_REF')
                        ->first();
						
						$objMat =  DB::table('TBL_TRN_SLSC01_MAT')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('SRNO','=',$objBatch->SERIALNO)
						->where('SCID_REF','=',$objBatch->SCID_REF)
                        ->select('ITEMID_REF','ALTUOMID_REF')
                        ->first();
						
						$objBatch1 =  DB::table('TBL_MST_BATCH')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('BATCHID','=',$objBatch->BATCHID_REF)
                        ->select('BATCH_CODE','STID_REF','UOMID_REF')
                        ->first();

                        $BATCHNO        =   $objBatch1->BATCH_CODE;
                        $SERIALNO       =   $objBatch->SERIALNO;
                        $STID_REF       =   $objBatch1->STID_REF;
                        $MAINUOMID_REF  =   $objBatch1->UOMID_REF;
                        $ALTUOMID_REF   =   $objMat->ALTUOMID_REF;
                        $BATCHNO        =   $objBatch1->BATCH_CODE;

                        $STOCK_INHAND   =   $this->getStockQty($BATCHNO,$SERIALNO,$STID_REF,$ITEMID_REF,$MAINUOMID_REF);
                        $RETURN_QTYA    =   $this->getAltUmQty($ALTUOMID_REF,$ITEMID_REF,$val);

                        $req_data33[$i][] = [
                            'SIID_REF'      => $request['SQA_'.$i] ,
                            'ITEMID_REF'    => $ITEMID_REF,
                            'BATCH_NO'      => $BATCHNO,
                            'STID_REF'      => intval($STID_REF),
                            'SERIAL_NO'     => $SERIALNO,
                            'MAIN_UOMID_REF'=> $MAINUOMID_REF,
                            'STOCK_INHAND'  => $STOCK_INHAND !=''?floatval($STOCK_INHAND):0,
                            'RETURN_QTYM'   => $val,
                            'ALT_UOMID_REF' => $ALTUOMID_REF,
                            'RETURN_QTYA'   => $RETURN_QTYA,
                        ];

                    }
                }
            }
        }


       

        $wrapped_links33["STORE"] = $req_data33; 
        $XMLSTORE = ArrayToXml::convert($wrapped_links33);
        
        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $SSI_NO     = $request['SONO'];
        $SSI_DT     = $request['SODT'];
        $GLID_REF   = $request['GLID_REF'];
        $SLID_REF   = $request['SLID_REF'];        
        $BILLTO     = $request['BILLTO'];
        $SHIPTO     = $request['SHIPTO'];
        $REASONOFSR = $request['REASONOFSR'];
        $COMMONNRT  = $request['COMMONNRT'];
        $PAYMENT    =(isset($request['chk_Purchase'])!="true" ? 0 : 1);
        $BILL_NO    = $request['BILL_NO'];
        $BILL_DT    = $request['BILL_DT'];

        $FC = (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
     

        $log_data = [ 
            $SSI_NO,$SSI_DT,$GLID_REF,$SLID_REF, $REASONOFSR,$COMMONNRT,$BILLTO,$SHIPTO,$CYID_REF, $BRID_REF,
            $FYID_REF,$VTID_REF,$XMLMAT,$XMLSTORE,$XMLUDF,$XMLCAL,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,
            $IPADDRESS,$PAYMENT,$BILL_NO,$BILL_DT,
            $FC,$CRID_REF,$CONVFACT,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES
        ];

      dd($log_data);
     
        $sp_result = DB::select('EXEC SP_SAR_IN ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?, ?,?,?,?,?', $log_data);   
        
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
        }

        exit();   
     }

     public function getsalesorder(Request $request){
        $columns = array( 
            0 =>'NO',
            1 =>'SONO',
            2 =>'SODT',
            3 =>'OVFDT',
            4 =>'OVTDT',
            5 =>'STATUS',
        );  
        

        $COL_APP_STATUS =   'STATUS';  
      
            $USERID_REF    =   Auth::user()->USERID;
            $CYID_REF      =   Auth::user()->CYID_REF;
            $BRID_REF      =   Session::get('BRID_REF');
            $FYID_REF      =   Session::get('FYID_REF');       
            $TABLE1        =   "TBL_TRN_SLSO01_HDR";
            $PK_COL        =   "SOID";
            $SELECT_COL    =   "SOID,SONO,SODT,OVFDT,OVTDT";    
            $WHERE_COL     =   " ";
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

          

            if(!empty($request->input('search.value')))
            {

                $search_text = $request->input('search.value'); 
                $filtercolumn = $request->input('filtercolumn');

                $search_text = "'". $search_text ."'";
              
                if($filtercolumn =='ALL'){

                    $WHERE_COL =  " WHERE SOID LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR SONO LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR SODT LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR OVFDT LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR OVTDT LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR ".$COL_APP_STATUS." LIKE  ". $search_text;


                }else{

                    $WHERE_COL =  " WHERE ".$filtercolumn." LIKE ". $search_text;

                }         
                
            }
           
            $ORDER_BY_COL   =  $order. " ". $dir;
            $OFFSET_COL     =   " offset ".$start." rows fetch next ".$limit." rows only ";
           
            $sp_listing_data = [
                $USERID_REF, $CYID_REF,$BRID_REF, $FYID_REF, $TABLE1, $PK_COL,
                $SELECT_COL,$WHERE_COL, $ORDER_BY_COL, $OFFSET_COL

            ];

            
            
            $sp_listing_result = DB::select('EXEC SP_LISTINGDATA ?,?,?,?, ?,?,?,?, ?,?', $sp_listing_data);

            $totalRows = 0;      
            $totalFiltered = 0;  

            $data = array();
            
            
            if(!empty($sp_listing_result))
            {
                foreach ($sp_listing_result as $key=>$salesorderitem)
                {
                    $totalRows      = $salesorderitem->TotalRows;
                    $totalFiltered  = $salesorderitem->FilteredRows;

                    if (!Empty($salesorderitem->STATUS) && $salesorderitem->STATUS=="Approved") 
                    { $app_status = 1 ;} 
                    elseif($salesorderitem->STATUS=="Cancel")
                    { $app_status = 2 ;}
                    else{ $app_status = 0 ;}

      

                    $nestedData['NO']           = '<input type="checkbox" id="chkId'.$salesorderitem->SOID.'"  value="'.$salesorderitem->SOID.'" class="js-selectall1" data-rcdstatus="'.$app_status.'">';
                    $nestedData['SONO']         = strtoupper($salesorderitem->SONO);
                    $nestedData['SODT']     = $salesorderitem->SODT;
                    $nestedData['OVFDT']      = $salesorderitem->OVFDT;
                    $nestedData['OVTDT']         = $salesorderitem->OVTDT;
                    $nestedData['STATUS']       = $salesorderitem->STATUS;
                    $data[] = $nestedData;
                    
                    
                }

            }
         
            $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalRows),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
            );            
            echo json_encode($json_data); 

            
            exit(); 

    }

    public function edit($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){

          

            $objSO = DB::table('TBL_TRN_SLSR01_HDR')
            ->where('TBL_TRN_SLSR01_HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_SLSR01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_SLSR01_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_SLSR01_HDR.SRID','=',$id)
            ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_SLSR01_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
            ->select('TBL_TRN_SLSR01_HDR.*','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
            ->first();

   



            $objSOMAT=[];
            if(isset($objSO) && !empty($objSO)){

            $objSOMAT = DB::table('TBL_TRN_SLSR01_MAT')  
            ->leftJoin('TBL_TRN_SLSI01_HDR', 'TBL_TRN_SLSR01_MAT.SIID_REF','=','TBL_TRN_SLSI01_HDR.SIID')  
            ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_SLSR01_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
            ->leftJoin('TBL_TRN_SLSI01_MAT', function($join){
                $join->on('TBL_TRN_SLSR01_MAT.SIID_REF', '=', 'TBL_TRN_SLSI01_MAT.SIID_REF')
                     ->on('TBL_TRN_SLSR01_MAT.ITEMID_REF', '=', 'TBL_TRN_SLSI01_MAT.ITEMID_REF')
                     ->on('TBL_TRN_SLSR01_MAT.SCID_REF', '=', 'TBL_TRN_SLSI01_MAT.SCID_REF')
                     ->on('TBL_TRN_SLSR01_MAT.SOID_REF', '=', 'TBL_TRN_SLSI01_MAT.SOID');
                    
            }) 
            
            ->where('TBL_TRN_SLSR01_MAT.SRID_REF','=',$id)
            ->select(
                'TBL_TRN_SLSR01_MAT.*',
                'TBL_TRN_SLSI01_HDR.SINO',
                'TBL_TRN_SLSI01_HDR.SIDT',
                'TBL_TRN_SLSI01_HDR.SIDT',
                'TBL_MST_ITEM.ICODE',
                'TBL_MST_ITEM.NAME',
                'TBL_MST_ITEM.ALPS_PART_NO',
                'TBL_MST_ITEM.CUSTOMER_PART_NO',
                'TBL_MST_ITEM.OEM_PART_NO',
                'TBL_MST_ITEM.MAIN_UOMID_REF',
                'TBL_MST_ITEM.ALT_UOMID_REF',
                'TBL_TRN_SLSI01_MAT.SIMAIN_QTY',
                'TBL_TRN_SLSI01_MAT.SQID_REF',
                'TBL_TRN_SLSI01_MAT.SOID',
                'TBL_TRN_SLSI01_MAT.SCID_REF',
                'TBL_TRN_SLSI01_MAT.SEID_REF'
                )
            ->orderBy('TBL_TRN_SLSR01_MAT.SRMATID','ASC')
            ->get()->toArray();

            }

			
            $objCount1 = count($objSOMAT);

            
        

            $objSOTNC=array();
            $objCount2 = count($objSOTNC);

            $objSOUDF = DB::table('TBL_TRN_SLSR01_UDF')                    
            ->where('TBL_TRN_SLSR01_UDF.SRID_REF','=',$id)
            ->select('TBL_TRN_SLSR01_UDF.*')
            ->orderBy('TBL_TRN_SLSR01_UDF.SRUDFID','ASC')
            ->get()->toArray();
            $objCount3 = count($objSOUDF);

            $objSOCAL = DB::table('TBL_TRN_SLSR01_CAL')                    
            ->where('TBL_TRN_SLSR01_CAL.SRID_REF','=',$id)
            ->select('TBL_TRN_SLSR01_CAL.*')
            ->orderBy('TBL_TRN_SLSR01_CAL.SRCALID','ASC')
            ->get()->toArray();
            $objCount4 = count($objSOCAL);

          

            $objSOPSLB=array();
            $objCount5 = count($objSOPSLB);
     
                
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
                             $TAXSTATE=[];
                             $objShpAddress=[] ;
                             $objBillAddress=[];

                             if(isset($objSO->SHIP_TO) && $objSO->SHIP_TO !=""){

                             $sid = $objSO->SHIP_TO;
         
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

                            if(isset($objSO->BILL_TO) && $objSO->BILL_TO !=""){
                                        
                            $bid = $objSO->BILL_TO;
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
                           
            
            $objglcode2 =[];                  
            if(isset($objSO->GLID_REF) && $objSO->GLID_REF !=""){
            $objglcode2 = DB::table('TBL_MST_GENERALLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('GLID','=',$objSO->GLID_REF)
            ->select('TBL_MST_GENERALLEDGER.*')
            ->first();
            }

           

            $objEMP=array();
            $objSPID=NULL;

            $objglcode = DB::table('TBL_MST_GENERALLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('STATUS','=',$Status)
            ->where('SUBLEDGER','=','1')
            ->select('TBL_MST_GENERALLEDGER.*')
            ->get()
            ->toArray();

            $objsubglcode =[];
            if(isset($objSO->GLID_REF) && $objSO->GLID_REF !=""){
            $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('GLID_REF','=',$objSO->GLID_REF)
            ->where('SGLID','=',$objSO->SLID_REF)
            ->select('TBL_MST_SUBLEDGER.*')
            ->first();
            }

   

          
           

            $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
            WHERE  CYID_REF = ? AND BRID_REF = ?  
            order by CTCODE ASC', [$CYID_REF, $BRID_REF ]);
    
            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
                WHERE  CYID_REF = ? AND BRID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                order by TNC_CODE ASC', [$CYID_REF, $BRID_REF, 'A' ]);
    
            $objCalculationHeader	=   Helper::getCalculationHeader(array(
                'CYID_REF'=>Auth::user()->CYID_REF,
                'BRID_REF'=>Session::get('BRID_REF'),
                'USERID'=>Auth::user()->USERID,
                'HEADING'=>'Transactions',
                'VTID_REF'=>$this->vtid_ref,
                'FORMID'=>$this->form_id
                ));
            
            $ObjUnionUDF = DB::table("TBL_MST_UDF_SRETURN")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('UDF_SRID')->from('TBL_MST_UDF_SRETURN')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                        
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                        
                    
    
            $objUdfSOData = DB::table('TBL_MST_UDF_SRETURN')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDF_SRETURN")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDF_SRID')->from('TBL_MST_UDF_SRETURN')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                            
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                            
            $objUdfSOData2 = DB::table('TBL_MST_UDF_SRETURN')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
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
    
            $ObjSalesQuotationData = DB::table("TBL_TRN_SLSI01_HDR")->select('*')
                        ->where('STATUS','=','A')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF)
                        ->where('FYID_REF','=',$FYID_REF) ->get() ->toArray(); 
                        
            $objSalesQuotationAData=array();
                    
          

            $objItems=array();



            $objSQMAT=$objSOMAT;

           

            $objUOM = DB::table('TBL_MST_UOM')->select('*')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',$CYID_REF)
            ->get() ->toArray(); 


            $objItemUOMConv = DB::select("SELECT T1.*
                FROM TBL_MST_ITEM_UOMCONV T1
                INNER JOIN TBL_TRN_SLSR01_MAT T2 ON T1.ITEMID_REF=T2.ITEMID_REF
            ");

          
          


            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')->get() ->toArray(); 
            $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')->get() ->toArray(); 
        
           
        $FormId         =   $this->form_id;

        $AlpsStatus =   $this->AlpsStatus();
        $InputStatus=   "";
        
        $lastdt=$this->LastApprovedDocDate(); 

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

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

        $COMPANY_NAME   =   DB::table('TBL_MST_COMPANY')->where('STATUS','=','A')->where('CYID','=',Auth::user()->CYID_REF)->select('TBL_MST_COMPANY.NAME')->first()->NAME;
        
        $objCountAttachment = "1";
        if(empty($objAttachments) && strpos($COMPANY_NAME,"ALPS")!== false){
            $objCountAttachment = "0";
        }

        $objothcurrency = $this->GetCurrencyMaster(); 

        return view($this->view.$FormId.'edit',compact(['FormId','objSO','objRights','objCount1','objSPID',
           'objCount2','objCount3','objCount4','objCount5','objSOMAT','objSOCAL','objSOTNC','objSOUDF','objSOPSLB',
           'objglcode','objCalculationHeader','objUdfSOData','objTNCHeader','objothcurrency','objglcode2',
           'objSalesPerson','objSalesQuotationAData','ObjSalesQuotationData','objsubglcode',
           'objShpAddress','objBillAddress','objItems','objSQMAT','objUOM','objItemUOMConv','objTNCDetails','objUdfSOData2',
           'objCalHeader','objCalDetails','TAXSTATE','AlpsStatus','InputStatus','lastdt','TabSetting',
           'objCountAttachment']));
        }
     
       }
     
       public function view($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){

           
            $objSO = DB::table('TBL_TRN_SLSR01_HDR')
            ->where('TBL_TRN_SLSR01_HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_SLSR01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_SLSR01_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_SLSR01_HDR.SRID','=',$id)
            ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_SLSR01_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
            ->select('TBL_TRN_SLSR01_HDR.*','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
            ->first();



          

            $objSOMAT=[];
            if(isset($objSO) && !empty($objSO)){

            $objSOMAT = DB::table('TBL_TRN_SLSR01_MAT')  
            ->leftJoin('TBL_TRN_SLSI01_HDR', 'TBL_TRN_SLSR01_MAT.SIID_REF','=','TBL_TRN_SLSI01_HDR.SIID')  
            ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_SLSR01_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
            ->leftJoin('TBL_TRN_SLSI01_MAT', function($join){
                $join->on('TBL_TRN_SLSR01_MAT.SIID_REF', '=', 'TBL_TRN_SLSI01_MAT.SIID_REF')
                     ->on('TBL_TRN_SLSR01_MAT.ITEMID_REF', '=', 'TBL_TRN_SLSI01_MAT.ITEMID_REF')
                     ->on('TBL_TRN_SLSR01_MAT.SCID_REF', '=', 'TBL_TRN_SLSI01_MAT.SCID_REF')
                     ->on('TBL_TRN_SLSR01_MAT.SOID_REF', '=', 'TBL_TRN_SLSI01_MAT.SOID');
                    
            }) 
            
            ->where('TBL_TRN_SLSR01_MAT.SRID_REF','=',$id)
            ->select(
                'TBL_TRN_SLSR01_MAT.*',
                'TBL_TRN_SLSI01_HDR.SINO',
                'TBL_TRN_SLSI01_HDR.SIDT',
                'TBL_TRN_SLSI01_HDR.SIDT',
                'TBL_MST_ITEM.ICODE',
                'TBL_MST_ITEM.NAME',
                'TBL_MST_ITEM.ALPS_PART_NO',
                'TBL_MST_ITEM.CUSTOMER_PART_NO',
                'TBL_MST_ITEM.OEM_PART_NO',
                'TBL_MST_ITEM.MAIN_UOMID_REF',
                'TBL_MST_ITEM.ALT_UOMID_REF',
                'TBL_TRN_SLSI01_MAT.SIMAIN_QTY',
                'TBL_TRN_SLSI01_MAT.SQID_REF',
                'TBL_TRN_SLSI01_MAT.SOID',
                'TBL_TRN_SLSI01_MAT.SCID_REF',
                'TBL_TRN_SLSI01_MAT.SEID_REF'
                )
            ->orderBy('TBL_TRN_SLSR01_MAT.SRMATID','ASC')
            ->get()->toArray();

            }

		
            $objCount1 = count($objSOMAT);

            
          

            $objSOTNC=array();
            $objCount2 = count($objSOTNC);

            $objSOUDF = DB::table('TBL_TRN_SLSR01_UDF')                    
            ->where('TBL_TRN_SLSR01_UDF.SRID_REF','=',$id)
            ->select('TBL_TRN_SLSR01_UDF.*')
            ->orderBy('TBL_TRN_SLSR01_UDF.SRUDFID','ASC')
            ->get()->toArray();
            $objCount3 = count($objSOUDF);

            $objSOCAL = DB::table('TBL_TRN_SLSR01_CAL')                    
            ->where('TBL_TRN_SLSR01_CAL.SRID_REF','=',$id)
            ->select('TBL_TRN_SLSR01_CAL.*')
            ->orderBy('TBL_TRN_SLSR01_CAL.SRCALID','ASC')
            ->get()->toArray();
            $objCount4 = count($objSOCAL);

          

            $objSOPSLB=array();
            $objCount5 = count($objSOPSLB);
     
                
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
                             $TAXSTATE=[];
                             $objShpAddress=[] ;
                             $objBillAddress=[];

                             if(isset($objSO->SHIP_TO) && $objSO->SHIP_TO !=""){

                             $sid = $objSO->SHIP_TO;
         
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

                            if(isset($objSO->BILL_TO) && $objSO->BILL_TO !=""){
                                        
                            $bid = $objSO->BILL_TO;
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
                           
            
            $objglcode2 =[];                  
            if(isset($objSO->GLID_REF) && $objSO->GLID_REF !=""){
            $objglcode2 = DB::table('TBL_MST_GENERALLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('GLID','=',$objSO->GLID_REF)
            ->select('TBL_MST_GENERALLEDGER.*')
            ->first();
            }

           

            $objEMP=array();
            $objSPID=NULL;

            $objglcode = DB::table('TBL_MST_GENERALLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('STATUS','=',$Status)
            ->where('SUBLEDGER','=','1')
            ->select('TBL_MST_GENERALLEDGER.*')
            ->get()
            ->toArray();

            $objsubglcode =[];
            if(isset($objSO->GLID_REF) && $objSO->GLID_REF !=""){
            $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('GLID_REF','=',$objSO->GLID_REF)
            ->where('SGLID','=',$objSO->SLID_REF)
            ->select('TBL_MST_SUBLEDGER.*')
            ->first();
            }

                

           

            $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
            WHERE  CYID_REF = ? AND BRID_REF = ?  
            order by CTCODE ASC', [$CYID_REF, $BRID_REF ]);
    
            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
                WHERE  CYID_REF = ? AND BRID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                order by TNC_CODE ASC', [$CYID_REF, $BRID_REF, 'A' ]);
    
            $objCalculationHeader	=   Helper::getCalculationHeader(array(
                'CYID_REF'=>Auth::user()->CYID_REF,
                'BRID_REF'=>Session::get('BRID_REF'),
                'USERID'=>Auth::user()->USERID,
                'HEADING'=>'Transactions',
                'VTID_REF'=>$this->vtid_ref,
                'FORMID'=>$this->form_id
                ));
            
            $ObjUnionUDF = DB::table("TBL_MST_UDF_SRETURN")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('UDF_SRID')->from('TBL_MST_UDF_SRETURN')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                        
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                        
                    
    
            $objUdfSOData = DB::table('TBL_MST_UDF_SRETURN')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDF_SRETURN")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDF_SRID')->from('TBL_MST_UDF_SRETURN')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                            
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                            
            $objUdfSOData2 = DB::table('TBL_MST_UDF_SRETURN')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
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
    
            $ObjSalesQuotationData = DB::table("TBL_TRN_SLSI01_HDR")->select('*')
                        ->where('STATUS','=','A')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF)
                        ->where('FYID_REF','=',$FYID_REF) ->get() ->toArray(); 
                        
            $objSalesQuotationAData=array();
        

           

            $objItems=array();


           


            $objSQMAT=$objSOMAT;

           

            $objUOM = DB::table('TBL_MST_UOM')->select('*')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',$CYID_REF)
            ->get() ->toArray(); 


            $objItemUOMConv = DB::select("SELECT T1.*
                FROM TBL_MST_ITEM_UOMCONV T1
                INNER JOIN TBL_TRN_SLSR01_MAT T2 ON T1.ITEMID_REF=T2.ITEMID_REF
            ");

          
          


            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')->get() ->toArray(); 
            $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')->get() ->toArray(); 
        
           
        $FormId         =   $this->form_id;

        $AlpsStatus =   $this->AlpsStatus();
        $InputStatus=   "disabled";

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        $objothcurrency = $this->GetCurrencyMaster(); 

        $lastdt=$this->LastApprovedDocDate(); 
        $objCountAttachment=NULL;

        return view($this->view.$FormId.'view',compact(['FormId','objSO','objRights','objCount1','objSPID',
           'objCount2','objCount3','objCount4','objCount5','objSOMAT','objSOCAL','objSOTNC','objSOUDF','objSOPSLB',
           'objglcode','objCalculationHeader','objUdfSOData','objTNCHeader','objothcurrency','objglcode2',
           'objSalesPerson','objSalesQuotationAData','ObjSalesQuotationData','objsubglcode',
           'objShpAddress','objBillAddress','objItems','objSQMAT','objUOM','objItemUOMConv','objTNCDetails','objUdfSOData2',
           'objCalHeader','objCalDetails','TAXSTATE','AlpsStatus','InputStatus','TabSetting','lastdt','objCountAttachment']));
        }
     
       }

   
   public function update(Request $request){

    $r_count1 = $request['Row_Count1'];
    $r_count3 = $request['Row_Count3'];
    $r_count4 = $request['Row_Count4'];

    $GROSS_TOTAL    =   0; 
    $NET_TOTAL 		= $request['TotalValue'];
    $CGSTAMT        =   0; 
    $SGSTAMT        =   0; 
    $IGSTAMT        =   0; 
    $DISCOUNT       =   0; 
    $OTHER_CHARGES  =   0; 


    
    for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i]))
            {
                $GROSS_TOTAL+= $request['DISAFTT_AMT_'.$i]; 
                $CGSTAMT+= $request['CGSTAMT_'.$i]; 
                $SGSTAMT+= $request['SGSTAMT_'.$i]; 
                $IGSTAMT+= $request['IGSTAMT_'.$i]; 
                $DISCOUNT+= $request['DISCOUNT_AMT_'.$i]; 


                $StoreArr   =   array();
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];

                if($ITEMROWID !=""){

                    $exp        =   explode(",",$ITEMROWID);

                    foreach($exp as $val){
                        $keyid              =   explode("_",$val);
                        $batchid            =   intval($keyid[0]);

                        $objStore =  DB::table('TBL_TRN_SLSC01_STORE')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('STORE_MATID','=',$batchid)
                        ->select('STID_REF')
                        ->first();

                        $StoreArr[]=$objStore->STID_REF;
                    }
                }

                if(!empty($StoreArr)){
                    $StoreId    =   array_unique($StoreArr);
                    $STID_REF   =   implode(",",$StoreId);
                }
                else{
                    $STID_REF   =   NULL;
                }

                $WhereId    =   $request['exist_'.$i];
                $Field_Id   =   explode("-",$WhereId);
                $SEID_REF   =   intval($Field_Id[2]);
                $SQID_REF   =   intval($Field_Id[3]);
                $SO         =   intval($Field_Id[4]);
                $SCID_REF   =   intval($Field_Id[5]);

                $req_data[$i] = [
                    'SIID_REF' => $request['SQA_'.$i] ,
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'ITEMDESC'    => $request['ItemName_'.$i],
                    'MAIN_SRUOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'SRQTY' => $request['SO_QTY_'.$i],
                    'ALT_SRUOMID_REF' => $request['ALT_UOMID_REF_'.$i],
                    'STID_REF' => $STID_REF,
                    'SRRATE' => $request['RATEPUOM_'.$i],
                    'SRAMT' => $request['DISAFTT_AMT_'.$i],
                    'GST' =>  (isset($request['flagtype_'.$i])!="true" ? 0 : 1) ,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'BATCH_QTY_REF'   => $request['HiddenRowId_'.$i],
                    'SEID_REF'     => $SEID_REF,
                    'SQID_REF'     => $SQID_REF,
                    'SOID_REF'     => $SO,
                    'SCID_REF'     => $SCID_REF,
                ];
            }
        }


        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        $reqdata3=array();
        for ($i=0; $i<=$r_count3; $i++)
        {
                if(isset($request['UDFSOID_REF_'.$i]) && !is_null($request['UDFSOID_REF_'.$i]))
                {
                    $reqdata3[$i] = [
                        'UDF_SRID_REF'   => $request['UDFSOID_REF_'.$i],
                        'VALUE'      => $request['udfvalue_'.$i],
                    ];
                }
            
        }

        if(!empty($reqdata3))
        { 
            $wrapped_links3["UDF"] = $reqdata3; 
            $XMLUDF = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $XMLUDF = NULL; 
        }

        $reqdata4=array();
        for ($i=0; $i<=$r_count4; $i++)
        {
                if(isset($request['CTID_REF']) && !is_null($request['CTID_REF']))
                {
                    if(isset($request['TID_REF_'.$i]))
                    {

                        if(isset($request['CT_TYPE_'.$i]) && $request['CT_TYPE_'.$i]=="DISCOUNT"){
                          
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

        if(!empty($reqdata4))
        { 
            $wrapped_links4["CAL"] = $reqdata4; 
            $XMLCAL = ArrayToXml::convert($wrapped_links4);
        }
        else
        {
            $XMLCAL = NULL; 
        }

        
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $dataArr    =   array();
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];

                if($ITEMROWID !=""){
                    $exp        =   explode(",",$ITEMROWID);

                    foreach($exp as $val){
                        $keyid              =   explode("_",$val);
                        $batchid            =   $keyid[0];
                        $qty                =   $keyid[1];
                        $dataArr[$batchid]  =   $qty;
                    }
                }

                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        $objBatch =  DB::table('TBL_TRN_SLSC01_STORE')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('STORE_MATID','=',$key)
                        ->select('STORE_MATID','BATCHNO','ITEMID_REF','STID_REF','SERIALNO','MAINUOMID_REF','ALTUOMID_REF','DISPATCH_MAIN_QTY')
                        ->first();

                        $BATCHNO        =   $objBatch->BATCHNO;
                        $SERIALNO       =   $objBatch->SERIALNO;
                        $STID_REF       =   $objBatch->STID_REF;
                        $MAINUOMID_REF  =   $objBatch->MAINUOMID_REF;
                        $ALTUOMID_REF   =   $objBatch->ALTUOMID_REF;
                        $BATCHNO        =   $objBatch->BATCHNO;

                        $STOCK_INHAND   =   $this->getStockQty($BATCHNO,$SERIALNO,$STID_REF,$ITEMID_REF,$MAINUOMID_REF);
                        $RETURN_QTYA    =   $this->getAltUmQty($ALTUOMID_REF,$ITEMID_REF,$val);

                        $req_data33[$i][] = [
                            'SIID_REF'      => $request['SQA_'.$i],
                            'ITEMID_REF'    => $ITEMID_REF,
                            'BATCH_NO'      => $BATCHNO,
                            'STID_REF'      => $STID_REF,
                            'SERIAL_NO'     => $SERIALNO,
                            'MAIN_UOMID_REF'=> $MAINUOMID_REF,
                            'STOCK_INHAND'  => $STOCK_INHAND !=''?floatval($STOCK_INHAND):0,
                            'RETURN_QTYM'   => $val,
                            'ALT_UOMID_REF' => $ALTUOMID_REF,
                            'RETURN_QTYA'   => $RETURN_QTYA,
                        ];

                    }
                }
            }
        }


        $wrapped_links33["STORE"] = $req_data33; 
        $XMLSTORE = ArrayToXml::convert($wrapped_links33);

        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $SSI_NO     = $request['SONO'];
        $SSI_DT     = $request['SODT'];
        $GLID_REF   = $request['GLID_REF'];
        $SLID_REF   = $request['SLID_REF'];        
        $BILLTO     = $request['BILLTO'];
        $SHIPTO     = $request['SHIPTO'];
        $REASONOFSR = $request['REASONOFSR'];
        $COMMONNRT  = $request['COMMONNRT'];
        $PAYMENT    =(isset($request['chk_Purchase'])!="true" ? 0 : 1);
        $BILL_NO    = $request['BILL_NO'];
        $BILL_DT    = $request['BILL_DT'];

        $log_data = [ 
            $SSI_NO,$SSI_DT,$GLID_REF,$SLID_REF, $REASONOFSR,$COMMONNRT,$BILLTO,$SHIPTO,$CYID_REF, $BRID_REF,
            $FYID_REF,$VTID_REF,$XMLMAT,$XMLSTORE,$XMLUDF,$XMLCAL,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,
            $IPADDRESS,$PAYMENT,$BILL_NO,$BILL_DT,$FC,$CRID_REF,$CONVFACT,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES
        ];
     
        $sp_result = DB::select('EXEC SP_SAR_UP ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?, ?,?,?,?,?', $log_data);  

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
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
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];

        $GROSS_TOTAL    =   0; 
        $NET_TOTAL 		= $request['TotalValue'];
        $CGSTAMT        =   0; 
        $SGSTAMT        =   0; 
        $IGSTAMT        =   0; 
        $DISCOUNT       =   0; 
        $OTHER_CHARGES  =   0; 
       
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i]))
            {

                $GROSS_TOTAL+= $request['DISAFTT_AMT_'.$i]; 
                $CGSTAMT+= $request['CGSTAMT_'.$i]; 
                $SGSTAMT+= $request['SGSTAMT_'.$i]; 
                $IGSTAMT+= $request['IGSTAMT_'.$i]; 
                $DISCOUNT+= $request['DISCOUNT_AMT_'.$i]; 

                $StoreArr   =   array();
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];

                if($ITEMROWID !=""){

                    $exp        =   explode(",",$ITEMROWID);

                    foreach($exp as $val){
                        $keyid              =   explode("_",$val);
                        $batchid            =   $keyid[0];

                        $objStore =  DB::table('TBL_TRN_SLSC01_STORE')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('STORE_MATID','=',$batchid)
                        ->select('STID_REF')
                        ->first();

                        $StoreArr[]=$objStore->STID_REF;
                    }
                }

                if(!empty($StoreArr)){
                    $StoreId    =   array_unique($StoreArr);
                    $STID_REF   =   implode(",",$StoreId);
                }
                else{
                    $STID_REF   =   NULL;
                }

                $WhereId    =   $request['exist_'.$i];
                $Field_Id   =   explode("-",$WhereId);
                $SEID_REF   =   intval($Field_Id[2]);
                $SQID_REF   =   intval($Field_Id[3]);
                $SO         =   intval($Field_Id[4]);
                $SCID_REF   =   intval($Field_Id[5]);

                $req_data[$i] = [
                    'SIID_REF' => $request['SQA_'.$i],
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'ITEMDESC'    => $request['ItemName_'.$i],
                    'MAIN_SRUOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'SRQTY' => $request['SO_QTY_'.$i],
                    'ALT_SRUOMID_REF' => $request['ALT_UOMID_REF_'.$i],
                    'STID_REF' => $STID_REF,
                    'SRRATE' => $request['RATEPUOM_'.$i],
                    'SRAMT' => $request['DISAFTT_AMT_'.$i],
                    'GST' =>  (isset($request['flagtype_'.$i])!="true" ? 0 : 1) ,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'BATCH_QTY_REF'   => $request['HiddenRowId_'.$i],
                    'SEID_REF'     => $SEID_REF,
                    'SQID_REF'     => $SQID_REF,
                    'SOID_REF'     => $SO,
                    'SCID_REF'     => $SCID_REF,
                ];
            }
        }


        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        $reqdata3=array();
        for ($i=0; $i<=$r_count3; $i++)
        {
                if(isset($request['UDFSOID_REF_'.$i]) && !is_null($request['UDFSOID_REF_'.$i]))
                {
                    $reqdata3[$i] = [
                        'UDF_SRID_REF'   => $request['UDFSOID_REF_'.$i],
                        'VALUE'      => $request['udfvalue_'.$i],
                    ];
                }
            
        }

        if(!empty($reqdata3))
        { 
            $wrapped_links3["UDF"] = $reqdata3; 
            $XMLUDF = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $XMLUDF = NULL; 
        }

        $reqdata4=array();
        for ($i=0; $i<=$r_count4; $i++)
        {
                if(isset($request['CTID_REF']) && !is_null($request['CTID_REF']))
                {
                    if(isset($request['TID_REF_'.$i]))
                    {
                        if(isset($request['CT_TYPE_'.$i]) && $request['CT_TYPE_'.$i]=="DISCOUNT"){
                          
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

        if(!empty($reqdata4))
        { 
            $wrapped_links4["CAL"] = $reqdata4; 
            $XMLCAL = ArrayToXml::convert($wrapped_links4);
        }
        else
        {
            $XMLCAL = NULL; 
        }

        
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $dataArr    =   array();
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];

                if($ITEMROWID !=""){
                    $exp        =   explode(",",$ITEMROWID);

                    foreach($exp as $val){
                        $keyid              =   explode("_",$val);
                        $batchid            =   $keyid[0];
                        $qty                =   $keyid[1];
                        $dataArr[$batchid]  =   $qty;
                    }
                }

                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        $objBatch =  DB::table('TBL_TRN_SLSC01_STORE')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('STORE_MATID','=',$key)
                        ->select('STORE_MATID','BATCHNO','ITEMID_REF','STID_REF','SERIALNO','MAINUOMID_REF','ALTUOMID_REF','DISPATCH_MAIN_QTY')
                        ->first();

                        $BATCHNO        =   $objBatch->BATCHNO;
                        $SERIALNO       =   $objBatch->SERIALNO;
                        $STID_REF       =   $objBatch->STID_REF;
                        $MAINUOMID_REF  =   $objBatch->MAINUOMID_REF;
                        $ALTUOMID_REF   =   $objBatch->ALTUOMID_REF;
                        $BATCHNO        =   $objBatch->BATCHNO;

                        $STOCK_INHAND   =   $this->getStockQty($BATCHNO,$SERIALNO,$STID_REF,$ITEMID_REF,$MAINUOMID_REF);
                        $RETURN_QTYA    =   $this->getAltUmQty($ALTUOMID_REF,$ITEMID_REF,$val);

                        $req_data33[$i][] = [
                            'SIID_REF' => $request['SQA_'.$i],
                            'ITEMID_REF'    => $ITEMID_REF,
                            'BATCH_NO'      => $BATCHNO,
                            'STID_REF'      => $STID_REF,
                            'SERIAL_NO'     => $SERIALNO,
                            'MAIN_UOMID_REF'=> $MAINUOMID_REF,
                            'STOCK_INHAND'  => $STOCK_INHAND !=''?floatval($STOCK_INHAND):0,
                            'RETURN_QTYM'   => $val,
                            'ALT_UOMID_REF' => $ALTUOMID_REF,
                            'RETURN_QTYA'   => $RETURN_QTYA,
                        ];

                    }
                }
            }
        }

      

        $wrapped_links33["STORE"] = $req_data33; 
        $XMLSTORE = ArrayToXml::convert($wrapped_links33);

        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $SSI_NO     = $request['SONO'];
        $SSI_DT     = $request['SODT'];
        $GLID_REF   = $request['GLID_REF'];
        $SLID_REF   = $request['SLID_REF'];        
        $BILLTO     = $request['BILLTO'];
        $SHIPTO     = $request['SHIPTO'];
        $REASONOFSR = $request['REASONOFSR'];
        $COMMONNRT  = $request['COMMONNRT'];
        $PAYMENT    =(isset($request['chk_Purchase'])!="true" ? 0 : 1);
        $BILL_NO    = $request['BILL_NO'];
        $BILL_DT    = $request['BILL_DT'];
        

        $log_data = [ 
            $SSI_NO,$SSI_DT,$GLID_REF,$SLID_REF, $REASONOFSR,$COMMONNRT,$BILLTO,$SHIPTO,$CYID_REF, $BRID_REF,
            $FYID_REF,$VTID_REF,$XMLMAT,$XMLSTORE,$XMLUDF,$XMLCAL,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,
            $IPADDRESS,$PAYMENT,$BILL_NO,$BILL_DT,
            $FC,$CRID_REF,$CONVFACT,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES
        ]; 
     
        $sp_result = DB::select('EXEC SP_SAR_UP ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?, ?,?,?,?,?', $log_data);   
             
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully Approved.']);

        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
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
                $TABLE      =   "TBL_TRN_SLSR01_HDR";
                $FIELD      =   "SRID";
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
        $TABLE      =   "TBL_TRN_SLSR01_HDR";
        $FIELD      =   "SRID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_SLSR01_MAT',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_SLSR01_STORE',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_SLSR01_UDF',
        ];
        $req_data[3]=[
            'NT'  => 'TBL_TRN_SLSR01_CAL',
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

            $objResponse = DB::table('TBL_TRN_SLSR01_HDR')->where('SRID','=',$id)->first();

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
        
		$image_path         =   "docs/company".$CYID_REF."/SalesReturn";     
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

    public function checkso(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $SONO = $request->SONO;
        
        $objSO = DB::table('TBL_TRN_SLSR01_HDR')
        ->where('TBL_TRN_SLSR01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLSR01_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_SLSR01_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_SLSR01_HDR.SRNO','=',$SONO)
        ->select('TBL_TRN_SLSR01_HDR.SRID')
        ->first();
        
        if($objSO){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate SONO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

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

   public function getStoreDetails(Request $request){

        $ITEMID_REF = $request['ITEMID_REF'];
        $SIID_REF   = $request['SIID_REF'];
        $ROW_ID     = $request['ROW_ID'];
        $ITEMROWID  = $request['ITEMROWID'];
        $ACTION_TYPE= $request['ACTION_TYPE'] =="VIEW"?'disabled':'';
        $WhereId    = $request['WhereId'];
        $SRNOA      =   NULL;
        $BATCHNOA   =   NULL;

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

        $Field_Id   =   explode("-",$WhereId);
        $SIID_REF   =   $Field_Id[0];
        $ITEMID_REF =   $Field_Id[1];
        $SEID_REF   =   $Field_Id[2];
        $SQID_REF   =   $Field_Id[3];
        $SO         =   $Field_Id[4];
        $SCID_REF   =   $Field_Id[5];
		
		if($SEID_REF == NULL && $SQID_REF == NULL)
		{
			$objRes =  DB::table('TBL_TRN_SLSC01_MAT')
			->where('SCID_REF','=',$SCID_REF)
			->where('SO','=',$SO)
			->where('ITEMID_REF','=',$ITEMID_REF)
			->select('SCMATID','SCID_REF','SO','SQID_REF','SEID_REF','ITEMID_REF')
			->first();
		}
		else if($SEID_REF == NULL && $SQID_REF != NULL)
		{
			$objRes =  DB::table('TBL_TRN_SLSC01_MAT')
			->where('SCID_REF','=',$SCID_REF)
			->where('SO','=',$SO)
			->where('SQID_REF','=',$SQID_REF)
			->where('ITEMID_REF','=',$ITEMID_REF)
			->select('SCMATID','SCID_REF','SO','SQID_REF','SEID_REF','ITEMID_REF')
			->first();
		}
		else
		{
        $objRes =  DB::table('TBL_TRN_SLSC01_MAT')
        ->where('SCID_REF','=',$SCID_REF)
        ->where('SO','=',$SO)
        ->where('SQID_REF','=',$SQID_REF)
        ->where('SEID_REF','=',$SEID_REF)
        ->where('ITEMID_REF','=',$ITEMID_REF)
        ->select('SCMATID','SCID_REF','SO','SQID_REF','SEID_REF','ITEMID_REF')
        ->first();
        }
        
        $SCMATID    =   $objRes->SCMATID;
        $SCID_REF   =   $objRes->SCID_REF;
        $SO         =   $objRes->SO;
        $SQID_REF   =   $objRes->SQID_REF;
        $SEID_REF   =   $objRes->SEID_REF;
        $ITEMID_REF =   $objRes->ITEMID_REF;

        
		if($SEID_REF == NULL && $SQID_REF == NULL){
			$objBatch =  DB::SELECT("SELECT 
            ST.STORE_MATID,ST.SCID_REF,SC.SO AS SOID_REF,SC.SQID_REF,
			SC.SEID_REF,ST.ITEMID_REF,BT.STID_REF,BT.BATCH_CODE AS BATCHNO,ST.SERIALNO,SC.MAINUOMID_REF,SC.ALTUOMID_REF,
			CONCAT(T4.STCODE,'-',T4.NAME) AS StoreName, 
			CONCAT(T5.UOMCODE,'-',T5.DESCRIPTIONS) as MainUom,
			CONCAT(T6.UOMCODE,'-',T6.DESCRIPTIONS) as AltUom 
			FROM TBL_TRN_SLSC01_STORE ST 
			left join (SELECT SCMATID,SCID_REF,SO,SQID_REF,SEID_REF,ITEMID_REF ,SRNO,MAINUOMID_REF,ALTUOMID_REF
			FROM TBL_TRN_SLSC01_MAT WHERE SCID_REF='$SCID_REF' AND SO='$SO' AND SQID_REF='$SQID_REF' AND SEID_REF='$SEID_REF' AND ITEMID_REF='$ITEMID_REF') AS SC 
			ON  ST.SCID_REF=SC.SCID_REF  AND ST.ITEMID_REF=SC.ITEMID_REF AND SC.SRNO = ST.SERIALNO
			LEFT JOIN TBL_MST_BATCH BT ON ST.BATCHID_REF = BT.BATCHID
			LEFT JOIN TBL_MST_STORE T4 ON BT.STID_REF=T4.STID
			LEFT JOIN TBL_MST_UOM T5 ON SC.MAINUOMID_REF=T5.UOMID
			LEFT JOIN TBL_MST_UOM T6 ON SC.ALTUOMID_REF=T6.UOMID
			WHERE ST.SCID_REF='$SCID_REF' AND ST.ITEMID_REF='$ITEMID_REF'
			");
		}
		else if($SEID_REF == NULL && $SQID_REF != NULL)
		{
			$objBatch =  DB::SELECT("SELECT ST.STORE_MATID,ST.SCID_REF,SC.SO AS SOID_REF,SC.SQID_REF,
			SC.SEID_REF,ST.ITEMID_REF,BT.STID_REF,BT.BATCH_CODE AS BATCHNO,ST.SERIALNO,SC.MAINUOMID_REF,SC.ALTUOMID_REF,
			CONCAT(T4.STCODE,'-',T4.NAME) AS StoreName, 
			CONCAT(T5.UOMCODE,'-',T5.DESCRIPTIONS) as MainUom,
			CONCAT(T6.UOMCODE,'-',T6.DESCRIPTIONS) as AltUom 
			FROM TBL_TRN_SLSC01_STORE ST 
			left join (SELECT SCMATID,SCID_REF,SO,SQID_REF,SEID_REF,ITEMID_REF ,SRNO,MAINUOMID_REF,ALTUOMID_REF
			FROM TBL_TRN_SLSC01_MAT WHERE SCID_REF='$SCID_REF' AND SO='$SO' AND SQID_REF='$SQID_REF' AND SEID_REF='$SEID_REF' AND ITEMID_REF='$ITEMID_REF') AS SC 
			ON  ST.SCID_REF=SC.SCID_REF  AND ST.ITEMID_REF=SC.ITEMID_REF AND SC.SRNO = ST.SERIALNO
			LEFT JOIN TBL_MST_BATCH BT ON ST.BATCHID_REF = BT.BATCHID
			LEFT JOIN TBL_MST_STORE T4 ON BT.STID_REF=T4.STID
			LEFT JOIN TBL_MST_UOM T5 ON SC.MAINUOMID_REF=T5.UOMID
			LEFT JOIN TBL_MST_UOM T6 ON SC.ALTUOMID_REF=T6.UOMID
			WHERE ST.SCID_REF='$SCID_REF' AND ST.ITEMID_REF='$ITEMID_REF'
			");
		}
		else
		{
			$objBatch =  DB::SELECT("SELECT ST.STORE_MATID,ST.SCID_REF,SC.SO AS SOID_REF,SC.SQID_REF,
			SC.SEID_REF,ST.ITEMID_REF,BT.STID_REF,BT.BATCH_CODE AS BATCHNO,ST.SERIALNO,SC.MAINUOMID_REF,SC.ALTUOMID_REF,
			CONCAT(T4.STCODE,'-',T4.NAME) AS StoreName, 
			CONCAT(T5.UOMCODE,'-',T5.DESCRIPTIONS) as MainUom,
			CONCAT(T6.UOMCODE,'-',T6.DESCRIPTIONS) as AltUom 
			FROM TBL_TRN_SLSC01_STORE ST 
			left join (SELECT SCMATID,SCID_REF,SO,SQID_REF,SEID_REF,ITEMID_REF ,SRNO,MAINUOMID_REF,ALTUOMID_REF
			FROM TBL_TRN_SLSC01_MAT WHERE SCID_REF='$SCID_REF' AND SO='$SO' AND SQID_REF='$SQID_REF' AND SEID_REF='$SEID_REF' AND ITEMID_REF='$ITEMID_REF') AS SC 
			ON  ST.SCID_REF=SC.SCID_REF  AND ST.ITEMID_REF=SC.ITEMID_REF AND SC.SRNO = ST.SERIALNO
			LEFT JOIN TBL_MST_BATCH BT ON ST.BATCHID_REF = BT.BATCHID
			LEFT JOIN TBL_MST_STORE T4 ON BT.STID_REF=T4.STID
			LEFT JOIN TBL_MST_UOM T5 ON SC.MAINUOMID_REF=T5.UOMID
			LEFT JOIN TBL_MST_UOM T6 ON SC.ALTUOMID_REF=T6.UOMID
			WHERE ST.SCID_REF='$SCID_REF' AND ST.ITEMID_REF='$ITEMID_REF'
			");
        }
        
        echo '<thead>';
        echo '<tr>';
        echo '<th>Store</th>';
        echo $BATCHNOA =='1'?'<th>Batch No</th>':'';
        echo $SRNOA =='1'?'<th>Serial No</th>':'';
        echo '<th>Main UoM (MU)</th>';
        echo '<th hidden>Stock-in-hand</th>';
        echo '<th width="15%">Return Qty (MU)</th>';
        echo '<th>Alt UOM (AU)</th>';
        echo '<th width="15%">Return Qty (AU)</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        if(!empty($objBatch)){
            foreach($objBatch as $key=>$val){
                
                $desc6          =   $val->STORE_MATID;
                $qtyvalue       =   array_key_exists($desc6, $dataArr)?$dataArr[$desc6]:0;
                $AluQty         =   $this->getAltUmQty($val->ALTUOMID_REF,$val->ITEMID_REF,$qtyvalue);
                $STOCK_INHAND   =   $this->getStockQty($val->BATCHNO,$val->SERIALNO,$val->STID_REF,$ITEMID_REF,$val->MAINUOMID_REF);
            
                echo '<tr  class="participantRow33">';
                echo '<td>'.$val->StoreName.'</td>';
                echo $BATCHNOA =='1'?'<td>'.$val->BATCHNO.'</td>':'';
                echo $SRNOA =='1'?'<td>'.$val->SERIALNO.'</td>':'';
                echo '<td>'.$val->MainUom.'</td>';
                echo '<td hidden>'.$STOCK_INHAND.'</td>';
                echo '<td style="width:10%"><input '.$ACTION_TYPE.' type="text" name="UserQty_'.$key.'" id="UserQty_'.$key.'" value="'.$qtyvalue.'" class="qtytext" onkeyup="checkStoreQty('.$ROW_ID.','.$qtyvalue.',this.value,'.$key.','.$val->ITEMID_REF.','.$val->ALTUOMID_REF.')" onkeypress="return isNumberDecimalKey(event,this)" autocomplete="off"  ></td>';
                echo '<td>'.$val->AltUom.'</td>';
                echo '<td style="width:10%"><input '.$ACTION_TYPE.' type="text" name="AltUserQty_'.$key.'" id="AltUserQty_'.$key.'" value="'.$AluQty.'" readonly class="qtytext" autocomplete="off"  ></td>';
                echo '<td hidden><input type="hidden" name="BATCHID_'.$key.'" id="BATCHID_'.$key.'" value="'.$desc6.'" class="qtytext" ></td>';
                echo '</tr>';
            }
        }
        else{
            echo '<tr  class="participantRow33">';
            echo '<td colspan="6" style="text-align:left;">Record not found.</td>';
            echo '</tr>';
        }

        echo '</tbody>';
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

    public function getStockQty($BATCH_CODE,$SERIALNO,$STID_REF,$ITEMID_REF,$UOMID_REF){

        $CYID_REF   = Auth::user()->CYID_REF;
        $BRID_REF   = Session::get('BRID_REF');
        $FYID_REF   = Session::get('FYID_REF');

        if($BATCH_CODE !="" && $SERIALNO ==""){
            $ObjData =  DB::table('TBL_MST_BATCH')
            ->where('BATCH_CODE','=',$BATCH_CODE)
            ->where('STID_REF','=',$STID_REF)
            ->where('ITEMID_REF','=',$ITEMID_REF)
            ->where('UOMID_REF','=',$UOMID_REF)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('STATUS','=',"A")
            ->select('CURRENT_QTY')
            ->first();
        }
        else if($SERIALNO !="" && $BATCH_CODE ==""){
            $ObjData =  DB::table('TBL_MST_BATCH')
            ->where('SERIALNO','=',$SERIALNO)
            ->where('STID_REF','=',$STID_REF)
            ->where('ITEMID_REF','=',$ITEMID_REF)
            ->where('UOMID_REF','=',$UOMID_REF)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('STATUS','=',"A")
            ->select('CURRENT_QTY')
            ->first();
        }
        else if($BATCH_CODE !="" && $SERIALNO !=""){
            $ObjData =  DB::table('TBL_MST_BATCH')
            ->where('BATCH_CODE','=',$BATCH_CODE)
            ->where('SERIALNO','=',$SERIALNO)
            ->where('STID_REF','=',$STID_REF)
            ->where('ITEMID_REF','=',$ITEMID_REF)
            ->where('UOMID_REF','=',$UOMID_REF)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('STATUS','=',"A")
            ->select('CURRENT_QTY')
            ->first();
        }
        
        if(!empty($ObjData)){
            return $ObjData->CURRENT_QTY;
        }else{
            return '0';
        }

    }


    

    public function LastApprovedDocDate(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $VTID_REF     =   $this->vtid_ref;
        return $objlastDocDate = DB::select('SELECT MAX(SRDT) SRDT FROM TBL_TRN_SLSR01_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $VTID_REF, $Status ]);

    }

    
}
