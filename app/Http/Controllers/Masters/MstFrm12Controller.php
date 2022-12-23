<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Master\TblMstFrm12;
use App\Models\Admin\TblMstUser;
use Auth;
use DB;
use Session;
use Response;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MstFrm12Controller extends Controller
{
    protected $form_id = 12;
    protected $vtid_ref   = 12;  //voucher type id
    // //validation messages
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
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
                        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                        ->first();
        $objCount = DB::table('TBL_MST_UDF_SRETURN')
                        ->where('TBL_MST_UDF_SRETURN.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_UDF_SRETURN.STATUS', '!=', 'C')
                        ->where('TBL_MST_UDF_SRETURN.PARENTID','=', 0)
                        ->select('TBL_MST_UDF_SRETURN.*')
                        ->count();

       

        return view('masters.Sales.UDFSR.mstfrm12',compact(['objRights','objCount']));        
    }

    public function add(){       
        return view('masters.Sales.UDFSR.mstfrm12add');       
   }


   //display attachments form
   public function attachment($id){

    if(!is_null($id))
    {
        $objResponse = TblMstFrm12::where('UDF_SRID','=',$id)->first();

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
                        ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                        ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                        ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                        ->get()->toArray();

                 // dump( $objAttachments);

            return view('masters.Sales.UDFSR.mstfrm12attachment',compact(['objResponse','objMstVoucherType','objAttachments']));
    }

}

    
   public function save(Request $request) {
    
        $r_count = $request['Row_Count'];

        $data = [
            'UDF_SRID' => "0",
            'LABEL'    => strtoupper($request['LABEL_0']),
            'VALUETYPE' => $request['VALUETYPE_0'],
            'DESCRIPTIONS' => (isset($request->DESCRIPTIONS_0)? $request->DESCRIPTIONS_0 : ""),
            'ISMANDATORY' => (isset($request['ISMANDATORY_0'])!="true" ? 0 : 1) ,
            'DEACTIVATED' => (isset($request['DEACTIVATED_0'])!="true" ? 0 : 1) ,
            'DODEACTIVATED' => !(is_null($request['DODEACTIVATED_0'])||empty($request['DODEACTIVATED_0']))=="true"? $request['DODEACTIVATED_0'] : NULL,
            ];
         
        $links["UDF"] = $data; 
        $parentxml = ArrayToXml::convert($links);
        
        for ($i=0; $i<=$r_count; $i++)
        {
            if((isset($request['LABEL_'.$i])))
            {
                $req_data[$i] = [
                    'UDF_SRID' => (isset($request['UDF_SRID_'.$i]) ? $request['UDF_SRID_'.$i] : "0") ,
                    'LABEL'    => strtoupper($request['LABEL_'.$i]),
                    'VALUETYPE' => $request['VALUETYPE_'.$i],
                    'DESCRIPTIONS' => !(is_null($request['DESCRIPTIONS_'.$i]))=="true"? $request['DESCRIPTIONS_'.$i] : "",
                    'ISMANDATORY' => (isset($request['ISMANDATORY_'.$i])!="true" ? 0 : 1) ,
                    'DEACTIVATED' => (isset($request['DEACTIVATED_'.$i])!="true" ? 0 : 1) ,
                    'DODEACTIVATED' => !(is_null($request['DODEACTIVATED_'.$i])||empty($request['DODEACTIVATED_'.$i]))=="true"? $request['DODEACTIVATED_'.$i] : NULL,
                ];
            }
        }
            $wrapped_links["UDF"] = $req_data; 

            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            
            
            $xml = ArrayToXml::convert($wrapped_links);
            
            $log_data = [ 
                $CYID_REF, $BRID_REF, $FYID_REF,$parentxml, $xml,$VTID_REF, $USERID, Date('Y-m-d'), Date('h:i:s.u'),  
                $ACTIONNAME, $IPADDRESS
            ];

            
            $sp_result = DB::select('EXEC SP_UDF_SALES_RETURN_INUPDE ?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
        
            if($sp_result[0]->RESULT=="SUCCESS"){
    
                return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);
    
            }else{
                return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
            }
            exit();   
     }

     public function getListing(Request $request){

        

        $columns = array( 
            0 =>'NO',
            1 =>'LABEL',
            2 =>'VALUETYPE',
            3 =>'DESCRIPTIONS',
            4 =>'ISMANDATORY',
            5 =>'DEACTIVATED',
            6 =>'DODEACTIVATED',
            7 =>'STATUS',
        );  
        

        $COL_APP_STATUS =   'STATUS';  //never change value, value must be 'APPROVED_STATUS' as per stored procedure;
      
            $USERID_REF    =   Auth::user()->USERID;
            $CYID_REF      =   Auth::user()->CYID_REF;
            $BRID_REF      =   Session::get('BRID_REF');
            $FYID_REF      =   Session::get('FYID_REF');       
            $TABLE1        =   "TBL_MST_UDF_SRETURN";
            $PK_COL        =   "UDF_SRID";
            $SELECT_COL    =   "UDF_SRID,LABEL,VALUETYPE,DESCRIPTIONS,ISMANDATORY,DEACTIVATED,DODEACTIVATED";    
            $WHERE_COL     =   " WHERE PARENTID = 0";
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

                    $WHERE_COL =  " WHERE PARENTID = 0 AND UDF_SRID LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR PARENTID = 0 AND LABEL LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR PARENTID = 0 AND VALUETYPE LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR PARENTID = 0 AND DESCRIPTIONS LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR PARENTID = 0 AND ISMANDATORY LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR PARENTID = 0 AND DEACTIVATED LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR PARENTID = 0 AND DODEACTIVATED LIKE  ". $search_text;
                    $WHERE_COL =  $WHERE_COL.  " OR PARENTID = 0 AND ".$COL_APP_STATUS." LIKE  ". $search_text;


                }else{

                    $WHERE_COL =  " WHERE PARENTID = 0 AND ".$filtercolumn." LIKE ". $search_text;

                }         
                
            }
           
            $ORDER_BY_COL   =  $order. " ". $dir;
            $OFFSET_COL     =   " offset ".$start." rows fetch next ".$limit." rows only ";
           
            $sp_listing_data = [
                $USERID_REF, $CYID_REF,$BRID_REF, $FYID_REF, $TABLE1, $PK_COL,
                $SELECT_COL,$WHERE_COL, $ORDER_BY_COL, $OFFSET_COL

            ];

            
            
            $sp_listing_result = DB::select('EXEC SP_LISTINGDATA_UDF ?,?,?,?, ?,?,?,?, ?,?', $sp_listing_data);

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

                    if (!Empty($valueitem->ISMANDATORY) && $valueitem->ISMANDATORY=="1") 
                    { $ISMANDATORY = "Yes" ;} 
                    else{ $ISMANDATORY = "No" ;}
                    if (!Empty($valueitem->DEACTIVATED) && $valueitem->DEACTIVATED=="1") 
                    { $DEACTIVATED = "Yes" ;} 
                    else{ $DEACTIVATED = "No" ;}

                    $nestedData['NO']           = '<input type="checkbox" id="chkId'.$valueitem->UDF_SRID.'"  value="'.$valueitem->UDF_SRID.'" class="js-selectall1" data-rcdstatus="'.$app_status.'">';
                    $nestedData['LABEL']         = strtoupper($valueitem->LABEL);
                    $nestedData['VALUETYPE']     = $valueitem->VALUETYPE;
                    $nestedData['DESCRIPTIONS']      = $valueitem->DESCRIPTIONS;
                    $nestedData['ISMANDATORY']         = $ISMANDATORY;
                    $nestedData['DEACTIVATED']  = $DEACTIVATED;
                    $nestedData['DODEACTIVATED']      = $valueitem->DODEACTIVATED =="1900-01-01"?"":$valueitem->DODEACTIVATED;
                    $nestedData['STATUS']       = $valueitem->STATUS;
                    // $nestedData['action'] = '<a href="#" class="del"><span class="glyphicon glyphicon-trash"></span> 
                    // </a><a href="#" class="edit"><span class="glyphicon glyphicon-edit"></span></a>';
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

    public function edit($id){

        if(!is_null($id))
        {
            $objUdfResponse = DB::table('TBL_MST_UDF_SRETURN')                    
                             ->where('TBL_MST_UDF_SRETURN.UDF_SRID','=',$id)
                             ->orwhere('TBL_MST_UDF_SRETURN.PARENTID','=',$id)
                             ->select('TBL_MST_UDF_SRETURN.*')
                             ->orderBy('TBL_MST_UDF_SRETURN.UDF_SRID','ASC')
                             ->get()->toArray();
            $objCount = count($objUdfResponse);
            if(strtoupper($objUdfResponse[0]->STATUS)!="N"){
                exit("Sorry, Only Un Approved record can edit.");
            }
     
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
           return view('masters.Sales.UDFSR.mstfrm12edit',compact(['objUdfResponse','objRights','objCount']));
        }
     
       }
     
       public function view($id){
     
         if(!is_null($id))
         {
             // $objUdfResponse = TblMstFrm12::where('UDF_SRID','=',$id)->first();
             $objUdfResponse = DB::table('TBL_MST_UDF_SRETURN')                    
                             ->where('TBL_MST_UDF_SRETURN.UDF_SRID','=',$id)
                             ->orwhere('TBL_MST_UDF_SRETURN.PARENTID','=',$id)
                             ->select('TBL_MST_UDF_SRETURN.*')
                             ->orderBy('TBL_MST_UDF_SRETURN.UDF_SRID','ASC')
                             ->get()->toArray();
             return view('masters.Sales.UDFSR.mstfrm12view',compact(['objUdfResponse']));
         }
      
        }

     

    //update the data
   public function update(Request $request){

       $r_count = $request['Row_Count'];
        // dd($request->all());
    //   dd($r_count); 
    //   dd((isset($request->DODEACTIVATED[0])? $request->DODEACTIVATED[0] : NULL));
    // dd($request->VALUETYPE);
        $data = [
            'UDF_SRID' => (isset($request->UDF_SRID_0) ? $request->UDF_SRID_0 : "0"),
            'LABEL'    => strtoupper($request->LABEL_0),
            'VALUETYPE' => $request->VALUETYPE_0,
            'DESCRIPTIONS' => (isset($request->DESCRIPTIONS_0)? $request->DESCRIPTIONS_0 : ""),
            'ISMANDATORY' => (isset($request->ISMANDATORY_0) ? 1 : 0) ,
            'DEACTIVATED' => (isset($request->DEACTIVATED_0) ? 1 : 0) ,
            'DODEACTIVATED' => (isset($request->DODEACTIVATED_0)? $request->DODEACTIVATED_0 : NULL),
            ];
        $links["UDF"] = $data; 
        $parentxml = ArrayToXml::convert($links);
        
        for ($i=0; $i<=$r_count; $i++)
        {
            if((isset($request['UDF_SRID_'.$i])) && (isset($request['LABEL_'.$i])))
            {
                $req_data[$i] = [
                    'UDF_SRID' => (isset($request['UDF_SRID_'.$i]) ? $request['UDF_SRID_'.$i] : "0") ,
                    'LABEL'    => strtoupper($request['LABEL_'.$i]),
                    'VALUETYPE' => $request['VALUETYPE_'.$i],
                    'DESCRIPTIONS' => !(is_null($request['DESCRIPTIONS_'.$i]))=="true"? $request['DESCRIPTIONS_'.$i] : "",
                    'ISMANDATORY' => (isset($request['ISMANDATORY_'.$i])!="true" ? 0 : 1) ,
                    'DEACTIVATED' => (isset($request['DEACTIVATED_'.$i])!="true" ? 0 : 1) ,
                    'DODEACTIVATED' => !(is_null($request['DODEACTIVATED_'.$i])||empty($request['DODEACTIVATED_'.$i]))=="true"? $request['DODEACTIVATED_'.$i] : NULL,
                ];
            }
        }
            
        // dd($req_data);
        $wrapped_links["UDF"] = $req_data; 
        $xml = ArrayToXml::convert($wrapped_links);
        //  dd($xml); 
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID_REF     =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $ACTIONNAME     =   "ADD";
        $IPADDRESS  =   $request->getClientIp();

        
        $log_data = [ 
            $CYID_REF, $BRID_REF, $FYID_REF,$parentxml, $xml,$VTID_REF, $USERID, Date('Y-m-d'), Date('h:i:s.u'),  
            $ACTIONNAME, $IPADDRESS
                ];
    
            $sp_result = DB::select('EXEC SP_UDF_SALES_RETURN_INUPDE ?,?,?,?,?,?,?,?,?,?,?',  $log_data);       


            if($sp_result[0]->RESULT=="SUCCESS"){

                return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

            }elseif($sp_result[0]->RESULT=="Some cancel records in input records"){
                            
                return Response::json(['cancel'=>true,'msg' => 'Already cancel record exists with same data.']);
                
            }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
                
                return Response::json(['duplicate'=>true,'msg' => 'Duplicate record.','reqdata'=>'duplicate']);
                
            }else{
                return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','reqdata'=>'Some Error']);
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
                foreach ($sp_listing_result as $key=>$valueitem)
            {  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$valueitem->LAVELS;
            }
            }
           
            $r_count = $request['Row_Count'];

            $data = [
                'UDF_SRID' => (isset($request->UDF_SRID_0)? $request->UDF_SRID_0 : "0"),
                'LABEL'    => strtoupper($request->LABEL_0),
                'VALUETYPE' => $request->VALUETYPE_0,
                'DESCRIPTIONS' => (isset($request->DESCRIPTIONS_0)? $request->DESCRIPTIONS_0 : ""),
                'ISMANDATORY' => (isset($request->ISMANDATORY_0) ? 1 : 0) ,
                'DEACTIVATED' => (isset($request->DEACTIVATED_0) ? 1 : 0) ,
                'DODEACTIVATED' => (isset($request->DODEACTIVATED_0)? $request->DODEACTIVATED_0 : NULL),
                ];
            // dd($r_count);   
            $links["UDF"] = $data; 
            $parentxml = ArrayToXml::convert($links);
        
            for ($i=0; $i<=$r_count; $i++)
            {
                if((isset($request['UDF_SRID_'.$i])) && (isset($request['LABEL_'.$i])))
                {
                    $req_data[$i] = [
                        'UDF_SRID' => (isset($request['UDF_SRID_'.$i]) ? $request['UDF_SRID_'.$i] : "0") ,
                        'LABEL'    => strtoupper($request['LABEL_'.$i]),
                        'VALUETYPE' => $request['VALUETYPE_'.$i],
                        'DESCRIPTIONS' => !(is_null($request['DESCRIPTIONS_'.$i]))=="true"? $request['DESCRIPTIONS_'.$i] : "",
                        'ISMANDATORY' => (isset($request['ISMANDATORY_'.$i])!="true" ? 0 : 1) ,
                        'DEACTIVATED' => (isset($request['DEACTIVATED_'.$i])!="true" ? 0 : 1) ,
                        'DODEACTIVATED' => !(is_null($request['DODEACTIVATED_'.$i])||empty($request['DODEACTIVATED_'.$i]))=="true"? $request['DODEACTIVATED_'.$i] : NULL,
                    ];
                }
            }
            
            $wrapped_links["UDF"] = $req_data; 
            $xml = ArrayToXml::convert($wrapped_links);

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID_REF     =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $ACTIONNAME     = $Approvallevel;
        $IPADDRESS  =   $request->getClientIp();
        
        
        $log_data = [ 
        $CYID_REF, $BRID_REF, $FYID_REF,$parentxml, $xml,$VTID_REF, $USERID, Date('Y-m-d'), Date('h:i:s.u'),  
        $ACTIONNAME, $IPADDRESS
        ];

            
        $sp_result = DB::select('EXEC SP_UDF_SALES_RETURN_INUPDE ?,?,?,?,?,?,?,?,?,?,?',  $log_data);       


        if($sp_result[0]->RESULT=="SUCCESS"){

        return Response::json(['success' =>true,'msg' => 'Record successfully Approved.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
        
        return Response::json(['duplicate'=>true,'msg' => 'Duplicate record.','reqdata'=>'duplicate']);
        
        }else{
        return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','reqdata'=>'Some Error']);
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
            

                // $LABEL          =   $request['LABEL'];
                // $VALUETYPE      =   $request['VALUETYPE'];
                // $DESCRIPTIONS    =   $request['DESCRIPTIONS'];
                // $DEACTIVATED    =   $request['DEACTIVATED'];
                // $ISMANDATORY    =   $request['ISMANDATORY'];     
                // $DODEACTIVATED  =   $request['DODEACTIVATED'];  
                
                // $r_count = $request['ID']->length();
                
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
                $TABLE      =   "TBL_MST_UDF_SRETURN";
                $FIELD      =   "UDF_SRID";
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
            
            return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','reqdata'=>'norecord']);
            
            }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','reqdata'=>'Some Error']);
            }
            
            exit();    
            }

    //Cancel the data
   public function cancel(Request $request){
    //  dd($request->{0});  

   //save data
        $id = $request->{0};

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_UDF_SRETURN";
        $FIELD      =   "UDF_SRID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $canceldata[0]=[
            'NT'  => 'TBL_MST_UDF_SRETURN',
       ];        
       $links["TABLES"] = $canceldata; 
       $cancelxml = ArrayToXml::convert($links);

        
        $udf_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$cancelxml ];

        $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $udf_cancel_data);

        if($sp_result[0]->RESULT=="CANCELED"){  

            return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
        
            return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'invalid'=>'invalid']);
        }
        
        exit(); 
    }

  
  

   

   public function docuploads(Request $request){

    $formData = $request->all();

    $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
    $allow_size = config("erpconst.attachments.max_size") * 1024 * 1024;

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
    
    $destinationPath = storage_path()."/docs/company".$CYID_REF."/udfsomst";

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
        return redirect()->route("master",[12,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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

        return redirect()->route("master",[12,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("master",[12,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("master",[12,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

    public function checkLabel(Request $request){

        // dd($request->LABEL_0);
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $LABEL = $request->LABEL_0;
        
        $objLabel = DB::table('TBL_MST_UDF_SRETURN')
        ->where('TBL_MST_UDF_SRETURN.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_UDF_SRETURN.LABEL','=',$LABEL)
        ->select('TBL_MST_UDF_SRETURN.UDF_SRID')
        ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate Label']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }
    
}
