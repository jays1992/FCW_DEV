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

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use DateTime;
use App\Helpers\Helper;
use App\Helpers\Utils;

class TrnFrm299Controller extends Controller{

    protected $form_id  = 299;
    protected $vtid_ref = 389;
    protected $view     = "transactions.Accounts.ImportPurchaseOrder.trnfrm";
   
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        $FormId         =   $this->form_id;

        $objRights      =   DB::table('TBL_MST_USERROLMAP')
                            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                            
                            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                            ->first();
        
        
		$CYID_REF   	=   Auth::user()->CYID_REF;
		$BRID_REF   	=   Session::get('BRID_REF');
		$FYID_REF   	=   Session::get('FYID_REF');     


		$objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
		
		$FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;
		
		$objDataList	=	DB::select("select hdr.IPO_ID,hdr.IPO_NO,hdr.IPO_DT,hdr.SALE_ORDER_NO,hdr.REMARKS,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.IPO_ID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
								when a.ACTIONNAME = 'CLOSE' then 'Closed' 
							when a.ACTIONNAME = 'CANCEL' then 'Cancelled'
							end end as STATUS_DESC
							from TBL_TRN_AUDITTRAIL a 
							inner join TBL_TRN_IPO_HDR hdr
							on a.VID = hdr.IPO_ID 
							and a.VTID_REF = hdr.VTID_REF 
							and a.CYID_REF = hdr.CYID_REF 
							and a.BRID_REF = hdr.BRID_REF
							inner join TBL_MST_SUBLEDGER sl ON hdr.VID_REF = sl.SGLID  
							where a.VTID_REF = '$this->vtid_ref'
							and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
							and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
							ORDER BY hdr.IPO_ID DESC ");

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
        $IPO_ID       =   $myValue['IPO_ID'];
        $Flag         =   $myValue['Flag'];

         $objSalesOrder = DB::table('TBL_TRN_IPO_HDR')
         ->where('TBL_TRN_IPO_HDR.CYID_REF','=',Auth::user()->CYID_REF)
         ->where('TBL_TRN_IPO_HDR.BRID_REF','=',Auth::user()->BRID_REF)
         ->where('TBL_TRN_IPO_HDR.IPO_ID','=',$IPO_ID)
         ->select('TBL_TRN_IPO_HDR.*')
         ->first();
        
        
	$ssrs = new \SSRS\Report(Session::get('ssrs_config')['REPORT_URL'], array('username' => Session::get('ssrs_config')['username'], 'password' => Session::get('ssrs_config')['password'])); 
        $result = $ssrs->loadReport('/ERP/Import_PurchaseOrder');
        
        $reportParameters = array(
            'IPONO' => $objSalesOrder->IPO_NO,
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

    public function getConversionFactor(Request $request){

        $CRID_REF   =   $request['CRID_REF'];
        $CYID_REF   =   Auth::user()->CYID_REF;
       
        $data   =   DB::select("SELECT TOP 1 FRAMOUNT 
                    FROM TBL_MST_CRCONVERSION 
                    WHERE TOCRID_REF='$CRID_REF' AND FROMCRID_REF= (SELECT CRID_REF FROM TBL_MST_COMPANY WHERE CYID='$CYID_REF' AND STATUS='A') 
                    AND EFFDATE <= CAST( GETDATE() AS Date ) AND ENDDATE >=  CAST( GETDATE() AS Date ) 
                    AND STATUS='A' AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 )  ORDER BY CRCOID DESC ");

        if(!empty($data)){
            echo $data[0]->FRAMOUNT;
        }
        else{
            echo "";
        }
    }

    public function getVendor(Request $request){

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $CODE       =   $request['CODE'];
        $NAME       =   $request['NAME'];
    
        $sp_popup = [
            $CYID_REF, $BRID_REF,$CODE,$NAME
        ]; 
        
        $ObjData = DB::select('EXEC sp_get_vendor_popup_enquiry ?,?,?,?', $sp_popup);
    
        if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){
    
                $VID    =   $dataRow->SGLID;
                $VCODE  =   $dataRow->SGLCODE;
                $NAME   =   $dataRow->SLNAME;
                
               
                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_VID_REF[]" id="vendoridcode_'.$index.'"  class="clsvendorid" value="'.$VID.'" ></td>
                <td class="ROW2">'.$VCODE.'<input type="hidden" id="txtvendoridcode_'.$index.'" data-desc="'.$VCODE.'-'.$NAME.'" value="'.$VID.'" > </td>
                <td class="ROW3">'.$NAME.'</td>
                </tr>';
    
                echo $row;
    
            }
    
        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();
    
    }

    public function add(){

        $FormId     =   $this->form_id;
        $Status     = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
       
        $objlastdt          =   $this->getLastdt();
        $objImportDuty      =   $this->getImportDutyList();
        
		$d_currency = DB::table('TBL_MST_COMPANY')
        ->where('STATUS','=',$Status)
        ->where('CYID','=',Auth::user()->CYID_REF)
        ->select('TBL_MST_COMPANY.CRID_REF','TBL_MST_COMPANY.NAME')
        ->first();

 

		
        $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
            WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by TNC_CODE ASC', [$CYID_REF, $BRID_REF, $FYID_REF, 'A' ]);

        $objCalculationHeader	=   Helper::getCalculationHeader(array(
                                    'CYID_REF'=>Auth::user()->CYID_REF,
                                    'BRID_REF'=>Session::get('BRID_REF'),
                                    'USERID'=>Auth::user()->USERID,
                                    'HEADING'=>'Transactions',
                                    'VTID_REF'=>$this->vtid_ref,
                                    'FORMID'=>$this->form_id
                                    ));

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_IPO_HDR',
            'HDR_ID'=>'IPO_ID',
            'HDR_DOC_NO'=>'IPO_NO',
            'HDR_DOC_DT'=>'IPO_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
        
        


        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_IPO")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDFIPOID')->from('TBL_MST_UDFFOR_IPO')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF)
                                                ->where('BRID_REF','=',$BRID_REF);
                                                                
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF);
                                 
                   

        $objUdfPBData = DB::table('TBL_MST_UDFFOR_IPO')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdfPBData);

        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	        =	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        $required_status    =   $this->required_status();

        $objothcurrency = $this->GetCurrencyMaster(); 

        return view($this->view.$FormId.'add',compact([
            'AlpsStatus','FormId','objlastdt','objUdfPBData',
            'objCountUDF','objTNCHeader','objCalculationHeader','objothcurrency',
            'objImportDuty','TabSetting','doc_req','docarray','required_status'
        ]));       
    }

    

    public function gettax(Request $request){
        $Status = "A";
        $id = $request['id'];
        $taxstate = $request['taxstate'];
        $BRID_REF = Session::get('BRID_REF');
        $CYID_REF = Auth::user()->CYID_REF;

        if ($taxstate == 'WithinState')
        {
            $ObjTax = DB::table('TBL_MST_TAXTYPE')
            ->leftJoin('TBL_MST_HSNNORMAL', 'TBL_MST_HSNNORMAL.TAXID_REF','=','TBL_MST_TAXTYPE.TAXID')
            ->where('TBL_MST_TAXTYPE.CYID_REF','=',$CYID_REF)
            ->where('TBL_MST_TAXTYPE.WITHINSTATE','=','1')
            ->where('TBL_MST_TAXTYPE.OUTOFSTATE','=','0')
            ->where('TBL_MST_TAXTYPE.FOR_PURCHASE','=','1')
            ->where('TBL_MST_TAXTYPE.STATUS','=','A')
            ->where('TBL_MST_HSNNORMAL.HSNID_REF','=',$id)
            ->select('TBL_MST_TAXTYPE.TTCODE','TBL_MST_HSNNORMAL.NRATE')
            ->first(); 
        }
        else
        {
            $ObjTax = DB::table('TBL_MST_TAXTYPE')
            ->leftJoin('TBL_MST_HSNNORMAL', 'TBL_MST_HSNNORMAL.TAXID_REF','=','TBL_MST_TAXTYPE.TAXID')
            ->where('TBL_MST_TAXTYPE.CYID_REF','=',$CYID_REF)
            ->where('TBL_MST_TAXTYPE.WITHINSTATE','=','0')
            ->where('TBL_MST_TAXTYPE.OUTOFSTATE','=','1')
            ->where('TBL_MST_TAXTYPE.FOR_PURCHASE','=','1')
            ->where('TBL_MST_TAXTYPE.STATUS','=','A')
            ->where('TBL_MST_HSNNORMAL.HSNID_REF','=',$id)
            ->select('TBL_MST_TAXTYPE.TTCODE','TBL_MST_HSNNORMAL.NRATE')
            ->first();
        }
        if($ObjTax)
        {
            echo $ObjTax->NRATE;
        }
        else
        {
            echo 0.00;
        }
    }

    public function gettax2(Request $request){
        $Status = "A";
        $id = $request['id'];
        $taxstate = $request['taxstate'];
        $TaxCode1 = $request['TaxCode1'];
        $BRID_REF = Session::get('BRID_REF');
        $CYID_REF = Auth::user()->CYID_REF;

        if ($taxstate == 'WithinState')
        {
            $ObjTax = DB::table('TBL_MST_TAXTYPE')
            ->leftJoin('TBL_MST_HSNNORMAL', 'TBL_MST_HSNNORMAL.TAXID_REF','=','TBL_MST_TAXTYPE.TAXID')
            ->where('TBL_MST_TAXTYPE.CYID_REF','=',$CYID_REF)
            ->where('TBL_MST_TAXTYPE.WITHINSTATE','=','1')
            ->where('TBL_MST_TAXTYPE.OUTOFSTATE','=','0')
            ->where('TBL_MST_TAXTYPE.TTCODE','!=',$TaxCode1)
            ->where('TBL_MST_TAXTYPE.FOR_PURCHASE','=','1')
            ->where('TBL_MST_TAXTYPE.STATUS','=','A')
            ->where('TBL_MST_HSNNORMAL.HSNID_REF','=',$id)
            ->select('TBL_MST_HSNNORMAL.NRATE')
            ->first(); 
        }
        if($ObjTax)
        {
            echo $ObjTax->NRATE;
        }
        else
        {
            echo 0.00;
        }
            
    }

    public function gettaxCode(Request $request){
        $Status = "A";
        $id = $request['id'];
        $taxstate = $request['taxstate'];
        $BRID_REF = Session::get('BRID_REF');
        $CYID_REF = Auth::user()->CYID_REF;

        if ($taxstate == 'WithinState')
        {
            $ObjTax = DB::table('TBL_MST_TAXTYPE')
            ->leftJoin('TBL_MST_HSNNORMAL', 'TBL_MST_HSNNORMAL.TAXID_REF','=','TBL_MST_TAXTYPE.TAXID')
            ->where('TBL_MST_TAXTYPE.CYID_REF','=',$CYID_REF)
            ->where('TBL_MST_TAXTYPE.WITHINSTATE','=','1')
            ->where('TBL_MST_TAXTYPE.OUTOFSTATE','=','0')
            ->where('TBL_MST_TAXTYPE.FOR_PURCHASE','=','1')
            ->where('TBL_MST_TAXTYPE.STATUS','=','A')
            ->where('TBL_MST_HSNNORMAL.HSNID_REF','=',$id)
            ->select('TBL_MST_TAXTYPE.TTCODE','TBL_MST_HSNNORMAL.NRATE')
            ->first(); 
        }
        else
        {
            $ObjTax = DB::table('TBL_MST_TAXTYPE')
            ->leftJoin('TBL_MST_HSNNORMAL', 'TBL_MST_HSNNORMAL.TAXID_REF','=','TBL_MST_TAXTYPE.TAXID')
            ->where('TBL_MST_TAXTYPE.CYID_REF','=',$CYID_REF)
            ->where('TBL_MST_TAXTYPE.WITHINSTATE','=','0')
            ->where('TBL_MST_TAXTYPE.OUTOFSTATE','=','1')
            ->where('TBL_MST_TAXTYPE.FOR_PURCHASE','=','1')
            ->where('TBL_MST_TAXTYPE.STATUS','=','A')
            ->where('TBL_MST_HSNNORMAL.HSNID_REF','=',$id)
            ->select('TBL_MST_TAXTYPE.TTCODE','TBL_MST_HSNNORMAL.NRATE')
            ->first();
        }
        if($ObjTax)
        {
            echo $ObjTax->TTCODE;
        }
        else
        {
            echo '';
        }
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
            // dd($ObjData);
                if(!empty($ObjData)){
        
                foreach ($ObjData as $index=>$dataRow){
                    $dynamicid = "tncdetvalue_".$index;
                    $txtvaluetype = $dataRow->VALUE_TYPE; 
                    $chkvaltype =  strtolower($txtvaluetype);
                    $txtdescription = $dataRow->DESCRIPTIONS; 
                    echo($txtdescription);
                    // dd($txtdescription);
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
                        // $txtdescription;
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
        
        public function getgoodsreceiptnote(Request $request){
            $Status = "A";
            $id = $request['id'];
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
    
            $SP_PARAMETERS = [$CYID_REF,$BRID_REF,$FYID_REF,$id];
    
            $ObjData =  DB::select('EXEC SP_GRN_GETLIST ?,?,?,?', $SP_PARAMETERS);
        
                if(!empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){
                    $row = '';
                    $row = $row.'<tr id="grncode_'.$dataRow->GRNID .'"  class="clsgrnid"><td width="50%">'.$dataRow->GRN_NO;
                    $row = $row.'<input type="hidden" id="txtgrncode_'.$dataRow->GRNID.'" data-desc="'.$dataRow->GRN_NO.'" 
                    value="'.$dataRow->GRNID.'"/></td><td>'.$dataRow->GRN_DT.'</td></tr>';
                    echo $row;
                }
                }else{
                    echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
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

        public function getcreditdays(Request $request){
            $Status = "A";
            $SLID_REF   =   $request['id'];
            $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
            $id         =   $ObVID->VID;

            $ObjData =  DB::select('SELECT top 1 CREDITDAY FROM TBL_MST_VENDOR  
                        WHERE STATUS= ? AND VID = ? ', [$Status,$id]);
        
             
                    if(!empty($ObjData)){
        
                    echo($ObjData[0]->CREDITDAY);
        
                    }else{
                        echo '0';
                    }
                    exit();
        
        }

        public function getBillTo(Request $request){
            $Status = "A";
            $SLID_REF   =   $request['id'];
            $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
            $id         =   $ObVID->VID;
            
            $ObjCust =  DB::select('SELECT top 1 VID FROM TBL_MST_VENDOR  
                        WHERE STATUS= ? AND VID = ? ', [$Status,$id]);
        
            $VID = $ObjCust[0]->VID;
            $ObjBillTo =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                        WHERE DEFAULT_BILLING= ? AND VID_REF = ? ', [1,$VID]);
    
            $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                        WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                        [$Status,$ObjBillTo[0]->CITYID_REF,$ObjBillTo[0]->CTRYID_REF,$ObjBillTo[0]->STID_REF]);
    
            $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                        WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBillTo[0]->STID_REF,$ObjBillTo[0]->CTRYID_REF]);
    
            $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                        WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBillTo[0]->CTRYID_REF]);
    
            $ObjAddressID = $ObjBillTo[0]->LID;
                    if(!empty($ObjBillTo)){
                        
                    $objAddress = $ObjBillTo[0]->LADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                    
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
                $SLID_REF   =   $request['id'];
                $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
                $id         =   $ObVID->VID;
                $BRID_REF = Session::get('BRID_REF');
                
    
                $ObjCust =  DB::select('SELECT top 1 VID FROM TBL_MST_VENDOR  
                            WHERE STATUS= ? AND VID = ? ', [$Status,$id]);
            
                $VID = $ObjCust[0]->VID;
                $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                            WHERE DEFAULT_SHIPPING= ? AND VID_REF = ? ', [1,$VID]);
    
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
        
                $ObjAddressID = $ObjSHIPTO[0]->LID;
                        if(!empty($ObjSHIPTO)){
                            
                        $objAddress = $ObjSHIPTO[0]->LADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                        
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
                    $SLID_REF   =   $request['id'];
                    $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
                    $id         =   $ObVID->VID;
                    if(!is_null($id))
                    {
                    $ObjCust =  DB::select('SELECT top 1 VID FROM TBL_MST_VENDOR  
                            WHERE STATUS= ? AND VID = ? ', [$Status,$id]);
            
                    $VID = $ObjCust[0]->VID;
                    $ObjBillTo =  DB::select('SELECT  * FROM TBL_MST_VENDORLOCATION  
                                WHERE BILLTO= ? AND VID_REF = ? ', [1,$VID]);
                
                        if(!empty($ObjBillTo)){
                
                        foreach ($ObjBillTo as $index=>$dataRow){
        
                            $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                            WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                            [$Status,$dataRow->CITYID_REF,$dataRow->CTRYID_REF,$dataRow->STID_REF]);
        
                            $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                                        WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$dataRow->STID_REF,$dataRow->CTRYID_REF]);
        
                            $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                                        WHERE STATUS= ? AND CTRYID = ? ', [$Status,$dataRow->CTRYID_REF]);
                            $objAddress = $dataRow->LADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
        
                            $row = '';
                            $row = $row.'<tr>
                            <td class="ROW1"> <input type="checkbox" name="SELECT_BILLTO[]" id="billto_'.$dataRow->LID .'"  class="clsbillto" value="'.$dataRow->LID.'" ></td>
                            <td class="ROW2">'.$dataRow->NAME;
                            $row = $row.'<input type="hidden" id="txtbillto_'.$dataRow->LID.'" data-desc="'.$objAddress.'" 
                            value="'.$dataRow->LID.'"/></td><td class="ROW3">'.$objAddress.'</td></tr>';
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
                    $SLID_REF   =   $request['id'];
                    $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
                    $id         =   $ObVID->VID;
                    $BRID_REF = Session::get('BRID_REF');
                    if(!is_null($id))
                    {
                    $ObjCust =  DB::select('SELECT top 1 VID FROM TBL_MST_VENDOR  
                            WHERE STATUS= ? AND VID = ? ', [$Status,$id]);
            
                    $VID = $ObjCust[0]->VID;
                    $ObjShipTo =  DB::select('SELECT  * FROM TBL_MST_VENDORLOCATION  
                                WHERE SHIPTO= ? AND VID_REF = ? ', [1,$VID]);
                
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
                            $objAddress = $dataRow->LADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
        
                            $row = '';
                            $row = $row.'<tr >
                            <td class="ROW1"> <input type="checkbox" name="SELECT_SHIPTO[]" id="shipto_'.$dataRow->LID .'"  class="clsshipto" value="'.$dataRow->LID.'" ></td>
                            <td class="ROW2">'.$dataRow->NAME;
                            $row = $row.'<input type="hidden" id="txtshipto_'.$dataRow->LID.'" data-desc="'.$TAXSTATE.'" 
                            value="'.$dataRow->LID.'"/></td>
                            <td class="ROW3" id="txtshipadd_'.$dataRow->LID.'" >'.$objAddress.'</td></tr>';
                            echo $row;
                        }
                
                        }else{
                            echo '<tr><td colspan="2">Record not found.</td></tr>';
                        }
                        exit();
                    }
                }

    public function getTDSApplicability(Request $request){
        $Status = "A";
        $SLID_REF   =   $request['id'];
        $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
        $id         =   $ObVID->VID;
        $BRID_REF = Session::get('BRID_REF');
        

        $ObjVendor =  DB::select('SELECT top 1 * FROM TBL_MST_VENDOR  
                    WHERE STATUS= ? AND VID = ? ', [$Status,$id]);
    
        if($ObjVendor)
        {
            echo $ObjVendor[0]->TDS_APPLICABLE;
        }
        else
        {
            echo '0';
        }
    }

    public function getTDSDetails(Request $request){
        $Status = "A";
        $SLID_REF   =   $request['id'];
        $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
        $id         =   $ObVID->VID;
        $BRID_REF = Session::get('BRID_REF');
        
        $sp_param = [ 
            $id,$BRID_REF
        ];  

        $sp_result = DB::select('EXEC SP_GET_VENDOR_TDSDETAILS ?,?', $sp_param);
        
        if($sp_result)
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
                <td hidden><input type="hidden" name="TDS_EXEMPT_'.$index.'" id="TDS_EXEMPT_'.$index.'" class="form-control two-digits" value="'.$dataRow->TDS_EXEMP_LIMIT.'" /></td>
                <td><input type="text" name="TDS_AMT_'.$index.'" id="TDS_AMT_'.$index.'" class="form-control two-digits" maxlength="15"  autocomplete="off" readonly /></td>
                <td><input type="text" name="ASSESSABLE_VL_SURCHARGE_'.$index.'" id="ASSESSABLE_VL_SURCHARGE_'.$index.'"  class="form-control two-digits" maxlength="15"  autocomplete="off"  /></td>
                <td><input type="text" name="SURCHARGE_RATE_'.$index.'" id="SURCHARGE_RATE_'.$index.'" value="'.$dataRow->SURCHARGE_RAGE.'" class="form-control four-digits" maxlength="12"  autocomplete="off" readonly /></td>
                <td hidden><input type="hidden" name="SURCHARGE_EXEMPT_'.$index.'" id="SURCHARGE_EXEMPT_'.$index.'" class="form-control two-digits" value="'.$dataRow->SURCHARGE_EXEMP_LIMIT.'" /></td>
                <td><input type="text" name="SURCHARGE_AMT_'.$index.'" id="SURCHARGE_AMT_'.$index.'" class="form-control two-digits" maxlength="15"  autocomplete="off" readonly /></td>
                <td><input type="text" name="ASSESSABLE_VL_CESS_'.$index.'" id="ASSESSABLE_VL_CESS_'.$index.'"  class="form-control two-digits" maxlength="15"  autocomplete="off"  /></td>
                <td><input type="text" name="CESS_RATE_'.$index.'" id="CESS_RATE_'.$index.'" value="'.$dataRow->CESS_RATE.'" class="form-control four-digits" maxlength="12"  autocomplete="off"  readonly/></td>
                <td hidden><input type="hidden" name="CESS_EXEMPT_'.$index.'" id="CESS_EXEMPT_'.$index.'" class="form-control two-digits" value="'.$dataRow->CESS_EXEMP_LIMIT.'" /></td>
                <td><input type="text" name="CESS_AMT_'.$index.'" id="CESS_AMT_'.$index.'" class="form-control two-digits" maxlength="15"  autocomplete="off" readonly /></td>
                <td><input type="text" name="ASSESSABLE_VL_SPCESS_'.$index.'" id="ASSESSABLE_VL_SPCESS_'.$index.'"  class="form-control two-digits" maxlength="15"  autocomplete="off"  /></td>
                <td><input type="text" name="SPCESS_RATE_'.$index.'" id="SPCESS_RATE_'.$index.'" value="'.$dataRow->SP_CESS_RATE.'" class="form-control four-digits" maxlength="12"  autocomplete="off" readonly /></td>
                <td hidden><input type="hidden" name="SPCESS_EXEMPT_'.$index.'" id="SPCESS_EXEMPT_'.$index.'" class="form-control two-digits" value="'.$dataRow->SP_CESS_EXEMP_LIMIT.'" /></td>
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

    public function getItemDetailsGRNwise(Request $request){
        $Status = "A";
        $id = $request['id'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $taxstate = $request['taxstate'];  
        $VENDORID= $request['vendorid'];

        $AlpsStatus =   $this->AlpsStatus();

            $log_data = [ 
                $id
            ];

            $Objquote =  DB::select('EXEC SP_GET_ITEM_GRNWISE ?', $log_data);
            
            if(!empty($Objquote)){

                 //CHECK VENDOER PRICE LIST FIRST GROUP WISE, NOT FOUND THEN VENDOR WISE  
                 $objVendorMst =  DB::select('SELECT TOP 1 VID,VCODE,VGID_REF FROM TBL_MST_VENDOR  WHERE VID = ?', [ $VENDORID ]);         
                 $VGID = $objVendorMst[0]->VGID_REF;
                 $objVPLHDR =  DB::select('SELECT VPLID,VGID_REF,VID_REF FROM TBL_MST_VENDORPRICELIST_HDR  where VGID_REF=? AND STATUS=?', [$VGID, 'A']);   //check vendor group

                 
                 if(empty($objVPLHDR)){
                     $objVPLHDR =  DB::select('SELECT VPLID,VGID_REF,VID_REF FROM TBL_MST_VENDORPRICELIST_HDR  where VID_REF=? AND STATUS=?', [$VENDORID, 'A']); //check vendor
                 }

                foreach ($Objquote as $index=>$dataRow){

                    
                    $ObjItem =  DB::select('SELECT top 1 * FROM TBL_MST_ITEM  
                                WHERE ITEMID = ? ', [$dataRow->ITEMID_REF]);

                                    $ObjLIST=[];
                                    if(!empty($objVPLHDR)){
                                        $ObjLIST =   DB::table('TBL_MST_VENDORPRICELIST_MAT')  
                                            ->select('*')
                                            ->where('VPLID_REF','=',$objVPLHDR[0]->VPLID)
                                            ->where('ITEMID_REF','=',$dataRow->ITEMID_REF)
                                            ->where('UOMID_REF','=',$dataRow->MAIN_UOMID_REF)
                                            ->first();
                                    }
                                        
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
                                                $ObjStdCost =  ($ObjLIST->LP*100)/$ObjTaxDet;
                                                $StdCost = $ObjStdCost;
                                                // echo($ObjStdCost);
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
                                                $StdCost = $ObjLIST->LP;
                                                // echo($ObjLIST->LISTPRICE);
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
                                [$CYID_REF, $dataRow->MAIN_UOMID_REF, $Status ]);

                    $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ?  AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $dataRow->ALT_UOMID_REF, $Status ]);
                    
                    $ObjAltQTY =  DB::select('SELECT TOP 1  * FROM TBL_MST_ITEM_UOMCONV  
                                WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                                [$dataRow->ITEMID_REF,$dataRow->ALT_UOMID_REF ]);
                    
                    if(!is_null($ObjItem[0]->BUID_REF))
                    {
                        $ObjBusinessUnit =  DB::select('SELECT TOP 1  * FROM TBL_MST_BUSINESSUNIT  
                                WHERE  CYID_REF = ? AND BRID_REF = ?  AND BUID = ?', 
                                [$CYID_REF, $BRID_REF, $ObjItem[0]->BUID_REF]);
                    }
                    else
                    {
                        $ObjBusinessUnit = NULL;
                    }
                    
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
                
                    $row = '';
                    if($taxstate != "OutofState"){
                        $row = $row.'<tr id="item_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'"  class="clsitemid"><td  style="width:6.5%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td>'.$ObjItem[0]->ICODE;
                        $row = $row.'<input type="hidden" id="txtitem_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" data-desc="'.$ObjItem[0]->ICODE.'" data-desc1="'.$dataRow->RECEIVED_QTY_MU.'" data-desc2="'.$dataRow->RECEIVED_QTY_AU.'"
                        value="'.$ObjItem[0]->ITEMID.'"/></td><td id="itemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" >'.$ObjItem[0]->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" data-desc="'.$ObjItem[0]->ITEM_SPECI.'"
                        value="'.$ObjItem[0]->NAME.'"/></td>';
                        $row = $row.'<td id="itemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" ><input type="hidden" id="txtitemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                        value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td id="uomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" ><input type="hidden" id="txtuomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" 
                        value="'.$dataRow->ALT_UOMID_REF.'"/>'.$dataRow->RECEIVED_QTY_MU.'</td>';
                        $row = $row.'<td id="irate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'"><input type="hidden" id="txtirate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" 
                        value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                        $row = $row.'<td id="itax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'"><input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" data-desc="'.$Taxid[0].'"
                        value="'.$Taxid[1].'"/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td>'.(isset($ObjBusinessUnit) && $ObjBusinessUnit != NULL ? $ObjBusinessUnit[0]->BUCODE.'-'.$ObjBusinessUnit[0]->BUNAME : '').'</td>';
                        $row = $row.'<td '.$AlpsStatus['hidden'].' >'.$ObjItem[0]->ALPS_PART_NO.'</td>';
                        $row = $row.'<td '.$AlpsStatus['hidden'].' >'.$ObjItem[0]->CUSTOMER_PART_NO.'</td>';
                        $row = $row.'<td '.$AlpsStatus['hidden'].' >'.$ObjItem[0]->OEM_PART_NO.'</td>';
                        $row = $row.'<td id="ise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'"><input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'"  data-desc="'.$dataRow->GEID_REF.'"
                        value="'.$dataRow->POID_REF.'"/>Authorized</td>
                        </tr>';
                    }
                    else
                    {
                        $row = $row.'<tr id="item_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'"  class="clsitemid"><td  style="width:6.5%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td>'.$ObjItem[0]->ICODE;
                        $row = $row.'<input type="hidden" id="txtitem_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" data-desc="'.$ObjItem[0]->ICODE.'" data-desc1="'.$dataRow->RECEIVED_QTY_MU.'" data-desc2="'.$dataRow->RECEIVED_QTY_AU.'"
                        value="'.$ObjItem[0]->ITEMID.'"/></td><td id="itemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" >'.$ObjItem[0]->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" data-desc="'.$ObjItem[0]->ITEM_SPECI.'"
                        value="'.$ObjItem[0]->NAME.'"/></td>';
                        $row = $row.'<td id="itemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" ><input type="hidden" id="txtitemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                        value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td id="uomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" ><input type="hidden" id="txtuomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" 
                        value="'.$dataRow->ALT_UOMID_REF.'"/>'.$dataRow->RECEIVED_QTY_MU.'</td>';
                        $row = $row.'<td id="irate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'"><input type="hidden" id="txtirate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'"
                        value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                        $row = $row.'<td id="itax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'"><input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" data-desc="'.$Taxid[0].'"
                        value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td>'.(isset($ObjBusinessUnit) && $ObjBusinessUnit != NULL ? $ObjBusinessUnit[0]->BUCODE.'-'.$ObjBusinessUnit[0]->BUNAME : '').'</td>';
                        $row = $row.'<td '.$AlpsStatus['hidden'].' >'.$ObjItem[0]->ALPS_PART_NO.'</td>';
                        $row = $row.'<td '.$AlpsStatus['hidden'].' >'.$ObjItem[0]->CUSTOMER_PART_NO.'</td>';
                        $row = $row.'<td '.$AlpsStatus['hidden'].' >'.$ObjItem[0]->OEM_PART_NO.'</td>';
                        $row = $row.'<td id="ise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'"><input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" data-desc="'.$dataRow->GEID_REF.'"
                        value="'.$dataRow->POID_REF.'"/>Authorized</td>
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
        
        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $AlpsStatus =   $this->AlpsStatus();



        $taxstate   =   $request['taxstate'];
        $StdCost    =   0;
        $Taxid      =   [];
        $CODE       =   $request['CODE'];
        $NAME       =   $request['NAME'];
        $MUOM       =   $request['MUOM'];
        $GROUP      =   $request['GROUP'];
        $CTGRY      =   $request['CTGRY'];
        $BUNIT      =   $request['BUNIT'];
        $APART      =   $request['APART'];
        $CPART      =   $request['CPART'];
        $OPART      =   $request['OPART'];

        //$sp_popup = [$CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART];
        //$ObjItem = DB::select('EXEC sp_get_items_popup_enquiry ?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);
        
        $sp_popup = [$CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART,$taxstate]; 
        $ObjItem = DB::select('EXEC sp_get_items_popup_withtax ?,?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);
   
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


                $ITEMID             =   $ITEMID;
                $IMPORT_DUTYID_REF  =   $request['IMPORT_DUTYID_REF'];

                $ImortDutyTax       =   array();

                if($IMPORT_DUTYID_REF !=""){

                    $objImportDuty      =   DB::table('TBL_MST_IMPORT_DUTY')  
                                            ->select('ALIAS')
                                            ->where('IMPORT_DUTYID','=',$IMPORT_DUTYID_REF)
                                            ->first();
                
                    $IDF                =   $objImportDuty->ALIAS;

                    $SLID_REF   =   $request['VID_REF'];
                    $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
                    $VID_REF    =   $ObVID->VID;
                
                    $ImortDutyTax   =  DB::select("SELECT top 1 T1.$IDF,T1.SWS,T1.TAX 
                    FROM TBL_MST_VENDOR_CD_MAT T1
                    INNER JOIN TBL_MST_VENDOR_CD_HDR T2 ON T1.VCDID_REF=T2.VCDID AND T2.STATUS='A' AND (T2.DEACTIVATED IS NULL OR T2.DEACTIVATED ='0')
                    WHERE T1.VID_REF='$VID_REF' AND T1.ITEMID_REF='$ITEMID'");

                }

                $CustomDutyRate =   "0.000";
                $SWSRate        =   "0.000";
                $IGSTRate       =   "0.000";

                if(isset($ImortDutyTax) && !empty($ImortDutyTax)){

                    $CustomDutyRate =   $ImortDutyTax[0]->$IDF > 0?$ImortDutyTax[0]->$IDF:"0.0000";
                    $SWSRate        =   $ImortDutyTax[0]->SWS > 0?$ImortDutyTax[0]->SWS:"0.0000";
                    $IGSTRate       =   $ImortDutyTax[0]->TAX > 0?$ImortDutyTax[0]->TAX:"0.0000";

                }
                        
                        
                $row = '';

                $StdCost    =   $STDCOST;

                $row = $row.'<tr id="item_'.$ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1"  ></td>';
                    $row = $row.'<td style="width:10%;">'.$ICODE;
                    $row = $row.'<input type="hidden" id="txtitem_'.$ITEMID.'" data-desc="'.$ICODE.'" data-desc1="0.00" data-desc2="0.00"  data-desc4="'.$CustomDutyRate.'" data-desc5="'.$SWSRate.'" data-desc6="'.$IGSTRate.'" value="'.$ITEMID.'"/></td>
                    <td style="width:10%;" id="itemname_'.$ITEMID.'" >'.$NAME;
                    $row = $row.'<input type="hidden" id="txtitemname_'.$ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'"
                    value="'.$NAME.'"/></td>';
                    $row = $row.'<td style="width:8%;" id="itemuom_'.$ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ITEMID.'" data-desc="'.$Alt_UOM.'" data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'"  value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>';
                    $row = $row.'<td style="width:8%;" id="uomqty_'.$ITEMID.'" ><input type="hidden" id="txtuomqty_'.$ITEMID.'" data-desc="'.$TOQTY.'" value="'.$ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                    $row = $row.'<td style="width:8%;" id="irate_'.$ITEMID.'"><input type="hidden" id="txtirate_'.$ITEMID.'" data-desc="'.$FROMQTY.'" value="'.$StdCost.'"/>'.$GroupName.'</td>';
                    $row = $row.'<td style="width:8%;" id="itax_'.$ITEMID.'"><input type="hidden" id="txtitax_'.$ITEMID.'" data-desc="'.$Taxid1.'" value="'.$Taxid2.'"/>'.$Categoryname.'</td>
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
    
    /*
	public function getItemDetails(Request $request){

        $Status             =   "A";
        $CYID_REF           =   Auth::user()->CYID_REF;
        $BRID_REF           =   Session::get('BRID_REF');
        $FYID_REF           =   Session::get('FYID_REF');
        $taxstate           =   $request['taxstate'];
        $IMPORT_DUTYID_REF  =   $request['IMPORT_DUTYID_REF'];
        $VID_REF            =   $request['VID_REF'];
        $StdCost            =   0;

        $ObjItem    =   DB::select('SELECT * FROM TBL_MST_ITEM  WHERE CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', [$CYID_REF, $BRID_REF, $FYID_REF, $Status ]);
                   
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
                        WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND UOMID = ? 
                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                        [$CYID_REF, $BRID_REF, $FYID_REF,$dataRow->MAIN_UOMID_REF, 'A' ]);

            $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                        WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND UOMID = ? 
                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                        [$CYID_REF, $BRID_REF, $FYID_REF,$dataRow->ALT_UOMID_REF, $Status ]);
            
            $ObjAltQTY =  DB::select('SELECT TOP 1  * FROM TBL_MST_ITEM_UOMCONV  
                        WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                        [$dataRow->ITEMID,$dataRow->ALT_UOMID_REF ]);

            $TOQTY =  isset($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 0;
            $FROMQTY =  isset($ObjAltQTY[0]->FROM_QTY)? $ObjAltQTY[0]->FROM_QTY : 0;

            $ObjItemGroup =  DB::select('SELECT TOP 1 GROUPCODE, GROUPNAME FROM TBL_MST_ITEMGROUP  
                        WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ITEMGID = ?
                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                        [$CYID_REF, $BRID_REF, $FYID_REF,$dataRow->ITEMGID_REF, 'A' ]);

            $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS FROM TBL_MST_ITEMCATEGORY  
                        WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ICID = ?
                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                        [$CYID_REF, $BRID_REF, $FYID_REF,$dataRow->ICID_REF, 'A' ]);


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




            $objImportDuty =   DB::table('TBL_MST_IMPORT_DUTY')  
            ->select('ALIAS')
            ->where('IMPORT_DUTYID','=',$IMPORT_DUTYID_REF)
            ->first();

            $ITEMID         =   $dataRow->ITEMID;
            $IDF            =   $objImportDuty->ALIAS;
            
            $ImortDutyTax   =  DB::select("SELECT top 1 T1.$IDF,T1.SWS,T1.TAX 
            FROM TBL_MST_VENDOR_CD_MAT T1
            INNER JOIN TBL_MST_VENDOR_CD_HDR T2 ON T1.VCDID_REF=T2.VCDID AND T2.STATUS='A' AND (T2.DEACTIVATED IS NULL OR T2.DEACTIVATED ='0')
            WHERE T1.VID_REF='$VID_REF' AND T1.ITEMID_REF='$ITEMID'");


            $CustomDutyRate =   "0.000";
            $SWSRate        =   "0.000";
            $IGSTRate       =   "0.000";

            if(isset($ImortDutyTax) && !empty($ImortDutyTax)){

                $CustomDutyRate =   $ImortDutyTax[0]->$IDF > 0?$ImortDutyTax[0]->$IDF:"0.0000";
                $SWSRate        =   $ImortDutyTax[0]->SWS > 0?$ImortDutyTax[0]->SWS:"0.0000";
                $IGSTRate       =   $ImortDutyTax[0]->TAX > 0?$ImortDutyTax[0]->TAX:"0.0000";

            }

                
            
            
                
                $row = '';
                if($taxstate != "OutofState"){
                $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'" data-desc1="0.00" data-desc2="0.00"  data-desc4="'.$CustomDutyRate.'" data-desc5="'.$SWSRate.'" data-desc6="'.$IGSTRate.'"
                value="'.$dataRow->ITEMID.'"/></td>
                <td style="width:10%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'"
                value="'.$dataRow->NAME.'"/></td>';
                $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                $row = $row.'<td style="width:8%;" id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'"
                value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                $row = $row.'<td style="width:8%;" id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="'.$FROMQTY.'"
                value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                $row = $row.'<td style="width:8%;" id="itax_'.$dataRow->ITEMID.'"><input type="hidden" id="txtitax_'.$dataRow->ITEMID.'" data-desc="'.$Taxid[0].'"
                value="'.$Taxid[1].'"/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                <td style="width:8%;">'.$BusinessUnit.'</td>
                <td style="width:8%;">'.$ALPS_PART_NO.'</td>
                <td style="width:8%;">'.$CUSTOMER_PART_NO.'</td>
                <td style="width:8%;">'.$OEM_PART_NO.'</td>
                <td style="width:8%;">Authorized</td>
                </tr>';
                }
                else
                {
                    $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                    $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                    $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'" data-desc1="0.00" data-desc2="0.00"  data-desc4="'.$CustomDutyRate.'" data-desc5="'.$SWSRate.'" data-desc6="'.$IGSTRate.'"
                    value="'.$dataRow->ITEMID.'"/></td>
                    <td style="width:10%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                    $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'"
                    value="'.$dataRow->NAME.'"/></td>';
                    $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                    value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                    $row = $row.'<td style="width:8%;" id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'"
                    value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                    $row = $row.'<td style="width:8%;" id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="'.$FROMQTY.'"
                    value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                    $row = $row.'<td style="width:8%;" id="itax_'.$dataRow->ITEMID.'"><input type="hidden" id="txtitax_'.$dataRow->ITEMID.'" data-desc="'.$Taxid[0].'"
                    value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                    <td style="width:8%;">'.$BusinessUnit.'</td>
                    <td style="width:8%;">'.$ALPS_PART_NO.'</td>
                    <td style="width:8%;">'.$CUSTOMER_PART_NO.'</td>
                    <td style="width:8%;">'.$OEM_PART_NO.'</td>
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
    }*/

    public function save(Request $request) {
		
		
        
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];
        $r_count5 = $request['Row_Count5'];
        $r_count6 = $request['Row_Count6'];

        $GROSS_TOTAL    =   0; 
        $NET_TOTAL 		= 	$request['tot_amt'];
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
                    'ITEMID_REF'                => $request['ITEMID_REF_'.$i],
                    'MAIN_UOMID_REF'                 => $request['MAIN_UOMID_REF_'.$i],
                    'IPO_MAIN_QTY'              => (!is_null($request['SO_QTY_'.$i]) ? $request['SO_QTY_'.$i] : 0),
                    'ALT_UOMID_REF'             => $request['ALT_UOMID_REF_'.$i],
                    'ITEM_SPECI'                  => (!is_null($request['Itemspec_'.$i]) ? $request['Itemspec_'.$i] : 0),
                    'RATE_ASP_MU'            => $request['RATEPUOM_'.$i],
                    'DISC_PER'             => (!is_null($request['DISC_PER_'.$i]) ? $request['DISC_PER_'.$i] : 0),
                    'DISC_AMT'                  => (!is_null($request['DISC_AMT_'.$i]) ? $request['DISC_AMT_'.$i] : 0),
                    'FREIGHT_AMT'                  => (!is_null($request['FREIGHT_AMT_'.$i]) ? $request['FREIGHT_AMT_'.$i] : 0),
                    'INSURANCE_AMT'                      => (!is_null($request['INSURANCE_AMT_'.$i]) ? $request['INSURANCE_AMT_'.$i] : 0),
                    'ASSESSABLE_VALUE'                      => (!is_null($request['ASSESSABLE_VALUE_'.$i]) ? $request['ASSESSABLE_VALUE_'.$i] : 0),
                    'CUSTOME_DUTY_RATE'                      => (!is_null($request['CUSTOME_DUTY_RATE_PER_'.$i]) ? $request['CUSTOME_DUTY_RATE_PER_'.$i] : 0),
                    'SWS_RATE'                 => (isset($request['SWS_RATE_PER_'.$i]) ? $request['SWS_RATE_PER_'.$i] : ''),
                    'IGST'                  => (isset($request['IGST_RATE_PER_'.$i]) ? $request['IGST_RATE_PER_'.$i] : ''),
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
                if(isset($request['CTID_REF']) && !is_null($request['CTID_REF']))
                {
                    if(isset($request['CT_TYPE_'.$i]) && $request['CT_TYPE_'.$i]=="DISCOUNT"){
                          
                        $DISCOUNT      += $request['VALUE_'.$i]; 
                    }else{
                        $OTHER_CHARGES += $request['VALUE_'.$i];   
                    }

                    if(isset($request['TID_REF_'.$i]))
                    {
                        $reqdata3[$i] = [
                            'CTID_REF'      => $request['CTID_REF'] ,
                            'TID_REF'       => $request['TID_REF_'.$i],
                            'RATE'          => $request['RATE_'.$i],
                            'VALUE'         => $request['VALUE_'.$i],
                            //'GST'           => (isset($request['calGST_'.$i])!="true" ? 0 : 1) ,
                            'IGST'          => (isset($request['calIGST_'.$i]) && !empty($request['calIGST_'.$i]) ? $request['calIGST_'.$i] : 0),
                            //'CGST'          => (isset($request['calCGST_'.$i]) && !empty($request['calCGST_'.$i]) ? $request['calCGST_'.$i] : 0),
                            //'SGST'          => (isset($request['calSGST_'.$i]) && !empty($request['calSGST_'.$i]) ? $request['calSGST_'.$i] : 0),
                            //'ACTUAL'        => (isset($request['calACTUAL_'.$i])!="true" ? 0 : 1) ,
                        ];
                    }
                }
            
        }
        if(isset($reqdata3))
        { 
            $wrapped_links3["CAL"] = $reqdata3; 
            $XMLCAL = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $XMLCAL = NULL; 
        }


        for ($i=0; $i<=$r_count4; $i++)
        {
            if(isset($request['TDSID_REF_'.$i]))
            {
                if(isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0==1){
                    $TDS_AMOUNT      += $request['TDS_AMT_'.$i]; 
                    }

                $reqdata4[$i] = [
                    'TDSID_REF'                 => $request['TDSID_REF_'.$i],
                    'ASSESSABLE_VL_TDS'         => (!is_null($request['ASSESSABLE_VL_TDS_'.$i]) ? $request['ASSESSABLE_VL_TDS_'.$i] : 0),
                    'TDS_RATE'                  => (!is_null($request['TDS_RATE_'.$i]) ? $request['TDS_RATE_'.$i] : 0),
                    'ASSESSABLE_VL_SURCHAPGE'   => (!is_null($request['ASSESSABLE_VL_SURCHARGE_'.$i]) ? $request['ASSESSABLE_VL_SURCHARGE_'.$i] : 0),
                    'SURCHAPGE_RATE'            => (!is_null($request['SURCHARGE_RATE_'.$i]) ? $request['SURCHARGE_RATE_'.$i] : 0),
                    'ASSESSABLE_VL_CESS'        => (!is_null($request['ASSESSABLE_VL_CESS_'.$i]) ? $request['ASSESSABLE_VL_CESS_'.$i] : 0),
                    'CESS_RATE'                 => (!is_null($request['CESS_RATE_'.$i]) ? $request['CESS_RATE_'.$i] : 0),
                    'ASSESSABLE_VL_SPCESS'      => (!is_null($request['ASSESSABLE_VL_SPCESS_'.$i]) ? $request['ASSESSABLE_VL_SPCESS_'.$i] : 0),
                    'SPCESS_RATE'               => (!is_null($request['SPCESS_RATE_'.$i]) ? $request['SPCESS_RATE_'.$i] : 0),
                ];
            }
        }


        if(isset($reqdata4)){ 
            $wrapped_links4["TDSD"] = $reqdata4; 

            if(isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'){
                $XMLTDSD = ArrayToXml::convert($wrapped_links4);
            }
            else{
                $XMLTDSD = NULL; 
            }
        }
        else
        {
            $XMLTDSD = NULL; 
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


        for ($i=0; $i<=$r_count6; $i++)
        {
            if(isset($request['UDFPBID_REF_'.$i]) && !is_null($request['UDFPBID_REF_'.$i]))
            {
                $reqdata6[$i] = [
                    'UDFIPOID_REF'  => $request['UDFPBID_REF_'.$i],
                    'VALUE'         => $request['udfvalue_'.$i],
                ];
            }
        }

        if(isset($reqdata6))
        { 
            $wrapped_links6["UDF"] = $reqdata6; 
            $XMLUDF = ArrayToXml::convert($wrapped_links6);
        }
        else
        {
            $XMLUDF = NULL; 
        }


        



        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $IPO_NO             =   $request['IPO_NO'];
        $IPO_DT             =   $request['IPO_DT'];
        $VID_REF            =   $request['VID_REF'];
        $FC                 =   (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF           =   $request['CRID_REF'];
        $CONVFACT           =   $request['CONVFACT'];
        $SALE_ORDER_NO      =   $request['SALE_ORDER_NO'];
        $DOC_TYPE           =   $request['DOC_TYPE'];
        $BILL_TO            =   $request['BILLTO'];
        $SHIP_TO            =   $request['SHIPTO'];
        $COUNTRY_FROM       =   NULL;
        $REMARKS            =   $request['REMARKS'];
        $FOB                =   $request['FOB'];
        $SLID_REF           =   $request['SLID_REF'];
        $REQ_DELIVERY_DATE  =   $request['REQ_DELIVERY_DATE'];
        $IMPORT_DUTYID_REF  =   $request['IMPORT_DUTYID_REF'];
        $REF_NO             =   $request['REF_NO'];
        $CREDIT_DAYS        =   (!is_null($request['Credit_days']) ? $request['Credit_days'] : 0);
        $DUE_DATE           =   (!is_null($request['DUE_DATE']) ? $request['DUE_DATE'] : '');
        $TDS                =   (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);
       
        $log_data = [ 
            $IPO_NO,$IPO_DT,$VID_REF,$FC,$CRID_REF,
            $CONVFACT,$SALE_ORDER_NO,$DOC_TYPE,$BILL_TO,$SHIP_TO,
            $COUNTRY_FROM,$REMARKS,$FOB,$SLID_REF,$REQ_DELIVERY_DATE,
            $IMPORT_DUTYID_REF,$REF_NO,$CREDIT_DAYS,$DUE_DATE,$TDS,
            $CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT,
            $XMLTNC,$XMLUDF,$XMLCAL,$XMLPSLB,$XMLTDSD,
            $USERID,Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
            ,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT
        ];
		
        
        $sp_result = DB::select('EXEC SP_IPO_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  
		//dd($sp_result[0]->RESULT);
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

            $objHDR = DB::table('TBL_TRN_IPO_HDR')
                    ->where('TBL_TRN_IPO_HDR.FYID_REF','=',$FYID_REF)
                    ->where('TBL_TRN_IPO_HDR.CYID_REF','=',$CYID_REF)
                    ->where('TBL_TRN_IPO_HDR.BRID_REF','=',$BRID_REF)
                    ->where('TBL_TRN_IPO_HDR.IPO_ID','=',$id)
                    ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_IPO_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
                    ->select('TBL_TRN_IPO_HDR.*','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
                    ->first();

                 


            $objPBVID =[];
            if(isset($objHDR->VID_REF) && $objHDR->VID_REF !=""){
            $objPBVID = DB::table('TBL_MST_SUBLEDGER')
                    ->where('BELONGS_TO','=','Vendor')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('SGLID','=',$objHDR->VID_REF)    
                    ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                    ->first();
            }


            $objsubglcode =[];
            if(isset($objHDR->SLID_REF) && $objHDR->SLID_REF){
                $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('SGLID','=',$objHDR->SLID_REF)
                    ->select('TBL_MST_SUBLEDGER.*')
                    ->first();
            }
        


            $objImportDuty      =   $this->getImportDutyList();


               

        
                
                $TAXSTATE=[];
                $objShpAddress=[] ;
                $objBillAddress=[];


                if(isset($objHDR->SHIP_TO) && $objHDR->SHIP_TO !=""){
                $sid = $objHDR->SHIP_TO;

                if(is_null($sid)){
                    $TAXSTATE[]         =   NULL;
                    $objShpAddress[]    =   NULL;
                }
                else{

                    $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION WHERE  SHIPTO= ? AND LID = ? ', [1,$sid]);
                    $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH WHERE BRID= ? ', [$BRID_REF]);

                    if($ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF){
                        $TAXSTATE[] = 'WithinState';
                    }
                    else{
                        $TAXSTATE[] = 'OutofState';
                    }
               

                    $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                                WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                                [$Status,$ObjSHIPTO[0]->CITYID_REF,$ObjSHIPTO[0]->CTRYID_REF,$ObjSHIPTO[0]->STID_REF]);
            
                    $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                                WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjSHIPTO[0]->STID_REF,$ObjSHIPTO[0]->CTRYID_REF]);
            
                    $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                                WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjSHIPTO[0]->CTRYID_REF]);
        
                    $ObjAddressID = $ObjSHIPTO[0]->LID;
                    if(!empty($ObjSHIPTO)){
                        $objShpAddress[] = $ObjSHIPTO[0]->LADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                    }

                }

                }


                if(isset( $objHDR->BILL_TO) &&  $objHDR->BILL_TO !=""){
                
                $bid            =   $objHDR->BILL_TO;

                if(is_null($bid)){
                    $objBillAddress[]=NULL; 
                }
                else{

                    $ObjBILLTO      =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION WHERE BILLTO= ? AND LID = ? ', [1,$bid]);
                    $ObjCity2       =  DB::select('SELECT top 1 * FROM TBL_MST_CITY WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', [$Status,$ObjBILLTO[0]->CITYID_REF,$ObjBILLTO[0]->CTRYID_REF,$ObjBILLTO[0]->STID_REF]);
                    $ObjState2      =  DB::select('SELECT top 1 * FROM TBL_MST_STATE WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBILLTO[0]->STID_REF,$ObjBILLTO[0]->CTRYID_REF]);
                    $ObjCountry2    =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBILLTO[0]->CTRYID_REF]);
            
                    $ObjAddressID = $ObjBILLTO[0]->LID;
                    if(!empty($ObjBILLTO)){
                        $objBillAddress[] = $ObjBILLTO[0]->LADD .' '.$ObjCity2[0]->NAME .' '.$ObjState2[0]->NAME .' '.$ObjCountry2[0]->NAME;
                    }
                }

            }
                
                $log_data = [ 
                    $id
                ];
                
                //$objPBMAT = DB::select('EXEC SP_GET_PB_MATERIAL ?', $log_data);
                

                $objPBMAT = DB::select("SELECT 
                T1.*,
                T2.ICODE,T2.NAME AS ITEM_NAME,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE
                FROM TBL_TRN_IPO_MAT T1
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T1.ALT_UOMID_REF=T4.UOMID
                WHERE T1.IPO_ID_REF='$id' ORDER BY T1.IPO_ID_REF ASC
                ");                    
                                

                $objCount1 = count($objPBMAT);  
                
                $objPBUDF = DB::table('TBL_TRN_IPO_UDF')                    
                                ->where('TBL_TRN_IPO_UDF.IPO_ID_REF','=',$id)
                                ->get()
                                ->toArray();
                
                $objCount2 = count($objPBUDF);

                $objPBTNC = DB::table('TBL_TRN_IPO_TNC')                    
                                ->where('TBL_TRN_IPO_TNC.IPO_ID_REF','=',$id)
                                ->get()->toArray();
                
                $objCount3 = count($objPBTNC);

                $objPBCAL = DB::table('TBL_TRN_IPO_CAL')                    
                                ->where('TBL_TRN_IPO_CAL.IPO_ID_REF','=',$id)
                                ->get()->toArray();
                
                $objCount4 = count($objPBCAL);
                $objPBPSLB = DB::table('TBL_TRN_IPO_PSLB')                    
                                ->where('TBL_TRN_IPO_PSLB.IPO_ID_REF','=',$id)
                                ->get()->toArray();
                
                $objCount5 = count($objPBPSLB);
 
                $objPBTDS = DB::select('EXEC SP_GET_IPO_TDS ?', $log_data);
                
                $objCount6 = count($objPBTDS);
        
                $objRights = DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_USERROLMAP.BRID_REF','=',Auth::user()->BRID_REF)
                                ->where('TBL_MST_USERROLMAP.FYID_REF','=',Auth::user()->FYID_REF)
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();

                

               // $objvendor          =   $this->getvendor();
        
                $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
                    WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                    order by TNC_CODE ASC', [$CYID_REF, $BRID_REF, $FYID_REF, 'A' ]);
        
                $objCalculationHeader	=   Helper::getCalculationHeader(array(
                                            'CYID_REF'=>Auth::user()->CYID_REF,
                                            'BRID_REF'=>Session::get('BRID_REF'),
                                            'USERID'=>Auth::user()->USERID,
                                            'HEADING'=>'Transactions',
                                            'VTID_REF'=>$this->vtid_ref,
                                            'FORMID'=>$this->form_id
                                            ));

                $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
                WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? 
                order by CTCODE ASC', [$CYID_REF, $BRID_REF, $FYID_REF ]);
                
                
                $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_IPO")->select('*')
                            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                        {       
                                        $query->select('UDFIPOID')->from('TBL_MST_UDFFOR_IPO')
                                                        ->where('STATUS','=','A')
                                                        ->where('PARENTID','=',0)
                                                        ->where('DEACTIVATED','=',0)
                                                        ->where('CYID_REF','=',$CYID_REF)
                                                        ->where('BRID_REF','=',$BRID_REF);
                                                                          
                            })->where('DEACTIVATED','=',0)
                            ->where('STATUS','<>','C')                    
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('BRID_REF','=',$BRID_REF);
                                              
                            
        
                $objUdfPBData = DB::table('TBL_MST_UDFFOR_IPO')
                    ->where('STATUS','=','A')
                    ->where('PARENTID','=',0)
                    ->where('DEACTIVATED','=',0)
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF)
                    ->union($ObjUnionUDF)
                    ->get()->toArray();   
                $objCountUDF = count($objUdfPBData);

                $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_IPO")->select('*')
                            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                        {       
                                        $query->select('UDFIPOID')->from('TBL_MST_UDFFOR_IPO')
                                                        ->where('PARENTID','=',0)
                                                        ->where('DEACTIVATED','=',0)
                                                        ->where('CYID_REF','=',$CYID_REF)
                                                        ->where('BRID_REF','=',$BRID_REF);              
                            })->where('DEACTIVATED','=',0)              
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('BRID_REF','=',$BRID_REF);               
                            
        
                $objUdfPBData2 = DB::table('TBL_MST_UDFFOR_IPO')
                    ->where('PARENTID','=',0)
                    ->where('DEACTIVATED','=',0)
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF)
                    ->union($ObjUnionUDF2)
                    ->get()->toArray(); 
            
                $FormId     =   $this->form_id;

                $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
                ->get() ->toArray(); 

                $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')
                ->get() ->toArray(); 

                $objlastdt          =   $this->getLastdt();

                $AlpsStatus =   $this->AlpsStatus();
                $ActionStatus   =   "";

                $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
                $required_status    =   $this->required_status();

                $objothcurrency = $this->GetCurrencyMaster(); 
            
            return view($this->view.$FormId.'edit',compact(['AlpsStatus','FormId','objRights','objHDR','objPBVID','TAXSTATE','objShpAddress',
                'objBillAddress','objPBMAT','objPBUDF','objPBTNC','objPBCAL','objPBPSLB','objPBTDS','objCount1','objCount2','objCount3',
                'objCount4','objCount5','objCount6','objUdfPBData','objCountUDF','objTNCHeader',
                'objCalculationHeader','objTNCDetails','objCalDetails','objlastdt','objCalHeader','objUdfPBData2',
                'objothcurrency','objImportDuty','objsubglcode','ActionStatus',
                'TabSetting','required_status'
                ]));      

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
        $NET_TOTAL 		= 	$request['tot_amt'];
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
                    'ITEMID_REF'                => $request['ITEMID_REF_'.$i],
                    'MAIN_UOMID_REF'                 => $request['MAIN_UOMID_REF_'.$i],
                    'IPO_MAIN_QTY'              => (!is_null($request['SO_QTY_'.$i]) ? $request['SO_QTY_'.$i] : 0),
                    'ALT_UOMID_REF'             => $request['ALT_UOMID_REF_'.$i],
                    'ITEM_SPECI'                  => (!is_null($request['Itemspec_'.$i]) ? $request['Itemspec_'.$i] : 0),
                    'RATE_ASP_MU'            => $request['RATEPUOM_'.$i],
                    'DISC_PER'             => (!is_null($request['DISC_PER_'.$i]) ? $request['DISC_PER_'.$i] : 0),
                    'DISC_AMT'                  => (!is_null($request['DISC_AMT_'.$i]) ? $request['DISC_AMT_'.$i] : 0),
                    'FREIGHT_AMT'                  => (!is_null($request['FREIGHT_AMT_'.$i]) ? $request['FREIGHT_AMT_'.$i] : 0),
                    'INSURANCE_AMT'                      => (!is_null($request['INSURANCE_AMT_'.$i]) ? $request['INSURANCE_AMT_'.$i] : 0),
                    'ASSESSABLE_VALUE'                      => (!is_null($request['ASSESSABLE_VALUE_'.$i]) ? $request['ASSESSABLE_VALUE_'.$i] : 0),
                    'CUSTOME_DUTY_RATE'                      => (!is_null($request['CUSTOME_DUTY_RATE_PER_'.$i]) ? $request['CUSTOME_DUTY_RATE_PER_'.$i] : 0),
                    'SWS_RATE'                 => (isset($request['SWS_RATE_PER_'.$i]) ? $request['SWS_RATE_PER_'.$i] : ''),
                    'IGST'                  => (isset($request['IGST_RATE_PER_'.$i]) ? $request['IGST_RATE_PER_'.$i] : ''),
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
                if(isset($request['CTID_REF']) && !is_null($request['CTID_REF']))
                {
                    if(isset($request['TID_REF_'.$i]))
                    {
                        if(isset($request['CT_TYPE_'.$i]) && $request['CT_TYPE_'.$i]=="DISCOUNT"){
                          
                            $DISCOUNT      += $request['VALUE_'.$i]; 
                        }else{
                            $OTHER_CHARGES += $request['VALUE_'.$i];   
                        }


                        $reqdata3[$i] = [
                            'CTID_REF'      => $request['CTID_REF'] ,
                            'TID_REF'       => $request['TID_REF_'.$i],
                            'RATE'          => $request['RATE_'.$i],
                            'VALUE'         => $request['VALUE_'.$i],
                            //'GST'           => (isset($request['calGST_'.$i])!="true" ? 0 : 1) ,
                            'IGST'          => (isset($request['calIGST_'.$i]) && !empty($request['calIGST_'.$i]) ? $request['calIGST_'.$i] : 0),
                            //'CGST'          => (isset($request['calCGST_'.$i]) && !empty($request['calCGST_'.$i]) ? $request['calCGST_'.$i] : 0),
                            //'SGST'          => (isset($request['calSGST_'.$i]) && !empty($request['calSGST_'.$i]) ? $request['calSGST_'.$i] : 0),
                            //'ACTUAL'        => (isset($request['calACTUAL_'.$i])!="true" ? 0 : 1) ,
                        ];
                    }
                }
            
        }
        if(isset($reqdata3))
        { 
            $wrapped_links3["CAL"] = $reqdata3; 
            $XMLCAL = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $XMLCAL = NULL; 
        }


        for ($i=0; $i<=$r_count4; $i++)
        {
            if(isset($request['TDSID_REF_'.$i]))
            {
                if(isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0==1){
                    $TDS_AMOUNT      += $request['TDS_AMT_'.$i]; 
                    }

                $reqdata4[$i] = [
                    'TDSID_REF'                 => $request['TDSID_REF_'.$i],
                    'ASSESSABLE_VL_TDS'         => (!is_null($request['ASSESSABLE_VL_TDS_'.$i]) ? $request['ASSESSABLE_VL_TDS_'.$i] : 0),
                    'TDS_RATE'                  => (!is_null($request['TDS_RATE_'.$i]) ? $request['TDS_RATE_'.$i] : 0),
                    'ASSESSABLE_VL_SURCHAPGE'   => (!is_null($request['ASSESSABLE_VL_SURCHARGE_'.$i]) ? $request['ASSESSABLE_VL_SURCHARGE_'.$i] : 0),
                    'SURCHAPGE_RATE'            => (!is_null($request['SURCHARGE_RATE_'.$i]) ? $request['SURCHARGE_RATE_'.$i] : 0),
                    'ASSESSABLE_VL_CESS'        => (!is_null($request['ASSESSABLE_VL_CESS_'.$i]) ? $request['ASSESSABLE_VL_CESS_'.$i] : 0),
                    'CESS_RATE'                 => (!is_null($request['CESS_RATE_'.$i]) ? $request['CESS_RATE_'.$i] : 0),
                    'ASSESSABLE_VL_SPCESS'      => (!is_null($request['ASSESSABLE_VL_SPCESS_'.$i]) ? $request['ASSESSABLE_VL_SPCESS_'.$i] : 0),
                    'SPCESS_RATE'               => (!is_null($request['SPCESS_RATE_'.$i]) ? $request['SPCESS_RATE_'.$i] : 0),
                ];
            }
        }

        if(isset($reqdata4)){ 
            $wrapped_links4["TDSD"] = $reqdata4; 

            if(isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'){
                $XMLTDSD = ArrayToXml::convert($wrapped_links4);
            }
            else{
                $XMLTDSD = NULL; 
            }
        }
        else
        {
            $XMLTDSD = NULL; 
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


        for ($i=0; $i<=$r_count6; $i++)
        {
            if(isset($request['UDFPBID_REF_'.$i]) && !is_null($request['UDFPBID_REF_'.$i]))
            {
                $reqdata6[$i] = [
                    'IPO_UDFID'  => $request['UDFPBID_REF_'.$i],
                    'VALUE'         => $request['udfvalue_'.$i],
                ];
            }
        }

        if(isset($reqdata6))
        { 
            $wrapped_links6["UDF"] = $reqdata6; 
            $XMLUDF = ArrayToXml::convert($wrapped_links6);
        }
        else
        {
            $XMLUDF = NULL; 
        }
 
       

        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $IPO_NO             =   $request['IPO_NO'];
        $IPO_DT             =   $request['IPO_DT'];
        $VID_REF            =   $request['VID_REF'];
        $FC               =   (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF           =   $request['CRID_REF'];
        $CONVFACT           =   $request['CONVFACT'];
        $SALE_ORDER_NO        =   $request['SALE_ORDER_NO'];
        $DOC_TYPE        =   $request['DOC_TYPE'];
        $BILL_TO            =   $request['BILLTO'];
        $SHIP_TO            =   $request['SHIPTO'];
        $COUNTRY_FROM       =   NULL;
        $REMARKS            =   $request['REMARKS'];
        $FOB                =   $request['FOB'];
        $SLID_REF             =   $request['SLID_REF'];
        $REQ_DELIVERY_DATE             =   $request['REQ_DELIVERY_DATE'];
        $IMPORT_DUTYID_REF  =   $request['IMPORT_DUTYID_REF'];
        $REF_NO             =   $request['REF_NO'];
        $CREDIT_DAYS        =   (!is_null($request['Credit_days']) ? $request['Credit_days'] : 0);
        $DUE_DATE           =   (!is_null($request['DUE_DATE']) ? $request['DUE_DATE'] : '');
        $TDS                =   (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);
       
        $log_data = [ 
            $IPO_NO,$IPO_DT,$VID_REF,$FC,$CRID_REF,
            $CONVFACT,$SALE_ORDER_NO,$DOC_TYPE,$BILL_TO,$SHIP_TO,
            $COUNTRY_FROM,$REMARKS,$FOB,$SLID_REF,$REQ_DELIVERY_DATE,
            $IMPORT_DUTYID_REF,$REF_NO,$CREDIT_DAYS,$DUE_DATE,$TDS,
            $CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT,
            $XMLTNC,$XMLUDF,$XMLCAL,$XMLPSLB,$XMLTDSD,
            $USERID,Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
            ,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT
        ];

        
        $sp_result = DB::select('EXEC SP_IPO_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data); 

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $IPO_NO. ' Sucessfully Updated.']);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
    }

    public function view($id=NULL){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){

            $objHDR = DB::table('TBL_TRN_IPO_HDR')
                    ->where('TBL_TRN_IPO_HDR.FYID_REF','=',$FYID_REF)
                    ->where('TBL_TRN_IPO_HDR.CYID_REF','=',$CYID_REF)
                    ->where('TBL_TRN_IPO_HDR.BRID_REF','=',$BRID_REF)
                    ->where('TBL_TRN_IPO_HDR.IPO_ID','=',$id)
                    ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_IPO_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
                    ->select('TBL_TRN_IPO_HDR.*','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
                    ->first();


            $objPBVID =[];
            if(isset($objHDR->VID_REF) && $objHDR->VID_REF !=""){
            $objPBVID = DB::table('TBL_MST_SUBLEDGER')
                    ->where('BELONGS_TO','=','Vendor')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('SGLID','=',$objHDR->VID_REF)    
                    ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                    ->first();
            }


            $objsubglcode =[];
            if(isset($objHDR->SLID_REF) && $objHDR->SLID_REF){
                $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('SGLID','=',$objHDR->SLID_REF)
                    ->select('TBL_MST_SUBLEDGER.*')
                    ->first();
            }
        


            $objImportDuty      =   $this->getImportDutyList();


                       
                $TAXSTATE=[];
                $objShpAddress=[] ;
                $objBillAddress=[];


                if(isset($objHDR->SHIP_TO) && $objHDR->SHIP_TO !=""){
                $sid = $objHDR->SHIP_TO;

                if(is_null($sid)){
                    $TAXSTATE[]         =   NULL;
                    $objShpAddress[]    =   NULL;
                }
                else{

                    $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION WHERE  SHIPTO= ? AND LID = ? ', [1,$sid]);
                    $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH WHERE BRID= ? ', [$BRID_REF]);

                    if($ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF){
                        $TAXSTATE[] = 'WithinState';
                    }
                    else{
                        $TAXSTATE[] = 'OutofState';
                    }
               

                    $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                                WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                                [$Status,$ObjSHIPTO[0]->CITYID_REF,$ObjSHIPTO[0]->CTRYID_REF,$ObjSHIPTO[0]->STID_REF]);
            
                    $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                                WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjSHIPTO[0]->STID_REF,$ObjSHIPTO[0]->CTRYID_REF]);
            
                    $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                                WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjSHIPTO[0]->CTRYID_REF]);
        
                    $ObjAddressID = $ObjSHIPTO[0]->LID;
                    if(!empty($ObjSHIPTO)){
                        $objShpAddress[] = $ObjSHIPTO[0]->LADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                    }

                }

                }


                if(isset( $objHDR->BILL_TO) &&  $objHDR->BILL_TO !=""){
                
                $bid            =   $objHDR->BILL_TO;

                if(is_null($bid)){
                    $objBillAddress[]=NULL; 
                }
                else{

                    $ObjBILLTO      =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION WHERE BILLTO= ? AND LID = ? ', [1,$bid]);
                    $ObjCity2       =  DB::select('SELECT top 1 * FROM TBL_MST_CITY WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', [$Status,$ObjBILLTO[0]->CITYID_REF,$ObjBILLTO[0]->CTRYID_REF,$ObjBILLTO[0]->STID_REF]);
                    $ObjState2      =  DB::select('SELECT top 1 * FROM TBL_MST_STATE WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBILLTO[0]->STID_REF,$ObjBILLTO[0]->CTRYID_REF]);
                    $ObjCountry2    =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBILLTO[0]->CTRYID_REF]);
            
                    $ObjAddressID = $ObjBILLTO[0]->LID;
                    if(!empty($ObjBILLTO)){
                        $objBillAddress[] = $ObjBILLTO[0]->LADD .' '.$ObjCity2[0]->NAME .' '.$ObjState2[0]->NAME .' '.$ObjCountry2[0]->NAME;
                    }
                }

            }
                
                $log_data = [ 
                    $id
                ];
                
                //$objPBMAT = DB::select('EXEC SP_GET_PB_MATERIAL ?', $log_data);
                

                $objPBMAT = DB::select("SELECT 
                T1.*,
                T2.ICODE,T2.NAME AS ITEM_NAME,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE
                FROM TBL_TRN_IPO_MAT T1
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T1.ALT_UOMID_REF=T4.UOMID
                WHERE T1.IPO_ID_REF='$id' ORDER BY T1.IPO_ID_REF ASC
                ");                    
                                

                $objCount1 = count($objPBMAT);  
                
                $objPBUDF = DB::table('TBL_TRN_IPO_UDF')                    
                                ->where('TBL_TRN_IPO_UDF.IPO_ID_REF','=',$id)
                                ->get()
                                ->toArray();
                
                $objCount2 = count($objPBUDF);

                $objPBTNC = DB::table('TBL_TRN_IPO_TNC')                    
                                ->where('TBL_TRN_IPO_TNC.IPO_ID_REF','=',$id)
                                ->get()->toArray();
                
                $objCount3 = count($objPBTNC);

                $objPBCAL = DB::table('TBL_TRN_IPO_CAL')                    
                                ->where('TBL_TRN_IPO_CAL.IPO_ID_REF','=',$id)
                                ->get()->toArray();
                
                $objCount4 = count($objPBCAL);
                $objPBPSLB = DB::table('TBL_TRN_IPO_PSLB')                    
                                ->where('TBL_TRN_IPO_PSLB.IPO_ID_REF','=',$id)
                                ->get()->toArray();
                
                $objCount5 = count($objPBPSLB);
 
                $objPBTDS = DB::select('EXEC SP_GET_IPO_TDS ?', $log_data);
                
                $objCount6 = count($objPBTDS);
        
                $objRights = DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_USERROLMAP.BRID_REF','=',Auth::user()->BRID_REF)
                                ->where('TBL_MST_USERROLMAP.FYID_REF','=',Auth::user()->FYID_REF)
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();

                

               // $objvendor          =   $this->getvendor();
        
                $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
                    WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                    order by TNC_CODE ASC', [$CYID_REF, $BRID_REF, $FYID_REF, 'A' ]);
        
                $objCalculationHeader	=   Helper::getCalculationHeader(array(
                                            'CYID_REF'=>Auth::user()->CYID_REF,
                                            'BRID_REF'=>Session::get('BRID_REF'),
                                            'USERID'=>Auth::user()->USERID,
                                            'HEADING'=>'Transactions',
                                            'VTID_REF'=>$this->vtid_ref,
                                            'FORMID'=>$this->form_id
                                            ));

                $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
                WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? 
                order by CTCODE ASC', [$CYID_REF, $BRID_REF, $FYID_REF ]);
                
                
                $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_IPO")->select('*')
                            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                        {       
                                        $query->select('UDFIPOID')->from('TBL_MST_UDFFOR_IPO')
                                                        ->where('STATUS','=','A')
                                                        ->where('PARENTID','=',0)
                                                        ->where('DEACTIVATED','=',0)
                                                        ->where('CYID_REF','=',$CYID_REF)
                                                        ->where('BRID_REF','=',$BRID_REF);
                                                                          
                            })->where('DEACTIVATED','=',0)
                            ->where('STATUS','<>','C')                    
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('BRID_REF','=',$BRID_REF);
                                         
                            
        
                $objUdfPBData = DB::table('TBL_MST_UDFFOR_IPO')
                    ->where('STATUS','=','A')
                    ->where('PARENTID','=',0)
                    ->where('DEACTIVATED','=',0)
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF)
                   
                    ->union($ObjUnionUDF)
                    ->get()->toArray();   
                $objCountUDF = count($objUdfPBData);

                $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_IPO")->select('*')
                            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                        {       
                                        $query->select('UDFIPOID')->from('TBL_MST_UDFFOR_IPO')
                                                        ->where('PARENTID','=',0)
                                                        ->where('DEACTIVATED','=',0)
                                                        ->where('CYID_REF','=',$CYID_REF)
                                                        ->where('BRID_REF','=',$BRID_REF);
                                                                       
                            })->where('DEACTIVATED','=',0)              
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('BRID_REF','=',$BRID_REF);
                                           
                            
        
                $objUdfPBData2 = DB::table('TBL_MST_UDFFOR_IPO')
                    ->where('PARENTID','=',0)
                    ->where('DEACTIVATED','=',0)
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF)
                   
                    ->union($ObjUnionUDF2)
                    ->get()->toArray(); 
            
                $FormId     =   $this->form_id;

                $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
                ->get() ->toArray(); 

                $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')
                ->get() ->toArray(); 

                $objlastdt          =   $this->getLastdt();

                $AlpsStatus =   $this->AlpsStatus();
                $ActionStatus   =   "disabled";

                $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
                $required_status    =   $this->required_status();
                $objothcurrency = $this->GetCurrencyMaster(); 
            
            return view($this->view.$FormId.'view',compact(['AlpsStatus','FormId','objRights','objHDR','objPBVID','TAXSTATE','objShpAddress',
                'objBillAddress','objPBMAT','objPBUDF','objPBTNC','objPBCAL','objPBPSLB','objPBTDS','objCount1','objCount2','objCount3',
                'objCount4','objCount5','objCount6','objUdfPBData','objCountUDF','objTNCHeader',
                'objCalculationHeader','objTNCDetails','objCalDetails','objlastdt','objCalHeader','objUdfPBData2',
                'objothcurrency','objImportDuty','objsubglcode','ActionStatus',
                'TabSetting','required_status'
                ]));      

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

        if(!empty($sp_listing_result)){
            foreach ($sp_listing_result as $key=>$valueitem){  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$valueitem->LAVELS;
            }
        }
   
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];
        $r_count5 = $request['Row_Count5'];
        $r_count6 = $request['Row_Count6'];

        $GROSS_TOTAL    =   0; 
        $NET_TOTAL 		= 	$request['tot_amt'];
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
                    'ITEMID_REF'                => $request['ITEMID_REF_'.$i],
                    'MAIN_UOMID_REF'                 => $request['MAIN_UOMID_REF_'.$i],
                    'IPO_MAIN_QTY'              => (!is_null($request['SO_QTY_'.$i]) ? $request['SO_QTY_'.$i] : 0),
                    'ALT_UOMID_REF'             => $request['ALT_UOMID_REF_'.$i],
                    'ITEM_SPECI'                  => (!is_null($request['Itemspec_'.$i]) ? $request['Itemspec_'.$i] : 0),
                    'RATE_ASP_MU'            => $request['RATEPUOM_'.$i],
                    'DISC_PER'             => (!is_null($request['DISC_PER_'.$i]) ? $request['DISC_PER_'.$i] : 0),
                    'DISC_AMT'                  => (!is_null($request['DISC_AMT_'.$i]) ? $request['DISC_AMT_'.$i] : 0),
                    'FREIGHT_AMT'                  => (!is_null($request['FREIGHT_AMT_'.$i]) ? $request['FREIGHT_AMT_'.$i] : 0),
                    'INSURANCE_AMT'                      => (!is_null($request['INSURANCE_AMT_'.$i]) ? $request['INSURANCE_AMT_'.$i] : 0),
                    'ASSESSABLE_VALUE'                      => (!is_null($request['ASSESSABLE_VALUE_'.$i]) ? $request['ASSESSABLE_VALUE_'.$i] : 0),
                    'CUSTOME_DUTY_RATE'                      => (!is_null($request['CUSTOME_DUTY_RATE_PER_'.$i]) ? $request['CUSTOME_DUTY_RATE_PER_'.$i] : 0),
                    'SWS_RATE'                 => (isset($request['SWS_RATE_PER_'.$i]) ? $request['SWS_RATE_PER_'.$i] : ''),
                    'IGST'                  => (isset($request['IGST_RATE_PER_'.$i]) ? $request['IGST_RATE_PER_'.$i] : ''),
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
                if(isset($request['CTID_REF']) && !is_null($request['CTID_REF']))
                {
                    if(isset($request['TID_REF_'.$i]))
                    {
                        if(isset($request['CT_TYPE_'.$i]) && $request['CT_TYPE_'.$i]=="DISCOUNT"){
                          
                            $DISCOUNT      += $request['VALUE_'.$i]; 
                        }else{
                            $OTHER_CHARGES += $request['VALUE_'.$i];   
                        }

                        $reqdata3[$i] = [
                            'CTID_REF'      => $request['CTID_REF'] ,
                            'TID_REF'       => $request['TID_REF_'.$i],
                            'RATE'          => $request['RATE_'.$i],
                            'VALUE'         => $request['VALUE_'.$i],
                            //'GST'           => (isset($request['calGST_'.$i])!="true" ? 0 : 1) ,
                            'IGST'          => (isset($request['calIGST_'.$i]) && !empty($request['calIGST_'.$i]) ? $request['calIGST_'.$i] : 0),
                            //'CGST'          => (isset($request['calCGST_'.$i]) && !empty($request['calCGST_'.$i]) ? $request['calCGST_'.$i] : 0),
                            //'SGST'          => (isset($request['calSGST_'.$i]) && !empty($request['calSGST_'.$i]) ? $request['calSGST_'.$i] : 0),
                            //'ACTUAL'        => (isset($request['calACTUAL_'.$i])!="true" ? 0 : 1) ,
                        ];
                    }
                }
            
        }
        if(isset($reqdata3))
        { 
            $wrapped_links3["CAL"] = $reqdata3; 
            $XMLCAL = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $XMLCAL = NULL; 
        }


        for ($i=0; $i<=$r_count4; $i++)
        {
            if(isset($request['TDSID_REF_'.$i]))
            {
                if(isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0==1){
                    $TDS_AMOUNT      += $request['TDS_AMT_'.$i]; 
                    }
					
                $reqdata4[$i] = [
                    'TDSID_REF'                 => $request['TDSID_REF_'.$i],
                    'ASSESSABLE_VL_TDS'         => (!is_null($request['ASSESSABLE_VL_TDS_'.$i]) ? $request['ASSESSABLE_VL_TDS_'.$i] : 0),
                    'TDS_RATE'                  => (!is_null($request['TDS_RATE_'.$i]) ? $request['TDS_RATE_'.$i] : 0),
                    'ASSESSABLE_VL_SURCHAPGE'   => (!is_null($request['ASSESSABLE_VL_SURCHARGE_'.$i]) ? $request['ASSESSABLE_VL_SURCHARGE_'.$i] : 0),
                    'SURCHAPGE_RATE'            => (!is_null($request['SURCHARGE_RATE_'.$i]) ? $request['SURCHARGE_RATE_'.$i] : 0),
                    'ASSESSABLE_VL_CESS'        => (!is_null($request['ASSESSABLE_VL_CESS_'.$i]) ? $request['ASSESSABLE_VL_CESS_'.$i] : 0),
                    'CESS_RATE'                 => (!is_null($request['CESS_RATE_'.$i]) ? $request['CESS_RATE_'.$i] : 0),
                    'ASSESSABLE_VL_SPCESS'      => (!is_null($request['ASSESSABLE_VL_SPCESS_'.$i]) ? $request['ASSESSABLE_VL_SPCESS_'.$i] : 0),
                    'SPCESS_RATE'               => (!is_null($request['SPCESS_RATE_'.$i]) ? $request['SPCESS_RATE_'.$i] : 0),
                ];
            }
        }

        if(isset($reqdata4)){ 
            $wrapped_links4["TDSD"] = $reqdata4; 

            if(isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'){
                $XMLTDSD = ArrayToXml::convert($wrapped_links4);
            }
            else{
                $XMLTDSD = NULL; 
            }
        }
        else
        {
            $XMLTDSD = NULL; 
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


        for ($i=0; $i<=$r_count6; $i++)
        {
            if(isset($request['UDFPBID_REF_'.$i]) && !is_null($request['UDFPBID_REF_'.$i]))
            {
                $reqdata6[$i] = [
                    'IPO_UDFID'  => $request['UDFPBID_REF_'.$i],
                    'VALUE'         => $request['udfvalue_'.$i],
                ];
            }
        }

        if(isset($reqdata6))
        { 
            $wrapped_links6["UDF"] = $reqdata6; 
            $XMLUDF = ArrayToXml::convert($wrapped_links6);
        }
        else
        {
            $XMLUDF = NULL; 
        }

        

        $VTID_REF       =   $this->vtid_ref;
        $VID            = 0;
        $USERID         = Auth::user()->USERID;   
        $ACTIONNAME     = $Approvallevel;
        $IPADDRESS      = $request->getClientIp();
        $CYID_REF       = Auth::user()->CYID_REF;
        $BRID_REF       = Session::get('BRID_REF');
        $FYID_REF       = Session::get('FYID_REF');

        $IPO_NO             =   $request['IPO_NO'];
        $IPO_DT             =   $request['IPO_DT'];
        $VID_REF            =   $request['VID_REF'];
        $FC                 =   (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF           =   $request['CRID_REF'];
        $CONVFACT           =   $request['CONVFACT'];
        $SALE_ORDER_NO      =   $request['SALE_ORDER_NO'];
        $DOC_TYPE           =   $request['DOC_TYPE'];
        $BILL_TO            =   $request['BILLTO'];
        $SHIP_TO            =   $request['SHIPTO'];
        $COUNTRY_FROM       =   NULL;
        $REMARKS            =   $request['REMARKS'];
        $FOB                =   $request['FOB'];
        $SLID_REF             =   $request['SLID_REF'];
        $REQ_DELIVERY_DATE             =   $request['REQ_DELIVERY_DATE'];
        $IMPORT_DUTYID_REF  =   $request['IMPORT_DUTYID_REF'];
        $REF_NO             =   $request['REF_NO'];
        $CREDIT_DAYS        =   (!is_null($request['Credit_days']) ? $request['Credit_days'] : 0);
        $DUE_DATE           =   (!is_null($request['DUE_DATE']) ? $request['DUE_DATE'] : '');
        $TDS                =   (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);
       
        $log_data = [ 
            $IPO_NO,$IPO_DT,$VID_REF,$FC,$CRID_REF,
            $CONVFACT,$SALE_ORDER_NO,$DOC_TYPE,$BILL_TO,$SHIP_TO,
            $COUNTRY_FROM,$REMARKS,$FOB,$SLID_REF,$REQ_DELIVERY_DATE,
            $IMPORT_DUTYID_REF,$REF_NO,$CREDIT_DAYS,$DUE_DATE,$TDS,
            $CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT,
            $XMLTNC,$XMLUDF,$XMLCAL,$XMLPSLB,$XMLTDSD,
            $USERID,Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
            ,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT
        ];

        
        $sp_result = DB::select('EXEC SP_IPO_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data); 

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $IPO_NO. ' Sucessfully Approved.']);

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
        $TABLE      =   "TBL_TRN_IPO_HDR";
        $FIELD      =   "IPO_ID";
        $ACTIONNAME     = $Approvallevel;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
            
        $log_data = [ 
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_IPO ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
        
        if($sp_result[0]->RESULT=="All records approved"){

        return Response::json(['approve' =>true,'msg' => 'Record successfully Approved.']);

        }elseif($sp_result[0]->RESULT=="NO RECORD FOR APPROVAL"){
        
        return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','importpurchaseorder'=>'norecord']);
        
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
        $TABLE      =   "TBL_TRN_IPO_HDR";
        $FIELD      =   "IPO_ID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

       
        $req_data[0]=[
            'NT'  => 'TBL_TRN_IPO_HDR',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_IPO_MAT',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_IPO_TNC',
        ];
        $req_data[3]=[
            'NT'  => 'TBL_TRN_IPO_TDS',
        ];
        $req_data[4]=[
            'NT'  => 'TBL_TRN_IPO_CAL',
        ];
        $req_data[5]=[
            'NT'  => 'TBL_TRN_IPO_PSLB',
        ];
        $req_data[6]=[
            'NT'  => 'TBL_TRN_IPO_UDF',
        ];

        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $pb_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_IPO  ?,?,?,?, ?,?,?,?, ?,?,?,?', $pb_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_IPO_HDR')->where('IPO_ID','=',$id)->first();

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
        
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/ImportPurchaseOrder";
		
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

        $IPO_NO  =   trim($request['IPO_NO']);
        $objLabel = DB::table('TBL_TRN_IPO_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('IPO_NO','=',$IPO_NO)
        ->select('IPO_NO')->first();

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

        return  DB::select('SELECT MAX(IPO_DT) IPO_DT FROM TBL_TRN_IPO_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
        
    }

   

    public function getDueDate(Request $request){
        $date       =   date("Y-m-d",strtotime($request['date']));
        $days       =   $request['days'];
        $newdate    =   date('Y-m-d',strtotime($date . "+$days days"));

        echo $newdate;

        exit();
    
    }
	
	public function getImportDutyList(){
 
        $objImportDuty  = DB::table('TBL_MST_IMPORT_DUTY')
        ->where('DEACTIVATED','=',NULL)
        ->orWhere('DEACTIVATED','<>',1)
        ->where('STATUS','=','A')
        ->select('IMPORT_DUTYID','IMPORT_DUTY_CODE')
        ->get();
        
        return $objImportDuty;

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
        $id         = $request['id'];
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


    public function import(){

        $FormId     =   $this->form_id;

        $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
        ->where('VTID','=',$this->vtid_ref)
        ->select('VTID','VCODE','DESCRIPTIONS','INDATE')
        ->get()
        ->toArray();

        return view($this->view.$FormId.'import',compact(['FormId','objMstVoucherType',]));       
    }
    
    public function downloadExcelFormate(){

        $excelfile_path =   "docs/importsamplefiles/ImportPurchaseOrder/import_purchase_order.xlsx";   
        $custfilename   =   str_replace('\\', '/', public_path($excelfile_path));
       
        $reader         =   \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet    =   $reader->load($custfilename);
        
        $writer         =   new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="import_purchase_order.xlsx"');
        ob_end_clean();
        $writer->save("php://output");
        return redirect()->back();
    }


    public function importexcelindb(Request $request){

        ini_set('memory_limit', '-1');

        $FormId             =   $this->form_id;

        $formData           =   $request->all();
        $allow_extnesions   =   explode(",",$formData["allow_extensions"]);
        $allow_size         =   (int)$formData["allow_max_size"] * 1024 * 1024;

    
        $VTID_REF   =   $this->vtid_ref;
        $VID        =   0;
        $USERID     =   Auth::user()->USERID;   
        $ACTIONNAME =   'ADD';
        $IPADDRESS  =   $request->getClientIp();
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        if(isset($formData["FILENAME"])){

            $uploadedFile = $formData["FILENAME"]; 


            
            if($uploadedFile->isValid()){

                $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
                $filesize               =   $uploadedFile ->getSize();  
                $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );
                $inputFileType          =   ucfirst($extension); //as per API Xls or Xlsx: first charter in upper case

                $filenametostore        =   $VTID_REF.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".Date('YmdHis')."_".$filenamewithextension;
                $file_name              =   pathinfo($filenamewithextension, PATHINFO_FILENAME); // fetch only file name
                $logfile_name           =   "LOG_".$VTID_REF.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".Date('YmdHis')."_".$file_name.".txt";

                $excelfile_path         =   "docs/company".$CYID_REF."/ImportPurchaseOrder/importexcel";     
                $destinationPath        =   str_replace('\\', '/', public_path($excelfile_path));

                if ( !is_dir($destinationPath) ) {
                    mkdir($destinationPath, 0777, true);
                }


                if(in_array($extension,$allow_extnesions)){
                    
                    if($filesize < $allow_size){

                        $custfilename = $destinationPath."/".$filenametostore;

                        if ( !is_dir($destinationPath) ) {
                            mkdir($destinationPath, 0777, true);
                        }                                    
                       
                        $uploadedFile->move($destinationPath, $filenametostore); //upload file in dir if not exists

                        if (file_exists($custfilename)) {

                            try {

                                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                                $reader->setReadDataOnly(true);
                                $spreadsheet = $reader->load($custfilename);
                                $worksheet = $spreadsheet->getActiveSheet();
                            
                                $excelHeaderdata    =  [];
                                $excelAlldata       =  [];

                                foreach ($worksheet->getRowIterator() as $rowindex=>$row) {
                            
                                    $cellIterator = $row->getCellIterator();
                                
                                    $cellIterator->setIterateOnlyExistingCells(false);

                                    foreach ($cellIterator as $index=>$cell) {
                                        if($rowindex==1){
                                            $excelHeaderdata[$index] = trim(strtolower($cell->getValue()) ); // fetch value for making header data
                                        }else{
                                            $excelAlldata[$rowindex-1][str_replace(' ', '', $excelHeaderdata[$index]) ]= trim($cell->getValue() );
                                        }
                                    }                        
                                }
                            }
                            catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                                
                                return redirect()->route("transaction",[$FormId,"import"])->with("error","Error loading file: ".$e->getMessage());

                            }

                        }
                        else{
                            return redirect()->route("transaction",[$FormId,"import"])->with("error","There is some file uploading error. Please try again.");
                        }
                         
                    }else{
                        return redirect()->route("transaction",[$FormId,"import"])->with("error","Invalid size - Please check."); //invalid size
                    } 
                    
                }else{

                    return redirect()->route("transaction",[$FormId,"import"])->with("error","Invalid file extension - Please check."); // invalid extension                      
                }
            
            }else{
                    
                return redirect()->route("transaction",[$FormId,"import"])->with("error","Invalid file - Please check."); //invalid 
            }

        }else{
            return redirect()->route("transaction",[$FormId,"import"])->with("error","File not found. - Please check.");  
        }

        $logfile_path = $excelfile_path."/".$logfile_name;     
        
        
        if(!$logfile = fopen($logfile_path, "a") ){

            return redirect()->route("transaction",[$FormId,"import"])->with("error","Log creating file error."); //create or open log file
        }

        $validationErr  =   false;
        $headerArr      =   []; 

        $exit_array     =   array();


       

        foreach($excelAlldata as $eIndex=>$eRowData){

            $purchasing_document                =   trim($eRowData["purchasingdocument"]);
            $order_input_date                   =   trim($eRowData["orderinputdate(header)"]);
            $payer                              =   trim($eRowData["payer"]);
            $document_currency                  =   trim($eRowData["documentcurrency"]);
            $sales_document                     =   trim($eRowData["salesdocument"]);
            $sales_document_type                =   trim($eRowData["salesdocumenttype"]);
            $sold_to_party                      =   trim($eRowData["sold-toparty"]);
            $requested_delivery_date            =   trim($eRowData["requesteddeliv.date"]);
            $created_by                         =   trim($eRowData["createdby"]);

            $material                           =   trim($eRowData["material"]);
            $customermaterialnumber             =   trim($eRowData["customermaterialnumber"]);
            $order_total_quantity               =   trim($eRowData["ordertotalquantity"]);
            $sales_unit                         =   trim($eRowData["salesunit"]);
            $sales_price_unit_price             =   trim($eRowData["salespriceunitprice"]);


            $exist_data =   $purchasing_document.'###'.$payer.'###'.$sales_document.'###'.$sold_to_party.'###'.$material.'###'.$customermaterialnumber;

            if($purchasing_document ==""){
                $this->appendLogData($logfile,"Invalid: Blank purchasing cocument. check row no ".$eIndex);
                $validationErr=true;
            }

            if(!empty($this->exist_doc_no($purchasing_document))){  
                $this->appendLogData($logfile,"Invalid: Already exist purchasing document in database. check row no ".$eIndex);
                $validationErr=true;
            }

            if($order_input_date ==""){
                $this->appendLogData($logfile,"Invalid: Blank order input date. check row no ".$eIndex);
                $validationErr=true;
            }

            if($payer ==""){
                $this->appendLogData($logfile,"Invalid: Blank payer. check row no ".$eIndex);
                $validationErr=true;
            }

            if($sales_document ==""){
                $this->appendLogData($logfile,"Invalid: Blank sales document. check row no ".$eIndex);
                $validationErr=true;
            }

            if($sales_document_type ==""){
                $this->appendLogData($logfile,"Invalid: Blank sales document type. check row no ".$eIndex);
                $validationErr=true;
            }

            if($sold_to_party ==""){
                $this->appendLogData($logfile,"Invalid: Blank sold to party. check row no ".$eIndex);
                $validationErr=true;
            }

            if($requested_delivery_date ==""){
                $this->appendLogData($logfile,"Invalid: Blank requested delivery date. check row no ".$eIndex);
                $validationErr=true;
            }

            if($created_by ==""){
                $this->appendLogData($logfile,"Invalid: Blank created_by. check row no ".$eIndex);
                $validationErr=true;
            }

            if($material ==""){
                $this->appendLogData($logfile,"Invalid: Blank material. check row no ".$eIndex);
                $validationErr=true;
            }

            if($customermaterialnumber ==""){
                $this->appendLogData($logfile,"Invalid: Blank Customer material number. check row no ".$eIndex);
                $validationErr=true;
            }


            if($order_total_quantity ==""){
                $this->appendLogData($logfile,"Invalid: Blank order total quantity. check row no ".$eIndex);
                $validationErr=true;
            }

            if(!is_numeric($order_total_quantity)){
                $this->appendLogData($logfile,"Invalid: Allow only number in order total quantity. check row no ".$eIndex);
                $validationErr=true;
            }

            if($sales_unit ==""){
                $this->appendLogData($logfile,"Invalid: Blank sales unit. check row no ".$eIndex);
                $validationErr=true;
            }

            if($sales_price_unit_price ==""){
                $this->appendLogData($logfile,"Invalid: Blank sales price unit price. check row no ".$eIndex);
                $validationErr=true;
            }

            if(!is_numeric($sales_price_unit_price)){
                $this->appendLogData($logfile,"Invalid: Allow only number sales price unit price. check row no ".$eIndex);
                $validationErr=true;
            }

            if(is_null($this->get_vendor_id($payer))){
                $this->appendLogData($logfile,"Invalid: Payer is not exist in database. check row no ".$eIndex);
                $validationErr=true;
            }

            if(is_null($this->get_customer_id($sold_to_party))){
                $this->appendLogData($logfile,"Invalid: Sold to party is not exist in database. check row no ".$eIndex);
                $validationErr=true;
            }

            if(is_null($this->get_user_id($created_by))){
                $this->appendLogData($logfile,"Invalid: Created by is not exist in database. check row no ".$eIndex);
                $validationErr=true;
            }

            if(is_null($this->get_item($material,$customermaterialnumber)['ITEMID_REF'])){
                $this->appendLogData($logfile,"Invalid: Material/Customer material number is not exist in database. check row no ".$eIndex);
                $validationErr=true;
            }

            if(is_null($this->get_main_uom($sales_unit))){
                $this->appendLogData($logfile,"Invalid: Sales Unit is not exist in database. check row no ".$eIndex);
                $validationErr=true;
            }

        
            if($validationErr ==false){

                $hkey = trim($eRowData["purchasingdocument"]);
                
                if($hkey!=""){

                    if(!array_key_exists($hkey, $headerArr)) {

                        $headerArr[$eRowData["purchasingdocument"]]["header"]["IPO_NO"]                         =   $purchasing_document;
                        $headerArr[$eRowData["purchasingdocument"]]["header"]["IPO_DATE"]                       =   $order_input_date;
                        $headerArr[$eRowData["purchasingdocument"]]["header"]["VENDOR"]                         =   $payer;
                        $headerArr[$eRowData["purchasingdocument"]]["header"]["CURRENCY"]                       =   $document_currency;
                        $headerArr[$eRowData["purchasingdocument"]]["header"]["SALE_ORDER_NO"]                  =   $sales_document;
                        $headerArr[$eRowData["purchasingdocument"]]["header"]["DOCUMENT_TYPE"]                  =   $sales_document_type;
                        $headerArr[$eRowData["purchasingdocument"]]["header"]["CUSTOMER"]                       =   $sold_to_party;
                        $headerArr[$eRowData["purchasingdocument"]]["header"]["REQ_DELIVERY_DATE"]              =   $requested_delivery_date;
                        $headerArr[$eRowData["purchasingdocument"]]["header"]["EMP_CODE"]                       =   $created_by;
                        
                        $headerArr[$eRowData["purchasingdocument"]]["material"][$eIndex]["ALPS_PART_NO"]        =   $material;
                        $headerArr[$eRowData["purchasingdocument"]]["material"][$eIndex]["CUSTOMER_PART_NO"]    =   $customermaterialnumber;
                        $headerArr[$eRowData["purchasingdocument"]]["material"][$eIndex]["MAIN_UOM"]            =   $sales_unit;
                        $headerArr[$eRowData["purchasingdocument"]]["material"][$eIndex]["IPO_QTY"]             =   $order_total_quantity;
                        $headerArr[$eRowData["purchasingdocument"]]["material"][$eIndex]["RATEPERUOM"]          =   $sales_price_unit_price;

                        
                       
                    }
                    else{
                            
                        $dif_result=array_diff($headerArr[$eRowData["purchasingdocument"]]["header"], $eRowData);
                        if(!empty($dif_result)){
                            foreach($dif_result as $dfkey=>$dfval){
                                
                                $this->appendLogData($logfile,"Column Name=".strtoupper($dfkey)." value is different. Data must be same for same purchasing document (".$hkey.")");
                                $validationErr=true;

                            }

                            break 1;
                        }
                        else{

                            if(!in_array($exist_data, $exit_array)){
 
                                $headerArr[$eRowData["purchasingdocument"]]["material"][$eIndex]["ALPS_PART_NO"]        =   $material;
                                $headerArr[$eRowData["purchasingdocument"]]["material"][$eIndex]["CUSTOMER_PART_NO"]    =   $customermaterialnumber;
                                $headerArr[$eRowData["purchasingdocument"]]["material"][$eIndex]["MAIN_UOM"]            =   $sales_unit;
                                $headerArr[$eRowData["purchasingdocument"]]["material"][$eIndex]["IPO_QTY"]             =   $order_total_quantity;
                                $headerArr[$eRowData["purchasingdocument"]]["material"][$eIndex]["RATEPERUOM"]          =   $sales_price_unit_price;
                                
                            }

                        }
                                     
                    }

                }
                else{
                    echo "<br>Invalid Row or Blank Purchasing Document in Row no ".$eIndex++;
                    $this->appendLogData($logfile,"Invalid Row or Blank Purchasing Document in Row no",$eIndex++);
                    $validationErr=true;
                    break 1;
                }
            
            }

            $exit_array[]=$exist_data;
                   
        }

        if($validationErr){
            fclose($logfile);
            
            return redirect()->route("transaction",[$FormId,"import"])->with("logerror",$logfile_path);  
        }

       

        foreach($headerArr as $hIndex=>$hRowData){

            
            $IPO_NO                     =   $hRowData["header"]["IPO_NO"];
            $IPO_DT                     =   $this->changeDateFormate($hRowData["header"]["IPO_DATE"]);
            $VID_REF                    =   $this->get_vendor_id($hRowData["header"]["VENDOR"]);
            $FC                         =   0;
            $CRID_REF                   =   NULL;
            $CONVFACT                   =   NULL;
            $SALE_ORDER_NO              =   $hRowData["header"]["SALE_ORDER_NO"];
            $DOC_TYPE                   =   $hRowData["header"]["DOCUMENT_TYPE"];
            $BILL_TO                    =   $this->get_bill_id($VID_REF);
            $SHIP_TO                    =   $this->get_ship_id($VID_REF);
            $FOB                        =   "NO";
            $SLID_REF                   =   $this->get_customer_id($hRowData["header"]["CUSTOMER"]);
            $REQ_DELIVERY_DATE          =   $this->changeDateFormate($hRowData["header"]["REQ_DELIVERY_DATE"]);
            $CREDIT_DAYS                =   $this->get_credit_days($VID_REF);
            $DUE_DATE                   =   date('Y-m-d', strtotime($IPO_DT. ' + '.$CREDIT_DAYS.' days'));
            $USERID                     =   $this->get_user_id($hRowData["header"]["EMP_CODE"]);

            $hdr_data   =   array(
                'IPO_NO'    =>$IPO_NO,
                'IPO_DT'    =>$IPO_DT,
                'VID_REF'   =>$VID_REF,
                'FC'        =>$FC,
                'CRID_REF'  =>$CRID_REF,
                'CONV_FACTOR'=>$CONVFACT,
                'SALE_ORDER_NO'=>$SALE_ORDER_NO,
                'DOC_TYPE'  =>$DOC_TYPE,
                'BILL_TO'=>$BILL_TO,
                'SHIP_TO'=>$SHIP_TO,
                'FOB'=>$FOB,
                'SLID_REF'=>$SLID_REF,
                'REQ_DELIVERY_DATE'=>$REQ_DELIVERY_DATE,
                'CREDIT_DAYS'=>$CREDIT_DAYS,
                'DUE_DATE'=>$DUE_DATE,
                'CYID_REF'=>$CYID_REF,
                'BRID_REF'=>$BRID_REF,
                'FYID_REF'=>$FYID_REF,
                'VTID_REF'=>$VTID_REF,
            );

            
            $sp_result  =   DB::table('TBL_TRN_IPO_HDR')->insert($hdr_data);

            if($sp_result){

                $IPO_ID_REF =   DB::getPdo()->lastInsertId();

                $audit_trail_data   =   array(
                    'VTID_REF'      =>  $VTID_REF,
                    'VID'           =>  $IPO_ID_REF,
                    'USERID'        =>  $USERID,
                    'ACTIONNAME'    =>  $ACTIONNAME,
                    'DATE'          =>  Date('Y-m-d'),
                    'TIME'          =>  Date('h:i:s'),
                    'IPADDRESS'     =>  $IPADDRESS,
                    'CYID_REF'      =>  $CYID_REF,
                    'BRID_REF'      =>  $BRID_REF,
                    'FYID_REF'      =>  $FYID_REF,  
                );

                DB::table('TBL_TRN_AUDITTRAIL')->insert($audit_trail_data);


                foreach($hRowData["material"] as $pindex=>$prow){
   
                    $ITEMID_REF         =   $this->get_item($prow["ALPS_PART_NO"],$prow["CUSTOMER_PART_NO"])['ITEMID_REF'];
                    $MAIN_UOMID_REF     =   $this->get_main_uom($prow["MAIN_UOM"]);
                    $IPO_MAIN_QTY       =   $prow["IPO_QTY"];
                    $ALT_UOMID_REF      =   $this->get_item($prow["ALPS_PART_NO"],$prow["CUSTOMER_PART_NO"])['ALT_UOMID_REF'];
                    $ITEM_SPECI         =   $this->get_item($prow["ALPS_PART_NO"],$prow["CUSTOMER_PART_NO"])['ITEM_SPECI'];
                    $RATE_ASP_MU        =   $prow["RATEPERUOM"];
                    $DISC_PER           =   0;
                    $DISC_AMT           =   0;
                    $FREIGHT_AMT        =   0;
                    $INSURANCE_AMT      =   0;
                    $ASSESSABLE_VALUE   =   ($RATE_ASP_MU*$IPO_MAIN_QTY);
                    $CUSTOME_DUTY_RATE  =   0;
                    $SWS_RATE           =   0;
                    $IGST               =   0;
    
                    $mat_data= [
                        'IPO_ID_REF'        =>  $IPO_ID_REF,
                        'ITEMID_REF'        =>  $ITEMID_REF,
                        'MAIN_UOMID_REF'    =>  $MAIN_UOMID_REF,
                        'IPO_MAIN_QTY'      =>  $IPO_MAIN_QTY,
                        'PENDING_QTY'       =>  $IPO_MAIN_QTY,
                        'ALT_UOMID_REF'     =>  $ALT_UOMID_REF,
                        'ITEM_SPECI'        =>  $ITEM_SPECI,
                        'RATE_ASP_MU'       =>  $RATE_ASP_MU,
                        'DISC_PER'          =>  $DISC_PER,
                        'DISC_AMT'          =>  $DISC_AMT,
                        'FREIGHT_AMT'       =>  $FREIGHT_AMT,
                        'INSURANCE_AMT'     =>  $INSURANCE_AMT,
                        'ASSESSABLE_VALUE'  =>  $ASSESSABLE_VALUE,
                        'CUSTOME_DUTY_RATE' =>  $CUSTOME_DUTY_RATE,
                        'SWS_RATE'          =>  $SWS_RATE,
                        'IGST'              =>  $IGST,
                    ];
                    
                    DB::table('TBL_TRN_IPO_MAT')->insert($mat_data);
                    
                }

                $ACTION_data =   array( 'VTID_REF'      =>   $VTID_REF,                                
                                        'VID'           =>   $IPO_ID_REF,
                                        'ADDUSER_ID'    =>   $USERID,
                                        'ADDUSER_DT'    =>   Date('Y-m-d'),
                                        'ADDUSER_TM'    =>   Date('h:i:s'),
                                        'CYID_REF'      =>   $CYID_REF,
                                        'BRID_REF'      =>   $BRID_REF,
                                        'FYID_REF'      =>   $FYID_REF,  
                                    );
                DB::table('TBL_TRN_ACTION')->insert($ACTION_data);

                $this->appendLogData($logfile," Purchasing Document ".$hIndex.": Record successfully inserted.","",1 ); 

            }
            else{

                $this->appendLogData($logfile," Purchasing Document ".$hIndex.": Record not inserted. ".$sp_result );
                fclose($logfile);
                return redirect()->route("transaction",[$FormId,"import"])->with("logerror",$logfile_path); 

            }
            

        }
             
        fclose($logfile);
        return redirect()->route("transaction",[$FormId,"import"])->with("logsuccess",$logfile_path);

    } 

    public function appendLogData($logfile, $label, $cellval="",$removeError=0){
        if($removeError==0){
            $txtstring = "Error:".$label." ".$cellval."\n"; 
        }else{
            $txtstring = $label." ".$cellval."\n"; 
        }
             
        echo "<br>".$txtstring;
        fwrite($logfile, $txtstring);
    }

    
    public function get_vendor_id($SGLCODE){

        $CYID_REF   =   Auth::user()->CYID_REF;

        $data       =   DB::select("SELECT SGLID 
                        FROM TBL_MST_SUBLEDGER where SGLID in (SELECT SLID_REF 
                        FROM TBL_MST_VENDOR where SAP_VENDOR_CODE = '$SGLCODE') 
                        AND BELONGS_TO = 'VENDOR' AND CYID_REF='$CYID_REF'");

        if(!empty($data)){
                    
            $SGLID  =   $data[0]->SGLID;

        }
        else{

            $SGLID  =   NULL;

        }

        return $SGLID;

    }

    public function get_customer_id($SAP_CUSTOMER_CODE){

        $CYID_REF   =   Auth::user()->CYID_REF;

        $data       =   DB::select("SELECT SGLID 
                        FROM TBL_MST_SUBLEDGER where SGLID in (SELECT SLID_REF 
                        FROM TBL_MST_CUSTOMER where SAP_CUSTOMER_CODE = '$SAP_CUSTOMER_CODE') 
                        AND BELONGS_TO = 'CUSTOMER' AND CYID_REF='$CYID_REF'");

        if(!empty($data)){
            
            $SGLID  =   $data[0]->SGLID;

        }
        else{

            $SGLID  =   NULL;

        }

        return $SGLID;
    }

    public function get_bill_id($SLID_REF){
        
        $Status     =   "A";
        $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
        $id         =   $ObVID->VID;
        
        $ObjCust    =   DB::select('SELECT top 1 VID FROM TBL_MST_VENDOR WHERE STATUS= ? AND VID = ? ', [$Status,$id]);
        $VID        =   $ObjCust[0]->VID;

        $ObjBillTo  =   DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION WHERE DEFAULT_BILLING= ? AND VID_REF = ? ', [1,$VID]);


        if(!empty($ObjBillTo)){

            $BillTo =   $ObjBillTo[0]->LID;

        }
        else{

            $BillTo =   NULL;
        }

        return $BillTo;
    
    }

    public function get_ship_id($SLID_REF){

        $Status     =   "A";
        $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
        $id         =   $ObVID->VID;
        $BRID_REF   =   Session::get('BRID_REF');
            

        $ObjCust    =   DB::select('SELECT top 1 VID FROM TBL_MST_VENDOR WHERE STATUS= ? AND VID = ? ', [$Status,$id]);
        $VID        =   $ObjCust[0]->VID;

        $ObjSHIPTO  =   DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION WHERE DEFAULT_SHIPPING= ? AND VID_REF = ? ', [1,$VID]);
   
        if(!empty($ObjSHIPTO)){
                        
            $SHIPTO =   $ObjSHIPTO[0]->LID;

        }else{

            $SHIPTO =   NULL;
        }

        return $SHIPTO;

    }

    public function get_credit_days($SLID_REF){

        $Status     =   "A";
        $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
        $id         =   $ObVID->VID;

        $ObjData    =   DB::select('SELECT top 1 CREDITDAY FROM TBL_MST_VENDOR WHERE STATUS= ? AND VID = ? ', [$Status,$id]);
    
        if(!empty($ObjData)){

            return $ObjData[0]->CREDITDAY;

        }
        else{
            return '0';
        }

    }

    public function get_item($ALPS_PART_NO,$CUSTOMER_PART_NO){

        $CYID_REF   =   Auth::user()->CYID_REF;
        
        $data       =   DB::select("SELECT ITEMID,ITEM_SPECI,MAIN_UOMID_REF FROM TBL_MST_ITEM 
		where (DEACTIVATED = 0 OR DEACTIVATED IS NULL) AND STATUS = 'A' AND
		ALPS_PART_NO = '$ALPS_PART_NO' AND CUSTOMER_PART_NO='$CUSTOMER_PART_NO' AND CYID_REF='$CYID_REF'");

        $item_array =   array();
        if(!empty($data)){
           
            $item_array=array(
                'ITEMID_REF'    =>$data[0]->ITEMID,
                'ITEM_SPECI'=>$data[0]->ITEM_SPECI,
                'ALT_UOMID_REF'=>$data[0]->MAIN_UOMID_REF,
            );

        }
        else{

            $item_array=array(
                'ITEMID_REF'    =>NULL,
                'ITEM_SPECI'=>NULL,
                'ALT_UOMID_REF'=>NULL,
            );
        }

        return $item_array;

    }

    public function get_main_uom($UOMCODE){

        $CYID_REF   =   Auth::user()->CYID_REF;
        
        $data       =   DB::select("SELECT UOMID FROM TBL_MST_UOM WHERE CYID_REF='$CYID_REF' AND STATUS='A' AND UOMCODE='$UOMCODE'");

        if(!empty($data)){

            $MAIN_UOM   =   $data[0]->UOMID;
        }
        else{

            $MAIN_UOM   =   NULL;
        }

        return $MAIN_UOM;

    }

    public function get_user_id($EMPCODE){
        
        $data       =   DB::select("SELECT USERID_REF from TBL_MST_USERDETAILS where EMPID_REF in (select EMPID from TBL_MST_EMPLOYEE where EMPCODE = '$EMPCODE')");
    
        if(!empty($data)){

            $USERID_REF = $data[0]->USERID_REF;
       
        }else{

            $USERID_REF =  NULL;

        }

        return $USERID_REF;
    
    }

    public function exist_doc_no($IPO_NO){

        $data   =   DB::table('TBL_TRN_IPO_HDR')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('BRID_REF','=',Session::get('BRID_REF'))
                    ->where('FYID_REF','=',Session::get('FYID_REF'))
                    ->where('IPO_NO','=',$IPO_NO)
                    ->select('IPO_NO')->first();

        return $data;
    }


    
    public function changeDateFormate($date){

        $Date_Val   =   \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date);
        $Date_Data  =   json_decode(json_encode($Date_Val), true);
        $newDate    =   date("Y-m-d",strtotime($Date_Data['date']));

        return $newDate;
    }

    public function required_status(){

        $result   = DB::table('TBL_MST_ADDL_TAB_SETTING')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('TABLE_NAME','=','IPO_REQUIRED')
                    ->where('TAB_NAME','=','YES')
                    ->count();

        return $result;
    }

   
}
