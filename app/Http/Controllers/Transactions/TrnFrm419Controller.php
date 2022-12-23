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

class TrnFrm419Controller extends Controller{

    protected $form_id  = 419;
    protected $vtid_ref = 493;
    protected $view     = "transactions.inventory.BarcodeOut.trnfrm";
   
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

        $objDataList	=	DB::select("select hdr.BRCOID,hdr.BRC_OUT_NO,hdr.BRC_OUT_DT,hdr.INDATE,
                            hdr.STATUS,hdr.SOURCE_TYPE,hdr.SOURCE_DOCNO,
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
                                inner join TBL_TRN_BARCODE_OUT_HDR hdr
                                on a.VID = hdr.BRCOID 
                                and a.VTID_REF = hdr.VTID_REF 
                                and a.CYID_REF = hdr.CYID_REF 
                                and a.BRID_REF = hdr.BRID_REF
                                where a.VTID_REF = '$this->vtid_ref'
                                and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                                and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                                ORDER BY hdr.BRCOID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );

                        //    dd($objDataList); 

                           // $objDataList=[];


                           


        return view($this->view.$FormId,compact(['REQUEST_DATA','objRights','objDataList','FormId']));
    }

    public function add(){       
        $Status     = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');       
        $objlastdt  =   $this->getLastdt();   
        
        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_BARCODE_OUT_HDR',
            'HDR_ID'=>'BRCOID',
            'HDR_DOC_NO'=>'BRC_OUT_NO',
            'HDR_DOC_DT'=>'BRC_OUT_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
        
        

        //dd($objDataNo);
        $FormId     =   $this->form_id;
        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
        WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by UOMCODE ASC', [$CYID_REF,'A' ]);  
        
        $CompanySpecific	=	Helper::getAddSetting(Auth::user()->CYID_REF,'BARCODE');
        $checkCompany=isset($CompanySpecific->FIELD1) && $CompanySpecific->FIELD1 =='YES' ? '':'hidden'; 


        
        return view($this->view.$FormId.'add',compact(['AlpsStatus','FormId','objlastdt','TabSetting','objUOM','checkCompany','doc_req','docarray']));       
    }

    public function save(Request $request) {

        $Main_r_count1 = $request['Main_Row_Count1'];
        $r_count5 = $request['Row_Count5'];

        for ($i=0; $i<=$Main_r_count1; $i++){
            if(isset($request['Main_ITEMID_REF_'.$i]) && $request['Main_ITEMID_REF_'.$i]!='' ){

                $req_datas[$i] = [
                    'ITEMID_REF'       => $request['Main_ITEMID_REF_'.$i],
                    'UOMID_REF'        => $request['Main_MAIN_UOMID_REF_'.$i],
                    'OUT_QTY'     => (!empty($request['Main_RECEIVED_QTY_'.$i]) ? $request['Main_RECEIVED_QTY_'.$i] : 0),
                    'ALT_QTY'          => (!empty($request['Alt_RECEIVED_QTY_'.$i]) ? $request['Alt_RECEIVED_QTY_'.$i] : 0),
                    //'SERIALNOAPL'      => isset($request['SERIAL_NO_'.$i]) ? 1:0 ,
                    //'BARCODEAPL'       => isset($request['BARCODE_'.$i]) ? 1:0 ,                   
                ];
            }
        }
        

        $wrapped_links1["MAT"] = $req_datas; 
        $XMLMAT = ArrayToXml::convert($wrapped_links1);

        
        $req_data5=array();
        for ($i=0; $i<=$r_count5; $i++){
            if(isset($request['REQ_ITEMID_REF_'.$i]) && $request['REQ_ITEMID_REF_'.$i]!=''){
    

                $req_data5[$i] = [
                    'ITEMID_REF'    	=> $request['REQ_ITEMID_REF_'.$i],
                    'UOMID_REF'    	    => $request['REQ_UOMID_REF_'.$i],
                    'UNIT_REF'    	    => $request['PACKUOMID_REF_'.$i],
                    'QTY'    	        => $request['REQ_QTY_'.$i]!='' ? $request['REQ_QTY_'.$i]:0 ,                    
                    'OUT_QTY'           => $request['REQ_RETURN_QTY_'.$i],
                    'SERIALNUMBER'      => $request['txtSR_popup_'.$i],             
                ];

            }
        }


		if($r_count5 > 0){
            $wrapped_links5["BRC"] = $req_data5; 
			$XMLBARC = ArrayToXml::convert($wrapped_links5);
        }
        else{
            $XMLBARC=NULL;
        }




        $VTID_REF       =   $this->vtid_ref;
        $VID            = 0;
        $USERID         = Auth::user()->USERID;   
        $ACTIONNAME     = 'ADD';
        $IPADDRESS      = $request->getClientIp();
        $CYID_REF       = Auth::user()->CYID_REF;
        $BRID_REF       = Session::get('BRID_REF');
        $FYID_REF       = Session::get('FYID_REF');
        $BRC_OUT_NO     = $request['BRC_OUT_NO'];
        $BRC_OUT_DT     = $request['BRC_OUT_DT'];
        $DOCTYPE        = $request['DOCTYPE'];
        $SOURCE_DOCNO   = $request['Documentpopup'];
        $DOCID_REF      = $request['DOCID_REF'];
        $REMARKS        = $request['HEADER_REMARKS'];



        
        $log_data = [ 
            $BRC_OUT_NO,$BRC_OUT_DT,$DOCTYPE,$SOURCE_DOCNO,$DOCID_REF,$REMARKS,$CYID_REF,$BRID_REF,$FYID_REF,            
            $XMLMAT,$XMLBARC,$VTID_REF,$USERID,Date('Y-m-d'),
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];  

        

        $sp_result = DB::select('EXEC SP_BARCODE_OUT ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?  ,?,?', $log_data);  

        //dd($sp_result); 
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

            $objResponse =  DB::table('TBL_TRN_BARCODE_OUT_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('BRCOID','=',$id)
            ->first();

           // dd($objResponse); 
           
            $objlastdt          =   $this->getLastdt();
      
            
            //Material Tab         
            $objMAT = DB::select("SELECT 
            T1.*,T2.ICODE,T2.NAME AS ITEM_NAME,T2.ITEM_SPECI,T3.UOMCODE,T3.DESCRIPTIONS,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO
            FROM TBL_TRN_BARCODE_OUT_MAT T1
            LEFT JOIN TBL_TRN_BARCODE_OUT_HDR H ON T1.BRCOID_REF=H.BRCOID
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            WHERE T1.BRCOID_REF='$id' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  ORDER BY T1.BRCO_MATID ASC");    
             
             //dd($objMAT); 


            $objMATDetail = DB::select("SELECT 
            T1.*,T2.ICODE,T2.NAME AS ITEM_NAME,T2.ITEM_SPECI,T3.UOMCODE,T3.DESCRIPTIONS,T4.SERIALNO_MODE,T4.SRNOA AS SERIAL_NO_APPLICABLE,T5.UOMCODE AS UOMCODE1 ,T5.DESCRIPTIONS AS DESCRIPTIONS1,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO
            ,BRCIN.PENDING_QTY,BRCIN.QTY AS INQTY
			FROM TBL_TRN_BARCODE_OUT_BRC T1
            LEFT JOIN TBL_TRN_BARCODE_OUT_HDR H ON T1.BRCOID_REF=H.BRCOID
			LEFT JOIN TBL_TRN_BARCODE_BRC BRCIN ON BRCIN.SERIALNUMBER=T1.SERIALNUMBER
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID			
            LEFT JOIN TBL_MST_ITEMCHECKFLAG T4 ON T2.ITEMID=T4.ITEMID_REF
			LEFT JOIN TBL_MST_UOM T5 ON T1.UNIT_REF=T5.UOMID
            WHERE T1.BRCOID_REF='$id' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF' ORDER BY T1.BRCO_BRCOID ASC");  

           //dd($objMATDetail); 

            $FormId         =   $this->form_id;
            $AlpsStatus     =   $this->AlpsStatus();
            $ActionStatus   =   "";

            $TabSetting	    =	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
            $CompanySpecific	=	Helper::getAddSetting(Auth::user()->CYID_REF,'BARCODE');
        $checkCompany=isset($CompanySpecific->FIELD1) && $CompanySpecific->FIELD1 =='YES' ? '':'hidden'; 
            $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                                order by UOMCODE ASC', [$CYID_REF,'A' ]);  
        
            return view($this->view.$FormId.'edit',compact(['AlpsStatus','FormId','objRights','objlastdt','objResponse','objMAT','ActionStatus','TabSetting','objMATDetail','checkCompany','objUOM']));      

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

            $objResponse =  DB::table('TBL_TRN_BARCODE_OUT_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('BRCOID','=',$id)
            ->first();

           // dd($objResponse); 
           
            $objlastdt          =   $this->getLastdt();
      
            
            //Material Tab         
            $objMAT = DB::select("SELECT 
            T1.*,T2.ICODE,T2.NAME AS ITEM_NAME,T2.ITEM_SPECI,T3.UOMCODE,T3.DESCRIPTIONS,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO
            FROM TBL_TRN_BARCODE_OUT_MAT T1
            LEFT JOIN TBL_TRN_BARCODE_OUT_HDR H ON T1.BRCOID_REF=H.BRCOID
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            WHERE T1.BRCOID_REF='$id' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  ORDER BY T1.BRCO_MATID ASC");    
             
             //dd($objMAT); 


             $objMATDetail = DB::select("SELECT 
             T1.*,T2.ICODE,T2.NAME AS ITEM_NAME,T2.ITEM_SPECI,T3.UOMCODE,T3.DESCRIPTIONS,T4.SERIALNO_MODE,T4.SRNOA AS SERIAL_NO_APPLICABLE,T5.UOMCODE AS UOMCODE1 ,T5.DESCRIPTIONS AS DESCRIPTIONS1,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO
             ,BRCIN.PENDING_QTY,BRCIN.QTY AS INQTY
             FROM TBL_TRN_BARCODE_OUT_BRC T1
             LEFT JOIN TBL_TRN_BARCODE_OUT_HDR H ON T1.BRCOID_REF=H.BRCOID
             LEFT JOIN TBL_TRN_BARCODE_BRC BRCIN ON BRCIN.SERIALNUMBER=T1.SERIALNUMBER
             LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
             LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID			
             LEFT JOIN TBL_MST_ITEMCHECKFLAG T4 ON T2.ITEMID=T4.ITEMID_REF
             LEFT JOIN TBL_MST_UOM T5 ON T1.UNIT_REF=T5.UOMID
             WHERE T1.BRCOID_REF='$id' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF' ORDER BY T1.BRCO_BRCOID ASC");  

           //dd($objMATDetail); 

            $FormId         =   $this->form_id;
            $AlpsStatus     =   $this->AlpsStatus();
            $ActionStatus   =   "disabled";

            $TabSetting	    =	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
            $CompanySpecific	=	Helper::getAddSetting(Auth::user()->CYID_REF,'BARCODE');
        $checkCompany=isset($CompanySpecific->FIELD1) && $CompanySpecific->FIELD1 =='YES' ? '':'hidden'; 
            $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
                                order by UOMCODE ASC', [$CYID_REF,'A' ]);  
        
            return view($this->view.$FormId.'view',compact(['AlpsStatus','FormId','objRights','objlastdt','objResponse','objMAT','ActionStatus','TabSetting','objMATDetail','checkCompany','objUOM']));      

        }
     
    }


    


    

    public function update(Request $request){

        $Main_r_count1 = $request['Main_Row_Count1'];
        $r_count5 = $request['Row_Count5'];

        for ($i=0; $i<=$Main_r_count1; $i++){
            if(isset($request['Main_ITEMID_REF_'.$i]) && $request['Main_ITEMID_REF_'.$i]!='' ){

                $req_datas[$i] = [
                    'ITEMID_REF'       => $request['Main_ITEMID_REF_'.$i],
                    'UOMID_REF'        => $request['Main_MAIN_UOMID_REF_'.$i],
                    'OUT_QTY'     => (!empty($request['Main_RECEIVED_QTY_'.$i]) ? $request['Main_RECEIVED_QTY_'.$i] : 0),
                    'ALT_QTY'          => (!empty($request['Alt_RECEIVED_QTY_'.$i]) ? $request['Alt_RECEIVED_QTY_'.$i] : 0),
                    //'SERIALNOAPL'      => isset($request['SERIAL_NO_'.$i]) ? 1:0 ,
                    //'BARCODEAPL'       => isset($request['BARCODE_'.$i]) ? 1:0 ,                   
                ];
            }
        }
        

        $wrapped_links1["MAT"] = $req_datas; 
        $XMLMAT = ArrayToXml::convert($wrapped_links1);

        
        $req_data5=array();
        for ($i=0; $i<=$r_count5; $i++){
            if(isset($request['REQ_ITEMID_REF_'.$i]) && $request['REQ_ITEMID_REF_'.$i]!=''){
    

                $req_data5[$i] = [
                    'ITEMID_REF'    	=> $request['REQ_ITEMID_REF_'.$i],
                    'UOMID_REF'    	    => $request['REQ_UOMID_REF_'.$i],
                    'UNIT_REF'    	    => $request['PACKUOMID_REF_'.$i],
                    'QTY'    	        => $request['REQ_QTY_'.$i]!='' ? $request['REQ_QTY_'.$i]:0 ,                    
                    'OUT_QTY'           => $request['REQ_RETURN_QTY_'.$i],
                    'SERIALNUMBER'      => $request['txtSR_popup_'.$i],             
                ];

            }
        }


		if($r_count5 > 0){
            $wrapped_links5["BRC"] = $req_data5; 
			$XMLBARC = ArrayToXml::convert($wrapped_links5);
        }
        else{
            $XMLBARC=NULL;
        }




        $VTID_REF      =   $this->vtid_ref;
        $VID           = 0;
        $USERID        = Auth::user()->USERID;   
        $ACTIONNAME    = 'EDIT';
        $IPADDRESS     = $request->getClientIp();
        $CYID_REF      = Auth::user()->CYID_REF;
        $BRID_REF      = Session::get('BRID_REF');
        $FYID_REF      = Session::get('FYID_REF');

        $BRC_OUT_NO     = $request['BRC_OUT_NO'];
        $BRC_OUT_DT     = $request['BRC_OUT_DT'];
        $DOCTYPE        = $request['DOCTYPE'];
        $SOURCE_DOCNO   = $request['Documentpopup'];
        $DOCID_REF      = $request['DOCID_REF'];
        $REMARKS        = $request['HEADER_REMARKS'];

        
        $log_data = [ 
            $BRC_OUT_NO,$BRC_OUT_DT,$DOCTYPE,$SOURCE_DOCNO,$DOCID_REF,$REMARKS,$CYID_REF,$BRID_REF,$FYID_REF,            
            $XMLMAT,$XMLBARC,$VTID_REF,$USERID,Date('Y-m-d'),
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];  

        

        $sp_result = DB::select('EXEC SP_BARCODE_OUT_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?  ,?,?', $log_data);  
        //dd($sp_result);
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $BRC_OUT_NO. ' Sucessfully Updated.']);

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
   
        $Main_r_count1 = $request['Main_Row_Count1'];
        $r_count5 = $request['Row_Count5'];

        for ($i=0; $i<=$Main_r_count1; $i++){
            if(isset($request['Main_ITEMID_REF_'.$i]) && $request['Main_ITEMID_REF_'.$i]!='' ){

                $req_datas[$i] = [
                    'ITEMID_REF'       => $request['Main_ITEMID_REF_'.$i],
                    'UOMID_REF'        => $request['Main_MAIN_UOMID_REF_'.$i],
                    'OUT_QTY'     => (!empty($request['Main_RECEIVED_QTY_'.$i]) ? $request['Main_RECEIVED_QTY_'.$i] : 0),
                    'ALT_QTY'          => (!empty($request['Alt_RECEIVED_QTY_'.$i]) ? $request['Alt_RECEIVED_QTY_'.$i] : 0),
                    //'SERIALNOAPL'      => isset($request['SERIAL_NO_'.$i]) ? 1:0 ,
                    //'BARCODEAPL'       => isset($request['BARCODE_'.$i]) ? 1:0 ,                   
                ];
            }
        }
        

        $wrapped_links1["MAT"] = $req_datas; 
        $XMLMAT = ArrayToXml::convert($wrapped_links1);

        
        $req_data5=array();
        for ($i=0; $i<=$r_count5; $i++){
            if(isset($request['REQ_ITEMID_REF_'.$i]) && $request['REQ_ITEMID_REF_'.$i]!=''){
    

                $req_data5[$i] = [
                    'ITEMID_REF'    	=> $request['REQ_ITEMID_REF_'.$i],
                    'UOMID_REF'    	    => $request['REQ_UOMID_REF_'.$i],
                    'UNIT_REF'    	    => $request['PACKUOMID_REF_'.$i],
                    'QTY'    	        => $request['REQ_QTY_'.$i]!='' ? $request['REQ_QTY_'.$i]:0 ,                    
                    'OUT_QTY'           => $request['REQ_RETURN_QTY_'.$i],
                    'SERIALNUMBER'      => $request['txtSR_popup_'.$i],             
                ];

            }
        }


		if($r_count5 > 0){
            $wrapped_links5["BRC"] = $req_data5; 
			$XMLBARC = ArrayToXml::convert($wrapped_links5);
        }
        else{
            $XMLBARC=NULL;
        }


        $VTID_REF      =   $this->vtid_ref;
        $VID           = 0;
        $USERID        = Auth::user()->USERID;   
        $ACTIONNAME    = $Approvallevel;
        $IPADDRESS     = $request->getClientIp();
        $CYID_REF      = Auth::user()->CYID_REF;
        $BRID_REF      = Session::get('BRID_REF');
        $FYID_REF      = Session::get('FYID_REF');

        $BRC_OUT_NO     = $request['BRC_OUT_NO'];
        $BRC_OUT_DT     = $request['BRC_OUT_DT'];
        $DOCTYPE        = $request['DOCTYPE'];
        $SOURCE_DOCNO   = $request['Documentpopup'];
        $DOCID_REF      = $request['DOCID_REF'];
        $REMARKS        = $request['HEADER_REMARKS'];

        
        $log_data = [ 
            $BRC_OUT_NO,$BRC_OUT_DT,$DOCTYPE,$SOURCE_DOCNO,$DOCID_REF,$REMARKS,$CYID_REF,$BRID_REF,$FYID_REF,            
            $XMLMAT,$XMLBARC,$VTID_REF,$USERID,Date('Y-m-d'),
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];  

        

        $sp_result = DB::select('EXEC SP_BARCODE_OUT_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?  ,?,?', $log_data);   
        //dd($sp_result);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $BRC_OUT_NO. ' Sucessfully Approved.']);

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
                
        $USERID_REF     =   Auth::user()->USERID;
        $VTID_REF       =   $this->vtid_ref;  
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');       
        $TABLE          =   "TBL_TRN_BARCODE_OUT_HDR";
        $FIELD          =   "BRCOID";
        $ACTIONNAME     = $Approvallevel;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $IPADDRESS      =   $request->getClientIp();
            
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
        $TABLE      =   "TBL_TRN_BARCODE_OUT_HDR";
        $FIELD      =   "BRCOID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
         'NT'  => 'TBL_TRN_BARCODE_OUT_MAT',
        ];

        $req_data[1]=[
        'NT'  => 'TBL_TRN_BARCODE_OUT_BRC',
        ];

        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_BARCODE_OUT  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_BARCODE_OUT_HDR')->where('BRCOID','=',$id)->first();

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
        
		$image_path         =   "docs/company".$CYID_REF."/BarcodeOut";     
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
   



    





    public function get_materital_item(Request $request){

        $AlpsStatus =   $this->AlpsStatus();  
             
        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
        $ALPS_PARTNO=isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''? $TabSetting->FIELD8:'Add. Info Part No';
        $CUSTOMER_PARTNO=isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''? $TabSetting->FIELD9:'Add. Info Customer Part No';
        $OEM_PARTNO=isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''? $TabSetting->FIELD10:'Add. Info OEM Part No';
        
        
        $CYID_REF       =   Auth::user()->CYID_REF;
        $Status         =   'A';
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $item_array     =   $request['item_array'];
        $checkCompany   =   $request['checkCompany'];
        $action_type    =   $request['action_type'];
        $recordid       =   $request['recordid'];
         $main_item    =   $request['main_item'];


         

     
        $material_array=array();
       if(!empty($item_array)){
        foreach($item_array as $key=>$val){

            $exp                        =   explode("_",$val);
            $Main_ITEMID_REF            =   $exp[0];
            $Main_MAIN_UOMID_REF        =   $exp[1];
            $Main_RECEIVED_QTY          =   $exp[2];
            if($checkCompany==''){
            $Main_RECEIVED_QTY           =   isset($exp[3]) && $exp[3]==''? 0:$exp[3];   
            }

            $mitem_id                   =   $Main_ITEMID_REF."_".$Main_MAIN_UOMID_REF;

            $BARCODE_REQ    =   DB::select("SELECT T2.ITEMID,T2.ICODE,T2.NAME,T2.MAIN_UOMID_REF,CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOMCODE,
            IC.SRNOA AS SERIAL_NO_APPLICABLE,IC.BARCODE_APPLICABLE,IC.SERIALNO_MODE,IC.SERIALNO_PREFIX,IC.SERIALNO_STARTS_FROM,IC.SERIALNO_SUFFIX,IC.SERIALNO_MAX_LENGTH,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.ALT_UOMID_REF,CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS ALT_UOMCODE
            ,'' AS SERIALNUMBER,'' AS QTY
            FROM TBL_MST_ITEM T2           
            LEFT JOIN TBL_MST_UOM T3 ON T2.MAIN_UOMID_REF=T3.UOMID
            LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID
            LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
            WHERE T2.ITEMID=$Main_ITEMID_REF");


            $SavedItemId=array(); 
            if($action_type=='EDIT'){        
            $SavedItemIds    =   DB::select("SELECT DISTINCT ITEMID_REF FROM TBL_TRN_BARCODE_OUT_BRC WHERE BRCOID_REF=$recordid");
            $SavedItemId=array(); 
            foreach($SavedItemIds as $key=>$Ilist){
                $SavedItemId[]=$Ilist->ITEMID_REF; 
                }
            }

           // dd($BARCODE_REQ); 


            

     

            if(isset($BARCODE_REQ) && !empty($BARCODE_REQ)){
                foreach($BARCODE_REQ as $row){

                    if(!in_array($row->ITEMID,$SavedItemId) && $action_type=='EDIT' ){                
                   $material_array[]=array(                     
                        'ITEMID_REF'            =>$row->ITEMID,
                        'ICODE'                 =>$row->ICODE,
                        'NAME'                  =>$row->NAME,
                        'ALPS_PART_NO'          =>$row->ALPS_PART_NO,
                        'CUSTOMER_PART_NO'      =>$row->CUSTOMER_PART_NO,
                        'OEM_PART_NO'           =>$row->OEM_PART_NO,
                        'ALT_UOMID_REF'         =>$row->ALT_UOMID_REF,
                        'MAIN_UOMCODE'          =>$row->MAIN_UOMCODE,
                        'ALT_UOMCODE'           =>$row->ALT_UOMCODE,
                        'MAIN_UOMID_REF'        =>$row->MAIN_UOMID_REF,               
                        'MAIN_ITEM_ROWID'       =>$mitem_id ,                   
                        'SERIAL_NO_APPLICABLE'  =>$row->SERIAL_NO_APPLICABLE ,                   
                        'BARCODE_APPLICABLE'    =>$row->BARCODE_APPLICABLE ,                   
                        'SERIALNO_MODE'         =>$row->SERIALNO_MODE ,                   
                        'SERIALNO_PREFIX'       =>$row->SERIALNO_PREFIX ,                   
                        'SERIALNO_STARTS_FROM'  =>$row->SERIALNO_STARTS_FROM ,                   
                        'SERIALNO_SUFFIX'       =>$row->SERIALNO_SUFFIX ,                   
                        'SERIALNO_MAX_LENGTH'   =>$row->SERIALNO_MAX_LENGTH ,                   
                        'Main_RECEIVED_QTY'     =>$Main_RECEIVED_QTY ,     
                        'SERIALNUMBER'          =>$row->SERIALNUMBER ,                   
                        'QTY'                   =>$row->QTY ,                
                    );
                    }else if($action_type!='EDIT'){
                    $material_array[]=array(                     
                        'ITEMID_REF'            =>$row->ITEMID,
                        'ICODE'                 =>$row->ICODE,
                        'NAME'                  =>$row->NAME,
                        'ALPS_PART_NO'          =>$row->ALPS_PART_NO,
                        'CUSTOMER_PART_NO'      =>$row->CUSTOMER_PART_NO,
                        'OEM_PART_NO'           =>$row->OEM_PART_NO,
                        'ALT_UOMID_REF'         =>$row->ALT_UOMID_REF,
                        'MAIN_UOMCODE'          =>$row->MAIN_UOMCODE,
                        'ALT_UOMCODE'           =>$row->ALT_UOMCODE,
                        'MAIN_UOMID_REF'        =>$row->MAIN_UOMID_REF,               
                        'MAIN_ITEM_ROWID'       =>$mitem_id ,                   
                        'SERIAL_NO_APPLICABLE'  =>$row->SERIAL_NO_APPLICABLE ,                   
                        'BARCODE_APPLICABLE'    =>$row->BARCODE_APPLICABLE ,                   
                        'SERIALNO_MODE'         =>$row->SERIALNO_MODE ,                   
                        'SERIALNO_PREFIX'       =>$row->SERIALNO_PREFIX ,                   
                        'SERIALNO_STARTS_FROM'  =>$row->SERIALNO_STARTS_FROM ,                   
                        'SERIALNO_SUFFIX'       =>$row->SERIALNO_SUFFIX ,                   
                        'SERIALNO_MAX_LENGTH'   =>$row->SERIALNO_MAX_LENGTH ,                   
                        'Main_RECEIVED_QTY'     =>$Main_RECEIVED_QTY ,  
                        'SERIALNUMBER'          =>$row->SERIALNUMBER ,                   
                        'QTY'                   =>$row->QTY ,                   
                    );

                    }
                    
                }
            }

    
            
        }
    }

        


        //$Main_RECEIVED_QTY=5; 
        
        $objAutoGenNo='';
        $material_array_final=array();
        foreach($material_array as $index=>$row_data){

            $r_count1=range(1,intval($row_data['Main_RECEIVED_QTY']));



            //if(isset($row_data['SERIAL_NO_APPLICABLE']) && $row_data['SERIAL_NO_APPLICABLE'] == 1)
             //   {

            $objAutoGenNo='';
            if($row_data['Main_RECEIVED_QTY']!=0){
        foreach($r_count1 as $key=>$data){

                            
         
            if(!empty($row_data )){   
                $objAutoGenNo='';   
                           

                if(isset($row_data['SERIALNO_MODE']) && $row_data['SERIALNO_MODE'] == "AUTOMATIC")
                {
                                                               
                if(isset($row_data['SERIALNO_PREFIX']) && $row_data['SERIALNO_PREFIX'] != "")
                    {
                        $objAutoGenNo = $objAutoGenNo.$row_data['SERIALNO_PREFIX'];
                    }
                         
                    
                if(isset($row_data['SERIALNO_MAX_LENGTH']) && $row_data['SERIALNO_MAX_LENGTH'])
                    {   
                     $objAutoGenNo = $objAutoGenNo.str_pad($row_data['SERIALNO_STARTS_FROM']+$key, $row_data['SERIALNO_MAX_LENGTH'], "0", STR_PAD_LEFT);
                    }
                    
            
                    if(isset($row_data['SERIALNO_SUFFIX']) && $row_data['SERIALNO_SUFFIX'] != "")
                    {
                        $objAutoGenNo = $objAutoGenNo.$row_data['SERIALNO_SUFFIX'];
                    }
                }
            }  


           // dd($objAutoGenNo); 

         
            $received_qty='1.00'; 
            $material_array_final[]=array(                     
                'ITEMID_REF'            =>$row_data['ITEMID_REF'],
                'ICODE'                 =>$row_data['ICODE'],
                'NAME'                  =>$row_data['NAME'],
                'ALPS_PART_NO'          =>$row_data['ALPS_PART_NO'],
                'CUSTOMER_PART_NO'      =>$row_data['CUSTOMER_PART_NO'],
                'OEM_PART_NO'           =>$row_data['OEM_PART_NO'],
                'ALT_UOMID_REF'         =>$row_data['ALT_UOMID_REF'],
                'MAIN_UOMCODE'          =>$row_data['MAIN_UOMCODE'],
                'ALT_UOMCODE'           =>$row_data['ALT_UOMCODE'],
                'MAIN_UOMID_REF'        =>$row_data['MAIN_UOMID_REF'],               
                'MAIN_ITEM_ROWID'       =>$mitem_id.'_'.intval($received_qty).($objAutoGenNo!=""? '_'.$objAutoGenNo:''),                   
                'SERIAL_NO_APPLICABLE'  =>$row_data['SERIAL_NO_APPLICABLE'] ,                   
                'BARCODE_APPLICABLE'    =>$row_data['BARCODE_APPLICABLE'] ,                   
                'SERIALNO_MODE'         =>$row_data['SERIALNO_MODE'] ,                   
                'SERIALNO_PREFIX'       =>$row_data['SERIALNO_PREFIX'] ,                   
                'SERIALNO_STARTS_FROM'  =>$row_data['SERIALNO_STARTS_FROM'] ,                   
                'SERIALNO_SUFFIX'       =>$row_data['SERIALNO_SUFFIX'] ,                   
                'SERIALNO_MAX_LENGTH'   =>$row_data['SERIALNO_MAX_LENGTH'] ,   
                'REQ_RETURN_QTY'        =>$received_qty ,                   
                'S_NO'                  =>'' ,     
                'SERIALNUMBER'          =>$row_data['SERIALNUMBER'] ,                   
                'QTY'                   =>$row_data['QTY'],     
                          
            );

            


        }
    }
    
        }




        if($action_type=="EDIT"){
            $BARCODE_REQ    =   DB::select("SELECT T2.ITEMID,T2.ICODE,T2.NAME,T2.MAIN_UOMID_REF,CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOMCODE,
            IC.SRNOA AS SERIAL_NO_APPLICABLE,IC.BARCODE_APPLICABLE,IC.SERIALNO_MODE,IC.SERIALNO_PREFIX,IC.SERIALNO_STARTS_FROM,IC.SERIALNO_SUFFIX,IC.SERIALNO_MAX_LENGTH,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,ISNULL(T1.UOMID_REF,T2.ALT_UOMID_REF) AS ALT_UOMID_REF,
            CASE WHEN T4.UOMCODE <> '' THEN CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) ELSE CONCAT(T5.UOMCODE,'-',T5.DESCRIPTIONS) END AS ALT_UOMCODE
            ,T1.SERIALNUMBER,T1.QTY
            FROM TBL_MST_ITEM T2           
            LEFT JOIN TBL_TRN_BARCODE_OUT_BRC T1 ON T2.ITEMID=T1.ITEMID_REF
            LEFT JOIN TBL_MST_UOM T3 ON T2.MAIN_UOMID_REF=T3.UOMID
            LEFT JOIN TBL_MST_UOM T4 ON T1.UOMID_REF=T4.UOMID
            LEFT JOIN TBL_MST_UOM T5 ON T2.ALT_UOMID_REF=T5.UOMID
            LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
            WHERE  T1.BRCOID_REF=$recordid 
            ");
    
    
            foreach($BARCODE_REQ as $index=>$row_data){
                if(!empty($main_item) && in_array($row_data->ITEMID,$main_item)){
            $material_array_final[]=array(                     
                'ITEMID_REF'            =>$row_data->ITEMID,
                'ICODE'                 =>$row_data->ICODE,
                'NAME'                  =>$row_data->NAME,
                'ALPS_PART_NO'          =>$row_data->ALPS_PART_NO,
                'CUSTOMER_PART_NO'      =>$row_data->CUSTOMER_PART_NO,
                'OEM_PART_NO'           =>$row_data->OEM_PART_NO,
                'ALT_UOMID_REF'         =>$row_data->ALT_UOMID_REF,
                'MAIN_UOMCODE'          =>$row_data->MAIN_UOMCODE,
                'ALT_UOMCODE'           =>$row_data->ALT_UOMCODE,
                'MAIN_UOMID_REF'        =>$row_data->MAIN_UOMID_REF,               
                'MAIN_ITEM_ROWID'       =>'',                      
                'SERIAL_NO_APPLICABLE'  =>$row_data->SERIAL_NO_APPLICABLE ,                   
                'BARCODE_APPLICABLE'    =>$row_data->BARCODE_APPLICABLE ,                   
                'SERIALNO_MODE'         =>$row_data->SERIALNO_MODE ,                   
                'SERIALNO_PREFIX'       =>$row_data->SERIALNO_PREFIX ,                   
                'SERIALNO_STARTS_FROM'  =>$row_data->SERIALNO_STARTS_FROM ,                   
                'SERIALNO_SUFFIX'       =>$row_data->SERIALNO_SUFFIX ,                   
                'SERIALNO_MAX_LENGTH'   =>$row_data->SERIALNO_MAX_LENGTH ,   
                'REQ_RETURN_QTY'        =>1 ,                   
                'S_NO'                  =>$row_data->SERIALNUMBER ,                   
                'QTY'                   =>$row_data->QTY,     
                          
            );

        }
    
        }
    
           }



       

        if(!empty($material_array_final)){            
            $Row_Count5 =   count($material_array_final);
            echo'<table id="example8" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                        <tr>
                        <th hidden ><input class="form-control" type="hidden" name="Row_Count5" id ="Row_Count5" value="'.$Row_Count5.'"></th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th '.$AlpsStatus['hidden'].' >'.$ALPS_PARTNO.'</th>
                            <th '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PARTNO.'</th>
                            <th '.$AlpsStatus['hidden'].' >'.$OEM_PARTNO.'</th>    
                            <th>UOM</th>
                            <th>Out Qty</th>
                            <th '.$checkCompany.'>Unit</th>
                            <th '.$checkCompany.'>Out Qty (Alt)</th>                   
                            <th '.$checkCompany.'>Balance Qty</th>
                            <th>Serial No</th>
                            <th '.$checkCompany.'>Action</th>
                
                        </tr>
                    </thead>
                    <tbody>';
                    
                    foreach($material_array_final as $index=>$row_data){

         

                     $SERIALNO_MODE=isset($row_data['SERIALNO_MODE']) && $row_data['SERIALNO_MODE'] == "AUTOMATIC" ? "readonly":""; 
                     $Qty_STATUS=$row_data['SERIAL_NO_APPLICABLE']==1  ? "":"readonly"; 
                     
                  
//dd($AlpsStatus['hidden']); 
                        echo '<tr  class="participantRow8">';
                        echo '<td><input type="text" id="txtSUBITEM_popup_'.$index.'" value="'.$row_data['ICODE'].'" class="form-control" readonly  /></td>';
                        echo '<td><input type="text" id="SUBITEM_NAME_'.$index.'"     value="'.$row_data['NAME'].'"  class="form-control" readonly  /></td>';
                        echo '<td '.$AlpsStatus['hidden'].'><input type="text"    id="ALPS_PART_NO_'.$index.'"     value="'.$row_data['ALPS_PART_NO'].'"  class="form-control" readonly  /></td>';
                        echo '<td '.$AlpsStatus['hidden'].'><input type="text"    id="CUSTOMER_PART_NO_'.$index.'"     value="'.$row_data['CUSTOMER_PART_NO'].'"  class="form-control" readonly  /></td>';
                        echo '<td '.$AlpsStatus['hidden'].'><input type="text"    id="OEM_PART_NO_'.$index.'"     value="'.$row_data['OEM_PART_NO'].'"  class="form-control" readonly  /></td>';       
                        echo '<td hidden><input type="text"   name="REQ_ITEMID_REF_'.$index.'"   id="REQ_ITEMID_REF_'.$index.'"   value="'.$row_data['ITEMID_REF'].'" /></td>';                        
                   
                        echo '<td><input    type="text" name="REQ_MAIN_UOM_'.$index.'"           id="REQ_MAIN_UOM_'.$index.'"     value="'.$row_data['MAIN_UOMCODE'].'"   class="form-control" readonly   /></td>';

                        echo '<td hidden><input type="hidden" name="REQ_UOMID_REF_'.$index.'"    id="REQ_UOMID_REF_'.$index.'"    value="'.$row_data['MAIN_UOMID_REF'].'" /></td>';

                        echo '<td ><input type="text" style="text-align:right;" name="REQ_RETURN_QTY_'.$index.'" class="form-control" readonly   id="REQ_RETURN_QTY_'.$index.'" 
                        value="'.number_format($row_data['REQ_RETURN_QTY'],2).'"  /></td>';                        

                        echo '<td '.$checkCompany.' ><input type="text" name="PACKUOM_'.$index.'" class="form-control" readonly  onclick="getUOM(this.id,'.$row_data['ITEMID_REF'].')"   id="PACKUOM_'.$index.'"  value="'.$row_data['ALT_UOMCODE'].'"  /></td>';

                        echo '<td hidden ><input type="text" name="PACKUOMID_REF_'.$index.'"    id="PACKUOMID_REF_'.$index.'" value="'.$row_data['ALT_UOMID_REF'].'"/></td>';    
                        
                        echo '<td '.$checkCompany.'><input type="text"  name="REQ_QTY_'.$index.'" class="form-control"  readonly  id="REQ_QTY_'.$index.'"  value="'.$row_data['QTY'].'"  /></td>';

                        echo '<td '.$checkCompany.'><input type="text"  name="PENDING_QTY_'.$index.'" class="form-control"  id="PENDING_QTY_'.$index.'" readonly   /></td>';

                        echo '<td><input type="text" name="txtSR_popup_'.$index.'" class="form-control" readonly      id="txtSR_popup_'.$index.'"   value="'.$row_data['S_NO'].'"  /></td>';
                        echo '<td hidden ><input type="text" name="SERIALNO_REF_'.$index.'"    id="SERIALNO_REF_'.$index.'" /></td>';    


                        echo '<td hidden ><input type="text" name="SERIAL_NO_APPLICABLE_'.$index.'" class="form-control"   id="SERIAL_NO_APPLICABLE_'.$index.'"   value="'.$row_data['SERIAL_NO_APPLICABLE'].'" /></td>';   
                        echo '<td hidden><input type="text" name="BARCODE_APPLICABLE_'.$index.'" class="form-control"   id="BARCODE_APPLICABLE_'.$index.'"   value="'.$row_data['BARCODE_APPLICABLE'].'" /></td>';  

                        echo '<td '.$checkCompany.' style="width: 106px;" align="center" ><button class="btn Sub_add Sub_material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                        <button class="btn Sub_remove Sub_dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>';
                    
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

      
    public function GetDocument(Request $request) {        
                    $DOCTYPE=$request['DOCTYPE'];
                    //dd($DOCTYPE); 
                    $Status = "N";
                    $CYID_REF = Auth::user()->CYID_REF;
                    $BRID_REF = Session::get('BRID_REF');
                    $FYID_REF = Session::get('FYID_REF');       
                    if($DOCTYPE=='SALES_CHALLAN'){
                        $objdata = DB::table('TBL_TRN_SLSC01_HDR')
                        ->where('TBL_TRN_SLSC01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_TRN_SLSC01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_TRN_SLSC01_HDR.STATUS','=',$Status)     
                        ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_TRN_SLSC01_HDR.SLID_REF','=','TBL_MST_SUBLEDGER.SGLID')
                        ->select('TBL_TRN_SLSC01_HDR.SCID AS DOCID','TBL_TRN_SLSC01_HDR.SCNO AS DOC_COL1','TBL_TRN_SLSC01_HDR.SCDT AS DOC_COL2','TBL_MST_SUBLEDGER.SLNAME AS DOC_COL3') 
                        ->get()    
                        ->toArray();

                    }else if($DOCTYPE=='MISR'){                
                        $objdata = DB::select("SELECT MISRID AS DOCID,MISRNO AS DOC_COL1,MISRDT AS DOC_COL2,'' AS DOC_COL3 FROM TBL_TRN_PDMISR_HDR
                        WHERE STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF'");           
                        }
                    else if($DOCTYPE=='JOB_WORK_CHALLAN'){
                        $objdata = DB::table('TBL_TRN_JWC_HDR')
                        ->where('TBL_TRN_JWC_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_TRN_JWC_HDR.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_TRN_JWC_HDR.STATUS','=',$Status)     
                        ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_TRN_JWC_HDR.VID_REF','=','TBL_MST_SUBLEDGER.SGLID')
                        ->select('TBL_TRN_JWC_HDR.JWCID AS DOCID','TBL_TRN_JWC_HDR.JWCNO AS DOC_COL1','TBL_TRN_JWC_HDR.JWCDT AS DOC_COL2','TBL_MST_SUBLEDGER.SLNAME AS DOC_COL3') 
                        ->get()    
                        ->toArray();  
                        }
             
                    else if($DOCTYPE=='RGP'){
                        $objdata = DB::table('TBL_TRN_IRGP01_HDR')
                        ->where('TBL_TRN_IRGP01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_TRN_IRGP01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_TRN_IRGP01_HDR.STATUS','=',$Status)     
                        ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_TRN_IRGP01_HDR.VID_REF','=','TBL_MST_SUBLEDGER.SGLID')
                        ->select('TBL_TRN_IRGP01_HDR.RGPID AS DOCID','TBL_TRN_IRGP01_HDR.RGP_NO AS DOC_COL1','TBL_TRN_IRGP01_HDR.RGP_DT AS DOC_COL2','TBL_MST_SUBLEDGER.SLNAME AS DOC_COL3') 
                        ->get()    
                        ->toArray(); 
                        }

                    else if($DOCTYPE=='PURCHASE_RETURN'){
                        $objdata = DB::table('TBL_TRN_PRRT01_HDR')
                        ->where('TBL_TRN_PRRT01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_TRN_PRRT01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_TRN_PRRT01_HDR.STATUS','=',$Status)     
                        ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_TRN_PRRT01_HDR.VID_REF','=','TBL_MST_SUBLEDGER.SGLID')
                        ->select('TBL_TRN_PRRT01_HDR.PRRID AS DOCID','TBL_TRN_PRRT01_HDR.PRR_NO AS DOC_COL1','TBL_TRN_PRRT01_HDR.PRR_DT AS DOC_COL2','TBL_MST_SUBLEDGER.SLNAME AS DOC_COL3') 
                        ->get()    
                        ->toArray();           
                        }
                    else if($DOCTYPE=='STTOSTTRANSAFER'){
                        $objdata = DB::select("SELECT ST_STID AS DOCID,ST_ST_DOCNO AS DOC_COL1,ST_ST_DOCDT AS DOC_COL2,'' AS DOC_COL3 FROM TBL_TRN_STOCK_STOCK_HDR
                        WHERE STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF' ");
                        }

                    else if($DOCTYPE=='MIS'){
                        $objdata = DB::select("SELECT MISID AS DOCID,MIS_NO AS DOC_COL1,MIS_DT AS DOC_COL2,'' AS DOC_COL3 FROM TBL_TRN_MISS01_HDR
                        WHERE STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF' ");
                        }
                    else if($DOCTYPE=='NRGP'){
                        $objdata = DB::table('TBL_TRN_NRGP01_HDR')
                        ->where('TBL_TRN_NRGP01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_TRN_NRGP01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_TRN_NRGP01_HDR.STATUS','=',$Status)     
                        ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_TRN_NRGP01_HDR.VID_REF','=','TBL_MST_SUBLEDGER.SGLID')
                        ->select('TBL_TRN_NRGP01_HDR.NRGPID AS DOCID','TBL_TRN_NRGP01_HDR.NRGP_NO AS DOC_COL1','TBL_TRN_NRGP01_HDR.NRGP_DT AS DOC_COL2','TBL_MST_SUBLEDGER.SLNAME AS DOC_COL3') 
                        ->get()    
                        ->toArray(); 
                    }
                    else if($DOCTYPE=='STOCKADJUSTMENT'){
                            $objdata = DB::select("SELECT ST_ADJUSTID AS DOCID,ST_ADJUST_DOCNO AS DOC_COL1,ST_ADJUST_DOCDT AS DOC_COL2,'' AS DOC_COL3 FROM TBL_TRN_STOCK_ADJUST_HDR
                            WHERE STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF'");   
                        }else if($DOCTYPE=='PMOVEMENT'){
                            $objdata = DB::select("SELECT PNMID AS DOCID,PNM_NO AS DOC_COL1,PNM_DT AS DOC_COL2,'' AS DOC_COL3 FROM TBL_TRN_PDPNM_HDR
                            WHERE STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF' ");  
                        }else if($DOCTYPE=='ASSEMBLING'){
                            $objdata = DB::select("SELECT ADSMID AS DOCID,ADSMNO AS DOC_COL1,ADSMDT AS DOC_COL2,'' AS DOC_COL3 FROM TBL_TRN_ASMDSM_HDR
                            WHERE TYPE='ASSEMBLING' AND STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF'   ");
                         }else if($DOCTYPE=='DISSEMBLING'){
                            $objdata = DB::select("SELECT ADSMID AS DOCID,ADSMNO AS DOC_COL1,ADSMDT AS DOC_COL2,'' AS DOC_COL3 FROM TBL_TRN_ASMDSM_HDR
                            WHERE TYPE='DISSEMBLING' AND STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF'   ");
                         }
            
         
             if(!empty($objdata)){        
                 foreach ($objdata as $index=>$dataRow){   
                     $row = '';
                     $row = $row.'<tr ><td style="text-align:center; width:10%">';
                     $row = $row.'<input type="checkbox" name="DOCUMENT_NO[]"  id="getglcode_'.$dataRow->DOCID.'" class="clsspid_prr" 
                     value="'.$dataRow->DOCID.'"/>             
                     </td>           
                     <td style="width:30%;">'.$dataRow->DOC_COL1;
                     $row = $row.'<input type="hidden" id="txtgetglcode_'.$dataRow->DOCID.'" data-code="'.$dataRow->DOC_COL1.'"   data-desc="'.$dataRow->DOC_COL2.'" 
                     value="'.$dataRow->DOCID.'"/></td>
         
                     <td style="width:30%;">'.$dataRow->DOC_COL2.'</td>
                     <td style="width:30%;">'.$dataRow->DOC_COL3.'</td>
           
         
                    </tr>';
                     echo $row;
                 }
         
                 }else{
                     echo '<tr><td colspan="2">Record not found.</td></tr>';
                 }
         
                 exit();
         
         
         
            }



        
            /*MAIN ITEM*/
            
            public function Main_getItemDetails(Request $request){
                $Status      =   'N';
                $DOCID_REF   =   $request['DOCID_REF'];      
                $docType     =   $request['docType'];      
                $CYID_REF    =   Auth::user()->CYID_REF;
                $BRID_REF    =   Session::get('BRID_REF');
                $FYID_REF    =   Session::get('FYID_REF');
                $StdCost     = 0;
                $AlpsStatus         =   $this->AlpsStatus();       

               $ObjItem =[];
               if($docType=='SALES_CHALLAN'){
               $ObjItem =  DB::select("SELECT 
               T1.*,T1.CHALLAN_MAINQTY AS RECEIVED_QTY_MU, 
               T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
               T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
               CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
               CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
               IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
               FROM TBL_TRN_SLSC01_MAT T1 
               LEFT JOIN TBL_TRN_SLSC01_HDR H ON T1.SCID_REF=H.SCID               
               LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
               LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
               LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
               LEFT JOIN TBL_MST_UOM T3 ON T1.MAINUOMID_REF=T3.UOMID
               LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
               WHERE H.SCID='$DOCID_REF' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
               ORDER BY T1.SCMATID ASC
                ");       
            }else if($docType=='MISR'){
                $ObjItem =  DB::select("SELECT 
                T1.*,T1.ISSUED_QTY AS RECEIVED_QTY_MU, 
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
                T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
                FROM TBL_TRN_PDMISR_MAT T1 
                LEFT JOIN TBL_TRN_PDMISR_HDR H ON T1.MISRID_REF=H.MISRID               
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
                LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
                LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
                WHERE H.MISRID='$DOCID_REF' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                ORDER BY T1.MISR_MATID ASC
                 ");       
             }else if($docType=='JOB_WORK_CHALLAN'){
                $ObjItem =  DB::select("SELECT 
                T1.*,T1.JW_PRODUCE_QTY AS RECEIVED_QTY_MU, 
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
                T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
                FROM TBL_TRN_JWC_MAT T1 
                LEFT JOIN TBL_TRN_JWC_HDR H ON T1.JWCID_REF=H.JWCID               
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
                LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
                LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
                WHERE H.JWCID='$DOCID_REF' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                ORDER BY T1.JWC_MATID ASC
                 ");       
             }else if($docType=='RGP'){
                $ObjItem =  DB::select("SELECT 
                T1.*,T1.ISSUE_QTY AS RECEIVED_QTY_MU, 
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
                T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
                FROM TBL_TRN_IRGP01_MAT T1 
                LEFT JOIN TBL_TRN_IRGP01_HDR H ON T1.RGPID_REF=H.RGPID               
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
                LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
                LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
                WHERE H.RGPID='$DOCID_REF' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                ORDER BY T1.RGP_MATID ASC
                 ");       
             }else if($docType=='PURCHASE_RETURN'){
                $ObjItem =  DB::select("SELECT 
                T1.*,T1.RETURN_QTY_MU AS RECEIVED_QTY_MU, 
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
                T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
                FROM TBL_TRN_PRRT01_MAT T1 
                LEFT JOIN TBL_TRN_PRRT01_HDR H ON T1.PRRID_REF=H.PRRID               
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
                LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
                LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
                WHERE H.PRRID='$DOCID_REF' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                ORDER BY T1.PRR_MATID ASC
                 ");       
             }
             else if($docType=='STTOSTTRANSAFER'){
                $ObjItem =  DB::select("SELECT 
                T1.*,T1.QTY AS RECEIVED_QTY_MU,T1.UOMID_REF AS MAIN_UOMID_REF,
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
                T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
                FROM TBL_TRN_STOCK_STOCK_MAT T1 
                LEFT JOIN TBL_TRN_STOCK_STOCK_HDR H ON T1.ST_STID_REF=H.ST_STID               
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF_IN=T2.ITEMID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
                LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
                LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
                WHERE H.ST_STID='$DOCID_REF' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                ORDER BY T1.ST_ST_MATID ASC
                 ");

            }else if($docType=='MIS'){
                $ObjItem =  DB::select("SELECT 
                T1.*,T1.ISSUED_QTY AS RECEIVED_QTY_MU, 
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
                T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
                FROM TBL_TRN_MISS01_MAT T1 
                LEFT JOIN TBL_TRN_MISS01_HDR H ON T1.MISID_REF=H.MISID               
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
                LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
                LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
                WHERE H.MISID='$DOCID_REF' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                ORDER BY T1.MIS_MATID ASC
                 ");

            }else if($docType=='NRGP'){
                $ObjItem =  DB::select("SELECT 
                T1.*,T1.NRGP_QTY AS RECEIVED_QTY_MU, 
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
                T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
                FROM TBL_TRN_NRGP01_MAT T1 
                LEFT JOIN TBL_TRN_NRGP01_HDR H ON T1.NRGPID_REF=H.NRGPID               
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
                LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
                LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
                WHERE H.NRGPID='$DOCID_REF' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                ORDER BY T1.NRGP_MATID ASC
                 ");

            } else if($docType=='STOCKADJUSTMENT'){
                $ObjItem =  DB::select("SELECT 
                T1.*,T1.QTY AS RECEIVED_QTY_MU,T1.UOMID_REF AS MAIN_UOMID_REF,
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
                T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
                FROM TBL_TRN_STOCK_ADJUST_MAT T1 
                LEFT JOIN TBL_TRN_STOCK_ADJUST_HDR H ON T1.ST_ADJUSTID_REF=H.ST_ADJUSTID               
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
                LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
                LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
                WHERE H.ST_ADJUSTID='$DOCID_REF' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                ORDER BY T1.ST_ADJUST_MATID ASC
                 ");

            }

            else if($docType=='DISSEMBLING'){
                $ObjItem =  DB::select("SELECT 
                T1.*,T1.QTY AS RECEIVED_QTY_MU,T1.UOMID_REF AS MAIN_UOMID_REF,
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
                T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
                FROM TBL_TRN_ASMDSM_HDR T1                   
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
                LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
                LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
                WHERE T1.ADSMID='$DOCID_REF' AND T1.TYPE='DISSEMBLING' AND T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF'  
                ORDER BY T1.ADSMID ASC
                ");
   
               
            }else if($docType=='ASSEMBLING'){
                $ObjItem =  DB::select("SELECT 
                T1.*,T1.ITEM_QTY AS RECEIVED_QTY_MU,T1.UOMID_REF AS MAIN_UOMID_REF,
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
                T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
                FROM TBL_TRN_ASMDSM_MAT T1 
                LEFT JOIN TBL_TRN_ASMDSM_HDR H ON T1.ADSMID_REF=H.ADSMID               
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
                LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
                LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
                WHERE H.ADSMID='$DOCID_REF' AND H.TYPE='ASSEMBLING' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                ORDER BY T1.ADSMMATID ASC
                ");

            }

         // dd($ObjItem); 
        
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
                                    [$dataRow->ITEMID,$dataRow->ALT_UOMID_REF]);
        
                        $TOQTY =  isset($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 0;

                        $PENDING_QTY=$dataRow->RECEIVED_QTY_MU;
        
                     
                        $TOQTY =  0;

                        $FROMQTY =  isset($PENDING_QTY)? $PENDING_QTY : 0;
        
                        $ObjItemGroup =  DB::select('SELECT TOP 1 GROUPCODE, GROUPNAME FROM TBL_MST_ITEMGROUP  
                                    WHERE  CYID_REF = ?  AND ITEMGID = ?
                                    AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                    [$CYID_REF, $dataRow->ITEMGID_REF, 'A' ]);

                            //dd($dataRow->ITEMID);         
                           // dd($ObjItemGroup);         
        
                        $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS FROM TBL_MST_ITEMCATEGORY  
                                    WHERE  CYID_REF = ?  AND ICID = ?
                                    AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                    [$CYID_REF, $dataRow->ICID_REF, 'A' ]);
        
        
                            if(!is_null($dataRow->BUID_REF)){
                                $ObjBusinessUnit =  DB::select('SELECT TOP 1  BUCODE,BUNAME FROM TBL_MST_BUSINESSUNIT  
                                WHERE  CYID_REF = ? AND BRID_REF = ?  AND BUID = ?', 
                                [$CYID_REF, $BRID_REF, $dataRow->BUID_REF]);
                            }
                            else
                            {
                                $ObjBusinessUnit = NULL;
                            }
                                
                            $BusinessUnit       =   isset($ObjBusinessUnit) && $ObjBusinessUnit != NULL ? $ObjBusinessUnit[0]->BUCODE.'-'.$ObjBusinessUnit[0]->BUNAME : '';
                            $ALPS_PART_NO       =   $dataRow->ALPS_PART_NO;
                            $CUSTOMER_PART_NO   =   $dataRow->CUSTOMER_PART_NO;
                            $OEM_PART_NO        =   $dataRow->OEM_PART_NO;
        
        
                            $AultUmQuantity = $this->getAltUmQty($dataRow->ALT_UOMID_REF,$dataRow->ITEMID,$FROMQTY);
        
                            $desc6  =   $dataRow->ITEMID;

                            $UOMCODE=isset($ObjMainUOM[0]->UOMCODE) ? $ObjMainUOM[0]->UOMCODE:'';
                            $DESCRIPTIONS=isset($ObjMainUOM[0]->DESCRIPTIONS) ? $ObjMainUOM[0]->DESCRIPTIONS:'';
                            //dd($UOMCODE); 
                     
                            $row = '';
                            $row = $row.'<tr id="item_'.$dataRow->ITEMID .'"  class="Main_clsitemid"><td  style="width:8%; text-align: center;"><input type="checkbox" id="Main_chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="Main_js-selectall1"  ></td>';
                            $row = $row.'<td style="width:10%;">'.$dataRow->ICODE;
                            $row = $row.'<input type="hidden" id="uniquerowid_'.$desc6.'"   />';
                            $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'" data-desc6="'.$desc6.'"  data-desc7="'.$AultUmQuantity.'" data-desc8="'.$PENDING_QTY.'"  data-desc9="'.$dataRow->SERIAL_NO.'" data-desc10="'.$dataRow->BARCODE.'"
                            value="'.$dataRow->ITEMID.'"/></td>
                            
                            <td style="width:10%;" id="itemname_'.$dataRow->ITEMID.'" >'.$dataRow->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ITEM_SPECI.'"
                            value="'.$dataRow->NAME.'"/></td>';
                            $row = $row.'<td style="width:8%;" id="itemuom_'.$dataRow->ITEMID.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'" data-desc="'.$UOMCODE.'-'.$DESCRIPTIONS.'"  data-desc2="'.$ALPS_PART_NO.'" data-desc3="'.$CUSTOMER_PART_NO.'" data-desc4="'.$OEM_PART_NO.'"
                            value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$UOMCODE.'-'.$DESCRIPTIONS.'</td>';
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

        




            public function codeduplicate(Request $request){

                $PRR_DOCNO  =   trim($request['PRR_DOCNO']);
                $objLabel = DB::table('TBL_TRN_BARCODE_OUT_HDR')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('FYID_REF','=',Session::get('FYID_REF'))
                ->where('BRC_OUT_NO','=',$PRR_DOCNO)
                ->select('BRC_OUT_NO')->first();
        
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
                return  DB::select('SELECT MAX(BRC_OUT_DT) BRC_OUT_DT FROM TBL_TRN_BARCODE_OUT_HDR  
                WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND VTID_REF = ? AND STATUS = ?', 
                [$CYID_REF, $BRID_REF, $FYID_REF, $this->vtid_ref, 'A' ]);
            }
        





            public function get_STR(Request $request){
                $CYID_REF           =   Auth::user()->CYID_REF;
                $BRID_REF           =   Session::get('BRID_REF');
                $FYID_REF           =   Session::get('FYID_REF');
                $fieldid            =   $request['fieldid'];
                $class_name         =   $request['class_name'];
                $ITEMID_REF         =   $request['ITEMID_REF'];
                $checkCompany       =   $request['checkCompany']==''? "":"hidden";

                $ObjData1   =   DB::select("SELECT DISTINCT T1.SERIALNUMBER,T1.QTY,T1.PENDING_QTY,T1.INDATE
                                FROM TBL_TRN_BARCODE_BRC T1
                                LEFT JOIN TBL_TRN_BARCODE_HDR T2 ON T1.BRCID_REF=T2.BRCID          
                                WHERE T1.ITEMID_REF='$ITEMID_REF' AND T2.CYID_REF='$CYID_REF' AND T2.BRID_REF='$BRID_REF'
                                ORDER BY T1.INDATE --ORDER BY T1.BRC_BRCID ASC
                                ");

                               // dd($ObjData1); 

                if(!empty($ObjData1)){
                   foreach ($ObjData1 as $index=>$dataRow){ 
                    $date           =   strtotime($dataRow->INDATE);
                    $create_date    =   date('Y-m-d H:i:s', $date);

                      $BALANCEQTY=isset($dataRow->PENDING_QTY) && $dataRow->QTY != $dataRow->PENDING_QTY ? $dataRow->PENDING_QTY: $dataRow->QTY;

                       $row =   '';
                       $row = $row.'<tr >
                       <td style="width:10%"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="socode_'.$dataRow->SERIALNUMBER .'"  class="'.$class_name.'" value="'.$dataRow->SERIALNUMBER.'" ></td>
                       <td style="width:30%">'.$dataRow->SERIALNUMBER;
                       $row = $row.'<input type="hidden" id="txtsocode_'.$dataRow->SERIALNUMBER.'" data-desc="'.$dataRow->SERIALNUMBER.'" data-desc1="'.$dataRow->SERIALNUMBER.'" data-desc2="'.$dataRow->QTY.'"  data-desc3="'.$BALANCEQTY.'"  value="'.$dataRow->SERIALNUMBER.'"/></td>
                       <td style="width:30%" '.$checkCompany.' >'.$dataRow->QTY.'</td>
                       <td style="width:30%">'.$create_date.'</td>
                       </tr>';
                       echo $row;                       
                   }                
                }else{
                    echo '<tr><td>Record not found.</td></tr>';
                }
                exit();   
                }
                


    
}
