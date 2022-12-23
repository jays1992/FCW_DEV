<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm147;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Helpers\Utils;


class MstFrm147Controller extends Controller
{
   
    protected $form_id = 147;
    protected $vtid_ref   = 105;  //voucher type id

    //validation messages
    protected   $messages = [
                    'BRCODE.required' => 'Required field',
                    'BRCODE.unique' => 'Duplicate Code',
                    'BRNAME.required' => 'Required field',
                    'CYID_REF.required' => 'Required field',
                    'BGID_REF.required' => 'Required field',
                    'ADDL1.required' => 'Required field',
                    'CTRYID_REF.required' => 'Required field',
                    'STID_REF.required' => 'Required field',
                    'CITYID_REF.required' => 'Required field',
                    'GSTTYPE.required' => 'Required field'
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
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first(); 

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');  

        $objDataList=DB::select("SELECT * FROM TBL_MST_BRANCH WHERE CYID_REF='$CYID_REF'");

        return view('masters.Common.BranchMaster.mstfrm147',compact(['objRights','objDataList']));

    }

    
    public function add(){
        
        $objCountryList = DB::table('TBL_MST_COUNTRY')
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('CTRYID','CTRYCODE','NAME')
        ->get();

        $objIndTypeList = DB::table('TBL_MST_INDUSTRYTYPE')
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('INDSID','INDSCODE','DESCRIPTIONS')
        ->get();

        $objIndVerList = DB::table('TBL_MST_INDUSTRYVERTICAL')
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('INDSVID','INDSVCODE','DESCRIPTIONS')
        ->get();

        $objGstTypeList = DB::table('TBL_MST_GSTTYPE')
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('GSTID','GSTCODE','DESCRIPTIONS')
        ->get();

        $objCurrencyList = DB::table('TBL_MST_CURRENCY')
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('CRID','CRCODE','CRDESCRIPTION')
        ->get();
        
        $objUdfListing  =   $this->getUdfListing();
        $objudfCount    =   count($objUdfListing);                
		if($objudfCount==0){
			$objudfCount=1;
		}
        
        $objCompanyList     =   $this->getCompany();
        $objBranchGroupList =   $this->getBranchGroup();
        
        return view('masters.Common.BranchMaster.mstfrm147add',compact(['objCountryList','objIndTypeList','objIndVerList','objGstTypeList','objCurrencyList','objUdfListing','objudfCount','objCompanyList','objBranchGroupList']));
    }

    public function getCompany(){

        return $objCompanyList = DB::table('TBL_MST_COMPANY')
            ->where('CYID','=',Auth::user()->CYID_REF)
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('CYID','CYCODE','NAME')
            ->get();
    }

    public function getBranchGroup(){

        return $objCompanyList = DB::table('TBL_MST_BRANCH_GROUP')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('BGID','BG_CODE','BG_DESC')
            ->get();
    }


    public function getUdfListing(){ 
  
        $cyidRef =  Auth::user()->CYID_REF;
        $bridRef = Session::get('BRID_REF');
        $fyidRef = Session::get('FYID_REF');

		$ObjUnionUDF = DB::table("TBL_MST_UDFFOR_BRANCH")->select('*')
                    ->whereIn('PARENTID',function($query) use ($cyidRef)
                                {       
                                $query->select('UDFBRID')->from('TBL_MST_UDFFOR_BRANCH')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$cyidRef);
                                                                     
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$cyidRef);
                                  
                   

        $objUdfData = DB::table('TBL_MST_UDFFOR_BRANCH')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$cyidRef)
           
            ->union($ObjUnionUDF)
            ->get(); 
            
		return $objUdfData;
    }
    

    public function getCountryWiseState(Request $request){

        $objStateList = DB::table('TBL_MST_STATE')
        ->where('STATUS','=','A')
        ->where('CTRYID_REF','=',$request['CTRYID_REF'])
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('STID','NAME','STCODE')
        ->get();
    
        if(!empty($objStateList)){
            foreach($objStateList as $state){
            
                echo '<tr>
                <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_STID_REF[]" id="stidref_'.$state->STID.'" class="cls_stidref" value="'.$state->STID.'" ></td>
                <td width="39%" class="ROW2">'.$state->STCODE.'
                <input type="hidden" id="txtstidref_'.$state->STID.'" data-desc="'.$state->STCODE."-".$state->NAME.'" data-descname="'.$state->NAME.'" value="'.$state->STID.'" />
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
            
                echo '<tr >
                <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_CITYID_REF[]" id="cityidref_'.$city->CITYID.'" class="cls_cityidref" value="'.$city->CITYID.'" ></td>
                <td width="39%" class="ROW2">'.$city->CITYCODE.'
                <input type="hidden" id="txtcityidref_'.$city->CITYID.'" data-desc="'.$city->CITYCODE.'-'.$city->NAME.'" value="'.$city->CITYID.'" />
                </td>
                <td width="39%" class="ROW3">'.$city->NAME.'</td>
                </tr>';

            }
        }
        else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
    }

    public function getCorCountryWiseState(Request $request){
        
        $objStateList = DB::table('TBL_MST_STATE')
        ->where('STATUS','=','A')
        ->where('CTRYID_REF','=',$request['CTRYID_REF'])
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('STID','NAME','STCODE')
        ->get();
    
        if(!empty($objStateList)){
            foreach($objStateList as $state){
            
                echo '<tr id="cor_stidref_'.$state->STID.'" class="cls_cor_stidref">
                <td width="50%">'.$state->STCODE.'
                <input type="hidden" id="txtcor_stidref_'.$state->STID.'" data-desc="'.$state->STCODE.'-'.$state->NAME.'" value="'.$state->STID.'" />
                </td>
                <td>'.$state->NAME.'</td>
                </tr>';
            }
        }
        else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();
    }

    public function getCorStateWiseCity(Request $request){
        
        $objCityList = DB::table('TBL_MST_CITY')
        ->where('STATUS','=','A')
        ->where('CTRYID_REF','=',$request['CTRYID_REF'])
        ->where('STID_REF','=',$request['STID_REF'])
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('CITYID','CITYCODE','NAME')
        ->get();
        
        if(!empty($objCityList)){
            foreach($objCityList as $city){
            
                echo '<tr id="cor_cityidref_'.$city->CITYID.'" class="cls_cor_cityidref">
                <td width="50%">'.$city->CITYCODE.'
                <input type="hidden" id="txtcor_cityidref_'.$city->CITYID.'" data-desc="'.$city->CITYCODE.'-'.$city->NAME.'" value="'.$city->CITYID.'" />
                </td>
                <td>'.$city->NAME.'</td>
                </tr>';
            }
        }
        else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
    }

   public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $BRCODE =   $request['BRCODE'];
        
        $objLabel = DB::table('TBL_MST_BRANCH')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRCODE','=',$BRCODE)
        ->select('BRCODE')
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
            'CYID_REF' => 'required', 
            'BRCODE' => 'required|unique:TBL_MST_BRANCH',
            'BRNAME' => 'required', 
            'BGID_REF' => 'required', 
            'ADDL1' => 'required', 
            'CTRYID_REF' => 'required', 
            'STID_REF' => 'required', 
            'CITYID_REF' => 'required', 
            'GSTTYPE' => 'required'         
        ];

        $req_data = [
            'CYID_REF'     =>    $request['CYID_REF'],
            'BRCODE'     =>    $request['BRCODE'],
            'BRNAME' =>   $request['BRNAME'],
            'BGID_REF' =>   $request['BGID_REF'],
            'ADDL1' =>   $request['ADDL1'],
            'CTRYID_REF' =>   $request['CTRYID_REF'],
            'STID_REF' =>   $request['STID_REF'],
            'CITYID_REF' =>   $request['CITYID_REF'],
            'GSTTYPE' =>   $request['GSTTYPE']
        ]; 

        
        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }

        $CYID_REF               =   trim($request['CYID_REF']); 
        $BRCODE             =   strtoupper(trim($request['BRCODE']) );
        $BRNAME               =   trim($request['BRNAME']); 
        $SAP_CODE               =   trim($request['SAP_CODE']); 
        $ALPS_REFNO               =   trim($request['ALPS_REFNO']); 
        
        $BGID_REF             =   (isset($request['BGID_REF']) && trim($request['BGID_REF']) !="" )? trim($request['BGID_REF']) : NULL ;
        $GSTINNO             =   (isset($request['GSTINNO']) && trim($request['GSTINNO']) !="" )? trim($request['GSTINNO']) : NULL ;
        $CINNO             =   (isset($request['CINNO']) && trim($request['CINNO']) !="" )? trim($request['CINNO']) : NULL ;
        $ADDL1             =   (isset($request['ADDL1']) && trim($request['ADDL1']) !="" )? trim($request['ADDL1']) : NULL ;
        $ADDL2             =   (isset($request['ADDL2']) && trim($request['ADDL2']) !="" )? trim($request['ADDL2']) : NULL ;
        $CTRYID_REF             =   (isset($request['CTRYID_REF']) && trim($request['CTRYID_REF']) !="" )? trim($request['CTRYID_REF']) : NULL ;
        $STID_REF             =   (isset($request['STID_REF']) && trim($request['STID_REF']) !="" )? trim($request['STID_REF']) : NULL ;
        $CITYID_REF             =   (isset($request['CITYID_REF']) && trim($request['CITYID_REF']) !="" )? trim($request['CITYID_REF']) : NULL ;
        $PINCODE             =   (isset($request['PINCODE']) && trim($request['PINCODE']) !="" )? trim($request['PINCODE']) : NULL ;
        $BRLM             =   (isset($request['BRLM']) && trim($request['BRLM']) !="" )? trim($request['BRLM']) : NULL ;
        $EMAILID             =   (isset($request['EMAILID']) && trim($request['EMAILID']) !="" )? trim($request['EMAILID']) : NULL ;
        $PHNO             =   (isset($request['PHNO']) && trim($request['PHNO']) !="" )? trim($request['PHNO']) : NULL ;
        $MONO             =   (isset($request['MONO']) && trim($request['MONO']) !="" )? trim($request['MONO']) : NULL ;
        $WEBSITE             =   (isset($request['WEBSITE']) && trim($request['WEBSITE']) !="" )? trim($request['WEBSITE']) : NULL ;
        $SKYPEID             =   (isset($request['SKYPEID']) && trim($request['SKYPEID']) !="" )? trim($request['SKYPEID']) : NULL ;
        $AUTHPNAME             =   (isset($request['AUTHPNAME']) && trim($request['AUTHPNAME']) !="" )? trim($request['AUTHPNAME']) : NULL ;
        $AUTHPDESG             =   (isset($request['AUTHPDESG']) && trim($request['AUTHPDESG']) !="" )? trim($request['AUTHPDESG']) : NULL ;
        $INDSID_REF             =   (isset($request['INDSID_REF']) && trim($request['INDSID_REF']) !="" )? trim($request['INDSID_REF']) : NULL ;
        $INDSVID_REF             =   (isset($request['INDSVID_REF']) && trim($request['INDSVID_REF']) !="" )? trim($request['INDSVID_REF']) : NULL ;
        $DEALSIN             =   (isset($request['DEALSIN']) && trim($request['DEALSIN']) !="" )? trim($request['DEALSIN']) : NULL ;
        $GSTTYPE             =   (isset($request['GSTTYPE']) && trim($request['GSTTYPE']) !="" )? trim($request['GSTTYPE']) : NULL ;
        $MSME_NO             =   (isset($request['MSME_NO']) && trim($request['MSME_NO']) !="" )? trim($request['MSME_NO']) : NULL ;
        $FACTORY_ACT_NO             =   (isset($request['FACTORY_ACT_NO']) && trim($request['FACTORY_ACT_NO']) !="" )? trim($request['FACTORY_ACT_NO']) : NULL ;
        $LOGO           =   NULL;

        // UDF XML
        $r_count4 = $request['Row_Count4'];
        $udffield_Data = [];
        for ($i=0; $i<=$r_count4; $i++){
            if(isset( $request['udffie_'.$i])){
                $udffield_Data[$i]['UDFBRID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['UDF_VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
            }
        }  
 
        if(count($udffield_Data)>0){            
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
 
        }else{
                $XMLUDF = NULL;
        }

        // BANK XML
        $r_count = $request['Row_Count'];
        $data = array();
        for ($i=0; $i<=$r_count; $i++){

            if((isset($request['NAME_'.$i]) && $request['NAME_'.$i] !="" || $request['IFSC_'.$i] !="" || $request['BRANCH_'.$i] !="" || $request['ACTYPE_'.$i] !="" || $request['ACNO_'.$i] !="")){
                $data[$i] = [
                    'NAME' => strtoupper(trim($request['NAME_'.$i])),
                    'IFSC' => strtoupper(trim($request['IFSC_'.$i])),
                    'BRANCH' => strtoupper(trim($request['BRANCH_'.$i])),
                    'ACTYPE' => strtoupper(trim($request['ACTYPE_'.$i])),
                    'ACNO' => trim($request['ACNO_'.$i]),
                ];
            }
        }

        if(!empty($data)){     
            $wrapped_links["BANK"] = $data; 
            $XMLBANK = ArrayToXml::convert($wrapped_links);
        }else{
            $XMLBANK = NULL;
        }

        $DEACTIVATED    =   0;  
        $DODEACTIVATED  =   NULL;  

        //$CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');       
        $VTID           =   $this->vtid_ref;
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();

       
        // Logo Upload

        /*
        $allow_extnesions   =   explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size         =   config("erpconst.attachments.max_size") * 1020 * 1024;
		$destinationPath    =   storage_path()."/docs/company".$CYID_REF."/BranchMaster/Logo";
		
        if ( !is_dir($destinationPath) ) {
            mkdir($destinationPath, 0777, true);
        }

        if(isset($request["LOGO"])){
        
            $uploadedFile           =   $request["LOGO"]; 
            $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
            $filesize               =   $uploadedFile ->getSize();  
            $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );
            $filenametostore        =  $VTID.$USERID.$CYID_REF.$BRID_REF.$FYID_REF.Date('ymdhis')."logo.".$extension;  

            if($uploadedFile->isValid()) {

                if(in_array($extension,$allow_extnesions)){
                    
                    if($filesize < $allow_size){

                        $filename   = $destinationPath."/".$filenametostore;

                        $uploadedFile->move($destinationPath, $filenametostore);
                        $LOGO = $filename;
                           
                    }else{
                        return Response::json(['errors'=>true,'msg' => 'Invalid image size ('.$filenamewithextension.')','save'=>'invalid']);
                    }
                    
                }else{
                    return Response::json(['errors'=>true,'msg' => 'Invalid image extension ('.$filenamewithextension.')','save'=>'invalid']);                            
                }
            
            }else{   
                return Response::json(['errors'=>true,'msg' => 'Invalid image file ('.$filenamewithextension.')','save'=>'invalid']);
            }
        }
        */

        $array_data   = [
                        $CYID_REF,$BRCODE, $BRNAME,
                        $BGID_REF, $GSTINNO, $CINNO, $ADDL1,$ADDL2,
                        $CTRYID_REF, $STID_REF, $CITYID_REF, $PINCODE,$BRLM,
                        $EMAILID, $PHNO, $MONO, $WEBSITE,$SKYPEID,
                        $AUTHPNAME, $AUTHPDESG, $INDSID_REF, $INDSVID_REF,$DEALSIN,
                        $GSTTYPE, $MSME_NO, $FACTORY_ACT_NO,$DEACTIVATED, $DODEACTIVATED,$SAP_CODE,$ALPS_REFNO,
                        $FYID_REF, $XMLBANK, $XMLUDF,$BRID_REF, $VTID, 
                        $USERID,$UPDATE,$UPTIME, $ACTION, $IPADDRESS
                    ];

        try {

            $sp_result = DB::select('EXEC SP_BRANCH_IN ?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?,?,?', $array_data);
        
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

            $objResponse = TblMstFrm147::where('BRID','=',$id)->first();

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first(); 

            $objCountryList = DB::table('TBL_MST_COUNTRY')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('CTRYID','CTRYCODE','NAME')
            ->get();

            $objIndTypeList = DB::table('TBL_MST_INDUSTRYTYPE')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('INDSID','INDSCODE','DESCRIPTIONS')
            ->get();

            $objIndVerList = DB::table('TBL_MST_INDUSTRYVERTICAL')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('INDSVID','INDSVCODE','DESCRIPTIONS')
            ->get();

            $objGstTypeList = DB::table('TBL_MST_GSTTYPE')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('GSTID','GSTCODE','DESCRIPTIONS')
            ->get();

            $objCurrencyList = DB::table('TBL_MST_CURRENCY')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('CRID','CRCODE','CRDESCRIPTION')
            ->get();
            
            $objUDF = DB::table('TBL_MST_BRANCH_UDF')                    
            ->where('TBL_MST_BRANCH_UDF.BRID_REF','=',$id)
            ->leftJoin('TBL_MST_UDFFOR_BRANCH','TBL_MST_UDFFOR_BRANCH.UDFBRID','=','TBL_MST_BRANCH_UDF.UDFBRID_REF')                
            ->select('TBL_MST_BRANCH_UDF.*','TBL_MST_UDFFOR_BRANCH.*')
            ->orderBy('TBL_MST_BRANCH_UDF.BR_UDFID','ASC')
            ->get()->toArray();
            $objudfCount = count($objUDF);                
            if($objudfCount==0){
                $objudfCount=1;
            }
   

            $objRegCountryName =   $this->getCountryName($objResponse->CTRYID_REF);
            $objRegStateName   =   $this->getStateName($objResponse->STID_REF);
            $objRegCityName    =   $this->getCityName($objResponse->CITYID_REF);

            $objIndtypeName   =   $this->getIndtypeName($objResponse->INDSID_REF);
            $objIndVerName    =   $this->getIndVerName($objResponse->INDSVID_REF);

            $objCompanyList     =   $this->getCompany();
            $objBranchGroupList =   $this->getBranchGroup();

            $objDataResponse = DB::table('TBL_MST_BRANCHBANK')                    
                             ->where('TBL_MST_BRANCHBANK.BRID_REF','=',$id)
                             ->select('TBL_MST_BRANCHBANK.*')
                             ->orderBy('TBL_MST_BRANCHBANK.BBID','ASC')
                             ->get()->toArray();
            $objCount = count($objDataResponse);

            

            return view('masters.Common.BranchMaster.mstfrm147edit',compact(['objResponse','user_approval_level','objRights','objCountryList','objIndTypeList','objIndVerList','objGstTypeList','objCurrencyList','objRegCountryName','objRegStateName','objRegCityName','objIndtypeName','objIndVerName','objUDF','objudfCount','objCompanyList','objBranchGroupList','objDataResponse','objCount']));
        }

    }

    public function getCountryName($CTRYID){
        return $objCountryName = DB::table('TBL_MST_COUNTRY')
        ->where('STATUS','=','A')
        ->where('CTRYID','=',$CTRYID)
        ->select('CTRYCODE','NAME')
        ->first();
    }

    public function getStateName($STID){
       return $objStateName = DB::table('TBL_MST_STATE')
        ->where('STATUS','=','A')
        ->where('STID','=',$STID)
        ->select('STCODE','NAME')
        ->first();
    }

    public function getCityName($CITYID){
        return $objCityName = DB::table('TBL_MST_CITY')
        ->where('STATUS','=','A')
        ->where('CITYID','=',$CITYID)
        ->select('CITYCODE','NAME')
        ->first();
    }

    public function getIndtypeName($INDSID){
        return $objIndTypeList = DB::table('TBL_MST_INDUSTRYTYPE')
        ->where('STATUS','=','A')
        ->where('INDSID','=',$INDSID)
        ->select('INDSID','INDSCODE','DESCRIPTIONS')
        ->first();
    }

    public function getIndVerName($INDSVID){
        return $objIndVerList = DB::table('TBL_MST_INDUSTRYVERTICAL')
            ->where('STATUS','=','A')
            ->where('INDSVID','=',$INDSVID)
            ->select('INDSVID','INDSVCODE','DESCRIPTIONS')
            ->first();
    }
     
    public function update(Request $request)
    {

      // dd($request->all());

        $update_rules = [

            'CYID_REF' => 'required', 
            'BRNAME' => 'required', 
            'BGID_REF' => 'required', 
            'ADDL1' => 'required', 
            'CTRYID_REF' => 'required', 
            'STID_REF' => 'required', 
            'CITYID_REF' => 'required', 
            'GSTTYPE' => 'required'       
        ];

        $req_update_data = [

            'CYID_REF'     =>    $request['CYID_REF'],
            'BRNAME' =>   $request['BRNAME'],
            'BGID_REF' =>   $request['BGID_REF'],
            'ADDL1' =>   $request['ADDL1'],
            'CTRYID_REF' =>   $request['CTRYID_REF'],
            'STID_REF' =>   $request['STID_REF'],
            'CITYID_REF' =>   $request['CITYID_REF'],
            'GSTTYPE' =>   $request['GSTTYPE']
        ]; 

       
        $validator = Validator::make( $req_update_data, $update_rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }
    
        $CYID_REF               =   trim($request['CYID_REF']); 
        $BRCODE             =   strtoupper(trim($request['BRCODE']) );
        $BRNAME               =   trim($request['BRNAME']); 
        $SAP_CODE               =   trim($request['SAP_CODE']); 
        $ALPS_REFNO               =   trim($request['ALPS_REFNO']); 
        
        $BGID_REF             =   (isset($request['BGID_REF']) && trim($request['BGID_REF']) !="" )? trim($request['BGID_REF']) : NULL ;
        $GSTINNO             =   (isset($request['GSTINNO']) && trim($request['GSTINNO']) !="" )? trim($request['GSTINNO']) : NULL ;
        $CINNO             =   (isset($request['CINNO']) && trim($request['CINNO']) !="" )? trim($request['CINNO']) : NULL ;
        $ADDL1             =   (isset($request['ADDL1']) && trim($request['ADDL1']) !="" )? trim($request['ADDL1']) : NULL ;
        $ADDL2             =   (isset($request['ADDL2']) && trim($request['ADDL2']) !="" )? trim($request['ADDL2']) : NULL ;
        $CTRYID_REF             =   (isset($request['CTRYID_REF']) && trim($request['CTRYID_REF']) !="" )? trim($request['CTRYID_REF']) : NULL ;
        $STID_REF             =   (isset($request['STID_REF']) && trim($request['STID_REF']) !="" )? trim($request['STID_REF']) : NULL ;
        $CITYID_REF             =   (isset($request['CITYID_REF']) && trim($request['CITYID_REF']) !="" )? trim($request['CITYID_REF']) : NULL ;
        $PINCODE             =   (isset($request['PINCODE']) && trim($request['PINCODE']) !="" )? trim($request['PINCODE']) : NULL ;
        $BRLM             =   (isset($request['BRLM']) && trim($request['BRLM']) !="" )? trim($request['BRLM']) : NULL ;
        $EMAILID             =   (isset($request['EMAILID']) && trim($request['EMAILID']) !="" )? trim($request['EMAILID']) : NULL ;
        $PHNO             =   (isset($request['PHNO']) && trim($request['PHNO']) !="" )? trim($request['PHNO']) : NULL ;
        $MONO             =   (isset($request['MONO']) && trim($request['MONO']) !="" )? trim($request['MONO']) : NULL ;
        $WEBSITE             =   (isset($request['WEBSITE']) && trim($request['WEBSITE']) !="" )? trim($request['WEBSITE']) : NULL ;
        $SKYPEID             =   (isset($request['SKYPEID']) && trim($request['SKYPEID']) !="" )? trim($request['SKYPEID']) : NULL ;
        $AUTHPNAME             =   (isset($request['AUTHPNAME']) && trim($request['AUTHPNAME']) !="" )? trim($request['AUTHPNAME']) : NULL ;
        $AUTHPDESG             =   (isset($request['AUTHPDESG']) && trim($request['AUTHPDESG']) !="" )? trim($request['AUTHPDESG']) : NULL ;
        $INDSID_REF             =   (isset($request['INDSID_REF']) && trim($request['INDSID_REF']) !="" )? trim($request['INDSID_REF']) : NULL ;
        $INDSVID_REF             =   (isset($request['INDSVID_REF']) && trim($request['INDSVID_REF']) !="" )? trim($request['INDSVID_REF']) : NULL ;
        $DEALSIN             =   (isset($request['DEALSIN']) && trim($request['DEALSIN']) !="" )? trim($request['DEALSIN']) : NULL ;
        $GSTTYPE             =   (isset($request['GSTTYPE']) && trim($request['GSTTYPE']) !="" )? trim($request['GSTTYPE']) : NULL ;
        $MSME_NO             =   (isset($request['MSME_NO']) && trim($request['MSME_NO']) !="" )? trim($request['MSME_NO']) : NULL ;
        $FACTORY_ACT_NO             =   (isset($request['FACTORY_ACT_NO']) && trim($request['FACTORY_ACT_NO']) !="" )? trim($request['FACTORY_ACT_NO']) : NULL ;
        $LOGO           =   NULL;

        $r_count4 = $request['Row_Count4'];
        $udffield_Data = [];
        for ($i=0; $i<=$r_count4; $i++){
            
            if(isset( $request['udffie_'.$i])){
                $udffield_Data[$i]['UDFBRID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['UDF_VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
            }
        }  

        if(count($udffield_Data)>0){            
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
 
        }else{
                $XMLUDF = NULL;
        }

        
       // dd($XMLUDF);


        // BANK XML
        $r_count = $request['Row_Count'];
        $data = array();
        for ($i=0; $i<=$r_count; $i++){

            if((isset($request['NAME_'.$i]) && $request['NAME_'.$i] !="" || $request['IFSC_'.$i] !="" || $request['BRANCH_'.$i] !="" || $request['ACTYPE_'.$i] !="" || $request['ACNO_'.$i] !="")){
                $data[$i] = [
                    'NAME' => strtoupper(trim($request['NAME_'.$i])),
                    'IFSC' => strtoupper(trim($request['IFSC_'.$i])),
                    'BRANCH' => strtoupper(trim($request['BRANCH_'.$i])),
                    'ACTYPE' => strtoupper(trim($request['ACTYPE_'.$i])),
                    'ACNO' => trim($request['ACNO_'.$i]),
                ];
            }
        }

        if(!empty($data)){     
            $wrapped_links["BANK"] = $data; 
            $XMLBANK = ArrayToXml::convert($wrapped_links);
        }else{
            $XMLBANK = NULL;
        }


        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
        $newDateString = NULL;

        $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt) ){
            
            $newdt = str_replace( "/", "-",  $newdt ) ;

            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        

        $DODEACTIVATED = $newDateString;

        //$CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID     =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();

        /*
        // Logo Upload
        $allow_extnesions   =   explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size         =   config("erpconst.attachments.max_size") * 1020 * 1024;
        $ATTACH_DOCNO       =   ""; 
        $ATTACH_DOCDT       =   ""; 

		$destinationPath    =   storage_path()."/docs/company".$CYID_REF."/BranchMaster/Logo";
		
        if ( !is_dir($destinationPath) ) {
            mkdir($destinationPath, 0777, true);
        }

        if(isset($request["LOGO"])){
        
            $uploadedFile           =   $request["LOGO"]; 
            $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
            $filesize               =   $uploadedFile ->getSize();  
            $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );
            $filenametostore        =  $VTID.$USERID.$CYID_REF.$BRID_REF.$FYID_REF.Date('ymdhis')."logo.".$extension;  
           
            if($uploadedFile->isValid()) {

                if(in_array($extension,$allow_extnesions)){
                    
                    if($filesize < $allow_size){

                        $filename   = $destinationPath."/".$filenametostore;

                        $uploadedFile->move($destinationPath, $filenametostore);
                        $LOGO = $filename;
                           
                    }else{
                        return Response::json(['errors'=>true,'msg' => 'Invalid image size ('.$filenamewithextension.')','save'=>'invalid']);
                    }
                    
                }else{
                    return Response::json(['errors'=>true,'msg' => 'Invalid image extension ('.$filenamewithextension.')','save'=>'invalid']);                            
                }
            
            }else{   
                return Response::json(['errors'=>true,'msg' => 'Invalid image file ('.$filenamewithextension.')','save'=>'invalid']);
            }
        }*/
        
        $array_data   = [
            $CYID_REF,$BRCODE, $BRNAME,
            $BGID_REF, $GSTINNO, $CINNO, $ADDL1,$ADDL2,
            $CTRYID_REF, $STID_REF, $CITYID_REF, $PINCODE,$BRLM,
            $EMAILID, $PHNO, $MONO, $WEBSITE,$SKYPEID,
            $AUTHPNAME, $AUTHPDESG, $INDSID_REF, $INDSVID_REF,$DEALSIN,
            $GSTTYPE, $MSME_NO, $FACTORY_ACT_NO,$DEACTIVATED, $DODEACTIVATED,$SAP_CODE,$ALPS_REFNO,
            $FYID_REF, $XMLBANK, $XMLUDF,$BRID_REF, $VTID, 
            $USERID,$UPDATE,$UPTIME, $ACTION, $IPADDRESS
        ];

        try {

        $sp_result = DB::select('EXEC SP_BRANCH_UP ?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?,?,?', $array_data);

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
        
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/BranchMaster";
		
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
            return redirect()->route("master",[147,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
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
        
        //    return redirect()->route("master",[147,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");
    
      //  }
     
        if($sp_result[0]->RESULT=="SUCCESS"){

            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }

            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }

            return redirect()->route("master",[147,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[147,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("master",[147,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
            return redirect()->route("master",[147,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   

  


   



    //singleApprove begin
    public function singleapprove(Request $request)
    {
      
         $approv_rules = [
 
            'CYID_REF' => 'required', 
            'BRNAME' => 'required', 
            'BGID_REF' => 'required', 
            'ADDL1' => 'required', 
            'CTRYID_REF' => 'required', 
            'STID_REF' => 'required', 
            'CITYID_REF' => 'required', 
            'GSTTYPE' => 'required'          
         ];
 
         $req_approv_data = [
 
            'CYID_REF'     =>    $request['CYID_REF'],
            'BRNAME' =>   $request['BRNAME'],
            'BGID_REF' =>   $request['BGID_REF'],
            'ADDL1' =>   $request['ADDL1'],
            'CTRYID_REF' =>   $request['CTRYID_REF'],
            'STID_REF' =>   $request['STID_REF'],
            'CITYID_REF' =>   $request['CITYID_REF'],
            'GSTTYPE' =>   $request['GSTTYPE']
         ]; 
 
 
         $validator = Validator::make( $req_approv_data, $approv_rules, $this->messages);
 
         if ($validator->fails())
         {
         return Response::json(['errors' => $validator->errors()]);	
         }
     
         $CYID_REF               =   trim($request['CYID_REF']); 
        $BRCODE             =   strtoupper(trim($request['BRCODE']) );
        $BRNAME               =   trim($request['BRNAME']); 
        $SAP_CODE               =   trim($request['SAP_CODE']); 
        $ALPS_REFNO               =   trim($request['ALPS_REFNO']); 
        
        $BGID_REF             =   (isset($request['BGID_REF']) && trim($request['BGID_REF']) !="" )? trim($request['BGID_REF']) : NULL ;
        $GSTINNO             =   (isset($request['GSTINNO']) && trim($request['GSTINNO']) !="" )? trim($request['GSTINNO']) : NULL ;
        $CINNO             =   (isset($request['CINNO']) && trim($request['CINNO']) !="" )? trim($request['CINNO']) : NULL ;
        $ADDL1             =   (isset($request['ADDL1']) && trim($request['ADDL1']) !="" )? trim($request['ADDL1']) : NULL ;
        $ADDL2             =   (isset($request['ADDL2']) && trim($request['ADDL2']) !="" )? trim($request['ADDL2']) : NULL ;
        $CTRYID_REF             =   (isset($request['CTRYID_REF']) && trim($request['CTRYID_REF']) !="" )? trim($request['CTRYID_REF']) : NULL ;
        $STID_REF             =   (isset($request['STID_REF']) && trim($request['STID_REF']) !="" )? trim($request['STID_REF']) : NULL ;
        $CITYID_REF             =   (isset($request['CITYID_REF']) && trim($request['CITYID_REF']) !="" )? trim($request['CITYID_REF']) : NULL ;
        $PINCODE             =   (isset($request['PINCODE']) && trim($request['PINCODE']) !="" )? trim($request['PINCODE']) : NULL ;
        $BRLM             =   (isset($request['BRLM']) && trim($request['BRLM']) !="" )? trim($request['BRLM']) : NULL ;
        $EMAILID             =   (isset($request['EMAILID']) && trim($request['EMAILID']) !="" )? trim($request['EMAILID']) : NULL ;
        $PHNO             =   (isset($request['PHNO']) && trim($request['PHNO']) !="" )? trim($request['PHNO']) : NULL ;
        $MONO             =   (isset($request['MONO']) && trim($request['MONO']) !="" )? trim($request['MONO']) : NULL ;
        $WEBSITE             =   (isset($request['WEBSITE']) && trim($request['WEBSITE']) !="" )? trim($request['WEBSITE']) : NULL ;
        $SKYPEID             =   (isset($request['SKYPEID']) && trim($request['SKYPEID']) !="" )? trim($request['SKYPEID']) : NULL ;
        $AUTHPNAME             =   (isset($request['AUTHPNAME']) && trim($request['AUTHPNAME']) !="" )? trim($request['AUTHPNAME']) : NULL ;
        $AUTHPDESG             =   (isset($request['AUTHPDESG']) && trim($request['AUTHPDESG']) !="" )? trim($request['AUTHPDESG']) : NULL ;
        $INDSID_REF             =   (isset($request['INDSID_REF']) && trim($request['INDSID_REF']) !="" )? trim($request['INDSID_REF']) : NULL ;
        $INDSVID_REF             =   (isset($request['INDSVID_REF']) && trim($request['INDSVID_REF']) !="" )? trim($request['INDSVID_REF']) : NULL ;
        $DEALSIN             =   (isset($request['DEALSIN']) && trim($request['DEALSIN']) !="" )? trim($request['DEALSIN']) : NULL ;
        $GSTTYPE             =   (isset($request['GSTTYPE']) && trim($request['GSTTYPE']) !="" )? trim($request['GSTTYPE']) : NULL ;
        $MSME_NO             =   (isset($request['MSME_NO']) && trim($request['MSME_NO']) !="" )? trim($request['MSME_NO']) : NULL ;
        $FACTORY_ACT_NO             =   (isset($request['FACTORY_ACT_NO']) && trim($request['FACTORY_ACT_NO']) !="" )? trim($request['FACTORY_ACT_NO']) : NULL ;
        $LOGO           =   NULL;

        $r_count4 = $request['Row_Count4'];
        $udffield_Data = [];
        for ($i=0; $i<=$r_count4; $i++){
            
            if(isset( $request['udffie_'.$i])){
                $udffield_Data[$i]['UDFBRID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['UDF_VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
            }
        }  

        if(count($udffield_Data)>0){            
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
 
        }else{
                $XMLUDF = NULL;
        }

        
       // dd($XMLUDF);


        // BANK XML
        $r_count = $request['Row_Count'];
        $data = array();
        for ($i=0; $i<=$r_count; $i++){

            if((isset($request['NAME_'.$i]) && $request['NAME_'.$i] !="" || $request['IFSC_'.$i] !="" || $request['BRANCH_'.$i] !="" || $request['ACTYPE_'.$i] !="" || $request['ACNO_'.$i] !="")){
                $data[$i] = [
                    'NAME' => strtoupper(trim($request['NAME_'.$i])),
                    'IFSC' => strtoupper(trim($request['IFSC_'.$i])),
                    'BRANCH' => strtoupper(trim($request['BRANCH_'.$i])),
                    'ACTYPE' => strtoupper(trim($request['ACTYPE_'.$i])),
                    'ACNO' => trim($request['ACNO_'.$i]),
                ];
            }
        }

        if(!empty($data)){     
            $wrapped_links["BANK"] = $data; 
            $XMLBANK = ArrayToXml::convert($wrapped_links);
        }else{
            $XMLBANK = NULL;
        }


        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
        $newDateString = NULL;

        $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt) ){
            
            $newdt = str_replace( "/", "-",  $newdt ) ;

            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        

        $DODEACTIVATED = $newDateString;


         //$CYID_REF   =   Auth::user()->CYID_REF;
         $BRID_REF   =   Session::get('BRID_REF');
 
         $FYID_REF   =   Session::get('FYID_REF');       
         $VTID       =   $this->vtid_ref;
         $USERID     =   Auth::user()->USERID;
         $UPDATE     =   Date('Y-m-d');
         
         $UPTIME     =   Date('h:i:s.u');
         $ACTION     =   trim($request['user_approval_level']);   // user approval level value
         $IPADDRESS  =   $request->getClientIp();

        /*                                                                            
         // Logo Upload
        $allow_extnesions   =   explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size         =   config("erpconst.attachments.max_size") * 1020 * 1024;
        $ATTACH_DOCNO       =   ""; 
        $ATTACH_DOCDT       =   ""; 

		$destinationPath    =   storage_path()."/docs/company".$CYID_REF."/BranchMaster/Logo";
		
        if ( !is_dir($destinationPath) ) {
            mkdir($destinationPath, 0777, true);
        }

        if(isset($request["LOGO"])){
        
            $uploadedFile           =   $request["LOGO"]; 
            $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
            $filesize               =   $uploadedFile ->getSize();  
            $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );
            $filenametostore        =  $VTID.$USERID.$CYID_REF.$BRID_REF.$FYID_REF.Date('ymdhis')."logo.".$extension;  
            if($uploadedFile->isValid()) {

                if(in_array($extension,$allow_extnesions)){
                    
                    if($filesize < $allow_size){

                        $filename   = $destinationPath."/".$filenametostore;

                        $uploadedFile->move($destinationPath, $filenametostore);
                        $LOGO = $filename;
                           
                    }else{
                        return Response::json(['errors'=>true,'msg' => 'Invalid image size ('.$filenamewithextension.')','save'=>'invalid']);
                    }
                    
                }else{
                    return Response::json(['errors'=>true,'msg' => 'Invalid image extension ('.$filenamewithextension.')','save'=>'invalid']);                            
                }
            
            }else{   
                return Response::json(['errors'=>true,'msg' => 'Invalid image file ('.$filenamewithextension.')','save'=>'invalid']);
            }
        }
         
        */
       
            $array_data   = [
                $CYID_REF,$BRCODE, $BRNAME,
                $BGID_REF, $GSTINNO, $CINNO, $ADDL1,$ADDL2,
                $CTRYID_REF, $STID_REF, $CITYID_REF, $PINCODE,$BRLM,
                $EMAILID, $PHNO, $MONO, $WEBSITE,$SKYPEID,
                $AUTHPNAME, $AUTHPDESG, $INDSID_REF, $INDSVID_REF,$DEALSIN,
                $GSTTYPE, $MSME_NO, $FACTORY_ACT_NO,$DEACTIVATED, $DODEACTIVATED,$SAP_CODE,$ALPS_REFNO,
                $FYID_REF, $XMLBANK, $XMLUDF,$BRID_REF, $VTID, 
                $USERID,$UPDATE,$UPTIME, $ACTION, $IPADDRESS
             ];

        try {

        $sp_result = DB::select('EXEC SP_BRANCH_UP ?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?,?,?', $array_data);

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
            $objResponse = TblMstFrm147::where('BRID','=',$id)->first();

            $objCountryList = DB::table('TBL_MST_COUNTRY')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('CTRYID','CTRYCODE','NAME')
            ->get();
                
            $objIndTypeList = DB::table('TBL_MST_INDUSTRYTYPE')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('INDSID','INDSCODE','DESCRIPTIONS')
            ->get();

            $objIndVerList = DB::table('TBL_MST_INDUSTRYVERTICAL')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('INDSVID','INDSVCODE','DESCRIPTIONS')
            ->get();

            $objGstTypeList = DB::table('TBL_MST_GSTTYPE')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('GSTID','GSTCODE','DESCRIPTIONS')
            ->get();

           
            
            $objUDF = DB::table('TBL_MST_BRANCH_UDF')                    
            ->where('TBL_MST_BRANCH_UDF.BRID_REF','=',$id)
            ->leftJoin('TBL_MST_UDFFOR_BRANCH','TBL_MST_UDFFOR_BRANCH.UDFBRID','=','TBL_MST_BRANCH_UDF.UDFBRID_REF')                
            ->select('TBL_MST_BRANCH_UDF.*','TBL_MST_UDFFOR_BRANCH.*')
            ->orderBy('TBL_MST_BRANCH_UDF.BR_UDFID','ASC')
            ->get()->toArray();
            $objudfCount = count($objUDF);                
            if($objudfCount==0){
                $objudfCount=1;
            }
   
            $objRegCountryName =   $this->getCountryName($objResponse->CTRYID_REF);
            $objRegStateName   =   $this->getStateName($objResponse->STID_REF);
            $objRegCityName    =   $this->getCityName($objResponse->CITYID_REF);

            $objIndtypeName   =   $this->getIndtypeName($objResponse->INDSID_REF);
            $objIndVerName    =   $this->getIndVerName($objResponse->INDSVID_REF);

            $objCompanyList     =   $this->getCompany();
            $objBranchGroupList =   $this->getBranchGroup();

            $objDataResponse = DB::table('TBL_MST_BRANCHBANK')                    
                             ->where('TBL_MST_BRANCHBANK.BRID_REF','=',$id)
                             ->select('TBL_MST_BRANCHBANK.*')
                             ->orderBy('TBL_MST_BRANCHBANK.BBID','ASC')
                             ->get()->toArray();
            $objCount = count($objDataResponse);
            

            return view('masters.Common.BranchMaster.mstfrm147view',compact([
                'objResponse','objCountryList','objIndTypeList','objIndVerList','objGstTypeList','objUDF',
                'objudfCount','objRegCountryName','objRegStateName','objRegCityName','objIndtypeName',
                'objIndVerName','objCompanyList','objBranchGroupList','objDataResponse','objCount'
                ]));
        }

    }
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }

        $objResponse = TblMstFrm147::whereIn('BRID',$ids_data)->get();
        
        return view('masters.Common.BranchMaster.mstfrm147print',compact(['objResponse']));
   }//print


   
    

    
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objResponse = TblMstFrm147::where('BRID','=',$id)->first();

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

            return view('masters.Common.BranchMaster.mstfrm147attachment',compact(['objResponse','objMstVoucherType','objAttachments']));
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
            $TABLE      =   "TBL_MST_BRANCH";
            $FIELD      =   "BRID";
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
        $TABLE      =   "TBL_MST_BRANCH";
        $FIELD      =   "BRID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
        
        $cancelData[0]= ['NT' =>'TBL_MST_BRANCHBANK'];
        $cancelData[1]= ['NT' =>'TBL_MST_BRANCH_UDF'];
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

public function amendment($id){

        if(!is_null($id))
        {        
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');

            $objResponse  =   DB::table('TBL_MST_BRANCH')                          
            ->where('TBL_MST_BRANCH.CYID_REF','=',$CYID_REF)
            ->where('TBL_MST_BRANCH.BRID','=',$id)
            ->select('TBL_MST_BRANCH.*')
            ->first();

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

          
            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first(); 

            $objCountryList = DB::table('TBL_MST_COUNTRY')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('CTRYID','CTRYCODE','NAME')
            ->get();

            $objIndTypeList = DB::table('TBL_MST_INDUSTRYTYPE')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('INDSID','INDSCODE','DESCRIPTIONS')
            ->get();

            $objIndVerList = DB::table('TBL_MST_INDUSTRYVERTICAL')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('INDSVID','INDSVCODE','DESCRIPTIONS')
            ->get();

            $objGstTypeList = DB::table('TBL_MST_GSTTYPE')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('GSTID','GSTCODE','DESCRIPTIONS')
            ->get();

            $objCurrencyList = DB::table('TBL_MST_CURRENCY')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('CRID','CRCODE','CRDESCRIPTION')
            ->get();
            
            $objUDF = DB::table('TBL_MST_BRANCH_UDF')                    
            ->where('TBL_MST_BRANCH_UDF.BRID_REF','=',$id)
            ->leftJoin('TBL_MST_UDFFOR_BRANCH','TBL_MST_UDFFOR_BRANCH.UDFBRID','=','TBL_MST_BRANCH_UDF.UDFBRID_REF')                
            ->select('TBL_MST_BRANCH_UDF.*','TBL_MST_UDFFOR_BRANCH.*')
            ->orderBy('TBL_MST_BRANCH_UDF.BR_UDFID','ASC')
            ->get()->toArray();
            $objudfCount = count($objUDF);                
            if($objudfCount==0){
                $objudfCount=1;
            }
   

            $objRegCountryName =   $this->getCountryName($objResponse->CTRYID_REF);
            $objRegStateName   =   $this->getStateName($objResponse->STID_REF);
            $objRegCityName    =   $this->getCityName($objResponse->CITYID_REF);

            $objIndtypeName   =   $this->getIndtypeName($objResponse->INDSID_REF);
            $objIndVerName    =   $this->getIndVerName($objResponse->INDSVID_REF);

            $objCompanyList     =   $this->getCompany();
            $objBranchGroupList =   $this->getBranchGroup();

            $objDataResponse = DB::table('TBL_MST_BRANCHBANK')                    
                             ->where('TBL_MST_BRANCHBANK.BRID_REF','=',$id)
                             ->select('TBL_MST_BRANCHBANK.*')
                             ->orderBy('TBL_MST_BRANCHBANK.BBID','ASC')
                             ->get()->toArray();
            $objCount = count($objDataResponse);

            

            return view('masters.Common.BranchMaster.mstfrm147amendment',compact(['objResponse','user_approval_level','objRights','objCountryList','objIndTypeList','objIndVerList','objGstTypeList','objCurrencyList','objRegCountryName','objRegStateName','objRegCityName','objIndtypeName','objIndVerName','objUDF','objudfCount','objCompanyList','objBranchGroupList','objDataResponse','objCount']));
        }
     
    }

    public function saveamendment(Request $request)
    {

        $update_rules = [

            'CYID_REF' => 'required', 
            'BRNAME' => 'required', 
            'BGID_REF' => 'required', 
            'ADDL1' => 'required', 
            'CTRYID_REF' => 'required', 
            'STID_REF' => 'required', 
            'CITYID_REF' => 'required', 
            'GSTTYPE' => 'required'       
        ];

        $req_update_data = [

            'CYID_REF'     =>    $request['CYID_REF'],
            'BRNAME' =>   $request['BRNAME'],
            'BGID_REF' =>   $request['BGID_REF'],
            'ADDL1' =>   $request['ADDL1'],
            'CTRYID_REF' =>   $request['CTRYID_REF'],
            'STID_REF' =>   $request['STID_REF'],
            'CITYID_REF' =>   $request['CITYID_REF'],
            'GSTTYPE' =>   $request['GSTTYPE']
        ]; 

       
        $validator = Validator::make( $req_update_data, $update_rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);  
        }

        $AMENDMENTNO                =   trim($request['ANO']);
        $AMENDMENTDATE     =   trim($request['AMENDMENT_DATE']);
        $AMENDMENTREASON   =   trim($request['AMENDMENT_REASON']);
    
        $CYID_REF               =   trim($request['CYID_REF']); 
        $BRCODE             =   strtoupper(trim($request['BRCODE']) );
        $BRNAME               =   trim($request['BRNAME']); 
        $SAP_CODE               =   trim($request['SAP_CODE']); 
        $ALPS_REFNO               =   trim($request['ALPS_REFNO']); 
        
        $BGID_REF             =   (isset($request['BGID_REF']) && trim($request['BGID_REF']) !="" )? trim($request['BGID_REF']) : NULL ;
        $GSTINNO             =   (isset($request['GSTINNO']) && trim($request['GSTINNO']) !="" )? trim($request['GSTINNO']) : NULL ;
        $CINNO             =   (isset($request['CINNO']) && trim($request['CINNO']) !="" )? trim($request['CINNO']) : NULL ;
        $ADDL1             =   (isset($request['ADDL1']) && trim($request['ADDL1']) !="" )? trim($request['ADDL1']) : NULL ;
        $ADDL2             =   (isset($request['ADDL2']) && trim($request['ADDL2']) !="" )? trim($request['ADDL2']) : NULL ;
        $CTRYID_REF             =   (isset($request['CTRYID_REF']) && trim($request['CTRYID_REF']) !="" )? trim($request['CTRYID_REF']) : NULL ;
        $STID_REF             =   (isset($request['STID_REF']) && trim($request['STID_REF']) !="" )? trim($request['STID_REF']) : NULL ;
        $CITYID_REF             =   (isset($request['CITYID_REF']) && trim($request['CITYID_REF']) !="" )? trim($request['CITYID_REF']) : NULL ;
        $PINCODE             =   (isset($request['PINCODE']) && trim($request['PINCODE']) !="" )? trim($request['PINCODE']) : NULL ;
        $BRLM             =   (isset($request['BRLM']) && trim($request['BRLM']) !="" )? trim($request['BRLM']) : NULL ;
        $EMAILID             =   (isset($request['EMAILID']) && trim($request['EMAILID']) !="" )? trim($request['EMAILID']) : NULL ;
        $PHNO             =   (isset($request['PHNO']) && trim($request['PHNO']) !="" )? trim($request['PHNO']) : NULL ;
        $MONO             =   (isset($request['MONO']) && trim($request['MONO']) !="" )? trim($request['MONO']) : NULL ;
        $WEBSITE             =   (isset($request['WEBSITE']) && trim($request['WEBSITE']) !="" )? trim($request['WEBSITE']) : NULL ;
        $SKYPEID             =   (isset($request['SKYPEID']) && trim($request['SKYPEID']) !="" )? trim($request['SKYPEID']) : NULL ;
        $AUTHPNAME             =   (isset($request['AUTHPNAME']) && trim($request['AUTHPNAME']) !="" )? trim($request['AUTHPNAME']) : NULL ;
        $AUTHPDESG             =   (isset($request['AUTHPDESG']) && trim($request['AUTHPDESG']) !="" )? trim($request['AUTHPDESG']) : NULL ;
        $INDSID_REF             =   (isset($request['INDSID_REF']) && trim($request['INDSID_REF']) !="" )? trim($request['INDSID_REF']) : NULL ;
        $INDSVID_REF             =   (isset($request['INDSVID_REF']) && trim($request['INDSVID_REF']) !="" )? trim($request['INDSVID_REF']) : NULL ;
        $DEALSIN             =   (isset($request['DEALSIN']) && trim($request['DEALSIN']) !="" )? trim($request['DEALSIN']) : NULL ;
        $GSTTYPE             =   (isset($request['GSTTYPE']) && trim($request['GSTTYPE']) !="" )? trim($request['GSTTYPE']) : NULL ;
        $MSME_NO             =   (isset($request['MSME_NO']) && trim($request['MSME_NO']) !="" )? trim($request['MSME_NO']) : NULL ;
        $FACTORY_ACT_NO             =   (isset($request['FACTORY_ACT_NO']) && trim($request['FACTORY_ACT_NO']) !="" )? trim($request['FACTORY_ACT_NO']) : NULL ;
        $LOGO           =   NULL;

        $r_count4 = $request['Row_Count4'];
        $udffield_Data = [];
        for ($i=0; $i<=$r_count4; $i++){
            
            if(isset( $request['udffie_'.$i])){
                $udffield_Data[$i]['UDFBRID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['UDF_VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
            }
        }  

        if(count($udffield_Data)>0){            
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
 
        }else{
                $XMLUDF = NULL;
        }
        
       
        $r_count = $request['Row_Count'];
        $data = array();
        for ($i=0; $i<=$r_count; $i++){

            if((isset($request['NAME_'.$i]) && $request['NAME_'.$i] !="" || $request['IFSC_'.$i] !="" || $request['BRANCH_'.$i] !="" || $request['ACTYPE_'.$i] !="" || $request['ACNO_'.$i] !="")){
                $data[$i] = [
                    'NAME' => strtoupper(trim($request['NAME_'.$i])),
                    'IFSC' => strtoupper(trim($request['IFSC_'.$i])),
                    'BRANCH' => strtoupper(trim($request['BRANCH_'.$i])),
                    'ACTYPE' => strtoupper(trim($request['ACTYPE_'.$i])),
                    'ACNO' => trim($request['ACNO_'.$i]),
                ];
            }
        }

        if(!empty($data)){     
            $wrapped_links["BANK"] = $data; 
            $XMLBANK = ArrayToXml::convert($wrapped_links);
        }else{
            $XMLBANK = NULL;
        }


        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
        $newDateString = NULL;

        $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt) ){
            
            $newdt = str_replace( "/", "-",  $newdt ) ;

            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        

        $DODEACTIVATED = $newDateString;

        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID     =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();

        
        
        $array_data   = [
            $CYID_REF,$BRCODE, $BRNAME,
            $BGID_REF, $GSTINNO, $CINNO, $ADDL1,$ADDL2,
            $CTRYID_REF, $STID_REF, $CITYID_REF, $PINCODE,$BRLM,
            $EMAILID, $PHNO, $MONO, $WEBSITE,$SKYPEID,
            $AUTHPNAME, $AUTHPDESG, $INDSID_REF, $INDSVID_REF,$DEALSIN,
            $GSTTYPE, $MSME_NO, $FACTORY_ACT_NO,$DEACTIVATED, $DODEACTIVATED,$SAP_CODE,$ALPS_REFNO,
            $FYID_REF, $XMLBANK, $XMLUDF,$BRID_REF, $VTID, 
            $USERID,$UPDATE,$UPTIME, $ACTION, $IPADDRESS, $AMENDMENTNO, $AMENDMENTDATE, $AMENDMENTREASON
        ];

        
        try {

        $sp_result = DB::select('EXEC SP_BRANCH_AMENDMENT ?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?,?,?, ?,?,?', $array_data);

        } catch (\Throwable $th) {

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);

        }  
 

        if($sp_result[0]->RESULT=="SUCCESS"){  


            return Response::json(['success' =>true,'msg' => 'Record successfully Amended.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOUND"){

        
            return Response::json(['errors'=>true,'msg' => 'No record found.','exist'=>'norecord']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);
        }
        
        exit();            
    } 




}
