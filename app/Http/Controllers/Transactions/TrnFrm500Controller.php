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

class TrnFrm500Controller extends Controller{

    protected $form_id  = 500;
    protected $vtid_ref = 570;
    protected $view     = "transactions.sales.DealerCommission.trnfrm";
   
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

        $objDataList	=	DB::select("select hdr.DCID,hdr.DC_NO,hdr.DC_DATE,hdr.DC_TYPE,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.DCID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_DEALER_COMMISSION_HDR hdr
                            on a.VID = hdr.DCID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.DCID DESC ");

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

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_DEALER_COMMISSION_HDR',
            'HDR_ID'=>'DCID',
            'HDR_DOC_NO'=>'DC_NO',
            'HDR_DOC_DT'=>'DC_DATE'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
        
        $FormId     =   $this->form_id;
        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
        return view($this->view.$FormId.'add',compact(['AlpsStatus','FormId','objlastdt','TabSetting','doc_req','docarray']));       
    }

    public function save(Request $request){

        $materialArray  = array();
        if(isset($_REQUEST['DOC_ID']) && !empty($_REQUEST['DOC_ID'])){
            foreach($_REQUEST['DOC_ID'] as $key=>$val){

                $materialArray[] = array(
                    'SOSI_REF'          =>  trim($_REQUEST['DOC_ID'][$key]) !=''?trim($_REQUEST['DOC_ID'][$key]):NULL,
                    'DC_NO'             =>  trim($_REQUEST['DOC_NO'][$key]) !=''?trim($_REQUEST['DOC_NO'][$key]):NULL,
                    'DC_DATE'           =>  trim($_REQUEST['DOC_DATE'][$key]) !=''?date('Y-m-d',strtotime(trim($_REQUEST['DOC_DATE'][$key]))):NULL,
                    'DC_TYPE'           =>  trim($request['DC_TYPE']) !=''?trim($request['DC_TYPE']):NULL,
                    'DEALER_REF'        =>  trim($request['DEALER_REF']) !=''?trim($request['DEALER_REF']):NULL,
                    'SLID_REF'          =>  trim($_REQUEST['SLID_REF'][$key]) !=''?trim($_REQUEST['SLID_REF'][$key]):NULL,
                    'SOSI_AMOUNT'       =>  trim($_REQUEST['AMOUNT'][$key]) !=''?trim($_REQUEST['AMOUNT'][$key]):0,
                    'COMMISSION_AMOUNT' =>  trim($_REQUEST['COMMISSION_AMOUNT'][$key]) !=''?trim($_REQUEST['COMMISSION_AMOUNT'][$key]):0,
                );
            }
        }


        if(!empty($materialArray)){
            $XML_MAT_DATA["MAT"] = $materialArray; 
            $XMLMAT = ArrayToXml::convert($XML_MAT_DATA);
        }
        else{
            $XMLMAT = NULL; 
        }

        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'ADD';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $DC_NO      =   $request['DC_NO'];
        $DC_DATE    =   $request['DC_DATE'];
        $DC_TYPE    =   $request['DC_TYPE'];
        $DEALER_REF =   $request['DEALER_REF'];
        $FROM_DATE  =   $request['FROM_DATE'];
        $TO_DATE    =   $request['TO_DATE'];
        $REMARKS    =   $request['REMARKS'];
        
        $log_data = [ 
            $DC_NO,$DC_DATE,$DC_TYPE,$DEALER_REF,$FROM_DATE,
            $TO_DATE,$REMARKS,$CYID_REF,$BRID_REF,$FYID_REF,
            $VTID_REF,$XMLMAT,$USERID,Date('Y-m-d'),Date('h:i:s.u'),
            $ACTIONNAME,$IPADDRESS,
        ];  

        $sp_result = DB::select('EXEC SP_DEALER_COMMISSION_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?', $log_data);  

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

            $objResponse    =   DB::table('TBL_TRN_DEALER_COMMISSION_HDR AS HDR')
            ->leftJoin('TBL_MST_CUSTOMER AS CMR', 'HDR.DEALER_REF','=','CMR.CID')
            ->where('HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('HDR.DCID','=',$id)
            ->select('HDR.*','CMR.CCODE','CMR.NAME')
            ->first();

            $objlastdt      =   $this->getLastdt();
            
            if(isset($objResponse->DC_TYPE) && $objResponse->DC_TYPE =='Sales Order'){
                $objMAT     =   DB::select("SELECT 
                T1.*,
                T2.SLNAME AS CUSTOMER_NAME,
                (SELECT SUM(SO_QTY) FROM TBL_TRN_SLSO01_MAT WHERE SOID_REF=T1.SOSI_REF) AS SOQTY,
                (SELECT SUM(SIMAIN_QTY) FROM TBL_TRN_SLSI01_MAT WHERE SOID=T1.SOSI_REF) AS SO_INVOICE_QTY
                FROM TBL_TRN_DEALER_COMMISSION_DET T1
                LEFT JOIN  TBL_MST_SUBLEDGER T2 ON T1.SLID_REF=T2.SGLID
                WHERE T1.DCID_REF='$id' ORDER BY DCMATID ASC
                "); 
            }
            else{
                $objMAT     =   DB::select("SELECT 
                T1.*,
                T2.SLNAME AS CUSTOMER_NAME,
                '' AS SOQTY,
                '' AS SO_INVOICE_QTY
                FROM TBL_TRN_DEALER_COMMISSION_DET T1
                LEFT JOIN  TBL_MST_SUBLEDGER T2 ON T1.SLID_REF=T2.SGLID
                WHERE T1.DCID_REF='$id' ORDER BY DCMATID ASC
                "); 
            }
            
            $FormId         =   $this->form_id;
            $AlpsStatus     =   $this->AlpsStatus();
            $ActionStatus   =   "";
            $TabSetting	    =   Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
            return view($this->view.$FormId.'edit',compact(['AlpsStatus','FormId','objRights','objlastdt','objResponse','objMAT','ActionStatus','TabSetting']));      

        }
     
    }

    public function update(Request $request){
        
        $materialArray  = array();
        if(isset($_REQUEST['DOC_ID']) && !empty($_REQUEST['DOC_ID'])){
            foreach($_REQUEST['DOC_ID'] as $key=>$val){

                $materialArray[] = array(
                    'SOSI_REF'          =>  trim($_REQUEST['DOC_ID'][$key]) !=''?trim($_REQUEST['DOC_ID'][$key]):NULL,
                    'DC_NO'             =>  trim($_REQUEST['DOC_NO'][$key]) !=''?trim($_REQUEST['DOC_NO'][$key]):NULL,
                    'DC_DATE'           =>  trim($_REQUEST['DOC_DATE'][$key]) !=''?date('Y-m-d',strtotime(trim($_REQUEST['DOC_DATE'][$key]))):NULL,
                    'DC_TYPE'           =>  trim($request['DC_TYPE']) !=''?trim($request['DC_TYPE']):NULL,
                    'DEALER_REF'        =>  trim($request['DEALER_REF']) !=''?trim($request['DEALER_REF']):NULL,
                    'SLID_REF'          =>  trim($_REQUEST['SLID_REF'][$key]) !=''?trim($_REQUEST['SLID_REF'][$key]):NULL,
                    'SOSI_AMOUNT'       =>  trim($_REQUEST['AMOUNT'][$key]) !=''?trim($_REQUEST['AMOUNT'][$key]):0,
                    'COMMISSION_AMOUNT' =>  trim($_REQUEST['COMMISSION_AMOUNT'][$key]) !=''?trim($_REQUEST['COMMISSION_AMOUNT'][$key]):0,
                );
            }
        }


        if(!empty($materialArray)){
            $XML_MAT_DATA["MAT"] = $materialArray; 
            $XMLMAT = ArrayToXml::convert($XML_MAT_DATA);
        }
        else{
            $XMLMAT = NULL; 
        }

        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $DCID       =   $request['DCID'];
        $DC_NO      =   $request['DC_NO'];
        $DC_DATE    =   $request['DC_DATE'];
        $DC_TYPE    =   $request['DC_TYPE'];
        $DEALER_REF =   $request['DEALER_REF'];
        $FROM_DATE  =   $request['FROM_DATE'];
        $TO_DATE    =   $request['TO_DATE'];
        $REMARKS    =   $request['REMARKS'];

        $log_data = [ 
            $DCID,$DC_NO,$DC_DATE,$DC_TYPE,$DEALER_REF,$FROM_DATE,
            $TO_DATE,$REMARKS,$CYID_REF,$BRID_REF,$FYID_REF,
            $VTID_REF,$XMLMAT,$USERID,Date('Y-m-d'),Date('h:i:s.u'),
            $ACTIONNAME,$IPADDRESS,
        ];  

        $sp_result = DB::select('EXEC SP_DEALER_COMMISSION_UP ?,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?', $log_data); 
       
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $DC_NO. ' Sucessfully Updated.']);
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

            $objResponse    =   DB::table('TBL_TRN_DEALER_COMMISSION_HDR AS HDR')
            ->leftJoin('TBL_MST_CUSTOMER AS CMR', 'HDR.DEALER_REF','=','CMR.CID')
            ->where('HDR.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('HDR.BRID_REF','=',Session::get('BRID_REF'))
            ->where('HDR.FYID_REF','=',Session::get('FYID_REF'))
            ->where('HDR.DCID','=',$id)
            ->select('HDR.*','CMR.CCODE','CMR.NAME')
            ->first();

            $objlastdt      =   $this->getLastdt();
            
            if(isset($objResponse->DC_TYPE) && $objResponse->DC_TYPE =='Sales Order'){
                $objMAT     =   DB::select("SELECT 
                T1.*,
                T2.SLNAME AS CUSTOMER_NAME,
                (SELECT SUM(SO_QTY) FROM TBL_TRN_SLSO01_MAT WHERE SOID_REF=T1.SOSI_REF) AS SOQTY,
                (SELECT SUM(SIMAIN_QTY) FROM TBL_TRN_SLSI01_MAT WHERE SOID=T1.SOSI_REF) AS SO_INVOICE_QTY
                FROM TBL_TRN_DEALER_COMMISSION_DET T1
                LEFT JOIN  TBL_MST_SUBLEDGER T2 ON T1.SLID_REF=T2.SGLID
                WHERE T1.DCID_REF='$id' ORDER BY DCMATID ASC
                "); 
            }
            else{
                $objMAT     =   DB::select("SELECT 
                T1.*,
                T2.SLNAME AS CUSTOMER_NAME,
                '' AS SOQTY,
                '' AS SO_INVOICE_QTY
                FROM TBL_TRN_DEALER_COMMISSION_DET T1
                LEFT JOIN  TBL_MST_SUBLEDGER T2 ON T1.SLID_REF=T2.SGLID
                WHERE T1.DCID_REF='$id' ORDER BY DCMATID ASC
                "); 
            }
            
            $FormId         =   $this->form_id;
            $AlpsStatus     =   $this->AlpsStatus();
            $ActionStatus   =   "disabled";
            $TabSetting	    =   Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        
            return view($this->view.$FormId.'view',compact(['AlpsStatus','FormId','objRights','objlastdt','objResponse','objMAT','ActionStatus','TabSetting']));      

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
   
        $materialArray  = array();
        if(isset($_REQUEST['DOC_ID']) && !empty($_REQUEST['DOC_ID'])){
            foreach($_REQUEST['DOC_ID'] as $key=>$val){

                $materialArray[] = array(
                    'SOSI_REF'          =>  trim($_REQUEST['DOC_ID'][$key]) !=''?trim($_REQUEST['DOC_ID'][$key]):NULL,
                    'DC_NO'             =>  trim($_REQUEST['DOC_NO'][$key]) !=''?trim($_REQUEST['DOC_NO'][$key]):NULL,
                    'DC_DATE'           =>  trim($_REQUEST['DOC_DATE'][$key]) !=''?date('Y-m-d',strtotime(trim($_REQUEST['DOC_DATE'][$key]))):NULL,
                    'DC_TYPE'           =>  trim($request['DC_TYPE']) !=''?trim($request['DC_TYPE']):NULL,
                    'DEALER_REF'        =>  trim($request['DEALER_REF']) !=''?trim($request['DEALER_REF']):NULL,
                    'SLID_REF'          =>  trim($_REQUEST['SLID_REF'][$key]) !=''?trim($_REQUEST['SLID_REF'][$key]):NULL,
                    'SOSI_AMOUNT'       =>  trim($_REQUEST['AMOUNT'][$key]) !=''?trim($_REQUEST['AMOUNT'][$key]):0,
                    'COMMISSION_AMOUNT' =>  trim($_REQUEST['COMMISSION_AMOUNT'][$key]) !=''?trim($_REQUEST['COMMISSION_AMOUNT'][$key]):0,
                );
            }
        }


        if(!empty($materialArray)){
            $XML_MAT_DATA["MAT"] = $materialArray; 
            $XMLMAT = ArrayToXml::convert($XML_MAT_DATA);
        }
        else{
            $XMLMAT = NULL; 
        }


        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = $Approvallevel;
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $DCID       =   $request['DCID'];
        $DC_NO      =   $request['DC_NO'];
        $DC_DATE    =   $request['DC_DATE'];
        $DC_TYPE    =   $request['DC_TYPE'];
        $DEALER_REF =   $request['DEALER_REF'];
        $FROM_DATE  =   $request['FROM_DATE'];
        $TO_DATE    =   $request['TO_DATE'];
        $REMARKS    =   $request['REMARKS'];
       
        $log_data = [ 
            $DCID,$DC_NO,$DC_DATE,$DC_TYPE,$DEALER_REF,$FROM_DATE,
            $TO_DATE,$REMARKS,$CYID_REF,$BRID_REF,$FYID_REF,
            $VTID_REF,$XMLMAT,$USERID,Date('Y-m-d'),Date('h:i:s.u'),
            $ACTIONNAME,$IPADDRESS,
        ];  

        $sp_result = DB::select('EXEC SP_DEALER_COMMISSION_UP ?,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?', $log_data);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $DC_NO. ' Sucessfully Approved.']);

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
        $TABLE      =   "TBL_TRN_DEALER_COMMISSION_HDR";
        $FIELD      =   "DCID";
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
        $TABLE      =   "TBL_TRN_DEALER_COMMISSION_HDR";
        $FIELD      =   "DCID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
         'NT'  => 'TBL_TRN_DEALER_COMMISSION_DET',
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

            $objResponse = DB::table('TBL_TRN_DEALER_COMMISSION_HDR')->where('DCID','=',$id)->first();

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

            $dirname =   'DealerCommission';

            return view($this->view.$FormId.'attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments','dirname']));
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
        
		$image_path         =   "docs/company".$CYID_REF."/DealerCommission";     
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
 
                    $filenametostore        =  $VTID.$ATTACH_DOCNO.date('YmdHis')."_".str_replace(' ', '', $filenamewithextension);  

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

        $DC_NO  =   trim($request['DC_NO']);
        $data    = DB::table('TBL_TRN_DEALER_COMMISSION_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('DC_NO','=',$DC_NO)
        ->count();

        if($data > 0){  
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

        return  DB::select('SELECT MAX(DC_DATE) DC_DATE FROM TBL_TRN_DEALER_COMMISSION_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
    }

    public function getsubledger(Request $request){
        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $CODE       =   trim($request['CODE']);
        $NAME       =   trim($request['NAME']);

        $WCODE      =   $CODE !=''?"AND CCODE LIKE '%$CODE%'":'';
        $WNAME      =   $NAME !=''?"AND NAME LIKE '%$NAME%'":'';
      
        $ObjData    =   DB::select("SELECT CID,CCODE,NAME FROM TBL_MST_CUSTOMER WHERE TYPE='DEALER' AND CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' $WCODE $WNAME");

        if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){
                $row = '';
                $row = $row.'<tr><td class="ROW1"> <input type="checkbox" name="SELECT_SLID_REF[]" id="subgl_'.$index.'" class="clssubgl" value="'.$dataRow-> CID.'" ></td>';
                $row = $row.'<td class="ROW2">'.$dataRow->CCODE;
                $row = $row.'<input type="hidden" id="txtsubgl_'.$index.'" data-desc="'.$dataRow->CCODE .' - ';
                $row = $row.$dataRow->NAME. '" data-desc2="'.$dataRow->CID. '"value="'.$dataRow->CID.'"/></td><td class="ROW3">'.$dataRow->NAME.'</td></tr>';

                echo $row;
            }
    
        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();
    
    }

    public function getCommission(Request $request){

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $DC_TYPE        =   $request['DC_TYPE'];
        $DEALER_REF     =   $request['DEALER_REF'];
        $FROM_DATE      =   $request['FROM_DATE'];
        $TO_DATE        =   $request['TO_DATE'];
       
        if($DC_TYPE =='Sales Order'){
            $query_data =   DB::SELECT("SELECT 
                            T1.SOID AS DOC_ID,T1.SONO AS DOC_NO,T1.SODT AS DOC_DATE,T1.SLID_REF,T2.SLNAME AS CUSTOMER_NAME,T1.DEALER_COMMISSION_AMT AS COMMISSION_AMOUNT,
                            (SELECT SUM(SO_QTY) FROM TBL_TRN_SLSO01_MAT WHERE SOID_REF=T1.SOID) AS SOQTY,
                            (SELECT SUM(SIMAIN_QTY) FROM TBL_TRN_SLSI01_MAT WHERE SOID=T1.SOID) AS SO_INVOICE_QTY
                            FROM TBL_TRN_SLSO01_HDR T1
                            LEFT JOIN TBL_MST_SUBLEDGER T2 ON T1.SLID_REF = T2.SGLID  
                            WHERE T1.SODT BETWEEN '$FROM_DATE' AND '$TO_DATE' AND T1.DEALERID_REF='$DEALER_REF' AND T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' AND T1.STATUS='A'
                            ");
        }
        else{
            $query_data =   DB::SELECT("SELECT T1.SIID AS DOC_ID,T1.SINO AS DOC_NO,T1.SIDT AS DOC_DATE,T1.SLID_REF,T2.SLNAME AS CUSTOMER_NAME,T1.DEALER_COMMISSION_AMT AS COMMISSION_AMOUNT
                            FROM TBL_TRN_SLSI01_HDR T1
                            LEFT JOIN TBL_MST_SUBLEDGER T2 ON T1.SLID_REF = T2.SGLID  
                            WHERE T1.SIDT BETWEEN '$FROM_DATE' AND '$TO_DATE' AND T1.DEALERID_REF='$DEALER_REF' AND T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' AND T1.STATUS='A'
                            ");  
        }

        $data_array=[];
        if(isset($query_data) && !empty($query_data)){
            foreach($query_data as $key=>$data){

                $count  =   DB::table('TBL_TRN_DEALER_COMMISSION_DET')->where('SOSI_REF','=',$data->DOC_ID)->where('DC_TYPE','=',$DC_TYPE)->count();

                if($count == 0){

                    $COMMISSION_STATUS  ='readonly';
                    if($DC_TYPE =='Sales Order'){
                        $COMMISSION_STATUS  = isset($data->SOQTY) && floatval($data->SOQTY) != floatval($data->SO_INVOICE_QTY)?'readonly':'';
                    }

                    $TOTAL_MAT_AMT      =   $this->getTotalMaterialAmount($data->DOC_ID,$DC_TYPE);
                    $TOTAL_CAL_AMT      =   $this->getTotalCalculationAmount($data->DOC_ID,$DC_TYPE);
                    $TOTAL_TDS_AMT      =   $this->getTotalTdsAmount($data->DOC_ID,$DC_TYPE);
                    $TOTAL_AMOUNT       =   ($TOTAL_MAT_AMT+$TOTAL_CAL_AMT)-$TOTAL_TDS_AMT;
                    
                    $data_array[]=array(
                        'DOC_ID'=>$data->DOC_ID,
                        'DOC_NO'=>$data->DOC_NO,
                        'DOC_DATE'=>date('d-m-Y',strtotime($data->DOC_DATE)),
                        'AMOUNT'=>$TOTAL_AMOUNT,
                        'CUSTOMER_NAME'=>$data->CUSTOMER_NAME,
                        'SLID_REF'=>$data->SLID_REF,
                        'COMMISSION_AMOUNT'=>$data->COMMISSION_AMOUNT,
                        'COMMISSION_STATUS'=>$COMMISSION_STATUS,
                    );
                }
            }
        }

        

        return Response::json($data_array);
    }


    public function getInvoiceDetails(Request $request){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $DC_TYPE    =   "Sales Invoice";
        $id         =   $request['id'];

        
        $query_data =   DB::SELECT("SELECT T1.SIID AS DOC_ID,T1.SINO AS DOC_NO,T1.SIDT AS DOC_DATE,T2.SLNAME AS CUSTOMER_NAME
                        FROM TBL_TRN_SLSI01_HDR T1
                        LEFT JOIN TBL_MST_SUBLEDGER T2 ON T1.SLID_REF = T2.SGLID  
                        INNER JOIN TBL_TRN_SLSI01_MAT T3 ON T1.SIID = T3.SIID_REF  
                        WHERE T3.SOID='$id' AND T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF' AND T1.STATUS='A'
                        ");

        $data_array=[];
        if(isset($query_data) && !empty($query_data)){
            foreach($query_data as $key=>$data){

                $TOTAL_MAT_AMT      =   $this->getTotalMaterialAmount($data->DOC_ID,$DC_TYPE);
                $TOTAL_CAL_AMT      =   $this->getTotalCalculationAmount($data->DOC_ID,$DC_TYPE);
                $TOTAL_TDS_AMT      =   $this->getTotalTdsAmount($data->DOC_ID,$DC_TYPE);
                $TOTAL_AMOUNT       =   ($TOTAL_MAT_AMT+$TOTAL_CAL_AMT)-$TOTAL_TDS_AMT;
                
                $data_array[]=array(
                    'DOC_ID'=>$data->DOC_ID,
                    'DOC_NO'=>$data->DOC_NO,
                    'DOC_DATE'=>date('d-m-Y',strtotime($data->DOC_DATE)),
                    'AMOUNT'=>$TOTAL_AMOUNT,
                    'CUSTOMER_NAME'=>$data->CUSTOMER_NAME,
                );
            }
        }

        return Response::json($data_array);
    }

    function getTotalMaterialAmount($id,$type){

        $TOTAL_MAT_AMOUNT   =   0;
       
        if($type =='Sales Order'){
            $data   =   DB::select("SELECT 
                        SO_QTY AS QTY,RATEPUOM AS RATE,DISCPER AS DIS_PER,DISCOUNT_AMT AS DIS_AMT,IGST,CGST,SGST
                        FROM TBL_TRN_SLSO01_MAT 
                        WHERE SOID_REF	='$id'
                        ");
        }
        else{
            $data   =   DB::select("SELECT 
                        SIMAIN_QTY AS QTY,RATEPUOM AS RATE,DISPER AS DIS_PER,DISCOUNT_AMT AS DIS_AMT,IGST,CGST,SGST
                        FROM TBL_TRN_SLSI01_MAT 
                        WHERE SIID_REF	='$id'
                        ");
        }

        foreach($data as $key=>$val){
            $QTY            =   $val->QTY !=""?floatval($val->QTY):0;
            $RATE           =   $val->RATE !=""?floatval($val->RATE):0;
            $DIS_PER        =   $val->DIS_PER !=""?floatval($val->DIS_PER):0;
            $DIS_AMT        =   $val->DIS_AMT !=""?floatval($val->DIS_AMT):0;
            $IGST           =   $val->IGST !=""?floatval($val->IGST):0;
            $CGST           =   $val->CGST !=""?floatval($val->CGST):0;
            $SGST           =   $val->SGST !=""?floatval($val->SGST):0;

            $TOTAL_AMOUNT   =   $QTY*$RATE;
            
            if($DIS_PER > 0){
                $TOTAL_DISCOUNT   =   ($TOTAL_AMOUNT*$DIS_PER)/100;
            }
            else if($DIS_AMT > 0){
                $TOTAL_DISCOUNT   =   $DIS_AMT;
            }
            else{
                $TOTAL_DISCOUNT   =   0;
            }

            $TOTAL_AMOUNT       =   $TOTAL_AMOUNT-$TOTAL_DISCOUNT;

            $IGST_AMOUNT        =   ($TOTAL_AMOUNT*$IGST)/100;
            $CGST_AMOUNT        =   ($TOTAL_AMOUNT*$CGST)/100;
            $SGST_AMOUNT        =   ($TOTAL_AMOUNT*$SGST)/100;
            $TOTAL_TAX_AMOUNT   =   ($IGST_AMOUNT+$CGST_AMOUNT+$SGST_AMOUNT);
            $TOTAL_AMOUNT       =   $TOTAL_AMOUNT+$TOTAL_TAX_AMOUNT;

            $TOTAL_MAT_AMOUNT   =   $TOTAL_MAT_AMOUNT+$TOTAL_AMOUNT;
        }

        return $TOTAL_MAT_AMOUNT;
    }

    function getTotalCalculationAmount($id,$type){

        $TOTAL_CAL_AMOUNT   =   0;

        if($type =='Sales Order'){
            $data               =   DB::select("SELECT * FROM TBL_TRN_SLSO01_CAL WHERE SOID_REF	='$id'");
        }
        else{
            $data   =   DB::select("SELECT * FROM TBL_TRN_SLSI01_CAL WHERE SIID_REF ='$id'");
        }

        foreach($data as $key=>$val){

            $VALUE              =   $val->VALUE !=""?floatval($val->VALUE):0;
            $IGST               =   $val->IGST !=""?floatval($val->IGST):0;
            $CGST               =   $val->CGST !=""?floatval($val->CGST):0;
            $SGST               =   $val->SGST !=""?floatval($val->SGST):0;

            $TOTAL_AMOUNT       =   $VALUE;

            $IGST_AMOUNT        =   ($TOTAL_AMOUNT*$IGST)/100;
            $CGST_AMOUNT        =   ($TOTAL_AMOUNT*$CGST)/100;
            $SGST_AMOUNT        =   ($TOTAL_AMOUNT*$SGST)/100;
            $TOTAL_TAX_AMOUNT   =   ($IGST_AMOUNT+$CGST_AMOUNT+$SGST_AMOUNT);

            $TOTAL_AMOUNT       =   $TOTAL_AMOUNT+$TOTAL_TAX_AMOUNT;
            $TOTAL_CAL_AMOUNT   =   $TOTAL_CAL_AMOUNT+$TOTAL_AMOUNT;
        }

        return $TOTAL_CAL_AMOUNT;
    }

    function getTotalTdsAmount($id,$type){

        $TOTAL_TDS_AMOUNT   =   0;

        if($type =='Sales Order'){
            $data   =   DB::select("SELECT * FROM TBL_TRN_SLSO01_TDS WHERE SOID_REF ='$id'");
        }
        else{
            $data   =   DB::select("SELECT * FROM TBL_TRN_SLSI01_TDS WHERE SIID_REF ='$id'");
        }

        foreach($data as $key=>$val){
            $ASSESSABLE_VL_TDS  =   $val->ASSESSABLE_VL_TDS !=""?floatval($val->ASSESSABLE_VL_TDS):0;
            $TDS_RATE           =   $val->TDS_RATE !=""?floatval($val->TDS_RATE):0;
            $TOTAL_AMOUNT       =   ($ASSESSABLE_VL_TDS*$TDS_RATE)/100;
            $TOTAL_TDS_AMOUNT   =   $TOTAL_TDS_AMOUNT+$TOTAL_AMOUNT;
        }

        return $TOTAL_TDS_AMOUNT;
    }
    
}
