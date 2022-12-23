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
use Carbon\Carbon;

class TrnFrm418Controller extends Controller{

    protected $form_id  = 418;
    protected $vtid_ref = 492;
    protected $view     = "transactions.inventory.Barcode.trnfrm";
   
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

        $objDataList	=	DB::select("select hdr.BRCID,hdr.BRC_NO,hdr.BRC_DT,hdr.INDATE,
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
                                inner join TBL_TRN_BARCODE_HDR hdr
                                on a.VID = hdr.BRCID 
                                and a.VTID_REF = hdr.VTID_REF 
                                and a.CYID_REF = hdr.CYID_REF 
                                and a.BRID_REF = hdr.BRID_REF
                                where a.VTID_REF = '$this->vtid_ref'
                                and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                                and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                                ORDER BY hdr.BRCID DESC ");

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
        $objSON = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('FYID_REF','=',$FYID_REF)
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_DOCNO_DEFINITION.*')
        ->first();

        $objDataNo   =   NULL;

        if( isset($objSON->SYSTEM_GRSR) && $objSON->SYSTEM_GRSR == "1")
        {
            if($objSON->PREFIX_RQ == "1")
            {
                $objDataNo = $objSON->PREFIX;
            }        
            if($objSON->PRE_SEP_RQ == "1")
            {
                if($objSON->PRE_SEP_SLASH == "1")
                {
                $objDataNo = $objDataNo.'/';
                }
                if($objSON->PRE_SEP_HYPEN == "1")
                {
                $objDataNo = $objDataNo.'-';
                }
            }        
            if($objSON->NO_MAX)
            {   
                $objDataNo = $objDataNo.str_pad($objSON->LAST_RECORDNO+1, $objSON->NO_MAX, "0", STR_PAD_LEFT);
            }
            
            if($objSON->NO_SEP_RQ == "1")
            {
                if($objSON->NO_SEP_SLASH == "1")
                {
                $objDataNo = $objDataNo.'/';
                }
                if($objSON->NO_SEP_HYPEN == "1")
                {
                $objDataNo = $objDataNo.'-';
                }
            }
            if($objSON->SUFFIX_RQ == "1")
            {
                $objDataNo = $objDataNo.$objSON->SUFFIX;
            }
        }

        //dd($objDataNo);
        $FormId     =   $this->form_id;
        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        $objUOM = DB::select('SELECT UOMID, UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
        WHERE  CYID_REF = ?  AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?
        order by UOMCODE ASC', [$CYID_REF,'A' ]);  

        $CompanySpecific	=	Helper::getAddSetting(Auth::user()->CYID_REF,'BARCODE');
        $checkCompany=isset($CompanySpecific->FIELD1) && $CompanySpecific->FIELD1 =='YES' ? '':'hidden'; 

        return view($this->view.$FormId.'add',compact(['AlpsStatus','FormId','objSON','objDataNo','objlastdt','TabSetting','objUOM','checkCompany']));       
    }

    public function save(Request $request) {

        $Main_r_count1 = $request['Main_Row_Count1'];
        $r_count5 = $request['Row_Count5'];

        for ($i=0; $i<=$Main_r_count1; $i++){
            if(isset($request['Main_ITEMID_REF_'.$i]) && $request['Main_ITEMID_REF_'.$i]!='' ){

                $req_datas[$i] = [
                    'ITEMID_REF'       => $request['Main_ITEMID_REF_'.$i],
                    'UOMID_REF'        => $request['Main_MAIN_UOMID_REF_'.$i],
                    'RECEIVED_QTY'     => (!empty($request['Main_RECEIVED_QTY_'.$i]) ? $request['Main_RECEIVED_QTY_'.$i] : 0),
                    'ALT_QTY'          => (!empty($request['Alt_RECEIVED_QTY_'.$i]) ? $request['Alt_RECEIVED_QTY_'.$i] : 0),
                    'SERIALNOAPL'      => isset($request['SERIAL_NO_'.$i]) ? 1:0 ,
                    'BARCODEAPL'       => isset($request['BARCODE_'.$i]) ? 1:0 ,                   
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
                    'RECEIVED_QTY'      => $request['REQ_RETURN_QTY_'.$i],
                    'SERIALNUMBER'      => $request['REQ_SERIALNO_'.$i],    
                    'WEIGHT'    	    => isset($request['WEIGHT_QTY_'.$i]) && $request['WEIGHT_QTY_'.$i]!='' ? $request['WEIGHT_QTY_'.$i]:0 ,           
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
        $ACTIONNAME    = 'ADD';
        $IPADDRESS     = $request->getClientIp();
        $CYID_REF      = Auth::user()->CYID_REF;
        $BRID_REF      = Session::get('BRID_REF');
        $FYID_REF      = Session::get('FYID_REF');

        $BRC_NO         = $request['BRC_NO'];
        $BRC_DT         = $request['BRC_DT'];
        $DOCTYPE        = $request['DOCTYPE'];
        $SOURCE_DOCNO   = $request['Documentpopup'];
        $DOCID_REF      = $request['DOCID_REF'];
        $REMARKS        = $request['HEADER_REMARKS'];


        $ExistCRID  =   DB::select("SELECT COUNT(*) AS RECORD FROM TBL_TRN_BARCODE_HDR WHERE SOURCE_TYPE='$DOCTYPE' AND DOCID_REF=$DOCID_REF AND STATUS ='N'  ");
        $Record=isset($ExistCRID[0]->RECORD)? $ExistCRID[0]->RECORD:0;
      
        if($Record != "0"){
            return Response::json(['errors'=>true,'msg' => 'Sorry! Kindly approve the exisiting records for-'.$SOURCE_DOCNO.' document type before creating the new one.']);

           
        }

        
        $log_data = [ 
            $BRC_NO,$BRC_DT,$DOCTYPE,$SOURCE_DOCNO,$DOCID_REF,$REMARKS,$CYID_REF,$BRID_REF,$FYID_REF,            
            $XMLMAT,$XMLBARC,$VTID_REF,$USERID,Date('Y-m-d'),
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];  

       // dd($log_data); 

        $sp_result = DB::select('EXEC SP_BARCODE_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?  ,?,?', $log_data);  

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

            $objResponse =  DB::table('TBL_TRN_BARCODE_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('BRCID','=',$id)
            ->first();

           // dd($objResponse); 
           
            $objlastdt          =   $this->getLastdt();
      
            
            //Material Tab         
            $objMAT = DB::select("SELECT 
            T1.*,T2.ICODE,T2.NAME AS ITEM_NAME,T2.ITEM_SPECI,T3.UOMCODE,T3.DESCRIPTIONS,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO
            FROM TBL_TRN_BARCODE_MAT T1
            LEFT JOIN TBL_TRN_BARCODE_HDR H ON T1.BRCID_REF=H.BRCID
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            WHERE T1.BRCID_REF='$id' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF' ORDER BY T1.BRC_MATID ASC");     


            $objMATDetail = DB::select("SELECT 
            T1.*,T2.ICODE,T2.NAME AS ITEM_NAME,T2.ITEM_SPECI,T3.UOMCODE,T3.DESCRIPTIONS,T4.SERIALNO_MODE,T4.SRNOA AS SERIAL_NO_APPLICABLE,T5.UOMCODE AS UOMCODE1 ,T5.DESCRIPTIONS AS DESCRIPTIONS1,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO
            FROM TBL_TRN_BARCODE_BRC T1
            LEFT JOIN TBL_TRN_BARCODE_HDR H ON T1.BRCID_REF=H.BRCID
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID			
            LEFT JOIN TBL_MST_ITEMCHECKFLAG T4 ON T2.ITEMID=T4.ITEMID_REF
			LEFT JOIN TBL_MST_UOM T5 ON T1.UNIT_REF=T5.UOMID
            WHERE T1.BRCID_REF='$id' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF' ORDER BY T1.BRC_BRCID ASC");  

        //     dd($objMATDetail); 


        if(isset($objMATDetail) && !empty($objMATDetail)){
            foreach($objMATDetail as $key=>$val){

          

            $ObjData =  DB::select('SELECT top 1 TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV  
                WHERE  ITEMID_REF = ? AND TO_UOMID_REF =?', [$val->ITEMID_REF,$val->UNIT_REF]);


            if(!empty($ObjData)){ 
            $auomqty = ($val->QTY/$ObjData[0]->FROM_QTY)/($ObjData[0]->TO_QTY);
            $objMATDetail[$key]->WEIGHT_QTY=number_format(($auomqty),3); 
            }else{
            $objMATDetail[$key]->WEIGHT_QTY ='0';
            }

            }
        } 

            $FormId         =   $this->form_id;
            $AlpsStatus     =   $this->AlpsStatus();
            $ActionStatus   =   "";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
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

            $objResponse =  DB::table('TBL_TRN_BARCODE_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('BRCID','=',$id)
            ->first();

           // dd($objResponse); 
           
            $objlastdt          =   $this->getLastdt();
      
            
            //Material Tab         
            $objMAT = DB::select("SELECT 
            T1.*,T2.ICODE,T2.NAME AS ITEM_NAME,T2.ITEM_SPECI,T3.UOMCODE,T3.DESCRIPTIONS,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO
            FROM TBL_TRN_BARCODE_MAT T1
            LEFT JOIN TBL_TRN_BARCODE_HDR H ON T1.BRCID_REF=H.BRCID
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
            WHERE T1.BRCID_REF='$id' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF' ORDER BY T1.BRC_MATID ASC");     


            $objMATDetail = DB::select("SELECT 
            T1.*,T2.ICODE,T2.NAME AS ITEM_NAME,T2.ITEM_SPECI,T3.UOMCODE,T3.DESCRIPTIONS,T4.SERIALNO_MODE,T4.SRNOA AS SERIAL_NO_APPLICABLE,T5.UOMCODE AS UOMCODE1 ,T5.DESCRIPTIONS AS DESCRIPTIONS1,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO
            FROM TBL_TRN_BARCODE_BRC T1
            LEFT JOIN TBL_TRN_BARCODE_HDR H ON T1.BRCID_REF=H.BRCID
            LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
            LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID			
            LEFT JOIN TBL_MST_ITEMCHECKFLAG T4 ON T2.ITEMID=T4.ITEMID_REF
			LEFT JOIN TBL_MST_UOM T5 ON T1.UNIT_REF=T5.UOMID
            WHERE T1.BRCID_REF='$id' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF' ORDER BY T1.BRC_BRCID ASC");  

        //     dd($objMATDetail); 

            $FormId         =   $this->form_id;
            $AlpsStatus     =   $this->AlpsStatus();
            $ActionStatus   =   "disabled";

            $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
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
                    'RECEIVED_QTY'     => (!empty($request['Main_RECEIVED_QTY_'.$i]) ? $request['Main_RECEIVED_QTY_'.$i] : 0),
                    'ALT_QTY'          => (!empty($request['Alt_RECEIVED_QTY_'.$i]) ? $request['Alt_RECEIVED_QTY_'.$i] : 0),
                    'SERIALNOAPL'      => isset($request['SERIAL_NO_'.$i]) ? 1:0 ,
                    'BARCODEAPL'       => isset($request['BARCODE_'.$i]) ? 1:0 ,                   
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
                    'RECEIVED_QTY'      => $request['REQ_RETURN_QTY_'.$i],
                    'SERIALNUMBER'      => $request['REQ_SERIALNO_'.$i],      
                    'WEIGHT'    	    => isset($request['WEIGHT_QTY_'.$i]) && $request['WEIGHT_QTY_'.$i]!='' ? $request['WEIGHT_QTY_'.$i]:0 ,         
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

        $BRC_NO        = $request['BRC_NO'];
        $BRC_DT        = $request['BRC_DT'];
        $DOCTYPE       = $request['DOCTYPE'];
        $SOURCE_DOCNO  = $request['Documentpopup'];
        $DOCID_REF     = $request['DOCID_REF'];
        $REMARKS       = $request['HEADER_REMARKS'];



        
        $log_data = [ 
            $BRC_NO,$BRC_DT,$DOCTYPE,$SOURCE_DOCNO,$DOCID_REF,$REMARKS,$CYID_REF,$BRID_REF,$FYID_REF,            
            $XMLMAT,$XMLBARC,$VTID_REF,$USERID,Date('Y-m-d'),
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];  

      //dd($log_data);       

        $sp_result = DB::select('EXEC SP_BARCODE_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?  ,?,?', $log_data);  
        //dd($sp_result);
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $BRC_NO. ' Sucessfully Updated.']);

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
                    'RECEIVED_QTY'     => (!empty($request['Main_RECEIVED_QTY_'.$i]) ? $request['Main_RECEIVED_QTY_'.$i] : 0),
                    'ALT_QTY'          => (!empty($request['Alt_RECEIVED_QTY_'.$i]) ? $request['Alt_RECEIVED_QTY_'.$i] : 0),
                    'SERIALNOAPL'      => isset($request['SERIAL_NO_'.$i]) ? 1:0 ,
                    'BARCODEAPL'       => isset($request['BARCODE_'.$i]) ? 1:0 ,                   
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
                    'RECEIVED_QTY'      => $request['REQ_RETURN_QTY_'.$i],
                    'SERIALNUMBER'      => $request['REQ_SERIALNO_'.$i],        
                    'WEIGHT'    	    => isset($request['WEIGHT_QTY_'.$i]) && $request['WEIGHT_QTY_'.$i]!='' ? $request['WEIGHT_QTY_'.$i]:0 ,       
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

        $BRC_NO        = $request['BRC_NO'];
        $BRC_DT        = $request['BRC_DT'];
        $DOCTYPE       = $request['DOCTYPE'];
        $SOURCE_DOCNO  = $request['Documentpopup'];
        $DOCID_REF     = $request['DOCID_REF'];
        $REMARKS       = $request['HEADER_REMARKS'];
        
        $log_data = [ 
            $BRC_NO,$BRC_DT,$DOCTYPE,$SOURCE_DOCNO,$DOCID_REF,$REMARKS,$CYID_REF,$BRID_REF,$FYID_REF,            
            $XMLMAT,$XMLBARC,$VTID_REF,$USERID,Date('Y-m-d'),
            Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
        ];  

        //dd($log_data); 
      


        $sp_result = DB::select('EXEC SP_BARCODE_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?  ,?,?', $log_data);  
        
        //dd($sp_result);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $BRC_NO. ' Sucessfully Approved.']);

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
        $TABLE          =   "TBL_TRN_BARCODE_HDR";
        $FIELD          =   "BRCID";
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
        $TABLE      =   "TBL_TRN_BARCODE_HDR";
        $FIELD      =   "BRCID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
         'NT'  => 'TBL_TRN_BARCODE_MAT',
        ];
        $req_data[1]=[
        'NT'  => 'TBL_TRN_BARCODE_BRC',
        ];

        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $salesorder_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_BARCODE  ?,?,?,?, ?,?,?,?, ?,?,?,?', $salesorder_cancel_data);

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

            $objResponse = DB::table('TBL_TRN_BARCODE_HDR')->where('BRCID','=',$id)->first();

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
        
		$image_path         =   "docs/company".$CYID_REF."/Barcode";     
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

        //dd($request->all()); 
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

        $doctype   =   $request['doctype'];        
        $DOCTYPE_LIST=array("SALESRETURN","STTOSTTRANSFER","PR"); 
        $ReturnDoctype=0;
        if(in_array($doctype,$DOCTYPE_LIST) && $checkCompany==''){
            $ReturnDoctype=1;
        }

    
      
        $main_itemid    =   $request['main_itemid'];
        $action_type    =   $request['action_type'];
        $recordid       =   $request['recordid'];
        
        $material_array=array();
       if(!empty($item_array)){
        foreach($item_array as $key=>$val){

            $exp                        =   explode("_",$val);
           // dd($exp); 
            $Main_ITEMID_REF            =   $exp[0];
            $Main_MAIN_UOMID_REF        =   $exp[1];
            $Main_RECEIVED_QTY          =   $exp[2];
            $Main_RECEIVED              =   $exp[2];
            if($checkCompany==''){
            $Main_RECEIVED_QTY           =   isset($exp[3]) && $exp[3]==''? 0:$exp[3];   
            }
            //dd($Main_RECEIVED_QTY);
    
            $mitem_id                   =   $Main_ITEMID_REF."_".$Main_MAIN_UOMID_REF."-".$Main_RECEIVED_QTY;

     
            $BARCODE_REQ    =   DB::select("SELECT T2.ITEMID,T2.ICODE,T2.NAME,T2.MAIN_UOMID_REF,CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOMCODE,
            IC.SRNOA AS SERIAL_NO_APPLICABLE,IC.BARCODE_APPLICABLE,IC.SERIALNO_MODE,IC.SERIALNO_PREFIX,IC.SERIALNO_STARTS_FROM,IC.SERIALNO_SUFFIX,IC.SERIALNO_MAX_LENGTH,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.ALT_UOMID_REF,CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS ALT_UOMCODE
            ,'' AS SERIALNUMBER,'' AS QTY
            FROM TBL_MST_ITEM T2           
            LEFT JOIN TBL_MST_UOM T3 ON T2.MAIN_UOMID_REF=T3.UOMID
            LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID
            LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
            WHERE T2.ITEMID=$Main_ITEMID_REF");

            if($Main_RECEIVED_QTY > 0){
            $AutoCalQty= number_format(($Main_RECEIVED/ $Main_RECEIVED_QTY),0); 
            }
            // dd($AutoCalQty); 
            // dd($BARCODE_REQ); 

            $SavedItemId=array(); 
            if($action_type=='EDIT'){        
            $SavedItemIds    =   DB::select("SELECT DISTINCT ITEMID_REF FROM TBL_TRN_BARCODE_BRC WHERE BRCID_REF=$recordid");
            $SavedItemId=array(); 
            foreach($SavedItemIds as $key=>$Ilist){
                $SavedItemId[]=$Ilist->ITEMID_REF; 
                }
            }

            //dd($SavedItemId); 

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
                        'QTY'                   =>$row->QTY=='' ? $AutoCalQty:$row->QTY ,                   
                    );

                }else if($action_type!='EDIT'){

                  //  dd($AutoCalQty);
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
                        'QTY'                   =>isset($AutoCalQty) ? $AutoCalQty :''                  
                    );


                }
                    
                }
            }

    
            
        }
    }

        

//dd($material_array); 
        //$Main_RECEIVED_QTY=5; 
        
        $objAutoGenNo='';
        $material_array_final=array();
        foreach($material_array as $index=>$row_data){

           

            $r_count1=range(1,intval($row_data['Main_RECEIVED_QTY']));

            
            if(isset($row_data['SERIAL_NO_APPLICABLE']) && $row_data['SERIAL_NO_APPLICABLE'] == 1)
                {

            $objAutoGenNo='';
            if($row_data['Main_RECEIVED_QTY']!=0){

                $ITEMID_REF=$row_data["ITEMID_REF"];

                $existing_count     =    DB::select("SELECT COUNT(*) as EXISTING_SERIALNO FROM TBL_TRN_BARCODE_BRC M 
                                        LEFT JOIN TBL_TRN_BARCODE_HDR H ON H.BRCID=M.BRCID_REF
                                        WHERE M.ITEMID_REF='$ITEMID_REF' AND H.BRID_REF='$BRID_REF' AND H.CYID_REF='$CYID_REF'");
                $ExistingNo         =   isset($existing_count[0]->EXISTING_SERIALNO) ? $existing_count[0]->EXISTING_SERIALNO :0;    
               
        foreach($r_count1 as $key=>$data){

           // dd($AutoCalQty);
            $key=$ExistingNo+$key;
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
                'MAIN_ITEM_ROWID'       =>$mitem_id,                   
                'SERIAL_NO_APPLICABLE'  =>$row_data['SERIAL_NO_APPLICABLE'] ,                   
                'BARCODE_APPLICABLE'    =>$row_data['BARCODE_APPLICABLE'] ,                   
                'SERIALNO_MODE'         =>$row_data['SERIALNO_MODE'] ,                   
                'SERIALNO_PREFIX'       =>$row_data['SERIALNO_PREFIX'] ,                   
                'SERIALNO_STARTS_FROM'  =>$row_data['SERIALNO_STARTS_FROM'] ,                   
                'SERIALNO_SUFFIX'       =>$row_data['SERIALNO_SUFFIX'] ,                   
                'SERIALNO_MAX_LENGTH'   =>$row_data['SERIALNO_MAX_LENGTH'] ,   
                'REQ_RETURN_QTY'        =>$received_qty ,                   
                'S_NO'                  =>$objAutoGenNo ,   
                'SERIALNUMBER'          =>$row_data['SERIALNUMBER'] ,                   
                'QTY'                   =>$row_data['QTY'] ,      
                          
            );



            }
        }
    }else{       

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
            'MAIN_ITEM_ROWID'       =>$mitem_id,                      
            'SERIAL_NO_APPLICABLE'  =>$row_data['SERIAL_NO_APPLICABLE'] ,                   
            'BARCODE_APPLICABLE'    =>$row_data['BARCODE_APPLICABLE'] ,                   
            'SERIALNO_MODE'         =>$row_data['SERIALNO_MODE'] ,                   
            'SERIALNO_PREFIX'       =>$row_data['SERIALNO_PREFIX'] ,                   
            'SERIALNO_STARTS_FROM'  =>$row_data['SERIALNO_STARTS_FROM'] ,                   
            'SERIALNO_SUFFIX'       =>$row_data['SERIALNO_SUFFIX'] ,                   
            'SERIALNO_MAX_LENGTH'   =>$row_data['SERIALNO_MAX_LENGTH'] ,   
            'REQ_RETURN_QTY'        =>$Main_RECEIVED_QTY ,                   
            'S_NO'                  =>$objAutoGenNo ,    
            'SERIALNUMBER'          =>$row_data['SERIALNUMBER'] ,                   
            'QTY'                   =>$row_data['QTY'],     
                      
        );

    }
        }


        //dd($material_array_final); 


       if($action_type=="EDIT"){
        $BARCODE_REQ    =   DB::select("SELECT T2.ITEMID,T2.ICODE,T2.NAME,T2.MAIN_UOMID_REF,CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOMCODE,
        IC.SRNOA AS SERIAL_NO_APPLICABLE,IC.BARCODE_APPLICABLE,IC.SERIALNO_MODE,IC.SERIALNO_PREFIX,IC.SERIALNO_STARTS_FROM,IC.SERIALNO_SUFFIX,IC.SERIALNO_MAX_LENGTH,T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,ISNULL(T1.UOMID_REF,T2.ALT_UOMID_REF) AS ALT_UOMID_REF,
        CASE WHEN T4.UOMCODE <> '' THEN CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) ELSE CONCAT(T5.UOMCODE,'-',T5.DESCRIPTIONS) END AS ALT_UOMCODE
        ,T1.SERIALNUMBER,T1.QTY
        FROM TBL_MST_ITEM T2           
        LEFT JOIN TBL_TRN_BARCODE_BRC T1 ON T2.ITEMID=T1.ITEMID_REF
        LEFT JOIN TBL_MST_UOM T3 ON T2.MAIN_UOMID_REF=T3.UOMID
        LEFT JOIN TBL_MST_UOM T4 ON T1.UOMID_REF=T4.UOMID
        LEFT JOIN TBL_MST_UOM T5 ON T2.ALT_UOMID_REF=T5.UOMID
        LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
        WHERE  T1.BRCID_REF=$recordid 
        ");




        foreach($BARCODE_REQ as $index=>$row_data){

            if(!empty($main_itemid) && in_array($row_data->ITEMID,$main_itemid)){

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
                            <th>Received Qty</th>
                            <th '.$checkCompany.'>Unit</th>
                            <th '.$checkCompany.'>Qty</th>                   
                            <th '.$checkCompany.'>Weight</th>                   
                            <th>Serial No</th>
                
                        </tr>
                    </thead>
                    <tbody>';

                    foreach($material_array_final as $index=>$row_data){        
                    $WEIGHT=$this->GetWeight($row_data['ITEMID_REF'],$row_data['ALT_UOMID_REF'],$row_data['QTY']);
                    //dd($WEIGHT); 
                    $SERIALNO_MODE=isset($row_data['SERIALNO_MODE']) && $row_data['SERIALNO_MODE'] == "AUTOMATIC" ? "readonly":""; 
                     $Qty_STATUS=$row_data['SERIAL_NO_APPLICABLE']==1  ? "":"readonly"; 
                    
                     if($ReturnDoctype==1 && $action_type=="")
                     {  $SERIALNO_MODE="readonly";
                        $row_data['S_NO']="";                   
                     }
                     else if($ReturnDoctype==1 && $action_type=="EDIT" && isset($SavedItemId) && !in_array($row_data['ITEMID_REF'],$SavedItemId) )
                     {
                        $SERIALNO_MODE="readonly";
                        $row_data['S_NO']="";  
                     }
                  

                        echo '<tr  class="participantRow8">';
                        echo '<td><input type="text" id="txtSUBITEM_popup_'.$index.'" value="'.$row_data['ICODE'].'" class="form-control" readonly  /></td>';
                        echo '<td><input type="text" id="SUBITEM_NAME_'.$index.'"     value="'.$row_data['NAME'].'"  class="form-control" readonly  /></td>';

                        echo '<td '.$AlpsStatus['hidden'].'><input type="text"  id="ALPS_PART_NO_'.$index.'"     value="'.$row_data['ALPS_PART_NO'].'"  class="form-control" readonly  /></td>';
                        echo '<td '.$AlpsStatus['hidden'].'><input type="text"  id="CUSTOMER_PART_NO_'.$index.'"     value="'.$row_data['CUSTOMER_PART_NO'].'"  class="form-control" readonly  /></td>';
                        echo '<td '.$AlpsStatus['hidden'].'><input type="text"  id="OEM_PART_NO_'.$index.'"     value="'.$row_data['OEM_PART_NO'].'"  class="form-control" readonly  /></td>';
                        
       
                        echo '<td hidden><input type="text"   name="REQ_ITEMID_REF_'.$index.'"   id="REQ_ITEMID_REF_'.$index.'"   value="'.$row_data['ITEMID_REF'].'" /></td>';                        
                   
                        echo '<td><input    type="text" name="REQ_MAIN_UOM_'.$index.'"           id="REQ_MAIN_UOM_'.$index.'"     value="'.$row_data['MAIN_UOMCODE'].'"   class="form-control" readonly   /></td>';

                        echo '<td hidden><input type="hidden" name="REQ_UOMID_REF_'.$index.'"    id="REQ_UOMID_REF_'.$index.'"    value="'.$row_data['MAIN_UOMID_REF'].'" /></td>';

                        echo '<td ><input type="text" style="text-align:right;" name="REQ_RETURN_QTY_'.$index.'" class="form-control" readonly   id="REQ_RETURN_QTY_'.$index.'" 
                        value="'.number_format($row_data['REQ_RETURN_QTY'],2).'"  /></td>';
                        

                        echo '<td '.$checkCompany.' ><input type="text" name="PACKUOM_'.$index.'" class="form-control" readonly  onclick="getUOM(this.id,'.$row_data['ITEMID_REF'].')"   id="PACKUOM_'.$index.'"  value="'.$row_data['ALT_UOMCODE'].'"  /></td>';
                        echo '<td hidden ><input type="text" name="PACKUOMID_REF_'.$index.'"    id="PACKUOMID_REF_'.$index.'" value="'.$row_data['ALT_UOMID_REF'].'"/></td>';                                    

                        echo '<td '.$checkCompany.' ><input type="text" name="REQ_QTY_'.$index.'" onkeyup="dataCal(this.id)" class="form-control"    id="REQ_QTY_'.$index.'"  value="'.$row_data['QTY'].'"  /></td>';

                        echo '<td '.$checkCompany.' ><input type="text" name="REQ_WEIGHT_'.$index.'" class="form-control"    id="REQ_WEIGHT_'.$index.'"  value="'.$WEIGHT.'"  /></td>';

                        echo '<td ><input type="text" name="REQ_SERIALNO_'.$index.'" class="form-control"   id="REQ_SERIALNO_'.$index.'"  value="'.$row_data['S_NO'].'"  '.$SERIALNO_MODE.'
                        /></td>';          
                        
                      



            echo '<td hidden ><input type="text" name="SERIAL_NO_APPLICABLE_'.$index.'" class="form-control"   id="SERIAL_NO_APPLICABLE_'.$index.'"   value="'.$row_data['SERIAL_NO_APPLICABLE'].'" /></td>';   
            echo '<td hidden><input type="text" name="BARCODE_APPLICABLE_'.$index.'" class="form-control"   id="BARCODE_APPLICABLE_'.$index.'"   value="'.$row_data['BARCODE_APPLICABLE'].'" /></td>';   
                        
       
                      
                    
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
             $Status = "N";
             $CYID_REF = Auth::user()->CYID_REF;
             $BRID_REF = Session::get('BRID_REF');
             $FYID_REF = Session::get('FYID_REF');       
             if($DOCTYPE=='GRN'){
                    $objdata = DB::table('TBL_TRN_IGRN02_HDR')
                    ->where('TBL_TRN_IGRN02_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('TBL_TRN_IGRN02_HDR.BRID_REF','=',Session::get('BRID_REF'))
                    ->where('TBL_TRN_IGRN02_HDR.STATUS','=',$Status)     
                    ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_TRN_IGRN02_HDR.VID_REF','=','TBL_MST_SUBLEDGER.SGLID')
                    ->select('TBL_TRN_IGRN02_HDR.GRNID AS DOCID','TBL_TRN_IGRN02_HDR.GRN_NO AS DOC_COL1','TBL_TRN_IGRN02_HDR.GRN_DT AS DOC_COL2','TBL_MST_SUBLEDGER.SLNAME AS DOC_COL3') 
                    ->get()    
                    ->toArray();

             //dd($objdata); 
             }else if($DOCTYPE=='RGP'){
                    $objdata = DB::table('TBL_TRN_IGRN01_HDR')
                    ->where('TBL_TRN_IGRN01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('TBL_TRN_IGRN01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                    ->where('TBL_TRN_IGRN01_HDR.STATUS','=',$Status)     
                    ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_TRN_IGRN01_HDR.VID_REF','=','TBL_MST_SUBLEDGER.SGLID')
                    ->select('TBL_TRN_IGRN01_HDR.GRNID AS DOCID','TBL_TRN_IGRN01_HDR.GRN_NO AS DOC_COL1','TBL_TRN_IGRN01_HDR.GRN_DT AS DOC_COL2','TBL_MST_SUBLEDGER.SLNAME AS DOC_COL3') 
                    ->get()    
                    ->toArray(); 

                   // dd($objdata); 


             }else if($DOCTYPE=='STADJUSTMENT'){
                    $objdata = DB::select("SELECT ST_ADJUSTID AS DOCID,ST_ADJUST_DOCNO AS DOC_COL1,ST_ADJUST_DOCDT AS DOC_COL2,'' AS DOC_COL3 FROM TBL_TRN_STOCK_ADJUST_HDR
                    WHERE STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF'");   
                    }else if($DOCTYPE=='STTOSTTRANSFER'){
                    $objdata = DB::select("SELECT ST_STID AS DOCID,ST_ST_DOCNO AS DOC_COL1,ST_ST_DOCDT AS DOC_COL2,'' AS DOC_COL3 FROM TBL_TRN_STOCK_STOCK_HDR
                    WHERE STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF' ");
                    }else if($DOCTYPE=='PMOVEMENT'){
                    $objdata = DB::select("SELECT PNMID AS DOCID,PNM_NO AS DOC_COL1,PNM_DT AS DOC_COL2,'' AS DOC_COL3 FROM TBL_TRN_PDPNM_HDR
                    WHERE STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF' ");  
             }else if($DOCTYPE=='JOBWORKGRN'){
                    $objdata = DB::table('TBL_TRN_GRJ_HDR')
                    ->where('TBL_TRN_GRJ_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('TBL_TRN_GRJ_HDR.BRID_REF','=',Session::get('BRID_REF'))
                    ->where('TBL_TRN_GRJ_HDR.STATUS','=',$Status)     
                    ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_TRN_GRJ_HDR.VID_REF','=','TBL_MST_SUBLEDGER.SGLID')
                    ->select('TBL_TRN_GRJ_HDR.GRJID AS DOCID','TBL_TRN_GRJ_HDR.GRNNO AS DOC_COL1','TBL_TRN_GRJ_HDR.GRNDT AS DOC_COL2','TBL_MST_SUBLEDGER.SLNAME AS DOC_COL3') 
                    ->get()    
                    ->toArray();  
             }
             else if($DOCTYPE=='SALESRETURN'){
                $objdata = DB::table('TBL_TRN_SLSR01_HDR')
                ->where('TBL_TRN_SLSR01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                ->where('TBL_TRN_SLSR01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                ->where('TBL_TRN_SLSR01_HDR.STATUS','=',$Status)     
                ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_TRN_SLSR01_HDR.SLID_REF','=','TBL_MST_SUBLEDGER.SGLID')
                ->select('TBL_TRN_SLSR01_HDR.SRID AS DOCID','TBL_TRN_SLSR01_HDR.SRNO AS DOC_COL1','TBL_TRN_SLSR01_HDR.SRDT AS DOC_COL2','TBL_MST_SUBLEDGER.SLNAME AS DOC_COL3') 
                ->get()    
                ->toArray();  
             }
             else if($DOCTYPE=='CSV'){
                $objdata = DB::table('TBL_TRN_CRSV01_HDR')
                ->where('TBL_TRN_CRSV01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                ->where('TBL_TRN_CRSV01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                ->where('TBL_TRN_CRSV01_HDR.STATUS','=',$Status)     
                ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_TRN_CRSV01_HDR.SGLID_REF','=','TBL_MST_SUBLEDGER.SGLID')
                ->select('TBL_TRN_CRSV01_HDR.CSVID AS DOCID','TBL_TRN_CRSV01_HDR.CSV_NO AS DOC_COL1','TBL_TRN_CRSV01_HDR.CSV_DT AS DOC_COL2','TBL_MST_SUBLEDGER.SLNAME AS DOC_COL3') 
                ->get()    
                ->toArray();  
             }
             else if($DOCTYPE=='DSV'){
                $objdata = DB::table('TBL_TRN_DRSV01_HDR')
                ->where('TBL_TRN_DRSV01_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                ->where('TBL_TRN_DRSV01_HDR.BRID_REF','=',Session::get('BRID_REF'))
                ->where('TBL_TRN_DRSV01_HDR.STATUS','=',$Status)     
                ->leftJoin('TBL_MST_SUBLEDGER', 'TBL_TRN_DRSV01_HDR.SGLID_REF','=','TBL_MST_SUBLEDGER.SGLID')
                ->select('TBL_TRN_DRSV01_HDR.DSVID AS DOCID','TBL_TRN_DRSV01_HDR.DSV_NO AS DOC_COL1','TBL_TRN_DRSV01_HDR.DSV_DT AS DOC_COL2','TBL_MST_SUBLEDGER.SLNAME AS DOC_COL3') 
                ->get()    
                ->toArray();  
             }
             else if($DOCTYPE=='PR'){
                $objdata = DB::select("SELECT PRRID AS DOCID,PRR_NO AS DOC_COL1,PRR_DT AS DOC_COL2,'' AS DOC_COL3 FROM          TBL_TRN_PDPRR_HDR
                WHERE STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF' ");
             }
             else if($DOCTYPE=='IO'){
                $objdata = DB::select("SELECT IOBID AS DOCID,CONCAT(IOBID,'/',OPENING_BL_DT) AS DOC_COL1,OPENING_BL_DT AS DOC_COL2,'' AS DOC_COL3 FROM TBL_MST_ITEM_OB_HDR
                WHERE STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF' ");
             } else if($DOCTYPE=='ASSEMBLING'){
                $objdata = DB::select("SELECT ADSMID AS DOCID,ADSMNO AS DOC_COL1,ADSMDT AS DOC_COL2,'' AS DOC_COL3 FROM TBL_TRN_ASMDSM_HDR
                WHERE TYPE='ASSEMBLING' AND STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF'   ");
             }else if($DOCTYPE=='DISSEMBLING'){
                $objdata = DB::select("SELECT ADSMID AS DOCID,ADSMNO AS DOC_COL1,ADSMDT AS DOC_COL2,'' AS DOC_COL3 FROM TBL_TRN_ASMDSM_HDR
                WHERE TYPE='DISSEMBLING' AND STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF'   ");
             }
         
           // dd($objdata); 
              
         
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
                $CYID_REF    = Auth::user()->CYID_REF;
                $BRID_REF    = Session::get('BRID_REF');
                $FYID_REF    = Session::get('FYID_REF');
                $StdCost        = 0;
                $Taxid = [];
        
                $AlpsStatus         =   $this->AlpsStatus();           
                $ObjItem =[];

               
               if($docType=='GRN'){
               $ObjItem =  DB::select("SELECT 
               T1.*,
               T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
               T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
               CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
               CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
               IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
               FROM TBL_TRN_IGRN02_MAT T1 
               LEFT JOIN TBL_TRN_IGRN02_HDR H ON T1.GRNID_REF=H.GRNID               
               LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
               LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
               LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
               LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
               LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
               WHERE H.GRNID='$DOCID_REF' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
               ORDER BY T1.GRN_MATID ASC
               
                ");
            }else if($docType=='RGP'){
                $ObjItem =  DB::select("SELECT 
                T1.*,
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
                T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
                FROM TBL_TRN_IGRN01_MAT T1 
                LEFT JOIN TBL_TRN_IGRN01_HDR H ON T1.GRNID_REF=H.GRNID               
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
                LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
                LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
                WHERE H.GRNID='$DOCID_REF' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                ORDER BY T1.GRN_MATID ASC
                 ");

                

            }else if($docType=='STADJUSTMENT'){
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

            }else if($docType=='STTOSTTRANSFER'){
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

            }else if($docType=='PMOVEMENT'){
                $ObjItem =  DB::select("SELECT DISTINCT T1.ITEMID_REF,T1.PNMID_REF,H.ACTUAL_QTY AS RECEIVED_QTY_MU,T1.UOMID_REF AS MAIN_UOMID_REF,
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
                T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
                FROM TBL_TRN_PDPNM_MACHINE T1 
                LEFT JOIN TBL_TRN_PDPNM_HDR H ON T1.PNMID_REF=H.PNMID               
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
                LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
                LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID            
                WHERE H.PNMID='$DOCID_REF'  AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'
                 ");

            }else if($docType=='JOBWORKGRN'){
                $ObjItem =  DB::select("SELECT 
                T1.*,T1.RECEIVED_QTY AS RECEIVED_QTY_MU,T1.UOMID_REF AS MAIN_UOMID_REF,
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
                T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
                FROM TBL_TRN_GRJ_MAT T1 
                LEFT JOIN TBL_TRN_GRJ_HDR H ON T1.GRJID_REF=H.GRJID               
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
                LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
                LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
                WHERE H.GRJID='$DOCID_REF' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                ORDER BY T1.GRJ_MATID ASC
                 ");

            }else if($docType=='SALESRETURN'){
                $ObjItem =  DB::select("SELECT 
                T1.*,T1.SRQTY AS RECEIVED_QTY_MU,T1.MAIN_SRUOMID_REF AS MAIN_UOMID_REF,
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
                T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
                FROM TBL_TRN_SLSR01_MAT T1 
                LEFT JOIN TBL_TRN_SLSR01_HDR H ON T1.SRID_REF=H.SRID               
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
                LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
                LEFT JOIN TBL_MST_UOM T3 ON T1.MAIN_SRUOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
                WHERE H.SRID='$DOCID_REF' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                ORDER BY T1.SRMATID ASC
                 ");

            }else if($docType=='CSV'){
                $ObjItem =  DB::select("SELECT 
                T1.*,T1.CR_NOTE_QTY AS RECEIVED_QTY_MU,T1.UOMID_REF AS MAIN_UOMID_REF,
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
                T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
                FROM TBL_TRN_CRSV01_MAT T1 
                LEFT JOIN TBL_TRN_CRSV01_HDR H ON T1.CSVID_REF=H.CSVID               
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
                LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
                LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
                WHERE H.CSVID='$DOCID_REF' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                ORDER BY T1.CSV_MATID ASC
                 ");

            }else if($docType=='DSV'){
                $ObjItem =  DB::select("SELECT 
                T1.*,T1.DR_NOTE_QTY AS RECEIVED_QTY_MU,T1.UOMID_REF AS MAIN_UOMID_REF,
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
                T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
                FROM TBL_TRN_DRSV01_MAT T1 
                LEFT JOIN TBL_TRN_DRSV01_HDR H ON T1.DSVID_REF=H.DSVID               
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
                LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
                LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
                WHERE H.DSVID='$DOCID_REF' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                ORDER BY T1.DSV_MATID ASC
                 ");

            
            }else if($docType=='PR'){
                $ObjItem =  DB::select("SELECT 
                T1.*,T1.RETURNQTY AS RECEIVED_QTY_MU,T1.UOMID_REF AS MAIN_UOMID_REF,
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
                T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
                FROM TBL_TRN_PDPRR_MAT T1 
                LEFT JOIN TBL_TRN_PDPRR_HDR H ON T1.PRRID_REF=H.PRRID               
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
                LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
                LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
                WHERE H.PRRID='$DOCID_REF' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                ORDER BY T1.PRR_MATID ASC
                 ");

            }else if($docType=='IO'){

                $UsedItem   =   DB::select("SELECT T1.ITEMID_REF FROM TBL_TRN_BARCODE_MAT T1
                LEFT JOIN TBL_TRN_BARCODE_HDR H ON T1.BRCID_REF=H.BRCID  
                WHERE H.SOURCE_TYPE='IO' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF' ");


              
                foreach($UsedItem as $key=>$Items){
                    $ITEMID_REF[]=$Items->ITEMID_REF;
                }
                $CONDITION="";
                if(!empty($ITEMID_REF)){
                $ITEMID_REF=implode(",",$ITEMID_REF); 
                $CONDITION  =   "AND T1.ITEMID_REF NOT IN ($ITEMID_REF)";

                }
                $ObjItem =  DB::select("SELECT 
                T1.*,T1.OPENING_BL AS RECEIVED_QTY_MU,T1.UOMID_REF AS MAIN_UOMID_REF,
                T2.ITEMID,T2.ICODE,T2.NAME,T2.ICID_REF,T2.ITEMGID_REF,T2.ALT_UOMID_REF,
                T2.ALPS_PART_NO,T2.CUSTOMER_PART_NO,T2.OEM_PART_NO,T2.BUID_REF,T2.MAIN_UOMID_REF,T2.ALT_UOMID_REF,T2.ITEM_SPECI,
                CONCAT(T3.UOMCODE,'-',T3.DESCRIPTIONS) AS MAIN_UOM_CODE,
                CONCAT(T4.UOMCODE,'-',T4.DESCRIPTIONS) AS AULT_UOM_CODE,
                IC.SRNOA AS SERIAL_NO, IC.BARCODE_APPLICABLE AS BARCODE
                FROM TBL_MST_ITEM_OB_MAT T1 
                LEFT JOIN TBL_MST_ITEM_OB_HDR H ON T1.IOBID_REF=H.IOBID               
                LEFT JOIN TBL_MST_ITEM T2 ON T1.ITEMID_REF=T2.ITEMID
                LEFT JOIN TBL_MST_ITEMCHECKFLAG IC ON T2.ITEMID=IC.ITEMID_REF
                LEFT JOIN TBL_MST_ITEMGROUP G ON T2.ITEMGID_REF=G.ITEMGID
                LEFT JOIN TBL_MST_UOM T3 ON T1.UOMID_REF=T3.UOMID
                LEFT JOIN TBL_MST_UOM T4 ON T2.ALT_UOMID_REF=T4.UOMID               
                WHERE H.IOBID='$DOCID_REF' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  $CONDITION
                ORDER BY T1.IOB_MATID ASC
                 ");

            }

            else if($docType=='ASSEMBLING'){
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
                WHERE T1.ADSMID='$DOCID_REF' AND T1.TYPE='ASSEMBLING' AND T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF'  
                ORDER BY T1.ADSMID ASC
                 ");
               

            }else if($docType=='DISSEMBLING'){
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
                WHERE H.ADSMID='$DOCID_REF' AND H.TYPE='DISSEMBLING' AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                ORDER BY T1.ADSMMATID ASC
                 ");

            }

   
        
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


                        $ReceivedQty=$this->GetReceivedQty($docType,$DOCID_REF,$dataRow->ITEMID);
                        if($ReceivedQty > 0) {
                            $PENDING_QTY=$PENDING_QTY-$ReceivedQty;   
                        }

                   
        
                        //dd($TOQTY);
        
                        //$FROMQTY =  isset($ObjAltQTY[0]->FROM_QTY)? $ObjAltQTY[0]->FROM_QTY : 0;
                     
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
                $objLabel = DB::table('TBL_TRN_BARCODE_HDR')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Session::get('BRID_REF'))
                ->where('FYID_REF','=',Session::get('FYID_REF'))
                ->where('BRC_NO','=',$PRR_DOCNO)
                ->select('BRC_NO')->first();
        
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
                return  DB::select('SELECT MAX(BRC_DT) BRC_DT FROM TBL_TRN_BARCODE_HDR  
                WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND VTID_REF = ? AND STATUS = ?', 
                [$CYID_REF, $BRID_REF, $FYID_REF, $this->vtid_ref, 'A' ]);
            }



            public function get_STR(Request $request){
                $CYID_REF   =   Auth::user()->CYID_REF;
                $BRID_REF   =   Session::get('BRID_REF');
                $FYID_REF   =   Session::get('FYID_REF');
                $fieldid    =   $request['fieldid'];
                $class_name =   $request['class_name'];
                $ITEMID_REF =   $request['ITEMID_REF'];
                $DOCID_REF  =   $request['DOCID_REF'];
         
                $doctype    =   trim($request['doctype']);
                //dd($doctype); 
                $checkCompany =  $request['checkCompany']==''? "":"hidden";

                    $ObjData1   =   array(); 
                    if($doctype =="SALESRETURN"){
                    $ObjData1   =   DB::select("SELECT DISTINCT T1.SERIALNUMBER,T1.QTY FROM TBL_TRN_BARCODE_OUT_BRC T1
                                    LEFT JOIN TBL_TRN_BARCODE_OUT_HDR H ON T1.BRCOID_REF=H.BRCOID 
                                    LEFT JOIN TBL_TRN_SLSR01_MAT M ON H.DOCID_REF=M.SCID_REF
                                    WHERE H.SOURCE_TYPE='SALES_CHALLAN' AND H.STATUS='A' AND M.SRID_REF='$DOCID_REF'  AND T1.ITEMID_REF=$ITEMID_REF AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF' ");
                    }else if($doctype =="STTOSTTRANSFER"){ 
                    $ObjData1   =   DB::select("SELECT DISTINCT T1.SERIALNUMBER,T1.QTY FROM TBL_TRN_BARCODE_OUT_BRC T1
                                    LEFT JOIN TBL_TRN_BARCODE_OUT_HDR H ON T1.BRCOID_REF=H.BRCOID 
                                    WHERE H.SOURCE_TYPE='STTOSTTRANSAFER' AND H.STATUS='A'AND H.DOCID_REF='$DOCID_REF' AND T1.ITEMID_REF=$ITEMID_REF  AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF' ");
                    
                    }else if($doctype =="PR"){ 
                     
                        $objdata = DB::select("SELECT PROID_REF FROM          TBL_TRN_PDPRR_HDR
                        WHERE PRRID='$DOCID_REF' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF' ");                    
                        $PROID_REF=isset($objdata[0]->PROID_REF) ? $objdata[0]->PROID_REF:'';
                     
                    $ObjData1   =   DB::select("SELECT DISTINCT T1.SERIALNUMBER,T1.QTY FROM TBL_TRN_BARCODE_OUT_BRC T1
                    LEFT JOIN TBL_TRN_BARCODE_OUT_HDR H ON T1.BRCOID_REF=H.BRCOID 
                    LEFT JOIN TBL_TRN_PDMISR_MAT M ON M.MISRID_REF=H.DOCID_REF
                    WHERE H.SOURCE_TYPE='MISR' AND H.STATUS='A' AND M.PROID_REF=$PROID_REF AND T1.ITEMID_REF=$ITEMID_REF AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  ");
                    }
                    
                  //  dd($ObjData1); 
                if(!empty($ObjData1)){
                   foreach ($ObjData1 as $index=>$dataRow){                
                       $row            =   '';
                       $row = $row.'<tr >
                       <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="socode_'.$dataRow->SERIALNUMBER .'"  class="'.$class_name.'" value="'.$dataRow->SERIALNUMBER.'" ></td>
                       <td class="ROW2">'.$dataRow->SERIALNUMBER;
                       $row = $row.'<input type="hidden" id="txtsocode_'.$dataRow->SERIALNUMBER.'" data-desc="'.$dataRow->SERIALNUMBER.'" data-desc1="'.$dataRow->SERIALNUMBER.'" data-desc2="'.$dataRow->QTY.'"   value="'.$dataRow->SERIALNUMBER.'"/></td>
                       <td class="ROW3" '.$checkCompany.' >'.$dataRow->QTY.'</td></tr>';
                       echo $row;                       
                   }                
                }else{
                    echo '<tr><td>Record not found.</td></tr>';
                }
                exit();   
                }




    public function importdata(){
        $objMstVoucherType  =   DB::table("TBL_MST_VOUCHERTYPE")
                                ->where('VTID','=',$this->vtid_ref)
                                ->select('VTID','VCODE','DESCRIPTIONS','INDATE')
                                ->get()
                                ->toArray();

        return view('transactions.inventory.Barcode.trnfrm418importexcel',compact(['objMstVoucherType']));
    }

    public function downloadExcelFormate(){

        $excelfile_path =   "docs/importsamplefiles/BARCODE/SERIAL_NUMBER.xlsx";   
        $custfilename   =   str_replace('\\', '/', public_path($excelfile_path));
        
        $reader         =   \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet    =   $reader->load($custfilename);
        
        $writer         =   new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="SERIAL_NUMBER.xlsx"');
        ob_end_clean();
        $writer->save("php://output");
        return redirect()->back();
    }

    public function importexcelindb(Request $request){
        ini_set('memory_limit', '-1');
        $formData           =   $request->all();                
        $allow_extnesions   =   explode(",",$formData["allow_extensions"]);
        $allow_size         =   (int)$formData["allow_max_size"] * 1024 * 1024;
        $VTID_REF           =   $this->vtid_ref;
        $USERID             =   Auth::user()->USERID;   
        $CYID_REF           =   Auth::user()->CYID_REF;
        $BRID_REF           =   Session::get('BRID_REF');
        $FYID_REF           =   Session::get('FYID_REF');

        if(isset($formData["FILENAME"])){
            $uploadedFile   =   $formData["FILENAME"];
            if($uploadedFile->isValid()){
                $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
                $filesize               =   $uploadedFile ->getSize();  
                $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );
                $inputFileType          =   ucfirst($extension);   // as per API Xls or Xlsx: first charter in upper case
                $filenametostore        =   $VTID_REF.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".Date('YmdHis')."_".$filenamewithextension;  //excel file
                $file_name              =   pathinfo($filenamewithextension, PATHINFO_FILENAME);  // fetch only file name
                $logfile_name           =   "LOG_".$VTID_REF.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".Date('YmdHis')."_".$file_name.".txt";  //log text file
                $excelfile_path         =   "docs/company".$CYID_REF."/Barcode/importexcel";     
                $destinationPath        =   str_replace('\\', '/', public_path($excelfile_path));

                if ( !is_dir($destinationPath) ) {
                    mkdir($destinationPath, 0777, true);
                }

                if(in_array($extension,$allow_extnesions)){
                    
                    if($filesize < $allow_size){

                        $custfilename = $destinationPath."/".$filenametostore;

                        if ( !is_dir($destinationPath) ) {
                            mkdir($destinationPath, 0777, true);
                        } 

                        $uploadedFile->move($destinationPath, $filenametostore);  //upload file in dir if not exists

                        if (file_exists($custfilename)) {

                            try {
                                /** Load $inputFileName to a Spreadsheet Object  **/
                                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                                $reader->setReadDataOnly(true);
                                $spreadsheet = $reader->load($custfilename);
                                $worksheet = $spreadsheet->getActiveSheet();
                                
                                $excelHeaderdata    =   [];
                                $excelAlldata       =   [];

                                foreach ($worksheet->getRowIterator() as $rowindex=>$row) {
                                
                                    $cellIterator = $row->getCellIterator();
                                    
                                    $cellIterator->setIterateOnlyExistingCells(true);   
                                    /* ***** setIterateOnlyExistingCells(true)
                                    This loops through all cells, even if a cell value is not set.
                                    For 'TRUE', we loop through cells, only when their value is set.
                                    If this method is not called, the default value is 'false'.
                                    **** */
                                    foreach ($cellIterator as $index=>$cell) {
                                        if($rowindex==1){
                                            $excelHeaderdata[$index] = trim(strtolower($cell->getValue()) );  // fetch value for making header data
                                        }
                                        else{
                                            $excelAlldata[$rowindex-1][$excelHeaderdata[$index]]= trim($cell->getValue());
                                        }
                                    }                        
                                }
                        
                            } 
                            catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                                return redirect()->route("transaction",[418,"importdata"])->with("error","Error loading file: ".$e->getMessage());
                            }
                        }
                        else{
                            return redirect()->route("transaction",[418,"importdata"])->with("error","There is some file uploading error. Please try again.");
                        }
                    }
                    else{
                        return redirect()->route("transaction",[418,"importdata"])->with("error","Invalid size - Please check.");
                    }
                }
                else{
                    return redirect()->route("transaction",[418,"importdata"])->with("error","Invalid file extension - Please check.");                      
                }
            }
            else{  
                return redirect()->route("transaction",[418,"importdata"])->with("error","Invalid file - Please check.");  
            }
        }
        else{
            return redirect()->route("transaction",[418,"importdata"])->with("error","File not found. - Please check.");  
        }

        $logfile_path   =   $excelfile_path."/".$logfile_name;     

        if(!$logfile = fopen($logfile_path, "a") ){
            return redirect()->route("transaction",[418,"importdata"])->with("error","Log creating file error.");     //create or open log file
        }

        $validationErr          =   false;
        $headerArr              =   []; 
        $unique_doc_code1       =   "";
        //dd($excelAlldata);

            $ArrSerialNo              =   [];
        foreach($excelAlldata as $eIndex=>$eRowData){
            if(isset($eRowData["serial_number"]) && trim($eRowData["serial_number"]) !=''){
            $ArrSerialNo[]  =   $eRowData["serial_number"];
            }
        }
    

        $SerialNo =    $this->showDups($ArrSerialNo);
        if($SerialNo !=""){
            $this->appendLogData($logfile,"Invalid: Given Serial No $SerialNo is duplicate");
            $validationErr=true;
        }
        

        

        foreach($excelAlldata as $eIndex=>$eRowData){
            $doc_no             =   isset($eRowData["doc_no"])?trim($eRowData["doc_no"]):NULL;     
            $reference_type     =   isset($eRowData["reference_type"])?trim($eRowData["reference_type"]):NULL;
            $reference_doc_no   =   isset($eRowData["reference_doc_no"])?trim($eRowData["reference_doc_no"]):NULL;
            $item_code          =   isset($eRowData["item_code"])?trim($eRowData["item_code"]):NULL;
            $serial_number      =   isset($eRowData["serial_number"])?trim($eRowData["serial_number"]):NULL;
     
            if($doc_no ==""){
                $this->appendLogData($logfile,"Invalid: Blank Doc no. check row no ".$eIndex);
                $validationErr=true;
            }

  
            if($reference_type ==""){
                $this->appendLogData($logfile,"Invalid: Blank Reference Doc Type. check row no ".$eIndex);
                $validationErr=true;
            }

            if($reference_doc_no ==""){
                $this->appendLogData($logfile,"Invalid: Blank Reference Doc no. check row no ".$eIndex);
                $validationErr=true;
            }


            if($item_code ==""){
                $this->appendLogData($logfile,"Invalid: Blank Item Code. check row no ".$eIndex);
                $validationErr=true;
            }

            if($serial_number ==""){
                $this->appendLogData($logfile,"Invalid: Blank Serial No. check row no ".$eIndex);
                $validationErr=true;
            }

           /*  if(!empty($this->exist_doc_no($doc_no))){  
                $this->appendLogData($logfile,"Invalid: Already exist Doc no ".$doc_no." check row no ".$eIndex);
                $validationErr=true; 
            } */
  
           
            if($validationErr ==false){

             //   if (!array_key_exists($doc_no, $headerArr)) {            
                    $headerArr[$doc_no]["header"]["doc_no"]                         =   $doc_no;
                    $headerArr[$doc_no]["header"]["reference_type"]                 =   $reference_type;
                    $headerArr[$doc_no]["header"]["reference_doc_no"]               =   $reference_doc_no;
                    $headerArr[$doc_no]["material"][$eIndex]["item_code"]           =   $item_code;
                    $headerArr[$doc_no]["material"][$eIndex]["serial_number"]       =   $serial_number;
             //   }
            }
        }

       

        if($validationErr){
            fclose($logfile);
            return redirect()->route("transaction",[418,"importdata"])->with("logerror",$logfile_path);  
        }

        foreach($headerArr as $hIndex=>$hRowData){         
            $VTID_REF           =   $this->vtid_ref;
            $USERID             =   Auth::user()->USERID;   
            $ACTIONNAME         =   'ADD';
            $IPADDRESS          =   $request->getClientIp();
            $CYID_REF           =   Auth::user()->CYID_REF;
            $BRID_REF           =   Session::get('BRID_REF');
            $FYID_REF           =   Session::get('FYID_REF');
            $DOCNO              =   strtoupper($hRowData["header"]["doc_no"]);
            $SOURCE_DOC_TYPE    =   strtoupper($hRowData["header"]["reference_type"]);
            $SOURCE_DOC_NO      =   strtoupper($hRowData["header"]["reference_doc_no"]);
            $get_docid          =   $this->get_souce_docid($hRowData["header"]["reference_type"],$hRowData["header"]["reference_doc_no"]);
            $SOURCE_DOC_ID      =   $get_docid["DOCID"] ? $get_docid["DOCID"]:"";
            $DOCDT              =   date('Y-m-d');
            $REMARKS            =   NULL;
            $XMLMAT             =   NULL;

            $req_data           =   array();
            $req_data1          =   array();
			$req_data2          =   array();

            foreach($hRowData["material"] as $pindex=>$prow){
            $MAT_DATA               =   $this->getCodeId($prow["item_code"]);
            $ITEMID_REF             =   $MAT_DATA['ITEMID'];
            $UOMID_REF              =   $MAT_DATA['MAIN_UOMID_REF'];
            $ALT_UOMID_REF          =   $MAT_DATA['ALT_UOMID_REF'];
            $RECEIVED_QTY           =   count($hRowData["material"]);
            $SERIAL_NO_APPLICABLE   =   $MAT_DATA['SERIAL_NO_APPLICABLE'];
            $BARCODE_APPLICABLE     =   $MAT_DATA['BARCODE_APPLICABLE'];

         
						$req_data[]= [
						'ITEMID_REF'        =>  $ITEMID_REF,
						'UOMID_REF'         =>  $UOMID_REF,
						'RECEIVED_QTY'      =>  $RECEIVED_QTY,
						'SERIALNOAPL'       =>  $SERIAL_NO_APPLICABLE,
						'BARCODEAPL'        =>  $BARCODE_APPLICABLE,
					];
				
                $req_data1[]= [
                    'ITEMID_REF'        =>  $ITEMID_REF,
                    'UOMID_REF'         =>  $UOMID_REF,
                    'UNIT_REF'          =>  $RECEIVED_QTY,
                    'RECEIVED_QTY'      =>  $SERIAL_NO_APPLICABLE,
                    'SERIALNUMBER'      =>  $prow["serial_number"],
                ];
				//$req_data2[] = array_values(array_unique($req_data));
            }
			//dd($req_data);
			if(!empty($req_data)){
				
				
				
                $wrapped_links["MAT"] = $req_data; 
                $XMLMAT = ArrayToXml::convert($wrapped_links);
				}
				else{
					$XMLMAT=NULL;
				}

				if(!empty($req_data1)){
				//	$req_data1 = array_unique($req_data1);
					$wrapped_links1["BRC"] = $req_data1; 
					$XMLBARC = ArrayToXml::convert($wrapped_links1);
				}
				else{
					$XMLBARC=NULL;
				}
			  
		   
				$log_data = [ 
					$DOCNO,$DOCDT,$SOURCE_DOC_TYPE,$SOURCE_DOC_NO,$SOURCE_DOC_ID,$REMARKS,$CYID_REF,$BRID_REF,$FYID_REF,            
					$XMLMAT,$XMLBARC,$VTID_REF,$USERID,Date('Y-m-d'),
					Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS
				];
			
				//dd($log_data);
				
				try{
					$sp_result = DB::select('EXEC SP_BARCODE_IMPORT ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?  ,?,?', $log_data);    
				} 
				catch (\Throwable $th) {
					$this->appendLogData($logfile," bom ".$hIndex.": There is some error. Please try after sometime. " );
					fclose($logfile);
					return redirect()->route("transaction",[418,"importdata"])->with("logerror",$logfile_path); 
				}
		
				if(Str::contains(strtoupper($sp_result[0]->RESULT), 'SUCCESS')){
					$this->appendLogData($logfile,"bom ".$hIndex.": Record successfully inserted.","",1 );
				}
				else{
					$this->appendLogData($logfile," bom ".$hIndex.": Record not inserted. ".$sp_result[0]->RESULT );
					fclose($logfile);
					return redirect()->route("transaction",[418,"importdata"])->with("logerror",$logfile_path);                     
				}
            
            

        }
   
        fclose($logfile);
        return redirect()->route("transaction",[418,"importdata"])->with("logsuccess",$logfile_path);      
         
    }



    
    public function getCodeId($ICODE){

        $dataArr    =   array();    
        $data       =   DB::table('TBL_MST_ITEM')
                        ->where('TBL_MST_ITEM.ICODE','=',$ICODE)
                        ->where('TBL_MST_ITEM.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_ITEM.BRID_REF','=',Session::get('BRID_REF'))
                        //->where('TBL_MST_ITEM.FYID_REF','=',Session::get('FYID_REF'))
                        ->leftJoin('TBL_MST_ITEMCHECKFLAG', 'TBL_MST_ITEM.ITEMID','=','TBL_MST_ITEMCHECKFLAG.ITEMID_REF')
                        ->select('TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.MAIN_UOMID_REF','TBL_MST_ITEM.ALT_UOMID_REF','TBL_MST_ITEMCHECKFLAG.SRNOA AS SERIAL_NO_APPLICABLE','TBL_MST_ITEMCHECKFLAG.BARCODE_APPLICABLE')
                        ->first();

        if(isset($data) && !empty($data)){
            $dataArr['ITEMID']              =   $data->ITEMID;
            $dataArr['MAIN_UOMID_REF']      =   $data->MAIN_UOMID_REF;
            $dataArr['ALT_UOMID_REF']       =   $data->ALT_UOMID_REF;
            $dataArr['SERIAL_NO_APPLICABLE']=   $data->SERIAL_NO_APPLICABLE;
            $dataArr['BARCODE_APPLICABLE']  =   $data->BARCODE_APPLICABLE;
        }

        return $dataArr;

    }

    public function get_souce_docid($SOURCE_DOC_TYPE,$SOURCE_DOC_NO){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $Status     =   'N';
        $dataArr    =   array();
        if($SOURCE_DOC_TYPE=='GRN'){
            $data = DB::table('TBL_TRN_IGRN02_HDR')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('BRID_REF','=',Session::get('BRID_REF'))
                    ->where('STATUS','=',$Status)
                    ->select('GRNID AS DOCID') 
                    ->first();
     }else if($SOURCE_DOC_TYPE=='RGP'){
            $data = DB::table('TBL_TRN_IGRN01_HDR')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('BRID_REF','=',Session::get('BRID_REF'))
                    ->where('STATUS','=',$Status)   
                    ->select('GRNID AS DOCID') 
                    ->first();

     }else if($SOURCE_DOC_TYPE=='STADJUSTMENT'){
                    $data = DB::select("SELECT ST_ADJUSTID AS DOCID FROM TBL_TRN_STOCK_ADJUST_HDR
                    WHERE STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF'");   
     }else if($SOURCE_DOC_TYPE=='STTOSTTRANSFER'){
                    $data = DB::select("SELECT ST_STID AS DOCID FROM TBL_TRN_STOCK_STOCK_HDR
                    WHERE STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF' ");
     }else if($SOURCE_DOC_TYPE=='PMOVEMENT'){
                    $data = DB::select("SELECT PNMID AS DOCID FROM TBL_TRN_PDPNM_HDR
                    WHERE STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF' ");  
     }else if($SOURCE_DOC_TYPE=='JOBWORKGRN'){
            $data = DB::table('TBL_TRN_GRJ_HDR')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('BRID_REF','=',Session::get('BRID_REF'))
                    ->where('STATUS','=',$Status)     
                    ->select('GRJID AS DOCID') 
                    ->first();
     }
     else if($SOURCE_DOC_TYPE=='SALESRETURN'){
            $data = DB::table('TBL_TRN_SLSR01_HDR')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('BRID_REF','=',Session::get('BRID_REF'))
                    ->where('STATUS','=',$Status)
                    ->select('SRID AS DOCID') 
                    ->first();
     }
     else if($SOURCE_DOC_TYPE=='CSV'){
        $data = DB::table('TBL_TRN_CRSV01_HDR')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('BRID_REF','=',Session::get('BRID_REF'))
                    ->where('STATUS','=',$Status)  
                    ->select('CSVID AS DOCID') 
                    ->first();
     }
     else if($SOURCE_DOC_TYPE=='DSV'){
                    $data = DB::table('TBL_TRN_DRSV01_HDR')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('BRID_REF','=',Session::get('BRID_REF'))
                    ->where('STATUS','=',$Status) 
                    ->select('DSVID AS DOCID') 
                    ->first();
     }
     else if($SOURCE_DOC_TYPE=='PR'){
                    $data = DB::select("SELECT PRRID AS DOCID FROM TBL_TRN_PDPRR_HDR
                    WHERE STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF' ");
     }
     else if($SOURCE_DOC_TYPE=='IO'){
                    $data = DB::select("SELECT IOBID AS DOCID FROM TBL_MST_ITEM_OB_HDR
                    WHERE STATUS='$Status' AND BRID_REF='$BRID_REF' AND CYID_REF='$CYID_REF' ");
     }

        if(isset($data) && !empty($data)){
            $dataArr['DOCID']              =   $data->DOCID;

        }

        return $dataArr;

    }

    public function exist_doc_no($DOCNO){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
         
         $data      =   DB::table('TBL_TRN_BARCODE_HDR')
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('BRID_REF','=',Session::get('BRID_REF'))
                        ->where('BRC_NO','=',$DOCNO)
                        ->select('BRCID')
                        ->first();

         return $data;         
    }

    public function appendLogData($logfile, $label, $cellval="",$removeError=0){
        if($removeError==0){
            $txtstring = "Error:".$label." ".$cellval."\n"; 
        }else{
            $txtstring = $label." ".$cellval."\n"; 
        }
            
        echo "<br>".$txtstring;
        fwrite($logfile, $txtstring);
    }



    function showDups($array)
    {
      $array_temp = array();
    
       foreach($array as $val)
       {
         if (!in_array($val, $array_temp))
         {
           $array_temp[] = $val;
         }
         else
         {
           return  $val ;
         }
       }
    }


    public function getaltuomqty(Request $request){
        $id = $request['id'];
        $itemid = $request['itemid'];
        $mqty = $request['mqty'];

    
        $ObjData =  DB::select('SELECT top 1 TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV  
                    WHERE  ITEMID_REF = ? AND TO_UOMID_REF =?', [$itemid,$id]);
    
         
                if(!empty($ObjData)){ 
                $auomqty = ($mqty/$ObjData[0]->FROM_QTY)/($ObjData[0]->TO_QTY);
                echo($auomqty);
    
                }else{
                    echo '0';
                }
                exit();
    
        }





        public function GetWeight($ITEMID,$ALT_UOMID_REF,$QTY){
            $ObjData =  DB::select('SELECT top 1 TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV  
            WHERE  ITEMID_REF = ? AND TO_UOMID_REF =?', [$ITEMID,$ALT_UOMID_REF]);

            if(!empty($ObjData)){ 
            $auomqty = ($QTY/$ObjData[0]->FROM_QTY)/($ObjData[0]->TO_QTY);
           // dd($auomqty);
            return $WEIGHT=number_format(($auomqty),0); 
            }else{
            return $WEIGHT ='0';
            }
        }


        public function GetReceivedQty($SOURCE_TYPE,$DOCID,$ITEMID){
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');

            $ObjData =  DB::select("SELECT SUM(M.RECEIVED_QTY) AS RECEIVED_QTY FROM TBL_TRN_BARCODE_MAT M 
            LEFT JOIN TBL_TRN_BARCODE_HDR H ON H.BRCID=M.BRCID_REF
            WHERE H.SOURCE_TYPE='$SOURCE_TYPE' AND H.DOCID_REF=$DOCID AND H.CYID_REF=$CYID_REF AND H.BRID_REF=$BRID_REF AND M.ITEMID_REF=$ITEMID AND H.STATUS <> 'C'");

            if($ObjData[0]->RECEIVED_QTY != ''){ 
             $WEIGHT    =   $ObjData[0]->RECEIVED_QTY;            
            }else{
             $WEIGHT    =  0;
            }

            return $WEIGHT; 
        }


        public function GetPendingQty(Request $request){
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');
            $itemid     = $request['itemid'];
            $doctype    = $request['doctype'];
            $docid      = $request['docid'];
            $action     = $request['action'];
                
            
            if($doctype=='GRN'){
                $ObjItem =  DB::select("SELECT 
                T1.RECEIVED_QTY_MU
                FROM TBL_TRN_IGRN02_MAT T1 
                LEFT JOIN TBL_TRN_IGRN02_HDR H ON T1.GRNID_REF=H.GRNID
                WHERE H.GRNID='$docid' AND   T1.ITEMID_REF=$itemid AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'                
                 ");
             }else if($doctype=='RGP'){
                 $ObjItem =  DB::select("SELECT 
                 T1.RECEIVED_QTY_MU
                 FROM TBL_TRN_IGRN01_MAT T1 
                 LEFT JOIN TBL_TRN_IGRN01_HDR H ON T1.GRNID_REF=H.GRNID      
                 WHERE H.GRNID='$docid' AND T1.ITEMID_REF=$itemid AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF' 
                  ");
 
                 
 
             }else if($doctype=='STADJUSTMENT'){
                 $ObjItem =  DB::select("SELECT 
                 T1.QTY AS RECEIVED_QTY_MU
                 FROM TBL_TRN_STOCK_ADJUST_MAT T1 
                 LEFT JOIN TBL_TRN_STOCK_ADJUST_HDR H ON T1.ST_ADJUSTID_REF=H.ST_ADJUSTID    
                 WHERE H.ST_ADJUSTID='$docid' AND T1.ITEMID_REF=$itemid AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                  ");
 
             }else if($doctype=='STTOSTTRANSFER'){
                 $ObjItem =  DB::select("SELECT 
                 T1.QTY AS RECEIVED_QTY_MU
                 FROM TBL_TRN_STOCK_STOCK_MAT T1 
                 LEFT JOIN TBL_TRN_STOCK_STOCK_HDR H ON T1.ST_STID_REF=H.ST_STID                            
                 WHERE H.ST_STID='$docid' AND T1.ITEMID_REF=$itemid AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                  ");
 
             }else if($doctype=='PMOVEMENT'){
                 $ObjItem =  DB::select("SELECT DISTINCT H.ACTUAL_QTY AS RECEIVED_QTY_MU
                 FROM TBL_TRN_PDPNM_MACHINE T1 
                 LEFT JOIN TBL_TRN_PDPNM_HDR H ON T1.PNMID_REF=H.PNMID  
                 WHERE H.PNMID='$docid' AND T1.ITEMID_REF=$itemid AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'   
                  ");
 
             }else if($doctype=='JOBWORKGRN'){
                 $ObjItem =  DB::select("SELECT 
                 T1.RECEIVED_QTY AS RECEIVED_QTY_MU
                 FROM TBL_TRN_GRJ_MAT T1 
                 LEFT JOIN TBL_TRN_GRJ_HDR H ON T1.GRJID_REF=H.GRJID   
                 WHERE H.GRJID='$docid' AND T1.ITEMID_REF=$itemid AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'  
                  ");
 
             }else if($doctype=='SALESRETURN'){
                 $ObjItem =  DB::select("SELECT 
                 T1.SRQTY AS RECEIVED_QTY_MU
                 FROM TBL_TRN_SLSR01_MAT T1 
                 LEFT JOIN TBL_TRN_SLSR01_HDR H ON T1.SRID_REF=H.SRID               
                 WHERE H.SRID='$docid' AND T1.ITEMID_REF=$itemid AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF' 
                  ");
 
             }else if($doctype=='CSV'){
                 $ObjItem =  DB::select("SELECT 
                 T1.CR_NOTE_QTY AS RECEIVED_QTY_MU
                 FROM TBL_TRN_CRSV01_MAT T1 
                 LEFT JOIN TBL_TRN_CRSV01_HDR H ON T1.CSVID_REF=H.CSVID              
                 WHERE H.CSVID='$docid' AND T1.ITEMID_REF=$itemid AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF' 
                  ");
 
             }else if($doctype=='DSV'){
                 $ObjItem =  DB::select("SELECT 
                 T1.DR_NOTE_QTY AS RECEIVED_QTY_MU
                 FROM TBL_TRN_DRSV01_MAT T1 
                 LEFT JOIN TBL_TRN_DRSV01_HDR H ON T1.DSVID_REF=H.DSVID               
                 WHERE H.DSVID='$docid' AND T1.ITEMID_REF=$itemid AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF' 
                  ");
 
             
             }else if($doctype=='PR'){
                 $ObjItem =  DB::select("SELECT 
                 T1.RETURNQTY AS RECEIVED_QTY_MU
                 FROM TBL_TRN_PDPRR_MAT T1 
                 LEFT JOIN TBL_TRN_PDPRR_HDR H ON T1.PRRID_REF=H.PRRID               
                 WHERE H.PRRID='$docid' AND T1.ITEMID_REF=$itemid AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF' 
                  ");
 
             }else if($doctype=='IO'){
 
                
                 $ObjItem =  DB::select("SELECT 
                 SUM(T1.OPENING_BL) AS RECEIVED_QTY_MU
                 FROM TBL_MST_ITEM_OB_MAT T1 
                 LEFT JOIN TBL_MST_ITEM_OB_HDR H ON T1.IOBID_REF=H.IOBID   
                 WHERE H.IOBID='$docid' AND T1.ITEMID_REF=$itemid AND H.CYID_REF='$CYID_REF' AND H.BRID_REF='$BRID_REF'
                  ");
 
             }
 
             else if($doctype=='ASSEMBLING'){
                 $ObjItem =  DB::select("SELECT 
                 T1.QTY AS RECEIVED_QTY_MU
                 FROM TBL_TRN_ASMDSM_HDR T1
                 WHERE T1.ADSMID='$docid' AND T1.ITEMID_REF=$itemid AND T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF'  
                  ");
                
 
             }else if($doctype=='DISSEMBLING'){
                 $ObjItem =  DB::select("SELECT 
                 T1.ITEM_QTY AS RECEIVED_QTY_MU
                 FROM TBL_TRN_ASMDSM_MAT T1 
                 LEFT JOIN TBL_TRN_ASMDSM_HDR H ON T1.ADSMID_REF=H.ADSMID
                 WHERE H.ADSMID='$docid' AND T1.ITEMID_REF=$itemid AND T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' 
                  ");
 
             }

         

             if(isset($ObjItem[0]->RECEIVED_QTY_MU) && $ObjItem[0]->RECEIVED_QTY_MU != ''){ 
                $RECEIVED_QTY    =   $ObjItem[0]->RECEIVED_QTY_MU;            
               }else{
                $RECEIVED_QTY    =  0;
               }

              
        
            $ObjData =  DB::select("SELECT SUM(M.RECEIVED_QTY) AS RECEIVED_QTY FROM TBL_TRN_BARCODE_MAT M 
            LEFT JOIN TBL_TRN_BARCODE_HDR H ON H.BRCID=M.BRCID_REF
            WHERE H.SOURCE_TYPE='$doctype' AND H.DOCID_REF=$docid AND  M.ITEMID_REF='$itemid' AND  H.CYID_REF=$CYID_REF AND H.BRID_REF=$BRID_REF  AND H.STATUS <> 'C'");

           
            if($ObjData[0]->RECEIVED_QTY != ''){ 
            
             $PENDING_QTY    =   $RECEIVED_QTY-$ObjData[0]->RECEIVED_QTY;
             
             if($action=='edit'){
                $PENDING_QTY=$PENDING_QTY+ $ObjData[0]->RECEIVED_QTY; 
             }
 

            }else{
             $PENDING_QTY    =  0;
            }


   
            echo  $PENDING_QTY; 

                    exit();
        
            }
        





    
}
