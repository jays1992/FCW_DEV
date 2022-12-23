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

class TrnFrm159Controller extends Controller{

    protected $form_id  = 159;
    protected $vtid_ref = 94;
    protected $view     = "transactions.inventory.GrnGateEntry.trnfrm";
   
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
                            inner join TBL_TRN_IGRN02_HDR hdr
                            on a.VID = hdr.GRNID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_SUBLEDGER sl ON hdr.VID_REF = sl.SGLID  
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.GRNID DESC ");

        foreach($objDataList as $key=>$objResponse){

            $GE_NO  =   NULL;
            $BOE_NO =   NULL;
            if(isset($objResponse->GRNID) && $objResponse->GRNID !=""){
                $GRNID      =   $objResponse->GRNID;
                $DataRow    =   DB::select("SELECT TOP 1 T2.GE_NO,T2.BOE_NO 
                                FROM TBL_TRN_IGRN02_MAT T1 
                                LEFT JOIN TBL_TRN_IMGE01_HDR T2 ON T1.GEID_REF=T2.GEID
                                WHERE T1.GRNID_REF='$GRNID'");

                $GE_NO      =   isset($DataRow[0]->GE_NO) && $DataRow[0]->GE_NO !=''?$DataRow[0]->GE_NO:NULL;
                $BOE_NO     =   isset($DataRow[0]->BOE_NO) && $DataRow[0]->BOE_NO !=''?$DataRow[0]->BOE_NO:NULL;
            }

            $objDataList[$key]->GE_NO   =   $GE_NO;
            $objDataList[$key]->BOE_NO  =   $BOE_NO;
        }

                    

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
        $GRNID       =   $myValue['GRNID'];
        $Flag       =   $myValue['Flag'];

        // $objSalesOrder = DB::table('TBL_TRN_SLSO01_HDR')
        // ->where('TBL_TRN_SLSO01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        // ->where('TBL_TRN_SLSO01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        // ->where('TBL_TRN_SLSO01_HDR.SOID','=',$SOID)
        // ->select('TBL_TRN_SLSO01_HDR.*')
        // ->first();
        
        
        $ssrs = new \SSRS\Report('http://103.139.58.23:8181//ReportServer/', array('username' => 'Administrator', 'password' => 'VRt+wDPuDYLwxxC'));
        $result = $ssrs->loadReport('/ERP/GRNGEPrint');
        
        $reportParameters = array(
            'GRNID' => $GRNID,
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
                <td class="ROW1"> <input type="checkbox" name="SELECT_VID_REF[]" id="vendoridcode_'.$index.'"  class="clsvendorid" value="'.$VID.'" ></td>
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

    public function add(){       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
       
        $objStoreList       =   $this->getStoreList();
        $objPriority        =   $this->getPriority();
        $objlastdt          =   $this->getLastdt();

        $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
        WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by UOMCODE ASC', [$CYID_REF,  'A' ]);  
        
        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_IGRN02_HDR',
            'HDR_ID'=>'GRNID',
            'HDR_DOC_NO'=>'GRN_NO',
            'HDR_DOC_DT'=>'GRN_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        
   
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_GRNGE")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                {       
                                $query->select('GRNGEID')->from('TBL_MST_UDFFOR_GRNGE')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);                      
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);                   
                   

        $objUdfData = DB::table('TBL_MST_UDFFOR_GRNGE')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();  

        $objCountUDF = count($objUdfData);

        $FormId     =   $this->form_id;

        $AlpsStatus     =   $this->AlpsStatus();
        $checkCompany   =   $this->checkCompany('zep');

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        //dd($AlpsStatus); 
        
        return view($this->view.$FormId.'add',compact([
                'FormId',
                'objStoreList',
                'objUdfData',
               
                'objCountUDF',
               
                'objlastdt',
                'objPriority',
                'objUOM',
                'AlpsStatus',
                'checkCompany',
                'TabSetting',
                'doc_req','docarray'
        ]));       
    }

    public function save(Request $request) {

        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];

        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                if($request['GE_TYPE'] =="IPO"){
                    $req_data[$i] = [
                        'GEID_REF'          =>  $request['RGP_NO_'.$i],
                        'IPOID_REF'         =>  $request['POID_REF_'.$i],
                        'ITEMID_REF'        =>  $request['ITEMID_REF_'.$i],
                        'PO_PENDING_QTY'    =>  $request['PO_PENDING_QTY_'.$i],
                        'BILL_QTY'          =>  $request['SE_QTY_'.$i],
                        'MAIN_UOMID_REF'    =>  $request['MAIN_UOMID_REF_'.$i],
                        'RECEIVED_QTY_MU'   =>  $request['RECEIVED_QTY_MU_'.$i],
                        'ALT_UOMID_REF'     =>  $request['ALT_UOMID_REF_'.$i],
                        'RECEIVED_QTY_AU'   =>  $request['RECEIVED_QTY_AU_'.$i],
                        'SHORT_QTY'         =>  $request['SHORT_QTY_'.$i],
                        'RATE'              =>  $request['RATE_'.$i],
                        'STID'              =>  $request['STORE_ID_'.$i],
                        'REMARKS'           =>  $request['REMARKS_'.$i],
                        'STORE_NAME'        =>  $request['STORE_NAME_'.$i],
                        'BATCHQTY_REF'      =>  $request['HiddenRowId_'.$i],
                        'MRSID_REF'         =>  $request['MRSID_REF_'.$i],
                        'PIID_REF'          =>  $request['PIID_REF_'.$i],
                        'RFQID_REF'         =>  $request['RFQID_REF_'.$i],
                        'VQID_REF'          =>  $request['VQID_REF_'.$i]
                    ];
                }
                else if($request['GE_TYPE'] =="BPO"){
                    $req_data[$i] = [
                        'GEID_REF'          =>  $request['RGP_NO_'.$i],
                        'BPOID_REF'         =>  $request['POID_REF_'.$i],
                        'ITEMID_REF'        =>  $request['ITEMID_REF_'.$i],
                        'PO_PENDING_QTY'    =>  $request['PO_PENDING_QTY_'.$i],
                        'BILL_QTY'          =>  $request['SE_QTY_'.$i],
                        'MAIN_UOMID_REF'    =>  $request['MAIN_UOMID_REF_'.$i],
                        'RECEIVED_QTY_MU'   =>  $request['RECEIVED_QTY_MU_'.$i],
                        'ALT_UOMID_REF'     =>  $request['ALT_UOMID_REF_'.$i],
                        'RECEIVED_QTY_AU'   =>  $request['RECEIVED_QTY_AU_'.$i],
                        'SHORT_QTY'         =>  $request['SHORT_QTY_'.$i],
                        'RATE'              =>  $request['RATE_'.$i],
                        'STID'              =>  $request['STORE_ID_'.$i],
                        'REMARKS'           =>  $request['REMARKS_'.$i],
                        'STORE_NAME'        =>  $request['STORE_NAME_'.$i],
                        'BATCHQTY_REF'      =>  $request['HiddenRowId_'.$i],
                        'MRSID_REF'         =>  $request['MRSID_REF_'.$i],
                        'PIID_REF'          =>  $request['PIID_REF_'.$i],
                        'RFQID_REF'         =>  $request['RFQID_REF_'.$i],
                        'VQID_REF'          =>  $request['VQID_REF_'.$i],
                    ];
                }
                else{
                    $req_data[$i] = [
                        'GEID_REF'          =>  $request['RGP_NO_'.$i],
                        'POID_REF'          =>  $request['POID_REF_'.$i],
                        'ITEMID_REF'        =>  $request['ITEMID_REF_'.$i],
                        'PO_PENDING_QTY'    =>  $request['PO_PENDING_QTY_'.$i],
                        'BILL_QTY'          =>  $request['SE_QTY_'.$i],
                        'MAIN_UOMID_REF'    =>  $request['MAIN_UOMID_REF_'.$i],
                        'RECEIVED_QTY_MU'   =>  $request['RECEIVED_QTY_MU_'.$i],
                        'ALT_UOMID_REF'     =>  $request['ALT_UOMID_REF_'.$i],
                        'RECEIVED_QTY_AU'   =>  $request['RECEIVED_QTY_AU_'.$i],
                        'SHORT_QTY'         =>  $request['SHORT_QTY_'.$i],
                        'RATE'              =>  $request['RATE_'.$i],
                        'STID'              =>  $request['STORE_ID_'.$i],
                        'REMARKS'           =>  $request['REMARKS_'.$i],
                        'STORE_NAME'        =>  $request['STORE_NAME_'.$i],
                        'BATCHQTY_REF'      =>  $request['HiddenRowId_'.$i],
                        'MRSID_REF'         =>  $request['MRSID_REF_'.$i],
                        'PIID_REF'          =>  $request['PIID_REF_'.$i],
                        'RFQID_REF'         =>  $request['RFQID_REF_'.$i],
                        'VQID_REF'          =>  $request['VQID_REF_'.$i],
                    ];
                }
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

       
        
        if($r_count2 > 0){
            $wrapped_links3["UDF1"] = $reqdata3; 
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

                        $dataArr[$batchid]['RECEIVED_QTYM']  =   $keyid[1];
                        $dataArr[$batchid]['LOT_NO']  =   $keyid[2];
                        $dataArr[$batchid]['VENDOR_LOTNO']  =  $keyid[3];
                        $dataArr[$batchid]['STOCK_INHAND']  =  $keyid[4];
                        $dataArr[$batchid]['EXP_DATE']  =  $keyid[5];
                    }
                }

                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        $ExpID              =   explode("#",$key);
                        $STID_REF           =   $ExpID[0];
                        $RGPID_REF          =   $ExpID[1];
                        $ITEMID_REF         =   $ExpID[3];
                        $UOMID_REF          =   $ExpID[4];
                        $ALTUOM             =   $ExpID[5];
                        $AluQty             =   $this->getAltUmQty($ALTUOM,$ITEMID_REF,$val['RECEIVED_QTYM']);

                        $req_data33[$i][] = [
                            'GEID_REF'          => $RGPID_REF,
                            'ITEMID_REF'        => $ITEMID_REF,
                            'STID_REF'          => $STID_REF,
                            'MAIN_UOMID_REF'    => $UOMID_REF,
                            'STOCK_INHAND'      => $val['STOCK_INHAND'],
                            'RECEIVED_QTYM'     => $val['RECEIVED_QTYM'],
                            'ALTUOM'            => $ALTUOM,
                            'RECEIVED_QTYA'     => $AluQty,
                            'LOT_NO'            => $val['LOT_NO'],
                            'VENDOR_LOTNO'      => $val['VENDOR_LOTNO'],
                            'RATE'              => $request['RATE_'.$i],
                            'BPOID_REF'         => $request['GE_TYPE'] =="BPO"?$request['POID_REF_'.$i]:NULL,
                            'MRSID_REF'         => $request['MRSID_REF_'.$i],
                            'PIID_REF'          => $request['PIID_REF_'.$i],
                            'RFQID_REF'         => $request['RFQID_REF_'.$i],
                            'VQID_REF'          => $request['VQID_REF_'.$i],
                            'IPOID_REF'         => $request['GE_TYPE'] =="IPO"?$request['POID_REF_'.$i]:NULL,
                            'POID_REF'          => $request['GE_TYPE'] =="PO"?$request['POID_REF_'.$i]:NULL,
                            'EXP_DATE'          => $val['EXP_DATE'],                   
                        ];

                    }
                }
            }
        }

        $wrapped_links33["MULTISTORE"] = $req_data33; 
        $XMLSTORE = ArrayToXml::convert($wrapped_links33);

        $req_data44 =   array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $dataArr    =   array();
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $HIDDEN_BIN  =   $request['HIDDEN_BIN_'.$i];

                if($HIDDEN_BIN !=""){
                    $exp        =   explode(",",$HIDDEN_BIN);
                    foreach($exp as $val){
                        $keyid                                      =   explode("###",$val);
                        $req_data44[$i][] = [
                            'ITEMID_REF'        => $ITEMID_REF,
                            'MAIN_UOMID_REF'    => $UOMID_REF,
                            'RACKID_REF'        => $keyid[0],
                            'RACKNO'            => $keyid[1],
                            'BINNO'             => $keyid[2],
                            'RECEIVED_QTYM'     => $keyid[3],
                            'BPOID_REF'         => $request['GE_TYPE'] =="BPO"?$request['POID_REF_'.$i]:NULL,
                            'MRSID_REF'         => $request['MRSID_REF_'.$i],
                            'PIID_REF'          => $request['PIID_REF_'.$i],
                            'RFQID_REF'         => $request['RFQID_REF_'.$i],
                            'VQID_REF'          => $request['VQID_REF_'.$i],
                            'IPOID_REF'         => $request['GE_TYPE'] =="IPO"?$request['POID_REF_'.$i]:NULL,
                            'POID_REF'          => $request['GE_TYPE'] =="PO"?$request['POID_REF_'.$i]:NULL,                 
                        ];
                    }
                }
            }
        }

        if(!empty($req_data44)){
            $wrapped_links44["BIN"] = $req_data44; 
            $XMLBIN = ArrayToXml::convert($wrapped_links44);
        }
        else{
            $XMLBIN = NULL;
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
        $VID_REF        = $request['VID_REF'];
        $REMARKS        = $request['REMARKS'];
        $GE_TYPE        = $request['GE_TYPE'];

        $log_data = [ 
            $GRN_NO,$GRN_DT,$VID_REF,$REMARKS,
            $CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT, 
            $XMLUDF,$XMLSTORE,$XMLBIN,$USERID, Date('Y-m-d'),Date('h:i:s.u'),
            $ACTIONNAME,$IPADDRESS,$GE_TYPE
        ];

        $sp_result = DB::select('EXEC SP_GRN_GE_IN ?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $log_data);  

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

            $objResponse =  DB::table('TBL_TRN_IGRN02_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('GRNID','=',$id)
            ->first();

            $objStoreList       =   $this->getStoreList();
            $objPriority        =   $this->getPriority();

            $objlastdt          =NULL;
            if(isset($objResponse->GRN_DT) && $objResponse->GRN_DT !=""){
                $objlastdt          =   $objResponse->GRN_DT;
            }
            
            $objStoreName       = [];
            if(isset($objResponse->STID_REF) && $objResponse->STID_REF !=""){
                $objStoreName       =   $this->getStoreName($objResponse->STID_REF);
            }

            $objVendorName      =[];
            if(isset($objResponse->VID_REF) && $objResponse->VID_REF !=""){
                $objVendorName      =   $this->getVendorName($objResponse->VID_REF);
            }

            
            if( isset($objResponse->GE_TYPE) && $objResponse->GE_TYPE =="IPO"){

                $objMAT = DB::select("SELECT 
                T1.*,
                T2.ICODE,T2.NAME AS ITEM_NAME,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                T5.GEID,T5.GE_NO,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                T6.IPO_NO AS PO_NO,T7.BIN
                FROM TBL_TRN_IGRN02_MAT T1
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T1.ALT_UOMID_REF=T4.UOMID
                LEFT JOIN TBL_TRN_IMGE01_HDR T5 ON T1.GEID_REF=T5.GEID
                LEFT JOIN TBL_TRN_IPO_HDR T6 ON T1.IPOID_REF=T6.IPO_ID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG T7 ON T1.ITEMID_REF=T7.ITEMID_REF
                WHERE T1.GRNID_REF='$id' ORDER BY T1.GRN_MATID ASC
                "); 
            
            }
            else if(isset($objResponse->GE_TYPE) && $objResponse->GE_TYPE =="BPO"){

                $objMAT = DB::select("SELECT 
                T1.*,
                T2.ICODE,T2.NAME AS ITEM_NAME,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                T5.GEID,T5.GE_NO,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,
                T6.BPO_NO AS PO_NO,T7.BIN
                FROM TBL_TRN_IGRN02_MAT T1
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T1.ALT_UOMID_REF=T4.UOMID
                LEFT JOIN TBL_TRN_IMGE01_HDR T5 ON T1.GEID_REF=T5.GEID
                LEFT JOIN TBL_TRN_PROR03_HDR T6 ON T1.BPOID_REF=T6.BPOID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG T7 ON T1.ITEMID_REF=T7.ITEMID_REF
                WHERE T1.GRNID_REF='$id' ORDER BY T1.GRN_MATID ASC
                "); 

            }
            else{
                
                $objMAT = DB::select("SELECT 
                T1.*,
                T2.ICODE,T2.NAME AS ITEM_NAME,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                T5.GEID,T5.GE_NO,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,
                T6.PO_NO,T7.BIN
                FROM TBL_TRN_IGRN02_MAT T1
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T1.ALT_UOMID_REF=T4.UOMID
                LEFT JOIN TBL_TRN_IMGE01_HDR T5 ON T1.GEID_REF=T5.GEID
                LEFT JOIN TBL_TRN_PROR01_HDR T6 ON T1.POID_REF=T6.POID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG T7 ON T1.ITEMID_REF=T7.ITEMID_REF
                WHERE T1.GRNID_REF='$id' ORDER BY T1.GRN_MATID ASC
                "); 

            }

            if(isset($objMAT) && count($objMAT) > 0){
                foreach($objMAT as $key=>$val){
                    $BIN        =   DB::table('TBL_TRN_IGRN02_BIN')
                                    ->where('GRN_MATID_REF','=',$val->GRN_MATID)
                                    ->where('GRNID_REF','=',$val->GRNID_REF)
                                    ->where('ITEMID_REF','=',$val->ITEMID_REF)
                                    ->get();

                    if(isset($BIN) && count($BIN) > 0){
                        $BinData    =   array();
                        $BinNo      =   array();
                        foreach($BIN as $data){
                            $rack_id    =   $data->RACKID_REF;
                            $rack_no    =   $data->RACKNO;
                            $bin_no     =   $data->BINNO;
                            $bin_qty    =   $data->RECEIVED_QTYM;
                            $BinData[]  =   $rack_id."###".$rack_no."###".$bin_no."###".round($bin_qty);
                            $BinNo[]    =   $bin_no;
                        }

                        $objMAT[$key]->HIDDEN_BIN       =   implode(',',$BinData);
                        $objMAT[$key]->CHECK_BIN_DATA   =   implode(',',$BinNo);

                    }
                }
            }

            $objCount1 = count($objMAT);
            
            $objUDF = DB::table('TBL_TRN_IGRN02_UDF')                    
            ->where('GRNID_REF','=',$id)
            ->orderBy('GRN_UDFID','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF); 

            $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
            WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by UOMCODE ASC', [$CYID_REF,  'A' ]); 

            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_GRNGE")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('GRNGEID')->from('TBL_MST_UDFFOR_GRNGE')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                     
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                     
                    

            $objUdfData = DB::table('TBL_MST_UDFFOR_GRNGE')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_GRNGE")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('GRNGEID')->from('TBL_MST_UDFFOR_GRNGE')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                         
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                           
            

            $objUdfData2 = DB::table('TBL_MST_UDFFOR_GRNGE')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF2)
            ->get()->toArray(); 

            

            $objItems=array();
            
           
            
            $objUOM=array();

            

            $objItemUOMConv=array();

            $FormId         =   $this->form_id;
            
            $AlpsStatus =   $this->AlpsStatus();
            $ActionStatus   =   "";
            $checkCompany   =   $this->checkCompany('zep');

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
            return view($this->view.$FormId.'edit',compact([
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
                'AlpsStatus',
                'ActionStatus',
                'checkCompany',
                'TabSetting'
        ]));      


        }
     
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

            $objResponse =  DB::table('TBL_TRN_IGRN02_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('GRNID','=',$id)
            ->first();

            // DD($objResponse);

            $objStoreList       =   $this->getStoreList();
            $objPriority        =   $this->getPriority();

            $objlastdt          =NULL;
            if(isset($objResponse->GRN_DT) && $objResponse->GRN_DT !=""){
                $objlastdt          =   $objResponse->GRN_DT;
            }
            
            $objStoreName       = [];
            if(isset($objResponse->STID_REF) && $objResponse->STID_REF !=""){
                $objStoreName       =   $this->getStoreName($objResponse->STID_REF);
            }

            $objVendorName      =[];
            if(isset($objResponse->VID_REF) && $objResponse->VID_REF !=""){
                $objVendorName      =   $this->getVendorName($objResponse->VID_REF);
            }

            
            if( isset($objResponse->GE_TYPE) && $objResponse->GE_TYPE =="IPO"){

                $objMAT = DB::select("SELECT 
                T1.*,
                T2.ICODE,T2.NAME AS ITEM_NAME,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                T5.GEID,T5.GE_NO,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                T6.IPO_NO AS PO_NO,T7.BIN
                FROM TBL_TRN_IGRN02_MAT T1
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T1.ALT_UOMID_REF=T4.UOMID
                LEFT JOIN TBL_TRN_IMGE01_HDR T5 ON T1.GEID_REF=T5.GEID
                LEFT JOIN TBL_TRN_IPO_HDR T6 ON T1.IPOID_REF=T6.IPO_ID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG T7 ON T1.ITEMID_REF=T7.ITEMID_REF
                WHERE T1.GRNID_REF='$id' ORDER BY T1.GRN_MATID ASC
                "); 
            
            }
            else if(isset($objResponse->GE_TYPE) && $objResponse->GE_TYPE =="BPO"){

                $objMAT = DB::select("SELECT 
                T1.*,
                T2.ICODE,T2.NAME AS ITEM_NAME,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                T5.GEID,T5.GE_NO,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,
                T6.BPO_NO AS PO_NO,T7.BIN
                FROM TBL_TRN_IGRN02_MAT T1
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T1.ALT_UOMID_REF=T4.UOMID
                LEFT JOIN TBL_TRN_IMGE01_HDR T5 ON T1.GEID_REF=T5.GEID
                LEFT JOIN TBL_TRN_PROR03_HDR T6 ON T1.BPOID_REF=T6.BPOID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG T7 ON T1.ITEMID_REF=T7.ITEMID_REF
                WHERE T1.GRNID_REF='$id' ORDER BY T1.GRN_MATID ASC
                "); 

            }
            else{
                
                $objMAT = DB::select("SELECT 
                T1.*,
                T2.ICODE,T2.NAME AS ITEM_NAME,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                T5.GEID,T5.GE_NO,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,
                T6.PO_NO,T7.BIN
                FROM TBL_TRN_IGRN02_MAT T1
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T1.ALT_UOMID_REF=T4.UOMID
                LEFT JOIN TBL_TRN_IMGE01_HDR T5 ON T1.GEID_REF=T5.GEID
                LEFT JOIN TBL_TRN_PROR01_HDR T6 ON T1.POID_REF=T6.POID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG T7 ON T1.ITEMID_REF=T7.ITEMID_REF
                WHERE T1.GRNID_REF='$id' ORDER BY T1.GRN_MATID ASC
                "); 

            }

            if(isset($objMAT) && count($objMAT) > 0){
                foreach($objMAT as $key=>$val){
                    $BIN        =   DB::table('TBL_TRN_IGRN02_BIN')
                                    ->where('GRN_MATID_REF','=',$val->GRN_MATID)
                                    ->where('GRNID_REF','=',$val->GRNID_REF)
                                    ->where('ITEMID_REF','=',$val->ITEMID_REF)
                                    ->get();

                    if(isset($BIN) && count($BIN) > 0){
                        $BinData    =   array();
                        $BinNo      =   array();
                        foreach($BIN as $data){
                            $rack_id    =   $data->RACKID_REF;
                            $rack_no    =   $data->RACKNO;
                            $bin_no     =   $data->BINNO;
                            $bin_qty    =   $data->RECEIVED_QTYM;
                            $BinData[]  =   $rack_id."###".$rack_no."###".$bin_no."###".round($bin_qty);
                            $BinNo[]    =   $bin_no;
                        }

                        $objMAT[$key]->HIDDEN_BIN       =   implode(',',$BinData);
                        $objMAT[$key]->CHECK_BIN_DATA   =   implode(',',$BinNo);

                    }
                }
            }

            $objCount1 = count($objMAT);
            
            
            $objUDF = DB::table('TBL_TRN_IGRN02_UDF')                    
            ->where('GRNID_REF','=',$id)
            ->orderBy('GRN_UDFID','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF); 

            $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
            WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
            order by UOMCODE ASC', [$CYID_REF,  'A' ]); 

            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_GRNGE")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('GRNGEID')->from('TBL_MST_UDFFOR_GRNGE')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                     
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                     
                    

            $objUdfData = DB::table('TBL_MST_UDFFOR_GRNGE')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_GRNGE")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('GRNGEID')->from('TBL_MST_UDFFOR_GRNGE')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                         
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                           
            

            $objUdfData2 = DB::table('TBL_MST_UDFFOR_GRNGE')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF2)
            ->get()->toArray(); 

            

            $objItems=array();
            
           
            
            $objUOM=array();

            

            $objItemUOMConv=array();

            $FormId         =   $this->form_id;
            
            $AlpsStatus =   $this->AlpsStatus();
            $ActionStatus   =   "disabled";
            
            $checkCompany   =   $this->checkCompany('zep');
            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view($this->view.$FormId.'view',compact([
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
                'AlpsStatus',
                'ActionStatus',
                'checkCompany',
                'TabSetting'
        ]));      


        }
     
    }

    public function update(Request $request){
        
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                if($request['GE_TYPE'] =="IPO"){
                    $req_data[$i] = [
                        'GEID_REF'          =>  $request['RGP_NO_'.$i],
                        'IPOID_REF'         =>  $request['POID_REF_'.$i],
                        'ITEMID_REF'        =>  $request['ITEMID_REF_'.$i],
                        'PO_PENDING_QTY'    =>  $request['PO_PENDING_QTY_'.$i],
                        'BILL_QTY'          =>  $request['SE_QTY_'.$i],
                        'MAIN_UOMID_REF'    =>  $request['MAIN_UOMID_REF_'.$i],
                        'RECEIVED_QTY_MU'   =>  $request['RECEIVED_QTY_MU_'.$i],
                        'ALT_UOMID_REF'     =>  $request['ALT_UOMID_REF_'.$i],
                        'RECEIVED_QTY_AU'   =>  $request['RECEIVED_QTY_AU_'.$i],
                        'SHORT_QTY'         =>  $request['SHORT_QTY_'.$i],
                        'RATE'              =>  $request['RATE_'.$i],
                        'STID'              =>  $request['STORE_ID_'.$i],
                        'REMARKS'           =>  $request['REMARKS_'.$i],
                        'STORE_NAME'        =>  $request['STORE_NAME_'.$i],
                        'BATCHQTY_REF'      =>  $request['HiddenRowId_'.$i],
                        'MRSID_REF'         =>  $request['MRSID_REF_'.$i],
                        'PIID_REF'          =>  $request['PIID_REF_'.$i],
                        'RFQID_REF'         =>  $request['RFQID_REF_'.$i],
                        'VQID_REF'          =>  $request['VQID_REF_'.$i]
                    ];
                }
                else if($request['GE_TYPE'] =="BPO"){
                    $req_data[$i] = [
                        'GEID_REF'          =>  $request['RGP_NO_'.$i],
                        'BPOID_REF'         =>  $request['POID_REF_'.$i],
                        'ITEMID_REF'        =>  $request['ITEMID_REF_'.$i],
                        'PO_PENDING_QTY'    =>  $request['PO_PENDING_QTY_'.$i],
                        'BILL_QTY'          =>  $request['SE_QTY_'.$i],
                        'MAIN_UOMID_REF'    =>  $request['MAIN_UOMID_REF_'.$i],
                        'RECEIVED_QTY_MU'   =>  $request['RECEIVED_QTY_MU_'.$i],
                        'ALT_UOMID_REF'     =>  $request['ALT_UOMID_REF_'.$i],
                        'RECEIVED_QTY_AU'   =>  $request['RECEIVED_QTY_AU_'.$i],
                        'SHORT_QTY'         =>  $request['SHORT_QTY_'.$i],
                        'RATE'              =>  $request['RATE_'.$i],
                        'STID'              =>  $request['STORE_ID_'.$i],
                        'REMARKS'           =>  $request['REMARKS_'.$i],
                        'STORE_NAME'        =>  $request['STORE_NAME_'.$i],
                        'BATCHQTY_REF'      =>  $request['HiddenRowId_'.$i],
                        'MRSID_REF'         =>  $request['MRSID_REF_'.$i],
                        'PIID_REF'          =>  $request['PIID_REF_'.$i],
                        'RFQID_REF'         =>  $request['RFQID_REF_'.$i],
                        'VQID_REF'          =>  $request['VQID_REF_'.$i],
                    ];
                }
                else{
                    $req_data[$i] = [
                        'GEID_REF'          =>  $request['RGP_NO_'.$i],
                        'POID_REF'          =>  $request['POID_REF_'.$i],
                        'ITEMID_REF'        =>  $request['ITEMID_REF_'.$i],
                        'PO_PENDING_QTY'    =>  $request['PO_PENDING_QTY_'.$i],
                        'BILL_QTY'          =>  $request['SE_QTY_'.$i],
                        'MAIN_UOMID_REF'    =>  $request['MAIN_UOMID_REF_'.$i],
                        'RECEIVED_QTY_MU'   =>  $request['RECEIVED_QTY_MU_'.$i],
                        'ALT_UOMID_REF'     =>  $request['ALT_UOMID_REF_'.$i],
                        'RECEIVED_QTY_AU'   =>  $request['RECEIVED_QTY_AU_'.$i],
                        'SHORT_QTY'         =>  $request['SHORT_QTY_'.$i],
                        'RATE'              =>  $request['RATE_'.$i],
                        'STID'              =>  $request['STORE_ID_'.$i],
                        'REMARKS'           =>  $request['REMARKS_'.$i],
                        'STORE_NAME'        =>  $request['STORE_NAME_'.$i],
                        'BATCHQTY_REF'      =>  $request['HiddenRowId_'.$i],
                        'MRSID_REF'         =>  $request['MRSID_REF_'.$i],
                        'PIID_REF'          =>  $request['PIID_REF_'.$i],
                        'RFQID_REF'         =>  $request['RFQID_REF_'.$i],
                        'VQID_REF'          =>  $request['VQID_REF_'.$i],
                    ];
                }
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

        if($r_count2 > 0){
            $wrapped_links3["UDF1"] = $reqdata3; 
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

                        $dataArr[$batchid]['RECEIVED_QTYM']  =   $keyid[1];
                        $dataArr[$batchid]['LOT_NO']  =   $keyid[2];
                        $dataArr[$batchid]['VENDOR_LOTNO']  =  $keyid[3];
                        $dataArr[$batchid]['STOCK_INHAND']  =  $keyid[4];
                        $dataArr[$batchid]['EXP_DATE']  =  isset($keyid[5])?$keyid[5]:"";
                    }
                }

                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        $ExpID              =   explode("#",$key);
                        $STID_REF           =   $ExpID[0];
                        $RGPID_REF          =   $ExpID[1];
                        $ITEMID_REF         =   $ExpID[3];
                        $UOMID_REF          =   $ExpID[4];
                        $ALTUOM             =   $ExpID[5];
                        $AluQty             =   $this->getAltUmQty($ALTUOM,$ITEMID_REF,$val['RECEIVED_QTYM']);
                       
                        $req_data33[$i][] = [
                            'GEID_REF'          => $RGPID_REF,
                            'ITEMID_REF'        => $ITEMID_REF,
                            'STID_REF'          => $STID_REF,
                            'MAIN_UOMID_REF'    => $UOMID_REF,
                            'STOCK_INHAND'      => $val['STOCK_INHAND'],
                            'RECEIVED_QTYM'     => $val['RECEIVED_QTYM'],
                            'ALTUOM'            => $ALTUOM,
                            'RECEIVED_QTYA'     => $AluQty,
                            'LOT_NO'            => $val['LOT_NO'],
                            'VENDOR_LOTNO'      => $val['VENDOR_LOTNO'],
                            'RATE'              => $request['RATE_'.$i],
                            'BPOID_REF'         => $request['GE_TYPE'] =="BPO"?$request['POID_REF_'.$i]:NULL,
                            'MRSID_REF'         => $request['MRSID_REF_'.$i],
                            'PIID_REF'          => $request['PIID_REF_'.$i],
                            'RFQID_REF'         => $request['RFQID_REF_'.$i],
                            'VQID_REF'          => $request['VQID_REF_'.$i],
                            'IPOID_REF'         => $request['GE_TYPE'] =="IPO"?$request['POID_REF_'.$i]:NULL,
                            'POID_REF'          => $request['GE_TYPE'] =="PO"?$request['POID_REF_'.$i]:NULL,
                            'EXP_DATE'          => $val['EXP_DATE'],                   
                        ];

                    }
                }
            }
        }


        $wrapped_links33["MULTISTORE"] = $req_data33; 
        $XMLSTORE = ArrayToXml::convert($wrapped_links33);

        $req_data44 =   array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $dataArr    =   array();
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $HIDDEN_BIN  =   $request['HIDDEN_BIN_'.$i];

                if($HIDDEN_BIN !=""){
                    $exp        =   explode(",",$HIDDEN_BIN);
                    foreach($exp as $val){
                        $keyid                                      =   explode("###",$val);
                        $req_data44[$i][] = [
                            'ITEMID_REF'        => $ITEMID_REF,
                            'MAIN_UOMID_REF'    => $UOMID_REF,
                            'RACKID_REF'        => $keyid[0],
                            'RACKNO'            => $keyid[1],
                            'BINNO'             => $keyid[2],
                            'RECEIVED_QTYM'     => $keyid[3],
                            'BPOID_REF'         => $request['GE_TYPE'] =="BPO"?$request['POID_REF_'.$i]:NULL,
                            'MRSID_REF'         => $request['MRSID_REF_'.$i],
                            'PIID_REF'          => $request['PIID_REF_'.$i],
                            'RFQID_REF'         => $request['RFQID_REF_'.$i],
                            'VQID_REF'          => $request['VQID_REF_'.$i],
                            'IPOID_REF'         => $request['GE_TYPE'] =="IPO"?$request['POID_REF_'.$i]:NULL,
                            'POID_REF'          => $request['GE_TYPE'] =="PO"?$request['POID_REF_'.$i]:NULL,                 
                        ];
                    }
                }
            }
        }

        if(!empty($req_data44)){
            $wrapped_links44["BIN"] = $req_data44; 
            $XMLBIN = ArrayToXml::convert($wrapped_links44);
        }
        else{
            $XMLBIN = NULL;
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
        $VID_REF        = $request['VID_REF'];
        $REMARKS        = $request['REMARKS'];
        $GE_TYPE        = $request['GE_TYPE'];

        $log_data = [ 
            $GRN_NO,$GRN_DT,$VID_REF,$REMARKS,
            $CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT, 
            $XMLUDF,$XMLSTORE,$XMLBIN,$USERID, Date('Y-m-d'),Date('h:i:s.u'),
            $ACTIONNAME,$IPADDRESS,$GE_TYPE
        ];

       //dd($log_data);

        $sp_result = DB::select('EXEC SP_GRN_GE_UP ?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $log_data);  

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $GRN_NO. ' Sucessfully Updated.']);

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

                if($request['GE_TYPE'] =="IPO"){
                    $req_data[$i] = [
                        'GEID_REF'          =>  $request['RGP_NO_'.$i],
                        'IPOID_REF'         =>  $request['POID_REF_'.$i],
                        'ITEMID_REF'        =>  $request['ITEMID_REF_'.$i],
                        'PO_PENDING_QTY'    =>  $request['PO_PENDING_QTY_'.$i],
                        'BILL_QTY'          =>  $request['SE_QTY_'.$i],
                        'MAIN_UOMID_REF'    =>  $request['MAIN_UOMID_REF_'.$i],
                        'RECEIVED_QTY_MU'   =>  $request['RECEIVED_QTY_MU_'.$i],
                        'ALT_UOMID_REF'     =>  $request['ALT_UOMID_REF_'.$i],
                        'RECEIVED_QTY_AU'   =>  $request['RECEIVED_QTY_AU_'.$i],
                        'SHORT_QTY'         =>  $request['SHORT_QTY_'.$i],
                        'RATE'              =>  $request['RATE_'.$i],
                        'STID'              =>  $request['STORE_ID_'.$i],
                        'REMARKS'           =>  $request['REMARKS_'.$i],
                        'STORE_NAME'        =>  $request['STORE_NAME_'.$i],
                        'BATCHQTY_REF'      =>  $request['HiddenRowId_'.$i],
                        'MRSID_REF'         =>  $request['MRSID_REF_'.$i],
                        'PIID_REF'          =>  $request['PIID_REF_'.$i],
                        'RFQID_REF'         =>  $request['RFQID_REF_'.$i],
                        'VQID_REF'          =>  $request['VQID_REF_'.$i]
                    ];
                }
                else if($request['GE_TYPE'] =="BPO"){
                    $req_data[$i] = [
                        'GEID_REF'          =>  $request['RGP_NO_'.$i],
                        'BPOID_REF'         =>  $request['POID_REF_'.$i],
                        'ITEMID_REF'        =>  $request['ITEMID_REF_'.$i],
                        'PO_PENDING_QTY'    =>  $request['PO_PENDING_QTY_'.$i],
                        'BILL_QTY'          =>  $request['SE_QTY_'.$i],
                        'MAIN_UOMID_REF'    =>  $request['MAIN_UOMID_REF_'.$i],
                        'RECEIVED_QTY_MU'   =>  $request['RECEIVED_QTY_MU_'.$i],
                        'ALT_UOMID_REF'     =>  $request['ALT_UOMID_REF_'.$i],
                        'RECEIVED_QTY_AU'   =>  $request['RECEIVED_QTY_AU_'.$i],
                        'SHORT_QTY'         =>  $request['SHORT_QTY_'.$i],
                        'RATE'              =>  $request['RATE_'.$i],
                        'STID'              =>  $request['STORE_ID_'.$i],
                        'REMARKS'           =>  $request['REMARKS_'.$i],
                        'STORE_NAME'        =>  $request['STORE_NAME_'.$i],
                        'BATCHQTY_REF'      =>  $request['HiddenRowId_'.$i],
                        'MRSID_REF'         =>  $request['MRSID_REF_'.$i],
                        'PIID_REF'          =>  $request['PIID_REF_'.$i],
                        'RFQID_REF'         =>  $request['RFQID_REF_'.$i],
                        'VQID_REF'          =>  $request['VQID_REF_'.$i],
                    ];
                }
                else{
                    $req_data[$i] = [
                        'GEID_REF'          =>  $request['RGP_NO_'.$i],
                        'POID_REF'          =>  $request['POID_REF_'.$i],
                        'ITEMID_REF'        =>  $request['ITEMID_REF_'.$i],
                        'PO_PENDING_QTY'    =>  $request['PO_PENDING_QTY_'.$i],
                        'BILL_QTY'          =>  $request['SE_QTY_'.$i],
                        'MAIN_UOMID_REF'    =>  $request['MAIN_UOMID_REF_'.$i],
                        'RECEIVED_QTY_MU'   =>  $request['RECEIVED_QTY_MU_'.$i],
                        'ALT_UOMID_REF'     =>  $request['ALT_UOMID_REF_'.$i],
                        'RECEIVED_QTY_AU'   =>  $request['RECEIVED_QTY_AU_'.$i],
                        'SHORT_QTY'         =>  $request['SHORT_QTY_'.$i],
                        'RATE'              =>  $request['RATE_'.$i],
                        'STID'              =>  $request['STORE_ID_'.$i],
                        'REMARKS'           =>  $request['REMARKS_'.$i],
                        'STORE_NAME'        =>  $request['STORE_NAME_'.$i],
                        'BATCHQTY_REF'      =>  $request['HiddenRowId_'.$i],
                        'MRSID_REF'         =>  $request['MRSID_REF_'.$i],
                        'PIID_REF'          =>  $request['PIID_REF_'.$i],
                        'RFQID_REF'         =>  $request['RFQID_REF_'.$i],
                        'VQID_REF'          =>  $request['VQID_REF_'.$i],
                    ];
                }
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

        if($r_count2 > 0){
            $wrapped_links3["UDF1"] = $reqdata3; 
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

                        $dataArr[$batchid]['RECEIVED_QTYM']  =   $keyid[1];
                        $dataArr[$batchid]['LOT_NO']  =   $keyid[2];
                        $dataArr[$batchid]['VENDOR_LOTNO']  =  $keyid[3];
                        $dataArr[$batchid]['STOCK_INHAND']  =  $keyid[4];
                        $dataArr[$batchid]['EXP_DATE']  =  isset($keyid[5])?$keyid[5]:"";
                    }
                }

                if(!empty($dataArr)){
                    foreach($dataArr as $key=>$val){

                        $ExpID              =   explode("#",$key);
                        $STID_REF           =   $ExpID[0];
                        $RGPID_REF          =   $ExpID[1];
                        $ITEMID_REF         =   $ExpID[3];
                        $UOMID_REF          =   $ExpID[4];
                        $ALTUOM             =   $ExpID[5];
                        $AluQty             =   $this->getAltUmQty($ALTUOM,$ITEMID_REF,$val['RECEIVED_QTYM']);
                       
                        $req_data33[$i][] = [
                            'GEID_REF'          => $RGPID_REF,
                            'ITEMID_REF'        => $ITEMID_REF,
                            'STID_REF'          => $STID_REF,
                            'MAIN_UOMID_REF'    => $UOMID_REF,
                            'STOCK_INHAND'      => $val['STOCK_INHAND'],
                            'RECEIVED_QTYM'     => $val['RECEIVED_QTYM'],
                            'ALTUOM'            => $ALTUOM,
                            'RECEIVED_QTYA'     => $AluQty,
                            'LOT_NO'            => $val['LOT_NO'],
                            'VENDOR_LOTNO'      => $val['VENDOR_LOTNO'],
                            'RATE'              => $request['RATE_'.$i],
                            'BPOID_REF'         => $request['GE_TYPE'] =="BPO"?$request['POID_REF_'.$i]:NULL,
                            'MRSID_REF'         => $request['MRSID_REF_'.$i],
                            'PIID_REF'          => $request['PIID_REF_'.$i],
                            'RFQID_REF'         => $request['RFQID_REF_'.$i],
                            'VQID_REF'          => $request['VQID_REF_'.$i],
                            'IPOID_REF'         => $request['GE_TYPE'] =="IPO"?$request['POID_REF_'.$i]:NULL,
                            'POID_REF'          => $request['GE_TYPE'] =="PO"?$request['POID_REF_'.$i]:NULL,
                            'EXP_DATE'          => $val['EXP_DATE'],                   
                        ];

                    }
                }
            }
        }

        $wrapped_links33["MULTISTORE"] = $req_data33; 
        $XMLSTORE = ArrayToXml::convert($wrapped_links33);

        $req_data44 =   array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $dataArr    =   array();
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $HIDDEN_BIN  =   $request['HIDDEN_BIN_'.$i];

                if($HIDDEN_BIN !=""){
                    $exp        =   explode(",",$HIDDEN_BIN);
                    foreach($exp as $val){
                        $keyid                                      =   explode("###",$val);
                        $req_data44[$i][] = [
                            'ITEMID_REF'        => $ITEMID_REF,
                            'MAIN_UOMID_REF'    => $UOMID_REF,
                            'RACKID_REF'        => $keyid[0],
                            'RACKNO'            => $keyid[1],
                            'BINNO'             => $keyid[2],
                            'RECEIVED_QTYM'     => $keyid[3],
                            'BPOID_REF'         => $request['GE_TYPE'] =="BPO"?$request['POID_REF_'.$i]:NULL,
                            'MRSID_REF'         => $request['MRSID_REF_'.$i],
                            'PIID_REF'          => $request['PIID_REF_'.$i],
                            'RFQID_REF'         => $request['RFQID_REF_'.$i],
                            'VQID_REF'          => $request['VQID_REF_'.$i],
                            'IPOID_REF'         => $request['GE_TYPE'] =="IPO"?$request['POID_REF_'.$i]:NULL,
                            'POID_REF'          => $request['GE_TYPE'] =="PO"?$request['POID_REF_'.$i]:NULL,                 
                        ];
                    }
                }
            }
        }

        if(!empty($req_data44)){
            $wrapped_links44["BIN"] = $req_data44; 
            $XMLBIN = ArrayToXml::convert($wrapped_links44);
        }
        else{
            $XMLBIN = NULL;
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
        $VID_REF        = $request['VID_REF'];
        $REMARKS        = $request['REMARKS'];
        $GE_TYPE        = $request['GE_TYPE'];

        $log_data = [ 
            $GRN_NO,$GRN_DT,$VID_REF,$REMARKS,
            $CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT, 
            $XMLUDF,$XMLSTORE,$XMLBIN,$USERID, Date('Y-m-d'),Date('h:i:s.u'),
            $ACTIONNAME,$IPADDRESS,$GE_TYPE
        ];

       
        $sp_result = DB::select('EXEC SP_GRN_GE_UP ?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $log_data);  

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

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
        $TABLE      =   "TBL_TRN_IGRN02_HDR";
        $FIELD      =   "GRNID";
        $ACTIONNAME     = $Approvallevel;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
            
        $log_data = [ 
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
        
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $GRN_NO. ' Sucessfully Approved.']);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
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
        $TABLE      =   "TBL_TRN_IGRN02_HDR";
        $FIELD      =   "GRNID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
         'NT'  => 'TBL_TRN_IGRN02_MAT',
        ];
        $req_data[1]=[
        'NT'  => 'TBL_TRN_IGRN02_MULTISTORE',
        ];
        $req_data[2]=[
        'NT'  => 'TBL_TRN_IGRN02_UDF',
        ];
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_GRNGE  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_IGRN02_HDR')->where('GRNID','=',$id)->first();

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
        
		$image_path         =   "docs/company".$CYID_REF."/GrnGateEntry";     
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

        return  DB::select('SELECT MAX(GRN_DT) GRN_DT FROM TBL_TRN_IGRN02_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
    }

    public function getItemDetails(Request $request){

        $Status     =   $request['status'];
        $GEID_REF   =   $request['RGP_NO'];
        $POID_REF   =   $request['POID_REF'];
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $GE_TYPE    =   $request['GE_TYPE'];;
        $StdCost    =   0;
        $Taxid      =   [];

        $AlpsStatus =   $this->AlpsStatus();


        if($GE_TYPE =="PO"){

            $ObjItem =  DB::select("SELECT 
            T1.ITEMID,T1.ICODE,T1.NAME,T1.ICID_REF,T1.ITEMGID_REF,T1.ALT_UOMID_REF,
            T2.* 
            FROM TBL_MST_ITEM T1
            INNER JOIN TBL_TRN_PROR01_MAT T2 ON T1.ITEMID=T2.ITEMID_REF
            WHERE T1.CYID_REF = '$CYID_REF'  AND T2.PENDING_QTY > 0
            AND ( T1.DEACTIVATED IS NULL OR T1.DEACTIVATED = 0 ) AND T1.STATUS ='$Status' AND T2.POID_REF='$POID_REF'");

            
        }
        else if($GE_TYPE =="BPO"){

            $ObjItem =  DB::select("SELECT 
            T2.UOMID_REF,
            T2.PENDING_QTY AS PO_QTY,
            T2.ITEMSPECI,
            T2.RATE AS RATEP_UOM,
            T3.ITEMID,T3.ICODE,T3.NAME,T3.ICID_REF,T3.ITEMGID_REF,T3.ALT_UOMID_REF AS ALTUOMID_REF
            FROM TBL_TRN_SPOR03_HDR T1
            INNER JOIN TBL_TRN_SPOR03_MAT T2 ON T1.SBPID=T2.SBPID_REF
            INNER JOIN TBL_MST_ITEM T3 ON T2.ITEMID_REF=T3.ITEMID
            WHERE T1.CYID_REF = '$CYID_REF' AND T1.BRID_REF = '$BRID_REF'  AND T1.FYID_REF = '$FYID_REF' AND T2.PENDING_QTY > 0
            AND T1.STATUS ='A' AND T1.BPOID_REF='$POID_REF' AND T2.PENDING_QTY IS NOT NULL");

        }
    
        else{
            $ObjItem =  DB::select("SELECT 
            T1.ITEMID,T1.ICODE,T1.NAME,T1.ICID_REF,T1.ITEMGID_REF,T1.ALT_UOMID_REF,
            T2.MAIN_UOMID_REF AS UOMID_REF,
            T2.ALT_UOMID_REF AS ALTUOMID_REF,
            T2.IPO_MAIN_QTY AS PO_QTY,
            T2.ITEM_SPECI AS ITEMSPECI,
            T2.RATE_ASP_MU AS RATEP_UOM,
            T2.PENDING_QTY
            FROM TBL_MST_ITEM T1
            INNER JOIN TBL_TRN_IPO_MAT T2 ON T1.ITEMID=T2.ITEMID_REF
            WHERE T1.CYID_REF = '$CYID_REF'  AND T2.PENDING_QTY > 0
            AND ( T1.DEACTIVATED IS NULL OR T1.DEACTIVATED = 0 ) AND T1.STATUS ='$Status' AND T2.IPO_ID_REF='$POID_REF'");



        }
// /dd($ObjItem); 
        if(!empty($ObjItem)){

            foreach ($ObjItem as $index=>$dataRow){
                     
                $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                            WHERE  CYID_REF = ?  AND UOMID = ? 
                            AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                            [$CYID_REF, $dataRow->UOMID_REF, 'A' ]);

                $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                            WHERE  CYID_REF = ?  AND UOMID = ? 
                            AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                            [$CYID_REF, $dataRow->ALTUOMID_REF, $Status ]);
                    
                $ObjAltQTY =  DB::select('SELECT TOP 1  TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV  
                            WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                            [$dataRow->ITEMID,$dataRow->ALTUOMID_REF]);

                $TOQTY =  isset($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 0;

                //dd($TOQTY);

                //$FROMQTY =  isset($ObjAltQTY[0]->FROM_QTY)? $ObjAltQTY[0]->FROM_QTY : 0;
                
                $TOQTY =  0;
                $FROMQTY =  isset($dataRow->PO_QTY)? $dataRow->PO_QTY : 0;

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


                    $AultUmQuantity = $this->getAltUmQty($dataRow->ALTUOMID_REF,$dataRow->ITEMID,$FROMQTY);

                    $PENDING_QTY=   0;
                    $MRSID_REF  =   '';
                    $PIID_REF   =   '';
                    $RFQID_REF  =   '';
                    $VQID_REF   =   '';

                    $RATE       =  $dataRow->RATEP_UOM;

                    if($GE_TYPE =="PO"){
                        $PENDING_QTY=   $dataRow->PENDING_QTY;
                        $MRSID_REF  =   $dataRow->MRSID_REF;
                        $PIID_REF   =   $dataRow->PIID_REF;
                        $RFQID_REF  =   $dataRow->RFQID_REF;
                        $VQID_REF   =   $dataRow->RFQPINO;

                        $desc6  =   $MRSID_REF.'-'.$PIID_REF.'-'.$RFQID_REF.'-'.$VQID_REF.'-'.$POID_REF.'-'.$GEID_REF.'-'.$dataRow->ITEMID;
                   
                    }
                    else if($GE_TYPE =="BPO"){
                        $PENDING_QTY    =   $dataRow->PO_QTY;
                        $desc6          =   $POID_REF.'-'.$GEID_REF.'-'.$dataRow->ITEMID;
                   
                    }
                    else{
                        $PENDING_QTY    =   $dataRow->PENDING_QTY;
                        $desc6  =   $POID_REF.'-'.$GEID_REF.'-'.$dataRow->ITEMID;
                    }


                   // DD($PENDING_QTY);

                    
                    $row = '';
                    $row = $row.'<tr id="item_'.$desc6.'"  class="clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$desc6.'"  value="'.$desc6.'" class="js-selectall1"  ></td>';
                    $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                    $row = $row.'<input type="hidden" id="uniquerowid_'.$desc6.'"   />';
                    $row = $row.'<input type="hidden" id="txtitem_'.$desc6.'" data-desc="'.$dataRow->ICODE.'" data-desc6="'.$desc6.'"  data-desc7="'.$AultUmQuantity.'" data-desc8="'.$PENDING_QTY.'" data-desc9="'.$MRSID_REF.'" data-desc10="'.$PIID_REF.'" data-desc11="'.$RFQID_REF.'" data-desc12="'.$VQID_REF.'" data-desc13="'.$RATE.'" value="'.$dataRow->ITEMID.'"/></td> 
                    <td style="width:10%;" id="itemname_'.$desc6.'" >'.$dataRow->NAME;
                    $row = $row.'<input type="hidden" id="txtitemname_'.$desc6.'" data-desc="'.$dataRow->ITEMSPECI.'" value="'.$dataRow->NAME.'"/></td>';
                    $row = $row.'<td style="width:8%;" id="itemuom_'.$desc6.'" ><input type="hidden" id="txtitemuom_'.$desc6.'" data-desc="'.$ObjAltUOM[0]->UOMCODE.'-'.$ObjAltUOM[0]->DESCRIPTIONS.'"  data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'"   value="'.$dataRow->UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                    $row = $row.'<td style="width:8%;" id="uomqty_'.$desc6.'" ><input type="hidden" id="txtuomqty_'.$desc6.'" data-desc="'.$TOQTY.'" value="'.$dataRow->ALTUOMID_REF.'"/>'.$FROMQTY.'</td>';
                    $row = $row.'<td style="width:8%;" id="irate_'.$desc6.'"><input type="hidden" id="txtirate_'.$desc6.'" data-desc="'.$FROMQTY.'" value="'.$StdCost.'"/>'.$ObjItemGroup[0]->GROUPCODE.'-'.$ObjItemGroup[0]->GROUPNAME.'</td>';
                    $row = $row.'<td style="width:8%;" id="itax_'.$desc6.'"><input type="hidden" id="txtitax_'.$desc6.'" />'.$ObjItemCategory[0]->ICCODE.'-'.$ObjItemCategory[0]->DESCRIPTIONS.'</td>
                    
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

        $ROW_ID         =   $request['ROW_ID'];
        $RGP_NO         =   $request['RGP_NO'];
        $POID_REF       =   $request['POID_REF'];
        $ITEMID_REF     =   $request['ITEMID_REF'];
        $MAIN_UOMID_DES =   $request['MAIN_UOMID_DES'];
        $MAIN_UOMID_REF =   $request['MAIN_UOMID_REF'];
        $ALT_UOMID_DES  =   $request['ALT_UOMID_DES'];
        $ALT_UOMID_REF  =   $request['ALT_UOMID_REF'];
        $ITEMROWID      =   $request['ITEMROWID'];
        $PO_PENDING_QTY =   $request['PO_PENDING_QTY'];
        $ACTION_TYPE    =   $request['ACTION_TYPE'] =="VIEW"?'disabled':'';
        $SRNOA          =   NULL;
        $EXPIRY         =   0;
        $dataArr        =   array();
        $dataArr2       =   array();
        $dataArr3       =   array();
        $dataArr4       =   array();

        if($ITEMROWID !=""){
            $exp        =   explode(",",$ITEMROWID);

            foreach($exp as $val){
                $keyid              =   explode("_",$val);
                
                $batchid            =   isset($keyid[0])?$keyid[0]:'';
                $qty                =   isset($keyid[1])?$keyid[1]:'';
                $LOT                =   isset($keyid[2])?$keyid[2]:'';
                $VENDLOT            =   isset($keyid[3])?$keyid[3]:'';
                $EXPIRE_DATE        =   isset($keyid[5])?$keyid[5]:'';

                $dataArr[$batchid]  =   $qty;
                $dataArr2[$batchid] =   $LOT;
                $dataArr3[$batchid] =   $VENDLOT;
                $dataArr4[$batchid] =   $EXPIRE_DATE;
            }
        }
        
        $objResponse =  DB::table('TBL_MST_ITEMCHECKFLAG')
        ->where('ITEMID_REF','=',$ITEMID_REF)
        ->select('SRNOA','BATCHNOA','BIN','EXPIRY_APPLICABLE')
        ->first();

        if(!empty($objResponse)){
            $SRNOA      =   $objResponse->SRNOA;
            $BATCHNOA   =   $objResponse->BATCHNOA;
            $BIN        =   $objResponse->BIN;
            $EXPIRY     =   $objResponse->EXPIRY_APPLICABLE;
        }

        $expire =   $EXPIRY !='1'?'readonly':'';
        
        $objBatch =  DB::SELECT("SELECT STID,STCODE,NAME AS STNAME , (SELECT SUM(CURRENT_QTY)  FROM TBL_MST_BATCH WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND STID_REF=STID AND ITEMID_REF='$ITEMID_REF') AS TOTAL_STOCK
        FROM TBL_MST_STORE 
        WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 )
        ");

        
        echo '<thead>';
        echo '<tr>';
        echo '<th style="text-align:left;">Store</th>';
        echo '<th style="text-align:left;">Expiry Date</th>';
        echo '<th style="text-align:left;">Total Stock</th>';
        echo '<th style="text-align:left;">Main UoM</th>';
        echo '<th style="text-align:left;">Main UoM Qty</th>';
        echo '<th style="text-align:left;">Alt UOM</th>';
        echo '<th style="text-align:left;">Alt UOM Qty</th>';
        echo '<th style="text-align:left;">Our Lot No</th>';
        echo '<th style="text-align:left;">Vendor Lot No</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach($objBatch as $key=>$val){

            $TOTAL_STOCK        =   $val->TOTAL_STOCK !=""?$val->TOTAL_STOCK:0;
            $StoreRowId         =   $val->STID.'#'.$RGP_NO.'#'.$POID_REF.'#'.$ITEMID_REF.'#'.$MAIN_UOMID_REF.'#'.$ALT_UOMID_REF;
            $qtyvalue           =   array_key_exists($StoreRowId, $dataArr)?$dataArr[$StoreRowId]:0;
            $AluQty             =   $this->getAltUmQty($ALT_UOMID_REF,$ITEMID_REF,$qtyvalue);
            $CURRENT_QTY        =   $TOTAL_STOCK;
            $MainReceivedQty    =   $qtyvalue > 0?$qtyvalue:'';
            $AultReceivedQty    =   $AluQty > 0?$AluQty:''; 
            $LOT_NO             =   array_key_exists($StoreRowId, $dataArr2)?$dataArr2[$StoreRowId]:'';
            $VENDOR_LOTNO       =   array_key_exists($StoreRowId, $dataArr3)?$dataArr3[$StoreRowId]:'';
            $EXPIRE_DATE        =   array_key_exists($StoreRowId, $dataArr4)?$dataArr4[$StoreRowId]:'';
            
            echo '<tr  class="participantRow33">';
            echo '<td hidden><input type="text" id="'.$key.'" value="'.$ROW_ID.'" ></td>';
            echo '<td style="width:15%;text-align:left;">'.$val->STCODE.' - '.$val->STNAME.'</td>';
            echo '<td style="width:10%" ><input '.$expire.' '.$ACTION_TYPE.'  type="date" name="EXPIRE_DATE" id="EXPIRE_DATE" value="'.$EXPIRE_DATE.'" class="qtytext" style="font-size:11px;" ></td>';
            echo '<td hidden><input '.$ACTION_TYPE.' type="text" class="qtytext" name="STORE_NAME_'.$key.'" id="STORE_NAME_'.$key.'"  value="'.$val->STNAME.'"  readonly  autocomplete="off"  ></td>';

            echo '<td style="width:10%">'.$CURRENT_QTY.'</td>';
            echo '<td style="width:10%">'.$MAIN_UOMID_DES.'</td>';
            echo '<td style="width:10%"><input '.$ACTION_TYPE.' type="text" name="UserQty_'.$key.'" id="UserQty_'.$key.'"  value="'.$MainReceivedQty.'" onkeyup="checkStoreQty('.$ROW_ID.','.$ITEMID_REF.','.$ALT_UOMID_REF.',this.value,'.$key.','.$PO_PENDING_QTY.')" class="qtytext"  onkeypress="return isNumberDecimalKey(event,this)" autocomplete="off"  ></td>';
            echo '<td style="width:10%">'.$ALT_UOMID_DES.'</td>';
            echo '<td style="width:10%"><input '.$ACTION_TYPE.' type="text" name="AltUserQty_'.$key.'" id="AltUserQty_'.$key.'" value="'.$AultReceivedQty.'" class="qtytext"  autocomplete="off" readonly  ></td>';
            echo '<td style="width:10%"><input '.$ACTION_TYPE.' type="text" name="LOT_NO_'.$key.'" id="LOT_NO_'.$key.'" value="'.$LOT_NO.'"  class="qtytext" autocomplete="off"  ></td>';
            echo '<td style="width:10%"><input '.$ACTION_TYPE.' type="text" name="VENDOR_LOTNO_'.$key.'" id="VENDOR_LOTNO_'.$key.'" value="'.$VENDOR_LOTNO.'"  class="qtytext"  autocomplete="off"  ></td>';
            echo '<td hidden><input type="hidden" name="PO_PENDING_QTY_'.$key.'" id="PO_PENDING_QTY_'.$key.'" value="'.$PO_PENDING_QTY.'" class="qtytext" ></td>';
            echo '<td hidden><input type="hidden" name="BATCHID_'.$key.'" id="BATCHID_'.$key.'" value="'.$StoreRowId.'" class="qtytext" ></td>';
            echo '<td hidden><input type="hidden" name="ROWID_'.$key.'" id="ROWID_'.$key.'" value="'.$ROW_ID.'" class="qtytext" ></td>';
            echo '<td hidden><input type="hidden" name="TOTAL_STOCK_'.$key.'" id="TOTAL_STOCK_'.$key.'" value="'.$CURRENT_QTY.'" class="qtytext" ></td>';
            echo '<td hidden><input type="hidden" name="BATCHNOA_'.$key.'" id="BATCHNOA_'.$key.'" value="'.$BATCHNOA.'" class="qtytext" ></td>';
            echo '<td hidden><input type="hidden" name="RACK_QTY_'.$key.'" id="RACK_QTY_'.$key.'" class="qtytext" ></td>';
            echo '<td hidden><input type="hidden" name="EXPIRY_APPLICABLE_'.$key.'" id="EXPIRY_APPLICABLE_'.$key.'" value="'.$EXPIRY.'" class="qtytext" ></td>';
            echo '<td hidden><input type="hidden" name="STOREID_'.$key.'" id="STOREID_'.$key.'" value="'.$val->STID.'" class="qtytext" ></td>';
            echo '</tr>';
            echo '</tr>';
        }
        
        echo '</tbody>';

        echo '<tr  class="participantRowFotter">
        <td style="text-align:center;font-weight:bold;">TOTAL</td>  
        <td></td> 
        <td></td>   
        <td id="strSOTCK_total"   style="text-align:center;font-weight:bold;"></td>    
        <td></td>                                                                    
        <td id="strDISPATCH_MAIN_QTY_total"       style="text-align:left;font-weight:bold;"></td>
        <td></td>
        <td id="DISPATCH_ALT_QTY_total"   style="text-align:left;font-weight:bold;"></td>
        <td colspan="2"></td>
      
                                  
        </tr>';

        exit();
    }

    public function getCodeNo(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $VID_REF        =   $request['id'];
        $GE_TYPE        =   $request['GE_TYPE'];
        $fieldid        =   $request['fieldid'];
        

         $ObjData =  DB::select("SELECT GEID,GE_NO,GE_DT,PO_NO FROM TBL_TRN_IMGE01_HDR 
        WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF'  
        AND VID_REF='$VID_REF' AND STATUS='A' AND GETYPE='$GE_TYPE' and GEID not in (select A.GEID_REF from TBL_TRN_IGRN02_MAT A
		INNER JOIN TBL_TRN_IGRN02_HDR B ON B.GRNID = A.GRNID_REF WHERE B.STATUS != 'C')");
       // dd($request->all()); 

        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){
                
                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="rgpcode_'.$dataRow->GEID .'"  class="clssqid" value="'.$dataRow->GEID.'" ></td>
                <td class="ROW2">'.$dataRow->GE_NO;
                $row = $row.'<input type="hidden" id="txtrgpcode_'.$dataRow->GEID.'" data-desc="'.$dataRow->GE_NO.'"  value="'.$dataRow->GEID.'"/></td>
                <td class="ROW3">'.$dataRow->GE_DT.'</td></tr>';
                echo $row;
                
            }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
    }
	
	public function getCodeNoEDIT(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $VID_REF        =   $request['id'];
        $GE_TYPE        =   $request['GE_TYPE'];
        $fieldid        =   $request['fieldid'];
		$GRNID			= 	$request['GRNID'];
        

         $ObjData =  DB::select("SELECT GEID,GE_NO,GE_DT,PO_NO FROM TBL_TRN_IMGE01_HDR 
        WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF'  
        AND VID_REF='$VID_REF' AND STATUS='A' AND GETYPE='$GE_TYPE' and GEID not in (select A.GEID_REF from TBL_TRN_IGRN02_MAT A
		INNER JOIN TBL_TRN_IGRN02_HDR B ON B.GRNID = A.GRNID_REF WHERE B.STATUS != 'C' AND B.GRNID != $GRNID)");
       // dd($request->all()); 

        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){
                
                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="rgpcode_'.$dataRow->GEID .'"  class="clssqid" value="'.$dataRow->GEID.'" ></td>
                <td class="ROW2">'.$dataRow->GE_NO;
                $row = $row.'<input type="hidden" id="txtrgpcode_'.$dataRow->GEID.'" data-desc="'.$dataRow->GE_NO.'"  value="'.$dataRow->GEID.'"/></td>
                <td class="ROW3">'.$dataRow->GE_DT.'</td></tr>';
                echo $row;
                
            }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
    }

    public function getPoCodeNo(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $id             =   $request['id'];
        $GE_TYPE        =   $request['GE_TYPE'];
        $fieldid        = $request['fieldid'];


  
        $DataArr        =   DB::table('TBL_TRN_IMGE01_HDR')->where('GEID','=',$id)->select('PO_NO')->first();
        
       // dd($DataArr); 

        if(!empty($DataArr)){
            $ListArr        =   explode(",",$DataArr->PO_NO);
            $row = '';

            if($GE_TYPE =="IPO"){

                foreach($ListArr as $key=>$value){

                    //$dataRow    = DB::table('TBL_TRN_IPO_HDR')->where('IPO_ID','=',$value)->select('IPO_ID','IPO_NO','IPO_DT')->first();

                    $dataRow = DB::table('TBL_TRN_IPO_HDR')
                    ->where('TBL_TRN_IPO_HDR.IPO_ID','=',$value) 
                   ->where('TBL_TRN_IPO_MAT.PENDING_QTY','>',0) 
                    ->leftJoin('TBL_TRN_IPO_MAT', 'TBL_TRN_IPO_HDR.IPO_ID','=','TBL_TRN_IPO_MAT.IPO_ID_REF')                   
                    ->select('TBL_TRN_IPO_HDR.IPO_ID','TBL_TRN_IPO_HDR.IPO_NO','TBL_TRN_IPO_HDR.IPO_DT')
                    ->first();

                  //  dd($dataRow); 

                    if(!empty($dataRow)){


                    $row        = $row.'<tr >
                                    <td style="width:10%;"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="pocode_'.$dataRow->IPO_ID .'"  class="clsspoid" value="'.$dataRow->IPO_ID.'" ></td>
                                    <td style="width:30%;">'.$dataRow->IPO_NO;
                    $row        = $row.'<input type="hidden" id="txtpocode_'.$dataRow->IPO_ID.'" data-desc="'.$dataRow->IPO_NO.'" value="'.$dataRow->IPO_ID.'"/></td>
                                    <td style="width:30%;">'.$dataRow->IPO_DT.'</td>
                                    <td style="width:30%;"></td>
                                    </tr>';

                    }
                    else{

                        echo '<tr><td colspan="2">Record not found.</td></tr>';

                    }
                
                }
            }

            else if($GE_TYPE =="BPO"){

                foreach($ListArr as $key=>$value){

                   // $dataRow    = DB::table('TBL_TRN_PROR03_HDR')->where('BPOID','=',$value)->select('BPOID','BPO_NO','BPO_DT')->first();

                    $data = [$value,$CYID_REF,$BRID_REF];

                    $dataRow = DB::select('EXEC SP_TRN_GET_BPO ?,?,?', $data);

                    //dd($dataRow);

                    if(!empty($dataRow)){

                    $row        = $row.'<tr >
                                    <td style="width:10%;"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="pocode_'.$dataRow[0]->BPOID .'"  class="clsspoid" value="'.$dataRow[0]->BPOID.'" ></td>
                                    <td style="width:30%;">'.$dataRow[0]->BPO_NO;
                    $row        = $row.'<input type="hidden" id="txtpocode_'.$dataRow[0]->BPOID.'" data-desc="'.$dataRow[0]->BPO_NO.'" value="'.$dataRow[0]->BPOID.'"/></td>
                                    <td style="width:30%;">'.$dataRow[0]->BPO_DT.'</td>
                                    <td style="width:30%;"></td>
                                    </tr>';
                                

                    }else{

                          echo '<tr><td colspan="2">Record not found.</td></tr>'; 

                    }
                
                }
            }
            else{

                foreach($ListArr as $key=>$value){
                  //  dd($value); 

                   // $dataRow    = DB::table('TBL_TRN_PROR01_HDR')->where('POID','=',$value)->select('POID','PO_NO','PO_DT')->first();

                    $dataRow = DB::table('TBL_TRN_PROR01_HDR')
                    ->where('TBL_TRN_PROR01_HDR.POID','=',$value) 
                    ->where('TBL_TRN_PROR01_MAT.PENDING_QTY','>',0) 
                    ->leftJoin('TBL_TRN_PROR01_MAT', 'TBL_TRN_PROR01_HDR.POID','=','TBL_TRN_PROR01_MAT.POID_REF')
                    ->leftJoin('TBL_MST_DEPARTMENT', 'TBL_TRN_PROR01_HDR.DEPID_REF','=','TBL_MST_DEPARTMENT.DEPID')
                    ->select('TBL_TRN_PROR01_HDR.POID','TBL_TRN_PROR01_HDR.PO_NO','TBL_TRN_PROR01_HDR.PO_DT', 'TBL_MST_DEPARTMENT.NAME','TBL_TRN_PROR01_MAT.PENDING_QTY')
                    ->first();

                    //dd($dataRow); 

                    if(!empty($dataRow)){


                    $row        = $row.'<tr style="width:10%;" >
                                <td style="style"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="pocode_'.$dataRow->POID .'"  class="clsspoid" value="'.$dataRow->POID.'" ></td>
                            
                                <td style="width:30%;">'.$dataRow->PO_NO;

                    $row        = $row.'<input type="hidden" id="txtpocode_'.$dataRow->POID.'" data-desc="'.$dataRow->PO_NO.'" value="'.$dataRow->POID.'"/></td>
                                <td style="width:30%;">'.$dataRow->PO_DT.'</td>;


                                <td style="width:30%;">'.$dataRow->NAME.'</td></tr>';

                    }else{

                        echo '<tr><td colspan="2">Record not found.</td></tr>';

                    }

                
                }
            }


            echo $row;
        }
        else{
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
            echo $auomqty;
        }else{
            echo '0';
        }
        exit();
    }


   

    public function checkCompany($str){
        $COMPANY_NAME   =   DB::table('TBL_MST_COMPANY')->where('STATUS','=','A')->where('CYID','=',Auth::user()->CYID_REF)->select('TBL_MST_COMPANY.NAME')->first()->NAME;
        $result = strpos(strtolower($COMPANY_NAME),$str)!== false?'1':'';
        return $result;
    }


    

    public function check_rejected_item(Request $request){

        $result     =   [];
        $id         =   $request['id'];
        $ObjData    =   DB::table('TBL_TRN_QIG_HDR')
                        ->where('GRNID_REF','=',$id)
                        ->where('STATUS','=','A')
                        ->select('REJECTED_QTY','REJECTED_STID_REF','ITEMID_REF')
                        ->get();

        if(isset($ObjData) && !empty($ObjData)){

            foreach($ObjData as $key=>$val){
            
                $REJECTED_QTY       =   $val->REJECTED_QTY;
                $REJECTED_STID_REF  =   $val->REJECTED_STID_REF;
                $ITEMID_REF         =   $val->ITEMID_REF;

                if($REJECTED_QTY > 0){

                    $ObjStore    =   DB::table('TBL_TRN_IGRN02_MULTISTORE')
                                    ->where('STID_REF','=',$REJECTED_STID_REF)
                                    ->where('GRNID_REF','=',$id)
                                    ->where('ITEMID_REF','=',$ITEMID_REF)
                                    ->first();

                    if(empty($ObjStore)){
                        $result[]     =   '1';
                    }
                    else{

                        if($REJECTED_QTY != $ObjStore->RECEIVED_QTYM){
                            $result[]    =   '1';
                        }
                    }

                }

            }

        }

        if(!empty($result)){
            echo '1';
        }
        else{
            echo '';
        }
        
        exit();
    }


    public function check_qcserial_item(Request $request){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $id             =   $request['id'];    
        $result         =   [];       
        $objMAT =   DB::select("SELECT T1.ITEMID_REF FROM TBL_TRN_IGRN02_MAT T1 WHERE T1.GRNID_REF='$id' ORDER BY T1.GRN_MATID ASC"); 
        
        if(isset($objMAT) && !empty($objMAT)){

            foreach($objMAT as $key=>$val){
            
                $ITEMID_REF =   $val->ITEMID_REF;
                $CHECK_QC   =   DB::table('TBL_MST_ITEMCHECKFLAG')->where('ITEMID_REF','=',$ITEMID_REF)->first();
                $QCA        =   $CHECK_QC->QCA;
                $SRNOA      =   $CHECK_QC->SRNOA;

                if($QCA =='1'){
                    
                    $data  =   DB::table('TBL_TRN_QIG_HDR')
                                ->where('CYID_REF','=',$CYID_REF)
                                ->where('BRID_REF','=',$BRID_REF)
                                ->where('GRNID_REF','=',$id)
                                ->where('ITEMID_REF','=',$ITEMID_REF)
                                ->where('STATUS','=','A')
                                ->count();
                    
                    if($data < 1){
                        $result[]     =   '1';
                    }
                }

                if($SRNOA =='1'){

                    $data   =   DB::table('TBL_TRN_BARCODE_HDR')
                                ->join('TBL_TRN_BARCODE_MAT', 'TBL_TRN_BARCODE_HDR.BRCID', '=', 'TBL_TRN_BARCODE_MAT.BRCID_REF')
                                ->where('TBL_TRN_BARCODE_HDR.CYID_REF','=',$CYID_REF)
                                ->where('TBL_TRN_BARCODE_HDR.BRID_REF','=',$BRID_REF)
                                ->where('TBL_TRN_BARCODE_HDR.DOCID_REF','=',$id)
                                ->where('TBL_TRN_BARCODE_HDR.STATUS','=','A')
                                ->where('TBL_TRN_BARCODE_MAT.ITEMID_REF','=',$ITEMID_REF)
                                ->count();

                    if($data < 1){
                        $result[]     =   '1';
                    }
                }

            }

        }

        if(!empty($result)){
            echo '1';
        }
        else{
            echo '';
        }
        
        exit();
    }


    public function getBinDetails(Request $request){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $STID           =   $request['STID'];
        $STQTY          =   $request['STQTY'];
        $MATERIAL_ROWID =   $request['MATERIAL_ROWID'];
        $RACK_QTY       =   $request['RACK_QTY'];
        $BINARRAY       =   isset($request['BINARRAY']) && $request['BINARRAY'] !=''?explode(',',$request['BINARRAY']):[];
        $ACTION_TYPE    =   $request['ACTION_TYPE'] =="VIEW"?'disabled':'';
        $dataArr        =   array();

        if($RACK_QTY !=""){
            $exp        =   explode(",",$RACK_QTY);
            foreach($exp as $val){
                $keyid              =   explode("###",$val);
                $dataArr[$keyid[0]] =   $keyid[3];
            }
        }

        $TBL_MST_STORERACK  =   DB::SELECT("SELECT 
        T1.*,T2.STCODE,T2.NAME AS STNAME 
        FROM TBL_MST_STORERACK T1
        LEFT JOIN TBL_MST_STORE T2 ON T1.STID_REF=T2.STID
        WHERE T1.STID_REF IN($STID)
        ");

        echo'
        <thead>
            <tr>
                <th style="text-align:left;font-weight:bold;">Store</th>
                <th style="text-align:left;font-weight:bold;">Rack No</th>
                <th style="text-align:left;font-weight:bold;">Bin No</th>
                <th style="text-align:left;font-weight:bold;">Qty</th>
            </tr>
        <tbody>
        <tbody>';

        $TotalBinQty=0;
        foreach($TBL_MST_STORERACK as $key=>$val){
            
            $RACKID         =   $val->RACKID;
            $RACKNO         =   $val->RACKNO;
            $BINNO          =   $val->BINNO;
            $STCODE         =   $val->STCODE;
            $STNAME         =   $val->STNAME;
            $BINQTY         =   array_key_exists($RACKID, $dataArr)?$dataArr[$RACKID]:'';
           // $readonly       =   in_array(intval($BINNO),$BINARRAY)?'readonly':'';
            $TBINQTY        =   $BINQTY !=''?$BINQTY:0;
            $TotalBinQty    =   $TotalBinQty+$TBINQTY; 

            echo'
            <tr class="participantRow44" >
                <td style="text-align:left;width:30px;0%;font-size:11px;">'.$STCODE.' - '.$STNAME.'</td>
                <td style="text-align:left;width:30%;font-size:11px;"><input type="text"    id="RACK_NO_'.$key.'"   value="'.$RACKNO.'" class="qtytext" readonly ></td>
                <td style="text-align:left;width:20%;font-size:11px;"><input type="text"    id="BIN_NO_'.$key.'"    value="'.$BINNO.'"  class="qtytext" readonly ></td>
                <td style="text-align:left;width:20%;font-size:11px;"><input type="text"    id="BIN_QTY'.$key.'"    value="'.$BINQTY.'"   onkeyup="sumBinQty()" class="qtytext"  onkeypress="return isNumberDecimalKey(event,this)" autocomplete="off" style="text-align:right;" '.$ACTION_TYPE.' ></td>
                <td hidden><input type="text"   id="RACK_ID_'.$key.'" value="'.$RACKID.'"></td>
                <td hidden><input type="text"   id="MATERIAL_ROWID_'.$key.'" value="'.$MATERIAL_ROWID.'" ></td>
            </tr>'; 
        }
        echo'
        </tbody>
        <thead>
            <tr>
                <th style="text-align:left;font-weight:bold;">Total Received Qty (MU): '.$STQTY.'</th>
                <th style="text-align:left;font-weight:bold;"></th>
                <th style="text-align:left;font-weight:bold;">Total</th>
                <td style="text-align:left;font-weight:bold;" ><input type="text" name="TOTAL_BIN" id="TOTAL_BIN" value='.$TotalBinQty.' class="qtytext" readonly style="text-align:right;" ></td>
                <td hidden><input type="text" name="TOTAL_STORE" id="TOTAL_STORE" value="'.$STQTY.'"></td>
            </tr>
        </thead>';
        exit();
    }

    public function getBinApplicable(Request $request){

        $count =  DB::table('TBL_MST_ITEMCHECKFLAG')
        ->where('ITEMID_REF','=',$request['ITEMID_REF'])
        ->where('BIN','=','1')
        ->count();

        echo $count;die;
       
    }
    
}
