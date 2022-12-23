<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use DB;
use Session;
use Response;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MstFrm271Controller extends Controller
{
    protected $form_id = 271;
    protected $vtid_ref   = 361;  //voucher type id
    
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
      
        $objDataList = DB::table('TBL_MST_ROUTING_HDR')
        ->where('TBL_MST_ROUTING_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->leftJoin('TBL_MST_ITEM',   'TBL_MST_ITEM.ITEMID','=',   'TBL_MST_ROUTING_HDR.ITEMID_REF')
        ->select('TBL_MST_ROUTING_HDR.*', 'TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME')
        ->orderBy('TBL_MST_ROUTING_HDR.ROUTINGID', 'ASC')
        ->get();    

        
        return view('masters.Production.RoutingMaster.mstfrm271',compact(['objRights','FormId','objDataList']));        
    }

    public function add(){       
       
        return view('masters.Production.RoutingMaster.mstfrm271add');       
   }

   
    
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objResponse = DB::table('TBL_MST_ROUTING_HDR')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('ROUTINGID','=',$id)
                ->select('*')
                ->first();
            
            
            
            $objItemNo = DB::table('TBL_MST_ITEM')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('ITEMID','=',$objResponse->ITEMID_REF )
                ->select('ITEMID','ICODE','NAME')
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

            return view('masters.Production.RoutingMaster.mstfrm271attachment',compact(['objResponse','objMstVoucherType','objAttachments','objItemNo']));
        }

    }

    
   public function save(Request $request) {

      
        $r_count1 = $request['Row_Count1'];

        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['PSTAGEID_REF_'.$i]))
            {
                $req_data[$i] = [
                        
                    'PSTAGEID_REF'      => $request['PSTAGEID_REF_'.$i],
                    'INHOUSE_PRODUCTION'=>isset($request['INHOUSE_PRODUCTION_'.$i]) ? 1 : 0,     
                    'JOB_WORKER'=>isset($request['JOB_WORKER_'.$i])  ? 1 : 0,  
                    'FG_STAGE'=>isset($request['FG_STAGE_'.$i])  ? 1 : 0,   
                ];
            }
        }
        

            $wrapped_links["ITEM"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
            

            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $VTID_REF     =   $this->vtid_ref;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();


            $ITEMID_REF = trim( $request['ITEMID_REF']);
            $DEACTIVATED = 0 ;
            $DODEACTIVATED = NULL ;
              
          
            $log_data = [ 
                $ITEMID_REF,      $DEACTIVATED,    $DODEACTIVATED, $CYID_REF,  
                $BRID_REF,        $FYID_REF,        $XMLMAT,       $VTID_REF, 
                $USERID,          Date('Y-m-d'),    Date('h:i:s.u'), $ACTIONNAME,
                $IPADDRESS
            ];

          
            
            try {

                $sp_result = DB::select('EXEC SP_ROUTING_IN ?,?,?,?, ?,?,?,?, ?,?,?,?, ?', $log_data);       
               
           
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
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        $cur_date = Date('Y-m-d');
        
        if(!is_null($id))
        {
            $objRouting = DB::table('TBL_MST_ROUTING_HDR')
                ->where('FYID_REF','=',$FYID_REF)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('ROUTINGID','=',$id)
                ->select('*')
                ->first();
            
            if(!empty($objRouting)){
                if(trim($objRouting->STATUS) !='N'){
                    exit('Sorry: Only "Not Approved" record can be edit.');
                }
            } 

            $objItemNo = DB::table('TBL_MST_ITEM')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('ITEMID','=',$objRouting->ITEMID_REF )
                ->select('ITEMID','ICODE','NAME')
                ->first();


            $objRoutingMAT = DB::table('TBL_MST_ROUTING_MAT')                    
                             ->where('TBL_MST_ROUTING_MAT.ROUTINGID_REF','=',$id)
                             ->leftJoin('TBL_MST_PRODUCTIONSTAGES', 'TBL_MST_ROUTING_MAT.PSTAGEID_REF','=','TBL_MST_PRODUCTIONSTAGES.PSTAGEID') 
                             ->select('TBL_MST_ROUTING_MAT.*','TBL_MST_PRODUCTIONSTAGES.PSTAGE_CODE','TBL_MST_PRODUCTIONSTAGES.DESCRIPTIONS')
                             ->orderBy('TBL_MST_ROUTING_MAT.MWITEM_MATID','ASC')
                             ->get()->toArray();
           
         
            $objCount1 = count($objRoutingMAT);            
                    

            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
  
            
            return view('masters.Production.RoutingMaster.mstfrm271edit',compact(['objRouting','objRights','objCount1', 'objRoutingMAT','objItemNo']));
        }
     
    }
     
    public function view($id){

        
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        $cur_date = Date('Y-m-d');
        
        if(!is_null($id))
        {
            $objRouting = DB::table('TBL_MST_ROUTING_HDR')
                ->where('FYID_REF','=',$FYID_REF)
                ->where('CYID_REF','=',$CYID_REF)
                ->where('ROUTINGID','=',$id)
                ->select('*')
                ->first();
            
         
            $objItemNo = DB::table('TBL_MST_ITEM')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('ITEMID','=',$objRouting->ITEMID_REF )
                ->select('ITEMID','ICODE','NAME')
                ->first();


            $objRoutingMAT = DB::table('TBL_MST_ROUTING_MAT')                    
                             ->where('TBL_MST_ROUTING_MAT.ROUTINGID_REF','=',$id)
                             ->leftJoin('TBL_MST_PRODUCTIONSTAGES', 'TBL_MST_ROUTING_MAT.PSTAGEID_REF','=','TBL_MST_PRODUCTIONSTAGES.PSTAGEID') 
                             ->select('TBL_MST_ROUTING_MAT.*','TBL_MST_PRODUCTIONSTAGES.PSTAGE_CODE','TBL_MST_PRODUCTIONSTAGES.DESCRIPTIONS')
                             ->orderBy('TBL_MST_ROUTING_MAT.MWITEM_MATID','ASC')
                             ->get()->toArray();
           
         
            $objCount1 = count($objRoutingMAT);            
                    

            return view('masters.Production.RoutingMaster.mstfrm271view',compact(['objRouting','objCount1', 'objRoutingMAT','objItemNo']));
        }
        
    } //view

    


    public function update(Request $request){

        $r_count1 = $request['Row_Count1'];  
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['PSTAGEID_REF_'.$i]))
            {
                $req_data[$i] = [
                        
                'MWITEM_MATID'      => !is_null($request['MWITEM_MATID_'.$i])? $request['MWITEM_MATID_'.$i]:0,
                'PSTAGEID_REF'      => $request['PSTAGEID_REF_'.$i],
                'INHOUSE_PRODUCTION'=>isset($request['INHOUSE_PRODUCTION_'.$i]) ? 1 : 0,     
                'JOB_WORKER'=>isset($request['JOB_WORKER_'.$i])  ? 1 : 0,  
                'FG_STAGE'=>isset($request['FG_STAGE_'.$i])  ? 1 : 0,  
                ];
            }
        }

        $wrapped_links["ITEM"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        $VTID_REF     =   $this->vtid_ref;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $ITEMID_REF = trim( $request['ITEMID_REF']);

        $DEACTIVATED = (isset($request['HDR_DEACTIVATED']) )? 1 : 0 ;
       
        $newDateString5 = NULL;
        $newdt5 = !(is_null($request['HDR_DODEACTIVATED']) ||empty($request['HDR_DODEACTIVATED']) )=="true" ? $request['HDR_DODEACTIVATED'] : NULL; 
        if(!is_null($newdt5) ){
            $newdt5 = str_replace( "/", "-",  $newdt5 ) ;
            $newDateString5 = Carbon::parse($newdt5)->format('Y-m-d');        
        }
        $DODEACTIVATED = $newDateString5;

        $log_data = [ 
            $ITEMID_REF,   $DEACTIVATED,    $DODEACTIVATED, $CYID_REF,  
            $BRID_REF,        $FYID_REF,        $XMLMAT,       $VTID_REF, 
            $USERID,          Date('Y-m-d'),    Date('h:i:s.u'), $ACTIONNAME,
            $IPADDRESS
        ];

        
        $sp_result =  DB::select('EXEC SP_ROUTING_UP ?,?,?,?, ?,?,?,?, ?,?,?,?, ?', $log_data);  
        
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
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

        
        $r_count1 = $request['Row_Count1'];            
        //------------------------
        for ($i=0; $i<=$r_count1; $i++)
        {
           
            if(isset($request['PSTAGEID_REF_'.$i]))
            {
                $req_data[$i] = [
                        
                    'MWITEM_MATID'      => !is_null($request['MWITEM_MATID_'.$i])? $request['MWITEM_MATID_'.$i]:0,
                    'PSTAGEID_REF'      => $request['PSTAGEID_REF_'.$i],
                    'INHOUSE_PRODUCTION'=>isset($request['INHOUSE_PRODUCTION_'.$i]) ? 1 : 0,     
                    'JOB_WORKER'=>isset($request['JOB_WORKER_'.$i])  ? 1 : 0,  
                    'FG_STAGE'=>isset($request['FG_STAGE_'.$i])  ? 1 : 0,  
                ];
            }
        }    
        //------------------------
        $ACTIONNAME = $Approvallevel;

        $wrapped_links["ITEM"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        $VTID_REF     =   $this->vtid_ref;
        $USERID = Auth::user()->USERID;   
        
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $ITEMID_REF = trim( $request['ITEMID_REF']);

        $DEACTIVATED = (isset($request['HDR_DEACTIVATED']) )? 1 : 0 ;

        $newDateString5 = NULL;
        $newdt5 = !(is_null($request['HDR_DODEACTIVATED']) ||empty($request['HDR_DODEACTIVATED']) )=="true" ? $request['HDR_DODEACTIVATED'] : NULL; 
        if(!is_null($newdt5) ){
            $newdt5 = str_replace( "/", "-",  $newdt5 ) ;
            $newDateString5 = Carbon::parse($newdt5)->format('Y-m-d');        
        }
        $DODEACTIVATED = $newDateString5;

        $log_data = [ 

            $ITEMID_REF,   $DEACTIVATED,    $DODEACTIVATED, $CYID_REF,  
            $BRID_REF,        $FYID_REF,        $XMLMAT,       $VTID_REF, 
            $USERID,          Date('Y-m-d'),    Date('h:i:s.u'), $ACTIONNAME,
            $IPADDRESS
        ];

        $sp_result = DB::select('EXEC SP_ROUTING_UP ?,?,?,?, ?,?,?,?, ?,?,?,?, ?', $log_data);        
        
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully approved.']);

        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
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
        $TABLE      =   "TBL_MST_ROUTING_HDR";
        $FIELD      =   "ROUTINGID";
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
        $TABLE      =   "TBL_MST_ROUTING_HDR";
        $FIELD      =   "ROUTINGID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_MST_ROUTING_MAT',
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
        
        $destinationPath = storage_path()."/docs/company".$CYID_REF."/RoutingMaster";

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
            return redirect()->route("master",[271,"attachment",$ATTACH_DOCNO])->with("success","The file is already exist");
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

            return redirect()->route("master",[271,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[271,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            return redirect()->route("master",[271,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
      
        
    }   

    //--------------------------
    public function getfgitems(Request $request){

        $Status  = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $cur_date = Date('Y-m-d');
        //-----------------------
        //--SINGLE ITEM  ROUTING ALLOW--
        //skip those items which have status N or A in TBL_MST_ROUTING_HDR table from item list
        $ObjData = DB::select("SELECT  ITEMID,ICODE,NAME,MAIN_UOMID_REF,ITEM_DESC,ITEM_SPECI,ITEM_TYPE,MATERIAL_TYPE FROM TBL_MST_ITEM 
            where  (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>='".$cur_date."') and CYID_REF = ".$CYID_REF."  and STATUS = 'A' and ITEM_TYPE='I-Inventory' and (MATERIAL_TYPE='FG-Finish Good' OR MATERIAL_TYPE='SFG- Semi Finish Good') and ITEMID NOT IN(SELECT ITEMID_REF from TBL_MST_ROUTING_HDR  where CYID_REF = ".$CYID_REF."  and STATUS <> 'C' ) order by ICODE");            

        if(!empty($ObjData)){

            foreach ($ObjData as $index=>$dataRow){
                
                $row = '';
                $row = $row.'<tr >
                <td width="10%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_FGITEMID_REF[]" id="FGITEMPOPcode_'.$dataRow->ITEMID .'"  class="clsFGITEMPOP" value="'.$dataRow->ITEMID.'" ></td>
                <td width="20%" class="ROW2">'.$dataRow->ICODE;
                $row = $row.'<input type="hidden" id="txtFGITEMPOPcode_'.$dataRow->ITEMID.'" data-desc="'.$dataRow->ICODE.'"  data-descdate="'.$dataRow->NAME.'"
                value="'.$dataRow->ITEMID.'"  /></td><td width="40%" class="ROW3">'.$dataRow->NAME.'</td>';
                $row = $row.'<td width="30%" class="ROW4">'.$dataRow->MATERIAL_TYPE.'</td>';
                $row = $row.'</tr>';
                echo $row;

            }
        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();
    }

    public function getprodstages(Request $request){

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;

        $cur_date = Date('Y-m-d');
        $ObjData = DB::select('select * from TBL_MST_PRODUCTIONSTAGES  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?) and CYID_REF = ?   and STATUS = ? ',  [$cur_date,$CYID_REF,'A']);
                        
        if(!empty($ObjData)){

        foreach ($ObjData as $index=>$dataRow){
            $row = '';
            $row = $row.'<tr >
            <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_PSTAGEID_REF[]" id="pstagerow_'.$dataRow->PSTAGEID.'"  class="clsPStage" value="'.$dataRow->PSTAGEID.'" ></td>
            <td width="39%" class="ROW2">'.$dataRow->PSTAGE_CODE;
            $row = $row.'<input type="hidden" id="txtpstagerow_'.$dataRow->PSTAGEID.'" data-desc="'.$dataRow->PSTAGE_CODE .'" data-stagedesc="'.$dataRow->DESCRIPTIONS.'" value="'.$dataRow->PSTAGEID.'"/></td>';
            $row = $row.'<td width="39%" class="ROW3">'.$dataRow->DESCRIPTIONS.'</td>';
            $row = $row.'</tr>';
            echo $row;
        }
        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();

    }

    
 
    
} //class
