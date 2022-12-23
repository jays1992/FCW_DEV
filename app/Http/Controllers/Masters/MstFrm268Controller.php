<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Master\TblMstFrm268;
use App\Models\Admin\TblMstUser;
use Auth;
use DB;
use Session;
use Response;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class MstFrm268Controller extends Controller
{
   
    protected $form_id = 268;
    protected $vtid_ref   = 218;  //voucher type id

    //validation messages
    protected   $messages = [
                    'CTCODE.required' => 'Required field',
                    'CTCODE.unique' => 'Duplicate Code'
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

        $objDataList    =   DB::table('TBL_MST_COMPANY_HOLIDAY')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->get();
        
       return view('masters.Payroll.CompanyHoliday.mstfrm268',compact(['objDataList','objRights']));
        
    }

    public function getconditiontemplate(Request $request){

        

        $columns = array( 
            0 =>'NO',
            1 =>'TNC_CODE',
            2 =>'TNC_DESC',
            3 =>'DEACTIVATED',
            4 =>'DODEACTIVATED',
            5 =>'STATUS',
        );  
        

        $COL_APP_STATUS =   'STATUS';  //never change value, value must be 'APPROVED_STATUS' as per stored procedure;
      
            $USERID_REF    =   Auth::user()->USERID;
            $CYID_REF      =   Auth::user()->CYID_REF;
            $BRID_REF      =   Session::get('BRID_REF');
            $FYID_REF      =   Session::get('FYID_REF');       
            $TABLE1        =   "TBL_MST_TNC";
            $PK_COL        =   "TNCID";
            $SELECT_COL    =   "TNCID,TNC_CODE,TNC_DESC,DEACTIVATED,DODEACTIVATED";    
            $WHERE_COL     =   "";
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

          

            if(!empty($request->input('search.value')))
            {

                $search_text = $request->input('search.value'); 
                $filtercolumn = $request->input('filtercolumn');

                $search_text = "'". $search_text ."'";
                //ALL COLUMN
                if($filtercolumn =='ALL'){

                    $WHERE_COL =  " WHERE TNCID LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR TNC_CODE LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR TNC_DESC LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR DEACTIVATED LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR DODEACTIVATED LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR ".$COL_APP_STATUS." LIKE  ". $search_text;


                }else{

                    $WHERE_COL =  " WHERE ".$filtercolumn." LIKE ". $search_text;

                }         
                
            }
           
            $ORDER_BY_COL   =  $order. " ". $dir;
            $OFFSET_COL     =   " offset ".$start." rows fetch next ".$limit." rows only ";
           
            $sp_listing_data = [
                $USERID_REF, $CYID_REF,$BRID_REF, $FYID_REF, $TABLE1, $PK_COL,
                $SELECT_COL,$WHERE_COL, $ORDER_BY_COL, $OFFSET_COL

            ];

            
            
            $sp_listing_result = DB::select('EXEC SP_LISTINGDATA ?,?,?,?, ?,?,?,?, ?,?', $sp_listing_data);

            $totalRows = 0;       //total no of records
            $totalFiltered = 0;   // total filtered count

            $data = array();
            
            
            if(!empty($sp_listing_result))
            {
                foreach ($sp_listing_result as $key=>$conditiontemplateitem)
                {
                    $totalRows      = $conditiontemplateitem->TotalRows;
                    $totalFiltered  = $conditiontemplateitem->FilteredRows;

                    if (!Empty($conditiontemplateitem->STATUS) && $conditiontemplateitem->STATUS=="Approved") 
                    { $app_status = 1 ;} 
                    elseif($conditiontemplateitem->STATUS=="Cancel")
                    { $app_status = 2 ;}
                    else{ $app_status = 0 ;}

                    if (!Empty($conditiontemplateitem->DEACTIVATED) && $conditiontemplateitem->DEACTIVATED=="1") 
                    { $DEACTIVATED = "Yes" ;} 
                    else{ $DEACTIVATED = "No" ;}

                    $nestedData['NO']               = '<input type="checkbox" id="chkId'.$conditiontemplateitem->TNCID.'"  value="'.$conditiontemplateitem->TNCID.'" class="js-selectall1" data-rcdstatus="'.$app_status.'">';
                    $nestedData['TNC_CODE']         = strtoupper($conditiontemplateitem->TNC_CODE);
                    $nestedData['TNC_DESC']         = $conditiontemplateitem->TNC_DESC;
                    $nestedData['DEACTIVATED']      = $DEACTIVATED;
                    $nestedData['DODEACTIVATED']    = $conditiontemplateitem->DODEACTIVATED =="1900-01-01"?"":$conditiontemplateitem->DODEACTIVATED;
                    $nestedData['STATUS']           = $conditiontemplateitem->STATUS;
                     $data[] = $nestedData;
                    
                    
                }

            }
            // dd($data);
            $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalRows),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
            );            
            echo json_encode($json_data); 

            
            exit(); 

    }

    //uploads attachments files
    public function docuploads(Request $request){

        $formData = $request->all();

        $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size = config("erpconst.attachments.max_size") * 1020 * 1024;

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
        
        $destinationPath = storage_path()."/docs/company".$CYID_REF."/calculationtemplate";

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
                    
                    echo $filenametostore ;

                    if ($uploadedFile->isValid()) {

                        if(in_array($extension,$allow_extnesions)){
                            
                            if($filesize < $allow_size){

                                $custfilename = $destinationPath."/".$filenametostore;

                                if (!file_exists($custfilename)) {

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
            return redirect()->route("master",[268,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
        
          
       // try {

             //save data
             $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);

      //  } catch (\Throwable $th) {
        
        //    return redirect()->route("master",[4,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");
    
      //  }
     
        if($sp_result[0]->RESULT=="SUCCESS"){

            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }

            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }

            return redirect()->route("master",[268,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[268,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            return redirect()->route("master",[268,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   


    public function add(){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $objglcode = DB::table('TBL_MST_GENERALLEDGER')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('STATUS','=',$Status)
        ->select('TBL_MST_GENERALLEDGER.*')
        ->get()
        ->toArray();

        $objFnlyearList = DB::table('TBL_MST_YEAR')
        //->where('CYID_REF','=',Auth::user()->CYID_REF)
        //->where('BRID_REF','=',Session::get('BRID_REF'))
        //->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('YRID','YRCODE','YRDESCRIPTION')
        ->get();

        $objHldTypeList    =   DB::table('TBL_MST_HOLIDAY_TYPE')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->get();

        $objSON = DB::table('TBL_MST_DOCNO_DEFINITION')
            ->where('VTID_REF','=',$this->vtid_ref)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
           // ->where('FYID_REF','=',$FYID_REF)
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_DOCNO_DEFINITION.*')
            ->first();

            //dd($objSON);
            
        $objAutoGenNo='';
        if(!empty($objSON )){

            if($objSON->SYSTEM_GRSR == "1")
            {
                if($objSON->PREFIX_RQ == "1")
                {
                    $objAutoGenNo = $objSON->PREFIX;
                }        
                if($objSON->PRE_SEP_RQ == "1")
                {
                    if($objSON->PRE_SEP_SLASH == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'/';
                    }
                    if($objSON->PRE_SEP_HYPEN == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'-';
                    }
                }        
                if($objSON->NO_MAX)
                {   
                    $objAutoGenNo = $objAutoGenNo.str_pad($objSON->LAST_RECORDNO+1, $objSON->NO_MAX, "0", STR_PAD_LEFT);
                }
                
                if($objSON->NO_SEP_RQ == "1")
                {
                    if($objSON->NO_SEP_SLASH == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'/';
                    }
                    if($objSON->NO_SEP_HYPEN == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'-';
                    }
                }
                if($objSON->SUFFIX_RQ == "1")
                {
                    $objAutoGenNo = $objAutoGenNo.$objSON->SUFFIX;
                }
            }
        } 

        
        $AlpsStatus =   $this->AlpsStatus();
       
      return view('masters.Payroll.CompanyHoliday.mstfrm268add',compact(['objglcode','objSON','objAutoGenNo','AlpsStatus','objHldTypeList','objFnlyearList']));
       
   }

   public function codeduplicate(Request $request){

        
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $COMPANY_HOLIDAY_CODE =   strtoupper($request['COMPANY_HOLIDAY_CODE']);
        
        $objLabel = DB::table('TBL_MST_COMPANY_HOLIDAY')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('COMPANY_HOLIDAY_CODE','=',$COMPANY_HOLIDAY_CODE)
        ->select('COMPANY_HOLIDAY_CODE')
        ->first();
        // dd($objLabel);
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }

    
   public function save(Request $request){
    
        //validation rules
    $rules = [
            'COMPANY_HOLIDAY_CODE' => 'required',         
        ];


        $req_data = [

            'COMPANY_HOLIDAY_CODE'     =>    $request['COMPANY_HOLIDAY_CODE']
        ]; 


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
           return Response::json(['errors' => $validator->errors()]);	
        }

        $r_count = $request['Row_Count'];
        for ($i=0; $i<=$r_count; $i++)
        {
            if(isset($request['HOLIDAY_DATE_'.$i]))
            {
                $data[$i] = [
                    'HOLIDAY_DATE'    => strtoupper($request['HOLIDAY_DATE_'.$i]),
                    'HOLIDAYTYPEID_REF' => $request['HOLIDAYTYPEID_REF_'.$i],
                    'HOLIDAY_EVENT' => $request['HOLIDAY_EVENT_'.$i],
                ];
            }
        }

        //dd($data);

        $wrapped_links["MAT"] = $data; 
       
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        // dd($xml);
        //get data
        $COMPANY_HOLIDAY_CODE   = $request['COMPANY_HOLIDAY_CODE'];
        $COMPANY_HOLIDAY_DATE   = $request['COMPANY_HOLIDAY_DATE']; 
        $FYID_REF               = $request['FYID_REF']; 
        
        $DEACTIVATED =  ($request['DE_ACTIVATED'] == "on"  ? 1 : 0) ;
        $DODEACTIVATED =  (isset($request->DO_DEACTIVATED)? $request->DO_DEACTIVATED : NULL) ;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        //$FYID_REF   =   Session::get('FYID_REF');       
        $VTID        =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "ADD";
        $IPADDRESS  =   $request->getClientIp();
        
        $condition_data = [
            $COMPANY_HOLIDAY_CODE, $COMPANY_HOLIDAY_DATE, $FYID_REF, $XMLMAT, 
            $CYID_REF, $BRID_REF,$VTID, $USERID,$UPDATE, $UPTIME, $ACTION, $IPADDRESS
        ];


        //dd($condition_data);
                    
     //  DB::enableQueryLog();
       try {

            //save data
           $sp_result = DB::select('EXEC SP_COMPANY_HOLIDAY_IN ?,?,?,?,?,?,?,?,?,?,?,?', $condition_data);
      
        } catch (\Throwable $th) {
        
            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
    
        }
     
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
           
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','country'=>'duplicate']);
            
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
            $Status = "A";

            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
            

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objCondition = DB::table('TBL_MST_COMPANY_HOLIDAY')
                            ->where('TBL_MST_COMPANY_HOLIDAY.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_COMPANY_HOLIDAY.BRID_REF','=',Session::get('BRID_REF'))
                            //->where('TBL_MST_COMPANY_HOLIDAY.FYID_REF','=',Session::get('FYID_REF'))
                            ->where('TBL_MST_COMPANY_HOLIDAY.CP_HOLIDAYID','=',$id)
                            ->select('TBL_MST_COMPANY_HOLIDAY.*')
                            ->first();

                            $objConditiontemp = DB::table('TBL_MST_COMPANY_HOLIDAY_DETAIL')
                            ->where('TBL_MST_COMPANY_HOLIDAY_DETAIL.CP_HOLIDAYID_REF','=',$id)
                            ->select('TBL_MST_COMPANY_HOLIDAY_DETAIL.*')
                            ->get()
                            ->toArray();

                $objFnlyearList = DB::table('TBL_MST_YEAR')
                    ->where('STATUS','=','A')
                    ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                    ->select('YRID','YRCODE','YRDESCRIPTION')
                    ->get();

                    $objHldTypeList    =   DB::table('TBL_MST_HOLIDAY_TYPE')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->get();
            
           
            return view('masters.Payroll.CompanyHoliday.mstfrm268edit',compact(['objCondition','objFnlyearList','objHldTypeList','objConditiontemp','user_approval_level','objRights']));
        }

    }//edit function

    //update the data
   public function update(Request $request)
   {
     
    $update_rules = [
        'COMPANY_HOLIDAY_CODE' => 'required',         
    ];


    $req_update_data = [

        'COMPANY_HOLIDAY_CODE'     =>    $request['COMPANY_HOLIDAY_CODE']
    ]; 


        $validator = Validator::make( $req_update_data, $update_rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }


    
        $r_count = $request['Row_Count'];
       
        for ($i=0; $i<=$r_count; $i++)
        {
            if(isset($request['HOLIDAY_DATE_'.$i]))
            {
                $data[$i] = [
                    'HOLIDAY_DATE'    => strtoupper($request['HOLIDAY_DATE_'.$i]),
                    'HOLIDAYTYPEID_REF' => $request['HOLIDAYTYPEID_REF_'.$i],
                    'HOLIDAY_EVENT' => $request['HOLIDAY_EVENT_'.$i],
                    
                ];
            }
        }

        //dd($data);
        $wrapped_links["MAT"] = $data; 
       
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        // dd($xml);
        //get data


        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        //$FYID_REF   =   Session::get('FYID_REF');       
        $VTID        =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();

        $CP_HOLIDAYID   =   trim($request['CP_HOLIDAYID_REF']);
        $COMPANY_HOLIDAY_CODE       =   strtoupper(trim($request['COMPANY_HOLIDAY_CODE']) );
        $COMPANY_HOLIDAY_DATE   =   trim($request['COMPANY_HOLIDAY_DATE']); 
        $FYID_REF   =   trim($request['FYID_REF']); 

        
        $condition_data = [
            $CP_HOLIDAYID,      $COMPANY_HOLIDAY_CODE,  $COMPANY_HOLIDAY_DATE,  $FYID_REF, 
            $XMLMAT,            $CYID_REF,              $BRID_REF,              $VTID, 
            $USERID,            $UPDATE,                $UPTIME,                $ACTION,                
            $IPADDRESS
        ];

        //dd($condition_data);
       
     //  DB::enableQueryLog();
       try {

            //save data
           $sp_result = DB::select('EXEC SP_COMPANY_HOLIDAY_UP ?,?,?,?, ?,?,?,?, ?,?,?,?, ?', $condition_data);
      
        } catch (\Throwable $th) {
        
            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
    
        }
     
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
           
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','country'=>'duplicate']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }
        
        exit();           
    } // update function
    

    //singleApprove begin
    
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

    if(!empty($sp_listing_result))
        {
            foreach ($sp_listing_result as $key=>$salesenquiryitem)
        {  
            $record_status = 0;
            $Approvallevel = "APPROVAL".$salesenquiryitem->LAVELS;
        }
        }
       
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['HOLIDAY_DATE_'.$i]))
            {
                $data[$i] = [
                    'HOLIDAY_DATE'    => strtoupper($request['HOLIDAY_DATE_'.$i]),
                    'HOLIDAYTYPEID_REF' => $request['HOLIDAYTYPEID_REF_'.$i],
                    'HOLIDAY_EVENT' => $request['HOLIDAY_EVENT_'.$i],
                    
                ];
            }
        }


        if(isset($req_data)) { 
       
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
            }
            else {
                $XMLMAT = NULL; 
            }  
        
        
            $COMPANY_HOLIDAY_CODE   = $request['COMPANY_HOLIDAY_CODE'];
            $COMPANY_HOLIDAY_DATE   = $request['COMPANY_HOLIDAY_DATE']; 
            $FYID_REF               = $request['FYID_REF']; 
            $CP_HOLIDAYID               = $request['CP_HOLIDAYID_REF']; 
            $DEACTIVATED =  ($request['DE_ACTIVATED'] == "on"  ? 1 : 0) ;
            $DODEACTIVATED =  (isset($request->DO_DEACTIVATED)? $request->DO_DEACTIVATED : NULL) ;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');
            //$FYID_REF   =   Session::get('FYID_REF');       
            $VTID        =   $this->vtid_ref;
            $USERID     =   Auth::user()->USERID;
            $UPDATE     =   Date('Y-m-d');
            
            $UPTIME     =   Date('h:i:s.u');
            $ACTION = $Approvallevel;
            $IPADDRESS  =   $request->getClientIp();
            
            $condition_data = [
                $CP_HOLIDAYID,$COMPANY_HOLIDAY_CODE, $COMPANY_HOLIDAY_DATE, $FYID_REF, $XMLMAT, 
                $CYID_REF, $BRID_REF,$VTID, $USERID,$UPDATE, $UPTIME, $ACTION, $IPADDRESS
            ];              
           
            
            $sp_result = DB::select('EXEC SP_COMPANY_HOLIDAY_UP ?,?,?,?,?,?,?,?,?,?,?,?,?', $condition_data); 
                
            
            if($sp_result[0]->RESULT=="SUCCESS"){
    
                return Response::json(['success' =>true,'msg' => 'Record successfully approved.']);
    
            }else{
                return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
            }
            exit();     
    }




    public function view($id){

        if(!is_null($id))
        {
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');
            $Status = "A";

            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
            

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objCondition = DB::table('TBL_MST_COMPANY_HOLIDAY')
                            ->where('TBL_MST_COMPANY_HOLIDAY.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_COMPANY_HOLIDAY.BRID_REF','=',Session::get('BRID_REF'))
                            //->where('TBL_MST_COMPANY_HOLIDAY.FYID_REF','=',Session::get('FYID_REF'))
                            ->where('TBL_MST_COMPANY_HOLIDAY.CP_HOLIDAYID','=',$id)
                            ->select('TBL_MST_COMPANY_HOLIDAY.*')
                            ->first();

                            $objConditiontemp = DB::table('TBL_MST_COMPANY_HOLIDAY_DETAIL')
                            ->where('TBL_MST_COMPANY_HOLIDAY_DETAIL.CP_HOLIDAYID_REF','=',$id)
                            ->select('TBL_MST_COMPANY_HOLIDAY_DETAIL.*')
                            ->get()
                            ->toArray();

                $objFnlyearList = DB::table('TBL_MST_YEAR')
                    ->where('STATUS','=','A')
                    ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                    ->select('YRID','YRCODE','YRDESCRIPTION')
                    ->get();

                    $objHldTypeList    =   DB::table('TBL_MST_HOLIDAY_TYPE')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->get();

            //return view('masters.Payroll.CompanyHoliday.mstfrm268edit',compact(['objCondition','objFnlyearList','objHldTypeList','objConditiontemp','user_approval_level','objRights']));
            return view('masters.Payroll.CompanyHoliday.mstfrm268view',compact(['objCondition','objFnlyearList','objHldTypeList','objConditiontemp','user_approval_level','objRights']));

        }

    }//view function
  
   

   
  
    
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objCondition = DB::table('TBL_MST_COMPANY_HOLIDAY')
            ->where('TBL_MST_COMPANY_HOLIDAY.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_COMPANY_HOLIDAY.BRID_REF','=',Session::get('BRID_REF'))
            //->where('TBL_MST_COMPANY_HOLIDAY.FYID_REF','=',Session::get('FYID_REF'))
            ->where('TBL_MST_COMPANY_HOLIDAY.CP_HOLIDAYID','=',$id)
            ->select('TBL_MST_COMPANY_HOLIDAY.*')
            ->first();
            // dd($objCalculation);
            //select * from TBL_MST_VOUCHERTYPE where VTID=114
            $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
                    ->where('VTID','=',$this->vtid_ref)
                    ->select('VTID','VCODE','DESCRIPTIONS','INDATE')
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

            return view('masters.Payroll.CompanyHoliday.mstfrm268attachment',compact(['objCondition','objMstVoucherType','objAttachments']));
        }

    }
    
    

    public function cancel(Request $request){

        $id = $request->{0};

        $objResponse = DB::table('TBL_MST_COMPANY_HOLIDAY')->where('CP_HOLIDAYID','=',$id)->select('*')->first();
        $FYID_REF = $objResponse->FYID_REF;

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   $FYID_REF;       
        $TABLE      =   "TBL_MST_COMPANY_HOLIDAY";
        $FIELD      =   "CP_HOLIDAYID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_MST_COMPANY_HOLIDAY_DETAIL',
        ];
    
        $wrapped_links["TABLES"] = $req_data; 
        
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$XMLTAB];
        $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);


        if($sp_result[0]->RESULT=="CANCELED"){  
          
          return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
        
            
            return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
            
        }else{
           
               return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'invalid'=>'invalid']);
        }
        
        exit(); 


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
                foreach ($sp_listing_result as $key=>$salesenquiryitem)
            {  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$salesenquiryitem->LAVELS;
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
            $TABLE      =   "TBL_MST_TNC";
            $FIELD      =   "TNCID";
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
        
        return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','termscondition'=>'norecord']);
        
        }else{
        return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','termscondition'=>'Some Error']);
        }
        
        exit();    
    }



    public function AlpsStatus(){

        $COMPANY_NAME   =   DB::table('TBL_MST_COMPANY')->where('STATUS','=','A')->where('CYID','=',Auth::user()->CYID_REF)->select('TBL_MST_COMPANY.NAME')->first()->NAME;
    
        $disabled       =   strpos($COMPANY_NAME,"ALPS")!== false?'disabled':'';
        $hidden         =   strpos($COMPANY_NAME,"ALPS")!== false?'':'hidden';
       
        return  $ALPS_STATUS=array(
            'hidden'=>$hidden,
            'disabled'=>$disabled
        );
    
    }


    


}
