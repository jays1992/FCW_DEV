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

class TrnFrm362Controller extends Controller{

    protected $form_id  = 362;
    protected $vtid_ref = 448;
    protected $view     = "transactions.Quality.QualityInspection.trnfrm362";
   
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];
    
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){ 
        
        $FormId     =   $this->form_id;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');   
        
        $objRights  =   DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();
  
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

        $objDataList    =	DB::select("SELECT '$USER_LEVEL' AS USER_LEVEL,a.ACTIONNAME,hdr.QIGID,hdr.QIGNO,hdr.QIGDT,hdr.STATUS,hdr.INDATE,CONCAT(V.VCODE,'-',V.NAME) AS VENDOR_NAME,
        (
        SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
        LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
        WHERE  AUD.VID=hdr.QIGID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
        AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
        ) AS CREATED_BY,
        
        T2.ICODE,T2.NAME
        from TBL_TRN_AUDITTRAIL a 
        INNER JOIN TBL_TRN_QIG_HDR hdr ON a.VID = hdr.QIGID AND a.VTID_REF = hdr.VTID_REF AND a.CYID_REF = hdr.CYID_REF AND a.BRID_REF = hdr.BRID_REF 
        INNER JOIN TBL_MST_ITEM T2 ON hdr.ITEMID_REF = T2.ITEMID  
        LEFT JOIN TBL_TRN_IGRN02_HDR GRN ON GRN.GRNID=hdr.GRNID_REF
        LEFT JOIN TBL_MST_VENDOR V ON V.SLID_REF=GRN.VID_REF        
        WHERE a.VTID_REF = '$this->vtid_ref' AND hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' 
        AND a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b WHERE a.VTID_REF = b.VTID_REF AND a.VID = b.VID)
        ORDER BY hdr.QIGID DESC 
        ");
        //dd($objDataList); 

        return view($this->view,compact(['REQUEST_DATA','FormId','objRights','objDataList','DATA_STATUS']));
    }

  public function ViewReport($request) {

        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
        
            $QIGID       =   $myValue['QIGID'];
            $Flag       =   $myValue['Flag'];

        // $objSalesOrder = DB::table('TBL_TRN_PROR01_HDR')
        // ->where('TBL_TRN_PROR01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        // ->where('TBL_TRN_PROR01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        // ->where('TBL_TRN_PROR01_HDR.POID','=',$POID)
        // ->select('TBL_TRN_PROR01_HDR.*')
        // ->first();
        
        
        $ssrs = new \SSRS\Report('http://103.139.58.23:8181//ReportServer/', array('username' => 'App', 'password' => 'admin@123'));
        $result = $ssrs->loadReport('/ERP/QCPrint');
        //$result = $ssrs->loadReport('/ERP/POPrint -ZEP');
        
        $reportParameters = array(
            'QIGID' => $QIGID,
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
			else if($Flag == 'R')
            {
                $output = $ssrs->render('HTML4.0'); 
                echo $output;
            }
        
    }


    public function add(){       
        
        $Status         =   "A";
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $objlastdt      =   $this->getLastdt();
        $objUserList    =   $this->getUserList();
        $objGRNList     =   $this->getGRNList();
        $objStoreList   =   $this->getStoreList();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_QIG_HDR',
            'HDR_ID'=>'QIGID',
            'HDR_DOC_NO'=>'QIGNO',
            'HDR_DOC_DT'=>'QIGDT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        
        
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_QI")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDFQIID')->from('TBL_MST_UDFFOR_QI')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                     
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                
                   

        $objUdf  = DB::table('TBL_MST_UDFFOR_QI')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdf); 
                    
        $FormId  = $this->form_id;

        $AlpsStatus =   $this->AlpsStatus();
        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        return view($this->view.'add', compact(
            ['FormId','objUdf','objCountUDF','objlastdt','objUserList','objGRNList','objStoreList','AlpsStatus','TabSetting','doc_req','docarray']
        ));       
    }

    public function getLastdt(){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        return  DB::select('SELECT MAX(QIGDT) QIGDT FROM TBL_TRN_QIG_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
    }

    public function save(Request $request) {

        $r_count1 = $request['SELECT_QCP'];
        $r_count3 = $request['Row_Count3'];
  
        $req_data=array();
        foreach($r_count1 as $key=>$i){

            if(isset($request['QCPID_REF_'.$i])){

                $req_data[$i] = [
                    'QCPID_REF'         => $request['QCPID_REF_'.$i] !=""?$request['QCPID_REF_'.$i]:NULL,
                    'STD_VALUE'         => $request['STD_VALUE_'.$i] !=""?$request['STD_VALUE_'.$i]:NULL,
                    'OBS_VALUE1'        => $request['OBS_VALUE1_'.$i] !=""?$request['OBS_VALUE1_'.$i]:0,
                    'OBS_VALUE2'        => $request['OBS_VALUE2_'.$i] !=""?$request['OBS_VALUE2_'.$i]:0,
                    'OBS_VALUE3'        => $request['OBS_VALUE3_'.$i] !=""?$request['OBS_VALUE3_'.$i]:0,
                    'OBS_VALUE4'        => $request['OBS_VALUE4_'.$i] !=""?$request['OBS_VALUE4_'.$i]:0,
                    'OBS_VALUE5'        => $request['OBS_VALUE5_'.$i] !=""?$request['OBS_VALUE5_'.$i]:0,
                    'OBS_VALUE6'        => $request['OBS_VALUE6_'.$i] !=""?$request['OBS_VALUE6_'.$i]:0,
                    'OBS_VALUE7'        => $request['OBS_VALUE7_'.$i] !=""?$request['OBS_VALUE7_'.$i]:0,
                    'OBS_VALUE8'        => $request['OBS_VALUE8_'.$i] !=""?$request['OBS_VALUE8_'.$i]:0,
                    'OBS_VALUE9'        => $request['OBS_VALUE9_'.$i] !=""?$request['OBS_VALUE9_'.$i]:0,
                    'OBS_VALUE10'       => $request['OBS_VALUE10_'.$i] !=""?$request['OBS_VALUE10_'.$i]:0,
                    'AVG_OBS_VALUE'     => $request['AVG_OBS_VALUE_'.$i] !=""?$request['AVG_OBS_VALUE_'.$i]:NULL,
                    'REJECTED'          => $request['REJECTED_'.$i] =="Yes"?1:0,
                    'RRID_REF'          => $request['RRID_REF_'.$i] !=""?$request['RRID_REF_'.$i]:NULL,
                    'UOMID_REF'          => $request['UOMID_REF_'.$i] !=""?$request['UOMID_REF_'.$i]:NULL,
                    'INSTRUMENT_METHOD_ID_REF'          => $request['INTMNTID_REF_'.$i] !=""?$request['INTMNTID_REF_'.$i]:NULL,                    
                    'REJECTION_REMARKS' => $request['REJECTION_REMARKS_'.$i] !=""?$request['REJECTION_REMARKS_'.$i]:NULL
                ];

            }
        }

        //dd($req_data);

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);


        $udffield_Data = [];      
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFQIID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
            }
            
        }

        if(count($udffield_Data)>0){
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
        }else{
            $XMLUDF = NULL;
        }


        $VTID_REF     =   $this->vtid_ref;
        
        $USERID_REF = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $QIGNO                  =   $request['QIGNO'] !=""?$request['QIGNO']:NULL;
        $QIGDT                  =   $request['QIGDT'] !=""?$request['QIGDT']:NULL;
        $QC_PROCESS_BY          =   $request['QC_PROCESS_BY'] !=""?$request['QC_PROCESS_BY']:NULL;
        $EXT_LABNAME            =   $request['EXT_LABNAME'] !=""?$request['EXT_LABNAME']:NULL;

        $REF_DOCNO              =   $request['REF_DOCNO'] !=""?$request['REF_DOCNO']:NULL;
        $GRNID_REF              =   $request['GRNID_REF'] !=""?$request['GRNID_REF']:NULL;
        $ITEMID_REF             =   $request['ITEMID_REF'] !=""?$request['ITEMID_REF']:NULL;
        $GRN_REC_QTY            =   $request['GRN_REC_QTY'] !=""?$request['GRN_REC_QTY']:0;
        $UOMID_REF              =   $request['UOMID_REF'] !=""?$request['UOMID_REF']:NULL;

        $REMARKS                =   $request['REMARKS'] !=""?$request['REMARKS']:NULL;
        $QI_PICK_QTY            =   $request['QI_PICK_QTY'] !=""?$request['QI_PICK_QTY']:0;
        $REJECTED_QTY           =   $request['REJECTED_QTY'] !=""?$request['REJECTED_QTY']:0;
        $QC_OK_QTY              =   $request['QC_OK_QTY'] !=""?$request['QC_OK_QTY']:0;

        $GEID_REF               =   $request['GEID_REF'] !=""?$request['GEID_REF']:NULL;
        $POID_REF               =   $request['POID_REF'] !=""?$request['POID_REF']:NULL;

        $MRSID_REF              =   $request['MRSID_REF'] !=""?$request['MRSID_REF']:NULL;
        $PIID_REF               =   $request['PIID_REF'] !=""?$request['PIID_REF']:NULL;
        $RFQID_REF              =   $request['RFQID_REF'] !=""?$request['RFQID_REF']:NULL;
        $VQID_REF               =   $request['VQID_REF'] !=""?$request['VQID_REF']:NULL;
        $IPOID_REF              =   $request['IPOID_REF'] !=""?$request['IPOID_REF']:NULL;
        $BPOID_REF              =   $request['BPOID_REF'] !=""?$request['BPOID_REF']:NULL;

        $PENDING_QC_QTY         =   $request['PENDING_QC_QTY'] !=""?$request['PENDING_QC_QTY']:0;
        $REJECTED_STID_REF      =   $request['REJECTED_STID_REF'] !=""?$request['REJECTED_STID_REF']:NULL;
        $QC_OK_STID_REF         =   $request['QC_OK_STID_REF'] !=""?$request['QC_OK_STID_REF']:NULL;
        $PENDING_QC_STID_REF    =   $request['PENDING_QC_STID_REF'] !=""?$request['PENDING_QC_STID_REF']:NULL;
        $QC_ACCEPTED_AS         =   $request['QC_ACCEPTED_AS'] !=""?$request['QC_ACCEPTED_AS']:NULL;
        
        $log_data = [ 
            $QIGNO,                 $QIGDT,         $QC_PROCESS_BY,     $EXT_LABNAME,       $REF_DOCNO,
            $GRNID_REF,             $ITEMID_REF,    $GRN_REC_QTY,       $UOMID_REF,         $REMARKS,               
            $QI_PICK_QTY,           $REJECTED_QTY,  $QC_OK_QTY,         $CYID_REF,          $BRID_REF,              
            $FYID_REF,              $VTID_REF,      $GEID_REF,          $POID_REF,          $MRSID_REF,             
            $PIID_REF,              $RFQID_REF,     $VQID_REF,          $IPOID_REF,         $BPOID_REF,
            $XMLMAT,                $XMLUDF,        $USERID_REF,        Date('Y-m-d'),      Date('h:i:s.u'),    
            $ACTIONNAME,            $IPADDRESS,     $PENDING_QC_QTY,    $REJECTED_STID_REF, $QC_OK_STID_REF,
            $PENDING_QC_STID_REF,   $QC_ACCEPTED_AS
        ];
        

        $sp_result = DB::select('EXEC SP_QIG_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);     
        
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

            $objRights      =   DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();

            $objlastdt      =   $this->getLastdt();
            $objUserList    =   $this->getUserList();
            $objUserList    =   $this->getUserList();
            $objGRNList     =   $this->getGRNList();
            $objStoreList   =   $this->getStoreList();


            $HDR            =   DB::select("SELECT 
                                T1.*,
                                CONCAT(T2.ICODE,'-',T2.NAME) AS ITEM_CODE_NAME,
                                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS UOM_CODE_DES,
                                CONCAT(T4.EMPCODE,'-',T4.FNAME) AS EMP_CODE_DES,
                                T5.GRN_NO,
                                CONCAT(T6.STCODE,'-',T6.NAME) AS REJECTED_STORE,
                                CONCAT(T7.STCODE,'-',T7.NAME) AS QC_OK_STORE,
                                CONCAT(T8.STCODE,'-',T8.NAME) AS PENDING_QC_STORE
                                FROM TBL_TRN_QIG_HDR T1
                                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                                LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                                LEFT JOIN TBL_MST_EMPLOYEE T4 ON T1.QC_PROCESS_BY=T4.EMPID
                                LEFT JOIN TBL_TRN_IGRN02_HDR T5 ON T1.GRNID_REF=T5.GRNID
                                LEFT JOIN TBL_MST_STORE T6 ON T1.REJECTED_STID_REF=T6.STID
                                LEFT JOIN TBL_MST_STORE T7 ON T1.QC_OK_STID_REF=T7.STID
                                LEFT JOIN TBL_MST_STORE T8 ON T1.PENDING_QC_STID_REF=T8.STID

                                WHERE T1.QIGID='$id' ")[0];

                                
            $ITEMID_REF     =   isset($HDR->ITEMID_REF)?$HDR->ITEMID_REF:NULL;

            $MAT            =   DB::select("SELECT 
                                T1.*,
                                T2.QCPID,T2.QCP_CODE,T2.QCP_DESC,
                                CONCAT(T3.RR_CODE,'-',T3.RR_DESC) AS RR_CODE_DES,
                                (T4.UOMCODE) AS UOM_CODE_DES,
                                (T5.INSTRUMENT_METHOD_NAME) AS INTMNT_CODE_DES,
                                (
                                SELECT DISTINCT T5.STANDARDVALUE_TYPE
                                FROM TBL_MST_QIC_HDR T4
                                INNER JOIN TBL_MST_QIC_MAT T5 ON T4.QICID=T5.QICID_REF AND T5.QCPID_REF=T1.QCPID_REF
                                WHERE T4.ITEMID_REF='$ITEMID_REF' AND T4.CYID_REF='$CYID_REF' AND T4.BRID_REF='$BRID_REF' AND T4.STATUS='A' AND (T4.DEACTIVATED=0 or T4.DEACTIVATED is null) 
                                ) AS STANDARDVALUE_TYPE
                                FROM TBL_TRN_QIG_MAT T1
                                LEFT JOIN TBL_MST_QCP T2 ON T1.QCPID_REF=T2.QCPID
                                LEFT JOIN TBL_MST_REJECTION_REASON T3 ON T1.RRID_REF=T3.RRID
                                LEFT JOIN TBL_MST_UOM T4 ON T1.UOMID_REF=T4.UOMID
                                LEFT JOIN TBL_MST_INSTRUMENT_METHOD T5 ON T1.INSTRUMENT_METHOD_ID_REF=T5.INSTRUMENT_METHOD_ID
                                WHERE T1.QIGID_REF='$id' ORDER BY T1.QIGID_REF ASC");
                                
                               
            $ObjUnionUDF    =   DB::table("TBL_MST_UDFFOR_QI")->select('*')
                                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF){       
                                        $query->select('UDFQIID')->from('TBL_MST_UDFFOR_QI')
                                        ->where('STATUS','=','A')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);                                  
                                        }
                                    )
                                    ->where('DEACTIVATED','=',0)
                                    ->where('STATUS','<>','C')                    
                                    ->where('CYID_REF','=',$CYID_REF);
                                        
                $objUdf         =   DB::table('TBL_MST_UDFFOR_QI')
                                    ->where('STATUS','=','A')
                                    ->where('PARENTID','=',0)
                                    ->where('DEACTIVATED','=',0)
                                    ->where('CYID_REF','=',$CYID_REF)
                                    ->union($ObjUnionUDF)
                                    ->get()->toArray();  

                $objCountUDF    =   count($objUdf);
            
            
                $objtempUdf     =   $objUdf;
                foreach ($objtempUdf as $index => $udfvalue) {

                    $objSavedUDF =  DB::table('TBL_TRN_QIG_UDF')
                                    ->where('QIGID_REF','=',$id)
                                    ->where('UDFQIID_REF','=',$udfvalue->UDFQIID)
                                    ->select('VALUE')
                                    ->get()->toArray();

                    if(!empty($objSavedUDF)){
                        $objUdf[$index]->UDF_VALUE = $objSavedUDF[0]->VALUE;
                    }
                    else{
                        $objUdf[$index]->UDF_VALUE = NULL;
                    }
                }

                $objtempUdf = []; 
                        
            $FormId  = $this->form_id;

            $AlpsStatus =   $this->AlpsStatus();
            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');


            return view($this->view.'edit', compact([
                'FormId','objUdf','objCountUDF','objlastdt','objUserList','objGRNList',
                'objStoreList','objRights','HDR','MAT','AlpsStatus','TabSetting'
            ]));
        
        }
    }

    public function view($id=NULL){  
         
        if(!is_null($id)){
        
            $Status         =   "A";
            $CYID_REF       =   Auth::user()->CYID_REF;
            $BRID_REF       =   Session::get('BRID_REF');
            $FYID_REF       =   Session::get('FYID_REF');

            $objRights      =   DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();

            $objlastdt      =   $this->getLastdt();
            $objUserList    =   $this->getUserList();
            $objUserList    =   $this->getUserList();
            $objGRNList     =   $this->getGRNList();
            $objStoreList   =   $this->getStoreList();


            $HDR            =   DB::select("SELECT 
                                T1.*,
                                CONCAT(T2.ICODE,'-',T2.NAME) AS ITEM_CODE_NAME,
                                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS UOM_CODE_DES,
                                CONCAT(T4.EMPCODE,'-',T4.FNAME) AS EMP_CODE_DES,
                                T5.GRN_NO,
                                CONCAT(T6.STCODE,'-',T6.NAME) AS REJECTED_STORE,
                                CONCAT(T7.STCODE,'-',T7.NAME) AS QC_OK_STORE,
                                CONCAT(T8.STCODE,'-',T8.NAME) AS PENDING_QC_STORE
                                FROM TBL_TRN_QIG_HDR T1
                                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                                LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                                LEFT JOIN TBL_MST_EMPLOYEE T4 ON T1.QC_PROCESS_BY=T4.EMPID
                                LEFT JOIN TBL_TRN_IGRN02_HDR T5 ON T1.GRNID_REF=T5.GRNID
                                LEFT JOIN TBL_MST_STORE T6 ON T1.REJECTED_STID_REF=T6.STID
                                LEFT JOIN TBL_MST_STORE T7 ON T1.QC_OK_STID_REF=T7.STID
                                LEFT JOIN TBL_MST_STORE T8 ON T1.PENDING_QC_STID_REF=T8.STID
                                WHERE T1.QIGID='$id' ")[0];

                                
            $ITEMID_REF     =   isset($HDR->ITEMID_REF)?$HDR->ITEMID_REF:NULL;
			
            $MAT            =   DB::select("SELECT 
                                T1.*,
                                T2.QCPID,T2.QCP_CODE,T2.QCP_DESC,
                                CONCAT(T3.RR_CODE,'-',T3.RR_DESC) AS RR_CODE_DES,
                                (T4.UOMCODE) AS UOM_CODE_DES,
                                (T5.INSTRUMENT_METHOD_NAME) AS INTMNT_CODE_DES,
                                (
                                SELECT DISTINCT T5.STANDARDVALUE_TYPE
                                FROM TBL_MST_QIC_HDR T4
                                INNER JOIN TBL_MST_QIC_MAT T5 ON T4.QICID=T5.QICID_REF AND T5.QCPID_REF=T1.QCPID_REF
                                WHERE T4.ITEMID_REF='$ITEMID_REF' AND T4.CYID_REF='$CYID_REF' AND T4.BRID_REF='$BRID_REF' AND T4.STATUS='A' AND (T4.DEACTIVATED=0 or T4.DEACTIVATED is null) 
                                ) AS STANDARDVALUE_TYPE
                                FROM TBL_TRN_QIG_MAT T1
                                LEFT JOIN TBL_MST_QCP T2 ON T1.QCPID_REF=T2.QCPID
                                LEFT JOIN TBL_MST_REJECTION_REASON T3 ON T1.RRID_REF=T3.RRID
                                LEFT JOIN TBL_MST_UOM T4 ON T1.UOMID_REF=T4.UOMID
                                LEFT JOIN TBL_MST_INSTRUMENT_METHOD T5 ON T1.INSTRUMENT_METHOD_ID_REF=T5.INSTRUMENT_METHOD_ID
                                WHERE T1.QIGID_REF='$id' ORDER BY T1.QIGID_REF ASC");
                                
                               
            $ObjUnionUDF    =   DB::table("TBL_MST_UDFFOR_QI")->select('*')
                                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF){       
                                        $query->select('UDFQIID')->from('TBL_MST_UDFFOR_QI')
                                        ->where('STATUS','=','A')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);                                  
                                        }
                                    )
                                    ->where('DEACTIVATED','=',0)
                                    ->where('STATUS','<>','C')                    
                                    ->where('CYID_REF','=',$CYID_REF);
                                        
                $objUdf         =   DB::table('TBL_MST_UDFFOR_QI')
                                    ->where('STATUS','=','A')
                                    ->where('PARENTID','=',0)
                                    ->where('DEACTIVATED','=',0)
                                    ->where('CYID_REF','=',$CYID_REF)
                                    ->union($ObjUnionUDF)
                                    ->get()->toArray();  

                $objCountUDF    =   count($objUdf);
            
            
                $objtempUdf     =   $objUdf;
                foreach ($objtempUdf as $index => $udfvalue) {

                    $objSavedUDF =  DB::table('TBL_TRN_QIG_UDF')
                                    ->where('QIGID_REF','=',$id)
                                    ->where('UDFQIID_REF','=',$udfvalue->UDFQIID)
                                    ->select('VALUE')
                                    ->get()->toArray();

                    if(!empty($objSavedUDF)){
                        $objUdf[$index]->UDF_VALUE = $objSavedUDF[0]->VALUE;
                    }
                    else{
                        $objUdf[$index]->UDF_VALUE = NULL;
                    }
                }

                $objtempUdf = []; 
                        
            $FormId  = $this->form_id;

            $AlpsStatus =   $this->AlpsStatus();
            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view($this->view.'view', compact([
                'FormId','objUdf','objCountUDF','objlastdt','objUserList','objGRNList',
                'objStoreList','objRights','HDR','MAT','AlpsStatus','TabSetting'
            ]));
        
        }
    }

    public function update(Request $request){

        $r_count1 = $request['SELECT_QCP'];
        $r_count3 = $request['Row_Count3'];
  
        $req_data=array();
        foreach($r_count1 as $key=>$i){

            if(isset($request['QCPID_REF_'.$i])){

                $req_data[$i] = [
                    'QCPID_REF'         => $request['QCPID_REF_'.$i] !=""?$request['QCPID_REF_'.$i]:NULL,
                    'STD_VALUE'         => $request['STD_VALUE_'.$i] !=""?$request['STD_VALUE_'.$i]:NULL,
                    'OBS_VALUE1'        => $request['OBS_VALUE1_'.$i] !=""?$request['OBS_VALUE1_'.$i]:0,
                    'OBS_VALUE2'        => $request['OBS_VALUE2_'.$i] !=""?$request['OBS_VALUE2_'.$i]:0,
                    'OBS_VALUE3'        => $request['OBS_VALUE3_'.$i] !=""?$request['OBS_VALUE3_'.$i]:0,
                    'OBS_VALUE4'        => $request['OBS_VALUE4_'.$i] !=""?$request['OBS_VALUE4_'.$i]:0,
                    'OBS_VALUE5'        => $request['OBS_VALUE5_'.$i] !=""?$request['OBS_VALUE5_'.$i]:0,
                    'OBS_VALUE6'        => $request['OBS_VALUE6_'.$i] !=""?$request['OBS_VALUE6_'.$i]:0,
                    'OBS_VALUE7'        => $request['OBS_VALUE7_'.$i] !=""?$request['OBS_VALUE7_'.$i]:0,
                    'OBS_VALUE8'        => $request['OBS_VALUE8_'.$i] !=""?$request['OBS_VALUE8_'.$i]:0,
                    'OBS_VALUE9'        => $request['OBS_VALUE9_'.$i] !=""?$request['OBS_VALUE9_'.$i]:0,
                    'OBS_VALUE10'       => $request['OBS_VALUE10_'.$i] !=""?$request['OBS_VALUE10_'.$i]:0,
                    'AVG_OBS_VALUE'     => $request['AVG_OBS_VALUE_'.$i] !=""?$request['AVG_OBS_VALUE_'.$i]:NULL,
                    'REJECTED'          => $request['REJECTED_'.$i] =="Yes"?1:0,
                    'RRID_REF'          => $request['RRID_REF_'.$i] !=""?$request['RRID_REF_'.$i]:NULL,
                    'UOMID_REF'          => $request['UOMID_REF_'.$i] !=""?$request['UOMID_REF_'.$i]:NULL,
                    'INSTRUMENT_METHOD_ID_REF'          => $request['INTMNTID_REF_'.$i] !=""?$request['INTMNTID_REF_'.$i]:NULL,
                    'REJECTION_REMARKS' => $request['REJECTION_REMARKS_'.$i] !=""?$request['REJECTION_REMARKS_'.$i]:NULL
                ];

            }
        }

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);


        $udffield_Data = [];      
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFQIID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
            }
            
        }

        if(count($udffield_Data)>0){
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
        }else{
            $XMLUDF = NULL;
        }
        
       

        $VTID_REF     =   $this->vtid_ref;
        
        $USERID_REF = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $QIGNO                  =   $request['QIGNO'] !=""?$request['QIGNO']:NULL;
        $QIGDT                  =   $request['QIGDT'] !=""?$request['QIGDT']:NULL;
        $QC_PROCESS_BY          =   $request['QC_PROCESS_BY'] !=""?$request['QC_PROCESS_BY']:NULL;
        $EXT_LABNAME            =   $request['EXT_LABNAME'] !=""?$request['EXT_LABNAME']:NULL;

        $REF_DOCNO              =   $request['REF_DOCNO'] !=""?$request['REF_DOCNO']:NULL;
        $GRNID_REF              =   $request['GRNID_REF'] !=""?$request['GRNID_REF']:NULL;
        $ITEMID_REF             =   $request['ITEMID_REF'] !=""?$request['ITEMID_REF']:NULL;
        $GRN_REC_QTY            =   $request['GRN_REC_QTY'] !=""?$request['GRN_REC_QTY']:0;
        $UOMID_REF              =   $request['UOMID_REF'] !=""?$request['UOMID_REF']:NULL;

        $REMARKS                =   $request['REMARKS'] !=""?$request['REMARKS']:NULL;
        $QI_PICK_QTY            =   $request['QI_PICK_QTY'] !=""?$request['QI_PICK_QTY']:0;
        $REJECTED_QTY           =   $request['REJECTED_QTY'] !=""?$request['REJECTED_QTY']:0;
        $QC_OK_QTY              =   $request['QC_OK_QTY'] !=""?$request['QC_OK_QTY']:0;

        $GEID_REF               =   $request['GEID_REF'] !=""?$request['GEID_REF']:NULL;
        $POID_REF               =   $request['POID_REF'] !=""?$request['POID_REF']:NULL;

        $MRSID_REF              =   $request['MRSID_REF'] !=""?$request['MRSID_REF']:NULL;
        $PIID_REF               =   $request['PIID_REF'] !=""?$request['PIID_REF']:NULL;
        $RFQID_REF              =   $request['RFQID_REF'] !=""?$request['RFQID_REF']:NULL;
        $VQID_REF               =   $request['VQID_REF'] !=""?$request['VQID_REF']:NULL;
        $IPOID_REF              =   $request['IPOID_REF'] !=""?$request['IPOID_REF']:NULL;
        $BPOID_REF              =   $request['BPOID_REF'] !=""?$request['BPOID_REF']:NULL;

        $PENDING_QC_QTY         =   $request['PENDING_QC_QTY'] !=""?$request['PENDING_QC_QTY']:0;
        $REJECTED_STID_REF      =   $request['REJECTED_STID_REF'] !=""?$request['REJECTED_STID_REF']:NULL;
        $QC_OK_STID_REF         =   $request['QC_OK_STID_REF'] !=""?$request['QC_OK_STID_REF']:NULL;
        $PENDING_QC_STID_REF    =   $request['PENDING_QC_STID_REF'] !=""?$request['PENDING_QC_STID_REF']:NULL;
        $QC_ACCEPTED_AS         =   $request['QC_ACCEPTED_AS'] !=""?$request['QC_ACCEPTED_AS']:NULL;
        
        $log_data = [ 
            $QIGNO,                 $QIGDT,         $QC_PROCESS_BY,     $EXT_LABNAME,       $REF_DOCNO,
            $GRNID_REF,             $ITEMID_REF,    $GRN_REC_QTY,       $UOMID_REF,         $REMARKS,               
            $QI_PICK_QTY,           $REJECTED_QTY,  $QC_OK_QTY,         $CYID_REF,          $BRID_REF,              
            $FYID_REF,              $VTID_REF,      $GEID_REF,          $POID_REF,          $MRSID_REF,             
            $PIID_REF,              $RFQID_REF,     $VQID_REF,          $IPOID_REF,         $BPOID_REF,
            $XMLMAT,                $XMLUDF,        $USERID_REF,        Date('Y-m-d'),      Date('h:i:s.u'),    
            $ACTIONNAME,            $IPADDRESS,     $PENDING_QC_QTY,    $REJECTED_STID_REF, $QC_OK_STID_REF,
            $PENDING_QC_STID_REF,   $QC_ACCEPTED_AS    
        ];

        $sp_result = DB::select('EXEC SP_QIG_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data); 
         
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $QIGNO. ' Sucessfully Updated.']);

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
           
        $r_count1 = $request['SELECT_QCP'];
        $r_count3 = $request['Row_Count3'];
  
        $req_data=array();
        foreach($r_count1 as $key=>$i){

            if(isset($request['QCPID_REF_'.$i])){

                $req_data[$i] = [
                    'QCPID_REF'         => $request['QCPID_REF_'.$i] !=""?$request['QCPID_REF_'.$i]:NULL,
                    'STD_VALUE'         => $request['STD_VALUE_'.$i] !=""?$request['STD_VALUE_'.$i]:NULL,
                    'OBS_VALUE1'        => $request['OBS_VALUE1_'.$i] !=""?$request['OBS_VALUE1_'.$i]:0,
                    'OBS_VALUE2'        => $request['OBS_VALUE2_'.$i] !=""?$request['OBS_VALUE2_'.$i]:0,
                    'OBS_VALUE3'        => $request['OBS_VALUE3_'.$i] !=""?$request['OBS_VALUE3_'.$i]:0,
                    'OBS_VALUE4'        => $request['OBS_VALUE4_'.$i] !=""?$request['OBS_VALUE4_'.$i]:0,
                    'OBS_VALUE5'        => $request['OBS_VALUE5_'.$i] !=""?$request['OBS_VALUE5_'.$i]:0,
                    'OBS_VALUE6'        => $request['OBS_VALUE6_'.$i] !=""?$request['OBS_VALUE6_'.$i]:0,
                    'OBS_VALUE7'        => $request['OBS_VALUE7_'.$i] !=""?$request['OBS_VALUE7_'.$i]:0,
                    'OBS_VALUE8'        => $request['OBS_VALUE8_'.$i] !=""?$request['OBS_VALUE8_'.$i]:0,
                    'OBS_VALUE9'        => $request['OBS_VALUE9_'.$i] !=""?$request['OBS_VALUE9_'.$i]:0,
                    'OBS_VALUE10'       => $request['OBS_VALUE10_'.$i] !=""?$request['OBS_VALUE10_'.$i]:0,
                    'AVG_OBS_VALUE'     => $request['AVG_OBS_VALUE_'.$i] !=""?$request['AVG_OBS_VALUE_'.$i]:NULL,
                    'REJECTED'          => $request['REJECTED_'.$i] =="Yes"?1:0,
                    'RRID_REF'          => $request['RRID_REF_'.$i] !=""?$request['RRID_REF_'.$i]:NULL,
                    'UOMID_REF'          => $request['UOMID_REF_'.$i] !=""?$request['UOMID_REF_'.$i]:NULL,
                    'INSTRUMENT_METHOD_ID_REF'          => $request['INTMNTID_REF_'.$i] !=""?$request['INTMNTID_REF_'.$i]:NULL,
                    'REJECTION_REMARKS' => $request['REJECTION_REMARKS_'.$i] !=""?$request['REJECTION_REMARKS_'.$i]:NULL
                ];

            }
        }


        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);


        $udffield_Data = [];      
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFQIID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
            }
            
        }

        if(count($udffield_Data)>0){
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
        }else{
            $XMLUDF = NULL;
        }
        
        

        $VTID_REF     =   $this->vtid_ref;
        
        $USERID_REF = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');


        $QIGNO                  =   $request['QIGNO'] !=""?$request['QIGNO']:NULL;
        $QIGDT                  =   $request['QIGDT'] !=""?$request['QIGDT']:NULL;
        $QC_PROCESS_BY          =   $request['QC_PROCESS_BY'] !=""?$request['QC_PROCESS_BY']:NULL;
        $EXT_LABNAME            =   $request['EXT_LABNAME'] !=""?$request['EXT_LABNAME']:NULL;

        $REF_DOCNO              =   $request['REF_DOCNO'] !=""?$request['REF_DOCNO']:NULL;
        $GRNID_REF              =   $request['GRNID_REF'] !=""?$request['GRNID_REF']:NULL;
        $ITEMID_REF             =   $request['ITEMID_REF'] !=""?$request['ITEMID_REF']:NULL;
        $GRN_REC_QTY            =   $request['GRN_REC_QTY'] !=""?$request['GRN_REC_QTY']:0;
        $UOMID_REF              =   $request['UOMID_REF'] !=""?$request['UOMID_REF']:NULL;

        $REMARKS                =   $request['REMARKS'] !=""?$request['REMARKS']:NULL;
        $QI_PICK_QTY            =   $request['QI_PICK_QTY'] !=""?$request['QI_PICK_QTY']:0;
        $REJECTED_QTY           =   $request['REJECTED_QTY'] !=""?$request['REJECTED_QTY']:0;
        $QC_OK_QTY              =   $request['QC_OK_QTY'] !=""?$request['QC_OK_QTY']:0;

        $GEID_REF               =   $request['GEID_REF'] !=""?$request['GEID_REF']:NULL;
        $POID_REF               =   $request['POID_REF'] !=""?$request['POID_REF']:NULL;

        $MRSID_REF              =   $request['MRSID_REF'] !=""?$request['MRSID_REF']:NULL;
        $PIID_REF               =   $request['PIID_REF'] !=""?$request['PIID_REF']:NULL;
        $RFQID_REF              =   $request['RFQID_REF'] !=""?$request['RFQID_REF']:NULL;
        $VQID_REF               =   $request['VQID_REF'] !=""?$request['VQID_REF']:NULL;
        $IPOID_REF              =   $request['IPOID_REF'] !=""?$request['IPOID_REF']:NULL;
        $BPOID_REF              =   $request['BPOID_REF'] !=""?$request['BPOID_REF']:NULL;

        $PENDING_QC_QTY         =   $request['PENDING_QC_QTY'] !=""?$request['PENDING_QC_QTY']:0;
        $REJECTED_STID_REF      =   $request['REJECTED_STID_REF'] !=""?$request['REJECTED_STID_REF']:NULL;
        $QC_OK_STID_REF         =   $request['QC_OK_STID_REF'] !=""?$request['QC_OK_STID_REF']:NULL;
        $PENDING_QC_STID_REF    =   $request['PENDING_QC_STID_REF'] !=""?$request['PENDING_QC_STID_REF']:NULL;
        $QC_ACCEPTED_AS         =   $request['QC_ACCEPTED_AS'] !=""?$request['QC_ACCEPTED_AS']:NULL;
        
        $log_data = [ 
            $QIGNO,                 $QIGDT,         $QC_PROCESS_BY,     $EXT_LABNAME,       $REF_DOCNO,
            $GRNID_REF,             $ITEMID_REF,    $GRN_REC_QTY,       $UOMID_REF,         $REMARKS,               
            $QI_PICK_QTY,           $REJECTED_QTY,  $QC_OK_QTY,         $CYID_REF,          $BRID_REF,              
            $FYID_REF,              $VTID_REF,      $GEID_REF,          $POID_REF,          $MRSID_REF,             
            $PIID_REF,              $RFQID_REF,     $VQID_REF,          $IPOID_REF,         $BPOID_REF,
            $XMLMAT,                $XMLUDF,        $USERID_REF,        Date('Y-m-d'),      Date('h:i:s.u'),    
            $ACTIONNAME,            $IPADDRESS,     $PENDING_QC_QTY,    $REJECTED_STID_REF, $QC_OK_STID_REF,
            $PENDING_QC_STID_REF,    $QC_ACCEPTED_AS
        ];

        $sp_result = DB::select('EXEC SP_QIG_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data); 
         
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $QIGNO. ' Sucessfully Approved.']);

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
                $TABLE      =   "TBL_TRN_QIG_HDR";
                $FIELD      =   "QIGID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_QIG ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
    
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
        $TABLE      =   "TBL_TRN_QIG_HDR";
        $FIELD      =   "QIGID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_QIG_MAT',
        ];

        $req_data[1]=[
            'NT'  => 'TBL_TRN_QIG_UDF',
        ];
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $qualityinspection_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_QIG  ?,?,?,?, ?,?,?,?, ?,?,?,?', $qualityinspection_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_QIG_HDR')->where('QIGID','=',$id)->first();

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
        
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/QualityInspectionGRN";
		
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
  
    
    public function checkExist(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $QIGNO      =   $request->QIGNO;
        
        $objExit    =   DB::table('TBL_TRN_QIG_HDR')
                        ->where('TBL_TRN_QIG_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_TRN_QIG_HDR.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_TRN_QIG_HDR.FYID_REF','=',Session::get('FYID_REF'))
                        ->where('TBL_TRN_QIG_HDR.QIGNO','=',$QIGNO)
                        ->select('TBL_TRN_QIG_HDR.QIGNO')
                        ->first();
        
        if($objExit){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate Quality Inspection NO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }




    
    public function getItemDetails(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $Status     =   $request['status'];
        $GRNID_REF  =   $request['GRNID_REF'];

        $CheckItem  =   DB::select("SELECT ITEMID_REF, GEID_REF,POID_REF,IPOID_REF,BPOID_REF
                        FROM TBL_TRN_QIG_HDR 
                        WHERE CYID_REF = $CYID_REF AND  BRID_REF = $BRID_REF AND GRNID_REF = $GRNID_REF AND STATUS='A' 
						GROUP BY ITEMID_REF, GEID_REF,POID_REF,IPOID_REF,BPOID_REF
                        ");
        $ExistItem=[];
        foreach($CheckItem as $val){
            $ExistItem[]=$val->ITEMID_REF;
        }


        $StdCost    =   0;

        $ObjItem =  DB::select("SELECT 
        T1.MAIN_UOMID_REF AS MAIN_UOMID_REF,
        T1.RECEIVED_QTY_MU AS ORDER_QTY,
        T1.RECEIVED_QTY_MU AS Qty,
        T1.GRNID_REF,
        T1.POID_REF,
        T1.MRSID_REF,
        T1.PIID_REF,
        T1.RFQID_REF,
        T1.VQID_REF,
        T1.IPOID_REF,
        T1.BPOID_REF,
        T2.ITEMID,
        T2.ICODE,
        T2.NAME,
        T2.ITEMGID_REF,
        T2.ICID_REF,
        T2.ITEM_SPECI,
        T1.GEID_REF

        FROM TBL_TRN_IGRN02_MAT T1
        LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
        WHERE T1.GRNID_REF='$GRNID_REF' AND T1.GEID_REF+'-'+ISNULL(T1.POID_REF,'0')+'-'+ISNULL(T1.IPOID_REF,'0')+'-'+ISNULL(T1.BPOID_REF,'0')+'-'+T2.ITEMID 
		NOT IN (SELECT T3.GEID_REF+'-'+ISNULL(T3.POID_REF,'0')+'-'+ISNULL(T3.IPOID_REF,'0')+'-'+ISNULL(T3.BPOID_REF,'0')+'-'+T3.ITEMID_REF FROM TBL_TRN_QIG_HDR T3 WHERE T3.GRNID_REF = '$GRNID_REF')
        ");       

        if($ObjItem){

            foreach ($ObjItem as $index=>$dataRow){

              /* if(!in_array($dataRow->ITEMID,$ExistItem)){ */
               

                $SOQTY      =   isset($dataRow->ORDER_QTY)? $dataRow->ORDER_QTY : 0;   
                $FROMQTY    =   isset($dataRow->Qty)? $dataRow->Qty : 0;   
                     
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

                $item_unique_row_id =   $dataRow->GRNID_REF."_".$dataRow->GEID_REF."_".$dataRow->POID_REF."_".$dataRow->MRSID_REF."_".$dataRow->PIID_REF."_".$dataRow->RFQID_REF."_".$dataRow->VQID_REF."_".$dataRow->IPOID_REF."_".$dataRow->BPOID_REF."_".$dataRow->ITEMID;
               
                $row = '';

                $row = $row.'
                <tr id="item_'.$index.'"  class="clsitemid">
                    <td style="width:8%;text-align:center;"><input type="checkbox" id="chkId'.$index.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>
                    <td style="width:10%;">'.$dataRow->ICODE.'</td>
                    <td style="width:10%;">'.$dataRow->NAME.'</td>
                    <td style="width:8%;">'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>
                    <td style="width:8%;">'.$FROMQTY.'</td>
                    <td style="width:8%;">'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>
                    <td style="width:8%;">'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                    <td style="width:8%;">'.$BusinessUnit.'</td>
                    <td style="width:8%;">'.$ALPS_PART_NO.'</td>
                    <td style="width:8%;">'.$CUSTOMER_PART_NO.'</td>
                    <td style="width:8%;">'.$OEM_PART_NO.'</td>
                    <td style="width:8%;">Authorized</td>
                    <td hidden>
                        <input type="text" id="txtitem_'.$index.'" 
                            data-desc1="'.$dataRow->ITEMID.'" 
                            data-desc2="'.$dataRow->ICODE.'" 
                            data-desc3="'.$dataRow->NAME.'" 
                            data-desc4="'.$dataRow->MAIN_UOMID_REF.'" 
                            data-desc5="'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'" 
                            data-desc6="'.$FROMQTY.'" 
                            data-desc7="'.$item_unique_row_id.'" 
                            data-desc8="'.$dataRow->POID_REF.'"
                            data-desc9="'.$dataRow->MRSID_REF.'"
                            data-desc10="'.$dataRow->PIID_REF.'"
                            data-desc11="'.$dataRow->RFQID_REF.'"
                            data-desc12="'.$SOQTY.'"
                            data-desc13="'.$dataRow->VQID_REF.'"
                            data-desc14="'.$dataRow->ORDER_QTY.'"
                            data-desc15="'.$dataRow->GEID_REF.'"
                            data-desc16="'.$dataRow->IPOID_REF.'"
                            data-desc17="'.$dataRow->BPOID_REF.'"
                        />
                    </td>
                </tr>';
                echo $row; 
           /*  }   */               
                
			}         
		}           
        else{
            echo '<tr><td> Record not found.</td></tr>';
        }
        exit();
    }
        
    public function getDocNo(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $ITEMID_REF     =   $request['id'];
        $fieldid        =   $request['fieldid'];

        $ObjData        =   DB::select("SELECT 
        T3.QCPID,T3.QCP_CODE,T3.QCP_DESC,T2.STANDARDVALUE_TYPE,
        T2.STANDARD_VALUE,
        T4.UOMCODE,T4.UOMID,
        T5.INSTRUMENT_METHOD_NAME,T5.INSTRUMENT_METHOD_ID
        FROM TBL_MST_QIC_HDR T1
        INNER JOIN TBL_MST_QIC_MAT T2 ON T1.QICID=T2.QICID_REF
        INNER JOIN TBL_MST_QCP T3 ON T2.QCPID_REF=T3.QCPID
        LEFT JOIN TBL_MST_UOM T4 ON T4.UOMID=T2.UOMID_REF
        LEFT JOIN TBL_MST_INSTRUMENT_METHOD T5 ON T2.INSTRUMENT_METHOD_ID_REF=T5.INSTRUMENT_METHOD_ID
        WHERE T1.ITEMID_REF='$ITEMID_REF' AND T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' 
		AND T1.STATUS='A' AND (T1.DEACTIVATED=0 or T1.DEACTIVATED is null)"); 

        $Row_Count1 =   isset($ObjData)?count($ObjData):0;

        echo '<thead id="thead1"  style="position: sticky;top: 0">                           
                <tr>
                    <th>Select</th>
                    <th>QC Parameter Code</th>
                    <th>QC Parameter Description</th>
                    <th>Unit Of Measurement (UOM)</th>
                    <th>Instrument Method</th>
                    <th>Standard Value</th>
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>6</th>
                    <th>7</th>
                    <th>8</th>
                    <th>9</th>
                    <th>10</th>
                    <th>Average Total</th>
                    <th>Rejected  Yes / No</th>
                    <th>Rejection Reason</th>
                    <th>Rejection Remarks</th>
                    <th hidden><input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="'.$Row_Count1.'"></th>
                </tr>
                </thead>';
               
        echo '<tbody>';
        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){

                $OBS_VALUE_READONLY     =   $dataRow->STANDARDVALUE_TYPE=="Text" || $dataRow->STANDARDVALUE_TYPE=="Logical"?"readonlys":'';
                $AVG_OBS_VALUE_READONLY =   $dataRow->STANDARDVALUE_TYPE=="Numeric Value" || $dataRow->STANDARDVALUE_TYPE=="Range In Value" || $dataRow->STANDARDVALUE_TYPE=="Range Percent"?"readonly":'';
                $STANDARD_VALUE         =   $dataRow->STANDARDVALUE_TYPE=="Range In Value" || $dataRow->STANDARDVALUE_TYPE=="Range Percent"?str_replace(",", "-",$dataRow->STANDARD_VALUE):$dataRow->STANDARD_VALUE;

                if($dataRow->STANDARDVALUE_TYPE =="Numeric Value"){
                    $STANDARD_TYPE="(In Numeric)";
                    $TEXT_VALIDATE='onkeypress="return isNumberDecimalKey(event,this)"';
                }
                else if($dataRow->STANDARDVALUE_TYPE =="Range In Value"){
                    $STANDARD_TYPE="(In Numeric)";
                    $TEXT_VALIDATE='';
                }
                else if($dataRow->STANDARDVALUE_TYPE =="Range Percent"){
                    $STANDARD_TYPE="(In %)";
                    $TEXT_VALIDATE='';
                }
                else if($dataRow->STANDARDVALUE_TYPE =="Logical"){
                    $STANDARD_TYPE="(In Logical)";
                    $TEXT_VALIDATE='';
                } 
                else if($dataRow->STANDARDVALUE_TYPE =="Text"){
                    $STANDARD_TYPE="(In Text)";
                    $TEXT_VALIDATE='';
                } 

                echo '<tr  class="participantRow">
                    <td><input type="checkbox" name="SELECT_QCP[]"  id="SELECT_QCP_'.$index.'" checked class="clssQCPID" value="'.$index.'" ></td>
                    <td><input type="text" name="txtQCPID_popup_'.$index.'"   id="txtQCPID_popup_'.$index.'" value="'.$dataRow->QCP_CODE.'" class="form-control" autocomplete="off" readonly/></td>
                    <td hidden><input type="text" name="QCPID_REF_'.$index.'" id="QCPID_REF_'.$index.'"    value="'.$dataRow->QCPID.'"  class="form-control" autocomplete="off" /></td>
                    
                    <td><input type="text" name="QCP_DES_'.$index.'" id="QCP_DES_'.$index.'" value="'.$dataRow->QCP_DESC.'" class="form-control"  autocomplete="off" readonly /></td>
                    <td hidden><input type="text" name="STD_TYPE_'.$index.'" id="STD_TYPE_'.$index.'" value="'.$dataRow->STANDARDVALUE_TYPE.'" class="form-control"  autocomplete="off" readonly style="width:100px;" /></td>
                                        
                    <td><input type="text" name="txtUOMID_popup_'.$index.'"   id="txtUOMID_popup_'.$index.'" value="'.$dataRow->UOMCODE.'" class="form-control" autocomplete="off" readonly style="width:160px;" /></td>
                    <td hidden><input type="text" name="UOMID_REF_'.$index.'" id="UOMID_REF_'.$index.'"      value="'.$dataRow->UOMID.'" class="form-control" autocomplete="off" /></td>
                      
                    <td><input type="text" name="txtINTMNTID_popup_'.$index.'"   id="txtINTMNTID_popup_'.$index.'" value="'.$dataRow->INSTRUMENT_METHOD_NAME.'" class="form-control" autocomplete="off" readonly style="width:107px;" /></td>
                    <td hidden><input type="text" name="INTMNTID_REF_'.$index.'" id="INTMNTID_REF_'.$index.'"      value="'.$dataRow->INSTRUMENT_METHOD_ID.'"    class="form-control" autocomplete="off" /></td>
                                                                               
                    <td><input type="text" name="STD_VALUE_'.$index.'" id="STD_VALUE_'.$index.'" value="'.$STANDARD_VALUE.'" class="form-control"  autocomplete="off" readonly style="width:100px;"  /> <div style="width:170px !important;"> <span style="margin-left:2px">'.$STANDARD_TYPE.' </span></div> </td>

                    <td><input type="text" name="OBS_VALUE1_'.$index.'" id="OBS_VALUE1_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" '.$TEXT_VALIDATE.' /></td>
                    <td><input type="text" name="OBS_VALUE2_'.$index.'" id="OBS_VALUE2_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" '.$TEXT_VALIDATE.' /></td>
                    <td><input type="text" name="OBS_VALUE3_'.$index.'" id="OBS_VALUE3_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" '.$TEXT_VALIDATE.' /></td>
                    <td><input type="text" name="OBS_VALUE4_'.$index.'" id="OBS_VALUE4_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" '.$TEXT_VALIDATE.' /></td>
                    <td><input type="text" name="OBS_VALUE5_'.$index.'" id="OBS_VALUE5_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" '.$TEXT_VALIDATE.' /></td>
                    <td><input type="text" name="OBS_VALUE6_'.$index.'" id="OBS_VALUE6_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" '.$TEXT_VALIDATE.' /></td>
                    <td><input type="text" name="OBS_VALUE7_'.$index.'" id="OBS_VALUE7_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" '.$TEXT_VALIDATE.' /></td>
                    <td><input type="text" name="OBS_VALUE8_'.$index.'" id="OBS_VALUE8_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" '.$TEXT_VALIDATE.' /></td>
                    <td><input type="text" name="OBS_VALUE9_'.$index.'" id="OBS_VALUE9_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" '.$TEXT_VALIDATE.' /></td>
                    <td><input type="text" name="OBS_VALUE10_'.$index.'" id="OBS_VALUE10_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" '.$TEXT_VALIDATE.' /></td>

                    <td><input type="text" name="AVG_OBS_VALUE_'.$index.'" id="AVG_OBS_VALUE_'.$index.'" '.$AVG_OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" onkeyup="AverageTotal(this.id)" /></td>
                    <td>

                    <select name="REJECTED_'.$index.'" id="REJECTED_'.$index.'" class="form-control">
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                  </select>                   
                    
                    
                    </td>

                    <td><input type="text" name="txtRRID_popup_'.$index.'"   id="txtRRID_popup_'.$index.'" class="form-control" autocomplete="off" readonly/></td>
                    <td hidden><input type="text" name="RRID_REF_'.$index.'" id="RRID_REF_'.$index.'"      class="form-control" autocomplete="off" /></td>
                    
                    <td><input type="text" name="REJECTION_REMARKS_'.$index.'" id="REJECTION_REMARKS_'.$index.'" class="form-control"  autocomplete="off" /></td>
                </tr>';
                
            }

        }
        else{
            echo'<tr  class="participantRow">
                <td><input type="checkbox" name="SELECT_QCP[]"  id="SELECT_QCP_0" class="clssQCPID"  ></td>
                <td><input type="text" name="txtQCPID_popup_0"   id="txtQCPID_popup_0" class="form-control" autocomplete="off" readonly/></td>
                <td hidden><input type="text" name="QCPID_REF_0" id="QCPID_REF_0"      class="form-control" autocomplete="off" /></td>
                
                <td><input type="text" name="QCP_DES_0" id="QCP_DES_0" class="form-control"  autocomplete="off" readonly /></td>
                <td hidden><input type="text" name="STD_TYPE_0" id="STD_TYPE_0" class="form-control"  autocomplete="off" readonly style="width:100px;" /></td>
                
                <td><input type="text" name="txtUOMID_popup_0"   id="txtUOMID_popup_0" class="form-control" autocomplete="off" readonly style="width:161px;" /></td>
                <td hidden><input type="text" name="UOMID_REF_0" id="UOMID_REF_0"      class="form-control" autocomplete="off" /></td>
                  
                <td><input type="text" name="txtINTMNTID_popup_0"   id="txtINTMNTID_popup_0" class="form-control" autocomplete="off" readonly style="width:107px;" /></td>
                <td hidden><input type="text" name="INTMNTID_REF_0" id="INTMNTID_REF_0"      class="form-control" autocomplete="off" /></td>
                
                <td><input type="text" name="STD_VALUE_0" id="STD_VALUE_0" class="form-control"  autocomplete="off" readonly /></td>

                <td><input type="text" name="OBS_VALUE1_0" id="OBS_VALUE1_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                <td><input type="text" name="OBS_VALUE2_0" id="OBS_VALUE2_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                <td><input type="text" name="OBS_VALUE3_0" id="OBS_VALUE3_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                <td><input type="text" name="OBS_VALUE4_0" id="OBS_VALUE4_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                <td><input type="text" name="OBS_VALUE5_0" id="OBS_VALUE5_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                <td><input type="text" name="OBS_VALUE6_0" id="OBS_VALUE6_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                <td><input type="text" name="OBS_VALUE7_0" id="OBS_VALUE7_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                <td><input type="text" name="OBS_VALUE8_0" id="OBS_VALUE8_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                <td><input type="text" name="OBS_VALUE9_0" id="OBS_VALUE9_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                <td><input type="text" name="OBS_VALUE10_0" id="OBS_VALUE10_0" class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>

                <td><input type="text" name="AVG_OBS_VALUE_0" id="AVG_OBS_VALUE_0" class="form-control"  autocomplete="off" onkeyup="AverageTotal(this.id)" /></td>
                <td><input type="text" name="REJECTED_0" id="REJECTED_0" class="form-control"  autocomplete="off" readonly ></td>

                <td><input type="text" name="txtRRID_popup_0"   id="txtRRID_popup_0" class="form-control" autocomplete="off" readonly/></td>
                <td hidden><input type="text" name="RRID_REF_0" id="RRID_REF_0"      class="form-control" autocomplete="off" /></td>
                
                <td><input type="text" name="REJECTION_REMARKS_0" id="REJECTION_REMARKS_0" class="form-control"  autocomplete="off" /></td>

          </tr>';
        }

        echo '</tbody>';

        
        exit();   
    }

    public function getRRNo(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $fieldid        =   $request['fieldid'];

        $ObjData        =   DB::select("SELECT RRID,RR_CODE,RR_DESC FROM TBL_MST_REJECTION_REASON 
        where CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND (DEACTIVATED=0 or DEACTIVATED is null)"); 

        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){

                $row            =   '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="socode_'.$dataRow->RRID .'"  class="clssRRID" value="'.$dataRow->RRID.'" ></td>
                <td class="ROW2">'.$dataRow->RR_CODE;
                $row = $row.'<input type="hidden" id="txtsocode_'.$dataRow->RRID.'" data-desc="'.$dataRow->RR_CODE.'-'.$dataRow->RR_DESC.'" value="'.$dataRow->RRID.'"/></td>
                <td class="ROW3" >'.$dataRow->RR_DESC.'</td></tr>';
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
        where CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND (DEACTIVATED=0 or DEACTIVATED is null)"); 

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
        where CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF'  AND STATUS='A' AND (DEACTIVATED=0 or DEACTIVATED is null)"); 

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


    
    public function getUserList(){
        return DB::table('TBL_MST_EMPLOYEE')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('EMPID','EMPCODE','FNAME','MNAME','LNAME')
            ->get();
    }


    public function getGRNList(){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $data = DB::select("SELECT 
        T1.GRNID AS DOC_ID,
        GRN_NO AS DOC_CODE,
        T1.GRN_DT AS DOC_DESC
        FROM TBL_TRN_IGRN02_HDR AS T1
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.STATUS='N'");


            $GRNdata=[]; 

            foreach($data as $val){

                $ItemStatus =   $this->CheckGRNItem($val->DOC_ID);

                if(!empty($ItemStatus)){

                    $GRNdata[] = array(
                        'DOC_ID'=> $val->DOC_ID,
                        'DOC_CODE'=> $val->DOC_CODE, 
                        'DOC_DESC'=> $val->DOC_DESC
                    );  
                }            

            }

        return $GRNdata;
        
    }

    public function CheckGRNItem($GRN_ID){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');       

        $CheckGRN  =   DB::select("SELECT ITEMID_REF,GEID_REF,POID_REF,IPOID_REF,BPOID_REF FROM TBL_TRN_IGRN02_MAT  WHERE  GRNID_REF ='$GRN_ID'");

        $ItemStatus=[]; 

        foreach($CheckGRN as $val1){

            $ITEMID_REF =$val1->ITEMID_REF;
			$GEID_REF =$val1->GEID_REF;
			$POID_REF =$val1->POID_REF;
			$IPOID_REF =$val1->IPOID_REF;
			$BPOID_REF =$val1->BPOID_REF;
            
            $ItemNo =  DB::table('TBL_TRN_QIG_HDR')
                        ->where('CYID_REF','=',$CYID_REF)
                        ->where('BRID_REF','=',$BRID_REF)
                        ->where('GRNID_REF','=',$GRN_ID)
                        ->where('ITEMID_REF','=',$ITEMID_REF)  
						->where('GEID_REF','=',$GEID_REF)
						->where('POID_REF','=',$POID_REF)
						->where('IPOID_REF','=',$IPOID_REF)
						->where('BPOID_REF','=',$BPOID_REF)
                        ->count();  
                
            if($ItemNo ==0){
                $ItemStatus[]=1;
            }

        }
        
        return $ItemStatus;
        
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
    

    
}
