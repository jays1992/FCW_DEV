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
use App\Helpers\ClearTaxApi;

class TrnFrm156Controller extends Controller
{
    protected $form_id = 156;
    protected $vtid_ref   = 252;
    protected $view     = "transactions.sales.SalesServiceInvoice.trnfrm";
  
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];
    
    public function __construct()
    {
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

        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList	=	DB::select("select hdr.SSIID,hdr.SSI_NO,hdr.SSI_DT,hdr.DUE_DT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.SSIID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_SLSI02_HDR hdr
                            on a.VID = hdr.SSIID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_SUBLEDGER sl ON hdr.SGLID_REF = sl.SGLID 
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.SSIID DESC ");

                            if(isset($objDataList) && !empty($objDataList)){
                                foreach($objDataList as $key=>$val){
        
                                    $SSIID   =   $val->SSIID;
                                   
                                    $data   =   DB::table('TBL_TRN_SLSI01_IRN')->where('DOC_ID','=',$SSIID)->where('DOC_TYPE','=',$this->vtid_ref)->select('STATUS') ->orderBy('IRN_ID','DESC')->first();
                                    if(isset($data) && !empty($data)){
                                        $objDataList[$key]->IRN_NO    =   $data->STATUS;
                                    }

                                    $data   =   DB::table('TBL_TRN_SLSI01_EWAY')->where('DOC_ID','=',$SSIID)->where('DOC_TYPE','=',$this->vtid_ref)->select('STATUS') ->orderBy('IRN_ID','DESC')->first();
                                    if(isset($data) && !empty($data)){
                                        $objDataList[$key]->EWAY_BILLNO    =   $data->STATUS;
                                    }

                                }
                            }

                           // DD($objDataList);

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
            $SSIID       =   $myValue['SSI_NO'];
            $Flag        =   $myValue['Flag'];
        }
        else
        {
            $SSIID       =   Session::get('SSIID');
            $Flag        =   $myValue['Flag'];
        }
        
        $objSalesOrder = DB::table('TBL_TRN_SLSI02_HDR')
        ->where('TBL_TRN_SLSI02_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLSI02_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        ->where('TBL_TRN_SLSI02_HDR.SSIID','=',$SSIID)
        ->select('TBL_TRN_SLSI02_HDR.*')
        ->first();
        
        $ssrs = new \SSRS\Report('http://103.139.58.23:8181//ReportServer/', array('username' => 'App', 'password' => 'admin@123'));
        $result = $ssrs->loadReport('/ERP/SSIPrint');
        
        $reportParameters = array(
            'SSI_NO' => $objSalesOrder->SSI_NO,
        );
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
        ->setExecutionParameters($parameters);
        if($Flag == 'H')
        {
            Session::put('SSIID', $SSIID);
            $output = $ssrs->render('HTML4.0'); 
            echo $output;
        }
        else if($Flag == 'P')
        {
            $output = $ssrs->render('PDF'); 
            return $output->download('Report.pdf');
        }
        else if($Flag == 'E')
        {
            $output = $ssrs->render('CSV'); 
            return $output->download('Report.xls');
        }
        else if($Flag == 'R')
        {
            $output = $ssrs->render('HTML4.0'); 
            echo $output;
        }
         
     } 

    public function add(){       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

      

        $d_currency = DB::table('TBL_MST_COMPANY')
        ->where('STATUS','=',$Status)
        ->where('CYID','=',Auth::user()->CYID_REF)
        ->select('TBL_MST_COMPANY.CRID_REF')
        ->first();

        $objothcurrency = $this->GetCurrencyMaster(); 

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_SLSI02_HDR',
            'HDR_ID'=>'SSIID',
            'HDR_DOC_NO'=>'SSI_NO',
            'HDR_DOC_DT'=>'SSI_DT'
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
        
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_SSI")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDFSSIID')->from('TBL_MST_UDFFOR_SSI')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                     
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                
                   

        $objUdfSOData = DB::table('TBL_MST_UDFFOR_SSI')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdfSOData);
    


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
      
          

        $FormId =   $this->form_id;    
        $AlpsStatus =   $this->AlpsStatus();
        $objTemplateMaster  =$this->getTemplateMaster("SALES");
        $lastdt=$this->LastApprovedDocDate(); 

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
       
    return view('transactions.sales.SalesServiceInvoice.trnfrm156add',
    compact(['objCalculationHeader','objUdfSOData','objTNCHeader','objothcurrency',
    'objSalesPerson','objSalesQuotationAData','ObjSalesQuotationData','objCountUDF',
   'AlpsStatus','FormId','objTemplateMaster','lastdt','TabSetting','doc_req','docarray']));       
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
                        $row3 =    '<td hidden style="text-align:center;" ><input type="checkbox" class="filter-none" name="calACTUAL_'.$dindex.'" id="calACTUAL_'.$dindex.'" checked ></td>';
                    }
                    else{
                        $row3 =    '<td hidden style="text-align:center;" ><input type="checkbox" class="filter-none" name="calACTUAL_'.$dindex.'" id="calACTUAL_'.$dindex.'"  ></td>';
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

    public function getsalesquotation(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $SLID_REF       =   $request['id'];
        $BILLTO_REF     =   $request['BILLTO_REF'];
        $SHIPTO_REF     =   $request['SHIPTO_REF'];
       
        $SP_PARAMETERS  = [$CYID_REF,$BRID_REF,$FYID_REF,$SLID_REF,$BILLTO_REF,$SHIPTO_REF];

        $ObjData        =  DB::select('EXEC SP_SSO_GETLIST ?,?,?,?,?,?', $SP_PARAMETERS);
    
        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){

              
                $row = '';
                $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_SSOID_REF[]" id="sqcode_'.$index.'" class="clssqid" value="'.$dataRow->SSOID.'" ></td>';
                $row = $row.'<td class="ROW2">'.$dataRow->SSO_NO;
                $row = $row.'<input type="hidden" id="txtsqcode_'.$index.'" data-desc="'.$dataRow->SSO_NO .'"';
                $row = $row.'" data-descdate="'.$dataRow->SSO_DT. '"value="'.$dataRow->SSOID.'"/></td><td class="ROW3">'.$dataRow->SSO_DT.'</td></tr>';
                
                echo $row;
            }
        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
    }


    public function getItemDetailsQuotationwise(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $taxstate = $request['taxstate'];
        $SSOID_REF= $request['id'];
        $StdCost = 0;

        $objCOMPANY = DB::table('TBL_MST_COMPANY')
            ->where('STATUS','=','A')
            ->where('CYID','=',Auth::user()->CYID_REF)
            ->select('TBL_MST_COMPANY.NAME')
            ->first();

        $hidden     =   strpos($objCOMPANY->NAME,"ALPS")!== false?'':'hidden';

        
       
        
        $ObjItem =  DB::select("SELECT * FROM TBL_MST_ITEM T1
        INNER JOIN TBL_TRN_SLSO04_MAT T2 ON T1.ITEMID=T2.ITEMID_REF
        WHERE T1.CYID_REF = '$CYID_REF' 
        AND ( T1.DEACTIVATED IS NULL OR T1.DEACTIVATED = 0 ) AND T1.STATUS ='$Status' AND T2.SSOID_REF='$SSOID_REF'");


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

                    $TOQTY    =   $dataRow->SSO_QTY;
                    $FROMQTY    =   $dataRow->SSO_QTY;

                    

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

                        
                        $row = '';
                        if($taxstate != "OutofState"){
                        $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                        $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'" data-desc1="'.$dataRow->SSO_QTY.'" data-desc2="'.$dataRow->RATE_PER_UOM.'" data-desc3="'.$dataRow->DIS_PER.'" data-desc4="'.$dataRow->DIS_AMT.'" data-desc5="'.$SSOID_REF.'-'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'"/></td> <td style="width:10%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'" value="'.$dataRow->NAME.'"/></td>';
                        $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'" data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td style="width:8%;" id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'" value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                        $row = $row.'<td style="width:8%;" id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->RATE_PER_UOM.'" value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                        $row = $row.'<td style="width:8%;" id="itax_'.$dataRow->ITEMID.'"><input type="hidden" id="txtitax_'.$dataRow->ITEMID.'" data-desc="'.$Taxid[0].'" value="'.$Taxid[1].'"/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td> 
                        
                        <td style="width:8%;">'.$BusinessUnit.'</td>
                        <td style="width:8%;" '.$hidden.'>'.$ALPS_PART_NO.'</td>
                        <td style="width:8%;" '.$hidden.'>'.$CUSTOMER_PART_NO.'</td>
                        <td style="width:8%;" '.$hidden.'>'.$OEM_PART_NO.'</td>
                        <td style="width:8%;">Authorized</td></tr>';
                        }
                        else
                        {
                            $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                            $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                            $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'" data-desc1="'.$dataRow->SSO_QTY.'" data-desc2="'.$dataRow->RATE_PER_UOM.'" data-desc3="'.$dataRow->DIS_PER.'" data-desc4="'.$dataRow->DIS_AMT.'" data-desc5="'.$SSOID_REF.'-'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'"/></td> <td style="width:10%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'" value="'.$dataRow->NAME.'"/></td>';
                            $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'" data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                            $row = $row.'<td style="width:8%;" id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'" value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                            $row = $row.'<td style="width:8%;" id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->RATE_PER_UOM.'" value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                            $row = $row.'<td style="width:8%;" id="itax_'.$dataRow->ITEMID.'"><input type="hidden" id="txtitax_'.$dataRow->ITEMID.'" data-desc="'.$Taxid[0].'" value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                            
                            <td style="width:8%;">'.$BusinessUnit.'</td>
                            <td style="width:8%;" '.$hidden.'>'.$ALPS_PART_NO.'</td>
                            <td style="width:8%;" '.$hidden.'>'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:8%;" '.$hidden.'>'.$OEM_PART_NO.'</td>
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

        $AlpsStatus =   $this->AlpsStatus();
        
                
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
                    
                        
                        $row = '';
                        if($taxstate != "OutofState"){
                        $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                        $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'"
                        value="'.$dataRow->ITEMID.'"/></td>
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
                            $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'"
                            value="'.$dataRow->ITEMID.'"/></td>
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
                       
                       

                        $row = $row.'<tr>
                        <td class="ROW1"> <input type="checkbox" name="SELECT_BILLTO[]" id="billto_'.$dataRow->CLID .'"  class="clsbillto" value="'.$dataRow->CLID.'" ></td>
                        <td class="ROW2">'.$dataRow->NAME;
                        $row = $row.'<input type="hidden" id="txtbillto_'.$dataRow->CLID.'" data-desc="'.$objAddress.'" 
                        value="'.$dataRow->CLID.'"/></td><td class="ROW3">'.$objAddress.'</td></tr>';
                        
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
                        $row = $row.'<input type="hidden" id="txtshipto_'.$dataRow->CLID.'" data-desc="'.$TAXSTATE.'" data-shipaddress="'.$objAddress.'"  value="'.$dataRow->CLID.'"/></td>
                        <td class="ROW3" id="txtshipadd_'.$dataRow->CLID.'" >'.$objAddress.'</td></tr>';

                        echo $row;
                    }
            
                    }else{
                        echo '<tr><td colspan="2">Record not found.</td></tr>';
                    }
                    exit();
                }
            }


            public function attachment($id){

                if(!is_null($id)){
                
                    $FormId     =   $this->form_id;
        
                    $objResponse = DB::table('TBL_TRN_SLSI02_HDR')->where('SSIID','=',$id)->first();
        
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

    
   public function save(Request $request) {
    
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];
        $r_count5 = $request['Row_Count5'];
        $r_count6 = $request['Row_Count6'];

        $GROSS_TOTAL    =   0; 
        $NET_TOTAL 		= 	$request['TotalValue'];
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
                    'SSOID_REF' => (!empty($request['SQA_'.$i])) == 'true' ? $request['SQA_'.$i] : "0" ,
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'REMARKS' => $request['REMARKS_'.$i],
                    'SSI_QTY' => $request['SO_QTY_'.$i],
                    'RATE_PRUOM' => $request['RATEPUOM_'.$i],
                    'DISCOUNT_PER'    => (!empty($request['DISCPER_'.$i])) == 'true' ? $request['DISCPER_'.$i] : 0,
                    'DISCOUNT_AMT' => (!empty($request['DISCOUNT_AMT_'.$i])) == 'true' ? $request['DISCOUNT_AMT_'.$i] : 0,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
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
                if(isset($request['UDFSOID_REF_'.$i]) && !is_null($request['UDFSOID_REF_'.$i]))
                {
                    $reqdata3[$i] = [
                        'UDFSSIID_REF'   => $request['UDFSOID_REF_'.$i],
                        'VALUE'      => $request['udfvalue_'.$i],
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

            $SSI_NO = $request['SONO'];
            $SSI_DT = $request['SODT'];
            $GLID_REF = $request['GLID_REF'];
            $SLID_REF = $request['SLID_REF'];        
            $BILLTO = $request['BILLTO'];
            $SHIPTO = $request['SHIPTO'];
            $CREDIT_DAYS = $request['CREDITDAYS'];
            $DUE_DT = $request['CUSTOMERDT'];

            $GST_N_Avail = (isset($request['GST_N_Avail'])!="true" ? 0 : 1);
            $GST_Reverse = (isset($request['GST_Reverse'])!="true" ? 0 : 1);
            $EXE_GST = (isset($request['EXE_GST'])!="true" ? 0 : 1);
            $Template_Description = $request['Template_Description'];
            $TDS   = (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);

            $FC = (isset($request['FC'])!="true" ? 0 : 1);
            $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
            $CONVFACT = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";

            $log_data = [ 
                $SSI_NO,$SSI_DT,$GLID_REF,$SLID_REF,$BILLTO,$SHIPTO,$CREDIT_DAYS,$DUE_DT,$CYID_REF, $BRID_REF,
                $FYID_REF,$VTID_REF,$XMLMAT, $XMLTNC,$XMLUDF,$XMLCAL,$XMLPSLB, $USERID, Date('Y-m-d'), Date('h:i:s.u'),
                $ACTIONNAME,$IPADDRESS,$GST_N_Avail,$GST_Reverse,$EXE_GST,$Template_Description,$XMLTDSD,$TDS
                ,$FC,$CRID_REF,$CONVFACT,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT
            ];
     
            $sp_result = DB::select('EXEC SP_SSI_IN ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?, ?,?, ?,?,?,?,?, ?,?,?,?,?,? ', $log_data);     
            
        
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
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
            $TABLE1        =   "TBL_TRN_SLSI02_HDR";
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

            $objSO = DB::table('TBL_TRN_SLSI02_HDR')
            ->where('TBL_TRN_SLSI02_HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_SLSI02_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_SLSI02_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_SLSI02_HDR.SSIID','=',$id)
            ->leftJoin('TBL_MST_SUBLEDGER','TBL_TRN_SLSI02_HDR.SGLID_REF','=','TBL_MST_SUBLEDGER.SGLID')  
            ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_SLSI02_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
            ->select(
                'TBL_TRN_SLSI02_HDR.*',
                'TBL_MST_SUBLEDGER.SGLCODE',
                'TBL_MST_SUBLEDGER.SLNAME'
                ,'TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE'
                )
            ->first();

            $objSOMAT=[];
            if(isset($objSO) && !empty($objSO)){
            $objSOMAT = DB::table('TBL_TRN_SLSI02_MAT')  
            ->leftJoin('TBL_TRN_SLSO04_HDR', 'TBL_TRN_SLSI02_MAT.SSOID_REF','=','TBL_TRN_SLSO04_HDR.SSOID')                  
            ->leftJoin('TBL_MST_ITEM','TBL_TRN_SLSI02_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')      
            ->leftJoin('TBL_MST_UOM','TBL_TRN_SLSI02_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')        

            
            ->where('TBL_TRN_SLSI02_MAT.SSIID_REF','=',$id)
            ->select(
                'TBL_TRN_SLSI02_MAT.*',
                'TBL_TRN_SLSO04_HDR.SSO_NO',
                'TBL_TRN_SLSO04_HDR.SSO_DT',
                'TBL_MST_ITEM.ITEMID',
                'TBL_MST_ITEM.ICODE',
                'TBL_MST_ITEM.NAME',
                'TBL_MST_UOM.UOMID',
                'TBL_MST_UOM.UOMCODE',
                'TBL_MST_UOM.DESCRIPTIONS'
                )
            ->orderBy('TBL_TRN_SLSI02_MAT.SSI_MATID','ASC')
            ->get()->toArray();

            }

            $objCount1 = count($objSOMAT);
            
            $objSOTNC = DB::table('TBL_TRN_SLSI02_TNC')                    
            ->where('TBL_TRN_SLSI02_TNC.SSIID_REF','=',$id)
            ->select('TBL_TRN_SLSI02_TNC.*')
            ->orderBy('TBL_TRN_SLSI02_TNC.SSI_TNCID','ASC')
            ->get()->toArray();
            $objCount2 = count($objSOTNC);

            $objSOUDF = DB::table('TBL_TRN_SLSI02_UDF')                    
            ->where('TBL_TRN_SLSI02_UDF.SSIID_REF','=',$id)
            ->select('TBL_TRN_SLSI02_UDF.*')
            ->orderBy('TBL_TRN_SLSI02_UDF.SSI_UDFID','ASC')
            ->get()->toArray();
            $objCount3 = count($objSOUDF);

            $objSOCAL = DB::table('TBL_TRN_SLSI02_CAL')                    
            ->where('TBL_TRN_SLSI02_CAL.SSIID_REF','=',$id)
            ->select('TBL_TRN_SLSI02_CAL.*')
            ->orderBy('TBL_TRN_SLSI02_CAL.SSI_CALID','ASC')
            ->get()->toArray();
            $objCount4 = count($objSOCAL);

            $objSOPSLB = DB::table('TBL_TRN_SLSI02_PSLB')                    
            ->where('TBL_TRN_SLSI02_PSLB.SSIID_REF','=',$id)
            ->select('TBL_TRN_SLSI02_PSLB.*')
            ->orderBy('TBL_TRN_SLSI02_PSLB.SSI_PSLBID','ASC')
            ->get()->toArray();
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


                            if(isset($objSO->SHIPTO_REF) && $objSO->SHIPTO_REF !=""){
                             $sid = $objSO->SHIPTO_REF;
         
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

                            if(isset($objSO->BILLTO_REF) && $objSO->BILLTO_REF !=""){      
                            
                            $bid = $objSO->BILLTO_REF;
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
                           
            

            $objEMP=array();
            $objSPID=NULL;

     

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
            
            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_SSI")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('UDFSSIID')->from('TBL_MST_UDFFOR_SSI')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                        
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                        
                    
    
            $objUdfSOData = DB::table('TBL_MST_UDFFOR_SSI')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_SSI")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDFSSIID')->from('TBL_MST_UDFFOR_SSI')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                            
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                            
            $objUdfSOData2 = DB::table('TBL_MST_UDFFOR_SSI')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
    
        
   
            $objSalesPerson = DB::table('TBL_MST_EMPLOYEE')
            ->where('STATUS','=',$Status)
            ->where('SALES_PERSON','=','1')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->select('TBL_MST_EMPLOYEE.*')
            ->get()
            ->toArray();
    
            $ObjSalesQuotationData = DB::table("TBL_TRN_SLQT01_HDR")->select('*')
                        ->where('STATUS','=','A')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF)
                        ->where('FYID_REF','=',$FYID_REF) ->get() ->toArray();                    
                    
            $objSalesQuotationAData = DB::table('TBL_TRN_SLQT02_HDR')->select('*')
                ->where('STATUS','=','A')
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->where('FYID_REF','=',$FYID_REF)
                ->get() ->toArray();  
            
               
          $objSQMAT = DB::table('TBL_TRN_SLQT01_MAT')->select('*')->get() ->toArray();
            
            $objUOM=array();
            $objItemUOMConv=array();

          

            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')->get() ->toArray(); 

            $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')->get() ->toArray(); 
        
            $AlpsStatus =   $this->AlpsStatus();
            $InputStatus =   "";
            $log_data = [ 
                $id
            ];

            $objSSITDS = DB::select('EXEC SP_GET_SSI_TDS ?', $log_data);
            //dd($objSSITDS); 
                   
            $objCount6 = count($objSSITDS);
            $objTemplateMaster  =$this->getTemplateMaster("SALES");
        

            $Template = DB::table('TBL_TRN_SLSI02_ADD_INFO')
            ->where('SSIID_REF','=',$id)
            ->select('TBL_TRN_SLSI02_ADD_INFO.TEMPLATE')
            ->first();

            $lastdt=$this->LastApprovedDocDate(); 

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            $objothcurrency = $this->GetCurrencyMaster(); 
        

        return view('transactions.sales.SalesServiceInvoice.trnfrm156edit',compact(['objSO','objRights','objCount1','objSPID',
           'objCount2','objCount3','objCount4','objCount5','objSOMAT','objSOCAL','objSOTNC','objSOUDF','objSOPSLB',
           'objCalculationHeader','objUdfSOData','objTNCHeader','objothcurrency',
           'objSalesPerson','objSalesQuotationAData','ObjSalesQuotationData',
           'objShpAddress','objBillAddress','objSQMAT','objUOM','objItemUOMConv','objTNCDetails','objUdfSOData2',
           'objCalHeader','objCalDetails','TAXSTATE','AlpsStatus','InputStatus','objSSITDS','objCount6',
           'objTemplateMaster','Template','lastdt','TabSetting']));
        }
     
       }



    public function view($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){


            $objSO = DB::table('TBL_TRN_SLSI02_HDR')
            ->where('TBL_TRN_SLSI02_HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_SLSI02_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_SLSI02_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_SLSI02_HDR.SSIID','=',$id)
            ->leftJoin('TBL_MST_SUBLEDGER','TBL_TRN_SLSI02_HDR.SGLID_REF','=','TBL_MST_SUBLEDGER.SGLID')  
            ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_SLSI02_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
            ->select(
                'TBL_TRN_SLSI02_HDR.*',
                'TBL_MST_SUBLEDGER.SGLCODE',
                'TBL_MST_SUBLEDGER.SLNAME'
                ,'TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE'
                )
            ->first();

            $objSOMAT=[];
            if(isset($objSO) && !empty($objSO)){
            $objSOMAT = DB::table('TBL_TRN_SLSI02_MAT')  
            ->leftJoin('TBL_TRN_SLSO04_HDR', 'TBL_TRN_SLSI02_MAT.SSOID_REF','=','TBL_TRN_SLSO04_HDR.SSOID')                  
            ->leftJoin('TBL_MST_ITEM','TBL_TRN_SLSI02_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')      
            ->leftJoin('TBL_MST_UOM','TBL_TRN_SLSI02_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')        

            
            ->where('TBL_TRN_SLSI02_MAT.SSIID_REF','=',$id)
            ->select(
                'TBL_TRN_SLSI02_MAT.*',
                'TBL_TRN_SLSO04_HDR.SSO_NO',
                'TBL_TRN_SLSO04_HDR.SSO_DT',
                'TBL_MST_ITEM.ITEMID',
                'TBL_MST_ITEM.ICODE',
                'TBL_MST_ITEM.NAME',
                'TBL_MST_UOM.UOMID',
                'TBL_MST_UOM.UOMCODE',
                'TBL_MST_UOM.DESCRIPTIONS'
                )
            ->orderBy('TBL_TRN_SLSI02_MAT.SSI_MATID','ASC')
            ->get()->toArray();

            }

            $objCount1 = count($objSOMAT);
            
            $objSOTNC = DB::table('TBL_TRN_SLSI02_TNC')                    
            ->where('TBL_TRN_SLSI02_TNC.SSIID_REF','=',$id)
            ->select('TBL_TRN_SLSI02_TNC.*')
            ->orderBy('TBL_TRN_SLSI02_TNC.SSI_TNCID','ASC')
            ->get()->toArray();
            $objCount2 = count($objSOTNC);

            $objSOUDF = DB::table('TBL_TRN_SLSI02_UDF')                    
            ->where('TBL_TRN_SLSI02_UDF.SSIID_REF','=',$id)
            ->select('TBL_TRN_SLSI02_UDF.*')
            ->orderBy('TBL_TRN_SLSI02_UDF.SSI_UDFID','ASC')
            ->get()->toArray();
            $objCount3 = count($objSOUDF);

            $objSOCAL = DB::table('TBL_TRN_SLSI02_CAL')                    
            ->where('TBL_TRN_SLSI02_CAL.SSIID_REF','=',$id)
            ->select('TBL_TRN_SLSI02_CAL.*')
            ->orderBy('TBL_TRN_SLSI02_CAL.SSI_CALID','ASC')
            ->get()->toArray();
            $objCount4 = count($objSOCAL);

            $objSOPSLB = DB::table('TBL_TRN_SLSI02_PSLB')                    
            ->where('TBL_TRN_SLSI02_PSLB.SSIID_REF','=',$id)
            ->select('TBL_TRN_SLSI02_PSLB.*')
            ->orderBy('TBL_TRN_SLSI02_PSLB.SSI_PSLBID','ASC')
            ->get()->toArray();
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


                            if(isset($objSO->SHIPTO_REF) && $objSO->SHIPTO_REF !=""){
                             $sid = $objSO->SHIPTO_REF;
         
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

                            if(isset($objSO->BILLTO_REF) && $objSO->BILLTO_REF !=""){      
                            
                            $bid = $objSO->BILLTO_REF;
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
                           
            

            $objEMP=array();
            $objSPID=NULL;


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
            
            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_SSI")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('UDFSSIID')->from('TBL_MST_UDFFOR_SSI')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                        
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                        
                    
    
            $objUdfSOData = DB::table('TBL_MST_UDFFOR_SSI')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_SSI")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDFSSIID')->from('TBL_MST_UDFFOR_SSI')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                            
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                            
            $objUdfSOData2 = DB::table('TBL_MST_UDFFOR_SSI')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
    
        
    

    
            $objSalesPerson = DB::table('TBL_MST_EMPLOYEE')
            ->where('STATUS','=',$Status)
            ->where('SALES_PERSON','=','1')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->select('TBL_MST_EMPLOYEE.*')
            ->get()
            ->toArray();
    
            $ObjSalesQuotationData = DB::table("TBL_TRN_SLQT01_HDR")->select('*')
                        ->where('STATUS','=','A')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF)
                        ->where('FYID_REF','=',$FYID_REF) ->get() ->toArray();                    
                    
            $objSalesQuotationAData = DB::table('TBL_TRN_SLQT02_HDR')->select('*')
                ->where('STATUS','=','A')
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->where('FYID_REF','=',$FYID_REF)
                ->get() ->toArray();  
            
               
          $objSQMAT = DB::table('TBL_TRN_SLQT01_MAT')->select('*')->get() ->toArray();
            
            $objUOM=array();
            $objItemUOMConv=array();

          

            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')->get() ->toArray(); 

            $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')->get() ->toArray(); 
        
            $AlpsStatus =   $this->AlpsStatus();
            $InputStatus =   "disabled";
            $log_data = [ 
                $id
            ];

            $objSSITDS = DB::select('EXEC SP_GET_SSI_TDS ?', $log_data);
            //dd($objSSITDS); 
                   
            $objCount6 = count($objSSITDS);
            $objTemplateMaster  =$this->getTemplateMaster("SALES");
        

            $Template = DB::table('TBL_TRN_SLSI02_ADD_INFO')
            ->where('SSIID_REF','=',$id)
            ->select('TBL_TRN_SLSI02_ADD_INFO.TEMPLATE')
            ->first();

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            $objothcurrency = $this->GetCurrencyMaster(); 
        

        return view('transactions.sales.SalesServiceInvoice.trnfrm156view',compact(['objSO','objRights','objCount1','objSPID',
           'objCount2','objCount3','objCount4','objCount5','objSOMAT','objSOCAL','objSOTNC','objSOUDF','objSOPSLB',
           'objCalculationHeader','objUdfSOData','objTNCHeader','objothcurrency',
           'objSalesPerson','objSalesQuotationAData','ObjSalesQuotationData',
           'objShpAddress','objBillAddress','objSQMAT','objUOM','objItemUOMConv','objTNCDetails','objUdfSOData2',
           'objCalHeader','objCalDetails','TAXSTATE','AlpsStatus','InputStatus','objSSITDS','objCount6',
           'objTemplateMaster','Template','TabSetting']));
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
    $NET_TOTAL 		= 	$request['TotalValue'];
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
                    'SSI_MATID'    => $request['SOMATID_'.$i],
                    'SSOID_REF'    => $request['SQA_'.$i],
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'REMARKS' => $request['REMARKS_'.$i],
                    'SSI_QTY' => $request['SO_QTY_'.$i],
                    'RATE_PRUOM' => $request['RATEPUOM_'.$i],
                    'DISCOUNT_PER'    => (!empty($request['DISCPER_'.$i])) == 'true' ? $request['DISCPER_'.$i] : 0,
                    'DISCOUNT_AMT' => (!empty($request['DISCOUNT_AMT_'.$i])) == 'true' ? $request['DISCOUNT_AMT_'.$i] : 0,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
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
                        'SSI_TNCID'    => $request['SSI_TNCID_'.$i],
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
                        'SSI_UDFID'    => $request['SSI_UDFID_'.$i],
                        'UDFSSIID_REF'   => $request['UDFSOID_REF_'.$i],
                        'VALUE'      => $request['udfvalue_'.$i],
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
                        if(isset($request['CT_TYPE_'.$i]) && $request['CT_TYPE_'.$i]=="DISCOUNT"){
                          
                            $DISCOUNT      += $request['VALUE_'.$i]; 
                        }else{
                            $OTHER_CHARGES += $request['VALUE_'.$i];   
                        }

                        $reqdata4[$i] = [
                            'SSI_CALID'    => $request['SSI_CALID_'.$i],
                            'CTID_REF'      => $request['CTID_REF'] ,
                            'TID_REF'       => $request['TID_REF_'.$i],
                            'RATE'          => $request['RATE_'.$i],
                            'VALUE'         => $request['VALUE_'.$i],
                            'GST'           => (isset($request['calGST_'.$i])!="true" ? 0 : 1) ,
                            'IGST'          => (isset($request['calIGST_'.$i]) && !empty($request['calIGST_'.$i]) ? $request['calIGST_'.$i] : 0),
                            'CGST'          => (isset($request['calCGST_'.$i]) && !empty($request['calCGST_'.$i]) ? $request['calCGST_'.$i] : 0),
                            'SGST'          => (isset($request['calSGST_'.$i]) && !empty($request['calSGST_'.$i]) ? $request['calSGST_'.$i] : 0),
                            
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
                        'SSI_PSLBID'    => $request['SSI_PSLBID_'.$i],
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

        $SSI_NO = $request['SONO'];
        $SSI_DT = $request['SODT'];
        $GLID_REF = $request['GLID_REF'];
        $SLID_REF = $request['SLID_REF'];        
        $BILLTO = $request['BILLTO'];
        $SHIPTO = $request['SHIPTO'];
        $CREDIT_DAYS = $request['CREDITDAYS'];
        $DUE_DT = $request['CUSTOMERDT'];

        $GST_N_Avail = (isset($request['GST_N_Avail'])!="true" ? 0 : 1);
        $GST_Reverse = (isset($request['GST_Reverse'])!="true" ? 0 : 1);
        $EXE_GST = (isset($request['EXE_GST'])!="true" ? 0 : 1);
        $Template_Description = $request['Template_Description'];
        $TDS   = (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);

        $FC = (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";



        $log_data = [ 
            $SSI_NO,$SSI_DT,$GLID_REF,$SLID_REF,$BILLTO,$SHIPTO,$CREDIT_DAYS,$DUE_DT,$CYID_REF, $BRID_REF,
            $FYID_REF,$VTID_REF,$XMLMAT, $XMLTNC,$XMLUDF,$XMLCAL,$XMLPSLB, $USERID, Date('Y-m-d'), Date('h:i:s.u'),
            $ACTIONNAME,$IPADDRESS,$GST_N_Avail,$GST_Reverse,$EXE_GST,$Template_Description,$XMLTDSD,$TDS
            ,$FC,$CRID_REF,$CONVFACT,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT
        ];
        
        $sp_result = DB::select('EXEC SP_SSI_UP ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?, ?,?, ?,?,?,?,?, ?,?,?,?,?,?', $log_data); 

    
        
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
            $r_count5 = $request['Row_Count5'];
            $r_count6 = $request['Row_Count6'];

            $GROSS_TOTAL    =   0; 
            $NET_TOTAL 		= 	$request['TotalValue'];
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
                        'SSI_MATID'    => $request['SOMATID_'.$i],
                        'SSOID_REF'    => $request['SQA_'.$i],
                        'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                        'UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                        'REMARKS' => $request['REMARKS_'.$i],
                        'SSI_QTY' => $request['SO_QTY_'.$i],
                        'RATE_PRUOM' => $request['RATEPUOM_'.$i],
                        'DISCOUNT_PER'    => (!empty($request['DISCPER_'.$i])) == 'true' ? $request['DISCPER_'.$i] : 0,
                        'DISCOUNT_AMT' => (!empty($request['DISCOUNT_AMT_'.$i])) == 'true' ? $request['DISCOUNT_AMT_'.$i] : 0,
                        'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                        'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                        'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
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
                            'SSI_TNCID'    => $request['SSI_TNCID_'.$i],
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
                            'SSI_UDFID'    => $request['SSI_UDFID_'.$i],
                            'UDFSSIID_REF'   => $request['UDFSOID_REF_'.$i],
                            'VALUE'      => $request['udfvalue_'.$i],
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
                            if(isset($request['CT_TYPE_'.$i]) && $request['CT_TYPE_'.$i]=="DISCOUNT"){
                          
                                $DISCOUNT      += $request['VALUE_'.$i]; 
                            }else{
                                $OTHER_CHARGES += $request['VALUE_'.$i];   
                            }

                            $reqdata4[$i] = [
                                'SSI_CALID'    => $request['SSI_CALID_'.$i],
                                'CTID_REF'      => $request['CTID_REF'] ,
                                'TID_REF'       => $request['TID_REF_'.$i],
                                'RATE'          => $request['RATE_'.$i],
                                'VALUE'         => $request['VALUE_'.$i],
                                'GST'           => (isset($request['calGST_'.$i])!="true" ? 0 : 1) ,
                                'IGST'          => (isset($request['calIGST_'.$i]) && !empty($request['calIGST_'.$i]) ? $request['calIGST_'.$i] : 0),
                                'CGST'          => (isset($request['calCGST_'.$i]) && !empty($request['calCGST_'.$i]) ? $request['calCGST_'.$i] : 0),
                                'SGST'          => (isset($request['calSGST_'.$i]) && !empty($request['calSGST_'.$i]) ? $request['calSGST_'.$i] : 0),
                                
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
                            'SSI_PSLBID'    => $request['SSI_PSLBID_'.$i],
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

                $SSI_NO = $request['SONO'];
                $SSI_DT = $request['SODT'];
                $GLID_REF = $request['GLID_REF'];
                $SLID_REF = $request['SLID_REF'];        
                $BILLTO = $request['BILLTO'];
                $SHIPTO = $request['SHIPTO'];
                $CREDIT_DAYS = $request['CREDITDAYS'];
                $DUE_DT = $request['CUSTOMERDT'];

                $GST_N_Avail = (isset($request['GST_N_Avail'])!="true" ? 0 : 1);
                $GST_Reverse = (isset($request['GST_Reverse'])!="true" ? 0 : 1);
                $EXE_GST = (isset($request['EXE_GST'])!="true" ? 0 : 1);
                $Template_Description = $request['Template_Description'];
                $TDS   = (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);

                $FC = (isset($request['FC'])!="true" ? 0 : 1);
                $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
                $CONVFACT = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
        
        
            

                $log_data = [ 
                    $SSI_NO,$SSI_DT,$GLID_REF,$SLID_REF,$BILLTO,$SHIPTO,$CREDIT_DAYS,$DUE_DT,$CYID_REF, $BRID_REF,
                    $FYID_REF,$VTID_REF,$XMLMAT, $XMLTNC,$XMLUDF,$XMLCAL,$XMLPSLB, $USERID, Date('Y-m-d'), Date('h:i:s.u'),
                    $ACTIONNAME,$IPADDRESS,$GST_N_Avail,$GST_Reverse,$EXE_GST,$Template_Description,$XMLTDSD,$TDS
                    ,$FC,$CRID_REF,$CONVFACT,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT
                ];

              //  dd($log_data); 
                
                $sp_result = DB::select('EXEC SP_SSI_UP ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,  ?,?,?,?, ?,?, ?,?,?,?,?, ?,?,?,?,?,?', $log_data); 
             
                
             
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
                $TABLE      =   "TBL_TRN_SLSI02_HDR";
                $FIELD      =   "SSIID";
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
        $TABLE      =   "TBL_TRN_SLSI02_HDR";
        $FIELD      =   "SSIID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_SLSI02_MAT',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_SLSI02_TNC',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_SLSI02_UDF',
        ];
        $req_data[3]=[
            'NT'  => 'TBL_TRN_SLSI02_CAL',
        ];
        $req_data[4]=[
            'NT'  => 'TBL_TRN_SLSI02_PSLB',
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
        
		$image_path         =   "docs/company".$CYID_REF."/SalesServiceInvoice";     
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
        
        $objSO = DB::table('TBL_TRN_SLSI02_HDR')
        ->where('TBL_TRN_SLSI02_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLSI02_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_SLSI02_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_SLSI02_HDR.SSI_NO','=',$SONO)
        ->select('TBL_TRN_SLSI02_HDR.SSIID')
        ->first();
        
        if($objSO){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate SONO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
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
    

    public function LastApprovedDocDate(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $VTID_REF     =   $this->vtid_ref;
        return $objlastDocDate = DB::select('SELECT MAX(SSI_DT) SSI_DT FROM TBL_TRN_SLSI02_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $VTID_REF, $Status ]);

    }




    // Clear Tax Invoice & EWAY Bill Section Starts Here-------------------

    public function invoice($id=NULL){ 

        if(!is_null($id)){

            $id             =   urldecode(base64_decode($id));
            $FormId         =   $this->form_id;
            $VTID_REF       =   $this->vtid_ref; 
            $InvoiceDetails =   array();

            $data           =   DB::table('TBL_TRN_SLSI01_IRN')
                                ->where('DOC_ID','=',$id)
                                ->where('DOC_TYPE','=',$VTID_REF)
                                ->orderBy('IRN_ID','DESC')
                                ->first();

            if(!empty($data)){
                $response       =	$data->RESPONSE;
                $InvoiceDetails =   json_decode($response)[0]; 
                $InvoiceDetails->document_status=$data->STATUS;
            }

            return view('transactions.sales.SalesServiceInvoice.trnfrm156invoice',compact(['FormId','id','InvoiceDetails']));
        }
     
    }

    public function GenerateIrn(Request $request){

        $VTID_REF           =   $this->vtid_ref;   
        $USERID             =   Auth::user()->USERID;  
        $INDATE             =   date('Y-m-d H:i:s');
        $id                 =   $request['id'];
        $responseJson       =   $this->ClearTaxApiGenerateIrn($id);
        $response           =   json_decode($responseJson,true)[0];
		//dd($response);
        $Success            =   isset($response['govt_response']['Success'])?$response['govt_response']['Success']:NULL;
        $AckNo              =   isset($response['govt_response']['AckNo'])?$response['govt_response']['AckNo']:NULL;
        $AckDt              =   isset($response['govt_response']['AckDt'])?$response['govt_response']['AckDt']:NULL;       
        $Irn                =   isset($response['govt_response']['Irn'])?$response['govt_response']['Irn']:NULL;
        $Status             =   isset($response['document_status'])?$response['document_status']:NULL;
    
        if($Success =="Y"){

            if(isset($response['govt_response']['info'])){
                echo ' <div class="alert alert-info">';
                foreach($response['govt_response']['info'] as $key=>$val){
                    $hr =   count($response['govt_response']['info']) > $key+1 ?'<hr/>':'';
                    echo '<strong>Info! ('.$val['InfCd'].') </strong> '.$val['Desc'].$hr;
                }
                echo '</div>';
            }
            else{

                DB::update("UPDATE TBL_TRN_SLSI02_HDR SET IRN_NO='$Irn' WHERE SSIID='$id'");
                DB::insert("INSERT INTO TBL_TRN_SLSI01_IRN (DOC_ID, CREATED_DATE, CREATED_BY, ACK_NO, STATUS,INDATE,DOC_TYPE,RESPONSE) VALUES ('$id', '$AckDt', '$USERID', '$AckNo', '$Status', '$INDATE','$VTID_REF','$responseJson')");

                echo'
                    <div class="alert alert-success">
                        <strong>Success!</strong> Your IRN has been generated successfully
                    </div>
                ';

            }

        }
        else{

            if(isset($response['govt_response']['ErrorDetails'])){
                echo ' <div class="alert alert-danger">';
                foreach($response['govt_response']['ErrorDetails'] as $key=>$val){
                    $hr =   count($response['govt_response']['ErrorDetails']) > $key+1 ?'<hr/>':'';
                    echo '<strong>Error! ('.$val['error_code'].') </strong> '.$val['error_message'].$hr;
                }
                echo '</div>';
            }

        }

        die;

    }

    public function ClearTaxApiGenerateIrn($id){
        
        $HDR                =   DB::table('TBL_TRN_SLSI02_HDR')
                                ->where('TBL_TRN_SLSI02_HDR.FYID_REF','=',Session::get('FYID_REF'))
                                ->where('TBL_TRN_SLSI02_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_TRN_SLSI02_HDR.BRID_REF','=',Session::get('BRID_REF'))
                                ->where('TBL_TRN_SLSI02_HDR.SSIID','=',$id)
                                ->select('TBL_TRN_SLSI02_HDR.*')
                                ->first();

                                //dd($HDR) ;

                               

        $MAT                =   DB::select("SELECT 
                                    T1.*,T2.ICODE,T2.NAME AS ITEM_NAME,T3.UOMCODE,T4.HSNCODE
                                    FROM TBL_TRN_SLSI02_MAT T1
                                    LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                                    LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                                    LEFT JOIN TBL_MST_HSN T4 ON T2.HSNID_REF=T4.HSNID
                                    WHERE T1.SSIID_REF='$id' ORDER BY T1.SSI_MATID ASC
                                ");


                            

                               
        $BRANCH             =   DB::table('TBL_MST_BRANCH')->where('BRID','=',Session::get('BRID_REF'))->first();
        $COMPANY            =   DB::table('TBL_MST_COMPANY')->where('CYID','=',Auth::user()->CYID_REF)->first();
        $CUSTOMER           =   DB::table('TBL_MST_CUSTOMER')->where('CYID_REF','=',Auth::user()->CYID_REF)
                               // ->where('BRID_REF','=',Session::get('BRID_REF'))
                                ->where('SLID_REF','=',$HDR->SGLID_REF)
                                ->first();
        
        $CITY               =   DB::table('TBL_MST_CITY')
                                //->where('CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('CITYID','=',$CUSTOMER->REGCITYID_REF)
                                ->first();

        $BILL_TO            =   DB::table('TBL_MST_CUSTOMERLOCATION')
                                ->where('CLID','=',$HDR->BILLTO_REF)
                                ->first();
                                

        $BILL_CITY          =   DB::table('TBL_MST_CITY')
                                //->where('CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('CITYID','=',$BILL_TO->CITYID_REF)
                                ->first();

        $DocDtls_No         =   isset($HDR->SSI_NO) && $HDR->SSI_NO !=''?$HDR->SSI_NO:NULL;
        $DocDtls_Dt         =   isset($HDR->SSI_DT) && $HDR->SSI_DT !=''?date('d/m/Y',strtotime($HDR->SSI_DT)):NULL;

        $SellerDtls_Gstin   =   isset($BRANCH->GSTINNO) && $BRANCH->GSTINNO !=''?$BRANCH->GSTINNO:NULL;
        $SellerDtls_LglNm   =   isset($COMPANY->NAME) && $COMPANY->NAME !=''?$COMPANY->NAME:NULL;
        $SellerDtls_Addr1   =   isset($BRANCH->ADDL1) && $BRANCH->ADDL1 !=''?$BRANCH->ADDL1:NULL;
        $SellerDtls_Addr2   =   isset($BRANCH->ADDL2) && $BRANCH->ADDL2 !=''?$BRANCH->ADDL2:NULL;
        $SellerDtls_Loc     =   isset($BRANCH->BRNAME) && $BRANCH->BRNAME !=''?$BRANCH->BRNAME:NULL;
        $SellerDtls_Pin     =   isset($BRANCH->PINCODE) && $BRANCH->PINCODE !=''?$BRANCH->PINCODE:NULL;
        $SellerDtls_Stcd    =   $SellerDtls_Gstin !=''?substr($SellerDtls_Gstin, 0, 2):NULL;
        $SellerDtls_Ph      =   isset($BRANCH->MONO) && $BRANCH->MONO !=''?str_replace(' ', '', $BRANCH->MONO):NULL;
        $SellerDtls_Em      =   isset($BRANCH->EMAILID) && $BRANCH->EMAILID !=''?$BRANCH->EMAILID:NULL;

        $BuyerDtls_Gstin    =   isset($CUSTOMER->GSTIN) && $CUSTOMER->GSTIN !=''?$CUSTOMER->GSTIN:NULL;
        $BuyerDtls_LglNm    =   isset($CUSTOMER->NAME) && $CUSTOMER->NAME !=''?$CUSTOMER->NAME:NULL;
        $BuyerDtls_Addr1    =   isset($CUSTOMER->REGADDL1) && $CUSTOMER->REGADDL1 !=''?$CUSTOMER->REGADDL1:NULL;
        $BuyerDtls_Addr2    =   isset($CUSTOMER->REGADDL2) && $CUSTOMER->REGADDL2 !=''?$CUSTOMER->REGADDL2:NULL;
        $BuyerDtls_Loc      =   isset($CITY->NAME) && $CITY->NAME !=''?$CITY->NAME:NULL;
        $BuyerDtls_Pin      =   isset($CUSTOMER->REGPIN) && $CUSTOMER->REGPIN !=''?$CUSTOMER->REGPIN:NULL;
        $BuyerDtls_Stcd     =   $BuyerDtls_Gstin !=''?substr($BuyerDtls_Gstin, 0, 2):NULL;
        $BuyerDtls_Ph       =   isset($CUSTOMER->MONO) && $CUSTOMER->MONO !=''?str_replace(' ', '', $CUSTOMER->MONO):NULL;
        $BuyerDtls_Em       =   isset($CUSTOMER->EMAILID) && $CUSTOMER->EMAILID !=''?$CUSTOMER->EMAILID:NULL;

        $ShipDtls_Gstin     =   isset($BILL_TO->GSTIN) && $BILL_TO->GSTIN !=''?$BILL_TO->GSTIN:NULL;
        $ShipDtls_LglNm     =   isset($BILL_TO->NAME) && $BILL_TO->NAME !=''?$BILL_TO->NAME:NULL;
        $ShipDtls_Addr1     =   isset($BILL_TO->CADD) && $BILL_TO->CADD !=''?$BILL_TO->CADD:NULL;
        $ShipDtls_Loc       =   isset($BILL_CITY->NAME) && $BILL_CITY->NAME !=''?$BILL_CITY->NAME:NULL;
        $ShipDtls_Pin       =   isset($BILL_TO->PIN) && $BILL_TO->PIN !=''?$BILL_TO->PIN:NULL;
        $ShipDtls_Stcd      =   $ShipDtls_Gstin !=''?substr($ShipDtls_Gstin, 0, 2):NULL;


        $TOTAL_MAT_AMT      =   $this->getTotalMaterialAmount($id);
        $TOTAL_CAL_AMT      =   $this->getTotalCalculationAmount($id);
        $TOTAL_TDS_AMT      =   $this->getTotalTdsAmount($id);
        $TOTAL_AMOUNT       =   ($TOTAL_MAT_AMT+$TOTAL_CAL_AMT)-$TOTAL_TDS_AMT;
       
        $ItemList           =   array();
        $TOTAL_DISC_AMOUNT  =   0;
        $TOTAL_IGST_AMOUNT  =   0;
        $TOTAL_CGST_AMOUNT  =   0;
        $TOTAL_SGST_AMOUNT  =   0;
        $TOTAL_ASSE_AMOUNT  =    0;
        $TOTAL_OTHE_CHARGE  =    $TOTAL_CAL_AMT;
        
        foreach($MAT as $key=>$val){
            $SlNo               =   $val->SSI_MATID;
            $PrdDesc            =   $val->ITEM_NAME;
            $HsnCd              =   $val->HSNCODE;
            $Qty                =   $val->SSI_QTY;
            $Unit               =   $val->UOMCODE;
            $UnitPrice          =   $val->RATE_PRUOM;
            $TotAmt             =   floatval($Qty)*floatval($UnitPrice);
            $Discount           =   floatval($val->DISCOUNT_AMT);
            $GstRt              =   floatval($val->IGST)+floatval($val->CGST)+floatval($val->SGST);
            $AssAmt             =   $TotAmt - $Discount; 

            $IGST               =   $val->IGST !=""?floatval($val->IGST):0;
            $CGST               =   $val->CGST !=""?floatval($val->CGST):0;
            $SGST               =   $val->SGST !=""?floatval($val->SGST):0;

            $IGST_AMOUNT        =   ($AssAmt*$IGST)/100;
            $CGST_AMOUNT        =   ($AssAmt*$CGST)/100;
            $SGST_AMOUNT        =   ($AssAmt*$SGST)/100;

            $TOTAL_TAX_AMOUNT   =   ($IGST_AMOUNT+$CGST_AMOUNT+$SGST_AMOUNT);
            $TOTAL_ITEM_VALUE   =   $AssAmt+$TOTAL_TAX_AMOUNT;

            $TOTAL_DISC_AMOUNT  =   $TOTAL_DISC_AMOUNT+$Discount;
            $TOTAL_IGST_AMOUNT  =   $TOTAL_IGST_AMOUNT+$IGST_AMOUNT;
            $TOTAL_CGST_AMOUNT  =   $TOTAL_CGST_AMOUNT+$CGST_AMOUNT;
            $TOTAL_SGST_AMOUNT  =   $TOTAL_SGST_AMOUNT+$SGST_AMOUNT;
            $TOTAL_ASSE_AMOUNT  =   $TOTAL_ASSE_AMOUNT+$AssAmt;

            $ItemList[]= array(
                "SlNo"          => $key+1,
                "PrdDesc"       => $PrdDesc,
                "IsServc"       => "Y",
                "HsnCd"         => $HsnCd,
                "Barcde"        => NULL,
                "Qty"           => $Qty,
                "FreeQty"       => NULL,
                "Unit"          => $Unit,
                "UnitPrice"     => $UnitPrice,
                "TotAmt"        => $TotAmt,
                "Discount"      => $Discount,
                "PreTaxVal"     => NULL,
                "AssAmt"        => $AssAmt, 
                "GstRt"         => $GstRt,
                "IgstAmt"       => $IGST_AMOUNT,
                "CgstAmt"       => $CGST_AMOUNT,
                "SgstAmt"       => $SGST_AMOUNT,
                "CesRt"         => NULL,
                "CesAmt"        => NULL,
                "CesNonAdvlAmt" => NULL,
                "StateCesRt"    => NULL,
                "StateCesAmt"   => NULL,
                "StateCesNonAdvlAmt"=> NULL,
                "OthChrg"       => NULL,
                "TotItemVal"    => $TOTAL_ITEM_VALUE,
                "OrdLineRef"    => NULL,
                "OrgCntry"      => NULL,
                "PrdSlNo"       => NULL,
                "BchDtls"       => array(
                    "Nm"        => NULL,
                    "ExpDt"     => NULL,
                    "WrDt"      => NULL
                ),
                "AttribDtls"    => [
                    array(
                        "Nm"    => NULL,
                        "Val"   => NULL
                    )
                ]
            );
        }

        $data[]=array(
            "transaction"           => array(
                "Version"           => "1.1",
                "TranDtls"          => array(
                    "TaxSch"        => "GST",
                    "SupTyp"        => "B2B",
                    "RegRev"        => isset($HDR->REVERSE_GST) && $HDR->REVERSE_GST==1 ? "Y":"N" ,
                    "EcmGstin"      => NULL,
                    "IgstOnIntra"   => "N"
                ),
                "DocDtls"           => array(
                    "Typ"           => "INV",
                    "No"            => $DocDtls_No,
                    "Dt"            => $DocDtls_Dt
                ),
                "SellerDtls"        => array(
                    "Gstin"         => $SellerDtls_Gstin,
                    "LglNm"         => $SellerDtls_LglNm,
                    "TrdNm"         => $SellerDtls_LglNm,
                    "Addr1"         => $SellerDtls_Addr1,
                    "Addr2"         => $SellerDtls_Addr2,
                    "Loc"           => $SellerDtls_Loc,
                    "Pin"           => $SellerDtls_Pin,
                    "Stcd"          => $SellerDtls_Stcd,
                    "Ph"            => $SellerDtls_Ph,
                    "Em"            => $SellerDtls_Em
                ),
                "BuyerDtls" => array(
                    "Gstin"     => $BuyerDtls_Gstin,
                    "LglNm"     => $BuyerDtls_LglNm,
                    "TrdNm"     => $BuyerDtls_LglNm,
                    "Pos"       => 12,
                    "Addr1"     => $BuyerDtls_Addr1,
                    "Addr2"     => $BuyerDtls_Addr2,
                    "Loc"       => $BuyerDtls_Loc,
                    "Pin"       => $BuyerDtls_Pin,
                    "Stcd"      => $BuyerDtls_Stcd,
                    "Ph"        => $BuyerDtls_Ph,
                    "Em"        => $BuyerDtls_Em
                ),
                "DispDtls"      => array(
                    "Nm"        => $SellerDtls_LglNm,
                    "Addr1"     => $SellerDtls_Addr1,
                    "Addr2"     => $SellerDtls_Addr2,
                    "Loc"       => $SellerDtls_Loc,
                    "Pin"       => $SellerDtls_Pin,
                    "Stcd"      => $SellerDtls_Stcd
                ),
                "ShipDtls"=> array(
                    "Gstin"     => $ShipDtls_Gstin,
                    "LglNm"     => $ShipDtls_LglNm,
                    "TrdNm"     => $ShipDtls_LglNm,
                    "Addr1"     => $ShipDtls_Addr1,
                    "Addr2"     => NULL,
                    "Loc"       => $ShipDtls_Loc,
                    "Pin"       => $ShipDtls_Pin,
                    "Stcd"      => $ShipDtls_Stcd
                ),

                "ItemList"=> $ItemList,
                "ValDtls"=> array(
                    "AssVal"            => $TOTAL_ASSE_AMOUNT,
                    "CgstVal"           => $TOTAL_CGST_AMOUNT,
                    "SgstVal"           => $TOTAL_SGST_AMOUNT,
                    "IgstVal"           => $TOTAL_IGST_AMOUNT,
                    "CesVal"            => NULL,
                    "StCesVal"          => NULL,
                    "Discount"          => $TOTAL_DISC_AMOUNT,
                    "OthChrg"           => $TOTAL_OTHE_CHARGE,
                    "RndOffAmt"         => NULL,
                    "TotInvVal"         => $TOTAL_AMOUNT,
                    "TotInvValFc"       => NULL
                ),
                "PayDtls"=> array(
                    "Nm"            => NULL,
                    "AccDet"        => NULL,
                    "Mode"          => NULL,
                    "FinInsBr"      => NULL,
                    "PayTerm"       => NULL,
                    "PayInstr"      => NULL,
                    "CrTrn"         => NULL,
                    "DirDr"         => NULL,
                    "CrDay"         => NULL,
                    "PaidAmt"       => NULL,
                    "PaymtDue"      => NULL
                ),
                "RefDtls"=> array(
                    "InvRm"         => NULL,
                    "DocPerdDtls"   => array(
                        "InvStDt"   => NULL,
                        "InvEndDt"  => NULL
                    ),
                    "PrecDocDtls"   => [
                        array(
                            "InvNo"     => NULL,
                            "InvDt"     => NULL,
                            "OthRefNo"  => NULL
                        )
                    ],
                    "ContrDtls"=> [
                        array(
                            "RecAdvRefr"=> NULL,
                            "RecAdvDt"  => NULL,
                            "TendRefr"  => NULL,
                            "ContrRefr" => NULL,
                            "ExtRefr"   => NULL,
                            "ProjRefr"  => NULL,
                            "PORefr"    => NULL,
                            "PORefDt"   => NULL
                        )
                    ]
                ),
                "AddlDocDtls"=> [
                    array(
                        "Url"       => NULL,
                        "Docs"      => NULL,
                        "Info"      => NULL
                    )
                ],
                "ExpDtls"=> array(
                    "ShipBNo"       => NULL,
                    "ShipBDt"       => NULL,
                    "Port"          => NULL,
                    "RefClm"        => NULL,
                    "ForCur"        => NULL,
                    "CntCode"       => NULL
                ),
                "EwbDtls"=> array(
                    "TransId"       => NULL,
                    "TransName"     => NULL,
                    "Distance"      => NULL,
                    "TransDocNo"    => NULL,
                    "TransDocDt"    => NULL,
                    "VehNo"         => NULL,
                    "VehType"       => NULL,
                    "TransMode"     => NULL 
                )
            ),
    
            "custom_fields"=> array(
                "customfieldLable1"     => NULL,
                "customfieldLable2"     => NULL,
                "customfieldLable3"     => NULL
            )
    
        );
 
        $data       =   json_encode($data,JSON_UNESCAPED_SLASHES);        
        $response   =	ClearTaxApi::GenerateIrn($data);
        return $response;

    }

    public function cancelIRN(Request $request){
        $id        =    $request['id'];
        $IRN_No    =    NULL;   
        if($id){
            $IRN_No = DB::table('TBL_TRN_SLSI02_HDR')
            ->where('TBL_TRN_SLSI02_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_SLSI02_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_SLSI02_HDR.SSIID','=',$id)
            ->select('IRN_No')
            ->first();
        }
        
        $IRN_No = isset($IRN_No->IRN_No) && $IRN_No->IRN_No !=''? $IRN_No->IRN_No:''; 
        if($IRN_No ==""){
            return Response::json(['errors'=>true,'msg' => "Record not found.",'norecord'=>'norecord']);
        }
        
        $USERID_REF     =   Auth::user()->USERID;
        $VTID_REF       =   $this->vtid_ref; 
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF'); 

        $response       =	ClearTaxApi::CancelIRN($IRN_No);
        $response_data  =   json_decode($response)[0];
 
        if(isset($response_data->document_status) && $response_data->document_status=="IRN_CANCELLED"){

            $data   =   DB::table('TBL_TRN_SLSI01_IRN')
                        ->where('DOC_ID','=',$id)
                        ->where('DOC_TYPE','=',$VTID_REF)
                        ->orderBy('IRN_ID','DESC')
                        ->first();

            DB::table('TBL_TRN_SLSI01_IRN')
            ->where('IRN_ID', $data->IRN_ID)                
            ->update(["MODIFIED_DATE"=>Date('Y-m-d'),"STATUS"=>"IRN_CANCELLED","MODIFIED_BY"=>$USERID_REF]);
            
            return Response::json(['cancel' =>true,'msg' => 'IRN has been cancelled successfully']);

        }
        else if(isset($response_data->document_status) && $response_data->document_status=="IRN_CANCELLATION_FAILED"){
            return Response::json(['errors'=>true,'msg' => isset($response_data->govt_response->ErrorDetails[0]->error_message) ? $response_data->govt_response->ErrorDetails[0]->error_message :"",'norecord'=>'norecord']);
        }
        else{
            return Response::json(['errors'=>true,'msg' => "An error has been occurred.",'norecord'=>'norecord']);
        }

        exit(); 
    }

    
    public function PrintIrn($id=NULL){
        $response   =	ClearTaxApi::PrintIrn($id);
        header('Cache-Control: public'); 
        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="INVOICE'.date('YmdHis').'.pdf"');
        header('Content-Length: '.strlen($response));
        echo $response;
        
    }

    public function SendInvoice(Request $request){

        $data   =   array(
                        "DOC_NO"=>$request['DOC_NO'],
                        "DOC_DT"=>$request['DOC_DT'],
                        "BUYER_NAME"=>$request['BUYER_NAME'],
                        "EMAIL"=>$request['EMAIL']
                        
                    ); 

        $response       =	ClearTaxApi::SendInvoice($data);


        $response_data  =   json_decode($response);

        if(isset($response_data->success) && $response_data->success==true){
            return Response::json(['sent' =>true,'msg' => 'Email has been sent successfully.']);
        }
        else if(isset($response_data->success) && $response_data->success==false){
            return Response::json(['errors'=>true,'msg' => isset($response_data->errors->error_message) ? $response_data->errors->error_message :"",'norecord'=>'norecord']);
        }
        else{
            return Response::json(['errors'=>true,'msg' => "An error has been occurred.",'norecord'=>'norecord']);
        }

        exit(); 
    }


   
public function ewaybill($id=NULL){ 
    if(!is_null($id)){

        $id             =   urldecode(base64_decode($id));
        $FormId         =   $this->form_id;
        $VTID_REF       =   $this->vtid_ref; 
        $InvoiceDetails =   array();
        $country        =   $this->country();

        $data           =   DB::table('TBL_TRN_SLSI02_HDR')
                            ->where('SSIID','=',$id)
                            ->orderBy('SSIID','DESC')
                            ->first();

        $IRN_NO         =   isset($data->IRN_NO) ? $data->IRN_NO:"";
   

        $data           =   DB::table('TBL_TRN_SLSI01_EWAY')
                            ->where('DOC_ID','=',$id)
                            ->where('DOC_TYPE','=',$VTID_REF)
                            ->orderBy('IRN_ID','DESC')
                            ->first();
                         
        $EwayBillDetails=array(); 

        if(!empty($data)){
            $response       =	$data->RESPONSE;
            $InvoiceDetails =   json_decode($response)[0]; 
            $date               =   strtotime($data->EWAYBILL_VALIDTO);
            $EwayBillValidTo    =   date('Y-m-d H:i:s', $date);        
            $EwayBillDetails    =  array("EWAY_NO"=>$data->EWAY_NO,"ACK_NO"=>$data->ACK_NO,"EWAYBILL_VALIDTO"=>$EwayBillValidTo,"STATUS"=>$data->STATUS,"EWAYBILLDT"=>$data->CREATED_DATE); 
        }

        $TRANSPORT_MODE=NULL;
        if(isset($InvoiceDetails->VehType) && $InvoiceDetails->VehType=="R")
        {
            $TRANSPORT_MODE =   "Road";
        }else if(isset($InvoiceDetails->VehType) && $InvoiceDetails->VehType=="T")
        {
            $TRANSPORT_MODE =   "Train";
        }
        else if(isset($InvoiceDetails->VehType) && $InvoiceDetails->VehType=="A")
        {
            $TRANSPORT_MODE =   "Air";
        }

        return view('transactions.sales.SalesServiceInvoice.trnfrm156ewaybill',compact(['FormId','id','InvoiceDetails','country','TRANSPORT_MODE','IRN_NO','EwayBillDetails']));
        }
 
    }

public function GenerateEway(Request $request) {
    $VTID_REF               =   $this->vtid_ref;   
    $USERID                 =   Auth::user()->USERID;  
    $INDATE                 =   date('Y-m-d H:i:s');
    $id                     =   $request['id'];
    $CYID_REF               =   Auth::user()->CYID_REF;
    $IRN                    =   $request['IRN'];
    $TRANSPORTID            =   $request['TRANSPORTID'];
    $TRANSPORT_NAME         =   $request['TRANSPORT_NAME'];
    $TRANSPORT_DOCDT        =   $request['TRANSPORT_DOCDT'];
    $TRANSPORT_DOCNO        =   $request['TRANSPORT_DOCNO'];
    $VEHICLENO              =   $request['VEHICLENO'];
    $VEHICLE_TYPE           =   $request['VEHICLE_TYPE'];
    $SHIPPING_ADDRESS_1     =   $request['SHIPPING_ADDRESS_1'];
    $DISPATCH_LEGALNAME     =   $request['DISPATCH_LEGALNAME'];
    $DISPATCH_ADDRESS1      =   $request['DISPATCH_ADDRESS1'];
    $SHIPCTRYID_REF         =   $request['SHIPCTRYID_REF'];
    $DISCTRYID_REF          =   $request['DISCTRYID_REF'];
    $SHIPSTID_REF           =   $request['SHIPSTID_REF'];
    $DISSTID_REF            =   $request['DISSTID_REF'];
    $SHIPCITYID_REF         =   $request['SHIPCITYID_REF'];
    $DISCITYID_REF          =   $request['DISCITYID_REF'];
    $SHPPING_PINCODE        =   $request['SHPPING_PINCODE'];
    $DISPATCH_PINCODE       =   $request['DISPATCH_PINCODE'];
    $DISTANCE               =   $request['DISTANCE'];   
    $SHIPPING_STATECODE     =   $this->getStateCode($SHIPSTID_REF); 
    $SHIPPING_CITY          =   $this->getCityName($SHIPCITYID_REF); 
    $DISPATCH_STATECODE     =   $this->getStateCode($DISSTID_REF); 
    $DISPATCH_CITY          =   $this->getCityName($DISCITYID_REF); 

    $data[]=  array(
        "Irn"=> $IRN,
        "Distance"=> $DISTANCE,
        "TransMode"=> "1",
        "TransId"=> $TRANSPORTID,
        "TransName"=> $TRANSPORT_NAME,    
        "TransDocDt"=> $TRANSPORT_DOCDT,    
        "TransDocNo"=> $TRANSPORT_DOCNO,    
        "VehNo"=> $VEHICLENO,    
        "VehType"=> $VEHICLE_TYPE,    
        "ExpShipDtls"=> array(
            "Addr1"=> $SHIPPING_ADDRESS_1,
            "Addr2"=> NULL,
            "Loc"=> $SHIPPING_CITY,
            "Pin"=>$SHPPING_PINCODE,
            "Stcd"=>$SHIPPING_STATECODE,
        ),
        "DispDtls"=> array(
            "Nm"=> $DISPATCH_LEGALNAME,
            "Addr1"=> $DISPATCH_ADDRESS1,
            "Addr2"=> NULL,
            "Loc"=> $DISPATCH_CITY,
            "Pin"=> $DISPATCH_PINCODE,
            "Stcd"=>$DISPATCH_STATECODE,           
        ) 
    );

        $data               =   json_encode($data,JSON_UNESCAPED_SLASHES);   
        $response           =	ClearTaxApi::GenerateEwayBill($data);
		//dd($response);
        $response           =   json_decode($response,true); 
        if(!isset($response[0])){
         $Error_Message = isset($response["error_code"]) ? $response["error_code"].'-'.$response["error_message"] :""; 
        
            echo'
                <div class="alert alert-danger">
                    <strong>Error!</strong> '.$Error_Message.'
                </div>
            ';
            exit(); 

        }
        $response           =   $response[0]; 
       // dd($response);   
        $Success            =   isset($response['govt_response']['Success'])?$response['govt_response']['Success']:NULL;
        $Irn                =   isset($response['govt_response']['Irn'])?$response['govt_response']['Irn']:NULL;
        $AckNo              =   isset($response['govt_response']['AckNo'])?$response['govt_response']['AckNo']:NULL;
        $EwbNo              =   isset($response['govt_response']['EwbNo'])?$response['govt_response']['EwbNo']:NULL;
        $EwbValidTill       =   isset($response['govt_response']['EwbValidTill'])?$response['govt_response']['EwbValidTill']:NULL;
        $EwbDt              =   isset($response['govt_response']['EwbDt'])?$response['govt_response']['EwbDt']:NULL;
        $Status             =   isset($response['document_status'])?$response['document_status']:NULL;


    if($Success =="Y"){
        if(isset($response['govt_response']['info'])){
            DB::insert("INSERT INTO TBL_TRN_SLSI01_EWAY (DOC_ID, CREATED_DATE, CREATED_BY, STATUS,INDATE,DOC_TYPE,RESPONSE,ACK_NO,EWAY_NO,EWAYBILL_VALIDTO) VALUES ('$id', '$EwbDt', '$USERID',  'EWAY_GENERATED', '$INDATE','$VTID_REF','$data','$AckNo','$EwbNo','$EwbValidTill')");

            return Response::json(['status' =>200,'message'=>'<div class="alert alert-success"><strong>Success!</strong> Your Eway Bill has been generated successfully.</div>']);            
        }     

    }
    else{
        if(isset($response['govt_response']['ErrorDetails'])){
            echo ' <div class="alert alert-danger">';
            foreach($response['govt_response']['ErrorDetails'] as $key=>$val){
                $hr =   count($response['govt_response']['ErrorDetails']) > $key+1 ?'<hr/>':'';
            echo '<strong>Error! ('.$val['error_code'].') </strong> '.$val['error_message'].$hr;
            }
            echo '</div>';
        }
    }

    die;

}



public function CancelEway(Request $request){
    $id             =    $request['id'];
    $EwayBillNo     =    $request['ewaybillno'];
    $USERID_REF     =   Auth::user()->USERID;
    $VTID_REF       =   $this->vtid_ref; 
    $CYID_REF       =   Auth::user()->CYID_REF;
    $BRID_REF       =   Session::get('BRID_REF');
    $FYID_REF       =   Session::get('FYID_REF'); 
    $response       =	ClearTaxApi::CancelEway($EwayBillNo);
    $response_data  =   json_decode($response);

    if(isset($response_data->ewbStatus) && $response_data->ewbStatus=="CANCELLED"){
        $data       =   DB::table('TBL_TRN_SLSI01_EWAY')
                        ->where('DOC_ID','=',$id)
                        ->where('DOC_TYPE','=',$VTID_REF)
                        ->orderBy('IRN_ID','DESC')
                        ->first();

        DB::table('TBL_TRN_SLSI01_EWAY')
        ->where('IRN_ID', $data->IRN_ID)                
        ->update(["MODIFIED_DATE"=>Date('Y-m-d'),"STATUS"=>"EWAY_CANCELLED","MODIFIED_BY"=>$USERID_REF]);
        
        return Response::json(['cancel' =>true,'msg' => 'Eway Bill has been cancelled successfully']);

    }
    else if(isset($response_data->ewbStatus) && $response_data->ewbStatus=="CANCELLATION_FAILED"){
        
        return Response::json(['errors'=>true,'msg' => isset($response_data->errorDetails[0]->error_message) ? '('.$response_data->errorDetails[0]->error_source.')- '.$response_data->errorDetails[0]->error_message :"",'norecord'=>'norecord']);
    }
    else{
        return Response::json(['errors'=>true,'msg' => "An error has been occurred.",'norecord'=>'norecord']);
    }

    exit(); 
}

public function PrintEway($id=NULL){
    //$id=331009118390;
    $response   =	ClearTaxApi::PrintEway($id);
   
    header('Cache-Control: public'); 
    header('Content-type: application/pdf');
    header('Content-Disposition: attachment; filename="EwayBill'.date('YmdHis').'.pdf"');
    header('Content-Length: '.strlen($response));
    echo $response;
    
}



function getTotalMaterialAmount($SIID_REF){

    $TOTAL_MAT_AMOUNT   =   0;
    $data               =   DB::select("SELECT * FROM TBL_TRN_SLSI02_MAT	WHERE SSIID_REF	='$SIID_REF'");

    if(isset($data) && !empty($data)){
        foreach($data as $key=>$val){
            $QTY        =   $val->SSI_QTY !=""?floatval($val->SSI_QTY):0;
            $RATE       =   $val->RATE_PRUOM !=""?floatval($val->RATE_PRUOM):0;
            $DIS_PER    =   $val->DISCOUNT_PER !=""?floatval($val->DISCOUNT_PER):0;
            $DIS_AMT    =   $val->DISCOUNT_AMT !=""?floatval($val->DISCOUNT_AMT):0;
            $IGST       =   $val->IGST !=""?floatval($val->IGST):0;
            $CGST       =   $val->CGST !=""?floatval($val->CGST):0;
            $SGST       =   $val->SGST !=""?floatval($val->SGST):0;

            $TOTAL_AMOUNT   =   $QTY*$RATE;
            
            if($DIS_PER > 0){
                $TOTAL_DISCOUNT   =   ($TOTAL_AMOUNT*$DIS_PER)/100;
            }
            else if($DIS_AMT > 0){
                $TOTAL_DISCOUNT   =   $DIS_AMT;
            }
            else{
                $TOTAL_DISCOUNT   =   0;
            }

            $TOTAL_AMOUNT       =   $TOTAL_AMOUNT-$TOTAL_DISCOUNT;

            $IGST_AMOUNT        =   ($TOTAL_AMOUNT*$IGST)/100;
            $CGST_AMOUNT        =   ($TOTAL_AMOUNT*$CGST)/100;
            $SGST_AMOUNT        =   ($TOTAL_AMOUNT*$SGST)/100;
            $TOTAL_TAX_AMOUNT   =   ($IGST_AMOUNT+$CGST_AMOUNT+$SGST_AMOUNT);
            $TOTAL_AMOUNT       =   $TOTAL_AMOUNT+$TOTAL_TAX_AMOUNT;

            $TOTAL_MAT_AMOUNT   =   $TOTAL_MAT_AMOUNT+$TOTAL_AMOUNT;
        }
    }

    return $TOTAL_MAT_AMOUNT;
}

function getTotalCalculationAmount($SIID_REF){

    $TOTAL_CAL_AMOUNT   =   0;
    $data               =   DB::select("SELECT * FROM TBL_TRN_SLSI02_CAL	WHERE SSIID_REF	='$SIID_REF'");

    if(isset($data) && !empty($data)){
        foreach($data as $key=>$val){

            $VALUE              =   $val->VALUE !=""?floatval($val->VALUE):0;
            $IGST               =   $val->IGST !=""?floatval($val->IGST):0;
            $CGST               =   $val->CGST !=""?floatval($val->CGST):0;
            $SGST               =   $val->SGST !=""?floatval($val->SGST):0;

            $TOTAL_AMOUNT       =   $VALUE;

            $IGST_AMOUNT        =   ($TOTAL_AMOUNT*$IGST)/100;
            $CGST_AMOUNT        =   ($TOTAL_AMOUNT*$CGST)/100;
            $SGST_AMOUNT        =   ($TOTAL_AMOUNT*$SGST)/100;
            $TOTAL_TAX_AMOUNT   =   ($IGST_AMOUNT+$CGST_AMOUNT+$SGST_AMOUNT);

            $TOTAL_AMOUNT       =   $TOTAL_AMOUNT+$TOTAL_TAX_AMOUNT;
            $TOTAL_CAL_AMOUNT   =   $TOTAL_CAL_AMOUNT+$TOTAL_AMOUNT;
        }
    }

    return $TOTAL_CAL_AMOUNT;
}

function getTotalTdsAmount($SIID_REF){

    $TOTAL_TDS_AMOUNT   =   0;
    $data               =   DB::select("SELECT * FROM TBL_TRN_SLSI02_TDS	WHERE SSIID_REF	='$SIID_REF'");

    if(isset($data) && !empty($data)){
        foreach($data as $key=>$val){

            $ASSESSABLE_VL_TDS  =   $val->ASSESSABLE_VL_TDS !=""?floatval($val->ASSESSABLE_VL_TDS):0;
            $TDS_RATE           =   $val->TDS_RATE !=""?floatval($val->TDS_RATE):0;
            $TOTAL_AMOUNT       =   ($ASSESSABLE_VL_TDS*$TDS_RATE)/100;
            $TOTAL_TDS_AMOUNT   =   $TOTAL_TDS_AMOUNT+$TOTAL_AMOUNT;
        }
    }

    return $TOTAL_TDS_AMOUNT;
}


// Clear Tax Invoice & EWAY Bill Section Ends Here-------------------


   

    
}
