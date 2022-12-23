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

class TrnFrm229Controller extends Controller{

    protected $form_id = 229;
    protected $vtid_ref   = 319;  //voucher type id
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
        $objRights = DB::table('TBL_MST_USERROLMAP')
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
        
        $objDataList    =	DB::select("SELECT '$USER_LEVEL' AS USER_LEVEL,T1.*,T2.ACTIONNAME,T3.MTDESCRIPTION,
        (
        SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
        LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
        WHERE  AUD.VID=T1.MPPID AND  AUD.CYID_REF=T1.CYID_REF AND  AUD.BRID_REF=T1.BRID_REF AND  
        AUD.FYID_REF=T1.FYID_REF AND  AUD.VTID_REF=T1.VTID_REF AND AUD.ACTIONNAME='ADD'       
        ) AS CREATED_BY
        FROM TBL_TRN_PDMPP_HDR T1
        INNER JOIN TBL_TRN_AUDITTRAIL T2 ON T1.MPPID=T2.VID AND T1.VTID_REF=T2.VTID_REF AND T1.CYID_REF=T2.CYID_REF AND T1.BRID_REF=T2.BRID_REF 
        INNER JOIN TBL_MST_MONTH T3 ON T1.PERIOD_MTID_REF = T3.MTID   
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' AND T2.VTID_REF = '$this->vtid_ref' AND T2.ACTID IN (SELECT max(ACTID) FROM TBL_TRN_AUDITTRAIL A WHERE T2.VTID_REF = A.VTID_REF AND T2.VID = A.VID)
        ORDER BY T1.MPPID DESC 
        ");
        
        return view('transactions.Production.ManualProductionPlan.trnfrm229',compact(['REQUEST_DATA','objRights','FormId','objDataList','DATA_STATUS']));        
    }

    public function add(){       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
       

        $cur_date = Date('Y-m-d');
        $objItemCategoryList = DB::select('select * from TBL_MST_ITEMCATEGORY  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?) and CYID_REF = ?   and STATUS = ? ',  [$cur_date,$CYID_REF,'A']);
            

        $objMonths = DB::select('select * from TBL_MST_MONTH  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?)  and STATUS = ? ', [$cur_date,'A']);

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_PDMPP_HDR',
            'HDR_ID'=>'MPPID',
            'HDR_DOC_NO'=>'MPP_DOC_NO',
            'HDR_DOC_DT'=>'MPP_DOC_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

         

        $objlast_DT = DB::select('SELECT MAX(MPP_DOC_DT) MPP_DOC_DT FROM TBL_TRN_PDMPP_HDR  
                            WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND VTID_REF = ? AND STATUS = ?', 
                            [$CYID_REF, $BRID_REF, $FYID_REF, $this->vtid_ref, 'N' ]);
        
      
      
    return view('transactions.Production.ManualProductionPlan.trnfrm229add', compact(['objItemCategoryList','objMonths','objlast_DT','doc_req','docarray']));       
   }

   


   

    public function getItemDetails(Request $request){
        //dd($request->all()); 
        $Status = $request['status'];
        $itemcat_id = $request['BU_NO'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $StdCost = 0;
        $Taxid = [];
                
        // $ObjItem =  DB::select('SELECT * FROM TBL_MST_ITEM  
        //             WHERE CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? 
        //             AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ? AND BUID_REF=?', 
        //             [$CYID_REF, $BRID_REF, $FYID_REF, $Status,$Bu_id ]);

        $cur_date = Date('Y-m-d');
        $ObjItem = DB::select('select * from TBL_MST_ITEM  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?) and CYID_REF = ?   and STATUS = ? and ICID_REF=?',  [$cur_date,$CYID_REF,'A',$itemcat_id]);

           //dd($ObjItem);
                if(!empty($ObjItem)){

                    foreach ($ObjItem as $index=>$dataRow){ //print_r($dataRow);
                    
                        
                    $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $BRID_REF, $FYID_REF,$dataRow->MAIN_UOMID_REF, 'A' ]);

                    $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $BRID_REF, $FYID_REF,$dataRow->ALT_UOMID_REF, $Status ]);
                    
                    $ObjAltQTY =  DB::select('SELECT TOP 1  TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV  
                                WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                                [$dataRow->ITEMID,$dataRow->ALT_UOMID_REF ]);

                    $TOQTY =  isset($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 0;
                    $FROMQTY =  isset($ObjAltQTY[0]->FROM_QTY)? $ObjAltQTY[0]->FROM_QTY : 0;

                    $ObjItemGroup =  DB::select('SELECT TOP 1 GROUPCODE, GROUPNAME FROM TBL_MST_ITEMGROUP  
                                WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ITEMGID = ?
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                [$CYID_REF, $BRID_REF, $FYID_REF,$dataRow->ITEMGID_REF, 'A' ]);

                    $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS FROM TBL_MST_ITEMCATEGORY  
                                WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND ICID = ?
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                [$CYID_REF, $BRID_REF, $FYID_REF,$dataRow->ICID_REF, 'A' ]);
                   
                                   
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
                        $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:10%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td  style="width:15%;">'.$dataRow->ICODE;
                        $row = $row.'<input  type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'"  value="'.$dataRow->ITEMID.'"/></td>
                        <td style="width:15%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'"
                        value="'.$dataRow->NAME.'"/></td>';

                        $row = $row.' <td hidden id="itempartno_'.$dataRow->ITEMID.'" >'.$dataRow->SAP_PART_NO;
                        $row = $row.'<input type="hidden" id="txtitempartno_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->SAP_PART_NO.'"
                        value="'.$dataRow->SAP_PART_NO.'"/></td>';
                        
                        $row = $row.'<td style="width:10%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                        value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td hidden id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'"
                        value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                        $row = $row.'<td hidden id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="'.$FROMQTY.'"
                        value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                        $row = $row.'<td style="width:10%;" id="itax_'.$ObjItem[0]->ITEMID.'"><input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'" />'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                        
                        <td style="width:10%;">'.$BusinessUnit.'</td>
                        <td style="width:10%;">'.$ALPS_PART_NO.'</td>
                        <td style="width:10%;">'.$CUSTOMER_PART_NO.'</td>
                        <td style="width:10%;">'.$OEM_PART_NO.'</td>

                        <td hidden>Authorized</td>';

                        // $row = $row.'<td> <input type="text" id="txtitem_openval_'.$dataRow->ITEMID.'" data-desc="'. $ITEM_OPENING_VL.'"
                        // value="'.$ITEM_OPENING_VL.'"/></td>';

                        
                        $row = $row.'</tr>';
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

    


   //display attachments form
   public function attachment($id){

    $FormId = $this->form_id;
    if(!is_null($id))
    {
        $objMst = DB::table("TBL_TRN_PDMPP_HDR")
                    ->where('MPPID','=',$id)
                    ->select('*')
                    ->first();        

        $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
                    ->where('VTID','=',$this->vtid_ref)
                    ->select('VTID','VCODE','DESCRIPTIONS','INDATE')
                    ->get()
                    ->toArray();
            
                    //uplaoded docs
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
                        
            return view('transactions.Production.ManualProductionPlan.trnfrm229attachment',compact(['FormId','objMst','objMstVoucherType','objAttachments']));
    }

}

    
   public function save(Request $request) {

    
    
    $r_count1 = count($request['rowscount']);
       
        
    for ($i=0; $i<=$r_count1; $i++)
    {
        if(isset($request['ITEMID_REF_'.$i]))
        {
            $req_data[$i] = [
                
            'ICATID_REF'        => $request['REF_BUID_'.$i],  //item category
            'ITEMIDID_REF'        => $request['ITEMID_REF_'.$i],
            'UOMID_REF'        => $request['itemuom_'.$i],     
            'PD_PLAN_QTY'         => (!is_null($request['ppqty_'.$i]) && !empty($request['ppqty_'.$i]) )? $request['ppqty_'.$i] : 0,
                
            'MACHINEID_1'=> ( !is_null($request['machine1_MACH_REFID_'.$i]) && !empty($request['machine1_MACH_REFID_'.$i]) ) ? $request['machine1_MACH_REFID_'.$i] : null,
            'SHIFTID_1'  => ( !is_null($request['shift1_SHIFT_REFID_'.$i])  && !empty($request['shift1_SHIFT_REFID_'.$i]) ) ? $request['shift1_SHIFT_REFID_'.$i] : NULL,
            'QTY_1' =>      ( !is_null($request['dayqty1_'.$i]) && !empty($request['dayqty1_'.$i]) )? $request['dayqty1_'.$i] : 0,
            
            'MACHINEID_2'=> (!is_null($request['machine2_MACH_REFID_'.$i]) && !empty($request['machine2_MACH_REFID_'.$i]) ) ? $request['machine2_MACH_REFID_'.$i] : NULL,
            'SHIFTID_2'  => (!is_null($request['shift2_SHIFT_REFID_'.$i])  && !empty($request['shift2_SHIFT_REFID_'.$i]) )  ? $request['shift2_SHIFT_REFID_'.$i] : NULL,
            'QTY_2' =>      (!is_null($request['dayqty2_'.$i])   && !empty($request['dayqty2_'.$i]) ) ? $request['dayqty2_'.$i] : 0,

            'MACHINEID_3'=> (!is_null($request['machine3_MACH_REFID_'.$i]) && !empty($request['machine3_MACH_REFID_'.$i]) ) ? $request['machine3_MACH_REFID_'.$i] : NULL,
            'SHIFTID_3'  => (!is_null($request['shift3_SHIFT_REFID_'.$i])  && !empty($request['shift3_SHIFT_REFID_'.$i]) )  ? $request['shift3_SHIFT_REFID_'.$i] : NULL,
            'QTY_3' =>      (!is_null($request['dayqty3_'.$i])   && !empty($request['dayqty3_'.$i]) ) ? $request['dayqty3_'.$i] : 0,

            'MACHINEID_4'=> (!is_null($request['machine4_MACH_REFID_'.$i]) && !empty($request['machine4_MACH_REFID_'.$i]) ) ? $request['machine4_MACH_REFID_'.$i] : NULL,
            'SHIFTID_4'  => (!is_null($request['shift4_SHIFT_REFID_'.$i])  && !empty($request['shift4_SHIFT_REFID_'.$i]) )  ? $request['shift4_SHIFT_REFID_'.$i] : NULL,
            'QTY_4' =>      (!is_null($request['dayqty4_'.$i])   && !empty($request['dayqty4_'.$i]) ) ? $request['dayqty4_'.$i] : 0,
            
            'MACHINEID_5'=> (!is_null($request['machine5_MACH_REFID_'.$i]) && !empty($request['machine5_MACH_REFID_'.$i]) ) ? $request['machine5_MACH_REFID_'.$i] : NULL,
            'SHIFTID_5'  => (!is_null($request['shift5_SHIFT_REFID_'.$i])  && !empty($request['shift5_SHIFT_REFID_'.$i]) )  ? $request['shift5_SHIFT_REFID_'.$i] : NULL,
            'QTY_5' =>      (!is_null($request['dayqty5_'.$i])   && !empty($request['dayqty5_'.$i]) ) ? $request['dayqty5_'.$i] : 0,

            
            'MACHINEID_6'=> (!is_null($request['machine6_MACH_REFID_'.$i]) && !empty($request['machine6_MACH_REFID_'.$i]) ) ? $request['machine6_MACH_REFID_'.$i] : NULL,
            'SHIFTID_6'  => (!is_null($request['shift6_SHIFT_REFID_'.$i])  && !empty($request['shift6_SHIFT_REFID_'.$i]) )  ? $request['shift6_SHIFT_REFID_'.$i] : NULL,
            'QTY_6' =>      (!is_null($request['dayqty6_'.$i])   && !empty($request['dayqty6_'.$i]) ) ? $request['dayqty6_'.$i] : 0,

            'MACHINEID_7'=> (!is_null($request['machine7_MACH_REFID_'.$i]) && !empty($request['machine7_MACH_REFID_'.$i]) ) ? $request['machine7_MACH_REFID_'.$i] : NULL,
            'SHIFTID_7'  => (!is_null($request['shift7_SHIFT_REFID_'.$i])  && !empty($request['shift7_SHIFT_REFID_'.$i]) )  ? $request['shift7_SHIFT_REFID_'.$i] : NULL,
            'QTY_7' =>      (!is_null($request['dayqty7_'.$i])   && !empty($request['dayqty7_'.$i]) ) ? $request['dayqty7_'.$i] : 0,

            'MACHINEID_8'=> (!is_null($request['machine8_MACH_REFID_'.$i]) && !empty($request['machine8_MACH_REFID_'.$i]) ) ? $request['machine8_MACH_REFID_'.$i] : NULL,
            'SHIFTID_8'  => (!is_null($request['shift8_SHIFT_REFID_'.$i])  && !empty($request['shift8_SHIFT_REFID_'.$i]) )  ? $request['shift8_SHIFT_REFID_'.$i] : NULL,
            'QTY_8' =>      (!is_null($request['dayqty8_'.$i])   && !empty($request['dayqty8_'.$i]) ) ? $request['dayqty8_'.$i] : 0,

            
            'MACHINEID_9'=> (!is_null($request['machine9_MACH_REFID_'.$i]) && !empty($request['machine9_MACH_REFID_'.$i]) ) ? $request['machine9_MACH_REFID_'.$i] : NULL,
            'SHIFTID_9'  => (!is_null($request['shift9_SHIFT_REFID_'.$i])  && !empty($request['shift9_SHIFT_REFID_'.$i]) )  ? $request['shift9_SHIFT_REFID_'.$i] : NULL,
            'QTY_9' =>      (!is_null($request['dayqty9_'.$i])   && !empty($request['dayqty9_'.$i]) ) ? $request['dayqty9_'.$i] : 0,

            'MACHINEID_10'=> (!is_null($request['machine10_MACH_REFID_'.$i]) && !empty($request['machine10_MACH_REFID_'.$i]) ) ? $request['machine10_MACH_REFID_'.$i] : NULL,
            'SHIFTID_10'  => (!is_null($request['shift10_SHIFT_REFID_'.$i])  && !empty($request['shift10_SHIFT_REFID_'.$i]) )  ? $request['shift10_SHIFT_REFID_'.$i] : NULL,
            'QTY_10' =>      (!is_null($request['dayqty10_'.$i])   && !empty($request['dayqty10_'.$i]) ) ? $request['dayqty10_'.$i] : 0,

            'MACHINEID_11'=> (!is_null($request['machine11_MACH_REFID_'.$i]) && !empty($request['machine11_MACH_REFID_'.$i]) ) ? $request['machine11_MACH_REFID_'.$i] : NULL,
            'SHIFTID_11'  => (!is_null($request['shift11_SHIFT_REFID_'.$i])  && !empty($request['shift11_SHIFT_REFID_'.$i]) )  ? $request['shift11_SHIFT_REFID_'.$i] : NULL,
            'QTY_11' =>      (!is_null($request['dayqty11_'.$i])   && !empty($request['dayqty11_'.$i]) ) ? $request['dayqty11_'.$i] : 0,

            'MACHINEID_12'=> (!is_null($request['machine12_MACH_REFID_'.$i]) && !empty($request['machine12_MACH_REFID_'.$i]) ) ? $request['machine12_MACH_REFID_'.$i] : NULL,
            'SHIFTID_12'  => (!is_null($request['shift12_SHIFT_REFID_'.$i])  && !empty($request['shift12_SHIFT_REFID_'.$i]) )  ? $request['shift12_SHIFT_REFID_'.$i] : NULL,
            'QTY_12' =>      (!is_null($request['dayqty12_'.$i])   && !empty($request['dayqty12_'.$i]) ) ? $request['dayqty12_'.$i] : 0,

            'MACHINEID_13'=> (!is_null($request['machine13_MACH_REFID_'.$i]) && !empty($request['machine13_MACH_REFID_'.$i]) ) ? $request['machine13_MACH_REFID_'.$i] : NULL,
            'SHIFTID_13'  => (!is_null($request['shift13_SHIFT_REFID_'.$i])  && !empty($request['shift13_SHIFT_REFID_'.$i]) )  ? $request['shift13_SHIFT_REFID_'.$i] : NULL,
            'QTY_13' =>      (!is_null($request['dayqty13_'.$i])   && !empty($request['dayqty13_'.$i]) ) ? $request['dayqty13_'.$i] : 0,

            'MACHINEID_14'=> (!is_null($request['machine14_MACH_REFID_'.$i]) && !empty($request['machine14_MACH_REFID_'.$i]) ) ? $request['machine14_MACH_REFID_'.$i] : NULL,
            'SHIFTID_14'  => (!is_null($request['shift14_SHIFT_REFID_'.$i])  && !empty($request['shift14_SHIFT_REFID_'.$i]) )  ? $request['shift14_SHIFT_REFID_'.$i] : NULL,
            'QTY_14' =>      (!is_null($request['dayqty14_'.$i])   && !empty($request['dayqty14_'.$i]) ) ? $request['dayqty14_'.$i] : 0,

            'MACHINEID_15'=> (!is_null($request['machine15_MACH_REFID_'.$i]) && !empty($request['machine15_MACH_REFID_'.$i]) ) ? $request['machine15_MACH_REFID_'.$i] : NULL,
            'SHIFTID_15'  => (!is_null($request['shift15_SHIFT_REFID_'.$i])  && !empty($request['shift15_SHIFT_REFID_'.$i]) )  ? $request['shift15_SHIFT_REFID_'.$i] : NULL,
            'QTY_15' =>      (!is_null($request['dayqty15_'.$i])   && !empty($request['dayqty15_'.$i]) ) ? $request['dayqty15_'.$i] : 0,

            'MACHINEID_16'=> (!is_null($request['machine16_MACH_REFID_'.$i]) && !empty($request['machine16_MACH_REFID_'.$i]) ) ? $request['machine16_MACH_REFID_'.$i] : NULL,
            'SHIFTID_16'  => (!is_null($request['shift16_SHIFT_REFID_'.$i])  && !empty($request['shift16_SHIFT_REFID_'.$i]) )  ? $request['shift16_SHIFT_REFID_'.$i] : NULL,
            'QTY_16' =>      (!is_null($request['dayqty16_'.$i])   && !empty($request['dayqty16_'.$i]) ) ? $request['dayqty16_'.$i] : 0,

            'MACHINEID_17'=> (!is_null($request['machine17_MACH_REFID_'.$i]) && !empty($request['machine17_MACH_REFID_'.$i]) ) ? $request['machine17_MACH_REFID_'.$i] : NULL,
            'SHIFTID_17'  => (!is_null($request['shift17_SHIFT_REFID_'.$i])  && !empty($request['shift17_SHIFT_REFID_'.$i]) )  ? $request['shift17_SHIFT_REFID_'.$i] : NULL,
            'QTY_17' =>      (!is_null($request['dayqty17_'.$i])   && !empty($request['dayqty17_'.$i]) ) ? $request['dayqty17_'.$i] : 0,

            'MACHINEID_18'=> (!is_null($request['machine18_MACH_REFID_'.$i]) && !empty($request['machine18_MACH_REFID_'.$i]) ) ? $request['machine18_MACH_REFID_'.$i] : NULL,
            'SHIFTID_18'  => (!is_null($request['shift18_SHIFT_REFID_'.$i])  && !empty($request['shift18_SHIFT_REFID_'.$i]) )  ? $request['shift18_SHIFT_REFID_'.$i] : NULL,
            'QTY_18' =>      (!is_null($request['dayqty18_'.$i])   && !empty($request['dayqty18_'.$i]) ) ? $request['dayqty18_'.$i] : 0,

            'MACHINEID_19'=> (!is_null($request['machine19_MACH_REFID_'.$i]) && !empty($request['machine19_MACH_REFID_'.$i]) ) ? $request['machine19_MACH_REFID_'.$i] : NULL,
            'SHIFTID_19'  => (!is_null($request['shift19_SHIFT_REFID_'.$i])  && !empty($request['shift19_SHIFT_REFID_'.$i]) )  ? $request['shift19_SHIFT_REFID_'.$i] : NULL,
            'QTY_19' =>      (!is_null($request['dayqty19_'.$i])   && !empty($request['dayqty19_'.$i]) ) ? $request['dayqty19_'.$i] : 0,

            'MACHINEID_20'=> (!is_null($request['machine20_MACH_REFID_'.$i]) && !empty($request['machine20_MACH_REFID_'.$i]) ) ? $request['machine20_MACH_REFID_'.$i] : NULL,
            'SHIFTID_20'  => (!is_null($request['shift20_SHIFT_REFID_'.$i])  && !empty($request['shift20_SHIFT_REFID_'.$i]) )  ? $request['shift20_SHIFT_REFID_'.$i] : NULL,
            'QTY_20' =>      (!is_null($request['dayqty20_'.$i])   && !empty($request['dayqty20_'.$i]) ) ? $request['dayqty20_'.$i] : 0,

            'MACHINEID_21'=> (!is_null($request['machine21_MACH_REFID_'.$i]) && !empty($request['machine21_MACH_REFID_'.$i]) ) ? $request['machine21_MACH_REFID_'.$i] : NULL,
            'SHIFTID_21'  => (!is_null($request['shift21_SHIFT_REFID_'.$i])  && !empty($request['shift21_SHIFT_REFID_'.$i]) )  ? $request['shift21_SHIFT_REFID_'.$i] : NULL,
            'QTY_21' =>      (!is_null($request['dayqty21_'.$i])   && !empty($request['dayqty21_'.$i]) ) ? $request['dayqty21_'.$i] : 0,

            'MACHINEID_22'=> (!is_null($request['machine22_MACH_REFID_'.$i]) && !empty($request['machine22_MACH_REFID_'.$i]) ) ? $request['machine22_MACH_REFID_'.$i] : NULL,
            'SHIFTID_22'  => (!is_null($request['shift22_SHIFT_REFID_'.$i])  && !empty($request['shift22_SHIFT_REFID_'.$i]) )  ? $request['shift22_SHIFT_REFID_'.$i] : NULL,
            'QTY_22' =>      (!is_null($request['dayqty22_'.$i])   && !empty($request['dayqty22_'.$i]) ) ? $request['dayqty22_'.$i] : 0,

            'MACHINEID_23'=> (!is_null($request['machine23_MACH_REFID_'.$i]) && !empty($request['machine23_MACH_REFID_'.$i]) ) ? $request['machine23_MACH_REFID_'.$i] : NULL,
            'SHIFTID_23'  => (!is_null($request['shift23_SHIFT_REFID_'.$i])  && !empty($request['shift23_SHIFT_REFID_'.$i]) )  ? $request['shift23_SHIFT_REFID_'.$i] : NULL,
            'QTY_23' =>      (!is_null($request['dayqty23_'.$i])   && !empty($request['dayqty23_'.$i]) ) ? $request['dayqty23_'.$i] : 0,

            'MACHINEID_24'=> (!is_null($request['machine24_MACH_REFID_'.$i]) && !empty($request['machine24_MACH_REFID_'.$i]) ) ? $request['machine24_MACH_REFID_'.$i] : NULL,
            'SHIFTID_24'  => (!is_null($request['shift24_SHIFT_REFID_'.$i])  && !empty($request['shift24_SHIFT_REFID_'.$i]) )  ? $request['shift24_SHIFT_REFID_'.$i] : NULL,
            'QTY_24' =>      (!is_null($request['dayqty24_'.$i])   && !empty($request['dayqty24_'.$i]) ) ? $request['dayqty24_'.$i] : 0,

            'MACHINEID_25'=> (!is_null($request['machine25_MACH_REFID_'.$i]) && !empty($request['machine25_MACH_REFID_'.$i]) ) ? $request['machine25_MACH_REFID_'.$i] : NULL,
            'SHIFTID_25'  => (!is_null($request['shift25_SHIFT_REFID_'.$i])  && !empty($request['shift25_SHIFT_REFID_'.$i]) )  ? $request['shift25_SHIFT_REFID_'.$i] : NULL,
            'QTY_25' =>      (!is_null($request['dayqty25_'.$i])   && !empty($request['dayqty25_'.$i]) ) ? $request['dayqty25_'.$i] : 0,

            'MACHINEID_26'=> (!is_null($request['machine26_MACH_REFID_'.$i]) && !empty($request['machine26_MACH_REFID_'.$i]) ) ? $request['machine26_MACH_REFID_'.$i] : NULL,
            'SHIFTID_26'  => (!is_null($request['shift26_SHIFT_REFID_'.$i])  && !empty($request['shift26_SHIFT_REFID_'.$i]) )  ? $request['shift26_SHIFT_REFID_'.$i] : NULL,
            'QTY_26' =>      (!is_null($request['dayqty26_'.$i])   && !empty($request['dayqty26_'.$i]) ) ? $request['dayqty26_'.$i] : 0,

            'MACHINEID_27'=> (!is_null($request['machine27_MACH_REFID_'.$i]) && !empty($request['machine27_MACH_REFID_'.$i]) ) ? $request['machine27_MACH_REFID_'.$i] : NULL,
            'SHIFTID_27'  => (!is_null($request['shift27_SHIFT_REFID_'.$i])  && !empty($request['shift27_SHIFT_REFID_'.$i]) )  ? $request['shift27_SHIFT_REFID_'.$i] : NULL,
            'QTY_27' =>      (!is_null($request['dayqty27_'.$i])   && !empty($request['dayqty27_'.$i]) ) ? $request['dayqty27_'.$i] : 0,

            'MACHINEID_28'=> (!is_null($request['machine28_MACH_REFID_'.$i]) && !empty($request['machine28_MACH_REFID_'.$i]) ) ? $request['machine28_MACH_REFID_'.$i] : NULL,
            'SHIFTID_28'  => (!is_null($request['shift28_SHIFT_REFID_'.$i])  && !empty($request['shift28_SHIFT_REFID_'.$i]) )  ? $request['shift28_SHIFT_REFID_'.$i] : NULL,
            'QTY_28' =>      (!is_null($request['dayqty28_'.$i])   && !empty($request['dayqty28_'.$i]) ) ? $request['dayqty28_'.$i] : 0,

            'MACHINEID_29'=> (!is_null($request['machine29_MACH_REFID_'.$i]) && !empty($request['machine29_MACH_REFID_'.$i]) ) ? $request['machine29_MACH_REFID_'.$i] : NULL,
            'SHIFTID_29'  => (!is_null($request['shift29_SHIFT_REFID_'.$i])  && !empty($request['shift29_SHIFT_REFID_'.$i]) )  ? $request['shift29_SHIFT_REFID_'.$i] : NULL,
            'QTY_29' =>      (!is_null($request['dayqty29_'.$i])   && !empty($request['dayqty29_'.$i]) ) ? $request['dayqty29_'.$i] : 0,

            'MACHINEID_30'=> (!is_null($request['machine30_MACH_REFID_'.$i]) && !empty($request['machine30_MACH_REFID_'.$i]) ) ? $request['machine30_MACH_REFID_'.$i] : NULL,
            'SHIFTID_30'  => (!is_null($request['shift30_SHIFT_REFID_'.$i])  && !empty($request['shift30_SHIFT_REFID_'.$i]) )  ? $request['shift30_SHIFT_REFID_'.$i] : NULL,
            'QTY_30' =>      (!is_null($request['dayqty30_'.$i])   && !empty($request['dayqty30_'.$i]) ) ? $request['dayqty30_'.$i] : 0,

            'MACHINEID_31'=> (!is_null($request['machine31_MACH_REFID_'.$i]) && !empty($request['machine31_MACH_REFID_'.$i]) ) ? $request['machine31_MACH_REFID_'.$i] : NULL,
            'SHIFTID_31'  => (!is_null($request['shift31_SHIFT_REFID_'.$i])  && !empty($request['shift31_SHIFT_REFID_'.$i]) )  ? $request['shift31_SHIFT_REFID_'.$i] : NULL,
            'QTY_31' =>      (!is_null($request['dayqty31_'.$i])   && !empty($request['dayqty31_'.$i]) ) ? $request['dayqty31_'.$i] : 0,
            
        
        ];
        }
    }
    
        
            $wrapped_links["ITEM"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
            

            $VTID_REF     =   $this->vtid_ref;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $MPP_DOC_NO = trim( strtoupper($request['MPPDOCNO']) );
            $MPP_DOC_DT = $request['MPPDT'];
            $PERIOD_MTID_REF = $request['MONTH_DT'];
            $MONTH_DAYS = $request['act_month_day'];

          
            $log_data = [ 
                $MPP_DOC_NO,    $MPP_DOC_DT,    $PERIOD_MTID_REF, $CYID_REF,
                $BRID_REF,      $FYID_REF,      $VTID_REF,        $XMLMAT , 
                $USERID,         Date('Y-m-d'), Date('h:i:s.u'),  $ACTIONNAME,
                $IPADDRESS, $MONTH_DAYS
            ];

            
            

            $sp_result = DB::select('EXEC SP_MPP_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
           
            
        
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
       // DD($CYID_REF.$BRID_REF.$FYID_REF.$id);
        
        if(!is_null($id))
        {
            $objSE = DB::table('TBL_TRN_PDMPP_HDR')
                             ->where('TBL_TRN_PDMPP_HDR.FYID_REF','=',$FYID_REF)
                             ->where('TBL_TRN_PDMPP_HDR.CYID_REF','=',$CYID_REF)
                             ->where('TBL_TRN_PDMPP_HDR.BRID_REF','=',$BRID_REF)
                             ->where('TBL_TRN_PDMPP_HDR.MPPID','=',$id)
                             ->select('TBL_TRN_PDMPP_HDR.*')
                             ->first();
           // DD( $objSE);

            $objSEMAT = DB::table('TBL_TRN_PDMPP_MAT')                    
                             ->where('TBL_TRN_PDMPP_MAT.MPPID_REF','=',$id)
                             ->leftJoin('TBL_MST_ITEMCATEGORY', 'TBL_TRN_PDMPP_MAT.ICATID_REF','=','TBL_MST_ITEMCATEGORY.ICID') 
                             ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_PDMPP_MAT.ITEMIDID_REF','=','TBL_MST_ITEM.ITEMID') 
                             ->leftJoin('TBL_MST_UOM','TBL_TRN_PDMPP_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID') 
                             ->select('TBL_TRN_PDMPP_MAT.*','TBL_MST_ITEMCATEGORY.ICCODE','TBL_MST_ITEMCATEGORY.DESCRIPTIONS as ICAT_DESC','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ICODE','TBL_MST_UOM.UOMID',
                             'TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS')
                             ->orderBy('TBL_TRN_PDMPP_MAT.MPP_MATID','ASC')
                             ->get()->toArray();
                           
         
            $objCount1 = count($objSEMAT);            
            //----------------------------------------
                $tempObj = $objSEMAT;
                foreach ($tempObj as $mindex => $mvalue) {

                    //------------------day_1
                    $mach_id = "";
                    $mach_id = ( !is_null($mvalue->MACHINEID_1) && $mvalue->MACHINEID_1!=0 ) ? $mvalue->MACHINEID_1 : "";
                    $objSEMAT[$mindex]->MACH_1_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_1_DESC = $this->MachineCode($mach_id);

                    //--------SHIFT
                    $shft_id="";
                    
                    $shft_id = ( !is_null($mvalue->SHIFTID_1) && $mvalue->SHIFTID_1!=0 ) ? $mvalue->SHIFTID_1 : "";
                    $objSEMAT[$mindex]->SHFT_1_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_1_DESC = $this->ShiftCode($shft_id);

                    //------------------day_2
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_2) && $mvalue->MACHINEID_2!=0 ) ? $mvalue->MACHINEID_2 : "";
                    $objSEMAT[$mindex]->MACH_2_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_2_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_2) && $mvalue->SHIFTID_2!=0 ) ? $mvalue->SHIFTID_2 : "";
                    $objSEMAT[$mindex]->SHFT_2_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_2_DESC = $this->ShiftCode($shft_id);

                    //-----------day_3
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_3) && $mvalue->MACHINEID_3!=0 ) ? $mvalue->MACHINEID_3 : "";
                    $objSEMAT[$mindex]->MACH_3_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_3_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_3) && $mvalue->SHIFTID_3!=0 ) ? $mvalue->SHIFTID_3 : "";
                    $objSEMAT[$mindex]->SHFT_3_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_3_DESC = $this->ShiftCode($shft_id);

                    //---------------------day_4
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_4) && $mvalue->MACHINEID_4!=0 ) ? $mvalue->MACHINEID_4 : "";
                    $objSEMAT[$mindex]->MACH_4_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_4_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_4) && $mvalue->SHIFTID_4!=0 ) ? $mvalue->SHIFTID_4 : "";
                    $objSEMAT[$mindex]->SHFT_4_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_4_DESC = $this->ShiftCode($shft_id);


                    //-----------day_5
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_5) && $mvalue->MACHINEID_5!=0 ) ? $mvalue->MACHINEID_5 : "";
                    $objSEMAT[$mindex]->MACH_5_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_5_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_5) && $mvalue->SHIFTID_5!=0 ) ? $mvalue->SHIFTID_5 : "";
                    $objSEMAT[$mindex]->SHFT_5_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_5_DESC = $this->ShiftCode($shft_id);


                    //-----------day_6
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_6) && $mvalue->MACHINEID_6!=0 ) ? $mvalue->MACHINEID_6 : "";
                    $objSEMAT[$mindex]->MACH_6_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_6_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_6) && $mvalue->SHIFTID_6!=0 ) ? $mvalue->SHIFTID_6 : "";
                    $objSEMAT[$mindex]->SHFT_6_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_6_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_7
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_7) && $mvalue->MACHINEID_7!=0 ) ? $mvalue->MACHINEID_7 : "";
                    $objSEMAT[$mindex]->MACH_7_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_7_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_7) && $mvalue->SHIFTID_7!=0 ) ? $mvalue->SHIFTID_7 : "";
                    $objSEMAT[$mindex]->SHFT_7_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_7_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_8
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_8) && $mvalue->MACHINEID_8!=0 ) ? $mvalue->MACHINEID_8 : "";
                    $objSEMAT[$mindex]->MACH_8_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_8_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_8) && $mvalue->SHIFTID_8!=0 ) ? $mvalue->SHIFTID_8 : "";
                    $objSEMAT[$mindex]->SHFT_8_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_8_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_9
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_9) && $mvalue->MACHINEID_9!=0 ) ? $mvalue->MACHINEID_9 : "";
                    $objSEMAT[$mindex]->MACH_9_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_9_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_9) && $mvalue->SHIFTID_9!=0 ) ? $mvalue->SHIFTID_9 : "";
                    $objSEMAT[$mindex]->SHFT_9_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_9_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_10
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_10) && $mvalue->MACHINEID_10!=0 ) ? $mvalue->MACHINEID_10 : "";
                    $objSEMAT[$mindex]->MACH_10_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_10_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_10) && $mvalue->SHIFTID_10!=0 ) ? $mvalue->SHIFTID_10 : "";
                    $objSEMAT[$mindex]->SHFT_10_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_10_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_11
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_11) && $mvalue->MACHINEID_11!=0 ) ? $mvalue->MACHINEID_11 : "";
                    $objSEMAT[$mindex]->MACH_11_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_11_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_11) && $mvalue->SHIFTID_11!=0 ) ? $mvalue->SHIFTID_11 : "";
                    $objSEMAT[$mindex]->SHFT_11_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_11_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_12
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_12) && $mvalue->MACHINEID_12!=0 ) ? $mvalue->MACHINEID_12 : "";
                    $objSEMAT[$mindex]->MACH_12_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_12_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_12) && $mvalue->SHIFTID_12!=0 ) ? $mvalue->SHIFTID_12 : "";
                    $objSEMAT[$mindex]->SHFT_12_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_12_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_13
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_13) && $mvalue->MACHINEID_13!=0 ) ? $mvalue->MACHINEID_13 : "";
                    $objSEMAT[$mindex]->MACH_13_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_13_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_13) && $mvalue->SHIFTID_13!=0 ) ? $mvalue->SHIFTID_13 : "";
                    $objSEMAT[$mindex]->SHFT_13_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_13_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_14
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_14) && $mvalue->MACHINEID_14!=0 ) ? $mvalue->MACHINEID_14 : "";
                    $objSEMAT[$mindex]->MACH_14_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_14_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_14) && $mvalue->SHIFTID_14!=0 ) ? $mvalue->SHIFTID_14 : "";
                    $objSEMAT[$mindex]->SHFT_14_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_14_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_15
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_15) && $mvalue->MACHINEID_15!=0 ) ? $mvalue->MACHINEID_15 : "";
                    $objSEMAT[$mindex]->MACH_15_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_15_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_15) && $mvalue->SHIFTID_15!=0 ) ? $mvalue->SHIFTID_15 : "";
                    $objSEMAT[$mindex]->SHFT_15_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_15_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_16
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_16) && $mvalue->MACHINEID_16!=0 ) ? $mvalue->MACHINEID_16 : "";
                    $objSEMAT[$mindex]->MACH_16_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_16_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_16) && $mvalue->SHIFTID_16!=0 ) ? $mvalue->SHIFTID_16 : "";
                    $objSEMAT[$mindex]->SHFT_16_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_16_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_17
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_17) && $mvalue->MACHINEID_17!=0 ) ? $mvalue->MACHINEID_17 : "";
                    $objSEMAT[$mindex]->MACH_17_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_17_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_17) && $mvalue->SHIFTID_17!=0 ) ? $mvalue->SHIFTID_17 : "";
                    $objSEMAT[$mindex]->SHFT_17_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_17_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_18
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_18) && $mvalue->MACHINEID_18!=0 ) ? $mvalue->MACHINEID_18 : "";
                    $objSEMAT[$mindex]->MACH_18_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_18_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_18) && $mvalue->SHIFTID_18!=0 ) ? $mvalue->SHIFTID_18 : "";
                    $objSEMAT[$mindex]->SHFT_18_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_18_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_19
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_19) && $mvalue->MACHINEID_19!=0 ) ? $mvalue->MACHINEID_19 : "";
                    $objSEMAT[$mindex]->MACH_19_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_19_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_19) && $mvalue->SHIFTID_19!=0 ) ? $mvalue->SHIFTID_19 : "";
                    $objSEMAT[$mindex]->SHFT_19_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_19_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_20
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_20) && $mvalue->MACHINEID_20!=0 ) ? $mvalue->MACHINEID_20 : "";
                    $objSEMAT[$mindex]->MACH_20_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_20_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_20) && $mvalue->SHIFTID_20!=0 ) ? $mvalue->SHIFTID_20 : "";
                    $objSEMAT[$mindex]->SHFT_20_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_20_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_21
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_21) && $mvalue->MACHINEID_21!=0 ) ? $mvalue->MACHINEID_21 : "";
                    $objSEMAT[$mindex]->MACH_21_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_21_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_21) && $mvalue->SHIFTID_21!=0 ) ? $mvalue->SHIFTID_21 : "";
                    $objSEMAT[$mindex]->SHFT_21_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_21_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_22
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_22) && $mvalue->MACHINEID_22!=0 ) ? $mvalue->MACHINEID_22 : "";
                    $objSEMAT[$mindex]->MACH_22_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_22_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_22) && $mvalue->SHIFTID_22!=0 ) ? $mvalue->SHIFTID_22 : "";
                    $objSEMAT[$mindex]->SHFT_22_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_22_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_23
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_23) && $mvalue->MACHINEID_23!=0 ) ? $mvalue->MACHINEID_23 : "";
                    $objSEMAT[$mindex]->MACH_23_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_23_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_23) && $mvalue->SHIFTID_23!=0 ) ? $mvalue->SHIFTID_23 : "";
                    $objSEMAT[$mindex]->SHFT_23_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_23_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_24
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_24) && $mvalue->MACHINEID_24!=0 ) ? $mvalue->MACHINEID_24 : "";
                    $objSEMAT[$mindex]->MACH_24_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_24_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_24) && $mvalue->SHIFTID_24!=0 ) ? $mvalue->SHIFTID_24 : "";
                    $objSEMAT[$mindex]->SHFT_24_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_24_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_25
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_25) && $mvalue->MACHINEID_25!=0 ) ? $mvalue->MACHINEID_25 : "";
                    $objSEMAT[$mindex]->MACH_25_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_25_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_25) && $mvalue->SHIFTID_25!=0 ) ? $mvalue->SHIFTID_25 : "";
                    $objSEMAT[$mindex]->SHFT_25_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_25_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_26
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_26) && $mvalue->MACHINEID_26!=0 ) ? $mvalue->MACHINEID_26 : "";
                    $objSEMAT[$mindex]->MACH_26_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_26_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_26) && $mvalue->SHIFTID_26!=0 ) ? $mvalue->SHIFTID_26 : "";
                    $objSEMAT[$mindex]->SHFT_26_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_26_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_27
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_27) && $mvalue->MACHINEID_27!=0 ) ? $mvalue->MACHINEID_27 : "";
                    $objSEMAT[$mindex]->MACH_27_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_27_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_27) && $mvalue->SHIFTID_27!=0 ) ? $mvalue->SHIFTID_27 : "";
                    $objSEMAT[$mindex]->SHFT_27_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_27_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_28
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_28) && $mvalue->MACHINEID_28!=0 ) ? $mvalue->MACHINEID_28 : "";
                    $objSEMAT[$mindex]->MACH_28_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_28_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_28) && $mvalue->SHIFTID_28!=0 ) ? $mvalue->SHIFTID_28 : "";
                    $objSEMAT[$mindex]->SHFT_28_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_28_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_29
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_29) && $mvalue->MACHINEID_29!=0 ) ? $mvalue->MACHINEID_29 : "";
                    $objSEMAT[$mindex]->MACH_29_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_29_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_29) && $mvalue->SHIFTID_29!=0 ) ? $mvalue->SHIFTID_29 : "";
                    $objSEMAT[$mindex]->SHFT_29_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_29_DESC = $this->ShiftCode($shft_id);


                    //--------------------day_30
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_30) && $mvalue->MACHINEID_30!=0 ) ? $mvalue->MACHINEID_30 : "";
                    $objSEMAT[$mindex]->MACH_30_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_30_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_30) && $mvalue->SHIFTID_30!=0 ) ? $mvalue->SHIFTID_30 : "";
                    $objSEMAT[$mindex]->SHFT_30_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_30_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_31
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_31) && $mvalue->MACHINEID_31!=0 ) ? $mvalue->MACHINEID_31 : "";
                    $objSEMAT[$mindex]->MACH_31_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_31_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_31) && $mvalue->SHIFTID_31!=0 ) ? $mvalue->SHIFTID_31 : "";
                    $objSEMAT[$mindex]->SHFT_31_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_31_DESC = $this->ShiftCode($shft_id);


                    //------------------------------
                } // foreach end

                $tempObj="";
        
             //   DD($objSEMAT); 
            //----------------------------------------
     
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
            
        
  
            
            $objItems = DB::table('TBL_MST_ITEM')->select('*')
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('FYID_REF','=',$FYID_REF)
            ->get() ->toArray(); 
      
   
           $cur_date = Date('Y-m-d');
           $objItemCategoryList = DB::select('select * from TBL_MST_ITEMCATEGORY  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?) and CYID_REF = ?   and STATUS = ? ',  [$cur_date,$CYID_REF,'A']);
               
   
           $objMonths = DB::select('select * from TBL_MST_MONTH  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?)  and STATUS = ? ', [$cur_date,'A']);
           

           $objlast_DT = DB::select('SELECT MAX(MPP_DOC_DT) MPP_DOC_DT FROM TBL_TRN_PDMPP_HDR  
           WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND VTID_REF = ? AND STATUS = ?', 
           [$CYID_REF, $BRID_REF, $FYID_REF, $this->vtid_ref, 'N' ]);


        return view('transactions.Production.ManualProductionPlan.trnfrm229edit',compact(['objSE','objRights','objCount1',
           'objSEMAT','objItems','objlast_DT','objItemCategoryList','objMonths','objlast_DT']));
        }
     
       }

    public function amendment($id){
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');
            $FYID_REF   =   Session::get('FYID_REF'); 
            $Status     =   'A';
        // DD($CYID_REF.$BRID_REF.$FYID_REF.$id);
            
            if(!is_null($id))
            {
                $objSE = DB::table('TBL_TRN_PRAF01_HDR')
                                ->where('TBL_TRN_PRAF01_HDR.FYID_REF1','=',$FYID_REF)
                                ->where('TBL_TRN_PRAF01_HDR.CYID_REF','=',$CYID_REF)
                                ->where('TBL_TRN_PRAF01_HDR.BRID_REF','=',$BRID_REF)
                                ->where('TBL_TRN_PRAF01_HDR.AFPID','=',$id)
                                ->leftJoin('TBL_MST_DEPARTMENT', 'TBL_TRN_PRAF01_HDR.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID') 
                                ->leftJoin('TBL_MST_FYEAR', 'TBL_TRN_PRAF01_HDR.FYID_REF','=','TBL_MST_FYEAR.FYID') 
                                ->select('TBL_TRN_PRAF01_HDR.*','TBL_MST_DEPARTMENT.NAME','TBL_MST_FYEAR.FYDESCRIPTION')
                                ->first();
            // DD( $objSE);

                $objSEMAT = DB::table('TBL_TRN_PRAF01_MAT')                    
                                ->where('TBL_TRN_PRAF01_MAT.AFPID_REF','=',$id)
                                ->leftJoin('TBL_MST_BUSINESSUNIT', 'TBL_TRN_PRAF01_MAT.BUID_REF','=','TBL_MST_BUSINESSUNIT.BUID') 
                                ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_PRAF01_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID') 
                                ->leftJoin('TBL_MST_UOM','TBL_TRN_PRAF01_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID') 
                                ->select('TBL_TRN_PRAF01_MAT.*','TBL_MST_BUSINESSUNIT.BUCODE','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_UOM.UOMID',
                                'TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS')
                                ->orderBy('TBL_TRN_PRAF01_MAT.AFPMATID','ASC')
                                ->get()->toArray();
                            
            // DD($objSEMAT); 
                $objCount1 = count($objSEMAT);            
                
            
        
                $objRights = DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                                
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();
                
            
    
                
                $objItems = DB::table('TBL_MST_ITEM')->select('*')
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->where('FYID_REF','=',$FYID_REF)
                ->get() ->toArray(); 
        
                $objDepartmentList = DB::table('TBL_MST_DEPARTMENT')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('DEPID','DCODE','NAME')
            ->get(); 
    


            $year = date("Y");
            $objFyearList = DB::table('TBL_MST_FYEAR')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('BRID_REF','=',Session::get('BRID_REF'))
                    ->where('STATUS','=','A')
                    ->where('FYENDYEAR','>=',$year)            
                ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                ->select('FYID','FYCODE','FYDESCRIPTION')
                ->get(); 
        $objCustomerList = DB::table('TBL_MST_CUSTOMER')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('CID','CCODE','NAME')
            ->get(); 
        $objBusinessUnitList = DB::table('TBL_MST_BUSINESSUNIT')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('BUID','BUCODE','BUNAME')
            ->get(); 

            $objlast_DT =$objSE->AFP_DT;  

            $AFSA_MAX = DB::table('TBL_TRN_PRAF02_HDR')
            ->where('VTID_REF','=',173)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('AFPID_REF','=',$id)    
            ->max('ANO');
           
             $ANumber=$AFSA_MAX+1;

            return view('transactions.Production.ManualProductionPlan.trnfrm229amendment',compact(['objSE','objRights','objCount1',
            'objSEMAT','objItems','objDepartmentList','objFyearList','objCustomerList','objBusinessUnitList','objlast_DT','ANumber']));
            }
     
       }
       //update the data
     
    public function view($id){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
       // DD($CYID_REF.$BRID_REF.$FYID_REF.$id);
        
        if(!is_null($id))
        {
            $objSE = DB::table('TBL_TRN_PDMPP_HDR')
                             ->where('TBL_TRN_PDMPP_HDR.FYID_REF','=',$FYID_REF)
                             ->where('TBL_TRN_PDMPP_HDR.CYID_REF','=',$CYID_REF)
                             ->where('TBL_TRN_PDMPP_HDR.BRID_REF','=',$BRID_REF)
                             ->where('TBL_TRN_PDMPP_HDR.MPPID','=',$id)
                             ->select('TBL_TRN_PDMPP_HDR.*')
                             ->first();
           // DD( $objSE);

            $objSEMAT = DB::table('TBL_TRN_PDMPP_MAT')                    
                             ->where('TBL_TRN_PDMPP_MAT.MPPID_REF','=',$id)
                             ->leftJoin('TBL_MST_ITEMCATEGORY', 'TBL_TRN_PDMPP_MAT.ICATID_REF','=','TBL_MST_ITEMCATEGORY.ICID') 
                             ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_PDMPP_MAT.ITEMIDID_REF','=','TBL_MST_ITEM.ITEMID') 
                             ->leftJoin('TBL_MST_UOM','TBL_TRN_PDMPP_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID') 
                             ->select('TBL_TRN_PDMPP_MAT.*','TBL_MST_ITEMCATEGORY.ICCODE','TBL_MST_ITEMCATEGORY.DESCRIPTIONS as ICAT_DESC','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ICODE','TBL_MST_UOM.UOMID',
                             'TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS')
                             ->orderBy('TBL_TRN_PDMPP_MAT.MPP_MATID','ASC')
                             ->get()->toArray();
                           
         
            $objCount1 = count($objSEMAT);            
            //----------------------------------------
                $tempObj = $objSEMAT;
                foreach ($tempObj as $mindex => $mvalue) {

                    //------------------day_1
                    $mach_id = "";
                    $mach_id = ( !is_null($mvalue->MACHINEID_1) && $mvalue->MACHINEID_1!=0 ) ? $mvalue->MACHINEID_1 : "";
                    $objSEMAT[$mindex]->MACH_1_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_1_DESC = $this->MachineCode($mach_id);

                    //--------SHIFT
                    $shft_id="";
                    
                    $shft_id = ( !is_null($mvalue->SHIFTID_1) && $mvalue->SHIFTID_1!=0 ) ? $mvalue->SHIFTID_1 : "";
                    $objSEMAT[$mindex]->SHFT_1_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_1_DESC = $this->ShiftCode($shft_id);

                    //------------------day_2
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_2) && $mvalue->MACHINEID_2!=0 ) ? $mvalue->MACHINEID_2 : "";
                    $objSEMAT[$mindex]->MACH_2_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_2_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_2) && $mvalue->SHIFTID_2!=0 ) ? $mvalue->SHIFTID_2 : "";
                    $objSEMAT[$mindex]->SHFT_2_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_2_DESC = $this->ShiftCode($shft_id);

                    //-----------day_3
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_3) && $mvalue->MACHINEID_3!=0 ) ? $mvalue->MACHINEID_3 : "";
                    $objSEMAT[$mindex]->MACH_3_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_3_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_3) && $mvalue->SHIFTID_3!=0 ) ? $mvalue->SHIFTID_3 : "";
                    $objSEMAT[$mindex]->SHFT_3_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_3_DESC = $this->ShiftCode($shft_id);

                    //---------------------day_4
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_4) && $mvalue->MACHINEID_4!=0 ) ? $mvalue->MACHINEID_4 : "";
                    $objSEMAT[$mindex]->MACH_4_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_4_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_4) && $mvalue->SHIFTID_4!=0 ) ? $mvalue->SHIFTID_4 : "";
                    $objSEMAT[$mindex]->SHFT_4_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_4_DESC = $this->ShiftCode($shft_id);


                    //-----------day_5
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_5) && $mvalue->MACHINEID_5!=0 ) ? $mvalue->MACHINEID_5 : "";
                    $objSEMAT[$mindex]->MACH_5_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_5_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_5) && $mvalue->SHIFTID_5!=0 ) ? $mvalue->SHIFTID_5 : "";
                    $objSEMAT[$mindex]->SHFT_5_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_5_DESC = $this->ShiftCode($shft_id);


                    //-----------day_6
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_6) && $mvalue->MACHINEID_6!=0 ) ? $mvalue->MACHINEID_6 : "";
                    $objSEMAT[$mindex]->MACH_6_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_6_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_6) && $mvalue->SHIFTID_6!=0 ) ? $mvalue->SHIFTID_6 : "";
                    $objSEMAT[$mindex]->SHFT_6_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_6_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_7
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_7) && $mvalue->MACHINEID_7!=0 ) ? $mvalue->MACHINEID_7 : "";
                    $objSEMAT[$mindex]->MACH_7_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_7_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_7) && $mvalue->SHIFTID_7!=0 ) ? $mvalue->SHIFTID_7 : "";
                    $objSEMAT[$mindex]->SHFT_7_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_7_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_8
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_8) && $mvalue->MACHINEID_8!=0 ) ? $mvalue->MACHINEID_8 : "";
                    $objSEMAT[$mindex]->MACH_8_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_8_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_8) && $mvalue->SHIFTID_8!=0 ) ? $mvalue->SHIFTID_8 : "";
                    $objSEMAT[$mindex]->SHFT_8_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_8_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_9
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_9) && $mvalue->MACHINEID_9!=0 ) ? $mvalue->MACHINEID_9 : "";
                    $objSEMAT[$mindex]->MACH_9_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_9_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_9) && $mvalue->SHIFTID_9!=0 ) ? $mvalue->SHIFTID_9 : "";
                    $objSEMAT[$mindex]->SHFT_9_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_9_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_10
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_10) && $mvalue->MACHINEID_10!=0 ) ? $mvalue->MACHINEID_10 : "";
                    $objSEMAT[$mindex]->MACH_10_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_10_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_10) && $mvalue->SHIFTID_10!=0 ) ? $mvalue->SHIFTID_10 : "";
                    $objSEMAT[$mindex]->SHFT_10_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_10_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_11
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_11) && $mvalue->MACHINEID_11!=0 ) ? $mvalue->MACHINEID_11 : "";
                    $objSEMAT[$mindex]->MACH_11_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_11_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_11) && $mvalue->SHIFTID_11!=0 ) ? $mvalue->SHIFTID_11 : "";
                    $objSEMAT[$mindex]->SHFT_11_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_11_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_12
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_12) && $mvalue->MACHINEID_12!=0 ) ? $mvalue->MACHINEID_12 : "";
                    $objSEMAT[$mindex]->MACH_12_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_12_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_12) && $mvalue->SHIFTID_12!=0 ) ? $mvalue->SHIFTID_12 : "";
                    $objSEMAT[$mindex]->SHFT_12_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_12_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_13
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_13) && $mvalue->MACHINEID_13!=0 ) ? $mvalue->MACHINEID_13 : "";
                    $objSEMAT[$mindex]->MACH_13_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_13_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_13) && $mvalue->SHIFTID_13!=0 ) ? $mvalue->SHIFTID_13 : "";
                    $objSEMAT[$mindex]->SHFT_13_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_13_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_14
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_14) && $mvalue->MACHINEID_14!=0 ) ? $mvalue->MACHINEID_14 : "";
                    $objSEMAT[$mindex]->MACH_14_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_14_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_14) && $mvalue->SHIFTID_14!=0 ) ? $mvalue->SHIFTID_14 : "";
                    $objSEMAT[$mindex]->SHFT_14_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_14_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_15
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_15) && $mvalue->MACHINEID_15!=0 ) ? $mvalue->MACHINEID_15 : "";
                    $objSEMAT[$mindex]->MACH_15_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_15_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_15) && $mvalue->SHIFTID_15!=0 ) ? $mvalue->SHIFTID_15 : "";
                    $objSEMAT[$mindex]->SHFT_15_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_15_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_16
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_16) && $mvalue->MACHINEID_16!=0 ) ? $mvalue->MACHINEID_16 : "";
                    $objSEMAT[$mindex]->MACH_16_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_16_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_16) && $mvalue->SHIFTID_16!=0 ) ? $mvalue->SHIFTID_16 : "";
                    $objSEMAT[$mindex]->SHFT_16_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_16_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_17
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_17) && $mvalue->MACHINEID_17!=0 ) ? $mvalue->MACHINEID_17 : "";
                    $objSEMAT[$mindex]->MACH_17_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_17_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_17) && $mvalue->SHIFTID_17!=0 ) ? $mvalue->SHIFTID_17 : "";
                    $objSEMAT[$mindex]->SHFT_17_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_17_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_18
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_18) && $mvalue->MACHINEID_18!=0 ) ? $mvalue->MACHINEID_18 : "";
                    $objSEMAT[$mindex]->MACH_18_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_18_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_18) && $mvalue->SHIFTID_18!=0 ) ? $mvalue->SHIFTID_18 : "";
                    $objSEMAT[$mindex]->SHFT_18_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_18_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_19
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_19) && $mvalue->MACHINEID_19!=0 ) ? $mvalue->MACHINEID_19 : "";
                    $objSEMAT[$mindex]->MACH_19_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_19_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_19) && $mvalue->SHIFTID_19!=0 ) ? $mvalue->SHIFTID_19 : "";
                    $objSEMAT[$mindex]->SHFT_19_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_19_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_20
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_20) && $mvalue->MACHINEID_20!=0 ) ? $mvalue->MACHINEID_20 : "";
                    $objSEMAT[$mindex]->MACH_20_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_20_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_20) && $mvalue->SHIFTID_20!=0 ) ? $mvalue->SHIFTID_20 : "";
                    $objSEMAT[$mindex]->SHFT_20_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_20_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_21
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_21) && $mvalue->MACHINEID_21!=0 ) ? $mvalue->MACHINEID_21 : "";
                    $objSEMAT[$mindex]->MACH_21_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_21_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_21) && $mvalue->SHIFTID_21!=0 ) ? $mvalue->SHIFTID_21 : "";
                    $objSEMAT[$mindex]->SHFT_21_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_21_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_22
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_22) && $mvalue->MACHINEID_22!=0 ) ? $mvalue->MACHINEID_22 : "";
                    $objSEMAT[$mindex]->MACH_22_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_22_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_22) && $mvalue->SHIFTID_22!=0 ) ? $mvalue->SHIFTID_22 : "";
                    $objSEMAT[$mindex]->SHFT_22_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_22_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_23
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_23) && $mvalue->MACHINEID_23!=0 ) ? $mvalue->MACHINEID_23 : "";
                    $objSEMAT[$mindex]->MACH_23_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_23_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_23) && $mvalue->SHIFTID_23!=0 ) ? $mvalue->SHIFTID_23 : "";
                    $objSEMAT[$mindex]->SHFT_23_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_23_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_24
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_24) && $mvalue->MACHINEID_24!=0 ) ? $mvalue->MACHINEID_24 : "";
                    $objSEMAT[$mindex]->MACH_24_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_24_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_24) && $mvalue->SHIFTID_24!=0 ) ? $mvalue->SHIFTID_24 : "";
                    $objSEMAT[$mindex]->SHFT_24_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_24_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_25
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_25) && $mvalue->MACHINEID_25!=0 ) ? $mvalue->MACHINEID_25 : "";
                    $objSEMAT[$mindex]->MACH_25_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_25_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_25) && $mvalue->SHIFTID_25!=0 ) ? $mvalue->SHIFTID_25 : "";
                    $objSEMAT[$mindex]->SHFT_25_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_25_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_26
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_26) && $mvalue->MACHINEID_26!=0 ) ? $mvalue->MACHINEID_26 : "";
                    $objSEMAT[$mindex]->MACH_26_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_26_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_26) && $mvalue->SHIFTID_26!=0 ) ? $mvalue->SHIFTID_26 : "";
                    $objSEMAT[$mindex]->SHFT_26_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_26_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_27
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_27) && $mvalue->MACHINEID_27!=0 ) ? $mvalue->MACHINEID_27 : "";
                    $objSEMAT[$mindex]->MACH_27_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_27_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_27) && $mvalue->SHIFTID_27!=0 ) ? $mvalue->SHIFTID_27 : "";
                    $objSEMAT[$mindex]->SHFT_27_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_27_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_28
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_28) && $mvalue->MACHINEID_28!=0 ) ? $mvalue->MACHINEID_28 : "";
                    $objSEMAT[$mindex]->MACH_28_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_28_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_28) && $mvalue->SHIFTID_28!=0 ) ? $mvalue->SHIFTID_28 : "";
                    $objSEMAT[$mindex]->SHFT_28_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_28_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_29
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_29) && $mvalue->MACHINEID_29!=0 ) ? $mvalue->MACHINEID_29 : "";
                    $objSEMAT[$mindex]->MACH_29_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_29_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_29) && $mvalue->SHIFTID_29!=0 ) ? $mvalue->SHIFTID_29 : "";
                    $objSEMAT[$mindex]->SHFT_29_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_29_DESC = $this->ShiftCode($shft_id);


                    //--------------------day_30
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_30) && $mvalue->MACHINEID_30!=0 ) ? $mvalue->MACHINEID_30 : "";
                    $objSEMAT[$mindex]->MACH_30_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_30_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_30) && $mvalue->SHIFTID_30!=0 ) ? $mvalue->SHIFTID_30 : "";
                    $objSEMAT[$mindex]->SHFT_30_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_30_DESC = $this->ShiftCode($shft_id);

                    //--------------------day_31
                    $mach_id="";
                    $mach_id = ( !is_null($mvalue->MACHINEID_31) && $mvalue->MACHINEID_31!=0 ) ? $mvalue->MACHINEID_31 : "";
                    $objSEMAT[$mindex]->MACH_31_ID = $mach_id;
                    $objSEMAT[$mindex]->MACH_31_DESC = $this->MachineCode($mach_id);
                    //--------SHIFT
                    $shft_id="";
                    $shft_id = ( !is_null($mvalue->SHIFTID_31) && $mvalue->SHIFTID_31!=0 ) ? $mvalue->SHIFTID_31 : "";
                    $objSEMAT[$mindex]->SHFT_31_ID = $shft_id;
                    $objSEMAT[$mindex]->SHFT_31_DESC = $this->ShiftCode($shft_id);


                    //------------------------------
                } // foreach end

                $tempObj="";
        
             //   DD($objSEMAT); 
            //----------------------------------------
     
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
            
        
  
            
            $objItems = DB::table('TBL_MST_ITEM')->select('*')
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('FYID_REF','=',$FYID_REF)
            ->get() ->toArray(); 
      
   
           $cur_date = Date('Y-m-d');
           $objItemCategoryList = DB::select('select * from TBL_MST_ITEMCATEGORY  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?) and CYID_REF = ?   and STATUS = ? ',  [$cur_date,$CYID_REF,'A']);
               
   
           $objMonths = DB::select('select * from TBL_MST_MONTH  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?)  and STATUS = ? ', [$cur_date,'A']);
           

           $objlast_DT = DB::select('SELECT MAX(MPP_DOC_DT) MPP_DOC_DT FROM TBL_TRN_PDMPP_HDR  
           WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND VTID_REF = ? AND STATUS = ?', 
           [$CYID_REF, $BRID_REF, $FYID_REF, $this->vtid_ref, 'N' ]);


            return view('transactions.Production.ManualProductionPlan.trnfrm229view',compact(['objSE','objRights','objCount1', 'objSEMAT','objItems','objlast_DT','objItemCategoryList','objMonths','objlast_DT']));
        }
        
    }

    public function saveamendment(Request $request){
     
        $r_count1 = $request['Row_Count1'];

        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]))
            {
                $req_data[$i] = [
                 'BUSINESSUNIT'        => $request['REF_BUID_'.$i],
                 'ITEMCODE'        => $request['ITEMID_REF_'.$i],
                 'CUSTOMER'        => !is_null($request['CID_REF_'.$i])? $request['CID_REF_'.$i] : '',
                 'PARTNO'        => !is_null($request['ItemPartno_'.$i])? $request['ItemPartno_'.$i] : '',
                 'UOM'        => $request['itemuom_'.$i],     
                 'ITEMSPECIFICATION'         => $request['Itemspec_'.$i],

                 'MONTH1OPENING' => (($request['MONTH1_OP_'.$i])=="" ? 0 : $request['MONTH1_OP_'.$i]),
                 'MONTH1SALES' => (($request['MONTH1_SL_'.$i])=="" ? 0 : $request['MONTH1_SL_'.$i]),
                 'MONTH1PURCHASE' => (($request['MONTH1_PR_'.$i])=="" ? 0 : $request['MONTH1_PR_'.$i]),
                 'MONTH1ADDPURCHASE' => (($request['MONTH1_AP_'.$i])=="" ? 0 : $request['MONTH1_AP_'.$i]),
                 'MONTH1TOBEPROCURED' => (($request['MONTH1_TP_'.$i])=="" ? 0 : $request['MONTH1_TP_'.$i]),
                 'MONTH1INVENTORY' => (($request['MONTH1_IV_'.$i])=="" ? 0 : $request['MONTH1_IV_'.$i]),
                 'MONTH1NUMBEROFINVENTORYDAY' => (($request['MONTH1_ND_'.$i])=="" ? 0 : $request['MONTH1_ND_'.$i]),
                 'MONTH1RATE' => (($request['MONTH1_RT_'.$i])=="" ? 0 : $request['MONTH1_RT_'.$i]),
                 'MONTH1PURCHASEVALUE' => (($request['MONTH1_PV_'.$i])=="" ? 0 : $request['MONTH1_PV_'.$i]),

                 'MONTH2OPENING' => (($request['MONTH2_OP_'.$i])=="" ? 0 : $request['MONTH2_OP_'.$i]),
                 'MONTH2SALES' => (($request['MONTH2_SL_'.$i])=="" ? 0 : $request['MONTH2_SL_'.$i]),
                 'MONTH2PURCHASE' => (($request['MONTH2_PR_'.$i])=="" ? 0 : $request['MONTH2_PR_'.$i]),
                 'MONTH2ADDPURCHASE' => (($request['MONTH2_AP_'.$i])=="" ? 0 : $request['MONTH2_AP_'.$i]),
                 'MONTH2TOBEPROCURED' => (($request['MONTH2_TP_'.$i])=="" ? 0 : $request['MONTH2_TP_'.$i]),
                 'MONTH2INVENTORY' => (($request['MONTH2_IV_'.$i])=="" ? 0 : $request['MONTH2_IV_'.$i]),
                 'MONTH2NUMBEROFINVENTORYDAY' => (($request['MONTH2_ND_'.$i])=="" ? 0 : $request['MONTH2_ND_'.$i]),
                 'MONTH2RATE' => (($request['MONTH2_RT_'.$i])=="" ? 0 : $request['MONTH2_RT_'.$i]),
                 'MONTH2PURCHASEVALUE' => (($request['MONTH2_PV_'.$i])=="" ? 0 : $request['MONTH2_PV_'.$i]),

                 'MONTH3OPENING' => (($request['MONTH3_OP_'.$i])=="" ? 0 : $request['MONTH3_OP_'.$i]),
                 'MONTH3SALES' => (($request['MONTH3_SL_'.$i])=="" ? 0 : $request['MONTH3_SL_'.$i]),
                 'MONTH3PURCHASE' => (($request['MONTH3_PR_'.$i])=="" ? 0 : $request['MONTH3_PR_'.$i]),
                 'MONTH3ADDPURCHASE' => (($request['MONTH3_AP_'.$i])=="" ? 0 : $request['MONTH3_AP_'.$i]),
                 'MONTH3TOBEPROCURED' => (($request['MONTH3_TP_'.$i])=="" ? 0 : $request['MONTH3_TP_'.$i]),
                 'MONTH3INVENTORY' => (($request['MONTH3_IV_'.$i])=="" ? 0 : $request['MONTH3_IV_'.$i]),
                 'MONTH3NUMBEROFINVENTORYDAY' => (($request['MONTH3_ND_'.$i])=="" ? 0 : $request['MONTH3_ND_'.$i]),
                 'MONTH3RATE' => (($request['MONTH3_RT_'.$i])=="" ? 0 : $request['MONTH3_RT_'.$i]),
                 'MONTH3PURCHASEVALUE' => (($request['MONTH3_PV_'.$i])=="" ? 0 : $request['MONTH3_PV_'.$i]),

                 'MONTH4OPENING' => (($request['MONTH4_OP_'.$i])=="" ? 0 : $request['MONTH4_OP_'.$i]),
                 'MONTH4SALES' => (($request['MONTH4_SL_'.$i])=="" ? 0 : $request['MONTH4_SL_'.$i]),
                 'MONTH4PURCHASE' => (($request['MONTH4_PR_'.$i])=="" ? 0 : $request['MONTH4_PR_'.$i]),
                 'MONTH4ADDPURCHASE' => (($request['MONTH4_AP_'.$i])=="" ? 0 : $request['MONTH4_AP_'.$i]),
                 'MONTH4TOBEPROCURED' => (($request['MONTH4_TP_'.$i])=="" ? 0 : $request['MONTH4_TP_'.$i]),
                 'MONTH4INVENTORY' => (($request['MONTH4_IV_'.$i])=="" ? 0 : $request['MONTH4_IV_'.$i]),
                 'MONTH4NUMBEROFINVENTORYDAY' => (($request['MONTH4_ND_'.$i])=="" ? 0 : $request['MONTH4_ND_'.$i]),
                 'MONTH4RATE' => (($request['MONTH4_RT_'.$i])=="" ? 0 : $request['MONTH4_RT_'.$i]),
                 'MONTH4PURCHASEVALUE' => (($request['MONTH4_PV_'.$i])=="" ? 0 : $request['MONTH4_PV_'.$i]),

                 'MONTH5OPENING' => (($request['MONTH5_OP_'.$i])=="" ? 0 : $request['MONTH5_OP_'.$i]),
                 'MONTH5SALES' => (($request['MONTH5_SL_'.$i])=="" ? 0 : $request['MONTH5_SL_'.$i]),
                 'MONTH5PURCHASE' => (($request['MONTH5_PR_'.$i])=="" ? 0 : $request['MONTH5_PR_'.$i]),
                 'MONTH5ADDPURCHASE' => (($request['MONTH5_AP_'.$i])=="" ? 0 : $request['MONTH5_AP_'.$i]),
                 'MONTH5TOBEPROCURED' => (($request['MONTH5_TP_'.$i])=="" ? 0 : $request['MONTH5_TP_'.$i]),
                 'MONTH5INVENTORY' => (($request['MONTH5_IV_'.$i])=="" ? 0 : $request['MONTH5_IV_'.$i]),
                 'MONTH5NUMBEROFINVENTORYDAY' => (($request['MONTH5_ND_'.$i])=="" ? 0 : $request['MONTH5_ND_'.$i]),
                 'MONTH5RATE' => (($request['MONTH5_RT_'.$i])=="" ? 0 : $request['MONTH5_RT_'.$i]),
                 'MONTH5PURCHASEVALUE' => (($request['MONTH5_PV_'.$i])=="" ? 0 : $request['MONTH5_PV_'.$i]),

                 'MONTH6OPENING' => (($request['MONTH6_OP_'.$i])=="" ? 0 : $request['MONTH6_OP_'.$i]),
                 'MONTH6SALES' => (($request['MONTH6_SL_'.$i])=="" ? 0 : $request['MONTH6_SL_'.$i]),
                 'MONTH6PURCHASE' => (($request['MONTH6_PR_'.$i])=="" ? 0 : $request['MONTH6_PR_'.$i]),
                 'MONTH6ADDPURCHASE' => (($request['MONTH6_AP_'.$i])=="" ? 0 : $request['MONTH6_AP_'.$i]),
                 'MONTH6TOBEPROCURED' => (($request['MONTH6_TP_'.$i])=="" ? 0 : $request['MONTH6_TP_'.$i]),
                 'MONTH6INVENTORY' => (($request['MONTH6_IV_'.$i])=="" ? 0 : $request['MONTH6_IV_'.$i]),
                 'MONTH6NUMBEROFINVENTORYDAY' => (($request['MONTH6_ND_'.$i])=="" ? 0 : $request['MONTH6_ND_'.$i]),
                 'MONTH6RATE' => (($request['MONTH6_RT_'.$i])=="" ? 0 : $request['MONTH6_RT_'.$i]),
                 'MONTH6PURCHASEVALUE' => (($request['MONTH6_PV_'.$i])=="" ? 0 : $request['MONTH6_PV_'.$i]),

                 'MONTH7OPENING' => (($request['MONTH7_OP_'.$i])=="" ? 0 : $request['MONTH7_OP_'.$i]),
                 'MONTH7SALES' => (($request['MONTH7_SL_'.$i])=="" ? 0 : $request['MONTH7_SL_'.$i]),
                 'MONTH7PURCHASE' => (($request['MONTH7_PR_'.$i])=="" ? 0 : $request['MONTH7_PR_'.$i]),
                 'MONTH7ADDPURCHASE' => (($request['MONTH7_AP_'.$i])=="" ? 0 : $request['MONTH7_AP_'.$i]),
                 'MONTH7TOBEPROCURED' => (($request['MONTH7_TP_'.$i])=="" ? 0 : $request['MONTH7_TP_'.$i]),
                 'MONTH7INVENTORY' => (($request['MONTH7_IV_'.$i])=="" ? 0 : $request['MONTH7_IV_'.$i]),
                 'MONTH7NUMBEROFINVENTORYDAY' => (($request['MONTH7_ND_'.$i])=="" ? 0 : $request['MONTH7_ND_'.$i]),
                 'MONTH7RATE' => (($request['MONTH7_RT_'.$i])=="" ? 0 : $request['MONTH7_RT_'.$i]),
                 'MONTH7PURCHASEVALUE' => (($request['MONTH7_PV_'.$i])=="" ? 0 : $request['MONTH7_PV_'.$i]),

                 'MONTH8OPENING' => (($request['MONTH8_OP_'.$i])=="" ? 0 : $request['MONTH8_OP_'.$i]),
                 'MONTH8SALES' => (($request['MONTH8_SL_'.$i])=="" ? 0 : $request['MONTH8_SL_'.$i]),
                 'MONTH8PURCHASE' => (($request['MONTH8_PR_'.$i])=="" ? 0 : $request['MONTH8_PR_'.$i]),
                 'MONTH8ADDPURCHASE' => (($request['MONTH8_AP_'.$i])=="" ? 0 : $request['MONTH8_AP_'.$i]),
                 'MONTH8TOBEPROCURED' => (($request['MONTH8_TP_'.$i])=="" ? 0 : $request['MONTH8_TP_'.$i]),
                 'MONTH8INVENTORY' => (($request['MONTH8_IV_'.$i])=="" ? 0 : $request['MONTH8_IV_'.$i]),
                 'MONTH8NUMBEROFINVENTORYDAY' => (($request['MONTH8_ND_'.$i])=="" ? 0 : $request['MONTH8_ND_'.$i]),
                 'MONTH8RATE' => (($request['MONTH8_RT_'.$i])=="" ? 0 : $request['MONTH8_RT_'.$i]),
                 'MONTH8PURCHASEVALUE' => (($request['MONTH8_PV_'.$i])=="" ? 0 : $request['MONTH8_PV_'.$i]),

                 'MONTH9OPENING' => (($request['MONTH9_OP_'.$i])=="" ? 0 : $request['MONTH9_OP_'.$i]),
                 'MONTH9SALES' => (($request['MONTH9_SL_'.$i])=="" ? 0 : $request['MONTH9_SL_'.$i]),
                 'MONTH9PURCHASE' => (($request['MONTH9_PR_'.$i])=="" ? 0 : $request['MONTH9_PR_'.$i]),
                 'MONTH9ADDPURCHASE' => (($request['MONTH9_AP_'.$i])=="" ? 0 : $request['MONTH9_AP_'.$i]),
                 'MONTH9TOBEPROCURED' => (($request['MONTH9_TP_'.$i])=="" ? 0 : $request['MONTH9_TP_'.$i]),
                 'MONTH9INVENTORY' => (($request['MONTH9_IV_'.$i])=="" ? 0 : $request['MONTH9_IV_'.$i]),
                 'MONTH9NUMBEROFINVENTORYDAY' => (($request['MONTH9_ND_'.$i])=="" ? 0 : $request['MONTH9_ND_'.$i]),
                 'MONTH9RATE' => (($request['MONTH9_RT_'.$i])=="" ? 0 : $request['MONTH9_RT_'.$i]),
                 'MONTH9PURCHASEVALUE' => (($request['MONTH9_PV_'.$i])=="" ? 0 : $request['MONTH9_PV_'.$i]),

                 'MONTH10OPENING' => (($request['MONTH10_OP_'.$i])=="" ? 0 : $request['MONTH10_OP_'.$i]),
                 'MONTH10SALES' => (($request['MONTH10_SL_'.$i])=="" ? 0 : $request['MONTH10_SL_'.$i]),
                 'MONTH10PURCHASE' => (($request['MONTH10_PR_'.$i])=="" ? 0 : $request['MONTH10_PR_'.$i]),
                 'MONTH10ADDPURCHASE' => (($request['MONTH10_AP_'.$i])=="" ? 0 : $request['MONTH10_AP_'.$i]),
                 'MONTH10TOBEPROCURED' => (($request['MONTH10_TP_'.$i])=="" ? 0 : $request['MONTH10_TP_'.$i]),
                 'MONTH10INVENTORY' => (($request['MONTH10_IV_'.$i])=="" ? 0 : $request['MONTH10_IV_'.$i]),
                 'MONTH10NUMBEROFINVENTORYDAY' => (($request['MONTH10_ND_'.$i])=="" ? 0 : $request['MONTH10_ND_'.$i]),
                 'MONTH10RATE' => (($request['MONTH10_RT_'.$i])=="" ? 0 : $request['MONTH10_RT_'.$i]),
                 'MONTH10PURCHASEVALUE' => (($request['MONTH10_PV_'.$i])=="" ? 0 : $request['MONTH10_PV_'.$i]),

                 'MONTH11OPENING' => (($request['MONTH11_OP_'.$i])=="" ? 0 : $request['MONTH11_OP_'.$i]),
                 'MONTH11SALES' => (($request['MONTH11_SL_'.$i])=="" ? 0 : $request['MONTH11_SL_'.$i]),
                 'MONTH11PURCHASE' => (($request['MONTH11_PR_'.$i])=="" ? 0 : $request['MONTH11_PR_'.$i]),
                 'MONTH11ADDPURCHASE' => (($request['MONTH11_AP_'.$i])=="" ? 0 : $request['MONTH11_AP_'.$i]),
                 'MONTH11TOBEPROCURED' => (($request['MONTH11_TP_'.$i])=="" ? 0 : $request['MONTH11_TP_'.$i]),
                 'MONTH11INVENTORY' => (($request['MONTH11_IV_'.$i])=="" ? 0 : $request['MONTH11_IV_'.$i]),
                 'MONTH11NUMBEROFINVENTORYDAY' => (($request['MONTH11_ND_'.$i])=="" ? 0 : $request['MONTH11_ND_'.$i]),
                 'MONTH11RATE' => (($request['MONTH11_RT_'.$i])=="" ? 0 : $request['MONTH11_RT_'.$i]),
                 'MONTH11PURCHASEVALUE' => (($request['MONTH11_PV_'.$i])=="" ? 0 : $request['MONTH11_PV_'.$i]),

                 'MONTH12OPENING' => (($request['MONTH12_OP_'.$i])=="" ? 0 : $request['MONTH12_OP_'.$i]),
                 'MONTH12SALES' => (($request['MONTH12_SL_'.$i])=="" ? 0 : $request['MONTH12_SL_'.$i]),
                 'MONTH12PURCHASE' => (($request['MONTH12_PR_'.$i])=="" ? 0 : $request['MONTH12_PR_'.$i]),
                 'MONTH12ADDPURCHASE' => (($request['MONTH12_AP_'.$i])=="" ? 0 : $request['MONTH12_AP_'.$i]),
                 'MONTH12TOBEPROCURED' => (($request['MONTH12_TP_'.$i])=="" ? 0 : $request['MONTH12_TP_'.$i]),
                 'MONTH12INVENTORY' => (($request['MONTH12_IV_'.$i])=="" ? 0 : $request['MONTH12_IV_'.$i]),
                 'MONTH12NUMBEROFINVENTORYDAY' => (($request['MONTH12_ND_'.$i])=="" ? 0 : $request['MONTH12_ND_'.$i]),
                 'MONTH12RATE' => (($request['MONTH12_RT_'.$i])=="" ? 0 : $request['MONTH12_RT_'.$i]),
                 'MONTH12PURCHASEVALUE' => (($request['MONTH12_PV_'.$i])=="" ? 0 : $request['MONTH12_PV_'.$i]),
                    
                ];
            }
        }
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        
            $AFPID_REF=$request['AFSID_REF'];
            $AFPNO=$request['AFSNO'];

            $AFPADT=$request['AFSADT'];
            $REASON_AFSA=$request['REASON_AFSA'];
        

           $VTID_REF     =   173;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'EDIT';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');

            $AFSDT = $request['AFSDT'];
            $DEPID_REF = $request['DEPID_REF'];
            $FYID_REFS = $request['FYID_REF'];
           
            //dump($request->all());
            // $log_data = [ 
            //     $AFPID_REF, $AFPANO, $AFPADT, $DEPID_REF, $FYID_REFS, $REASON_AFSA, $CYID_REF, $BRID_REF, $VTID_REF, $FYID_REF,
            //     $XMLMAT , $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            // ];

            $log_data = [ 
                $AFPID_REF, $AFPNO, $AFPADT, $DEPID_REF, $REASON_AFSA, $FYID_REFS, $CYID_REF, $BRID_REF, $VTID_REF, $FYID_REF,
                $XMLMAT , $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];

            //dump( $log_data);
            // @AFPID INT,@AFP_NO VARCHAR(20),@AFPA_DT DATE,@DEPID_REF INT,@REASON VARCHAR(200),@FYID_REF INT,@CYID_REF INT,@BRID_REF INT,@FYID_REF1 INT,@VTID_REF INT,@XMLMAT XML  ,              
            // @USERID_REF INT,@UPDATE date,@UPTIME time,@ACTION varchar(30),@IPADDRESS varchar(30) 
            
            $sp_result = DB::select('EXEC SP_AFP_ABN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?', $log_data);    
           
           //dd($sp_result );
    
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
        }
        exit();   
    }



public function update(Request $request){
        
    
    $r_count1 = $request['Row_Count1'];  
    
   // dump($request->all());
          
            

    for ($i=0; $i<=$r_count1; $i++)
    {
        if(isset($request['ITEMID_REF_'.$i]))
        {
            $req_data[$i] = [
                
            'ICATID_REF'        => $request['REF_BUID_'.$i],  //item category
            'ITEMIDID_REF'        => $request['ITEMID_REF_'.$i],
            'UOMID_REF'        => $request['itemuom_'.$i],     
            'PD_PLAN_QTY'         => (!is_null($request['ppqty_'.$i]) && !empty($request['ppqty_'.$i]) )? $request['ppqty_'.$i] : 0,
                
            'MACHINEID_1'=> ( !is_null($request['machine1_MACH_REFID_'.$i]) && !empty($request['machine1_MACH_REFID_'.$i]) ) ? $request['machine1_MACH_REFID_'.$i] : null,
            'SHIFTID_1'  => ( !is_null($request['shift1_SHIFT_REFID_'.$i])  && !empty($request['shift1_SHIFT_REFID_'.$i]) ) ? $request['shift1_SHIFT_REFID_'.$i] : NULL,
            'QTY_1' =>      ( !is_null($request['dayqty1_'.$i]) && !empty($request['dayqty1_'.$i]) )? $request['dayqty1_'.$i] : 0,
            
            'MACHINEID_2'=> (!is_null($request['machine2_MACH_REFID_'.$i]) && !empty($request['machine2_MACH_REFID_'.$i]) ) ? $request['machine2_MACH_REFID_'.$i] : NULL,
            'SHIFTID_2'  => (!is_null($request['shift2_SHIFT_REFID_'.$i])  && !empty($request['shift2_SHIFT_REFID_'.$i]) )  ? $request['shift2_SHIFT_REFID_'.$i] : NULL,
            'QTY_2' =>      (!is_null($request['dayqty2_'.$i])   && !empty($request['dayqty2_'.$i]) ) ? $request['dayqty2_'.$i] : 0,

            'MACHINEID_3'=> (!is_null($request['machine3_MACH_REFID_'.$i]) && !empty($request['machine3_MACH_REFID_'.$i]) ) ? $request['machine3_MACH_REFID_'.$i] : NULL,
            'SHIFTID_3'  => (!is_null($request['shift3_SHIFT_REFID_'.$i])  && !empty($request['shift3_SHIFT_REFID_'.$i]) )  ? $request['shift3_SHIFT_REFID_'.$i] : NULL,
            'QTY_3' =>      (!is_null($request['dayqty3_'.$i])   && !empty($request['dayqty3_'.$i]) ) ? $request['dayqty3_'.$i] : 0,

            'MACHINEID_4'=> (!is_null($request['machine4_MACH_REFID_'.$i]) && !empty($request['machine4_MACH_REFID_'.$i]) ) ? $request['machine4_MACH_REFID_'.$i] : NULL,
            'SHIFTID_4'  => (!is_null($request['shift4_SHIFT_REFID_'.$i])  && !empty($request['shift4_SHIFT_REFID_'.$i]) )  ? $request['shift4_SHIFT_REFID_'.$i] : NULL,
            'QTY_4' =>      (!is_null($request['dayqty4_'.$i])   && !empty($request['dayqty4_'.$i]) ) ? $request['dayqty4_'.$i] : 0,
            
            'MACHINEID_5'=> (!is_null($request['machine5_MACH_REFID_'.$i]) && !empty($request['machine5_MACH_REFID_'.$i]) ) ? $request['machine5_MACH_REFID_'.$i] : NULL,
            'SHIFTID_5'  => (!is_null($request['shift5_SHIFT_REFID_'.$i])  && !empty($request['shift5_SHIFT_REFID_'.$i]) )  ? $request['shift5_SHIFT_REFID_'.$i] : NULL,
            'QTY_5' =>      (!is_null($request['dayqty5_'.$i])   && !empty($request['dayqty5_'.$i]) ) ? $request['dayqty5_'.$i] : 0,

            
            'MACHINEID_6'=> (!is_null($request['machine6_MACH_REFID_'.$i]) && !empty($request['machine6_MACH_REFID_'.$i]) ) ? $request['machine6_MACH_REFID_'.$i] : NULL,
            'SHIFTID_6'  => (!is_null($request['shift6_SHIFT_REFID_'.$i])  && !empty($request['shift6_SHIFT_REFID_'.$i]) )  ? $request['shift6_SHIFT_REFID_'.$i] : NULL,
            'QTY_6' =>      (!is_null($request['dayqty6_'.$i])   && !empty($request['dayqty6_'.$i]) ) ? $request['dayqty6_'.$i] : 0,

            'MACHINEID_7'=> (!is_null($request['machine7_MACH_REFID_'.$i]) && !empty($request['machine7_MACH_REFID_'.$i]) ) ? $request['machine7_MACH_REFID_'.$i] : NULL,
            'SHIFTID_7'  => (!is_null($request['shift7_SHIFT_REFID_'.$i])  && !empty($request['shift7_SHIFT_REFID_'.$i]) )  ? $request['shift7_SHIFT_REFID_'.$i] : NULL,
            'QTY_7' =>      (!is_null($request['dayqty7_'.$i])   && !empty($request['dayqty7_'.$i]) ) ? $request['dayqty7_'.$i] : 0,

            'MACHINEID_8'=> (!is_null($request['machine8_MACH_REFID_'.$i]) && !empty($request['machine8_MACH_REFID_'.$i]) ) ? $request['machine8_MACH_REFID_'.$i] : NULL,
            'SHIFTID_8'  => (!is_null($request['shift8_SHIFT_REFID_'.$i])  && !empty($request['shift8_SHIFT_REFID_'.$i]) )  ? $request['shift8_SHIFT_REFID_'.$i] : NULL,
            'QTY_8' =>      (!is_null($request['dayqty8_'.$i])   && !empty($request['dayqty8_'.$i]) ) ? $request['dayqty8_'.$i] : 0,

            
            'MACHINEID_9'=> (!is_null($request['machine9_MACH_REFID_'.$i]) && !empty($request['machine9_MACH_REFID_'.$i]) ) ? $request['machine9_MACH_REFID_'.$i] : NULL,
            'SHIFTID_9'  => (!is_null($request['shift9_SHIFT_REFID_'.$i])  && !empty($request['shift9_SHIFT_REFID_'.$i]) )  ? $request['shift9_SHIFT_REFID_'.$i] : NULL,
            'QTY_9' =>      (!is_null($request['dayqty9_'.$i])   && !empty($request['dayqty9_'.$i]) ) ? $request['dayqty9_'.$i] : 0,

            'MACHINEID_10'=> (!is_null($request['machine10_MACH_REFID_'.$i]) && !empty($request['machine10_MACH_REFID_'.$i]) ) ? $request['machine10_MACH_REFID_'.$i] : NULL,
            'SHIFTID_10'  => (!is_null($request['shift10_SHIFT_REFID_'.$i])  && !empty($request['shift10_SHIFT_REFID_'.$i]) )  ? $request['shift10_SHIFT_REFID_'.$i] : NULL,
            'QTY_10' =>      (!is_null($request['dayqty10_'.$i])   && !empty($request['dayqty10_'.$i]) ) ? $request['dayqty10_'.$i] : 0,

            'MACHINEID_11'=> (!is_null($request['machine11_MACH_REFID_'.$i]) && !empty($request['machine11_MACH_REFID_'.$i]) ) ? $request['machine11_MACH_REFID_'.$i] : NULL,
            'SHIFTID_11'  => (!is_null($request['shift11_SHIFT_REFID_'.$i])  && !empty($request['shift11_SHIFT_REFID_'.$i]) )  ? $request['shift11_SHIFT_REFID_'.$i] : NULL,
            'QTY_11' =>      (!is_null($request['dayqty11_'.$i])   && !empty($request['dayqty11_'.$i]) ) ? $request['dayqty11_'.$i] : 0,

            'MACHINEID_12'=> (!is_null($request['machine12_MACH_REFID_'.$i]) && !empty($request['machine12_MACH_REFID_'.$i]) ) ? $request['machine12_MACH_REFID_'.$i] : NULL,
            'SHIFTID_12'  => (!is_null($request['shift12_SHIFT_REFID_'.$i])  && !empty($request['shift12_SHIFT_REFID_'.$i]) )  ? $request['shift12_SHIFT_REFID_'.$i] : NULL,
            'QTY_12' =>      (!is_null($request['dayqty12_'.$i])   && !empty($request['dayqty12_'.$i]) ) ? $request['dayqty12_'.$i] : 0,

            'MACHINEID_13'=> (!is_null($request['machine13_MACH_REFID_'.$i]) && !empty($request['machine13_MACH_REFID_'.$i]) ) ? $request['machine13_MACH_REFID_'.$i] : NULL,
            'SHIFTID_13'  => (!is_null($request['shift13_SHIFT_REFID_'.$i])  && !empty($request['shift13_SHIFT_REFID_'.$i]) )  ? $request['shift13_SHIFT_REFID_'.$i] : NULL,
            'QTY_13' =>      (!is_null($request['dayqty13_'.$i])   && !empty($request['dayqty13_'.$i]) ) ? $request['dayqty13_'.$i] : 0,

            'MACHINEID_14'=> (!is_null($request['machine14_MACH_REFID_'.$i]) && !empty($request['machine14_MACH_REFID_'.$i]) ) ? $request['machine14_MACH_REFID_'.$i] : NULL,
            'SHIFTID_14'  => (!is_null($request['shift14_SHIFT_REFID_'.$i])  && !empty($request['shift14_SHIFT_REFID_'.$i]) )  ? $request['shift14_SHIFT_REFID_'.$i] : NULL,
            'QTY_14' =>      (!is_null($request['dayqty14_'.$i])   && !empty($request['dayqty14_'.$i]) ) ? $request['dayqty14_'.$i] : 0,

            'MACHINEID_15'=> (!is_null($request['machine15_MACH_REFID_'.$i]) && !empty($request['machine15_MACH_REFID_'.$i]) ) ? $request['machine15_MACH_REFID_'.$i] : NULL,
            'SHIFTID_15'  => (!is_null($request['shift15_SHIFT_REFID_'.$i])  && !empty($request['shift15_SHIFT_REFID_'.$i]) )  ? $request['shift15_SHIFT_REFID_'.$i] : NULL,
            'QTY_15' =>      (!is_null($request['dayqty15_'.$i])   && !empty($request['dayqty15_'.$i]) ) ? $request['dayqty15_'.$i] : 0,

            'MACHINEID_16'=> (!is_null($request['machine16_MACH_REFID_'.$i]) && !empty($request['machine16_MACH_REFID_'.$i]) ) ? $request['machine16_MACH_REFID_'.$i] : NULL,
            'SHIFTID_16'  => (!is_null($request['shift16_SHIFT_REFID_'.$i])  && !empty($request['shift16_SHIFT_REFID_'.$i]) )  ? $request['shift16_SHIFT_REFID_'.$i] : NULL,
            'QTY_16' =>      (!is_null($request['dayqty16_'.$i])   && !empty($request['dayqty16_'.$i]) ) ? $request['dayqty16_'.$i] : 0,

            'MACHINEID_17'=> (!is_null($request['machine17_MACH_REFID_'.$i]) && !empty($request['machine17_MACH_REFID_'.$i]) ) ? $request['machine17_MACH_REFID_'.$i] : NULL,
            'SHIFTID_17'  => (!is_null($request['shift17_SHIFT_REFID_'.$i])  && !empty($request['shift17_SHIFT_REFID_'.$i]) )  ? $request['shift17_SHIFT_REFID_'.$i] : NULL,
            'QTY_17' =>      (!is_null($request['dayqty17_'.$i])   && !empty($request['dayqty17_'.$i]) ) ? $request['dayqty17_'.$i] : 0,

            'MACHINEID_18'=> (!is_null($request['machine18_MACH_REFID_'.$i]) && !empty($request['machine18_MACH_REFID_'.$i]) ) ? $request['machine18_MACH_REFID_'.$i] : NULL,
            'SHIFTID_18'  => (!is_null($request['shift18_SHIFT_REFID_'.$i])  && !empty($request['shift18_SHIFT_REFID_'.$i]) )  ? $request['shift18_SHIFT_REFID_'.$i] : NULL,
            'QTY_18' =>      (!is_null($request['dayqty18_'.$i])   && !empty($request['dayqty18_'.$i]) ) ? $request['dayqty18_'.$i] : 0,

            'MACHINEID_19'=> (!is_null($request['machine19_MACH_REFID_'.$i]) && !empty($request['machine19_MACH_REFID_'.$i]) ) ? $request['machine19_MACH_REFID_'.$i] : NULL,
            'SHIFTID_19'  => (!is_null($request['shift19_SHIFT_REFID_'.$i])  && !empty($request['shift19_SHIFT_REFID_'.$i]) )  ? $request['shift19_SHIFT_REFID_'.$i] : NULL,
            'QTY_19' =>      (!is_null($request['dayqty19_'.$i])   && !empty($request['dayqty19_'.$i]) ) ? $request['dayqty19_'.$i] : 0,

            'MACHINEID_20'=> (!is_null($request['machine20_MACH_REFID_'.$i]) && !empty($request['machine20_MACH_REFID_'.$i]) ) ? $request['machine20_MACH_REFID_'.$i] : NULL,
            'SHIFTID_20'  => (!is_null($request['shift20_SHIFT_REFID_'.$i])  && !empty($request['shift20_SHIFT_REFID_'.$i]) )  ? $request['shift20_SHIFT_REFID_'.$i] : NULL,
            'QTY_20' =>      (!is_null($request['dayqty20_'.$i])   && !empty($request['dayqty20_'.$i]) ) ? $request['dayqty20_'.$i] : 0,

            'MACHINEID_21'=> (!is_null($request['machine21_MACH_REFID_'.$i]) && !empty($request['machine21_MACH_REFID_'.$i]) ) ? $request['machine21_MACH_REFID_'.$i] : NULL,
            'SHIFTID_21'  => (!is_null($request['shift21_SHIFT_REFID_'.$i])  && !empty($request['shift21_SHIFT_REFID_'.$i]) )  ? $request['shift21_SHIFT_REFID_'.$i] : NULL,
            'QTY_21' =>      (!is_null($request['dayqty21_'.$i])   && !empty($request['dayqty21_'.$i]) ) ? $request['dayqty21_'.$i] : 0,

            'MACHINEID_22'=> (!is_null($request['machine22_MACH_REFID_'.$i]) && !empty($request['machine22_MACH_REFID_'.$i]) ) ? $request['machine22_MACH_REFID_'.$i] : NULL,
            'SHIFTID_22'  => (!is_null($request['shift22_SHIFT_REFID_'.$i])  && !empty($request['shift22_SHIFT_REFID_'.$i]) )  ? $request['shift22_SHIFT_REFID_'.$i] : NULL,
            'QTY_22' =>      (!is_null($request['dayqty22_'.$i])   && !empty($request['dayqty22_'.$i]) ) ? $request['dayqty22_'.$i] : 0,

            'MACHINEID_23'=> (!is_null($request['machine23_MACH_REFID_'.$i]) && !empty($request['machine23_MACH_REFID_'.$i]) ) ? $request['machine23_MACH_REFID_'.$i] : NULL,
            'SHIFTID_23'  => (!is_null($request['shift23_SHIFT_REFID_'.$i])  && !empty($request['shift23_SHIFT_REFID_'.$i]) )  ? $request['shift23_SHIFT_REFID_'.$i] : NULL,
            'QTY_23' =>      (!is_null($request['dayqty23_'.$i])   && !empty($request['dayqty23_'.$i]) ) ? $request['dayqty23_'.$i] : 0,

            'MACHINEID_24'=> (!is_null($request['machine24_MACH_REFID_'.$i]) && !empty($request['machine24_MACH_REFID_'.$i]) ) ? $request['machine24_MACH_REFID_'.$i] : NULL,
            'SHIFTID_24'  => (!is_null($request['shift24_SHIFT_REFID_'.$i])  && !empty($request['shift24_SHIFT_REFID_'.$i]) )  ? $request['shift24_SHIFT_REFID_'.$i] : NULL,
            'QTY_24' =>      (!is_null($request['dayqty24_'.$i])   && !empty($request['dayqty24_'.$i]) ) ? $request['dayqty24_'.$i] : 0,

            'MACHINEID_25'=> (!is_null($request['machine25_MACH_REFID_'.$i]) && !empty($request['machine25_MACH_REFID_'.$i]) ) ? $request['machine25_MACH_REFID_'.$i] : NULL,
            'SHIFTID_25'  => (!is_null($request['shift25_SHIFT_REFID_'.$i])  && !empty($request['shift25_SHIFT_REFID_'.$i]) )  ? $request['shift25_SHIFT_REFID_'.$i] : NULL,
            'QTY_25' =>      (!is_null($request['dayqty25_'.$i])   && !empty($request['dayqty25_'.$i]) ) ? $request['dayqty25_'.$i] : 0,

            'MACHINEID_26'=> (!is_null($request['machine26_MACH_REFID_'.$i]) && !empty($request['machine26_MACH_REFID_'.$i]) ) ? $request['machine26_MACH_REFID_'.$i] : NULL,
            'SHIFTID_26'  => (!is_null($request['shift26_SHIFT_REFID_'.$i])  && !empty($request['shift26_SHIFT_REFID_'.$i]) )  ? $request['shift26_SHIFT_REFID_'.$i] : NULL,
            'QTY_26' =>      (!is_null($request['dayqty26_'.$i])   && !empty($request['dayqty26_'.$i]) ) ? $request['dayqty26_'.$i] : 0,

            'MACHINEID_27'=> (!is_null($request['machine27_MACH_REFID_'.$i]) && !empty($request['machine27_MACH_REFID_'.$i]) ) ? $request['machine27_MACH_REFID_'.$i] : NULL,
            'SHIFTID_27'  => (!is_null($request['shift27_SHIFT_REFID_'.$i])  && !empty($request['shift27_SHIFT_REFID_'.$i]) )  ? $request['shift27_SHIFT_REFID_'.$i] : NULL,
            'QTY_27' =>      (!is_null($request['dayqty27_'.$i])   && !empty($request['dayqty27_'.$i]) ) ? $request['dayqty27_'.$i] : 0,

            'MACHINEID_28'=> (!is_null($request['machine28_MACH_REFID_'.$i]) && !empty($request['machine28_MACH_REFID_'.$i]) ) ? $request['machine28_MACH_REFID_'.$i] : NULL,
            'SHIFTID_28'  => (!is_null($request['shift28_SHIFT_REFID_'.$i])  && !empty($request['shift28_SHIFT_REFID_'.$i]) )  ? $request['shift28_SHIFT_REFID_'.$i] : NULL,
            'QTY_28' =>      (!is_null($request['dayqty28_'.$i])   && !empty($request['dayqty28_'.$i]) ) ? $request['dayqty28_'.$i] : 0,

            'MACHINEID_29'=> (!is_null($request['machine29_MACH_REFID_'.$i]) && !empty($request['machine29_MACH_REFID_'.$i]) ) ? $request['machine29_MACH_REFID_'.$i] : NULL,
            'SHIFTID_29'  => (!is_null($request['shift29_SHIFT_REFID_'.$i])  && !empty($request['shift29_SHIFT_REFID_'.$i]) )  ? $request['shift29_SHIFT_REFID_'.$i] : NULL,
            'QTY_29' =>      (!is_null($request['dayqty29_'.$i])   && !empty($request['dayqty29_'.$i]) ) ? $request['dayqty29_'.$i] : 0,

            'MACHINEID_30'=> (!is_null($request['machine30_MACH_REFID_'.$i]) && !empty($request['machine30_MACH_REFID_'.$i]) ) ? $request['machine30_MACH_REFID_'.$i] : NULL,
            'SHIFTID_30'  => (!is_null($request['shift30_SHIFT_REFID_'.$i])  && !empty($request['shift30_SHIFT_REFID_'.$i]) )  ? $request['shift30_SHIFT_REFID_'.$i] : NULL,
            'QTY_30' =>      (!is_null($request['dayqty30_'.$i])   && !empty($request['dayqty30_'.$i]) ) ? $request['dayqty30_'.$i] : 0,

            'MACHINEID_31'=> (!is_null($request['machine31_MACH_REFID_'.$i]) && !empty($request['machine31_MACH_REFID_'.$i]) ) ? $request['machine31_MACH_REFID_'.$i] : NULL,
            'SHIFTID_31'  => (!is_null($request['shift31_SHIFT_REFID_'.$i])  && !empty($request['shift31_SHIFT_REFID_'.$i]) )  ? $request['shift31_SHIFT_REFID_'.$i] : NULL,
            'QTY_31' =>      (!is_null($request['dayqty31_'.$i])   && !empty($request['dayqty31_'.$i]) ) ? $request['dayqty31_'.$i] : 0,
            

            ];
        }
    }

        //dd($req_data);


        $wrapped_links["ITEM"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        // dump($XMLMAT);

        $VTID_REF     =   $this->vtid_ref;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $MPP_DOC_NO = trim(strtoupper($request['MPPDOCNO']));
        $MPP_DOC_DT = $request['MPP_DOC_DT'];
        $PERIOD_MTID_REF = $request['PERIOD_MTID_REF'];
        $MONTH_DAYS = $request['act_month_day'];

       
        $log_data = [ 
            $MPP_DOC_NO,    $MPP_DOC_DT,    $PERIOD_MTID_REF, $CYID_REF,
            $BRID_REF,      $FYID_REF,      $VTID_REF,        $XMLMAT , 
            $USERID,         Date('Y-m-d'), Date('h:i:s.u'),  $ACTIONNAME,
            $IPADDRESS, $MONTH_DAYS
        ];

      
        
        $sp_result = DB::select('EXEC SP_MPP_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
       
      
          if($sp_result[0]->RESULT=="SUCCESS"){
  
              return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);
  
          }else{
              return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
          }
          exit();   
}

//update the data

    public function Approve(Request $request){
        // dd($request->all());
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
            
            //------------------------
    for ($i=0; $i<=$r_count1; $i++)
    {
        if(isset($request['ITEMID_REF_'.$i]))
        {
        $req_data[$i] = [
            
        'ICATID_REF'        => $request['REF_BUID_'.$i],  //item category
        'ITEMIDID_REF'        => $request['ITEMID_REF_'.$i],
        'UOMID_REF'        => $request['itemuom_'.$i],     
        'PD_PLAN_QTY'         => (!is_null($request['ppqty_'.$i]) && !empty($request['ppqty_'.$i]) )? $request['ppqty_'.$i] : 0,
            
        'MACHINEID_1'=> ( !is_null($request['machine1_MACH_REFID_'.$i]) && !empty($request['machine1_MACH_REFID_'.$i]) ) ? $request['machine1_MACH_REFID_'.$i] : null,
        'SHIFTID_1'  => ( !is_null($request['shift1_SHIFT_REFID_'.$i])  && !empty($request['shift1_SHIFT_REFID_'.$i]) ) ? $request['shift1_SHIFT_REFID_'.$i] : NULL,
        'QTY_1' =>      ( !is_null($request['dayqty1_'.$i]) && !empty($request['dayqty1_'.$i]) )? $request['dayqty1_'.$i] : 0,
        
        'MACHINEID_2'=> (!is_null($request['machine2_MACH_REFID_'.$i]) && !empty($request['machine2_MACH_REFID_'.$i]) ) ? $request['machine2_MACH_REFID_'.$i] : NULL,
        'SHIFTID_2'  => (!is_null($request['shift2_SHIFT_REFID_'.$i])  && !empty($request['shift2_SHIFT_REFID_'.$i]) )  ? $request['shift2_SHIFT_REFID_'.$i] : NULL,
        'QTY_2' =>      (!is_null($request['dayqty2_'.$i])   && !empty($request['dayqty2_'.$i]) ) ? $request['dayqty2_'.$i] : 0,

        'MACHINEID_3'=> (!is_null($request['machine3_MACH_REFID_'.$i]) && !empty($request['machine3_MACH_REFID_'.$i]) ) ? $request['machine3_MACH_REFID_'.$i] : NULL,
        'SHIFTID_3'  => (!is_null($request['shift3_SHIFT_REFID_'.$i])  && !empty($request['shift3_SHIFT_REFID_'.$i]) )  ? $request['shift3_SHIFT_REFID_'.$i] : NULL,
        'QTY_3' =>      (!is_null($request['dayqty3_'.$i])   && !empty($request['dayqty3_'.$i]) ) ? $request['dayqty3_'.$i] : 0,

        'MACHINEID_4'=> (!is_null($request['machine4_MACH_REFID_'.$i]) && !empty($request['machine4_MACH_REFID_'.$i]) ) ? $request['machine4_MACH_REFID_'.$i] : NULL,
        'SHIFTID_4'  => (!is_null($request['shift4_SHIFT_REFID_'.$i])  && !empty($request['shift4_SHIFT_REFID_'.$i]) )  ? $request['shift4_SHIFT_REFID_'.$i] : NULL,
        'QTY_4' =>      (!is_null($request['dayqty4_'.$i])   && !empty($request['dayqty4_'.$i]) ) ? $request['dayqty4_'.$i] : 0,
        
        'MACHINEID_5'=> (!is_null($request['machine5_MACH_REFID_'.$i]) && !empty($request['machine5_MACH_REFID_'.$i]) ) ? $request['machine5_MACH_REFID_'.$i] : NULL,
        'SHIFTID_5'  => (!is_null($request['shift5_SHIFT_REFID_'.$i])  && !empty($request['shift5_SHIFT_REFID_'.$i]) )  ? $request['shift5_SHIFT_REFID_'.$i] : NULL,
        'QTY_5' =>      (!is_null($request['dayqty5_'.$i])   && !empty($request['dayqty5_'.$i]) ) ? $request['dayqty5_'.$i] : 0,

        
        'MACHINEID_6'=> (!is_null($request['machine6_MACH_REFID_'.$i]) && !empty($request['machine6_MACH_REFID_'.$i]) ) ? $request['machine6_MACH_REFID_'.$i] : NULL,
        'SHIFTID_6'  => (!is_null($request['shift6_SHIFT_REFID_'.$i])  && !empty($request['shift6_SHIFT_REFID_'.$i]) )  ? $request['shift6_SHIFT_REFID_'.$i] : NULL,
        'QTY_6' =>      (!is_null($request['dayqty6_'.$i])   && !empty($request['dayqty6_'.$i]) ) ? $request['dayqty6_'.$i] : 0,

        'MACHINEID_7'=> (!is_null($request['machine7_MACH_REFID_'.$i]) && !empty($request['machine7_MACH_REFID_'.$i]) ) ? $request['machine7_MACH_REFID_'.$i] : NULL,
        'SHIFTID_7'  => (!is_null($request['shift7_SHIFT_REFID_'.$i])  && !empty($request['shift7_SHIFT_REFID_'.$i]) )  ? $request['shift7_SHIFT_REFID_'.$i] : NULL,
        'QTY_7' =>      (!is_null($request['dayqty7_'.$i])   && !empty($request['dayqty7_'.$i]) ) ? $request['dayqty7_'.$i] : 0,

        'MACHINEID_8'=> (!is_null($request['machine8_MACH_REFID_'.$i]) && !empty($request['machine8_MACH_REFID_'.$i]) ) ? $request['machine8_MACH_REFID_'.$i] : NULL,
        'SHIFTID_8'  => (!is_null($request['shift8_SHIFT_REFID_'.$i])  && !empty($request['shift8_SHIFT_REFID_'.$i]) )  ? $request['shift8_SHIFT_REFID_'.$i] : NULL,
        'QTY_8' =>      (!is_null($request['dayqty8_'.$i])   && !empty($request['dayqty8_'.$i]) ) ? $request['dayqty8_'.$i] : 0,

        
        'MACHINEID_9'=> (!is_null($request['machine9_MACH_REFID_'.$i]) && !empty($request['machine9_MACH_REFID_'.$i]) ) ? $request['machine9_MACH_REFID_'.$i] : NULL,
        'SHIFTID_9'  => (!is_null($request['shift9_SHIFT_REFID_'.$i])  && !empty($request['shift9_SHIFT_REFID_'.$i]) )  ? $request['shift9_SHIFT_REFID_'.$i] : NULL,
        'QTY_9' =>      (!is_null($request['dayqty9_'.$i])   && !empty($request['dayqty9_'.$i]) ) ? $request['dayqty9_'.$i] : 0,

        'MACHINEID_10'=> (!is_null($request['machine10_MACH_REFID_'.$i]) && !empty($request['machine10_MACH_REFID_'.$i]) ) ? $request['machine10_MACH_REFID_'.$i] : NULL,
        'SHIFTID_10'  => (!is_null($request['shift10_SHIFT_REFID_'.$i])  && !empty($request['shift10_SHIFT_REFID_'.$i]) )  ? $request['shift10_SHIFT_REFID_'.$i] : NULL,
        'QTY_10' =>      (!is_null($request['dayqty10_'.$i])   && !empty($request['dayqty10_'.$i]) ) ? $request['dayqty10_'.$i] : 0,

        'MACHINEID_11'=> (!is_null($request['machine11_MACH_REFID_'.$i]) && !empty($request['machine11_MACH_REFID_'.$i]) ) ? $request['machine11_MACH_REFID_'.$i] : NULL,
        'SHIFTID_11'  => (!is_null($request['shift11_SHIFT_REFID_'.$i])  && !empty($request['shift11_SHIFT_REFID_'.$i]) )  ? $request['shift11_SHIFT_REFID_'.$i] : NULL,
        'QTY_11' =>      (!is_null($request['dayqty11_'.$i])   && !empty($request['dayqty11_'.$i]) ) ? $request['dayqty11_'.$i] : 0,

        'MACHINEID_12'=> (!is_null($request['machine12_MACH_REFID_'.$i]) && !empty($request['machine12_MACH_REFID_'.$i]) ) ? $request['machine12_MACH_REFID_'.$i] : NULL,
        'SHIFTID_12'  => (!is_null($request['shift12_SHIFT_REFID_'.$i])  && !empty($request['shift12_SHIFT_REFID_'.$i]) )  ? $request['shift12_SHIFT_REFID_'.$i] : NULL,
        'QTY_12' =>      (!is_null($request['dayqty12_'.$i])   && !empty($request['dayqty12_'.$i]) ) ? $request['dayqty12_'.$i] : 0,

        'MACHINEID_13'=> (!is_null($request['machine13_MACH_REFID_'.$i]) && !empty($request['machine13_MACH_REFID_'.$i]) ) ? $request['machine13_MACH_REFID_'.$i] : NULL,
        'SHIFTID_13'  => (!is_null($request['shift13_SHIFT_REFID_'.$i])  && !empty($request['shift13_SHIFT_REFID_'.$i]) )  ? $request['shift13_SHIFT_REFID_'.$i] : NULL,
        'QTY_13' =>      (!is_null($request['dayqty13_'.$i])   && !empty($request['dayqty13_'.$i]) ) ? $request['dayqty13_'.$i] : 0,

        'MACHINEID_14'=> (!is_null($request['machine14_MACH_REFID_'.$i]) && !empty($request['machine14_MACH_REFID_'.$i]) ) ? $request['machine14_MACH_REFID_'.$i] : NULL,
        'SHIFTID_14'  => (!is_null($request['shift14_SHIFT_REFID_'.$i])  && !empty($request['shift14_SHIFT_REFID_'.$i]) )  ? $request['shift14_SHIFT_REFID_'.$i] : NULL,
        'QTY_14' =>      (!is_null($request['dayqty14_'.$i])   && !empty($request['dayqty14_'.$i]) ) ? $request['dayqty14_'.$i] : 0,

        'MACHINEID_15'=> (!is_null($request['machine15_MACH_REFID_'.$i]) && !empty($request['machine15_MACH_REFID_'.$i]) ) ? $request['machine15_MACH_REFID_'.$i] : NULL,
        'SHIFTID_15'  => (!is_null($request['shift15_SHIFT_REFID_'.$i])  && !empty($request['shift15_SHIFT_REFID_'.$i]) )  ? $request['shift15_SHIFT_REFID_'.$i] : NULL,
        'QTY_15' =>      (!is_null($request['dayqty15_'.$i])   && !empty($request['dayqty15_'.$i]) ) ? $request['dayqty15_'.$i] : 0,

        'MACHINEID_16'=> (!is_null($request['machine16_MACH_REFID_'.$i]) && !empty($request['machine16_MACH_REFID_'.$i]) ) ? $request['machine16_MACH_REFID_'.$i] : NULL,
        'SHIFTID_16'  => (!is_null($request['shift16_SHIFT_REFID_'.$i])  && !empty($request['shift16_SHIFT_REFID_'.$i]) )  ? $request['shift16_SHIFT_REFID_'.$i] : NULL,
        'QTY_16' =>      (!is_null($request['dayqty16_'.$i])   && !empty($request['dayqty16_'.$i]) ) ? $request['dayqty16_'.$i] : 0,

        'MACHINEID_17'=> (!is_null($request['machine17_MACH_REFID_'.$i]) && !empty($request['machine17_MACH_REFID_'.$i]) ) ? $request['machine17_MACH_REFID_'.$i] : NULL,
        'SHIFTID_17'  => (!is_null($request['shift17_SHIFT_REFID_'.$i])  && !empty($request['shift17_SHIFT_REFID_'.$i]) )  ? $request['shift17_SHIFT_REFID_'.$i] : NULL,
        'QTY_17' =>      (!is_null($request['dayqty17_'.$i])   && !empty($request['dayqty17_'.$i]) ) ? $request['dayqty17_'.$i] : 0,

        'MACHINEID_18'=> (!is_null($request['machine18_MACH_REFID_'.$i]) && !empty($request['machine18_MACH_REFID_'.$i]) ) ? $request['machine18_MACH_REFID_'.$i] : NULL,
        'SHIFTID_18'  => (!is_null($request['shift18_SHIFT_REFID_'.$i])  && !empty($request['shift18_SHIFT_REFID_'.$i]) )  ? $request['shift18_SHIFT_REFID_'.$i] : NULL,
        'QTY_18' =>      (!is_null($request['dayqty18_'.$i])   && !empty($request['dayqty18_'.$i]) ) ? $request['dayqty18_'.$i] : 0,

        'MACHINEID_19'=> (!is_null($request['machine19_MACH_REFID_'.$i]) && !empty($request['machine19_MACH_REFID_'.$i]) ) ? $request['machine19_MACH_REFID_'.$i] : NULL,
        'SHIFTID_19'  => (!is_null($request['shift19_SHIFT_REFID_'.$i])  && !empty($request['shift19_SHIFT_REFID_'.$i]) )  ? $request['shift19_SHIFT_REFID_'.$i] : NULL,
        'QTY_19' =>      (!is_null($request['dayqty19_'.$i])   && !empty($request['dayqty19_'.$i]) ) ? $request['dayqty19_'.$i] : 0,

        'MACHINEID_20'=> (!is_null($request['machine20_MACH_REFID_'.$i]) && !empty($request['machine20_MACH_REFID_'.$i]) ) ? $request['machine20_MACH_REFID_'.$i] : NULL,
        'SHIFTID_20'  => (!is_null($request['shift20_SHIFT_REFID_'.$i])  && !empty($request['shift20_SHIFT_REFID_'.$i]) )  ? $request['shift20_SHIFT_REFID_'.$i] : NULL,
        'QTY_20' =>      (!is_null($request['dayqty20_'.$i])   && !empty($request['dayqty20_'.$i]) ) ? $request['dayqty20_'.$i] : 0,

        'MACHINEID_21'=> (!is_null($request['machine21_MACH_REFID_'.$i]) && !empty($request['machine21_MACH_REFID_'.$i]) ) ? $request['machine21_MACH_REFID_'.$i] : NULL,
        'SHIFTID_21'  => (!is_null($request['shift21_SHIFT_REFID_'.$i])  && !empty($request['shift21_SHIFT_REFID_'.$i]) )  ? $request['shift21_SHIFT_REFID_'.$i] : NULL,
        'QTY_21' =>      (!is_null($request['dayqty21_'.$i])   && !empty($request['dayqty21_'.$i]) ) ? $request['dayqty21_'.$i] : 0,

        'MACHINEID_22'=> (!is_null($request['machine22_MACH_REFID_'.$i]) && !empty($request['machine22_MACH_REFID_'.$i]) ) ? $request['machine22_MACH_REFID_'.$i] : NULL,
        'SHIFTID_22'  => (!is_null($request['shift22_SHIFT_REFID_'.$i])  && !empty($request['shift22_SHIFT_REFID_'.$i]) )  ? $request['shift22_SHIFT_REFID_'.$i] : NULL,
        'QTY_22' =>      (!is_null($request['dayqty22_'.$i])   && !empty($request['dayqty22_'.$i]) ) ? $request['dayqty22_'.$i] : 0,

        'MACHINEID_23'=> (!is_null($request['machine23_MACH_REFID_'.$i]) && !empty($request['machine23_MACH_REFID_'.$i]) ) ? $request['machine23_MACH_REFID_'.$i] : NULL,
        'SHIFTID_23'  => (!is_null($request['shift23_SHIFT_REFID_'.$i])  && !empty($request['shift23_SHIFT_REFID_'.$i]) )  ? $request['shift23_SHIFT_REFID_'.$i] : NULL,
        'QTY_23' =>      (!is_null($request['dayqty23_'.$i])   && !empty($request['dayqty23_'.$i]) ) ? $request['dayqty23_'.$i] : 0,

        'MACHINEID_24'=> (!is_null($request['machine24_MACH_REFID_'.$i]) && !empty($request['machine24_MACH_REFID_'.$i]) ) ? $request['machine24_MACH_REFID_'.$i] : NULL,
        'SHIFTID_24'  => (!is_null($request['shift24_SHIFT_REFID_'.$i])  && !empty($request['shift24_SHIFT_REFID_'.$i]) )  ? $request['shift24_SHIFT_REFID_'.$i] : NULL,
        'QTY_24' =>      (!is_null($request['dayqty24_'.$i])   && !empty($request['dayqty24_'.$i]) ) ? $request['dayqty24_'.$i] : 0,

        'MACHINEID_25'=> (!is_null($request['machine25_MACH_REFID_'.$i]) && !empty($request['machine25_MACH_REFID_'.$i]) ) ? $request['machine25_MACH_REFID_'.$i] : NULL,
        'SHIFTID_25'  => (!is_null($request['shift25_SHIFT_REFID_'.$i])  && !empty($request['shift25_SHIFT_REFID_'.$i]) )  ? $request['shift25_SHIFT_REFID_'.$i] : NULL,
        'QTY_25' =>      (!is_null($request['dayqty25_'.$i])   && !empty($request['dayqty25_'.$i]) ) ? $request['dayqty25_'.$i] : 0,

        'MACHINEID_26'=> (!is_null($request['machine26_MACH_REFID_'.$i]) && !empty($request['machine26_MACH_REFID_'.$i]) ) ? $request['machine26_MACH_REFID_'.$i] : NULL,
        'SHIFTID_26'  => (!is_null($request['shift26_SHIFT_REFID_'.$i])  && !empty($request['shift26_SHIFT_REFID_'.$i]) )  ? $request['shift26_SHIFT_REFID_'.$i] : NULL,
        'QTY_26' =>      (!is_null($request['dayqty26_'.$i])   && !empty($request['dayqty26_'.$i]) ) ? $request['dayqty26_'.$i] : 0,

        'MACHINEID_27'=> (!is_null($request['machine27_MACH_REFID_'.$i]) && !empty($request['machine27_MACH_REFID_'.$i]) ) ? $request['machine27_MACH_REFID_'.$i] : NULL,
        'SHIFTID_27'  => (!is_null($request['shift27_SHIFT_REFID_'.$i])  && !empty($request['shift27_SHIFT_REFID_'.$i]) )  ? $request['shift27_SHIFT_REFID_'.$i] : NULL,
        'QTY_27' =>      (!is_null($request['dayqty27_'.$i])   && !empty($request['dayqty27_'.$i]) ) ? $request['dayqty27_'.$i] : 0,

        'MACHINEID_28'=> (!is_null($request['machine28_MACH_REFID_'.$i]) && !empty($request['machine28_MACH_REFID_'.$i]) ) ? $request['machine28_MACH_REFID_'.$i] : NULL,
        'SHIFTID_28'  => (!is_null($request['shift28_SHIFT_REFID_'.$i])  && !empty($request['shift28_SHIFT_REFID_'.$i]) )  ? $request['shift28_SHIFT_REFID_'.$i] : NULL,
        'QTY_28' =>      (!is_null($request['dayqty28_'.$i])   && !empty($request['dayqty28_'.$i]) ) ? $request['dayqty28_'.$i] : 0,

        'MACHINEID_29'=> (!is_null($request['machine29_MACH_REFID_'.$i]) && !empty($request['machine29_MACH_REFID_'.$i]) ) ? $request['machine29_MACH_REFID_'.$i] : NULL,
        'SHIFTID_29'  => (!is_null($request['shift29_SHIFT_REFID_'.$i])  && !empty($request['shift29_SHIFT_REFID_'.$i]) )  ? $request['shift29_SHIFT_REFID_'.$i] : NULL,
        'QTY_29' =>      (!is_null($request['dayqty29_'.$i])   && !empty($request['dayqty29_'.$i]) ) ? $request['dayqty29_'.$i] : 0,

        'MACHINEID_30'=> (!is_null($request['machine30_MACH_REFID_'.$i]) && !empty($request['machine30_MACH_REFID_'.$i]) ) ? $request['machine30_MACH_REFID_'.$i] : NULL,
        'SHIFTID_30'  => (!is_null($request['shift30_SHIFT_REFID_'.$i])  && !empty($request['shift30_SHIFT_REFID_'.$i]) )  ? $request['shift30_SHIFT_REFID_'.$i] : NULL,
        'QTY_30' =>      (!is_null($request['dayqty30_'.$i])   && !empty($request['dayqty30_'.$i]) ) ? $request['dayqty30_'.$i] : 0,

        'MACHINEID_31'=> (!is_null($request['machine31_MACH_REFID_'.$i]) && !empty($request['machine31_MACH_REFID_'.$i]) ) ? $request['machine31_MACH_REFID_'.$i] : NULL,
        'SHIFTID_31'  => (!is_null($request['shift31_SHIFT_REFID_'.$i])  && !empty($request['shift31_SHIFT_REFID_'.$i]) )  ? $request['shift31_SHIFT_REFID_'.$i] : NULL,
        'QTY_31' =>      (!is_null($request['dayqty31_'.$i])   && !empty($request['dayqty31_'.$i]) ) ? $request['dayqty31_'.$i] : 0,
        
        ];

        }
    }    

            //------------------------
            $ACTIONNAME = $Approvallevel;

            $wrapped_links["ITEM"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
            
            // dump($XMLMAT);

            $VTID_REF     =   $this->vtid_ref;
            $USERID = Auth::user()->USERID;   
           
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $MPP_DOC_NO = trim(strtoupper($request['MPPDOCNO']));
            $MPP_DOC_DT = $request['MPP_DOC_DT'];
            $PERIOD_MTID_REF = $request['PERIOD_MTID_REF'];
            $MONTH_DAYS = $request['act_month_day'];

            
            $log_data = [ 
                $MPP_DOC_NO,    $MPP_DOC_DT,    $PERIOD_MTID_REF, $CYID_REF,
                $BRID_REF,      $FYID_REF,      $VTID_REF,        $XMLMAT , 
                $USERID,         Date('Y-m-d'), Date('h:i:s.u'),  $ACTIONNAME,
                $IPADDRESS, $MONTH_DAYS
            ];

            DUMP($log_data);
            
            $sp_result = DB::select('EXEC SP_MPP_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
            dd($sp_result);
    
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
                $TABLE      =   "TBL_TRN_PDMPP_HDR";
                $FIELD      =   "MPPID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
            // dd($xml);
            
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
        $TABLE      =   "TBL_TRN_PDMPP_HDR";
        $FIELD      =   "MPPID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_PDMPP_MAT',
           ];

        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_AFP  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);
        //dd($sp_result); 

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
    
    $destinationPath = storage_path()."/docs/company".$CYID_REF.'/ManualProductionPlanMSD';

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

                $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."#_".$filenamewithextension;  
                
                echo $filenametostore ;

                if ($uploadedFile->isValid()) {

                    if(in_array($extension,$allow_extnesions)){
                        
                        if($filesize < $allow_size){

                            $custfilename = $destinationPath."/".$filenametostore;

                            if (!file_exists($custfilename)) {

                               $uploadedFile->move($destinationPath, $filenametostore);  //upload in dir if not exists
                               $uploaded_data[$index]["FILENAME"] =$filenametostore;
                               $uploaded_data[$index]["LOCATION"] = $destinationPath."/";
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
        return redirect()->route("transaction",[229,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
         $sp_result = DB::select('EXEC SP_TRN_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);

   } catch (\Throwable $th) {
    
       return redirect()->route("transaction",[229,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("transaction",[229,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("transaction",[229,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("transaction",[229,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

    public function checkse(Request $request){

        // dd($request->LABEL_0);
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

    public function getmachines(Request $request){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;

        $cur_date = Date('Y-m-d');
        $ObjData = DB::select('select * from TBL_MST_MACHINE  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?) and CYID_REF = ?   and STATUS = ? ',
                        [$cur_date,$CYID_REF,'A']);
        
        
            if(!empty($ObjData)){

            foreach ($ObjData as $index=>$dataRow){               

                $row = '';
                $row = $row.'<tr id="FORMcode_'.$dataRow->MACHINEID .'"  class="clsFORMid"><td width="50%">'.$dataRow->MACHINE_NO;
                $row = $row.'<input type="hidden" id="txtFORMcode_'.$dataRow->MACHINEID.'" data-desc="'.$dataRow->MACHINE_NO.'"  data-descdate="'.$dataRow->MACHINE_DESC.'"
                value="'.$dataRow->MACHINEID.'"/></td><td>'.$dataRow->MACHINE_DESC.'</td></tr>';
                echo $row;

            }
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    }
    
    public function getshifts(Request $request){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;

        $cur_date = Date('Y-m-d');
        $ObjData = DB::select('select SHIFTID, SHIFT_CODE, SHIFT_NAME from TBL_MST_SHIFT  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?) and CYID_REF = ?   and STATUS = ? ',
                        [$cur_date,$CYID_REF,'A']);
        
        
            if(!empty($ObjData)){

            foreach ($ObjData as $index=>$dataRow){               

                $row = '';
                $row = $row.'<tr id="LISTPOP1code_'.$dataRow->SHIFTID .'"  class="clsLISTPOP1id"><td width="50%">'.$dataRow->SHIFT_CODE;
                $row = $row.'<input type="hidden" id="txtLISTPOP1code_'.$dataRow->SHIFTID.'" data-desc="'.$dataRow->SHIFT_CODE.'"  data-descdate="'.$dataRow->SHIFT_NAME.'"
                value="'.$dataRow->SHIFTID.'"/></td><td>'.$dataRow->SHIFT_NAME.'</td></tr>';
                echo $row;

            }
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    }


    public function codeduplicate(Request $request){

            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $MPPDOCNO =   $request['MPPDOCNO'];
            
            $objLabel = DB::table('TBL_TRN_PDMPP_HDR')
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('FYID_REF','=',$FYID_REF)
            ->where('MPP_DOC_NO','=',$MPPDOCNO)
            ->select('MPP_DOC_NO')
            ->first();
            
            if($objLabel){  

                return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
            
            }else{

                return Response::json(['notexists'=>true,'msg' => 'Ok']);
            }
            
            exit();
    }
 
    public function MachineCode($mid){
        $macinecode = "";
        if(trim($mid)!=""){
            $objMach =   DB::select('SELECT top 1 MACHINEID,MACHINE_NO,MACHINE_DESC FROM TBL_MST_MACHINE WHERE  MACHINEID = ? ', [$mid]);
            if(!empty($objMach)){
                $macinecode = $objMach[0]->MACHINE_NO;
            }
        }
        return $macinecode;
    }

    public function ShiftCode($sid){
        $shftcode = "";
        if(trim($sid)!=""){
            $objShit =   DB::select('SELECT top 1 SHIFTID,SHIFT_CODE,SHIFT_NAME FROM TBL_MST_SHIFT WHERE  SHIFTID = ? ', [$sid]);
            if(!empty($objShit)){
                $shftcode = $objShit[0]->SHIFT_CODE;
            }
        }        
        return $shftcode;
    }


    
} //class
