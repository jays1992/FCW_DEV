<?php

namespace App\Http\Controllers\Transactions;

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
use App\Helpers\Helper;
use App\Helpers\Utils;
use Carbon\Carbon;

class TrnFrm416Controller extends Controller
{
   
    protected $form_id  = 416;
    protected $vtid_ref = 490;  //voucher type id
    protected $view     = "transactions.Payroll.ProvisionOfBonusGratuityLeaveEncashment.trnfrm";

       
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
        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');     

        $objDataList = DB::table('TBL_TRN_PROVISION_GRAT_BONUS')
            ->select('TBL_TRN_PROVISION_GRAT_BONUS.*')
            ->orderBy('TBL_TRN_PROVISION_GRAT_BONUS.PGBID', 'DESC')
            ->get();

            return view($this->view.$FormId,compact(['objRights','objDataList','FormId']));
        }

    public function add(){ 

        $FormId   =   $this->form_id;
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $objList      = $this->payperiod();
        $objDataList  = $this->employee();
        $objEarnHead  = $this->EaringHead();
        $objBonus     = $this->Bonus();
        $objGraty     = $this->Gratuity();
        $objLeaveEnt  = $this->LeaveEncashment();

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_PROVISION_GRAT_BONUS',
            'HDR_ID'=>'PGBID',
            'HDR_DOC_NO'=>'PGB_CODE',
            'HDR_DOC_DT'=>'PGB_DATE'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

        
        

        return view($this->view.$FormId.'add',compact(['FormId','objList','objEarnHead','objDataList','objBonus','objGraty','objLeaveEnt','doc_req','docarray'])); 
    }
  
    public function getPayPrName(Request $request){
        
        $PAYPERIODID  =   $request['PAYPERIODID'];
		
		$objPayPrName = DB::table('TBL_MST_PAY_PERIOD')
        ->where('PAYPERIODID','=', $PAYPERIODID )
        ->select('PAY_PERIOD_DESC')
        ->first();
		
		if(!empty($objPayPrName)){
			echo $objPayPrName->PAY_PERIOD_DESC;
		}
		else{
			echo "";
		}
        exit();
    }

    public function getEmpName(Request $request){
        
        $EMPID   =   $request['EMPID'];
		$objEmpName = DB::table('TBL_MST_EMPLOYEE')
        ->where('EMPID','=', $EMPID )
        ->select('FNAME')
        ->first();
		
		if(!empty($objEmpName)){
			echo $objEmpName->FNAME;
		}
		else{
			echo "";
		}
        exit();
    }


    public function getEearHeadName(Request $request){
        
        $EARNING_HEADID   =   $request['EARNING_HEADID'];
		$objEarnName = DB::table('TBL_MST_EARNING_HEAD')
        ->where('EARNING_HEADID','=', $EARNING_HEADID )
        ->select('EARNING_HEAD_DESC')
        ->first();
		
		if(!empty($objEarnName)){
			echo $objEarnName->EARNING_HEAD_DESC;
		}
		else{
			echo "";
		}
        exit();
    }


    public function getEmpCodeName(Request $request){
        
        $EMPID  =   $request['EMPID'];
		
		$objEmpCodeName = DB::table('TBL_MST_EMPLOYEE')
        ->where('EMPID','=', $EMPID )
        ->select('FNAME')
        ->first();
		
		if(!empty($objEmpCodeName)){
			echo $objEmpCodeName->FNAME;
		}
		else{
			echo "";
		}
        exit();
    }

    public function getBonusName(Request $request){
        
        $BONUSID  =   $request['BONUSID'];
		
		$objBonusCode = DB::table('TBL_MST_BONUS')
        ->where('BONUSID','=', $BONUSID )
        ->select('BONUS_DESC')
        ->first();
		
		if(!empty($objBonusCode)){
			echo $objBonusCode->BONUS_DESC;
		}
		else{
			echo "";
		}
        exit();
    }


    public function getGratuityName(Request $request){
        
        $GRATUITYID  =   $request['GRATUITYID'];
		
		$objGratyCode = DB::table('TBL_MST_GRATUITY')
        ->where('GRATUITYID','=', $GRATUITYID )
        ->select('GRATUITY_DESC')
        ->first();
		
		if(!empty($objGratyCode)){
			echo $objGratyCode->GRATUITY_DESC;
		}
		else{
			echo "";
		}
        exit();
    }


    public function getLeaveEntName(Request $request){
        
        $LEPID  =   $request['LEPID'];
		
		$objLeaveEntCode = DB::table('TBL_MST_LEAVE_ENCASHMENT_PROVISION')
        ->where('LEPID','=', $LEPID )
        ->select('LEAVE_TYPE_DESC')
        ->first();
		
		if(!empty($objLeaveEntCode)){
			echo $objLeaveEntCode->LEAVE_TYPE_DESC;
		}
		else{
			echo "";
		}
        exit();
    }


   public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $ATTCODE =   $request['ATTCODE'];
        
        $objLabel = DB::table('TBL_MST_ATTRIBUTE')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('ATTCODE','=',$ATTCODE)
        ->select('ATTCODE')
        ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
   }

   public function save(Request $request){

        $data = array();
        if(isset($_REQUEST['EMPCODE_REF']) && !empty($_REQUEST['EMPCODE_REF'])){
            foreach($_REQUEST['EMPCODE_REF'] as $key=>$val){

                $data[] = array(
                'EMPID_REF'  => trim($_REQUEST['EMPCODE_REF'][$key]),
                'AMOUNT'     => trim($_REQUEST['AMOUNT'][$key]),
                );
            }
        }
        //dd($data);

        if(!empty($data)){
            $wrapped_links["MAT"] = $data; 
            $MAT = ArrayToXml::convert($wrapped_links);
        }
        else{
            $MAT = NULL; 
        }

        $PGB_CODE          =   trim($request['PROVISION_DOC_NO']);
        $PGB_DATE          =   trim($request['PROVISION_DT']);
        $BONUS             =   trim($request['BONUS']);
        $GRTUITY           =   trim($request['GRTUITY']);
        $LEAVE_ENCASH      =   trim($request['LEAVE_ENCASH']);
        $PAYPERIODID_FROM  =   trim($request['PAYPERIOD_FROM']);
        $PAYPERIODID_TO    =   trim($request['PAYPERIOD_TO']);
        $GENERATE          =   trim($request['GENERATE']);
        $HEADID_REF        =   trim($request['HEADID_REF']);

        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       = Session::get('FYID_REF');
        $VTID_REF       =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();
        
        $array_data   = [
                    $PGB_CODE,   $PGB_DATE,     $BONUS,     $GRTUITY,   $LEAVE_ENCASH,    $PAYPERIODID_FROM,    $PAYPERIODID_TO,
                    $GENERATE,   $HEADID_REF,   $CYID_REF,  $BRID_REF,  $FYID_REF,        $VTID_REF,            $MAT,       
                    $USERID,     $UPDATE,       $UPTIME,    $ACTION,      $IPADDRESS         
                    ];

        $sp_result = DB::select('EXEC SP_TRN_PROVISION_GRAT_BONUS_IN ?,?,?,?,?,?,?, ?,?,?,?,?,?,?,   ?,?,?,?,?', $array_data);

        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }
        exit();      
    }

    public function edit($id){

        if(!is_null($id))
        {
        
            $FormId     =   $this->form_id;
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objResponse = DB::table('TBL_TRN_PROVISION_GRAT_BONUS')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('PGBID','=',$id)
            ->select('*')
            ->first();         
            if(strtoupper($objResponse->STATUS)=="A" || strtoupper($objResponse->STATUS)=="C"){
                exit("Sorry, Only Un Approved record can edit.");
            }

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $objList      = $this->payperiod();
            $objDataList  = $this->employee();
            $objEarnHead  = $this->EaringHead();
            $objBonus     = $this->Bonus();
            $objGraty     = $this->Gratuity();
            $objLeaveEnt  = $this->LeaveEncashment();
    
            
            $HDR = DB::table('TBL_TRN_PROVISION_GRAT_BONUS')
            ->where('TBL_TRN_PROVISION_GRAT_BONUS.PGBID','=',$id)
            ->leftJoin('TBL_MST_PAY_PERIOD', 'TBL_TRN_PROVISION_GRAT_BONUS.PAYPERIODID_FROM','=','TBL_MST_PAY_PERIOD.PAYPERIODID')
            ->leftJoin('TBL_MST_BONUS', 'TBL_TRN_PROVISION_GRAT_BONUS.HEADID_REF','=','TBL_MST_BONUS.BONUSID')
            ->leftJoin('TBL_MST_GRATUITY', 'TBL_TRN_PROVISION_GRAT_BONUS.HEADID_REF','=','TBL_MST_GRATUITY.GRATUITYID')
            ->leftJoin('TBL_MST_LEAVE_ENCASHMENT_PROVISION', 'TBL_TRN_PROVISION_GRAT_BONUS.HEADID_REF','=','TBL_MST_LEAVE_ENCASHMENT_PROVISION.LEPID')
            ->select('TBL_TRN_PROVISION_GRAT_BONUS.*','TBL_MST_PAY_PERIOD.*','TBL_MST_BONUS.*','TBL_MST_GRATUITY.*','TBL_MST_LEAVE_ENCASHMENT_PROVISION.*')
            ->first();

            $MAT = DB::table('TBL_TRN_PROVISION_GRAT_BONUS_MAT')                    
            ->where('TBL_TRN_PROVISION_GRAT_BONUS_MAT.PGBID_REF','=',$id)
            ->leftJoin('TBL_TRN_PROVISION_GRAT_BONUS', 'TBL_TRN_PROVISION_GRAT_BONUS_MAT.PGBID_REF','=','TBL_TRN_PROVISION_GRAT_BONUS.PGBID')
            ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_TRN_PROVISION_GRAT_BONUS_MAT.EMPID_REF','=','TBL_MST_EMPLOYEE.EMPID')
            ->select('TBL_TRN_PROVISION_GRAT_BONUS_MAT.*','TBL_TRN_PROVISION_GRAT_BONUS.*','TBL_MST_EMPLOYEE.*')
            ->get()->toArray();

            $objCount = count($MAT);         

            return view($this->view.$FormId.'edit',compact(['FormId','objResponse','HDR','objList','objDataList','objEarnHead','objBonus','objGraty','objLeaveEnt','user_approval_level','objRights','MAT','objCount']));
        }

    }

     
    public function update(Request $request)
    {

      //dd($request->all());

      $data = array();
      if(isset($_REQUEST['EMPCODE_REF']) && !empty($_REQUEST['EMPCODE_REF'])){
          foreach($_REQUEST['EMPCODE_REF'] as $key=>$val){

              $data[] = array(
              'EMPID_REF'  => trim($_REQUEST['EMPCODE_REF'][$key]),
              'AMOUNT'     => trim($_REQUEST['AMOUNT'][$key]),
              );
          }
      }
      //dd($data);

      if(!empty($data)){
          $wrapped_links["MAT"] = $data; 
          $MAT = ArrayToXml::convert($wrapped_links);
      }
      else{
          $MAT = NULL; 
      }

      $PGB_CODE          =   trim($request['PROVISION_DOC_NO']);
      $PGB_DATE          =   trim($request['PROVISION_DT']);
      $BONUS             =   trim($request['BONUS']);
      $GRTUITY           =   trim($request['GRTUITY']);
      $LEAVE_ENCASH      =   trim($request['LEAVE_ENCASH']);
      $PAYPERIODID_FROM  =   trim($request['PAYPERIOD_FROM']);
      $PAYPERIODID_TO    =   trim($request['PAYPERIOD_TO']);
      $GENERATE          =   trim($request['GENERATE']);
      $HEADID_REF        =   trim($request['HEADID_REF']);

      $CYID_REF       =   Auth::user()->CYID_REF;
      $BRID_REF       =   Session::get('BRID_REF');
      $FYID_REF       =   Session::get('FYID_REF');
      $VTID_REF       =   $this->vtid_ref;
      $USERID         =   Auth::user()->USERID;
      $UPDATE         =   Date('Y-m-d');
      $UPTIME         =   Date('h:i:s.u');
      $ACTION         =   "EDIT";
      $IPADDRESS      =   $request->getClientIp();
      
      $array_data   = [
                  $PGB_CODE,   $PGB_DATE,     $BONUS,     $GRTUITY,   $LEAVE_ENCASH,    $PAYPERIODID_FROM,    $PAYPERIODID_TO,
                  $GENERATE,   $HEADID_REF,   $CYID_REF,  $BRID_REF,  $FYID_REF,        $VTID_REF,            $MAT,       
                  $USERID,     $UPDATE,       $UPTIME,    $ACTION,      $IPADDRESS         
                  ];

      $sp_result = DB::select('EXEC SP_TRN_PROVISION_GRAT_BONUS_UP ?,?,?,?,?,?,?, ?,?,?,?,?,?,?,   ?,?,?,?,?', $array_data);

      $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
  
      if($contains){
          return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

      }else{
          return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
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
        $FormId         =   $this->form_id;
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
 
		$destinationPath = storage_path()."/docs/company".$CYID_REF."/AssignLeave";

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
            return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
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

            return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            //return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
            return redirect()->route("transaction",[$FormId,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   


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
           
            $data = array();
            if(isset($_REQUEST['EMPCODE_REF']) && !empty($_REQUEST['EMPCODE_REF'])){
                foreach($_REQUEST['EMPCODE_REF'] as $key=>$val){
      
                    $data[] = array(
                    'EMPID_REF'  => trim($_REQUEST['EMPCODE_REF'][$key]),
                    'AMOUNT'     => trim($_REQUEST['AMOUNT'][$key]),
                    );
                }
            }
            //dd($data);
      
            if(!empty($data)){
                $wrapped_links["MAT"] = $data; 
                $MAT = ArrayToXml::convert($wrapped_links);
            }
            else{
                $MAT = NULL; 
            }
      
            $PGB_CODE          =   trim($request['PROVISION_DOC_NO']);
            $PGB_DATE          =   trim($request['PROVISION_DT']);
            $BONUS             =   trim($request['BONUS']);
            $GRTUITY           =   trim($request['GRTUITY']);
            $LEAVE_ENCASH      =   trim($request['LEAVE_ENCASH']);
            $PAYPERIODID_FROM  =   trim($request['PAYPERIOD_FROM']);
            $PAYPERIODID_TO    =   trim($request['PAYPERIOD_TO']);
            $GENERATE          =   trim($request['GENERATE']);
            $HEADID_REF        =   trim($request['HEADID_REF']);
      
            $CYID_REF       =   Auth::user()->CYID_REF;
            $BRID_REF       =   Session::get('BRID_REF');
            $FYID_REF       =   Session::get('FYID_REF');
            $VTID_REF       =   $this->vtid_ref;
            $USERID         =   Auth::user()->USERID;
            $UPDATE         =   Date('Y-m-d');
            $UPTIME         =   Date('h:i:s.u');
            $ACTION         =   $Approvallevel;
            $IPADDRESS      =   $request->getClientIp();
            
            $array_data   = [
                        $PGB_CODE,   $PGB_DATE,     $BONUS,     $GRTUITY,   $LEAVE_ENCASH,    $PAYPERIODID_FROM,    $PAYPERIODID_TO,
                        $GENERATE,   $HEADID_REF,   $CYID_REF,  $BRID_REF,  $FYID_REF,        $VTID_REF,            $MAT,       
                        $USERID,     $UPDATE,       $UPTIME,    $ACTION,      $IPADDRESS         
                        ];
      
            $sp_result = DB::select('EXEC SP_TRN_PROVISION_GRAT_BONUS_UP ?,?,?,?,?,?,?, ?,?,?,?,?,?,?,   ?,?,?,?,?', $array_data);
      
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
        
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);
      
            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            exit();     
    }

    public function view($id){

        if(!is_null($id))
        {
        
            $FormId     =   $this->form_id;
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objResponse = DB::table('TBL_TRN_PROVISION_GRAT_BONUS')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('PGBID','=',$id)
            ->select('*')
            ->first();         
            if(strtoupper($objResponse->STATUS)=="A" || strtoupper($objResponse->STATUS)=="C"){
                exit("Sorry, Only Un Approved record can edit.");
            }

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $objList      = $this->payperiod();
            $objDataList  = $this->employee();
            $objEarnHead  = $this->EaringHead();
            $objBonus     = $this->Bonus();
            $objGraty     = $this->Gratuity();
            $objLeaveEnt  = $this->LeaveEncashment();
    
            
            $HDR = DB::table('TBL_TRN_PROVISION_GRAT_BONUS')
            ->where('TBL_TRN_PROVISION_GRAT_BONUS.PGBID','=',$id)
            ->leftJoin('TBL_MST_PAY_PERIOD', 'TBL_TRN_PROVISION_GRAT_BONUS.PAYPERIODID_FROM','=','TBL_MST_PAY_PERIOD.PAYPERIODID')
            ->leftJoin('TBL_MST_BONUS', 'TBL_TRN_PROVISION_GRAT_BONUS.HEADID_REF','=','TBL_MST_BONUS.BONUSID')
            ->leftJoin('TBL_MST_GRATUITY', 'TBL_TRN_PROVISION_GRAT_BONUS.HEADID_REF','=','TBL_MST_GRATUITY.GRATUITYID')
            ->leftJoin('TBL_MST_LEAVE_ENCASHMENT_PROVISION', 'TBL_TRN_PROVISION_GRAT_BONUS.HEADID_REF','=','TBL_MST_LEAVE_ENCASHMENT_PROVISION.LEPID')
            ->select('TBL_TRN_PROVISION_GRAT_BONUS.*','TBL_MST_PAY_PERIOD.*','TBL_MST_BONUS.*','TBL_MST_GRATUITY.*','TBL_MST_LEAVE_ENCASHMENT_PROVISION.*')
            ->first();

            $MAT = DB::table('TBL_TRN_PROVISION_GRAT_BONUS_MAT')                    
            ->where('TBL_TRN_PROVISION_GRAT_BONUS_MAT.PGBID_REF','=',$id)
            ->leftJoin('TBL_TRN_PROVISION_GRAT_BONUS', 'TBL_TRN_PROVISION_GRAT_BONUS_MAT.PGBID_REF','=','TBL_TRN_PROVISION_GRAT_BONUS.PGBID')
            ->leftJoin('TBL_MST_EMPLOYEE', 'TBL_TRN_PROVISION_GRAT_BONUS_MAT.EMPID_REF','=','TBL_MST_EMPLOYEE.EMPID')
            ->select('TBL_TRN_PROVISION_GRAT_BONUS_MAT.*','TBL_TRN_PROVISION_GRAT_BONUS.*','TBL_MST_EMPLOYEE.*')
            ->get()->toArray();

            $objCount = count($MAT);         

            return view($this->view.$FormId.'view',compact(['FormId','objResponse','HDR','objList','objDataList','objEarnHead','objBonus','objGraty','objLeaveEnt','user_approval_level','objRights','MAT','objCount']));
        }


    }
  
    public function printdata(Request $request){
        //
        $ids_data = [];
        if(isset($request->records_ids)){
            
            $ids_data = explode(",",$request->records_ids);
        }

        $objResponse = TblMstFrm416::whereIn('ATTID',$ids_data)->get();
        
        return view('transactions.Payroll.AssignLeave.trnfrm416print',compact(['objResponse']));
   }//print

    
    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
            $objResponse = DB::table('TBL_TRN_PROVISION_GRAT_BONUS')->where('PGBID','=',$id)->select('*')->first();

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
                        ->where('TBL_MST_ATTACHMENT.BRID_REF','=',Session::get('BRID_REF'))
                        ->where('TBL_MST_ATTACHMENT.FYID_REF','=',Session::get('FYID_REF'))
                        ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                        ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                        ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                        ->get()->toArray();

                 // dump( $objAttachments);

                 $FormId    =   $this->form_id;

                 return view($this->view.$FormId.'attachment',compact(['FormId','objResponse','objMstVoucherType','objAttachments']));
        }

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
            $TABLE      =   "TBL_MST_ATTRIBUTE";
            $FIELD      =   "ATTID";
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
   
            $USERID =   Auth::user()->USERID;
            $VTID   =   $this->vtid_ref;  //voucher type id
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');
            $FYID_REF   =   Session::get('FYID_REF');  
            $TABLE      =   "TBL_TRN_PROVISION_GRAT_BONUS";
            $FIELD      =   "PGBID";
            $ID         =   $id;
            $UPDATE     =   Date('Y-m-d');
            $UPTIME     =   Date('h:i:s.u');
            $IPADDRESS  =   $request->getClientIp();
    
            $req_data[0]=[
                'NT'  => 'TBL_TRN_PROVISION_GRAT_BONUS_MAT',
            ];
        
            $wrapped_links["TABLES"] = $req_data; 
            
            $XMLTAB = ArrayToXml::convert($wrapped_links);
            
            $mst_cancel_data = [ $USERID, $VTID, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$XMLTAB];
            $sp_result = DB::select('EXEC SP_TRN_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);
   
            if($sp_result[0]->RESULT=="CANCELED"){  
              
              return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
            
            }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
            
                
                return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
                
            }else{
               
                   return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'invalid'=>'invalid']);
            }
            
            exit(); 
   
   
       }

        public function payperiod(){
        $objpayperiod = DB::table('TBL_MST_PAY_PERIOD')
        ->select('PAYPERIODID','PAY_PERIOD_CODE','PAY_PERIOD_DESC')
        ->get();
        return $objpayperiod; 
        }
        public function employee(){
        $objemp    =   DB::table('TBL_MST_EMPLOYEE')
        ->where('STATUS','=','A')
        ->get();
        return $objemp; 
        }
        public function  EaringHead(){
        $EarnHead    =   DB::table('TBL_MST_EARNING_HEAD')
        ->where('STATUS','=','A')
        ->get();
        return $EarnHead; 
        }

        public function  Bonus(){
            $BonusData    =   DB::table('TBL_MST_BONUS')->first();
            return $BonusData; 
        }

        public function  Gratuity(){
            $GratyData    =   DB::table('TBL_MST_GRATUITY')->first();
            return $GratyData; 
        }

        public function  LeaveEncashment(){
            $LeaveEntData    =   DB::table('TBL_MST_LEAVE_ENCASHMENT_PROVISION')->first();
            return $LeaveEntData; 
        }

   
        

}
