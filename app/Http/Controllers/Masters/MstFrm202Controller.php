<?php

namespace App\Http\Controllers\Masters;
use App\Helpers\Helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
// use App\Models\Master\TblMstFrm202;

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

class MstFrm202Controller extends Controller
{
    protected $form_id = 202;
    protected $vtid_ref   = 306;  //voucher type id
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
       
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');  
        $FormId     =   $this->form_id; 

        $objRights = DB::table('TBL_MST_USERROLMAP')
                        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                       // ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
                        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                        ->first();

        
        $objFinalAppr   =   DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO           =   "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList    =   DB::select("select hdr.*,
                            case when a.ACTIONNAME = '$FANO' then 'Final Approved' 
                            else case 
                            when a.ACTIONNAME = 'ADD' then 'Added'  
                            when a.ACTIONNAME = 'EDIT' then 'Edited'
                            when a.ACTIONNAME = 'APPROVAL1' then 'First Level Approved'
                            when a.ACTIONNAME = 'APPROVAL2' then 'Second Level Approved'
                            when a.ACTIONNAME = 'APPROVAL3' then 'Third Level Approved'
                            when a.ACTIONNAME = 'APPROVAL4' then 'Fourth Level Approved'
                            when a.ACTIONNAME = 'APPROVAL5' then 'Final Approved' 
                            when a.ACTIONNAME = 'CANCEL' then 'Cancelled'
                            end end as STATUS_DESC,
                            CONCAT(T2.ICODE,' - ',T2.NAME) AS ITEM_CODE_NAME
                            from TBL_MST_AUDITTRAIL a 
                            inner join TBL_MST_BOM_HDR hdr
                            on a.VID = hdr.BOMID 
                            
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF

                            LEFT JOIN TBL_MST_ITEM T2 ON hdr.ITEMID_REF=T2.ITEMID
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_MST_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.BOMID DESC ");
                             
                           
        
        return view('masters.Production.BillofMaterial.mstfrm202',compact(['objRights','FormId','objDataList']));        
    }
	
	public function ViewReport($request) {

        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
           
        $BOMID       =   $myValue['BOMID'];
        $Flag       =   $myValue['Flag'];

        // $objSalesOrder = DB::table('TBL_TRN_PROR01_HDR')
        // ->where('TBL_TRN_PROR01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        // ->where('TBL_TRN_PROR01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        // ->where('TBL_TRN_PROR01_HDR.POID','=',$POID)
        // ->select('TBL_TRN_PROR01_HDR.*')
        // ->first();
        
        
        $ssrs = new \SSRS\Report('http://103.139.58.23:8181//ReportServer/', array('username' => 'App', 'password' => 'admin@123'));   
        $result = $ssrs->loadReport('/ERP/BOMPrint');
		//$result = $ssrs->loadReport('/SSRS_Reports/POPrint -ZEP');
        
        $reportParameters = array(
            'BOMID' => $BOMID,
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
            $output = $ssrs->render('HTML4.0'); 
            echo $output;
        }
         
     }


	public function ViewReport_costing($request) {

        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
           
        $BOMID       =   $myValue['BOMID'];
        $Flag       =   $myValue['Flag'];

        // $objSalesOrder = DB::table('TBL_TRN_PROR01_HDR')
        // ->where('TBL_TRN_PROR01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        // ->where('TBL_TRN_PROR01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        // ->where('TBL_TRN_PROR01_HDR.POID','=',$POID)
        // ->select('TBL_TRN_PROR01_HDR.*')
        // ->first();
        
        
        $ssrs = new \SSRS\Report('http://103.139.58.23:8181//ReportServer/', array('username' => 'App', 'password' => 'admin@123'));   
        $result = $ssrs->loadReport('/ERP/BOM_Costing_Print');
		//$result = $ssrs->loadReport('/SSRS_Reports/POPrint -ZEP');
        
        $reportParameters = array(
            'BOMID' => $BOMID,
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
            $output = $ssrs->render('HTML4.0'); 
            echo $output;
        }
         
     }

    public function add(){       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

            
        $production_stage = DB::table('TBL_MST_PRODUCTIONSTAGES')
        ->where('STATUS','=',$Status)
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->select('TBL_MST_PRODUCTIONSTAGES.*')
        ->get();


        $component_list = DB::table('TBL_MST_COSTCOMPONENT')
        ->where('STATUS','=',$Status)
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->select('TBL_MST_COSTCOMPONENT.*')
        ->get();
        
        
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_BOM")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {$query->select('UDFBOMID')->from('TBL_MST_UDFFOR_BOM')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF)
                                                ->where('BRID_REF','=',$BRID_REF);
                                              //  ->where('FYID_REF','=',$FYID_REF);                       
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF);
                  //  ->where('FYID_REF','=',$FYID_REF) ; 

            
              
        $objUdfOSOData = DB::table('TBL_MST_UDFFOR_BOM')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
       //     ->where('FYID_REF','=',$FYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdfOSOData);

        $FormId = $this->form_id;

        $objDD = DB::table('TBL_MST_DOCNO_DEFINITION')
                ->where('VTID_REF','=',$this->vtid_ref)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
            //    ->where('FYID_REF','=',$FYID_REF)
                ->where('STATUS','=','A')
                ->select('TBL_MST_DOCNO_DEFINITION.*')
                ->first();

        //dump($objDD);
        $objDOCNO ='';
        if(!empty($objDD)){
            if($objDD->SYSTEM_GRSR == "1")
            {
                if($objDD->PREFIX_RQ == "1")
                {
                    $objDOCNO = $objDD->PREFIX;
                }        
                if($objDD->PRE_SEP_RQ == "1")
                {
                    if($objDD->PRE_SEP_SLASH == "1")
                    {
                    $objDOCNO = $objDOCNO.'/';
                    }
                    if($objDD->PRE_SEP_HYPEN == "1")
                    {
                    $objDOCNO = $objDOCNO.'-';
                    }
                }        
                if($objDD->NO_MAX)
                {   
                    $objDOCNO = $objDOCNO.str_pad($objDD->LAST_RECORDNO+1, $objDD->NO_MAX, "0", STR_PAD_LEFT);
                }
                
                if($objDD->NO_SEP_RQ == "1")
                {
                    if($objDD->NO_SEP_SLASH == "1")
                    {
                    $objDOCNO = $objDOCNO.'/';
                    }
                    if($objDD->NO_SEP_HYPEN == "1")
                    {
                    $objDOCNO = $objDOCNO.'-';
                    }
                }
                if($objDD->SUFFIX_RQ == "1")
                {
                    $objDOCNO = $objDOCNO.$objDD->SUFFIX;
                }
            }
        }

        $AlpsStatus =   $this->AlpsStatus();
        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        $objUOM      =   $this->GetUOM(); 
       
    return view('masters.Production.BillofMaterial.mstfrm202add', compact(['objUdfOSOData',
    'objCountUDF','production_stage','component_list','FormId','objDD','objDOCNO','AlpsStatus','TabSetting','objUOM']));       
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
    


    
    public function getItemDetails_new(Request $request){
        
        $Status = 'A';
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $StdCost = 0;
        $Taxid = [];
        $CODE = $request['CODE'];
        $NAME = $request['NAME'];
        $MUOM = $request['MUOM'];
        $GROUP = $request['GROUP'];
        $CTGRY = $request['CTGRY'];
        $BUNIT = $request['BUNIT'];
        $APART = $request['APART'];
        $CPART = $request['CPART'];
        $OPART = $request['OPART'];
        $PARTNO = $request['PARTNO'];
        $DRAWINGNO = $request['DRAWNO'];
        $MATERIAL_TYPE = NULL;

        $AlpsStatus =   $this->AlpsStatus();

        // $id = 53;
        // $ObjData = DB::select("SELECT  * FROM TBL_MST_STOCK_BATCH where ITEMID_REF=53");   
        // $CURRENTDATA = 0;
        // foreach ($ObjData as $index=>$dataRow){
        //     $CURRENTDATA += $dataRow->CURRENT_QTY;
        // }
        //dd($CURRENTDATA);
        $sp_popup = [
            $CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART,$PARTNO,$DRAWINGNO,$MATERIAL_TYPE
        ];
            
        $ObjItem = DB::select('EXEC sp_get_items_popup_enquiry2 ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);

            //dd($ObjItem);
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
                    $DRAWING_NO         =   isset($dataRow->DRAWINGNO)?$dataRow->DRAWINGNO:NULL;
                    $PART_NO            =   isset($dataRow->PARTNO)?$dataRow->PARTNO:NULL;
                    //$STOCK_IH            =   $CURRENTDATA;

                    $row = '';
                    $row = $row.'<tr  id="item_'.$ITEMID .'"  class="clsitemid"><td style="width:10%;text-align:center;"><input type="checkbox" id="chkId'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1"  ></td>';
                    $row = $row.'<td style="width:10%;">'.$ICODE;
                    $row = $row.'<input type="hidden" id="txtitem_'.$ITEMID.'" data-desc="'.$ICODE.'"  value="'.$ITEMID.'"/></td>
                    <td  style="width:8%;" id="itemname_'.$ITEMID.'" >'.$NAME;

                    // $row = $row.' <td style="width:8%;" id="itemstkihd_'.$ITEMID.'" >'.$STOCK_IH;
                    // $row = $row.'<input type="hidden" id="txtitemstkihd_'.$ITEMID.'" data-desc="'.$STOCK_IH.'"   value="'.$STOCK_IH.'"/></td>';
                    
                    $row = $row.' <td style="width:8%;" id="itempartno_'.$ITEMID.'" >'.$PART_NO;
                    $row = $row.'<input type="hidden" id="txtitempartno_'.$ITEMID.'" data-desc="'.$PART_NO.'"   value="'.$PART_NO.'"/></td>';
                    
                    $row = $row.'<input type="hidden" id="txtitemname_'.$ITEMID.'" data-desc="'.$ITEM_SPECI.'" value="'.$NAME.'"/></td>';
                    $row = $row.'<td style="width:8%;" id="itemuom_'.$ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ITEMID.'" data-desc="'.$Alt_UOM.'"
                    value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>';
                    $row = $row.'<td style="width:8%;" id="uomqty_'.$ITEMID.'" ><input type="hidden" id="txtuomqty_'.$ITEMID.'" data-desc="'.$TOQTY.'"
                    value="'.$ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                    $row = $row.'<td style="width:8%;" id="irate_'.$ITEMID.'"><input type="hidden" id="txtirate_'.$ITEMID.'" data-desc="'.$FROMQTY.'"
                    value="'.$STDCOST.'"/>'.$GroupName.'</td>';
                    $row = $row.'<td style="width:8%;" id="itax_'.$ITEMID.'"><input type="hidden" id="txtitax_'.$ITEMID.'" />'.$Categoryname.'</td>
                    <td style="width:8%;">'.$BusinessUnit.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                    <td hidden><input type="text" id="addinfoitem_'.$ITEMID .'"  data-desc101="'.$ALPS_PART_NO.'" data-desc102="'.$CUSTOMER_PART_NO.'" data-desc103="'.$OEM_PART_NO.'" ></td>
                    </tr>';
                    echo $row;   

                } 
                
                // return Response::json($ObjItem);
            }           
            else{
                echo '<tr><td colspan="12"> Record not found.</td></tr>';
            }
              
        //itmlimit
        exit();
    }

   
    public function getStockInhdDetails(Request $request){

        $ITEMID_REF             = $request['ITEMID_REF'];
        $MAIN_UOMID_REF         = $request['MAIN_UOMID_REF']; 

            $ObjItem = DB::table('TBL_MST_BATCH')
            ->where('ITEMID_REF','=',$ITEMID_REF)
            ->where('UOMID_REF','=',$MAIN_UOMID_REF)
            ->select('TBL_MST_BATCH.*')
            ->get();

            $STOCK_IN_HAND = 0;     
            foreach ($ObjItem as $index=>$dataRow){         
                $STOCK_IN_HAND +=$dataRow->OPENING_QTY + $dataRow->IN_QTY - $dataRow->OUT_QTY;                
              }             
                echo $STOCK_IN_HAND;   
            }    

            public function getItemDetails_prod_code(Request $request){   

                $taxstate = $request['taxstate'];
                $Status = "A";
                $CYID_REF = Auth::user()->CYID_REF;
                $BRID_REF = Session::get('BRID_REF');
                $FYID_REF = Session::get('FYID_REF');
                $StdCost = 0;
                $Taxid = [];
                $CODE = $request['CODE'];
                $NAME = $request['NAME'];
                $MUOM = $request['MUOM'];
                $GROUP = $request['GROUP'];
                $CTGRY = $request['CTGRY'];
                $BUNIT = $request['BUNIT'];
                $APART = $request['APART'];
                $CPART = $request['CPART'];
                $OPART = $request['OPART'];
                $PARTNO = $request['PARTNO'];
                $DRAWINGNO = $request['DRAWNO'];
                $MATERIAL_TYPE = NULL;
        
                $AlpsStatus =   $this->AlpsStatus();
        
                $sp_popup = [
                    $CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART,$PARTNO,$DRAWINGNO,$MATERIAL_TYPE
                ]; 
        
                
                    $ObjItem = DB::select('EXEC sp_get_items_popup_enquiry2 ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);
        
                    //dd($ObjItem); 
                    
                        if(!empty($ObjItem)){
        
                            foreach ($ObjItem as $index=>$dataRow){
        
                                $MATERIAL_TYPE            =   isset($dataRow->MATERIAL_TYPE)?$dataRow->MATERIAL_TYPE:NULL;
                                if($MATERIAL_TYPE=='FG-Finish Good' || $MATERIAL_TYPE=='SFG- Semi Finish Good')
                                {
        
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
                                    $DRAWING_NO         =   isset($dataRow->DRAWINGNO)?$dataRow->DRAWINGNO:NULL;
                                    $PART_NO            =   isset($dataRow->PARTNO)?$dataRow->PARTNO:NULL;
                                    
                                                             
        
                                    $row = '';
                                    $row .='<tr id="glidcode_'.$ITEMID.'" class="clsglid" >
                                            <td style="width:5%;text-align:center;" ><input type="checkbox" id="chkIdProdCode'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1ProdCode"  > </td>
                                            <td style="width:10%;">'.$ICODE.'
                                            <input type="hidden" id="txtglidcode_'.$ITEMID.'" data-code="'.$ICODE.'" data-uomno="'.$MAIN_UOMID_REF.'" data-name="'.$NAME.'" data-drawingno="'.$DRAWING_NO.'" data-partno="'.$PART_NO.'"    data-uom="'.$Main_UOM.'" 
                                            data-toqty="'.$TOQTY.'"
                                            value="'.$ITEMID.'"/>
                                            </td>
                                            <td style="width:15%;">'.$NAME.'</td>
                                            <td style="width:10%;">'.$Main_UOM.'</td>
                                            <td style="width:10%;">'.$BusinessUnit.'</td>
                                            <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                                            <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                                            <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                                            <td style="width:10%;">'.$DRAWING_NO.'</td>
                                            <td style="width:10%;">'.$PART_NO.'</td>
                                        </tr>';
                                
                                    echo $row;    
                                }
                            } 
                            
                            // return Response::json($ObjItem);
                        }           
                        else{
                            echo '<tr><td colspan="12"> Record not found.</td></tr>';
                        }
                exit();
            }

    //----------------------------------getItemDetails_prod_code end

    //----------------------------------getItemDetails_main_item
    public function getItemDetails_main_item(Request $request){   

        $taxstate = $request['taxstate'];
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $StdCost = 0;
        $Taxid = [];
        $CODE = $request['CODE'];
        $NAME = $request['NAME'];
        $MUOM = $request['MUOM'];
        $GROUP = $request['GROUP'];
        $CTGRY = $request['CTGRY'];
        $BUNIT = $request['BUNIT'];
        $APART = $request['APART'];
        $CPART = $request['CPART'];
        $OPART = $request['OPART'];
        $PARTNO = $request['PARTNO'];
        $DRAWINGNO = $request['DRAWNO'];
        $MATERIAL_TYPE = NULL;

        $AlpsStatus =   $this->AlpsStatus();

        $sp_popup = [
            $CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART,$PARTNO,$DRAWINGNO,$MATERIAL_TYPE
        ]; 

       // dd($sp_popup);
        
            $ObjItem = DB::select('EXEC sp_get_items_popup_enquiry2 ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);
            
                if(!empty($ObjItem)){

                    foreach ($ObjItem as $index=>$dataRow){

                        $MATERIAL_TYPE            =   isset($dataRow->MATERIAL_TYPE)?$dataRow->MATERIAL_TYPE:NULL;
                        //if($MATERIAL_TYPE=='FG-Finish Good' || $MATERIAL_TYPE=='SFG- Semi Finish Good'){
                           

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
                            $DRAWING_NO         =   isset($dataRow->DRAWINGNO)?$dataRow->DRAWINGNO:NULL;
                            $PART_NO            =   isset($dataRow->PARTNO)?$dataRow->PARTNO:NULL;
                            
                            
                            $row = '';
                            $row .='<tr id="mainitemidcodes_'.$ITEMID.'" class="mainitem_tab" >
                                    <td style="width:5%;text-align:center;" ><input type="checkbox" id="chkIdMainItem'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1MainItem"  > </td>
                                    <td style="width:10%;">'.$ICODE.'
                                    <input type="hidden" id="txtmainitemidcodes_'.$ITEMID.'" data-code="'.$ICODE.'" data-uomno="'.$MAIN_UOMID_REF.'" data-name="'.$NAME.'" data-drawingno="'.$DRAWING_NO.'" data-partno="'.$PART_NO.'"  data-uom="'.$Main_UOM.'" value="'.$ITEMID.'"/>
                                    <td style="width:15%;">'.$NAME.'</td>
                                    <td style="width:10%;">'.$Main_UOM.'</td>
                                    <td style="width:10%;">'.$BusinessUnit.'</td>
                                    <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                                    <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                                    <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                                    <td style="width:10%;">'.$DRAWING_NO.'</td>
                                    <td style="width:10%;">'.$PART_NO.'</td>
                                </tr>';
                        
                            echo $row;    
                        
                        //}
                    } 
                    
                    
                }           
                else{
                    echo '<tr><td colspan="12"> Record not found.</td></tr>';
                }
        exit();
    }

    //----------------------------------getItemDetails_main_item end

    //----------------------------------getItemDetails_subs_item
    public function getItemDetails_subs_item(Request $request){   

        $taxstate = $request['taxstate'];
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $StdCost = 0;
        $Taxid = [];
        $CODE = $request['CODE'];
        $NAME = $request['NAME'];
        $MUOM = $request['MUOM'];
        $GROUP = $request['GROUP'];
        $CTGRY = $request['CTGRY'];
        $BUNIT = $request['BUNIT'];
        $APART = $request['APART'];
        $CPART = $request['CPART'];
        $OPART = $request['OPART'];
        $PARTNO = $request['PARTNO'];
        $DRAWINGNO = $request['DRAWNO'];
        $MATERIAL_TYPE = NULL;

        $AlpsStatus =   $this->AlpsStatus();

        $sp_popup = [
            $CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART,$PARTNO,$DRAWINGNO,$MATERIAL_TYPE
        ]; 

              
            $ObjItem = DB::select('EXEC sp_get_items_popup_enquiry2 ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);
            
                if(!empty($ObjItem)){

                    foreach ($ObjItem as $index=>$dataRow){

                        $MATERIAL_TYPE            =   isset($dataRow->MATERIAL_TYPE)?$dataRow->MATERIAL_TYPE:NULL;
                        //if($MATERIAL_TYPE=='FG-Finish Good' || $MATERIAL_TYPE=='SFG- Semi Finish Good'){
                           

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
                            $DRAWING_NO         =   isset($dataRow->DRAWINGNO)?$dataRow->DRAWINGNO:NULL;
                            $PART_NO            =   isset($dataRow->PARTNO)?$dataRow->PARTNO:NULL;
                 

                            $row = '';
                            $row .='<tr id="mainitemidcode_substitute_'.$ITEMID.'" class="mainitem_tab1" >
                                    <td style="width:5%;text-align:center;" ><input type="checkbox" id="chkIdSubsItem'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1SubsItem"  > </td>
                                    <td style="width:10%;">'.$ICODE.'
                                    <input type="hidden" id="txtmainitemidcode_substitute_'.$ITEMID.'" data-code="'.$ICODE.'" data-uomno="'.$MAIN_UOMID_REF.'" data-name="'.$NAME.'" data-drawingno="'.$DRAWING_NO.'" data-partno="'.$PART_NO.'"  data-uom="'.$Main_UOM.'" value="'.$ITEMID.'"/>
                                    <td style="width:15%;">'.$NAME.'</td>
                                    <td style="width:10%;">'.$Main_UOM.'</td>
                                    <td style="width:10%;">'.$BusinessUnit.'</td>
                                    <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                                    <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                                    <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                                    <td style="width:10%;">'.$DRAWING_NO.'</td>
                                    <td style="width:10%;">'.$PART_NO.'</td>
                                </tr>';
                        
                            echo $row;    
                        
                        //}
                    } 
                    
                    
                }           
                else{
                    echo '<tr><td colspan="12"> Record not found.</td></tr>';
                }
        exit();
    }
    //----------------------------------getItemDetails_subs_item end

    //----------------------------------getItemDetails_by_pro
    public function getItemDetails_by_pro(Request $request){   

        $taxstate = $request['taxstate'];
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $StdCost = 0;
        $Taxid = [];
        $CODE = $request['CODE'];
        $NAME = $request['NAME'];
        $MUOM = $request['MUOM'];
        $GROUP = $request['GROUP'];
        $CTGRY = $request['CTGRY'];
        $BUNIT = $request['BUNIT'];
        $APART = $request['APART'];
        $CPART = $request['CPART'];
        $OPART = $request['OPART'];
        $PARTNO = $request['PARTNO'];
        $DRAWINGNO = $request['DRAWNO'];
        $MATERIAL_TYPE = NULL;

        $AlpsStatus =   $this->AlpsStatus();

        $sp_popup = [
            $CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART,$PARTNO,$DRAWINGNO,$MATERIAL_TYPE
        ]; 

                      
            $ObjItem = DB::select('EXEC sp_get_items_popup_enquiry2 ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);
            
                if(!empty($ObjItem)){

                    foreach ($ObjItem as $index=>$dataRow){

                        $MATERIAL_TYPE            =   isset($dataRow->MATERIAL_TYPE)?$dataRow->MATERIAL_TYPE:NULL;
                        //if($MATERIAL_TYPE=='FG-Finish Good' || $MATERIAL_TYPE=='SFG- Semi Finish Good'){
                           

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
                            $DRAWING_NO         =   isset($dataRow->DRAWINGNO)?$dataRow->DRAWINGNO:NULL;
                            $PART_NO            =   isset($dataRow->PARTNO)?$dataRow->PARTNO:NULL;
                 

                            $row = '';
                            $row .='<tr id="mainitemidcode_byproduct_'.$ITEMID.'" class="mainitem_tab2" >
                                    <td style="width:5%;text-align:center;" ><input type="checkbox" id="chkIdByProItem'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1ByProItem"  > </td>
                                    <td style="width:10%;">'.$ICODE.'
                                    <input type="hidden" id="txtmainitemidcode_byproduct_'.$ITEMID.'" data-code="'.$ICODE.'" data-uomno="'.$MAIN_UOMID_REF.'" data-name="'.$NAME.'" data-drawingno="'.$DRAWING_NO.'" data-partno="'.$PART_NO.'"  data-uom="'.$Main_UOM.'" value="'.$ITEMID.'"/>
                                    <td style="width:15%;">'.$NAME.'</td>
                                    <td style="width:10%;">'.$Main_UOM.'</td>
                                    <td style="width:10%;">'.$BusinessUnit.'</td>
                                    <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                                    <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                                    <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                                    <td style="width:10%;">'.$DRAWING_NO.'</td>
                                    <td style="width:10%;">'.$PART_NO.'</td>
                                </tr>';
                        
                            echo $row;    
                        
                        //}
                    } 
                    
                    
                }           
                else{
                    echo '<tr><td colspan="12"> Record not found.</td></tr>';
                }
        exit();
    }
    //----------------------------------getItemDetails_by_pro end
    

   //display attachments form
   public function attachment($id){

    if(!is_null($id))
    {
        $objOpenSalesOrder = DB::table("TBL_MST_BOM_HDR")
                        ->where('BOMID','=',$id)
                        ->select('TBL_MST_BOM_HDR.*')
                        ->first(); 

        $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
                    ->where('VTID','=',$this->vtid_ref)
                    ->select('VTID','VCODE','DESCRIPTIONS')
                    ->get()
                    ->toArray();
            
                    //uplaoded docs
                    $objAttachments = DB::table('TBL_MST_ATTACHMENT')                    
                        ->where('TBL_MST_ATTACHMENT.VTID_REF','=',$this->vtid_ref)
                        ->where('TBL_MST_ATTACHMENT.ATTACH_DOCNO','=',$id)
                        ->where('TBL_MST_ATTACHMENT.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_ATTACHMENT.BRID_REF','=',Session::get('BRID_REF'))
                      //  ->where('TBL_MST_ATTACHMENT.FYID_REF','=',Session::get('FYID_REF'))
                        ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                        ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                        ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                        ->get()->toArray();


            return view('masters.Production.BillofMaterial.mstfrm202attachment',compact(['objOpenSalesOrder','objMstVoucherType','objAttachments']));
    }

}

    
   public function save(Request $request) {


        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];
        $r_count5 = $request['Row_Count5'];
    
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]))
            {
                $req_data[$i] = [
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'UOMID_REF' => $request['UOMID_REF_'.$i],
                    'CONSUME_QTY' => $request['CONSUMEQTY_'.$i],
                    'LOSS_PRODUCTION_QTY' => (!empty($request['PRODUCTIONQTY_'.$i]) ? $request['PRODUCTIONQTY_'.$i] : 0),
                    'WASTEAGE_SCRAP_QTY' =>  (!empty($request['SCRAPQTY_'.$i]) ? $request['SCRAPQTY_'.$i] : 0),
                    'STOCK_IN_HAND'    => $request['ItemStockih_'.$i],  
					'REMARKS'    => $request['REMARKS_'.$i],  
                 
                ];
            }

            //dd($req_data);
        }
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);



        
        for ($i=0; $i<=$r_count2; $i++)
        {
                if(isset($request['MainItemId_Ref_'.$i]) && !is_null($request['MainItemId_Ref_'.$i]))
                {
                    if(isset($request['MainItemId_Ref_'.$i]))
                    {
                        $reqdata2[$i] = [
                          
                            'MAINITEMID_REF'    => $request['MainItemId_Ref_'.$i],
                            'SUBITEMID_REF'         => $request['MainItemId1_Ref_'.$i],
                            'UOMID_REF'         => $request['Mainuom_ref1_'.$i],
                            'CONSUME_QTY'         => (!empty($request['CONSUMEQTY1_'.$i]) ? $request['CONSUMEQTY1_'.$i] : 0),
                            'LOSS_PRODUCTION_QTY'  =>  (!empty($request['PRODUCTION1_'.$i]) ? $request['PRODUCTION1_'.$i] : 0),
                            'WASTEAGE_SCRAP_QTY'    =>   (!empty($request['SCRAP1_'.$i]) ? $request['SCRAP1_'.$i] : 0),
                      
                        ];
                    }
                }
            
        }

       
        if(isset($reqdata2))
        { 
            $wrapped_links2["SUB"] = $reqdata2;
            $XMLMAT2 = ArrayToXml::convert($wrapped_links2);
        }
        else
        {
            $XMLMAT2 = NULL; 
        }   
    
            
        for ($i=0; $i<=$r_count3; $i++)
        {
                if(isset($request['MainItemId2_Ref_'.$i]) && !is_null($request['MainItemId2_Ref_'.$i]))
                {
                    if(isset($request['MainItemId2_Ref_'.$i]))
                    {
                        $reqdata3[$i] = [
                          
                            'ITEMID_REF'    => $request['MainItemId2_Ref_'.$i],
                            'UOMID_REF'         => $request['Mainuom2_Ref_'.$i],
                            'PRODUCE_QTY'         =>(!empty($request['PRODUCE_QTY2_'.$i]) ? $request['PRODUCE_QTY2_'.$i] : 0),
                            'ALT_UOMID_REF'         => $request['PACKUOMID_REF_'.$i],
                            'TYPE'         => $request['TYPE_'.$i],

                      
                        ];
                    }
                }
            
        }
   
         


        if(isset($reqdata3))
        { 
            $wrapped_links3["BYP"] = $reqdata3;
            $XMLMAT3 = ArrayToXml::convert($wrapped_links3);
        }
        else
        {
            $XMLMAT3 = NULL; 
        }   
                
            for ($i=0; $i<=$r_count4; $i++)
            {
                    if(isset($request['UDFBOMID_REF_'.$i]) && !is_null($request['UDFBOMID_REF_'.$i]))
                    {
                        $reqdata4[$i] = [
                            'UDFBOMID_REF'   => $request['UDFBOMID_REF_'.$i],
                            'VALUE'      =>  (!empty($request['udfvalue_'.$i]) ? $request['udfvalue_'.$i] : ''),
                        ];
                    }
                
                
            }
        
        if(isset($reqdata4))
        { 
            $wrapped_links4["UDF"] = $reqdata4; 
            $XMLUDF = ArrayToXml::convert($wrapped_links4);
        }
        else
        {
            $XMLUDF = NULL; 
        }
 


        for ($i=0; $i<=$r_count5; $i++)
        {
                if(isset($request['Componentid_'.$i]) && !is_null($request['Componentid_'.$i]))
                {
                    if(isset($request['Componentid_'.$i]))
                    {
                        $reqdata5[$i] = [
                          
                            'CCOMPONENTID_REF'    => $request['Componentid_'.$i],
                            'VALUE'         => (!empty($request['value_'.$i]) ? $request['value_'.$i] : 0),
                      
                        ];
                    }
                }
            
        }
     
           if(isset($reqdata5))
           { 
            $wrapped_links5["OTH"] = $reqdata5;
            $XMLMAT4 = ArrayToXml::convert($wrapped_links5);
           }
           else
           {
            $XMLMAT4 = NULL; 
           }   

        
        
            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $BOMNO = strtoupper($request['BOMNO']);
            $BOM_DT = $request['BOM_DT'];
            $PRODUCT_CODE = $request['PRODUCT_CODE'];
            $DESCRIPTION = $request['DESCRIPTION'];
            $UOMID_REF = $request['UOMID_REF'];
            $PRODUCEQTY = $request['PRODUCEQTY'];
            $PRODUCTION_STAGE = $request['PRODUCTION_STAGE'];
            $DESIGNNO = $request['DESIGNNO'];
            $DRAWINGNO = $request['DRAWINGNO'];
            $REMARKS = $request['REMARKS'];
            $INSTRUCTION = $request['instruction'];
            $DEACTIVATED   =   NULL;  
            $DODEACTIVATED =   NULL;  
           

            $log_data = [ 
                $BOMNO,$BOM_DT,$PRODUCT_CODE,$UOMID_REF,$PRODUCEQTY,$PRODUCTION_STAGE,$DESIGNNO,$DRAWINGNO,$REMARKS,$DEACTIVATED
                ,$DODEACTIVATED,$INSTRUCTION,$CYID_REF, $BRID_REF,$FYID_REF,$XMLMAT,$XMLMAT2,$XMLMAT3,$XMLUDF,$XMLMAT4, $VTID_REF 
                ,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];

            //dd($log_data); 

            try {

                $sp_result = DB::select('EXEC SP_BOM_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);     
            
                } catch (\Throwable $th) {
                
                    return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
    
                }
        
                if(Str::contains(strtoupper($sp_result[0]->RESULT), 'SUCCESS')){
    
                    return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);
        
                }elseif(Str::contains(strtoupper($sp_result[0]->RESULT), 'DUPLICATE RECORD')){
                
                    return Response::json(['errors'=>true,'msg' => $sp_result[0]->RESULT,'exist'=>'duplicate']);
                    
                }else{
        
                    return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
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
            $objBOM = DB::table('TBL_MST_BOM_HDR')
                          //   ->where('TBL_MST_BOM_HDR.FYID_REF','=',Session::get('FYID_REF'))
                             ->where('TBL_MST_BOM_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_BOM_HDR.BRID_REF','=',Session::get('BRID_REF'))
                             ->leftJoin('TBL_MST_ITEM', 'TBL_MST_BOM_HDR.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                             ->leftJoin('TBL_MST_UOM', 'TBL_MST_BOM_HDR.UOMID_REF','=','TBL_MST_UOM.UOMID') 
                             ->leftJoin('TBL_MST_ITEM_UOMCONV', 'TBL_MST_BOM_HDR.ITEMID_REF','=','TBL_MST_ITEM_UOMCONV.ITEMID_REF')
                             ->leftJoin('TBL_MST_PRODUCTIONSTAGES', 'TBL_MST_BOM_HDR.PSTAGEID_REF','=','TBL_MST_PRODUCTIONSTAGES.PSTAGEID')   
                             ->where('TBL_MST_BOM_HDR.BOMID','=',$id)
                             ->select('TBL_MST_BOM_HDR.*','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.PARTNO','TBL_MST_ITEM.DRAWINGNO','TBL_MST_ITEM.MAIN_UOMID_REF','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','TBL_MST_PRODUCTIONSTAGES.PSTAGE_CODE','TBL_MST_PRODUCTIONSTAGES.DESCRIPTIONS as PSTAGE_NAME','TBL_MST_ITEM_UOMCONV.TO_QTY')
                             ->first();

            /* if(isset($objBOM->STATUS) && $objBOM->STATUS !=""){
                if(strtoupper($objBOM->STATUS)=="A" || strtoupper($objBOM->STATUS)=="C"){
                    exit("Sorry, Only Un Approved record can edit.");
                }
            } */
            
            
            $objOSOMAT = DB::table('TBL_MST_BOM_MAT')                    
                            ->where('TBL_MST_BOM_MAT.BOMID_REF','=',$id)
                            ->leftJoin('TBL_MST_ITEM', 'TBL_MST_BOM_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                            ->leftJoin('TBL_MST_UOM', 'TBL_MST_BOM_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                           
                            ->orderBy('TBL_MST_BOM_MAT.BOM_MATID','ASC')
                            ->select('TBL_MST_BOM_MAT.*','TBL_MST_ITEM.*','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS')
                            ->get()->toArray();

            $objCount1 = count($objOSOMAT);
          
                            

            $objSUB = DB::table('TBL_MST_BOM_SUB')                    
                             ->where('TBL_MST_BOM_SUB.BOMID_REF','=',$id)
                             ->leftJoin('TBL_MST_ITEM', 'TBL_MST_BOM_SUB.SUBITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                           ->leftJoin('TBL_MST_UOM', 'TBL_MST_BOM_SUB.UOMID_REF','=','TBL_MST_UOM.UOMID')    
                           ->leftJoin('TBL_MST_ITEM as table2', 'TBL_MST_BOM_SUB.MAINITEMID_REF','=','table2.ITEMID')                       
                             ->orderBy('TBL_MST_BOM_SUB.BOM_SUBID','ASC')
                             ->select('TBL_MST_BOM_SUB.*','TBL_MST_ITEM.*','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','table2.ICODE as ICODE2','table2.NAME as NAME2')
                             ->get()->toArray();

            $objCount2 = count($objSUB);
                      

            $objBYP = DB::table('TBL_MST_BOM_BYP')                    
                ->where('TBL_MST_BOM_BYP.BOMID_REF','=',$id)
                ->leftJoin('TBL_MST_ITEM', 'TBL_MST_BOM_BYP.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                ->leftJoin('TBL_MST_UOM', 'TBL_MST_BOM_BYP.UOMID_REF','=','TBL_MST_UOM.UOMID')                           
                ->leftJoin('TBL_MST_UOM AS ALTUOM', 'TBL_MST_BOM_BYP.ALT_UOMID_REF','=','ALTUOM.UOMID')                           
                ->orderBy('TBL_MST_BOM_BYP.BOM_BYPID','ASC')
                ->select('TBL_MST_BOM_BYP.*','TBL_MST_ITEM.*','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','ALTUOM.UOMCODE as UOMCODE1','ALTUOM.DESCRIPTIONS as DESCRIPTIONS1')
                ->get()->toArray();
               // dd($objBYP); 

            $objCount3 = count($objBYP);
      

            $objOSOUDF = DB::table('TBL_MST_BOM_UDF')                    
                             ->where('TBL_MST_BOM_UDF.BOMID_REF','=',$id)
                             ->leftJoin('TBL_MST_UDFFOR_BOM', 'TBL_MST_BOM_UDF.UDFBOMID_REF','=','TBL_MST_UDFFOR_BOM.UDFBOMID')     
                             ->select('TBL_MST_BOM_UDF.*','TBL_MST_UDFFOR_BOM.*')
                             ->orderBy('TBL_MST_BOM_UDF.BOM_UDFID','ASC')
                             ->get()->toArray();
            $objCount4 = count($objOSOUDF);           


            $objOTH = DB::table('TBL_MST_BOM_OTH')                    
                             ->where('TBL_MST_BOM_OTH.BOMID_REF','=',$id)
                             ->leftJoin('TBL_MST_COSTCOMPONENT', 'TBL_MST_BOM_OTH.CCOMPONENTID_REF','=','TBL_MST_COSTCOMPONENT.CCOMPONENTID')     
                             ->select('TBL_MST_BOM_OTH.*','TBL_MST_COSTCOMPONENT.*')
                             ->orderBy('TBL_MST_BOM_OTH.BOM_OTHID','ASC')
                             ->get()->toArray();
            $objCount5 = count($objOTH);  
                          
            
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                        //     ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
            

             $production_stage = DB::table('TBL_MST_PRODUCTIONSTAGES')
                             ->where('STATUS','=',$Status)
                             ->where('CYID_REF','=',Auth::user()->CYID_REF)
                             ->select('TBL_MST_PRODUCTIONSTAGES.*')
                             ->get();
                     
                     
            $component_list = DB::table('TBL_MST_COSTCOMPONENT')
                             ->where('STATUS','=',$Status)
                             ->where('CYID_REF','=',Auth::user()->CYID_REF)
                             ->select('TBL_MST_COSTCOMPONENT.*')
                             ->get();

    
             $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_BOM")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                    {       
                                    $query->select('UDFBOMID')->from('TBL_MST_UDFFOR_BOM')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF)
                                                    ->where('BRID_REF','=',$BRID_REF);
                                                  //  ->where('FYID_REF','=',$FYID_REF);                       
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF);
                    //    ->where('FYID_REF','=',$FYID_REF) ;                   
             

            $objUdfOSOData = DB::table('TBL_MST_UDFFOR_BOM')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
             //   ->where('FYID_REF','=',$FYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_BOM")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                        {       
                        $query->select('UDFBOMID')->from('TBL_MST_UDFFOR_BOM')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF)
                                        ->where('BRID_REF','=',$BRID_REF);
                                     //   ->where('FYID_REF','=',$FYID_REF);                       
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF);
         //   ->where('FYID_REF','=',$FYID_REF) ;                   
            

            $objUdfOSOData2 = DB::table('TBL_MST_UDFFOR_BOM')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
               // ->where('FYID_REF','=',$FYID_REF)
                ->union($ObjUnionUDF2)
                ->get()->toArray(); 
    
        $FormId = $this->form_id;

        $AlpsStatus =   $this->AlpsStatus();
        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        $InputStatus =   "";

        $objUOM      =   $this->GetUOM(); 
            
        return view('masters.Production.BillofMaterial.mstfrm202edit',compact(['objBOM','objRights',
           'objOSOMAT','objOSOUDF', 'objUdfOSOData','objUdfOSOData2','production_stage','component_list',
           'objSUB','objBYP','objOTH','FormId','objCount1','objCount2','objCount3','objCount4','objCount5','AlpsStatus','InputStatus','TabSetting','objUOM']));
        }
     
    }


    public function copy($id=NULL){
       
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){

            $objBOM = DB::table('TBL_MST_BOM_HDR')
                       //      ->where('TBL_MST_BOM_HDR.FYID_REF','=',Session::get('FYID_REF'))
                             ->where('TBL_MST_BOM_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_BOM_HDR.BRID_REF','=',Session::get('BRID_REF'))
                             ->leftJoin('TBL_MST_ITEM', 'TBL_MST_BOM_HDR.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                             ->leftJoin('TBL_MST_UOM', 'TBL_MST_BOM_HDR.UOMID_REF','=','TBL_MST_UOM.UOMID') 
                             ->leftJoin('TBL_MST_ITEM_UOMCONV', 'TBL_MST_BOM_HDR.ITEMID_REF','=','TBL_MST_ITEM_UOMCONV.ITEMID_REF')
                             ->leftJoin('TBL_MST_PRODUCTIONSTAGES', 'TBL_MST_BOM_HDR.PSTAGEID_REF','=','TBL_MST_PRODUCTIONSTAGES.PSTAGEID')   
                             ->where('TBL_MST_BOM_HDR.BOMID','=',$id)
                             ->select('TBL_MST_BOM_HDR.*','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.PARTNO','TBL_MST_ITEM.DRAWINGNO','TBL_MST_ITEM.MAIN_UOMID_REF','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','TBL_MST_PRODUCTIONSTAGES.PSTAGE_CODE','TBL_MST_PRODUCTIONSTAGES.DESCRIPTIONS as PSTAGE_NAME','TBL_MST_ITEM_UOMCONV.TO_QTY')
                             ->first();
                       // dd($objBOM);

                            
                            // TBL_MST_UOM.UOMID')

            $objOSOMAT = DB::table('TBL_MST_BOM_MAT')                    
                             ->where('TBL_MST_BOM_MAT.BOMID_REF','=',$id)
                             ->leftJoin('TBL_MST_ITEM', 'TBL_MST_BOM_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                             ->leftJoin('TBL_MST_UOM', 'TBL_MST_BOM_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                           
                             ->orderBy('TBL_MST_BOM_MAT.BOM_MATID','ASC')
                             ->select('TBL_MST_BOM_MAT.*','TBL_MST_ITEM.*','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS')
                             ->get()->toArray();
            $objCount1 = count($objOSOMAT);
            //dd($objOSOMAT);
                            

            $objSUB = DB::table('TBL_MST_BOM_SUB')                    
                             ->where('TBL_MST_BOM_SUB.BOMID_REF','=',$id)
                             ->leftJoin('TBL_MST_ITEM', 'TBL_MST_BOM_SUB.SUBITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                           ->leftJoin('TBL_MST_UOM', 'TBL_MST_BOM_SUB.UOMID_REF','=','TBL_MST_UOM.UOMID')    
                           ->leftJoin('TBL_MST_ITEM as table2', 'TBL_MST_BOM_SUB.MAINITEMID_REF','=','table2.ITEMID')                       
                             ->orderBy('TBL_MST_BOM_SUB.BOM_SUBID','ASC')
                             ->select('TBL_MST_BOM_SUB.*','TBL_MST_ITEM.*','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','table2.ICODE as ICODE2','table2.NAME as NAME2')
                             ->get()->toArray();
            $objCount2 = count($objSUB);
                           //  dd($objSUB); 

            $objBYP = DB::table('TBL_MST_BOM_BYP')                    
                           ->where('TBL_MST_BOM_BYP.BOMID_REF','=',$id)
                           ->leftJoin('TBL_MST_ITEM', 'TBL_MST_BOM_BYP.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                           ->leftJoin('TBL_MST_UOM', 'TBL_MST_BOM_BYP.UOMID_REF','=','TBL_MST_UOM.UOMID')                           
                           ->leftJoin('TBL_MST_UOM AS ALTUOM', 'TBL_MST_BOM_BYP.ALT_UOMID_REF','=','ALTUOM.UOMID')                           
                           ->orderBy('TBL_MST_BOM_BYP.BOM_BYPID','ASC')
                           ->select('TBL_MST_BOM_BYP.*','TBL_MST_ITEM.*','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','ALTUOM.UOMCODE as UOMCODE1','ALTUOM.DESCRIPTIONS as DESCRIPTIONS1')
                           ->get()->toArray();
            $objCount3 = count($objBYP);

     
      

            $objOSOUDF = DB::table('TBL_MST_BOM_UDF')                    
                             ->where('TBL_MST_BOM_UDF.BOMID_REF','=',$id)
                             ->leftJoin('TBL_MST_UDFFOR_BOM', 'TBL_MST_BOM_UDF.UDFBOMID_REF','=','TBL_MST_UDFFOR_BOM.UDFBOMID')     
                             ->select('TBL_MST_BOM_UDF.*','TBL_MST_UDFFOR_BOM.*')
                             ->orderBy('TBL_MST_BOM_UDF.BOM_UDFID','ASC')
                             ->get()->toArray();
            $objCount4 = count($objOSOUDF); 
                           


            $objOTH = DB::table('TBL_MST_BOM_OTH')                    
                             ->where('TBL_MST_BOM_OTH.BOMID_REF','=',$id)
                             ->leftJoin('TBL_MST_COSTCOMPONENT', 'TBL_MST_BOM_OTH.CCOMPONENTID_REF','=','TBL_MST_COSTCOMPONENT.CCOMPONENTID')     
                             ->select('TBL_MST_BOM_OTH.*','TBL_MST_COSTCOMPONENT.*')
                             ->orderBy('TBL_MST_BOM_OTH.BOM_OTHID','ASC')
                             ->get()->toArray();
            $objCount5 = count($objOTH);  

            
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                        //     ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
            

                            
                             $production_stage = DB::table('TBL_MST_PRODUCTIONSTAGES')
                             ->where('STATUS','=',$Status)
                             ->where('CYID_REF','=',Auth::user()->CYID_REF)
                             ->select('TBL_MST_PRODUCTIONSTAGES.*')
                             ->get();
                     
                     
                             $component_list = DB::table('TBL_MST_COSTCOMPONENT')
                             ->where('STATUS','=',$Status)
                             ->where('CYID_REF','=',Auth::user()->CYID_REF)
                             ->select('TBL_MST_COSTCOMPONENT.*')
                             ->get();
    
    
             $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_BOM")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                    {       
                                    $query->select('UDFBOMID')->from('TBL_MST_UDFFOR_BOM')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF)
                                                    ->where('BRID_REF','=',$BRID_REF);
                                                   // ->where('FYID_REF','=',$FYID_REF);                       
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF);
                       // ->where('FYID_REF','=',$FYID_REF) ;                   
             

            $objUdfOSOData = DB::table('TBL_MST_UDFFOR_BOM')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                //->where('FYID_REF','=',$FYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_BOM")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                        {       
                        $query->select('UDFBOMID')->from('TBL_MST_UDFFOR_BOM')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF)
                                        ->where('BRID_REF','=',$BRID_REF);
                                  //      ->where('FYID_REF','=',$FYID_REF);                       
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF);
         //   ->where('FYID_REF','=',$FYID_REF) ;                   
            

            $objUdfOSOData2 = DB::table('TBL_MST_UDFFOR_BOM')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
               // ->where('FYID_REF','=',$FYID_REF)
                ->union($ObjUnionUDF2)
                ->get()->toArray();     

            $FormId = $this->form_id;

            $objDD = DB::table('TBL_MST_DOCNO_DEFINITION')
                ->where('VTID_REF','=',$this->vtid_ref)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                //->where('FYID_REF','=',$FYID_REF)
                ->where('STATUS','=','A')
                ->select('TBL_MST_DOCNO_DEFINITION.*')
                ->first();

        $objDOCNO ='';
        if(!empty($objDD)){
            if($objDD->SYSTEM_GRSR == "1")
            {
                if($objDD->PREFIX_RQ == "1")
                {
                    $objDOCNO = $objDD->PREFIX;
                }        
                if($objDD->PRE_SEP_RQ == "1")
                {
                    if($objDD->PRE_SEP_SLASH == "1")
                    {
                    $objDOCNO = $objDOCNO.'/';
                    }
                    if($objDD->PRE_SEP_HYPEN == "1")
                    {
                    $objDOCNO = $objDOCNO.'-';
                    }
                }        
                if($objDD->NO_MAX)
                {   
                    $objDOCNO = $objDOCNO.str_pad($objDD->LAST_RECORDNO+1, $objDD->NO_MAX, "0", STR_PAD_LEFT);
                }
                
                if($objDD->NO_SEP_RQ == "1")
                {
                    if($objDD->NO_SEP_SLASH == "1")
                    {
                    $objDOCNO = $objDOCNO.'/';
                    }
                    if($objDD->NO_SEP_HYPEN == "1")
                    {
                    $objDOCNO = $objDOCNO.'-';
                    }
                }
                if($objDD->SUFFIX_RQ == "1")
                {
                    $objDOCNO = $objDOCNO.$objDD->SUFFIX;
                }
            }
        }

        $AlpsStatus =   $this->AlpsStatus();
        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
		$InputStatus =   "";

        $objUOM      =   $this->GetUOM(); 
            
        return view('masters.Production.BillofMaterial.mstfrm202copy',compact(['objBOM','objRights','InputStatus',
           'objOSOMAT','objOSOUDF', 'objUdfOSOData','objUdfOSOData2','production_stage','component_list',
           'objSUB','objBYP','objOTH','FormId','objCount1','objCount2','objCount3','objCount4','objCount5','objDD','objDOCNO','AlpsStatus','TabSetting','objUOM']));
        }
     
       }
     
    public function amendment($id=NULL){
       
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id))
        {
            $objBOM = DB::table('TBL_MST_BOM_HDR')
                         //    ->where('TBL_MST_BOM_HDR.FYID_REF','=',Session::get('FYID_REF'))
                             ->where('TBL_MST_BOM_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_BOM_HDR.BRID_REF','=',Session::get('BRID_REF'))
                             ->leftJoin('TBL_MST_ITEM', 'TBL_MST_BOM_HDR.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                             ->leftJoin('TBL_MST_UOM', 'TBL_MST_BOM_HDR.UOMID_REF','=','TBL_MST_UOM.UOMID') 
                             ->leftJoin('TBL_MST_ITEM_UOMCONV', 'TBL_MST_BOM_HDR.ITEMID_REF','=','TBL_MST_ITEM_UOMCONV.ITEMID_REF')
                             ->leftJoin('TBL_MST_PRODUCTIONSTAGES', 'TBL_MST_BOM_HDR.PSTAGEID_REF','=','TBL_MST_PRODUCTIONSTAGES.PSTAGEID')   
                             ->where('TBL_MST_BOM_HDR.BOMID','=',$id)
                             ->select('TBL_MST_BOM_HDR.*','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.PARTNO','TBL_MST_ITEM.DRAWINGNO','TBL_MST_ITEM.MAIN_UOMID_REF','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','TBL_MST_PRODUCTIONSTAGES.PSTAGE_CODE','TBL_MST_PRODUCTIONSTAGES.DESCRIPTIONS as PSTAGE_NAME','TBL_MST_ITEM_UOMCONV.TO_QTY')
                             ->first();
                       // dd($objBOM);


            $objOSOMAT = DB::table('TBL_MST_BOM_MAT')                    
                             ->where('TBL_MST_BOM_MAT.BOMID_REF','=',$id)
                             ->leftJoin('TBL_MST_ITEM', 'TBL_MST_BOM_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                             ->leftJoin('TBL_MST_UOM', 'TBL_MST_BOM_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                           
                             ->orderBy('TBL_MST_BOM_MAT.BOM_MATID','ASC')
                             ->select('TBL_MST_BOM_MAT.*','TBL_MST_ITEM.*','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS')
                             ->get()->toArray();
            //dd($objOSOMAT);
            $objCount1 = count($objOSOMAT);
                            

            $objSUB = DB::table('TBL_MST_BOM_SUB')                    
                             ->where('TBL_MST_BOM_SUB.BOMID_REF','=',$id)
                             ->leftJoin('TBL_MST_ITEM', 'TBL_MST_BOM_SUB.SUBITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                            ->leftJoin('TBL_MST_UOM', 'TBL_MST_BOM_SUB.UOMID_REF','=','TBL_MST_UOM.UOMID')    
                            ->leftJoin('TBL_MST_ITEM as table2', 'TBL_MST_BOM_SUB.MAINITEMID_REF','=','table2.ITEMID')                       
                             ->orderBy('TBL_MST_BOM_SUB.BOM_SUBID','ASC')
                             ->select('TBL_MST_BOM_SUB.*','TBL_MST_ITEM.*','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','table2.ICODE as ICODE2','table2.NAME as NAME2')
                             ->get()->toArray();

            // dd($objSUB); 
            $objCount2 = count($objSUB);

            $objBYP = DB::table('TBL_MST_BOM_BYP')                    
                        ->where('TBL_MST_BOM_BYP.BOMID_REF','=',$id)
                        ->leftJoin('TBL_MST_ITEM', 'TBL_MST_BOM_BYP.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                        ->leftJoin('TBL_MST_UOM', 'TBL_MST_BOM_BYP.UOMID_REF','=','TBL_MST_UOM.UOMID')                           
                        ->leftJoin('TBL_MST_UOM AS ALTUOM', 'TBL_MST_BOM_BYP.ALT_UOMID_REF','=','ALTUOM.UOMID')                           
                        ->orderBy('TBL_MST_BOM_BYP.BOM_BYPID','ASC')
                        ->select('TBL_MST_BOM_BYP.*','TBL_MST_ITEM.*','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','ALTUOM.UOMCODE as UOMCODE1','ALTUOM.DESCRIPTIONS as DESCRIPTIONS1')
                        ->get()->toArray();

            $objCount3 = count($objBYP);
        
      

            $objOSOUDF = DB::table('TBL_MST_BOM_UDF')                    
                             ->where('TBL_MST_BOM_UDF.BOMID_REF','=',$id)
                             ->leftJoin('TBL_MST_UDFFOR_BOM', 'TBL_MST_BOM_UDF.UDFBOMID_REF','=','TBL_MST_UDFFOR_BOM.UDFBOMID')     
                             ->select('TBL_MST_BOM_UDF.*','TBL_MST_UDFFOR_BOM.*')
                             ->orderBy('TBL_MST_BOM_UDF.BOM_UDFID','ASC')
                             ->get()->toArray();
            $objCount4 = count($objOSOUDF); 
                           


            $objOTH = DB::table('TBL_MST_BOM_OTH')                    
                             ->where('TBL_MST_BOM_OTH.BOMID_REF','=',$id)
                             ->leftJoin('TBL_MST_COSTCOMPONENT', 'TBL_MST_BOM_OTH.CCOMPONENTID_REF','=','TBL_MST_COSTCOMPONENT.CCOMPONENTID')     
                             ->select('TBL_MST_BOM_OTH.*','TBL_MST_COSTCOMPONENT.*')
                             ->orderBy('TBL_MST_BOM_OTH.BOM_OTHID','ASC')
                             ->get()->toArray();
            $objCount5 = count($objOTH);  

            
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                        //     ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
            

                           
                             $production_stage = DB::table('TBL_MST_PRODUCTIONSTAGES')
                             ->where('STATUS','=',$Status)
                             ->where('CYID_REF','=',Auth::user()->CYID_REF)
                             ->select('TBL_MST_PRODUCTIONSTAGES.*')
                             ->get();
                     
                     
                             $component_list = DB::table('TBL_MST_COSTCOMPONENT')
                             ->where('STATUS','=',$Status)
                             ->where('CYID_REF','=',Auth::user()->CYID_REF)
                             ->select('TBL_MST_COSTCOMPONENT.*')
                             ->get();
    
    
             $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_BOM")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                    {       
                                    $query->select('UDFBOMID')->from('TBL_MST_UDFFOR_BOM')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF)
                                                    ->where('BRID_REF','=',$BRID_REF);
                                                   // ->where('FYID_REF','=',$FYID_REF);                       
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF);
                   //     ->where('FYID_REF','=',$FYID_REF) ;                   
             

            $objUdfOSOData = DB::table('TBL_MST_UDFFOR_BOM')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                //->where('FYID_REF','=',$FYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_BOM")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                        {       
                        $query->select('UDFBOMID')->from('TBL_MST_UDFFOR_BOM')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF)
                                        ->where('BRID_REF','=',$BRID_REF);
                                      //  ->where('FYID_REF','=',$FYID_REF);                       
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF);
          //  ->where('FYID_REF','=',$FYID_REF) ;                   
            

            $objUdfOSOData2 = DB::table('TBL_MST_UDFFOR_BOM')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
          //      ->where('FYID_REF','=',$FYID_REF)
                ->union($ObjUnionUDF2)
                ->get()->toArray(); 

                $AFSA_MAX = DB::table('TBL_MST_BOM1_HDR')
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->where('BOMID_REF','=',$id)    
                ->max('ANO');
               
            
             $ANumber=$AFSA_MAX+1;

            $AlpsStatus =   $this->AlpsStatus();
            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
            $FormId     = $this->form_id;
			$InputStatus =   "";

            $objUOM      =   $this->GetUOM(); 
            return view('masters.Production.BillofMaterial.mstfrm202amendment',compact(['objBOM','objRights','InputStatus',
            'objOSOMAT','objOSOUDF', 'objUdfOSOData','objUdfOSOData2','production_stage','component_list',
            'objSUB','objBYP','objOTH','ANumber','FormId','objCount1','objCount2','objCount3','objCount4','objCount5','AlpsStatus','TabSetting','objUOM']));
            }
     
       }
     
       public function view($id=NULL){
       
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id))
        {
            $objBOM = DB::table('TBL_MST_BOM_HDR')
                        //     ->where('TBL_MST_BOM_HDR.FYID_REF','=',Session::get('FYID_REF'))
                             ->where('TBL_MST_BOM_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_BOM_HDR.BRID_REF','=',Session::get('BRID_REF'))
                             ->leftJoin('TBL_MST_ITEM', 'TBL_MST_BOM_HDR.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                             ->leftJoin('TBL_MST_UOM', 'TBL_MST_BOM_HDR.UOMID_REF','=','TBL_MST_UOM.UOMID') 
                             ->leftJoin('TBL_MST_ITEM_UOMCONV', 'TBL_MST_BOM_HDR.ITEMID_REF','=','TBL_MST_ITEM_UOMCONV.ITEMID_REF')
                             ->leftJoin('TBL_MST_PRODUCTIONSTAGES', 'TBL_MST_BOM_HDR.PSTAGEID_REF','=','TBL_MST_PRODUCTIONSTAGES.PSTAGEID')   
                             ->where('TBL_MST_BOM_HDR.BOMID','=',$id)
                             ->select('TBL_MST_BOM_HDR.*','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.PARTNO','TBL_MST_ITEM.DRAWINGNO','TBL_MST_ITEM.MAIN_UOMID_REF','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','TBL_MST_PRODUCTIONSTAGES.PSTAGE_CODE','TBL_MST_PRODUCTIONSTAGES.DESCRIPTIONS as PSTAGE_NAME','TBL_MST_ITEM_UOMCONV.TO_QTY')
                             ->first();

          
            
            $objOSOMAT = DB::table('TBL_MST_BOM_MAT')                    
                            ->where('TBL_MST_BOM_MAT.BOMID_REF','=',$id)
                            ->leftJoin('TBL_MST_ITEM', 'TBL_MST_BOM_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                            ->leftJoin('TBL_MST_UOM', 'TBL_MST_BOM_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                           
                            ->orderBy('TBL_MST_BOM_MAT.BOM_MATID','ASC')
                            ->select('TBL_MST_BOM_MAT.*','TBL_MST_ITEM.*','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS')
                            ->get()->toArray();

            $objCount1 = count($objOSOMAT);
          
                            

            $objSUB = DB::table('TBL_MST_BOM_SUB')                    
                             ->where('TBL_MST_BOM_SUB.BOMID_REF','=',$id)
                             ->leftJoin('TBL_MST_ITEM', 'TBL_MST_BOM_SUB.SUBITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                           ->leftJoin('TBL_MST_UOM', 'TBL_MST_BOM_SUB.UOMID_REF','=','TBL_MST_UOM.UOMID')    
                           ->leftJoin('TBL_MST_ITEM as table2', 'TBL_MST_BOM_SUB.MAINITEMID_REF','=','table2.ITEMID')                       
                             ->orderBy('TBL_MST_BOM_SUB.BOM_SUBID','ASC')
                             ->select('TBL_MST_BOM_SUB.*','TBL_MST_ITEM.*','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','table2.ICODE as ICODE2','table2.NAME as NAME2')
                             ->get()->toArray();

            $objCount2 = count($objSUB);
                      

            $objBYP = DB::table('TBL_MST_BOM_BYP')                    
                ->where('TBL_MST_BOM_BYP.BOMID_REF','=',$id)
                ->leftJoin('TBL_MST_ITEM', 'TBL_MST_BOM_BYP.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                ->leftJoin('TBL_MST_UOM', 'TBL_MST_BOM_BYP.UOMID_REF','=','TBL_MST_UOM.UOMID')                           
                ->leftJoin('TBL_MST_UOM AS ALTUOM', 'TBL_MST_BOM_BYP.ALT_UOMID_REF','=','ALTUOM.UOMID')                           
                ->orderBy('TBL_MST_BOM_BYP.BOM_BYPID','ASC')
                ->select('TBL_MST_BOM_BYP.*','TBL_MST_ITEM.*','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','ALTUOM.UOMCODE as UOMCODE1','ALTUOM.DESCRIPTIONS as DESCRIPTIONS1')
                ->get()->toArray();

            $objCount3 = count($objBYP);
      

            $objOSOUDF = DB::table('TBL_MST_BOM_UDF')                    
                             ->where('TBL_MST_BOM_UDF.BOMID_REF','=',$id)
                             ->leftJoin('TBL_MST_UDFFOR_BOM', 'TBL_MST_BOM_UDF.UDFBOMID_REF','=','TBL_MST_UDFFOR_BOM.UDFBOMID')     
                             ->select('TBL_MST_BOM_UDF.*','TBL_MST_UDFFOR_BOM.*')
                             ->orderBy('TBL_MST_BOM_UDF.BOM_UDFID','ASC')
                             ->get()->toArray();
            $objCount4 = count($objOSOUDF);           


            $objOTH = DB::table('TBL_MST_BOM_OTH')                    
                             ->where('TBL_MST_BOM_OTH.BOMID_REF','=',$id)
                             ->leftJoin('TBL_MST_COSTCOMPONENT', 'TBL_MST_BOM_OTH.CCOMPONENTID_REF','=','TBL_MST_COSTCOMPONENT.CCOMPONENTID')     
                             ->select('TBL_MST_BOM_OTH.*','TBL_MST_COSTCOMPONENT.*')
                             ->orderBy('TBL_MST_BOM_OTH.BOM_OTHID','ASC')
                             ->get()->toArray();
            $objCount5 = count($objOTH);  
                          
            
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                          //   ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
            

             $production_stage = DB::table('TBL_MST_PRODUCTIONSTAGES')
                             ->where('STATUS','=',$Status)
                             ->where('CYID_REF','=',Auth::user()->CYID_REF)
                             ->select('TBL_MST_PRODUCTIONSTAGES.*')
                             ->get();
                     
                     
            $component_list = DB::table('TBL_MST_COSTCOMPONENT')
                             ->where('STATUS','=',$Status)
                             ->where('CYID_REF','=',Auth::user()->CYID_REF)
                             ->select('TBL_MST_COSTCOMPONENT.*')
                             ->get();

    
             $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_BOM")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                    {       
                                    $query->select('UDFBOMID')->from('TBL_MST_UDFFOR_BOM')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF)
                                                    ->where('BRID_REF','=',$BRID_REF);
                                                  //  ->where('FYID_REF','=',$FYID_REF);                       
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF);
                   //     ->where('FYID_REF','=',$FYID_REF) ;                  
             

            $objUdfOSOData = DB::table('TBL_MST_UDFFOR_BOM')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
             //   ->where('FYID_REF','=',$FYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_BOM")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                        {       
                        $query->select('UDFBOMID')->from('TBL_MST_UDFFOR_BOM')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF)
                                        ->where('BRID_REF','=',$BRID_REF);
                                       // ->where('FYID_REF','=',$FYID_REF);                       
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF);
        //    ->where('FYID_REF','=',$FYID_REF) ;                   
            

            $objUdfOSOData2 = DB::table('TBL_MST_UDFFOR_BOM')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
             //   ->where('FYID_REF','=',$FYID_REF)
                ->union($ObjUnionUDF2)
                ->get()->toArray(); 
    
        $FormId = $this->form_id;
        $AlpsStatus =   $this->AlpsStatus();
        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        $InputStatus =   "disabled";
        $objUOM      =   $this->GetUOM(); 

        
            
        return view('masters.Production.BillofMaterial.mstfrm202view',compact(['objBOM','objRights',
           'objOSOMAT','objOSOUDF', 'objUdfOSOData','objUdfOSOData2','production_stage','component_list',
           'objSUB','objBYP','objOTH','FormId','objCount1','objCount2','objCount3','objCount4','objCount5','AlpsStatus','InputStatus','TabSetting','objUOM']));
        }
     
    }

    //update the data
   public function update(Request $request){

        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        $r_count4 = $request['Row_Count4'];
        $r_count5 = $request['Row_Count5'];    

    
    for ($i=0; $i<=$r_count1; $i++)
    {
        if(isset($request['ITEMID_REF_'.$i]))
        {
            $req_data[$i] = [
                'BOM_MATID'    => $request['BOM_MATID_'.$i],
                'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                'UOMID_REF' => $request['UOMID_REF_'.$i],
                'CONSUME_QTY' => $request['CONSUMEQTY_'.$i],
                'LOSS_PRODUCTION_QTY' => (!empty($request['PRODUCTIONQTY_'.$i]) ? $request['PRODUCTIONQTY_'.$i] : 0),
                'WASTEAGE_SCRAP_QTY' =>  (!empty($request['SCRAPQTY_'.$i]) ? $request['SCRAPQTY_'.$i] : 0),
                'STOCK_IN_HAND'    => $request['ItemStockih_'.$i], 
				'REMARKS'    => $request['REMARKS_'.$i],
             
            ];
        }
    }
        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);



    
    for ($i=0; $i<=$r_count2; $i++)
    {
            if(isset($request['MainItemId_Ref_'.$i]) && !is_null($request['MainItemId_Ref_'.$i]))
            {
                if(isset($request['MainItemId_Ref_'.$i]))
                {
                    $reqdata2[$i] = [
                      
                        'BOM_SUBID'    => $request['BOM_SUBID_'.$i],
                        'MAINITEMID_REF'    => $request['MainItemId_Ref_'.$i],
                        'SUBITEMID_REF'         => $request['MainItemId1_Ref_'.$i],
                        'UOMID_REF'         => $request['Mainuom_ref1_'.$i],
                        'CONSUME_QTY'         => (!empty($request['CONSUMEQTY1_'.$i]) ? $request['CONSUMEQTY1_'.$i] : 0),
                        'LOSS_PRODUCTION_QTY'  =>  (!empty($request['PRODUCTION1_'.$i]) ? $request['PRODUCTION1_'.$i] : 0),
                        'WASTEAGE_SCRAP_QTY'    =>   (!empty($request['SCRAP1_'.$i]) ? $request['SCRAP1_'.$i] : 0),
                  
                    ];
                }
            }
        
    }
       if(isset($reqdata2))
       { 
        $wrapped_links2["SUB"] = $reqdata2;
        $XMLMAT2 = ArrayToXml::convert($wrapped_links2);
       }
       else
       {
        $XMLMAT2 = NULL; 
       }   
 
        
    for ($i=0; $i<=$r_count3; $i++)
    {
            if(isset($request['MainItemId2_Ref_'.$i]) && !is_null($request['MainItemId2_Ref_'.$i]))
            {
                if(isset($request['MainItemId2_Ref_'.$i]))
                {
                    $reqdata3[$i] = [
                      
                        'BOM_BYPID'    => $request['BOM_BYPID_'.$i],
                        'ITEMID_REF'    => $request['MainItemId2_Ref_'.$i],
                        'UOMID_REF'         => $request['Mainuom2_Ref_'.$i],
                        'PRODUCE_QTY'         => (!empty($request['PRODUCE_QTY2_'.$i]) ? $request['PRODUCE_QTY2_'.$i] : 0),
                        'ALT_UOMID_REF'         => $request['PACKUOMID_REF_'.$i],
                        'TYPE'         => $request['TYPE_'.$i],
                        
                    ];
                }
            }
        
    }

    
       if(isset($reqdata3))
       { 
        $wrapped_links3["BYP"] = $reqdata3;
        $XMLMAT3 = ArrayToXml::convert($wrapped_links3);
       }
       else
       {
        $XMLMAT3 = NULL; 
       }   


       

            for ($i=0; $i<=$r_count4; $i++)
            {
                    if(isset($request['UDFBOMID_REF_'.$i]) && !is_null($request['UDFBOMID_REF_'.$i]))
                    {
                        $reqdata4[$i] = [
                            'BOM_UDFID'   => $request['BOM_UDFID_'.$i],
                            'UDFBOMID_REF'   => $request['UDFBOMID_REF_'.$i],
                            'VALUE'      => (!empty($request['udfvalue_'.$i]) ? $request['udfvalue_'.$i] : ''),
                        ];
                    }
                    
            }
       
    if(isset($reqdata4))
    { 
        $wrapped_links4["UDF"] = $reqdata4; 
        $XMLUDF = ArrayToXml::convert($wrapped_links4);
    }
    else
    {
        $XMLUDF = NULL; 
    }



    for ($i=0; $i<=$r_count5; $i++)
    {
            if(isset($request['Componentid_'.$i]) && !is_null($request['Componentid_'.$i]))
            {
                if(isset($request['Componentid_'.$i]))
                {
                    $reqdata5[$i] = [
                      
                        'BOM_OTHID'    => $request['BOM_OTHID'.$i],
                        'CCOMPONENTID_REF'    => $request['Componentid_'.$i],
                        'VALUE'         => (!empty($request['value_'.$i]) ? $request['value_'.$i] : 0),
                        
                    ];
                }
            }
        
    }
 
       if(isset($reqdata5))
       { 
        $wrapped_links5["OTH"] = $reqdata5;
        $XMLMAT4 = ArrayToXml::convert($wrapped_links5);
       }
       else
       {
        $XMLMAT4 = NULL; 
       }   


       $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       $newDateString = NULL;

       $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

       if(!is_null($newdt) ){
           
           $newdt = str_replace( "/", "-",  $newdt ) ;  

           $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
       }
       

       $DODEACTIVATED = $newDateString;

   
       $VTID_REF     =   $this->vtid_ref;
       $VID = 0;
       $USERID = Auth::user()->USERID;   
       $ACTIONNAME = 'EDIT';
       $IPADDRESS = $request->getClientIp();
       $CYID_REF = Auth::user()->CYID_REF;
       $BRID_REF = Session::get('BRID_REF');
       $FYID_REF = Session::get('FYID_REF');
       $BOMNO = strtoupper($request['BOMNO']);
       $BOM_DT = $request['BOM_DT'];
       $PRODUCT_CODE = $request['PRODUCT_CODE'];
       $DESCRIPTION = $request['DESCRIPTION'];
       $UOMID_REF = $request['UOMID_REF'];
       $PRODUCEQTY = $request['PRODUCEQTY'];
       $PRODUCTION_STAGE = $request['PRODUCTION_STAGE'];
       $DESIGNNO = $request['DESIGNNO'];
       $DRAWINGNO = $request['DRAWINGNO'];
       $REMARKS = $request['REMARKS'];
       $INSTRUCTION = $request['instruction'];
 
      

       $log_data = [ 
           $BOMNO,$BOM_DT,$PRODUCT_CODE,$UOMID_REF,$PRODUCEQTY,$PRODUCTION_STAGE,$DESIGNNO,$DRAWINGNO,$REMARKS,$DEACTIVATED
           ,$DODEACTIVATED,$INSTRUCTION,$CYID_REF, $BRID_REF,$FYID_REF,$XMLMAT,$XMLMAT2,$XMLMAT3,$XMLUDF,$XMLMAT4, $VTID_REF 
           ,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
       ];

 

             
       $sp_result = DB::select('EXEC SP_BOM_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);      

      
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
        }
        exit();  
    }
   public function saveamendment(Request $request){

            $r_count1 = $request['Row_Count1'];
            $r_count2 = $request['Row_Count2'];
            $r_count3 = $request['Row_Count3'];
            $r_count4 = $request['Row_Count4'];
            $r_count5 = $request['Row_Count5'];
            
    
            for ($i=0; $i<=$r_count1; $i++)
            {
                if(isset($request['ITEMID_REF_'.$i]))
                {
                    $req_data[$i] = [
                        'BOM_MATID'    => $request['BOM_MATID_'.$i],
                        'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                        'UOMID_REF' => $request['UOMID_REF_'.$i],
                        'CONSUME_QTY' => $request['CONSUMEQTY_'.$i],
                        'LOSS_PRODUCTION_QTY' => (!empty($request['PRODUCTIONQTY_'.$i]) ? $request['PRODUCTIONQTY_'.$i] : 0),
                        'WASTEAGE_SCRAP_QTY' =>  (!empty($request['SCRAPQTY_'.$i]) ? $request['SCRAPQTY_'.$i] : 0),
						'STOCK_IN_HAND'    => $request['ItemStockih_'.$i], 
						'REMARKS'    => $request['REMARKS_'.$i],
                    ];
                }
            }
        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);



    
    for ($i=0; $i<=$r_count2; $i++)
    {
            if(isset($request['MainItemId_Ref_'.$i]) && !is_null($request['MainItemId_Ref_'.$i]))
            {
                if(isset($request['MainItemId_Ref_'.$i]))
                {
                    $reqdata2[$i] = [
                      
                        'BOM_SUBID'    => $request['BOM_SUBID_'.$i],
                        'MAINITEMID_REF'    => $request['MainItemId_Ref_'.$i],
                        'SUBITEMID_REF'         => $request['MainItemId1_Ref_'.$i],
                        'UOMID_REF'         => $request['Mainuom_ref1_'.$i],
                        'CONSUME_QTY'         => (!empty($request['CONSUMEQTY1_'.$i]) ? $request['CONSUMEQTY1_'.$i] : 0),
                        'LOSS_PRODUCTION_QTY'  =>  (!empty($request['PRODUCTION1_'.$i]) ? $request['PRODUCTION1_'.$i] : 0),
                        'WASTEAGE_SCRAP_QTY'    =>   (!empty($request['SCRAP1_'.$i]) ? $request['SCRAP1_'.$i] : 0),
                  
                    ];
                }
            }
        
    }
       if(isset($reqdata2))
       { 
        $wrapped_links2["SUB"] = $reqdata2;
        $XMLMAT2 = ArrayToXml::convert($wrapped_links2);
       }
       else
       {
        $XMLMAT2 = NULL; 
       }   
  
        
    for ($i=0; $i<=$r_count3; $i++)
    {
            if(isset($request['MainItemId2_Ref_'.$i]) && !is_null($request['MainItemId2_Ref_'.$i]))
            {
                if(isset($request['MainItemId2_Ref_'.$i]))
                {
                    $reqdata3[$i] = [
                      
                        'BOM_BYPID'    => $request['BOM_BYPID_'.$i],
                        'ITEMID_REF'    => $request['MainItemId2_Ref_'.$i],
                        'UOMID_REF'         => $request['Mainuom2_Ref_'.$i],
                        'PRODUCE_QTY'         =>  (!empty($request['PRODUCE_QTY2_'.$i]) ? $request['PRODUCE_QTY2_'.$i] : 0),
                        'ALT_UOMID_REF'         => $request['PACKUOMID_REF_'.$i],
                        'TYPE'         => $request['TYPE_'.$i],

                       
                    ];
                }
            }
        
    }


       if(isset($reqdata3))
       { 
        $wrapped_links3["BYP"] = $reqdata3;
        $XMLMAT3 = ArrayToXml::convert($wrapped_links3);
       }
       else
       {
        $XMLMAT3 = NULL; 
       }   
        
       
            for ($i=0; $i<=$r_count4; $i++)
            {
                    if(isset($request['UDFBOMID_REF_'.$i]) && !is_null($request['UDFBOMID_REF_'.$i]))
                    {
                        $reqdata4[$i] = [
                            'BOM_UDFID'   => $request['BOM_UDFID_'.$i],
                            'UDFBOMID_REF'   => $request['UDFBOMID_REF_'.$i],
                            'VALUE'      =>  (!empty($request['udfvalue_'.$i]) ? $request['udfvalue_'.$i] : ''),
                        
                        ];
                    }
                
            }
       
        if(isset($reqdata4))
        { 
            $wrapped_links4["UDF"] = $reqdata4; 
            $XMLUDF = ArrayToXml::convert($wrapped_links4);
        }
        else
        {
            $XMLUDF = NULL; 
        }



    for ($i=0; $i<=$r_count5; $i++)
    {
            if(isset($request['Componentid_'.$i]) && !is_null($request['Componentid_'.$i]))
            {
                if(isset($request['Componentid_'.$i]))
                {
                    $reqdata5[$i] = [
                      
                        'BOM_OTHID'    => $request['BOM_OTHID'.$i],
                        'CCOMPONENTID_REF'    => $request['Componentid_'.$i],
                        'VALUE'         =>  (!empty($request['value_'.$i]) ? $request['value_'.$i] : 0),
                   
                       
                  
                    ];
                }
            }
        
    }
 
       if(isset($reqdata5))
       { 
        $wrapped_links5["OTH"] = $reqdata5;
        $XMLMAT4 = ArrayToXml::convert($wrapped_links5);
       }
       else
       {
        $XMLMAT4 = NULL; 
       }   




       $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
       $newDateString = NULL;

       $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

       if(!is_null($newdt) ){
           
           $newdt = str_replace( "/", "-",  $newdt ) ;  

           $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
       }
       

       $DODEACTIVATED = $newDateString;




   
       $VTID_REF     =   $this->vtid_ref;
       $VID = 0;
       $USERID = Auth::user()->USERID;   
       $ACTIONNAME = 'EDIT';
       $IPADDRESS = $request->getClientIp();
       $CYID_REF = Auth::user()->CYID_REF;
       $BRID_REF = Session::get('BRID_REF');
       $FYID_REF = Session::get('FYID_REF');
       $BOMNO = strtoupper($request['BOMNO']);
       $BOM_DT = $request['BOM_DT'];
       $PRODUCT_CODE = $request['PRODUCT_CODE'];
       $DESCRIPTION = $request['DESCRIPTION'];
       $UOMID_REF = $request['UOMID_REF'];
       $PRODUCEQTY = $request['PRODUCEQTY'];
       $PRODUCTION_STAGE = $request['PRODUCTION_STAGE'];
       $DESIGNNO = $request['DESIGNNO'];
       $DRAWINGNO = $request['DRAWINGNO'];
       $REMARKS = $request['REMARKS'];
       $INSTRUCTION = $request['instruction'];
       $BOMID_REF = $request['BOMID_REF'];
       $BOMADT = $request['BOMADT'];
       $REASON_BOMA = $request['REASON_BOMA'];
 
      

       $log_data = [ 
           $BOMID_REF,$BOMNO,$BOM_DT,$BOMADT,$PRODUCT_CODE,$UOMID_REF,$PRODUCEQTY,$PRODUCTION_STAGE,$DESIGNNO,$DRAWINGNO,$REMARKS,$REASON_BOMA,$DEACTIVATED
           ,$DODEACTIVATED,$INSTRUCTION,$CYID_REF, $BRID_REF,$FYID_REF,$XMLMAT,$XMLMAT2,$XMLMAT3,$XMLUDF,$XMLMAT4, $VTID_REF 
           ,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
       ];

       $sp_result = DB::select('EXEC SP_BOM_AMEN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);      

        
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
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
                    $r_count5 = $request['Row_Count5'];


                    for ($i=0; $i<=$r_count1; $i++)
                    {
                    if(isset($request['ITEMID_REF_'.$i]))
                    {
                        $req_data[$i] = [
                            'BOM_MATID'    => $request['BOM_MATID_'.$i],
                            'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                            'UOMID_REF' => $request['UOMID_REF_'.$i],
                            'CONSUME_QTY' => $request['CONSUMEQTY_'.$i],
                            'LOSS_PRODUCTION_QTY' => (!empty($request['PRODUCTIONQTY_'.$i]) ? $request['PRODUCTIONQTY_'.$i] : 0),
                            'WASTEAGE_SCRAP_QTY' =>  (!empty($request['SCRAPQTY_'.$i]) ? $request['SCRAPQTY_'.$i] : 0),
                            'STOCK_IN_HAND'    => $request['ItemStockih_'.$i], 
							'REMARKS'    => $request['REMARKS_'.$i],
                        
                        ];
                    }
                    }
                    $wrapped_links["MAT"] = $req_data; 
                    $XMLMAT = ArrayToXml::convert($wrapped_links);




                    for ($i=0; $i<=$r_count2; $i++)
                    {
                        if(isset($request['MainItemId_Ref_'.$i]) && !is_null($request['MainItemId_Ref_'.$i]))
                        {
                            if(isset($request['MainItemId_Ref_'.$i]))
                            {
                                $reqdata2[$i] = [
                                
                                    'BOM_SUBID'    => $request['BOM_SUBID_'.$i],
                                    'MAINITEMID_REF'    => $request['MainItemId_Ref_'.$i],
                                    'SUBITEMID_REF'         => $request['MainItemId1_Ref_'.$i],
                                    'UOMID_REF'         => $request['Mainuom_ref1_'.$i],
                                    'CONSUME_QTY'         => (!empty($request['CONSUMEQTY1_'.$i]) ? $request['CONSUMEQTY1_'.$i] : 0),
                                    'LOSS_PRODUCTION_QTY'  =>  (!empty($request['PRODUCTION1_'.$i]) ? $request['PRODUCTION1_'.$i] : 0),
                                    'WASTEAGE_SCRAP_QTY'    =>   (!empty($request['SCRAP1_'.$i]) ? $request['SCRAP1_'.$i] : 0),
                            
                                ];
                            }
                        }

                    }
                    if(isset($reqdata2))
                    { 
                    $wrapped_links2["SUB"] = $reqdata2;
                    $XMLMAT2 = ArrayToXml::convert($wrapped_links2);
                    }
                    else
                    {
                    $XMLMAT2 = NULL; 
                    }   


                    for ($i=0; $i<=$r_count3; $i++)
                    {
                        if(isset($request['MainItemId2_Ref_'.$i]) && !is_null($request['MainItemId2_Ref_'.$i]))
                        {
                            if(isset($request['MainItemId2_Ref_'.$i]))
                            {
                                $reqdata3[$i] = [
                                
                                    'BOM_BYPID'    => $request['BOM_BYPID_'.$i],
                                    'ITEMID_REF'    => $request['MainItemId2_Ref_'.$i],
                                    'UOMID_REF'         => $request['Mainuom2_Ref_'.$i],
                                    'PRODUCE_QTY'         =>  (!empty($request['PRODUCE_QTY2_'.$i]) ? $request['PRODUCE_QTY2_'.$i] : 0),
                                    'ALT_UOMID_REF'         => $request['PACKUOMID_REF_'.$i],
                                    'TYPE'         => $request['TYPE_'.$i],
                                   
                            
                                ];
                            }
                        }

                    }

                    if(isset($reqdata3))
                    { 
                        $wrapped_links3["BYP"] = $reqdata3;
                        $XMLMAT3 = ArrayToXml::convert($wrapped_links3);
                    }
                    else
                    {
                        $XMLMAT3 = NULL; 
                    }   
                   

                    for ($i=0; $i<=$r_count4; $i++)
                    {
                        if(isset($request['UDFBOMID_REF_'.$i]) && !is_null($request['UDFBOMID_REF_'.$i]))
                        {
                            $reqdata4[$i] = [
                                'BOM_UDFID'   => $request['BOM_UDFID_'.$i],
                                'UDFBOMID_REF'   => $request['UDFBOMID_REF_'.$i],
                                'VALUE'      => (!empty($request['udfvalue_'.$i]) ? $request['udfvalue_'.$i] : ''),
                                
                            ];
                        }

                    }
                    
                    if(isset($reqdata4))
                    { 
                    $wrapped_links4["UDF"] = $reqdata4; 
                    $XMLUDF = ArrayToXml::convert($wrapped_links4);
                    }
                    else
                    {
                    $XMLUDF = NULL; 
                    }



                    for ($i=0; $i<=$r_count5; $i++)
                    {
                        if(isset($request['Componentid_'.$i]) && !is_null($request['Componentid_'.$i]))
                        {
                            if(isset($request['Componentid_'.$i]))
                            {
                                $reqdata5[$i] = [
                                
                                    'BOM_OTHID'    => $request['BOM_OTHID'.$i],
                                    'CCOMPONENTID_REF'    => $request['Componentid_'.$i],
                                    'VALUE'         =>    (!empty($request['value_'.$i]) ? $request['value_'.$i] : 0),
                                 
                                ];
                            }
                        }

                    }

                    if(isset($reqdata5))
                    { 
                    $wrapped_links5["OTH"] = $reqdata5;
                    $XMLMAT4 = ArrayToXml::convert($wrapped_links5);
                    }
                    else
                    {
                    $XMLMAT4 = NULL; 
                    }   




                    $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;

                    $newDateString = NULL;

                    $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

                    if(!is_null($newdt) ){
                    
                    $newdt = str_replace( "/", "-",  $newdt ) ;  

                    $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
                    }


                    $DODEACTIVATED = $newDateString;

                    $VTID_REF     =   $this->vtid_ref;
                    $VID = 0;
                    $USERID = Auth::user()->USERID;   
                    $ACTIONNAME = $Approvallevel;
                    $IPADDRESS = $request->getClientIp();
                    $CYID_REF = Auth::user()->CYID_REF;
                    $BRID_REF = Session::get('BRID_REF');
                    $FYID_REF = Session::get('FYID_REF');
                    $BOMNO = strtoupper($request['BOMNO']);
                    $BOM_DT = $request['BOM_DT'];
                    $PRODUCT_CODE = $request['PRODUCT_CODE'];
                    $DESCRIPTION = $request['DESCRIPTION'];
                    $UOMID_REF = $request['UOMID_REF'];
                    $PRODUCEQTY = $request['PRODUCEQTY'];
                    $PRODUCTION_STAGE = $request['PRODUCTION_STAGE'];
                    $DESIGNNO = $request['DESIGNNO'];
                    $DRAWINGNO = $request['DRAWINGNO'];
                    $REMARKS = $request['REMARKS'];
                    $INSTRUCTION = $request['instruction'];



                    $log_data = [ 
                    $BOMNO,$BOM_DT,$PRODUCT_CODE,$UOMID_REF,$PRODUCEQTY,$PRODUCTION_STAGE,$DESIGNNO,$DRAWINGNO,$REMARKS,$DEACTIVATED
                    ,$DODEACTIVATED,$INSTRUCTION,$CYID_REF, $BRID_REF,$FYID_REF,$XMLMAT,$XMLMAT2,$XMLMAT3,$XMLUDF,$XMLMAT4, $VTID_REF 
                    ,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
                    ];


                $sp_result = DB::select('EXEC SP_BOM_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);      

                if($sp_result[0]->RESULT=="SUCCESS"){
        
                    return Response::json(['success' =>true,'msg' => 'Record successfully approved.']);
        
                }else{
                    return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
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
                $TABLE      =   "TBL_MST_BOM_HDR";
                $FIELD      =   "BOMID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            

            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_MST_MULTIAPPROVAL ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
    
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
    // dd($request->{0});  

   //save data
        $id = $request->{0};


        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_BOM_HDR";
        $FIELD      =   "BOMID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

       

        $req_data[0]=[
            'NT'  => 'TBL_MST_BOM_HDR',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_MST_BOM_MAT',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_MST_BOM_SUB',
        ];
        $req_data[3]=[
            'NT'  => 'TBL_MST_BOM_BYP',
        ];
        $req_data[4]=[
            'NT'  => 'TBL_MST_BOM_UDF',
        ];
        $req_data[5]=[
            'NT'  => 'TBL_MST_BOM_OTH',
        ];
        $req_data[6]=[
            'NT'  => 'TBL_MST_BOM1_HDR',
        ];
  
        // $req_data[2]=[
        //     'NT'  => 'TBL_MST_PRICELIST_MAT',
        // ];
   
            
      
        $wrapped_links["TABLES"] = $req_data; 
        
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
     

        $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$XMLTAB];
       // dd($mst_cancel_data);
        $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);
      
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
    
   //$destinationPath = storage_path()."/docs/company".$CYID_REF."/BillOfMaterial";
    $image_path         =   "docs/company".$CYID_REF."/BillOfMaterial";     
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
        return redirect()->route("master",[202,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
         $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);

   } catch (\Throwable $th) {
    
       return redirect()->route("master",[202,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("master",[202,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("master",[202,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("master",[202,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

    public function checkbomno(Request $request){

        // dd($request->LABEL_0);
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $BOMNO = $request->BOMNO;
        
        $objSO = DB::table('TBL_MST_BOM_HDR')
        ->where('TBL_MST_BOM_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_BOM_HDR.BRID_REF','=',Session::get('BRID_REF'))
     //   ->where('TBL_MST_BOM_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_MST_BOM_HDR.BOM_NO','=',$BOMNO)
        ->select('TBL_MST_BOM_HDR.BOMID')
        ->first();
        
        if($objSO){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate BOMNO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }

    public function getExistProductCode(Request $request){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $ITEMID_REF     =   trim($request->ITEMID_REF);
        $BOMID          =   trim($request->BOMID);

        if($BOMID ==''){
            $TOTAL_ROW      =   DB::table('TBL_MST_BOM_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('ITEMID_REF','=',$ITEMID_REF)
			->where('STATUS','=','A')
            ->count();
        }
        else{
            $TOTAL_ROW      =   DB::table('TBL_MST_BOM_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('ITEMID_REF','=',$ITEMID_REF)
            ->where('BOMID','!=',$BOMID)
			->where('STATUS','=','A')
            ->count();
        }

        echo intval($TOTAL_ROW);die;
        
    }

    public function importdata(){
        $objMstVoucherType  =   DB::table("TBL_MST_VOUCHERTYPE")
                                ->where('VTID','=',$this->vtid_ref)
								->where('STATUS','=','A')
                                ->select('VTID','VCODE','DESCRIPTIONS','INDATE')
                                ->get()
                                ->toArray();

        return view('masters.Production.BillofMaterial.mstfrm202importexcel',compact(['objMstVoucherType']));
    }
    
    public function importexcelindb(Request $request){
        ini_set('memory_limit', '-1');

        $formData           =   $request->all();                
        $allow_extnesions   =   explode(",",$formData["allow_extensions"]);
        $allow_size         =   (int)$formData["allow_max_size"] * 1024 * 1024;

        $VTID_REF           =   $this->vtid_ref;
        $USERID             =   Auth::user()->USERID;   
        $CYID_REF           =   Auth::user()->CYID_REF; 
        $BRID_REF           =   Session::get('BRID_REF');
        $FYID_REF           =   Session::get('FYID_REF');

        if(isset($formData["FILENAME"])){

            $uploadedFile   =   $formData["FILENAME"];

            if($uploadedFile->isValid()){
                $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
                $filesize               =   $uploadedFile ->getSize();  
                $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );
                $inputFileType          =   ucfirst($extension);   // as per API Xls or Xlsx: first charter in upper case
                $filenametostore        =   $VTID_REF.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".Date('YmdHis')."_".$filenamewithextension;  //excel file
                $file_name              =   pathinfo($filenamewithextension, PATHINFO_FILENAME);  // fetch only file name
                $logfile_name           =   "LOG_".$VTID_REF.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".Date('YmdHis')."_".$file_name.".txt";  //log text file
                $excelfile_path         =   "docs/company".$CYID_REF."/Production/importexcel";     
                $destinationPath        =   str_replace('\\', '/', public_path($excelfile_path));

                if ( !is_dir($destinationPath) ) {
                    mkdir($destinationPath, 0777, true);
                }

                if(in_array($extension,$allow_extnesions)){
                    
                    if($filesize < $allow_size){

                        $custfilename = $destinationPath."/".$filenametostore;

                        if ( !is_dir($destinationPath) ) {
                            mkdir($destinationPath, 0777, true);
                        } 

                        $uploadedFile->move($destinationPath, $filenametostore);  //upload file in dir if not exists

                        if (file_exists($custfilename)) {

                            try {
                                /** Load $inputFileName to a Spreadsheet Object  **/
                                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                                $reader->setReadDataOnly(true);
                                $spreadsheet = $reader->load($custfilename);
                                $worksheet = $spreadsheet->getActiveSheet();
                                
                                $excelHeaderdata    =   [];
                                $excelAlldata       =   [];

                                foreach ($worksheet->getRowIterator() as $rowindex=>$row) {
                                
                                    $cellIterator = $row->getCellIterator();
                                    
                                    $cellIterator->setIterateOnlyExistingCells(true);   
                                    /* ***** setIterateOnlyExistingCells(true)
                                    This loops through all cells, even if a cell value is not set.
                                    For 'TRUE', we loop through cells, only when their value is set.
                                    If this method is not called, the default value is 'false'.
                                    **** */
                                    foreach ($cellIterator as $index=>$cell) {
                                        if($rowindex==1){
                                            $excelHeaderdata[$index] = trim(strtolower($cell->getValue()) );  // fetch value for making header data
                                        }
                                        else{
                                            $excelAlldata[$rowindex-1][$excelHeaderdata[$index]]= trim($cell->getValue());
                                        }
                                    }                        
                                }
                        
                            } 
                            catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                                return redirect()->route("master",[202,"importdata"])->with("error","Error loading file: ".$e->getMessage());
                            }
                        }
                        else{
                            return redirect()->route("master",[202,"importdata"])->with("error","There is some file uploading error. Please try again.");
                        }
                    }
                    else{
                        return redirect()->route("master",[202,"importdata"])->with("error","Invalid size - Please check.");
                    }
                }
                else{
                    return redirect()->route("master",[202,"importdata"])->with("error","Invalid file extension - Please check.");                      
                }
            }
            else{  
                return redirect()->route("master",[202,"importdata"])->with("error","Invalid file - Please check.");  
            }
        }
        else{
            return redirect()->route("master",[202,"importdata"])->with("error","File not found. - Please check.");  
        }

        $logfile_path   =   $excelfile_path."/".$logfile_name;     

        if(!$logfile = fopen($logfile_path, "a") ){
            return redirect()->route("master",[202,"importdata"])->with("error","Log creating file error.");     //create or open log file
        }

        $validationErr      =   false;
        $headerArr          =   []; 
        $unique_doc_code1    =   "";

        foreach($excelAlldata as $eIndex=>$eRowData){

            $bom_no             =   isset($eRowData["bom_no"])?trim($eRowData["bom_no"]):NULL;
            $fg_product_code    =   isset($eRowData["fg_product_code"])?trim($eRowData["fg_product_code"]):NULL;
            $production_stage   =   isset($eRowData["production_stage"])?trim($eRowData["production_stage"]):NULL;
            $rm_code            =   isset($eRowData["rm_code"])?trim($eRowData["rm_code"]):NULL;
            $consumed_qty       =   isset($eRowData["consumed_qty"])?trim($eRowData["consumed_qty"]):NULL;
            $exist_data         =   $bom_no.'###'.$rm_code;
            $unique_doc_code2   =   $bom_no.'###'.$fg_product_code;
            

            if($bom_no ==""){
                $this->appendLogData($logfile,"Invalid: Blank BOM no. check row no ".$eIndex);
                $validationErr=true;
            }

            if(!empty($this->exist_doc_no($bom_no))){  
                $this->appendLogData($logfile,"Invalid: Already exist BOM no ".$bom_no." check row no ".$eIndex);
                $validationErr=true; 
            }

            if($fg_product_code ==""){
                $this->appendLogData($logfile,"Invalid: Blank Product Code. check row no ".$eIndex);
                $validationErr=true;
            }

            if($rm_code ==""){
                $this->appendLogData($logfile,"Invalid: Blank RM Code. check row no ".$eIndex);
                $validationErr=true;
            }

            if($consumed_qty ==""){
                $this->appendLogData($logfile,"Invalid: Blank Consumed Qty. check row no ".$eIndex);
                $validationErr=true;
            }

            if($validationErr ==false){

                if (!array_key_exists($bom_no, $headerArr)) {
                    $headerArr[$bom_no]["header"]["bom_no"]                     =   $bom_no;
                    $headerArr[$bom_no]["header"]["fg_product_code"]            =   $fg_product_code;
                    $headerArr[$bom_no]["header"]["production_stage"]           =   $production_stage;

                    $headerArr[$bom_no]["material"][$eIndex]["rm_code"]         =   $rm_code;
                    $headerArr[$bom_no]["material"][$eIndex]["consumed_qty"]    =   $consumed_qty;

                    $unique_doc_code1   =   $unique_doc_code2;
                }
                else{
                    if($unique_doc_code1 !=$unique_doc_code2){
                        $this->appendLogData($logfile,"Invalid: bom no/product code should same. check row no".$eIndex);
                        $validationErr=true; 
                    }
                    else{
                        if(!in_array($exist_data, $exit_array)){
                            $headerArr[$bom_no]["material"][$eIndex]["rm_code"]         =   $rm_code;
                            $headerArr[$bom_no]["material"][$eIndex]["consumed_qty"]    =   $consumed_qty; 
                        } 
                    }
                }
            }
          
            $exit_array[]=$exist_data;  
        }

        

        if($validationErr){
            fclose($logfile);
            return redirect()->route("master",[202,"importdata"])->with("logerror",$logfile_path);  
        }

        foreach($headerArr as $hIndex=>$hRowData){

            $HDR_DATA           =   $this->getCodeId($hRowData["header"]["fg_product_code"]);

            $VTID_REF           =   $this->vtid_ref;
            $USERID             =   Auth::user()->USERID;   
            $ACTIONNAME         =   'ADD';
            $IPADDRESS          =   $request->getClientIp();
            $CYID_REF           =   Auth::user()->CYID_REF;
            $BRID_REF           =   Session::get('BRID_REF');
            $FYID_REF           =   Session::get('FYID_REF');

            $BOMNO              =   strtoupper($hRowData["header"]["bom_no"]);
            $BOM_DT             =   date('Y-m-d');
            $PRODUCT_CODE       =   $HDR_DATA['ITEMID'];
            $UOMID_REF          =   $HDR_DATA['MAIN_UOMID_REF'];
            $PRODUCEQTY         =   1;
            $PRODUCTION_STAGE   =   $hRowData["header"]["production_stage"];
            $DESIGNNO           =   NULL;
            $DRAWINGNO          =   NULL;
            $REMARKS            =   NULL;
            $INSTRUCTION        =   NULL;
            $DEACTIVATED        =   NULL;  
            $DODEACTIVATED      =   NULL;
            $XMLMAT             =   NULL;
            $XMLMAT2            =   NULL;
            $XMLMAT3            =   NULL;
            $XMLUDF             =   NULL;
            $XMLMAT4            =   NULL;

            $req_data           =   array();

            foreach($hRowData["material"] as $pindex=>$prow){

                $MAT_DATA       =   $this->getCodeId($prow["rm_code"]);
                $ITEMID_REF     =   $MAT_DATA['ITEMID'];
                $UOMID_REF      =   $MAT_DATA['MAIN_UOMID_REF'];
                $CONSUME_QTY    =   $prow["consumed_qty"];
                
                $req_data[]= [
                    'ITEMID_REF'        =>  $ITEMID_REF,
                    'UOMID_REF'         =>  $UOMID_REF,
                    'CONSUME_QTY'       =>  $CONSUME_QTY,
                ];

            }
            
            if(!empty($req_data)){
                $wrapped_links["MAT"] = $req_data; 
                $XMLMAT = ArrayToXml::convert($wrapped_links);
            }
          
            $log_data = [ 
                $BOMNO,$BOM_DT,$PRODUCT_CODE,$UOMID_REF,$PRODUCEQTY,$PRODUCTION_STAGE,$DESIGNNO,$DRAWINGNO,$REMARKS,$DEACTIVATED
                ,$DODEACTIVATED,$INSTRUCTION,$CYID_REF, $BRID_REF,$FYID_REF,$XMLMAT,$XMLMAT2,$XMLMAT3,$XMLUDF,$XMLMAT4, $VTID_REF 
                ,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];


            try{
                $sp_result = DB::select('EXEC SP_BOM_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);    
            } 
            catch (\Throwable $th) {
                $this->appendLogData($logfile," bom ".$hIndex.": There is some error. Please try after sometime. " );
                fclose($logfile);
                return redirect()->route("master",[202,"importdata"])->with("logerror",$logfile_path); 
            }
    
            if(Str::contains(strtoupper($sp_result[0]->RESULT), 'SUCCESS')){
                $this->appendLogData($logfile,"bom ".$hIndex.": Record successfully inserted.","",1 );
            }
            else{
                $this->appendLogData($logfile," bom ".$hIndex.": Record not inserted. ".$sp_result[0]->RESULT );
                fclose($logfile);
                return redirect()->route("master",[202,"importdata"])->with("logerror",$logfile_path);                     
            }

        }
   
        fclose($logfile);
        return redirect()->route("master",[202,"importdata"])->with("logsuccess",$logfile_path);      
         
    }
    
    public function getCodeId($ICODE){

        $dataArr    =   array();    
        $data       =   DB::table('TBL_MST_ITEM')
                        ->where('ICODE','=',$ICODE)
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('BRID_REF','=',Session::get('BRID_REF'))
                //        ->where('FYID_REF','=',Session::get('FYID_REF'))
                        ->select('ITEMID','MAIN_UOMID_REF')
                        ->first();

        if(isset($data) && !empty($data)){
            $dataArr['ITEMID']=   $data->ITEMID;
            $dataArr['MAIN_UOMID_REF']=   $data->MAIN_UOMID_REF;
        }

        return $dataArr;

    }

    public function exist_doc_no($BOMNO){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
         
         $data      =   DB::table('TBL_MST_BOM_HDR')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('BRID_REF','=',Session::get('BRID_REF'))
                        ->where('BOM_NO','=',$BOMNO)
                        ->select('BOMID')
                        ->first();

         return $data;
         
    }

    public function appendLogData($logfile, $label, $cellval="",$removeError=0){
        if($removeError==0){
            $txtstring = "Error:".$label." ".$cellval."\n"; 
        }else{
            $txtstring = $label." ".$cellval."\n"; 
        }
            
        echo "<br>".$txtstring;
        fwrite($logfile, $txtstring);
    }

    public function downloadExcelFormate(){

        $excelfile_path =   "docs/importsamplefiles/BOM/BOM.xlsx";   
        $custfilename   =   str_replace('\\', '/', public_path($excelfile_path));
       
        $reader         =   \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet    =   $reader->load($custfilename);
        
        $writer         =   new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="BOM.xlsx"');
        ob_end_clean();
        $writer->save("php://output");
        return redirect()->back();
    }

    public function GetUOM(){
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        return $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
        WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by UOMCODE ASC', [$CYID_REF,'A' ]);  
    }

}