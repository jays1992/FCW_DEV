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

class MstFrm377Controller extends Controller
{
    protected $form_id = 377;
    protected $vtid_ref   = 463;  //voucher type id
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

            
                        $objDataList = DB::table('TBL_MST_LEAVE_RULE')    
                        ->where('TBL_MST_LEAVE_RULE.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_LEAVE_RULE.BRID_REF','=',$BRID_REF)
                        ->leftJoin('TBL_MST_PAY_PERIOD', 'TBL_MST_LEAVE_RULE.FYID_REF','=','TBL_MST_PAY_PERIOD.PAYPERIODID')
                        ->select('TBL_MST_LEAVE_RULE.*','TBL_MST_PAY_PERIOD.PAY_PERIOD_CODE','TBL_MST_PAY_PERIOD.PAY_PERIOD_DESC')
                        ->orderBy('TBL_MST_LEAVE_RULE.LRULEID','DESC')
                        ->get();      
        
        return view('masters.Payroll.LeaveRules.mstfrm377',compact(['objRights','FormId','objDataList']));        
    }

    public function add(){       
       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');  
        $FormId  = $this->form_id;   
      
        return view('masters.Payroll.LeaveRules.mstfrm377add',compact(['FormId']));   
        
   }

    
   public function save(Request $request) {   

  //  dd($request->all()); 
  
        $r_count1 = $request['Row_Count1'];
       // dd($r_count1); 
        $r_count2 = $request['Row_Count2'];
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['LTID_REF_'.$i]))
            {
                if(isset($request['DEACTIVATED_'.$i])){
                    $deactivated_status=1;
                }else{
                    $deactivated_status=0;
                }

                $req_data[$i] = [
                    'LTID_REF'              => $request['LTID_REF_'.$i],
                    'YEARLEAVE_MAX'         => $request['MAX_LEAVE_YEAR_'.$i],
                    'CUMM_LEAVE_MAX'        => $request['MAX_COMMULATIVE_LEAVE_'.$i],
                    'CARRY_FW'              => (isset($request['CARRRY_FORWARD_LEAVE_'.$i])!="true" ? 0 : 1) ,
                    'HALF_DAY'              => (isset($request['HALF_DAY_LEAVE_'.$i])!="true" ? 0 : 1) ,
                    'LAPES_EOY'             => (isset($request['LAPSE_LEAVE_'.$i])!="true" ? 0 : 1) ,
                    'ENCASHMENT'            => (isset($request['LEAVE_ENCASHMENT_'.$i])!="true" ? 0 : 1) ,
                    'SANDWICH'              => (isset($request['SANDWITCH_LEAVE_'.$i])!="true" ? 0 : 1) ,
                    'NON_DUE_LEAVE'         => (isset($request['NON_DUE_LEAVE_'.$i])!="true" ? 0 : 1) ,
                    'DESG_SPECI'            =>(isset($request['DESIGNATION_SPECIFIC_'.$i])!="true" ? 0 : 1) ,
                    'REQUIRED_DOC'          => (isset($request['DOCUMENT_REQUIRED_'.$i])!="true" ? 0 : 1) ,
                    'GENDER_SPECI'          => $request['GENDER_SPECIFIC_'.$i],
                    'GENDER_SPECI_MAX'      => $request['MAX_LEAVE_GENDER_'.$i],
                    'MAX_KIDS'              => $request['MAX_LEAVE_KID_'.$i],
                    'DEACTIVATED'           => (isset($request['DEACTIVATED_'.$i])!="true" ? 0 : 1) ,
                    'DODEACTIVATED'           => ($deactivated_status==1 ? $request['DODEACTIVATED_'.$i] : NULL) ,
                               
                ];
            }
        }


      // dd($req_data); 
        


        for ($i=0; $i<=$r_count2; $i++)
        {
            if(isset($request['LTID_REF1_'.$i]))
            {
                $req_data1[$i] = [
                    'LTID_REF'        => $request['LTID_REF1_'.$i],
                    'DESGID_REF'      => $request['DID_REF_'.$i],
                    'YEARLEAVE_MAX'   => $request['MAX_LEAVE_YEAR1_'.$i],
                    'CUMM_LEAVE_MAX'  => $request['MAX_COMMULATIVE_LEAVE1_'.$i],
                                       
                ];
            }
        }

     
     
            $wrapped_links["LEAVERULE"] = $req_data; 
            $XMLLEAVERULE = ArrayToXml::convert($wrapped_links);  


            if(isset($req_data1)) { 
                $wrapped_links1["DESIGNATIONSPECIFIC"] = $req_data1; 
                $XMLDESIGNATIONSPECIFIC = ArrayToXml::convert($wrapped_links1);  
            }
            else {
                $XMLDESIGNATIONSPECIFIC = NULL; 
            } 
        
            
            
       



            $VTID     =   $this->vtid_ref;       
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $FYID_REF = $request['PERIOD_REF1'];     

            $log_data = [ 
                $CYID_REF, $BRID_REF,$FYID_REF,$XMLLEAVERULE, $XMLDESIGNATIONSPECIFIC
            ];

                
            $sp_result = DB::select('EXEC SP_LEAVE_RULE_INUPDE ?,?,?,?,?', $log_data);      

                  
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

             $objLeaveRule = DB::table('TBL_MST_LEAVE_RULE')                    
                        ->where('TBL_MST_LEAVE_RULE.FYID_REF','=',$id) 
                        ->leftJoin('TBL_MST_LEAVE_TYPE','TBL_MST_LEAVE_RULE.LTID_REF','=','TBL_MST_LEAVE_TYPE.LTID')                
                        ->leftJoin('TBL_MST_PAY_PERIOD','TBL_MST_LEAVE_RULE.FYID_REF','=','TBL_MST_PAY_PERIOD.PAYPERIODID')                
                        ->select('TBL_MST_LEAVE_RULE.*','TBL_MST_LEAVE_TYPE.LEAVETYPE_CODE','TBL_MST_LEAVE_TYPE.LEAVETYPE_DESC','TBL_MST_PAY_PERIOD.PAY_PERIOD_CODE','TBL_MST_PAY_PERIOD.PAY_PERIOD_DESC')
                        ->orderBy('TBL_MST_LEAVE_RULE.LRULEID','ASC')
                        ->get()->toArray();

                   

                    
            $objCount1 = count($objLeaveRule);   


             $objDesignation_data = DB::table('TBL_MST_LEAVE_RULE_DESG')                    
                        ->where('TBL_MST_LEAVE_RULE_DESG.FYID_REF','=',$id) 
                        ->leftJoin('TBL_MST_LEAVE_TYPE','TBL_MST_LEAVE_RULE_DESG.LTID_REF','=','TBL_MST_LEAVE_TYPE.LTID')                
                        ->leftJoin('TBL_MST_DESIGNATION','TBL_MST_LEAVE_RULE_DESG.DESGID_REF','=','TBL_MST_DESIGNATION.DESGID')                
                        ->select('TBL_MST_LEAVE_RULE_DESG.*','TBL_MST_LEAVE_TYPE.LEAVETYPE_CODE','TBL_MST_LEAVE_TYPE.LEAVETYPE_DESC','TBL_MST_DESIGNATION.DESGCODE','TBL_MST_DESIGNATION.DESCRIPTIONS')
                        ->orderBy('TBL_MST_LEAVE_RULE_DESG.LRULEDID','ASC')
                        ->get()->toArray();

                        $objCount2 = count($objDesignation_data);   

                        $FormId  = $this->form_id;   

                        $Action_Status="";
                                                      
         

        return view('masters.Payroll.LeaveRules.mstfrm377edit',compact(['objRights','objCount1','objCount2','objLeaveRule','objDesignation_data','FormId','Action_Status']));
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

             $objLeaveRule = DB::table('TBL_MST_LEAVE_RULE')                    
                        ->where('TBL_MST_LEAVE_RULE.FYID_REF','=',$id) 
                        ->leftJoin('TBL_MST_LEAVE_TYPE','TBL_MST_LEAVE_RULE.LTID_REF','=','TBL_MST_LEAVE_TYPE.LTID')                
                        ->leftJoin('TBL_MST_PAY_PERIOD','TBL_MST_LEAVE_RULE.FYID_REF','=','TBL_MST_PAY_PERIOD.PAYPERIODID')                
                        ->select('TBL_MST_LEAVE_RULE.*','TBL_MST_LEAVE_TYPE.LEAVETYPE_CODE','TBL_MST_LEAVE_TYPE.LEAVETYPE_DESC','TBL_MST_PAY_PERIOD.PAY_PERIOD_CODE','TBL_MST_PAY_PERIOD.PAY_PERIOD_DESC')
                        ->orderBy('TBL_MST_LEAVE_RULE.LRULEID','ASC')
                        ->get()->toArray();

                   

                    
            $objCount1 = count($objLeaveRule);   


             $objDesignation_data = DB::table('TBL_MST_LEAVE_RULE_DESG')                    
                        ->where('TBL_MST_LEAVE_RULE_DESG.FYID_REF','=',$id) 
                        ->leftJoin('TBL_MST_LEAVE_TYPE','TBL_MST_LEAVE_RULE_DESG.LTID_REF','=','TBL_MST_LEAVE_TYPE.LTID')                
                        ->leftJoin('TBL_MST_DESIGNATION','TBL_MST_LEAVE_RULE_DESG.DESGID_REF','=','TBL_MST_DESIGNATION.DESGID')                
                        ->select('TBL_MST_LEAVE_RULE_DESG.*','TBL_MST_LEAVE_TYPE.LEAVETYPE_CODE','TBL_MST_LEAVE_TYPE.LEAVETYPE_DESC','TBL_MST_DESIGNATION.DESGCODE','TBL_MST_DESIGNATION.DESCRIPTIONS')
                        ->orderBy('TBL_MST_LEAVE_RULE_DESG.LRULEDID','ASC')
                        ->get()->toArray();

                        $objCount2 = count($objDesignation_data);   

                        $FormId  = $this->form_id;   

                        $Action_Status="disabled";
                                                      
         

        return view('masters.Payroll.LeaveRules.mstfrm377view',compact(['objRights','objCount1','objCount2','objLeaveRule','objDesignation_data','FormId','Action_Status']));
        }
     
       }


       public function codeduplicate(Request $request){

        $FYID_REF  =  strtoupper(trim($request['PERIOD_REF1']));
        $BRID_REF = Session::get('BRID_REF');

        $objLabel = DB::table('TBL_MST_LEAVE_RULE')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)  
        ->where('BRID_REF','=',$BRID_REF)  
        ->where('FYID_REF','=',$FYID_REF)
        //->where('STATUS','=','A')
        ->select('FYID_REF')->first();

        if($objLabel){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }        
        exit();
    }


    public function get_LeaveType(Request $request){
         $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $fieldid        =   $request['fieldid'];
        $class_name        =   $request['class_name'];
    
        $ObjData        =   DB::table('TBL_MST_LEAVE_TYPE')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('BRID_REF','=',Session::get('BRID_REF'))
                            ->where('FYID_REF','=',Session::get('FYID_REF'))
                            ->where('STATUS','=','A')
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('LTID AS ID','LEAVETYPE_CODE AS CODE','LEAVETYPE_DESC AS DESC')
                            ->get();

                         
    
        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){
    
                $row            =   '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="socode_'.$dataRow->ID .'"  class="'.$class_name.'" value="'.$dataRow->ID.'" ></td>
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




    public function get_Designation(Request $request){
         $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $fieldid        =   $request['fieldid'];
        $class_name        =   $request['class_name'];
    
        $ObjData        =   DB::table('TBL_MST_DESIGNATION')
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('BRID_REF','=',Session::get('BRID_REF'))
                            ->where('FYID_REF','=',Session::get('FYID_REF'))
                            ->where('STATUS','=','A')
                            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                            ->select('DESGID AS ID','DESGCODE AS CODE','DESCRIPTIONS AS DESC')
                            ->get();

                         
    
        if(!empty($ObjData)){
            foreach ($ObjData as $index=>$dataRow){
    
                $row            =   '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="socode_'.$dataRow->ID .'"  class="'.$class_name.'" value="'.$dataRow->ID.'" ></td>
                <td class="ROW2">'.$dataRow->CODE;
                $row = $row.'<input type="hidden" id="txtsocode_'.$dataRow->ID.'" data-desc="'.$dataRow->CODE.'-'.$dataRow->DESC.'" data-desc1="'.$dataRow->DESC.'"  value="'.$dataRow->ID.'"/></td>
                <td class="ROW3" >'.$dataRow->DESC.'</td></tr>';
                echo $row;
                
            }
    
        }else{
            echo '<tr><td>Record not found.</td></tr>';
        }
        exit();   
    }



    public function getPeriod(Request $request) {    

        //dd($request->all()); 
         
             $Status = "A";
             $CYID_REF = Auth::user()->CYID_REF;
             $BRID_REF = Session::get('BRID_REF');
             $FYID_REF = Session::get('FYID_REF');
             $PERIOD_TYPE=$request['PERIOD_TYPE'];
    
         
             $objPeriod = DB::table('TBL_MST_PAY_PERIOD')
             ->where('TBL_MST_PAY_PERIOD.CYID_REF','=',Auth::user()->CYID_REF)
             ->where('TBL_MST_PAY_PERIOD.BRID_REF','=',Session::get('BRID_REF'))
             ->where('TBL_MST_PAY_PERIOD.STATUS','=',$Status)
             ->select('*') 
             ->get()    
             ->toArray();
         
       // dd($objPeriod); 
              
         
             if(!empty($objPeriod)){        
                 foreach ($objPeriod as $index=>$dataRow){   
         
                     $row = '';
                     $row = $row.'<tr ><td style="text-align:center; width:10%">';
                     $row = $row.'<input type="checkbox" name="'.$PERIOD_TYPE.'[]"  id="periodcode_'.$dataRow->PAYPERIODID.'" class="clsspid_period" 
                     value="'.$dataRow->PAYPERIODID.'"/>             
                     </td>           
                     <td style="width:30%;">'.$dataRow->PAY_PERIOD_CODE;
                     $row = $row.'<input type="hidden" id="txtperiodcode_'.$dataRow->PAYPERIODID.'" data-code="'.$dataRow->PAY_PERIOD_CODE.'"  
                     data-desc="'.$dataRow->PAY_PERIOD_DESC.'" 
                  
                     value="'.$dataRow->PAYPERIODID.'"/></td>
         
                     <td style="width:60%;">'.$dataRow->PAY_PERIOD_DESC.'</td>
           
         
                    </tr>';
                     echo $row;
                 }
         
                 }else{
                     echo '<tr><td colspan="2">Record not found.</td></tr>';
                 }
         
                 exit();
         
         
         
            }







            
public function attachment($id){

    if(!is_null($id)){
    
        $FormId     =   $this->form_id;

        $objResponse = DB::table('TBL_MST_LEAVE_RULE')->where('FYID_REF','=',$id)->first();

        $objResponse = DB::table('TBL_MST_LEAVE_RULE')                    
        ->where('TBL_MST_LEAVE_RULE.FYID_REF','=',$id)            
        ->leftJoin('TBL_MST_PAY_PERIOD','TBL_MST_LEAVE_RULE.FYID_REF','=','TBL_MST_PAY_PERIOD.PAYPERIODID')                
        ->select('TBL_MST_LEAVE_RULE.*','TBL_MST_PAY_PERIOD.PAY_PERIOD_CODE','TBL_MST_PAY_PERIOD.PAY_PERIOD_DESC')
        ->orderBy('TBL_MST_LEAVE_RULE.LRULEID','ASC')
        ->first();




        //dd($objResponse);

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

        return view('masters.Payroll.LeaveRules.mstfrm377attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments']));
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
    $image_path         =   "docs/company".$CYID_REF."/LeaveRules";     
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






     



   

  

    
}
