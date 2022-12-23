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

class TrnFrm61Controller extends Controller
{
    protected $form_id = 61;
    protected $vtid_ref   = 61;
    protected $view     = "transactions.Purchase.VendorQuotation.trnfrm61";
   
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

        $objDataList	=	DB::select("select hdr.VQID,hdr.VQ_NO,hdr.VQ_DT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.VQID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_VDQT01_HDR hdr
                            on a.VID = hdr.VQID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_SUBLEDGER sl ON hdr.VID_REF = sl.SGLID  
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.VQID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );


        return view($this->view,compact(['REQUEST_DATA','FormId','objRights','objDataList']));
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
        
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $objglcode=array();


        
        
        $d_currency = DB::table('TBL_MST_COMPANY')
        ->where('STATUS','=',$Status)
        ->where('CYID','=',Auth::user()->CYID_REF)
        ->select('TBL_MST_COMPANY.CRID_REF')
        ->first();

        $objcurrency =NULL;
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

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_VDQT01_HDR',
            'HDR_ID'=>'VQID',
            'HDR_DOC_NO'=>'VQ_NO',
            'HDR_DOC_DT'=>'VQ_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

         
        

        $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by TNC_CODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);

        $objCalculationHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by CTCODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);
        
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_VQ_MANAGEMENT")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('VQMID')->from('TBL_MST_UDFFOR_VQ_MANAGEMENT')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                     
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                
                   

        $objUdf  = DB::table('TBL_MST_UDFFOR_VQ_MANAGEMENT')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdf);
    

        $objCurrencyconverter = DB::table('TBL_MST_CRCONVERSION')
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_CRCONVERSION.*')
        ->get()
        ->toArray();

        $objlastVQ_DT = DB::select('SELECT MAX(VQ_DT) VQ_DT FROM TBL_TRN_VDQT01_HDR  
                WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
                [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
                    
        $FormId  = $this->form_id;
        $objCOMPANY = DB::table('TBL_MST_COMPANY')
        ->where('CYID','=',$CYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_COMPANY.*')
        ->first();

        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

    return view($this->view.'add', compact(['AlpsStatus','FormId','objCalculationHeader','objcurrency','objTNCHeader','objothcurrency',
    'objCurrencyconverter','objUdf','objCountUDF','objlastVQ_DT','objCOMPANY','TabSetting','doc_req','docarray']));       
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
    $id = $request['id'];

    $ObjData =  DB::select('SELECT SGLID, SGLCODE, SLNAME, SALIAS FROM TBL_MST_SUBLEDGER  
                WHERE STATUS= ? AND GLID_REF = ? order by SGLCODE ASC', [$Status,$id]);

        if(!empty($ObjData)){

        foreach ($ObjData as $index=>$dataRow){
        
            $row = '';
            $row = $row.'<tr id="subgl_'.$dataRow->SGLID .'"  class="clssubgl"><td width="50%">'.$dataRow->SGLCODE;
            $row = $row.'<input type="hidden" id="txtsubgl_'.$dataRow->SGLID.'" data-desc="'.$dataRow->SGLCODE .' - ';
            $row = $row.$dataRow->SLNAME. '" value="'.$dataRow->SGLID.'"/></td><td>'.$dataRow->SLNAME.'</td></tr>';

            echo $row;
        }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();

    }

   
   
    


    

    public function getRFQ(Request $request){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $VID_REF       =   $request['id'];
        $fieldid    = $request['fieldid'];

        
        $SP_PARAMETERS  = [$CYID_REF,$BRID_REF,$FYID_REF,$VID_REF];

        $ObjData        =  DB::select('EXEC SP_RFQ_GETLIST ?,?,?,?', $SP_PARAMETERS);

      

        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){
                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="rfqcode_'.$dataRow->RFQID .'"  class="clsrfqid" value="'.$dataRow->RFQID.'" ></td>
                <td class="ROW2">'.$dataRow->RFQ_NO;
                $row = $row.'<input type="hidden" id="txtrfqcode_'.$dataRow->RFQID.'" data-desc="'.$dataRow->RFQ_NO.'"  data-descdate="'.$dataRow->RFQ_DT.'"
                value="'.$dataRow->RFQID.'"/></td><td class="ROW3">'.$dataRow->RFQ_DT.'</td></tr>';
                echo $row;
            }
        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
    }

    
    public function getItemDetailsRFQwise(Request $request){
     
        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $taxstate   =   $request['taxstate'];
        $RFQID_REF  =   $request['id'];
        $SLID_REF   =   $request['vendorid'];

        $AlpsStatus =   $this->AlpsStatus();

        $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
        $VENDORID   =   $ObVID->VID;

        $FMODE= $request['mode'];
        $VQID = 0;
        if($FMODE=='edit'){
            $VQID= $request['vqid'];
        }
     
        $StdCost = 0;
      
        $objVendorMst =  DB::select('SELECT TOP 1 VID,VCODE,VGID_REF FROM TBL_MST_VENDOR  WHERE VID = ?', [ $VENDORID ]);  
       
        $VGID = $objVendorMst[0]->VGID_REF;

        $objVPLHDR =  DB::select('SELECT VPLID,VGID_REF,VID_REF FROM TBL_MST_VENDORPRICELIST_HDR  where VGID_REF=? AND STATUS=?', [$VGID, 'A']);   //check vendor group

      
        if(empty($objVPLHDR)){
            $objVPLHDR =  DB::select('SELECT VPLID,VGID_REF,VID_REF FROM TBL_MST_VENDORPRICELIST_HDR  where VID_REF=? AND STATUS=?', [$VENDORID, 'A']); //check vendor
          
        }


            $ObjItem =  DB::select("SELECT * FROM TBL_MST_ITEM T1
                INNER JOIN TBL_TRN_RQFQ01_MAT T2 ON T1.ITEMID=T2.ITEMID_REF
                WHERE T1.CYID_REF = '$CYID_REF' 
                AND ( T1.DEACTIVATED IS NULL OR T1.DEACTIVATED = 0 ) AND T1.STATUS ='$Status' AND T2.PENDING_QTY>'0.000' AND T2.RFQID_REF='$RFQID_REF'");
                  
            $ObjItem2 = $ObjItem;


            

            
            if($FMODE=='edit'){
                foreach ($ObjItem2 as $index=>$dataRow){
                    $ObjOldVQTY =  DB::select("select TOP 1 QUOTATION_QTY from TBL_TRN_VDQT01_MAT where 
                    VQID_REF=$VQID AND RFQNO=$dataRow->RFQID_REF AND ITEMID_REF=$dataRow->ITEMID_REF AND UOMID_REF=$dataRow->UOMID_REF AND PIID_REF=$dataRow->PINO");
                    if(!empty($ObjOldVQTY)){
                       
                        $total_pen_qty = 0;
                        $total_pen_qty = number_format(floatVal($dataRow->PENDING_QTY ) +  floatval($ObjOldVQTY[0]->QUOTATION_QTY), 3, '.', '');
                        $ObjItem[$index]->TOTAL_PENDING = $total_pen_qty;
                    }
                   
                
                }
            }
            
        
        $DIS_PER = 0.0000;
        $DIS_AMT =  0.00000;
      

                if(!empty($ObjItem)){

                   

                    foreach ($ObjItem as $index=>$dataRow){
                    

                       
                      
                        $ObjLIST=[];
                            if(!empty($objVPLHDR)){
                                $ObjLIST =   DB::table('TBL_MST_VENDORPRICELIST_MAT')  
                                    ->select('*')
                                    ->where('VPLID_REF','=',$objVPLHDR[0]->VPLID)
                                    ->where('ITEMID_REF','=',$dataRow->ITEMID)
                                    ->where('UOMID_REF','=',$dataRow->MAIN_UOMID_REF)
                                    ->first();
                            }
                            
                        if(!empty($ObjLIST)){
                                    $ObjInTax = $ObjLIST->GST_IN_LP; 
                                    $RATE = $ObjLIST->LP;  
                                                       
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
                                        $StdCost = $ObjLIST->LP;
                                       
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
                            $RATE = $dataRow->STDCOST;
                        }
                    
                    
                    $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ? AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $dataRow->MAIN_UOMID_REF, 'A' ]);

                    $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ? AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $dataRow->ALT_UOMID_REF, $Status ]);
                    
                    $ObjAltQTY =  DB::select('SELECT TOP 1  * FROM TBL_MST_ITEM_UOMCONV  
                                WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                                [$dataRow->ITEMID,$dataRow->ALT_UOMID_REF ]);

                    $TOQTY =  isset($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 0;
                    $FROMQTY =  isset($ObjAltQTY[0]->FROM_QTY)? $ObjAltQTY[0]->FROM_QTY : 0;


                    


                   if($FMODE=='edit'){
                       $TEMP_QTY =  isset($dataRow->RFQ_QTY)?$dataRow->PENDING_QTY:'0.000'; 
                   }
                   else if($FMODE=='add'){
                        $TEMP_QTY =  isset($dataRow->RFQ_QTY)?$dataRow->PENDING_QTY:'0.000';   

                   }
                    $TOQTY    =    $TEMP_QTY ;
                    $FROMQTY    =    $TEMP_QTY ;

                    

                    $ObjItemGroup =  DB::select('SELECT TOP 1 GROUPCODE, GROUPNAME FROM TBL_MST_ITEMGROUP  
                                WHERE  CYID_REF = ? AND ITEMGID = ?
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                [$CYID_REF, $dataRow->ITEMGID_REF, 'A' ]);

                    $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS FROM TBL_MST_ITEMCATEGORY  
                                WHERE  CYID_REF = ? AND ICID = ?
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                [$CYID_REF, $dataRow->ICID_REF, 'A' ]);



                    $ItemRowData =  DB::select('SELECT top 1 * FROM TBL_MST_ITEM  WHERE ITEMID = ? ', [$dataRow->ITEMID]);

                    if(!is_null($ItemRowData[0]->BUID_REF)){
                        $ObjBusinessUnit =  DB::select('SELECT TOP 1  * FROM TBL_MST_BUSINESSUNIT  
                        WHERE  CYID_REF = ? AND BUID = ?', 
                        [$CYID_REF, $ItemRowData[0]->BUID_REF]);
                    }
                    else
                    {
                        $ObjBusinessUnit = NULL;
                    }
                    
                    $BusinessUnit       =   isset($ObjBusinessUnit) && $ObjBusinessUnit != NULL ? $ObjBusinessUnit[0]->BUCODE.'-'.$ObjBusinessUnit[0]->BUNAME : '';
                    $ALPS_PART_NO       =   $ItemRowData[0]->ALPS_PART_NO;
                    $CUSTOMER_PART_NO   =   $ItemRowData[0]->CUSTOMER_PART_NO;
                    $OEM_PART_NO        =   $ItemRowData[0]->OEM_PART_NO;

                                
                    
                    $CustomId = $RFQID_REF.'-'.$dataRow->PINO.'-'.$dataRow->ITEMID.'-'.$dataRow->MRSID_REF;  
                    

                        $row = '';
                        if($taxstate != "OutofState"){
                            $row = $row.'<tr id="item_'.$CustomId.'"  class="clsitemid"><td  style="width:8%; text-align: center;">
                            <input type="hidden" id="MRSNOitem_'.$CustomId.'"  value="'.$dataRow->MRSID_REF.'"  >
                            <input type="checkbox" id="chkId'.$CustomId.'"  value="'.$CustomId.'" class="js-selectall1"  ></td>
                            <input type="hidden" id="txtrecordId_'.$CustomId.'"  value="'.$CustomId.'"   >';
                            $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                            $row = $row.'<input type="hidden" id="txtitem_'.$CustomId.'" data-desc="'.$dataRow->ICODE.'" data-desc1="'.$TEMP_QTY.'" data-quoqty="'.$TEMP_QTY.'" data-rfqqty="'.$TEMP_QTY.'" data-desc2="'.$RATE.'" data-desc3="'.$DIS_PER.'" data-desc4="'.$DIS_AMT.'" data-desc5="'.$RFQID_REF.'" data-pino="'.$dataRow->PINO.'"  value="'.$dataRow->ITEMID.'"/></td>
                             <td style="width:10%;" id="itemname_'.$CustomId.'" >'.$dataRow->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$CustomId.'" data-desc="'.$dataRow->ITEM_SPECI.'" value="'.$dataRow->NAME.'"/></td>';
                            $row = $row.'<td style="width:8%;" id="itemuom_'.$CustomId.'" ><input type="hidden" id="txtitemuom_'.$CustomId.'" 
                            data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'" 
                            data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" 
                            value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                            $row = $row.'<td style="width:8%;" id="uomqty_'.$CustomId.'" ><input type="hidden" id="txtuomqty_'.$CustomId.'" data-desc="'.$TOQTY.'" value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                            $row = $row.'<td style="width:8%;" id="irate_'.$CustomId.'"><input type="hidden" id="txtirate_'.$CustomId.'" data-desc="'.$RATE.'" value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                            $row = $row.'<td style="width:8%;" id="itax_'.$CustomId.'"><input type="hidden" id="txtitax_'.$CustomId.'" data-desc="'.$Taxid[0].'" value="'.$Taxid[1].'"/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td> 
                            
                            <td style="width:8%;">'.$BusinessUnit.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                            <td style="width:8%;">Authorized</td></tr>';
                            }
                        else
                        {
                            $row = $row.'<tr id="item_'.$CustomId.'"  class="clsitemid"><td  style="width:8%; text-align: center;">
                            <input type="hidden" id="MRSNOitem_'.$CustomId.'"  value="'.$dataRow->MRSID_REF.'"  >
                            <input type="checkbox" id="chkId'.$CustomId.'"  value="'.$CustomId.'" class="js-selectall1"  ></td>
                            <input type="hidden" id="txtrecordId_'.$CustomId.'"  value="'.$CustomId.'"   >';
                            $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                            $row = $row.'<input type="hidden" id="txtitem_'.$CustomId.'" data-desc="'.$dataRow->ICODE.'"data-desc1="'.$TEMP_QTY.'" data-quoqty="'.$TEMP_QTY.'" data-desc2="'.$dataRow->STDCOST.'" data-desc3="'.$DIS_PER.'" data-desc4="'.$DIS_AMT.'" data-desc5="'.$RFQID_REF.'" data-pino="'.$dataRow->PINO.'" value="'.$dataRow->ITEMID.'"/></td>
                             <td style="width:10%;" id="itemname_'.$CustomId.'" >'.$dataRow->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$CustomId.'" data-desc="'.$dataRow->ITEM_SPECI.'" value="'.$dataRow->NAME.'"/></td>';
                            $row = $row.'<td style="width:8%;" id="itemuom_'.$CustomId.'" ><input type="hidden" id="txtitemuom_'.$CustomId.'" 
                            data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'" 
                            data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" 
                            value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                            $row = $row.'<td style="width:8%;" id="uomqty_'.$CustomId.'" ><input type="hidden" id="txtuomqty_'.$CustomId.'" data-desc="'.$TOQTY.'" value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                            $row = $row.'<td style="width:8%;" id="irate_'.$CustomId.'"><input type="hidden" id="txtirate_'.$CustomId.'" data-desc="'.$dataRow->STDCOST.'" value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                            $row = $row.'<td style="width:8%;" id="itax_'.$CustomId.'"><input type="hidden" id="txtitax_'.$CustomId.'" data-desc="'.$Taxid[0].'" value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                            
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
                        
                        
                $row = '';

                
                $DIS_PER    =   0.0000;
                $DIS_AMT    =   0.00000;
                $PINO       =   '';
                $RFQID_REF  =   '';
                $MRSID_REF  =   '';
                $TEMP_QTY   =   '0.000';
                $RATE       =   $STDCOST;
                
                $CustomId = $RFQID_REF.'-'.$PINO.'-'.$ITEMID.'-'.$MRSID_REF;

                $row = $row.'<tr id="item_'.$CustomId.'"  class="clsitemid"><td  style="width:8%; text-align: center;">
                <input type="hidden" id="MRSNOitem_'.$CustomId.'"  value="'.$MRSID_REF.'"  >
                <input type="checkbox" id="chkId'.$CustomId.'"  value="'.$CustomId.'" class="js-selectall1"  ></td>
                <input type="hidden" id="txtrecordId_'.$CustomId.'"  value="'.$CustomId.'"   >';
                $row = $row.'<td style="width:10%;">'.$ICODE;
                $row = $row.'<input type="hidden" id="txtitem_'.$CustomId.'" data-desc="'.$ICODE.'" data-desc1="'.$TEMP_QTY.'" data-quoqty="'.$TEMP_QTY.'" data-rfqqty="'.$TEMP_QTY.'" data-desc2="'.$RATE.'" data-desc3="'.$DIS_PER.'" data-desc4="'.$DIS_AMT.'" data-desc5="'.$RFQID_REF.'" data-pino="'.$PINO.'"  value="'.$ITEMID.'"/></td>
                    <td style="width:10%;" id="itemname_'.$CustomId.'" >'.$NAME;
                $row = $row.'<input type="hidden" id="txtitemname_'.$CustomId.'" data-desc="'.$ITEM_SPECI.'" value="'.$NAME.'"/></td>';
                $row = $row.'<td style="width:8%;" id="itemuom_'.$CustomId.'" ><input type="hidden" id="txtitemuom_'.$CustomId.'" 
                data-desc="'.$Alt_UOM.'" data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>';
                $row = $row.'<td style="width:8%;" id="uomqty_'.$CustomId.'" ><input type="hidden" id="txtuomqty_'.$CustomId.'" data-desc="'.$TOQTY.'" value="'.$ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                $row = $row.'<td style="width:8%;" id="irate_'.$CustomId.'"><input type="hidden" id="txtirate_'.$CustomId.'" data-desc="'.$RATE.'" value="'.$STDCOST.'"/>'.$GroupName.'</td>';
                $row = $row.'<td style="width:8%;" id="itax_'.$CustomId.'"><input type="hidden" id="txtitax_'.$CustomId.'" data-desc="'.$Taxid1.'" value="'.$Taxid2.'"/>'.$Categoryname.'</td> 
                
                <td style="width:8%;">'.$BusinessUnit.'</td>
                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                <td style="width:8%;">Authorized</td></tr>';


                echo $row;

            } 
                    
        }           
        else{
            echo '<tr><td colspan="12"> Record not found.</td></tr>';
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
                        data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" 
                        value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
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
                            data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" 
                            value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
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

        $Status     =   "A";
        $SLID_REF   =   $request['id'];
        $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
        $id         =   $ObVID->VID;

        $ObjBillTo =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                    WHERE DEFAULT_BILLING= ? AND VID_REF = ? ', [1,$id]);

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
            $Status     =   "A";
            $SLID_REF   =   $request['id'];
            $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
            $id         =   $ObVID->VID;
            $BRID_REF   =   Session::get('BRID_REF');
            

           
            $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                        WHERE DEFAULT_SHIPPING= ? AND VID_REF = ? ', [1,$id]);

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
                $Status     =   "A";
                $SLID_REF   =   $request['id'];
                $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
                $id         =   $ObVID->VID;

                if(!is_null($id))
                {
              
                $ObjBillTo =  DB::select('SELECT  * FROM TBL_MST_VENDORLOCATION  
                            WHERE BILLTO= ? AND VID_REF = ? ', [1,$id]);
            
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
                        value="'.$dataRow->LID.'"/></td>
                        <td class="ROW3">'.$objAddress.'</td>
                        </tr>';
                        echo $row;

                    }
            
                    }else{
                        echo '<tr><td colspan="2">Record not found.</td></tr>';
                    }
                    exit();
                }
            }
    
            public function getShipAddress(Request $request){

                $Status     =   "A";
                $SLID_REF   =   $request['id'];
                $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
                $id         =   $ObVID->VID;
                $BRID_REF   =   Session::get('BRID_REF');

                if(!is_null($id))
                {
               
                $ObjShipTo =  DB::select('SELECT  * FROM TBL_MST_VENDORLOCATION  
                            WHERE SHIPTO= ? AND VID_REF = ? ', [1,$id]);
            
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


            public function attachment($id){

                $FormId = $this->form_id;
                if(!is_null($id))
                {
                    $objMst = DB::table("TBL_TRN_VDQT01_HDR")
                                ->where('VQID','=',$id)
                                ->select('*')
                                ->first();        

                    $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
                                ->where('VTID','=',$this->vtid_ref)
                                ->select('VTID','VCODE','DESCRIPTIONS','INDATE')
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
                                    
                        return view($this->view.'attachment',compact(['FormId','objMst','objMstVoucherType','objAttachments']));
                }
        
            }

    
   public function save(Request $request) {
    
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count4 = $request['Row_Count4'];
        $r_count5 = $request['Row_Count5'];
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i]))
            {
                $req_data[$i] = [
                    'RFQNO'      => isset( $request['RFQID_'.$i]) &&  (!is_null($request['RFQID_'.$i]) ) ? $request['RFQID_'.$i] : 0,
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'ITEMSPECI' => $request['Itemspec_'.$i],
                    'RFQ_QTY' => $request['RFQ_QTY_'.$i],
                    'QUOTATION_QTY' => $request['VQ_QTY_'.$i],
                    'RATEP_UOM' => $request['RATEPUOM_'.$i],
                    'DISCOUNT_PER'    => (!empty($request['DISCPER_'.$i])) == 'true' ? $request['DISCPER_'.$i] : 0,
                    'DISCOUNT_AMT' => (!empty($request['DISCOUNT_AMT_'.$i])) == 'true' ? $request['DISCOUNT_AMT_'.$i] : 0,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'PIID_REF' => (!empty($request['PINO_'.$i]) ? $request['PINO_'.$i] : 0),
                    'MRSID_REF'    => $request['MRSNO_'.$i],
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
        if(isset($reqdata2)) { 
            $wrapped_links2["TNC"] = $reqdata2;
            $XMLTNC = ArrayToXml::convert($wrapped_links2);
        }
        else {
            $XMLTNC = NULL; 
        }  
        
        $r_count3 = $request['Row_Count3'];
        $udffield_Data = [];      
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           }
            
        }
        if(count($udffield_Data)>0){
            $udffield_wrapped["UDF1"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
        }else{
            $XMLUDF = NULL;
        }
        
        for ($i=0; $i<=$r_count4; $i++)
        {
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
            $wrapped_links4["CAL"] = $reqdata4; 
            $XMLCAL = ArrayToXml::convert($wrapped_links4);
        }
        else
        {
            $XMLCAL = NULL; 
        }
        
        

            $VTID_REF     =   $this->vtid_ref;
            
            $USERID_REF = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');

            $VQ_NO = $request['VQ_NO'];
            $VQ_DT = $request['VQ_DT'];
            $VID_REF = $request['VID_REF'];
            
            $VENDOR_QNO = $request['VENDOR_QNO'];
            $VENDOR_QDT = $request['VENDOR_QDT'];

            $QUOTATION_VFR = $request['QUOTATION_VFR'];
            $QUOTATION_VTO = $request['QUOTATION_VTO'];
            $REMARKS = trim($request['REMARKS']);
            $DIRECT_VQ = (isset($request['DIRECT_VQ']) )? 1 : 0 ; 
            $BILLTO = $request['BILLTO'];
            $SHIPTO = $request['SHIPTO'];

            $log_data = [ 
                $VQ_NO,     $VQ_DT,     $VID_REF,       $VENDOR_QNO,    $VENDOR_QDT,    $QUOTATION_VFR, $QUOTATION_VTO,     $REMARKS,
                $BILLTO,    $SHIPTO,    $DIRECT_VQ,     $CYID_REF,      $BRID_REF,      $FYID_REF,      $VTID_REF,          $XMLMAT,
                $XMLTNC,    $XMLUDF,    $XMLCAL,        $USERID_REF,    Date('Y-m-d'),  Date('h:i:s.u'),    $ACTIONNAME,    $IPADDRESS
            ];

            

            $sp_result = DB::select('EXEC SP_VQ_IN ?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?', $log_data);     
           
        
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            
            exit();   
     }


    
     
     

    public function edit($id=NULL){       
        
        if(!is_null($id)){

            $FormId = $this->form_id;
            $Status = "A";
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $USERID = Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

          
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objRights = DB::table('TBL_MST_USERROLMAP')
                ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
                ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
                ->where('TBL_MST_USERROLMAP.BRID_REF','=',$BRID_REF)
                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID)
                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                ->first();

            
            $d_currency = DB::table('TBL_MST_COMPANY')
            ->where('STATUS','=',$Status)
            ->where('CYID','=',Auth::user()->CYID_REF)
            ->select('TBL_MST_COMPANY.CRID_REF')
            ->first();


            $objcurrency =NULL;
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
            
            $objMstResponse = DB::table('TBL_TRN_VDQT01_HDR')
                ->where('FYID_REF','=',Session::get('FYID_REF'))
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('VQID','=',$id)
                ->select('*')
                ->first();

            $objList1 = DB::table('TBL_TRN_VDQT01_MAT')                    
                ->where('TBL_TRN_VDQT01_MAT.VQID_REF','=',$id)
                     
                ->leftJoin('TBL_TRN_RQFQ01_HDR','TBL_TRN_VDQT01_MAT.RFQNO','=','TBL_TRN_RQFQ01_HDR.RFQID')                
                ->leftJoin('TBL_MST_ITEM','TBL_TRN_VDQT01_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                
                ->leftJoin('TBL_MST_UOM','TBL_TRN_VDQT01_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                
                ->select( 
                    'TBL_TRN_VDQT01_MAT.*',
                 
                    'TBL_TRN_RQFQ01_HDR.RFQ_NO',
                    'TBL_MST_ITEM.ITEMID',
                    'TBL_MST_ITEM.ICODE',
                    'TBL_MST_ITEM.NAME',
                    'TBL_MST_UOM.UOMID',
                    'TBL_MST_UOM.UOMCODE',
                    'TBL_MST_UOM.DESCRIPTIONS',
                )
                ->orderBy('TBL_TRN_VDQT01_MAT.VQMATID','ASC')
                ->get()->toArray();
            
             


                $ObjItem2 = $objList1;
                foreach ($ObjItem2 as $index=>$dataRow){

                    if($dataRow->MRSID_REF =="" || $dataRow->MRSID_REF ==NULL || $dataRow->MRSID_REF ==0){
                        $WhereMrs="";
                    }
                    else{
                        $WhereMrs="AND	MRSID_REF=$dataRow->MRSID_REF";
                    }

                    if($dataRow->PIID_REF =="" || $dataRow->PIID_REF ==NULL || $dataRow->PIID_REF ==0){
                        $WherePIID="";
                    }
                    else{
                        $WherePIID="AND PINO=$dataRow->PIID_REF";
                    }

                    if($dataRow->RFQNO =="" || $dataRow->RFQNO ==NULL || $dataRow->RFQNO ==0){
                        $WhereRFQNO="";
                    }
                    else{
                        $WhereRFQNO="AND RFQID_REF=$dataRow->RFQNO";
                    }

                    $ObjActPenQTY =  DB::select("select TOP 1 PENDING_QTY from TBL_TRN_RQFQ01_MAT where ITEMID_REF=$dataRow->ITEMID_REF AND  UOMID_REF=$dataRow->UOMID_REF $WherePIID  $WhereMrs $WhereRFQNO");
                    
                    
                    if(!empty($ObjActPenQTY)){
                        $total_pen_qty = 0;
                        $total_pen_qty = number_format(floatVal($dataRow->QUOTATION_QTY ) +  floatval($ObjActPenQTY[0]->PENDING_QTY), 3, '.', '');
                        $objList1[$index]->TOTAL_PENDING = $total_pen_qty;
                    }
                    else{
                        $objList1[$index]->TOTAL_PENDING =NULL;
                    }


                }

                  
                $objList1Count = count($objList1);
                if($objList1Count==0){
                    $objList1Count=1;
                }
                
                $objvendorcode2 =[];
                if(isset($objMstResponse->VID_REF) && $objMstResponse->VID_REF !=""){
                    $objvendorcode2 = DB::table('TBL_MST_SUBLEDGER')
                    ->where('BELONGS_TO','=','Vendor')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('SGLID','=',$objMstResponse->VID_REF)    
                    ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                    ->first();
                }
            


                $objSavedTNC =  DB::table('TBL_TRN_VDQT01_TNC')
                ->where('VQID_REF','=',$id)
                ->select('*')
                ->get()->toArray();

                $objSOTNC = DB::table('TBL_TRN_VDQT01_TNC')
                ->where('VQID_REF','=',$id)
                ->select('*')
                ->get()->toArray();
                $objCount2 = count($objSOTNC);


               


                $objSavedTNCHeader=[];
                $objSavedTNCHeaderDTL=[];

                if(!empty($objSavedTNC)){
                    $objSavedTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
                                WHERE  TNCID = ?', [$objSavedTNC[0]->TNCID_REF ]);

                    $objSavedTNCHeaderDTL = DB::select('SELECT * FROM TBL_MST_TNC_DETAILS  
                                 WHERE  TNCID_REF = ?', [$objSavedTNC[0]->TNCDID_REF ]);
                   
                }
               

                $objSavedCalT =  DB::table('TBL_TRN_VDQT01_CAL')
                                ->where('VQID_REF','=',$id)
                                ->select('*')
                                ->get()->toArray();

                $objVQCAL =  DB::table('TBL_TRN_VDQT01_CAL')
                    ->where('VQID_REF','=',$id)
                    ->select('*')
                    ->get()->toArray();
                $objCount4 = count($objVQCAL);


                $ObjBranch =  [];
                $TAXSTATE = [];
                $objShpAddress=[] ;
                $objBillAddress=[];

                
                if(isset($objMstResponse->SHIP_TO) && $objMstResponse->SHIP_TO !="" && isset($objMstResponse->VID_REF) && $objMstResponse->VID_REF !=""){
                $sid        =   $objMstResponse->SHIP_TO;
                $SLID_REF   =   $objMstResponse->VID_REF;
                $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
                $VID        =   $ObVID->VID;


                $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                        WHERE SHIPTO= ? AND VID_REF = ? ', [$sid,$VID]);

               

                $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  
                                WHERE BRID= ? ', [$BRID_REF]);

               


                if(isset($ObjSHIPTO) && $ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF)
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
               
                
                if(isset($objMstResponse->BILL_TO) && $objMstResponse->BILL_TO !=""){
                $bid = $objMstResponse->BILL_TO;
                

                $ObjBillTo =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                    WHERE BILLTO= ? AND VID_REF = ? ', [$bid,$VID]);

                

                $ObjCity2 =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                            WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                            [$Status,$ObjBillTo[0]->CITYID_REF,$ObjBillTo[0]->CTRYID_REF,$ObjBillTo[0]->STID_REF]);

                   


                $ObjState2 =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                            WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBillTo[0]->STID_REF,$ObjBillTo[0]->CTRYID_REF]);

                $ObjCountry2 =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                            WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBillTo[0]->CTRYID_REF]);

                $ObjAddressID = $ObjBillTo[0]->LID;
                if(!empty($ObjBillTo)){
                    $objBillAddress[] = $ObjBillTo[0]->LADD .' '.$ObjCity2[0]->NAME .' '.$ObjState2[0]->NAME .' '.$ObjCountry2[0]->NAME;
                }  

            }

                $objSavedCalTHeader = [];
                $objSavedCalTHeaderDTL = [];

                if(!empty($objSavedCalT)){

                    $objSavedCalTHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
                                WHERE  CTID = ?', [$objSavedCalT[0]->CTID_REF ]);
                }
             
            

            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
                WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                order by TNC_CODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);

            $objCalculationHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by CTCODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);
            
            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_VQ_MANAGEMENT")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                    {       
                                    $query->select('VQMID')->from('TBL_MST_UDFFOR_VQ_MANAGEMENT')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                        
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                    
            
         
            $objUdf  = DB::table('TBL_MST_UDFFOR_VQ_MANAGEMENT')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray();   
            $objCountUDF = count($objUdf);
          

            $objtempUdf = $objUdf;
            foreach ($objtempUdf as $index => $udfvalue) {

                $objSavedUDF =  DB::table('TBL_TRN_VDQT01_UDF')
                ->where('VQID_REF','=',$id)
                ->where('UDF','=',$udfvalue->VQMID)
                ->select('VALUE')
                ->get()->toArray();

                if(!empty($objSavedUDF)){
                    $objUdf[$index]->UDF_VALUE = $objSavedUDF[0]->VALUE;
                }
                else{
                    $objUdf[$index]->UDF_VALUE = NULL; 
                }
            }
            $objtempUdf = [];

        
            


            $objUdfSOData2 =  DB::table('TBL_MST_UDFFOR_VQ_MANAGEMENT')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   


            
            $objSOUDF = DB::table('TBL_TRN_VDQT01_UDF')                    
            ->where('VQID_REF','=',$id)
            ->select('*')
            ->orderBy('VQUDFID','ASC')
            ->get()->toArray();
            $objCount3 = count($objSOUDF);

            $objCurrencyconverter = DB::table('TBL_MST_CRCONVERSION')
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_CRCONVERSION.*')
            ->get()
            ->toArray();

            $objlastVQ_DT = DB::select('SELECT MAX(VQ_DT) VQ_DT FROM TBL_TRN_VDQT01_HDR  
                    WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
                    [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
                    
            $FormId  = $this->form_id;

            $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
            WHERE  CYID_REF = ? AND BRID_REF = ?   
            order by CTCODE ASC', [$CYID_REF, $BRID_REF ]);

            $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')
            ->get() ->toArray(); 

            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by TNC_CODE ASC', [$CYID_REF, $BRID_REF, 'A' ]);

            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
            ->get() ->toArray(); 

            $objCOMPANY = DB::table('TBL_MST_COMPANY')
            ->where('CYID','=',$CYID_REF)
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_COMPANY.*')
            ->first();

            $AlpsStatus =   $this->AlpsStatus();

            $ActionStatus   =   "";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        return view($this->view.'edit', compact(['AlpsStatus','FormId','objRights','objCalculationHeader','objcurrency','objTNCHeader','objothcurrency', 
        'objCurrencyconverter','objUdf','objCountUDF','objlastVQ_DT','objMstResponse','objList1','objList1Count','objvendorcode2',
        'objShpAddress','objBillAddress','objSavedTNCHeader','objSavedTNCHeaderDTL','objVQCAL','objCount4','objCalHeader','objCalDetails',
        'TAXSTATE','objSOUDF','objUdfSOData2','objSOTNC','objTNCDetails','objCount2','objCOMPANY','ActionStatus','TabSetting']));     
        }
    
   }
   
   public function view($id=NULL){       
        
    if(!is_null($id)){

        $FormId = $this->form_id;
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $USERID = Auth::user()->USERID;
        $VTID       =   $this->vtid_ref;

        $sp_user_approval_req = [
            $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
        ];        

      
        $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
        $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

        $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',$BRID_REF)
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

        
        $d_currency = DB::table('TBL_MST_COMPANY')
        ->where('STATUS','=',$Status)
        ->where('CYID','=',Auth::user()->CYID_REF)
        ->select('TBL_MST_COMPANY.CRID_REF')
        ->first();


        $objcurrency =NULL;
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
        
        $objMstResponse = DB::table('TBL_TRN_VDQT01_HDR')
            ->where('FYID_REF','=',Session::get('FYID_REF'))
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('VQID','=',$id)
            ->select('*')
            ->first();

        $objList1 = DB::table('TBL_TRN_VDQT01_MAT')                    
            ->where('TBL_TRN_VDQT01_MAT.VQID_REF','=',$id)
                 
            ->leftJoin('TBL_TRN_RQFQ01_HDR','TBL_TRN_VDQT01_MAT.RFQNO','=','TBL_TRN_RQFQ01_HDR.RFQID')                
            ->leftJoin('TBL_MST_ITEM','TBL_TRN_VDQT01_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                
            ->leftJoin('TBL_MST_UOM','TBL_TRN_VDQT01_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                
            ->select( 
                'TBL_TRN_VDQT01_MAT.*',
             
                'TBL_TRN_RQFQ01_HDR.RFQ_NO',
                'TBL_MST_ITEM.ITEMID',
                'TBL_MST_ITEM.ICODE',
                'TBL_MST_ITEM.NAME',
                'TBL_MST_UOM.UOMID',
                'TBL_MST_UOM.UOMCODE',
                'TBL_MST_UOM.DESCRIPTIONS',
            )
            ->orderBy('TBL_TRN_VDQT01_MAT.VQMATID','ASC')
            ->get()->toArray();
        
         


            $ObjItem2 = $objList1;
            foreach ($ObjItem2 as $index=>$dataRow){

                if($dataRow->MRSID_REF =="" || $dataRow->MRSID_REF ==NULL || $dataRow->MRSID_REF ==0){
                    $WhereMrs="";
                }
                else{
                    $WhereMrs="AND	MRSID_REF=$dataRow->MRSID_REF";
                }

                if($dataRow->PIID_REF =="" || $dataRow->PIID_REF ==NULL || $dataRow->PIID_REF ==0){
                    $WherePIID="";
                }
                else{
                    $WherePIID="AND PINO=$dataRow->PIID_REF";
                }

                if($dataRow->RFQNO =="" || $dataRow->RFQNO ==NULL || $dataRow->RFQNO ==0){
                    $WhereRFQNO="";
                }
                else{
                    $WhereRFQNO="AND RFQID_REF=$dataRow->RFQNO";
                }

                $ObjActPenQTY =  DB::select("select TOP 1 PENDING_QTY from TBL_TRN_RQFQ01_MAT where ITEMID_REF=$dataRow->ITEMID_REF AND  UOMID_REF=$dataRow->UOMID_REF $WherePIID  $WhereMrs $WhereRFQNO");
                
                
                if(!empty($ObjActPenQTY)){
                    $total_pen_qty = 0;
                    $total_pen_qty = number_format(floatVal($dataRow->QUOTATION_QTY ) +  floatval($ObjActPenQTY[0]->PENDING_QTY), 3, '.', '');
                    $objList1[$index]->TOTAL_PENDING = $total_pen_qty;
                }
                else{
                    $objList1[$index]->TOTAL_PENDING = NULL;
                }

            }

              
            $objList1Count = count($objList1);
            if($objList1Count==0){
                $objList1Count=1;
            }
            
            $objvendorcode2 =[];
            if(isset($objMstResponse->VID_REF) && $objMstResponse->VID_REF !=""){
                $objvendorcode2 = DB::table('TBL_MST_SUBLEDGER')
                ->where('BELONGS_TO','=','Vendor')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('SGLID','=',$objMstResponse->VID_REF)    
                ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                ->first();
            }
        


            $objSavedTNC =  DB::table('TBL_TRN_VDQT01_TNC')
            ->where('VQID_REF','=',$id)
            ->select('*')
            ->get()->toArray();

            $objSOTNC = DB::table('TBL_TRN_VDQT01_TNC')
            ->where('VQID_REF','=',$id)
            ->select('*')
            ->get()->toArray();
            $objCount2 = count($objSOTNC);


           


            $objSavedTNCHeader=[];
            $objSavedTNCHeaderDTL=[];

            if(!empty($objSavedTNC)){
                $objSavedTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
                            WHERE  TNCID = ?', [$objSavedTNC[0]->TNCID_REF ]);

                $objSavedTNCHeaderDTL = DB::select('SELECT * FROM TBL_MST_TNC_DETAILS  
                             WHERE  TNCID_REF = ?', [$objSavedTNC[0]->TNCDID_REF ]);
               
            }
           

            $objSavedCalT =  DB::table('TBL_TRN_VDQT01_CAL')
                            ->where('VQID_REF','=',$id)
                            ->select('*')
                            ->get()->toArray();

            $objVQCAL =  DB::table('TBL_TRN_VDQT01_CAL')
                ->where('VQID_REF','=',$id)
                ->select('*')
                ->get()->toArray();
            $objCount4 = count($objVQCAL);


            $ObjBranch =  [];
            $TAXSTATE = [];
            $objShpAddress=[] ;
            $objBillAddress=[];

            
            if(isset($objMstResponse->SHIP_TO) && $objMstResponse->SHIP_TO !="" && isset($objMstResponse->VID_REF) && $objMstResponse->VID_REF !=""){
            $sid        =   $objMstResponse->SHIP_TO;
            $SLID_REF   =   $objMstResponse->VID_REF;
            $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
            $VID        =   $ObVID->VID;


            $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                    WHERE SHIPTO= ? AND VID_REF = ? ', [$sid,$VID]);

           

            $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  
                            WHERE BRID= ? ', [$BRID_REF]);

           


            if(isset($ObjSHIPTO) && $ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF)
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
           
            
            if(isset($objMstResponse->BILL_TO) && $objMstResponse->BILL_TO !=""){
            $bid = $objMstResponse->BILL_TO;
            

            $ObjBillTo =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                WHERE BILLTO= ? AND VID_REF = ? ', [$bid,$VID]);

            

            $ObjCity2 =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                        WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                        [$Status,$ObjBillTo[0]->CITYID_REF,$ObjBillTo[0]->CTRYID_REF,$ObjBillTo[0]->STID_REF]);

               


            $ObjState2 =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                        WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBillTo[0]->STID_REF,$ObjBillTo[0]->CTRYID_REF]);

            $ObjCountry2 =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                        WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBillTo[0]->CTRYID_REF]);

            $ObjAddressID = $ObjBillTo[0]->LID;
            if(!empty($ObjBillTo)){
                $objBillAddress[] = $ObjBillTo[0]->LADD .' '.$ObjCity2[0]->NAME .' '.$ObjState2[0]->NAME .' '.$ObjCountry2[0]->NAME;
            }  

        }

            $objSavedCalTHeader = [];
            $objSavedCalTHeaderDTL = [];

            if(!empty($objSavedCalT)){

                $objSavedCalTHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
                            WHERE  CTID = ?', [$objSavedCalT[0]->CTID_REF ]);
            }
         
        

        $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by TNC_CODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);

        $objCalculationHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by CTCODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);
        
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_VQ_MANAGEMENT")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('VQMID')->from('TBL_MST_UDFFOR_VQ_MANAGEMENT')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                    
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                
        
     
        $objUdf  = DB::table('TBL_MST_UDFFOR_VQ_MANAGEMENT')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdf);
      

        $objtempUdf = $objUdf;
        foreach ($objtempUdf as $index => $udfvalue) {

            $objSavedUDF =  DB::table('TBL_TRN_VDQT01_UDF')
            ->where('VQID_REF','=',$id)
            ->where('UDF','=',$udfvalue->VQMID)
            ->select('VALUE')
            ->get()->toArray();

            if(!empty($objSavedUDF)){
                $objUdf[$index]->UDF_VALUE = $objSavedUDF[0]->VALUE;
            }
            else{
                $objUdf[$index]->UDF_VALUE = NULL; 
            }
        }
        $objtempUdf = [];

    
        


        $objUdfSOData2 =  DB::table('TBL_MST_UDFFOR_VQ_MANAGEMENT')
        ->where('STATUS','=','A')
        ->where('PARENTID','=',0)
        ->where('DEACTIVATED','=',0)
        ->where('CYID_REF','=',$CYID_REF)
        ->union($ObjUnionUDF)
        ->get()->toArray();   


        
        $objSOUDF = DB::table('TBL_TRN_VDQT01_UDF')                    
        ->where('VQID_REF','=',$id)
        ->select('*')
        ->orderBy('VQUDFID','ASC')
        ->get()->toArray();
        $objCount3 = count($objSOUDF);

        $objCurrencyconverter = DB::table('TBL_MST_CRCONVERSION')
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_CRCONVERSION.*')
        ->get()
        ->toArray();

        $objlastVQ_DT = DB::select('SELECT MAX(VQ_DT) VQ_DT FROM TBL_TRN_VDQT01_HDR  
                WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
                [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
                
        $FormId  = $this->form_id;

        $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
        WHERE  CYID_REF = ? AND BRID_REF = ?   
        order by CTCODE ASC', [$CYID_REF, $BRID_REF ]);

        $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')
        ->get() ->toArray(); 

        $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by TNC_CODE ASC', [$CYID_REF, $BRID_REF, 'A' ]);

        $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
        ->get() ->toArray(); 

        $objCOMPANY = DB::table('TBL_MST_COMPANY')
        ->where('CYID','=',$CYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_COMPANY.*')
        ->first();

        $AlpsStatus =   $this->AlpsStatus();

        $ActionStatus   =   "disabled";

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

    return view($this->view.'view', compact(['AlpsStatus','FormId','objRights','objCalculationHeader','objcurrency','objTNCHeader','objothcurrency', 
    'objCurrencyconverter','objUdf','objCountUDF','objlastVQ_DT','objMstResponse','objList1','objList1Count','objvendorcode2',
    'objShpAddress','objBillAddress','objSavedTNCHeader','objSavedTNCHeaderDTL','objVQCAL','objCount4','objCalHeader','objCalDetails',
    'TAXSTATE','objSOUDF','objUdfSOData2','objSOTNC','objTNCDetails','objCount2','objCOMPANY','ActionStatus','TabSetting']));     
    }

}

    
   
   public function update(Request $request){

      
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count4 = $request['Row_Count4'];
        $r_count5 = $request['Row_Count5'];
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i]))
            {
                $req_data[$i] = [
                    'RFQNO'      => isset( $request['RFQID_'.$i]) &&  (!is_null($request['RFQID_'.$i]) ) ? $request['RFQID_'.$i] : 0,
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'ITEMSPECI' => $request['Itemspec_'.$i],
                    'RFQ_QTY' => $request['RFQ_QTY_'.$i],
                    'QUOTATION_QTY' => $request['VQ_QTY_'.$i],
                    'RATEP_UOM' => $request['RATEPUOM_'.$i],
                    'DISCOUNT_PER'    => (!empty($request['DISCPER_'.$i])) == 'true' ? $request['DISCPER_'.$i] : 0,
                    'DISCOUNT_AMT' => (!empty($request['DISCOUNT_AMT_'.$i])) == 'true' ? $request['DISCOUNT_AMT_'.$i] : 0,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'PIID_REF' => (!empty($request['PINO_'.$i]) ? $request['PINO_'.$i] : 0),
                    'MRSID_REF'    => $request['MRSNO_'.$i],

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
        if(isset($reqdata2)) { 
            $wrapped_links2["TNC"] = $reqdata2;
            $XMLTNC = ArrayToXml::convert($wrapped_links2);
        }
        else {
            $XMLTNC = NULL; 
        }  
        
        $r_count3 = $request['Row_Count3'];
        $udffield_Data = [];      
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           }
            
        }
        if(count($udffield_Data)>0){
            $udffield_wrapped["UDF1"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
        }else{
            $XMLUDF = NULL;
        }
        
        for ($i=0; $i<=$r_count4; $i++)
        {
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
            $wrapped_links4["CAL"] = $reqdata4; 
            $XMLCAL = ArrayToXml::convert($wrapped_links4);
        }
        else
        {
            $XMLCAL = NULL; 
        }
        
       

            $VTID_REF     =   $this->vtid_ref;
            
            $USERID_REF = Auth::user()->USERID;   
            $ACTIONNAME = 'EDIT';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');

            $VQ_NO = $request['VQ_NO'];
            $VQ_DT = $request['VQ_DT'];
            $VID_REF = $request['VID_REF'];
            
            $VENDOR_QNO = $request['VENDOR_QNO'];
            $VENDOR_QDT = $request['VENDOR_QDT'];

            $QUOTATION_VFR = $request['QUOTATION_VFR'];
            $QUOTATION_VTO = $request['QUOTATION_VTO'];
            $REMARKS = trim($request['REMARKS']);
            $DIRECT_VQ = (isset($request['DIRECT_VQ']) )? 1 : 0 ; 
            $BILLTO = $request['BILLTO'];
            $SHIPTO = $request['SHIPTO'];


            $log_data = [ 
                $VQ_NO,     $VQ_DT,     $VID_REF,       $VENDOR_QNO,    $VENDOR_QDT,    $QUOTATION_VFR, $QUOTATION_VTO,     $REMARKS,
                $BILLTO,    $SHIPTO,    $DIRECT_VQ,     $CYID_REF,      $BRID_REF,      $FYID_REF,      $VTID_REF,          $XMLMAT,
                $XMLTNC,    $XMLUDF,    $XMLCAL,        $USERID_REF,    Date('Y-m-d'),  Date('h:i:s.u'),    $ACTIONNAME,    $IPADDRESS
            ];

           

            $sp_result = DB::select('EXEC SP_VQ_UP ?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?', $log_data);   

            
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $VQ_NO. ' Sucessfully Updated.']);

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
        $r_count4 = $request['Row_Count4'];
        $r_count5 = $request['Row_Count5'];
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i]))
            {
                $req_data[$i] = [
                    'RFQNO'      => isset( $request['RFQID_'.$i]) &&  (!is_null($request['RFQID_'.$i]) ) ? $request['RFQID_'.$i] : 0,
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'UOMID_REF' => $request['MAIN_UOMID_REF_'.$i],
                    'ITEMSPECI' => $request['Itemspec_'.$i],
                    'RFQ_QTY' => $request['RFQ_QTY_'.$i],
                    'QUOTATION_QTY' => $request['VQ_QTY_'.$i],
                    'RATEP_UOM' => $request['RATEPUOM_'.$i],
                    'DISCOUNT_PER'    => (!empty($request['DISCPER_'.$i])) == 'true' ? $request['DISCPER_'.$i] : 0,
                    'DISCOUNT_AMT' => (!empty($request['DISCOUNT_AMT_'.$i])) == 'true' ? $request['DISCOUNT_AMT_'.$i] : 0,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'PIID_REF' => (!empty($request['PINO_'.$i]) ? $request['PINO_'.$i] : 0),
                    'MRSID_REF'    => $request['MRSNO_'.$i],

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
        if(isset($reqdata2)) { 
            $wrapped_links2["TNC"] = $reqdata2;
            $XMLTNC = ArrayToXml::convert($wrapped_links2);
        }
        else {
            $XMLTNC = NULL; 
        }  
        
        $r_count3 = $request['Row_Count3'];
        $udffield_Data = [];      
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           }
            
        }
        if(count($udffield_Data)>0){
            $udffield_wrapped["UDF1"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
        }else{
            $XMLUDF = NULL;
        }
        
        for ($i=0; $i<=$r_count4; $i++)
        {
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
            $wrapped_links4["CAL"] = $reqdata4; 
            $XMLCAL = ArrayToXml::convert($wrapped_links4);
        }
        else
        {
            $XMLCAL = NULL; 
        }
        
        

            $VTID_REF     =   $this->vtid_ref;
            
            $USERID_REF = Auth::user()->USERID;   
            $ACTIONNAME = $Approvallevel;
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');

            $VQ_NO = $request['VQ_NO'];
            $VQ_DT = $request['VQ_DT'];
            $VID_REF = $request['VID_REF'];
            
            $VENDOR_QNO = $request['VENDOR_QNO'];
            $VENDOR_QDT = $request['VENDOR_QDT'];

            $QUOTATION_VFR = $request['QUOTATION_VFR'];
            $QUOTATION_VTO = $request['QUOTATION_VTO'];
            $REMARKS = trim($request['REMARKS']);
            $DIRECT_VQ = (isset($request['DIRECT_VQ']) )? 1 : 0 ; 
            $BILLTO = $request['BILLTO'];
            $SHIPTO = $request['SHIPTO'];


            $log_data = [ 
                $VQ_NO,     $VQ_DT,     $VID_REF,       $VENDOR_QNO,    $VENDOR_QDT,    $QUOTATION_VFR, $QUOTATION_VTO,     $REMARKS,
                $BILLTO,    $SHIPTO,    $DIRECT_VQ,     $CYID_REF,      $BRID_REF,      $FYID_REF,      $VTID_REF,          $XMLMAT,
                $XMLTNC,    $XMLUDF,    $XMLCAL,        $USERID_REF,    Date('Y-m-d'),  Date('h:i:s.u'),    $ACTIONNAME,    $IPADDRESS
            ];

           

            $sp_result = DB::select('EXEC SP_VQ_UP ?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?', $log_data);     
          
             
                
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $VQ_NO. ' Sucessfully Approved.']);

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
                $TABLE      =   "TBL_TRN_VDQT01_HDR";
                $FIELD      =   "VQID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
            
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_VQ ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
    
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
        $TABLE      =   "TBL_TRN_VDQT01_HDR";
        $FIELD      =   "VQID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_VDQT01_MAT',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_VDQT01_TNC',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_VDQT01_UDF',
        ];
        $req_data[3]=[
            'NT'  => 'TBL_TRN_VDQT01_CAL',
        ];
       
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_VQ  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

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
        
		$image_path         =   "docs/company".$CYID_REF."/VendorQuotation";     
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

    


   

    
}
