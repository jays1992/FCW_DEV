<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
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

class MstFrm199Controller extends Controller
{
    protected $form_id = 199;
    protected $vtid_ref   = 213;  //voucher type id
    // //validation messages

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
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');    

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

            
                        // $objDataList = DB::table('TBL_MST_CALCULATION_BASIS')    
                        // ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        // ->where('BRID_REF','=',Session::get('BRID_REF'))        
                        // ->select('TBL_MST_CALCULATION_BASIS.*')
                        // ->orderBy('TBL_MST_CALCULATION_BASIS.CAL_BASISID','DESC')
                        // ->get();


                        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
                        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;

                        $objDataList	=	DB::select("select hdr.CAL_BASISID,hdr.BASIS_DOC_NO,hdr.BASIS_DOC_DT,hdr.INDATE,hdr.STATUS,
                        case when a.ACTIONNAME = '$FANO' then 'Final Approved' 
                        else case when a.ACTIONNAME = 'ADD' then 'Added'  
                            when a.ACTIONNAME = 'EDIT' then 'Edited'
                            when a.ACTIONNAME = 'APPROVAL1' then 'First Level Approved'
                            when a.ACTIONNAME = 'APPROVAL2' then 'Second Level Approved'
                            when a.ACTIONNAME = 'APPROVAL3' then 'Third Level Approved'
                            when a.ACTIONNAME = 'APPROVAL4' then 'Fourth Level Approved'
                            when a.ACTIONNAME = 'APPROVAL5' then 'Final Approved' 
                        when a.ACTIONNAME = 'CANCEL' then 'Cancelled'
                        end end as STATUS_DESC
                        from TBL_MST_CALCULATION_BASIS hdr 
                        inner join TBL_MST_AUDITTRAIL a                        
                        on a.VID = hdr.CAL_BASISID  
                        and a.VTID_REF = hdr.VTID_REF            
                        and a.CYID_REF = hdr.CYID_REF 
                        and a.BRID_REF = hdr.BRID_REF
                        where a.VTID_REF = '$this->vtid_ref'
                        and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                        and a.ACTID in (select max(ACTID) from TBL_MST_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                        ORDER BY hdr.CAL_BASISID DESC ");

                     // dd($objDataList);               
        
        return view('masters.Payroll.Calculation_Basis.mstfrm199',compact(['objRights','FormId','objDataList']));        
    }

    public function add(){       
       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');        

              


            $objDD = DB::table('TBL_MST_DOCNO_DEFINITION')
            ->where('VTID_REF','=',$this->vtid_ref)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('FYID_REF','=',$FYID_REF)
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_DOCNO_DEFINITION.*')
            ->first();

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

       

            $objlastdt          =   $this->getLastdt();
      
        return view('masters.Payroll.Calculation_Basis.mstfrm199add',compact(['objDD','objDOCNO','objlastdt']));   
        
   }

   
   public function codeduplicate(Request $request){

        $BASIS_DOC_NO  =  strtoupper(trim($request['BASIS_DOC_NO']));
        $objLabel = DB::table('TBL_MST_CALCULATION_BASIS')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('BASIS_DOC_NO','=',$BASIS_DOC_NO)
        ->select('BASIS_DOC_NO')->first();

        if($objLabel){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }        
        exit();
    }


    public function get_EarningHead(Request $request){
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $fieldid        =   $request['fieldid'];
    
        $ObjData        =   DB::table('TBL_MST_EARNING_HEAD')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('BRID_REF','=',Session::get('BRID_REF'))
                            ->where('FYID_REF','=',Session::get('FYID_REF'))
                            ->where('STATUS','=','A')
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('EARNING_HEADID AS ID','EARNING_HEADCODE AS CODE','EARNING_HEAD_DESC AS DESC')
                            ->get();
    
        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){
    
                $row            =   '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="socode_'.$dataRow->ID .'"  class="clssEHid" value="'.$dataRow->ID.'" ></td>
                <td class="ROW2">'.$dataRow->CODE;
                $row = $row.'<input type="hidden" id="txtsocode_'.$dataRow->ID.'" data-desc="'.$dataRow->CODE.'" data-desc1="'.$dataRow->DESC.'"  value="'.$dataRow->ID.'"/></td>
                <td class="ROW3" >'.$dataRow->DESC.'</td></tr>';
                echo $row;
                
            }
    
        }else{
            echo '<tr><td>Record not found.</td></tr>';
        }
        exit();   
    }

   


public function attachment($id){

        if(!is_null($id)){
        
            $FormId     =   $this->form_id;

            $objResponse = DB::table('TBL_MST_CALCULATION_BASIS')->where('CAL_BASISID','=',$id)->first();

            $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
            ->where('VTID','=',$this->vtid_ref)
            ->select('VTID','VCODE','DESCRIPTIONS')
            ->get()
            ->toArray();

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

            return view('masters.Payroll.Calculation_Basis.mstfrm199attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments']));
        }

    }

    public function docuploads(Request $request){

        $FormId     =   $this->form_id;

        $formData = $request->all();

        $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
        $allow_size = config("erpconst.attachments.max_size") * 1020 * 1024;

       
        $VTID           =   $formData["VTID_REF"]; 
        $ATTACH_DOCNO   =   $formData["ATTACH_DOCNO"]; 
        $ATTACH_DOCDT   =   $formData["ATTACH_DOCDT"]; 
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');       
       
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();
        
		//$destinationPath = storage_path()."/docs/company".$CYID_REF."/JobWorkOrder";
        $image_path         =   "docs/company".$CYID_REF."/CalculationBasis";     
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
                    
                   

                    $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
                    $filesize               =   $uploadedFile ->getSize();  
                    $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );

                   

                    $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".$filenamewithextension;  

                    if ($uploadedFile->isValid()) {

                        if(in_array($extension,$allow_extnesions)){
                            
                            if($filesize < $allow_size){

                                $filename = $destinationPath."/".$filenametostore;

                                if (!file_exists($filename)) {

                                   $uploadedFile->move($destinationPath, $filenametostore);  
                                   $uploaded_data[$index]["FILENAME"] =$filenametostore;
                                   $uploaded_data[$index]["LOCATION"] = $image_path."/";
                                   $uploaded_data[$index]["REMARKS"] = is_null($row_val) ? '' : trim($row_val);

                                }else{

                                    $duplicate_files = " ". $duplicate_files.$filenamewithextension. " ";
                                }
                                

                                
                            }else{
                                
                                $invlid_files = $invlid_files.$filenamewithextension." (invalid size)  "; 
                            } 
                            
                        }else{

                            $invlid_files = $invlid_files.$filenamewithextension." (invalid extension)  ";                             
                        }
                    
                    }else{
                            
                        $invlid_files = $invlid_files.$filenamewithextension." (invalid)"; 
                    }

                }

        }

      
        if(empty($uploaded_data)){
            return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
        }
     

        $wrapped_links["ATTACHMENT"] = $uploaded_data;     
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
        

        $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);     

        if($sp_result[0]->RESULT=="SUCCESS"){

            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }

            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }

            return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            
            return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
       
    }

    
   public function save(Request $request) {   
        $r_count1 = $request['Row_Count1'];
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['EHID_REF_'.$i]))
            {
                $req_data[$i] = [
                    'EARNING_HEADID_REF'        => $request['EHID_REF_'.$i],
                    'PF' => (isset($request['PF_'.$i])!="true" ? 0 : 1) ,
                    'VPF' => (isset($request['VPF_'.$i])!="true" ? 0 : 1) ,
                    'ESI' => (isset($request['ESI_'.$i])!="true" ? 0 : 1) ,
                    'BONUS' => (isset($request['Bonus_'.$i])!="true" ? 0 : 1) ,
                    'OT' => (isset($request['OT_'.$i])!="true" ? 0 : 1) ,
                    'GRATUITY' => (isset($request['Gratuity_'.$i])!="true" ? 0 : 1) ,
                    'WELFARE_FUND' => (isset($request['Welfare_Fund_'.$i])!="true" ? 0 : 1) ,
                    'TDS' => (isset($request['TDS_'.$i])!="true" ? 0 : 1) ,
                    'PT' => (isset($request['PT_'.$i])!="true" ? 0 : 1) ,
                    'LWP' => (isset($request['LWP_'.$i])!="true" ? 0 : 1) ,
                    'EARNED_LEAVE' => (isset($request['Earned_Leave_'.$i])!="true" ? 0 : 1) ,
                    'INCENTIVE' => (isset($request['Incentive_'.$i])!="true" ? 0 : 1) ,
                    'SUPER_ANNUATION' => (isset($request['Super_Anuation_'.$i])!="true" ? 0 : 1) ,
                    'OTHER1' => (isset($request['OTHER1_'.$i])!="true" ? 0 : 1) ,
                    'OTHER2' => (isset($request['OTHER2_'.$i])!="true" ? 0 : 1) ,
                    'OTHER3' => (isset($request['OTHER3_'.$i])!="true" ? 0 : 1) ,
                    'OTHER4' => (isset($request['OTHER4_'.$i])!="true" ? 0 : 1) ,
                    'OTHER5' => (isset($request['OTHER5_'.$i])!="true" ? 0 : 1) ,                    
                ];
            }
        }

     
        
            $wrapped_links["BASIS"] = $req_data; 
            $XMLBASIS = ArrayToXml::convert($wrapped_links);   
            $VTID     =   $this->vtid_ref;       
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $BASIS_DOC_NO = $request['BASIS_DOC_NO'];
            $BASIS_DOC_DT = $request['BASIS_DOC_DT'];
            $DEACTIVATED    =   NULL;  
            $DODEACTIVATED  =   NULL;  



            $log_data = [ 
                $BASIS_DOC_NO, $BASIS_DOC_DT,$DEACTIVATED,$DODEACTIVATED, $CYID_REF, $BRID_REF,$FYID_REF,$XMLBASIS, $VTID,  $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];

           // dd($log_data); 
                    
    

            
            $sp_result = DB::select('EXEC SP_CALCULATION_BASIS_IN ?,?,?,?,?,   ?,?,?,?,?,   ?,?,?,?', $log_data);       
           // dd($sp_result);
                  
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            
            exit();   
        
    
     }



    public function edit($id){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
       // DD($CYID_REF.$BRID_REF.$FYID_REF.$id);

       
        
        if(!is_null($id))
        {

            $objRights = DB::table('TBL_MST_USERROLMAP')
                        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
                        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                        ->first();


            $objHeader = DB::table('TBL_MST_CALCULATION_BASIS')                          
                        ->where('TBL_MST_CALCULATION_BASIS.CYID_REF','=',$CYID_REF)
                        ->where('TBL_MST_CALCULATION_BASIS.BRID_REF','=',$BRID_REF)
                        ->where('TBL_MST_CALCULATION_BASIS.CAL_BASISID','=',$id)
                        ->select('TBL_MST_CALCULATION_BASIS.*')
                        ->first();

                  //  dd($objHeader);                                     
                

             $objMAT = DB::table('TBL_MST_CALCULATION_BASIS_DETAILS')                    
                        ->where('TBL_MST_CALCULATION_BASIS_DETAILS.CAL_BASISID_REF','=',$id) 
                        ->leftJoin('TBL_MST_EARNING_HEAD','TBL_MST_CALCULATION_BASIS_DETAILS.EARNING_HEADID_REF','=','TBL_MST_EARNING_HEAD.EARNING_HEADID')                
                        ->select('TBL_MST_CALCULATION_BASIS_DETAILS.*','TBL_MST_EARNING_HEAD.EARNING_HEADCODE','TBL_MST_EARNING_HEAD.EARNING_HEAD_DESC')
                        ->orderBy('TBL_MST_CALCULATION_BASIS_DETAILS.BASISID','ASC')
                        ->get()->toArray();
                                                      
            $objCount1 = count($objMAT);    

        return view('masters.Payroll.Calculation_Basis.mstfrm199edit',compact(['objHeader','objRights','objCount1','objMAT']));
        }
     
       }



    public function view($id){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
       // DD($CYID_REF.$BRID_REF.$FYID_REF.$id);

       
        
        if(!is_null($id))
        {

            $objRights = DB::table('TBL_MST_USERROLMAP')
                        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
                        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                        ->first();


            $objHeader = DB::table('TBL_MST_CALCULATION_BASIS')                          
                        ->where('TBL_MST_CALCULATION_BASIS.CYID_REF','=',$CYID_REF)
                        ->where('TBL_MST_CALCULATION_BASIS.BRID_REF','=',$BRID_REF)
                        ->where('TBL_MST_CALCULATION_BASIS.CAL_BASISID','=',$id)
                        ->select('TBL_MST_CALCULATION_BASIS.*')
                        ->first();

                  //  dd($objHeader);                                     
                

             $objMAT = DB::table('TBL_MST_CALCULATION_BASIS_DETAILS')                    
                        ->where('TBL_MST_CALCULATION_BASIS_DETAILS.CAL_BASISID_REF','=',$id) 
                        ->leftJoin('TBL_MST_EARNING_HEAD','TBL_MST_CALCULATION_BASIS_DETAILS.EARNING_HEADID_REF','=','TBL_MST_EARNING_HEAD.EARNING_HEADID')                
                        ->select('TBL_MST_CALCULATION_BASIS_DETAILS.*','TBL_MST_EARNING_HEAD.EARNING_HEADCODE','TBL_MST_EARNING_HEAD.EARNING_HEAD_DESC')
                        ->orderBy('TBL_MST_CALCULATION_BASIS_DETAILS.BASISID','ASC')
                        ->get()->toArray();
                                                      
            $objCount1 = count($objMAT);    

        return view('masters.Payroll.Calculation_Basis.mstfrm199view',compact(['objHeader','objRights','objCount1','objMAT']));
        }
     
       }


     

    //update the data
   


    public function update(Request $request){
        
       

        $r_count1 = $request['Row_Count1'];

        
    
  
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['EHID_REF_'.$i]))
            {
                $req_data[$i] = [
                    'EARNING_HEADID_REF'         => $request['EHID_REF_'.$i],
                    'PF' => (isset($request['PF_'.$i])!="true" ? 0 : 1) ,
                    'VPF' => (isset($request['VPF_'.$i])!="true" ? 0 : 1) ,
                    'ESI' => (isset($request['ESI_'.$i])!="true" ? 0 : 1) ,
                    'BONUS' => (isset($request['Bonus_'.$i])!="true" ? 0 : 1) ,
                    'OT' => (isset($request['OT_'.$i])!="true" ? 0 : 1) ,
                    'GRATUITY' => (isset($request['Gratuity_'.$i])!="true" ? 0 : 1) ,
                    'WELFARE_FUND' => (isset($request['Welfare_Fund_'.$i])!="true" ? 0 : 1) ,
                    'TDS' => (isset($request['TDS_'.$i])!="true" ? 0 : 1) ,
                    'PT' => (isset($request['PT_'.$i])!="true" ? 0 : 1) ,
                    'LWP' => (isset($request['LWP_'.$i])!="true" ? 0 : 1) ,
                    'EARNED_LEAVE' => (isset($request['Earned_Leave_'.$i])!="true" ? 0 : 1) ,
                    'INCENTIVE' => (isset($request['Incentive_'.$i])!="true" ? 0 : 1) ,
                    'SUPER_ANNUATION' => (isset($request['Super_Anuation_'.$i])!="true" ? 0 : 1) ,
                    'OTHER1' => (isset($request['OTHER1_'.$i])!="true" ? 0 : 1) ,
                    'OTHER2' => (isset($request['OTHER2_'.$i])!="true" ? 0 : 1) ,
                    'OTHER3' => (isset($request['OTHER3_'.$i])!="true" ? 0 : 1) ,
                    'OTHER4' => (isset($request['OTHER4_'.$i])!="true" ? 0 : 1) ,
                    'OTHER5' => (isset($request['OTHER5_'.$i])!="true" ? 0 : 1) ,                    
                ];
            }
        }
     
        
            $wrapped_links["BASIS"] = $req_data; 
            $XMLBASIS = ArrayToXml::convert($wrapped_links);   
            $VTID     =   $this->vtid_ref;       
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'EDIT';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $BASIS_DOC_NO = $request['BASIS_DOC_NO'];
            $BASIS_DOC_DT = $request['BASIS_DOC_DT'];
            $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;       
            $newDateString = NULL;    
            $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL;     
            if(!is_null($newdt) ){                
                $newdt = str_replace( "/", "-",  $newdt ) ;    
                $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
            }
            $DODEACTIVATED = $newDateString;
            $log_data = [ 
                $BASIS_DOC_NO, $BASIS_DOC_DT,$DEACTIVATED,$DODEACTIVATED, $CYID_REF, $BRID_REF,$FYID_REF,$XMLBASIS, $VTID,  $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];
            
            $sp_result = DB::select('EXEC SP_CALCULATION_BASIS_UP ?,?,?,?,?,   ?,?,?,?,?,   ?,?,?,?', $log_data);       
           // dd($sp_result);
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');    
            if($contains){
                return Response::json(['success' =>true,'msg' => 'Document No '.$BASIS_DOC_NO. ' Sucessfully Updated.']);
    
            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            exit();   


      }

    //update the data


    public function Approve(Request $request){
        // dd($request->all());
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
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['EHID_REF_'.$i]))
            {
                $req_data[$i] = [
                    'EARNING_HEADID_REF'         => $request['EHID_REF_'.$i],
                    'PF' => (isset($request['PF_'.$i])!="true" ? 0 : 1) ,
                    'VPF' => (isset($request['VPF_'.$i])!="true" ? 0 : 1) ,
                    'ESI' => (isset($request['ESI_'.$i])!="true" ? 0 : 1) ,
                    'BONUS' => (isset($request['Bonus_'.$i])!="true" ? 0 : 1) ,
                    'OT' => (isset($request['OT_'.$i])!="true" ? 0 : 1) ,
                    'GRATUITY' => (isset($request['Gratuity_'.$i])!="true" ? 0 : 1) ,
                    'WELFARE_FUND' => (isset($request['Welfare_Fund_'.$i])!="true" ? 0 : 1) ,
                    'TDS' => (isset($request['TDS_'.$i])!="true" ? 0 : 1) ,
                    'PT' => (isset($request['PT_'.$i])!="true" ? 0 : 1) ,
                    'LWP' => (isset($request['LWP_'.$i])!="true" ? 0 : 1) ,
                    'EARNED_LEAVE' => (isset($request['Earned_Leave_'.$i])!="true" ? 0 : 1) ,
                    'INCENTIVE' => (isset($request['Incentive_'.$i])!="true" ? 0 : 1) ,
                    'SUPER_ANNUATION' => (isset($request['Super_Anuation_'.$i])!="true" ? 0 : 1) ,
                    'OTHER1' => (isset($request['OTHER1_'.$i])!="true" ? 0 : 1) ,
                    'OTHER2' => (isset($request['OTHER2_'.$i])!="true" ? 0 : 1) ,
                    'OTHER3' => (isset($request['OTHER3_'.$i])!="true" ? 0 : 1) ,
                    'OTHER4' => (isset($request['OTHER4_'.$i])!="true" ? 0 : 1) ,
                    'OTHER5' => (isset($request['OTHER5_'.$i])!="true" ? 0 : 1) ,                    
                ];
            }
        }
     
        
            $wrapped_links["BASIS"] = $req_data; 
            $XMLBASIS = ArrayToXml::convert($wrapped_links);   
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = $Approvallevel;
            $IPADDRESS = $request->getClientIp();   
            $BASIS_DOC_NO = $request['BASIS_DOC_NO'];
            $BASIS_DOC_DT = $request['BASIS_DOC_DT'];
            $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;       
            $newDateString = NULL;    
            $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL;     
            if(!is_null($newdt) ){                
                $newdt = str_replace( "/", "-",  $newdt ) ;    
                $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
            }
            $DODEACTIVATED = $newDateString;
            $log_data = [ 
                $BASIS_DOC_NO, $BASIS_DOC_DT,$DEACTIVATED,$DODEACTIVATED, $CYID_REF, $BRID_REF,$FYID_REF,$XMLBASIS, $VTID_REF,  $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];
            
            $sp_result = DB::select('EXEC SP_CALCULATION_BASIS_UP ?,?,?,?,?,   ?,?,?,?,?,   ?,?,?,?', $log_data);       
           // dd($sp_result);
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');    
            if($contains){
                return Response::json(['success' =>true,'msg' => 'Document No '.$BASIS_DOC_NO. ' Sucessfully approved.']);
    
            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
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
                $TABLE      =   "TBL_MST_CALCULATION_BASIS";
                $FIELD      =   "CAL_BASISID";
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
            $TABLE      =   "TBL_MST_CALCULATION_BASIS";
            $FIELD      =   "CAL_BASISID";
            $ID         =   $id;
            $UPDATE     =   Date('Y-m-d');
            $UPTIME     =   Date('h:i:s.u');
            $IPADDRESS  =   $request->getClientIp();
    
            $req_data[0]=[
                'NT'  => 'TBL_MST_CALCULATION_BASIS_DETAILS',
            ];
          
            $wrapped_links["TABLES"] = $req_data; 
            
            $XMLTAB = ArrayToXml::convert($wrapped_links);
            
         
    
            $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$XMLTAB];
           // dd($mst_cancel_data);
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

  
  


public function getLastdt(){
    $Status = "A";
    $CYID_REF = Auth::user()->CYID_REF;
    $BRID_REF = Session::get('BRID_REF');
    $FYID_REF = Session::get('FYID_REF');
    return  DB::select('SELECT MAX(BASIS_DOC_DT) BASIS_DOC_DT FROM TBL_MST_CALCULATION_BASIS  
    WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND  STATUS = ?', 
    [$CYID_REF, $BRID_REF, $FYID_REF, 'A']);
}

    
}
