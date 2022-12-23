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

class TrnFrm356Controller extends Controller{

    protected $form_id  = 356;
    protected $vtid_ref = 442;
    protected $view     = "transactions.JobWork.JobWorkGateEntry.trnfrm";   

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
        WHERE  AUD.VID=T1.GEJWOID AND  AUD.CYID_REF=T1.CYID_REF AND  AUD.BRID_REF=T1.BRID_REF AND  
        AUD.FYID_REF=T1.FYID_REF AND  AUD.VTID_REF=T1.VTID_REF AND AUD.ACTIONNAME='ADD'       
        ) AS CREATED_BY
        FROM TBL_TRN_GEJWO_HDR T1
        INNER JOIN TBL_TRN_AUDITTRAIL T2 ON T1.GEJWOID=T2.VID AND T1.VTID_REF=T2.VTID_REF AND T1.CYID_REF=T2.CYID_REF AND T1.BRID_REF=T2.BRID_REF 
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' AND T2.VTID_REF = '$this->vtid_ref' AND T2.ACTID IN (SELECT max(ACTID) FROM TBL_TRN_AUDITTRAIL A WHERE T2.VTID_REF = A.VTID_REF AND T2.VID = A.VID)
        ORDER BY T1.GEJWOID DESC 
        ");

        return view($this->view.$FormId,compact(['REQUEST_DATA','objRights','objDataList','FormId','DATA_STATUS']));
    }

    public function add(){       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
       
        $objTransporterList =   $this->getTransporterList();
        $objPackingList     =   $this->getPackingList();
        
        $objlastdt          =   $this->getLastdt();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_GEJWO_HDR',
            'HDR_ID'=>'GEJWOID',
            'HDR_DOC_NO'=>'GENO',
            'HDR_DOC_DT'=>'GEDT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

       

        $table       =  "TBL_MST_UDFFOR_GEJ";
        $ObjUnionUDF =  DB::table($table)->select('*')
        ->whereIn('PARENTID',function($query) use ($CYID_REF){       
            $query->select("UDFGEJID")->from('TBL_MST_UDFFOR_GEJ')
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

        $FormId =   $this->form_id;
        return view($this->view.$FormId.'add',compact([
                'FormId',
                'objTransporterList',
                'objPackingList',
                'objUdfData',
               
                'objCountUDF',
              
                'objlastdt',
                'doc_req','docarray'   
        ]));       
    }

    public function save(Request $request) {

        $r_count2 = $request['Row_Count2'];
            
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['UDF_'.$i])){
                $reqdata3[$i] = [
                    'UDFGEJIID_REF'      => $request['UDF_'.$i],
                    'VALUE'  => $request['udfvalue_'.$i],
                ];
            }
        }


        if(isset($reqdata3)){ 
            $wrapped_links3["UDF"] = $reqdata3; 
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

        $GE_NO          = $request['GE_NO'];
        $GE_DT          = $request['GE_DT'];
        $GE_TM          = $request['GE_TM'];
        $SECURITY_SUP   = $request['SECURITY_SUP'];
        $SECURITY_GUARD = $request['SECURITY_GUARD'];
        $VID_REF        = $request['VID_REF'];
        $JWO_NO          = $request['JWO_NO'];
        $VENDOR_BILLNO  = $request['VENDOR_BILLNO'];
        $VENDOR_BILLDT  = $request['VENDOR_BILLDT'];
        $VENDOR_CHNO    = $request['VENDOR_CHNO'];
        $VENDOR_CHDT    = $request['VENDOR_CHDT'];
        $GATE_NO        = $request['GATE_NO'];
        $GATE_RGNO      = $request['GATE_RGNO'];
        $DATE           = $request['DATE'];
        $REMARKS        = $request['REMARKS'];
        $LR_NO          = $request['LR_NO'];
        $LR_DT          = $request['LR_DT'];
        $TRANSPORT_MODE = $request['TRANSPORT_MODE'];
        $VEHICLE_NO     = $request['VEHICLE_NO'];
        $VEHICLE_CAT    = $request['VEHICLE_CAT'];
        $TRANSPORTER    = $request['TRANSPORTER'];
        $DRIVER_NAME    = $request['DRIVER_NAME'];
        $WEIGHMENT_MACHINE = $request['WEIGHMENT_MACHINE'];
        $WEIGHMENT_SLIP = $request['WEIGHMENT_SLIP'];
        $GROSS_WEIGHT   = $request['GROSS_WEIGHT'];
        $TARE_WEIGHT    = $request['TARE_WEIGHT'];
        $NET_WEIGHT     = $request['NET_WEIGHT'];
        $UNL_BAYNO      = $request['UNL_BAYNO'];
        $ST_CUSTODIAN   = $request['ST_CUSTODIAN'];
        $UNL_METHOD     = $request['UNL_METHOD'];
        $PTID_REF       = $request['PTID_REF'];
        $PACKING_NO     = $request['PACKING_NO'];
        $PACKING_CONDITION = $request['PACKING_CONDITION'];
        
        $log_data = [ 
            $GE_NO,$GE_DT,$GE_TM,$SECURITY_SUP,$SECURITY_GUARD,
            $VID_REF,$JWO_NO,$VENDOR_BILLNO,$VENDOR_BILLDT,$VENDOR_CHNO,
            $VENDOR_CHDT,$GATE_NO,$GATE_RGNO,$DATE,$REMARKS,
            $LR_NO,$LR_DT,$TRANSPORT_MODE,$VEHICLE_NO,$VEHICLE_CAT,
            $TRANSPORTER,$DRIVER_NAME,$WEIGHMENT_MACHINE,$WEIGHMENT_SLIP,$GROSS_WEIGHT,
            $TARE_WEIGHT,$NET_WEIGHT,$UNL_BAYNO,$ST_CUSTODIAN,$UNL_METHOD,
            $PTID_REF,$PACKING_NO,$PACKING_CONDITION,$CYID_REF,$BRID_REF,
            $FYID_REF,$VTID_REF,$XMLUDF,$USERID, Date('Y-m-d'),
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];


        $sp_result = DB::select('EXEC SP_GEJ_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  

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

            $objResponse =  DB::table('TBL_TRN_GEJWO_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('GEJWOID','=',$id)
            ->first();

            $objTransporterList =   $this->getTransporterList();
            $objPackingList     =   $this->getPackingList();
            $objlastdt          =   $this->getLastdt();

            $objVendorName      =   $this->getVendorName($objResponse->VID_REF);
            $objTransporterName =   $this->getTransporterName($objResponse->TRANSPORTER);
            $objPackingName     =   $this->getPackingName($objResponse->PTID_REF);
            
            $objJwoName         =   $this->getJwoNoName('JWO',$objResponse->JWOID_REF);

            $objUDF = DB::table('TBL_TRN_GEJWO_UDF')                    
            ->where('GEJWOID_REF','=',$id)
            ->orderBy('GEJOW_UDFID','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF); 

            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_GEJ")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('UDFGEJID')->from('TBL_MST_UDFFOR_GEJ')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                         
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                        
                    

            $objUdfData = DB::table('TBL_MST_UDFFOR_GEJ')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
          
            ->union($ObjUnionUDF)
            ->get()->toArray();   
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_GEJ")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDFGEJID')->from('TBL_MST_UDFFOR_GEJ')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                         
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                         
            $objUdfData2 = DB::table('TBL_MST_UDFFOR_GEJ')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF2)
            ->get()->toArray(); 

            $FormId         =   $this->form_id;


            return view($this->view.$FormId.'edit',compact([
                'FormId',
                'objRights',
                'objResponse', 
                'objTransporterList',
                'objPackingList',
                'objVendorName',
                'objTransporterName',
                'objPackingName',
                'objJwoName',
                'objCount2',
                'objUDF',
                'objUdfData',
                'objCountUDF',
                'objUdfData2',
                'objlastdt'
        ]));      


        }
     
    }

    public function update(Request $request){
        
        $r_count2 = $request['Row_Count2'];
            
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['UDF_'.$i])){
                $reqdata3[$i] = [
                    'UDFGEJIID_REF'      => $request['UDF_'.$i],
                    'VALUE'  => $request['udfvalue_'.$i],
                ];
            }
        }


        if(isset($reqdata3)){ 
            $wrapped_links3["UDF"] = $reqdata3; 
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

        $GE_NO          = $request['GE_NO'];
        $GE_DT          = $request['GE_DT'];
        $GE_TM          = $request['GE_TM'];
        $SECURITY_SUP   = $request['SECURITY_SUP'];
        $SECURITY_GUARD = $request['SECURITY_GUARD'];
        $VID_REF        = $request['VID_REF'];
        $JWO_NO          = $request['JWO_NO'];
        $VENDOR_BILLNO  = $request['VENDOR_BILLNO'];
        $VENDOR_BILLDT  = $request['VENDOR_BILLDT'];
        $VENDOR_CHNO    = $request['VENDOR_CHNO'];
        $VENDOR_CHDT    = $request['VENDOR_CHDT'];
        $GATE_NO        = $request['GATE_NO'];
        $GATE_RGNO      = $request['GATE_RGNO'];
        $DATE           = $request['DATE'];
        $REMARKS        = $request['REMARKS'];
        $LR_NO          = $request['LR_NO'];
        $LR_DT          = $request['LR_DT'];
        $TRANSPORT_MODE = $request['TRANSPORT_MODE'];
        $VEHICLE_NO     = $request['VEHICLE_NO'];
        $VEHICLE_CAT    = $request['VEHICLE_CAT'];
        $TRANSPORTER    = $request['TRANSPORTER'];
        $DRIVER_NAME    = $request['DRIVER_NAME'];
        $WEIGHMENT_MACHINE = $request['WEIGHMENT_MACHINE'];
        $WEIGHMENT_SLIP = $request['WEIGHMENT_SLIP'];
        $GROSS_WEIGHT   = $request['GROSS_WEIGHT'];
        $TARE_WEIGHT    = $request['TARE_WEIGHT'];
        $NET_WEIGHT     = $request['NET_WEIGHT'];
        $UNL_BAYNO      = $request['UNL_BAYNO'];
        $ST_CUSTODIAN   = $request['ST_CUSTODIAN'];
        $UNL_METHOD     = $request['UNL_METHOD'];
        $PTID_REF       = $request['PTID_REF'];
        $PACKING_NO     = $request['PACKING_NO'];
        $PACKING_CONDITION = $request['PACKING_CONDITION'];
        
        $log_data = [ 
            $GE_NO,$GE_DT,$GE_TM,$SECURITY_SUP,$SECURITY_GUARD,
            $VID_REF,$JWO_NO,$VENDOR_BILLNO,$VENDOR_BILLDT,$VENDOR_CHNO,
            $VENDOR_CHDT,$GATE_NO,$GATE_RGNO,$DATE,$REMARKS,
            $LR_NO,$LR_DT,$TRANSPORT_MODE,$VEHICLE_NO,$VEHICLE_CAT,
            $TRANSPORTER,$DRIVER_NAME,$WEIGHMENT_MACHINE,$WEIGHMENT_SLIP,$GROSS_WEIGHT,
            $TARE_WEIGHT,$NET_WEIGHT,$UNL_BAYNO,$ST_CUSTODIAN,$UNL_METHOD,
            $PTID_REF,$PACKING_NO,$PACKING_CONDITION,$CYID_REF,$BRID_REF,
            $FYID_REF,$VTID_REF,$XMLUDF,$USERID, Date('Y-m-d'),
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_GEJ_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  
            
           
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

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

            $objResponse =  DB::table('TBL_TRN_GEJWO_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('GEJWOID','=',$id)
            ->first();

            $objTransporterList =   $this->getTransporterList();
            $objPackingList     =   $this->getPackingList();
            $objlastdt          =   $this->getLastdt();

            $objVendorName      =   $this->getVendorName($objResponse->VID_REF);
            $objTransporterName =   $this->getTransporterName($objResponse->TRANSPORTER);
            $objPackingName     =   $this->getPackingName($objResponse->PTID_REF);
            
            $objJwoName         =   $this->getJwoNoName('JWO',$objResponse->JWOID_REF);

            $objUDF = DB::table('TBL_TRN_GEJWO_UDF')                    
            ->where('GEJWOID_REF','=',$id)
            ->orderBy('GEJOW_UDFID','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF); 

            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_GEJ")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('UDFGEJID')->from('TBL_MST_UDFFOR_GEJ')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                         
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                        
                    

            $objUdfData = DB::table('TBL_MST_UDFFOR_GEJ')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
          
            ->union($ObjUnionUDF)
            ->get()->toArray();   
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_GEJ")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDFGEJID')->from('TBL_MST_UDFFOR_GEJ')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                         
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                         
            $objUdfData2 = DB::table('TBL_MST_UDFFOR_GEJ')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF2)
            ->get()->toArray(); 

            $FormId         =   $this->form_id;


            return view($this->view.$FormId.'view',compact([
                'FormId',
                'objRights',
                'objResponse', 
                'objTransporterList',
                'objPackingList',
                'objVendorName',
                'objTransporterName',
                'objPackingName',
                'objJwoName',
                'objCount2',
                'objUDF',
                'objUdfData',
                'objCountUDF',
                'objUdfData2',
                'objlastdt'
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
   
        $r_count2 = $request['Row_Count2'];
            
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['UDF_'.$i])){
                $reqdata3[$i] = [
                    'UDFGEJIID_REF'      => $request['UDF_'.$i],
                    'VALUE'  => $request['udfvalue_'.$i],
                ];
            }
        }


        if(isset($reqdata3)){ 
            $wrapped_links3["UDF"] = $reqdata3; 
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

        $GE_NO          = $request['GE_NO'];
        $GE_DT          = $request['GE_DT'];
        $GE_TM          = $request['GE_TM'];
        $SECURITY_SUP   = $request['SECURITY_SUP'];
        $SECURITY_GUARD = $request['SECURITY_GUARD'];
        $VID_REF        = $request['VID_REF'];
        $JWO_NO          = $request['JWO_NO'];
        $VENDOR_BILLNO  = $request['VENDOR_BILLNO'];
        $VENDOR_BILLDT  = $request['VENDOR_BILLDT'];
        $VENDOR_CHNO    = $request['VENDOR_CHNO'];
        $VENDOR_CHDT    = $request['VENDOR_CHDT'];
        $GATE_NO        = $request['GATE_NO'];
        $GATE_RGNO      = $request['GATE_RGNO'];
        $DATE           = $request['DATE'];
        $REMARKS        = $request['REMARKS'];
        $LR_NO          = $request['LR_NO'];
        $LR_DT          = $request['LR_DT'];
        $TRANSPORT_MODE = $request['TRANSPORT_MODE'];
        $VEHICLE_NO     = $request['VEHICLE_NO'];
        $VEHICLE_CAT    = $request['VEHICLE_CAT'];
        $TRANSPORTER    = $request['TRANSPORTER'];
        $DRIVER_NAME    = $request['DRIVER_NAME'];
        $WEIGHMENT_MACHINE = $request['WEIGHMENT_MACHINE'];
        $WEIGHMENT_SLIP = $request['WEIGHMENT_SLIP'];
        $GROSS_WEIGHT   = $request['GROSS_WEIGHT'];
        $TARE_WEIGHT    = $request['TARE_WEIGHT'];
        $NET_WEIGHT     = $request['NET_WEIGHT'];
        $UNL_BAYNO      = $request['UNL_BAYNO'];
        $ST_CUSTODIAN   = $request['ST_CUSTODIAN'];
        $UNL_METHOD     = $request['UNL_METHOD'];
        $PTID_REF       = $request['PTID_REF'];
        $PACKING_NO     = $request['PACKING_NO'];
        $PACKING_CONDITION = $request['PACKING_CONDITION'];
        
        $log_data = [ 
            $GE_NO,$GE_DT,$GE_TM,$SECURITY_SUP,$SECURITY_GUARD,
            $VID_REF,$JWO_NO,$VENDOR_BILLNO,$VENDOR_BILLDT,$VENDOR_CHNO,
            $VENDOR_CHDT,$GATE_NO,$GATE_RGNO,$DATE,$REMARKS,
            $LR_NO,$LR_DT,$TRANSPORT_MODE,$VEHICLE_NO,$VEHICLE_CAT,
            $TRANSPORTER,$DRIVER_NAME,$WEIGHMENT_MACHINE,$WEIGHMENT_SLIP,$GROSS_WEIGHT,
            $TARE_WEIGHT,$NET_WEIGHT,$UNL_BAYNO,$ST_CUSTODIAN,$UNL_METHOD,
            $PTID_REF,$PACKING_NO,$PACKING_CONDITION,$CYID_REF,$BRID_REF,
            $FYID_REF,$VTID_REF,$XMLUDF,$USERID, Date('Y-m-d'),
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_GEJ_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?', $log_data);  
            
            
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
        $TABLE      =   "TBL_TRN_GEJWO_HDR";
        $FIELD      =   "GEJWOID";
        $ACTIONNAME     = $Approvallevel;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
            
        $log_data = [ 
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_GEJ ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
        
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
        $TABLE      =   "TBL_TRN_GEJWO_HDR";
        $FIELD      =   "GEJWOID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
         'NT'  => 'TBL_TRN_GEJWO_UDF',
        ];
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_GEJ  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_GEJWO_HDR')->where('GEJWOID','=',$id)->first();

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
        
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/JobWorkGateEntry";
		
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


    public function codeduplicate(Request $request){

        $GE_NO  =   trim($request['GE_NO']);
        $objLabel = DB::table('TBL_TRN_GEJWO_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('GENO','=',$GE_NO)
        ->select('GEJWOID')->first();

        if($objLabel){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
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




    public function getVendorName($id){

        return DB::table('TBL_MST_SUBLEDGER')
        ->where('BELONGS_TO','=','Vendor')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('SGLID','=',$id)    
        ->select('SGLID AS VID','SGLCODE AS VCODE','SLNAME AS NAME')
        ->first();


    }

    public function getTransporterList(){
        return  DB::table('TBL_MST_TRANSPORTER')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('TRANSPORTERID','TRANSPORTER_CODE','TRANSPORTER_NAME')
        ->get();
    }

    public function getTransporterName($id){
        return  DB::table('TBL_MST_TRANSPORTER')
            ->where('TRANSPORTERID','=',$id)
            ->select('TRANSPORTERID','TRANSPORTER_CODE','TRANSPORTER_NAME')
            ->first();
    }

    public function getPackingList(){
        return  DB::table('TBL_MST_PACKAGINGTYPE')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('PTID','PTCODE','PTNAME')
        ->get();
    }

    public function getPackingName($id){
        return  DB::table('TBL_MST_PACKAGINGTYPE')
            ->where('PTID','=',$id)
            ->select('PTID','PTCODE','PTNAME')
            ->first();
    }

    public function getJwoNoName($Type,$idd){

       $id  =   trim($idd);


        if($Type ==="JWO"){
            
            $dataArr=array();
            $objDataList = DB::select("select JWONO from TBL_TRN_JWO_HDR where JWOID in($id)");
            foreach($objDataList as $val){
                $dataArr[]=$val->JWONO;
            } 

        }

        $Result =implode(",",$dataArr);
        return $Result;
    }

    public function getJwoNo(Request $request){

        $Type           =   $request['Type'];
        $VID_REF        =   $request['VID_REF'];
        $value          =   $request['value'] !=""?$request['value']:'';
       
        if($Type ==="JWO"){
            $objDataList = DB::table('TBL_TRN_JWO_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('FYID_REF','=',Session::get('FYID_REF'))
            ->where('STATUS','=','A')
            ->where('VID_REF','=',$VID_REF)
            ->select('JWOID As DOC_ID','JWONO AS DOC_NO','JWODT AS DOC_DATE')
            ->get()
            ->toArray();  
        }

        if(!empty($objDataList)){
            foreach ($objDataList as $key=>$val){
                if($request['value'] !=""){
                    $checked=   in_array($val-> DOC_ID,explode(",",$request['value']))?"checked":'';
                }
                else{
                    $checked="";
                }
            ?>
            <tr id="JWO_NO_TDID_<?php echo $key;?>" class="JWO_NO_Row">
                <td class="ROW1" ><input <?php echo $checked;?> type="checkbox" class="JWO_NO_CHECK" id="txtJWO_NO_CHECK_<?php echo $key;?>" value="<?php echo $val-> DOC_ID;?>"></td>
                <td class="ROW2" ><?php echo $val-> DOC_NO;?>
                <input type="hidden" id="txtJWO_NO_TDID_<?php echo $key;?>" data-desc="<?php echo $val-> DOC_NO;?>"  value="<?php echo $val-> DOC_ID;?>"/>
                </td>
                <td class="ROW3"><?php echo $val-> DOC_DATE;?></td>
            </tr>
            <?php
            }
        }
        else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
        }
        exit();
    }

    public function getLastdt(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        return  DB::select('SELECT MAX(GEDT) GEDT FROM TBL_TRN_GEJWO_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
    }

    
}
