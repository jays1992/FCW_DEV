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

class TrnFrm368Controller extends Controller{
    protected $form_id  =   368;
    protected $vtid_ref =   453;
    protected $view     =   "transactions.PlantMaintenance.EnergyMeterConsumption.trnfrm";
   
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

        // $objDataList	=	DB::select("SELECT * FROM TBL_TRN_JWC_HDR WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF'  ORDER BY JWCID DESC");

        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList	=	DB::select("select hdr.EMC_ID,hdr.EMC_NO,hdr.EMC_DATE,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.EMC_ID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                            ) AS CREATED_BY,
                            hdr.STATUS,sl.METER_CODE,sl.METER_DESC,                     
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
                            inner join TBL_TRN_EN_CONSUMPTION hdr
                            on a.VID = hdr.EMC_ID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            inner join TBL_MST_ENERGY sl ON hdr.ENERGYID_REF = sl.ENERGYID  
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.EMC_ID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );

                           // dd($objDataList); 

        return view($this->view.$FormId,compact(['REQUEST_DATA','objRights','objDataList','FormId']));
    }


    public function getComplaintNo(){

        return DB::table('TBL_TRN_BD_COMPLAINT_LOG')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('STATUS','=','A')
        ->select('BDCL_ID','BDCL_NO','BDCL_DATE','BDCL_TIME')
        ->get();
    }

    public function getDetailsforComplaintNo(Request $request){

        return DB::table('TBL_TRN_BD_COMPLAINT_LOG')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('STATUS','=','A')
        ->where('BDCL_ID','=',$request['BDCL_ID'])
        ->select('*')
        ->get();
    }


    public function add(){  

        $FormId     =   $this->form_id;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');    
        $FYID_REF   =   Session::get('FYID_REF');
        $Status = 'A';		


         $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_EN_CONSUMPTION',
            'HDR_ID'=>'EMC_ID',
            'HDR_DOC_NO'=>'EMC_NO',
            'HDR_DOC_DT'=>'EMC_DATE'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

       
        

       

        $objlastdt          =   $this->getLastdt();

        $objEnergy = DB::table('TBL_MST_ENERGY')
        ->where('STATUS','=','A')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Auth::user()->BRID_REF)
        ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
        ->select('TBL_MST_ENERGY.*')
        ->get()->toArray();

    //    / dd($objEnergy); 

  


      

        return view($this->view.$FormId.'add',compact(['FormId','objlastdt','objEnergy','doc_req','docarray']));

    }

   



    public function codeduplicate(Request $request){

        $ST_ADJUST_DOCNO  =   trim($request['BDSL_NO']);
        $objLabel = DB::table('TBL_TRN_BD_SOLUTION')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('BDSL_NO','=',$ST_ADJUST_DOCNO)
        ->select('BDSL_ID')->first();

        if($objLabel){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }


    public function get_meter_details(Request $request){
       // dd($request->all()); 

        $ENERGYID_REF  =   trim($request['ENERGYID_REF']);
        $FROMDATE  =   trim($request['FROMDATE']);
        $TODATE  =   trim($request['TODATE']);
        $objMeter = DB::table('TBL_TRN_EN_CONSUMPTION')
        ->whereBetween('EMC_DATE', [$FROMDATE, $TODATE])
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('ENERGYID_REF','=',$ENERGYID_REF)
        ->orderBy('EMC_DATE','DESC')
        ->limit(1)
        ->select('*')->first();
      //  dd($objMeter); 
        if($objMeter){

        $KWH= (!empty($objMeter->METER_READING_END_KWH) ? $objMeter->METER_READING_END_KWH : '0.00');
        $KVARH= (!empty($objMeter->METER_READING_END_KVARH) ? $objMeter->METER_READING_END_KVARH : '0.00');
        $KVAH= (!empty($objMeter->METER_READING_END_KVAH) ? $objMeter->METER_READING_END_KVAH : '0.00');
        $MD= (!empty($objMeter->METER_READING_END_MD) ? $objMeter->METER_READING_END_MD : '0.00');
        }else{
        $objEnergy = DB::table('TBL_MST_ENERGY')
        ->where('STATUS','=','A')
        ->where('ENERGYID','=',$ENERGYID_REF)
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Auth::user()->BRID_REF)       
        ->select('TBL_MST_ENERGY.*')
        ->first();

        $KWH= (!empty($objEnergy->KWH) ? $objEnergy->KWH : '0.00');
        $KVARH= (!empty($objEnergy->KVARH) ? $objEnergy->KVARH : '0.00');
        $KVAH= (!empty($objEnergy->KVAH) ? $objEnergy->KVAH : '0.00');
        $MD= (!empty($objEnergy->MD) ? $objEnergy->MD : '0.00');
  
       

        }  
    return Response::json(['KWH' =>$KWH,'KVARH' =>$KVARH,'KVAH' =>$KVAH,'MD' =>$MD]);     

        exit();


    }



    public function save(Request $request) {    
       
           $VTID_REF     =   $this->vtid_ref;
           $VID = 0;
           $USERID = Auth::user()->USERID;   
           $ACTIONNAME = 'ADD';
           $IPADDRESS = $request->getClientIp();
           $CYID_REF = Auth::user()->CYID_REF;
           $BRID_REF = Session::get('BRID_REF');
           $FYID_REF = Session::get('FYID_REF');
           $EMC_NO = strtoupper($request['EMC_NO']);
           $EMC_DATE = $request['EMC_DATE'];
           $ENERGYID_REF = $request['ENERGYID_REF'];
   
           $FROMDATE = $request['FROMDATE'];
           $TODATE = $request['TODATE'];
           $KWH_STARTED = $request['KWH_STARTED'];
           $KVARH_STARTED = $request['KVARH_STARTED'];
           $KVAH_STARTED = $request['KVAH_STARTED'];
           $MD_STARTED = $request['MD_STARTED'];
           $KWH_ENDED = $request['KWH_ENDED'];
           $KVARH_ENDED = $request['KVARH_ENDED'];
           $KVAH_ENDED = $request['KVAH_ENDED'];
           $drpstatus = $request['drpstatus'];
           $REMARKS = $request['REMARKS'];
           $MD_ENDED = $request['MD_ENDED'];


       
        $log_data = [ 
            $EMC_NO,                                       $EMC_DATE,                               $ENERGYID_REF,                   $FROMDATE,                     $TODATE,       
            $KWH_STARTED,                                  $KVARH_STARTED,                          $KVAH_STARTED,                   $MD_STARTED,                   $KWH_ENDED,
            $KVARH_ENDED,                                  $KVAH_ENDED,                             $MD_ENDED,                       $drpstatus,                    $REMARKS,
            $CYID_REF,                                     $BRID_REF,                               $FYID_REF,                       $VTID_REF,                     $USERID,
            Date('Y-m-d'),                                 Date('h:i:s.u'),                         $ACTIONNAME,                     $IPADDRESS
        ];


        // /dd($log_data); 

        
        $sp_result = DB::select('EXEC SP_TRN_EN_CONSUMPTION_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,? ', $log_data); 
        
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

            $FormId     =   $this->form_id;


            $objResponse = DB::table('TBL_TRN_EN_CONSUMPTION')            
            ->where('TBL_TRN_EN_CONSUMPTION.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_EN_CONSUMPTION.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_EN_CONSUMPTION.FYID_REF','=',Session::get('FYID_REF'))
			->where('TBL_TRN_EN_CONSUMPTION.EMC_ID','=',$id)            
            ->leftJoin('TBL_MST_ENERGY', 'TBL_TRN_EN_CONSUMPTION.ENERGYID_REF','=','TBL_MST_ENERGY.ENERGYID')           
             ->select('TBL_TRN_EN_CONSUMPTION.*','TBL_MST_ENERGY.*')
            ->first();
           // dd($objResponse); 

           $CONSUME1=(!empty($objResponse->METER_READING_END_KWH) ? $objResponse->METER_READING_END_KWH-$objResponse->METER_READING_START_KWH : '0.00');
           $CONSUME2= (!empty($objResponse->METER_READING_END_KVARH) ?$objResponse->METER_READING_END_KVARH-$objResponse->METER_READING_START_KVARH : '0.00');
           $CONSUME3= (!empty($objResponse->METER_READING_END_KVAH) ? $objResponse->METER_READING_END_KVAH-$objResponse->METER_READING_START_KVAH : '0.00');
           $CONSUME4= (!empty($objResponse->METER_READING_END_MD) ? $objResponse->METER_READING_END_MD-$objResponse->METER_READING_START_MD : '0.00');
           
    
            $objlastdt          =   $this->getLastdt();

            $objEnergy = DB::table('TBL_MST_ENERGY')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Auth::user()->BRID_REF)
            ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
            ->select('TBL_MST_ENERGY.*')
            ->get()->toArray();
    
          
    
            return view($this->view.$FormId.'edit',compact(['FormId','objlastdt','objRights','objResponse','CONSUME1','CONSUME2','CONSUME3','CONSUME4','objEnergy']));
        }
     
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

            $FormId     =   $this->form_id;


            $objResponse = DB::table('TBL_TRN_EN_CONSUMPTION')            
            ->where('TBL_TRN_EN_CONSUMPTION.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_EN_CONSUMPTION.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_EN_CONSUMPTION.FYID_REF','=',Session::get('FYID_REF'))
			->where('TBL_TRN_EN_CONSUMPTION.EMC_ID','=',$id)            
            ->leftJoin('TBL_MST_ENERGY', 'TBL_TRN_EN_CONSUMPTION.ENERGYID_REF','=','TBL_MST_ENERGY.ENERGYID')           
             ->select('TBL_TRN_EN_CONSUMPTION.*','TBL_MST_ENERGY.*')
            ->first();
           // dd($objResponse); 

           $CONSUME1=(!empty($objResponse->METER_READING_END_KWH) ? $objResponse->METER_READING_END_KWH-$objResponse->METER_READING_START_KWH : '0.00');
           $CONSUME2= (!empty($objResponse->METER_READING_END_KVARH) ?$objResponse->METER_READING_END_KVARH-$objResponse->METER_READING_START_KVARH : '0.00');
           $CONSUME3= (!empty($objResponse->METER_READING_END_KVAH) ? $objResponse->METER_READING_END_KVAH-$objResponse->METER_READING_START_KVAH : '0.00');
           $CONSUME4= (!empty($objResponse->METER_READING_END_MD) ? $objResponse->METER_READING_END_MD-$objResponse->METER_READING_START_MD : '0.00');
           
    
            $objlastdt          =   $this->getLastdt();

            $objEnergy = DB::table('TBL_MST_ENERGY')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Auth::user()->BRID_REF)
            ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
            ->select('TBL_MST_ENERGY.*')
            ->get()->toArray();
    
          
    
            return view($this->view.$FormId.'view',compact(['FormId','objlastdt','objRights','objResponse','CONSUME1','CONSUME2','CONSUME3','CONSUME4','objEnergy']));
        }
     
    }

   


    
    public function update(Request $request){

        $VTID_REF     =   $this->vtid_ref;
        $VID = 0;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $EMC_NO = strtoupper($request['EMC_NO']);
        $EMC_DATE = $request['EMC_DATE'];
        $ENERGYID_REF = $request['ENERGYID_REF'];

        $FROMDATE = $request['FROMDATE'];
        $TODATE = $request['TODATE'];
        $KWH_STARTED = $request['KWH_STARTED'];
        $KVARH_STARTED = $request['KVARH_STARTED'];
        $KVAH_STARTED = $request['KVAH_STARTED'];
        $MD_STARTED = $request['MD_STARTED'];
        $KWH_ENDED = $request['KWH_ENDED'];
        $KVARH_ENDED = $request['KVARH_ENDED'];
        $KVAH_ENDED = $request['KVAH_ENDED'];
        $drpstatus = $request['drpstatus'];
        $REMARKS = $request['REMARKS'];
        $MD_ENDED = $request['MD_ENDED'];


    
     $log_data = [ 
         $EMC_NO,                                       $EMC_DATE,                               $ENERGYID_REF,                   $FROMDATE,                     $TODATE,       
         $KWH_STARTED,                                  $KVARH_STARTED,                          $KVAH_STARTED,                   $MD_STARTED,                   $KWH_ENDED,
         $KVARH_ENDED,                                  $KVAH_ENDED,                             $MD_ENDED,                       $drpstatus,                    $REMARKS,
         $CYID_REF,                                     $BRID_REF,                               $FYID_REF,                       $VTID_REF,                     $USERID,
         Date('Y-m-d'),                                 Date('h:i:s.u'),                         $ACTIONNAME,                     $IPADDRESS
     ];


     // /dd($log_data); 

     
     $sp_result = DB::select('EXEC SP_TRN_EN_CONSUMPTION_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,? ', $log_data);  
    //   / dd($sp_result); 
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $EMC_NO. ' Sucessfully Updated.']);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();   
    }

    //update the data
   public function Approve(Request $request){

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
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


          
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = $Approvallevel;
            $IPADDRESS = $request->getClientIp();
    
            $EMC_NO = strtoupper($request['EMC_NO']);
            $EMC_DATE = $request['EMC_DATE'];
            $ENERGYID_REF = $request['ENERGYID_REF'];
    
            $FROMDATE = $request['FROMDATE'];
            $TODATE = $request['TODATE'];
            $KWH_STARTED = $request['KWH_STARTED'];
            $KVARH_STARTED = $request['KVARH_STARTED'];
            $KVAH_STARTED = $request['KVAH_STARTED'];
            $MD_STARTED = $request['MD_STARTED'];
            $KWH_ENDED = $request['KWH_ENDED'];
            $KVARH_ENDED = $request['KVARH_ENDED'];
            $KVAH_ENDED = $request['KVAH_ENDED'];
            $drpstatus = $request['drpstatus'];
            $REMARKS = $request['REMARKS'];
            $MD_ENDED = $request['MD_ENDED'];
    
    
        
         $log_data = [ 
             $EMC_NO,                                       $EMC_DATE,                               $ENERGYID_REF,                   $FROMDATE,                     $TODATE,       
             $KWH_STARTED,                                  $KVARH_STARTED,                          $KVAH_STARTED,                   $MD_STARTED,                   $KWH_ENDED,
             $KVARH_ENDED,                                  $KVAH_ENDED,                             $MD_ENDED,                       $drpstatus,                    $REMARKS,
             $CYID_REF,                                     $BRID_REF,                               $FYID_REF,                       $VTID_REF,                     $USERID,
             Date('Y-m-d'),                                 Date('h:i:s.u'),                         $ACTIONNAME,                     $IPADDRESS
         ];
    
    
         // /dd($log_data); 
    
         
         $sp_result = DB::select('EXEC SP_TRN_EN_CONSUMPTION_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,? ', $log_data);  


        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $EMC_NO. ' Sucessfully Approved.']);

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
                $TABLE      =   "TBL_TRN_EN_CONSUMPTION";
                $FIELD      =   "EMC_ID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
            
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_JWR ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
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
        $TABLE      =   "TBL_TRN_EN_CONSUMPTION";
        $FIELD      =   "EMC_ID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_EN_CONSUMPTION',
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

            $objResponse = DB::table('TBL_TRN_EN_CONSUMPTION')->where('EMC_ID','=',$id)->first();

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
        
		//$destinationPath = storage_path()."/docs/company".$CYID_REF."/JobWorkOrder";
        $image_path         =   "docs/company".$CYID_REF."/EnergyMeterComsumption";     
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






    public function getLastdt(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        return  DB::select('SELECT MAX(EMC_DATE) EMC_DATE FROM TBL_TRN_EN_CONSUMPTION  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
    }


    
}
