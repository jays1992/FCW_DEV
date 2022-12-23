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

class TrnFrm482Controller extends Controller{
    protected $form_id  =   482;
    protected $vtid_ref =   552;
    protected $view     =   "transactions.inventory.StockTransfer.trnfrm";
   
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){ 

        $FormId         =   $this->form_id;
        $USERID   	    =   Auth::user()->USERID;
        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');     
        
        $objRights  =   DB::table('TBL_MST_USERROLMAP')
			->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
			->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
			->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
			->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
			->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
			->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
			->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
			->first();
  
        $objFinalAppr   =   DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO           =   "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList	=	DB::select("select hdr.STID,hdr.STNO,hdr.STDT,hdr.NATURE,hdr.CUST_ADDRESS,hdr.GSTIN_NO,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.STID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                            ) AS CREATED_BY,
                            hdr.STATUS,
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
                            inner join TBL_TRN_STOCK_TRNF_HDR hdr
                            on a.VID = hdr.STID 
                            and a.VTID_REF = hdr.VTID_REF2 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            
                            where a.VTID_REF in(552,44,326)
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.STID DESC ");

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
        $Status         =   "A";
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       = Session::get('BRID_REF');
        $FYID_REF       = Session::get('FYID_REF');

        $objCalculationHeader	=   Helper::getCalculationHeader(array(
            'CYID_REF'=>Auth::user()->CYID_REF,
            'BRID_REF'=>Session::get('BRID_REF'),
            'USERID'=>Auth::user()->USERID,
            'HEADING'=>'Transactions',
            'VTID_REF'=>$this->vtid_ref,
            'FORMID'=>$this->form_id
            ));


        $objUdfSOData   =   [];
        $objCountUDF    =   count($objUdfSOData);
        $AlpsStatus     =   $this->AlpsStatus();
        $TabSetting	    =   Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        return view($this->view.$FormId.'add',compact(['AlpsStatus','FormId','objCalculationHeader','objUdfSOData','objCountUDF','TabSetting']));       
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
    
    public function getsubledger(Request $request){
        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $CODE       =   $request['CODE'];
        $NAME       =   $request['NAME'];
    
        $sp_popup   =   [$CYID_REF, $BRID_REF,$CODE,$NAME]; 

        if(isset($request['TRANSFER_TYPE']) && $request['TRANSFER_TYPE']==="IN"){
            $ObjData = DB::select('EXEC sp_get_vendor_popup_enquiry ?,?,?,?', $sp_popup);

            if(isset($ObjData) && !empty($ObjData)){
    
                foreach ($ObjData as $index=>$dataRow){

                    $row = '';
                    $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_SLID_REF[]" id="subgl_'.$index.'" class="clssubgl" value="'.$dataRow-> SGLID.'" ></td>';
                    $row = $row.'<td class="ROW2">'.$dataRow->SGLCODE;
                    $row = $row.'<input type="hidden" id="txtsubgl_'.$index.'" data-desc="'.$dataRow->SGLCODE .' - ';
                    $row = $row.$dataRow->SLNAME. '" data-desc2="'.$dataRow->GLID_REF. '"value="'.$dataRow->SGLID.'"/></td><td class="ROW3">'.$dataRow->SLNAME.'</td></tr>';
        
        
                    echo $row;
                }
    
            }
            else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }


        }
        else{
        
            $ObjData    =   DB::select('EXEC sp_get_customer_popup_enquiry ?,?,?,?', $sp_popup);
            
            if(isset($ObjData) && !empty($ObjData)){
    
                foreach ($ObjData as $index=>$dataRow){
                
                    $row = '';
                    $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_SLID_REF[]" id="subgl_'.$index.'" class="clssubgl" value="'.$dataRow-> SGLID.'" ></td>';
                    $row = $row.'<td class="ROW2">'.$dataRow->SGLCODE;
                    $row = $row.'<input type="hidden" id="txtsubgl_'.$index.'" data-desc="'.$dataRow->SGLCODE .' - ';
                    $row = $row.$dataRow->SLNAME. '" data-desc2="'.$dataRow->GLID_REF. '"value="'.$dataRow->SGLID.'"/></td><td class="ROW3">'.$dataRow->SLNAME.'</td></tr>';
        
        
                    echo $row;
                }
    
            }
            else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }

        }

        exit();
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
            INNER JOIN TBL_TRN_STOCK_TRNF_MAT T2 ON T1.ITEMID=T2.ITEMID_REF
            WHERE T1.CYID_REF = '$CYID_REF' 
            AND ( T1.DEACTIVATED IS NULL OR T1.DEACTIVATED = 0 ) AND T1.STATUS ='$Status' AND T2.STID_REF='$CodeNoId'");
        
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

                    $FROMQTY  =   isset($dataRow->ST_QTY)?$dataRow->ST_QTY:0;

                


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

                    $DIS_PER    =   0.0000;
                    $DIS_AMT    =   0.00000;
                    $PINO       =   '';
                    $RFQID_REF  =   '';
                    $MRSID_REF  =   '';
                    $TEMP_QTY   =   '0.000';
                    $RATE       =   $StdCost;


                    $CodeNoId   =   NULL;
                    $SEID_REF   =   NULL;
                    $SQID_REF   =   NULL;
                    $SOID       =   NULL;
                    $SCID_REF   =   NULL;
                   
                    $desc6      =   $CodeNoId.'-'.$dataRow->ITEMID.'-'.$SEID_REF.'-'.$SQID_REF.'-'.$SOID.'-'.$SCID_REF;

                     
                        $row = '';
                        if($taxstate != "OutofState"){
                            
                        $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                        $row = $row.'<input type="hidden" id="uniquerowid_'.$desc6.'"  data-desc6="'.$desc6.'" />';
                        $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'" data-desc1="'.$FROMQTY.'" data-desc2="'.$dataRow->RATEPUOM.'" data-desc3="'.$DIS_PER.'" data-desc4="'.$DIS_AMT.'" data-desc5="'.$CodeNoId.'-'.$dataRow->ITEMID.'" data-desc22="'.$ALPS_PART_NO.'" data-desc23="'.$CUSTOMER_PART_NO.'" data-desc24="'.$OEM_PART_NO.'"   value="'.$dataRow->ITEMID.'"/></td> <td style="width:10%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'" value="'.$dataRow->NAME.'"/></td>';
                        $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'" value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td style="width:8%;" id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'" value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                        $row = $row.'<td style="width:8%;" id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->RATEPUOM.'" value="'.$dataRow->RATEPUOM.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
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
                        
                
                    $DIS_PER    =   0.0000;
                    $DIS_AMT    =   0.00000;
                    $PINO       =   '';
                    $RFQID_REF  =   '';
                    $MRSID_REF  =   '';
                    $TEMP_QTY   =   '0.000';
                    $RATE       =   $STDCOST;
                    


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

    public function save(Request $request){

        $r_count1           =   $request['Row_Count1'];
        $r_count3           =   $request['Row_Count3'];
        $r_count4           =   $request['Row_Count4'];
        $TRANSFER_TYPE      =   $request['TRANSFER_TYPE'];
       
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
                        $storeid    =   NULL;

                        if($TRANSFER_TYPE =="IN"){
                            $storeid        =   $batchid;
                        }
                        else{
                            $objBatch =  DB::SELECT("SELECT BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF,SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) AS STOCK 
                            FROM TBL_MST_BATCH 
                            WHERE BATCHID='$batchid'
                            GROUP BY BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF 
                            HAVING SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) > '0.000'
                            ")[0];

                            $storeid        =   $objBatch->STID_REF;
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

                $req_data[$i] = [
                    'ITEMID_REF'    => isset($request['ITEMID_REF_'.$i]) && $request['ITEMID_REF_'.$i] !=""?$request['ITEMID_REF_'.$i]:'',
                    'ITEMDESC'    => isset($request['ItemName_'.$i]) && $request['ItemName_'.$i] !=""?$request['ItemName_'.$i]:'',
                    'MAIN_UOMID_REF' => isset($request['MAIN_UOMID_REF_'.$i]) && $request['MAIN_UOMID_REF_'.$i] !=""?$request['MAIN_UOMID_REF_'.$i]:'',
                    'STID' => isset($STID_REF) && $STID_REF !=""?$STID_REF:'',
                    'ST_QTY' => $request['SO_QTY_'.$i] !=""?$request['SO_QTY_'.$i]:0,
                    'RATEPUOM' => $request['RATEPUOM_'.$i] !=""?$request['RATEPUOM_'.$i]:0,
                    'GST' =>  (isset($request['flagtype_'.$i])!="true" ? 0 : 1) ,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'BATCH_QTY'   => $request['HiddenRowId_'.$i] !=""?$request['HiddenRowId_'.$i]:'',
                ];
            }
        }

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);

        $XMLUDF = NULL; 

        
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

                        $BATCHNO        =   NULL;
                        $SERIALNO       =   NULL;
                        $STID_REF       =   NULL;
                        $MAINUOMID_REF  =   NULL;
                        $STOCK_INHAND   =   NULL;

                        if($TRANSFER_TYPE =="IN"){
                            $BATCHNO        =   NULL;
                            $SERIALNO       =   NULL;
                            $STID_REF       =   $key;
                            $MAINUOMID_REF  =   NULL;
                            $STOCK_INHAND   =   NULL;
                        }
                        else{
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
                    
                        $req_data33[$i][] = [
                            'ITEMID_REF'    => isset($ITEMID_REF) && $ITEMID_REF !=""?$ITEMID_REF:'',
                            'MAINUOMID_REF'     => isset($MAINUOMID_REF) && $MAINUOMID_REF !=""?$MAINUOMID_REF:'',
                            'BATCHNO'      => isset($BATCHNO) && $BATCHNO !=""?$BATCHNO:'',
                            'SERIALNO'     => isset($SERIALNO) && $SERIALNO !=""?$SERIALNO:'',
                            'SOTCK'  => isset($STOCK_INHAND) && $STOCK_INHAND !=''?floatval($STOCK_INHAND):0,
                            'STORE_QTY'           => isset($val) && $val !=""?$val:0,
                            'STID'      => isset($STID_REF) && $STID_REF !=""?intval($STID_REF):'',

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

        
        $VTID_REF           =   $this->vtid_ref;
        $VTID_REF2          =   isset($request['VTID_REF'])?$request['VTID_REF']:NULL;
        $VID                =   0;
        $USERID             =   Auth::user()->USERID;   
        $ACTIONNAME         =   'ADD';
        $IPADDRESS          =   $request->getClientIp();
        $CYID_REF           =   Auth::user()->CYID_REF;
        $BRID_REF           =   Session::get('BRID_REF');
        $FYID_REF           =   Session::get('FYID_REF');

        $DOC_NO             =   isset($request['DOC_NO'])?$request['DOC_NO']:NULL;
        $DOC_DT             =   isset($request['DOC_DT'])?$request['DOC_DT']:NULL;
        $NATURE             =   isset($request['NATURE'])?$request['NATURE']:NULL;
        $TRANSFER_TYPE      =   isset($request['TRANSFER_TYPE'])?$request['TRANSFER_TYPE']:NULL;
        $CUSTOMER_TYPE      =   isset($request['CUSTOMER_TYPE'])?$request['CUSTOMER_TYPE']:NULL;
        $SLID_REF           =   isset($request['SLID_REF'])?$request['SLID_REF']:NULL;
        $SubGl_popup        =   isset($request['SubGl_popup'])?$request['SubGl_popup']:NULL;
        $BRANCH_ID          =   isset($request['BRANCH_ID'])?$request['BRANCH_ID']:NULL;
        $TRANSFER_OUT_NO_ID =   isset($request['TRANSFER_OUT_NO_ID'])?$request['TRANSFER_OUT_NO_ID']:NULL;
        $ADDRESS            =   isset($request['ADDRESS'])?$request['ADDRESS']:NULL;
        $GST_NO             =   isset($request['GST_NO'])?$request['GST_NO']:NULL;
        $COUNTRY_ID         =   isset($request['COUNTRY_ID'])?$request['COUNTRY_ID']:NULL;
        $STATE_ID           =   isset($request['STATE_ID'])?$request['STATE_ID']:NULL;
        $CITY_ID            =   isset($request['CITY_ID'])?$request['CITY_ID']:NULL;
        $REMARKS            =   isset($request['REMARKS'])?$request['REMARKS']:NULL;
 
        $log_data = [ 
            $DOC_NO,$DOC_DT,$NATURE,$TRANSFER_TYPE,$SLID_REF,
            $BRANCH_ID,$TRANSFER_OUT_NO_ID,$SubGl_popup, $ADDRESS,$GST_NO,
            $COUNTRY_ID,$STATE_ID,$CITY_ID, $REMARKS,$CYID_REF,
            $BRID_REF,$FYID_REF,$VTID_REF, $XMLMAT,$XMLCAL,
            $XMLSTORE,$USERID,Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,
            $IPADDRESS,$CUSTOMER_TYPE,$VTID_REF2
           
        ];

        $sp_result = DB::select('EXEC SP_STOCK_TRNF_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data); 

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
         
            $HDR    =   DB::select("SELECT 
                        T1.*,
                        T2.NAME AS COUNTRY_NAME,
                        T3.NAME AS STATE_NAME,
                        T4.NAME AS CITY_NAME,
                        T5.BRNAME AS BRANCH_NAME
                        FROM TBL_TRN_STOCK_TRNF_HDR T1
                        LEFT JOIN TBL_MST_COUNTRY T2 ON T1.CTRYID_REF=T2.CTRYID
                        LEFT JOIN TBL_MST_STATE T3 ON T1.STID_REF=T3.STID
                        LEFT JOIN TBL_MST_CITY T4 ON  T1.CITYID_REF=T4.CITYID
                        LEFT JOIN TBL_MST_BRANCH T5 ON T1.BRID=T5.BRID
                        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' AND T1.STID='$id'
                        ");
            
            $HDR    =   isset($HDR) && count($HDR) > 0?$HDR[0]:[];

            $TRANSFER_OUT_NO    =   '';
            if(isset($HDR->TRNF_TYPE) && $HDR->TRNF_TYPE =='IN' && $HDR->TRNSFR_OUT_NO !=''){
                $TBL_TRN_STOCK_TRNF_HDR =   DB::table('TBL_TRN_STOCK_TRNF_HDR')->where('STID','=',$HDR->TRNSFR_OUT_NO)->select('STNO')->first();  
                $TRANSFER_OUT_NO        =   isset($TBL_TRN_STOCK_TRNF_HDR->STNO)?$TBL_TRN_STOCK_TRNF_HDR->STNO:'';  
                $HDR->TRANSFER_OUT_NO   =   $TRANSFER_OUT_NO;
            }
            
            $MAT    =   DB::table('TBL_TRN_STOCK_TRNF_MAT')
                        ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_STOCK_TRNF_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                        ->leftJoin('TBL_MST_UOM', 'TBL_TRN_STOCK_TRNF_MAT.MAIN_UOMID_REF','=','TBL_MST_UOM.UOMID')             
                        ->where('TBL_TRN_STOCK_TRNF_MAT.STID_REF','=',$id)
                        ->select(
                            'TBL_TRN_STOCK_TRNF_MAT.*',
                            'TBL_MST_ITEM.ICODE',
                            'TBL_MST_ITEM.NAME AS ITEM_NAME',
                            'TBL_MST_ITEM.ALPS_PART_NO',
                            'TBL_MST_ITEM.CUSTOMER_PART_NO',
                            'TBL_MST_ITEM.OEM_PART_NO',
                            'TBL_MST_ITEM.MAIN_UOMID_REF',
                            'TBL_MST_ITEM.ALT_UOMID_REF',
                            'TBL_MST_UOM.UOMCODE',
                            'TBL_MST_UOM.DESCRIPTIONS')
                        ->orderBy('TBL_TRN_STOCK_TRNF_MAT.STMATID','ASC')
                        ->get()->toArray();

            $objCount1      =   count($MAT);
            $objCount2      =   count(array());
            $objCount3      =   count(array());

            $objSOCAL       =   DB::table('TBL_TRN_STOCK_TRNF_CAL')                    
                                ->where('TBL_TRN_STOCK_TRNF_CAL.STID_REF','=',$id)
                                ->select('TBL_TRN_STOCK_TRNF_CAL.*')
                                ->orderBy('TBL_TRN_STOCK_TRNF_CAL.STCALID','ASC')
                                ->get()->toArray();

            $objCount4      =   count($objSOCAL);
            $objCount5      =   count(array());
     
            $objRights      =   DB::table('TBL_MST_USERROLMAP')
			->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
			->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
			->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
			->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
			->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
			->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
			->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
			->first();

            $TAXSTATE       =   [];
           
            if(isset($objSO->SHIPTO) && $objSO->SHIPTO !=""){
                $sid        =   $objSO->SHIPTO;
                $ObjSHIPTO  =   DB::select('SELECT top 1 * FROM TBL_MST_CUSTOMERLOCATION WHERE  SHIPTO= ? AND CLID = ? ', [1,$sid]);
                $ObjBranch  =   DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH WHERE BRID= ? ', [$BRID_REF]);

                if($ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF){
                    $TAXSTATE[] = 'WithinState';
                }
                else{
                    $TAXSTATE[] = 'OutofState';
                }
            }

            $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION WHERE  CYID_REF = ? AND BRID_REF = ?   order by CTCODE ASC', [$CYID_REF, $BRID_REF ]);

      

            $objCalculationHeader	=   Helper::getCalculationHeader(array(
                'CYID_REF'=>Auth::user()->CYID_REF,
                'BRID_REF'=>Session::get('BRID_REF'),
                'USERID'=>Auth::user()->USERID,
                'HEADING'=>'Transactions',
                'VTID_REF'=>$this->vtid_ref,
                'FORMID'=>$this->form_id
                ));

       
            $objCalDetails  =   DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')->get() ->toArray(); 

            $FormId         =   $this->form_id;
            $AlpsStatus     =   $this->AlpsStatus();
            $ActionStatus   =   "";
            $TabSetting	    =	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view($this->view.$FormId.'edit',compact(['HDR','AlpsStatus','FormId','objRights','objCount1',
           'objCount2','objCount3','objCount4','objCount5','MAT','objSOCAL',
           'objCalculationHeader','objCalHeader','objCalDetails','TAXSTATE','ActionStatus','TabSetting']));
        }
     
    }
     
    public function view($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){
         
            $HDR    =   DB::select("SELECT 
                        T1.*,
                        T2.NAME AS COUNTRY_NAME,
                        T3.NAME AS STATE_NAME,
                        T4.NAME AS CITY_NAME,
                        T5.BRNAME AS BRANCH_NAME
                        FROM TBL_TRN_STOCK_TRNF_HDR T1
                        LEFT JOIN TBL_MST_COUNTRY T2 ON T1.CTRYID_REF=T2.CTRYID
                        LEFT JOIN TBL_MST_STATE T3 ON T1.STID_REF=T3.STID
                        LEFT JOIN TBL_MST_CITY T4 ON  T1.CITYID_REF=T4.CITYID
                        LEFT JOIN TBL_MST_BRANCH T5 ON T1.BRID=T5.BRID
                        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' AND T1.STID='$id'
                        ");
            
            $HDR    =   isset($HDR) && count($HDR) > 0?$HDR[0]:[];

            $TRANSFER_OUT_NO    =   '';
            if(isset($HDR->TRNF_TYPE) && $HDR->TRNF_TYPE =='IN' && $HDR->TRNSFR_OUT_NO !=''){
                $TBL_TRN_STOCK_TRNF_HDR =   DB::table('TBL_TRN_STOCK_TRNF_HDR')->where('STID','=',$HDR->TRNSFR_OUT_NO)->select('STNO')->first();  
                $TRANSFER_OUT_NO        =   isset($TBL_TRN_STOCK_TRNF_HDR->STNO)?$TBL_TRN_STOCK_TRNF_HDR->STNO:'';  
                $HDR->TRANSFER_OUT_NO   =   $TRANSFER_OUT_NO;
            }
            
            $MAT    =   DB::table('TBL_TRN_STOCK_TRNF_MAT')
                        ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_STOCK_TRNF_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                        ->leftJoin('TBL_MST_UOM', 'TBL_TRN_STOCK_TRNF_MAT.MAIN_UOMID_REF','=','TBL_MST_UOM.UOMID')             
                        ->where('TBL_TRN_STOCK_TRNF_MAT.STID_REF','=',$id)
                        ->select(
                            'TBL_TRN_STOCK_TRNF_MAT.*',
                            'TBL_MST_ITEM.ICODE',
                            'TBL_MST_ITEM.NAME AS ITEM_NAME',
                            'TBL_MST_ITEM.ALPS_PART_NO',
                            'TBL_MST_ITEM.CUSTOMER_PART_NO',
                            'TBL_MST_ITEM.OEM_PART_NO',
                            'TBL_MST_ITEM.MAIN_UOMID_REF',
                            'TBL_MST_ITEM.ALT_UOMID_REF',
                            'TBL_MST_UOM.UOMCODE',
                            'TBL_MST_UOM.DESCRIPTIONS')
                        ->orderBy('TBL_TRN_STOCK_TRNF_MAT.STMATID','ASC')
                        ->get()->toArray();

            $objCount1      =   count($MAT);
            $objCount2      =   count(array());
            $objCount3      =   count(array());

            $objSOCAL       =   DB::table('TBL_TRN_STOCK_TRNF_CAL')                    
                                ->where('TBL_TRN_STOCK_TRNF_CAL.STID_REF','=',$id)
                                ->select('TBL_TRN_STOCK_TRNF_CAL.*')
                                ->orderBy('TBL_TRN_STOCK_TRNF_CAL.STCALID','ASC')
                                ->get()->toArray();

            $objCount4      =   count($objSOCAL);
            $objCount5      =   count(array());
     
            $objRights      =   DB::table('TBL_MST_USERROLMAP')
			->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
			->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
			->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
			->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
			->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
			->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
			->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
			->first();

            $TAXSTATE       =   [];
           
            if(isset($objSO->SHIPTO) && $objSO->SHIPTO !=""){
                $sid        =   $objSO->SHIPTO;
                $ObjSHIPTO  =   DB::select('SELECT top 1 * FROM TBL_MST_CUSTOMERLOCATION WHERE  SHIPTO= ? AND CLID = ? ', [1,$sid]);
                $ObjBranch  =   DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH WHERE BRID= ? ', [$BRID_REF]);

                if($ObjSHIPTO[0]->STID_REF == $ObjBranch[0]->STID_REF){
                    $TAXSTATE[] = 'WithinState';
                }
                else{
                    $TAXSTATE[] = 'OutofState';
                }
            }

            $objCalHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION WHERE  CYID_REF = ? AND BRID_REF = ?   order by CTCODE ASC', [$CYID_REF, $BRID_REF ]);

      

            $objCalculationHeader	=   Helper::getCalculationHeader(array(
                'CYID_REF'=>Auth::user()->CYID_REF,
                'BRID_REF'=>Session::get('BRID_REF'),
                'USERID'=>Auth::user()->USERID,
                'HEADING'=>'Transactions',
                'VTID_REF'=>$this->vtid_ref,
                'FORMID'=>$this->form_id
                ));

       
            $objCalDetails  =   DB::table('TBL_MST_CALCULATIONTEMPLATE')->select('*')->get() ->toArray(); 

            $FormId         =   $this->form_id;
            $AlpsStatus     =   $this->AlpsStatus();
            $ActionStatus   =   "disabled";
            $TabSetting	    =	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view($this->view.$FormId.'view',compact(['HDR','AlpsStatus','FormId','objRights','objCount1',
           'objCount2','objCount3','objCount4','objCount5','MAT','objSOCAL',
           'objCalculationHeader','objCalHeader','objCalDetails','TAXSTATE','ActionStatus','TabSetting']));
        }
     
    }

    
    public function update(Request $request){

        $r_count1           =   $request['Row_Count1'];
        $r_count3           =   $request['Row_Count3'];
        $r_count4           =   $request['Row_Count4'];
        $TRANSFER_TYPE      =   $request['TRANSFER_TYPE'];
    
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
                        $storeid    =   NULL;

                        if($TRANSFER_TYPE =="IN"){
                            $storeid        =   $batchid;
                        }
                        else{
                            $objBatch =  DB::SELECT("SELECT BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF,SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) AS STOCK 
                            FROM TBL_MST_BATCH 
                            WHERE BATCHID='$batchid'
                            GROUP BY BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF 
                            HAVING SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) > '0.000'
                            ")[0];

                            $storeid        =   $objBatch->STID_REF;
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

                //$WhereId    =   $request['exist_'.$i];
                //$Field_Id   =   explode("-",$WhereId);
                // $SEID_REF   =   intval($Field_Id[2]);
                // $SQID_REF   =   intval($Field_Id[3]);
                // $SO         =   intval($Field_Id[4]);
                // $SCID_REF   =   intval($Field_Id[5]);

                $req_data[$i] = [
                    'ITEMID_REF'    => isset($request['ITEMID_REF_'.$i]) && $request['ITEMID_REF_'.$i] !=""?$request['ITEMID_REF_'.$i]:'',
                    'ITEMDESC'    => isset($request['ItemName_'.$i]) && $request['ItemName_'.$i] !=""?$request['ItemName_'.$i]:'',
                    'MAIN_UOMID_REF' => isset($request['MAIN_UOMID_REF_'.$i]) && $request['MAIN_UOMID_REF_'.$i] !=""?$request['MAIN_UOMID_REF_'.$i]:'',
                    'STID' => isset($STID_REF) && $STID_REF !=""?$STID_REF:'',
                    'ST_QTY' => $request['SO_QTY_'.$i] !=""?$request['SO_QTY_'.$i]:0,
                    'RATEPUOM' => $request['RATEPUOM_'.$i] !=""?$request['RATEPUOM_'.$i]:0,
                    'GST' =>  (isset($request['flagtype_'.$i])!="true" ? 0 : 1) ,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'BATCH_QTY'   => $request['HiddenRowId_'.$i] !=""?$request['HiddenRowId_'.$i]:'',
                ];
            }
        }

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        $XMLUDF = NULL; 

        
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


                // $WhereId    =   $request['exist_'.$i];
                // $Field_Id   =   explode("-",$WhereId);
                // $SEID_REF   =   intval($Field_Id[2]);
                // $SQID_REF   =   intval($Field_Id[3]);
                // $SO         =   intval($Field_Id[4]);
                // $SCID_REF   =   intval($Field_Id[5]);


                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        $BATCHNO        =   NULL;
                        $SERIALNO       =   NULL;
                        $STID_REF       =   NULL;
                        $MAINUOMID_REF  =   NULL;
                        $STOCK_INHAND   =   NULL;

                        if($TRANSFER_TYPE =="IN"){
                            $BATCHNO        =   NULL;
                            $SERIALNO       =   NULL;
                            $STID_REF       =   $key;
                            $MAINUOMID_REF  =   NULL;
                            $STOCK_INHAND   =   NULL;
                        }
                        else{
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
                    
                        $req_data33[$i][] = [
                            'ITEMID_REF'    => isset($ITEMID_REF) && $ITEMID_REF !=""?$ITEMID_REF:'',
                            'MAINUOMID_REF'     => isset($MAINUOMID_REF) && $MAINUOMID_REF !=""?$MAINUOMID_REF:'',
                            'BATCHNO'      => isset($BATCHNO) && $BATCHNO !=""?$BATCHNO:'',
                            'SERIALNO'     => isset($SERIALNO) && $SERIALNO !=""?$SERIALNO:'',
                            'SOTCK'  => isset($STOCK_INHAND) && $STOCK_INHAND !=''?floatval($STOCK_INHAND):0,
                            'STORE_QTY'           => isset($val) && $val !=""?$val:0,
                            'STID'      => isset($STID_REF) && $STID_REF !=""?intval($STID_REF):'',

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
        $VTID_REF2    =   isset($request['VTID_REF'])?$request['VTID_REF']:NULL;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $DOC_NO             =   isset($request['DOC_NO'])?$request['DOC_NO']:NULL;
        $DOC_DT             =   isset($request['DOC_DT'])?$request['DOC_DT']:NULL;
        $NATURE             =   isset($request['NATURE'])?$request['NATURE']:NULL;
        $TRANSFER_TYPE      =   isset($request['TRANSFER_TYPE'])?$request['TRANSFER_TYPE']:NULL;
        $CUSTOMER_TYPE      =   isset($request['CUSTOMER_TYPE'])?$request['CUSTOMER_TYPE']:NULL;
        $SLID_REF           =   isset($request['SLID_REF'])?$request['SLID_REF']:NULL;
        $SubGl_popup        =   isset($request['SubGl_popup'])?$request['SubGl_popup']:NULL;
        $BRANCH_ID          =   isset($request['BRANCH_ID'])?$request['BRANCH_ID']:NULL;
        $TRANSFER_OUT_NO_ID =   isset($request['TRANSFER_OUT_NO_ID'])?$request['TRANSFER_OUT_NO_ID']:NULL;
        $ADDRESS            =   isset($request['ADDRESS'])?$request['ADDRESS']:NULL;
        $GST_NO             =   isset($request['GST_NO'])?$request['GST_NO']:NULL;
        $COUNTRY_ID         =   isset($request['COUNTRY_ID'])?$request['COUNTRY_ID']:NULL;
        $STATE_ID           =   isset($request['STATE_ID'])?$request['STATE_ID']:NULL;
        $CITY_ID            =   isset($request['CITY_ID'])?$request['CITY_ID']:NULL;
        $REMARKS            =   isset($request['REMARKS'])?$request['REMARKS']:NULL;

        $log_data = [ 
            $DOC_NO,$DOC_DT,$NATURE,$TRANSFER_TYPE,$SLID_REF,
            $BRANCH_ID,$TRANSFER_OUT_NO_ID,$SubGl_popup, $ADDRESS,$GST_NO,
            $COUNTRY_ID,$STATE_ID,$CITY_ID, $REMARKS,$CYID_REF,
            $BRID_REF,$FYID_REF,$VTID_REF, $XMLMAT,$XMLCAL,
            $XMLSTORE,$USERID,Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,
            $IPADDRESS,$CUSTOMER_TYPE,$VTID_REF2
        ];

        $sp_result = DB::select('EXEC SP_STOCK_TRNF_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data); 

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $DOC_NO. ' Sucessfully Updated.']);

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
        $TRANSFER_TYPE      =   $request['TRANSFER_TYPE'];
       
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
                        $storeid    =   NULL;

                        if($TRANSFER_TYPE =="IN"){
                            $storeid        =   $batchid;
                        }
                        else{
                            $objBatch =  DB::SELECT("SELECT BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF,SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) AS STOCK 
                            FROM TBL_MST_BATCH 
                            WHERE BATCHID='$batchid'
                            GROUP BY BATCHID,BATCH_CODE,ITEMID_REF,UOMID_REF,STID_REF 
                            HAVING SUM((ISNULL(OPENING_QTY,0)+ISNULL(IN_QTY,0))-ISNULL(OUT_QTY,0)) > '0.000'
                            ")[0];

                            $storeid        =   $objBatch->STID_REF;
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

                //$WhereId    =   $request['exist_'.$i];
                //$Field_Id   =   explode("-",$WhereId);
                // $SEID_REF   =   intval($Field_Id[2]);
                // $SQID_REF   =   intval($Field_Id[3]);
                // $SO         =   intval($Field_Id[4]);
                // $SCID_REF   =   intval($Field_Id[5]);

                $req_data[$i] = [
                    'ITEMID_REF'    => isset($request['ITEMID_REF_'.$i]) && $request['ITEMID_REF_'.$i] !=""?$request['ITEMID_REF_'.$i]:'',
                    'ITEMDESC'    => isset($request['ItemName_'.$i]) && $request['ItemName_'.$i] !=""?$request['ItemName_'.$i]:'',
                    'MAIN_UOMID_REF' => isset($request['MAIN_UOMID_REF_'.$i]) && $request['MAIN_UOMID_REF_'.$i] !=""?$request['MAIN_UOMID_REF_'.$i]:'',
                    'STID' => isset($STID_REF) && $STID_REF !=""?$STID_REF:'',
                    'ST_QTY' => $request['SO_QTY_'.$i] !=""?$request['SO_QTY_'.$i]:0,
                    'RATEPUOM' => $request['RATEPUOM_'.$i] !=""?$request['RATEPUOM_'.$i]:0,
                    'GST' =>  (isset($request['flagtype_'.$i])!="true" ? 0 : 1) ,
                    'IGST' => (!empty($request['IGST_'.$i]) ? $request['IGST_'.$i] : 0),
                    'CGST' => (!empty($request['CGST_'.$i]) ? $request['CGST_'.$i] : 0),
                    'SGST' => (!empty($request['SGST_'.$i]) ? $request['SGST_'.$i] : 0),
                    'BATCH_QTY'   => $request['HiddenRowId_'.$i] !=""?$request['HiddenRowId_'.$i]:'',
                ];
            }
        }

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        $XMLUDF = NULL; 

        
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


                // $WhereId    =   $request['exist_'.$i];
                // $Field_Id   =   explode("-",$WhereId);
                // $SEID_REF   =   intval($Field_Id[2]);
                // $SQID_REF   =   intval($Field_Id[3]);
                // $SO         =   intval($Field_Id[4]);
                // $SCID_REF   =   intval($Field_Id[5]);


                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        $BATCHNO        =   NULL;
                        $SERIALNO       =   NULL;
                        $STID_REF       =   NULL;
                        $MAINUOMID_REF  =   NULL;
                        $STOCK_INHAND   =   NULL;

                        if($TRANSFER_TYPE =="IN"){
                            $BATCHNO        =   NULL;
                            $SERIALNO       =   NULL;
                            $STID_REF       =   $key;
                            $MAINUOMID_REF  =   NULL;
                            $STOCK_INHAND   =   NULL;
                        }
                        else{
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
                    
                        $req_data33[$i][] = [
                            'ITEMID_REF'    => isset($ITEMID_REF) && $ITEMID_REF !=""?$ITEMID_REF:'',
                            'MAINUOMID_REF'     => isset($MAINUOMID_REF) && $MAINUOMID_REF !=""?$MAINUOMID_REF:'',
                            'BATCHNO'      => isset($BATCHNO) && $BATCHNO !=""?$BATCHNO:'',
                            'SERIALNO'     => isset($SERIALNO) && $SERIALNO !=""?$SERIALNO:'',
                            'SOTCK'  => isset($STOCK_INHAND) && $STOCK_INHAND !=''?floatval($STOCK_INHAND):0,
                            'STORE_QTY'           => isset($val) && $val !=""?$val:0,
                            'STID'      => isset($STID_REF) && $STID_REF !=""?intval($STID_REF):'',

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
        $VTID_REF2    =   isset($request['VTID_REF'])?$request['VTID_REF']:NULL;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $DOC_NO             =   isset($request['DOC_NO'])?$request['DOC_NO']:NULL;
        $DOC_DT             =   isset($request['DOC_DT'])?$request['DOC_DT']:NULL;
        $NATURE             =   isset($request['NATURE'])?$request['NATURE']:NULL;
        $TRANSFER_TYPE      =   isset($request['TRANSFER_TYPE'])?$request['TRANSFER_TYPE']:NULL;
        $CUSTOMER_TYPE      =   isset($request['CUSTOMER_TYPE'])?$request['CUSTOMER_TYPE']:NULL;
        $SLID_REF           =   isset($request['SLID_REF'])?$request['SLID_REF']:NULL;
        $SubGl_popup        =   isset($request['SubGl_popup'])?$request['SubGl_popup']:NULL;
        $BRANCH_ID          =   isset($request['BRANCH_ID'])?$request['BRANCH_ID']:NULL;
        $TRANSFER_OUT_NO_ID =   isset($request['TRANSFER_OUT_NO_ID'])?$request['TRANSFER_OUT_NO_ID']:NULL;
        $ADDRESS            =   isset($request['ADDRESS'])?$request['ADDRESS']:NULL;
        $GST_NO             =   isset($request['GST_NO'])?$request['GST_NO']:NULL;
        $COUNTRY_ID         =   isset($request['COUNTRY_ID'])?$request['COUNTRY_ID']:NULL;
        $STATE_ID           =   isset($request['STATE_ID'])?$request['STATE_ID']:NULL;
        $CITY_ID            =   isset($request['CITY_ID'])?$request['CITY_ID']:NULL;
        $REMARKS            =   isset($request['REMARKS'])?$request['REMARKS']:NULL;

        $log_data = [ 
            $DOC_NO,$DOC_DT,$NATURE,$TRANSFER_TYPE,$SLID_REF,
            $BRANCH_ID,$TRANSFER_OUT_NO_ID,$SubGl_popup, $ADDRESS,$GST_NO,
            $COUNTRY_ID,$STATE_ID,$CITY_ID, $REMARKS,$CYID_REF,
            $BRID_REF,$FYID_REF,$VTID_REF, $XMLMAT,$XMLCAL,
            $XMLSTORE,$USERID,Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,
            $IPADDRESS,$CUSTOMER_TYPE,$VTID_REF2
        ];

        //dd($log_data);
        
        $sp_result = DB::select('EXEC SP_STOCK_TRNF_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data); 

             
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $DOC_NO. ' Sucessfully Approved.']);

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
                $TABLE      =   "TBL_TRN_STOCK_TRNF_HDR";
                $FIELD      =   "STID";
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
        $TABLE      =   "TBL_TRN_STOCK_TRNF_HDR";
        $FIELD      =   "STID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_STOCK_TRNF_MAT',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_STOCK_TRNF_CAL',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_STOCK_TRNF_STORE',
        ];
        
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_STOCK_TRNF_HDR')->where('STID','=',$id)->first();

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
        $image_path         =   "docs/company".$CYID_REF."/StockTransfer";     
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
        $STNO = $request->STNO;
        
        $objSO = DB::table('TBL_TRN_STOCK_TRNF_HDR')
        ->where('TBL_TRN_STOCK_TRNF_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_STOCK_TRNF_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_STOCK_TRNF_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_STOCK_TRNF_HDR.STNO','=',$STNO)
        ->select('TBL_TRN_STOCK_TRNF_HDR.STNO')
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
        $TRANSFER_TYPE      =   $request['TRANSFER_TYPE'];
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

        if($TRANSFER_TYPE=="IN"){
        
            $objBatch =  DB::SELECT("SELECT 
            STID,CONCAT(STCODE,'-',NAME) AS StoreName
            FROM TBL_MST_STORE 
            WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND (DEACTIVATED IS NULL OR DEACTIVATED =0)
            ");

            echo'
            <thead>
                <tr>
                    <th style="width:80%;text-align:left;">Store</th>
                    <th style="width:20%;text-align:left;">Qty</th>
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
        else {
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
                    <th style="width:20%;text-align:left;">Qty</th>
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

    public function getAddressDetails(Request $request){

        $ADDRESS        =   NULL;
        $GST_NO         =   NULL;
        $COUNTRY_ID     =   NULL;
        $COUNTRY_NAME   =   NULL;
        $STATE_ID       =   NULL;
        $STATE_NAME     =   NULL;
        $CITY_ID        =   NULL;
        $CITY_NAME      =   NULL;

        if(isset($request['id']) && $request['id'] !=''){

            $SLID_REF                   =   $request['id'];
            $TBL_MST_CUSTOMER_VENDOR    =   DB::table('TBL_MST_CUSTOMER')->where('STATUS','=','A')->where('SLID_REF','=',$SLID_REF)->select('CID AS CVID')->first();

            if(isset($request['TRANSFER_TYPE']) && $request['TRANSFER_TYPE'] === "IN"){
                $TBL_MST_CUSTOMER_VENDOR    =   DB::table('TBL_MST_VENDOR')->where('STATUS','=','A')->where('SLID_REF','=',$SLID_REF)->select('VID AS CVID')->first();
            }
            
            if(isset($TBL_MST_CUSTOMER_VENDOR->CVID) && $TBL_MST_CUSTOMER_VENDOR->CVID !=''){

                $CVID_REF                       =   $TBL_MST_CUSTOMER_VENDOR->CVID;
                $TBL_MST_CUSTOMER_VENDOR_LOC    =   DB::select("SELECT top 1 
                                                    T1.CADD AS ADDRESS, 
                                                    T1.GSTIN AS GST_NO, 
                                                    T2.CTRYID AS COUNTRY_ID,
                                                    T2.NAME AS COUNTRY_NAME, 
                                                    T3.STID AS STATE_ID, 
                                                    T3.NAME AS STATE_NAME, 
                                                    T4.CITYID AS CITY_ID,
                                                    T4.NAME AS CITY_NAME
                                                    FROM TBL_MST_CUSTOMERLOCATION T1  
                                                    LEFT JOIN TBL_MST_COUNTRY T2 ON T1.CTRYID_REF=T2.CTRYID
                                                    LEFT JOIN TBL_MST_STATE T3 ON T1.STID_REF=T3.STID
                                                    LEFT JOIN TBL_MST_CITY T4 ON T1.CITYID_REF=T4.CITYID
                                                    WHERE T1.DEFAULT_BILLTO= 1 AND T1.CID_REF = '$CVID_REF'
                                                    ");

                if(isset($request['TRANSFER_TYPE']) && $request['TRANSFER_TYPE'] === "IN"){
                    $TBL_MST_CUSTOMER_VENDOR_LOC    =   DB::select("SELECT top 1 
                                                        T1.LADD AS ADDRESS, 
                                                        T1.GSTIN AS GST_NO, 
                                                        T2.CTRYID AS COUNTRY_ID,
                                                        T2.NAME AS COUNTRY_NAME, 
                                                        T3.STID AS STATE_ID, 
                                                        T3.NAME AS STATE_NAME, 
                                                        T4.CITYID AS CITY_ID,
                                                        T4.NAME AS CITY_NAME
                                                        FROM TBL_MST_VENDORLOCATION T1  
                                                        LEFT JOIN TBL_MST_COUNTRY T2 ON T1.CTRYID_REF=T2.CTRYID
                                                        LEFT JOIN TBL_MST_STATE T3 ON T1.STID_REF=T3.STID
                                                        LEFT JOIN TBL_MST_CITY T4 ON T1.CITYID_REF=T4.CITYID
                                                        WHERE T1.DEFAULT_BILLING= 1 AND T1.VID_REF = '$CVID_REF'
                                                        ");
                }

                if(isset($TBL_MST_CUSTOMER_VENDOR_LOC) && !empty($TBL_MST_CUSTOMER_VENDOR_LOC)){

                    $ADDRESS        =   $TBL_MST_CUSTOMER_VENDOR_LOC[0]->ADDRESS;
                    $GST_NO         =   $TBL_MST_CUSTOMER_VENDOR_LOC[0]->GST_NO;
                    $COUNTRY_ID     =   $TBL_MST_CUSTOMER_VENDOR_LOC[0]->COUNTRY_ID;
                    $COUNTRY_NAME   =   $TBL_MST_CUSTOMER_VENDOR_LOC[0]->COUNTRY_NAME;
                    $STATE_ID       =   $TBL_MST_CUSTOMER_VENDOR_LOC[0]->STATE_ID;
                    $STATE_NAME     =   $TBL_MST_CUSTOMER_VENDOR_LOC[0]->STATE_NAME;
                    $CITY_ID        =   $TBL_MST_CUSTOMER_VENDOR_LOC[0]->CITY_ID;
                    $CITY_NAME      =   $TBL_MST_CUSTOMER_VENDOR_LOC[0]->CITY_NAME;

                }

            }

        }

        $DATA   =   array(
                        'ADDRESS'       =>  $ADDRESS,
                        'GST_NO'        =>  $GST_NO,
                        'COUNTRY_ID'    =>  $COUNTRY_ID,
                        'COUNTRY_NAME'  =>  $COUNTRY_NAME,
                        'STATE_ID'      =>  $STATE_ID,
                        'STATE_NAME'    =>  $STATE_NAME,
                        'CITY_ID'       =>  $CITY_ID,
                        'CITY_NAME'     =>  $CITY_NAME
                    );

        return Response::json($DATA);
        exit();   
    }

    public function getBranch(Request $request){

        $CYID_REF           =   Auth::user()->CYID_REF;
        $TBL_MST_BRANCH    =   DB::select("SELECT BRID,BRCODE,BRNAME 
                                FROM TBL_MST_BRANCH 
                                WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 )
                                ");

        if(isset($TBL_MST_BRANCH) && !empty($TBL_MST_BRANCH)){
            foreach ($TBL_MST_BRANCH as $index=>$dataRow){

            echo'
            <tr>
                <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->BRID .'" class="clsbranch" value="'.$dataRow->BRID.'" ></td>
                <td class="ROW2">'.$dataRow->BRCODE.'</td>
                <td class="ROW3">'.$dataRow->BRNAME.'</td>
                <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->BRID.'" data-desc="'.$dataRow->BRNAME .'" data-ccname="'.$dataRow->BRNAME.'" value="'.$dataRow->BRID.'"/></td>
            </tr>
            ';
            }
        }
        else{
            echo '<tr><td>Record not found.</td></tr>';
        }
        exit();
    }

    public function getTransferOutNo(Request $request){

        $CYID_REF               =   Auth::user()->CYID_REF;
        $BRID_REF               =   Session::get('BRID_REF');
        $TBL_TRN_STOCK_TRNF_HDR =   DB::select("SELECT STID,STNO,STDT 
                                    FROM TBL_TRN_STOCK_TRNF_HDR 
                                    WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF'
                                    ");

        if(isset($TBL_TRN_STOCK_TRNF_HDR) && !empty($TBL_TRN_STOCK_TRNF_HDR)){
            foreach ($TBL_TRN_STOCK_TRNF_HDR as $index=>$dataRow){

            echo'
            <tr>
                <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->STID .'" class="clstransferoutno" value="'.$dataRow->STID.'" ></td>
                <td class="ROW2">'.$dataRow->STNO.'</td>
                <td class="ROW3">'.$dataRow->STDT.'</td>
                <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->STID.'" data-desc="'.$dataRow->STNO .'" data-ccname="'.$dataRow->STDT.'" value="'.$dataRow->STID.'"/></td>
            </tr>
            ';
            }
        }
        else{
            echo '<tr><td>Record not found.</td></tr>';
        }
        exit();
    }
    
    public function getCountry(Request $request){

        $CYID_REF           =   Auth::user()->CYID_REF;
        $TBL_MST_COUNTRY    =   DB::select("SELECT CTRYID,CTRYCODE,NAME
                                FROM TBL_MST_COUNTRY 
                                WHERE STATUS='A' AND CYID_REF='$CYID_REF' AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) 
                                ");

        if(isset($TBL_MST_COUNTRY) && !empty($TBL_MST_COUNTRY)){
            foreach ($TBL_MST_COUNTRY as $index=>$dataRow){

            echo'
            <tr>
                <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->CTRYID .'" class="clscontry" value="'.$dataRow->CTRYID.'" ></td>
                <td class="ROW2">'.$dataRow->CTRYCODE.'</td>
                <td class="ROW3">'.$dataRow->NAME.'</td>
                <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->CTRYID.'" data-desc="'.$dataRow->CTRYCODE .'" data-ccname="'.$dataRow->NAME.'" value="'.$dataRow->CTRYID.'"/></td>
            </tr>
            ';
            }
        }
        else{
            echo '<tr><td>Record not found.</td></tr>';
        }
        exit();
    }

    public function getState(Request $request){

        $objStateList   =   DB::table('TBL_MST_STATE')
                            ->where('STATUS','=','A')
                            ->where('CTRYID_REF','=',$request['CTRYID_REF'])
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('STID','NAME','STCODE')
                            ->get();

       
    
        if(isset($objStateList) && count($objStateList) > 0){
            foreach($objStateList as $state){
            
                echo '<tr>
                <td class="ROW1"> <input type="checkbox" name="SELECT_STID_REF[]" id="stidref_'.$state->STID.'" class="cls_stidref" value="'.$state->STID.'" ></td>
                <td class="ROW2">'.$state->STCODE.'<input type="hidden" id="txtstidref_'.$state->STID.'" data-desc="'.$state->STCODE.'-'.$state->NAME.'" data-descname="'.$state->NAME.'" value="'.$state->STID.'" /></td>
                <td class="ROW3">'.$state->NAME.'</td>
                </tr>';
            }
        }
        else{
            echo '<tr><td>Record not found.</td></tr>';
        }
        exit();
    }

    public function getCity(Request $request){
    
        $objCityList    =   DB::table('TBL_MST_CITY')
                            ->where('STATUS','=','A')
                            ->where('CTRYID_REF','=',$request['CTRYID_REF'])
                            ->where('STID_REF','=',$request['STID_REF'])
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('CITYID','CITYCODE','NAME')
                            ->get();

        if(isset($objCityList) && count($objCityList) > 0){
            foreach($objCityList as $city){
            
                echo '<tr>
                <td class="ROW1"> <input type="checkbox" name="SELECT_CITYID_REF[]" id="cityidref_'.$city->CITYID.'" class="cls_cityidref" value="'.$city->CITYID.'" ></td>
                <td class="ROW2">'.$city->CITYCODE.'<input type="hidden" id="txtcityidref_'.$city->CITYID.'"  data-desc="'.$city->CITYCODE.'-'.$city->NAME.'" data-descname="'.$city->NAME.'"  value="'.$city->CITYID.'" /></td>
                <td class="ROW3">'.$city->NAME.'</td>
                </tr>';
            }
        }
        else{
            echo '<tr><td>Record not found.</td></tr>';
        }

        exit();
    }

    public function getDocType(Request $request){

        $VTID_REF   =   $request['DOC_TYPE'];
        $STATUS     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $DOC_NO     =   NULL;
        $DOC_LEN    =   '';

        $objSON     =   DB::table('TBL_MST_DOCNO_DEFINITION')
                        ->where('VTID_REF','=',$VTID_REF)
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF)
                        ->where('FYID_REF','=',$FYID_REF)
                        ->where('STATUS','=',$STATUS)
                        ->select('TBL_MST_DOCNO_DEFINITION.*')
                        ->first();

        if( isset($objSON->SYSTEM_GRSR) && $objSON->SYSTEM_GRSR == "1"){
            
            if($objSON->PREFIX_RQ == "1")
            {
                $DOC_NO = $objSON->PREFIX;
            }        
            if($objSON->PRE_SEP_RQ == "1")
            {
                if($objSON->PRE_SEP_SLASH == "1")
                {
                $DOC_NO = $DOC_NO.'/';
                }
                if($objSON->PRE_SEP_HYPEN == "1")
                {
                $DOC_NO = $DOC_NO.'-';
                }
            }        
            if($objSON->NO_MAX)
            {   
                $DOC_NO = $DOC_NO.str_pad($objSON->LAST_RECORDNO+1, $objSON->NO_MAX, "0", STR_PAD_LEFT);
            }
            
            if($objSON->NO_SEP_RQ == "1")
            {
                if($objSON->NO_SEP_SLASH == "1")
                {
                $DOC_NO = $DOC_NO.'/';
                }
                if($objSON->NO_SEP_HYPEN == "1")
                {
                $DOC_NO = $DOC_NO.'-';
                }
            }
            if($objSON->SUFFIX_RQ == "1")
            {
                $DOC_NO = $DOC_NO.$objSON->SUFFIX;
            }
        }
        else if(isset($objSON->MANUAL_SR) && $objSON->MANUAL_SR == "1"){
            $DOC_NO     =   NULL;
            $DOC_LEN    =   isset($objSON->MANUAL_MAXLENGTH)?$objSON->MANUAL_MAXLENGTH:'';
        }

        $DATA   =   array('DOC_NO'=>$DOC_NO,'DOC_LEN'=>$DOC_LEN);

        return Response::json($DATA);
        exit();   
    }



    
}
