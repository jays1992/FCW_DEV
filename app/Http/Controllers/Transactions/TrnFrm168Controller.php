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

class TrnFrm168Controller extends Controller
{
    protected $form_id = 168;
    protected $vtid_ref   = 170;  //voucher type id
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

        $objDataList	=	DB::select("select hdr.*,dpt.NAME,fy.FYDESCRIPTION,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.AFSID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                            ) AS CREATED_BY,
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
                            inner join TBL_TRN_SLAF01_HDR hdr
                            on a.VID = hdr.AFSID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_DEPARTMENT dpt ON hdr.DEPID_REF = dpt.DEPID  
                            inner join TBL_MST_FYEAR fy ON hdr.FYID_REF = fy.FYID  
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.AFSID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );

        
        return view('transactions.sales.AnnualForecastSales.trnfrm168',compact(['REQUEST_DATA','objRights','FormId','objDataList']));        
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
                'HDR_TABLE'=>'TBL_TRN_SLAF01_HDR',
                'HDR_ID'=>'AFSID',
                'HDR_DOC_NO'=>'AFSNO',
                'HDR_DOC_DT'=>'AFSDT'
            );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        


        

        
       // dd($objSON);
             // return $objSalesQuotationData;

             $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
      
    return view('transactions.sales.AnnualForecastSales.trnfrm168add',
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
                    WHERE CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? 
                    AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ? AND BUID_REF=?', 
                    [$CYID_REF, $BRID_REF, $FYID_REF, $Status,$Bu_id ]);
        // dd($ObjItem);
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
                        $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:5%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                        $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                        $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'"
                        value="'.$dataRow->ITEMID.'"/></td>
                        <td style="width:8%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                        $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'"
                        value="'.$dataRow->NAME.'"/></td>';

                        $row = $row.' <td style="width:8%;" id="itempartno_'.$dataRow->ITEMID.'" >'.$dataRow->PARTNO;
                        $row = $row.'<input type="hidden" id="txtitempartno_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->PARTNO.'"
                        value="'.$dataRow->PARTNO.'"/></td>';
                        
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
                        <td style="width:5%;">Authorized</td>


     
                        </tr>';
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

    if(!is_null($id))
    {
        $objSalesenquiry = DB::table("TBL_TRN_SLAF01_HDR")
                        ->where('AFSID','=',$id)
                        ->select('TBL_TRN_SLAF01_HDR.*')
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
                        ->where('TBL_MST_ATTACHMENT.FYID_REF','=',Session::get('FYID_REF'))
                        ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                        ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                        ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                        ->get()->toArray();

                 // dump( $objAttachments);

            return view('transactions.sales.AnnualForecastSales.trnfrm168attachment',compact(['objSalesenquiry','objMstVoucherType','objAttachments']));
    }

}

    
   public function save(Request $request) {

    
        $r_count1 = count($request['rowscount']);
       // dd($r_count1); 
     
        //DD($request->ALL());
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]))
            {
                $req_data[$i] = [
                    'AFSID_REF'        => $request['AFSNO'],
                    'BUID_REF'        => $request['REF_BUID_'.$i],
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'CID_REF'        => $request['CUSTOMERID_REF_'.$i],
                    'PARTNO'        => $request['ItemPartno_'.$i],
                    'UOMID_REF'        => $request['itemuom_'.$i],     
                    'ITEMSPECI'         => $request['Itemspec_'.$i],
                    'MONTH1_QTY' => (($request['APRIL_QTY_'.$i])=="" ? 0 : $request['APRIL_QTY_'.$i]),
                    'MONTH1_VL' => (($request['APRIL_VALUE_'.$i])=="" ? 0 : $request['APRIL_VALUE_'.$i]),
                    'MONTH2_QTY' => (($request['MAY_QTY_'.$i])=="" ? 0 : $request['MAY_QTY_'.$i]),
                    'MONTH2_VL' => (($request['MAY_VALUE_'.$i])=="" ? 0 : $request['MAY_VALUE_'.$i]),
                    'MONTH3_QTY' => (($request['JUNE_QTY_'.$i])=="" ? 0 : $request['JUNE_QTY_'.$i]),
                    'MONTH3_VL' => (($request['JUNE_VALUE_'.$i])=="" ? 0 : $request['JUNE_VALUE_'.$i]),
                    'MONTH4_QTY' => (($request['JULY_QTY_'.$i])=="" ? 0 : $request['JULY_QTY_'.$i]),
                    'MONTH4_VL' => (($request['JULY_VALUE_'.$i])=="" ? 0 : $request['JULY_VALUE_'.$i]),
                    'MONTH5_QTY' => (($request['AUGUST_QTY_'.$i])=="" ? 0 : $request['AUGUST_QTY_'.$i]),
                    'MONTH5_VL' => (($request['AUGUST_VALUE_'.$i])=="" ? 0 : $request['AUGUST_VALUE_'.$i]),
                    'MONTH6_QTY' => (($request['SEPTEMBER_QTY_'.$i])=="" ? 0 : $request['SEPTEMBER_QTY_'.$i]),
                    'MONTH6_VL' => (($request['SEMPEMBER_VALUE_'.$i])=="" ? 0 : $request['SEMPEMBER_VALUE_'.$i]),
                    'MONTH7_QTY' => (($request['OCTOBER_QTY_'.$i])=="" ? 0 : $request['OCTOBER_QTY_'.$i]),
                    'MONTH7_VL' => (($request['OCTOBER_VALUE_'.$i])=="" ? 0 : $request['OCTOBER_VALUE_'.$i]),
                    'MONTH8_QTY' => (($request['NOVEMBER_QTY_'.$i])=="" ? 0 : $request['NOVEMBER_QTY_'.$i]),
                    'MONTH8_VL' => (($request['NOVEMBER_VALUE_'.$i])=="" ? 0 : $request['NOVEMBER_VALUE_'.$i]),
                    'MONTH9_QTY' => (($request['DECEMBER_QTY_'.$i])=="" ? 0 : $request['DECEMBER_QTY_'.$i]),
                    'MONTH9_VL' => (($request['DECEMBER_VALUE_'.$i])=="" ? 0 : $request['DECEMBER_VALUE_'.$i]),
                    'MONTH10_QTY' => (($request['JANUARY_QTY_'.$i])=="" ? 0 : $request['JANUARY_QTY_'.$i]),
                    'MONTH10_VL' => (($request['JANUARY_VALUE_'.$i])=="" ? 0 : $request['JANUARY_VALUE_'.$i]),
                    'MONTH11_QTY' => (($request['FEBRUARY_QTY_'.$i])=="" ? 0 : $request['FEBRUARY_QTY_'.$i]),
                    'MONTH11_VL' => (($request['FEBRUARY_VALUE_'.$i])=="" ? 0 : $request['FEBRUARY_VALUE_'.$i]),
                    'MONTH12_QTY' => (($request['MARCH_QTY_'.$i])=="" ? 0 : $request['MARCH_QTY_'.$i]),
                    'MONTH12_VL' => (($request['MARCH_VALUE_'.$i])=="" ? 0 : $request['MARCH_VALUE_'.$i]),
                    'FY_QTY' => (($request['FY_QTY_'.$i])=="" ? 0 : $request['FY_QTY_'.$i]),
                    'FY_VL' => (($request['FY_VALUE_'.$i])=="" ? 0 : $request['FY_VALUE_'.$i]),
                    
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
            $AFSNO = $request['AFSNO'];
            $AFSDT = $request['AFSDT'];
            $DEPID_REF = $request['DEPID_REF'];
            $FYID_REFS = $request['FYID_REF'];
           

            $log_data = [ 
                $AFSNO, $AFSDT, $DEPID_REF, $FYID_REFS, $CYID_REF, $BRID_REF, $VTID_REF, $FYID_REF,
                $XMLMAT , $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];

            //dd($log_data);
           


            
            $sp_result = DB::select('EXEC SP_AFS_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
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
            $objSE = DB::table('TBL_TRN_SLAF01_HDR')
                             ->where('TBL_TRN_SLAF01_HDR.FYID_REF1','=',$FYID_REF)
                             ->where('TBL_TRN_SLAF01_HDR.CYID_REF','=',$CYID_REF)
                             ->where('TBL_TRN_SLAF01_HDR.BRID_REF','=',$BRID_REF)
                             ->where('TBL_TRN_SLAF01_HDR.AFSID','=',$id)
                             ->leftJoin('TBL_MST_DEPARTMENT', 'TBL_TRN_SLAF01_HDR.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID') 
                             ->leftJoin('TBL_MST_FYEAR', 'TBL_TRN_SLAF01_HDR.FYID_REF','=','TBL_MST_FYEAR.FYID') 
                             ->select('TBL_TRN_SLAF01_HDR.*','TBL_MST_DEPARTMENT.NAME','TBL_MST_FYEAR.FYDESCRIPTION')
                             ->first();


            $objSEMAT = DB::table('TBL_TRN_SLAF01_MAT')                    
                             ->where('TBL_TRN_SLAF01_MAT.AFSID_REF','=',$id)
                             ->leftJoin('TBL_MST_BUSINESSUNIT', 'TBL_TRN_SLAF01_MAT.BUID_REF','=','TBL_MST_BUSINESSUNIT.BUID') 
                             ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_SLAF01_MAT.BUID_REF','=','TBL_MST_CUSTOMER.CID') 
                             ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_SLAF01_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID') 
                             ->select('TBL_TRN_SLAF01_MAT.*','TBL_MST_BUSINESSUNIT.BUCODE','TBL_MST_CUSTOMER.CCODE','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME')
                             ->orderBy('TBL_TRN_SLAF01_MAT.AFSMATID','ASC')
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


           $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        return view('transactions.sales.AnnualForecastSales.trnfrm168edit',compact(['objSE','objRights','objCount1',
           'objSEMAT','objItems','objDepartmentList','objFyearList','objCustomerList','objBusinessUnitList','TabSetting']));
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
            $objSE = DB::table('TBL_TRN_SLAF01_HDR')
                             ->where('TBL_TRN_SLAF01_HDR.FYID_REF1','=',$FYID_REF)
                             ->where('TBL_TRN_SLAF01_HDR.CYID_REF','=',$CYID_REF)
                             ->where('TBL_TRN_SLAF01_HDR.BRID_REF','=',$BRID_REF)
                             ->where('TBL_TRN_SLAF01_HDR.AFSID','=',$id)
                             ->leftJoin('TBL_MST_DEPARTMENT', 'TBL_TRN_SLAF01_HDR.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID') 
                             ->leftJoin('TBL_MST_FYEAR', 'TBL_TRN_SLAF01_HDR.FYID_REF','=','TBL_MST_FYEAR.FYID') 
                             ->select('TBL_TRN_SLAF01_HDR.*','TBL_MST_DEPARTMENT.NAME','TBL_MST_FYEAR.FYDESCRIPTION')
                             ->first();
                             


            $objSEMAT = DB::table('TBL_TRN_SLAF01_MAT')                    
                             ->where('TBL_TRN_SLAF01_MAT.AFSID_REF','=',$id)
                             ->leftJoin('TBL_MST_BUSINESSUNIT', 'TBL_TRN_SLAF01_MAT.BUID_REF','=','TBL_MST_BUSINESSUNIT.BUID') 
                             ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_SLAF01_MAT.BUID_REF','=','TBL_MST_CUSTOMER.CID') 
                             ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_SLAF01_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID') 
                             ->select('TBL_TRN_SLAF01_MAT.*','TBL_MST_BUSINESSUNIT.BUCODE','TBL_MST_CUSTOMER.CCODE','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME')
                             ->orderBy('TBL_TRN_SLAF01_MAT.AFSMATID','ASC')
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

           $objSONAmendment = DB::table('TBL_TRN_SLAF02_HDR')
           ->where('VTID_REF','=',171)
           ->where('CYID_REF','=',$CYID_REF)
           ->where('BRID_REF','=',$BRID_REF)
           //->where('FYID_REF1','=',$FYID_REF)
           ->where('AFSID_REF','=',$id)
           //->max('ANO')
          ->select('TBL_TRN_SLAF02_HDR.*')
           ->get();

           $AFSA_MAX = DB::table('TBL_TRN_SLAF02_HDR')
           ->where('VTID_REF','=',171)
           ->where('CYID_REF','=',$CYID_REF)
           ->where('BRID_REF','=',$BRID_REF)
           ->where('AFSID_REF','=',$id)    
           ->max('ANO');
          
       
        $ANumber=$AFSA_MAX+1;

        return view('transactions.sales.AnnualForecastSales.trnfrm168amendment',compact(['objSE','objRights','objCount1',
           'objSEMAT','objItems','objDepartmentList','objFyearList','objCustomerList','objBusinessUnitList','ANumber']));
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
            $objSE = DB::table('TBL_TRN_SLAF01_HDR')
            ->where('TBL_TRN_SLAF01_HDR.FYID_REF1','=',$FYID_REF)
            ->where('TBL_TRN_SLAF01_HDR.CYID_REF','=',$CYID_REF)
            ->where('TBL_TRN_SLAF01_HDR.BRID_REF','=',$BRID_REF)
            ->where('TBL_TRN_SLAF01_HDR.AFSID','=',$id)
            ->leftJoin('TBL_MST_DEPARTMENT', 'TBL_TRN_SLAF01_HDR.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID') 
            ->leftJoin('TBL_MST_FYEAR', 'TBL_TRN_SLAF01_HDR.FYID_REF','=','TBL_MST_FYEAR.FYID') 
            ->select('TBL_TRN_SLAF01_HDR.*','TBL_MST_DEPARTMENT.NAME','TBL_MST_FYEAR.FYDESCRIPTION')
            ->first();
        

            
            $objSEMAT = DB::table('TBL_TRN_SLAF01_MAT')                    
                             ->where('TBL_TRN_SLAF01_MAT.AFSID_REF','=',$id)
                             ->leftJoin('TBL_MST_BUSINESSUNIT', 'TBL_TRN_SLAF01_MAT.BUID_REF','=','TBL_MST_BUSINESSUNIT.BUID') 
                             ->leftJoin('TBL_MST_CUSTOMER', 'TBL_TRN_SLAF01_MAT.BUID_REF','=','TBL_MST_CUSTOMER.CID') 
                             ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_SLAF01_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID') 
                             ->select('TBL_TRN_SLAF01_MAT.*','TBL_MST_BUSINESSUNIT.BUCODE','TBL_MST_CUSTOMER.CCODE','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME')
                             ->orderBy('TBL_TRN_SLAF01_MAT.AFSMATID','ASC')
                             ->get()->toArray();
                           
                           
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

           $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        return view('transactions.sales.AnnualForecastSales.trnfrm168view',compact(['objSE','objRights','objCount1',
        'objSEMAT','objItems','objDepartmentList','objFyearList','objCustomerList','objBusinessUnitList','TabSetting']));
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
                    'AFSID_REF'        => $request['AFSNO'],
                    'BUID_REF'        => $request['REF_BUID_'.$i],
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'CID_REF'        => $request['CUSTOMERID_REF_'.$i],
                    'PARTNO'        => $request['ItemPartno_'.$i],
                    'UOMID_REF'        => $request['itemuom_'.$i],     
                    'ITEMSPECI'         => $request['Itemspec_'.$i],
                    'MONTH1_QTY' => (($request['APRIL_QTY_'.$i])=="" ? 0 : $request['APRIL_QTY_'.$i]),
                    'MONTH1_VL' => (($request['APRIL_VALUE_'.$i])=="" ? 0 : $request['APRIL_VALUE_'.$i]),
                    'MONTH2_QTY' => (($request['MAY_QTY_'.$i])=="" ? 0 : $request['MAY_QTY_'.$i]),
                    'MONTH2_VL' => (($request['MAY_VALUE_'.$i])=="" ? 0 : $request['MAY_VALUE_'.$i]),
                    'MONTH3_QTY' => (($request['JUNE_QTY_'.$i])=="" ? 0 : $request['JUNE_QTY_'.$i]),
                    'MONTH3_VL' => (($request['JUNE_VALUE_'.$i])=="" ? 0 : $request['JUNE_VALUE_'.$i]),
                    'MONTH4_QTY' => (($request['JULY_QTY_'.$i])=="" ? 0 : $request['JULY_QTY_'.$i]),
                    'MONTH4_VL' => (($request['JULY_VALUE_'.$i])=="" ? 0 : $request['JULY_VALUE_'.$i]),
                    'MONTH5_QTY' => (($request['AUGUST_QTY_'.$i])=="" ? 0 : $request['AUGUST_QTY_'.$i]),
                    'MONTH5_VL' => (($request['AUGUST_VALUE_'.$i])=="" ? 0 : $request['AUGUST_VALUE_'.$i]),
                    'MONTH6_QTY' => (($request['SEPTEMBER_QTY_'.$i])=="" ? 0 : $request['SEPTEMBER_QTY_'.$i]),
                    'MONTH6_VL' => (($request['SEMPEMBER_VALUE_'.$i])=="" ? 0 : $request['SEMPEMBER_VALUE_'.$i]),
                    'MONTH7_QTY' => (($request['OCTOBER_QTY_'.$i])=="" ? 0 : $request['OCTOBER_QTY_'.$i]),
                    'MONTH7_VL' => (($request['OCTOBER_VALUE_'.$i])=="" ? 0 : $request['OCTOBER_VALUE_'.$i]),
                    'MONTH8_QTY' => (($request['NOVEMBER_QTY_'.$i])=="" ? 0 : $request['NOVEMBER_QTY_'.$i]),
                    'MONTH8_VL' => (($request['NOVEMBER_VALUE_'.$i])=="" ? 0 : $request['NOVEMBER_VALUE_'.$i]),
                    'MONTH9_QTY' => (($request['DECEMBER_QTY_'.$i])=="" ? 0 : $request['DECEMBER_QTY_'.$i]),
                    'MONTH9_VL' => (($request['DECEMBER_VALUE_'.$i])=="" ? 0 : $request['DECEMBER_VALUE_'.$i]),
                    'MONTH10_QTY' => (($request['JANUARY_QTY_'.$i])=="" ? 0 : $request['JANUARY_QTY_'.$i]),
                    'MONTH10_VL' => (($request['JANUARY_VALUE_'.$i])=="" ? 0 : $request['JANUARY_VALUE_'.$i]),
                    'MONTH11_QTY' => (($request['FEBRUARY_QTY_'.$i])=="" ? 0 : $request['FEBRUARY_QTY_'.$i]),
                    'MONTH11_VL' => (($request['FEBRUARY_VALUE_'.$i])=="" ? 0 : $request['FEBRUARY_VALUE_'.$i]),
                    'MONTH12_QTY' => (($request['MARCH_QTY_'.$i])=="" ? 0 : $request['MARCH_QTY_'.$i]),
                    'MONTH12_VL' => (($request['MARCH_VALUE_'.$i])=="" ? 0 : $request['MARCH_VALUE_'.$i]),
                    'FY_QTY' => (($request['FY_QTY_'.$i])=="" ? 0 : $request['FY_QTY_'.$i]),
                    'FY_VL' => (($request['FY_VALUE_'.$i])=="" ? 0 : $request['FY_VALUE_'.$i]),
                    
                ];
            }
        }
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        

            $AFSANO=$request['AFSNO'];
            $AFSADT=$request['AFSADT'];
            $REASON_AFSA=$request['REASON_AFSA'];
            $AFSID_REF=$request['AFSID_REF'];
        

           $VTID_REF     =   171;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'EDIT';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
;
            $AFSDT = $request['AFSDT'];
            $DEPID_REF = $request['DEPID_REF'];
            $FYID_REFS = $request['FYID_REF'];
           

            $log_data = [ 
                $AFSID_REF, $AFSANO, $AFSADT, $DEPID_REF, $FYID_REFS, $REASON_AFSA, $CYID_REF, $BRID_REF, $VTID_REF, $FYID_REF,
                $XMLMAT , $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];

           
           


            
            $sp_result = DB::select('EXEC SP_AFSA_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);    
           
    
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
                      'AFSID_REF'        => $request['AFSNO'],
                      'BUID_REF'        => $request['REF_BUID_'.$i],
                      'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                      'CID_REF'        => $request['CUSTOMERID_REF_'.$i],
                      'PARTNO'        => $request['ItemPartno_'.$i],
                      'UOMID_REF'        => $request['itemuom_'.$i],     
                      'ITEMSPECI'         => $request['Itemspec_'.$i],
                      'MONTH1_QTY' => (($request['APRIL_QTY_'.$i])=="" ? 0 : $request['APRIL_QTY_'.$i]),
                      'MONTH1_VL' => (($request['APRIL_VALUE_'.$i])=="" ? 0 : $request['APRIL_VALUE_'.$i]),
                      'MONTH2_QTY' => (($request['MAY_QTY_'.$i])=="" ? 0 : $request['MAY_QTY_'.$i]),
                      'MONTH2_VL' => (($request['MAY_VALUE_'.$i])=="" ? 0 : $request['MAY_VALUE_'.$i]),
                      'MONTH3_QTY' => (($request['JUNE_QTY_'.$i])=="" ? 0 : $request['JUNE_QTY_'.$i]),
                      'MONTH3_VL' => (($request['JUNE_VALUE_'.$i])=="" ? 0 : $request['JUNE_VALUE_'.$i]),
                      'MONTH4_QTY' => (($request['JULY_QTY_'.$i])=="" ? 0 : $request['JULY_QTY_'.$i]),
                      'MONTH4_VL' => (($request['JULY_VALUE_'.$i])=="" ? 0 : $request['JULY_VALUE_'.$i]),
                      'MONTH5_QTY' => (($request['AUGUST_QTY_'.$i])=="" ? 0 : $request['AUGUST_QTY_'.$i]),
                      'MONTH5_VL' => (($request['AUGUST_VALUE_'.$i])=="" ? 0 : $request['AUGUST_VALUE_'.$i]),
                      'MONTH6_QTY' => (($request['SEPTEMBER_QTY_'.$i])=="" ? 0 : $request['SEPTEMBER_QTY_'.$i]),
                      'MONTH6_VL' => (($request['SEMPEMBER_VALUE_'.$i])=="" ? 0 : $request['SEMPEMBER_VALUE_'.$i]),
                      'MONTH7_QTY' => (($request['OCTOBER_QTY_'.$i])=="" ? 0 : $request['OCTOBER_QTY_'.$i]),
                      'MONTH7_VL' => (($request['OCTOBER_VALUE_'.$i])=="" ? 0 : $request['OCTOBER_VALUE_'.$i]),
                      'MONTH8_QTY' => (($request['NOVEMBER_QTY_'.$i])=="" ? 0 : $request['NOVEMBER_QTY_'.$i]),
                      'MONTH8_VL' => (($request['NOVEMBER_VALUE_'.$i])=="" ? 0 : $request['NOVEMBER_VALUE_'.$i]),
                      'MONTH9_QTY' => (($request['DECEMBER_QTY_'.$i])=="" ? 0 : $request['DECEMBER_QTY_'.$i]),
                      'MONTH9_VL' => (($request['DECEMBER_VALUE_'.$i])=="" ? 0 : $request['DECEMBER_VALUE_'.$i]),
                      'MONTH10_QTY' => (($request['JANUARY_QTY_'.$i])=="" ? 0 : $request['JANUARY_QTY_'.$i]),
                      'MONTH10_VL' => (($request['JANUARY_VALUE_'.$i])=="" ? 0 : $request['JANUARY_VALUE_'.$i]),
                      'MONTH11_QTY' => (($request['FEBRUARY_QTY_'.$i])=="" ? 0 : $request['FEBRUARY_QTY_'.$i]),
                      'MONTH11_VL' => (($request['FEBRUARY_VALUE_'.$i])=="" ? 0 : $request['FEBRUARY_VALUE_'.$i]),
                      'MONTH12_QTY' => (($request['MARCH_QTY_'.$i])=="" ? 0 : $request['MARCH_QTY_'.$i]),
                      'MONTH12_VL' => (($request['MARCH_VALUE_'.$i])=="" ? 0 : $request['MARCH_VALUE_'.$i]),
                      'FY_QTY' => (($request['FY_QTY_'.$i])=="" ? 0 : $request['FY_QTY_'.$i]),
                      'FY_VL' => (($request['FY_VALUE_'.$i])=="" ? 0 : $request['FY_VALUE_'.$i]),
                      
                  ];
              }
          }
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
              $AFSNO = $request['AFSNO'];
              $AFSDT = $request['AFSDT'];
              $DEPID_REF = $request['DEPID_REF'];
              $FYID_REFS = $request['FYID_REF'];
             
  
              $log_data = [ 
                  $AFSNO, $AFSDT, $DEPID_REF, $FYID_REFS, $CYID_REF, $BRID_REF, $VTID_REF, $FYID_REF,
                  $XMLMAT , $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
              ];
  
              //dd($log_data);
             
  
  
              
              $sp_result = DB::select('EXEC SP_AFS_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);    
             // dd($sp_result); 
      
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
                      'AFSID_REF'        => $request['AFSNO'],
                      'BUID_REF'        => $request['REF_BUID_'.$i],
                      'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                      'CID_REF'        => $request['CUSTOMERID_REF_'.$i],
                      'PARTNO'        => $request['ItemPartno_'.$i],
                      'UOMID_REF'        => $request['itemuom_'.$i],     
                      'ITEMSPECI'         => $request['Itemspec_'.$i],
                      'MONTH1_QTY' => (($request['APRIL_QTY_'.$i])=="" ? 0 : $request['APRIL_QTY_'.$i]),
                      'MONTH1_VL' => (($request['APRIL_VALUE_'.$i])=="" ? 0 : $request['APRIL_VALUE_'.$i]),
                      'MONTH2_QTY' => (($request['MAY_QTY_'.$i])=="" ? 0 : $request['MAY_QTY_'.$i]),
                      'MONTH2_VL' => (($request['MAY_VALUE_'.$i])=="" ? 0 : $request['MAY_VALUE_'.$i]),
                      'MONTH3_QTY' => (($request['JUNE_QTY_'.$i])=="" ? 0 : $request['JUNE_QTY_'.$i]),
                      'MONTH3_VL' => (($request['JUNE_VALUE_'.$i])=="" ? 0 : $request['JUNE_VALUE_'.$i]),
                      'MONTH4_QTY' => (($request['JULY_QTY_'.$i])=="" ? 0 : $request['JULY_QTY_'.$i]),
                      'MONTH4_VL' => (($request['JULY_VALUE_'.$i])=="" ? 0 : $request['JULY_VALUE_'.$i]),
                      'MONTH5_QTY' => (($request['AUGUST_QTY_'.$i])=="" ? 0 : $request['AUGUST_QTY_'.$i]),
                      'MONTH5_VL' => (($request['AUGUST_VALUE_'.$i])=="" ? 0 : $request['AUGUST_VALUE_'.$i]),
                      'MONTH6_QTY' => (($request['SEPTEMBER_QTY_'.$i])=="" ? 0 : $request['SEPTEMBER_QTY_'.$i]),
                      'MONTH6_VL' => (($request['SEMPEMBER_VALUE_'.$i])=="" ? 0 : $request['SEMPEMBER_VALUE_'.$i]),
                      'MONTH7_QTY' => (($request['OCTOBER_QTY_'.$i])=="" ? 0 : $request['OCTOBER_QTY_'.$i]),
                      'MONTH7_VL' => (($request['OCTOBER_VALUE_'.$i])=="" ? 0 : $request['OCTOBER_VALUE_'.$i]),
                      'MONTH8_QTY' => (($request['NOVEMBER_QTY_'.$i])=="" ? 0 : $request['NOVEMBER_QTY_'.$i]),
                      'MONTH8_VL' => (($request['NOVEMBER_VALUE_'.$i])=="" ? 0 : $request['NOVEMBER_VALUE_'.$i]),
                      'MONTH9_QTY' => (($request['DECEMBER_QTY_'.$i])=="" ? 0 : $request['DECEMBER_QTY_'.$i]),
                      'MONTH9_VL' => (($request['DECEMBER_VALUE_'.$i])=="" ? 0 : $request['DECEMBER_VALUE_'.$i]),
                      'MONTH10_QTY' => (($request['JANUARY_QTY_'.$i])=="" ? 0 : $request['JANUARY_QTY_'.$i]),
                      'MONTH10_VL' => (($request['JANUARY_VALUE_'.$i])=="" ? 0 : $request['JANUARY_VALUE_'.$i]),
                      'MONTH11_QTY' => (($request['FEBRUARY_QTY_'.$i])=="" ? 0 : $request['FEBRUARY_QTY_'.$i]),
                      'MONTH11_VL' => (($request['FEBRUARY_VALUE_'.$i])=="" ? 0 : $request['FEBRUARY_VALUE_'.$i]),
                      'MONTH12_QTY' => (($request['MARCH_QTY_'.$i])=="" ? 0 : $request['MARCH_QTY_'.$i]),
                      'MONTH12_VL' => (($request['MARCH_VALUE_'.$i])=="" ? 0 : $request['MARCH_VALUE_'.$i]),
                      'FY_QTY' => (($request['FY_QTY_'.$i])=="" ? 0 : $request['FY_QTY_'.$i]),
                      'FY_VL' => (($request['FY_VALUE_'.$i])=="" ? 0 : $request['FY_VALUE_'.$i]),
                      
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
              $AFSNO = $request['AFSNO'];
              $AFSDT = $request['AFSDT'];
              $DEPID_REF = $request['DEPID_REF'];
              $FYID_REFS = $request['FYID_REF'];
             
  
              $log_data = [ 
                  $AFSNO, $AFSDT, $DEPID_REF, $FYID_REFS, $CYID_REF, $BRID_REF, $VTID_REF, $FYID_REF,
                  $XMLMAT , $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
              ];
  
              //dd($log_data);
             
  
  
              
              $sp_result = DB::select('EXEC SP_AFS_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);    
             // dd($sp_result); 
      
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
                $TABLE      =   "TBL_TRN_SLAF01_HDR";
                $FIELD      =   "AFSID";
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
        $TABLE      =   "TBL_TRN_SLAF01_HDR";
        $FIELD      =   "AFSID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
         'NT'  => 'TBL_TRN_SLAF01_MAT',
        ];
   
        $req_data[1]=[
         'NT'  => 'TBL_TRN_SLAF02_HDR',
        ];
   
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_AFS  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);
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
    
    $image_path         =   "docs/company".$CYID_REF."/AnnualForecastSales";     
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
        return redirect()->route("transaction",[168,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
    
       return redirect()->route("transaction",[168,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("transaction",[168,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("transaction",[168,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("transaction",[168,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
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
