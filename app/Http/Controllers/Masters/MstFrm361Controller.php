<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm361;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;


class MstFrm361Controller extends Controller{
   
    protected $form_id      =   361;
    protected $vtid_ref     =   447;
    protected $view         =   "masters.Quality.QualityInspectionMaster.mstfrm";

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){ 
        
        $objRights  =   DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();
  
        $FormId         =   $this->form_id;
        $objDataList = DB::select("SELECT T1.*,T2.NAME,T2.ICODE FROM TBL_MST_QIC_HDR T1 LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID ORDER BY T1.QICID DESC");

        return view($this->view.$FormId,compact(['objRights','objDataList','FormId']));
    }

    public function ViewReport($request) {

        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
           
        $QICID       =   $myValue['QICID'];
        $Flag       =   $myValue['Flag'];

        // $objSalesOrder = DB::table('TBL_TRN_PROR01_HDR')
        // ->where('TBL_TRN_PROR01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        // ->where('TBL_TRN_PROR01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        // ->where('TBL_TRN_PROR01_HDR.POID','=',$POID)
        // ->select('TBL_TRN_PROR01_HDR.*')
        // ->first();
        
        
        $ssrs = new \SSRS\Report('http://103.35.123.42:8181//ReportServer/', array('username' => 'Administrator', 'password' => 'fSE7+Dagv-g^RU'));
        $result = $ssrs->loadReport('/Alps/QICMPrint');
		//$result = $ssrs->loadReport('/Alps/POPrint -ZEP');
        
        $reportParameters = array(
            'QICID' => $QICID,
        );
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
        ->setExecutionParameters($parameters);
        if($Flag == 'H')
        {
            $output = $ssrs->render('HTML4.0'); 
            echo $output;
        }
        else if($Flag == 'P')
        {
            $output = $ssrs->render('PDF'); 
            return $output->download('Report.pdf');
        }
        else if($Flag == 'E')
        {
            $output = $ssrs->render('EXCEL'); 
            return $output->download('Report.xls');
        }
         
     }

    public function add(){ 

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $objlastdt  =   $this->getLastdt();

        $objSON = DB::table('TBL_MST_DOCNO_DEFINITION')
            ->where('VTID_REF','=',$this->vtid_ref)
            ->where('CYID_REF','=',$CYID_REF)
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

        $FormId  = $this->form_id;
      
        return view($this->view.$FormId.'add',compact(['objSON','objAutoGenNo','objlastdt','FormId']));
    }

    public function getItemDetails(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

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

                $row.=' <tr id="item_'.$ITEMID.'" class="clsitemid">
                        <td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1"  ></td>
                        <td style="width:10%;">'.$ICODE.'<input type="hidden" id="txtitem_'.$ITEMID.'" data-desc="'.$ICODE.'" value="'.$ITEMID.'"/></td>
                        <td style="width:10%;" id="itemname_'.$ITEMID.'" >'.$NAME.'<input type="hidden" id="txtitemname_'.$ITEMID.'" data-desc="'.$ITEM_SPECI.'" value="'.$NAME.'"/></td>
                        <td style="width:8%;" id="itemuom_'.$ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ITEMID.'" data-desc="'.$Alt_UOM.'" value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>
                        <td style="width:8%;" id="uomqty_'.$ITEMID.'" ><input type="hidden" id="txtuomqty_'.$ITEMID.'" data-desc="'.$TOQTY.'" value="'.$ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>
                        <td style="width:8%;" id="irate_'.$ITEMID.'"><input type="hidden" id="txtirate_'.$ITEMID.'" data-desc="'.$FROMQTY.'" value="'.$STDCOST.'"/>'.$GroupName.'</td>
                        <td style="width:8%;" id="itax_'.$ITEMID.'"><input type="hidden" id="txtitax_'.$ITEMID.'" />'.$Categoryname.'</td>
                        <td style="width:8%;">'.$BusinessUnit.'</td>
                        <td style="width:8%;">'.$ALPS_PART_NO.'</td>
                        <td style="width:8%;">'.$CUSTOMER_PART_NO.'</td>
                        <td style="width:8%;">'.$OEM_PART_NO.'</td>
                        <td style="width:8%;">Authorized</td>
                        </tr>'; 
            } 

            echo $row;
                               
        }           
        else{
            echo '<tr><td colspan="12"> Record not found.</td></tr>';
        }

        exit();
    }

    
    public function getQcpCode(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $fieldid        =   $request['fieldid'];

        $ObjData        =   DB::table('TBL_MST_QCP')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('STATUS','=','A')
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('QCPID AS ID','QCP_CODE AS CODE','QCP_DESC AS DESC')
                            ->get();

        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){

                $row            =   '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="socode_'.$dataRow->ID .'"  class="clssQCPid" value="'.$dataRow->ID.'" ></td>
                <td class="ROW2">'.$dataRow->CODE;
                $row = $row.'<input type="hidden" id="txtsocode_'.$dataRow->ID.'" data-desc="'.$dataRow->CODE.'" data-desc1="'.$dataRow->DESC.'"  value="'.$dataRow->ID.'"/></td>
                <td class="ROW3" >'.$dataRow->DESC.'</td></tr>';
                echo $row;
                
            }

        }else{
            echo '<tr><td>Record not found.</td></tr>';
        }
        exit();   
    }


    public function getUOMNo(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $fieldid        =   $request['fieldid'];

        $ObjData        =   DB::select("SELECT UOMID,UOMCODE,DESCRIPTIONS FROM TBL_MST_UOM 
        where CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF'  AND STATUS='A' AND (DEACTIVATED=0 or DEACTIVATED is null)"); 

        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){

                $row            =   '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="socode_'.$dataRow->UOMID .'"  class="clssUOMID" value="'.$dataRow->UOMID.'" ></td>
                <td class="ROW2">'.$dataRow->UOMCODE;
                $row = $row.'<input type="hidden" id="txtsocode_'.$dataRow->UOMID.'" data-desc="'.$dataRow->UOMCODE.'" value="'.$dataRow->UOMID.'"/></td>
                <td class="ROW3" >'.$dataRow->DESCRIPTIONS.'</td></tr>';
                echo $row;
                
            }

        }else{
            echo '<tr><td>Record not found.</td></tr>';
        }
        exit();   
    }




    
    public function getINTMNTNo(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $fieldid        =   $request['fieldid'];

        $ObjData        =   DB::select("SELECT INSTRUMENT_METHOD_ID,INSTRUMENT_METHOD_CODE,INSTRUMENT_METHOD_NAME FROM TBL_MST_INSTRUMENT_METHOD 
        where CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND (DEACTIVATED=0 or DEACTIVATED is null)"); 

        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){

                $row            =   '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="socodeint_'.$dataRow->INSTRUMENT_METHOD_ID .'"  class="clssINTMNTID" value="'.$dataRow->INSTRUMENT_METHOD_ID.'" ></td>
                <td class="ROW2">'.$dataRow->INSTRUMENT_METHOD_CODE;
                $row = $row.'<input type="hidden" id="txtsocodeint_'.$dataRow->INSTRUMENT_METHOD_ID.'" data-desc="'.$dataRow->INSTRUMENT_METHOD_NAME.'" value="'.$dataRow->INSTRUMENT_METHOD_ID.'"/></td>
                <td class="ROW3" >'.$dataRow->INSTRUMENT_METHOD_NAME.'</td></tr>';
                echo $row;
                
            }

        }else{
            echo '<tr><td>Record not found.</td></tr>';
        }
        exit();   
    }


   public function codeduplicate(Request $request){

        $Status     =   "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $ITEMID_REF =   $request['ITEMID_REF']; 
        
        $objLabel = DB::table('TBL_MST_QIC_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('ITEMID_REF','=',$ITEMID_REF)
        ->where('STATUS','=',$Status)        
        ->select('*')
        ->first();

        $objDup = DB::table('TBL_MST_QIC_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('ITEMID_REF','=',$ITEMID_REF)
        ->where('STATUS','=','N')        
        ->select('*')
        ->first();        
        
        if($objLabel){  
           
            return Response::json(['exists' =>true,'msg' => 'Item Duplicate record']);
          
          }elseif($objDup){
          
              
            return Response::json(['exists' =>true,'msg' => 'Item Duplicate record']);
              
          }else{
             
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
          } 
        
        exit();
   }

   public function save(Request $request){

        $r_count1 = $request['Row_Count1'];

        for ($i=0; $i<=$r_count1; $i++){

            if(isset($request['QCPID_REF_'.$i]) && !is_null($request['QCPID_REF_'.$i])){

                $req_data[$i] = [
                    'QCPID_REF'             => $request['QCPID_REF_'.$i],
                    'STANDARDVALUE_TYPE'    => $request['STANDARDVALUE_TYPE_'.$i],
                    'STANDARD_VALUE'        => $request['STANDARD_VALUE_'.$i] ,
                    'UOMID_REF'          => $request['UOMID_REF_'.$i] !=""?$request['UOMID_REF_'.$i]:NULL,
                    'INSTRUMENT_METHOD_ID_REF'          => $request['INTMNTID_REF_'.$i] !=""?$request['INTMNTID_REF_'.$i]:NULL,     
                ];
            }
        }

        //dd($req_data);

        $wrapped_links["ROLE"] = $req_data; 
        $XMLROLE = ArrayToXml::convert($wrapped_links);


        $QICNO          =     strtoupper(trim($request['QICNO']) );
        $QICDT          =   trim($request['QICDT']); 
        $ITEMID_REF     =   trim($request['ITEMID_REF']); 
        $DEACTIVATED    =   NULL;  
        $DODEACTIVATED  =   NULL;  

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');       
        $VTID           =   $this->vtid_ref;
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();
        
        $array_data     =   [
                                $QICNO,     $QICDT,     $ITEMID_REF,    $DEACTIVATED,   $DODEACTIVATED, 
                                $XMLROLE,   $CYID_REF,  $BRID_REF,      $FYID_REF,      $VTID, 
                                $USERID,    $UPDATE,    $UPTIME,        $ACTION,        $IPADDRESS
                            ];

        $sp_result = DB::select('EXEC SP_QIC_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $array_data);

    
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
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
                        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                        ->first(); 

        $HDR        =   array(); 
        $MAT        =   array();            

        if(!is_null($id)){
        
            $HDR    =   DB::select("SELECT top 1 T1.*,T2.ICODE,T2.NAME AS ITEM_NAME
                        FROM TBL_MST_QIC_HDR T1
                        LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                        WHERE T1.QICID='$id' ORDER BY T1.QICID ASC
                        ")[0];

            $MAT    =   DB::select("SELECT 
                        T1.*,T2.QCP_CODE,T2.QCP_DESC,
                        T4.UOMCODE,
                        T5.INSTRUMENT_METHOD_NAME

                        FROM TBL_MST_QIC_MAT T1
                        LEFT JOIN TBL_MST_QCP T2 ON T1.QCPID_REF=T2.QCPID
                        LEFT JOIN TBL_MST_UOM T4 ON T1.UOMID_REF=T4.UOMID
                        LEFT JOIN TBL_MST_INSTRUMENT_METHOD T5 ON T1.INSTRUMENT_METHOD_ID_REF=T5.INSTRUMENT_METHOD_ID
                        WHERE T1.QICID_REF='$id' ORDER BY T1.QIC_MATID ASC
                        ");
			$objCount = count($MAT);
        }

        $FormId  = $this->form_id;

        return view($this->view.$FormId.'edit',compact(['FormId','objRights','HDR','MAT','objCount']));

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
                        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                        ->first(); 

        $HDR        =   array(); 
        $MAT        =   array();            

        if(!is_null($id)){
        
            $HDR    =   DB::select("SELECT top 1 T1.*,T2.ICODE,T2.NAME AS ITEM_NAME
                        FROM TBL_MST_QIC_HDR T1
                        LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                        WHERE T1.QICID='$id' ORDER BY T1.QICID ASC
                        ")[0];

            $MAT    =   DB::select("SELECT
                        T1.*,T2.QCP_CODE,T2.QCP_DESC,
                        T4.UOMCODE,
                        T5.INSTRUMENT_METHOD_NAME
                        
                        FROM TBL_MST_QIC_MAT T1
                        LEFT JOIN TBL_MST_QCP T2 ON T1.QCPID_REF=T2.QCPID
                        LEFT JOIN TBL_MST_UOM T4 ON T1.UOMID_REF=T4.UOMID
                        LEFT JOIN TBL_MST_INSTRUMENT_METHOD T5 ON T1.INSTRUMENT_METHOD_ID_REF=T5.INSTRUMENT_METHOD_ID
                        WHERE T1.QICID_REF='$id' ORDER BY T1.QIC_MATID ASC
                        ");
			$objCount = count($MAT);
        }

        $FormId  = $this->form_id;

        return view($this->view.$FormId.'view',compact(['FormId','objRights','HDR','MAT','objCount']));

    }

     
    public function update(Request $request){

        $r_count1 = $request['Row_Count1'];

        for ($i=0; $i<=$r_count1; $i++){

            if(isset($request['QCPID_REF_'.$i]) && !is_null($request['QCPID_REF_'.$i])){

                $req_data[$i] = [
                    'QCPID_REF'             => $request['QCPID_REF_'.$i],
                    'STANDARDVALUE_TYPE'    => $request['STANDARDVALUE_TYPE_'.$i],
                    'STANDARD_VALUE'        => $request['STANDARD_VALUE_'.$i] , 
                    'UOMID_REF'          => $request['UOMID_REF_'.$i] !=""?$request['UOMID_REF_'.$i]:NULL,
                    'INSTRUMENT_METHOD_ID_REF'          => $request['INTMNTID_REF_'.$i] !=""?$request['INTMNTID_REF_'.$i]:NULL,     
                ];
            }
        }

        
        $wrapped_links["QIC"] = $req_data; 
        $XMLROLE = ArrayToXml::convert($wrapped_links);


        $QICNO          =   strtoupper(trim($request['QICNO']) );
        $QICDT          =   trim($request['QICDT']); 
        $ITEMID_REF     =   trim($request['ITEMID_REF']); 

        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
        $newDateString = NULL;

        $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt) ){
            
            $newdt = str_replace( "/", "-",  $newdt ) ;

            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        
        $DODEACTIVATED = $newDateString;

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID       =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();
        
        $array_data     =   [
            $QICNO,     $QICDT,     $ITEMID_REF,    $DEACTIVATED,   $DODEACTIVATED, 
            $XMLROLE,   $CYID_REF,  $BRID_REF,      $FYID_REF,      $VTID, 
            $USERID,    $UPDATE,    $UPTIME,        $ACTION,        $IPADDRESS
        ];


        $sp_result = DB::select('EXEC SP_QIC_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $array_data);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $QICNO. ' Sucessfully Updated.']);

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

            if(isset($request['QCPID_REF_'.$i]) && !is_null($request['QCPID_REF_'.$i])){

                $req_data[$i] = [
                    'QCPID_REF'             => $request['QCPID_REF_'.$i],
                    'STANDARDVALUE_TYPE'    => $request['STANDARDVALUE_TYPE_'.$i],
                    'STANDARD_VALUE'        => $request['STANDARD_VALUE_'.$i] ,
                    'UOMID_REF'          => $request['UOMID_REF_'.$i] !=""?$request['UOMID_REF_'.$i]:NULL,
                    'INSTRUMENT_METHOD_ID_REF'          => $request['INTMNTID_REF_'.$i] !=""?$request['INTMNTID_REF_'.$i]:NULL,      
                ];
            }
        }

        
        $wrapped_links["QIC"] = $req_data; 
        $XMLROLE = ArrayToXml::convert($wrapped_links);

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

        $QICNO          =   strtoupper(trim($request['QICNO']) );
        $QICDT          =   trim($request['QICDT']); 
        $ITEMID_REF     =   trim($request['ITEMID_REF']); 

        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
        $newDateString = NULL;

        $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt) ){
            
            $newdt = str_replace( "/", "-",  $newdt ) ;

            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        

        $DODEACTIVATED = $newDateString;
        
        $array_data     =   [
            $QICNO,     $QICDT,     $ITEMID_REF,    $DEACTIVATED,   $DODEACTIVATED, 
            $XMLROLE,   $CYID_REF,  $BRID_REF,      $FYID_REF,      $VTID_REF, 
            $USERID,    $UPDATE,    $UPTIME,        $ACTION,        $IPADDRESS
        ];


        $sp_result = DB::select('EXEC SP_QIC_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $array_data);


        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $QICNO. ' Sucessfully Approved.']);

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
            $TABLE      =   "TBL_MST_QIC_HDR";
            $FIELD      =   "QICID";
            $ACTIONNAME     = $Approvallevel;
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
         $TABLE      =   "TBL_MST_QIC_HDR";
         $FIELD      =   "QICID";
         $ID         =   $id;
         $UPDATE     =   Date('Y-m-d');
         $UPTIME     =   Date('h:i:s.u');
         $IPADDRESS  =   $request->getClientIp();
         
        $canceldata[0]=[
            'NT'  => 'TBL_MST_QIC_HDR',
        ];

        $canceldata[1]=[
            'NT'  => 'TBL_MST_QIC_MAT',
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

    public function checkduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $ITEMID_REF =   $request['ITEMID_REF'];        
        $QICNO =   $request['QICNO'];
        
        $objLabel = DB::table('TBL_MST_QIC_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('ITEMID_REF','=',$ITEMID_REF)
        ->where('QICNO','!=',$QICNO)
        ->where('STATUS','!=','C')        
        ->get();

        //dd($objLabel);


        if(!empty($objLabel)){  

            return Response::json(['exists' =>true,'msg' => 'Item Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }

    public function attachment($id){

        if(!is_null($id)){
        
            $FormId     =   $this->form_id;

            $objResponse = DB::table('TBL_MST_QIC_HDR')->where('QICID','=',$id)->first();

            $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
            ->where('VTID','=',$this->vtid_ref)
                ->select('VTID','VCODE','DESCRIPTIONS')
            ->get()
            ->toArray();

            $objAttachments = DB::table('TBL_MST_ATTACHMENT')                    
            ->where('TBL_MST_ATTACHMENT.VTID_REF','=',$this->vtid_ref)
            ->where('TBL_MST_ATTACHMENT.ATTACH_DOCNO','=',$id)
            ->where('TBL_MST_ATTACHMENT.CYID_REF','=',Auth::user()->CYID_REF)
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
        
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/QualityInspectionMaster";
		
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

                   

                    $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."#_".$filenamewithextension;  

                    if ($uploadedFile->isValid()) {

                        if(in_array($extension,$allow_extnesions)){
                            
                            if($filesize < $allow_size){

                                $filename = $destinationPath."/".$filenametostore;

                                if (!file_exists($filename)) {

                                   $uploadedFile->move($destinationPath, $filenametostore);  
                                   $uploaded_data[$index]["FILENAME"] =$filenametostore;
                                   $uploaded_data[$index]["LOCATION"] = $destinationPath."/";
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

    public function getLastdt(){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        return  DB::select('SELECT MAX(QICDT) QICDT FROM TBL_MST_QIC_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
    }


    public function amendment($id=NULL){

        $USERID     =   Auth::user()->USERID;
        $VTID       =   $this->vtid_ref;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');    
        $FYID_REF   =   Session::get('FYID_REF');

        $objRights  =   DB::table('TBL_MST_USERROLMAP')
                        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                        ->first(); 

        $HDR        =   array(); 
        $MAT        =   array();            

        if(!is_null($id)){
        
            $HDR    =   DB::select("SELECT top 1 T1.*,T2.ICODE,T2.NAME AS ITEM_NAME
                        FROM TBL_MST_QIC_HDR T1
                        LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                        WHERE T1.QICID='$id' ORDER BY T1.QICID ASC
                        ")[0];

            $MAT    =   DB::select("SELECT 
                        T1.*,T2.QCP_CODE,T2.QCP_DESC,
                        T4.UOMCODE,
                        T5.INSTRUMENT_METHOD_NAME

                        FROM TBL_MST_QIC_MAT T1
                        LEFT JOIN TBL_MST_QCP T2 ON T1.QCPID_REF=T2.QCPID
                        LEFT JOIN TBL_MST_UOM T4 ON T1.UOMID_REF=T4.UOMID
                        LEFT JOIN TBL_MST_INSTRUMENT_METHOD T5 ON T1.INSTRUMENT_METHOD_ID_REF=T5.INSTRUMENT_METHOD_ID
                        WHERE T1.QICID_REF='$id' ORDER BY T1.QIC_MATID ASC
                        ");

                $objRights  =   DB::table('TBL_MST_USERROLMAP')
                                    ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                    ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                    ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                    ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                    ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                    ->first();

                $objQIC = DB::table('TBL_MST_QIC_HDR')
                             ->where('CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('BRID_REF','=',Session::get('BRID_REF'))
                             ->where('QICID','=',$id)
                             ->first();

                $MAXQIC_NO=NULL;
                if(isset($objQIC->QICID) && $objQIC->QICID !=""){
                    $objQICNO = DB::SELECT("select  MAX(isnull(ANO,0))+1  AS ANO from TBL_MST_QIC_HDR  WHERE QICID=? AND QICNO=?",[$objQIC->QICID,$objQIC->QICNO]);
                    $MAXQIC_NO = $objQICNO[0]->ANO;
                }

			$objCount = count($MAT);
        }

        $FormId  = $this->form_id;

        return view($this->view.$FormId.'amendment',compact(['FormId','objRights','HDR','MAT','objCount','objQIC','MAXQIC_NO']));

    }



    public function saveamendment(Request $request){

        $r_count1 = $request['Row_Count1'];

        for ($i=0; $i<=$r_count1; $i++){

            if(isset($request['QCPID_REF_'.$i]) && !is_null($request['QCPID_REF_'.$i])){

                $req_data[$i] = [
                    'QCPID_REF'             => $request['QCPID_REF_'.$i],
                    'STANDARDVALUE_TYPE'    => $request['STANDARDVALUE_TYPE_'.$i],
                    'STANDARD_VALUE'        => $request['STANDARD_VALUE_'.$i] ,
                    'UOMID_REF'          => $request['UOMID_REF_'.$i] !=""?$request['UOMID_REF_'.$i]:NULL,
                    'INSTRUMENT_METHOD_ID_REF'          => $request['INTMNTID_REF_'.$i] !=""?$request['INTMNTID_REF_'.$i]:NULL,     
                ];
            }
        }

        //dd($req_data);

        if(isset($req_data)) { 
            $wrapped_links["QIC"] = $req_data; 
            $XML = ArrayToXml::convert($wrapped_links);
        } else {
            $XML = NULL; 
        }       


        $QICNO              =   trim($request['QICNO'])?trim($request['QICNO']):NULL;
        $QICDT              =   trim($request['QICDT'])?trim($request['QICDT']):NULL; 
        $ITEMID_REF         =   trim($request['ITEMID_REF'])?trim($request['ITEMID_REF']):NULL; 
        $ANO                =   trim($request['QIC_NO'])?trim($request['QIC_NO']):NULL;
        $AMENDMENT_DATE     =   trim($request['QIC_DT'])?trim($request['QIC_DT']):NULL;
        $AMENDMENT_REASON   =   trim($request['CUSTOMERAREFNO'])?trim($request['CUSTOMERAREFNO']):NULL;

        
        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
        $newDateString = NULL;

        $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt) ){
            
            $newdt = str_replace( "/", "-",  $newdt ) ;

            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }

        $DODEACTIVATED = $newDateString;


        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');       
        $VTID_REF       =   $this->vtid_ref;
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();
        
        $array_data     =   [   $QICNO,     $QICDT,     $ITEMID_REF,    $DEACTIVATED,   $DODEACTIVATED, 
                                $XML,       $CYID_REF,  $BRID_REF,      $FYID_REF,      $VTID_REF, 
                                $USERID,    $UPDATE,    $UPTIME,        $ACTION,        $IPADDRESS,
                                $ANO,       $AMENDMENT_DATE,$AMENDMENT_REASON
                            ];

                            //dd($array_data);

        $sp_result = DB::select('EXEC SP_QIC_AMENDMENT ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $array_data);

        //dd($sp_result);
    
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        
        exit();    
    }























}
