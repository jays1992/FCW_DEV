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

class TrnFrm40Controller extends Controller
{
    protected $form_id = 40;
    protected $vtid_ref   = 40; 

    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(){    
        $objRights = DB::table('TBL_MST_USERROLMAP')
                        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                        
                        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                        ->first();

        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');     
        
      
        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList	=	DB::select("select hdr.OSOID,hdr.OSONO,hdr.OSODT,hdr.OVFDT,hdr.OVTDT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.OSOID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_SLSO03_HDR hdr
                            on a.VID = hdr.OSOID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_SUBLEDGER sl ON hdr.SLID_REF = sl.SGLID  
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.OSOID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );

        return view('transactions.sales.OpenSalesOrder.trnfrm40',compact(['REQUEST_DATA','objRights','objDataList']));        
    }

    public function ViewReport($request) {
        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);

    if($myValue['Flag'] == 'H')
    {    
        $OSOID       =   $myValue['OSONO'];
        $Flag        =   $myValue['Flag'];
    }
    else
    {
        $OSOID       =   Session::get('OSOID');
        $Flag        =   $myValue['Flag'];
    }

    $objSalesOrder = DB::table('TBL_TRN_SLSO03_HDR')
        ->where('TBL_TRN_SLSO03_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLSO03_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        ->where('TBL_TRN_SLSO03_HDR.OSOID','=',$OSOID)
        ->select('TBL_TRN_SLSO03_HDR.*')
        ->first();

       
        $ssrs = new \SSRS\Report('http://103.139.58.23:8181//ReportServer/', array('username' => 'App', 'password' => 'admin@123'));
        $result = $ssrs->loadReport('/ERP/OSOPrint');
        
        $reportParameters = array(
            'OSONO' => $objSalesOrder->OSONO,
        );
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
        ->setExecutionParameters($parameters);
        if($Flag == 'H')
        {
            Session::put('OSOID', $OSOID);
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
         
     }

    public function add(){       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $FormId = $this->form_id;

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
            'HDR_TABLE'=>'TBL_TRN_SLSO03_HDR',
            'HDR_ID'=>'OSOID',
            'HDR_DOC_NO'=>'OSONO',
            'HDR_DOC_DT'=>'OSODT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        
        

        $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by TNC_CODE ASC', [$CYID_REF, $BRID_REF, 'A' ]);

        $ObjUnionUDF = DB::table("TBL_MST_UDFFORSO")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                                {$query->select('UDFID')->from('TBL_MST_UDFFORSO')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF)
                                                ->where('BRID_REF','=',$BRID_REF);
                                                               
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF);
                   
        $objUdfOSOData = DB::table('TBL_MST_UDFFORSO')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdfOSOData);

        $objCurrencyconverter = DB::table('TBL_MST_CRCONVERSION')
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_CRCONVERSION.*')
        ->get()
        ->toArray();

        $objlastOSODT = DB::select('SELECT MAX(OSODT) OSODT FROM TBL_TRN_SLSO03_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  40, 'A' ]);

        $objSalesPerson = DB::table('TBL_MST_EMPLOYEE')
        ->where('STATUS','=',$Status)
        ->where('SALES_PERSON','=','1')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->select('TBL_MST_EMPLOYEE.*')
        ->get()
        ->toArray();

        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
       
    return view('transactions.sales.OpenSalesOrder.trnfrm40add',
    compact(['objUdfOSOData','objcurrency','objTNCHeader','objothcurrency',
    'objCurrencyconverter','objSalesPerson','objCountUDF','objlastOSODT','FormId','AlpsStatus','TabSetting','doc_req','docarray']));       
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
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
    
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

    
    public function getItemDetails(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $taxstate = $request['taxstate'];
        $StdCost = 0;

        $AlpsStatus =   $this->AlpsStatus();
        
        $CODE = $request['CODE'];
        $NAME = $request['NAME'];
        $MUOM = $request['MUOM'];
        $GROUP = $request['GROUP'];
        $CTGRY = $request['CTGRY'];
        $BUNIT = $request['BUNIT'];
        $APART = $request['APART'];
        $CPART = $request['CPART'];
        $OPART = $request['OPART'];

        $sp_popup = [
            $CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART,$taxstate
        ]; 
        
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
                        $row.=' <tr id="item_'.$ITEMID.'" class="clsitemid">
                                <td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1"  ></td>
                                <td style="width:10%;">'.$ICODE.'<input type="hidden" id="txtitem_'.$ITEMID.'" data-desc="'.$ICODE.'" value="'.$ITEMID.'"/></td>
                                <td style="width:10%;" id="itemname_'.$ITEMID.'" >'.$NAME.'<input type="hidden" id="txtitemname_'.$ITEMID.'" data-desc="'.$ITEM_SPECI.'" value="'.$NAME.'"/></td>
                                <td style="width:8%;" id="itemuom_'.$ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ITEMID.'" data-desc="'.$Alt_UOM.'" data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'"  value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>
                                <td style="width:8%;" id="uomqty_'.$ITEMID.'" ><input type="hidden" id="txtuomqty_'.$ITEMID.'" data-desc="'.$TOQTY.'" value="'.$ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>
                                <td style="width:8%;" id="irate_'.$ITEMID.'"><input type="hidden" id="txtirate_'.$ITEMID.'" data-desc="'.$FROMQTY.'" value="'.$STDCOST.'"/>'.$GroupName.'</td>
                                <td style="width:8%;" id="itax_'.$ITEMID.'"><input type="hidden" id="txtitax_'.$ITEMID.'" data-desc="'.$Taxid1.'"  value="'.$Taxid2.'" />'.$Categoryname.'</td>
                                <td style="width:8%;">'.$BusinessUnit.'</td>
                                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                                <td style="width:8%;">Authorized</td>
                                </tr>'; 
                        echo $row;  
                    } 
                    
                }           
                else{
                 echo '<tr><td> Record not found.</td></tr>';
                }
        exit();
    }

    


   public function attachment($id){

    if(!is_null($id))
    {
        $objOpenSalesOrder = DB::table("TBL_TRN_SLSO03_HDR")
                        ->where('OSOID','=',$id)
                        ->select('TBL_TRN_SLSO03_HDR.*')
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

               

            return view('transactions.sales.OpenSalesOrder.trnfrm40attachment',compact(['objOpenSalesOrder','objMstVoucherType','objAttachments']));
    }

}

    
   public function save(Request $request) {
    
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]))
            {
                $req_data[$i] = [
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'UOMID_REF' => $request['UOMID_REF_'.$i],
                    'ITEMSPECI' => $request['ITEMSPECI_'.$i],
                    'RATEPUOM' => $request['RATEPUOM_'.$i],
                    'REMARKS' => $request['RMK_'.$i],
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
            
        
        for ($i=0; $i<=$r_count3; $i++)
        {
                if(isset($request['UDFID_REF_'.$i]) && !is_null($request['UDFID_REF_'.$i]))
                {
                    $reqdata3[$i] = [
                        'SOUDFID_REF'   => $request['UDFID_REF_'.$i],
                        'OSOUVALUE'      => $request['udfvalue_'.$i],
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
        
        
            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $OSONO = $request['OSONO'];
            $OSODT = $request['OSODT'];
            $GLID_REF = $request['GLID_REF'];
            $SLID_REF = $request['SLID_REF'];
            $OVFDT = $request['OVFDT'];
            $OVTDT = $request['OVTDT'];
            $CUSTOMERPONO = $request['CUSTOMERPONO'];
            $CUSTOMERPODT = $request['CUSTOMERPODT'];
            $SPID_REF = $request['SPID_REF'];
            $REFNO = $request['REFNO'];
            $BILLTO = $request['BILLTO'];
            $SHIPTO = $request['SHIPTO'];
            $OSOFC = (isset($request['OSOFC'])!="true" ? 0 : 1);
            $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
            $CONVFACT = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
            $REMARKS = $request['REMARKS'];

            $log_data = [ 
                $OSONO,$OSODT,$GLID_REF,$SLID_REF,$OVFDT,$OVTDT,$CUSTOMERPONO,$CUSTOMERPODT,$SPID_REF,$REFNO,
                $OSOFC,$CRID_REF,$CONVFACT,$REMARKS,$CYID_REF, $BRID_REF,$FYID_REF, $VTID_REF,
                $XMLMAT,$XMLTNC,$XMLUDF,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];

            
            $sp_result = DB::select('EXEC SP_OSO_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
            
        
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

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
        $FormId = $this->form_id;

        if(!is_null($id))
        {
            $objOSO = DB::table('TBL_TRN_SLSO03_HDR')
                             ->where('TBL_TRN_SLSO03_HDR.FYID_REF','=',Session::get('FYID_REF'))
                             ->where('TBL_TRN_SLSO03_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_TRN_SLSO03_HDR.BRID_REF','=',Session::get('BRID_REF'))
                             ->where('TBL_TRN_SLSO03_HDR.OSOID','=',$id)
                             ->select('TBL_TRN_SLSO03_HDR.*')
                             ->first();
            $log_data = [ 
                $id
            ];

            $objOSOMAT=array();
            if(isset($objOSO) && !empty($objOSO)){
                $objOSOMAT = DB::select('EXEC sp_get_open_sales_order_material ?', $log_data);
            }

            $objCount1 = count($objOSOMAT);

            

          
            $objOSOTNC = DB::table('TBL_TRN_SLSO03_TNC')                    
                             ->where('TBL_TRN_SLSO03_TNC.OSOID_REF','=',$id)
                             ->select('TBL_TRN_SLSO03_TNC.*')
                             ->orderBy('TBL_TRN_SLSO03_TNC.OSOTNCID','ASC')
                             ->get()->toArray();
            $objCount2 = count($objOSOTNC);

            $objOSOUDF = DB::table('TBL_TRN_SLSO03_UDF')                    
                             ->where('TBL_TRN_SLSO03_UDF.OSOID_REF','=',$id)
                             ->select('TBL_TRN_SLSO03_UDF.*')
                             ->orderBy('TBL_TRN_SLSO03_UDF.OSOUDFID','ASC')
                             ->get()->toArray();
            $objCount3 = count($objOSOUDF);

            
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
            
            $objSPID=[];
            if(isset($objOSO->SPID_REF) && $objOSO->SPID_REF !=""){
                $objEMP = DB::table('TBL_MST_EMPLOYEE')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('STATUS','=',$Status)
                ->where('SALES_PERSON','=',1)
                ->where('EMPID','=',$objOSO->SPID_REF)
                ->select('TBL_MST_EMPLOYEE.*')
                ->first();
                $objSPID[] = $objEMP->EMPCODE.'-'.$objEMP->FNAME.' '.$objEMP->MNAME.' '.$objEMP->LNAME;
            }
            
            $objsubglcode =[];
            if(isset($objOSO->GLID_REF) && $objOSO->GLID_REF !=""){
            $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('STATUS','=',$Status)
            ->where('GLID_REF','=',$objOSO->GLID_REF)
            ->where('SGLID','=',$objOSO->SLID_REF)
            ->select('TBL_MST_SUBLEDGER.*')
            ->first();
            }

            $objOSOcurrency=[];
            $objcurrency=[];
            $objothcurrency=[];
            if(isset($objOSO->CRID_REF) && $objOSO->CRID_REF !=""){
                $objcurrency = DB::table('TBL_MST_CURRENCY')
                ->where('CRID','=',$objOSO->CRID_REF)
                ->select('TBL_MST_CURRENCY.*')
                ->first();
                if($objcurrency)
                {
                $objOSOcurrency = $objcurrency->CRCODE;
                }
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

            }

           
            $objlastOSODT = DB::select('SELECT MAX(OSODT) OSODT FROM TBL_TRN_SLSO03_HDR  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
            [$CYID_REF, $BRID_REF,  40, 'A' ]);

            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
            WHERE  CYID_REF = ? AND BRID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by TNC_CODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);
    
             $ObjUnionUDF = DB::table("TBL_MST_UDFFORSO")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                                    {       
                                    $query->select('UDFID')->from('TBL_MST_UDFFORSO')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF)
                                                    ->where('BRID_REF','=',$BRID_REF);
                                                                  
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF);
                                          
                    
    
            $objUdfOSOData = DB::table('TBL_MST_UDFFORSO')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFORSO")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                        {       
                        $query->select('UDFID')->from('TBL_MST_UDFFORSO')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF)
                                        ->where('BRID_REF','=',$BRID_REF);
                                                        
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF);                 
            

            $objUdfOSOData2 = DB::table('TBL_MST_UDFFORSO')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF2)
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
            
            
            
            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
            ->get() ->toArray(); 

            $AlpsStatus =   $this->AlpsStatus();
            $InputStatus =   "";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
            
            return view('transactions.sales.OpenSalesOrder.trnfrm40edit',compact(['objOSO','objRights','objCount1','objSPID',
            'objCount2','objCount3','objOSOMAT','objOSOTNC','objOSOUDF','FormId','objsubglcode',
            'objUdfOSOData','objcurrency','objTNCHeader','objothcurrency','objCurrencyconverter','objSalesPerson',
            'objTNCDetails','objUdfOSOData2','objOSOcurrency','objlastOSODT','AlpsStatus','InputStatus','TabSetting']));
            }
     
       }
     
       public function view($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        $FormId = $this->form_id;

        if(!is_null($id))
        {
            $objOSO = DB::table('TBL_TRN_SLSO03_HDR')
                             ->where('TBL_TRN_SLSO03_HDR.FYID_REF','=',Session::get('FYID_REF'))
                             ->where('TBL_TRN_SLSO03_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_TRN_SLSO03_HDR.BRID_REF','=',Session::get('BRID_REF'))
                             ->where('TBL_TRN_SLSO03_HDR.OSOID','=',$id)
                             ->select('TBL_TRN_SLSO03_HDR.*')
                             ->first();
            $log_data = [ 
                $id
            ];

            $objOSOMAT=array();
            if(isset($objOSO) && !empty($objOSO)){
                $objOSOMAT = DB::select('EXEC sp_get_open_sales_order_material ?', $log_data);
            }

            $objCount1 = count($objOSOMAT);

            

          
            $objOSOTNC = DB::table('TBL_TRN_SLSO03_TNC')                    
                             ->where('TBL_TRN_SLSO03_TNC.OSOID_REF','=',$id)
                             ->select('TBL_TRN_SLSO03_TNC.*')
                             ->orderBy('TBL_TRN_SLSO03_TNC.OSOTNCID','ASC')
                             ->get()->toArray();
            $objCount2 = count($objOSOTNC);

            $objOSOUDF = DB::table('TBL_TRN_SLSO03_UDF')                    
                             ->where('TBL_TRN_SLSO03_UDF.OSOID_REF','=',$id)
                             ->select('TBL_TRN_SLSO03_UDF.*')
                             ->orderBy('TBL_TRN_SLSO03_UDF.OSOUDFID','ASC')
                             ->get()->toArray();
            $objCount3 = count($objOSOUDF);

            
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
            
            $objSPID=[];
            if(isset($objOSO->SPID_REF) && $objOSO->SPID_REF !=""){
                $objEMP = DB::table('TBL_MST_EMPLOYEE')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('STATUS','=',$Status)
                ->where('SALES_PERSON','=',1)
                ->where('EMPID','=',$objOSO->SPID_REF)
                ->select('TBL_MST_EMPLOYEE.*')
                ->first();
                $objSPID[] = $objEMP->EMPCODE.'-'.$objEMP->FNAME.' '.$objEMP->MNAME.' '.$objEMP->LNAME;
            }
            
            $objsubglcode =[];
            if(isset($objOSO->GLID_REF) && $objOSO->GLID_REF !=""){
            $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('STATUS','=',$Status)
            ->where('GLID_REF','=',$objOSO->GLID_REF)
            ->where('SGLID','=',$objOSO->SLID_REF)
            ->select('TBL_MST_SUBLEDGER.*')
            ->first();
            }

            $objOSOcurrency=[];
            $objcurrency=[];
            $objothcurrency=[];
            if(isset($objOSO->CRID_REF) && $objOSO->CRID_REF !=""){
                $objcurrency = DB::table('TBL_MST_CURRENCY')
                ->where('CRID','=',$objOSO->CRID_REF)
                ->select('TBL_MST_CURRENCY.*')
                ->first();
                if($objcurrency)
                {
                $objOSOcurrency = $objcurrency->CRCODE;
                }
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

            }

           
            $objlastOSODT = DB::select('SELECT MAX(OSODT) OSODT FROM TBL_TRN_SLSO03_HDR  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
            [$CYID_REF, $BRID_REF,  40, 'A' ]);

            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
            WHERE  CYID_REF = ? AND BRID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by TNC_CODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);
    
             $ObjUnionUDF = DB::table("TBL_MST_UDFFORSO")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                                    {       
                                    $query->select('UDFID')->from('TBL_MST_UDFFORSO')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF)
                                                    ->where('BRID_REF','=',$BRID_REF);
                                                                        
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF);
                           
                    
    
            $objUdfOSOData = DB::table('TBL_MST_UDFFORSO')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                
                ->union($ObjUnionUDF)
                ->get()->toArray(); 
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFORSO")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                        {       
                        $query->select('UDFID')->from('TBL_MST_UDFFORSO')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF)
                                        ->where('BRID_REF','=',$BRID_REF);
                                                          
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF);
                              
            

            $objUdfOSOData2 = DB::table('TBL_MST_UDFFORSO')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF2)
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
            
            
            
            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
            ->get() ->toArray(); 

            $AlpsStatus =   $this->AlpsStatus();
            $InputStatus =   "disabled";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
            
            return view('transactions.sales.OpenSalesOrder.trnfrm40view',compact(['objOSO','objRights','objCount1','objSPID',
            'objCount2','objCount3','objOSOMAT','objOSOTNC','objOSOUDF','FormId','objsubglcode',
            'objUdfOSOData','objcurrency','objTNCHeader','objothcurrency','objCurrencyconverter','objSalesPerson',
            'objTNCDetails','objUdfOSOData2','objOSOcurrency','objlastOSODT','AlpsStatus','InputStatus','TabSetting']));
            }
     
       }

  
   public function update(Request $request){

    $r_count1 = $request['Row_Count1'];
    $r_count2 = $request['Row_Count2'];
    $r_count3 = $request['Row_Count3'];
    
    for ($i=0; $i<=$r_count1; $i++)
    {
        if(isset($request['ITEMID_REF_'.$i]))
        {
            $req_data[$i] = [
                'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                'UOMID_REF' => $request['UOMID_REF_'.$i],
                'ITEMSPECI' => $request['ITEMSPECI_'.$i],
                'RATEPUOM' => $request['RATEPUOM_'.$i],
                'REMARKS' => $request['RMK_'.$i],
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
        
    
    for ($i=0; $i<=$r_count3; $i++)
    {
            if(isset($request['UDFID_REF_'.$i]) && !is_null($request['UDFID_REF_'.$i]))
            {
                $reqdata3[$i] = [
                    'SOUDFID_REF'   => $request['UDFID_REF_'.$i],
                    'OSOUVALUE'      => $request['udfvalue_'.$i],
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
        
    
    
        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $OSONO = $request['OSONO'];
        $OSODT = $request['OSODT'];
        $GLID_REF = $request['GLID_REF'];
        $SLID_REF = $request['SLID_REF'];
        $OVFDT = $request['OVFDT'];
        $OVTDT = $request['OVTDT'];
        $CUSTOMERPONO = $request['CUSTOMERPONO'];
        $CUSTOMERPODT = $request['CUSTOMERPODT'];
        $SPID_REF = $request['SPID_REF'];
        $REFNO = $request['REFNO'];
        $BILLTO = $request['BILLTO'];
        $SHIPTO = $request['SHIPTO'];
        $OSOFC = (isset($request['OSOFC'])!="true" ? 0 : 1);
        $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
        $CONVFACT = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
        $REMARKS = $request['REMARKS'];

        $log_data = [ 
            $OSONO,$OSODT,$GLID_REF,$SLID_REF,$OVFDT,$OVTDT,$CUSTOMERPONO,$CUSTOMERPODT,$SPID_REF,$REFNO,
            $OSOFC,$CRID_REF,$CONVFACT,$REMARKS,$CYID_REF, $BRID_REF,$FYID_REF, $VTID_REF,
            $XMLMAT,$XMLTNC,$XMLUDF,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
        ];

        
        $sp_result = DB::select('EXEC SP_OSO_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
        
    
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
            
            for ($i=0; $i<=$r_count1; $i++)
            {
                if(isset($request['ITEMID_REF_'.$i]))
                {
                    $req_data[$i] = [
                        'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                        'UOMID_REF' => $request['UOMID_REF_'.$i],
                        'ITEMSPECI' => $request['ITEMSPECI_'.$i],
                        'RATEPUOM' => $request['RATEPUOM_'.$i],
                        'REMARKS' => $request['RMK_'.$i],
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
                
            
            for ($i=0; $i<=$r_count3; $i++)
            {
                    if(isset($request['UDFID_REF_'.$i]) && !is_null($request['UDFID_REF_'.$i]))
                    {
                        $reqdata3[$i] = [
                            'SOUDFID_REF'   => $request['UDFID_REF_'.$i],
                            'OSOUVALUE'      => $request['udfvalue_'.$i],
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
                
            
            
                $VTID_REF     =   $this->vtid_ref;
                $VID = 0;
                $USERID = Auth::user()->USERID;   
                $ACTIONNAME = $Approvallevel;
                $IPADDRESS = $request->getClientIp();
                $CYID_REF = Auth::user()->CYID_REF;
                $BRID_REF = Session::get('BRID_REF');
                $FYID_REF = Session::get('FYID_REF');
                $OSONO = $request['OSONO'];
                $OSODT = $request['OSODT'];
                $GLID_REF = $request['GLID_REF'];
                $SLID_REF = $request['SLID_REF'];
                $OVFDT = $request['OVFDT'];
                $OVTDT = $request['OVTDT'];
                $CUSTOMERPONO = $request['CUSTOMERPONO'];
                $CUSTOMERPODT = $request['CUSTOMERPODT'];
                $SPID_REF = $request['SPID_REF'];
                $REFNO = $request['REFNO'];
                $BILLTO = $request['BILLTO'];
                $SHIPTO = $request['SHIPTO'];
                $OSOFC = (isset($request['OSOFC'])!="true" ? 0 : 1);
                $CRID_REF = (isset($request['CRID_REF'])) ? $request['CRID_REF'] : 0;
                $CONVFACT = (isset($request['CONVFACT'])) ? $request['CONVFACT'] : "";
                $REMARKS = $request['REMARKS'];
    
                $log_data = [ 
                    $OSONO,$OSODT,$GLID_REF,$SLID_REF,$OVFDT,$OVTDT,$CUSTOMERPONO,$CUSTOMERPODT,$SPID_REF,$REFNO,
                    $OSOFC,$CRID_REF,$CONVFACT,$REMARKS,$CYID_REF, $BRID_REF,$FYID_REF, $VTID_REF,
                    $XMLMAT,$XMLTNC,$XMLUDF,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
                ];
    
                
                $sp_result = DB::select('EXEC SP_OSO_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
                
            
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
                $TABLE      =   "TBL_TRN_SLSO03_HDR";
                $FIELD      =   "OSOID";
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
        $TABLE      =   "TBL_TRN_SLSO03_HDR";
        $FIELD      =   "OSOID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
        
        $req_data[0]=[
        'NT'  => 'TBL_TRN_SLSO03_MAT',
        ];
        $req_data[1]=[
        'NT'  => 'TBL_TRN_SLSO03_TNC',
        ];
        $req_data[2]=[
        'NT'  => 'TBL_TRN_SLSO03_UDF',
        ];
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_OSO  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);


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
    
    $image_path         =   "docs/company".$CYID_REF."/OpenSalesOrder";     
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
        return redirect()->route("transaction",[40,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
    
       return redirect()->route("transaction",[40,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("transaction",[40,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("transaction",[40,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("transaction",[40,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

    public function checkoso(Request $request){

        
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $OSONO = $request->OSONO;
        
        $objSO = DB::table('TBL_TRN_SLSO03_HDR')
        ->where('TBL_TRN_SLSO03_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLSO03_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_SLSO03_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_SLSO03_HDR.OSONO','=',$OSONO)
        ->select('TBL_TRN_SLSO03_HDR.OSOID')
        ->first();
        
        if($objSO){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate OSONO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }

}



