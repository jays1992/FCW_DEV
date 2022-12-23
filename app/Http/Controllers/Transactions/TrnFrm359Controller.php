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

class TrnFrm359Controller extends Controller{

    protected $form_id  = 359;
    protected $vtid_ref = 445;
    protected $view     = "transactions.JobWork.JobWorkInvoice.trnfrm";
   
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

        $CYID_REF       =  Auth::user()->CYID_REF;
        $BRID_REF       =  Session::get('BRID_REF');
        $FYID_REF       =  Session::get('FYID_REF');
                
        $REQUEST_DATA   =   array(
            'FORMID'    =>  $this->form_id,
            'VTID_REF'  =>  $this->vtid_ref,
            'USERID'    =>  Auth::user()->USERID,
            'CYID_REF'  =>  Auth::user()->CYID_REF,
            'BRID_REF'  =>  Session::get('BRID_REF'),
            'FYID_REF'  =>  Session::get('FYID_REF'),
        );

        $DATA_STATUS    =	Helper::get_user_level($REQUEST_DATA);
        $USER_LEVEL     =   $DATA_STATUS['USER_LEVEL'];

        $objDataList    =	DB::select("SELECT '$USER_LEVEL' AS USER_LEVEL,T1.*,T2.ACTIONNAME,
        (
        SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
        LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
        WHERE  AUD.VID=T1.JWIID AND  AUD.CYID_REF=T1.CYID_REF AND  AUD.BRID_REF=T1.BRID_REF AND  
        AUD.FYID_REF=T1.FYID_REF AND  AUD.VTID_REF=T1.VTID_REF AND AUD.ACTIONNAME='ADD'       
        ) AS CREATED_BY
        FROM TBL_TRN_JWI_HDR T1
        INNER JOIN TBL_TRN_AUDITTRAIL T2 ON T1.JWIID=T2.VID AND T1.VTID_REF=T2.VTID_REF AND T1.CYID_REF=T2.CYID_REF AND T1.BRID_REF=T2.BRID_REF 
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' AND T2.VTID_REF = '$this->vtid_ref' AND T2.ACTID IN (SELECT max(ACTID) FROM TBL_TRN_AUDITTRAIL A WHERE T2.VTID_REF = A.VTID_REF AND T2.VID = A.VID)
        ORDER BY T1.JWIID DESC 
        ");

        return view($this->view.$FormId,compact(['REQUEST_DATA','objRights','objDataList','FormId','DATA_STATUS']));
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
        $objdepartment      =   $this->getdepartment();

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
                'HDR_TABLE'=>'TBL_TRN_JWI_HDR',
                'HDR_ID'=>'JWIID',
                'HDR_DOC_NO'=>'JWINO',
                'HDR_DOC_DT'=>'JWIDT'
            );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
        
       


        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_JWI")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDFJWIID')->from('TBL_MST_UDFFOR_JWI')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF)
                                                ->where('BRID_REF','=',$BRID_REF)
                                                ->where('FYID_REF','=',$FYID_REF);                       
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF)
                    ->where('FYID_REF','=',$FYID_REF) ;                   
                   

        $objUdfPBData = DB::table('TBL_MST_UDFFOR_JWI')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('FYID_REF','=',$FYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdfPBData);
   
        $FormId     =   $this->form_id;
        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
        return view($this->view.$FormId.'add',compact(['AlpsStatus','FormId','objlastdt','objdepartment'
                            ,'objUdfPBData','objCountUDF','objTNCHeader','objCalculationHeader','TabSetting','doc_req','docarray']));       
    }

    public function getdepartment(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
    
        return  DB::table('TBL_MST_DEPARTMENT')
        ->where('TBL_MST_DEPARTMENT.CYID_REF','=',$CYID_REF)
        ->where('TBL_MST_DEPARTMENT.STATUS','=','A')
        ->where('TBL_MST_DEPARTMENT.DEACTIVATED','=',"0")
        ->select('TBL_MST_DEPARTMENT.*')
        ->get();
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
    
            $ObjData =  DB::select("SELECT GRJID AS GRNID,GRNNO AS GRN_NO,GRNDT AS GRN_DT
            FROM TBL_TRN_GRJ_HDR WHERE VID_REF='$id' AND  CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF' AND STATUS='A' ");


                if(!empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){
                    $row = '';
                    $row = $row.'<tr >
                    <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="grncode_'.$dataRow->GRNID .'"  class="clsgrnid" value="'.$dataRow->GRNID.'" ></td>
                    <td class="ROW2">'.$dataRow->GRN_NO;
                    $row = $row.'<input type="hidden" id="txtgrncode_'.$dataRow->GRNID.'" data-desc="'.$dataRow->GRN_NO.'" 
                    value="'.$dataRow->GRNID.'"/></td><td class="ROW3">'.$dataRow->GRN_DT.'</td></tr>';
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
    
                        $row = $row.'<tr  class="participantRow5">
                        <td><input type="text" name="popupTID_'.$dindex.'" id="popupTID_'.$dindex.'" class="form-control"  autocomplete="off" value="'.$dataRow->COMPONENT.'"  readonly/></td>
                        <td hidden><input type="hidden" name="TID_REF_'.$dindex.'" id="TID_REF_'.$dindex.'" class="form-control" value="'.$dataRow->TID.'" autocomplete="off" /></td>
                        <td><input type="text" name="RATE_'.$dindex.'" id="RATE_'.$dindex.'" class="form-control four-digits"  value="'.$dataRow->RATEPERCENTATE.'" maxlength="6" autocomplete="off"  readonly/></td>
                        <td hidden><input type="hidden" name="BASIS_'.$dindex.'" id="BASIS_'.$dindex.'" class="form-control"  value="'.$dataRow->BASIS.'" autocomplete="off" /></td>
                        <td hidden><input type="hidden" name="SQNO_'.$dindex.'" id="SQNO_'.$dindex.'" class="form-control"  value="'.$dataRow->SQNO.'" autocomplete="off" /></td>
                        <td hidden><input type="hidden" name="FORMULA_'.$dindex.'" id="FORMULA_'.$dindex.'" class="form-control"  value="'.$dataRow->FORMULA.'" autocomplete="off" /></td>
                        <td><input type="text" name="VALUE_'.$dindex.'" id="VALUE_'.$dindex.'" class="form-control two-digits"  value="'.$dataRow->AMOUNT.'" maxlength="15" autocomplete="off"  readonly/></td>
                        '.$row2.'<td><input type="text" name="calIGST_'.$dindex.'" id="calIGST_'.$dindex.'" class="form-control four-digits" maxlength="8"   autocomplete="off"  readonly/></td>
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

    

    public function getItemDetailsGRNwise(Request $request){
        $Status     = "A";
        $id         = $request['id'];
        $CYID_REF   = Auth::user()->CYID_REF;
        $BRID_REF   = Session::get('BRID_REF');
        $FYID_REF   = Session::get('FYID_REF');
        $taxstate   = $request['taxstate']; 
 
        $AlpsStatus =   $this->AlpsStatus();

        $SLID_REF   =   $request['vendorid'];
        $ObVID      =   DB::table('TBL_MST_VENDOR')->where('SLID_REF','=',$SLID_REF)->select('VID')->first();
        $VENDORID   =   $ObVID->VID;

        $Objquote =  DB::select("SELECT 
        T1.ITEMID_REF,T1.UOMID_REF AS MAIN_UOMID_REF,T1.RECEIVED_QTY AS RECEIVED_QTY_MU,
        T1.GRJID_REF,T1.JWCID_REF,T1.JWOID_REF,T1.PROID_REF,T1.SOID_REF,T1.SEID_REF,T1.SQID_REF,T1.JWRATE,
        T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI,T2.ALT_UOMID_REF
        FROM TBL_TRN_GRJ_MAT T1
        LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
        WHERE T1.GRJID_REF='$id'
        ");

        if(isset($Objquote) && !empty($Objquote)){
  
            $objVendorMst   =   DB::select('SELECT TOP 1 VID,VCODE,VGID_REF FROM TBL_MST_VENDOR  WHERE VID = ?', [ $VENDORID ]);         
            $VGID           =   $objVendorMst[0]->VGID_REF;
            $objVPLHDR      =   DB::select('SELECT VPLID,VGID_REF,VID_REF FROM TBL_MST_VENDORPRICELIST_HDR  where VGID_REF=? AND STATUS=?', [$VGID, 'A']);
            $objVPLHDR      =   [];

            if(empty($objVPLHDR)){
                $objVPLHDR =  DB::select('SELECT VPLID,VGID_REF,VID_REF FROM TBL_MST_VENDORPRICELIST_HDR  where VID_REF=? AND STATUS=?', [$VENDORID, 'A']);
            }

            foreach ($Objquote as $index=>$dataRow){
  
                $ObjItem =  DB::select('SELECT top 1 * FROM TBL_MST_ITEM WHERE ITEMID = ? ', [$dataRow->ITEMID_REF]);

                $ObjLIST=[];

                if(!empty($objVPLHDR)){
                    $ObjLIST    =   DB::table('TBL_MST_VENDORPRICELIST_MAT')  
                                    ->select('*')
                                    ->where('VPLID_REF','=',$objVPLHDR[0]->VPLID)
                                    ->where('ITEMID_REF','=',$dataRow->ITEMID_REF)
                                    ->where('UOMID_REF','=',$dataRow->MAIN_UOMID_REF)
                                    ->first();
                }
                   
                if(!empty($ObjLIST)){

                    $Taxid = [];
                    $ObjInTax = $ObjLIST->GST_IN_LP; 

                    if ($ObjInTax == 1){

                        $ObjHSN =   DB::select('SELECT top 1 HSNID_REF FROM TBL_MST_ITEM WHERE STATUS= ? AND ITEMID = ? ', ['A',$dataRow->ITEMID_REF]);

                        $ObjTax =   [];
                        if($taxstate == "OutofState"){

                            if(isset($ObjHSN[0]->HSNID_REF) && $ObjHSN[0]->HSNID_REF !=""){
                                $ObjTax =   DB::table('TBL_MST_HSNNORMAL')  
                                            ->select('NRATE')
                                            ->whereIn('TAXID_REF',function($query){$query->select('TAXID')->from('TBL_MST_TAXTYPE')->where('STATUS','=','A')->where('OUTOFSTATE','=',1);})
                                            ->where('HSNID_REF','=',$ObjHSN[0]->HSNID_REF) 
                                            ->get()->toArray();
                            }
                        }
                        else{

                            if(isset($ObjHSN[0]->HSNID_REF) && $ObjHSN[0]->HSNID_REF !=""){
                                $ObjTax =   DB::table('TBL_MST_HSNNORMAL')  
                                            ->select('NRATE')
                                            ->whereIn('TAXID_REF',function($query){$query->select('TAXID')->from('TBL_MST_TAXTYPE')->where('STATUS','=','A')->where('WITHINSTATE','=',1);})
                                            ->where('HSNID_REF','=',$ObjHSN[0]->HSNID_REF)
                                            ->get()->toArray();
                            }
                        }

                        $ObjTaxR    =   0;
                        foreach ($ObjTax as $tindex=>$tRow){
                            $ObjTaxR += $tRow->NRATE;
                            if($tRow->NRATE !== ''){
                                array_push($Taxid,$tRow->NRATE);
                            }
                        }

                        $ObjTaxDet = 100 + $ObjTaxR;
                        $ObjStdCost =  ($ObjLIST->LP*100)/$ObjTaxDet;
                        $StdCost = $ObjStdCost;
                           
                    }
                    else{

                        $ObjHSN =   DB::select('SELECT top 1 HSNID_REF FROM TBL_MST_ITEM  WHERE STATUS= ? AND ITEMID = ? ', ['A',$dataRow->ITEMID_REF]);
                        $ObjTax =   [];

                        if($taxstate == "OutofState"){

                            if(isset($ObjHSN[0]->HSNID_REF) && $ObjHSN[0]->HSNID_REF !=""){
                                $ObjTax =   DB::table('TBL_MST_HSNNORMAL')  
                                            ->select('NRATE')
                                            ->whereIn('TAXID_REF',function($query){$query->select('TAXID')->from('TBL_MST_TAXTYPE')->where('STATUS','=','A')->where('OUTOFSTATE','=',1);})
                                            ->where('HSNID_REF','=',$ObjHSN[0]->HSNID_REF)
                                            ->get()->toArray();
                            }

                        }
                        else{

                            if(isset($ObjHSN[0]->HSNID_REF) && $ObjHSN[0]->HSNID_REF !=""){
                                $ObjTax =  DB::table('TBL_MST_HSNNORMAL')  
                                        ->select('NRATE')
                                        ->whereIn('TAXID_REF',function($query){$query->select('TAXID')->from('TBL_MST_TAXTYPE')->where('STATUS','=','A')->where('WITHINSTATE','=',1);})
                                        ->where('HSNID_REF','=',$ObjHSN[0]->HSNID_REF)
                                        ->get()->toArray();
                            }
                        }

                        foreach ($ObjTax as $tindex=>$tRow){
                            if($tRow->NRATE !== ''){
                                array_push($Taxid,$tRow->NRATE);
                            }
                        }

                        $StdCost = $ObjLIST->LP;
                           
                    }
                }
                else{

                    $Taxid  =   [];
                    $ObjHSN =   DB::select('SELECT top 1 HSNID_REF FROM TBL_MST_ITEM WHERE STATUS= ? AND ITEMID = ? ', ['A',$dataRow->ITEMID_REF]);

                    $ObjTax =   [];
                    if($taxstate == "OutofState"){
                        
                        if(isset($ObjHSN[0]->HSNID_REF) && $ObjHSN[0]->HSNID_REF !=""){
                            $ObjTax =   DB::table('TBL_MST_HSNNORMAL')  
                                        ->select('NRATE')
                                        ->whereIn('TAXID_REF',function($query){$query->select('TAXID')->from('TBL_MST_TAXTYPE')->where('STATUS','=','A')->where('OUTOFSTATE','=',1);})
                                        ->where('HSNID_REF','=',$ObjHSN[0]->HSNID_REF) 
                                        ->get()->toArray();
                        }
                    }
                    else{

                        if(isset($ObjHSN[0]->HSNID_REF) && $ObjHSN[0]->HSNID_REF !=""){
                            $ObjTax =   DB::table('TBL_MST_HSNNORMAL')  
                                        ->select('NRATE')
                                        ->whereIn('TAXID_REF',function($query){$query->select('TAXID')->from('TBL_MST_TAXTYPE')->where('STATUS','=','A')->where('WITHINSTATE','=',1);})
                                        ->where('HSNID_REF','=',$ObjHSN[0]->HSNID_REF)
                                        ->get()->toArray();
                        }
                                
                    }

                    foreach ($ObjTax as $tindex=>$tRow){
                        if($tRow->NRATE !== ''){
                            array_push($Taxid,$tRow->NRATE);
                        }
                    }
                    
                    $StdCost = $ObjItem[0]->STDCOST;

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
                            WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ITEMGID = ?
                            AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                            [$CYID_REF, $BRID_REF, $FYID_REF,$ObjItem[0]->ITEMGID_REF, $Status ]);

                $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS FROM TBL_MST_ITEMCATEGORY  
                            WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ICID = ?
                            AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                            [$CYID_REF, $BRID_REF, $FYID_REF,$ObjItem[0]->ICID_REF, $Status ]);

                

                $item_unique_row_id =   $dataRow->GRJID_REF."_".$dataRow->JWCID_REF."_".$dataRow->JWOID_REF."_".$dataRow->PROID_REF."_".$dataRow->SOID_REF."_".$dataRow->SQID_REF."_".$dataRow->SEID_REF."_".$dataRow->ITEMID;
               
                
                $StdCost = 0;

                $Taxid[0]   =   isset($Taxid[0]) && $Taxid[0] !=""?$Taxid[0]:'0.0000';
                $Taxid[1]   =   isset($Taxid[1]) && $Taxid[1] !=""?$Taxid[1]:'0.0000';

                $row = '';

                if($taxstate != "OutofState"){
                    $row = $row.'<tr id="item_'.$index.'" 
                        class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                    $row = $row.'<td style="width:10%;">'.$ObjItem[0]->ICODE;
                    $row = $row.'<input type="hidden" id="txtitem_'.$index.'" 
                    data-desc="'.$ObjItem[0]->ICODE.'" data-desc1="'.$dataRow->RECEIVED_QTY_MU.'" data-desc2=""
                    value="'.$ObjItem[0]->ITEMID.'"/></td><td style="width:10%;" id="itemname_'.$index.'" >'.$ObjItem[0]->NAME;
                    $row = $row.'<input type="hidden" id="txtitemname_'.$index.'" 
                    data-desc="'.$ObjItem[0]->ITEM_SPECI.'"
                    value="'.$ObjItem[0]->NAME.'"/></td>';
                    $row = $row.'<td style="width:8%;" id="itemuom_'.$index.'" >
                    <input type="hidden" id="txtitemuom_'.$index.'" 
                    data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                    value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                    $row = $row.'<td style="width:8%;" id="uomqty_'.$index.'" >
                    <input type="hidden" id="txtuomqty_'.$index.'" 
                    value="'.$dataRow->ALT_UOMID_REF.'"/>'.$dataRow->RECEIVED_QTY_MU.'</td>';
                    $row = $row.'<td style="width:8%;" id="irate_'.$index.'">
                    <input type="hidden" id="txtirate_'.$index.'" 
                    value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                    $row = $row.'<td style="width:8%;" id="itax_'.$index.'">
                    <input type="hidden" id="txtitax_'.$index.'" data-desc="'.$Taxid[0].'"
                    value="'.$Taxid[1].'"/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>';
                    $row = $row.'<td style="width:8%;">'.(isset($ObjBusinessUnit) && $ObjBusinessUnit != NULL ? $ObjBusinessUnit[0]->BUCODE.'-'.$ObjBusinessUnit[0]->BUNAME : '').'</td>';
                    $row = $row.'<td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ObjItem[0]->ALPS_PART_NO.'</td>';
                    $row = $row.'<td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ObjItem[0]->CUSTOMER_PART_NO.'</td>';
                    $row = $row.'<td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ObjItem[0]->OEM_PART_NO.'</td>';
                    $row = $row.'
                        <td style="width:8%;" id="ise_'.$index.'">
                
                            <input type="hidden" id="txtise_'.$index.'" 
                        
                                data-desc0="'.$item_unique_row_id.'" 
                                data-desc1="'.$dataRow->SEID_REF.'"
                                data-desc2="'.$dataRow->SQID_REF.'"
                                data-desc3="'.$dataRow->SOID_REF.'"
                                data-desc4="'.$dataRow->PROID_REF.'"
                                data-desc5="'.$dataRow->JWOID_REF.'"
                                data-desc6="'.$dataRow->JWCID_REF.'"
                                data-desc7="'.$dataRow->GRJID_REF.'"
                                data-desc8="'.$dataRow->JWRATE.'"
                    
                            />
                            Authorized
                    </td>
                
                    </tr>';
                }
                else{
                    $row = $row.'<tr id="item_'.$index.'"  
                    class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                    $row = $row.'<td style="width:10%;">'.$ObjItem[0]->ICODE;
                    $row = $row.'<input type="hidden" id="txtitem_'.$index.'" 
                    data-desc="'.$ObjItem[0]->ICODE.'" data-desc1="'.$dataRow->RECEIVED_QTY_MU.'" data-desc2=""
                    value="'.$ObjItem[0]->ITEMID.'"/></td><td style="width:10%;" id="itemname_'.$index.'" >'.$ObjItem[0]->NAME;
                    $row = $row.'<input type="hidden" id="txtitemname_'.$index.'" 
                    data-desc="'.$ObjItem[0]->ITEM_SPECI.'"
                    value="'.$ObjItem[0]->NAME.'"/></td>';
                    $row = $row.'<td style="width:8%;" id="itemuom_'.$index.'" >
                    <input type="hidden" id="txtitemuom_'.$index.'" 
                    data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                    value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                    $row = $row.'<td style="width:8%;" id="uomqty_'.$index.'" >
                    <input type="hidden" id="txtuomqty_'.$index.'" 
                    value="'.$dataRow->ALT_UOMID_REF.'"/>'.$dataRow->RECEIVED_QTY_MU.'</td>';
                    $row = $row.'<td style="width:8%;" id="irate_'.$index.'">
                    <input type="hidden" id="txtirate_'.$index.'"
                    value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                    $row = $row.'<td style="width:8%;" id="itax_'.$index.'">
                    <input type="hidden" id="txtitax_'.$index.'" data-desc="'.$Taxid[0].'"
                    value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>';
                    $row = $row.'<td style="width:8%;">'.(isset($ObjBusinessUnit) && $ObjBusinessUnit != NULL ? $ObjBusinessUnit[0]->BUCODE.'-'.$ObjBusinessUnit[0]->BUNAME : '').'</td>';
                    $row = $row.'<td style="width:8%;">'.$ObjItem[0]->ALPS_PART_NO.'</td>';
                    $row = $row.'<td style="width:8%;">'.$ObjItem[0]->CUSTOMER_PART_NO.'</td>';
                    $row = $row.'<td style="width:8%;">'.$ObjItem[0]->OEM_PART_NO.'</td>';
                    $row = $row.'
                        <td style="width:8%;" id="ise_'.$index.'">
                
                            <input type="hidden" id="txtise_'.$index.'" 
                        
                                data-desc0="'.$item_unique_row_id.'" 
                                data-desc1="'.$dataRow->SEID_REF.'"
                                data-desc2="'.$dataRow->SQID_REF.'"
                                data-desc3="'.$dataRow->SOID_REF.'"
                                data-desc4="'.$dataRow->PROID_REF.'"
                                data-desc5="'.$dataRow->JWOID_REF.'"
                                data-desc6="'.$dataRow->JWCID_REF.'"
                                data-desc7="'.$dataRow->GRJID_REF.'"
                                data-desc8="'.$dataRow->JWRATE.'"
                    
                            />
                            Authorized
                    </td>
                
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
       
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count6 = $request['Row_Count6'];
        
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['GRJID_REF_'.$i])){
                $req_data[$i] = [
                    'ITEMID_REF'                => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'                 => $request['UOMID_REF_'.$i],
                    'JWOID_REF'                 => $request['JWOID_REF_'.$i],
                    'GRJID_REF'                 => $request['GRJID_REF_'.$i],
                    'BILL_QTY'                  => (!is_null($request['BILL_QTY_'.$i]) ? $request['BILL_QTY_'.$i] : 0),
                    'RECV_QTY'                  => (!is_null($request['RECEIVED_QTY_'.$i]) ? $request['RECEIVED_QTY_'.$i] : 0),
                    'BILL_RATE'                 => (!is_null($request['BILL_RATEPUOM_'.$i]) ? $request['BILL_RATEPUOM_'.$i] : 0),
                    'DISC_PER'                  => (!is_null($request['DISC_PER_'.$i]) ? $request['DISC_PER_'.$i] : 0),
                    'DISC_AMT'                  => (!is_null($request['DISC_AMT_'.$i]) ? $request['DISC_AMT_'.$i] : 0),
                    'IGST'                      => (!is_null($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST'                      => (!is_null($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST'                      => (!is_null($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'JWCID_REF'                 => (isset($request['JWCID_REF_'.$i]) ? $request['JWCID_REF_'.$i] : ''),
                    'PROID_REF'                 => (isset($request['PROID_REF_'.$i]) ? $request['PROID_REF_'.$i] : ''),
                    'SOID_REF'                  => (isset($request['SOID_REF_'.$i]) ? $request['SOID_REF_'.$i] : ''),
                    'SQID_REF'                  => (isset($request['SQID_REF_'.$i]) ? $request['SQID_REF_'.$i] : ''),
                    'SEID_REF'                  => (isset($request['SEID_REF_'.$i]) ? $request['SEID_REF_'.$i] : ''),
                   
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
            if(isset($request['UDFJWIID_REF_'.$i]) && !is_null($request['UDFJWIID_REF_'.$i]))
            {
                $reqdata6[$i] = [
                    'UDFJWIID_REF'   => $request['UDFJWIID_REF_'.$i],
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

        for ($i=0; $i<=$r_count3; $i++)
        {
                if(isset($request['CTID_REF']) && !is_null($request['CTID_REF']))
                {
                    if(isset($request['TID_REF_'.$i]))
                    {
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
            $wrapped_links3["CAL"] = $reqdata3; 
            $XMLCAL = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $XMLCAL = NULL; 
        }

        $XMLOTH = NULL; 
        

        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $JWINO                       = $request['JWINO'];
        $JWIDT                       = $request['JWIDT'];
        $DEPID_REF                   = $request['DEPID_REF'];
        $GRJID_REF                   =   NULL;
        $VID_REF                     = $request['VID_REF'];
        $VENDOR_INNO                 = $request['VENDOR_INNO'];
        $VENDOR_INDT                 = $request['VENDOR_INDT'];
        $REMARKS                     = $request['REMARKS'];
        $BILL_TO                     = $request['BILLTO'];
        $SHIP_TO                     = $request['SHIPTO'];
       
        $log_data = [ 
            $JWINO,$JWIDT,$DEPID_REF,$GRJID_REF,$VID_REF,
            $VENDOR_INNO,$VENDOR_INDT,$REMARKS,$CYID_REF,$BRID_REF,
            $FYID_REF,$VTID_REF,$XMLMAT,$XMLTNC,$XMLUDF,
            $XMLCAL,$XMLOTH,$USERID,Date('Y-m-d'),Date('h:i:s.u'),
            $ACTIONNAME,$IPADDRESS,$BILL_TO,$SHIP_TO
            
        ]; 
        

        $sp_result = DB::select('EXEC SP_JWI_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $log_data);  

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

        $objRights  =   DB::table('TBL_MST_USERROLMAP')
                        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Auth::user()->BRID_REF)
                        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Auth::user()->FYID_REF)
                        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                        ->first();
        
        if(!is_null($id)){

            $objlastdt      =   $this->getLastdt();
            $objdepartment  =   $this->getdepartment();

            $OBJ_HDR        =   DB::table('TBL_TRN_JWI_HDR')
                                ->where('TBL_TRN_JWI_HDR.FYID_REF','=',$FYID_REF)
                                ->where('TBL_TRN_JWI_HDR.CYID_REF','=',$CYID_REF)
                                ->where('TBL_TRN_JWI_HDR.BRID_REF','=',$BRID_REF)
                                ->where('TBL_TRN_JWI_HDR.JWIID','=',$id)
                                ->select('TBL_TRN_JWI_HDR.*')
                                ->first();
           
            $OBJ_HDR_DEP    =   DB::table('TBL_MST_DEPARTMENT')
                                ->where('TBL_MST_DEPARTMENT.CYID_REF','=',$CYID_REF)
                                ->where('TBL_MST_DEPARTMENT.BRID_REF','=',$BRID_REF)
                                ->where('TBL_MST_DEPARTMENT.DEPID','=',$OBJ_HDR->DEPARTMENTID_REF)
                                ->select('TBL_MST_DEPARTMENT.*')
                                ->first();
                

            $OBJ_HDR_VID       =   DB::table('TBL_MST_SUBLEDGER')
                                ->where('BELONGS_TO','=','Vendor')
                                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('SGLID','=',$OBJ_HDR->VID_REF)    
                                ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                                ->first();

                
            $TAXSTATE       =   [];
            $objShpAddress  =   [] ;
            $objBillAddress =   [];
            $sid            =   $OBJ_HDR->SHIP_TO;


            if(is_null($sid)){
                $TAXSTATE[]         =   NULL;
                $objShpAddress[]    =   NULL;
            }
            else{

                $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION WHERE  SHIPTO= ? AND LID = ? ', [1,$sid]);
                $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  WHERE BRID= ? ', [$BRID_REF]);
            
                if($ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF){
                    $TAXSTATE[] = 'WithinState';
                }
                else{
                    $TAXSTATE[] = 'OutofState';
                }
        
                $ObjCity    =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', [$Status,$ObjSHIPTO[0]->CITYID_REF,$ObjSHIPTO[0]->CTRYID_REF,$ObjSHIPTO[0]->STID_REF]);
                $ObjState   =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjSHIPTO[0]->STID_REF,$ObjSHIPTO[0]->CTRYID_REF]);
                $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjSHIPTO[0]->CTRYID_REF]);
        
                $ObjAddressID = $ObjSHIPTO[0]->LID;
                if(!empty($ObjSHIPTO)){
                    $objShpAddress[] = $ObjSHIPTO[0]->LADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                }

            }
                
            $bid = $OBJ_HDR->BILL_TO;

            if(is_null($bid)){
                $objBillAddress[]=NULL; 
            }
            else{
                $ObjBILLTO  =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION WHERE BILLTO= ? AND LID = ? ', [1,$bid]);
                $ObjCity2   =  DB::select('SELECT top 1 * FROM TBL_MST_CITY WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', [$Status,$ObjBILLTO[0]->CITYID_REF,$ObjBILLTO[0]->CTRYID_REF,$ObjBILLTO[0]->STID_REF]);
                $ObjState2  =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBILLTO[0]->STID_REF,$ObjBILLTO[0]->CTRYID_REF]);
                $ObjCountry2=  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBILLTO[0]->CTRYID_REF]);
        
                $ObjAddressID = $ObjBILLTO[0]->LID;
                if(!empty($ObjBILLTO)){
                    $objBillAddress[] = $ObjBILLTO[0]->LADD .' '.$ObjCity2[0]->NAME .' '.$ObjState2[0]->NAME .' '.$ObjCountry2[0]->NAME;
                }
            }


            $objPBMAT = DB::select("SELECT 
            T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,ITEM_SPECI,
            CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
            T4.GRNNO
            FROM TBL_TRN_JWI_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            LEFT JOIN TBL_TRN_GRJ_HDR T4 ON T1.GRJID_REF=T4.GRJID
            WHERE T1.JWIID_REF='$id' ORDER BY T1.JWI_MATID ASC
            ");

            $objCount1  = count($objPBMAT);  
                
            $objPBUDF   =   DB::table('TBL_TRN_JWI_UDF')->where('TBL_TRN_JWI_UDF.JWIID_REF','=',$id)->get()->toArray();
            $objCount2  =   count($objPBUDF);

            

            $objPBTNC   =   DB::table('TBL_TRN_JWI_TNC')->where('TBL_TRN_JWI_TNC.JWIID_REF','=',$id)->get()->toArray();
            $objCount3  =   count($objPBTNC);

            $objPBCAL   =   DB::table('TBL_TRN_JWI_CAL')->where('TBL_TRN_JWI_CAL.JWIID_REF','=',$id)->get()->toArray(); 
            $objCount4  =   count($objPBCAL);

            $objPBPSLB  =   array();
            $objCount5  =   count($objPBPSLB);

            $objPBTDS   =   array();  
            $objCount6  =   count($objPBTDS);
        
                
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
                
                
                $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_JWI")->select('*')
                            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                        {       
                                        $query->select('UDFJWIID')->from('TBL_MST_UDFFOR_JWI')
                                                        ->where('STATUS','=','A')
                                                        ->where('PARENTID','=',0)
                                                        ->where('DEACTIVATED','=',0)
                                                        ->where('CYID_REF','=',$CYID_REF)
                                                        ->where('BRID_REF','=',$BRID_REF)
                                                        ->where('FYID_REF','=',$FYID_REF);                       
                            })->where('DEACTIVATED','=',0)
                            ->where('STATUS','<>','C')                    
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('BRID_REF','=',$BRID_REF)
                            ->where('FYID_REF','=',$FYID_REF) ;                   
                            
        
                $objUdfPBData = DB::table('TBL_MST_UDFFOR_JWI')
                    ->where('STATUS','=','A')
                    ->where('PARENTID','=',0)
                    ->where('DEACTIVATED','=',0)
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF)
                    ->where('FYID_REF','=',$FYID_REF)
                    ->union($ObjUnionUDF)
                    ->get()->toArray();   
                $objCountUDF = count($objUdfPBData);

                $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_JWI")->select('*')
                            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                        {       
                                        $query->select('UDFJWIID')->from('TBL_MST_UDFFOR_JWI')
                                                        ->where('PARENTID','=',0)
                                                        ->where('DEACTIVATED','=',0)
                                                        ->where('CYID_REF','=',$CYID_REF)
                                                        ->where('BRID_REF','=',$BRID_REF)
                                                        ->where('FYID_REF','=',$FYID_REF);                       
                            })->where('DEACTIVATED','=',0)              
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('BRID_REF','=',$BRID_REF)
                            ->where('FYID_REF','=',$FYID_REF) ;                   
                            
        
                $objUdfPBData2 = DB::table('TBL_MST_UDFFOR_JWI')
                    ->where('PARENTID','=',0)
                    ->where('DEACTIVATED','=',0)
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF)
                    ->where('FYID_REF','=',$FYID_REF)
                    ->union($ObjUnionUDF2)
                    ->get()->toArray(); 
            
                

            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')->get() ->toArray(); 
            $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')->get() ->toArray();
                 

            $FormId     =   $this->form_id;
            $AlpsStatus =   $this->AlpsStatus();
            $ActionStatus   =   "";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view($this->view.$FormId.'edit',compact(['AlpsStatus','FormId','objRights','OBJ_HDR','OBJ_HDR_DEP','OBJ_HDR_VID','TAXSTATE','objShpAddress',
                'objBillAddress','objPBMAT','objPBUDF','objPBTNC','objPBCAL','objPBPSLB','objPBTDS','objCount1','objCount2','objCount3',
                'objCount4','objCount5','objCount6','objUdfPBData','objCountUDF','objdepartment','objTNCHeader',
                'objCalculationHeader','objTNCDetails','objCalDetails','objlastdt','objCalHeader','objUdfPBData2','ActionStatus','TabSetting'
            ]));      

        }
     
    }

    public function update(Request $request){
        
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count6 = $request['Row_Count6'];
        
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['GRJID_REF_'.$i])){
                $req_data[$i] = [
                    'ITEMID_REF'                => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'                 => $request['UOMID_REF_'.$i],
                    'JWOID_REF'                 => $request['JWOID_REF_'.$i],
                    'GRJID_REF'                 => $request['GRJID_REF_'.$i],
                    'BILL_QTY'                  => (!is_null($request['BILL_QTY_'.$i]) ? $request['BILL_QTY_'.$i] : 0),
                    'RECV_QTY'                  => (!is_null($request['RECEIVED_QTY_'.$i]) ? $request['RECEIVED_QTY_'.$i] : 0),
                    'BILL_RATE'                 => (!is_null($request['BILL_RATEPUOM_'.$i]) ? $request['BILL_RATEPUOM_'.$i] : 0),
                    'DISC_PER'                  => (!is_null($request['DISC_PER_'.$i]) ? $request['DISC_PER_'.$i] : 0),
                    'DISC_AMT'                  => (!is_null($request['DISC_AMT_'.$i]) ? $request['DISC_AMT_'.$i] : 0),
                    'IGST'                      => (!is_null($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST'                      => (!is_null($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST'                      => (!is_null($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'JWCID_REF'                 => (isset($request['JWCID_REF_'.$i]) ? $request['JWCID_REF_'.$i] : ''),
                    'PROID_REF'                 => (isset($request['PROID_REF_'.$i]) ? $request['PROID_REF_'.$i] : ''),
                    'SOID_REF'                  => (isset($request['SOID_REF_'.$i]) ? $request['SOID_REF_'.$i] : ''),
                    'SQID_REF'                  => (isset($request['SQID_REF_'.$i]) ? $request['SQID_REF_'.$i] : ''),
                    'SEID_REF'                  => (isset($request['SEID_REF_'.$i]) ? $request['SEID_REF_'.$i] : ''),
                   
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
            if(isset($request['UDFJWIID_REF_'.$i]) && !is_null($request['UDFJWIID_REF_'.$i]))
            {
                $reqdata6[$i] = [
                    'UDFJWIID_REF'   => $request['UDFJWIID_REF_'.$i],
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

        for ($i=0; $i<=$r_count3; $i++)
        {
                if(isset($request['CTID_REF']) && !is_null($request['CTID_REF']))
                {
                    if(isset($request['TID_REF_'.$i]))
                    {
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
            $wrapped_links3["CAL"] = $reqdata3; 
            $XMLCAL = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $XMLCAL = NULL; 
        }

        $XMLOTH = NULL;

        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $JWINO                       = $request['JWINO'];
        $JWIDT                       = $request['JWIDT'];
        $DEPID_REF                   = $request['DEPID_REF'];
        $GRJID_REF                   =   NULL;
        $VID_REF                     = $request['VID_REF'];
        $VENDOR_INNO                 = $request['VENDOR_INNO'];
        $VENDOR_INDT                 = $request['VENDOR_INDT'];
        $REMARKS                     = $request['REMARKS'];
        $BILL_TO                     = $request['BILLTO'];
        $SHIP_TO                     = $request['SHIPTO'];
       
        $log_data = [ 
            $JWINO,$JWIDT,$DEPID_REF,$GRJID_REF,$VID_REF,
            $VENDOR_INNO,$VENDOR_INDT,$REMARKS,$CYID_REF,$BRID_REF,
            $FYID_REF,$VTID_REF,$XMLMAT,$XMLTNC,$XMLUDF,
            $XMLCAL,$XMLOTH,$USERID,Date('Y-m-d'),Date('h:i:s.u'),
            $ACTIONNAME,$IPADDRESS,$BILL_TO,$SHIP_TO
            
        ]; 
        

        $sp_result = DB::select('EXEC SP_JWI_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $log_data); 

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $JWINO. ' Sucessfully Updated.']);

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

        $objRights  =   DB::table('TBL_MST_USERROLMAP')
                        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Auth::user()->BRID_REF)
                        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Auth::user()->FYID_REF)
                        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                        ->first();
        
        if(!is_null($id)){

            $objlastdt      =   $this->getLastdt();
            $objdepartment  =   $this->getdepartment();

            $OBJ_HDR        =   DB::table('TBL_TRN_JWI_HDR')
                                ->where('TBL_TRN_JWI_HDR.FYID_REF','=',$FYID_REF)
                                ->where('TBL_TRN_JWI_HDR.CYID_REF','=',$CYID_REF)
                                ->where('TBL_TRN_JWI_HDR.BRID_REF','=',$BRID_REF)
                                ->where('TBL_TRN_JWI_HDR.JWIID','=',$id)
                                ->select('TBL_TRN_JWI_HDR.*')
                                ->first();
           
            $OBJ_HDR_DEP    =   DB::table('TBL_MST_DEPARTMENT')
                                ->where('TBL_MST_DEPARTMENT.CYID_REF','=',$CYID_REF)
                                ->where('TBL_MST_DEPARTMENT.BRID_REF','=',$BRID_REF)
                                ->where('TBL_MST_DEPARTMENT.DEPID','=',$OBJ_HDR->DEPARTMENTID_REF)
                                ->select('TBL_MST_DEPARTMENT.*')
                                ->first();
                

            $OBJ_HDR_VID       =   DB::table('TBL_MST_SUBLEDGER')
                                ->where('BELONGS_TO','=','Vendor')
                                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('SGLID','=',$OBJ_HDR->VID_REF)    
                                ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                                ->first();

                
            $TAXSTATE       =   [];
            $objShpAddress  =   [] ;
            $objBillAddress =   [];
            $sid            =   $OBJ_HDR->SHIP_TO;


            if(is_null($sid)){
                $TAXSTATE[]         =   NULL;
                $objShpAddress[]    =   NULL;
            }
            else{

                $ObjSHIPTO =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION WHERE  SHIPTO= ? AND LID = ? ', [1,$sid]);
                $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH  WHERE BRID= ? ', [$BRID_REF]);
            
                if($ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF){
                    $TAXSTATE[] = 'WithinState';
                }
                else{
                    $TAXSTATE[] = 'OutofState';
                }
        
                $ObjCity    =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', [$Status,$ObjSHIPTO[0]->CITYID_REF,$ObjSHIPTO[0]->CTRYID_REF,$ObjSHIPTO[0]->STID_REF]);
                $ObjState   =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjSHIPTO[0]->STID_REF,$ObjSHIPTO[0]->CTRYID_REF]);
                $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjSHIPTO[0]->CTRYID_REF]);
        
                $ObjAddressID = $ObjSHIPTO[0]->LID;
                if(!empty($ObjSHIPTO)){
                    $objShpAddress[] = $ObjSHIPTO[0]->LADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                }

            }
                
            $bid = $OBJ_HDR->BILL_TO;

            if(is_null($bid)){
                $objBillAddress[]=NULL; 
            }
            else{
                $ObjBILLTO  =  DB::select('SELECT top 1 * FROM TBL_MST_VENDORLOCATION WHERE BILLTO= ? AND LID = ? ', [1,$bid]);
                $ObjCity2   =  DB::select('SELECT top 1 * FROM TBL_MST_CITY WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', [$Status,$ObjBILLTO[0]->CITYID_REF,$ObjBILLTO[0]->CTRYID_REF,$ObjBILLTO[0]->STID_REF]);
                $ObjState2  =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$ObjBILLTO[0]->STID_REF,$ObjBILLTO[0]->CTRYID_REF]);
                $ObjCountry2=  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  WHERE STATUS= ? AND CTRYID = ? ', [$Status,$ObjBILLTO[0]->CTRYID_REF]);
        
                $ObjAddressID = $ObjBILLTO[0]->LID;
                if(!empty($ObjBILLTO)){
                    $objBillAddress[] = $ObjBILLTO[0]->LADD .' '.$ObjCity2[0]->NAME .' '.$ObjState2[0]->NAME .' '.$ObjCountry2[0]->NAME;
                }
            }


            $objPBMAT = DB::select("SELECT 
            T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,ITEM_SPECI,
            CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
            T4.GRNNO
            FROM TBL_TRN_JWI_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            LEFT JOIN TBL_TRN_GRJ_HDR T4 ON T1.GRJID_REF=T4.GRJID
            WHERE T1.JWIID_REF='$id' ORDER BY T1.JWI_MATID ASC
            ");

            $objCount1  = count($objPBMAT);  
                
            $objPBUDF   =   DB::table('TBL_TRN_JWI_UDF')->where('TBL_TRN_JWI_UDF.JWIID_REF','=',$id)->get()->toArray();
            $objCount2  =   count($objPBUDF);

            

            $objPBTNC   =   DB::table('TBL_TRN_JWI_TNC')->where('TBL_TRN_JWI_TNC.JWIID_REF','=',$id)->get()->toArray();
            $objCount3  =   count($objPBTNC);

            $objPBCAL   =   DB::table('TBL_TRN_JWI_CAL')->where('TBL_TRN_JWI_CAL.JWIID_REF','=',$id)->get()->toArray(); 
            $objCount4  =   count($objPBCAL);

            $objPBPSLB  =   array();
            $objCount5  =   count($objPBPSLB);

            $objPBTDS   =   array();  
            $objCount6  =   count($objPBTDS);
        
                
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
                
                
                $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_JWI")->select('*')
                            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                        {       
                                        $query->select('UDFJWIID')->from('TBL_MST_UDFFOR_JWI')
                                                        ->where('STATUS','=','A')
                                                        ->where('PARENTID','=',0)
                                                        ->where('DEACTIVATED','=',0)
                                                        ->where('CYID_REF','=',$CYID_REF)
                                                        ->where('BRID_REF','=',$BRID_REF)
                                                        ->where('FYID_REF','=',$FYID_REF);                       
                            })->where('DEACTIVATED','=',0)
                            ->where('STATUS','<>','C')                    
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('BRID_REF','=',$BRID_REF)
                            ->where('FYID_REF','=',$FYID_REF) ;                   
                            
        
                $objUdfPBData = DB::table('TBL_MST_UDFFOR_JWI')
                    ->where('STATUS','=','A')
                    ->where('PARENTID','=',0)
                    ->where('DEACTIVATED','=',0)
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF)
                    ->where('FYID_REF','=',$FYID_REF)
                    ->union($ObjUnionUDF)
                    ->get()->toArray();   
                $objCountUDF = count($objUdfPBData);

                $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_JWI")->select('*')
                            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                        {       
                                        $query->select('UDFJWIID')->from('TBL_MST_UDFFOR_JWI')
                                                        ->where('PARENTID','=',0)
                                                        ->where('DEACTIVATED','=',0)
                                                        ->where('CYID_REF','=',$CYID_REF)
                                                        ->where('BRID_REF','=',$BRID_REF)
                                                        ->where('FYID_REF','=',$FYID_REF);                       
                            })->where('DEACTIVATED','=',0)              
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('BRID_REF','=',$BRID_REF)
                            ->where('FYID_REF','=',$FYID_REF) ;                   
                            
        
                $objUdfPBData2 = DB::table('TBL_MST_UDFFOR_JWI')
                    ->where('PARENTID','=',0)
                    ->where('DEACTIVATED','=',0)
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF)
                    ->where('FYID_REF','=',$FYID_REF)
                    ->union($ObjUnionUDF2)
                    ->get()->toArray(); 
            
                

            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')->get() ->toArray(); 
            $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')->get() ->toArray();
                 

            $FormId     =   $this->form_id;
            $AlpsStatus =   $this->AlpsStatus();
            $ActionStatus   =   "disabled";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view($this->view.$FormId.'view',compact(['AlpsStatus','FormId','objRights','OBJ_HDR','OBJ_HDR_DEP','OBJ_HDR_VID','TAXSTATE','objShpAddress',
                'objBillAddress','objPBMAT','objPBUDF','objPBTNC','objPBCAL','objPBPSLB','objPBTDS','objCount1','objCount2','objCount3',
                'objCount4','objCount5','objCount6','objUdfPBData','objCountUDF','objdepartment','objTNCHeader',
                'objCalculationHeader','objTNCDetails','objCalDetails','objlastdt','objCalHeader','objUdfPBData2','ActionStatus','TabSetting'
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
            $USERID_REF, $VTID_REF, $CYID_REF,$BRID_REF,$FYID_REF
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
        $r_count6 = $request['Row_Count6'];
        
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['GRJID_REF_'.$i])){
                $req_data[$i] = [
                    'ITEMID_REF'                => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'                 => $request['UOMID_REF_'.$i],
                    'JWOID_REF'                 => $request['JWOID_REF_'.$i],
                    'GRJID_REF'                 => $request['GRJID_REF_'.$i],
                    'BILL_QTY'                  => (!is_null($request['BILL_QTY_'.$i]) ? $request['BILL_QTY_'.$i] : 0),
                    'RECV_QTY'                  => (!is_null($request['RECEIVED_QTY_'.$i]) ? $request['RECEIVED_QTY_'.$i] : 0),
                    'BILL_RATE'                 => (!is_null($request['BILL_RATEPUOM_'.$i]) ? $request['BILL_RATEPUOM_'.$i] : 0),
                    'DISC_PER'                  => (!is_null($request['DISC_PER_'.$i]) ? $request['DISC_PER_'.$i] : 0),
                    'DISC_AMT'                  => (!is_null($request['DISC_AMT_'.$i]) ? $request['DISC_AMT_'.$i] : 0),
                    'IGST'                      => (!is_null($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST'                      => (!is_null($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST'                      => (!is_null($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'JWCID_REF'                 => (isset($request['JWCID_REF_'.$i]) ? $request['JWCID_REF_'.$i] : ''),
                    'PROID_REF'                 => (isset($request['PROID_REF_'.$i]) ? $request['PROID_REF_'.$i] : ''),
                    'SOID_REF'                  => (isset($request['SOID_REF_'.$i]) ? $request['SOID_REF_'.$i] : ''),
                    'SQID_REF'                  => (isset($request['SQID_REF_'.$i]) ? $request['SQID_REF_'.$i] : ''),
                    'SEID_REF'                  => (isset($request['SEID_REF_'.$i]) ? $request['SEID_REF_'.$i] : ''),
                   
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
            if(isset($request['UDFJWIID_REF_'.$i]) && !is_null($request['UDFJWIID_REF_'.$i]))
            {
                $reqdata6[$i] = [
                    'UDFJWIID_REF'   => $request['UDFJWIID_REF_'.$i],
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

        for ($i=0; $i<=$r_count3; $i++)
        {
                if(isset($request['CTID_REF']) && !is_null($request['CTID_REF']))
                {
                    if(isset($request['TID_REF_'.$i]))
                    {
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
            $wrapped_links3["CAL"] = $reqdata3; 
            $XMLCAL = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $XMLCAL = NULL; 
        }

        $XMLOTH = NULL;

        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $JWINO                       = $request['JWINO'];
        $JWIDT                       = $request['JWIDT'];
        $DEPID_REF                   = $request['DEPID_REF'];
        $GRJID_REF                   =   NULL;
        $VID_REF                     = $request['VID_REF'];
        $VENDOR_INNO                 = $request['VENDOR_INNO'];
        $VENDOR_INDT                 = $request['VENDOR_INDT'];
        $REMARKS                     = $request['REMARKS'];
        $BILL_TO                     = $request['BILLTO'];
        $SHIP_TO                     = $request['SHIPTO'];
       
        $log_data = [ 
            $JWINO,$JWIDT,$DEPID_REF,$GRJID_REF,$VID_REF,
            $VENDOR_INNO,$VENDOR_INDT,$REMARKS,$CYID_REF,$BRID_REF,
            $FYID_REF,$VTID_REF,$XMLMAT,$XMLTNC,$XMLUDF,
            $XMLCAL,$XMLOTH,$USERID,Date('Y-m-d'),Date('h:i:s.u'),
            $ACTIONNAME,$IPADDRESS,$BILL_TO,$SHIP_TO
            
        ]; 
        
        
        $sp_result = DB::select('EXEC SP_JWI_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $log_data);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $JWINO. ' Sucessfully Approved.']);

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
        $TABLE      =   "TBL_TRN_JWI_HDR";
        $FIELD      =   "JWIID";
        $ACTIONNAME     = $Approvallevel;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
            
        $log_data = [ 
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_JWI ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
        

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
        $TABLE      =   "TBL_TRN_JWI_HDR";
        $FIELD      =   "JWIID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_JWI_HDR',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_JWI_MAT',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_JWI_TNC',
        ];
        $req_data[3]=[
            'NT'  => 'TBL_TRN_JWI_CAL',
        ];
        $req_data[4]=[
            'NT'  => 'TBL_TRN_JWI_UDF',
        ];


        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $pb_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_JWI  ?,?,?,?, ?,?,?,?, ?,?,?,?', $pb_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_JWI_HDR')->where('JWIID','=',$id)->first();

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
        
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/JobWorkInvoice";
		
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

        $JWINO      =   trim($request['JWINO']);
        $objLabel   =   DB::table('TBL_TRN_JWI_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('JWINO','=',$JWINO)
        ->select('JWINO')->first();

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

        return  DB::select('SELECT MAX(JWIDT) JWIDT FROM TBL_TRN_JWI_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);

    }

    

    


    
}
