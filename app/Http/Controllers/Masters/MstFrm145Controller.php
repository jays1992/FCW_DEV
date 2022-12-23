<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm145;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;


class MstFrm145Controller extends Controller
{
   
    protected $form_id = 145;
    protected $vtid_ref   = 118;  //voucher type id

    //validation messages
    protected   $messages = [
                    'VTID_REF.required' => 'Required field',
                    'VTID_REF.unique' => 'Duplicate Code',
                    'EFFECTIVE_DT.required' => 'Required field'
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

        $objDataList    =   DB::select("SELECT T1.DOCNODEFIID,T1.EFFECTIVE_DT,T1.INDATE,T1.DEACTIVATED,T1.DODEACTIVATED,T1.STATUS,T2.VCODE,T2.DESCRIPTIONS
        FROM TBL_MST_DOCNO_DEFINITION T1
        LEFT JOIN TBL_MST_VOUCHERTYPE T2 ON T1.VTID_REF=T2.VTID
        WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.FYID_REF='$FYID_REF'
        ");

        return view('masters.Common.DocumentNumberDefinition.mstfrm145',compact(['objRights','objDataList']));

    }

    public function getlisting(Request $request){
        $columns = array( 
            0 =>'NO', 
            1 =>'VTID_REF',
            2 =>'EFFECTIVE_DT',
            3 =>'STATUS',
        );  

        $COL_APP_STATUS =   'STATUS';  //never change value, value must be 'STATUS' as per stored procedure;
    
        $USERID_REF =   Auth::user()->USERID;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        
      


        $TABLE1     =   "TBL_MST_DOCNO_DEFINITION";
        $PK_COL     =   "DOCNODEFIID";
        $SELECT_COL =   "DOCNODEFIID,VTID_REF,EFFECTIVE_DT";    

        $WHERE_COL     =   " ";

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');


        if(!empty($request->input('search.value')))
        {

            $search_text = $request->input('search.value'); 
            $filtercolumn = $request->input('filtercolumn');

            $search_text = "'". $search_text ."'";
           
            if($filtercolumn =='ALL'){

                $WHERE_COL =  " WHERE VTID_REF LIKE  ". $search_text;
                $WHERE_COL =  $WHERE_COL.  " OR  EFFECTIVE_DT LIKE  ". $search_text;

            }else{

                $WHERE_COL =  " WHERE  ".$filtercolumn." LIKE ". $search_text;

            }         
            
        }
        
        $ORDER_BY_COL   =  $order. " ". $dir;
        $OFFSET_COL     =   " offset ".$start." rows fetch next ".$limit." rows only ";

        $sp_listing_data = [
            $USERID_REF,  $CYID_REF,$BRID_REF,
            $FYID_REF, $TABLE1, $PK_COL,
            $SELECT_COL,$WHERE_COL, $ORDER_BY_COL, $OFFSET_COL

        ];
        
        $sp_listing_result = DB::select('EXEC SP_LISTINGDATA ?,?,?,?, ?,?,?,?, ?,?', $sp_listing_data);

        $totalRows = 0;       //total no of records
        $totalFiltered = 0;   // total filtered count

        $data = array();
        
        if(!empty($sp_listing_result))
        {
            foreach ($sp_listing_result as $key=>$valueitem)
            {
                $totalRows      = $valueitem->TotalRows;
                $totalFiltered  = $valueitem->FilteredRows;

                if (!Empty($valueitem->STATUS) && $valueitem->STATUS=="Approved") 
                { $app_status = 1 ;} 
                elseif($valueitem->STATUS=="Cancel")
                { $app_status = 2 ;}
                else{ $app_status = 0 ;}
            

                $nestedData['NO']           = '<input type="checkbox" id="chkId'.$valueitem->DOCNODEFIID.'"  value="'.$valueitem->DOCNODEFIID.'" class="js-selectall1" data-rcdstatus="'.$app_status.'">';
                $nestedData['VTID_REF']         = $valueitem->VTID_REF;
                $nestedData['EFFECTIVE_DT']     = $valueitem->EFFECTIVE_DT;
                $nestedData['STATUS']           = $valueitem->STATUS;

                $data[] = $nestedData;
            }

        }
        $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalRows),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data   
        );            
        echo json_encode($json_data); 
        
        exit();                               
    }
    
    public function add(){ 

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');

        $objVtList = DB::select("select t1.VTID as VTID,t1.VCODE AS VTCODE,t1.DESCRIPTIONS AS VTDESCRIPTIONS from TBL_MST_VOUCHERTYPE t1
            inner join TBL_MST_MODULE_VOUCHER_MAP t2 on t1.VTID=t2.VTID_REF
            where t2.CYID_REF='$CYID_REF' and t2.[STATUS]='A' 
            and (t2.DEACTIVATED=0 or t2.DEACTIVATED is null)");

            return view('masters.Common.DocumentNumberDefinition.mstfrm145add',compact(['objVtList']));
    }

   public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $VTID_REF =   $request['VTID_REF'];
        
        $objLabel = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('VTID_REF','=',$VTID_REF)
        ->where('STATUS','!=','C')
        ->select('VTID_REF')
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
            'VTID_REF' => 'required',
            'EFFECTIVE_DT' => 'required',          
        ];

        $req_data = [

            'VTID_REF'     =>    $request['VTID_REF'],
            'EFFECTIVE_DT' =>   $request['EFFECTIVE_DT']
        ]; 


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }
 
        $VTID_REF           =   strtoupper(trim($request['VTID_REF']) );
        $EFFECTIVE_DT       =   trim($request['EFFECTIVE_DT']); 
        $DOC_TYPE           =   trim($request['DOC_TYPE']); 
        
        $MANUAL_SR          =   (isset($request['MANUAL_SR']) && trim($request['MANUAL_SR']) !="" )? trim($request['MANUAL_SR']) : 0 ;
        $SYSTEM_GRSR        =   (isset($request['SYSTEM_GRSR']) && trim($request['SYSTEM_GRSR']) !="" )? trim($request['SYSTEM_GRSR']) : 0 ;
        $MANUAL_MAXLENGTH   =   (isset($request['MANUAL_MAXLENGTH']) && trim($request['MANUAL_MAXLENGTH']) !="" )? trim($request['MANUAL_MAXLENGTH']) : NULL ;
        $PREFIX_RQ          =   (isset($request['PREFIX_RQ']) && trim($request['PREFIX_RQ']) !="" )? trim($request['PREFIX_RQ']) : 0 ;
        $PREFIX             =   (isset($request['PREFIX']) && trim($request['PREFIX']) !="" )? trim($request['PREFIX']) : NULL ;
        $PRE_SEP_RQ         =   (isset($request['PRE_SEP_RQ']) && trim($request['PRE_SEP_RQ']) !="" )? trim($request['PRE_SEP_RQ']) : 0 ;
        $PRE_SEP_SLASH      =   (isset($request['PRE_SEP_SLASH']) && trim($request['PRE_SEP_SLASH']) !="" )? addslashes(trim($request['PRE_SEP_SLASH'])) : 0 ;
        $PRE_SEP_HYPEN      =   (isset($request['PRE_SEP_HYPEN']) && trim($request['PRE_SEP_HYPEN']) !="" )? addslashes(trim($request['PRE_SEP_HYPEN'])) : 0 ;
        $NO_MAX             =   (isset($request['NO_MAX']) && trim($request['NO_MAX']) !="" )? trim($request['NO_MAX']) : NULL ;
        $NO_START           =   (isset($request['NO_START']) && trim($request['NO_START']) !="" )? trim($request['NO_START']) : NULL ;
        $NEWNO_FYEAR        =   (isset($request['NEWNO_FYEAR']) && trim($request['NEWNO_FYEAR']) !="" )? trim($request['NEWNO_FYEAR']) : 0 ;
        $NO_SEP_RQ          =   (isset($request['NO_SEP_RQ']) && trim($request['NO_SEP_RQ']) !="" )? trim($request['NO_SEP_RQ']) : 0 ;
        $NO_SEP_SLASH       =   (isset($request['NO_SEP_SLASH']) && trim($request['NO_SEP_SLASH']) !="" )? addslashes(trim($request['NO_SEP_SLASH'])) : 0 ;
        $NO_SEP_HYPEN       =   (isset($request['NO_SEP_HYPEN']) && trim($request['NO_SEP_HYPEN']) !="" )? addslashes(trim($request['NO_SEP_HYPEN'])) : 0 ;
        $SUFFIX_RQ          =   (isset($request['SUFFIX_RQ']) && trim($request['SUFFIX_RQ']) !="" )? trim($request['SUFFIX_RQ']) : 0 ;
        $SUFFIX             =   (isset($request['SUFFIX']) && trim($request['SUFFIX']) !="" )? trim($request['SUFFIX']) : NULL ;
        $DOC_SERIES_TYPE    =   (isset($request['DOC_SERIES_TYPE']) && trim($request['DOC_SERIES_TYPE']) !="" )? trim($request['DOC_SERIES_TYPE']) : 'YEAR' ;

        $PREFIX_TYPE        =   (isset($request['PREFIX_TYPE']) && trim($request['PREFIX_TYPE']) !="" )? trim($request['PREFIX_TYPE']) : NULL ;

        
        $DEACTIVATED    =   NULL;  
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
                        $VTID_REF, $EFFECTIVE_DT,
                        $MANUAL_SR, $SYSTEM_GRSR, $MANUAL_MAXLENGTH,
                        $PREFIX_RQ, $PREFIX, $PRE_SEP_RQ, $PRE_SEP_SLASH,
                        $PRE_SEP_HYPEN, $NO_MAX, $NO_START, $NEWNO_FYEAR,
                        $NO_SEP_RQ, $NO_SEP_SLASH, $NO_SEP_HYPEN, $SUFFIX_RQ,$SUFFIX,
                        $DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF,
                        $FYID_REF, $VTID, $USERID, $UPDATE,
                        $UPTIME, $ACTION, $IPADDRESS,$DOC_TYPE,$DOC_SERIES_TYPE,$PREFIX_TYPE
                    ];

        try {

            $sp_result = DB::select('EXEC SP_DOCUMENTNUMBER_DEFINITION_IN ?,?, ?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?,?, ?', $array_data);
        
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

            $objResponse = TblMstFrm145::where('DOCNODEFIID','=',$id)->first();
            if(strtoupper($objResponse->STATUS)=="A" || strtoupper($objResponse->STATUS)=="C"){
                exit("Sorry, Only Un Approved record can edit.");
             }
            

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first(); 



            $objVtList = DB::select("select t1.VTID as VTID,t1.VCODE AS VTCODE,t1.DESCRIPTIONS AS VTDESCRIPTIONS from TBL_MST_VOUCHERTYPE t1
            inner join TBL_MST_MODULE_VOUCHER_MAP t2 on t1.VTID=t2.VTID_REF
            where t2.CYID_REF='$CYID_REF' and t2.[STATUS]='A' 
            and (t2.DEACTIVATED=0 or t2.DEACTIVATED is null)");


            $objVtName = DB::table('TBL_MST_VOUCHERTYPE')
            ->where('STATUS','=','A')
            ->where('VTID','=',$objResponse->VTID_REF)
            ->select('VCODE','DESCRIPTIONS')
            ->first();

            

            
            return view('masters.Common.DocumentNumberDefinition.mstfrm145edit',compact(['objResponse','user_approval_level','objRights','objVtList','objVtName']));
        }

    }

     
    public function update(Request $request)
    {

      // dd($request->all());

        $update_rules = [

            'EFFECTIVE_DT' => 'required'       
        ];

        $req_update_data = [

            'EFFECTIVE_DT' =>   $request['EFFECTIVE_DT']
        ]; 

       
        $validator = Validator::make( $req_update_data, $update_rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }
    
        $VTID_REF           =   strtoupper(trim($request['VTID_REF']) );
        $EFFECTIVE_DT       =   trim($request['EFFECTIVE_DT']); 
        $DOC_TYPE           =   trim($request['DOC_TYPE']);

        $MANUAL_SR          =   (isset($request['MANUAL_SR']) && trim($request['MANUAL_SR']) !="" )? trim($request['MANUAL_SR']) : 0 ;
        $SYSTEM_GRSR        =   (isset($request['SYSTEM_GRSR']) && trim($request['SYSTEM_GRSR']) !="" )? trim($request['SYSTEM_GRSR']) : 0 ;
        $MANUAL_MAXLENGTH   =   (isset($request['MANUAL_MAXLENGTH']) && trim($request['MANUAL_MAXLENGTH']) !="" )? trim($request['MANUAL_MAXLENGTH']) : NULL ;
        $PREFIX_RQ          =   (isset($request['PREFIX_RQ']) && trim($request['PREFIX_RQ']) !="" )? trim($request['PREFIX_RQ']) : 0 ;
        $PREFIX             =   (isset($request['PREFIX']) && trim($request['PREFIX']) !="" )? trim($request['PREFIX']) : NULL ;
        $PRE_SEP_RQ         =   (isset($request['PRE_SEP_RQ']) && trim($request['PRE_SEP_RQ']) !="" )? trim($request['PRE_SEP_RQ']) : 0 ;
        $PRE_SEP_SLASH      =   (isset($request['PRE_SEP_SLASH']) && trim($request['PRE_SEP_SLASH']) !="" )? addslashes(trim($request['PRE_SEP_SLASH'])) : 0 ;
        $PRE_SEP_HYPEN      =   (isset($request['PRE_SEP_HYPEN']) && trim($request['PRE_SEP_HYPEN']) !="" )? addslashes(trim($request['PRE_SEP_HYPEN'])) : 0 ;
        $NO_MAX             =   (isset($request['NO_MAX']) && trim($request['NO_MAX']) !="" )? trim($request['NO_MAX']) : NULL ;
        $NO_START           =   (isset($request['NO_START']) && trim($request['NO_START']) !="" )? trim($request['NO_START']) : NULL ;
        $NEWNO_FYEAR        =   (isset($request['NEWNO_FYEAR']) && trim($request['NEWNO_FYEAR']) !="" )? trim($request['NEWNO_FYEAR']) : 0 ;
        $NO_SEP_RQ          =   (isset($request['NO_SEP_RQ']) && trim($request['NO_SEP_RQ']) !="" )? trim($request['NO_SEP_RQ']) : 0 ;
        $NO_SEP_SLASH       =   (isset($request['NO_SEP_SLASH']) && trim($request['NO_SEP_SLASH']) !="" )? addslashes(trim($request['NO_SEP_SLASH'])) : 0 ;
        $NO_SEP_HYPEN       =   (isset($request['NO_SEP_HYPEN']) && trim($request['NO_SEP_HYPEN']) !="" )? addslashes(trim($request['NO_SEP_HYPEN'])) : 0 ;
        $SUFFIX_RQ          =   (isset($request['SUFFIX_RQ']) && trim($request['SUFFIX_RQ']) !="" )? trim($request['SUFFIX_RQ']) : 0 ;
        $SUFFIX             =   (isset($request['SUFFIX']) && trim($request['SUFFIX']) !="" )? trim($request['SUFFIX']) : NULL ;
        $DOC_SERIES_TYPE    =   (isset($request['DOC_SERIES_TYPE']) && trim($request['DOC_SERIES_TYPE']) !="" )? trim($request['DOC_SERIES_TYPE']) : 'YEAR' ;
       
        $PREFIX_TYPE        =   (isset($request['PREFIX_TYPE']) && trim($request['PREFIX_TYPE']) !="" )? trim($request['PREFIX_TYPE']) : NULL ;


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
                        $VTID_REF, $EFFECTIVE_DT,
                        $MANUAL_SR, $SYSTEM_GRSR, $MANUAL_MAXLENGTH,
                        $PREFIX_RQ, $PREFIX, $PRE_SEP_RQ, $PRE_SEP_SLASH,
                        $PRE_SEP_HYPEN, $NO_MAX, $NO_START, $NEWNO_FYEAR,
                        $NO_SEP_RQ, $NO_SEP_SLASH, $NO_SEP_HYPEN, $SUFFIX_RQ,$SUFFIX,
                        $DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF,
                        $FYID_REF, $VTID, $USERID, $UPDATE,
                        $UPTIME, $ACTION, $IPADDRESS,$DOC_TYPE,$DOC_SERIES_TYPE,$PREFIX_TYPE
        ];

        try {

        $sp_result = DB::select('EXEC SP_DOCUMENTNUMBER_DEFINITION_UP ?,?, ?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?,? ,?', $array_data);

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
        
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/DocumentNumberDefinition";
		
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
            return redirect()->route("master",[145,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
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
        
        //    return redirect()->route("master",[145,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");
    
      //  }
     
        if($sp_result[0]->RESULT=="SUCCESS"){

            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }

            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }

            return redirect()->route("master",[145,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[145,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("master",[145,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
            return redirect()->route("master",[145,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   

  


   



    //singleApprove begin
    public function singleapprove(Request $request)
    {
      
         $approv_rules = [
 
             'EFFECTIVE_DT' => 'required',          
         ];
 
         $req_approv_data = [
 
             'EFFECTIVE_DT' =>   $request['EFFECTIVE_DT']
         ]; 
 
 
         $validator = Validator::make( $req_approv_data, $approv_rules, $this->messages);
 
         if ($validator->fails())
         {
         return Response::json(['errors' => $validator->errors()]);	
         }
     
        $VTID_REF          =   strtoupper(trim($request['VTID_REF']) );
        $EFFECTIVE_DT      =   trim($request['EFFECTIVE_DT']);  
        $DOC_TYPE          =   trim($request['DOC_TYPE']);  

        $MANUAL_SR          =   (isset($request['MANUAL_SR']) && trim($request['MANUAL_SR']) !="" )? trim($request['MANUAL_SR']) : 0 ;
        $SYSTEM_GRSR        =   (isset($request['SYSTEM_GRSR']) && trim($request['SYSTEM_GRSR']) !="" )? trim($request['SYSTEM_GRSR']) : 0 ;
        $MANUAL_MAXLENGTH   =   (isset($request['MANUAL_MAXLENGTH']) && trim($request['MANUAL_MAXLENGTH']) !="" )? trim($request['MANUAL_MAXLENGTH']) : NULL ;
        $PREFIX_RQ          =   (isset($request['PREFIX_RQ']) && trim($request['PREFIX_RQ']) !="" )? trim($request['PREFIX_RQ']) : 0 ;
        $PREFIX             =   (isset($request['PREFIX']) && trim($request['PREFIX']) !="" )? trim($request['PREFIX']) : NULL ;
        $PRE_SEP_RQ         =   (isset($request['PRE_SEP_RQ']) && trim($request['PRE_SEP_RQ']) !="" )? trim($request['PRE_SEP_RQ']) : 0 ;
        $PRE_SEP_SLASH      =   (isset($request['PRE_SEP_SLASH']) && trim($request['PRE_SEP_SLASH']) !="" )? addslashes(trim($request['PRE_SEP_SLASH'])) : 0 ;
        $PRE_SEP_HYPEN      =   (isset($request['PRE_SEP_HYPEN']) && trim($request['PRE_SEP_HYPEN']) !="" )? addslashes(trim($request['PRE_SEP_HYPEN'])) : 0 ;
        $NO_MAX             =   (isset($request['NO_MAX']) && trim($request['NO_MAX']) !="" )? trim($request['NO_MAX']) : NULL ;
        $NO_START           =   (isset($request['NO_START']) && trim($request['NO_START']) !="" )? trim($request['NO_START']) : NULL ;
        $NEWNO_FYEAR        =   (isset($request['NEWNO_FYEAR']) && trim($request['NEWNO_FYEAR']) !="" )? trim($request['NEWNO_FYEAR']) : 0 ;
        $NO_SEP_RQ          =   (isset($request['NO_SEP_RQ']) && trim($request['NO_SEP_RQ']) !="" )? trim($request['NO_SEP_RQ']) : 0 ;
        $NO_SEP_SLASH       =   (isset($request['NO_SEP_SLASH']) && trim($request['NO_SEP_SLASH']) !="" )? addslashes(trim($request['NO_SEP_SLASH'])) : 0 ;
        $NO_SEP_HYPEN       =   (isset($request['NO_SEP_HYPEN']) && trim($request['NO_SEP_HYPEN']) !="" )? addslashes(trim($request['NO_SEP_HYPEN'])) : 0 ;
        $SUFFIX_RQ          =   (isset($request['SUFFIX_RQ']) && trim($request['SUFFIX_RQ']) !="" )? trim($request['SUFFIX_RQ']) : 0 ;
        $SUFFIX             =   (isset($request['SUFFIX']) && trim($request['SUFFIX']) !="" )? trim($request['SUFFIX']) : NULL ;
        $DOC_SERIES_TYPE    =   (isset($request['DOC_SERIES_TYPE']) && trim($request['DOC_SERIES_TYPE']) !="" )? trim($request['DOC_SERIES_TYPE']) : 'YEAR' ;
		
		$PREFIX_TYPE        =   (isset($request['PREFIX_TYPE']) && trim($request['PREFIX_TYPE']) !="" )? trim($request['PREFIX_TYPE']) : NULL ;
       

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
                $VTID_REF, $EFFECTIVE_DT,
                $MANUAL_SR, $SYSTEM_GRSR, $MANUAL_MAXLENGTH,
                $PREFIX_RQ, $PREFIX, $PRE_SEP_RQ, $PRE_SEP_SLASH,
                $PRE_SEP_HYPEN, $NO_MAX, $NO_START, $NEWNO_FYEAR,
                $NO_SEP_RQ, $NO_SEP_SLASH, $NO_SEP_HYPEN, $SUFFIX_RQ,$SUFFIX,
                $DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF,
                $FYID_REF, $VTID, $USERID, $UPDATE,
                $UPTIME, $ACTION, $IPADDRESS,$DOC_TYPE,$DOC_SERIES_TYPE,$PREFIX_TYPE
             ];

        try {

        $sp_result = DB::select('EXEC SP_DOCUMENTNUMBER_DEFINITION_UP ?,?, ?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?,?,?', $array_data);

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
            $objResponse = TblMstFrm145::where('DOCNODEFIID','=',$id)->first();

            $objVtName = DB::table('TBL_MST_VOUCHERTYPE')
            ->where('STATUS','=','A')
            ->where('VTID','=',$objResponse->VTID_REF)
            ->select('VCODE','DESCRIPTIONS')
            ->first();


            return view('masters.Common.DocumentNumberDefinition.mstfrm145view',compact(['objResponse','objVtName']));
        }

    }
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }

        $objResponse = TblMstFrm145::whereIn('DOCNODEFIID',$ids_data)->get();
        
        return view('masters.Common.DocumentNumberDefinition.mstfrm145print',compact(['objResponse']));
   }//print


   
    

    
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objResponse = TblMstFrm145::where('DOCNODEFIID','=',$id)->first();

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

            return view('masters.Common.DocumentNumberDefinition.mstfrm145attachment',compact(['objResponse','objMstVoucherType','objAttachments']));
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
            $TABLE      =   "TBL_MST_DOCNO_DEFINITION";
            $FIELD      =   "DOCNODEFIID";
            $ACTIONNAME     = $Approvallevel;
            $UPDATE     =   Date('Y-m-d');
            $UPTIME     =   Date('h:i:s.u');
            $IPADDRESS  =   $request->getClientIp();
        
        
        
        dd($xml);
        
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
        $TABLE      =   "TBL_MST_DOCNO_DEFINITION";
        $FIELD      =   "DOCNODEFIID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();
        
        $canceldata[0]=[
            'NT'  => 'TBL_MST_DOCNO_DEFINITION',
       ];        
       $links["TABLES"] = $canceldata; 
       $cancelxml = ArrayToXml::convert($links);

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
