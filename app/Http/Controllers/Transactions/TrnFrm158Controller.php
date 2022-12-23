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

class TrnFrm158Controller extends Controller{

    protected $form_id  = 158;
    protected $vtid_ref = 93;
    protected $view     = "transactions.inventory.GrnReturnableGatePass.trnfrm";
   
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

        $objDataList	=	DB::select("select hdr.GRNID,hdr.GRN_NO,hdr.GRN_DT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.GRNID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                            ) AS CREATED_BY,
                            hdr.STATUS, sl.SLNAME,sl.SLNAME AS VENDOR_NAME,cu.SLNAME AS CUSTOMER_NAME,EMP.FNAME AS EMPLOYEE_NAME,hdr.TYPE,
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
                            inner join TBL_TRN_IGRN01_HDR hdr
                            on a.VID = hdr.GRNID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            left join TBL_MST_SUBLEDGER sl ON hdr.VID_REF = sl.SGLID AND sl.BELONGS_TO='Vendor' AND hdr.TYPE='Vendor'   
                            left join TBL_MST_SUBLEDGER cu ON hdr.VID_REF = cu.SGLID AND cu.BELONGS_TO='Customer' AND hdr.TYPE='Customer'  
                            left join TBL_MST_EMPLOYEE EMP ON hdr.VID_REF = EMP.EMPID  AND hdr.TYPE='Employee'
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.GRNID DESC ");

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
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
       
        $objStoreList       =   $this->getStoreList();
        $objPriority        =   $this->getPriority();
        $objlastdt          =   $this->getLastdt();
        $Employee           =   $this->getEmployee();

        $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
        WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by UOMCODE ASC', [$CYID_REF,  'A' ]);   
        
        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_IGRN01_HDR',
            'HDR_ID'=>'GRNID',
            'HDR_DOC_NO'=>'GRN_NO',
            'HDR_DOC_DT'=>'GRN_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        
   
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_GRNRGP")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                {       
                                $query->select('GRNRGPID')->from('TBL_MST_UDFFOR_GRNRGP')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);                      
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);                   
                   

        $objUdfData = DB::table('TBL_MST_UDFFOR_GRNRGP')
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
                'objStoreList',
                'objUdfData',
                
                'objCountUDF',
                
                'objlastdt',
                'objPriority',
                'objUOM',
                'TabSetting',
                'Employee',
                'doc_req','docarray'
        ]));       
    }

    public function save(Request $request) {

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

                    $StoreArr[]=$batchid;
                }

                if(!empty($StoreArr)){
                    $StoreId    =   array_unique($StoreArr);
                    $STID_REF   =   implode(",",$StoreId);
                }
                else{
                    $STID_REF   =   NULL;
                }

                $req_data[$i] = [
                    'RGPID_REF'        => $request['RGP_NO_'.$i],
                    'ITEMID_REF'       => $request['ITEMID_REF_'.$i],
                    'BILL_CH_QTY'      => $request['SE_QTY_'.$i],
                    'MAIN_UOMID_REF'   => $request['MAIN_UOMID_REF_'.$i],
                    'RECEIVED_QTY_MU'  => $request['RECEIVED_QTY_MU_'.$i],
                    'SHORT_QTY'        => $request['SHORT_QTY_'.$i],
                    'STID'             =>  $STID_REF,
                    'REMARKS'          => $request['REMARKS_'.$i],
                    'BATCHQTY_REF'     => $request['HiddenRowId_'.$i],
                    'NATURE'          => $request['NATURE_'.$i],
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
		if(isset($reqdata3))
		{
        $wrapped_links3["UDF1"] = $reqdata3; 
        $XMLUDF = ArrayToXml::convert($wrapped_links3);
		}
		else
		{
		$XMLUDF = NULL;
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
                        $qty                =   $keyid[1];
                        $dataArr[$batchid]  =   $qty;
                    }
                }


                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        $ExpID              =   explode("#",$key);
                        $STID_REF           =   $ExpID[0];
                        $RGPID_REF          =   $ExpID[1];
                        $BATCH_NO           =   $ExpID[2];
                        $ITEMID_REF         =   $ExpID[3];
                        $UOMID_REF          =   $ExpID[4];

                      

                        $req_data33[$i][] = [
                            'ITEMID_REF'    => $ITEMID_REF,
                            'STID_REF'      => $STID_REF,
                            'MAIN_UOMID_REF'     => $UOMID_REF,
                            'RECEIVED_QTYM'   => $val,  
                            'RGPID_REF'     => $RGPID_REF,
                        ];

                    }
                }
            }
        }

		if(isset($req_data33))
		{
        $wrapped_links33["MULTISTORE"] = $req_data33; 
        $XMLSTORE = ArrayToXml::convert($wrapped_links33);
		}
		else
		{
		$XMLSTORE = NULL;
		}
        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $GRN_NO         = $request['GRN_NO'];
        $GRN_DT         = $request['GRN_DT'];
        $STID_REF        = NULL;
        $VID_REF        = $request['VID_REF'];
        $REMARKS        = $request['REMARKS'];
        $TYPE       = $request['TYPE'];

        $log_data = [ 
            $GRN_NO,$GRN_DT,$STID_REF,$VID_REF,$REMARKS,
            $CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT, 
            $XMLUDF,$XMLSTORE,$USERID, Date('Y-m-d'),Date('h:i:s.u'),
            $ACTIONNAME,$IPADDRESS,$TYPE
        ];

        $sp_result = DB::select('EXEC SP_GRN_RGP_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  

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

            $objResponse =  DB::table('TBL_TRN_IGRN01_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('GRNID','=',$id)
            ->first();

            $objStoreList       =   $this->getStoreList();
            $objPriority        =   $this->getPriority();
            $objlastdt          =   $this->getLastdt();
            $Employee           =   $this->getEmployee();

            $objStoreName       =[];
            if(isset($objResponse->STID_REF) && $objResponse->STID_REF !=""){
                $objStoreName       =   $this->getStoreName($objResponse->STID_REF);
            }

            $objVendorName      =[];
            if(isset($objResponse->VID_REF) && $objResponse->VID_REF !=""){
                $objVendorName      =   $this->getVendorName($objResponse->VID_REF,$objResponse->TYPE);
            }
           

            $objMAT = DB::select("SELECT 
                    T1.*,
                    T2.ICODE,T2.NAME AS ITEM_NAME,
                    CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                    CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                    T5.FROM_QTY,T5.TO_QTY,
                    T6.RGPID,T6.RGP_NO
                    FROM TBL_TRN_IGRN01_MAT T1
                    LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                    LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
                    LEFT JOIN TBL_MST_UOM T4 ON T1.ALT_UOMID_REF=T4.UOMID
                    LEFT JOIN TBL_MST_ITEM_UOMCONV T5 ON T1.ITEMID_REF=T5.ITEMID_REF AND T1.MAIN_UOMID_REF=T5.TO_UOMID_REF
                    LEFT JOIN TBL_TRN_IRGP01_HDR T6 ON T1.RGPID_REF=T6.RGPID
                    WHERE T1.GRNID_REF='$id' ORDER BY T1.GRN_MATID ASC
                    "); 


            $objCount1 = count($objMAT);  

            //dd($objMAT);
            
            $objUDF = DB::table('TBL_TRN_IGRN01_UDF')                    
            ->where('GRNID_REF','=',$id)
            ->orderBy('GRN_UDFID','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF); 

            $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
            WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by UOMCODE ASC', [$CYID_REF,  'A' ]); 

            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_GRNRGP")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('GRNRGPID')->from('TBL_MST_UDFFOR_GRNRGP')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                     
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                     
                    

            $objUdfData = DB::table('TBL_MST_UDFFOR_GRNRGP')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_GRNRGP")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('GRNRGPID')->from('TBL_MST_UDFFOR_GRNRGP')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                         
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                           
            

            $objUdfData2 = DB::table('TBL_MST_UDFFOR_GRNRGP')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF2)
            ->get()->toArray(); 

           

            $objItems=array();

           
            
            $objUOM=array();

           

            $objItemUOMConv=array();

            $FormId         =   $this->form_id;
            $AlpsStatus     =   $this->AlpsStatus();
            $ActionStatus   =   "";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
            return view($this->view.$FormId.'edit',compact([
                'AlpsStatus',
                'FormId',
                'objRights',
                'objResponse',
                'objStoreList',
                'objVendorName',
                'objStoreName',
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
                'ActionStatus',
                'TabSetting',
                'Employee'
        ]));      


        }
     
    }

    public function update(Request $request){
        
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

                  

                    $StoreArr[]=$batchid;
                }

                if(!empty($StoreArr)){
                    $StoreId    =   array_unique($StoreArr);
                    $STID_REF   =   implode(",",$StoreId);
                }
                else{
                    $STID_REF   =   NULL;
                }

                $req_data[$i] = [
                    'RGPID_REF'        => $request['RGP_NO_'.$i],
                    'ITEMID_REF'       => $request['ITEMID_REF_'.$i],
                    'BILL_CH_QTY'      => $request['SE_QTY_'.$i],
                    'MAIN_UOMID_REF'   => $request['MAIN_UOMID_REF_'.$i],
                    'RECEIVED_QTY_MU'  => $request['RECEIVED_QTY_MU_'.$i],
                    'SHORT_QTY'        => $request['SHORT_QTY_'.$i],
                    'STID'             =>  $STID_REF,
                    'REMARKS'          => $request['REMARKS_'.$i],
                    'BATCHQTY_REF'     => $request['HiddenRowId_'.$i],
                    'NATURE'          => $request['NATURE_'.$i],
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
		if(isset($req_data3))
		{
        $wrapped_links3["UDF1"] = $reqdata3; 
        $XMLUDF = ArrayToXml::convert($wrapped_links3);
		}
		else
		{
		$XMLUDF = NULL;
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
                        $qty                =   $keyid[1];
                        $dataArr[$batchid]  =   $qty;
                    }
                }


                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        $ExpID              =   explode("#",$key);
                        $STID_REF           =   $ExpID[0];
                        $RGPID_REF          =   $ExpID[1];
                        $BATCH_NO           =   $ExpID[2];
                        $ITEMID_REF         =   $ExpID[3];
                        $UOMID_REF          =   $ExpID[4];

                        

                        $req_data33[$i][] = [
                            'ITEMID_REF'    => $ITEMID_REF,
                            'STID_REF'      => $STID_REF,
                            'MAIN_UOMID_REF'     => $UOMID_REF,
                            'RECEIVED_QTYM'   => $val,  
                            'RGPID_REF'     => $RGPID_REF,
                        ];

                    }
                }
            }
        }


        if(isset($req_data33))
		{
        $wrapped_links33["MULTISTORE"] = $req_data33; 
        $XMLSTORE = ArrayToXml::convert($wrapped_links33);
		}
		else
		{
		$XMLSTORE = NULL;
		}

        
        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $GRN_NO         = $request['GRN_NO'];
        $GRN_DT         = $request['GRN_DT'];
        $STID_REF        = NULL;
        $VID_REF        = $request['VID_REF'];
        $REMARKS        = $request['REMARKS'];
        $TYPE       = $request['TYPE'];

        $log_data = [ 
            $GRN_NO,$GRN_DT,$STID_REF,$VID_REF,$REMARKS,
            $CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT, 
            $XMLUDF,$XMLSTORE,$USERID, Date('Y-m-d'),Date('h:i:s.u'),
            $ACTIONNAME,$IPADDRESS,$TYPE
        ];


        $sp_result = DB::select('EXEC SP_GRN_RGP_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $GRN_NO. ' Sucessfully Updated.']);

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

            $objRights = DB::table('TBL_MST_USERROLMAP')
			->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
			->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
			->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
			->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
			->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
			->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
			->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
			->first();

            $objResponse =  DB::table('TBL_TRN_IGRN01_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('GRNID','=',$id)
            ->first();

            $objStoreList       =   $this->getStoreList();
            $objPriority        =   $this->getPriority();
            $objlastdt          =   $this->getLastdt();
            $Employee           =   $this->getEmployee();

            $objStoreName       =[];
            if(isset($objResponse->STID_REF) && $objResponse->STID_REF !=""){
                $objStoreName       =   $this->getStoreName($objResponse->STID_REF);
            }

            $objVendorName      =[];
            if(isset($objResponse->VID_REF) && $objResponse->VID_REF !=""){
                $objVendorName      =   $this->getVendorName($objResponse->VID_REF,$objResponse->TYPE);
            }
           

            $objMAT = DB::select("SELECT 
                    T1.*,
                    T2.ICODE,T2.NAME AS ITEM_NAME,
                    CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                    CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                    T5.FROM_QTY,T5.TO_QTY,
                    T6.RGPID,T6.RGP_NO
                    FROM TBL_TRN_IGRN01_MAT T1
                    LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                    LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
                    LEFT JOIN TBL_MST_UOM T4 ON T1.ALT_UOMID_REF=T4.UOMID
                    LEFT JOIN TBL_MST_ITEM_UOMCONV T5 ON T1.ITEMID_REF=T5.ITEMID_REF AND T1.MAIN_UOMID_REF=T5.TO_UOMID_REF
                    LEFT JOIN TBL_TRN_IRGP01_HDR T6 ON T1.RGPID_REF=T6.RGPID
                    WHERE T1.GRNID_REF='$id' ORDER BY T1.GRN_MATID ASC
                    "); 


            $objCount1 = count($objMAT);  

            //dd($objMAT);
            
            $objUDF = DB::table('TBL_TRN_IGRN01_UDF')                    
            ->where('GRNID_REF','=',$id)
            ->orderBy('GRN_UDFID','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF); 

            $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
            WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by UOMCODE ASC', [$CYID_REF,  'A' ]); 

            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_GRNRGP")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('GRNRGPID')->from('TBL_MST_UDFFOR_GRNRGP')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                     
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                     
                    

            $objUdfData = DB::table('TBL_MST_UDFFOR_GRNRGP')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_GRNRGP")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('GRNRGPID')->from('TBL_MST_UDFFOR_GRNRGP')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                         
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                           
            

            $objUdfData2 = DB::table('TBL_MST_UDFFOR_GRNRGP')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF2)
            ->get()->toArray(); 

           

            $objItems=array();

           
            
            $objUOM=array();

           

            $objItemUOMConv=array();

            $FormId         =   $this->form_id;
            $AlpsStatus     =   $this->AlpsStatus();
            $ActionStatus   =   "disabled";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
            return view($this->view.$FormId.'view',compact([
                'AlpsStatus',
                'FormId',
                'objRights',
                'objResponse',
                'objStoreList',
                'objVendorName',
                'objStoreName',
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
                'ActionStatus',
                'TabSetting',
                'Employee'
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

                 

                    $StoreArr[]=$batchid;
                }

                if(!empty($StoreArr)){
                    $StoreId    =   array_unique($StoreArr);
                    $STID_REF   =   implode(",",$StoreId);
                }
                else{
                    $STID_REF   =   NULL;
                }

                $req_data[$i] = [
                    'RGPID_REF'        => $request['RGP_NO_'.$i],
                    'ITEMID_REF'       => $request['ITEMID_REF_'.$i],
                    'BILL_CH_QTY'      => $request['SE_QTY_'.$i],
                    'MAIN_UOMID_REF'   => $request['MAIN_UOMID_REF_'.$i],
                    'RECEIVED_QTY_MU'  => $request['RECEIVED_QTY_MU_'.$i],
                    'SHORT_QTY'        => $request['SHORT_QTY_'.$i],
                    'STID'             =>  $STID_REF,
                    'REMARKS'          => $request['REMARKS_'.$i],
                    'BATCHQTY_REF'     => $request['HiddenRowId_'.$i],
                    'NATURE'          => $request['NATURE_'.$i],
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

        if(isset($req_data3))
		{
        $wrapped_links3["UDF1"] = $reqdata3; 
        $XMLUDF = ArrayToXml::convert($wrapped_links3);
		}
		else
		{
		$XMLUDF = NULL;
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
                        $qty                =   $keyid[1];
                        $dataArr[$batchid]  =   $qty;
                    }
                }


                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        $ExpID              =   explode("#",$key);
                        $STID_REF           =   $ExpID[0];
                        $RGPID_REF          =   $ExpID[1];
                        $BATCH_NO           =   $ExpID[2];
                        $ITEMID_REF         =   $ExpID[3];
                        $UOMID_REF          =   $ExpID[4];

                       

                        $req_data33[$i][] = [
                            'ITEMID_REF'    => $ITEMID_REF,
                            'STID_REF'      => $STID_REF,
                            'MAIN_UOMID_REF'     => $UOMID_REF,
                            'RECEIVED_QTYM'   => $val,  
                            'RGPID_REF'     => $RGPID_REF,
                        ];

                    }
                }
            }
        }
		if(isset($req_data33))
		{
        $wrapped_links33["MULTISTORE"] = $req_data33; 
        $XMLSTORE = ArrayToXml::convert($wrapped_links33);
		}
		else
		{
		$XMLSTORE = NULL;
		}



        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $GRN_NO         = $request['GRN_NO'];
        $GRN_DT         = $request['GRN_DT'];
        $STID_REF        = NULL;
        $VID_REF        = $request['VID_REF'];
        $REMARKS        = $request['REMARKS'];
        $TYPE       = $request['TYPE'];

        $log_data = [ 
            $GRN_NO,$GRN_DT,$STID_REF,$VID_REF,$REMARKS,
            $CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT, 
            $XMLUDF,$XMLSTORE,$USERID, Date('Y-m-d'),Date('h:i:s.u'),
            $ACTIONNAME,$IPADDRESS,$TYPE
        ];

        $sp_result = DB::select('EXEC SP_GRN_RGP_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $GRN_NO. ' Sucessfully Approved.']);

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
        $TABLE      =   "TBL_TRN_IGRN01_HDR";
        $FIELD      =   "GRNID";
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
        $TABLE      =   "TBL_TRN_IGRN01_HDR";
        $FIELD      =   "GRNID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
         'NT'  => 'TBL_TRN_IGRN01_MAT',
        ];
        $req_data[1]=[
        'NT'  => 'TBL_TRN_IGRN01_MULTISTORE',
        ];
        $req_data[2]=[
        'NT'  => 'TBL_TRN_IGRN01_UDF',
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

            $objResponse = DB::table('TBL_TRN_IGRN01_HDR')->where('GRNID','=',$id)->first();

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
        
		$image_path         =   "docs/company".$CYID_REF."/GrnReturnableGatePass";     
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

        $GRN_NO  =   trim($request['GRN_NO']);
        $objLabel = DB::table('TBL_TRN_IGRN01_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('GRN_NO','=',$GRN_NO)
        ->select('GRNID')->first();

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


    public function getVendor(Request $request){

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $CODE       =   $request['CODE'];
        $NAME       =   $request['NAME'];
    
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
                <td class="ROW1"> <input type="checkbox" name="SELECT_VENDORID_REF[]" id="vendoridcode_'.$index.'"  class="clsvendorid" value="'.$VID.'" ></td>
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

    public function getVendorName($id,$type){

        if($type ==="Vendor"){
            return DB::table('TBL_MST_SUBLEDGER')
                ->where('BELONGS_TO','=','Vendor')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('SGLID','=',$id)    
                ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                ->first();
        }
        else if($type ==="Customer"){
            return DB::table('TBL_MST_SUBLEDGER')
                ->where('BELONGS_TO','=','Customer')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('SGLID','=',$id)    
                ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                ->first();
        }
        else if($type ==="Employee"){
            return DB::table('TBL_MST_EMPLOYEE')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('EMPID','=',$id)    
                ->select('EMPID AS VID','EMPCODE AS VCODE','FNAME AS NAME')
                ->first();
        }

    }

    public function getPriority(){
        return  DB::table('TBL_MST_PRIORITY')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('PRIORITYID','PRIORITYCODE','DESCRIPTIONS')
            ->get();
    }

    public function getPriorityName($id){
        return  DB::table('TBL_MST_PRIORITY')
            ->where('PRIORITYID','=',$id)
            ->select('PRIORITYID','PRIORITYCODE','DESCRIPTIONS')
            ->first();
    }

    public function getLastdt(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        return  DB::select('SELECT MAX(GRN_DT) GRN_DT FROM TBL_TRN_IGRN01_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
    }

    public function getItemDetails(Request $request){
        $Status = $request['status'];
        $CodeNoId = $request['RGP_NO'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $StdCost = 0;
        $Taxid = [];

        $AlpsStatus =   $this->AlpsStatus();
        
        $ObjItem =  DB::select("SELECT T1.ITEMID,T1.ICODE,T1.NAME,T1.ICID_REF,T1.ITEMGID_REF,T1.ALT_UOMID_REF,T2.* FROM TBL_MST_ITEM T1
        INNER JOIN TBL_TRN_IRGP01_MAT T2 ON T1.ITEMID=T2.ITEMID_REF
        WHERE T1.CYID_REF = '$CYID_REF'
        AND ( T1.DEACTIVATED IS NULL OR T1.DEACTIVATED = 0 ) AND T1.STATUS ='$Status' AND T2.RGPID_REF='$CodeNoId'");

        if(!empty($ObjItem)){

            foreach ($ObjItem as $index=>$dataRow){
                     
                $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                            WHERE  CYID_REF = ?  AND UOMID = ? 
                            AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                            [$CYID_REF, $dataRow->MAIN_UOMID_REF, 'A' ]);

                $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                            WHERE  CYID_REF = ?  AND UOMID = ? 
                            AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                            [$CYID_REF, $dataRow->ALT_UOMID_REF, $Status ]);
                    
                $ObjAltQTY =  DB::select('SELECT TOP 1  TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV  
                            WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                            [$dataRow->ITEMID,$dataRow->ALT_UOMID_REF ]);

                //$TOQTY =  isset($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 0;
                //$FROMQTY =  isset($ObjAltQTY[0]->FROM_QTY)? $ObjAltQTY[0]->FROM_QTY : 0;
                
                $TOQTY =  isset($dataRow->ISSUE_QTY)? $dataRow->ISSUE_QTY : 0;
                $FROMQTY =  isset($dataRow->ISSUE_QTY)? $dataRow->ISSUE_QTY : 0;

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


                    $desc6  =   $CodeNoId.'-'.$dataRow->ITEMID;

                    $row = '';
                    $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';
                    $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                    $row = $row.'<input type="hidden" id="uniquerowid_'.$desc6.'"   />';
                    $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'" data-desc6="'.$desc6.'"   
                    value="'.$dataRow->ITEMID.'"/></td>
                    <td style="width:10%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                    $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'"
                    value="'.$dataRow->NAME.'"/></td>';
                    $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"
                    value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                    $row = $row.'<td style="width:8%;" id="uomqty_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtuomqty_'.$dataRow->ITEMID.'" data-desc="'.$TOQTY.'"
                    value="'.$dataRow->ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>';
                    $row = $row.'<td style="width:8%;" id="irate_'.$dataRow->ITEMID.'"><input type="hidden" id="txtirate_'.$dataRow->ITEMID.'" data-desc="'.$FROMQTY.'"
                    value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                    $row = $row.'<td style="width:8%;" id="itax_'.$ObjItem[0]->ITEMID.'"><input type="hidden" id="txtitax_'.$ObjItem[0]->ITEMID.'" />'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                    <td style="width:8%;">'.$BusinessUnit.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                    <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
                    <td style="width:8%;">Authorized</td>
                    </tr>';
                    echo $row;    
                } 
                    
                    
                }           
                else{
                 echo '<tr><td> Record not found.</td></tr>';
                }
        exit();
    }


    public function getStoreDetails(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $ITEMID_REF = $request['ITEMID_REF'];
        $RGP_NO     = $request['RGP_NO'];
        $UOMID_REF  = $request['UOMID_REF'];
        $ROW_ID     = $request['ROW_ID'];
        $ITEMROWID  = $request['ITEMROWID'];
        $ACTION_TYPE= $request['ACTION_TYPE'] =="VIEW"?'disabled':'';
        $SRNOA      =   NULL;
        $BATCHNOA   =   NULL;

        $dataArr    =   array();

        if($ITEMROWID !=""){
            $exp        =   explode(",",$ITEMROWID);

            foreach($exp as $val){
                $keyid      =   explode("_",$val);
                $batchid    =   $keyid[0];
                $qty        =   $keyid[1];
                $dataArr[$batchid]  =   $qty;
            }
        }

        
        $objResponse =  DB::table('TBL_MST_ITEMCHECKFLAG')
            ->where('ITEMID_REF','=',$ITEMID_REF)
            ->select('SRNOA','BATCHNOA')
            ->first();

        if(!empty($objResponse)){
            $SRNOA      =   $objResponse->SRNOA;
            $BATCHNOA   =   $objResponse->BATCHNOA;
        }

        // $objBatch =  DB::SELECT("SELECT T1.BATCHID,T1.BATCH_CODE,T1.ITEMID_REF,T1.STID_REF,T1.SERIALNO,T1.UOMID_REF,
        // T1.CURRENT_QTY,T2.STCODE,T2.NAME AS STNAME,T3.UOMCODE,T3.DESCRIPTIONS AS UOMDESCRIPTIONS 
        // FROM TBL_MST_BATCH T1
        // LEFT JOIN TBL_MST_STORE T2 ON T1.STID_REF=T2.STID
        // LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
        // WHERE T1.STATUS='A' AND T1.ITEMID_REF='$ITEMID_REF' AND T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' 
        // AND T1.FYID_REF='$FYID_REF' AND T1.UOMID_REF='$UOMID_REF'
        // ");

        $objBatch =  DB::SELECT("SELECT T1.RGPID_REF,T1.BATCH_NO,T1.ITEMID_REF,T1.STID_REF,T1.SERIAL_NO,T1.UOMID_REF,
        T1.ISSUED_QTYM,T2.STCODE,T2.NAME AS STNAME,T3.UOMCODE,T3.DESCRIPTIONS AS UOMDESCRIPTIONS 
        FROM TBL_TRN_IRGP01_MULTISTORE T1
        LEFT JOIN TBL_MST_STORE T2 ON T1.STID_REF=T2.STID
        LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
        WHERE T1.ITEMID_REF='$ITEMID_REF' AND T1.UOMID_REF='$UOMID_REF' AND T1.RGPID_REF='$RGP_NO'
        ");

        //DD($objBatch);
     
        echo '<thead>';
        echo '<tr>';
        //echo $BATCHNOA =='1'?'<th>Batch / Lot No</th>':'';
        echo '<th>Store</th>';
        //echo $SRNOA =='1'?'<th>Serial No</th>':'';
        echo '<th>Main UoM (MU)</th>';
        //echo '<th>Stock-in-hand</th>';
        echo '<th>Received Qty (MU)</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach($objBatch as $key=>$val){

            $StoreRowId = $val->STID_REF.'#'.$val->RGPID_REF.'#'.$val->BATCH_NO.'#'.$val->ITEMID_REF.'#'.$val->UOMID_REF;

            $qtyvalue   =   array_key_exists($StoreRowId, $dataArr)?$dataArr[$StoreRowId]:'';

            if($request['ACTION_TYPE'] =="ADD"){
                $ISSUED_QTYM=$val->ISSUED_QTYM;
            }
            else{
                $ISSUED_QTYM=(floatval($val->ISSUED_QTYM)+floatval($qtyvalue));
            }

            echo '<tr  class="participantRow33">';
            //echo $BATCHNOA =='1'?'<td>'.$val->BATCH_CODE.'</td>':'';
            echo '<td>'.$val->STCODE.' - '.$val->STNAME.'</td>';
           // echo $SRNOA =='1'?'<td>'.$val->SERIALNO.'</td>':'';
            echo '<td>'.$val->UOMCODE.' - '.$val->UOMDESCRIPTIONS.'</td>';
            //echo '<td>'.$ISSUED_QTYM.'</td>';
            echo '<td style="width:10%"><input '.$ACTION_TYPE.' type="text" name="UserQty_'.$key.'" id="UserQty_'.$key.'" value="'.$qtyvalue.'" class="qtytext" onkeyup="checkStoreQty('.$ROW_ID.','.$ISSUED_QTYM.',this.value,'.$key.')" onkeypress="return isNumberDecimalKey(event,this)" autocomplete="off"  ></td>';
            echo '<td hidden><input type="hidden" name="BATCHID_'.$key.'" id="BATCHID_'.$key.'" value="'.$StoreRowId.'" class="qtytext" ></td>';
            echo '</tr>';
        }

        echo '</tbody>';
        exit();
    }

    public function getCodeNo(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $VID_REF        =   $request['id'];
        $TYPE           =   $request['TYPE'];
        $fieldid        =   $request['fieldid'];

        $ObjData =  DB::select("SELECT RGPID,RGP_NO,RGP_DT FROM TBL_TRN_IRGP01_HDR 
        WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF' 
        AND VID_REF='$VID_REF' AND  TYPE='$TYPE' AND STATUS='A'");

        // $ObjDataG =  DB::select("SELECT PO_NO FROM TBL_TRN_IMGE01_HDR 
        // WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF' 
        // AND VID_REF='$VID_REF' AND STATUS='A' AND GETYPE='RGP'");

        $ObjDataG =  DB::select("SELECT PO_NO FROM TBL_TRN_IMGE01_HDR 
        WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF' 
        AND VID_REF='$VID_REF' AND TYPE='$TYPE' AND STATUS='A' AND GETYPE='RGP'");


        $PoArrId=array();
        foreach($ObjDataG as $val){
            $poArr              =   explode(",",$val->PO_NO);
            foreach($poArr as $row){
                $PoArrId[]=$row;
            }
        }

        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){
                if(in_array($dataRow->RGPID,$PoArrId)){
                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="rgpcode_'.$dataRow->RGPID .'"  class="clssqid" value="'.$dataRow->RGPID.'" ></td>
                <td class="ROW2">'.$dataRow->RGP_NO;
                $row = $row.'<input type="hidden" id="txtrgpcode_'.$dataRow->RGPID.'" data-desc="'.$dataRow->RGP_NO.'"  data-descdate="'.$dataRow->RGP_DT.'" value="'.$dataRow->RGPID.'"/></td>
                <td class="ROW3">'.$dataRow->RGP_DT.'</td></tr>';
                echo $row;
                }
            }
        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
    }

    public function getEmployee(){
        return DB::table('TBL_MST_EMPLOYEE')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('EMPID','EMPCODE','FNAME','MNAME','LNAME')
            ->get();
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
    

    
}
