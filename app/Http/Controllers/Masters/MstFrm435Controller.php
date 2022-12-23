<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm435;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;


class MstFrm435Controller extends Controller{

    protected $form_id    = 435;
    protected $vtid_ref   = 505;
    protected $view       = "masters.PreSales.EmployeeTargetMaster.mstfrm";
       
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
        $objDataList    =   DB::table('TBL_MST_EMPLOYEE_TARGET')
        ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_MST_EMPLOYEE_TARGET.EMPLOYEE_TARGETNAME','=','TBL_MST_EMPLOYEE.EMPID')
        ->select('TBL_MST_EMPLOYEE_TARGET.*','TBL_MST_EMPLOYEE.EMPID','TBL_MST_EMPLOYEE.EMPCODE','TBL_MST_EMPLOYEE.FNAME')
        ->orderBy('ID','desc')
        ->get();

        return view($this->view.$FormId,compact(['objRights','objDataList','FormId']));

    }
   
    
    public function add(){ 

        $FormId   =   $this->form_id;
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');

        $objDD = DB::table('TBL_MST_DOCNO_DEFINITION')
        ->where('VTID_REF','=',$this->vtid_ref)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('BRID_REF','=',$BRID_REF)
        ->where('STATUS','=','A')
        ->select('TBL_MST_DOCNO_DEFINITION.*')
        ->first();

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

        return view($this->view.$FormId.'add',compact(['objDD','objDOCNO','FormId']));
    }

   
    public function save(Request $request){

        $EMPLOYEE_TARGETCODE   =   trim($request['EMPLOYEE_TARGETCODE']);
        $EMPLOYEE_TARGETNAME   =   trim($request['EMPLOYEE_TARGET_REF']); 
        $FYID_REF1             =   trim($request['FYID_REF1']);
        $TARGET_AMOUNT         =   trim($request['TARGET_AMOUNT']);
        $EMPLOYEE_TYPE         =   trim($request['EMPTARGETTYPE']);

        $DEACTIVATED    =   NULL;  
        $DODEACTIVATED  =   NULL; 
        
        
        $Details  = array();
        if(isset($_REQUEST['MONTH1_AMT']) && !empty($_REQUEST['MONTH1_AMT'])){
            foreach($_REQUEST['MONTH1_AMT'] as $key=>$val){

                $Details[] = array(
                'MONTH1_AMT'         => trim($_REQUEST['MONTH1_AMT'][$key])?trim($_REQUEST['MONTH1_AMT'][$key]):0,
                'MONTH2_AMT'         => trim($_REQUEST['MONTH2_AMT'][$key])?trim($_REQUEST['MONTH2_AMT'][$key]):0,
                'MONTH3_AMT'         => trim($_REQUEST['MONTH3_AMT'][$key])?trim($_REQUEST['MONTH3_AMT'][$key]):0,
                'MONTH4_AMT'         => trim($_REQUEST['MONTH4_AMT'][$key])?trim($_REQUEST['MONTH4_AMT'][$key]):0,
                'MONTH5_AMT'         => trim($_REQUEST['MONTH5_AMT'][$key])?trim($_REQUEST['MONTH5_AMT'][$key]):0,
                'MONTH6_AMT'         => trim($_REQUEST['MONTH6_AMT'][$key])?trim($_REQUEST['MONTH6_AMT'][$key]):0,
                'MONTH7_AMT'         => trim($_REQUEST['MONTH7_AMT'][$key])?trim($_REQUEST['MONTH7_AMT'][$key]):0,
                'MONTH8_AMT'         => trim($_REQUEST['MONTH8_AMT'][$key])?trim($_REQUEST['MONTH8_AMT'][$key]):0,
                'MONTH9_AMT'         => trim($_REQUEST['MONTH9_AMT'][$key])?trim($_REQUEST['MONTH9_AMT'][$key]):0,
                'MONTH10_AMT'        => trim($_REQUEST['MONTH10_AMT'][$key])?trim($_REQUEST['MONTH10_AMT'][$key]):0,
                'MONTH11_AMT'        => trim($_REQUEST['MONTH11_AMT'][$key])?trim($_REQUEST['MONTH11_AMT'][$key]):0,
                'MONTH12_AMT'        => trim($_REQUEST['MONTH12_AMT'][$key])?trim($_REQUEST['MONTH12_AMT'][$key]):0,
                );
            }
        }

        //dd($Details);
    
        if(!empty($Details)){
            $wrapped_linksd["DETAILS"] = $Details; 
            $XMLDETAILS = ArrayToXml::convert($wrapped_linksd);
        }
        else{
            $XMLDETAILS = NULL; 
        }


        $Product  = array();
        if(isset($_REQUEST['ITEMID_REF']) && !empty($_REQUEST['ITEMID_REF'])){
            foreach($_REQUEST['ITEMID_REF'] as $key=>$val){

                $Product[] = array(
                'ITEMID_REF'        => trim($_REQUEST['ITEMID_REF'][$key])?trim($_REQUEST['ITEMID_REF'][$key]):0,
                'MONTH1_QTY'        => trim($_REQUEST['MONTH1_QTY'][$key])?trim($_REQUEST['MONTH1_QTY'][$key]):0,
                'MONTH2_QTY'        => trim($_REQUEST['MONTH2_QTY'][$key])?trim($_REQUEST['MONTH2_QTY'][$key]):0,
                'MONTH3_QTY'        => trim($_REQUEST['MONTH3_QTY'][$key])?trim($_REQUEST['MONTH3_QTY'][$key]):0,
                'MONTH4_QTY'        => trim($_REQUEST['MONTH4_QTY'][$key])?trim($_REQUEST['MONTH4_QTY'][$key]):0,
                'MONTH5_QTY'        => trim($_REQUEST['MONTH5_QTY'][$key])?trim($_REQUEST['MONTH5_QTY'][$key]):0,
                'MONTH6_QTY'        => trim($_REQUEST['MONTH6_QTY'][$key])?trim($_REQUEST['MONTH6_QTY'][$key]):0,
                'MONTH7_QTY'        => trim($_REQUEST['MONTH7_QTY'][$key])?trim($_REQUEST['MONTH7_QTY'][$key]):0,
                'MONTH8_QTY'        => trim($_REQUEST['MONTH8_QTY'][$key])?trim($_REQUEST['MONTH8_QTY'][$key]):0,
                'MONTH9_QTY'        => trim($_REQUEST['MONTH9_QTY'][$key])?trim($_REQUEST['MONTH9_QTY'][$key]):0,
                'MONTH10_QTY'       => trim($_REQUEST['MONTH10_QTY'][$key])?trim($_REQUEST['MONTH10_QTY'][$key]):0,
                'MONTH11_QTY'       => trim($_REQUEST['MONTH11_QTY'][$key])?trim($_REQUEST['MONTH11_QTY'][$key]):0,
                'MONTH12_QTY'       => trim($_REQUEST['MONTH12_QTY'][$key])?trim($_REQUEST['MONTH12_QTY'][$key]):0,
                );
            }
        }

        //dd($Product);
    
        if(!empty($Product)){
            $wrapped_linksp["PRODUCT"] = $Product; 
            $XMLPRODUCT = ArrayToXml::convert($wrapped_linksp);
        }
        else{
            $XMLPRODUCT = NULL; 
        }


        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');    
        $USERID         =   Auth::user()->USERID;   
        $VTID           =   $this->vtid_ref;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();

        $array_data     = [$EMPLOYEE_TARGETCODE, $EMPLOYEE_TARGETNAME, $DEACTIVATED, $DODEACTIVATED,$CYID_REF, $BRID_REF, $FYID_REF, $VTID,  $USERID, $UPDATE,$UPTIME, $ACTION, $IPADDRESS,$TARGET_AMOUNT,$XMLDETAILS,$XMLPRODUCT,$FYID_REF1,$EMPLOYEE_TYPE ];
        
        //dd($array_data);

        try {

            $sp_result = DB::select('EXEC SP_EMPLOYEE_TARGET_IN ?,?,?,?,?,?, ?,?,?,?,?,?, ?,?,?,?,?,?', $array_data);

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
            return $this->showRecord($id,'edit','');
        }
        public function view($id){
            return $this->showRecord($id,'view','disabled');
        }
    
        public function update(Request $request){
            return  $this->updateRecord($request,'update');        
        } 
        
        public function Approve(Request $request){
          return  $this->updateRecord($request,'approve');    
        }



    public function showRecord($id,$type,$ActionStatus){

        if(!is_null($id)){
        
            $FormId     =   $this->form_id;
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');

            $objResponse = DB::table('TBL_MST_EMPLOYEE_TARGET')
            ->leftJoin('TBL_MST_FYEAR', 'TBL_MST_EMPLOYEE_TARGET.FYID_REF','=','TBL_MST_FYEAR.FYID')
            ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_MST_EMPLOYEE_TARGET.EMPLOYEE_TARGETNAME','=','TBL_MST_EMPLOYEE.EMPID')
            ->where('ID','=',$id)
            ->select('TBL_MST_EMPLOYEE_TARGET.*','TBL_MST_FYEAR.FYID','TBL_MST_FYEAR.FYCODE','TBL_MST_FYEAR.FYDESCRIPTION','TBL_MST_EMPLOYEE.EMPID','TBL_MST_EMPLOYEE.EMPCODE','TBL_MST_EMPLOYEE.FNAME')
            ->first();

            $MAT = DB::table('TBL_MST_EMPLOYEE_TARGET_DETAILS')
            ->where('TARGETID_REF','=',$id)
            ->first();

            $MAT1 = DB::table('TBL_MST_EMPLOYEE_TARGET_PRODUCT')
            ->leftJoin('TBL_MST_ITEM', 'TBL_MST_EMPLOYEE_TARGET_PRODUCT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
            ->where('TARGETID_REF','=',$id)
            ->select('TBL_MST_EMPLOYEE_TARGET_PRODUCT.*','TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME')
            ->get();

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();
            
            return view($this->view.$FormId.$type,compact(['objResponse','objRights','MAT','MAT1','ActionStatus','FormId']));
        }
    }



    public function updateRecord($request,$type){

        $FormId     =   $this->form_id;
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

        if(!empty($sp_listing_result)){
            foreach ($sp_listing_result as $key=>$salesenquiryitem){  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$salesenquiryitem->LAVELS;
            }
        }

        $requestType    =   $request->requestType;
        $Approvallevel  =   $requestType =='update'?'EDIT':$Approvallevel;
        $msgTxt         =   $requestType =='update'?'updated':'approved';
         
        $EMPLOYEE_TARGETCODE   =   trim($request['EMPLOYEE_TARGETCODE']);
        $EMPLOYEE_TARGETNAME   =   trim($request['EMPLOYEE_TARGET_REF']); 
        $FYID_REF1             =   trim($request['FYID_REF1']);
        $TARGET_AMOUNT         =   trim($request['TARGET_AMOUNT']);
        $EMPLOYEE_TYPE         =   trim($request['EMPTARGETTYPE']);

        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;
       
        $newDateString = NULL;

        $newdt = !(is_null($request['DODEACTIVATED']) ||empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 

        if(!is_null($newdt) ){
            
            $newdt = str_replace( "/", "-",  $newdt ) ;

            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }

        $DODEACTIVATED = $newDateString; 
        
        
        $Details  = array();
        if(isset($_REQUEST['MONTH1_AMT']) && !empty($_REQUEST['MONTH1_AMT'])){
            foreach($_REQUEST['MONTH1_AMT'] as $key=>$val){

                $Details[] = array(
                'MONTH1_AMT'         => trim($_REQUEST['MONTH1_AMT'][$key])?trim($_REQUEST['MONTH1_AMT'][$key]):0,
                'MONTH2_AMT'         => trim($_REQUEST['MONTH2_AMT'][$key])?trim($_REQUEST['MONTH2_AMT'][$key]):0,
                'MONTH3_AMT'         => trim($_REQUEST['MONTH3_AMT'][$key])?trim($_REQUEST['MONTH3_AMT'][$key]):0,
                'MONTH4_AMT'         => trim($_REQUEST['MONTH4_AMT'][$key])?trim($_REQUEST['MONTH4_AMT'][$key]):0,
                'MONTH5_AMT'         => trim($_REQUEST['MONTH5_AMT'][$key])?trim($_REQUEST['MONTH5_AMT'][$key]):0,
                'MONTH6_AMT'         => trim($_REQUEST['MONTH6_AMT'][$key])?trim($_REQUEST['MONTH6_AMT'][$key]):0,
                'MONTH7_AMT'         => trim($_REQUEST['MONTH7_AMT'][$key])?trim($_REQUEST['MONTH7_AMT'][$key]):0,
                'MONTH8_AMT'         => trim($_REQUEST['MONTH8_AMT'][$key])?trim($_REQUEST['MONTH8_AMT'][$key]):0,
                'MONTH9_AMT'         => trim($_REQUEST['MONTH9_AMT'][$key])?trim($_REQUEST['MONTH9_AMT'][$key]):0,
                'MONTH10_AMT'        => trim($_REQUEST['MONTH10_AMT'][$key])?trim($_REQUEST['MONTH10_AMT'][$key]):0,
                'MONTH11_AMT'        => trim($_REQUEST['MONTH11_AMT'][$key])?trim($_REQUEST['MONTH11_AMT'][$key]):0,
                'MONTH12_AMT'        => trim($_REQUEST['MONTH12_AMT'][$key])?trim($_REQUEST['MONTH12_AMT'][$key]):0,
                );
            }
        }

        //dd($Details);
    
        if(!empty($Details)){
            $wrapped_linksd["DETAILS"] = $Details; 
            $XMLDETAILS = ArrayToXml::convert($wrapped_linksd);
        }
        else{
            $XMLDETAILS = NULL; 
        }


        $Product  = array();
        if(isset($_REQUEST['ITEMID_REF']) && !empty($_REQUEST['ITEMID_REF'])){
            foreach($_REQUEST['ITEMID_REF'] as $key=>$val){

                $Product[] = array(
                'ITEMID_REF'        => trim($_REQUEST['ITEMID_REF'][$key])?trim($_REQUEST['ITEMID_REF'][$key]):0,
                'MONTH1_QTY'        => trim($_REQUEST['MONTH1_QTY'][$key])?trim($_REQUEST['MONTH1_QTY'][$key]):0,
                'MONTH2_QTY'        => trim($_REQUEST['MONTH2_QTY'][$key])?trim($_REQUEST['MONTH2_QTY'][$key]):0,
                'MONTH3_QTY'        => trim($_REQUEST['MONTH3_QTY'][$key])?trim($_REQUEST['MONTH3_QTY'][$key]):0,
                'MONTH4_QTY'        => trim($_REQUEST['MONTH4_QTY'][$key])?trim($_REQUEST['MONTH4_QTY'][$key]):0,
                'MONTH5_QTY'        => trim($_REQUEST['MONTH5_QTY'][$key])?trim($_REQUEST['MONTH5_QTY'][$key]):0,
                'MONTH6_QTY'        => trim($_REQUEST['MONTH6_QTY'][$key])?trim($_REQUEST['MONTH6_QTY'][$key]):0,
                'MONTH7_QTY'        => trim($_REQUEST['MONTH7_QTY'][$key])?trim($_REQUEST['MONTH7_QTY'][$key]):0,
                'MONTH8_QTY'        => trim($_REQUEST['MONTH8_QTY'][$key])?trim($_REQUEST['MONTH8_QTY'][$key]):0,
                'MONTH9_QTY'        => trim($_REQUEST['MONTH9_QTY'][$key])?trim($_REQUEST['MONTH9_QTY'][$key]):0,
                'MONTH10_QTY'       => trim($_REQUEST['MONTH10_QTY'][$key])?trim($_REQUEST['MONTH10_QTY'][$key]):0,
                'MONTH11_QTY'       => trim($_REQUEST['MONTH11_QTY'][$key])?trim($_REQUEST['MONTH11_QTY'][$key]):0,
                'MONTH12_QTY'       => trim($_REQUEST['MONTH12_QTY'][$key])?trim($_REQUEST['MONTH12_QTY'][$key]):0,
                );
            }
        }

        //dd($Product);
    
        if(!empty($Product)){
            $wrapped_linksp["PRODUCT"] = $Product; 
            $XMLPRODUCT = ArrayToXml::convert($wrapped_linksp);
        }
        else{
            $XMLPRODUCT = NULL; 
        }

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');    
        $USERID         =   Auth::user()->USERID;   
        $VTID           =   $this->vtid_ref;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   $Approvallevel;
        $IPADDRESS      =   $request->getClientIp();

        $array_data     = [$EMPLOYEE_TARGETCODE, $EMPLOYEE_TARGETNAME, $DEACTIVATED, $DODEACTIVATED,$CYID_REF, $BRID_REF, $FYID_REF, $VTID,  $USERID, $UPDATE,$UPTIME, $ACTION, $IPADDRESS,$TARGET_AMOUNT,$XMLDETAILS,$XMLPRODUCT,$FYID_REF1,$EMPLOYEE_TYPE ];
        
        try {

            $sp_result = DB::select('EXEC SP_EMPLOYEE_TARGET_UP ?,?,?,?,?,?, ?,?,?,?,?,?, ?,?,?,?,?,?', $array_data);

            } catch (\Throwable $th) {

                return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);

            }    

            if($sp_result[0]->RESULT=="SUCCESS"){  

            return Response::json(['success' =>true,'msg' => 'Record successfully '.$msgTxt]);
            
            }elseif($sp_result[0]->RESULT=="NO RECORD FOUND"){
            
                return Response::json(['errors'=>true,'msg' => 'No record found.','exist'=>'norecord']);
                
            }else{

                return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);
            }
            
            exit(); 
     
    }


    public function codeduplicate(Request $request){

        $FormId   = $this->form_id;
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $EMPLOYEE_TARGETCODE =   $request['EMPLOYEE_TARGETCODE'];
        $EMPLOYEE_TARGET_REF =   $request['EMPLOYEE_TARGET_REF'];
        $EMPLOYEE_TYPE       =   $request['EMPTARGETTYPE'];
        $FYID_REF1           =   $request['FYID_REF1'];       

        ///if($EMPTARGETTYPE=='DEMO')
        $objLabel = DB::table('TBL_MST_EMPLOYEE_TARGET')
        ->where('EMPLOYEE_TYPE','=',$EMPLOYEE_TYPE)
        ->where('EMPLOYEE_TARGETNAME','=',$EMPLOYEE_TARGET_REF)
        ->where('FYID_REF','=',$FYID_REF1)
        ->select('EMPLOYEE_TARGETNAME','EMPLOYEE_TYPE','FYID_REF')
        ->first();
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }


    public function cancel(Request $request){

        $id         = $request->{0};    
        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_MST_EMPLOYEE_TARGET";
        $FIELD      =   "ID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_MST_EMPLOYEE_TARGET',
        ];

        $req_data[1]=[
            'NT'  => 'TBL_MST_EMPLOYEE_TARGET_DETAILS',
        ];

        $req_data[2]=[
            'NT'  => 'TBL_MST_EMPLOYEE_TARGET_PRODUCT',
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


    public function attachment($id){

        if(!is_null($id))
        {
            $FormId      =   $this->form_id;
            $objResponse = DB::table('TBL_MST_EMPLOYEE_TARGET')
            ->where('ID','=',$id)
            ->first();

            $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
            ->where('VTID','=',$this->vtid_ref)
            ->select('VTID','VCODE','DESCRIPTIONS')
            ->get()->toArray();
            
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

            return view($this->view.$FormId.'attachment',compact(['objResponse','objMstVoucherType','objAttachments','FormId']));
        }

    }

    
   public function docuploads(Request $request){

    $FormId   =   $this->form_id;
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
    
    $destinationPath = storage_path()."/docs/company".$CYID_REF."/EmployeeTargetMaster";

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

                $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."#_".$filenamewithextension;  

                if ($uploadedFile->isValid()) {

                    if(in_array($extension,$allow_extnesions)){
                        
                        if($filesize < $allow_size){

                            $filename = $destinationPath."/".$filenametostore;

                            if (!file_exists($filename)) {

                               $uploadedFile->move($destinationPath, $filenametostore);
                               $uploaded_data[$index]["FILENAME"] =$filenametostore;
                               $uploaded_data[$index]["LOCATION"] = $destinationPath."/";
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
        return redirect()->route("master",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","The file is already exist");
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


/*************************************   Product Code    ****************************************************** */

        public function getProdctCode(Request $request){

            $ObjData = DB::table('TBL_MST_ITEM')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('STATUS','=','N')
            ->get();

            if(isset($ObjData) && !empty($ObjData)){
                foreach ($ObjData as $index=>$dataRow){

                echo'
                <tr>
                    <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->ITEMID .'" class="clsprodct" value="'.$dataRow->ITEMID.'" ></td>
                    <td class="ROW2" style="width: 47%">'.$dataRow->ICODE.'</td>
                    <td class="ROW3" style="width: 40%">'.$dataRow->NAME.'</td>
                    <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->ITEMID.'" data-code="'.$dataRow->ICODE .'" data-ccname="'.$dataRow->NAME.'" value="'.$dataRow->ITEMID.'"/></td>
                </tr>
                ';
                }
            }
            else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
        exit();
        }


                      
/*************************************   Financial Year Code    ****************************************************** */

            public function getFinancialYearCode(Request $request){
                $ObjData = DB::table('TBL_MST_FYEAR')
                ->where('STATUS','=','A')
                ->get();

                if(isset($ObjData) && !empty($ObjData)){
                    foreach ($ObjData as $index=>$dataRow){

                    echo'
                    <tr>
                        <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->FYID .'" class="clsfyear" value="'.$dataRow->FYID.'" ></td>
                        <td class="ROW2">'.$dataRow->FYCODE.'</td>
                        <td class="ROW3">'.$dataRow->FYDESCRIPTION.'</td>
                        <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->FYID.'" data-desc="'.$dataRow->FYCODE .'" data-ccname="'.$dataRow->FYDESCRIPTION.'" value="'.$dataRow->FYID.'"/></td>
                    </tr>
                    ';
                    }
                }
                else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
            exit();
            }


                    
/*************************************   Employee Target Code    ****************************************************** */

            public function getEmpTargetName(Request $request){

                $ObjData = DB::table('TBL_MST_EMPLOYEE')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('STATUS','=','A')
                ->get();

                if(isset($ObjData) && !empty($ObjData)){
                    foreach ($ObjData as $index=>$dataRow){

                    echo'
                    <tr>
                        <td class="ROW1"><input type="checkbox" id="subgl_'.$dataRow->EMPID .'" class="clsemptrgt" value="'.$dataRow->EMPID.'" ></td>
                        <td class="ROW2">'.$dataRow->EMPCODE.'</td>
                        <td class="ROW3">'.$dataRow->FNAME.'</td>
                        <td hidden><input type="hidden" id="txtsubgl_'.$dataRow->EMPID.'" data-desc="'.$dataRow->EMPCODE.'-'.$dataRow->FNAME.'" data-ccname="'.$dataRow->FNAME.'" value="'.$dataRow->EMPID.'"/></td>
                    </tr>
                    ';
                    }
                }
                else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
                }
            exit();
            }














































































}
