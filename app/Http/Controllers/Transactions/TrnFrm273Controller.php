<?php
namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use DB;
use Facade\Ignition\DumpRecorder\Dump;
use Session;
use Response;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use App\Helpers\Utils;


class TrnFrm273Controller extends Controller{

    protected $form_id  = 273;
    protected $vtid_ref = 363;
    protected $view     = "transactions.Production.RequisitionAgainstPRO.trnfrm";
   
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

        $objDataList	=	DB::select("select hdr.RPRID,hdr.RPR_NO,hdr.RPR_DT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.RPRID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                            ) AS CREATED_BY,
                            hdr.STATUS, pro.PRO_NO,pro.PRO_TITLE,
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
                            inner join TBL_TRN_PDRPR_HDR hdr
                            on a.VID = hdr.RPRID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_TRN_PDPRO_HDR pro ON hdr.PROID_REF = pro.PROID  
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.RPRID DESC ");

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
	
	public function ViewReport($request) 
    {
        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
       // dd($myValue);  
        $RPRID       =   $myValue['RPRID'];
        $Flag       =   $myValue['Flag'];

       /*  $objSalesOrder = DB::table('TBL_TRN_SLSO01_HDR')
        ->where('TBL_TRN_SLSO01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_SLSO01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        ->where('TBL_TRN_SLSO01_HDR.SOID','=',$PROID)
        ->select('TBL_TRN_SLSO01_HDR.*')
        ->first(); */
        
        
        $ssrs = new \SSRS\Report('http://103.139.58.23:8181//ReportServer/', array('username' => 'Administrator', 'password' => 'VRt+wDPuDYLwxxC'));
        $result = $ssrs->loadReport('/Accurate/RPR-Accurate');
        
        $reportParameters = array(
            'RPRID' => $RPRID,
        );
        $parameters = new \SSRS\Object\ExecutionParameters($reportParameters);
        
        $ssrs->setSessionId($result->executionInfo->ExecutionID)
        ->setExecutionParameters($parameters);
        if($Flag == 'H')
        {
            $output = $ssrs->render('HTML4.0'); // PDF | XML | CSV
            echo $output;
        }
        else if($Flag == 'P')
        {
            $output = $ssrs->render('PDF'); // PDF | XML | CSV | HTML4.0
            return $output->download('Report.pdf');
        }
        else if($Flag == 'E')
        {
            $output = $ssrs->render('EXCEL'); // PDF | XML | CSV | HTML4.0
            return $output->download('Report.xls');
        }
        else if($Flag == 'R')
        {
            $output = $ssrs->render('HTML4.0'); // PDF | XML | CSV | HTML4.0
            echo $output;

        }
         
     }

    public function add(){       
        $Status     = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
       
        $objlastdt          =   $this->getLastdt();
        $objPRO             =   $this->getPRONo();
        $objPStage          =   $this->getobjPStage();
        $objStore           =   $this->objStore();
        $objEmployee        =   $this->objEmployee(); 
        
        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_PDRPR_HDR',
            'HDR_ID'=>'RPRID',
            'HDR_DOC_NO'=>'RPR_NO',
            'HDR_DOC_DT'=>'RPR_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
        
       
   
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_RPR")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                {       
                                $query->select('UDFRPRID')->from('TBL_MST_UDFFOR_RPR')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);                      
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);                   
                   

        $objUdfData = DB::table('TBL_MST_UDFFOR_RPR')
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
            'objPRO',
            'objPStage',
            'objStore',
            'objEmployee',
            
            'objlastdt',            
            'objUdfData',
            'objCountUDF',
            'TabSetting',
            'doc_req','docarray'
            ]));       
    }

    public function save(Request $request) {       
       
        //DUMP($request->all());

        $VTID_REF     =   $this->vtid_ref;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $RPRO_NO     =   strtoupper(trim($request['RPRO_NO']));
        $RPR_DT     =   $request['RPR_DT'];
        $PROID_REF  =   $request['PROID_REF'];
        $PSTAGEID_REF     =   $request['PSTAGEID_REF'];
        $STID_REF     =   $request['STID_REF'];
        $REQUESTED_BY     =   $request['EMPID_REF'];

        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
       
		$req_data=array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $req_data[$i] = [
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'     => $request['MAIN_UOMID_REF_'.$i],
                    'BL_PD_OR_QTY'  => $request['QTY_'.$i],
                    'REQ_QTY'       => $request['REQ_QTY_'.$i],
                    'REMARKS'       => $request['REMARKS_'.$i],
                    'PROID_REF'     => $PROID_REF,                    
                    'SOID_REF'      => $request['SOID_REF_'.$i],                   
                    'SQID_REF'      => $request['SQID_REF_'.$i],
                    'SEID_REF'      => $request['SEID_REF_'.$i],    
                    'MAINITEMID_REF'      => $request['FGI_REF_'.$i],    
                    'MAINITEMUOMID_REF'      => $request['MAINITEM_UOMID_REF_'.$i],    
                ];

            }
        }
		
        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
		
		$reqdata3=array();
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['UDF_'.$i])){
                $reqdata3[$i] = [
                    'UDFRPRID_REF'      => $request['UDF_'.$i],
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
       
       
        $log_data = [ 
            $RPRO_NO,        $RPR_DT,    $PROID_REF, $PSTAGEID_REF,  $STID_REF,
            $REQUESTED_BY,  $CYID_REF,  $BRID_REF,  $FYID_REF,      $VTID_REF,
            $XMLMAT,        $XMLUDF,    $USERID,    Date('Y-m-d'),  Date('h:i:s.u'),
            $ACTIONNAME,    $IPADDRESS
        ]; 

       // dump($log_data);
       

        $sp_result = DB::select('EXEC SP_RPR_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?', $log_data);  
        //dd($sp_result);

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
			//$objPRO             =   $this->getPRONo();
            $objPStage          =   $this->getobjPStage();
            $objStore           =   $this->objStore();
            $objEmployee        =   $this->objEmployee(); 
			
            $objResponse =  DB::table('TBL_TRN_PDRPR_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
			->where('FYID_REF','=',Session::get('FYID_REF'))
            ->where('RPRID','=',$id)
            ->first();
         
            //dd($objResponse);
            $objPRO2 =   DB::table('TBL_TRN_PDPRO_HDR')
                ->where('PROID','=',$objResponse->PROID_REF)
                ->first();

            $objPStage2 =   DB::table('TBL_MST_PRODUCTIONSTAGES')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('PSTAGEID','=',$objResponse->PSTAGEID_REF)
                ->first();

            $objStore2 =   DB::table('TBL_MST_STORE')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('STID','=',$objResponse->STID_REF)
                ->first();

            $objEmployee2 =   DB::table('TBL_MST_EMPLOYEE')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('EMPID','=',$objResponse->REQUESTED_BY)
                ->first();
                
            if(strtoupper($objResponse->STATUS)=="A"){
               // exit("Sorry, Approved record can not edit.");
            }

            $objMAT = DB::select("SELECT T1.*,
                T2.ICODE,T2.NAME AS ITEM_NAME,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,
                T3.UOMCODE,T3.DESCRIPTIONS,
                T4.ICODE AS MAINITEM_CODE,T4.NAME AS MAINITEM_NAME,
                T5.UOMCODE AS MAINITEM_UOMCODE, T5.DESCRIPTIONS AS MAINITEM_UOMDESC
                FROM TBL_TRN_PDRPR_MAT T1
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_ITEM T4 ON T1.MAINITEMID_REF=T4.ITEMID
                LEFT JOIN TBL_MST_UOM T5 ON T1.MAINITEMUOMID_REF=T5.UOMID
                WHERE T1.RPRID_REF='$id' ORDER BY T1.RPRID_REF ASC"); 



            $ObjItem =  DB::table('TBL_TRN_PDPRO_REQ')                     
            ->where('PROID_REF','=',$objResponse->PROID_REF)
            ->get();

            $objtempMAT = $objMAT;
            
            //-------------------------
            foreach ($objtempMAT as $matindex => $matRow) {             
                $BAL_PRO_REQ_QTY = 0;
                $objMAT[$matindex]->BAL_CHANGES_PD_OR_QTY= $BAL_PRO_REQ_QTY;

                $ObjSavedQty =   DB::table('TBL_TRN_PDRPR_MAT')
                    ->where('TBL_TRN_PDRPR_MAT.PROID_REF','=',$matRow->PROID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.SOID_REF','=',$matRow->SOID_REF)             
                    ->where('TBL_TRN_PDRPR_MAT.SQID_REF','=',$matRow->SQID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.SEID_REF','=',$matRow->SEID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.MAINITEMID_REF','=',$matRow->MAINITEMID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.ITEMID_REF','=',$matRow->ITEMID_REF)
                    ->where('TBL_TRN_PDRPR_HDR.STATUS','<>','C')
                    ->leftJoin('TBL_TRN_PDRPR_HDR',   'TBL_TRN_PDRPR_HDR.RPRID','=',   'TBL_TRN_PDRPR_MAT.RPRID_REF')
                    ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDRPR_MAT.REQ_QTY),0) AS REQ_QTY'))                       
                    ->get();
                $Total_ReqQty = $ObjSavedQty[0]->REQ_QTY;


                //get from production order req qty
                $ObjProReqItem =   DB::table('TBL_TRN_PDPRO_REQ')
                    ->where('TBL_TRN_PDPRO_REQ.PROID_REF','=',$matRow->PROID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SOID_REF','=',$matRow->SOID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SQID_REF','=',$matRow->SQID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SEID_REF','=',$matRow->SEID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SOITEMID_REF','=',$matRow->MAINITEMID_REF)      
                    ->where('TBL_TRN_PDPRO_REQ.ITEMID_REF','=',$matRow->ITEMID_REF)      
                    ->select('TBL_TRN_PDPRO_REQ.*',)                    
                    ->first();

                if(!empty($ObjProReqItem)){
                    $ObjProReqItem=$ObjProReqItem;
                }
                else{
                    $ObjProReqItem =   DB::table('TBL_TRN_PDPRO_REQ')
                    ->where('TBL_TRN_PDPRO_REQ.PROID_REF','=',$matRow->PROID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SOID_REF','=',$matRow->SOID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SQID_REF','=',$matRow->SQID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SEID_REF','=',$matRow->SEID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.ITEMID_REF','=',$matRow->MAINITEMID_REF)      
                    ->where('TBL_TRN_PDPRO_REQ.ITEMID_REF','=',$matRow->ITEMID_REF)      
                    ->select('TBL_TRN_PDPRO_REQ.*',)                    
                    ->first();

                }

                $PRO_REQ_QTY =  isset($ObjProReqItem->CHANGES_PD_OR_QTY)? $ObjProReqItem->CHANGES_PD_OR_QTY : 0; 
                
                $BAL_PRO_REQ_QTY = number_format( floatVal($PRO_REQ_QTY) - floatval($Total_ReqQty), 3,".","" ) ;

               

                //ADD CONSUMED QTY               
                $ObjConsumedQty =   DB::table('TBL_TRN_PDRPR_MAT')                                    
                        ->where('TBL_TRN_PDRPR_MAT.RPRID_REF','=',$matRow->RPRID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.PROID_REF','=',$matRow->PROID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.SOID_REF','=',$matRow->SOID_REF)             
                        ->where('TBL_TRN_PDRPR_MAT.SQID_REF','=',$matRow->SQID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.SEID_REF','=',$matRow->SEID_REF)
                            ->where('TBL_TRN_PDRPR_MAT.MAINITEMID_REF','=',$matRow->MAINITEMID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.ITEMID_REF','=',$matRow->ITEMID_REF)
                        ->where('TBL_TRN_PDRPR_HDR.STATUS','<>','C') 
                        ->leftJoin('TBL_TRN_PDRPR_HDR',   'TBL_TRN_PDRPR_HDR.RPRID','=',   'TBL_TRN_PDRPR_MAT.RPRID_REF')
                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDRPR_MAT.REQ_QTY),0) AS CONSUMED_REQ_QTY'))
                        ->get();  
                        
                        
                
                $BAL_PRO_REQ_QTY = number_format( floatVal($BAL_PRO_REQ_QTY) + floatval($ObjConsumedQty[0]->CONSUMED_REQ_QTY), 3,".","" ) ; 
                $objMAT[$matindex]->BAL_CHANGES_PD_OR_QTY = $BAL_PRO_REQ_QTY; 

               
                
            }

            $STID_REF=isset($objResponse->STID_REF) ? $objResponse->STID_REF :"";
            if(isset($objtempMAT) && !empty($objtempMAT)){
                foreach($objtempMAT as $key=>$val){  
                   
                    $StockInHand   =   $this->GetStockInHand($STID_REF,$val->ITEMID_REF);
                    
                    $objBatch =  DB::SELECT("SELECT 
                            T1.BATCHID,T1.BATCH_CODE,T1.CURRENT_QTY AS TOTAL_STOCK,
                            T2.STID,T2.STCODE,T2.NAME AS STNAME
                            FROM TBL_MST_BATCH T1 
                            LEFT JOIN TBL_MST_STORE T2 ON T2.STID=T1.STID_REF
                            WHERE T1.STATUS='A' AND T1.CYID_REF ='$CYID_REF' AND T1.BRID_REF ='$BRID_REF'  AND T1.ITEMID_REF =$val->ITEMID_REF AND T1.UOMID_REF =$val->UOMID_REF
                            ");
                $totalstock = 0;
                foreach($objBatch as $bindex=>$brow){
                    $bstock  = !is_null($brow->TOTAL_STOCK) && trim($brow->TOTAL_STOCK)!="" ? $brow->TOTAL_STOCK : 0;
                    $totalstock = floatval($totalstock) + floatval($bstock);

                }
                $objMAT[$key]->TOTAL_STOCK = number_format($totalstock,3,".","") ;		
				
                    if(isset($StockInHand) && !empty($StockInHand)){
                        $objtempMAT[$key]->StockInHand    =   $StockInHand;
                    }

                }
            }

          

          	
			$objUDF = DB::table('TBL_TRN_PDRPR_UDF')                    
            ->where('RPRID_REF','=',$id)
            ->orderBy('RPRID_REF','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF);
					
			$ObjUnionUDF = DB::table("TBL_MST_UDFFOR_RPR")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                {       
                                $query->select('UDFRPRID')->from('TBL_MST_UDFFOR_RPR')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);                      
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);                   
                   

            $objUdfData = DB::table('TBL_MST_UDFFOR_RPR')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray();  

        
            
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_RPR")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDFRPRID')->from('TBL_MST_UDFFOR_RPR')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                         
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);                           
            

            $objUdfData2 = DB::table('TBL_MST_UDFFOR_RPR')
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
				'objResponse',
				'objPRO2',
				'objPStage2',
                'objStore2',
                'objEmployee2',
				'objMAT',                
				'objPStage',
                'objStore',
                'objEmployee',
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
      
       $VTID_REF     =   $this->vtid_ref;
       $USERID = Auth::user()->USERID;   
       $ACTIONNAME = 'EDIT';
       $IPADDRESS = $request->getClientIp();
       $CYID_REF = Auth::user()->CYID_REF;
       $BRID_REF = Session::get('BRID_REF');
       $FYID_REF = Session::get('FYID_REF');

       $RPRO_NO     =   strtoupper(trim($request['RPRO_NO']));
       $RPR_DT     =   $request['RPR_DT'];
       $PROID_REF  =   $request['PROID_REF'];
       $PSTAGEID_REF     =   $request['PSTAGEID_REF'];
       $STID_REF     =   $request['STID_REF'];
       $REQUESTED_BY     =   $request['EMPID_REF'];

       $r_count1 = $request['Row_Count1'];
       $r_count2 = $request['Row_Count2'];
        
       $req_data=array();
       for ($i=0; $i<=$r_count1; $i++){
           if(isset($request['ITEMID_REF_'.$i])){

               $req_data[$i] = [
                   'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                   'UOMID_REF'     => $request['MAIN_UOMID_REF_'.$i],
                   'BL_PD_OR_QTY'  => $request['QTY_'.$i],
                   'REQ_QTY'       => $request['REQ_QTY_'.$i],
                   'REMARKS'       => $request['REMARKS_'.$i],
                   'PROID_REF'     => $PROID_REF,                    
                   'SOID_REF'      => $request['SOID_REF_'.$i],                   
                   'SQID_REF'      => $request['SQID_REF_'.$i],
                   'SEID_REF'      => $request['SEID_REF_'.$i],    
                   'MAINITEMID_REF'      => $request['FGI_REF_'.$i],    
                   'MAINITEMUOMID_REF'      => $request['MAINITEM_UOMID_REF_'.$i],  
               ];

           }
       }
   
       $wrapped_links["MAT"] = $req_data; 
       $XMLMAT = ArrayToXml::convert($wrapped_links);
   
        $reqdata3=array();
       for ($i=0; $i<=$r_count2; $i++){
           if(isset($request['UDF_'.$i])){
               $reqdata3[$i] = [
                   'UDFRPRID_REF'      => $request['UDF_'.$i],
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
      
      
       $log_data = [ 
           $RPRO_NO,        $RPR_DT,    $PROID_REF, $PSTAGEID_REF,  $STID_REF,
           $REQUESTED_BY,  $CYID_REF,  $BRID_REF,  $FYID_REF,      $VTID_REF,
           $XMLMAT,        $XMLUDF,    $USERID,    Date('Y-m-d'),  Date('h:i:s.u'),
           $ACTIONNAME,    $IPADDRESS
       ]; 
       
       
       $sp_result = DB::select('EXEC SP_RPR_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?', $log_data);  

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
			//$objPRO             =   $this->getPRONo();
            $objPStage          =   $this->getobjPStage();
            $objStore           =   $this->objStore();
            $objEmployee        =   $this->objEmployee(); 
			
            $objResponse =  DB::table('TBL_TRN_PDRPR_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
			->where('FYID_REF','=',Session::get('FYID_REF'))
            ->where('RPRID','=',$id)
            ->first();
            
            $objPRO2 =   DB::table('TBL_TRN_PDPRO_HDR')
                ->where('PROID','=',$objResponse->PROID_REF)
                ->first();

            $objPStage2 =   DB::table('TBL_MST_PRODUCTIONSTAGES')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('PSTAGEID','=',$objResponse->PSTAGEID_REF)
                ->first();

            $objStore2 =   DB::table('TBL_MST_STORE')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('STID','=',$objResponse->STID_REF)
                ->first();

            $objEmployee2 =   DB::table('TBL_MST_EMPLOYEE')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('EMPID','=',$objResponse->REQUESTED_BY)
                ->first();
                
            if(strtoupper($objResponse->STATUS)=="A"){
               // exit("Sorry, Approved record can not edit.");
            }

            $objMAT = DB::select("SELECT T1.*,
                T2.ICODE,T2.NAME AS ITEM_NAME,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,
                T3.UOMCODE,T3.DESCRIPTIONS,
                T4.ICODE AS MAINITEM_CODE,T4.NAME AS MAINITEM_NAME,
                T5.UOMCODE AS MAINITEM_UOMCODE, T5.DESCRIPTIONS AS MAINITEM_UOMDESC
                FROM TBL_TRN_PDRPR_MAT T1
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_ITEM T4 ON T1.MAINITEMID_REF=T4.ITEMID
                LEFT JOIN TBL_MST_UOM T5 ON T1.MAINITEMUOMID_REF=T5.UOMID
                WHERE T1.RPRID_REF='$id' ORDER BY T1.RPRID_REF ASC"); 

 
            $ObjItem =  DB::table('TBL_TRN_PDPRO_REQ')                     
            ->where('PROID_REF','=',$objResponse->PROID_REF)
            ->get();

            $objtempMAT = $objMAT;
            
            //-------------------------
            foreach ($objtempMAT as $matindex => $matRow) {
               
                $BAL_PRO_REQ_QTY = 0;
                $objMAT[$matindex]->BAL_CHANGES_PD_OR_QTY= $BAL_PRO_REQ_QTY;

                $ObjSavedQty =   DB::table('TBL_TRN_PDRPR_MAT')
                    ->where('TBL_TRN_PDRPR_MAT.PROID_REF','=',$matRow->PROID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.SOID_REF','=',$matRow->SOID_REF)             
                    ->where('TBL_TRN_PDRPR_MAT.SQID_REF','=',$matRow->SQID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.SEID_REF','=',$matRow->SEID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.MAINITEMID_REF','=',$matRow->MAINITEMID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.ITEMID_REF','=',$matRow->ITEMID_REF)
                    ->where('TBL_TRN_PDRPR_HDR.STATUS','<>','C')
                    ->leftJoin('TBL_TRN_PDRPR_HDR',   'TBL_TRN_PDRPR_HDR.RPRID','=',   'TBL_TRN_PDRPR_MAT.RPRID_REF')
                    ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDRPR_MAT.REQ_QTY),0) AS REQ_QTY'))                       
                    ->get();
                $Total_ReqQty = $ObjSavedQty[0]->REQ_QTY;


                //get from production order req qty
                $ObjProReqItem =   DB::table('TBL_TRN_PDPRO_REQ')
                    ->where('TBL_TRN_PDPRO_REQ.PROID_REF','=',$matRow->PROID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SOID_REF','=',$matRow->SOID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SQID_REF','=',$matRow->SQID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SEID_REF','=',$matRow->SEID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SOITEMID_REF','=',$matRow->MAINITEMID_REF)      
                    ->where('TBL_TRN_PDPRO_REQ.ITEMID_REF','=',$matRow->ITEMID_REF)      
                    ->select('TBL_TRN_PDPRO_REQ.*',)                    
                    ->first();

                if(!empty($ObjProReqItem)){
                    $ObjProReqItem=$ObjProReqItem;
                }
                else{
                    $ObjProReqItem =   DB::table('TBL_TRN_PDPRO_REQ')
                    ->where('TBL_TRN_PDPRO_REQ.PROID_REF','=',$matRow->PROID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SOID_REF','=',$matRow->SOID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SQID_REF','=',$matRow->SQID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SEID_REF','=',$matRow->SEID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.ITEMID_REF','=',$matRow->MAINITEMID_REF)      
                    ->where('TBL_TRN_PDPRO_REQ.ITEMID_REF','=',$matRow->ITEMID_REF)      
                    ->select('TBL_TRN_PDPRO_REQ.*',)                    
                    ->first();

                }

                $PRO_REQ_QTY =  isset($ObjProReqItem->CHANGES_PD_OR_QTY)? $ObjProReqItem->CHANGES_PD_OR_QTY : 0; 
                
                $BAL_PRO_REQ_QTY = number_format( floatVal($PRO_REQ_QTY) - floatval($Total_ReqQty), 3,".","" ) ;

               

                //ADD CONSUMED QTY               
                $ObjConsumedQty =   DB::table('TBL_TRN_PDRPR_MAT')                                    
                        ->where('TBL_TRN_PDRPR_MAT.RPRID_REF','=',$matRow->RPRID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.PROID_REF','=',$matRow->PROID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.SOID_REF','=',$matRow->SOID_REF)             
                        ->where('TBL_TRN_PDRPR_MAT.SQID_REF','=',$matRow->SQID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.SEID_REF','=',$matRow->SEID_REF)
                            ->where('TBL_TRN_PDRPR_MAT.MAINITEMID_REF','=',$matRow->MAINITEMID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.ITEMID_REF','=',$matRow->ITEMID_REF)
                        ->where('TBL_TRN_PDRPR_HDR.STATUS','<>','C') 
                        ->leftJoin('TBL_TRN_PDRPR_HDR',   'TBL_TRN_PDRPR_HDR.RPRID','=',   'TBL_TRN_PDRPR_MAT.RPRID_REF')
                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDRPR_MAT.REQ_QTY),0) AS CONSUMED_REQ_QTY'))
                        ->get();  
                        
                        
                
                $BAL_PRO_REQ_QTY = number_format( floatVal($BAL_PRO_REQ_QTY) + floatval($ObjConsumedQty[0]->CONSUMED_REQ_QTY), 3,".","" ) ; 
                $objMAT[$matindex]->BAL_CHANGES_PD_OR_QTY = $BAL_PRO_REQ_QTY; 

               
                
            }

          	
			$objUDF = DB::table('TBL_TRN_PDRPR_UDF')                    
            ->where('RPRID_REF','=',$id)
            ->orderBy('RPRID_REF','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF);
					
			$ObjUnionUDF = DB::table("TBL_MST_UDFFOR_RPR")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                {       
                                $query->select('UDFRPRID')->from('TBL_MST_UDFFOR_RPR')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);                      
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);                   
                   

            $objUdfData = DB::table('TBL_MST_UDFFOR_RPR')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$CYID_REF)
                ->union($ObjUnionUDF)
                ->get()->toArray();  

        
            
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_RPR")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDFRPRID')->from('TBL_MST_UDFFOR_RPR')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                         
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);                           
            

            $objUdfData2 = DB::table('TBL_MST_UDFFOR_RPR')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF2)
            ->get()->toArray(); 
		
            $FormId         =   $this->form_id;
            $AlpsStatus =   $this->AlpsStatus();
            $ActionStatus   =   "disabled";


            $STID_REF=isset($objResponse->STID_REF) ? $objResponse->STID_REF :"";
            if(isset($objtempMAT) && !empty($objtempMAT)){
                foreach($objtempMAT as $key=>$val){  
                   
                    $StockInHand   =   $this->GetStockInHand($STID_REF,$val->ITEMID_REF);
					
					 $objBatch =  DB::SELECT("SELECT 
                            T1.BATCHID,T1.BATCH_CODE,T1.CURRENT_QTY AS TOTAL_STOCK,
                            T2.STID,T2.STCODE,T2.NAME AS STNAME
                            FROM TBL_MST_BATCH T1 
                            LEFT JOIN TBL_MST_STORE T2 ON T2.STID=T1.STID_REF
                            WHERE T1.STATUS='A' AND T1.CYID_REF ='$CYID_REF' AND T1.BRID_REF ='$BRID_REF'  AND T1.ITEMID_REF =$val->ITEMID_REF AND T1.UOMID_REF =$val->UOMID_REF
                            ");
                $totalstock = 0;
                foreach($objBatch as $bindex=>$brow){
                    $bstock  = !is_null($brow->TOTAL_STOCK) && trim($brow->TOTAL_STOCK)!="" ? $brow->TOTAL_STOCK : 0;
                    $totalstock = floatval($totalstock) + floatval($bstock);

                }
                $objMAT[$key]->TOTAL_STOCK = number_format($totalstock,3,".","") ;		
				
                    if(isset($StockInHand) && !empty($StockInHand)){
                        $objtempMAT[$key]->StockInHand    =   $StockInHand;
                    }

                }
            }


            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
            return view($this->view.$FormId.'view',compact([
                'AlpsStatus',
				'FormId',
				'objRights',
				'objlastdt',
				'objResponse',
				'objPRO2',
				'objPStage2',
                'objStore2',
                'objEmployee2',
				'objMAT',                
				'objPStage',
                'objStore',
                'objEmployee',
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
   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
 
        $RPRO_NO     =   strtoupper(trim($request['RPRO_NO']));
        $RPR_DT     =   $request['RPR_DT'];
        $PROID_REF  =   $request['PROID_REF'];
        $PSTAGEID_REF     =   $request['PSTAGEID_REF'];
        $STID_REF     =   $request['STID_REF'];
        $REQUESTED_BY     =   $request['EMPID_REF'];

        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
         
        $req_data=array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){
 
                $req_data[$i] = [
                    'ITEMID_REF'    => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'     => $request['MAIN_UOMID_REF_'.$i],
                    'BL_PD_OR_QTY'  => $request['QTY_'.$i],
                    'REQ_QTY'       => $request['REQ_QTY_'.$i],
                    'REMARKS'       => $request['REMARKS_'.$i],
                    'PROID_REF'     => $PROID_REF,                    
                    'SOID_REF'      => $request['SOID_REF_'.$i],                   
                    'SQID_REF'      => $request['SQID_REF_'.$i],
                    'SEID_REF'      => $request['SEID_REF_'.$i],    
                    'MAINITEMID_REF'      => $request['FGI_REF_'.$i],    
                    'MAINITEMUOMID_REF'      => $request['MAINITEM_UOMID_REF_'.$i],  
                ];
 
            }
        }
    
        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
    
         $reqdata3=array();
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['UDF_'.$i])){
                $reqdata3[$i] = [
                    'UDFRPRID_REF'      => $request['UDF_'.$i],
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

        $log_data = [ 
            $RPRO_NO,        $RPR_DT,    $PROID_REF, $PSTAGEID_REF,  $STID_REF,
            $REQUESTED_BY,  $CYID_REF,  $BRID_REF,  $FYID_REF,      $VTID_REF,
            $XMLMAT,        $XMLUDF,    $USERID_REF,    Date('Y-m-d'),  Date('h:i:s.u'),
            $ACTIONNAME,    $IPADDRESS
        ]; 


        $sp_result = DB::select('EXEC SP_RPR_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?', $log_data);  
        

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
        $TABLE      =   "TBL_TRN_PDRPR_HDR";
        $FIELD      =   "RPRID";
        $ACTIONNAME     = $Approvallevel;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
            
        $log_data = [ 
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_RPR ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
        
        if($sp_result[0]->RESULT=="All records approved"){

        return Response::json(['approve' =>true,'msg' => 'Record successfully Approved.']);

        }elseif($sp_result[0]->RESULT=="NO RECORD FOR APPROVAL"){
        
        return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','save'=>'invalid']);
        
        }else{
        return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','save'=>'invalid']);
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
        $TABLE      =   "TBL_TRN_PDRPR_HDR";
        $FIELD      =   "RPRID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
		
        $req_data[0]=[
         'NT'  => 'TBL_TRN_PDRPR_MAT',
        ];
        $req_data[1]=[
        'NT'  => 'TBL_TRN_PDRPR_UDF',
        ];
		

        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_RPR  ?,?,?,?, ?,?,?,?, ?,?,?,?', $cancel_data);

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

            $objResponse = DB::table('TBL_TRN_PDRPR_HDR')->where('RPRID','=',$id)->first();

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
        
		$image_path         =   "docs/company".$CYID_REF."/RequisitionAgainstPRO";     
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
        
        $RPRO_NO  =   strtoupper(trim($request['RPRO_NO']));
        $objLabel = DB::table('TBL_TRN_PDRPR_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('RPR_NO','=',$RPRO_NO)
        ->select('RPRID')->first();

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

        return  DB::select('SELECT MAX(RPR_DT) RPR_DT FROM TBL_TRN_PDRPR_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
    }

    public function getPRONo(){
        
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
               
        //  return  DB::select('SELECT PROID,PRO_NO,PRO_DT,PRO_TITLE FROM TBL_TRN_PDPRO_HDR  
        //   WHERE  CYID_REF = ? AND BRID_REF = ? AND VTID_REF = ? AND STATUS = ?', 
        //   [$CYID_REF, $BRID_REF,'329', 'A' ]);

        $RPRID      =   isset($request['RPRID'])?$request['RPRID']:0;  //edit case

        $ObjData        =   DB::select('SELECT PROID,PRO_NO,PRO_DT,PRO_TITLE FROM TBL_TRN_PDPRO_HDR  
                            WHERE  CYID_REF = ? AND BRID_REF = ? AND VTID_REF = ? AND STATUS = ?', 
                            [$CYID_REF, $BRID_REF,'329', 'A' ]);
            
            $objNewRPRO= [];
            if(!empty($ObjData)){
                
                foreach ($ObjData as $index=>$dataRow){


                    $total_count =  DB::table('TBL_TRN_PDRPR_HDR')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('BRID_REF','=',Session::get('BRID_REF'))
                    ->where('FYID_REF','=',Session::get('FYID_REF'))
                    ->where('STATUS','=','A')
                    ->where('PROID_REF','=',$dataRow->PROID)
                    ->COUNT();

                    /*
                    $ObjPROItems =  DB::select("select * from TBL_TRN_PDPRO_REQ where PROID_REF=$dataRow->PROID");
                  

                    $addRecord = [];
                    foreach ($ObjPROItems as $index2=>$dataRow2){
                        
                        
                        $ObjSavedQty =   DB::table('TBL_TRN_PDRPR_MAT')
                        ->where('TBL_TRN_PDRPR_MAT.PROID_REF','=',$dataRow2->PROID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.SOID_REF','=',$dataRow2->SOID_REF)             
                        ->where('TBL_TRN_PDRPR_MAT.SQID_REF','=',$dataRow2->SQID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.SEID_REF','=',$dataRow2->SEID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.ITEMID_REF','=',$dataRow2->ITEMID_REF)
                        ->where('TBL_TRN_PDRPR_HDR.STATUS','<>','C')
                        ->leftJoin('TBL_TRN_PDRPR_HDR',   'TBL_TRN_PDRPR_HDR.RPRID','=',   'TBL_TRN_PDRPR_MAT.RPRID_REF')
                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDRPR_MAT.REQ_QTY),0) AS REQ_QTY'))                       
                        ->get();
                      
                            
                            $ObjConsumedQty =   DB::table('TBL_TRN_PDRPR_MAT')                                    
                                    ->where('TBL_TRN_PDRPR_MAT.RPRID_REF','=',$RPRID)
                                    ->where('TBL_TRN_PDRPR_MAT.PROID_REF','=',$dataRow2->PROID_REF)
                                    ->where('TBL_TRN_PDRPR_MAT.SOID_REF','=',$dataRow2->SOID_REF)             
                                    ->where('TBL_TRN_PDRPR_MAT.SQID_REF','=',$dataRow2->SQID_REF)
                                    ->where('TBL_TRN_PDRPR_MAT.SEID_REF','=',$dataRow2->SEID_REF)
                                    ->where('TBL_TRN_PDRPR_MAT.ITEMID_REF','=',$dataRow2->ITEMID_REF)
                                    ->where('TBL_TRN_PDRPR_HDR.STATUS','<>','C') 
                                    ->leftJoin('TBL_TRN_PDRPR_HDR',   'TBL_TRN_PDRPR_HDR.RPRID','=',   'TBL_TRN_PDRPR_MAT.RPRID_REF')
                                    ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDRPR_MAT.REQ_QTY),0) AS CONSUMED_REQ_QTY'))
                                    ->get();
                                                    
                           
                            $TOTAL_QTY = number_format( floatval($ObjSavedQty[0]->REQ_QTY) - floatval($ObjConsumedQty[0]->CONSUMED_REQ_QTY), 3,".","" ) ;
   
                            if(floatval($TOTAL_QTY) < floatval($dataRow2->CHANGES_PD_OR_QTY) ){
                                $addRecord[]=true;
                            }else
                            {
                                $addRecord[]=false;
                            }    
                    }
                    
                        
                        if(in_array('true',$addRecord)){
                            $objNewRPRO[$index]=$dataRow;
                        }  
                        */  

                        
                        if($total_count == 0){
                            $objNewRPRO[$index]=$dataRow;
                        }

                }

                return $objNewRPRO;

            }else{

               return $objNewRPRO;
            
            }

            

    } //function

    public function getobjPStage(){

        $CYID_REF = Auth::user()->CYID_REF;
                      
        return  DB::select('SELECT PSTAGEID,PSTAGE_CODE,DESCRIPTIONS FROM TBL_MST_PRODUCTIONSTAGES  
        WHERE  CYID_REF = ?  AND STATUS = ?', 
        [$CYID_REF, 'A' ]);
    }

    public function objStore(){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
                      
        return  DB::select('SELECT STID,STCODE,NAME FROM TBL_MST_STORE  
        WHERE  CYID_REF = ? AND BRID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF, 'A' ]);
    }

    public function objEmployee(){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
                      
        return  DB::select('SELECT EMPID,EMPCODE,FNAME,LNAME FROM TBL_MST_EMPLOYEE  
        WHERE  CYID_REF = ? AND BRID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF, 'A' ]);
    }

    
    public function getFGIDetails(Request $request){

        //dd($request->all()); 

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');        
        $PROID_REF  =   $request['PROID_REF'];
        $STID_REF   =   $request['STID_REF'];
        $RPRID      =   isset($request['RPRID'])?$request['RPRID']:0;  
        $AlpsStatus =   $this->AlpsStatus();

        $Direct     =   DB::table('TBL_TRN_PDPRO_HDR')
                        ->where('STATUS','=','A')
                        ->where('PROID','=',$PROID_REF)
                        ->select('DIRECTPO')
                        ->first()->DIRECTPO;
        /*
        $ObjItem    =   DB::select("SELECT 
                        T1.*,
                        T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI,T2.MAIN_UOMID_REF,T2.MATERIAL_TYPE
                        FROM TBL_TRN_PDPRO_MAT T1
                        LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                        WHERE T1.PROID_REF='$PROID_REF' AND MATERIAL_TYPE !='RM-Raw Material'");
                        */

        $ObjItem    =   DB::select("SELECT
                        T1.SOID_REF,T1.SQID_REF,T1.SEID_REF, 
                        T2.MATERIAL_TYPE,T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI,T2.MAIN_UOMID_REF
                        FROM TBL_TRN_PDPRO_MAT T1
                        LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                        WHERE T1.PROID_REF='$PROID_REF' AND T2.MATERIAL_TYPE !='RM-Raw Material'
                        UNION
                        SELECT 
                        T1.SOID_REF,T1.SQID_REF,T1.SEID_REF,
                        T2.MATERIAL_TYPE,T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI,T2.MAIN_UOMID_REF
                        FROM TBL_TRN_PDPRO_REQ T1
                        LEFT JOIN TBL_MST_ITEM T2 ON T1.SOITEMID_REF=T2.ITEMID
                        WHERE T1.PROID_REF='$PROID_REF' AND T2.MATERIAL_TYPE !='RM-Raw Material'");

        if(!empty($ObjItem)){
            foreach ($ObjItem as $index=>$dataRow){

                $ObjProReqItem  =   DB::table('TBL_TRN_PDPRO_REQ')
                                    ->where('TBL_TRN_PDPRO_REQ.PROID_REF','=',$PROID_REF)
                                    ->where('TBL_TRN_PDPRO_REQ.SOID_REF','=',trim($dataRow->SOID_REF)==""?NULL:$dataRow->SOID_REF)
                                    ->where('TBL_TRN_PDPRO_REQ.SQID_REF','=',trim($dataRow->SQID_REF)==""?NULL:$dataRow->SQID_REF)
                                    ->where('TBL_TRN_PDPRO_REQ.SEID_REF','=',trim($dataRow->SEID_REF)==""?NULL:$dataRow->SEID_REF)
                                    ->where('TBL_TRN_PDPRO_REQ.SOITEMID_REF','=',$dataRow->ITEMID)      
                                    ->leftJoin('TBL_MST_ITEM',   'TBL_MST_ITEM.ITEMID','=',   'TBL_TRN_PDPRO_REQ.ITEMID_REF')   
                                    ->select('TBL_TRN_PDPRO_REQ.*','TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ITEMGID_REF','TBL_MST_ITEM.ICID_REF','TBL_MST_ITEM.ITEM_SPECI','TBL_MST_ITEM.MAIN_UOMID_REF')
                                    ->orderBy('TBL_TRN_PDPRO_REQ.PRO_REQID', 'DESC')
                                    ->get();

                
                //$addRecord      =   [true];   
                /* 
                foreach($ObjProReqItem as $PRIindex=>$PRIRow){
                    
                    $ObjSavedQty    =   DB::table('TBL_TRN_PDRPR_MAT')
                                        ->where('TBL_TRN_PDRPR_MAT.PROID_REF','=',$PRIRow->PROID_REF)
                                        ->where('TBL_TRN_PDRPR_MAT.SOID_REF','=',trim($PRIRow->SOID_REF)==""?NULL:$PRIRow->SOID_REF)             
                                        ->where('TBL_TRN_PDRPR_MAT.SQID_REF','=',trim($PRIRow->SQID_REF)==""?NULL:$PRIRow->SQID_REF)
                                        ->where('TBL_TRN_PDRPR_MAT.SEID_REF','=',trim($PRIRow->SEID_REF)==""?NULL:$PRIRow->SEID_REF)
                                        ->where('TBL_TRN_PDRPR_MAT.MAINITEMID_REF','=',$PRIRow->SOITEMID_REF)
                                        ->where('TBL_TRN_PDRPR_MAT.ITEMID_REF','=',$PRIRow->ITEMID_REF)
                                        ->where('TBL_TRN_PDRPR_HDR.STATUS','<>','C')
                                        ->leftJoin('TBL_TRN_PDRPR_HDR',   'TBL_TRN_PDRPR_HDR.RPRID','=',   'TBL_TRN_PDRPR_MAT.RPRID_REF')
                                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDRPR_MAT.REQ_QTY),0) AS REQ_QTY'))                       
                                        ->get();

                    $Total_ReqQty   =   $ObjSavedQty[0]->REQ_QTY;

                    $ObjConsumedQty =   DB::table('TBL_TRN_PDRPR_MAT')                                    
                                        ->where('TBL_TRN_PDRPR_MAT.RPRID_REF','=',$RPRID)
                                        ->where('TBL_TRN_PDRPR_MAT.PROID_REF','=',$PRIRow->PROID_REF)
                                        ->where('TBL_TRN_PDRPR_MAT.SOID_REF','=',trim($PRIRow->SOID_REF)==""?NULL:$PRIRow->SOID_REF)             
                                        ->where('TBL_TRN_PDRPR_MAT.SQID_REF','=',trim($PRIRow->SQID_REF)==""?NULL:$PRIRow->SQID_REF)
                                        ->where('TBL_TRN_PDRPR_MAT.SEID_REF','=',trim($PRIRow->SEID_REF)==""?NULL:$PRIRow->SEID_REF)
                                        ->where('TBL_TRN_PDRPR_MAT.MAINITEMID_REF','=',$PRIRow->SOITEMID_REF)
                                        ->where('TBL_TRN_PDRPR_MAT.ITEMID_REF','=',$PRIRow->ITEMID_REF)
                                        ->where('TBL_TRN_PDRPR_HDR.STATUS','<>','C') 
                                        ->leftJoin('TBL_TRN_PDRPR_HDR',   'TBL_TRN_PDRPR_HDR.RPRID','=',   'TBL_TRN_PDRPR_MAT.RPRID_REF')
                                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDRPR_MAT.REQ_QTY),0) AS CONSUMED_REQ_QTY'))
                                        ->get();
                
                    $Total_ReqQty   =   number_format( floatVal($Total_ReqQty) - floatval($ObjConsumedQty[0]->CONSUMED_REQ_QTY), 3,".","" ) ;

                    if(floatval($PRIRow->CHANGES_PD_OR_QTY)>floatval($Total_ReqQty)){
                        $addRecord[]=true;
                    }
                    else{
                        $addRecord[]=false;
                    }  
    
                }
                */


                //if(in_array('true',$addRecord)){
                   
                    $FROMQTY =0;                     
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


                    $item_unique_row_id  =   $dataRow->SOID_REF."_".$dataRow->SQID_REF."_".$dataRow->SEID_REF."_".$dataRow->ITEMID;    
                    
           
                 

                    $row = '';
                    $row = $row.'
                    <tr id="item_'.$index.'"  class="clsfgiid">
                        <td style="width:8%;text-align:center;"><input type="checkbox" id="chkfgiId'.$index.'"  value="'.$dataRow->ITEMID.'" class="fgijs-selectall1"  ></td>
                        <td style="width:10%;">'.$dataRow->ICODE.'</td>
                        <td style="width:10%;">'.$dataRow->NAME.'</td>
                        <td style="width:8%;">'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>
                        <td style="width:8%;display:none;" >'.$FROMQTY.'</td>
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
                                data-desc8="'.$dataRow->SQID_REF.'"
                                data-desc9="'.$dataRow->SEID_REF.'"
                                data-desc10=""
                                data-desc11="'.$dataRow->SOID_REF.'"
                                data-proid="'.$PROID_REF.'"
                            />
                        </td>                        
                    </tr>';
                    echo $row;  
                // }
                // else{
                //     echo "No Record found.";
                // }  
            } 
            die;        
        }           
        else{
            echo '<tr><td> Record not found.</td></tr>';
        }
        exit();
    }
    
    public function getAllItem(Request $request){
        
        $CYID_REF   =   Auth::user()->CYID_REF;
        $item_array     =   $request['item_array'];
        $STID_REF     =   $request['STID_REF'];
        $material_array=array();
        $row_array=array();
        $mat_row_array=array();

        $AlpsStatus =   $this->AlpsStatus();

        foreach($item_array as $key=>$val){         

            $exp            =   explode("_",$val);
            $PROID_REF      =   $exp[0];
            $SOID_REF       =   $exp[1];
            $SEID_REF       =   $exp[2];
            $SQID_REF       =   $exp[3];
            $MAINITEM_ID    =   $exp[4];
            $MAINITEM_UOMID =   $exp[5];  
            
            $ObjMainItem =  DB::select('SELECT TOP 1  ITEMID, ICODE ,NAME
                    FROM TBL_MST_ITEM  
                    WHERE  CYID_REF = ?  AND ITEMID = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                    [$CYID_REF, $MAINITEM_ID, 'A' ]);

            
                                
            $MAINITEM_CODE = $ObjMainItem[0]->ICODE;
            $MAINITEM_NAME = $ObjMainItem[0]->NAME;

            $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS 
                            FROM TBL_MST_UOM  
                            WHERE  CYID_REF = ?  AND UOMID = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                            [$CYID_REF, $MAINITEM_UOMID, 'A' ]);           

            //$MAINUOMCODE = $ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS;  
            $MAINUOMCODE = $ObjMainUOM[0]->DESCRIPTIONS;            

            $material_array[]=array(
                'PROID_REF'=>$PROID_REF,
                'SOID_REF'=>$SOID_REF,
                'SEID_REF'=>$SEID_REF, 
                'SQID_REF'=>$SQID_REF, 
                'MAINITEM_ID'=>$MAINITEM_ID, 
                'MAINITEM_CODE'=>$MAINITEM_CODE, 
                'MAINITEM_NAME'=>$MAINITEM_NAME, 
                'MAINITEM_UOMID'=>$MAINITEM_UOMID,            
                'MAINITEM_UOMCODE'=>$MAINUOMCODE               
            );
            
        }

        $row_array= $material_array;

        $tr ='<tr  class="participantRow">
                <td hidden><input type="hidden" id="0" > </td>                
                <td><input  type="text" name="popupFGI_0" id="popupFGI_0" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                <td   hidden><input type="text" name="FGI_REF_0" id="FGI_REF_0" class="form-control" autocomplete="off" /></td>
                <td><input type="text" name="FGIName_0" id="FGIName_0" class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>
                <td><input type="text" name="popupMAINITEMUOM_0" id="popupMAINITEMUOM_0" class="form-control"  autocomplete="off"  readonly style="width:100px;"/></td>
                <td  hidden><input type="text" name="MAINITEM_UOMID_REF_0" id="MAINITEM_UOMID_REF_0" readonly class="form-control"  autocomplete="off" /></td>
        
                <td     hidden><input type="text" name="SOID_REF_0" id="SOID_REF_0" class="form-control" autocomplete="off" /></td>
                <td     hidden><input type="text" name="SQID_REF_0" id="SQID_REF_0" class="form-control" autocomplete="off" /></td>
                <td     hidden><input type="text" name="SEID_REF_0" id="SEID_REF_0" class="form-control" autocomplete="off" /></td>
            
                <td><input  type="text" name="popupITEMID_0" id="popupITEMID_0" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                <td  hidden><input type="text" name="ITEMID_REF_0" id="ITEMID_REF_0" class="form-control" autocomplete="off" /></td>
            
                <td><input type="text" name="ItemName_0" id="ItemName_0" class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>
                
                <td '.$AlpsStatus['hidden'].'><input type="text" name="Alpspartno_0" id="Alpspartno_0" class="form-control"  autocomplete="off"  readonly/></td>
                <td '.$AlpsStatus['hidden'].'><input type="text" name="Custpartno_0" id="Custpartno_0" class="form-control"  autocomplete="off"  readonly/></td>
                <td '.$AlpsStatus['hidden'].'><input type="text" name="OEMpartno_0"  id="OEMpartno_0" class="form-control"  autocomplete="off"   readonly/></td>

                
                
                <td><input type="text" name="popupMUOM_0" id="popupMUOM_0" class="form-control"  autocomplete="off"  readonly style="width:100px;"/></td>
                <td  hidden><input type="text" name="MAIN_UOMID_REF_0" id="MAIN_UOMID_REF_0" class="form-control"  autocomplete="off" /></td>
        
        
                <td><input type="text"   name="QTY_0" id="QTY_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  style="width:100px;"  /></td>
        
                <td><input type="text"   name="REQ_QTY_0" id="REQ_QTY_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="change_production_order(this.id,this.value)"  /></td>

                <td><input type="text"   name="STOCK_INHAND_0" readonly id="STOCK_INHAND_0"   class="form-control" maxlength="200"  autocomplete="off" style="width:100px;" /></td>

                <td><input type="text"   name="REMARKS_0" id="REMARKS_0" class="form-control" maxlength="200"  autocomplete="off" style="width:200px;" /></td>
                
                <td align="center" >
                <button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                </td>
            </tr>';


        //echo "<pre>";
        //print_r($material_array);die;    

        if(!empty($material_array)){
            foreach($row_array as $mindex=>$mrow){
         
            $RPRID      =   isset($request['RPRID'])?$request['RPRID']:0;          
           
            $ObjItem =   DB::table('TBL_TRN_PDPRO_REQ')
                        ->where('TBL_TRN_PDPRO_REQ.PROID_REF','=',$mrow['PROID_REF'])
                        ->where('TBL_TRN_PDPRO_REQ.SOID_REF','=',trim($mrow['SOID_REF'])==""?NULL:$mrow['SOID_REF'])
                        ->where('TBL_TRN_PDPRO_REQ.SQID_REF','=',trim($mrow['SQID_REF'])==""?NULL:$mrow['SQID_REF'])
                        ->where('TBL_TRN_PDPRO_REQ.SEID_REF','=',trim($mrow['SEID_REF'])==""?NULL:$mrow['SEID_REF'])
                        ->where('TBL_TRN_PDPRO_REQ.SOITEMID_REF','=',$mrow['MAINITEM_ID'])      
                        ->where('TBL_MST_ITEM.MATERIAL_TYPE','=','RM-Raw Material')
                        ->leftJoin('TBL_MST_ITEM',   'TBL_MST_ITEM.ITEMID','=',   'TBL_TRN_PDPRO_REQ.ITEMID_REF')   
                        ->select('TBL_TRN_PDPRO_REQ.*','TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ITEMGID_REF','TBL_MST_ITEM.ICID_REF','TBL_MST_ITEM.ITEM_SPECI','TBL_MST_ITEM.MAIN_UOMID_REF','TBL_MST_ITEM.MATERIAL_TYPE')
                        ->orderBy('TBL_TRN_PDPRO_REQ.PRO_REQID', 'DESC')
                        ->get();

            if(count($ObjItem) > 0){
                $ObjItem=$ObjItem;
            }
            else{

                $ObjItem =   DB::table('TBL_TRN_PDPRO_REQ')
                        ->where('TBL_TRN_PDPRO_REQ.PROID_REF','=',$mrow['PROID_REF'])
                        ->where('TBL_TRN_PDPRO_REQ.SOID_REF','=',trim($mrow['SOID_REF'])==""?NULL:$mrow['SOID_REF'])
                        ->where('TBL_TRN_PDPRO_REQ.SQID_REF','=',trim($mrow['SQID_REF'])==""?NULL:$mrow['SQID_REF'])
                        ->where('TBL_TRN_PDPRO_REQ.SEID_REF','=',trim($mrow['SEID_REF'])==""?NULL:$mrow['SEID_REF'])
                        ->where('TBL_TRN_PDPRO_REQ.ITEMID_REF','=',$mrow['MAINITEM_ID'])      
                        ->leftJoin('TBL_MST_ITEM',   'TBL_MST_ITEM.ITEMID','=',   'TBL_TRN_PDPRO_REQ.ITEMID_REF')   
                        ->where('TBL_MST_ITEM.MATERIAL_TYPE','=','RM-Raw Material')
                        ->select('TBL_TRN_PDPRO_REQ.*','TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ITEMGID_REF','TBL_MST_ITEM.ICID_REF','TBL_MST_ITEM.ITEM_SPECI','TBL_MST_ITEM.MAIN_UOMID_REF','TBL_MST_ITEM.MATERIAL_TYPE')
                        ->orderBy('TBL_TRN_PDPRO_REQ.PRO_REQID', 'DESC')
                        ->get();

            }


            $tempObjItem = $ObjItem;            
            foreach($tempObjItem as $tmpindex=>$tmpRow){

                $ObjSavedQty =   DB::table('TBL_TRN_PDRPR_MAT')
                        ->where('TBL_TRN_PDRPR_MAT.PROID_REF','=',$tmpRow->PROID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.SOID_REF','=',trim($tmpRow->SOID_REF)==""?NULL:$tmpRow->SOID_REF)             
                        ->where('TBL_TRN_PDRPR_MAT.SQID_REF','=',trim($tmpRow->SQID_REF)==""?NULL:$tmpRow->SQID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.SEID_REF','=',trim($tmpRow->SEID_REF)==""?NULL:$tmpRow->SEID_REF)
                            ->where('TBL_TRN_PDRPR_MAT.MAINITEMID_REF','=',$tmpRow->SOITEMID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.ITEMID_REF','=',$tmpRow->ITEMID_REF)
                        ->where('TBL_TRN_PDRPR_HDR.STATUS','<>','C')
                        ->leftJoin('TBL_TRN_PDRPR_HDR',   'TBL_TRN_PDRPR_HDR.RPRID','=',   'TBL_TRN_PDRPR_MAT.RPRID_REF')
                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDRPR_MAT.REQ_QTY),0) AS REQ_QTY'))                       
                        ->get();

                $Total_ReqQty = $ObjSavedQty[0]->REQ_QTY;
                
                $PRO_REQ_QTY =  isset($tmpRow->CHANGES_PD_OR_QTY)? $tmpRow->CHANGES_PD_OR_QTY : 0;              
                $BAL_PRO_REQ_QTY = number_format( floatVal($PRO_REQ_QTY) - floatval($Total_ReqQty), 3,".","" ) ;

                $ObjConsumedQty =   DB::table('TBL_TRN_PDRPR_MAT')                                    
                        ->where('TBL_TRN_PDRPR_MAT.RPRID_REF','=',$RPRID)
                        ->where('TBL_TRN_PDRPR_MAT.PROID_REF','=',$tmpRow->PROID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.SOID_REF','=',trim($tmpRow->SOID_REF)==""?NULL:$tmpRow->SOID_REF)             
                        ->where('TBL_TRN_PDRPR_MAT.SQID_REF','=',trim($tmpRow->SQID_REF)==""?NULL:$tmpRow->SQID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.SEID_REF','=',trim($tmpRow->SEID_REF)==""?NULL:$tmpRow->SEID_REF)
                            ->where('TBL_TRN_PDRPR_MAT.MAINITEMID_REF','=',$tmpRow->SOITEMID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.ITEMID_REF','=',$tmpRow->ITEMID_REF)
                        ->where('TBL_TRN_PDRPR_HDR.STATUS','<>','C') 
                        ->leftJoin('TBL_TRN_PDRPR_HDR',   'TBL_TRN_PDRPR_HDR.RPRID','=',   'TBL_TRN_PDRPR_MAT.RPRID_REF')
                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDRPR_MAT.REQ_QTY),0) AS CONSUMED_REQ_QTY'))
                        ->get();

                $BAL_PRO_REQ_QTY = number_format( floatVal($BAL_PRO_REQ_QTY) + floatval($ObjConsumedQty[0]->CONSUMED_REQ_QTY), 3,".","" ) ;
                
                $ObjItem[$tmpindex]->BAL_PRO_REQ_QTY = $BAL_PRO_REQ_QTY;
                $ObjItem[$tmpindex]->ITEMQTY = number_format( floatval($ObjConsumedQty[0]->CONSUMED_REQ_QTY), 3,".","" ) ;

                $ObjSubItemUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS 
                FROM TBL_MST_UOM  
                WHERE  CYID_REF = ?  AND UOMID = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                [$CYID_REF, $tmpRow->MAIN_UOMID_REF, 'A' ]);           

                $ObjItem[$tmpindex]->ITEM_UOMID = $tmpRow->MAIN_UOMID_REF;
                //$ObjItem[$tmpindex]->ITEM_UOMCODE = $ObjSubItemUOM[0]->UOMCODE.'-'.$ObjSubItemUOM[0]->DESCRIPTIONS; 
                $ObjItem[$tmpindex]->ITEM_UOMCODE = $ObjSubItemUOM[0]->DESCRIPTIONS;  
                
                
                $ItemRowData =  DB::select('SELECT top 1 * FROM TBL_MST_ITEM  WHERE ITEMID = ? ', [$tmpRow->ITEMID_REF]);

                $ALPS_PART_NO       =   $ItemRowData[0]->ALPS_PART_NO;
                $CUSTOMER_PART_NO   =   $ItemRowData[0]->CUSTOMER_PART_NO;
                $OEM_PART_NO        =   $ItemRowData[0]->OEM_PART_NO;

                $ObjItem[$tmpindex]->ALPS_PART_NO       =   $ALPS_PART_NO;
                $ObjItem[$tmpindex]->CUSTOMER_PART_NO   =   $CUSTOMER_PART_NO;
                $ObjItem[$tmpindex]->OEM_PART_NO        =   $OEM_PART_NO;
                
            }

            
           
                foreach($ObjItem as $itmindex=>$itmRow){
                    $mat_row_array[] =array(
                        'PROID_REF'=>$mrow['PROID_REF'],
                        'SOID_REF'=>$mrow['SOID_REF'],
                        'SEID_REF'=>$mrow['SEID_REF'], 
                        'SQID_REF'=>$mrow['SQID_REF'], 
                        'MAINITEM_ID'=>$mrow['MAINITEM_ID'], 
                        'MAINITEM_CODE'=>$mrow['MAINITEM_CODE'], 
                        'MAINITEM_NAME'=>$mrow['MAINITEM_NAME'], 
                        'MAINITEM_UOMID'=>$mrow['MAINITEM_UOMID'],            
                        'MAINITEM_UOMCODE'=>$mrow['MAINITEM_UOMCODE'],  
                        'ITEMID'=>$itmRow->ITEMID_REF,             
                        'ITEM_ICODE'=>$itmRow->ICODE,             
                        'ITEM_NAME'=>$itmRow->NAME,             
                        'ITEM_SPECI'=>$itmRow->ITEM_SPECI,             
                        'ITEM_UOMID'=>$itmRow->ITEM_UOMID,             
                        'ITEM_UOMCODE'=>$itmRow->ITEM_UOMCODE,             
                        'BAL_PRO_REQ_QTY'=>$itmRow->BAL_PRO_REQ_QTY,             
                        'ITEMQTY'=>$itmRow->ITEMQTY,
                        'ALPS_PART_NO'=>$itmRow->ALPS_PART_NO,     
                        'CUSTOMER_PART_NO'=>$itmRow->CUSTOMER_PART_NO,     
                        'OEM_PART_NO'=>$itmRow->OEM_PART_NO,                           
                    );
                }


            }
         
            $rowscount = count($mat_row_array)>0 ?count($mat_row_array) :1;
            $rowdata = '';
            foreach($mat_row_array as $mrindex=>$mrval){

                $ALPS_PART_NO       =   isset($mrval['ALPS_PART_NO'])?$mrval['ALPS_PART_NO']:NULL;
                $CUSTOMER_PART_NO   =   isset($mrval['CUSTOMER_PART_NO'])?$mrval['CUSTOMER_PART_NO']:NULL;
                $OEM_PART_NO        =   isset($mrval['OEM_PART_NO'])?$mrval['OEM_PART_NO']:NULL;
                $StockInHand       =  $this->GetStockInHand($STID_REF,$mrval["ITEMID"]);
                

                if(floatval($mrval["BAL_PRO_REQ_QTY"])>0)
                {
                        $rowdata =$rowdata. '
                        <tr  class="participantRow">
                        <td hidden><input type="hidden" id="'.$mrindex.'" > </td>

                        <td><input  type="text" name="popupFGI_'.$mrindex.'" id="popupFGI_'.$mrindex.'"  value="'.$mrval["MAINITEM_CODE"].'" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                        <td   hidden><input type="text" name="FGI_REF_'.$mrindex.'" id="FGI_REF_'.$mrindex.'"  value="'.$mrval["MAINITEM_ID"].'" class="form-control"  autocomplete="off" /></td>
                        <td><input type="text" name="FGIName_'.$mrindex.'"      id="FGIName_'.$mrindex.'"  value="'.$mrval["MAINITEM_NAME"].'" class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>
                        <td><input type="text" name="popupMAINITEMUOM_'.$mrindex.'"     id="popupMAINITEMUOM_'.$mrindex.'"   value="'.$mrval["MAINITEM_UOMCODE"].'" class="form-control"  autocomplete="off"  readonly style="width:100px;"/></td>
                        <td  hidden><input type="text" name="MAINITEM_UOMID_REF_'.$mrindex.'" id="MAINITEM_UOMID_REF_'.$mrindex.'" value="'.$mrval["MAINITEM_UOMID"].'" readonly class="form-control"  autocomplete="off" /></td>

                        <td     hidden><input type="text" name="SOID_REF_'.$mrindex.'" id="SOID_REF_'.$mrindex.'" value="'.$mrval["SOID_REF"].'" class="form-control" autocomplete="off" /></td>
                        <td     hidden><input type="text" name="SQID_REF_'.$mrindex.'" id="SQID_REF_'.$mrindex.'" value="'.$mrval["SQID_REF"].'"  class="form-control" autocomplete="off" /></td>
                        <td     hidden><input type="text" name="SEID_REF_'.$mrindex.'" id="SEID_REF_'.$mrindex.'" value="'.$mrval["SEID_REF"].'"  class="form-control" autocomplete="off" /></td>

                        <td><input  type="text" name="popupITEMID_'.$mrindex.'" id="popupITEMID_'.$mrindex.'" value="'.$mrval["ITEM_ICODE"].'"  class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                        <td  hidden><input type="text" name="ITEMID_REF_'.$mrindex.'" id="ITEMID_REF_'.$mrindex.'"  value="'.$mrval["ITEMID"].'"  class="form-control" autocomplete="off" /></td>

                        <td><input type="text" name="ItemName_'.$mrindex.'" id="ItemName_'.$mrindex.'"  value="'.$mrval["ITEM_NAME"].'"   class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>
                        
                        
                        <td '.$AlpsStatus['hidden'].'><input  type="text" name="Alpspartno_'.$mrindex.'" id="Alpspartno_'.$mrindex.'" class="form-control"  autocomplete="off" value="'.$mrval["ALPS_PART_NO"].'" readonly/></td>
                        <td '.$AlpsStatus['hidden'].'><input  type="text" name="Custpartno_'.$mrindex.'" id="Custpartno_'.$mrindex.'" class="form-control"  autocomplete="off" value="'.$mrval["CUSTOMER_PART_NO"].'" readonly/></td>
                        <td '.$AlpsStatus['hidden'].'><input  type="text" name="OEMpartno_'.$mrindex.'"  id="OEMpartno_'.$mrindex.'" class="form-control"  autocomplete="off" value="'.$mrval["OEM_PART_NO"].'" readonly/></td>

                        
                        <td><input type="text" name="popupMUOM_'.$mrindex.'" id="popupMUOM_'.$mrindex.'"  value="'.$mrval["ITEM_UOMCODE"].'"  class="form-control"  autocomplete="off"  readonly style="width:100px;"/></td>
                        <td  hidden><input type="text" name="MAIN_UOMID_REF_'.$mrindex.'" id="MAIN_UOMID_REF_'.$mrindex.'"  value="'.$mrval["ITEM_UOMID"].'"  class="form-control"  autocomplete="off" /></td>


                        <td><input type="text"   name="QTY_'.$mrindex.'" id="QTY_'.$mrindex.'"  value="'.$mrval["BAL_PRO_REQ_QTY"].'"  class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  style="width:100px;"  /></td>

                        <td><input type="text"   name="REQ_QTY_'.$mrindex.'" id="REQ_QTY_'.$mrindex.'"   value="'.$mrval["BAL_PRO_REQ_QTY"].'" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="change_production_order(this.id,this.value)"  /></td>

                        <td><input type="text"   name="STOCK_INHAND_'.$mrindex.'" id="STOCK_INHAND_'.$mrindex.'"  readonly value="'.$StockInHand.'" class="form-control" maxlength="200"  autocomplete="off" style="width:100px;" /></td>

                        <td><input type="text"   name="REMARKS_'.$mrindex.'" id="REMARKS_'.$mrindex.'" class="form-control" maxlength="200"  autocomplete="off" style="width:200px;" /></td>

                        <td align="center" >
                        <button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                        <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                        </td>
                        </tr>
                        ';
                }
                
            }

            if(!empty($rowdata)){
                return Response::json(['totalrows' =>$rowscount,'matrows' => $rowdata]);
            }else{               
                return Response::json(['totalrows' =>$rowscount,'matrows' =>$tr]);
            }
            

        }
        else{
           
            return Response::json(['totalrows' =>1,'matrows' =>$tr]);
        
        }
        exit();
    }

    public function getItemDetails(Request $request){
       // dd($request->all()); 
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $Status     =   $request['status'];
        $STID_REF  =   $request['STID_REF'];
        $PROID_REF  =   $request['PROID_REF'];
        $FGI_REF    =   $request['FGI_REF'];
        $SQID_REF    =   trim($request['SQID_REF'])==""?NULL:$request['SQID_REF'];
        $SEID_REF    =   trim($request['SEID_REF'])==""?NULL:$request['SEID_REF'];
        $SOID_REF    =   trim($request['SOID_REF'])==""?NULL:$request['SOID_REF'];
        $RPRID      =   isset($request['RPRID'])?$request['RPRID']:0;

       

        $AlpsStatus =   $this->AlpsStatus();

        $ObjItem =   DB::table('TBL_TRN_PDPRO_REQ')
                    ->where('TBL_TRN_PDPRO_REQ.PROID_REF','=',$PROID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SOID_REF','=',$SOID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SQID_REF','=',$SQID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SEID_REF','=',$SEID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SOITEMID_REF','=',$FGI_REF)  
                    ->leftJoin('TBL_MST_ITEM',   'TBL_MST_ITEM.ITEMID','=',   'TBL_TRN_PDPRO_REQ.ITEMID_REF') 
                    ->where('TBL_MST_ITEM.MATERIAL_TYPE','=','RM-Raw Material')   
                    ->select('TBL_TRN_PDPRO_REQ.*','TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ITEMGID_REF','TBL_MST_ITEM.ICID_REF','TBL_MST_ITEM.ITEM_SPECI','TBL_MST_ITEM.MAIN_UOMID_REF','TBL_MST_ITEM.MATERIAL_TYPE')
                    ->orderBy('TBL_TRN_PDPRO_REQ.PRO_REQID', 'DESC')
                    ->get();


        if(count($ObjItem) > 0){
            $ObjItem=$ObjItem;
        }
        else{

            $ObjItem =   DB::table('TBL_TRN_PDPRO_REQ')
                    ->where('TBL_TRN_PDPRO_REQ.PROID_REF','=',$PROID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SOID_REF','=',$SOID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SQID_REF','=',$SQID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.SEID_REF','=',$SEID_REF)
                    ->where('TBL_TRN_PDPRO_REQ.ITEMID_REF','=',$FGI_REF)      
                    ->leftJoin('TBL_MST_ITEM',   'TBL_MST_ITEM.ITEMID','=',   'TBL_TRN_PDPRO_REQ.ITEMID_REF')   
                    ->where('TBL_MST_ITEM.MATERIAL_TYPE','=','RM-Raw Material')
                    ->select('TBL_TRN_PDPRO_REQ.*','TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ITEMGID_REF','TBL_MST_ITEM.ICID_REF','TBL_MST_ITEM.ITEM_SPECI','TBL_MST_ITEM.MAIN_UOMID_REF','TBL_MST_ITEM.MATERIAL_TYPE')
                    ->orderBy('TBL_TRN_PDPRO_REQ.PRO_REQID', 'DESC')
                    ->get();
        }

     


        if(!empty($ObjItem)){

            foreach ($ObjItem as $index=>$dataRow){

                $ObjSavedQty =   DB::table('TBL_TRN_PDRPR_MAT')
                        ->where('TBL_TRN_PDRPR_MAT.PROID_REF','=',$dataRow->PROID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.SOID_REF','=',trim($dataRow->SOID_REF)==""?NULL:$dataRow->SOID_REF)             
                        ->where('TBL_TRN_PDRPR_MAT.SQID_REF','=',trim($dataRow->SQID_REF)==""?NULL:$dataRow->SQID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.SEID_REF','=',trim($dataRow->SEID_REF)==""?NULL:$dataRow->SEID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.MAINITEMID_REF','=',$dataRow->SOITEMID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.ITEMID_REF','=',$dataRow->ITEMID_REF)
                        ->where('TBL_TRN_PDRPR_HDR.STATUS','<>','C')
                        ->leftJoin('TBL_TRN_PDRPR_HDR',   'TBL_TRN_PDRPR_HDR.RPRID','=',   'TBL_TRN_PDRPR_MAT.RPRID_REF')
                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDRPR_MAT.REQ_QTY),0) AS REQ_QTY'))                       
                        ->get();
                $Total_ReqQty = $ObjSavedQty[0]->REQ_QTY;
                
                $PRO_REQ_QTY =  isset($dataRow->CHANGES_PD_OR_QTY)? $dataRow->CHANGES_PD_OR_QTY : 0;              
                $BAL_PRO_REQ_QTY = number_format( floatVal($PRO_REQ_QTY) - floatval($Total_ReqQty), 3,".","" ) ;

                
                //ADD CONSUMED QTY               
                $ObjConsumedQty =   DB::table('TBL_TRN_PDRPR_MAT')                                    
                        ->where('TBL_TRN_PDRPR_MAT.RPRID_REF','=',$RPRID)
                        ->where('TBL_TRN_PDRPR_MAT.PROID_REF','=',$dataRow->PROID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.SOID_REF','=',trim($dataRow->SOID_REF)==""?NULL:$dataRow->SOID_REF)             
                        ->where('TBL_TRN_PDRPR_MAT.SQID_REF','=',trim($dataRow->SQID_REF)==""?NULL:$dataRow->SQID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.SEID_REF','=',trim($dataRow->SEID_REF)==""?NULL:$dataRow->SEID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.MAINITEMID_REF','=',$dataRow->SOITEMID_REF)
                        ->where('TBL_TRN_PDRPR_MAT.ITEMID_REF','=',$dataRow->ITEMID_REF)
                        ->where('TBL_TRN_PDRPR_HDR.STATUS','<>','C') 
                        ->leftJoin('TBL_TRN_PDRPR_HDR',   'TBL_TRN_PDRPR_HDR.RPRID','=',   'TBL_TRN_PDRPR_MAT.RPRID_REF')
                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDRPR_MAT.REQ_QTY),0) AS CONSUMED_REQ_QTY'))
                        ->get();

                        
                
                $BAL_PRO_REQ_QTY = number_format( floatVal($BAL_PRO_REQ_QTY) + floatval($ObjConsumedQty[0]->CONSUMED_REQ_QTY), 3,".","" ) ;
                
                               
                     
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

                $item_unique_row_id  =  $FGI_REF."_".$dataRow->SOID_REF."_".$dataRow->SQID_REF."_".$dataRow->SEID_REF."_".$dataRow->ITEMID; 
                
                
                $Stock_In_Hand       =  $this->GetStockInHand($STID_REF,$dataRow->ITEMID);

                $row = '';
                if( floatVal($BAL_PRO_REQ_QTY)>0){
                    $row = $row.'
                    <tr id="item_'.$index.'"  class="clsitemid">
                        <td style="width:8%;text-align:center;"><input type="checkbox" id="chkId'.$index.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>
                        <td style="width:10%;">'.$dataRow->ICODE.'</td>
                        <td style="width:10%;">'.$dataRow->NAME.'</td>
                        <td style="width:8%;">'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>
                        <td style="width:8%;">'.$BAL_PRO_REQ_QTY.'</td>
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
                                data-desc6="'.$BAL_PRO_REQ_QTY.'" 
                                data-desc7="'.$item_unique_row_id.'" 
                                data-desc8="'.$dataRow->SQID_REF.'"
                                data-desc9="'.$dataRow->SEID_REF.'"
                                data-desc10=""
                                data-desc11="'.$dataRow->SOID_REF.'"
                                data-desc12="'.$Stock_In_Hand.'"
                                data-itemfgiid="'.$FGI_REF.'"
                                data-itemsavedqty="'.number_format( floatval($ObjConsumedQty[0]->CONSUMED_REQ_QTY), 3,".","" ).'"
                            />
                        </td>
                        <td hidden><input type="hidde" id="addinfoitem_'.$index.'"  data-desc101="'.$ALPS_PART_NO.'" data-desc102="'.$CUSTOMER_PART_NO.'" data-desc103="'.$OEM_PART_NO.'" ></td>
                    </tr>';
                    echo $row;   
                } 
            }         
        }           
        else{
            echo '<tr><td> Record not found.</td></tr>';
        }
        exit();
    }


    
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


    public function GetStockInHand($STID_REF,$ITEMID_REF)
    {  $CYID_REF           =   Auth::user()->CYID_REF;
       $BRID_REF           =   Session::get('BRID_REF');
       $ObjStock            =   DB::select("SELECT SUM(CURRENT_QTY) AS CURRENT_QTY FROM TBL_MST_BATCH WHERE ITEMID_REF=$ITEMID_REF AND                  
                                BRID_REF=$BRID_REF AND CYID_REF=$CYID_REF AND STID_REF=$STID_REF ");
       $Stock_In_Hand       =   isset($ObjStock[0]->CURRENT_QTY) ? $ObjStock[0]->CURRENT_QTY:"0.00"; 
       return $Stock_In_Hand;
    }

    
    

   

    
}
