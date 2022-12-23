<?php
namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Admin\TblMstUser;
use Auth;
use DB;
use Facade\Ignition\DumpRecorder\Dump;
use Session;
use Response;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Helpers\Helper;
use App\Helpers\Utils;

class TrnFrm309Controller extends Controller{

    protected $form_id  = 309;
    protected $vtid_ref = 398;
    protected $view     = "transactions.Production.ProductionMovement.trnfrm";
   
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

        $objDataList	=	DB::select("select hdr.PNMID,hdr.PNM_NO,hdr.PNM_DT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.PNMID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_PDPNM_HDR hdr
                            on a.VID = hdr.PNMID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.PNMID DESC ");

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
        $Status     = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
       
        $objlastdt          =   $this->getLastdt();
        $objPRO             =   $this->getPROList();
        $objStage           =   $this->getStageList();
        $objMACHINE         =   $this->getMachineList();
        $objSHIFT           =   $this->getShiftList();
        $objEMP             =   $this->getOperatorList();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_PDPNM_HDR',
            'HDR_ID'=>'PNMID',
            'HDR_DOC_NO'=>'PNM_NO',
            'HDR_DOC_DT'=>'PNM_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        
       
   
        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_PNM")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                {       
                                $query->select('UDFPNMID')->from('TBL_MST_UDFFOR_PNM')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);                      
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);                   
                   

        $objUdfData = DB::table('TBL_MST_UDFFOR_PNM')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();  

        $objCountUDF = count($objUdfData);

        $component_list = DB::table('TBL_MST_COSTCOMPONENT')
                             ->where('STATUS','=',$Status)
                             ->where('CYID_REF','=',Auth::user()->CYID_REF)
                             ->select('TBL_MST_COSTCOMPONENT.*')
                             ->get();
        
        $FormId     =   $this->form_id;
        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
		
		$objTOLERENCE = DB::table('TBL_MST_ADDL_TAB_SETTING')
        ->where('TABLE_NAME','=','PNM')
        ->where('TAB_NAME','=','PNM TOLERENCE')
        ->select('TBL_MST_ADDL_TAB_SETTING.FIELD1')
        ->first();

        $objUOM      =   $this->GetUOM(); 
        
        return view($this->view.$FormId.'add',compact([
            'AlpsStatus',
            'FormId',
            'objPRO',
            'objStage',
            'objMACHINE',
            'objSHIFT',
            'objEMP',
           
            'objlastdt',
            'objUdfData',
            'objCountUDF',
            'component_list',
            'TabSetting','objTOLERENCE','objUOM',
            'doc_req','docarray'
            ]));       
    }

    public function save(Request $request) {

        $PNM_NO             =   $request['PNM_NO'];
        $PNM_DT             =   $request['PNM_DT'];
        $PROID_REF          =   $request['PROID_REF'];
        $ITEMID_REF         =   $request['ITEMID_REF'];
        $UOMID_REF          =   $request['UOMID_REF'];
        $PNM_QTY            =   $request['PNM_QTY'];
        $SOID_REF           =   $request['SOID_REF'];
        $SQID_REF           =   $request['SQID_REF'];
        $SEID_REF           =   $request['SEID_REF'];
        $MOVEMENT_STAGE     =   $request['MOVEMENT_STAGE'];
        $PSTAGEID_REF       =   $request['PSTAGEID_REF'];
        $TO_STAGE_STORE_QC  =   $request['TO_STAGE_STORE_QC'];
        $TOTAL_COST         =   $request['TOTAL_COST'];
		$ACTUAL_QTY         =   $request['ACTUAL_QTY'];
		$PARTIAL_PRODUCTION = 	(isset($request['PARTIAL_PRODUCTION'])!="true" ? 0 : 1);

        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count4 = $request['Row_Count4'];
        $r_count5 = $request['Row_Count5'];
        $r_count6 = $request['Row_Count6'];
        $r_count7 = $request['Row_Count7'];
        
		$req_data=array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['MACHINEID_REF_'.$i])){

                $req_data[$i] = [
                    'ITEMID_REF'    =>  $ITEMID_REF,
                    'UOMID_REF'     =>  $UOMID_REF,
                    'MACHINEID_REF' =>  (!empty($request['MACHINEID_REF_'.$i])) == 'true' ? $request['MACHINEID_REF_'.$i] : NULL,
                    'SHIFTID_REF'   =>  (!empty($request['SHIFTID_REF_'.$i])) == 'true' ? $request['SHIFTID_REF_'.$i] : NULL,
                    'EMPID_REF'     =>  (!empty($request['EMPID_REF_'.$i])) == 'true' ? $request['EMPID_REF_'.$i] : NULL,
                    'BATCH'         =>  $request['BATCH_'.$i],
                    'QTY'           =>  $request['QTY_'.$i],
                    'PROID_REF'     =>  $PROID_REF,
                    'SOID_REF'     =>   $SOID_REF,
                    'SQID_REF'     =>   $SQID_REF,  
                    'SEID_REF'     =>   $SEID_REF,  
                ];

            }
        }


        $wrapped_links["MACHINE"] = $req_data; 
        $XMLMACHINE = ArrayToXml::convert($wrapped_links);
		
		$reqdata3=array();
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['UDF_'.$i])){
                $reqdata3[$i] = [
                    'UDFPNMID_REF'      => $request['UDF_'.$i],
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

		
		$req_data4=array();
        for ($i=0; $i<=$r_count4; $i++){
            if(isset($request['REQ_ITEMID_REF_'.$i])){

                $req_data4[$i] = [
                    'ITEMID_REF'      	    => $request['REQ_ITEMID_REF_'.$i],
                    'UOMID_REF'   	        => $request['REQ_UOMID_REF_'.$i],
                    'ITEM_DESCRIPTION'      => $request['REQ_ITEM_DESCRIPTION_'.$i],
                    'CONSUME_QTY'           => $request['REQ_CONSUME_QTY_'.$i],
                    'BATCH'     		    => $request['REQ_LOT_NO_'.$i],
                    'CHANGE_IN_CONSUME_QTY'	=> $request['REQ_CHANGE_IN_CONSUME_QTY_'.$i],
					'PROID_REF'             => $PROID_REF,
                    'SOID_REF'     		    => $SOID_REF,
                    'SQID_REF'     		    => $SQID_REF,  
                    'SEID_REF'     		    => $SEID_REF,
                    'RATE'     		        => $request['RATE_'.$i],
                    'WASTAGE_QTY'     		=> (!empty($request['WASTAGE_QTY_'.$i]) ? $request['WASTAGE_QTY_'.$i] : 0),
                ];

            }
        }
        
		if($r_count4 > 0){
            $wrapped_links4["CONSUME"] = $req_data4; 
			$XMLCONSUME = ArrayToXml::convert($wrapped_links4);
        }
        else{
            $XMLCONSUME=NULL;
        }	

        //By product Tab starts here 

        for ($i=0; $i<=$r_count6; $i++)
        {
                if(isset($request['MainItemId2_Ref_'.$i]) && !is_null($request['MainItemId2_Ref_'.$i]))
                {
                    if(isset($request['MainItemId2_Ref_'.$i]))
                    {
                        $reqdata6[$i] = [                          
                            'ITEMID_REF'        => $request['MainItemId2_Ref_'.$i],
                            'UOMID_REF'         => $request['Mainuom2_Ref_'.$i],
                            'CONSUME_QTY'       =>(!empty($request['PRODUCE_QTY2_'.$i]) ? $request['PRODUCE_QTY2_'.$i] : 0),
                            'ALT_UOMID_REF'     => $request['BYPUOM_REF_'.$i],
                            'TYPE'              => $request['TYPE_'.$i],                      
                        ];
                    }
                }
            
        }

        if(isset($reqdata6))
        { 
            $wrapped_links6["BYP"] = $reqdata6;
            $BYPRODUCTMAT = ArrayToXml::convert($wrapped_links6);
        }
        else
        {
            $BYPRODUCTMAT = NULL; 
        }   

        // By product section ends here. 



            //By Additional Material Tab starts here 

            for ($i=0; $i<=$r_count7; $i++)
            {
                    if(isset($request['AD_ITEMID_REF_'.$i]) && !is_null($request['AD_ITEMID_REF_'.$i]))
                    {
                        if(isset($request['AD_ITEMID_REF_'.$i]))
                        {
                            $reqdata7[$i] = [                          
                                'ITEMID_REF'        => $request['AD_ITEMID_REF_'.$i],
                                'UOMID_REF'         => $request['AD_UOMID_REF_'.$i],
                                'ISSUED_QTY'       =>(!empty($request['ISSUED_QTY_'.$i]) ? $request['ISSUED_QTY_'.$i] : 0),
                                'CONSUME_QTY'       =>(!empty($request['ADCONSUME_QTY_'.$i]) ? $request['ADCONSUME_QTY_'.$i] : 0),
                                'WASTAGE_QTY'       =>(!empty($request['ADWASTAGE_QTY_'.$i]) ? $request['ADWASTAGE_QTY_'.$i] : 0),                          
                            
                            ];
                        }
                    }
                
            }
    
            if(isset($reqdata7))
            { 
                $wrapped_links7["ADDITIONAL"] = $reqdata7;
                $ADDITIONALMAT = ArrayToXml::convert($wrapped_links7);
            }
            else
            {
                $ADDITIONALMAT = NULL; 
            }   
    
            // By Additional Material Section ends here. 




        
        for ($i=0; $i<=$r_count5; $i++){
            if(isset($request['Componentid_'.$i]) && !is_null($request['Componentid_'.$i]))
            {
                if(isset($request['Componentid_'.$i]))
                {
                    $reqdata5[$i] = [
                        
                        'CCOMPONENTID_REF'    => $request['Componentid_'.$i],
                        'VALUE'         => (!empty($request['value_'.$i]) ? $request['value_'.$i] : 0),
                    
                    ];
                }
            }
        }
     
        if(isset($reqdata5)){ 
            $wrapped_links5["OTH"] = $reqdata5;
            $XMLOTH = ArrayToXml::convert($wrapped_links5);
        }
        else{
            $XMLOTH = NULL; 
        }  

        
        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $log_data = [ 
            $PNM_NO,$PNM_DT,$PROID_REF,$MOVEMENT_STAGE,$PSTAGEID_REF,
            $TO_STAGE_STORE_QC,$ITEMID_REF,$UOMID_REF,$PNM_QTY,$CYID_REF,
            $BRID_REF,$FYID_REF,$VTID_REF,$XMLCONSUME,$XMLMACHINE,
            $XMLUDF,$USERID, Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,
            $IPADDRESS,$SOID_REF,$SQID_REF,$SEID_REF,$TOTAL_COST,$XMLOTH,$ACTUAL_QTY,$PARTIAL_PRODUCTION,$BYPRODUCTMAT,$ADDITIONALMAT
        ]; 

        //dd($log_data); 

        $sp_result = DB::select('EXEC SP_PNM_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?', $log_data);  

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

            $objRights  =   DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();
			
            $objlastdt          =   $this->getLastdt();
            $objPRO             =   $this->getPROList();
            $objStage           =   $this->getStageList();
            $objMACHINE         =   $this->getMachineList();
            $objSHIFT           =   $this->getShiftList();
            $objEMP             =   $this->getOperatorList();
    
            $objResponse    =   DB::table('TBL_TRN_PDPNM_HDR AS T1')
            ->leftJoin('TBL_TRN_PDPRO_HDR AS T2', 'T1.PROID_REF','=','T2.PROID')
            ->leftJoin('TBL_MST_ITEM AS T3', 'T1.ITEMID_REF','=','T3.ITEMID')
            ->leftJoin('TBL_MST_UOM AS T4', 'T1.UOMID_REF','=','T4.UOMID')
            ->leftJoin('TBL_MST_PRODUCTIONSTAGES AS T5', 'T1.PSTAGEID_REF','=','T5.PSTAGEID')
            ->leftJoin('TBL_MST_PRODUCTIONSTAGES AS T6', 'T1.TO_STAGE_STORE_QC','=','T6.PSTAGEID')
            ->leftJoin('TBL_MST_STORE AS T7', 'T1.TO_STAGE_STORE_QC','=','T7.STID')
           
            ->where('T1.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('T1.BRID_REF','=',Session::get('BRID_REF'))
            ->where('T1.FYID_REF','=',Session::get('FYID_REF'))
            ->where('T1.PNMID','=',$id)
            ->select(
                'T1.*', 
                'T2.PRO_NO',
                'T3.ICODE','T3.NAME',
                'T4.UOMCODE',
                'T4.DESCRIPTIONS AS UOMDESC',
                'T5.PSTAGE_CODE AS FROM_STAGE_CODE',
                'T5.DESCRIPTIONS AS FROM_STAGE_DESC',
                'T6.PSTAGE_CODE AS TO_STAGE_CODE',
                'T6.DESCRIPTIONS AS TO_STAGE_DESC',
                'T7.STCODE AS TO_STORE_CODE',
                'T7.NAME AS TO_STORE_DESC'
               
                )
            ->first();

            $objMAT = DB::select("SELECT 
            T1.*,
            T2.MACHINE_NO,T2.MACHINE_DESC,
            T3.SHIFT_CODE,T3.SHIFT_NAME,
            T4.EMPCODE,T4.FNAME
			FROM TBL_TRN_PDPNM_MACHINE T1
			LEFT JOIN TBL_MST_MACHINE T2 ON T1.MACHINEID_REF=T2.MACHINEID
            LEFT JOIN TBL_MST_SHIFT T3 ON T1.SHIFTID_REF=T3.SHIFTID
            LEFT JOIN TBL_MST_EMPLOYEE T4 ON T1.EMPID_REF=T4.EMPID
            WHERE T1.PNMID_REF='$id' ORDER BY T1.PNM_MACHINEID ASC"); 


            $material_array = DB::select("SELECT 
            T1.*,
			T2.ICODE,T2.NAME AS ITEM_NAME,T2.MATERIAL_TYPE,
			T3.UOMCODE,
            T3.DESCRIPTIONS AS UOMDESC
			FROM TBL_TRN_PDPNM_CONSUME T1
			LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
			LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            WHERE T1.PNMID_REF='$id' ORDER BY T1.PNM_CONSUMEID ASC");



			$objUDF = DB::table('TBL_TRN_PDPNM_UDF')                    
            ->where('PNMID_REF','=',$id)
            ->orderBy('PNM_UDFID','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF);
			
		
			$ObjUnionUDF = DB::table("TBL_MST_UDFFOR_PNM")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('UDFPNMID')->from('TBL_MST_UDFFOR_PNM')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                     
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                     
                    

            $objUdfData = DB::table('TBL_MST_UDFFOR_PNM')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_PNM")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDFPNMID')->from('TBL_MST_UDFFOR_PNM')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                         
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                           
            

            $objUdfData2 = DB::table('TBL_MST_UDFFOR_PNM')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF2)
            ->get()->toArray(); 

            $component_list = DB::table('TBL_MST_COSTCOMPONENT')
                             ->where('STATUS','=',$Status)
                             ->where('CYID_REF','=',Auth::user()->CYID_REF)
                             ->select('TBL_MST_COSTCOMPONENT.*')
                             ->get();

            $objOTH = DB::table('TBL_TRN_PDPNM_OTH')                    
            ->where('TBL_TRN_PDPNM_OTH.PNMID_REF','=',$id)
            ->leftJoin('TBL_MST_COSTCOMPONENT', 'TBL_TRN_PDPNM_OTH.CCOMPONENTID_REF','=','TBL_MST_COSTCOMPONENT.CCOMPONENTID')     
            ->select('TBL_TRN_PDPNM_OTH.*','TBL_MST_COSTCOMPONENT.*')
            ->orderBy('TBL_TRN_PDPNM_OTH.PNM_OTHID','ASC')
            ->get()->toArray();
    
           

		
            $FormId         =   $this->form_id;
            $AlpsStatus =   $this->AlpsStatus();
            $ActionStatus   =   "";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
			
			$objTOLERENCE = DB::table('TBL_MST_ADDL_TAB_SETTING')
			->where('TABLE_NAME','=','PNM')
			->where('TAB_NAME','=','PNM TOLERENCE')
			->select('TBL_MST_ADDL_TAB_SETTING.FIELD1')
			->first();
            $objUOM      =   $this->GetUOM(); 
            return view($this->view.$FormId.'edit',compact([
                'AlpsStatus',
				'FormId',
                'objRights',
                'objPRO',
                'objStage',
                'objMACHINE',
                'objSHIFT',
                'objEMP',
				'objlastdt',
				'objResponse',
				'objMAT',
				'material_array',
				'objUDF',
                'objUdfData',
                'objCountUDF',
                'objUdfData2',
                'component_list',
                'objOTH',
                'ActionStatus',
                'TabSetting','objTOLERENCE',
                'objUOM'
			]));      

        }
     
    }

    public function update(Request $request){

        $PNM_NO             =   $request['PNM_NO'];
        $PNM_DT             =   $request['PNM_DT'];
        $PROID_REF          =   $request['PROID_REF'];
        $ITEMID_REF         =   $request['ITEMID_REF'];
        $UOMID_REF          =   $request['UOMID_REF'];
        $PNM_QTY            =   $request['PNM_QTY'];
        $SOID_REF           =   $request['SOID_REF'];
        $SQID_REF           =   $request['SQID_REF'];
        $SEID_REF           =   $request['SEID_REF'];
        $MOVEMENT_STAGE     =   $request['MOVEMENT_STAGE'];
        $PSTAGEID_REF       =   $request['PSTAGEID_REF'];
        $TO_STAGE_STORE_QC  =   $request['TO_STAGE_STORE_QC'];
        $TOTAL_COST         =   $request['TOTAL_COST'];
		$ACTUAL_QTY         =   $request['ACTUAL_QTY'];
		$PARTIAL_PRODUCTION = 	(isset($request['PARTIAL_PRODUCTION'])!="true" ? 0 : 1);
        
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count4 = $request['Row_Count4'];
        $r_count5 = $request['Row_Count5'];
        $r_count6 = $request['Row_Count6'];
        $r_count7 = $request['Row_Count7'];
        
		$req_data=array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['MACHINEID_REF_'.$i])){

                $req_data[$i] = [
                    'ITEMID_REF'    =>  $ITEMID_REF,
                    'UOMID_REF'     =>  $UOMID_REF,
                    'MACHINEID_REF' =>  (!empty($request['MACHINEID_REF_'.$i])) == 'true' ? $request['MACHINEID_REF_'.$i] : NULL,
                    'SHIFTID_REF'   =>  (!empty($request['SHIFTID_REF_'.$i])) == 'true' ? $request['SHIFTID_REF_'.$i] : NULL,
                    'EMPID_REF'     =>  (!empty($request['EMPID_REF_'.$i])) == 'true' ? $request['EMPID_REF_'.$i] : NULL,
                    'BATCH'         =>  $request['BATCH_'.$i],
                    'QTY'      =>  $request['QTY_'.$i],
                    'PROID_REF'     =>  $PROID_REF,
                    'SOID_REF'     =>   $SOID_REF,
                    'SQID_REF'     =>   $SQID_REF,  
                    'SEID_REF'     =>   $SEID_REF,  
                ];

            }
        }

        $wrapped_links["MACHINE"] = $req_data; 
        $XMLMACHINE = ArrayToXml::convert($wrapped_links);
		
		$reqdata3=array();
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['UDF_'.$i])){
                $reqdata3[$i] = [
                    'UDFPNMID_REF'      => $request['UDF_'.$i],
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

		
		$req_data4=array();
        for ($i=0; $i<=$r_count4; $i++){
            if(isset($request['REQ_ITEMID_REF_'.$i])){

                $req_data4[$i] = [
                    'ITEMID_REF'      	    => $request['REQ_ITEMID_REF_'.$i],
                    'UOMID_REF'   	        => $request['REQ_UOMID_REF_'.$i],
                    'ITEM_DESCRIPTION'      => $request['REQ_ITEM_DESCRIPTION_'.$i],
                    'CONSUME_QTY'           => $request['REQ_CONSUME_QTY_'.$i],
                    'BATCH'     		    => $request['REQ_LOT_NO_'.$i],
                    'CHANGE_IN_CONSUME_QTY'	=> $request['REQ_CHANGE_IN_CONSUME_QTY_'.$i],
					'PROID_REF'             => $PROID_REF,
                    'SOID_REF'     		    => $SOID_REF,
                    'SQID_REF'     		    => $SQID_REF,  
                    'SEID_REF'     		    => $SEID_REF,
                    'RATE'     		        => $request['RATE_'.$i],
                    'WASTAGE_QTY'     		=> (!empty($request['WASTAGE_QTY_'.$i]) ? $request['WASTAGE_QTY_'.$i] : 0),
                ];

            }
        }

		if($r_count4 > 0){
            $wrapped_links4["CONSUME"] = $req_data4; 
			$XMLCONSUME = ArrayToXml::convert($wrapped_links4);
        }
        else{
            $XMLCONSUME=NULL;
        }
        

        //By product Tab starts here 

        for ($i=0; $i<=$r_count6; $i++)
        {
                if(isset($request['MainItemId2_Ref_'.$i]) && !is_null($request['MainItemId2_Ref_'.$i]))
                {
                    if(isset($request['MainItemId2_Ref_'.$i]))
                    {
                        $reqdata6[$i] = [                          
                            'ITEMID_REF'        => $request['MainItemId2_Ref_'.$i],
                            'UOMID_REF'         => $request['Mainuom2_Ref_'.$i],
                            'CONSUME_QTY'       =>(!empty($request['PRODUCE_QTY2_'.$i]) ? $request['PRODUCE_QTY2_'.$i] : 0),
                            'ALT_UOMID_REF'     => $request['BYPUOM_REF_'.$i],
                            'TYPE'              => $request['TYPE_'.$i],                      
                        ];
                    }
                }
            
        }

        if(isset($reqdata6))
        { 
            $wrapped_links6["BYP"] = $reqdata6;
            $BYPRODUCTMAT = ArrayToXml::convert($wrapped_links6);
        }
        else
        {
            $BYPRODUCTMAT = NULL; 
        }   

        // By product section ends here. 



            //By Additional Material Tab starts here 

            for ($i=0; $i<=$r_count7; $i++)
            {
                    if(isset($request['AD_ITEMID_REF_'.$i]) && !is_null($request['AD_ITEMID_REF_'.$i]))
                    {
                        if(isset($request['AD_ITEMID_REF_'.$i]))
                        {
                            $reqdata7[$i] = [                          
                                'ITEMID_REF'        => $request['AD_ITEMID_REF_'.$i],
                                'UOMID_REF'         => $request['AD_UOMID_REF_'.$i],
                                'ISSUED_QTY'        =>(!empty($request['ISSUED_QTY_'.$i]) ? $request['ISSUED_QTY_'.$i] : 0),
                                'CONSUME_QTY'       =>(!empty($request['ADCONSUME_QTY_'.$i]) ? $request['ADCONSUME_QTY_'.$i] : 0),
                                'WASTAGE_QTY'       =>(!empty($request['ADWASTAGE_QTY_'.$i]) ? $request['ADWASTAGE_QTY_'.$i] : 0),
                            ];
                        }
                    }
                
            }

            if(isset($reqdata7))
            { 
                $wrapped_links7["ADDITIONAL"] = $reqdata7;
                $ADDITIONALMAT = ArrayToXml::convert($wrapped_links7);
            }
            else
            {
                $ADDITIONALMAT = NULL; 
            }   

            // By Additional Material Section ends here. 

        for ($i=0; $i<=$r_count5; $i++){
            if(isset($request['Componentid_'.$i]) && !is_null($request['Componentid_'.$i]))
            {
                if(isset($request['Componentid_'.$i]))
                {
                    $reqdata5[$i] = [
                        
                        'CCOMPONENTID_REF'    => $request['Componentid_'.$i],
                        'VALUE'         => (!empty($request['value_'.$i]) ? $request['value_'.$i] : 0),
                    
                    ];
                }
            }
        }
     
        if(isset($reqdata5)){ 
            $wrapped_links5["OTH"] = $reqdata5;
            $XMLOTH = ArrayToXml::convert($wrapped_links5);
        }
        else{
            $XMLOTH = NULL; 
        } 

        
        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $log_data = [ 
            $PNM_NO,$PNM_DT,$PROID_REF,$MOVEMENT_STAGE,$PSTAGEID_REF,
            $TO_STAGE_STORE_QC,$ITEMID_REF,$UOMID_REF,$PNM_QTY,$CYID_REF,
            $BRID_REF,$FYID_REF,$VTID_REF,$XMLCONSUME,$XMLMACHINE,
            $XMLUDF,$USERID, Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,
            $IPADDRESS,$SOID_REF,$SQID_REF,$SEID_REF,$TOTAL_COST,$XMLOTH,$ACTUAL_QTY,$PARTIAL_PRODUCTION,$BYPRODUCTMAT,$ADDITIONALMAT
        ]; 

        //dd($log_data); 
   
       
        $sp_result = DB::select('EXEC SP_PNM_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?', $log_data);  
        //dd($sp_result); 
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => 'Record successfully Updated.']);

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

            $objRights  =   DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();
			
            $objlastdt          =   $this->getLastdt();
            $objPRO             =   $this->getPROList();
            $objStage           =   $this->getStageList();
            $objMACHINE         =   $this->getMachineList();
            $objSHIFT           =   $this->getShiftList();
            $objEMP             =   $this->getOperatorList();
    
            $objResponse    =   DB::table('TBL_TRN_PDPNM_HDR AS T1')
            ->leftJoin('TBL_TRN_PDPRO_HDR AS T2', 'T1.PROID_REF','=','T2.PROID')
            ->leftJoin('TBL_MST_ITEM AS T3', 'T1.ITEMID_REF','=','T3.ITEMID')
            ->leftJoin('TBL_MST_UOM AS T4', 'T1.UOMID_REF','=','T4.UOMID')
            ->leftJoin('TBL_MST_PRODUCTIONSTAGES AS T5', 'T1.PSTAGEID_REF','=','T5.PSTAGEID')
            ->leftJoin('TBL_MST_PRODUCTIONSTAGES AS T6', 'T1.TO_STAGE_STORE_QC','=','T6.PSTAGEID')
            ->leftJoin('TBL_MST_STORE AS T7', 'T1.TO_STAGE_STORE_QC','=','T7.STID')
           
            ->where('T1.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('T1.BRID_REF','=',Session::get('BRID_REF'))
            ->where('T1.FYID_REF','=',Session::get('FYID_REF'))
            ->where('T1.PNMID','=',$id)
            ->select(
                'T1.*', 
                'T2.PRO_NO',
                'T3.ICODE','T3.NAME',
                'T4.UOMCODE',
                'T4.DESCRIPTIONS AS UOMDESC',
                'T5.PSTAGE_CODE AS FROM_STAGE_CODE',
                'T5.DESCRIPTIONS AS FROM_STAGE_DESC',
                'T6.PSTAGE_CODE AS TO_STAGE_CODE',
                'T6.DESCRIPTIONS AS TO_STAGE_DESC',
                'T7.STCODE AS TO_STORE_CODE',
                'T7.NAME AS TO_STORE_DESC'
               
                )
            ->first();

            $objMAT = DB::select("SELECT 
            T1.*,
            T2.MACHINE_NO,T2.MACHINE_DESC,
            T3.SHIFT_CODE,T3.SHIFT_NAME,
            T4.EMPCODE,T4.FNAME
			FROM TBL_TRN_PDPNM_MACHINE T1
			LEFT JOIN TBL_MST_MACHINE T2 ON T1.MACHINEID_REF=T2.MACHINEID
            LEFT JOIN TBL_MST_SHIFT T3 ON T1.SHIFTID_REF=T3.SHIFTID
            LEFT JOIN TBL_MST_EMPLOYEE T4 ON T1.EMPID_REF=T4.EMPID
            WHERE T1.PNMID_REF='$id' ORDER BY T1.PNM_MACHINEID ASC"); 


            $material_array = DB::select("SELECT 
            T1.*,
			T2.ICODE,T2.NAME AS ITEM_NAME,T2.MATERIAL_TYPE,
			T3.UOMCODE,
            T3.DESCRIPTIONS AS UOMDESC
			FROM TBL_TRN_PDPNM_CONSUME T1
			LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
			LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            WHERE T1.PNMID_REF='$id' ORDER BY T1.PNM_CONSUMEID ASC");


			$objUDF = DB::table('TBL_TRN_PDPNM_UDF')                    
            ->where('PNMID_REF','=',$id)
            ->orderBy('PNM_UDFID','ASC')
            ->get()->toArray();
            $objCount2 = count($objUDF);
			
		
			$ObjUnionUDF = DB::table("TBL_MST_UDFFOR_PNM")->select('*')
                        ->whereIn('PARENTID',function($query) use ($CYID_REF)
                                    {       
                                    $query->select('UDFPNMID')->from('TBL_MST_UDFFOR_PNM')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$CYID_REF);
                                                                     
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$CYID_REF);
                                     
                    

            $objUdfData = DB::table('TBL_MST_UDFFOR_PNM')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF)
            ->get()->toArray();   
            $objCountUDF = count($objUdfData);        

            $ObjUnionUDF2 = DB::table("TBL_MST_UDFFOR_PNM")->select('*')
            ->whereIn('PARENTID',function($query) use ($CYID_REF)
                        {       
                        $query->select('UDFPNMID')->from('TBL_MST_UDFFOR_PNM')
                                        ->where('PARENTID','=',0)
                                        ->where('DEACTIVATED','=',0)
                                        ->where('CYID_REF','=',$CYID_REF);
                                                         
            })->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF);
                           
            

            $objUdfData2 = DB::table('TBL_MST_UDFFOR_PNM')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
            ->union($ObjUnionUDF2)
            ->get()->toArray(); 

            $component_list = DB::table('TBL_MST_COSTCOMPONENT')
                             ->where('STATUS','=',$Status)
                             ->where('CYID_REF','=',Auth::user()->CYID_REF)
                             ->select('TBL_MST_COSTCOMPONENT.*')
                             ->get();

            $objOTH = DB::table('TBL_TRN_PDPNM_OTH')                    
            ->where('TBL_TRN_PDPNM_OTH.PNMID_REF','=',$id)
            ->leftJoin('TBL_MST_COSTCOMPONENT', 'TBL_TRN_PDPNM_OTH.CCOMPONENTID_REF','=','TBL_MST_COSTCOMPONENT.CCOMPONENTID')     
            ->select('TBL_TRN_PDPNM_OTH.*','TBL_MST_COSTCOMPONENT.*')
            ->orderBy('TBL_TRN_PDPNM_OTH.PNM_OTHID','ASC')
            ->get()->toArray();
    
           

		
            $FormId         =   $this->form_id;
            $AlpsStatus =   $this->AlpsStatus();
            $ActionStatus   =   "disabled";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
			
			$objTOLERENCE = DB::table('TBL_MST_ADDL_TAB_SETTING')
			->where('TABLE_NAME','=','PNM')
			->where('TAB_NAME','=','PNM TOLERENCE')
			->select('TBL_MST_ADDL_TAB_SETTING.FIELD1')
			->first();

            $objUOM      =   $this->GetUOM();

            return view($this->view.$FormId.'view',compact([
                'AlpsStatus',
				'FormId',
                'objRights',
                'objPRO',
                'objStage',
                'objMACHINE',
                'objSHIFT',
                'objEMP',
				'objlastdt',
				'objResponse',
				'objMAT',
				'material_array',
				'objUDF',
                'objUdfData',
                'objCountUDF',
                'objUdfData2',
                'component_list',
                'objOTH',
                'ActionStatus',
                'TabSetting','objTOLERENCE',
                'objUOM'
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

        $PNM_NO             =   $request['PNM_NO'];
        $PNM_DT             =   $request['PNM_DT'];
        $PROID_REF          =   $request['PROID_REF'];
        $ITEMID_REF         =   $request['ITEMID_REF'];
        $UOMID_REF          =   $request['UOMID_REF'];
        $PNM_QTY            =   $request['PNM_QTY'];
        $SOID_REF           =   $request['SOID_REF'];
        $SQID_REF           =   $request['SQID_REF'];
        $SEID_REF           =   $request['SEID_REF'];
        $MOVEMENT_STAGE     =   $request['MOVEMENT_STAGE'];
        $PSTAGEID_REF       =   $request['PSTAGEID_REF'];
        $TO_STAGE_STORE_QC  =   $request['TO_STAGE_STORE_QC'];
        $TOTAL_COST         =   $request['TOTAL_COST'];
		$ACTUAL_QTY         =   $request['ACTUAL_QTY'];
		$PARTIAL_PRODUCTION = 	(isset($request['PARTIAL_PRODUCTION'])!="true" ? 0 : 1);
   
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count4 = $request['Row_Count4'];
        $r_count5 = $request['Row_Count5'];
        $r_count6 = $request['Row_Count6'];
        $r_count7 = $request['Row_Count7'];
        
		$req_data=array();
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['MACHINEID_REF_'.$i])){

                $req_data[$i] = [
                    'ITEMID_REF'    =>  $ITEMID_REF,
                    'UOMID_REF'     =>  $UOMID_REF,
                    'MACHINEID_REF' =>  (!empty($request['MACHINEID_REF_'.$i])) == 'true' ? $request['MACHINEID_REF_'.$i] : NULL,
                    'SHIFTID_REF'   =>  (!empty($request['SHIFTID_REF_'.$i])) == 'true' ? $request['SHIFTID_REF_'.$i] : NULL,
                    'EMPID_REF'     =>  (!empty($request['EMPID_REF_'.$i])) == 'true' ? $request['EMPID_REF_'.$i] : NULL,
                    'BATCH'         =>  $request['BATCH_'.$i],
                    'QTY'      =>  $request['QTY_'.$i],
                    'PROID_REF'     =>  $PROID_REF,
                    'SOID_REF'     =>   $SOID_REF,
                    'SQID_REF'     =>   $SQID_REF,  
                    'SEID_REF'     =>   $SEID_REF,  
                ];

            }
        }


        $wrapped_links["MACHINE"] = $req_data; 
        $XMLMACHINE = ArrayToXml::convert($wrapped_links);
		
		$reqdata3=array();
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['UDF_'.$i])){
                $reqdata3[$i] = [
                    'UDFPNMID_REF'      => $request['UDF_'.$i],
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

		
		$req_data4=array();
        for ($i=0; $i<=$r_count4; $i++){
            if(isset($request['REQ_ITEMID_REF_'.$i])){

                $req_data4[$i] = [
                    'ITEMID_REF'      	    => $request['REQ_ITEMID_REF_'.$i],
                    'UOMID_REF'   	        => $request['REQ_UOMID_REF_'.$i],
                    'ITEM_DESCRIPTION'      => $request['REQ_ITEM_DESCRIPTION_'.$i],
                    'CONSUME_QTY'           => $request['REQ_CONSUME_QTY_'.$i],
                    'BATCH'     		    => $request['REQ_LOT_NO_'.$i],
                    'CHANGE_IN_CONSUME_QTY'	=> $request['REQ_CHANGE_IN_CONSUME_QTY_'.$i],
					'PROID_REF'             => $PROID_REF,
                    'SOID_REF'     		    => $SOID_REF,
                    'SQID_REF'     		    => $SQID_REF,  
                    'SEID_REF'     		    => $SEID_REF,
                    'RATE'     		        => $request['RATE_'.$i],
                    'WASTAGE_QTY'     		=> (!empty($request['WASTAGE_QTY_'.$i]) ? $request['WASTAGE_QTY_'.$i] : 0),
                ];

            }
        }

		if($r_count4 > 0){
            $wrapped_links4["CONSUME"] = $req_data4; 
			$XMLCONSUME = ArrayToXml::convert($wrapped_links4);
        }
        else{
            $XMLCONSUME=NULL;
        }	
        
        
        for ($i=0; $i<=$r_count5; $i++){
            if(isset($request['Componentid_'.$i]) && !is_null($request['Componentid_'.$i]))
            {
                if(isset($request['Componentid_'.$i]))
                {
                    $reqdata5[$i] = [
                        
                        'CCOMPONENTID_REF'    => $request['Componentid_'.$i],
                        'VALUE'         => (!empty($request['value_'.$i]) ? $request['value_'.$i] : 0),
                    
                    ];
                }
            }
        }
     
        if(isset($reqdata5)){ 
            $wrapped_links5["OTH"] = $reqdata5;
            $XMLOTH = ArrayToXml::convert($wrapped_links5);
        }
        else{
            $XMLOTH = NULL; 
        } 



          //By product Tab starts here 

          for ($i=0; $i<=$r_count6; $i++)
          {
                  if(isset($request['MainItemId2_Ref_'.$i]) && !is_null($request['MainItemId2_Ref_'.$i]))
                  {
                      if(isset($request['MainItemId2_Ref_'.$i]))
                      {
                          $reqdata6[$i] = [                          
                              'ITEMID_REF'        => $request['MainItemId2_Ref_'.$i],
                              'UOMID_REF'         => $request['Mainuom2_Ref_'.$i],
                              'CONSUME_QTY'       =>(!empty($request['PRODUCE_QTY2_'.$i]) ? $request['PRODUCE_QTY2_'.$i] : 0),
                              'ALT_UOMID_REF'     => $request['BYPUOM_REF_'.$i],
                              'TYPE'              => $request['TYPE_'.$i],                      
                          ];
                      }
                  }
              
          }
  
          if(isset($reqdata6))
          { 
              $wrapped_links6["BYP"] = $reqdata6;
              $BYPRODUCTMAT = ArrayToXml::convert($wrapped_links6);
          }
          else
          {
              $BYPRODUCTMAT = NULL; 
          }   
  
          // By product section ends here. 
  
  
  
              //By Additional Material Tab starts here 
  
              for ($i=0; $i<=$r_count7; $i++)
              {
                      if(isset($request['AD_ITEMID_REF_'.$i]) && !is_null($request['AD_ITEMID_REF_'.$i]))
                      {
                          if(isset($request['AD_ITEMID_REF_'.$i]))
                          {
                              $reqdata7[$i] = [                          
                                  'ITEMID_REF'        => $request['AD_ITEMID_REF_'.$i],
                                  'UOMID_REF'         => $request['AD_UOMID_REF_'.$i],
                                  'ISSUED_QTY'       =>(!empty($request['ISSUED_QTY_'.$i]) ? $request['ISSUED_QTY_'.$i] : 0),
                                  'CONSUME_QTY'       =>(!empty($request['ADCONSUME_QTY_'.$i]) ? $request['ADCONSUME_QTY_'.$i] : 0),
                                  'WASTAGE_QTY'       =>(!empty($request['ADWASTAGE_QTY_'.$i]) ? $request['ADWASTAGE_QTY_'.$i] : 0),
                                
                                ];
                          }
                      }
                  
              }
  
              if(isset($reqdata7))
              { 
                  $wrapped_links7["ADDITIONAL"] = $reqdata7;
                  $ADDITIONALMAT = ArrayToXml::convert($wrapped_links7);
              }
              else
              {
                  $ADDITIONALMAT = NULL; 
              }   
  
              // By Additional Material Section ends here. 


        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $log_data = [ 
            $PNM_NO,$PNM_DT,$PROID_REF,$MOVEMENT_STAGE,$PSTAGEID_REF,
            $TO_STAGE_STORE_QC,$ITEMID_REF,$UOMID_REF,$PNM_QTY,$CYID_REF,
            $BRID_REF,$FYID_REF,$VTID_REF,$XMLCONSUME,$XMLMACHINE,
            $XMLUDF,$USERID, Date('Y-m-d'),Date('h:i:s.u'),$ACTIONNAME,
            $IPADDRESS,$SOID_REF,$SQID_REF,$SEID_REF,$TOTAL_COST,$XMLOTH,$ACTUAL_QTY,$PARTIAL_PRODUCTION,$BYPRODUCTMAT,$ADDITIONALMAT
        ]; 
   
       
        $sp_result = DB::select('EXEC SP_PNM_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?', $log_data);  

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' =>'Record successfully Approved.']);
          
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
        $TABLE      =   "TBL_TRN_PDPNM_HDR";
        $FIELD      =   "PNMID";
        $ACTIONNAME     = $Approvallevel;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
            
        $log_data = [ 
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_PNM ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
        
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
        $TABLE      =   "TBL_TRN_PDPNM_HDR";
        $FIELD      =   "PNMID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
		
        $req_data[0]=[
         'NT'  => 'TBL_TRN_PDPNM_MACHINE',
        ];
        $req_data[1]=[
        'NT'  => 'TBL_TRN_PDPNM_CONSUME',
        ];
		$req_data[2]=[
        'NT'  => 'TBL_TRN_PDPNM_UDF',
        ];

        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_PNM  ?,?,?,?, ?,?,?,?, ?,?,?,?', $cancel_data);

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

            $objResponse = DB::table('TBL_TRN_PDPNM_HDR')->where('PNMID','=',$id)->first();

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
        
		//$destinationPath = storage_path()."/docs/company".$CYID_REF."/ProductionMovement";
        $image_path         =   "docs/company".$CYID_REF."/ProductionMovement";     
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

        $PNM_NO  =   trim($request['PNM_NO']);
        $objLabel = DB::table('TBL_TRN_PDPNM_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('PNM_NO','=',$PNM_NO)
        ->select('PNM_NO')->first();

        if($objLabel){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }

    public function getLastdt(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        return  DB::select('SELECT MAX(PNM_DT) PNM_DT FROM TBL_TRN_PDPNM_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
    }


    public function getItemDetails(Request $request){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $Status     =   $request['status'];
        $PROID_REF  =   $request['PROID_REF'];
        $StdCost    =   0;
        $AlpsStatus =   $this->AlpsStatus();

        $Direct     =   DB::table('TBL_TRN_PDPRO_HDR')
                        ->where('STATUS','=','A')
                        ->where('PROID','=',$PROID_REF)
                        ->select('DIRECTPO')
                        ->first()->DIRECTPO;

        if($Direct !=1){
            $ObjItem    =   DB::select("SELECT 
                            T1.ITEMID_REF,T1.UOMID_REF AS MAIN_UOMID_REF,T1.SOQTY AS SO_QTY,T1.SOID_REF,T1.SEID_REF,T1.SQID_REF,
                            T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI,T2.MATERIAL_TYPE,
                            T3.UOMCODE,T3.DESCRIPTIONS
                            FROM TBL_TRN_PDPRO_MAT T1
                            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                            WHERE T1.PROID_REF='$PROID_REF' ORDER BY T1.PROID_REF ASC");
        }
        else{
            $ObjItem    =   DB::select("SELECT 
                            T1.ITEMID_REF,T1.UOMID_REF AS MAIN_UOMID_REF,T1.PD_OR_QTY AS SO_QTY,T1.SOID_REF,T1.SEID_REF,T1.SQID_REF,
                            T2.ITEMID,T2.ICODE,T2.NAME,T2.ITEMGID_REF,T2.ICID_REF,T2.ITEM_SPECI,T2.MATERIAL_TYPE,
                            T3.UOMCODE,T3.DESCRIPTIONS
                            FROM TBL_TRN_PDPRO_MAT T1
                            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                            WHERE T1.PROID_REF='$PROID_REF' ORDER BY T1.PROID_REF ASC");
        }

        if(!empty($ObjItem)){

            foreach ($ObjItem as $index=>$dataRow){

                $FROMQTY      =  isset($dataRow->SO_QTY)? $dataRow->SO_QTY : 0;   
               
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

                $MATERIAL_TYPE      =   $dataRow->ICODE !=""?' ('.$dataRow->MATERIAL_TYPE.')':'';
              
                $row = '';

                $row = $row.'
                <tr id="item_'.$index.'"  class="clsitemid">
                    <td style="width:8%;text-align:center;"><input type="checkbox" id="chkId'.$index.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>
                    <td style="width:10%;">'.$dataRow->ICODE.'</td>
                    <td style="width:10%;">'.$dataRow->NAME.$MATERIAL_TYPE.'</td>
                    <td style="width:8%;">'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>
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
                            data-desc5="'.$ObjMainUOM[0]->DESCRIPTIONS.'" 
                            data-desc6="'.$FROMQTY.'" 
                            data-desc7="'.$dataRow->SOID_REF.'"
                            data-desc8="'.$dataRow->SEID_REF.'"
                            data-desc9="'.$dataRow->SQID_REF.'"
                           
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

    public function getSOCodeNo(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $TYPE           =   $request['TYPE'];

        if($TYPE =="To Stage"){

            $ObjData    =   DB::table('TBL_MST_PRODUCTIONSTAGES')
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('BRID_REF','=',$BRID_REF)
                            
                            ->where('STATUS','=','A')
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('PSTAGEID AS ID','PSTAGE_CODE AS CODE','DESCRIPTIONS AS DESC')
                            ->get();
        }
        else if($TYPE =="To QC"){

            $ObjData    =   DB::table('TBL_MST_PRODUCTIONSTAGES')
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('BRID_REF','=',$BRID_REF)
                          
                            ->where('STATUS','=','A')
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('PSTAGEID AS ID','PSTAGE_CODE AS CODE','DESCRIPTIONS AS DESC')
                            ->get();

        }
        else{

            $ObjData    =   DB::table('TBL_MST_STORE')
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('BRID_REF','=',$BRID_REF)
                           
                            ->where('STATUS','=','A')
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('STID AS ID','STCODE AS CODE','NAME AS DESC')
                            ->get();
        }

        



        $row = '';

        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){

                $row .= '<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_TO_STAGE_STORE_QC[]" id="socode_'.$dataRow->ID .'"  class="clssSOid" value="'.$dataRow->ID.'" ></td>
                <td class="ROW2">'.$dataRow->CODE;
                $row .= '<input type="hidden" id="txtsocode_'.$dataRow->ID.'" data-desc="'.$dataRow->CODE.' - '.$dataRow->DESC.'" value="'.$dataRow->ID.'"/></td>
                <td class="ROW3">'.$dataRow->DESC.'</td></tr>'; 
                                   
            }

        }else{
            $row .= '<tr><td colspan="2">Record not found.</td></tr>';
        }

        echo $row;

        exit(); 
    }
    
    public function get_materital_item(Request $request){

       
        
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $item_array     =   $request['item_array'];

        $material_array=array();
        foreach($item_array as $key=>$val){

            $exp        =   explode("_",$val);
            $PROID_REF  =   $exp[0];
            $SOID       =   $exp[1];
            $ITEMID     =   $exp[2];
            $ITEMCODE   =   $exp[3];
            $PD_OR_QTY  =   $exp[4];
            $SQID       =   $exp[5];
            $SEID       =   $exp[6];

            $WHERE_SOID =   $SOID !=""?" AND T1.SOID_REF='$SOID'":"";
            $WHERE_SEID =   $SEID !=""?" AND T1.SEID_REF='$SEID'":"";;
            $WHERE_SQID =   $SQID !=""?" AND T1.SQID_REF='$SQID'":"";;

            $BOM_MAT    =   DB::select("SELECT 
                            T1.ITEMID_REF,T1.BOM_QTY,
                            T2.ICODE,T2.NAME,
                            T3.UOMID,T3.UOMCODE,T3.DESCRIPTIONS AS UOMDESC
                            FROM TBL_TRN_PDPRO_REQ T1 
                            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                            LEFT JOIN TBL_MST_UOM T3 ON T2.MAIN_UOMID_REF=T3.UOMID
                            WHERE T1.PROID_REF='$PROID_REF'  AND T1.SOITEMID_REF='$ITEMID' $WHERE_SOID  $WHERE_SEID  $WHERE_SQID
                            ");

            

            if(!empty($BOM_MAT)){
                $BOM_MAT=$BOM_MAT;
            }
            else{
                $BOM_MAT    =   DB::select("SELECT 
                T1.ITEMID_REF,T1.BOM_QTY,
                T2.ICODE,T2.NAME,
                T3.UOMID,T3.UOMCODE,T3.DESCRIPTIONS AS UOMDESC
                FROM TBL_TRN_PDPRO_REQ T1 
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_UOM T3 ON T2.MAIN_UOMID_REF=T3.UOMID
                WHERE T1.PROID_REF='$PROID_REF'  AND T1.ITEMID_REF='$ITEMID' $WHERE_SOID  $WHERE_SEID  $WHERE_SQID
                ");
            }

            if(isset($BOM_MAT) && !empty($BOM_MAT)){
                foreach($BOM_MAT as $row){

                    $RATE_ARR   =   DB::select("SELECT TOP 1 RATE FROM TBL_MST_BATCH WHERE ITEMID_REF='$row->ITEMID_REF' AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF'");
                    $RATE       =   isset($RATE_ARR) && !empty($RATE_ARR)?$RATE_ARR[0]->RATE:'0.00000';

                    $material_array[]=array(
                        'BOM_MATID'=>'',
                        'BOMID_REF'=>'',
                        'ITEMID_REF'=>$row->ITEMID_REF,
                        'CONSUME_QTY'=>($row->BOM_QTY*$PD_OR_QTY),
                        'ICODE'=>$row->ICODE,
                        'NAME'=>$row->NAME,
                        'UOMID'=>$row->UOMID,
                        'UOMCODE'=>$row->UOMCODE,
                        'UOMDESC'=>$row->UOMDESC,
                        'MAIN_SLID'=>'',
                        'MAIN_SOID'=>$SOID,
                        'MAIN_ITEMID'=>$ITEMID,
                        'MAIN_ITEMCODE'=>$ITEMCODE,
                        'MAIN_PD_OR_QTY'=>$PD_OR_QTY,
                        'MAIN_SQID'=>$SQID,
                        'MAIN_SEID'=>$SEID,
                        'MAIN_ITEM_ROWID'=>'' ,
                        'RATE'=>$RATE,
                    );
                }
            } 
        }
       
        foreach($material_array as $val){

            $MAIN_SOID   =   $val['MAIN_SOID'];
            $ITEMID_REF  =   $val['ITEMID_REF'];

            $BOM_MAT    =   DB::select("SELECT 
                            T1.ITEMID_REF,T1.BOM_QTY,
                            T2.ICODE,T2.NAME,
                            T3.UOMID,T3.UOMCODE,T3.DESCRIPTIONS AS UOMDESC
                            FROM TBL_TRN_PDPRO_REQ T1 
                            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                            LEFT JOIN TBL_MST_UOM T3 ON T2.MAIN_UOMID_REF=T3.UOMID
                            WHERE T1.PROID_REF='$PROID_REF' AND T1.SOID_REF='$MAIN_SOID' AND T1.SOITEMID_REF='$ITEMID_REF' 
                            
                            ");
            
            if(!empty($BOM_MAT)){

                foreach($BOM_MAT as $row){

                    $RATE_ARR   =   DB::select("SELECT TOP 1 RATE FROM TBL_MST_BATCH WHERE ITEMID_REF='$row->ITEMID_REF' AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF'");
                    $RATE       =   isset($RATE_ARR) && !empty($RATE_ARR)?$RATE_ARR[0]->RATE:'0.00000';

                    $material_array[]=array(
                        'BOM_MATID'=>'',
                        'BOMID_REF'=>'',
                        'ITEMID_REF'=>$row->ITEMID_REF,
                        'CONSUME_QTY'=>($row->BOM_QTY*$PD_OR_QTY),
                        'ICODE'=>$row->ICODE,
                        'NAME'=>$row->NAME,
                        'UOMID'=>$row->UOMID,
                        'UOMCODE'=>$row->UOMCODE,
                        'UOMDESC'=>$row->UOMDESC,
                        'MAIN_SLID'=>'',
                        'MAIN_SOID'=>$SOID,
                        'MAIN_ITEMID'=>$ITEMID,
                        'MAIN_ITEMCODE'=>$ITEMCODE,
                        'MAIN_PD_OR_QTY'=>$PD_OR_QTY,
                        'MAIN_SQID'=>$SQID,
                        'MAIN_SEID'=>$SEID,
                        'MAIN_ITEM_ROWID'=>'' ,
                        'RATE'=>$RATE,
                    );
                }
            }

        }


        if(!empty($material_array)){
            $Row_Count4 =   count($material_array);
            echo'<table id="example4" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                        <tr>
                            <th hidden ><input class="form-control" type="hidden" name="Row_Count4" id ="Row_Count4" value="'.$Row_Count4.'"></th>
                            <th>Item Code</th>
                            <th>Item Description</th>
                            <th>UOM</th>
                            <th>Issued Qty</th>
                            <th>Lot No</th>
                            <th>Rate</th>
                            <th>Actual Consumed Qty</th>
                            <th>Wastage/Scrap Qty</th>
                            <th>Balance Qty</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>';

                    foreach($material_array as $index=>$row_data){

                        $CONSUME_QTY    =   number_format($row_data['CONSUME_QTY'],3,".","");
                        $RATE           =   number_format($row_data['RATE'],5);
                       
                        echo '<tr  class="participantRow4">';
                        echo '<td><input type="text" id="txtSUBITEM_popup_'.$index.'" class="form-control"  value="'.$row_data['ICODE'].'"  readonly /></td>';
                        echo '<td hidden><input type="text" name="REQ_ITEMID_REF_'.$index.'" id="REQ_ITEMID_REF_'.$index.'" value="'.$row_data['ITEMID_REF'].'" /></td>';
                        echo '<td><input type="text" name="REQ_ITEM_DESCRIPTION_'.$index.'" id="REQ_ITEM_DESCRIPTION_'.$index.'" class="form-control" value="'.$row_data['NAME'].'" readonly /></td>';

                        echo '<td><input type="text" name="txtUOM_popup_'.$index.'"         id="txtUOM_popup_'.$index.'"   value="'.$row_data['UOMCODE'].' - '.$row_data['UOMDESC'].'" class="form-control" readonly /></td>';
                        echo '<td hidden><input type="text" name="REQ_UOMID_REF_'.$index.'" id="REQ_UOMID_REF_'.$index.'"  value="'.$row_data['UOMID'].'" class="form-control" /></td>';
                        
                        echo '<td><input type="text" name="REQ_CONSUME_QTY_'.$index.'" id="REQ_CONSUME_QTY_'.$index.'" class="form-control" autocomplete="off" value="'.$CONSUME_QTY.'"  onkeypress="return isNumberDecimalKey(event,this)" readonly  /></td>';
                        echo '<td><input type="text" name="REQ_LOT_NO_'.$index.'"   id="REQ_LOT_NO_'.$index.'" class="form-control" /></td>';
                        echo '<td><input type="text" name="RATE_'.$index.'"   id="RATE_'.$index.'" value="'.$RATE.'" class="form-control" readonly /></td>';
                        echo '<td><input type="text" name="REQ_CHANGE_IN_CONSUME_QTY_'.$index.'" id="REQ_CHANGE_IN_CONSUME_QTY_'.$index.'" class="form-control three-digits" value="'.$CONSUME_QTY.'" onkeypress="return isNumberDecimalKey(event,this)" onkeyup="calculateRateQty()" onfocusout="resetValue(this.id,this.value,3)" maxlength="13"  autocomplete="off" /></td>';
                        echo '<td><input  type="text" name="WASTAGE_QTY_'.$index.'" id="WASTAGE_QTY_'.$index.'" class="form-control three-digits" value="" onkeypress="return isNumberDecimalKey(event,this)" onkeyup="calculateRateQty()" onfocusout="resetValue(this.id,this.value,3)" maxlength="13"  autocomplete="off"  /></td>';
                        echo '<td><input  readonly type="text" name="BALANCE_QTY_'.$index.'" id="BALANCE_QTY_'.$index.'" class="form-control three-digits" value="" onkeypress="return isNumberDecimalKey(event,this)" onfocusout="resetValue(this.id,this.value,3)" maxlength="13"  autocomplete="off"  /></td>';
                       
                        echo '<td><input type="text" name="TOTAL_AMOUNT_'.$index.'"   id="TOTAL_AMOUNT_'.$index.'"  class="form-control" readonly /></td>';
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

    public function getOtherDirectCost(Request $request){
        
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $ITEMID_REF =   $request['ITEMID_REF'];
        
        $BOM_DATA   =   DB::select("SELECT TOP 1 BOMID 
                        FROM TBL_MST_BOM_HDR 
                        WHERE STATUS='A' AND ITEMID_REF='$ITEMID_REF' AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND (DEACTIVATED=0 or DEACTIVATED is null) ORDER BY BOMID DESC
                        ");
        
        $BOMID_REF  =   !empty($BOM_DATA)?$BOM_DATA[0]->BOMID:'';
        $objOTH     =   [];

        if($BOMID_REF !=""){
            $objOTH =   DB::table('TBL_MST_BOM_OTH')                    
            ->where('TBL_MST_BOM_OTH.BOMID_REF','=',$BOMID_REF)
            ->leftJoin('TBL_MST_COSTCOMPONENT', 'TBL_MST_BOM_OTH.CCOMPONENTID_REF','=','TBL_MST_COSTCOMPONENT.CCOMPONENTID')     
            ->select('TBL_MST_BOM_OTH.*','TBL_MST_COSTCOMPONENT.*')
            ->orderBy('TBL_MST_BOM_OTH.BOM_OTHID','ASC')
            ->get()->toArray();
        }

        $Row_Count5 =   !empty($objOTH)?count($objOTH):'1';

        echo'<table id="example6" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                <thead id="thead5"  style="position: sticky;top: 0">
                    <tr>
                        <th hidden ><input class="form-control" type="hidden" name="Row_Count5" id ="Row_Count5" value="'.$Row_Count5.'"></th>
                        <th>Cost Component(s)</th>
                        <th>Value</th>                            
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>';

                if(!empty($objOTH)){
                    foreach($objOTH as $key => $row){

                        echo'<tr class="participantRow10">
                                <td hidden><input  class="form-control" type="hidden" name="BOM_OTHID_'.$key.'" id ="BOM_OTHID_'.$key.'" maxlength="100" value="'.$row->BOM_OTHID.'" autocomplete="off"></td>
                                <td><input  type="text" name="Componentname_'.$key.'" id="Componentname_'.$key.'" value="'.$row->CCOMPONENT_CODE.'-'.$row->DESCRIPTIONS.'"   onclick="get_item_component(this.id)" class="form-control"  autocomplete="off"  readonly/></td>                                                
                                <td hidden><input type="hidden" name="Componentid_'.$key.'" id="Componentid_'.$key.'" maxlength="100" value="'.$row->CCOMPONENTID_REF.'"   class="form-control" autocomplete="off" /><input type="text" name="rowscount5[]"  /></td>
                                <td><input  type="text" name="value_'.$key.'" id="value_'.$key.'" maxlength="100" value="'.$row->VALUE.'"  class="form-control"  autocomplete="off" onkeyup="calculateRateQty()" onfocusout="resetValue(this.id,this.value,2)"  /></td>
                                <td align="center" ><button  class="btn add dcmaterial" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button  class="btn remove dcmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                            </tr>';
                    }
                }
                else{
                    echo'<tr class="participantRow10">
                            <td hidden><input  class="form-control" type="hidden" name="BOM_OTHID_0" id ="BOM_OTHID_0" maxlength="100"  autocomplete="off"></td>
                            <td><input  type="text" name="Componentname_0" id="Componentname_0"    onclick="get_item_component(this.id)" class="form-control"  autocomplete="off"  readonly/></td>                                                
                            <td hidden><input type="hidden" name="Componentid_0" id="Componentid_0" maxlength="100"   class="form-control" autocomplete="off" /><input type="text" name="rowscount5[]"  /></td>
                            <td><input  type="text" name="value_0" id="value_0" maxlength="100"   class="form-control"  autocomplete="off" onkeyup="calculateRateQty()" onfocusout="resetValue(this.id,this.value,2)"  /></td>
                            <td align="center" ><button  class="btn add dcmaterial" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button  class="btn remove dcmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                        </tr>'; 
                }

               

            echo '</tbody>';
        echo'</table>';

        exit();
    }

    public function getPROList(){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
       
        return  DB::table('TBL_TRN_PDPRO_HDR')
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->where('FYID_REF','=',$FYID_REF)
                ->where('STATUS','=','A')
                ->select('PROID','PRO_NO','PRO_DT')
                ->get(); 
    }

    
    public function getStageList(){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
       
        return  DB::table('TBL_MST_PRODUCTIONSTAGES')
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
               
                ->where('STATUS','=','A')
                ->select('PSTAGEID','PSTAGE_CODE','DESCRIPTIONS')
                ->get();
    }
    
    public function getMachineList(){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
       
        return  DB::table('TBL_MST_MACHINE')
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
               
                ->where('STATUS','=','A')
                ->select('MACHINEID AS ID','MACHINE_NO AS CODE','MACHINE_DESC AS DESC')
                ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                ->get();
    }

    public function getShiftList(){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
       
        return  DB::table('TBL_MST_SHIFT')
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
               
                ->where('STATUS','=','A')
                ->select('SHIFTID AS ID','SHIFT_CODE AS CODE','SHIFT_NAME AS DESC')
                ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                ->get(); 
    }

    public function getOperatorList(){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
       
        return  DB::table('TBL_MST_EMPLOYEE')
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
               
                ->where('STATUS','=','A')
                ->select('EMPID AS ID','EMPCODE AS CODE','FNAME AS DESC')
                ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                ->get(); 
    }



    
    public function Main_getStoreDetails(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $ROW_ID         =   $request['ROW_ID'];
        $RGP_NO         =   NULL;
        $ITEMID_REF     =   $request['ITEMID_REF'];
        $MAIN_UOMID_DES =   $request['MAIN_UOMID_DES'];
        $MAIN_UOMID_REF =   $request['MAIN_UOMID_REF'];
        $ALT_UOMID_DES  =   $request['ALT_UOMID_DES'];
        $ALT_UOMID_REF  =   $request['ALT_UOMID_REF'];
        $ITEMROWID      =   $request['ITEMROWID'];
        $ACTION_TYPE    =   $request['ACTION_TYPE'] =="VIEW"?'disabled':'';
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
                $qty                =   isset($keyid[1])?$keyid[1]:0;
                $rate               =   isset($keyid[3])?$keyid[3]:0;
                $dataArr[$batchid]  =   $qty;
                $dataArr2[$batchid] =   $rate;
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

        /*
        $objBatch =  DB::SELECT("SELECT 
        T1.BATCHID,T1.BATCH_CODE,T1.CURRENT_QTY AS TOTAL_STOCK,
        T2.STID,T2.STCODE,T2.NAME AS STNAME
        FROM TBL_MST_BATCH T1 
        LEFT JOIN TBL_MST_STORE T2 ON T2.STID=T1.STID_REF
        WHERE T1.STATUS='A' AND T1.CYID_REF ='$CYID_REF' AND T1.BRID_REF ='$BRID_REF' AND T1.FYID_REF ='$FYID_REF' AND T1.ITEMID_REF ='$ITEMID_REF' AND T1.UOMID_REF ='$MAIN_UOMID_REF'
        ");
        */

        $objBatch =  DB::SELECT("SELECT '' AS BATCHID, '' as BATCH_CODE, STID,STCODE,NAME AS STNAME ,
        (SELECT SUM(CURRENT_QTY)  FROM TBL_MST_BATCH WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' 
        AND STATUS='A' AND STID_REF=STID AND ITEMID_REF='$ITEMID_REF') AS TOTAL_STOCK
        FROM TBL_MST_STORE WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' 
        AND STATUS='A' AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 )
        ");

        echo '<thead>';
        echo '<tr>';
        echo '<th style="width:25%;">Store</th>';
        echo '<th style="width:15%;">Main UoM (MU)</th>';
        echo '<th style="width:15%;">Stock-in-hand</th>';
        echo '<th style="width:15%;">Wastage Reusable Qty</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';


        foreach($objBatch as $key=>$val){

            $BATCHID        =   $val->BATCHID;
            $TOTAL_STOCK    =   $val->TOTAL_STOCK !=""?$val->TOTAL_STOCK:0;
            $StoreRowId     =   $val->STID.'#'.$RGP_NO.'#'.$BATCHID.'#'.$ITEMID_REF.'#'.$MAIN_UOMID_REF;
            $qtyvalue       =   array_key_exists($StoreRowId, $dataArr)?$dataArr[$StoreRowId]:0;
            $CURRENT_QTY    =   floatval($TOTAL_STOCK);
            $MainReceivedQty=   $qtyvalue > 0?$qtyvalue:'';

            $qtyrate        =   array_key_exists($StoreRowId, $dataArr2)?$dataArr2[$StoreRowId]:0;
            $st_amount      =   $qtyvalue > 0?number_format($qtyvalue*$qtyrate, 2, '.', ''):'';
            $qtyrate        =   $qtyrate > 0?number_format($qtyrate, 2, '.', ''):'';

            
            if($ITEMID_REF !=''){
                $rateFlag       =   array_key_exists($StoreRowId, $dataArr2)?true:false;
                if($rateFlag !=true){

                    $RATE =  DB::SELECT("SELECT TOP 1 RATE FROM (
                    SELECT  A.MIS_DT AS ENTRYDATE,ISNULL(B.RATE,0)  AS RATE         
                    FROM TBL_TRN_MISS01_MULTISTORE S  INNER JOIN TBL_TRN_MISS01_HDR A ON S.MISID_REF = A.MISID             
                    LEFT JOIN TBL_MST_BATCH B ON S.ITEMID_REF=B.ITEMID_REF AND S.BATCH_CODE=B.BATCHID              
                    WHERE S.ITEMID_REF='$ITEMID_REF' AND B.CYID_REF='$CYID_REF' AND B.BRID_REF='$BRID_REF'               
                    UNION
                    SELECT  A.MISP_DT AS ENTRYDATE,ISNULL(B.RATE,0)  AS RATE         
                    FROM TBL_TRN_MISP01_MULTISTORE S  INNER JOIN TBL_TRN_MISP01_HDR A ON S.MISPID_REF = A.MISPID             
                    LEFT JOIN TBL_MST_BATCH B ON S.ITEMID_REF=B.ITEMID_REF AND S.BATCH_CODE=B.BATCHID              
                    WHERE S.ITEMID_REF='$ITEMID_REF' AND B.CYID_REF='$CYID_REF' AND B.BRID_REF='$BRID_REF'  
                    UNION
                    SELECT  A.MISRDT AS ENTRYDATE,ISNULL(B.RATE,0)    AS RATE       
                    FROM TBL_TRN_PDMISR_STORE S  INNER JOIN TBL_TRN_PDMISR_HDR A ON S.MISRID_REF = A.MISRID             
                    LEFT JOIN TBL_MST_BATCH B ON S.ITEMID_REF=B.ITEMID_REF AND S.BATCHID_REF=B.BATCHID              
                    WHERE S.ITEMID_REF='$ITEMID_REF' AND B.CYID_REF='$CYID_REF' AND B.BRID_REF='$BRID_REF'  
                    ) AS S ORDER BY S.ENTRYDATE DESC
                    ");

                    if(isset($RATE[0]->RATE) && $RATE[0]->RATE !='' && $RATE[0]->RATE > 0){
                        $qtyrate        =   number_format($RATE[0]->RATE, 2, '.', '');
                    }
                    else{
                        $qtyrate        =   number_format(0.00, '.', '');
                    }
                }
            }
            

            echo '<tr  class="Main_participantRow333">';
            
            echo '<td style="width:25%">'.$val->STCODE.' - '.$val->STNAME.'</td>';
            echo '<td style="width:15%">'.$MAIN_UOMID_DES.'</td>';
            echo '<td style="width:15%">'.$CURRENT_QTY.'</td>';
            echo '<td style="width:15%"><input '.$ACTION_TYPE.' type="text" name="UserQty_'.$key.'" id="UserQty_'.$key.'"  value="'.$MainReceivedQty.'" onkeyup="Main_checkStoreQty('.$ROW_ID.','.$ITEMID_REF.',this.value,'.$key.','.$CURRENT_QTY.')" class="qtytext"  onkeypress="return isNumberDecimalKey(event,this)" autocomplete="off"  ></td>';
            echo '<td style="width:15%"><input '.$ACTION_TYPE.' type="text" name="ST_RATE_'.$key.'" id="ST_RATE_'.$key.'"  value="'.$qtyrate.'" class="qtytext" onkeyup="getStoreAmount('.$key.')"  onkeypress="return isNumberDecimalKey(event,this)" autocomplete="off" ></td>';
            echo '<td style="width:15%"><input '.$ACTION_TYPE.' type="text" name="ST_AMOUNT_'.$key.'" id="ST_AMOUNT_'.$key.'" value="'.$st_amount.'"  class="qtytext"  onkeypress="return isNumberDecimalKey(event,this)" autocomplete="off" readonly ></td>';

            echo '<td hidden><input type="text" id="'.$key.'" value="'.$ROW_ID.'" ></td>';
            echo '<td hidden style="width:15%"><input '.$ACTION_TYPE.' type="text" class="qtytext" name="STORE_NAME_'.$key.'" id="STORE_NAME_'.$key.'"  value="'.$val->STNAME.'"  readonly  autocomplete="off"  ></td>';
            echo '<td hidden><input type="hidden" name="BATCHID_'.$key.'" id="BATCHID_'.$key.'" value="'.$StoreRowId.'" class="qtytext" ></td>';
            echo '<td hidden><input type="hidden" name="ROWID_'.$key.'" id="ROWID_'.$key.'" value="'.$ROW_ID.'" class="qtytext" ></td>';
            echo '<td hidden><input type="hidden" name="TOTAL_STOCK_'.$key.'" id="TOTAL_STOCK_'.$key.'" value="'.$CURRENT_QTY.'" class="qtytext" ></td>';
            echo '<td hidden><input type="hidden" name="BATCHNOA_'.$key.'" id="BATCHNOA_'.$key.'" value="'.$BATCHNOA.'" class="qtytext" ></td>';
            echo '</tr>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        exit();
    }

    



   


    public function GetByProductItems(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $ITEMID_REF     =   $request['ITEMID_REF'];
        $PNMID          =   $request['PNMID'];
        $TYPE           =   $request['TYPE'];
        $ACTION_TYPE    =   isset($request['ACTION_TYPE']) ? $request['ACTION_TYPE'] :"";
        $AlpsStatus     =   $this->AlpsStatus();
        $TabSetting	    =	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        $COLUMN1        =   isset($TabSetting->FIELD8) ? $TabSetting->FIELD8 : ""; 
        $COLUMN2        =   isset($TabSetting->FIELD9) ? $TabSetting->FIELD9 : ""; 
        $COLUMN3        =   isset($TabSetting->FIELD10) ? $TabSetting->FIELD10 : ""; 
        $objBOM = DB::table('TBL_MST_BOM_HDR')
                          //   ->where('TBL_MST_BOM_HDR.FYID_REF','=',Session::get('FYID_REF'))
                             ->where('TBL_MST_BOM_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_BOM_HDR.BRID_REF','=',Session::get('BRID_REF'))                             
                             ->where('TBL_MST_BOM_HDR.ITEMID_REF','=',$ITEMID_REF)
                             ->select('TBL_MST_BOM_HDR.BOMID')
                             ->first();

        if(isset($objBOM->BOMID)){       
     
            if(isset($TYPE) && $TYPE=='EDIT'){
            
            $material_array =    DB::table('TBL_TRN_PDPNM_BYPRODUCT')                    
                                ->where('TBL_TRN_PDPNM_BYPRODUCT.PNMID_REF','=',$PNMID)
                                ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_PDPNM_BYPRODUCT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                                ->leftJoin('TBL_MST_UOM', 'TBL_TRN_PDPNM_BYPRODUCT.UOMID_REF','=','TBL_MST_UOM.UOMID')                           
                                ->leftJoin('TBL_MST_UOM AS ALTUOM', 'TBL_TRN_PDPNM_BYPRODUCT.ALT_UOMID_REF','=','ALTUOM.UOMID')                           
                                ->orderBy('TBL_TRN_PDPNM_BYPRODUCT.PNM_BYPRODUCTID','ASC')
                                ->select('TBL_TRN_PDPNM_BYPRODUCT.*','TBL_TRN_PDPNM_BYPRODUCT.CONSUME_QTY AS PRODUCE_QTY','TBL_MST_ITEM.*','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','ALTUOM.UOMCODE as UOMCODE1','ALTUOM.DESCRIPTIONS as DESCRIPTIONS1')
                                ->get()->toArray();

        }else{
        $material_array =    DB::table('TBL_MST_BOM_BYP')                    
                            ->where('TBL_MST_BOM_BYP.BOMID_REF','=',$objBOM->BOMID)
                            ->leftJoin('TBL_MST_ITEM', 'TBL_MST_BOM_BYP.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                            ->leftJoin('TBL_MST_UOM', 'TBL_MST_BOM_BYP.UOMID_REF','=','TBL_MST_UOM.UOMID')                           
                            ->leftJoin('TBL_MST_UOM AS ALTUOM', 'TBL_MST_BOM_BYP.ALT_UOMID_REF','=','ALTUOM.UOMID')                           
                            ->orderBy('TBL_MST_BOM_BYP.BOM_BYPID','ASC')
                            ->select('TBL_MST_BOM_BYP.*','TBL_MST_ITEM.*','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','ALTUOM.UOMCODE as UOMCODE1','ALTUOM.DESCRIPTIONS as DESCRIPTIONS1')
                            ->get()->toArray();
        }


                           //dd($material_array);


        if(!empty($material_array)){
            $Row_Count6 =   count($material_array);
            echo'                  
                    
                    <table id="example6" class="display nowrap table table-striped table-bordered itemlist" width="60%" style="height:auto !important; "  align="center">
                    <thead id="thead3"  style="position: sticky;top: 0">
                        <tr>
                            <th>Item Code<input class="form-control" type="hidden" name="Row_Count6" id ="Row_Count6" value="'.$Row_Count6.'"></th>
                            <th>Item Description</th>                                 
                            <th>Part NO</th>                                 
                            <th '.$AlpsStatus['hidden'].'>'.$COLUMN1.'</th>                                 
                            <th '.$AlpsStatus['hidden'].'>'.$COLUMN2.'</th>                                 
                            <th '.$AlpsStatus['hidden'].'>'.$COLUMN3.'</th>                                 
                            <th>UOM</th>
                            <th>Type</th>
                            <th>Consume Qty</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody > ';

                    foreach($material_array as $index=>$row_data){
                        $Scrap  =   ""; 
                        $Reusable  =   ""; 
                        $Other  =   ""; 
                         if($row_data->TYPE=="Scrap")
                            {
                                $Scrap      = "selected"; 
                            }else if($row_data->TYPE=="Reusable")
                            {
                                $Reusable   =   "selected";    
                            }else if($row_data->TYPE=="Other"){
                                $Other   =   "selected";    
                            }

                        $alt_uom    =    isset($row_data->UOMCODE1) ? $row_data->UOMCODE1.'-'.$row_data->DESCRIPTIONS1:'';
                        echo '<tr  class="participantRow6">';              
                        echo '<td><input readonly type="text" name="MainItemCode2_'.$index.'" id="MainItemCode2_'.$index.'"   value="'.$row_data->ICODE .'"  class="form-control"  autocomplete="off"  readonly/></td> ';                                               
                        echo '<td hidden><input type="text" name="MainItemId2_Ref_'.$index.'" id="MainItemId2_Ref_'.$index.'"  value="'. $row_data->ITEMID_REF.'"   class="form-control" autocomplete="off" />
                           
                       </td>';
                            echo '<td><input  readonly type="text" name="MainItemName2_'.$index.'" id="MainItemName2_'.$index.'"  value="'. $row_data->NAME .'"  class="form-control"  autocomplete="off"  readonly/></td>';
                                                             
                           
                        echo '<td><input readonly type="text" name="MainItemPartno2_'.$index.'" id="MainItemPartno2_'.$index.'"  value="'. $row_data->PARTNO .'"  class="form-control"  autocomplete="off"  readonly/></td>'; 

                        echo '<td  hidden><input type="text" name="Mainuom2_Ref_'.$index.'" id="Mainuom2_Ref_'.$index.'"  class="form-control" value="'. $row_data->UOMID_REF .'"   autocomplete="off" /></td>'; 


                        echo '<td  ><input type="text"     class="form-control" readonly  value="'. $row_data->ALPS_PART_NO .'"   /></td>'; 
                        echo '<td  ><input type="text"     class="form-control" readonly  value="'. $row_data->CUSTOMER_PART_NO .'"   /></td>'; 
                        echo '<td  ><input type="text"     class="form-control" readonly  value="'. $row_data->OEM_PART_NO .'"   /></td>'; 


                                       
                        echo '<td  ><input type="text" name="PACKUOM_'.$index.'" id="PACKUOM_'.$index.'"  '.$ACTION_TYPE.'    class="form-control" readonly  onclick="getUOM(this.id)"  value="'.$alt_uom.'"   /></td>'; 

                        echo '<td hidden ><input type="text" name="BYPUOM_REF_'.$index.'" id="BYPUOM_REF_'.$index.'"   value="'. $row_data->ALT_UOMID_REF .'"/></td>'; 

                        echo '<td>       
                          <select name="TYPE_'.$index.'" id="TYPE_'.$index.'"  '.$ACTION_TYPE.'  class="form-control mandatory">
                          <option value="">Select</option>
                          <option value="Scrap"  '.$Scrap.' >Scrap</option>
                          <option value="Reusable" '.$Reusable.' >Reusable</option>
                          <option value="Other" '.$Other.' >Other</option>  
                          </select>  
                          </td>'; 

                        echo '<td style="width: 100px; text-align: center;" ><input  type="text" onkeyup="checkQty(this.id,this.value)" onkeypress="return isNumberDecimalKey(event,this)" name="PRODUCE_QTY2_'.$index.'" id="PRODUCE_QTY2_'.$index.'" '.$ACTION_TYPE.'   value="'.$row_data->PRODUCE_QTY .'"   class="form-control" maxlength="200" autocomplete="off"  /></td>'; 
                        echo '<td align="center" ><button readonly class="btn add " title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button readonly class="btn remove bmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>';
                        
                        echo '</tr>';
                        
                    }
                    
            echo '</tbody>';
            echo'</table>';
        }
        else{
            echo "Record not found.";
        }
    }else{
        echo "Record not found.";
    } 
        
        exit();
    }

   

    public function GetAdditionalMaterialItems(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $ITEMID_REF     =   $request['ITEMID_REF'];
        $PROID_REF      =   $request['PROID_REF'];
        $TYPE           =   $request['TYPE'];
        $PNMID          =   $request['PNMID'];
        $ACTION_TYPE    =   isset($request['ACTION_TYPE']) ? $request['ACTION_TYPE'] :"";
        
        $AlpsStatus     =   $this->AlpsStatus();
        $TabSetting	    =	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        $COLUMN1        =   isset($TabSetting->FIELD8) ? $TabSetting->FIELD8 : ""; 
        $COLUMN2        =   isset($TabSetting->FIELD9) ? $TabSetting->FIELD9 : ""; 
        $COLUMN3        =   isset($TabSetting->FIELD10) ? $TabSetting->FIELD10 : ""; 

      
        if(isset($TYPE) && $TYPE=='EDIT'){
        $material_array =     DB::select("SELECT 
        T2.ICODE,T2.NAME ,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,
        CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,T1.UOMID_REF AS MAIN_UOMID_REF,T1.ITEMID_REF,SUM(T1.ISSUED_QTY) AS ISSUED_QTY,SUM(T1.WASTAGE_QTY) AS WASTAGE_QTY,SUM(T1.CONSUME_QTY) AS CONSUME_QTY
        FROM TBL_TRN_PDPNM_ADDITIONAL T1
        LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
        LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
        WHERE T1.PNMID_REF=$PNMID  
        GROUP BY T2.ICODE,T2.NAME,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T1.ITEMID_REF,
        CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) ,T1.UOMID_REF");   

        }else{
        $material_array =     DB::select("SELECT 
        T2.ICODE,T2.NAME ,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,
        CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,T1.MAIN_UOMID_REF,T1.ITEMID_REF,SUM(T1.ISSUED_QTY) AS ISSUED_QTY,'0.00' AS WASTAGE_QTY,'0.00' AS CONSUME_QTY
        FROM TBL_TRN_MISP01_MAT T1
        LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
        LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
        LEFT JOIN TBL_TRN_MRSP01_HDR T6 ON T1.MRSPID_REF=T6.MRSPID
        WHERE T6.PROID_REF=$PROID_REF AND T6.STATUS='A' 
        GROUP BY T2.ICODE,T2.NAME,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T1.ITEMID_REF,
        CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) ,T1.MAIN_UOMID_REF");     
        }              
                      

        //dd($material_array);

        if(!empty($material_array)){
            $Row_Count7 =   count($material_array);
            echo'                  
                    
                    <table id="example7" class="display nowrap table table-striped table-bordered itemlist" width="60%" style="height:auto !important; "  align="center">
                    <thead id="thead3"  style="position: sticky;top: 0">
                        <tr>
                            <th>Item Code<input class="form-control" type="hidden" name="Row_Count7" id ="Row_Count7" value="'.$Row_Count7.'"></th>
                            <th>Item Description</th>    
                            <th>UOM</th>      
                            <th '.$AlpsStatus['hidden'].'>'.$COLUMN1.'</th>                                 
                            <th '.$AlpsStatus['hidden'].'>'.$COLUMN2.'</th>                                 
                            <th '.$AlpsStatus['hidden'].'>'.$COLUMN3.'</th>         
                            <th>Issued Qty</th>
                            <th>Consume Qty</th>
                            <th>Wastage/Scrap Qty</th>
                            <th>Balance Qty</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>';

                    foreach($material_array as $index=>$row_data){
                        //dd($row_data);                        
                        $ISSUED_QTY = $row_data->ISSUED_QTY !='' ? $row_data->ISSUED_QTY :0;
                        $CONSUME_QTY = $row_data->CONSUME_QTY !='' ? $row_data->CONSUME_QTY :0;
                        $WASTAGE_QTY = $row_data->WASTAGE_QTY !='' ? $row_data->WASTAGE_QTY :0;
                        $BALANCE_QTY     =   number_format($ISSUED_QTY-($CONSUME_QTY+$WASTAGE_QTY),3,".",""); 
                       //dd($BALANCE_QTY);

                        echo '<tr  class="participantRow7">'; 
                        echo '<td><input readonly type="text" name="AD_ICODE_'.$index.'" id="AD_ICODE_'.$index.'"   value="'.$row_data->ICODE .'"  class="form-control"  autocomplete="off"  readonly/></td> ';                                               
                        echo '<td hidden><input type="text" name="AD_ITEMID_REF_'.$index.'" id="AD_ITEMID_REF_'.$index.'"  value="'. $row_data->ITEMID_REF.'"   class="form-control" autocomplete="off" />
                           
                       </td>';
                            echo '<td><input  readonly type="text" name="AD_NAME_'.$index.'" id="AD_NAME_'.$index.'"  value="'. $row_data->NAME .'"  class="form-control"  autocomplete="off"  readonly/></td>';

                            echo '<td  ><input type="text"  name="AD_UOM_'.$index.'" id="AD_UOM_'.$index.'"    class="form-control" readonly  value="'.$row_data->MAIN_UOM_CODE.'"   /></td>'; 

                            echo '<td hidden ><input type="text" name="AD_UOMID_REF_'.$index.'" id="AD_UOMID_REF_'.$index.'"   value="'. $row_data->MAIN_UOMID_REF .'"/></td>'; 
                                                             
                           
                            echo '<td  ><input type="text"     class="form-control" readonly  value="'. $row_data->ALPS_PART_NO .'"   /></td>'; 
                            echo '<td  ><input type="text"     class="form-control" readonly  value="'. $row_data->CUSTOMER_PART_NO .'"   /></td>'; 
                            echo '<td  ><input type="text"     class="form-control" readonly  value="'. $row_data->OEM_PART_NO .'"   /></td>'; 

                            echo '<td style="width: 100px; text-align: center;" ><input  type="text" onkeypress="return isNumberDecimalKey(event,this)" name="ISSUED_QTY_'.$index.'" '.$ACTION_TYPE.'   id="ISSUED_QTY_'.$index.'" readonly value="'.$row_data->ISSUED_QTY .'"  class="form-control" maxlength="200" autocomplete="off"  /></td>'; 

                            echo '<td><input type="text" name="ADCONSUME_QTY_'.$index.'" id="ADCONSUME_QTY_'.$index.'" class="form-control three-digits" value="'.$row_data->CONSUME_QTY .'" '.$ACTION_TYPE.' onkeypress="return isNumberDecimalKey(event,this)" onkeyup="calculateRateQty_Additional()" onfocusout="resetValue(this.id,this.value,3)" maxlength="13"  autocomplete="off" /></td>';
                            echo '<td><input  type="text" name="ADWASTAGE_QTY_'.$index.'" '.$ACTION_TYPE.' id="ADWASTAGE_QTY_'.$index.'" class="form-control three-digits" value="'.$row_data->WASTAGE_QTY .'"  onkeypress="return isNumberDecimalKey(event,this)" onkeyup="calculateRateQty_Additional()" onfocusout="resetValue(this.id,this.value,3)" maxlength="13"  autocomplete="off"  /></td>';
                            echo '<td><input  readonly type="text" name="AD_BALANCE_QTY_'.$index.'" id="AD_BALANCE_QTY_'.$index.'" class="form-control three-digits" value="'.$BALANCE_QTY.'" onkeypress="return isNumberDecimalKey(event,this)" onfocusout="resetValue(this.id,this.value,3)" maxlength="13"  autocomplete="off"  /></td>';


                            echo '<td align="center" ><button readonly class="btn add " title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button readonly class="btn remove bmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>';                        
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



    public function GetUOM(){
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        return $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
        WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by UOMCODE ASC', [$CYID_REF,'A' ]);  
    }

    
}
