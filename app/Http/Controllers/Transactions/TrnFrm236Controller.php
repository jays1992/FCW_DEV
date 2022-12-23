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

class TrnFrm236Controller extends Controller{

    protected $form_id  = 236;
    protected $vtid_ref = 326;
    protected $view     = "transactions.Accounts.PurchaseInvoice.trnfrm";
   
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

        $objDataList	=	DB::select("select hdr.PBID,hdr.PB_DOCNO,hdr.PB_DOCDT,hdr.REMARKS,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.PBID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                            ) AS CREATED_BY,
                            hdr.STATUS, sl.SLNAME,dept.DCODE, dept.NAME, 
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
                            inner join TBL_TRN_PRPB01_HDR hdr
                            on a.VID = hdr.PBID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_SUBLEDGER sl ON hdr.VID_REF = sl.SGLID  
                            inner join TBL_MST_DEPARTMENT dept ON hdr.DEPID_REF = dept.DEPID
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.PBID DESC ");

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
        $Status     = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
       
        $objlastdt          =   $this->getLastdt();

        $objdepartment       =   $this->getdepartment();

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
        
       

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_PRPB01_HDR',
            'HDR_ID'=>'PBID',
            'HDR_DOC_NO'=>'PB_DOCNO',
            'HDR_DOC_DT'=>'PB_DOCDT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        

        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_PB")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDFPBID')->from('TBL_MST_UDFFOR_PB')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                    
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                     
                   

        $objUdfPBData = DB::table('TBL_MST_UDFFOR_PB')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdfPBData);

        $objTemplateMaster  =$this->getTemplateMaster("PURCHASE");
   
        $FormId     =   $this->form_id;

        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        $objothcurrency = $this->GetCurrencyMaster(); 
        
        return view($this->view.$FormId.'add',compact(['AlpsStatus','FormId','objlastdt','objdepartment'
                            ,'objUdfPBData','objCountUDF','objTNCHeader','objCalculationHeader','objTemplateMaster','TabSetting','doc_req','docarray','objothcurrency']));       
    }



        public function getdepartment(){
            $Status = "A";
            $CYID_REF = Auth::user()->CYID_REF;
        
            return  DB::select('SELECT DEPID, DCODE, NAME FROM TBL_MST_DEPARTMENT  
            WHERE  CYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by DCODE ASC', [$CYID_REF, 'A' ]);
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
            $fieldid    = $request['fieldid'];
    
            $SP_PARAMETERS = [$CYID_REF,$BRID_REF,$FYID_REF,$id];
    
            $ObjData =  DB::select('EXEC SP_GRN_GETLIST ?,?,?,?', $SP_PARAMETERS);



                if(!empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){

                    if($dataRow->GE_TYPE == 'PO' ||  $dataRow->GE_TYPE == 'BPO'){

                        $row = '';
                        $row = $row.'<tr >
                        <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="grncode_'.$dataRow->GRNID .'"  class="clsgrnid" value="'.$dataRow->GRNID.'" ></td>
                        <td class="ROW2">'.$dataRow->GRN_NO;
                        $row = $row.'<input type="hidden" id="txtgrncode_'.$dataRow->GRNID.'" data-desc="'.$dataRow->GRN_NO.'" data-desc1="'.$dataRow->GEID_REF.'" 
                        value="'.$dataRow->GRNID.'"/></td><td class="ROW3">'.$dataRow->GRN_DT.'</td></tr>';
                        echo $row;
                    }

                }
                }else{
                    echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
                exit();
        
            }


            public function getGeDetails(Request $request){
                $CYID_REF       =   Auth::user()->CYID_REF;
                $BRID_REF       =   Session::get('BRID_REF');
                $FYID_REF       =   Session::get('FYID_REF');
                $GEID        =   $request['id'];
               
                $ObjData =  DB::select("SELECT 
                VENDOR_BILLNO,VENDOR_BILLDT,BOE_NO,BOE_DATE 
                FROM TBL_TRN_IMGE01_HDR 
                WHERE GEID='$GEID'")[0];
        
                return Response::json([
                    'VENDOR_BILLNO' =>$ObjData->VENDOR_BILLNO,
                    'VENDOR_BILLDT' => $ObjData->VENDOR_BILLDT,
                    'BOE_NO' => $ObjData->BOE_NO,
                    'BOE_DATE' => $ObjData->BOE_DATE,
                ]);
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
                            $row = $row.'<tr >
                            <td class="ROW1"> <input type="checkbox" name="SELECT_BILLTO[]" id="billto_'.$dataRow->LID .'"  class="clsbillto" value="'.$dataRow->LID.'" ></td>
                            <td class="ROW2">'.$dataRow->NAME;
                            $row = $row.'<input type="hidden" id="txtbillto_'.$dataRow->LID.'" data-desc="'.$objAddress.'" 
                            value="'.$dataRow->LID.'"/></td><td class="ROW3">'.$objAddress.'</td></tr>';
                            echo $row;
                        }
                
                        }else{
                            echo '<tr><td colspan="3">Record not found.</td></tr>';
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
        $Status   = "A";
        $SLID_REF = $request['id'];
        $BRID_REF = Session::get('BRID_REF');
        
        $sp_param = [ 
            $SLID_REF,$BRID_REF
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

    public function getItemDetailsGRNwise(Request $request){
        $Status = "A";
        $id = $request['id'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $taxstate = $request['taxstate'];  

        $AlpsStatus =   $this->AlpsStatus();
       
        $SLID_REF   =   $request['vendorid'];
        $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
        $VENDORID   =   $ObVID->VID;

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
                                    if($dataRow->RATE != "0.00000") 
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
                                        $StdCost = $dataRow->RATE;
                                    }   
                                    else if(($ObjLIST)){
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
                                WHERE  CYID_REF = ? AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $dataRow->MAIN_UOMID_REF, $Status ]);

                    $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ? AND UOMID = ? 
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
                                WHERE  CYID_REF = ? AND ITEMGID = ?
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                [$CYID_REF, $ObjItem[0]->ITEMGID_REF, $Status ]);

                    $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS FROM TBL_MST_ITEMCATEGORY  
                                WHERE  CYID_REF = ? AND ICID = ?
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                [$CYID_REF, $ObjItem[0]->ICID_REF, $Status ]);
                
                    $row = '';
                    if($taxstate != "OutofState"){
                        $row = $row.'<tr id="item_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'" 
                         class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td style="width:10%;">'.$ObjItem[0]->ICODE;
                        $row = $row.'<input type="hidden" id="txtitem_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'" 
                        data-desc="'.$ObjItem[0]->ICODE.'" data-desc1="'.$dataRow->RECEIVED_QTY_MU.'" data-desc2="'.$dataRow->RECEIVED_QTY_AU.'"
                        value="'.$ObjItem[0]->ITEMID.'"/></td><td style="width:10%;" id="itemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'" >'.$ObjItem[0]->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'" 
                        data-desc="'.$ObjItem[0]->ITEM_SPECI.'"
                        value="'.$ObjItem[0]->NAME.'"/></td>';
                        $row = $row.'<td style="width:8%;" id="itemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'" >
                        <input type="hidden" id="txtitemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'" 
                        data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                        value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td style="width:8%;" id="uomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'" >
                        <input type="hidden" id="txtuomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'" 
                        value="'.$dataRow->ALT_UOMID_REF.'"/>'.$dataRow->RECEIVED_QTY_MU.'</td>';
                        $row = $row.'<td style="width:8%;" id="irate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'">
                        <input type="hidden" id="txtirate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'" 
                        value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                        $row = $row.'<td style="width:8%;" id="itax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'">
                        <input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'" data-desc="'.$Taxid[0].'"
                        value="'.$Taxid[1].'"/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td style="width:8%;">'.(isset($ObjBusinessUnit) && $ObjBusinessUnit != NULL ? $ObjBusinessUnit[0]->BUCODE.'-'.$ObjBusinessUnit[0]->BUNAME : '').'</td>';
                        $row = $row.'<td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ObjItem[0]->ALPS_PART_NO.'</td>';
                        $row = $row.'<td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ObjItem[0]->CUSTOMER_PART_NO.'</td>';
                        $row = $row.'<td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ObjItem[0]->OEM_PART_NO.'</td>';
                        $row = $row.'<td style="width:8%;" id="ise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'">
                        <input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'"  
                        data-desc="'.$dataRow->GEID_REF.'" data-desc1="'.$dataRow->MRSID_REF.'" data-desc2="'.$dataRow->PIID_REF.'" data-desc3="'.$dataRow->RFQID_REF.'"
                        data-desc4="'.$dataRow->VQID_REF.'" data-desc5="'.$dataRow->IPOID_REF.'"
                        value="'.$dataRow->POID_REF.'"/>Authorized</td>
                        </tr>';
                    }
                    else
                    {
                        $row = $row.'<tr id="item_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'"  
                        class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td style="width:10%;">'.$ObjItem[0]->ICODE;
                        $row = $row.'<input type="hidden" id="txtitem_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'" 
                        data-desc="'.$ObjItem[0]->ICODE.'" data-desc1="'.$dataRow->RECEIVED_QTY_MU.'" data-desc2="'.$dataRow->RECEIVED_QTY_AU.'"
                        value="'.$ObjItem[0]->ITEMID.'"/></td><td style="width:10%;" id="itemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'" >'.$ObjItem[0]->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'" 
                        data-desc="'.$ObjItem[0]->ITEM_SPECI.'"
                        value="'.$ObjItem[0]->NAME.'"/></td>';
                        $row = $row.'<td style="width:8%;" id="itemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'" >
                        <input type="hidden" id="txtitemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'" 
                        data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                        value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td style="width:8%;" id="uomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'" >
                        <input type="hidden" id="txtuomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'" 
                        value="'.$dataRow->ALT_UOMID_REF.'"/>'.$dataRow->RECEIVED_QTY_MU.'</td>';
                        $row = $row.'<td style="width:8%;" id="irate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'">
                        <input type="hidden" id="txtirate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'"
                        value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                        $row = $row.'<td style="width:8%;" id="itax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'">
                        <input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'-'.$dataRow->MRSID_REF.'-'.$dataRow->PIID_REF.'-'.$dataRow->RFQID_REF.'-'.$dataRow->VQID_REF.'-'.$dataRow->IPOID_REF.'" data-desc="'.$Taxid[0].'"
                        value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td style="width:8%;">'.(isset($ObjBusinessUnit) && $ObjBusinessUnit != NULL ? $ObjBusinessUnit[0]->BUCODE.'-'.$ObjBusinessUnit[0]->BUNAME : '').'</td>';
                        $row = $row.'<td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ObjItem[0]->ALPS_PART_NO.'</td>';
                        $row = $row.'<td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ObjItem[0]->CUSTOMER_PART_NO.'</td>';
                        $row = $row.'<td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ObjItem[0]->OEM_PART_NO.'</td>';
                        $row = $row.'<td style="width:8%;" id="ise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'"><input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->GEID_REF.'-'.$dataRow->POID_REF.'" 
                        data-desc="'.$dataRow->GEID_REF.'" data-desc1="'.$dataRow->MRSID_REF.'" data-desc2="'.$dataRow->PIID_REF.'" data-desc3="'.$dataRow->RFQID_REF.'"
                        data-desc4="'.$dataRow->VQID_REF.'" data-desc5="'.$dataRow->IPOID_REF.'"
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

    public function save(Request $request) {
        // dd($request->all());
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
            if(isset($request['GRN_ID_'.$i]))
            {
                $GROSS_TOTAL+= $request['DISAFTT_AMT_'.$i]; 
                $CGSTAMT+= $request['CGST_AMT_'.$i]; 
                $SGSTAMT+= $request['SGST_AMT_'.$i]; 
                $IGSTAMT+= $request['IGST_AMT_'.$i]; 
                $DISCOUNT+= $request['DISCOUNT_AMT_'.$i]; 

                $req_data[$i] = [
                    'GRN_NO'                    => $request['GRN_ID_'.$i],
                    'GEID_REF'                  => (isset($request['GEID_REF_'.$i]) ? $request['GEID_REF_'.$i] : ''),
                    'POID_REF'                  => (isset($request['POID_REF_'.$i]) ? $request['POID_REF_'.$i] : ''),
                    'ITEMID_REF'                => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'                 => $request['UOMID_REF_'.$i],
                    'RECEIVED_QTY'              => (!is_null($request['RECEIVED_QTY_'.$i]) ? $request['RECEIVED_QTY_'.$i] : 0),
                    'ALT_UOMID_REF'             => $request['ALT_UOMID_REF_'.$i],
                    'BILL_QTY'                  => (!is_null($request['BILL_QTY_'.$i]) ? $request['BILL_QTY_'.$i] : 0),
                    'BILL_UOMID_REF'            => $request['BILL_UOMID_REF_'.$i],
                    'BILL_RATEPUOM'             => (!is_null($request['BILL_RATEPUOM_'.$i]) ? $request['BILL_RATEPUOM_'.$i] : 0),
                    'DISCOUNT'                  => (!is_null($request['DISC_PER_'.$i]) ? $request['DISC_PER_'.$i] : 0),
                    'DISC_AMT'                  => (!is_null($request['DISC_AMT_'.$i]) ? $request['DISC_AMT_'.$i] : 0),
                    'IGST'                      => (!is_null($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST'                      => (!is_null($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST'                      => (!is_null($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'MRSID_REF'                 => (isset($request['MRSID_REF_'.$i]) ? $request['MRSID_REF_'.$i] : ''),
                    'PIID_REF'                  => (isset($request['PIID_REF_'.$i]) ? $request['PIID_REF_'.$i] : ''),
                    'RFQID_REF'                 => (isset($request['RFQID_REF_'.$i]) ? $request['RFQID_REF_'.$i] : ''),
                    'VQID_REF'                  => (isset($request['VQID_REF_'.$i]) ? $request['VQID_REF_'.$i] : ''),
                    'IPOID_REF'                 => (isset($request['IPOID_REF_'.$i]) ? $request['IPOID_REF_'.$i] : ''),
                    'ITEM_SEPECIFICATION'       => (isset($request['ItemDesc_'.$i]) ? $request['ItemDesc_'.$i] : ''),
                    'CGSTAMT'                   => (!is_null($request['CGST_AMT_'.$i]) ? $request['CGST_AMT_'.$i] : 0),
                    'SGSTAMT'                   => (!is_null($request['SGST_AMT_'.$i]) ? $request['SGST_AMT_'.$i] : 0),
                    'IGSTAMT'                   => (!is_null($request['IGST_AMT_'.$i]) ? $request['IGST_AMT_'.$i] : 0)
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

        for ($i=0; $i<=$r_count6; $i++)
        {
            if(isset($request['UDFPBID_REF_'.$i]) && !is_null($request['UDFPBID_REF_'.$i]))
            {
                $reqdata6[$i] = [
                    'UDFPBID_REF'   => $request['UDFPBID_REF_'.$i],
                    'VALUE'         => $request['udfvalue_'.$i],
                ];
            }
        }

        if(isset($reqdata6))
        { 
            $wrapped_links6["UDF1"] = $reqdata6; 
            $XMLUDF = ArrayToXml::convert($wrapped_links6);
        }
        else
        {
            $XMLUDF = NULL; 
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
                            'GST'           => (isset($request['calGST_'.$i])!="true" ? 0 : 1) ,
                            'IGST'          => (isset($request['calIGST_'.$i]) && !empty($request['calIGST_'.$i]) ? $request['calIGST_'.$i] : 0),
                            'CGST'          => (isset($request['calCGST_'.$i]) && !empty($request['calCGST_'.$i]) ? $request['calCGST_'.$i] : 0),
                            'SGST'          => (isset($request['calSGST_'.$i]) && !empty($request['calSGST_'.$i]) ? $request['calSGST_'.$i] : 0),
                            'ACTUAL'        => (isset($request['calACTUAL_'.$i])!="true" ? 0 : 1) ,
                        ];
                    }
                }
            
        }
        if(isset($reqdata3))
        { 
            $wrapped_links3["CALCULATIONTEMPLATE"] = $reqdata3; 
            $XMLCAL = ArrayToXml::convert($wrapped_links3);
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
                    'ASSESSABLE_VL_SURCHARGE'   => (!is_null($request['ASSESSABLE_VL_SURCHARGE_'.$i]) ? $request['ASSESSABLE_VL_SURCHARGE_'.$i] : 0),
                    'SURCHARGE_RATE'            => (!is_null($request['SURCHARGE_RATE_'.$i]) ? $request['SURCHARGE_RATE_'.$i] : 0),
                    'ASSESSABLE_VL_CESS'        => (!is_null($request['ASSESSABLE_VL_CESS_'.$i]) ? $request['ASSESSABLE_VL_CESS_'.$i] : 0),
                    'CESS_RATE'                 => (!is_null($request['CESS_RATE_'.$i]) ? $request['CESS_RATE_'.$i] : 0),
                    'ASSESSABLE_VL_SPCESS'      => (!is_null($request['ASSESSABLE_VL_SPCESS_'.$i]) ? $request['ASSESSABLE_VL_SPCESS_'.$i] : 0),
                    'SPCESS_RATE'               => (!is_null($request['SPCESS_RATE_'.$i]) ? $request['SPCESS_RATE_'.$i] : 0),
                ];
            }
        }
        if(isset($reqdata4))
        { 
            $wrapped_links4["TDSD"] = $reqdata4; 
            $XMLTDSD = ArrayToXml::convert($wrapped_links4);
        }
        else
        {
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

        $PB_DOCNO                       = $request['PB_DOCNO'];
        $PB_DOCDT                       = $request['PB_DOCDT'];
        $DEPID_REF                      = $request['DEPID_REF'];
        $VID_REF                        = $request['VID_REF'];
        $VENDOR_INNO                    = $request['VENDOR_INNO'];
        $VENDOR_INDT                    = $request['VENDOR_INDT'];
        $REMARKS                        = $request['REMARKS'];
        $BILL_TO                        = $request['BILLTO'];
        $SHIP_TO                        = $request['SHIPTO'];
        $CREDIT_DAYS                    = (!is_null($request['Credit_days']) ? $request['Credit_days'] : 0);
        $DUE_DATE                       = (!is_null($request['DUE_DATE']) ? $request['DUE_DATE'] : '');
        $GST_INPUT                      = (isset($request['GST_N_Avail'])!="true" ? 0 : 1);
        $REVERSE_GST                    = (isset($request['GST_Reverse'])!="true" ? 0 : 1);
        $TDS                            = (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);

        $FC = (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";

        $EXE_GST                =   (isset($request['EXE_GST'])!="true" ? 0 : 1);
        $Template_Description  =   $request['Template_Description'];
		$TYPE = $request['TYPE'];
       
        $log_data = [ 
            $PB_DOCNO,$PB_DOCDT,$DEPID_REF,$VID_REF,$VENDOR_INNO,$VENDOR_INDT,$REMARKS,$TDS,$CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,
            $XMLMAT,$XMLTNC,$XMLUDF,$XMLCAL,$XMLPSLB,$XMLTDSD,$USERID,Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$BILL_TO,$SHIP_TO,
            $CREDIT_DAYS,$DUE_DATE,$GST_INPUT,$REVERSE_GST,$EXE_GST,$Template_Description,$TYPE
            ,$FC,$CRID_REF,$CONVFACT,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT
        ]; 
        
        // dd($log_data);

        $sp_result = DB::select('EXEC SP_PB_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?, ?,?,?,?,?, ?,?,?,?,?,?', $log_data);  

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

                $objPBHDR = DB::table('TBL_TRN_PRPB01_HDR')
                        ->where('TBL_TRN_PRPB01_HDR.FYID_REF','=',$FYID_REF)
                        ->where('TBL_TRN_PRPB01_HDR.CYID_REF','=',$CYID_REF)
                        ->where('TBL_TRN_PRPB01_HDR.BRID_REF','=',$BRID_REF)
                        ->where('TBL_TRN_PRPB01_HDR.PBID','=',$id)
                        ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_PRPB01_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
                        ->select('TBL_TRN_PRPB01_HDR.*','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
                        ->first();

                $objPBDEP =[];
                if(isset($objPBHDR->DEPID_REF) && $objPBHDR->DEPID_REF !=""){ 
                $objPBDEP = DB::table('TBL_MST_DEPARTMENT')
                        ->where('TBL_MST_DEPARTMENT.CYID_REF','=',$CYID_REF)
                        ->where('TBL_MST_DEPARTMENT.BRID_REF','=',$BRID_REF)
                        ->where('TBL_MST_DEPARTMENT.DEPID','=',$objPBHDR->DEPID_REF)
                        ->select('TBL_MST_DEPARTMENT.*')
                        ->first();
                }
                
                $objPBVID =[];
                if(isset($objPBHDR->VID_REF) && $objPBHDR->VID_REF !=""){
                    $objPBVID = DB::table('TBL_MST_SUBLEDGER')
                    ->where('BELONGS_TO','=','Vendor')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('SGLID','=',$objPBHDR->VID_REF)    
                    ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                    ->first();
                }

                
                $TAXSTATE=[];
                $objShpAddress=[] ;
                $objBillAddress=[];


                if(isset($objPBHDR->SHIP_TO) && $objPBHDR->SHIP_TO !=""){ 
                $sid = $objPBHDR->SHIP_TO;
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
                

                  
                if(isset($objPBHDR->BILL_TO) && $objPBHDR->BILL_TO !=""){
                $bid = $objPBHDR->BILL_TO;
                $ObjBILLTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                            WHERE BILLTO= ? AND LID = ? ', [1,$bid]);
    
                
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

                $log_data = [ 
                    $id
                ];
                
                $objPBMAT = DB::select('EXEC SP_GET_PB_MATERIAL ?', $log_data);
                            
                                 //DD($objPBMAT); 
                $objCount1 = count($objPBMAT);  
                
                $objPBUDF = DB::table('TBL_TRN_PRPB01_UDF')                    
                                ->where('TBL_TRN_PRPB01_UDF.PBID_REF','=',$id)
                                ->get()
                                ->toArray();
                
                $objCount2 = count($objPBUDF);

                $objPBTNC = DB::table('TBL_TRN_PRPB01_TNC')                    
                                ->where('TBL_TRN_PRPB01_TNC.PBID_REF','=',$id)
                                ->get()->toArray();
                
                $objCount3 = count($objPBTNC);

                $objPBCAL = DB::table('TBL_TRN_PRPB01_CAL')                    
                                ->where('TBL_TRN_PRPB01_CAL.PBID_REF','=',$id)
                                ->get()->toArray();
                
                $objCount4 = count($objPBCAL);
                $objPBPSLB = DB::table('TBL_TRN_PRPB01_PSLB')                    
                                ->where('TBL_TRN_PRPB01_PSLB.PBID_REF','=',$id)
                                ->get()->toArray();
                
                $objCount5 = count($objPBPSLB);

                $objPBTDS = DB::select('EXEC SP_GET_PB_TDS ?', $log_data);
                
                $objCount6 = count($objPBTDS);
        
                $objRights = DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_USERROLMAP.BRID_REF','=',Auth::user()->BRID_REF)
                              
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();

                $objdepartment       =   $this->getdepartment();

              
        
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

                $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
                WHERE  CYID_REF = ? AND BRID_REF = ?   
                order by CTCODE ASC', [$CYID_REF, $BRID_REF ]);
                
                
                $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_PB")->select('*')
                            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                        {       
                                        $query->select('UDFPBID')->from('TBL_MST_UDFFOR_PB')
                                                        ->where('STATUS','=','A')
                                                        ->where('PARENTID','=',0)
                                                        ->where('DEACTIVATED','=',0)
                                                        ->where('CYID_REF','=',$CYID_REF);
                                                                           
                            })->where('DEACTIVATED','=',0)
                            ->where('STATUS','<>','C')                    
                            ->where('CYID_REF','=',$CYID_REF);
                                              
                            
        
                $objUdfPBData = DB::table('TBL_MST_UDFFOR_PB')
                    ->where('STATUS','=','A')
                    ->where('PARENTID','=',0)
                    ->where('DEACTIVATED','=',0)
                    ->where('CYID_REF','=',$CYID_REF)
                   
                    ->union($ObjUnionUDF)
                    ->get()->toArray();   
                $objCountUDF = count($objUdfPBData);

                $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_PB")->select('*')
                            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                        {       
                                        $query->select('UDFPBID')->from('TBL_MST_UDFFOR_PB')
                                                        ->where('PARENTID','=',0)
                                                        ->where('DEACTIVATED','=',0)
                                                        ->where('CYID_REF','=',$CYID_REF);
                                                                            
                            })->where('DEACTIVATED','=',0)              
                            ->where('CYID_REF','=',$CYID_REF);
                                           
                            
        
                $objUdfPBData2 = DB::table('TBL_MST_UDFFOR_PB')
                    ->where('PARENTID','=',0)
                    ->where('DEACTIVATED','=',0)
                    ->where('CYID_REF','=',$CYID_REF)
                   
                    ->union($ObjUnionUDF2)
                    ->get()->toArray(); 
            
                $FormId     =   $this->form_id;

                $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
                ->get() ->toArray(); 

                $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')
                ->get() ->toArray(); 

                $objlastdt          =   $this->getLastdt();

                $objTemplateMaster  =$this->getTemplateMaster("PURCHASE");

                $Template = DB::table('TBL_TRN_PRPB01_ADD_INFO')
                ->where('PBID_REF','=',$id)
                ->select('TBL_TRN_PRPB01_ADD_INFO.TEMPLATE')
                ->first();

                $AlpsStatus =   $this->AlpsStatus();
                $ActionStatus   =   "";

                $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

                $objothcurrency = $this->GetCurrencyMaster(); 
            
            return view($this->view.$FormId.'edit',compact(['AlpsStatus','FormId','objRights','objPBHDR','objPBDEP','objPBVID','TAXSTATE','objShpAddress',
                'objBillAddress','objPBMAT','objPBUDF','objPBTNC','objPBCAL','objPBPSLB','objPBTDS','objCount1','objCount2','objCount3',
                'objCount4','objCount5','objCount6','objUdfPBData','objCountUDF','objdepartment','objTNCHeader',
                'objCalculationHeader','objTNCDetails','objCalDetails','objlastdt','objCalHeader','objUdfPBData2','ActionStatus',
                'objTemplateMaster','Template','TabSetting','objothcurrency'
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
        $NET_TOTAL 		= 	$request['TotalValue'];
        $CGSTAMT        =   0; 
        $SGSTAMT        =   0; 
        $IGSTAMT        =   0; 
        $DISCOUNT       =   0; 
        $OTHER_CHARGES  =   0; 
        $TDS_AMOUNT     =   0; 
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['GRN_ID_'.$i]))
            {
                $GROSS_TOTAL+= $request['DISAFTT_AMT_'.$i]; 
                $CGSTAMT+= $request['CGST_AMT_'.$i]; 
                $SGSTAMT+= $request['SGST_AMT_'.$i]; 
                $IGSTAMT+= $request['IGST_AMT_'.$i]; 
                $DISCOUNT+= $request['DISCOUNT_AMT_'.$i]; 

                $req_data[$i] = [
                    'GRN_NO'                    => $request['GRN_ID_'.$i],
                    'GEID_REF'                  => (isset($request['GEID_REF_'.$i]) ? $request['GEID_REF_'.$i] : ''),
                    'POID_REF'                  => (isset($request['POID_REF_'.$i]) ? $request['POID_REF_'.$i] : ''),
                    'ITEMID_REF'                => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'                 => $request['UOMID_REF_'.$i],
                    'RECEIVED_QTY'              => (!is_null($request['RECEIVED_QTY_'.$i]) ? $request['RECEIVED_QTY_'.$i] : 0),
                    'ALT_UOMID_REF'             => $request['ALT_UOMID_REF_'.$i],
                    'BILL_QTY'                  => (!is_null($request['BILL_QTY_'.$i]) ? $request['BILL_QTY_'.$i] : 0),
                    'BILL_UOMID_REF'            => $request['BILL_UOMID_REF_'.$i],
                    'BILL_RATEPUOM'             => (!is_null($request['BILL_RATEPUOM_'.$i]) ? $request['BILL_RATEPUOM_'.$i] : 0),
                    'DISCOUNT'                  => (!is_null($request['DISC_PER_'.$i]) ? $request['DISC_PER_'.$i] : 0),
                    'DISC_AMT'                  => (!is_null($request['DISC_AMT_'.$i]) ? $request['DISC_AMT_'.$i] : 0),
                    'IGST'                      => (!is_null($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST'                      => (!is_null($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST'                      => (!is_null($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'MRSID_REF'                 => (isset($request['MRSID_REF_'.$i]) ? $request['MRSID_REF_'.$i] : ''),
                    'PIID_REF'                  => (isset($request['PIID_REF_'.$i]) ? $request['PIID_REF_'.$i] : ''),
                    'RFQID_REF'                 => (isset($request['RFQID_REF_'.$i]) ? $request['RFQID_REF_'.$i] : ''),
                    'VQID_REF'                  => (isset($request['VQID_REF_'.$i]) ? $request['VQID_REF_'.$i] : ''),
                    'IPOID_REF'                 => (isset($request['IPOID_REF_'.$i]) ? $request['IPOID_REF_'.$i] : ''),
                    'ITEM_SEPECIFICATION'       => (isset($request['ItemDesc_'.$i]) ? $request['ItemDesc_'.$i] : ''),
                    'CGSTAMT'                   => (!is_null($request['CGST_AMT_'.$i]) ? $request['CGST_AMT_'.$i] : 0),
                    'SGSTAMT'                   => (!is_null($request['SGST_AMT_'.$i]) ? $request['SGST_AMT_'.$i] : 0),
                    'IGSTAMT'                   => (!is_null($request['IGST_AMT_'.$i]) ? $request['IGST_AMT_'.$i] : 0)
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

        for ($i=0; $i<=$r_count6; $i++)
        {
            if(isset($request['UDFPBID_REF_'.$i]) && !is_null($request['UDFPBID_REF_'.$i]))
            {
                $reqdata6[$i] = [
                    'UDFPBID_REF'   => $request['UDFPBID_REF_'.$i],
                    'VALUE'         => $request['udfvalue_'.$i],
                ];
            }
        }

        if(isset($reqdata6))
        { 
            $wrapped_links6["UDF1"] = $reqdata6; 
            $XMLUDF = ArrayToXml::convert($wrapped_links6);
        }
        else
        {
            $XMLUDF = NULL; 
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
                            'GST'           => (isset($request['calGST_'.$i])!="true" ? 0 : 1) ,
                            'IGST'          => (isset($request['calIGST_'.$i]) && !empty($request['calIGST_'.$i]) ? $request['calIGST_'.$i] : 0),
                            'CGST'          => (isset($request['calCGST_'.$i]) && !empty($request['calCGST_'.$i]) ? $request['calCGST_'.$i] : 0),
                            'SGST'          => (isset($request['calSGST_'.$i]) && !empty($request['calSGST_'.$i]) ? $request['calSGST_'.$i] : 0),
                            'ACTUAL'        => (isset($request['calACTUAL_'.$i])!="true" ? 0 : 1) ,
                        ];
                    }
                }
            
        }
        if(isset($reqdata3))
        { 
            $wrapped_links3["CALCULATIONTEMPLATE"] = $reqdata3; 
            $XMLCAL = ArrayToXml::convert($wrapped_links3);
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
                    'ASSESSABLE_VL_SURCHARGE'   => (!is_null($request['ASSESSABLE_VL_SURCHARGE_'.$i]) ? $request['ASSESSABLE_VL_SURCHARGE_'.$i] : 0),
                    'SURCHARGE_RATE'            => (!is_null($request['SURCHARGE_RATE_'.$i]) ? $request['SURCHARGE_RATE_'.$i] : 0),
                    'ASSESSABLE_VL_CESS'        => (!is_null($request['ASSESSABLE_VL_CESS_'.$i]) ? $request['ASSESSABLE_VL_CESS_'.$i] : 0),
                    'CESS_RATE'                 => (!is_null($request['CESS_RATE_'.$i]) ? $request['CESS_RATE_'.$i] : 0),
                    'ASSESSABLE_VL_SPCESS'      => (!is_null($request['ASSESSABLE_VL_SPCESS_'.$i]) ? $request['ASSESSABLE_VL_SPCESS_'.$i] : 0),
                    'SPCESS_RATE'               => (!is_null($request['SPCESS_RATE_'.$i]) ? $request['SPCESS_RATE_'.$i] : 0),
                ];
            }
        }
        if(isset($reqdata4))
        { 
            $wrapped_links4["TDSD"] = $reqdata4; 
            $XMLTDSD = ArrayToXml::convert($wrapped_links4);
        }
        else
        {
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

        $PB_DOCNO                       =   $request['PB_DOCNO'];
        $PB_DOCDT                       =   $request['PB_DOCDT'];
        $DEPID_REF                      =   $request['DEPID_REF'];
        $VID_REF                        =   $request['VID_REF'];
        $VENDOR_INNO                    =   $request['VENDOR_INNO'];
        $VENDOR_INDT                    =   $request['VENDOR_INDT'];
        $REMARKS                        =   $request['REMARKS'];
        $BILL_TO                        =   $request['BILLTO'];
        $SHIP_TO                        =   $request['SHIPTO'];
        $CREDIT_DAYS                    =   (!is_null($request['Credit_days']) ? $request['Credit_days'] : 0);
        $DUE_DATE                       =   (!is_null($request['DUE_DATE']) ? $request['DUE_DATE'] : '');
        $GST_INPUT                      =   (isset($request['GST_N_Avail'])!="true" ? 0 : 1);
        $REVERSE_GST                    =   (isset($request['GST_Reverse'])!="true" ? 0 : 1);
        $TDS                            =   (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);

        $FC                             =   (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF                       =   (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT                       =   (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";

        $EXE_GST                        =   (isset($request['EXE_GST'])!="true" ? 0 : 1);
        $Template_Description           =   $request['Template_Description'];
		$TYPE                           =   $request['TYPE'];
        $log_data = [ 
            $PB_DOCNO,$PB_DOCDT,$DEPID_REF,$VID_REF,$VENDOR_INNO,$VENDOR_INDT,$REMARKS,$TDS,$CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,
            $XMLMAT,$XMLTNC,$XMLUDF,$XMLCAL,$XMLPSLB,$XMLTDSD,$USERID,Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$BILL_TO,$SHIP_TO,
            $CREDIT_DAYS,$DUE_DATE,$GST_INPUT,$REVERSE_GST,$EXE_GST,$Template_Description,$TYPE
            ,$FC,$CRID_REF,$CONVFACT,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT
        ]; 
        
        // dd($log_data);

        $sp_result = DB::select('EXEC SP_PB_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?, ?,?,?,?,?, ?,?,?,?,?,?', $log_data);  

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $PB_DOCNO. ' Sucessfully Updated.']);
        
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

            $objPBHDR = DB::table('TBL_TRN_PRPB01_HDR')
                        ->where('TBL_TRN_PRPB01_HDR.FYID_REF','=',$FYID_REF)
                        ->where('TBL_TRN_PRPB01_HDR.CYID_REF','=',$CYID_REF)
                        ->where('TBL_TRN_PRPB01_HDR.BRID_REF','=',$BRID_REF)
                        ->where('TBL_TRN_PRPB01_HDR.PBID','=',$id)
                        ->leftJoin('TBL_MST_CURRENCY', 'TBL_TRN_PRPB01_HDR.CRID_REF','=','TBL_MST_CURRENCY.CRID')
                        ->select('TBL_TRN_PRPB01_HDR.*','TBL_MST_CURRENCY.CRDESCRIPTION','TBL_MST_CURRENCY.CRCODE')
                        ->first();

                $objPBDEP =[];
                if(isset($objPBHDR->DEPID_REF) && $objPBHDR->DEPID_REF !=""){ 
                $objPBDEP = DB::table('TBL_MST_DEPARTMENT')
                        ->where('TBL_MST_DEPARTMENT.CYID_REF','=',$CYID_REF)
                        ->where('TBL_MST_DEPARTMENT.BRID_REF','=',$BRID_REF)
                        ->where('TBL_MST_DEPARTMENT.DEPID','=',$objPBHDR->DEPID_REF)
                        ->select('TBL_MST_DEPARTMENT.*')
                        ->first();
                }
                
                $objPBVID =[];
                if(isset($objPBHDR->VID_REF) && $objPBHDR->VID_REF !=""){
                    $objPBVID = DB::table('TBL_MST_SUBLEDGER')
                    ->where('BELONGS_TO','=','Vendor')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('SGLID','=',$objPBHDR->VID_REF)    
                    ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                    ->first();
                }

                
                $TAXSTATE=[];
                $objShpAddress=[] ;
                $objBillAddress=[];


                if(isset($objPBHDR->SHIP_TO) && $objPBHDR->SHIP_TO !=""){ 
                $sid = $objPBHDR->SHIP_TO;
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
                

                  
                if(isset($objPBHDR->BILL_TO) && $objPBHDR->BILL_TO !=""){
                $bid = $objPBHDR->BILL_TO;
                $ObjBILLTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION  
                            WHERE BILLTO= ? AND LID = ? ', [1,$bid]);
    
                
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

                $log_data = [ 
                    $id
                ];
                
                $objPBMAT = DB::select('EXEC SP_GET_PB_MATERIAL ?', $log_data);
                            
                                // DD($objSEMAT); 
                $objCount1 = count($objPBMAT);  
                
                $objPBUDF = DB::table('TBL_TRN_PRPB01_UDF')                    
                                ->where('TBL_TRN_PRPB01_UDF.PBID_REF','=',$id)
                                ->get()
                                ->toArray();
                
                $objCount2 = count($objPBUDF);

                $objPBTNC = DB::table('TBL_TRN_PRPB01_TNC')                    
                                ->where('TBL_TRN_PRPB01_TNC.PBID_REF','=',$id)
                                ->get()->toArray();
                
                $objCount3 = count($objPBTNC);

                $objPBCAL = DB::table('TBL_TRN_PRPB01_CAL')                    
                                ->where('TBL_TRN_PRPB01_CAL.PBID_REF','=',$id)
                                ->get()->toArray();
                
                $objCount4 = count($objPBCAL);
                $objPBPSLB = DB::table('TBL_TRN_PRPB01_PSLB')                    
                                ->where('TBL_TRN_PRPB01_PSLB.PBID_REF','=',$id)
                                ->get()->toArray();
                
                $objCount5 = count($objPBPSLB);

                $objPBTDS = DB::select('EXEC SP_GET_PB_TDS ?', $log_data);
                
                $objCount6 = count($objPBTDS);
        
                $objRights = DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_USERROLMAP.BRID_REF','=',Auth::user()->BRID_REF)
                              
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();

                $objdepartment       =   $this->getdepartment();

              
        
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

                $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
                WHERE  CYID_REF = ? AND BRID_REF = ?   
                order by CTCODE ASC', [$CYID_REF, $BRID_REF ]);
                
                
                $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_PB")->select('*')
                            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                        {       
                                        $query->select('UDFPBID')->from('TBL_MST_UDFFOR_PB')
                                                        ->where('STATUS','=','A')
                                                        ->where('PARENTID','=',0)
                                                        ->where('DEACTIVATED','=',0)
                                                        ->where('CYID_REF','=',$CYID_REF);
                                                                           
                            })->where('DEACTIVATED','=',0)
                            ->where('STATUS','<>','C')                    
                            ->where('CYID_REF','=',$CYID_REF);
                                              
                            
        
                $objUdfPBData = DB::table('TBL_MST_UDFFOR_PB')
                    ->where('STATUS','=','A')
                    ->where('PARENTID','=',0)
                    ->where('DEACTIVATED','=',0)
                    ->where('CYID_REF','=',$CYID_REF)
                   
                    ->union($ObjUnionUDF)
                    ->get()->toArray();   
                $objCountUDF = count($objUdfPBData);

                $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_PB")->select('*')
                            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                        {       
                                        $query->select('UDFPBID')->from('TBL_MST_UDFFOR_PB')
                                                        ->where('PARENTID','=',0)
                                                        ->where('DEACTIVATED','=',0)
                                                        ->where('CYID_REF','=',$CYID_REF);
                                                                            
                            })->where('DEACTIVATED','=',0)              
                            ->where('CYID_REF','=',$CYID_REF);
                                           
                            
        
                $objUdfPBData2 = DB::table('TBL_MST_UDFFOR_PB')
                    ->where('PARENTID','=',0)
                    ->where('DEACTIVATED','=',0)
                    ->where('CYID_REF','=',$CYID_REF)
                   
                    ->union($ObjUnionUDF2)
                    ->get()->toArray(); 
            
                $FormId     =   $this->form_id;

                $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
                ->get() ->toArray(); 

                $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')
                ->get() ->toArray(); 

                $objlastdt          =   $this->getLastdt();

                $objTemplateMaster  =$this->getTemplateMaster("PURCHASE");

                $Template = DB::table('TBL_TRN_PRPB01_ADD_INFO')
                ->where('PBID_REF','=',$id)
                ->select('TBL_TRN_PRPB01_ADD_INFO.TEMPLATE')
                ->first();

                $AlpsStatus =   $this->AlpsStatus();
                $ActionStatus   =   "disabled";

                $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

                $objothcurrency = $this->GetCurrencyMaster(); 

            
            return view($this->view.$FormId.'view',compact(['AlpsStatus','FormId','objRights','objPBHDR','objPBDEP','objPBVID','TAXSTATE','objShpAddress',
                'objBillAddress','objPBMAT','objPBUDF','objPBTNC','objPBCAL','objPBPSLB','objPBTDS','objCount1','objCount2','objCount3',
                'objCount4','objCount5','objCount6','objUdfPBData','objCountUDF','objdepartment','objTNCHeader',
                'objCalculationHeader','objTNCDetails','objCalDetails','objlastdt','objCalHeader','objUdfPBData2','ActionStatus',
                'objTemplateMaster','Template','TabSetting','objothcurrency'
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
        $NET_TOTAL 		= 	$request['TotalValue'];
        $CGSTAMT        =   0; 
        $SGSTAMT        =   0; 
        $IGSTAMT        =   0; 
        $DISCOUNT       =   0; 
        $OTHER_CHARGES  =   0; 
        $TDS_AMOUNT     =   0; 
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['GRN_ID_'.$i]))
            {
                $GROSS_TOTAL+= $request['DISAFTT_AMT_'.$i]; 
                $CGSTAMT+= $request['CGST_AMT_'.$i]; 
                $SGSTAMT+= $request['SGST_AMT_'.$i]; 
                $IGSTAMT+= $request['IGST_AMT_'.$i]; 
                $DISCOUNT+= $request['DISCOUNT_AMT_'.$i]; 

                $req_data[$i] = [
                    'GRN_NO'                    => $request['GRN_ID_'.$i],
                    'GEID_REF'                  => (isset($request['GEID_REF_'.$i]) ? $request['GEID_REF_'.$i] : ''),
                    'POID_REF'                  => (isset($request['POID_REF_'.$i]) ? $request['POID_REF_'.$i] : ''),
                    'ITEMID_REF'                => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'                 => $request['UOMID_REF_'.$i],
                    'RECEIVED_QTY'              => (!is_null($request['RECEIVED_QTY_'.$i]) ? $request['RECEIVED_QTY_'.$i] : 0),
                    'ALT_UOMID_REF'             => $request['ALT_UOMID_REF_'.$i],
                    'BILL_QTY'                  => (!is_null($request['BILL_QTY_'.$i]) ? $request['BILL_QTY_'.$i] : 0),
                    'BILL_UOMID_REF'            => $request['BILL_UOMID_REF_'.$i],
                    'BILL_RATEPUOM'             => (!is_null($request['BILL_RATEPUOM_'.$i]) ? $request['BILL_RATEPUOM_'.$i] : 0),
                    'DISCOUNT'                  => (!is_null($request['DISC_PER_'.$i]) ? $request['DISC_PER_'.$i] : 0),
                    'DISC_AMT'                  => (!is_null($request['DISC_AMT_'.$i]) ? $request['DISC_AMT_'.$i] : 0),
                    'IGST'                      => (!is_null($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST'                      => (!is_null($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST'                      => (!is_null($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'MRSID_REF'                 => (isset($request['MRSID_REF_'.$i]) ? $request['MRSID_REF_'.$i] : ''),
                    'PIID_REF'                  => (isset($request['PIID_REF_'.$i]) ? $request['PIID_REF_'.$i] : ''),
                    'RFQID_REF'                 => (isset($request['RFQID_REF_'.$i]) ? $request['RFQID_REF_'.$i] : ''),
                    'VQID_REF'                  => (isset($request['VQID_REF_'.$i]) ? $request['VQID_REF_'.$i] : ''),
                    'IPOID_REF'                 => (isset($request['IPOID_REF_'.$i]) ? $request['IPOID_REF_'.$i] : ''),
                    'ITEM_SEPECIFICATION'       => (isset($request['ItemDesc_'.$i]) ? $request['ItemDesc_'.$i] : ''),
                    'CGSTAMT'                   => (!is_null($request['CGST_AMT_'.$i]) ? $request['CGST_AMT_'.$i] : 0),
                    'SGSTAMT'                   => (!is_null($request['SGST_AMT_'.$i]) ? $request['SGST_AMT_'.$i] : 0),
                    'IGSTAMT'                   => (!is_null($request['IGST_AMT_'.$i]) ? $request['IGST_AMT_'.$i] : 0)
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

        for ($i=0; $i<=$r_count6; $i++)
        {
            if(isset($request['UDFPBID_REF_'.$i]) && !is_null($request['UDFPBID_REF_'.$i]))
            {
                $reqdata6[$i] = [
                    'UDFPBID_REF'   => $request['UDFPBID_REF_'.$i],
                    'VALUE'         => $request['udfvalue_'.$i],
                ];
            }
        }

        if(isset($reqdata6))
        { 
            $wrapped_links6["UDF1"] = $reqdata6; 
            $XMLUDF = ArrayToXml::convert($wrapped_links6);
        }
        else
        {
            $XMLUDF = NULL; 
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
                            'GST'           => (isset($request['calGST_'.$i])!="true" ? 0 : 1) ,
                            'IGST'          => (isset($request['calIGST_'.$i]) && !empty($request['calIGST_'.$i]) ? $request['calIGST_'.$i] : 0),
                            'CGST'          => (isset($request['calCGST_'.$i]) && !empty($request['calCGST_'.$i]) ? $request['calCGST_'.$i] : 0),
                            'SGST'          => (isset($request['calSGST_'.$i]) && !empty($request['calSGST_'.$i]) ? $request['calSGST_'.$i] : 0),
                            'ACTUAL'        => (isset($request['calACTUAL_'.$i])!="true" ? 0 : 1) ,
                        ];
                    }
                }
            
        }
        if(isset($reqdata3))
        { 
            $wrapped_links3["CALCULATIONTEMPLATE"] = $reqdata3; 
            $XMLCAL = ArrayToXml::convert($wrapped_links3);
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
                    'ASSESSABLE_VL_SURCHARGE'   => (!is_null($request['ASSESSABLE_VL_SURCHARGE_'.$i]) ? $request['ASSESSABLE_VL_SURCHARGE_'.$i] : 0),
                    'SURCHARGE_RATE'            => (!is_null($request['SURCHARGE_RATE_'.$i]) ? $request['SURCHARGE_RATE_'.$i] : 0),
                    'ASSESSABLE_VL_CESS'        => (!is_null($request['ASSESSABLE_VL_CESS_'.$i]) ? $request['ASSESSABLE_VL_CESS_'.$i] : 0),
                    'CESS_RATE'                 => (!is_null($request['CESS_RATE_'.$i]) ? $request['CESS_RATE_'.$i] : 0),
                    'ASSESSABLE_VL_SPCESS'      => (!is_null($request['ASSESSABLE_VL_SPCESS_'.$i]) ? $request['ASSESSABLE_VL_SPCESS_'.$i] : 0),
                    'SPCESS_RATE'               => (!is_null($request['SPCESS_RATE_'.$i]) ? $request['SPCESS_RATE_'.$i] : 0),
                ];
            }
        }
        if(isset($reqdata4))
        { 
            $wrapped_links4["TDSD"] = $reqdata4; 
            $XMLTDSD = ArrayToXml::convert($wrapped_links4);
        }
        else
        {
            $XMLTDSD = NULL; 
        }

        $VTID_REF                       = $this->vtid_ref;
        $VID                            = 0;
        $USERID                         = Auth::user()->USERID;   
        $ACTIONNAME                     = $Approvallevel;
        $IPADDRESS                      = $request->getClientIp();
        $CYID_REF                       = Auth::user()->CYID_REF;
        $BRID_REF                       = Session::get('BRID_REF');
        $FYID_REF                       = Session::get('FYID_REF');
        $PB_DOCNO                       = $request['PB_DOCNO'];
        $PB_DOCDT                       = $request['PB_DOCDT'];
        $DEPID_REF                      = $request['DEPID_REF'];
        $VID_REF                        = $request['VID_REF'];
        $VENDOR_INNO                    = $request['VENDOR_INNO'];
        $VENDOR_INDT                    = $request['VENDOR_INDT'];
        $REMARKS                        = $request['REMARKS'];
        $BILL_TO                        = $request['BILLTO'];
        $SHIP_TO                        = $request['SHIPTO'];
        $CREDIT_DAYS                    = (!is_null($request['Credit_days']) ? $request['Credit_days'] : 0);
        $DUE_DATE                       = (!is_null($request['DUE_DATE']) ? $request['DUE_DATE'] : '');
        $GST_INPUT                      = (isset($request['GST_N_Avail'])!="true" ? 0 : 1);
        $REVERSE_GST                    = (isset($request['GST_Reverse'])!="true" ? 0 : 1);
        $TDS                            = (isset($request['drpTDS']) && $request['drpTDS'] == 'Yes'? 1 : 0);
        $FC                             = (isset($request['FC'])!="true" ? 0 : 1);
        $CRID_REF                       = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT                       = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
        $EXE_GST                        = (isset($request['EXE_GST'])!="true" ? 0 : 1);
        $Template_Description           = $request['Template_Description'];
		$TYPE                           = $request['TYPE'];
        $log_data = [ 
            $PB_DOCNO,$PB_DOCDT,$DEPID_REF,$VID_REF,$VENDOR_INNO,$VENDOR_INDT,$REMARKS,$TDS,$CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,
            $XMLMAT,$XMLTNC,$XMLUDF,$XMLCAL,$XMLPSLB,$XMLTDSD,$USERID,Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$BILL_TO,$SHIP_TO,
            $CREDIT_DAYS,$DUE_DATE,$GST_INPUT,$REVERSE_GST,$EXE_GST,$Template_Description,$TYPE
            ,$FC,$CRID_REF,$CONVFACT,$GROSS_TOTAL,$NET_TOTAL,$CGSTAMT,$SGSTAMT,$IGSTAMT,$DISCOUNT,$OTHER_CHARGES,$TDS_AMOUNT
        ]; 
        
        // dd($log_data);

        $sp_result = DB::select('EXEC SP_PB_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?, ?,?,?,?,?, ?,?,?,?,?,?', $log_data);   


        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $PB_DOCNO. ' Sucessfully Approved.']);

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
        $TABLE      =   "TBL_TRN_PRPB01_HDR";
        $FIELD      =   "PBID";
        $ACTIONNAME     = $Approvallevel;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
            
        $log_data = [ 
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_PB ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
        
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
        $TABLE      =   "TBL_TRN_PRPB01_HDR";
        $FIELD      =   "PBID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_PRPB01_HDR',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_PRPB01_MAT',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_PRPB01_TNC',
        ];
        $req_data[3]=[
            'NT'  => 'TBL_TRN_PRPB01_TDS',
        ];
        $req_data[4]=[
            'NT'  => 'TBL_TRN_PRPB01_CAL',
        ];
        $req_data[5]=[
            'NT'  => 'TBL_TRN_PRPB01_PSLB',
        ];
        $req_data[6]=[
            'NT'  => 'TBL_TRN_PRPB01_UDF',
        ];

        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $pb_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_PB  ?,?,?,?, ?,?,?,?, ?,?,?,?', $pb_cancel_data);

        if($sp_result[0]->RESULT=="CANCELED"){  

            return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
        
            return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => $sp_result[0]->RESULT,'invalid'=>'invalid']);
        }
        
        exit(); 
    }

    public function attachment($id){

        if(!is_null($id)){
        
            $FormId     =   $this->form_id;

            $objResponse = DB::table('TBL_TRN_PRPB01_HDR')->where('PBID','=',$id)->first();

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
        
		$image_path         =   "docs/company".$CYID_REF."/PURCHASEBILL";     
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

        $PB_DOCNO  =   trim($request['PB_DOCNO']);
        $objLabel = DB::table('TBL_TRN_PRPB01_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('PB_DOCNO','=',$PB_DOCNO)
        ->select('PBID')->first();

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

        return  DB::select('SELECT MAX(PB_DOCDT) PB_DOCDT FROM TBL_TRN_PRPB01_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
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
    


    
}
