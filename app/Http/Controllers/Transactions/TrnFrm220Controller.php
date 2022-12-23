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

class TrnFrm220Controller extends Controller
{
    protected $form_id = 220;
    protected $vtid_ref   = 172;  //voucher type id
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


        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');   
            


        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList	=	DB::select("select hdr.AFPID,hdr.AFP_NO,hdr.AFP_DT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.AFPID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                            ) AS CREATED_BY,
                            hdr.STATUS,dpt.NAME,fy.FYDESCRIPTION,
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
                            inner join TBL_TRN_PRAF01_HDR hdr
                            on a.VID = hdr.AFPID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_DEPARTMENT dpt ON hdr.DEPID_REF = dpt.DEPID 
                            inner join TBL_MST_FYEAR fy ON hdr.FYID_REF = fy.FYID 
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.AFPID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );
        
        return view('transactions.purchase.AnnualForecastPurchase.trnfrm220',compact(['REQUEST_DATA','objRights','FormId','objDataList']));        
    }

    public function add(){       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
       


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
        

            $doc_req    =   array(
                'VTID_REF'=>$this->vtid_ref,
                'HDR_TABLE'=>'TBL_TRN_PRAF01_HDR',
                'HDR_ID'=>'AFPID',
                'HDR_DOC_NO'=>'AFP_NO',
                'HDR_DOC_DT'=>'AFP_DT'
            );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
    

          

        // $objlast_DT = DB::select('SELECT MAX(AFP_DT) AFP_DT FROM TBL_TRN_PRAF01_HDR  
        // WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND VTID_REF = ? AND STATUS = ?', 
        // [$CYID_REF, $BRID_REF, $FYID_REF, $this->vtid_ref, 'N' ]);
        

        
       // dd($objSON);


       $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
            
      
    return view('transactions.purchase.AnnualForecastPurchase.trnfrm220add',
    compact(['objDepartmentList','objFyearList','objCustomerList','objBusinessUnitList','TabSetting','doc_req','docarray']));       
   }

   

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

   

    public function getItemDetails(Request $request){
        //dd($request->all()); 
        $Status = $request['status'];
        $Bu_id = $request['BU_NO'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $StdCost = 0;
        $Taxid = [];
                
        $ObjItem =  DB::select('SELECT * FROM TBL_MST_ITEM  
                    WHERE CYID_REF = ? AND BRID_REF = ?  
                    AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ? AND BUID_REF=?', 
                    [$CYID_REF, $BRID_REF,  $Status,$Bu_id ]);
       //dd($ObjItem);
                if(!empty($ObjItem)){

                    foreach ($ObjItem as $index=>$dataRow){ //print_r($dataRow);
                    
                        
                    $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ?  AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $dataRow->MAIN_UOMID_REF, 'A' ]);

                    $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ?  AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $dataRow->ALT_UOMID_REF, $Status ]);
                    
                    $ObjAltQTY =  DB::select('SELECT TOP 1  TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV  
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
                   
                //    $ObjItemOpen =  DB::select('select top 1  *  from TBL_MST_ITEM_OB_MAT 
                //                 WHERE  ITEMID_REF = ? AND UOMID_REF = ?', [$dataRow->ITEMID, $dataRow->MAIN_UOMID_REF]);

                
                $ObjItemOpen =  DB::select('select SUM(CURRENT_QTY) AS OPENING_VL from TBL_MST_STOCK 
                                    WHERE  ITEMID_REF = ? AND UOMID_REF = ? AND CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND STATUS = ?', [$dataRow->ITEMID, $dataRow->MAIN_UOMID_REF, $CYID_REF, $BRID_REF, $FYID_REF,'A']);
                              
                  // DUMP($ObjItemOpen);
                        $ITEM_OPENING_VL=0;
                        if(!empty($ObjItemOpen)){
                            $ITEM_OPENING_VL= !is_null($ObjItemOpen[0]->OPENING_VL) ? $ObjItemOpen[0]->OPENING_VL : 0;
                        }                    
                        
                        $ObjAFSMat =  DB::select('select top 1  *  from TBL_TRN_SLAF01_MAT 
                                    WHERE  ITEMID_REF = ? AND UOMID_REF = ?', [$dataRow->ITEMID, $dataRow->MAIN_UOMID_REF]);

                        if(!empty($ObjAFSMat)){
                            $SMONTH1_QTY = $ObjAFSMat[0]->MONTH1_QTY;
                            $SMONTH2_QTY = $ObjAFSMat[0]->MONTH2_QTY;
                            $SMONTH3_QTY = $ObjAFSMat[0]->MONTH3_QTY;
                            $SMONTH4_QTY = $ObjAFSMat[0]->MONTH4_QTY;
                            $SMONTH5_QTY = $ObjAFSMat[0]->MONTH5_QTY;
                            $SMONTH6_QTY = $ObjAFSMat[0]->MONTH6_QTY;
                            $SMONTH7_QTY = $ObjAFSMat[0]->MONTH7_QTY;
                            $SMONTH8_QTY = $ObjAFSMat[0]->MONTH8_QTY;
                            $SMONTH9_QTY = $ObjAFSMat[0]->MONTH9_QTY;
                            $SMONTH10_QTY = $ObjAFSMat[0]->MONTH10_QTY;
                            $SMONTH11_QTY = $ObjAFSMat[0]->MONTH11_QTY;
                            $SMONTH12_QTY = $ObjAFSMat[0]->MONTH12_QTY;
                        } else {
                            $SMONTH1_QTY = 0;
                            $SMONTH2_QTY = 0;
                            $SMONTH3_QTY = 0;
                            $SMONTH4_QTY = 0;
                            $SMONTH5_QTY = 0;
                            $SMONTH6_QTY = 0;
                            $SMONTH7_QTY = 0;
                            $SMONTH8_QTY = 0;
                            $SMONTH9_QTY = 0;
                            $SMONTH10_QTY = 0;
                            $SMONTH11_QTY = 0;
                            $SMONTH12_QTY = 0;
                        }
                                                            


                        $row = '';
                        $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:5%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                        $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'" data-sapcustcode="'.$dataRow->SAP_CUSTOMER_CODE.'"  data-sapcustname="'.$dataRow->SAP_CUSTOMER_NAME.'" data-sappartno="'.$dataRow->SAP_PART_NO.'" data-itemopenqty="'.$ITEM_OPENING_VL.'"
                        data-itemrate="'.$dataRow->STDCOST.'"
                        data-smonth1_qty="'.$SMONTH1_QTY.'"
                        data-smonth2_qty="'.$SMONTH2_QTY.'"
                        data-smonth3_qty="'.$SMONTH3_QTY.'"
                        data-smonth4_qty="'.$SMONTH4_QTY.'"
                        data-smonth5_qty="'.$SMONTH5_QTY.'"
                        data-smonth6_qty="'.$SMONTH6_QTY.'"
                        data-smonth7_qty="'.$SMONTH7_QTY.'"
                        data-smonth8_qty="'.$SMONTH8_QTY.'"
                        data-smonth9_qty="'.$SMONTH9_QTY.'"
                        data-smonth10_qty="'.$SMONTH10_QTY.'"
                        data-smonth11_qty="'.$SMONTH11_QTY.'"
                        data-smonth12_qty="'.$SMONTH12_QTY.'"                      
                        
                        value="'.$dataRow->ITEMID.'"/></td>
                        <td style="width:8%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'"
                        value="'.$dataRow->NAME.'"/></td>';

                        $row = $row.' <td style="width:8%;" id="itempartno_'.$dataRow->ITEMID.'" >'.$dataRow->SAP_PART_NO;
                        $row = $row.'<input type="hidden" id="txtitempartno_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->SAP_PART_NO.'"
                        value="'.$dataRow->SAP_PART_NO.'"/></td>';
                        
                        $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                        value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        $row = $row.'<td style="width:8%;" id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'"
                        value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                        $row = $row.'<td style="width:8%;" id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="'.$FROMQTY.'"
                        value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                        $row = $row.'<td style="width:8%;" id="itax_'.$ObjItem[0]->ITEMID.'"><input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'" />'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                        
                        <td style="width:8%;">'.$BusinessUnit.'</td>
                        <td style="width:8%;">'.$ALPS_PART_NO.'</td>
                        <td style="width:8%;">'.$CUSTOMER_PART_NO.'</td>
                        <td style="width:8%;">'.$OEM_PART_NO.'</td>
                        <td style="width:5%;">Authorized</td>';

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
        $objMst = DB::table("TBL_TRN_PRAF01_HDR")
                    ->where('AFPID','=',$id)
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
                        
            return view('transactions.purchase.AnnualForecastPurchase.trnfrm220attachment',compact(['FormId','objMst','objMstVoucherType','objAttachments']));
    }

}

    
   public function save(Request $request) {

    
        $r_count1 = count($request['rowscount']);
       //dump($r_count1); 
     
       //DD($request->ALL());
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]))
            {
                $req_data[$i] = [
                   // 'AFSID_REF'        => $request['AFSNO'],
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
      //  dd($req_data);
     
        
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
            
          

            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $AFP_NO = $request['AFSNO'];
            $AFP_DT = $request['AFSDT'];
            $DEPID_REF = $request['DEPID_REF'];
            $FYID_REFS = $request['FYID_REF'];

            // @AFP_NO INT,@AFP_DT DATE,@DEPID_REF INT,@FYID_REF INT,@CYID_REF INT,@BRID_REF INT,@FYID_REF1 INT,@VTID_REF INT,@XMLMAT XML  ,            
            // @USERID_REF INT,@UPDATE date,@UPTIME time,@ACTION varchar(30),@IPADDRESS varchar(30)    
           

            $log_data = [ 
                $AFP_NO, $AFP_DT, $DEPID_REF, $FYID_REFS, $CYID_REF, $BRID_REF, $FYID_REF, $VTID_REF,
                $XMLMAT , $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];

            //dump($log_data);
            
            $sp_result = DB::select('EXEC SP_AFP_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
           // dd($sp_result);
            
        
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

           $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');


        return view('transactions.purchase.AnnualForecastPurchase.trnfrm220edit',compact(['objSE','objRights','objCount1',
           'objSEMAT','objItems','objDepartmentList','objFyearList','objCustomerList','objBusinessUnitList','objlast_DT','TabSetting']));
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

            return view('transactions.purchase.AnnualForecastPurchase.trnfrm220amendment',compact(['objSE','objRights','objCount1',
            'objSEMAT','objItems','objDepartmentList','objFyearList','objCustomerList','objBusinessUnitList','objlast_DT','ANumber']));
            }
     
       }
     
       public function view($id){
     
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


           $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');


        return view('transactions.purchase.AnnualForecastPurchase.trnfrm220view',compact(['objSE','objRights','objCount1',
           'objSEMAT','objItems','objDepartmentList','objFyearList','objCustomerList','objBusinessUnitList','objlast_DT','TabSetting']));
        }
      
        }

    //update the data
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
        // dd($request->all());
          
        $r_count1 = $request['Row_Count1'];  
          
          
        for ($i=0; $i<=$r_count1; $i++)
         {
             if(isset($request['ITEMID_REF_'.$i]))
             {
                 $req_data[$i] = [
                    // 'AFSID_REF'        => $request['AFSNO'],
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
       //  dd($req_data);
      
         
             $wrapped_links["MAT"] = $req_data; 
             $XMLMAT = ArrayToXml::convert($wrapped_links);
             
           
 
             $VTID_REF     =   $this->vtid_ref;
             $VID = 0;
             $USERID = Auth::user()->USERID;   
             $ACTIONNAME = 'EDIT';
             $IPADDRESS = $request->getClientIp();
             $CYID_REF = Auth::user()->CYID_REF;
             $BRID_REF = Session::get('BRID_REF');
             $FYID_REF = Session::get('FYID_REF');
             $AFP_NO = $request['AFSNO'];
             $AFP_DT = $request['AFSDT'];
             $DEPID_REF = $request['DEPID_REF'];
             $FYID_REFS = $request['FYID_REF'];
 
             // @AFP_NO INT,@AFP_DT DATE,@DEPID_REF INT,@FYID_REF INT,@CYID_REF INT,@BRID_REF INT,@FYID_REF1 INT,@VTID_REF INT,@XMLMAT XML  ,            
             // @USERID_REF INT,@UPDATE date,@UPTIME time,@ACTION varchar(30),@IPADDRESS varchar(30)    
            
 
             $log_data = [ 
                 $AFP_NO, $AFP_DT, $DEPID_REF, $FYID_REFS, $CYID_REF, $BRID_REF, $FYID_REF, $VTID_REF,
                 $XMLMAT , $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
             ];
 
           // dump($log_data);
             
             $sp_result = DB::select('EXEC SP_AFP_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
            //dd($sp_result);
      
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
  
          
          
          for ($i=0; $i<=$r_count1; $i++)
          {
              if(isset($request['ITEMID_REF_'.$i]))
              {
                $req_data[$i] = [
                    // 'AFSID_REF'        => $request['AFSNO'],
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
  
             $VTID_REF     =   $this->vtid_ref;
              $VID = 0;
              $USERID = Auth::user()->USERID;   
              $ACTIONNAME = $Approvallevel;
              $IPADDRESS = $request->getClientIp();
              $CYID_REF = Auth::user()->CYID_REF;
              $BRID_REF = Session::get('BRID_REF');
              $FYID_REF = Session::get('FYID_REF');
              $AFP_NO = $request['AFSNO'];
              $AFP_DT = $request['AFSDT'];
              $DEPID_REF = $request['DEPID_REF'];
              $FYID_REFS = $request['FYID_REF'];
             
  
            $log_data = [ 
                $AFP_NO, $AFP_DT, $DEPID_REF, $FYID_REFS, $CYID_REF, $BRID_REF, $FYID_REF, $VTID_REF,
                $XMLMAT , $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];
  
              //DUMP($log_data);
              $sp_result = DB::select('EXEC SP_AFP_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);   
              //dd($sp_result); 
      
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
                $TABLE      =   "TBL_TRN_PRAF01_HDR";
                $FIELD      =   "AFPID";
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
        $TABLE      =   "TBL_TRN_PRAF01_HDR";
        $FIELD      =   "AFPID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_PRAF01_MAT',
           ];

           
        $req_data[1]=[
         'NT'  => 'TBL_TRN_PRAF02_HDR',
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
    
    $image_path         =   "docs/company".$CYID_REF."/AnnualForecastPurchase";     
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
        return redirect()->route("transaction",[220,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
    
       return redirect()->route("transaction",[220,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("transaction",[220,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("transaction",[220,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("transaction",[220,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
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
    
}
