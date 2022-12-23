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

class TrnFrm357Controller extends Controller{

    protected $form_id  = 357;
    protected $vtid_ref = 443;
    protected $view     = "transactions.JobWork.JobWorkGrn.trnfrm357";
   
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
       
        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF'); 
		
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
        WHERE  AUD.VID=T1.GRJID AND  AUD.CYID_REF=T1.CYID_REF AND  AUD.BRID_REF=T1.BRID_REF AND  
        AUD.FYID_REF=T1.FYID_REF AND  AUD.VTID_REF=T1.VTID_REF AND AUD.ACTIONNAME='ADD'       
        ) AS CREATED_BY
        FROM TBL_TRN_GRJ_HDR T1
        INNER JOIN TBL_TRN_AUDITTRAIL T2 ON T1.GRJID=T2.VID AND T1.VTID_REF=T2.VTID_REF AND T1.CYID_REF=T2.CYID_REF AND T1.BRID_REF=T2.BRID_REF 
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' AND T2.VTID_REF = '$this->vtid_ref' AND T2.ACTID IN (SELECT max(ACTID) FROM TBL_TRN_AUDITTRAIL A WHERE T2.VTID_REF = A.VTID_REF AND T2.VID = A.VID)
        ORDER BY T1.GRJID DESC 
        ");

        return view($this->view,compact(['REQUEST_DATA','FormId','objRights','objDataList','FormId','DATA_STATUS']));
    }
	
	public function ViewReport($request) {

        $box = $request;        
        $myValue=  array();
        parse_str($box, $myValue);
           
        $GRJID       =   $myValue['GRJID'];
        $Flag       =   $myValue['Flag'];

        // $objSalesOrder = DB::table('TBL_TRN_PROR01_HDR')
        // ->where('TBL_TRN_PROR01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        // ->where('TBL_TRN_PROR01_HDR.BRID_REF','=',Auth::user()->BRID_REF)
        // ->where('TBL_TRN_PROR01_HDR.POID','=',$POID)
        // ->select('TBL_TRN_PROR01_HDR.*')
        // ->first();
        
        
        $ssrs = new \SSRS\Report('http://103.139.58.23:8181//ReportServer/', array('username' => 'App', 'password' => 'admin@123'));
        $result = $ssrs->loadReport('/ERP/GRNJPrint');
		//$result = $ssrs->loadReport('/ERP/POPrint -ZEP');
        
        $reportParameters = array(
            'GRJID' => $GRJID,
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
        
        $Status         =   "A";
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $objlastdt      =   $this->getLastdt();
        $objStoreList   =   $this->getStoreList();
        $objGEList      =   $this->getGEList();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_GRJ_HDR',
            'HDR_ID'=>'GRJID',
            'HDR_DOC_NO'=>'GRNNO',
            'HDR_DOC_DT'=>'GRNDT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        
       
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_GRJ")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDFGRJID')->from('TBL_MST_UDFFOR_GRJ')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                     
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                
                   

        $objUdf  = DB::table('TBL_MST_UDFFOR_GRJ')
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
        $checkCompany   =   $this->checkCompany('zep'); 

        return view($this->view.'add', compact(
            ['AlpsStatus','FormId','objUdf','objCountUDF','objlastdt','objStoreList','objGEList','TabSetting','checkCompany','doc_req','docarray']
        ));       
    }

    public function getLastdt(){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        return  DB::select('SELECT MAX(GRNDT) GRNDT FROM TBL_TRN_GRJ_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
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

    

    public function getGEList(){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $data = DB::select("SELECT 
        T1.GEJWOID AS DOC_ID,GENO AS DOC_CODE,T1.GEDT AS DOC_DESC,T1.VID_REF,T1.VENDOR_CHALLANNO,T1.VENDOR_BILLNO,
        T2.SGLCODE,T2.SLNAME
        FROM TBL_TRN_GEJWO_HDR AS T1
        INNER JOIN TBL_MST_SUBLEDGER T2 ON T1.VID_REF=T2.SGLID AND T2.BELONGS_TO = 'Vendor'
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.STATUS='A'");

        return $data;
    }

    public function save(Request $request) {

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
    
        $r_count1 = $request['Row_Count1'];
        $r_count3 = $request['Row_Count3'];
        $r_count5 = $request['Row_Count5'];
        
        $req_data=array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $StoreArr   =   array();
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $exp        =   explode(",",$ITEMROWID);

                foreach($exp as $val){
                    $keyid              =   explode("_",$val);
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
                    'JWCID_REF'         => $request['JWCID_REF_'.$i],
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'         => $request['MAIN_UOMID_REF_'.$i],
                    'VENDOR_BATCHNO'    => $request['VENDOR_BATCHNO_'.$i],
                    'OURLOT_NO'         => $request['OURLOT_NO_'.$i],
                    'RECEIVED_QTY'      => $request['PD_OR_QTY_'.$i],
                    'SHORT_QTY'         => $request['BL_SOQTY_'.$i],
                    'REMARKS'           => $request['REMARKS_'.$i],
                    'JWOID_REF'         => $request['JWOID_REF_'.$i],
                    'PROID_REF'         => $request['PROID_REF_'.$i],
                    'SOID_REF'          => $request['SOID_REF_'.$i],
                    'SQID_REF'          => $request['SQID_REF_'.$i],
                    'SEID_REF'          => $request['SEID_REF_'.$i],
                    'STORE_NAME'        => $request['STORE_NAME_'.$i],
                    'STID_REF'    	    => $STID_REF,
                    'BATCH_QTY_REF'     => $request['HiddenRowId_'.$i],  
                    'RATE'              => $request['ITEMRATE_'.$i],
                    'JWRATE'            => $request['JWRATE_'.$i],
                    'JWC_QTY'            => $request['QTY_'.$i],  
                ];

            }
        }


        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);


        $req_data5=array();
        for ($i=0; $i<=$r_count5; $i++){
            if(isset($request['REQ_ITEMID_REF_'.$i])){

                $req_data5[$i] = [
                    'JWCID_REF'      	=> $request['REQ_JWCID_REF_'.$i],
                    'ITEMID_REF'    	=> $request['REQ_ITEMID_REF_'.$i],
                    'IN_JWO_QTY'    	=> $request['REQ_STD_BOM_QTY_'.$i],
                    'ACTUAL_CON_QTY'	=> $request['REQ_JWC_QTY_'.$i],
                    'CONSUMED_LOTNO'	=> $request['REQ_CONSUMED_LOTNO_'.$i],
                    'REMARKS'	        => $request['REQ_REMARKS_'.$i],
                    'MAIN_ITEMID_REF'   => $request['REQ_SOITEMID_REF_'.$i],
                    'JWOID_REF'      	=> $request['REQ_JWOID_REF_'.$i],
                    'PROID_REF'      	=> $request['REQ_PROID_REF_'.$i],
                    'SOID_REF'      	=> $request['REQ_SOID_REF_'.$i],
                    'SQID_REF'     		=> $request['REQ_SQID_REF_'.$i],  
                    'SEID_REF'     		=> $request['REQ_SEID_REF_'.$i],
                ];

            }
        }


		if($r_count5 > 0){
            $wrapped_links5["CON"] = $req_data5; 
			$XMLREQ = ArrayToXml::convert($wrapped_links5);
        }
        else{
            $XMLREQ=NULL;
        }
        

        $udffield_Data = [];      
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFGRJID_REF'] = $request['udffie_'.$i]; 
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


        $req_data11=array();
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
                        
                        $STOCK_INHAND =  DB::SELECT("SELECT SUM(CURRENT_QTY) AS CURRENT_QTY
                        FROM TBL_MST_BATCH 
                        WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF' AND STATUS='A' AND STID_REF='$key' AND ITEMID_REF='$ITEMID_REF'
                        ")[0]->CURRENT_QTY;


                        $req_data11[$i][] = [
                            'STID_REF'          => $key,
                            'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                            'UOMID_REF'         => $request['MAIN_UOMID_REF_'.$i],
                            'STOCK_INHAND'      => $STOCK_INHAND !=''?$STOCK_INHAND:0,
                            'RECEIVED_QTY'      => $request['PD_OR_QTY_'.$i],
                            'LOT_NO'            => $request['OURLOT_NO_'.$i],
                            'VENDOR_BATCHNO'    => $request['VENDOR_BATCHNO_'.$i],
                            'JWCID_REF'         => $request['JWCID_REF_'.$i],
                            'JWOID_REF'         => $request['JWOID_REF_'.$i],
                            'PROID_REF'         => $request['PROID_REF_'.$i],
                            'SOID_REF'          => $request['SOID_REF_'.$i],
                            'SQID_REF'          => $request['SQID_REF_'.$i],
                            'SEID_REF'          => $request['SEID_REF_'.$i]   
                        ];

                    }
                }

            }
        }
        
		if($r_count1 > 0){
            $wrapped_links11["MULTISTORE"] = $req_data11; 
			$XMLSTORE = ArrayToXml::convert($wrapped_links11);
        }
        else{
            $XMLSTORE=NULL;
        }
        
        
        $VTID_REF     =   $this->vtid_ref;
        
        $USERID_REF = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();

        $GRNNO          = $request['GRNNO'];
        $GRNDT          = $request['GRNDT'];
        $STID_REF       = NULL;
        $GEJWOID_REF    = $request['GEJWOID_REF'];
        $VID_REF        = $request['VID_REF'];
        $REMARKS        = $request['REMARKS'];
        
        $log_data = [ 
            $GRNNO,             $GRNDT,         $STID_REF,      $GEJWOID_REF,   $VID_REF,       
            $REMARKS,           $CYID_REF,      $BRID_REF,      $FYID_REF,      $VTID_REF,          
            $XMLMAT,            $XMLREQ,        $XMLUDF,        $XMLSTORE,      $USERID_REF,    
            Date('Y-m-d'),      Date('h:i:s.u'),$ACTIONNAME,    $IPADDRESS
        ];

        
        $sp_result = DB::select('EXEC SP_GRJ_IN ?,?,?,?,? ,?,?,?,?,? ,?,?,?,?,? ,?,?,?,?', $log_data); 
         
    
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

            $FormId = $this->form_id;
            $Status = "A";
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $USERID = Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

          
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objRights  =   DB::table('TBL_MST_USERROLMAP')
                            ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
                            ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
                            ->where('TBL_MST_USERROLMAP.BRID_REF','=',$BRID_REF)
                        
                            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID)
                            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                            ->first();

            $objlastdt      =   $this->getLastdt();
            $objStoreList   =   $this->getStoreList();
            $objGEList      =   $this->getGEList();

            $objMstResponse =   DB::table('TBL_TRN_GRJ_HDR AS T1')
                                ->leftJoin('TBL_MST_STORE AS T2', 'T1.STID_REF','=','T2.STID')
                                ->leftJoin('TBL_TRN_GEJWO_HDR AS T3', 'T1.GEJWOID_REF','=','T3.GEJWOID')
                                ->where('T1.FYID_REF','=',Session::get('FYID_REF'))
                                ->where('T1.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('T1.BRID_REF','=',Session::get('BRID_REF'))
                                ->where('T1.GRJID','=',$id)
                                ->select('T1.*','T2.STCODE','T2.NAME','T3.GENO','T3.VENDOR_BILLNO','T3.VENDOR_CHALLANNO')
                                ->first();


            $objMAT = DB::select("SELECT T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,
            T3.UOMCODE,T3.DESCRIPTIONS,
            T5.JWCNO
            FROM TBL_TRN_GRJ_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            LEFT JOIN TBL_TRN_JWC_HDR T5 ON T1.JWCID_REF=T5.JWCID
            WHERE T1.GRJID_REF='$id' ORDER BY T1.GRJID_REF ASC"); 


            $objREQ = DB::select("SELECT T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,T3.ICODE AS SOITEMID_CODE
            FROM TBL_TRN_GRJ_CON T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_ITEM T3 ON T1.MAIN_ITEMID_REF=T3.ITEMID
            WHERE T1.GRJID_REF='$id' ORDER BY T1.GRJID_REF ASC");

            
           
			$material_array=array();

			if(isset($objREQ) && !empty($objREQ)){
				foreach($objREQ as $row){

                    $JWCID_REF          =   $row->JWCID_REF;
                    $JWOID_REF          =   $row->JWOID_REF;
                    $PROID_REF          =   $row->PROID_REF;
                    $SOID_REF           =   $row->SOID_REF;
                    $SQID_REF           =   $row->SQID_REF;
                    $SEID_REF           =   $row->SEID_REF;
                    $MAIN_ITEMID_REF    =   $row->MAIN_ITEMID_REF;
                    $ITEMID_REF         =   $row->ITEMID_REF;
                    
                    $mitem_id           =   $JWCID_REF."_".$JWOID_REF."_".$PROID_REF."_".$SOID_REF."_".$SQID_REF."_".$SEID_REF."_".$MAIN_ITEMID_REF; 

                    $STD_BOM_QTY    =   DB::select("SELECT TOP 1 STD_BOM_QTY 
                    FROM TBL_TRN_JWO_REQ 
                    WHERE JWOID_REF='$JWOID_REF' AND ITEMID_REF='$ITEMID_REF' AND MAIN_ITEMID_REF='$MAIN_ITEMID_REF' 
                    AND PROID_REF='$PROID_REF' AND SOID_REF='$SOID_REF' AND SQID_REF='$SQID_REF' AND SEID_REF='$SEID_REF'");
					//dd($STD_BOM_QTY);
                    $material_array[]=array(
                        'JWO_REQID'=>NULL,
                        'ITEMID_REF'=>$ITEMID_REF,
                        'ICODE'=>$row->ICODE,
                        'NAME'=>$row->ITEM_NAME,
                        'MAIN_ITEMID'=>$MAIN_ITEMID_REF,
                        'IN_JWO_QTY'=>$row->IN_JWO_QTY,
                        'ACTUAL_CON_QTY'=>$row->ACTUAL_CON_QTY,
                        'CONSUMED_LOTNO'=>$row->CONSUMED_LOTNO,
                        'REMARKS'=>$row->REMARKS,
                        'STD_BOM_QTY'=> isset( $STD_BOM_QTY) &&  (!empty($STD_BOM_QTY) )? $STD_BOM_QTY[0]->STD_BOM_QTY :  1,
                        'MAIN_JWCID'=>$JWCID_REF,
                        'MAIN_JWOID'=>$JWOID_REF,
                        'MAIN_PROID'=>$PROID_REF,
                        'MAIN_SOID'=>$SOID_REF,
                        'MAIN_SQID'=>$SQID_REF,
                        'MAIN_SEID'=>$SEID_REF,
                        'MAIN_ITEM_ROWID'=>$mitem_id,
                        
                    );

				}
						
            }

            $objvendorcode2 =   DB::table('TBL_MST_SUBLEDGER')
                                ->where('BELONGS_TO','=','Vendor')
                                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('SGLID','=',$objMstResponse->VID_REF)    
                                ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                                ->first();

            $ObjBranch      =   [];
            $TAXSTATE       =   [];
            $objShpAddress  =   [] ;
            $objBillAddress =   [];


            $ObjUnionUDF    =   DB::table("TBL_MST_UDFFOR_GRJ")->select('*')
                                ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF){       
                                    $query->select('UDFGRJID')->from('TBL_MST_UDFFOR_GRJ')
                                    ->where('STATUS','=','A')
                                    ->where('PARENTID','=',0)
                                    ->where('DEACTIVATED','=',0)
                                    ->where('CYID_REF','=',$CYID_REF);                                  
                                    }
                                )
                                ->where('DEACTIVATED','=',0)
                                ->where('STATUS','<>','C')                    
                                ->where('CYID_REF','=',$CYID_REF);
                                    
            $objUdf         =   DB::table('TBL_MST_UDFFOR_GRJ')
                                ->where('STATUS','=','A')
                                ->where('PARENTID','=',0)
                                ->where('DEACTIVATED','=',0)
                                ->where('CYID_REF','=',$CYID_REF)
                                ->union($ObjUnionUDF)
                                ->get()->toArray();  

            $objCountUDF    =   count($objUdf);
        
        
            $objtempUdf     =   $objUdf;
            foreach ($objtempUdf as $index => $udfvalue) {

                $objSavedUDF =  DB::table('TBL_TRN_GRJ_UDF')
                                ->where('GRJID_REF','=',$id)
                                ->where('UDFGRJID_REF','=',$udfvalue->UDFGRJID)
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
            $ActionStatus   =   "";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            $checkCompany   =   $this->checkCompany('zep'); 

           // dd($checkCompany); 

            return view($this->view.'edit', compact([
                'AlpsStatus','FormId','objRights','objMAT','objUdf','objCountUDF','objMstResponse','objvendorcode2',
                'material_array','objShpAddress','objBillAddress','TAXSTATE','objlastdt','objStoreList','objGEList',
                'ActionStatus','TabSetting','checkCompany'
                ]));

        }
    
    }
    

    public function view($id=NULL){       
        
        if(!is_null($id)){

            $FormId = $this->form_id;
            $Status = "A";
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $USERID = Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

          
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objRights  =   DB::table('TBL_MST_USERROLMAP')
                            ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
                            ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
                            ->where('TBL_MST_USERROLMAP.BRID_REF','=',$BRID_REF)
                            ->where('TBL_MST_USERROLMAP.FYID_REF','=',$FYID_REF)
                            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID)
                            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                            ->first();

            $objlastdt      =   $this->getLastdt();
            $objStoreList   =   $this->getStoreList();
            $objGEList      =   $this->getGEList();

            $objMstResponse =   DB::table('TBL_TRN_GRJ_HDR AS T1')
                                ->leftJoin('TBL_MST_STORE AS T2', 'T1.STID_REF','=','T2.STID')
                                ->leftJoin('TBL_TRN_GEJWO_HDR AS T3', 'T1.GEJWOID_REF','=','T3.GEJWOID')
                                ->where('T1.FYID_REF','=',Session::get('FYID_REF'))
                                ->where('T1.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('T1.BRID_REF','=',Session::get('BRID_REF'))
                                ->where('T1.GRJID','=',$id)
                                ->select('T1.*','T2.STCODE','T2.NAME','T3.GENO','T3.VENDOR_BILLNO','T3.VENDOR_CHALLANNO')
                                ->first();


            $objMAT = DB::select("SELECT T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,
            T3.UOMCODE,T3.DESCRIPTIONS,
            T5.JWCNO
            FROM TBL_TRN_GRJ_MAT T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            LEFT JOIN TBL_TRN_JWC_HDR T5 ON T1.JWCID_REF=T5.JWCID
            WHERE T1.GRJID_REF='$id' ORDER BY T1.GRJID_REF ASC"); 


            $objREQ = DB::select("SELECT T1.*,
            T2.ICODE,T2.NAME AS ITEM_NAME,T3.ICODE AS SOITEMID_CODE
            FROM TBL_TRN_GRJ_CON T1
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_ITEM T3 ON T1.MAIN_ITEMID_REF=T3.ITEMID
            WHERE T1.GRJID_REF='$id' ORDER BY T1.GRJID_REF ASC");

           
			$material_array=array();

			if(isset($objREQ) && !empty($objREQ)){
				foreach($objREQ as $row){

                    $JWCID_REF          =   $row->JWCID_REF;
                    $JWOID_REF          =   $row->JWOID_REF;
                    $PROID_REF          =   $row->PROID_REF;
                    $SOID_REF           =   $row->SOID_REF;
                    $SQID_REF           =   $row->SQID_REF;
                    $SEID_REF           =   $row->SEID_REF;
                    $MAIN_ITEMID_REF    =   $row->MAIN_ITEMID_REF;
                    $ITEMID_REF         =   $row->ITEMID_REF;
                    
                    $mitem_id           =   $JWCID_REF."_".$JWOID_REF."_".$PROID_REF."_".$SOID_REF."_".$SQID_REF."_".$SEID_REF."_".$MAIN_ITEMID_REF; 

                    $STD_BOM_QTY    =   DB::select("SELECT TOP 1 STD_BOM_QTY 
                    FROM TBL_TRN_JWO_REQ 
                    WHERE JWOID_REF='$JWOID_REF' AND ITEMID_REF='$ITEMID_REF' AND MAIN_ITEMID_REF='$MAIN_ITEMID_REF' 
                    AND PROID_REF='$PROID_REF' AND SOID_REF='$SOID_REF' AND SQID_REF='$SQID_REF' AND SEID_REF='$SEID_REF'");

                    $material_array[]=array(
                        'JWO_REQID'=>NULL,
                        'ITEMID_REF'=>$ITEMID_REF,
                        'ICODE'=>$row->ICODE,
                        'NAME'=>$row->ITEM_NAME,
                        'MAIN_ITEMID'=>$MAIN_ITEMID_REF,
                        'IN_JWO_QTY'=>$row->IN_JWO_QTY,
                        'ACTUAL_CON_QTY'=>$row->ACTUAL_CON_QTY,
                        'CONSUMED_LOTNO'=>$row->CONSUMED_LOTNO,
                        'REMARKS'=>$row->REMARKS,
                        'STD_BOM_QTY'=>isset( $STD_BOM_QTY) &&  (!empty($STD_BOM_QTY) )? $STD_BOM_QTY[0]->STD_BOM_QTY :  1,
                        'MAIN_JWCID'=>$JWCID_REF,
                        'MAIN_JWOID'=>$JWOID_REF,
                        'MAIN_PROID'=>$PROID_REF,
                        'MAIN_SOID'=>$SOID_REF,
                        'MAIN_SQID'=>$SQID_REF,
                        'MAIN_SEID'=>$SEID_REF,
                        'MAIN_ITEM_ROWID'=>$mitem_id,
                        
                    );

				}
						
            }

            $objvendorcode2 =   DB::table('TBL_MST_SUBLEDGER')
                                ->where('BELONGS_TO','=','Vendor')
                                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('SGLID','=',$objMstResponse->VID_REF)    
                                ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
                                ->first();

            $ObjBranch      =   [];
            $TAXSTATE       =   [];
            $objShpAddress  =   [] ;
            $objBillAddress =   [];


            $ObjUnionUDF    =   DB::table("TBL_MST_UDFFOR_GRJ")->select('*')
                                ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF){       
                                    $query->select('UDFGRJID')->from('TBL_MST_UDFFOR_GRJ')
                                    ->where('STATUS','=','A')
                                    ->where('PARENTID','=',0)
                                    ->where('DEACTIVATED','=',0)
                                    ->where('CYID_REF','=',$CYID_REF);                                  
                                    }
                                )
                                ->where('DEACTIVATED','=',0)
                                ->where('STATUS','<>','C')                    
                                ->where('CYID_REF','=',$CYID_REF);
                                    
            $objUdf         =   DB::table('TBL_MST_UDFFOR_GRJ')
                                ->where('STATUS','=','A')
                                ->where('PARENTID','=',0)
                                ->where('DEACTIVATED','=',0)
                                ->where('CYID_REF','=',$CYID_REF)
                                ->union($ObjUnionUDF)
                                ->get()->toArray();  

            $objCountUDF    =   count($objUdf);
        
        
            $objtempUdf     =   $objUdf;
            foreach ($objtempUdf as $index => $udfvalue) {

                $objSavedUDF =  DB::table('TBL_TRN_GRJ_UDF')
                                ->where('GRJID_REF','=',$id)
                                ->where('UDFGRJID_REF','=',$udfvalue->UDFGRJID)
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
            $ActionStatus   =   "disabled";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            $checkCompany   =   $this->checkCompany('zep'); 

            return view($this->view.'view', compact([
                'AlpsStatus','FormId','objRights','objMAT','objUdf','objCountUDF','objMstResponse','objvendorcode2',
                'material_array','objShpAddress','objBillAddress','TAXSTATE','objlastdt','objStoreList','objGEList',
                'ActionStatus','TabSetting','checkCompany'
                ]));

        }
    
    }
     
    public function update(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $r_count1 = $request['Row_Count1'];
        $r_count3 = $request['Row_Count3'];
        $r_count5 = $request['Row_Count5'];
        
        $req_data=array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $StoreArr   =   array();
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $exp        =   explode(",",$ITEMROWID);

                foreach($exp as $val){
                    $keyid              =   explode("_",$val);
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
                    'JWCID_REF'         => $request['JWCID_REF_'.$i],
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'         => $request['MAIN_UOMID_REF_'.$i],
                    'VENDOR_BATCHNO'    => $request['VENDOR_BATCHNO_'.$i],
                    'OURLOT_NO'         => $request['OURLOT_NO_'.$i],
                    'RECEIVED_QTY'      => $request['PD_OR_QTY_'.$i],
                    'SHORT_QTY'         => $request['BL_SOQTY_'.$i],
                    'REMARKS'           => $request['REMARKS_'.$i],
                    'JWOID_REF'         => $request['JWOID_REF_'.$i],
                    'PROID_REF'         => $request['PROID_REF_'.$i],
                    'SOID_REF'          => $request['SOID_REF_'.$i],
                    'SQID_REF'          => $request['SQID_REF_'.$i],
                    'SEID_REF'          => $request['SEID_REF_'.$i],
                    'STORE_NAME'        => $request['STORE_NAME_'.$i],
                    'STID_REF'    	    => $STID_REF,
                    'BATCH_QTY_REF'     => $request['HiddenRowId_'.$i],  
                    'RATE'              => $request['ITEMRATE_'.$i],
                    'JWRATE'            => $request['JWRATE_'.$i],
                    'JWC_QTY'            => $request['QTY_'.$i],  
                    
                ];

            }
        }


        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);


        $req_data5=array();
        for ($i=0; $i<=$r_count5; $i++){
            if(isset($request['REQ_ITEMID_REF_'.$i])){

                $req_data5[$i] = [
                    'JWCID_REF'      	=> $request['REQ_JWCID_REF_'.$i],
                    'ITEMID_REF'    	=> $request['REQ_ITEMID_REF_'.$i],
                    'IN_JWO_QTY'    	=> $request['REQ_STD_BOM_QTY_'.$i],
                    'ACTUAL_CON_QTY'	=> $request['REQ_JWC_QTY_'.$i],
                    'CONSUMED_LOTNO'	=> $request['REQ_CONSUMED_LOTNO_'.$i],
                    'REMARKS'	        => $request['REQ_REMARKS_'.$i],
                    'MAIN_ITEMID_REF'   => $request['REQ_SOITEMID_REF_'.$i],
                    'JWOID_REF'      	=> $request['REQ_JWOID_REF_'.$i],
                    'PROID_REF'      	=> $request['REQ_PROID_REF_'.$i],
                    'SOID_REF'      	=> $request['REQ_SOID_REF_'.$i],
                    'SQID_REF'     		=> $request['REQ_SQID_REF_'.$i],  
                    'SEID_REF'     		=> $request['REQ_SEID_REF_'.$i],
                ];

            }
        }


		if($r_count5 > 0){
            $wrapped_links5["CON"] = $req_data5; 
			$XMLREQ = ArrayToXml::convert($wrapped_links5);
        }
        else{
            $XMLREQ=NULL;
        }
        

        $udffield_Data = [];      
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFGRJID_REF'] = $request['udffie_'.$i]; 
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


        $req_data11=array();
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
                 
                        $STOCK_INHAND =  DB::SELECT("SELECT SUM(CURRENT_QTY) AS CURRENT_QTY
                        FROM TBL_MST_BATCH 
                        WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF' AND STATUS='A' AND STID_REF='$key' AND ITEMID_REF='$ITEMID_REF'
                        ")[0]->CURRENT_QTY;

                        $req_data11[$i][] = [
                            'STID_REF'          => $key,
                            'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                            'UOMID_REF'         => $request['MAIN_UOMID_REF_'.$i],
                            'STOCK_INHAND'      => $STOCK_INHAND !=''?$STOCK_INHAND:0,
                            'RECEIVED_QTY'      => $request['PD_OR_QTY_'.$i],
                            'LOT_NO'            => $request['OURLOT_NO_'.$i],
                            'VENDOR_BATCHNO'    => $request['VENDOR_BATCHNO_'.$i],
                            'JWCID_REF'         => $request['JWCID_REF_'.$i],
                            'JWOID_REF'         => $request['JWOID_REF_'.$i],
                            'PROID_REF'         => $request['PROID_REF_'.$i],
                            'SOID_REF'          => $request['SOID_REF_'.$i],
                            'SQID_REF'          => $request['SQID_REF_'.$i],
                            'SEID_REF'          => $request['SEID_REF_'.$i]   
                        ];

                    }
                }

            }
        }
        
		if($r_count1 > 0){
            $wrapped_links11["MULTISTORE"] = $req_data11; 
			$XMLSTORE = ArrayToXml::convert($wrapped_links11);
        }
        else{
            $XMLSTORE=NULL;
        }
        
       

        $VTID_REF     =   $this->vtid_ref;
        
        $USERID_REF = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        
        $GRNNO          = $request['GRNNO'];
        $GRNDT          = $request['GRNDT'];
        $STID_REF       = NULL;
        $GEJWOID_REF    = $request['GEJWOID_REF'];
        $VID_REF        = $request['VID_REF'];
        $REMARKS        = $request['REMARKS'];
        
        $log_data = [ 
            $GRNNO,             $GRNDT,         $STID_REF,      $GEJWOID_REF,   $VID_REF,       
            $REMARKS,           $CYID_REF,      $BRID_REF,      $FYID_REF,      $VTID_REF,          
            $XMLMAT,            $XMLREQ,        $XMLUDF,        $XMLSTORE,      $USERID_REF,    
            Date('Y-m-d'),      Date('h:i:s.u'),$ACTIONNAME,    $IPADDRESS
        ];

        
        $sp_result = DB::select('EXEC SP_GRJ_UP ?,?,?,?,? ,?,?,?,?,? ,?,?,?,?,? ,?,?,?,?', $log_data);    
        

        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
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
        $r_count3 = $request['Row_Count3'];
        $r_count5 = $request['Row_Count5'];
        
        $req_data=array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['ITEMID_REF_'.$i])){

                $StoreArr   =   array();
                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $ITEMROWID  =   $request['HiddenRowId_'.$i];
                $exp        =   explode(",",$ITEMROWID);

                foreach($exp as $val){
                    $keyid              =   explode("_",$val);
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
                    'JWCID_REF'         => $request['JWCID_REF_'.$i],
                    'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'         => $request['MAIN_UOMID_REF_'.$i],
                    'VENDOR_BATCHNO'    => $request['VENDOR_BATCHNO_'.$i],
                    'OURLOT_NO'         => $request['OURLOT_NO_'.$i],
                    'RECEIVED_QTY'      => $request['PD_OR_QTY_'.$i],
                    'SHORT_QTY'         => $request['BL_SOQTY_'.$i],
                    'REMARKS'           => $request['REMARKS_'.$i],
                    'JWOID_REF'         => $request['JWOID_REF_'.$i],
                    'PROID_REF'         => $request['PROID_REF_'.$i],
                    'SOID_REF'          => $request['SOID_REF_'.$i],
                    'SQID_REF'          => $request['SQID_REF_'.$i],
                    'SEID_REF'          => $request['SEID_REF_'.$i],
                    'STORE_NAME'        => $request['STORE_NAME_'.$i],
                    'STID_REF'    	    => $STID_REF,
                    'BATCH_QTY_REF'     => $request['HiddenRowId_'.$i],  
                    'RATE'              => $request['ITEMRATE_'.$i],
                    'JWRATE'            => $request['JWRATE_'.$i],
                    'JWC_QTY'            => $request['QTY_'.$i],  
                    
                ];

            }
        }

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);


        $req_data5=array();
        for ($i=0; $i<=$r_count5; $i++){
            if(isset($request['REQ_ITEMID_REF_'.$i])){

                $req_data5[$i] = [
                    'JWCID_REF'      	=> $request['REQ_JWCID_REF_'.$i],
                    'ITEMID_REF'    	=> $request['REQ_ITEMID_REF_'.$i],
                    'IN_JWO_QTY'    	=> $request['REQ_STD_BOM_QTY_'.$i],
                    'ACTUAL_CON_QTY'	=> $request['REQ_JWC_QTY_'.$i],
                    'CONSUMED_LOTNO'	=> $request['REQ_CONSUMED_LOTNO_'.$i],
                    'REMARKS'	        => $request['REQ_REMARKS_'.$i],
                    'MAIN_ITEMID_REF'   => $request['REQ_SOITEMID_REF_'.$i],
                    'JWOID_REF'      	=> $request['REQ_JWOID_REF_'.$i],
                    'PROID_REF'      	=> $request['REQ_PROID_REF_'.$i],
                    'SOID_REF'      	=> $request['REQ_SOID_REF_'.$i],
                    'SQID_REF'     		=> $request['REQ_SQID_REF_'.$i],  
                    'SEID_REF'     		=> $request['REQ_SEID_REF_'.$i],
                ];

            }
        }

       

		if($r_count5 > 0){
            $wrapped_links5["CON"] = $req_data5; 
			$XMLREQ = ArrayToXml::convert($wrapped_links5);
        }
        else{
            $XMLREQ=NULL;
        }
        

        $udffield_Data = [];      
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFGRJID_REF'] = $request['udffie_'.$i]; 
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


        $req_data11=array();
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
                 
                        $STOCK_INHAND =  DB::SELECT("SELECT SUM(CURRENT_QTY) AS CURRENT_QTY
                        FROM TBL_MST_BATCH 
                        WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF' AND STATUS='A' AND STID_REF='$key' AND ITEMID_REF='$ITEMID_REF'
                        ")[0]->CURRENT_QTY;

                        $req_data11[$i][] = [
                            'STID_REF'          => $key,
                            'ITEMID_REF'        => $request['ITEMID_REF_'.$i],
                            'UOMID_REF'         => $request['MAIN_UOMID_REF_'.$i],
                            'STOCK_INHAND'      => $STOCK_INHAND !=''?$STOCK_INHAND:0,
                            'RECEIVED_QTY'      => $request['PD_OR_QTY_'.$i],
                            'LOT_NO'            => $request['OURLOT_NO_'.$i],
                            'VENDOR_BATCHNO'    => $request['VENDOR_BATCHNO_'.$i],
                            'JWCID_REF'         => $request['JWCID_REF_'.$i],
                            'JWOID_REF'         => $request['JWOID_REF_'.$i],
                            'PROID_REF'         => $request['PROID_REF_'.$i],
                            'SOID_REF'          => $request['SOID_REF_'.$i],
                            'SQID_REF'          => $request['SQID_REF_'.$i],
                            'SEID_REF'          => $request['SEID_REF_'.$i]   
                        ];

                    }
                }

            }
        }

       
        
		if($r_count1 > 0){
            $wrapped_links11["MULTISTORE"] = $req_data11; 
			$XMLSTORE = ArrayToXml::convert($wrapped_links11);
        }
        else{
            $XMLSTORE=NULL;
        }
        
        

        $VTID_REF     =   $this->vtid_ref;
        
        $USERID_REF = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $GRNNO          = $request['GRNNO'];
        $GRNDT          = $request['GRNDT'];
        $STID_REF       = NULL;
        $GEJWOID_REF    = $request['GEJWOID_REF'];
        $VID_REF        = $request['VID_REF'];
        $REMARKS        = $request['REMARKS'];
        
        $log_data = [ 
            $GRNNO,             $GRNDT,         $STID_REF,      $GEJWOID_REF,   $VID_REF,       
            $REMARKS,           $CYID_REF,      $BRID_REF,      $FYID_REF,      $VTID_REF,          
            $XMLMAT,            $XMLREQ,        $XMLUDF,        $XMLSTORE,      $USERID_REF,    
            Date('Y-m-d'),      Date('h:i:s.u'),$ACTIONNAME,    $IPADDRESS
        ];

        
        $sp_result = DB::select('EXEC SP_GRJ_UP ?,?,?,?,? ,?,?,?,?,? ,?,?,?,?,? ,?,?,?,?', $log_data);  
        
        
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
                $TABLE      =   "TBL_TRN_GRJ_HDR";
                $FIELD      =   "GRJID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_GRJ ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
               

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
        $TABLE      =   "TBL_TRN_GRJ_HDR";
        $FIELD      =   "GRJID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_GRJ_MAT',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_GRJ_CON',
        ];
        $req_data[2]=[
            'NT'  => 'TBL_TRN_GRJ_UDF',
        ];
   
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_GRJ  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_GRJ_HDR')->where('GRJID','=',$id)->first();


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
        
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/JobWorkGrn";
		
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
        $GRNNO      =   $request->GRNNO;
        
        $objExit    =   DB::table('TBL_TRN_GRJ_HDR')
                        ->where('TBL_TRN_GRJ_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_TRN_GRJ_HDR.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_TRN_GRJ_HDR.FYID_REF','=',Session::get('FYID_REF'))
                        ->where('TBL_TRN_GRJ_HDR.GRNNO','=',$GRNNO)
                        ->select('TBL_TRN_GRJ_HDR.GRNNO')
                        ->first();
        
        if($objExit){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate Data']);
        
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
        $VID_REF    =   $request['VID_REF'];
        $JWCID_REF  =   $request['JWCID_REF'];
        $StdCost    =   0;
        $AlpsStatus =   $this->AlpsStatus();

        $ObjItem =  DB::select("SELECT 
        T1.UOMID_REF AS MAIN_UOMID_REF,T1.PENDING_QTY AS Qty,T1.JWCID_REF,T1.JWOID_REF,T1.PROID_REF,T1.SOID_REF,T1.SEID_REF,T1.SQID_REF,
        T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI,T3.RATE_PUOM
        FROM TBL_TRN_JWC_MAT T1
        LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
        LEFT JOIN TBL_TRN_JWO_MAT T3 ON T1.JWOID_REF = T3.JWOID_REF
        WHERE T1.JWCID_REF='$JWCID_REF'

        UNION

        SELECT 
        T1.UOMID_REF AS MAIN_UOMID_REF,T1.JWC_QTY AS Qty,T1.JWCID_REF,T1.JWOID_REF,T1.PROID_REF,T1.SOID_REF,T1.SEID_REF,T1.SQID_REF,
        T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI,T3.RATE_PUOM
        FROM TBL_TRN_JWC_DIS T1
        LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
        LEFT JOIN TBL_TRN_JWO_MAT T3 ON T1.JWOID_REF = T3.JWOID_REF
        WHERE T1.JWCID_REF='$JWCID_REF'
        ");

        if(!empty($ObjItem)){

            foreach ($ObjItem as $index=>$dataRow){

                $SOQTY      =   isset($dataRow->Qty)? $dataRow->Qty : 0;   
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

                $item_unique_row_id =   $JWCID_REF."_".$dataRow->JWOID_REF."_".$dataRow->PROID_REF."_".$dataRow->SOID_REF."_".$dataRow->SQID_REF."_".$dataRow->SEID_REF."_".$dataRow->ITEMID;
               
               
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
                            data-desc5="'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'" 
                            data-desc6="'.$FROMQTY.'" 
                            data-desc7="'.$item_unique_row_id.'" 
                            data-desc8="'.$dataRow->SQID_REF.'"
                            data-desc9="'.$dataRow->SEID_REF.'"
                            data-desc10="'.$dataRow->JWOID_REF.'"
                            data-desc11="'.$dataRow->SOID_REF.'"
                            data-desc12="'.$SOQTY.'"
                            data-desc13="'.$dataRow->PROID_REF.'"
                            data-desc14="'.$FROMQTY.'"
                            data-desc15="'.$dataRow->JWCID_REF.'"
                            data-desc16="'.$dataRow->RATE_PUOM.'"
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
        $VTID_REF       =   $request['id'];
        $fieldid        =   $request['fieldid'];

        $ObjData        =   DB::select("SELECT JWCID AS DOC_ID,JWCNO AS DOC_NO,JWCDT AS DOC_DESC
                            FROM TBL_TRN_JWC_HDR 
                            WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND FYID_REF='$FYID_REF' 
                            AND VID_REF='$VTID_REF' AND STATUS='A'"); 

        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){

                $row            =   '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="socode_'.$dataRow->DOC_ID .'"  class="clssJWOID" value="'.$dataRow->DOC_ID.'" ></td>
                <td class="ROW2">'.$dataRow->DOC_NO;
                $row = $row.'<input type="hidden" id="txtsocode_'.$dataRow->DOC_ID.'" data-desc="'.$dataRow->DOC_NO.'"  value="'.$dataRow->DOC_ID.'"/></td>
                <td class="ROW3" >'.$dataRow->DOC_DESC.'</td></tr>';
                echo $row;
                
            }

        }else{
            echo '<tr><td>Record not found.</td></tr>';
        }
        exit();   
    }
    
    public function get_materital_item(Request $request){
        
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $item_array     =   $request['item_array'];
      //  dd($request['item_array']);
        $material_array=array();
        foreach($item_array as $key=>$val){

            $exp        =   explode("_",$val);
            $JWOID      =   $exp[0];
            $SOID       =   $exp[1];
            $ITEMID     =   $exp[2];
            $ITEMCODE   =   $exp[3];
            $JWC_QTY    =   $exp[4];
            $SQID       =   $exp[5];
            $SEID       =   $exp[6];
            $PROID      =   $exp[7];
            $JWCID      =   $exp[8];

            $mitem_id   =   $JWCID."_".$JWOID."_".$PROID."_".$SOID."_".$SQID."_".$SEID."_".$ITEMID;

            $WHERE_SQID_REF= $SQID !=""?" AND SQID_REF='$SQID' ":"";
            $WHERE_SEID_REF= $SEID !=""?" AND SEID_REF='$SEID' ":"";
            $WHERE_SOID_REF= $SOID !=""?" AND SOID_REF='$SOID' ":"";
            $WHERE_PROID_REF= $PROID !=""?" AND PROID_REF='$PROID' ":"";

            $JWO_REQ    =   DB::select("SELECT T1.*,
            T2.ICODE,T2.NAME,T2.MAIN_UOMID_REF,CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOMCODE,T1.JWC_QTY
            FROM TBL_TRN_JWC_DIS T1 
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T2.MAIN_UOMID_REF=T3.UOMID
            WHERE T1.JWCID_REF='$JWCID' AND T1.JWOID_REF='$JWOID' AND MAIN_ITEMID_REF='$ITEMID' $WHERE_PROID_REF 
            $WHERE_SOID_REF $WHERE_SQID_REF $WHERE_SEID_REF");

            

            $material_array  = array();
            if(isset($JWO_REQ) && !empty($JWO_REQ)){
                foreach($JWO_REQ as $row){

                    $ITEMID_REF =   $row->ITEMID_REF;

                    $STD_BOM_QTY    =   DB::select("SELECT TOP 1 STD_BOM_QTY 
                    FROM TBL_TRN_JWO_REQ 
                    WHERE JWOID_REF='$JWOID' AND ITEMID_REF='$ITEMID_REF' AND MAIN_ITEMID_REF='$ITEMID' 
                    $WHERE_PROID_REF $WHERE_SOID_REF $WHERE_SQID_REF $WHERE_SEID_REF");

                    $material_array[]=array(
                        'JWO_REQID'=>$row->JWC_DISID,
                        'ITEMID_REF'=>$row->ITEMID_REF,
                        'ICODE'=>$row->ICODE,
                        'NAME'=>$row->NAME,
                        'MAIN_UOMCODE'=>$row->MAIN_UOMCODE,
                        'MAIN_UOMID_REF'=>$row->MAIN_UOMID_REF,
                        'MAIN_JWCID'=>$JWCID,
                        'MAIN_JWOID'=>$JWOID,
                        'MAIN_PROID'=>$PROID,
                        'MAIN_SOID'=>$SOID,
                        'MAIN_ITEMID'=>$ITEMID,
                        'STD_BOM_QTY'=>isset($STD_BOM_QTY)? $STD_BOM_QTY[0]->STD_BOM_QTY:1,
                        'MAIN_SQID'=>$SQID,
                        'MAIN_SEID'=>$SEID,
                        'MAIN_ITEM_ROWID'=>$mitem_id ,
                        'JWC_QTY'=>$row->JWC_QTY,
                    );
                }
            }
            
        }
        
        if(!empty($material_array)){
            $Row_Count5 =   count($material_array);
            echo'<table id="example8" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                        <tr>
                            <th hidden ><input class="form-control" type="hidden" name="Row_Count5" id ="Row_Count5" value="'.$Row_Count5.'"></th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Input Item as per Job Work Order Qty</th>
                            <th>Actual consumption</th>
                            <th>Consumed Lot Nos</th>
                            <th>Remarks</th>
                           
                        </tr>
                    </thead>
                    <tbody>';

                    foreach($material_array as $index=>$row_data){

                        $sta_qty     = number_format(round(($row_data['STD_BOM_QTY']), 3),3,".","")  ;
                        $jwc_qty     = number_format(round(($row_data['JWC_QTY']), 3),3,".","")  ;

                        echo '<tr  class="participantRow8">';

                        echo '<td><input type="text" id="txtSUBITEM_popup_'.$index.'" value="'.$row_data['ICODE'].'" class="form-control" readonly /></td>';
                        echo '<td><input type="text" id="SUBITEM_NAME_'.$index.'"     value="'.$row_data['NAME'].'"  class="form-control" readonly /></td>';
                        
                        echo '<td><input type="text" name="REQ_STD_BOM_QTY_'.$index.'" id="REQ_STD_BOM_QTY_'.$index.'" value="'.$sta_qty.'" readonly class="form-control"  /></td>';
                        echo '<td><input type="text" name="REQ_JWC_QTY_'.$index.'" id="REQ_JWC_QTY_'.$index.'"     value="'.$jwc_qty.'" readonly     class="form-control three-digits"  readonly onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" /></td>';
                        
                        echo '<td><input type="text" name="REQ_CONSUMED_LOTNO_'.$index.'" id="REQ_CONSUMED_LOTNO_'.$index.'"  class="form-control" autocomplete="off"  /></td>';
                        echo '<td><input type="text" name="REQ_REMARKS_'.$index.'" id="REQ_REMARKS_'.$index.'"  class="form-control" autocomplete="off" /></td>';
                       
                        echo '<td hidden><input type="hidden" name="REQ_JWO_REQID_'.$index.'"    id="REQ_JWO_REQID_'.$index.'"    value="'.$row_data['JWO_REQID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_JWCID_REF_'.$index.'"    id="REQ_JWCID_REF_'.$index.'"    value="'.$row_data['MAIN_JWCID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_JWOID_REF_'.$index.'"    id="REQ_JWOID_REF_'.$index.'"    value="'.$row_data['MAIN_JWOID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_PROID_REF_'.$index.'"    id="REQ_PROID_REF_'.$index.'"    value="'.$row_data['MAIN_PROID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SOID_REF_'.$index.'"     id="REQ_SOID_REF_'.$index.'"     value="'.$row_data['MAIN_SOID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SOITEMID_REF_'.$index.'" id="REQ_SOITEMID_REF_'.$index.'" value="'.$row_data['MAIN_ITEMID'].'" /></td>';
                        echo '<td hidden><input type="text"   name="REQ_ITEMID_REF_'.$index.'"   id="REQ_ITEMID_REF_'.$index.'"   value="'.$row_data['ITEMID_REF'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SQID_REF_'.$index.'"     id="REQ_SQID_REF_'.$index.'"     value="'.$row_data['MAIN_SQID'].'" /></td>';
                        echo '<td hidden><input type="hidden" name="REQ_SEID_REF_'.$index.'"     id="REQ_SEID_REF_'.$index.'"     value="'.$row_data['MAIN_SEID'].'" /></td>';
                  
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

    public function getStoreDetails(Request $request){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $ITEMID_REF     =   $request['ITEMID_REF'];
        $CHALLAN_QTY     =   $request['CHALLAN_QTY'];
        $UOMID_REF      =   $request['UOMID_REF'];
        $ROW_ID         =   $request['ROW_ID'];
        $ITEMROWID      =   $request['ITEMROWID'];
        $ACTION_TYPE    =   $request['ACTION_TYPE'] =="VIEW"?'disabled':'';
        $SRNOA          =   NULL;
        $BATCHNOA       =   NULL;



        

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

        $objBatch =  DB::SELECT("SELECT STID,STCODE,NAME AS STNAME ,0 AS BATCHID,
        (SELECT SUM(CURRENT_QTY)  FROM TBL_MST_BATCH WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' 
        AND STATUS='A' AND STID_REF=STID AND ITEMID_REF='$ITEMID_REF') AS CURRENT_QTY
        FROM TBL_MST_STORE 
        WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF'  AND STATUS='A' AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 )
        ");

        echo '<thead>';
        echo '<tr>';
        echo '<th>Store</th>';
        echo '<th>Stock-in-hand</th>';
        echo '<th>Received Qty</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach($objBatch as $key=>$val){

            
            $qtyvalue   =   array_key_exists($val->STID, $dataArr)?$dataArr[$val->STID]:'';

            if($request['ACTION_TYPE'] =="ADD"){
                $CURRENT_QTY=$val->CURRENT_QTY !=""?$val->CURRENT_QTY:'0.000';
            }
            else{
                $CURRENT_QTY=$val->CURRENT_QTY !=""?(floatval($val->CURRENT_QTY)+floatval($qtyvalue)):'0.000';
            }
            
            echo '<tr  class="participantRow33">';
            echo '<td>'.$val->STCODE.' - '.$val->STNAME.'</td>';
            echo '<td>'.$CURRENT_QTY.'</td>';
            echo '<td><input '.$ACTION_TYPE.' type="text" name="UserQty_'.$key.'" id="UserQty_'.$key.'" value="'.$qtyvalue.'" class="form-control qtytext" onkeyup="checkStoreQty('.$ROW_ID.','.$CURRENT_QTY.',this.value,'.$key.','.$CHALLAN_QTY.')" onkeypress="return isNumberDecimalKey(event,this)" autocomplete="off"  ></td>';
            echo '<td hidden><input type="hidden" name="BATCHID_'.$key.'" id="BATCHID_'.$key.'" value="'.$val->STID.'" class="qtytext" ></td>';
            echo '<td hidden><input type="hidden" name="STORENAME_'.$key.'" id="STORENAME_'.$key.'" value="'.$val->STNAME.'" ></td>';
            echo '</tr>';
        }

        echo '</tbody>';
        exit();
    }

    /*
    public function getStoreDetails(Request $request){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');

        $ITEMID_REF     =   $request['ITEMID_REF'];
        $UOMID_REF      =   $request['UOMID_REF'];
        $ROW_ID         =   $request['ROW_ID'];
        $ITEMROWID      =   $request['ITEMROWID'];
        $ACTION_TYPE    =   $request['ACTION_TYPE'] =="VIEW"?'disabled':'';
        $SRNOA          =   NULL;
        $BATCHNOA       =   NULL;

        

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

        $objBatch =  DB::SELECT("
        SELECT T1.BATCHID,T1.BATCH_CODE,T1.ITEMID_REF,T1.STID_REF,T1.SERIALNO,T1.UOMID_REF,
        T1.CURRENT_QTY,T2.STCODE,T2.NAME AS STNAME,T3.UOMCODE,T3.DESCRIPTIONS AS UOMDESCRIPTIONS 
        FROM TBL_MST_BATCH T1
        LEFT JOIN TBL_MST_STORE T2 ON T1.STID_REF=T2.STID
        LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
        WHERE T1.STATUS='A' AND T1.ITEMID_REF='$ITEMID_REF' AND T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' 
        AND T1.FYID_REF='$FYID_REF' AND T1.UOMID_REF='$UOMID_REF'
        ");
        
     
        echo '<thead>';
        echo '<tr>';
        echo '<th>Store</th>';
        echo '<th>Main UoM</th>';
        echo '<th>Stock-in-hand</th>';
        echo '<th>Dispatch Qty</th>';
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
            echo '<td><input '.$ACTION_TYPE.' type="text" name="UserQty_'.$key.'" id="UserQty_'.$key.'" value="'.$qtyvalue.'" class="form-control qtytext" onkeyup="checkStoreQty('.$ROW_ID.','.$CURRENT_QTY.',this.value,'.$key.')" onkeypress="return isNumberDecimalKey(event,this)" autocomplete="off"  ></td>';
            echo '<td hidden><input type="hidden" name="BATCHID_'.$key.'" id="BATCHID_'.$key.'" value="'.$val->BATCHID.'" class="qtytext" ></td>';
            echo '<td hidden><input type="hidden" name="STORENAME_'.$key.'" id="STORENAME_'.$key.'" value="'.$val->STNAME.'" ></td>';
            echo '</tr>';
        }

        echo '</tbody>';
        exit();
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


    public function checkCompany($str){
        $COMPANY_NAME   =   DB::table('TBL_MST_COMPANY')->where('STATUS','=','A')->where('CYID','=',Auth::user()->CYID_REF)->select('TBL_MST_COMPANY.NAME')->first()->NAME;
       //$COMPANY_NAME   =  "ZEP";
        $result = strpos(strtolower($COMPANY_NAME),$str)!== false?'1':'';
        return $result;
    }

    
}
