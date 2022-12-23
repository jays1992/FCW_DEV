<?php
namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm197;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;

class MstFrm197Controller extends Controller{
   
    protected $form_id      =   197;
    protected $vtid_ref     =   212;
    protected $view         =   "masters.Payroll.SalaryStructureMaster.mstfrm";

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){ 

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');   
        $FormId     =   $this->form_id;
        
        $objRights  =   DB::table('TBL_MST_USERROLMAP')
                        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
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
                            inner join TBL_MST_SALARY_STRUCTURE hdr
                            on a.VID = hdr.SALARY_STRUCID 
                            and a.VTID_REF = hdr.VTID_REF
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_MST_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.SALARY_STRUCID DESC ");
        
        /*
        $objDataList    =   DB::table('TBL_MST_SALARY_STRUCTURE')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('BRID_REF','=',Session::get('BRID_REF'))
                            ->where('FYID_REF','=',Session::get('FYID_REF'))
                            ->orderBy('SALARY_STRUCID','DESC')
                            ->get();*/
        

        return view($this->view.$FormId,compact(['objRights','objDataList','FormId']));
    }

    public function add(){ 

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $objSON = DB::table('TBL_MST_DOCNO_DEFINITION')
            ->where('VTID_REF','=',$this->vtid_ref)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('FYID_REF','=',$FYID_REF)
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_DOCNO_DEFINITION.*')
            ->first();

        $objAutoGenNo   =   '';
        if(isset($objSON) && !empty($objSON )){

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

        $FormId  = $this->form_id;
      
        return view($this->view.$FormId.'add',compact(['objSON','objAutoGenNo','FormId']));
    }

    public function get_earning_deduction_head(Request $request){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $fieldid        =   $request['fieldid'];
        $type           =   $request['type'];

        $ObjData        =   array();

        if($type =="EARNING"){

            $ObjData    =   DB::select("SELECT 
                            T1.EARNING_HEADID AS HEADID,
                            T1.EARNING_HEADCODE AS HEADCODE,
                            T1.EARNING_HEAD_DESC AS HEADDESC,
                            T2.EARNING_TYPEID AS TYPEID,
                            T2.EARNING_TYPECODE AS TYPECODE,
                            T2.EARNING_TYPE_DESC AS TYPEDESC
                            FROM TBL_MST_EARNING_HEAD T1
                            LEFT JOIN TBL_MST_EARNING_HEAD_TYPE T2 ON T1.EARNING_TYPEID_REF=T2.EARNING_TYPEID
                            WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' 
                            AND T1.STATUS='A' AND (T1.DEACTIVATED=0 or T1.DEACTIVATED is null)
                            ");
        }
        else if($type =="DEDUCTION"){

            $ObjData    =   DB::select("SELECT 
                            T1.DEDUCTION_HEADID AS HEADID,
                            T1.DEDUCTION_HEADCODE AS HEADCODE,
                            T1.DEDUCTION_HEAD_DESC AS HEADDESC,
                            T2.DEDUCTION_TYPEID AS TYPEID,
                            T2.DEDUCTION_TYPECODE AS TYPECODE,
                            T2.DEDUCTION_TYPE_DESC AS TYPEDESC
                            FROM TBL_MST_DEDUCTION_HEAD T1
                            LEFT JOIN TBL_MST_DEDUCTION_HEAD_TYPE T2 ON T1.DEDUCTION_TYPEID_REF=T2.DEDUCTION_TYPEID
                            WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' 
                            AND T1.STATUS='A' AND (T1.DEACTIVATED=0 or T1.DEACTIVATED is null)
                            ");

        }

        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){
              
                echo'
                    <tr>
                        <td class="ROW1"> <input type="checkbox" name="SELECT_EARNING_HEADID_REF_'.$fieldid.'[]" onclick=bind_data("'.$fieldid.'","#text_'.$index.'","'.$type.'") ></td>
                        <td class="ROW2">'.$dataRow->HEADCODE.' </td>
                        <td class="ROW3" >'.$dataRow->HEADDESC.'</td>
                        <td hidden>
                            <input type="hidden" id="text_'.$index.'" data-desc1="'.$dataRow->HEADID.'" data-desc2="'.$dataRow->HEADCODE.'" data-desc3="'.$dataRow->HEADDESC.'" data-desc4="'.$dataRow->TYPEID.'" data-desc5="'.$dataRow->TYPECODE.'" data-desc6="'.$dataRow->TYPEDESC.'" >
                        </td>
                    </tr>
                ';
            }

        }else{
            echo '<tr><td>Record not found.</td></tr>';
        }

        exit();   
    }

    public function save(Request $request){

        $r_count1 = $request['Row_Count1'];

        for ($i=0; $i<=$r_count1; $i++){

            if(isset($request['EARNING_HEADID_REF_'.$i]) && !is_null($request['EARNING_HEADID_REF_'.$i])){

                $req_data[$i] = [
                    'EARNING_HEADID_REF'    => $request['EARNING_HEADID_REF_'.$i],
                    'EARNING_TYPEID_REF'    => $request['EARNING_TYPEID_REF_'.$i],
                    'SQ_NO'                 => $request['SQ_NO_'.$i] , 
                    'AMT_FORMULA'           => $request['AMT_FORMULA_'.$i] ,     
                    'FORMULA'               => $request['FORMULA_'.$i] ,     
                    'AMOUNT'                => $request['AMOUNT_'.$i] ,     
                    'REMARKS'               => $request['REMARKS_'.$i] ,     
                    'HEAD_TYPE'             => $request['HEAD_TYPE_'.$i] ,         
                ];
            }
        }

        $wrapped_links["EARNING"]   =   $req_data; 
        $XMLEARNING                 =   ArrayToXml::convert($wrapped_links);

        $XMLDEDUCTION   = NULL;

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');       
        $VTID           =   $this->vtid_ref;
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();

        $SALARY_STRUC_NO    =   strtoupper(trim($request['SALARY_STRUC_NO']) );
        $SALARY_STRUC_DESC  =   trim($request['SALARY_STRUC_DESC']); 
        $DEACTIVATED        =   NULL;  
        $DODEACTIVATED      =   NULL; 
        
        $array_data     =   [
                                $SALARY_STRUC_NO,   $SALARY_STRUC_DESC, $DEACTIVATED,   $DODEACTIVATED,     $CYID_REF,
                                $BRID_REF,          $FYID_REF,          $XMLEARNING,    $XMLDEDUCTION,       $VTID, 
                                $USERID,            $UPDATE,            $UPTIME,        $ACTION,             $IPADDRESS
                            ];

        $sp_result = DB::select('EXEC SP_SALARY_STRUCTURE_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $array_data);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);
        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        
        exit();    
    }

    public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $SALARY_STRUC_NO =   $request['SALARY_STRUC_NO'];
        
        $objLabel = DB::table('TBL_MST_SALARY_STRUCTURE')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('SALARY_STRUC_NO','=',$SALARY_STRUC_NO)
        ->select('SALARY_STRUC_NO')
        ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }

    public function edit($id=NULL){

        $USERID     =   Auth::user()->USERID;
        $VTID       =   $this->vtid_ref;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');    
        $FYID_REF   =   Session::get('FYID_REF');

        $objRights  =   DB::table('TBL_MST_USERROLMAP')
                        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
                        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                        ->first(); 

        $HDR        =   array(); 
        $MAT        =   array();            

        if(!is_null($id)){
        
            $HDR    =   DB::table('TBL_MST_SALARY_STRUCTURE')
                        ->where('SALARY_STRUCID','=',$id)
                        ->first();

            $MAT_ARR=   DB::select("SELECT T1.*
                        FROM TBL_MST_SALARY_STRUCTURE_EARNING T1
                        WHERE T1.SALARY_STRUCID_REF='$id' ORDER BY T1.SALARY_EARNINGID ASC
                        ");

            if(isset($MAT_ARR) && !empty($MAT_ARR)){
                foreach($MAT_ARR as $key=>$val){

                    $data   =   $this->getEarningDeduction($val->HEAD_TYPE,$val->EARNING_HEADID_REF);

                    $MAT[]=array(
                        'SALARY_EARNINGID'=>$val->SALARY_EARNINGID,
                        'SALARY_STRUCID_REF'=>$val->SALARY_STRUCID_REF,
                        'EARNING_HEADID_REF'=>$val->EARNING_HEADID_REF,
                        'EARNING_TYPEID_REF'=>$val->EARNING_TYPEID_REF,
                        'SQ_NO'=>$val->SQ_NO,
                        'AMT_FORMULA'=>$val->AMT_FORMULA,
                        'FORMULA'=>$val->FORMULA,
                        'AMOUNT'=>$val->AMOUNT,
                        'REMARKS'=>$val->REMARKS,
                        'INDATE'=>$val->INDATE,
                        'HEAD_TYPE'=>$val->HEAD_TYPE,
                        'HEADCODE'=>$data->HEADCODE,
                        'HEADDESC'=>$data->HEADDESC,
                        'TYPECODE'=>$data->TYPECODE,
                        'TYPEDESC'=>$data->TYPEDESC,
                    );
                }

            }

        }

        $FormId  = $this->form_id;

        return view($this->view.$FormId.'edit',compact(['FormId','objRights','HDR','MAT']));

    }

    public function view($id=NULL){

        $USERID     =   Auth::user()->USERID;
        $VTID       =   $this->vtid_ref;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');    
        $FYID_REF   =   Session::get('FYID_REF');

        $objRights  =   DB::table('TBL_MST_USERROLMAP')
                        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
                        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                        ->first(); 

        $HDR        =   array(); 
        $MAT        =   array();            

        if(!is_null($id)){
        
            $HDR    =   DB::table('TBL_MST_SALARY_STRUCTURE')
                        ->where('SALARY_STRUCID','=',$id)
                        ->first();

            $MAT_ARR=   DB::select("SELECT T1.*
                        FROM TBL_MST_SALARY_STRUCTURE_EARNING T1
                        WHERE T1.SALARY_STRUCID_REF='$id' ORDER BY T1.SALARY_EARNINGID ASC
                        ");

            if(isset($MAT_ARR) && !empty($MAT_ARR)){
                foreach($MAT_ARR as $key=>$val){

                    $data   =   $this->getEarningDeduction($val->HEAD_TYPE,$val->EARNING_HEADID_REF);

                    $MAT[]=array(
                        'SALARY_EARNINGID'=>$val->SALARY_EARNINGID,
                        'SALARY_STRUCID_REF'=>$val->SALARY_STRUCID_REF,
                        'EARNING_HEADID_REF'=>$val->EARNING_HEADID_REF,
                        'EARNING_TYPEID_REF'=>$val->EARNING_TYPEID_REF,
                        'SQ_NO'=>$val->SQ_NO,
                        'AMT_FORMULA'=>$val->AMT_FORMULA,
                        'FORMULA'=>$val->FORMULA,
                        'AMOUNT'=>$val->AMOUNT,
                        'REMARKS'=>$val->REMARKS,
                        'INDATE'=>$val->INDATE,
                        'HEAD_TYPE'=>$val->HEAD_TYPE,
                        'HEADCODE'=>$data->HEADCODE,
                        'HEADDESC'=>$data->HEADDESC,
                        'TYPECODE'=>$data->TYPECODE,
                        'TYPEDESC'=>$data->TYPEDESC,
                    );
                }

            }

        }

        $FormId  = $this->form_id;

        return view($this->view.$FormId.'view',compact(['FormId','objRights','HDR','MAT']));

    }

    public function getEarningDeduction($type,$id){

        if($type =="EARNING"){

            $data   =   DB::select("SELECT 
                        T1.EARNING_HEADCODE AS HEADCODE,T1.EARNING_HEAD_DESC AS HEADDESC,
                        T2.EARNING_TYPECODE AS TYPECODE,T2.EARNING_TYPE_DESC AS TYPEDESC
                        FROM TBL_MST_EARNING_HEAD T1
                        LEFT JOIN TBL_MST_EARNING_HEAD_TYPE T2 ON T1.EARNING_TYPEID_REF=T2.EARNING_TYPEID
                        WHERE T1.EARNING_HEADID='$id' 
                        ")[0];
        }
        else if($type =="DEDUCTION"){

            $data   =   DB::select("SELECT 
                        T1.DEDUCTION_HEADCODE AS HEADCODE,T1.DEDUCTION_HEAD_DESC AS HEADDESC,
                        T2.DEDUCTION_TYPECODE AS TYPECODE,T2.DEDUCTION_TYPE_DESC AS TYPEDESC
                        FROM TBL_MST_DEDUCTION_HEAD T1
                        LEFT JOIN TBL_MST_DEDUCTION_HEAD_TYPE T2 ON T1.DEDUCTION_TYPEID_REF=T2.DEDUCTION_TYPEID
                        WHERE T1.DEDUCTION_HEADID='$id'
                        ")[0];
        }

        return $data;

    }
     
    public function update(Request $request){

        $r_count1 = $request['Row_Count1'];

        for ($i=0; $i<=$r_count1; $i++){

            if(isset($request['EARNING_HEADID_REF_'.$i]) && !is_null($request['EARNING_HEADID_REF_'.$i])){

                $req_data[$i] = [
                    'EARNING_HEADID_REF'    => $request['EARNING_HEADID_REF_'.$i],
                    'EARNING_TYPEID_REF'    => $request['EARNING_TYPEID_REF_'.$i],
                    'SQ_NO'                 => $request['SQ_NO_'.$i] , 
                    'AMT_FORMULA'           => $request['AMT_FORMULA_'.$i] ,     
                    'FORMULA'               => $request['FORMULA_'.$i] ,     
                    'AMOUNT'                => $request['AMOUNT_'.$i] ,     
                    'REMARKS'               => $request['REMARKS_'.$i] ,     
                    'HEAD_TYPE'             => $request['HEAD_TYPE_'.$i] ,         
                ];
            }
        }

        $wrapped_links["EARNING"]   =   $req_data; 
        $XMLEARNING                 =   ArrayToXml::convert($wrapped_links);
        $XMLDEDUCTION   = NULL;


        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID       =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();


        $SALARY_STRUC_NO    =   strtoupper(trim($request['SALARY_STRUC_NO']) );
        $SALARY_STRUC_DESC  =   trim($request['SALARY_STRUC_DESC']); 

        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
        $newDateString = NULL;

        $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt) ){
            
            $newdt = str_replace( "/", "-",  $newdt ) ;

            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        
        $DODEACTIVATED = $newDateString;


        $array_data     =   [
                                $SALARY_STRUC_NO,   $SALARY_STRUC_DESC, $DEACTIVATED,   $DODEACTIVATED,     $CYID_REF,
                                $BRID_REF,          $FYID_REF,          $XMLEARNING,    $XMLDEDUCTION,       $VTID, 
                                $USERID,            $UPDATE,            $UPTIME,        $ACTION,             $IPADDRESS
                            ];

        $sp_result = DB::select('EXEC SP_SALARY_STRUCTURE_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $array_data);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $SALARY_STRUC_NO. ' Sucessfully Updated.']);

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

        for ($i=0; $i<=$r_count1; $i++){

            if(isset($request['EARNING_HEADID_REF_'.$i]) && !is_null($request['EARNING_HEADID_REF_'.$i])){

                $req_data[$i] = [
                    'EARNING_HEADID_REF'    => $request['EARNING_HEADID_REF_'.$i],
                    'EARNING_TYPEID_REF'    => $request['EARNING_TYPEID_REF_'.$i],
                    'SQ_NO'                 => $request['SQ_NO_'.$i] , 
                    'AMT_FORMULA'           => $request['AMT_FORMULA_'.$i] ,     
                    'FORMULA'               => $request['FORMULA_'.$i] ,     
                    'AMOUNT'                => $request['AMOUNT_'.$i] ,     
                    'REMARKS'               => $request['REMARKS_'.$i] ,     
                    'HEAD_TYPE'             => $request['HEAD_TYPE_'.$i] ,         
                ];
            }
        }

        $wrapped_links["EARNING"]   =   $req_data; 
        $XMLEARNING                 =   ArrayToXml::convert($wrapped_links);
        $XMLDEDUCTION   = NULL;

        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTION = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');

        $SALARY_STRUC_NO    =   strtoupper(trim($request['SALARY_STRUC_NO']) );
        $SALARY_STRUC_DESC  =   trim($request['SALARY_STRUC_DESC']); 

        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
        $newDateString = NULL;

        $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt) ){
            
            $newdt = str_replace( "/", "-",  $newdt ) ;

            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        
        $DODEACTIVATED = $newDateString;


        $array_data     =   [
                                $SALARY_STRUC_NO,   $SALARY_STRUC_DESC, $DEACTIVATED,   $DODEACTIVATED,     $CYID_REF,
                                $BRID_REF,          $FYID_REF,          $XMLEARNING,    $XMLDEDUCTION,       $VTID_REF, 
                                $USERID,            $UPDATE,            $UPTIME,        $ACTION,             $IPADDRESS
                            ];

        $sp_result = DB::select('EXEC SP_SALARY_STRUCTURE_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $array_data);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $SALARY_STRUC_NO. ' Sucessfully Approved.']);

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
                foreach ($sp_listing_result as $key=>$valueitem)
            {  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$valueitem->LAVELS;
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
            $TABLE      =   "TBL_MST_SALARY_STRUCTURE";
            $FIELD      =   "SALARY_STRUCID";
            $ACTIONNAME = $Approvallevel;
            $UPDATE     =   Date('Y-m-d');
            $UPTIME     =   Date('h:i:s.u');
            $IPADDRESS  =   $request->getClientIp();
        
        
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];

            
        $sp_result = DB::select('EXEC SP_MST_MULTIAPPROVAL ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
        
        
        if($sp_result[0]->RESULT=="All records approved"){

            return Response::json(['approve' =>true,'msg' => 'Record successfully Approved.']);

        }elseif($sp_result[0]->RESULT=="NO RECORD FOR APPROVAL"){
        
            return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','exist'=>'norecord']);
        
        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','exist'=>'Some Error']);
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
         $TABLE      =   "TBL_MST_SALARY_STRUCTURE";
         $FIELD      =   "SALARY_STRUCID";
         $ID         =   $id;
         $UPDATE     =   Date('Y-m-d');
         $UPTIME     =   Date('h:i:s.u');
         $IPADDRESS  =   $request->getClientIp();
         
        $canceldata[0]=[
            'NT'  => 'TBL_MST_SALARY_STRUCTURE',
        ];

        $canceldata[1]=[
            'NT'  => 'TBL_MST_SALARY_STRUCTURE_EARNING',
        ]; 

        $links["TABLES"] = $canceldata; 
        $cancelxml = ArrayToXml::convert($links);
         
         
         $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$cancelxml ];
 
         
         $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);
         
         
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

            $objResponse = DB::table('TBL_MST_SALARY_STRUCTURE')->where('SALARY_STRUCID','=',$id)->first();

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

            return view($this->view.$FormId.'attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments']));
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
    
    $image_path         =   "docs/company".$CYID_REF."/SalaryStructureMaster";     
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





}
