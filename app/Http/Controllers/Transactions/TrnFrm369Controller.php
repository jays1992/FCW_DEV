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

class TrnFrm369Controller extends Controller{
    protected $form_id  =   369;
    protected $vtid_ref =   454;
    protected $view     =   "transactions.PlantMaintenance.DGUsageFuelFill.trnfrm";
   
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

        $objDataList	=	DB::select("select hdr.DGUFF_ID,hdr.DGUFF_NO,hdr.DGUFF_DATE,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.DGUFF_ID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_DGUFF hdr
                            on a.VID = hdr.DGUFF_ID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF                   
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.DGUFF_ID DESC ");

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
            'HDR_TABLE'=>'TBL_TRN_DGUFF',
            'HDR_ID'=>'DGUFF_ID',
            'HDR_DOC_NO'=>'DGUFF_NO',
            'HDR_DOC_DT'=>'DGUFF_DATE'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

       

       

        $objlastdt          =   $this->getLastdt();

        $objFuelType = DB::table('TBL_MST_FUEL_TYPE')
        ->where('STATUS','=','A')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Auth::user()->BRID_REF)
        ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
        ->select('TBL_MST_FUEL_TYPE.*')
        ->get()->toArray();


        $objUom = DB::table('TBL_MST_UOM')
        ->where('STATUS','=','A')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Auth::user()->BRID_REF)
        ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
        ->select('TBL_MST_UOM.*')
        ->get()->toArray();



  


      

        return view($this->view.$FormId.'add',compact(['FormId','objlastdt','objFuelType','objUom','doc_req','docarray']));

    }

   




    public function get_genset_details(Request $request) {    

        //dd($request->all()); 
         
             $Status = "A";
             $CYID_REF = Auth::user()->CYID_REF;
             $BRID_REF = Session::get('BRID_REF');
             $FYID_REF = Session::get('FYID_REF');
 
         
             $objmachine = DB::table('TBL_MST_MACHINE')
             ->where('CYID_REF','=',Auth::user()->CYID_REF)
             ->where('BRID_REF','=',Session::get('BRID_REF'))
             ->where('MACHINE_TYPE','=','Genset')
             ->where('STATUS','=',$Status)
             ->select('MACHINEID','MACHINE_NO','MACHINE_DESC') 
             ->get()    
             ->toArray();
         
            // / dd($objmachine); 
              
         
             if(!empty($objmachine)){        
                 foreach ($objmachine as $index=>$dataRow){
         
         
                     $row = '';
                     $row = $row.'<tr ><td style="text-align:center; width:10%">';
                     $row = $row.'<input type="checkbox" name="machine[]"  id="machinecode_'.$dataRow->MACHINEID.'" class="clsspid_machine" 
                     value="'.$dataRow->MACHINEID.'"/>             
                     </td>           
                     <td style="width:30%;">'.$dataRow->MACHINE_NO;
                     $row = $row.'<input type="hidden" id="txtmachinecode_'.$dataRow->MACHINEID.'" data-code="'.$dataRow->MACHINE_NO.'"   data-desc="'.$dataRow->MACHINE_DESC.'" 
                     value="'.$dataRow->MACHINEID.'"/></td>
         
                     <td style="width:60%;">'.$dataRow->MACHINE_DESC.'</td>
           
         
                    </tr>';
                     echo $row;
                 }
         
                 }else{
                     echo '<tr><td colspan="2">Record not found.</td></tr>';
                 }
         
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

           $TYPE     = $request['TYPE'];
         // dd($TYPE); 
           if($TYPE=='usage'){
           $USAGE_TYPE=1;
           $CONSUMPATION_TYPE=0;
           $BOTH_TYPE=0;
           }else if($TYPE=='fuel_consumption'){
           $USAGE_TYPE=0;
           $CONSUMPATION_TYPE=1;
           $BOTH_TYPE=0;
           }else if($TYPE=='both'){
           $USAGE_TYPE=0;
           $CONSUMPATION_TYPE=0;
           $BOTH_TYPE=1;
           }


           $DGUFF_NO = strtoupper($request['DGUFF_NO']);
           $DGUFF_DATE = $request['DGUFF_DATE'];
           $MACHINEID_REF = $request['MACHINE_REF'];
   
           $FUELID_REF = $request['FUELTYPE_REF'];
           $STANDARD_CONSUMPTION_PH = $request['CONSUMPTION_PER_HOUR'];
           $UOMID_REF = $request['UOMID_REF'];

           $USAGE = $USAGE_TYPE;
           $FUEL_CONSUMPTION = $CONSUMPATION_TYPE;

           $USAGE_FROMDATE = $request['FROMDATE'];
           $USAGE_FROMTIME = $request['FROMTIME'];
           $USAGE_TODATE = $request['TODATE'];
           $KVAH_ENDED = $request['KVAH_ENDED'];
           $USAGE_TOTIME = $request['TOTIME'];
           $GENSET_STARTEDBY = $request['GENSET_STARTED_BY'];
           $GENSET_STOPPEDBY = $request['GENSET_STOPPED_BY'];
           $USAGE_READING_START_KWH = $request['KWH_START_USAGE'];
           $USAGE_READING_END_KWH = $request['KWH_END_USAGE'];
           $USAGE_REMARKS = $request['REMARKS'];
           $OPENING_FUEL = $request['OPENDING_FUEL'];
           $FILLED_FUEL = $request['FILLED_FUEL'];
           $CLOSING_FUEL = $request['CLOSING_FUEL'];
           $FUEL_FILLEDBY = $request['FUEL_FILLED_BY'];
           $FUEL_REMARKS = $request['CONSUMPTION_REMARKS'];
           $FUEL_READING_START_KWH = $request['KWH_START_CONSUMPTION'];
           $FUEL_READING_END_KWH = $request['KWH_END_CONSUMPTION'];
           $OBSERVATION = $request['OBSERVATION'];


     
        $log_data = [$DGUFF_NO,$DGUFF_DATE,$MACHINEID_REF,$FUELID_REF,$STANDARD_CONSUMPTION_PH,$UOMID_REF,$USAGE,
        $FUEL_CONSUMPTION,$USAGE_FROMDATE,$USAGE_FROMTIME,$USAGE_TODATE,$USAGE_TOTIME,
        $GENSET_STARTEDBY,$GENSET_STOPPEDBY,$USAGE_READING_START_KWH, $USAGE_READING_END_KWH,$USAGE_REMARKS,
        $OPENING_FUEL,$FILLED_FUEL,$CLOSING_FUEL,$FUEL_FILLEDBY,$FUEL_REMARKS,$FUEL_READING_START_KWH,$FUEL_READING_END_KWH,
        $OBSERVATION,$CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$USERID,Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$BOTH_TYPE];


        //dd($log_data); 

        
        $sp_result = DB::select('EXEC SP_TRN_DGUFF_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,? ,?,?,?,?,? ,?,?,?,?,?', $log_data); 
    
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


            $objResponse = DB::table('TBL_TRN_DGUFF')            
            ->where('TBL_TRN_DGUFF.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_DGUFF.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_DGUFF.FYID_REF','=',Session::get('FYID_REF'))
			->where('TBL_TRN_DGUFF.DGUFF_ID','=',$id)            
            ->leftJoin('TBL_MST_UOM', 'TBL_TRN_DGUFF.UOMID_REF','=','TBL_MST_UOM.UOMID')           
            ->leftJoin('TBL_MST_FUEL_TYPE', 'TBL_TRN_DGUFF.FUELID_REF','=','TBL_MST_FUEL_TYPE.FUELID')           
            ->leftJoin('TBL_MST_MACHINE', 'TBL_TRN_DGUFF.MACHINEID_REF','=','TBL_MST_MACHINE.MACHINEID')           
             ->select('TBL_TRN_DGUFF.*','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','TBL_MST_FUEL_TYPE.FUEL_CODE','TBL_MST_FUEL_TYPE.FUEL_DESC','TBL_MST_MACHINE.MACHINE_NO','TBL_MST_MACHINE.MACHINE_DESC')
            ->first();
            //dd($objResponse); 


            if(isset($objResponse->USAGE_FROMTIME)){
                $TIME_DATA=explode(':',$objResponse->USAGE_FROMTIME);
                $FROMTIME=$TIME_DATA[0].':'.$TIME_DATA[1];
                }else{
                $FROMTIME='';
                }
    
                if(isset($objResponse->USAGE_TOTIME)){
                $TIME_DATA=explode(':',$objResponse->USAGE_TOTIME);
                $TOTIME=$TIME_DATA[0].':'.$TIME_DATA[1];
                }else{
                $TOTIME='';
                }


           
    
            $objlastdt          =   $objResponse->DGUFF_DATE;

      
            $objFuelType = DB::table('TBL_MST_FUEL_TYPE')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Auth::user()->BRID_REF)
            ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
            ->select('TBL_MST_FUEL_TYPE.*')
            ->get()->toArray();
    
    
            $objUom = DB::table('TBL_MST_UOM')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Auth::user()->BRID_REF)
            ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
            ->select('TBL_MST_UOM.*')
            ->get()->toArray();
    
          
    
            return view($this->view.$FormId.'edit',compact(['FormId','objlastdt','objRights','objResponse','objFuelType','objUom','FROMTIME','TOTIME']));
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


            $objResponse = DB::table('TBL_TRN_DGUFF')            
            ->where('TBL_TRN_DGUFF.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_TRN_DGUFF.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_TRN_DGUFF.FYID_REF','=',Session::get('FYID_REF'))
			->where('TBL_TRN_DGUFF.DGUFF_ID','=',$id)            
            ->leftJoin('TBL_MST_UOM', 'TBL_TRN_DGUFF.UOMID_REF','=','TBL_MST_UOM.UOMID')           
            ->leftJoin('TBL_MST_FUEL_TYPE', 'TBL_TRN_DGUFF.FUELID_REF','=','TBL_MST_FUEL_TYPE.FUELID')           
            ->leftJoin('TBL_MST_MACHINE', 'TBL_TRN_DGUFF.MACHINEID_REF','=','TBL_MST_MACHINE.MACHINEID')           
             ->select('TBL_TRN_DGUFF.*','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','TBL_MST_FUEL_TYPE.FUEL_CODE','TBL_MST_FUEL_TYPE.FUEL_DESC','TBL_MST_MACHINE.MACHINE_NO','TBL_MST_MACHINE.MACHINE_DESC')
            ->first();
            //dd($objResponse); 


            if(isset($objResponse->USAGE_FROMTIME)){
                $TIME_DATA=explode(':',$objResponse->USAGE_FROMTIME);
                $FROMTIME=$TIME_DATA[0].':'.$TIME_DATA[1];
                }else{
                $FROMTIME='';
                }
    
                if(isset($objResponse->USAGE_TOTIME)){
                $TIME_DATA=explode(':',$objResponse->USAGE_TOTIME);
                $TOTIME=$TIME_DATA[0].':'.$TIME_DATA[1];
                }else{
                $TOTIME='';
                }


           
    
            $objlastdt          =   $objResponse->DGUFF_DATE;

      
            $objFuelType = DB::table('TBL_MST_FUEL_TYPE')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Auth::user()->BRID_REF)
            ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
            ->select('TBL_MST_FUEL_TYPE.*')
            ->get()->toArray();
    
    
            $objUom = DB::table('TBL_MST_UOM')
            ->where('STATUS','=','A')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Auth::user()->BRID_REF)
            ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
            ->select('TBL_MST_UOM.*')
            ->get()->toArray();
    
          
    
            return view($this->view.$FormId.'view',compact(['FormId','objlastdt','objRights','objResponse','objFuelType','objUom','FROMTIME','TOTIME']));
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
        $TYPE     = $request['TYPE'];
        // dd($TYPE); 
          if($TYPE=='usage'){
          $USAGE_TYPE=1;
          $CONSUMPATION_TYPE=0;
          $BOTH_TYPE=0;
          }else if($TYPE=='fuel_consumption'){
          $USAGE_TYPE=0;
          $CONSUMPATION_TYPE=1;
          $BOTH_TYPE=0;
          }else if($TYPE=='both'){
          $USAGE_TYPE=0;
          $CONSUMPATION_TYPE=0;
          $BOTH_TYPE=1;
          }


          $DGUFF_NO = strtoupper($request['DGUFF_NO']);
          $DGUFF_DATE = $request['DGUFF_DATE'];
          $MACHINEID_REF = $request['MACHINE_REF'];
  
          $FUELID_REF = $request['FUELTYPE_REF'];
          $STANDARD_CONSUMPTION_PH = $request['CONSUMPTION_PER_HOUR'];
          $UOMID_REF = $request['UOMID_REF'];

          $USAGE = $USAGE_TYPE;
          $FUEL_CONSUMPTION = $CONSUMPATION_TYPE;

          $USAGE_FROMDATE = $request['FROMDATE'];
          $USAGE_FROMTIME = $request['FROMTIME'];
          $USAGE_TODATE = $request['TODATE'];
          $KVAH_ENDED = $request['KVAH_ENDED'];
          $USAGE_TOTIME = $request['TOTIME'];
          $GENSET_STARTEDBY = $request['GENSET_STARTED_BY'];
          $GENSET_STOPPEDBY = $request['GENSET_STOPPED_BY'];
          $USAGE_READING_START_KWH = $request['KWH_START_USAGE'];
          $USAGE_READING_END_KWH = $request['KWH_END_USAGE'];
          $USAGE_REMARKS = $request['REMARKS'];
          $OPENING_FUEL = $request['OPENDING_FUEL'];
          $FILLED_FUEL = $request['FILLED_FUEL'];
          $CLOSING_FUEL = $request['CLOSING_FUEL'];
          $FUEL_FILLEDBY = $request['FUEL_FILLED_BY'];
          $FUEL_REMARKS = $request['CONSUMPTION_REMARKS'];
          $FUEL_READING_START_KWH = $request['KWH_START_CONSUMPTION'];
          $FUEL_READING_END_KWH = $request['KWH_END_CONSUMPTION'];
          $OBSERVATION = $request['OBSERVATION'];


    
       $log_data = [$DGUFF_NO,$DGUFF_DATE,$MACHINEID_REF,$FUELID_REF,$STANDARD_CONSUMPTION_PH,$UOMID_REF,$USAGE,
       $FUEL_CONSUMPTION,$USAGE_FROMDATE,$USAGE_FROMTIME,$USAGE_TODATE,$USAGE_TOTIME,
       $GENSET_STARTEDBY,$GENSET_STOPPEDBY,$USAGE_READING_START_KWH, $USAGE_READING_END_KWH,$USAGE_REMARKS,
       $OPENING_FUEL,$FILLED_FUEL,$CLOSING_FUEL,$FUEL_FILLEDBY,$FUEL_REMARKS,$FUEL_READING_START_KWH,$FUEL_READING_END_KWH,
       $OBSERVATION,$CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$USERID,Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$BOTH_TYPE];


       //dd($log_data); 

       
       $sp_result = DB::select('EXEC SP_TRN_DGUFF_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,? ,?,?,?,?,? ,?,?,?,?,?', $log_data); 
   
    //   / dd($sp_result); 
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $DGUFF_NO. ' Sucessfully Updated.']);

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
    
            $TYPE     = $request['TYPE'];
            // dd($TYPE); 
              if($TYPE=='usage'){
              $USAGE_TYPE=1;
              $CONSUMPATION_TYPE=0;
              $BOTH_TYPE=0;
              }else if($TYPE=='fuel_consumption'){
              $USAGE_TYPE=0;
              $CONSUMPATION_TYPE=1;
              $BOTH_TYPE=0;
              }else if($TYPE=='both'){
              $USAGE_TYPE=0;
              $CONSUMPATION_TYPE=0;
              $BOTH_TYPE=1;
              }
    
    
              $DGUFF_NO = strtoupper($request['DGUFF_NO']);
              $DGUFF_DATE = $request['DGUFF_DATE'];
              $MACHINEID_REF = $request['MACHINE_REF'];
      
              $FUELID_REF = $request['FUELTYPE_REF'];
              $STANDARD_CONSUMPTION_PH = $request['CONSUMPTION_PER_HOUR'];
              $UOMID_REF = $request['UOMID_REF'];
    
              $USAGE = $USAGE_TYPE;
              $FUEL_CONSUMPTION = $CONSUMPATION_TYPE;
    
              $USAGE_FROMDATE = $request['FROMDATE'];
              $USAGE_FROMTIME = $request['FROMTIME'];
              $USAGE_TODATE = $request['TODATE'];
              $KVAH_ENDED = $request['KVAH_ENDED'];
              $USAGE_TOTIME = $request['TOTIME'];
              $GENSET_STARTEDBY = $request['GENSET_STARTED_BY'];
              $GENSET_STOPPEDBY = $request['GENSET_STOPPED_BY'];
              $USAGE_READING_START_KWH = $request['KWH_START_USAGE'];
              $USAGE_READING_END_KWH = $request['KWH_END_USAGE'];
              $USAGE_REMARKS = $request['REMARKS'];
              $OPENING_FUEL = $request['OPENDING_FUEL'];
              $FILLED_FUEL = $request['FILLED_FUEL'];
              $CLOSING_FUEL = $request['CLOSING_FUEL'];
              $FUEL_FILLEDBY = $request['FUEL_FILLED_BY'];
              $FUEL_REMARKS = $request['CONSUMPTION_REMARKS'];
              $FUEL_READING_START_KWH = $request['KWH_START_CONSUMPTION'];
              $FUEL_READING_END_KWH = $request['KWH_END_CONSUMPTION'];
              $OBSERVATION = $request['OBSERVATION'];
    
    
        
           $log_data = [$DGUFF_NO,$DGUFF_DATE,$MACHINEID_REF,$FUELID_REF,$STANDARD_CONSUMPTION_PH,$UOMID_REF,$USAGE,
           $FUEL_CONSUMPTION,$USAGE_FROMDATE,$USAGE_FROMTIME,$USAGE_TODATE,$USAGE_TOTIME,
           $GENSET_STARTEDBY,$GENSET_STOPPEDBY,$USAGE_READING_START_KWH, $USAGE_READING_END_KWH,$USAGE_REMARKS,
           $OPENING_FUEL,$FILLED_FUEL,$CLOSING_FUEL,$FUEL_FILLEDBY,$FUEL_REMARKS,$FUEL_READING_START_KWH,$FUEL_READING_END_KWH,
           $OBSERVATION,$CYID_REF,$BRID_REF,$FYID_REF,$VTID_REF,$USERID,Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME,$IPADDRESS,$BOTH_TYPE];
    
    
           //dd($log_data); 
    
           
           $sp_result = DB::select('EXEC SP_TRN_DGUFF_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,? ,?,?,?,?,? ,?,?,?,?,?', $log_data); 


        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $DGUFF_NO. ' Sucessfully Approved.']);

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
                $TABLE      =   "TBL_TRN_DGUFF";
                $FIELD      =   "DGUFF_ID";
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
        $TABLE      =   "TBL_TRN_DGUFF";
        $FIELD      =   "DGUFF_ID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_DGUFF',
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

            $objResponse = DB::table('TBL_TRN_DGUFF')->where('DGUFF_ID','=',$id)->first();

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
        $image_path         =   "docs/company".$CYID_REF."/DGUsageFuelFill";     
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

        return  DB::select('SELECT MAX(DGUFF_DATE) DGUFF_DATE FROM TBL_TRN_DGUFF  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);
    }



    public function codeduplicate(Request $request){

        $DGUFF_NO  =   trim($request['DGUFF_NO']);
        $objLabel = DB::table('TBL_TRN_DGUFF')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('DGUFF_NO','=',$DGUFF_NO)
        ->select('DGUFF_ID')->first();

        if($objLabel){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }

    
}
