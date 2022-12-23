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
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Helpers\Utils;

class TrnFrm43Controller extends Controller
{
    protected $form_id = 43;
    protected $vtid_ref   = 43;  
    
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    
    public function index(){    
        $objRights = DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
		->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
		->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();

        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');     
        
                
        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList	=	DB::select("select hdr.SCID,hdr.SCNO,hdr.SCDT,hdr.remarks,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.SCID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_SLSC01_HDR hdr
                            on a.VID = hdr.SCID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_SUBLEDGER sl ON hdr.SLID_REF = sl.SGLID 
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.SCID DESC ");

                    if(isset($objDataList) && !empty($objDataList)){
                        foreach($objDataList as $key=>$val){

                            $SCID   =   $val->SCID;
                            $data	=   DB::select("SELECT DISTINCT T2.SONO AS SONO,T2.CUSTOMERPONO AS CPNO FROM TBL_TRN_SLSC01_MAT T1 
                            LEFT JOIN TBL_TRN_SLSO01_HDR T2 ON T1.SO=T2.SOID
                            WHERE T1.SCID_REF='$SCID'");

                            $SONO   =   array();
                            $CPNO   =   array();
                            if(isset($data) && !empty($data)){
                                foreach($data as $index=>$row){

                                    if($row->SONO !=''){
                                        $SONO[]=$row->SONO;
                                    }

                                    if($row->CPNO !=''){
                                        $CPNO[]=$row->CPNO;
                                    }
                                }
                            }

                            $objDataList[$key]->SONO=!empty($SONO)?implode(',',$SONO):NULL;
                            $objDataList[$key]->CPNO=!empty($CPNO)?implode(',',$CPNO):NULL;
                        }
                    }

                
                            

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );

                                                    
        
        return view('transactions.sales.SalesChallan.trnfrm43',compact(['REQUEST_DATA','objRights','objDataList']));        
    }

    public function ViewReport($request) {
        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
           
        $SCID       =   $myValue['SCNO'];
        $Flag       =   $myValue['Flag'];
    
            $objSalesChallan = DB::table('TBL_TRN_SLSC01_HDR')
            ->where('TBL_TRN_SLSC01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_SLSC01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
            ->where('TBL_TRN_SLSC01_HDR.SCID','=',$SCID)
            ->select('TBL_TRN_SLSC01_HDR.*')
            ->first();
            //dd($objSalesChallan);
            $ssrs = new \SSRS\Report('http://103.139.58.23:8181//ReportServer/', array('username' => 'App', 'password' => 'admin@123'));
            $result = $ssrs->loadReport('/ERP/SCPrint_ALPS');
            
            $reportParameters = array(
                'SCNo' => $objSalesChallan->SCNO,
            );
            // dd($reportParameters);
            $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
            // dd($parameters);
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
                $output = $ssrs->render('HTML4.0'); // PDF | XML | CSV
                echo $output;
            }
             
    }  

    public function add(){       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $FormId = $this->form_id;
        

        $objtransporter = DB::table('TBL_MST_TRANSPORTER')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_TRANSPORTER.*')
        ->get()
        ->toArray();

        $objlastSCDT = DB::select('SELECT MAX(SCDT) SCDT FROM TBL_TRN_SLSC01_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  43, 'A' ]);
        
        $objSCN = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',43)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('FYID_REF','=',$FYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_DOCNO_DEFINITION.*')
        ->first();

        $objSCNO =[];
        if(isset($objSCN) && !empty($objSCN)){
            if($objSCN->SYSTEM_GRSR == "1")
            {
                if($objSCN->PREFIX_RQ == "1")
                {
                    $objSCNO = $objSCN->PREFIX;
                }        
                if($objSCN->PRE_SEP_RQ == "1")
                {
                    if($objSCN->PRE_SEP_SLASH == "1")
                    {
                    $objSCNO = $objSCNO.'/';
                    }
                    if($objSCN->PRE_SEP_HYPEN == "1")
                    {
                    $objSCNO = $objSCNO.'-';
                    }
                }        
                if($objSCN->NO_MAX)
                {   
                    $objSCNO = $objSCNO.str_pad($objSCN->LAST_RECORDNO+1, $objSCN->NO_MAX, "0", STR_PAD_LEFT);
                }
                
                if($objSCN->NO_SEP_RQ == "1")
                {
                    if($objSCN->NO_SEP_SLASH == "1")
                    {
                    $objSCNO = $objSCNO.'/';
                    }
                    if($objSCN->NO_SEP_HYPEN == "1")
                    {
                    $objSCNO = $objSCNO.'-';
                    }
                }
                if($objSCN->SUFFIX_RQ == "1")
                {
                    $objSCNO = $objSCNO.$objSCN->SUFFIX;
                }
            }
        }
		
		$doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_SLSC01_HDR',
            'HDR_ID'=>'SCID',
            'HDR_DOC_NO'=>'SCNO',
            'HDR_DOC_DT'=>'SCDT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        $ObjUnionUDF = DB::table("TBL_MST_UDF_SCHALLAN")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                                {       
                                $query->select('UDF_SCID')->from('TBL_MST_UDF_SCHALLAN')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF)
                                                ->where('BRID_REF','=',$BRID_REF);
                                                                    
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF);
                                 
                   

        $objUdfSCData = DB::table('TBL_MST_UDF_SCHALLAN')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdfSCData);
    

        $ObjSalesOrderData = DB::table("TBL_TRN_SLSO01_HDR")->select('*')
                    ->where('STATUS','=','A')                    
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF)
                    ->where('FYID_REF','=',$FYID_REF) 
                    ->get() ->toArray(); 
                    
                    $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
       
    return view('transactions.sales.SalesChallan.trnfrm43add',
    compact(['objUdfSCData','objSCN','ObjSalesOrderData','objCountUDF','objSCNO','objlastSCDT','objtransporter','FormId','AlpsStatus','TabSetting',
	'doc_req','docarray']));       
   }

   public function getItemwiseStoreDetails(Request $request){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $Status         =   'A';
        $ITEMID_REF     =   $request['ITEMID_REF'];
        $MAINUOMID_REF  =   $request['MAINUOMID_REF'];
        $ALTUOMID_REF   =   $request['ALTUOMID_REF'];
        $RawID          =   $request['RawID'];
        $BATCHID_REF    =   $request['BATCHID_REF'] !=''?explode(',',$request['BATCHID_REF']):[];
        $STORE_QTYS     =   $request['STORE_QTYS'] !=''?explode(',',$request['STORE_QTYS']):[];
        $STORE_DATA     =   array_combine($BATCHID_REF,$STORE_QTYS);
		$ACTION_TYPE    =   $request['ACTION_TYPE'] =="VIEW"?'disabled':'';
   
        if($request['ACTION_TYPE'] =="ADD"){

            $ObjData    =   DB::table("TBL_MST_BATCH")->select('*')
                            ->where('STATUS','=',$Status)                    
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('BRID_REF','=',$BRID_REF)
                            ->where('ITEMID_REF','=',$ITEMID_REF) 
                            ->where('UOMID_REF','=',$MAINUOMID_REF) 
                           ->where('CURRENT_QTY','>',0) 
                            ->get() ->toArray(); 
        }
        else{

            $ObjData    =   DB::table("TBL_MST_BATCH")->select('*')
                            ->where('STATUS','=',$Status)                    
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('BRID_REF','=',$BRID_REF)
                            ->where('ITEMID_REF','=',$ITEMID_REF) 
                            ->where('UOMID_REF','=',$MAINUOMID_REF) 
                            ->get() ->toArray(); 
        }


        if(!empty($ObjData)){

            $row1 = '';
            $row2 = '<input type="hidden" id="RawID" name="RawID" value="'.$RawID.'" />';
            $row3 = '';

        foreach ($ObjData as $dindex=>$dRow){

            $ObjStore = DB::select('SELECT TOP 1 * FROM TBL_MST_STORE WHERE STATUS = ?
                        AND STID = ?',[$Status,$dRow->STID_REF]);
            
            $ObjMUOM = DB::select('SELECT TOP 1 * FROM TBL_MST_UOM WHERE STATUS = ?
                        AND UOMID = ?',[$Status,$dRow->UOMID_REF]);

            $ObjAUOM = DB::select('SELECT TOP 1 * FROM TBL_MST_UOM WHERE STATUS = ?
                        AND UOMID = ?',[$Status,$ALTUOMID_REF]);

            $ObjConv = DB::select('SELECT TOP 1 * FROM TBL_MST_ITEM_UOMCONV WHERE ITEMID_REF = ?
                        AND TO_UOMID_REF = ?',[$ITEMID_REF,$ALTUOMID_REF]);

             $qtyvalue   =   array_key_exists($dRow->BATCHID, $STORE_DATA)?$STORE_DATA[$dRow->BATCHID]:'';
			

             $mqty       =   $ObjConv[0]->FROM_QTY;
             $aqty       =   $ObjConv[0]->TO_QTY;
             $daltqty       =  $qtyvalue;

            if($qtyvalue !=""){
                $daltqty    =   ($qtyvalue * $aqty)/$mqty;
            }
            else{
                $daltqty    =   "";
            }

            if($request['ACTION_TYPE'] =="ADD"){

                $CURRENT_QTY=$dRow->CURRENT_QTY;
            }
            else{
                $CURRENT_QTY=(floatval($dRow->CURRENT_QTY)+floatval($qtyvalue));
            }

            
            
            $row = '';
            $row = $row.' <tr class="clsstrid">';
            $row = $row.'<td hidden><input type="text" name= "STORE_NAME_'.$dindex.'" id= "STORE_NAME_'.$dindex.'" class="form-control" value="'.$ObjStore[0]->NAME.'" /></td>';
            $row = $row.'<td hidden><input type="text" name= "strITEMID_REF_'.$dindex.'" id= "strITEMID_REF_'.$dindex.'" class="form-control" value="'.$ITEMID_REF.'" /></td>';
            $row = $row.'<td><input '.$ACTION_TYPE.' type="text" name= "strBATCHNO_'.$dindex.'" id= "strBATCHNO_'.$dindex.'" class="form-control" value="'.$dRow->BATCH_CODE.'" readonly /></td>';
            $row = $row.'<td hidden><input type="text" name= "strBATCHID_'.$dindex.'" id= "strBATCHID_'.$dindex.'" class="form-control" value="'.$dRow->BATCHID.'"  /></td>';
            $row = $row.'<td><input '.$ACTION_TYPE.' type="text" name= "STORE_REF_'.$dindex.'" id= "STORE_REF_'.$dindex.'" class="form-control" value="'.$ObjStore[0]->NAME.'" readonly /></td>';
               
            $row = $row.'<td hidden><input type="text" name= "strSTID_REF_'.$dindex.'" id= "strSTID_REF_'.$dindex.'" class="form-control" value="'.$dRow->STID_REF.'" /></td>';
            $row = $row.'<td hidden><input type="text" name= "MUOM_REF_'.$dindex.'" id= "MUOM_REF_'.$dindex.'" class="form-control" value="'.$dRow->UOMID_REF.'" /></td>';
            $row = $row.'<td><input '.$ACTION_TYPE.' type="text" name= "strMAINUOMID_REF_'.$dindex.'" id= "strMAINUOMID_REF_'.$dindex.'" value="'.$ObjMUOM[0]->UOMCODE.'-'.$ObjMUOM[0]->DESCRIPTIONS.'" class="form-control" readonly /></td>';
            $row = $row.'<td><input '.$ACTION_TYPE.' type="text" name= "strSOTCK_'.$dindex.'" id= "strSOTCK_'.$dindex.'" class="form-control three-digits" value="'.$CURRENT_QTY.'" style="text-align:right;" readonly /></td>';
            $row = $row.'<td><input '.$ACTION_TYPE.' type="text" name= "strDISPATCH_MAIN_QTY_'.$dindex.'" style="text-align:right;" id= "strDISPATCH_MAIN_QTY_'.$dindex.'"  class="form-control three-digits" maxlength="13" onkeypress="return isNumberDecimalKey(event,this)" autocomplete="off"  value="'.$qtyvalue.'"  /></td>';
            $row = $row.'<td><input '.$ACTION_TYPE.' type="text" name= "strRATE_'.$dindex.'" style="text-align:right;" id= "strRATE_'.$dindex.'" class="form-control three-digits" value="'.$dRow->RATE.'" readonly /></td>';
            $row = $row.'<td hidden><input type="text" name= "CONV_MAIN_QTY_'.$dindex.'" id= "CONV_MAIN_QTY_'.$dindex.'" class="form-control three-digits" value="'.$ObjConv[0]->FROM_QTY.'"  maxlength="13"   /></td>';
            $row = $row.'<td hidden><input type="text" name= "CONV_ALT_QTY_'.$dindex.'" id= "CONV_ALT_QTY_'.$dindex.'" class="form-control three-digits" value="'.$ObjConv[0]->TO_QTY.'"  maxlength="13"   /></td>';
            $row = $row.'<td hidden><input type="text" name= "AUOM_REF_'.$dindex.'" id= "AUOM_REF_'.$dindex.'" class="form-control" value="'.$ALTUOMID_REF.'" /></td>';
            $row = $row.'<td><input '.$ACTION_TYPE.' type="text" name= "strALTUOMID_REF_'.$dindex.'" style="text-align:right;" id= "strALTUOMID_REF_'.$dindex.'" value="'.$ObjAUOM[0]->UOMCODE.'-'.$ObjAUOM[0]->DESCRIPTIONS.'" class="form-control" readonly /></td>';
            $row = $row.'<td><input '.$ACTION_TYPE.' style="text-align:right;" type="text" name= "DISPATCH_ALT_QTY_'.$dindex.'" id= "DISPATCH_ALT_QTY_'.$dindex.'" class="form-control three-digits"  value="'.$daltqty.'" readonly /></td>';
            $row = $row.'</tr>';
            $row1 = $row1.$row;
            
        }

        $row3 = $row1.$row2;
        echo $row3;



        }else{
            echo '<tr><td colspan="7">Record not found.</td></tr>';
        }
        exit();

    }


    

    public function getaltqty(Request $request){
    
        $Status = "A";
        $itemid = $request['itemid'];
        $auomid = $request['auomid'];
    
        $ObjData =  DB::table('TBL_MST_ITEM_UOMCONV')
        ->where('TBL_MST_ITEM_UOMCONV.ITEMID_REF','=',$itemid)
        ->where('TBL_MST_ITEM_UOMCONV.TO_UOMID_REF','=',$auomid)
        ->select('TBL_MST_ITEM_UOMCONV.*')
        ->first();
           
            if(!empty($ObjData)){
                echo $ObjData->TO_QTY ;
            }else{
                echo '0';
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

    public function getsalesorder(Request $request){
        $TYPE = $request['TYPE'];
        $Status = "A";      
        $id = $request['id'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $SHIPTO = $request['SHIPTO'];
        $BILLTO = $request['BILLTO'];
    
        $SP_PARAMETERS = [$CYID_REF,$BRID_REF,$FYID_REF,$id,$BILLTO];

        if($TYPE=='SO'){
        $ObjData =  DB::select('EXEC SP_SO_GETLIST ?,?,?,?,?', $SP_PARAMETERS);
      
        }else{
        $ObjData = DB::table('TBL_TRN_SLSO03_HDR')
        ->where('STATUS','=',$Status)
        ->where('SLID_REF','=',$id)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('FYID_REF','=',$FYID_REF)
        ->select('OSOID AS SOID','OSONO AS SONO','OSODT AS SODT','CUSTOMERPONO','REFNO AS CUSTOMERAREFNO','REMARKS')
        ->get()
        ->toArray();
        }       
        
        


      
   
            if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){
                $DEALERID_REF =isset($dataRow->DEALERID_REF) ? $dataRow->DEALERID_REF:''; 
                $SCHEMEID_REF =isset($dataRow->SCHEMEID_REF) ? $dataRow->SCHEMEID_REF:''; 
                $row = '';
                $row = $row.'<tr><td style="width:10%;text-align:center;"> <input type="checkbox" name="SELECT_SOID[]" id="socode_'.$index.'" class="clssoid" value="'.$dataRow-> SOID.'" ></td>';
                $row = $row.'<td style="width:15%;">'.$dataRow->SONO;
                $row = $row.'<input type="hidden" id="txtsocode_'.$index.'" data-desc="'.$dataRow->SONO .'"  data-desc1="'.$DEALERID_REF  .'"  data-desc2="'.$SCHEMEID_REF .'"  value="'.$dataRow->SOID.'"/></td><td style="width:15%;">'.$dataRow->SODT.'</td>
                <td style="width:15%;">'.$dataRow->CUSTOMERPONO.'</td>
                <td style="width:15%;">'.$dataRow->CUSTOMERAREFNO.'</td>
                <td style="width:60%;">'.$dataRow->REMARKS.'</td>     
                </tr>';

                echo $row;
            }
    
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    }




    public function getItemDetailsSalesOrderwise(Request $request){
        $Status = "A";
        $id = $request['id'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');       
        
        $AlpsStatus =   $this->AlpsStatus();

                    $Objquote =  DB::select('SELECT * FROM TBL_TRN_SLSO01_MAT  
                    WHERE SOID_REF = ? order by ITEMID_REF ASC', [$id]);

               // dd($Objquote); 

                  

                    if(!empty($Objquote)){

                        foreach ($Objquote as $index=>$dataRow){

                            $ObjSO = DB::select('select Top 1 * from TBL_TRN_SLSO01_HDR
                            WHERE SOID = ?',[$id]);
                           // dd($ObjSO);
                            
                            $SOPENDINGQTY = 0;
                            $TOTCHLNQTY = 0;
                            $objSlschln = DB::select('SELECT  * FROM TBL_TRN_SLSC01_MAT  
                            WHERE SO = ? and ITEMID_REF = ? AND SQID_REF = ? AND SEID_REF = ? ', 
                            [$dataRow->SOID_REF,$dataRow->ITEMID_REF,$dataRow->SQA,$dataRow->SEQID_REF]);
                            foreach ($objSlschln as $cindex=>$cRow){
                                $TOTCHLNQTY += !empty($cRow->CHALLAN_MAINQTY)? $cRow->CHALLAN_MAINQTY : 0;
                            }
                            $SOPENDINGQTY = !empty($dataRow->PENDING_QTY)? $dataRow->PENDING_QTY : 0 - $TOTCHLNQTY;
                            $ObjItem =  DB::select('SELECT top 1 * FROM TBL_MST_ITEM  
                                        WHERE ITEMID = ? ', [$dataRow->ITEMID_REF]);

                                     
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
                            
                            $TOQTY =  !empty($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 0;
                            
                            $FROMQTY =  !empty($ObjAltQTY[0]->FROM_QTY)? $ObjAltQTY[0]->FROM_QTY : 0;

                            $ObjItemGroup =  DB::select('SELECT TOP 1 GROUPCODE, GROUPNAME FROM TBL_MST_ITEMGROUP  
                                        WHERE  CYID_REF = ?  AND ITEMGID = ?
                                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                        [$CYID_REF, $ObjItem[0]->ITEMGID_REF, $Status ]);

                            $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS FROM TBL_MST_ITEMCATEGORY  
                                        WHERE  CYID_REF = ?  AND ICID = ?
                                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                        [$CYID_REF, $ObjItem[0]->ICID_REF, $Status ]);


                            $ItemRowData =  DB::select('SELECT top 1 * FROM TBL_MST_ITEM  WHERE ITEMID = ? ', [$ObjItem[0]->ITEMID]);

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

                            //dd($dataRow);
            
                        
                        
                            $row = '';

                            if($dataRow->PENDING_QTY > 0){

                            $row = $row.'<tr id="item_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'"  class="clsitemid">
                            <td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  
                            value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                            $row = $row.'<td style="width:10%;">'.$ObjItem[0]->ICODE;

                            $row = $row.'<input type="hidden" id="txtitem_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'" 
                            data-desc="'.$ObjItem[0]->ICODE.'" value="'.$ObjItem[0]->ITEMID.'"/></td>


                            <td style="width:10%;" id="itemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'" >'.$ObjItem[0]->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'" 
                            data-desc="'.$ObjItem[0]->ITEM_SPECI.'" value="'.$ObjItem[0]->NAME.'"/></td>';



                            $row = $row.'<td style="width:8%;" id="itemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'" >
                            <input type="hidden" id="txtitemuom_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'" 
                            data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'" data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" data-desc5="'.$dataRow->RATEPUOM.'" 
                            value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';

                            
                            $row = $row.'<td style="width:8%;" id="uomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'" >
                            <input type="hidden" id="txtuomqty_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'" 
                            data-desc="'.$TOQTY.'" value="'.$dataRow->ALT_UOMID_REF.'"/>'.$dataRow->PENDING_QTY.'</td>';                            

                            $row = $row.'<td style="width:8%;" id="irate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'">
                            <input type="hidden" id="txtirate_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'" 
                            data-desc="'.$FROMQTY.'" value="'.$SOPENDINGQTY.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                            $row = $row.'<td style="width:8%;" id="itax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'">
                            <input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'" data-desc="'.$dataRow->SQA.'"
                            value="'.$dataRow->SEQID_REF.'"/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                            
                            <td style="width:8%;">'.$BusinessUnit.'</td>   


                            <td style="width:8%;" '.$AlpsStatus['hidden'].'  id="alps_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'">
                            <input type="hidden" id="txtalps_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'"  
                            data-desc="'.$ALPS_PART_NO.'" value="'.$ALPS_PART_NO.'"/>'.$ALPS_PART_NO.' </td>

                            
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                            <td style="width:8%;" id="ise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'">
                            <input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'"  
                            data-desc="'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'" value="'.$ObjSO[0]->SONO.'"/>Authorized</td>
                            </tr>';

                            }


                        echo $row;
                        }

                    }else{
                        echo '<tr><td> Record not found.</td></tr>';
                    }
        exit();
    
    }



    public function getItemDetailsOpenSalesOrderwise(Request $request){

            $Status = "A";
            $id = $request['id'];
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF'); 
            
            $AlpsStatus =   $this->AlpsStatus();
         
            $Objquote = DB::table('TBL_TRN_SLSO03_MAT')   
            ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_SLSO03_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                 
            ->where('TBL_TRN_SLSO03_MAT.OSOID_REF','=',$id)
            ->select('TBL_TRN_SLSO03_MAT.*', 'TBL_MST_ITEM.ALT_UOMID_REF')
            ->get()->toArray();
          
            


                    if(!empty($Objquote)){

                        foreach ($Objquote as $index=>$dataRow){

                            $ObjSO = DB::select('select Top 1 * from TBL_TRN_SLSO03_HDR
                            WHERE OSOID = ?',[$id]);
                            
                            $SOPENDINGQTY = 1;
                            $TOTCHLNQTY = 0;
                            $SO_QTY = 1;
                   



                        $objSlschln = DB::select('SELECT  * FROM TBL_TRN_SLSO03_MAT  
                        WHERE OSOID_REF = ? and ITEMID_REF = ? ', 
                        [$dataRow->OSOID_REF,$dataRow->ITEMID_REF]);

                         
                    
                            $ObjItem =  DB::select('SELECT top 1 * FROM TBL_MST_ITEM  
                                        WHERE ITEMID = ? ', [$dataRow->ITEMID_REF]);

                                     
                            $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                        WHERE  CYID_REF = ?  AND UOMID = ? 
                                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                        [$CYID_REF, $dataRow->UOMID_REF, $Status ]);



                                        $objSO = DB::table('TBL_MST_ITEM')
                                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                                        ->where('ITEMID','=',$dataRow->ITEMID_REF)
                                        ->select('ALT_UOMID_REF')
                                        ->first();
                                    
                                        $auomid=$objSO->ALT_UOMID_REF; 




                            $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                            WHERE  CYID_REF = ?  AND UOMID = ? 
                            AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                            [$CYID_REF, $auomid, $Status ]);

                
                            $ObjAltQTY =  DB::select('SELECT TOP 1  * FROM TBL_MST_ITEM_UOMCONV  
                            WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                            [$dataRow->ITEMID_REF,$auomid ]);


                            
                            $TOQTY =  !empty($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 0;
                            
                            $FROMQTY =  !empty($ObjAltQTY[0]->FROM_QTY)? $ObjAltQTY[0]->FROM_QTY : 0;

                            $ObjItemGroup =  DB::select('SELECT TOP 1 GROUPCODE, GROUPNAME FROM TBL_MST_ITEMGROUP  
                                        WHERE  CYID_REF = ?  AND ITEMGID = ?
                                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                        [$CYID_REF, $ObjItem[0]->ITEMGID_REF, $Status ]);

                            $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS FROM TBL_MST_ITEMCATEGORY  
                                        WHERE  CYID_REF = ?  AND ICID = ?
                                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                        [$CYID_REF, $ObjItem[0]->ICID_REF, $Status ]);


                            $ItemRowData =  DB::select('SELECT top 1 * FROM TBL_MST_ITEM  WHERE ITEMID = ? ', [$ObjItem[0]->ITEMID]);

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
                            $row = $row.'<tr id="item_'.$ObjItem[0]->ITEMID.'"  class="clsitemid">
                            <td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ObjItem[0]->ITEMID.'"  
                            value="'.$ObjItem[0]->ITEMID.'" class="js-selectall1"  ></td>';
                            $row = $row.'<td style="width:10%;">'.$ObjItem[0]->ICODE;

                            $row = $row.'<input type="hidden" id="txtitem_'.$ObjItem[0]->ITEMID.'" 
                            data-desc="'.$ObjItem[0]->ICODE.'" value="'.$ObjItem[0]->ITEMID.'"/></td>


                            <td style="width:10%;" id="itemname_'.$ObjItem[0]->ITEMID.'" >'.$ObjItem[0]->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$ObjItem[0]->ITEMID.'" 
                            data-desc="'.$ObjItem[0]->ITEM_SPECI.'" value="'.$ObjItem[0]->NAME.'"/></td>';




                            $row = $row.'<td style="width:8%;" id="itemuom_'.$ObjItem[0]->ITEMID.'" >
                            <input type="hidden" id="txtitemuom_'.$ObjItem[0]->ITEMID.'" 
                            data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'" data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'"  data-desc5="'.$dataRow->RATEPUOM.'"
                            value="'.$dataRow->UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';

                            
                            $row = $row.'<td style="width:8%;" id="uomqty_'.$ObjItem[0]->ITEMID.'" >
                            <input type="hidden" id="txtuomqty_'.$ObjItem[0]->ITEMID.'" 
                            data-desc="'.$TOQTY.'" value="'.$SO_QTY.'"/>'.$SO_QTY.'</td>';

                            

                            $row = $row.'<td style="width:8%;" id="irate_'.$ObjItem[0]->ITEMID.'">
                            <input type="hidden" id="txtirate_'.$ObjItem[0]->ITEMID.'" 
                            data-desc="'.$FROMQTY.'" value="'.$SOPENDINGQTY.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                            $row = $row.'<td style="width:8%;" id="itax_'.$ObjItem[0]->ITEMID.'">  
                            
                            <td style="width:8%;">'.$BusinessUnit.'</td>
     


                            <td style="width:8%;" '.$AlpsStatus['hidden'].'  id="alps_'.$ObjItem[0]->ITEMID.'">
                            <input type="hidden" id="txtalps_'.$ObjItem[0]->ITEMID.'"  
                            data-desc="'.$ALPS_PART_NO.'" value="'.$ALPS_PART_NO.'"/>'.$ALPS_PART_NO.' </td>

                            
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                            <td style="width:8%;" id="ise_'.$ObjItem[0]->ITEMID.'">
                            <input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'"  
                            data-desc="'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'" value="'.$ObjSO[0]->OSONO.'"/>Authorized</td>
                            </tr>';

                        echo $row;
                        }

                    }else{
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
              echo   $auomqty = ($mqty/$ObjData[0]->FROM_QTY)*($ObjData[0]->TO_QTY);   
            
    
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
                $row = $row.'<input type="hidden" name="BILLTO" id="BILLTO" class="form-control" autocomplete="off" value="'. $ObjAddressID.'" />';
                
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
                    $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_CLID[]" id="billto_'.$index.'" class="clsbillto" value="'.$dataRow-> CLID.'" ></td>';
                    $row = $row.'<td class="ROW2">'.$dataRow->NAME;
                    $row = $row.'<input type="hidden" id="txtbillto_'.$index.'" data-desc="'.$objAddress.'" value="'.$dataRow->CLID.'"/></td><td class="ROW3">'.$objAddress.'</td></tr>';

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
                    $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_CLID[]" id="shipto_'.$index.'" class="clsshipto" value="'.$dataRow-> CLID.'" ></td>';
                    $row = $row.'<td class="ROW2">'.$dataRow->NAME;
                    $row = $row.'<input type="hidden" id="txtshipto_'.$index.'" data-desc="'.$objAddress.'" data-desc2="'.$TAXSTATE.'" value="'.$dataRow->CLID.'"/></td><td class="ROW3">'.$objAddress.'</td></tr>';

                    echo $row;
                }
        
                }else{
                    echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
                exit();
            }
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
                    $row = $row.'<input type="hidden" name="SHIPTO" id="SHIPTO" class="form-control" autocomplete="off" value="'. $ObjAddressID.'" />';
                    $row = $row.'<input type="hidden" name="Tax_State" id="Tax_State" class="form-control" autocomplete="off" value="'. $TAXSTATE.'" />';
                    
                    echo $row;
                    }else{
                        echo '';
                    }
                    exit();
        
            }


  
   public function attachment($id){

    if(!is_null($id))
    {
        $objSalesChallan = DB::table("TBL_TRN_SLSC01_HDR")
                        ->where('SCID','=',$id)
                        ->select('TBL_TRN_SLSC01_HDR.*')
                        ->first(); 

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

                

            return view('transactions.sales.SalesChallan.trnfrm43attachment',compact(['objSalesChallan','objMstVoucherType','objAttachments']));
    }

}

    
   public function save(Request $request) {
        
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
       
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['SOID_REF_'.$i]))
            {
                $req_data[$i] = [
                    'SRNO' => $i+1,
                    'SO' => (isset($request['SOID_REF_'.$i]) ? $request['SOID_REF_'.$i] : NULL) ,
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'MAINUOMID_REF' => $request['MAINUOMID_REF_'.$i],
                    'ITEMSPECI' => $request['ITEMSPECI_'.$i],
                    'SOPENDINGQTY' => $request['SOPENDINGQTY_'.$i],
                    'CHALLAN_MAINQTY' => $request['CHALLAN_MAINQTY_'.$i],
                    'ALTUOMID_REF' => $request['ALTUOMID_REF_'.$i],
                    'SQID_REF' => (!empty($request['SQID_REF_'.$i])) == 'true' ? $request['SQID_REF_'.$i] : NULL,
                    'SEID_REF' => (!empty($request['SEID_REF_'.$i])) == 'true' ? $request['SEID_REF_'.$i] : NULL,
                  
                ];



                $store_data    =   array();
                if(isset($request['BATCHID_REF_'.$i]) && $request['BATCHID_REF_'.$i] !=''){
                    $batchid_array =   explode(',',$request['BATCHID_REF_'.$i]);
                    $batchQty =   explode(',',$request['STORE_QTYS_'.$i]);
                    $BatchQtyDta=array_combine($batchid_array,$batchQty); 
                    foreach($batchid_array as $batchid){
                        $store_data[] = [
                            'SERIALNO'      =>  $i+1,
                            'BATCHID_REF'      =>  $batchid,
                            'QTY'      =>   array_key_exists($batchid, $BatchQtyDta)?$BatchQtyDta[$batchid]:'0',
                            'ITEMID_REF'      =>  $request['ITEMID_REF_'.$i]
                        ];
                    }
                }                
                $req_data[$i]['STORE']=$store_data;
            }
        }

		
       //DD($req_data); 

            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
       
        for ($i=0; $i<=$r_count2; $i++)
        {
                if(isset($request['SC_UDFID_REF_'.$i]))
                {
                    $reqdata2[$i] = [
                        'SC_UDFID_REF'   => $request['SC_UDFID_REF_'.$i],
                        'VALUE'      => $request['udfvalue_'.$i],
                    ];
                }
            
        }

       

        if(isset($reqdata2))
        { 
            $wrapped_links3["UDF"] = $reqdata2; 
            $XMLUDF = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $XMLUDF = NULL; 
        }
        
     
       

            $VTID_REF       =   $this->vtid_ref;
            $VID            = 0;
            $USERID         = Auth::user()->USERID;   
            $ACTIONNAME     = 'ADD';
            $IPADDRESS      = $request->getClientIp();
            $CYID_REF       = Auth::user()->CYID_REF;
            $BRID_REF       = Session::get('BRID_REF');
            $FYID_REF       = Session::get('FYID_REF');
            $SCNO           = $request['SCNO'];
            $SCDT           = $request['SCDT'];
            $GLID_REF       = $request['GLID_REF'];
            $SLID_REF       = $request['SLID_REF'];
            $BILLTO         = $request['BILLTO'];
            $SHIPTO         = $request['SHIPTO'];
            $REMARKS        = $request['REMARKS'];
            $TRANSPORTERID_REF      = $request['TRANSPORTERID_REF'];            
            $SHIPPINGINSTRUCTION    = $request['SHIPPINGINSTRUCTION'];
            $TRANSPORTTIME          = $request['TRANSPORTTIME'];
            $TYPE                   = $request['TYPE'];
			$CHALLANTYPE            = $request['CHALLANTYPE'];

            $log_data = [ 
                $SCNO,$SCDT,$GLID_REF,$SLID_REF,$REMARKS,$BILLTO,$SHIPTO,$TRANSPORTERID_REF,$CYID_REF, $BRID_REF, 
                $FYID_REF,$VTID_REF,$XMLMAT, $XMLUDF, $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, 
                $IPADDRESS,$SHIPPINGINSTRUCTION,$TRANSPORTTIME,$TYPE,$CHALLANTYPE
            ];

           
            
            $sp_result = DB::select('EXEC SP_SC_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       

            //dd($sp_result); 
            
           
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
    
                return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);
    
            }else{
                return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
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
            $objSC = DB::table('TBL_TRN_SLSC01_HDR')
                             ->where('TBL_TRN_SLSC01_HDR.FYID_REF','=',Session::get('FYID_REF'))
                             ->where('TBL_TRN_SLSC01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_TRN_SLSC01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                             ->where('TBL_TRN_SLSC01_HDR.SCID','=',$id)
                             ->select('TBL_TRN_SLSC01_HDR.*')
                             ->first();
            $log_data = [ 
                $id
            ];

            $objSCMAT=[];
            if(isset($objSC) && !empty($objSC)){
                $objSCMAT = DB::select('EXEC sp_get_sales_challan_material ?', $log_data);
            }

           // dd($objSCMAT); 
          

            if(isset($objSCMAT) && !empty($objSCMAT)){
                foreach($objSCMAT as $key=>$val){                     
                    $objStoredata   =  $this->getStoreId($val->SRNO,$val->SCID_REF);
                    $objSCMAT[$key]->BATCHID_REF    =  $objStoredata['BATCHID_REF'];
                    $objSCMAT[$key]->STORE_QTYS     =  $objStoredata['QTY'];
                    $objSCMAT[$key]->STORE_NAME     =  $objStoredata['STORE_NAME'];   
                 
                }
            }      
          

            if(isset($objSCMAT) && !empty($objSCMAT)){
                foreach($objSCMAT as $key=>$val){

               

                    if($objSC->TYPE =="SO"){
                        
                        $RATE       =   DB::table('TBL_TRN_SLSO01_MAT')->where('SOID_REF','=',$val->SO)->where('ITEMID_REF','=',$val->ITEMID_REF)->select('RATEPUOM')->first();
                        $RATEPUOM   =   isset($RATE->RATEPUOM) && $RATE->RATEPUOM !=""?$RATE->RATEPUOM:NULL; 
                        $objSCMAT[$key]->RATEPUOM=$RATEPUOM;
                        $objSCMAT[$key]->SCHEMEID_REF  =  $this->getSchemeData($val->SO);
                       
                    }
                    else if($objSC->TYPE =="OSO"){
                        
                        $RATE       =   DB::table('TBL_TRN_SLSO03_MAT')->where('OSOID_REF','=',$val->SO)->where('ITEMID_REF','=',$val->ITEMID_REF)->select('RATEPUOM')->first();
                        $RATEPUOM   =   isset($RATE->RATEPUOM) && $RATE->RATEPUOM !=""?$RATE->RATEPUOM:NULL; 
                        $objSCMAT[$key]->RATEPUOM=$RATEPUOM;
                        $objSCMAT[$key]->SCHEMEID_REF  =  '';  
                      
                       
                    }
                   
                }
            }


           

            $objCount1 = count($objSCMAT);
 
            $objSCUDF = DB::table('TBL_TRN_SLSC01_UDF')                    
                             ->where('TBL_TRN_SLSC01_UDF.SCID_REF','=',$id)
                             ->select('TBL_TRN_SLSC01_UDF.*')
                             ->orderBy('TBL_TRN_SLSC01_UDF.SCUDFID','ASC')
                             ->get()->toArray();

            $objCount2 = count($objSCUDF);

            $SP_PARAMETERS = [$id];
         

            
     
            $objRights = DB::table('TBL_MST_USERROLMAP')
			->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
			->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
			->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
			->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
			->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
			->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
			->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
			->first();
                             $TAXSTATE=[];
                             $objShpAddress=[] ;
                             $objBillAddress=[];

                             if(isset($objSC->SHIPTO) && $objSC->SHIPTO !=""){
                             $sid = $objSC->SHIPTO;
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

                            if(isset($objSC->BILLTO) && $objSC->BILLTO !=""){
                            
                            $bid = $objSC->BILLTO;
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
            
            $objsubglcode=[];
            if(isset($objSC->GLID_REF) && $objSC->GLID_REF !=""){
                $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('STATUS','=',$Status)
                ->where('GLID_REF','=',$objSC->GLID_REF)
                ->where('SGLID','=',$objSC->SLID_REF)
                ->select('TBL_MST_SUBLEDGER.*')
                ->first();
            }

            
            $ObjUnionUDF = DB::table("TBL_MST_UDF_SCHALLAN")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                                    {       
                                    $query->select('UDF_SCID')->from('TBL_MST_UDF_SCHALLAN')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF)
                                                    ->where('BRID_REF','=',$BRID_REF);
                                                                
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF);
                                      
                    
    
            $objUdfSCData = DB::table('TBL_MST_UDF_SCHALLAN')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            $objCountUDF = count($objUdfSCData);

            $ObjUnionUDF2 = DB::table("TBL_MST_UDF_SCHALLAN")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                        {       
                        $query->select('UDF_SCID')->from('TBL_MST_UDF_SCHALLAN')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF)
                                        ->where('BRID_REF','=',$BRID_REF);
                                                          
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF);
           

            $objUdfSCData2 = DB::table('TBL_MST_UDF_SCHALLAN')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF2)
                ->get()->toArray(); 
            
            $objtransporter = DB::table('TBL_MST_TRANSPORTER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_TRANSPORTER.*')
            ->get()
            ->toArray();
            
            $objTransporter2=[];
            if(isset($objSC->TRANSPORTERID_REF) && $objSC->TRANSPORTERID_REF !=""){
                $objTransporter2 = DB::table('TBL_MST_TRANSPORTER')
                ->where('TRANSPORTERID','=',$objSC->TRANSPORTERID_REF)
                ->select('TBL_MST_TRANSPORTER.*')
                ->first();
            }
    
            $ObjSalesOrderData = DB::table("TBL_TRN_SLSO01_HDR")->select('*')
                        ->where('STATUS','=','A')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF)
                        ->where('FYID_REF','=',$FYID_REF) 
                        ->get() ->toArray();                    
                    
            $objlastSCDT = DB::select('SELECT MAX(SCDT) SCDT FROM TBL_TRN_SLSC01_HDR  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
            [$CYID_REF, $BRID_REF,  43, 'A' ]);
 
            $objSOMAT = DB::table('TBL_TRN_SLSO01_MAT')->select('*')
            ->get() ->toArray();

            $objUOM = DB::table('TBL_MST_UOM')->select('*')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',$CYID_REF)
            ->get() ->toArray(); 
            $objItemUOMConv = DB::table('TBL_MST_ITEM_UOMCONV')->select('*')
            ->get() ->toArray(); 
            $FormId = $this->form_id;

            $AlpsStatus =   $this->AlpsStatus();
            $InputStatus=   "";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
        return view('transactions.sales.SalesChallan.trnfrm43edit',compact(['objSC','objRights','objCount1','objUOM',
           'objCount2','objSCMAT','objSCUDF','objUdfSCData','objItemUOMConv','objUdfSCData2',
           'ObjSalesOrderData','objsubglcode','objShpAddress','objBillAddress','objSOMAT','FormId',
           'TAXSTATE','objtransporter','objTransporter2','objCountUDF','objlastSCDT','AlpsStatus','InputStatus','TabSetting']));
        }
     
       }
     
       public function view($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id))
        {
            $objSC = DB::table('TBL_TRN_SLSC01_HDR')
                             ->where('TBL_TRN_SLSC01_HDR.FYID_REF','=',Session::get('FYID_REF'))
                             ->where('TBL_TRN_SLSC01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_TRN_SLSC01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                             ->where('TBL_TRN_SLSC01_HDR.SCID','=',$id)
                             ->select('TBL_TRN_SLSC01_HDR.*')
                             ->first();
            $log_data = [ 
                $id
            ];

            $objSCMAT=[];
            if(isset($objSC) && !empty($objSC)){
                $objSCMAT = DB::select('EXEC sp_get_sales_challan_material ?', $log_data);
            }

            
            if(isset($objSCMAT) && !empty($objSCMAT)){
                foreach($objSCMAT as $key=>$val){                     
                    $objStoredata   =  $this->getStoreId($val->SRNO,$val->SCID_REF);
                    $objSCMAT[$key]->BATCHID_REF    =  $objStoredata['BATCHID_REF'];
                    $objSCMAT[$key]->STORE_QTYS     =  $objStoredata['QTY'];
                    $objSCMAT[$key]->STORE_NAME     =  $objStoredata['STORE_NAME'];
                }
            }

            if(isset($objSCMAT) && !empty($objSCMAT)){
                foreach($objSCMAT as $key=>$val){

               

                    if($objSC->TYPE =="SO"){
                        
                        $RATE       =   DB::table('TBL_TRN_SLSO01_MAT')->where('SOID_REF','=',$val->SO)->where('ITEMID_REF','=',$val->ITEMID_REF)->select('RATEPUOM')->first();
                        $RATEPUOM   =   isset($RATE->RATEPUOM) && $RATE->RATEPUOM !=""?$RATE->RATEPUOM:NULL; 
                        $objSCMAT[$key]->RATEPUOM=$RATEPUOM;
                        $objSCMAT[$key]->SCHEMEID_REF  =  $this->getSchemeData($val->SO);
                       
                    }
                    else if($objSC->TYPE =="OSO"){
                        
                        $RATE       =   DB::table('TBL_TRN_SLSO03_MAT')->where('OSOID_REF','=',$val->SO)->where('ITEMID_REF','=',$val->ITEMID_REF)->select('RATEPUOM')->first();
                        $RATEPUOM   =   isset($RATE->RATEPUOM) && $RATE->RATEPUOM !=""?$RATE->RATEPUOM:NULL; 
                        $objSCMAT[$key]->RATEPUOM=$RATEPUOM;
                        $objSCMAT[$key]->SCHEMEID_REF  =  '';  
                      
                       
                    }
                   
                }
            }

            $objCount1 = count($objSCMAT);
 
            $objSCUDF = DB::table('TBL_TRN_SLSC01_UDF')                    
                             ->where('TBL_TRN_SLSC01_UDF.SCID_REF','=',$id)
                             ->select('TBL_TRN_SLSC01_UDF.*')
                             ->orderBy('TBL_TRN_SLSC01_UDF.SCUDFID','ASC')
                             ->get()->toArray();
            $objCount2 = count($objSCUDF);
            $SP_PARAMETERS = [$id];



            
     
            $objRights = DB::table('TBL_MST_USERROLMAP')
			->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
			->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
			->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
			->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
			->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
			->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
			->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
			->first();
                             $TAXSTATE=[];
                             $objShpAddress=[] ;
                             $objBillAddress=[];

                             if(isset($objSC->SHIPTO) && $objSC->SHIPTO !=""){
                             $sid = $objSC->SHIPTO;
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

                            if(isset($objSC->BILLTO) && $objSC->BILLTO !=""){
                            
                            $bid = $objSC->BILLTO;
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
            
            $objsubglcode=[];
            if(isset($objSC->GLID_REF) && $objSC->GLID_REF !=""){
                $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('STATUS','=',$Status)
                ->where('GLID_REF','=',$objSC->GLID_REF)
                ->where('SGLID','=',$objSC->SLID_REF)
                ->select('TBL_MST_SUBLEDGER.*')
                ->first();
            }

            
            $ObjUnionUDF = DB::table("TBL_MST_UDF_SCHALLAN")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                                    {       
                                    $query->select('UDF_SCID')->from('TBL_MST_UDF_SCHALLAN')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF)
                                                    ->where('BRID_REF','=',$BRID_REF);
                                                                      
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF);
                                      
                    
    
            $objUdfSCData = DB::table('TBL_MST_UDF_SCHALLAN')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            $objCountUDF = count($objUdfSCData);

            $ObjUnionUDF2 = DB::table("TBL_MST_UDF_SCHALLAN")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                        {       
                        $query->select('UDF_SCID')->from('TBL_MST_UDF_SCHALLAN')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF)
                                        ->where('BRID_REF','=',$BRID_REF);
                                                       
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF);
            

            $objUdfSCData2 = DB::table('TBL_MST_UDF_SCHALLAN')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF2)
                ->get()->toArray(); 
            
            $objtransporter = DB::table('TBL_MST_TRANSPORTER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_TRANSPORTER.*')
            ->get()
            ->toArray();
            
            $objTransporter2=[];
            if(isset($objSC->TRANSPORTERID_REF) && $objSC->TRANSPORTERID_REF !=""){
                $objTransporter2 = DB::table('TBL_MST_TRANSPORTER')
                ->where('TRANSPORTERID','=',$objSC->TRANSPORTERID_REF)
                ->select('TBL_MST_TRANSPORTER.*')
                ->first();
            }
    
            $ObjSalesOrderData = DB::table("TBL_TRN_SLSO01_HDR")->select('*')
                        ->where('STATUS','=','A')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF)
                        ->where('FYID_REF','=',$FYID_REF) 
                        ->get() ->toArray();                    
                    
            $objlastSCDT = DB::select('SELECT MAX(SCDT) SCDT FROM TBL_TRN_SLSC01_HDR  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
            [$CYID_REF, $BRID_REF,  43, 'A' ]);
 
            $objSOMAT = DB::table('TBL_TRN_SLSO01_MAT')->select('*')
            ->get() ->toArray();

            $objUOM = DB::table('TBL_MST_UOM')->select('*')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',$CYID_REF)
            ->get() ->toArray(); 
            $objItemUOMConv = DB::table('TBL_MST_ITEM_UOMCONV')->select('*')
            ->get() ->toArray(); 
            $FormId = $this->form_id;

            $AlpsStatus =   $this->AlpsStatus();
            $InputStatus=   "disabled";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
        return view('transactions.sales.SalesChallan.trnfrm43view',compact(['objSC','objRights','objCount1','objUOM',
           'objCount2','objSCMAT','objSCUDF','objUdfSCData','objItemUOMConv','objUdfSCData2',
           'ObjSalesOrderData','objsubglcode','objShpAddress','objBillAddress','objSOMAT','FormId',
           'TAXSTATE','objtransporter','objTransporter2','objCountUDF','objlastSCDT','AlpsStatus','InputStatus','TabSetting']));
        }
     
       }

    
   public function update(Request $request){
    
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
     


       
       
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['SOID_REF_'.$i]))
            {
                $req_data[$i] = [
                    'SRNO' => $i+1,
                    'SO' => (isset($request['SOID_REF_'.$i]) ? $request['SOID_REF_'.$i] : NULL) ,
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'MAINUOMID_REF' => $request['MAINUOMID_REF_'.$i],
                    'ITEMSPECI' => $request['ITEMSPECI_'.$i],
                    'SOPENDINGQTY' => $request['SOPENDINGQTY_'.$i],
                    'CHALLAN_MAINQTY' => $request['CHALLAN_MAINQTY_'.$i],
                    'ALTUOMID_REF' => $request['ALTUOMID_REF_'.$i],
                    'SQID_REF' => (!empty($request['SQID_REF_'.$i])) == 'true' ? $request['SQID_REF_'.$i] : NULL,
                    'SEID_REF' => (!empty($request['SEID_REF_'.$i])) == 'true' ? $request['SEID_REF_'.$i] : NULL,
                  
                ];



                $store_data    =   array();
                if(isset($request['BATCHID_REF_'.$i]) && $request['BATCHID_REF_'.$i] !=''){
                    $batchid_array =   explode(',',$request['BATCHID_REF_'.$i]);
                    $batchQty =   explode(',',$request['STORE_QTYS_'.$i]);
                    $BatchQtyDta=array_combine($batchid_array,$batchQty); 
                    foreach($batchid_array as $batchid){
                        $store_data[] = [
                            'SERIALNO'      =>  $i+1,
                            'BATCHID_REF'      =>  $batchid,
                            'QTY'      =>   array_key_exists($batchid, $BatchQtyDta)?$BatchQtyDta[$batchid]:'0',
                            'ITEMID_REF'      =>  $request['ITEMID_REF_'.$i]
                        ];
                    }
                }

                
                $req_data[$i]['STORE']=$store_data;
            }
        }

		
       //DD($req_data); 

            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);

       
        for ($i=0; $i<=$r_count2; $i++)
        {
                if(isset($request['SC_UDFID_REF_'.$i]))
                {
                    $reqdata2[$i] = [
                        'SC_UDFID_REF'   => $request['SC_UDFID_REF_'.$i],
                        'VALUE'      => $request['udfvalue_'.$i],
                    ];
                }
            
        }
        if(isset($reqdata2))
        { 
            $wrapped_links3["UDF"] = $reqdata2; 
            $XMLUDF = ArrayToXml::convert($wrapped_links3);
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
            $SCNO = $request['SCNO'];
            $SCDT = $request['SCDT'];
            $GLID_REF = $request['GLID_REF'];
            $SLID_REF = $request['SLID_REF'];
            $BILLTO = $request['BILLTO'];
            $SHIPTO = $request['SHIPTO'];
            $REMARKS = $request['REMARKS'];
            $TRANSPORTERID_REF = $request['TRANSPORTERID_REF'];
            $SHIPPINGINSTRUCTION = $request['SHIPPINGINSTRUCTION'];
            $TRANSPORTTIME = $request['TRANSPORTTIME'];
            $TYPE = $request['TYPE'];
			$CHALLANTYPE = $request['CHALLANTYPE'];

            $log_data = [ 
                $SCNO,$SCDT,$GLID_REF,$SLID_REF,$REMARKS,$BILLTO,$SHIPTO,$TRANSPORTERID_REF,$CYID_REF, $BRID_REF, 
                $FYID_REF,$VTID_REF,$XMLMAT, $XMLUDF,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, 
                $IPADDRESS,$SHIPPINGINSTRUCTION,$TRANSPORTTIME,$TYPE,$CHALLANTYPE
            ];

            
            $sp_result = DB::select('EXEC SP_SC_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       

          //dd($sp_result); 
            
        
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
                foreach ($sp_listing_result as $key=>$saleschallanitem)
            {  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$saleschallanitem->LAVELS;
            }
            }
           
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];



       
      
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['SOID_REF_'.$i]))
            {
                $req_data[$i] = [
                    'SRNO' => $i+1,
                    'SO' => (isset($request['SOID_REF_'.$i]) ? $request['SOID_REF_'.$i] : NULL) ,
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'MAINUOMID_REF' => $request['MAINUOMID_REF_'.$i],
                    'ITEMSPECI' => $request['ITEMSPECI_'.$i],
                    'SOPENDINGQTY' => $request['SOPENDINGQTY_'.$i],
                    'CHALLAN_MAINQTY' => $request['CHALLAN_MAINQTY_'.$i],
                    'ALTUOMID_REF' => $request['ALTUOMID_REF_'.$i],
                    'SQID_REF' => (!empty($request['SQID_REF_'.$i])) == 'true' ? $request['SQID_REF_'.$i] : NULL,
                    'SEID_REF' => (!empty($request['SEID_REF_'.$i])) == 'true' ? $request['SEID_REF_'.$i] : NULL,
                  
                ];



                $store_data    =   array();
                if(isset($request['BATCHID_REF_'.$i]) && $request['BATCHID_REF_'.$i] !=''){
                    $batchid_array =   explode(',',$request['BATCHID_REF_'.$i]);
                    $batchQty =   explode(',',$request['STORE_QTYS_'.$i]);
                    $BatchQtyDta=array_combine($batchid_array,$batchQty); 
                    foreach($batchid_array as $batchid){
                        $store_data[] = [
                            'SERIALNO'      =>  $i+1,
                            'BATCHID_REF'      =>  $batchid,
                            'QTY'      =>   array_key_exists($batchid, $BatchQtyDta)?$BatchQtyDta[$batchid]:'0',
                            'ITEMID_REF'      =>  $request['ITEMID_REF_'.$i]
                        ];
                    }
                }

                
                $req_data[$i]['STORE']=$store_data;
            }
        }

		
       // DD($req_data); 

            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
       
        for ($i=0; $i<=$r_count2; $i++)
        {
                if(isset($request['SC_UDFID_REF_'.$i]))
                {
                    $reqdata2[$i] = [
                        'SC_UDFID_REF'   => $request['SC_UDFID_REF_'.$i],
                        'VALUE'      => $request['udfvalue_'.$i],
                    ];
                }
            
        }
        if(isset($reqdata2))
        { 
            $wrapped_links3["UDF"] = $reqdata2; 
            $XMLUDF = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $XMLUDF = NULL; 
        }
        
        
        

            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = $Approvallevel;
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $SCNO = $request['SCNO'];
            $SCDT = $request['SCDT'];
            $GLID_REF = $request['GLID_REF'];
            $SLID_REF = $request['SLID_REF'];
            $BILLTO = $request['BILLTO'];
            $SHIPTO = $request['SHIPTO'];
            $REMARKS = $request['REMARKS'];
            $TRANSPORTERID_REF = $request['TRANSPORTERID_REF'];
            $SHIPPINGINSTRUCTION = $request['SHIPPINGINSTRUCTION'];
            $TRANSPORTTIME = $request['TRANSPORTTIME'];
            $TYPE = $request['TYPE'];
			$CHALLANTYPE = $request['CHALLANTYPE'];

            $log_data = [ 
                $SCNO,$SCDT,$GLID_REF,$SLID_REF,$REMARKS,$BILLTO,$SHIPTO,$TRANSPORTERID_REF,$CYID_REF, $BRID_REF, 
                $FYID_REF,$VTID_REF,$XMLMAT, $XMLUDF, $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, 
                $IPADDRESS,$SHIPPINGINSTRUCTION,$TRANSPORTTIME,$TYPE,$CHALLANTYPE
            ];

            
            $sp_result = DB::select('EXEC SP_SC_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);      
            
        
            if($sp_result[0]->RESULT=="SUCCESS"){
    
                return Response::json(['success' =>true,'msg' => 'Record successfully approved.']);
    
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
                    foreach ($sp_listing_result as $key=>$saleschallanitem)
                {  
                    $record_status = 0;
                    $Approvallevel = "APPROVAL".$saleschallanitem->LAVELS;
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
                $TABLE      =   "TBL_TRN_SLSC01_HDR";
                $FIELD      =   "SCID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
           
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
    
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
        $TABLE      =   "TBL_TRN_SLSC01_HDR";
        $FIELD      =   "SCID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_SLSC01_MAT',
           ];
           $req_data[1]=[
           'NT'  => 'TBL_TRN_SLSC01_STORE',
           ];
           $req_data[2]=[
           'NT'  => 'TBL_TRN_SLSC01_UDF',
           ];
           $req_data[3]=[
            'NT'  => 'TBL_TRN_SLSC01_HDR',
            ];
    
           
           $wrapped_links["TABLES"] = $req_data; 
           $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $SalesChallan_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_SC  ?,?,?,?, ?,?,?,?, ?,?,?,?', $SalesChallan_cancel_data);

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
    
    $image_path         =   "docs/company".$CYID_REF."/SalesChallan";     
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
                
                echo $filenametostore ;

                if ($uploadedFile->isValid()) {

                    if(in_array($extension,$allow_extnesions)){
                        
                        if($filesize < $allow_size){

                            $custfilename = $destinationPath."/".$filenametostore;

                            if (!file_exists($custfilename)) {

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
        return redirect()->route("transaction",[43,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
    
      
   try {

        
         $sp_result = DB::select('EXEC SP_TRN_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);

   } catch (\Throwable $th) {
    
       return redirect()->route("transaction",[43,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("transaction",[43,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("transaction",[43,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("transaction",[43,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

    public function checksc(Request $request){

       
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $SCNO = $request->SCNO;
        
        $objSO = DB::table('TBL_TRN_SLSC01_HDR')
        ->where('TBL_TRN_SLSC01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLSC01_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_SLSC01_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_SLSC01_HDR.SCNO','=',$SCNO)
        ->select('TBL_TRN_SLSC01_HDR.SCID')
        ->first();
        
        if($objSO){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate SONO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }
	
	

    public function getStoreRateAvg(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $ITEMID_REF =   $request['ITEMID_REF'];
       
        $data       =   DB::select("SELECT 
        CASE WHEN SUM(CURRENT_QTY)='0.000' OR SUM(CURRENT_QTY*RATE)='0.00000000' THEN '0.00' ELSE
        (CASE WHEN SUM(CURRENT_QTY*RATE)='0.00000000' THEN 0 ELSE SUM(CURRENT_QTY*RATE) END) / (CASE WHEN SUM(CURRENT_QTY)='0.000' THEN 0 ELSE SUM(CURRENT_QTY) END) END AS AVG_RATE
        from TBL_MST_BATCH where  CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND ITEMID_REF = '$ITEMID_REF'");
    
        $TOTAL_AVG_RATE    =   isset($data[0]->AVG_RATE) && $data[0]->AVG_RATE !=''?$data[0]->AVG_RATE:'0.00';
    
        echo $TOTAL_AVG_RATE;
    
        exit();
    }

    public function getStoreId($SERIALNO,$SCID_REF){
        $data_array_id=[];
        $data_array_qty=[];
        $data_array_store=[];
        
            $data = DB::select("SELECT MS.BATCHID_REF,MS.DISPATCH_MAIN_QTY,S.NAME AS STORE_NAME
            FROM TBL_TRN_SLSC01_STORE MS
            LEFT JOIN TBL_MST_BATCH B ON B.BATCHID=MS.BATCHID_REF
            LEFT JOIN TBL_MST_STORE S ON S.STID=B.STID_REF
            WHERE MS.SERIALNO=$SERIALNO AND MS.SCID_REF=$SCID_REF");
            if(isset($data) && count($data) > 0){
                foreach($data as $key=>$val){
                    if(!in_array($val->STORE_NAME,$data_array_store)){

                    }
                    $data_array_id[]=$val->BATCHID_REF;
                    $data_array_qty[]=$val->DISPATCH_MAIN_QTY;
                    if(!in_array($val->STORE_NAME,$data_array_store)){
                    $data_array_store[]=$val->STORE_NAME ;
                    }
                } 
            }  
       

    $result =   array('BATCHID_REF'=>!empty($data_array_id)?implode(',',$data_array_id):'','QTY'=>!empty($data_array_qty)?implode(',',$data_array_qty):'','STORE_NAME'=>!empty($data_array_store)?implode(',',$data_array_store):'');
       return $result;
    }




    public function getSchemeData($SOID){
            $data = DB::select("SELECT TOP 1 SCHEMEID_REF FROM TBL_TRN_SLSO01_MAT 
            WHERE SOID_REF=$SOID");
            $result =    !empty($data) ? $data[0]->SCHEMEID_REF : ''; 
       return $result;
    }

    
    


    
    
}
