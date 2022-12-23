<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm222;
use DB;
use Response;
use Auth;
USE Session;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;


class MstFrm222Controller extends Controller
{
   
    protected $form_id = 222;
    protected $vtid_ref   = 178;  //voucher type id

    //validation messages
    protected   $messages = [
                    'MACHINE_NO.required' => 'Required field',
                    'MACHINE_NO.unique' => 'Duplicate Code',
                    'MACHINE_DESC.required' => 'Required field'
                ];
    
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){

        $objRights = DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first(); 


        $FormId         =   $this->form_id;
        $objDataList    =   DB::table('TBL_MST_MACHINE')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->get();

     

        return view('masters.PlantMaintenance.Machine.mstfrm222',compact(['objRights','objDataList','FormId']));

    }



   

    
    public function add(){ 
    
		
		$CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');    
        $FYID_REF   =   Session::get('FYID_REF');

        $Status = 'A';
		
        
         $cur_date = Date('Y-m-d');
         $ObjFuelType = DB::select('select   * from TBL_MST_FUEL_TYPE  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?)  and STATUS = ? and CYID_REF=? order by FUEL_CODE', [$cur_date, $Status, $CYID_REF]);

         $ObjMainUOM = DB::select('select   * from TBL_MST_UOM  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?)  and STATUS = ? and CYID_REF=? order by UOMCODE', [$cur_date, $Status, $CYID_REF]);

         $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $objDD = DB::table('TBL_MST_DOCNO_DEFINITION')
                ->where('VTID_REF','=',$this->vtid_ref)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->where('FYID_REF','=',$FYID_REF)
                ->where('STATUS','=','A')
                ->select('TBL_MST_DOCNO_DEFINITION.*')
                ->first();

        //dump($objDD);
        $objDOCNO ='';
        if(!empty($objDD)){
            if($objDD->SYSTEM_GRSR == "1")
            {
                if($objDD->PREFIX_RQ == "1")
                {
                    $objDOCNO = $objDD->PREFIX;
                }        
                if($objDD->PRE_SEP_RQ == "1")
                {
                    if($objDD->PRE_SEP_SLASH == "1")
                    {
                    $objDOCNO = $objDOCNO.'/';
                    }
                    if($objDD->PRE_SEP_HYPEN == "1")
                    {
                    $objDOCNO = $objDOCNO.'-';
                    }
                }        
                if($objDD->NO_MAX)
                {   
                    $objDOCNO = $objDOCNO.str_pad($objDD->LAST_RECORDNO+1, $objDD->NO_MAX, "0", STR_PAD_LEFT);
                }
                
                if($objDD->NO_SEP_RQ == "1")
                {
                    if($objDD->NO_SEP_SLASH == "1")
                    {
                    $objDOCNO = $objDOCNO.'/';
                    }
                    if($objDD->NO_SEP_HYPEN == "1")
                    {
                    $objDOCNO = $objDOCNO.'-';
                    }
                }
                if($objDD->SUFFIX_RQ == "1")
                {
                    $objDOCNO = $objDOCNO.$objDD->SUFFIX;
                }
            }
        }   

        
		     
      return view('masters.PlantMaintenance.Machine.mstfrm222add',compact(['ObjFuelType','ObjMainUOM','objDD','objDOCNO']));        
    }

   public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $MACHINE_NO =   $request['MACHINE_NO'];
        
        $objLabel = DB::table('TBL_MST_MACHINE')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('MACHINE_NO','=',$MACHINE_NO)
        ->select('MACHINE_NO')
        ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }

   public function save(Request $request){

        $rules = [
            'MACHINE_NO' => 'required',
            'MACHINE_DESC' => 'required',          
        ];

        $req_data = [

            'MACHINE_NO'  => strtoupper(trim($request['MACHINE_NO']) ),
            'MACHINE_DESC' =>   $request['MACHINE_DESC'],
           
        ]; 


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
            return Response::json(['errors' => $validator->errors()]);	
        }


 
        $MACHINE_NO   =   strtoupper(trim($request['MACHINE_NO']) );
        $MACHINE_DESC   =   trim($request['MACHINE_DESC']);  
       
        $MACHINE_TYPE   =   !empty(trim($request['MACHINE_TYPE'])) ? trim($request['MACHINE_TYPE']) :null;  

        $ASSETID_REF   =  !empty(trim($request['LISTPOP1ID_0'])) ? trim($request['LISTPOP1ID_0']) :null;  
        $ASCATID_REF   =  !empty(trim($request['ASCATID_REF'])) ? trim($request['ASCATID_REF']) :null;  
        $ASTID_REF   =  !empty(trim($request['ASTID_REF'])) ? trim($request['ASTID_REF']) :null;  

        $VENDOR   =  !empty(trim($request['VENDOR'])) ? trim($request['VENDOR']) :null;  
        $COMPANY_NAME   =  !empty(trim($request['COMPANY_NAME'])) ? trim($request['COMPANY_NAME']) :null;  
        $BRAND   =  !empty(trim($request['BRAND'])) ? trim($request['BRAND']) :null;  
        $MODEL_NO   =  !empty(trim($request['MODEL_NO'])) ? trim($request['MODEL_NO']) :null;  
        $SERIAL_NO   =  !empty(trim($request['SERIAL_NO'])) ? trim($request['SERIAL_NO']) :null;  
        $CAPACITY   =  !empty(trim($request['CAPACITY'])) ? trim($request['CAPACITY']) :null;  
        $TECH_SPECI1   =  !empty(trim($request['TECH_SPECI1'])) ? trim($request['TECH_SPECI1']) :null;  
        $TECH_SPECI2   =  !empty(trim($request['TECH_SPECI2'])) ? trim($request['TECH_SPECI2']) :null;  
        $TECH_SPECI3   =  !empty(trim($request['TECH_SPECI3'])) ? trim($request['TECH_SPECI3']) :null;  


        $newDateString = NULL;        
        $newdt = !(is_null($request['DOPURCHASE']) ||empty($request['DOPURCHASE']) )=="true" ? $request['DOPURCHASE'] : NULL; 
        if(!is_null($newdt) ){

            $newdt = str_replace( "/", "-",  $newdt ) ;
            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        $DOPURCHASE = $newDateString;
      
        //------
        $newDateString2 = NULL;
        $newdt2 = !(is_null($request['DOINSTALLATION']) ||empty($request['DOINSTALLATION']) )=="true" ? $request['DOINSTALLATION'] : NULL; 
        if(!is_null($newdt2) ){

            $newdt2 = str_replace( "/", "-",  $newdt2 ) ;
            $newDateString2 = Carbon::parse($newdt2)->format('Y-m-d');        
        }
        $DOINSTALLATION = $newDateString2;
      
        //------
        $newDateString3 = NULL;
        $newdt3 = !(is_null($request['WARRANTY_UPTO']) ||empty($request['WARRANTY_UPTO']) )=="true" ? $request['WARRANTY_UPTO'] : NULL; 
        if(!is_null($newdt3) ){

            $newdt3 = str_replace( "/", "-",  $newdt3 ) ;
            $newDateString3 = Carbon::parse($newdt3)->format('Y-m-d');        
        }
        $WARRANTY_UPTO = $newDateString3;
      
        $SERVICE_STATUS   =  !empty(trim($request['SERVICE_STATUS'])) ? trim($request['SERVICE_STATUS']) :null;  
        $INSTRUCTIONS1   =  !empty(trim($request['INSTRUCTIONS1'])) ? trim($request['INSTRUCTIONS1']) :null;  
        $INSTRUCTIONS2   =  !empty(trim($request['INSTRUCTIONS2'])) ? trim($request['INSTRUCTIONS2']) :null;  
        $REMARKS   =  !empty(trim($request['REMARKS'])) ? trim($request['REMARKS']) :null;  



        $FUELID_REF   =  !empty(trim($request['FUELID_REF'])) ? trim($request['FUELID_REF']) :null;  
        $CONSUMPTION   =  !empty(trim($request['CONSUMPTION'])) ? trim($request['CONSUMPTION']) :null;  
        $UOMID_REF   =  !empty(trim($request['UOMID_REF'])) ? trim($request['UOMID_REF']) :null;  

        $DEACTIVATED    =   0;  
        $DODEACTIVATED  =   null;  

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');    
        $USERID         =   Auth::user()->USERID;   
        $VTID           =   $this->vtid_ref;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();
        
        
        $array_data   = [
                    $MACHINE_NO,
                    $MACHINE_DESC,
                    $MACHINE_TYPE,
                    $ASSETID_REF,
                    $ASCATID_REF,
                    $ASTID_REF,

                    $DEACTIVATED,
                    $DODEACTIVATED,
                    $VENDOR,
                    $COMPANY_NAME,
                    $BRAND,
                    $MODEL_NO,

                    $SERIAL_NO,
                    $CAPACITY,
                    $TECH_SPECI1,
                    $TECH_SPECI2,
                    $TECH_SPECI3,
                    $DOPURCHASE,

                    $DOINSTALLATION,
                    $WARRANTY_UPTO,
                    $SERVICE_STATUS,
                    $INSTRUCTIONS1,
                    $INSTRUCTIONS2,
                    $REMARKS,

                    $FUELID_REF,
                    $CONSUMPTION,
                    $UOMID_REF,                
                    $CYID_REF,                        
                    $BRID_REF,                        
                    $FYID_REF,
                        
                    $VTID, 
                    $USERID,                    
                    $UPDATE,
                    $UPTIME,
                    $ACTION,
                    $IPADDRESS                        
                    ];


        try {

            $sp_result = DB::select('EXEC SP_MACHINE_IN  ?,?,?,?,?,?, ?,?,?,?,?,?, ?,?,?,?,?,?, ?,?,?,?,?,?, ?,?,?,?,?,?, ?,?,?,?,?,?', $array_data);
        
        

         } catch (\Throwable $th) {
            
             return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);

        }
    
        if(Str::contains(strtoupper($sp_result[0]->RESULT), 'SUCCESS')){

            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }elseif(Str::contains(strtoupper($sp_result[0]->RESULT), 'DUPLICATE RECORD')){
        
            return Response::json(['errors'=>true,'msg' => $sp_result[0]->RESULT,'exist'=>'duplicate']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }

        exit();    
    }



    public function edit($id){

        if(!is_null($id))
        {
        
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objResponse = DB::table('TBL_MST_MACHINE')
                            ->where('MACHINEID','=',$id)
                            ->select('*')
                            ->first();
            if(strtoupper($objResponse->STATUS)=="A" || strtoupper($objResponse->STATUS)=="C"){
                exit("Sorry, Only Un Approved record can edit.");
            }

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

          
         
            $Status = 'A';
		
        
            $cur_date = Date('Y-m-d');
            $ObjFuelType = DB::select('select   * from TBL_MST_FUEL_TYPE  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?)  and STATUS = ? and CYID_REF=? order by FUEL_CODE', [$cur_date, $Status, $CYID_REF]);
   
            $ObjMainUOM = DB::select('select   * from TBL_MST_UOM  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?)  and STATUS = ? and CYID_REF=? order by UOMCODE', [$cur_date, $Status, $CYID_REF]);

            $objAsset = DB::table('TBL_MST_ASSET')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('ASSETID','=',$objResponse->ASSETID_REF)
                            ->select('ASSETID','ASSETCODE','DESCRIPTIONS')
                            ->first();

            if(!empty($objAsset)){
                $strAssetID  = $objAsset->ASSETID;
                $strAssetCode  = $objAsset->ASSETCODE;
                $strAssetDesc  = $objAsset->DESCRIPTIONS;
            }else {
                $strAssetID  = '';
                $strAssetCode  = '';
                $strAssetDesc ='';
            }


            $objACat = DB::table('TBL_MST_ASSETCATEGORY')
                            ->where('ASCATID','=',$objResponse->ASCATID_REF)
                            ->select('*')
                            ->first();                            
            if(!empty($objACat)){
                $strAssetCatID  = $objACat->ASCATID;
                $strAssetCatCode  = $objACat->CATEGORY.'-'.$objACat->DESCRIPTIONS;
            }else {
                $strAssetCatID  = '';
                $strAssetCatCode  = '';
            }
                
            
            $objAType = DB::table('TBL_MST_ASSETTYPE')
                            ->where('ASTID','=',$objResponse->ASTID_REF)
                            ->select('*')
                            ->first();

            if(!empty($objAType)){
                $strAssetTypeID  = $objAType->ASTID;
                $strAssetTypeCode  = $objAType->ASSETTYPE.'-'.$objAType->DESCRIPTIONS;
            }else {
                $strAssetTypeID  = '';
                $strAssetTypeCode  = '';
            }                
         
            return view('masters.PlantMaintenance.Machine.mstfrm222edit',compact(['objResponse','user_approval_level','objRights','ObjFuelType','ObjMainUOM','strAssetID','strAssetCode','strAssetDesc','strAssetCatID','strAssetCatCode','strAssetTypeID','strAssetTypeCode']));
        }

    }

     
    public function update(Request $request)
    {
        //----
        $rules = [
            'MACHINE_NO' => 'required',
            'MACHINE_DESC' => 'required',          
        ];

        $req_data = [

            'MACHINE_NO'  => strtoupper(trim($request['MACHINE_NO']) ),
            'MACHINE_DESC' =>   $request['MACHINE_DESC'],
           
        ]; 


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
            return Response::json(['errors' => $validator->errors()]);	
        }


 
        $MACHINE_NO   =   strtoupper(trim($request['MACHINE_NO']) );
        $MACHINE_DESC   =   trim($request['MACHINE_DESC']);  
       
        $MACHINE_TYPE   =   !empty(trim($request['MACHINE_TYPE'])) ? trim($request['MACHINE_TYPE']) :null;  

        $ASSETID_REF   =  !empty(trim($request['LISTPOP1ID_0'])) ? trim($request['LISTPOP1ID_0']) :null;  
        $ASCATID_REF   =  !empty(trim($request['ASCATID_REF'])) ? trim($request['ASCATID_REF']) :null;  
        $ASTID_REF   =  !empty(trim($request['ASTID_REF'])) ? trim($request['ASTID_REF']) :null;  

        $VENDOR   =  !empty(trim($request['VENDOR'])) ? trim($request['VENDOR']) :null;  
        $COMPANY_NAME   =  !empty(trim($request['COMPANY_NAME'])) ? trim($request['COMPANY_NAME']) :null;  
        $BRAND   =  !empty(trim($request['BRAND'])) ? trim($request['BRAND']) :null;  
        $MODEL_NO   =  !empty(trim($request['MODEL_NO'])) ? trim($request['MODEL_NO']) :null;  
        $SERIAL_NO   =  !empty(trim($request['SERIAL_NO'])) ? trim($request['SERIAL_NO']) :null;  
        $CAPACITY   =  !empty(trim($request['CAPACITY'])) ? trim($request['CAPACITY']) :null;  
        $TECH_SPECI1   =  !empty(trim($request['TECH_SPECI1'])) ? trim($request['TECH_SPECI1']) :null;  
        $TECH_SPECI2   =  !empty(trim($request['TECH_SPECI2'])) ? trim($request['TECH_SPECI2']) :null;  
        $TECH_SPECI3   =  !empty(trim($request['TECH_SPECI3'])) ? trim($request['TECH_SPECI3']) :null;  


        $newDateString = NULL;        
        $newdt = !(is_null($request['DOPURCHASE']) ||empty($request['DOPURCHASE']) )=="true" ? $request['DOPURCHASE'] : NULL; 
        if(!is_null($newdt) ){

            $newdt = str_replace( "/", "-",  $newdt ) ;
            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        $DOPURCHASE = $newDateString;
      
        //------
        $newDateString2 = NULL;
        $newdt2 = !(is_null($request['DOINSTALLATION']) ||empty($request['DOINSTALLATION']) )=="true" ? $request['DOINSTALLATION'] : NULL; 
        if(!is_null($newdt2) ){

            $newdt2 = str_replace( "/", "-",  $newdt2 ) ;
            $newDateString2 = Carbon::parse($newdt2)->format('Y-m-d');        
        }
        $DOINSTALLATION = $newDateString2;
      
        //------
        $newDateString3 = NULL;
        $newdt3 = !(is_null($request['WARRANTY_UPTO']) ||empty($request['WARRANTY_UPTO']) )=="true" ? $request['WARRANTY_UPTO'] : NULL; 
        if(!is_null($newdt3) ){

            $newdt3 = str_replace( "/", "-",  $newdt3 ) ;
            $newDateString3 = Carbon::parse($newdt3)->format('Y-m-d');        
        }
        $WARRANTY_UPTO = $newDateString3;
      
        $SERVICE_STATUS   =  !empty(trim($request['SERVICE_STATUS'])) ? trim($request['SERVICE_STATUS']) :null;  
        $INSTRUCTIONS1   =  !empty(trim($request['INSTRUCTIONS1'])) ? trim($request['INSTRUCTIONS1']) :null;  
        $INSTRUCTIONS2   =  !empty(trim($request['INSTRUCTIONS2'])) ? trim($request['INSTRUCTIONS2']) :null;  
        $REMARKS   =  !empty(trim($request['REMARKS'])) ? trim($request['REMARKS']) :null;  



        $FUELID_REF   =  !empty(trim($request['FUELID_REF'])) ? trim($request['FUELID_REF']) :null;  
        $CONSUMPTION   =  !empty(trim($request['CONSUMPTION'])) ? trim($request['CONSUMPTION']) :null;  
        $UOMID_REF   =  !empty(trim($request['UOMID_REF'])) ? trim($request['UOMID_REF']) :null;  

        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
        $newDateString5 = NULL;
        $newdt5 = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt5) ){
            
            $newdt5 = str_replace( "/", "-",  $newdt5 ) ;
            $newDateString5 = Carbon::parse($newdt5)->format('Y-m-d');        
        }
        $DODEACTIVATED = $newDateString5;

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');    
        $USERID         =   Auth::user()->USERID;   
        $VTID           =   $this->vtid_ref;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "EDIT";
        $IPADDRESS      =   $request->getClientIp();
        
        
        $array_data   = [
                    $MACHINE_NO,
                    $MACHINE_DESC,
                    $MACHINE_TYPE,
                    $ASSETID_REF,
                    $ASCATID_REF,
                    $ASTID_REF,

                    $DEACTIVATED,
                    $DODEACTIVATED,
                    $VENDOR,
                    $COMPANY_NAME,
                    $BRAND,
                    $MODEL_NO,

                    $SERIAL_NO,
                    $CAPACITY,
                    $TECH_SPECI1,
                    $TECH_SPECI2,
                    $TECH_SPECI3,
                    $DOPURCHASE,

                    $DOINSTALLATION,
                    $WARRANTY_UPTO,
                    $SERVICE_STATUS,
                    $INSTRUCTIONS1,
                    $INSTRUCTIONS2,
                    $REMARKS,

                    $FUELID_REF,
                    $CONSUMPTION,
                    $UOMID_REF,                
                    $CYID_REF,                        
                    $BRID_REF,                        
                    $FYID_REF,
                        
                    $VTID, 
                    $USERID,                    
                    $UPDATE,
                    $UPTIME,
                    $ACTION,
                    $IPADDRESS                        
                    ];


       try {            
       

            $sp_result = DB::select('EXEC SP_MACHINE_UP  ?,?,?,?,?,?, ?,?,?,?,?,?, ?,?,?,?,?,?, ?,?,?,?,?,?, ?,?,?,?,?,?, ?,?,?,?,?,?', $array_data);    
       
        } catch (\Throwable $th) {

             return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);

        }    

        if($sp_result[0]->RESULT=="SUCCESS"){  

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOUND"){
        
            return Response::json(['errors'=>true,'msg' => 'No record found.','exist'=>'norecord']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);
        }
        
        exit();            
    } 

    //uploads attachments files
    public function docuploads(Request $request){

        $formData = $request->all();
  
        $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size = config("erpconst.attachments.max_size") * 1020 * 1024;

        //echo '<br> c='."--".Config("erpconst.attachments.max_size");
        
        //get data
        $VTID           =   $formData["VTID_REF"]; 
        $ATTACH_DOCNO   =   $formData["ATTACH_DOCNO"]; 

        $ATTACH_DOCDT   =   $formData["ATTACH_DOCDT"]; 
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');       
        // @XML	xml
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();
        
        $destinationPath = storage_path()."/docs/company".$CYID_REF."/Machine";

        if ( !is_dir($destinationPath) ) {
            mkdir($destinationPath, 0777, true);
        }

        $uploaded_data = [];
    
        $invlid_files = "";

        $duplicate_files="";

        foreach($formData["REMARKS"] as $index=>$row_val){

                if(isset($formData["FILENAME"][$index])){

                    $uploadedFile = $formData["FILENAME"][$index]; 
                    
                    //$filenamewithextension  = $formData["FILENAME"][$index]->getClientOriginalName();

                    $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
                    $filesize               =   $uploadedFile ->getSize();  
                    $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );

                    //$filenametostore        =   $filenamewithextension; 

                    $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."#_".$filenamewithextension;  

                    if ($uploadedFile->isValid()) {

                        if(in_array($extension,$allow_extnesions)){
                            
                            if($filesize < $allow_size){

                                $filename = $destinationPath."/".$filenametostore;

                                if (!file_exists($filename)) {

                                   $uploadedFile->move($destinationPath, $filenametostore);  //upload in dir if not exists
                                   $uploaded_data[$index]["FILENAME"] =$filenametostore;
                                   $uploaded_data[$index]["LOCATION"] = $destinationPath."/";
                                   $uploaded_data[$index]["REMARKS"] = is_null($row_val) ? '' : trim($row_val);

                                }else{

                                    $duplicate_files = " ". $duplicate_files.$filenamewithextension. " ";
                                }
                                

                                
                            }else{
                                
                                $invlid_files = $invlid_files.$filenamewithextension." (invalid size)  "; 
                            } //invalid size
                            
                        }else{

                            $invlid_files = $invlid_files.$filenamewithextension." (invalid extension)  ";                             
                        }// invalid extension
                    
                    }else{
                            
                        $invlid_files = $invlid_files.$filenamewithextension." (invalid)"; 
                    }//invalid

                }

        }//foreach

      
        if(empty($uploaded_data)){
            return redirect()->route("master",[222,"attachment",$ATTACH_DOCNO])->with("success","The file is already exist");
        }
     

        $wrapped_links["ATTACHMENT"] = $uploaded_data;     //root node: <ATTACHMENT>
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

             //save data
             $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);

        

     
        if($sp_result[0]->RESULT=="SUCCESS"){

            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }

            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }

            return redirect()->route("master",[222,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[222,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("master",[222,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
            return redirect()->route("master",[222,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   

  


    //singleApprove begin
    public function singleapprove(Request $request)
    {
        //----
        $rules = [
            'MACHINE_NO' => 'required',
            'MACHINE_DESC' => 'required',          
        ];

        $req_data = [

            'MACHINE_NO'  => strtoupper(trim($request['MACHINE_NO']) ),
            'MACHINE_DESC' =>   $request['MACHINE_DESC'],
        
        ]; 


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
            return Response::json(['errors' => $validator->errors()]);	
        }



        $MACHINE_NO   =   strtoupper(trim($request['MACHINE_NO']) );
        $MACHINE_DESC   =   trim($request['MACHINE_DESC']);  

        $MACHINE_TYPE   =   !empty(trim($request['MACHINE_TYPE'])) ? trim($request['MACHINE_TYPE']) :null;  

        $ASSETID_REF   =  !empty(trim($request['LISTPOP1ID_0'])) ? trim($request['LISTPOP1ID_0']) :null;  
        $ASCATID_REF   =  !empty(trim($request['ASCATID_REF'])) ? trim($request['ASCATID_REF']) :null;  
        $ASTID_REF   =  !empty(trim($request['ASTID_REF'])) ? trim($request['ASTID_REF']) :null;  

        $VENDOR   =  !empty(trim($request['VENDOR'])) ? trim($request['VENDOR']) :null;  
        $COMPANY_NAME   =  !empty(trim($request['COMPANY_NAME'])) ? trim($request['COMPANY_NAME']) :null;  
        $BRAND   =  !empty(trim($request['BRAND'])) ? trim($request['BRAND']) :null;  
        $MODEL_NO   =  !empty(trim($request['MODEL_NO'])) ? trim($request['MODEL_NO']) :null;  
        $SERIAL_NO   =  !empty(trim($request['SERIAL_NO'])) ? trim($request['SERIAL_NO']) :null;  
        $CAPACITY   =  !empty(trim($request['CAPACITY'])) ? trim($request['CAPACITY']) :null;  
        $TECH_SPECI1   =  !empty(trim($request['TECH_SPECI1'])) ? trim($request['TECH_SPECI1']) :null;  
        $TECH_SPECI2   =  !empty(trim($request['TECH_SPECI2'])) ? trim($request['TECH_SPECI2']) :null;  
        $TECH_SPECI3   =  !empty(trim($request['TECH_SPECI3'])) ? trim($request['TECH_SPECI3']) :null;  


        $newDateString = NULL;        
        $newdt = !(is_null($request['DOPURCHASE']) ||empty($request['DOPURCHASE']) )=="true" ? $request['DOPURCHASE'] : NULL; 
        if(!is_null($newdt) ){

            $newdt = str_replace( "/", "-",  $newdt ) ;
            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        $DOPURCHASE = $newDateString;

        //------
        $newDateString2 = NULL;
        $newdt2 = !(is_null($request['DOINSTALLATION']) ||empty($request['DOINSTALLATION']) )=="true" ? $request['DOINSTALLATION'] : NULL; 
        if(!is_null($newdt2) ){

            $newdt2 = str_replace( "/", "-",  $newdt2 ) ;
            $newDateString2 = Carbon::parse($newdt2)->format('Y-m-d');        
        }
        $DOINSTALLATION = $newDateString2;

        //------
        $newDateString3 = NULL;
        $newdt3 = !(is_null($request['WARRANTY_UPTO']) ||empty($request['WARRANTY_UPTO']) )=="true" ? $request['WARRANTY_UPTO'] : NULL; 
        if(!is_null($newdt3) ){

            $newdt3 = str_replace( "/", "-",  $newdt3 ) ;
            $newDateString3 = Carbon::parse($newdt3)->format('Y-m-d');        
        }
        $WARRANTY_UPTO = $newDateString3;

        $SERVICE_STATUS   =  !empty(trim($request['SERVICE_STATUS'])) ? trim($request['SERVICE_STATUS']) :null;  
        $INSTRUCTIONS1   =  !empty(trim($request['INSTRUCTIONS1'])) ? trim($request['INSTRUCTIONS1']) :null;  
        $INSTRUCTIONS2   =  !empty(trim($request['INSTRUCTIONS2'])) ? trim($request['INSTRUCTIONS2']) :null;  
        $REMARKS   =  !empty(trim($request['REMARKS'])) ? trim($request['REMARKS']) :null;  



        $FUELID_REF   =  !empty(trim($request['FUELID_REF'])) ? trim($request['FUELID_REF']) :null;  
        $CONSUMPTION   =  !empty(trim($request['CONSUMPTION'])) ? trim($request['CONSUMPTION']) :null;  
        $UOMID_REF   =  !empty(trim($request['UOMID_REF'])) ? trim($request['UOMID_REF']) :null;  

        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;

        $newDateString5 = NULL;
        $newdt5 = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt5) ){
            
            $newdt5 = str_replace( "/", "-",  $newdt5 ) ;
            $newDateString5 = Carbon::parse($newdt5)->format('Y-m-d');        
        }
        $DODEACTIVATED = $newDateString5;

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');    
        $USERID         =   Auth::user()->USERID;   
        $VTID           =   $this->vtid_ref;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   trim($request['user_approval_level']);   // user approval level value
        $IPADDRESS      =   $request->getClientIp();


        $array_data   = [
                    $MACHINE_NO,
                    $MACHINE_DESC,
                    $MACHINE_TYPE,
                    $ASSETID_REF,
                    $ASCATID_REF,
                    $ASTID_REF,

                    $DEACTIVATED,
                    $DODEACTIVATED,
                    $VENDOR,
                    $COMPANY_NAME,
                    $BRAND,
                    $MODEL_NO,

                    $SERIAL_NO,
                    $CAPACITY,
                    $TECH_SPECI1,
                    $TECH_SPECI2,
                    $TECH_SPECI3,
                    $DOPURCHASE,

                    $DOINSTALLATION,
                    $WARRANTY_UPTO,
                    $SERVICE_STATUS,
                    $INSTRUCTIONS1,
                    $INSTRUCTIONS2,
                    $REMARKS,

                    $FUELID_REF,
                    $CONSUMPTION,
                    $UOMID_REF,                
                    $CYID_REF,                        
                    $BRID_REF,                        
                    $FYID_REF,
                        
                    $VTID, 
                    $USERID,                    
                    $UPDATE,
                    $UPTIME,
                    $ACTION,
                    $IPADDRESS                        
                    ];


        try {            


            $sp_result = DB::select('EXEC SP_MACHINE_UP  ?,?,?,?,?,?, ?,?,?,?,?,?, ?,?,?,?,?,?, ?,?,?,?,?,?, ?,?,?,?,?,?, ?,?,?,?,?,?', $array_data);    
        
         
         
       
        } catch (\Throwable $th) {

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);

        }    
                    
        if($sp_result[0]->RESULT=="SUCCESS"){  
 
             return Response::json(['success' =>true,'msg' => 'Record successfully approved.']);
         
         }elseif($sp_result[0]->RESULT=="NO RECORD FOUND"){
         
             return Response::json(['errors'=>true,'msg' => 'No record found.','exist'=>'norecord']);
             
         }else{
 
             return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);
         }
         
         exit();  

     }  //singleApprove end


    public function view($id){

        if(!is_null($id))
        {
        
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objResponse = DB::table('TBL_MST_MACHINE')
                            ->where('MACHINEID','=',$id)
                            ->select('*')
                            ->first();

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

          
         
            $Status = 'A';
		
        
            $cur_date = Date('Y-m-d');
            $ObjFuelType = DB::select('select   * from TBL_MST_FUEL_TYPE  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?)  and STATUS = ? and CYID_REF=? order by FUEL_CODE', [$cur_date, $Status, $CYID_REF]);
   
            $ObjMainUOM = DB::select('select   * from TBL_MST_UOM  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?)  and STATUS = ? and CYID_REF=? order by UOMCODE', [$cur_date, $Status, $CYID_REF]);

            $objAsset = DB::table('TBL_MST_ASSET')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('ASSETID','=',$objResponse->ASSETID_REF)
                            ->select('ASSETID','ASSETCODE','DESCRIPTIONS')
                            ->first();

            if(!empty($objAsset)){
                $strAssetID  = $objAsset->ASSETID;
                $strAssetCode  = $objAsset->ASSETCODE;
                $strAssetDesc  = $objAsset->DESCRIPTIONS;
            }else {
                $strAssetID  = '';
                $strAssetCode  = '';
                $strAssetDesc ='';
            }


            $objACat = DB::table('TBL_MST_ASSETCATEGORY')
                            ->where('ASCATID','=',$objResponse->ASCATID_REF)
                            ->select('*')
                            ->first();                            
            if(!empty($objACat)){
                $strAssetCatID  = $objACat->ASCATID;
                $strAssetCatCode  = $objACat->CATEGORY.'-'.$objACat->DESCRIPTIONS;
            }else {
                $strAssetCatID  = '';
                $strAssetCatCode  = '';
            }
                
            
            $objAType = DB::table('TBL_MST_ASSETTYPE')
                            ->where('ASTID','=',$objResponse->ASTID_REF)
                            ->select('*')
                            ->first();

            if(!empty($objAType)){
                $strAssetTypeID  = $objAType->ASTID;
                $strAssetTypeCode  = $objAType->ASSETTYPE.'-'.$objAType->DESCRIPTIONS;
            }else {
                $strAssetTypeID  = '';
                $strAssetTypeCode  = '';
            }                
         
            return view('masters.PlantMaintenance.Machine.mstfrm222view',compact(['objResponse','user_approval_level','objRights','ObjFuelType','ObjMainUOM','strAssetID','strAssetCode','strAssetDesc','strAssetCatID','strAssetCatCode','strAssetTypeID','strAssetTypeCode']));
        }
        

    }
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }
        $objResponse = TblMstFrm222::whereIn('MACHINEID',$ids_data)->get();
        
        return view('masters.PlantMaintenance.Machine.mstfrm222print',compact(['objResponse']));
   }//print




        //display attachments form
        public function attachment($id){

            if(!is_null($id))
            {
                //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
                
                $objResponse = DB::table('TBL_MST_MACHINE')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('MACHINEID','=',$id)
                    ->select('*')
                    ->first();                

                
                $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
                        ->where('VTID','=',$this->vtid_ref)
                         ->select('VTID','VCODE','DESCRIPTIONS')
                        ->get()
                        ->toArray();
    
                
                        //uplaoded docs
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
    
                     
    
                return view('masters.PlantMaintenance.Machine.mstfrm222attachment',compact(['objResponse','objMstVoucherType','objAttachments']));
            }
    
        }
        
    
    public function MultiApprove(Request $request){

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
                foreach ($sp_listing_result as $key=>$valueitem)
            {  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$valueitem->LAVELS;
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
            $VTID_REF   =   $this->vtid_ref;  //voucher type id
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');
            $FYID_REF   =   Session::get('FYID_REF');       
            $TABLE      =   "TBL_MST_MACHINE";
            $FIELD      =   "MACHINEID";
            $ACTIONNAME     = $Approvallevel;
            $UPDATE     =   Date('Y-m-d');
            $UPTIME     =   Date('h:i:s.u');
            $IPADDRESS  =   $request->getClientIp();
        
        
        
      
        
        $log_data = [
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];

            
        $sp_result = DB::select('EXEC SP_MST_MULTIAPPROVAL ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
        
        

        if($sp_result[0]->RESULT=="All records approved"){

        return Response::json(['approve' =>true,'msg' => 'Record successfully Approved.']);

        }elseif($sp_result[0]->RESULT=="NO RECORD FOR APPROVAL"){
        
        return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','exist'=>'norecord']);
        
        }else{
        return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','exist'=>'Some Error']);
        }
        
        exit();    
        }


        //Cancel the data
   public function cancel(Request $request){
     
      
    $id = $request->{0};    
        

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_MACHINE";
        $FIELD      =   "MACHINEID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $cancelData[0]= ['NT' =>'TBL_MST_MACHINE'];
        $cancel_links["TABLES"] = $cancelData;
        $cancelxml = ArrayToXml::convert($cancel_links);

        $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$cancelxml];

        $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);

        if($sp_result[0]->RESULT=="CANCELED"){  
          //  echo 'in cancel';
          return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
        
            //echo "NO RECORD FOR CANCEL";
            return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
            
        }else{
            //echo "--else--";
               return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'invalid'=>'invalid']);
        }
        
        exit(); 
}

    public function getasset(Request $request){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        
                      
        $cur_date = Date('Y-m-d');
        
        $ObjData = DB::select('select  * from TBL_MST_ASSET  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?)  and CYID_REF = ? and STATUS = ? order by ASSETCODE', [$cur_date, $CYID_REF, $Status]);
                         

            if(!empty($ObjData)){

            foreach ($ObjData as $index=>$dataRow){

                $ObjACat = DB::select('select  top 1 * from TBL_MST_ASSETCATEGORY  where (ACTIVE=0 or ACTIVE is null or ACTIVE=1) AND (ACTIVEDATE is null or ACTIVEDATE>=?)  and STATUS = ? and ASCATID=? order by CATEGORY', [$cur_date, $Status, $dataRow->ASCATID_REF]);
               
                if(!empty($ObjACat)){
                    $asset_cat_id =  $ObjACat[0]->ASCATID;
                    $asset_cat_code =  $ObjACat[0]->CATEGORY;
                    $asset_cat_desc =  $ObjACat[0]->DESCRIPTIONS;
                }else{
                    $asset_cat_id =  '';
                    $asset_cat_code =  '';
                    $asset_cat_desc =  '';
                }

                $ObjAType = DB::select('select  top 1 * from TBL_MST_ASSETTYPE  where (ACTIVE=0 or ACTIVE is null or ACTIVE=1) AND (ACTIVEDATE is null or ACTIVEDATE>=?)   and ASTID=? order by ASSETTYPE', [$cur_date, $dataRow->ASTID_REF]);
               
                if(!empty($ObjAType)){
                    $asset_type_id =  $ObjAType[0]->ASTID;
                    $asset_type_code =  $ObjAType[0]->ASSETTYPE;
                    $asset_type_desc =  $ObjAType[0]->DESCRIPTIONS;
                }else{
                    $asset_type_id =  '';
                    $asset_type_code =  '';
                    $asset_type_desc =  '';
                }


                // $row = '';
                // $row = $row.'<tr id="LISTPOP1code_'.$dataRow->ASSETID .'"  class="clsLISTPOP1id"><td width="50%">'.$dataRow->ASSETCODE;
                // $row = $row.'<input type="hidden" id="txtLISTPOP1code_'.$dataRow->ASSETID.'" data-desc="'.$dataRow->ASSETCODE.'"  data-descdate="'.$dataRow->DESCRIPTIONS.'"
                // value="'.$dataRow->ASSETID.'"  data-asset_cat_id="'.$asset_cat_id.'" data-asset_cat_code="'.$asset_cat_code.'" data-asset_cat_desc="'.$asset_cat_desc.'" data-asset_type_id="'.$asset_type_id.'"  data-asset_type_code="'.$asset_type_code.'" data-asset_type_desc="'.$asset_type_desc.'"  /></td><td>'.$dataRow->DESCRIPTIONS.'</td></tr>';
                // echo $row;

                $row = '';
                $row = $row.'<tr >
                <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_ASSETID_REF[]" id="LISTPOP1code_'.$dataRow->ASSETID .'"  class="clsLISTPOP1id" value="'.$dataRow->ASSETID.'" ></td>
                <td width="39%" class="ROW2">'.$dataRow->ASSETCODE;
                $row = $row.'<input type="hidden" id="txtLISTPOP1code_'.$dataRow->ASSETID.'" data-desc="'.$dataRow->ASSETCODE.'"  data-descdate="'.$dataRow->DESCRIPTIONS.'"
                value="'.$dataRow->ASSETID.'"  data-asset_cat_id="'.$asset_cat_id.'" data-asset_cat_code="'.$asset_cat_code.'" data-asset_cat_desc="'.$asset_cat_desc.'" data-asset_type_id="'.$asset_type_id.'"  data-asset_type_code="'.$asset_type_code.'" data-asset_type_desc="'.$asset_type_desc.'"  /></td><td width="39%" class="ROW3">'.$dataRow->DESCRIPTIONS.'</td></tr>';
                echo $row;

            }
            }else{
                echo '<tr><td colspan="3">Record not found.</td></tr>';
            }
            exit();
    }


}
