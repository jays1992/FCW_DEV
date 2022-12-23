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

class TrnFrm358Controller extends Controller{

    protected $form_id  = 358;
    protected $vtid_ref = 444;
    protected $view     = "transactions.JobWork.QualityInspection.trnfrm358";
   
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
       
        $CYID_REF       =  Auth::user()->CYID_REF;
        $BRID_REF       =  Session::get('BRID_REF');
        $FYID_REF       =  Session::get('FYID_REF');
		
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

        $objDataList    =	DB::select("SELECT '$USER_LEVEL' AS USER_LEVEL,T1.*,T2.ACTIONNAME,
        (
        SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
        LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
        WHERE  AUD.VID=T1.QIJID AND  AUD.CYID_REF=T1.CYID_REF AND  AUD.BRID_REF=T1.BRID_REF AND  
        AUD.FYID_REF=T1.FYID_REF AND  AUD.VTID_REF=T1.VTID_REF AND AUD.ACTIONNAME='ADD'       
        ) AS CREATED_BY
        FROM TBL_TRN_QIJ_HDR T1
        INNER JOIN TBL_TRN_AUDITTRAIL T2 ON T1.QIJID=T2.VID AND T1.VTID_REF=T2.VTID_REF AND T1.CYID_REF=T2.CYID_REF AND T1.BRID_REF=T2.BRID_REF 
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' AND T2.VTID_REF = '$this->vtid_ref' AND T2.ACTID IN (SELECT max(ACTID) FROM TBL_TRN_AUDITTRAIL A WHERE T2.VTID_REF = A.VTID_REF AND T2.VID = A.VID)
        ORDER BY T1.QIJID DESC 
        ");     

        return view($this->view,compact(['REQUEST_DATA','DATA_STATUS','FormId','objRights','objDataList']));
    }

    public function add(){       
        
        $Status         =   "A";
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $objlastdt      =   $this->getLastdt();
        $objUserList    =   $this->getUserList();
        $objUserList    =   $this->getUserList();
        $objGRNList     =   $this->getGRNList();
        $objStoreList   =   $this->getStoreList();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_QIJ_HDR',
            'HDR_ID'=>'QIJID',
            'HDR_DOC_NO'=>'QIJNO',
            'HDR_DOC_DT'=>'QIJDT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        
        
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_QIJ")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDFQIJID')->from('TBL_MST_UDFFOR_QIJ')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                     
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                
                   

        $objUdf  = DB::table('TBL_MST_UDFFOR_QIJ')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdf); 
                    
        $FormId  = $this->form_id;

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        return view($this->view.'add', compact(
            ['FormId','objUdf','objCountUDF','objlastdt','objUserList','objGRNList','objStoreList','TabSetting','doc_req','docarray']
        ));       
    }

    public function getLastdt(){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        return  DB::select('SELECT MAX(QIJDT) QIJDT FROM TBL_TRN_QIJ_HDR  
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
                $udffield_Data[$i]['UDFQIJID_REF'] = $request['udffie_'.$i]; 
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

        $QIJNO                  =   $request['QIJNO'] !=""?$request['QIJNO']:NULL;
        $QIJDT                  =   $request['QIJDT'] !=""?$request['QIJDT']:NULL;
        $QC_PROCESS_BY          =   $request['QC_PROCESS_BY'] !=""?$request['QC_PROCESS_BY']:NULL;
        $EXT_LABNAME            =   $request['EXT_LABNAME'] !=""?$request['EXT_LABNAME']:NULL;

        $REF_DOCNO              =   $request['REF_DOCNO'] !=""?$request['REF_DOCNO']:NULL;
        $GRJID_REF              =   $request['GRJID_REF'] !=""?$request['GRJID_REF']:NULL;
        $ITEMID_REF             =   $request['ITEMID_REF'] !=""?$request['ITEMID_REF']:NULL;
        $GRJ_REC_QTY            =   $request['GRJ_REC_QTY'] !=""?$request['GRJ_REC_QTY']:0;
        $UOMID_REF              =   $request['UOMID_REF'] !=""?$request['UOMID_REF']:NULL;

        $REMARKS                =   $request['REMARKS'] !=""?$request['REMARKS']:NULL;
        $QI_PICK_QTY            =   $request['QI_PICK_QTY'] !=""?$request['QI_PICK_QTY']:0;
        $REJECTED_QTY           =   $request['REJECTED_QTY'] !=""?$request['REJECTED_QTY']:0;
        $QC_OK_QTY              =   $request['QC_OK_QTY'] !=""?$request['QC_OK_QTY']:0;

        $GEJWOID_REF            =   $request['GEJWOID_REF'] !=""?$request['GEJWOID_REF']:NULL;
        $JWCID_REF              =   $request['JWCID_REF'] !=""?$request['JWCID_REF']:NULL;

        $JWOID_REF              =   $request['JWOID_REF'] !=""?$request['JWOID_REF']:NULL;
        $PROID_REF              =   $request['PROID_REF'] !=""?$request['PROID_REF']:NULL;
        $SOID_REF               =   $request['SOID_REF'] !=""?$request['SOID_REF']:NULL;
        $SQID_REF               =   $request['SQID_REF'] !=""?$request['SQID_REF']:NULL;
        $SEID_REF               =   $request['SEID_REF'] !=""?$request['SEID_REF']:NULL;

        $PENDING_QC_QTY         =   $request['PENDING_QC_QTY'] !=""?$request['PENDING_QC_QTY']:0;
        $REJECTED_STID_REF      =   $request['REJECTED_STID_REF'] !=""?$request['REJECTED_STID_REF']:NULL;
        $QC_OK_STID_REF         =   $request['QC_OK_STID_REF'] !=""?$request['QC_OK_STID_REF']:NULL;
        $PENDING_QC_STID_REF    =   $request['PENDING_QC_STID_REF'] !=""?$request['PENDING_QC_STID_REF']:NULL;
        
        $log_data = [ 
            $QIJNO,                 $QIJDT,         $QC_PROCESS_BY,     $EXT_LABNAME,  
            $REF_DOCNO,             $GRJID_REF,     $ITEMID_REF,        $GRJ_REC_QTY,       $UOMID_REF,  
            $REMARKS,               $QI_PICK_QTY,   $REJECTED_QTY,      $QC_OK_QTY,         $CYID_REF, 
            $BRID_REF,              $FYID_REF,      $VTID_REF,          $GEJWOID_REF,       $JWCID_REF,  
            $JWOID_REF,             $PROID_REF,     $SOID_REF,          $SQID_REF,          $SEID_REF,
            $XMLMAT,                $XMLUDF,        $USERID_REF,        Date('Y-m-d'),      Date('h:i:s.u'),    
            $ACTIONNAME,            $IPADDRESS,     $PENDING_QC_QTY,    $REJECTED_STID_REF, $QC_OK_STID_REF,
            $PENDING_QC_STID_REF    
        ];

        $sp_result = DB::select('EXEC SP_QIJ_IN ?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?', $log_data);     
        
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
                                T5.GRNNO,
                                CONCAT(T6.STCODE,'-',T6.NAME) AS REJECTED_STORE,
                                CONCAT(T7.STCODE,'-',T7.NAME) AS QC_OK_STORE,
                                CONCAT(T8.STCODE,'-',T8.NAME) AS PENDING_QC_STORE
                            
                                FROM TBL_TRN_QIJ_HDR T1
                                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                                LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                                LEFT JOIN TBL_MST_EMPLOYEE T4 ON T1.QC_PROCESS_BY=T4.EMPID
                                LEFT JOIN TBL_TRN_GRJ_HDR T5 ON T1.GRJID_REF=T5.GRJID
                                LEFT JOIN TBL_MST_STORE T6 ON T1.REJECTED_STID_REF=T6.STID
                                LEFT JOIN TBL_MST_STORE T7 ON T1.QC_OK_STID_REF=T7.STID
                                LEFT JOIN TBL_MST_STORE T8 ON T1.PENDING_QC_STID_REF=T8.STID

                                WHERE T1.QIJID='$id' ")[0];
                                
            $ITEMID_REF     =   isset($HDR->ITEMID_REF)?$HDR->ITEMID_REF:NULL;

            $MAT            =   DB::select("SELECT 
                                T1.*,
                                T2.QCPID,T2.QCP_CODE,T2.QCP_DESC,
                                CONCAT(T3.RR_CODE,'-',T3.RR_DESC) AS RR_CODE_DES, 

                                (
                                SELECT T5.STANDARDVALUE_TYPE
                                FROM TBL_MST_QIC_HDR T4
                                INNER JOIN TBL_MST_QIC_MAT T5 ON T4.QICID=T5.QICID_REF AND T5.QCPID_REF=T1.QCPID_REF
                                WHERE T4.ITEMID_REF='$ITEMID_REF' AND T4.CYID_REF='$CYID_REF' AND T4.BRID_REF='$BRID_REF'  AND T4.STATUS='A' AND (T4.DEACTIVATED=0 or T4.DEACTIVATED is null) 
                                ) AS STANDARDVALUE_TYPE

                                FROM TBL_TRN_QIJ_MAT T1
                                LEFT JOIN TBL_MST_QCP T2 ON T1.QCPID_REF=T2.QCPID
                                LEFT JOIN TBL_MST_REJECTION_REASON T3 ON T1.RRID_REF=T3.RRID
                                WHERE T1.QIJID_REF='$id' ORDER BY T1.QIJID_REF ASC");
                                
                               
            $ObjUnionUDF    =   DB::table("TBL_MST_UDFFOR_QIJ")->select('*')
                                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF){       
                                        $query->select('UDFQIJID')->from('TBL_MST_UDFFOR_QIJ')
                                        ->where('STATUS','=','A')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);                                  
                                        }
                                    )
                                    ->where('DEACTIVATED','=',0)
                                    ->where('STATUS','<>','C')                    
                                    ->where('CYID_REF','=',$CYID_REF);
                                        
                $objUdf         =   DB::table('TBL_MST_UDFFOR_QIJ')
                                    ->where('STATUS','=','A')
                                    ->where('PARENTID','=',0)
                                    ->where('DEACTIVATED','=',0)
                                    ->where('CYID_REF','=',$CYID_REF)
                                    ->union($ObjUnionUDF)
                                    ->get()->toArray();  

                $objCountUDF    =   count($objUdf);
            
            
                $objtempUdf     =   $objUdf;
                foreach ($objtempUdf as $index => $udfvalue) {

                    $objSavedUDF =  DB::table('TBL_TRN_QIJ_UDF')
                                    ->where('QIJID_REF','=',$id)
                                    ->where('UDFQIJID_REF','=',$udfvalue->UDFQIJID)
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

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view($this->view.'edit', compact([
                'FormId','objUdf','objCountUDF','objlastdt','objUserList','objGRNList',
                'objStoreList','objRights','HDR','MAT','TabSetting'
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
                                T5.GRNNO,
                                CONCAT(T6.STCODE,'-',T6.NAME) AS REJECTED_STORE,
                                CONCAT(T7.STCODE,'-',T7.NAME) AS QC_OK_STORE,
                                CONCAT(T8.STCODE,'-',T8.NAME) AS PENDING_QC_STORE
                            
                                FROM TBL_TRN_QIJ_HDR T1
                                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                                LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                                LEFT JOIN TBL_MST_EMPLOYEE T4 ON T1.QC_PROCESS_BY=T4.EMPID
                                LEFT JOIN TBL_TRN_GRJ_HDR T5 ON T1.GRJID_REF=T5.GRJID
                                LEFT JOIN TBL_MST_STORE T6 ON T1.REJECTED_STID_REF=T6.STID
                                LEFT JOIN TBL_MST_STORE T7 ON T1.QC_OK_STID_REF=T7.STID
                                LEFT JOIN TBL_MST_STORE T8 ON T1.PENDING_QC_STID_REF=T8.STID

                                WHERE T1.QIJID='$id' ")[0]; 

            $ITEMID_REF     =   isset($HDR->ITEMID_REF)?$HDR->ITEMID_REF:NULL;

            $MAT            =   DB::select("SELECT 
                                T1.*,
                                T2.QCPID,T2.QCP_CODE,T2.QCP_DESC,
                                CONCAT(T3.RR_CODE,'-',T3.RR_DESC) AS RR_CODE_DES,  
                                                              
                                (
                                SELECT T5.STANDARDVALUE_TYPE
                                FROM TBL_MST_QIC_HDR T4
                                INNER JOIN TBL_MST_QIC_MAT T5 ON T4.QICID=T5.QICID_REF AND T5.QCPID_REF=T1.QCPID_REF
                                WHERE T4.ITEMID_REF='$ITEMID_REF' AND T4.CYID_REF='$CYID_REF' AND T4.BRID_REF='$BRID_REF'  AND T4.STATUS='A' AND (T4.DEACTIVATED=0 or T4.DEACTIVATED is null) 
                                ) AS STANDARDVALUE_TYPE
                                
                                FROM TBL_TRN_QIJ_MAT T1
                                LEFT JOIN TBL_MST_QCP T2 ON T1.QCPID_REF=T2.QCPID
                                LEFT JOIN TBL_MST_REJECTION_REASON T3 ON T1.RRID_REF=T3.RRID
                                WHERE T1.QIJID_REF='$id' ORDER BY T1.QIJID_REF ASC");

           
                                
                               
            $ObjUnionUDF    =   DB::table("TBL_MST_UDFFOR_QIJ")->select('*')
                                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF){       
                                        $query->select('UDFQIJID')->from('TBL_MST_UDFFOR_QIJ')
                                        ->where('STATUS','=','A')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);                                  
                                        }
                                    )
                                    ->where('DEACTIVATED','=',0)
                                    ->where('STATUS','<>','C')                    
                                    ->where('CYID_REF','=',$CYID_REF);
                                        
                $objUdf         =   DB::table('TBL_MST_UDFFOR_QIJ')
                                    ->where('STATUS','=','A')
                                    ->where('PARENTID','=',0)
                                    ->where('DEACTIVATED','=',0)
                                    ->where('CYID_REF','=',$CYID_REF)
                                    ->union($ObjUnionUDF)
                                    ->get()->toArray();  

                $objCountUDF    =   count($objUdf);
            
            
                $objtempUdf     =   $objUdf;
                foreach ($objtempUdf as $index => $udfvalue) {

                    $objSavedUDF =  DB::table('TBL_TRN_QIJ_UDF')
                                    ->where('QIJID_REF','=',$id)
                                    ->where('UDFQIJID_REF','=',$udfvalue->UDFQIJID)
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

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');


            return view($this->view.'view', compact([
                'FormId','objUdf','objCountUDF','objlastdt','objUserList','objGRNList',
                'objStoreList','objRights','HDR','MAT','TabSetting'
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
                $udffield_Data[$i]['UDFQIJID_REF'] = $request['udffie_'.$i]; 
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

        $QIJNO                  =   $request['QIJNO'] !=""?$request['QIJNO']:NULL;
        $QIJDT                  =   $request['QIJDT'] !=""?$request['QIJDT']:NULL;
        $QC_PROCESS_BY          =   $request['QC_PROCESS_BY'] !=""?$request['QC_PROCESS_BY']:NULL;
        $EXT_LABNAME            =   $request['EXT_LABNAME'] !=""?$request['EXT_LABNAME']:NULL;

        $REF_DOCNO              =   $request['REF_DOCNO'] !=""?$request['REF_DOCNO']:NULL;
        $GRJID_REF              =   $request['GRJID_REF'] !=""?$request['GRJID_REF']:NULL;
        $ITEMID_REF             =   $request['ITEMID_REF'] !=""?$request['ITEMID_REF']:NULL;
        $GRJ_REC_QTY            =   $request['GRJ_REC_QTY'] !=""?$request['GRJ_REC_QTY']:0;
        $UOMID_REF              =   $request['UOMID_REF'] !=""?$request['UOMID_REF']:NULL;

        $REMARKS                =   $request['REMARKS'] !=""?$request['REMARKS']:NULL;
        $QI_PICK_QTY            =   $request['QI_PICK_QTY'] !=""?$request['QI_PICK_QTY']:0;
        $REJECTED_QTY           =   $request['REJECTED_QTY'] !=""?$request['REJECTED_QTY']:0;
        $QC_OK_QTY              =   $request['QC_OK_QTY'] !=""?$request['QC_OK_QTY']:0;

        $GEJWOID_REF            =   $request['GEJWOID_REF'] !=""?$request['GEJWOID_REF']:NULL;
        $JWCID_REF              =   $request['JWCID_REF'] !=""?$request['JWCID_REF']:NULL;

        $JWOID_REF              =   $request['JWOID_REF'] !=""?$request['JWOID_REF']:NULL;
        $PROID_REF              =   $request['PROID_REF'] !=""?$request['PROID_REF']:NULL;
        $SOID_REF               =   $request['SOID_REF'] !=""?$request['SOID_REF']:NULL;
        $SQID_REF               =   $request['SQID_REF'] !=""?$request['SQID_REF']:NULL;
        $SEID_REF               =   $request['SEID_REF'] !=""?$request['SEID_REF']:NULL;

        $PENDING_QC_QTY         =   $request['PENDING_QC_QTY'] !=""?$request['PENDING_QC_QTY']:0;
        $REJECTED_STID_REF      =   $request['REJECTED_STID_REF'] !=""?$request['REJECTED_STID_REF']:NULL;
        $QC_OK_STID_REF         =   $request['QC_OK_STID_REF'] !=""?$request['QC_OK_STID_REF']:NULL;
        $PENDING_QC_STID_REF    =   $request['PENDING_QC_STID_REF'] !=""?$request['PENDING_QC_STID_REF']:NULL;
        
        $log_data = [ 
            $QIJNO,                 $QIJDT,         $QC_PROCESS_BY,     $EXT_LABNAME,  
            $REF_DOCNO,             $GRJID_REF,     $ITEMID_REF,        $GRJ_REC_QTY,       $UOMID_REF,  
            $REMARKS,               $QI_PICK_QTY,   $REJECTED_QTY,      $QC_OK_QTY,         $CYID_REF, 
            $BRID_REF,              $FYID_REF,      $VTID_REF,          $GEJWOID_REF,       $JWCID_REF,  
            $JWOID_REF,             $PROID_REF,     $SOID_REF,          $SQID_REF,          $SEID_REF,
            $XMLMAT,                $XMLUDF,        $USERID_REF,        Date('Y-m-d'),      Date('h:i:s.u'),    
            $ACTIONNAME,            $IPADDRESS,     $PENDING_QC_QTY,    $REJECTED_STID_REF, $QC_OK_STID_REF,
            $PENDING_QC_STID_REF    
        ];

        $sp_result = DB::select('EXEC SP_QIJ_UP ?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?', $log_data);     
        
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $QIJNO. ' Sucessfully Updated.']);

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
                $udffield_Data[$i]['UDFQIJID_REF'] = $request['udffie_'.$i]; 
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


        $QIJNO                  =   $request['QIJNO'] !=""?$request['QIJNO']:NULL;
        $QIJDT                  =   $request['QIJDT'] !=""?$request['QIJDT']:NULL;
        $QC_PROCESS_BY          =   $request['QC_PROCESS_BY'] !=""?$request['QC_PROCESS_BY']:NULL;
        $EXT_LABNAME            =   $request['EXT_LABNAME'] !=""?$request['EXT_LABNAME']:NULL;

        $REF_DOCNO              =   $request['REF_DOCNO'] !=""?$request['REF_DOCNO']:NULL;
        $GRJID_REF              =   $request['GRJID_REF'] !=""?$request['GRJID_REF']:NULL;
        $ITEMID_REF             =   $request['ITEMID_REF'] !=""?$request['ITEMID_REF']:NULL;
        $GRJ_REC_QTY            =   $request['GRJ_REC_QTY'] !=""?$request['GRJ_REC_QTY']:0;
        $UOMID_REF              =   $request['UOMID_REF'] !=""?$request['UOMID_REF']:NULL;

        $REMARKS                =   $request['REMARKS'] !=""?$request['REMARKS']:NULL;
        $QI_PICK_QTY            =   $request['QI_PICK_QTY'] !=""?$request['QI_PICK_QTY']:0;
        $REJECTED_QTY           =   $request['REJECTED_QTY'] !=""?$request['REJECTED_QTY']:0;
        $QC_OK_QTY              =   $request['QC_OK_QTY'] !=""?$request['QC_OK_QTY']:0;

        $GEJWOID_REF            =   $request['GEJWOID_REF'] !=""?$request['GEJWOID_REF']:NULL;
        $JWCID_REF              =   $request['JWCID_REF'] !=""?$request['JWCID_REF']:NULL;

        $JWOID_REF              =   $request['JWOID_REF'] !=""?$request['JWOID_REF']:NULL;
        $PROID_REF              =   $request['PROID_REF'] !=""?$request['PROID_REF']:NULL;
        $SOID_REF               =   $request['SOID_REF'] !=""?$request['SOID_REF']:NULL;
        $SQID_REF               =   $request['SQID_REF'] !=""?$request['SQID_REF']:NULL;
        $SEID_REF               =   $request['SEID_REF'] !=""?$request['SEID_REF']:NULL;

        $PENDING_QC_QTY         =   $request['PENDING_QC_QTY'] !=""?$request['PENDING_QC_QTY']:0;
        $REJECTED_STID_REF      =   $request['REJECTED_STID_REF'] !=""?$request['REJECTED_STID_REF']:NULL;
        $QC_OK_STID_REF         =   $request['QC_OK_STID_REF'] !=""?$request['QC_OK_STID_REF']:NULL;
        $PENDING_QC_STID_REF    =   $request['PENDING_QC_STID_REF'] !=""?$request['PENDING_QC_STID_REF']:NULL;
        
        $log_data = [ 
            $QIJNO,                 $QIJDT,         $QC_PROCESS_BY,     $EXT_LABNAME,  
            $REF_DOCNO,             $GRJID_REF,     $ITEMID_REF,        $GRJ_REC_QTY,       $UOMID_REF,  
            $REMARKS,               $QI_PICK_QTY,   $REJECTED_QTY,      $QC_OK_QTY,         $CYID_REF, 
            $BRID_REF,              $FYID_REF,      $VTID_REF,          $GEJWOID_REF,       $JWCID_REF,  
            $JWOID_REF,             $PROID_REF,     $SOID_REF,          $SQID_REF,          $SEID_REF,
            $XMLMAT,                $XMLUDF,        $USERID_REF,        Date('Y-m-d'),      Date('h:i:s.u'),    
            $ACTIONNAME,            $IPADDRESS,     $PENDING_QC_QTY,    $REJECTED_STID_REF, $QC_OK_STID_REF,
            $PENDING_QC_STID_REF    
        ];

        $sp_result = DB::select('EXEC SP_QIJ_UP ?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?', $log_data);     
        
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $QIJNO. ' Sucessfully Approved.']);

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
                $TABLE      =   "TBL_TRN_QIJ_HDR";
                $FIELD      =   "QIJID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_QIJ ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
    
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
        $TABLE      =   "TBL_TRN_QIJ_HDR";
        $FIELD      =   "QIJID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_QIJ_MAT',
        ];

        $req_data[1]=[
            'NT'  => 'TBL_TRN_QIJ_UDF',
        ];
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_QIJ  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_QIJ_HDR')->where('QIJID','=',$id)->first();

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
        
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/QualityInspection";
		
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
        $QIJNO      =   $request->QIJNO;
        
        $objExit    =   DB::table('TBL_TRN_QIJ_HDR')
                        ->where('TBL_TRN_QIJ_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_TRN_QIJ_HDR.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_TRN_QIJ_HDR.FYID_REF','=',Session::get('FYID_REF'))
                        ->where('TBL_TRN_QIJ_HDR.QIJNO','=',$QIJNO)
                        ->select('TBL_TRN_QIJ_HDR.QIJNO')
                        ->first();
        
        if($objExit){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate QIJ NO']);
        
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
        $GRJID_REF  =   $request['GRJID_REF'];
        
        $StdCost    =   0;

        $ObjItem =  DB::select("SELECT 
        T1.UOMID_REF AS MAIN_UOMID_REF,T1.RECEIVED_QTY AS ORDER_QTY,T1.RECEIVED_QTY AS Qty,
        T1.GRJID_REF,T1.JWCID_REF,T1.JWOID_REF,T1.PROID_REF,T1.SOID_REF,T1.SEID_REF,T1.SQID_REF,
        T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI,T3.GEJWOID
        FROM TBL_TRN_GRJ_MAT T1
        LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
        LEFT JOIN TBL_TRN_GEJWO_HDR T3 ON T1.JWOID_REF=T3.JWOID_REF
        WHERE T1.GRJID_REF='$GRJID_REF' 
        ");

    
        if(!empty($ObjItem)){

            foreach ($ObjItem as $index=>$dataRow){

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

                $item_unique_row_id =   $dataRow->GRJID_REF."_".$dataRow->JWCID_REF."_".$dataRow->JWOID_REF."_".$dataRow->PROID_REF."_".$dataRow->SOID_REF."_".$dataRow->SQID_REF."_".$dataRow->SEID_REF."_".$dataRow->ITEMID;
               
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
                            data-desc8="'.$dataRow->SQID_REF.'"
                            data-desc9="'.$dataRow->SEID_REF.'"
                            data-desc10="'.$dataRow->JWOID_REF.'"
                            data-desc11="'.$dataRow->SOID_REF.'"
                            data-desc12="'.$SOQTY.'"
                            data-desc13="'.$dataRow->PROID_REF.'"
                            data-desc14="'.$dataRow->ORDER_QTY.'"
                            data-desc15="'.$dataRow->JWCID_REF.'"
                            data-desc16="'.$dataRow->GEJWOID.'"
                        />
                    </td>
                </tr>';
                echo $row;    
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
        T3.QCPID,T3.QCP_CODE,T3.QCP_DESC,T2.STANDARDVALUE_TYPE,T2.STANDARD_VALUE
        FROM TBL_MST_QIC_HDR T1
        INNER JOIN TBL_MST_QIC_MAT T2 ON T1.QICID=T2.QICID_REF
        INNER JOIN TBL_MST_QCP T3 ON T2.QCPID_REF=T3.QCPID
        WHERE T1.ITEMID_REF='$ITEMID_REF' AND T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF'  AND T1.STATUS='A' AND (T1.DEACTIVATED=0 or T1.DEACTIVATED is null)"); 

        //dd($ObjData);

        $Row_Count1 =   isset($ObjData)?count($ObjData):0;

        echo '<thead id="thead1"  style="position: sticky;top: 0">                           
                <tr>
                    <th>Select</th>
                    <th>QC Parameter Code</th>
                    <th>QC Parameter Description</th>
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

                $OBS_VALUE_READONLY     =   $dataRow->STANDARDVALUE_TYPE=="Text" || $dataRow->STANDARDVALUE_TYPE=="Logical"?"readonly":'';
                $AVG_OBS_VALUE_READONLY =   $dataRow->STANDARDVALUE_TYPE=="Numeric Value" || $dataRow->STANDARDVALUE_TYPE=="Range In Value" || $dataRow->STANDARDVALUE_TYPE=="Range Percent"?"readonly":'';
                $STANDARD_VALUE         =   $dataRow->STANDARDVALUE_TYPE=="Range In Value" || $dataRow->STANDARDVALUE_TYPE=="Range Percent"?str_replace(",", "-",$dataRow->STANDARD_VALUE):$dataRow->STANDARD_VALUE;

                if($dataRow->STANDARDVALUE_TYPE =="Numeric Value"){
                    $STANDARD_TYPE="(In Numeric)";
                }
                else if($dataRow->STANDARDVALUE_TYPE =="Range In Value"){
                    $STANDARD_TYPE="(In Numeric)";
                }
                else if($dataRow->STANDARDVALUE_TYPE =="Range Percent"){
                    $STANDARD_TYPE="(In %)";
                }
                else if($dataRow->STANDARDVALUE_TYPE =="Logical"){
                    $STANDARD_TYPE="(In Logical)";
                } 
                else if($dataRow->STANDARDVALUE_TYPE =="Text"){
                    $STANDARD_TYPE="(In Text)";
                } 

                echo '<tr  class="participantRow">
                    <td><input type="checkbox" name="SELECT_QCP[]"  id="SELECT_QCP_'.$index.'" class="clssQCPID" value="'.$index.'" ></td>
                    <td><input type="text" name="txtQCPID_popup_'.$index.'"   id="txtQCPID_popup_'.$index.'" value="'.$dataRow->QCP_CODE.'" class="form-control" autocomplete="off" readonly/></td>
                    <td hidden><input type="text" name="QCPID_REF_'.$index.'" id="QCPID_REF_'.$index.'"    value="'.$dataRow->QCPID.'"  class="form-control" autocomplete="off" /></td>
                    
                    <td><input type="text" name="QCP_DES_'.$index.'" id="QCP_DES_'.$index.'" value="'.$dataRow->QCP_DESC.'" class="form-control"  autocomplete="off" readonly /></td>
                    <td hidden><input type="text" name="STD_TYPE_'.$index.'" id="STD_TYPE_'.$index.'" value="'.$dataRow->STANDARDVALUE_TYPE.'" class="form-control"  autocomplete="off" readonly style="width:100px;" /></td>
                    <td><input type="text" name="STD_VALUE_'.$index.'" id="STD_VALUE_'.$index.'" value="'.$STANDARD_VALUE.'" class="form-control"  autocomplete="off" readonly style="width:100px;"  /> <div style="width:170px !important;"> <span style="margin-left:2px">'.$STANDARD_TYPE.' </span></div> </td>

                    <td><input type="text" name="OBS_VALUE1_'.$index.'" id="OBS_VALUE1_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                    <td><input type="text" name="OBS_VALUE2_'.$index.'" id="OBS_VALUE2_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                    <td><input type="text" name="OBS_VALUE3_'.$index.'" id="OBS_VALUE3_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                    <td><input type="text" name="OBS_VALUE4_'.$index.'" id="OBS_VALUE4_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                    <td><input type="text" name="OBS_VALUE5_'.$index.'" id="OBS_VALUE5_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                    <td><input type="text" name="OBS_VALUE6_'.$index.'" id="OBS_VALUE6_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                    <td><input type="text" name="OBS_VALUE7_'.$index.'" id="OBS_VALUE7_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                    <td><input type="text" name="OBS_VALUE8_'.$index.'" id="OBS_VALUE8_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                    <td><input type="text" name="OBS_VALUE9_'.$index.'" id="OBS_VALUE9_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                    <td><input type="text" name="OBS_VALUE10_'.$index.'" id="OBS_VALUE10_'.$index.'" '.$OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" style="width:50px;" onkeyup="AverageTotal(this.id)" onkeypress="return isNumberDecimalKey(event,this)" /></td>

                    <td><input type="text" name="AVG_OBS_VALUE_'.$index.'" id="AVG_OBS_VALUE_'.$index.'" '.$AVG_OBS_VALUE_READONLY.' class="form-control"  autocomplete="off" onkeyup="AverageTotal(this.id)" /></td>
                    <td><input type="text" name="REJECTED_'.$index.'" id="REJECTED_'.$index.'" class="form-control"  autocomplete="off" readonly ></td>

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
        where CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF'  AND STATUS='A' AND (DEACTIVATED=0 or DEACTIVATED is null)"); 

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

        $data = DB::select("SELECT T1.GRJID AS DOC_ID,GRNNO AS DOC_CODE,T1.GRNDT AS DOC_DESC
        FROM TBL_TRN_GRJ_HDR AS T1
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.STATUS='A'");

        return $data;
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
