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
use App\Helpers\UserRights;
use App\Helpers\Utils;
use Facade\Ignition\DumpRecorder\Dump;

class TrnFrm201Controller extends Controller
{
    protected $form_id = 201;
    protected $vtid_ref   = 167;  //voucher type id
    // //validation messages
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];
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

        
        $objRights=[];
        $objRights      =   UserRights::getRights(Auth::user()->USERID,Auth::user()->CYID_REF,Session::get('BRID_REF'),Session::get('FYID_REF'),$this->vtid_ref);

        $FormId         =   $this->form_id;

        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');     


        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList	=	DB::select("select hdr.SPIID,hdr.SPI_NO,hdr.SPI_DT,hdr.REMARKS,hdr.INDATE,hdr.VENDOR_INNO,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.SPIID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_PRPB02_HDR hdr
                            on a.VID = hdr.SPIID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_SUBLEDGER sl ON hdr.VID_REF = sl.SGLID
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.SPIID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );

                            $checkCompany   =	Helper::checkCompany(Auth::user()->CYID_REF,'alps');

       
        return view('transactions.Purchase.ServicePurchaseInvoice.trnfrm201',compact(['REQUEST_DATA','objRights','FormId','objDataList','checkCompany']));        
    }

    public function add(){       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $objglcode = DB::table('TBL_MST_DEPARTMENT')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('STATUS','=',$Status)
        ->select('*')
        ->get()
        ->toArray();

       

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_PRPB02_HDR',
            'HDR_ID'=>'SPIID',
            'HDR_DOC_NO'=>'SPI_NO',
            'HDR_DOC_DT'=>'SPI_DT'
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
        
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_SPI")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDFSPIID')->from('TBL_MST_UDFFOR_SPI')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                      
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                     
                   

        $objUdfSOData = DB::table('TBL_MST_UDFFOR_SPI')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdfSOData);
    
        
        $objlast_DT = DB::select('SELECT MAX(SPI_DT) SPI_DT FROM TBL_TRN_PRPB02_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF, $FYID_REF, $this->vtid_ref, 'N' ]);

        $objTemplateMaster  =$this->getTemplateMaster("PURCHASE");

        $AlpsStatus =   $this->AlpsStatus();
       
        $FormId=$this->form_id;

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        $checkCompany   =	Helper::checkCompany(Auth::user()->CYID_REF,'alps');
        $objothcurrency = $this->GetCurrencyMaster();
       // dd($TabSetting);
    return view('transactions.Purchase.ServicePurchaseInvoice.trnfrm201add',  
    compact(['objglcode','objCalculationHeader','objUdfSOData','objTNCHeader',  'objothcurrency',
    'objCountUDF','objlast_DT','AlpsStatus','objTemplateMaster','FormId','TabSetting','checkCompany','doc_req','docarray']));       
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
                        $row3 =    '<td style="text-align:center;" hidden><input type="checkbox" class="filter-none" name="calACTUAL_'.$dindex.'" id="calACTUAL_'.$dindex.'" checked ></td>';
                    }
                    else{
                        $row3 =    '<td style="text-align:center;" hidden><input type="checkbox" class="filter-none" name="calACTUAL_'.$dindex.'" id="calACTUAL_'.$dindex.'"  ></td>';
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

    public function getTDSDetails(Request $request){
            $Status = "A";
            $id = $request['id'];
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');


            $ObjVenorData =  DB::select('SELECT TOP 1 * FROM TBL_MST_VENDOR  WHERE SLID_REF = ? AND  CYID_REF = ? AND BRID_REF = ?  ', [$id,$CYID_REF,$BRID_REF,$FYID_REF]);

           
           
            $HIDS='';
            if(!empty($ObjVenorData)){
                $HIDS = is_null($ObjVenorData[0]->HOLDINGID_REF) ? 0 : $ObjVenorData[0]->HOLDINGID_REF;
            }
           
            $ObjData=[];
            if($HIDS!=''){
                $ObjData =  DB::select("SELECT * FROM TBL_MST_WITHHOLDING WHERE CYID_REF = $CYID_REF AND FYID_REF = $FYID_REF AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 )  AND HOLDINGID IN ($HIDS)  ");
            }
            
            if(!empty($ObjData)){

                 $cur_date = Date('Y-m-d');
              
    
                foreach ($ObjData as $dindex=>$dataRow){

                  //  SELECT * FROM TBL_MST_SECTION  where SECTIONID
                    $ObjSection =  DB::select("SELECT SECTION_CODE,SECTION_NAME FROM TBL_MST_SECTION WHERE CYID_REF=$CYID_REF AND SECTIONID = $dataRow->SECTIONID_REF ");
                    $section_name = '';
                    
                    if(!empty($ObjSection)){
                        $section_name =  $ObjSection[0]->SECTION_CODE.'-'.$ObjSection[0]->SECTION_NAME;
                    }

                    $row = '';
                    $add_row = false;
                    if( is_null($dataRow->APPLICABLE_FRDT) ){
                        $add_row = true;
                    }
                    else if( !is_null($dataRow->APPLICABLE_FRDT) && strtotime($cur_date) >= strtotime($dataRow->APPLICABLE_FRDT) )
                    {
                        $add_row = true;
                    }
                    
                    if($add_row){
                        $row = $row.'<tr class="participantRow8"><td hidden><input type="hidden" name="HOLDINGID_'.$dindex.'" id="HOLDINGID_'.$dindex.'" value="'.$dataRow->HOLDINGID.'" class="form-control" autocomplete="off" /></td>
                        <td hidden><input type="text" name="ASSESSEEID_REF_'.$dindex.'" id="ASSESSEEID_REF_'.$dindex.'" value="'.$dataRow->ASSESSEEID_REF.'"  class="form-control five-digits" maxlength="8" autocomplete="off"  readonly/></td>
                        <td hidden><input type="hidden" name="BASE_TYPE_'.$dindex.'" id="BASE_TYPE_'.$dindex.'" value="'.$dataRow->BASE_TYPE.'" class="form-control" autocomplete="off" /></td>
                        <td hidden><input type="hidden" name="APPLICABLE_FRDT_'.$dindex.'" id="APPLICABLE_FRDT_'.$dindex.'" value="'.$dataRow->APPLICABLE_FRDT.'" class="form-control" autocomplete="off" /></td>
    
                        <td style="text-align:center;" ><input type="checkbox" class="filter-none" name="calTDS_'.$dindex.'" id="calTDS_'.$dindex.'"  value="'.$dataRow->HOLDINGID.'" ></td>
                        <td hidden><input type="text" name="popupHID_'.$dindex.'" id="popupHID_'.$dindex.'" class="form-control"  autocomplete="off"  value="" readonly/></td>
                        <td ><input type="text" name="CODE_DESC_'.$dindex.'" id="CODE_DESC_'.$dindex.'" class="form-control"  value="'.$dataRow->CODE_DESC.'" style="width:100px" readonly/></td>
                        <td><input type="text" name="SECTIONID_REF_'.$dindex.'" id="SECTIONID_REF_'.$dindex.'" value="'.$section_name.'"  class="form-control" style="width:100px" readonly/></td>
                        
                        <td><input type="text" name="ASSEVAL_TDS_RATE_'.$dindex.'" id="ASSEVAL_TDS_RATE_'.$dindex.'" value="0"  class="form-control five-digits" maxlength="13" autocomplete="off" style="width:100px" readonly/></td>
                        <td><input type="text" name="ACT_TDS_RATE_'.$dindex.'" id="ACT_TDS_RATE_'.$dindex.'" value="'.$dataRow->TDS_RATE.'"  class="form-control " maxlength="13" autocomplete="off" style="width:100px" readonly/></td>
                        <td hidden><input type="text" name="TDS_EXEMP_LIMIT_'.$dindex.'" id="TDS_EXEMP_LIMIT_'.$dindex.'" value="0.00" class="form-control two-digits" maxlength="13" autocomplete="off" style="width:100px"  readonly/></td>
                        <td><input type="text" name="TDS_RATE_AMT_'.$dindex.'" id="TDS_RATE_AMT_'.$dindex.'" value="0.00" class="form-control two-digits" maxlength="13" autocomplete="off" style="width:100px"  readonly/></td>
                        
                        <td><input type="text" name="ASSEVAL_SURCHARGE_RAGE_'.$dindex.'" id="ASSEVAL_SURCHARGE_RAGE_'.$dindex.'" value="0"  class="form-control five-digits" maxlength="13" autocomplete="off" style="width:100px" readonly/></td>
                        <td><input type="text" name="ACT_SURCHARGE_RAGE_'.$dindex.'" id="ACT_SURCHARGE_RAGE_'.$dindex.'" value="'.$dataRow->SURCHARGE_RAGE.'"  class="form-control " maxlength="13" autocomplete="off" style="width:100px" readonly/></td>
                        <td hidden><input type="text" name="SURCHARGE_EXEMP_LIMIT_'.$dindex.'" id="SURCHARGE_EXEMP_LIMIT_'.$dindex.'" value="0.00"  class="form-control " maxlength="13" autocomplete="off" style="width:100px" readonly/></td>
                        <td><input type="text" name="SURCHARGE_RAGE_AMT_'.$dindex.'" id="SURCHARGE_RAGE_AMT_'.$dindex.'" value="0.00"  class="form-control " maxlength="13" autocomplete="off" style="width:100px" readonly/></td>
                        
                        <td><input type="text" name="ASSEVAL_CESS_RATE_'.$dindex.'" id="ASSEVAL_CESS_RATE_'.$dindex.'" value="0" class="form-control five-digits" maxlength="13" autocomplete="off" style="width:100px" readonly/></td>
                        <td><input type="text" name="ACT_CESS_RATE_'.$dindex.'" id="ACT_CESS_RATE_'.$dindex.'" value="'.$dataRow->CESS_RATE.'" class="form-control " maxlength="13" autocomplete="off" style="width:100px" readonly/></td>
                        <td hidden><input type="text" name="CESS_EXEMP_LIMIT_'.$dindex.'" id="CESS_EXEMP_LIMIT_'.$dindex.'" value="0.00" class="form-control " maxlength="13" autocomplete="off" style="width:100px" readonly/></td>
                        <td><input type="text" name="CESS_RATE_AMT_'.$dindex.'" id="CESS_RATE_AMT_'.$dindex.'" value="0.00" class="form-control " maxlength="13" autocomplete="off" style="width:100px" readonly/></td>
                        
                        <td><input type="text" name="ASSEVAL_SP_CESS_RATE_'.$dindex.'" id="ASSEVAL_SP_CESS_RATE_'.$dindex.'"  value="0" class="form-control five-digits"maxlength="13"" autocomplete="off" style="width:100px" readonly/></td>
                        <td><input type="text" name="ACT_SP_CESS_RATE_'.$dindex.'" id="ACT_SP_CESS_RATE_'.$dindex.'"  value="'.$dataRow->SP_CESS_RATE.'" class="form-control " maxlength="13" autocomplete="off" style="width:100px" readonly/></td>
                        <td hidden><input type="text" name="SP_CESS_EXEMP_LIMIT_'.$dindex.'" id="SP_CESS_EXEMP_LIMIT_'.$dindex.'"  value="0.00" class="form-control " maxlength="13" autocomplete="off" style="width:100px" readonly/></td>
                        <td><input type="text" name="SP_CESS_RATE_AMT_'.$dindex.'" id="SP_CESS_RATE_AMT_'.$dindex.'"  value="0.00" class="form-control " maxlength="13" autocomplete="off" style="width:100px" readonly/></td>
                        
                        <td><input type="text"  name="TOT_TDS_AMT_'.$dindex.'" id="TOT_TDS_AMT_'.$dindex.'" value="0.00" class="form-control "   autocomplete="off" style="width:100px" readonly/></td>
                        </tr>                                                  
                        ';
                        echo $row;
                    }
                   
                }   
    
            }else{
                echo '';
            }
            exit();
        
    }

    public function getTDSDetailsCount(Request $request){
            $Status = "A";
            $id = $request['id'];
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');

            $ObjVenorData =  DB::select('SELECT TOP 1 * FROM TBL_MST_VENDOR  WHERE VID = ? AND  CYID_REF = ? AND BRID_REF = ?  ', [$id,$CYID_REF,$BRID_REF,$FYID_REF]);

           
           
            $HIDS='';
            if(!empty($ObjVenorData)){
                $HIDS = is_null($ObjVenorData[0]->HOLDINGID_REF) ? 0 : $ObjVenorData[0]->HOLDINGID_REF;
            }
           
            $ObjData=[];
            if($HIDS!=''){
                $ObjData =  DB::select("SELECT * FROM TBL_MST_WITHHOLDING WHERE CYID_REF = $CYID_REF AND FYID_REF = $FYID_REF AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS='A' AND HOLDINGID IN ($HIDS)  ");
            }
            
            if(!empty($ObjData)){
                    $cur_date = Date('Y-m-d');
                    $row_count = 0;
                    foreach ($ObjData as $dindex=>$dataRow){
                        $add_row = false;
                        if( is_null($dataRow->APPLICABLE_FRDT) ){
                            $add_row = true;
                        }
                        else if( !is_null($dataRow->APPLICABLE_FRDT) && strtotime($cur_date) >= strtotime($dataRow->APPLICABLE_FRDT) ){
                            $add_row = true;
                        }
                        
                        if($add_row){                         
                            $row_count =  $row_count + 1;
                        }                     
                    }      
                    echo $row_count;      
            }else{
                echo '0';
            }
            exit();
    }

  


    public function getsalesquotation(Request $request){
        
    
        }

        //pi list begin
        public function getpilist(Request $request){
            $Status = "A";
            $id = $request['id'];
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
    
            $SP_PARAMETERS = [$CYID_REF,$BRID_REF,$FYID_REF,$id];
    
            $ObjData =  DB::select('EXEC SP_PI_GETLIST ?,?,?,?', $SP_PARAMETERS);
        
                if(!empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){
                    $row = '';
                    $row = $row.'<tr id="sqcode_'.$dataRow->PIID .'"  class="clssqid"><td width="50%">'.$dataRow->PI_NO;
                    $row = $row.'<input type="hidden" id="txtsqcode_'.$dataRow->PIID.'" data-desc="'.$dataRow->PI_NO.'" 
                    value="'.$dataRow->PIID.'"/></td><td>'.$dataRow->PI_DT.'</td></tr>';
                    echo $row;
                }
                }else{
                    echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
                exit();
        }

        //pi list end
        public function getvqlist(Request $request){
            $Status = "A";
            $id = $request['id'];
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
    
            $SP_PARAMETERS = [$CYID_REF,$BRID_REF,$FYID_REF,$id];
    
            $ObjData =  DB::select('EXEC SP_VQ_GETLIST ?,?,?,?', $SP_PARAMETERS);
        
                if(!empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){
                    $row = '';
                    $row = $row.'<tr id="sqcode_'.$dataRow->VQID .'"  class="clssqid"><td width="50%">'.$dataRow->VQ_NO;
                    $row = $row.'<input type="hidden" id="txtsqcode_'.$dataRow->VQID.'" data-desc="'.$dataRow->VQ_NO.'" 
                    value="'.$dataRow->VQID.'"/></td><td>'.$dataRow->VQ_DT.'</td></tr>';
                    echo $row;
                }
                }else{
                    echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
                exit();
        }

        public function getvendorquotation(Request $request){
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
                    $row = $row.'<tr id="sqcode_'.$dataRow->SQID .'"  class="clssqid"><td width="50%">'.$dataRow->SQNO;
                    $row = $row.'<input type="hidden" id="txtsqcode_'.$dataRow->SQID.'" data-desc="'.$dataRow->SQNO.'" 
                    value="'.$dataRow->SQID.'"/></td><td>'.$dataRow->SQDT.'</td></tr>';
                    echo $row;
                }
                }else{
                    echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
                exit();
        }
    
        



public function getItemSPO(Request $request){

    $Status = "A";
    $CYID_REF = Auth::user()->CYID_REF;
    $BRID_REF = Session::get('BRID_REF');
    $FYID_REF = Session::get('FYID_REF');

    $AlpsStatus =   $this->AlpsStatus();

    $SPIID= isset($request['SPIID'])?$request['SPIID']:0; 
   
    
   
    $SPOID= $request['id'];
    
    $StdCost = 0;

    $objSO = DB::table('TBL_TRN_PROR04_HDR')
            ->where('TBL_TRN_PROR04_HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_PROR04_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_PROR04_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_PROR04_HDR.SPOID','=',$SPOID)
            ->select('TBL_TRN_PROR04_HDR.*')
            ->first();

       

        $VENDORID = $objSO->VID_REF;

        $taxstate='';
        
        $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  WHERE  SHIPTO= ? AND LID = ? ', [1,$objSO->SHIP_TO]);
        $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  WHERE BRID= ? ', [$BRID_REF]);
        if($ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF)
        {
            $taxstate = 'WithinState';
        }
        else
        {
            $taxstate = 'OutofState';
        }

        $objVendorMst =  DB::select('SELECT TOP 1 VID,VCODE,VGID_REF FROM TBL_MST_VENDOR  WHERE SLID_REF = ?', [ $VENDORID ]);  
        $VGID = $objVendorMst[0]->VGID_REF;

        $objVPLHDR =  DB::select('SELECT VPLID,VGID_REF,VID_REF FROM TBL_MST_VENDORPRICELIST_HDR  where VGID_REF=? AND STATUS=? AND CYID_REF=?', [$VGID, 'A',$CYID_REF]);   //check vendor group

        //dd($objVPLHDR);     
        if(empty($objVPLHDR)){
            $objVPLHDR =  DB::select('SELECT VPLID,VGID_REF,VID_REF FROM TBL_MST_VENDORPRICELIST_HDR  where VID_REF=? AND STATUS=?', [$VENDORID, 'A']); //check vendor
        //dump($objVPLHDR); 
        }

        $ObjItem =  DB::select("SELECT * FROM TBL_MST_ITEM T1
        INNER JOIN TBL_TRN_PROR04_MAT T2 ON T1.ITEMID=T2.SERVICECODE
        WHERE T1.CYID_REF = '$CYID_REF' AND ( T1.DEACTIVATED IS NULL OR T1.DEACTIVATED = 0 ) AND T1.STATUS ='$Status' AND T2.SPOID_REF='$SPOID'");

        

            if(!empty($ObjItem))
            {

                foreach ($ObjItem as $index=>$dataRow){

                    $spo_balance    =   $this->getBalanceAmount($dataRow->SPOID_REF,$dataRow->SERVICECODE);
                    $spi_balance    =   $this->getSpiBalanceAmount($dataRow->SPOID_REF,$dataRow->SERVICECODE);
                    $tot_balance    =   $spo_balance-$spi_balance;

                    

                    /*
                    $ObjSavedQty =   DB::table('TBL_TRN_PRPB02_SRV')
                        ->where('TBL_TRN_PRPB02_SRV.SPOID_REF','=',$dataRow->SPOID_REF)
                        ->where('TBL_TRN_PRPB02_SRV.SRVID_REF','=',$dataRow->SERVICECODE)
                        ->where('TBL_TRN_PRPB02_SRV.UOMID_REF','=',$dataRow->UOMID_REF)
                        ->where('TBL_TRN_PRPB02_HDR.STATUS','<>','C') 
                        ->leftJoin('TBL_TRN_PRPB02_HDR',   'TBL_TRN_PRPB02_HDR.SPIID','=',   'TBL_TRN_PRPB02_SRV.SPIID_REF')
                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PRPB02_SRV.BILL_QTY),0) AS ISSUED_QTY'))                       
                        ->get();

                    $Total_ISSUED_QTY = $ObjSavedQty[0]->ISSUED_QTY ;
                   
                    $SPI_SPOQTY =  isset($dataRow->SPO_QTY)? $dataRow->SPO_QTY : 0;   
                    $BAL_SPI_SPOQTY = number_format( floatVal($SPI_SPOQTY) - floatval($Total_ISSUED_QTY), 3,".","" ) ;

                  
                    $consQty = 0;
                    if($SPIID>0){
                        $ObjConsumedQty =   DB::table('TBL_TRN_PRPB02_SRV')                                    
                        ->where('TBL_TRN_PRPB02_SRV.SPIID_REF','=',$SPIID)
                        ->where('TBL_TRN_PRPB02_SRV.SPOID_REF','=',$dataRow->SPOID_REF)
                        ->where('TBL_TRN_PRPB02_SRV.SRVID_REF','=',$dataRow->SERVICECODE)
                        ->where('TBL_TRN_PRPB02_SRV.UOMID_REF','=',$dataRow->UOMID_REF)
                        ->where('TBL_TRN_PRPB02_HDR.STATUS','<>','C') 
                        ->leftJoin('TBL_TRN_PRPB02_HDR',   'TBL_TRN_PRPB02_HDR.SPIID','=',   'TBL_TRN_PRPB02_SRV.SPIID_REF')
                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PRPB02_SRV.BILL_QTY),0) AS CONSUMED_ISSUED_QTY'))
                        ->get();
                        $consQty = $ObjConsumedQty[0]->CONSUMED_ISSUED_QTY;      
                        $BAL_SPI_SPOQTY = number_format( floatVal($BAL_SPI_SPOQTY) + floatval($consQty), 3,".","" ) ;   
               
                    }
                    */

                    $BAL_SPI_SPOQTY =0;

                    ///++++++++++++++++++++++++++++++++++
                    //############################
                    ///$Taxid = [];
                    $ObjLIST=[];
                    if(!empty($objVPLHDR)){
                        $ObjLIST =   DB::table('TBL_MST_VENDORPRICELIST_MAT')  
                            ->select('*')
                            ->where('VPLID_REF','=',$objVPLHDR[0]->VPLID)
                            ->where('ITEMID_REF','=',$dataRow->ITEMID)
                            ->where('UOMID_REF','=',$dataRow->MAIN_UOMID_REF)
                            ->first();
                    }
                        
                    if(!empty($ObjLIST))
                    {
                                $ObjInTax = $ObjLIST->GST_IN_LP; 
                                $RATE = $ObjLIST->LP;  
                               // dd($ObjLIST);                          
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
                                    $ObjStdCost =  ($ObjLIST->LP*100)/$ObjTaxDet;
                                    $StdCost = $ObjStdCost;
                                    
                                }
                                else
                                {
                                   // $Taxid = [];
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
                                    $StdCost = $ObjLIST->LP;                                       
                                }
                    }
                    else
                    {
                        //IF VENDOR PRICE LIST NOT FOUND
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
                        //$RATE = $dataRow->STDCOST;
                    }
                

                        ///////////////////////////////
                       // if(floatval($BAL_SPI_SPOQTY)>0){  ///%%%%
                       
                       
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

                        
                        $TOQTY    =    $dataRow->SPO_QTY;
                        $FROMQTY    =  $dataRow->SPO_QTY;      
                  
                       

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
                            $Taxid_0 = (!empty($Taxid[0])) ? $Taxid[0] : '';  
                            $Taxid_1 = (!empty($Taxid[1])) ? $Taxid[1] : '';  
                            if($taxstate != "OutofState")
                            {
                            $row = $row.'<tr id="item_'.$index.'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$index.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                            $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                            $row = $row.'<input type="hidden" id="txtitem_'.$index.'" data-desc="'.$dataRow->ICODE.'" data-desc1="'.$dataRow->SPO_QTY.'" data-desc2="'.$dataRow->SPO_RATE.'" data-desc3="'.$dataRow->DISCOUNT_PER.'" data-desc4="'.$dataRow->DIS_AMT.'" data-desc5="'.$dataRow->ITEMID.'" data-desc6="'.$tot_balance.'"  data-balspoqty="'.$BAL_SPI_SPOQTY.'" value="'.$dataRow->ITEMID.'"/></td>
                            <td  style="width:10%;" id="itemname_'.$index.'" >'.$dataRow->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$index.'" data-desc="'.$dataRow->ITEM_SPECI.'"  value="'.$dataRow->NAME.'"/></td>';
                            $row = $row.'<td  style="width:8%;" id="itemuom_'.$index.'" ><input type="hidden" id="txtitemuom_'.$index.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                            data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                            $row = $row.'<td  style="width:8%;" id="uomqty_'.$index.'" ><input type="hidden" id="txtuomqty_'.$index.'" data-desc="'.$TOQTY.'"  value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                            $row = $row.'<td style="width:8%;" id="irate_'.$index.'"><input type="hidden" id="txtirate_'.$index.'" data-desc="'.$dataRow->SPO_RATE.'"   value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                            $row = $row.'<td  style="width:8%;" id="itax_'.$index.'"><input type="hidden" id="txtitax_'.$index.'" data-desc="'.$Taxid_0.'"     value="'.$Taxid_1.'"/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                            <td style="width:8%;">'.$BusinessUnit.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                            <td  style="width:8%;" id="ise_'.$index.'"><input type="hidden" id="txtise_'.$index.'"       value=""/>Authorized </td>  </tr>';
                            }
                            else
                            {
                                $row = $row.'<tr id="item_'.$index .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$index.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                                $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                                $row = $row.'<input type="hidden" id="txtitem_'.$index.'" data-desc="'.$dataRow->ICODE.'" data-desc1="'.$dataRow->SPO_QTY.'" data-desc2="'.$dataRow->SPO_RATE.'" data-desc3="'.$dataRow->DISCOUNT_PER.'" data-desc4="'.$dataRow->DIS_AMT.'" data-desc5="'.$dataRow->ITEMID.'"  data-desc6="'.$tot_balance.'"  data-balspoqty="'.$BAL_SPI_SPOQTY.'"  value="'.$dataRow->ITEMID.'"/></td>
                                <td  style="width:10%;" id="itemname_'.$index.'" >'.$dataRow->NAME;
                                $row = $row.'<input type="hidden" id="txtitemname_'.$index.'" data-desc="'.$dataRow->ITEM_SPECI.'"  value="'.$dataRow->NAME.'"/></td>';
                                $row = $row.'<td  style="width:8%;" id="itemuom_'.$index.'" ><input type="hidden" id="txtitemuom_'.$index.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                                value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                                $row = $row.'<td style="width:8%;" id="uomqty_'.$index.'" ><input type="hidden" id="txtuomqty_'.$index.'" data-desc="'.$TOQTY.'" value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                                $row = $row.'<td  style="width:8%;" id="irate_'.$index.'"><input type="hidden" id="txtirate_'.$index.'" data-desc="'.$dataRow->SPO_RATE.'"  value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                                $row = $row.'<td  style="width:8%;" id="itax_'.$index.'"><input type="hidden" id="txtitax_'.$index.'" data-desc="'.$Taxid_0.'"    value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                                
                                <td style="width:8%;">'.$BusinessUnit.'</td>
                                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                                <td  style="width:8%;" id="ise_'.$index.'"><input type="hidden" id="txtise_'.$index.'" 
                                value=""/>Authorized </td>
                                </tr>'; 
                            }
                            
                            // if(floatval($BAL_SPI_SPOQTY)>0){  
                            //     echo $row;
                            // }   
                            
                            echo $row;
                        
                         
                        ///////////////////////////////
                    //############################
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

        $ObjData =  DB::select('SELECT top 1 CREDITDAY FROM TBL_MST_VENDOR  
                    WHERE STATUS= ? AND VID = ? ', [$Status,$id]);

        
            if(!empty($ObjData)){

            $CDAYS = IS_NULL($ObjData[0]->CREDITDAY)? 0 : $ObjData[0]->CREDITDAY;
            // echo($ObjData[0]->CREDITDAY);
            echo($CDAYS);

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
            
            $ObjCust =  DB::select('SELECT top 1 VID FROM TBL_MST_VENDOR  
                        WHERE STATUS= ? AND VID = ? ', [$Status,$id]);
        
            $cid = $ObjCust[0]->VID;
            $ObjBillTo =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                        WHERE DEFAULT_BILLING= ? AND VID_REF = ? ', [1,$cid]);

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
                $id = $request['id'];
                $BRID_REF = Session::get('BRID_REF');
                

                $ObjCust =  DB::select('SELECT top 1 VID FROM TBL_MST_VENDOR  
                            WHERE STATUS= ? AND VID = ? ', [$Status,$id]);
            
                $cid = $ObjCust[0]->VID;
                $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                            WHERE DEFAULT_SHIPPING= ? AND VID_REF = ? ', [1,$cid]);

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
                $id = $request['id'];
                if(!is_null($id))
                {
                $ObjCust =  DB::select('SELECT top 1 VID FROM TBL_MST_VENDOR  
                WHERE STATUS= ? AND VID = ? ', [$Status,$id]);
    
                $cid = $ObjCust[0]->VID;
                $ObjBillTo =  DB::select('SELECT  * FROM TBL_MST_VENDORLOCATION  
                            WHERE BILLTO= ? AND VID_REF = ? ', [1,$cid]);
            
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
                        $row = $row.'<tr id="billto_'.$dataRow->LID .'"  class="clsbillto"><td width="50%">'.$dataRow->NAME;
                        $row = $row.'<input type="hidden" id="txtbillto_'.$dataRow->LID.'" data-desc="'.$objAddress.'" 
                        value="'.$dataRow->LID.'"/></td><td>'.$objAddress.'</td></tr>';
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
                $ObjCust =  DB::select('SELECT top 1 VID FROM TBL_MST_VENDOR  
                        WHERE STATUS= ? AND VID = ? ', [$Status,$id]);
        
                $cid = $ObjCust[0]->VID;
                $ObjShipTo =  DB::select('SELECT  * FROM TBL_MST_VENDORLOCATION  
                            WHERE SHIPTO= ? AND VID_REF = ? ', [1,$cid]);
            
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
                        $row = $row.'<tr id="shipto_'.$dataRow->LID .'"  class="clsshipto"><td width="50%">'.$dataRow->NAME;
                        $row = $row.'<input type="hidden" id="txtshipto_'.$dataRow->LID.'" data-desc="'.$TAXSTATE.'" 
                        value="'.$dataRow->LID.'"/></td><td id="txtshipadd_'.$dataRow->LID.'" >'.$objAddress.'</td></tr>';
                        echo $row;
                    }
            
                    }else{
                        echo '<tr><td colspan="2">Record not found.</td></tr>';
                    }
                    exit();
                }
            }


   //display attachments form
   public function attachment($id){

        $FormId = $this->form_id;
        if(!is_null($id))
        {
            $objMst = DB::table("TBL_TRN_PRPB02_HDR")
                        ->where('SPIID','=',$id)
                        ->select('*')
                        ->first();        

            $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
                        ->where('VTID','=',$this->vtid_ref)
                        ->select('VTID','VCODE','DESCRIPTIONS','INDATE')
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

                            $checkCompany   =	Helper::checkCompany(Auth::user()->CYID_REF,'alps');
                            
                return view('transactions.Purchase.ServicePurchaseInvoice.trnfrm201attachment',compact(['FormId','objMst','objMstVoucherType','objAttachments','checkCompany']));
        }

}

    
   public function save(Request $request) {

     
     
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];
        $r_count5 = $request['Row_Count5'];
        $r_count8 = $request['Row_Count8'];  

        $GROSS_TOTAL    =   0; 
        $NET_TOTAL 		= 	$request['TotalValue'];
        $CGSTAMT        =   0; 
        $SGSTAMT        =   0; 
        $IGSTAMT        =   0; 
        $DISCOUNT       =   0; 
        $OTHER_CHARGES  =   0; 
        $TDS_AMOUNT     =   0; 	
        $FC 			= (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF 		= (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT 		= (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";

        
        if(isset($request['rowcountitem'])){
            $r_count9 = count($request['rowcountitem']);
            }else{
            $r_count9 = NULL;
            }


        $SPOID_REF = $request['SLID_REF']; 

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
                    'SPOID_REF'    => $SPOID_REF,
                    'SRVID_REF'    => $request['ITEMID_REF_'.$i],
                    'UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'BILLQTY' => $request['SO_QTY_'.$i],
                    'SHORT_QTY' => (!empty($request['SHORT_QTY_'.$i]) ? $request['SHORT_QTY_'.$i] : 0),
                    'BILL_RATEPUOM' => $request['RATEPUOM_'.$i],
                    'DIS_PER'    => (!empty($request['DISCPER_'.$i])) == 'true' ? $request['DISCPER_'.$i] : 0,
                    'DISCOUNT' => (!empty($request['DISCOUNT_AMT_'.$i])) == 'true' ? $request['DISCOUNT_AMT_'.$i] : 0,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'BOE_NO_REF' => (!empty($request['BOEID_REF_'.$i]) ? $request['BOEID_REF_'.$i] : NULL),
                    'ASSESSABLE_VALUE' => (!empty($request['ASSESSABLE_VALUE_'.$i]) ? $request['ASSESSABLE_VALUE_'.$i] : NULL),
                ];
            }
        }
            $wrapped_links["SERVICE"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);

        //dump($request->all());
       // dd($XMLMAT);    
        
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
                        'UDFSPIID_REF'   => $request['UDFSOID_REF_'.$i],
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
                            'ACTUAL'        => (isset($request['calACTUAL_'.$i])!="true" ? 0 : 1) ,
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

            //tds
            for ($i=0; $i<=$r_count8; $i++)
            {
               
                    if(isset($request['calTDS_'.$i]) && $request['calTDS_'.$i]=!'')
                    {
                        if(isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0==1){
                            $TDS_AMOUNT      += $request['TDS_AMT_'.$i]; 
                            }

                        $reqdata8[$i] = [
                            'TDSID_REF'                 => $request['HOLDINGID_'.$i],                            
                            'ASSESSABLE_VL_TDS' => $request['ASSEVAL_TDS_RATE_'.$i], 
                            'TDS_RATE'          => $request['ACT_TDS_RATE_'.$i],    
                            'ASSESSABLE_VL_SURCHARGE'   => $request['ASSEVAL_SURCHARGE_RAGE_'.$i], 
                            'SURCHARGE_RATE'            => $request['ACT_SURCHARGE_RAGE_'.$i],   
                            'ASSESSABLE_VL_CESS'    => $request['ASSEVAL_CESS_RATE_'.$i], 
                            'CESS_RATE'             => $request['ACT_CESS_RATE_'.$i], 
                            'ASSESSABLE_VL_SPCESS'  => $request['ASSEVAL_SP_CESS_RATE_'.$i], 
                            'SPCESS_RATE'           => $request['ACT_SP_CESS_RATE_'.$i],                            
                        ];
                    }
                
            }
            if(isset($reqdata8))
            { 
                $wrapped_links8["TDS"] = $reqdata8;
                $XMLTDS = ArrayToXml::convert($wrapped_links8);
            }
            else
            {
                $XMLTDS = NULL; 
            }  


            for ($i=0; $i<=$r_count9; $i++)
            {
                if(isset($request['A_ITEMID_REF_'.$i]) && !is_null($request['A_ITEMID_REF_'.$i]))
                {
                    $reqdata9[$i] = [
                        'ITEMID_REF'    => $request['A_ITEMID_REF_'.$i],
                        'UOMID_REF' => $request['A_UOMID_REF_'.$i],
                        'ITEM_SPECS' => $request['A_ITEMSPECI_'.$i],                              
                        'ITEM_VALUE' => (!empty($request['ITEM_AMOUNT_'.$i]) ? $request['ITEM_AMOUNT_'.$i] : 0),
                        'STID_REF' =>  $request['STRID_REF_'.$i],
                    ];
                }
            }

            if(isset($reqdata9))
            { 
                $wrapped_links9["ITEM"] = $reqdata9;
                $XMLITEM = ArrayToXml::convert($wrapped_links9);
            }
            else
            {
                $XMLITEM = NULL; 
            }  





            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');

            $SPI_NO = $request['SONO'];
            $SPI_DT = $request['SODT'];
            $DEPID_REF = $request['GLID_REF']; //DEPT ID
            $VID_REF = $request['VID_REF']; //VENDOR ID
            

           
            $VENDOR_REF_NO = $request['REFNO'];
            $VENDOR_REF_DT = $request['VENDOR_REF_DT'];

            $CREDITDAYS = (trim($request['CREDITDAYS']) =='' ? 0 : $request['CREDITDAYS'] );

         

            $REMARKS = $request['REMARKS'];

            $TDS_CAL = $request['TDS_APPLICABLE'];


            $GST_N_Avail            =   (isset($request['GST_N_Avail'])!="true" ? 0 : 1);
            $GST_Reverse            =   (isset($request['GST_Reverse'])!="true" ? 0 : 1);
            $EXE_GST                =   (isset($request['EXE_GST'])!="true" ? 0 : 1);
            $Template_Description   =   $request['Template_Description'];
            $TDS                    = (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);
            $BOE                    =   (isset($request['BOE'])!="true" ? 0 : 1);
            $DPB                    =   $request['DPB'];
			$TYPE 					= $request['TYPE'];

            $log_data = [ 
                $SPI_NO,    $SPI_DT,    $DEPID_REF,     $VID_REF,     $SPOID_REF,     $VENDOR_REF_NO, $VENDOR_REF_DT,
                $CREDITDAYS,$REMARKS,   $TDS_CAL,       $CYID_REF,    $BRID_REF,      $FYID_REF,      $VTID_REF,
                $XMLMAT,    $XMLTNC,    $XMLUDF,        $XMLCAL,        $XMLTDS,      $XMLPSLB,       $USERID, 
                Date('Y-m-d'),          Date('h:i:s.u'),$ACTIONNAME,      $IPADDRESS,
                $GST_N_Avail,$GST_Reverse,$EXE_GST,$Template_Description,$TDS,$BOE,$DPB,$XMLITEM,$TYPE,
                $FC,$CRID_REF,$CONVFACT,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT

            ];

    
    
            $sp_result = DB::select('EXEC SP_SPI_IN ?,?,?,?,?,?,?, ?,?,?,?,?,?,?, ?,?,?,?,?,?,?, ?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?', $log_data);   

          
            
            $contains = Str::contains(strtolower($sp_result[0]->RESULT), 'success');
    
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
            
            if(!is_null($id))
            {
                $objSO = DB::table('TBL_TRN_PRPB02_HDR')
                                ->where('TBL_TRN_PRPB02_HDR.FYID_REF','=',Session::get('FYID_REF'))
                                ->where('TBL_TRN_PRPB02_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_TRN_PRPB02_HDR.BRID_REF','=',Session::get('BRID_REF'))
                                ->where('TBL_TRN_PRPB02_HDR.SPIID','=',$id)
                                ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_PRPB02_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
                                ->select('TBL_TRN_PRPB02_HDR.*','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
                                ->first();

                if( isset($objSO->STATUS) && strtoupper($objSO->STATUS)=="A"){
                   // exit("Sorry, Approved record can not edit.");
                }
                                
                //GET SPO DATA
                
                $objData2 =[];
                if(isset($objSO->SPOID_REF) && $objSO->SPOID_REF !=""){
                $objData2 =     DB::table('TBL_TRN_PROR04_HDR')
                                    ->where('FYID_REF','=',Session::get('FYID_REF'))
                                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                                    ->where('BRID_REF','=',Session::get('BRID_REF'))
                                    ->where('SPOID','=',$objSO->SPOID_REF)
                                    ->select('*')
                                    ->first();
                }


                $log_data = [ 
                    $id
                ];

                

                $objSOMAT = DB::select('EXEC sp_get_service_purchase_invoice_material ?', $log_data);

                if(!empty($objSOMAT)){
                    foreach($objSOMAT as $key=>$val){
                        if($objSO->DPB ==="1"){
                            $PB_DOCNO =   DB::table('TBL_TRN_PRPB01_HDR')->where('PBID','=',$val->BOE_NO_REF)->select('PB_DOCNO')->first();
                            $objSOMAT[$key]->DOC_CODE = isset($PB_DOCNO) && $PB_DOCNO->PB_DOCNO !=''?$PB_DOCNO->PB_DOCNO:NULL;
                        }
                        else{
                            $objSOMAT[$key]->DOC_CODE =   $val->BOE_NO_REF;
                        }

                        
                        $spo_balance        =   $this->getBalanceAmount($objSO->SPOID_REF,$val->SRVID_REF);
                        $spi_balance        =   $this->getSpiBalanceAmount($objSO->SPOID_REF,$val->SRVID_REF);
                        $spi_balance_edit   =   $this->getSpiEditBalance($val->SPISRVID);
                        $tot_balance        =   ($spo_balance-$spi_balance)+$spi_balance_edit;

                        $objSOMAT[$key]->TOT_BAL_AMT=$tot_balance;


                    }

                }




               
                $objCount1 = count($objSOMAT);

                
              
              //DUMP($objSOMAT);
                $objSOTNC = DB::table('TBL_TRN_PRPB02_TNC')                    
                                ->where('SPIID_REF','=',$id)
                                ->select('*')
                                ->orderBy('SPI_TNCID','ASC')
                                ->get()->toArray();
                $objCount2 = count($objSOTNC);

                $objSOUDF = DB::table('TBL_TRN_PRPB02_UDF')                    
                                ->where('SPIID_REF','=',$id)
                                ->select('*')
                                ->orderBy('SPI_UDFID','ASC')
                                ->get()->toArray();

                            
                $objCount3 = count($objSOUDF);

                $objSOCAL = DB::table('TBL_TRN_PRPB02_CAL')                    
                                ->where('SPIID_REF','=',$id)
                                ->select('*')
                                ->orderBy('SPI_CALID','ASC')
                                ->get()->toArray();
                $objCount4 = count($objSOCAL);

                
                $objSOPSLB = DB::table('TBL_TRN_PRPB02_PSLB')                    
                ->where('SPIID_REF','=',$id)
                ->select('*')
                ->orderBy('PSLB_SPIID','ASC')
                ->get()->toArray();
                $objCount5 = count($objSOPSLB);

                $objSOTDS = DB::table('TBL_TRN_PRPB02_TDS')                    
                ->where('SPIID_REF','=',$id)
                ->select('*')
                ->orderBy('SPI_DTSID','ASC')
                ->get()->toArray();
                $objCount8 = count($objSOTDS);

               
                $objRights = DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                                
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();

                                $TDS_APPLICABLE = 0;
                                $VENDOR_CODE = '';
                                $VENDOR_NAME = '';

                                if(isset($objSO->VID_REF) && $objSO->VID_REF!=""){
                                    $ObjVendor = DB::select('select VID,VCODE,NAME,TDS_APPLICABLE from TBL_MST_VENDOR where SLID_REF = ? ', [$objSO->VID_REF]);
                                    if(!empty($ObjVendor)){
                                        $VENDOR_CODE = $ObjVendor[0]->VCODE;
                                        $VENDOR_NAME = $ObjVendor[0]->NAME;
                                        $TDS_APPLICABLE =  $ObjVendor[0]->TDS_APPLICABLE==1 ? $ObjVendor[0]->TDS_APPLICABLE : 0 ;
                                    } 
                                }     

                                $TAXSTATE=[];

                                 $objShpAddress=[] ;
                                 $objBillAddress=[];

                            if(isset($objSO->SPOID_REF) && $objSO->SPOID_REF !=""){

                                $objFromSelected = DB::select('SELECT top 1 * FROM TBL_TRN_PROR04_HDR  WHERE  SPOID= ?', [$objSO->SPOID_REF]);
                                //dd($objFromSelected);
                                $sid = $objFromSelected[0]->SHIP_TO;  
                            
                                $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                                            WHERE  SHIPTO= ? AND LID = ? ', [1,$sid]);
                    
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

                            }
                
                                
                $objglcode2 =[];
                if(isset($objSO->DEPID_REF) && $objSO->DEPID_REF !=""){
                    $objglcode2 = DB::table('TBL_MST_DEPARTMENT')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('DEPID','=',$objSO->DEPID_REF)
                    ->select('TBL_MST_DEPARTMENT.*')
                    ->first();
                }

                
                $objglcode = DB::table('TBL_MST_DEPARTMENT')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('STATUS','=',$Status)
                ->select('TBL_MST_DEPARTMENT.*')
                ->get()
                ->toArray();

         
                $objsubglcode =[];
                if(isset($objSO->VID_REF) && $objSO->VID_REF !=""){
                $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
                ->where('BELONGS_TO','=','Vendor')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('SGLID','=',$objSO->VID_REF)    
                ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                ->first();
                }
                
    


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
                
                
                
                $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_SPI")->select('*')
                ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                            {       
                            $query->select('UDFSPIID')->from('TBL_MST_UDFFOR_SPI')
                                            ->where('PARENTID','=',0)
                                            ->where('DEACTIVATED','=',0)
                                            ->where('CYID_REF','=',$CYID_REF);
                                                                  
                })->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF);
                                 
                

                $objUdfSOData2 = DB::table('TBL_MST_UDFFOR_SPI')
                    ->where('PARENTID','=',0)
                    ->where('DEACTIVATED','=',0)
                    ->where('CYID_REF','=',$CYID_REF)
                 
                    ->union($ObjUnionUDF2)
                    ->get()->toArray(); 
        
                
                $objCurrencyconverter = DB::table('TBL_MST_CRCONVERSION')
                ->where('STATUS','=',$Status)
                ->select('TBL_MST_CRCONVERSION.*')
                ->get()
                ->toArray();
        
                               
                $ObjSalesQuotationData= [];
               
                $POBASEDON =  'direct';
                
                $objSQMAT =[];
                
                $objItems=array();                
                
                $objUOM=array();

                $objItemUOMConv = DB::table('TBL_MST_ITEM_UOMCONV')->select('*')
                ->get() ->toArray(); 

                $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
                ->get() ->toArray(); 

                $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')
                ->get() ->toArray(); 

                $POBASEDON =  'direct';
            
         
            //---GET TDS TAB DATA

            $ObjVenorData =[];
            if(isset($objSO->VID_REF) && $objSO->VID_REF !=""){
            $ObjVenorData =  DB::select('SELECT TOP 1 * FROM TBL_MST_VENDOR  WHERE SLID_REF = ? AND  CYID_REF = ? AND BRID_REF = ?  ', [$objSO->VID_REF,$CYID_REF,$BRID_REF]);
            }
           
           
            $HIDS='';
            if(!empty($ObjVenorData)){
                $HIDS = is_null($ObjVenorData[0]->HOLDINGID_REF) ? 0 : $ObjVenorData[0]->HOLDINGID_REF;
            }
           
            $ObjTDSData=[];
            if($HIDS!=''){
                $ObjTDSData =  DB::select("SELECT * FROM TBL_MST_WITHHOLDING WHERE CYID_REF = $CYID_REF AND FYID_REF = $FYID_REF AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 )  AND HOLDINGID IN ($HIDS)  ");
            }
            
            $ObjTDSDataCount = 0;
            if(!empty($ObjTDSData)){
                 $cur_date = Date('Y-m-d');
                foreach ($ObjTDSData as $dindex=>$dataRow){
                    
                    $ObjSection =  DB::select("SELECT SECTION_CODE,SECTION_NAME FROM TBL_MST_SECTION WHERE CYID_REF=$CYID_REF AND SECTIONID = $dataRow->SECTIONID_REF ");
                    $section_name = '';
                    if(!empty($ObjSection)){
                        $section_name =  $ObjSection[0]->SECTION_CODE.'-'.$ObjSection[0]->SECTION_NAME;
                    }

                    $add_row = false;
                    if( is_null($dataRow->APPLICABLE_FRDT) ){
                        $add_row = true;
                    }
                    else if( !is_null($dataRow->APPLICABLE_FRDT) && strtotime($cur_date) >= strtotime($dataRow->APPLICABLE_FRDT) )
                    {
                        $add_row = true;
                    }
                    
                    $ObjTDSData[$dindex]->SECTION_NAME =  $section_name;
                    if($add_row){
                        $ObjTDSData[$dindex]->EXPIRE =  false;
                        $ObjTDSDataCount = $ObjTDSDataCount + 1 ;
                    }else{
                        $ObjTDSData[$dindex]->EXPIRE =  true;
                    }
                }       
            }

            $objlast_DT = NULL;
            if(isset($objSO->SPI_DT) && $objSO->SPI_DT !=""){
                $objlast_DT =$objSO->SPI_DT;  
            }
            
            $AlpsStatus =   $this->AlpsStatus();
            
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

            $objTemplateMaster  =$this->getTemplateMaster("PURCHASE");

            $Template = DB::table('TBL_TRN_PRPB02_ADD_INFO')
            ->where('SPIID_REF','=',$id)
            ->select('TBL_TRN_PRPB02_ADD_INFO.TEMPLATE')
            ->first();

            $ActionStatus   =   "";
            $FormId=$this->form_id;

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
            $checkCompany   =	Helper::checkCompany(Auth::user()->CYID_REF,'alps');




            $objItem = DB::select("SELECT 
            T1.*,T2.ICODE,T2.NAME AS ITEM_NAME,T2.ITEM_SPECI,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T3.UOMCODE,T3.DESCRIPTIONS,T4.STCODE,T4.NAME AS STORE_NAME    
            FROM TBL_TRN_PRPB02_ITEM T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID      
            LEFT JOIN TBL_MST_STORE T4 ON T1.STID_REF=T4.STID      
            WHERE T1.SPIID_REF='$id' ORDER BY T1.SPIID_REF ASC");   

            //dd($objItem); 


            $objothcurrency = $this->GetCurrencyMaster();

            return view('transactions.Purchase.ServicePurchaseInvoice.trnfrm201edit',compact(['objSO','objRights','objCount1',
            'objCount2','objCount3','objCount4','objCount5','objCount8','objSOMAT','objSOCAL','objSOTNC','objSOUDF','objothcurrency',
            'objglcode','objCalculationHeader','objTNCHeader','objglcode2','ObjSalesQuotationData','objsubglcode','AlpsStatus','objCountAttachment',
            'objShpAddress','objBillAddress','objItems','objSQMAT','objUOM','objItemUOMConv','objTNCDetails','objUdfSOData2','objSOPSLB',
            'objCalHeader','objCalDetails','objSOTDS','TAXSTATE','POBASEDON','objlast_DT','TDS_APPLICABLE','VENDOR_CODE','VENDOR_NAME','objData2',
            'ObjTDSData','ObjTDSDataCount','ActionStatus','objTemplateMaster','Template','FormId','TabSetting','objItem','checkCompany']));

            }
     
    }
     
    public function view($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id))
        {
            $objSO = DB::table('TBL_TRN_PRPB02_HDR')
                    ->where('TBL_TRN_PRPB02_HDR.FYID_REF','=',Session::get('FYID_REF'))
                    ->where('TBL_TRN_PRPB02_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('TBL_TRN_PRPB02_HDR.BRID_REF','=',Session::get('BRID_REF'))
                    ->where('TBL_TRN_PRPB02_HDR.SPIID','=',$id)
                    ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_PRPB02_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
                    ->select('TBL_TRN_PRPB02_HDR.*','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
                    ->first();

            if( isset($objSO->STATUS) && strtoupper($objSO->STATUS)=="A"){
               // exit("Sorry, Approved record can not edit.");
            }
                            
            //GET SPO DATA
            
            $objData2 =[];
            if(isset($objSO->SPOID_REF) && $objSO->SPOID_REF !=""){
            $objData2 =     DB::table('TBL_TRN_PROR04_HDR')
                                ->where('FYID_REF','=',Session::get('FYID_REF'))
                                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('BRID_REF','=',Session::get('BRID_REF'))
                                ->where('SPOID','=',$objSO->SPOID_REF)
                                ->select('*')
                                ->first();
            }


            $log_data = [ 
                $id
            ];

            $objSOMAT = DB::select('EXEC sp_get_service_purchase_invoice_material ?', $log_data);

            if(!empty($objSOMAT)){
                foreach($objSOMAT as $key=>$val){
                    if($objSO->DPB ==="1"){
                        $PB_DOCNO =   DB::table('TBL_TRN_PRPB01_HDR')->where('PBID','=',$val->BOE_NO_REF)->select('PB_DOCNO')->first();
                        $objSOMAT[$key]->DOC_CODE = isset($PB_DOCNO) && $PB_DOCNO->PB_DOCNO !=''?$PB_DOCNO->PB_DOCNO:NULL;
                    }
                    else{
                        $objSOMAT[$key]->DOC_CODE =   $val->BOE_NO_REF;
                    }

                    $spo_balance        =   $this->getBalanceAmount($objSO->SPOID_REF,$val->SRVID_REF);
                    $spi_balance        =   $this->getSpiBalanceAmount($objSO->SPOID_REF,$val->SRVID_REF);
                    $spi_balance_edit   =   $this->getSpiEditBalance($val->SPISRVID);
                    $tot_balance        =   ($spo_balance-$spi_balance)+$spi_balance_edit;

                    $objSOMAT[$key]->TOT_BAL_AMT=$tot_balance;
                }

            }

           
            $objCount1 = count($objSOMAT);

            
          
          //DUMP($objSOMAT);
            $objSOTNC = DB::table('TBL_TRN_PRPB02_TNC')                    
                            ->where('SPIID_REF','=',$id)
                            ->select('*')
                            ->orderBy('SPI_TNCID','ASC')
                            ->get()->toArray();
            $objCount2 = count($objSOTNC);

            $objSOUDF = DB::table('TBL_TRN_PRPB02_UDF')                    
                            ->where('SPIID_REF','=',$id)
                            ->select('*')
                            ->orderBy('SPI_UDFID','ASC')
                            ->get()->toArray();

                        
            $objCount3 = count($objSOUDF);

            $objSOCAL = DB::table('TBL_TRN_PRPB02_CAL')                    
                            ->where('SPIID_REF','=',$id)
                            ->select('*')
                            ->orderBy('SPI_CALID','ASC')
                            ->get()->toArray();
            $objCount4 = count($objSOCAL);

            
            $objSOPSLB = DB::table('TBL_TRN_PRPB02_PSLB')                    
            ->where('SPIID_REF','=',$id)
            ->select('*')
            ->orderBy('PSLB_SPIID','ASC')
            ->get()->toArray();
            $objCount5 = count($objSOPSLB);

            $objSOTDS = DB::table('TBL_TRN_PRPB02_TDS')                    
            ->where('SPIID_REF','=',$id)
            ->select('*')
            ->orderBy('SPI_DTSID','ASC')
            ->get()->toArray();
            $objCount8 = count($objSOTDS);

           
            $objRights = DB::table('TBL_MST_USERROLMAP')
                            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                            
                            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                            ->first();

                            $TDS_APPLICABLE = 0;
                            $VENDOR_CODE = '';
                            $VENDOR_NAME = '';

                            if(isset($objSO->VID_REF) && $objSO->VID_REF!=""){
                                $ObjVendor = DB::select('select VID,VCODE,NAME,TDS_APPLICABLE from TBL_MST_VENDOR where SLID_REF = ? ', [$objSO->VID_REF]);
                                if(!empty($ObjVendor)){
                                    $VENDOR_CODE = $ObjVendor[0]->VCODE;
                                    $VENDOR_NAME = $ObjVendor[0]->NAME;
                                    $TDS_APPLICABLE =  $ObjVendor[0]->TDS_APPLICABLE==1 ? $ObjVendor[0]->TDS_APPLICABLE : 0 ;
                                } 
                            }     

                            $TAXSTATE=[];

                             $objShpAddress=[] ;
                             $objBillAddress=[];

                        if(isset($objSO->SPOID_REF) && $objSO->SPOID_REF !=""){

                            $objFromSelected = DB::select('SELECT top 1 * FROM TBL_TRN_PROR04_HDR  WHERE  SPOID= ?', [$objSO->SPOID_REF]);
                            //dd($objFromSelected);
                            $sid = $objFromSelected[0]->SHIP_TO;  
                        
                            $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                                        WHERE  SHIPTO= ? AND LID = ? ', [1,$sid]);
                
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

                        }
            
                            
            $objglcode2 =[];
            if(isset($objSO->DEPID_REF) && $objSO->DEPID_REF !=""){
                $objglcode2 = DB::table('TBL_MST_DEPARTMENT')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('DEPID','=',$objSO->DEPID_REF)
                ->select('TBL_MST_DEPARTMENT.*')
                ->first();
            }

            
            $objglcode = DB::table('TBL_MST_DEPARTMENT')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_DEPARTMENT.*')
            ->get()
            ->toArray();

     
            $objsubglcode =[];
            if(isset($objSO->VID_REF) && $objSO->VID_REF !=""){
            $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
            ->where('BELONGS_TO','=','Vendor')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('SGLID','=',$objSO->VID_REF)    
            ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
            ->first();
            }
            
         


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
            
            
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_SPI")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                        {       
                        $query->select('UDFSPIID')->from('TBL_MST_UDFFOR_SPI')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                              
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                             
            

            $objUdfSOData2 = DB::table('TBL_MST_UDFFOR_SPI')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
             
                ->union($ObjUnionUDF2)
                ->get()->toArray(); 
    
            
            $objCurrencyconverter = DB::table('TBL_MST_CRCONVERSION')
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_CRCONVERSION.*')
            ->get()
            ->toArray();
    
                           
            $ObjSalesQuotationData= [];
           
            $POBASEDON =  'direct';
            
            $objSQMAT =[];
            
            $objItems=array();                
            
            $objUOM=array();

            $objItemUOMConv = DB::table('TBL_MST_ITEM_UOMCONV')->select('*')
            ->get() ->toArray(); 

            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
            ->get() ->toArray(); 

            $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')
            ->get() ->toArray(); 

            $POBASEDON =  'direct';
        
     
        //---GET TDS TAB DATA

        $ObjVenorData =[];
        if(isset($objSO->VID_REF) && $objSO->VID_REF !=""){
        $ObjVenorData =  DB::select('SELECT TOP 1 * FROM TBL_MST_VENDOR  WHERE SLID_REF = ? AND  CYID_REF = ? AND BRID_REF = ?  ', [$objSO->VID_REF,$CYID_REF,$BRID_REF]);
        }
       
       
        $HIDS='';
        if(!empty($ObjVenorData)){
            $HIDS = is_null($ObjVenorData[0]->HOLDINGID_REF) ? 0 : $ObjVenorData[0]->HOLDINGID_REF;
        }
       
        $ObjTDSData=[];
        if($HIDS!=''){
            $ObjTDSData =  DB::select("SELECT * FROM TBL_MST_WITHHOLDING WHERE CYID_REF = $CYID_REF AND FYID_REF = $FYID_REF AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 )  AND HOLDINGID IN ($HIDS)  ");
        }
        
        $ObjTDSDataCount = 0;
        if(!empty($ObjTDSData)){
             $cur_date = Date('Y-m-d');
            foreach ($ObjTDSData as $dindex=>$dataRow){
                
                $ObjSection =  DB::select("SELECT SECTION_CODE,SECTION_NAME FROM TBL_MST_SECTION WHERE CYID_REF=$CYID_REF AND SECTIONID = $dataRow->SECTIONID_REF ");
                $section_name = '';
                if(!empty($ObjSection)){
                    $section_name =  $ObjSection[0]->SECTION_CODE.'-'.$ObjSection[0]->SECTION_NAME;
                }

                $add_row = false;
                if( is_null($dataRow->APPLICABLE_FRDT) ){
                    $add_row = true;
                }
                else if( !is_null($dataRow->APPLICABLE_FRDT) && strtotime($cur_date) >= strtotime($dataRow->APPLICABLE_FRDT) )
                {
                    $add_row = true;
                }
                
                $ObjTDSData[$dindex]->SECTION_NAME =  $section_name;
                if($add_row){
                    $ObjTDSData[$dindex]->EXPIRE =  false;
                    $ObjTDSDataCount = $ObjTDSDataCount + 1 ;
                }else{
                    $ObjTDSData[$dindex]->EXPIRE =  true;
                }
            }       
        }

        $objlast_DT = NULL;
        if(isset($objSO->SPI_DT) && $objSO->SPI_DT !=""){
            $objlast_DT =$objSO->SPI_DT;  
        }
        
        $AlpsStatus =   $this->AlpsStatus();
        
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

        $objTemplateMaster  =$this->getTemplateMaster("PURCHASE");

        $Template = DB::table('TBL_TRN_PRPB02_ADD_INFO')
        ->where('SPIID_REF','=',$id)
        ->select('TBL_TRN_PRPB02_ADD_INFO.TEMPLATE')
        ->first();

        $ActionStatus   =   "disabled";
        $FormId=$this->form_id;

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        $checkCompany   =	Helper::checkCompany(Auth::user()->CYID_REF,'alps');
        
        $objItem = DB::select("SELECT 
        T1.*,T2.ICODE,T2.NAME AS ITEM_NAME,T2.ITEM_SPECI,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T3.UOMCODE,T3.DESCRIPTIONS,T4.STCODE,T4.NAME AS STORE_NAME    
        FROM TBL_TRN_PRPB02_ITEM T1
        LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
        LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID      
        LEFT JOIN TBL_MST_STORE T4 ON T1.STID_REF=T4.STID      
        WHERE T1.SPIID_REF='$id' ORDER BY T1.SPIID_REF ASC");   

        $objothcurrency = $this->GetCurrencyMaster();

        return view('transactions.Purchase.ServicePurchaseInvoice.trnfrm201view',compact(['objSO','objRights','objCount1',
        'objCount2','objCount3','objCount4','objCount5','objCount8','objSOMAT','objSOCAL','objSOTNC','objSOUDF','objCurrencyconverter',
        'objglcode','objCalculationHeader','objTNCHeader','objglcode2','ObjSalesQuotationData','objsubglcode','AlpsStatus','objCountAttachment',
        'objShpAddress','objBillAddress','objItems','objSQMAT','objUOM','objItemUOMConv','objTNCDetails','objUdfSOData2','objSOPSLB',
        'objCalHeader','objCalDetails','objSOTDS','TAXSTATE','POBASEDON','objlast_DT','TDS_APPLICABLE','VENDOR_CODE','VENDOR_NAME','objData2',
        'ObjTDSData','ObjTDSDataCount','ActionStatus','objTemplateMaster','Template','FormId','TabSetting','objItem','checkCompany','objothcurrency']));

        }
 
}

   
   public function update(Request $request){

    $r_count1 = $request['Row_Count1'];
    $r_count2 = $request['Row_Count2'];
    $r_count3 = $request['Row_Count3'];
    $r_count4 = $request['Row_Count4'];
    $r_count5 = $request['Row_Count5'];
    $r_count8 = $request['Row_Count8'];  

    $GROSS_TOTAL    =   0; 
    $NET_TOTAL 		= 	$request['TotalValue'];
    $CGSTAMT        =   0; 
    $SGSTAMT        =   0; 
    $IGSTAMT        =   0; 
    $DISCOUNT       =   0; 
    $OTHER_CHARGES  =   0; 
    $TDS_AMOUNT     =   0; 	
	$FC 			= (isset($request['FC'])!="true" ? 0 : 1);
	$CRID_REF 		= (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
	$CONVFACT 		= (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";

    if(isset($request['rowcountitem'])){
    $r_count9 = count($request['rowcountitem']);
    }else{
    $r_count9 = NULL;
    }


    
    $SPOID_REF = $request['SLID_REF']; //SPOID
    
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
                'SPOID_REF'    => $SPOID_REF,
                'SRVID_REF'    => $request['ITEMID_REF_'.$i],
                'UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                'BILLQTY' => $request['SO_QTY_'.$i],
                'SHORT_QTY' => (!empty($request['SHORT_QTY_'.$i]) ? $request['SHORT_QTY_'.$i] : 0),
                'BILL_RATEPUOM' => $request['RATEPUOM_'.$i],
                'DIS_PER'    => (!empty($request['DISCPER_'.$i])) == 'true' ? $request['DISCPER_'.$i] : 0,
                'DISCOUNT' => (!empty($request['DISCOUNT_AMT_'.$i])) == 'true' ? $request['DISCOUNT_AMT_'.$i] : 0,
                'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                'BOE_NO_REF' => (!empty($request['BOEID_REF_'.$i]) ? $request['BOEID_REF_'.$i] : NULL),
                'ASSESSABLE_VALUE' => (!empty($request['ASSESSABLE_VALUE_'.$i]) ? $request['ASSESSABLE_VALUE_'.$i] : NULL),
            ];
        }
    }
        $wrapped_links["SERVICE"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);

//     dump($request->all());
//    dd($XMLMAT);    
    
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
                    'UDFSPIID_REF'   => $request['UDFSOID_REF_'.$i],
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
                        'ACTUAL'        => (isset($request['calACTUAL_'.$i])!="true" ? 0 : 1) ,
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

        //tds
        for ($i=0; $i<=$r_count8; $i++)
        {
           
            if(isset($request['calTDS_'.$i]) && $request['calTDS_'.$i]=!'')
                {
                    if(isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0==1){
                        $TDS_AMOUNT      += $request['TDS_AMT_'.$i]; 
                        }

                    $reqdata8[$i] = [
                        'TDSID_REF'                 => $request['HOLDINGID_'.$i],                            
                        'ASSESSABLE_VL_TDS' => $request['ASSEVAL_TDS_RATE_'.$i], 
                        'TDS_RATE'          => $request['ACT_TDS_RATE_'.$i],    
                        'ASSESSABLE_VL_SURCHARGE'   => $request['ASSEVAL_SURCHARGE_RAGE_'.$i], 
                        'SURCHARGE_RATE'            => $request['ACT_SURCHARGE_RAGE_'.$i],   
                        'ASSESSABLE_VL_CESS'    => $request['ASSEVAL_CESS_RATE_'.$i], 
                        'CESS_RATE'             => $request['ACT_CESS_RATE_'.$i], 
                        'ASSESSABLE_VL_SPCESS'  => $request['ASSEVAL_SP_CESS_RATE_'.$i], 
                        'SPCESS_RATE'           => $request['ACT_SP_CESS_RATE_'.$i],                            
                    ];
                }
            
        }
        if(isset($reqdata8))
        { 
            $wrapped_links8["TDS"] = $reqdata8;
            $XMLTDS = ArrayToXml::convert($wrapped_links8);
        }
        else
        {
            $XMLTDS = NULL; 
        }  


        for ($i=0; $i<=$r_count9; $i++)
        {
            if(isset($request['A_ITEMID_REF_'.$i]) && !is_null($request['A_ITEMID_REF_'.$i]))
            {
                $reqdata9[$i] = [
                    'ITEMID_REF'    => $request['A_ITEMID_REF_'.$i],
                    'UOMID_REF' => $request['A_UOMID_REF_'.$i],
                    'ITEM_SPECS' => $request['A_ITEMSPECI_'.$i],                              
                    'ITEM_VALUE' => (!empty($request['ITEM_AMOUNT_'.$i]) ? $request['ITEM_AMOUNT_'.$i] : 0),
                    'STID_REF' =>  $request['STRID_REF_'.$i],
                ];
            }
        }

        if(isset($reqdata9))
        { 
            $wrapped_links9["ITEM"] = $reqdata9;
            $XMLITEM = ArrayToXml::convert($wrapped_links9);
        }
        else
        {
            $XMLITEM = NULL; 
        }  




        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $SPI_NO = $request['SONO'];
        $SPI_DT = $request['SODT'];
        $DEPID_REF = $request['GLID_REF']; //DEPT ID
        $VID_REF = $request['VID_REF']; //VENDOR ID


       
        $VENDOR_REF_NO = $request['REFNO'];
        $VENDOR_REF_DT = $request['VENDOR_REF_DT'];

        $CREDITDAYS = (trim($request['CREDITDAYS']) =='' ? 0 : $request['CREDITDAYS'] );

       

        $REMARKS = $request['REMARKS'];
        $TDS_CAL = $request['TDS_APPLICABLE'];

        $GST_N_Avail            =   (isset($request['GST_N_Avail'])!="true" ? 0 : 1);
        $GST_Reverse            =   (isset($request['GST_Reverse'])!="true" ? 0 : 1);
        $EXE_GST                =   (isset($request['EXE_GST'])!="true" ? 0 : 1);
        $Template_Description  =   $request['Template_Description'];
        $TDS                    = (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);
        $BOE                    =   (isset($request['BOE'])!="true" ? 0 : 1);
        $DPB                    =   $request['DPB'];
		$TYPE = $request['TYPE'];

        $log_data = [ 
            $SPI_NO,    $SPI_DT,    $DEPID_REF,     $VID_REF,     $SPOID_REF,     $VENDOR_REF_NO, $VENDOR_REF_DT,
            $CREDITDAYS,$REMARKS,   $TDS_CAL,       $CYID_REF,    $BRID_REF,      $FYID_REF,      $VTID_REF,
            $XMLMAT,    $XMLTNC,    $XMLUDF,        $XMLCAL,        $XMLTDS,      $XMLPSLB,       $USERID, 
            Date('Y-m-d'),          Date('h:i:s.u'),$ACTIONNAME,      $IPADDRESS,
            $GST_N_Avail,$GST_Reverse,$EXE_GST,$Template_Description,$TDS,$BOE,$DPB,$XMLITEM,$TYPE
            ,$FC,$CRID_REF,$CONVFACT,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT
        ];

        // dump($request->all());
      //dd($log_data );

        $sp_result = DB::select('EXEC SP_SPI_UP ?,?,?,?,?,?,?, ?,?,?,?,?,?,?, ?,?,?,?,?,?,?, ?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?', $log_data);   

        //dd($sp_result);
        $contains = Str::contains(strtolower($sp_result[0]->RESULT), 'success');  
           
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $SPI_NO. ' Sucessfully Updated.']);
        
        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
            exit();   
    } //update the data
   
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
                $r_count5 = $request['Row_Count5'];
                $r_count8 = $request['Row_Count8'];  

                $GROSS_TOTAL    =   0; 
                $NET_TOTAL 		= 	$request['TotalValue'];
                $CGSTAMT        =   0; 
                $SGSTAMT        =   0; 
                $IGSTAMT        =   0; 
                $DISCOUNT       =   0; 
                $OTHER_CHARGES  =   0; 
                $TDS_AMOUNT     =   0; 	
                $FC 			= (isset($request['FC'])!="true" ? 0 : 1);
                $CRID_REF 		= (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
                $CONVFACT 		= (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";


                if(isset($request['rowcountitem'])){
                    $r_count9 = count($request['rowcountitem']);
                    }else{
                    $r_count9 = NULL;
                    }
        

                $SPOID_REF = $request['SLID_REF']; //SPOID
                
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
                            'SPOID_REF'    => $SPOID_REF,
                            'SRVID_REF'    => $request['ITEMID_REF_'.$i],
                            'UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                            'BILLQTY' => $request['SO_QTY_'.$i],
                            'SHORT_QTY' => (!empty($request['SHORT_QTY_'.$i]) ? $request['SHORT_QTY_'.$i] : 0),
                            'BILL_RATEPUOM' => $request['RATEPUOM_'.$i],
                            'DIS_PER'    => (!empty($request['DISCPER_'.$i])) == 'true' ? $request['DISCPER_'.$i] : 0,
                            'DISCOUNT' => (!empty($request['DISCOUNT_AMT_'.$i])) == 'true' ? $request['DISCOUNT_AMT_'.$i] : 0,
                            'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                            'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                            'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                            'BOE_NO_REF' => (!empty($request['BOEID_REF_'.$i]) ? $request['BOEID_REF_'.$i] : NULL),
                            'ASSESSABLE_VALUE' => (!empty($request['ASSESSABLE_VALUE_'.$i]) ? $request['ASSESSABLE_VALUE_'.$i] : NULL),
                        ];
                    }
                }
                    $wrapped_links["SERVICE"] = $req_data; 
                    $XMLMAT = ArrayToXml::convert($wrapped_links);

                //dump($request->all());
            // dd($XMLMAT);    
                
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
                                'UDFSPIID_REF'   => $request['UDFSOID_REF_'.$i],
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
                                    'ACTUAL'        => (isset($request['calACTUAL_'.$i])!="true" ? 0 : 1) ,
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

                    //tds
                    for ($i=0; $i<=$r_count8; $i++)
                    {
                    
                        if(isset($request['calTDS_'.$i]) && $request['calTDS_'.$i]=!'')
                            {
                                if(isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0==1){
                                    $TDS_AMOUNT      += $request['TDS_AMT_'.$i]; 
                                    }

                                $reqdata8[$i] = [
                                    'TDSID_REF'                 => $request['HOLDINGID_'.$i],                            
                                    'ASSESSABLE_VL_TDS' => $request['ASSEVAL_TDS_RATE_'.$i], 
                                    'TDS_RATE'          => $request['ACT_TDS_RATE_'.$i],    
                                    'ASSESSABLE_VL_SURCHARGE'   => $request['ASSEVAL_SURCHARGE_RAGE_'.$i], 
                                    'SURCHARGE_RATE'            => $request['ACT_SURCHARGE_RAGE_'.$i],   
                                    'ASSESSABLE_VL_CESS'    => $request['ASSEVAL_CESS_RATE_'.$i], 
                                    'CESS_RATE'             => $request['ACT_CESS_RATE_'.$i], 
                                    'ASSESSABLE_VL_SPCESS'  => $request['ASSEVAL_SP_CESS_RATE_'.$i], 
                                    'SPCESS_RATE'           => $request['ACT_SP_CESS_RATE_'.$i],                            
                                ];
                            }
                        
                    }
                    if(isset($reqdata8))
                    { 
                        $wrapped_links8["TDS"] = $reqdata8;
                        $XMLTDS = ArrayToXml::convert($wrapped_links8);
                    }
                    else
                    {
                        $XMLTDS = NULL; 
                    }  



                    for ($i=0; $i<=$r_count9; $i++)
                    {
                        if(isset($request['A_ITEMID_REF_'.$i]) && !is_null($request['A_ITEMID_REF_'.$i]))
                        {
                            $reqdata9[$i] = [
                                'ITEMID_REF'    => $request['A_ITEMID_REF_'.$i],
                                'UOMID_REF' => $request['A_UOMID_REF_'.$i],
                                'ITEM_SPECS' => $request['A_ITEMSPECI_'.$i],                              
                                'ITEM_VALUE' => (!empty($request['ITEM_AMOUNT_'.$i]) ? $request['ITEM_AMOUNT_'.$i] : 0),
                                'STID_REF' =>  $request['STRID_REF_'.$i],
                            ];
                        }
                    }
        
                    if(isset($reqdata9))
                    { 
                        $wrapped_links9["ITEM"] = $reqdata9;
                        $XMLITEM = ArrayToXml::convert($wrapped_links9);
                    }
                    else
                    {
                        $XMLITEM = NULL; 
                    }  
        


                    $VTID_REF     =   $this->vtid_ref;
                    $VID = 0;
                    $USERID = Auth::user()->USERID;   
                    $ACTIONNAME = $Approvallevel;
                    $IPADDRESS = $request->getClientIp();
                    $CYID_REF = Auth::user()->CYID_REF;
                    $BRID_REF = Session::get('BRID_REF');
                    $FYID_REF = Session::get('FYID_REF');

                    $SPI_NO = $request['SONO'];
                    $SPI_DT = $request['SODT'];
                    $DEPID_REF = $request['GLID_REF']; //DEPT ID
                    $VID_REF = $request['VID_REF']; //VENDOR ID
 
                
                    $VENDOR_REF_NO = $request['REFNO'];
                    $VENDOR_REF_DT = $request['VENDOR_REF_DT'];

                    $CREDITDAYS = (trim($request['CREDITDAYS']) =='' ? 0 : $request['CREDITDAYS'] );

                

                    $REMARKS = $request['REMARKS'];
                    $TDS_CAL = $request['TDS_APPLICABLE'];

                    $GST_N_Avail            =   (isset($request['GST_N_Avail'])!="true" ? 0 : 1);
                    $GST_Reverse            =   (isset($request['GST_Reverse'])!="true" ? 0 : 1);
                    $EXE_GST                =   (isset($request['EXE_GST'])!="true" ? 0 : 1);
                    $Template_Description  =   $request['Template_Description'];
                    $TDS                    = (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);
                    $BOE                    =   (isset($request['BOE'])!="true" ? 0 : 1);
                    $DPB                    =   $request['DPB'];
					$TYPE = $request['TYPE'];

                    $log_data = [ 
                        $SPI_NO,    $SPI_DT,    $DEPID_REF,     $VID_REF,     $SPOID_REF,     $VENDOR_REF_NO, $VENDOR_REF_DT,
                        $CREDITDAYS,$REMARKS,   $TDS_CAL,       $CYID_REF,    $BRID_REF,      $FYID_REF,      $VTID_REF,
                        $XMLMAT,    $XMLTNC,    $XMLUDF,        $XMLCAL,        $XMLTDS,      $XMLPSLB,       $USERID, 
                        Date('Y-m-d'),          Date('h:i:s.u'),$ACTIONNAME,      $IPADDRESS,
                        $GST_N_Avail,$GST_Reverse,$EXE_GST,$Template_Description,$TDS,$BOE,$DPB,$XMLITEM,$TYPE
                        ,$FC,$CRID_REF,$CONVFACT,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT
                    ];
                    
                // dump($request->all());
                  //dd($log_data );

                    $sp_result = DB::select('EXEC SP_SPI_UP ?,?,?,?,?,?,?, ?,?,?,?,?,?,?, ?,?,?,?,?,?,?, ?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?', $log_data);   

                   // dd($sp_result);

                   $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
                   if($contains){
                       return Response::json(['success' =>true,'msg' => $SPI_NO. ' Sucessfully Approved.']);
                   
                   }else{
                       return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
                   }
                    exit();        
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
                $TABLE      =   "TBL_TRN_PRPB02_HDR";
                $FIELD      =   "SPIID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
            // dd($xml);
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_SPI ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
                
            if($sp_result[0]->RESULT=="All records approved"){
    
            return Response::json(['approve' =>true,'msg' => 'Record successfully Approved.']);
    
            }elseif($sp_result[0]->RESULT=="NO RECORD FOR APPROVAL"){
            
                 return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','salesenquiry'=>'norecord']);
            
            }else{
                 return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','salesenquiry'=>'Some Error']);
            }
            
            exit();    
            }

    //Cancel the data
   public function cancel(Request $request){
   
        $id = $request->{0};

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_TRN_PRPB02_HDR";
        $FIELD      =   "SPIID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_PRPB02_SRV',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_PRPB02_TNC',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_PRPB02_UDF',
        ];
        $req_data[3]=[
            'NT'  => 'TBL_TRN_PRPB02_CAL',
        ];
        $req_data[4]=[
            'NT'  => 'TBL_TRN_PRPB02_PSLB',
        ];
        $req_data[5]=[
            'NT'  => 'TBL_TRN_PRPB02_TDS',
        ];
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_SPI  ?,?,?,?, ?,?,?,?, ?,?,?,?', $cancel_data);

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
    
    $image_path         =   "docs/company".$CYID_REF."/ServicePurchaseInvoice";     
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
                
                //$filenamewithextension  = $formData["FILENAME"][$index]->getClientOriginalName();

                $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
                $filesize               =   $uploadedFile ->getSize();  
                $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );

                //$filenametostore        =   $filenamewithextension; 

                $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".$filenamewithextension;  
                
                echo $filenametostore ;

                if ($uploadedFile->isValid()) {

                    if(in_array($extension,$allow_extnesions)){
                        
                        if($filesize < $allow_size){

                            $custfilename = $destinationPath."/".$filenametostore;

                            if (!file_exists($custfilename)) {

                               $uploadedFile->move($destinationPath, $filenametostore);  //upload in dir if not exists
                               $uploaded_data[$index]["FILENAME"] =$filenametostore;
                               $uploaded_data[$index]["LOCATION"] = $image_path."/";
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
        return redirect()->route("transaction",[201,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
    }
 
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
    
      
   try {

         //save data
         $sp_result = DB::select('EXEC SP_TRN_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);

   } catch (\Throwable $th) {
    
       return redirect()->route("transaction",[201,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("transaction",[201,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("transaction",[201,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("transaction",[201,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

    public function checkso(Request $request){

        // dd($request->LABEL_0);
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $SONO = $request->SONO;
        
        $objSO = DB::table('TBL_TRN_PRPB02_HDR')
        ->where('TBL_TRN_PRPB02_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_PRPB02_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_PRPB02_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_PRPB02_HDR.SPI_NO','=',$SONO)
        ->select('TBL_TRN_PRPB02_HDR.SPIID')
        ->first();
        
        if($objSO){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate SPI NO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }

    public function getSPONO(Request $request){

        $Status         =   "A";
        $id             =   $request['id'];
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $SP_PARAMETERS  =   [$CYID_REF,$BRID_REF,$FYID_REF,$id];
        $SPIID          =   isset($request['SPIID'])?$request['SPIID']:0;  
        $ObjData        =   DB::select("SELECT * FROM TBL_TRN_PROR04_HDR WHERE  CYID_REF = '$CYID_REF' AND BRID_REF ='$BRID_REF' AND STATUS ='A'");
       
        $objNewSPONO    =   [];

        if(!empty($ObjData)){
            
            $tempObjData = $ObjData;
            foreach ($ObjData as $index=>$dataRow){

                $ObjSPOItems        =   DB::select("select * from TBL_TRN_PROR04_MAT where SPOID_REF=$dataRow->SPOID");

                $addRecord          =   [];
                $BalanceAmount      =   0;
                $SpiBalanceAmount   =   0;

                foreach ($ObjSPOItems as $index2=>$dataRow2){

                    $BalanceAmount      =   $BalanceAmount+$this->getBalanceAmount($dataRow2->SPOID_REF,$dataRow2->SERVICECODE);
                    $SpiBalanceAmount   =   $SpiBalanceAmount+$this->getSpiBalanceAmount($dataRow2->SPOID_REF,$dataRow2->SERVICECODE);

                    /*
                    $ObjSavedQty =   DB::table('TBL_TRN_PRPB02_SRV')
                        ->where('TBL_TRN_PRPB02_SRV.SPOID_REF','=',$dataRow->SPOID)
                        ->where('TBL_TRN_PRPB02_SRV.SRVID_REF','=',$dataRow2->SERVICECODE)
                        ->where('TBL_TRN_PRPB02_SRV.UOMID_REF','=',$dataRow2->UOMID_REF)
                        ->where('TBL_TRN_PRPB02_HDR.STATUS','<>','C') 
                        ->leftJoin('TBL_TRN_PRPB02_HDR',   'TBL_TRN_PRPB02_HDR.SPIID','=',   'TBL_TRN_PRPB02_SRV.SPIID_REF')
                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PRPB02_SRV.BILL_QTY),0) AS BILLQTY'))                       
                        ->get();

                    $Total_BILLQTY = $ObjSavedQty[0]->BILLQTY;
                            
                    $consQty = 0;
                    if($SPIID>0){
                        $ObjConsumedQty =   DB::table('TBL_TRN_PRPB02_SRV')                                    
                        ->where('TBL_TRN_PRPB02_SRV.SPIID_REF','=',$SPIID)
                        ->where('TBL_TRN_PRPB02_SRV.SPOID_REF','=',$dataRow->SPOID)
                        ->where('TBL_TRN_PRPB02_SRV.SRVID_REF','=',$dataRow2->SERVICECODE)
                        ->where('TBL_TRN_PRPB02_SRV.UOMID_REF','=',$dataRow2->UOMID_REF)
                        ->where('TBL_TRN_PRPB02_HDR.STATUS','<>','C') 
                        ->leftJoin('TBL_TRN_PRPB02_HDR',   'TBL_TRN_PRPB02_HDR.SPIID','=',   'TBL_TRN_PRPB02_SRV.SPIID_REF')
                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PRPB02_SRV.BILL_QTY),0) AS CONSUMED_BILLQTY'))
                        ->get();
                        $consQty = $ObjConsumedQty[0]->CONSUMED_BILLQTY;                            
                    }

                    $TOTAL_QTY = number_format( floatval($Total_BILLQTY) - floatval($consQty), 3,".","" );
                    if(floatval($dataRow2->SPO_QTY)>floatval($TOTAL_QTY)){
                        $addRecord[]=true;
                    }else
                    {
                        $addRecord[]=false;
                    }

                    */     
                }
               
                //if(in_array('true',$addRecord)){
                if($BalanceAmount > $SpiBalanceAmount){
                    $ObjVendor = [];
                    $ObjVendor = DB::select('select SLID_REF,VCODE,NAME,TDS_APPLICABLE from TBL_MST_VENDOR where SLID_REF = ? ', [$dataRow->VID_REF]);
                    if(!empty($ObjVendor)){
                        $ObjData[$index]->VCODE = $ObjVendor[0]->VCODE;
                        $ObjData[$index]->VNAME = $ObjVendor[0]->NAME;
                        $ObjData[$index]->VTDS_APPLICABLE =  isset($ObjVendor[0]->TDS_APPLICABLE) ? $ObjVendor[0]->TDS_APPLICABLE : 0 ;
                    }  
                   
                    $taxstate='';
                    $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  WHERE  SHIPTO= ? AND LID = ? ', [1,$dataRow->SHIP_TO]);
                    $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  WHERE BRID= ? ', [$BRID_REF]);
                    if($ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF)
                    {
                        $taxstate = 'WithinState';
                    }
                    else
                    {
                        $taxstate = 'OutofState';
                    }
                    $ObjData[$index]->taxstate =  $taxstate;
                   
                    $objNewSPONO[$index]=$ObjData[$index];                    
                } 
                
            }            
        }
      
        if(!empty($objNewSPONO)){
            foreach ($objNewSPONO as $index3=>$dataRow3){
                $CREDIT_DAYS = ( is_null($dataRow3->CREDIT_DAYS) ||  trim($dataRow3->CREDIT_DAYS) )=='' ? 0 : $dataRow3->CREDIT_DAYS;
                $row = '';
                $row = $row.'<tr >
                <td style="width:10%;text-align:center;"> <input type="checkbox" name="SELECT_SLID_REF[]" id="subgl_'.$dataRow3->SPOID .'"  class="clssubgl" value="'.$dataRow3->SPOID.'" ></td>
                <td style="width:18%;">'.$dataRow3->SPO_NO;
                $row = $row.'<input type="hidden" id="txtsubgl_'.$dataRow3->SPOID.'" data-desc="'.$dataRow3->SPO_NO.'" data-taxstate="'.$dataRow3->taxstate.'"  data-creditdays="'.$CREDIT_DAYS .'" 
                data-vendorrefno="'.$dataRow3->VENDOR_REFNO.'" data-vendorrefdt="'.$dataRow3->VENDOR_REFDT.'"
                data-tdsapplicable="'.$dataRow3->VTDS_APPLICABLE .'"  data-vendordesc="'.$dataRow3->VCODE.' - '.$dataRow3->VNAME. '" data-vendorid='.$dataRow3->VID_REF.'  value="'.$dataRow3->SPOID.'"/></td>';
                $row = $row.'<td style="width:18%;">'.$dataRow3->SPO_DT.'</td>';
                $row = $row.'<td style="width:18%;">'.$dataRow3->VCODE.'</td>';
                $row = $row.'<td style="width:18%;">'.$dataRow3->VNAME.'</td>';
                $row = $row.'<td style="width:18%;">'.$dataRow3->REMARKS.'</td>';
                $row = $row.'</tr>';
                echo $row;
            }
        }
        else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();
    }
   
    
    /*
    public function getSPONO(Request $request){

        $Status         =   "A";
        $id             =   $request['id'];
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $SP_PARAMETERS  =   [$CYID_REF,$BRID_REF,$FYID_REF,$id];
        $SPIID          =   isset($request['SPIID'])?$request['SPIID']:0;
        $ObjData        =   DB::select('SELECT * FROM TBL_TRN_PROR04_HDR  WHERE  CYID_REF = ? AND BRID_REF = ? AND FYID_REF = ? AND STATUS = ?', [$CYID_REF, $BRID_REF, $FYID_REF,  'A' ]);
    
        $objNewSPONO    =   [];

        if(!empty($ObjData)){
            
            $tempObjData = $ObjData;
            foreach ($ObjData as $index=>$dataRow){

                $ObjSPOItems    =   DB::select("select * from TBL_TRN_PROR04_MAT where SPOID_REF=$dataRow->SPOID");
                $addRecord      =   [];
                foreach ($ObjSPOItems as $index2=>$dataRow2){
                    
                    $ObjSavedRate =   DB::table('TBL_TRN_PRPB02_SRV')
                        ->where('TBL_TRN_PRPB02_SRV.SPOID_REF','=',$dataRow->SPOID)
                        ->where('TBL_TRN_PRPB02_SRV.SRVID_REF','=',$dataRow2->SERVICECODE)
                        ->where('TBL_TRN_PRPB02_SRV.UOMID_REF','=',$dataRow2->UOMID_REF)
                        ->where('TBL_TRN_PRPB02_HDR.STATUS','<>','C') 
                        ->leftJoin('TBL_TRN_PRPB02_HDR',   'TBL_TRN_PRPB02_HDR.SPIID','=',   'TBL_TRN_PRPB02_SRV.SPIID_REF')
                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PRPB02_SRV.BILL_RATEPUOM),0) AS BILL_RATE'))                       
                        ->get();

                    $Total_BILL_RATE = $ObjSavedRate[0]->BILL_RATE;
                            
                    $consRate = 0;
                    if($SPIID>0){
                        $ObjConsumedRate =   DB::table('TBL_TRN_PRPB02_SRV')                                    
                        ->where('TBL_TRN_PRPB02_SRV.SPIID_REF','=',$SPIID)
                        ->where('TBL_TRN_PRPB02_SRV.SPOID_REF','=',$dataRow->SPOID)
                        ->where('TBL_TRN_PRPB02_SRV.SRVID_REF','=',$dataRow2->SERVICECODE)
                        ->where('TBL_TRN_PRPB02_SRV.UOMID_REF','=',$dataRow2->UOMID_REF)
                        ->where('TBL_TRN_PRPB02_HDR.STATUS','<>','C') 
                        ->leftJoin('TBL_TRN_PRPB02_HDR',   'TBL_TRN_PRPB02_HDR.SPIID','=',   'TBL_TRN_PRPB02_SRV.SPIID_REF')
                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PRPB02_SRV.BILL_RATEPUOM),0) AS CONSUMED_BILL_RATE'))
                        ->get();
                        $consRate = $ObjConsumedRate[0]->CONSUMED_BILL_RATE;                            
                    }
                                                
                        
                    $TOTAL_RATE = number_format( floatval($Total_BILL_RATE) - floatval($consRate), 3,".","" );
                    if(floatval($dataRow2->SPO_RATE)>floatval($TOTAL_RATE)){
                        $addRecord[]=true;
                    }else
                    {
                        $addRecord[]=false;
                    }                           
                       
                }
                //dump($addRecord);
                if(in_array('true',$addRecord)){
                    //=======================
                    $ObjVendor = [];
                    $ObjVendor = DB::select('select SLID_REF,VCODE,NAME,TDS_APPLICABLE from TBL_MST_VENDOR where SLID_REF = ? ', [$dataRow->VID_REF]);
                    if(!empty($ObjVendor)){
                        $ObjData[$index]->VCODE = $ObjVendor[0]->VCODE;
                        $ObjData[$index]->VNAME = $ObjVendor[0]->NAME;
                        $ObjData[$index]->VTDS_APPLICABLE =  isset($ObjVendor[0]->TDS_APPLICABLE) ? $ObjVendor[0]->TDS_APPLICABLE : 0 ;
                    }  
                    //----
                    $taxstate='';
                    $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  WHERE  SHIPTO= ? AND LID = ? ', [1,$dataRow->SHIP_TO]);
                    $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  WHERE BRID= ? ', [$BRID_REF]);
                    if($ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF)
                    {
                        $taxstate = 'WithinState';
                    }
                    else
                    {
                        $taxstate = 'OutofState';
                    }
                    $ObjData[$index]->taxstate =  $taxstate;
                    //=======================
                    //$objNewSPONO[$index]=$dataRow;
                    $objNewSPONO[$index]=$ObjData[$index];                    
                } 
                //--
            }            
        }

        if(!empty($objNewSPONO)){
            foreach ($objNewSPONO as $index3=>$dataRow3){
                $CREDIT_DAYS = ( is_null($dataRow3->CREDIT_DAYS) ||  trim($dataRow3->CREDIT_DAYS) )=='' ? 0 : $dataRow3->CREDIT_DAYS;
                // $TDS_APPLICABLE = ( is_null($dataRow->TDS_APPLICABLE) ||  trim($dataRow->TDS_APPLICABLE) )=='' ? 0 : $dataRow->TDS_APPLICABLE;
                $row = '';
                $row = $row.'<tr >
                <td style="width:10%;text-align:center;"> <input type="checkbox" name="SELECT_SLID_REF[]" id="subgl_'.$dataRow3->SPOID .'"  class="clssubgl" value="'.$dataRow3->SPOID.'" ></td>
                <td style="width:18%;">'.$dataRow3->SPO_NO;
                $row = $row.'<input type="hidden" id="txtsubgl_'.$dataRow3->SPOID.'" data-desc="'.$dataRow3->SPO_NO.'" data-taxstate="'.$dataRow3->taxstate.'"  data-creditdays="'.$CREDIT_DAYS .'" 
                data-vendorrefno="'.$dataRow3->VENDOR_REFNO.'" data-vendorrefdt="'.$dataRow3->VENDOR_REFDT.'"
                data-tdsapplicable="'.$dataRow3->VTDS_APPLICABLE .'"  data-vendordesc="'.$dataRow3->VCODE.' - '.$dataRow3->VNAME. '" data-vendorid='.$dataRow3->VID_REF.' value="'.$dataRow3->SPOID.'"/></td>';
                $row = $row.'<td style="width:18%;">'.$dataRow3->SPO_DT.'</td>';
                $row = $row.'<td style="width:18%;">'.$dataRow3->VCODE.'</td>';
                $row = $row.'<td style="width:18%;">'.$dataRow3->VNAME.'</td>';
                $row = $row.'<td style="width:18%;">'.$dataRow3->REMARKS.'</td>';
                $row = $row.'</tr>';
                echo $row;
            }
        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();

    }*/

    public function AlpsStatus(){
        
        $COMPANY_NAME   =   DB::table('TBL_MST_COMPANY')->where('STATUS','=','A')->where('CYID','=',Auth::user()->CYID_REF)->select('TBL_MST_COMPANY.NAME')->first()->NAME;
        //$COMPANY_NAME='ALPS';
        $disabled       =   strpos($COMPANY_NAME,"ALPS")!== false?'disabled':'';
        $hidden         =   strpos($COMPANY_NAME,"ALPS")!== false?'':'hidden';
        $colspan        =   strpos($COMPANY_NAME,"ALPS")!== false?'7':'4';

        return  $ALPS_STATUS=array(
            'hidden'=>$hidden,
            'disabled'=>$disabled,
            'colspan'=>$colspan
        );

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

    public function getTemplateMaster($VOUCHER_TYPE){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
            
        $objTemplateMaster  =   DB::select('SELECT TEMPLATEID, TEMPLATE_NAME, TEMPLATE,INDATE FROM TBL_MST_TEMPLATE  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ? AND TEMPLATE_FOR=?
        order by TEMPLATEID ASC', [$CYID_REF, $BRID_REF, 'A',$VOUCHER_TYPE ]);

        return $objTemplateMaster;

    }

    public function get_BOE(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $fieldid    =   $request['fieldid'];
        $class_name =   $request['class_name'];
        $DPB        =   $request['DPB'];

        if($DPB ==='1'){
            $ObjData1   =   DB::select("SELECT DISTINCT PBID AS DOC_ID,PB_DOCNO AS BOE_NO , PB_DOCDT AS BOE_DT FROM TBL_TRN_PRPB01_HDR
                            WHERE  CYID_REF=$CYID_REF AND BRID_REF=$BRID_REF AND STATUS='A'
                            AND (PB_DOCNO  IS NOT NULL OR PB_DOCNO <> '')");
        }
        else{
            $ObjData1   =   DB::select("SELECT DISTINCT  BOE_NO AS DOC_ID,BOE_NO , BOE_DT FROM TBL_TRN_PII_HDR
            WHERE  CYID_REF=$CYID_REF AND BRID_REF=$BRID_REF AND STATUS='A'
            AND (BOE_NO  IS NOT NULL OR BOE_NO <> '')");
        }
                   
        if(!empty($ObjData1)){
           foreach ($ObjData1 as $index=>$dataRow){
   
               $row            =   '';
               $row = $row.'<tr >
               <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="socode_'.$dataRow->DOC_ID .'"  class="'.$class_name.'" value="'.$dataRow->DOC_ID.'" ></td>
               <td class="ROW2">'.$dataRow->BOE_NO;
               $row = $row.'<input type="hidden" id="txtsocode_'.$dataRow->DOC_ID.'" data-desc="'.$dataRow->BOE_NO.'" data-desc1="'.$dataRow->BOE_DT.'"  value="'.$dataRow->DOC_ID.'"/></td>
               <td class="ROW3" >'.$dataRow->BOE_DT.'</td></tr>';
               echo $row;
               
           }
   
        }else{
            echo '<tr><td>Record not found.</td></tr>';
        }
        exit();   
    }


    public function getTotalSpoAmount(Request $request){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $SPOID_REF      =   $request['SPOID_REF'];
        $TotalValue     =   $request['TotalValue'] !=""?floatval($request['TotalValue']):0;
       
        $TOTAL_MAT_AMT  =   $this->getTotalMaterialAmount($SPOID_REF);
        // $TOTAL_CAL_AMT  =   $this->getTotalCalculationAmount($SPOID_REF);
        // $TOTAL_TDS_AMT  =   $this->getTotalTdsAmount($SPOID_REF);
        // $TOTAL_AMOUNT   =   ($TOTAL_MAT_AMT+$TOTAL_CAL_AMT)-$TOTAL_TDS_AMT;

        $TOTAL_AMOUNT   =   $TOTAL_MAT_AMT;


        if($TOTAL_AMOUNT >= $TotalValue){
            echo '1';
        }
        else{
            echo '';
        }

        exit();
    }

    function getTotalMaterialAmount($SPOID_REF){

        $TOTAL_MAT_AMOUNT   =   0;
        $data               =   DB::select("SELECT * FROM TBL_TRN_PROR04_MAT	WHERE SPOID_REF	='$SPOID_REF'");

        foreach($data as $key=>$val){
            $SPO_QTY        =   $val->SPO_QTY !=""?floatval($val->SPO_QTY):0;
            $SPO_RATE       =   $val->SPO_RATE !=""?floatval($val->SPO_RATE):0;
            $DISCOUNT_PER   =   $val->DISCOUNT_PER !=""?floatval($val->DISCOUNT_PER):0;
            $DIS_AMT        =   $val->DIS_AMT !=""?floatval($val->DIS_AMT):0;
            $IGST           =   $val->IGST !=""?floatval($val->IGST):0;
            $CGST           =   $val->CGST !=""?floatval($val->CGST):0;
            $SGST           =   $val->SGST !=""?floatval($val->SGST):0;

            $TOTAL_AMOUNT   =   $SPO_QTY*$SPO_RATE;
            
            if($DISCOUNT_PER > 0){
                $TOTAL_DISCOUNT   =   ($TOTAL_AMOUNT*$DISCOUNT_PER)/100;
            }
            else if($DIS_AMT > 0){
                $TOTAL_DISCOUNT   =   $DIS_AMT;
            }
            else{
                $TOTAL_DISCOUNT   =   0;
            }

            $TOTAL_AMOUNT       =   $TOTAL_AMOUNT-$TOTAL_DISCOUNT;

            // $IGST_AMOUNT        =   ($TOTAL_AMOUNT*$IGST)/100;
            // $CGST_AMOUNT        =   ($TOTAL_AMOUNT*$CGST)/100;
            // $SGST_AMOUNT        =   ($TOTAL_AMOUNT*$SGST)/100;
            // $TOTAL_TAX_AMOUNT   =   ($IGST_AMOUNT+$CGST_AMOUNT+$SGST_AMOUNT);
            // $TOTAL_AMOUNT       =   $TOTAL_AMOUNT+$TOTAL_TAX_AMOUNT;

            $TOTAL_MAT_AMOUNT   =   $TOTAL_MAT_AMOUNT+$TOTAL_AMOUNT;
        }

        return $TOTAL_MAT_AMOUNT;
    }

    function getTotalCalculationAmount($SPOID_REF){

        $TOTAL_CAL_AMOUNT   =   0;
        $data               =   DB::select("SELECT * FROM TBL_TRN_PROR04_CAL	WHERE SPOID_REF	='$SPOID_REF'");

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

        return $TOTAL_CAL_AMOUNT;
    }

    function getTotalTdsAmount($SPOID_REF){

        $TOTAL_TDS_AMOUNT   =   0;
        $data               =   DB::select("SELECT * FROM TBL_TRN_PROR04_TDS	WHERE SPOID_REF	='$SPOID_REF'");

        foreach($data as $key=>$val){

            $ASSESSABLE_VL_TDS  =   $val->ASSESSABLE_VL_TDS !=""?floatval($val->ASSESSABLE_VL_TDS):0;
            $TDS_RATE           =   $val->TDS_RATE !=""?floatval($val->TDS_RATE):0;
            $TOTAL_AMOUNT       =   ($ASSESSABLE_VL_TDS*$TDS_RATE)/100;
            $TOTAL_TDS_AMOUNT   =   $TOTAL_TDS_AMOUNT+$TOTAL_AMOUNT;
        }

        return $TOTAL_TDS_AMOUNT;
    }

    function getBalanceAmount($SPOID_REF,$SERVICECODE){

        $TOTAL_MAT_AMOUNT   =   0;
        $data               =   DB::select("SELECT * FROM TBL_TRN_PROR04_MAT	WHERE SPOID_REF	='$SPOID_REF' AND SERVICECODE='$SERVICECODE' ");
        
        foreach($data as $key=>$val){
            $SPO_QTY        =   $val->SPO_QTY !=""?floatval($val->SPO_QTY):0;
            $SPO_RATE       =   $val->SPO_RATE !=""?floatval($val->SPO_RATE):0;
            $DISCOUNT_PER   =   $val->DISCOUNT_PER !=""?floatval($val->DISCOUNT_PER):0;
            $DIS_AMT        =   $val->DIS_AMT !=""?floatval($val->DIS_AMT):0;
            $IGST           =   $val->IGST !=""?floatval($val->IGST):0;
            $CGST           =   $val->CGST !=""?floatval($val->CGST):0;
            $SGST           =   $val->SGST !=""?floatval($val->SGST):0;

            $TOTAL_AMOUNT   =   $SPO_QTY*$SPO_RATE;
            
            if($DISCOUNT_PER > 0){
                $TOTAL_DISCOUNT   =   ($TOTAL_AMOUNT*$DISCOUNT_PER)/100;
            }
            else if($DIS_AMT > 0){
                $TOTAL_DISCOUNT   =   $DIS_AMT;
            }
            else{
                $TOTAL_DISCOUNT   =   0;
            }

            $TOTAL_AMOUNT       =   $TOTAL_AMOUNT-$TOTAL_DISCOUNT;

            // $IGST_AMOUNT        =   ($TOTAL_AMOUNT*$IGST)/100;
            // $CGST_AMOUNT        =   ($TOTAL_AMOUNT*$CGST)/100;
            // $SGST_AMOUNT        =   ($TOTAL_AMOUNT*$SGST)/100;
            // $TOTAL_TAX_AMOUNT   =   ($IGST_AMOUNT+$CGST_AMOUNT+$SGST_AMOUNT);
            // $TOTAL_AMOUNT       =   $TOTAL_AMOUNT+$TOTAL_TAX_AMOUNT;

            $TOTAL_MAT_AMOUNT   =   $TOTAL_MAT_AMOUNT+$TOTAL_AMOUNT;
        }

        return $TOTAL_MAT_AMOUNT;
    }

    
    function getSpiBalanceAmount($SPOID_REF,$SERVICECODE){

        $TOTAL_MAT_AMOUNT   =   0;
        $data               =   DB::select("SELECT * FROM TBL_TRN_PRPB02_HDR T1
                                INNER JOIN TBL_TRN_PRPB02_SRV T2 ON T1.SPIID=SPIID_REF
                                WHERE T1.STATUS !='C' AND T2.SPOID_REF='$SPOID_REF' AND T2.SRVID_REF='$SERVICECODE'
                                ");

            

        foreach($data as $key=>$val){
            $BILL_QTY       =   $val->BILL_QTY !=""?floatval($val->BILL_QTY):0;
            $BILL_RATE      =   $val->BILL_RATEPUOM !=""?floatval($val->BILL_RATEPUOM):0;
            $DISCOUNT_PER   =   $val->DIS_PER !=""?floatval($val->DIS_PER):0;
            $DIS_AMT        =   $val->DISCOUNT !=""?floatval($val->DISCOUNT):0;
            $IGST           =   $val->IGST !=""?floatval($val->IGST):0;
            $CGST           =   $val->CGST !=""?floatval($val->CGST):0;
            $SGST           =   $val->SGST !=""?floatval($val->SGST):0;

            $TOTAL_AMOUNT   =   $BILL_QTY*$BILL_RATE;
            
            if($DISCOUNT_PER > 0){
                $TOTAL_DISCOUNT   =   ($TOTAL_AMOUNT*$DISCOUNT_PER)/100;
            }
            else if($DIS_AMT > 0){
                $TOTAL_DISCOUNT   =   $DIS_AMT;
            }
            else{
                $TOTAL_DISCOUNT   =   0;
            }

            $TOTAL_AMOUNT       =   $TOTAL_AMOUNT-$TOTAL_DISCOUNT;

            // $IGST_AMOUNT        =   ($TOTAL_AMOUNT*$IGST)/100;
            // $CGST_AMOUNT        =   ($TOTAL_AMOUNT*$CGST)/100;
            // $SGST_AMOUNT        =   ($TOTAL_AMOUNT*$SGST)/100;
            // $TOTAL_TAX_AMOUNT   =   ($IGST_AMOUNT+$CGST_AMOUNT+$SGST_AMOUNT);
            // $TOTAL_AMOUNT       =   $TOTAL_AMOUNT+$TOTAL_TAX_AMOUNT;

            $TOTAL_MAT_AMOUNT   =   $TOTAL_MAT_AMOUNT+$TOTAL_AMOUNT;
        }

        return $TOTAL_MAT_AMOUNT;
    }

    function getSpiEditBalance($SPISRVID){

        $TOTAL_MAT_AMOUNT   =   0;
        $data               =   DB::select("SELECT * FROM TBL_TRN_PRPB02_SRV WHERE SPISRVID='$SPISRVID'");

        foreach($data as $key=>$val){
            $BILL_QTY       =   $val->BILL_QTY !=""?floatval($val->BILL_QTY):0;
            $BILL_RATE      =   $val->BILL_RATEPUOM !=""?floatval($val->BILL_RATEPUOM):0;
            $DISCOUNT_PER   =   $val->DIS_PER !=""?floatval($val->DIS_PER):0;
            $DIS_AMT        =   $val->DISCOUNT !=""?floatval($val->DISCOUNT):0;
            $IGST           =   $val->IGST !=""?floatval($val->IGST):0;
            $CGST           =   $val->CGST !=""?floatval($val->CGST):0;
            $SGST           =   $val->SGST !=""?floatval($val->SGST):0;

            $TOTAL_AMOUNT   =   $BILL_QTY*$BILL_RATE;
            
            if($DISCOUNT_PER > 0){
                $TOTAL_DISCOUNT   =   ($TOTAL_AMOUNT*$DISCOUNT_PER)/100;
            }
            else if($DIS_AMT > 0){
                $TOTAL_DISCOUNT   =   $DIS_AMT;
            }
            else{
                $TOTAL_DISCOUNT   =   0;
            }

            $TOTAL_AMOUNT       =   $TOTAL_AMOUNT-$TOTAL_DISCOUNT;

            // $IGST_AMOUNT        =   ($TOTAL_AMOUNT*$IGST)/100;
            // $CGST_AMOUNT        =   ($TOTAL_AMOUNT*$CGST)/100;
            // $SGST_AMOUNT        =   ($TOTAL_AMOUNT*$SGST)/100;
            // $TOTAL_TAX_AMOUNT   =   ($IGST_AMOUNT+$CGST_AMOUNT+$SGST_AMOUNT);
            // $TOTAL_AMOUNT       =   $TOTAL_AMOUNT+$TOTAL_TAX_AMOUNT;

            $TOTAL_MAT_AMOUNT   =   $TOTAL_MAT_AMOUNT+$TOTAL_AMOUNT;
        }

        return $TOTAL_MAT_AMOUNT;
    }

    
    

    
    

        public function getItemDetails_All(Request $request){

            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');
            $FYID_REF   =   Session::get('FYID_REF');
    
            $AlpsStatus =   $this->AlpsStatus();
    
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
            
            $ObjItem = DB::select('EXEC sp_get_items_popup_enquiry_BATCH ?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);
                    
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
    
                    $row.=' <tr id="item_'.$ITEMID.'" class="clsitemid">
                            <td  style="width:8%; text-align: center;"><input type="checkbox" id="A_chkId'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1"   ></td>
                            <td style="width:10%;">'.$ICODE.'<input type="hidden" id="txtitem_'.$ITEMID.'" data-desc="'.$ICODE.'" value="'.$ITEMID.'"/></td>
                            <td style="width:10%;" id="itemname_'.$ITEMID.'" >'.$NAME.'<input type="hidden" id="txtitemname_'.$ITEMID.'" data-desc="'.$ITEM_SPECI.'" value="'.$NAME.'"/></td>
                            <td style="width:8%;" id="itemuom_'.$ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ITEMID.'" data-desc="'.$Alt_UOM.'" data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'"  value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>
                            <td style="width:8%;" id="uomqty_'.$ITEMID.'" ><input type="hidden" id="txtuomqty_'.$ITEMID.'" data-desc="'.$TOQTY.'" value="'.$ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>
                            <td style="width:8%;" id="irate_'.$ITEMID.'"><input type="hidden" id="txtirate_'.$ITEMID.'" data-desc="'.$FROMQTY.'" value="'.$STDCOST.'"/>'.$GroupName.'</td>
                            <td style="width:8%;" id="itax_'.$ITEMID.'"><input type="hidden" id="txtitax_'.$ITEMID.'" />'.$Categoryname.'</td>
                            <td style="width:8%;">'.$BusinessUnit.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].'>'.$ALPS_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].'>'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].'>'.$OEM_PART_NO.'</td>
                            <td style="width:8%;">Authorized</td>
                            </tr>'; 
                } 
    
                echo $row;
                                   
            }           
            else{
                echo '<tr><td colspan="12"> Record not found.</td></tr>';
            }
    
            exit();
        }




        public function get_STR(Request $request){

            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');
            $FYID_REF   =   Session::get('FYID_REF');
            $fieldid    =   $request['fieldid'];
            $class_name =   $request['class_name'];
            $DPB        =   $request['DPB'];
    
   
                $ObjData1   =   DB::select("SELECT DISTINCT  STID AS DOC_ID, STCODE AS DOC_CODE, NAME AS DOC_NAME FROM TBL_MST_STORE
                WHERE  CYID_REF=$CYID_REF AND BRID_REF=$BRID_REF AND STATUS='A'
                ");
      
                       
            if(!empty($ObjData1)){
               foreach ($ObjData1 as $index=>$dataRow){
       
                   $row            =   '';
                   $row = $row.'<tr >
                   <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="socode_'.$dataRow->DOC_ID .'"  class="'.$class_name.'" value="'.$dataRow->DOC_ID.'" ></td>
                   <td class="ROW2">'.$dataRow->DOC_CODE;
                   $row = $row.'<input type="hidden" id="txtsocode_'.$dataRow->DOC_ID.'" data-desc="'.$dataRow->DOC_CODE.'-'.$dataRow->DOC_NAME.'" data-desc1="'.$dataRow->DOC_NAME.'"  value="'.$dataRow->DOC_ID.'"/></td>
                   <td class="ROW3" >'.$dataRow->DOC_NAME.'</td></tr>';
                   echo $row;
                   
               }
       
            }else{
                echo '<tr><td>Record not found.</td></tr>';
            }
            exit();   
        }
		
		public function checkDuplicateVendorBillNo(Request $request){

        $REFNO      =   trim($request['REFNO']);
        $VID_REF    =   trim($request['VID_REF']);

        $objData    =   DB::table('TBL_TRN_PRPB02_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('VENDOR_INNO','=',$REFNO)
        ->where('VID_REF','=',$VID_REF)
		->where('STATUS','!=','C')
        ->select('SPIID')->first();
        if($objData){  
            echo 1;
        }else{
            echo 0;
        }
        exit();
    }
	
	public function checkDuplicateVendorBillNoEdit(Request $request){

        $REFNO      =   trim($request['REFNO']);
        $VID_REF    =   trim($request['VID_REF']);
		$SPIID      =   trim($request['SPIID']);

        $objData    =   DB::table('TBL_TRN_PRPB02_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('VENDOR_INNO','=',$REFNO)
		->where('SPIID','!=',$SPIID)
        ->where('VID_REF','=',$VID_REF)
		->where('STATUS','!=','C')
        ->select('SPIID')->first();
        if($objData){  
            echo 1;
        }else{
            echo 0;
        }
        exit();
    }
        
        

}
