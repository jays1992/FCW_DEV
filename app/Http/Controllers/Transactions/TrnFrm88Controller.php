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

class TrnFrm88Controller extends Controller{

    protected $form_id  = 88;
    protected $vtid_ref = 88;
    protected $view     = "transactions.inventory.MaterialRequisitionSlip.trnfrm";
   
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){    
        $objRights      =   DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
		->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
		->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();
        
        $FormId         =   $this->form_id;
        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');     

        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList	=	DB::select("select hdr.MRSID,hdr.MRS_NO,hdr.MRS_DT,hdr.REMARKS,hdr.INDATE,hdr.MRS_TYPE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.MRSID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                            ) AS CREATED_BY,
                            hdr.STATUS,
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
                            inner join TBL_TRN_MRQS01_HDR hdr
                            on a.VID = hdr.MRSID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF 
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.MRSID DESC ");

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

   
     public function ViewReport($request) {

        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
           
        $MRSID       =   $myValue['MRSID'];
        $Flag       =   $myValue['Flag'];

        // $objSalesOrder = DB::table('TBL_TRN_PROR01_HDR')
        // ->where('TBL_TRN_PROR01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        // ->where('TBL_TRN_PROR01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        // ->where('TBL_TRN_PROR01_HDR.POID','=',$POID)
        // ->select('TBL_TRN_PROR01_HDR.*')
        // ->first();
        
        
        $ssrs = new \SSRS\Report('http://103.139.58.23:8181//ReportServer/', array('username' => 'App', 'password' => 'admin@123'));
        $result = $ssrs->loadReport('/ERP/MRSPrint');
		//$result = $ssrs->loadReport('/ERP/POPrint -ZEP');
        
        $reportParameters = array(
            'MRSID' => $MRSID,
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
            $output = $ssrs->render('HTML4.0'); // PDF | XML | CSV | HTML4.0
            echo $output;

        }
         
     }



    public function add(){       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
       
        $objDepartmentList  =   $this->getDepartmentList();
        $objStoreList       =   $this->getStoreList();
        $objUserList        =   $this->getUserList();
        $objPriority        =   $this->getPriority();
        $AlpsStatus         =   $this->AlpsStatus();

        $objlastdt = DB::select('SELECT MAX(MRS_DT) MRS_DT FROM TBL_TRN_MRQS01_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
       
        $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
        WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by UOMCODE ASC', [$CYID_REF, 'A' ]);  
        
        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_MRQS01_HDR',
            'HDR_ID'=>'MRSID',
            'HDR_DOC_NO'=>'MRS_NO',
            'HDR_DOC_DT'=>'MRS_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
        
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_MRS")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDFMRSID')->from('TBL_MST_UDFFOR_MRS')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                 
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);                   
                   

        $objUdfData = DB::table('TBL_MST_UDFFOR_MRS')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();  

        $objCountUDF = count($objUdfData);

        $FormId     =   $this->form_id;

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        return view($this->view.$FormId.'add',compact([
                'AlpsStatus',
                'FormId',
                'objDepartmentList',
                'objStoreList',
                'objUserList',
                'objUdfData',
                'objCountUDF',
                'objlastdt',
                'objPriority',
                'objUOM',
                'TabSetting',
                'doc_req',
                'docarray',              
        ]));       
    }

    public function save(Request $request) {

        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){
                $req_data[$i] = [
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'MAIN_UOMID_REF'    => $request['MAIN_UOMID_REF_'.$i],
                    'QTY'               => $request['SE_QTY_'.$i],
                    'ITEM_SPECI'        => $request['Itemspec_'.$i],
                    'PRIORITYID_REF'    => $request['PTID_REF_'.$i],
                    'EXP_DATE'          => $request['EDD_'.$i]
                ];
            }
        }
 
        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
            
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['UDF_'.$i])){
                $reqdata3[$i] = [
                    'UDF'      => $request['UDF_'.$i],
                    'COMMENT'  => $request['udfvalue_'.$i],
                ];
            }
        }


        if(isset($reqdata3)){ 
            $wrapped_links3["UDF1"] = $reqdata3; 
            $XMLUDF = ArrayToXml::convert($wrapped_links3);
        }
        else{
            $XMLUDF = NULL; 
        }


        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $MRS_NO = $request['MRS_NO'];
        $MRS_DT = $request['MRS_DT'];
        $MRS_TYPE = $request['MRS_TYPE'];
        $REQUEST_USER = $request['REQUEST_USER'];
        $DEPID_REF = $request['DEPID_REF'];
        $STID_REF = $request['STID_REF'];
        $REMARKS = $request['REMARKS'];

        $PURPOSE        = NULL;
        $MAINTENANCE    = NULL;
        $OTHERS         = NULL;
        

        $log_data = [ 
            $MRS_NO,$MRS_DT,$REQUEST_USER,$DEPID_REF,$STID_REF,
            $REMARKS,$PURPOSE,$MAINTENANCE,$OTHERS,$CYID_REF,
            $BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT, $XMLUDF, 
            $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,
            $MRS_TYPE
        ];

        $sp_result = DB::select('EXEC SP_MRS_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?', $log_data);  

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
    }


    public function edit($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){


            $objRights = DB::table('TBL_MST_USERROLMAP')
			->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
			->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
			->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
			->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
			->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
			->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
			->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
			->first();

            $objResponse =  DB::table('TBL_TRN_MRQS01_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('MRSID','=',$id)
            ->first();

            
            $objDepartmentList  =   $this->getDepartmentList();
            $objStoreList       =   $this->getStoreList();
            $objUserList        =   $this->getUserList();
            $objPriority        =   $this->getPriority();

            $objDepartmentName  =[];
            if(isset($objResponse->DEPID_REF) && $objResponse->DEPID_REF !=""){
                $objDepartmentName  =   $this->getDepartmentName($objResponse->DEPID_REF);
            }

            $objStoreName       =[];
            if(isset($objResponse->STID_REF) && $objResponse->STID_REF !=""){
                $objStoreName       =   $this->getStoreName($objResponse->STID_REF);
            }

            $objUserName        =[];
            if(isset($objResponse->REQUEST_USER) && $objResponse->REQUEST_USER !=""){
                $objUserName        =   $this->getUserName($objResponse->REQUEST_USER);
            }

            $objMAT =[];
            if(isset($objResponse) && !empty($objResponse)){
                $objMAT = DB::select("SELECT 
                T1.*,
                T2.ICODE,T2.NAME AS ITEM_NAME,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                T4.PRIORITYCODE,T4.DESCRIPTIONS AS PRIORITYDESC
                FROM TBL_TRN_MRQS01_MAT T1
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_PRIORITY T4 ON T1.PRIORITYID_REF=T4.PRIORITYID
                WHERE T1.MRSID_REF='$id' ORDER BY T1.MRS_MATID ASC
                "); 
            }
            

            $objCount1 = count($objMAT); 
                        
            $objUDF = DB::table('TBL_TRN_MRQS01_UDF')                    
            ->where('MRSID_REF','=',$id)
            ->orderBy('MRS_UDFID','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF); 

            $objlastdt = DB::select('SELECT MAX(MRS_DT) MRS_DT FROM TBL_TRN_MRQS01_HDR  
            WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
            [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
            
            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_MRS")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                    {       
                                    $query->select('UDFMRSID')->from('TBL_MST_UDFFOR_MRS')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                   
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                     
                    

            $objUdfData = DB::table('TBL_MST_UDFFOR_MRS')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_MRS")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                        {       
                        $query->select('UDFMRSID')->from('TBL_MST_UDFFOR_MRS')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                           
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
           
            
            $objUdfData2 = DB::table('TBL_MST_UDFFOR_MRS')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF2)
            ->get()->toArray(); 

            $FormId         =   $this->form_id;
            $AlpsStatus     =   $this->AlpsStatus();
            $ActionStatus   =   "";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view($this->view.$FormId.'edit',compact(
                [
                'AlpsStatus',
                'FormId',
                'objRights',
                'objResponse',
                'objDepartmentList',
                'objStoreList',
                'objUserList',
                'objDepartmentName',
                'objStoreName',
                'objUserName',
                'objMAT',
                'objCount1',
                'objCount2',
                'objUDF',
                'objUdfData',
                'objCountUDF',
                'objUdfData2',
                'objlastdt',
                'objPriority',
                'ActionStatus',
                'TabSetting'
                ]
            ));      

        }
     
    }

    public function update(Request $request){
        
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $req_data[$i] = [
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'MAIN_UOMID_REF'    => $request['MAIN_UOMID_REF_'.$i],
                    'QTY'               => $request['SE_QTY_'.$i],
                    'ITEM_SPECI'        => $request['Itemspec_'.$i],
                    'PRIORITYID_REF'    => $request['PTID_REF_'.$i],
                    'EXP_DATE'          => $request['EDD_'.$i]
                ];
            }
        }

        
        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        for ($i=0; $i<=$r_count2; $i++){

            if(isset($request['UDF_'.$i])){
                $reqdata3[$i] = [
                    'UDF'      => $request['UDF_'.$i],
                    'COMMENT'  => $request['udfvalue_'.$i],
                ];
            }
            
        }

        if(isset($reqdata3)){ 
            $wrapped_links3["UDF1"] = $reqdata3; 
            $XMLUDF = ArrayToXml::convert($wrapped_links3);
        }
        else{
            $XMLUDF = NULL; 
        }
 

        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $MRS_NO = $request['MRS_NO'];
        $MRS_DT = $request['MRS_DT'];
        $MRS_TYPE = $request['MRS_TYPE'];
        $REQUEST_USER = $request['REQUEST_USER'];
        $DEPID_REF = $request['DEPID_REF'];
        $STID_REF = $request['STID_REF'];
        $REMARKS = $request['REMARKS'];

        $PURPOSE        = NULL;
        $MAINTENANCE    = NULL;
        $OTHERS         = NULL;

        $log_data = [ 
            $MRS_NO,$MRS_DT,$REQUEST_USER,$DEPID_REF,$STID_REF,
            $REMARKS,$PURPOSE,$MAINTENANCE,$OTHERS,$CYID_REF,
            $BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT, $XMLUDF, 
            $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,
            $MRS_TYPE
        ];

        $sp_result = DB::select('EXEC SP_MRS_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?', $log_data);  
            
           
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
    }

    public function view($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){


            DB::table('TBL_MST_USERROLMAP')
			->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
			->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
			->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
			->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
			->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
			->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
			->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
			->first();

            $objResponse =  DB::table('TBL_TRN_MRQS01_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('MRSID','=',$id)
            ->first();

            
            $objDepartmentList  =   $this->getDepartmentList();
            $objStoreList       =   $this->getStoreList();
            $objUserList        =   $this->getUserList();
            $objPriority        =   $this->getPriority();

            $objDepartmentName  =[];
            if(isset($objResponse->DEPID_REF) && $objResponse->DEPID_REF !=""){
                $objDepartmentName  =   $this->getDepartmentName($objResponse->DEPID_REF);
            }

            $objStoreName       =[];
            if(isset($objResponse->STID_REF) && $objResponse->STID_REF !=""){
                $objStoreName       =   $this->getStoreName($objResponse->STID_REF);
            }

            $objUserName        =[];
            if(isset($objResponse->REQUEST_USER) && $objResponse->REQUEST_USER !=""){
                $objUserName        =   $this->getUserName($objResponse->REQUEST_USER);
            }

            $objMAT =[];
            if(isset($objResponse) && !empty($objResponse)){
                $objMAT = DB::select("SELECT 
                T1.*,
                T2.ICODE,T2.NAME AS ITEM_NAME,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                T4.PRIORITYCODE,T4.DESCRIPTIONS AS PRIORITYDESC
                FROM TBL_TRN_MRQS01_MAT T1
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_PRIORITY T4 ON T1.PRIORITYID_REF=T4.PRIORITYID
                WHERE T1.MRSID_REF='$id' ORDER BY T1.MRS_MATID ASC
                "); 
            }
            

            $objCount1 = count($objMAT); 
                        
            $objUDF = DB::table('TBL_TRN_MRQS01_UDF')                    
            ->where('MRSID_REF','=',$id)
            ->orderBy('MRS_UDFID','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF); 

            $objlastdt = DB::select('SELECT MAX(MRS_DT) MRS_DT FROM TBL_TRN_MRQS01_HDR  
            WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND VTID_REF = ? AND STATUS = ?', 
            [$CYID_REF, $BRID_REF, $FYID_REF, $this->vtid_ref, 'A' ]);
            
            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_MRS")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                    {       
                                    $query->select('UDFMRSID')->from('TBL_MST_UDFFOR_MRS')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                   
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                     
                    

            $objUdfData = DB::table('TBL_MST_UDFFOR_MRS')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_MRS")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                        {       
                        $query->select('UDFMRSID')->from('TBL_MST_UDFFOR_MRS')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                           
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
           
            
            $objUdfData2 = DB::table('TBL_MST_UDFFOR_MRS')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF2)
            ->get()->toArray(); 

            $FormId         =   $this->form_id;
            $AlpsStatus     =   $this->AlpsStatus();
            $ActionStatus   =   "disabled";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            $ItemListSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_LIST_SETTING');

            
            
            return view($this->view.$FormId.'view',compact(
                [
                'AlpsStatus',
                'FormId',
                'objRights',
                'objResponse',
                'objDepartmentList',
                'objStoreList',
                'objUserList',
                'objDepartmentName',
                'objStoreName',
                'objUserName',
                'objMAT',
                'objCount1',
                'objCount2',
                'objUDF',
                'objUdfData',
                'objCountUDF',
                'objUdfData2',
                'objlastdt',
                'objPriority',
                'ActionStatus',
                'TabSetting',
                'ItemListSetting'
                ]
            ));      

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

                $req_data[$i] = [
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'MAIN_UOMID_REF'    => $request['MAIN_UOMID_REF_'.$i],
                    'QTY'               => $request['SE_QTY_'.$i],
                    'ITEM_SPECI'        => $request['Itemspec_'.$i],
                    'PRIORITYID_REF'    => $request['PTID_REF_'.$i],
                    'EXP_DATE'          => $request['EDD_'.$i]
                ];
            }
        }

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        for ($i=0; $i<=$r_count2; $i++){

            if(isset($request['UDF_'.$i])){
                $reqdata3[$i] = [
                    'UDF'      => $request['UDF_'.$i],
                    'COMMENT'  => $request['udfvalue_'.$i],
                ];
            }
            
        }

        if(isset($reqdata3)){ 
            $wrapped_links3["UDF1"] = $reqdata3; 
            $XMLUDF = ArrayToXml::convert($wrapped_links3);
        }
        else{
            $XMLUDF = NULL; 
        }
        
        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $MRS_NO = $request['MRS_NO'];
        $MRS_DT = $request['MRS_DT'];
        $MRS_TYPE = $request['MRS_TYPE'];
        $REQUEST_USER = $request['REQUEST_USER'];
        $DEPID_REF = $request['DEPID_REF'];
        $STID_REF = $request['STID_REF'];
        $REMARKS = $request['REMARKS'];

        $PURPOSE        = NULL;
        $MAINTENANCE    = NULL;
        $OTHERS         = NULL;

        $log_data = [ 
            $MRS_NO,$MRS_DT,$REQUEST_USER,$DEPID_REF,$STID_REF,
            $REMARKS,$PURPOSE,$MAINTENANCE,$OTHERS,$CYID_REF,
            $BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT, $XMLUDF, 
            $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,
            $MRS_TYPE
        ];

        $sp_result = DB::select('EXEC SP_MRS_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?', $log_data);  

            
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully Approved.']);

        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
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
        $TABLE      =   "TBL_TRN_MRQS01_HDR";
        $FIELD      =   "MRSID";
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
        $TABLE      =   "TBL_TRN_MRQS01_HDR";
        $FIELD      =   "MRSID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
         'NT'  => 'TBL_TRN_MRQS01_MAT',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_MRQS01_UDF',
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

    public function attachment($id){

        if(!is_null($id)){
        
            $FormId     =   $this->form_id;

            $objResponse = DB::table('TBL_TRN_MRQS01_HDR')->where('MRSID','=',$id)->first();

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
        
		$image_path         =   "docs/company".$CYID_REF."/MaterialRequisitionSlip";     
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

        $MRS_NO  =   trim($request['MRS_NO']);
        $objLabel = DB::table('TBL_TRN_MRQS01_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('MRS_NO','=',$MRS_NO)
        ->select('MRSID')->first();

        if($objLabel){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }


    public function getDepartmentList(){
        return  DB::table('TBL_MST_DEPARTMENT')
            ->where('STATUS','=','A')
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


    public function getUserList(){
        return DB::table('TBL_MST_EMPLOYEE')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('EMPID','EMPCODE','FNAME','MNAME','LNAME')
            ->get();
    }

    public function getUserName($id){
        return DB::table('TBL_MST_EMPLOYEE')
            ->where('EMPID','=',$id)
            ->select('EMPID','EMPCODE','FNAME','MNAME','LNAME')
            ->first();
    }

    public function getPriority(){
        return  DB::table('TBL_MST_PRIORITY')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('PRIORITYID','PRIORITYCODE','DESCRIPTIONS')
            ->get();
    }

    public function getItemDetails(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $ItemListSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_LIST_SETTING');
        $DataField = NULL;
        if(isset($ItemListSetting) && !empty($ItemListSetting)){
          
            $DataField = $ItemListSetting->FIELD1;

        } 

       
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
                $ITEM_TYPE          =   isset($dataRow->ITEM_TYPE)?$dataRow->ITEM_TYPE:NULL;
               
                if($DataField ==""){

                    $row.=' <tr id="item_'.$ITEMID.'" class="clsitemid">
                            <td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1"  ></td>
                            <td style="width:10%;">'.$ICODE.'<input type="hidden" id="txtitem_'.$ITEMID.'" data-desc="'.$ICODE.'" value="'.$ITEMID.'"/></td>
                            <td style="width:10%;" id="itemname_'.$ITEMID.'" >'.$NAME.'<input type="hidden" id="txtitemname_'.$ITEMID.'" data-desc="'.$ITEM_SPECI.'" value="'.$NAME.'"/></td>
                            <td style="width:8%;" id="itemuom_'.$ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ITEMID.'" data-desc="'.$Alt_UOM.'" value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>
                            <td style="width:8%;" id="uomqty_'.$ITEMID.'" ><input type="hidden" id="txtuomqty_'.$ITEMID.'" data-desc="'.$TOQTY.'" value="'.$ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>
                            <td style="width:8%;" id="irate_'.$ITEMID.'"><input type="hidden" id="txtirate_'.$ITEMID.'" data-desc="'.$FROMQTY.'" value="'.$STDCOST.'"/>'.$GroupName.'</td>
                            <td style="width:8%;" id="itax_'.$ITEMID.'"><input type="hidden" id="txtitax_'.$ITEMID.'" />'.$Categoryname.'</td>
                            <td style="width:8%;">'.$BusinessUnit.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].'>'.$ALPS_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].'>'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].'>'.$OEM_PART_NO.'</td>
                            <td style="width:8%;">Authorized</td>
                            </tr>'; 
                }
                else{

                    if($DataField ==$MATERIAL_TYPE){

                        $row.=' <tr id="item_'.$ITEMID.'" class="clsitemid">
                            <td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1"  ></td>
                            <td style="width:10%;">'.$ICODE.'<input type="hidden" id="txtitem_'.$ITEMID.'" data-desc="'.$ICODE.'" value="'.$ITEMID.'"/></td>
                            <td style="width:10%;" id="itemname_'.$ITEMID.'" >'.$NAME.'<input type="hidden" id="txtitemname_'.$ITEMID.'" data-desc="'.$ITEM_SPECI.'" value="'.$NAME.'"/></td>
                            <td style="width:8%;" id="itemuom_'.$ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ITEMID.'" data-desc="'.$Alt_UOM.'" value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>
                            <td style="width:8%;" id="uomqty_'.$ITEMID.'" ><input type="hidden" id="txtuomqty_'.$ITEMID.'" data-desc="'.$TOQTY.'" value="'.$ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>
                            <td style="width:8%;" id="irate_'.$ITEMID.'"><input type="hidden" id="txtirate_'.$ITEMID.'" data-desc="'.$FROMQTY.'" value="'.$STDCOST.'"/>'.$GroupName.'</td>
                            <td style="width:8%;" id="itax_'.$ITEMID.'"><input type="hidden" id="txtitax_'.$ITEMID.'" />'.$Categoryname.'</td>
                            <td style="width:8%;">'.$BusinessUnit.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].'>'.$ALPS_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].'>'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:8%;" '.$AlpsStatus['hidden'].'>'.$OEM_PART_NO.'</td>
                            <td style="width:8%;">Authorized</td>
                            </tr>'; 
                    }

                }
            } 

            
            echo $row;                   
        }           
        else{
            echo '<tr><td colspan="12"> Record not found.</td></tr>';
        }

        exit();
    }

    

    
}
