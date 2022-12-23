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

class TrnFrm301Controller extends Controller{

    protected $form_id  = 301;
    protected $vtid_ref = 391;
    protected $view     = "transactions.Accounts.PaymentEntry.trnfrm";
   
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

        $objDataList	=	DB::select("select hdr.PAYMENTID,hdr.PAYMENT_NO,hdr.PAYMENT_DT,hdr.PAYMENT_FOR,hdr.PAYMENT_TYPE,hdr.NARRATION,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.PAYMENTID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_PAYMENT_HDR hdr
                            on a.VID = hdr.PAYMENTID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            left join TBL_MST_SUBLEDGER sl ON hdr.CUSTMER_VENDOR_ID = sl.SGLID
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.PAYMENTID DESC ");

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

    public function add(){   
       
        $Status     = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
 
        $objlastdt          =   $this->getLastdt();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_PAYMENT_HDR',
            'HDR_ID'=>'PAYMENTID',
            'HDR_DOC_NO'=>'PAYMENT_NO',
            'HDR_DOC_DT'=>'PAYMENT_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

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

    

  
        $objothcurrency = $this->GetCurrencyMaster(); 
        $FormId     =   $this->form_id;
        
        return view($this->view.$FormId.'add',compact(['FormId','objlastdt','objgeneralledger','objCostCenter','objBank','doc_req','docarray','objothcurrency']));       
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


    public function getsubledger_customer(Request $request){
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
                $row = $row.'<tr id="">             

                <td style="text-align:center; width:10%">';
                $row = $row.'<input type="checkbox" name="customer_vendorid[]"  id="custid_'.$dataRow->SGLID.'" class="clscustid" 
                value="'.$dataRow->SGLID.'"/></td> 
                <td style="width:30%;">'.$dataRow->SGLCODE;
                $row = $row.'<input type="hidden" id="txtcustid_'.$dataRow->SGLID.'" data-desc="'.$dataRow->SGLCODE .'" 
                data-desc2="'.$dataRow->SLNAME .'" value="'.$dataRow->SGLID.'"/></td>

                
                <td style="width:60%;">'.$dataRow->SLNAME.'</td></tr>';
                echo $row;
            }
    
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    
        }



        
    public function getsubledger_employee(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $CODE = $request['CODE'];
        $NAME = $request['NAME'];
        $EMPVALUE = $request['EMPVALUE'];
    
        $sp_popup = [
            $CYID_REF, $BRID_REF,$CODE,$NAME
        ]; 
        
            $ObjData =  DB::table('TBL_MST_SUBLEDGER')
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('BELONGS_TO','=',$EMPVALUE)
            ->where('STATUS','=','A')
            ->get();

            if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){
            
                $row = '';
                $row = $row.'<tr id="">             

                <td style="text-align:center; width:10%">';
                $row = $row.'<input type="checkbox" name="customer_vendorid[]"  id="custid_'.$dataRow->SGLID.'" class="clscustid" 
                value="'.$dataRow->SGLID.'"/></td> 
                <td style="width:30%;">'.$dataRow->SGLCODE;
                $row = $row.'<input type="hidden" id="txtcustid_'.$dataRow->SGLID.'" data-desc="'.$dataRow->SGLCODE .'" 
                data-desc2="'.$dataRow->SLNAME .'" value="'.$dataRow->SGLID.'"/></td>

                
                <td style="width:60%;">'.$dataRow->SLNAME.'</td></tr>';
                echo $row;
            }
    
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    
        }



    public function getsubledger_vendor(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $CODE = $request['CODE'];
        $NAME = $request['NAME'];
    
        $sp_popup = [
            $CYID_REF, $BRID_REF,$CODE,$NAME
        ]; 
        
            $ObjData = DB::select('EXEC sp_get_vendor_popup_enquiry ?,?,?,?', $sp_popup);
    
            if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){
            
                $row = '';
                $row = $row.'<tr>     
                
                
                

                <td style="text-align:center; width:10%">';
                $row = $row.'<input type="checkbox" name="customer_vendorid[]"  id="custid_'.$dataRow->SGLID.'" class="clscustid" 
                value="'.$dataRow->SGLID.'"/></td> 
                <td style="width:30%;">'.$dataRow->SGLCODE;
                $row = $row.'<input type="hidden" id="txtcustid_'.$dataRow->SGLID.'" data-desc="'.$dataRow->SGLCODE .'" 
                data-desc2="'.$dataRow->SLNAME .'" value="'.$dataRow->SGLID.'"/></td>

                
                <td style="width:60%;">'.$dataRow->SLNAME.'</td></tr>';
                echo $row;
            }
    
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
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

        $ObjData = DB::select('EXEC sp_trn_get_invoice_cust_vendor_wise ?,?,?,?,?', $sp_param);

       
       
        if(!empty($ObjData)){
                
            foreach ($ObjData as $index=>$dataRow){

                $INVOICE_DATE=isset($dataRow->SUPPLIERINVOICE_DT)? $dataRow->SUPPLIERINVOICE_DT:''; 
            
                $row = '';
                $row = $row.'<tr id="invoiceid_'.$dataRow->SOURCETYPE.'-'.$dataRow->ID.'-'.$dataRow->BRID_REF.'" class="clsinvoiceid">
                <td  style="width:10%; text-align: center;">
                <input type="checkbox" id="chkId'.$dataRow->SOURCETYPE.'-'.$dataRow->ID.'-'.$dataRow->BRID_REF.'"  
                value="'.$dataRow->ID.'" class="js-selectall1"  ></td> <td style="width:15%;">'.$dataRow->SOURCETYPE;
                $row = $row.'<input type="hidden" id="txtinvoiceid_'.$dataRow->SOURCETYPE.'-'.$dataRow->ID.'-'.$dataRow->BRID_REF.'" 
                data-desc="'.$dataRow->DOCNO.'" data-desc2="'.$dataRow->DOCDT.'" data-desc3="'.$dataRow->BRANCH.'" data-desc4="'.$dataRow->DOCAMT.'"
                data-desc5="'.$dataRow->BALANCEAMT.'" data-desc6="'.$dataRow->BRID_REF.'" data-desc7="'.$dataRow->SOURCETYPE.'"
                value="'.$dataRow->ID.'"/></td><td style="width:10%;">'.$dataRow->DOCNO.'</td>
                <td style="width:10%;">'.$dataRow->DOCDT.'</td><td style="width:15%;">'.$dataRow->BRANCH.'</td>
                <td style="width:7%;">'.$dataRow->DOCAMT.'</td> <td style="width:7%;">'.$dataRow->BALANCEAMT.'</td>
                <td style="width:8%;">'.$dataRow->SUPPLIERINVOICE.'</td> 
                <td style="width:8%;">'.$dataRow->DUEDAYS.'</td>
				<td style="width:10%;">'.$dataRow->SUPPLIERINVOICEDATE.'</td>
                
                
                </tr>';
                
                echo $row;
            }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        
            exit();
    
    } 

    public function save(Request $request) {
       // $r_count1 = $request['Row_Count1'];
        if(isset($request['rowcount1'])){
        $r_count1 = count($request['rowcount1']);
        }else{
        $r_count1 = NULL;
        }


        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count6 = $request['Row_Count4'];
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['DOCNO_ID_'.$i]) && $request['DOCNO_ID_'.$i] != '')
            {
                $req_data[$i] = [
                    'DOC_TYPE'                  => $request['Doc_Type_'.$i],
                    'DOCNO_ID'                  => $request['DOCNO_ID_'.$i],                    
                    'BALANCE_DUE'               => (isset($request['BALANCE_DUE_'.$i]) ? $request['BALANCE_DUE_'.$i] : 0),
                    'PAYMENT_AMT'               => (isset($request['PAYMENT_AMT_'.$i]) ? $request['PAYMENT_AMT_'.$i] : 0),
                    'REMARKS'                   => (isset($request['REMARKS_'.$i]) ? $request['REMARKS_'.$i] : ''),
                    'BRID_REF'                  => (isset($request['BRID_REF_'.$i]) ? $request['BRID_REF_'.$i] : ''),
                ];
            }
        }

        if(isset($req_data))
        { 
            $wrapped_links["INVOCE"] = $req_data; 
            $XMLINVOICE = ArrayToXml::convert($wrapped_links);
        }
        else
        {
            $XMLINVOICE = NULL; 
        }

        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset($request['GLID_REF_'.$i]) && $request['GLID_REF_'.$i] != '')
            {
                $reqdata2[$i] = [
                    'GLID_REF'                  => $request['GLID_REF_'.$i],
                    'AMOUNT'                    => (isset($request['AMOUNT_'.$i]) ? $request['AMOUNT_'.$i] : 0),
                    'ASSESSABLE_VALUE'          => (isset($request['ASSESSABLE_VALUE_'.$i]) ? $request['ASSESSABLE_VALUE_'.$i] : 0),
                    'IGST'                      => (isset($request['calIGST_'.$i]) ? $request['calIGST_'.$i] : 0),
                    'CGST'                      => (isset($request['calCGST_'.$i]) ? $request['calCGST_'.$i] : 0),
                    'SGST'                      => (isset($request['calSGST_'.$i]) ? $request['calSGST_'.$i] : 0),
                    'CCID_REF'                  => (isset($request['CCID_REF_'.$i]) ? $request['CCID_REF_'.$i] : ''),
                    'TYPE'                      => $request['TYPE_'.$i],
                ];
            }
            
        }
        if(isset($reqdata2))
        { 
            $wrapped_links2["ACCOUNT"] = $reqdata2;
            $XMLACCOUNT = ArrayToXml::convert($wrapped_links2);
        }
        else
        {
            $XMLACCOUNT = NULL; 
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
            $XMLCCD = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $XMLCCD = NULL; 
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

        $PAYMENT_NO                       = $request['PAYMENT_NO'];
        $PAYMENT_DT                       = $request['PAYMENT_DT'];
        $PAYMENT_FOR                      = $request['hdnpaymentfor'];
        $CUSTMER_VENDOR_ID                = $request['CUSTMER_VENDOR_ID'];
        $PAYMENT_TYPE                     = $request['PAYMENT_TYPE'];
        $PAYMENT_ON_ACCOUNT               = (isset($request['chk_PayAccount'])!="true" ? 0 : 1);
        $CASH_BANK_ID                     = (isset($request['chk_Account'])=="true" ? $request['BANK_CASH_ID'] : $request['CASH_BANK_ID']);
        $TRANSACTION_DT                   = $request['TRANSACTION_DT'];
        $INSTRUMENT_TYPE                  = $request['INSTRUMENT_TYPE'];
        $INSTRUMENT_NO                    = $request['INSTRUMENT_NO'];
        $BANK_CHARGE                      = (!is_null($request['BANK_CHARGE']) ? $request['BANK_CHARGE'] : 0);
        $NARRATION                        = (!is_null($request['NARRATION']) ? $request['NARRATION'] : '');
        $CENTERLIZED_PAYMENT              = (isset($request['CENTERLIZED_PAYMENT'])!="true" ? 0 : 1);
        $REMARKS                          = '';
        $CHALLAN_NO                       = '';
        $CHALLAN_DT                       = '';
        $BSR_CODE                         = '';
        $TDS_INTREST                      = 0;
        $TDS_OTH_CHARGE                   = 0;
        $TDS_LATE_FEE                     = 0;
        $TOAL_AMOUNT                      = $request['tot_amt1'];
        $AMOUNT                           = (!is_null($request['AMOUNT']) ? $request['AMOUNT'] : 0);

        $GLID_REF_ROUNDOFF               = $request['GLID_REF_ROUNDOFF'];
        $ROUNDOFF_AMT                    = $request['ROUNDOFF_AMT'];
        $ROUNDOFF_MODE                   = $request['ROUNDOFF_MODE']; 
        $TDS                             = (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);
        $BANK_AMOUNT                     = $request['BANK_AMOUNT'];
        $BANK_REMARKS                    = $request['BANK_REMARKS']; 

        $POID_REF                        = $request['POID_REF']; 
        $FC 							 = (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF 						 = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT 						 = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
 

        $log_data = [ 
            $PAYMENT_NO,$PAYMENT_DT,$PAYMENT_FOR,$CUSTMER_VENDOR_ID,$PAYMENT_TYPE,$PAYMENT_ON_ACCOUNT,
            $CASH_BANK_ID,$TRANSACTION_DT,$INSTRUMENT_TYPE,$INSTRUMENT_NO,$BANK_CHARGE,$NARRATION,$CENTERLIZED_PAYMENT,
            $REMARKS,$CHALLAN_NO,$CHALLAN_DT,$BSR_CODE,$TDS_INTREST,$TDS_OTH_CHARGE,$TDS_LATE_FEE,$TOAL_AMOUNT,
            $CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$XMLINVOICE,$XMLACCOUNT,$XMLTDSD ,$XMLCCD,$USERID,
            Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$AMOUNT,$GLID_REF_ROUNDOFF,$ROUNDOFF_AMT,$ROUNDOFF_MODE,$TDS,$BANK_AMOUNT,$BANK_REMARKS,$POID_REF,$FC,$CRID_REF,$CONVFACT
        ]; 
        
        // dd($log_data);

        $sp_result = DB::select('EXEC SP_PAYMENT_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);  

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

            $objPAYHDR = DB::table('TBL_TRN_PAYMENT_HDR')
                ->where('TBL_TRN_PAYMENT_HDR.FYID_REF','=',$FYID_REF)
                ->where('TBL_TRN_PAYMENT_HDR.CYID_REF','=',$CYID_REF)
                ->where('TBL_TRN_PAYMENT_HDR.BRID_REF','=',$BRID_REF)
                ->where('TBL_TRN_PAYMENT_HDR.PAYMENTID','=',$id)
                ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_PAYMENT_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
                ->leftJoin('TBL_TRN_PROR01_HDR', 'TBL_TRN_PROR01_HDR.POID','=','TBL_TRN_PAYMENT_HDR.POID_REF')
                ->select('TBL_TRN_PAYMENT_HDR.*','TBL_TRN_PROR01_HDR.PO_NO','TBL_TRN_PROR01_HDR.PO_DT','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
                ->first();
              

     


                if($objPAYHDR->ROUNDOFF_GLID!=''){
                $objGl = DB::table('TBL_MST_GENERALLEDGER')
                ->where('GLID','=',$objPAYHDR->ROUNDOFF_GLID)
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
           
                $objPAYMENTTDS = DB::select('EXEC SP_GET_PAYMENT_TDS ?', $log_data);
                $objCount6 = count($objPAYMENTTDS);

                    
            $objPAYINV =[];
            if(isset($objPAYHDR) && !empty($objPAYHDR)){

                $log_data = [ 
                    $id,$BRID_REF,$CYID_REF,$objPAYHDR->CUSTMER_VENDOR_ID,$objPAYHDR->CENTERLIZED_PAYMENT,$objPAYHDR->PAYMENT_FOR
                ];                
                $objPAYINV = DB::select('EXEC SP_GET_PAYMENT_INVOICE ?,?,?,?,?,?', $log_data);
            }


            $objCount1 = count($objPAYINV); 
              
                if(isset($objPAYHDR->PAYMENT_FOR) && $objPAYHDR->PAYMENT_FOR == 'Vendor')
                {
                    $objPAYCUSTVNDR = DB::table('tbl_MST_SUBLEDGER')
                    ->where('tbl_MST_SUBLEDGER.CYID_REF','=',$CYID_REF)
                    ->where('tbl_MST_SUBLEDGER.BRID_REF','=',$BRID_REF)
                    ->where('tbl_MST_SUBLEDGER.SGLID','=',$objPAYHDR->CUSTMER_VENDOR_ID)
                    ->where('tbl_MST_SUBLEDGER.BELONGS_TO','=','Vendor')
                    ->select('tbl_MST_SUBLEDGER.SGLCODE AS CODE','tbl_MST_SUBLEDGER.SLNAME AS NAME')
                    ->first();
                }
                else if(isset($objPAYHDR->PAYMENT_FOR) && $objPAYHDR->PAYMENT_FOR == 'Customer')
                {
                    $objPAYCUSTVNDR = DB::table('tbl_MST_SUBLEDGER')
                    ->where('tbl_MST_SUBLEDGER.CYID_REF','=',$CYID_REF)
                    ->where('tbl_MST_SUBLEDGER.BRID_REF','=',$BRID_REF)
                    ->where('tbl_MST_SUBLEDGER.SGLID','=',$objPAYHDR->CUSTMER_VENDOR_ID)
                    ->where('tbl_MST_SUBLEDGER.BELONGS_TO','=','Customer')
                    ->select('tbl_MST_SUBLEDGER.SGLCODE AS CODE','tbl_MST_SUBLEDGER.SLNAME AS NAME')
                    ->first();
                }
                else 
                {
                    $objPAYCUSTVNDR = NULL;
                }

                
               
            $objPAYCASHBANK =[];
            if(isset($objPAYHDR->CASH_BANK_ID) && $objPAYHDR->CASH_BANK_ID !=""){
                $objPAYCASHBANK = DB::table('TBL_MST_BANK')
                ->where('TBL_MST_BANK.CYID_REF','=',$CYID_REF)
                ->where('TBL_MST_BANK.BRID_REF','=',$BRID_REF)
                ->where('TBL_MST_BANK.BID','=',$objPAYHDR->CASH_BANK_ID)
                ->select('TBL_MST_BANK.*')
                ->first();
            }
            
                
                $objPAYACCOUNT = DB::table('TBL_TRN_PAYMENT_ACCOUNT')  
                ->leftJoin('TBL_MST_GENERALLEDGER', 'TBL_TRN_PAYMENT_ACCOUNT.GLID_REF','=','TBL_MST_GENERALLEDGER.GLID')
                ->where('TBL_MST_GENERALLEDGER.CYID_REF','=',$CYID_REF)                   
                ->where('TBL_TRN_PAYMENT_ACCOUNT.PAYMENTID_REF','=',$id)
                ->select('TBL_TRN_PAYMENT_ACCOUNT.*','TBL_MST_GENERALLEDGER.*')
                ->get()
                ->toArray();
                
                $objCount2 = count($objPAYACCOUNT);

                $objPAYCCD = DB::table('TBL_TRN_PAYMENT_CCD')                    
                ->where('TBL_TRN_PAYMENT_CCD.PAYMENTID_REF','=',$id)
                ->get()->toArray();
                
                $objCount3 = count($objPAYCCD);                
        
                $objRights = DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_USERROLMAP.BRID_REF','=',Auth::user()->BRID_REF)
                                ->where('TBL_MST_USERROLMAP.FYID_REF','=',Auth::user()->FYID_REF)
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
            
            return view($this->view.$FormId.'edit',compact(['FormId','objRights','objPAYHDR','objPAYINV','objPAYACCOUNT','objPAYCCD',
                'objBank','objCostCenter','objgeneralledger','objCount1','objCount2','objCount3','objPAYCASHBANK','objPAYCUSTVNDR',
                'objlastdt','ActionStatus','objGl','objPAYMENTTDS','objCount6','objothcurrency']));      

        }
     
    }


    public function view($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){

 

            $objPAYHDR = DB::table('TBL_TRN_PAYMENT_HDR')
                ->where('TBL_TRN_PAYMENT_HDR.FYID_REF','=',$FYID_REF)
                ->where('TBL_TRN_PAYMENT_HDR.CYID_REF','=',$CYID_REF)
                ->where('TBL_TRN_PAYMENT_HDR.BRID_REF','=',$BRID_REF)
                ->where('TBL_TRN_PAYMENT_HDR.PAYMENTID','=',$id)
                ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_PAYMENT_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
                ->leftJoin('TBL_TRN_PROR01_HDR', 'TBL_TRN_PROR01_HDR.POID','=','TBL_TRN_PAYMENT_HDR.POID_REF')
                ->select('TBL_TRN_PAYMENT_HDR.*','TBL_TRN_PROR01_HDR.PO_NO','TBL_TRN_PROR01_HDR.PO_DT','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
                ->first();

                if($objPAYHDR->ROUNDOFF_GLID!=''){
                $objGl = DB::table('TBL_MST_GENERALLEDGER')
                ->where('GLID','=',$objPAYHDR->ROUNDOFF_GLID)
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
           
                $objPAYMENTTDS = DB::select('EXEC SP_GET_PAYMENT_TDS ?', $log_data);
                $objCount6 = count($objPAYMENTTDS);

                    
            $objPAYINV =[];
            if(isset($objPAYHDR) && !empty($objPAYHDR)){

                $log_data = [ 
                    $id,$BRID_REF,$CYID_REF,$objPAYHDR->CUSTMER_VENDOR_ID,$objPAYHDR->CENTERLIZED_PAYMENT,$objPAYHDR->PAYMENT_FOR
                ];                
                $objPAYINV = DB::select('EXEC SP_GET_PAYMENT_INVOICE ?,?,?,?,?,?', $log_data);
            }


            $objCount1 = count($objPAYINV); 
              
                if(isset($objPAYHDR->PAYMENT_FOR) && $objPAYHDR->PAYMENT_FOR == 'Vendor')
                {
                    $objPAYCUSTVNDR = DB::table('tbl_MST_SUBLEDGER')
                    ->where('tbl_MST_SUBLEDGER.CYID_REF','=',$CYID_REF)
                    ->where('tbl_MST_SUBLEDGER.BRID_REF','=',$BRID_REF)
                    ->where('tbl_MST_SUBLEDGER.SGLID','=',$objPAYHDR->CUSTMER_VENDOR_ID)
                    ->where('tbl_MST_SUBLEDGER.BELONGS_TO','=','Vendor')
                    ->select('tbl_MST_SUBLEDGER.SGLCODE AS CODE','tbl_MST_SUBLEDGER.SLNAME AS NAME')
                    ->first();
                }
                else if(isset($objPAYHDR->PAYMENT_FOR) && $objPAYHDR->PAYMENT_FOR == 'Customer')
                {
                    $objPAYCUSTVNDR = DB::table('tbl_MST_SUBLEDGER')
                    ->where('tbl_MST_SUBLEDGER.CYID_REF','=',$CYID_REF)
                    ->where('tbl_MST_SUBLEDGER.BRID_REF','=',$BRID_REF)
                    ->where('tbl_MST_SUBLEDGER.SGLID','=',$objPAYHDR->CUSTMER_VENDOR_ID)
                    ->where('tbl_MST_SUBLEDGER.BELONGS_TO','=','Customer')
                    ->select('tbl_MST_SUBLEDGER.SGLCODE AS CODE','tbl_MST_SUBLEDGER.SLNAME AS NAME')
                    ->first();
                }
                else 
                {
                    $objPAYCUSTVNDR = NULL;
                }

                
               
            $objPAYCASHBANK =[];
            if(isset($objPAYHDR->CASH_BANK_ID) && $objPAYHDR->CASH_BANK_ID !=""){
                $objPAYCASHBANK = DB::table('TBL_MST_BANK')
                ->where('TBL_MST_BANK.CYID_REF','=',$CYID_REF)
                ->where('TBL_MST_BANK.BRID_REF','=',$BRID_REF)
                ->where('TBL_MST_BANK.BID','=',$objPAYHDR->CASH_BANK_ID)
                ->select('TBL_MST_BANK.*')
                ->first();
            }
                
                $objPAYACCOUNT = DB::table('TBL_TRN_PAYMENT_ACCOUNT')  
                ->leftJoin('TBL_MST_GENERALLEDGER', 'TBL_TRN_PAYMENT_ACCOUNT.GLID_REF','=','TBL_MST_GENERALLEDGER.GLID')
                ->where('TBL_MST_GENERALLEDGER.CYID_REF','=',$CYID_REF)                   
                ->where('TBL_TRN_PAYMENT_ACCOUNT.PAYMENTID_REF','=',$id)
                ->select('TBL_TRN_PAYMENT_ACCOUNT.*','TBL_MST_GENERALLEDGER.*')
                ->get()
                ->toArray();
                
                $objCount2 = count($objPAYACCOUNT);

                $objPAYCCD = DB::table('TBL_TRN_PAYMENT_CCD')                    
                ->where('TBL_TRN_PAYMENT_CCD.PAYMENTID_REF','=',$id)
                ->get()->toArray();
                
                $objCount3 = count($objPAYCCD);                
        
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
            
            return view($this->view.$FormId.'view',compact(['FormId','objRights','objPAYHDR','objPAYINV','objPAYACCOUNT','objPAYCCD',
                'objBank','objCostCenter','objgeneralledger','objCount1','objCount2','objCount3','objPAYCASHBANK','objPAYCUSTVNDR',
                'objlastdt','ActionStatus','objGl','objPAYMENTTDS','objCount6','objothcurrency']));      

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
        $r_count6 = $request['Row_Count4'];
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['DOCNO_ID_'.$i]) && $request['DOCNO_ID_'.$i] != '')
            {
                $req_data[$i] = [
                    'DOC_TYPE'                  => $request['Doc_Type_'.$i],
                    'DOCNO_ID'                  => $request['DOCNO_ID_'.$i],                    
                    'BALANCE_DUE'               => (isset($request['BALANCE_DUE_'.$i]) ? $request['BALANCE_DUE_'.$i] : 0),
                    'PAYMENT_AMT'               => (isset($request['PAYMENT_AMT_'.$i]) ? $request['PAYMENT_AMT_'.$i] : 0),
                    'REMARKS'                   => (isset($request['REMARKS_'.$i]) ? $request['REMARKS_'.$i] : ''),
                    'BRID_REF'                  => (isset($request['BRID_REF_'.$i]) ? $request['BRID_REF_'.$i] : ''),
                ];
            }
        }

        if(isset($req_data))
        { 
            $wrapped_links["INVOCE"] = $req_data; 
            $XMLINVOICE = ArrayToXml::convert($wrapped_links);
        }
        else
        {
            $XMLINVOICE = NULL; 
        }

        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset($request['GLID_REF_'.$i]) && $request['GLID_REF_'.$i] != '')
            {
                $reqdata2[$i] = [
                    'GLID_REF'                  => $request['GLID_REF_'.$i],
                    'AMOUNT'                    => (isset($request['AMOUNT_'.$i]) ? $request['AMOUNT_'.$i] : 0),
                    'ASSESSABLE_VALUE'          => (isset($request['ASSESSABLE_VALUE_'.$i]) ? $request['ASSESSABLE_VALUE_'.$i] : 0),
                    'IGST'                      => (isset($request['calIGST_'.$i]) ? $request['calIGST_'.$i] : 0),
                    'CGST'                      => (isset($request['calCGST_'.$i]) ? $request['calCGST_'.$i] : 0),
                    'SGST'                      => (isset($request['calSGST_'.$i]) ? $request['calSGST_'.$i] : 0),
                    'CCID_REF'                  => (isset($request['CCID_REF_'.$i]) ? $request['CCID_REF_'.$i] : ''),
                    'TYPE'                      => $request['TYPE_'.$i],
                ];
            }
            
        }
        if(isset($reqdata2))
        { 
            $wrapped_links2["ACCOUNT"] = $reqdata2;
            $XMLACCOUNT = ArrayToXml::convert($wrapped_links2);
        }
        else
        {
            $XMLACCOUNT = NULL; 
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
            $XMLCCD = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $XMLCCD = NULL; 
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

        $PAYMENT_NO                       = $request['PAYMENT_NO'];
        $PAYMENT_DT                       = $request['PAYMENT_DT'];
        $PAYMENT_FOR                      = $request['hdnpaymentfor'];
        $CUSTMER_VENDOR_ID                = $request['CUSTMER_VENDOR_ID'];
        $PAYMENT_TYPE                     = $request['PAYMENT_TYPE'];
        $PAYMENT_ON_ACCOUNT               = (isset($request['chk_PayAccount'])!="true" ? 0 : 1);
        $CASH_BANK_ID                     = (isset($request['chk_Account'])=="true" ? $request['BANK_CASH_ID'] : $request['CASH_BANK_ID']);
        $TRANSACTION_DT                   = $request['TRANSACTION_DT'];
        $INSTRUMENT_TYPE                  = $request['INSTRUMENT_TYPE'];
        $INSTRUMENT_NO                    = $request['INSTRUMENT_NO'];
        $BANK_CHARGE                      = (!is_null($request['BANK_CHARGE']) ? $request['BANK_CHARGE'] : 0);
        $NARRATION                        = (!is_null($request['NARRATION']) ? $request['NARRATION'] : '');
        $CENTERLIZED_PAYMENT              = (isset($request['CENTERLIZED_PAYMENT'])!="true" ? 0 : 1);
        $REMARKS                          = '';
        $CHALLAN_NO                       = '';
        $CHALLAN_DT                       = '';
        $BSR_CODE                         = '';
        $TDS_INTREST                      = 0;
        $TDS_OTH_CHARGE                   = 0;
        $TDS_LATE_FEE                     = 0;
        $TOAL_AMOUNT                      = $request['tot_amt1'];
        $AMOUNT                           = (!is_null($request['AMOUNT']) ? $request['AMOUNT'] : 0);


        $GLID_REF_ROUNDOFF               = $request['GLID_REF_ROUNDOFF'];
        $ROUNDOFF_AMT                    = $request['ROUNDOFF_AMT'];
        $ROUNDOFF_MODE                   = $request['ROUNDOFF_MODE']; 
        $TDS                             = (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);
        $BANK_AMOUNT                     = $request['BANK_AMOUNT'];
        $BANK_REMARKS                    = $request['BANK_REMARKS'];
        $POID_REF                        = $request['POID_REF']; 

        $FC 							 = (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF 		     			 = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT 		  				 = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : 0;
    

		
        $log_data = [ 
            $PAYMENT_NO,$PAYMENT_DT,$PAYMENT_FOR,$CUSTMER_VENDOR_ID,$PAYMENT_TYPE,$PAYMENT_ON_ACCOUNT,
            $CASH_BANK_ID,$TRANSACTION_DT,$INSTRUMENT_TYPE,$INSTRUMENT_NO,$BANK_CHARGE,$NARRATION,$CENTERLIZED_PAYMENT,
            $REMARKS,$CHALLAN_NO,$CHALLAN_DT,$BSR_CODE,$TDS_INTREST,$TDS_OTH_CHARGE,$TDS_LATE_FEE,$TOAL_AMOUNT,
            $CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$XMLINVOICE,$XMLACCOUNT,$XMLTDSD,$XMLCCD,$USERID,
            Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$AMOUNT,$GLID_REF_ROUNDOFF,$ROUNDOFF_AMT,$ROUNDOFF_MODE,$TDS,$BANK_AMOUNT,$BANK_REMARKS,$POID_REF
            ,$FC,                            $CRID_REF,                     $CONVFACT
        ]; 
        //dd($log_data);
        $sp_result = DB::select('EXEC SP_PAYMENT_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);  

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $PAYMENT_NO. ' Sucessfully Updated.']);

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
    $r_count6 = $request['Row_Count4'];
    
    for ($i=0; $i<=$r_count1; $i++)
    {
        if(isset($request['DOCNO_ID_'.$i]) && $request['DOCNO_ID_'.$i] != '')
        {
            $req_data[$i] = [
                'DOC_TYPE'                  => $request['Doc_Type_'.$i],
                'DOCNO_ID'                  => $request['DOCNO_ID_'.$i],                    
                'BALANCE_DUE'               => (isset($request['BALANCE_DUE_'.$i]) ? $request['BALANCE_DUE_'.$i] : 0),
                'PAYMENT_AMT'               => (isset($request['PAYMENT_AMT_'.$i]) ? $request['PAYMENT_AMT_'.$i] : 0),
                'REMARKS'                   => (isset($request['REMARKS_'.$i]) ? $request['REMARKS_'.$i] : ''),
                'BRID_REF'                  => (isset($request['BRID_REF_'.$i]) ? $request['BRID_REF_'.$i] : ''),
            ];
        }
    }

    if(isset($req_data))
    { 
        $wrapped_links["INVOCE"] = $req_data; 
        $XMLINVOICE = ArrayToXml::convert($wrapped_links);
    }
    else
    {
        $XMLINVOICE = NULL; 
    }

    for ($i=0; $i<=$r_count3; $i++)
    {
        if(isset($request['GLID_REF_'.$i]) && $request['GLID_REF_'.$i] != '')
        {
            $reqdata2[$i] = [
                'GLID_REF'                  => $request['GLID_REF_'.$i],
                'AMOUNT'                    => (isset($request['AMOUNT_'.$i]) ? $request['AMOUNT_'.$i] : 0),
                'ASSESSABLE_VALUE'          => (isset($request['ASSESSABLE_VALUE_'.$i]) ? $request['ASSESSABLE_VALUE_'.$i] : 0),
                'IGST'                      => (isset($request['calIGST_'.$i]) ? $request['calIGST_'.$i] : 0),
                'CGST'                      => (isset($request['calCGST_'.$i]) ? $request['calCGST_'.$i] : 0),
                'SGST'                      => (isset($request['calSGST_'.$i]) ? $request['calSGST_'.$i] : 0),
                'CCID_REF'                  => (isset($request['CCID_REF_'.$i]) ? $request['CCID_REF_'.$i] : ''),
                'TYPE'                      => $request['TYPE_'.$i],
            ];
        }
        
    }
    if(isset($reqdata2))
    { 
        $wrapped_links2["ACCOUNT"] = $reqdata2;
        $XMLACCOUNT = ArrayToXml::convert($wrapped_links2);
    }
    else
    {
        $XMLACCOUNT = NULL; 
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
        $XMLCCD = ArrayToXml::convert($wrapped_links3);
    }
    else
    {
        $XMLCCD = NULL; 
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
        $XMLTDSD = ArrayToXml::convert($wrapped_links6);
    }
    else{
        $XMLTDSD = NULL; 
    }

    $VTID_REF     =   $this->vtid_ref;
    $VID          = 0;
    $USERID       = Auth::user()->USERID;   
    $ACTIONNAME   = $Approvallevel;
    $IPADDRESS    = $request->getClientIp();
    $CYID_REF     = Auth::user()->CYID_REF;
    $BRID_REF     = Session::get('BRID_REF');
    $FYID_REF     = Session::get('FYID_REF');

    $PAYMENT_NO                       = $request['PAYMENT_NO'];
    $PAYMENT_DT                       = $request['PAYMENT_DT'];
    $PAYMENT_FOR                      = $request['hdnpaymentfor'];
    $CUSTMER_VENDOR_ID                = $request['CUSTMER_VENDOR_ID'];
    $PAYMENT_TYPE                     = $request['PAYMENT_TYPE'];
    $PAYMENT_ON_ACCOUNT               = (isset($request['chk_PayAccount'])!="true" ? 0 : 1);
    $CASH_BANK_ID                     = (isset($request['chk_Account'])=="true" ? $request['BANK_CASH_ID'] : $request['CASH_BANK_ID']);
    $TRANSACTION_DT                   = $request['TRANSACTION_DT'];
    $INSTRUMENT_TYPE                  = $request['INSTRUMENT_TYPE'];
    $INSTRUMENT_NO                    = $request['INSTRUMENT_NO'];
    $BANK_CHARGE                      = (!is_null($request['BANK_CHARGE']) ? $request['BANK_CHARGE'] : 0);
    $NARRATION                        = (!is_null($request['NARRATION']) ? $request['NARRATION'] : '');
    $CENTERLIZED_PAYMENT              = (isset($request['CENTERLIZED_PAYMENT'])!="true" ? 0 : 1);
    $REMARKS                          = '';
    $CHALLAN_NO                       = '';
    $CHALLAN_DT                       = '';
    $BSR_CODE                         = '';
    $TDS_INTREST                      = 0;
    $TDS_OTH_CHARGE                   = 0;
    $TDS_LATE_FEE                     = 0;
    $TOAL_AMOUNT                      = $request['tot_amt1'];
    $AMOUNT                           = (!is_null($request['AMOUNT']) ? $request['AMOUNT'] : 0);

    $GLID_REF_ROUNDOFF               = $request['GLID_REF_ROUNDOFF'];
    $ROUNDOFF_AMT                    = $request['ROUNDOFF_AMT'];
    $ROUNDOFF_MODE                   = $request['ROUNDOFF_MODE']; 
    $TDS                             = (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);
    $BANK_AMOUNT                     = $request['BANK_AMOUNT'];
    $BANK_REMARKS                    = $request['BANK_REMARKS'];
    $POID_REF                        = $request['POID_REF']; 
    $FC 			                 = (isset($request['FC'])!="true" ? 0 : 1);
	$CRID_REF 		                 = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
	$CONVFACT 		                 = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : 0;

   
    $log_data = [ 
        $PAYMENT_NO,                     $PAYMENT_DT,                   $PAYMENT_FOR,               $CUSTMER_VENDOR_ID,                 $PAYMENT_TYPE,                   $PAYMENT_ON_ACCOUNT,           $CASH_BANK_ID,              $TRANSACTION_DT,
        $INSTRUMENT_TYPE,                $INSTRUMENT_NO,                $BANK_CHARGE,               $NARRATION,
        $CENTERLIZED_PAYMENT,            $REMARKS,                      $CHALLAN_NO,                $CHALLAN_DT,
        $BSR_CODE,                       $TDS_INTREST,                  $TDS_OTH_CHARGE,            $TDS_LATE_FEE,
        $TOAL_AMOUNT,                    $CYID_REF,                     $BRID_REF,                  $FYID_REF,
        $VTID_REF,                       $XMLINVOICE,                   $XMLACCOUNT,                $XMLTDSD,
        $XMLCCD,                         $USERID,                       Date('Y-m-d'),              Date('h:i:s.u'),
        $ACTIONNAME,                     $IPADDRESS,                    $AMOUNT,                    $GLID_REF_ROUNDOFF,
        $ROUNDOFF_AMT,                   $ROUNDOFF_MODE,                $TDS,                       $BANK_AMOUNT,
        $BANK_REMARKS,                   $POID_REF
        ,$FC,                            $CRID_REF,                     $CONVFACT
    ]; 
    
    $sp_result = DB::select('EXEC SP_PAYMENT_UP ?,?,?,?,?,  ?,?,?,?,?,  ?,?,?,?,?,  ?,?,?,?,?,  ?,?,?,?,?,  ?,?,?,?,?,  ?,?,?,?,?,  ?,?,?,?,?,  ?,?,?,?,?', $log_data);   

//dd($sp_result); 
    $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');

    if($contains){
        return Response::json(['success' =>true,'msg' => $PAYMENT_NO. ' Sucessfully Approved.']);

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
        $TABLE      =   "TBL_TRN_PAYMENT_HDR";
        $FIELD      =   "PAYMENTID";
        $ACTIONNAME     = $Approvallevel;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
            
        $log_data = [ 
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_PAYMENT ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
        
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
        $TABLE      =   "TBL_TRN_PAYMENT_HDR";
        $FIELD      =   "PAYMENTID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_PAYMENT_HDR',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_PAYMENT_INVOICE',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_PAYMENT_ACCOUNT',
        ];
        $req_data[3]=[
            'NT'  => 'TBL_TRN_PAYMENT_TDS',
        ];
        $req_data[4]=[
            'NT'  => 'TBL_TRN_PAYMENT_CCD',
        ];

        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $pb_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_PAYMENT  ?,?,?,?, ?,?,?,?, ?,?,?,?', $pb_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_PAYMENT_HDR')->where('PAYMENTID','=',$id)->first();

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
        
		$image_path         =   "docs/company".$CYID_REF."/PAYMENTENTRY";     
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

        $PAYMENT_NO  =   trim($request['PAYMENT_NO']);
        $objLabel = DB::table('TBL_TRN_PAYMENT_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('PAYMENT_NO','=',$PAYMENT_NO)
        ->select('PAYMENTID')->first();

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

        return  DB::select('SELECT MAX(PAYMENT_DT) PAYMENT_DT FROM TBL_TRN_PAYMENT_HDR  
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
			public function getTDSDetailsCount(Request $request){
                $SLID_REF   =   $request['id'];
                $BRID_REF   =   Session::get('BRID_REF');
                
                $sp_param = [ 
                    $SLID_REF,$BRID_REF
                ];  
            
                $sp_result = DB::select('EXEC SP_GET_VENDOR_TDSDETAILS ?,?', $sp_param);
                
                if(!empty($sp_result)){
                    echo count($sp_result);
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

public function getTDSDetailsCount_customer(Request $request){
                $SLID_REF   =   $request['id'];
                $BRID_REF   =   Session::get('BRID_REF');
                
                $sp_param = [ 
                    $SLID_REF,$BRID_REF
                ];  
            
                $sp_result = DB::select('EXEC SP_GET_CUSTOMER_TDSDETAILS ?,?', $sp_param);
                
                if(!empty($sp_result)){
                    echo count($sp_result);
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


public function getBalance(Request $request){
	$Status     =   "A";
	$ID   =   $request['id'];
    $CYID_REF   =   Auth::user()->CYID_REF;
    $BRID_REF   =   Session::get('BRID_REF');
    $FYID_REF   =   Session::get('FYID_REF');
    
    $TaxStatus  =   DB::table('TBL_MST_GLOPENING_LEDGER')
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF)
                    ->where('FYID_REF','=',$FYID_REF)
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

public function get_po(Request $request) { 
    //dd($request->all());  
    $Status = "A";
    $CYID_REF = Auth::user()->CYID_REF;
    $BRID_REF = Session::get('BRID_REF');
    $FYID_REF = Session::get('FYID_REF');  
    $SLID_REF = $request['SLID_REF'];        
    $objPO = DB::table('TBL_TRN_PROR01_HDR')
    ->where('CYID_REF','=',Auth::user()->CYID_REF)
    ->where('BRID_REF','=',Session::get('BRID_REF'))
    ->where('STATUS','=',$Status)  
    ->where('VID_REF','=',$SLID_REF)  
    ->select('POID AS DOCID','PO_NO AS DOCNO','PO_DT AS DESC')
    ->get()    
    ->toArray();
    //dd($objPO); 


    if(!empty($objPO)){        
        foreach ($objPO as $index=>$dataRow){   
            $row = '';
            $row = $row.'<tr ><td style="text-align:center; width:10%">';
            $row = $row.'<input type="checkbox" name="po[]"  id="pocode_'.$dataRow->DOCID.'" class="clsspid_po" 
            value="'.$dataRow->DOCID.'"/>             
            </td>           
            <td style="width:40%;">'.$dataRow->DOCNO;
            $row = $row.'<input type="hidden" id="txtpocode_'.$dataRow->DOCID.'" data-code="'.$dataRow->DOCNO.'"   data-desc="'.$dataRow->DESC.'" 
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


//=====================================TDS METHODS FOR CUSTOMER ENDS HERE====================================================
        

// function downloadFile() {

//         //all files are present inside 'outgoing' folder
//     $file_list = Storage::disk('sftp')->allFiles('outgoing/');

//     foreach ($file_list as $key => $value) {
//         # code...//output the name of the files
//         $this->printVariable(str_replace("outgoing/", "", $value));
//         Storage::disk('public')->put(str_replace("outgoing/", "", $value), Storage::disk('sftp')->get($value));
//       }
//       return Response::json(['approve' =>true,'msg' => 'Download Sucessfully.']);
// }

    
public function GetMonthlyBudgets(Request $request) { 
    $DATE               =       $request['DATE'];
    $GLID_REF           =       $request['GLID_REF'];   
    if($GLID_REF != ""){
        echo $response=$this->GetMonthlyBudget($DATE,$GLID_REF);
    }else{
        echo ""; 
    }

    
}

    
}


