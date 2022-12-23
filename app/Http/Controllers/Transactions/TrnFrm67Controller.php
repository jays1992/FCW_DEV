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

class TrnFrm67Controller extends Controller
{
    protected $form_id = 67;
    protected $vtid_ref   = 67;
    protected $view     = "transactions.Purchase.BlanketPurchaseOrder.trnfrm67";
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

        $objDataList	=	DB::select("select hdr.BPOID,hdr.BPO_NO,hdr.BPO_DT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.BPOID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_PROR03_HDR hdr
                            on a.VID = hdr.BPOID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_SUBLEDGER sl ON hdr.VID_REF = sl.SGLID  
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.BPOID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );

        return view($this->view,compact(['REQUEST_DATA','FormId','objRights','objDataList','FormId']));
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
        
       
        
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $objglcode = DB::table('TBL_MST_DEPARTMENT')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('STATUS','=',$Status)
        ->select('*')
        ->get()
        ->toArray();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_PROR03_HDR',
            'HDR_ID'=>'BPOID',
            'HDR_DOC_NO'=>'BPO_NO',
            'HDR_DOC_DT'=>'BPO_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
        
        
        

        $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
            WHERE  CYID_REF = ? AND BRID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by TNC_CODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);

        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_BLANKET_PO")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('BLPOID')->from('TBL_MST_UDFFOR_BLANKET_PO')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                     
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                
                   

        $objUdf  = DB::table('TBL_MST_UDFFOR_BLANKET_PO')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdf);
    //dd($objUdf);


    




        $objlastVQ_DT = $this->LastApprovedDocDate(); 
                    
        $FormId  = $this->form_id;

        $objCOMPANY = DB::table('TBL_MST_COMPANY')
        ->where('CYID','=',$CYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_COMPANY.*')
        ->first();

        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

    return view($this->view.'add', compact(['AlpsStatus','FormId','objTNCHeader','objglcode','objUdf','objCountUDF',
                                            'objCOMPANY','objlastVQ_DT','TabSetting','doc_req','docarray']));       
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
                   <button class="btn removeTNC DTNC" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i>
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


   


    public function getItemDetails(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $AlpsStatus =   $this->AlpsStatus();
      
        $CODE       =   $request['CODE'];
        $NAME       =   $request['NAME'];
        $MUOM       =   $request['MUOM'];
        $GROUP      =   $request['GROUP'];
        $CTGRY      =   $request['CTGRY'];
        $BUNIT      =   $request['BUNIT'];
        $APART      =   $request['APART'];
        $CPART      =   $request['CPART'];
        $OPART      =   $request['OPART'];
      
        $sp_popup = [
            $CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART
        ]; 
        
        $ObjItem = DB::select('EXEC sp_get_items_popup_enquiry ?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);
                
        $row        =   '';
      
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
      
                $StdCost=$STDCOST;
                $Taxid[0]=0;


                $row = $row.'<tr id="item_'.$ITEMID.'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1"  ></td>';
                $row = $row.'<td style="width:10%;">'.$ICODE;
                $row = $row.'<input type="hidden" id="txtitem_'.$ITEMID.'" data-desc="'.$ICODE.'" value="'.$ITEMID.'"/></td>
                <td style="width:10%;" id="itemname_'.$ITEMID.'" >'.$NAME;
                $row = $row.'<input type="hidden" id="txtitemname_'.$ITEMID.'" data-desc="'.$ITEM_SPECI.'" value="'.$NAME.'"/></td>';
                $row = $row.'<td style="width:8%;" id="itemuom_'.$ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ITEMID.'" data-desc="'.$Alt_UOM.'" 
                data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'" value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>';
                $row = $row.'<td style="width:8%;" id="uomqty_'.$ITEMID.'" ><input type="hidden" id="txtuomqty_'.$ITEMID.'" data-desc="'.$TOQTY.'" value="'.$ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                $row = $row.'<td style="width:8%;" id="irate_'.$ITEMID.'"><input type="hidden" id="txtirate_'.$ITEMID.'" data-desc="'.$FROMQTY.'" value="'.$StdCost.'"/>'.$GroupName.'</td>';
                $row = $row.'<td style="width:8%;" id="itax_'.$ITEMID.'"><input type="hidden" id="txtitax_'.$ITEMID.'" data-desc="'.$Taxid[0].'" value=""/>'.$Categoryname.'</td>
                <td style="width:8%;">'.$BusinessUnit.'</td>
                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                <td style="width:8%;" id="ise_'.$ITEMID.'"><input type="hidden" id="txtise_'.$ITEMID.'" value=""/>Authorized</td>
                </tr>'; 


            } 
      
            echo $row;
                               
        }           
        else{
            echo '<tr><td colspan="12"> Record not found.</td></tr>';
        }
      
        exit();
      }


    public function getALLItemDetails(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $taxstate = $request['taxstate'];
       
        $VENDORID= $request['vendorid'];
       
        $StdCost = 0;

        $AlpsStatus =   $this->AlpsStatus();

        $objVendorMst =  DB::select('SELECT TOP 1 VID,VCODE,VGID_REF FROM TBL_MST_VENDOR  WHERE VID = ?', [ $VENDORID ]);  
        $VGID = $objVendorMst[0]->VGID_REF;

        $objVPLHDR =  DB::select('SELECT VPLID,VGID_REF,VID_REF FROM TBL_MST_VENDORPRICELIST_HDR  where VGID_REF=? AND STATUS=? AND CYID_REF=?', [$VGID, 'A',$CYID_REF]);   //check vendor group

          
        if(empty($objVPLHDR)){
            $objVPLHDR =  DB::select('SELECT VPLID,VGID_REF,VID_REF FROM TBL_MST_VENDORPRICELIST_HDR  where VID_REF=? AND STATUS=?', [$VENDORID, 'A']); //check vendor
         
        }

            $ObjItem =  DB::select("SELECT * FROM TBL_MST_ITEM 
                    WHERE CYID_REF = '$CYID_REF' AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS ='$Status'");
        
       

                if(!empty($ObjItem)){

                    foreach ($ObjItem as $index=>$dataRow){
                        $Taxid = [];
                        $ObjLIST=[];
                            if(!empty($objVPLHDR)){
                                $ObjLIST =   DB::table('TBL_MST_VENDORPRICELIST_MAT')  
                                    ->select('*')
                                    ->where('VPLID_REF','=',$objVPLHDR[0]->VPLID)
                                    ->where('ITEMID_REF','=',$dataRow->ITEMID)
                                    ->where('UOMID_REF','=',$dataRow->MAIN_UOMID_REF)
                                    ->first();
                            }
                            
                        if(!empty($ObjLIST)){
                                    $ObjInTax = $ObjLIST->GST_IN_LP; 

                                    $RATE = $ObjLIST->LP;  
                                                       
                                    if ($ObjInTax == 1){
                                       
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
                                        $ObjStdCost =  ($ObjLIST->LP*100)/$ObjTaxDet;
                                        $StdCost = $ObjStdCost;
                                        
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
                                        foreach ($ObjTax as $tindex=>$tRow)
                                        {   
                                            if($tRow->NRATE !== '')
                                                {
                                                array_push($Taxid,$tRow->NRATE);
                                                }
                                            }
                                        $StdCost = $ObjLIST->LP;                                       
                                    }
                        }
                        else
                        {
                            //IF VENDOR PRICE LIST NOT FOUND
                            
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
                            //$RATE = $dataRow->STDCOST;
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

                        
                           
                            $row = '';
                            if($taxstate != "OutofState"){
                                $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                                $row = $row.'<td style="width:8%;">'.$dataRow->ICODE;
                                $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'"
                                value="'.$dataRow->ITEMID.'"/></td>
                                <td style="width:10%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                                $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'"
                                value="'.$dataRow->NAME.'"/></td>';
                                $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                                value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                                $row = $row.'<td style="width:8%;" id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'"
                                value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                                $row = $row.'<td style="width:8%;" id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="'.$FROMQTY.'"
                                value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                                $row = $row.'<td style="width:8%;" id="itax_'.$dataRow->ITEMID.'"><input type="hidden" id="txtitax_'.$dataRow->ITEMID.'" data-desc="'.$Taxid[0].'"
                                value="'.$Taxid[1].'"/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                                <td style="width:8%;">'.$BusinessUnit.'</td>
                                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                                <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                                <td style="width:8%;" id="ise_'.$ObjItem[0]->ITEMID.'"><input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'" 
                                value=""/>Authorized</td>
                                </tr>';
                                }
                                else{
                                    $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                                    $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                                    $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'"
                                    value="'.$dataRow->ITEMID.'"/></td>
                                    <td style="width:10%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                                    $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'"
                                    value="'.$dataRow->NAME.'"/></td>';
                                    $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                                    value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                                    $row = $row.'<td style="width:8%;" id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'"
                                    value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                                    $row = $row.'<td style="width:8%;" id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="'.$FROMQTY.'"
                                    value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                                    $row = $row.'<td style="width:8%;" id="itax_'.$dataRow->ITEMID.'"><input type="hidden" id="txtitax_'.$dataRow->ITEMID.'" data-desc="'.$Taxid[0].'"
                                    value=""/>'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                                    <td style="width:8%;">'.$BusinessUnit.'</td>
                                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                                    <td style="width:8%;" id="ise_'.$ObjItem[0]->ITEMID.'"><input type="hidden" id="txtise_'.$ObjItem[0]->ITEMID.'" 
                                    value=""/>Authorized</td>
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

    

   

    public function attachment($id){

        $FormId = $this->form_id;
        if(!is_null($id))
        {
            $objMst = DB::table("TBL_TRN_PROR03_HDR")
                        ->where('BPOID','=',$id)
                        ->select('*')
                        ->first();        

            $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
                        ->where('VTID','=',$this->vtid_ref)
                        ->select('VTID','VCODE','DESCRIPTIONS','INDATE')
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
                            
                return view($this->view.'attachment',compact(['FormId','objMst','objMstVoucherType','objAttachments']));
        }

    }

    
   public function save(Request $request) {
    
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i]))
            {
                $req_data[$i] = [
                   
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'UOMID_REF' => $request['UOMID_REF_'.$i],
                    'ITEMSPECI' => $request['ITEMSPECI_'.$i],
                    'RFQ_QTY' => $request['RFQ_QTY_'.$i],
                    'RATEP_UOM' => $request['RATEPUOM_'.$i],
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
                $udffield_Data[$i]['UDF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
           }
            
        }
        if(count($udffield_Data)>0){
            $udffield_wrapped["UDF1"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
        }else{
            $XMLUDF = NULL;
        }
        
        $VTID_REF     =   $this->vtid_ref;
        
        $USERID_REF = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $BPO_NO = $request['VQ_NO'];
        $BPO_DT = $request['VQ_DT'];
        $DEPID_REF = $request['GLID_REF'];
        $VID_REF = $request['VID_REF'];
        $BPO_VFR = $request['VFRDT'];
        $BPO_VTO = $request['VTODT'];
        $CREDIT_DAYS = $request['CREDITDAYS'];
        

        // @BPO_NO VARCHAR(20),@BPO_DT DATE,@DEPID_REF INT,@VID_REF INT,@BPO_VFR DATE,@BPO_VTO DATE,@CREDIT_DAYS INT,  
        // @CYID_REF INT,@BRID_REF INT,@FYID_REF INT,@VTID_REF INT,@XMLMAT XML,@XMLTNC XML,@XMLUDF XML,@USERID_REF INT,  
        // @UPDATE date,@UPTIME time,@ACTION varchar(30),@IPADDRESS varchar(30)  

        $log_data = [ 
            $BPO_NO,        $BPO_DT,    $DEPID_REF,    $VID_REF,      $BPO_VFR,         $BPO_VTO,     
            $CREDIT_DAYS,   $CYID_REF,  $BRID_REF,     $FYID_REF,     $VTID_REF,        $XMLMAT, 
            $XMLTNC,        $XMLUDF,    $USERID_REF,   Date('Y-m-d'), Date('h:i:s.u'),  $ACTIONNAME,
            $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_BPO_IN ?,?,?,?,?,?, ?,?,?,?,?,?,   ?,?,?,?,?,?, ?', $log_data);     

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){

            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
     }


    public function edit($id=NULL){       
        
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

            //get user approval data
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

            
            $objglcode = DB::table('TBL_MST_DEPARTMENT')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('STATUS','=',$Status)
                ->select('*')
                ->get()
                ->toArray();
            
            
            $objMstResponse = DB::table('TBL_TRN_PROR03_HDR')
                ->where('FYID_REF','=',Session::get('FYID_REF'))
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('BPOID','=',$id)
                ->select('*')
                ->first();
          
            $objList1 = DB::table('TBL_TRN_PROR03_MAT')                    
                ->where('BPOID_REF','=',$id)
                ->select( '*')
                ->orderBy('BPOMATID','ASC')
                ->get()->toArray();


            $objList1 = DB::table('TBL_TRN_PROR03_MAT')                    
            ->where('TBL_TRN_PROR03_MAT.BPOID_REF','=',$id)
                      ->leftJoin('TBL_MST_ITEM','TBL_TRN_PROR03_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                
            ->leftJoin('TBL_MST_UOM','TBL_TRN_PROR03_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                
            ->select( 
                'TBL_TRN_PROR03_MAT.*',
                'TBL_MST_ITEM.ITEMID',
                'TBL_MST_ITEM.ICODE',
                'TBL_MST_ITEM.NAME',
                'TBL_MST_UOM.UOMID',
                'TBL_MST_UOM.UOMCODE',
                'TBL_MST_UOM.DESCRIPTIONS',
                'TBL_MST_ITEM.ALPS_PART_NO',
                'TBL_MST_ITEM.CUSTOMER_PART_NO',
                'TBL_MST_ITEM.OEM_PART_NO',
            )
            ->orderBy('TBL_TRN_PROR03_MAT.BPOMATID','ASC')
            ->get()->toArray();    

            $objList1Count = count($objList1);
            if($objList1Count==0){
                $objList1Count=1;
            }    



                $objOSOMAT = DB::table('TBL_TRN_PROR03_MAT')                    
                                ->where('TBL_TRN_PROR03_MAT.BPOID_REF','=',$id)
                                ->select('TBL_TRN_PROR03_MAT.*')
                                ->orderBy('TBL_TRN_PROR03_MAT.BPOMATID','ASC')
                                ->get()->toArray();
                
                $objglcode2 =[];
                if(isset($objMstResponse->DEPID_REF) && $objMstResponse->DEPID_REF !=""){
                $objglcode2 = DB::table('TBL_MST_DEPARTMENT')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('DEPID','=',$objMstResponse->DEPID_REF)
                        ->select('*')
                        ->first();
                }

                $objvendorcode2 =[];
                if(isset($objMstResponse->VID_REF) && $objMstResponse->VID_REF !=""){
                $objvendorcode2 = DB::table('TBL_MST_SUBLEDGER')
                        ->where('BELONGS_TO','=','Vendor')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('SGLID','=',$objMstResponse->VID_REF)    
                        ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                        ->first();
                }


                $objSavedTNC =  DB::table('TBL_TRN_PROR03_TNC')
                ->where('BPOID_REF','=',$id)
                ->select('*')
                ->get()->toArray();


                $objSavedTNCHeader=[];
                $objSavedTNCHeaderDTL=[];

                if(!empty($objSavedTNC)){
                    $objSavedTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
                                WHERE  TNCID = ?', [$objSavedTNC[0]->TNCID_REF ]);

                    $objSavedTNCHeaderDTL = DB::select('SELECT * FROM TBL_MST_TNC_DETAILS  
                                 WHERE  TNCID_REF = ?', [$objSavedTNC[0]->TNCDID_REF ]);
                   
                }
                
            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
                WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                order by TNC_CODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);

           
            
            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_BLANKET_PO")->select('*')
                ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                            {       
                            $query->select('BLPOID')->from('TBL_MST_UDFFOR_BLANKET_PO')
                                            ->where('STATUS','=','A')
                                            ->where('PARENTID','=',0)
                                            ->where('DEACTIVATED','=',0)
                                            ->where('CYID_REF','=',$CYID_REF);
                                                                
                })->where('DEACTIVATED','=',0)
                ->where('STATUS','<>','C')                    
                ->where('CYID_REF','=',$CYID_REF);
                        


            $objUdf  = DB::table('TBL_MST_UDFFOR_BLANKET_PO')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray();   
            $objCountUDF = count($objUdf);
           

            $objtempUdf = $objUdf;
            foreach ($objtempUdf as $index => $udfvalue) {

                $objSavedUDF =  DB::table('TBL_TRN_PROR03_UDF')
                ->where('BPOID_REF','=',$id)
                ->where('UDF','=',$udfvalue->BLPOID)
                ->select('VALUE')
                ->first();

                if(!empty($objSavedUDF)){
                    $objUdf[$index]->UDF_VALUE = $objSavedUDF->VALUE;
                }else{
                    $objUdf[$index]->UDF_VALUE ='';
                }
            }
            $objtempUdf = [];


            $objlastVQ_DT = $this->LastApprovedDocDate(); 
                       
            $FormId  = $this->form_id;

          
            $objSOTNC = DB::table('TBL_TRN_PROR03_TNC')                    
                             ->where('BPOID_REF','=',$id)
                             ->select('*')
                             ->orderBy('BPOTNCID','ASC')
                             ->get()->toArray();
            
            
            $objCount2 = count($objSOTNC);

            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by TNC_CODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);

            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
            ->get() ->toArray(); 

            $objlastVQ_DT = DB::select('SELECT MAX(BPO_DT) BPO_DT FROM TBL_TRN_PROR03_HDR  
                    WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
                    [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);

            $objCOMPANY = DB::table('TBL_MST_COMPANY')
            ->where('CYID','=',$CYID_REF)
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_COMPANY.*')
            ->first();

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
            if($objAttachments)
            {
                $objCountAttachment = count($objAttachments);
            }
            else
            {
                $objCountAttachment = "0";
            }
               
            $AlpsStatus =   $this->AlpsStatus();
            $ActionStatus   =   "";
            
            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view($this->view.'edit', compact(['AlpsStatus','FormId','objRights','objTNCHeader','objUdf','objCountUDF','objlastVQ_DT',
                'objglcode','objMstResponse','objList1','objList1Count','objglcode2','objvendorcode2','objSavedTNCHeader','objSOTNC',
                'objCOMPANY','objTNCDetails','objCount2','objCountAttachment','ActionStatus','TabSetting']));     
        }
    
   } 

    
    //update the data
    public function update(Request $request)
    {

        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i]))
            {
                $req_data[$i] = [
                
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'UOMID_REF' => $request['UOMID_REF_'.$i],
                    'ITEMSPECI' => $request['ITEMSPECI_'.$i],
                    'RFQ_QTY' => $request['RFQ_QTY_'.$i],
                    'RATEP_UOM' => $request['RATEPUOM_'.$i],
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
                $udffield_Data[$i]['UDF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
        }
            
        }
        if(count($udffield_Data)>0){
            $udffield_wrapped["UDF1"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
        }else{
            $XMLUDF = NULL;
        }
    
    
    
        $VTID_REF     =   $this->vtid_ref;
        
        $USERID_REF = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
    
        $BPO_NO = $request['VQ_NO'];
        $BPO_DT = $request['VQ_DT'];
        $DEPID_REF = $request['GLID_REF'];
        $VID_REF = $request['VID_REF'];
        $BPO_VFR = $request['VFRDT'];
        $BPO_VTO = $request['VTODT'];
        $CREDIT_DAYS = $request['CREDITDAYS'];
        
    
        // @BPO_NO VARCHAR(20),@BPO_DT DATE,@DEPID_REF INT,@VID_REF INT,@BPO_VFR DATE,@BPO_VTO DATE,@CREDIT_DAYS INT,  
        // @CYID_REF INT,@BRID_REF INT,@FYID_REF INT,@VTID_REF INT,@XMLMAT XML,@XMLTNC XML,@XMLUDF XML,@USERID_REF INT,  
        // @UPDATE date,@UPTIME time,@ACTION varchar(30),@IPADDRESS varchar(30)   
    
        $log_data = [ 
            $BPO_NO,        $BPO_DT,    $DEPID_REF,    $VID_REF,      $BPO_VFR,         $BPO_VTO,     
            $CREDIT_DAYS,   $CYID_REF,  $BRID_REF,     $FYID_REF,     $VTID_REF,        $XMLMAT, 
            $XMLTNC,        $XMLUDF,    $USERID_REF,   Date('Y-m-d'), Date('h:i:s.u'),  $ACTIONNAME,
            $IPADDRESS
        ];
    
        
        $sp_result = DB::select('EXEC SP_BPO_UP ?,?,?,?,?,?, ?,?,?,?,?,?,   ?,?,?,?,?,?, ?', $log_data);    
      
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $BPO_NO. ' Sucessfully Updated.']);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();  
    
    }   

    public function view($id=NULL){       
        
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

            //get user approval data
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

            
            $objglcode = DB::table('TBL_MST_DEPARTMENT')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('STATUS','=',$Status)
                ->select('*')
                ->get()
                ->toArray();
            
            
            $objMstResponse = DB::table('TBL_TRN_PROR03_HDR')
                ->where('FYID_REF','=',Session::get('FYID_REF'))
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('BPOID','=',$id)
                ->select('*')
                ->first();
          
            $objList1 = DB::table('TBL_TRN_PROR03_MAT')                    
                ->where('BPOID_REF','=',$id)
                ->select( '*')
                ->orderBy('BPOMATID','ASC')
                ->get()->toArray();


            $objList1 = DB::table('TBL_TRN_PROR03_MAT')                    
            ->where('TBL_TRN_PROR03_MAT.BPOID_REF','=',$id)
                      ->leftJoin('TBL_MST_ITEM','TBL_TRN_PROR03_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                
            ->leftJoin('TBL_MST_UOM','TBL_TRN_PROR03_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                
            ->select( 
                'TBL_TRN_PROR03_MAT.*',
                'TBL_MST_ITEM.ITEMID',
                'TBL_MST_ITEM.ICODE',
                'TBL_MST_ITEM.NAME',
                'TBL_MST_UOM.UOMID',
                'TBL_MST_UOM.UOMCODE',
                'TBL_MST_UOM.DESCRIPTIONS',
                'TBL_MST_ITEM.ALPS_PART_NO',
                'TBL_MST_ITEM.CUSTOMER_PART_NO',
                'TBL_MST_ITEM.OEM_PART_NO',
            )
            ->orderBy('TBL_TRN_PROR03_MAT.BPOMATID','ASC')
            ->get()->toArray();    

            $objList1Count = count($objList1);
            if($objList1Count==0){
                $objList1Count=1;
            }    



                $objOSOMAT = DB::table('TBL_TRN_PROR03_MAT')                    
                                ->where('TBL_TRN_PROR03_MAT.BPOID_REF','=',$id)
                                ->select('TBL_TRN_PROR03_MAT.*')
                                ->orderBy('TBL_TRN_PROR03_MAT.BPOMATID','ASC')
                                ->get()->toArray();
                
                $objglcode2 =[];
                if(isset($objMstResponse->DEPID_REF) && $objMstResponse->DEPID_REF !=""){
                $objglcode2 = DB::table('TBL_MST_DEPARTMENT')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('DEPID','=',$objMstResponse->DEPID_REF)
                        ->select('*')
                        ->first();
                }

                $objvendorcode2 =[];
                if(isset($objMstResponse->VID_REF) && $objMstResponse->VID_REF !=""){
                $objvendorcode2 = DB::table('TBL_MST_SUBLEDGER')
                        ->where('BELONGS_TO','=','Vendor')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('SGLID','=',$objMstResponse->VID_REF)    
                        ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                        ->first();
                }


                $objSavedTNC =  DB::table('TBL_TRN_PROR03_TNC')
                ->where('BPOID_REF','=',$id)
                ->select('*')
                ->get()->toArray();


                $objSavedTNCHeader=[];
                $objSavedTNCHeaderDTL=[];

                if(!empty($objSavedTNC)){
                    $objSavedTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
                                WHERE  TNCID = ?', [$objSavedTNC[0]->TNCID_REF ]);

                    $objSavedTNCHeaderDTL = DB::select('SELECT * FROM TBL_MST_TNC_DETAILS  
                                 WHERE  TNCID_REF = ?', [$objSavedTNC[0]->TNCDID_REF ]);
                   
                }
                
            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
                WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                order by TNC_CODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);

           
            
            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_BLANKET_PO")->select('*')
                ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                            {       
                            $query->select('BLPOID')->from('TBL_MST_UDFFOR_BLANKET_PO')
                                            ->where('STATUS','=','A')
                                            ->where('PARENTID','=',0)
                                            ->where('DEACTIVATED','=',0)
                                            ->where('CYID_REF','=',$CYID_REF);
                                                                
                })->where('DEACTIVATED','=',0)
                ->where('STATUS','<>','C')                    
                ->where('CYID_REF','=',$CYID_REF);
                        


            $objUdf  = DB::table('TBL_MST_UDFFOR_BLANKET_PO')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray();   
            $objCountUDF = count($objUdf);
           

            $objtempUdf = $objUdf;
            foreach ($objtempUdf as $index => $udfvalue) {

                $objSavedUDF =  DB::table('TBL_TRN_PROR03_UDF')
                ->where('BPOID_REF','=',$id)
                ->where('UDF','=',$udfvalue->BLPOID)
                ->select('VALUE')
                ->first();

                if(!empty($objSavedUDF)){
                    $objUdf[$index]->UDF_VALUE = $objSavedUDF->VALUE;
                }else{
                    $objUdf[$index]->UDF_VALUE ='';
                }
            }
            $objtempUdf = [];


            $objlastVQ_DT = DB::select('SELECT MAX(VQ_DT) VQ_DT FROM TBL_TRN_VDQT01_HDR  
                    WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND VTID_REF = ? AND STATUS = ?', 
                    [$CYID_REF, $BRID_REF, $FYID_REF, $this->vtid_ref, 'A' ]);
                       
            $FormId  = $this->form_id;

          
            $objSOTNC = DB::table('TBL_TRN_PROR03_TNC')                    
                             ->where('BPOID_REF','=',$id)
                             ->select('*')
                             ->orderBy('BPOTNCID','ASC')
                             ->get()->toArray();
            
            
            $objCount2 = count($objSOTNC);

            $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by TNC_CODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);

            $objTNCDetails = DB::table('TBL_MST_TNC_DETAILS')->select('*')
            ->get() ->toArray(); 

            $objlastVQ_DT = DB::select('SELECT MAX(BPO_DT) BPO_DT FROM TBL_TRN_PROR03_HDR  
                    WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND VTID_REF = ? AND STATUS = ?', 
                    [$CYID_REF, $BRID_REF, $FYID_REF, $this->vtid_ref, 'A' ]);

            $objCOMPANY = DB::table('TBL_MST_COMPANY')
            ->where('CYID','=',$CYID_REF)
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_COMPANY.*')
            ->first();

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
            if($objAttachments)
            {
                $objCountAttachment = count($objAttachments);
            }
            else
            {
                $objCountAttachment = "0";
            }
               
            $AlpsStatus =   $this->AlpsStatus();
            $ActionStatus   =   "disabled";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view($this->view.'view', compact(['AlpsStatus','FormId','objRights','objTNCHeader','objUdf','objCountUDF','objlastVQ_DT',
                'objglcode','objMstResponse','objList1','objList1Count','objglcode2','objvendorcode2','objSavedTNCHeader','objSOTNC',
                'objCOMPANY','objTNCDetails','objCount2','objCountAttachment','ActionStatus','TabSetting']));     
        }
    
   }

    
    
    
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
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i]))
            {
                $req_data[$i] = [
                
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'UOMID_REF' => $request['UOMID_REF_'.$i],
                    'ITEMSPECI' => $request['ITEMSPECI_'.$i],
                    'RFQ_QTY' => $request['RFQ_QTY_'.$i],
                    'RATEP_UOM' => $request['RATEPUOM_'.$i],
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
                $udffield_Data[$i]['UDF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
        }
            
        }
        if(count($udffield_Data)>0){
            $udffield_wrapped["UDF1"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
        }else{
            $XMLUDF = NULL;
        }
    
    
    
        $VTID_REF     =   $this->vtid_ref;
        
        $USERID_REF = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
    
        $BPO_NO = $request['VQ_NO'];
        $BPO_DT = $request['VQ_DT'];
        $DEPID_REF = $request['GLID_REF'];
        $VID_REF = $request['VID_REF'];
        $BPO_VFR = $request['VFRDT'];
        $BPO_VTO = $request['VTODT'];
        $CREDIT_DAYS = $request['CREDITDAYS'];
        
        $log_data = [ 
            $BPO_NO,        $BPO_DT,    $DEPID_REF,    $VID_REF,      $BPO_VFR,         $BPO_VTO,     
            $CREDIT_DAYS,   $CYID_REF,  $BRID_REF,     $FYID_REF,     $VTID_REF,        $XMLMAT, 
            $XMLTNC,        $XMLUDF,    $USERID_REF,   Date('Y-m-d'), Date('h:i:s.u'),  $ACTIONNAME,
            $IPADDRESS
        ];
    
       
        $sp_result = DB::select('EXEC SP_BPO_UP ?,?,?,?,?,?, ?,?,?,?,?,?,   ?,?,?,?,?,?, ?', $log_data);    
       
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $BPO_NO. ' Sucessfully Approved.']);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();      
    }

    public function MultiApprove(Request $request)
    {

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
                    foreach ($sp_listing_result as $key=>$listitem)
                {  
                    $record_status = 0;
                    $Approvallevel = "APPROVAL".$listitem->LAVELS;
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
                $VTID_REF   =   $this->vtid_ref;  //voucher type id
                $CYID_REF   =   Auth::user()->CYID_REF;
                $BRID_REF   =   Session::get('BRID_REF');
                $FYID_REF   =   Session::get('FYID_REF');       
                $TABLE      =   "TBL_TRN_PROR03_HDR";
                $FIELD      =   "BPOID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
            
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_BPO ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
    
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
        $TABLE      =   "TBL_TRN_PROR03_HDR";
        $FIELD      =   "BPOID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_PROR03_MAT',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_PROR03_TNC',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_PROR03_UDF',
        ];
        
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_BPO  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

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
        
		$image_path         =   "docs/company".$CYID_REF."/BlanketPurchaseOrder";     
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
        $SONO = $request->SONO;
        
        $objSO = DB::table('TBL_TRN_SLSI02_HDR')
        ->where('TBL_TRN_SLSI02_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLSI02_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_SLSI02_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_SLSI02_HDR.SSI_NO','=',$SONO)
        ->select('TBL_TRN_SLSI02_HDR.SSIID')
        ->first();
        
        if($objSO){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate SONO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }

    public function LastApprovedDocDate(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $VTID_REF     =   $this->vtid_ref;
        return $objlastDocDate = DB::select('SELECT MAX(BPO_DT) BPO_DT FROM TBL_TRN_PROR03_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $VTID_REF, $Status ]);

    }
    
}
