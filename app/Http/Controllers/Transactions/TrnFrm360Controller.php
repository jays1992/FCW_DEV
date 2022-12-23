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

class TrnFrm360Controller extends Controller{
    protected $form_id  =   360;
    protected $vtid_ref =   446;
    protected $view     =   "transactions.JobWork.JobWorkReturn.trnfrm";
   
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
        WHERE  AUD.VID=T1.JWRID AND  AUD.CYID_REF=T1.CYID_REF AND  AUD.BRID_REF=T1.BRID_REF AND  
        AUD.FYID_REF=T1.FYID_REF AND  AUD.VTID_REF=T1.VTID_REF AND AUD.ACTIONNAME='ADD'       
        ) AS CREATED_BY
        FROM TBL_TRN_JWR_HDR T1
        INNER JOIN TBL_TRN_AUDITTRAIL T2 ON T1.JWRID=T2.VID AND T1.VTID_REF=T2.VTID_REF AND T1.CYID_REF=T2.CYID_REF AND T1.BRID_REF=T2.BRID_REF 
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' AND T2.VTID_REF = '$this->vtid_ref' AND T2.ACTID IN (SELECT max(ACTID) FROM TBL_TRN_AUDITTRAIL A WHERE T2.VTID_REF = A.VTID_REF AND T2.VID = A.VID)
        ORDER BY T1.JWRID DESC 
        ");

        return view($this->view.$FormId,compact(['REQUEST_DATA','DATA_STATUS','objRights','objDataList','FormId']));
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
        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $objTRASPORTER  =   $this->getTransport();    
        
        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_JWR_HDR',
            'HDR_ID'=>'JWRID',
            'HDR_DOC_NO'=>'JWRNO',
            'HDR_DOC_DT'=>'JWRDT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

       
        

        $objTNCHeader           =   DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ? order by TNC_CODE ASC', [$CYID_REF, $BRID_REF, $FYID_REF, 'A' ]);
        
        $objCalculationHeader	=   Helper::getCalculationHeader(array(
            'CYID_REF'=>Auth::user()->CYID_REF,
            'BRID_REF'=>Session::get('BRID_REF'),
            'USERID'=>Auth::user()->USERID,
            'HEADING'=>'Transactions',
            'VTID_REF'=>$this->vtid_ref,
            'FORMID'=>$this->form_id
            ));

        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_JWR")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDFJWRID')->from('TBL_MST_UDFFOR_JWR')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                     
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                
                   

        $objUdfSOData = DB::table('TBL_MST_UDFFOR_JWR')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdfSOData);

        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
    

        return view($this->view.$FormId.'add',compact(['FormId','objTRASPORTER','objCalculationHeader','objUdfSOData','objTNCHeader','objCountUDF','AlpsStatus','TabSetting','doc_req','docarray']));

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
                        $row3 =    '<td  style="text-align:center;" ><input type="checkbox" class="filter-none" name="calACTUAL_'.$dindex.'" id="calACTUAL_'.$dindex.'" checked ></td>';
                    }
                    else{
                        $row3 =    '<td  style="text-align:center;" ><input type="checkbox" class="filter-none" name="calACTUAL_'.$dindex.'" id="calACTUAL_'.$dindex.'"  ></td>';
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



    public function getCodeNo(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $VID_REF    =   $request['id'];
        $fieldid    =   $request['fieldid'];

        $ObjData =  DB::select("SELECT JWIID,JWINO,JWIDT FROM TBL_TRN_JWI_HDR 
        WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF' 
        AND VID_REF='$VID_REF' AND STATUS='A'");


        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){
                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="sqcode_'.$dataRow->JWIID .'"  class="clssqid"  class="clsaltuom" value="'.$dataRow->JWIID.'" ></td>
                <td class="ROW2">'.$dataRow->JWINO;
                $row = $row.'<input type="hidden" id="txtsqcode_'.$dataRow->JWIID.'" data-desc="'.$dataRow->JWINO.'"  data-descdate="'.$dataRow->JWIDT.'"
                value="'.$dataRow->JWIID.'"/></td><td class="ROW3">'.$dataRow->JWIDT.'</td></tr>';
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
        $JWIID_REF  =   $request['id'];
        $StdCost    =   0;

        $objCOMPANY = DB::table('TBL_MST_COMPANY')
        ->where('STATUS','=','A')
        ->where('CYID','=',Auth::user()->CYID_REF)
        ->select('TBL_MST_COMPANY.NAME')
        ->first();

        $hidden     =   strpos($objCOMPANY->NAME,"ALPS")!== false?'':'hidden';

        $ObjItem =  DB::select("SELECT 
        T1.*,
        T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEM_SPECI,T2.ITEMGID_REF,T2.ICID_REF,
        T2.BUID_REF,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,
        T3.GRNNO,T3.GEJWOID_REF
        FROM TBL_TRN_JWI_MAT T1
        LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
        LEFT JOIN TBL_TRN_GRJ_HDR T3 ON T1.GRJID_REF=T3.GRJID
        WHERE T1.JWIID_REF='$JWIID_REF'");

       
        if(!empty($ObjItem)){

            foreach ($ObjItem as $index=>$dataRow){

                $StdCost = $dataRow->BILL_RATE;

                $ObjMainUOM =   DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                WHERE  CYID_REF = ?  AND UOMID = ? 
                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                [$CYID_REF, $dataRow->UOMID_REF, 'A' ]);

                $TOQTY    =     0;
                $FROMQTY  =     isset($dataRow->RECV_QTY)?$dataRow->RECV_QTY:0;

                $ObjItemGroup =  DB::select('SELECT TOP 1 GROUPCODE, GROUPNAME FROM TBL_MST_ITEMGROUP  
                WHERE  CYID_REF = ?  AND ITEMGID = ?
                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                [$CYID_REF, $dataRow->ITEMGID_REF, 'A' ]);

                $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS FROM TBL_MST_ITEMCATEGORY  
                WHERE  CYID_REF = ?  AND ICID = ?
                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                [$CYID_REF, $dataRow->ICID_REF, 'A' ]);

                if(!is_null($dataRow->BUID_REF)){
                    $ObjBusinessUnit =  DB::select('SELECT TOP 1  * FROM TBL_MST_BUSINESSUNIT  
                    WHERE  CYID_REF = ? AND BRID_REF = ?  AND BUID = ?', 
                    [$CYID_REF, $BRID_REF, $dataRow->BUID_REF]);
                }
                else
                {
                    $ObjBusinessUnit = NULL;
                }

                
                $BusinessUnit       =   isset($ObjBusinessUnit) && $ObjBusinessUnit != NULL ? $ObjBusinessUnit[0]->BUCODE.'-'.$ObjBusinessUnit[0]->BUNAME : '';
                $ALPS_PART_NO       =   $dataRow->ALPS_PART_NO;
                $CUSTOMER_PART_NO   =   $dataRow->CUSTOMER_PART_NO;
                $OEM_PART_NO        =   $dataRow->OEM_PART_NO;

                $item_unique_row_id =   $dataRow->JWIID_REF."_".$dataRow->GEJWOID_REF."_".$dataRow->GRJID_REF."_".$dataRow->JWCID_REF."_".$dataRow->JWOID_REF."_".$dataRow->PROID_REF."_".$dataRow->SOID_REF."_".$dataRow->SQID_REF."_".$dataRow->SEID_REF."_".$dataRow->ITEMID;
                
                
                $TaxAmt     =   number_format((($dataRow->BILL_QTY*$dataRow->BILL_RATE) - $dataRow->DISC_AMT),2, '.', '');
                $IGSTAMT    =   number_format((($TaxAmt*$dataRow->IGST)/100),2, '.', '');
                $CGSTAMT    =   number_format((($TaxAmt*$dataRow->CGST)/100),2, '.', '');
                $SGSTAMT    =   number_format((($TaxAmt*$dataRow->SGST)/100),2, '.', '');
                $TOTGST     =   number_format(($IGSTAMT+$CGSTAMT+$SGSTAMT),2, '.', '');
                $TOTAL      =   number_format(($TaxAmt+$IGSTAMT+$CGSTAMT+$SGSTAMT),2, '.', '');
                
                
                $row                =   '';

                $row = $row.'<tr id="item_'.$dataRow->ITEMID.'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'" data-desc1="'.$FROMQTY.'" data-desc2="'.$dataRow->BILL_RATE.'" data-desc3="'.$dataRow->DISC_PER.'" data-desc4="'.$dataRow->DISC_AMT.'" data-desc5=""  value="'.$dataRow->ITEMID.'"/></td> <td style="width:10%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'" value="'.$dataRow->NAME.'"/></td>';
                $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="" value="'.$dataRow->UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                $row = $row.'<td style="width:8%;" id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'" value=""/>'.$FROMQTY.'</td>';
                $row = $row.'<td style="width:8%;" id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="1" value="'.$dataRow->BILL_RATE.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                $row = $row.'<td style="width:8%;" id="itax_'.$dataRow->ITEMID.'"><input type="hidden" id="txtitax_'.$dataRow->ITEMID.'" data-desc="" value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                <td style="width:8%;">'.$BusinessUnit.'</td>
                <td style="width:8%;" '.$hidden.'>'.$ALPS_PART_NO.'</td>
                <td style="width:8%;" '.$hidden.'>'.$CUSTOMER_PART_NO.'</td>
                <td style="width:8%;" '.$hidden.'>'.$OEM_PART_NO.'</td>
                <td style="width:8%;" id="ise_'.$dataRow->ITEMID.'">
                    
                    <input type="hidden" id="uniquerowid_'.$index.'" 
                
                        data-desc0="'.$item_unique_row_id.'" 
                        data-desc1="'.$dataRow->SEID_REF.'"
                        data-desc2="'.$dataRow->SQID_REF.'"
                        data-desc3="'.$dataRow->SOID_REF.'"
                        data-desc4="'.$dataRow->PROID_REF.'"
                        data-desc5="'.$dataRow->JWOID_REF.'"
                        data-desc6="'.$dataRow->JWCID_REF.'"
                        data-desc7="'.$dataRow->GRJID_REF.'"
                        data-desc8="'.$dataRow->GRNNO.'"
                        data-desc9="'.$dataRow->GEJWOID_REF.'"

                        data-desc21="'.$dataRow->IGST.'"
                        data-desc22="'.$dataRow->CGST.'"
                        data-desc23="'.$dataRow->SGST.'"
                        data-desc24="'.$IGSTAMT.'"
                        data-desc25="'.$CGSTAMT.'"
                        data-desc26="'.$SGSTAMT.'"

                    />
 
                    Authorized
                </td></tr>';   

                echo $row; 
                        
            }           
        }           
        else{
            echo '<tr><td> Record not found.</td></tr>';
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

    public function save(Request $request) {

        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];
       
        for ($i=0; $i<=$r_count1; $i++){

            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i])){

                $StoreArr   =   array();
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $exp        =   explode(",",$ITEMROWID);

                foreach($exp as $val){
                    $keyid              =   explode("_",$val);
                    $batchid            =   $keyid[0];

                    $objStore =  DB::table('TBL_MST_BATCH')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('BATCHID','=',$batchid)
                        ->where('STATUS','=',"A")
                        ->select('STID_REF')
                        ->first();

                    $StoreArr[]=$objStore->STID_REF;
                }

                if(!empty($StoreArr)){
                    $StoreId    =   array_unique($StoreArr);
                    $STID_REF   =   implode(",",$StoreId);
                }
                else{
                    $STID_REF   =   NULL;
                }

                $req_data[$i] = [
                    
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'         => $request['MAIN_UOMID_REF_'.$i],
                    'GRJID_REF'         => $request['GRJID_REF_'.$i] ,
                    'RETURN_QTY'        => $request['SO_QTY_'.$i],
                    'RATE_PUOM'         => $request['RATEPUOM_'.$i],
                    'GST'               =>  (isset($request['flagtype_'.$i])!="true" ? 0 : 1) ,
                    'IGST'              => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST'              => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST'              => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'JWID_REF'          => $request['JWID_REF_'.$i],
                    'GEJID_REF'         => $request['GEJID_REF_'.$i],
                    'JWCID_REF'         => $request['JWCID_REF_'.$i],
                    'JWOID_REF'         => $request['JWOID_REF_'.$i],
                    'PROID_REF'         => $request['PROID_REF_'.$i],
                    'SOID_REF'          => $request['SOID_REF_'.$i],
                    'SQID_REF'          => $request['SQID_REF_'.$i],
                    'SEID_REF'          => $request['SEID_REF_'.$i],
                    'STORE_NAME'        => $request['STORE_NAME_'.$i],
                    'STID_REF'    	    => $STID_REF,
                    'BATCH_QTY_REF'     => $request['HiddenRowId_'.$i]
                       
                ];
            }
        }

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);


        $req_data11=array();
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

                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){
                 
                        $objBatch =  DB::table('TBL_MST_BATCH')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('BATCHID','=',$key)
                        ->where('STATUS','=',"A")
                        ->select('BATCHID','BATCH_CODE','ITEMID_REF','STID_REF','SERIALNO','UOMID_REF','CURRENT_QTY')
                        ->first();

                        $req_data11[$i][] = [
                            'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                            'STID_REF'          => $objBatch->STID_REF,
                            'UOMID_REF'         => $request['MAIN_UOMID_REF_'.$i],
                            'STOCK_INHAND'      => $objBatch->CURRENT_QTY,
                            'RETURN_QTY'        => $request['SO_QTY_'.$i],
                            'JWID_REF'          => $request['JWID_REF_'.$i],
                            'GRJID_REF'         => $request['GRJID_REF_'.$i] ,
                            'JWCID_REF'         => $request['JWCID_REF_'.$i],
                            'JWOID_REF'         => $request['JWOID_REF_'.$i],
                            'PROID_REF'         => $request['PROID_REF_'.$i],
                            'SOID_REF'          => $request['SOID_REF_'.$i],
                            'SQID_REF'          => $request['SQID_REF_'.$i],
                            'SEID_REF'          => $request['SEID_REF_'.$i],
                            'BATCH_NO'          => isset($objBatch) && $objBatch->BATCH_CODE !=""?$objBatch->BATCH_CODE:NULL    
                        ];

                    }
                }

            }
        }
        
		if($r_count1 > 0){
            $wrapped_links11["MULTISTORE"] = $req_data11; 
			$XMLSTORE = ArrayToXml::convert($wrapped_links11);
        }
        else{
            $XMLSTORE=NULL;
        }

        
        $reqdata3=array();
        for ($i=0; $i<=$r_count3; $i++){
            if(isset($request['UDFSOID_REF_'.$i]) && !is_null($request['UDFSOID_REF_'.$i])){
                $reqdata3[$i] = [
                    'UDFJWRID_REF'       => $request['UDFSOID_REF_'.$i],
                    'VALUE'   => $request['udfvalue_'.$i],
                ];
            }
        }


        if(!empty($reqdata3)){ 
            $wrapped_links3["UDF"] = $reqdata3; 
            $XMLUDF = ArrayToXml::convert($wrapped_links3);
        }
        else{
            $XMLUDF = NULL; 
        }
        
        
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

        $JWRNO             =   $request['JWRNO'];
        $JWRDT             =   $request['JWRDT'];
        $VID_REF           =   $request['VID_REF'];        
        $TRANSPORT_MODE    =   $request['TRANSPORT_MODE'];
        $PURPOSE           =   $request['PURPOSE'];
        $VENDOR_INVOICE_NO =   $request['VENDOR_INVOICE_NO'];
        $VCL_NO             =   $request['VCL_NO'];
        $TRASPORTER_NAME    =   $request['TRASPORTER_NAME'];
        $DRIVER_NAME        =   $request['DRIVER_NAME'];
        
        $log_data = [ 
            $JWRNO,$JWRDT,$VID_REF,$TRANSPORT_MODE,$PURPOSE,
            $VENDOR_INVOICE_NO,$VCL_NO,$TRASPORTER_NAME,$DRIVER_NAME,$CYID_REF, 
            $BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT,$XMLSTORE,
            $XMLUDF,$XMLTNC,$XMLCAL,$USERID, Date('Y-m-d'), 
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];

        
        $sp_result = DB::select('EXEC SP_JWR_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data); 
        
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

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $objTRASPORTER  =   $this->getTransport();

            $DATA_HDR = DB::table('TBL_TRN_JWR_HDR')
            ->leftJoin('TBL_MST_TRANSPORTER', 'TBL_TRN_JWR_HDR.TRANSPORTERID_REF','=','TBL_MST_TRANSPORTER.TRANSPORTERID') 
            ->where('TBL_TRN_JWR_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_JWR_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_JWR_HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_JWR_HDR.JWRID','=',$id)
            ->select(
                'TBL_TRN_JWR_HDR.*',
                'TBL_MST_TRANSPORTER.TRANSPORTERID',
                'TBL_MST_TRANSPORTER.TRANSPORTER_CODE',
                'TBL_MST_TRANSPORTER.TRANSPORTER_NAME'
                )->first();
            
            $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
            ->where('BELONGS_TO','=','Vendor')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('SGLID','=',$DATA_HDR->VID_REF)    
            ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
            ->first();
    

            $DATA_MAT = DB::select("SELECT 
            T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,
            CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
            T4.JWINO,
            T5.GRNNO,T5.GEJWOID_REF,
            T6.RECV_QTY
            FROM TBL_TRN_JWR_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            LEFT JOIN TBL_TRN_JWI_HDR T4 ON T1.JWIID_REF=T4.JWIID
            LEFT JOIN TBL_TRN_GRJ_HDR T5 ON T1.GRJID_REF=T5.GRJID
            LEFT JOIN TBL_TRN_JWI_MAT T6 ON T1.JWIID_REF=T6.JWIID_REF 
            AND T1.ITEMID_REF=T6.ITEMID_REF AND T1.UOMID_REF=T6.UOMID_REF AND T1.JWOID_REF=T6.JWOID_REF 
            AND T1.GRJID_REF=T6.GRJID_REF AND T1.JWCID_REF=T6.JWCID_REF AND T1.PROID_REF=T6.PROID_REF 
            AND T1.SOID_REF=T6.SOID_REF AND isnull(T1.SQID_REF,0)=isnull(T6.SQID_REF,0) 
            AND isnull(T1.SEID_REF,0)=isnull(T6.SEID_REF,0)
            WHERE T1.JWRID_REF='$id' ORDER BY T1.JWR_MATID ASC
            ");
            

            $objCount1 = count($DATA_MAT);

            $objSOTNC = DB::table('TBL_TRN_JWR_TNC')                    
            ->where('TBL_TRN_JWR_TNC.JWRID_REF','=',$id)
            ->select('TBL_TRN_JWR_TNC.*')
            ->orderBy('TBL_TRN_JWR_TNC.JWR_TNCID','ASC')
            ->get()->toArray();
            $objCount2 = count($objSOTNC);

            $objSOUDF = DB::table('TBL_TRN_JWR_UDF')                    
            ->where('TBL_TRN_JWR_UDF.JWRID_REF','=',$id)
            ->select('TBL_TRN_JWR_UDF.*')
            ->orderBy('TBL_TRN_JWR_UDF.JWR_UDFID','ASC')
            ->get()->toArray();
            $objCount3 = count($objSOUDF);

            $objSOCAL = DB::table('TBL_TRN_JWR_CAL')                    
            ->where('TBL_TRN_JWR_CAL.JWRID_REF','=',$id)
            ->select('TBL_TRN_JWR_CAL.*')
            ->orderBy('TBL_TRN_JWR_CAL.JWR_CALID','ASC')
            ->get()->toArray();
            $objCount4 = count($objSOCAL);

            $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
            WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? 
            order by CTCODE ASC', [$CYID_REF, $BRID_REF, $FYID_REF ]);
    
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

            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_JWR")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('UDFJWRID')->from('TBL_MST_UDFFOR_JWR')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                        
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                        
                    
    
            $objUdfSOData = DB::table('TBL_MST_UDFFOR_JWR')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_JWR")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDFJWRID')->from('TBL_MST_UDFFOR_JWR')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                            
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                            
            $objUdfSOData2 = DB::table('TBL_MST_UDFFOR_JWR')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 

            $TAXSTATE      =    array();
           
            $objTNCDetails =    DB::table('TBL_MST_TNC_DETAILS')->select('*')->get() ->toArray(); 
            $objCalDetails =    DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')->get() ->toArray(); 
        
           
            $FormId         =   $this->form_id;
            $AlpsStatus =   $this->AlpsStatus();

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        return view($this->view.$FormId.'edit',compact([
            'FormId','objTRASPORTER','DATA_HDR','objRights','objCount1','objCount2','objCount3','objCount4',
            'DATA_MAT','objSOCAL','objSOTNC','objSOUDF','objCalculationHeader','objUdfSOData','objTNCHeader',
            'objsubglcode','objTNCDetails','objUdfSOData2','objCalHeader','objCalDetails','TAXSTATE','AlpsStatus','TabSetting'
            ]));
        }
     
    }
     
       public function view($id){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $objTRASPORTER  =   $this->getTransport();

            $DATA_HDR = DB::table('TBL_TRN_JWR_HDR')
            ->leftJoin('TBL_MST_TRANSPORTER', 'TBL_TRN_JWR_HDR.TRANSPORTERID_REF','=','TBL_MST_TRANSPORTER.TRANSPORTERID') 
            ->where('TBL_TRN_JWR_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_JWR_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_JWR_HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_TRN_JWR_HDR.JWRID','=',$id)
            ->select(
                'TBL_TRN_JWR_HDR.*',
                'TBL_MST_TRANSPORTER.TRANSPORTERID',
                'TBL_MST_TRANSPORTER.TRANSPORTER_CODE',
                'TBL_MST_TRANSPORTER.TRANSPORTER_NAME'
                )->first();
            
            $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
            ->where('BELONGS_TO','=','Vendor')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('SGLID','=',$DATA_HDR->VID_REF)    
            ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
            ->first();
    

            $DATA_MAT = DB::select("SELECT 
            T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,
            CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
            T4.JWINO,
            T5.GRNNO,T5.GEJWOID_REF,
            T6.RECV_QTY
            FROM TBL_TRN_JWR_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            LEFT JOIN TBL_TRN_JWI_HDR T4 ON T1.JWIID_REF=T4.JWIID
            LEFT JOIN TBL_TRN_GRJ_HDR T5 ON T1.GRJID_REF=T5.GRJID
            LEFT JOIN TBL_TRN_JWI_MAT T6 ON T1.JWIID_REF=T6.JWIID_REF 
            AND T1.ITEMID_REF=T6.ITEMID_REF AND T1.UOMID_REF=T6.UOMID_REF AND T1.JWOID_REF=T6.JWOID_REF 
            AND T1.GRJID_REF=T6.GRJID_REF AND T1.JWCID_REF=T6.JWCID_REF AND T1.PROID_REF=T6.PROID_REF 
            AND T1.SOID_REF=T6.SOID_REF AND isnull(T1.SQID_REF,0)=isnull(T6.SQID_REF,0) 
            AND isnull(T1.SEID_REF,0)=isnull(T6.SEID_REF,0)
            WHERE T1.JWRID_REF='$id' ORDER BY T1.JWR_MATID ASC
            ");
            

            $objCount1 = count($DATA_MAT);

            $objSOTNC = DB::table('TBL_TRN_JWR_TNC')                    
            ->where('TBL_TRN_JWR_TNC.JWRID_REF','=',$id)
            ->select('TBL_TRN_JWR_TNC.*')
            ->orderBy('TBL_TRN_JWR_TNC.JWR_TNCID','ASC')
            ->get()->toArray();
            $objCount2 = count($objSOTNC);

            $objSOUDF = DB::table('TBL_TRN_JWR_UDF')                    
            ->where('TBL_TRN_JWR_UDF.JWRID_REF','=',$id)
            ->select('TBL_TRN_JWR_UDF.*')
            ->orderBy('TBL_TRN_JWR_UDF.JWR_UDFID','ASC')
            ->get()->toArray();
            $objCount3 = count($objSOUDF);

            $objSOCAL = DB::table('TBL_TRN_JWR_CAL')                    
            ->where('TBL_TRN_JWR_CAL.JWRID_REF','=',$id)
            ->select('TBL_TRN_JWR_CAL.*')
            ->orderBy('TBL_TRN_JWR_CAL.JWR_CALID','ASC')
            ->get()->toArray();
            $objCount4 = count($objSOCAL);

            $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
            WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? 
            order by CTCODE ASC', [$CYID_REF, $BRID_REF, $FYID_REF ]);
    
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

            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_JWR")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('UDFJWRID')->from('TBL_MST_UDFFOR_JWR')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                        
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                        
                    
    
            $objUdfSOData = DB::table('TBL_MST_UDFFOR_JWR')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_JWR")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDFJWRID')->from('TBL_MST_UDFFOR_JWR')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                            
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                            
            $objUdfSOData2 = DB::table('TBL_MST_UDFFOR_JWR')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 

            $TAXSTATE      =    array();
           
            $objTNCDetails =    DB::table('TBL_MST_TNC_DETAILS')->select('*')->get() ->toArray(); 
            $objCalDetails =    DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')->get() ->toArray(); 
        
           
            $FormId         =   $this->form_id;
            $AlpsStatus =   $this->AlpsStatus();

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        return view($this->view.$FormId.'view',compact([
            'FormId','objTRASPORTER','DATA_HDR','objRights','objCount1','objCount2','objCount3','objCount4',
            'DATA_MAT','objSOCAL','objSOTNC','objSOUDF','objCalculationHeader','objUdfSOData','objTNCHeader',
            'objsubglcode','objTNCDetails','objUdfSOData2','objCalHeader','objCalDetails','TAXSTATE','AlpsStatus','TabSetting'
            ]));
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
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $exp        =   explode(",",$ITEMROWID);

                foreach($exp as $val){
                    $keyid              =   explode("_",$val);
                    $batchid            =   $keyid[0];

                    $objStore =  DB::table('TBL_MST_BATCH')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('BATCHID','=',$batchid)
                        ->where('STATUS','=',"A")
                        ->select('STID_REF')
                        ->first();

                    $StoreArr[]=$objStore->STID_REF;
                }

                if(!empty($StoreArr)){
                    $StoreId    =   array_unique($StoreArr);
                    $STID_REF   =   implode(",",$StoreId);
                }
                else{
                    $STID_REF   =   NULL;
                }

                $req_data[$i] = [
                    
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'         => $request['MAIN_UOMID_REF_'.$i],
                    'GRJID_REF'         => $request['GRJID_REF_'.$i] ,
                    'RETURN_QTY'        => $request['SO_QTY_'.$i],
                    'RATE_PUOM'         => $request['RATEPUOM_'.$i],
                    'GST'               =>  (isset($request['flagtype_'.$i])!="true" ? 0 : 1) ,
                    'IGST'              => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST'              => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST'              => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'JWID_REF'          => $request['JWID_REF_'.$i],
                    'GEJID_REF'         => $request['GEJID_REF_'.$i],
                    'JWCID_REF'         => $request['JWCID_REF_'.$i],
                    'JWOID_REF'         => $request['JWOID_REF_'.$i],
                    'PROID_REF'         => $request['PROID_REF_'.$i],
                    'SOID_REF'          => $request['SOID_REF_'.$i],
                    'SQID_REF'          => $request['SQID_REF_'.$i],
                    'SEID_REF'          => $request['SEID_REF_'.$i],
                    'STORE_NAME'        => $request['STORE_NAME_'.$i],
                    'STID_REF'    	    => $STID_REF,
                    'BATCH_QTY_REF'     => $request['HiddenRowId_'.$i]
                       
                ];
            }
        }

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);


        $req_data11=array();
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

                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){
                 
                        $objBatch =  DB::table('TBL_MST_BATCH')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('BATCHID','=',$key)
                        ->where('STATUS','=',"A")
                        ->select('BATCHID','BATCH_CODE','ITEMID_REF','STID_REF','SERIALNO','UOMID_REF','CURRENT_QTY')
                        ->first();

                        $req_data11[$i][] = [
                            'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                            'STID_REF'          => $objBatch->STID_REF,
                            'UOMID_REF'         => $request['MAIN_UOMID_REF_'.$i],
                            'STOCK_INHAND'      => $objBatch->CURRENT_QTY,
                            'RETURN_QTY'        => $request['SO_QTY_'.$i],
                            'JWID_REF'          => $request['JWID_REF_'.$i],
                            'GRJID_REF'         => $request['GRJID_REF_'.$i] ,
                            'JWCID_REF'         => $request['JWCID_REF_'.$i],
                            'JWOID_REF'         => $request['JWOID_REF_'.$i],
                            'PROID_REF'         => $request['PROID_REF_'.$i],
                            'SOID_REF'          => $request['SOID_REF_'.$i],
                            'SQID_REF'          => $request['SQID_REF_'.$i],
                            'SEID_REF'          => $request['SEID_REF_'.$i],
                            'BATCH_NO'          => isset($objBatch) && $objBatch->BATCH_CODE !=""?$objBatch->BATCH_CODE:NULL    
                        ];

                    }
                }

            }
        }
        
		if($r_count1 > 0){
            $wrapped_links11["MULTISTORE"] = $req_data11; 
			$XMLSTORE = ArrayToXml::convert($wrapped_links11);
        }
        else{
            $XMLSTORE=NULL;
        }

        
        $reqdata3=array();
        for ($i=0; $i<=$r_count3; $i++){
            if(isset($request['UDFSOID_REF_'.$i]) && !is_null($request['UDFSOID_REF_'.$i])){
                $reqdata3[$i] = [
                    'UDFJWRID_REF'       => $request['UDFSOID_REF_'.$i],
                    'VALUE'   => $request['udfvalue_'.$i],
                ];
            }
        }


        if(!empty($reqdata3)){ 
            $wrapped_links3["UDF"] = $reqdata3; 
            $XMLUDF = ArrayToXml::convert($wrapped_links3);
        }
        else{
            $XMLUDF = NULL; 
        }
        
        
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

        $JWRNO             =   $request['JWRNO'];
        $JWRDT             =   $request['JWRDT'];
        $VID_REF           =   $request['VID_REF'];        
        $TRANSPORT_MODE    =   $request['TRANSPORT_MODE'];
        $PURPOSE           =   $request['PURPOSE'];
        $VENDOR_INVOICE_NO =   $request['VENDOR_INVOICE_NO'];
        $VCL_NO            =   $request['VCL_NO'];
        $TRASPORTER_NAME   =   $request['TRASPORTER_NAME'];
        $DRIVER_NAME       =   $request['DRIVER_NAME'];
        
        $log_data = [ 
            $JWRNO,$JWRDT,$VID_REF,$TRANSPORT_MODE,$PURPOSE,
            $VENDOR_INVOICE_NO,$VCL_NO,$TRASPORTER_NAME,$DRIVER_NAME,$CYID_REF, 
            $BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT,$XMLSTORE,
            $XMLUDF,$XMLTNC,$XMLCAL,$USERID, Date('Y-m-d'), 
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_JWR_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data); 
        
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $JWRNO. ' Sucessfully Updated.']);

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
                    $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                    $ITEMROWID  =   $request['HiddenRowId_'.$i];
                    $exp        =   explode(",",$ITEMROWID);
    
                    foreach($exp as $val){
                        $keyid              =   explode("_",$val);
                        $batchid            =   $keyid[0];
    
                        $objStore =  DB::table('TBL_MST_BATCH')
                            ->where('ITEMID_REF','=',$ITEMID_REF)
                            ->where('BATCHID','=',$batchid)
                            ->where('STATUS','=',"A")
                            ->select('STID_REF')
                            ->first();
    
                        $StoreArr[]=$objStore->STID_REF;
                    }
    
                    if(!empty($StoreArr)){
                        $StoreId    =   array_unique($StoreArr);
                        $STID_REF   =   implode(",",$StoreId);
                    }
                    else{
                        $STID_REF   =   NULL;
                    }
    
                    $req_data[$i] = [
                        
                        'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                        'UOMID_REF'         => $request['MAIN_UOMID_REF_'.$i],
                        'GRJID_REF'         => $request['GRJID_REF_'.$i] ,
                        'RETURN_QTY'        => $request['SO_QTY_'.$i],
                        'RATE_PUOM'         => $request['RATEPUOM_'.$i],
                        'GST'               =>  (isset($request['flagtype_'.$i])!="true" ? 0 : 1) ,
                        'IGST'              => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                        'CGST'              => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                        'SGST'              => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                        'JWID_REF'          => $request['JWID_REF_'.$i],
                        'GEJID_REF'         => $request['GEJID_REF_'.$i],
                        'JWCID_REF'         => $request['JWCID_REF_'.$i],
                        'JWOID_REF'         => $request['JWOID_REF_'.$i],
                        'PROID_REF'         => $request['PROID_REF_'.$i],
                        'SOID_REF'          => $request['SOID_REF_'.$i],
                        'SQID_REF'          => $request['SQID_REF_'.$i],
                        'SEID_REF'          => $request['SEID_REF_'.$i],
                        'STORE_NAME'        => $request['STORE_NAME_'.$i],
                        'STID_REF'    	    => $STID_REF,
                        'BATCH_QTY_REF'     => $request['HiddenRowId_'.$i]
                           
                    ];
                }
            }
    
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
    
    
            $req_data11=array();
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
    
                    if(!empty($dataArr)){
                        foreach($dataArr as $key=>$val){
                     
                            $objBatch =  DB::table('TBL_MST_BATCH')
                            ->where('ITEMID_REF','=',$ITEMID_REF)
                            ->where('BATCHID','=',$key)
                            ->where('STATUS','=',"A")
                            ->select('BATCHID','BATCH_CODE','ITEMID_REF','STID_REF','SERIALNO','UOMID_REF','CURRENT_QTY')
                            ->first();
    
                            $req_data11[$i][] = [
                                'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                                'STID_REF'          => $objBatch->STID_REF,
                                'UOMID_REF'         => $request['MAIN_UOMID_REF_'.$i],
                                'STOCK_INHAND'      => $objBatch->CURRENT_QTY,
                                'RETURN_QTY'        => $request['SO_QTY_'.$i],
                                'JWID_REF'          => $request['JWID_REF_'.$i],
                                'GRJID_REF'         => $request['GRJID_REF_'.$i] ,
                                'JWCID_REF'         => $request['JWCID_REF_'.$i],
                                'JWOID_REF'         => $request['JWOID_REF_'.$i],
                                'PROID_REF'         => $request['PROID_REF_'.$i],
                                'SOID_REF'          => $request['SOID_REF_'.$i],
                                'SQID_REF'          => $request['SQID_REF_'.$i],
                                'SEID_REF'          => $request['SEID_REF_'.$i],
                                'BATCH_NO'          => isset($objBatch) && $objBatch->BATCH_CODE !=""?$objBatch->BATCH_CODE:NULL    
                            ];
    
                        }
                    }
    
                }
            }
            
            if($r_count1 > 0){
                $wrapped_links11["MULTISTORE"] = $req_data11; 
                $XMLSTORE = ArrayToXml::convert($wrapped_links11);
            }
            else{
                $XMLSTORE=NULL;
            }
    
            
            $reqdata3=array();
            for ($i=0; $i<=$r_count3; $i++){
                if(isset($request['UDFSOID_REF_'.$i]) && !is_null($request['UDFSOID_REF_'.$i])){
                    $reqdata3[$i] = [
                        'UDFJWRID_REF'       => $request['UDFSOID_REF_'.$i],
                        'VALUE'   => $request['udfvalue_'.$i],
                    ];
                }
            }
    
    
            if(!empty($reqdata3)){ 
                $wrapped_links3["UDF"] = $reqdata3; 
                $XMLUDF = ArrayToXml::convert($wrapped_links3);
            }
            else{
                $XMLUDF = NULL; 
            }
            
            
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

        $JWRNO             =   $request['JWRNO'];
        $JWRDT             =   $request['JWRDT'];
        $VID_REF           =   $request['VID_REF'];        
        $TRANSPORT_MODE    =   $request['TRANSPORT_MODE'];
        $PURPOSE           =   $request['PURPOSE'];
        $VENDOR_INVOICE_NO =   $request['VENDOR_INVOICE_NO'];
        $VCL_NO            =   $request['VCL_NO'];
        $TRASPORTER_NAME   =   $request['TRASPORTER_NAME'];
        $DRIVER_NAME       =   $request['DRIVER_NAME'];
        
        $log_data = [ 
            $JWRNO,$JWRDT,$VID_REF,$TRANSPORT_MODE,$PURPOSE,
            $VENDOR_INVOICE_NO,$VCL_NO,$TRASPORTER_NAME,$DRIVER_NAME,$CYID_REF, 
            $BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT,$XMLSTORE,
            $XMLUDF,$XMLTNC,$XMLCAL,$USERID, Date('Y-m-d'), 
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_JWR_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);


        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $JWRNO. ' Sucessfully Approved.']);

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
                $TABLE      =   "TBL_TRN_JWR_HDR";
                $FIELD      =   "JWRID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
            
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_JWR ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
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
        $TABLE      =   "TBL_TRN_JWR_HDR";
        $FIELD      =   "JWRID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_JWR_MAT',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_JWR_MULTISTORE',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_JWR_UDF',
        ];
        $req_data[3]=[
            'NT'  => 'TBL_TRN_JWR_TNC',
        ];
        $req_data[4]=[
            'NT'  => 'TBL_TRN_JWR_CAL',
        ];


        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_JWR  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_JWR_HDR')->where('JWRID','=',$id)->first();

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
        
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/JobWorkReturn";
		
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

    public function checkso(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $JWRNO = $request->JWRNO;
        
        $objSO = DB::table('TBL_TRN_JWR_HDR')
        ->where('TBL_TRN_JWR_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_JWR_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_JWR_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_JWR_HDR.JWRNO','=',$JWRNO)
        ->select('TBL_TRN_JWR_HDR.JWRNO')
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


    public function getStoreDetails(Request $request){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $ITEMID_REF     =   $request['ITEMID_REF'];
        $UOMID_REF      =   $request['UOMID_REF'];
        $ROW_ID         =   $request['ROW_ID'];
        $ITEMROWID      =   $request['ITEMROWID'];
        $ACTION_TYPE    =   $request['ACTION_TYPE'] =="VIEW"?'disabled':'';
        $SRNOA          =   NULL;
        $BATCHNOA       =   NULL;

        

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

        $objBatch =  DB::SELECT("SELECT T1.BATCHID,T1.BATCH_CODE,T1.ITEMID_REF,T1.STID_REF,T1.SERIALNO,T1.UOMID_REF,
        T1.CURRENT_QTY,T2.STCODE,T2.NAME AS STNAME,T3.UOMCODE,T3.DESCRIPTIONS AS UOMDESCRIPTIONS 
        FROM TBL_MST_BATCH T1
        LEFT JOIN TBL_MST_STORE T2 ON T1.STID_REF=T2.STID
        LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
        WHERE T1.STATUS='A' AND T1.ITEMID_REF='$ITEMID_REF' AND T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' 
        AND T1.UOMID_REF='$UOMID_REF'
        ");
     
        echo '<thead>';
        echo '<tr>';
        echo $BATCHNOA =='1'?'<th>Batch / Lot No</th>':'';
        echo '<th>Store</th>';
        echo $SRNOA =='1'?'<th>Serial No</th>':'';
        echo '<th>Main UoM</th>';
        echo '<th>Stock-in-hand</th>';
        echo '<th>Dispatch Qty</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach($objBatch as $key=>$val){

            $qtyvalue   =   array_key_exists($val->BATCHID, $dataArr)?$dataArr[$val->BATCHID]:'';

            if($request['ACTION_TYPE'] =="ADD"){
                $CURRENT_QTY=$val->CURRENT_QTY;
            }
            else{
                $CURRENT_QTY=(floatval($val->CURRENT_QTY)+floatval($qtyvalue));
            }

            echo '<tr  class="participantRow33">';
            echo $BATCHNOA =='1'?'<td>'.$val->BATCH_CODE.'</td>':'';
            echo '<td>'.$val->STCODE.' - '.$val->STNAME.'</td>';
            echo $SRNOA =='1'?'<td>'.$val->SERIALNO.'</td>':'';
            echo '<td>'.$val->UOMCODE.' - '.$val->UOMDESCRIPTIONS.'</td>';
            echo '<td>'.$CURRENT_QTY.'</td>';
            echo '<td><input '.$ACTION_TYPE.' type="text" name="UserQty_'.$key.'" id="UserQty_'.$key.'" value="'.$qtyvalue.'" class="form-control qtytext" onkeyup="checkStoreQty('.$ROW_ID.','.$CURRENT_QTY.',this.value,'.$key.')" onkeypress="return isNumberDecimalKey(event,this)" autocomplete="off"  ></td>';
            echo '<td hidden><input type="hidden" name="BATCHID_'.$key.'" id="BATCHID_'.$key.'" value="'.$val->BATCHID.'" class="qtytext" ></td>';
            echo '<td hidden><input type="hidden" name="STORENAME_'.$key.'" id="STORENAME_'.$key.'" value="'.$val->STNAME.'" ></td>';
            echo '</tr>';
        }

        echo '</tbody>';
        exit();
    }

    

    
}
