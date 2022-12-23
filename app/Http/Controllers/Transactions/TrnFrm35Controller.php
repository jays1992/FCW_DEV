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

class TrnFrm35Controller extends Controller{

    protected $form_id      = 35;
    protected $vtid_ref     = 35;

    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){   

        $objRights  =   DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
		->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
		->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');    
        
        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;
         
        $objDataList	=	DB::select("select hdr.SEQID,HDR.ENQNO,hdr.ENQDT,hdr.ENQBY,hdr.REMARKS,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.SEQID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                            ) AS CREATED_BY,
                        hdr.STATUS,sl.SLNAME,
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
                        inner join TBL_TRN_SLEQ01_HDR hdr
                        on a.VID = hdr.SEQID 
                        and a.VTID_REF = hdr.VTID_REF 
                        and a.CYID_REF = hdr.CYID_REF 
                        and a.BRID_REF = hdr.BRID_REF
                        inner join TBL_MST_SUBLEDGER sl ON hdr.SLID_REF = sl.SGLID
                        where a.VTID_REF = '$this->vtid_ref'
                        and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                        and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                        ORDER BY hdr.SEQID DESC ");

                        $REQUEST_DATA   =   array(
                            'FORMID'    =>  $this->form_id,
                            'VTID_REF'  =>  $this->vtid_ref,
                            'USERID'    =>  Auth::user()->USERID,
                            'CYID_REF'  =>  Auth::user()->CYID_REF,
                            'BRID_REF'  =>  Session::get('BRID_REF'),
                            'FYID_REF'  =>  Session::get('FYID_REF'),
                        );

        return view('transactions.sales.SalesEnquiry.trnfrm35',compact(['REQUEST_DATA','objRights','objDataList']));        
    }

    public function ViewReport($request) {
        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
        
        $SEQID       =   $myValue['ENQNO'];
        $Flag        =   $myValue['Flag'];

        $objSalesChallan = DB::table('TBL_TRN_SLEQ01_HDR')
        ->where('TBL_TRN_SLEQ01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLEQ01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        ->where('TBL_TRN_SLEQ01_HDR.SEQID','=',$SEQID)
        ->select('TBL_TRN_SLEQ01_HDR.*')
        ->first();
        //dd($objSalesChallan);
        $ssrs = new \SSRS\Report('http://103.139.58.23:8181//ReportServer/', array('username' => 'App', 'password' => 'admin@123'));
        $result = $ssrs->loadReport('/ERP/SalesEnquiryPrint');
        
        $reportParameters = array(
            'SEPrint' => $objSalesChallan->ENQNO,
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
            $output = $ssrs->render('HTML4.0'); // PDF | XML | CSV | HTML4.0
            echo $output;

        }
             
    }

    public function add(){ 
        
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');      
        
        $objenquirymedia = DB::select('SELECT EMID, EMCODE, DESCRIPTIONS FROM TBL_MST_ENQUIRYMEDIA  
        WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by EMCODE ASC', [$CYID_REF, 'A' ]);

        $objPriority = DB::select('SELECT PRIORITYID, PRIORITYCODE, DESCRIPTIONS FROM TBL_MST_PRIORITY  
        WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by PRIORITYCODE ASC', [$CYID_REF, 'A' ]);

        $objlastENQDT = DB::select('SELECT MAX(ENQDT) ENQDT FROM TBL_TRN_SLEQ01_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  35, 'A' ]);
        
        $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
        WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by UOMCODE ASC', [$CYID_REF,'A' ]);        

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_SLEQ01_HDR',
            'HDR_ID'=>'SEQID',
            'HDR_DOC_NO'=>'ENQNO',
            'HDR_DOC_DT'=>'ENQDT'
        );

        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
        
        $objPackingType = DB::select('SELECT PTID, PTCODE, PTNAME FROM TBL_MST_PACKAGINGTYPE  
            WHERE  CYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by PTCODE ASC', [$CYID_REF, 'A' ]);

        
        $ObjUnionUDF = DB::table("TBL_MST_UDFFORSE")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                                {       
                                $query->select('UDFSEID')->from('TBL_MST_UDFFORSE')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF)
                                                ->where('BRID_REF','=',$BRID_REF);
                                                             
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF);
                              
                   

        $objUdfSEData = DB::table('TBL_MST_UDFFORSE')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdfSEData);
    

        
        $objSalesPerson = DB::table('TBL_MST_EMPLOYEE')
        ->where('STATUS','=',$Status)
        ->where('SALES_PERSON','=','1')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->select('TBL_MST_EMPLOYEE.*')
        ->get()
        ->toArray();

        $FormId = $this->form_id;

        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
      
    return view('transactions.sales.SalesEnquiry.trnfrm35add',
    compact(['objUdfSEData','objSalesPerson','objCountUDF',
    'objenquirymedia','objlastENQDT','objPriority','objPackingType','objUOM','FormId','AlpsStatus','TabSetting','doc_req','docarray']));       
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

        $AlpsStatus =   $this->AlpsStatus();

        $sp_popup = [
            $CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART
        ]; 
        
            $ObjItem = DB::select('EXEC sp_get_items_popup_enquiry ?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);
            
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
                        
                        
                        $row = '';
                        $row.=' <tr id="item_'.$ITEMID.'" class="clsitemid">
                                <td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1"  ></td>
                                <td style="width:10%;">'.$ICODE.'<input type="hidden" id="txtitem_'.$ITEMID.'" data-desc="'.$ICODE.'" value="'.$ITEMID.'"/></td>
                                <td style="width:10%;" id="itemname_'.$ITEMID.'" >'.$NAME.'<input type="hidden" id="txtitemname_'.$ITEMID.'" data-desc="'.$ITEM_SPECI.'" value="'.$NAME.'"/></td>
                                <td style="width:8%;" id="itemuom_'.$ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ITEMID.'" data-desc="'.$Alt_UOM.'" data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>
                                <td style="width:8%;" id="uomqty_'.$ITEMID.'" ><input type="hidden" id="txtuomqty_'.$ITEMID.'" data-desc="'.$TOQTY.'" value="'.$ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>
                                <td style="width:8%;" id="irate_'.$ITEMID.'"><input type="hidden" id="txtirate_'.$ITEMID.'" data-desc="'.$FROMQTY.'" value="'.$STDCOST.'"/>'.$GroupName.'</td>
                                <td style="width:8%;" id="itax_'.$ITEMID.'"><input type="hidden" id="txtitax_'.$ITEMID.'" />'.$Categoryname.'</td>
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
                    echo '<tr><td colspan="12"> Record not found.</td></tr>';
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
        $fieldid    = $request['fieldid'];

        $ObjData =  DB::select('SELECT TO_UOMID_REF FROM TBL_MST_ITEM_UOMCONV  
                WHERE ITEMID_REF= ?  order by IUCID ASC', [$id]);

        if(!empty($ObjData)){

        foreach ($ObjData as $index=>$dataRow){

            $ObjAltUOM =  DB::select('SELECT top 1 UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                WHERE UOMID= ?  ', [$dataRow->TO_UOMID_REF]);
        
            $row = '';
            $row = $row.'<tr >
            <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="altuom_'.$dataRow->TO_UOMID_REF .'"  class="clsaltuom" value="'.$dataRow->TO_UOMID_REF.'" ></td>
            <td class="ROW2">'.$ObjAltUOM[0]->UOMCODE;
            $row = $row.'<input type="hidden" id="txtaltuom_'.$dataRow->TO_UOMID_REF.'" data-desc="'.$ObjAltUOM[0]->UOMCODE .' - ';
            $row = $row.$ObjAltUOM[0]->DESCRIPTIONS. '" value="'.$dataRow->TO_UOMID_REF.'"/></td>
            <td class="ROW3" >'.$ObjAltUOM[0]->DESCRIPTIONS.'</td></tr>';

            echo $row;
        }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();
    
    }

    
   public function attachment($id){

    if(!is_null($id))
    {
        $objSalesenquiry = DB::table("TBL_TRN_SLEQ01_HDR")
                        ->where('SEQID','=',$id)
                        ->select('TBL_TRN_SLEQ01_HDR.*')
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

                 

            return view('transactions.sales.SalesEnquiry.trnfrm35attachment',compact(['objSalesenquiry','objMstVoucherType','objAttachments']));
    }

}

    
   public function save(Request $request) {
    
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]))
            {
                $req_data[$i] = [
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'MAIN_QTY'          => $request['SE_QTY_'.$i],
                    'ALTUOMID_REF'      => $request['ALT_UOMID_REF_'.$i],
                    'EDD'               => $request['EDD_'.$i],
                    'TARGETPRICE'       => (Empty($request['TARGETPRICE_'.$i]) !="true" ? $request['TARGETPRICE_'.$i] : 0),
                    'PTID_REF'          => (Empty($request['PTID_REF_'.$i]) !="true" ? $request['PTID_REF_'.$i] : null),
                    'PACKUOMID_REF'     => $request['PACKUOMID_REF_'.$i],
                    'PACK_QTY'          => (Empty($request['PACK_QTY_'.$i])!="true" ? $request['PACK_QTY_'.$i] : 0),
                    'ITEM_SPECI'         => $request['Itemspec_'.$i],
                ];
            }
        }
        
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
            
        
        for ($i=0; $i<=$r_count2; $i++)
        {
                if(isset($request['SEID_REF_'.$i]))
                {
                    $reqdata3[$i] = [
                        'SEID_REF'      => $request['SEID_REF_'.$i],
                        'SEQUVALUE'     => $request['udfvalue_'.$i],
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
            $ENQNO = $request['ENQNO'];
            $ENQDT = $request['ENQDT'];
            $GLID_REF = $request['GLID_REF'];
            $SLID_REF = $request['SLID_REF'];
            $EMID_REF = $request['EMID_REF'];
            $ENQBY = $request['ENQBY'];
            $PRIORITYID_REF = $request['PRIORITYID_REF'];
            $SPID_REF = $request['SPID_REF'];
            $PROSPECTREFNO = $request['PROSPECTREFNO'];
            $CONV_PROLTY = $request['CONV_PROLTY'];
            $APPROXV = $request['APPROXV'];
            $REMARKS = $request['REMARKS'];

            $log_data = [ 
                $ENQNO,$ENQDT,$GLID_REF,$SLID_REF,$EMID_REF,$ENQBY,$PRIORITYID_REF,$SPID_REF,$PROSPECTREFNO,
                $CONV_PROLTY,$APPROXV,$REMARKS,$FYID_REF,$CYID_REF, $BRID_REF, $VTID_REF,
                $XMLMAT, $XMLUDF, $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
            ];

            
            $sp_result = DB::select('EXEC SP_SE_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
            
        
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
            
            $objSE = DB::table('TBL_TRN_SLEQ01_HDR')     
                    ->where('TBL_TRN_SLEQ01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('TBL_TRN_SLEQ01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                    ->where('TBL_TRN_SLEQ01_HDR.FYID_REF','=',Session::get('FYID_REF'))
                    ->where('TBL_TRN_SLEQ01_HDR.SEQID','=',$id)
                    ->select('TBL_TRN_SLEQ01_HDR.*')
                    ->first();


          
            

            $log_data = [ 
                $id
            ];

            $objSEMAT=array();
            if(!empty($objSE)){
                $objSEMAT = DB::select('EXEC sp_get_sales_enquiry_material ?', $log_data);
            }


            $objCount1 = count($objSEMAT);            
            
            $objSEUDF = DB::table('TBL_TRN_SLEQ01_UDF')                    
                             ->where('TBL_TRN_SLEQ01_UDF.SEQID_REF','=',$id)
                             ->select('TBL_TRN_SLEQ01_UDF.*')
                             ->orderBy('TBL_TRN_SLEQ01_UDF.SEQUDFID','ASC')
                             ->get()->toArray();
            $objCount2 = count($objSEUDF);            
     
            $objRights = DB::table('TBL_MST_USERROLMAP')
			->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
			->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
			->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
			->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
			->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
			->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
			->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
			->first();
            
            $objSPID    =   [];
            if(isset($objSE->SPID_REF) && $objSE->SPID_REF !=""){

                $objEMP = DB::table('TBL_MST_EMPLOYEE')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('STATUS','=',$Status)
                ->where('SALES_PERSON','=',1)
                ->where('EMPID','=',$objSE->SPID_REF)
                ->select('EMPCODE','FNAME','MNAME','LNAME')
                ->first();

                $objSPID[] = $objEMP->EMPCODE.'-'.$objEMP->FNAME.' '.$objEMP->MNAME.' '.$objEMP->LNAME;
            }

            $objsubglcode=array();
            if(isset($objSE->GLID_REF) && $objSE->GLID_REF !=""){

                $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('GLID_REF','=',$objSE->GLID_REF)
                ->where('SGLID','=',$objSE->SLID_REF)
                ->select('TBL_MST_SUBLEDGER.SGLCODE','TBL_MST_SUBLEDGER.SLNAME')
                ->first();
            }
           
            $objenquirymedia2=array();
            if(isset($objSE->EMID_REF) && $objSE->EMID_REF !=""){
                $objenquirymedia2 = DB::table('TBL_MST_ENQUIRYMEDIA')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('EMID','=',$objSE->EMID_REF)
                ->select('EMID', 'EMCODE', 'DESCRIPTIONS')
                ->first();
            }
            
            $objPriority2=array();
            if(isset($objSE->PRIORITYID_REF) && $objSE->PRIORITYID_REF !=""){
                $objPriority2 = DB::table('TBL_MST_PRIORITY')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('PRIORITYID','=',$objSE->PRIORITYID_REF)
                ->select('PRIORITYID', 'PRIORITYCODE', 'DESCRIPTIONS')
                ->first();
            }

            $objenquirymedia = DB::select('SELECT EMID, EMCODE, DESCRIPTIONS FROM TBL_MST_ENQUIRYMEDIA  
            WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by EMCODE ASC', [$CYID_REF, 'A' ]);

            $objPriority = DB::select('SELECT PRIORITYID, PRIORITYCODE, DESCRIPTIONS FROM TBL_MST_PRIORITY  
            WHERE  CYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by PRIORITYCODE ASC', [$CYID_REF, 'A' ]);

            $objlastENQDT = DB::select('SELECT MAX(ENQDT) ENQDT FROM TBL_TRN_SLEQ01_HDR  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
            [$CYID_REF, $BRID_REF,  35, 'A' ]);
            
            $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
            WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by UOMCODE ASC', [$CYID_REF, 'A' ]);        

            $objPackingType = DB::select('SELECT PTID, PTCODE, PTNAME FROM TBL_MST_PACKAGINGTYPE  
                WHERE  CYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                order by PTCODE ASC', [$CYID_REF,'A' ]);

            
            $ObjUnionUDF = DB::table("TBL_MST_UDFFORSE")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                                    {       
                                    $query->select('UDFSEID')->from('TBL_MST_UDFFORSE')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF)
                                                    ->where('BRID_REF','=',$BRID_REF);
                                                                    
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF);
                                     
                    

            $objUdfSEData = DB::table('TBL_MST_UDFFORSE')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray();   
            $objCountUDF = count($objUdfSEData);        

            
            $objSalesPerson = DB::table('TBL_MST_EMPLOYEE')
            ->where('STATUS','=',$Status)
            ->where('SALES_PERSON','=','1')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->select('EMPID','EMPCODE','FNAME','MNAME','LNAME')
            ->get()
            ->toArray();
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFORSE")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                        {       
                        $query->select('UDFSEID')->from('TBL_MST_UDFFORSE')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF)
                                        ->where('BRID_REF','=',$BRID_REF);
                                                          
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF);
                         
            

            $objUdfSEData2 = DB::table('TBL_MST_UDFFORSE')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
               
                ->union($ObjUnionUDF2)
                ->get()->toArray();     
                $FormId = $this->form_id;

                $AlpsStatus =   $this->AlpsStatus();
                $ActionStatus   =   "";

                $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view('transactions.sales.SalesEnquiry.trnfrm35edit',compact(['objSE','objRights','objCount1','objSPID',
           'objCount2','objSEMAT','objSEUDF','objUdfSEData','objCountUDF','FormId',
           'objSalesPerson','objsubglcode','objUdfSEData2','objenquirymedia','objlastENQDT','objPriority',
           'objPackingType','objUOM','objenquirymedia2','objPriority2','AlpsStatus','ActionStatus','TabSetting']));
        }
     
    }
     
    public function view($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){
            
            $objSE = DB::table('TBL_TRN_SLEQ01_HDR')     
                    ->where('TBL_TRN_SLEQ01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('TBL_TRN_SLEQ01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                    ->where('TBL_TRN_SLEQ01_HDR.FYID_REF','=',Session::get('FYID_REF'))
                    ->where('TBL_TRN_SLEQ01_HDR.SEQID','=',$id)
                    ->select('TBL_TRN_SLEQ01_HDR.*')
                    ->first();


          
            

            $log_data = [ 
                $id
            ];

            $objSEMAT=array();
            if(!empty($objSE)){
                $objSEMAT = DB::select('EXEC sp_get_sales_enquiry_material ?', $log_data);
            }


            $objCount1 = count($objSEMAT);            
            
            $objSEUDF = DB::table('TBL_TRN_SLEQ01_UDF')                    
                             ->where('TBL_TRN_SLEQ01_UDF.SEQID_REF','=',$id)
                             ->select('TBL_TRN_SLEQ01_UDF.*')
                             ->orderBy('TBL_TRN_SLEQ01_UDF.SEQUDFID','ASC')
                             ->get()->toArray();
            $objCount2 = count($objSEUDF);            
     
            $objRights = DB::table('TBL_MST_USERROLMAP')
			->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
			->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
			->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
			->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
			->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
			->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
			->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
			->first();
            
            $objSPID    =   [];
            if(isset($objSE->SPID_REF) && $objSE->SPID_REF !=""){

                $objEMP = DB::table('TBL_MST_EMPLOYEE')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('STATUS','=',$Status)
                ->where('SALES_PERSON','=',1)
                ->where('EMPID','=',$objSE->SPID_REF)
                ->select('EMPCODE','FNAME','MNAME','LNAME')
                ->first();

                $objSPID[] = $objEMP->EMPCODE.'-'.$objEMP->FNAME.' '.$objEMP->MNAME.' '.$objEMP->LNAME;
            }

            $objsubglcode=array();
            if(isset($objSE->GLID_REF) && $objSE->GLID_REF !=""){

                $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('GLID_REF','=',$objSE->GLID_REF)
                ->where('SGLID','=',$objSE->SLID_REF)
                ->select('TBL_MST_SUBLEDGER.SGLCODE','TBL_MST_SUBLEDGER.SLNAME')
                ->first();
            }
           
            $objenquirymedia2=array();
            if(isset($objSE->EMID_REF) && $objSE->EMID_REF !=""){
                $objenquirymedia2 = DB::table('TBL_MST_ENQUIRYMEDIA')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('EMID','=',$objSE->EMID_REF)
                ->select('EMID', 'EMCODE', 'DESCRIPTIONS')
                ->first();
            }
            
            $objPriority2=array();
            if(isset($objSE->PRIORITYID_REF) && $objSE->PRIORITYID_REF !=""){
                $objPriority2 = DB::table('TBL_MST_PRIORITY')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('PRIORITYID','=',$objSE->PRIORITYID_REF)
                ->select('PRIORITYID', 'PRIORITYCODE', 'DESCRIPTIONS')
                ->first();
            }

            $objenquirymedia = DB::select('SELECT EMID, EMCODE, DESCRIPTIONS FROM TBL_MST_ENQUIRYMEDIA  
            WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by EMCODE ASC', [$CYID_REF, 'A' ]);

            $objPriority = DB::select('SELECT PRIORITYID, PRIORITYCODE, DESCRIPTIONS FROM TBL_MST_PRIORITY  
            WHERE  CYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by PRIORITYCODE ASC', [$CYID_REF, 'A' ]);

            $objlastENQDT = DB::select('SELECT MAX(ENQDT) ENQDT FROM TBL_TRN_SLEQ01_HDR  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
            [$CYID_REF, $BRID_REF,  35, 'A' ]);
            
            $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
            WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by UOMCODE ASC', [$CYID_REF, 'A' ]);        

            $objPackingType = DB::select('SELECT PTID, PTCODE, PTNAME FROM TBL_MST_PACKAGINGTYPE  
                WHERE  CYID_REF = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                order by PTCODE ASC', [$CYID_REF,'A' ]);

            
            $ObjUnionUDF = DB::table("TBL_MST_UDFFORSE")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                                    {       
                                    $query->select('UDFSEID')->from('TBL_MST_UDFFORSE')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF)
                                                    ->where('BRID_REF','=',$BRID_REF);
                                                                       
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF);
                                     
                    

            $objUdfSEData = DB::table('TBL_MST_UDFFORSE')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray();   
            $objCountUDF = count($objUdfSEData);        

            
            $objSalesPerson = DB::table('TBL_MST_EMPLOYEE')
            ->where('STATUS','=',$Status)
            ->where('SALES_PERSON','=','1')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->select('EMPID','EMPCODE','FNAME','MNAME','LNAME')
            ->get()
            ->toArray();
            
            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFORSE")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF)
                        {       
                        $query->select('UDFSEID')->from('TBL_MST_UDFFORSE')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF)
                                        ->where('BRID_REF','=',$BRID_REF);
                                                   
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF);       
            

            $objUdfSEData2 = DB::table('TBL_MST_UDFFORSE')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->union($ObjUnionUDF2)
                ->get()->toArray();     
                $FormId = $this->form_id;

                $AlpsStatus =   $this->AlpsStatus();
                $ActionStatus   =   "disabled";

                $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view('transactions.sales.SalesEnquiry.trnfrm35view',compact(['objSE','objRights','objCount1','objSPID',
           'objCount2','objSEMAT','objSEUDF','objUdfSEData','objCountUDF','FormId',
           'objSalesPerson','objsubglcode','objUdfSEData2','objenquirymedia','objlastENQDT','objPriority',
           'objPackingType','objUOM','objenquirymedia2','objPriority2','AlpsStatus','ActionStatus','TabSetting']));
        }
     
    }

   
   public function update(Request $request){
        
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]))
            {
                $req_data[$i] = [
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'MAIN_QTY'          => $request['SE_QTY_'.$i],
                    'ALTUOMID_REF'      => $request['ALT_UOMID_REF_'.$i],
                    'EDD'               => $request['EDD_'.$i],
                    'TARGETPRICE'       => (Empty($request['TARGETPRICE_'.$i]) !="true" ? $request['TARGETPRICE_'.$i] : 0),
                    'PTID_REF'          => (Empty($request['PTID_REF_'.$i]) !="true" ? $request['PTID_REF_'.$i] : NULL),
                    'PACKUOMID_REF'     => $request['PACKUOMID_REF_'.$i],
                    'PACK_QTY'          => (Empty($request['PACK_QTY_'.$i])!="true" ? $request['PACK_QTY_'.$i] : 0),
                    'ITEM_SPECI'         => $request['Itemspec_'.$i],
                ];
            }
        }
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        
        for ($i=0; $i<=$r_count2; $i++)
        {
                if(isset($request['SEID_REF_'.$i]))
                {
                    $reqdata3[$i] = [
                        'SEID_REF'      => $request['SEID_REF_'.$i],
                        'SEQUVALUE'     => $request['udfvalue_'.$i],
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
            $ENQNO = $request['ENQNO'];
            $ENQDT = $request['ENQDT'];
            $GLID_REF = $request['GLID_REF'];
            $SLID_REF = $request['SLID_REF'];
            $EMID_REF = $request['EMID_REF'];
            $ENQBY = $request['ENQBY'];
            $PRIORITYID_REF = $request['PRIORITYID_REF'];
            $SPID_REF = $request['SPID_REF'];
            $PROSPECTREFNO = $request['PROSPECTREFNO'];
            $CONV_PROLTY = $request['CONV_PROLTY'];
            $APPROXV = $request['APPROXV'];
            $REMARKS = $request['REMARKS'];

            $log_data = [ 
                $ENQNO,$ENQDT,$GLID_REF,$SLID_REF,$EMID_REF,$ENQBY,$PRIORITYID_REF,$SPID_REF,$PROSPECTREFNO,
                $CONV_PROLTY,$APPROXV,$REMARKS,$FYID_REF,$CYID_REF, $BRID_REF, $VTID_REF,
                $XMLMAT, $XMLUDF, $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
            ];

           
            
            $sp_result = DB::select('EXEC SP_SE_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data); 
    
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $ENQNO. ' Sucessfully Updated.']);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
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
        
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]))
            {
                $req_data[$i] = [
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'MAIN_QTY'          => $request['SE_QTY_'.$i],
                    'ALTUOMID_REF'      => $request['ALT_UOMID_REF_'.$i],
                    'EDD'               => $request['EDD_'.$i],
                    'TARGETPRICE'       => (Empty($request['TARGETPRICE_'.$i]) !="true" ? $request['TARGETPRICE_'.$i] : 0),
                    'PTID_REF'          => (Empty($request['PTID_REF_'.$i]) !="true" ? $request['PTID_REF_'.$i] : NULL),
                    'PACKUOMID_REF'     => $request['PACKUOMID_REF_'.$i],
                    'PACK_QTY'          => (Empty($request['PACK_QTY_'.$i])!="true" ? $request['PACK_QTY_'.$i] : 0),
                    'ITEM_SPECI'         => $request['Itemspec_'.$i],
                ];
            }
        }
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        
        for ($i=0; $i<=$r_count2; $i++)
        {
                if(isset($request['SEID_REF_'.$i]))
                {
                    $reqdata3[$i] = [
                        'SEID_REF'      => $request['SEID_REF_'.$i],
                        'SEQUVALUE'     => $request['udfvalue_'.$i],
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
            $ENQNO = $request['ENQNO'];
            $ENQDT = $request['ENQDT'];
            $GLID_REF = $request['GLID_REF'];
            $SLID_REF = $request['SLID_REF'];
            $EMID_REF = $request['EMID_REF'];
            $ENQBY = $request['ENQBY'];
            $PRIORITYID_REF = $request['PRIORITYID_REF'];
            $SPID_REF = $request['SPID_REF'];
            $PROSPECTREFNO = $request['PROSPECTREFNO'];
            $CONV_PROLTY = $request['CONV_PROLTY'];
            $APPROXV = $request['APPROXV'];
            $REMARKS = $request['REMARKS'];

            $log_data = [ 
                $ENQNO,$ENQDT,$GLID_REF,$SLID_REF,$EMID_REF,$ENQBY,$PRIORITYID_REF,$SPID_REF,$PROSPECTREFNO,
                $CONV_PROLTY,$APPROXV,$REMARKS,$FYID_REF,$CYID_REF, $BRID_REF, $VTID_REF,
                $XMLMAT, $XMLUDF, $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
            ];

            
            $sp_result = DB::select('EXEC SP_SE_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data); 
            
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $ENQNO. ' Sucessfully Approved.']);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }    
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
                $TABLE      =   "TBL_TRN_SLEQ01_HDR";
                $FIELD      =   "SEQID";
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
        $TABLE      =   "TBL_TRN_SLEQ01_HDR";
        $FIELD      =   "SEQID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
         'NT'  => 'TBL_TRN_SLEQ01_MAT',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_SLEQ01_UDF',
           ];
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_SE  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

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
    
    $image_path         =   "docs/company".$CYID_REF."/SalesEnquiry";     
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
        return redirect()->route("transaction",[35,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
    
       return redirect()->route("transaction",[35,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("transaction",[35,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("transaction",[35,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("transaction",[35,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

    public function checkse(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $ENQNO = $request->ENQNO;
        
        $objSE = DB::table('TBL_TRN_SLEQ01_HDR')
        ->where('TBL_TRN_SLEQ01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLEQ01_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_SLEQ01_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_SLEQ01_HDR.ENQNO','=',$ENQNO)
        ->select('TBL_TRN_SLEQ01_HDR.SEQID')
        ->first();
        
        if($objSE){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate Enquiry No.']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }


    
}
