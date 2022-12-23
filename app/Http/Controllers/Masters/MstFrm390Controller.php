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
use App\Helpers\Helper;
use App\Helpers\Utils;
use Carbon\Carbon;

class MstFrm390Controller extends Controller
{
    protected $form_id = 390;
    protected $vtid_ref   = 227;
    //protected $view     = "masters.Payroll.LeaveOpeningBalance.mstfrm390"; 

    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){ 
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        
        $objRights  =   DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();
  
        $FormId         =   $this->form_id;
        $objDataList = DB::select("SELECT * FROM TBL_MST_LEAVE_OPENING T1 WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' ORDER BY T1.LEAVE_OPBLID DESC"); 
        return view('masters.Payroll.LeaveOpeningBalance.mstfrm390',compact(['objRights','FormId','objDataList'])); 
    }


    public function add(){       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $FormId = $this->form_id;

        $d_currency = DB::table('TBL_MST_COMPANY')
        ->where('STATUS','=',$Status)
        ->where('CYID','=',Auth::user()->CYID_REF)
        ->select('TBL_MST_COMPANY.CRID_REF')
        ->first();
        $objcurrency = $d_currency->CRID_REF;

        $objothcurrency = DB::table('TBL_MST_CURRENCY')
        ->where('STATUS','=',$Status)
        ->where('CRID','<>',$objcurrency)
        ->select('TBL_MST_CURRENCY.*')
        ->get()
        ->toArray();       
        
        $objYearList = DB::table('TBL_MST_YEAR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('YRID','YRCODE','YRDESCRIPTION')
        ->get();

        $ObjMstleavetype = DB::select("SELECT T1.LTID,T1.LEAVETYPE_CODE,T1.LEAVETYPE_DESC FROM TBL_MST_LEAVE_TYPE T1 WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF'");

        $objSON = DB::table('TBL_MST_DOCNO_DEFINITION')
            ->where('VTID_REF','=',$this->vtid_ref)
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
           // ->where('FYID_REF','=',$FYID_REF)
            ->where('STATUS','=',$Status)
            ->select('TBL_MST_DOCNO_DEFINITION.*')
            ->first();
            

        $objAutoGenNo='';
        if(!empty($objSON )){

            if($objSON->SYSTEM_GRSR == "1")
            {
                if($objSON->PREFIX_RQ == "1")
                {
                    $objAutoGenNo = $objSON->PREFIX;
                }        
                if($objSON->PRE_SEP_RQ == "1")
                {
                    if($objSON->PRE_SEP_SLASH == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'/';
                    }
                    if($objSON->PRE_SEP_HYPEN == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'-';
                    }
                }        
                if($objSON->NO_MAX)
                {   
                    $objAutoGenNo = $objAutoGenNo.str_pad($objSON->LAST_RECORDNO+1, $objSON->NO_MAX, "0", STR_PAD_LEFT);
                }
                
                if($objSON->NO_SEP_RQ == "1")
                {
                    if($objSON->NO_SEP_SLASH == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'/';
                    }
                    if($objSON->NO_SEP_HYPEN == "1")
                    {
                    $objAutoGenNo = $objAutoGenNo.'-';
                    }
                }
                if($objSON->SUFFIX_RQ == "1")
                {
                    $objAutoGenNo = $objAutoGenNo.$objSON->SUFFIX;
                }
            }
        } 

        $AlpsStatus =   $this->AlpsStatus();
       
    return view('masters.Payroll.LeaveOpeningBalance.mstfrm390add',compact(['objcurrency','objothcurrency','objSON','objAutoGenNo','objYearList','ObjMstleavetype','FormId','AlpsStatus']));       
   }



    public function getEmplyDetails(Request $request){
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $taxstate = $request['taxstate'];
        $StdCost = 0;

        $AlpsStatus =   $this->AlpsStatus();
        $objDataList = DB::select("SELECT T1.*,T2.*,T3.*
                FROM TBL_MST_EMPLOYEE T1
                LEFT JOIN TBL_MST_DESIGNATION T2 ON T1.DESGID_REF=T2.DESGID
                LEFT JOIN TBL_MST_DEPARTMENT T3 ON T1.DEPID_REF=T3.DEPID
                WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF'");        
     
     //dd($objDataList);
                if(!empty($objDataList)){

                    foreach ($objDataList as $index=>$dataRow){
                        $EMPLYID             =   isset($dataRow->EMPID)?$dataRow->EMPID:NULL;
                        $EMPCODE              =   isset($dataRow->EMPCODE)?$dataRow->EMPCODE:NULL;
                        $FNAME               =   isset($dataRow->FNAME)?$dataRow->FNAME:NULL;
                        $DESGCODE               =   isset($dataRow->DESGCODE)?$dataRow->DESGCODE:NULL;
                        $DCODE               =   isset($dataRow->DCODE)?$dataRow->DCODE:NULL;
                        $DESGID_REF               =   isset($dataRow->DESGID_REF)?$dataRow->DESGID_REF:NULL;
                        $DEPID_REF               =   isset($dataRow->DEPID_REF)?$dataRow->DEPID_REF:NULL;
                        
                        $EMPLY_SPECI         =   isset($dataRow->EMPLY_SPECI)?$dataRow->EMPLY_SPECI:NULL;                        
                        
                        $row = '';
                        $row.=' <tr id="item_'.$EMPLYID.'" class="clsitemid">
                                <td  style="width:8%; text-align: center;"><input type="checkbox" id="chkId'.$EMPLYID.'"  value="'.$EMPLYID.'" class="js-selectall1"  ></td>
                                <td style="width:10%;">'.$EMPCODE.'<input type="hidden" id="txtitem_'.$EMPLYID.'" data-desc="'.$EMPCODE.'" value="'.$EMPLYID.'"/></td>
                                <td style="width:10%;" id="itemname_'.$EMPLYID.'" >'.$FNAME.'<input type="hidden" id="txtitemname_'.$EMPLYID.'" data-desc="'.$FNAME.'" value="'.$FNAME.'"/></td>
                                <td style="width:8%;" id="itemuom_'.$EMPLYID.'" ><input type="hidden" id="txtitemuom_'.$EMPLYID.'" data-desc="'.$DESGCODE.'" value="'.$DESGID_REF.'"/>'.$DESGCODE.'</td>
                                <td style="width:8%;" id="deptmnt_'.$EMPLYID.'" ><input type="hidden" id="txtdeptmnt_'.$EMPLYID.'" data-desc="'.$DCODE.'" value="'.$DEPID_REF.'"/>'.$DCODE.'</td>
                                        
                                
                                <td style="width:8%;">Authorized</td>
                                </tr>'; 
                        echo $row;  
                    } 
                    
                }           
                else{
                 echo '<tr><td> Record not found.</td></tr>';
                }
        exit();
    }


   public function attachment($id){

    if(!is_null($id))
    {
        $objLeaveOpeningBalance = DB::table("TBL_MST_LEAVE_OPENING")
                        ->where('LEAVE_OPBLID','=',$id)
                        ->select('TBL_MST_LEAVE_OPENING.*')
                        ->first(); 

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

            return view('masters.Payroll.LeaveOpeningBalance.mstfrm390attachment',compact(['objLeaveOpeningBalance','objMstVoucherType','objAttachments']));
    }

}

    
   public function save(Request $request) {
    
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];
        
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['EMPLY_REF_'.$i]))
            {
                $req_data[$i] = [
                    'EMPID_REF'    => $request['EMPLY_REF_'.$i],
                    'LTID_REF' => $request['hdnleavetypecode_popup_'.$i],
                    'LEAVE_OPBL' => $request['OPNGBALANCE_'.$i],
                ];
            }
        }

        //dd($req_data);

        if(isset($req_data)) { 
           
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
            }
            else {
                $XMLMAT = NULL; 
            }  
        
            $USERID = Auth::user()->USERID;   
            $ACTION = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            //$FYID_REF = Session::get('FYID_REF');
            $UPDATE         =  Date('Y-m-d');
            $UPTIME         =  Date('h:i:s.u');
            $VTID = $this->vtid_ref;

            $LOB_NO = $request['LOB_NO'];
            $LOB_DT = $request['LOB_DT'];
            $FYID_REF = $request['FYID_REF'];
            //$REMARKS = $request['REMARKS'];
            

            $log_data = [ 
                $LOB_NO,    $LOB_DT,    $FYID_REF,  $XMLMAT,
                $CYID_REF,  $BRID_REF,  $VTID,      $USERID,
                $UPDATE,    $UPTIME,    $ACTION,    $IPADDRESS
            ];

            //dd($log_data);
            
            $sp_result = DB::select('EXEC SP_LEAVE_OPENING_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $log_data);       
        
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

            }else{
                return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
            }
            exit();   
     }

    public function edit($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        $FormId = $this->form_id;

        if(!is_null($id))
        {
            
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
            
            $objYearList = DB::table('TBL_MST_YEAR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            //->where('BRID_REF','=',Session::get('BRID_REF'))
            //->where('FYID_REF','=',Session::get('FYID_REF'))
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('YRID','YRCODE','YRDESCRIPTION')
            ->get();


            $HDR = DB::table('TBL_MST_LEAVE_OPENING')               
            ->where('LEAVE_OPBLID','=',$id)
            ->first();

            $objMAT = DB::select("SELECT T1.*,T2.*,T3.*,T4.*,T5.* FROM TBL_MST_EMPLOYEE T1 LEFT JOIN TBL_MST_DESIGNATION T2 ON T1.DESGID_REF=T2.DESGID
                LEFT JOIN TBL_MST_DEPARTMENT T3 ON T1.DEPID_REF=T3.DEPID LEFT JOIN TBL_MST_LEAVE_OPENING_DETAIL T4 ON T1.EMPID=T4.EMPID_REF LEFT JOIN TBL_MST_LEAVE_TYPE T5 ON T4.LTID_REF=T5.LTID
                WHERE T4.LEAVE_OPBLID_REF= $id");
           //dd($objMAT); 
                  
            $ObjMstleavetype = DB::select("SELECT T1.LTID,T1.LEAVETYPE_CODE,T1.LEAVETYPE_DESC FROM TBL_MST_LEAVE_TYPE T1 WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF'");
            $AlpsStatus =   $this->AlpsStatus();
            $InputStatus =   "";
            
            return view('masters.Payroll.LeaveOpeningBalance.mstfrm390edit',compact(['objYearList','ObjMstleavetype','objRights',
            'FormId','HDR','objMAT','AlpsStatus','InputStatus']));
            }
     
       }
     
       public function view($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        $FormId = $this->form_id;

        if(!is_null($id))
        {
            
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first(); 
                             
                $objYearList = DB::table('TBL_MST_YEAR')
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    //->where('BRID_REF','=',Session::get('BRID_REF'))
                    //->where('FYID_REF','=',Session::get('FYID_REF'))
                    ->where('STATUS','=','A')
                    ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                    ->select('YRID','YRCODE','YRDESCRIPTION')
                    ->get();


                    $HDR = DB::table('TBL_MST_LEAVE_OPENING')               
                    ->where('LEAVE_OPBLID','=',$id)
                    ->first();

                    $objMAT = DB::select("SELECT T1.*,T2.*,T3.*,T4.*,T5.* FROM TBL_MST_EMPLOYEE T1 LEFT JOIN TBL_MST_DESIGNATION T2 ON T1.DESGID_REF=T2.DESGID LEFT JOIN TBL_MST_DEPARTMENT T3 ON T1.DEPID_REF=T3.DEPID
                        LEFT JOIN TBL_MST_LEAVE_OPENING_DETAIL T4 ON T1.EMPID=T4.EMPID_REF LEFT JOIN TBL_MST_LEAVE_TYPE T5 ON T4.LTID_REF=T5.LTID
                        WHERE T4.LEAVE_OPBLID_REF= $id");
                //dd($objMAT); 
                        
                    $ObjMstleavetype = DB::select("SELECT T1.LTID,T1.LEAVETYPE_CODE,T1.LEAVETYPE_DESC FROM TBL_MST_LEAVE_TYPE T1 WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF'");
            
                    $AlpsStatus =   $this->AlpsStatus();
                    $InputStatus =   "disabled";

            return view('masters.Payroll.LeaveOpeningBalance.mstfrm390view',compact(['objYearList','ObjMstleavetype','objRights',
            'FormId','HDR','objMAT','AlpsStatus','InputStatus']));
        
        }
     
       }
  
   public function update(Request $request){

    //dd($request->all());
    $r_count1 = $request['Row_Count1'];
    $r_count2 = $request['Row_Count2'];
    $r_count3 = $request['Row_Count3'];
    
    
    for ($i=0; $i<=$r_count1; $i++)
    {
        if(isset($request['EMPLY_REF_'.$i]))
        {
            $req_data[$i] = [                
                'LEAVE_OPBLID_REF'    => $request['LEAVE_OPBLID_REF_'.$i],
                'EMPID_REF'    => $request['EMPLY_REF_'.$i],
                'LTID_REF' => $request['hdnleavetypecode_popup_'.$i],
                'LEAVE_OPBL' => $request['OPNGBALANCE_'.$i],
            ];
        }
    }
    //dd($req_data);    
    if(isset($req_data)) { 
           
        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        }
        else {
            $XMLMAT = NULL; 
        }  
    
        $VTID_REF     =   $this->vtid_ref;
        $USERID = Auth::user()->USERID;   
        $ACTION = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        //$FYID_REF = Session::get('FYID_REF');
        $UPDATE         =  Date('Y-m-d');
        $UPTIME         = Date('h:i:s.u');
        $VTID = $this->vtid_ref;
        $LOB_NO = $request['LOB_NO'];
        $LOB_DT = $request['LOB_DT'];
        $LEAVE_OPBLID = NULL;
        $FYID_REF = $request['FYID_REF'];
        //$REMARKS = $request['REMARKS'];

        $log_data = [ 
            $LEAVE_OPBLID,  $LOB_NO,    $LOB_DT,    $FYID_REF,  $XMLMAT,
            $CYID_REF,  $BRID_REF,  $VTID,      $USERID,
            $UPDATE,    $UPTIME,    $ACTION,    $IPADDRESS
        ];

        //dd($log_data);
        $sp_result = DB::select('EXEC SP_LEAVE_OPENING_UP ?,?,?,?,?, ?,?,?,?, ?,?,?,?', $log_data);
        //dd($sp_result);
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
        }
        exit();  
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
                foreach ($sp_listing_result as $key=>$val)
            {  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$val->LAVELS;
            }
            }
           
            $r_count1 = $request['Row_Count1'];
            $r_count2 = $request['Row_Count2'];
            $r_count3 = $request['Row_Count3'];
            
            for ($i=0; $i<=$r_count1; $i++)
            {
                if(isset($request['EMPLY_REF_'.$i]))
                {
                    $req_data[$i] = [                
                        'EMPID_REF'    => $request['EMPLY_REF_'.$i],
                        'LTID_REF' => $request['hdnleavetypecode_popup_'.$i],
                        'LEAVE_OPBL' => $request['OPNGBALANCE_'.$i],
                    ];
                }
            }


            if(isset($req_data)) { 
           
                $wrapped_links["MAT"] = $req_data; 
                $XMLMAT = ArrayToXml::convert($wrapped_links);
                }
                else {
                    $XMLMAT = NULL; 
                }  
            
                $LEAVE_OPBLID = $request['LEAVE_OPBLID'];
                $LOB_NO = $request['LOB_NO'];
                $LOB_DT = $request['LOB_DT'];               
                $FYID_REF = $request['FYID_REF'];

                $VTID_REF       =   $this->vtid_ref;
                $USERID         =   Auth::user()->USERID;   
                $VTID           =   $this->vtid_ref;
                $CYID_REF       =   Auth::user()->CYID_REF;
                $BRID_REF       =   Session::get('BRID_REF');
                //$FYID_REF     =   Session::get('FYID_REF');
                $UPDATE         =   Date('Y-m-d');
                $UPTIME         =   Date('h:i:s.u');
                $ACTION         =   $Approvallevel;
                $IPADDRESS      =   $request->getClientIp();
        
                $log_data = [ 
                    $LEAVE_OPBLID,  $LOB_NO,    $LOB_DT,    $FYID_REF,  $XMLMAT,
                    $CYID_REF,  $BRID_REF,  $VTID,      $USERID,
                    $UPDATE,    $UPTIME,    $ACTION,    $IPADDRESS
                ];                
               
                //dd($log_data);
                
                $sp_result = DB::select('EXEC SP_LEAVE_OPENING_UP ?,?,?,?,?, ?,?,?,?, ?,?,?,?', $log_data);       
                
                //dd($sp_result);

                if($sp_result[0]->RESULT=="SUCCESS"){
        
                    return Response::json(['success' =>true,'msg' => 'Record successfully approved.']);
        
                }else{
                    return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
                }
                exit();     
        }
        
    
    public function cancel(Request $request){

         $id = $request->{0};

         $objResponse = DB::table('TBL_MST_LEAVE_OPENING')->where('LEAVE_OPBLID','=',$id)->select('*')->first();
         $FYID_REF = $objResponse->FYID_REF;

         $USERID_REF =   Auth::user()->USERID;
         $VTID_REF   =   $this->vtid_ref;  //voucher type id
         $CYID_REF   =   Auth::user()->CYID_REF;
         $BRID_REF   =   Session::get('BRID_REF');
         $FYID_REF   =   $FYID_REF;       
         $TABLE      =   "TBL_MST_LEAVE_OPENING";
         $FIELD      =   "LEAVE_OPBLID";
         $ID         =   $id;
         $UPDATE     =   Date('Y-m-d');
         $UPTIME     =   Date('h:i:s.u');
         $IPADDRESS  =   $request->getClientIp();
 
         $req_data[0]=[
             'NT'  => 'TBL_MST_LEAVE_OPENING_DETAIL',
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


   public function docuploads(Request $request){

    $formData = $request->all();

    $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
    $allow_size = config("erpconst.attachments.max_size") * 1024 * 1024;

  
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
    
    $image_path         =   "docs/company".$CYID_REF."/LeaveOpeningBalance";     
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
                
                echo $filenametostore ;

                if ($uploadedFile->isValid()) {

                    if(in_array($extension,$allow_extnesions)){
                        
                        if($filesize < $allow_size){

                            $custfilename = $destinationPath."/".$filenametostore;

                            if (!file_exists($custfilename)) {

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
        return redirect()->route("master",[390,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
    
      
   try {

      
         $sp_result = DB::select('EXEC SP_TRN_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);

   } catch (\Throwable $th) {
    
       return redirect()->route("master",[390,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("master",[390,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("master",[390,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("master",[390,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

    public function checkoso(Request $request){

        
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $LOB_NO = $request->LOB_NO;
        
        $objSO = DB::table('TBL_MST_LEAVE_OPENING')
        ->where('TBL_MST_LEAVE_OPENING.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_LEAVE_OPENING.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_MST_LEAVE_OPENING.LOB_NO','=',$LOB_NO)
        ->select('TBL_MST_LEAVE_OPENING.LEAVE_OPBLID')
        ->first();
        
        if($objSO){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate LOB_NO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }

    public function AlpsStatus(){

        $COMPANY_NAME   =   DB::table('TBL_MST_COMPANY')->where('STATUS','=','A')->where('CYID','=',Auth::user()->CYID_REF)->select('TBL_MST_COMPANY.NAME')->first()->NAME;
    
        $disabled       =   strpos($COMPANY_NAME,"ALPS")!== false?'disabled':'';
        $hidden         =   strpos($COMPANY_NAME,"ALPS")!== false?'':'hidden';
       
        return  $ALPS_STATUS=array(
            'hidden'=>$hidden,
            'disabled'=>$disabled
        );
    
    }

    
}
