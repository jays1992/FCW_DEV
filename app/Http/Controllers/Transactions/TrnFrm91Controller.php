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

class TrnFrm91Controller extends Controller{

    protected $form_id  = 91;
    protected $vtid_ref = 91;
    protected $view     = "transactions.inventory.NonReturnableGatePass.trnfrm";
   
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

        $objDataList	=	DB::select("select hdr.NRGPID,hdr.NRGP_NO,hdr.NRGP_DT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.NRGPID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                            ) AS CREATED_BY,
                            hdr.STATUS, sl.SLNAME,
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
                            inner join TBL_TRN_NRGP01_HDR hdr
                            on a.VID = hdr.NRGPID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_SUBLEDGER sl ON hdr.VID_REF = sl.SGLID 
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.NRGPID DESC ");

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
        $MrsNo              =   $this->getMrsNo();

        $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
        WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by UOMCODE ASC', [$CYID_REF,  'A' ]); 
        
        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_NRGP01_HDR',
            'HDR_ID'=>'NRGPID',
            'HDR_DOC_NO'=>'NRGP_NO',
            'HDR_DOC_DT'=>'NRGP_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        
   
        $table       =  "TBL_MST_UDFFOR_NRGP";
        $ObjUnionUDF =  DB::table($table)->select('*')
        ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF){       
            $query->select("UDFNRGPID")->from('TBL_MST_UDFFOR_NRGP')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                             
        })
        ->where('DEACTIVATED','=',0)
        ->where('STATUS','<>','C')                    
        ->where('CYID_REF','=',$CYID_REF);
                       
                   

        $objUdfData = DB::table($table)
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
                'MrsNo',
                'doc_req','docarray'
        ]));       
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
                    $keyid              =   explode("_",$val);
                    $batchid            =   $keyid[0];

                    $objStore =  DB::table('TBL_MST_BATCH')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('BATCHID','=',$batchid)
                        ->where('STATUS','=',"A")
                        ->select('STID_REF')
                        ->first();

                    $StoreArr[]=$objStore->STID_REF;
                }

                if(!empty($StoreArr)){
                    $StoreId    =   array_unique($StoreArr);
                    $STID_REF   =   implode(",",$StoreId);
                }
                else{
                    $STID_REF   =   NULL;
                }

                $req_data[$i] = [
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'MAIN_UOMID_REF'    => $request['MAIN_UOMID_REF_'.$i],
                    'NRGP_QTY'          => $request['SE_QTY_'.$i],
                    'ITEM_SPECI'        => $request['Itemspec_'.$i],
                    'REASON_FOR_NRGP'   => $request['REMARKS_'.$i],
                    'STID_REF'          =>  $STID_REF, 
                    'BATCH_QTY_REF'     => $request['HiddenRowId_'.$i],
                    'MRSID_REF'         => $request['MRSID_REF_'.$i],
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
                 
                        $objBatch =  DB::table('TBL_MST_BATCH')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('BATCHID','=',$key)
                        ->where('STATUS','=',"A")
                        ->select('BATCHID','BATCH_CODE','ITEMID_REF','STID_REF','SERIALNO','UOMID_REF','CURRENT_QTY')
                        ->first();

                        $req_data33[$i][] = [
                            'ITEMID_REF'    => $ITEMID_REF,
                            'BATCH_NO'      => $objBatch->BATCH_CODE,
                            'STID_REF'      => $objBatch->STID_REF,
                            'SERIAL_NO'     => $objBatch->SERIALNO,
                            'UOMID_REF'     => $objBatch->UOMID_REF,
                            'STOCK_INHAND'  => $objBatch->CURRENT_QTY,
                            'ISSUED_QTYM'   => $val,
                            'MRSID_REF'     => $request['MRSID_REF_'.$i],
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

        $NRGP_NO = $request['NRGP_NO'];
        $NRGP_DT = $request['NRGP_DT'];
        $VID_REF = $request['VID_REF'];
        $PRIORITYID_REF = $request['PRIORITYID_REF'];
        $PURPOSE = $request['PURPOSE'];
        $NRGP_STATUS     = (isset($request['NRGP_STATUS'])!="true" ? 1 : 0);

        $log_data = [ 
            $NRGP_NO,$NRGP_DT,$VID_REF,$PRIORITYID_REF,$PURPOSE,
            $CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT, 
            $XMLUDF,$XMLSTORE,$USERID, Date('Y-m-d'), Date('h:i:s.u'),
            $ACTIONNAME,$IPADDRESS,$NRGP_STATUS
        ];

        //dd($log_data);

        $sp_result = DB::select('EXEC SP_NRGP_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  


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
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $objResponse =  DB::table('TBL_TRN_NRGP01_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('NRGPID','=',$id)
            ->first();

            $objStoreList       =   $this->getStoreList();
            $objPriority        =   $this->getPriority();
            $MrsNo              =   $this->getMrsNo();
            
            $objStoreName       =   [];
            if(isset($objResponse->STID_REF) && $objResponse->STID_REF !=""){
                $objStoreName       =   $this->getStoreName($objResponse->STID_REF);
            }

            $objVendorName      =[];
            if(isset($objResponse->VID_REF) && $objResponse->VID_REF !=""){
                $objVendorName      =   $this->getVendorName($objResponse->VID_REF);
            }

            $objPriorityName    =[];
            if(isset($objResponse->PRIORITYID_REF) && $objResponse->PRIORITYID_REF !=""){
                $objPriorityName    =   $this->getPriorityName($objResponse->PRIORITYID_REF);
            }
           
            $objMAT = DB::select("SELECT 
            T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,
            CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
            T4.MRS_NO
            FROM TBL_TRN_NRGP01_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
            LEFT JOIN TBL_TRN_MRQS01_HDR T4 ON T1.MRSID_REF=T4.MRSID
            WHERE T1.NRGPID_REF='$id' ORDER BY T1.NRGP_MATID ASC
            "); 

            $objCount1 = count($objMAT);  

            $objUDF = DB::table('TBL_TRN_NRGP01_UDF')                    
            ->where('NRGPID_REF','=',$id)
            ->orderBy('NRGP_UDFID','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF); 

            $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
            WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by UOMCODE ASC', [$CYID_REF,  'A' ]); 

            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_NRGP")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                        {       
                        $query->select('UDFNRGPID')->from('TBL_MST_UDFFOR_NRGP')
                                        ->where('STATUS','=','A')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                              
            })->where('DEACTIVATED','=',0)
            ->where('STATUS','<>','C')                    
            ->where('CYID_REF','=',$CYID_REF);
                            


            $objUdfData = DB::table('TBL_MST_UDFFOR_NRGP')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_NRGP")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
            {       
            $query->select('UDFNRGPID')->from('TBL_MST_UDFFOR_NRGP')
                            ->where('PARENTID','=',0)
                            ->where('DEACTIVATED','=',0)
                            ->where('CYID_REF','=',$CYID_REF);
                                                 
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                             
            $objUdfData2 = DB::table('TBL_MST_UDFFOR_NRGP')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF2)
            ->get()->toArray(); 
            
            $FormId         =   $this->form_id;
            $AlpsStatus =   $this->AlpsStatus();
            $ActionStatus   =   "";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
            return view($this->view.$FormId.'edit',compact(
                [
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
                'objUdfData2',
                'objPriority',
                'objPriorityName',
                'ActionStatus',
                'TabSetting',
                'MrsNo'   
                ]
            ));      

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
                    $keyid              =   explode("_",$val);
                    $batchid            =   $keyid[0];

                    $objStore =  DB::table('TBL_MST_BATCH')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('BATCHID','=',$batchid)
                        ->where('STATUS','=',"A")
                        ->select('STID_REF')
                        ->first();

                    $StoreArr[]=$objStore->STID_REF;
                }

                if(!empty($StoreArr)){
                    $StoreId    =   array_unique($StoreArr);
                    $STID_REF   =   implode(",",$StoreId);
                }
                else{
                    $STID_REF   =   NULL;
                }

                $req_data[$i] = [
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'MAIN_UOMID_REF'    => $request['MAIN_UOMID_REF_'.$i],
                    'NRGP_QTY'          => $request['SE_QTY_'.$i],
                    'ITEM_SPECI'        => $request['Itemspec_'.$i],
                    'REASON_FOR_NRGP'   => $request['REMARKS_'.$i],
                    'STID_REF'          =>  $STID_REF, 
                    'BATCH_QTY_REF'     => $request['HiddenRowId_'.$i],
                    'MRSID_REF'         => $request['MRSID_REF_'.$i],
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
                 
                        $objBatch =  DB::table('TBL_MST_BATCH')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('BATCHID','=',$key)
                        ->where('STATUS','=',"A")
                        ->select('BATCHID','BATCH_CODE','ITEMID_REF','STID_REF','SERIALNO','UOMID_REF','CURRENT_QTY')
                        ->first();

                        $req_data33[$i][] = [
                            'ITEMID_REF'    => $ITEMID_REF,
                            'BATCH_NO'      => $objBatch->BATCH_CODE,
                            'STID_REF'      => $objBatch->STID_REF,
                            'SERIAL_NO'     => $objBatch->SERIALNO,
                            'UOMID_REF'     => $objBatch->UOMID_REF,
                            'STOCK_INHAND'  => $objBatch->CURRENT_QTY,
                            'ISSUED_QTYM'   => $val,
                            'MRSID_REF'     => $request['MRSID_REF_'.$i],
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

        $NRGP_NO = $request['NRGP_NO'];
        $NRGP_DT = $request['NRGP_DT'];
        $VID_REF = $request['VID_REF'];
        $PRIORITYID_REF = $request['PRIORITYID_REF'];
        $PURPOSE = $request['PURPOSE'];
        $NRGP_STATUS     = $request['NRGP_STATUS'];

        $log_data = [ 
            $NRGP_NO,$NRGP_DT,$VID_REF,$PRIORITYID_REF,$PURPOSE,
            $CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT, 
            $XMLUDF,$XMLSTORE,$USERID, Date('Y-m-d'), Date('h:i:s.u'),
            $ACTIONNAME,$IPADDRESS,$NRGP_STATUS
        ];

        //dd($log_data);

        $sp_result = DB::select('EXEC SP_NRGP_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $NRGP_NO. ' Sucessfully Updated.']);

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
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $objResponse =  DB::table('TBL_TRN_NRGP01_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('NRGPID','=',$id)
            ->first();

            $objStoreList       =   $this->getStoreList();
            $objPriority        =   $this->getPriority();
            $MrsNo              =   $this->getMrsNo();
            
            $objStoreName       =   [];
            if(isset($objResponse->STID_REF) && $objResponse->STID_REF !=""){
                $objStoreName       =   $this->getStoreName($objResponse->STID_REF);
            }

            $objVendorName      =[];
            if(isset($objResponse->VID_REF) && $objResponse->VID_REF !=""){
                $objVendorName      =   $this->getVendorName($objResponse->VID_REF);
            }

            $objPriorityName    =[];
            if(isset($objResponse->PRIORITYID_REF) && $objResponse->PRIORITYID_REF !=""){
                $objPriorityName    =   $this->getPriorityName($objResponse->PRIORITYID_REF);
            }
           
            $objMAT = DB::select("SELECT 
            T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,
            CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE
            FROM TBL_TRN_NRGP01_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
            WHERE T1.NRGPID_REF='$id' ORDER BY T1.NRGP_MATID ASC
            "); 

            $objCount1 = count($objMAT);  

            $objUDF = DB::table('TBL_TRN_NRGP01_UDF')                    
            ->where('NRGPID_REF','=',$id)
            ->orderBy('NRGP_UDFID','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF); 

            $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
            WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by UOMCODE ASC', [$CYID_REF,  'A' ]); 

            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_NRGP")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                        {       
                        $query->select('UDFNRGPID')->from('TBL_MST_UDFFOR_NRGP')
                                        ->where('STATUS','=','A')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                              
            })->where('DEACTIVATED','=',0)
            ->where('STATUS','<>','C')                    
            ->where('CYID_REF','=',$CYID_REF);
                            


            $objUdfData = DB::table('TBL_MST_UDFFOR_NRGP')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_NRGP")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
            {       
            $query->select('UDFNRGPID')->from('TBL_MST_UDFFOR_NRGP')
                            ->where('PARENTID','=',0)
                            ->where('DEACTIVATED','=',0)
                            ->where('CYID_REF','=',$CYID_REF);
                                                 
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                             
            $objUdfData2 = DB::table('TBL_MST_UDFFOR_NRGP')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF2)
            ->get()->toArray(); 
            
            $FormId         =   $this->form_id;
            $AlpsStatus =   $this->AlpsStatus();
            $ActionStatus   =   "disabled";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
            return view($this->view.$FormId.'view',compact(
                [
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
                'objUdfData2',
                'objPriority',
                'objPriorityName',
                'ActionStatus',
                'TabSetting',
                'MrsNo'  
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

                $StoreArr   =   array();
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $exp        =   explode(",",$ITEMROWID);

                foreach($exp as $val){
                    $keyid              =   explode("_",$val);
                    $batchid            =   $keyid[0];

                    $objStore =  DB::table('TBL_MST_BATCH')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('BATCHID','=',$batchid)
                        ->where('STATUS','=',"A")
                        ->select('STID_REF')
                        ->first();

                    $StoreArr[]=$objStore->STID_REF;
                }

                if(!empty($StoreArr)){
                    $StoreId    =   array_unique($StoreArr);
                    $STID_REF   =   implode(",",$StoreId);
                }
                else{
                    $STID_REF   =   NULL;
                }

                $req_data[$i] = [
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'MAIN_UOMID_REF'    => $request['MAIN_UOMID_REF_'.$i],
                    'NRGP_QTY'          => $request['SE_QTY_'.$i],
                    'ITEM_SPECI'        => $request['Itemspec_'.$i],
                    'REASON_FOR_NRGP'   => $request['REMARKS_'.$i],
                    'STID_REF'          =>  $STID_REF, 
                    'BATCH_QTY_REF'     => $request['HiddenRowId_'.$i],
                    'MRSID_REF'         => $request['MRSID_REF_'.$i],
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
                 
                        $objBatch =  DB::table('TBL_MST_BATCH')
                        ->where('ITEMID_REF','=',$ITEMID_REF)
                        ->where('BATCHID','=',$key)
                        ->where('STATUS','=',"A")
                        ->select('BATCHID','BATCH_CODE','ITEMID_REF','STID_REF','SERIALNO','UOMID_REF','CURRENT_QTY')
                        ->first();

                        $req_data33[$i][] = [
                            'ITEMID_REF'    => $ITEMID_REF,
                            'BATCH_NO'      => $objBatch->BATCH_CODE,
                            'STID_REF'      => $objBatch->STID_REF,
                            'SERIAL_NO'     => $objBatch->SERIALNO,
                            'UOMID_REF'     => $objBatch->UOMID_REF,
                            'STOCK_INHAND'  => $objBatch->CURRENT_QTY,
                            'ISSUED_QTYM'   => $val,
                            'MRSID_REF'         => $request['MRSID_REF_'.$i],
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

        $NRGP_NO = $request['NRGP_NO'];
        $NRGP_DT = $request['NRGP_DT'];
        $VID_REF = $request['VID_REF'];
        $PRIORITYID_REF = $request['PRIORITYID_REF'];
        $PURPOSE = $request['PURPOSE'];
        $NRGP_STATUS     = $request['NRGP_STATUS'];

        $log_data = [ 
            $NRGP_NO,$NRGP_DT,$VID_REF,$PRIORITYID_REF,$PURPOSE,
            $CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT, 
            $XMLUDF,$XMLSTORE,$USERID, Date('Y-m-d'), Date('h:i:s.u'),
            $ACTIONNAME,$IPADDRESS,$NRGP_STATUS
        ];

       
        $sp_result = DB::select('EXEC SP_NRGP_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  
            
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
        $TABLE      =   "TBL_TRN_NRGP01_HDR";
        $FIELD      =   "NRGPID";
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
        $TABLE      =   "TBL_TRN_NRGP01_HDR";
        $FIELD      =   "NRGPID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
         'NT'  => 'TBL_TRN_NRGP01_MAT',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_NRGP01_UDF',
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

            $objResponse = DB::table('TBL_TRN_NRGP01_HDR')->where('NRGPID','=',$id)->first();

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
        
		$image_path         =   "docs/company".$CYID_REF."/NonReturnableGatePass";     
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

        $NRGP_NO  =   trim($request['NRGP_NO']);
        $objLabel = DB::table('TBL_TRN_NRGP01_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('NRGP_NO','=',$NRGP_NO)
        ->select('NRGPID')->first();

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

    public function getVendorList(){
        return  DB::table('TBL_MST_VENDOR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('VID','VCODE','NAME')
            ->get();
    }

    public function getVendorName($id){
  
            return DB::table('TBL_MST_SUBLEDGER')
            ->where('BELONGS_TO','=','Vendor')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('SGLID','=',$id)    
            ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
            ->first();

    }

    public function getPriority(){
        return  DB::table('TBL_MST_PRIORITY')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('PRIORITYID','PRIORITYCODE','DESCRIPTIONS')
            ->get();
    }

    public function getMrsNo(){
        return  DB::table('TBL_TRN_MRQS01_HDR')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('FYID_REF','=',Session::get('FYID_REF'))
                ->where('STATUS','=','A')
                ->where('MRS_TYPE','=','NRGP')
                ->select('MRSID','MRS_NO','MRS_DT')
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

        return  DB::select('SELECT MAX(NRGP_DT) NRGP_DT FROM TBL_TRN_NRGP01_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
    }

    public function getItemDetails(Request $request){

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
        $MRSID_REF  =   $request['MRSID_REF'];

        if($MRSID_REF !=''){
            $ObjItem    =   DB::select("SELECT
                            T2.ITEMID,
                            T2.ICODE,
                            T2.NAME,
                            T1.ITEM_SPECI,
                            T1.MAIN_UOMID_REF,
                            CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS Main_UOM,
                            '' AS ALT_UOMID_REF,
                            '' AS Alt_UOM,
                            T1.QTY AS FROMQTY,
                            T1.QTY AS TOQTY,
                            T2.STDCOST,
                            CONCAT(T4.GROUPCODE,'-',T4.GROUPNAME) AS GroupName,
                            CONCAT(T5.ICCODE,'-',T5.DESCRIPTIONS) AS Categoryname,
                            CONCAT(T6.BUCODE,'-',T6.BUNAME) AS BusinessUnit,
                            T2.ALPS_PART_NO,
                            T2.CUSTOMER_PART_NO,
                            T2.OEM_PART_NO
                            FROM TBL_TRN_MRQS01_MAT T1
                            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                            LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
                            LEFT JOIN TBL_MST_ITEMGROUP T4 ON T2.ITEMGID_REF=T4.ITEMGID
                            LEFT JOIN TBL_MST_ITEMCATEGORY T5 ON T2.ICID_REF=T5.ICID
                            LEFT JOIN TBL_MST_BUSINESSUNIT T6 ON T2.BUID_REF=T6.BUID
                            WHERE T1.MRSID_REF='$MRSID_REF' ORDER BY T1.MRS_MATID ASC
                            ");

        }
        else{
            $sp_popup = [
                $CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART
            ]; 
            $ObjItem = DB::select('EXEC sp_get_items_popup_enquiry ?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);
        }
                
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

                $CHECK_UNIQUE       =   $MRSID_REF.$ITEMID; 
      
                $row.=' <tr id="item_'.$ITEMID.'" class="clsitemid">
                        <td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1"  ></td>
                        <td style="width:10%;">'.$ICODE.'<input type="hidden" id="txtitem_'.$ITEMID.'" data-desc="'.$ICODE.'" data-desc1="'.$CHECK_UNIQUE.'" value="'.$ITEMID.'"/></td>
                        <td style="width:10%;" id="itemname_'.$ITEMID.'" >'.$NAME.'<input type="hidden" id="txtitemname_'.$ITEMID.'" data-desc="'.$ITEM_SPECI.'" value="'.$NAME.'"/></td>
                        <td style="width:8%;" id="itemuom_'.$ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ITEMID.'" data-desc="'.$Alt_UOM.'" value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>
                        <td style="width:8%;" id="uomqty_'.$ITEMID.'" ><input type="hidden" id="txtuomqty_'.$ITEMID.'" data-desc="'.$TOQTY.'" value="'.$ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>
                        <td style="width:8%;" id="irate_'.$ITEMID.'"><input type="hidden" id="txtirate_'.$ITEMID.'" data-desc="'.$FROMQTY.'" value="'.$STDCOST.'"/>'.$GroupName.'</td>
                        <td style="width:8%;" id="itax_'.$ITEMID.'"><input type="hidden" id="txtitax_'.$ITEMID.'" />'.$Categoryname.'</td>
                        <td style="width:8%;">'.$BusinessUnit.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' > '.$CUSTOMER_PART_NO.'</td>
                        <td style="width:8%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>
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


    public function getStoreDetails(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $ITEMID_REF = $request['ITEMID_REF'];
        $UOMID_REF = $request['UOMID_REF'];
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

        $objBatch =  DB::SELECT("SELECT T1.BATCHID,T1.BATCH_CODE,T1.ITEMID_REF,T1.STID_REF,T1.SERIALNO,T1.UOMID_REF,
        T1.CURRENT_QTY,T2.STCODE,T2.NAME AS STNAME,T3.UOMCODE,T3.DESCRIPTIONS AS UOMDESCRIPTIONS 
        FROM TBL_MST_BATCH T1
        LEFT JOIN TBL_MST_STORE T2 ON T1.STID_REF=T2.STID
        LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
        WHERE T1.STATUS='A' AND T1.ITEMID_REF='$ITEMID_REF' AND T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' 
         AND T1.UOMID_REF='$UOMID_REF'
        ");
     
        echo '<thead>';
        echo '<tr>';
        echo $BATCHNOA =='1'?'<th>Batch / Lot No</th>':'';
        echo '<th>Store</th>';
        echo $SRNOA =='1'?'<th>Serial No</th>':'';
        echo '<th>Main UoM</th>';
        echo '<th>Stock-in-hand</th>';
        echo '<th>Issue Qty</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach($objBatch as $key=>$val){

            $qtyvalue   =   array_key_exists($val->BATCHID, $dataArr)?$dataArr[$val->BATCHID]:'';

            if($request['ACTION_TYPE'] =="ADD"){
                $CURRENT_QTY=$val->CURRENT_QTY;
            }
            else{
                $CURRENT_QTY=(floatval($val->CURRENT_QTY)+floatval($qtyvalue));
            }

            echo '<tr  class="participantRow33">';
            echo $BATCHNOA =='1'?'<td>'.$val->BATCH_CODE.'</td>':'';
            echo '<td>'.$val->STCODE.' - '.$val->STNAME.'</td>';
            echo $SRNOA =='1'?'<td>'.$val->SERIALNO.'</td>':'';
            echo '<td>'.$val->UOMCODE.' - '.$val->UOMDESCRIPTIONS.'</td>';
            echo '<td>'.$CURRENT_QTY.'</td>';
            echo '<td style="width:10%"><input '.$ACTION_TYPE.' type="text" name="UserQty_'.$key.'" id="UserQty_'.$key.'" value="'.$qtyvalue.'" class="qtytext" onkeyup="checkStoreQty('.$ROW_ID.','.$CURRENT_QTY.',this.value,'.$key.')" onkeypress="return isNumberDecimalKey(event,this)" autocomplete="off"  ></td>';
            echo '<td hidden><input type="hidden" name="BATCHID_'.$key.'" id="BATCHID_'.$key.'" value="'.$val->BATCHID.'" class="qtytext" ></td>';
            echo '</tr>';
        }

        echo '</tbody>';
        exit();
    }

    
    
    
}
