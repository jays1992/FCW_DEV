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


class TrnFrm306Controller extends Controller{
    protected $form_id  =   306;
    protected $vtid_ref =   396;
    protected $view     =   "transactions.Accounts.CreditNoteCsv.trnfrm";
   
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

        $objDataList	=	DB::select("select hdr.CSVID,hdr.CSV_NO,hdr.CSV_DT,hdr.REASON_CR_NOTE,hdr.NARRATION,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.CSVID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_CRSV01_HDR hdr
                            on a.VID = hdr.CSVID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_SUBLEDGER sl ON hdr.SGLID_REF = sl.SGLID 
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.CSVID DESC ");

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
        $objcurrency = $d_currency->CRID_REF;

        $objothcurrency = DB::table('TBL_MST_CURRENCY')
        ->where('STATUS','=',$Status)
        ->where('CRID','<>',$objcurrency)
        ->select('TBL_MST_CURRENCY.*')
        ->get()
        ->toArray();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_CRSV01_HDR',
            'HDR_ID'=>'CSVID',
            'HDR_DOC_NO'=>'CSV_NO',
            'HDR_DOC_DT'=>'CSV_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        $DOCNO = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('FYID_REF','=',$FYID_REF)
        ->where('STATUS','=','A')
        ->select('TBL_MST_DOCNO_DEFINITION.*')
        ->get();

        foreach ($DOCNO as $index=>$OBJ_DOC){        
            $p_type = $OBJ_DOC->PREFIX_TYPE;
        } 
        
        $READONLY   =   'readonly';
        $MAXLENGTH  =   100;
        $DOC_NO     =   NULL;
        if(isset($OBJ_DOC->SYSTEM_GRSR) && $OBJ_DOC->SYSTEM_GRSR == "1"){

            $PREFIX         =   $OBJ_DOC->PREFIX_RQ == "1"?$OBJ_DOC->PREFIX:NULL;
            $PRE_SEP_SLASH  =   $OBJ_DOC->PRE_SEP_RQ == "1" && $OBJ_DOC->PRE_SEP_SLASH == "1"?'/':NULL;
            $PRE_SEP_HYPEN  =   $OBJ_DOC->PRE_SEP_RQ == "1" && $OBJ_DOC->PRE_SEP_HYPEN == "1"?'-':NULL;
            $NO_MAX         =   $OBJ_DOC->NO_MAX; 
            $NO_SEP_SLASH   =   $OBJ_DOC->NO_SEP_RQ == "1" && $OBJ_DOC->NO_SEP_SLASH == "1"?'/':NULL;
            $NO_SEP_HYPEN   =   $OBJ_DOC->NO_SEP_RQ == "1" && $OBJ_DOC->NO_SEP_HYPEN == "1"?'-':NULL;
            $SUFFIX         =   $OBJ_DOC->SUFFIX_RQ == "1"?$OBJ_DOC->SUFFIX:NULL;  

            if($OBJ_DOC->DOC_SERIES_TYPE ==="MONTH"){

                $MONTH  =   date('m',strtotime($DATE));
                $YEAR   =   date('Y',strtotime($DATE));

                $OBJ_HDR = DB::select("SELECT TOP 1 $HDR_DOC_NO 
                FROM $HDR_TABLE 
                WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF' AND MONTH($HDR_DOC_DT)='$MONTH' AND YEAR($HDR_DOC_DT)='$YEAR' 
                ORDER BY $HDR_ID DESC");

                $LAST_RECORDNO  =   0;
                if(isset($OBJ_HDR) && !empty($OBJ_HDR)){
                    $strlen         =   strlen($PREFIX.$PRE_SEP_SLASH.$PRE_SEP_HYPEN.$MONTH);
                    $substr         =   substr($OBJ_HDR[0]->$HDR_DOC_NO,$strlen);
                    $substr         =   substr($substr,0,$OBJ_DOC->NO_MAX);
                    $LAST_RECORDNO  =   intval($substr);  
                }
                $AUTO_NO    = $MONTH.str_pad($LAST_RECORDNO+1, $OBJ_DOC->NO_MAX, "0", STR_PAD_LEFT);

            }
            else{
                $AUTO_NO    =   str_pad($OBJ_DOC->LAST_RECORDNO+1, $OBJ_DOC->NO_MAX, "0", STR_PAD_LEFT);
            }

            $DOC_NO         =   $PREFIX.$PRE_SEP_SLASH.$PRE_SEP_HYPEN.$AUTO_NO.$NO_SEP_SLASH.$NO_SEP_HYPEN.$SUFFIX;             
        }
        else if(isset($OBJ_DOC->MANUAL_SR) && $OBJ_DOC->MANUAL_SR == "1"){
            $READONLY   =   'readonly';
            $MAXLENGTH  =   $OBJ_DOC->MANUAL_MAXLENGTH;
        }
  
        $DOC_NO =   $DOC_NO;

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

        
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_CRV")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDFCRVID')->from('TBL_MST_UDFFOR_CRV')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                     
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                
                   

        $objUdfSOData = DB::table('TBL_MST_UDFFOR_CRV')
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
        ->where('FYID_REF','=',Session::get('FYID_REF'))
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

       

            //dd($objUdfSOData);

            $AlpsStatus =   $this->AlpsStatus();

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
       
    return view($this->view.$FormId.'add',
    compact(['AlpsStatus','FormId','objglcode','objCalculationHeader','objUdfSOData','objcurrency','objTNCHeader','objothcurrency',
    'objCurrencyconverter','objSalesPerson','objSalesQuotationAData','ObjSalesQuotationData','objCountUDF','TabSetting','doc_req','docarray','DOC_NO']));       
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
                            $row4 = '';
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
    
    /*
    public function getsubledger(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $ObjData    =   DB::select("SELECT SLID_REF,CCODE,NAME 
                        FROM TBL_MST_CUSTOMER 
                        WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND SLID_REF IS NOT NULL");

        if(!empty($ObjData)){

            foreach ($ObjData as $index=>$dataRow){
            
                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_SLID_REF[]" id="subgl_'.$dataRow->SLID_REF .'"  class="clssubgl" value="'.$dataRow->SLID_REF.'" ></td>
                <td class="ROW2">'.$dataRow->CCODE;
                $row = $row.'<input type="hidden" id="txtsubgl_'.$dataRow->SLID_REF.'" data-desc="'.$dataRow->CCODE .' - ';
                $row = $row.$dataRow->NAME. '" value="'.$dataRow->SLID_REF.'"/></td>
                <td class="ROW3">'.$dataRow->NAME.'</td></tr>';

                echo $row;
            }

        }else{

            echo '<tr><td colspan="2">Record not found.</td></tr>';

        }

        exit();

    }*/

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
        AND SLID_REF='$SLID_REF' AND BILLTO='$BILLTO_REF' AND SHIPTO='$SHIPTO_REF' AND STATUS='A'");

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

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $taxstate   =   $request['taxstate'];
        $CodeNoId   =   $request['id'];
        $StdCost    =   0;
        $AlpsStatus =   $this->AlpsStatus();

        if($CodeNoId !=""){

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
                    //$FROMQTY =  isset($ObjAltQTY[0]->FROM_QTY)? $ObjAltQTY[0]->FROM_QTY : 0;
                    $FROMQTY  =   isset($dataRow->SIMAIN_QTY)?$dataRow->SIMAIN_QTY:0;

                


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
                        $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'" data-desc1="'.$FROMQTY.'" data-desc2="'.$dataRow->RATEPUOM.'" data-desc3="'.$dataRow->DISPER.'" data-desc4="'.$dataRow->DISCOUNT_AMT.'" data-desc5="'.$CodeNoId.'-'.$dataRow->ITEMID.'" data-desc22="'.$ALPS_PART_NO.'" data-desc23="'.$CUSTOMER_PART_NO.'" data-desc24="'.$OEM_PART_NO.'"   value="'.$dataRow->ITEMID.'"/></td> <td style="width:10%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'" value="'.$dataRow->NAME.'"/></td>';
                        $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'" value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td style="width:8%;" id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'" value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                        $row = $row.'<td style="width:8%;" id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->RATEPUOM.'" value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
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
                            $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'" data-desc1="'.$FROMQTY.'" data-desc2="'.$dataRow->RATEPUOM.'" data-desc3="'.$dataRow->DISPER.'" data-desc4="'.$dataRow->DISCOUNT_AMT.'" data-desc5="'.$CodeNoId.'-'.$dataRow->ITEMID.'" data-desc22="'.$ALPS_PART_NO.'" data-desc23="'.$CUSTOMER_PART_NO.'" data-desc24="'.$OEM_PART_NO.'"  value="'.$dataRow->ITEMID.'"/></td> <td style="width:10%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'" value="'.$dataRow->NAME.'"/></td>';
                            $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'" value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
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

                

        }
        else{

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

            $sp_popup   =   [$CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART,$taxstate]; 
            $ObjItem    =   DB::select('EXEC sp_get_items_popup_withtax ?,?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);

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
                        
                        
                    //$row = '';

                
                    $DIS_PER    =   0.0000;
                    $DIS_AMT    =   0.00000;
                    $PINO       =   '';
                    $RFQID_REF  =   '';
                    $MRSID_REF  =   '';
                    $TEMP_QTY   =   '0.000';
                    $RATE       =   $STDCOST;
                    
                    /*
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
                    */


                    $CodeNoId   =   NULL;
                    $SEID_REF   =   NULL;
                    $SQID_REF   =   NULL;
                    $SOID       =   NULL;
                    $SCID_REF   =   NULL;
                   
                    $desc6      =   $CodeNoId.'-'.$ITEMID.'-'.$SEID_REF.'-'.$SQID_REF.'-'.$SOID.'-'.$SCID_REF;
                     
                    $row = '';
                       
                            
                    $row = $row.'<tr id="item_'.$ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1"  ></td>';
                    $row = $row.'<td style="width:10%;">'.$ICODE;
                    $row = $row.'<input type="hidden" id="uniquerowid_'.$desc6.'"  data-desc6="'.$desc6.'" />';
                    $row = $row.'<input type="hidden" id="txtitem_'.$ITEMID.'" data-desc="'.$ICODE.'" data-desc1="'.$FROMQTY.'" data-desc2="'.$RATE.'" data-desc3="'.$DIS_PER.'" data-desc4="'.$DIS_AMT.'" data-desc5="'.$ITEMID.'" data-desc22="'.$ALPS_PART_NO.'" data-desc23="'.$CUSTOMER_PART_NO.'" data-desc24="'.$OEM_PART_NO.'"   value="'.$ITEMID.'"/></td> <td style="width:10%;" id="itemname_'.$ITEMID.'" >'.$NAME;
                    $row = $row.'<input type="hidden" id="txtitemname_'.$ITEMID.'" data-desc="'.$ITEM_SPECI.'" value="'.$NAME.'"/></td>';
                    $row = $row.'<td style="width:8%;" id="itemuom_'.$ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ITEMID.'" data-desc="'.$Alt_UOM.'" value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>';
                    $row = $row.'<td style="width:8%;" id="uomqty_'.$ITEMID.'" ><input type="hidden" id="txtuomqty_'.$ITEMID.'" data-desc="'.$TOQTY.'" value="'.$ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                    $row = $row.'<td style="width:8%;" id="irate_'.$ITEMID.'"><input type="hidden" id="txtirate_'.$ITEMID.'" data-desc="'.$RATE.'" value="'.$STDCOST.'"/>'.$GroupName.'</td>';
                    $row = $row.'<td style="width:8%;" id="itax_'.$ITEMID.'"><input type="hidden" id="txtitax_'.$ITEMID.'" data-desc="'.$Taxid1.'" value="'.$Taxid2.'"/>'.$Categoryname.'</td> 
                    <td style="width:8%;">'.$BusinessUnit.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                    <td style="width:8%;">Authorized</td></tr>';
                        


                echo $row;

            } 
                    
        }           
        else{
            echo '<tr><td> Record not found.</td></tr>';
        }

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


    /*
    public function getItemDetailsQuotationwise(Request $request){
        $Status = "A";
        $id = $request['id'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $taxstate = $request['taxstate'];  
        $contains = Str::contains($id, 'A');

        if($contains)
        {
            $QuoteID = DB::select('SELECT * FROM TBL_TRN_SLQT02_HDR
                        WHERE SQANO = ? AND CYID_REF = ? AND BRID_REF = ? AND FYID_REF = ?
                        AND ',
                        [$id,$CYID_REF,$BRID_REF,$FYID_REF]);
            
            $SQAID = $QuoteID[0]->SQAID;

            $Objquote =  DB::select('SELECT * FROM TBL_TRN_SLQT02_MAT  
            WHERE  PENDING_QTY > ? AND SQAID_REF = ? order by ITEMID_REF ASC', ['0.000',$SQAID]);
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
                                                // echo($ObjStdCost);
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
                                                // echo($ObjLIST->LISTPRICE);
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
                                WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $BRID_REF, $FYID_REF,$dataRow->MAIN_UOMID_REF, $Status ]);

                    $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $BRID_REF, $FYID_REF,$dataRow->ALT_UOMID_REF, $Status ]);
                    
                    $ObjAltQTY =  DB::select('SELECT TOP 1  * FROM TBL_MST_ITEM_UOMCONV  
                                WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                                [$dataRow->ITEMID_REF,$dataRow->ALT_UOMID_REF ]);
                    
                    $TOQTY =  isset($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 0;
                    $FROMQTY =  isset($ObjAltQTY[0]->FROM_QTY)? $ObjAltQTY[0]->FROM_QTY : 0;

                    $ObjItemGroup =  DB::select('SELECT TOP 1 GROUPCODE, GROUPNAME FROM TBL_MST_ITEMGROUP  
                                WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ITEMGID = ?
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                [$CYID_REF, $BRID_REF, $FYID_REF,$ObjItem[0]->ITEMGID_REF, $Status ]);

                    $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS FROM TBL_MST_ITEMCATEGORY  
                                WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ICID = ?
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                [$CYID_REF, $BRID_REF, $FYID_REF,$ObjItem[0]->ICID_REF, $Status ]);
                    
                    
                    $row = '';
                    if($taxstate != "OutofState"){
                        $row = $row.'<tr id="item_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"  class="clsitemid"><td  style="width:6.5%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td>'.$ObjItem[0]->ICODE;
                        $row = $row.'<input type="hidden" id="txtitem_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ICODE.'"
                        value="'.$ObjItem[0]->ITEMID.'"/></td><td id="itemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" >'.$ObjItem[0]->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ITEM_SPECI.'"
                        value="'.$ObjItem[0]->NAME.'"/></td>';
                        $row = $row.'<td id="itemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtitemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                        value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td id="uomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtuomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$TOQTY.'"
                        value="'.$dataRow->ALT_UOMID_REF.'"/>'.$dataRow->SQ_QTY.'</td>';
                        $row = $row.'<td id="irate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtirate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$FROMQTY.'"
                        value="'.$dataRow->RATEPUOM.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                        $row = $row.'<td id="itax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$Taxid[0].'"
                        value="'.$Taxid[1].'"/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                        <td id="ise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"  data-desc="'.$dataRow->SEQID_REF.'"
                        value="'.$QuoteID[0]->SQNO.'"/>Authorized</td>
                        </tr>';
                        }
                        else
                        {
                        $row = $row.'<tr id="item_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"  class="clsitemid"><td  style="width:6.5%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td>'.$ObjItem[0]->ICODE;
                        $row = $row.'<input type="hidden" id="txtitem_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ICODE.'"
                        value="'.$ObjItem[0]->ITEMID.'"/></td><td id="itemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" >'.$ObjItem[0]->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ITEM_SPECI.'"
                        value="'.$ObjItem[0]->NAME.'"/></td>';
                        $row = $row.'<td id="itemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtitemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                        value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td id="uomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtuomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$TOQTY.'"
                        value="'.$dataRow->ALT_UOMID_REF.'"/>'.$dataRow->SQ_QTY.'</td>';
                        $row = $row.'<td id="irate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtirate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$FROMQTY.'"
                        value="'.$dataRow->RATEPUOM.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                        $row = $row.'<td id="itax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$Taxid[0].'"
                        value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                        <td id="ise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$dataRow->SEQID_REF.'"
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
                                                        $StdCost = $ObjLIST->LISTPRICE;
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
                                                    $StdCost = $dataRow->RATEPUOM;
                                                }
                        
                            $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                        WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND UOMID = ? 
                                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                        [$CYID_REF, $BRID_REF, $FYID_REF,$dataRow->MAIN_UOMID_REF, $Status ]);

                            $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                        WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND UOMID = ? 
                                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                        [$CYID_REF, $BRID_REF, $FYID_REF,$dataRow->ALT_UOMID_REF, $Status ]);
                            
                            $ObjAltQTY =  DB::select('SELECT TOP 1  * FROM TBL_MST_ITEM_UOMCONV  
                                        WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                                        [$dataRow->ITEMID_REF,$dataRow->ALT_UOMID_REF ]);
                            
                            $TOQTY =  isset($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 0;
                            $FROMQTY =  isset($ObjAltQTY[0]->FROM_QTY)? $ObjAltQTY[0]->FROM_QTY : 0;

                            $ObjItemGroup =  DB::select('SELECT TOP 1 GROUPCODE, GROUPNAME FROM TBL_MST_ITEMGROUP  
                                        WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ITEMGID = ?
                                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                        [$CYID_REF, $BRID_REF, $FYID_REF,$ObjItem[0]->ITEMGID_REF, $Status ]);

                            $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS FROM TBL_MST_ITEMCATEGORY  
                                        WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ICID = ?
                                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                        [$CYID_REF, $BRID_REF, $FYID_REF,$ObjItem[0]->ICID_REF, $Status ]);
                        
                        
                        
                            $row = '';
                            if($taxstate != "OutofState"){
                            $row = $row.'<tr id="item_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"  class="clsitemid"><td  style="width:6.5%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                            $row = $row.'<td>'.$ObjItem[0]->ICODE;
                            $row = $row.'<input type="hidden" id="txtitem_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ICODE.'"
                            value="'.$ObjItem[0]->ITEMID.'"/></td><td id="itemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" >'.$ObjItem[0]->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ITEM_SPECI.'"
                            value="'.$ObjItem[0]->NAME.'"/></td>';
                            $row = $row.'<td id="itemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtitemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                            value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                            $row = $row.'<td id="uomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtuomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$TOQTY.'"
                            value="'.$dataRow->ALT_UOMID_REF.'"/>'.$dataRow->SQ_QTY.'</td>';
                            $row = $row.'<td id="irate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtirate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$FROMQTY.'"
                            value="'.$dataRow->RATEPUOM.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                            $row = $row.'<td id="itax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$Taxid[0].'"
                            value="'.$Taxid[1].'"/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                            <td id="ise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"  data-desc="'.$dataRow->SEQID_REF.'"
                            value="'.$QuoteID[0]->SQNO.'"/>Authorized</td>
                            </tr>';
                            }
                            else
                            {
                            $row = $row.'<tr id="item_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"  class="clsitemid"><td  style="width:6.5%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                            $row = $row.'<td>'.$ObjItem[0]->ICODE;
                            $row = $row.'<input type="hidden" id="txtitem_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ICODE.'"
                            value="'.$ObjItem[0]->ITEMID.'"/></td><td id="itemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" >'.$ObjItem[0]->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjItem[0]->ITEM_SPECI.'"
                            value="'.$ObjItem[0]->NAME.'"/></td>';
                            $row = $row.'<td id="itemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtitemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                            value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                            $row = $row.'<td id="uomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtuomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$TOQTY.'"
                            value="'.$dataRow->ALT_UOMID_REF.'"/>'.$dataRow->SQ_QTY.'</td>';
                            $row = $row.'<td id="irate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtirate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$FROMQTY.'"
                            value="'.$dataRow->RATEPUOM.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                            $row = $row.'<td id="itax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$Taxid[0].'"
                            value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                            <td id="ise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'"><input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SEQID_REF.'" data-desc="'.$dataRow->SEQID_REF.'"
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
    
    }*/

    public function getItemDetailswithoutQuotation(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $taxstate = $request['taxstate'];
        $StdCost = 0;
        
                
        $ObjItem =  DB::select('SELECT * FROM TBL_MST_ITEM  
                    WHERE CYID_REF = ? AND BRID_REF = ?   
                    AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                    [$CYID_REF, $BRID_REF,  $Status ]);
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
                        $row = $row.'<input type="hidden" id="txtbillto_'.$dataRow->CLID.'" data-desc="'.$objAddress.'" value="'.$dataRow->CLID.'"/></td>
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

        $r_count1           =   $request['Row_Count1'];
        $r_count3           =   $request['Row_Count3'];
        $r_count4           =   $request['Row_Count4'];
        $TRANSACTION_TYPE   =   $request['TRANSACTION_TYPE'];

        $GROSS_TOTAL    =   0; 
        $NET_TOTAL 		= 	$request['TotalValue'];
        $CGSTAMT        =   0; 
        $SGSTAMT        =   0; 
        $IGSTAMT        =   0; 
        $DISCOUNT       =   0; 
        $OTHER_CHARGES  =   0; 
        $FC 			= (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF 		= (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT 		= (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";

       
        for ($i=0; $i<=$r_count1; $i++){

            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i])){

                $StoreArr   =   array();
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];

                if($ITEMROWID !=""){
                    $exp        =   explode(",",$ITEMROWID);
                    foreach($exp as $val){
                        $keyid      =   explode("_",$val);
                        $batchid    =   $keyid[0];

                        if($TRANSACTION_TYPE =="CREDIT"){
                            $storeid        =   $batchid;
                        }
                        else if($TRANSACTION_TYPE =="DEBIT"){

                            $objBatch =  DB::SELECT("SELECT BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF,SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) AS STOCK 
                            FROM TBL_MST_BATCH 
                            WHERE BATCHID='$batchid'
                            GROUP BY BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF 
                            HAVING SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) > '0.000'
                            ")[0];

                            $storeid        =   $objBatch->STID_REF;

                        }
                        else{
                            $storeid        =   NULL;
                        }

                        $StoreArr[] =   $storeid;
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


                $GROSS_TOTAL+= $request['DISAFTT_AMT_'.$i]; 
                $CGSTAMT+= $request['CGSTAMT_'.$i]; 
                $SGSTAMT+= $request['SGSTAMT_'.$i]; 
                $IGSTAMT+= $request['IGSTAMT_'.$i]; 
                $DISCOUNT+= $request['DISCOUNT_AMT_'.$i]; 
                
                $req_data[$i] = [
                    'SIID_REF' => isset($request['SQA_'.$i]) && $request['SQA_'.$i] !=""?$request['SQA_'.$i]:'' ,
                    'ITEMID_REF'    => isset($request['ITEMID_REF_'.$i]) && $request['ITEMID_REF_'.$i] !=""?$request['ITEMID_REF_'.$i]:'',
                    'UOMID_REF' => isset($request['MAIN_UOMID_REF_'.$i]) && $request['MAIN_UOMID_REF_'.$i] !=""?$request['MAIN_UOMID_REF_'.$i]:'',
                    'SI_QTY' => isset($request['SQMUOMQTY_'.$i]) && $request['SQMUOMQTY_'.$i] !=""?$request['SQMUOMQTY_'.$i]:0,
                    'SI_RATE' => isset($request['SI_RATE_'.$i]) && $request['SI_RATE_'.$i] !=""?$request['SI_RATE_'.$i]:0,
                    'STID' => isset($STID_REF) && $STID_REF !=""?$STID_REF:'',
                    'CR_NOTE_QTY' => $request['SO_QTY_'.$i] !=""?$request['SO_QTY_'.$i]:0,
                    'CR_NOTE_RATE' => $request['RATEPUOM_'.$i] !=""?$request['RATEPUOM_'.$i]:0,
                    'CR_NOTE_AMT' => $request['DISAFTT_AMT_'.$i] !=""?$request['DISAFTT_AMT_'.$i]:0,
                    'GST' =>  (isset($request['flagtype_'.$i])!="true" ? 0 : 1) ,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'BATCH_QTY'   => $request['HiddenRowId_'.$i] !=""?$request['HiddenRowId_'.$i]:'',
                    'SEID_REF'     => isset($SEID_REF) && $SEID_REF !=""?$SEID_REF:'',
                    'SQID_REF'     => isset($SQID_REF) && $SQID_REF !=""?$SQID_REF:'',
                    'SOID_REF'     => isset($SO) && $SO !=""?$SO:'',
                    'SCID_REF'     => isset($SCID_REF) && $SCID_REF !=""?$SCID_REF:'',
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
                        'UDFCRVID_REF'   => $request['UDFSOID_REF_'.$i],
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

        

        $req_data33=array();
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
                $SEID_REF   =   intval($Field_Id[2]);
                $SQID_REF   =   intval($Field_Id[3]);
                $SO         =   intval($Field_Id[4]);
                $SCID_REF   =   intval($Field_Id[5]);


                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        if($TRANSACTION_TYPE =="CREDIT"){
                            $BATCHNO        =   NULL;
                            $SERIALNO       =   NULL;
                            $STID_REF       =   $key;
                            $MAINUOMID_REF  =   NULL;
                            $STOCK_INHAND   =   NULL;
                        }
                        else if($TRANSACTION_TYPE =="DEBIT"){

                            $objBatch =  DB::SELECT("SELECT BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF,SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) AS STOCK 
                            FROM TBL_MST_BATCH 
                            WHERE BATCHID='$key'
                            GROUP BY BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF 
                            HAVING SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) > '0.000'
                            ")[0];

                            $BATCHNO        =   $objBatch->BATCH_CODE;
                            $SERIALNO       =   NULL;
                            $STID_REF       =   $objBatch->STID_REF;
                            $MAINUOMID_REF  =   $objBatch->UOMID_REF;
                            $STOCK_INHAND   =   $objBatch->STOCK;
                        }
                        else{
                            $BATCHNO        =   NULL;
                            $SERIALNO       =   NULL;
                            $STID_REF       =   NULL;
                            $MAINUOMID_REF  =   NULL;
                            $STOCK_INHAND   =   NULL;
                        }
                       
                        $req_data33[$i][] = [
                            'SIID_REF'      => isset($request['SQA_'.$i]) && $request['SQA_'.$i] !=""?$request['SQA_'.$i]:'',
                            'ITEMID_REF'    => isset($ITEMID_REF) && $ITEMID_REF !=""?$ITEMID_REF:'',
                            'BATCH_NO'      => isset($BATCHNO) && $BATCHNO !=""?$BATCHNO:'',
                            'STID_REF'      => isset($STID_REF) && $STID_REF !=""?intval($STID_REF):'',
                            'SERIAL_NO'     => isset($SERIALNO) && $SERIALNO !=""?$SERIALNO:'',
                            'UOMID_REF'     => isset($MAINUOMID_REF) && $MAINUOMID_REF !=""?$MAINUOMID_REF:'',
                            'STOCK_INHAND'  => isset($STOCK_INHAND) && $STOCK_INHAND !=''?floatval($STOCK_INHAND):0,
                            'QTY'           => isset($val) && $val !=""?$val:0,
                            'SEID_REF'      => isset($SEID_REF) && $SEID_REF !=""?$SEID_REF:'',
                            'SQID_REF'      => isset($SQID_REF) && $SQID_REF !=""?$SQID_REF:'',
                            'SOID_REF'      => isset($SO) && $SO !=""?$SO:'',
                            'SCID_REF'      => isset($SCID_REF) && $SCID_REF !=""?$SCID_REF:'',
                        ];

                    }
                }
            }
        }

        if(isset($req_data33) && !empty($req_data33)){
            $wrapped_links33["STORE"] = $req_data33; 
            $XMLSTORE = ArrayToXml::convert($wrapped_links33);
        }
        else{
            $XMLSTORE = NULL;
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

        $CSV_NO     = $request['CSV_NO'];
        $CSV_DT     = $request['CSV_DT'];
        $SLID_REF   = $request['SLID_REF'];        
        $BILLTO     = $request['BILLTO'];
        $SHIPTO     = $request['SHIPTO'];
        $REASONOFSR = $request['REASONOFSR'];
        $COMMONNRT  = $request['COMMONNRT'];
        $COMMONNRT  = $request['Direct'];
        $Direct     = (isset($request['Direct'])!="true" ? 0 : 1);
        

        $log_data = [ 
            $CSV_NO,$CSV_DT,$SLID_REF, $REASONOFSR,$COMMONNRT,$BILLTO,$SHIPTO,$CYID_REF, $BRID_REF,$FYID_REF,
            $VTID_REF,$XMLMAT,$XMLSTORE,$XMLUDF,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$Direct,$XMLCAL,$TRANSACTION_TYPE
            ,$FC,$CRID_REF,$CONVFACT,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES
        ];

        $sp_result = DB::select('EXEC SP_CSV_IN ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?, ?, ?,?,?,?,?, ?,?,?,?,?', $log_data); 

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
        

        $COL_APP_STATUS =   'STATUS';  //never change value, value must be 'APPROVED_STATUS' as per stored procedure;
      
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
                //ALL COLUMN
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

            $totalRows = 0;       //total no of records
            $totalFiltered = 0;   // total filtered count

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
            // dd($data);
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

            $objSO = DB::table('TBL_TRN_CRSV01_HDR')
            ->where('TBL_TRN_CRSV01_HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_CRSV01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_CRSV01_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_CRSV01_HDR.CSVID','=',$id)
            ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_CRSV01_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
            ->select('TBL_TRN_CRSV01_HDR.*','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
            ->first();

            $objSOMAT = DB::table('TBL_TRN_CRSV01_MAT')  
            ->leftJoin('TBL_TRN_SLSI01_HDR', 'TBL_TRN_CRSV01_MAT.SIID_REF','=','TBL_TRN_SLSI01_HDR.SIID')  
            ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_CRSV01_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
            ->leftJoin('TBL_MST_UOM', 'TBL_TRN_CRSV01_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')
            ->leftJoin('TBL_TRN_SLSI01_MAT', function($join){
                $join->on('TBL_TRN_CRSV01_MAT.SIID_REF', '=', 'TBL_TRN_SLSI01_MAT.SIID_REF')
                     ->on('TBL_TRN_CRSV01_MAT.ITEMID_REF', '=', 'TBL_TRN_SLSI01_MAT.ITEMID_REF')
                     ->on('TBL_TRN_CRSV01_MAT.SCID_REF', '=', 'TBL_TRN_SLSI01_MAT.SCID_REF')
                     ->on('TBL_TRN_CRSV01_MAT.SOID_REF', '=', 'TBL_TRN_SLSI01_MAT.SOID')
                     ->on('TBL_TRN_CRSV01_MAT.SQID_REF', '=', 'TBL_TRN_SLSI01_MAT.SQID_REF')
                     ->on('TBL_TRN_CRSV01_MAT.SEID_REF', '=', 'TBL_TRN_SLSI01_MAT.SEID_REF');
            })              
            ->where('TBL_TRN_CRSV01_MAT.CSVID_REF','=',$id)
            ->select(
                'TBL_TRN_CRSV01_MAT.*',
                'TBL_TRN_SLSI01_HDR.SINO',
                'TBL_TRN_SLSI01_HDR.SIDT',
                'TBL_MST_ITEM.ICODE',
                'TBL_MST_ITEM.NAME AS ITEM_NAME',
                'TBL_MST_ITEM.ALPS_PART_NO',
                'TBL_MST_ITEM.CUSTOMER_PART_NO',
                'TBL_MST_ITEM.OEM_PART_NO',
                'TBL_MST_ITEM.MAIN_UOMID_REF',
                'TBL_MST_ITEM.ALT_UOMID_REF',
                'TBL_TRN_SLSI01_MAT.SIMAIN_QTY',
                'TBL_TRN_SLSI01_MAT.SQID_REF',
                'TBL_TRN_SLSI01_MAT.SOID',
                'TBL_TRN_SLSI01_MAT.SCID_REF',
                'TBL_TRN_SLSI01_MAT.SEID_REF',
                'TBL_MST_UOM.UOMCODE',
                'TBL_MST_UOM.DESCRIPTIONS'
               
                )

            ->orderBy('TBL_TRN_CRSV01_MAT.CSV_MATID','ASC')
            ->get()->toArray();
            $objCount1 = count($objSOMAT);

            $objSOTNC=array();
            $objCount2 = count($objSOTNC);

            $objSOUDF = DB::table('TBL_TRN_CRSV01_UDF')                    
            ->where('TBL_TRN_CRSV01_UDF.CSVID_REF','=',$id)
            ->select('TBL_TRN_CRSV01_UDF.*')
            ->orderBy('TBL_TRN_CRSV01_UDF.CSV_UDFID','ASC')
            ->get()->toArray();
            $objCount3 = count($objSOUDF);

            $objSOCAL = DB::table('TBL_TRN_CRSV01_CAL')                    
            ->where('TBL_TRN_CRSV01_CAL.CSVID_REF','=',$id)
            ->select('TBL_TRN_CRSV01_CAL.*')
            ->orderBy('TBL_TRN_CRSV01_CAL.CSVCALID','ASC')
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

            $objEMP=array();
            $objSPID=NULL;

            $objglcode=[];
            $objsubglcode=[];

        if(isset($objSO->SGLID_REF) && $objSO->SGLID_REF !=""){
            $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('BELONGS_TO','=','Customer')
            ->where('SGLID','=',$objSO->SGLID_REF)
            ->select('TBL_MST_SUBLEDGER.SGLCODE AS CCODE','TBL_MST_SUBLEDGER.SLNAME As NAME')
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

        $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')->get() ->toArray(); 
        $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')->get() ->toArray(); 


        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_CRV")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                {       
                                $query->select('UDFCRVID')->from('TBL_MST_UDFFOR_CRV')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                    
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                        
                    
    
        $objUdfSOData = DB::table('TBL_MST_UDFFOR_CRV')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray(); 
        
        $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_CRV")->select('*')
        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                    {       
                    $query->select('UDFCRVID')->from('TBL_MST_UDFFOR_CRV')
                                    ->where('PARENTID','=',0)
                                    ->where('DEACTIVATED','=',0)
                                    ->where('CYID_REF','=',$CYID_REF);
                                                        
        })->where('DEACTIVATED','=',0)
        ->where('CYID_REF','=',$CYID_REF);
                            
        $objUdfSOData2 = DB::table('TBL_MST_UDFFOR_CRV')
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

        $d_currency = DB::table('TBL_MST_COMPANY')
        ->where('STATUS','=',$Status)
        ->where('CYID','=',Auth::user()->CYID_REF)
        ->select('TBL_MST_COMPANY.CRID_REF')
        ->first();
        $objcurrency = $d_currency->CRID_REF;

        $objothcurrency = DB::table('TBL_MST_CURRENCY')
        ->where('STATUS','=',$Status)
        ->where('CRID','<>',$objcurrency)
        ->select('TBL_MST_CURRENCY.*')
        ->get()
        ->toArray();
    
        $ObjSalesQuotationData = DB::table("TBL_TRN_SLSI01_HDR")->select('*')
                    ->where('STATUS','=','A')                    
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF)
                    ->where('FYID_REF','=',$FYID_REF) ->get() ->toArray(); 
                    
        $FormId     =   $this->form_id;
        $AlpsStatus =   $this->AlpsStatus();

        $ActionStatus   =   "";

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        return view($this->view.$FormId.'edit',compact(['AlpsStatus','FormId','objSO','objRights','objCount1','objSPID',
           'objCount2','objCount3','objCount4','objCount5','objSOMAT','objSOCAL','objSOTNC','objSOUDF','objSOPSLB',
           'objglcode','objUdfSOData','objglcode2','objCalculationHeader','objCalHeader','objCalDetails','objothcurrency',
           'objCurrencyconverter','objSalesPerson','ObjSalesQuotationData','objsubglcode',
           'objShpAddress','objBillAddress','objUdfSOData2',
           'TAXSTATE','ActionStatus','TabSetting']));
        }
     
    }
     
    public function view($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){

            $objSO = DB::table('TBL_TRN_CRSV01_HDR')
            ->where('TBL_TRN_CRSV01_HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_CRSV01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_CRSV01_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_CRSV01_HDR.CSVID','=',$id)
            ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_CRSV01_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
            ->select('TBL_TRN_CRSV01_HDR.*','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
            ->first();

            $objSOMAT = DB::table('TBL_TRN_CRSV01_MAT')  
            ->leftJoin('TBL_TRN_SLSI01_HDR', 'TBL_TRN_CRSV01_MAT.SIID_REF','=','TBL_TRN_SLSI01_HDR.SIID')  
            ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_CRSV01_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
            ->leftJoin('TBL_MST_UOM', 'TBL_TRN_CRSV01_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')
            ->leftJoin('TBL_TRN_SLSI01_MAT', function($join){
                $join->on('TBL_TRN_CRSV01_MAT.SIID_REF', '=', 'TBL_TRN_SLSI01_MAT.SIID_REF')
                     ->on('TBL_TRN_CRSV01_MAT.ITEMID_REF', '=', 'TBL_TRN_SLSI01_MAT.ITEMID_REF')
                     ->on('TBL_TRN_CRSV01_MAT.SCID_REF', '=', 'TBL_TRN_SLSI01_MAT.SCID_REF')
                     ->on('TBL_TRN_CRSV01_MAT.SOID_REF', '=', 'TBL_TRN_SLSI01_MAT.SOID')
                     ->on('TBL_TRN_CRSV01_MAT.SQID_REF', '=', 'TBL_TRN_SLSI01_MAT.SQID_REF')
                     ->on('TBL_TRN_CRSV01_MAT.SEID_REF', '=', 'TBL_TRN_SLSI01_MAT.SEID_REF');
            })              
            ->where('TBL_TRN_CRSV01_MAT.CSVID_REF','=',$id)
            ->select(
                'TBL_TRN_CRSV01_MAT.*',
                'TBL_TRN_SLSI01_HDR.SINO',
                'TBL_TRN_SLSI01_HDR.SIDT',
                'TBL_MST_ITEM.ICODE',
                'TBL_MST_ITEM.NAME AS ITEM_NAME',
                'TBL_MST_ITEM.ALPS_PART_NO',
                'TBL_MST_ITEM.CUSTOMER_PART_NO',
                'TBL_MST_ITEM.OEM_PART_NO',
                'TBL_MST_ITEM.MAIN_UOMID_REF',
                'TBL_MST_ITEM.ALT_UOMID_REF',
                'TBL_TRN_SLSI01_MAT.SIMAIN_QTY',
                'TBL_TRN_SLSI01_MAT.SQID_REF',
                'TBL_TRN_SLSI01_MAT.SOID',
                'TBL_TRN_SLSI01_MAT.SCID_REF',
                'TBL_TRN_SLSI01_MAT.SEID_REF',
                'TBL_MST_UOM.UOMCODE',
                'TBL_MST_UOM.DESCRIPTIONS'
               
                )

            ->orderBy('TBL_TRN_CRSV01_MAT.CSV_MATID','ASC')
            ->get()->toArray();
            $objCount1 = count($objSOMAT);

            $objSOTNC=array();
            $objCount2 = count($objSOTNC);

            $objSOUDF = DB::table('TBL_TRN_CRSV01_UDF')                    
            ->where('TBL_TRN_CRSV01_UDF.CSVID_REF','=',$id)
            ->select('TBL_TRN_CRSV01_UDF.*')
            ->orderBy('TBL_TRN_CRSV01_UDF.CSV_UDFID','ASC')
            ->get()->toArray();
            $objCount3 = count($objSOUDF);

            $objSOCAL = DB::table('TBL_TRN_CRSV01_CAL')                    
            ->where('TBL_TRN_CRSV01_CAL.CSVID_REF','=',$id)
            ->select('TBL_TRN_CRSV01_CAL.*')
            ->orderBy('TBL_TRN_CRSV01_CAL.CSVCALID','ASC')
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

            $objEMP=array();
            $objSPID=NULL;

            $objglcode=[];
            $objsubglcode=[];

        if(isset($objSO->SGLID_REF) && $objSO->SGLID_REF !=""){
            $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('BELONGS_TO','=','Customer')
            ->where('SGLID','=',$objSO->SGLID_REF)
            ->select('TBL_MST_SUBLEDGER.SGLCODE AS CCODE','TBL_MST_SUBLEDGER.SLNAME As NAME')
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

        $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')->get() ->toArray(); 
        $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')->get() ->toArray(); 


        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_CRV")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                {       
                                $query->select('UDFCRVID')->from('TBL_MST_UDFFOR_CRV')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                    
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                        
                    
    
        $objUdfSOData = DB::table('TBL_MST_UDFFOR_CRV')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray(); 
        
        $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_CRV")->select('*')
        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                    {       
                    $query->select('UDFCRVID')->from('TBL_MST_UDFFOR_CRV')
                                    ->where('PARENTID','=',0)
                                    ->where('DEACTIVATED','=',0)
                                    ->where('CYID_REF','=',$CYID_REF);
                                                        
        })->where('DEACTIVATED','=',0)
        ->where('CYID_REF','=',$CYID_REF);
                            
        $objUdfSOData2 = DB::table('TBL_MST_UDFFOR_CRV')
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
        
        $d_currency = DB::table('TBL_MST_COMPANY')
        ->where('STATUS','=',$Status)
        ->where('CYID','=',Auth::user()->CYID_REF)
        ->select('TBL_MST_COMPANY.CRID_REF')
        ->first();
        $objcurrency = $d_currency->CRID_REF;

        $objothcurrency = DB::table('TBL_MST_CURRENCY')
        ->where('STATUS','=',$Status)
        ->where('CRID','<>',$objcurrency)
        ->select('TBL_MST_CURRENCY.*')
        ->get()
        ->toArray();
    
        $ObjSalesQuotationData = DB::table("TBL_TRN_SLSI01_HDR")->select('*')
                    ->where('STATUS','=','A')                    
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF)
                    ->where('FYID_REF','=',$FYID_REF) ->get() ->toArray(); 
                    
        $FormId     =   $this->form_id;
        $AlpsStatus =   $this->AlpsStatus();

        $ActionStatus   =   "disabled";

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        return view($this->view.$FormId.'view',compact(['AlpsStatus','FormId','objSO','objRights','objCount1','objSPID',
           'objCount2','objCount3','objCount4','objCount5','objSOMAT','objSOCAL','objSOTNC','objSOUDF','objSOPSLB',
           'objglcode','objUdfSOData','objglcode2','objCalculationHeader','objCalHeader','objCalDetails','objothcurrency',
           'objCurrencyconverter','objSalesPerson','ObjSalesQuotationData','objsubglcode',
           'objShpAddress','objBillAddress','objUdfSOData2',
           'TAXSTATE','ActionStatus','TabSetting']));
        }
     
    }

    
    public function update(Request $request){

        $r_count1           =   $request['Row_Count1'];
        $r_count3           =   $request['Row_Count3'];
        $r_count4           =   $request['Row_Count4'];
        $TRANSACTION_TYPE   =   $request['TRANSACTION_TYPE'];
        $GROSS_TOTAL        =   0; 
        $NET_TOTAL 		    = 	$request['TotalValue'];
        $CGSTAMT            =   0; 
        $SGSTAMT            =   0; 
        $IGSTAMT            =   0; 
        $DISCOUNT           =   0; 
        $OTHER_CHARGES      =   0; 
        $FC 			    = (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF 		    = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT 		    = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
    
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i]))
            {

                $StoreArr   =   array();
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];

                if($ITEMROWID !=""){

                    $exp        =   explode(",",$ITEMROWID);

                    foreach($exp as $val){
                        $keyid              =   explode("_",$val);
                        $batchid            =   intval($keyid[0]);

                        if($TRANSACTION_TYPE =="CREDIT"){
                            $storeid        =   $batchid;
                        }
                        else if($TRANSACTION_TYPE =="DEBIT"){

                            $objBatch =  DB::SELECT("SELECT BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF,SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) AS STOCK 
                            FROM TBL_MST_BATCH 
                            WHERE BATCHID='$batchid'
                            GROUP BY BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF 
                            HAVING SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) > '0.000'
                            ")[0];

                            $storeid        =   $objBatch->STID_REF;

                        }
                        else{
                            $storeid        =   NULL;
                        }

                        $StoreArr[] =   $storeid;
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

                $GROSS_TOTAL+= $request['DISAFTT_AMT_'.$i]; 
                $CGSTAMT+= $request['CGSTAMT_'.$i]; 
                $SGSTAMT+= $request['SGSTAMT_'.$i]; 
                $IGSTAMT+= $request['IGSTAMT_'.$i]; 
                $DISCOUNT+= $request['DISCOUNT_AMT_'.$i]; 

                $req_data[$i] = [
                    'SIID_REF' => isset($request['SQA_'.$i]) && $request['SQA_'.$i] !=""?$request['SQA_'.$i]:'' ,
                    'ITEMID_REF'    => isset($request['ITEMID_REF_'.$i]) && $request['ITEMID_REF_'.$i] !=""?$request['ITEMID_REF_'.$i]:'',
                    'UOMID_REF' => isset($request['MAIN_UOMID_REF_'.$i]) && $request['MAIN_UOMID_REF_'.$i] !=""?$request['MAIN_UOMID_REF_'.$i]:'',
                    'SI_QTY' => isset($request['SQMUOMQTY_'.$i]) && $request['SQMUOMQTY_'.$i] !=""?$request['SQMUOMQTY_'.$i]:0,
                    'SI_RATE' => isset($request['SI_RATE_'.$i]) && $request['SI_RATE_'.$i] !=""?$request['SI_RATE_'.$i]:0,
                    'STID' => isset($STID_REF) && $STID_REF !=""?$STID_REF:'',
                    'CR_NOTE_QTY' => $request['SO_QTY_'.$i] !=""?$request['SO_QTY_'.$i]:0,
                    'CR_NOTE_RATE' => $request['RATEPUOM_'.$i] !=""?$request['RATEPUOM_'.$i]:0,
                    'CR_NOTE_AMT' => $request['DISAFTT_AMT_'.$i] !=""?$request['DISAFTT_AMT_'.$i]:0,
                    'GST' =>  (isset($request['flagtype_'.$i])!="true" ? 0 : 1) ,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'BATCH_QTY'   => $request['HiddenRowId_'.$i] !=""?$request['HiddenRowId_'.$i]:'',
                    'SEID_REF'     => isset($SEID_REF) && $SEID_REF !=""?$SEID_REF:'',
                    'SQID_REF'     => isset($SQID_REF) && $SQID_REF !=""?$SQID_REF:'',
                    'SOID_REF'     => isset($SO) && $SO !=""?$SO:'',
                    'SCID_REF'     => isset($SCID_REF) && $SCID_REF !=""?$SCID_REF:'',
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
                        'UDFCRVID_REF'   => $request['UDFSOID_REF_'.$i],
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

        
        $req_data33=array();
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
                $SEID_REF   =   intval($Field_Id[2]);
                $SQID_REF   =   intval($Field_Id[3]);
                $SO         =   intval($Field_Id[4]);
                $SCID_REF   =   intval($Field_Id[5]);


                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        if($TRANSACTION_TYPE =="CREDIT"){
                            $BATCHNO        =   NULL;
                            $SERIALNO       =   NULL;
                            $STID_REF       =   $key;
                            $MAINUOMID_REF  =   NULL;
                            $STOCK_INHAND   =   NULL;
                        }
                        else if($TRANSACTION_TYPE =="DEBIT"){

                            $objBatch =  DB::SELECT("SELECT BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF,SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) AS STOCK 
                            FROM TBL_MST_BATCH 
                            WHERE BATCHID='$key'
                            GROUP BY BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF 
                            HAVING SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) > '0.000'
                            ")[0];

                            $BATCHNO        =   $objBatch->BATCH_CODE;
                            $SERIALNO       =   NULL;
                            $STID_REF       =   $objBatch->STID_REF;
                            $MAINUOMID_REF  =   $objBatch->UOMID_REF;
                            $STOCK_INHAND   =   $objBatch->STOCK;
                        }
                        else{
                            $BATCHNO        =   NULL;
                            $SERIALNO       =   NULL;
                            $STID_REF       =   NULL;
                            $MAINUOMID_REF  =   NULL;
                            $STOCK_INHAND   =   NULL;
                        }
                       
                        $req_data33[$i][] = [
                            'SIID_REF'      => isset($request['SQA_'.$i]) && $request['SQA_'.$i] !=""?$request['SQA_'.$i]:'',
                            'ITEMID_REF'    => isset($ITEMID_REF) && $ITEMID_REF !=""?$ITEMID_REF:'',
                            'BATCH_NO'      => isset($BATCHNO) && $BATCHNO !=""?$BATCHNO:'',
                            'STID_REF'      => isset($STID_REF) && $STID_REF !=""?intval($STID_REF):'',
                            'SERIAL_NO'     => isset($SERIALNO) && $SERIALNO !=""?$SERIALNO:'',
                            'UOMID_REF'     => isset($MAINUOMID_REF) && $MAINUOMID_REF !=""?$MAINUOMID_REF:'',
                            'STOCK_INHAND'  => isset($STOCK_INHAND) && $STOCK_INHAND !=''?floatval($STOCK_INHAND):0,
                            'QTY'           => isset($val) && $val !=""?$val:0,
                            'SEID_REF'      => isset($SEID_REF) && $SEID_REF !=""?$SEID_REF:'',
                            'SQID_REF'      => isset($SQID_REF) && $SQID_REF !=""?$SQID_REF:'',
                            'SOID_REF'      => isset($SO) && $SO !=""?$SO:'',
                            'SCID_REF'      => isset($SCID_REF) && $SCID_REF !=""?$SCID_REF:'',
                        ];

                    }
                }
            }
        }

        if(isset($req_data33) && $req_data33 !=""){
            $wrapped_links33["STORE"] = $req_data33; 
            $XMLSTORE = ArrayToXml::convert($wrapped_links33);
        }
        else{
            $XMLSTORE = NULL;
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

        $CSV_NO     = $request['CSV_NO'];
        $CSV_DT     = $request['CSV_DT'];
        $SLID_REF   = $request['SLID_REF'];        
        $BILLTO     = $request['BILLTO'];
        $SHIPTO     = $request['SHIPTO'];
        $REASONOFSR = $request['REASONOFSR'];
        $COMMONNRT  = $request['COMMONNRT'];
        $Direct     = (isset($request['Direct'])!="true" ? 0 : 1);

        $log_data = [ 
            $CSV_NO,$CSV_DT,$SLID_REF, $REASONOFSR,$COMMONNRT,$BILLTO,$SHIPTO,$CYID_REF, $BRID_REF,$FYID_REF,
            $VTID_REF,$XMLMAT,$XMLSTORE,$XMLUDF,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$Direct,$XMLCAL,$TRANSACTION_TYPE
            ,$FC,$CRID_REF,$CONVFACT,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES
        ];

        $sp_result = DB::select('EXEC SP_CSV_UP ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?, ?, ?,?,?,?,?, ?,?,?,?,?', $log_data); 
  
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $CSV_NO. ' Sucessfully Updated.']);

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
           
        $r_count1           =   $request['Row_Count1'];
        $r_count3           =   $request['Row_Count3'];
        $r_count4           =   $request['Row_Count4'];
        $TRANSACTION_TYPE   =   $request['TRANSACTION_TYPE'];
        $GROSS_TOTAL        =   0; 
        $NET_TOTAL 		    = 	$request['TotalValue'];
        $CGSTAMT            =   0; 
        $SGSTAMT            =   0; 
        $IGSTAMT            =   0; 
        $DISCOUNT           =   0; 
        $OTHER_CHARGES      =   0; 
        $FC 			    = (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF 		    = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT 		    = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
       
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i]))
            {

                $StoreArr   =   array();
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];

                if($ITEMROWID !=""){

                    $exp        =   explode(",",$ITEMROWID);

                    foreach($exp as $val){
                        $keyid              =   explode("_",$val);
                        $batchid            =   $keyid[0];

                        if($TRANSACTION_TYPE =="CREDIT"){
                            $storeid        =   $batchid;
                        }
                        else if($TRANSACTION_TYPE =="DEBIT"){

                            $objBatch =  DB::SELECT("SELECT BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF,SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) AS STOCK 
                            FROM TBL_MST_BATCH 
                            WHERE BATCHID='$batchid'
                            GROUP BY BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF 
                            HAVING SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) > '0.000'
                            ")[0];

                            $storeid        =   $objBatch->STID_REF;

                        }
                        else{
                            $storeid        =   NULL;
                        }

                        $StoreArr[] =   $storeid;
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

                $GROSS_TOTAL+= $request['DISAFTT_AMT_'.$i]; 
                $CGSTAMT+= $request['CGSTAMT_'.$i]; 
                $SGSTAMT+= $request['SGSTAMT_'.$i]; 
                $IGSTAMT+= $request['IGSTAMT_'.$i]; 
                $DISCOUNT+= $request['DISCOUNT_AMT_'.$i]; 

                $req_data[$i] = [
                    'SIID_REF' => isset($request['SQA_'.$i]) && $request['SQA_'.$i] !=""?$request['SQA_'.$i]:'' ,
                    'ITEMID_REF'    => isset($request['ITEMID_REF_'.$i]) && $request['ITEMID_REF_'.$i] !=""?$request['ITEMID_REF_'.$i]:'',
                    'UOMID_REF' => isset($request['MAIN_UOMID_REF_'.$i]) && $request['MAIN_UOMID_REF_'.$i] !=""?$request['MAIN_UOMID_REF_'.$i]:'',
                    'SI_QTY' => isset($request['SQMUOMQTY_'.$i]) && $request['SQMUOMQTY_'.$i] !=""?$request['SQMUOMQTY_'.$i]:0,
                    'SI_RATE' => isset($request['SI_RATE_'.$i]) && $request['SI_RATE_'.$i] !=""?$request['SI_RATE_'.$i]:0,
                    'STID' => isset($STID_REF) && $STID_REF !=""?$STID_REF:'',
                    'CR_NOTE_QTY' => $request['SO_QTY_'.$i] !=""?$request['SO_QTY_'.$i]:0,
                    'CR_NOTE_RATE' => $request['RATEPUOM_'.$i] !=""?$request['RATEPUOM_'.$i]:0,
                    'CR_NOTE_AMT' => $request['DISAFTT_AMT_'.$i] !=""?$request['DISAFTT_AMT_'.$i]:0,
                    'GST' =>  (isset($request['flagtype_'.$i])!="true" ? 0 : 1) ,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'BATCH_QTY'   => $request['HiddenRowId_'.$i] !=""?$request['HiddenRowId_'.$i]:'',
                    'SEID_REF'     => isset($SEID_REF) && $SEID_REF !=""?$SEID_REF:'',
                    'SQID_REF'     => isset($SQID_REF) && $SQID_REF !=""?$SQID_REF:'',
                    'SOID_REF'     => isset($SO) && $SO !=""?$SO:'',
                    'SCID_REF'     => isset($SCID_REF) && $SCID_REF !=""?$SCID_REF:'',
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
                        'UDFCRVID_REF'   => $request['UDFSOID_REF_'.$i],
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

        $req_data33=array();
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
                $SEID_REF   =   intval($Field_Id[2]);
                $SQID_REF   =   intval($Field_Id[3]);
                $SO         =   intval($Field_Id[4]);
                $SCID_REF   =   intval($Field_Id[5]);


                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        if($TRANSACTION_TYPE =="CREDIT"){
                            $BATCHNO        =   NULL;
                            $SERIALNO       =   NULL;
                            $STID_REF       =   $key;
                            $MAINUOMID_REF  =   NULL;
                            $STOCK_INHAND   =   NULL;
                        }
                        else if($TRANSACTION_TYPE =="DEBIT"){

                            $objBatch =  DB::SELECT("SELECT BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF,SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) AS STOCK 
                            FROM TBL_MST_BATCH 
                            WHERE BATCHID='$key'
                            GROUP BY BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF 
                            HAVING SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) > '0.000'
                            ")[0];

                            $BATCHNO        =   $objBatch->BATCH_CODE;
                            $SERIALNO       =   NULL;
                            $STID_REF       =   $objBatch->STID_REF;
                            $MAINUOMID_REF  =   $objBatch->UOMID_REF;
                            $STOCK_INHAND   =   $objBatch->STOCK;
                        }
                        else{
                            $BATCHNO        =   NULL;
                            $SERIALNO       =   NULL;
                            $STID_REF       =   NULL;
                            $MAINUOMID_REF  =   NULL;
                            $STOCK_INHAND   =   NULL;
                        }
                       
                        $req_data33[$i][] = [
                            'SIID_REF'      => isset($request['SQA_'.$i]) && $request['SQA_'.$i] !=""?$request['SQA_'.$i]:'',
                            'ITEMID_REF'    => isset($ITEMID_REF) && $ITEMID_REF !=""?$ITEMID_REF:'',
                            'BATCH_NO'      => isset($BATCHNO) && $BATCHNO !=""?$BATCHNO:'',
                            'STID_REF'      => isset($STID_REF) && $STID_REF !=""?intval($STID_REF):'',
                            'SERIAL_NO'     => isset($SERIALNO) && $SERIALNO !=""?$SERIALNO:'',
                            'UOMID_REF'     => isset($MAINUOMID_REF) && $MAINUOMID_REF !=""?$MAINUOMID_REF:'',
                            'STOCK_INHAND'  => isset($STOCK_INHAND) && $STOCK_INHAND !=''?floatval($STOCK_INHAND):0,
                            'QTY'           => isset($val) && $val !=""?$val:0,
                            'SEID_REF'      => isset($SEID_REF) && $SEID_REF !=""?$SEID_REF:'',
                            'SQID_REF'      => isset($SQID_REF) && $SQID_REF !=""?$SQID_REF:'',
                            'SOID_REF'      => isset($SO) && $SO !=""?$SO:'',
                            'SCID_REF'      => isset($SCID_REF) && $SCID_REF !=""?$SCID_REF:'',
                        ];

                    }
                }
            }
        }
      

        if(isset($req_data33) && !empty($req_data33)){
            $wrapped_links33["STORE"] = $req_data33; 
            $XMLSTORE = ArrayToXml::convert($wrapped_links33);
        }
        else{
            $XMLSTORE = NULL;
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

        $CSV_NO     = $request['CSV_NO'];
        $CSV_DT     = $request['CSV_DT'];
        $SLID_REF   = $request['SLID_REF'];        
        $BILLTO     = $request['BILLTO'];
        $SHIPTO     = $request['SHIPTO'];
        $REASONOFSR = $request['REASONOFSR'];
        $COMMONNRT  = $request['COMMONNRT'];
        $Direct     = (isset($request['Direct'])!="true" ? 0 : 1);

        $log_data = [ 
            $CSV_NO,$CSV_DT,$SLID_REF, $REASONOFSR,$COMMONNRT,$BILLTO,$SHIPTO,$CYID_REF, $BRID_REF,$FYID_REF,
            $VTID_REF,$XMLMAT,$XMLSTORE,$XMLUDF,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$Direct,$XMLCAL,$TRANSACTION_TYPE
            ,$FC,$CRID_REF,$CONVFACT,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES
        ];

        
        $sp_result = DB::select('EXEC SP_CSV_UP ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?, ?, ?,?,?,?,?, ?,?,?,?,?', $log_data); 

             
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $CSV_NO. ' Sucessfully Approved.']);

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
                $TABLE      =   "TBL_TRN_CRSV01_HDR";
                $FIELD      =   "CSVID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
            
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_CSV ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
    
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
        $TABLE      =   "TBL_TRN_CRSV01_HDR";
        $FIELD      =   "CSVID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_CRSV01_MAT',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_CRSV01_STORE',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_CRSV01_UDF',
        ];
        
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_CSV  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_CRSV01_HDR')->where('CSVID','=',$id)->first();

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
        
		//$destinationPath = storage_path()."/docs/company".$CYID_REF."/CreditNoteCsv";
        $image_path         =   "docs/company".$CYID_REF."/CreditNoteCsv";     
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

    public function getCSVNo(Request $request){
        
        $dataval          =   $request['dataval'];

		$onjData = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('PREFIX_TYPE','=', $dataval )
        ->get();

        foreach ($onjData as $index=>$OBJ_DOC){        
            $p_type = $OBJ_DOC->PREFIX_TYPE;
        } 
        
        $READONLY   =   'readonly';
        $MAXLENGTH  =   100;
        $DOC_NO     =   NULL;
        if(isset($OBJ_DOC->SYSTEM_GRSR) && $OBJ_DOC->SYSTEM_GRSR == "1"){

            $PREFIX         =   $OBJ_DOC->PREFIX_RQ == "1"?$OBJ_DOC->PREFIX:NULL;
            $PRE_SEP_SLASH  =   $OBJ_DOC->PRE_SEP_RQ == "1" && $OBJ_DOC->PRE_SEP_SLASH == "1"?'/':NULL;
            $PRE_SEP_HYPEN  =   $OBJ_DOC->PRE_SEP_RQ == "1" && $OBJ_DOC->PRE_SEP_HYPEN == "1"?'-':NULL;
            $NO_MAX         =   $OBJ_DOC->NO_MAX; 
            $NO_SEP_SLASH   =   $OBJ_DOC->NO_SEP_RQ == "1" && $OBJ_DOC->NO_SEP_SLASH == "1"?'/':NULL;
            $NO_SEP_HYPEN   =   $OBJ_DOC->NO_SEP_RQ == "1" && $OBJ_DOC->NO_SEP_HYPEN == "1"?'-':NULL;
            $SUFFIX         =   $OBJ_DOC->SUFFIX_RQ == "1"?$OBJ_DOC->SUFFIX:NULL;  

            if($OBJ_DOC->DOC_SERIES_TYPE ==="MONTH"){

                $MONTH  =   date('m',strtotime($DATE));
                $YEAR   =   date('Y',strtotime($DATE));

                $OBJ_HDR = DB::select("SELECT TOP 1 $HDR_DOC_NO 
                FROM $HDR_TABLE 
                WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF' AND MONTH($HDR_DOC_DT)='$MONTH' AND YEAR($HDR_DOC_DT)='$YEAR' 
                ORDER BY $HDR_ID DESC");

                $LAST_RECORDNO  =   0;
                if(isset($OBJ_HDR) && !empty($OBJ_HDR)){
                    $strlen         =   strlen($PREFIX.$PRE_SEP_SLASH.$PRE_SEP_HYPEN.$MONTH);
                    $substr         =   substr($OBJ_HDR[0]->$HDR_DOC_NO,$strlen);
                    $substr         =   substr($substr,0,$OBJ_DOC->NO_MAX);
                    $LAST_RECORDNO  =   intval($substr);  
                }
                $AUTO_NO    = $MONTH.str_pad($LAST_RECORDNO+1, $OBJ_DOC->NO_MAX, "0", STR_PAD_LEFT);

            }
            else{
                $AUTO_NO    =   str_pad($OBJ_DOC->LAST_RECORDNO+1, $OBJ_DOC->NO_MAX, "0", STR_PAD_LEFT);
            }

            $DOC_NO         =   $PREFIX.$PRE_SEP_SLASH.$PRE_SEP_HYPEN.$AUTO_NO.$NO_SEP_SLASH.$NO_SEP_HYPEN.$SUFFIX;             
        }
        else if(isset($OBJ_DOC->MANUAL_SR) && $OBJ_DOC->MANUAL_SR == "1"){
            $READONLY   =   'readonly';
            $MAXLENGTH  =   $OBJ_DOC->MANUAL_MAXLENGTH;
        }
  
        $DOC_NO =   $DOC_NO;
		
		if(!empty($DOC_NO)){
			echo $DOC_NO;
		}
		else{
			echo "";
		}
        exit();
    } 

    public function checkso(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $CSV_NO = $request->CSV_NO;
        
        $objSO = DB::table('TBL_TRN_CRSV01_HDR')
        ->where('TBL_TRN_CRSV01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_CRSV01_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_CRSV01_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_CRSV01_HDR.CSV_NO','=',$CSV_NO)
        ->select('TBL_TRN_CRSV01_HDR.CSV_NO')
        ->first();
        
        if($objSO){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate CSV NO']);
        
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

        $CYID_REF           =   Auth::user()->CYID_REF;
        $BRID_REF           =   Session::get('BRID_REF');
        $TRANSACTION_TYPE   =   $request['TRANSACTION_TYPE'];
        $ITEMID_REF         =   $request['ITEMID_REF'];
        $MAIN_UOMID_REF     =   $request['MAIN_UOMID_REF'];
        $SIID_REF           =   $request['SIID_REF'];
        $ROW_ID             =   $request['ROW_ID'];
        $ITEMROWID          =   $request['ITEMROWID'];
        $READONLY           =   $request['ACTION_TYPE'] =="VIEW"?'readonly':'';
        $WhereId            =   $request['WhereId'];
        $SRNOA              =   NULL;
        $BATCHNOA           =   NULL;
        $dataArr            =   array();

        if($ITEMROWID !=""){
            $exp    =   explode(",",$ITEMROWID);

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

        if($TRANSACTION_TYPE=="CREDIT"){
        
            $objBatch =  DB::SELECT("SELECT 
            STID,CONCAT(STCODE,'-',NAME) AS StoreName
            FROM TBL_MST_STORE 
            WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND (DEACTIVATED IS NULL OR DEACTIVATED =0)
            ");
            
            echo'
            <thead>
                <tr>
                    <th style="width:80%;text-align:left;">Store</th>
                    <th style="width:20%;text-align:left;">Credit Return Qty</th>
                </tr>
            </thead>';

            echo'<tbody>';
            
            if(!empty($objBatch)){
                foreach($objBatch as $key=>$val){
                    
                    $desc6          =   $val->STID;
                    $qtyvalue       =   array_key_exists($desc6, $dataArr)?$dataArr[$desc6]:0;
                    
                    echo '<tr class="participantRow33">';
                    echo '<td style="width:80%;text-align:left;">'.$val->StoreName.'</td>';
                    echo '<td style="width:20%;text-align:left;"><input type="text" name="UserQty_'.$key.'" id="UserQty_'.$key.'" value="'.$qtyvalue.'" class="qtytext" onkeyup="checkStoreQty('.$ROW_ID.','.$qtyvalue.',this.value,'.$key.','.$ITEMID_REF.')" autocomplete="off"  onkeypress="return isNumberDecimalKey(event,this)" '.$READONLY.' ></td>';
                    echo '<td hidden><input type="hidden" name="BATCHID_'.$key.'" id="BATCHID_'.$key.'" value="'.$desc6.'" class="qtytext" ></td>';
                    echo '</tr>';
                }
            }
            else{
                echo '<tr class="participantRow33"><td colspan="2" style="text-align:left">No data available in store</td></tr>';
            }

        }
        else if($TRANSACTION_TYPE=="DEBIT"){
            $objBatch =  DB::SELECT("SELECT BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF,SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) AS STOCK 
            FROM TBL_MST_BATCH 
            WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND ITEMID_REF='$ITEMID_REF' AND UOMID_REF='$MAIN_UOMID_REF'
            GROUP BY BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF 
            HAVING SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) > '0.000'
            ");
            
            echo'
            <thead>
                <tr>
                    <th style="width:30%;text-align:left;">Store</th>
                    <th style="width:30%;text-align:left;">Batch No</th>
                    <th style="width:20%;">Stock-in-hand</th>
                    <th style="width:20%;text-align:left;">Debit Return Qty</th>
                </tr>
            </thead>';

            echo'<tbody>';
            
            if(!empty($objBatch)){
                foreach($objBatch as $key=>$val){
                    
                    $desc6          =   $val->BATCHID;
                    $qtyvalue       =   array_key_exists($desc6, $dataArr)?$dataArr[$desc6]:0;
                    $STOCK_INHAND   =   $val->STOCK;
                    $STID_REF       =   $val->STID_REF;
                    $STORE_NAME     =   DB::SELECT("SELECT CONCAT(STCODE,'-',NAME) AS StoreName FROM TBL_MST_STORE WHERE STID='$STID_REF'")[0]->StoreName;
                
                    echo '<tr class="participantRow33">';
                    echo '<td style="width:30%;text-align:left;">'.$STORE_NAME.'</td>';
                    echo '<td style="width:30%;text-align:left;">'.$val->BATCH_CODE.'</td>';
                    echo '<td style="width:20%;">'.$STOCK_INHAND.'</td>';
                    echo '<td style="width:20%;text-align:left;"><input type="text" name="UserQty_'.$key.'" id="UserQty_'.$key.'" value="'.$qtyvalue.'" class="qtytext" onkeyup="checkStoreQty('.$ROW_ID.','.$qtyvalue.',this.value,'.$key.','.$ITEMID_REF.')" autocomplete="off"  onkeypress="return isNumberDecimalKey(event,this)" '.$READONLY.' ></td>';
                    echo '<td hidden><input type="hidden" name="BATCHID_'.$key.'" id="BATCHID_'.$key.'" value="'.$desc6.'" class="qtytext" ></td>';
                    echo '</tr>';
                }
            }
            else{
                echo '<tr class="participantRow33"><td colspan="4" style="text-align:left">No data available in store</td></tr>';
            }
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

    

    
}
