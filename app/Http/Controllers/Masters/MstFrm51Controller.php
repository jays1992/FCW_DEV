<?php

namespace App\Http\Controllers\Masters;

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
use Carbon\Carbon;

class MstFrm51Controller extends Controller
{
    protected $form_id = 51;
    protected $vtid_ref   = 51;
    protected $view     = "masters.Purchase.VendorItemInfo.mstfrm51";
   
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];
    
    public function __construct()
    {
        $this->middleware('auth');
    }  

    public function index(){  
        
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 

        $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
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
                end end as STATUS_DESC
                from TBL_MST_AUDITTRAIL a 
                inner join TBL_MST_VENDORITEMINFO_HDR hdr
                on a.VID = hdr.VIINFOID 
                and a.CYID_REF = hdr.CYID_REF 
                and a.BRID_REF = hdr.BRID_REF
                where a.VTID_REF = '$this->vtid_ref'
                and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                and a.ACTID in (select max(ACTID) from TBL_MST_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                ORDER BY hdr.VIINFOID DESC ");

        return view('masters.Purchase.VendorItemInfo.mstfrm51',compact(['objRights','objDataList']));
       

    }

//************************{  getVendor  }******************* */
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

    //*******************************{  getVendorList  }****************** */
    public function getVendorList(){
        return $objUserList = DB::table('TBL_MST_VENDOR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        //->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('VID','VCODE','NAME')
        ->get();
        }

//************************{  getItemGroupList  }******************* */
    public function getItemGroupList(){

        return $objUserList = DB::table('TBL_MST_ITEMGROUP')
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('TBL_MST_ITEMGROUP.*')
        ->limit(13) //newlimit
        ->get();


    }
    
//**********************{  objItemCodeList  }******************* */

            public function objItemCodeList(){                        
            return $objUserList =  DB::table('TBL_MST_VENDORITEMINFO_MAT')
            ->leftJoin('TBL_MST_ITEM', 'TBL_MST_VENDORITEMINFO_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
            ->leftJoin('TBL_MST_UOM', 'TBL_MST_VENDORITEMINFO_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')
            ->leftJoin('TBL_MST_ITEMGROUP', 'TBL_MST_VENDORITEMINFO_MAT.ITEMGID_REF','=','TBL_MST_ITEMGROUP.ITEMGID')
            ->select('TBL_MST_VENDORITEMINFO_MAT.*','TBL_MST_ITEM.*','TBL_MST_UOM.*','TBL_MST_ITEMGROUP.*')
            ->get();          
            } 

//**************************{  add  }*************************** */
    public function add(){   
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $objglcode=array();        
        $d_currency = DB::table('TBL_MST_COMPANY')
        ->where('STATUS','=',$Status)
        ->where('CYID','=',Auth::user()->CYID_REF)
        ->select('TBL_MST_COMPANY.CRID_REF')
        ->first();

        $objcurrency =NULL;
        $objothcurrency =[];
        if(isset($d_currency->CRID_REF) && $d_currency->CRID_REF !=""){
            $objcurrency = $d_currency->CRID_REF;
            $objothcurrency = DB::table('TBL_MST_CURRENCY')
            ->where('STATUS','=',$Status)
            ->where('CRID','<>',$objcurrency)
            ->select('TBL_MST_CURRENCY.*')
            ->get()
            ->toArray();
        }

        $objSON = DB::table('TBL_MST_DOCNO_DEFINITION')
            ->where('VTID_REF','=',$this->vtid_ref)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            //->where('FYID_REF','=',$FYID_REF)
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_DOCNO_DEFINITION.*')
            ->first();

        $objAutoGenNo='';
        if(!empty($objSON )){

            if($objSON->SYSTEM_GRSR == "1")
            {
                if($objSON->PREFIX_RQ == "1")
                {
                    $objAutoGenNo = $objSON->PREFIX;
                }        
                if($objSON->PRE_SEP_RQ == "1")
                {
                    if($objSON->PRE_SEP_SLASH == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'/';
                    }
                    if($objSON->PRE_SEP_HYPEN == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'-';
                    }
                }        
                if($objSON->NO_MAX)
                {   
                    $objAutoGenNo = $objAutoGenNo.str_pad($objSON->LAST_RECORDNO+1, $objSON->NO_MAX, "0", STR_PAD_LEFT);
                }
                
                if($objSON->NO_SEP_RQ == "1")
                {
                    if($objSON->NO_SEP_SLASH == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'/';
                    }
                    if($objSON->NO_SEP_HYPEN == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'-';
                    }
                }
                if($objSON->SUFFIX_RQ == "1")
                {
                    $objAutoGenNo = $objAutoGenNo.$objSON->SUFFIX;
                }
            }
        }  
        

        $objTNCHeader = DB::select('SELECT TNCID, TNC_CODE, TNC_DESC FROM TBL_MST_TNC  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by TNC_CODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);

        $objCalculationHeader = DB::select('SELECT CTID, CTCODE, CTDESCRIPTION FROM TBL_MST_CALCULATION  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by CTCODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);
        
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_VQ_MANAGEMENT")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
            {       
            $query->select('VQMID')->from('TBL_MST_UDFFOR_VQ_MANAGEMENT')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);

            })->where('DEACTIVATED','=',0)
            ->where('STATUS','<>','C')                    
            ->where('CYID_REF','=',$CYID_REF);                   

        $objUdf  = DB::table('TBL_MST_UDFFOR_VQ_MANAGEMENT')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdf);

        $objCurrencyconverter = DB::table('TBL_MST_CRCONVERSION')
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_CRCONVERSION.*')
        ->get()
        ->toArray();

        $objlastVQ_DT = DB::select('SELECT MAX(VQ_DT) VQ_DT FROM TBL_TRN_VDQT01_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF, $FYID_REF, $this->vtid_ref, 'A' ]);
                    
        $FormId  = $this->form_id;
        $objCOMPANY = DB::table('TBL_MST_COMPANY')
        ->where('CYID','=',$CYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_COMPANY.*')
        ->first();

        $AlpsStatus =   $this->AlpsStatus();

    return view($this->view.'add', compact(['AlpsStatus','FormId','objCalculationHeader','objcurrency','objTNCHeader','objothcurrency','objSON',
    'objCurrencyconverter','objUdf','objCountUDF','objAutoGenNo','objlastVQ_DT','objCOMPANY']));       
   }
  
//*******************{  getsubledger  }****************************** */
   public function getsubledger(Request $request){
    $Status = "A";
    $id = $request['id'];

    $ObjData =  DB::select('SELECT SGLID, SGLCODE, SLNAME, SALIAS FROM TBL_MST_SUBLEDGER  
                WHERE STATUS= ? AND GLID_REF = ? order by SGLCODE ASC', [$Status,$id]);

        if(!empty($ObjData)){
        foreach ($ObjData as $index=>$dataRow){        
            $row = '';
            $row = $row.'<tr id="subgl_'.$dataRow->SGLID .'"  class="clssubgl"><td width="50%">'.$dataRow->SGLCODE;
            $row = $row.'<input type="hidden" id="txtsubgl_'.$dataRow->SGLID.'" data-desc="'.$dataRow->SGLCODE .' - ';
            $row = $row.$dataRow->SLNAME. '" value="'.$dataRow->SGLID.'"/></td><td>'.$dataRow->SLNAME.'</td></tr>';
            echo $row;
        }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();

    }

//*****************{  getVdInfo  }******************************** */
    public function getVdInfo(Request $request){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $VID_REF       =   $request['id'];
        $fieldid    = $request['fieldid'];

        $venderInfo = DB::table('TBL_MST_ITEMGROUP')
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('TBL_MST_ITEMGROUP.*')
        ->limit(13) //newlimit
        ->get();

        if(!empty($venderInfo)){
            foreach ($venderInfo as $index=>$dataRow){
                
                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="rfqcode_'.$dataRow->ITEMGID .'"  class="clsrfqid" value="'.$dataRow->ITEMGID.'" ></td>
                <td class="ROW2">'.$dataRow->GROUPCODE;
                $row = $row.'<input type="hidden" id="txtrfqcode_'.$dataRow->ITEMGID.'" data-desc="'.$dataRow->GROUPCODE.'"  data-descdate="'.$dataRow->GROUPCODE.'"
                value="'.$dataRow->ITEMGID.'"/>
                </td>
                <td class="ROW3">'.$dataRow->GROUPCODE.'</td>
                
                </tr>';
                echo $row;
            }
        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
    }

//*********************{  getitemgroup  }************************* */

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
        $ItemGroupId      =   $request['ITEMGID_REF'];

        $sp_popup = [
            $CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART
        ]; 
            
        $ObjItem        = DB::select('EXEC sp_get_items_popup_enquiry ?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);
        
       //echo $ItemGroupId;

        $row        =   '';
      
        
        if(!empty($ObjItem)){
            foreach ($ObjItem as $index=>$dataRow){

                if($dataRow->ITEMGID_REF == $ItemGroupId){       
                                     
            
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

                $row.=' <tr id="item_'.$ITEMID.'" class="clsitemid">
                        <td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1"  ></td>
                        <td style="width:10%;">'.$ICODE.'<input type="hidden" id="txtitem_'.$ITEMID.'" data-desc="'.$ICODE.'" value="'.$ITEMID.'"/></td>
                        <td style="width:10%;" id="itemname_'.$ITEMID.'" >'.$NAME.'<input type="hidden" id="txtitemname_'.$ITEMID.'" data-desc="'.$NAME.'" value="'.$NAME.'"/></td>
                        <td style="width:8%;" id="itemuom_'.$ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ITEMID.'" data-desc="'.$Alt_UOM.'" value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>
                        <td style="width:8%;" id="uomqty_'.$ITEMID.'" ><input type="hidden" id="txtuomqty_'.$ITEMID.'" data-desc="'.$TOQTY.'" value="'.$ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>
                        <td style="width:8%;" id="irate_'.$ITEMID.'"><input type="hidden" id="txtirate_'.$ITEMID.'" data-desc="'.$FROMQTY.'" value="'.$STDCOST.'"/>'.$GroupName.'</td>
                        <td style="width:8%;" id="itax_'.$ITEMID.'"><input type="hidden" id="txtitax_'.$ITEMID.'" />'.$Categoryname.'</td>
                        <td style="width:8%;">'.$BusinessUnit.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].'>'.$ALPS_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].'>'.$CUSTOMER_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].'>'.$OEM_PART_NO.'</td>
                        <td style="width:8%;">Authorized</td>
                        </tr>'; 
            } 
        }
            echo $row;
                                
        }           
        else{
            echo '<tr><td colspan="12"> Record not found.</td></tr>';
        }

        exit();
        
    }

//***********************{  codeduplicate  }************************* */
    public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $VD_NO    = trim($request['VD_NO']);
        
        $objLabel = DB::table('TBL_MST_VENDORPRICELIST_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            //->where('FYID_REF','=',Session::get('FYID_REF'))
            ->where('VPL_NO','=',$VD_NO)
            ->select('VPL_NO')
            ->first();
        if($objLabel){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }    

//***********************{  save  }********************* */    
   public function save(Request $request) {    
        $r_count = $request['Row_Count1'];
        for ($i=0; $i<=$r_count; $i++){
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i])){
                $req_data[$i] = [
                    'ITEMGID_REF' => $request['RFQID_'.$i],
                    'ITEMID_REF' => $request['ITEMID_REF_'.$i],
                    'UOMID_REF' => $request['ItemuomText_'.$i],
                    'EOQ' => $request['EOQ_'.$i],
                    'LEADDAYS' => $request['LEADDAYS_'.$i],
                    'REMARKS' => $request['REMARKS_'.$i],
                    
                ];
            }
        }

        if(isset($req_data)) { 
           
        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        }
        else {
            $XMLMAT = NULL; 
        }  
        
        $VTID_REF       =   $this->vtid_ref;   
        $USERID_REF     = Auth::user()->USERID;   
        $ACTIONNAME     = 'ADD';
        $IPADDRESS      = $request->getClientIp();
        $CYID_REF       = Auth::user()->CYID_REF;
        $BRID_REF       = Session::get('BRID_REF');
        $FYID_REF       = Session::get('FYID_REF');
        $UPDATE         =  Date('Y-m-d');
        $UPTIME         = Date('h:i:s.u');
        $DEACTIVATED 	= 	'0';
        $DODEACTIVATED  = 	NULL;
    
        $VIINFONO   = $request['VIINFONO'];
        $VIINFODT   = $request['VIINFODT'];
        $VID_REF    = $request['VID_REF']; 
        
        $log_data = [ 
            $VIINFONO,  $VIINFODT,  $VID_REF,   $DEACTIVATED,   $DODEACTIVATED, 
            $CYID_REF,  $BRID_REF,  $FYID_REF,   $XMLMAT,       $VTID_REF,
            $USERID_REF,  $UPDATE,  $UPTIME,   $ACTIONNAME,       $IPADDRESS  
        ];

        $sp_result = DB::select('EXEC SP_VENDORITEMINFO_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $log_data);     

            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            
            exit();   
     }

//**********************{  Edit  }***************************** */
     
    public function edit($id=NULL){
        $USERID     =   Auth::user()->USERID;
        $VTID       =   $this->vtid_ref;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');    
        $FYID_REF   =   Session::get('FYID_REF');
           $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();
            $objVendorList      =   $this->getVendorList();
            $objItemGroupList      =   $this->getItemGroupList();
            $objItemCodeList       =   $this->objItemCodeList();

            $HDR        =   array(); 
            $MAT        =   array();            

        if(!is_null($id)){
        
            $HDR = DB::table('TBL_MST_VENDORITEMINFO_HDR')
            ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_MST_VENDORITEMINFO_HDR.VID_REF','=','TBL_MST_SUBLEDGER.SGLID')     
            ->where('VIINFOID','=',$id)
            ->first();

            $MAT_ARR = DB::table('TBL_MST_ITEMGROUP')                    
            ->leftJoin('TBL_MST_VENDORITEMINFO_MAT', 'TBL_MST_ITEMGROUP.ITEMGID','=','TBL_MST_VENDORITEMINFO_MAT.ITEMGID_REF')
            ->leftJoin('TBL_MST_ITEM', 'TBL_MST_VENDORITEMINFO_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')     
            ->leftJoin('TBL_MST_UOM', 'TBL_MST_VENDORITEMINFO_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')     

            ->where('TBL_MST_VENDORITEMINFO_MAT.VIINFOID_REF','=',$id)
            ->select('TBL_MST_ITEMGROUP.*', 'TBL_MST_VENDORITEMINFO_MAT.*','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_UOM.*')
            ->get(); 

            if(isset($MAT_ARR) && !empty($MAT_ARR)){
                foreach($MAT_ARR as $key=>$val){

                    $MAT[]=array(
                        
                        'GROUPCODE'=>$val->GROUPCODE,
                        'GROUPNAME'=>$val->GROUPNAME,
                        'ICODE'=>$val->ICODE,
                        'NAME'=>$val->NAME,
                        'UOMCODE'=>$val->UOMCODE,
                        'DESCRIPTIONS'=>$val->DESCRIPTIONS,
                        'VIIMATID'=>$val->VIIMATID,
                        'VIINFOID_REF'=>$val->VIINFOID_REF,
                        'ITEMGID_REF'=>$val->ITEMGID_REF,
                        'ITEMID_REF'=>$val->ITEMID_REF,
                        'UOMID_REF'=>$val->UOMID_REF,
                        'EOQ'=>$val->EOQ,
                        'LEADDAYS'=>$val->LEADDAYS,
                        'LOCATION'=>$val->LOCATION,
                        'REMARKS'=>$val->REMARKS,
                        'INDATE'=>$val->INDATE,
                    );
                }

            }

        }

        $objCOMPANY = DB::table('TBL_MST_COMPANY')
        ->where('CYID','=',$CYID_REF)
        ->where('STATUS','=','A')
        ->select('TBL_MST_COMPANY.*')
        ->first();
        $AlpsStatus =   $this->AlpsStatus();
        $FormId  = $this->form_id;

        return view('masters.Purchase.VendorItemInfo.mstfrm51edit', 
        compact(['FormId','objRights','HDR','MAT','objVendorList',
        'objItemGroupList','objCOMPANY','AlpsStatus','objItemCodeList']));

    }
 //********************{  UPDATE  }****************** */

    public function update(Request $request){  
        //$r_count = $request->all();
        $r_count = $request['Row_Count1'];        
        for ($i=0; $i<=$r_count; $i++){
            if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i])){
                $req_data[$i] = [
                    'ITEMGID_REF' => $request['RFQID_'.$i],
                    'ITEMID_REF' => $request['ITEMID_REF_'.$i],
                    'UOMID_REF' => $request['ItemuomText_'.$i],
                    'EOQ' => $request['EOQ_'.$i],
                    'LEADDAYS' => $request['LEADDAYS_'.$i],
                    'REMARKS' => $request['REMARKS_'.$i],
                ];
            }
        }

        if(isset($req_data)) { 
           
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
            }
            else {
                $XMLMAT = NULL; 
            }  
     
    
            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID_REF = Auth::user()->USERID;   
            $ACTIONNAME = 'EDIT';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $UPDATE         =  Date('Y-m-d');
            $UPTIME         = Date('h:i:s.u');
            
            
            $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
        $newDateString = NULL;

        $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt) ){
            
            $newdt = str_replace( "/", "-",  $newdt ) ;

            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        

        $DODEACTIVATED = $newDateString;

        
    
            $VIINFONO   = $request['VIINFONO'];
            $VIINFODT   = $request['VIINFODT'];
            $VID_REF    = $request['VID_REF'];
            
            $log_data = [ 
                $VIINFONO,  $VIINFODT,  $VID_REF,   $DEACTIVATED,   $DODEACTIVATED, 
                $CYID_REF,  $BRID_REF,  $FYID_REF,   $XMLMAT,       $VTID_REF,
                $USERID_REF,  $UPDATE,  $UPTIME,   $ACTIONNAME,       $IPADDRESS  
            ];
    
            $sp_result = DB::select('EXEC SP_VENDORITEMINFO_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $log_data);     
            
        
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
        
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);
    
            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            exit(); 
    }

//*******************{  View  }******************** */
     
    public function view($id=NULL){
        $USERID     =   Auth::user()->USERID;
        $VTID       =   $this->vtid_ref;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');    
        $FYID_REF   =   Session::get('FYID_REF');

           $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first(); 

            $objVendorList      =   $this->getVendorList();
            $objItemGroupList      =   $this->getItemGroupList();
            $objItemCodeList       =   $this->objItemCodeList();

            $HDR        =   array(); 
            $MAT        =   array();            

        if(!is_null($id)){
        
            $HDR = DB::table('TBL_MST_VENDORITEMINFO_HDR')
            ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_MST_VENDORITEMINFO_HDR.VID_REF','=','TBL_MST_SUBLEDGER.SGLID')     
            ->where('VIINFOID','=',$id)
            ->first();

            $MAT_ARR = DB::table('TBL_MST_ITEMGROUP')                    
            ->leftJoin('TBL_MST_VENDORITEMINFO_MAT', 'TBL_MST_ITEMGROUP.ITEMGID','=','TBL_MST_VENDORITEMINFO_MAT.ITEMGID_REF')
            ->leftJoin('TBL_MST_ITEM', 'TBL_MST_VENDORITEMINFO_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')     
            ->leftJoin('TBL_MST_UOM', 'TBL_MST_VENDORITEMINFO_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')     
            ->where('TBL_MST_VENDORITEMINFO_MAT.VIINFOID_REF','=',$id)
            ->select('TBL_MST_ITEMGROUP.*', 'TBL_MST_VENDORITEMINFO_MAT.*','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_UOM.*')
            ->get(); 

            if(isset($MAT_ARR) && !empty($MAT_ARR)){
                foreach($MAT_ARR as $key=>$val){              
                    $MAT[]=array(
                        'GROUPCODE'=>$val->GROUPCODE,
                        'GROUPNAME'=>$val->GROUPNAME,
                        'ICODE'=>$val->ICODE,
                        'NAME'=>$val->NAME,
                        'UOMCODE'=>$val->UOMCODE,
                        'DESCRIPTIONS'=>$val->DESCRIPTIONS,
                        'VIIMATID'=>$val->VIIMATID,
                        'VIINFOID_REF'=>$val->VIINFOID_REF,
                        'ITEMGID_REF'=>$val->ITEMGID_REF,
                        'ITEMID_REF'=>$val->ITEMID_REF,
                        'UOMID_REF'=>$val->UOMID_REF,
                        'EOQ'=>$val->EOQ,
                        'LEADDAYS'=>$val->LEADDAYS,
                        'LOCATION'=>$val->LOCATION,
                        'REMARKS'=>$val->REMARKS,
                        'INDATE'=>$val->INDATE,
                    );
                }

            }

        }

        $objCOMPANY = DB::table('TBL_MST_COMPANY')
        ->where('CYID','=',$CYID_REF)
        ->where('STATUS','=','A')
        ->select('TBL_MST_COMPANY.*')
        ->first();
        $AlpsStatus =   $this->AlpsStatus();
        $FormId  = $this->form_id;
        return view('masters.Purchase.VendorItemInfo.mstfrm51view', 
        compact(['FormId','objRights','HDR','MAT','objVendorList',
        'objItemGroupList','objCOMPANY','AlpsStatus','objItemCodeList']));
    }

//**********************{  Approve  }********************** */
    
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


    //dd($sp_Approvallevel);
    
    $sp_listing_result = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?, ?', $sp_Approvallevel);

    
    if(!empty($sp_listing_result)){
        foreach ($sp_listing_result as $key=>$valueitem){  
            $record_status = 0;
            $Approvallevel = "APPROVAL".$valueitem->LAVELS;
        }
    }


   

    $r_count = $request['Row_Count1'];        
    for ($i=0; $i<=$r_count; $i++){
        if(isset($request['ITEMID_REF_'.$i]) && !is_null($request['ITEMID_REF_'.$i])){
            $req_data[$i] = [
                'ITEMGID_REF' => $request['RFQID_'.$i],
                'ITEMID_REF' => $request['ITEMID_REF_'.$i],
                'UOMID_REF' => $request['ItemuomText_'.$i],
                'EOQ' => $request['EOQ_'.$i],
                'LEADDAYS' => $request['LEADDAYS_'.$i],
                'REMARKS' => $request['REMARKS_'.$i],
            ];
        }
    }

    if(isset($req_data)) { 
       
        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        }
        else {
            $XMLMAT = NULL; 
        }  
  
     
            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID_REF = Auth::user()->USERID;   
            $ACTIONNAME = $Approvallevel;
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $UPDATE         =  Date('Y-m-d');
            $UPTIME         = Date('h:i:s.u');
            $DEACTIVATED 	= 	'0';
            $DODEACTIVATED  = 	NULL;
    
            
            $VIINFONO   = $request['VIINFONO'];
            $VIINFODT   = $request['VIINFODT'];
            $VID_REF    = $request['VID_REF'];
            
            $log_data = [ 
                $VIINFONO,  $VIINFODT,  $VID_REF,   $DEACTIVATED,   $DODEACTIVATED, 
                $CYID_REF,  $BRID_REF,  $FYID_REF,   $XMLMAT,       $VTID_REF,
                $USERID_REF,  $UPDATE,  $UPTIME,   $ACTIONNAME,       $IPADDRESS  
            ];
    
            $sp_result = DB::select('EXEC SP_VENDORITEMINFO_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $log_data);     
            
        
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
        
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);
    
            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            exit();     
}

//****************{  cancel  }************************ */        

 public function cancel(Request $request){
        $id = $request->{0};

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_VENDORITEMINFO_HDR";
        $FIELD      =   "VIINFOID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $canceldata[0]=[
            'NT'  => 'TBL_MST_VENDORITEMINFO_MAT',
       ];        
       $links["TABLES"] = $canceldata; 
       $cancelxml = ArrayToXml::convert($links);
        
        $udf_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$cancelxml ];

        $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $udf_cancel_data);

        if($sp_result[0]->RESULT=="CANCELED"){  

            return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
        
            return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'invalid'=>'invalid']);
        }
        
        exit(); 
    }

    public function cancelold(Request $request){

        $id = $request->{0};

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_VENDORITEMINFO_HDR";
        $FIELD      =   "VIINFOID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_MST_VENDORITEMINFO_MAT',
        ];        
       
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

        if($sp_result[0]->RESULT=="CANCELED"){  

            return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
        
            return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'invalid'=>'invalid']);
        }
        
        exit(); 
    }


  
//********************{  Attachment  }********************* */



public function attachment($id){

    if(!is_null($id)){
    
        $FormId     =   $this->form_id;

        $objResponse = DB::table('TBL_MST_VENDORITEMINFO_HDR')->where('VIINFOID','=',$id)->first();

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

        return view('masters.Purchase.VendorItemInfo.mstfrm51attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments']));
    }

}





//*******************{  docuploads  }********************* */



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
    
    $image_path         =   "docs/company".$CYID_REF."/VendorItemInfo";     
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
        return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
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

        return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        
        return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
   
}



        
//*********************{  checkso  }************************* */

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

    public function AlpsStatus(){

        $COMPANY_NAME   =   DB::table('TBL_MST_COMPANY')->where('STATUS','=','A')->where('CYID','=',Auth::user()->CYID_REF)->select('TBL_MST_COMPANY.NAME')->first()->NAME;
        
        
        $disabled       =   strpos($COMPANY_NAME,"ALPS")!== false?'disabled':'';
        $hidden         =   strpos($COMPANY_NAME,"ALPS")!== false?'':'hidden';
     
        return  $ALPS_STATUS=array(
            'hidden'=>$hidden,
            'disabled'=>$disabled
        );
    
    }


    
}
