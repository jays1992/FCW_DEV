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

class TrnFrm305Controller extends Controller{

    protected $form_id  = 305;
    protected $vtid_ref = 395;
    protected $view     = "transactions.Production.MaterialIssueSlipRPR.trnfrm";
   
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];

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

        $objDataList	=	DB::select("select hdr.*,T2.PRO_TITLE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.MISRID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_PDMISR_HDR hdr
                            on a.VID = hdr.MISRID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            INNER JOIN TBL_TRN_PDRPR_HDR T1 ON hdr.RPRID_REF=T1.RPRID
                            LEFT JOIN TBL_TRN_PDPRO_HDR T2 ON T1.PROID_REF=T2.PROID
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.MISRID DESC ");

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
        $MISRID       =   $myValue['MISRID'];
        $Flag       =   $myValue['Flag'];
        
        $ssrs = new \SSRS\Report('http://103.139.58.23:8181//ReportServer/', array('username' => 'App', 'password' => 'admin@123'));
        $result = $ssrs->loadReport('/ERP/MISRPrint');
        
        $reportParameters = array(
            'MISRID' => $MISRID,
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
        $objRPR             =   $this->getRPRNo();
        $objPStage          =   $this->getobjPStage();
        $objStore           =   $this->getobjStore();
        $objEmployee        =   $this->getobjEmployee(); 
        
        $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by UOMCODE ASC', [$CYID_REF, $BRID_REF,  'A' ]);   
        
        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_PDMISR_HDR',
            'HDR_ID'=>'MISRID',
            'HDR_DOC_NO'=>'MISRNO',
            'HDR_DOC_DT'=>'MISRDT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        
   
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_PMIS")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                {       
                                $query->select('UDFPMISID')->from('TBL_MST_UDFFOR_PMIS')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);                      
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);                   
                   

        $objUdfData = DB::table('TBL_MST_UDFFOR_PMIS')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();  

        $objCountUDF = count($objUdfData);

        $FormId     =   $this->form_id;
        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	    =	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        $checkCompany   =   $this->misrCheckIssueReQsQty();
        
        
        return view($this->view.$FormId.'add',compact([
                'AlpsStatus',
                'FormId',
                'objRPR',
                'objPStage',
                'objStore',
                'objEmployee',
                'objUdfData',
               
                'objCountUDF',
              
                'objlastdt',
                'objUOM',
                'TabSetting',
                'checkCompany',
                'doc_req','docarray'
        ]));       
    }

    public function save(Request $request) {

        //dump($request->all());
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $StoreArr   =   array();
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $exp        =   explode(",",$ITEMROWID);

                foreach($exp as $val){
                    $keyid              =   explode("#",$val);
                    $batchid            =   $keyid[0];
                    $StoreArr[]         =   $batchid;
                }

                if(!empty($StoreArr)){
                    $StoreId    =   array_unique($StoreArr);
                    $STID_REF   =   implode(",",$StoreId);
                }
                else{
                    $STID_REF   =   NULL;
                }

                $req_data[$i] = [
                    'ITEMID_REF'       => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'   => $request['MAIN_UOMID_REF_'.$i],
                    'REQUEST_QTY'   => $request['PENDING_QTY_'.$i],
                    'STID'             =>  $STID_REF,
                    'ISSUED_QTY'       => $request['RECEIVED_QTY_MU_'.$i],
                    'SHORT_QTY'        => $request['SHORT_QTY_'.$i],
                    'REASON_SHORT_QTY' => $request['REASON_SHORT_QTY_'.$i],
                    'BATCH_QTY'        => $request['HiddenRowId_'.$i],
                    'REMARKS'          => $request['REMARKS_'.$i],
                    'PROID_REF'        => $request['PROID_REF_'.$i],
                    'RPRID_REF'        => $request['RPRID_REF_'.$i],
                    'SOID_REF'        => $request['SOID_REF_'.$i],
                    'SQID_REF'        => $request['SQID_REF_'.$i],
                    'SEID_REF'        => $request['SEID_REF_'.$i],
                    'MAINITEMID_REF'        => $request['FGI_REF_'.$i],
                    'MAINITEMUOMID_REF'     => $request['MAINITEM_UOMID_REF_'.$i],
                    'OLDITEMID_REF'        => $request['OLDITEM_ID_REF_'.$i],
                    //'ISSUED_QTY'       => $request['SE_QTY_'.$i],
                    //'MRS_QTY_BL'       => $request['PENDING_QTY_'.$i],
                    //'STOCK_INHAND'     => $request['STOCK_INHAND_'.$i],
                    //'ALT_UOMID_REF'    => $request['ALT_UOMID_REF_'.$i],
                ];
            }
        }

        
        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
            
        
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['UDF_'.$i])){
                $reqdata3[$i] = [
                    'UDFMISRID_REF'  => $request['UDF_'.$i],
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


        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $dataArr    =   array();
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];

                if($ITEMROWID !=""){
                    $exp        =   explode(",",$ITEMROWID);

                    foreach($exp as $val){
                        $keyid              =   explode("_",$val);
                        $batchid            =   $keyid[0];

                        $dataArr[$batchid]['RECEIVED_QTYM'] =   $keyid[1];
                        //$dataArr[$batchid]['LOT_NO']        =   $keyid[2];
                        //$dataArr[$batchid]['VENDOR_LOTNO']  =  $keyid[3];
                        $dataArr[$batchid]['STOCK_INHAND']  =  $keyid[2];
                    }
                }

                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        $ExpID              =   explode("#",$key);
                        $STID_REF           =   $ExpID[0];
                        $RPRID_REF          =   $ExpID[1];
                        $BATCH_CODE         =   $ExpID[2];
                        $ITEMID_REF         =   $ExpID[3];
                        $UOMID_REF          =   $ExpID[4];
                        $ALTUOM             =   $ExpID[5];
                        $PROID_REF          =   $ExpID[6];
                        $SOID_REF          =   $ExpID[7];
                        $SQID_REF          =   $ExpID[8];
                        $SEID_REF          =   $ExpID[9];
                        $FGI_REF           =   $ExpID[10];
                        $AluQty             =   $this->getAltUmQty($ALTUOM,$ITEMID_REF,$val['RECEIVED_QTYM']);
                       
                        $req_data33[$i][] = [
                           // 'MRSID_REF'         => $RGPID_REF,
                            'ITEMID_REF'        => $ITEMID_REF,
                            'UOMID_REF'         => $UOMID_REF,
                            'STID_REF'          => $STID_REF,
                            'BATCHID_REF'        => $BATCH_CODE,
                            'ISSUED_QTY'       => $val['RECEIVED_QTYM'],
                            'ALTUOMID_REF'     => $ALTUOM,
                            'PROID_REF'         => $PROID_REF,
                            'RPRID_REF'         => $RPRID_REF,
                            'SOID_REF'          => $SOID_REF,
                            'SQID_REF'          => $SQID_REF,
                            'SEID_REF'          => $SEID_REF,
                            'MAINITEMID_REF'    => $FGI_REF,
                            //'STOCK_INHAND'      => $val['STOCK_INHAND'], 
                            //'ISSUED_QTYA'       => $AluQty,
                        ];

                    }
                }
            }
        }

        $wrapped_links33["STORE"] = $req_data33; 
        $XMLSTORE = ArrayToXml::convert($wrapped_links33);

        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $MISRNO         = strtoupper(trim($request['MISRNO']));
        $MISRDT         = $request['MISRDT'];
        $RPRID_REF      = $request['RPRID_REF'];
        $FROM_STID_REF  = $request['STID_REF'];
        $STAGEID_REF    = $request['PSTAGEID_REF'];
        $ISSUED_BY      = $request['EMPID_REF'];
        

        $log_data = [ 
            $MISRNO, $MISRDT,$RPRID_REF ,$FROM_STID_REF,$STAGEID_REF,
            $ISSUED_BY, $CYID_REF, $BRID_REF, $FYID_REF, $VTID_REF,
            $XMLMAT, $XMLSTORE, $XMLUDF,  $USERID, Date('Y-m-d'),
            Date('h:i:s.u'), $ACTIONNAME, $IPADDRESS
        ];  

        //dump($log_data);
        $sp_result = DB::select('EXEC SP_MISR_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  

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

            $objResponse =  DB::table('TBL_TRN_PDMISR_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('MISRID','=',$id)
            ->first();

           
            $objlastdt          =   $this->getLastdt();
            $objRPR             =   $this->getRPRNo();
            $objRPRDtl          =   $this->getRPRNoDtl($objResponse->RPRID_REF);
            $objPStage          =   $this->getobjPStage();
            $objPStageDtl       =   $this->getobjPStageDtl($objResponse->STAGEID_REF);
            $objStore           =   $this->getobjStore();
            $objStoreDtl        =   $this->getobjStoreDtl($objResponse->FROM_STID_REF);
            $objEmployee        =   $this->getobjEmployee(); 
            $objEmployeeDtl     =   $this->getobjEmployeeDtl($objResponse->ISSUED_BY); 

            /*
            $objMAT = DB::table('TBL_TRN_PDMISR_MAT') 
            ->where('MISRID_REF','=',$id)
            ->select('TBL_TRN_PDMISR_MAT.*' )
            ->orderBy('MISR_MATID','ASC')
            ->get()->toArray();
            */


            $objMAT = DB::select("SELECT 
            T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,T2.ALT_UOMID_REF,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,
            CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
            CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE

            FROM TBL_TRN_PDMISR_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID
            WHERE T1.MISRID_REF='$id' ORDER BY T1.MISR_MATID ASC
            ");

            

            $objCount1 = count($objMAT);   
            
            $tempobjMAT= $objMAT;
            foreach ($tempobjMAT as $index => $value) {

                $objtempItem = DB::select('SELECT top 1 ALT_UOMID_REF FROM TBL_MST_ITEM  
                                    WHERE ITEMID=? AND MAIN_UOMID_REF=? AND CYID_REF=?', [ $value->ITEMID_REF, $value->UOMID_REF,$CYID_REF]);
                $objMAT[$index]->ALT_UOMID_REF = $objtempItem[0]->ALT_UOMID_REF;

                $objMainitem =   DB::select('SELECT top 1 ITEMID,ICODE,NAME FROM TBL_MST_ITEM  
                                    WHERE STATUS= ? AND ITEMID = ? ', ['A',$value->MAINITEMID_REF]);

                        

                
                $objMAT[$index]->MAINITEMID_CODE = $objMainitem[0]->ICODE;
                $objMAT[$index]->MAINITEMID_NAME = $objMainitem[0]->NAME;

               

                $objMainitemUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                    WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND UOMID = ? 
                                    AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                    [$CYID_REF, $BRID_REF, $FYID_REF,$value->MAINITEMUOMID_REF, 'A' ]);

                                   
                
                $objMAT[$index]->MAINITEMID_UOMCODE = isset($objMainitemUOM[0]->UOMCODE)?$objMainitemUOM[0]->UOMCODE:NULL;
                $objMAT[$index]->MAINITEMID_UOMDESC = isset($objMainitemUOM[0]->DESCRIPTIONS)?$objMainitemUOM[0]->DESCRIPTIONS:NULL;

                

                $objBatch =  DB::SELECT("SELECT 
                            T1.BATCHID,T1.BATCH_CODE,T1.CURRENT_QTY AS TOTAL_STOCK,
                            T2.STID,T2.STCODE,T2.NAME AS STNAME
                            FROM TBL_MST_BATCH T1 
                            LEFT JOIN TBL_MST_STORE T2 ON T2.STID=T1.STID_REF
                            WHERE T1.STATUS='A' AND T1.CYID_REF ='$CYID_REF' AND T1.BRID_REF ='$BRID_REF'  AND T1.ITEMID_REF =$value->ITEMID_REF AND T1.UOMID_REF =$value->UOMID_REF
                            ");
                $totalstock = 0;
                foreach($objBatch as $bindex=>$brow){
                    $bstock  = !is_null($brow->TOTAL_STOCK) && trim($brow->TOTAL_STOCK)!="" ? $brow->TOTAL_STOCK : 0;
                    $totalstock = floatval($totalstock) + floatval($bstock);

                }
                $objMAT[$index]->TOTAL_STOCK = number_format($totalstock,3,".","") ;

                //-----------------------------------------------
                $BAL_RPR_REQ_QTY = 0;
                $objMAT[$index]->TOTAL_PENDING= $BAL_RPR_REQ_QTY;

                $ObjSavedQty =   DB::table('TBL_TRN_PDMISR_MAT')
                    ->where('TBL_TRN_PDMISR_MAT.RPRID_REF','=',$value->RPRID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.PROID_REF','=',$value->PROID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.SOID_REF','=',$value->SOID_REF)             
                    ->where('TBL_TRN_PDMISR_MAT.SQID_REF','=',$value->SQID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.SEID_REF','=',$value->SEID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.MAINITEMID_REF','=',$value->MAINITEMID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.OLDITEMID_REF','=',$value->OLDITEMID_REF)
                    ->where('TBL_TRN_PDMISR_HDR.STATUS','<>','C') 
                    ->leftJoin('TBL_TRN_PDMISR_HDR',   'TBL_TRN_PDMISR_HDR.MISRID','=',   'TBL_TRN_PDMISR_MAT.MISRID_REF')
                    ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDMISR_MAT.ISSUED_QTY),0) AS ISSUED_QTY'))                       
                    ->get();
                // dump($ObjSavedQty);
                $Total_ISSUED_QTY = $ObjSavedQty[0]->ISSUED_QTY;

                //get from TBL_TRN_PDRPR_MAT req qty
                $ObjRPRItem =   DB::table('TBL_TRN_PDRPR_MAT')
                    ->where('TBL_TRN_PDRPR_MAT.RPRID_REF','=',$value->RPRID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.PROID_REF','=',$value->PROID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.SOID_REF','=',$value->SOID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.SQID_REF','=',$value->SQID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.SEID_REF','=',$value->SEID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.MAINITEMID_REF','=',$value->MAINITEMID_REF)      
                    ->where('TBL_TRN_PDRPR_MAT.ITEMID_REF','=',$value->OLDITEMID_REF)      
                    ->select('TBL_TRN_PDRPR_MAT.*',)                    
                    ->first();
                $RPR_REQ_QTY =  isset($ObjRPRItem->REQ_QTY)? $ObjRPRItem->REQ_QTY : 0; 
                
                $BAL_RPR_REQ_QTY = number_format( floatVal($RPR_REQ_QTY) - floatval($Total_ISSUED_QTY), 3,".","" ) ;

                //ADD CONSUMED QTY      
                $ObjConsumedQty =   DB::table('TBL_TRN_PDMISR_MAT')                                    
                    ->where('TBL_TRN_PDMISR_MAT.MISRID_REF','=',$id)
                    ->where('TBL_TRN_PDMISR_MAT.RPRID_REF','=',$value->RPRID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.PROID_REF','=',$value->PROID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.SOID_REF','=',$value->SOID_REF)             
                    ->where('TBL_TRN_PDMISR_MAT.SQID_REF','=',$value->SQID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.SEID_REF','=',$value->SEID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.MAINITEMID_REF','=',$value->MAINITEMID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.OLDITEMID_REF','=',$value->OLDITEMID_REF)
                    ->where('TBL_TRN_PDMISR_HDR.STATUS','<>','C') 
                    ->leftJoin('TBL_TRN_PDMISR_HDR',   'TBL_TRN_PDMISR_HDR.MISRID','=',   'TBL_TRN_PDMISR_MAT.MISRID_REF')
                    ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDMISR_MAT.ISSUED_QTY),0) AS CONSUMED_ISSUED_QTY'))
                    ->get();

                $BAL_RPR_REQ_QTY = number_format( floatVal($BAL_RPR_REQ_QTY) + floatval($ObjConsumedQty[0]->CONSUMED_ISSUED_QTY), 3,".","" ) ;         

                $objMAT[$index]->TOTAL_PENDING= $BAL_RPR_REQ_QTY;               
                //--------------------------------------------------    
              
            }   
            
            // DUMP($objMAT);

            
            $objUDF = DB::table('TBL_TRN_PDMISR_UDF')                    
            ->where('MISRID_REF','=',$id)
            ->orderBy('MISR_UDFID','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF); 

            $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by UOMCODE ASC', [$CYID_REF, $BRID_REF,  'A' ]); 

            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_PMIS")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('UDFPMISID')->from('TBL_MST_UDFFOR_PMIS')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                     
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                     
                    

            $objUdfData = DB::table('TBL_MST_UDFFOR_PMIS')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_PMIS")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDFPMISID')->from('TBL_MST_UDFFOR_PMIS')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                         
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                           
          

            $objUdfData2 = DB::table('TBL_MST_UDFFOR_PMIS')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF2)
            ->get()->toArray(); 

            // dump($objUDF);
            //  dump($objUdfData2);

            /*
            $objItems = DB::table('TBL_MST_ITEM')->select('*')
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('FYID_REF','=',$FYID_REF)
            ->get() ->toArray(); 
            */

            $objItems=array();
            /*
            $objUOM = DB::table('TBL_MST_UOM')->select('*')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('FYID_REF','=',$FYID_REF)
            ->get() ->toArray(); 
            */

            $objUOM=array();

            //$objItemUOMConv = DB::table('TBL_MST_ITEM_UOMCONV')->select('*')->get() ->toArray(); 

            $objItemUOMConv=array();

            $FormId         =   $this->form_id;
            $AlpsStatus =   $this->AlpsStatus();

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
            $checkCompany   =   $this->misrCheckIssueReQsQty();
           
            return view($this->view.$FormId.'edit',compact([
                'AlpsStatus',
                'FormId',
                'objRights',
                'objResponse',
                'objRPR',
                'objRPRDtl',
                'objPStage',
                'objPStageDtl',
                'objStore',
                'objStoreDtl',
                'objEmployee',
                'objEmployeeDtl',
                'objMAT',
                'objCount1',
                'objCount2',
                'objUDF',
                'objUdfData',
                'objCountUDF',
                'objItems',
                'objUdfData2',
                'objlastdt',
                'objUOM',
                'objItemUOMConv',
                'TabSetting',
                'checkCompany'
        ]));      


        }
     
    }

    public function update(Request $request){
        
        //dump($request->all());
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $StoreArr   =   array();
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $exp        =   explode(",",$ITEMROWID);

                foreach($exp as $val){
                    $keyid              =   explode("#",$val);
                    $batchid            =   $keyid[0];
                    $StoreArr[]         =   $batchid;
                }

                if(!empty($StoreArr)){
                    $StoreId    =   array_unique($StoreArr);
                    $STID_REF   =   implode(",",$StoreId);
                }
                else{
                    $STID_REF   =   NULL;
                }

                $req_data[$i] = [
                    'ITEMID_REF'       => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'   => $request['MAIN_UOMID_REF_'.$i],
                    'REQUEST_QTY'   => $request['PENDING_QTY_'.$i],
                    'STID'             =>  $STID_REF,
                    'ISSUED_QTY'       => $request['RECEIVED_QTY_MU_'.$i],
                    'SHORT_QTY'        => $request['SHORT_QTY_'.$i],
                    'REASON_SHORT_QTY' => $request['REASON_SHORT_QTY_'.$i],
                    'BATCH_QTY'        => $request['HiddenRowId_'.$i],
                    'REMARKS'          => $request['REMARKS_'.$i],
                    'PROID_REF'        => $request['PROID_REF_'.$i],
                    'RPRID_REF'        => $request['RPRID_REF_'.$i],
                    'SOID_REF'        => $request['SOID_REF_'.$i],
                    'SQID_REF'        => $request['SQID_REF_'.$i],
                    'SEID_REF'        => $request['SEID_REF_'.$i],
                    'MAINITEMID_REF'        => $request['FGI_REF_'.$i],
                    'MAINITEMUOMID_REF'     => $request['MAINITEM_UOMID_REF_'.$i],
                    'OLDITEMID_REF'        => $request['OLDITEM_ID_REF_'.$i],
                    //'ISSUED_QTY'       => $request['SE_QTY_'.$i],
                    //'MRS_QTY_BL'       => $request['PENDING_QTY_'.$i],
                    //'STOCK_INHAND'     => $request['STOCK_INHAND_'.$i],
                    //'ALT_UOMID_REF'    => $request['ALT_UOMID_REF_'.$i],
                ];
            }
        }

        
        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
            
        
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['UDF_'.$i])){
                $reqdata3[$i] = [
                    'UDFMISRID_REF'  => $request['UDF_'.$i],
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


        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $dataArr    =   array();
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];

                if($ITEMROWID !=""){
                    $exp        =   explode(",",$ITEMROWID);

                    foreach($exp as $val){
                        $keyid              =   explode("_",$val);
                        $batchid            =   $keyid[0];

                        $dataArr[$batchid]['RECEIVED_QTYM'] =   $keyid[1];
                        //$dataArr[$batchid]['LOT_NO']        =   $keyid[2];
                        //$dataArr[$batchid]['VENDOR_LOTNO']  =  $keyid[3];
                        $dataArr[$batchid]['STOCK_INHAND']  =  $keyid[2];
                    }
                }

                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        $ExpID              =   explode("#",$key);
                        $STID_REF           =   $ExpID[0];
                        $RPRID_REF          =   $ExpID[1];
                        $BATCH_CODE         =   $ExpID[2];
                        $ITEMID_REF         =   $ExpID[3];
                        $UOMID_REF          =   $ExpID[4];
                        $ALTUOM             =   $ExpID[5];
                        $PROID_REF          =   $ExpID[6];
                        $SOID_REF          =   $ExpID[7];
                        $SQID_REF          =   $ExpID[8];
                        $SEID_REF          =   $ExpID[9];
                        $FGI_REF           =   $ExpID[10];
                        $AluQty             =   $this->getAltUmQty($ALTUOM,$ITEMID_REF,$val['RECEIVED_QTYM']);
                       
                        $req_data33[$i][] = [
                           // 'MRSID_REF'         => $RGPID_REF,
                            'ITEMID_REF'        => $ITEMID_REF,
                            'UOMID_REF'         => $UOMID_REF,
                            'STID_REF'          => $STID_REF,
                            'BATCHID_REF'        => $BATCH_CODE,
                            'ISSUED_QTY'       => $val['RECEIVED_QTYM'],
                            'ALTUOMID_REF'     => $ALTUOM,
                            'PROID_REF'         => $PROID_REF,
                            'RPRID_REF'         => $RPRID_REF,
                            'SOID_REF'          => $SOID_REF,
                            'SQID_REF'          => $SQID_REF,
                            'SEID_REF'          => $SEID_REF,
                            'MAINITEMID_REF'    => $FGI_REF,
                            //'STOCK_INHAND'      => $val['STOCK_INHAND'], 
                            //'ISSUED_QTYA'       => $AluQty,
                        ];

                    }
                }
            }
        }

        $wrapped_links33["STORE"] = $req_data33; 
        $XMLSTORE = ArrayToXml::convert($wrapped_links33);

        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $MISRNO         = strtoupper(trim($request['MISRNO']));
        $MISRDT         = $request['MISRDT'];
        $RPRID_REF      = $request['RPRID_REF'];
        $FROM_STID_REF  = $request['STID_REF'];
        $STAGEID_REF    = $request['PSTAGEID_REF'];
        $ISSUED_BY      = $request['EMPID_REF'];
        

        $log_data = [ 
            $MISRNO, $MISRDT,$RPRID_REF ,$FROM_STID_REF,$STAGEID_REF,
            $ISSUED_BY, $CYID_REF, $BRID_REF, $FYID_REF, $VTID_REF,
            $XMLMAT, $XMLSTORE, $XMLUDF,  $USERID, Date('Y-m-d'),
            Date('h:i:s.u'), $ACTIONNAME, $IPADDRESS
        ];  

        //dump($log_data);
        $sp_result = DB::select('EXEC SP_MISR_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  

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

            $objResponse =  DB::table('TBL_TRN_PDMISR_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('MISRID','=',$id)
            ->first();

           
            $objlastdt          =   $this->getLastdt();
            $objRPR             =   $this->getRPRNo();
            $objRPRDtl          =   $this->getRPRNoDtl($objResponse->RPRID_REF);
            $objPStage          =   $this->getobjPStage();
            $objPStageDtl       =   $this->getobjPStageDtl($objResponse->STAGEID_REF);
            $objStore           =   $this->getobjStore();
            $objStoreDtl        =   $this->getobjStoreDtl($objResponse->FROM_STID_REF);
            $objEmployee        =   $this->getobjEmployee(); 
            $objEmployeeDtl     =   $this->getobjEmployeeDtl($objResponse->ISSUED_BY); 

            /*
            $objMAT = DB::table('TBL_TRN_PDMISR_MAT') 
            ->where('MISRID_REF','=',$id)
            ->select('TBL_TRN_PDMISR_MAT.*' )
            ->orderBy('MISR_MATID','ASC')
            ->get()->toArray();
            */


            $objMAT = DB::select("SELECT 
            T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,T2.ALT_UOMID_REF,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,
            CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
            CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE

            FROM TBL_TRN_PDMISR_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID
            WHERE T1.MISRID_REF='$id' ORDER BY T1.MISR_MATID ASC
            ");

            

            $objCount1 = count($objMAT);   
            
            $tempobjMAT= $objMAT;
            foreach ($tempobjMAT as $index => $value) {

                $objtempItem = DB::select('SELECT top 1 ALT_UOMID_REF FROM TBL_MST_ITEM  
                                    WHERE ITEMID=? AND MAIN_UOMID_REF=? AND CYID_REF=?', [ $value->ITEMID_REF, $value->UOMID_REF,$CYID_REF]);
                $objMAT[$index]->ALT_UOMID_REF = $objtempItem[0]->ALT_UOMID_REF;

                $objMainitem =   DB::select('SELECT top 1 ITEMID,ICODE,NAME FROM TBL_MST_ITEM  
                                    WHERE STATUS= ? AND ITEMID = ? ', ['A',$value->MAINITEMID_REF]);
                
                $objMAT[$index]->MAINITEMID_CODE = $objMainitem[0]->ICODE;
                $objMAT[$index]->MAINITEMID_NAME = $objMainitem[0]->NAME;

                $objMainitemUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                    WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND UOMID = ? 
                                    AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                    [$CYID_REF, $BRID_REF, $FYID_REF,$value->MAINITEMUOMID_REF, 'A' ]);
                

                $objMAT[$index]->MAINITEMID_UOMCODE = isset($objMainitemUOM[0]->UOMCODE)?$objMainitemUOM[0]->UOMCODE:NULL;
                $objMAT[$index]->MAINITEMID_UOMDESC = isset($objMainitemUOM[0]->DESCRIPTIONS)?$objMainitemUOM[0]->DESCRIPTIONS:NULL;


                $objBatch =  DB::SELECT("SELECT 
                            T1.BATCHID,T1.BATCH_CODE,T1.CURRENT_QTY AS TOTAL_STOCK,
                            T2.STID,T2.STCODE,T2.NAME AS STNAME
                            FROM TBL_MST_BATCH T1 
                            LEFT JOIN TBL_MST_STORE T2 ON T2.STID=T1.STID_REF
                            WHERE T1.STATUS='A' AND T1.CYID_REF ='$CYID_REF' AND T1.BRID_REF ='$BRID_REF'  AND T1.ITEMID_REF =$value->ITEMID_REF AND T1.UOMID_REF =$value->UOMID_REF
                            ");
                $totalstock = 0;
                foreach($objBatch as $bindex=>$brow){
                    $bstock  = !is_null($brow->TOTAL_STOCK) && trim($brow->TOTAL_STOCK)!="" ? $brow->TOTAL_STOCK : 0;
                    $totalstock = floatval($totalstock) + floatval($bstock);

                }
                $objMAT[$index]->TOTAL_STOCK = number_format($totalstock,3,".","") ;

                //-----------------------------------------------
                $BAL_RPR_REQ_QTY = 0;
                $objMAT[$index]->TOTAL_PENDING= $BAL_RPR_REQ_QTY;

                $ObjSavedQty =   DB::table('TBL_TRN_PDMISR_MAT')
                    ->where('TBL_TRN_PDMISR_MAT.RPRID_REF','=',$value->RPRID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.PROID_REF','=',$value->PROID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.SOID_REF','=',$value->SOID_REF)             
                    ->where('TBL_TRN_PDMISR_MAT.SQID_REF','=',$value->SQID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.SEID_REF','=',$value->SEID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.MAINITEMID_REF','=',$value->MAINITEMID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.OLDITEMID_REF','=',$value->OLDITEMID_REF)
                    ->where('TBL_TRN_PDMISR_HDR.STATUS','<>','C') 
                    ->leftJoin('TBL_TRN_PDMISR_HDR',   'TBL_TRN_PDMISR_HDR.MISRID','=',   'TBL_TRN_PDMISR_MAT.MISRID_REF')
                    ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDMISR_MAT.ISSUED_QTY),0) AS ISSUED_QTY'))                       
                    ->get();
                // dump($ObjSavedQty);
                $Total_ISSUED_QTY = $ObjSavedQty[0]->ISSUED_QTY;

                //get from TBL_TRN_PDRPR_MAT req qty
                $ObjRPRItem =   DB::table('TBL_TRN_PDRPR_MAT')
                    ->where('TBL_TRN_PDRPR_MAT.RPRID_REF','=',$value->RPRID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.PROID_REF','=',$value->PROID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.SOID_REF','=',$value->SOID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.SQID_REF','=',$value->SQID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.SEID_REF','=',$value->SEID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.MAINITEMID_REF','=',$value->MAINITEMID_REF)      
                    ->where('TBL_TRN_PDRPR_MAT.ITEMID_REF','=',$value->OLDITEMID_REF)      
                    ->select('TBL_TRN_PDRPR_MAT.*',)                    
                    ->first();
                $RPR_REQ_QTY =  isset($ObjRPRItem->REQ_QTY)? $ObjRPRItem->REQ_QTY : 0; 
                
                $BAL_RPR_REQ_QTY = number_format( floatVal($RPR_REQ_QTY) - floatval($Total_ISSUED_QTY), 3,".","" ) ;

                //ADD CONSUMED QTY      
                $ObjConsumedQty =   DB::table('TBL_TRN_PDMISR_MAT')                                    
                    ->where('TBL_TRN_PDMISR_MAT.MISRID_REF','=',$id)
                    ->where('TBL_TRN_PDMISR_MAT.RPRID_REF','=',$value->RPRID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.PROID_REF','=',$value->PROID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.SOID_REF','=',$value->SOID_REF)             
                    ->where('TBL_TRN_PDMISR_MAT.SQID_REF','=',$value->SQID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.SEID_REF','=',$value->SEID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.MAINITEMID_REF','=',$value->MAINITEMID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.OLDITEMID_REF','=',$value->OLDITEMID_REF)
                    ->where('TBL_TRN_PDMISR_HDR.STATUS','<>','C') 
                    ->leftJoin('TBL_TRN_PDMISR_HDR',   'TBL_TRN_PDMISR_HDR.MISRID','=',   'TBL_TRN_PDMISR_MAT.MISRID_REF')
                    ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDMISR_MAT.ISSUED_QTY),0) AS CONSUMED_ISSUED_QTY'))
                    ->get();

                $BAL_RPR_REQ_QTY = number_format( floatVal($BAL_RPR_REQ_QTY) + floatval($ObjConsumedQty[0]->CONSUMED_ISSUED_QTY), 3,".","" ) ;         

                $objMAT[$index]->TOTAL_PENDING= $BAL_RPR_REQ_QTY;               
                //--------------------------------------------------    
              
            }   
            
            // DUMP($objMAT);

            
            $objUDF = DB::table('TBL_TRN_PDMISR_UDF')                    
            ->where('MISRID_REF','=',$id)
            ->orderBy('MISR_UDFID','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF); 

            $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by UOMCODE ASC', [$CYID_REF, $BRID_REF,  'A' ]); 

            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_PMIS")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('UDFPMISID')->from('TBL_MST_UDFFOR_PMIS')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                     
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                     
                    

            $objUdfData = DB::table('TBL_MST_UDFFOR_PMIS')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_PMIS")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDFPMISID')->from('TBL_MST_UDFFOR_PMIS')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                         
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                           
          

            $objUdfData2 = DB::table('TBL_MST_UDFFOR_PMIS')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF2)
            ->get()->toArray(); 

            // dump($objUDF);
            //  dump($objUdfData2);

            /*
            $objItems = DB::table('TBL_MST_ITEM')->select('*')
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('FYID_REF','=',$FYID_REF)
            ->get() ->toArray(); 
            */

            $objItems=array();
            /*
            $objUOM = DB::table('TBL_MST_UOM')->select('*')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('FYID_REF','=',$FYID_REF)
            ->get() ->toArray(); 
            */

            $objUOM=array();

            //$objItemUOMConv = DB::table('TBL_MST_ITEM_UOMCONV')->select('*')->get() ->toArray(); 

            $objItemUOMConv=array();

            $FormId         =   $this->form_id;
            $AlpsStatus =   $this->AlpsStatus();

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
            $checkCompany   =   $this->misrCheckIssueReQsQty();
           
            return view($this->view.$FormId.'view',compact([
                'AlpsStatus',
                'FormId',
                'objRights',
                'objResponse',
                'objRPR',
                'objRPRDtl',
                'objPStage',
                'objPStageDtl',
                'objStore',
                'objStoreDtl',
                'objEmployee',
                'objEmployeeDtl',
                'objMAT',
                'objCount1',
                'objCount2',
                'objUDF',
                'objUdfData',
                'objCountUDF',
                'objItems',
                'objUdfData2',
                'objlastdt',
                'objUOM',
                'objItemUOMConv',
                'TabSetting',
                'checkCompany'
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
        
        
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $StoreArr   =   array();
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $exp        =   explode(",",$ITEMROWID);

                foreach($exp as $val){
                    $keyid              =   explode("#",$val);
                    $batchid            =   $keyid[0];
                    $StoreArr[]         =   $batchid;
                }

                if(!empty($StoreArr)){
                    $StoreId    =   array_unique($StoreArr);
                    $STID_REF   =   implode(",",$StoreId);
                }
                else{
                    $STID_REF   =   NULL;
                }

                $req_data[$i] = [
                    'ITEMID_REF'       => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'   => $request['MAIN_UOMID_REF_'.$i],
                    'REQUEST_QTY'   => $request['PENDING_QTY_'.$i],
                    'STID'             =>  $STID_REF,
                    'ISSUED_QTY'       => $request['RECEIVED_QTY_MU_'.$i],
                    'SHORT_QTY'        => $request['SHORT_QTY_'.$i],
                    'REASON_SHORT_QTY' => $request['REASON_SHORT_QTY_'.$i],
                    'BATCH_QTY'        => $request['HiddenRowId_'.$i],
                    'REMARKS'          => $request['REMARKS_'.$i],
                    'PROID_REF'        => $request['PROID_REF_'.$i],
                    'RPRID_REF'        => $request['RPRID_REF_'.$i],
                    'SOID_REF'        => $request['SOID_REF_'.$i],
                    'SQID_REF'        => $request['SQID_REF_'.$i],
                    'SEID_REF'        => $request['SEID_REF_'.$i],
                    'MAINITEMID_REF'        => $request['FGI_REF_'.$i],
                    'MAINITEMUOMID_REF'     => $request['MAINITEM_UOMID_REF_'.$i],
                    'OLDITEMID_REF'        => $request['OLDITEM_ID_REF_'.$i],
                    //'ISSUED_QTY'       => $request['SE_QTY_'.$i],
                    //'MRS_QTY_BL'       => $request['PENDING_QTY_'.$i],
                    //'STOCK_INHAND'     => $request['STOCK_INHAND_'.$i],
                    //'ALT_UOMID_REF'    => $request['ALT_UOMID_REF_'.$i],
                ];
            }
        }

        
        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
            
        
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['UDF_'.$i])){
                $reqdata3[$i] = [
                    'UDFMISRID_REF'  => $request['UDF_'.$i],
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


        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $dataArr    =   array();
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];

                if($ITEMROWID !=""){
                    $exp        =   explode(",",$ITEMROWID);

                    foreach($exp as $val){
                        $keyid              =   explode("_",$val);
                        $batchid            =   $keyid[0];

                        $dataArr[$batchid]['RECEIVED_QTYM'] =   $keyid[1];
                        //$dataArr[$batchid]['LOT_NO']        =   $keyid[2];
                        //$dataArr[$batchid]['VENDOR_LOTNO']  =  $keyid[3];
                        $dataArr[$batchid]['STOCK_INHAND']  =  $keyid[2];
                    }
                }

                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        $ExpID              =   explode("#",$key);
                        $STID_REF           =   $ExpID[0];
                        $RPRID_REF          =   $ExpID[1];
                        $BATCH_CODE         =   $ExpID[2];
                        $ITEMID_REF         =   $ExpID[3];
                        $UOMID_REF          =   $ExpID[4];
                        $ALTUOM             =   $ExpID[5];
                        $PROID_REF          =   $ExpID[6];
                        $SOID_REF          =   $ExpID[7];
                        $SQID_REF          =   $ExpID[8];
                        $SEID_REF          =   $ExpID[9];
                        $FGI_REF           =   $ExpID[10];
                        $AluQty             =   $this->getAltUmQty($ALTUOM,$ITEMID_REF,$val['RECEIVED_QTYM']);
                       
                        $req_data33[$i][] = [
                           // 'MRSID_REF'         => $RGPID_REF,
                            'ITEMID_REF'        => $ITEMID_REF,
                            'UOMID_REF'         => $UOMID_REF,
                            'STID_REF'          => $STID_REF,
                            'BATCHID_REF'        => $BATCH_CODE,
                            'ISSUED_QTY'       => $val['RECEIVED_QTYM'],
                            'ALTUOMID_REF'     => $ALTUOM,
                            'PROID_REF'         => $PROID_REF,
                            'RPRID_REF'         => $RPRID_REF,
                            'SOID_REF'          => $SOID_REF,
                            'SQID_REF'          => $SQID_REF,
                            'SEID_REF'          => $SEID_REF,
                            'MAINITEMID_REF'    => $FGI_REF,
                            //'STOCK_INHAND'      => $val['STOCK_INHAND'], 
                            //'ISSUED_QTYA'       => $AluQty,
                        ];

                    }
                }
            }
        }

        $wrapped_links33["STORE"] = $req_data33; 
        $XMLSTORE = ArrayToXml::convert($wrapped_links33);


        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $MISRNO         = strtoupper(trim($request['MISRNO']));
        $MISRDT         = $request['MISRDT'];
        $RPRID_REF      = $request['RPRID_REF'];
        $FROM_STID_REF  = $request['STID_REF'];
        $STAGEID_REF    = $request['PSTAGEID_REF'];
        $ISSUED_BY      = $request['EMPID_REF'];
        
       

        $log_data = [ 
            $MISRNO, $MISRDT,$RPRID_REF ,$FROM_STID_REF,$STAGEID_REF,
            $ISSUED_BY, $CYID_REF, $BRID_REF, $FYID_REF, $VTID_REF,
            $XMLMAT, $XMLSTORE, $XMLUDF,  $USERID, Date('Y-m-d'),
            Date('h:i:s.u'), $ACTIONNAME, $IPADDRESS
        ];  


        //dump($log_data);
        $sp_result = DB::select('EXEC SP_MISR_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => 'Record successfully Approved.']);

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
        $TABLE      =   "TBL_TRN_PDMISR_HDR";
        $FIELD      =   "MISRID";
        $ACTIONNAME     = $Approvallevel;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
            
        $log_data = [ 
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];

        //dump($log_data);

        $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_MISR ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);  
        
        //dd($sp_result);
        
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
        $TABLE      =   "TBL_TRN_PDMISR_HDR";
        $FIELD      =   "MISRID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
         'NT'  => 'TBL_TRN_PDMISR_MAT',
        ];
        $req_data[1]=[
        'NT'  => 'TBL_TRN_PDMISR_STORE',
        ];
        $req_data[2]=[
        'NT'  => 'TBL_TRN_PDMISR_UDF',
        ];
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_MISR  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_PDMISR_HDR')->where('MISRID','=',$id)->first();

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
        
		$image_path         =   "docs/company".$CYID_REF."/MaterialIssueSlipRPR";     
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

        $MISRNO  =   trim($request['MISRNO']);
        $objLabel = DB::table('TBL_TRN_PDMISR_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('MISRNO','=',$MISRNO)
        ->select('MISRID')->first();

        if($objLabel){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }        
        exit();
    }

    public function getStoreList(){
        return  DB::table('TBL_MST_STORE')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('STID','STCODE','NAME')
            ->get();
    }

    public function getStoreName($id){
        return  DB::table('TBL_MST_STORE')
            ->where('STID','=',$id)
            ->select('STID','STCODE','NAME')
            ->first();
    }

    public function getDepartmentList(){
        return  DB::table('TBL_MST_DEPARTMENT')
            ->where('STATUS','=','A')
            //->whereRaw("NAME  NOT LIKE '%producation%'")
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('DEPID','DCODE','NAME')
            ->get();
    }

    public function getDepartmentName($id){
        return  DB::table('TBL_MST_DEPARTMENT')
            ->where('DEPID','=',$id)
            ->select('DEPID','DCODE','NAME')
            ->first();
    }

    public function getRPRNo(){
        
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');

        //-----------------------------
        $MISRID= isset($request['MISRID'])?$request['MISRID']:0;  //edit case

        $ObjData    =   DB::select("SELECT 
                        T1.RPRID,T1.RPR_NO,T1.RPR_DT,T2.PRO_TITLE
                        FROM TBL_TRN_PDRPR_HDR T1
                        LEFT JOIN TBL_TRN_PDPRO_HDR T2 ON T1.PROID_REF=T2.PROID
                        WHERE  T1.CYID_REF ='$CYID_REF' AND T1.BRID_REF ='$BRID_REF' AND T1.VTID_REF ='363' AND T1.STATUS ='A'");
            
            $objNewRPR= [];
            if(!empty($ObjData)){
                
                foreach ($ObjData as $index=>$dataRow){

                    $ObjRPRItems =  DB::select("select * from TBL_TRN_PDRPR_MAT where RPRID_REF=$dataRow->RPRID");
                    //dump($ObjRPRItems);

                    $addRecord = [];
                    foreach ($ObjRPRItems as $index2=>$dataRow2){
                        
                        $ObjSavedQty =   DB::table('TBL_TRN_PDMISR_MAT')
                            ->where('TBL_TRN_PDMISR_MAT.RPRID_REF','=',$dataRow2->RPRID_REF)
                            ->where('TBL_TRN_PDMISR_MAT.PROID_REF','=',$dataRow2->PROID_REF)
                            ->where('TBL_TRN_PDMISR_MAT.SOID_REF','=',$dataRow2->SOID_REF)             
                            ->where('TBL_TRN_PDMISR_MAT.SQID_REF','=',$dataRow2->SQID_REF)
                            ->where('TBL_TRN_PDMISR_MAT.SEID_REF','=',$dataRow2->SEID_REF)
                            ->where('TBL_TRN_PDMISR_MAT.MAINITEMID_REF','=',$dataRow2->MAINITEMID_REF)
                            ->where('TBL_TRN_PDMISR_MAT.OLDITEMID_REF','=',$dataRow2->ITEMID_REF)
                            ->where('TBL_TRN_PDMISR_HDR.STATUS','<>','C') 
                            ->leftJoin('TBL_TRN_PDMISR_HDR',   'TBL_TRN_PDMISR_HDR.MISRID','=',   'TBL_TRN_PDMISR_MAT.MISRID_REF')
                            ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDMISR_MAT.ISSUED_QTY),0) AS ISSUED_QTY'))                       
                            ->get();

                        $Total_ISSUED_QTY = $ObjSavedQty[0]->ISSUED_QTY;
                                  
                        $consQty = 0;
                        if($MISRID>0){
                            $ObjConsumedQty =   DB::table('TBL_TRN_PDMISR_MAT')                                    
                            ->where('TBL_TRN_PDMISR_MAT.MISRID_REF','=',$MISRID)
                            ->where('TBL_TRN_PDMISR_MAT.RPRID_REF','=',$dataRow->RPRID_REF)
                            ->where('TBL_TRN_PDMISR_MAT.PROID_REF','=',$dataRow->PROID_REF)
                            ->where('TBL_TRN_PDMISR_MAT.SOID_REF','=',$dataRow->SOID_REF)             
                            ->where('TBL_TRN_PDMISR_MAT.SQID_REF','=',$dataRow->SQID_REF)
                            ->where('TBL_TRN_PDMISR_MAT.SEID_REF','=',$dataRow->SEID_REF)
                            ->where('TBL_TRN_PDMISR_MAT.MAINITEMID_REF','=',$dataRow->MAINITEMID_REF)
                            ->where('TBL_TRN_PDMISR_MAT.OLDITEMID_REF','=',$dataRow->ITEMID_REF)
                            ->where('TBL_TRN_PDMISR_HDR.STATUS','<>','C') 
                            ->leftJoin('TBL_TRN_PDMISR_HDR',   'TBL_TRN_PDMISR_HDR.MISRID','=',   'TBL_TRN_PDMISR_MAT.MISRID_REF')
                            ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDMISR_MAT.ISSUED_QTY),0) AS CONSUMED_ISSUED_QTY'))
                            ->get();
                            $consQty = $ObjConsumedQty[0]->CONSUMED_ISSUED_QTY;                            
                        }
                                                    
                            //MY CONSUMED QTY - edit case
                            $TOTAL_QTY = number_format( floatval($Total_ISSUED_QTY) - floatval($consQty), 3,".","" ) ;
   
                            if(floatval($dataRow2->REQ_QTY)>floatval($TOTAL_QTY)){
                                $addRecord[]=true;
                            }else
                            {
                                $addRecord[]=false;
                            }    
                    }
                        //dump($addRecord);
                        if(in_array('true',$addRecord)){
                            $objNewRPR[$index]=$dataRow;
                        }                
                }

                return $objNewRPR;

            }else{

               return $objNewRPR;
            
            }


        //-----------------------------
    }
    
    public function getRPRNoDtl($id){
        return  DB::table('TBL_TRN_PDRPR_HDR')
            ->where('RPRID','=',$id)
            ->select('RPRID','RPR_NO','RPR_DT')
            ->first();
    }

    public function getobjPStage(){

        $CYID_REF = Auth::user()->CYID_REF;
                      
        return  DB::select('SELECT PSTAGEID,PSTAGE_CODE,DESCRIPTIONS FROM TBL_MST_PRODUCTIONSTAGES  
        WHERE  CYID_REF = ?  AND STATUS = ?', 
        [$CYID_REF, 'A' ]);
    }

    public function getobjPStageDtl($id){
        return  DB::table('TBL_MST_PRODUCTIONSTAGES')
            ->where('PSTAGEID','=',$id)
            ->select('PSTAGEID','PSTAGE_CODE','DESCRIPTIONS')
            ->first();
    }

    public function getobjStore(){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
                      
        return  DB::select('SELECT STID,STCODE,NAME FROM TBL_MST_STORE  
        WHERE  CYID_REF = ? AND BRID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF, 'A' ]);
    }

    public function getobjStoreDtl($id){
        return  DB::table('TBL_MST_STORE')
            ->where('STID','=',$id)
            ->select('STID','STCODE','NAME')
            ->first();
    }

    public function getobjEmployee(){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
                      
        return  DB::select('SELECT EMPID,EMPCODE,FNAME,LNAME FROM TBL_MST_EMPLOYEE  
        WHERE  CYID_REF = ? AND BRID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF, 'A' ]);
    }

    public function getobjEmployeeDtl($id){
        return  DB::table('TBL_MST_EMPLOYEE')
            ->where('EMPID','=',$id)
            ->select('EMPID','EMPCODE','FNAME','LNAME')
            ->first();
    }

    public function getLastdt(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        return  DB::select('SELECT MAX(MISRDT) MISRDT FROM TBL_TRN_PDMISR_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
    }

    //Finished good item / main item
    public function getFGIDetails(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');        
        $RPRID      =   $request['RPRID_REF'];
        $MISRID      =   isset($request['MISRID'])?$request['MISRID']:0;  //edit case

        $AlpsStatus =   $this->AlpsStatus();
        
       

        $ObjItem =  DB::select("SELECT DISTINCT T1.RPRID_REF, T1.MAINITEMID_REF,T1.PROID_REF,T1.SOID_REF,T1.SQID_REF,T1.SEID_REF,
        T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI,T2.MAIN_UOMID_REF
        FROM TBL_TRN_PDRPR_MAT T1
        LEFT JOIN TBL_MST_ITEM T2 ON T1.MAINITEMID_REF=T2.ITEMID
        WHERE T1.MAINITEMID_REF IS NOT NULL AND  T1.RPRID_REF='$RPRID'");

        //DUMP( $ObjItem);

        if(!empty($ObjItem)){

            foreach ($ObjItem as $index=>$dataRow){

                //SUB items
                $ObjRPRItem =   DB::table('TBL_TRN_PDRPR_MAT')
                    ->where('TBL_TRN_PDRPR_MAT.RPRID_REF','=',$RPRID)
                    ->where('TBL_TRN_PDRPR_MAT.SOID_REF','=',$dataRow->SOID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.SQID_REF','=',$dataRow->SQID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.SEID_REF','=',$dataRow->SEID_REF)
                    ->where('TBL_TRN_PDRPR_MAT.MAINITEMID_REF','=',$dataRow->MAINITEMID_REF)      
                    ->leftJoin('TBL_MST_ITEM',   'TBL_MST_ITEM.ITEMID','=',   'TBL_TRN_PDRPR_MAT.MAINITEMID_REF')   
                    ->select('TBL_TRN_PDRPR_MAT.*','TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ITEMGID_REF','TBL_MST_ITEM.ICID_REF','TBL_MST_ITEM.ITEM_SPECI','TBL_MST_ITEM.MAIN_UOMID_REF')
                    ->orderBy('TBL_TRN_PDRPR_MAT.RPR_MATID', 'DESC')
                    ->get();
                //DD($ObjRPRItem);
                $addRecord = [];    
                foreach($ObjRPRItem as $RPRIndex=>$RPRRow){                  

                    $ObjSavedQty =   DB::table('TBL_TRN_PDMISR_MAT')
                        ->where('TBL_TRN_PDMISR_MAT.RPRID_REF','=',$RPRRow->RPRID_REF)
                        ->where('TBL_TRN_PDMISR_MAT.PROID_REF','=',$RPRRow->PROID_REF)
                        ->where('TBL_TRN_PDMISR_MAT.SOID_REF','=',$RPRRow->SOID_REF)             
                        ->where('TBL_TRN_PDMISR_MAT.SQID_REF','=',$RPRRow->SQID_REF)
                        ->where('TBL_TRN_PDMISR_MAT.SEID_REF','=',$RPRRow->SEID_REF)
                        ->where('TBL_TRN_PDMISR_MAT.MAINITEMID_REF','=',$RPRRow->MAINITEMID_REF)
                        ->where('TBL_TRN_PDMISR_MAT.OLDITEMID_REF','=',$RPRRow->ITEMID_REF)
                        ->where('TBL_TRN_PDMISR_HDR.STATUS','<>','C') 
                        ->leftJoin('TBL_TRN_PDMISR_HDR',   'TBL_TRN_PDMISR_HDR.MISRID','=',   'TBL_TRN_PDMISR_MAT.MISRID_REF')
                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDMISR_MAT.ISSUED_QTY),0) AS ISSUED_QTY'))                       
                        ->get();

                       // dump($ObjSavedQty);
                       $RPR_REQ_QTY =  isset($RPRRow->REQ_QTY)? $RPRRow->REQ_QTY : 0;   
                       $Total_ISSUED_QTY = $ObjSavedQty[0]->ISSUED_QTY;

                       
                        //CONSUMED QTY
                    $ObjConsumedQty =   DB::table('TBL_TRN_PDMISR_MAT')                                    
                        ->where('TBL_TRN_PDMISR_MAT.MISRID_REF','=',$MISRID)
                        ->where('TBL_TRN_PDMISR_MAT.RPRID_REF','=',$RPRRow->RPRID_REF)
                        ->where('TBL_TRN_PDMISR_MAT.PROID_REF','=',$RPRRow->PROID_REF)
                        ->where('TBL_TRN_PDMISR_MAT.SOID_REF','=',$RPRRow->SOID_REF)             
                        ->where('TBL_TRN_PDMISR_MAT.SQID_REF','=',$RPRRow->SQID_REF)
                        ->where('TBL_TRN_PDMISR_MAT.SEID_REF','=',$RPRRow->SEID_REF)
                          ->where('TBL_TRN_PDMISR_MAT.MAINITEMID_REF','=',$RPRRow->MAINITEMID_REF)
                        ->where('TBL_TRN_PDMISR_MAT.OLDITEMID_REF','=',$RPRRow->ITEMID_REF)
                        ->where('TBL_TRN_PDMISR_HDR.STATUS','<>','C') 
                        ->leftJoin('TBL_TRN_PDMISR_HDR',   'TBL_TRN_PDMISR_HDR.MISRID','=',   'TBL_TRN_PDMISR_MAT.MISRID_REF')
                        ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDMISR_MAT.ISSUED_QTY),0) AS CONSUMED_ISSUED_QTY'))
                        ->get();
                        //dump($ObjConsumedQty);                    
                        
                        //MY CONSUMED QTY - edit case
                        $Total_ISSUED_QTY = number_format( floatVal($Total_ISSUED_QTY) - floatval($ObjConsumedQty[0]->CONSUMED_ISSUED_QTY), 3,".","" ) ;

                        if(floatval($RPRRow->REQ_QTY)>floatval($Total_ISSUED_QTY)){
                            $addRecord[]=true;
                        }else
                        {
                            $addRecord[]=false;
                        }  

                }
               // dump($addRecord);
                if(in_array('true',$addRecord)){
                   
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
                        <td style="width:10%;text-align:center;"><input type="checkbox" id="chkfgiId'.$index.'"  value="'.$dataRow->ITEMID.'" class="fgijs-selectall1"  ></td>
                        <td style="width:10%;">'.$dataRow->ICODE.'</td>
                        <td style="width:10%;">'.$dataRow->NAME.'</td>
                        <td style="width:15%;display:none;">'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>
                        <td style="width:10%;display:none;" >'.$FROMQTY.'</td>
                        <td style="width:10%;">'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>
                        <td style="width:10%;">'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                        <td style="width:10%;">'.$BusinessUnit.'</td>
                        <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                        <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                        <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                        <td style="width:10%;">Authorized</td>
                        <td hidden><input type="text" id="addinfoitem_'.$index.'"  data-desc101="'.$ALPS_PART_NO.'" data-desc102="'.$CUSTOMER_PART_NO.'" data-desc103="'.$OEM_PART_NO.'" ></td>
                        <td hidden>
                            <input type="text" id="txtitem_'.$index.'" 
                                data-desc1="'.$dataRow->ITEMID.'" 
                                data-desc2="'.$dataRow->ICODE.'" 
                                data-desc3="'.$dataRow->NAME.'" 
                                data-desc4="'.$dataRow->MAIN_UOMID_REF.'" 
                                data-desc5="'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'" 
                                data-desc6="'.$FROMQTY.'" 
                                data-desc7="'.$item_unique_row_id.'" 
                                data-desc8="'.$dataRow->SQID_REF.'"
                                data-desc9="'.$dataRow->SEID_REF.'"
                                data-desc10=""
                                data-desc11="'.$dataRow->SOID_REF.'"
                                data-proid="'.$dataRow->PROID_REF.'"
                                data-rprid="'.$dataRow->RPRID_REF.'"
                            />
                        </td>                        
                    </tr>';
                    echo $row;  
                }else{
                    echo "No Record found.";
                }   // IN ARRAY                    
                  
            }         
        }           
        else{
            echo '<tr><td> Record not found.</td></tr>';
        }
        exit();
    }
    
    public function getAllItem(Request $request){
        
        $CYID_REF   =   Auth::user()->CYID_REF;
        $Status = 'A';
        $item_array =   $request['item_array'];

        $AlpsStatus =   $this->AlpsStatus();

       // dump($item_array);
        $material_array=array();
        $row_array=array();
        $mat_row_array=array();
        foreach($item_array as $key=>$val){         

            $exp            =   explode("_",$val);
            $PROID_REF      =   $exp[0];
            $SOID_REF       =   $exp[1];
            $SEID_REF       =   $exp[2];
            $SQID_REF       =   $exp[3];
            $MAINITEM_ID    =   $exp[4];
            $MAINITEM_UOMID =   $exp[5];          
            $RPRID_REF      =   $exp[6];          

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

            $MAINUOMCODE = $ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS;            

            $material_array[]=array(
                'PROID_REF'=>$PROID_REF,
                'SOID_REF'=>$SOID_REF,
                'SEID_REF'=>$SEID_REF, 
                'SQID_REF'=>$SQID_REF, 
                'MAINITEM_ID'=>$MAINITEM_ID, 
                'MAINITEM_CODE'=>$MAINITEM_CODE, 
                'MAINITEM_NAME'=>$MAINITEM_NAME, 
                'MAINITEM_UOMID'=>$MAINITEM_UOMID,            
                'MAINITEM_UOMCODE'=>$MAINUOMCODE,             
                'RPRID_REF'=>$RPRID_REF             
            );
            
        }

        //dump($material_array);
        $row_array= $material_array;
        $tr ='<tr class="participantRow">
                <td hidden=""><input type="hidden" id="0"> </td>

                <td><input type="text" name="popupFGI_0" id="popupFGI_0" class="form-control" autocomplete="off" readonly="" style="width:100px;"></td>
                <td hidden><input type="text" name="FGI_REF_0" id="FGI_REF_0" class="form-control" autocomplete="off"></td>
                <td><input type="text" name="FGIName_0" id="FGIName_0" class="form-control" autocomplete="off" readonly="" style="width:200px;"></td>
                <td  hidden ><input type="text" name="popupMAINITEMUOM_0" id="popupMAINITEMUOM_0" class="form-control" autocomplete="off" readonly="" style="width:100px;"></td>
                <td  hidden><input type="text" name="MAINITEM_UOMID_REF_0" id="MAINITEM_UOMID_REF_0" readonly="" class="form-control" autocomplete="off"></td>


                <td hidden><input type="text" name="SOID_REF_0" id="SOID_REF_0" class="form-control" autocomplete="off"></td>
                <td hidden><input type="text" name="SQID_REF_0" id="SQID_REF_0" class="form-control" autocomplete="off"></td>
                <td hidden><input type="text" name="SEID_REF_0" id="SEID_REF_0" class="form-control" autocomplete="off"></td>
                <td hidden><input type="text" name="RPRID_REF_0" id="RPRID_REF_0" class="form-control" autocomplete="off"></td>
                <td hidden><input type="text" name="PROID_REF_0" id="PROID_REF_0" class="form-control" autocomplete="off"></td>
            
                <td><input type="text" name="popupITEMID_0" id="popupITEMID_0" class="form-control" autocomplete="off" readonly="" style="width:100px;"></td>
                <td hidden><input type="text" name="ITEMID_REF_0" id="ITEMID_REF_0" class="form-control" autocomplete="off"></td>
                <td hidden><input type="text" name="OLDITEM_ID_REF_0" id="OLDITEM_ID_REF_0" class="form-control" autocomplete="off"></td>
                <td hidden><input type="text" name="RPR_REQ_QTY_0" id="RPR_REQ_QTY_0" class="form-control" autocomplete="off"></td>
            
                <td><input type="text" name="ItemName_0" id="ItemName_0" class="form-control" autocomplete="off" readonly="" style="width:200px;"></td>

                <td '.$AlpsStatus['hidden'].'><input type="text" name="Alpspartno_0" id="Alpspartno_0" class="form-control"  autocomplete="off"  readonly/></td>
                <td '.$AlpsStatus['hidden'].'><input type="text" name="Custpartno_0" id="Custpartno_0" class="form-control"  autocomplete="off"  readonly/></td>
                <td '.$AlpsStatus['hidden'].'><input type="text" name="OEMpartno_0"  id="OEMpartno_0" class="form-control"  autocomplete="off"   readonly/></td>

        
                <td><input type="text" name="popupMUOM_0" id="popupMUOM_0" class="form-control" autocomplete="off" readonly="" style="width:100px;"></td>
                <td  hidden><input type="text" name="MAIN_UOMID_REF_0" id="MAIN_UOMID_REF_0" class="form-control" autocomplete="off"></td>
        
                

                <td align="center"><a class="btn checkstore" id="0"><i class="fa fa-clone"></i></a></td>
                <td  hidden><input type="text" name="TotalHiddenQty_0" id="TotalHiddenQty_0"></td>
                <td hidden><input type="text" name="HiddenRowId_0" id="HiddenRowId_0"></td>
                
                <td><input type="text" name="PENDING_QTY_0" id="PENDING_QTY_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13" autocomplete="off" readonly=""></td>
                
                <td><input type="text" name="STOCK_INHAND_0" id="STOCK_INHAND_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" readonly="" autocomplete="off"></td>
                
                <td><input type="text" name="RECEIVED_QTY_MU_0" id="RECEIVED_QTY_MU_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13" autocomplete="off" readonly=""></td>
                
                <td><input type="text" name="popupALTUOM_0" id="popupALTUOM_0" class="form-control" autocomplete="off" readonly=""></td>
                <td hidden><input type="hidden" name="ALT_UOMID_REF_0" id="ALT_UOMID_REF_0" class="form-control" autocomplete="off"></td>
                
                <td hidden><input type="text" name="RECEIVED_QTY_AU_0" id="RECEIVED_QTY_AU_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" readonly="" maxlength="13" autocomplete="off"></td>
                
                <td hidden><input type="text" name="SHORT_QTY_0" id="SHORT_QTY_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13" autocomplete="off" readonly=""></td>
                
                <td><input type="text" name="REASON_SHORT_QTY_0" id="REASON_SHORT_QTY_0" class="form-control" autocomplete="off"></td>
                
                
                <td><input type="text" name="REMARKS_0" id="REMARKS_0" class="form-control" maxlength="200" autocomplete="off" style="width:200px;"></td>
                
                <td align="center">
                <button class="btn add material" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash"></i></button>
                </td>
            </tr>';

        if(!empty($material_array)){
            foreach($row_array as $mindex=>$mrow){
            //----------
           // $RPRID      =   isset($request['RPRID'])?$request['RPRID']:0;   
            $MISRID= isset($request['MISRID'])?$request['MISRID']:0;       
           
            $ObjItem =   DB::table('TBL_TRN_PDRPR_MAT')
                        ->where('TBL_TRN_PDRPR_MAT.RPRID_REF','=',$mrow['RPRID_REF'])
                        ->where('TBL_TRN_PDRPR_MAT.SOID_REF','=',trim($mrow['SOID_REF'])==""?NULL:$mrow['SOID_REF'])
                        ->where('TBL_TRN_PDRPR_MAT.SQID_REF','=',trim($mrow['SQID_REF'])==""?NULL:$mrow['SQID_REF'])
                        ->where('TBL_TRN_PDRPR_MAT.SEID_REF','=',trim($mrow['SEID_REF'])==""?NULL:$mrow['SEID_REF'])
                        ->where('TBL_TRN_PDRPR_MAT.MAINITEMID_REF','=',$mrow['MAINITEM_ID'])      
                        ->leftJoin('TBL_MST_ITEM',   'TBL_MST_ITEM.ITEMID','=',   'TBL_TRN_PDRPR_MAT.ITEMID_REF')   
                        ->select('TBL_TRN_PDRPR_MAT.*','TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ITEMGID_REF','TBL_MST_ITEM.ICID_REF','TBL_MST_ITEM.ITEM_SPECI','TBL_MST_ITEM.MAIN_UOMID_REF','TBL_MST_ITEM.ALT_UOMID_REF','TBL_MST_ITEM.ALPS_PART_NO','TBL_MST_ITEM.CUSTOMER_PART_NO','TBL_MST_ITEM.OEM_PART_NO')
                        ->orderBy('TBL_TRN_PDRPR_MAT.RPR_MATID', 'DESC')
                        ->get();
            // echo "--item obj--";
           // dump($ObjItem );
            $tempObjItem = $ObjItem;            
            foreach($tempObjItem as $tmpindex=>$tmpRow){

                //-----------------------------
                $ObjSavedQty =   DB::table('TBL_TRN_PDMISR_MAT')
                    ->where('TBL_TRN_PDMISR_MAT.RPRID_REF','=',$tmpRow->RPRID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.PROID_REF','=',$tmpRow->PROID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.SOID_REF','=',$tmpRow->SOID_REF)             
                    ->where('TBL_TRN_PDMISR_MAT.SQID_REF','=',$tmpRow->SQID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.SEID_REF','=',$tmpRow->SEID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.MAINITEMID_REF','=',$tmpRow->MAINITEMID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.OLDITEMID_REF','=',$tmpRow->ITEMID_REF)
                    ->where('TBL_TRN_PDMISR_HDR.STATUS','<>','C') 
                    ->leftJoin('TBL_TRN_PDMISR_HDR',   'TBL_TRN_PDMISR_HDR.MISRID','=',   'TBL_TRN_PDMISR_MAT.MISRID_REF')
                    ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDMISR_MAT.ISSUED_QTY),0) AS ISSUED_QTY'))                       
                    ->get();

                // dump($ObjSavedQty);
                $Total_ISSUED_QTY = $ObjSavedQty[0]->ISSUED_QTY;
                
                $RPR_REQ_QTY =  isset($tmpRow->REQ_QTY)? $tmpRow->REQ_QTY : 0;   
                $BAL_RPR_REQ_QTY = number_format( floatVal($RPR_REQ_QTY) - floatval($Total_ISSUED_QTY), 3,".","" ) ;

                //CONSUMED QTY -- edit case
                $consQty = 0;
                if($MISRID>0){
                    $ObjConsumedQty =   DB::table('TBL_TRN_PDMISR_MAT')                                    
                    ->where('TBL_TRN_PDMISR_MAT.MISRID_REF','=',$MISRID)
                    ->where('TBL_TRN_PDMISR_MAT.RPRID_REF','=',$tmpRow->RPRID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.PROID_REF','=',$tmpRow->PROID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.SOID_REF','=',$tmpRow->SOID_REF)             
                    ->where('TBL_TRN_PDMISR_MAT.SQID_REF','=',$tmpRow->SQID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.SEID_REF','=',$tmpRow->SEID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.MAINITEMID_REF','=',$tmpRow->MAINITEMID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.OLDITEMID_REF','=',$tmpRow->ITEMID_REF)
                    ->where('TBL_TRN_PDMISR_HDR.STATUS','<>','C') 
                    ->leftJoin('TBL_TRN_PDMISR_HDR',   'TBL_TRN_PDMISR_HDR.MISRID','=',   'TBL_TRN_PDMISR_MAT.MISRID_REF')
                    ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDMISR_MAT.ISSUED_QTY),0) AS CONSUMED_ISSUED_QTY'))
                    ->get();
                    $consQty = $ObjConsumedQty[0]->CONSUMED_ISSUED_QTY;
                    $BAL_RPR_REQ_QTY = number_format( floatVal($BAL_RPR_REQ_QTY) + floatval($consQty), 3,".","" ) ;
                }
                
                    $ObjItem[$tmpindex]->BAL_RPR_REQ_QTY = $BAL_RPR_REQ_QTY;
                    $ObjItem[$tmpindex]->ITEMQTY = number_format( floatval($consQty), 3,".","" ) ;

                    $ObjSubItemUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS 
                                        FROM TBL_MST_UOM  
                                        WHERE  CYID_REF = ?  AND UOMID = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                        [$CYID_REF, $tmpRow->MAIN_UOMID_REF, 'A' ]);           

                    $ObjItem[$tmpindex]->ITEM_UOMID = $tmpRow->MAIN_UOMID_REF;
                    $ObjItem[$tmpindex]->ITEM_UOMCODE = $ObjSubItemUOM[0]->UOMCODE.'-'.$ObjSubItemUOM[0]->DESCRIPTIONS; 
                    
                ///----
                    $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                            WHERE  CYID_REF = ?  AND UOMID = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                            [$CYID_REF, $tmpRow->ALT_UOMID_REF, $Status ]);
                    
                    $ObjAltQTY =  DB::select('SELECT TOP 1  TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV  
                                WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                                [$tmpRow->ITEMID,$tmpRow->ALT_UOMID_REF]);
                                
                
                    $TOQTY =  isset($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 0;

                    $ObjItem[$tmpindex]->ITEM_ALT_UOMID_REF = $tmpRow->ALT_UOMID_REF;
                    $ObjItem[$tmpindex]->ITEM_ALT_UOMCODE  = $ObjAltUOM[0]->UOMCODE;
                    $ObjItem[$tmpindex]->ITEM_ALT_UOMDESC  = $ObjAltUOM[0]->DESCRIPTIONS;
                    // dd($ObjAltQTY);                

                    // $PENDINGQTY =  !is_null($tmpRow->PENDING_QTY) ? $tmpRow->PENDING_QTY : 0;

                    // $ObjItem[$index]->TOTAL_PENDING =  $PENDINGQTY;
                    
                    //$TOQTY =  0;
                    $ObjItem[$tmpindex]->RPR_REQ_QTY =  isset($tmpRow->REQ_QTY)? $tmpRow->REQ_QTY : 0;

                    $FROMQTY =  isset($tmpRow->REQ_QTY)? $tmpRow->REQ_QTY : 0;
                  
                    $AultUmQuantity = $this->getAltUmQty($tmpRow->ALT_UOMID_REF,$tmpRow->ITEMID,$FROMQTY);

                    //$item_unique_row_id  =   $tmpRow->SOID_REF."_".$tmpRow->SQID_REF."_".$tmpRow->SEID_REF."_".$tmpRow->ITEMID."_".$tmpRow->RPRID_REF."_".$tmpRow->PROID_REF;    
                  
                    
                   $ObjItem[$tmpindex]->aultumquantity = $AultUmQuantity;
                    
               
                //-----------------------------
            }
            //----------
            //dump($ObjItem);
                foreach($ObjItem as $itmindex=>$itmRow){
                    $mat_row_array[] =array(
                        'PROID_REF'=>$mrow['PROID_REF'],
                        'SOID_REF'=>$mrow['SOID_REF'],
                        'SEID_REF'=>$mrow['SEID_REF'], 
                        'SQID_REF'=>$mrow['SQID_REF'], 
                        'RPRID_REF'=>$mrow['RPRID_REF'], 
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
                        'ITEM_ALT_UOMID_REF'=>$itmRow->ITEM_ALT_UOMID_REF,             
                        'ITEM_ALT_UOMCODE'=>$itmRow->ITEM_ALT_UOMCODE,             
                        'ITEM_ALT_UOMDESC'=>$itmRow->ITEM_ALT_UOMDESC,             
                        'BAL_RPR_REQ_QTY'=>$itmRow->BAL_RPR_REQ_QTY,             
                        'ITEM_ISSUED_QTY'=>$itmRow->ITEMQTY,                      
                        'aultumquantity'=>$itmRow->aultumquantity,                      
                        'RPR_REQ_QTY'=>$itmRow->aultumquantity,  
                        'ALPS_PART_NO'=>$itmRow->ALPS_PART_NO,
                        'CUSTOMER_PART_NO'=>$itmRow->CUSTOMER_PART_NO,
                        'OEM_PART_NO'=>$itmRow->OEM_PART_NO,                    
                    );
                }

               


            }
        //echo "-- mat row arr---";
       // DD($mat_row_array);
            $rowscount = count($mat_row_array)>0 ?count($mat_row_array) :1;
            $rowdata = '';
            foreach($mat_row_array as $mrindex=>$mrval){
                
                if(floatval($mrval["BAL_RPR_REQ_QTY"])>0)
                {
                        $rowdata =$rowdata. '
                        <tr  class="participantRow">
                        <td hidden><input type="hidden" id="'.$mrindex.'" > </td>

                        <td><input  type="text" name="popupFGI_'.$mrindex.'" id="popupFGI_'.$mrindex.'"  value="'.$mrval["MAINITEM_CODE"].'" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                        <td    hidden ><input type="text" name="FGI_REF_'.$mrindex.'" id="FGI_REF_'.$mrindex.'"  value="'.$mrval["MAINITEM_ID"].'" class="form-control"  autocomplete="off" /></td>
                        <td><input type="text" name="FGIName_'.$mrindex.'"      id="FGIName_'.$mrindex.'"  value="'.$mrval["MAINITEM_NAME"].'" class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>
                        <td    hidden  ><input type="text" name="popupMAINITEMUOM_'.$mrindex.'"     id="popupMAINITEMUOM_'.$mrindex.'"   value="'.$mrval["MAINITEM_UOMCODE"].'" class="form-control"  autocomplete="off"  readonly style="width:100px;"/></td>
                        <td    hidden  ><input type="text" name="MAINITEM_UOMID_REF_'.$mrindex.'" id="MAINITEM_UOMID_REF_'.$mrindex.'" value="'.$mrval["MAINITEM_UOMID"].'" readonly class="form-control"  autocomplete="off" /></td>

                        <td   hidden   ><input type="text" name="SOID_REF_'.$mrindex.'" id="SOID_REF_'.$mrindex.'" value="'.$mrval["SOID_REF"].'" class="form-control" autocomplete="off" /></td>
                        <td   hidden   ><input type="text" name="SQID_REF_'.$mrindex.'" id="SQID_REF_'.$mrindex.'" value="'.$mrval["SQID_REF"].'"  class="form-control" autocomplete="off" /></td>
                        <td   hidden   ><input type="text" name="SEID_REF_'.$mrindex.'" id="SEID_REF_'.$mrindex.'" value="'.$mrval["SEID_REF"].'"  class="form-control" autocomplete="off" /></td>
                        
                        <td   hidden   ><input type="text" name="RPRID_REF_'.$mrindex.'" id="RPRID_REF_'.$mrindex.'" value="'.$mrval["RPRID_REF"].'" class="form-control"  /></td>
                        <td   hidden   ><input type="text" name="PROID_REF_'.$mrindex.'" id="PROID_REF_'.$mrindex.'" value="'.$mrval["PROID_REF"].'" class="form-control"  /></td>

                        <td><input  type="text" name="popupITEMID_'.$mrindex.'" id="popupITEMID_'.$mrindex.'" value="'.$mrval["ITEM_ICODE"].'"  class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                        <td  hidden ><input type="text" name="ITEMID_REF_'.$mrindex.'" id="ITEMID_REF_'.$mrindex.'"  value="'.$mrval["ITEMID"].'"  class="form-control" autocomplete="off" /></td>
                        <td  hidden ><input type="text" name="OLDITEM_ID_REF_'.$mrindex.'" id="OLDITEM_ID_REF_'.$mrindex.'"  value="'.$mrval["ITEMID"].'"  class="form-control" autocomplete="off" /></td>
                        <td  hidden ><input type="text" name="RPR_REQ_QTY_'.$mrindex.'" id="RPR_REQ_QTY_'.$mrindex.'"  value="'.$mrval["RPR_REQ_QTY"].'"  class="form-control" autocomplete="off" /></td>

                        <td><input type="text" name="ItemName_'.$mrindex.'" id="ItemName_'.$mrindex.'"  value="'.$mrval["ITEM_NAME"].'"   class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>
                        
                        <td '.$AlpsStatus['hidden'].'><input type="text" name="Alpspartno_0" id="Alpspartno_0"  value="'.$mrval["ALPS_PART_NO"].'" class="form-control"  autocomplete="off"  readonly/></td>
                        <td '.$AlpsStatus['hidden'].'><input type="text" name="Custpartno_0" id="Custpartno_0"  value="'.$mrval["CUSTOMER_PART_NO"].'" class="form-control"  autocomplete="off"  readonly/></td>
                        <td '.$AlpsStatus['hidden'].'><input type="text" name="OEMpartno_0"  id="OEMpartno_0"  value="'.$mrval["OEM_PART_NO"].'" class="form-control"  autocomplete="off"   readonly/></td>
                      
                        
                        <td><input type="text" name="popupMUOM_'.$mrindex.'" id="popupMUOM_'.$mrindex.'"  value="'.$mrval["ITEM_UOMCODE"].'"  class="form-control"  autocomplete="off"  readonly style="width:100px;"/></td>
                        <td  hidden ><input type="text" name="MAIN_UOMID_REF_'.$mrindex.'" id="MAIN_UOMID_REF_'.$mrindex.'"  value="'.$mrval["ITEM_UOMID"].'"  class="form-control"  autocomplete="off" /></td>

                        <td align="center"><a class="btn checkstore"  id="'.$mrindex.'" ><i class="fa fa-clone"></i></a></td>
                        <td   hidden  ><input type="text" name="TotalHiddenQty_'.$mrindex.'" id="TotalHiddenQty_'.$mrindex.'"  value="" ></td>
                        <td   hidden  ><input type="text" name="HiddenRowId_'.$mrindex.'" id="HiddenRowId_'.$mrindex.'" value="" ></td>

                        <td  ><input type="text" name="PENDING_QTY_'.$mrindex.'" id="PENDING_QTY_'.$mrindex.'" value="'.$mrval["BAL_RPR_REQ_QTY"].'" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  /></td>
                    
                        <td><input type="text" name="STOCK_INHAND_'.$mrindex.'" id="STOCK_INHAND_'.$mrindex.'" value="" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" readonly  autocomplete="off"   /></td>
                        
                        <td><input type="text" name="RECEIVED_QTY_MU_'.$mrindex.'" id="RECEIVED_QTY_MU_'.$mrindex.'" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off"  readonly /></td>
                        
                        <td><input type="text" name="popupALTUOM_'.$mrindex.'" id="popupALTUOM_'.$mrindex.'" value="'.$mrval["ITEM_ALT_UOMCODE"].'-'.$mrval["ITEM_ALT_UOMDESC"].'" class="form-control"  autocomplete="off"  readonly/></td>
                        <td  hidden ><input type="text" name="ALT_UOMID_REF_'.$mrindex.'" id="ALT_UOMID_REF_'.$mrindex.'" value="'.$mrval["ITEM_ALT_UOMID_REF"].'" class="form-control"  autocomplete="off" /></td>
                        
                        <td  hidden  ><input type="text" name="RECEIVED_QTY_AU_'.$mrindex.'" id="RECEIVED_QTY_AU_'.$mrindex.'"  value="'.$mrval["aultumquantity"].'" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" readonly maxlength="13"  autocomplete="off"   /></td>
                        
                        <td   hidden ><input type="text" name="SHORT_QTY_'.$mrindex.'" id="SHORT_QTY_'.$mrindex.'" value="" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off"  readonly  /></td>
                        
                        <td><input type="text" name="REASON_SHORT_QTY_'.$mrindex.'" id="REASON_SHORT_QTY_'.$mrindex.'" value="" class="form-control"   autocomplete="off"   /></td>
                        
                        
                        <td><input type="text"   name="REMARKS_'.$mrindex.'" id="REMARKS_'.$mrindex.'" value="" class="form-control" maxlength="200"  autocomplete="off" style="width:200px;" /></td>
                        
                        <td align="center" >
                        <button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                        <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                        </td>
                        </tr>
                        ';
                }
                // echo $rowdata;
            }

            if(!empty($rowdata)){
                return Response::json(['totalrows' =>$rowscount,'matrows' => $rowdata]);
            }else{               
                return Response::json(['totalrows' =>$rowscount,'matrows' =>$tr]);
            }
            

        }else{
           
            return Response::json(['totalrows' =>1,'matrows' =>$tr]);
        
        }
        exit();
    }

    public function getItemDetails(Request $request){
        $Status     =   $request['status'];
        $CodeNoId   =   $request['RPRID_REF'];        
        $FGI_REF   =   $request['FGI_REF'];        
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $FMODE= isset($request['mode'])?$request['mode']:"";
        $MISRID= isset($request['MISRID'])?$request['MISRID']:0;
        if($FMODE=='edit'){
            $MISRID= $request['MISRID'];
        }

        $AlpsStatus =   $this->AlpsStatus();
        
        $ObjItem =   DB::table('TBL_TRN_PDRPR_MAT')
                    ->where('TBL_TRN_PDRPR_MAT.RPRID_REF','=',$CodeNoId)                        
                    ->where('TBL_TRN_PDRPR_MAT.MAINITEMID_REF','=',$FGI_REF)      
                    ->leftJoin('TBL_MST_ITEM',   'TBL_MST_ITEM.ITEMID','=',   'TBL_TRN_PDRPR_MAT.ITEMID_REF')   
                    ->select('TBL_TRN_PDRPR_MAT.*','TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ITEMGID_REF','TBL_MST_ITEM.ICID_REF','TBL_MST_ITEM.ITEM_SPECI','TBL_MST_ITEM.MAIN_UOMID_REF','TBL_MST_ITEM.ALT_UOMID_REF')
                    ->orderBy('TBL_TRN_PDRPR_MAT.RPR_MATID', 'DESC')
                    ->get();

       //dd($ObjItem); die;
       $ObjItem2 = $ObjItem;

        if(!empty($ObjItem)){

            foreach ($ObjItem2 as $index=>$dataRow){
                
                //dump($dataRow);
                $ObjSavedQty =   DB::table('TBL_TRN_PDMISR_MAT')
                    ->where('TBL_TRN_PDMISR_MAT.RPRID_REF','=',$dataRow->RPRID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.PROID_REF','=',$dataRow->PROID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.SOID_REF','=',$dataRow->SOID_REF)             
                    ->where('TBL_TRN_PDMISR_MAT.SQID_REF','=',$dataRow->SQID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.SEID_REF','=',$dataRow->SEID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.MAINITEMID_REF','=',$dataRow->MAINITEMID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.OLDITEMID_REF','=',$dataRow->ITEMID_REF)
                    ->where('TBL_TRN_PDMISR_HDR.STATUS','<>','C') 
                    ->leftJoin('TBL_TRN_PDMISR_HDR',   'TBL_TRN_PDMISR_HDR.MISRID','=',   'TBL_TRN_PDMISR_MAT.MISRID_REF')
                    ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDMISR_MAT.ISSUED_QTY),0) AS ISSUED_QTY'))                       
                    ->get();

                // dump($ObjSavedQty);
                $Total_ISSUED_QTY = $ObjSavedQty[0]->ISSUED_QTY;
                
                $RPR_REQ_QTY =  isset($dataRow->REQ_QTY)? $dataRow->REQ_QTY : 0;   
                $BAL_RPR_REQ_QTY = number_format( floatVal($RPR_REQ_QTY) - floatval($Total_ISSUED_QTY), 3,".","" ) ;

                //CONSUMED QTY -- edit case
                $consQty = 0;
                if($MISRID>0){
                    $ObjConsumedQty =   DB::table('TBL_TRN_PDMISR_MAT')                                    
                    ->where('TBL_TRN_PDMISR_MAT.MISRID_REF','=',$MISRID)
                    ->where('TBL_TRN_PDMISR_MAT.RPRID_REF','=',$dataRow->RPRID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.PROID_REF','=',$dataRow->PROID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.SOID_REF','=',$dataRow->SOID_REF)             
                    ->where('TBL_TRN_PDMISR_MAT.SQID_REF','=',$dataRow->SQID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.SEID_REF','=',$dataRow->SEID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.MAINITEMID_REF','=',$dataRow->MAINITEMID_REF)
                    ->where('TBL_TRN_PDMISR_MAT.OLDITEMID_REF','=',$dataRow->ITEMID_REF)
                    ->where('TBL_TRN_PDMISR_HDR.STATUS','<>','C') 
                    ->leftJoin('TBL_TRN_PDMISR_HDR',   'TBL_TRN_PDMISR_HDR.MISRID','=',   'TBL_TRN_PDMISR_MAT.MISRID_REF')
                    ->select(DB::Raw('ISNULL(SUM(TBL_TRN_PDMISR_MAT.ISSUED_QTY),0) AS CONSUMED_ISSUED_QTY'))
                    ->get();
                    $consQty = $ObjConsumedQty[0]->CONSUMED_ISSUED_QTY;
                    $BAL_RPR_REQ_QTY = number_format( floatVal($BAL_RPR_REQ_QTY) + floatval($consQty), 3,".","" ) ;
                }
                
                    $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                        WHERE  CYID_REF = ?  AND UOMID = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                        [$CYID_REF, $dataRow->UOMID_REF, 'A' ]);       

                   
                ///----
                    $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                            WHERE  CYID_REF = ?  AND UOMID = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                            [$CYID_REF, $dataRow->ALT_UOMID_REF, $Status ]);
                    
                    $ObjAltQTY =  DB::select('SELECT TOP 1  TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV  
                                WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                                [$dataRow->ITEMID,$dataRow->ALT_UOMID_REF]);
                                
                
                    $TOQTY =  isset($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 0;

                   
                    //$TOQTY =  0;
                    $FROMQTY =  isset($dataRow->REQ_QTY)? $dataRow->REQ_QTY : 0;
                    if($FMODE=='edit'){
                        $FROMQTY= $consQty;
                    }
                  
                    $AultUmQuantity = $this->getAltUmQty($dataRow->ALT_UOMID_REF,$dataRow->ITEMID,$FROMQTY);

                    $item_unique_row_id  =   $dataRow->SOID_REF."_".$dataRow->SQID_REF."_".$dataRow->SEID_REF."_".$dataRow->ITEMID."_".$dataRow->RPRID_REF."_".$dataRow->PROID_REF."_".$dataRow->MAINITEMID_REF;    
                   // $PENDINGQTY =  !is_null($tmpRow->PENDING_QTY) ? $tmpRow->PENDING_QTY : 0;
                    
                   $ObjItem[$index]->aultumquantity = $AultUmQuantity;            
                   
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
               
                //-----------------------------                
                    
                $row = '';
                if( floatVal($BAL_RPR_REQ_QTY)>0){
                    $row = $row.'
                    <tr id="item_'.$index.'"  class="clsitemid">
                        <td style="width:5%;text-align:center;"><input type="checkbox" id="chkId'.$index.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>
                        <td style="width:10%;">'.$dataRow->ICODE.'</td>
                        <td style="width:10%;">'.$dataRow->NAME.'</td>
                        <td style="width:10%;">'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>
                        <td style="width:10%;">'.$BAL_RPR_REQ_QTY.'</td>
                        <td style="width:10%;">'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>
                        <td style="width:10%;">'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                        <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                        <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                        <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                        <td style="width:5%;">Authorized</td>
                        <td hidden><input type="text" id="addinfoitem_'.$index.'"  data-desc101="'.$ALPS_PART_NO.'" data-desc102="'.$CUSTOMER_PART_NO.'" data-desc103="'.$OEM_PART_NO.'" ></td>
                        <td hidden>
                            <input type="text" id="txtitem_'.$index.'" 
                                data-desc1="'.$dataRow->ITEMID.'" 
                                data-desc2="'.$dataRow->ICODE.'" 
                                data-desc3="'.$dataRow->NAME.'" 
                                data-desc4="'.$dataRow->MAIN_UOMID_REF.'" 
                                data-desc5="'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'" 
                                data-desc6="'.$BAL_RPR_REQ_QTY.'" 
                                data-desc7="'.$item_unique_row_id.'" 
                                data-desc8="'.$dataRow->SQID_REF.'"
                                data-desc9="'.$dataRow->SEID_REF.'"
                                data-desc10="'.$dataRow->SOID_REF.'"
                                data-rpridref="'.$dataRow->RPRID_REF.'"
                                data-proidref="'.$dataRow->PROID_REF.'"                            
                                data-alt_uom_desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"                            
                                data-alt_uom_id="'.$dataRow->ALT_UOMID_REF.'"                            
                                data-pending_qty="'.$BAL_RPR_REQ_QTY.'"                            
                                data-aultumquantity="'.$AultUmQuantity.'"     
                                data-itemfgiid="'.$FGI_REF.'"
                                data-itemsavedqty="'.number_format( floatval($consQty), 3,".","" ).'"                       
                                data-rpr_reqqty="'.number_format( floatval($FROMQTY), 3,".","" ).'"                       
                            />
                        </td>
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

    // 
  

    public function getStoreDetails(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $ROW_ID         =   $request['ROW_ID'];
        $RPRID_REF         =   $request['RPRID_REF'];
        //$POID_REF       =   $request['POID_REF'];
        $ITEMID_REF     =   $request['ITEMID_REF'];
        $MAIN_UOMID_DES =   $request['MAIN_UOMID_DES'];
        $MAIN_UOMID_REF =   $request['MAIN_UOMID_REF'];
        $ALT_UOMID_DES  =   $request['ALT_UOMID_DES'];
        $ALT_UOMID_REF  =   $request['ALT_UOMID_REF'];
        $ITEMROWID      =   $request['ITEMROWID'];
        $ACTION_TYPE    =   $request['ACTION_TYPE'] =="VIEW"?'disabled':'';

        $PROID_REF  =   $request['PROID_REF'];
        $SOID_REF  =   $request['SOID_REF'];
        $SQID_REF  =   $request['SQID_REF'];
        $SEID_REF  =   $request['SEID_REF'];
        $FGI_REF   =   $request['FGI_REF'];

        $SRNOA          =   NULL;
        $BATCHNOA       =   NULL;
        $dataArr        =   array();
        $dataArr2       =   array();
        $dataArr3       =   array();

        if($ITEMROWID !=""){
            $exp        =   explode(",",$ITEMROWID);
            
            foreach($exp as $val){
                $keyid      =   explode("_",$val);
                $batchid    =   $keyid[0];
                $qty        =   $keyid[1];
               // $LOT        =   $keyid[2];
                //$VENDLOT    =   $keyid[3];
                $dataArr[$batchid]  =   $qty;
                //$dataArr2[$batchid]  =   $LOT;
                //$dataArr3[$batchid]  =   $VENDLOT;
            }
        }

        //dump($dataArr);
        $objResponse =  DB::table('TBL_MST_ITEMCHECKFLAG')
            ->where('ITEMID_REF','=',$ITEMID_REF)
            ->select('SRNOA','BATCHNOA')
            ->first();

        if(!empty($objResponse)){
            $SRNOA      =   $objResponse->SRNOA;
            $BATCHNOA   =   $objResponse->BATCHNOA;
        }

        // $objBatch =  DB::SELECT("SELECT STID,STCODE,NAME AS STNAME ,
        // (SELECT SUM(CURRENT_QTY)  FROM TBL_MST_BATCH WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF'
        // AND STATUS='A' AND STID_REF=STID AND ITEMID_REF='$ITEMID_REF') AS TOTAL_STOCK
        // FROM TBL_MST_STORE WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF'
        // AND STATUS='A' AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 )
        // ");

        $WHERE_CURRENT_QTY  =   $request['ACTION_TYPE'] =="ADD"?" AND T1.CURRENT_QTY > 0":"";
        $objBatch =  DB::SELECT("SELECT 
        T1.BATCHID,T1.BATCH_CODE,T1.CURRENT_QTY AS TOTAL_STOCK,
        T2.STID,T2.STCODE,T2.NAME AS STNAME
        FROM TBL_MST_BATCH T1 
        LEFT JOIN TBL_MST_STORE T2 ON T2.STID=T1.STID_REF
        WHERE T1.STATUS='A' AND T1.CYID_REF ='$CYID_REF' AND T1.BRID_REF ='$BRID_REF'  AND T1.ITEMID_REF ='$ITEMID_REF' AND T1.UOMID_REF ='$MAIN_UOMID_REF' $WHERE_CURRENT_QTY
        ");

    //     echo "objBatch=";
    //    dump($objBatch);
        echo '<thead>';
        echo '<tr>';
        echo '<th>Batch/Store</th>';
        echo '<th>Main UoM (MU)</th>';
        echo '<th>Stock-in-hand</th>';
        echo '<th>Issued Qty (MU)</th>';
        echo '<th>Alt UOM (AU)</th>';
        echo '<th>Issued Qty (AU)</th>';
        //echo '<th>Our Lot No</th>';
        //echo '<th>Vendor Lot No</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        if(!empty($objBatch)){        

            foreach($objBatch as $key=>$val){

                $BATCHID    =   $val->BATCHID;

                $TOTAL_STOCK    =   $val->TOTAL_STOCK !=""?$val->TOTAL_STOCK:0;

                $StoreRowId     =   $val->STID.'#'.$RPRID_REF.'#'.$BATCHID.'#'.$ITEMID_REF.'#'.$MAIN_UOMID_REF.'#'.$ALT_UOMID_REF.'#'.$PROID_REF.'#'.$SOID_REF.'#'.$SQID_REF.'#'.$SEID_REF.'#'.$FGI_REF;
                //echo "<br>".$StoreRowId;
            
                $qtyvalue       =   array_key_exists($StoreRowId, $dataArr)?$dataArr[$StoreRowId]:0;

                
                $AluQty         =   $this->getAltUmQty($ALT_UOMID_REF,$ITEMID_REF,$qtyvalue);

                // if($request['ACTION_TYPE'] =="ADD"){
                //     $CURRENT_QTY=$TOTAL_STOCK;
                // }
                // else{
                    
                //     $CURRENT_QTY=(floatval($TOTAL_STOCK)+floatval($qtyvalue));
                // }

                $CURRENT_QTY=$TOTAL_STOCK;

                $MainReceivedQty    =   $qtyvalue > 0?$qtyvalue:'';
                $AultReceivedQty    =   $AluQty > 0?$AluQty:'';       
                
                //$LOT_NO       =   array_key_exists($StoreRowId, $dataArr2)?$dataArr2[$StoreRowId]:'';
                //$VENDOR_LOTNO =   array_key_exists($StoreRowId, $dataArr3)?$dataArr3[$StoreRowId]:'';

                
            
                echo '<tr  class="participantRow33">';
                echo '<td hidden><input type="text" id="'.$key.'" value="'.$ROW_ID.'" ></td>';
                echo '<td style="width:25%">'.$val->BATCH_CODE.' / '.$val->STCODE.' - '.'</td>';
                echo '<td style="width:10%">'.$MAIN_UOMID_DES.'</td>';
                echo '<td style="width:10%">'.$CURRENT_QTY.'</td>';
                echo '<td style="width:15%"><input '.$ACTION_TYPE.' type="text" name="UserQty_'.$key.'" id="UserQty_'.$key.'"  value="'.$MainReceivedQty.'" onkeyup="checkStoreQty('.$ROW_ID.','.$ITEMID_REF.','.$ALT_UOMID_REF.',this.value,'.$key.','.$CURRENT_QTY.')" class="qtytext"  onkeypress="return isNumberDecimalKey(event,this)" autocomplete="off"  ></td>';
                echo '<td style="width:10%">'.$ALT_UOMID_DES.'</td>';
                echo '<td style="width:15%"><input '.$ACTION_TYPE.' type="text" name="AltUserQty_'.$key.'" id="AltUserQty_'.$key.'" value="'.$AultReceivedQty.'" class="qtytext"  autocomplete="off" readonly  ></td>';
                //echo '<td style="width:10%"><input '.$ACTION_TYPE.' type="text" name="LOT_NO_'.$key.'" id="LOT_NO_'.$key.'" value="'.$LOT_NO.'"  class="qtytext" autocomplete="off"  ></td>';
                //echo '<td style="width:10%"><input '.$ACTION_TYPE.' type="text" name="VENDOR_LOTNO_'.$key.'" id="VENDOR_LOTNO_'.$key.'" value="'.$VENDOR_LOTNO.'"  class="qtytext"  autocomplete="off"  ></td>';
                echo '<td hidden><input type="hidden" name="BATCHID_'.$key.'" id="BATCHID_'.$key.'" value="'.$StoreRowId.'" class="qtytext" ></td>';
                echo '<td hidden><input type="hidden" name="ROWID_'.$key.'" id="ROWID_'.$key.'" value="'.$ROW_ID.'" class="qtytext" ></td>';
                echo '<td hidden><input type="hidden" name="TOTAL_STOCK_'.$key.'" id="TOTAL_STOCK_'.$key.'" value="'.$CURRENT_QTY.'" class="qtytext" ></td>';
                echo '<td hidden><input type="hidden" name="BATCHNOA_'.$key.'" id="BATCHNOA_'.$key.'" value="'.$BATCHNOA.'" class="qtytext" ></td>';
                echo '</tr>';
                echo '</tr>';

                


            }

        }else{
            echo '<tr><td colspan="11">Record not found</td></tr>';
            
        }        
        echo '</tbody>';
        exit();
    }

    public function getCodeNo(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $DEPID_REF      =   $request['id'];

        $ObjData =  DB::select("SELECT MRSID,MRS_NO,MRS_DT 
        FROM TBL_TRN_MRQS01_HDR 
        WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF' AND DEPID_REF='$DEPID_REF' AND STATUS='A'");


        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){
                
                $row = '';
                $row = $row.'<tr id="rgpcode_'.$dataRow->MRSID .'"  class="clssqid"><td width="50%">'.$dataRow->MRS_NO;
                $row = $row.'<input type="hidden" id="txtrgpcode_'.$dataRow->MRSID.'" data-desc="'.$dataRow->MRS_NO.'" 
                value="'.$dataRow->MRSID.'"/></td><td>'.$dataRow->MRS_DT.'</td></tr>';
                echo $row;
                
            }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
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
            echo number_format($auomqty,3,".","") ;
        }else{
            echo '0';
        }
        exit();
    }

    public function getSUBITEMCodeNo(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $Status         = "A";
        $FGI_REF         =   $request['FGI_REF'];  //main item id
        $ITEMID_REF      =   $request['ITEMID_REF'];
        $RPR_REQ_QTY      =   $request['RPR_REQ_QTY'];
        $FMODE          = isset($request['FMODE']) ? $request['FMODE'] :"";
       
        $SOID_REF      =   $request['SOID_REF'];
        $SQID_REF      =   $request['SQID_REF'];
        $SEID_REF      =   $request['SEID_REF'];
        $RPRID_REF     =   $request['RPRID_REF'];
        $PROID_REF     =   $request['PROID_REF'];

        $fieldid    = $request['fieldid'];


        
        // $MAINITEMID_REF =   $request['REQ_ITEMID'];
        // $MAIN_PD_OR_QTY =   $request['MAIN_PD_OR_QTY'];

        $BOM_HDR    =   DB::table('TBL_MST_BOM_HDR')
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('BRID_REF','=',$BRID_REF)
                            ->where('ITEMID_REF','=',$FGI_REF)
                            ->where('STATUS','=','A')
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('BOMID')
                            ->first();
      //  dump($BOM_HDR);
        $ObjData=[];
        if(!empty($BOM_HDR)){

            $ObjData    =   DB::select("SELECT T1.*, T2.ICODE,T2.NAME
                            FROM TBL_MST_BOM_SUB T1
                            LEFT JOIN TBL_MST_ITEM T2 ON T1.SUBITEMID_REF=T2.ITEMID
                            WHERE BOMID_REF='$BOM_HDR->BOMID' AND MAINITEMID_REF='$ITEMID_REF' ");

        }
       //DUMP($ObjData);
    
     
        
        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){

               //--------------------------
               $objMstItem =   DB::select('SELECT top 1 ITEMID,ICODE,NAME,MAIN_UOMID_REF,ALT_UOMID_REF FROM TBL_MST_ITEM  
                                    WHERE STATUS= ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND ITEMID = ? ', ['A',$dataRow->SUBITEMID_REF]);

             // dump($objMstItem);

               $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
               WHERE  CYID_REF = ?  AND UOMID = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
               [$CYID_REF, $objMstItem[0]->MAIN_UOMID_REF, 'A' ]);       

                ///----
                $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                WHERE  CYID_REF = ?  AND UOMID = ? AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                [$CYID_REF, $objMstItem[0]->ALT_UOMID_REF, $Status ]);

                $ObjAltQTY =  DB::select('SELECT TOP 1  TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV  
                    WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                    [$objMstItem[0]->ITEMID,$objMstItem[0]->ALT_UOMID_REF]);
                    

                $TOQTY =  isset($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 0;


                //$TOQTY =  0;
                $FROMQTY =  isset( $RPR_REQ_QTY)?  $RPR_REQ_QTY : 0;

                $AultUmQuantity = $this->getAltUmQty($objMstItem[0]->ALT_UOMID_REF,$objMstItem[0]->ITEMID,$FROMQTY);

               // var exist_val   =   SOID_REF+"_"+SQID_REF+"_"+SEID_REF+"_"+ITEMID_REF+"_"+RPRID_REF+"_"+PROID_REF+"_"+FGI_REF;

                $uniqitemid  =   $SOID_REF."_".$SQID_REF."_".$SEID_REF."_".$dataRow->SUBITEMID_REF."_".$RPRID_REF."_".$PROID_REF."_".$FGI_REF;    
                
                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="subcode_'.$dataRow->BOM_SUBID .'"  class="clssSUBITEMid" value="'.$dataRow->BOM_SUBID.'" ></td>
                <td class="ROW2">'.$dataRow->ICODE; 
                $row = $row.'<input type="hidden" id="txtsubcode_'.$dataRow->BOM_SUBID.'" 

                data-desc1="'.$dataRow->ICODE.'"
                data-desc2="'.$dataRow->NAME.'"
                data-desc3="'.$dataRow->SUBITEMID_REF.'"
                data-desc4="'.$dataRow->MAINITEMID_REF.'"
                data-desc5=""
                data-desc6=""               
                data-subitem_uomid="'.$objMstItem[0]->MAIN_UOMID_REF.'"               
                data-subitem_uomdesc="'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'"   
                data-subitem_altuomid="'.$objMstItem[0]->ALT_UOMID_REF.'"              
                data-subitem_altuomdesc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"              
                data-subitem_altuomqty="'.$AultUmQuantity.'"              
                data-subitem_uniqitemid="'.$uniqitemid.'"              
                
                value="'.$dataRow->BOM_SUBID.'"/>
                
                </td>
                
                <td class="ROW3" >'.$dataRow->NAME.'</td></tr>';
                echo $row;
                
            }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();        
    }
    
    
    public function getTotalDateWiseStock(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
       
        if(isset($request['item_array']) && !empty($request['item_array'])){
            foreach($request['item_array'] as $key=>$val){

                $DOC_DT         =   $val['MISRDT'];
                $ITEMID_REF     =   $val['ITEMID_REF'];
                $UOMID_REF      =   $val['MAIN_UOMID_REF'];
                $RECEIVED_QTY   =   floatval($val['RECEIVED_QTY_MU']);
                $MAIN_ITEM_CODE =   $val['MAIN_ITEM_CODE'];
                $SUB_ITEM_CODE  =   $val['ITEMID_CODE'];
                $SUB_ITEM_NAME  =   $val['ITEMID_NAME'];

                $data           =   DB::select("SELECT SUM(A.CURRENT_QTY) AS TOTAL_CURRENT_STOCK 
                                    FROM TBL_MST_BATCH A (NOLOCK) INNER JOIN TBL_MST_STOCK_BATCH_HIS B (NOLOCK) 
                                    ON A.BATCHID = B.BATCHID_REF AND A.ITEMID_REF = B.ITEMID_REF AND A.UOMID_REF = B.UOMID_REF
                                    WHERE A.CYID_REF ='$CYID_REF' AND A.BRID_REF ='$BRID_REF'  AND A.STATUS='A'  
                                    AND B.DATE <= '$DOC_DT' AND A.ITEMID_REF ='$ITEMID_REF' AND A.UOMID_REF ='$UOMID_REF' ");


                $TOTAL_STOCK    =   floatval($data[0]->TOTAL_CURRENT_STOCK);

                if($RECEIVED_QTY > $TOTAL_STOCK){
                    return Response::json(['result' =>false,'message' =>'Stock is not avaliable as per document date for Issued Qty (MU) Main Item Code '.$MAIN_ITEM_CODE.' for ('.$SUB_ITEM_CODE.' - '.$SUB_ITEM_NAME.') in material tab.']);
                    exit();
                }

            }
  
        }

        return Response::json(['result' =>true,'message' => '']);
        exit(); 
    }

    public function misrCheckIssueReQsQty(){
        $count  =   DB::table('TBL_MST_ADDL_TAB_SETTING')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('TABLE_NAME','=','MISR_CHECK_ISSUE_REQS_QTY')
                    ->where('TAB_NAME','=','YES')
                    ->count();

        $result =   $count > 0?$count:'';   

        return $result;
    }

    public function checkIssueQty(Request $request){

        $response   =   array();
        if(isset($request['itemDetails']) && !empty($request['itemDetails'])){

            foreach($request['itemDetails'] as $key=>$val){

                $DOCID      =   $val['DOCID'];
                $ITEMID     =   $val['ITEMID'];
                $ITEMCODE   =   $val['ITEMCODE'];
                $ISSUED_QTY =   floatval($val['ISSUED_QTY']);

                $check_flag =   DB::table('TBL_MST_ITEMCHECKFLAG')
                                ->where('ITEMID_REF','=',$ITEMID)
                                ->where('SRNOA','=','1')
                                ->count();

                if($check_flag > 0){

                    $data       =   DB::table('TBL_TRN_BARCODE_OUT_MAT')
                                    ->where('BRCOID_REF','=',$DOCID)
                                    ->where('ITEMID_REF','=',$ITEMID)
                                    ->select('OUT_QTY')
                                    ->first();

                    if(!empty($data)){
                        $OUT_QTY    =   floatval($data->OUT_QTY);

                        if($ISSUED_QTY !=$OUT_QTY){
                            $response[]=$ITEMCODE;
                        }
                    }
                    else{
                        $response[]=$ITEMCODE;
                    }

                }
            }

        }

        $data   =   count($response) > 0?implode(',',$response):'';
        return $data;
    
    }


} 
