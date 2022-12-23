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

class TrnFrm92Controller extends Controller{

    protected $form_id  = 92;
    protected $vtid_ref = 92;
    protected $view     = "transactions.inventory.GateEntry.trnfrm";   

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

        $objDataList	=	DB::select("select hdr.GEID,hdr.GE_NO,hdr.GE_DT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.GEID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                            ) AS CREATED_BY,
                            hdr.STATUS, sl.SLNAME,hdr.BOE_NO,hdr.GETYPE,hdr.PO_NO,VENDOR_BILLNO,sl.SLNAME,sl.SLNAME AS VENDOR_NAME,cu.SLNAME AS CUSTOMER_NAME,EMP.FNAME AS EMPLOYEE_NAME,hdr.TYPE,
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
                            inner join TBL_TRN_IMGE01_HDR hdr
                            on a.VID = hdr.GEID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            left join TBL_MST_SUBLEDGER sl ON hdr.VID_REF = sl.SGLID AND sl.BELONGS_TO='Vendor' AND hdr.TYPE='Vendor'   
                            left join TBL_MST_SUBLEDGER cu ON hdr.VID_REF = cu.SGLID AND cu.BELONGS_TO='Customer' AND hdr.TYPE='Customer'  
                            left join TBL_MST_EMPLOYEE EMP ON hdr.VID_REF = EMP.EMPID  AND hdr.TYPE='Employee'
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.GEID DESC ");

       

                           

        foreach($objDataList as $key=>$objResponse){

            $objPoRgpName   =   NULL;
            if(isset($objResponse->PO_NO) && $objResponse->PO_NO !=""){
                $objPoRgpName   =   $this->getPoRgpName($objResponse->GETYPE,$objResponse->PO_NO);
            }

            $objDataList[$key]->INVOICE_NO    =   $objPoRgpName;
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

    public function add(){       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
       
       
        $objTransporterList =   $this->getTransporterList();
        $objPackingList     =   $this->getPackingList();
        $objlastdt          =   $this->getLastdt();
        $Employee           =   $this->getEmployee();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_IMGE01_HDR',
            'HDR_ID'=>'GEID',
            'HDR_DOC_NO'=>'GE_NO',
            'HDR_DOC_DT'=>'GE_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        

        $table       =  "TBL_MST_UDFFOR_GE";
        $ObjUnionUDF =  DB::table($table)->select('*')
        ->whereIn('PARENTID',function($query) use ($CYID_REF){       
            $query->select("UDFGEID")->from('TBL_MST_UDFFOR_GE')
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
        $AlpsStatus =   $this->AlpsStatus();

        $FormId =   $this->form_id;

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        return view($this->view.$FormId.'add',compact([
                'FormId',
                'objTransporterList',
                'objPackingList',
                'objUdfData',
               
                'objCountUDF',
                
                'objlastdt',
                'AlpsStatus',
                'TabSetting',
                'Employee',
                'doc_req','docarray'
        ]));       
    }

    public function save(Request $request) {

        $r_count2 = $request['Row_Count2'];
            
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

        $GE_NO          = $request['GE_NO'];
        $GE_DT          = $request['GE_DT'];
        $GE_TM          = $request['GE_TM'];
        $SECURITY_SUP   = $request['SECURITY_SUP'];
        $SECURITY_GUARD = $request['SECURITY_GUARD'];
        $VID_REF        = $request['VID_REF'];
        $PO_NO          = $request['PO_NO'];
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
        $GETYPE         = $request['GETYPE'];
        

        $BOE_NO         = $request['BOE_NO'];
        $BOE_DATE       = $request['BOE_DATE'];
        $COUNTRY_ORIGIN = $request['COUNTRY_ORIGIN'];
        $PORT_DETAIL    = $request['PORT_DETAIL'];
        $AIRBILL_NO     = $request['AIRBILL_NO'];
        $AIRBILL_DATE       = $request['AIRBILL_DATE'];
        $FREIGHT_TERMS  = $request['FREIGHT_TERMS'];
        $CARRIERF_AGENT = $request['CARRIERF_AGENT'];
        $EWAY_BILLNO    = $request['EWAY_BILLNO'];
        $EWAY_BILLDATE  = $request['EWAY_BILLDATE'];
        $TOTAL_BOXES    = $request['TOTAL_BOXES'];
        $TRUCK_SEAL_NO  = $request['TRUCK_SEAL_NO'];
        $TYPE           = $request['TYPE'];

        $log_data = [ 
            $GE_NO,$GE_DT,$GE_TM,$SECURITY_SUP,$SECURITY_GUARD,
            $VID_REF,$PO_NO,$VENDOR_BILLNO,$VENDOR_BILLDT,$VENDOR_CHNO,
            $VENDOR_CHDT,$GATE_NO,$GATE_RGNO,$DATE,$REMARKS,
            $LR_NO,$LR_DT,$TRANSPORT_MODE,$VEHICLE_NO,$VEHICLE_CAT,
            $TRANSPORTER,$DRIVER_NAME,$WEIGHMENT_MACHINE,$WEIGHMENT_SLIP,$GROSS_WEIGHT,
            $TARE_WEIGHT,$NET_WEIGHT,$UNL_BAYNO,$ST_CUSTODIAN,$UNL_METHOD,
            $PTID_REF,$PACKING_NO,$PACKING_CONDITION,$GETYPE,$CYID_REF,
            $BRID_REF,$FYID_REF,$VTID_REF,$XMLUDF,$USERID, 
            Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$BOE_NO,$BOE_DATE,$COUNTRY_ORIGIN,$PORT_DETAIL,$AIRBILL_NO,$AIRBILL_DATE,$FREIGHT_TERMS,$CARRIERF_AGENT,$EWAY_BILLNO,$EWAY_BILLDATE,$TOTAL_BOXES,$TRUCK_SEAL_NO,$TYPE
        ];

       // dd($log_data); 

       

        $sp_result = DB::select('EXEC SP_GE_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?, ?,?,?,? ,?,?,?,?,?,?,?,?,?', $log_data);  

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

            $objResponse =  DB::table('TBL_TRN_IMGE01_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('GEID','=',$id)
            ->first();

            $objResponseVcl =[];
            if(isset($objResponse) && !empty($objResponse)){
                $objResponseVcl =  DB::table('TBL_TRN_IMGE01_VCL')
                ->where('GEID_REF','=',$id)
                ->first();
            }

            $objResponseUnl =[];
            if(isset($objResponse) && !empty($objResponse)){
                $objResponseUnl =  DB::table('TBL_TRN_IMGE01_UNL')
                ->where('GEID_REF','=',$id)
                ->first();
            }

            $objTransporterList =   $this->getTransporterList();
            $objPackingList     =   $this->getPackingList();
            $objlastdt          =   $this->getLastdt();
            $Employee           =   $this->getEmployee();

            $objVendorName      =NULL;
            if(isset($objResponse->VID_REF) && $objResponse->VID_REF !=""){
                $objVendorName      =   $this->getVendorName($objResponse->VID_REF,$objResponse->TYPE);
            }
           
            $objTransporterName =NULL;
            if(isset($objResponseVcl->TRANSPORTER) && $objResponseVcl->TRANSPORTER !=""){
                $objTransporterName =   $this->getTransporterName($objResponseVcl->TRANSPORTER);
            }

            $objPackingName     =NULL;
            if(isset($objResponseUnl->PTID_REF) && $objResponseUnl->PTID_REF !=""){
                $objPackingName     =   $this->getPackingName($objResponseUnl->PTID_REF);
            }

            $objPoRgpName       =NULL;
            if(isset($objResponse->PO_NO) && $objResponse->PO_NO !=""){
                $objPoRgpName       =   $this->getPoRgpName($objResponse->GETYPE,$objResponse->PO_NO);
            }

           

            $objUDF = DB::table('TBL_TRN_IMGE01_UDF')                    
            ->where('GEID_REF','=',$id)
            ->orderBy('GE_UDFID','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF); 

            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_GE")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('UDFGEID')->from('TBL_MST_UDFFOR_GE')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                         
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                        
                    

            $objUdfData = DB::table('TBL_MST_UDFFOR_GE')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
          
            ->union($ObjUnionUDF)
            ->get()->toArray();   
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_GE")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDFGEID')->from('TBL_MST_UDFFOR_GE')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                         
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                         
            $objUdfData2 = DB::table('TBL_MST_UDFFOR_GE')
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
                'FormId',
                'objRights',
                'objResponse',
                'objResponseVcl',
                'objResponseUnl',
               
                'objTransporterList',
                'objPackingList',
                'objVendorName',
                'objTransporterName',
                'objPackingName',
                'objPoRgpName',
                'objCount2',
                'objUDF',
                'objUdfData',
                'objCountUDF',
                'objUdfData2',
                'objlastdt',
                'AlpsStatus',
                'ActionStatus',
                'TabSetting',
                'Employee'
        ]));      


        }
     
    }

    public function update(Request $request){
        
        $r_count2 = $request['Row_Count2'];
                
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

        $GE_NO          = $request['GE_NO'];
        $GE_DT          = $request['GE_DT'];
        $GE_TM          = $request['GE_TM'];
        $SECURITY_SUP   = $request['SECURITY_SUP'];
        $SECURITY_GUARD = $request['SECURITY_GUARD'];
        $VID_REF        = $request['VID_REF'];
        $PO_NO          = $request['PO_NO'];
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
        $GETYPE         = $request['GETYPE'];


        $BOE_NO         = $request['BOE_NO'];
        $BOE_DATE       = $request['BOE_DATE'];
        $COUNTRY_ORIGIN = $request['COUNTRY_ORIGIN'];
        $PORT_DETAIL    = $request['PORT_DETAIL'];
        $AIRBILL_NO     = $request['AIRBILL_NO'];
        $AIRBILL_DATE       = $request['AIRBILL_DATE'];
        $FREIGHT_TERMS  = $request['FREIGHT_TERMS'];
        $CARRIERF_AGENT = $request['CARRIERF_AGENT'];
        $EWAY_BILLNO    = $request['EWAY_BILLNO'];
        $EWAY_BILLDATE  = $request['EWAY_BILLDATE'];
        $TOTAL_BOXES    = $request['TOTAL_BOXES'];
        $TRUCK_SEAL_NO  = $request['TRUCK_SEAL_NO'];
        $TYPE           = $request['TYPE'];


        $log_data = [ 
            $GE_NO,$GE_DT,$GE_TM,$SECURITY_SUP,$SECURITY_GUARD,
            $VID_REF,$PO_NO,$VENDOR_BILLNO,$VENDOR_BILLDT,$VENDOR_CHNO,
            $VENDOR_CHDT,$GATE_NO,$GATE_RGNO,$DATE,$REMARKS,
            $LR_NO,$LR_DT,$TRANSPORT_MODE,$VEHICLE_NO,$VEHICLE_CAT,
            $TRANSPORTER,$DRIVER_NAME,$WEIGHMENT_MACHINE,$WEIGHMENT_SLIP,$GROSS_WEIGHT,
            $TARE_WEIGHT,$NET_WEIGHT,$UNL_BAYNO,$ST_CUSTODIAN,$UNL_METHOD,
            $PTID_REF,$PACKING_NO,$PACKING_CONDITION,$GETYPE,$CYID_REF,
            $BRID_REF,$FYID_REF,$VTID_REF,$XMLUDF,$USERID, 
            Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$BOE_NO,$BOE_DATE,$COUNTRY_ORIGIN,$PORT_DETAIL,$AIRBILL_NO,$AIRBILL_DATE,$FREIGHT_TERMS,$CARRIERF_AGENT,$EWAY_BILLNO,$EWAY_BILLDATE,$TOTAL_BOXES,$TRUCK_SEAL_NO,$TYPE
        ];




        $sp_result = DB::select('EXEC SP_GE_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,? ,?,?,?,? ,?,?,?,?,?,?,?,?,?', $log_data);    
            
           
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $GE_NO. ' Sucessfully Updated.']);

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

            $objResponse =  DB::table('TBL_TRN_IMGE01_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('GEID','=',$id)
            ->first();

            $objResponseVcl =[];
            if(isset($objResponse) && !empty($objResponse)){
                $objResponseVcl =  DB::table('TBL_TRN_IMGE01_VCL')
                ->where('GEID_REF','=',$id)
                ->first();
            }

            $objResponseUnl =[];
            if(isset($objResponse) && !empty($objResponse)){
                $objResponseUnl =  DB::table('TBL_TRN_IMGE01_UNL')
                ->where('GEID_REF','=',$id)
                ->first();
            }

            $objTransporterList =   $this->getTransporterList();
            $objPackingList     =   $this->getPackingList();
            $objlastdt          =   $this->getLastdt();
            $Employee           =   $this->getEmployee();

            $objVendorName      =NULL;
            if(isset($objResponse->VID_REF) && $objResponse->VID_REF !=""){
                $objVendorName      =   $this->getVendorName($objResponse->VID_REF,$objResponse->TYPE);
            }

            $objTransporterName =NULL;
            if(isset($objResponseVcl->TRANSPORTER) && $objResponseVcl->TRANSPORTER !=""){
                $objTransporterName =   $this->getTransporterName($objResponseVcl->TRANSPORTER);
            }

            $objPackingName     =NULL;
            if(isset($objResponseUnl->PTID_REF) && $objResponseUnl->PTID_REF !=""){
                $objPackingName     =   $this->getPackingName($objResponseUnl->PTID_REF);
            }

            $objPoRgpName       =NULL;
            if(isset($objResponse->PO_NO) && $objResponse->PO_NO !=""){
                $objPoRgpName       =   $this->getPoRgpName($objResponse->GETYPE,$objResponse->PO_NO);
            }

           

            $objUDF = DB::table('TBL_TRN_IMGE01_UDF')                    
            ->where('GEID_REF','=',$id)
            ->orderBy('GE_UDFID','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF); 

            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_GE")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('UDFGEID')->from('TBL_MST_UDFFOR_GE')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                         
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                        
                    

            $objUdfData = DB::table('TBL_MST_UDFFOR_GE')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
          
            ->union($ObjUnionUDF)
            ->get()->toArray();   
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_GE")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDFGEID')->from('TBL_MST_UDFFOR_GE')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                         
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                         
            $objUdfData2 = DB::table('TBL_MST_UDFFOR_GE')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF2)
            ->get()->toArray(); 

            $FormId         =   $this->form_id;

            
            $AlpsStatus =   $this->AlpsStatus();
            $ActionStatus   =   "disabled";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');


            return view($this->view.$FormId.'view',compact([
                'FormId',
                'objRights',
                'objResponse',
                'objResponseVcl',
                'objResponseUnl',
               
                'objTransporterList',
                'objPackingList',
                'objVendorName',
                'objTransporterName',
                'objPackingName',
                'objPoRgpName',
                'objCount2',
                'objUDF',
                'objUdfData',
                'objCountUDF',
                'objUdfData2',
                'objlastdt',
                'AlpsStatus',
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
   
        $r_count2 = $request['Row_Count2'];
                
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

        $GE_NO          = $request['GE_NO'];
        $GE_DT          = $request['GE_DT'];
        $GE_TM          = $request['GE_TM'];
        $SECURITY_SUP   = $request['SECURITY_SUP'];
        $SECURITY_GUARD = $request['SECURITY_GUARD'];
        $VID_REF        = $request['VID_REF'];
        $PO_NO          = $request['PO_NO'];
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
        $GETYPE         = $request['GETYPE'];

        $BOE_NO         = $request['BOE_NO'];
        $BOE_DATE       = $request['BOE_DATE'];
        $COUNTRY_ORIGIN = $request['COUNTRY_ORIGIN'];
        $PORT_DETAIL    = $request['PORT_DETAIL'];
        $AIRBILL_NO     = $request['AIRBILL_NO'];
        $AIRBILL_DATE       = $request['AIRBILL_DATE'];
        $FREIGHT_TERMS  = $request['FREIGHT_TERMS'];
        $CARRIERF_AGENT = $request['CARRIERF_AGENT'];
        $EWAY_BILLNO    = $request['EWAY_BILLNO'];
        $EWAY_BILLDATE  = $request['EWAY_BILLDATE'];
        $TOTAL_BOXES    = $request['TOTAL_BOXES'];
        $TRUCK_SEAL_NO  = $request['TRUCK_SEAL_NO'];
        $TYPE           = $request['TYPE'];

        $log_data = [ 
            $GE_NO,$GE_DT,$GE_TM,$SECURITY_SUP,$SECURITY_GUARD,
            $VID_REF,$PO_NO,$VENDOR_BILLNO,$VENDOR_BILLDT,$VENDOR_CHNO,
            $VENDOR_CHDT,$GATE_NO,$GATE_RGNO,$DATE,$REMARKS,
            $LR_NO,$LR_DT,$TRANSPORT_MODE,$VEHICLE_NO,$VEHICLE_CAT,
            $TRANSPORTER,$DRIVER_NAME,$WEIGHMENT_MACHINE,$WEIGHMENT_SLIP,$GROSS_WEIGHT,
            $TARE_WEIGHT,$NET_WEIGHT,$UNL_BAYNO,$ST_CUSTODIAN,$UNL_METHOD,
            $PTID_REF,$PACKING_NO,$PACKING_CONDITION,$GETYPE,$CYID_REF,
            $BRID_REF,$FYID_REF,$VTID_REF,$XMLUDF,$USERID, 
            Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$BOE_NO,$BOE_DATE,$COUNTRY_ORIGIN,$PORT_DETAIL,$AIRBILL_NO,$AIRBILL_DATE,$FREIGHT_TERMS,$CARRIERF_AGENT,$EWAY_BILLNO,$EWAY_BILLDATE,$TOTAL_BOXES,$TRUCK_SEAL_NO,$TYPE
        ];

        $sp_result = DB::select('EXEC SP_GE_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?  ,?,?,?,?,  ?,?,?,? ,?,?,?,?,?', $log_data); 
            
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $GE_NO. ' Sucessfully Approved.']);

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
        $TABLE      =   "TBL_TRN_IMGE01_HDR";
        $FIELD      =   "GEID";
        $ACTIONNAME     = $Approvallevel;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
            
        $log_data = [ 
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_GE ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
        
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
        $TABLE      =   "TBL_TRN_IMGE01_HDR";
        $FIELD      =   "GEID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
         'NT'  => 'TBL_TRN_IMGE01_VCL',
        ];
        $req_data[1]=[
        'NT'  => 'TBL_TRN_IMGE01_UNL',
        ];
        $req_data[2]=[
        'NT'  => 'TBL_TRN_IMGE01_UDF',
        ];
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_GE  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_IMGE01_HDR')->where('GEID','=',$id)->first();

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
        
		$image_path         =   "docs/company".$CYID_REF."/GateEntry";     
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

        $GE_NO  =   trim($request['GE_NO']);
        $objLabel = DB::table('TBL_TRN_IMGE01_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('GE_NO','=',$GE_NO)
        ->select('GEID')->first();

        if($objLabel){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }


    public function checkDuplicateVendorBillNo(Request $request){
        $VENDOR_BILLNO  =   trim($request['VENDOR_BILLNO']);
        $VID_REF  =   trim($request['VID_REF']);
        $objData = DB::table('TBL_TRN_IMGE01_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('VENDOR_BILLNO','=',$VENDOR_BILLNO)
        ->where('VID_REF','=',$VID_REF) 
		 ->where('STATUS','!=','C')
        ->select('GEID')->first();
        if($objData){  
            echo 1;
        }else{
            echo 0;
        }
        
      //  exit();
    }



   
    /*
    public function getVendorList(){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF'); 

        return  DB::select("
        select T1.VID,T1.VCODE,T1.NAME,T2.GLNAME,T3.NAME AS COUNTRY_NAME,T4.NAME AS STATE_NAME,T5.NAME AS CITY_NAME 
        from TBL_MST_VENDOR T1
        LEFT JOIN TBL_MST_GENERALLEDGER T2 ON T1.GLID_REF=T2.GLID
        LEFT JOIN TBL_MST_COUNTRY T3 ON T1.REGCTRYID_REF=T3.CTRYID
        LEFT JOIN TBL_MST_STATE T4 ON T1.REGSTID_REF=T4.STID
        LEFT JOIN TBL_MST_CITY T5 ON T1.REGCITYID_REF=T5.CITYID
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.STATUS='A' AND (T1.DEACTIVATED=0 or T1.DEACTIVATED is null)
        ORDER BY T1.VCODE
        "); 
    }
    */

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



    public function getVendorList(){
        return  DB::table('TBL_MST_VENDOR')
		->leftJoin('TBL_MST_GENERALLEDGER','TBL_MST_GENERALLEDGER.GLID','=','TBL_MST_VENDOR.GLID_REF')
		->leftJoin('TBL_MST_COUNTRY','TBL_MST_COUNTRY.CTRYID','=','TBL_MST_VENDOR.REGCTRYID_REF')
		->leftJoin('TBL_MST_STATE','TBL_MST_STATE.STID','=','TBL_MST_VENDOR.REGSTID_REF')
		->leftJoin('TBL_MST_CITY','TBL_MST_CITY.CITYID','=','TBL_MST_VENDOR.REGCITYID_REF')
        ->where('TBL_MST_VENDOR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_VENDOR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_MST_VENDOR.STATUS','=','A')
        ->whereRaw("(TBL_MST_VENDOR.DEACTIVATED=0 or TBL_MST_VENDOR.DEACTIVATED is null)")
        ->select('TBL_MST_VENDOR.VID','TBL_MST_VENDOR.VCODE','TBL_MST_VENDOR.NAME','TBL_MST_GENERALLEDGER.GLNAME','TBL_MST_COUNTRY.NAME AS COUNTRY_NAME',
			'TBL_MST_STATE.NAME AS STATE_NAME','TBL_MST_CITY.NAME AS CITY_NAME')
        ->get();         
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

    public function getPoRgpName($Type,$idd){

       $id  =   trim($idd);


        if($Type ==="RGP"){
            
            $dataArr=array();
            $objDataList = DB::select("select RGP_NO from TBL_TRN_IRGP01_HDR where RGPID in($id)");
            foreach($objDataList as $val){
                $dataArr[]=$val->RGP_NO;
            } 

        }
        else if($Type ==="BPO"){
            
            $dataArr=array();
            $objDataList = DB::select("select BPO_NO from TBL_TRN_PROR03_HDR where BPOID in($id)");
            foreach($objDataList as $val){
                $dataArr[]=$val->BPO_NO;
            } 
            
        }
        else if($Type ==="IPO"){
            
            $dataArr=array();
            $objDataList = DB::select("select IPO_NO from TBL_TRN_IPO_HDR where IPO_ID in($id)");
            foreach($objDataList as $val){
                $dataArr[]=$val->IPO_NO;
            } 
            
        }
        else{
            $dataArr=array();
            $objDataList = DB::select("select PO_NO from TBL_TRN_PROR01_HDR where POID in($id)");
            foreach($objDataList as $val){
                $dataArr[]=$val->PO_NO;
            }  
        }

        $Result =implode(",",$dataArr);
        return $Result;

    }

    public function getPoRgp(Request $request){

        $Type       =   $request['Type'];
        $VID_REF    =   $request['VID_REF'];
        $value      =   $request['value'] !=""?$request['value']:'';
        $CTYPE      =   $request['CTYPE'];

        if($Type ==="RGP"){
            $objDataList = DB::table('TBL_TRN_IRGP01_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('STATUS','=','A')
            ->where('VID_REF','=',$VID_REF)
            ->where('TYPE','=',$CTYPE)
            ->select('RGPID As PORGP_ID','RGP_NO AS PORGP_NO','RGP_DT AS PORGP_DATE')			
            ->get();
        }
        else if($Type ==="BPO"){
            
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');
            $FYID_REF   =   Session::get('FYID_REF');

            $objDataList =  DB::select("SELECT 
            DISTINCT T2.BPOID As PORGP_ID,T2.BPO_NO AS PORGP_NO,T2.BPO_DT AS PORGP_DATE
            FROM TBL_TRN_SPOR03_HDR T1
            INNER JOIN TBL_TRN_PROR03_HDR T2 ON T1.BPOID_REF=T2.BPOID AND T2.VID_REF='$VID_REF'
            WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' 
			AND T1.STATUS='A' 
            ");  

        }
        else if($Type ==="IPO"){

            $commercial_invoice_status  =   $this->commercial_invoice_status();

            if($commercial_invoice_status > 0){
                $objDataList = DB::table('TBL_TRN_IPO_HDR')
                ->Join('TBL_TRN_COM_INV_HDR', 'TBL_TRN_IPO_HDR.IPO_ID','=','TBL_TRN_COM_INV_HDR.IPO_ID_REF')  
                ->leftJoin('TBL_TRN_IPO_MAT', 'TBL_TRN_IPO_HDR.IPO_ID','=','TBL_TRN_IPO_MAT.IPO_ID_REF')  
                ->where('TBL_TRN_IPO_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                ->where('TBL_TRN_IPO_MAT.PENDING_QTY','>','0.000') 
                ->where('TBL_TRN_IPO_HDR.BRID_REF','=',Session::get('BRID_REF'))
                ->where('TBL_TRN_IPO_HDR.STATUS','=','A')
                ->where('TBL_TRN_COM_INV_HDR.STATUS','=','A')
                ->where('TBL_TRN_IPO_HDR.VID_REF','=',$VID_REF)
                ->select('TBL_TRN_IPO_HDR.IPO_ID As PORGP_ID','TBL_TRN_IPO_HDR.IPO_NO AS PORGP_NO','TBL_TRN_IPO_HDR.IPO_DT AS PORGP_DATE')
                ->distinct('TBL_TRN_IPO_HDR.IPO_ID')
                ->get(); 
            }
            else{
                $objDataList = DB::table('TBL_TRN_IPO_HDR')               
                ->leftJoin('TBL_TRN_IPO_MAT', 'TBL_TRN_IPO_HDR.IPO_ID','=','TBL_TRN_IPO_MAT.IPO_ID_REF')  
                ->where('TBL_TRN_IPO_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                ->where('TBL_TRN_IPO_MAT.PENDING_QTY','>','0.000') 
                ->where('TBL_TRN_IPO_HDR.BRID_REF','=',Session::get('BRID_REF'))
                ->where('TBL_TRN_IPO_HDR.STATUS','=','A')
                ->where('TBL_TRN_IPO_HDR.VID_REF','=',$VID_REF)
                ->select('TBL_TRN_IPO_HDR.IPO_ID As PORGP_ID','TBL_TRN_IPO_HDR.IPO_NO AS PORGP_NO','TBL_TRN_IPO_HDR.IPO_DT AS PORGP_DATE')
                ->distinct('TBL_TRN_IPO_HDR.IPO_ID')
                ->get(); 
            }
        }
        else{
            $objDataList = DB::table('TBL_TRN_PROR01_HDR')
			->leftJoin('TBL_TRN_PROR01_MAT', 'TBL_TRN_PROR01_HDR.POID','=','TBL_TRN_PROR01_MAT.POID_REF')
            ->where('TBL_TRN_PROR01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
			->where('TBL_TRN_PROR01_MAT.PENDING_QTY','>','0.000') 
            ->where('TBL_TRN_PROR01_HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_PROR01_HDR.STATUS','=','A')
            ->where('TBL_TRN_PROR01_HDR.VID_REF','=',$VID_REF)
            ->select('TBL_TRN_PROR01_HDR.POID As PORGP_ID','TBL_TRN_PROR01_HDR.PO_NO AS PORGP_NO','TBL_TRN_PROR01_HDR.PO_DT AS PORGP_DATE')
			->distinct('TBL_TRN_PROR01_HDR.POID')
            ->get(); 
        }

        if(!empty($objDataList)){
            foreach ($objDataList as $key=>$val){
                if($request['value'] !=""){
                    $checked=   in_array($val-> PORGP_ID,explode(",",$request['value']))?"checked":'';
                }
                else{
                    $checked="";
                }
            ?>
            <tr id="PO_NO_TDID_<?php echo $key;?>" class="PO_NO_Row">
                <td class="ROW1" ><input <?php echo $checked;?> type="checkbox" class="PO_NO_CHECK" id="txtPO_NO_CHECK_<?php echo $key;?>" value="<?php echo $val-> PORGP_ID;?>"></td>
                <td class="ROW2" ><?php echo $val-> PORGP_NO;?>
                <input type="hidden" id="txtPO_NO_TDID_<?php echo $key;?>" data-desc="<?php echo $val-> PORGP_NO;?>"  value="<?php echo $val-> PORGP_ID;?>"/>
                </td>
                <td class="ROW3"><?php echo $val-> PORGP_DATE;?></td>
            </tr>
            <?php
            }
        }
        else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();
    }

    public function getLastdt(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        return  DB::select('SELECT MAX(GE_DT) GE_DT FROM TBL_TRN_IMGE01_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
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

    public function commercial_invoice_status(){

        $result   = DB::table('TBL_MST_ADDL_TAB_SETTING')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('TABLE_NAME','=','COMMERCIAL_INVOICE_APPLICABLE')
                    ->where('TAB_NAME','=','YES')
                    ->count();

        return $result;
    }
    
}
