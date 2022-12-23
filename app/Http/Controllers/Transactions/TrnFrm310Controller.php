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

class TrnFrm310Controller extends Controller{
    protected $form_id  =   310;
    protected $vtid_ref =   95;
    protected $view     =   "transactions.Purchase.PurchaseReturn.trnfrm";
   
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];

    public function __construct(){
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

        $objDataList	=	DB::select("select hdr.PRRID,hdr.PRR_NO,hdr.PRR_DT,hdr.VCL_NO,hdr.DRIVER_NAME,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.PRRID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_PRRT01_HDR hdr
                            on a.VID = hdr.PRRID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_SUBLEDGER sl ON hdr.VID_REF = sl.SGLID  
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.PRRID DESC ");

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


    public function getTransport(){

        return DB::table('TBL_MST_TRANSPORTER')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('TRANSPORTERID AS ID','TRANSPORTER_CODE AS CODE','TRANSPORTER_NAME AS DESC')
        ->get();
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
                <td class="ROW1"> <input type="checkbox" name="SELECT_SLID_REF[]" id="vendoridcode_'.$index.'"  class="clsvendorid" value="'.$VID.'" ></td>
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

        $FormId         =   $this->form_id;

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $objglcode = DB::table('TBL_MST_GENERALLEDGER')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('STATUS','=',$Status)
        ->where('SUBLEDGER','=','1')
        ->select('TBL_MST_GENERALLEDGER.*')
        ->get()
        ->toArray();                                                                                                                        

        $d_currency = DB::table('TBL_MST_COMPANY')
        ->where('STATUS','=',$Status)
        ->where('CYID','=',Auth::user()->CYID_REF)
        ->select('TBL_MST_COMPANY.CRID_REF')
        ->first();

        $objcurrency=NULL;
        $objothcurrency =[];
        if(isset($d_currency->CRID_REF) && $d_currency->CRID_REF !=""){
            $objcurrency = $d_currency->CRID_REF;

            $objothcurrency = DB::table('TBL_MST_CURRENCY')
            ->where('STATUS','=',$Status)
            ->where('CRID','<>',$objcurrency)
            ->select('TBL_MST_CURRENCY.*')
            ->get()
            ->toArray();
        }

        $objTRASPORTER  =   $this->getTransport();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_PRRT01_HDR',
            'HDR_ID'=>'PRRID',
            'HDR_DOC_NO'=>'PRR_NO',
            'HDR_DOC_DT'=>'PRR_DT'
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

        
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_PRR")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDFPRRID')->from('TBL_MST_UDFFOR_PRR')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                     
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                
                   

        $objUdfSOData = DB::table('TBL_MST_UDFFOR_PRR')
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

       

           

            $AlpsStatus =   $this->AlpsStatus();
            $lastdt=$this->LastApprovedDocDate(); 
            //dd($lastdt);
            
            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
       
    return view($this->view.$FormId.'add',
    compact(['AlpsStatus','FormId','objTRASPORTER','objglcode','objCalculationHeader','objUdfSOData','objcurrency','objTNCHeader','objothcurrency',
    'objCurrencyconverter','objSalesPerson','objSalesQuotationAData','ObjSalesQuotationData','objCountUDF','lastdt','TabSetting','doc_req','docarray']));       
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



    public function getCodeNo(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $VID_REF       =   $request['id'];
        $BILLTO_REF     =   $request['BILLTO_REF'];
        $SHIPTO_REF     =   $request['SHIPTO_REF'];

        $fieldid    = $request['fieldid'];


        $ObjData =  DB::select("SELECT PBID,PB_DOCNO,PB_DOCDT FROM TBL_TRN_PRPB01_HDR 
        WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF' 
        AND VID_REF='$VID_REF' AND BILL_TO='$BILLTO_REF' AND SHIP_TO='$SHIPTO_REF' AND STATUS='A'");


        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){
                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="sqcode_'.$dataRow->PBID .'"  class="clssqid"  class="clsaltuom" value="'.$dataRow->PBID.'" ></td>
                <td class="ROW2">'.$dataRow->PB_DOCNO;
                $row = $row.'<input type="hidden" id="txtsqcode_'.$dataRow->PBID.'" data-desc="'.$dataRow->PB_DOCNO.'"  data-descdate="'.$dataRow->PB_DOCDT.'"
                value="'.$dataRow->PBID.'"/></td><td class="ROW3">'.$dataRow->PB_DOCDT.'</td></tr>';
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
        $PBID_REF = $request['id'];
        $StdCost = 0;

        $AlpsStatus =   $this->AlpsStatus();

        //$taxstate   ="WithinState";
        //$taxstate   ="OutofState";


        $ObjItem =  DB::select("SELECT 
        T1.ITEMID,T1.ICODE,T1.NAME,T1.ITEM_SPECI,T1.ITEMGID_REF,T1.ICID_REF,T1.STDCOST,
        T2.* 
        FROM TBL_MST_ITEM T1
        INNER JOIN TBL_TRN_PRPB01_MAT T2 ON T1.ITEMID=T2.ITEMID_REF
        WHERE T1.CYID_REF = '$CYID_REF' 
        AND ( T1.DEACTIVATED IS NULL OR T1.DEACTIVATED = 0 ) AND T1.STATUS ='$Status' AND T2.PBID_REF='$PBID_REF'");

        
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

                                $StdCost = $dataRow->BILL_RATEPUOM;

                               
                    
                    
                    $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ?  AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $dataRow->BILL_UOMID_REF, 'A' ]);

                    $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ?  AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $dataRow->ALT_UOMID_REF, $Status ]);
                    
                    $ObjAltQTY =  DB::select('SELECT TOP 1  * FROM TBL_MST_ITEM_UOMCONV  
                                WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                                [$dataRow->ITEMID,$dataRow->ALT_UOMID_REF ]);

                    $TOQTY =  isset($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 1;
                    //$FROMQTY =  isset($ObjAltQTY[0]->FROM_QTY)? $ObjAltQTY[0]->FROM_QTY : 0;
                    $FROMQTY  =   isset($dataRow->BILL_QTY)?$dataRow->BILL_QTY:0;

                


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


                        $MRSID_REF      =   $dataRow->MRSID_REF;
                        $PIID_REF       =   $dataRow->PIID_REF;
                        $RFQID_REF      =   $dataRow->RFQID_REF;
                        $VQID_REF       =   $dataRow->VQID_REF;
                        $POID_REF       =   $dataRow->POID_REF;
                        $GEID_REF       =   $dataRow->GEID_REF;
                        $GRN_NO         =   $dataRow->GRN_NO;
                        $IPOID_REF      =   $dataRow->IPOID_REF;
                       
                        $desc6  =   $PBID_REF.'-'.$dataRow->ITEMID.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->GEID_REF.'-'.$dataRow->GRN_NO.'-'.$dataRow->IPOID_REF;

                        
                        $row = '';
                        if($taxstate != "OutofState"){
                           
                        $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                        $row = $row.'<input type="hidden" id="uniquerowid_'.$desc6.'"  data-desc6="'.$desc6.'" data-desc31="'.$MRSID_REF.'" data-desc32="'.$PIID_REF.'" data-desc33="'.$RFQID_REF.'" data-desc34="'.$VQID_REF.'" data-desc35="'.$POID_REF.'" data-desc36="'.$GEID_REF.'" data-desc37="'.$GRN_NO.'" data-desc38="'.$IPOID_REF.'" />';
                        $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'" data-desc1="'.$FROMQTY.'" data-desc2="'.$dataRow->BILL_RATEPUOM.'" data-desc3="'.$dataRow->DISCOUNT.'" data-desc4="'.$dataRow->DISC_AMT.'" data-desc5="'.$PBID_REF.'-'.$dataRow->ITEMID.'"   value="'.$dataRow->ITEMID.'"/></td> <td style="width:10%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'" value="'.$dataRow->NAME.'"/></td>';
                        $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'" value="'.$dataRow->BILL_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td style="width:8%;" id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'" value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                        $row = $row.'<td style="width:8%;" id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->BILL_RATEPUOM.'" value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                        $row = $row.'<td style="width:8%;" id="itax_'.$dataRow->ITEMID.'"><input type="hidden" id="txtitax_'.$dataRow->ITEMID.'" data-desc="'.$Taxid[0].'" value="'.$Taxid[1].'"/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td> 
                        <td style="width:8%;">'.$BusinessUnit.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                        <td style="width:8%;">Authorized</td></tr>
                        ';
                        }
                        else
                        {

                            $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                            $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                            $row = $row.'<input type="hidden" id="uniquerowid_'.$desc6.'"  data-desc6="'.$desc6.'" data-desc31="'.$MRSID_REF.'" data-desc32="'.$PIID_REF.'" data-desc33="'.$RFQID_REF.'" data-desc34="'.$VQID_REF.'" data-desc35="'.$POID_REF.'" data-desc36="'.$GEID_REF.'" data-desc37="'.$GRN_NO.'" data-desc38="'.$IPOID_REF.'" />';
                            $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'" data-desc1="'.$FROMQTY.'" data-desc2="'.$dataRow->BILL_RATEPUOM.'" data-desc3="'.$dataRow->DISCOUNT.'" data-desc4="'.$dataRow->DISC_AMT.'" data-desc5="'.$PBID_REF.'-'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'"/></td> <td style="width:10%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'" value="'.$dataRow->NAME.'"/></td>';
                            $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'" value="'.$dataRow->BILL_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                            $row = $row.'<td style="width:8%;" id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'" value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                            $row = $row.'<td style="width:8%;" id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="1" value="'.$dataRow->BILL_RATEPUOM.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
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

        $AlpsStatus =   $this->AlpsStatus();
        
                
        $ObjItem =  DB::select('SELECT * FROM TBL_MST_ITEM  
                    WHERE CYID_REF = ? 
                    AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                    [$CYID_REF, $Status ]);
        //   dd($ObjItem);
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
                        $row = $row.'<tr >
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
                        value="'.$dataRow->LID.'"/></td><td class="ROW3" id="txtshipadd_'.$dataRow->LID.'" >'.$objAddress.'</td></tr>';
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
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];
       
        for ($i=0; $i<=$r_count1; $i++){

            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i])){

                $StoreArr   =   array();
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];

                if($ITEMROWID !=""){

                    $exp        =   explode(",",$ITEMROWID);

                    foreach($exp as $val){
                        $keyid              =   explode("_",$val);
                        $batchid            =   $keyid[0];

                        $objStore =  DB::table('TBL_TRN_IGRN02_MULTISTORE')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('MULTISTID','=',$batchid)
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
                
                $MRSID_REF  =   intval($Field_Id[2]);
                $PIID_REF   =   intval($Field_Id[3]);
                $RFQID_REF  =   intval($Field_Id[4]);
                $VQID_REF   =   intval($Field_Id[5]);
                $POID_REF   =   intval($Field_Id[6]);
                $GEID_REF   =   intval($Field_Id[7]);
                $GRN_NO     =   intval($Field_Id[8]);
                $IPOID_REF  =   intval($Field_Id[9]);

                $req_data[$i] = [
                    
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'STID'              => $STID_REF,
                    'MAIN_UOMID_REF'    => $request['MAIN_UOMID_REF_'.$i],
                    'RETURN_QTY_MU'     => $request['SO_QTY_'.$i],
                    'ALT_UOMID_REF'     => $request['ALT_UOMID_REF_'.$i],
                    'RETURN_QTY_AU'     => $request['ALT_UOMID_QTY_'.$i],
                    'RATEPUOM_MU'       => $request['RATEPUOM_'.$i],

                    'TAX_IMPACT'        =>  (isset($request['flagtype_'.$i])!="true" ? 0 : 1) ,
                    'IGST_RATE'         => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST_RATE'         => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST_RATE'         => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),

                    'PBID_REF'          => $request['SQA_'.$i] ,
                    'POID_REF'          => $POID_REF,
                    'VQID_REF'          => $VQID_REF,
                    'RFQID_REF'         => $RFQID_REF,
                    'PIID_REF'          => $PIID_REF,
                    'MRSID_REF'         => $MRSID_REF,
                    'GRN_REF'           => $GRN_NO,
                    'GEID_REF'          => $GEID_REF,
                    'IPOID_REF'         => $IPOID_REF,
                    'BATCH_QTY'         => $request['HiddenRowId_'.$i]
                       
                ];
            }
        }

       

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        $reqdata3=array();
        for ($i=0; $i<=$r_count3; $i++){
            if(isset($request['UDFSOID_REF_'.$i]) && !is_null($request['UDFSOID_REF_'.$i])){
                $reqdata3[$i] = [
                    'UDF'       => $request['UDFSOID_REF_'.$i],
                    'COMMENT'   => $request['udfvalue_'.$i],
                ];
            }
        }

        

        if(!empty($reqdata3))
        { 
            $wrapped_links3["UDF1"] = $reqdata3; 
            $XMLUDF = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $XMLUDF = NULL; 
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


                $WhereId    =   $request['exist_'.$i];
                $Field_Id   =   explode("-",$WhereId);
                
                $MRSID_REF  =   intval($Field_Id[2]);
                $PIID_REF   =   intval($Field_Id[3]);
                $RFQID_REF  =   intval($Field_Id[4]);
                $VQID_REF   =   intval($Field_Id[5]);
                $POID_REF   =   intval($Field_Id[6]);
                $GEID_REF   =   intval($Field_Id[7]);
                $GRN_NO     =   intval($Field_Id[8]);
                $IPOID_REF  =   intval($Field_Id[9]);


                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        $objBatch =  DB::table('TBL_TRN_IGRN02_MULTISTORE')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('MULTISTID','=',$key)
                        ->select('STID_REF','MAIN_UOMID_REF AS MAINUOMID_REF','ALT_UOMID_REF AS ALTUOMID_REF')
                        ->first();


                        $STID_REF       =   $objBatch->STID_REF;
                        $MAINUOMID_REF  =   $objBatch->MAINUOMID_REF;
                        $ALTUOMID_REF   =   $objBatch->ALTUOMID_REF;
                        
                        $ObjData        =   $this->getStockQty($GRN_NO,$STID_REF,$ITEMID_REF,$MAINUOMID_REF);
                        $BATCHNO        =   isset($ObjData) && $ObjData->BATCH_CODE !=""?$ObjData->BATCH_CODE:'';
                        $STOCK_INHAND   =   isset($ObjData) && $ObjData->CURRENT_QTY !=""?$ObjData->CURRENT_QTY:'0';
                        $RETURN_QTYA    =   $this->getAltUmQty($ALTUOMID_REF,$ITEMID_REF,$val);

                        $req_data33[$i][] = [
                            'ITEMID_REF'        => $ITEMID_REF,
                            'STID_REF'          => intval($STID_REF),
                            'MAIN_UOMID_REF'    => $MAINUOMID_REF,
                            'STOCK_INHAND'      => $STOCK_INHAND,
                            'RETURN_QTYM'       => $val,
                            'ALT_UOMID_REF'     => $ALTUOMID_REF,
                            'RETURN_QTYA'       => $RETURN_QTYA,
                            'BATCH_NO'          => $BATCHNO,
                            
                            'PBID_REF'      => $request['SQA_'.$i] ,
                            'POID_REF'     => $POID_REF,
                            'VQID_REF'     => $VQID_REF,
                            'RFQID_REF'     => $RFQID_REF,
                            'PIID_REF'     => $PIID_REF,
                            'MRSID_REF'     => $MRSID_REF,
                            'GRN_REF'     => $GRN_NO,
                            'GEID_REF'     => $GEID_REF,
                            'IPOID_REF'     => $IPOID_REF,

                        ];

                    }
                }
            }
        }

      

        $wrapped_links33["MULTISTORE"] = $req_data33; 
        $XMLSTORE = ArrayToXml::convert($wrapped_links33);



        for ($i=0; $i<=$r_count2; $i++){
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
        if(isset($reqdata2)) { 
            $wrapped_links2["TNC"] = $reqdata2;
            $XMLTNC = ArrayToXml::convert($wrapped_links2);
        }
        else {
            $XMLTNC = NULL; 
        }  


        for ($i=0; $i<=$r_count4; $i++){
                if(isset($request['CTID_REF']) && !is_null($request['CTID_REF']))
                {
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
                            'ASPER_ACTUAL'  => (isset($request['calACTUAL_'.$i]) ) ? 1 : 0  
                        ];
                    }
                }
            
        }

        if(isset($reqdata4))
        { 
            $wrapped_links4["CALCULATIONTEMPLATE"] = $reqdata4; 
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

        $PRR_NO             =   $request['PRR_NO'];
        $PRR_DT             =   $request['PRR_DT'];
        $SLID_REF           =   $request['SLID_REF'];        
        $BILLTO             =   $request['BILLTO'];
        $SHIPTO             =   $request['SHIPTO'];
        $VCL_NO             =   $request['VCL_NO'];
        $TRASPORTER_NAME    =   $request['TRASPORTER_NAME'];
        $DRIVER_NAME        =   $request['DRIVER_NAME'];
        $PURPOSE            =   $request['PURPOSE'];
        
        
        $log_data = [ 
            $PRR_NO,$PRR_DT,$SLID_REF,$BILLTO,$SHIPTO,$VCL_NO,$TRASPORTER_NAME,$DRIVER_NAME,$PURPOSE,$CYID_REF, 
            $BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT,$XMLUDF,$XMLSTORE,$XMLTNC,$XMLCAL,$USERID, Date('Y-m-d'), 
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];

        


        $sp_result = DB::select('EXEC SP_PRR_IN ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?', $log_data); 
        
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

            $objTRASPORTER  =   $this->getTransport();

            $objSO = DB::table('TBL_TRN_PRRT01_HDR')
            ->leftJoin('TBL_MST_TRANSPORTER', 'TBL_TRN_PRRT01_HDR.TRASPORTER_NAME','=','TBL_MST_TRANSPORTER.TRANSPORTERID') 
            ->where('TBL_TRN_PRRT01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_PRRT01_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_PRRT01_HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_PRRT01_HDR.PRRID','=',$id)
            ->select(
                'TBL_TRN_PRRT01_HDR.*',
                'TBL_MST_TRANSPORTER.TRANSPORTERID',
                'TBL_MST_TRANSPORTER.TRANSPORTER_CODE',
                'TBL_MST_TRANSPORTER.TRANSPORTER_NAME'
                )
            ->first();


            $objSOMAT = DB::select("SELECT 
            T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,
            CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
            CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
            T5.FROM_QTY,T5.TO_QTY,
            T6.PB_DOCNO,T6.PB_DOCDT
            FROM TBL_TRN_PRRT01_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
            LEFT JOIN TBL_MST_UOM T4 ON T1.ALT_UOMID_REF=T4.UOMID
            LEFT JOIN TBL_MST_ITEM_UOMCONV T5 ON T1.ITEMID_REF=T5.ITEMID_REF AND T1.MAIN_UOMID_REF=T5.FROM_UOMID_REF AND  T1.ALT_UOMID_REF=T5.TO_UOMID_REF
            LEFT JOIN TBL_TRN_PRPB01_HDR T6 ON T1.PBID_REF=T6.PBID

            WHERE T1.PRRID_REF='$id' ORDER BY T1.PRR_MATID ASC
            ");


            $objCount1 = count($objSOMAT);



            $objSOTNC = DB::table('TBL_TRN_PRRT01_TNC')                    
            ->where('TBL_TRN_PRRT01_TNC.PRRID_REF','=',$id)
            ->select('TBL_TRN_PRRT01_TNC.*')
            ->orderBy('TBL_TRN_PRRT01_TNC.PRR_TNCID','ASC')
            ->get()->toArray();
            $objCount2 = count($objSOTNC);


            $objSOUDF = DB::table('TBL_TRN_PRRT01_UDF')                    
            ->where('TBL_TRN_PRRT01_UDF.PRRID_REF','=',$id)
            ->select('TBL_TRN_PRRT01_UDF.*')
            ->orderBy('TBL_TRN_PRRT01_UDF.PRR_UDFID','ASC')
            ->get()->toArray();
            $objCount3 = count($objSOUDF);


            $objSOCAL = DB::table('TBL_TRN_PRRT01_CAL')                    
            ->where('TBL_TRN_PRRT01_CAL.PRRID_REF','=',$id)
            ->select('TBL_TRN_PRRT01_CAL.*')
            ->orderBy('TBL_TRN_PRRT01_CAL.PRR_CALID','ASC')
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


                            
                             if(isset($objSO->SHIPTO) && $objSO->SHIPTO !=""){
                            $sid = $objSO->SHIPTO;
         

                            $SLID_REF   =   $objSO->VID_REF;
                            $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
                            $VID         =   $ObVID->VID;



                            $ObjSHIPTO  =   DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                                            WHERE DEFAULT_SHIPPING= ? AND VID_REF = ? ', [1,$VID]);


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
                     
                             $ObjAddressID = $ObjSHIPTO[0]->LID;
                                     if(!empty($ObjSHIPTO)){
                                        $objShpAddress[] = $ObjSHIPTO[0]->LADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                                     }

                                    }


                                    
                            if(isset($objSO->VID_REF) && $objSO->VID_REF !=""){

                            $SLID_REF   =   $objSO->VID_REF;
                            $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
                            $VID         =   $ObVID->VID;

                            $ObjBILLTO  =   DB::select('SELECT  * FROM TBL_MST_VENDORLOCATION  
                                            WHERE DEFAULT_BILLING= ? AND VID_REF = ? ', [1,$VID]);
                
                            $ObjCity2 =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                                        WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                                        [$Status,$ObjBILLTO[0]->CITYID_REF,$ObjBILLTO[0]->CTRYID_REF,$ObjBILLTO[0]->STID_REF]);
                    
                            $ObjState2 =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                                        WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBILLTO[0]->STID_REF,$ObjBILLTO[0]->CTRYID_REF]);
                    
                            $ObjCountry2 =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                                        WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBILLTO[0]->CTRYID_REF]);
                    
                            $ObjAddressID = $ObjBILLTO[0]->LID;
                                    if(!empty($ObjBILLTO)){
                                    $objBillAddress[] = $ObjBILLTO[0]->LADD .' '.$ObjCity2[0]->NAME .' '.$ObjState2[0]->NAME .' '.$ObjCountry2[0]->NAME;
                                    }

                                }
            
                                 


            $objglcode2=array();                      



            $objEMP=array();
            $objSPID=NULL;

       
            $objglcode=array();


            $objsubglcode =[];
            if(isset($objSO->VID_REF) && $objSO->VID_REF !=""){
            $SLID_REF=$objSO->VID_REF;


                $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
                ->where('BELONGS_TO','=','Vendor')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('SGLID','=',$SLID_REF)    
                ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                ->first();
            }


           

            $objcurrency=array();
            $objothcurrency=array();
            $objsocurrency=array();

           
           

            $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
            WHERE  CYID_REF = ? AND BRID_REF = ?   
            order by CTCODE ASC', [$CYID_REF, $BRID_REF ]);
    
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

            
            
            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_PRR")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('UDFPRRID')->from('TBL_MST_UDFFOR_PRR')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                        
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                        
                    
    
            $objUdfSOData = DB::table('TBL_MST_UDFFOR_PRR')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_PRR")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDFPRRID')->from('TBL_MST_UDFFOR_PRR')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                            
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                            
            $objUdfSOData2 = DB::table('TBL_MST_UDFFOR_PRR')
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
                    
          

            $objSQMAT=$objSOMAT;

           

            $objUOM = DB::table('TBL_MST_UOM')->select('*')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',$CYID_REF)
            ->get() ->toArray(); 

           
            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
            ->get() ->toArray(); 

            $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')
            ->get() ->toArray(); 
        
           
        $FormId         =   $this->form_id;
        $AlpsStatus     =   $this->AlpsStatus();
        $ActionStatus   =   "";
        $lastdt=$this->LastApprovedDocDate(); 

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');


        return view($this->view.$FormId.'edit',compact(['AlpsStatus','FormId','objTRASPORTER','objSO','objRights','objCount1','objSPID',
           'objCount2','objCount3','objCount4','objCount5','objSOMAT','objSOCAL','objSOTNC','objSOUDF','objSOPSLB',
           'objglcode','objCalculationHeader','objUdfSOData','objcurrency','objTNCHeader','objothcurrency','objglcode2',
           'objCurrencyconverter','objSalesPerson','objSalesQuotationAData','ObjSalesQuotationData','objsubglcode',
           'objShpAddress','objBillAddress','objSQMAT','objUOM','objTNCDetails','objUdfSOData2',
           'objCalHeader','objCalDetails','objsocurrency','TAXSTATE','ActionStatus','lastdt','TabSetting']));
        }
     
       }
     
       public function view($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){

            $objTRASPORTER  =   $this->getTransport();

            $objSO = DB::table('TBL_TRN_PRRT01_HDR')
            ->leftJoin('TBL_MST_TRANSPORTER', 'TBL_TRN_PRRT01_HDR.TRASPORTER_NAME','=','TBL_MST_TRANSPORTER.TRANSPORTERID') 
            ->where('TBL_TRN_PRRT01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_PRRT01_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_PRRT01_HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_PRRT01_HDR.PRRID','=',$id)
            ->select(
                'TBL_TRN_PRRT01_HDR.*',
                'TBL_MST_TRANSPORTER.TRANSPORTERID',
                'TBL_MST_TRANSPORTER.TRANSPORTER_CODE',
                'TBL_MST_TRANSPORTER.TRANSPORTER_NAME'
                )
            ->first();


            $objSOMAT = DB::select("SELECT 
            T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,
            CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
            CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
            T5.FROM_QTY,T5.TO_QTY,
            T6.PB_DOCNO,T6.PB_DOCDT
            FROM TBL_TRN_PRRT01_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
            LEFT JOIN TBL_MST_UOM T4 ON T1.ALT_UOMID_REF=T4.UOMID
            LEFT JOIN TBL_MST_ITEM_UOMCONV T5 ON T1.ITEMID_REF=T5.ITEMID_REF AND T1.MAIN_UOMID_REF=T5.FROM_UOMID_REF AND  T1.ALT_UOMID_REF=T5.TO_UOMID_REF
            LEFT JOIN TBL_TRN_PRPB01_HDR T6 ON T1.PBID_REF=T6.PBID

            WHERE T1.PRRID_REF='$id' ORDER BY T1.PRR_MATID ASC
            ");


            $objCount1 = count($objSOMAT);



            $objSOTNC = DB::table('TBL_TRN_PRRT01_TNC')                    
            ->where('TBL_TRN_PRRT01_TNC.PRRID_REF','=',$id)
            ->select('TBL_TRN_PRRT01_TNC.*')
            ->orderBy('TBL_TRN_PRRT01_TNC.PRR_TNCID','ASC')
            ->get()->toArray();
            $objCount2 = count($objSOTNC);


            $objSOUDF = DB::table('TBL_TRN_PRRT01_UDF')                    
            ->where('TBL_TRN_PRRT01_UDF.PRRID_REF','=',$id)
            ->select('TBL_TRN_PRRT01_UDF.*')
            ->orderBy('TBL_TRN_PRRT01_UDF.PRR_UDFID','ASC')
            ->get()->toArray();
            $objCount3 = count($objSOUDF);


            $objSOCAL = DB::table('TBL_TRN_PRRT01_CAL')                    
            ->where('TBL_TRN_PRRT01_CAL.PRRID_REF','=',$id)
            ->select('TBL_TRN_PRRT01_CAL.*')
            ->orderBy('TBL_TRN_PRRT01_CAL.PRR_CALID','ASC')
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


                            
                             if(isset($objSO->SHIPTO) && $objSO->SHIPTO !=""){
                            $sid = $objSO->SHIPTO;
         

                            $SLID_REF   =   $objSO->VID_REF;
                            $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
                            $VID         =   $ObVID->VID;



                            $ObjSHIPTO  =   DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                                            WHERE DEFAULT_SHIPPING= ? AND VID_REF = ? ', [1,$VID]);


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
                     
                             $ObjAddressID = $ObjSHIPTO[0]->LID;
                                     if(!empty($ObjSHIPTO)){
                                        $objShpAddress[] = $ObjSHIPTO[0]->LADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                                     }

                                    }


                                    
                            if(isset($objSO->VID_REF) && $objSO->VID_REF !=""){

                            $SLID_REF   =   $objSO->VID_REF;
                            $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
                            $VID         =   $ObVID->VID;

                            $ObjBILLTO  =   DB::select('SELECT  * FROM TBL_MST_VENDORLOCATION  
                                            WHERE DEFAULT_BILLING= ? AND VID_REF = ? ', [1,$VID]);
                
                            $ObjCity2 =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                                        WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                                        [$Status,$ObjBILLTO[0]->CITYID_REF,$ObjBILLTO[0]->CTRYID_REF,$ObjBILLTO[0]->STID_REF]);
                    
                            $ObjState2 =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                                        WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBILLTO[0]->STID_REF,$ObjBILLTO[0]->CTRYID_REF]);
                    
                            $ObjCountry2 =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                                        WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBILLTO[0]->CTRYID_REF]);
                    
                            $ObjAddressID = $ObjBILLTO[0]->LID;
                                    if(!empty($ObjBILLTO)){
                                    $objBillAddress[] = $ObjBILLTO[0]->LADD .' '.$ObjCity2[0]->NAME .' '.$ObjState2[0]->NAME .' '.$ObjCountry2[0]->NAME;
                                    }

                                }
            
                                 


            $objglcode2=array();                      



            $objEMP=array();
            $objSPID=NULL;

       
            $objglcode=array();


            $objsubglcode =[];
            if(isset($objSO->VID_REF) && $objSO->VID_REF !=""){
            $SLID_REF=$objSO->VID_REF;


                $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
                ->where('BELONGS_TO','=','Vendor')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('SGLID','=',$SLID_REF)    
                ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                ->first();
            }


           

            $objcurrency=array();
            $objothcurrency=array();
            $objsocurrency=array();

           
           

            $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
            WHERE  CYID_REF = ? AND BRID_REF = ?   
            order by CTCODE ASC', [$CYID_REF, $BRID_REF ]);
    
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

            
            
            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_PRR")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('UDFPRRID')->from('TBL_MST_UDFFOR_PRR')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                        
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                        
                    
    
            $objUdfSOData = DB::table('TBL_MST_UDFFOR_PRR')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_PRR")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDFPRRID')->from('TBL_MST_UDFFOR_PRR')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                            
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                            
            $objUdfSOData2 = DB::table('TBL_MST_UDFFOR_PRR')
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
                    
          

            $objSQMAT=$objSOMAT;

           

            $objUOM = DB::table('TBL_MST_UOM')->select('*')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',$CYID_REF)
            ->get() ->toArray(); 

           
            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
            ->get() ->toArray(); 

            $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')
            ->get() ->toArray(); 
        
           
        $FormId         =   $this->form_id;
        $AlpsStatus =   $this->AlpsStatus();
        $ActionStatus   =   "disabled";

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        return view($this->view.$FormId.'view',compact(['AlpsStatus','FormId','objTRASPORTER','objSO','objRights','objCount1','objSPID',
           'objCount2','objCount3','objCount4','objCount5','objSOMAT','objSOCAL','objSOTNC','objSOUDF','objSOPSLB',
           'objglcode','objCalculationHeader','objUdfSOData','objcurrency','objTNCHeader','objothcurrency','objglcode2',
           'objCurrencyconverter','objSalesPerson','objSalesQuotationAData','ObjSalesQuotationData','objsubglcode',
           'objShpAddress','objBillAddress','objSQMAT','objUOM','objTNCDetails','objUdfSOData2',
           'objCalHeader','objCalDetails','objsocurrency','TAXSTATE','ActionStatus','TabSetting']));
        }
     
       }

    
    public function update(Request $request){

        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];
    
        for ($i=0; $i<=$r_count1; $i++){

            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i])){

                $StoreArr   =   array();
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];

                if($ITEMROWID !=""){

                    $exp        =   explode(",",$ITEMROWID);

                    foreach($exp as $val){
                        $keyid              =   explode("_",$val);
                        $batchid            =   $keyid[0];

                        $objStore =  DB::table('TBL_TRN_IGRN02_MULTISTORE')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('MULTISTID','=',$batchid)
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
                
                $MRSID_REF  =   intval($Field_Id[2]);
                $PIID_REF   =   intval($Field_Id[3]);
                $RFQID_REF  =   intval($Field_Id[4]);
                $VQID_REF   =   intval($Field_Id[5]);
                $POID_REF   =   intval($Field_Id[6]);
                $GEID_REF   =   intval($Field_Id[7]);
                $GRN_NO     =   intval($Field_Id[8]);
                $IPOID_REF  =   intval($Field_Id[9]);

                $req_data[$i] = [
                    
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'STID'              => $STID_REF,
                    'MAIN_UOMID_REF'    => $request['MAIN_UOMID_REF_'.$i],
                    'RETURN_QTY_MU'     => $request['SO_QTY_'.$i],
                    'ALT_UOMID_REF'     => $request['ALT_UOMID_REF_'.$i],
                    'RETURN_QTY_AU'     => $request['ALT_UOMID_QTY_'.$i],
                    'RATEPUOM_MU'       => $request['RATEPUOM_'.$i],

                    'TAX_IMPACT'        =>  (isset($request['flagtype_'.$i])!="true" ? 0 : 1) ,
                    'IGST_RATE'         => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST_RATE'         => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST_RATE'         => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),

                    'PBID_REF'          => $request['SQA_'.$i] ,
                    'POID_REF'          => $POID_REF,
                    'VQID_REF'          => $VQID_REF,
                    'RFQID_REF'         => $RFQID_REF,
                    'PIID_REF'          => $PIID_REF,
                    'MRSID_REF'         => $MRSID_REF,
                    'GRN_REF'           => $GRN_NO,
                    'GEID_REF'          => $GEID_REF,
                    'IPOID_REF'         => $IPOID_REF,
                    'BATCH_QTY'         => $request['HiddenRowId_'.$i]
                       
                ];
            }
        }



       

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        $reqdata3=array();
        for ($i=0; $i<=$r_count3; $i++){
            if(isset($request['UDFSOID_REF_'.$i]) && !is_null($request['UDFSOID_REF_'.$i])){
                $reqdata3[$i] = [
                    'UDF'       => $request['UDFSOID_REF_'.$i],
                    'COMMENT'   => $request['udfvalue_'.$i],
                ];
            }
        }

       

        if(!empty($reqdata3))
        { 
            $wrapped_links3["UDF1"] = $reqdata3; 
            $XMLUDF = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $XMLUDF = NULL; 
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


                $WhereId    =   $request['exist_'.$i];
                $Field_Id   =   explode("-",$WhereId);
                
                $MRSID_REF  =   intval($Field_Id[2]);
                $PIID_REF   =   intval($Field_Id[3]);
                $RFQID_REF  =   intval($Field_Id[4]);
                $VQID_REF   =   intval($Field_Id[5]);
                $POID_REF   =   intval($Field_Id[6]);
                $GEID_REF   =   intval($Field_Id[7]);
                $GRN_NO     =   intval($Field_Id[8]);
                $IPOID_REF  =   intval($Field_Id[9]);


                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        $objBatch =  DB::table('TBL_TRN_IGRN02_MULTISTORE')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('MULTISTID','=',$key)
                        ->select('STID_REF','MAIN_UOMID_REF AS MAINUOMID_REF','ALT_UOMID_REF AS ALTUOMID_REF')
                        ->first();


                        $STID_REF       =   $objBatch->STID_REF;
                        $MAINUOMID_REF  =   $objBatch->MAINUOMID_REF;
                        $ALTUOMID_REF   =   $objBatch->ALTUOMID_REF;
                        
                        $ObjData        =   $this->getStockQty($GRN_NO,$STID_REF,$ITEMID_REF,$MAINUOMID_REF);
                        $BATCHNO        =   isset($ObjData) && $ObjData->BATCH_CODE !=""?$ObjData->BATCH_CODE:'';
                        $STOCK_INHAND   =   isset($ObjData) && $ObjData->CURRENT_QTY !=""?$ObjData->CURRENT_QTY:'0';
                        $RETURN_QTYA    =   $this->getAltUmQty($ALTUOMID_REF,$ITEMID_REF,$val);

                        $req_data33[$i][] = [
                            'ITEMID_REF'        => $ITEMID_REF,
                            'STID_REF'          => intval($STID_REF),
                            'MAIN_UOMID_REF'    => $MAINUOMID_REF,
                            'STOCK_INHAND'      => $STOCK_INHAND,
                            'RETURN_QTYM'       => $val,
                            'ALT_UOMID_REF'     => $ALTUOMID_REF,
                            'RETURN_QTYA'       => $RETURN_QTYA,
                            'BATCH_NO'          => $BATCHNO,
                            
                            'PBID_REF'      => $request['SQA_'.$i] ,
                            'POID_REF'     => $POID_REF,
                            'VQID_REF'     => $VQID_REF,
                            'RFQID_REF'     => $RFQID_REF,
                            'PIID_REF'     => $PIID_REF,
                            'MRSID_REF'     => $MRSID_REF,
                            'GRN_REF'     => $GRN_NO,
                            'GEID_REF'     => $GEID_REF,
                            'IPOID_REF'     => $IPOID_REF,

                        ];

                    }
                }
            }
        }

      

        $wrapped_links33["MULTISTORE"] = $req_data33; 
        $XMLSTORE = ArrayToXml::convert($wrapped_links33);


        for ($i=0; $i<=$r_count2; $i++){
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

      

        if(isset($reqdata2)) { 
            $wrapped_links2["TNC"] = $reqdata2;
            $XMLTNC = ArrayToXml::convert($wrapped_links2);
        }
        else {
            $XMLTNC = NULL; 
        }  

        

        for ($i=0; $i<=$r_count4; $i++){
                if(isset($request['CTID_REF']) && !is_null($request['CTID_REF']))
                {
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
                            'ASPER_ACTUAL'  => (isset($request['calACTUAL_'.$i]) ) ? 1 : 0  
                        ];
                    }
                }
            
        }

       

        if(isset($reqdata4))
        { 
            $wrapped_links4["CALCULATIONTEMPLATE"] = $reqdata4; 
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

        $PRR_NO             =   $request['PRR_NO'];
        $PRR_DT             =   $request['PRR_DT'];
        $SLID_REF           =   $request['SLID_REF'];        
        $BILLTO             =   $request['BILLTO'];
        $SHIPTO             =   $request['SHIPTO'];
        $VCL_NO             =   $request['VCL_NO'];
        $TRASPORTER_NAME    =   $request['TRASPORTER_NAME'];
        $DRIVER_NAME        =   $request['DRIVER_NAME'];
        $PURPOSE            =   $request['PURPOSE'];
        
        
        $log_data = [ 
            $PRR_NO,$PRR_DT,$SLID_REF,$BILLTO,$SHIPTO,$VCL_NO,$TRASPORTER_NAME,$DRIVER_NAME,$PURPOSE,$CYID_REF, 
            $BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT,$XMLUDF,$XMLSTORE,$XMLTNC,$XMLCAL,$USERID, Date('Y-m-d'), 
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];

       


        $sp_result = DB::select('EXEC SP_PRR_UP ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?', $log_data); 
      
  
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $PRR_NO. ' Sucessfully Updated.']);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }

        exit();   
    }

    //update the data
   public function Approve(Request $request){

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
    
        for ($i=0; $i<=$r_count1; $i++){

            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i])){

                $StoreArr   =   array();
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];

                if($ITEMROWID !=""){

                    $exp        =   explode(",",$ITEMROWID);

                    foreach($exp as $val){
                        $keyid              =   explode("_",$val);
                        $batchid            =   $keyid[0];

                        $objStore =  DB::table('TBL_TRN_IGRN02_MULTISTORE')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('MULTISTID','=',$batchid)
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
                
                $MRSID_REF  =   intval($Field_Id[2]);
                $PIID_REF   =   intval($Field_Id[3]);
                $RFQID_REF  =   intval($Field_Id[4]);
                $VQID_REF   =   intval($Field_Id[5]);
                $POID_REF   =   intval($Field_Id[6]);
                $GEID_REF   =   intval($Field_Id[7]);
                $GRN_NO     =   intval($Field_Id[8]);
                $IPOID_REF  =   intval($Field_Id[9]);

                $req_data[$i] = [
                    
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'STID'              => $STID_REF,
                    'MAIN_UOMID_REF'    => $request['MAIN_UOMID_REF_'.$i],
                    'RETURN_QTY_MU'     => $request['SO_QTY_'.$i],
                    'ALT_UOMID_REF'     => $request['ALT_UOMID_REF_'.$i],
                    'RETURN_QTY_AU'     => $request['ALT_UOMID_QTY_'.$i],
                    'RATEPUOM_MU'       => $request['RATEPUOM_'.$i],

                    'TAX_IMPACT'        =>  (isset($request['flagtype_'.$i])!="true" ? 0 : 1) ,
                    'IGST_RATE'         => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST_RATE'         => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST_RATE'         => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),

                    'PBID_REF'          => $request['SQA_'.$i] ,
                    'POID_REF'          => $POID_REF,
                    'VQID_REF'          => $VQID_REF,
                    'RFQID_REF'         => $RFQID_REF,
                    'PIID_REF'          => $PIID_REF,
                    'MRSID_REF'         => $MRSID_REF,
                    'GRN_REF'           => $GRN_NO,
                    'GEID_REF'          => $GEID_REF,
                    'IPOID_REF'         => $IPOID_REF,
                    'BATCH_QTY'         => $request['HiddenRowId_'.$i]
                        
                ];
            }
        }



        //dd($req_data);

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        $reqdata3=array();
        for ($i=0; $i<=$r_count3; $i++){
            if(isset($request['UDFSOID_REF_'.$i]) && !is_null($request['UDFSOID_REF_'.$i])){
                $reqdata3[$i] = [
                    'UDF'       => $request['UDFSOID_REF_'.$i],
                    'COMMENT'   => $request['udfvalue_'.$i],
                ];
            }
        }

        //dd($reqdata3);

        if(!empty($reqdata3))
        { 
            $wrapped_links3["UDF1"] = $reqdata3; 
            $XMLUDF = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $XMLUDF = NULL; 
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


                $WhereId    =   $request['exist_'.$i];
                $Field_Id   =   explode("-",$WhereId);
                
                $MRSID_REF  =   intval($Field_Id[2]);
                $PIID_REF   =   intval($Field_Id[3]);
                $RFQID_REF  =   intval($Field_Id[4]);
                $VQID_REF   =   intval($Field_Id[5]);
                $POID_REF   =   intval($Field_Id[6]);
                $GEID_REF   =   intval($Field_Id[7]);
                $GRN_NO     =   intval($Field_Id[8]);
                $IPOID_REF  =   intval($Field_Id[9]);


                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        $objBatch =  DB::table('TBL_TRN_IGRN02_MULTISTORE')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('MULTISTID','=',$key)
                        ->select('STID_REF','MAIN_UOMID_REF AS MAINUOMID_REF','ALT_UOMID_REF AS ALTUOMID_REF')
                        ->first();


                        $STID_REF       =   $objBatch->STID_REF;
                        $MAINUOMID_REF  =   $objBatch->MAINUOMID_REF;
                        $ALTUOMID_REF   =   $objBatch->ALTUOMID_REF;
                        
                        $ObjData        =   $this->getStockQty($GRN_NO,$STID_REF,$ITEMID_REF,$MAINUOMID_REF);
                        $BATCHNO        =   isset($ObjData) && $ObjData->BATCH_CODE !=""?$ObjData->BATCH_CODE:'';
                        $STOCK_INHAND   =   isset($ObjData) && $ObjData->CURRENT_QTY !=""?$ObjData->CURRENT_QTY:'0';
                        $RETURN_QTYA    =   $this->getAltUmQty($ALTUOMID_REF,$ITEMID_REF,$val);

                        $req_data33[$i][] = [
                            'ITEMID_REF'        => $ITEMID_REF,
                            'STID_REF'          => intval($STID_REF),
                            'MAIN_UOMID_REF'    => $MAINUOMID_REF,
                            'STOCK_INHAND'      => $STOCK_INHAND,
                            'RETURN_QTYM'       => $val,
                            'ALT_UOMID_REF'     => $ALTUOMID_REF,
                            'RETURN_QTYA'       => $RETURN_QTYA,
                            'BATCH_NO'          => $BATCHNO,
                            
                            'PBID_REF'      => $request['SQA_'.$i] ,
                            'POID_REF'     => $POID_REF,
                            'VQID_REF'     => $VQID_REF,
                            'RFQID_REF'     => $RFQID_REF,
                            'PIID_REF'     => $PIID_REF,
                            'MRSID_REF'     => $MRSID_REF,
                            'GRN_REF'     => $GRN_NO,
                            'GEID_REF'     => $GEID_REF,
                            'IPOID_REF'     => $IPOID_REF,

                        ];

                    }
                }
            }
        }

        //dd($req_data33);

        $wrapped_links33["MULTISTORE"] = $req_data33; 
        $XMLSTORE = ArrayToXml::convert($wrapped_links33);


        for ($i=0; $i<=$r_count2; $i++){
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

        //dd($reqdata2);        

        if(isset($reqdata2)) { 
            $wrapped_links2["TNC"] = $reqdata2;
            $XMLTNC = ArrayToXml::convert($wrapped_links2);
        }
        else {
            $XMLTNC = NULL; 
        }  

        

        for ($i=0; $i<=$r_count4; $i++){
                if(isset($request['CTID_REF']) && !is_null($request['CTID_REF']))
                {
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
                            'ASPER_ACTUAL'  => (isset($request['calACTUAL_'.$i]) ) ? 1 : 0  
                        ];
                    }
                }
            
        }

        // dd($reqdata4);   

        if(isset($reqdata4))
        { 
            $wrapped_links4["CALCULATIONTEMPLATE"] = $reqdata4; 
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

        $PRR_NO             =   $request['PRR_NO'];
        $PRR_DT             =   $request['PRR_DT'];
        $SLID_REF           =   $request['SLID_REF'];        
        $BILLTO             =   $request['BILLTO'];
        $SHIPTO             =   $request['SHIPTO'];
        $VCL_NO             =   $request['VCL_NO'];
        $TRASPORTER_NAME    =   $request['TRASPORTER_NAME'];
        $DRIVER_NAME        =   $request['DRIVER_NAME'];
        $PURPOSE            =   $request['PURPOSE'];
        
        
        $log_data = [ 
            $PRR_NO,$PRR_DT,$SLID_REF,$BILLTO,$SHIPTO,$VCL_NO,$TRASPORTER_NAME,$DRIVER_NAME,$PURPOSE,$CYID_REF, 
            $BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT,$XMLUDF,$XMLSTORE,$XMLTNC,$XMLCAL,$USERID, Date('Y-m-d'), 
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_PRR_UP ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?', $log_data); 


         $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');    
        
        if($contains){
            return Response::json(['success' =>true,'msg' => $PRR_NO. ' Sucessfully Approved.']);

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
                $TABLE      =   "TBL_TRN_PRRT01_HDR";
                $FIELD      =   "PRRID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
            
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_PRR ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
    
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
        $TABLE      =   "TBL_TRN_PRRT01_HDR";
        $FIELD      =   "PRRID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_PRRT01_MAT',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_PRRT01_MULTISTORE',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_PRRT01_UDF',
        ];
        $req_data[3]=[
            'NT'  => 'TBL_TRN_PRRT01_TNC',
        ];
        $req_data[4]=[
            'NT'  => 'TBL_TRN_PRRT01_CAL',
        ];

        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_PRR  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_PRRT01_HDR')->where('PRRID','=',$id)->first();

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
        
		//$destinationPath = storage_path()."/docs/company".$CYID_REF."/PurchaseReturn";
        $image_path         =   "docs/company".$CYID_REF."/PurchaseReturn";     
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
        $PRR_NO = $request->PRR_NO;
        
        $objSO = DB::table('TBL_TRN_PRRT01_HDR')
        ->where('TBL_TRN_PRRT01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_PRRT01_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_PRRT01_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_PRRT01_HDR.PRR_NO','=',$PRR_NO)
        ->select('TBL_TRN_PRRT01_HDR.PRR_NO')
        ->first();
        
        if($objSO){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate PRR NO']);
        
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
        $PBID_REF   =   $Field_Id[0];
        $ITEMID_REF =   $Field_Id[1];
        $MRSID_REF  =   $Field_Id[2];
        $PIID_REF   =   $Field_Id[3];
        $RFQID_REF  =   $Field_Id[4];
        $VQID_REF   =   $Field_Id[5];
        $POID_REF   =   $Field_Id[6];
        $GEID_REF   =   $Field_Id[7];
        $GRN_NO     =   $Field_Id[8];
        $IPOID_REF  =   $Field_Id[9];

        $objBatch =  DB::SELECT("SELECT 
        ST.MULTISTID,ST.STID_REF,ST.MAIN_UOMID_REF AS MAINUOMID_REF,
        ST.ALT_UOMID_REF AS ALTUOMID_REF,ST.STOCK_INHAND,ST.ITEMID_REF,
        CONCAT(T4.STCODE,'-',T4.NAME) AS StoreName, 
        CONCAT(T5.UOMCODE,'-',T5.DESCRIPTIONS) as MainUom,
        CONCAT(T6.UOMCODE,'-',T6.DESCRIPTIONS) as AltUom 
        FROM TBL_TRN_IGRN02_MULTISTORE ST 
        LEFT JOIN TBL_MST_STORE T4 ON ST.STID_REF=T4.STID
        LEFT JOIN TBL_MST_UOM T5 ON ST.MAIN_UOMID_REF=T5.UOMID
        LEFT JOIN TBL_MST_UOM T6 ON ST.ALT_UOMID_REF=T6.UOMID
        WHERE ST.GRNID_REF='$GRN_NO' AND ST.GEID_REF='$GEID_REF' AND ISNULL(ST.POID_REF,ST.BPOID_REF)='$POID_REF' AND ST.ITEMID_REF='$ITEMID_REF'
        ");

        echo '<thead>';
        echo '<tr>';
        echo '<th>Store</th>';
        echo '<th>Batch No</th>';
        echo '<th>Main UoM (MU)</th>';
        echo '<th>Stock-in-hand</th>';
        echo '<th width="15%">Return Qty (MU)</th>';
        echo '<th>Alt UOM (AU)</th>';
        echo '<th width="15%">Return Qty (AU)</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach($objBatch as $key=>$val){

 
            $desc6          =   $val->MULTISTID;
            $qtyvalue       =   array_key_exists($desc6, $dataArr)?$dataArr[$desc6]:0;
            $qtyvalue1      =   $request['ACTION_TYPE'] =="ADD"?'':$qtyvalue;
            $AluQty         =   $this->getAltUmQty($val->ALTUOMID_REF,$val->ITEMID_REF,$qtyvalue);
            $ObjData        =   $this->getStockQty($GRN_NO,$val->STID_REF,$ITEMID_REF,$val->MAINUOMID_REF);

            $STOCK_INHAND   =   isset($ObjData) && $ObjData->CURRENT_QTY !=""?$ObjData->CURRENT_QTY:'0';
            $BATCHNO        =   isset($ObjData) && $ObjData->BATCH_CODE !=""?$ObjData->BATCH_CODE:'';
           
            echo '<tr  class="participantRow33">';
            echo '<td>'.$val->StoreName.'</td>';
            echo  '<td>'.$BATCHNO.'</td>';
            echo '<td>'.$val->MainUom.'</td>';
            echo '<td>'.$STOCK_INHAND.'</td>';
            echo '<td style="width:10%"><input '.$ACTION_TYPE.' type="text" name="UserQty_'.$key.'" id="UserQty_'.$key.'" value="'.$qtyvalue1.'" class="qtytext" onkeyup="checkStoreQty('.$ROW_ID.','.$qtyvalue.',this.value,'.$key.','.$val->ITEMID_REF.','.$val->ALTUOMID_REF.')" onkeypress="return isNumberDecimalKey(event,this)" autocomplete="off"  ></td>';
            echo '<td>'.$val->AltUom.'</td>';
            echo '<td style="width:10%"><input '.$ACTION_TYPE.' type="text" name="AltUserQty_'.$key.'" id="AltUserQty_'.$key.'" value="'.$AluQty.'" readonly class="qtytext" autocomplete="off"  ></td>';
            echo '<td hidden><input type="hidden" name="BATCHID_'.$key.'" id="BATCHID_'.$key.'" value="'.$desc6.'" class="qtytext" ></td>';
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

    public function getStockQty($GRN_NO,$STID_REF,$ITEMID_REF,$UOMID_REF){


        $CYID_REF   = Auth::user()->CYID_REF;
        $BRID_REF   = Session::get('BRID_REF');
        $FYID_REF   = Session::get('FYID_REF');

        $ObjData =  DB::table('TBL_MST_BATCH')
        ->where('DOC_ID','=',$GRN_NO)
        ->where('DOC_TYPE','=','GRN AGAINST GE')
        ->where('STID_REF','=',$STID_REF)
        ->where('ITEMID_REF','=',$ITEMID_REF)
        ->where('UOMID_REF','=',$UOMID_REF)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('STATUS','=',"A")
        ->select('BATCH_CODE','CURRENT_QTY')
        ->first();

        return $ObjData;

    }

    public function LastApprovedDocDate(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $VTID_REF     =   $this->vtid_ref;
        return $objlastDocDate = DB::select('SELECT MAX(PRR_DT) PRR_DT FROM TBL_TRN_PRRT01_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $VTID_REF, $Status ]);

    }

    
}
