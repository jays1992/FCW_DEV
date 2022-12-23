<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm76;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;
use App\Helpers\Helper;


class MstFrm76Controller extends Controller
{
   
    protected $form_id = 76;
    protected $vtid_ref   = 76;  //voucher type id

    //validation messages
    protected   $messages = [
                    'STCODE.required' => 'Required field',
                    'STCODE.unique' => 'Duplicate Code',
                    'NAME.required' => 'Required field'
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
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
		->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
		->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first(); 

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');  

        $objDataList=DB::select("SELECT * FROM TBL_MST_STORE WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' ORDER BY STID DESC");

        return view('masters.inventory.StoreMaster.mstfrm76',compact(['objRights','objDataList']));

    }
    
    public function add(){ 
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $objDD = DB::table('TBL_MST_DOCNO_DEFINITION')
                ->where('VTID_REF','=',$this->vtid_ref)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('BRID_REF','=',$BRID_REF)
                ->where('STATUS','=','A')
                ->select('TBL_MST_DOCNO_DEFINITION.*')
                ->first();


                $objCountryList = DB::table('TBL_MST_COUNTRY')
                ->where('STATUS','=','A')
                ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                ->select('CTRYID','CTRYCODE','NAME')
                ->get();




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

      
        return view('masters.inventory.StoreMaster.mstfrm76add',compact([
               'objDD','objDOCNO',
               'objCountryList'
    
    
    
    ]));
    }

/******************************** START Country State City ********************************************************* */
    
  public function getCountryWiseState(Request $request){
    
    $objStateList = DB::table('TBL_MST_STATE')
    ->where('STATUS','=','A')
    ->where('CTRYID_REF','=',$request['CTRYID_REF'])
    ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
    ->select('STID','NAME','STCODE')
    ->get();

    if(!empty($objStateList)){
        foreach($objStateList as $state){

            echo '<tr >
            <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_CORSTID_REF[]" id="cor_stidref_'.$state->STID.'" class="cls_cor_stidref" value="'.$state->STID.'" ></td>
            <td width="39%" class="ROW2">'.$state->STCODE.'
            <input type="hidden" id="txtcor_stidref_'.$state->STID.'" data-desc="'.$state->STCODE.'-'.$state->NAME.'" value="'.$state->STID.'" />
            </td>
            <td width="39%" class="ROW3">'.$state->NAME.'</td>
            </tr>';
        }
    }
    else{
        echo '<tr><td colspan="2">Record not found.</td></tr>';
    }
    exit();
}

public function getStateWiseCity(Request $request){
    
    $objCityList = DB::table('TBL_MST_CITY')
    ->where('STATUS','=','A')
    ->where('CTRYID_REF','=',$request['CTRYID_REF'])
    ->where('STID_REF','=',$request['STID_REF'])
    ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
    ->select('CITYID','CITYCODE','NAME')
    ->get();
    
    if(!empty($objCityList)){
        foreach($objCityList as $city){
        
            echo '<tr>
            <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_CORCITYID_REF[]"  id="cor_cityidref_'.$city->CITYID.'" class="cls_cor_cityidref" value="'.$city->CITYID.'" ></td>
            <td width="39%" class="ROW2">'.$city->CITYCODE.'
            <input type="hidden" id="txtcor_cityidref_'.$city->CITYID.'" data-desc="'.$city->CITYCODE.'-'.$city->NAME.'" value="'.$city->CITYID.'" />
            </td>
            <td width="39%" class="ROW3">'.$city->NAME.'</td>
            </tr>';
        }
    }
    else{
        echo '<tr><td colspan="2">Record not found.</td></tr>';
    }
}

/******************************** END Country State City ********************************************************* */




   public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $STCODE =   $request['STCODE'];
        
        $objLabel = DB::table('TBL_MST_STORE')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('STCODE','=',$STCODE)
        ->select('STCODE')
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
            'STCODE' => 'required',
            'NAME' => 'required',          
        ];

        $req_data = [

            'STCODE'     =>   $request['STCODE'],
            'NAME' =>   $request['NAME']
        ]; 


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }

        $data     =array();
        $existData=array();
        $r_count = $request['Row_Count'];

        for ($i=0; $i<=$r_count; $i++){

            if((isset($request['RACKNO_'.$i]) && $request['RACKNO_'.$i] !="")){
                $data[$i] = [
                    'RACKNO' => strtoupper(trim($request['RACKNO_'.$i])),
                    'DESCRIPTIONS' => trim($request['DESCRIPTIONS_'.$i]),
                    'BINNO' => trim($request['BINNO_'.$i]),
                ];

                $existData[$i]=strtoupper(trim($request['RACKNO_'.$i])).trim($request['BINNO_'.$i]);
            }
        }

        if(!empty($existData)){
            $counts     = array_count_values($existData);
            $NumVal     = max($counts);

            if( $NumVal > 1){
                return Response::json(['errors'=>true,'msg' => 'Error:Duplicate data','save'=>'invalid']);
            }
        }

        if(!empty($data)){ 
            $wrapped_links["RACK"] = $data; 
            $xml = ArrayToXml::convert($wrapped_links);
        }
        else{
            $xml = NULL; 
        }  


        $STCODE      =   strtoupper(trim($request['STCODE']) );
        $NAME      =   trim($request['NAME']);
        $Address1      =   trim($request['Address1']);
        $Address2      =   trim($request['Address2']);
        $CTRYID_REF      =   trim($request['CTRYID_REF']);
        $STID_REF      =   trim($request['STID_REF']);
        $CITYID_REF      =   trim($request['CITYID_REF']);
        $PINCODE      =   trim($request['PINCODE']);

        $DEACTIVATED    =   0;  
        $DODEACTIVATED  =   NULL;  

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');       
        $VTID           =   $this->vtid_ref;
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();
        
        $array_data   = [
                        $STCODE, $NAME, 
                        $DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF,$FYID_REF, 
                        $xml,$VTID, $USERID, $UPDATE,
                        $UPTIME, $ACTION, $IPADDRESS ,$Address1, $Address2, $CTRYID_REF, $STID_REF, $CITYID_REF, $PINCODE
                    ];
        
        try {

            $sp_result = DB::select('EXEC SP_STORE_IN ?,?,?,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?, ?,?,?', $array_data);
        

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

            $objResponse = TblMstFrm76::where('STID','=',$id)->first();
           /*  if(strtoupper($objResponse->STATUS)=="A" || strtoupper($objResponse->STATUS)=="C"){
                exit("Sorry, Only Un Approved record can edit.");
            }
 */
            $objRights = DB::table('TBL_MST_USERROLMAP')
			->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
			->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
			->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
			->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
			->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
			->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
			->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
			->first();
            
            $objMstCust = DB::table('TBL_MST_STORE')                    
            ->where('TBL_MST_STORE.STID','=',$id)
            //->where('TBL_MST_CUSTOMER.DEACTIVATED','<>',1)
            ->where('TBL_MST_STORE.CYID_REF','=',$CYID_REF)
            ->where('TBL_MST_STORE.BRID_REF','=',$BRID_REF)
            ->select('TBL_MST_STORE.*')
            ->first();

            
            
            $objRegCountry= DB::table('TBL_MST_COUNTRY')
                ->where('CTRYID','=',$objMstCust->CTRYID_REF)
                ->select('CTRYID','CTRYCODE','NAME')
                ->first();

            $objRegState = DB::table('TBL_MST_STATE')
            ->where('STID','=',$objMstCust->STID_REF)
            ->select('STID','STCODE','NAME')
            ->first();

            $objRegCity = DB::table('TBL_MST_CITY')
            ->where('CITYID','=',$objMstCust->CITYID_REF)
            ->select('CITYID','CITYCODE','NAME')
            ->first();

            $objCountryList = DB::table('TBL_MST_COUNTRY')
                ->where('STATUS','=','A')
                ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                ->select('CTRYID','CTRYCODE','NAME')
                ->get();



            $objDataResponse = DB::table('TBL_MST_STORERACK')                    
                             ->where('TBL_MST_STORERACK.STID_REF','=',$id)
                             ->select('TBL_MST_STORERACK.*')
                             ->orderBy('TBL_MST_STORERACK.RACKID','ASC')
                             ->get()->toArray();
            $objCount = count($objDataResponse);

            
            return view('masters.inventory.StoreMaster.mstfrm76edit',compact(['objResponse','user_approval_level','objRights', 'objMstCust', 'objRegCountry', 'objRegState', 'objRegCity', 'objCountryList','objDataResponse','objCount']));
        }

    }

     
    public function update(Request $request)
    {

      // dd($request->all());

        $update_rules = [

            'NAME' => 'required'       
        ];

        $req_update_data = [

            'NAME' =>   $request['NAME']
        ]; 

       
        $validator = Validator::make( $req_update_data, $update_rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }


        $data     =array();
        $existData=array();
        $r_count = $request['Row_Count'];

        for ($i=0; $i<=$r_count; $i++){

            if((isset($request['RACKNO_'.$i]) && $request['RACKNO_'.$i] !="")){
                $data[$i] = [
                    'RACKNO' => strtoupper(trim($request['RACKNO_'.$i])),
                    'DESCRIPTIONS' => trim($request['DESCRIPTIONS_'.$i]),
                    'BINNO' => trim($request['BINNO_'.$i]),
                    'RACKID' =>  trim($request['RACKID_'.$i]),
                ];

                $existData[$i]=strtoupper(trim($request['RACKNO_'.$i])).trim($request['BINNO_'.$i]);
            }
        }

        if(!empty($existData)){
            $counts     = array_count_values($existData);
            $NumVal     = max($counts);

            if( $NumVal > 1){
                return Response::json(['errors'=>true,'msg' => 'Error:Duplicate data','save'=>'invalid']);
            }
        }

        if(!empty($data)){ 
            $wrapped_links["RACK"] = $data; 
            $xml = ArrayToXml::convert($wrapped_links);
        }
        else{
            $xml = NULL; 
        } 


        $STCODE       =   strtoupper(trim($request['STCODE']) );
        $NAME   =   trim($request['NAME']); 
        $Address1      =   trim($request['Address1']);
        $Address2      =   trim($request['Address2']);
        $CTRYID_REF      =   trim($request['CTRYID_REF']);
        $STID_REF      =   trim($request['STID_REF']);
        $CITYID_REF      =   trim($request['CITYID_REF']);
        $PINCODE      =   trim($request['PINCODE']);

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
            $STCODE, $NAME,
            $DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF,$FYID_REF, 
            $xml,$VTID, $USERID, $UPDATE,
            $UPTIME, $ACTION, $IPADDRESS,$Address1, $Address2, $CTRYID_REF, $STID_REF, $CITYID_REF, $PINCODE
        ];

       // dd($array_data);

        try {

        $sp_result = DB::select('EXEC SP_STORE_UP ?,?, ?,?,?,?,?, ?,?,?,?, ?,?,?,?,?,?,?,?,?', $array_data);

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
        
		
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/StoreMaster";

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
            return redirect()->route("master",[76,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
        }
     //  dd($uploaded_data);

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
        
        //  echo "<pre>";
        // // print_r($uploaded_data);
        // dump($attachment_data);
        
        // echo "</pre>";

       


        // echo "<pre>";
        // print_r($attachment_data);
        // dump($ATTACHMENTS_XMl);
        
        // echo "</pre>";
          
       // try {

             //save data
             $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);

           //  dd($sp_result[0]->RESULT);
      
      //  } catch (\Throwable $th) {
        
        //    return redirect()->route("master",[76,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");
    
      //  }
     
        if($sp_result[0]->RESULT=="SUCCESS"){

            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }

            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }

            return redirect()->route("master",[76,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[76,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("master",[76,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
            return redirect()->route("master",[76,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   

  


   



    //singleApprove begin
    public function singleapprove(Request $request)
    {
      
         $approv_rules = [
 
             'NAME' => 'required',          
         ];
 
         $req_approv_data = [
 
             'NAME' =>   $request['NAME']
         ]; 
 
 
         $validator = Validator::make( $req_approv_data, $approv_rules, $this->messages);
 
         if ($validator->fails())
         {
         return Response::json(['errors' => $validator->errors()]);	
         }


         $data     =array();
        $existData=array();
        $r_count = $request['Row_Count'];

        for ($i=0; $i<=$r_count; $i++){

            if((isset($request['RACKNO_'.$i]) && $request['RACKNO_'.$i] !="")){
                $data[$i] = [
                    'RACKNO' => strtoupper(trim($request['RACKNO_'.$i])),
                    'DESCRIPTIONS' => trim($request['DESCRIPTIONS_'.$i]),
                    'BINNO' => trim($request['BINNO_'.$i]),
                    'RACKID' =>  trim($request['RACKID_'.$i]),
                ];

                $existData[$i]=strtoupper(trim($request['RACKNO_'.$i])).trim($request['BINNO_'.$i]);
            }
        }

        if(!empty($existData)){
            $counts     = array_count_values($existData);
            $NumVal     = max($counts);

            if( $NumVal > 1){
                return Response::json(['errors'=>true,'msg' => 'Error:Duplicate data','save'=>'invalid']);
            }
        }

        if(!empty($data)){ 
            $wrapped_links["RACK"] = $data; 
            $xml = ArrayToXml::convert($wrapped_links);
        }
        else{
            $xml = NULL; 
        } 
     
        $STCODE       =   strtoupper(trim($request['STCODE']) );
        $NAME   =   trim($request['NAME']);
        $Address1      =   trim($request['Address1']);
        $Address2      =   trim($request['Address2']);
        $CTRYID_REF      =   trim($request['CTRYID_REF']);
        $STID_REF      =   trim($request['STID_REF']);
        $CITYID_REF      =   trim($request['CITYID_REF']);
        $PINCODE      =   trim($request['PINCODE']); 

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
         $VTID       =   $this->vtid_ref;
         $USERID     =   Auth::user()->USERID;
         $UPDATE     =   Date('Y-m-d');
         
         $UPTIME     =   Date('h:i:s.u');
         $ACTION     =   trim($request['user_approval_level']);   // user approval level value
         $IPADDRESS  =   $request->getClientIp();
         
       
            $array_data   = [
                $STCODE, $NAME,
                $DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF,$FYID_REF, 
                $xml,$VTID, $USERID, $UPDATE,
                $UPTIME, $ACTION, $IPADDRESS,$Address1, $Address2, $CTRYID_REF, $STID_REF, $CITYID_REF, $PINCODE
             ];


        try {

        $sp_result = DB::select('EXEC SP_STORE_UP ?,?, ?,?,?,?,?, ?,?,?,?, ?,?,?,?,?,?,?,?,?', $array_data);

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

            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');


            $objResponse = TblMstFrm76::where('STID','=',$id)->first();

            $objDataResponse = DB::table('TBL_MST_STORERACK')                    
                             ->where('TBL_MST_STORERACK.STID_REF','=',$id)
                             ->select('TBL_MST_STORERACK.*')
                             ->orderBy('TBL_MST_STORERACK.RACKID','ASC')
                             ->get()->toArray();
            $objCount = count($objDataResponse);

            $objMstCust = DB::table('TBL_MST_STORE')                    
            ->where('TBL_MST_STORE.STID','=',$id)
            //->where('TBL_MST_CUSTOMER.DEACTIVATED','<>',1)
            ->where('TBL_MST_STORE.CYID_REF','=',$CYID_REF)
            ->where('TBL_MST_STORE.BRID_REF','=',$BRID_REF)
            ->select('TBL_MST_STORE.*')
            ->first();

            $objRegCountry= DB::table('TBL_MST_COUNTRY')
            ->where('CTRYID','=',$objMstCust->CTRYID_REF)
            ->select('CTRYID','CTRYCODE','NAME')
            ->first();

            $objRegState = DB::table('TBL_MST_STATE')
            ->where('STID','=',$objMstCust->STID_REF)
            ->select('STID','STCODE','NAME')
            ->first();

            $objRegCity = DB::table('TBL_MST_CITY')
            ->where('CITYID','=',$objMstCust->CITYID_REF)
            ->select('CITYID','CITYCODE','NAME')
            ->first();

            return view('masters.inventory.StoreMaster.mstfrm76view',compact(['objResponse','objDataResponse','objMstCust','objRegCountry','objRegState','objRegCity','objCount']));
        }

    }
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }

        $objResponse = TblMstFrm76::whereIn('STID',$ids_data)->get();
        
        return view('masters.inventory.StoreMaster.mstfrm76print',compact(['objResponse']));
   }//print


   
    

    
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objResponse = TblMstFrm76::where('STID','=',$id)->first();

            //select * from TBL_MST_VOUCHERTYPE where VTID=114

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
                        ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                        ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                        ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                        ->get()->toArray();

                 // dump( $objAttachments);

            return view('masters.inventory.StoreMaster.mstfrm76attachment',compact(['objResponse','objMstVoucherType','objAttachments']));
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

            // dd($req_data);
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
            $TABLE      =   "TBL_MST_STORE";
            $FIELD      =   "STID";
            $ACTIONNAME     = $Approvallevel;
            $UPDATE     =   Date('Y-m-d');
            $UPTIME     =   Date('h:i:s.u');
            $IPADDRESS  =   $request->getClientIp();
        
        
        
        // dd($xml);
        
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

          

   //save data

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_STORE";
        $FIELD      =   "STID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $cancelData[0]= ['NT' =>'TBL_MST_STORERACK'];
        $cancel_links["TABLES"] = $cancelData;
        $cancelxml = ArrayToXml::convert($cancel_links);

        $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$cancelxml];

        $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);


        //dump($sp_result);
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


}
