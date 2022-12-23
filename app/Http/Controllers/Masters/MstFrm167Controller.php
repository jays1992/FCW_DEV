<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm167;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;


class MstFrm167Controller extends Controller
{
   
    protected $form_id = 167;
    protected $vtid_ref   = 304;  //voucher type id

    //validation messages
    protected   $messages = [
                    'AC_SET_CODE.required' => 'Required field',
                    'AC_SET_CODE.unique' => 'Duplicate Code',
                    'AC_SET_DESC.required' => 'Required field'
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
        $objDataList    =   DB::table('TBL_MST_PURCHASE_AC_SET')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->get();

     

        return view('masters.Accounts.PurchaseAccountSet.mstfrm167',compact(['objRights','objDataList','FormId']));

    }



   

    
    public function add(){ 
    
		
		$CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');    
        $FYID_REF   =   Session::get('FYID_REF');

		
        $objLedgerList = DB::table('TBL_MST_GENERALLEDGER')
         ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('GLID','GLCODE','GLNAME')
        ->get();
        


		     
      return view('masters.Accounts.PurchaseAccountSet.mstfrm167add',compact(['objLedgerList']));        
    }

   public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $AC_SET_CODE =   $request['AC_SET_CODE'];
        
        $objLabel = DB::table('TBL_MST_PURCHASE_AC_SET')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('AC_SET_CODE','=',$AC_SET_CODE)
        ->select('AC_SET_CODE')
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
            'AC_SET_CODE' => 'required|unique:TBL_MST_PURCHASE_AC_SET',
            'AC_SET_CODE' => 'required',          
            'AC_SET_DESC' => 'required',          
        ];

        $req_data = [

            'AC_SET_CODE'  => strtoupper(trim($request['AC_SET_CODE']) ),
            'AC_SET_CODE' =>   $request['AC_SET_DESC'],
            'AC_SET_DESC' =>   $request['AC_SET_DESC'],
           
        ]; 


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
            return Response::json(['errors' => $validator->errors()]);	
        }


 
        $AC_SET_CODE   =   strtoupper(trim($request['AC_SET_CODE']) );
        $AC_SET_DESC   =   trim($request['AC_SET_DESC']);  
       
        $PURCHASE_AC   =   !empty(trim($request['LISTPOP1ID_0'])) ? trim($request['LISTPOP1ID_0']) :null;  
        $PAYABLE_CLEARING   =  !empty(trim($request['LISTPOP1ID_1'])) ? trim($request['LISTPOP1ID_1']) :null;  
        $INVENTORY_AC   =  !empty(trim($request['LISTPOP1ID_2'])) ? trim($request['LISTPOP1ID_2']) :null;  

        $ADJUSTMENT_WRITEOFF   =  !empty(trim($request['LISTPOP1ID_3'])) ? trim($request['LISTPOP1ID_3']) :null;  
        $TRANSFER_CLEARING   =   !empty(trim($request['LISTPOP1ID_4'])) ? trim($request['LISTPOP1ID_4']) :null;    
        $STOCK_TRANSFER_AC   =    !empty(trim($request['LISTPOP1ID_5'])) ? trim($request['LISTPOP1ID_5']) :null;    

        $RM_CONSUMPTION   =    !empty(trim($request['LISTPOP1ID_6'])) ? trim($request['LISTPOP1ID_6']) :null; 
        $REJECTED   =   !empty(trim($request['LISTPOP1ID_7'])) ? trim($request['LISTPOP1ID_7']) :null;  
        $SHORTAGE   =    !empty(trim($request['LISTPOP1ID_8'])) ? trim($request['LISTPOP1ID_8']) :null;  

        $PHY_INVENTORY_ADJ   =    !empty(trim($request['LISTPOP1ID_9'])) ? trim($request['LISTPOP1ID_9']) :null;  
        $WIP_AC   =    !empty(trim($request['LISTPOP1ID_10'])) ? trim($request['LISTPOP1ID_10']) :null;   
        $GAIN_LOSS_AC   =    !empty(trim($request['LISTPOP1ID_11'])) ? trim($request['LISTPOP1ID_11']) :null;   

        $FA_AC   =    !empty(trim($request['LISTPOP1ID_12'])) ? trim($request['LISTPOP1ID_12']) :null;   
        $FA_CLEANING_AC   =    !empty(trim($request['LISTPOP1ID_13'])) ? trim($request['LISTPOP1ID_13']) :null;  
        $DEPR_AC   =    !empty(trim($request['LISTPOP1ID_14'])) ? trim($request['LISTPOP1ID_14']) :null;    
        $PRTN_AC   =    !empty(trim($request['LISTPOP1ID_15'])) ? trim($request['LISTPOP1ID_15']) :null;    
        $IPOR_AC   =    !empty(trim($request['LISTPOP1ID_16'])) ? trim($request['LISTPOP1ID_16']) :null;    
        $JWI_AC   =    !empty(trim($request['LISTPOP1ID_17'])) ? trim($request['LISTPOP1ID_17']) :null;    
        $JWR_AC   =    !empty(trim($request['LISTPOP1ID_18'])) ? trim($request['LISTPOP1ID_18']) :null;   
        $CUSTOM_DUTY_AC   =    !empty(trim($request['LISTPOP1ID_19'])) ? trim($request['LISTPOP1ID_19']) :null;  
		$PURCHASEIS_AC   =    !empty(trim($request['LISTPOP1ID_20'])) ? trim($request['LISTPOP1ID_20']) :null; 


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
                        $AC_SET_CODE,
                        $AC_SET_DESC,
                        $PURCHASE_AC,
                        $PAYABLE_CLEARING,
                        
                        $INVENTORY_AC,
                        $ADJUSTMENT_WRITEOFF,
                        $TRANSFER_CLEARING,
                        $STOCK_TRANSFER_AC,
                
                        $RM_CONSUMPTION,
                        $REJECTED,
                        $SHORTAGE,
                        $PHY_INVENTORY_ADJ,

                        $WIP_AC,
                        $GAIN_LOSS_AC,
                        $FA_AC,
                        $DEACTIVATED,
                        
                        NULL, 
                        $FA_CLEANING_AC,
                        $DEPR_AC,
                        $CYID_REF,
                        
                        $BRID_REF,
                        $FYID_REF, 
                        $VTID, 
                        $USERID,
                        
                        $UPDATE,
                        $UPTIME,
                        $ACTION,
                        $IPADDRESS ,
                        $PRTN_AC, 
                        $IPOR_AC,
                        $JWI_AC,
                        $JWR_AC,
                        $CUSTOM_DUTY_AC,
						$PURCHASEIS_AC
                    ];

                   
       try {

            $sp_result = DB::select('EXEC SP_PURCHASE_AC_SET_IN  ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?,?,?', $array_data);

          } catch (\Throwable $th) {
            
                return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);

          }
    
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
        
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','exist'=>'duplicate']);
            
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

            $objResponse = DB::table('TBL_MST_PURCHASE_AC_SET')
                            ->where('PR_AC_SETID','=',$id)
                            ->where('STATUS','=','N')
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

          
         
           $objPURCHASE_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->PURCHASE_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();

           $objPAYABLE_CLEARING = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->PAYABLE_CLEARING)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           
           $objINVENTORY_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->INVENTORY_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           
           
           $objADJUSTMENT_WRITEOFF = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->ADJUSTMENT_WRITEOFF)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           
           $objTRANSFER_CLEARING = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->TRANSFER_CLEARING)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           
           $objSTOCK_TRANSFER_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->STOCK_TRANSFER_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           
           $objRM_CONSUMPTION = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->RM_CONSUMPTION)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           
           $objREJECTED = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->REJECTED)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
            
            
           $objSHORTAGE = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->SHORTAGE)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
         
           $objPHY_INVENTORY_ADJ = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->PHY_INVENTORY_ADJ)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
         
           $objWIP_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->WIP_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
         
           $objGAIN_LOSS_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->GAIN_LOSS_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
         
           $objFA_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->FA_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
         
           $objFA_CLEANING_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->FA_CLEANING_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
         
           $objDEPR_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->DEPR_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();

           $objPURCHASE_RETURN_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->PURCHASE_RETURN_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();

           $objIMPORT_PURCHASE_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->IMPORT_PURCHASE_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();

           $objJWI = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->JWI)
           ->select('GLID','GLCODE','GLNAME')
           ->first();

           $objJWR = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->JWR)
           ->select('GLID','GLCODE','GLNAME')
           ->first();

           $objCUSTOM = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->CUSTOM_DUTY_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
		   
		   $objPURCHASEIS_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->PURCHASEIS_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
         
            return view('masters.Accounts.PurchaseAccountSet.mstfrm167edit',compact(['objResponse','user_approval_level','objRights',
            'objPURCHASE_AC','objPAYABLE_CLEARING','objINVENTORY_AC','objADJUSTMENT_WRITEOFF','objTRANSFER_CLEARING','objSTOCK_TRANSFER_AC',
            'objRM_CONSUMPTION','objREJECTED','objSHORTAGE','objPHY_INVENTORY_ADJ','objWIP_AC','objGAIN_LOSS_AC','objFA_AC',
            'objFA_CLEANING_AC','objDEPR_AC','objPURCHASE_RETURN_AC','objIMPORT_PURCHASE_AC','objJWI','objJWR','objCUSTOM','objPURCHASEIS_AC']));
        }

    }

     
    public function update(Request $request)
    {

        $rules = [
            'AC_SET_CODE' => 'required|unique:TBL_MST_PURCHASE_AC_SET',
            'AC_SET_CODE' => 'required',          
            'AC_SET_DESC' => 'required',          
        ];
        
        $req_data = [
        
            'AC_SET_CODE'  => strtoupper(trim($request['AC_SET_CODE']) ),
            'AC_SET_CODE' =>   $request['AC_SET_DESC'],
            'AC_SET_DESC' =>   $request['AC_SET_DESC'],
           
        ]; 
        
        
        $validator = Validator::make( $req_data, $rules, $this->messages);
        
        if ($validator->fails())
        {
            return Response::json(['errors' => $validator->errors()]);	
        }
        
        
        
        $AC_SET_CODE   =   strtoupper(trim($request['AC_SET_CODE']) );
        $AC_SET_DESC   =   trim($request['AC_SET_DESC']);  
        
        $PURCHASE_AC   =   !empty(trim($request['LISTPOP1ID_0'])) ? trim($request['LISTPOP1ID_0']) :null;  
        $PAYABLE_CLEARING   =  !empty(trim($request['LISTPOP1ID_1'])) ? trim($request['LISTPOP1ID_1']) :null;  
        $INVENTORY_AC   =  !empty(trim($request['LISTPOP1ID_2'])) ? trim($request['LISTPOP1ID_2']) :null;  
        
        $ADJUSTMENT_WRITEOFF   =  !empty(trim($request['LISTPOP1ID_3'])) ? trim($request['LISTPOP1ID_3']) :null;  
        $TRANSFER_CLEARING   =   !empty(trim($request['LISTPOP1ID_4'])) ? trim($request['LISTPOP1ID_4']) :null;    
        $STOCK_TRANSFER_AC   =    !empty(trim($request['LISTPOP1ID_5'])) ? trim($request['LISTPOP1ID_5']) :null;    
        
        $RM_CONSUMPTION   =    !empty(trim($request['LISTPOP1ID_6'])) ? trim($request['LISTPOP1ID_6']) :null; 
        $REJECTED   =   !empty(trim($request['LISTPOP1ID_7'])) ? trim($request['LISTPOP1ID_7']) :null;  
        $SHORTAGE   =    !empty(trim($request['LISTPOP1ID_8'])) ? trim($request['LISTPOP1ID_8']) :null;  
        
        $PHY_INVENTORY_ADJ   =    !empty(trim($request['LISTPOP1ID_9'])) ? trim($request['LISTPOP1ID_9']) :null;  
        $WIP_AC   =    !empty(trim($request['LISTPOP1ID_10'])) ? trim($request['LISTPOP1ID_10']) :null;   
        $GAIN_LOSS_AC   =    !empty(trim($request['LISTPOP1ID_11'])) ? trim($request['LISTPOP1ID_11']) :null;   
        
        $FA_AC   =    !empty(trim($request['LISTPOP1ID_12'])) ? trim($request['LISTPOP1ID_12']) :null;   
        $FA_CLEANING_AC   =    !empty(trim($request['LISTPOP1ID_13'])) ? trim($request['LISTPOP1ID_13']) :null;  
        $DEPR_AC   =    !empty(trim($request['LISTPOP1ID_14'])) ? trim($request['LISTPOP1ID_14']) :null; 
        $DEPR_AC   =    !empty(trim($request['LISTPOP1ID_14'])) ? trim($request['LISTPOP1ID_14']) :null;    
        $PRTN_AC   =    !empty(trim($request['LISTPOP1ID_15'])) ? trim($request['LISTPOP1ID_15']) :null;    
        $IPOR_AC   =    !empty(trim($request['LISTPOP1ID_16'])) ? trim($request['LISTPOP1ID_16']) :null;    
        $JWI_AC   =    !empty(trim($request['LISTPOP1ID_17'])) ? trim($request['LISTPOP1ID_17']) :null;    
        $JWR_AC   =    !empty(trim($request['LISTPOP1ID_18'])) ? trim($request['LISTPOP1ID_18']) :null;    
        $CUSTOM_DUTY_AC   =    !empty(trim($request['LISTPOP1ID_19'])) ? trim($request['LISTPOP1ID_19']) :null;   
		$PURCHASEIS_AC   =    !empty(trim($request['LISTPOP1ID_20'])) ? trim($request['LISTPOP1ID_20']) :null; 
		
        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
        $newDateString = NULL;

        $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt) ){
            
            $newdt = str_replace( "/", "-",  $newdt ) ;

            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        

        $DODEACTIVATED = $newDateString;

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID     =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();
        
        $array_data   = [
            $AC_SET_CODE,
            $AC_SET_DESC,
            $PURCHASE_AC,
            $PAYABLE_CLEARING,
            
            $INVENTORY_AC,
            $ADJUSTMENT_WRITEOFF,
            $TRANSFER_CLEARING,
            $STOCK_TRANSFER_AC,
    
            $RM_CONSUMPTION,
            $REJECTED,
            $SHORTAGE,
            $PHY_INVENTORY_ADJ,

            $WIP_AC,
            $GAIN_LOSS_AC,
            $FA_AC,
            $DEACTIVATED,
            
            $DODEACTIVATED, 
            $FA_CLEANING_AC,
            $DEPR_AC,
            $CYID_REF,
            
            $BRID_REF,
            $FYID_REF, 
            $VTID, 
            $USERID,
            
            $UPDATE,
            $UPTIME,
            $ACTION,
            $IPADDRESS,
            $PRTN_AC, 
            $IPOR_AC,
            $JWI_AC,
            $JWR_AC,$CUSTOM_DUTY_AC,$PURCHASEIS_AC                       
        ];

        try {

        $sp_result = DB::select('EXEC SP_PURCHASE_AC_SET_UP  ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?,?,?', $array_data);
       
      

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
        
        $destinationPath = storage_path()."/docs/company".$CYID_REF."/PurchaseAccountSet";

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
            return redirect()->route("master",[167,"attachment",$ATTACH_DOCNO])->with("success","The file is already exist");
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

            return redirect()->route("master",[167,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[167,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("master",[167,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
            return redirect()->route("master",[167,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   

  


    //singleApprove begin
    public function singleapprove(Request $request)
    {
        
        $rules = [
            'AC_SET_CODE' => 'required|unique:TBL_MST_PURCHASE_AC_SET',
            'AC_SET_CODE' => 'required',          
            'AC_SET_DESC' => 'required',          
        ];
        
        $req_data = [
        
            'AC_SET_CODE'  => strtoupper(trim($request['AC_SET_CODE']) ),
            'AC_SET_CODE' =>   $request['AC_SET_DESC'],
            'AC_SET_DESC' =>   $request['AC_SET_DESC'],
           
        ]; 
        
        
        $validator = Validator::make( $req_data, $rules, $this->messages);
        
        if ($validator->fails())
        {
            return Response::json(['errors' => $validator->errors()]);	
        }
        
        
        
        $AC_SET_CODE   =   strtoupper(trim($request['AC_SET_CODE']) );
        $AC_SET_DESC   =   trim($request['AC_SET_DESC']);  
        
        $PURCHASE_AC   =   !empty(trim($request['LISTPOP1ID_0'])) ? trim($request['LISTPOP1ID_0']) :null;  
        $PAYABLE_CLEARING   =  !empty(trim($request['LISTPOP1ID_1'])) ? trim($request['LISTPOP1ID_1']) :null;  
        $INVENTORY_AC   =  !empty(trim($request['LISTPOP1ID_2'])) ? trim($request['LISTPOP1ID_2']) :null;  
        
        $ADJUSTMENT_WRITEOFF   =  !empty(trim($request['LISTPOP1ID_3'])) ? trim($request['LISTPOP1ID_3']) :null;  
        $TRANSFER_CLEARING   =   !empty(trim($request['LISTPOP1ID_4'])) ? trim($request['LISTPOP1ID_4']) :null;    
        $STOCK_TRANSFER_AC   =    !empty(trim($request['LISTPOP1ID_5'])) ? trim($request['LISTPOP1ID_5']) :null;    
        
        $RM_CONSUMPTION   =    !empty(trim($request['LISTPOP1ID_6'])) ? trim($request['LISTPOP1ID_6']) :null; 
        $REJECTED   =   !empty(trim($request['LISTPOP1ID_7'])) ? trim($request['LISTPOP1ID_7']) :null;  
        $SHORTAGE   =    !empty(trim($request['LISTPOP1ID_8'])) ? trim($request['LISTPOP1ID_8']) :null;  
        
        $PHY_INVENTORY_ADJ   =    !empty(trim($request['LISTPOP1ID_9'])) ? trim($request['LISTPOP1ID_9']) :null;  
        $WIP_AC   =    !empty(trim($request['LISTPOP1ID_10'])) ? trim($request['LISTPOP1ID_10']) :null;   
        $GAIN_LOSS_AC   =    !empty(trim($request['LISTPOP1ID_11'])) ? trim($request['LISTPOP1ID_11']) :null;   
        
        $FA_AC   =    !empty(trim($request['LISTPOP1ID_12'])) ? trim($request['LISTPOP1ID_12']) :null;   
        $FA_CLEANING_AC   =    !empty(trim($request['LISTPOP1ID_13'])) ? trim($request['LISTPOP1ID_13']) :null;  
        $DEPR_AC   =    !empty(trim($request['LISTPOP1ID_14'])) ? trim($request['LISTPOP1ID_14']) :null;    
        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
        $DEPR_AC   =    !empty(trim($request['LISTPOP1ID_14'])) ? trim($request['LISTPOP1ID_14']) :null;    
        $PRTN_AC   =    !empty(trim($request['LISTPOP1ID_15'])) ? trim($request['LISTPOP1ID_15']) :null;    
        $IPOR_AC   =    !empty(trim($request['LISTPOP1ID_16'])) ? trim($request['LISTPOP1ID_16']) :null;    
        $JWI_AC   =    !empty(trim($request['LISTPOP1ID_17'])) ? trim($request['LISTPOP1ID_17']) :null;    
        $JWR_AC   =    !empty(trim($request['LISTPOP1ID_18'])) ? trim($request['LISTPOP1ID_18']) :null; 
        $CUSTOM_DUTY_AC   =    !empty(trim($request['LISTPOP1ID_19'])) ? trim($request['LISTPOP1ID_19']) :null; 
		$PURCHASEIS_AC   =    !empty(trim($request['LISTPOP1ID_20'])) ? trim($request['LISTPOP1ID_20']) :null;
		
        $newDateString = NULL;

        $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt) ){
            
            $newdt = str_replace( "/", "-",  $newdt ) ;

            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        

        $DODEACTIVATED = $newDateString;
        

         $CYID_REF   =   Auth::user()->CYID_REF;
         $BRID_REF   =   Session::get('BRID_REF');
 
         $FYID_REF   =   Session::get('FYID_REF');       
         $VTID       =   $this->vtid_ref;
         $USERID     =   Auth::user()->USERID;
         $UPDATE     =   Date('Y-m-d');
         
         $UPTIME     =   Date('h:i:s.u');
         $ACTION     =   trim($request['user_approval_level']);   // user approval level value
         $IPADDRESS  =   $request->getClientIp();
         
         $array_data   = [
            $AC_SET_CODE,
            $AC_SET_DESC,
            $PURCHASE_AC,
            $PAYABLE_CLEARING,
            
            $INVENTORY_AC,
            $ADJUSTMENT_WRITEOFF,
            $TRANSFER_CLEARING,
            $STOCK_TRANSFER_AC,
    
            $RM_CONSUMPTION,
            $REJECTED,
            $SHORTAGE,
            $PHY_INVENTORY_ADJ,

            $WIP_AC,
            $GAIN_LOSS_AC,
            $FA_AC,
            $DEACTIVATED,
            
            $DODEACTIVATED, 
            $FA_CLEANING_AC,
            $DEPR_AC,
            $CYID_REF,
            
            $BRID_REF,
            $FYID_REF, 
            $VTID, 
            $USERID,
            
            $UPDATE,
            $UPTIME,
            $ACTION,
            $IPADDRESS ,
            $PRTN_AC, 
            $IPOR_AC,
            $JWI_AC,
            $JWR_AC,$CUSTOM_DUTY_AC,$PURCHASEIS_AC                       
        ];

        try {

             $sp_result = DB::select('EXEC SP_PURCHASE_AC_SET_UP  ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?,?,?', $array_data);
       
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

            $objResponse = DB::table('TBL_MST_PURCHASE_AC_SET')
                            ->where('PR_AC_SETID','=',$id)
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

           
         
           $objPURCHASE_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->PURCHASE_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();

           $objPAYABLE_CLEARING = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->PAYABLE_CLEARING)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           
           $objINVENTORY_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->INVENTORY_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           
           
           $objADJUSTMENT_WRITEOFF = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->ADJUSTMENT_WRITEOFF)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           
           $objTRANSFER_CLEARING = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->TRANSFER_CLEARING)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           
           $objSTOCK_TRANSFER_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->STOCK_TRANSFER_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           
           $objRM_CONSUMPTION = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->RM_CONSUMPTION)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           
           $objREJECTED = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->REJECTED)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
            
            
           $objSHORTAGE = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->SHORTAGE)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
         
           $objPHY_INVENTORY_ADJ = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->PHY_INVENTORY_ADJ)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
         
           $objWIP_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->WIP_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
         
           $objGAIN_LOSS_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->GAIN_LOSS_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
         
           $objFA_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->FA_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
         
           $objFA_CLEANING_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->FA_CLEANING_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
         
           $objDEPR_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->DEPR_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
         
           $objPURCHASE_RETURN_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->PURCHASE_RETURN_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();

           $objIMPORT_PURCHASE_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->IMPORT_PURCHASE_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();

           $objJWI = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->JWI)
           ->select('GLID','GLCODE','GLNAME')
           ->first();

           $objJWR = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->JWR)
           ->select('GLID','GLCODE','GLNAME')
           ->first();

           $objCUSTOM = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->CUSTOM_DUTY_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
		   
		   $objPURCHASEIS_AC = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('GLID','=',$objResponse->PURCHASEIS_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
         
            return view('masters.Accounts.PurchaseAccountSet.mstfrm167view',compact(['objResponse','user_approval_level','objRights',
            'objPURCHASE_AC','objPAYABLE_CLEARING','objINVENTORY_AC','objADJUSTMENT_WRITEOFF','objTRANSFER_CLEARING','objSTOCK_TRANSFER_AC',
            'objRM_CONSUMPTION','objREJECTED','objSHORTAGE','objPHY_INVENTORY_ADJ','objWIP_AC','objGAIN_LOSS_AC','objFA_AC',
            'objFA_CLEANING_AC','objDEPR_AC','objPURCHASE_RETURN_AC','objIMPORT_PURCHASE_AC','objJWI','objJWR','objCUSTOM','objPURCHASEIS_AC']));
        }

    }
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }
        $objResponse = TblMstFrm167::whereIn('PR_AC_SETID',$ids_data)->get();
        
        return view('masters.Accounts.PurchaseAccountSet.mstfrm167print',compact(['objResponse']));
   }//print




        //display attachments form
        public function attachment($id){

            if(!is_null($id))
            {
                //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
                
                $objResponse = DB::table('TBL_MST_PURCHASE_AC_SET')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('PR_AC_SETID','=',$id)
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
    
                     
    
                return view('masters.Accounts.PurchaseAccountSet.mstfrm167attachment',compact(['objResponse','objMstVoucherType','objAttachments']));
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
            $TABLE      =   "TBL_MST_PURCHASE_AC_SET";
            $FIELD      =   "PR_AC_SETID";
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
        $TABLE      =   "TBL_MST_PURCHASE_AC_SET";
        $FIELD      =   "PR_AC_SETID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $cancelData[0]= ['NT' =>'TBL_MST_PURCHASE_AC_SET'];
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

    public function getgl(Request $request){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        
        // $ObjData = DB::select('SELECT * FROM TBL_MST_VOUCHERTYPE  where (DEACTIVATED=0 or DEACTIVATED is null)  and STATUS = ? ',
        //                 ['A']);
                       
        $cur_date = Date('Y-m-d');
        
        $ObjData = DB::select('select  * from TBL_MST_GENERALLEDGER  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?)  and CYID_REF = ? and STATUS = ? order by GLCODE', [$cur_date, $CYID_REF,  $Status]);
                         

            if(!empty($ObjData)){

            foreach ($ObjData as $index=>$dataRow){
               

                // $row = '';
                // $row = $row.'<tr id="LISTPOP1code_'.$dataRow->GLID .'"  class="clsLISTPOP1id"><td width="50%">'.$dataRow->GLCODE;
                // $row = $row.'<input type="hidden" id="txtLISTPOP1code_'.$dataRow->GLID.'" data-desc="'.$dataRow->GLCODE.'"  data-descdate="'.$dataRow->GLNAME.'"
                // value="'.$dataRow->GLID.'"/></td><td>'.$dataRow->GLNAME.'</td></tr>';
                // echo $row;

                $row = '';
                $row = $row.'<tr >
                <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_GLID_REF[]" id="LISTPOP1code_'.$dataRow->GLID .'"  class="clsLISTPOP1id" value="'.$dataRow->GLID.'" ></td>
                <td width="39%" class="ROW2">'.$dataRow->GLCODE;
                $row = $row.'<input type="hidden" id="txtLISTPOP1code_'.$dataRow->GLID.'" data-desc="'.$dataRow->GLCODE.'"  data-descdate="'.$dataRow->GLNAME.'"
                value="'.$dataRow->GLID.'"/></td><td width="39%" class="ROW3">'.$dataRow->GLNAME.'</td></tr>';
                echo $row;

            }
            }else{
                echo '<tr><td colspan="3">Record not found.</td></tr>';
            }
            exit();
    }


}
