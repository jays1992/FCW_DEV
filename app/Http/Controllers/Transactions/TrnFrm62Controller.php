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

class TrnFrm62Controller extends Controller
{
    protected $form_id = 62;
    protected $vtid_ref   = 62;
    protected $view     = "transactions.Purchase.VendorQuotationComparision.trnfrm62";
   
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];
    
    public function __construct()
    {
        $this->middleware('auth');
    }

   

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

        $objDataList	=	DB::select("select hdr.VQCID,hdr.VQC_NO,hdr.VQC_DT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.VQCID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_VQTC01_HDR hdr
                            on a.VID = hdr.VQCID 
                            and a.VTID_REF = hdr.VTID_REF
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_SUBLEDGER sl ON hdr.VID_REF1 = sl.SGLID  
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.VQCID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );



        return view($this->view,compact(['REQUEST_DATA','FormId','objRights','objDataList']));
    }

    
    public function getVendor(Request $request){

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $CODE       =   $request['CODE'];
        $NAME       =   $request['NAME'];
        $VENDOR_TYPE       =   $request['VENDOR_TYPE'];
    
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
                <td class="ROW1"> <input type="checkbox" name="'.$VENDOR_TYPE.'[]" id="vendoridcode_'.$index.'"  class="clsvendorid" value="'.$VID.'" ></td>
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

    public function get_items(Request $request){

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $id       =   $request['id'];
        $VENDOR_TYPE       =   $request['VENDOR_TYPE'];
        $VID_REF       =   $request['VID_REF'];

        $COMPANY_NAME   =   DB::table('TBL_MST_COMPANY')->where('STATUS','=','A')->where('CYID','=',Auth::user()->CYID_REF)->select('TBL_MST_COMPANY.NAME')->first()->NAME;
    
        $company_check       =   strpos($COMPANY_NAME,"ALPS")!== false?'':'hidden';

        $ObjData = DB::table('TBL_TRN_VDQT01_MAT')                    
        ->where('TBL_TRN_VDQT01_MAT.VQID_REF','=',$id)
        ->leftJoin('TBL_MST_ITEM','TBL_TRN_VDQT01_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                
        ->leftJoin('TBL_MST_UOM','TBL_TRN_VDQT01_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                
        ->select( 
            'TBL_TRN_VDQT01_MAT.QUOTATION_QTY',
            'TBL_TRN_VDQT01_MAT.RATEP_UOM',
            'TBL_TRN_VDQT01_MAT.VQID_REF',
            'TBL_MST_ITEM.ITEMID',
            'TBL_MST_ITEM.ICODE',
            'TBL_MST_ITEM.NAME',
            'TBL_MST_ITEM.ALPS_PART_NO',
            'TBL_MST_ITEM.CUSTOMER_PART_NO',
            'TBL_MST_ITEM.OEM_PART_NO',
            'TBL_MST_UOM.UOMID',
            'TBL_MST_UOM.UOMCODE',
            'TBL_MST_UOM.DESCRIPTIONS',

        )
        ->orderBy('TBL_TRN_VDQT01_MAT.VQMATID','ASC')
        ->get()->toArray();



        if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){            
            if($VENDOR_TYPE=='REF1'){
            $RATE1=$dataRow->RATEP_UOM;
            $QTY1=$dataRow->QUOTATION_QTY;
            $AMOUNT1=round($RATE1*$QTY1, 2);     
            $RATE2='0.000';
            $QTY2='0.00000';
            $AMOUNT2='0.00'; 
            $RATE3='0.000';
            $QTY3='0.00000';
            $AMOUNT3='0.00'; 
            }else if($VENDOR_TYPE=='REF2'){
            $RATE1='0.000';
            $QTY1='0.00000';
            $AMOUNT1='0.00000'; 
            $RATE2=$dataRow->RATEP_UOM;
            $QTY2=$dataRow->QUOTATION_QTY;
            $AMOUNT2=round($RATE2*$QTY2, 2);   
            $RATE3='0.000';
            $QTY3='0.00000';
            $AMOUNT3='0.00'; 
            }
            else if($VENDOR_TYPE=='REF3'){
            $RATE1='0.000';
            $QTY1='0.00000';
            $AMOUNT1='0.00';   
            $RATE2='0.000';
            $QTY2='0.000000';
            $AMOUNT2='0.00'; 
            $RATE3=$dataRow->RATEP_UOM;
            $QTY3=$dataRow->QUOTATION_QTY;
            $AMOUNT3=round($RATE3*$QTY3, 2);  
            }     
               // $tbody = '';
                   $tbody= '<tr  class="participantRow'.$VID_REF.'" >
                         <td hidden><input type="text" name="rowscount1[]"  > </td>
                         <td hidden><input type="text" name="VID_REF[]" id="VID_REF_'.$VID_REF.'" value="'.$VID_REF.'" > </td>
                         <td hidden><input type="text" name="VQID_REF[]" id="VQID_REF_'.$index.'" value="'.$dataRow->VQID_REF.'" > </td>
                         <td hidden><input type="text" name="ITEMID_REF[]" id="ITEMID_REF_'.$index.'"  value="'.$dataRow->ITEMID.'"> </td>                                                    
                         <td><input type="text" name="ITEMCODE[]"  class="form-control" value="'.$dataRow->ICODE.'"  autocomplete="off"  readonly style="width:100px;" /></td>
                         <td><input type="text" name="ITEMNAME[]"  class="form-control" value="'.$dataRow->NAME.'"  autocomplete="off"  readonly style="width:200px;" /></td>
                         <td '.$company_check.'><input type="text" name="Alpspartno_0"  value="'.$dataRow->ALPS_PART_NO.'" class="form-control"  autocomplete="off"  readonly  /></td>
                         <td '.$company_check.'><input type="text" name="Custpartno_0"  value="'.$dataRow->CUSTOMER_PART_NO.'" class="form-control"  autocomplete="off"  readonly  /></td>
                         <td '.$company_check.'><input type="text" name="OEMpartno_0"  value="'.$dataRow->OEM_PART_NO.'" class="form-control"  autocomplete="off"  readonly /></td>
                           
                     <td><input type="text" name="UOMID[]"  class="form-control" value="'.$dataRow->UOMCODE.'-'.$dataRow->DESCRIPTIONS.'"  autocomplete="off"  readonly style="width:100px;"/></td>
                     <td hidden><input type="hidden" name="UOMID_REF[]"  value="'.$dataRow->UOMID.'"  class="form-control"  autocomplete="off" /></td>
                   
                       <td><input type="text" name="QTY1[]"   value="'.$QTY1.'" class="form-control three-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                       <td><input type="text" name="RATE1[]"  value="'.$RATE1.'" class="form-control three-digits" maxlength="15"  autocomplete="off" readonly  /></td>
                       <td><input type="text" name="AMOUNT1[]"  value="'.$AMOUNT1.'" class="form-control three-digits"  maxlength="15"  autocomplete="off" readonly /></td>

                       <td><input type="text" name="QTY2[]"   value="'.$QTY2.'" class="form-control three-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                       <td><input type="text" name="RATE2[]"  value="'.$RATE2.'" class="form-control three-digits" maxlength="15"  autocomplete="off" readonly  /></td>
                       <td><input type="text" name="AMOUNT2[]"  value="'.$AMOUNT2.'" class="form-control three-digits"  maxlength="15"  autocomplete="off" readonly /></td>

                       <td><input type="text" name="QTY3[]"   value="'.$QTY3.'" class="form-control three-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                       <td><input type="text" name="RATE3[]"  value="'.$RATE3.'" class="form-control three-digits" maxlength="15"  autocomplete="off" readonly  /></td>
                       <td><input type="text" name="AMOUNT3[]"  value="'.$AMOUNT3.'" class="form-control three-digits"  maxlength="15"  autocomplete="off" readonly /></td>
                   
                             
                     <td align="center" ><button class="btn add material" disabled title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button class="btn remove dmaterial" disabled title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                 
                         </tr>';

                         echo $tbody;

            }
    
        }else{
           
        }
        exit();
    
    }


    public function get_terms_conditions(Request $request){

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $id       =   $request['id'];
        $VENDOR_TYPE       =   $request['VENDOR_TYPE'];
        $VID_REF       =   $request['VID_REF'];

 


        $ObjData = DB::table('TBL_TRN_VDQT01_TNC')                    
        ->where('TBL_TRN_VDQT01_TNC.VQID_REF','=',$id)   
        ->leftJoin('TBL_MST_TNC_DETAILS','TBL_TRN_VDQT01_TNC.TNCDID_REF','=','TBL_MST_TNC_DETAILS.TNCDID')           
        ->select( 
            'TBL_MST_TNC_DETAILS.TNC_NAME',           
            'TBL_TRN_VDQT01_TNC.VQID_REF',
            'TBL_TRN_VDQT01_TNC.VALUE',
            'TBL_TRN_VDQT01_TNC.TNCDID_REF AS TNCID_REF',
        )
        ->orderBy('TBL_TRN_VDQT01_TNC.VQTNCID','ASC')
        ->get()->toArray();

       // dd($ObjData);

        

        if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){            
            if($VENDOR_TYPE=='REF1'){
            $VALUE1=$dataRow->VALUE;
            $VALUE2=''; 
            $VALUE3=''; 
            }else if($VENDOR_TYPE=='REF2'){       
            $VALUE1='';
            $VALUE2=$dataRow->VALUE;
            $VALUE3=''; 
            }
            else if($VENDOR_TYPE=='REF3'){
            $VALUE1='';
            $VALUE2='';
            $VALUE3=$dataRow->VALUE;
            }     

                   $tbody= '<tr  class="participantRow3'.$VID_REF.'" >                
                   <td hidden><input type="text" name="VID_REF_TNC[]" id="VID_REF_'.$VID_REF.'" value="'.$VID_REF.'" > </td>
                   <td hidden><input type="text" name="rowscount2[]"  > </td>
                   <td hidden><input type="hidden" name="TNCID_REF[]" value="'.$dataRow->TNCID_REF.'" class="form-control" autocomplete="off" /></td>
                   <td hidden><input type="hidden" name="VQID_REF_TNC[]" value="'.$dataRow->VQID_REF.'" class="form-control" autocomplete="off" /></td>
                   <td ><input type="text" name="TNC_DESC[]" readonly value="'.$dataRow->TNC_NAME.'" class="form-control" autocomplete="off" /></td>
                   <td ><input type="text"  name="VALUE1[]" value="'.$VALUE1.'"class="form-control" autocomplete="off" readonly /></td>
                   <td ><input type="text"  name="VALUE2[]" value="'.$VALUE2.'"class="form-control" autocomplete="off" readonly /></td>
                   <td ><input type="text"  name="VALUE3[]" value="'.$VALUE3.'"class="form-control" autocomplete="off" readonly /></td>
            
                   </td>
                      <td align="center" ><button class="btn add TNC" title="add" data-toggle="tooltip"  type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DTNC" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                  </tr>
              <tr></tr>';

                         echo $tbody;

            }
    
        }else{
           
        }
        exit();
    
    }

    public function get_calculations_temp(Request $request){

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $id       =   $request['id'];
        $VENDOR_TYPE       =   $request['VENDOR_TYPE'];
        $VID_REF       =   $request['VID_REF'];



        $ObjData = DB::table('TBL_TRN_VDQT01_CAL')                    
        ->where('TBL_TRN_VDQT01_CAL.VQID_REF','=',$id)   
        ->leftJoin('TBL_MST_CALCULATIONTEMPLATE','TBL_TRN_VDQT01_CAL.TID_REF','=','TBL_MST_CALCULATIONTEMPLATE.TID')           
        ->select( 
            'TBL_MST_CALCULATIONTEMPLATE.COMPONENT',           
            'TBL_TRN_VDQT01_CAL.VQID_REF',
            'TBL_TRN_VDQT01_CAL.VALUE',
            'TBL_TRN_VDQT01_CAL.RATE',
            'TBL_TRN_VDQT01_CAL.TID_REF AS CTID_REF'
        )
        ->orderBy('TBL_TRN_VDQT01_CAL.VQCALID','ASC')
        ->get()->toArray(); 


        if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){            
            if($VENDOR_TYPE=='REF1'){
            $RATE1=$dataRow->RATE;
            $VALUE1=$dataRow->VALUE;
            $RATE2='0.0000';
            $VALUE2='0.00';       
            $RATE3='0.0000';
            $VALUE3='0.00';   
            }else if($VENDOR_TYPE=='REF2'){       
            $RATE1='0.0000';
            $VALUE1='0.00';
            $RATE2=$dataRow->RATE;
            $VALUE2=$dataRow->VALUE;   
            $RATE3='0.0000';
            $VALUE3='0.00';  
            }
            else if($VENDOR_TYPE=='REF3'){
            $RATE1='0.0000';
            $VALUE1='0.00';
            $RATE2='0.0000';
            $VALUE2='0.00';
            $RATE3=$dataRow->RATE;
            $VALUE3=$dataRow->VALUE;            
            }     




                //$tbody = '';
                   $tbody= ' <tr  class="participantRow5'.$VID_REF.'">

                <td hidden><input type="text" name="VID_REF_CAL[]" id="VID_REF_'.$VID_REF.'" value="'.$VID_REF.'" > </td>
                <td hidden><input type="text" name="rowscount3[]"  > </td>
        
                <td hidden><input type="hidden"  name="VQID_REF_CAL[]" id="VQID_REF_'.$index.'" value="'.$dataRow->VQID_REF.'" class="form-control" autocomplete="off" /></td>
                <td hidden><input type="hidden"  name="CTID_REF[]"  value="'.$dataRow->CTID_REF.'" class="form-control" autocomplete="off" /></td>
                <td><input type="text"  name="COMPONENT[]"  value="'.$dataRow->COMPONENT.'" class="form-control four-digits"  autocomplete="off"  readonly/></td>
                
                 <td><input type="text" name="RATECAL1[]"  value="'.$RATE1.'" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                 <td><input type="text" name="AMOUNTCAL1[]"  value="'.$VALUE1.'"  class="form-control four-digits" maxlength="15" autocomplete="off"  readonly/></td>

                 <td><input type="text" name="RATECAL2[]"  value="'.$RATE2.'" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                 <td><input type="text" name="AMOUNTCAL2[]" value="'.$VALUE2.'"  class="form-control four-digits" maxlength="15" autocomplete="off"  readonly/></td>

                 <td><input type="text" name="RATECAL3[]"  value="'.$RATE3.'" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                 <td><input type="text" name="AMOUNTCAL3[]" value="'.$VALUE3.'"  class="form-control four-digits" maxlength="15" autocomplete="off"  readonly/></td>
                              
                 </td>
                   <td hidden style="text-align:center;"><input type="checkbox" class="filter-none" name="calACTUAL_0" id="calACTUAL_0" value=""   ></td>
                   <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button> <button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
               </tr>
               <tr></tr>';

                         echo $tbody;
            }
    
        }else{
           
        }
        exit();
    
    }





        public function get_vendor_quotations(Request $request) {    

            //dd($request->all()); 
             
                 $Status = "A";
                 $CYID_REF = Auth::user()->CYID_REF;
                 $BRID_REF = Session::get('BRID_REF');
                 $FYID_REF = Session::get('FYID_REF');
                 $VID_REF=$request['VID_REF'];
                 $DYNAMIC_NAME=$request['DYNAMIC_NAME'];
             
                 $objmachine = DB::table('TBL_TRN_VDQT01_HDR')
                 ->where('CYID_REF','=',Auth::user()->CYID_REF)
                 ->where('BRID_REF','=',Session::get('BRID_REF'))
                 ->where('VID_REF','=',$VID_REF)
                 ->where('STATUS','=',$Status)
                 ->select('VQID','VQ_NO','VQ_DT') 
                 ->get()    
                 ->toArray();
             
               //  dd($objmachine); 
                  
             
                 if(!empty($objmachine)){        
                     foreach ($objmachine as $index=>$dataRow){
             
             
                         $row = '';
                         $row = $row.'<tr ><td style="text-align:center; width:10%">';
                         $row = $row.'<input type="checkbox" name="'.$DYNAMIC_NAME.'[]"  id="machinecode_'.$dataRow->VQID.'" class="clsspid_vq" 
                         value="'.$dataRow->VQID.'"/>             
                         </td>           
                         <td style="width:30%;">'.$dataRow->VQ_NO;
                         $row = $row.'<input type="hidden" id="txtmachinecode_'.$dataRow->VQID.'" data-code="'.$dataRow->VQ_NO.'"   data-desc="'.$dataRow->VQ_NO.'" 
                         value="'.$dataRow->VQID.'"/></td>
             
                         <td style="width:60%;">'.$dataRow->VQ_DT.'</td>
               
             
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

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_VQTC01_HDR',
            'HDR_ID'=>'VQCID',
            'HDR_DOC_NO'=>'VQC_NO',
            'HDR_DOC_DT'=>'VQC_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        
  

        $objlastVQC_DT = DB::select('SELECT MAX(VQC_DT) VQC_DT FROM TBL_TRN_VQTC01_HDR  
                        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
                        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);

                    
        $FormId  = $this->form_id;
        $objCOMPANY = DB::table('TBL_MST_COMPANY')
                    ->where('CYID','=',$CYID_REF)
                    ->where('STATUS','=',$Status)
                    ->select('TBL_MST_COMPANY.*')
                    ->first();

        $AlpsStatus =   $this->AlpsStatus();
        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

    return view($this->view.'add', compact(['AlpsStatus','FormId','objlastVQC_DT','objCOMPANY','TabSetting','doc_req','docarray']));       
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



            public function attachment($id){

                $FormId = $this->form_id;
                if(!is_null($id))
                {
                    $objMst = DB::table("TBL_TRN_VQTC01_HDR")
                                ->where('VQCID','=',$id)
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
   
    
    $r_count1 = count($request['rowscount1']);
    if(isset($request['rowscount2'])){
    $r_count2 = count($request['rowscount2']);
    }
    if(isset($request['rowscount3'])){
        $r_count3 = count($request['rowscount3']);
    }



    
    for ($i=0; $i<=$r_count1; $i++)
    {
        if(isset($request['ITEMID_REF'][$i]))
        {
            $req_data[$i] = [
                'ITEMID_REF'    => $request['ITEMID_REF'][$i],
                'UOMID_REF' => $request['UOMID_REF'][$i],
                'DESCRIPTIONS' => $request['ITEMNAME'][$i],     
                'QID_REF' => $request['VID_REF'][$i],     
                'V1_QTY' => (!empty($request['QTY1'][$i]) ? $request['QTY1'][$i] : 0),
                'V1_RATE' => (!empty($request['RATE1'][$i]) ? $request['RATE1'][$i] : 0),
                'V2_QTY' => (!empty($request['QTY2'][$i]) ? $request['QTY2'][$i] : 0),
                'V2_RATE' => (!empty($request['RATE2'][$i]) ? $request['RATE2'][$i] : 0),
                'V3_QTY' => (!empty($request['QTY3'][$i]) ? $request['QTY3'][$i] : 0),
                'V3_RATE' => (!empty($request['RATE3'][$i]) ? $request['RATE3'][$i] : 0),
             
            ];
        }
    }



        $wrapped_links["PRC"] = $req_data; 
        $XMLPRC = ArrayToXml::convert($wrapped_links);



        if(isset($request['rowscount2'])){
    for ($i=0; $i<=$r_count2; $i++)
    {
            if(isset($request['TNCID_REF'][$i]) && !is_null($request['TNCID_REF'][$i]))
            {
                if(isset($request['TNCID_REF'][$i]))
                {
                    $reqdata2[$i] = [
                      
                        'TNCID_REF'    => $request['TNCID_REF'][$i],   
                        'QID_REF' => $request['VID_REF_TNC'][$i],                    
                        'V1_VALUE' => (!empty($request['VALUE1'][$i]) ? $request['VALUE1'][$i] : 0),
                        'V2_VALUE' => (!empty($request['VALUE2'][$i]) ? $request['VALUE2'][$i] : 0),
                        'V3_VALUE' => (!empty($request['VALUE3'][$i]) ? $request['VALUE3'][$i] : 0),
                        
                  
                    ];
                }
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
  
       if(isset($request['rowscount3'])){


        //dd($r_count3);
    for ($i=0; $i<=$r_count3; $i++)
    {
            if(isset($request['CTID_REF'][$i]) && !is_null($request['CTID_REF'][$i]))
            {
                if(isset($request['CTID_REF'][$i]))
                {
                    $reqdata3[$i] = [
                      
                        'TID_REF'    => $request['CTID_REF'][$i],    
                        'QID_REF' => $request['VID_REF_CAL'][$i],                   
                        'V1_RATE' => (!empty($request['RATECAL1'][$i]) ? $request['RATECAL1'][$i] : 0),
                        'V1_VALUE' => (!empty($request['AMOUNTCAL1'][$i]) ? $request['AMOUNTCAL1'][$i] : 0),
                        'V2_RATE' => (!empty($request['RATECAL2'][$i]) ? $request['RATECAL2'][$i] : 0),
                        'V2_VALUE' => (!empty($request['AMOUNTCAL2'][$i]) ? $request['AMOUNTCAL2'][$i] : 0),
                        'V3_RATE' => (!empty($request['RATECAL3'][$i]) ? $request['RATECAL3'][$i] : 0),
                        'V3_VALUE' => (!empty($request['AMOUNTCAL3'][$i]) ? $request['AMOUNTCAL3'][$i] : 0)                     

                  
                    ];
                }
            }
        
    }
}
   // }


//dd($reqdata3);


       if(isset($reqdata3))
       { 
        $wrapped_links3["CAL"] = $reqdata3;
        $XMLCAL = ArrayToXml::convert($wrapped_links3);
       }
       else
       {
        $XMLCAL = NULL; 
       }   
    
        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $VQC_NO = strtoupper($request['VQC_NO']);
        $VQC_DT = $request['VQC_DT'];

        $VID_REF1 = $request['VID_REF1'];
        $VQID_REF1 = $request['VQID_REF1'];
        $VID_REF2 = $request['VID_REF2'];
        $VQID_REF2 = $request['VQID_REF2'];
        $VID_REF3 = $request['VID_REF3'];
        $VQID_REF3 = $request['VQID_REF3'];

        $log_data = [ 
            $VQC_NO,$VQC_DT,$VID_REF1,$VQID_REF1,$VID_REF2,$VQID_REF2,$VID_REF3,$VQID_REF3,$CYID_REF, $BRID_REF,$FYID_REF,$VTID_REF,$XMLPRC,$XMLTNC,$XMLCAL, $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
        ];

       //dd($log_data); 

            $sp_result = DB::select('EXEC SP_VQC_IN ?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?, ?,?,?,?', $log_data);     
           
           
        
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
            
            
            $objMstResponse = DB::table('TBL_TRN_VQTC01_HDR')
                ->where('TBL_TRN_VQTC01_HDR.FYID_REF','=',Session::get('FYID_REF'))
                ->where('TBL_TRN_VQTC01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                ->where('TBL_TRN_VQTC01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                ->where('TBL_TRN_VQTC01_HDR.VQCID','=',$id)
                ->leftJoin('TBL_MST_VENDOR AS VENDOR1', 'TBL_TRN_VQTC01_HDR.VID_REF1','=','VENDOR1.SLID_REF')
                ->leftJoin('TBL_MST_VENDOR AS VENDOR2', 'TBL_TRN_VQTC01_HDR.VID_REF2','=','VENDOR2.SLID_REF')
                ->leftJoin('TBL_MST_VENDOR AS VENDOR3', 'TBL_TRN_VQTC01_HDR.VID_REF3','=','VENDOR3.SLID_REF')
                ->leftJoin('TBL_TRN_VDQT01_HDR AS QUOTATION1', 'TBL_TRN_VQTC01_HDR.VQID_REF1','=','QUOTATION1.VQID')
                ->leftJoin('TBL_TRN_VDQT01_HDR AS QUOTATION2', 'TBL_TRN_VQTC01_HDR.VQID_REF2','=','QUOTATION2.VQID')
                ->leftJoin('TBL_TRN_VDQT01_HDR AS QUOTATION3', 'TBL_TRN_VQTC01_HDR.VQID_REF3','=','QUOTATION3.VQID')
                ->select('TBL_TRN_VQTC01_HDR.*','VENDOR1.VCODE as VENDOR_CODE1','VENDOR1.NAME as VENDOR_NAME1','VENDOR2.VCODE as VENDOR_CODE2','VENDOR2.NAME as VENDOR_NAME2','VENDOR3.VCODE as VENDOR_CODE3','VENDOR3.NAME as VENDOR_NAME3','QUOTATION1.VQ_NO AS VQ_NO1','QUOTATION2.VQ_NO AS VQ_NO2','QUOTATION3.VQ_NO AS VQ_NO3')
                ->first();

               // dd($objMstResponse); 


            $objList1 = DB::table('TBL_TRN_VQTC01_PRC')                    
                ->where('TBL_TRN_VQTC01_PRC.VQCID_REF','=',$id) 
                ->leftJoin('TBL_MST_ITEM','TBL_TRN_VQTC01_PRC.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                
                ->leftJoin('TBL_MST_UOM','TBL_TRN_VQTC01_PRC.UOMID_REF','=','TBL_MST_UOM.UOMID')                
                ->select( 
                    'TBL_TRN_VQTC01_PRC.*',
                    'TBL_MST_ITEM.ITEMID',
                    'TBL_MST_ITEM.ICODE',
                    'TBL_MST_ITEM.NAME',
                    'TBL_MST_ITEM.ALPS_PART_NO',
                    'TBL_MST_ITEM.CUSTOMER_PART_NO',
                    'TBL_MST_ITEM.OEM_PART_NO',
                    'TBL_MST_UOM.UOMID',
                    'TBL_MST_UOM.UOMCODE',
                    'TBL_MST_UOM.DESCRIPTIONS',                )
                ->orderBy('TBL_TRN_VQTC01_PRC.PRCID','ASC')
                ->get()->toArray();

                //dd($objList1); 



                $objList2 = DB::table('TBL_TRN_VQTC01_TNC')                    
                ->where('TBL_TRN_VQTC01_TNC.VQCID_REF','=',$id)   
                ->leftJoin('TBL_MST_TNC_DETAILS','TBL_TRN_VQTC01_TNC.TNCID_REF','=','TBL_MST_TNC_DETAILS.TNCDID')           
                ->select( 
                    'TBL_MST_TNC_DETAILS.TNC_NAME',           
                    'TBL_TRN_VQTC01_TNC.*',
                )
                ->orderBy('TBL_TRN_VQTC01_TNC.VQCTNCID','ASC')
                ->get()->toArray();
                    
              


                $objList3 = DB::table('TBL_TRN_VQTC01_CAL')                    
                ->where('TBL_TRN_VQTC01_CAL.VQCID_REF','=',$id)   
                ->leftJoin('TBL_MST_CALCULATIONTEMPLATE','TBL_TRN_VQTC01_CAL.TID_REF','=','TBL_MST_CALCULATIONTEMPLATE.TID')           
                ->select( 
                    'TBL_MST_CALCULATIONTEMPLATE.COMPONENT',           
                    'TBL_TRN_VQTC01_CAL.*',          
                )
                ->orderBy('TBL_TRN_VQTC01_CAL.VQCCALID','ASC')
                ->get()->toArray();


           // dd($objList3); 
               





                $objlastVQC_DT = DB::select('SELECT MAX(VQC_DT) VQC_DT FROM TBL_TRN_VQTC01_HDR  
                WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
                [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);

            //     DD($objlastVQC_DT[0]->VQC_DT); 
                    
            $FormId  = $this->form_id;


            $objCOMPANY = DB::table('TBL_MST_COMPANY')
            ->where('CYID','=',$CYID_REF)
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_COMPANY.*')
            ->first();

            $AlpsStatus =   $this->AlpsStatus();
            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');


        return view($this->view.'edit', compact(['AlpsStatus','FormId','objRights','objlastVQC_DT','objMstResponse','objList1','objList2','objList3','objCOMPANY','TabSetting']));     
        }
    
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

            
       
            
            
            $objMstResponse = DB::table('TBL_TRN_VQTC01_HDR')
                ->where('TBL_TRN_VQTC01_HDR.FYID_REF','=',Session::get('FYID_REF'))
                ->where('TBL_TRN_VQTC01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                ->where('TBL_TRN_VQTC01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                ->where('TBL_TRN_VQTC01_HDR.VQCID','=',$id)
                ->leftJoin('TBL_MST_VENDOR AS VENDOR1', 'TBL_TRN_VQTC01_HDR.VID_REF1','=','VENDOR1.SLID_REF')
                ->leftJoin('TBL_MST_VENDOR AS VENDOR2', 'TBL_TRN_VQTC01_HDR.VID_REF2','=','VENDOR2.SLID_REF')
                ->leftJoin('TBL_MST_VENDOR AS VENDOR3', 'TBL_TRN_VQTC01_HDR.VID_REF3','=','VENDOR3.SLID_REF')
                ->leftJoin('TBL_TRN_VDQT01_HDR AS QUOTATION1', 'TBL_TRN_VQTC01_HDR.VQID_REF1','=','QUOTATION1.VQID')
                ->leftJoin('TBL_TRN_VDQT01_HDR AS QUOTATION2', 'TBL_TRN_VQTC01_HDR.VQID_REF2','=','QUOTATION2.VQID')
                ->leftJoin('TBL_TRN_VDQT01_HDR AS QUOTATION3', 'TBL_TRN_VQTC01_HDR.VQID_REF3','=','QUOTATION3.VQID')
                ->select('TBL_TRN_VQTC01_HDR.*','VENDOR1.VCODE as VENDOR_CODE1','VENDOR1.NAME as VENDOR_NAME1','VENDOR2.VCODE as VENDOR_CODE2','VENDOR2.NAME as VENDOR_NAME2','VENDOR3.VCODE as VENDOR_CODE3','VENDOR3.NAME as VENDOR_NAME3','QUOTATION1.VQ_NO AS VQ_NO1','QUOTATION2.VQ_NO AS VQ_NO2','QUOTATION3.VQ_NO AS VQ_NO3')
                ->first();

               // dd($objMstResponse); 

            $objList1 = DB::table('TBL_TRN_VQTC01_PRC')                    
                ->where('TBL_TRN_VQTC01_PRC.VQCID_REF','=',$id) 
                ->leftJoin('TBL_MST_ITEM','TBL_TRN_VQTC01_PRC.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                
                ->leftJoin('TBL_MST_UOM','TBL_TRN_VQTC01_PRC.UOMID_REF','=','TBL_MST_UOM.UOMID')                
                ->select( 
                    'TBL_TRN_VQTC01_PRC.*',
                    'TBL_MST_ITEM.ITEMID',
                    'TBL_MST_ITEM.ICODE',
                    'TBL_MST_ITEM.NAME',
                    'TBL_MST_ITEM.ALPS_PART_NO',
                    'TBL_MST_ITEM.CUSTOMER_PART_NO',
                    'TBL_MST_ITEM.OEM_PART_NO',
                    'TBL_MST_UOM.UOMID',
                    'TBL_MST_UOM.UOMCODE',
                    'TBL_MST_UOM.DESCRIPTIONS',                )
                ->orderBy('TBL_TRN_VQTC01_PRC.PRCID','ASC')
                ->get()->toArray();

                //dd($objList1); 


                $objList2 = DB::table('TBL_TRN_VQTC01_TNC')                    
                ->where('TBL_TRN_VQTC01_TNC.VQCID_REF','=',$id)   
                ->leftJoin('TBL_MST_TNC_DETAILS','TBL_TRN_VQTC01_TNC.TNCID_REF','=','TBL_MST_TNC_DETAILS.TNCDID')           
                ->select( 
                    'TBL_MST_TNC_DETAILS.TNC_NAME',           
                    'TBL_TRN_VQTC01_TNC.*',
                )
                ->orderBy('TBL_TRN_VQTC01_TNC.VQCTNCID','ASC')
                ->get()->toArray();
                    

       
                $objList3 = DB::table('TBL_TRN_VQTC01_CAL')                    
                ->where('TBL_TRN_VQTC01_CAL.VQCID_REF','=',$id)   
                ->leftJoin('TBL_MST_CALCULATIONTEMPLATE','TBL_TRN_VQTC01_CAL.TID_REF','=','TBL_MST_CALCULATIONTEMPLATE.TID')           
                ->select( 
                    'TBL_MST_CALCULATIONTEMPLATE.COMPONENT',           
                    'TBL_TRN_VQTC01_CAL.*',          
                )
                ->orderBy('TBL_TRN_VQTC01_CAL.VQCCALID','ASC')
                ->get()->toArray();




                $objlastVQC_DT = DB::select('SELECT MAX(VQC_DT) VQC_DT FROM TBL_TRN_VQTC01_HDR  
                WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
                [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);

            //     DD($objlastVQC_DT[0]->VQC_DT); 
                    
            $FormId  = $this->form_id;


            $objCOMPANY = DB::table('TBL_MST_COMPANY')
            ->where('CYID','=',$CYID_REF)
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_COMPANY.*')
            ->first();

            $AlpsStatus =   $this->AlpsStatus();
            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');


        return view($this->view.'view', compact(['AlpsStatus','FormId','objRights','objlastVQC_DT','objMstResponse','objList1','objList2','objList3','objCOMPANY','TabSetting']));     
        }
    
   } 

    
   
   public function update(Request $request){

       
    $r_count1 = count($request['rowscount1']);
    if(isset($request['rowscount2'])){
    $r_count2 = count($request['rowscount2']);
    }
    if(isset($request['rowscount3'])){
    $r_count3 = count($request['rowscount3']);
    }



    
    for ($i=0; $i<=$r_count1; $i++)
    {
        if(isset($request['ITEMID_REF'][$i]))
        {
            $req_data[$i] = [
                'ITEMID_REF'    => $request['ITEMID_REF'][$i],
                'UOMID_REF' => $request['UOMID_REF'][$i],
                'DESCRIPTIONS' => $request['ITEMNAME'][$i],     
                'QID_REF' => $request['VID_REF'][$i],     
                'V1_QTY' => (!empty($request['QTY1'][$i]) ? $request['QTY1'][$i] : 0),
                'V1_RATE' => (!empty($request['RATE1'][$i]) ? $request['RATE1'][$i] : 0),
                'V2_QTY' => (!empty($request['QTY2'][$i]) ? $request['QTY2'][$i] : 0),
                'V2_RATE' => (!empty($request['RATE2'][$i]) ? $request['RATE2'][$i] : 0),
                'V3_QTY' => (!empty($request['QTY3'][$i]) ? $request['QTY3'][$i] : 0),
                'V3_RATE' => (!empty($request['RATE3'][$i]) ? $request['RATE3'][$i] : 0),
             
            ];
        }
    }



        $wrapped_links["PRC"] = $req_data; 
        $XMLPRC = ArrayToXml::convert($wrapped_links);



        if(isset($request['rowscount2'])){
    for ($i=0; $i<=$r_count2; $i++)
    {
            if(isset($request['TNCID_REF'][$i]) && !is_null($request['TNCID_REF'][$i]))
            {
                if(isset($request['TNCID_REF'][$i]))
                {
                    $reqdata2[$i] = [
                      
                        'TNCID_REF'    => $request['TNCID_REF'][$i],   
                        'QID_REF' => $request['VID_REF_TNC'][$i],                    
                        'V1_VALUE' => (!empty($request['VALUE1'][$i]) ? $request['VALUE1'][$i] : 0),
                        'V2_VALUE' => (!empty($request['VALUE2'][$i]) ? $request['VALUE2'][$i] : 0),
                        'V3_VALUE' => (!empty($request['VALUE3'][$i]) ? $request['VALUE3'][$i] : 0),
                        
                  
                    ];
                }
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
  
       if(isset($request['rowscount3'])){


        //dd($r_count3);
    for ($i=0; $i<=$r_count3; $i++)
    {
            if(isset($request['CTID_REF'][$i]) && !is_null($request['CTID_REF'][$i]))
            {
                if(isset($request['CTID_REF'][$i]))
                {
                    $reqdata3[$i] = [
                      
                        'TID_REF'    => $request['CTID_REF'][$i],    
                        'QID_REF' => $request['VID_REF_CAL'][$i],                   
                        'V1_RATE' => (!empty($request['RATECAL1'][$i]) ? $request['RATECAL1'][$i] : 0),
                        'V1_VALUE' => (!empty($request['AMOUNTCAL1'][$i]) ? $request['AMOUNTCAL1'][$i] : 0),
                        'V2_RATE' => (!empty($request['RATECAL2'][$i]) ? $request['RATECAL2'][$i] : 0),
                        'V2_VALUE' => (!empty($request['AMOUNTCAL2'][$i]) ? $request['AMOUNTCAL2'][$i] : 0),
                        'V3_RATE' => (!empty($request['RATECAL3'][$i]) ? $request['RATECAL3'][$i] : 0),
                        'V3_VALUE' => (!empty($request['AMOUNTCAL3'][$i]) ? $request['AMOUNTCAL3'][$i] : 0)                     

                  
                    ];
                }
            }
        
    }
}
   // }


//dd($reqdata3);


       if(isset($reqdata3))
       { 
        $wrapped_links3["CAL"] = $reqdata3;
        $XMLCAL = ArrayToXml::convert($wrapped_links3);
       }
       else
       {
        $XMLCAL = NULL; 
       }   
    
        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $VQC_NO = strtoupper($request['VQC_NO']);
        $VQC_DT = $request['VQC_DT'];

        $VID_REF1 = $request['VID_REF1'];
        $VQID_REF1 = $request['VQID_REF1'];
        $VID_REF2 = $request['VID_REF2'];
        $VQID_REF2 = $request['VQID_REF2'];
        $VID_REF3 = $request['VID_REF3'];
        $VQID_REF3 = $request['VQID_REF3'];

        $log_data = [ 
            $VQC_NO,$VQC_DT,$VID_REF1,$VQID_REF1,$VID_REF2,$VQID_REF2,$VID_REF3,$VQID_REF3,$CYID_REF, $BRID_REF,$FYID_REF,$VTID_REF,$XMLPRC,$XMLTNC,$XMLCAL, $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
        ];

      // dd($log_data); 

            $sp_result = DB::select('EXEC SP_VQC_UP ?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?, ?,?,?,?', $log_data);      
            
        
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $VQC_NO. ' Sucessfully Updated.']);

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
           
         
    $r_count1 = count($request['rowscount1']);
    if(isset($request['rowscount2'])){
    $r_count2 = count($request['rowscount2']);
    }
    if(isset($request['rowscount3'])){
        $r_count3 = count($request['rowscount3']);
    }



    
    for ($i=0; $i<=$r_count1; $i++)
    {
        if(isset($request['ITEMID_REF'][$i]))
        {
            $req_data[$i] = [
                'ITEMID_REF'    => $request['ITEMID_REF'][$i],
                'UOMID_REF' => $request['UOMID_REF'][$i],
                'DESCRIPTIONS' => $request['ITEMNAME'][$i],     
                'QID_REF' => $request['VID_REF'][$i],     
                'V1_QTY' => (!empty($request['QTY1'][$i]) ? $request['QTY1'][$i] : 0),
                'V1_RATE' => (!empty($request['RATE1'][$i]) ? $request['RATE1'][$i] : 0),
                'V2_QTY' => (!empty($request['QTY2'][$i]) ? $request['QTY2'][$i] : 0),
                'V2_RATE' => (!empty($request['RATE2'][$i]) ? $request['RATE2'][$i] : 0),
                'V3_QTY' => (!empty($request['QTY3'][$i]) ? $request['QTY3'][$i] : 0),
                'V3_RATE' => (!empty($request['RATE3'][$i]) ? $request['RATE3'][$i] : 0),
             
            ];
        }
    }



        $wrapped_links["PRC"] = $req_data; 
        $XMLPRC = ArrayToXml::convert($wrapped_links);



        if(isset($request['rowscount2'])){
    for ($i=0; $i<=$r_count2; $i++)
    {
            if(isset($request['TNCID_REF'][$i]) && !is_null($request['TNCID_REF'][$i]))
            {
                if(isset($request['TNCID_REF'][$i]))
                {
                    $reqdata2[$i] = [
                      
                        'TNCID_REF'    => $request['TNCID_REF'][$i],   
                        'QID_REF' => $request['VID_REF_TNC'][$i],                    
                        'V1_VALUE' => (!empty($request['VALUE1'][$i]) ? $request['VALUE1'][$i] : 0),
                        'V2_VALUE' => (!empty($request['VALUE2'][$i]) ? $request['VALUE2'][$i] : 0),
                        'V3_VALUE' => (!empty($request['VALUE3'][$i]) ? $request['VALUE3'][$i] : 0),
                        
                  
                    ];
                }
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
  
       if(isset($request['rowscount3'])){


        //dd($r_count3);
    for ($i=0; $i<=$r_count3; $i++)
    {
            if(isset($request['CTID_REF'][$i]) && !is_null($request['CTID_REF'][$i]))
            {
                if(isset($request['CTID_REF'][$i]))
                {
                    $reqdata3[$i] = [
                      
                        'TID_REF'    => $request['CTID_REF'][$i],    
                        'QID_REF' => $request['VID_REF_CAL'][$i],                   
                        'V1_RATE' => (!empty($request['RATECAL1'][$i]) ? $request['RATECAL1'][$i] : 0),
                        'V1_VALUE' => (!empty($request['AMOUNTCAL1'][$i]) ? $request['AMOUNTCAL1'][$i] : 0),
                        'V2_RATE' => (!empty($request['RATECAL2'][$i]) ? $request['RATECAL2'][$i] : 0),
                        'V2_VALUE' => (!empty($request['AMOUNTCAL2'][$i]) ? $request['AMOUNTCAL2'][$i] : 0),
                        'V3_RATE' => (!empty($request['RATECAL3'][$i]) ? $request['RATECAL3'][$i] : 0),
                        'V3_VALUE' => (!empty($request['AMOUNTCAL3'][$i]) ? $request['AMOUNTCAL3'][$i] : 0)                     

                  
                    ];
                }
            }
        
    }
}
   // }


//dd($reqdata3);


       if(isset($reqdata3))
       { 
        $wrapped_links3["CAL"] = $reqdata3;
        $XMLCAL = ArrayToXml::convert($wrapped_links3);
       }
       else
       {
        $XMLCAL = NULL; 
       }   
    
        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $VQC_NO = strtoupper($request['VQC_NO']);
        $VQC_DT = $request['VQC_DT'];

        $VID_REF1 = $request['VID_REF1'];
        $VQID_REF1 = $request['VQID_REF1'];
        $VID_REF2 = $request['VID_REF2'];
        $VQID_REF2 = $request['VQID_REF2'];
        $VID_REF3 = $request['VID_REF3'];
        $VQID_REF3 = $request['VQID_REF3'];

        $log_data = [ 
            $VQC_NO,$VQC_DT,$VID_REF1,$VQID_REF1,$VID_REF2,$VQID_REF2,$VID_REF3,$VQID_REF3,$CYID_REF, $BRID_REF,$FYID_REF,$VTID_REF,$XMLPRC,$XMLTNC,$XMLCAL, $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
        ];

      // dd($log_data); 

            $sp_result = DB::select('EXEC SP_VQC_UP ?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?, ?,?,?,?', $log_data);           
          
             
                
            
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $VQC_NO. ' Sucessfully Approved.']);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
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
                $TABLE      =   "TBL_TRN_VQTC01_HDR";
                $FIELD      =   "VQCID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
            
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_VQ ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
    
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
        $TABLE      =   "TBL_TRN_VQTC01_HDR";
        $FIELD      =   "VQCID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_VQTC01_PRC',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_VQTC01_TNC',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_VQTC01_CAL',
        ];
 
       
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

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
        
		$image_path         =   "docs/company".$CYID_REF."/VendorQuotationComparison";     
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

    public function checkvqc(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $VQC_NO = $request->VQC_NO;
        //dd($VQC_NO); 
        
        $objVQC = DB::table('TBL_TRN_VQTC01_HDR')
        ->where('TBL_TRN_VQTC01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_VQTC01_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_VQTC01_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_VQTC01_HDR.VQC_NO','=',$VQC_NO)
        ->select('TBL_TRN_VQTC01_HDR.VQCID')
        ->first();
        
        if($objVQC){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate SONO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }

    


   

    
}
