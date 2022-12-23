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

class TrnFrm354Controller extends Controller{

    protected $form_id  = 354;
    protected $vtid_ref = 440;
    protected $view     = "transactions.JobWork.JobWorkOrder.trnfrm354";
   
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

        $objDataList    =	DB::select("SELECT '$USER_LEVEL' AS USER_LEVEL,T1.*,T2.ACTIONNAME,T3.SLNAME,
        (
        SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
        LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
        WHERE  AUD.VID=T1.JWOID AND  AUD.CYID_REF=T1.CYID_REF AND  AUD.BRID_REF=T1.BRID_REF AND  
        AUD.FYID_REF=T1.FYID_REF AND  AUD.VTID_REF=T1.VTID_REF AND AUD.ACTIONNAME='ADD'       
        ) AS CREATED_BY

        FROM TBL_TRN_JWO_HDR T1
        INNER JOIN TBL_TRN_AUDITTRAIL T2 ON T1.JWOID=T2.VID AND T1.VTID_REF=T2.VTID_REF AND T1.CYID_REF=T2.CYID_REF AND T1.BRID_REF=T2.BRID_REF 
        INNER JOIN TBL_MST_SUBLEDGER T3 ON T1.VID_REF = T3.SGLID  
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' AND T2.VTID_REF = '$this->vtid_ref' AND T2.ACTID IN (SELECT max(ACTID) FROM TBL_TRN_AUDITTRAIL A WHERE T2.VTID_REF = A.VTID_REF AND T2.VID = A.VID)
        ORDER BY T1.JWOID DESC 
        ");
        
        return view($this->view,compact(['REQUEST_DATA','DATA_STATUS','FormId','objRights','objDataList','FormId']));
    }
	
	public function ViewReport($request) {

        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
           
        $JWOID       =   $myValue['JWOID'];
        $Flag       =   $myValue['Flag'];

        // $objSalesOrder = DB::table('TBL_TRN_PROR01_HDR')
        // ->where('TBL_TRN_PROR01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        // ->where('TBL_TRN_PROR01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        // ->where('TBL_TRN_PROR01_HDR.POID','=',$POID)
        // ->select('TBL_TRN_PROR01_HDR.*')
        // ->first();
        
        
        $ssrs = new \SSRS\Report('http://103.139.58.23:8181//ReportServer/', array('username' => 'App', 'password' => 'admin@123'));
        $result = $ssrs->loadReport('/ERP/JobWorkPrint');
		//$result = $ssrs->loadReport('/ERP/POPrint -ZEP');
        
        $reportParameters = array(
            'JWOID' => $JWOID,
        );
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
        ->setExecutionParameters($parameters);
        if($Flag == 'H')
        {
            $output = $ssrs->render('HTML4.0'); 
            echo $output;
        }
        else if($Flag == 'P')
        {
            $output = $ssrs->render('PDF'); 
            return $output->download('Report.pdf');
        }
        else if($Flag == 'E')
        {
            $output = $ssrs->render('EXCEL'); 
            return $output->download('Report.xls');
        }
		else if($Flag == 'R')
        {
            $output = $ssrs->render('HTML4.0'); // PDF | XML | CSV | HTML4.0
            echo $output;

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
        
        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $objlastdt  =   $this->getLastdt();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_JWO_HDR',
            'HDR_ID'=>'JWOID',
            'HDR_DOC_NO'=>'JWONO',
            'HDR_DOC_DT'=>'JWODT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

       
        

        $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC 
        FROM TBL_MST_TNC  
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

        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_JWO")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDFJWOID')->from('TBL_MST_UDFFOR_JWO')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                     
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                
                   

        $objUdf  = DB::table('TBL_MST_UDFFOR_JWO')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdf); 
                    
        $FormId  = $this->form_id;
        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        return view($this->view.'add', compact(
            ['AlpsStatus','FormId','objCalculationHeader','objTNCHeader','objUdf','objCountUDF','objlastdt','TabSetting','doc_req','docarray']
        ));       
    }

    public function getLastdt(){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        return  DB::select('SELECT MAX(JWODT) JWODT FROM TBL_TRN_JWO_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
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
                        $row2 =    '<td hidden style="text-align:center;" ><input type="checkbox" class="filter-none" name="calGST_'.$dindex.'" id="calGST_'.$dindex.'" checked ></td>';
                    }
                    else{
                        $row2 =    '<td hidden style="text-align:center;" ><input type="checkbox" class="filter-none" name="calGST_'.$dindex.'" id="calGST_'.$dindex.'"  ></td>';
                    }

                    if($dataRow->ACTUAL == 1){
                        $row3 =    '<td hidden style="text-align:center;" ><input type="checkbox" class="filter-none" name="calACTUAL_'.$dindex.'" id="calACTUAL_'.$dindex.'" checked ></td>';
                    }
                    else{
                        $row3 =    '<td hidden style="text-align:center;" ><input type="checkbox" class="filter-none" name="calACTUAL_'.$dindex.'" id="calACTUAL_'.$dindex.'"  ></td>';
                    }

                    $row = $row.'<tr  class="participantRow5">
                    <td><input type="text" name="popupTID_'.$dindex.'" id="popupTID_'.$dindex.'" class="form-control"  autocomplete="off" value="'.$dataRow->COMPONENT.'"  readonly/></td>
                    <td hidden><input type="hidden" name="TID_REF_'.$dindex.'" id="TID_REF_'.$dindex.'" class="form-control" value="'.$dataRow->TID.'" autocomplete="off" /></td>
                    <td><input type="text" name="RATE_'.$dindex.'" id="RATE_'.$dindex.'" class="form-control four-digits"  value="'.$dataRow->RATEPERCENTATE.'" maxlength="6" autocomplete="off"  readonly/></td>
                    <td hidden><input type="hidden" name="BASIS_'.$dindex.'" id="BASIS_'.$dindex.'" class="form-control"  value="'.$dataRow->BASIS.'" autocomplete="off" /></td>
                    <td hidden><input type="hidden" name="SQNO_'.$dindex.'" id="SQNO_'.$dindex.'" class="form-control"  value="'.$dataRow->SQNO.'" autocomplete="off" /></td>
                    <td hidden><input type="hidden" name="FORMULA_'.$dindex.'" id="FORMULA_'.$dindex.'" class="form-control"  value="'.$dataRow->FORMULA.'" autocomplete="off" /></td>
                    <td><input type="text" name="VALUE_'.$dindex.'" id="VALUE_'.$dindex.'" class="form-control two-digits"  value="'.$dataRow->AMOUNT.'" maxlength="15" autocomplete="off"  readonly/></td>
                    '.$row2.'<td hidden><input type="text" name="calIGST_'.$dindex.'" id="calIGST_'.$dindex.'" class="form-control four-digits" maxlength="8"   autocomplete="off"  readonly/></td>
                    <td hidden><input type="text" name="AMTIGST_'.$dindex.'" id="AMTIGST_'.$dindex.'" class="form-control two-digits" maxlength="15"   autocomplete="off"  readonly/></td>
                    <td hidden><input type="text" name="calCGST_'.$dindex.'" id="calCGST_'.$dindex.'" class="form-control four-digits" maxlength="8"   autocomplete="off"  readonly/></td>
                    <td hidden><input type="text" name="AMTCGST_'.$dindex.'" id="AMTCGST_'.$dindex.'" class="form-control two-digits" maxlength="15"   autocomplete="off"  readonly/></td>
                    <td hidden><input type="text" name="calSGST_'.$dindex.'" id="calSGST_'.$dindex.'" class="form-control four-digits" maxlength="8"   autocomplete="off"  readonly/></td>
                    <td hidden><input type="text" name="AMTSGST_'.$dindex.'" id="AMTSGST_'.$dindex.'" class="form-control two-digits" maxlength="15"   autocomplete="off"  readonly/></td>
                    <td hidden><input type="text" name="TOTGSTAMT_'.$dindex.'" id="TOTGSTAMT_'.$dindex.'" class="form-control two-digits"  maxlength="15"   autocomplete="off"  readonly/></td>
                    '.$row3.'<td hidden align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
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

    public function save(Request $request) {
    
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];
        $r_count5 = $request['Row_Count5'];
        
        $req_data=array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $req_data[$i] = [
                    'PROID_REF'      => $request['PROID_REF_'.$i],
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'     => $request['MAIN_UOMID_REF_'.$i],
                    'PROD_QTY'         => $request['QTY_'.$i],
                    'JWO_QTY'     => $request['PD_OR_QTY_'.$i],
                    'EDA'     => $request['EDA_'.$i],
                    'RATE_PUOM'     => $request['RATEPUOM_'.$i],
                    'SOID_REF'      => $request['SOID_REF_'.$i],
                    'SQID_REF'     => $request['SQID_REF_'.$i],
                    'SEID_REF'     => $request['SEID_REF_'.$i]   
                ];

            }
        }

        


        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);


        $req_data5=array();
        for ($i=0; $i<=$r_count5; $i++){
            if(isset($request['REQ_SOITEMID_REF_'.$i])){

                $req_data5[$i] = [
                    'ITEMID_REF'    	=> $request['REQ_ITEMID_REF_'.$i],
                    'STD_BOM_QTY'     		=> $request['REQ_BOM_QTY_'.$i],
                    'AFT_CHANGES_QTY'	=> $request['REQ_CHANGES_PD_OR_QTY_'.$i],
                    'MAIN_ITEMID_REF'   => $request['REQ_SOITEMID_REF_'.$i],
                    'PROID_REF'      	=> $request['REQ_PROID_REF_'.$i],
                    'SOID_REF'      	=> $request['REQ_SOID_REF_'.$i],
                    'SQID_REF'     		=> $request['REQ_SQID_REF_'.$i],  
                    'SEID_REF'     		=> $request['REQ_SEID_REF_'.$i]    
                ];

            }
        }

        
		if($r_count5 > 0){
            $wrapped_links5["REQ"] = $req_data5; 
			$XMLREQ = ArrayToXml::convert($wrapped_links5);
        }
        else{
            $XMLREQ=NULL;
        }		


        
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
        
       
        $udffield_Data = [];      
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFJWOID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           }
            
        }

        if(count($udffield_Data)>0){
            $udffield_wrapped["UDF"] = $udffield_Data;  
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

        $JWONO = $request['JWONO'];
        $JWODT = $request['JWODT'];
        $VID_REF = $request['VID_REF'];
        $DIRECTJWO = $request['hiddenDirect'];
        
        $log_data = [ 
            $JWONO,     $JWODT,     $VID_REF,       $CYID_REF,      $BRID_REF,      
            $FYID_REF,  $VTID_REF,  $XMLMAT,        $XMLREQ,        $XMLTNC,
            $XMLCAL,    $XMLUDF,    $USERID_REF,    Date('Y-m-d'),  Date('h:i:s.u'),    
            $ACTIONNAME,$IPADDRESS,$DIRECTJWO
        ];

        
        $sp_result = DB::select('EXEC SP_JWO_IN ?,?,?,?,? ,?,?,?,?,? ,?,?,?,?,? ,?,?,?', $log_data);     
        
    
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');

        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        
        exit();   
     }


     public function edit($id){       
        
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

            $objMstResponse = DB::table('TBL_TRN_JWO_HDR')
                ->where('FYID_REF','=',Session::get('FYID_REF'))
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('JWOID','=',$id)
                ->select('*')
                ->first();

            $objMAT = DB::select("SELECT T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,
            T3.UOMCODE,T3.DESCRIPTIONS,
            T5.PRO_NO
            FROM TBL_TRN_JWO_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            LEFT JOIN TBL_TRN_PDPRO_HDR T5 ON T1.PROID_REF=T5.PROID
            WHERE T1.JWOID_REF='$id' ORDER BY T1.JWOID_REF ASC"); 

            $objREQ = DB::select("SELECT T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,
            T3.ICODE AS SOITEMID_CODE
            FROM TBL_TRN_JWO_REQ T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_ITEM T3 ON T1.MAIN_ITEMID_REF=T3.ITEMID
            WHERE T1.JWOID_REF='$id' ORDER BY T1.JWOID_REF ASC");
			
			$material_array=array();

			if(isset($objREQ) && !empty($objREQ)){
				foreach($objREQ as $row){

					$ITEMID		=	$row->MAIN_ITEMID_REF;
					
					$BOM_HDR =   DB::table('TBL_MST_BOM_HDR')
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF)
                        ->where('ITEMID_REF','=',$ITEMID)
                        ->where('STATUS','=','A')
                        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                        ->select('BOMID')
                        ->first();
						
					$MAIN_PD_OR_QTY	=($row->AFT_CHANGES_QTY/$row->STD_BOM_QTY);

                    $material_array[]=array(
						'BOMID_REF'=>isset($BOM_HDR) ? $BOM_HDR->BOMID : '',
						'MAIN_PD_OR_QTY'=>$MAIN_PD_OR_QTY,
						'SOITEMID_CODE'=>$row->SOITEMID_CODE,
						'ITEM_NAME'=>$row->ITEM_NAME,
						'ICODE'=>$row->ICODE,
						'SOID_REF'=>$row->SOID_REF,
						'SOITEMID_REF'=>$row->MAIN_ITEMID_REF,
						'ITEMID_REF'=>$row->ITEMID_REF,
						'MAIN_ITEMID_REF'=>$row->MAIN_ITEMID_REF,
						'SQID_REF'=>$row->SQID_REF,
						'SEID_REF'=>$row->SEID_REF,
						'BOM_QTY'=>$row->STD_BOM_QTY,
						'INPUT_PD_OR_QTY'=>$row->AFT_CHANGES_QTY,
						'CHANGES_PD_OR_QTY'=>$row->AFT_CHANGES_QTY,
						'PROID_REF'=>$row->PROID_REF,
					);
				}
						
			}

                
            $objvendorcode2 = DB::table('TBL_MST_SUBLEDGER')
                ->where('BELONGS_TO','=','Vendor')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('SGLID','=',$objMstResponse->VID_REF)    
                ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                ->first();

            $objSavedTNC =  DB::table('TBL_TRN_JWO_TNC')
            ->where('JWOID_REF','=',$id)
            ->select('*')
            ->get()->toArray();

            $objSOTNC = DB::table('TBL_TRN_JWO_TNC')
            ->where('JWOID_REF','=',$id)
            ->select('*')
            ->get()->toArray();
            $objCount2 = count($objSOTNC);

            $objSavedTNCHeader=[];
            $objSavedTNCHeaderDTL=[];

            if(!empty($objSavedTNC)){
                $objSavedTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC WHERE  TNCID = ?', [$objSavedTNC[0]->TNCID_REF ]);
                $objSavedTNCHeaderDTL = DB::select('SELECT * FROM TBL_MST_TNC_DETAILS  WHERE  TNCID_REF = ?', [$objSavedTNC[0]->TNCDID_REF ]);
            }
            
               
            $objSavedCalT =  DB::table('TBL_TRN_JWO_CAL')
                            ->where('JWOID_REF','=',$id)
                            ->select('*')
                            ->get()->toArray();

            $objVQCAL =  DB::table('TBL_TRN_JWO_CAL')
                ->where('JWOID_REF','=',$id)
                ->select('*')
                ->get()->toArray();
            $objCount4 = count($objVQCAL);

            $TAXSTATE = [];
            $objSavedCalTHeader = [];
            $objSavedCalTHeaderDTL = [];

            if(!empty($objSavedCalT)){

                $objSavedCalTHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
                            WHERE  CTID = ?', [$objSavedCalT[0]->CTID_REF ]);
            }
             
            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC 
            FROM TBL_MST_TNC  
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


            $ObjUnionUDF    =   DB::table("TBL_MST_UDFFOR_JWO")->select('*')
                                ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF){       
                                    $query->select('UDFJWOID')->from('TBL_MST_UDFFOR_JWO')
                                    ->where('STATUS','=','A')
                                    ->where('PARENTID','=',0)
                                    ->where('DEACTIVATED','=',0)
                                    ->where('CYID_REF','=',$CYID_REF);                                  
                                    }
                                )
                                ->where('DEACTIVATED','=',0)
                                ->where('STATUS','<>','C')                    
                                ->where('CYID_REF','=',$CYID_REF);
                                    
            $objUdf         =   DB::table('TBL_MST_UDFFOR_JWO')
                                ->where('STATUS','=','A')
                                ->where('PARENTID','=',0)
                                ->where('DEACTIVATED','=',0)
                                ->where('CYID_REF','=',$CYID_REF)
                                ->union($ObjUnionUDF)
                                ->get()->toArray();  

            $objCountUDF    =   count($objUdf);
        
        
            $objtempUdf     =   $objUdf;
            foreach ($objtempUdf as $index => $udfvalue) {

                $objSavedUDF =  DB::table('TBL_TRN_JWO_UDF')
                                ->where('JWOID_REF','=',$id)
                                ->where('UDFJWOID_REF','=',$udfvalue->UDFJWOID)
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
        
            $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
            WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? 
            order by CTCODE ASC', [$CYID_REF, $BRID_REF, $FYID_REF ]);

            $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')->get() ->toArray(); 

            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
            WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by TNC_CODE ASC', [$CYID_REF, $BRID_REF, $FYID_REF, 'A' ]);

            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')->get() ->toArray(); 


            $FormId  = $this->form_id;
            $AlpsStatus =   $this->AlpsStatus();

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view($this->view.'edit', compact([
                'AlpsStatus','FormId','objRights','objMAT','objCalculationHeader','objTNCHeader','objUdf','objCountUDF',
                'objMstResponse','objvendorcode2','objSavedTNCHeader','objSavedTNCHeaderDTL','objVQCAL','objCount4',
                'objCalHeader','objCalDetails','TAXSTATE','objSOTNC','objTNCDetails','objCount2','material_array','TabSetting'
                ]));     
        }
    
    }

    public function view($id){
     
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

            $objMstResponse = DB::table('TBL_TRN_JWO_HDR')
                ->where('FYID_REF','=',Session::get('FYID_REF'))
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('JWOID','=',$id)
                ->select('*')
                ->first();

            $objMAT = DB::select("SELECT T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,
            T3.UOMCODE,T3.DESCRIPTIONS,
            T5.PRO_NO
            FROM TBL_TRN_JWO_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            LEFT JOIN TBL_TRN_PDPRO_HDR T5 ON T1.PROID_REF=T5.PROID
            WHERE T1.JWOID_REF='$id' ORDER BY T1.JWOID_REF ASC"); 

            $objREQ = DB::select("SELECT T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,
            T3.ICODE AS SOITEMID_CODE
            FROM TBL_TRN_JWO_REQ T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_ITEM T3 ON T1.MAIN_ITEMID_REF=T3.ITEMID
            WHERE T1.JWOID_REF='$id' ORDER BY T1.JWOID_REF ASC");
			
			$material_array=array();

			if(isset($objREQ) && !empty($objREQ)){
				foreach($objREQ as $row){

					$ITEMID		=	$row->MAIN_ITEMID_REF;
					
					$BOM_HDR =   DB::table('TBL_MST_BOM_HDR')
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF)
                        ->where('ITEMID_REF','=',$ITEMID)
                        ->where('STATUS','=','A')
                        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                        ->select('BOMID')
                        ->first();
						
					$MAIN_PD_OR_QTY	=($row->AFT_CHANGES_QTY/$row->STD_BOM_QTY);

                    $material_array[]=array(
						'BOMID_REF'=>isset($BOM_HDR) ? $BOM_HDR->BOMID : '',
						'MAIN_PD_OR_QTY'=>$MAIN_PD_OR_QTY,
						'SOITEMID_CODE'=>$row->SOITEMID_CODE,
						'ITEM_NAME'=>$row->ITEM_NAME,
						'ICODE'=>$row->ICODE,
						'SOID_REF'=>$row->SOID_REF,
						'SOITEMID_REF'=>$row->MAIN_ITEMID_REF,
						'ITEMID_REF'=>$row->ITEMID_REF,
						'MAIN_ITEMID_REF'=>$row->MAIN_ITEMID_REF,
						'SQID_REF'=>$row->SQID_REF,
						'SEID_REF'=>$row->SEID_REF,
						'BOM_QTY'=>$row->STD_BOM_QTY,
						'INPUT_PD_OR_QTY'=>$row->AFT_CHANGES_QTY,
						'CHANGES_PD_OR_QTY'=>$row->AFT_CHANGES_QTY,
						'PROID_REF'=>$row->PROID_REF,
					);
				}
						
			}

                
            $objvendorcode2 = DB::table('TBL_MST_SUBLEDGER')
                ->where('BELONGS_TO','=','Vendor')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('SGLID','=',$objMstResponse->VID_REF)    
                ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                ->first();

            $objSavedTNC =  DB::table('TBL_TRN_JWO_TNC')
            ->where('JWOID_REF','=',$id)
            ->select('*')
            ->get()->toArray();

            $objSOTNC = DB::table('TBL_TRN_JWO_TNC')
            ->where('JWOID_REF','=',$id)
            ->select('*')
            ->get()->toArray();
            $objCount2 = count($objSOTNC);

            $objSavedTNCHeader=[];
            $objSavedTNCHeaderDTL=[];

            if(!empty($objSavedTNC)){
                $objSavedTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC WHERE  TNCID = ?', [$objSavedTNC[0]->TNCID_REF ]);
                $objSavedTNCHeaderDTL = DB::select('SELECT * FROM TBL_MST_TNC_DETAILS  WHERE  TNCID_REF = ?', [$objSavedTNC[0]->TNCDID_REF ]);
            }
            
               
            $objSavedCalT =  DB::table('TBL_TRN_JWO_CAL')
                            ->where('JWOID_REF','=',$id)
                            ->select('*')
                            ->get()->toArray();

            $objVQCAL =  DB::table('TBL_TRN_JWO_CAL')
                ->where('JWOID_REF','=',$id)
                ->select('*')
                ->get()->toArray();
            $objCount4 = count($objVQCAL);

            $TAXSTATE = [];
            $objSavedCalTHeader = [];
            $objSavedCalTHeaderDTL = [];

            if(!empty($objSavedCalT)){

                $objSavedCalTHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
                            WHERE  CTID = ?', [$objSavedCalT[0]->CTID_REF ]);
            }
             
            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC 
            FROM TBL_MST_TNC  
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


            $ObjUnionUDF    =   DB::table("TBL_MST_UDFFOR_JWO")->select('*')
                                ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF){       
                                    $query->select('UDFJWOID')->from('TBL_MST_UDFFOR_JWO')
                                    ->where('STATUS','=','A')
                                    ->where('PARENTID','=',0)
                                    ->where('DEACTIVATED','=',0)
                                    ->where('CYID_REF','=',$CYID_REF);                                  
                                    }
                                )
                                ->where('DEACTIVATED','=',0)
                                ->where('STATUS','<>','C')                    
                                ->where('CYID_REF','=',$CYID_REF);
                                    
            $objUdf         =   DB::table('TBL_MST_UDFFOR_JWO')
                                ->where('STATUS','=','A')
                                ->where('PARENTID','=',0)
                                ->where('DEACTIVATED','=',0)
                                ->where('CYID_REF','=',$CYID_REF)
                                ->union($ObjUnionUDF)
                                ->get()->toArray();  

            $objCountUDF    =   count($objUdf);
        
        
            $objtempUdf     =   $objUdf;
            foreach ($objtempUdf as $index => $udfvalue) {

                $objSavedUDF =  DB::table('TBL_TRN_JWO_UDF')
                                ->where('JWOID_REF','=',$id)
                                ->where('UDFJWOID_REF','=',$udfvalue->UDFJWOID)
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
        
            $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
            WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? 
            order by CTCODE ASC', [$CYID_REF, $BRID_REF, $FYID_REF ]);

            $objCalDetails = DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')->get() ->toArray(); 

            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
            WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by TNC_CODE ASC', [$CYID_REF, $BRID_REF, $FYID_REF, 'A' ]);

            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')->get() ->toArray(); 


            $FormId  = $this->form_id;
            $AlpsStatus =   $this->AlpsStatus();

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view($this->view.'view', compact([
                'AlpsStatus','FormId','objRights','objMAT','objCalculationHeader','objTNCHeader','objUdf','objCountUDF',
                'objMstResponse','objvendorcode2','objSavedTNCHeader','objSavedTNCHeaderDTL','objVQCAL','objCount4',
                'objCalHeader','objCalDetails','TAXSTATE','objSOTNC','objTNCDetails','objCount2','material_array','TabSetting'
                ]));     
        }
      
    }
     
    public function update(Request $request){

        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];
        $r_count5 = $request['Row_Count5'];
        
        $req_data=array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $req_data[$i] = [
                    'PROID_REF'      => $request['PROID_REF_'.$i],
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'     => $request['MAIN_UOMID_REF_'.$i],
                    'PROD_QTY'         => $request['QTY_'.$i],
                    'JWO_QTY'     => $request['PD_OR_QTY_'.$i],
                    'EDA'     => $request['EDA_'.$i],
                    'RATE_PUOM'     => $request['RATEPUOM_'.$i],
                    'SOID_REF'      => $request['SOID_REF_'.$i],
                    'SQID_REF'     => $request['SQID_REF_'.$i],
                    'SEID_REF'     => $request['SEID_REF_'.$i]   
                ];

            }
        }

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);


        $req_data5=array();
        for ($i=0; $i<=$r_count5; $i++){
            if(isset($request['REQ_SOITEMID_REF_'.$i])){

                $req_data5[$i] = [
                    'ITEMID_REF'    	=> $request['REQ_ITEMID_REF_'.$i],
                    'STD_BOM_QTY'     		=> $request['REQ_BOM_QTY_'.$i],
                    'AFT_CHANGES_QTY'	=> $request['REQ_CHANGES_PD_OR_QTY_'.$i],
                    'MAIN_ITEMID_REF'   => $request['REQ_SOITEMID_REF_'.$i],
                    'PROID_REF'      	=> $request['REQ_PROID_REF_'.$i],
                    'SOID_REF'      	=> $request['REQ_SOID_REF_'.$i],
                    'SQID_REF'     		=> $request['REQ_SQID_REF_'.$i],  
                    'SEID_REF'     		=> $request['REQ_SEID_REF_'.$i]    
                ];

            }
        }

        
		if($r_count5 > 0){
            $wrapped_links5["REQ"] = $req_data5; 
			$XMLREQ = ArrayToXml::convert($wrapped_links5);
        }
        else{
            $XMLREQ=NULL;
        }		


        
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
        
       
        $udffield_Data = [];      
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFJWOID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           }
            
        }

        if(count($udffield_Data)>0){
            $udffield_wrapped["UDF"] = $udffield_Data;  
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

        $JWONO = $request['JWONO'];
        $JWODT = $request['JWODT'];
        $VID_REF = $request['VID_REF'];
        $DIRECTJWO = $request['hiddenDirect'];
        
        $log_data = [ 
            $JWONO,     $JWODT,     $VID_REF,       $CYID_REF,      $BRID_REF,      
            $FYID_REF,  $VTID_REF,  $XMLMAT,        $XMLREQ,        $XMLTNC,
            $XMLCAL,    $XMLUDF,    $USERID_REF,    Date('Y-m-d'),  Date('h:i:s.u'),    
            $ACTIONNAME,$IPADDRESS, $DIRECTJWO
        ];

        $sp_result = DB::select('EXEC SP_JWO_UP ?,?,?,?,? ,?,?,?,?,? ,?,?,?,?,? ,?,?,?', $log_data);       
        
    
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
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
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];
        $r_count5 = $request['Row_Count5'];
        
        $req_data=array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $req_data[$i] = [
                    'PROID_REF'      => $request['PROID_REF_'.$i],
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'     => $request['MAIN_UOMID_REF_'.$i],
                    'PROD_QTY'         => $request['QTY_'.$i],
                    'JWO_QTY'     => $request['PD_OR_QTY_'.$i],
                    'EDA'     => $request['EDA_'.$i],
                    'RATE_PUOM'     => $request['RATEPUOM_'.$i],
                    'SOID_REF'      => $request['SOID_REF_'.$i],
                    'SQID_REF'     => $request['SQID_REF_'.$i],
                    'SEID_REF'     => $request['SEID_REF_'.$i]   
                ];

            }
        }

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);


        $req_data5=array();
        for ($i=0; $i<=$r_count5; $i++){
            if(isset($request['REQ_SOITEMID_REF_'.$i])){

                $req_data5[$i] = [
                    'ITEMID_REF'    	=> $request['REQ_ITEMID_REF_'.$i],
                    'STD_BOM_QTY'     		=> $request['REQ_BOM_QTY_'.$i],
                    'AFT_CHANGES_QTY'	=> $request['REQ_CHANGES_PD_OR_QTY_'.$i],
                    'MAIN_ITEMID_REF'   => $request['REQ_SOITEMID_REF_'.$i],
                    'PROID_REF'      	=> $request['REQ_PROID_REF_'.$i],
                    'SOID_REF'      	=> $request['REQ_SOID_REF_'.$i],
                    'SQID_REF'     		=> $request['REQ_SQID_REF_'.$i],  
                    'SEID_REF'     		=> $request['REQ_SEID_REF_'.$i]    
                ];

            }
        }

        
		if($r_count5 > 0){
            $wrapped_links5["REQ"] = $req_data5; 
			$XMLREQ = ArrayToXml::convert($wrapped_links5);
        }
        else{
            $XMLREQ=NULL;
        }		


        
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
        
       
        $udffield_Data = [];      
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFJWOID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           }
            
        }

        if(count($udffield_Data)>0){
            $udffield_wrapped["UDF"] = $udffield_Data;  
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

        $JWONO = $request['JWONO'];
        $JWODT = $request['JWODT'];
        $VID_REF = $request['VID_REF'];
        $DIRECTJWO = $request['hiddenDirect'];
        
        $log_data = [ 
            $JWONO,     $JWODT,     $VID_REF,       $CYID_REF,      $BRID_REF,      
            $FYID_REF,  $VTID_REF,  $XMLMAT,        $XMLREQ,        $XMLTNC,
            $XMLCAL,    $XMLUDF,    $USERID_REF,    Date('Y-m-d'),  Date('h:i:s.u'),    
            $ACTIONNAME,$IPADDRESS, $DIRECTJWO
        ];

        $sp_result = DB::select('EXEC SP_JWO_UP ?,?,?,?,? ,?,?,?,?,? ,?,?,?,?,? ,?,?,?', $log_data);      
        
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully Approved.']);

        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
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
                $TABLE      =   "TBL_TRN_JWO_HDR";
                $FIELD      =   "JWOID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
            
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_JWO ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
    
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
        $TABLE      =   "TBL_TRN_JWO_HDR";
        $FIELD      =   "JWOID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_JWO_MAT',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_JWO_REQ',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_JWO_TNC',
        ];
        $req_data[3]=[
            'NT'  => 'TBL_TRN_JWO_CAL',
        ];
        $req_data[4]=[
            'NT'  => 'TBL_TRN_JWO_UDF',
        ];
       
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_JWO  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_JWO_HDR')->where('JWOID','=',$id)->first();

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

            return view($this->view.'attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments']));
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
        
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/JobWorkOrder";
		
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
  
    
    public function checkExist(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $JWONO      =   $request->JWONO;
        
        $objExit    =   DB::table('TBL_TRN_JWO_HDR')
                        ->where('TBL_TRN_JWO_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_TRN_JWO_HDR.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_TRN_JWO_HDR.FYID_REF','=',Session::get('FYID_REF'))
                        ->where('TBL_TRN_JWO_HDR.JWONO','=',$JWONO)
                        ->select('TBL_TRN_JWO_HDR.JWOID')
                        ->first();
        
        if($objExit){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate JWO NO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }

    public function getItemDetails(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $Status     =   $request['status'];
        $VID_REF    =   $request['VID_REF'];
        $PROID_REF  =   $request['PROID_REF'];
        $StdCost    =   0;

        $AlpsStatus =   $this->AlpsStatus();
        if ($PROID_REF == "")
        {
            $ObjItem =  DB::select("SELECT 
            T2.MAIN_UOMID_REF AS MAIN_UOMID_REF, 1 AS Qty,0 AS PROID_REF,0 AS SOID_REF,0 AS SEID_REF,0 AS SQID_REF,
            T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI
            FROM TBL_MST_ITEM T2 
            WHERE T2.STATUS='$Status'");

        }
        else
        {
            $ObjItem =  DB::select("SELECT 
            T1.UOMID_REF AS MAIN_UOMID_REF,T1.PD_OR_QTY AS Qty,T1.PROID_REF,T1.SOID_REF,T1.SEID_REF,T1.SQID_REF,
            T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI
            FROM TBL_TRN_PDPRO_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            WHERE T1.PROID_REF='$PROID_REF'
            
            UNION
            
            SELECT 
            T2.MAIN_UOMID_REF AS MAIN_UOMID_REF,T1.CHANGES_PD_OR_QTY AS Qty,T1.PROID_REF,T1.SOID_REF,T1.SEID_REF,T1.SQID_REF,
            T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI
            FROM TBL_TRN_PDPRO_REQ T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            WHERE T1.PROID_REF='$PROID_REF'");
        }

        if(!empty($ObjItem)){

            foreach ($ObjItem as $index=>$dataRow){

                $SOQTY      =   isset($dataRow->Qty)? $dataRow->Qty : 0;   
                $FROMQTY    =   isset($dataRow->Qty)? $dataRow->Qty : 0;   
                     
                $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS 
                FROM TBL_MST_UOM  
                WHERE  CYID_REF = ?  AND UOMID = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                [$CYID_REF, $dataRow->MAIN_UOMID_REF, 'A' ]);          
                
                $ObjItemGroup =  DB::select('SELECT TOP 1 GROUPCODE, GROUPNAME 
                FROM TBL_MST_ITEMGROUP  
                WHERE  CYID_REF = ?  AND ITEMGID = ?
                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                [$CYID_REF, $dataRow->ITEMGID_REF, 'A' ]);

                $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS 
                FROM TBL_MST_ITEMCATEGORY  
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


                $item_unique_row_id  =   $PROID_REF."_".$dataRow->SOID_REF."_".$dataRow->SQID_REF."_".$dataRow->SEID_REF."_".$dataRow->ITEMID;
               
               

                $row = '';

                $row = $row.'
                <tr id="item_'.$index.'"  class="clsitemid">
                    <td style="width:8%;text-align:center;"><input type="checkbox" id="chkId'.$index.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>
                    <td style="width:10%;">'.$dataRow->ICODE.'</td>
                    <td style="width:10%;">'.$dataRow->NAME.'</td>
                    <td style="width:8%;">'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>
                    <td style="width:8%;">'.$FROMQTY.'</td>
                    <td style="width:8%;">'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>
                    <td style="width:8%;">'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                    <td style="width:8%;">'.$BusinessUnit.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                    <td style="width:8%;">Authorized</td>
                    <td hidden>
                        <input type="text" id="txtitem_'.$index.'" 
                            data-desc1="'.$dataRow->ITEMID.'" 
                            data-desc2="'.$dataRow->ICODE.'" 
                            data-desc3="'.$dataRow->NAME.'" 
                            data-desc4="'.$dataRow->MAIN_UOMID_REF.'" 
                            data-desc5="'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'" 
                            data-desc6="'.$FROMQTY.'" 
                            data-desc7="'.$item_unique_row_id.'" 
                            data-desc8="'.$dataRow->SQID_REF.'"
                            data-desc9="'.$dataRow->SEID_REF.'"
                            data-desc10="'.$PROID_REF.'"
                            data-desc11="'.$dataRow->SOID_REF.'"
                            data-desc12="'.$SOQTY.'"
                        />
                    </td>
                </tr>';
                echo $row;    
            }         
        }           
        else{
            echo '<tr><td> Record not found.</td></tr>';
        }
        exit();
    }
        
    public function getProNo(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $VTID_REF       =   $request['id'];
        $fieldid        =   $request['fieldid'];

        $ObjData        =   DB::select("SELECT PROID,PRO_NO,PRO_DT 
                            FROM TBL_TRN_PDPRO_HDR 
                            WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF' 
                            AND STATUS='A'"); 

        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){

                $row            =   '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="socode_'.$dataRow->PROID .'"  class="clssPROid" value="'.$dataRow->PROID.'" ></td>
                <td class="ROW2">'.$dataRow->PRO_NO;
                $row = $row.'<input type="hidden" id="txtsocode_'.$dataRow->PROID.'" data-desc="'.$dataRow->PRO_NO.'"  value="'.$dataRow->PROID.'"/></td>
                <td class="ROW3" >'.$dataRow->PRO_DT.'</td></tr>';
                echo $row;
                
            }

        }else{
            echo '<tr><td>Record not found.</td></tr>';
        }
        exit();   
    }
    
    public function get_materital_item(Request $request){
      //  dd($request->all());
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $item_array     =   $request['item_array'];
        $DIRECT         =   $request['hiddenDirect'];
        $material_array=array();
        foreach($item_array as $key=>$val){

            $exp        =   explode("_",$val);
            $PROID      =   $exp[0];
            $SOID       =   $exp[1];
            $ITEMID     =   $exp[2];
            $ITEMCODE   =   $exp[3];
            $PD_OR_QTY  =   $exp[4];
            $SQID       =   $exp[5];
            $SEID       =   $exp[6];
            $DIRECT     =   $exp[7];

            

           
            $BOM_HDR =   DB::table('TBL_MST_BOM_HDR')
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF)
                        ->where('ITEMID_REF','=',$ITEMID)
                        ->where('STATUS','=','A')
                        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                        ->select('BOMID')
                        ->first();

                        
            
            if(isset($BOM_HDR) && !empty($BOM_HDR)){

                $mitem_id = $PROID."_".$SOID."_".$SQID."_".$SEID."_".$ITEMID;

                $BOMID  =   $BOM_HDR->BOMID;

                $BOM_MAT    =   DB::select("SELECT 
                                T1.BOM_MATID,T1.BOMID_REF,T1.ITEMID_REF,T1.CONSUME_QTY,
                                T2.ICODE,T2.NAME
                                FROM TBL_MST_BOM_MAT T1 
                                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                                WHERE T1.BOMID_REF='$BOMID'");
                                

                if(isset($BOM_MAT) && !empty($BOM_MAT)){
                    foreach($BOM_MAT as $row){

                        $material_array[]=array(
                            'BOM_MATID'=>$row->BOM_MATID,
                            'BOMID_REF'=>$row->BOMID_REF,
                            'ITEMID_REF'=>$row->ITEMID_REF,
                            'CONSUME_QTY'=>$row->CONSUME_QTY,
                            'ICODE'=>$row->ICODE,
                            'NAME'=>$row->NAME,
                            'MAIN_PROID'=>$PROID,
                            'MAIN_SOID'=>$SOID,
                            'MAIN_ITEMID'=>$ITEMID,
                            'MAIN_ITEMCODE'=>$ITEMCODE,
                            'MAIN_PD_OR_QTY'=>$PD_OR_QTY,
                            'MAIN_SQID'=>$SQID,
                            'MAIN_SEID'=>$SEID,
                            'MAIN_ITEM_ROWID'=>$mitem_id ,
                        );
                    }
                }

            }
        }
        // dd(Str::contains($DIRECT, '1'));
        if(Str::contains($DIRECT, '1') == 'true')
        {
            echo'<table id="example8" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                        <tr>
                            <th hidden ><input class="form-control" type="hidden" name="Row_Count5" id ="Row_Count5" value="1"></th>
                            <th>Main Item</th>
                            <th>Item Code</th>
                            <th>Item Description</th>
                            <th hidden>MAIN_PD_OR_QTY</th>
                            <th>Standard BOM Qty</th>
                            <th>Input Item as per Job Work Order Qty</th>
                            <th>Input Item after changes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody> <tr  class="participantRow8"> <td><input type="text" id="txtMAINITEM_popup_0"  value=""  class="form-control" readonly style="width:100px;" /></td>
                    <td><input type="text" id="txtSUBITEM_popup_0"  value=""          class="form-control" readonly style="width:100px;" /></td>
                    <td><input type="text" id="SUBITEM_NAME_0"      value=""           class="form-control" readonly style="width:200px;" /></td>
                    <td hidden><input type="text" name="MAIN_PD_OR_QTY_0"      id="MAIN_PD_OR_QTY_0"      value="" /></td>
                    <td hidden><input type="hidden" name="REQ_BOMID_REF_0"       id="REQ_BOMID_REF_0"       value="" /></td>
                    <td hidden><input type="hidden" name="REQ_PROID_REF_0"        id="REQ_PROID_REF_0"        value="" /></td>
                    <td hidden><input type="hidden" name="REQ_SOID_REF_0"        id="REQ_SOID_REF_0"        value="" /></td>
                    <td hidden><input type="hidden" name="REQ_SOITEMID_REF_0"    id="REQ_SOITEMID_REF_0"    value="" /></td>
                    <td hidden><input type="text" name="REQ_ITEMID_REF_0"      id="REQ_ITEMID_REF_0"      value="" /></td>
                    <td hidden><input type="text" name="REQ_MAIN_ITEMID_REF_0" id="REQ_MAIN_ITEMID_REF_0"  /></td>
                    <td hidden><input type="hidden" name="REQ_SQID_REF_0"         id="REQ_SQID_REF_0"         value="" /></td>
                    <td hidden><input type="hidden" name="REQ_SEID_REF_0"        id="REQ_SEID_REF_0"        value="" /></td>
                    <td><input    type="text" name="REQ_BOM_QTY_0"           id="REQ_BOM_QTY_0"             value=""    class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="change_production_qty(this.id,this.value)" readonly  /></td>
                    <td><input    type="text" name="REQ_INPUT_PD_OR_QTY_0"   id="REQ_INPUT_PD_OR_QTY_0"     value=""             class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="change_production_qty(this.id,this.value)" readonly  /></td>
                    <td><input    type="text" name="REQ_CHANGES_PD_OR_QTY_0" id="REQ_CHANGES_PD_OR_QTY_0"   value=""             class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="change_production_qty(this.id,this.value)"  /></td>
                    <td hidden><input id="main_item_rowid_0" value=""  /></td><td align="center" >
                    <button class="btn suba" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                    <button class="btn subr" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                    </td>
                    </tr> </tbody> </table>';

        }

       else
       {

        if(!empty($material_array)){
            $Row_Count5 =   count($material_array);
            echo'<table id="example8" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                        <tr>
                            <th hidden ><input class="form-control" type="hidden" name="Row_Count5" id ="Row_Count5" value="'.$Row_Count5.'"></th>
                            <th>Main Item</th>
                            <th>Item Code</th>
                            <th>Item Description</th>
                            <th hidden>MAIN_PD_OR_QTY</th>
                            <th>Standard BOM Qty</th>
                            <th>Input Item as per Job Work Order Qty</th>
                            <th>Input Item after changes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>';

                    foreach($material_array as $index=>$row_data){

                        $prod_order_qty     = number_format(round(($row_data['CONSUME_QTY']*$row_data['MAIN_PD_OR_QTY']), 3),3,".","")  ;

                        echo '<tr  class="participantRow8">';
                        echo '<td><input type="text"  id="txtMAINITEM_popup_'.$index.'"  value="'.$row_data['MAIN_ITEMCODE'].'"  class="form-control" readonly style="width:100px;" /></td>';
                        echo '<td><input type="text" id="txtSUBITEM_popup_'.$index.'"  value="'.$row_data['ICODE'].'"          class="form-control" readonly style="width:100px;" /></td>';
                        echo '<td><input type="text" id="SUBITEM_NAME_'.$index.'"      value="'.$row_data['NAME'].'"           class="form-control" readonly style="width:200px;" /></td>';

                        echo '<td hidden><input type="text" name="MAIN_PD_OR_QTY_'.$index.'"      id="MAIN_PD_OR_QTY_'.$index.'"      value="'.$row_data['MAIN_PD_OR_QTY'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_BOMID_REF_'.$index.'"       id="REQ_BOMID_REF_'.$index.'"       value="'.$row_data['BOMID_REF'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_PROID_REF_'.$index.'"        id="REQ_PROID_REF_'.$index.'"        value="'.$row_data['MAIN_PROID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SOID_REF_'.$index.'"        id="REQ_SOID_REF_'.$index.'"        value="'.$row_data['MAIN_SOID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SOITEMID_REF_'.$index.'"    id="REQ_SOITEMID_REF_'.$index.'"    value="'.$row_data['MAIN_ITEMID'].'" /></td>';
                        echo '<td hidden><input type="text" name="REQ_ITEMID_REF_'.$index.'"      id="REQ_ITEMID_REF_'.$index.'"      value="'.$row_data['ITEMID_REF'].'" /></td>';
                        echo '<td hidden><input type="text" name="REQ_MAIN_ITEMID_REF_'.$index.'" id="REQ_MAIN_ITEMID_REF_'.$index.'"  /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SQID_REF_'.$index.'"         id="REQ_SQID_REF_'.$index.'"         value="'.$row_data['MAIN_SQID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SEID_REF_'.$index.'"        id="REQ_SEID_REF_'.$index.'"        value="'.$row_data['MAIN_SEID'].'" /></td>';
                       
                        echo '<td><input    type="text" name="REQ_BOM_QTY_'.$index.'"           id="REQ_BOM_QTY_'.$index.'"             value="'.number_format($row_data['CONSUME_QTY'],3,".","").'"    class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="change_production_qty(this.id,this.value)" readonly  /></td>';
                        echo '<td><input    type="text" name="REQ_INPUT_PD_OR_QTY_'.$index.'"   id="REQ_INPUT_PD_OR_QTY_'.$index.'"     value="'.$prod_order_qty.'"             class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="change_production_qty(this.id,this.value)" readonly  /></td>';
                        echo '<td><input    type="text" name="REQ_CHANGES_PD_OR_QTY_'.$index.'" id="REQ_CHANGES_PD_OR_QTY_'.$index.'"   value="'.$prod_order_qty.'"             class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="change_production_qty(this.id,this.value)"  /></td>';
                        echo '<td hidden><input id="main_item_rowid_'.$index.'" value="'.$row_data['MAIN_ITEM_ROWID'].'"  /></td>
                        <td align="center" >
                        <button class="btn suba" title="add" data-toggle="tooltip" type="button" disabled ><i class="fa fa-plus"></i></button>
                        <button class="btn subr" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button>
                        </td>';
                        echo '</tr>';
                    }
                    
            echo '</tbody>';
            echo'</table>';
            }
            else{
                echo "Record not found.";
            }
        }
        
        exit();
    }

    public function get_main_item(Request $request){
        //  dd($request->all());
          $CYID_REF       =   Auth::user()->CYID_REF;
          $BRID_REF       =   Session::get('BRID_REF');
          $FYID_REF       =   Session::get('FYID_REF');
          $item_array     =   $request['item_array'];
          $material_array=array();
          foreach($item_array as $key=>$val){
  
              $exp        =   explode("_",$val);
              $PROID      =   $exp[0];
              $SOID       =   $exp[1];
              $ITEMID     =   $exp[2];
              $ITEMCODE   =   $exp[3];
              $PD_OR_QTY  =   $exp[4];
              $SQID       =   $exp[5];
              $SEID       =   $exp[6];
              $DIRECT     =   $exp[7];
  
              
  
             
              $ObjItem =   DB::table('TBL_MST_ITEM')
                          ->where('CYID_REF','=',$CYID_REF)
                          ->where('ITEMID','=',$ITEMID)
                          ->where('STATUS','=','A')
                          ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                          ->select('ITEMID')
                          ->first();
  
                          
              
              if(isset($ObjItem) && !empty($ObjItem)){
  
                  $ITEM    =   DB::select("SELECT 
                                  T2.ITEMID,T2.ICODE,T2.NAME
                                  FROM TBL_MST_ITEM T2
                                  WHERE T2.ITEMID='$ITEMID'");
                                  
  
                  if(isset($ITEM) && !empty($ITEM)){
                      foreach($ITEM as $row){
  
                          $material_array[]=array(
                              'ITEMID'=>$row->ITEMID,
                              'ICODE'=>$row->ICODE,
                              'NAME'=>$row->NAME,
                              'MAIN_PD_OR_QTY'=>$PD_OR_QTY,
                          );
                      }
                  }
                //  dd($material_array);
                  if(!empty($material_array))
                  {
                    foreach($material_array as $index=>$dataRow)
                    {
                        $row = '';
                        $row = $row.'<tr >
                        <td class="ROW1"> <input type="checkbox" name="SELECT_'.$index.'[]" id="maincode_'.$dataRow['ITEMID'].'"  class="clsMAINITEMid" value="'.$dataRow['ITEMID'].'" ></td>
                        <td class="ROW2">'.$dataRow['ICODE']; 
                        $row = $row.'<input type="hidden" id="txtmaincode_'.$dataRow['ITEMID'].'" data-desc1="'.$dataRow['ICODE'].'"
                        data-desc2="'.$dataRow['MAIN_PD_OR_QTY'].'" value="'.$dataRow['ITEMID'].'"/></td>
                        <td class="ROW3">'.$dataRow['NAME'].'</td></tr>';
                        echo $row;

                    }
                  }
  
              }
          }
        exit();
    }

    public function get_sub_item(Request $request){
        //  dd($request->all());
          $CYID_REF       =   Auth::user()->CYID_REF;
          $BRID_REF       =   Session::get('BRID_REF');
          $FYID_REF       =   Session::get('FYID_REF');
          $ITEMID         =   $request['ITEMID'];
          $material_array=array();
  
                  $ITEM    =   DB::select("SELECT 
                                  T2.ITEMID,T2.ICODE,T2.NAME
                                  FROM TBL_MST_ITEM T2
                                  WHERE T2.ITEMID != '$ITEMID'");
                                  
  
                  if(isset($ITEM) && !empty($ITEM)){
                      foreach($ITEM as $row){
  
                          $material_array[]=array(
                              'ITEMID'=>$row->ITEMID,
                              'ICODE'=>$row->ICODE,
                              'NAME'=>$row->NAME,
                          );
                      }
                  }
                //  dd($material_array);
                  if(!empty($material_array))
                  {
                    foreach($material_array as $index=>$dataRow)
                    {
                        $row = '';
                        $row = $row.'<tr >
                        <td class="ROW1"> <input type="checkbox" name="SELECT_'.$index.'[]" id="subcode_'.$dataRow['ITEMID'].'"  class="clsSUBITEMid" value="'.$dataRow['ITEMID'].'" ></td>
                        <td class="ROW2">'.$dataRow['ICODE']; 
                        $row = $row.'<input type="hidden" id="txtsubcode_'.$dataRow['ITEMID'].'" data-desc1="'.$dataRow['ICODE'].'"
                        data-desc2="'.$dataRow['NAME'].'" value="'.$dataRow['ITEMID'].'"/></td>
                        <td class="ROW3">'.$dataRow['NAME'].'</td></tr>';
                        echo $row;

                    }
                  }
        exit();
    }

    public function getSUBITEMCodeNo(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $BOMID_REF      =   $request['REQ_BOMID_REF'];
        $MAINITEMID_REF =   $request['REQ_ITEMID'];
        $MAIN_PD_OR_QTY =   $request['MAIN_PD_OR_QTY'];
        $fieldid        =   $request['fieldid'];

        $ObjData        =   DB::select("SELECT T1.BOM_SUBID,T1.BOMID_REF,T1.SUBITEMID_REF,T1.CONSUME_QTY, T2.ICODE,T2.NAME
                            FROM TBL_MST_BOM_SUB T1
                            LEFT JOIN TBL_MST_ITEM T2 ON T1.SUBITEMID_REF=T2.ITEMID
                            WHERE BOMID_REF='$BOMID_REF' AND MAINITEMID_REF='$MAINITEMID_REF' ");
        
        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){

                $prod_order_qty     =   round(($dataRow->CONSUME_QTY*$MAIN_PD_OR_QTY), 3);
                
                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="subcode_'.$dataRow->BOM_SUBID .'"  class="clssSUBITEMid" value="'.$dataRow->BOM_SUBID.'" ></td>
                <td class="ROW2">'.$dataRow->ICODE; 
                $row = $row.'<input type="hidden" id="txtsubcode_'.$dataRow->BOM_SUBID.'" 

                data-desc1="'.$dataRow->ICODE.'"
                data-desc2="'.$dataRow->NAME.'"
                data-desc3="'.$dataRow->SUBITEMID_REF.'"
                data-desc4="'.$MAINITEMID_REF.'"
                data-desc5="'.$dataRow->CONSUME_QTY.'"
                data-desc6="'.$prod_order_qty.'"
               
                
                value="'.$dataRow->BOM_SUBID.'"/>
                
                </td>
                
                <td class="ROW3">'.$dataRow->NAME.'</td></tr>';
                echo $row;
                
            }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }

        exit();
        
    } 
    
    

    
}
