<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Master\TblMstFrm166;
use App\Models\Admin\TblMstUser;
use Auth;
use DB;
use Session;
use Response;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MstFrm166Controller extends Controller
{
    protected $form_id = 166;
    protected $vtid_ref   = 303;  //voucher type id
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
                        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
                        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                        ->first();
        $objCount = DB::table('TBL_MST_WITHHOLDING')
                        ->where('TBL_MST_WITHHOLDING.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_WITHHOLDING.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_MST_WITHHOLDING.FYID_REF','=',Session::get('FYID_REF'))
                        ->where('TBL_MST_WITHHOLDING.STATUS', '!=', 'C')
                        ->select('TBL_MST_WITHHOLDING.*')
                        ->count();


        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');     
        
        $objDataList	=	DB::select("SELECT * FROM TBL_MST_WITHHOLDING WHERE CYID_REF='$CYID_REF'  ORDER BY HOLDINGID DESC");


       

        return view('masters.accounts.WithholdingTaxMaster.mstfrm166',compact(['objRights','objCount','objDataList']));        
    }

    public function add(){  
        $objSectionMasterList = DB::table('TBL_MST_SECTION')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
       ->where('STATUS','=','A')
       ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
       ->select('SECTIONID','SECTION_CODE','SECTION_NAME')
       ->get();     
        $objNatureOfAsseesseeList = DB::table('TBL_MST_NATUAREOF_ASSESSEE')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
       ->where('STATUS','=','A')
       ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
       ->select('NOAID','NOA_CODE','NOA_NAME')
       ->get();    
        $objGenralLedgerList = DB::table('TBL_MST_GENERALLEDGER')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
       ->where('STATUS','=','A')
       ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
       ->select('GLID','GLCODE','GLNAME')
       ->get();    
       //dd($objNatureOfAsseesseeList);  
        return view('masters.accounts.WithholdingTaxMaster.mstfrm166add',compact(['objSectionMasterList','objNatureOfAsseesseeList','objGenralLedgerList']));       
   }


   //display attachments form
   public function attachment($id){

    if(!is_null($id))
    {
        $objResponse = TblMstFrm166::where('HOLDINGID','=',$id)->first();

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

            return view('masters.accounts.WithholdingTaxMaster.mstfrm166attachment',compact(['objResponse','objMstVoucherType','objAttachments']));
    }

}

    
   public function save(Request $request) {
    
        $r_count = $request['Row_Count'];


        
        for ($i=0; $i<=$r_count; $i++)
        {
            if((isset($request['CODE_'.$i])))
            {

                $req_data[$i] = [
         
                    'CODE'    => strtoupper($request['CODE_'.$i]),
                    'CODE_DESC' => $request['CODE_DESC_'.$i],
                    'SECTIONID_REF' => $request['SECTIONID_REF_'.$i],
                    'ASSESSEEID_REF' => $request['ASSESSEEID_REF_'.$i],
                    'BASE_TYPE' => $request['BASE_TYPE_'.$i],
                    'APPLICABLE_FRDT' => $request['APPLICABLE_FRDT_'.$i],
                    'TDS_RATE' => $request['TDS_RATE_'.$i],
                    'TDS_EXEMP_LIMIT' => (($request['TDS_EXEMP_LIMIT_'.$i])=="" ? 0 : $request['TDS_EXEMP_LIMIT_'.$i]),
                    'SURCHARGE_RAGE' => (($request['SURCHARGE_RAGE_'.$i])=="" ? 0 : $request['SURCHARGE_RAGE_'.$i]),
                    'SURCHARGE_EXEMP_LIMIT' => (($request['SURCHARGE_EXEMP_LIMIT_'.$i])=="" ? 0 : $request['SURCHARGE_EXEMP_LIMIT_'.$i]),
                    'CESS_RATE' => (($request['CESS_RATE_'.$i])=="" ? 0 : $request['CESS_RATE_'.$i]),
                    'CESS_EXEMP_LIMIT' => (($request['CESS_EXEMP_LIMIT_'.$i])=="" ? 0 : $request['CESS_EXEMP_LIMIT_'.$i]),
                    'SP_CESS_RATE' => (($request['SP_CESS_RATE_'.$i])=="" ? 0 : $request['SP_CESS_RATE_'.$i]),
                    'SP_CESS_EXEMP_LIMIT' => (($request['SP_CESS_EXEMP_LIMIT_'.$i])=="" ? 0 : $request['SP_CESS_EXEMP_LIMIT_'.$i]),
                    'TDS_GLID_REF' => $request['TDS_GLID_REF_'.$i],
                    'SURCHARGE_GLID_REF' => $request['SURCHARGE_GLID_REF_'.$i],
                    'CESS_GLID_REF' => $request['CESS_GLID_REF_'.$i],
                    'SP_CESS_GLID_REF' => $request['SP_CESS_GLID_REF_'.$i],
                    'RETURN_TYPE' => $request['RETURN_TYPE_'.$i],                   
                    'DEACTIVATED' => (isset($request['DEACTIVATED_'.$i])!="true" ? 0 : 1) ,
                    'DODEACTIVATED' => !(is_null($request['DODEACTIVATED_'.$i]) || empty($request['DODEACTIVATED_'.$i])) =="true"? $request['DODEACTIVATED_'.$i] : NULL,
                ];
            }
        }
    
            $wrapped_links["HOLDING"] = $req_data; 
          

            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $UPDATE =   Date('Y-m-d');
            $UPTIME =   Date('h:i:s.u');
            
            
            $xml = ArrayToXml::convert($wrapped_links);

            
            $log_data = [ $xml, $CYID_REF, $BRID_REF, $FYID_REF,$VTID_REF, $USERID, $UPDATE, $UPTIME,  
                $ACTIONNAME, $IPADDRESS
            ];

            
            $sp_result = DB::select('EXEC SP_WITHHOLDING_IN ?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
      // dd($sp_result);

            if($sp_result[0]->RESULT=="SUCCESS"){
    
                return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);
    
            }else{
                return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
            }
            exit();   
     }


     

    public function edit($id){

        if(!is_null($id))
        {

                            $objTdsResponse = DB::table('TBL_MST_WITHHOLDING')   
                            ->where('TBL_MST_WITHHOLDING.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_WITHHOLDING.BRID_REF','=',Session::get('BRID_REF'))
                             ->leftJoin('TBL_MST_SECTION', 'TBL_MST_WITHHOLDING.SECTIONID_REF','=','TBL_MST_SECTION.SECTIONID')                       
                             ->select('TBL_MST_WITHHOLDING.*','TBL_MST_SECTION.SECTION_CODE')
                             ->orderBy('TBL_MST_WITHHOLDING.HOLDINGID','ASC')
                             ->get()->toArray();
                            $objCount = count($objTdsResponse);                    
            
                          

                  
     
                             $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();

                             
                             $objSectionMasterList = DB::table('TBL_MST_SECTION')
                             ->where('CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('BRID_REF','=',Session::get('BRID_REF'))
                             ->where('STATUS','=','A')
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('SECTIONID','SECTION_CODE','SECTION_NAME')
                            ->get();     
                             $objNatureOfAsseesseeList = DB::table('TBL_MST_NATUAREOF_ASSESSEE')
                             ->where('CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('BRID_REF','=',Session::get('BRID_REF'))
                             ->where('STATUS','=','A')
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('NOAID','NOA_CODE','NOA_NAME')
                            ->get();    
                             $objGenralLedgerList = DB::table('TBL_MST_GENERALLEDGER')
                             ->where('CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('BRID_REF','=',Session::get('BRID_REF'))
                             ->where('STATUS','=','A')
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('GLID','GLCODE','GLNAME')
                            ->get();    
           return view('masters.accounts.WithholdingTaxMaster.mstfrm166edit',compact(['objSectionMasterList','objNatureOfAsseesseeList','objGenralLedgerList','objTdsResponse','objRights','objCount']));
        }
     
       }
     
       public function view($id){
     
         if(!is_null($id))
         {
            $objTdsResponse = DB::table('TBL_MST_WITHHOLDING')   
            ->where('TBL_MST_WITHHOLDING.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_WITHHOLDING.BRID_REF','=',Session::get('BRID_REF'))
             ->leftJoin('TBL_MST_SECTION', 'TBL_MST_WITHHOLDING.SECTIONID_REF','=','TBL_MST_SECTION.SECTIONID')                       
             ->select('TBL_MST_WITHHOLDING.*','TBL_MST_SECTION.SECTION_CODE')
             ->orderBy('TBL_MST_WITHHOLDING.HOLDINGID','ASC')
             ->get()->toArray();
            $objCount = count($objTdsResponse);         
          

  

             $objRights = DB::table('TBL_MST_USERROLMAP')
             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
             ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
             ->first();

             
             $objSectionMasterList = DB::table('TBL_MST_SECTION')
             ->where('CYID_REF','=',Auth::user()->CYID_REF)
             ->where('BRID_REF','=',Session::get('BRID_REF'))
             ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('SECTIONID','SECTION_CODE','SECTION_NAME')
            ->get();     
             $objNatureOfAsseesseeList = DB::table('TBL_MST_NATUAREOF_ASSESSEE')
             ->where('CYID_REF','=',Auth::user()->CYID_REF)
             ->where('BRID_REF','=',Session::get('BRID_REF'))
             ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('NOAID','NOA_CODE','NOA_NAME')
            ->get();    
             $objGenralLedgerList = DB::table('TBL_MST_GENERALLEDGER')
             ->where('CYID_REF','=',Auth::user()->CYID_REF)
             ->where('BRID_REF','=',Session::get('BRID_REF'))
             ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('GLID','GLCODE','GLNAME')
            ->get();    
return view('masters.accounts.WithholdingTaxMaster.mstfrm166view',compact(['objSectionMasterList','objNatureOfAsseesseeList','objGenralLedgerList','objTdsResponse','objRights','objCount']));
         }
      
        }

     

    //update the data
   public function update(Request $request){

       $r_count = $request['Row_Count'];
       
    //   dd($r_count); 
    //   dd((isset($request->DODEACTIVATED[0])? $request->DODEACTIVATED[0] : NULL));
    // dd($request->VALUETYPE);

            
    for ($i=0; $i<=$r_count; $i++)
    {
        if((isset($request['CODE_'.$i])))
        {

            $req_data[$i] = [
     
                'CODE'    => strtoupper($request['CODE_'.$i]),
                'CODE_DESC' => $request['CODE_DESC_'.$i],
                'SECTIONID_REF' => $request['SECTIONID_REF_'.$i],
                'ASSESSEEID_REF' => $request['ASSESSEEID_REF_'.$i],
                'BASE_TYPE' => $request['BASE_TYPE_'.$i],
                'APPLICABLE_FRDT' => $request['APPLICABLE_FRDT_'.$i],
                'TDS_RATE' => $request['TDS_RATE_'.$i],
                'TDS_EXEMP_LIMIT' => (($request['TDS_EXEMP_LIMIT_'.$i])=="" ? 0 : $request['TDS_EXEMP_LIMIT_'.$i]),
                'SURCHARGE_RAGE' => (($request['SURCHARGE_RAGE_'.$i])=="" ? 0 : $request['SURCHARGE_RAGE_'.$i]),
                'SURCHARGE_EXEMP_LIMIT' => (($request['SURCHARGE_EXEMP_LIMIT_'.$i])=="" ? 0 : $request['SURCHARGE_EXEMP_LIMIT_'.$i]),
                'CESS_RATE' => (($request['CESS_RATE_'.$i])=="" ? 0 : $request['CESS_RATE_'.$i]),
                'CESS_EXEMP_LIMIT' => (($request['CESS_EXEMP_LIMIT_'.$i])=="" ? 0 : $request['CESS_EXEMP_LIMIT_'.$i]),
                'SP_CESS_RATE' => (($request['SP_CESS_RATE_'.$i])=="" ? 0 : $request['SP_CESS_RATE_'.$i]),
                'SP_CESS_EXEMP_LIMIT' => (($request['SP_CESS_EXEMP_LIMIT_'.$i])=="" ? 0 : $request['SP_CESS_EXEMP_LIMIT_'.$i]),
                'TDS_GLID_REF' => $request['TDS_GLID_REF_'.$i],
                'SURCHARGE_GLID_REF' => $request['SURCHARGE_GLID_REF_'.$i],
                'CESS_GLID_REF' => $request['CESS_GLID_REF_'.$i],
                'SP_CESS_GLID_REF' => $request['SP_CESS_GLID_REF_'.$i],
                'RETURN_TYPE' => $request['RETURN_TYPE_'.$i],                   
                'DEACTIVATED' => (isset($request['DEACTIVATED_'.$i])!="true" ? 0 : 1) ,
                'DODEACTIVATED' => !(is_null($request['DODEACTIVATED_'.$i])||empty($request['DODEACTIVATED_'.$i]))=="true"? $request['DODEACTIVATED_'.$i] : NULL,
            ];
        }
    }


            
      //  dd($req_data);
        $wrapped_links["HOLDING"] = $req_data; 
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
            $xml, $CYID_REF, $BRID_REF, $FYID_REF, $VTID_REF, $USERID, Date('Y-m-d'), Date('h:i:s.u'),  
            $ACTIONNAME, $IPADDRESS
                ];
    
            $sp_result = DB::select('EXEC SP_WITHHOLDING_UP ?,?,?,?,?,?,?,?,?,?',  $log_data);       


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

    
            for ($i=0; $i<=$r_count; $i++)
    {
        if((isset($request['CODE_'.$i])))
        {

            $req_data[$i] = [
     
                'CODE'    => strtoupper($request['CODE_'.$i]),
                'CODE_DESC' => $request['CODE_DESC_'.$i],
                'SECTIONID_REF' => $request['SECTIONID_REF_'.$i],
                'ASSESSEEID_REF' => $request['ASSESSEEID_REF_'.$i],
                'BASE_TYPE' => $request['BASE_TYPE_'.$i],
                'APPLICABLE_FRDT' => $request['APPLICABLE_FRDT_'.$i],
                'TDS_RATE' => $request['TDS_RATE_'.$i],
                'TDS_EXEMP_LIMIT' => (($request['TDS_EXEMP_LIMIT_'.$i])=="" ? 0 : $request['TDS_EXEMP_LIMIT_'.$i]),
                'SURCHARGE_RAGE' => (($request['SURCHARGE_RAGE_'.$i])=="" ? 0 : $request['SURCHARGE_RAGE_'.$i]),
                'SURCHARGE_EXEMP_LIMIT' => (($request['SURCHARGE_EXEMP_LIMIT_'.$i])=="" ? 0 : $request['SURCHARGE_EXEMP_LIMIT_'.$i]),
                'CESS_RATE' => (($request['CESS_RATE_'.$i])=="" ? 0 : $request['CESS_RATE_'.$i]),
                'CESS_EXEMP_LIMIT' => (($request['CESS_EXEMP_LIMIT_'.$i])=="" ? 0 : $request['CESS_EXEMP_LIMIT_'.$i]),
                'SP_CESS_RATE' => (($request['SP_CESS_RATE_'.$i])=="" ? 0 : $request['SP_CESS_RATE_'.$i]),
                'SP_CESS_EXEMP_LIMIT' => (($request['SP_CESS_EXEMP_LIMIT_'.$i])=="" ? 0 : $request['SP_CESS_EXEMP_LIMIT_'.$i]),
                'TDS_GLID_REF' => $request['TDS_GLID_REF_'.$i],
                'SURCHARGE_GLID_REF' => $request['SURCHARGE_GLID_REF_'.$i],
                'CESS_GLID_REF' => $request['CESS_GLID_REF_'.$i],
                'SP_CESS_GLID_REF' => $request['SP_CESS_GLID_REF_'.$i],
                'RETURN_TYPE' => $request['RETURN_TYPE_'.$i],                   
                'DEACTIVATED' => (isset($request['DEACTIVATED_'.$i])!="true" ? 0 : 1) ,
                'DODEACTIVATED' => !(is_null($request['DODEACTIVATED_'.$i])||empty($request['DODEACTIVATED_'.$i]))=="true"? $request['DODEACTIVATED_'.$i] : NULL,
            ];
        }
    }
            
            $wrapped_links["HOLDING"] = $req_data; 
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
        $xml,$CYID_REF, $BRID_REF, $FYID_REF, $VTID_REF, $USERID, Date('Y-m-d'), Date('h:i:s.u'),  
        $ACTIONNAME, $IPADDRESS
        ];

            
        $sp_result = DB::select('EXEC SP_WITHHOLDING_UP ?,?,?,?,?,?,?,?,?,?',  $log_data);       


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
                $TABLE      =   "TBL_MST_WITHHOLDING";
                $FIELD      =   "HOLDINGID";
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
 

   //save data
        $id = $request->{0};

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_WITHHOLDING";
        $FIELD      =   "HOLDINGID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $canceldata[0]=[
            'NT'  => 'TBL_MST_WITHHOLDING',
       ];        
       $links["TABLES"] = $canceldata; 
       $cancelxml = ArrayToXml::convert($links);
        
        $mst_cancel_data = [$USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS, $cancelxml ];
  

        $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);
        //dd($sp_result); 

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
    
    $destinationPath = "E:/company".$CYID_REF."/WithholdingTaxMaster";

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
        return redirect()->route("master",[166,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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

        return redirect()->route("master",[166,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("master",[166,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("master",[166,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

    public function checkCode(Request $request){

       
        $code=strtoupper($request['code_value']);
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
     
        
        
        $objLabel = DB::table('TBL_MST_WITHHOLDING')
        ->where('TBL_MST_WITHHOLDING.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_WITHHOLDING.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_MST_WITHHOLDING.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_MST_WITHHOLDING.CODE','=',$code)
        ->select('TBL_MST_WITHHOLDING.HOLDINGID')
        ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate CODE']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }
    
}
