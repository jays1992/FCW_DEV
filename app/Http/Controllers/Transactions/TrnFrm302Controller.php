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
use Storage;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TrnFrm302Controller extends Controller{

    protected $form_id  = 302;
    protected $vtid_ref = 392;
    protected $view     = "transactions.Accounts.ReceiptEntry.trnfrm";
   
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){ 
       




        $objRights      =   DB::table('TBL_MST_USERROLMAP')
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

        $objDataList	=	DB::select("select hdr.RECEIPTID,hdr.RECEIPT_NO,hdr.RECEIPT_DT,hdr.RECEIPT_FOR,hdr.RECEIPT_TYPE,hdr.NARRATION,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.RECEIPTID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            end end as STATUS_DESC
                            from TBL_TRN_AUDITTRAIL a 
                            inner join TBL_TRN_RECEIPT_HDR hdr
                            on a.VID = hdr.RECEIPTID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            left join TBL_MST_SUBLEDGER sl ON hdr.CUSTMER_VENDOR_ID = sl.SGLID 
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.RECEIPTID DESC ");

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
	
	public function ViewReport($request) 
    {
        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
       // dd($myValue);  
        $RECEIPTID       =   $myValue['RECEIPTID'];
        $Flag          =   $myValue['Flag'];

         /* $objSalesOrder = DB::table('TBL_TRN_IPO_HDR')
         ->where('TBL_TRN_IPO_HDR.CYID_REF','=',Auth::user()->CYID_REF)
         ->where('TBL_TRN_IPO_HDR.BRID_REF','=',Auth::user()->BRID_REF)
         ->where('TBL_TRN_IPO_HDR.IPO_ID','=',$IPO_ID)
         ->select('TBL_TRN_IPO_HDR.*')
         ->first(); */
        
        
	$ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
        $result = $ssrs->loadReport('/ERP/Receipt_VoucherPrint');
        
        $reportParameters = array(
            'RECEIPTID' => $RECEIPTID,
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
        $Status     = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
       
        $objlastdt          =   $this->getLastdt();

        
        $objRCPTDOCNO = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('FYID_REF','=',$FYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_DOCNO_DEFINITION.*')
        ->first();

        $objDataNo   =   NULL;

        if( isset($objRCPTDOCNO->SYSTEM_GRSR) && $objRCPTDOCNO->SYSTEM_GRSR == "1")
        {
            if($objRCPTDOCNO->PREFIX_RQ == "1")
            {
                $objDataNo = $objRCPTDOCNO->PREFIX;
            }        
            if($objRCPTDOCNO->PRE_SEP_RQ == "1")
            {
                if($objRCPTDOCNO->PRE_SEP_SLASH == "1")
                {
                $objDataNo = $objDataNo.'/';
                }
                if($objRCPTDOCNO->PRE_SEP_HYPEN == "1")
                {
                $objDataNo = $objDataNo.'-';
                }
            }        
            if($objRCPTDOCNO->NO_MAX)
            {   
                $objDataNo = $objDataNo.str_pad($objRCPTDOCNO->LAST_RECORDNO+1, $objRCPTDOCNO->NO_MAX, "0", STR_PAD_LEFT);
            }
            
            if($objRCPTDOCNO->NO_SEP_RQ == "1")
            {
                if($objRCPTDOCNO->NO_SEP_SLASH == "1")
                {
                $objDataNo = $objDataNo.'/';
                }
                if($objRCPTDOCNO->NO_SEP_HYPEN == "1")
                {
                $objDataNo = $objDataNo.'-';
                }
            }
            if($objRCPTDOCNO->SUFFIX_RQ == "1")
            {
                $objDataNo = $objDataNo.$objRCPTDOCNO->SUFFIX;
            }
        }

        $objgeneralledger   =   $this->getGeneralLedger();

        $objCostCenter = DB::table('TBL_MST_COSTCENTER')
        ->where('CYID_REF','=',$CYID_REF)
        ->where('STATUS','=','A')
        ->select('TBL_MST_COSTCENTER.*')
        ->get();

        $objBank = DB::table('TBL_MST_BANK')
        ->leftJoin('TBL_MST_CITY', 'TBL_MST_CITY.CITYID','=','TBL_MST_BANK.CITYID_REF')
        ->leftJoin('TBL_MST_STATE', 'TBL_MST_STATE.STID','=','TBL_MST_BANK.STID_REF')
        ->leftJoin('TBL_MST_COUNTRY', 'TBL_MST_COUNTRY.CTRYID','=','TBL_MST_BANK.CTRYID_REF')
        ->join('TBL_MST_DOCNO_DEFINITION', 'TBL_MST_BANK.BANK_CASH','=','TBL_MST_DOCNO_DEFINITION.PREFIX_TYPE')
        ->where('TBL_MST_DOCNO_DEFINITION.VTID_REF','=',$this->vtid_ref)
        ->where('TBL_MST_BANK.CYID_REF','=',$CYID_REF)
        ->where('TBL_MST_BANK.BRID_REF','=',$BRID_REF)
        ->where('TBL_MST_BANK.STATUS','=','A')
        ->select('TBL_MST_BANK.*','TBL_MST_DOCNO_DEFINITION.*','TBL_MST_CITY.NAME AS CITY','TBL_MST_STATE.NAME AS STATE','TBL_MST_COUNTRY.NAME  AS COUNTRY')
        ->get();

        $FormId     =   $this->form_id;
        $objothcurrency = $this->GetCurrencyMaster(); 
        
        return view($this->view.$FormId.'add',compact(['FormId','objRCPTDOCNO','objDataNo','objlastdt','objgeneralledger',
                    'objCostCenter','objBank','objothcurrency']));       
    }

    public function getCostCenter(Request $request){
        
        $customid = $request['customid'];
        $CYID_REF = Auth::user()->CYID_REF;
              
        
        $sp_result = DB::table('TBL_MST_COSTCENTER')
        ->where('CYID_REF','=',$CYID_REF)
        ->where('STATUS','=','A')
        ->select('TBL_MST_COSTCENTER.*')
        ->get(); 
        if(!empty($sp_result)){
            foreach ($sp_result as $index=>$dataRow){
            
                $row = '';
                $row = $row.' <tr id="cccode_'.$dataRow->CCID .'"  class="clscccd"><td width="50%">'.$dataRow->CCCODE;
                $row = $row.' <input type="hidden" id="txtcccode_'.$dataRow->CCID.'" data-desc="'.$dataRow->CCCODE .'"  ';
                $row = $row.' value="'.$dataRow->CCID.'"/></td><td>'.$dataRow->NAME.'</td></tr>';
                echo $row;
            }

            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
        exit();
    }

    public function getGeneralLedger(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
    
        return  DB::table('TBL_MST_GENERALLEDGER')
        ->where('TBL_MST_GENERALLEDGER.CYID_REF','=',$CYID_REF)
        ->where('TBL_MST_GENERALLEDGER.STATUS','=','A')
        ->where('TBL_MST_GENERALLEDGER.DEACTIVATED','=',"0")
        ->select('TBL_MST_GENERALLEDGER.*')
        ->get();
        }

        
        public function getglsl(Request $request){
            $CYID_REF   = Auth::user()->CYID_REF;
            $BRID_REF   = Auth::user()->BRID_REF;
            $SL         = $request['SL'];
            $fieldid    = $request['fieldid'];
    
            $log_data = [ 
                $SL, $CYID_REF, $BRID_REF
            ];
        
            $sp_result = DB::select('EXEC SP_GET_GLSL ?,?,?', $log_data); 
    
            if(!empty($sp_result)){
    
                foreach ($sp_result as $index=>$dataRow){
                
                    $row = '';
                    $row = $row.'<tr >
                    <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="glidcode_'.$dataRow->ID .'"  class="clsglid" value="'.$dataRow->ID.'" ></td>
                    <td class="ROW2">'.$dataRow->CODE;
                    $row = $row.'<input type="hidden" id="txtglidcode_'.$dataRow->ID.'" data-desc="'.$dataRow->CODE .'" data-desc2="'.$dataRow->NAME.'" ';
                    $row = $row.' data-desc3="'.$dataRow->FLAG.'" value="'.$dataRow->ID.'"/></td>
                    
                    <td class="ROW3">'.$dataRow->NAME.'</td></tr>';
    
                    echo $row;
                }
    
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    
        }
    
    public function getvendor(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');

        return  DB::table('TBL_MST_VENDOR')
        ->where('TBL_MST_VENDOR.CYID_REF','=',$CYID_REF)
        ->where('TBL_MST_VENDOR.BRID_REF','=',$BRID_REF)
        ->where('TBL_MST_VENDOR.STATUS','=','A')
        ->where('TBL_MST_VENDOR.DEACTIVATED','=',"0")
        ->select('TBL_MST_VENDOR.*')
        ->get();
        }

    public function getCustVendor(Request $request){
            $Status = "A";
            $CommonValue = $request['CommonValue'];
            $BRID_REF = Session::get('BRID_REF');
            $CYID_REF = Auth::user()->CYID_REF;
            $CODE = $request['CODE'];
            $NAME = $request['NAME'];
            
            if($CommonValue == 'Customer')
            {
                $sp_popup = [
                    $CYID_REF, $BRID_REF,$CODE,$NAME
                ]; 
                
                    $ObjData = DB::select('EXEC sp_get_customer_popup_enquiry ?,?,?,?', $sp_popup);
            
                    if(!empty($ObjData)){
            
                    foreach ($ObjData as $index=>$dataRow){
                    
                        $row = '';
                        $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_SLID_REF[]" id="subgl_'.$index.'" class="clscustid" value="'.$dataRow-> SGLID.'" ></td>';
                        $row = $row.'<td class="ROW2">'.$dataRow->SGLCODE;
                        $row = $row.'<input type="hidden" id="txtsubgl_'.$index.'" data-desc="'.$dataRow->SGLCODE .' - ';
                        $row = $row.$dataRow->SLNAME. '" data-desc2="'.$dataRow->GLID_REF. '"value="'.$dataRow->SGLID.'"/></td><td class="ROW3">'.$dataRow->SLNAME.'</td></tr>';
            
            
                        echo $row;
                    }
            
                    }else{
                        echo '<tr><td colspan="2">Record not found.</td></tr>';
                    }
            }
            else if($CommonValue == 'Vendor')
            {
                $sp_popup = [
                    $CYID_REF, $BRID_REF,$CODE,$NAME
                ];
                $ObjData = DB::select('EXEC sp_get_vendor_popup_enquiry ?,?,?,?', $sp_popup);
            
                    if(!empty($ObjData)){
            
                    foreach ($ObjData as $index=>$dataRow){
                    
                        $row = '';
                        $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_SLID_REF[]" id="subgl_'.$index.'" class="clscustid" value="'.$dataRow-> SGLID.'" ></td>';
                        $row = $row.'<td class="ROW2">'.$dataRow->SGLCODE;
                        $row = $row.'<input type="hidden" id="txtsubgl_'.$index.'" data-desc="'.$dataRow->SGLCODE .' - ';
                        $row = $row.$dataRow->SLNAME. '" data-desc2="'.$dataRow->GLID_REF. '"value="'.$dataRow->SGLID.'"/></td><td class="ROW3">'.$dataRow->SLNAME.'</td></tr>';
            
            
                        echo $row;
                    }
            
                    }else{
                        echo '<tr><td colspan="2">Record not found.</td></tr>';
                    }
            }
                exit();
        
    }

    public function getCustVdrDocument(Request $request){
        $Status      = "A";
        $CommonValue = $request['CommonValue'];
        $id          = $request['Customid'];
        $centralized = $request['centralized'];
        $BRID_REF    = Session::get('BRID_REF');
        $CYID_REF    = Auth::user()->CYID_REF;
        
        $sp_param = [ 
            $CommonValue,$id,$centralized,$BRID_REF,$CYID_REF
        ];  




        $ObjData = DB::select('EXEC SP_TRN_GET_INVOICE_CUST_VENDOR_WISE_RECEIPT ?,?,?,?,?', $sp_param);

        //dd($sp_param); 

        if(isset($ObjData) && !empty($ObjData)){
                
            foreach ($ObjData as $index=>$dataRow){
            
                $row = '';
                $row = $row.'<tr id="invoiceid_'.$dataRow->SOURCETYPE.'-'.$dataRow->ID.'-'.$dataRow->BRID_REF.'" class="clsinvoiceid">
                <td  style="width:10%; text-align: center;">
                <input type="checkbox" id="chkId'.$dataRow->SOURCETYPE.'-'.$dataRow->ID.'-'.$dataRow->BRID_REF.'"  
                value="'.$dataRow->ID.'" class="js-selectall1"  ></td> <td style="width:15%;">'.$dataRow->SOURCETYPE;
                $row = $row.'<input type="hidden" id="txtinvoiceid_'.$dataRow->SOURCETYPE.'-'.$dataRow->ID.'-'.$dataRow->BRID_REF.'" 
                data-desc="'.$dataRow->DOCNO.'" data-desc2="'.$dataRow->DOCDT.'" data-desc3="'.$dataRow->BRANCH.'" data-desc4="'.$dataRow->DOCAMT.'"
                data-desc5="'.$dataRow->BALANCEAMT.'" data-desc6="'.$dataRow->BRID_REF.'" data-desc7="'.$dataRow->SOURCETYPE.'"
                value="'.$dataRow->ID.'"/></td><td style="width:15%;">'.$dataRow->DOCNO.'</td>
                <td style="width:15%;">'.$dataRow->DOCDT.'</td><td style="width:15%;">'.$dataRow->BRANCH.'</td>
                <td style="width:7%;">'.$dataRow->DOCAMT.'</td> <td style="width:7%;">'.$dataRow->BALANCEAMT.'</td>
                <td style="width:8%;">'.$dataRow->SUPPLIERINVOICE.'</td> 
                <td style="width:8%;">'.$dataRow->DUEDAYS.'</td></tr>';

                echo $row;
            }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        
        exit();
    
    } 

    public function save(Request $request) {
        //  dd($request->all());
       // $r_count1 = $request['Row_Count1'];
        if(isset($request['rowcount1'])){
            $r_count1 = count($request['rowcount1']);
            }else{
            $r_count1 = NULL;
            }
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count6 = $request['Row_Count6'];
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['DOCNO_ID_'.$i]) && $request['DOCNO_ID_'.$i] != '')
            {
                $req_data[$i] = [
                    'DOC_TYPE'                  => $request['Doc_Type_'.$i],
                    'DOCNO_ID'                  => $request['DOCNO_ID_'.$i],                    
                    'BALANCE_DUE'               => (!is_null($request['BALANCE_DUE_'.$i]) ? $request['BALANCE_DUE_'.$i] : 0),
                    'RECEIPT_AMT'               => (!is_null($request['RECEIPT_AMT_'.$i]) ? $request['RECEIPT_AMT_'.$i] : 0),
                    'REMARKS'                   => (isset($request['REMARKS_'.$i]) ? $request['REMARKS_'.$i] : ''),
                    'BRID_REF'                  => (isset($request['BRID_REF_'.$i]) ? $request['BRID_REF_'.$i] : ''),
                ];
            }
        }

        if(isset($req_data))
        { 
            $wrapped_links["INVOCE"] = $req_data; 
            $INVOICE = ArrayToXml::convert($wrapped_links);
        }
        else
        {
            $INVOICE = NULL; 
        }

        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset($request['GLID_REF_'.$i]) && $request['GLID_REF_'.$i] != '')
            {
                $reqdata2[$i] = [
                    'GLID_REF'                  => $request['GLID_REF_'.$i],
                    'AMOUNT'                    => (!is_null($request['AMOUNT_'.$i]) ? $request['AMOUNT_'.$i] : 0),
                    'IGST'                      => (!is_null($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST'                      => (!is_null($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST'                      => (!is_null($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'CCID_REF'                  => (isset($request['CCID_REF_'.$i]) ? $request['CCID_REF_'.$i] : ''),
                    'TYPE'                      => $request['TYPE_'.$i],
                ];
            }
            
        }
        if(isset($reqdata2))
        { 
            $wrapped_links2["ACCOUNT"] = $reqdata2;
            $ACCOUNT = ArrayToXml::convert($wrapped_links2);
        }
        else
        {
            $ACCOUNT = NULL; 
        }
        

        for ($i=0; $i<=$r_count2; $i++)
        {
            if(isset($request['GLID_'.$i]) && $request['GLID_'.$i] != '')
            {
                $reqdata3[$i] = [
                    'GLID_REF'                  => $request['GLID_'.$i],
                    'CCID_REF'                  => (isset($request['CCID_'.$i]) ? $request['CCID_'.$i] : ''),
                    'AMT'                       => (!is_null($request['GL_AMT_'.$i]) ? $request['GL_AMT_'.$i] : 0),
                ];
            }
        }
        if(isset($reqdata3))
        { 
            $wrapped_links3["CCD"] = $reqdata3; 
            $CCD = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $CCD = NULL; 
        }
        
                for ($i=0; $i<=$r_count6; $i++){
            if(isset($request['TDSID_REF_'.$i]) && $request['TDSID_REF_'.$i] !=''){
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
            $wrapped_links6["TDS"] = $reqdata6; 
            $TDS = ArrayToXml::convert($wrapped_links6);
        }
        else{
            $TDS = NULL; 
        }


        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID_REF = Auth::user()->USERID;   
        $ACTION = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $UPDATE  =  Date('Y-m-d');
        $UPTIME =   Date('h:i:s.u');

        $RECEIPT_NO                       = $request['RECEIPT_NO'] ? $request['RECEIPT_NO'] : NULL;
        $RECEIPT_DT                       = $request['RECEIPT_DT'];
        $RECEIPT_FOR                      = $request['hdnreceiptfor']? $request['hdnreceiptfor'] : NULL;
        $CUSTMER_VENDOR_ID                = $request['CUSTMER_VENDOR_ID']? $request['CUSTMER_VENDOR_ID'] : NULL;
        $RECEIPT_TYPE                     = $request['RECEIPT_TYPE']? $request['RECEIPT_TYPE'] : NULL;
        $RECEIPT_ON_ACCOUNT               = (isset($request['chk_RcptAccount'])!="true" ? 0 : 1);
        $CASH_BANK_ID                     = (isset($request['chk_Account'])=="true" ? $request['BANK_CASH_ID'] : $request['CASH_BANK_ID']);
        $TRANSACTION_DT                   = $request['TRANSACTION_DT'];
        $INSTRUMENT_TYPE                  = $request['INSTRUMENT_TYPE'] ? $request['INSTRUMENT_TYPE'] : NULL;
        $INSTRUMENT_NO                    = $request['INSTRUMENT_NO'] ? $request['INSTRUMENT_NO'] : 0;
        $BANK_CHARGE                      = (!is_null($request['BANK_CHARGE']) ? $request['BANK_CHARGE'] : 0);
        $NARRATION                        = (!is_null($request['NARRATION']) ? $request['NARRATION'] : '');
        $CENTERLIZED_RECEIPT              = (isset($request['CENTERLIZED_RECEIPT'])!="true" ? 0 : 1);
        $REMARKS                          = '';
        $TOAL_AMOUNT                      = $request['tot_amt1'] ? $request['tot_amt1'] : 0;
        $AMOUNT                           = (!is_null($request['AMOUNT']) ? $request['AMOUNT'] : 0);

        $ROUNDOFF_GLID                   = $request['GLID_REF_ROUNDOFF']?$request['GLID_REF_ROUNDOFF']:0;
        $ROUNDOFF_AMT                    = $request['ROUNDOFF_AMT']?$request['ROUNDOFF_AMT']:0;
        $ROUNDOFF_MODE                   = $request['ROUNDOFF_MODE'] ? $request['ROUNDOFF_MODE'] : NULL; 
        $TDS_CHECK                       = (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);
        $BANK_AMOUNT                     = $request['BANK_AMOUNT'] ? $request['BANK_AMOUNT'] : 0;
        $BANK_REMARKS                    = $request['BANK_REMARKS']? $request['BANK_REMARKS'] : NULL;

        $PDC_STATUS                      = (isset($request['PDC_STATUS'])!="true" ? 0 : 1);
        $PDC_DT                          = $request['PDC_DT']; 
        $CLEAR_PDC_STATUS                = (isset($request['CLEAR_PDC_STATUS'])!="true" ? 0 : 1);
        $CLEAR_PDC_DT                    = $request['CLEAR_PDC_DT']; 
        $RECORD_TYPE                    = $request['PDC_ECH_TYPE']? $request['PDC_ECH_TYPE'] : NULL;
        $FC 			                 = (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF 		                 = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT 		                 = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : 0;
       
        $log_data = [ 
            $RECEIPT_NO,            $RECEIPT_DT,        $RECEIPT_FOR,       $CUSTMER_VENDOR_ID,     $RECEIPT_TYPE,  $RECEIPT_ON_ACCOUNT,
            $CASH_BANK_ID,          $TRANSACTION_DT,    $INSTRUMENT_TYPE,   $INSTRUMENT_NO,         $BANK_CHARGE,   $NARRATION,
            $CENTERLIZED_RECEIPT,   $REMARKS,           $TOAL_AMOUNT,       $AMOUNT,                $CYID_REF,      $BRID_REF,
            $FYID_REF,              $VTID_REF,          $INVOICE,           $ACCOUNT,               $CCD,           $TDS,
            $USERID_REF,            $UPDATE,            $UPTIME,            $ACTION,                $IPADDRESS,     $ROUNDOFF_GLID,
            $ROUNDOFF_AMT,          $ROUNDOFF_MODE,     $TDS_CHECK,         $BANK_AMOUNT,           $BANK_REMARKS,  $PDC_STATUS,
            $PDC_DT,                $CLEAR_PDC_STATUS,  $CLEAR_PDC_DT,      $RECORD_TYPE,           $FC,            $CRID_REF,
            $CONVFACT
        ];
        
        $sp_result = DB::select('EXEC SP_RECEIPT_IN ?,?,?,?,?,?,    ?,?,?,?,?,?,    ?,?,?,?,?,?,    ?,?,?,?,?,?,    ?,?,?,?,?,?,    ?,?,?,?,?,?,    ?,?,?,?,?,?, ?', $log_data);  

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

                $objRCPTHDR = DB::table('TBL_TRN_RECEIPT_HDR')
                        ->where('TBL_TRN_RECEIPT_HDR.FYID_REF','=',$FYID_REF)
                        ->where('TBL_TRN_RECEIPT_HDR.CYID_REF','=',$CYID_REF)
                        ->where('TBL_TRN_RECEIPT_HDR.BRID_REF','=',$BRID_REF)
                        ->where('TBL_TRN_RECEIPT_HDR.RECEIPTID','=',$id)
                        ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_RECEIPT_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
                        ->select('TBL_TRN_RECEIPT_HDR.*','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
                        ->first();

                        if($objRCPTHDR->ROUNDOFF_GLID!=''){
                            $objGl = DB::table('TBL_MST_GENERALLEDGER')
                            ->where('GLID','=',$objRCPTHDR->ROUNDOFF_GLID)
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('BRID_REF','=',Session::get('BRID_REF'))
                            ->where('SUBLEDGER','!=',1)
                            ->select('GLID','GLCODE','GLNAME') 
                            ->first() ;
                            }else{
                            $objGl=NULL; 
                            }
            
                            $log_data = [ 
                                $id
                             ];
                       
                            $objRECEIPTTDS = DB::select('EXEC SP_GET_RECEIPT_TDS ?', $log_data);
                            $objCount6 = count($objRECEIPTTDS);
                           

          
                $objRCPTINV =[];
                if(isset($objRCPTHDR->CUSTMER_VENDOR_ID) && $objRCPTHDR->CUSTMER_VENDOR_ID !=""){

                    $log_data = [ 
                        $id,$BRID_REF,$CYID_REF,$objRCPTHDR->CUSTMER_VENDOR_ID,$objRCPTHDR->CENTERLIZED_RECEIPT,$objRCPTHDR->RECEIPT_FOR
                    ];      
                    
                  // DD($log_data); 

                    $objRCPTINV = DB::select('EXEC SP_GET_RECEIPT_INVOICE ?,?,?,?,?,?', $log_data);

                   // DD($objRCPTINV); 

                }
                $objCount1 = count($objRCPTINV); 

            
                if(isset($objRCPTHDR->RECEIPT_FOR) && $objRCPTHDR->RECEIPT_FOR == 'Vendor')
                {
                    $objRCPTCUSTVNDR = DB::table('tbl_MST_SUBLEDGER')
                    ->where('tbl_MST_SUBLEDGER.CYID_REF','=',$CYID_REF)
                    ->where('tbl_MST_SUBLEDGER.BRID_REF','=',$BRID_REF)
                    ->where('tbl_MST_SUBLEDGER.SGLID','=',$objRCPTHDR->CUSTMER_VENDOR_ID)
                    ->where('tbl_MST_SUBLEDGER.BELONGS_TO','=','Vendor')
                    ->select('tbl_MST_SUBLEDGER.SGLCODE AS CODE','tbl_MST_SUBLEDGER.SLNAME AS NAME')
                    ->first();
                }
                else if(isset($objRCPTHDR->RECEIPT_FOR) && $objRCPTHDR->RECEIPT_FOR == 'Customer')
                {
                    $objRCPTCUSTVNDR = DB::table('tbl_MST_SUBLEDGER')
                    ->where('tbl_MST_SUBLEDGER.CYID_REF','=',$CYID_REF)
                    ->where('tbl_MST_SUBLEDGER.BRID_REF','=',$BRID_REF)
                    ->where('tbl_MST_SUBLEDGER.SGLID','=',$objRCPTHDR->CUSTMER_VENDOR_ID)
                    ->where('tbl_MST_SUBLEDGER.BELONGS_TO','=','Customer')
                    ->select('tbl_MST_SUBLEDGER.SGLCODE AS CODE','tbl_MST_SUBLEDGER.SLNAME AS NAME')
                    ->first();
                }




                else 
                {
                    $objRCPTCUSTVNDR = NULL;
                }

                $objRCPTCASHBANK =[];
                if(isset($objRCPTHDR->CASH_BANK_ID) && $objRCPTHDR->CASH_BANK_ID !=""){
                    $objRCPTCASHBANK = DB::table('TBL_MST_BANK')
                    ->where('TBL_MST_BANK.CYID_REF','=',$CYID_REF)
                    ->where('TBL_MST_BANK.BRID_REF','=',$BRID_REF)
                    ->where('TBL_MST_BANK.BID','=',$objRCPTHDR->CASH_BANK_ID)
                    ->select('TBL_MST_BANK.*')
                    ->first();
                }
                
                $objRCPTACCOUNT = DB::table('TBL_TRN_RECEIPT_ACCOUNT')  
                ->leftJoin('TBL_MST_GENERALLEDGER', 'TBL_TRN_RECEIPT_ACCOUNT.GLID_REF','=','TBL_MST_GENERALLEDGER.GLID')
                ->where('TBL_MST_GENERALLEDGER.CYID_REF','=',$CYID_REF)                   
                ->where('TBL_TRN_RECEIPT_ACCOUNT.RECEIPTID_REF','=',$id)
                ->select('TBL_TRN_RECEIPT_ACCOUNT.*','TBL_MST_GENERALLEDGER.*')
                ->get()
                ->toArray();
                
                $objCount2 = count($objRCPTACCOUNT);

                $objRCPTCCD = DB::table('TBL_TRN_RECEIPT_CCD')                    
                ->where('TBL_TRN_RECEIPT_CCD.RECEIPTID_REF','=',$id)
                ->get()->toArray();
                
                $objCount3 = count($objRCPTCCD);                
        
                $objRights = DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_USERROLMAP.BRID_REF','=',Auth::user()->BRID_REF)
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();
                
            
                $FormId     =   $this->form_id;

                $objgeneralledger   =   $this->getGeneralLedger();
        
                $objCostCenter = DB::table('TBL_MST_COSTCENTER')
                ->where('CYID_REF','=',$CYID_REF)
                ->where('STATUS','=','A')
                ->select('TBL_MST_COSTCENTER.*')
                ->get();

                $objBank = DB::table('TBL_MST_BANK')
                ->leftJoin('TBL_MST_CITY', 'TBL_MST_CITY.CITYID','=','TBL_MST_BANK.CITYID_REF')
                ->leftJoin('TBL_MST_STATE', 'TBL_MST_STATE.STID','=','TBL_MST_BANK.STID_REF')
                ->leftJoin('TBL_MST_COUNTRY', 'TBL_MST_COUNTRY.CTRYID','=','TBL_MST_BANK.CTRYID_REF')
                ->join('TBL_MST_DOCNO_DEFINITION', 'TBL_MST_BANK.BANK_CASH','=','TBL_MST_DOCNO_DEFINITION.PREFIX_TYPE')
                ->where('TBL_MST_BANK.CYID_REF','=',$CYID_REF)
                ->where('TBL_MST_BANK.BRID_REF','=',$BRID_REF)
                ->where('TBL_MST_BANK.STATUS','=','A')
                ->select('TBL_MST_BANK.*','TBL_MST_DOCNO_DEFINITION.*','TBL_MST_CITY.NAME AS CITY','TBL_MST_STATE.NAME AS STATE','TBL_MST_COUNTRY.NAME  AS COUNTRY')
                ->get(); 

                $objlastdt          =   $this->getLastdt();

                $ActionStatus   =   "";

                $objothcurrency = $this->GetCurrencyMaster(); 
            
            return view($this->view.$FormId.'edit',compact(['FormId','objRights','objRCPTHDR','objRCPTINV','objRCPTACCOUNT','objRCPTCCD',
                'objBank','objCostCenter','objgeneralledger','objCount1','objCount2','objCount3','objRCPTCASHBANK','objRCPTCUSTVNDR',
                'objlastdt','ActionStatus','objGl','objRECEIPTTDS','objCount6','objothcurrency']));      

        }
     
    }


    public function view($id){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){

            $objRCPTHDR = DB::table('TBL_TRN_RECEIPT_HDR')
                        ->where('TBL_TRN_RECEIPT_HDR.FYID_REF','=',$FYID_REF)
                        ->where('TBL_TRN_RECEIPT_HDR.CYID_REF','=',$CYID_REF)
                        ->where('TBL_TRN_RECEIPT_HDR.BRID_REF','=',$BRID_REF)
                        ->where('TBL_TRN_RECEIPT_HDR.RECEIPTID','=',$id)
                        ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_RECEIPT_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
                        ->select('TBL_TRN_RECEIPT_HDR.*','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
                        ->first();

                        if($objRCPTHDR->ROUNDOFF_GLID!=''){
                            $objGl = DB::table('TBL_MST_GENERALLEDGER')
                            ->where('GLID','=',$objRCPTHDR->ROUNDOFF_GLID)
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('BRID_REF','=',Session::get('BRID_REF'))
                            ->where('SUBLEDGER','!=',1)
                            ->select('GLID','GLCODE','GLNAME') 
                            ->first() ;
                            }else{
                            $objGl=NULL; 
                            }
            
                            $log_data = [ 
                                $id
                             ];
                       
                            $objRECEIPTTDS = DB::select('EXEC SP_GET_RECEIPT_TDS ?', $log_data);
                            $objCount6 = count($objRECEIPTTDS);
                           

          
                $objRCPTINV =[];
                if(isset($objRCPTHDR->CUSTMER_VENDOR_ID) && $objRCPTHDR->CUSTMER_VENDOR_ID !=""){

                    $log_data = [ 
                        $id,$BRID_REF,$CYID_REF,$objRCPTHDR->CUSTMER_VENDOR_ID,$objRCPTHDR->CENTERLIZED_RECEIPT,$objRCPTHDR->RECEIPT_FOR
                    ];      
                    
                  // DD($log_data); 

                    $objRCPTINV = DB::select('EXEC SP_GET_RECEIPT_INVOICE ?,?,?,?,?,?', $log_data);

                   // DD($objRCPTINV); 

                }
                $objCount1 = count($objRCPTINV); 

            
                if(isset($objRCPTHDR->RECEIPT_FOR) && $objRCPTHDR->RECEIPT_FOR == 'Vendor')
                {
                    $objRCPTCUSTVNDR = DB::table('tbl_MST_SUBLEDGER')
                    ->where('tbl_MST_SUBLEDGER.CYID_REF','=',$CYID_REF)
                    ->where('tbl_MST_SUBLEDGER.BRID_REF','=',$BRID_REF)
                    ->where('tbl_MST_SUBLEDGER.SGLID','=',$objRCPTHDR->CUSTMER_VENDOR_ID)
                    ->where('tbl_MST_SUBLEDGER.BELONGS_TO','=','Vendor')
                    ->select('tbl_MST_SUBLEDGER.SGLCODE AS CODE','tbl_MST_SUBLEDGER.SLNAME AS NAME')
                    ->first();
                }
                else if(isset($objRCPTHDR->RECEIPT_FOR) && $objRCPTHDR->RECEIPT_FOR == 'Customer')
                {
                    $objRCPTCUSTVNDR = DB::table('tbl_MST_SUBLEDGER')
                    ->where('tbl_MST_SUBLEDGER.CYID_REF','=',$CYID_REF)
                    ->where('tbl_MST_SUBLEDGER.BRID_REF','=',$BRID_REF)
                    ->where('tbl_MST_SUBLEDGER.SGLID','=',$objRCPTHDR->CUSTMER_VENDOR_ID)
                    ->where('tbl_MST_SUBLEDGER.BELONGS_TO','=','Customer')
                    ->select('tbl_MST_SUBLEDGER.SGLCODE AS CODE','tbl_MST_SUBLEDGER.SLNAME AS NAME')
                    ->first();
                }




                else 
                {
                    $objRCPTCUSTVNDR = NULL;
                }

                $objRCPTCASHBANK =[];
                if(isset($objRCPTHDR->CASH_BANK_ID) && $objRCPTHDR->CASH_BANK_ID !=""){
                    $objRCPTCASHBANK = DB::table('TBL_MST_BANK')
                    ->where('TBL_MST_BANK.CYID_REF','=',$CYID_REF)
                    ->where('TBL_MST_BANK.BRID_REF','=',$BRID_REF)
                    ->where('TBL_MST_BANK.BID','=',$objRCPTHDR->CASH_BANK_ID)
                    ->select('TBL_MST_BANK.*')
                    ->first();
                }
                
                $objRCPTACCOUNT = DB::table('TBL_TRN_RECEIPT_ACCOUNT')  
                ->leftJoin('TBL_MST_GENERALLEDGER', 'TBL_TRN_RECEIPT_ACCOUNT.GLID_REF','=','TBL_MST_GENERALLEDGER.GLID')
                ->where('TBL_MST_GENERALLEDGER.CYID_REF','=',$CYID_REF)                   
                ->where('TBL_TRN_RECEIPT_ACCOUNT.RECEIPTID_REF','=',$id)
                ->select('TBL_TRN_RECEIPT_ACCOUNT.*','TBL_MST_GENERALLEDGER.*')
                ->get()
                ->toArray();
                
                $objCount2 = count($objRCPTACCOUNT);

                $objRCPTCCD = DB::table('TBL_TRN_RECEIPT_CCD')                    
                ->where('TBL_TRN_RECEIPT_CCD.RECEIPTID_REF','=',$id)
                ->get()->toArray();
                
                $objCount3 = count($objRCPTCCD);                
        
                $objRights = DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_USERROLMAP.BRID_REF','=',Auth::user()->BRID_REF)
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();
                
            
                $FormId     =   $this->form_id;

                $objgeneralledger   =   $this->getGeneralLedger();
        
                $objCostCenter = DB::table('TBL_MST_COSTCENTER')
                ->where('CYID_REF','=',$CYID_REF)
                ->where('STATUS','=','A')
                ->select('TBL_MST_COSTCENTER.*')
                ->get();

                $objBank = DB::table('TBL_MST_BANK')
                ->leftJoin('TBL_MST_CITY', 'TBL_MST_CITY.CITYID','=','TBL_MST_BANK.CITYID_REF')
                ->leftJoin('TBL_MST_STATE', 'TBL_MST_STATE.STID','=','TBL_MST_BANK.STID_REF')
                ->leftJoin('TBL_MST_COUNTRY', 'TBL_MST_COUNTRY.CTRYID','=','TBL_MST_BANK.CTRYID_REF')
                ->join('TBL_MST_DOCNO_DEFINITION', 'TBL_MST_BANK.BANK_CASH','=','TBL_MST_DOCNO_DEFINITION.PREFIX_TYPE')
                ->where('TBL_MST_BANK.CYID_REF','=',$CYID_REF)
                ->where('TBL_MST_BANK.BRID_REF','=',$BRID_REF)
                ->where('TBL_MST_BANK.STATUS','=','A')
                ->select('TBL_MST_BANK.*','TBL_MST_DOCNO_DEFINITION.*','TBL_MST_CITY.NAME AS CITY','TBL_MST_STATE.NAME AS STATE','TBL_MST_COUNTRY.NAME  AS COUNTRY')
                ->get(); 

                $objlastdt          =   $this->getLastdt();

                $ActionStatus   =   "disabled";
                $objothcurrency = $this->GetCurrencyMaster(); 

            
            return view($this->view.$FormId.'view',compact(['FormId','objRights','objRCPTHDR','objRCPTINV','objRCPTACCOUNT','objRCPTCCD',
                'objBank','objCostCenter','objgeneralledger','objCount1','objCount2','objCount3','objRCPTCASHBANK','objRCPTCUSTVNDR',
                'objlastdt','ActionStatus','objGl','objRECEIPTTDS','objCount6','objothcurrency']));      

        }
     
    }

    public function update(Request $request){
        
        //$r_count1 = $request['Row_Count1'];
        if(isset($request['rowcount1'])){
        $r_count1 = count($request['rowcount1']);
        }else{
        $r_count1 = NULL;
        }
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count6 = $request['Row_Count6'];
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['DOCNO_ID_'.$i]) && $request['DOCNO_ID_'.$i] != '')
            {
                $req_data[$i] = [
                    'DOC_TYPE'                  => $request['Doc_Type_'.$i],
                    'DOCNO_ID'                  => $request['DOCNO_ID_'.$i],                    
                    'BALANCE_DUE'               => (!is_null($request['BALANCE_DUE_'.$i]) ? $request['BALANCE_DUE_'.$i] : 0),
                    'RECEIPT_AMT'               => (!is_null($request['RECEIPT_AMT_'.$i]) ? $request['RECEIPT_AMT_'.$i] : 0),
                    'REMARKS'                   => (isset($request['REMARKS_'.$i]) ? $request['REMARKS_'.$i] : ''),
                    'BRID_REF'                  => (isset($request['BRID_REF_'.$i]) ? $request['BRID_REF_'.$i] : ''),
                ];
            }
        }

        if(isset($req_data))
        { 
            $wrapped_links["INVOCE"] = $req_data; 
            $INVOICE = ArrayToXml::convert($wrapped_links);
        }
        else
        {
            $INVOICE = NULL; 
        }

        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset($request['GLID_REF_'.$i]) && $request['GLID_REF_'.$i] != '')
            {
                $reqdata2[$i] = [
                    'GLID_REF'                  => $request['GLID_REF_'.$i],
                    'AMOUNT'                    => (!is_null($request['AMOUNT_'.$i]) ? $request['AMOUNT_'.$i] : 0),
                    'IGST'                      => (!is_null($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST'                      => (!is_null($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST'                      => (!is_null($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'CCID_REF'                  => (isset($request['CCID_REF_'.$i]) ? $request['CCID_REF_'.$i] : ''),
                    'TYPE'                      => $request['TYPE_'.$i],
                ];
            }
            
        }
        if(isset($reqdata2))
        { 
            $wrapped_links2["ACCOUNT"] = $reqdata2;
            $ACCOUNT = ArrayToXml::convert($wrapped_links2);
        }
        else
        {
            $ACCOUNT = NULL; 
        }
        

        for ($i=0; $i<=$r_count2; $i++)
        {
            if(isset($request['GLID_'.$i]) && $request['GLID_'.$i] != '')
            {
                $reqdata3[$i] = [
                    'GLID_REF'                  => $request['GLID_'.$i],
                    'CCID_REF'                  => (isset($request['CCID_'.$i]) ? $request['CCID_'.$i] : ''),
                    'AMT'                       => (!is_null($request['GL_AMT_'.$i]) ? $request['GL_AMT_'.$i] : 0),
                ];
            }
        }
        if(isset($reqdata3))
        { 
            $wrapped_links3["CCD"] = $reqdata3; 
            $CCD = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $CCD = NULL; 
        }
        
        for ($i=0; $i<=$r_count6; $i++){
            if(isset($request['TDSID_REF_'.$i]) && $request['TDSID_REF_'.$i] !=''){
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
            $wrapped_links6["TDS"] = $reqdata6; 
            $TDS = ArrayToXml::convert($wrapped_links6);
        }
        else{
            $TDS = NULL; 
        } 

        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID_REF = Auth::user()->USERID;   
        $ACTION = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $UPDATE  =  Date('Y-m-d');
        $UPTIME =   Date('h:i:s.u');

        $RECEIPT_NO                       = $request['RECEIPT_NO'] ? $request['RECEIPT_NO'] : NULL;
        $RECEIPT_DT                       = $request['RECEIPT_DT'];
        $RECEIPT_FOR                      = $request['hdnreceiptfor']? $request['hdnreceiptfor'] : NULL;
        $CUSTMER_VENDOR_ID                = $request['CUSTMER_VENDOR_ID']? $request['CUSTMER_VENDOR_ID'] : NULL;
        $RECEIPT_TYPE                     = $request['RECEIPT_TYPE']? $request['RECEIPT_TYPE'] : NULL;
        $RECEIPT_ON_ACCOUNT               = (isset($request['chk_RcptAccount'])!="true" ? 0 : 1);
        $CASH_BANK_ID                     = (isset($request['chk_Account'])=="true" ? $request['BANK_CASH_ID'] : $request['CASH_BANK_ID']);
        $TRANSACTION_DT                   = $request['TRANSACTION_DT'];
        $INSTRUMENT_TYPE                  = $request['INSTRUMENT_TYPE'] ? $request['INSTRUMENT_TYPE'] : NULL;
        $INSTRUMENT_NO                    = $request['INSTRUMENT_NO'] ? $request['INSTRUMENT_NO'] : 0;
        $BANK_CHARGE                      = (!is_null($request['BANK_CHARGE']) ? $request['BANK_CHARGE'] : 0);
        $NARRATION                        = (!is_null($request['NARRATION']) ? $request['NARRATION'] : '');
        $CENTERLIZED_RECEIPT              = (isset($request['CENTERLIZED_RECEIPT'])!="true" ? 0 : 1);
        $REMARKS                          = '';
        $TOAL_AMOUNT                      = $request['tot_amt1'] ? $request['tot_amt1'] : 0;
        $AMOUNT                           = (!is_null($request['AMOUNT']) ? $request['AMOUNT'] : 0);

        $ROUNDOFF_GLID                   = $request['GLID_REF_ROUNDOFF']?$request['GLID_REF_ROUNDOFF']:0;
        $ROUNDOFF_AMT                    = $request['ROUNDOFF_AMT']?$request['ROUNDOFF_AMT']:0;
        $ROUNDOFF_MODE                   = $request['ROUNDOFF_MODE'] ? $request['ROUNDOFF_MODE'] : NULL; 
        $TDS_CHECK                       = (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);
        $BANK_AMOUNT                     = $request['BANK_AMOUNT'] ? $request['BANK_AMOUNT'] : 0;
        $BANK_REMARKS                    = $request['BANK_REMARKS']? $request['BANK_REMARKS'] : NULL;

        $PDC_STATUS                      = (isset($request['PDC_STATUS'])!="true" ? 0 : 1);
        $PDC_DT                          = $request['PDC_DT']; 
        $CLEAR_PDC_STATUS                = (isset($request['CLEAR_PDC_STATUS'])!="true" ? 0 : 1);
        $CLEAR_PDC_DT                    = $request['CLEAR_PDC_DT']; 
        $RECORD_TYPE                    = $request['PDC_ECH_TYPE']? $request['PDC_ECH_TYPE'] : NULL;
        $FC 			                 = (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF 		                 = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT 		                 = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : 0;
       
        $log_data = [ 
            $RECEIPT_NO,            $RECEIPT_DT,        $RECEIPT_FOR,       $CUSTMER_VENDOR_ID,     $RECEIPT_TYPE,  $RECEIPT_ON_ACCOUNT,
            $CASH_BANK_ID,          $TRANSACTION_DT,    $INSTRUMENT_TYPE,   $INSTRUMENT_NO,         $BANK_CHARGE,   $NARRATION,
            $CENTERLIZED_RECEIPT,   $REMARKS,           $TOAL_AMOUNT,       $AMOUNT,                $CYID_REF,      $BRID_REF,
            $FYID_REF,              $VTID_REF,          $INVOICE,           $ACCOUNT,               $CCD,           $TDS,
            $USERID_REF,            $UPDATE,            $UPTIME,            $ACTION,                $IPADDRESS,     $ROUNDOFF_GLID,
            $ROUNDOFF_AMT,          $ROUNDOFF_MODE,     $TDS_CHECK,         $BANK_AMOUNT,           $BANK_REMARKS,  $PDC_STATUS,
            $PDC_DT,                $CLEAR_PDC_STATUS,  $CLEAR_PDC_DT,      $RECORD_TYPE,           $FC,            $CRID_REF,
            $CONVFACT
        ];
        
        $sp_result = DB::select('EXEC SP_RECEIPT_UP ?,?,?,?,?,?,    ?,?,?,?,?,?,    ?,?,?,?,?,?,    ?,?,?,?,?,?,    ?,?,?,?,?,?,    ?,?,?,?,?,?,    ?,?,?,?,?,?, ?', $log_data);  

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $RECEIPT_NO. ' Sucessfully Updated.']);

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

        if(!empty($sp_listing_result)){
            foreach ($sp_listing_result as $key=>$valueitem){  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$valueitem->LAVELS;
            }
        }
   
        //$r_count1 = $request['Row_Count1'];
        if(isset($request['rowcount1'])){
            $r_count1 = count($request['rowcount1']);
            }else{
            $r_count1 = NULL;
            }
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count6 = $request['Row_Count6'];
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['DOCNO_ID_'.$i]) && $request['DOCNO_ID_'.$i] != '')
            {
                $req_data[$i] = [
                    'DOC_TYPE'                  => $request['Doc_Type_'.$i],
                    'DOCNO_ID'                  => $request['DOCNO_ID_'.$i],                    
                    'BALANCE_DUE'               => (!is_null($request['BALANCE_DUE_'.$i]) ? $request['BALANCE_DUE_'.$i] : 0),
                    'RECEIPT_AMT'               => (!is_null($request['RECEIPT_AMT_'.$i]) ? $request['RECEIPT_AMT_'.$i] : 0),
                    'REMARKS'                   => (isset($request['REMARKS_'.$i]) ? $request['REMARKS_'.$i] : ''),
                    'BRID_REF'                  => (isset($request['BRID_REF_'.$i]) ? $request['BRID_REF_'.$i] : ''),
                ];
            }
        }

        if(isset($req_data))
        { 
            $wrapped_links["INVOCE"] = $req_data; 
            $INVOICE = ArrayToXml::convert($wrapped_links);
        }
        else
        {
            $INVOICE = NULL; 
        }

        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset($request['GLID_REF_'.$i]) && $request['GLID_REF_'.$i] != '')
            {
                $reqdata2[$i] = [
                    'GLID_REF'                  => $request['GLID_REF_'.$i],
                    'AMOUNT'                    => (!is_null($request['AMOUNT_'.$i]) ? $request['AMOUNT_'.$i] : 0),
                    'IGST'                      => (!is_null($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST'                      => (!is_null($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST'                      => (!is_null($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'CCID_REF'                  => (isset($request['CCID_REF_'.$i]) ? $request['CCID_REF_'.$i] : ''),
                    'TYPE'                      => $request['TYPE_'.$i],
                ];
            }
            
        }
        if(isset($reqdata2))
        { 
            $wrapped_links2["ACCOUNT"] = $reqdata2;
            $ACCOUNT = ArrayToXml::convert($wrapped_links2);
        }
        else
        {
            $ACCOUNT = NULL; 
        }
        

        for ($i=0; $i<=$r_count2; $i++)
        {
            if(isset($request['GLID_'.$i]) && $request['GLID_'.$i] != '')
            {
                $reqdata3[$i] = [
                    'GLID_REF'                  => $request['GLID_'.$i],
                    'CCID_REF'                  => (isset($request['CCID_'.$i]) ? $request['CCID_'.$i] : ''),
                    'AMT'                       => (!is_null($request['GL_AMT_'.$i]) ? $request['GL_AMT_'.$i] : 0),
                ];
            }
        }
        if(isset($reqdata3))
        { 
            $wrapped_links3["CCD"] = $reqdata3; 
            $CCD = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $CCD = NULL; 
        }
        
        for ($i=0; $i<=$r_count6; $i++){
            if(isset($request['TDSID_REF_'.$i]) && $request['TDSID_REF_'.$i] !=''){
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
            $wrapped_links6["TDS"] = $reqdata6; 
            $TDS = ArrayToXml::convert($wrapped_links6);
        }
        else{
            $TDS = NULL; 
        }

        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID_REF = Auth::user()->USERID;   
        $ACTION = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $UPDATE  =  Date('Y-m-d');
        $UPTIME =   Date('h:i:s.u');

        $RECEIPT_NO                       = $request['RECEIPT_NO'] ? $request['RECEIPT_NO'] : NULL;
        $RECEIPT_DT                       = $request['RECEIPT_DT'];
        $RECEIPT_FOR                      = $request['hdnreceiptfor']? $request['hdnreceiptfor'] : NULL;
        $CUSTMER_VENDOR_ID                = $request['CUSTMER_VENDOR_ID']? $request['CUSTMER_VENDOR_ID'] : NULL;
        $RECEIPT_TYPE                     = $request['RECEIPT_TYPE']? $request['RECEIPT_TYPE'] : NULL;
        $RECEIPT_ON_ACCOUNT               = (isset($request['chk_RcptAccount'])!="true" ? 0 : 1);
        $CASH_BANK_ID                     = (isset($request['chk_Account'])=="true" ? $request['BANK_CASH_ID'] : $request['CASH_BANK_ID']);
        $TRANSACTION_DT                   = $request['TRANSACTION_DT'];
        $INSTRUMENT_TYPE                  = $request['INSTRUMENT_TYPE'] ? $request['INSTRUMENT_TYPE'] : NULL;
        $INSTRUMENT_NO                    = $request['INSTRUMENT_NO'] ? $request['INSTRUMENT_NO'] : 0;
        $BANK_CHARGE                      = (!is_null($request['BANK_CHARGE']) ? $request['BANK_CHARGE'] : 0);
        $NARRATION                        = (!is_null($request['NARRATION']) ? $request['NARRATION'] : '');
        $CENTERLIZED_RECEIPT              = (isset($request['CENTERLIZED_RECEIPT'])!="true" ? 0 : 1);
        $REMARKS                          = '';
        $TOAL_AMOUNT                      = $request['tot_amt1'] ? $request['tot_amt1'] : 0;
        $AMOUNT                           = (!is_null($request['AMOUNT']) ? $request['AMOUNT'] : 0);

        $ROUNDOFF_GLID                   = $request['GLID_REF_ROUNDOFF']?$request['GLID_REF_ROUNDOFF']:0;
        $ROUNDOFF_AMT                    = $request['ROUNDOFF_AMT']?$request['ROUNDOFF_AMT']:0;
        $ROUNDOFF_MODE                   = $request['ROUNDOFF_MODE'] ? $request['ROUNDOFF_MODE'] : NULL; 
        $TDS_CHECK                       = (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);
        $BANK_AMOUNT                     = $request['BANK_AMOUNT'] ? $request['BANK_AMOUNT'] : 0;
        $BANK_REMARKS                    = $request['BANK_REMARKS']? $request['BANK_REMARKS'] : NULL;

        $PDC_STATUS                      = (isset($request['PDC_STATUS'])!="true" ? 0 : 1);
        $PDC_DT                          = $request['PDC_DT']; 
        $CLEAR_PDC_STATUS                = (isset($request['CLEAR_PDC_STATUS'])!="true" ? 0 : 1);
        $CLEAR_PDC_DT                    = $request['CLEAR_PDC_DT']; 
        $RECORD_TYPE                    = $request['PDC_ECH_TYPE']? $request['PDC_ECH_TYPE'] : NULL;
        $FC 			                 = (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF 		                 = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT 		                 = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : 0;
       
        $log_data = [ 
            $RECEIPT_NO,            $RECEIPT_DT,        $RECEIPT_FOR,       $CUSTMER_VENDOR_ID,     $RECEIPT_TYPE,  $RECEIPT_ON_ACCOUNT,
            $CASH_BANK_ID,          $TRANSACTION_DT,    $INSTRUMENT_TYPE,   $INSTRUMENT_NO,         $BANK_CHARGE,   $NARRATION,
            $CENTERLIZED_RECEIPT,   $REMARKS,           $TOAL_AMOUNT,       $AMOUNT,                $CYID_REF,      $BRID_REF,
            $FYID_REF,              $VTID_REF,          $INVOICE,           $ACCOUNT,               $CCD,           $TDS,
            $USERID_REF,            $UPDATE,            $UPTIME,            $ACTION,                $IPADDRESS,     $ROUNDOFF_GLID,
            $ROUNDOFF_AMT,          $ROUNDOFF_MODE,     $TDS_CHECK,         $BANK_AMOUNT,           $BANK_REMARKS,  $PDC_STATUS,
            $PDC_DT,                $CLEAR_PDC_STATUS,  $CLEAR_PDC_DT,      $RECORD_TYPE,           $FC,            $CRID_REF,
            $CONVFACT
        ];
        
        $sp_result = DB::select('EXEC SP_RECEIPT_UP ?,?,?,?,?,?,    ?,?,?,?,?,?,    ?,?,?,?,?,?,    ?,?,?,?,?,?,    ?,?,?,?,?,?,    ?,?,?,?,?,?,    ?,?,?,?,?,?, ?', $log_data);  

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $RECEIPT_NO. ' Sucessfully Approved.']);

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
        $TABLE      =   "TBL_TRN_RECEIPT_HDR";
        $FIELD      =   "RECEIPTID";
        $ACTIONNAME     = $Approvallevel;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
            
        $log_data = [ 
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_RECEIPT ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
        
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
        $TABLE      =   "TBL_TRN_RECEIPT_HDR";
        $FIELD      =   "RECEIPTID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_RECEIPT_HDR',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_RECEIPT_INVOICE',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_RECEIPT_ACCOUNT',
        ];
        $req_data[3]=[
            'NT'  => 'TBL_TRN_RECEIPT_CCD',
        ];

        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $pb_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_RECEIPT  ?,?,?,?, ?,?,?,?, ?,?,?,?', $pb_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_RECEIPT_HDR')->where('RECEIPTID','=',$id)->first();

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
        
		$image_path         =   "docs/company".$CYID_REF."/RECEIPTENTRY";     
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
   

    public function codeduplicate(Request $request){

        $RECEIPT_NO  =   trim($request['RECEIPT_NO']);
        $objLabel = DB::table('TBL_TRN_RECEIPT_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('RECEIPT_NO','=',$RECEIPT_NO)
        ->select('RECEIPTID')->first();

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

        return  DB::select('SELECT MAX(RECEIPT_DT) RECEIPT_DT FROM TBL_TRN_RECEIPT_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
    }



    public function get_gl_detail(Request $request) {    

        //dd($request->all()); 
         
             $Status = "A";
             $CYID_REF = Auth::user()->CYID_REF;
             $BRID_REF = Session::get('BRID_REF');
             $FYID_REF = Session::get('FYID_REF');
       
         
             $objGl = DB::table('TBL_MST_GENERALLEDGER')
             ->where('CYID_REF','=',Auth::user()->CYID_REF)
             ->where('BRID_REF','=',Session::get('BRID_REF'))
             ->where('STATUS','=',$Status)
             ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
             ->where('SUBLEDGER','!=',1)
             ->select('GLID','GLCODE','GLNAME') 
             ->get()    
             ->toArray();
         
           // dd($objGl); 
              
         
             if(!empty($objGl)){        
                 foreach ($objGl as $index=>$dataRow){
         
         
                     $row = '';
                     $row = $row.'<tr ><td style="text-align:center; width:10%">';
                     $row = $row.'<input type="checkbox" name="getgl[]"  id="getglcode_'.$dataRow->GLID.'" class="clsspid_gl" 
                     value="'.$dataRow->GLID.'"/>             
                     </td>           
                     <td style="width:30%;">'.$dataRow->GLCODE;
                     $row = $row.'<input type="hidden" id="txtgetglcode_'.$dataRow->GLID.'" data-code="'.$dataRow->GLCODE.'-'.$dataRow->GLNAME.'"   data-desc="'.$dataRow->GLNAME.'" 
                     value="'.$dataRow->GLID.'"/></td>
         
                     <td style="width:60%;">'.$dataRow->GLNAME.'</td>
           
         
                    </tr>';
                     echo $row;
                 }
         
                 }else{
                     echo '<tr><td colspan="2">Record not found.</td></tr>';
                 }
         
                 exit();
         
         
         
            }

            public function getTDSApplicability(Request $request){
                $Status = "A";
                $SLID_REF   =   $request['id'];
        
                $ObjVendor  =   DB::table('TBL_MST_VENDOR')
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
        
                $SLID_REF   =   $request['id'];
                $BRID_REF   =   Session::get('BRID_REF');
                
                $sp_param = [ 
                    $SLID_REF,$BRID_REF
                ];  
            
                $sp_result = DB::select('EXEC SP_GET_VENDOR_TDSDETAILS ?,?', $sp_param);
                
                if(!empty($sp_result)){
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
                
                $TaxStatus  =   DB::table('TBL_MST_VENDOR')
                                ->where('CYID_REF','=',$CYID_REF)
                                ->where('BRID_REF','=',$BRID_REF)
                                ->where('SLID_REF','=',$SLID_REF)
                                ->select('EXE_GST')->first()->EXE_GST;
        
                echo $TaxStatus;
            }


//=====================================TDS METHODS FOR CUSTOMER STARTS HERE====================================================

public function getTDSApplicability_customer(Request $request){
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
    
public function getTDSDetails_customer(Request $request){
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

public function getTaxStatus_customer(Request $request){
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

//=====================================TDS METHODS FOR CUSTOMER ENDS HERE====================================================

public function getBalance(Request $request){
	$Status     =   "A";
	$ID   =   $request['id'];
    $CYID_REF   =   Auth::user()->CYID_REF;
    $BRID_REF   =   Session::get('BRID_REF');
    $FYID_REF   =   Session::get('FYID_REF');
    
    $TaxStatus  =   DB::table('TBL_MST_GLOPENING_LEDGER')
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF)
                    ->where('GLID_REF','=',$ID)
                    ->where('STATUS','=',$Status)
                    ->select('GLDR_CLOSING','GLCR_CLOSING')->first();

                

                    //dd($TaxStatus); 

    if($TaxStatus){
        echo $Balance=$TaxStatus->GLDR_CLOSING-$TaxStatus->GLCR_CLOSING;            

    }else{
        echo $Balance='0.00';
    }
	
}





}
