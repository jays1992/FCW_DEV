<?php
namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Admin\TblMstUser;
use Auth;
use DB;
use Facade\Ignition\DumpRecorder\Dump;
use Session;
use Response;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Helpers\Helper;
use App\Helpers\Utils;

class TrnFrm239Controller extends Controller{

    protected $form_id  = 239;
    protected $vtid_ref = 329;
    protected $view     = "transactions.Production.ProductionOrder.trnfrm";
   
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        $objRights      =   DB::table('TBL_MST_USERROLMAP')
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
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.PROID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_PDPRO_HDR hdr
                            on a.VID = hdr.PROID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF  
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.PROID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );

        return view($this->view.$FormId,compact(['REQUEST_DATA','objRights','objDataList','FormId']));
    }

    public function add(){       
        $Status     = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
       
        $objlastdt          =   $this->getLastdt();
        // $objSubLedgerList   =   $this->getSubLedgerList();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_PDPRO_HDR',
            'HDR_ID'=>'PROID',
            'HDR_DOC_NO'=>'PRO_NO',
            'HDR_DOC_DT'=>'PRO_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
        
       
   
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_PRO")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                {       
                                $query->select('UDFPROID')->from('TBL_MST_UDFFOR_PRO')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);                      
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);                   
                   

        $objUdfData = DB::table('TBL_MST_UDFFOR_PRO')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();  

        $objCountUDF = count($objUdfData);

        $FormId     =   $this->form_id;
        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
        return view($this->view.$FormId.'add',compact([
            'AlpsStatus',
            'FormId',
          
            'objlastdt',
            // 'objSubLedgerList',
            'objUdfData',
            'objCountUDF',
            'TabSetting',
            'doc_req','docarray'
            ]));       
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

    public function save(Request $request) {

        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count4 = $request['Row_Count4'];

       
        
		$req_data=array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $req_data[$i] = [
                    'SLID_REF'     => isset($request['SLID_REF_'.$i]) && $request['SLID_REF_'.$i] !=""?$request['SLID_REF_'.$i]:NULL,
                    'SOID_REF'     => isset($request['SOID_REF_'.$i]) && $request['SOID_REF_'.$i] !=""?$request['SOID_REF_'.$i]:NULL,
                    'ITEMID_REF'   => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'    => $request['MAIN_UOMID_REF_'.$i],
                    'SOQTY'        => isset($request['QTY_'.$i]) && $request['QTY_'.$i] !=""?$request['QTY_'.$i]:0,
                    'BL_SOQTY'     => isset($request['BL_SOQTY_'.$i]) && $request['BL_SOQTY_'.$i] !=""?$request['BL_SOQTY_'.$i]:0,
                    'PD_OR_QTY'    => isset($request['PD_OR_QTY_'.$i]) && $request['PD_OR_QTY_'.$i] !=""?$request['PD_OR_QTY_'.$i]:0,
                    'SQID_REF'     => isset($request['SQID_REF_'.$i]) && $request['SQID_REF_'.$i] !=""?$request['SQID_REF_'.$i]:NULL,
                    'SEID_REF'     => isset($request['SEID_REF_'.$i]) && $request['SEID_REF_'.$i] !=""?$request['SEID_REF_'.$i]:NULL,  
                    'BOMID_REF'     => isset($request['BOMID_REF_'.$i]) && $request['BOMID_REF_'.$i] !=""?$request['BOMID_REF_'.$i]:NULL,  
                    'CONSUME_QTY_REF'    => isset($request['BOMDATA_'.$i]) && $request['BOMDATA_'.$i] !=""?$request['BOMDATA_'.$i]:0, 
                ];

            }
        }

        //echo "<pre>";
        //dd($req_data);

        //die;
		
        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
		
		$reqdata3=array();
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['UDF_'.$i])){
                $reqdata3[$i] = [
                    'UDFPROID_REF'      => $request['UDF_'.$i],
                    'VALUE'  => $request['udfvalue_'.$i],
                ];
            }
        }

        if($r_count2 > 0){
            $wrapped_links3["UDF"] = $reqdata3; 
            $XMLUDF = ArrayToXml::convert($wrapped_links3);
        }
        else{
            $XMLUDF=NULL;
        }

		
		$req_data4=array();
        for ($i=0; $i<=$r_count4; $i++){
            if(isset($request['REQ_SOITEMID_REF_'.$i])){

                $req_data4[$i] = [
                    'SOID_REF'          => isset($request['REQ_SOID_REF_'.$i]) && $request['REQ_SOID_REF_'.$i] !=""?$request['REQ_SOID_REF_'.$i]:NULL,
                    'SOITEMID_REF'      => $request['REQ_SOITEMID_REF_'.$i],
                    'ITEMID_REF'        => $request['REQ_ITEMID_REF_'.$i],
                    'BOM_QTY'           => isset($request['REQ_BOM_QTY_'.$i]) && $request['REQ_BOM_QTY_'.$i] !=""?$request['REQ_BOM_QTY_'.$i]:0,
                    'INPUT_PD_OR_QTY'   => isset($request['REQ_INPUT_PD_OR_QTY_'.$i]) && $request['REQ_INPUT_PD_OR_QTY_'.$i] !=""?$request['REQ_INPUT_PD_OR_QTY_'.$i]:0,
                    'CHANGES_PD_OR_QTY' => isset($request['REQ_CHANGES_PD_OR_QTY_'.$i]) && $request['REQ_CHANGES_PD_OR_QTY_'.$i] !=""?$request['REQ_CHANGES_PD_OR_QTY_'.$i]:0,
					'MAIN_ITEMID_REF'   => $request['REQ_MAIN_ITEMID_REF_'.$i],
                    'SEID_REF'          => isset($request['REQ_SEID_REF_'.$i]) && $request['REQ_SEID_REF_'.$i] !=""?$request['REQ_SEID_REF_'.$i]:NULL,
                    'SQID_REF'          => isset($request['REQ_SQID_REF_'.$i]) && $request['REQ_SQID_REF_'.$i] !=""?$request['REQ_SQID_REF_'.$i]:NULL,
                ];

            }
        }
		

        //dd($req_data4);


		if($r_count4 > 0){
            $wrapped_links4["REQ"] = $req_data4; 
			$XMLREQ = ArrayToXml::convert($wrapped_links4);
        }
        else{
            $XMLREQ=NULL;
        }		
 
        
        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $PRO_NO     =   $request['PRO_NO'];
        $PRO_DT     =   $request['PRO_DT'];
        $PRO_TITLE  =   $request['PRO_TITLE'];
        $DIRECTPO   =   (isset($request['Direct'])!="true" ? 0 : 1);
        $SELECTIONPARAM =   $request['AllStatus']=="1" ? 1 : 0;

        $log_data = [ 
            $PRO_NO,$PRO_DT,$PRO_TITLE,$CYID_REF,$BRID_REF,
            $FYID_REF,$VTID_REF,$XMLMAT,$XMLREQ,$XMLUDF,
            $USERID, Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$DIRECTPO,$SELECTIONPARAM
        ]; 

        $sp_result = DB::select('EXEC SP_PRO_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?', $log_data); 
        
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
        
        if(!is_null($id)){

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();
			
			$objlastdt          =   $this->getLastdt();
			$objSubLedgerList   =   $this->getSubLedgerList();
			
            $objResponse =  DB::table('TBL_TRN_PDPRO_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
			->where('FYID_REF','=',Session::get('FYID_REF'))
            ->where('PROID','=',$id)
            ->first();
            
            if(strtoupper($objResponse->STATUS)=="A"){
               // exit("Sorry, Approved record can not edit.");
            }

            $objMAT = DB::select("SELECT T1.*,
			T2.ICODE,T2.NAME AS ITEM_NAME,T2.MATERIAL_TYPE,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,
			T3.UOMCODE,T3.DESCRIPTIONS,
			T4.SGLCODE,T4.SLNAME,
			T5.SONO
			FROM TBL_TRN_PDPRO_MAT T1
			LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
			LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
			LEFT JOIN TBL_MST_SUBLEDGER T4 ON T1.SLID_REF=T4.SGLID
			LEFT JOIN TBL_TRN_SLSO01_HDR T5 ON T1.SOID_REF=T5.SOID
			WHERE T1.PROID_REF='$id' ORDER BY T1.PRO_MATID ASC"); 




            //echo "<pre>";
            //print_r($objMAT);die;

            $tempobjMAT  = $objMAT;
            /*
            foreach ($tempobjMAT as $index => $dataRow) {
               
                $ObjTotalSavedQty =   DB::table('TBL_TRN_PDPRO_MAT')
                ->where('SLID_REF','=',$dataRow->SLID_REF)
                ->where('SOID_REF','=',$dataRow->SOID_REF)
                ->where('SQID_REF','=',$dataRow->SQID_REF)
                ->where('SEID_REF','=',$dataRow->SEID_REF)
                ->where('ITEMID_REF','=',$dataRow->ITEMID_REF)
                ->where('UOMID_REF','=',$dataRow->UOMID_REF) 
                ->select(DB::Raw('ISNULL(SUM(PD_OR_QTY),0) AS PD_OR_QTY'))
                ->get();
                $Total_PDORQty = $ObjTotalSavedQty[0]->PD_OR_QTY;

               $ObjSOItem =   DB::table('TBL_TRN_SLSO01_MAT')
                    ->where('TBL_TRN_SLSO01_MAT.SOID_REF','=',$dataRow->SOID_REF)
                    ->where('TBL_TRN_SLSO01_MAT.SQA','=',$dataRow->SQID_REF)
                    ->where('TBL_TRN_SLSO01_MAT.SEQID_REF','=',$dataRow->SEID_REF)
                    ->where('TBL_TRN_SLSO01_MAT.ITEMID_REF','=',$dataRow->ITEMID_REF)      
                    ->where('TBL_TRN_SLSO01_MAT.MAIN_UOMID_REF','=',$dataRow->UOMID_REF)      
                    ->select('TBL_TRN_SLSO01_MAT.*',)                    
                    ->first();

                $SOQTY =  isset($ObjSOItem->SO_QTY)? $ObjSOItem->SO_QTY : 0; 
                $BAL_SOQTY = number_format( floatVal($SOQTY) - floatval($Total_PDORQty), 3,".","" ) ;

                //ADD CONSUMED QTY IN EDIT MODE
                $ObjConsumedQty =   DB::table('TBL_TRN_PDPRO_MAT')
                        ->where('PROID_REF','=',$id)
                        ->where('SLID_REF','=',$dataRow->SLID_REF)
                        ->where('SOID_REF','=',$dataRow->SOID_REF)
                        ->where('SQID_REF','=',$dataRow->SQID_REF)
                        ->where('SEID_REF','=',$dataRow->SEID_REF)
                        ->where('ITEMID_REF','=',$dataRow->ITEMID_REF)
                        ->where('UOMID_REF','=',$dataRow->UOMID_REF)  
                        ->select(DB::Raw('ISNULL(SUM(PD_OR_QTY),0) AS CONSUMED_PD_OR_QTY'))
                        ->get();

                $BAL_SOQTY = number_format( floatVal($BAL_SOQTY) + floatval($ObjConsumedQty[0]->CONSUMED_PD_OR_QTY), 3,".","" ) ;

                $objMAT[$index]->NEW_BAL_SO_QTY = $BAL_SOQTY;
                $objMAT[$index]->PRO_CONSUMED_QTY = $ObjConsumedQty[0]->CONSUMED_PD_OR_QTY;
                
                
            }
            */

            $objREQ = DB::select("SELECT T1.*,
			T2.ICODE,T2.NAME AS ITEM_NAME,T2.MATERIAL_TYPE,
			T3.ICODE AS SOITEMID_CODE
			FROM TBL_TRN_PDPRO_REQ T1
			LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
			LEFT JOIN TBL_MST_ITEM T3 ON T1.SOITEMID_REF=T3.ITEMID
			WHERE T1.PROID_REF='$id' ORDER BY T1.PRO_REQID ASC");
			
			$material_array=array();

            //dump($objREQ);
			
			if(isset($objREQ) && !empty($objREQ)){
				foreach($objREQ as $row){
					
					$ITEMID		=	$row->SOITEMID_REF;
					
					$BOM_HDR =   DB::table('TBL_MST_BOM_HDR')
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF)
                        ->where('ITEMID_REF','=',$ITEMID)
                        ->where('STATUS','=','A')
                        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                        ->select('BOMID')
                        ->first();
						
					$MAIN_PD_OR_QTY	=($row->INPUT_PD_OR_QTY/$row->BOM_QTY);

                    $objMAT2 =   DB::table('TBL_TRN_PDPRO_MAT')
                                ->where('PROID_REF','=',$id)
                                ->where('SOID_REF','=',$row->SOID_REF)
                                ->where('SQID_REF','=',$row->SQID_REF)
                                ->where('SEID_REF','=',$row->SEID_REF)
                                ->where('ITEMID_REF','=',$ITEMID)
                                ->select('SLID_REF')
                                ->first();
                    //dump($objMAT2);            
                    
                   $material_array[]=array(
						'BOMID_REF'=>$BOM_HDR->BOMID,
						'MAIN_PD_OR_QTY'=>$MAIN_PD_OR_QTY,
						'SOITEMID_CODE'=>$row->SOITEMID_CODE,
						'ITEM_NAME'=>$row->ITEM_NAME,
						'ICODE'=>$row->ICODE,
						'SOID_REF'=>$row->SOID_REF,
						'SOITEMID_REF'=>$row->SOITEMID_REF,
						'ITEMID_REF'=>$row->ITEMID_REF,
						'MAIN_ITEMID_REF'=>$row->MAIN_ITEMID_REF,
						'SQID_REF'=>$row->SQID_REF,
						'SEID_REF'=>$row->SEID_REF,
						'BOM_QTY'=>$row->BOM_QTY,
						'INPUT_PD_OR_QTY'=>$row->INPUT_PD_OR_QTY,
						'CHANGES_PD_OR_QTY'=>$row->CHANGES_PD_OR_QTY,
						'SLID_REF'=>isset($objMAT2->SLID_REF)?$objMAT2->SLID_REF:NULL,
                        'MATERIAL_TYPE'=>$row->MATERIAL_TYPE,
					);
				}
						
			}
			
			$objUDF = DB::table('TBL_TRN_PDPRO_UDF')                    
            ->where('PROID_REF','=',$id)
            ->orderBy('PRO_UDFID','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF);
			
		
			$ObjUnionUDF = DB::table("TBL_MST_UDFFOR_PRO")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('UDFPROID')->from('TBL_MST_UDFFOR_PRO')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                     
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                     
                    

            $objUdfData = DB::table('TBL_MST_UDFFOR_PRO')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_PRO")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDFPROID')->from('TBL_MST_UDFFOR_PRO')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                         
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                           
            

            $objUdfData2 = DB::table('TBL_MST_UDFFOR_PRO')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF2)
            ->get()->toArray(); 
		
            $FormId         =   $this->form_id;
            $AlpsStatus =   $this->AlpsStatus();
            $ActionStatus   =   "";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            
        
            return view($this->view.$FormId.'edit',compact([
                'AlpsStatus',
				'FormId',
				'objRights',
				'objlastdt',
				'objSubLedgerList',
				'objResponse',
				'objMAT',
				'material_array',
				'objUDF',
                'objUdfData',
                'objCountUDF',
                'objUdfData2',
                'ActionStatus',
                'TabSetting'
			]));      

        }
     
    }

    public function update(Request $request){
        
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count4 = $request['Row_Count4'];

        
		$req_data=array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i]) && $request['ITEMID_REF_'.$i] !=""){

                $req_data[] = [
                    'PRO_MATID'     => isset($request['PRO_MATID_'.$i]) && $request['PRO_MATID_'.$i] !=""?$request['PRO_MATID_'.$i]:0,
                    'SLID_REF'     => isset($request['SLID_REF_'.$i]) && $request['SLID_REF_'.$i] !=""?$request['SLID_REF_'.$i]:NULL,
                    'SOID_REF'     => isset($request['SOID_REF_'.$i]) && $request['SOID_REF_'.$i] !=""?$request['SOID_REF_'.$i]:NULL,
                    'ITEMID_REF'   => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'    => $request['MAIN_UOMID_REF_'.$i],
                    'SOQTY'        => isset($request['QTY_'.$i]) && $request['QTY_'.$i] !=""?$request['QTY_'.$i]:0,
                    'BL_SOQTY'     => isset($request['BL_SOQTY_'.$i]) && $request['BL_SOQTY_'.$i] !=""?$request['BL_SOQTY_'.$i]:0,
                    'PD_OR_QTY'    => isset($request['PD_OR_QTY_'.$i]) && $request['PD_OR_QTY_'.$i] !=""?$request['PD_OR_QTY_'.$i]:0,
                    'SQID_REF'     => isset($request['SQID_REF_'.$i]) && $request['SQID_REF_'.$i] !=""?$request['SQID_REF_'.$i]:NULL,
                    'SEID_REF'     => isset($request['SEID_REF_'.$i]) && $request['SEID_REF_'.$i] !=""?$request['SEID_REF_'.$i]:NULL,  
                    'BOMID_REF'     => isset($request['BOMID_REF_'.$i]) && $request['BOMID_REF_'.$i] !=""?$request['BOMID_REF_'.$i]:NULL,  
                    'CONSUME_QTY_REF'    => isset($request['BOMDATA_'.$i]) && $request['BOMDATA_'.$i] !=""?$request['BOMDATA_'.$i]:0,  
                ];

            }
        }

        //dd($req_data);
		
        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
		
		$reqdata3=array();
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['UDF_'.$i])){
                $reqdata3[$i] = [
                    'UDFPROID_REF'      => $request['UDF_'.$i],
                    'VALUE'  => $request['udfvalue_'.$i],
                ];
            }
        }
		
        if($r_count2 > 0){
            $wrapped_links3["UDF"] = $reqdata3; 
            $XMLUDF = ArrayToXml::convert($wrapped_links3);
        }
        else{
            $XMLUDF=NULL;
        }

		
		$req_data4=array();
        for ($i=0; $i<=$r_count4; $i++){
            if(isset($request['REQ_SOITEMID_REF_'.$i])){

                $req_data4[$i] = [
                    'SOID_REF'          => isset($request['REQ_SOID_REF_'.$i]) && $request['REQ_SOID_REF_'.$i] !=""?$request['REQ_SOID_REF_'.$i]:NULL,
                    'SOITEMID_REF'      => $request['REQ_SOITEMID_REF_'.$i],
                    'ITEMID_REF'        => $request['REQ_ITEMID_REF_'.$i],
                    'BOM_QTY'           => isset($request['REQ_BOM_QTY_'.$i]) && $request['REQ_BOM_QTY_'.$i] !=""?$request['REQ_BOM_QTY_'.$i]:0,
                    'INPUT_PD_OR_QTY'   => isset($request['REQ_INPUT_PD_OR_QTY_'.$i]) && $request['REQ_INPUT_PD_OR_QTY_'.$i] !=""?$request['REQ_INPUT_PD_OR_QTY_'.$i]:0,
                    'CHANGES_PD_OR_QTY' => isset($request['REQ_CHANGES_PD_OR_QTY_'.$i]) && $request['REQ_CHANGES_PD_OR_QTY_'.$i] !=""?$request['REQ_CHANGES_PD_OR_QTY_'.$i]:0,
					'MAIN_ITEMID_REF'   => $request['REQ_MAIN_ITEMID_REF_'.$i],
                    'SEID_REF'          => isset($request['REQ_SEID_REF_'.$i]) && $request['REQ_SEID_REF_'.$i] !=""?$request['REQ_SEID_REF_'.$i]:NULL,
                    'SQID_REF'          => isset($request['REQ_SQID_REF_'.$i]) && $request['REQ_SQID_REF_'.$i] !=""?$request['REQ_SQID_REF_'.$i]:NULL,
                ];

            }
        }
		


		if($r_count4 > 0){
            $wrapped_links4["REQ"] = $req_data4; 
			$XMLREQ = ArrayToXml::convert($wrapped_links4);
        }
        else{
            $XMLREQ=NULL;
        }

        
        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $PRO_NO     =   $request['PRO_NO'];
        $PRO_DT     =   $request['PRO_DT'];
        $PRO_TITLE  =   $request['PRO_TITLE'];
        $DIRECTPO   =   (isset($request['Direct'])!="true" ? 0 : 1);
        $SELECTIONPARAM =   $request['AllStatus']=="1" ? 1 : 0;
       
        $log_data = [ 
            $PRO_NO,$PRO_DT,$PRO_TITLE,$CYID_REF,$BRID_REF,
            $FYID_REF,$VTID_REF,$XMLMAT,$XMLREQ,$XMLUDF,
            $USERID, Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$DIRECTPO,$SELECTIONPARAM
        ]; 
		
	
        $sp_result = DB::select('EXEC SP_PRO_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?', $log_data);  


        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => 'Record successfully Updated.']);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
    }

    public function view($id){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();
			
			$objlastdt          =   $this->getLastdt();
			$objSubLedgerList   =   $this->getSubLedgerList();
			
            $objResponse =  DB::table('TBL_TRN_PDPRO_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
			->where('FYID_REF','=',Session::get('FYID_REF'))
            ->where('PROID','=',$id)
            ->first();
            
            if(strtoupper($objResponse->STATUS)=="A"){
               // exit("Sorry, Approved record can not edit.");
            }

            $objMAT = DB::select("SELECT T1.*,
			T2.ICODE,T2.NAME AS ITEM_NAME,T2.MATERIAL_TYPE,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,
			T3.UOMCODE,T3.DESCRIPTIONS,
			T4.SGLCODE,T4.SLNAME,
			T5.SONO
			FROM TBL_TRN_PDPRO_MAT T1
			LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
			LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
			LEFT JOIN TBL_MST_SUBLEDGER T4 ON T1.SLID_REF=T4.SGLID
			LEFT JOIN TBL_TRN_SLSO01_HDR T5 ON T1.SOID_REF=T5.SOID
			WHERE T1.PROID_REF='$id' ORDER BY T1.PRO_MATID ASC"); 
 
            $tempobjMAT  = $objMAT;
            /*
            foreach ($tempobjMAT as $index => $dataRow) {
               
                $ObjTotalSavedQty =   DB::table('TBL_TRN_PDPRO_MAT')
                ->where('SLID_REF','=',$dataRow->SLID_REF)
                ->where('SOID_REF','=',$dataRow->SOID_REF)
                ->where('SQID_REF','=',$dataRow->SQID_REF)
                ->where('SEID_REF','=',$dataRow->SEID_REF)
                ->where('ITEMID_REF','=',$dataRow->ITEMID_REF)
                ->where('UOMID_REF','=',$dataRow->UOMID_REF) 
                ->select(DB::Raw('ISNULL(SUM(PD_OR_QTY),0) AS PD_OR_QTY'))
                ->get();
                $Total_PDORQty = $ObjTotalSavedQty[0]->PD_OR_QTY;

               $ObjSOItem =   DB::table('TBL_TRN_SLSO01_MAT')
                    ->where('TBL_TRN_SLSO01_MAT.SOID_REF','=',$dataRow->SOID_REF)
                    ->where('TBL_TRN_SLSO01_MAT.SQA','=',$dataRow->SQID_REF)
                    ->where('TBL_TRN_SLSO01_MAT.SEQID_REF','=',$dataRow->SEID_REF)
                    ->where('TBL_TRN_SLSO01_MAT.ITEMID_REF','=',$dataRow->ITEMID_REF)      
                    ->where('TBL_TRN_SLSO01_MAT.MAIN_UOMID_REF','=',$dataRow->UOMID_REF)      
                    ->select('TBL_TRN_SLSO01_MAT.*',)                    
                    ->first();

                $SOQTY =  isset($ObjSOItem->SO_QTY)? $ObjSOItem->SO_QTY : 0; 
                $BAL_SOQTY = number_format( floatVal($SOQTY) - floatval($Total_PDORQty), 3,".","" ) ;

                //ADD CONSUMED QTY IN EDIT MODE
                $ObjConsumedQty =   DB::table('TBL_TRN_PDPRO_MAT')
                        ->where('PROID_REF','=',$id)
                        ->where('SLID_REF','=',$dataRow->SLID_REF)
                        ->where('SOID_REF','=',$dataRow->SOID_REF)
                        ->where('SQID_REF','=',$dataRow->SQID_REF)
                        ->where('SEID_REF','=',$dataRow->SEID_REF)
                        ->where('ITEMID_REF','=',$dataRow->ITEMID_REF)
                        ->where('UOMID_REF','=',$dataRow->UOMID_REF)  
                        ->select(DB::Raw('ISNULL(SUM(PD_OR_QTY),0) AS CONSUMED_PD_OR_QTY'))
                        ->get();

                $BAL_SOQTY = number_format( floatVal($BAL_SOQTY) + floatval($ObjConsumedQty[0]->CONSUMED_PD_OR_QTY), 3,".","" ) ;

                $objMAT[$index]->NEW_BAL_SO_QTY = $BAL_SOQTY;
                $objMAT[$index]->PRO_CONSUMED_QTY = $ObjConsumedQty[0]->CONSUMED_PD_OR_QTY;
                
                
            }
            */

            $objREQ = DB::select("SELECT T1.*,
			T2.ICODE,T2.NAME AS ITEM_NAME,T2.MATERIAL_TYPE,
			T3.ICODE AS SOITEMID_CODE
			FROM TBL_TRN_PDPRO_REQ T1
			LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
			LEFT JOIN TBL_MST_ITEM T3 ON T1.SOITEMID_REF=T3.ITEMID
			WHERE T1.PROID_REF='$id' ORDER BY T1.PRO_REQID ASC");
			
			$material_array=array();

            //dump($objREQ);
			
			if(isset($objREQ) && !empty($objREQ)){
				foreach($objREQ as $row){
					
					$ITEMID		=	$row->SOITEMID_REF;
					
					$BOM_HDR =   DB::table('TBL_MST_BOM_HDR')
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF)
                        ->where('ITEMID_REF','=',$ITEMID)
                        ->where('STATUS','=','A')
                        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                        ->select('BOMID')
                        ->first();
						
					$MAIN_PD_OR_QTY	=($row->INPUT_PD_OR_QTY/$row->BOM_QTY);

                    $objMAT2 =   DB::table('TBL_TRN_PDPRO_MAT')
                                ->where('PROID_REF','=',$id)
                                ->where('SOID_REF','=',$row->SOID_REF)
                                ->where('SQID_REF','=',$row->SQID_REF)
                                ->where('SEID_REF','=',$row->SEID_REF)
                                ->where('ITEMID_REF','=',$ITEMID)
                                ->select('SLID_REF')
                                ->first();
                    //dump($objMAT2);            
                    
                   $material_array[]=array(
						'BOMID_REF'=>$BOM_HDR->BOMID,
						'MAIN_PD_OR_QTY'=>$MAIN_PD_OR_QTY,
						'SOITEMID_CODE'=>$row->SOITEMID_CODE,
						'ITEM_NAME'=>$row->ITEM_NAME,
						'ICODE'=>$row->ICODE,
						'SOID_REF'=>$row->SOID_REF,
						'SOITEMID_REF'=>$row->SOITEMID_REF,
						'ITEMID_REF'=>$row->ITEMID_REF,
						'MAIN_ITEMID_REF'=>$row->MAIN_ITEMID_REF,
						'SQID_REF'=>$row->SQID_REF,
						'SEID_REF'=>$row->SEID_REF,
						'BOM_QTY'=>$row->BOM_QTY,
						'INPUT_PD_OR_QTY'=>$row->INPUT_PD_OR_QTY,
						'CHANGES_PD_OR_QTY'=>$row->CHANGES_PD_OR_QTY,
						'SLID_REF'=>isset($objMAT2->SLID_REF)?$objMAT2->SLID_REF:NULL,
                        'MATERIAL_TYPE'=>$row->MATERIAL_TYPE,
					);
				}
						
			}
			
			$objUDF = DB::table('TBL_TRN_PDPRO_UDF')                    
            ->where('PROID_REF','=',$id)
            ->orderBy('PRO_UDFID','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF);
			
		
			$ObjUnionUDF = DB::table("TBL_MST_UDFFOR_PRO")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('UDFPROID')->from('TBL_MST_UDFFOR_PRO')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                     
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                     
                    

            $objUdfData = DB::table('TBL_MST_UDFFOR_PRO')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_PRO")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDFPROID')->from('TBL_MST_UDFFOR_PRO')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                         
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                           
            

            $objUdfData2 = DB::table('TBL_MST_UDFFOR_PRO')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF2)
            ->get()->toArray(); 
		
            $FormId         =   $this->form_id;
            $AlpsStatus =   $this->AlpsStatus();
            $ActionStatus   =   "disabled";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
            return view($this->view.$FormId.'view',compact([
                'AlpsStatus',
				'FormId',
				'objRights',
				'objlastdt',
				'objSubLedgerList',
				'objResponse',
				'objMAT',
				'material_array',
				'objUDF',
                'objUdfData',
                'objCountUDF',
                'objUdfData2',
                'ActionStatus',
                'TabSetting'
			]));      

        }
     
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

        if(!empty($sp_listing_result)){
            foreach ($sp_listing_result as $key=>$valueitem){  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$valueitem->LAVELS;
            }
        }
   
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count4 = $request['Row_Count4'];
        
		$req_data=array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $req_data[$i] = [
                    'PRO_MATID'     => isset($request['PRO_MATID_'.$i]) && $request['PRO_MATID_'.$i] !=""?$request['PRO_MATID_'.$i]:0,
                    'SLID_REF'     => isset($request['SLID_REF_'.$i]) && $request['SLID_REF_'.$i] !=""?$request['SLID_REF_'.$i]:NULL,
                    'SOID_REF'     => isset($request['SOID_REF_'.$i]) && $request['SOID_REF_'.$i] !=""?$request['SOID_REF_'.$i]:NULL,
                    'ITEMID_REF'   => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'    => $request['MAIN_UOMID_REF_'.$i],
                    'SOQTY'        => isset($request['QTY_'.$i]) && $request['QTY_'.$i] !=""?$request['QTY_'.$i]:0,
                    'BL_SOQTY'     => isset($request['BL_SOQTY_'.$i]) && $request['BL_SOQTY_'.$i] !=""?$request['BL_SOQTY_'.$i]:0,
                    'PD_OR_QTY'    => isset($request['PD_OR_QTY_'.$i]) && $request['PD_OR_QTY_'.$i] !=""?$request['PD_OR_QTY_'.$i]:0,
                    'SQID_REF'     => isset($request['SQID_REF_'.$i]) && $request['SQID_REF_'.$i] !=""?$request['SQID_REF_'.$i]:NULL,
                    'SEID_REF'     => isset($request['SEID_REF_'.$i]) && $request['SEID_REF_'.$i] !=""?$request['SEID_REF_'.$i]:NULL,  
                    'BOMID_REF'     => isset($request['BOMID_REF_'.$i]) && $request['BOMID_REF_'.$i] !=""?$request['BOMID_REF_'.$i]:NULL,  
                    'CONSUME_QTY_REF'    => isset($request['BOMDATA_'.$i]) && $request['BOMDATA_'.$i] !=""?$request['BOMDATA_'.$i]:0,   
                ];

            }
        }
		
        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
		
		$reqdata3=array();
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['UDF_'.$i])){
                $reqdata3[$i] = [
                    'UDFPROID_REF'      => $request['UDF_'.$i],
                    'VALUE'  => $request['udfvalue_'.$i],
                ];
            }
        }

        if($r_count2 > 0){
            $wrapped_links3["UDF"] = $reqdata3; 
            $XMLUDF = ArrayToXml::convert($wrapped_links3);
        }
        else{
            $XMLUDF=NULL;
        }

		
		$req_data4=array();
        for ($i=0; $i<=$r_count4; $i++){
            if(isset($request['REQ_SOITEMID_REF_'.$i])){

                $req_data4[$i] = [
                    'SOID_REF'          => isset($request['REQ_SOID_REF_'.$i]) && $request['REQ_SOID_REF_'.$i] !=""?$request['REQ_SOID_REF_'.$i]:NULL,
                    'SOITEMID_REF'      => $request['REQ_SOITEMID_REF_'.$i],
                    'ITEMID_REF'        => $request['REQ_ITEMID_REF_'.$i],
                    'BOM_QTY'           => isset($request['REQ_BOM_QTY_'.$i]) && $request['REQ_BOM_QTY_'.$i] !=""?$request['REQ_BOM_QTY_'.$i]:0,
                    'INPUT_PD_OR_QTY'   => isset($request['REQ_INPUT_PD_OR_QTY_'.$i]) && $request['REQ_INPUT_PD_OR_QTY_'.$i] !=""?$request['REQ_INPUT_PD_OR_QTY_'.$i]:0,
                    'CHANGES_PD_OR_QTY' => isset($request['REQ_CHANGES_PD_OR_QTY_'.$i]) && $request['REQ_CHANGES_PD_OR_QTY_'.$i] !=""?$request['REQ_CHANGES_PD_OR_QTY_'.$i]:0,
					'MAIN_ITEMID_REF'   => $request['REQ_MAIN_ITEMID_REF_'.$i],
                    'SEID_REF'          => isset($request['REQ_SEID_REF_'.$i]) && $request['REQ_SEID_REF_'.$i] !=""?$request['REQ_SEID_REF_'.$i]:NULL,
                    'SQID_REF'          => isset($request['REQ_SQID_REF_'.$i]) && $request['REQ_SQID_REF_'.$i] !=""?$request['REQ_SQID_REF_'.$i]:NULL,
                ];

            }
        }
		


		if($r_count4 > 0){
            $wrapped_links4["REQ"] = $req_data4; 
			$XMLREQ = ArrayToXml::convert($wrapped_links4);
        }
        else{
            $XMLREQ=NULL;
        }


        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $PRO_NO     =   $request['PRO_NO'];
        $PRO_DT     =   $request['PRO_DT'];
        $PRO_TITLE  =   $request['PRO_TITLE'];
        $DIRECTPO   =   (isset($request['Direct'])!="true" ? 0 : 1);
        $SELECTIONPARAM =   $request['AllStatus']=="1" ? 1 : 0;
       
        $log_data = [ 
            $PRO_NO,$PRO_DT,$PRO_TITLE,$CYID_REF,$BRID_REF,
            $FYID_REF,$VTID_REF,$XMLMAT,$XMLREQ,$XMLUDF,
            $USERID, Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$DIRECTPO,$SELECTIONPARAM
        ]; 
	
        $sp_result = DB::select('EXEC SP_PRO_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?', $log_data);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' =>'Record successfully Approved.']);
          
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

        if(!empty($sp_listing_result)){
            foreach ($sp_listing_result as $key=>$valueitem){  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$valueitem->LAVELS;
            }
        }
               
        $req_data =  json_decode($request['ID']);

        $wrapped_links = $req_data; 
        $multi_array = $wrapped_links;
        $iddata = [];
        
        foreach($multi_array as $index=>$row){
            $m_array[$index] = $row->ID;
            $iddata['APPROVAL'][]['ID'] =  $row->ID;
        }

        $xml = ArrayToXml::convert($iddata);
                
        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_TRN_PDPRO_HDR";
        $FIELD      =   "PROID";
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
        $TABLE      =   "TBL_TRN_PDPRO_HDR";
        $FIELD      =   "PROID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
		
        $req_data[0]=[
         'NT'  => 'TBL_TRN_PDPRO_MAT',
        ];
        $req_data[1]=[
        'NT'  => 'TBL_TRN_PDPRO_REQ',
        ];
		$req_data[2]=[
        'NT'  => 'TBL_TRN_PDPRO_UDF',
        ];

        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_PRO  ?,?,?,?, ?,?,?,?, ?,?,?,?', $cancel_data);

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

            $objResponse = DB::table('TBL_TRN_PDPRO_HDR')->where('PROID','=',$id)->first();

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
        
		$image_path         =   "docs/company".$CYID_REF."/ProductionOrder";     
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
   

    public function codeduplicate(Request $request){

        $PRO_NO  =   trim($request['PRO_NO']);
        $objLabel = DB::table('TBL_TRN_PDPRO_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('PRO_NO','=',$PRO_NO)
        ->select('PROID')->first();

        if($objLabel){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }

    public function getLastdt(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        return  DB::select('SELECT MAX(PRO_DT) PRO_DT FROM TBL_TRN_PDPRO_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
    }


    
    /*
    public function geBomItem($ITEMID,$SLID_REF,$SOID_REF){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $StdCost        =   0;
        $AlpsStatus     =   $this->AlpsStatus();


        $FG_SFG_ITEM    =   DB::table('TBL_MST_ITEM')
                            ->where('ITEMID','=',$ITEMID)
                            ->whereRaw("(MATERIAL_TYPE='FG-Finish Good' OR MATERIAL_TYPE='SFG- Semi Finish Good')")
                            ->count();

        $BOM_HDR        =   DB::table('TBL_MST_BOM_HDR')
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('BRID_REF','=',$BRID_REF)
                            ->where('FYID_REF','=',$FYID_REF)
                            ->where('ITEMID_REF','=',$ITEMID)
                            ->where('STATUS','=','A')
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('BOMID')
                            ->first();

        if($FG_SFG_ITEM > 0 && !empty($BOM_HDR)){
            
            $BOMID      =   $BOM_HDR->BOMID;

            $BOM_MAT    =   DB::select("SELECT T1.*
                            FROM TBL_MST_BOM_MAT T1 
                            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                            WHERE T1.BOMID_REF='$BOMID'");

            $ObjItem    =   DB::select("SELECT 
                            T1.BOM_MATID AS MATID,'' AS SOID_REF,T1.UOMID_REF AS MAIN_UOMID_REF,'' AS ALT_UOMID_REF,T1.CONSUME_QTY AS SO_QTY,'' AS SQA, '' AS SEQID_REF,
                            T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI,T2.MATERIAL_TYPE
                            FROM TBL_MST_BOM_MAT T1 
                            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                            WHERE T1.BOMID_REF='$BOMID'");

            $row = '';
            if(!empty($ObjItem)){
               
                foreach ($ObjItem as $index=>$dataRow){

                    $SOQTY      =   $dataRow->SO_QTY !=''?$dataRow->SO_QTY : 0;   
                    $FROMQTY    =   $dataRow->SO_QTY !=''?$dataRow->SO_QTY : 0;
        
                    $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS 
                    FROM TBL_MST_UOM  
                    WHERE  CYID_REF = ?  AND UOMID = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                    [$CYID_REF, $dataRow->MAIN_UOMID_REF, 'A' ]);          
                    
                    $ObjItemGroup =  DB::select('SELECT TOP 1 GROUPCODE, GROUPNAME 
                    FROM TBL_MST_ITEMGROUP  
                    WHERE  CYID_REF = ?  AND ITEMGID = ?
                    AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                    [$CYID_REF, $dataRow->ITEMGID_REF, 'A' ]);

                    $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS 
                    FROM TBL_MST_ITEMCATEGORY  
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

                    $item_unique_row_id  =   $SLID_REF."_".$SOID_REF."_".$dataRow->SQA."_".$dataRow->SEQID_REF."_".$dataRow->ITEMID;
       
                    $row.= '<tr id="item_00'.$index.'"  class="clsitemid">
                        <td style="width:8%;text-align:center;"><input type="checkbox" id="chkId00'.$index.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>
                        <td style="width:10%;">'.$dataRow->ICODE.'&nbsp;&nbsp;'.$dataRow->MATERIAL_TYPE.'</td>
                        <td style="width:10%;">'.$dataRow->NAME.'</td>
                        <td style="width:8%;">'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>
                        <td style="width:8%;">'.$FROMQTY.'</td>
                        <td style="width:8%;">'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>
                        <td style="width:8%;">'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                        <td style="width:8%;">'.$BusinessUnit.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                        <td style="width:8%;">Authorized</td>
                        <td hidden>
                            <input type="text" id="txtitem_00'.$index.'" 
                                data-desc1="'.$dataRow->ITEMID.'" 
                                data-desc2="'.$dataRow->ICODE.'" 
                                data-desc3="'.$dataRow->NAME.'" 
                                data-desc4="'.$dataRow->MAIN_UOMID_REF.'" 
                                data-desc5="'.$ObjMainUOM[0]->DESCRIPTIONS.'" 
                                data-desc6="'.$FROMQTY.'" 
                                data-desc7="'.$item_unique_row_id.'" 
                                data-desc8="'.$dataRow->SQA.'"
                                data-desc9="'.$dataRow->SEQID_REF.'"
                                data-desc10="'.$SLID_REF.'"
                                data-desc11="'.$SOID_REF.'"
                                data-desc12="'.$SOQTY.'"
                                data-desc13="SFG"
                                data-desc14="'.$FROMQTY.'"
                                data-desc15="'.$dataRow->MATID.'"
                            />
                        </td>
                    </tr>';

                   

                } 

                return $row;
                
            }

        }

    }
    */

    
    /*
    public function getItemDetailsLevel2(Request $request){

        $AlpsStatus =   $this->AlpsStatus();
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $ITEMID     =   $request['ITEMID_REF'];
        $StdCost    =   0;

        $BOM_MAT    =   DB::select("SELECT 
                                T1.BOM_MATID,T1.BOMID_REF,T1.ITEMID_REF,T1.CONSUME_QTY,
                                T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI,T2.MATERIAL_TYPE,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF
                                FROM TBL_MST_BOM_MAT T1 
                                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                                WHERE T1.BOMID_REF=(SELECT BOMID FROM TBL_MST_BOM_HDR WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' 
                                AND FYID_REF='$FYID_REF' AND STATUS='A' AND ITEMID_REF='$ITEMID' AND (DEACTIVATED=0 or DEACTIVATED is null))");
                
        if(isset($BOM_MAT) && !empty($BOM_MAT)){
            foreach($BOM_MAT as $row){

                $ITEMID         =   $row->ITEMID_REF;
                $ICODE          =   $row->ICODE;
                $CONSUME_QTY    =   $row->CONSUME_QTY;
                $BOM_MAT        =   DB::select("SELECT 
                                    T1.BOM_MATID,T1.BOMID_REF,T1.ITEMID_REF,T1.CONSUME_QTY,
                                    T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI,T2.MATERIAL_TYPE,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF
                                    FROM TBL_MST_BOM_MAT T1 
                                    LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                                    WHERE T1.BOMID_REF=(SELECT BOMID FROM TBL_MST_BOM_HDR WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' 
                                    AND FYID_REF='$FYID_REF' AND STATUS='A' AND ITEMID_REF='$ITEMID' AND (DEACTIVATED=0 or DEACTIVATED is null))");

                if(isset($BOM_MAT) && !empty($BOM_MAT)){
                    foreach ($BOM_MAT as $index=>$dataRow){

                        $dataid =   $dataRow->BOM_MATID."_".$ITEMID."_".$ICODE."_".$CONSUME_QTY;

                        $SLID_REF=NULL;
                        $SOID_REF=NULL;
                        $SQID=NULL;
                        $SEID=NULL;
                        $SOQTY      =   $dataRow->CONSUME_QTY;   
                        $FROMQTY    =   $dataRow->CONSUME_QTY;
                
                        $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS 
                        FROM TBL_MST_UOM  
                        WHERE  CYID_REF = ?  AND UOMID = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                        [$CYID_REF, $dataRow->MAIN_UOMID_REF, 'A' ]);          
                
                        $ObjItemGroup =  DB::select('SELECT TOP 1 GROUPCODE, GROUPNAME 
                        FROM TBL_MST_ITEMGROUP  
                        WHERE  CYID_REF = ?  AND ITEMGID = ?
                        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                        [$CYID_REF, $dataRow->ITEMGID_REF, 'A' ]);

                        $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS 
                        FROM TBL_MST_ITEMCATEGORY  
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

                        $item_unique_row_id  =   $SLID_REF."_".$SOID_REF."_".$SQID."_".$SEID."_".$dataRow->ITEMID;

                        $row = '';

                        $row = $row.'
                        <tr id="item_'.$index.'"  class="clsitemid">
                            <td style="width:8%;text-align:center;"><input type="checkbox" name="selectAll[]" id="chkId'.$index.'"  value="'.$dataid.'" class="checkboxClass" onChange="getSubItemId()" ></td>
                            <td style="width:10%;">'.$dataRow->ICODE.'&nbsp;&nbsp;'.$dataRow->MATERIAL_TYPE.'</td>
                            <td style="width:10%;">'.$dataRow->NAME.'</td>
                            <td style="width:8%;">'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>
                            <td style="width:8%;">'.$FROMQTY.'</td>
                            <td style="width:8%;">'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>
                            <td style="width:8%;">'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                            <td style="width:8%;">'.$BusinessUnit.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                            <td style="width:8%;">Authorized</td>
                            <td hidden>
                                <input type="text" id="txtitem_'.$index.'" 
                                    data-desc1="'.$dataRow->ITEMID.'" 
                                    data-desc2="'.$dataRow->ICODE.'" 
                                    data-desc3="'.$dataRow->NAME.'" 
                                    data-desc4="'.$dataRow->MAIN_UOMID_REF.'" 
                                    data-desc5="'.$ObjMainUOM[0]->DESCRIPTIONS.'" 
                                    data-desc6="'.$FROMQTY.'" 
                                    data-desc7="'.$item_unique_row_id.'" 
                                    data-desc8="'.$SQID.'"
                                    data-desc9="'.$SEID.'"
                                    data-desc10="'.$SLID_REF.'"
                                    data-desc11="'.$SOID_REF.'"
                                    data-desc12="'.$SOQTY.'"
                                    data-desc13="FG"
                                    data-desc14="'.$FROMQTY.'" 
                                    data-desc15="'.$dataRow->BOM_MATID.'"
                                />
                            </td>
                        </tr>';

                        echo $row;

                    }
                }
           
            }
        }
        else{
            echo '<tr><td> Record not found.</td></tr>';
        }
        exit();
    }
    */

    /*
    function get_level2_item($ITEMID){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $BOM_HDR        =   DB::table('TBL_MST_BOM_HDR')
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('BRID_REF','=',$BRID_REF)
                            ->where('FYID_REF','=',$FYID_REF)
                            ->where('ITEMID_REF','=',$ITEMID)
                            ->where('STATUS','=','A')
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('BOMID')
                            ->first();    

        if(isset($BOM_HDR) && !empty($BOM_HDR)){

            $BOMID  =   $BOM_HDR->BOMID;

            $BOM_MAT    =   DB::select("SELECT 
                            T1.BOM_MATID,T1.BOMID_REF,T1.ITEMID_REF,T1.CONSUME_QTY,
                            T2.ICODE,T2.NAME,T2.MATERIAL_TYPE
                            FROM TBL_MST_BOM_MAT T1 
                            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                            WHERE T1.BOMID_REF='$BOMID'");
								
								          
                if(isset($BOM_MAT) && !empty($BOM_MAT)){
                    foreach($BOM_MAT as $row){

                        $material_array[]=array(
                            'BOM_MATID'=>$row->BOM_MATID,
                            'BOMID_REF'=>$row->BOMID_REF,
                            'ITEMID_REF'=>$row->ITEMID_REF,
                            'CONSUME_QTY'=>$row->CONSUME_QTY,
                            'ICODE'=>$row->ICODE,
                            'NAME'=>$row->NAME,
                            'MAIN_SLID'=>$SLID,
                            'MAIN_SOID'=>$SOID,
                            'MAIN_ITEMID'=>$ITEMID,
                            'MAIN_ITEMCODE'=>$ITEMCODE,
                            'MAIN_PD_OR_QTY'=>$PD_OR_QTY,
                            'MAIN_SQID'=>$SQID,
                            'MAIN_SEID'=>$SEID,
                            'MAIN_ITEM_ROWID'=>$mitem_id ,
                            'MATERIAL_TYPE'=>$row->MATERIAL_TYPE,
                        );

                    }
                }

            }


            return $material_array;

    }
    */

    
    public function getAltUmQty($id,$itemid,$mqty){

        $ObjData =  DB::select('SELECT top 1 TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV WHERE  ITEMID_REF = ? AND TO_UOMID_REF =?', [$itemid,$id]);
    
        if(!empty($ObjData)){
            $auomqty = ($mqty/$ObjData[0]->FROM_QTY)*($ObjData[0]->TO_QTY);
            return $auomqty;
        }else{
           return 0;
        }
    }

    public function changeAltUm(Request $request){

        $id       = $request['altumid'];
        $itemid   = $request['itemid'];
        $mqty     = $request['mqty'];

        $ObjData =  DB::select('SELECT top 1 TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV WHERE  ITEMID_REF = ? AND TO_UOMID_REF =?', [$itemid,$id]);
    
        if(!empty($ObjData)){
            $auomqty = ($mqty/$ObjData[0]->FROM_QTY)*($ObjData[0]->TO_QTY);
            echo $auomqty;
        }else{
            echo '0';
        }
        exit();
    }

    

    public function getSubLedgerList(){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        return DB::select("SELECT T2.SGLID,T2.SGLCODE,T2.SLNAME 
        FROM TBL_MST_CUSTOMER T1 
        INNER JOIN TBL_MST_SUBLEDGER T2 ON T1.SLID_REF=T2.SGLID
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' 
        AND T1.STATUS='A' AND T1.SLID_REF IS NOT NULL AND ( T1.DEACTIVATED IS NULL OR T1.DEACTIVATED = 0 )
        "); 
    }

    public function getSOCodeNo(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $SLID_REF       =   $request['id'];
        $PROID      =   isset($request['PROID'])?$request['PROID']:0;
        
        $ObjData        =   DB::select("SELECT SOID,SONO,SODT 
                            FROM TBL_TRN_SLSO01_HDR 
                            WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF' 
                            AND SLID_REF='$SLID_REF' AND STATUS='A'");
               
        
        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){

                /*$ObjSOItem =  DB::select("SELECT T1.SOMATID,T1.SOID_REF,T1.MAIN_UOMID_REF,T1.ALT_UOMID_REF,T1.SO_QTY,T1.SQA,T1.SEQID_REF,
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI
                FROM TBL_TRN_SLSO01_MAT T1
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                WHERE T1.SOID_REF=$dataRow->SOID");
                */

                $ObjSOItem =  DB::select("SELECT *  FROM TBL_TRN_SLSO01_MAT 
                               WHERE SOID_REF=$dataRow->SOID");

                //dump($ObjSOItem);

                $addSO = [];
                foreach ($ObjSOItem as $index2=>$dataRow2){
                    
                    $ObjSavedQty = [];
                    $ObjSavedQty =   DB::table('TBL_TRN_PDPRO_MAT')
						->leftJoin('TBL_TRN_PDPRO_HDR', 'TBL_TRN_PDPRO_HDR.PROID','=','TBL_TRN_PDPRO_MAT.PROID_REF')
						->where('TBL_TRN_PDPRO_HDR.STATUS','=','A')
                        ->where('TBL_TRN_PDPRO_MAT.SLID_REF','=',$SLID_REF)
                        ->where('TBL_TRN_PDPRO_MAT.SOID_REF','=',$dataRow2->SOID_REF)
                        ->where('TBL_TRN_PDPRO_MAT.SQID_REF','=',$dataRow2->SQA)
                        ->where('TBL_TRN_PDPRO_MAT.SEID_REF','=',$dataRow2->SEQID_REF)
                        ->where('TBL_TRN_PDPRO_MAT.ITEMID_REF','=',$dataRow2->ITEMID_REF)
                        ->where('TBL_TRN_PDPRO_MAT.UOMID_REF','=',$dataRow2->MAIN_UOMID_REF)  
                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDPRO_MAT.PD_OR_QTY),0) AS PD_OR_QTY'))                       
                        ->get();

                        $CONSQTY=0;
                        if($PROID>0){
                            $ObjConsumedQty = [];
                            $ObjConsumedQty =   DB::table('TBL_TRN_PDPRO_MAT')
								->leftJoin('TBL_TRN_PDPRO_HDR', 'TBL_TRN_PDPRO_HDR.PROID','=','TBL_TRN_PDPRO_MAT.PROID_REF')
								->where('TBL_TRN_PDPRO_HDR.STATUS','=','A')
                                ->where('TBL_TRN_PDPRO_MAT.PROID_REF','=',$PROID)
                                ->where('TBL_TRN_PDPRO_MAT.SLID_REF','=',$SLID_REF)
                                ->where('TBL_TRN_PDPRO_MAT.SOID_REF','=',$dataRow2->SOID_REF)
                                ->where('TBL_TRN_PDPRO_MAT.SQID_REF','=',$dataRow2->SQA)
                                ->where('TBL_TRN_PDPRO_MAT.SEID_REF','=',$dataRow2->SEQID_REF)
                                ->where('TBL_TRN_PDPRO_MAT.ITEMID_REF','=',$dataRow2->ITEMID_REF)
                                ->where('TBL_TRN_PDPRO_MAT.UOMID_REF','=',$dataRow2->MAIN_UOMID_REF)  
                                ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDPRO_MAT.PD_OR_QTY),0) AS CONSUMED_PD_OR_QTY'))
                                ->get();
                             $CONSQTY=         $ObjConsumedQty[0]->CONSUMED_PD_OR_QTY;
                        }
                        
                        
                                                
                        //MY CONSUMED QTY IN EDIT CASE
                        $TOTAL_QTY = number_format( floatval($ObjSavedQty[0]->PD_OR_QTY) - floatval($CONSQTY), 3,".","" ) ;

                       //echo "<br>doid=".$dataRow2->SOID_REF." soitem==".$dataRow2->SO_QTY." Saved=".$ObjSavedQty[0]->PD_OR_QTY." CONS=".$ObjConsumedQty[0]->CONSUMED_PD_OR_QTY." TOQTY=".$TOTAL_QTY;
                       
                        if(floatval($dataRow2->SO_QTY)>floatval($TOTAL_QTY)){
                            $addSO[]=true;
                        }else
                        {
                            $addSO[]=false;
                        }    
                }
                   // dump($addSO);
                    if(in_array('true',$addSO)){
                        $row = '';
                        $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_SOID[]" id="socode_'.$index.'" class="clssSOid" value="'.$dataRow-> SOID.'" ></td>';
                        $row = $row.'<td class="ROW2">'.$dataRow->SONO;
                        $row = $row.'<input type="hidden" id="txtsocode_'.$index.'" data-desc="'.$dataRow->SONO.'" 
                        value="'.$dataRow->SOID.'"/></td><td class="ROW3">'.$dataRow->SODT.'</td></tr>';
                        echo $row;
                    }                
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
        $Status     =   $request['status'];
        $SLID_REF   =   $request['SLID_REF'];
        $SOID_REF   =   $request['SOID_REF'];
        $PROID      =   isset($request['PROID'])?$request['PROID']:0;
        $StdCost    =   0;

        $AlpsStatus =   $this->AlpsStatus();

        $ObjItem =  DB::select("SELECT T1.SOMATID AS MATID,T1.SOID_REF,T1.MAIN_UOMID_REF,T1.ALT_UOMID_REF,T1.SO_QTY,T1.SQA,T1.SEQID_REF,
        T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI,T2.MATERIAL_TYPE
        FROM TBL_TRN_SLSO01_MAT T1
        LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
        WHERE T1.SOID_REF='$SOID_REF'");
		
        if(!empty($ObjItem)){

            foreach ($ObjItem as $index=>$dataRow){

                $ObjSavedQty =   DB::table('TBL_TRN_PDPRO_MAT')
				->leftJoin('TBL_TRN_PDPRO_HDR', 'TBL_TRN_PDPRO_HDR.PROID','=','TBL_TRN_PDPRO_MAT.PROID_REF')
				->where('TBL_TRN_PDPRO_HDR.STATUS','=','A')
                ->where('TBL_TRN_PDPRO_MAT.SLID_REF','=',$SLID_REF)
                ->where('TBL_TRN_PDPRO_MAT.SOID_REF','=',$SOID_REF)
                ->where('TBL_TRN_PDPRO_MAT.SQID_REF','=',$dataRow->SQA)
                ->where('TBL_TRN_PDPRO_MAT.SEID_REF','=',$dataRow->SEQID_REF)
                ->where('TBL_TRN_PDPRO_MAT.ITEMID_REF','=',$dataRow->ITEMID)
                ->where('TBL_TRN_PDPRO_MAT.UOMID_REF','=',$dataRow->MAIN_UOMID_REF)  
                ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDPRO_MAT.PD_OR_QTY),0) AS PD_OR_QTY'))
                ->get();
				
                $SOQTY =  isset($dataRow->SO_QTY)? $dataRow->SO_QTY : 0.000;   
                $FROMQTY = floatVal($SOQTY) - floatval($ObjSavedQty[0]->PD_OR_QTY);
                
                $ObjConsumedQty =   DB::table('TBL_TRN_PDPRO_MAT')
						->leftJoin('TBL_TRN_PDPRO_HDR', 'TBL_TRN_PDPRO_HDR.PROID','=','TBL_TRN_PDPRO_MAT.PROID_REF')
						->where('TBL_TRN_PDPRO_HDR.STATUS','=','A')
                        ->where('TBL_TRN_PDPRO_MAT.PROID_REF','=',$PROID)
                        ->where('TBL_TRN_PDPRO_MAT.SLID_REF','=',$SLID_REF)
                        ->where('TBL_TRN_PDPRO_MAT.SOID_REF','=',$SOID_REF)
                        ->where('TBL_TRN_PDPRO_MAT.SQID_REF','=',$dataRow->SQA)
                        ->where('TBL_TRN_PDPRO_MAT.SEID_REF','=',$dataRow->SEQID_REF)
                        ->where('TBL_TRN_PDPRO_MAT.ITEMID_REF','=',$dataRow->ITEMID)
                        ->where('TBL_TRN_PDPRO_MAT.UOMID_REF','=',$dataRow->MAIN_UOMID_REF) 
                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDPRO_MAT.PD_OR_QTY),0) AS CONSUMED_PD_OR_QTY'))
                        ->get();

                $FROMQTY =  floatVal($FROMQTY) + floatval($ObjConsumedQty[0]->CONSUMED_PD_OR_QTY);
                     
                $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS 
                FROM TBL_MST_UOM  
                WHERE  CYID_REF = ?  AND UOMID = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                [$CYID_REF, $dataRow->MAIN_UOMID_REF, 'A' ]);          
                
                $ObjItemGroup =  DB::select('SELECT TOP 1 GROUPCODE, GROUPNAME 
                FROM TBL_MST_ITEMGROUP  
                WHERE  CYID_REF = ?  AND ITEMGID = ?
                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                [$CYID_REF, $dataRow->ITEMGID_REF, 'A' ]);

                $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS 
                FROM TBL_MST_ITEMCATEGORY  
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



                $item_unique_row_id  =   $SLID_REF."_".$SOID_REF."_".$dataRow->SQA."_".$dataRow->SEQID_REF."_".$dataRow->ITEMID;
               

                $row = '';

                $row = $row.'
                <tr id="item_'.$index.'"  class="clsitemid">
                    <td style="width:8%;text-align:center;"><input type="checkbox" id="chkId'.$index.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>
                    <td style="width:10%;">'.$dataRow->ICODE.'&nbsp;&nbsp;'.$dataRow->MATERIAL_TYPE.'</td>
                    <td style="width:10%;">'.$dataRow->NAME.'</td>
                    <td style="width:8%;">'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>
                    <td style="width:8%;">'.$FROMQTY.'</td>
                    <td style="width:8%;">'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>
                    <td style="width:8%;">'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                    <td style="width:8%;">'.$BusinessUnit.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                    <td style="width:8%;">Authorized</td>
                    <td hidden>
                        <input type="text" id="txtitem_'.$index.'" 
                            data-desc1="'.$dataRow->ITEMID.'" 
                            data-desc2="'.$dataRow->ICODE.'" 
                            data-desc3="'.$dataRow->NAME.'" 
                            data-desc4="'.$dataRow->MAIN_UOMID_REF.'" 
                            data-desc5="'.$ObjMainUOM[0]->DESCRIPTIONS.'" 
                            data-desc6="'.$FROMQTY.'" 
                            data-desc7="'.$item_unique_row_id.'" 
                            data-desc8="'.$dataRow->SQA.'"
                            data-desc9="'.$dataRow->SEQID_REF.'"
                            data-desc10="'.$SLID_REF.'"
                            data-desc11="'.$SOID_REF.'"
                            data-desc12="'.$SOQTY.'"
                            data-desc13="FG"
                            data-desc14="'.$FROMQTY.'" 
                            data-desc15="'.$dataRow->MATID.'"
                        />
                        <td hidden><input type="hidde" id="addinfoitem_'.$index.'"  data-desc101="'.$ALPS_PART_NO.'" data-desc102="'.$CUSTOMER_PART_NO.'" data-desc103="'.$OEM_PART_NO.'" ></td>
                    </td>
                </tr>';

                //$BomItem    =   $this->geBomItem($dataRow->ITEMID,$SLID_REF,$SOID_REF);
                //echo $row.$BomItem;
                echo $row;

            }         
        }           
        else{
            echo '<tr><td> Record not found.</td></tr>';
        }
        exit();
    }

    public function getItemDetails2(Request $request){

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

                $MATERIAL_TYPE      =   isset($dataRow->MATERIAL_TYPE)?$dataRow->MATERIAL_TYPE:NULL;

                
                $SLID_REF           =   NULL;
                $SOID_REF           =   NULL;
                $SQA                =   NULL;
                $SEQID_REF          =   NULL;
                $item_unique_row_id =   $SLID_REF."_".$SOID_REF."_".$SQA."_".$SEQID_REF."_".$ITEMID;
     
                $row.=' <tr id="item_'.$index.'"  class="clsitemid">
                        <td style="width:8%;text-align:center;"><input type="checkbox" id="chkId'.$index.'"  value="'.$ITEMID.'" class="js-selectall1"  ></td>
                        <td style="width:10%;">'.$ICODE.'&nbsp;&nbsp;'.$MATERIAL_TYPE.'</td>
                        <td style="width:10%;">'.$NAME.'</td>
                        <td style="width:8%;">'.$Main_UOM.'</td>
                        <td style="width:8%;">'.$FROMQTY.'</td>
                        <td style="width:8%;">'.$GroupName.'</td>
                        <td style="width:8%;">'.$Categoryname.'</td>
                        <td style="width:8%;">'.$BusinessUnit.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                        <td style="width:8%;">Authorized</td>
                        <td hidden>
                                <input type="text" id="txtitem_'.$index.'" 
                                    data-desc1="'.$ITEMID.'" 
                                    data-desc2="'.$ICODE.'" 
                                    data-desc3="'.$NAME.'" 
                                    data-desc4="'.$MAIN_UOMID_REF.'" 
                                    data-desc5="'.$Main_UOM.'" 
                                    data-desc6="'.$FROMQTY.'" 
                                    data-desc7="'.$item_unique_row_id.'" 
                                    data-desc8="'.$SQA.'"
                                    data-desc9="'.$SEQID_REF.'"
                                    data-desc10="'.$SLID_REF.'"
                                    data-desc11="'.$SOID_REF.'"
                                    data-desc12=""
                                    data-desc13=""
                                    data-desc14=""
                                    data-desc15=""
                                />
                            </td>
                            <td hidden><input type="hidde" id="addinfoitem_'.$index.'"  data-desc101="'.$ALPS_PART_NO.'" data-desc102="'.$CUSTOMER_PART_NO.'" data-desc103="'.$OEM_PART_NO.'" ></td>
                        </tr>';
            } 

            echo $row;
                               
        }           
        else{
            echo '<tr><td colspan="12"> Record not found.</td></tr>';
        }

        exit();
    }

    public function get_materital_item(Request $request){
        
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $item_array     =   $request['item_array'];
        $AllStatus      =   $request['AllStatus'];
        $material_array =   array();

        foreach($item_array as $key=>$val){

            $exp        =   explode("_",$val);
            $SLID       =   $exp[0];
            $SOID       =   $exp[1];
            $ITEMID     =   $exp[2];
            $ITEMCODE   =   $exp[3];
            $PD_OR_QTY  =   $exp[4];
            $SQID       =   $exp[5];
            $SEID       =   $exp[6];

            $BOM_HDR    =   $this->get_bom_hdr($CYID_REF,$BRID_REF,$FYID_REF,$ITEMID);

            if(isset($BOM_HDR) && !empty($BOM_HDR)){

                $mitem_id   =   $SLID."_".$SOID."_".$SQID."_".$SEID."_".$ITEMID;
                $BOMID      =   $BOM_HDR->BOMID;
                $BOM_MAT    =   $this->get_bom_mat($BOMID);

                if(isset($BOM_MAT) && !empty($BOM_MAT)){
                    foreach($BOM_MAT as $key=>$row){
                        $material_array[]=array(
                            'BOM_MATID'=>$row->BOM_MATID,
                            'BOMID_REF'=>$row->BOMID_REF,
                            'ITEMID_REF'=>$row->ITEMID_REF,
                            'CONSUME_QTY'=>$row->CONSUME_QTY,
                            'ICODE'=>$row->ICODE,
                            'NAME'=>$row->NAME,
                            'MAIN_SLID'=>$SLID,
                            'MAIN_SOID'=>$SOID,
                            'MAIN_ITEMID'=>$ITEMID,
                            'MAIN_ITEMCODE'=>$ITEMCODE,
                            'MAIN_PD_OR_QTY'=>$PD_OR_QTY,
                            'MAIN_SQID'=>$SQID,
                            'MAIN_SEID'=>$SEID,
                            'MAIN_ITEM_ROWID'=>$mitem_id ,
                            'MATERIAL_TYPE'=>$row->MATERIAL_TYPE,
                        );
                    }

                    $material_array1    =   $material_array;
                    $material_array2    =   $this->getSubBomItem($SLID,$SOID,$SQID,$SEID,$CYID_REF,$BRID_REF,$FYID_REF,$material_array1);
                    $material_array3    =   $this->getSubBomItem($SLID,$SOID,$SQID,$SEID,$CYID_REF,$BRID_REF,$FYID_REF,$material_array2);
                    $material_array4    =   $this->getSubBomItem($SLID,$SOID,$SQID,$SEID,$CYID_REF,$BRID_REF,$FYID_REF,$material_array3);
                    $material_array5    =   $this->getSubBomItem($SLID,$SOID,$SQID,$SEID,$CYID_REF,$BRID_REF,$FYID_REF,$material_array4);
                    $material_array     =   array_merge($material_array1,$material_array2,$material_array3,$material_array4,$material_array5);

                }

            }
        }

        if(!empty($material_array)){
            $Row_Count4 =   count($material_array);
            echo'<table id="example4" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                        <tr>
                            <th hidden ><input class="form-control" type="hidden" name="Row_Count4" id ="Row_Count4" value="'.$Row_Count4.'"></th>
                            <th>Main Item</th>
                            <th>Item Code</th>
                            <th>Item Description</th>
                            <th hidden>MAIN_PD_OR_QTY</th>
                            <th>Standard BOM Qty</th>
                            <th>Input Item as per Production Order Qty</th>
                            <th>Changes in Production order Qty</th>
                        </tr>
                    </thead>
                    <tbody>';

                    foreach($material_array as $index=>$row_data){					
					
                        $material_wise_disabled =   $row_data['MATERIAL_TYPE'] =="SFG- Semi Finish Good"?"readonly":'';  			
                        $prod_order_qty         =   number_format(round((floatval($row_data['CONSUME_QTY'])*floatval($row_data['MAIN_PD_OR_QTY'])), 3),3,".","");						
						
                        echo '<tr  class="participantRow4">';
                        echo '<td hidden><input type="text" id="txtBOM_MATID_'.$index.'"  value="'.$row_data['BOM_MATID'].'"  class="form-control" readonly style="width:100px;" /></td>';
                        echo '<td><input type="text" id="txtMAIN_ITEMCODE_'.$index.'"  value="'.$row_data['MAIN_ITEMCODE'].'"  class="form-control" readonly style="width:100px;" /></td>';
                        echo '<td><input type="text" id="txtSUBITEM_popup_'.$index.'"  value="'.$row_data['ICODE'].'"          class="form-control" readonly style="width:100px;" /></td>';
                        echo '<td><input type="text" id="SUBITEM_NAME_'.$index.'"      value="'.$row_data['NAME'].'"           class="form-control" readonly style="width:200px;" /></td>';

                        echo '<td hidden><input type="text" name="MAIN_PD_OR_QTY_'.$index.'"      id="MAIN_PD_OR_QTY_'.$index.'"      value="'.$row_data['MAIN_PD_OR_QTY'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_BOMID_REF_'.$index.'"       id="REQ_BOMID_REF_'.$index.'"       value="'.$row_data['BOMID_REF'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SOID_REF_'.$index.'"        id="REQ_SOID_REF_'.$index.'"        value="'.$row_data['MAIN_SOID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SOITEMID_REF_'.$index.'"    id="REQ_SOITEMID_REF_'.$index.'"    value="'.$row_data['MAIN_ITEMID'].'" /></td>';
                        echo '<td hidden><input type="text" name="REQ_ITEMID_REF_'.$index.'"      id="REQ_ITEMID_REF_'.$index.'"      value="'.$row_data['ITEMID_REF'].'" /></td>';
                        echo '<td hidden><input type="text" name="REQ_MAIN_ITEMID_REF_'.$index.'" id="REQ_MAIN_ITEMID_REF_'.$index.'"  /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SQID_REF_'.$index.'"         id="REQ_SQID_REF_'.$index.'"         value="'.$row_data['MAIN_SQID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SEID_REF_'.$index.'"        id="REQ_SEID_REF_'.$index.'"        value="'.$row_data['MAIN_SEID'].'" /></td>';
                       
                        echo '<td><input    type="text" name="REQ_BOM_QTY_'.$index.'"           id="REQ_BOM_QTY_'.$index.'"             value="'.number_format($row_data['CONSUME_QTY'],3,".","").'"    class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="change_production_qty(this.id,this.value)" readonly  /></td>';
                        echo '<td><input    type="text" name="REQ_INPUT_PD_OR_QTY_'.$index.'"   id="REQ_INPUT_PD_OR_QTY_'.$index.'"     value="'.$prod_order_qty.'"             class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="change_production_qty(this.id,this.value)" readonly  /></td>';
                        echo '<td><input    type="text" name="REQ_CHANGES_PD_OR_QTY_'.$index.'" id="REQ_CHANGES_PD_OR_QTY_'.$index.'"   value="'.$prod_order_qty.'"             class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="change_production_qty(this.id,this.value)" '.$material_wise_disabled.'  /></td>';
                        echo '<td hidden><input id="main_item_rowid_'.$index.'" value="'.$row_data['MAIN_ITEM_ROWID'].'"  /></td>';
                        echo '</tr>';
                    }
					
			
                    
            echo '</tbody>';
            echo'</table>';
			
        }
        else{
            echo "Record not found.";
        }
        
        exit();
    }

    public function get_bom_hdr($CYID_REF,$BRID_REF,$FYID_REF,$ITEMID){
        
        return  DB::table('TBL_MST_BOM_HDR')
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->where('ITEMID_REF','=',$ITEMID)
                ->where('STATUS','=','A')
                ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                ->select('BOMID')
                ->first();
    }

    public function get_bom_mat($BOMID){

        return  DB::select("SELECT 
                T1.BOM_MATID,T1.BOMID_REF,T1.ITEMID_REF,T1.CONSUME_QTY,
                T2.ICODE,T2.NAME,T2.MATERIAL_TYPE
                FROM TBL_MST_BOM_MAT T1 
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                WHERE T1.BOMID_REF='$BOMID'");
    }

    function getSubBomItem($SLID,$SOID,$SQID,$SEID,$CYID_REF,$BRID_REF,$FYID_REF,$material_array){

        $data_array     =   array();
        if(!empty($material_array)){
            foreach($material_array as $key=>$val){
                $PD_OR_QTY  =   number_format(round((floatval($val['CONSUME_QTY'])*floatval($val['MAIN_PD_OR_QTY'])), 3),3,".","");
                $ITEMID     =   $val['ITEMID_REF'];
                $ITEMCODE   =   $val['ICODE'];
                $BOM_HDR    =   $this->get_bom_hdr($CYID_REF,$BRID_REF,$FYID_REF,$ITEMID);

                if(isset($BOM_HDR) && !empty($BOM_HDR)){

                    $mitem_id   =   $SLID."_".$SOID."_".$SQID."_".$SEID."_".$ITEMID;
                    $BOMID      =   $BOM_HDR->BOMID;
                    $BOM_MAT    =   $this->get_bom_mat($BOMID);
                    
                    if(isset($BOM_MAT) && !empty($BOM_MAT)){
                        foreach($BOM_MAT as $key=>$row){
                            $data_array[]=array(
                                'BOM_MATID'=>$row->BOM_MATID,
                                'BOMID_REF'=>$row->BOMID_REF,
                                'ITEMID_REF'=>$row->ITEMID_REF,
                                'CONSUME_QTY'=>$row->CONSUME_QTY,
                                'ICODE'=>$row->ICODE,
                                'NAME'=>$row->NAME,
                                'MAIN_SLID'=>$SLID,
                                'MAIN_SOID'=>$SOID,
                                'MAIN_ITEMID'=>$ITEMID,
                                'MAIN_ITEMCODE'=>$ITEMCODE,
                                'MAIN_PD_OR_QTY'=>$PD_OR_QTY,
                                'MAIN_SQID'=>$SQID,
                                'MAIN_SEID'=>$SEID,
                                'MAIN_ITEM_ROWID'=>$mitem_id ,
                                'MATERIAL_TYPE'=>$row->MATERIAL_TYPE,
                            );
                        }   
                    }
                }
            }
        }

        return $data_array;
    }
    

    /*
    public function get_bomitem_level2($aIds){

        $material_array =   array();

        if(isset($aIds) && !empty($aIds)){
            foreach($aIds as $val){

                $dataRow        =   explode("_",$val);
                
                $BOM_MATID      =   $dataRow[0];
                $ITEMID         =   $dataRow[1];
                $ITEMCODE       =   $dataRow[2];
                $PD_OR_QTY      =   $dataRow[3];
                $SLID           =   NULL;
                $SOID           =   NULL;
                $SQID           =   NULL;
                $SEID           =   NULL;
                $mitem_id       =   $SLID."_".$SOID."_".$SQID."_".$SEID."_".$ITEMID;

                $row    =   DB::select("SELECT 
                                T1.BOM_MATID,T1.BOMID_REF,T1.ITEMID_REF,T1.CONSUME_QTY,
                                T2.ICODE,T2.NAME,T2.MATERIAL_TYPE
                                FROM TBL_MST_BOM_MAT T1 
                                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                                WHERE T1.BOM_MATID='$BOM_MATID'");

                if(!empty($row)){
                    $material_array[]=array(
                        'BOM_MATID'=>$row[0]->BOM_MATID,
                        'BOMID_REF'=>$row[0]->BOMID_REF,
                        'ITEMID_REF'=>$row[0]->ITEMID_REF,
                        'CONSUME_QTY'=>$row[0]->CONSUME_QTY,
                        'ICODE'=>$row[0]->ICODE,
                        'NAME'=>$row[0]->NAME,
                        'MAIN_SLID'=>$SLID,
                        'MAIN_SOID'=>$SOID,
                        'MAIN_ITEMID'=>$ITEMID,
                        'MAIN_ITEMCODE'=>$ITEMCODE,
                        'MAIN_PD_OR_QTY'=>$PD_OR_QTY,
                        'MAIN_SQID'=>$SQID,
                        'MAIN_SEID'=>$SEID,
                        'MAIN_ITEM_ROWID'=>$mitem_id ,
                        'MATERIAL_TYPE'=>$row[0]->MATERIAL_TYPE,
                    ); 

                }          
            }
        } 

        return $material_array;
    }
    */
    
    /*
    public function get_materital_item_edit(Request $request){
        
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $item_array     =   $request['item_array'];
        $subitem_array  =   $request['subitem_array'];
        $AllStatus      =   $request['AllStatus'];
        $ActionStatus   =   $request['ActionStatus'];

        //dump($subitem_array);

        


        $material_array=array();
        foreach($item_array as $key=>$val){

            $exp        =   explode("_",$val);
            $SLID       =   $exp[0];
            $SOID       =   $exp[1];
            $ITEMID     =   $exp[2];
            $ITEMCODE   =   $exp[3];
            $PD_OR_QTY  =   $exp[4];
            $SQID       =   $exp[5];
            $SEID       =   $exp[6];
            $PROID      =   $exp[7];

            $mitem_id = $SLID."_".$SOID."_".$SQID."_".$SEID."_".$ITEMID;

            //echo "<br>mitem_id= ".$mitem_id;

            $PRO_REQ = DB::select("SELECT * FROM TBL_TRN_PDPRO_REQ                           
                            WHERE PROID_REF='$PROID'");

            $BOM_HDR =   DB::table('TBL_MST_BOM_HDR')
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF)
                        ->where('FYID_REF','=',$FYID_REF)
                        ->where('ITEMID_REF','=',$ITEMID)
                        ->where('STATUS','=','A')
                        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                        ->select('BOMID')
                        ->first();

            if(isset($BOM_HDR) && !empty($BOM_HDR)){

                $BOMID  =   $BOM_HDR->BOMID;

                $BOM_MAT    =   DB::select("SELECT 
                                T1.BOM_MATID,T1.BOMID_REF,T1.ITEMID_REF,T1.CONSUME_QTY,
                                T2.ICODE,T2.NAME,T2.MATERIAL_TYPE
                                FROM TBL_MST_BOM_MAT T1 
                                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                                WHERE T1.BOMID_REF='$BOMID'");
                //dump($BOM_MAT);                

                if(isset($BOM_MAT) && !empty($BOM_MAT)){
                    foreach($BOM_MAT as $row){

                        $material_array[]=array(
                            'BOM_MATID'=>$row->BOM_MATID,
                            'BOMID_REF'=>$row->BOMID_REF,
                            'ITEMID_REF'=>$row->ITEMID_REF,
                            'CONSUME_QTY'=>$row->CONSUME_QTY,
                            'ICODE'=>$row->ICODE,
                            'NAME'=>$row->NAME,
                            'MAIN_SLID'=>$SLID,
                            'MAIN_SOID'=>$SOID,
                            'MAIN_ITEMID'=>$ITEMID,
                            'MAIN_ITEMCODE'=>$ITEMCODE,
                            'MAIN_PD_OR_QTY'=>$PD_OR_QTY,
                            'MAIN_SQID'=>$SQID,
                            'MAIN_SEID'=>$SEID,
                            'MAIN_ITEM_ROWID'=>$mitem_id ,
                            'MATERIAL_TYPE'=>$row->MATERIAL_TYPE,
                        );
                        //dd( $material_array);
                    }
                }

            }
        }

       // dump($material_array);

        if(!empty($material_array)){
            $Row_Count4 =   count($material_array);
            echo'<table id="example4" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                        <tr>
                            <th hidden ><input class="form-control" type="hidden" name="Row_Count4" id ="Row_Count4" value="'.$Row_Count4.'"></th>
                            <th>Main Item</th>
                            <th>Item Code</th>
                            <th>Item Description</th>
                            <th>Standard BOM Qty</th>
                            <th>Input Item as per Production Order Qty</th>
                            <th>Changes in Production order Qty</th>
                        </tr>
                    </thead>
                    <tbody>';

                    foreach($material_array as $index=>$row_data){

                        $txtSUBITEM_popup          =   $row_data['ICODE'];
                        $SUBITEM_NAME              =   $row_data['NAME'];
                        $REQ_BOM_QTY               =   $row_data['CONSUME_QTY'];
                        $change_prod_order_qty     =   round(($row_data['CONSUME_QTY']*$row_data['MAIN_PD_OR_QTY']), 3);
                        $input_prod_order_qty      =   round(($row_data['CONSUME_QTY']*$row_data['MAIN_PD_OR_QTY']), 3);
                        $REQ_ITEMID_REF            =   $row_data['ITEMID_REF'];
                        $REQ_MAIN_ITEMID_REF       =    "";

                        $material_wise_disabled   =   $AllStatus == '1' && $row_data['MATERIAL_TYPE'] =="SFG- Semi Finish Good"?"readonly":'';     

                        if(!empty($subitem_array)){
                                foreach ($subitem_array as $subindex => $sub_row) {
                                    $main_item_id = "";
                                    if(is_null($sub_row["REQ_MAIN_ITEMID_REF"]) || trim($sub_row["REQ_MAIN_ITEMID_REF"])==""){
                                        $main_item_id = $sub_row["REQ_ITEMID_REF"];                                        
                                    }else{
                                        $main_item_id = $sub_row["REQ_MAIN_ITEMID_REF"];
                                    }
                                   $old_subitem_row_id = $sub_row["id"]."_".$sub_row["REQ_BOMID_REF"]."_".$main_item_id ;
                                   $new_subitem_row_id = $row_data['MAIN_ITEM_ROWID']."_".$row_data["BOMID_REF"]."_".$row_data["ITEMID_REF"] ;
                                                                      
                                   if($new_subitem_row_id==$old_subitem_row_id ){
                                        //echo "<br>".$old_subitem_row_id." == ".$new_subitem_row_id;
                                        
                                        $txtSUBITEM_popup       =   $sub_row["txtSUBITEM_popup"]; 
                                        $SUBITEM_NAME           =   $sub_row["SUBITEM_NAME"]; 
                                        $REQ_BOM_QTY            =   $sub_row["REQ_BOM_QTY"]; 
                                        $input_prod_order_qty   =   $sub_row["subitem_qty"];
                                        $change_prod_order_qty  =   $sub_row["subitem_qty2"];
                                        $REQ_ITEMID_REF         =   $sub_row["REQ_ITEMID_REF"];
                                        $REQ_MAIN_ITEMID_REF    =   $sub_row["REQ_MAIN_ITEMID_REF"];
                                   }
                                    

                                }//subitem_array
                        }

                       


                        echo '<tr  class="participantRow4">';
                        echo '<td><input '.$ActionStatus.' type="text" value="'.$row_data['MAIN_ITEMCODE'].'"  class="form-control" readonly style="width:100px;" /></td>';
                        echo '<td><input '.$ActionStatus.' type="text" id="txtSUBITEM_popup_'.$index.'"  value="'.$txtSUBITEM_popup.'"          class="form-control" readonly style="width:100px;" /></td>';
                        echo '<td><input '.$ActionStatus.' type="text" id="SUBITEM_NAME_'.$index.'"      value="'.$SUBITEM_NAME.'"           class="form-control" readonly style="width:200px;" /></td>';

                        // echo '<td  ><input type="text" name="subrowid_'.$index.'"      id="subrowid_'.$index.'"      value="'.$row_data['BOM_MATID']."_".$row_data['MAIN_ITEM_ROWID'].'" /></td>';
                        echo '<td  hidden><input style="width:60px" type="text" name="MAIN_PD_OR_QTY_'.$index.'"      id="MAIN_PD_OR_QTY_'.$index.'"     value="'.$row_data['MAIN_PD_OR_QTY'].'" /></td>';
                        echo '<td  hidden><input style="width:60px" type="text" name="REQ_BOMID_REF_'.$index.'"       id="REQ_BOMID_REF_'.$index.'"       value="'.$row_data['BOMID_REF'].'" /></td>';
                        echo '<td  hidden><input style="width:60px" type="text" name="REQ_SOID_REF_'.$index.'"        id="REQ_SOID_REF_'.$index.'"        value="'.$row_data['MAIN_SOID'].'" /></td>';
                        echo '<td  hidden><input style="width:60px"  type="text" name="REQ_SQID_REF_'.$index.'"         id="REQ_SQID_REF_'.$index.'"       value="'.$row_data['MAIN_SQID'].'" /></td>';
                        echo '<td  hidden><input style="width:60px"  type="text" name="REQ_SEID_REF_'.$index.'"        id="REQ_SEID_REF_'.$index.'"        value="'.$row_data['MAIN_SEID'].'" /></td>';
                        echo '<td  hidden><input style="width:60px"  type="text" name="REQ_SOITEMID_REF_'.$index.'"    id="REQ_SOITEMID_REF_'.$index.'"    value="'.$row_data['MAIN_ITEMID'].'" /></td>';
                        echo '<td  hidden><input style="width:60px"  type="text" name="REQ_ITEMID_REF_'.$index.'"      id="REQ_ITEMID_REF_'.$index.'"      value="'.$REQ_ITEMID_REF.'" /></td>';
                        echo '<td  hidden><input style="width:60px"  type="text" name="REQ_MAIN_ITEMID_REF_'.$index.'" id="REQ_MAIN_ITEMID_REF_'.$index.'" value="'.$REQ_MAIN_ITEMID_REF .'"  /></td>';
                        
                        echo '<td><input '.$ActionStatus.'    type="text" name="REQ_BOM_QTY_'.$index.'"           id="REQ_BOM_QTY_'.$index.'"             value="'.number_format($REQ_BOM_QTY,3,".","") .'"    class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="change_production_qty(this.id,this.value)" readonly  /></td>';
                        echo '<td><input '.$ActionStatus.'    type="text" name="REQ_INPUT_PD_OR_QTY_'.$index.'"   id="REQ_INPUT_PD_OR_QTY_'.$index.'"     value="'.number_format($input_prod_order_qty,3,".","") .'"             class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="change_production_qty(this.id,this.value)" readonly  /></td>';
                        echo '<td><input '.$ActionStatus.'    type="text" name="REQ_CHANGES_PD_OR_QTY_'.$index.'" id="REQ_CHANGES_PD_OR_QTY_'.$index.'"   value="'.number_format($change_prod_order_qty,3,".","") .'"             class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="change_production_qty(this.id,this.value)" '.$material_wise_disabled.'  /></td>';
                        echo '<td  hidden><input id="main_item_rowid_'.$index.'" value="'.$row_data['MAIN_ITEM_ROWID'].'"  /></td>';
                        echo '</tr>';
                    }
                    
            echo '</tbody>';
            echo'</table>';
        }
        else{
            echo "Record not found.";
        }
        
        exit();
    }
    */

    /*
    public function getSUBITEMCodeNo(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $BOMID_REF      =   $request['REQ_BOMID_REF'];
        $MAINITEMID_REF =   $request['REQ_ITEMID'];
        $MAIN_PD_OR_QTY =   $request['MAIN_PD_OR_QTY'];

        $ObjData        =   DB::select("SELECT T1.BOM_SUBID,T1.BOMID_REF,T1.SUBITEMID_REF,T1.CONSUME_QTY, T2.ICODE,T2.NAME
                            FROM TBL_MST_BOM_SUB T1
                            LEFT JOIN TBL_MST_ITEM T2 ON T1.SUBITEMID_REF=T2.ITEMID
                            WHERE BOMID_REF='$BOMID_REF' AND MAINITEMID_REF='$MAINITEMID_REF' ");
        
        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){

                $prod_order_qty     =   round(($dataRow->CONSUME_QTY*$MAIN_PD_OR_QTY), 3);
                
                $row = '';
                $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_BOM_SUBID[]" id="subcode_'.$index.'" 
                                class="clssSUBITEMid" value="'.$dataRow-> BOM_SUBID.'" ></td>';
                $row = $row.'<td class="ROW2">'.$dataRow->ICODE; 
                $row = $row.'<input type="hidden" id="txtsubcode_'.$dataRow->BOM_SUBID.'" 

                data-desc1="'.$dataRow->ICODE.'"
                data-desc2="'.$dataRow->NAME.'"
                data-desc3="'.$dataRow->SUBITEMID_REF.'"
                data-desc4="'.$MAINITEMID_REF.'"
                data-desc5="'.$dataRow->CONSUME_QTY.'"
                data-desc6="'.$prod_order_qty.'"
               
                
                value="'.$dataRow->BOM_SUBID.'"/>
                
                </td>
                
                <td class="ROW3">'.$dataRow->NAME.'</td></tr>';
                echo $row;
                
            }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();

        
    }
    */

    

    
}
