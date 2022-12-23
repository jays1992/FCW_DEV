<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm146;
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


class MstFrm146Controller extends Controller
{
   
    protected $form_id = 146;
    protected $vtid_ref   = 104;  //voucher type id

    //validation messages
    protected   $messages = [
                    'CYCODE.required' => 'Required field',
                    'CYCODE.unique' => 'Duplicate Code',
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
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first(); 

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');  

        $objDataList=DB::select("SELECT CYID,CYCODE,NAME,REGADDL1,INDATE,DEACTIVATED,DODEACTIVATED,STATUS FROM TBL_MST_COMPANY WHERE CYID='$CYID_REF'");
       
        

        return view('masters.Common.CompanyMaster.mstfrm146',compact(['objRights','objDataList']));

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
        
        return view('masters.Common.CompanyMaster.mstfrm146add',compact(['objCountryList','objIndTypeList','objIndVerList','objGstTypeList','objCurrencyList','objUdfListing','objudfCount']));
    }

    public function getUdfListing(){ 
  
        $cyidRef =  Auth::user()->CYID_REF;
        $bridRef = Session::get('BRID_REF');
        $fyidRef = Session::get('FYID_REF');

		$ObjUnionUDF = DB::table("TBL_MST_UDFFOR_CO")->select('*')
                    ->whereIn('PARENTID',function($query) use ($cyidRef)
                                {       
                                $query->select('UDFCOID')->from('TBL_MST_UDFFOR_CO')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$cyidRef);
                                                                       
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$cyidRef);
                                    
                   

        $objUdfData = DB::table('TBL_MST_UDFFOR_CO')
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
            
                // echo '<tr id="stidref_'.$state->STID.'" class="cls_stidref">
                // <td width="50%">'.$state->STCODE.'
                // <input type="hidden" id="txtstidref_'.$state->STID.'" data-desc="'.$state->STCODE.'-'.$state->NAME.'" value="'.$state->STID.'" />
                // </td>
                // <td>'.$state->NAME.'</td>
                // </tr>';

                echo '<tr >
                <td  class="ROW1" align="center"> <input type="checkbox" name="SELECT_STID_REF[]" id="stidref_'.$state->STID.'" class="cls_stidref" value="'.$state->STID.'" ></td>
                <td  class="ROW2">'.$state->STCODE.'
                <input type="hidden" id="txtstidref_'.$state->STID.'" data-desc="'.$state->STCODE.'-'.$state->NAME.'" value="'.$state->STID.'" />
                </td>
                <td class="ROW3">'.$state->NAME.'</td>
                </tr>';
            }
        }
        else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
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
            
                // echo '<tr id="cityidref_'.$city->CITYID.'" class="cls_cityidref">
                // <td width="50%">'.$city->CITYCODE.'
                // <input type="hidden" id="txtcityidref_'.$city->CITYID.'" data-desc="'.$city->CITYCODE.'-'.$city->NAME.'" value="'.$city->CITYID.'" />
                // </td>
                // <td>'.$city->NAME.'</td>
                // </tr>';
                echo '<tr >
                <td  class="ROW1" align="center"> <input type="checkbox" name="SELECT_CITYID_REF[]" id="cityidref_'.$city->CITYID.'" class="cls_cityidref" value="'.$city->CITYID.'" ></td>
                <td  class="ROW2" >'.$city->CITYCODE.'
                        <input type="hidden" id="txtcityidref_'.$city->CITYID.'" data-desc="'.$city->CITYCODE.'-'.$city->NAME.'" value="'.$city->CITYID.'" />
                </td>
                <td class="ROW3">'.$city->NAME.'</td>
                </tr>';

            }
        }
        else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
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
            
                echo '<tr >
                <td  class="ROW1" align="center"> <input type="checkbox" name="SELECT_CORSTID_REF[]" id="cor_stidref_'.$state->STID.'" class="cls_cor_stidref" value="'.$state->STID.'" ></td>
                <td class="ROW2">'.$state->STCODE.'
                <input type="hidden" id="txtcor_stidref_'.$state->STID.'" data-desc="'.$state->STCODE.'-'.$state->NAME.'" value="'.$state->STID.'" />
                </td>
                <td class="ROW3">'.$state->NAME.'</td>
                </tr>';
            }
        }
        else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
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
            
                echo '<tr >
                <td  class="ROW1" align="center"> <input type="checkbox" name="SELECT_CORCITYID_REF[]" id="cor_cityidref_'.$city->CITYID.'" class="cls_cor_cityidref" value="'.$city->CITYID.'" ></td>
                <td class="ROW2">'.$city->CITYCODE.'
                <input type="hidden" id="txtcor_cityidref_'.$city->CITYID.'" data-desc="'.$city->CITYCODE.'-'.$city->NAME.'" value="'.$city->CITYID.'" />
                </td>
                <td class="ROW3">'.$city->NAME.'</td>
                </tr>';
            }
        }
        else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
        }
    }

   public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $CYCODE =   $request['CYCODE'];
        
        $objLabel = DB::table('TBL_MST_COMPANY')
        ->where('CYCODE','=',$CYCODE)
        ->select('CYCODE')
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
            'CYCODE' => 'required|unique:TBL_MST_COMPANY',
            'NAME' => 'required', 
            'REGADDL1' => 'required', 
            'REGCTRYID_REF' => 'required', 
            'REGSTID_REF' => 'required', 
            'REGCITYID_REF' => 'required', 
            'GSTTYPE' => 'required', 
            'CRID_REF' => 'required',          
        ];

        $req_data = [

            'CYCODE'     =>    $request['CYCODE'],
            'NAME' =>   $request['NAME'],
            'REGADDL1' =>   $request['REGADDL1'],
            'REGCTRYID_REF' =>   $request['REGCTRYID_REF'],
            'REGSTID_REF' =>   $request['REGSTID_REF'],
            'REGCITYID_REF' =>   $request['REGCITYID_REF'],
            'GSTTYPE' =>   $request['GSTTYPE'],
            'CRID_REF' =>   $request['CRID_REF']
        ]; 

        
        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }

        $CYCODE             =   strtoupper(trim($request['CYCODE']) );
        $NAME               =   trim($request['NAME']); 
        
        $GSTINNO             =   (isset($request['GSTINNO']) && trim($request['GSTINNO']) !="" )? trim($request['GSTINNO']) : NULL ;
        $CINNO             =   (isset($request['CINNO']) && trim($request['CINNO']) !="" )? trim($request['CINNO']) : NULL ;
        $PANNO             =   (isset($request['PANNO']) && trim($request['PANNO']) !="" )? trim($request['PANNO']) : NULL ;
        $REGADDL1             =   (isset($request['REGADDL1']) && trim($request['REGADDL1']) !="" )? trim($request['REGADDL1']) : NULL ;
        $REGADDL2             =   (isset($request['REGADDL2']) && trim($request['REGADDL2']) !="" )? trim($request['REGADDL2']) : NULL ;
        $REGCTRYID_REF             =   (isset($request['REGCTRYID_REF']) && trim($request['REGCTRYID_REF']) !="" )? trim($request['REGCTRYID_REF']) : NULL ;
        $REGSTID_REF             =   (isset($request['REGSTID_REF']) && trim($request['REGSTID_REF']) !="" )? trim($request['REGSTID_REF']) : NULL ;
        $REGCITYID_REF             =   (isset($request['REGCITYID_REF']) && trim($request['REGCITYID_REF']) !="" )? trim($request['REGCITYID_REF']) : NULL ;
        $REGPINCODE             =   (isset($request['REGPINCODE']) && trim($request['REGPINCODE']) !="" )? trim($request['REGPINCODE']) : NULL ;
        $REGLM             =   (isset($request['REGLM']) && trim($request['REGLM']) !="" )? trim($request['REGLM']) : NULL ;
        $CORPADDL1             =   (isset($request['CORPADDL1']) && trim($request['CORPADDL1']) !="" )? trim($request['CORPADDL1']) : NULL ;
        $CORPADDL2             =   (isset($request['CORPADDL2']) && trim($request['CORPADDL2']) !="" )? trim($request['CORPADDL2']) : NULL ;
        $CORPCTRYID_REF             =   (isset($request['CORPCTRYID_REF']) && trim($request['CORPCTRYID_REF']) !="" )? trim($request['CORPCTRYID_REF']) : NULL ;
        $CORPSTID_REF             =   (isset($request['CORPSTID_REF']) && trim($request['CORPSTID_REF']) !="" )? trim($request['CORPSTID_REF']) : NULL ;
        $CORPCITYID_REF             =   (isset($request['CORPCITYID_REF']) && trim($request['CORPCITYID_REF']) !="" )? trim($request['CORPCITYID_REF']) : NULL ;
        $CORPPINCODE             =   (isset($request['CORPPINCODE']) && trim($request['CORPPINCODE']) !="" )? trim($request['CORPPINCODE']) : NULL ;
        $CORPLM             =   (isset($request['CORPLM']) && trim($request['CORPLM']) !="" )? trim($request['CORPLM']) : NULL ;
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
        $CRID_REF             =   (isset($request['CRID_REF']) && trim($request['CRID_REF']) !="" )? trim($request['CRID_REF']) : NULL ;
        $MSME_NO             =   (isset($request['MSME_NO']) && trim($request['MSME_NO']) !="" )? trim($request['MSME_NO']) : NULL ;
        $FACTORY_ACT_NO             =   (isset($request['FACTORY_ACT_NO']) && trim($request['FACTORY_ACT_NO']) !="" )? trim($request['FACTORY_ACT_NO']) : NULL ;
        $LOGO           =NULL;

        $SAP_CODE               =   trim($request['SAP_CODE']); 
        $ALPS_REFNO               =   trim($request['ALPS_REFNO']); 

        $r_count4 = $request['Row_Count4'];
        $udffield_Data = [];
        for ($i=0; $i<=$r_count4; $i++){
            if(isset( $request['udffie_'.$i])){
                $udffield_Data[$i]['UDFCOID_REF'] = $request['udffie_'.$i]; 
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

        // Logo Upload
        $allow_extnesions   =   explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size         =   config("erpconst.attachments.max_size") * 1020 * 1024;
        $ATTACH_DOCNO       =   ""; 
        $ATTACH_DOCDT       =   ""; 

		//$destinationPath    =   storage_path()."/docs/company".$CYID_REF."/CompanyMaster/Logo";
        $image_path         =   "docs/company".$CYID_REF."/CompanyMaster/Logo";     
        $destinationPath    =   str_replace('\\', '/', public_path($image_path));
		
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

                        $filename   = $image_path."/".$filenametostore;

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

        $array_data   = [
                        $CYCODE, $NAME,
                        $GSTINNO, $CINNO, $PANNO, $REGADDL1,$REGADDL2,
                        $REGCTRYID_REF, $REGSTID_REF, $REGCITYID_REF, $REGPINCODE,$REGLM,
                        $CORPADDL1, $CORPADDL2, $CORPCTRYID_REF, $CORPSTID_REF,$CORPCITYID_REF,
                        $CORPPINCODE, $CORPLM, $EMAILID, $PHNO,$MONO,
                        $WEBSITE, $SKYPEID, $AUTHPNAME, $AUTHPDESG,$INDSID_REF,
                        $INDSVID_REF, $DEALSIN, $GSTTYPE, $CRID_REF,$MSME_NO,
                        $FACTORY_ACT_NO,$LOGO,$DEACTIVATED, $DODEACTIVATED,$SAP_CODE,
                        $ALPS_REFNO,$XMLUDF, 
                        $CYID_REF, $BRID_REF,$FYID_REF, $VTID, $USERID, 
                        $UPDATE,$UPTIME, $ACTION, $IPADDRESS
                    ];


        try {

            $sp_result = DB::select('EXEC SP_COMPANY_IN ?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?,?', $array_data);
        
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

            $objResponse = TblMstFrm146::where('CYID','=',$id)->first();
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
            
            $objUDF = DB::table('TBL_MST_COMPANY_UDF')                    
            ->where('TBL_MST_COMPANY_UDF.CYID_REF','=',$id)
            ->leftJoin('TBL_MST_UDFFOR_CO','TBL_MST_UDFFOR_CO.UDFCOID','=','TBL_MST_COMPANY_UDF.UDFCOID_REF')                
            ->select('TBL_MST_COMPANY_UDF.*','TBL_MST_UDFFOR_CO.*')
            ->orderBy('TBL_MST_COMPANY_UDF.CY_UDFID','ASC')
            ->get()->toArray();
            $objudfCount = count($objUDF);                
            if($objudfCount==0){
                $objudfCount=1;
            }
   

            $objRegCountryName =   $this->getCountryName($objResponse->REGCTRYID_REF);
            $objRegStateName   =   $this->getStateName($objResponse->REGSTID_REF);
            $objRegCityName    =   $this->getCityName($objResponse->REGCITYID_REF);

            $objCorCountryName =   $this->getCountryName($objResponse->CORPCTRYID_REF);
            $objCorStateName   =   $this->getStateName($objResponse->CORPSTID_REF);
            $objCorCityName    =   $this->getCityName($objResponse->CORPCITYID_REF);

            $objIndtypeName   =   $this->getIndtypeName($objResponse->INDSID_REF);
            $objIndVerName    =   $this->getIndVerName($objResponse->INDSVID_REF);

            return view('masters.Common.CompanyMaster.mstfrm146edit',compact(['objResponse','user_approval_level','objRights','objCountryList','objIndTypeList','objIndVerList','objGstTypeList','objCurrencyList','objRegCountryName','objRegStateName','objRegCityName','objCorCountryName','objCorStateName','objCorCityName','objIndtypeName','objIndVerName','objUDF','objudfCount']));
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

            'NAME' => 'required',  
            'REGADDL1' => 'required', 
            'REGCTRYID_REF' => 'required', 
            'REGSTID_REF' => 'required', 
            'REGCITYID_REF' => 'required', 
            'GSTTYPE' => 'required', 
            'CRID_REF' => 'required',     
        ];

        $req_update_data = [

            'NAME' =>   $request['NAME'],
            'CYCODE'     =>    $request['CYCODE'],
            'NAME' =>   $request['NAME'],
            'REGADDL1' =>   $request['REGADDL1'],
            'REGCTRYID_REF' =>   $request['REGCTRYID_REF'],
            'REGSTID_REF' =>   $request['REGSTID_REF'],
            'REGCITYID_REF' =>   $request['REGCITYID_REF'],
            'GSTTYPE' =>   $request['GSTTYPE'],
            'CRID_REF' =>   $request['CRID_REF']
        ]; 

       
        $validator = Validator::make( $req_update_data, $update_rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }
    
        $CYCODE       =   strtoupper(trim($request['CYCODE']) );
        $NAME   =   trim($request['NAME']); 

        $GSTINNO             =   (isset($request['GSTINNO']) && trim($request['GSTINNO']) !="" )? trim($request['GSTINNO']) : NULL ;
        $CINNO             =   (isset($request['CINNO']) && trim($request['CINNO']) !="" )? trim($request['CINNO']) : NULL ;
        $PANNO             =   (isset($request['PANNO']) && trim($request['PANNO']) !="" )? trim($request['PANNO']) : NULL ;
        $REGADDL1             =   (isset($request['REGADDL1']) && trim($request['REGADDL1']) !="" )? trim($request['REGADDL1']) : NULL ;
        $REGADDL2             =   (isset($request['REGADDL2']) && trim($request['REGADDL2']) !="" )? trim($request['REGADDL2']) : NULL ;
        $REGCTRYID_REF             =   (isset($request['REGCTRYID_REF']) && trim($request['REGCTRYID_REF']) !="" )? trim($request['REGCTRYID_REF']) : NULL ;
        $REGSTID_REF             =   (isset($request['REGSTID_REF']) && trim($request['REGSTID_REF']) !="" )? trim($request['REGSTID_REF']) : NULL ;
        $REGCITYID_REF             =   (isset($request['REGCITYID_REF']) && trim($request['REGCITYID_REF']) !="" )? trim($request['REGCITYID_REF']) : NULL ;
        $REGPINCODE             =   (isset($request['REGPINCODE']) && trim($request['REGPINCODE']) !="" )? trim($request['REGPINCODE']) : NULL ;
        $REGLM             =   (isset($request['REGLM']) && trim($request['REGLM']) !="" )? trim($request['REGLM']) : NULL ;
        $CORPADDL1             =   (isset($request['CORPADDL1']) && trim($request['CORPADDL1']) !="" )? trim($request['CORPADDL1']) : NULL ;
        $CORPADDL2             =   (isset($request['CORPADDL2']) && trim($request['CORPADDL2']) !="" )? trim($request['CORPADDL2']) : NULL ;
        $CORPCTRYID_REF             =   (isset($request['CORPCTRYID_REF']) && trim($request['CORPCTRYID_REF']) !="" )? trim($request['CORPCTRYID_REF']) : NULL ;
        $CORPSTID_REF             =   (isset($request['CORPSTID_REF']) && trim($request['CORPSTID_REF']) !="" )? trim($request['CORPSTID_REF']) : NULL ;
        $CORPCITYID_REF             =   (isset($request['CORPCITYID_REF']) && trim($request['CORPCITYID_REF']) !="" )? trim($request['CORPCITYID_REF']) : NULL ;
        $CORPPINCODE             =   (isset($request['CORPPINCODE']) && trim($request['CORPPINCODE']) !="" )? trim($request['CORPPINCODE']) : NULL ;
        $CORPLM             =   (isset($request['CORPLM']) && trim($request['CORPLM']) !="" )? trim($request['CORPLM']) : NULL ;
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
        $CRID_REF             =   (isset($request['CRID_REF']) && trim($request['CRID_REF']) !="" )? trim($request['CRID_REF']) : NULL ;
        $MSME_NO             =   (isset($request['MSME_NO']) && trim($request['MSME_NO']) !="" )? trim($request['MSME_NO']) : NULL ;
        $FACTORY_ACT_NO             =   (isset($request['FACTORY_ACT_NO']) && trim($request['FACTORY_ACT_NO']) !="" )? trim($request['FACTORY_ACT_NO']) : NULL ;
        $LOGO           =   (isset($request['HID_LOGO']) && trim($request['HID_LOGO']) !="" )? trim($request['HID_LOGO']) : NULL ;
        $SAP_CODE               =   trim($request['SAP_CODE']); 
        $ALPS_REFNO               =   trim($request['ALPS_REFNO']); 


        $r_count4 = $request['Row_Count4'];
        $udffield_Data = [];
        for ($i=0; $i<=$r_count4; $i++){
            
            if(isset( $request['udffie_'.$i])){
                $udffield_Data[$i]['UDFCOID_REF'] = $request['udffie_'.$i]; 
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

        // Logo Upload
        $allow_extnesions   =   explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size         =   config("erpconst.attachments.max_size") * 1020 * 1024;
        $ATTACH_DOCNO       =   ""; 
        $ATTACH_DOCDT       =   ""; 

		//$destinationPath    =   storage_path()."/docs/company".$CYID_REF."/CompanyMaster/Logo";

        $image_path         =   "docs/company".$CYID_REF."/CompanyMaster/Logo";     
        $destinationPath    =   str_replace('\\', '/', public_path($image_path));
		
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

                        $filename   = $image_path."/".$filenametostore;

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
        
        $array_data   = [
            $CYCODE, $NAME,
            $GSTINNO, $CINNO, $PANNO, $REGADDL1,$REGADDL2,
            $REGCTRYID_REF, $REGSTID_REF, $REGCITYID_REF, $REGPINCODE,$REGLM,
            $CORPADDL1, $CORPADDL2, $CORPCTRYID_REF, $CORPSTID_REF,$CORPCITYID_REF,
            $CORPPINCODE, $CORPLM, $EMAILID, $PHNO,$MONO,
            $WEBSITE, $SKYPEID, $AUTHPNAME, $AUTHPDESG,$INDSID_REF,
            $INDSVID_REF, $DEALSIN, $GSTTYPE, $CRID_REF,$MSME_NO,
            $FACTORY_ACT_NO,$LOGO,$DEACTIVATED, $DODEACTIVATED,$SAP_CODE,
            $ALPS_REFNO,$XMLUDF, 
            $CYID_REF, $BRID_REF,$FYID_REF, $VTID, $USERID, 
            $UPDATE,$UPTIME, $ACTION, $IPADDRESS
        ];

        try {

        $sp_result = DB::select('EXEC SP_COMPANY_UP ?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?,?', $array_data);

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
        
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/CompanyMaster";
		
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
            return redirect()->route("master",[146,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
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
        
        //    return redirect()->route("master",[146,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");
    
      //  }
     
        if($sp_result[0]->RESULT=="SUCCESS"){

            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }

            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }

            return redirect()->route("master",[146,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[146,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("master",[146,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
            return redirect()->route("master",[146,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   

  


   



    //singleApprove begin
    public function singleapprove(Request $request)
    {
      
         $approv_rules = [
 
            'NAME' => 'required',  
            'REGADDL1' => 'required', 
            'REGCTRYID_REF' => 'required', 
            'REGSTID_REF' => 'required', 
            'REGCITYID_REF' => 'required', 
            'GSTTYPE' => 'required', 
            'CRID_REF' => 'required',         
         ];
 
         $req_approv_data = [
 
            'NAME' =>   $request['NAME'],
            'CYCODE'     =>    $request['CYCODE'],
            'NAME' =>   $request['NAME'],
            'REGADDL1' =>   $request['REGADDL1'],
            'REGCTRYID_REF' =>   $request['REGCTRYID_REF'],
            'REGSTID_REF' =>   $request['REGSTID_REF'],
            'REGCITYID_REF' =>   $request['REGCITYID_REF'],
            'GSTTYPE' =>   $request['GSTTYPE'],
            'CRID_REF' =>   $request['CRID_REF']
         ]; 
 
 
         $validator = Validator::make( $req_approv_data, $approv_rules, $this->messages);
 
         if ($validator->fails())
         {
         return Response::json(['errors' => $validator->errors()]);	
         }
     
         $CYCODE       =   strtoupper(trim($request['CYCODE']) );
         $NAME   =   trim($request['NAME']);  


        $GSTINNO             =   (isset($request['GSTINNO']) && trim($request['GSTINNO']) !="" )? trim($request['GSTINNO']) : NULL ;
        $CINNO             =   (isset($request['CINNO']) && trim($request['CINNO']) !="" )? trim($request['CINNO']) : NULL ;
        $PANNO             =   (isset($request['PANNO']) && trim($request['PANNO']) !="" )? trim($request['PANNO']) : NULL ;
        $REGADDL1             =   (isset($request['REGADDL1']) && trim($request['REGADDL1']) !="" )? trim($request['REGADDL1']) : NULL ;
        $REGADDL2             =   (isset($request['REGADDL2']) && trim($request['REGADDL2']) !="" )? trim($request['REGADDL2']) : NULL ;
        $REGCTRYID_REF             =   (isset($request['REGCTRYID_REF']) && trim($request['REGCTRYID_REF']) !="" )? trim($request['REGCTRYID_REF']) : NULL ;
        $REGSTID_REF             =   (isset($request['REGSTID_REF']) && trim($request['REGSTID_REF']) !="" )? trim($request['REGSTID_REF']) : NULL ;
        $REGCITYID_REF             =   (isset($request['REGCITYID_REF']) && trim($request['REGCITYID_REF']) !="" )? trim($request['REGCITYID_REF']) : NULL ;
        $REGPINCODE             =   (isset($request['REGPINCODE']) && trim($request['REGPINCODE']) !="" )? trim($request['REGPINCODE']) : NULL ;
        $REGLM             =   (isset($request['REGLM']) && trim($request['REGLM']) !="" )? trim($request['REGLM']) : NULL ;
        $CORPADDL1             =   (isset($request['CORPADDL1']) && trim($request['CORPADDL1']) !="" )? trim($request['CORPADDL1']) : NULL ;
        $CORPADDL2             =   (isset($request['CORPADDL2']) && trim($request['CORPADDL2']) !="" )? trim($request['CORPADDL2']) : NULL ;
        $CORPCTRYID_REF             =   (isset($request['CORPCTRYID_REF']) && trim($request['CORPCTRYID_REF']) !="" )? trim($request['CORPCTRYID_REF']) : NULL ;
        $CORPSTID_REF             =   (isset($request['CORPSTID_REF']) && trim($request['CORPSTID_REF']) !="" )? trim($request['CORPSTID_REF']) : NULL ;
        $CORPCITYID_REF             =   (isset($request['CORPCITYID_REF']) && trim($request['CORPCITYID_REF']) !="" )? trim($request['CORPCITYID_REF']) : NULL ;
        $CORPPINCODE             =   (isset($request['CORPPINCODE']) && trim($request['CORPPINCODE']) !="" )? trim($request['CORPPINCODE']) : NULL ;
        $CORPLM             =   (isset($request['CORPLM']) && trim($request['CORPLM']) !="" )? trim($request['CORPLM']) : NULL ;
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
        $CRID_REF             =   (isset($request['CRID_REF']) && trim($request['CRID_REF']) !="" )? trim($request['CRID_REF']) : NULL ;
        $MSME_NO             =   (isset($request['MSME_NO']) && trim($request['MSME_NO']) !="" )? trim($request['MSME_NO']) : NULL ;
        $FACTORY_ACT_NO             =   (isset($request['FACTORY_ACT_NO']) && trim($request['FACTORY_ACT_NO']) !="" )? trim($request['FACTORY_ACT_NO']) : NULL ;
        $LOGO           =   (isset($request['HID_LOGO']) && trim($request['HID_LOGO']) !="" )? trim($request['HID_LOGO']) : NULL ;
        $SAP_CODE               =   trim($request['SAP_CODE']); 
        $ALPS_REFNO               =   trim($request['ALPS_REFNO']); 

        

        $r_count4 = $request['Row_Count4'];
        $udffield_Data = [];
        for ($i=0; $i<=$r_count4; $i++){
            
            if(isset( $request['udffie_'.$i])){
                $udffield_Data[$i]['UDFCOID_REF'] = $request['udffie_'.$i]; 
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


         // Logo Upload
        $allow_extnesions   =   explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size         =   config("erpconst.attachments.max_size") * 1020 * 1024;
        $ATTACH_DOCNO       =   ""; 
        $ATTACH_DOCDT       =   ""; 

		//$destinationPath    =   storage_path()."/docs/company".$CYID_REF."/CompanyMaster/Logo";
        $image_path         =   "docs/company".$CYID_REF."/CompanyMaster/Logo";     
        $destinationPath    =   str_replace('\\', '/', public_path($image_path));
		
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

                        $filename   = $image_path."/".$filenametostore;

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
         
       
            $array_data   = [
                $CYCODE, $NAME,
                $GSTINNO, $CINNO, $PANNO, $REGADDL1,$REGADDL2,
                $REGCTRYID_REF, $REGSTID_REF, $REGCITYID_REF, $REGPINCODE,$REGLM,
                $CORPADDL1, $CORPADDL2, $CORPCTRYID_REF, $CORPSTID_REF,$CORPCITYID_REF,
                $CORPPINCODE, $CORPLM, $EMAILID, $PHNO,$MONO,
                $WEBSITE, $SKYPEID, $AUTHPNAME, $AUTHPDESG,$INDSID_REF,
                $INDSVID_REF, $DEALSIN, $GSTTYPE, $CRID_REF,$MSME_NO,
                $FACTORY_ACT_NO,$LOGO,$DEACTIVATED, $DODEACTIVATED,$SAP_CODE,
                $ALPS_REFNO,$XMLUDF, 
                $CYID_REF, $BRID_REF,$FYID_REF, $VTID, $USERID, 
                $UPDATE,$UPTIME, $ACTION, $IPADDRESS
             ];

        try {

        $sp_result = DB::select('EXEC SP_COMPANY_UP ?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?,?', $array_data);

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
            $objResponse = TblMstFrm146::where('CYID','=',$id)->first();

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

            

            $objUDF = DB::table('TBL_MST_COMPANY_UDF')                    
            ->where('TBL_MST_COMPANY_UDF.CYID_REF','=',$id)
            ->leftJoin('TBL_MST_UDFFOR_CO','TBL_MST_UDFFOR_CO.UDFCOID','=','TBL_MST_COMPANY_UDF.UDFCOID_REF')                
            ->select('TBL_MST_COMPANY_UDF.*','TBL_MST_UDFFOR_CO.*')
            ->orderBy('TBL_MST_COMPANY_UDF.CY_UDFID','ASC')
            ->get()->toArray();
            $objudfCount = count($objUDF);                
            if($objudfCount==0){
                $objudfCount=1;
            }
   

            $objRegCountryName =   $this->getCountryName($objResponse->REGCTRYID_REF);
            $objRegStateName   =   $this->getStateName($objResponse->REGSTID_REF);
            $objRegCityName    =   $this->getCityName($objResponse->REGCITYID_REF);

            $objCorCountryName =   $this->getCountryName($objResponse->CORPCTRYID_REF);
            $objCorStateName   =   $this->getStateName($objResponse->CORPSTID_REF);
            $objCorCityName    =   $this->getCityName($objResponse->CORPCITYID_REF);

            $objIndtypeName   =   $this->getIndtypeName($objResponse->INDSID_REF);
            $objIndVerName    =   $this->getIndVerName($objResponse->INDSVID_REF);


            return view('masters.Common.CompanyMaster.mstfrm146view',compact(['objResponse','objRegCountryName','objRegStateName','objRegCityName','objCorCountryName','objCorStateName','objCorCityName','objIndtypeName','objIndVerName','objUDF','objudfCount','objGstTypeList','objCurrencyList']));
        }

    }
  
    public function printdata(Request $request){
        
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }

        $objResponse = TblMstFrm146::whereIn('CYID',$ids_data)->get();
        
        return view('masters.Common.CompanyMaster.mstfrm146print',compact(['objResponse']));
   }


   
    

    
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objResponse = TblMstFrm146::where('CYID','=',$id)->first();

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
                        ->where('TBL_MST_ATTACHMENT.FYID_REF','=',Session::get('FYID_REF'))
                        ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                        ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                        ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                        ->get()->toArray();

                 // dump( $objAttachments);

            return view('masters.Common.CompanyMaster.mstfrm146attachment',compact(['objResponse','objMstVoucherType','objAttachments']));
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
            $TABLE      =   "TBL_MST_COMPANY";
            $FIELD      =   "CYID";
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
        $TABLE      =   "TBL_MST_COMPANY";
        $FIELD      =   "CYID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
        
        $cancelData[0]= ['NT' =>'TBL_MST_COMPANY_UDF'];
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
