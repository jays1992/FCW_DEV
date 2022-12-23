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

class TrnFrm41Controller extends Controller
{
    protected $form_id = 41;
    protected $vtid_ref   = 41;  
  
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

        $objDataList	=	DB::select("select hdr.SOSID,hdr.SOSNO,hdr.SOSDT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.SOSID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                                when a.ACTIONNAME = 'APPROVAL5' then  'Final Approved' 
                                when a.ACTIONNAME = 'CANCEL' then 'Cancelled'
                                when a.ACTIONNAME = 'CLOSE' then 'Closed'
                            end end as STATUS_DESC
                            from TBL_TRN_AUDITTRAIL a 
                            inner join TBL_TRN_SLSH01_HDR hdr
                            on a.VID = hdr.SOSID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_SUBLEDGER sl ON hdr.SLID_REF = sl.SGLID 
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.SOSID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );

                           
        
        return view('transactions.sales.ScheduleOpenSalesOrder.trnfrm41',compact(['REQUEST_DATA','objRights','objDataList']));        
    }

    public function add(){       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $objglcode = DB::table('TBL_MST_GENERALLEDGER')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('STATUS','=',$Status)
        ->where('SUBLEDGER','=',1)
        ->select('TBL_MST_GENERALLEDGER.*')
        ->get()
        ->toArray();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_SLSH01_HDR',
            'HDR_ID'=>'SOSID',
            'HDR_DOC_NO'=>'SOSNO',
            'HDR_DOC_DT'=>'SOSDT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
        
        

        $objlastSOSDT = DB::select('SELECT MAX(SOSDT) SOSDT FROM TBL_TRN_SLSH01_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  41, 'A' ]);

        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
       
    return view('transactions.sales.ScheduleOpenSalesOrder.trnfrm41add',
    compact(['objglcode','objlastSOSDT','AlpsStatus','TabSetting','doc_req','docarray']));       
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

    public function getOpenSalesOrder(Request $request){
        $Status = "A";
        $id = $request['id'];
    
        $ObjData =  DB::select('SELECT OSOID, OSONO, OSODT FROM TBL_TRN_SLSO03_HDR  
                    WHERE STATUS= ? AND SLID_REF = ? order by OSOID ASC', [$Status,$id]);
    
            if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){
            
                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_OSOID_REF[]" id="OSO_'.$dataRow->OSOID .'"  class="clsOSO" value="'.$dataRow->OSOID.'" ></td>
                <td class="ROW2">'.$dataRow->OSONO;
                $row = $row.'<input type="hidden" id="txtOSO_'.$dataRow->OSOID.'" data-desc="'.$dataRow->OSONO .'" 
                value="'.$dataRow->OSOID.'"/></td>
                <td class="ROW3" >'.$dataRow->OSODT.'</td></tr>';
    
                echo $row;
            }
    
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    
    }

    public function getOSOMaterial(Request $request){
        $Status = "A";
        $id = $request['id'];

        $row1 = '';

        $AlpsStatus =   $this->AlpsStatus();

        
    
        $ObjData =  DB::select('SELECT * FROM TBL_TRN_SLSO03_MAT  
                    WHERE OSOID_REF = ? order by OSOMATID ASC', [$id]);
    
            if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){

                $objItem = DB::select('SELECT top 1 * FROM TBL_MST_ITEM  WHERE ITEMID = ? order by ITEMID ASC', [$dataRow->ITEMID_REF]);

                $objUOM = DB::select('SELECT top 1 * FROM TBL_MST_UOM WHERE UOMID = ? order by UOMID ASC', [$dataRow->UOMID_REF]);
            
                $row = '';

                $row = $row.' <tr class="participantRow"> <td><input type="text" name="popupITEMID_'.$index.'" id="popupITEMID_'.$index.'" class="form-control"  autocomplete="off" value="'.$objItem[0]->ICODE.'"  readonly></td>';
                $row = $row.' <td hidden><input type="hidden" name="ITEMID_REF_'.$index.'" id="ITEMID_REF_'.$index.'" class="form-control"  autocomplete="off" value="'.$objItem[0]->ITEMID.'" /></td>';
                $row = $row.' <td><input type="text" name="ItemName_'.$index.'" id="ItemName_'.$index.'" class="form-control"  autocomplete="off" value="'.$objItem[0]->NAME.'"  readonly></td>';
                
                $row = $row.' <td '.$AlpsStatus['hidden'].' ><input  type="text" name="Alpspartno_'.$index.'" id="Alpspartno_'.$index.'" class="form-control"  autocomplete="off" value="'.$objItem[0]->ALPS_PART_NO.'"  readonly></td>';
                $row = $row.' <td '.$AlpsStatus['hidden'].' ><input  type="text" name="Custpartno_'.$index.'" id="Custpartno_'.$index.'" class="form-control"  autocomplete="off" value="'.$objItem[0]->CUSTOMER_PART_NO.'"  readonly></td>';
                $row = $row.' <td '.$AlpsStatus['hidden'].' ><input  type="text" name="OEMpartno_'.$index.'" id="OEMpartno_'.$index.'"   class="form-control"  autocomplete="off" value="'.$objItem[0]->OEM_PART_NO.'"  readonly></td>';
                
                
                $row = $row.' <td><input type="text" name="popupUOM_'.$index.'" id="popupUOM_'.$index.'" class="form-control"  autocomplete="off" value="'.$objUOM[0]->UOMCODE.'-'.$objUOM[0]->DESCRIPTIONS.'"  readonly></td>';
                $row = $row.' <td hidden><input type="hidden" name="UOMID_REF_'.$index.'" id="UOMID_REF_'.$index.'" class="form-control"  autocomplete="off" value="'.$dataRow->UOMID_REF.'"  /></td>';
                $row = $row.' <td><input type="text" name="ITEMSPECI_'.$index.'" id="ITEMSPECI_'.$index.'" class="form-control"  autocomplete="off" value="'.$dataRow->ITEMSPECI.'" readonly ></td>';
                $row = $row.' <td><input type="text" name="RATEPUOM_'.$index.'" id="RATEPUOM_'.$index.'" class="form-control"  autocomplete="off" value="'.$dataRow->RATEPUOM.'"  readonly> </td></tr><tr></tr>';

                $row1 = $row1.$row;
            }
            echo $row1;
          
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    
    }
    public function getOSOMaterial2(Request $request){
        $Status = "A";
        $id = $request['id'];
    
        $ObjData =  DB::select('SELECT * FROM TBL_TRN_SLSO03_MAT  
                    WHERE OSOID_REF = ? order by OSOMATID ASC', [$id]);
        $objCount1 = count($ObjData);
            if(!empty($objCount1)){
    
            echo $objCount1;
    
            }else{
                echo '1';
            }
            exit();
    
    }

    
    public function getItemDetails(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $id = $request['id'];
        $StdCost = 0;

        $AlpsStatus =   $this->AlpsStatus();
        
        $ObjData = DB::select('SELECT * FROM TBL_TRN_SLSO03_MAT WHERE OSOID_REF = ? order by OSOMATID ASC', [$id]);
                
        
    
                if(!empty($ObjData)){

                    foreach ($ObjData as $index=>$dataRow){

                    $ObjItem =  DB::select('SELECT * FROM TBL_MST_ITEM  
                                WHERE ITEMID = ?',[$dataRow->ITEMID_REF]);

                    $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  UOMID = ?',[$dataRow->UOMID_REF]);

                    $ItemRowData =  DB::select('SELECT top 1 * FROM TBL_MST_ITEM  WHERE ITEMID = ? ', [$dataRow->ITEMID_REF]);

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
                       
                            $row = $row.'<tr>';
                            $row = $row.'
                            <td style="width:5%;text-align:center;"> <input type="checkbox" name="SELECT_Sch_ITEMID[]" id="item_'.$dataRow->ITEMID_REF .'"  class="clsitemid" value="'.$dataRow->ITEMID_REF.'" ></td>
                            <td style="width:15%;">'.$ObjItem[0]->ICODE;
                            $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID_REF.'" data-desc="'.$ObjItem[0]->ICODE.'"
                            value="'.$dataRow->ITEMID_REF.'"/></td><td style="width:15%;" id="itemname_'.$dataRow->ITEMID_REF.'" >'.$ObjItem[0]->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID_REF.'" data-desc="'.$dataRow->ITEMSPECI.'"
                            value="'.$ObjItem[0]->NAME.'"/></td>';
                            $row = $row.'<td style="width:15%;" id="itemuom_'.$dataRow->ITEMID_REF.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID_REF.'" data-desc="'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'"
                            value="'.$dataRow->UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>
                            
                            <td style="width:15%;">'.$BusinessUnit.'</td>
                            <td style="width:15%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                            <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                            </tr>'; 
                        
                        echo $row;    
                    } 
                    
                }           
                else{
                 echo '<tr><td> Record not found.</td></tr>';
                }
        exit();
    }

    public function getShipAddress(Request $request){
        $Status = "A";
        $id = $request['id'];
        $fieldid    = $request['fieldid'];
        $BRID_REF = Session::get('BRID_REF');

        if(!is_null($id)){

            $ObjCust    =  DB::select('SELECT top 1 CID FROM TBL_MST_CUSTOMER  WHERE STATUS= ? AND SLID_REF = ? ', [$Status,$id]);

            if(!empty($ObjCust)){
            
                    $cid        =   $ObjCust[0]->CID;
                    $ObjShipTo  =   DB::select('SELECT  * FROM TBL_MST_CUSTOMERLOCATION  WHERE SHIPTO= ? AND CID_REF = ? ', [1,$cid]);
            
                    if(!empty($ObjShipTo)){
            
                        foreach ($ObjShipTo as $index=>$dataRow){

                            $ObjBranch =  DB::select('SELECT top 1 STID_REF FROM TBL_MST_BRANCH   WHERE BRID= ? ', [$BRID_REF]);

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
                            $row = $row.'<tr >
                            <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="shipto_'.$dataRow->CLID .'"  class="clsshipto" value="'.$dataRow->CLID.'" ></td>
                            <td class="ROW2">'.$dataRow->NAME;
                            $row = $row.'<input type="hidden" id="txtshipto_'.$dataRow->CLID.'" data-desc="'.$TAXSTATE.'" 
                            value="'.$dataRow->CLID.'"/></td><td class="ROW3" id="txtshipadd_'.$dataRow->CLID.'" >'.$objAddress.'</td></tr>';
                            echo $row;
                        }
            
                    }else{
                        echo '<tr><td colspan="2">Record not found.</td></tr>';
                    }
                    exit();

            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit(); 
        }
    }   

    public function getShipAddressIDwise(Request $request){
        $Status = "A";
        $id = $request['id'];
        $BRID_REF = Session::get('BRID_REF');
        
        if(!is_null($id))
        {
        
        $ObjShipTo =  DB::select('SELECT  top 1 * FROM TBL_MST_CUSTOMERLOCATION  
                    WHERE CLID = ? ', [$id]);
    
            if(!empty($ObjShipTo)){
    
            foreach ($ObjShipTo as $index=>$dataRow){

                $ObjCity =  DB::select('SELECT top 1 * FROM TBL_MST_CITY  
                WHERE STATUS= ? AND CITYID = ? AND CTRYID_REF = ? AND STID_REF = ?', 
                [$Status,$dataRow->CITYID_REF,$dataRow->CTRYID_REF,$dataRow->STID_REF]);

                $ObjState =  DB::select('SELECT top 1 * FROM TBL_MST_STATE  
                            WHERE STATUS= ? AND STID = ? AND CTRYID_REF = ?', [$Status,$dataRow->STID_REF,$dataRow->CTRYID_REF]);

                $ObjCountry =  DB::select('SELECT top 1 * FROM TBL_MST_COUNTRY  
                            WHERE STATUS= ? AND CTRYID = ? ', [$Status,$dataRow->CTRYID_REF]);
                $objAddress = $dataRow->CADD .' '.$ObjCity[0]->NAME .' '.$ObjState[0]->NAME .' '.$ObjCountry[0]->NAME;
                
                echo $objAddress;
            }
    
            }else{
                echo '';
            }
            exit();
        }
    }

 
   public function attachment($id){

    if(!is_null($id))
    {
        $objScheduleOpenSalesOrder = DB::table("TBL_TRN_SLSH01_HDR")
                        ->where('SOSID','=',$id)
                        ->select('TBL_TRN_SLSH01_HDR.*')
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

               

            return view('transactions.sales.ScheduleOpenSalesOrder.trnfrm41attachment',compact(['objScheduleOpenSalesOrder','objMstVoucherType','objAttachments']));
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
                    'UOMID_REF'     => $request['UOMID_REF_'.$i],
                    'ITEMSPECI'     => $request['ITEMSPECI_'.$i],
                    'RATE'          => $request['RATEPUOM_'.$i],
                ];
            }
        }
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        for ($i=0; $i<=$r_count2; $i++)
        {
                   if(isset($request['SCHDT_'.$i]))
                    {
                        $reqdata2[$i] = [
                            'SCHDT'     => $request['SCHDT_'.$i],
                            'SCHQTY'    => $request['SCHQTY_'.$i],
                            'SHIPTO'    => $request['txtSHIPTO_'.$i],
                        ];
                    }
        }
           if(isset($reqdata2))
           { 
            $wrapped_links2["SCHEDULE"] = $reqdata2;
            $XMLSCHD = ArrayToXml::convert($wrapped_links2);
           }
           else
           {
            $XMLSCHD = NULL; 
           }   
            
        
        
        
        
            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $SOSNO = $request['SOSNO'];
            $SOSDT = $request['SOSDT'];
            $GLID_REF = $request['GLID_REF'];
            $SLID_REF = $request['SLID_REF'];
            $OSOID_REF = $request['OSOID_REF'];
            $ITEMID_REF = $request['Sch_ITEMID'];
            $UOMID_REF = $request['Sch_UOMID_REF'];
            $SOQTY = $request['Sch_SOQTY'];

            $log_data = [ 
                $SOSNO,$SOSDT,$GLID_REF,$SLID_REF,$OSOID_REF,$CYID_REF, $BRID_REF,$FYID_REF, $VTID_REF,
                $XMLMAT,$ITEMID_REF,$UOMID_REF,$SOQTY,$XMLSCHD,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];

            
            $sp_result = DB::select('EXEC SP_SOS_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);    
            
            
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            exit();   
     }

     

    public function edit($id=NULL)
    {
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id))
        {
            $objSOS = DB::table('TBL_TRN_SLSH01_HDR')
                             ->where('TBL_TRN_SLSH01_HDR.FYID_REF','=',Session::get('FYID_REF'))
                             ->where('TBL_TRN_SLSH01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_TRN_SLSH01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                             ->where('TBL_TRN_SLSH01_HDR.SOSID','=',$id)
                             ->select('TBL_TRN_SLSH01_HDR.*')
                             ->first();

            $objSOSMAT=[];
            if(isset($objSOS) && !empty($objSOS)){

                $objSOSMAT = DB::table('TBL_TRN_SLSH01_MAT')                    
                             ->where('TBL_TRN_SLSH01_MAT.SOSID_REF','=',$id)
                             ->leftJoin('TBL_MST_ITEM','TBL_TRN_SLSH01_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')      
                             ->leftJoin('TBL_MST_UOM','TBL_TRN_SLSH01_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')        
                 
                             ->select(
                                 'TBL_TRN_SLSH01_MAT.*',
                                 'TBL_MST_ITEM.ITEMID',
                                'TBL_MST_ITEM.ICODE',
                                'TBL_MST_ITEM.NAME',
                                'TBL_MST_ITEM.ALPS_PART_NO',
                                'TBL_MST_ITEM.CUSTOMER_PART_NO',
                                'TBL_MST_ITEM.OEM_PART_NO',
                                'TBL_MST_UOM.UOMID',
                                'TBL_MST_UOM.UOMCODE',
                                'TBL_MST_UOM.DESCRIPTIONS'
                                 )
                             ->orderBy('TBL_TRN_SLSH01_MAT.SOSMATID','ASC')
                             ->get()->toArray();
            }

            $objCount1 = count($objSOSMAT);


          

            $objSOSSCH=[];
            if(isset($objSOS) && !empty($objSOS)){
                $objSOSSCH = DB::table('TBL_TRN_SLSH01_SCH')                    
                             ->where('TBL_TRN_SLSH01_SCH.SOSID_REF','=',$id)
                             ->leftJoin('TBL_MST_ITEM','TBL_TRN_SLSH01_SCH.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')      
                             ->leftJoin('TBL_MST_UOM','TBL_TRN_SLSH01_SCH.UOMID_REF','=','TBL_MST_UOM.UOMID')  
                             ->select(
                                 'TBL_TRN_SLSH01_SCH.*',
                                 'TBL_MST_ITEM.ITEMID',
                                 'TBL_MST_ITEM.ICODE',
                                 'TBL_MST_ITEM.NAME',
                                 'TBL_MST_UOM.UOMID',
                                 'TBL_MST_UOM.UOMCODE',
                                 'TBL_MST_UOM.DESCRIPTIONS'
                                 )
                             ->orderBy('TBL_TRN_SLSH01_SCH.SOSSCHID','ASC')
                             ->first();
            }

         
            $objSCHDET=[];
            if(isset($objSOSSCH->SOSSCHID) && $objSOSSCH->SOSSCHID !=""){
            $objSCHDET = DB::table('TBL_TRN_SLSH01_SCHDETAILS')                    
                             ->where('TBL_TRN_SLSH01_SCHDETAILS.SOSSCHID_REF','=',$objSOSSCH->SOSSCHID)
                             ->select('TBL_TRN_SLSH01_SCHDETAILS.*')
                             ->orderBy('TBL_TRN_SLSH01_SCHDETAILS.SCHDID','ASC')
                             ->get()->toArray();
            }


            $objCount3 = count($objSCHDET);

            
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
            
           

            $objsubglcode=[];
            if(isset($objSOS->GLID_REF) && $objSOS->GLID_REF !=""){
            $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('GLID_REF','=',$objSOS->GLID_REF)
            ->where('SGLID','=',$objSOS->SLID_REF)
            ->select('TBL_MST_SUBLEDGER.*')
            ->first();
            }

            $objOSO=[];
            if(isset($objSOS->OSOID_REF) && $objSOS->OSOID_REF !=""){
                $objOSO = DB::table('TBL_TRN_SLSO03_HDR')
                ->where('OSOID','=',$objSOS->OSOID_REF)
                ->select('TBL_TRN_SLSO03_HDR.*')
                ->first();
            }
           
            $objlastSOSDT = DB::select('SELECT MAX(SOSDT) SOSDT FROM TBL_TRN_SLSH01_HDR  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
            [$CYID_REF, $BRID_REF,  41, 'A' ]);
            
           

            $objItems=array();
            
           
            
            $objUOM=array();

            $AlpsStatus =   $this->AlpsStatus();
            $InputStatus=   "";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
            
        return view('transactions.sales.ScheduleOpenSalesOrder.trnfrm41edit',compact(['objSOS','objRights','objCount1',
           'objCount3','objSOSMAT','objSOSSCH','objSCHDET','objsubglcode','objItems','objUOM',
           'objlastSOSDT','objOSO','AlpsStatus','InputStatus','TabSetting']));
        }
     
    }
     
    public function view($id=NULL)
    {
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id))
        {
            $objSOS = DB::table('TBL_TRN_SLSH01_HDR')
                             ->where('TBL_TRN_SLSH01_HDR.FYID_REF','=',Session::get('FYID_REF'))
                             ->where('TBL_TRN_SLSH01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_TRN_SLSH01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                             ->where('TBL_TRN_SLSH01_HDR.SOSID','=',$id)
                             ->select('TBL_TRN_SLSH01_HDR.*')
                             ->first();

            $objSOSMAT=[];
            if(isset($objSOS) && !empty($objSOS)){

                $objSOSMAT = DB::table('TBL_TRN_SLSH01_MAT')                    
                             ->where('TBL_TRN_SLSH01_MAT.SOSID_REF','=',$id)
                             ->leftJoin('TBL_MST_ITEM','TBL_TRN_SLSH01_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')      
                             ->leftJoin('TBL_MST_UOM','TBL_TRN_SLSH01_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')        
                 
                             ->select(
                                 'TBL_TRN_SLSH01_MAT.*',
                                 'TBL_MST_ITEM.ITEMID',
                                'TBL_MST_ITEM.ICODE',
                                'TBL_MST_ITEM.NAME',
                                'TBL_MST_ITEM.ALPS_PART_NO',
                                'TBL_MST_ITEM.CUSTOMER_PART_NO',
                                'TBL_MST_ITEM.OEM_PART_NO',
                                'TBL_MST_UOM.UOMID',
                                'TBL_MST_UOM.UOMCODE',
                                'TBL_MST_UOM.DESCRIPTIONS'
                                 )
                             ->orderBy('TBL_TRN_SLSH01_MAT.SOSMATID','ASC')
                             ->get()->toArray();
            }

            $objCount1 = count($objSOSMAT);


           

            $objSOSSCH=[];
            if(isset($objSOS) && !empty($objSOS)){
                $objSOSSCH = DB::table('TBL_TRN_SLSH01_SCH')                    
                             ->where('TBL_TRN_SLSH01_SCH.SOSID_REF','=',$id)
                             ->leftJoin('TBL_MST_ITEM','TBL_TRN_SLSH01_SCH.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')      
                             ->leftJoin('TBL_MST_UOM','TBL_TRN_SLSH01_SCH.UOMID_REF','=','TBL_MST_UOM.UOMID')  
                             ->select(
                                 'TBL_TRN_SLSH01_SCH.*',
                                 'TBL_MST_ITEM.ITEMID',
                                 'TBL_MST_ITEM.ICODE',
                                 'TBL_MST_ITEM.NAME',
                                 'TBL_MST_UOM.UOMID',
                                 'TBL_MST_UOM.UOMCODE',
                                 'TBL_MST_UOM.DESCRIPTIONS'
                                 )
                             ->orderBy('TBL_TRN_SLSH01_SCH.SOSSCHID','ASC')
                             ->first();
            }

           
            $objSCHDET=[];
            if(isset($objSOSSCH->SOSSCHID) && $objSOSSCH->SOSSCHID !=""){
            $objSCHDET = DB::table('TBL_TRN_SLSH01_SCHDETAILS')                    
                             ->where('TBL_TRN_SLSH01_SCHDETAILS.SOSSCHID_REF','=',$objSOSSCH->SOSSCHID)
                             ->select('TBL_TRN_SLSH01_SCHDETAILS.*')
                             ->orderBy('TBL_TRN_SLSH01_SCHDETAILS.SCHDID','ASC')
                             ->get()->toArray();
            }


            $objCount3 = count($objSCHDET);

            
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
            
            

            $objsubglcode=[];
            if(isset($objSOS->GLID_REF) && $objSOS->GLID_REF !=""){
            $objsubglcode = DB::table('TBL_MST_SUBLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('GLID_REF','=',$objSOS->GLID_REF)
            ->where('SGLID','=',$objSOS->SLID_REF)
            ->select('TBL_MST_SUBLEDGER.*')
            ->first();
            }

            $objOSO=[];
            if(isset($objSOS->OSOID_REF) && $objSOS->OSOID_REF !=""){
                $objOSO = DB::table('TBL_TRN_SLSO03_HDR')
                ->where('OSOID','=',$objSOS->OSOID_REF)
                ->select('TBL_TRN_SLSO03_HDR.*')
                ->first();
            }
           
            $objlastSOSDT = DB::select('SELECT MAX(SOSDT) SOSDT FROM TBL_TRN_SLSH01_HDR  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
            [$CYID_REF, $BRID_REF,  41, 'A' ]);
            
          

            $objItems=array();
            
           
            
            $objUOM=array();

            $AlpsStatus =   $this->AlpsStatus();
            $InputStatus=   "disabled";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
            
        return view('transactions.sales.ScheduleOpenSalesOrder.trnfrm41view',compact(['objSOS','objRights','objCount1',
           'objCount3','objSOSMAT','objSOSSCH','objSCHDET','objsubglcode','objItems','objUOM',
           'objlastSOSDT','objOSO','AlpsStatus','InputStatus','TabSetting']));
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
                    'UOMID_REF'     => $request['UOMID_REF_'.$i],
                    'ITEMSPECI'     => $request['ITEMSPECI_'.$i],
                    'RATE'          => $request['RATEPUOM_'.$i],
                ];
            }
        }
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        for ($i=0; $i<=$r_count2; $i++)
        {
                   if(isset($request['SCHDT_'.$i]))
                    {
                        $reqdata2[$i] = [
                            'SCHDT'     => $request['SCHDT_'.$i],
                            'SCHQTY'    => $request['SCHQTY_'.$i],
                            'SHIPTO'    => $request['txtSHIPTO_'.$i],
                        ];
                    }
        }
           if(isset($reqdata2))
           { 
            $wrapped_links2["SCHEDULE"] = $reqdata2;
            $XMLSCHD = ArrayToXml::convert($wrapped_links2);
           }
           else
           {
            $XMLSCHD = NULL; 
           }   
        
            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'EDIT';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $SOSNO = $request['SOSNO'];
            $SOSDT = $request['SOSDT'];
            $GLID_REF = $request['GLID_REF'];
            $SLID_REF = $request['SLID_REF'];
            $OSOID_REF = $request['OSOID_REF'];
            $ITEMID_REF = $request['Sch_ITEMID'];
            $UOMID_REF = $request['Sch_UOMID_REF'];
            $SOQTY = $request['Sch_SOQTY'];

           

            $log_data = [ 
                $SOSNO,$SOSDT,$GLID_REF,$SLID_REF,$OSOID_REF,$CYID_REF, $BRID_REF,$FYID_REF, $VTID_REF,
                $XMLMAT,$ITEMID_REF,$UOMID_REF,$SOQTY,$XMLSCHD,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];

            
            $sp_result = DB::select('EXEC SP_SOS_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
            
        
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
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
                    'UOMID_REF'     => $request['UOMID_REF_'.$i],
                    'ITEMSPECI'     => $request['ITEMSPECI_'.$i],
                    'RATE'          => $request['RATEPUOM_'.$i],
                ];
            }
        }
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        for ($i=0; $i<=$r_count2; $i++)
        {
                   if(isset($request['SCHDT_'.$i]))
                    {
                        $reqdata2[$i] = [
                            'SCHDT'     => $request['SCHDT_'.$i],
                            'SCHQTY'    => $request['SCHQTY_'.$i],
                            'SHIPTO'    => $request['txtSHIPTO_'.$i],
                        ];
                    }
        }
           if(isset($reqdata2))
           { 
            $wrapped_links2["SCHEDULE"] = $reqdata2;
            $XMLSCHD = ArrayToXml::convert($wrapped_links2);
           }
           else
           {
            $XMLSCHD = NULL; 
           }   
        
            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = $Approvallevel;
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $SOSNO = $request['SOSNO'];
            $SOSDT = $request['SOSDT'];
            $GLID_REF = $request['GLID_REF'];
            $SLID_REF = $request['SLID_REF'];
            $OSOID_REF = $request['OSOID_REF'];
            $ITEMID_REF = $request['Sch_ITEMID'];
            $UOMID_REF = $request['Sch_UOMID_REF'];
            $SOQTY = $request['Sch_SOQTY'];

            $log_data = [ 
                $SOSNO,$SOSDT,$GLID_REF,$SLID_REF,$OSOID_REF,$CYID_REF, $BRID_REF,$FYID_REF, $VTID_REF,
                $XMLMAT,$ITEMID_REF,$UOMID_REF,$SOQTY,$XMLSCHD,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];

            
            $sp_result = DB::select('EXEC SP_SOS_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
            
        
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
                $TABLE      =   "TBL_TRN_SLSH01_HDR";
                $FIELD      =   "SOSID";
                $ACTIONNAME =   $Approvallevel;
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
        $TABLE      =   "TBL_TRN_SLSH01_HDR";
        $FIELD      =   "SOSID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();


        $req_data[0]=[
        'NT'  => 'TBL_TRN_SLSH01_MAT',
        ];
        $req_data[1]=[
        'NT'  => 'TBL_TRN_SLSH01_SCH',
        ];
        $req_data[2]=[
        'NT'  => 'TBL_TRN_SLSH01_SCHDETAILS',
        ];

        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_SOS  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);


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
    
    $image_path         =   "docs/company".$CYID_REF."/ScheduleOpenSalesOrder";     
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

                               $uploadedFile->move($destinationPath, $filenametostore);  //upload in dir if not exists
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
        return redirect()->route("transaction",[41,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
    
       return redirect()->route("transaction",[41,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("transaction",[41,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("transaction",[41,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("transaction",[41,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

    public function checksos(Request $request){

     
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $SOSNO = $request->SOSNO;
        
        $objSO = DB::table('TBL_TRN_SLSH01_HDR')
        ->where('TBL_TRN_SLSH01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLSH01_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_SLSH01_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_SLSH01_HDR.SOSNO','=',$SOSNO)
        ->select('TBL_TRN_SLSH01_HDR.SOSID')
        ->first();
        
        if($objSO){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate SOSNO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }
    
}
