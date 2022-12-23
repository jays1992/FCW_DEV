<?php

namespace App\Http\Controllers\Masters;
use App\Helpers\Helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
// use App\Models\Master\TblMstFrm237;

use App\Models\Admin\TblMstUser;
use Auth;
use DB;
use Session;
use Response;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MstFrm237Controller extends Controller
{
    protected $form_id = 237;
    
    protected $vtid_ref   = 327;  //voucher type id
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
       // phpinfo();  
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
                        $objDataList    =   DB::table('TBL_MST_VENDOR_CD_HDR')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->orderBy('VCDID','DESC')
                            ->get();       
                            //dd($objDataList);       
        
        return view('masters.Accounts.CustomDuty.mstfrm237',compact(['objRights','FormId','objDataList']));        
    }

    public function add(){       

        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $FormId         =   $this->form_id;     
        
        return view('masters.Accounts.CustomDuty.mstfrm237add', compact(['FormId']));       
   }


    



    

   //display attachments form
   public function attachment($id){

    if(!is_null($id))
    {
        $objOpenSalesOrder = DB::table("TBL_MST_VENDOR_CD_HDR")
                        ->where('VCDID','=',$id)
                        ->select('TBL_MST_VENDOR_CD_HDR.*')
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

                 // dump( $objAttachments);

            return view('masters.Accounts.CustomDuty.mstfrm237attachment',compact(['objOpenSalesOrder','objMstVoucherType','objAttachments']));
    }

}

    
   public function save(Request $request) {    
        $r_count1 = $request['Row_Count2'];
     
        for ($i=0; $i<=$r_count1; $i++)
        {
                if(isset($request['VENDOR_CODE_'.$i]) && !is_null($request['VENDOR_CODE_'.$i]))
                {
                   
                        $reqdata2[$i] = [                          
                            'VID_REF'    => $request['VENDORID_REF_'.$i],
                            'ITEMID_REF'    => $request['MainItemId1_Ref_'.$i],
                            'BCD'         => (!empty($request['NORMAL_BCD_'.$i]) ? $request['NORMAL_BCD_'.$i] : 0),
                            'CESS_BCD'  =>  (!empty($request['CESS_NORMAL_BCD_'.$i]) ? $request['CESS_NORMAL_BCD_'.$i] : 0),
                            'FTA_BCD'    =>   (!empty($request['FTA_BCD_'.$i]) ? $request['FTA_BCD_'.$i] : 0),
                            'CEPA_BCD'    =>   (!empty($request['CEPA_BCD_'.$i]) ? $request['CEPA_BCD_'.$i] : 0),
                            'SWS'    =>   (!empty($request['SW_RATE_'.$i]) ? $request['SW_RATE_'.$i] : 0),
                            'TAX'    =>   (!empty($request['TAX_'.$i]) ? $request['TAX_'.$i] : 0),                      
                        ];
                    
                }
            
        }


           if(isset($reqdata2))
           { 
            $wrapped_links2["MAT"] = $reqdata2;
            $XMLMAT = ArrayToXml::convert($wrapped_links2);
           }
           else
           {
            $XMLMAT = NULL; 
           }   

        
            $VTID_REF     =   $this->vtid_ref;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $VCD_DOCNO = strtoupper($request['DOCNO']);
            $VCD_DOCDT = $request['CD_DT'];
            $FROM_DT = $request['VALIDITY_FROM'];
            $TO_DT = $request['VALIDITY_TO'];
           
            $DEACTIVATED   =   NULL;  
            $DODEACTIVATED =   NULL;  

           

            $log_data = [ 
                $VCD_DOCNO,$VCD_DOCDT,$FROM_DT,$TO_DT,$DEACTIVATED ,$DODEACTIVATED,$CYID_REF, $BRID_REF,$FYID_REF,$XMLMAT,$VTID_REF,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];
           // dd($log_data); 
    
            $sp_result = DB::select('EXEC SP_VENDOR_CD_IN ?,?,?,? ,?,?,?,? ,?,?,?,?, ?,?,?,?', $log_data);       
            
           // dd($sp_result); 
        
            if($sp_result[0]->RESULT=="SUCCESS"){
    
                return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);
    
            }else if($sp_result[0]->RESULT=="DUPLICATE RECORD"){
                

                return Response::json(['errors' =>true,'msg' => 'Duplicate Record']);

            }else{
                return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
            }
            exit();   
     }


     public function get_tax_details(Request $request){

    
        $HSNID_REF=$request->hsnid_ref;
            $ObjTax =  DB::table('TBL_MST_HSNNORMAL')  
                    ->select('NRATE')
                    ->whereIn('TAXID_REF',function($query) 
                                {       
                                $query->select('TAXID')->from('TBL_MST_TAXTYPE')
                                                ->where('STATUS','=','A')
                                                ->where('FOR_PURCHASE','=',1)
                                                ->where('OUTOFSTATE','=',1);                       
                    })->where('HSNNID','=',$HSNID_REF) 
                    ->get()->toArray();
               

                    if($ObjTax){
                    echo  $tax=$ObjTax[0]->NRATE;
                    }else{
                    echo $tax='0.0000';

                    }   
     
     }


    public function edit($id){
       
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id))
        {
            $objCUSTOM = DB::table('TBL_MST_VENDOR_CD_HDR')
                             ->where('TBL_MST_VENDOR_CD_HDR.FYID_REF','=',Session::get('FYID_REF'))
                             ->where('TBL_MST_VENDOR_CD_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_VENDOR_CD_HDR.BRID_REF','=',Session::get('BRID_REF'))                         
                             ->where('TBL_MST_VENDOR_CD_HDR.VCDID','=',$id)
                             ->select('TBL_MST_VENDOR_CD_HDR.*')
                             ->first();
            if(strtoupper($objCUSTOM->STATUS)=="A" || strtoupper($objCUSTOM->STATUS)=="C"){
               // exit("Sorry, Only Un Approved record can edit.");
            }


            $objCUSTOMMAT = DB::table('TBL_MST_VENDOR_CD_MAT')                    
                             ->where('TBL_MST_VENDOR_CD_MAT.VCDID_REF','=',$id)
                             ->leftJoin('TBL_MST_ITEM', 'TBL_MST_VENDOR_CD_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                                              
                             ->leftJoin('TBL_MST_VENDOR', 'TBL_MST_VENDOR_CD_MAT.VID_REF','=','TBL_MST_VENDOR.SLID_REF')                                              
                             ->orderBy('TBL_MST_VENDOR_CD_MAT.VCD_MATID','ASC')
                             ->select('TBL_MST_VENDOR_CD_MAT.*','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_VENDOR.NAME as VENDOR_NAME','TBL_MST_VENDOR.VCODE')
                             ->get()->toArray();

            $objCount2 = count($objCUSTOMMAT);              

            
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
            
            return view('masters.Accounts.CustomDuty.mstfrm237edit',compact(['objCUSTOM','objRights','objCUSTOMMAT','FormId','objCount2']));
        }
     
       }

     
     
       public function view($id){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id))
        {
            $objCUSTOM = DB::table('TBL_MST_VENDOR_CD_HDR')
                             ->where('TBL_MST_VENDOR_CD_HDR.FYID_REF','=',Session::get('FYID_REF'))
                             ->where('TBL_MST_VENDOR_CD_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_VENDOR_CD_HDR.BRID_REF','=',Session::get('BRID_REF'))                         
                             ->where('TBL_MST_VENDOR_CD_HDR.VCDID','=',$id)
                             ->select('TBL_MST_VENDOR_CD_HDR.*')
                             ->first();



            $objCUSTOMMAT = DB::table('TBL_MST_VENDOR_CD_MAT')                    
                             ->where('TBL_MST_VENDOR_CD_MAT.VCDID_REF','=',$id)
                             ->leftJoin('TBL_MST_ITEM', 'TBL_MST_VENDOR_CD_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                                              
                             ->leftJoin('TBL_MST_VENDOR', 'TBL_MST_VENDOR_CD_MAT.VID_REF','=','TBL_MST_VENDOR.SLID_REF')                                              
                             ->orderBy('TBL_MST_VENDOR_CD_MAT.VCD_MATID','ASC')
                             ->select('TBL_MST_VENDOR_CD_MAT.*','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_VENDOR.NAME as VENDOR_NAME','TBL_MST_VENDOR.VCODE')
                             ->get()->toArray();

                          

            
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
                     
            
                    return view('masters.Accounts.CustomDuty.mstfrm237view',compact(['objCUSTOM','objRights','objCUSTOMMAT']));
        }
      
        }

    //update the data
   public function update(Request $request){

    $r_count1 = $request['Row_Count2'];
     
    for ($i=0; $i<=$r_count1; $i++)
    {
            if(isset($request['VENDOR_CODE_'.$i]) && !is_null($request['VENDOR_CODE_'.$i]))
            {
               
                    $reqdata2[$i] = [                          
                        'VID_REF'    => $request['VENDORID_REF_'.$i],
                        'ITEMID_REF'    => $request['MainItemId1_Ref_'.$i],
                        'BCD'         => (!empty($request['NORMAL_BCD_'.$i]) ? $request['NORMAL_BCD_'.$i] : 0),
                        'CESS_BCD'  =>  (!empty($request['CESS_NORMAL_BCD_'.$i]) ? $request['CESS_NORMAL_BCD_'.$i] : 0),
                        'FTA_BCD'    =>   (!empty($request['FTA_BCD_'.$i]) ? $request['FTA_BCD_'.$i] : 0),
                        'CEPA_BCD'    =>   (!empty($request['CEPA_BCD_'.$i]) ? $request['CEPA_BCD_'.$i] : 0),
                        'SWS'    =>   (!empty($request['SW_RATE_'.$i]) ? $request['SW_RATE_'.$i] : 0),
                        'TAX'    =>   (!empty($request['TAX_'.$i]) ? $request['TAX_'.$i] : 0),                      
                    ];
                
            }
        
    }




       if(isset($reqdata2))
       { 
        $wrapped_links2["MAT"] = $reqdata2;
        $XMLMAT = ArrayToXml::convert($wrapped_links2);
       }
       else
       {
        $XMLMAT = NULL; 
       }   

       $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
       $newDateString = NULL;

       $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

       if(!is_null($newdt) ){
           
           $newdt = str_replace( "/", "-",  $newdt ) ;  

           $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
       }
       

       $DODEACTIVATED = $newDateString;

       $VTID_REF     =   $this->vtid_ref;
       $USERID = Auth::user()->USERID;   
       $ACTIONNAME = 'EDIT';
       $IPADDRESS = $request->getClientIp();
       $CYID_REF = Auth::user()->CYID_REF;
       $BRID_REF = Session::get('BRID_REF');
       $FYID_REF = Session::get('FYID_REF');
       $VCD_DOCNO = strtoupper($request['DOCNO']);
       $VCD_DOCDT = $request['CD_DT'];
       $FROM_DT = $request['VALIDITY_FROM'];
       $TO_DT = $request['VALIDITY_TO'];      

       $log_data = [ 
           $VCD_DOCNO,$VCD_DOCDT,$FROM_DT,$TO_DT,$DEACTIVATED ,$DODEACTIVATED,$CYID_REF, $BRID_REF,$FYID_REF,$XMLMAT,$VTID_REF,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
       ];
      // dd($log_data); 

       $sp_result = DB::select('EXEC SP_VENDOR_CD_UP ?,?,?,? ,?,?,?,? ,?,?,?,?, ?,?,?,?', $log_data);     


    
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


                        $r_count1 = $request['Row_Count2'];
     
                        for ($i=0; $i<=$r_count1; $i++)
                        {
                                if(isset($request['VENDOR_CODE_'.$i]) && !is_null($request['VENDOR_CODE_'.$i]))
                                {
                                   
                                        $reqdata2[$i] = [                          
                                            'VID_REF'    => $request['VENDORID_REF_'.$i],
                                            'ITEMID_REF'    => $request['MainItemId1_Ref_'.$i],
                                            'BCD'         => (!empty($request['NORMAL_BCD_'.$i]) ? $request['NORMAL_BCD_'.$i] : 0),
                                            'CESS_BCD'  =>  (!empty($request['CESS_NORMAL_BCD_'.$i]) ? $request['CESS_NORMAL_BCD_'.$i] : 0),
                                            'FTA_BCD'    =>   (!empty($request['FTA_BCD_'.$i]) ? $request['FTA_BCD_'.$i] : 0),
                                            'CEPA_BCD'    =>   (!empty($request['CEPA_BCD_'.$i]) ? $request['CEPA_BCD_'.$i] : 0),
                                            'SWS'    =>   (!empty($request['SW_RATE_'.$i]) ? $request['SW_RATE_'.$i] : 0),
                                            'TAX'    =>   (!empty($request['TAX_'.$i]) ? $request['TAX_'.$i] : 0),                      
                                        ];
                                    
                                }
                            
                        }
                    
                    
                    
                    
                           if(isset($reqdata2))
                           { 
                            $wrapped_links2["MAT"] = $reqdata2;
                            $XMLMAT = ArrayToXml::convert($wrapped_links2);
                           }
                           else
                           {
                            $XMLMAT = NULL; 
                           }   
                           
                           
                    
                           $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
                           
                           $newDateString = NULL;
                    
                           $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 
                    
                           if(!is_null($newdt) ){
                               
                               $newdt = str_replace( "/", "-",  $newdt ) ;  
                    
                               $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
                           }
                           
                    
                           $DODEACTIVATED = $newDateString;                    
                           $VTID_REF     =   $this->vtid_ref;
                           $USERID = Auth::user()->USERID;   
                           $ACTIONNAME = $Approvallevel;
                           $IPADDRESS = $request->getClientIp();
                           $CYID_REF = Auth::user()->CYID_REF;
                           $BRID_REF = Session::get('BRID_REF');
                           $FYID_REF = Session::get('FYID_REF');
                           $VCD_DOCNO = strtoupper($request['DOCNO']);
                           $VCD_DOCDT = $request['CD_DT'];
                           $FROM_DT = $request['VALIDITY_FROM'];
                           $TO_DT = $request['VALIDITY_TO'];      
                    
                           $log_data = [ 
                               $VCD_DOCNO,$VCD_DOCDT,$FROM_DT,$TO_DT,$DEACTIVATED ,$DODEACTIVATED,$CYID_REF, $BRID_REF,$FYID_REF,$XMLMAT,$VTID_REF,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
                           ];
                          // dd($log_data); 
                    
                           $sp_result = DB::select('EXEC SP_VENDOR_CD_UP ?,?,?,? ,?,?,?,? ,?,?,?,?, ?,?,?,?', $log_data);     
                
            
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
                $TABLE      =   "TBL_MST_VENDOR_CD_HDR";
                $FIELD      =   "VCDID";
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
            
            return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','salesenquiry'=>'norecord']);
            
            }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','salesenquiry'=>'Some Error']);
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
        $TABLE      =   "TBL_MST_VENDOR_CD_HDR";
        $FIELD      =   "VCDID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();       

        $req_data[0]=[
            'NT'  => 'TBL_MST_VENDOR_CD_HDR',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_MST_VENDOR_CD_MAT',
        ];
     
    
      
        $wrapped_links["TABLES"] = $req_data; 
        
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
     

        $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$XMLTAB];
       // dd($mst_cancel_data);
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
    
   // $destinationPath = storage_path()."/docs/company".$CYID_REF."/CustomDuty";
    $image_path         =   "docs/company".$CYID_REF."/CustomDuty";     
    $destinationPath    =   str_replace('\\', '/', public_path($image_path));

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

                $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".$filenamewithextension;  
                
                echo $filenametostore ;

                if ($uploadedFile->isValid()) {

                    if(in_array($extension,$allow_extnesions)){
                        
                        if($filesize < $allow_size){

                            $custfilename = $destinationPath."/".$filenametostore;

                            if (!file_exists($custfilename)) {

                               $uploadedFile->move($destinationPath, $filenametostore);  //upload in dir if not exists
                               $uploaded_data[$index]["FILENAME"] =$filenametostore;
                               $uploaded_data[$index]["LOCATION"] = $image_path."/";
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
        return redirect()->route("master",[237,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
    
      
   try {    

         //save data
         $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);

   } catch (\Throwable $th) {
    
       return redirect()->route("master",[237,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("master",[237,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("master",[237,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("master",[237,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

    public function checkdocno(Request $request){

        // dd($request->LABEL_0);
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $VCD_DOCNO = $request->DOCNO;
      
        
        $objSO = DB::table('TBL_MST_VENDOR_CD_HDR')
        ->where('TBL_MST_VENDOR_CD_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_VENDOR_CD_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_MST_VENDOR_CD_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_MST_VENDOR_CD_HDR.VCD_DOCNO','=',$VCD_DOCNO)
        ->select('TBL_MST_VENDOR_CD_HDR.VCDID')
        ->first();
        
        if($objSO){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate DOCNO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }

    public function getVendor(Request $request){

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $CODE       =   $request['CODE'];
        $NAME       =   $request['NAME'];
    
        $sp_popup = [
            $CYID_REF, $BRID_REF,$CODE,$NAME
        ]; 
        
        $ObjData = DB::select('EXEC sp_get_vendor_popup_enquiry ?,?,?,?', $sp_popup);
    
        if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){
    
                $VID    =   $dataRow->SGLID;
                $VCODE  =   $dataRow->SGLCODE;
                $NAME   =   $dataRow->SLNAME;
                
               
                $row = '';
                $row = $row.'<tr id="vendoridcode_'.$index.'">
                <td class="ROW1"> <input type="checkbox" name="SELECT_VID_REF[]" id="chkVendorId_'.$index.'"  class="clsvendorid" value="'.$VID.'" ></td>
                <td class="ROW2">'.$VCODE.'<input type="hidden" id="txtvendoridcode_'.$index.'" data-desc="'.$VCODE.'-'.$NAME.'" data-vcode="'.$VCODE.'" data-vname="'.$NAME.'"  value="'.$VID.'" > </td>
                <td class="ROW3">'.$NAME.'</td>
                </tr>';
    
                echo $row;
    
            }
    
        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();
    
    }

    public function getItemDetails_main_item22(Request $request){        
        $taxstate = $request['taxstate'];
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $StdCost = 0;
        $Taxid = [];
        $CODE = $request['CODE'];
        $NAME = $request['NAME'];
        $MUOM = $request['MUOM'];
        $GROUP = $request['GROUP'];
        $CTGRY = $request['CTGRY'];
        $BUNIT = $request['BUNIT'];
        $APART = $request['APART'];
        $CPART = $request['CPART'];
        $OPART = $request['OPART'];

        $sp_popup = [
            $CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART
        ]; 
        
            $ObjItem = DB::select('EXEC sp_get_items_popup_enquiry ?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);
            
                if(!empty($ObjItem)){

                    foreach ($ObjItem as $index=>$dataRow){

                        $ITEMID             =   isset($dataRow->ITEMID)?$dataRow->ITEMID:NULL;
                        $ICODE              =   isset($dataRow->ICODE)?$dataRow->ICODE:NULL;
                        $NAME               =   isset($dataRow->NAME)?$dataRow->NAME:NULL;
                        $ITEM_SPECI         =   isset($dataRow->ITEM_SPECI)?$dataRow->ITEM_SPECI:NULL;
                        $MAIN_UOMID_REF     =   isset($dataRow->MAIN_UOMID_REF)?$dataRow->MAIN_UOMID_REF:NULL;
                        $Main_UOM           =   isset($dataRow->Main_UOM)?$dataRow->Main_UOM:NULL;
                        $ALT_UOMID_REF      =   isset($dataRow->ALT_UOMID_REF)?$dataRow->ALT_UOMID_REF:NULL;
                        $Alt_UOM            =   isset($dataRow->Alt_UOM)?$dataRow->Alt_UOM:NULL;
                        $FROMQTY            =   isset($dataRow->FROMQTY)?$dataRow->FROMQTY:NULL;
                        $TOQTY              =   isset($dataRow->TOQTY)?$dataRow->TOQTY:NULL;
                        $STDCOST            =   isset($dataRow->STDCOST)?$dataRow->STDCOST:NULL;
                        $GroupName          =   isset($dataRow->GroupName)?$dataRow->GroupName:NULL;
                        $Categoryname       =   isset($dataRow->Categoryname)?$dataRow->Categoryname:NULL;
                        $BusinessUnit       =   isset($dataRow->BusinessUnit)?$dataRow->BusinessUnit:NULL;
                        $ALPS_PART_NO       =   isset($dataRow->ALPS_PART_NO)?$dataRow->ALPS_PART_NO:NULL;
                        $CUSTOMER_PART_NO   =   isset($dataRow->CUSTOMER_PART_NO)?$dataRow->CUSTOMER_PART_NO:NULL;
                        $OEM_PART_NO        =   isset($dataRow->OEM_PART_NO)?$dataRow->OEM_PART_NO:NULL;
                        
                        
                        $row = '';
                        $row.=' <tr id="item_'.$ITEMID.'" class="clsitemid">
                                <td  style="width:8%; text-align: center;"><input type="checkbox" id="chkIdMainItem'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1"  ></td>
                                <td style="width:10%;">'.$ICODE.'<input type="hidden" id="txtitem_'.$ITEMID.'" data-desc="'.$ICODE.'" value="'.$ITEMID.'"/></td>
                                <td style="width:10%;" id="itemname_'.$ITEMID.'" >'.$NAME.'<input type="hidden" id="txtitemname_'.$ITEMID.'" data-desc="'.$ITEM_SPECI.'" value="'.$NAME.'"/></td>
                                <td style="width:8%;" id="itemuom_'.$ITEMID.'" ><input type="hidden" id="txtitemuom_'.$ITEMID.'" data-desc="'.$Alt_UOM.'" value="'.$MAIN_UOMID_REF.'"/>'.$Main_UOM.'</td>
                                <td style="width:8%;" id="uomqty_'.$ITEMID.'" ><input type="hidden" id="txtuomqty_'.$ITEMID.'" data-desc="'.$TOQTY.'" value="'.$ALT_UOMID_REF.'"/>'.$FROMQTY.'</td>
                                <td style="width:8%;" id="irate_'.$ITEMID.'"><input type="hidden" id="txtirate_'.$ITEMID.'" data-desc="'.$FROMQTY.'" value="'.$STDCOST.'"/>'.$GroupName.'</td>
                                <td style="width:8%;" id="itax_'.$ITEMID.'"><input type="hidden" id="txtitax_'.$ITEMID.'" />'.$Categoryname.'</td>
                                <td style="width:8%;">'.$BusinessUnit.'</td>
                                <td style="width:8%;">'.$ALPS_PART_NO.'</td>
                                <td style="width:8%;">'.$CUSTOMER_PART_NO.'</td>
                                <td style="width:8%;">'.$OEM_PART_NO.'</td>
                                <td style="width:8%;">Authorized</td>
                                </tr>'; 
                        echo $row;    
                    } 
                    
                    // return Response::json($ObjItem);
                }           
                else{
                    echo '<tr><td colspan="12"> Record not found.</td></tr>';
                }
        exit();
    }

    public function getItemDetails_main_item(Request $request){   

        $taxstate = $request['taxstate'];
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $StdCost = 0;
        $Taxid = [];
        $CODE = $request['CODE'];
        $NAME = $request['NAME'];
        $MUOM = $request['MUOM'];
        $GROUP = $request['GROUP'];
        $CTGRY = $request['CTGRY'];
        $BUNIT = $request['BUNIT'];
        $APART = $request['APART'];
        $CPART = $request['CPART'];
        $OPART = $request['OPART'];
        $PARTNO = $request['PARTNO'];
        $DRAWINGNO = $request['DRAWNO'];
        $MATERIAL_TYPE = NULL;

        $sp_popup = [
            $CYID_REF, $BRID_REF, $FYID_REF,$CODE,$NAME,$MUOM,$GROUP,$CTGRY,$BUNIT,$APART,$CPART,$OPART
        ]; 

        
        
        $ObjItem = DB::select('EXEC sp_get_items_popup_enquiry ?,?,?,?,?,?,?,?,?,?,?,?', $sp_popup);
            
                if(!empty($ObjItem)){

                    foreach ($ObjItem as $index=>$dataRow){

                      

                            $ITEMID             =   isset($dataRow->ITEMID)?$dataRow->ITEMID:NULL;
                            $ICODE              =   isset($dataRow->ICODE)?$dataRow->ICODE:NULL;
                            $NAME               =   isset($dataRow->NAME)?$dataRow->NAME:NULL;
                            $ITEM_SPECI         =   isset($dataRow->ITEM_SPECI)?$dataRow->ITEM_SPECI:NULL;
                            $MAIN_UOMID_REF     =   isset($dataRow->MAIN_UOMID_REF)?$dataRow->MAIN_UOMID_REF:NULL;
                            $Main_UOM           =   isset($dataRow->Main_UOM)?$dataRow->Main_UOM:NULL;
                            $ALT_UOMID_REF      =   isset($dataRow->ALT_UOMID_REF)?$dataRow->ALT_UOMID_REF:NULL;
                            $Alt_UOM            =   isset($dataRow->Alt_UOM)?$dataRow->Alt_UOM:NULL;
                            $FROMQTY            =   isset($dataRow->FROMQTY)?$dataRow->FROMQTY:NULL;
                            $TOQTY              =   isset($dataRow->TOQTY)?$dataRow->TOQTY:NULL;
                            $STDCOST            =   isset($dataRow->STDCOST)?$dataRow->STDCOST:NULL;
                            $GroupName          =   isset($dataRow->GroupName)?$dataRow->GroupName:NULL;
                            $Categoryname       =   isset($dataRow->Categoryname)?$dataRow->Categoryname:NULL;
                            $BusinessUnit       =   isset($dataRow->BusinessUnit)?$dataRow->BusinessUnit:NULL;
                            $ALPS_PART_NO       =   isset($dataRow->ALPS_PART_NO)?$dataRow->ALPS_PART_NO:NULL;
                            $CUSTOMER_PART_NO   =   isset($dataRow->CUSTOMER_PART_NO)?$dataRow->CUSTOMER_PART_NO:NULL;
                            $OEM_PART_NO        =   isset($dataRow->OEM_PART_NO)?$dataRow->OEM_PART_NO:NULL;
                            $DRAWING_NO         =   isset($dataRow->DRAWINGNO)?$dataRow->DRAWINGNO:NULL;
                            $PART_NO            =   isset($dataRow->PARTNO)?$dataRow->PARTNO:NULL;
                            $HSNID_REF            =   isset($dataRow->HSNID_REF)?$dataRow->HSNID_REF:0;
                            
                                                     

                            $row = '';
                            $row .='<tr id="item_'.$ITEMID.'" class="clsitem" >
                                    <td style="width:5%;text-align:center;" ><input type="checkbox" id="chkIdMainItem'.$ITEMID.'"  value="'.$ITEMID.'" class="js-selectall1ProdCode"  > </td>
                                    <td style="width:10%;">'.$ICODE.'
                                    <input type="hidden" id="txtitem_'.$ITEMID.'" data-code="'.$ICODE.'" data-uomno="'.$MAIN_UOMID_REF.'" data-name="'.$NAME.'" data-drawingno="'.$DRAWING_NO.'" data-partno="'.$PART_NO.'"    data-uom="'.$Main_UOM.'" data-hsnid="'.$HSNID_REF.'"  value="'.$ITEMID.'"/>
                                    </td>
                                    <td style="width:15%;">'.$NAME.'</td>
                                    <td style="width:10%;">'.$Main_UOM.'</td>
                                    <td style="width:10%;">'.$BusinessUnit.'</td>
                                    <td style="width:10%;">'.$ALPS_PART_NO.'</td>
                                    <td style="width:10%;">'.$CUSTOMER_PART_NO.'</td>
                                    <td style="width:10%;">'.$OEM_PART_NO.'</td>
                                    <td style="width:10%;">'.$DRAWING_NO.'</td>
                                    <td style="width:10%;">'.$PART_NO.'</td>
                                </tr>';
                        
                            echo $row;    
                       
                    } 
                    
                    // return Response::json($ObjItem);
                }           
                else{
                    echo '<tr><td colspan="12"> Record not found.</td></tr>';
                }
        exit();
    }


    
} //class
