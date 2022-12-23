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

class TrnFrm313Controller extends Controller{

    protected $form_id  = 313;
    protected $vtid_ref = 401;
    protected $view     = "transactions.inventory.KnowkOff.trnfrm313";
   
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];
    
    public function __construct(){
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

        $objDataList	=	DB::select("select hdr.*,
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
                            inner join TBL_TRN_KNOWKOFF_HDR hdr
                            on a.VID = hdr.KNOWOFFID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF 
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.KNOWOFFID DESC ");

        return view($this->view,compact(['FormId','objRights','objDataList']));
    }

    public function add(){       
        
        $FormId         =   $this->form_id;
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $Status         =   'A';	

        $objSON = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('FYID_REF','=',$FYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_DOCNO_DEFINITION.*')
        ->first();

        $objDataNo   =   NULL;

        if( isset($objSON->SYSTEM_GRSR) && $objSON->SYSTEM_GRSR == "1")
        {
            if($objSON->PREFIX_RQ == "1")
            {
                $objDataNo = $objSON->PREFIX;
            }        
            if($objSON->PRE_SEP_RQ == "1")
            {
                if($objSON->PRE_SEP_SLASH == "1")
                {
                $objDataNo = $objDataNo.'/';
                }
                if($objSON->PRE_SEP_HYPEN == "1")
                {
                $objDataNo = $objDataNo.'-';
                }
            }        
            if($objSON->NO_MAX)
            {   
                $objDataNo = $objDataNo.str_pad($objSON->LAST_RECORDNO+1, $objSON->NO_MAX, "0", STR_PAD_LEFT);
            }
            
            if($objSON->NO_SEP_RQ == "1")
            {
                if($objSON->NO_SEP_SLASH == "1")
                {
                $objDataNo = $objDataNo.'/';
                }
                if($objSON->NO_SEP_HYPEN == "1")
                {
                $objDataNo = $objDataNo.'-';
                }
            }
            if($objSON->SUFFIX_RQ == "1")
            {
                $objDataNo = $objDataNo.$objSON->SUFFIX;
            }
        }

        $objlastdt          =   $this->getLastdt();
               
       
        return view($this->view.'add', compact(['FormId','objSON','objDataNo','objlastdt']));       
    }

    public function getLastdt(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        return  DB::select('SELECT MAX(KNOCKOFF_DT) KNOCKOFF_DT FROM TBL_TRN_KNOWKOFF_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF, $FYID_REF, $this->vtid_ref, 'A' ]);
    }

    public function getCustomerVendor(Request $request){
       
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $CODE       =   $request['CODE'];
        $NAME       =   $request['NAME'];
        $TYPE       =   $request['VENDOR_CUSTOMER'];

        $sp_popup   =   [$CYID_REF, $BRID_REF,$CODE,$NAME]; 

        if($TYPE =="VENDOR"){
            $ObjData = DB::select('EXEC sp_get_vendor_popup_enquiry ?,?,?,?', $sp_popup);
        }
        else{
            $ObjData = DB::select('EXEC sp_get_customer_popup_enquiry ?,?,?,?', $sp_popup);
        }
    
        if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){
            
                echo '<tr>';
                echo '<td style="text-align:center; width:10%"><input type="checkbox" name="SELECT_VID_CID_REF[]"  id="custid_'.$dataRow->SGLID.'" class="clscustid" value="'.$dataRow->SGLID.'"/></td>'; 
                echo '<td style="width:30%;">'.$dataRow->SGLCODE.'<input type="hidden" id="txtcustid_'.$dataRow->SGLID.'" data-desc="'.$dataRow->SGLCODE .'" data-desc2="'.$dataRow->SLNAME .'" value="'.$dataRow->SGLID.'"/></td>';
                echo '<td style="width:60%;">'.$dataRow->SLNAME.'</td>';
                echo '</tr>';
            }
    
        }else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
        }
        
        exit();
    
    }

    public function getKnowkOffDetails(Request $request){
            
        $CYID_REF           =   Auth::user()->CYID_REF;
        $BRID_REF           =   Session::get('BRID_REF');
        $VID_CID_REF        =   $request['VID_CID_REF'];
        $VENDOR_CUSTOMER    =   $request['VENDOR_CUSTOMER'];
        $CENTRALIZED        =   0;

        $sp_param           =   [$VENDOR_CUSTOMER,$VID_CID_REF,$CENTRALIZED,$BRID_REF,$CYID_REF];  
        $ObjData            =   DB::select('EXEC SP_TRN_GET_INVOICE_CUST_VENDOR_WISE_RECONCILE ?,?,?,?,?', $sp_param);

        if(isset($ObjData) && !empty($ObjData)){
                
            foreach ($ObjData as $index=>$dataRow){

               $RECONCIL_AMT   =   DB::select("SELECT SUM(RECONCIL_AMT) AS RECONCIL_AMOUNT FROM TBL_TRN_KNOWKOFF_MAT WHERE VTID_REF='$dataRow->ID'
												AND DOC_TYPE ='$dataRow->SOURCETYPE'")[0]->RECONCIL_AMOUNT;
                if($RECONCIL_AMT != $dataRow->BALANCEAMT){
                    $dataRow->BALANCEAMT =   $dataRow->BALANCEAMT;

                    echo '<tr class="participantRow">';
                    echo '<td><input type="checkbox"  name="DOC_ID[]"                  id="DOC_ID_'.$index.'"         value="'.$index.'" onchange="getDocId(this.id)" ></td>';
                    echo '<td><input type="text"      name="DOC_TYPE_'.$index.'"       id="DOC_TYPE_'.$index.'"       value="'.$dataRow->SOURCETYPE.'"  class="form-control"  autocomplete="off" readonly /></td>';
                    echo '<td><input type="text"      name="DOC_NO_'.$index.'"         id="DOC_NO_'.$index.'"         value="'.$dataRow->DOCNO.'"       class="form-control"  autocomplete="off" readonly /></td>';
                    echo '<td><input type="text"      name="DOC_DT_'.$index.'"         id="DOC_DT_'.$index.'"         value="'.$dataRow->DOCDT.'"       class="form-control"  autocomplete="off" readonly /></td>';
                    echo '<td><input type="text"      name="AMOUNT_'.$index.'"         id="AMOUNT_'.$index.'"         value="'.$dataRow->DOCAMT.'"      class="form-control"  autocomplete="off" readonly /></td>';
                    echo '<td><input type="text"      name="BAL_AMOUNT_'.$index.'"     id="BAL_AMOUNT_'.$index.'"     value="'.$dataRow->BALANCEAMT.'"  class="form-control"  autocomplete="off" readonly ></td>';
                    echo '<td><input type="text"      name="RECONCIL_AMT_'.$index.'"   id="RECONCIL_AMT_'.$index.'"                                     class="form-control"  autocomplete="off" readonly onkeypress="return isNumberDecimalKey(event,this)" /></td>';
                    echo '<td hidden><input type="text" name="VTID_REF_'.$index.'"     id="VTID_REF_'.$index.'"       value="'.$dataRow->ID.'" /></td>';
                    echo '</tr>';
                }
                
            }
        }
        else{

            echo '<tr class="participantRow">';
            echo '<td><input type="checkbox"  name="DOC_ID[]"         id="DOC_ID_0"        disabled></td>';
            echo '<td><input type="text"      name="DOC_TYPE_0"       id="DOC_TYPE_0"      class="form-control"  autocomplete="off" readonly /></td>';
            echo '<td><input type="text"      name="DOC_NO_0"         id="DOC_NO_0"        class="form-control"  autocomplete="off" readonly /></td>';
            echo '<td><input type="text"      name="DOC_DT_0"         id="DOC_DT_0"        class="form-control"  autocomplete="off" readonly /></td>';
            echo '<td><input type="text"      name="AMOUNT_0"         id="AMOUNT_0"        class="form-control"  autocomplete="off" readonly /></td>';
            echo '<td><input type="text"      name="BAL_AMOUNT_0"     id="BAL_AMOUNT_0"    class="form-control"  autocomplete="off" readonly ></td>';
            echo '<td><input type="text"      name="RECONCIL_AMT_0"   id="RECONCIL_AMT_0"  class="form-control"  autocomplete="off" readonly /></td>';
            echo '</tr>';
        }
        
        exit();
        
    }

    public function getKnowkOffDetailsEdit(Request $request){
            
        $CYID_REF           =   Auth::user()->CYID_REF;
        $BRID_REF           =   Session::get('BRID_REF');
        $VID_CID_REF        =   $request['VID_CID_REF'];
        $VENDOR_CUSTOMER    =   $request['VENDOR_CUSTOMER'];
        $KNOWOFFID          =   $request['KNOWOFFID'];
        $ActionStatus       =   $request['ActionStatus'];
        $CENTRALIZED        =   0;

        

        $sp_param           =   [$VENDOR_CUSTOMER,$VID_CID_REF,$CENTRALIZED,$BRID_REF,$CYID_REF,$KNOWOFFID];  
        $ObjData            =   DB::select('EXEC SP_TRN_GET_INVOICE_CUST_VENDOR_WISE_RECONCILE_EDIT ?,?,?,?,?,?', $sp_param);

        if(isset($KNOWOFFID) && $KNOWOFFID !=""){
                
            foreach ($ObjData as $index=>$dataRow){

                $KNOWKOFF_MAT   =   DB::table('TBL_TRN_KNOWKOFF_MAT')
                                    ->where('KNOWOFFID_REF','=',$KNOWOFFID)
                                    ->where('VTID_REF','=',$dataRow->ID)
									->where('DOC_TYPE','=',$dataRow->SOURCETYPE)
                                    ->select('RECONCIL_AMT')
                                    ->first();

                $RECONCIL_AMT   = isset($KNOWKOFF_MAT->RECONCIL_AMT) && $KNOWKOFF_MAT->RECONCIL_AMT !=""?$KNOWKOFF_MAT->RECONCIL_AMT:'';
                $checked        = isset($KNOWKOFF_MAT->RECONCIL_AMT) && $KNOWKOFF_MAT->RECONCIL_AMT !=""?'checked':'';
                $readonly       = isset($KNOWKOFF_MAT->RECONCIL_AMT) && $KNOWKOFF_MAT->RECONCIL_AMT !=""?'':'readonly';

               
                    echo '<tr class="participantRow">';
                    echo '<td><input type="checkbox"  name="DOC_ID[]"                  id="DOC_ID_'.$index.'"         value="'.$index.'" onchange="getDocId(this.id)" '.$checked.' '.$ActionStatus.' ></td>';
                    echo '<td><input type="text"      name="DOC_TYPE_'.$index.'"       id="DOC_TYPE_'.$index.'"       value="'.$dataRow->SOURCETYPE.'"  class="form-control"  autocomplete="off" readonly /></td>';
                    echo '<td><input type="text"      name="DOC_NO_'.$index.'"         id="DOC_NO_'.$index.'"         value="'.$dataRow->DOCNO.'"       class="form-control"  autocomplete="off" readonly /></td>';
                    echo '<td><input type="text"      name="DOC_DT_'.$index.'"         id="DOC_DT_'.$index.'"         value="'.$dataRow->DOCDT.'"       class="form-control"  autocomplete="off" readonly /></td>';
                    echo '<td><input type="text"      name="AMOUNT_'.$index.'"         id="AMOUNT_'.$index.'"         value="'.$dataRow->DOCAMT.'"      class="form-control"  autocomplete="off" readonly /></td>';
                    echo '<td><input type="text"      name="BAL_AMOUNT_'.$index.'"     id="BAL_AMOUNT_'.$index.'"     value="'.$dataRow->BALANCEAMT.'"  class="form-control"  autocomplete="off" readonly ></td>';
                    echo '<td><input type="text"      name="RECONCIL_AMT_'.$index.'"   id="RECONCIL_AMT_'.$index.'"   value="'.$RECONCIL_AMT.'"  class="form-control"  autocomplete="off" '.$readonly.' onkeypress="return isNumberDecimalKey(event,this)" '.$ActionStatus.' /></td>';
                    echo '<td hidden><input type="text" name="VTID_REF_'.$index.'"     id="VTID_REF_'.$index.'"       value="'.$dataRow->ID.'" /></td>';
                    echo '</tr>';
                 
                
            }
        }
        else{

            echo '<tr class="participantRow">';
            echo '<td><input type="checkbox"  name="DOC_ID[]"         id="DOC_ID_0"        disabled></td>';
            echo '<td><input type="text"      name="DOC_TYPE_0"       id="DOC_TYPE_0"      class="form-control"  autocomplete="off" readonly /></td>';
            echo '<td><input type="text"      name="DOC_NO_0"         id="DOC_NO_0"        class="form-control"  autocomplete="off" readonly /></td>';
            echo '<td><input type="text"      name="DOC_DT_0"         id="DOC_DT_0"        class="form-control"  autocomplete="off" readonly /></td>';
            echo '<td><input type="text"      name="AMOUNT_0"         id="AMOUNT_0"        class="form-control"  autocomplete="off" readonly /></td>';
            echo '<td><input type="text"      name="BAL_AMOUNT_0"     id="BAL_AMOUNT_0"    class="form-control"  autocomplete="off" readonly ></td>';
            echo '<td><input type="text"      name="RECONCIL_AMT_0"   id="RECONCIL_AMT_0"  class="form-control"  autocomplete="off" readonly /></td>';
            echo '</tr>';
        }
        
        exit();
        
    }

    public function save(Request $request) {
    
        $req_data=array();
        foreach($request['DOC_ID'] as $i){

            if(isset($request['DOC_TYPE_'.$i])){
                $req_data[$i] = [
                    'VTID_RER'      => $request['VTID_REF_'.$i] !=""?$request['VTID_REF_'.$i]:NULL,
                    'DOC_TYPE'      => $request['DOC_TYPE_'.$i] !=""?$request['DOC_TYPE_'.$i]:NULL,
                    'DOC_NO'        => $request['DOC_NO_'.$i] !=""?$request['DOC_NO_'.$i]:NULL,
                    'DOC_DT'        => $request['DOC_DT_'.$i] !=""?$request['DOC_DT_'.$i]:NULL,
                    'AMOUNT'        => $request['AMOUNT_'.$i] !=""?$request['AMOUNT_'.$i]:0,
                    'BAL_AMOUNT'    => $request['BAL_AMOUNT_'.$i] !=""?$request['BAL_AMOUNT_'.$i]:0,
                    'RECONCIL_AMT'  => $request['RECONCIL_AMT_'.$i] !=""?$request['RECONCIL_AMT_'.$i]:0,
                ];
            }
        }

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);

        $VTID_REF   =   $this->vtid_ref;
        $USERID_REF =   Auth::user()->USERID;   
        $ACTIONNAME =   'ADD';
        $IPADDRESS  =   $request->getClientIp();
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $KNOCKOFF_NO    =   isset($request['KNOCKOFF_NO'])?$request['KNOCKOFF_NO']:NULL;
        $KNOCKOFF_DT    =   isset($request['KNOCKOFF_DT'])?$request['KNOCKOFF_DT']:NULL;
        $VENDOR_CUSTOMER    =   isset($request['VENDOR_CUSTOMER'])?$request['VENDOR_CUSTOMER']:NULL;
        $VID_CID_REF        =   isset($request['VID_CID_REF'])?$request['VID_CID_REF']:NULL;
        $TOTAL_AMOUNT       =   isset($request['tot_amt1'])?$request['tot_amt1']:NULL;

        $log_data = [ 
            $KNOCKOFF_NO,           $KNOCKOFF_DT,       $VENDOR_CUSTOMER,       $VID_CID_REF,       $TOTAL_AMOUNT,      
            $CYID_REF,              $BRID_REF,          $FYID_REF,              $VTID_REF,          $XMLMAT,            
            $USERID_REF,            Date('Y-m-d'),      Date('h:i:s.u'),        $ACTIONNAME,        $IPADDRESS    
        ];

        $sp_result = DB::select('EXEC SP_KNOWKOFF_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $log_data); 
        
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
        
            $Status         =   "A";
            $CYID_REF       =   Auth::user()->CYID_REF;
            $BRID_REF       =   Session::get('BRID_REF');
            $FYID_REF       =   Session::get('FYID_REF');
            $FormId         =   $this->form_id;

            $objRights      =   DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                                
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();

            $objlastdt      =   $this->getLastdt();

            $HDR            =   DB::table('TBL_TRN_KNOWKOFF_HDR')->where('KNOWOFFID','=',$id)->first();

            $CustomerVendor =   array();
            if(isset($HDR->VID_CID_REF) && $HDR->VID_CID_REF !=""){

                $BELONGS_TO =   $HDR->VENDOR_CUSTOMER =='VENDOR'?'Vendor':'Customer';

                $CustomerVendor = DB::table('TBL_MST_SUBLEDGER')
                ->where('BELONGS_TO','=',$BELONGS_TO)
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('SGLID','=',$HDR->VID_CID_REF)    
                ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                ->first();
            }

            $ActionStatus   =   "";

            return view($this->view.'edit', compact(['FormId','objRights','objlastdt','HDR','CustomerVendor','ActionStatus']));     
        
        }
    }

    public function view($id=NULL){  
         
        if(!is_null($id)){
        
            $Status         =   "A";
            $CYID_REF       =   Auth::user()->CYID_REF;
            $BRID_REF       =   Session::get('BRID_REF');
            $FYID_REF       =   Session::get('FYID_REF');
            $FormId         =   $this->form_id;

            $objRights      =   DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                                
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();

            $objlastdt      =   $this->getLastdt();

            $HDR            =   DB::table('TBL_TRN_KNOWKOFF_HDR')->where('KNOWOFFID','=',$id)->first();

            $CustomerVendor =   array();
            if(isset($HDR->VID_CID_REF) && $HDR->VID_CID_REF !=""){

                $BELONGS_TO =   $HDR->VENDOR_CUSTOMER =='VENDOR'?'Vendor':'Customer';

                $CustomerVendor = DB::table('TBL_MST_SUBLEDGER')
                ->where('BELONGS_TO','=',$BELONGS_TO)
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('SGLID','=',$HDR->VID_CID_REF)    
                ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                ->first();
            }

            $ActionStatus   =   "disabled";

            return view($this->view.'view', compact(['FormId','objRights','objlastdt','HDR','CustomerVendor','ActionStatus']));     
        
        }
    }


    public function update(Request $request){

        $req_data=array();
        foreach($request['DOC_ID'] as $i){

            if(isset($request['DOC_TYPE_'.$i])){
                $req_data[$i] = [
                    'VTID_RER'      => $request['VTID_REF_'.$i] !=""?$request['VTID_REF_'.$i]:NULL,
                    'DOC_TYPE'      => $request['DOC_TYPE_'.$i] !=""?$request['DOC_TYPE_'.$i]:NULL,
                    'DOC_NO'        => $request['DOC_NO_'.$i] !=""?$request['DOC_NO_'.$i]:NULL,
                    'DOC_DT'        => $request['DOC_DT_'.$i] !=""?$request['DOC_DT_'.$i]:NULL,
                    'AMOUNT'        => $request['AMOUNT_'.$i] !=""?$request['AMOUNT_'.$i]:0,
                    'BAL_AMOUNT'    => $request['BAL_AMOUNT_'.$i] !=""?$request['BAL_AMOUNT_'.$i]:0,
                    'RECONCIL_AMT'  => $request['RECONCIL_AMT_'.$i] !=""?$request['RECONCIL_AMT_'.$i]:0,
                ];
            }
        }

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);


        $VTID_REF   =   $this->vtid_ref;
        $USERID_REF =   Auth::user()->USERID;   
        $ACTIONNAME =   'EDIT';
        $IPADDRESS  =   $request->getClientIp();
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $KNOCKOFF_NO    =   isset($request['KNOCKOFF_NO'])?$request['KNOCKOFF_NO']:NULL;
        $KNOCKOFF_DT    =   isset($request['KNOCKOFF_DT'])?$request['KNOCKOFF_DT']:NULL;
        $VENDOR_CUSTOMER    =   isset($request['VENDOR_CUSTOMER'])?$request['VENDOR_CUSTOMER']:NULL;
        $VID_CID_REF        =   isset($request['VID_CID_REF'])?$request['VID_CID_REF']:NULL;
        $TOTAL_AMOUNT       =   isset($request['tot_amt1'])?$request['tot_amt1']:NULL;

        $log_data = [ 
            $KNOCKOFF_NO,           $KNOCKOFF_DT,       $VENDOR_CUSTOMER,       $VID_CID_REF,       $TOTAL_AMOUNT,      
            $CYID_REF,              $BRID_REF,          $FYID_REF,              $VTID_REF,          $XMLMAT,            
            $USERID_REF,            Date('Y-m-d'),      Date('h:i:s.u'),        $ACTIONNAME,        $IPADDRESS    
        ];

        $sp_result = DB::select('EXEC SP_KNOWKOFF_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $log_data);   
         
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' =>' Sucessfully Updated.']);
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
           
        $req_data=array();
        foreach($request['DOC_ID'] as $i){

            if(isset($request['DOC_TYPE_'.$i])){
                $req_data[$i] = [
                    'VTID_RER'      => $request['VTID_REF_'.$i] !=""?$request['VTID_REF_'.$i]:NULL,
                    'DOC_TYPE'      => $request['DOC_TYPE_'.$i] !=""?$request['DOC_TYPE_'.$i]:NULL,
                    'DOC_NO'        => $request['DOC_NO_'.$i] !=""?$request['DOC_NO_'.$i]:NULL,
                    'DOC_DT'        => $request['DOC_DT_'.$i] !=""?$request['DOC_DT_'.$i]:NULL,
                    'AMOUNT'        => $request['AMOUNT_'.$i] !=""?$request['AMOUNT_'.$i]:0,
                    'BAL_AMOUNT'    => $request['BAL_AMOUNT_'.$i] !=""?$request['BAL_AMOUNT_'.$i]:0,
                    'RECONCIL_AMT'  => $request['RECONCIL_AMT_'.$i] !=""?$request['RECONCIL_AMT_'.$i]:0,
                ];
            }
        }

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        

        $VTID_REF   =   $this->vtid_ref;
        $USERID_REF =   Auth::user()->USERID;   
        $ACTIONNAME =   $Approvallevel;
        $IPADDRESS  =   $request->getClientIp();
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');


        $KNOCKOFF_NO    =   isset($request['KNOCKOFF_NO'])?$request['KNOCKOFF_NO']:NULL;
        $KNOCKOFF_DT    =   isset($request['KNOCKOFF_DT'])?$request['KNOCKOFF_DT']:NULL;
        $VENDOR_CUSTOMER    =   isset($request['VENDOR_CUSTOMER'])?$request['VENDOR_CUSTOMER']:NULL;
        $VID_CID_REF        =   isset($request['VID_CID_REF'])?$request['VID_CID_REF']:NULL;
        $TOTAL_AMOUNT       =   isset($request['tot_amt1'])?$request['tot_amt1']:NULL;

        $log_data = [ 
            $KNOCKOFF_NO,           $KNOCKOFF_DT,       $VENDOR_CUSTOMER,       $VID_CID_REF,       $TOTAL_AMOUNT,      
            $CYID_REF,              $BRID_REF,          $FYID_REF,              $VTID_REF,          $XMLMAT,            
            $USERID_REF,            Date('Y-m-d'),      Date('h:i:s.u'),        $ACTIONNAME,        $IPADDRESS    
        ];

        $sp_result = DB::select('EXEC SP_KNOWKOFF_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $log_data);     
         
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' =>' Sucessfully Approved.']);

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
                $TABLE      =   "TBL_TRN_KNOWKOFF_HDR";
                $FIELD      =   "KNOWOFFID";
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
        $TABLE      =   "TBL_TRN_KNOWKOFF_HDR";
        $FIELD      =   "KNOWOFFID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_KNOWKOFF_MAT',
        ];

        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $qualityinspection_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $qualityinspection_cancel_data);

        if($sp_result[0]->RESULT=="CANCELED"){  

            return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
        
            return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'invalid'=>'invalid']);
        }
        
        exit(); 
    }


    public function attachment($id){

        if(!is_null($id)){
        
            $FormId     =   $this->form_id;

            $objResponse = DB::table('TBL_TRN_KNOWKOFF_HDR')->where('KNOWOFFID','=',$id)->first();

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

            return view($this->view.'attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments']));
        }

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
        
		$image_path         =   "docs/company".$CYID_REF."/KnowkOff";     
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
    

    
}
