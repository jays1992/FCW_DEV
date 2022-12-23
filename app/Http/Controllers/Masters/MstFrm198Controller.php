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

class MstFrm198Controller extends Controller
{
    protected $form_id = 198;
    protected $vtid_ref   = 112;  //voucher type id
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
        $objRights = DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();

        $FormId =   $this->form_id;
            
        $objDataList = DB::table('TBL_MST_ROLE')    
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))        
        ->select('TBL_MST_ROLE.*')
        ->orderBy('TBL_MST_ROLE.ROLLID','DESC')
        ->get();

        return view('masters.Common.RoleMaster.mstfrm198',compact(['objRights','FormId','objDataList']));        
    }

    public function add(){       
       
            $Status = "A";
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');        


            $ModuleList = DB::table('TBL_MST_MODULE')                    
            ->where('TBL_MST_MODULE_VOUCHER_MAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_MODULE_VOUCHER_MAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_MODULE_VOUCHER_MAP.STATUS','=','A')
            ->whereRaw("(TBL_MST_MODULE_VOUCHER_MAP.DEACTIVATED=0 or TBL_MST_MODULE_VOUCHER_MAP.DEACTIVATED is null)")
            ->leftJoin('TBL_MST_MODULE_VOUCHER_MAP', 'TBL_MST_MODULE_VOUCHER_MAP.MODULEID_REF','=','TBL_MST_MODULE.MODULEID')     
            ->select(DB::RAW('distinct(TBL_MST_MODULE_VOUCHER_MAP.MODULEID_REF), TBL_MST_MODULE.*'))
            ->orderBy('TBL_MST_MODULE.MODULE_SEQUENCE')
            ->get();
           

            $VoucherList = DB::table('TBL_MST_VOUCHERTYPE')                    
            ->where('TBL_MST_MODULE_VOUCHER_MAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_MODULE_VOUCHER_MAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_MODULE_VOUCHER_MAP.STATUS','=','A')
            ->whereRaw("(TBL_MST_MODULE_VOUCHER_MAP.DEACTIVATED=0 or TBL_MST_MODULE_VOUCHER_MAP.DEACTIVATED is null)")
            ->leftJoin('TBL_MST_MODULE_VOUCHER_MAP', 'TBL_MST_MODULE_VOUCHER_MAP.VTID_REF','=','TBL_MST_VOUCHERTYPE.VTID')     
            ->select('TBL_MST_VOUCHERTYPE.*')
            ->get();

                    
            $MAX_ROLLID = DB::table('TBL_MST_ROLE')
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->max('LAST_RECORD');
           
            $ROLL=$MAX_ROLLID+1;
            $MAX_ROLE='RC'.$ROLL;

            $objDD = DB::table('TBL_MST_DOCNO_DEFINITION')
            ->where('VTID_REF','=',$this->vtid_ref)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
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
      
        return view('masters.Common.RoleMaster.mstfrm198add',compact(['VoucherList','MAX_ROLE','objDD','objDOCNO','ModuleList']));   
        
   }

   
    public function codeduplicate(Request $request){

        $RCODE  =  strtoupper(trim($request['RCODE']));
        $objLabel = DB::table('TBL_MST_ROLE')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('RCODE','=',$RCODE)
        ->select('RCODE')->first();

        if($objLabel){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }        
        exit();
    }


    public function loadsVouchersList(Request $request){
        
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $module_id     =   $request['module_id'];

        $VoucherList = DB::table('TBL_MST_VOUCHERTYPE')                    
        ->where('TBL_MST_MODULE_VOUCHER_MAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_MODULE_VOUCHER_MAP.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_MST_MODULE_VOUCHER_MAP.STATUS','=','A')
        ->where('TBL_MST_MODULE_VOUCHER_MAP.MODULEID_REF','=',$module_id)
        ->whereRaw("(TBL_MST_MODULE_VOUCHER_MAP.DEACTIVATED=0 or TBL_MST_MODULE_VOUCHER_MAP.DEACTIVATED is null)")
        ->leftJoin('TBL_MST_MODULE_VOUCHER_MAP', 'TBL_MST_MODULE_VOUCHER_MAP.VTID_REF','=','TBL_MST_VOUCHERTYPE.VTID')     
        ->leftJoin('TBL_MST_MODULE', 'TBL_MST_MODULE_VOUCHER_MAP.MODULEID_REF','=','TBL_MST_MODULE.MODULEID')     
        ->select('TBL_MST_VOUCHERTYPE.*','TBL_MST_MODULE.MODULENAME')
        ->orderBy('TBL_MST_VOUCHERTYPE.VT_SEQUENCE')
        ->get();


        if(!empty($VoucherList)){
            //     echo '<tr class="participantRow modulename_'.$module_id.'">                            
            //     <td><input type="text" name="VTID_REF_POPUP_0" id="VTID_REF_POPUP_0" class="form-control mandatory" value="VT0001" style="width:91px" readonly="" tabindex="1"></td>
            //     <td hidden=""> <input type="text" name="VTID_REF_0" value="1" id="VTID_REF_0"><input type="text" name="rowscount[]"></td>
            //     <td>
            //     <input type="text" name="VTID_DESCRITPIONS_0" id="VTID_DESCRITPIONS_0" value="Module master" class="form-control mandatory" style="width:250px" readonly="" tabindex="1">module id =='.$module_id .'</td>
            //     <td style="text-align:center; width: 81px;"><input type="checkbox" name="ADD_0" id="ADD_0"></td>
            //     <td style="text-align:center; width: 81px;"><input type="checkbox" name="EDIT_0" id="EDIT_0"></td>
            //     <td style="text-align:center;width: 81px;"><input type="checkbox" name="CANCEL_0" id="CANCEL_0"></td>
            //     <td style="text-align:center; width: 81px;"><input type="checkbox" name="VIEW_0" id="VIEW_0"></td>
            //     <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL1_0" id="APPROVAL1_0"></td>
            //     <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL2_0" id="APPROVAL2_0"></td>
            //     <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL3_0" id="APPROVAL3_0"></td>
            //     <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL4_0" id="APPROVAL4_0"></td>
            //     <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL5_0" id="APPROVAL5_0"></td>
            //     <td style="text-align:center; width: 81px;"><input type="checkbox" name="PRINT_0" id="PRINT_0"></td>
            //     <td style="text-align:center; width: 81px;"><input type="checkbox" name="ATTACHMENT_0" id="ATTACHMENT_0"></td>
            //     <td style="text-align:center; width: 81px;"><input type="checkbox" name="AMENDMENT_0" id="AMENDMENT_0"></td>
            //     <td style="text-align:center; width: 81px;"><input type="checkbox" name="AMOUNTMATRIX_0" id="AMOUNTMATRIX_0"></td>
            //   </tr>';  
            
            // echo '<tr class="participantRow modulename_'.$module_id.'">   
            // <td><input type="text" name="MODULENAME_'.$key.'" id="MODULENAME_'.$key.'" value="'.$row->MODULENAME.'"  class="form-control mandatory" style="width:200px" readonly="" tabindex="1"> </td>

            // <td hidden><input type="text"  name="VTID_REF_POPUP_'.$key.'" id="VTID_REF_POPUP_'.$key.'" class="form-control mandatory" value="'.$row->VCODE.'" style="width:91px" readonly="" tabindex="1"></td>
            // <td  > <input type="text" name="VTID_REF_'.$key.'" value="'.$row->VTID.'" id="VTID_REF_'.$key.'" ><input type="text" name="rowscount[]" value="'.$row->VTID.'"  /></td>
            // <td><input type="text" name="VTID_DESCRITPIONS_'.$key.'" id="VTID_DESCRITPIONS_'.$key.'" value="'.$row->DESCRIPTIONS.'"  class="form-control mandatory" style="width:250px" readonly="" tabindex="1"></td>
            // <td style="text-align:center; width: 81px;"><input type="checkbox" name="ADD_'.$key.'" id="ADD_'.$key.'"  ></td>
            // <td style="text-align:center; width: 81px;"><input type="checkbox" name="EDIT_'.$key.'" id="EDIT_'.$key.'" ></td>
            // <td style="text-align:center;width: 81px;"><input type="checkbox" name="CANCEL_'.$key.'" id="CANCEL_'.$key.'"></td>
            // <td style="text-align:center; width: 81px;"><input type="checkbox" name="VIEW_'.$key.'" id="VIEW_'.$key.'" ></td>
            // <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL1_'.$key.'" id="APPROVAL1_'.$key.'" ></td>
            // <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL2_'.$key.'" id="APPROVAL2_'.$key.'" ></td>
            // <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL3_'.$key.'" id="APPROVAL3_'.$key.'" ></td>
            // <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL4_'.$key.'" id="APPROVAL4_'.$key.'" ></td>
            // <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL5_'.$key.'" id="APPROVAL5_'.$key.'" ></td>
            // <td style="text-align:center; width: 81px;"><input type="checkbox" name="PRINT_'.$key.'" id="PRINT_'.$key.'" ></td>
            // <td style="text-align:center; width: 81px;"><input type="checkbox" name="ATTACHMENT_'.$key.'" id="ATTACHMENT_'.$key.'" ></td>
            // <td style="text-align:center; width: 81px;"><input type="checkbox" name="AMENDMENT_'.$key.'" id="AMENDMENT_'.$key.'"  ></td>
            // <td style="text-align:center; width: 81px;"><input type="checkbox"  name="AMOUNTMATRIX_'.$key.'" id="AMOUNTMATRIX_'.$key.'" ></td>   
            // </tr>';
            
            
            foreach($VoucherList as $key => $row) {
                echo '<tr class="participantRow modulename_'.$module_id.'">   
                <td><input type="text" name="MODULENAME_'.$row->VTID.'" id="MODULENAME_'.$row->VTID.'" value="'.$row->MODULENAME.'"  class="form-control mandatory" style="width:200px" readonly="" tabindex="1"> </td>

                <td ><input type="text"  name="VTID_REF_POPUP_'.$row->VTID.'" id="VTID_REF_POPUP_'.$row->VTID.'" class="form-control mandatory" value="'.$row->VCODE.'" style="width:91px" readonly="" tabindex="1"></td>
                <td  hidden> <input type="text" name="VTID_REF_'.$row->VTID.'" value="'.$row->VTID.'" id="VTID_REF_'.$row->VTID.'" ><input type="text" name="rowscount[]" value="'.$row->VTID.'"  /></td>
                <td><input type="text" name="VTID_DESCRITPIONS_'.$row->VTID.'" id="VTID_DESCRITPIONS_'.$row->VTID.'" value="'.$row->DESCRIPTIONS.'"  class="form-control mandatory" style="width:250px" readonly="" tabindex="1"></td>
                <td style="text-align:center; width: 81px;"><input type="checkbox" name="ADD_'.$row->VTID.'" id="ADD_'.$row->VTID.'"  ></td>
                <td style="text-align:center; width: 81px;"><input type="checkbox" name="EDIT_'.$row->VTID.'" id="EDIT_'.$row->VTID.'" ></td>
                <td style="text-align:center;width: 81px;"><input type="checkbox" name="CANCEL_'.$row->VTID.'" id="CANCEL_'.$row->VTID.'"></td>
                <td style="text-align:center; width: 81px;"><input type="checkbox" name="VIEW_'.$row->VTID.'" id="VIEW_'.$row->VTID.'" ></td>
                <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL1_'.$row->VTID.'" id="APPROVAL1_'.$row->VTID.'" ></td>
                <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL2_'.$row->VTID.'" id="APPROVAL2_'.$row->VTID.'" ></td>
                <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL3_'.$row->VTID.'" id="APPROVAL3_'.$row->VTID.'" ></td>
                <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL4_'.$row->VTID.'" id="APPROVAL4_'.$row->VTID.'" ></td>
                <td style="text-align:center; width: 81px;"><input type="checkbox" name="APPROVAL5_'.$row->VTID.'" id="APPROVAL5_'.$row->VTID.'" ></td>
                <td style="text-align:center; width: 81px;"><input type="checkbox" name="PRINT_'.$row->VTID.'" id="PRINT_'.$row->VTID.'" ></td>
                <td style="text-align:center; width: 81px;"><input type="checkbox" name="ATTACHMENT_'.$row->VTID.'" id="ATTACHMENT_'.$row->VTID.'" ></td>
                <td style="text-align:center; width: 81px;"><input type="checkbox" name="AMENDMENT_'.$row->VTID.'" id="AMENDMENT_'.$row->VTID.'"  ></td>
                <td style="text-align:center; width: 81px;"><input type="checkbox"  name="AMOUNTMATRIX_'.$row->VTID.'" id="AMOUNTMATRIX_'.$row->VTID.'" ></td>   
                </tr>';
            }   
        }
        else{
            echo "Record not found.";
        }
        
        exit();
    }

   


   //display attachments form
   public function attachment($id){

    if(!is_null($id))
    {
        $objSalesenquiry = DB::table("TBL_MST_ROLE")
                        ->where('ROLLID','=',$id)
                        ->select('TBL_MST_ROLE.*')
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
                        ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                        ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                        ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                        ->get()->toArray();

                 // dump( $objAttachments);

            return view('masters.Common.RoleMaster.mstfrm198attachment',compact(['objSalesenquiry','objMstVoucherType','objAttachments']));
    }

}

    
   public function save(Request $request) {

    
        $r_count1 = count($request['rowscount']);
        
        for ($i=0; $i<$r_count1; $i++)
        {
            $counter = $request['rowscount'][$i];
            if(isset($request['VTID_REF_'.$counter ]))
            {
                // $req_data[$i] = [
                //     'VTID_REF'         => $request['VTID_REF_'.$i],
                //     'ADD' => (isset($request['ADD_'.$i])!="true" ? 0 : 1) ,
                //     'EDIT' => (isset($request['EDIT_'.$i])!="true" ? 0 : 1) ,
                //     'CANCEL' => (isset($request['CANCEL_'.$i])!="true" ? 0 : 1) ,
                //     'VIEW' => (isset($request['VIEW_'.$i])!="true" ? 0 : 1) ,
                //     'APPROVAL1' => (isset($request['APPROVAL1_'.$i])!="true" ? 0 : 1) ,
                //     'APPROVAL2' => (isset($request['APPROVAL2_'.$i])!="true" ? 0 : 1) ,
                //     'APPROVAL3' => (isset($request['APPROVAL3_'.$i])!="true" ? 0 : 1) ,
                //     'APPROVAL4' => (isset($request['APPROVAL4_'.$i])!="true" ? 0 : 1) ,
                //     'APPROVAL5' => (isset($request['APPROVAL5_'.$i])!="true" ? 0 : 1) ,
                //     'PRINT' => (isset($request['PRINT_'.$i])!="true" ? 0 : 1) ,
                //     'ATTECHMENT' => (isset($request['ATTACHMENT_'.$i])!="true" ? 0 : 1) ,
                //     'AMENDMENT' => (isset($request['AMENDMENT_'.$i])!="true" ? 0 : 1) ,
                //     'AMOUNT_MATRIX' => (isset($request['ADD_'.$i])!="true" ? 0 : 1) ,
                //     'AMOUNTMATRIX' => (isset($request['AMOUNTMATRIX_'.$i])!="true" ? 0 : 1) ,

                $req_data[$i] = [

                    'VTID_REF'         => $request['VTID_REF_'. $counter],
                    'ADD' => (isset($request['ADD_'.$counter])!="true" ? 0 : 1) ,
                    'EDIT' => (isset($request['EDIT_'.$counter])!="true" ? 0 : 1) ,
                    'CANCEL' => (isset($request['CANCEL_'.$counter])!="true" ? 0 : 1) ,
                    'VIEW' => (isset($request['VIEW_'.$counter])!="true" ? 0 : 1) ,
                    'APPROVAL1' => (isset($request['APPROVAL1_'.$counter])!="true" ? 0 : 1) ,
                    'APPROVAL2' => (isset($request['APPROVAL2_'.$counter])!="true" ? 0 : 1) ,
                    'APPROVAL3' => (isset($request['APPROVAL3_'.$counter])!="true" ? 0 : 1) ,
                    'APPROVAL4' => (isset($request['APPROVAL4_'.$counter])!="true" ? 0 : 1) ,
                    'APPROVAL5' => (isset($request['APPROVAL5_'.$counter])!="true" ? 0 : 1) ,
                    'PRINT' => (isset($request['PRINT_'.$counter])!="true" ? 0 : 1) ,
                    'ATTECHMENT' => (isset($request['ATTACHMENT_'.$counter])!="true" ? 0 : 1) ,
                    'AMENDMENT' => (isset($request['AMENDMENT_'.$counter])!="true" ? 0 : 1) ,
                    //'AMOUNT_MATRIX' => (isset($request['ADD_'.$counter])!="true" ? 0 : 1) ,
                    'AMOUNTMATRIX' => (isset($request['AMOUNTMATRIX_'.$counter])!="true" ? 0 : 1) ,

                    
                ];
            }
        }
     
     
        
            $wrapped_links["ROLE"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links); 
        

            $VTID     =   $this->vtid_ref;
       
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $RCODE = $request['RCODE'];
            $DESCRIPTIONS = $request['DESCRIPTIONS'];
            $DEACTIVATED    =   NULL;  
            $DODEACTIVATED  =   NULL;  
           



            $log_data = [ 
                $RCODE, $DESCRIPTIONS,$DEACTIVATED,$DODEACTIVATED,$XMLMAT, $CYID_REF, $BRID_REF,$FYID_REF, $VTID,  $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];

          //dump($log_data);
                     
            try{

            
            $sp_result = DB::select('EXEC SP_ROLE_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
            //dd($sp_result);
                  
        } catch (\Throwable $th) {
            
            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);

        }
        
        if(Str::contains(strtoupper($sp_result[0]->RESULT), 'SUCCESS')){

            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }elseif(Str::contains(strtoupper($sp_result[0]->RESULT), 'DUPLICATE RECORD')){
        
            return Response::json(['errors'=>true,'msg' => $sp_result[0]->RESULT,'exist'=>'duplicate']);
            
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
       // DD($CYID_REF.$BRID_REF.$FYID_REF.$id);
        
        if(!is_null($id))
        {
            $objSE = DB::table('TBL_MST_ROLE')                          
                             ->where('TBL_MST_ROLE.CYID_REF','=',$CYID_REF)
                             ->where('TBL_MST_ROLE.BRID_REF','=',$BRID_REF)
                             ->where('TBL_MST_ROLE.ROLLID','=',$id)
                             ->select('TBL_MST_ROLE.*')
                             ->first();

                                    
                            //  $objSEMAT = DB::select("
                            //  SELECT V.DESCRIPTIONS,V.VCODE,T.* FROM (select R.ROLLID_REF,ISNULL(R.VTID_REF,M.VTID_REF) AS VTID_REF,R.[ADD],R.EDIT,R.CANCEL,R.[VIEW],
                            //  R.APPROVAL1,R.APPROVAL2,R.APPROVAL3,R.APPROVAL4,R.APPROVAL5,
                            //  R.[PRINT],R.ATTECHMENT,R.SETTING,R.AMOUNT_MATRIX,R.AMENDMENT from (select * from TBL_MST_MODULE_VOUCHER_MAP where CYID_REF='$CYID_REF'
                            //  and BRID_REF='$BRID_REF') M
                            //  LEFT JOIN (SELECT * FROM TBL_MST_ROLEDETAILS WHERE ROLLID_REF='$id') R
                            //  ON R.VTID_REF=M.VTID_REF
                            //  WHERE M.CYID_REF='$CYID_REF' AND M.BRID_REF='$BRID_REF' AND M.STATUS='A' AND M.DEACTIVATED=0 OR M.DEACTIVATED is null) AS T
                            //  LEFT JOIN TBL_MST_VOUCHERTYPE AS V
                            //  ON T.VTID_REF=V.VTID");     

                            $objSEMAT = DB::select("
                            SELECT V.DESCRIPTIONS,V.VCODE,T.*,V.VT_SEQUENCE FROM 
                            (select MM.MODULEID,MM.MODULENAME,R.ROLLID_REF,ISNULL(R.VTID_REF,M.VTID_REF) AS VTID_REF,R.[ADD],R.EDIT,R.CANCEL,R.[VIEW],
                                                        R.APPROVAL1,R.APPROVAL2,R.APPROVAL3,R.APPROVAL4,R.APPROVAL5,
                                                        R.[PRINT],R.ATTECHMENT,R.SETTING,R.AMOUNT_MATRIX,R.AMENDMENT,MM.MODULE_SEQUENCE from (select * from TBL_MST_MODULE_VOUCHER_MAP where CYID_REF='$CYID_REF'
                                                        and BRID_REF='$BRID_REF') M
                                                        JOIN (SELECT * FROM TBL_MST_ROLEDETAILS WHERE ROLLID_REF=$id) R   ON R.VTID_REF=M.VTID_REF 
                                                        JOIN TBL_MST_MODULE MM (NOLOCK) ON M.MODULEID_REF = MM.MODULEID
                                                        WHERE M.CYID_REF='$CYID_REF' AND M.BRID_REF='$BRID_REF' AND M.STATUS='A' AND M.DEACTIVATED=0 OR M.DEACTIVATED is null) AS T
                                                        LEFT JOIN TBL_MST_VOUCHERTYPE AS V
                                                        ON T.VTID_REF=V.VTID
                            ORDER BY T.MODULE_SEQUENCE,V.VT_SEQUENCE");    

                             
                                                      
            $objCount1 = count($objSEMAT);       
     
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();


            $VoucherList = DB::table('TBL_MST_VOUCHERTYPE')                    
                        ->where('TBL_MST_MODULE_VOUCHER_MAP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_MODULE_VOUCHER_MAP.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_MST_MODULE_VOUCHER_MAP.STATUS','=','A')
                        ->whereRaw("(TBL_MST_MODULE_VOUCHER_MAP.DEACTIVATED=0 or TBL_MST_MODULE_VOUCHER_MAP.DEACTIVATED is null)")
                        ->leftJoin('TBL_MST_MODULE_VOUCHER_MAP', 'TBL_MST_MODULE_VOUCHER_MAP.VTID_REF','=','TBL_MST_VOUCHERTYPE.VTID')     
                        ->select('TBL_MST_VOUCHERTYPE.*')
                        ->get();      
                        //dd($VoucherList);


            $ModuleList = DB::table('TBL_MST_MODULE')                    
                        ->where('TBL_MST_MODULE_VOUCHER_MAP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_MODULE_VOUCHER_MAP.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_MST_MODULE_VOUCHER_MAP.STATUS','=','A')
                        ->whereRaw("(TBL_MST_MODULE_VOUCHER_MAP.DEACTIVATED=0 or TBL_MST_MODULE_VOUCHER_MAP.DEACTIVATED is null)")
                        ->leftJoin('TBL_MST_MODULE_VOUCHER_MAP', 'TBL_MST_MODULE_VOUCHER_MAP.MODULEID_REF','=','TBL_MST_MODULE.MODULEID')     
                        ->select(DB::RAW('distinct(TBL_MST_MODULE_VOUCHER_MAP.MODULEID_REF), TBL_MST_MODULE.*'))
                        ->orderBy('TBL_MST_MODULE.MODULE_SEQUENCE')
                        ->get();

            $SavedModList = DB::select("select distinct(MODULEID_REF) from TBL_MST_MODULE_VOUCHER_MAP where VTID_REF in (select distinct(VTID_REF) from TBL_MST_ROLEDETAILS where rollid_ref=$id)");   
            $SavedModArr = array();
            foreach($SavedModList as $index2=>$smlrow) {
                $SavedModArr[$index2] = $smlrow->MODULEID_REF;
            }
            $savedModData = implode(",", $SavedModArr);

            return view('masters.Common.RoleMaster.mstfrm198edit',compact(['objSE','objRights','objCount1','objSEMAT','VoucherList','ModuleList','SavedModArr']));
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
            $objSE = DB::table('TBL_MST_ROLE')
                         
                             ->where('TBL_MST_ROLE.CYID_REF','=',$CYID_REF)
                             ->where('TBL_MST_ROLE.BRID_REF','=',$BRID_REF)
                             ->where('TBL_MST_ROLE.ROLLID','=',$id)
                             ->select('TBL_MST_ROLE.*')
                             ->first();

            // $objSEMAT = DB::select("
            //                  SELECT V.DESCRIPTIONS,V.VCODE,T.* FROM (select R.ROLLID_REF,ISNULL(R.VTID_REF,M.VTID_REF) AS VTID_REF,R.[ADD],R.EDIT,R.CANCEL,R.[VIEW],
            //                  R.APPROVAL1,R.APPROVAL2,R.APPROVAL3,R.APPROVAL4,R.APPROVAL5,
            //                  R.[PRINT],R.ATTECHMENT,R.SETTING,R.AMOUNT_MATRIX,R.AMENDMENT from (select * from TBL_MST_MODULE_VOUCHER_MAP where CYID_REF='$CYID_REF'
            //                  and BRID_REF='$BRID_REF') M
            //                  LEFT JOIN (SELECT * FROM TBL_MST_ROLEDETAILS WHERE ROLLID_REF='$id') R
            //                  ON R.VTID_REF=M.VTID_REF
            //                  WHERE M.CYID_REF='$CYID_REF' AND M.BRID_REF='$BRID_REF' AND M.STATUS='A' AND M.DEACTIVATED=0 OR M.DEACTIVATED is null) AS T
            //                  LEFT JOIN TBL_MST_VOUCHERTYPE AS V
            //                  ON T.VTID_REF=V.VTID");        
                            // dd($objSEMAT);               
            
                            $objSEMAT = DB::select("
                            SELECT V.DESCRIPTIONS,V.VCODE,T.*,V.VT_SEQUENCE FROM 
                            (select MM.MODULEID,MM.MODULENAME,R.ROLLID_REF,ISNULL(R.VTID_REF,M.VTID_REF) AS VTID_REF,R.[ADD],R.EDIT,R.CANCEL,R.[VIEW],
                                                        R.APPROVAL1,R.APPROVAL2,R.APPROVAL3,R.APPROVAL4,R.APPROVAL5,
                                                        R.[PRINT],R.ATTECHMENT,R.SETTING,R.AMOUNT_MATRIX,R.AMENDMENT,MM.MODULE_SEQUENCE from (select * from TBL_MST_MODULE_VOUCHER_MAP where CYID_REF='$CYID_REF'
                                                        and BRID_REF='$BRID_REF') M
                                                        JOIN (SELECT * FROM TBL_MST_ROLEDETAILS WHERE ROLLID_REF=$id) R   ON R.VTID_REF=M.VTID_REF 
                                                        JOIN TBL_MST_MODULE MM (NOLOCK) ON M.MODULEID_REF = MM.MODULEID
                                                        WHERE M.CYID_REF='$CYID_REF' AND M.BRID_REF='$BRID_REF' AND M.STATUS='A' AND M.DEACTIVATED=0 OR M.DEACTIVATED is null) AS T
                                                        LEFT JOIN TBL_MST_VOUCHERTYPE AS V
                                                        ON T.VTID_REF=V.VTID
                            ORDER BY T.MODULE_SEQUENCE,V.VT_SEQUENCE");    
                           
            $objCount1 = count($objSEMAT);            
            
        
     
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();


            $VoucherList = DB::table('TBL_MST_VOUCHERTYPE')                    
                            ->where('TBL_MST_MODULE_VOUCHER_MAP.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_MODULE_VOUCHER_MAP.BRID_REF','=',Session::get('BRID_REF'))
                            ->where('TBL_MST_MODULE_VOUCHER_MAP.STATUS','=','A')
                            ->whereRaw("(TBL_MST_MODULE_VOUCHER_MAP.DEACTIVATED=0 or TBL_MST_MODULE_VOUCHER_MAP.DEACTIVATED is null)")
                            ->leftJoin('TBL_MST_MODULE_VOUCHER_MAP', 'TBL_MST_MODULE_VOUCHER_MAP.VTID_REF','=','TBL_MST_VOUCHERTYPE.VTID')     
                            ->select('TBL_MST_VOUCHERTYPE.*')
                            ->get();                   
        
      
            $ModuleList = DB::table('TBL_MST_MODULE')                    
                                ->where('TBL_MST_MODULE_VOUCHER_MAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_MODULE_VOUCHER_MAP.BRID_REF','=',Session::get('BRID_REF'))
                                ->where('TBL_MST_MODULE_VOUCHER_MAP.STATUS','=','A')
                                ->whereRaw("(TBL_MST_MODULE_VOUCHER_MAP.DEACTIVATED=0 or TBL_MST_MODULE_VOUCHER_MAP.DEACTIVATED is null)")
                                ->leftJoin('TBL_MST_MODULE_VOUCHER_MAP', 'TBL_MST_MODULE_VOUCHER_MAP.MODULEID_REF','=','TBL_MST_MODULE.MODULEID')     
                                ->select(DB::RAW('distinct(TBL_MST_MODULE_VOUCHER_MAP.MODULEID_REF), TBL_MST_MODULE.*'))
                                ->orderBy('TBL_MST_MODULE.MODULE_SEQUENCE')
                                ->get();
    
            $SavedModList = DB::select("select distinct(MODULEID_REF) from TBL_MST_MODULE_VOUCHER_MAP where VTID_REF in (select distinct(VTID_REF) from TBL_MST_ROLEDETAILS where rollid_ref=$id)");   
            $SavedModArr = array();
            foreach($SavedModList as $index2=>$smlrow) {
                $SavedModArr[$index2] = $smlrow->MODULEID_REF;
            }
            $savedModData = implode(",", $SavedModArr);


        return view('masters.Common.RoleMaster.mstfrm198view',compact(['objSE','objRights','objCount1','objSEMAT','VoucherList','ModuleList','SavedModArr']));
        }
      
    }

    //update the data
   


    public function update(Request $request){
   
          $r_count1 = count($request['rowscount']);         
       
          for ($i=0; $i<$r_count1; $i++)
          {
               $counter = $request['rowscount'][$i];
              if(isset($request['VTID_REF_'.$counter]))
              {
                  $req_data[$i] = [
                       
                        'VTID_REF'         => $request['VTID_REF_'. $counter],
                        'ADD' => (isset($request['ADD_'.$counter])!="true" ? 0 : 1) ,
                        'EDIT' => (isset($request['EDIT_'.$counter])!="true" ? 0 : 1) ,
                        'CANCEL' => (isset($request['CANCEL_'.$counter])!="true" ? 0 : 1) ,
                        'VIEW' => (isset($request['VIEW_'.$counter])!="true" ? 0 : 1) ,
                        'APPROVAL1' => (isset($request['APPROVAL1_'.$counter])!="true" ? 0 : 1) ,
                        'APPROVAL2' => (isset($request['APPROVAL2_'.$counter])!="true" ? 0 : 1) ,
                        'APPROVAL3' => (isset($request['APPROVAL3_'.$counter])!="true" ? 0 : 1) ,
                        'APPROVAL4' => (isset($request['APPROVAL4_'.$counter])!="true" ? 0 : 1) ,
                        'APPROVAL5' => (isset($request['APPROVAL5_'.$counter])!="true" ? 0 : 1) ,
                        'PRINT' => (isset($request['PRINT_'.$counter])!="true" ? 0 : 1) ,
                        'ATTECHMENT' => (isset($request['ATTACHMENT_'.$counter])!="true" ? 0 : 1) ,
                        'AMENDMENT' => (isset($request['AMENDMENT_'.$counter])!="true" ? 0 : 1) ,
                        //'AMOUNT_MATRIX' => (isset($request['ADD_'.$counter])!="true" ? 0 : 1) ,
                        'AMOUNTMATRIX' => (isset($request['AMOUNTMATRIX_'.$counter])!="true" ? 0 : 1) ,
                            
                  ];
              }
          }
          
         
              $wrapped_links["ROLE"] = $req_data; 
              $XMLMAT = ArrayToXml::convert($wrapped_links);

          
              $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
              $newDateString = NULL;
      
              $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 
      
              if(!is_null($newdt) ){
                  
                  $newdt = str_replace( "/", "-",  $newdt ) ;
      
                  $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
              }
              
      
              $DODEACTIVATED = $newDateString;
  
          
          
  
              $VTID     =   $this->vtid_ref;

              $USERID = Auth::user()->USERID;   
              $ACTIONNAME = 'EDIT';
              $IPADDRESS = $request->getClientIp();
              $CYID_REF = Auth::user()->CYID_REF;
              $BRID_REF = Session::get('BRID_REF');
              $FYID_REF = Session::get('FYID_REF');
              $RCODE = $request['RCODE'];
              $DESCRIPTIONS = $request['DESCRIPTIONS'];
    
             
  
              $log_data = [ 
                $RCODE, $DESCRIPTIONS,$DEACTIVATED,$DODEACTIVATED,$XMLMAT, $CYID_REF, $BRID_REF,$FYID_REF, $VTID,  $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
                ];
  
            //dd($log_data);
             
  
              try{
              
              $sp_result = DB::select('EXEC SP_ROLE_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);    
            
      

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

          
        //   $r_count1 =  count($request['rowscount']);
        //   for ($i=0; $i<=$r_count1; $i++)
        //   {
        //       if(isset($request['VTID_REF_'.$i]))
        //       {
        //           $req_data[$i] = [
        //             'VTID_REF'         => $request['VTID_REF_'.$i],
        //             'ADD' => (isset($request['ADD_'.$i])!="true" ? 0 : 1) ,
        //             'EDIT' => (isset($request['EDIT_'.$i])!="true" ? 0 : 1) ,
        //             'CANCEL' => (isset($request['CANCEL_'.$i])!="true" ? 0 : 1) ,
        //             'VIEW' => (isset($request['VIEW_'.$i])!="true" ? 0 : 1) ,
        //             'APPROVAL1' => (isset($request['APPROVAL1_'.$i])!="true" ? 0 : 1) ,
        //             'APPROVAL2' => (isset($request['APPROVAL2_'.$i])!="true" ? 0 : 1) ,
        //             'APPROVAL3' => (isset($request['APPROVAL3_'.$i])!="true" ? 0 : 1) ,
        //             'APPROVAL4' => (isset($request['APPROVAL4_'.$i])!="true" ? 0 : 1) ,
        //             'APPROVAL5' => (isset($request['APPROVAL5_'.$i])!="true" ? 0 : 1) ,
        //             'PRINT' => (isset($request['PRINT_'.$i])!="true" ? 0 : 1) ,
        //             'ATTECHMENT' => (isset($request['ATTACHMENT_'.$i])!="true" ? 0 : 1) ,
        //             'AMENDMENT' => (isset($request['AMENDMENT_'.$i])!="true" ? 0 : 1) ,
        //             'AMOUNT_MATRIX' => (isset($request['ADD_'.$i])!="true" ? 0 : 1) ,
        //             'AMOUNTMATRIX' => (isset($request['AMOUNTMATRIX_'.$i])!="true" ? 0 : 1) ,
                      
        //           ];
        //       }
        //   }

        $r_count1 = count($request['rowscount']);         
       
        for ($i=0; $i<$r_count1; $i++)
        {
             $counter = $request['rowscount'][$i];
            if(isset($request['VTID_REF_'.$counter]))
            {
                $req_data[$i] = [
                     
                      'VTID_REF'         => $request['VTID_REF_'. $counter],
                      'ADD' => (isset($request['ADD_'.$counter])!="true" ? 0 : 1) ,
                      'EDIT' => (isset($request['EDIT_'.$counter])!="true" ? 0 : 1) ,
                      'CANCEL' => (isset($request['CANCEL_'.$counter])!="true" ? 0 : 1) ,
                      'VIEW' => (isset($request['VIEW_'.$counter])!="true" ? 0 : 1) ,
                      'APPROVAL1' => (isset($request['APPROVAL1_'.$counter])!="true" ? 0 : 1) ,
                      'APPROVAL2' => (isset($request['APPROVAL2_'.$counter])!="true" ? 0 : 1) ,
                      'APPROVAL3' => (isset($request['APPROVAL3_'.$counter])!="true" ? 0 : 1) ,
                      'APPROVAL4' => (isset($request['APPROVAL4_'.$counter])!="true" ? 0 : 1) ,
                      'APPROVAL5' => (isset($request['APPROVAL5_'.$counter])!="true" ? 0 : 1) ,
                      'PRINT' => (isset($request['PRINT_'.$counter])!="true" ? 0 : 1) ,
                      'ATTECHMENT' => (isset($request['ATTACHMENT_'.$counter])!="true" ? 0 : 1) ,
                      'AMENDMENT' => (isset($request['AMENDMENT_'.$counter])!="true" ? 0 : 1) ,
                      //'AMOUNT_MATRIX' => (isset($request['ADD_'.$counter])!="true" ? 0 : 1) ,
                      'AMOUNTMATRIX' => (isset($request['AMOUNTMATRIX_'.$counter])!="true" ? 0 : 1) ,
                          
                ];
            }
        }





              $wrapped_links["ROLE"] = $req_data; 
              $XMLMAT = ArrayToXml::convert($wrapped_links);
          
          
  
          
          
  
             $VTID     =   $this->vtid_ref;
              $VID = 0;
              $USERID = Auth::user()->USERID;   
              $ACTIONNAME = $Approvallevel;
              $IPADDRESS = $request->getClientIp();
              $CYID_REF = Auth::user()->CYID_REF;
              $BRID_REF = Session::get('BRID_REF');
              $FYID_REF = Session::get('FYID_REF');
              $RCODE = $request['RCODE'];
              $DESCRIPTIONS = $request['DESCRIPTIONS'];
              $DEACTIVATED    =   NULL;  
              $DODEACTIVATED  =   NULL;  
             
  
              $log_data = [ 
                $RCODE, $DESCRIPTIONS,$DEACTIVATED,$DODEACTIVATED,$XMLMAT, $CYID_REF, $BRID_REF,$FYID_REF, $VTID,  $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
                ];
  
              try{
  
              
                $sp_result = DB::select('EXEC SP_ROLE_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);    
             // dd($sp_result); 
      
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
                $TABLE      =   "TBL_MST_ROLE";
                $FIELD      =   "ROLLID";
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
            $TABLE      =   "TBL_MST_ROLE";
            $FIELD      =   "ROLLID";
            $ID         =   $id;
            $UPDATE     =   Date('Y-m-d');
            $UPTIME     =   Date('h:i:s.u');
            $IPADDRESS  =   $request->getClientIp();
    
            $req_data[0]=[
                'NT'  => 'TBL_MST_ROLE',
            ];
             $req_data[1]=[
                 'NT'  => 'TBL_MST_ROLEDETAILS',
             ];
            // $req_data[2]=[
            //     'NT'  => 'TBL_MST_PRICELIST_MAT',
            // ];
       
                
          
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
        
        //$destinationPath = storage_path()."/docs/company".$CYID_REF.'/RoleMaster';
        $image_path         =   "docs/company".$CYID_REF."/RoleMaster";     
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
            return redirect()->route("master",[198,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
             $sp_result = DB::select('EXEC SP_TRN_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);
    
       } catch (\Throwable $th) {
        
           return redirect()->route("master",[198,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");
    
       }
     
        if($sp_result[0]->RESULT=="SUCCESS"){
    
            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }
    
            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }
    
            return redirect()->route("master",[198,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);
    
    
        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[198,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{
    
            return redirect()->route("master",[198,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }

    public function amendment($id){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id)){

            $objSE  =   DB::table('TBL_MST_ROLE')                          
            ->where('TBL_MST_ROLE.CYID_REF','=',$CYID_REF)
            ->where('TBL_MST_ROLE.BRID_REF','=',$BRID_REF)
            ->where('TBL_MST_ROLE.ROLLID','=',$id)
            ->select('TBL_MST_ROLE.*')
            ->first();

            $objSEMAT = DB::select("
            SELECT V.DESCRIPTIONS,V.VCODE,T.*,V.VT_SEQUENCE FROM 
            (select MM.MODULEID,MM.MODULENAME,R.ROLLID_REF,ISNULL(R.VTID_REF,M.VTID_REF) AS VTID_REF,R.[ADD],R.EDIT,R.CANCEL,R.[VIEW],
            R.APPROVAL1,R.APPROVAL2,R.APPROVAL3,R.APPROVAL4,R.APPROVAL5,
            R.[PRINT],R.ATTECHMENT,R.SETTING,R.AMOUNT_MATRIX,R.AMENDMENT,MM.MODULE_SEQUENCE from (select * from TBL_MST_MODULE_VOUCHER_MAP where CYID_REF='$CYID_REF'
            and BRID_REF='$BRID_REF') M
            JOIN (SELECT * FROM TBL_MST_ROLEDETAILS WHERE ROLLID_REF=$id) R   ON R.VTID_REF=M.VTID_REF 
            JOIN TBL_MST_MODULE MM (NOLOCK) ON M.MODULEID_REF = MM.MODULEID
            WHERE M.CYID_REF='$CYID_REF' AND M.BRID_REF='$BRID_REF' AND M.STATUS='A' AND M.DEACTIVATED=0 OR M.DEACTIVATED is null) AS T
            LEFT JOIN TBL_MST_VOUCHERTYPE AS V
            ON T.VTID_REF=V.VTID
            ORDER BY T.MODULE_SEQUENCE,V.VT_SEQUENCE");    
                                   
            $objCount1  =   count($objSEMAT);       
            $objRights  =   DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $VoucherList = DB::table('TBL_MST_VOUCHERTYPE')                    
            ->where('TBL_MST_MODULE_VOUCHER_MAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_MODULE_VOUCHER_MAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_MODULE_VOUCHER_MAP.STATUS','=','A')
            ->whereRaw("(TBL_MST_MODULE_VOUCHER_MAP.DEACTIVATED=0 or TBL_MST_MODULE_VOUCHER_MAP.DEACTIVATED is null)")
            ->leftJoin('TBL_MST_MODULE_VOUCHER_MAP', 'TBL_MST_MODULE_VOUCHER_MAP.VTID_REF','=','TBL_MST_VOUCHERTYPE.VTID')     
            ->select('TBL_MST_VOUCHERTYPE.*')
            ->get();      
                       
            $ModuleList = DB::table('TBL_MST_MODULE')                    
            ->where('TBL_MST_MODULE_VOUCHER_MAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_MODULE_VOUCHER_MAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_MODULE_VOUCHER_MAP.STATUS','=','A')
            ->whereRaw("(TBL_MST_MODULE_VOUCHER_MAP.DEACTIVATED=0 or TBL_MST_MODULE_VOUCHER_MAP.DEACTIVATED is null)")
            ->leftJoin('TBL_MST_MODULE_VOUCHER_MAP', 'TBL_MST_MODULE_VOUCHER_MAP.MODULEID_REF','=','TBL_MST_MODULE.MODULEID')     
            ->select(DB::RAW('distinct(TBL_MST_MODULE_VOUCHER_MAP.MODULEID_REF), TBL_MST_MODULE.*'))
            ->orderBy('TBL_MST_MODULE.MODULE_SEQUENCE')
            ->get();

            $SavedModList= DB::select("select distinct(MODULEID_REF) from TBL_MST_MODULE_VOUCHER_MAP where VTID_REF in (select distinct(VTID_REF) from TBL_MST_ROLEDETAILS where rollid_ref=$id)");   
            $SavedModArr = array();
            foreach($SavedModList as $index2=>$smlrow) {
                $SavedModArr[$index2] = $smlrow->MODULEID_REF;
            }
            $savedModData = implode(",", $SavedModArr);

            return view('masters.Common.RoleMaster.mstfrm198amendment',compact(['objSE','objRights','objCount1','objSEMAT','VoucherList','ModuleList','SavedModArr']));
        }
     
    }

    

    public function saveamendment(Request $request){
   
        $r_count1   =   count($request['rowscount']);         
     
        for ($i=0; $i<$r_count1; $i++){
            $counter    =   $request['rowscount'][$i];

            if(isset($request['VTID_REF_'.$counter])){
                $req_data[$i] = [
                    'VTID_REF'         => $request['VTID_REF_'. $counter],
                    'ADD' => (isset($request['ADD_'.$counter])!="true" ? 0 : 1) ,
                    'EDIT' => (isset($request['EDIT_'.$counter])!="true" ? 0 : 1) ,
                    'CANCEL' => (isset($request['CANCEL_'.$counter])!="true" ? 0 : 1) ,
                    'VIEW' => (isset($request['VIEW_'.$counter])!="true" ? 0 : 1) ,
                    'APPROVAL1' => (isset($request['APPROVAL1_'.$counter])!="true" ? 0 : 1) ,
                    'APPROVAL2' => (isset($request['APPROVAL2_'.$counter])!="true" ? 0 : 1) ,
                    'APPROVAL3' => (isset($request['APPROVAL3_'.$counter])!="true" ? 0 : 1) ,
                    'APPROVAL4' => (isset($request['APPROVAL4_'.$counter])!="true" ? 0 : 1) ,
                    'APPROVAL5' => (isset($request['APPROVAL5_'.$counter])!="true" ? 0 : 1) ,
                    'PRINT' => (isset($request['PRINT_'.$counter])!="true" ? 0 : 1) ,
                    'ATTECHMENT' => (isset($request['ATTACHMENT_'.$counter])!="true" ? 0 : 1) ,
                    'AMENDMENT' => (isset($request['AMENDMENT_'.$counter])!="true" ? 0 : 1) ,
                    'AMOUNTMATRIX' => (isset($request['AMOUNTMATRIX_'.$counter])!="true" ? 0 : 1) ,        
                ];
            }
        }
        
       
        $wrapped_links["ROLE"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);

        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
        $newDateString = NULL;
        $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt) ){
            $newdt = str_replace( "/", "-",  $newdt ) ;
            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        
        $DODEACTIVATED  =   $newDateString;
        $VTID           =   $this->vtid_ref;
        $USERID         =   Auth::user()->USERID;   
        $ACTIONNAME     =   'AMENDMENT';
        $IPADDRESS      =   $request->getClientIp();
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $RCODE          =   $request['RCODE'];
        $DESCRIPTIONS   =   $request['DESCRIPTIONS'];
        $ANO                =   $request['ANO'];
        $AMENDMENT_DATE     =   $request['AMENDMENT_DATE'];
        $AMENDMENT_REASON   =   $request['AMENDMENT_REASON'];
  
        $log_data = [ 
            $RCODE, $DESCRIPTIONS,$DEACTIVATED,$DODEACTIVATED,$XMLMAT, $CYID_REF, $BRID_REF,$FYID_REF, $VTID,  $USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS,
            $ANO,$AMENDMENT_DATE,$AMENDMENT_REASON
        ];

        //DD($log_data);

        try{
            $sp_result = DB::select('EXEC SP_ROLE_AMENDMENT ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);    
        } 
        catch (\Throwable $th) {
            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);
        }    
  
        if($sp_result[0]->RESULT=="SUCCESS"){  
            return Response::json(['success' =>true,'msg' => 'Record successfully amend.']);
        }
        elseif($sp_result[0]->RESULT=="NO RECORD FOUND"){
            return Response::json(['errors'=>true,'msg' => 'No record found.','exist'=>'norecord']); 
        }
        else{
            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);
        }
          
        exit();          
    }
    
}
