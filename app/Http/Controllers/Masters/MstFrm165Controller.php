<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm165;
use DB;
use Response;
use Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;


class MstFrm165Controller extends Controller
{
   
    protected $form_id = 165;
    protected $vtid_ref   = 265;  //voucher type id  

    //validation messages
    protected   $messages = [
                    'NOA_CODE.required' => 'Required field',
                    'NOA_CODE.unique' => 'Duplicate Code',
                    'NOA_NAME.required' => 'Required field'
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
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Auth::user()->BRID_REF)
        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Auth::user()->FYID_REF)
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first(); 



        return view('masters.Accounts.SalesAccountSet.mstfrm165',compact(['objRights']));

    }

    public function edit($id){

        if(!is_null($id))
        {
        
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Auth::user()->BRID_REF;    
            $FYID_REF   =   Auth::user()->FYID_REF;

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objResponse = TblMstFrm165::where('SL_AC_SETID','=',$id)->first();
            /* if(strtoupper($objResponse->STATUS)=="A" || strtoupper($objResponse->STATUS)=="C"){
                exit("Sorry, Only Un Approved record can edit.");
            } */

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Auth::user()->BRID_REF)
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Auth::user()->FYID_REF)
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $objLedgerList = DB::table('TBL_MST_GENERALLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('STATUS','=','A')
           ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
           ->select('GLID','GLCODE','GLNAME')
           ->get();


           $objSalesAccoutName = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('STATUS','=','A')
           ->where('GLID','=',$objResponse->SALES_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           if($objSalesAccoutName){

           }else{
               $objSalesAccoutName='';
           }

           $objSalesReturnName = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('STATUS','=','A')
           ->where('GLID','=',$objResponse->SALES_RETURN)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           if($objSalesReturnName){

            }else{
                $objSalesReturnName='';
            }
           $objShortageName = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('STATUS','=','A')
           ->where('GLID','=',$objResponse->SHORTAGE)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           if($objShortageName){

            }else{
                $objShortageName='';
            }
           $objCostOfGoodSoldName = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('BRID_REF','=',Auth::user()->BRID_REF)
           ->where('FYID_REF','=',Auth::user()->FYID_REF)
           ->where('STATUS','=','A')
           ->where('GLID','=',$objResponse->COST_OF_GOOD_SOLD)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           
           if($objCostOfGoodSoldName){

            }else{
                $objCostOfGoodSoldName='';
            }
			
			$objCostOfGoodSoldExportName = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('BRID_REF','=',Auth::user()->BRID_REF)
           ->where('FYID_REF','=',Auth::user()->FYID_REF)
           ->where('STATUS','=','A')
           ->where('GLID','=',$objResponse->COST_OF_GOOD_SOLD_EXPORT)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           
           if($objCostOfGoodSoldExportName){

            }else{
                $objCostOfGoodSoldExportName='';
            }

            $objExportSalesAcctName = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('BRID_REF','=',Auth::user()->BRID_REF)
           ->where('FYID_REF','=',Auth::user()->FYID_REF)
           ->where('STATUS','=','A')
           ->where('GLID','=',$objResponse->EXPORT_SALES_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           if($objExportSalesAcctName){

            }else{
                $objExportSalesAcctName='';
            }
			
			$objSalesISAccoutName = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('STATUS','=','A')
           ->where('GLID','=',$objResponse->SALESIS_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           if($objSalesISAccoutName){

           }else{
               $objSalesISAccoutName='';
           }

         
            return view('masters.Accounts.SalesAccountSet.mstfrm165edit',compact(['objResponse','user_approval_level','objSalesAccoutName',
            'objSalesReturnName','objShortageName','objCostOfGoodSoldName','objRights','objLedgerList','objExportSalesAcctName','objCostOfGoodSoldExportName',
			'objSalesISAccoutName']));
        }

    }




    public function getlisting(Request $request){
        $columns = array(
            0 =>'NO', 
            1 =>'AC_SET_CODE',
            2 =>'AC_SET_DESC',
            3 =>'STATUS',
        );  

        $COL_APP_STATUS =   'STATUS';  //never change value, value must be 'STATUS' as per stored procedure;
    
        $USERID_REF =   Auth::user()->USERID;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Auth::user()->BRID_REF;
        $FYID_REF   =   Auth::user()->FYID_REF;       
        $TABLE1     =   "TBL_MST_SALES_AC_SET";
        $PK_COL     =   "SL_AC_SETID";
          
        $SELECT_COL =   "SL_AC_SETID,AC_SET_CODE,AC_SET_DESC";    

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

                $WHERE_COL =  " WHERE AC_SET_CODE LIKE  ". $search_text;
                $WHERE_COL =  $WHERE_COL.  " OR  AC_SET_DESC LIKE  ". $search_text;
                $WHERE_COL =  $WHERE_COL.  " OR  AC_SET_DESC LIKE  ". $search_text;


            }else{

                $WHERE_COL =  " WHERE  ".$filtercolumn." LIKE ". $search_text;

            }         
            
        }
        
        $ORDER_BY_COL   =  $order. " ". $dir;
        $OFFSET_COL     =   " offset ".$start." rows fetch next ".$limit." rows only ";

        $sp_listing_data = [
            $USERID_REF,  $CYID_REF,$BRID_REF,
             $TABLE1, $PK_COL,
            $SELECT_COL,$WHERE_COL, $ORDER_BY_COL, $OFFSET_COL

        ];
        
        $sp_listing_result = DB::select('EXEC SP_LISTINGDATA ?,?,?, ?,?,?,?, ?,?', $sp_listing_data);

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

    
          
         
           

                $nestedData['NO']              = '<input type="checkbox" id="chkId'.$valueitem->SL_AC_SETID.'"  value="'.$valueitem->SL_AC_SETID.'" class="js-selectall1" data-rcdstatus="'.$app_status.'">';
                $nestedData['AC_SET_CODE']     = $valueitem->AC_SET_CODE;
                $nestedData['AC_SET_DESC']     = $valueitem->AC_SET_DESC;

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
        $BRID_REF   =   Auth::user()->BRID_REF;    
        $FYID_REF   =   Auth::user()->FYID_REF;

		
        $objLedgerList = DB::table('TBL_MST_GENERALLEDGER')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('GLID','GLCODE','GLNAME')
        ->get();
        //dd($objCountryList); 


		     
      return view('masters.Accounts.SalesAccountSet.mstfrm165add',compact(['objLedgerList']));        
    }

   public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Auth::user()->BRID_REF;
        $FYID_REF = Auth::user()->FYID_REF;
        $NOA_CODE =   $request['NOA_CODE'];
        
        $objLabel = DB::table('TBL_MST_GENERALLEDGER')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Auth::user()->BRID_REF)
        ->where('FYID_REF','=',Auth::user()->FYID_REF)
        ->where('NOA_CODE','=',$NOA_CODE)
        ->select('NOA_CODE')
        ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }

   public function save(Request $request){

    //dd($request->all());

       $rules = [
            'AC_SET_CODE' => 'required|unique:TBL_MST_SALES_AC_SET',
            'AC_SET_DESC' => 'required',          
            // 'SALES_AC' => 'required',          
            // 'SALES_RETURN' => 'required',          
            // 'SHORTAGE' => 'required',          
            // 'COST_OF_GOOD_SOLD' => 'required',          
        ];

        $req_data = [

            'AC_SET_CODE'     =>$request['AC_SET_CODE'],
            'AC_SET_DESC' =>   $request['AC_SET_DESC'],
            // 'SALES_AC' =>   $request['SALES_AC'],
            // 'SALES_RETURN' =>   $request['SALES_RETURN'],
            // 'SHORTAGE' =>   $request['SHORTAGE'],
            // 'COST_OF_GOOD_SOLD' =>   $request['COST_OF_GOOD_SOLD'],
        ]; 


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }


 
        $AC_SET_CODE   =   strtoupper(trim($request['AC_SET_CODE']) );
        $AC_SET_DESC   =   $request['AC_SET_DESC'];  
        $SALES_AC       =   $request['SALES_AC'];  
        $SALES_RETURN   =   $request['SALES_RETURN'];  
        $SHORTAGE        =   $request['SHORTAGE'];  
        $COST_OF_GOOD_SOLD   =   $request['COST_OF_GOOD_SOLD'];  
        $EXPORT_SALE_ACCT   =   $request['EXPORT_SALE_ACCT']; 
		$COST_OF_GOOD_SOLD_EXPORT   =   $request['COST_OF_GOOD_SOLD_EXPORT']; 
		$SALESIS_AC       =   $request['SALESIS_AC']; 
        $DEACTIVATED    =   NULL;  
        $DODEACTIVATED  =   NULL;  
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Auth::user()->BRID_REF;
        $FYID_REF       =   Auth::user()->FYID_REF;    
        $USERID         =   Auth::user()->USERID;   
        $VTID           =   $this->vtid_ref;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();
        
        
        $array_data   = [
                        $AC_SET_CODE, $AC_SET_DESC,$SALES_AC,$SALES_RETURN,$SHORTAGE,$COST_OF_GOOD_SOLD,
                        $DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF,
                        $FYID_REF, $VTID, $USERID, $UPDATE,
                        $UPTIME, $ACTION, $IPADDRESS,$EXPORT_SALE_ACCT,$COST_OF_GOOD_SOLD_EXPORT,$SALESIS_AC 
                    ];
                    
                    
        try {

            $sp_result = DB::select('EXEC SP_SALES_AC_SET_IN ?,?,?, ?,?,?, ?,?,?, ?,?,?,?,?,?,?,?,?,?,?', $array_data);
            //dd($sp_result); 
        
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



                    


     
    public function update(Request $request)
    {

      // dd($request->all());
      $rules = [
            'AC_SET_DESC' => 'required',          
            // 'SALES_AC' => 'required',          
            // 'SALES_RETURN' => 'required',          
            // 'SHORTAGE' => 'required',          
            // 'COST_OF_GOOD_SOLD' => 'required',          
        ];

        $req_data = [

            'AC_SET_DESC' =>   $request['AC_SET_DESC'],
            // 'SALES_AC' =>   $request['SALES_AC'],
            // 'SALES_RETURN' =>   $request['SALES_RETURN'],
            // 'SHORTAGE' =>   $request['SHORTAGE'],
            // 'COST_OF_GOOD_SOLD' =>   $request['COST_OF_GOOD_SOLD'],
        ]; 


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
        return Response::json(['errors' => $validator->errors()]);	
        }
    
        $AC_SET_CODE   =   strtoupper(trim($request['AC_SET_CODE']) );
        $AC_SET_DESC   =   $request['AC_SET_DESC'];  
        $SALES_AC   =   $request['SALES_AC'];  
        $SALES_RETURN   =   $request['SALES_RETURN'];  
        $SHORTAGE   =   $request['SHORTAGE'];  
        $COST_OF_GOOD_SOLD   =   $request['COST_OF_GOOD_SOLD'];  
        $EXPORT_SALE_ACCT   =   $request['EXPORT_SALE_ACCT']; 
		$COST_OF_GOOD_SOLD_EXPORT   =   $request['COST_OF_GOOD_SOLD_EXPORT'];
		$SALESIS_AC       =   $request['SALESIS_AC']; 
        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
        $newDateString = NULL;

        $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt) ){
            
            $newdt = str_replace( "/", "-",  $newdt ) ;

            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        

        $DODEACTIVATED = $newDateString;

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Auth::user()->BRID_REF;
        $FYID_REF   =   Auth::user()->FYID_REF;       
        $VTID     =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();
        
        $array_data   = [
            $AC_SET_CODE, $AC_SET_DESC,$SALES_AC,$SALES_RETURN,$SHORTAGE,$COST_OF_GOOD_SOLD,
            $DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF,
            $FYID_REF, $VTID, $USERID, $UPDATE,
            $UPTIME, $ACTION, $IPADDRESS,$EXPORT_SALE_ACCT,$COST_OF_GOOD_SOLD_EXPORT,$SALESIS_AC
        ];

        try {

        $sp_result = DB::select('EXEC SP_SALES_AC_SET_UP ?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?,?,?', $array_data);
        //dd($array_data);

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
        $BRID_REF       =   Auth::user()->BRID_REF;
        $FYID_REF       =   Auth::user()->FYID_REF;       
        // @XML	xml
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();
        
        $destinationPath = storage_path()."/docs/company".$CYID_REF."/SalesAccountSet";

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
            return redirect()->route("master",[165,"attachment",$ATTACH_DOCNO])->with("success","The file is already exist");
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

             //save data
             $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);

        

     
        if($sp_result[0]->RESULT=="SUCCESS"){

            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }

            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }

            return redirect()->route("master",[165,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[165,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("master",[165,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
            return redirect()->route("master",[165,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   

  


   


   



    //singleApprove begin
    public function singleapprove(Request $request)
    {
        
         $approv_rules = [
 
            'AC_SET_DESC' => 'required',          
            'SALES_AC' => 'required',          
            'SALES_RETURN' => 'required',          
            'SHORTAGE' => 'required',          
            'COST_OF_GOOD_SOLD' => 'required',       
         ];
 
         $req_approv_data = [
 
            'AC_SET_DESC' =>   $request['AC_SET_DESC'],
            'SALES_AC' =>   $request['SALES_AC'],
            'SALES_RETURN' =>   $request['SALES_RETURN'],
            'SHORTAGE' =>   $request['SHORTAGE'],
            'COST_OF_GOOD_SOLD' =>   $request['COST_OF_GOOD_SOLD'],
         ]; 
 
 
         $validator = Validator::make( $req_approv_data, $approv_rules, $this->messages);
 
         if ($validator->fails())
         {
         return Response::json(['errors' => $validator->errors()]);	
         }
     
         $AC_SET_CODE   =   strtoupper(trim($request['AC_SET_CODE']) );
         $AC_SET_DESC   =   trim($request['AC_SET_DESC']);  
         $SALES_AC   =   trim($request['SALES_AC']);  
         $SALES_RETURN   =   trim($request['SALES_RETURN']);  
         $SHORTAGE   =   trim($request['SHORTAGE']);  
         $COST_OF_GOOD_SOLD   =   trim($request['COST_OF_GOOD_SOLD']);  
         $EXPORT_SALE_ACCT   =   $request['EXPORT_SALE_ACCT']; 
		 $COST_OF_GOOD_SOLD_EXPORT   =   $request['COST_OF_GOOD_SOLD_EXPORT'];
		 $SALESIS_AC   =   trim($request['SALESIS_AC']);

        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
        $newDateString = NULL;

        $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt) ){
            
            $newdt = str_replace( "/", "-",  $newdt ) ;

            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        

        $DODEACTIVATED = $newDateString;


         $CYID_REF   =   Auth::user()->CYID_REF;
         $BRID_REF   =   Auth::user()->BRID_REF;
 
         $FYID_REF   =   Auth::user()->FYID_REF;       
         $VTID       =   $this->vtid_ref;
         $USERID     =   Auth::user()->USERID;
         $UPDATE     =   Date('Y-m-d');
         
         $UPTIME     =   Date('h:i:s.u');
         $ACTION     =   trim($request['user_approval_level']);   // user approval level value
         $IPADDRESS  =   $request->getClientIp();
         
         $array_data   = [
            $AC_SET_CODE, $AC_SET_DESC,$SALES_AC,$SALES_RETURN,$SHORTAGE,$COST_OF_GOOD_SOLD,
            $DEACTIVATED, $DODEACTIVATED, $CYID_REF, $BRID_REF,
            $FYID_REF, $VTID, $USERID, $UPDATE,
            $UPTIME, $ACTION, $IPADDRESS,$EXPORT_SALE_ACCT,$COST_OF_GOOD_SOLD_EXPORT,$SALESIS_AC
        ];
          

        try {

        $sp_result = DB::select('EXEC SP_SALES_AC_SET_UP ?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?,?,?', $array_data);
    

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
            $objResponse = TblMstFrm165::where('SL_AC_SETID','=',$id)->first();
            $objLedgerList = DB::table('TBL_MST_GENERALLEDGER')
            ->where('BRID_REF','=',Auth::user()->BRID_REF)
           ->where('FYID_REF','=',Auth::user()->FYID_REF)
           ->where('STATUS','=','A')
           ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
           ->select('GLID','GLCODE','GLNAME')
           ->get();


           $objSalesAccoutName = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('BRID_REF','=',Auth::user()->BRID_REF)
           ->where('FYID_REF','=',Auth::user()->FYID_REF)
           ->where('STATUS','=','A')
           ->where('GLID','=',$objResponse->SALES_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           if($objSalesAccoutName){

           }else{
               $objSalesAccoutName='';
           }

           $objSalesReturnName = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('BRID_REF','=',Auth::user()->BRID_REF)
           ->where('FYID_REF','=',Auth::user()->FYID_REF)
           ->where('STATUS','=','A')
           ->where('GLID','=',$objResponse->SALES_RETURN)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           if($objSalesReturnName){

            }else{
                $objSalesReturnName='';
            }
           $objShortageName = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('BRID_REF','=',Auth::user()->BRID_REF)
           ->where('FYID_REF','=',Auth::user()->FYID_REF)
           ->where('STATUS','=','A')
           ->where('GLID','=',$objResponse->SHORTAGE)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           if($objShortageName){

            }else{
                $objShortageName='';
            }
           $objCostOfGoodSoldName = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('BRID_REF','=',Auth::user()->BRID_REF)
           ->where('FYID_REF','=',Auth::user()->FYID_REF)
           ->where('STATUS','=','A')
           ->where('GLID','=',$objResponse->COST_OF_GOOD_SOLD)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           if($objCostOfGoodSoldName){

            }else{
                $objCostOfGoodSoldName='';
            }

            $objExportSalesAcctName = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('BRID_REF','=',Auth::user()->BRID_REF)
           ->where('FYID_REF','=',Auth::user()->FYID_REF)
           ->where('STATUS','=','A')
           ->where('GLID','=',$objResponse->EXPORT_SALES_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           if($objExportSalesAcctName){

            }else{
                $objExportSalesAcctName='';
            }
			
			$objCostOfGoodSoldExportName = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('BRID_REF','=',Auth::user()->BRID_REF)
           ->where('FYID_REF','=',Auth::user()->FYID_REF)
           ->where('STATUS','=','A')
           ->where('GLID','=',$objResponse->COST_OF_GOOD_SOLD_EXPORT)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           
           if($objCostOfGoodSoldExportName){

            }else{
                $objCostOfGoodSoldExportName='';
            }
			
			$objSalesISAccoutName = DB::table('TBL_MST_GENERALLEDGER')
           ->where('CYID_REF','=',Auth::user()->CYID_REF)
           ->where('STATUS','=','A')
           ->where('GLID','=',$objResponse->SALESIS_AC)
           ->select('GLID','GLCODE','GLNAME')
           ->first();
           if($objSalesISAccoutName){

           }else{
               $objSalesISAccoutName='';
           }


            return view('masters.Accounts.SalesAccountSet.mstfrm165view',compact(['objResponse','objSalesAccoutName','objSalesReturnName',
            'objShortageName','objCostOfGoodSoldName','objExportSalesAcctName','objCostOfGoodSoldExportName','objSalesISAccoutName']));
        }

    }
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }
        $objResponse = TblMstFrm165::whereIn('SL_AC_SETID',$ids_data)->get();
        
        return view('masters.Accounts.SalesAccountSet.mstfrm165print',compact(['objResponse']));
   }//print




        //display attachments form
        public function attachment($id){

            if(!is_null($id))
            {
                //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
                $objResponse = TblMstFrm165::where('SL_AC_SETID','=',$id)->first();

   // dd($objResponse); 
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
                            ->where('TBL_MST_ATTACHMENT.BRID_REF','=',Auth::user()->BRID_REF)
                            ->where('TBL_MST_ATTACHMENT.FYID_REF','=',Auth::user()->FYID_REF)
                            ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                            ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                            ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                            ->get()->toArray(); 
    
                     // dd( $objMstVoucherType);
    
                return view('masters.Accounts.SalesAccountSet.mstfrm165attachment',compact(['objResponse','objMstVoucherType','objAttachments']));
            }
    
        }
        
    
    public function MultiApprove(Request $request){

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Auth::user()->BRID_REF;
        $FYID_REF   =   Auth::user()->FYID_REF;   

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
            $BRID_REF   =   Auth::user()->BRID_REF;
            $FYID_REF   =   Auth::user()->FYID_REF;       
            $TABLE      =   "TBL_MST_SALES_AC_SET";
            $FIELD      =   "SL_AC_SETID";
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
        

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Auth::user()->BRID_REF;
        $FYID_REF   =   Auth::user()->FYID_REF;       
        $TABLE      =   "TBL_MST_SALES_AC_SET";
        $FIELD      =   "SL_AC_SETID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $cancelData[0]= ['NT' =>'TBL_MST_SALES_AC_SET'];
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


}
