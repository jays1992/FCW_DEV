<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Helpers\Utils;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use DateTime;

class MstFrm48Controller extends Controller{
   
    protected $form_id = 48;
    protected $vtid_ref   = 48;

    public function __construct(){
        $this->middleware('auth');
    }


    public function index(){

        $objRights = DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 

        $TotalRow   =   DB::select("SELECT count(T1.VID) AS TOTAL_ROW
        FROM TBL_MST_VENDOR T1
		INNER JOIN TBL_MST_VENDOR_BRANCH_MAP M ON T1.VID = M.VID_REF
        INNER JOIN TBL_MST_AUDITTRAIL T2 ON T1.VID=T2.VID AND T1.CYID_REF=T2.CYID_REF AND T1.BRID_REF=T2.BRID_REF AND T2.VTID_REF = '$this->vtid_ref' AND T2.ACTID IN (SELECT max(ACTID) FROM TBL_MST_AUDITTRAIL A WHERE T2.VTID_REF = A.VTID_REF AND T2.VID = A.VID)
        WHERE T1.CYID_REF='$CYID_REF' AND M.MAPBRID_REF = '$BRID_REF' ")[0]->TOTAL_ROW;

        return view('masters.purchase.vendormaster.mstfrm48',compact(['TotalRow','objRights']));
        
    }

    public function getListingData(Request $request){
   
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $USERID         =   Auth::user()->USERID;

        $VCODE          =   trim($_REQUEST['VCODE']);
        $NAME           =   trim($_REQUEST['NAME']);
        $STATE          =   trim($_REQUEST['STATE']);
        $GSTIN          =   trim($_REQUEST['GSTIN']);
        $INDATE         =   trim($_REQUEST['INDATE']);
        $STATUS         =   trim($_REQUEST['STATUS']);
       
        $start          =   $_POST["start"];
        $limit          =   $_POST["limit"];
       
        $W_VCODE        =   $VCODE !=''?"AND T1.VCODE ='$VCODE'":'';
        $W_NAME         =   $NAME !=''?"AND T1.NAME LIKE '%$NAME%'":'';
        $W_GSTIN        =   $GSTIN !=''?"AND T1.GSTIN LIKE '%$GSTIN%'":'';
        $W_STATUS       =   $STATUS !=''?"AND T1.STATUS ='$STATUS'":'';
       
        $WHERE_FIELD    =   trim("$W_VCODE $W_NAME $W_GSTIN $W_STATUS"); 
        
        $REQUEST_DATA   =   array(
            'FORMID'    =>  $this->form_id,
            'VTID_REF'  =>  $this->vtid_ref,
            'USERID'    =>  Auth::user()->USERID,
            'CYID_REF'  =>  Auth::user()->CYID_REF,
            'BRID_REF'  =>  Session::get('BRID_REF'),
            'FYID_REF'  =>  Session::get('FYID_REF'),
        );
        
        $DATA_STATUS    =	Helper::get_user_level($REQUEST_DATA);
        $USER_LEVEL     =   $DATA_STATUS['USER_LEVEL'];
        
        $objDataList     =   DB::select("SELECT '$USER_LEVEL' AS USER_LEVEL,T1.*,T2.ACTIONNAME,U.DESCRIPTIONS AS CREATED_BY,T3.NAME AS STATE_NAME 
        FROM TBL_MST_VENDOR T1
		INNER JOIN TBL_MST_VENDOR_BRANCH_MAP M ON T1.VID = M.VID_REF
        INNER JOIN TBL_MST_AUDITTRAIL T2 ON T1.VID=T2.VID AND T1.CYID_REF=T2.CYID_REF AND T1.BRID_REF=T2.BRID_REF AND T2.VTID_REF = '$this->vtid_ref' AND T2.ACTID IN (SELECT max(ACTID) FROM TBL_MST_AUDITTRAIL A WHERE T2.VTID_REF = A.VTID_REF AND T2.VID = A.VID)
        LEFT JOIN TBL_MST_STATE T3 ON T1.REGSTID_REF=T3.STID
        LEFT JOIN TBL_MST_USER U ON T2.USERID=U.USERID
        WHERE T1.CYID_REF='$CYID_REF' AND M.MAPBRID_REF = '$BRID_REF' $WHERE_FIELD  
        ORDER BY T1.VID DESC OFFSET $start ROWS FETCH NEXT  $limit ROWS ONLY");

        if(!empty($objDataList)){          
            foreach($objDataList as $key => $val){

                $app_status         =   isset($DATA_STATUS[$val->STATUS])?$DATA_STATUS[$val->STATUS]:0;
                $DataStatus         =   $val->USER_LEVEL == $val->ACTIONNAME?$DATA_STATUS['APPROVAL5']:$DATA_STATUS[$val->ACTIONNAME];
           
                $VCODE              =   isset($val->VCODE) && $val->VCODE !=''?$val->VCODE:'';
                $NAME               =   isset($val->NAME) && $val->NAME !=''?$val->NAME:'';
                $STATE_NAME         =   isset($val->STATE_NAME) && $val->STATE_NAME !=''?$val->STATE_NAME:'';
                $GSTIN              =   isset($val->GSTIN) && $val->GSTIN !=''?$val->GSTIN:'';
                $INDATE             =   isset($val->INDATE) && $val->INDATE !='' && $val->INDATE !='1900-01-01' ? date('d-m-Y',strtotime($val->INDATE)):'';
                $CREATED_BY         =   isset($val->CREATED_BY) && $val->CREATED_BY !=''?$val->CREATED_BY:'';
                $DEACTIVATED_STATUS =   isset($val->DEACTIVATED) && $val->DEACTIVATED =='1'?'Yes':'No';
                $DEACTIVATED_DATE   =   isset($val->DODEACTIVATED) && $val->DODEACTIVATED !='' && $val->DODEACTIVATED !='1900-01-01' ? date('d-m-Y',strtotime($val->DODEACTIVATED)):'';
        
                echo '<tr class="participantRow">
                    <td><input type="checkbox" name="selectAll[]" id="chkId'.$val->VID.'" value="'.$val->VID.'" class="js-selectall1" data-rcdstatus="'.$app_status.'"></td>
                    <td>'.$VCODE.'</td>
                    <td>'.$NAME.'</td>
                    <td>'.$STATE_NAME.'</td>
                    <td>'.$GSTIN.'</td>
                    <td>'.$INDATE.'</td>
                    <td>'.$CREATED_BY.'</td>
                    <td hidden >'.$DEACTIVATED_STATUS.'</td>
                    <td hidden >'.$DEACTIVATED_DATE.'</td>
                    <td>'.$DataStatus.'</td>
                    </tr>';
            }    
        }
        else{
            echo '';
        }
        exit();
    }

    /*
    public function getListingData(Request $request){
   
        if(!empty($request->input('search'))){

            $CYID_REF       =   Auth::user()->CYID_REF;
            $BRID_REF       =   Session::get('BRID_REF');
            $USERID         =   Auth::user()->USERID;
            $SEARCH         =   $request->input('search');
    
            $sp_where       =   [$CYID_REF, $BRID_REF, $SEARCH]; 
            $objDataList    =   DB::select('EXEC sp_get_vendor_list ?,?,?', $sp_where);

            $row="";

            if(!empty($objDataList)){          
                foreach($objDataList as $key => $val){
           
                    $DataStatus="";
                    if(!Empty($val->STATUS) && $val->STATUS=="A"){ 
                        $app_status = 1 ;
                        $DataStatus = "Approved";
                    } 
                    else if($val->STATUS=="C"){ 
                        $app_status = 2 ;
                        $DataStatus = "Cancel";
                    }
                    else{ 
                        $app_status = 0 ;
                        $DataStatus = "Not Approved";
                    }

                    $VCODE              =   isset($val->VCODE) && $val->VCODE !=''?$val->VCODE:'';
                    $NAME               =   isset($val->NAME) && $val->NAME !=''?$val->NAME:'';
                    $REGADDL1               =   isset($val->REGADDL1) && $val->REGADDL1 !=''?$val->REGADDL1:'';
                    $STATE_NAME         =   isset($val->STATE_NAME) && $val->STATE_NAME !=''?$val->STATE_NAME:'';
                    $GSTIN              =   isset($val->GSTIN) && $val->GSTIN !=''?$val->GSTIN:'';
                    $INDATE             =   isset($val->INDATE) && $val->INDATE !='' && $val->INDATE !='1900-01-01' ? date('d-m-Y',strtotime($val->INDATE)):'';
                    $DEACTIVATED_STATUS =   isset($val->DEACTIVATED) && $val->DEACTIVATED =='1'?'Yes':'No';
                    $DEACTIVATED_DATE   =   isset($val->DODEACTIVATED) && $val->DODEACTIVATED !='' && $val->DODEACTIVATED !='1900-01-01' ? date('d-m-Y',strtotime($val->DODEACTIVATED)):'';
            
                    $row=$row.'<tr>
                        <td><input type="checkbox" name="selectAll[]" id="chkId'.$val->VID.'" value="'.$val->VID.'" class="js-selectall1" data-rcdstatus="'.$app_status.'"></td>
                        <td>'.$VCODE.'</td>
                        <td>'.$NAME.'</td>
                        <td>'.$REGADDL1.'</td>
                        <td>'.$STATE_NAME.'</td>
                        <td>'.$GSTIN.'</td>
                        <td>'.$INDATE.'</td>
                        <td>'.$DEACTIVATED_STATUS.'</td>
                        <td>'.$DEACTIVATED_DATE.'</td>
                        <td>'.$DataStatus.'</td>
                    </tr>';
           
                }

                echo $row;
                exit();
            }
        }       

    }
    */


    public function downloadexcelsamplefile(){
        $excelfile_path         =   "docs/importsamplefiles/vendormaster/vendor_master_import_sample_format.xlsx";     
        $custfilename   =   str_replace('\\', '/', public_path($excelfile_path));
       
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load($custfilename);
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="vendor_master_import_sample_format.xlsx"');
        ob_end_clean();
        $writer->save("php://output");
        return redirect()->back();
    }

    //display import excel form
    public function importdata(){

        $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
                        ->where('VTID','=',$this->vtid_ref)
                        ->select('VTID','VCODE','DESCRIPTIONS','INDATE')
                        ->get()
                        ->toArray();
        return view('masters.purchase.vendormaster.mstfrm48importexcel',compact(['objMstVoucherType']));
    } 

//-------------------------------------------------------------------
    public function importexcelindb(Request $request){

        $formData = $request->all();                
        $allow_extnesions = explode(",",$formData["allow_extensions"]);
        $allow_size =  (int)$formData["allow_max_size"] * 1024 * 1024;  // 2 MB
       //dd($formData);

        $VTID           =   $formData["VTID_REF"]; 
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');       

        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "ADD";
        $IPADDRESS      =   $request->getClientIp();

        //-----------------------------------

        if(isset($formData["FILENAME"])){

            $uploadedFile = $formData["FILENAME"]; 
            if ($uploadedFile->isValid()) {

                $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
                $filesize               =   $uploadedFile ->getSize();  
                $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );
                $inputFileType          =   ucfirst($extension);   // as per API Xls or Xlsx: first charter in upper case

                $filenametostore        =  $VTID.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".Date('YmdHis')."_".$filenamewithextension;  //excel file

                $file_name = pathinfo($filenamewithextension, PATHINFO_FILENAME);  // fetch only file name

                $logfile_name        =  "LOG_".$VTID.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."_".Date('YmdHis')."_".$file_name.".txt";  //log text file
            
                

                //$filenametostore        =  $VTID.$USERID.$CYID_REF.$BRID_REF.$FYID_REF.Date('ymdhis')."logo.".$extension; 
                //$filenametostore        =   uniqid("impexl").$filenamewithextension; 

                //dd($filenametostore);
                //$destinationPath = storage_path()."/docs/importexcel".$CYID_REF."/customermst";    

                $excelfile_path         =   "docs/company".$CYID_REF."/vendormaster/importexcel";     
                $destinationPath    =   str_replace('\\', '/', public_path($excelfile_path));
                if ( !is_dir($destinationPath) ) {
                    mkdir($destinationPath, 0777, true);
                }


                if(in_array($extension,$allow_extnesions)){
                    
                    if($filesize < $allow_size){

                        $custfilename = $destinationPath."/".$filenametostore;
                        if ( !is_dir($destinationPath) ) {
                            mkdir($destinationPath, 0777, true);
                        }                                    
                        //--------
                        $uploadedFile->move($destinationPath, $filenametostore);  //upload file in dir if not exists

                    
                    //echo '</table>';
                    if (file_exists($custfilename)) {

                        //-------------
                        try {
                            /** Load $inputFileName to a Spreadsheet Object  **/
                            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                            $reader->setReadDataOnly(true);
                            $spreadsheet = $reader->load($custfilename);
                            $worksheet = $spreadsheet->getActiveSheet();
                            
                            $excelHeaderdata =  [];
                            $excelAlldata =  [];
                            foreach ($worksheet->getRowIterator() as $rowindex=>$row) {
                            
                                $cellIterator = $row->getCellIterator();
                                
                                $cellIterator->setIterateOnlyExistingCells(true);   
                                /* ***** setIterateOnlyExistingCells(true)
                                This loops through all cells, even if a cell value is not set.
                                For 'TRUE', we loop through cells, only when their value is set.
                                If this method is not called, the default value is 'false'.
                                **** */
                                foreach ($cellIterator as $index=>$cell) {
                                    if($rowindex==1){
                                        $excelHeaderdata[$index] = trim(strtolower($cell->getValue()) );  // fetch value for making header data
                                    }else{
                                        $excelAlldata[$rowindex-1][$excelHeaderdata[$index]]= trim($cell->getValue() );
                                
                                    }
                                }                        
                            } //row iterator
                            

                            if(count($excelAlldata)>0){            
                                $exlwrapped["VENDOR"] = $excelAlldata;    
                                $exl_xml = ArrayToXml::convert($exlwrapped);
                                $XMLEXCEL = $exl_xml;
                            }else{
                                $XMLEXCEL = NULL;
                            }

                        } catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                            //die('Error loading file: '.$e->getMessage());
                            return redirect()->route("master",[48,"importdata"])->with("error","Error loading file: ".$e->getMessage());
                        }

                        //-------------

                    }
                    else
                    {
                        return redirect()->route("master",[48,"importdata"])->with("error","There is some file uploading error. Please try again.");
                    } // file exists
                    

                        
                    }else{
                        
                        return redirect()->route("master",[48,"importdata"])->with("error","Invalid size - Please check.");
                    } //invalid size
                    
                }else{

                    return redirect()->route("master",[48,"importdata"])->with("error","Invalid file extension - Please check.");                      
                }// invalid extension
            
            }else{
                    
                return redirect()->route("master",[48,"importdata"])->with("error","Invalid file - Please check.");  
            }//invalid

        }else{
            return redirect()->route("master",[48,"importdata"])->with("error","File not found. - Please check.");  
        }

        $logfile_path = $excelfile_path."/".$logfile_name;     

        //dd($logfile_path);

        if(!$logfile = fopen($logfile_path, "a") ){

            return redirect()->route("master",[48,"importdata"])->with("error","Log creating file error.");     //create or open log file
        }
        //----------------------CHECK VALID DATA
            $validationErr = false;

            $headerArr = []; 
            foreach($excelAlldata as $eIndex=>$eRowData){
                //dd($eRowData);
                $hkey = trim($eRowData["vendor_code"]);
                if($hkey!="")
                {
                        if (!array_key_exists($hkey, $headerArr)) {        
                            $headerArr[$eRowData["vendor_code"]]["header"]["vendor_code"] = $eRowData["vendor_code"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["vendor_name"] = $eRowData["vendor_name"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["vendor_legal_name"] = $eRowData["vendor_legal_name"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["vendor_group_name"] = $eRowData["vendor_group_name"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["vendor_group_id"]   = "";
                            $headerArr[$eRowData["vendor_code"]]["header"]["old_ref_code"]        = $eRowData["old_ref_code"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["general_ledger_code"]         = $eRowData["general_ledger_code"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["general_ledger_id"]           = "";
                            $headerArr[$eRowData["vendor_code"]]["header"]["registered_address_line1"]    = $eRowData["registered_address_line1"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["registered_address_line2"]    = $eRowData["registered_address_line2"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["country_name"]                = $eRowData["country_name"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["country_id"]                  = "";
                            $headerArr[$eRowData["vendor_code"]]["header"]["state_name"]                  = $eRowData["state_name"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["state_id"]                    = "";
                            $headerArr[$eRowData["vendor_code"]]["header"]["city_name"]                   = $eRowData["city_name"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["city_id"]                     = "";
                            $headerArr[$eRowData["vendor_code"]]["header"]["registered_pincode"]          = $eRowData["registered_pincode"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["cha"]                         = $eRowData["cha"];
                            //contact tab
                            $headerArr[$eRowData["vendor_code"]]["header"]["contact_corporate_address_line1"] = $eRowData["contact_corporate_address_line1"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["contact_corporate_address_line2"] = $eRowData["contact_corporate_address_line2"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["contact_country_name"]        = $eRowData["contact_country_name"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["contact_country_id"]          = "";
                            $headerArr[$eRowData["vendor_code"]]["header"]["contact_state_name"]          = $eRowData["contact_state_name"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["contact_state_id"]            = "";
                            $headerArr[$eRowData["vendor_code"]]["header"]["contact_city_name"]           = $eRowData["contact_city_name"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["contact_city_id"]             = "";
                            $headerArr[$eRowData["vendor_code"]]["header"]["contact_pincode"]             = $eRowData["contact_pincode"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["contact_email_id"]            = $eRowData["contact_email_id"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["contact_website"]             = $eRowData["contact_website"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["contact_phone_no"]            = $eRowData["contact_phone_no"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["contact_mobile_no"]           = $eRowData["contact_mobile_no"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["contact_contact_person"]      = $eRowData["contact_contact_person"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["contact_skype"]               = $eRowData["contact_skype"];
                            //Statutory
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_industry_type_name"]    = $eRowData["statutory_industry_type_name"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_industry_type_id"]      = "";
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_industry_vertical_name"]= $eRowData["statutory_industry_vertical_name"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_industry_vertical_id"]  = "";
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_deals_in"]              = $eRowData["statutory_deals_in"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_gst_type_name"]         = $eRowData["statutory_gst_type_name"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_gst_type_id"]           = "";
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_default_currency_code"] = $eRowData["statutory_default_currency_code"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_default_currency_id"]   = "";
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_gstin"]                 = $eRowData["statutory_gstin"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_credit_limit"]          = $eRowData["statutory_credit_limit"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_cin"]                   = $eRowData["statutory_cin"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_credit_days"]           = $eRowData["statutory_credit_days"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_pan_no"]                = $eRowData["statutory_pan_no"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_execeptional_for_gst"]  = $eRowData["statutory_execeptional_for_gst"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_msme_no"]               = $eRowData["statutory_msme_no"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_factory_no"]            = $eRowData["statutory_factory_no"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_tds_applicable"]        = $eRowData["statutory_tds_applicable"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_certificate_no"]        = $eRowData["statutory_certificate_no"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_expiry_date"]           = $eRowData["statutory_expiry_date"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_assessee_type_code"]    = $eRowData["statutory_assessee_type_code"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_assessee_type_id"]      = "";
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_tds_code"]              = $eRowData["statutory_tds_code"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["statutory_tds_id"]                = "";
                            //ALPS SPECIFIC
                            $headerArr[$eRowData["vendor_code"]]["header"]["alps_specific_sap_vendor_code"]      = $eRowData["alps_specific_sap_vendor_code"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["alps_specific_sap_vendor_name1"]      = $eRowData["alps_specific_sap_vendor_name1"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["alps_specific_sap_vendor_name2"]      = $eRowData["alps_specific_sap_vendor_name2"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["alps_specific_sap_vendor_name3"]      = $eRowData["alps_specific_sap_vendor_name3"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["alps_specific_sap_corporate_group"]   = $eRowData["alps_specific_sap_corporate_group"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["alps_specific_sap_account_group"]     = $eRowData["alps_specific_sap_account_group"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["alps_specific_sap_account_group_name"]     = $eRowData["alps_specific_sap_account_group_name"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["alps_specific_sap_trading_partner"]   = $eRowData["alps_specific_sap_trading_partner"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["alps_specific_sap_trading_partner_name"]   = $eRowData["alps_specific_sap_trading_partner_name"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["alps_specific_sap_invoicing_party"]   = $eRowData["alps_specific_sap_invoicing_party"];
                            $headerArr[$eRowData["vendor_code"]]["header"]["alps_specific_our_code_in_vendor_book"] = $eRowData["alps_specific_our_code_in_vendor_book"];
                            //POINT OF CONTACT                            
                            $headerArr[$eRowData["vendor_code"]]["poc"][$eIndex]["poc_person_name"]      = $eRowData["poc_person_name"];
                            $headerArr[$eRowData["vendor_code"]]["poc"][$eIndex]["poc_designation"]      = $eRowData["poc_designation"];
                            $headerArr[$eRowData["vendor_code"]]["poc"][$eIndex]["poc_mobile"]           = $eRowData["poc_mobile"];
                            $headerArr[$eRowData["vendor_code"]]["poc"][$eIndex]["poc_email"]            = $eRowData["poc_email"];
                            $headerArr[$eRowData["vendor_code"]]["poc"][$eIndex]["poc_ll_no"]            = $eRowData["poc_ll_no"];
                            $headerArr[$eRowData["vendor_code"]]["poc"][$eIndex]["poc_authority_level"]  = $eRowData["poc_authority_level"];
                            $headerArr[$eRowData["vendor_code"]]["poc"][$eIndex]["poc_birthday"]         = $eRowData["poc_birthday"];
                            //BANK                            
                            $headerArr[$eRowData["vendor_code"]]["bank"][$eIndex]["bank_bank_name"]       = $eRowData["bank_bank_name"];
                            $headerArr[$eRowData["vendor_code"]]["bank"][$eIndex]["bank_ifsc"]            = $eRowData["bank_ifsc"];
                            $headerArr[$eRowData["vendor_code"]]["bank"][$eIndex]["bank_branch"]          = $eRowData["bank_branch"];
                            $headerArr[$eRowData["vendor_code"]]["bank"][$eIndex]["bank_account_type"]    = $eRowData["bank_account_type"];
                            $headerArr[$eRowData["vendor_code"]]["bank"][$eIndex]["bank_account_no"]      = $eRowData["bank_account_no"];
                            $headerArr[$eRowData["vendor_code"]]["bank"][$eIndex]["bank_default_bank"]    = $eRowData["bank_default_bank"];
                            //LOCATION
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_location_name"]       = $eRowData["location_location_name"];
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_location_address"]    = $eRowData["location_location_address"];
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_country_name"]        = $eRowData["location_country_name"];
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_country_id"]          = "";
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_state_name"]          = $eRowData["location_state_name"];
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_state_id"]            = "";
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_city_name"]           = $eRowData["location_city_name"];
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_city_id"]             = "";
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_pin_code"]            = $eRowData["location_pin_code"];
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_gstin"]               = $eRowData["location_gstin"];
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_contact_person_name"] = $eRowData["location_contact_person_name"];
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_designation"]         = $eRowData["location_designation"];
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_email_id"]            = $eRowData["location_email_id"];
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_mobile_no"]           = $eRowData["location_mobile_no"];
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_special_instruction"] = $eRowData["location_special_instruction"];
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_bill_to"]             = $eRowData["location_bill_to"];
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_default_billing"]     = $eRowData["location_default_billing"];
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_ship_to"]             = $eRowData["location_ship_to"];
                            $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_default_shipping"]    = $eRowData["location_default_shipping"];
                            //UDF
                            $headerArr[$eRowData["vendor_code"]]["udf"][$eIndex]["udf_udf_label"] = $eRowData["udf_udf_label"];
                            $headerArr[$eRowData["vendor_code"]]["udf"][$eIndex]["udf_value"]     = $eRowData["udf_value"];
                            $headerArr[$eRowData["vendor_code"]]["udf"][$eIndex]["udf_id"]        = "";
                            $headerArr[$eRowData["vendor_code"]]["udf"][$eIndex]["udf_valuetype"]        = "";
                            $headerArr[$eRowData["vendor_code"]]["udf"][$eIndex]["udf_valuetype"]        = "";


                        }else{
                        // echo "The ".$hkey." element is in the array ";
                            
                            $dif_result=array_diff($headerArr[$eRowData["vendor_code"]]["header"], $eRowData);
                            if(!empty($dif_result)){
                                foreach($dif_result as $dfkey=>$dfval){
                                    //for log
                                $this->appendLogData($logfile,"Column Name=".strtoupper($dfkey)." value is different. Data must be same for same vendor (".$hkey.")");
                                $validationErr=true;                                  
                                }
                                break 1;

                            }else{  //if found valid vendor data except grid's data
                            
                                //POINT OF CONTACT                            
                                $headerArr[$eRowData["vendor_code"]]["poc"][$eIndex]["poc_person_name"]      = $eRowData["poc_person_name"];
                                $headerArr[$eRowData["vendor_code"]]["poc"][$eIndex]["poc_designation"]      = $eRowData["poc_designation"];
                                $headerArr[$eRowData["vendor_code"]]["poc"][$eIndex]["poc_mobile"]           = $eRowData["poc_mobile"];
                                $headerArr[$eRowData["vendor_code"]]["poc"][$eIndex]["poc_email"]            = $eRowData["poc_email"];
                                $headerArr[$eRowData["vendor_code"]]["poc"][$eIndex]["poc_ll_no"]            = $eRowData["poc_ll_no"];
                                $headerArr[$eRowData["vendor_code"]]["poc"][$eIndex]["poc_authority_level"]  = $eRowData["poc_authority_level"];
                                $headerArr[$eRowData["vendor_code"]]["poc"][$eIndex]["poc_birthday"]         = $eRowData["poc_birthday"];
                                //BANK                            
                                $headerArr[$eRowData["vendor_code"]]["bank"][$eIndex]["bank_bank_name"]       = $eRowData["bank_bank_name"];
                                $headerArr[$eRowData["vendor_code"]]["bank"][$eIndex]["bank_ifsc"]            = $eRowData["bank_ifsc"];
                                $headerArr[$eRowData["vendor_code"]]["bank"][$eIndex]["bank_branch"]          = $eRowData["bank_branch"];
                                $headerArr[$eRowData["vendor_code"]]["bank"][$eIndex]["bank_account_type"]    = $eRowData["bank_account_type"];
                                $headerArr[$eRowData["vendor_code"]]["bank"][$eIndex]["bank_account_no"]      = $eRowData["bank_account_no"];
                                $headerArr[$eRowData["vendor_code"]]["bank"][$eIndex]["bank_default_bank"]    = $eRowData["bank_default_bank"];
                                //LOCATION
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_location_name"]       = $eRowData["location_location_name"];
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_location_address"]    = $eRowData["location_location_address"];
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_country_name"]        = $eRowData["location_country_name"];
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_country_id"]          = "";
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_state_name"]          = $eRowData["location_state_name"];
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_state_id"]            = "";
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_city_name"]           = $eRowData["location_city_name"];
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_city_id"]             = "";
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_pin_code"]            = $eRowData["location_pin_code"];
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_gstin"]               = $eRowData["location_gstin"];
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_contact_person_name"] = $eRowData["location_contact_person_name"];
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_designation"]         = $eRowData["location_designation"];
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_email_id"]            = $eRowData["location_email_id"];
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_mobile_no"]           = $eRowData["location_mobile_no"];
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_special_instruction"] = $eRowData["location_special_instruction"];
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_bill_to"]             = $eRowData["location_bill_to"];
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_default_billing"]     = $eRowData["location_default_billing"];
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_ship_to"]             = $eRowData["location_ship_to"];
                                $headerArr[$eRowData["vendor_code"]]["location"][$eIndex]["location_default_shipping"]    = $eRowData["location_default_shipping"];
                                //UDF
                                $headerArr[$eRowData["vendor_code"]]["udf"][$eIndex]["udf_udf_label"] = $eRowData["udf_udf_label"];
                                $headerArr[$eRowData["vendor_code"]]["udf"][$eIndex]["udf_value"]     = $eRowData["udf_value"];
                                $headerArr[$eRowData["vendor_code"]]["udf"][$eIndex]["udf_id"]        = "";
                                $headerArr[$eRowData["vendor_code"]]["udf"][$eIndex]["udf_valuetype"]        = "";

                            }
                        // dump($dif_result); // invalid values
                            
                        }
                    }else{
                        echo "<br>Invalid Row or Blank Vendor Code in Row no ".$eIndex++;
                        $this->appendLogData($logfile,"Invalid Row or Blank Vendor Code in Row no",$eIndex++);
                        $validationErr=true;
                        break 1;
                    }        
                
            } // $excelAlldata foreach end
           
            // dump($headerArr);  // after preaper all data

            $dbUDFData =   $this->exlGetVendorUDF(Auth::user()->CYID_REF);  //get all udf data
            //echo "--all db udf--";
            //dump($dbUDFData);
            $udfexllabel =[];
            $udfDbLabel=[];
            foreach ($dbUDFData as $ukey2 => $urow2) {
                $udfDbLabel[] = $urow2->LABEL;
            }
        
            //-----------check validation begin
            foreach($headerArr as $hIndex=>$hRowData){
                foreach($hRowData["header"] as $key1=>$val1){
                    if($key1=="vendor_code"){
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid: Blank Vendor Code");
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidCode($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Vendor Code ".$val1." Space not allowed or Only Alpha Numeric allowed" );
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidLen($val1,20)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Length: Vendor Code ".$val1." can have max 20 character.");
                            $validationErr=true;
                            break 2;                            
                        }
                        //check duplicate code
                        if($this->exlIsDuplicateVCode($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Vendor ".$hIndex.": Duplicate Vendor Code ".$val1);
                            $validationErr=true;
                            break 2; 
                        }
                        
                    } //vendor_code
                    if($key1=="vendor_name"){
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Vendor ".$hIndex.": Blank Vendor Name" );
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidLen($val1,100)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Vendor ".$hIndex.": Vendor Name (".$val1.") can have max 100 character.");
                            $validationErr=true;
                            break 2;                            
                        }
                        
                    } //customer name
                
                    if($key1=="vendor_legal_name"){
                            if($this->exlIsBlank($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Invalid Vendor ".$hIndex.": Blank Vendor Legal Name" );
                                $validationErr=true;
                                break 2;                            
                            }
                            if($this->exlIsValidLen($val1,100)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Invalid Vendor ".$hIndex.": Vendor Name (".$val1.") can have max 100 character.");
                                $validationErr=true;
                                break 2;                            
                            }                        
                    }// vendor_legal_name

                    if($key1=="vendor_group_name")
                    {
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Vendor ".$hIndex.": Blank Vendor Group Name" );
                            $validationErr=true;
                            break 2;                            
                        }
                        // if($this->exlIsValidCode($val1)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Vendor Group Code ".$val1.",  Space not allowed or Only Alpha Numeric allowed. " );
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        // if($this->exlIsValidLen($val1,20)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Vendor Group Code ".$val1." can have max 20 character.");
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        //check code
                        $resgl = $this->exlIsVGROUPCodeExists($val1);
                        if($resgl["result"]==true){
                            $validationErr=false; 
                            $headerArr[$hIndex]["header"]["vendor_group_id"]=$resgl["id"];
                        
                        }else {
                            $validationErr=true;
                            $headerArr[$hIndex]["header"]["vendor_group_id"]="";
                            $this->appendLogData($logfile,"Vendor ".$hIndex.": Vendor Group Code ".$val1." not found.");
                            break 2; 
                        }
                    }// vendor_group_name


                    if($key1=="old_ref_code"){
                            if($this->exlIsValidLen($val1,20)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Invalid Vendor ".$hIndex.": OLD Ref Code: ".$val1." can have max 20 character.");
                                $validationErr=true;
                                break 2;                            
                            }                        
                    }// old_ref_code
                    
                    if($key1=="general_ledger_code")
                    {

                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Vendor ".$hIndex.": Blank General Ledger Code" );
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidCode($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid General Ledger Code ".$val1.",  Space not allowed or Only Alpha Numeric allowed. " );
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidLen($val1,20)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: General Ledger Code ".$val1." can have max 20 character.");
                            $validationErr=true;
                            break 2;                            
                        }
                        //check code
                        $resgl = $this->exlIsGlExists($val1);
                        if($resgl["result"]==true){
                            $validationErr=false; 
                            $headerArr[$hIndex]["header"]["general_ledger_id"]=$resgl["id"];
                        
                        }else {
                            $validationErr=true;
                            $headerArr[$hIndex]["header"]["general_ledger_id"]="";
                            $this->appendLogData($logfile,"Vendor ".$hIndex.": General Ledger Code ".$val1." not found.");
                            break 2; 
                        }
                    }// general_ledger_code

                    if($key1=="registered_address_line1")
                    {

                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Vendor ".$hIndex.": Blank Registered Address Line 1" );
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidLen($val1,200)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Registered Address Line 1: ".$val1." can have max 200 character.");
                            $validationErr=true;
                            break 2;                            
                        }                        
                    }// registered_address_line1

                    if($key1=="registered_address_line2")
                    {
                        if($this->exlIsValidLen($val1,200)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Registered Address Line 2: ".$val1." can have max 200 character.");
                            $validationErr=true;
                            break 2;                            
                        }                        
                    }// registered_address_line2

                    if($key1=="country_name"){
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Vendor ".$hIndex."-Invalid: Blank Country Name");
                            $validationErr=true;
                            break 2;                            
                        }
                        // if($this->exlIsValidCode($val1)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Country Name ".$val1." Space not allowed or Only Alpha Numeric allowed" );
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        // if($this->exlIsValidLen($val1,10)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Length: Country Name ".$val1." can have max 10 character.");
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        //----------------
                        //check code
                        $resgl = $this->exlIsCountryExists($val1);
                        if($resgl["result"]==true){
                            $validationErr=false; 
                            $headerArr[$hIndex]["header"]["country_id"]=$resgl["id"];
                        
                        }else {
                            $validationErr=true;
                            $headerArr[$hIndex]["header"]["country_id"]="";
                            $this->appendLogData($logfile,"Vendor ".$hIndex.": Country Name ".$val1." not found.");
                            break 2; 
                        }
                    } //country_name

                    if($key1=="state_name"){
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Vendor ".$hIndex."-Invalid: Blank State Name");
                            $validationErr=true;
                            break 2;                            
                        }
                        // if($this->exlIsValidCode($val1)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: State Name ".$val1." Space not allowed or Only Alpha Numeric allowed" );
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        // if($this->exlIsValidLen($val1,10)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Length: State Name ".$val1." can have max 10 character.");
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        //----------------
                        //check code
                        $ctryid = $headerArr[$hIndex]["header"]["country_id"];
                        $resdata = $this->exlIsStateExists($ctryid,$val1);
                        if($resdata["result"]==true){
                            $validationErr=false; 
                            $headerArr[$hIndex]["header"]["state_id"]=$resdata["id"];
                        
                        }else {
                            $validationErr=true;
                            $headerArr[$hIndex]["header"]["state_id"]="";
                            $this->appendLogData($logfile,"Vendor ".$hIndex.": State Name ".$val1." not found.");
                            break 2; 
                        }
                    
                    } //state_name

                    
                    if($key1=="city_name"){
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Vendor ".$hIndex."-Invalid: Blank City Name");
                            $validationErr=true;
                            break 2;                            
                        }
                        // if($this->exlIsValidCode($val1)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: City Name ".$val1." Space not allowed or Only Alpha Numeric allowed" );
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        // if($this->exlIsValidLen($val1,10)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Length: City Name ".$val1." can have max 10 character.");
                        //     $validationErr=true;
                        //     break 2;                            
                        // }

                        $ctry_id   = $headerArr[$hIndex]["header"]["country_id"];
                        $state_id  = $headerArr[$hIndex]["header"]["state_id"];
                        $resdata = $this->exlIsCityExists($ctry_id,$state_id,$val1);
                        if($resdata["result"]==true){
                            $validationErr=false; 
                            $headerArr[$hIndex]["header"]["city_id"]=$resdata["id"];
                        
                        }else {
                            $validationErr=true;
                            $headerArr[$hIndex]["header"]["city_id"]="";
                            $this->appendLogData($logfile,"Vendor ".$hIndex.": City Name ".$val1." not found.");
                            break 2; 
                        }

                    } //city_name

                    //
                    if($key1=="registered_pincode")
                    {
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Vendor ".$hIndex.": Blank Registered Pincode" );
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidLen($val1,10)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Registered Pincode: ".$val1." can have max 10 character.");
                            $validationErr=true;
                            break 2;                            
                        }            
                        if($this->exlIsValidCode($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Registered Pincode ".$val1." Space not allowed or Only Alpha Numeric allowed" );
                            $validationErr=true;
                            break 2;                            
                        }            
                    }// registered_pincode
                    
                    if($key1=="contact_corporate_address_line1")
                    {
                        
                        if($this->exlIsValidLen($val1,200)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Contact Corporate Address Line1: ".$val1." can have max 200 character.");
                            $validationErr=true;
                            break 2;                            
                        }            
                            
                    }// contact_corporate_address_line1

                    if($key1=="contact_corporate_address_line2")
                    {                        
                        if($this->exlIsValidLen($val1,200)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Contact Corporate Address Line2: ".$val1." can have max 200 character.");
                            $validationErr=true;
                            break 2;                            
                        }                                        
                    }// contact_corporate_address_line2

                    //
                    if($key1=="contact_country_name"){
                        if(trim($val1)!=""){
                            // if($this->exlIsValidCode($val1)==true){
                            //     $validationErr=false; 
                            // }else {
                            //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Contact Country Name ".$val1." Space not allowed or Only Alpha Numeric allowed" );
                            //     $validationErr=true;
                            //     break 2;                            
                            // }
                            // if($this->exlIsValidLen($val1,10)==true){
                            //     $validationErr=false; 
                            // }else {
                            //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Length: Contact Country Name ".$val1." can have max 10 character.");
                            //     $validationErr=true;
                            //     break 2;                            
                            // }

                            //check code
                            $resgl = $this->exlIsCountryExists($val1);
                            if($resgl["result"]==true){
                                $validationErr=false; 
                                $headerArr[$hIndex]["header"]["contact_country_id"]=$resgl["id"];
                            }else {
                                $validationErr=true;
                                $headerArr[$hIndex]["header"]["contact_country_id"]="";
                                $this->appendLogData($logfile,"Vendor ".$hIndex.": Contact Country Name ".$val1." not found.");
                                break 2; 
                            }
                        } // not blank                       
                    } //contact_country_name

                    
                    if($key1=="contact_state_name"){
                        //check code  
                        $cctryid = $headerArr[$hIndex]["header"]["contact_country_id"];
                        if(trim($cctryid)!=""){

                            if($this->exlIsBlank($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Invalid Vendor ".$hIndex.": Blank Contact State Name" );
                                $validationErr=true;
                                break 2;                            
                            }
                            
                            // if($this->exlIsValidCode($val1)==true){
                            //     $validationErr=false; 
                            // }else {
                            //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Contact State Name ".$val1." Space not allowed or Only Alpha Numeric allowed" );
                            //     $validationErr=true;
                            //     break 2;                            
                            // }
                            // if($this->exlIsValidLen($val1,10)==true){
                            //     $validationErr=false; 
                            // }else {
                            //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Length: Contact State Name ".$val1." can have max 10 character.");
                            //     $validationErr=true;
                            //     break 2;                            
                            // }

                            //check code
                            $resdata = $this->exlIsStateExists($ctryid,$val1);
                            if($resdata["result"]==true){
                                $validationErr=false; 
                                $headerArr[$hIndex]["header"]["contact_state_id"]=$resdata["id"];
                                
                            }else {
                                $validationErr=true;
                                $headerArr[$hIndex]["header"]["contact_state_id"]="";
                                $this->appendLogData($logfile,"Vendor ".$hIndex.": Contact State Name ".$val1." not found.");
                                break 2; 
                            } 
                        } // not blank                       
                    } //contact_state_name

                    if($key1=="contact_city_name"){
                        //check code  
                        if(trim($cctryid)!=""){

                            if($this->exlIsBlank($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Invalid Vendor ".$hIndex.": Blank Contact City Name" );
                                $validationErr=true;
                                break 2;                            
                            }
                            
                            // if($this->exlIsValidCode($val1)==true){
                            //     $validationErr=false; 
                            // }else {
                            //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Contact City Name ".$val1." Space not allowed or Only Alpha Numeric allowed" );
                            //     $validationErr=true;
                            //     break 2;                            
                            // }
                            // if($this->exlIsValidLen($val1,10)==true){
                            //     $validationErr=false; 
                            // }else {
                            //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Length: Contact City Name ".$val1." can have max 10 character.");
                            //     $validationErr=true;
                            //     break 2;                            
                            // }

                            //check code
                            $cctryid = $headerArr[$hIndex]["header"]["contact_country_id"];
                            $cstateid = $headerArr[$hIndex]["header"]["contact_state_id"];
                            $resdata = $this->exlIsCityExists($cctryid,$cstateid,$val1);
                            if($resdata["result"]==true){
                                $validationErr=false; 
                                $headerArr[$hIndex]["header"]["contact_city_id"]=$resdata["id"];
                                
                            }else {
                                $validationErr=true;
                                $headerArr[$hIndex]["header"]["contact_city_id"]="";
                                $this->appendLogData($logfile,"Vendor ".$hIndex.": Contact City Name ".$val1." not found.");
                                break 2; 
                            } 
                        } // not blank                       
                    } //contact_city_name

                    if($key1=="contact_pincode")
                    {
                        if(trim($val1)!=""){
                            if($this->exlIsValidLen($val1,10)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Contact Pincode: ".$val1." can have max 10 character.");
                                $validationErr=true;
                                break 2;                            
                            }            
                            if($this->exlIsValidCode($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Contact Pincode ".$val1." Space not allowed or Only Alpha Numeric allowed" );
                                $validationErr=true;
                                break 2;                            
                            }   
                        }         
                    }// contact_pincode

                    if($key1=="contact_email_id")
                    {
                        if(trim($val1)!=""){

                            if($this->exlIsValidLen($val1,50)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Contact Email: ".$val1." can have max 50 character.");
                                $validationErr=true;
                                break 2;                            
                            }   
                            
                            if($this->exlIsValidEmail($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Contact Email ".$val1 );
                                $validationErr=true;
                                break 2;                            
                            }   
                        }         
                    }// contact_email_id

                    if($key1=="contact_website")
                    {
                        if(trim($val1)!=""){

                            if($this->exlIsValidLen($val1,50)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Contact Website: ".$val1." can have max 50 character.");
                                $validationErr=true;
                                break 2;                            
                            }                               
                            if($this->exlIsValidURL($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Contact Website ".$val1 );
                                $validationErr=true;
                                break 2;                            
                            }   
                        }         
                    }// contact_website

                    if($key1=="contact_phone_no")
                    {
                        if(trim($val1)!=""){
                            if($this->exlIsValidLen($val1,20)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Contact Phone No: ".$val1." can have max 20 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }         
                    }// contact_phone_no

                    if($key1=="contact_mobile_no")
                    {
                        if(trim($val1)!=""){
                            if($this->exlIsValidLen($val1,20)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Contact Mobile No: ".$val1." can have max 20 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }         
                    }// contact_mobile_no

                    if($key1=="contact_contact_person")
                    {
                        if(trim($val1)!=""){
                            if($this->exlIsValidLen($val1,50)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Contact Person Name: ".$val1." can have max 50 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }         
                    }// contact_contact_person

                    if($key1=="contact_skype")
                    {
                        if(trim($val1)!=""){
                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Contact Skype Id: ".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }         
                    }// contact_skype 


                    if($key1=="statutory_industry_type_name")
                    {
                        if(trim($val1)!=""){
                            // if($this->exlIsValidCode($val1)==true){
                            //     $validationErr=false; 
                            // }else {
                            //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Statutory Industry Typ Code ".$val1.",  Space not allowed or Only Alpha Numeric allowed. " );
                            //     $validationErr=true;
                            //     break 2;                            
                            // }
                            // if($this->exlIsValidLen($val1,20)==true){
                            //     $validationErr=false; 
                            // }else {
                            //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory Industry Typ Code ".$val1." can have max 20 character.");
                            //     $validationErr=true;
                            //     break 2;                            
                            // }
                            //check code
                            $resgl = $this->exlIsIndTyp($val1);
                            if($resgl["result"]==true){
                                $validationErr=false; 
                                $headerArr[$hIndex]["header"]["statutory_industry_type_id"]=$resgl["id"];
                            
                            }else {
                                $validationErr=true;
                                $headerArr[$hIndex]["header"]["statutory_industry_type_id"]="";
                                $this->appendLogData($logfile,"Vendor ".$hIndex.": Statutory Industry Typ Code ".$val1." not found.");
                                break 2; 
                            }
                        }
                    }// statutory_industry_type_name

                    if($key1=="statutory_industry_vertical_name")
                    {
                        if(trim($val1)!=""){
                            // if($this->exlIsValidCode($val1)==true){
                            //     $validationErr=false; 
                            // }else {
                            //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Statutory Industry Vertical Code ".$val1.",  Space not allowed or Only Alpha Numeric allowed. " );
                            //     $validationErr=true;
                            //     break 2;                            
                            // }
                            // if($this->exlIsValidLen($val1,20)==true){
                            //     $validationErr=false; 
                            // }else {
                            //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory Industry Vertical Code ".$val1." can have max 20 character.");
                            //     $validationErr=true;
                            //     break 2;                            
                            // }
                            //check code
                            $resgl = $this->exlIsIndVertical($val1);
                            if($resgl["result"]==true){
                                $validationErr=false; 
                                $headerArr[$hIndex]["header"]["statutory_industry_vertical_id"]=$resgl["id"];
                            
                            }else {
                                $validationErr=true;
                                $headerArr[$hIndex]["header"]["statutory_industry_vertical_id"]="";
                                $this->appendLogData($logfile,"Vendor ".$hIndex.": Statutory Industry Vertical Code ".$val1." not found.");
                                break 2; 
                            }
                        }
                    }// statutory_industry_vertical_name
                    
                    if($key1=="statutory_deals_in")
                    {
                        if(trim($val1)!=""){
                            if($this->exlIsValidLen($val1,50)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory Deals In: ".$val1." can have max 50 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }         
                    }// statutory_deals_in 

                    
                    if($key1=="statutory_gst_type_name")
                    {

                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Vendor ".$hIndex.": Blank Statutory GST Type Name" );
                            $validationErr=true;
                            break 2;                            
                        }
                        // if($this->exlIsValidCode($val1)==true){
                        //     $validationErr=false; 
                        // }else {	
                        //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Statutory GST Type Name ".$val1.",  Space not allowed or Only Alpha Numeric allowed. " );
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        // if($this->exlIsValidLen($val1,20)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory GST Type Name ".$val1." can have max 20 character.");
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        //check code
                        $resgl = $this->exlIsGSTTypeExists($val1);
                        if($resgl["result"]==true){
                            $validationErr=false; 
                            $headerArr[$hIndex]["header"]["statutory_gst_type_id"]=$resgl["id"];
                        
                        }else {
                            $validationErr=true;
                            $headerArr[$hIndex]["header"]["statutory_gst_type_id"]="";
                            $this->appendLogData($logfile,"Vendor ".$hIndex.": Statutory GST Type Name ".$val1." not found.");
                            break 2; 
                        }
                    }// statutory_gst_type_name
                    

                    if($key1=="statutory_default_currency_code")
                    {

                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Vendor ".$hIndex.": Blank Statutory Default Currency Code" );
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidCode($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Statutory Default Currency Code ".$val1.",  Space not allowed or Only Alpha Numeric allowed. " );
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidLen($val1,10)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory Default Currency Code ".$val1." can have max 10 character.");
                            $validationErr=true;
                            break 2;                            
                        }
                        //check code
                        $resgl = $this->exlIsDefCurrencyExists($val1);
                        if($resgl["result"]==true){
                            $validationErr=false; 
                            $headerArr[$hIndex]["header"]["statutory_default_currency_id"]=$resgl["id"];
                        
                        }else {
                            $validationErr=true;
                            $headerArr[$hIndex]["header"]["statutory_default_currency_id"]="";
                            $this->appendLogData($logfile,"Vendor ".$hIndex.": Statutory Default Currency Code ".$val1." not found.");
                            break 2; 
                        }
                    }// statutory_default_currency_code
                    if($key1=="statutory_gstin")
                    {
                        if(trim($val1)!=""){

                            if($this->exlIsValidLen($val1,15)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: GSTIN number: ".$val1." can have max 15 character.");
                                $validationErr=true;
                                break 2;                            
                            }   
                            
                            if($this->exlIsValidGSTIN($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: GSTIN number ".$val1 );
                                $validationErr=true;
                                break 2;                            
                            }   
                        }         
                    }// statutory_gstin

                    if($key1=="statutory_credit_limit")
                    {
                        if(trim($val1)!=""){
                            if($this->exlIsNum($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Statutory Credit Limit ".$val1.",  Only Number allowed. " );
                                $validationErr=true;
                                break 2;                            
                            }
                            if($this->exlIsValidLen($val1,18)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory Credit Limit ".$val1." can have max 18 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// statutory_credit_limit

                    if($key1=="statutory_credit_days")
                    {
                        if(trim($val1)!=""){
                            if($this->exlIsOnlyDigit($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Statutory Credit Days ".$val1.",  Only Digit allowed. " );
                                $validationErr=true;
                                break 2;                            
                            }
                            if($this->exlIsValidLen($val1,10)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory Credit Days ".$val1." can have max 10 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// statutory_credit_days

                    if($key1=="statutory_pan_no")
                    {
                        if(trim($val1)!=""){

                            if($this->exlIsValidLen($val1,10)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory PAN number: ".$val1." can have max 10 character.");
                                $validationErr=true;
                                break 2;                            
                            }   
                            
                            if($this->exlIsValidPAN($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory PAN number ".$val1 );
                                $validationErr=true;
                                break 2;                            
                            }   
                        }         
                    }// 

                    if($key1=="statutory_execeptional_for_gst")
                    {
                        if(trim($val1)!=""){

                            if($this->exlIsValidLen($val1,4)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory Exceptional for GST: ".$val1." can have max 4 character.");
                                $validationErr=true;
                                break 2;                            
                            } 
                            if(trim(strtolower($val1))=="1" || trim(strtolower($val1))=="0"){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory Exceptional for GST: ".$val1." can be 1 or 0.");
                                $validationErr=true;
                                break 2;                            
                            }    
                        }        
                    }// statutory_execeptional_for_gst
                    
                    if($key1=="statutory_msme_no")
                    {
                        if(trim($val1)!=""){

                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory MSME No: ".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            }   
                        }         
                    }// statutory_msme_no
                    if($key1=="statutory_factory_no")
                    {
                        if(trim($val1)!=""){

                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory Factory No: ".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            }   
                        }         
                    }// statutory_factory_no

                    if($key1=="statutory_tds_applicable")
                    {
                        if(trim($val1)!=""){

                            if($this->exlIsValidLen($val1,4)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory TDS Applicable: ".$val1." can have max 4 character.");
                                $validationErr=true;
                                break 2;                            
                            } 
                            if(trim(strtolower($val1))==true || trim(strtolower($val1))==false){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory TDS Applicable: ".$val1." can be true or false.");
                                $validationErr=true;
                                break 2;                            
                            }    
                        }        
                    }// statutory_tds_applicable

                    if($key1=="statutory_certificate_no")
                    {
                        if(trim($val1)!=""){

                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory Certificate Number: ".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            } 
                        
                        }        
                    }// statutory_certificate_no              
                
                    if($key1=="statutory_expiry_date")
                    {
                        if(trim($val1)!=""){
                            if($this->exlIsValidDate($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory Expiry Date: ".$val1);
                                $validationErr=true;
                                break 2;                            
                            }
                        }        
                    }// statutory_expiry_date

                    if($key1=="statutory_assessee_type_code")
                    {

                        if(trim($val1)!=""){
                            
                            if($this->exlIsValidCode($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Statutory Assessee Type Code ".$val1.",  Space not allowed or Only Alpha Numeric allowed. " );
                                $validationErr=true;
                                break 2;                            
                            }
                            if($this->exlIsValidLen($val1,20)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory Assessee Type Code ".$val1." can have max 20 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                            //check code
                            
                            $resgl = $this->exlIsAsseTypeExists($val1);
                            if($resgl["result"]==true){
                                $validationErr=false; 
                                $headerArr[$hIndex]["header"]["statutory_assessee_type_id"]=$resgl["id"];
                            
                            }else {
                                $validationErr=true;
                                $headerArr[$hIndex]["header"]["statutory_assessee_type_id"]="";
                                $this->appendLogData($logfile,"Vendor ".$hIndex.": Statutory Assessee Type Code ".$val1." not found.");
                                break 2; 
                            }
                        }
                    }// statutory_assessee_type_code

                    if($key1=="statutory_tds_code")
                    {

                        if(trim($val1)!=""){

                            
                            $tdsArr = explode(",",trim($val1));
                            
                            foreach ($tdsArr as $takey => $taval) 
                            {
                                
                                if($this->exlIsValidCode($taval)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Statutory TDS Code ".$taval.",  Space not allowed or Only Alpha Numeric allowed. " );
                                    $validationErr=true;
                                    break 2;                            
                                }
                                if($this->exlIsValidLen($taval,20)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Statutory TDS Code ".$taval." can have max 20 character.");
                                    $validationErr=true;
                                    break 2;                            
                                }
                                
                                //check code
                                $asseid = $headerArr[$hIndex]["header"]["statutory_assessee_type_id"];
                                $resdata = $this->exlIsStTDSCode($asseid,$taval);
                                if($resdata["result"]==true){
                                    $validationErr=false; 
                                    $headerArr[$hIndex]["header"]["statutory_tds_id"]= $headerArr[$hIndex]["header"]["statutory_tds_id"]. $resdata["id"].",";
                                    
                                }else {
                                    $validationErr=true;
                                    $headerArr[$hIndex]["header"]["statutory_tds_id"]="";
                                    $this->appendLogData($logfile,"Vendor ".$hIndex.": Statutory TDS Code ".$taval." not found.");
                                    break 2; 
                                } 

                            } //tds array
                            //remove last comma
                            $headerArr[$hIndex]["header"]["statutory_tds_id"] = substr(trim($headerArr[$hIndex]["header"]["statutory_tds_id"]),0,-1);

                        }
                    }// statutory_tds_code 

                    if($key1=="alps_specific_sap_vendor_code")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: ALPS Specific - SAP Vendor Code ".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_sap_vendor_code   

                    if($key1=="alps_specific_sap_vendor_name1")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,100)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: ALPS Specific - SAP Vendor Name1 ".$val1." can have max 100 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_sap_vendor_name1     

                    if($key1=="alps_specific_sap_vendor_name2")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,100)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: ALPS Specific - SAP Vendor Name2 ".$val1." can have max 100 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_sap_vendor_name2     

                    if($key1=="alps_specific_sap_vendor_name3")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,100)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: ALPS Specific - SAP Vendor Name3 ".$val1." can have max 100 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_sap_vendor_name3   

                    if($key1=="alps_specific_sap_corporate_group")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: ALPS Specific - SAP Corporate Group ".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_sap_corporate_group    

                    if($key1=="alps_specific_sap_account_group")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: ALPS Specific-SAP SAP Account Group ".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_sap_account_group  

                    if($key1=="alps_specific_sap_account_group_name")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: ALPS Specific-SAP Account Group Name ".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_sap_account_group_name      

                    if($key1=="alps_specific_sap_account_group_name")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: ALPS Specific- SAP Account Group Name ".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_sap_account_group_name         

                    if($key1=="alps_specific_sap_trading_partner")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: ALPS Specific- SAP Trading Partner ".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_sap_trading_partner    

                    if($key1=="alps_specific_sap_trading_partner_name")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,100)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: ALPS Specific-SAP Trading Partner Name ".$val1." can have max 100 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_sap_trading_partner_name                     
                   
                    if($key1=="alps_specific_sap_invoicing_party")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: ALPS Specific-SAP Invoicing Party".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_sap_invoicing_party 

                    if($key1=="alps_specific_our_code_in_vendor_book")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: ALPS Specific-Our Code In Vendor Book ".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_our_code_in_vendor_book    
                    
                    

                    

                //------------------  
                }//--- header foreach endloop
                //-------------POC BEGIN
                foreach($hRowData["poc"] as $pockey=>$pocrow){
                    //dump($pocrow);
                    foreach ($pocrow as $pkey1 => $pvalue1) {
                        //validation
                        if($pkey1=="poc_person_name")
                        {
                            if(trim($pvalue1)!=""){                            
                                if($this->exlIsValidLen($pvalue1,100)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Point of Contact-Person Name ".$pvalue1." can have max 100 character.");
                                    $validationErr=true;
                                    break 3;                            
                                }
                            }
                        }// poc_person_name  
                        if($pkey1=="poc_designation")
                        {
                            if(trim($pvalue1)!=""){                            
                                if($this->exlIsValidLen($pvalue1,50)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Point of Contact-Designation ".$pvalue1." can have max 50 character.");
                                    $validationErr=true;
                                    break 3;                            
                                }
                            }
                        }// poc_designation  
                        if($pkey1=="poc_mobile")
                        {
                            if(trim($pvalue1)!=""){                            
                                if($this->exlIsValidLen($pvalue1,15)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Point of Contact-Mobile No ".$pvalue1." can have max 15 character.");
                                    $validationErr=true;
                                    break 3;                            
                                }
                            }
                        }// poc_mobile  
                        if($pkey1=="poc_email")
                        {
                            if(trim($pvalue1)!=""){                            
                                if($this->exlIsValidLen($pvalue1,50)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Point of Contact-Email ".$pvalue1." can have max 50 character.");
                                    $validationErr=true;
                                    break 3;                            
                                }
                                if($this->exlIsValidEmail($pvalue1)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Point of Contact-Email ".$pvalue1 );
                                    $validationErr=true;
                                    break 3;                            
                                }   
                            }
                        }// poc_email  

                        if($pkey1=="poc_ll_no")
                        {
                            if(trim($pvalue1)!=""){                            
                                if($this->exlIsValidLen($pvalue1,20)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Point of Contact-LL No ".$pvalue1." can have max 20 character.");
                                    $validationErr=true;
                                    break 3;                            
                                }
                            }
                        }// poc_ll_no  
                        if($pkey1=="poc_authority_level")
                        {
                            if(trim($pvalue1)!=""){                            
                                if($this->exlIsValidLen($pvalue1,30)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Point of Contact-Authority Level ".$pvalue1." can have max 30 character.");
                                    $validationErr=true;
                                    break 3;                            
                                }
                            }
                        }// poc_authority_level  
                        if($pkey1=="poc_birthday")
                        {
                            if(trim($pvalue1)!=""){                            
                                if($this->exlIsValidDate($pvalue1)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."-Invalid: Point of Contact- Birthday : ".$pvalue1);
                                    $validationErr=true;
                                    break 3;                            
                                }
                            }
                        }// poc_birthday  


                    }//poc row end-loop
                }//poc grid loop
                //-------------POC END
                //-------------BANK BEGIN
                $defbankfound = false;
                $bankRowFound = false;
                $defbankcount = 0; 
                foreach($hRowData["bank"] as $bankkey=>$bankrow){
                
                    if(trim($bankrow["bank_bank_name"])!=""){
                        if(trim($bankrow["bank_ifsc"])!="" && trim($bankrow["bank_branch"])!="" && trim($bankrow["bank_account_type"])!=""  && trim($bankrow["bank_account_no"])!=""  ){
                            $validationErr=false; 

                        }else {
                            $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Details: Bank Name(".$bankrow["bank_bank_name"]."). Please insert the value for Branch/IFSC/Account Type/Account No");
                            $validationErr=true;
                            break 2;                            
                        }
                        //default bank at least one mandatory
                        $bankRowFound= true;
                        if(trim($bankrow["bank_default_bank"])!="")
                        {
                            $defbankfound = true;
                            $defbankcount++;
                        } 
                    }
                
                    foreach ($bankrow as $bkey1 => $bvalue1) {
                        if($bkey1=="bank_bank_name")
                        {
                            if(trim($bvalue1)!=""){                            
                                if($this->exlIsValidLen($bvalue1,100)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Bank- Bank Name ".$bvalue1." can have max 100 character.");
                                    $validationErr=true;
                                    break 3;                            
                                }
                            }
                        }// bank_bank_name  
                    
                        if($bkey1=="bank_ifsc")
                        {
                            if(trim($bvalue1)!=""){                            
                                if($this->exlIsValidLen($bvalue1,20)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Bank- IFSC ".$bvalue1." can have max 20 character.");
                                    $validationErr=true;
                                    break 3;                            
                                }
                            }
                        }// bank_ifsc  
                        if($bkey1=="bank_branch")
                        {
                            if(trim($bvalue1)!=""){                            
                                if($this->exlIsValidLen($bvalue1,100)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Bank- Branch ".$bvalue1." can have max 100 character.");
                                    $validationErr=true;
                                    break 3;                            
                                }
                            }
                        }// bank_branch  
                        
                        if($bkey1=="bank_account_type")
                        {
                            if(trim($bvalue1)!=""){   
                                if(strtoupper(trim($bvalue1))=="SAVING ACCOUNT" || strtoupper(trim($bvalue1))=="CURRENT ACCOUNT"
                                    || strtoupper(trim($bvalue1))=="OD"  || strtoupper(trim($bvalue1))=="OTHERS"  ){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Bank- Account Type value(".$bvalue1."). Only Allowed  SAVING ACCOUNT or CURRENT ACCOUNT or OD or OTHERS");
                                    $validationErr=true;
                                    break 3;                            
                                }
                            }
                        }// bank_account_type  
                        if($bkey1=="bank_account_no")
                        {
                            if(trim($bvalue1)!=""){  
                                if($this->exlIsOnlyDigit($bvalue1)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Bank- Account No ".$bvalue1.",  Only Digit allowed. " );
                                    $validationErr=true;
                                    break 3;                            
                                } 
                            }
                        }// bank_account_no  
                        if($bkey1=="bank_default_bank")
                        {
                            if(trim($bvalue1)!=""){                            
                                if($bvalue1=="1"){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Bank Details: Invaid Default Bank value=".$bvalue1.". Blank or 1 Allowed.");
                                    $validationErr=true;
                                    break 3;                            
                                }
                            }
                        }// bank_default_bank 
                    }//bank row end-loop
                }//bank grid loop
                //Default Billing 
                if($bankRowFound==true){                    
                    if($defbankfound==true){
                        $validationErr=false; 
                    }else {
                        $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Bank Details: Please Select Default Bank.");
                        $validationErr=true;
                        break 1;                            
                    }
                    if( $defbankcount=="1"){
                        $validationErr=false; 
                    }else {
                        $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Bank Details: Please Select Default Bank Single Time.");
                        $validationErr=true;
                        break 1;                            
                    }
                }
                //-------------BANK END
                //-------------LOCATION BEGIN
                $anyloc = false;
                $defbillfound = false;
                $defbillcount = 0; 
                $defshipfound = false;
                $defshipcount = 0; 
                
                foreach($hRowData["location"] as $locindex=>$locrow){
                
                    //one location row is mandatory
                    if(trim($locrow["location_location_name"])!="")
                    {
                        $anyloc = true;                      
                    }

                    //check country, state and city Name 
                    if(trim($locrow["location_location_name"])!=""){                    
                        
                        if(trim($locrow["location_country_name"])!="" && trim($locrow["location_state_name"])!="" && trim($locrow["location_city_name"])!=""  ){
                            $validationErr=false; 

                        }else {
                            $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Location Details: Country Name-".$locrow["location_country_name"].". Please insert the value for State Name or City Name.");
                            $validationErr=true;
                            break 2;                            
                        }
                        
                        //check Bill to / ship to                     
                        if(trim($locrow["location_bill_to"])!="" || trim($locrow["location_ship_to"])!=""  ){
                            $validationErr=false; 

                        }else {
                            $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Location Details: Please select Bill To or Ship To for every row.");
                            $validationErr=true;
                            break 2;                            
                        }
                        //default billing at least one mandatory
                        if(trim($locrow["location_default_billing"])!="")
                        {
                            $defbillfound = true;
                            $defbillcount++;
                        }                    
                        //default shipping at least one mandatory
                        if(trim($locrow["location_default_shipping"])!="")
                        {
                            $defshipfound = true;
                            $defshipcount++;
                        }
                    }
                    //--------------------
                    
                                    
                    foreach ($locrow as $lockey => $locval) {

                        if(trim($headerArr[$hIndex]["location"][$locindex]["location_location_name"])!="")
                        {
                        ///---------------------------------
                            if($lockey=="location_location_name")
                            {
                                if(trim($locval)!=""){                            
                                    if($this->exlIsValidLen($locval,50)==true){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Location- Location Name".$locval." can have max 50 character.");
                                        $validationErr=true;
                                        break 3;                            
                                    }
                                }
                            }// location_location_name  
                            if($lockey=="location_location_address")
                            {
                                if(trim($locval)!=""){                            
                                    if($this->exlIsValidLen($locval,200)==true){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Location- Location Address".$locval." can have max 200 character.");
                                        $validationErr=true;
                                        break 3;                            
                                    }
                                }
                            }// location_location_address  

                            if($lockey=="location_country_name")
                            {
                                if(trim($locval)!=""){                            
                                    
                                    // if($this->exlIsValidCode($locval)==true){
                                    //     $validationErr=false; 
                                    // }else {
                                    //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Location- Location Country Name ".$locval." Space not allowed or Only Alpha Numeric allowed" );
                                    //     $validationErr=true;
                                    //     break 3;                            
                                    // }
                                    // if($this->exlIsValidLen($locval,10)==true){
                                    //     $validationErr=false; 
                                    // }else {
                                    //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Location- Country Name ".$locval." can have max 10 character.");
                                    //     $validationErr=true;
                                    //     break 3;                            
                                    // }
                                    //check code
                                    $resgl = $this->exlIsCountryExists($locval);
                                    if($resgl["result"]==true){
                                        $validationErr=false; 
                                        $headerArr[$hIndex]["location"][$locindex]["location_country_id"]=$resgl["id"];
                                    }else {
                                        $validationErr=true;
                                        $headerArr[$hIndex]["location"][$locindex]["location_country_id"]="";
                                        $this->appendLogData($logfile,"Vendor ".$hIndex.": Invalid: Location - Country Name ".$locval." not found.");
                                        break 3; 
                                    }
                                }                            
                            }// location_country_name  
                            $lctryid = $headerArr[$hIndex]["location"][$locindex]["location_country_id"];
                            if($lockey=="location_state_name"){
                                
                                if(trim($lctryid)!=""){

                                    if($this->exlIsBlank($locval)==true){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Invalid Vendor ".$hIndex.": Blank Location- State Name" );
                                        $validationErr=true;
                                        break 3;                            
                                    }
                                    
                                    // if($this->exlIsValidCode($locval)==true){
                                    //     $validationErr=false; 
                                    // }else {
                                    //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Location- State Name ".$locval." Space not allowed or Only Alpha Numeric allowed" );
                                    //     $validationErr=true;
                                    //     break 3;                            
                                    // }
                                    // if($this->exlIsValidLen($locval,10)==true){
                                    //     $validationErr=false; 
                                    // }else {
                                    //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Length: Location- State Name ".$locval." can have max 10 character.");
                                    //     $validationErr=true;
                                    //     break 3;                            
                                    // }

                                    //check code
                                    $resdata = $this->exlIsStateExists($lctryid,$locval);
                                    if($resdata["result"]==true){
                                        $validationErr=false;                                     
                                        $headerArr[$hIndex]["location"][$locindex]["location_state_id"]=$resdata["id"];
                                        
                                    }else {
                                        $validationErr=true;
                                        $headerArr[$hIndex]["location"][$locindex]["location_state_id"]=$resdata["id"];
                                        $this->appendLogData($logfile,"Vendor ".$hIndex.": Location- State Name ".$locval." not found.");
                                        break 3; 
                                    } 
                                } // not blank                       
                            } //location_state_name

                            if($lockey=="location_city_name"){
                                //check code  
                                if(trim($lctryid)!=""){

                                    if($this->exlIsBlank($locval)==true){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Invalid Vendor ".$hIndex.": Blank Location City Name" );
                                        $validationErr=true;
                                        break 3;                            
                                    }
                                    
                                    // if($this->exlIsValidCode($locval)==true){
                                    //     $validationErr=false; 
                                    // }else {
                                    //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Location City Name ".$locval." Space not allowed or Only Alpha Numeric allowed" );
                                    //     $validationErr=true;
                                    //     break 3;                            
                                    // }
                                    // if($this->exlIsValidLen($locval,10)==true){
                                    //     $validationErr=false; 
                                    // }else {
                                    //     $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: Length: Location City Name ".$locval." can have max 10 character.");
                                    //     $validationErr=true;
                                    //     break 3;                            
                                    // }

                                    //check code
                                    $lctryid = $headerArr[$hIndex]["location"][$locindex]["location_country_id"];
                                    $lstateid = $headerArr[$hIndex]["location"][$locindex]["location_state_id"];
                                    $resdata = $this->exlIsCityExists($lctryid,$lstateid,$locval);
                                    if($resdata["result"]==true){
                                        $validationErr=false; 
                                        $headerArr[$hIndex]["location"][$locindex]["location_city_id"]=$resdata["id"];
                                        
                                    }else {
                                        $validationErr=true;
                                        $headerArr[$hIndex]["location"][$locindex]["location_city_id"]="";
                                        $this->appendLogData($logfile,"Vendor ".$hIndex.": Location City Name ".$locval." not found.");
                                        break 3; 
                                    } 
                                } // not blank                       
                            } //location_city_name

                            if($lockey=="location_pin_code")
                            {
                                if(trim($locval)!=""){
                                    if($this->exlIsValidLen($locval,20)==true){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Details: Location Pincode: ".$locval." can have max 20 character.");
                                        $validationErr=true;
                                        break 3;                            
                                    }            
                                    if($this->exlIsValidCode($locval)==true){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Details: Location Pincode ".$locval." Space not allowed or Only Alpha Numeric allowed" );
                                        $validationErr=true;
                                        break 3;                            
                                    }   
                                }         
                            }// location_pin_code

                            if($lockey=="location_gstin")
                            {
                                if(trim($locval)!=""){

                                    if($this->exlIsValidLen($locval,15)==true){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Vendor ".$hIndex."- Location Details: GSTIN number: ".$locval." can have max 15 character.");
                                        $validationErr=true;
                                        break 3;                            
                                    }   
                                    
                                    if($this->exlIsValidGSTIN($locval)==true){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Vendor ".$hIndex."- Location Details: Invalid GSTIN number ".$locval );
                                        $validationErr=true;
                                        break 3;                            
                                    }   
                                }         
                            }// location_gstin

                            if($lockey=="location_contact_person_name")
                            {
                                if(trim($locval)!=""){
                                    if($this->exlIsValidLen($locval,30)==true){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Location: Contact Person Name: ".$locval." can have max 30 character.");
                                        $validationErr=true;
                                        break 3;                            
                                    }
                                }         
                            }// location_contact_person_name

                            if($lockey=="location_designation")
                            {
                                if(trim($locval)!=""){
                                    if($this->exlIsValidLen($locval,20)==true){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Location: Designation: ".$locval." can have max 20 character.");
                                        $validationErr=true;
                                        break 3;                            
                                    }
                                }         
                            }// location_designation

                            if($lockey=="location_email_id")
                            {
                                if(trim($locval)!=""){                            
                                    if($this->exlIsValidLen($locval,50)==true){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Location Details: Email".$locval." can have max 50 character.");
                                        $validationErr=true;
                                        break 3;                            
                                    }
                                    if($this->exlIsValidEmail($locval)==true){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Location Details: Email".$locval );
                                        $validationErr=true;
                                        break 3;                            
                                    }   
                                }
                            }// location_email_id  
                            if($lockey=="location_mobile_no")
                            {
                                if(trim($locval)!=""){                            
                                    if($this->exlIsValidLen($locval,20)==true){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Location Details: Mobile No ".$locval." can have max 20 character.");
                                        $validationErr=true;
                                        break 3;                            
                                    }
                                }
                            }// location_mobile_no 
                            if($lockey=="location_special_instruction")
                            {
                                if(trim($locval)!=""){                            
                                    if($this->exlIsValidLen($locval,50)==true){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Location Details: Special Instructions ".$locval." can have max 50 character.");
                                        $validationErr=true;
                                        break 3;                            
                                    }
                                }
                            }// location_special_instruction 

                            if($lockey=="location_bill_to")
                            {
                                if(trim($locval)!=""){                            
                                    if($locval=="1"){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Location Details: Invaid Bill To value=".$locval.". Blank or 1 Allowed.");
                                        $validationErr=true;
                                        break 3;                            
                                    }
                                }
                            }// location_bill_to 
                            if($lockey=="location_default_billing")
                            {
                                if(trim($locval)!=""){                            
                                    if($locval=="1"){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Location Details: Invaid Default Billing value=".$locval.". Blank or 1 Allowed.");
                                        $validationErr=true;
                                        break 3;                            
                                    }
                                }
                            }// location_default_billing 


                            if($lockey=="location_ship_to")
                            {
                                if(trim($locval)!=""){                            
                                    if($locval=="1"){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Location Details: Invaid Ship To value=".$locval.". Blank or 1 Allowed.");
                                        $validationErr=true;
                                        break 3;                            
                                    }
                                }
                            }// location_ship_to 

                            if($lockey=="location_default_shipping")
                            {
                                if(trim($locval)!=""){                            
                                    if($locval=="1"){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Location Details: Invaid Default Shipping value=".$locval.". Blank or 1 Allowed.");
                                        $validationErr=true;
                                        break 3;                            
                                    }
                                }
                            }// location_default_shipping 
                        ///------------------------------------
                        }
                    }//location foreach row end-loop

                }//location all rows grid loop
                            
                //location name 
                if($anyloc==true){
                    $validationErr=false; 
                }else {
                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Location Details:Location name is blank. Please enter location data for at least  one row.");
                    $validationErr=true;
                    break 1;                            
                }
                //Default Billing 
                if($defbillfound==true){
                    $validationErr=false; 
                }else {
                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Location Details: Please Select Default Billing.");
                    $validationErr=true;
                    break 1;                            
                }
                if( $defbillcount=="1"){
                    $validationErr=false; 
                }else {
                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Location Details: Please Select Default Billing Single Time.");
                    $validationErr=true;
                    break 1;                            
                }
                //Default Shipping
                if($defshipfound==true){
                    $validationErr=false; 
                }else {
                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Location Details: Please Select Default Shipping.");
                    $validationErr=true;
                    break 1;                            
                }
                if( $defshipcount=="1"){
                    $validationErr=false; 
                }else {
                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid Location Details: Please Select Default Shipping Single Time.");
                    $validationErr=true;
                    break 1;                            
                }                
                //-------------LOCATION END
                //------------------------
                //-------------UDF BEGIN 
                //dump($hRowData["udf"]); 
                $exlUdfLabel=[];              
                foreach($hRowData["udf"] as $udfkey=>$udfrow){
                    if(trim($udfrow["udf_udf_label"])!=""){
                        $exlUdfLabel[] = trim($udfrow["udf_udf_label"]);  //fetch excel udf label
                    }
                   
                }
                if(count($udfDbLabel)==count($exlUdfLabel) ){
                    $validationErr=false;
                }else{
                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid :  UDF Data. ");
                    $validationErr=true;
                    break 1; 
                }
                
                $dif_udfs=array_diff($udfDbLabel,$exlUdfLabel); // check if Vendor has same db udf label data
                //($dif_udfs);
                if(!empty($dif_udfs)){
                    foreach($dif_udfs as $dkey=>$dval){
                        $this->appendLogData($logfile,"Vendor ".$hIndex." - ".$dval." - UDF Field not found. (Case sensitive)");
                        $validationErr=true;                                  
                    }
                    break 1;
                }else{                    
                    $validationErr=false; 
                }
                //check udf value type
                foreach($hRowData["udf"] as $udfkey=>$udfrow){
                        $exlUdfLabel[] = trim($udfrow["udf_udf_label"]);  //fetch excel udf label
                }

                /////----------------------------------------
                foreach($hRowData["udf"] as $udfIndex=>$udfrow2){
                    foreach ($dbUDFData as $dbukey => $dburow) {
                        if( strtoupper($udfrow2["udf_udf_label"])==strtoupper($dburow->LABEL) )
                        {  
                            $headerArr[$hIndex]["udf"][$udfIndex]["udf_id"]=$dburow->UDFVID;                            
                            $headerArr[$hIndex]["udf"][$udfIndex]["udf_valuetype"]=strtoupper($dburow->VALUETYPE);   
                            
                            if(strtoupper($dburow->ISMANDATORY)=="1"){
                                if( $this->exlIsBlank(trim($udfrow2["udf_value"]))==true){
                                    $validationErr=false; 
                                }else{
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid, Blank UDF value, Lable=".$udfrow2["udf_udf_label"]." value can not be left blank.");
                                    $validationErr=true;
                                    break 3;                                     
                                }
                            }
                        
                            //dd($udfrow2["udf_value"]);
                            if(strtoupper($dburow->VALUETYPE)=="NUMERIC"){
                                if(is_numeric($udfrow2["udf_value"])){
                                    $validationErr=false; 
                                }else{
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: UDF Numeric type, Lable=".$udfrow2["udf_udf_label"]." value=".$udfrow2["udf_value"]);
                                    $validationErr=true;
                                    break 3;                                     
                                }
                            }
                            if(strtoupper($dburow->VALUETYPE)=="BOOLEAN"){
                                if(is_bool(boolval($udfrow2["udf_value"]))){
                                    $validationErr=false;
                                }else{
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: UDF Boolean type,  Lable=".$udfrow2["udf_udf_label"]." value=".$udfrow2["udf_value"]);
                                    $validationErr=true;
                                    break 3; 
                                }
                            }
                            
                            if(strtoupper($dburow->VALUETYPE)=="DATE"){
                                if($this->exlIsValidDate($udfrow2["udf_value"])==true){
                                    $validationErr=false;
                                    $headerArr[$hIndex]["udf"][$udfIndex]["udf_value"]= Date('Y-m-d',strtotime($udfrow2["udf_value"]));
                                }else{
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid :  UDF Date type,  Lable=".$udfrow2["udf_udf_label"]." value=".$udfrow2["udf_value"]);
                                    $validationErr=true;
                                    break 3; 
                                }
                            }      
                            if(strtoupper($dburow->VALUETYPE)=="TIME"){
                                if($this->exlIsValidTime($udfrow2["udf_value"])==true){
                                    $validationErr=false;
                                }else{
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid :  UDF Time type,  Lable=".$udfrow2["udf_udf_label"]." value=".$udfrow2["udf_value"]);
                                    $validationErr=true;
                                    break 3; 
                                }
                            }  
                            if(strtoupper($dburow->VALUETYPE)=="COMBOBOX"){
                                $descArr=[];
                                $descArr = explode(",",$dburow->DESCRIPTIONS);
                                if(in_array(trim($udfrow2["udf_value"]),$descArr)){
                                    $validationErr=false;
                                }else{
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid :  UDF Combo value,  Lable=".$udfrow2["udf_udf_label"]." value=".$udfrow2["udf_value"]);
                                    $validationErr=true;
                                    break 3; 
                                }
                            }                 
                                                     
                            
                        }
                    }
                    
                   
                    foreach ($udfrow2 as $ukey3 => $uvalue3) {  
                        if(trim($udfrow2["udf_udf_label"])!=""){
                            if($ukey3=="udf_value"){                              
                                // if($this->exlIsBlank($uvalue3)==true){
                                //     $validationErr=false; 
                                // }else {
                                
                                //     $this->appendLogData($logfile,"Invalid Vendor ".$hIndex.": Blank UDF value." );
                                //     $validationErr=true;
                                //     break 3;                            
                                // }
                                if($this->exlIsValidLen($uvalue3,100)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Vendor ".$hIndex."- Invalid: UDF Value ".$uvalue3." can have max 100 character.");
                                    $validationErr=true;
                                    break 3;                            
                                }      
                            }// udf_value   
                        } //udf_udf_label
                                                  
                        
                    }//udf row end-loop
                }//udf grid loop
                /////----------------------------------------
                //-------------UDF END

            } 
            //-----------check validation end
        
        
        //dump($headerArr);
        if($validationErr){
                fclose($logfile);
                //echo "<br>--validation error--";
                return redirect()->route("master",[48,"importdata"])->with("logerror",$logfile_path);  
        }
        
        //dd("--validation executiaon complete");
            ///----------------------------------------save data begin
            foreach($headerArr as $hIndex=>$hRowData)
            {
                
                $VCODE          		=	strtoupper(trim($hRowData["header"]["vendor_code"]));     
                $NAME           		=   trim($hRowData["header"]["vendor_name"]); 
                $VENDOR_LEGAL_NAME	    =   trim($hRowData["header"]["vendor_legal_name"]); 
                $VGID_REF				=   trim($hRowData["header"]["vendor_group_id"]);
                $OLD_REFCODE 			=   $hRowData["header"]["old_ref_code"];  
                $GLID_REF				=   $hRowData["header"]["general_ledger_id"]; 
                $REGADDL1				=   $hRowData["header"]["registered_address_line1"]; 
                $REGADDL2 				=   $hRowData["header"]["registered_address_line2"]; 
                $REGCTRYID_REF			=   $hRowData["header"]["country_id"];
                $REGSTID_REF			=   $hRowData["header"]["state_id"];
                $REGCITYID_REF			=   $hRowData["header"]["city_id"];
                $REGPIN 				=   $hRowData["header"]["registered_pincode"];
                $CHA                    =   ($hRowData["header"]["cha"]=="1" || strtolower($hRowData["header"]["cha"])=="true" ) ? 1 : 0 ; 
                

                $CORPADDL1 				=   $hRowData["header"]["contact_corporate_address_line1"];
                $CORPADDL2 				=   $hRowData["header"]["contact_corporate_address_line2"];
                $CORPCTRYID_REF 		=   trim($hRowData["header"]["contact_country_id"]) !="" ? $hRowData["header"]["contact_country_id"]:NULL;
                $CORPSTID_REF 			=   trim($hRowData["header"]["contact_state_id"]) !="" ? $hRowData["header"]["contact_state_id"]:NULL;
                $CORPCITYID_REF 		=   trim($hRowData["header"]["contact_city_id"]) !="" ? $hRowData["header"]["contact_city_id"]:NULL;
                $CORPPIN 				=   trim($hRowData["header"]["contact_pincode"]) !="" ? $hRowData["header"]["contact_pincode"]:NULL;
                $EMAILID 				=   trim($hRowData["header"]["contact_email_id"]) !="" ? $hRowData["header"]["contact_email_id"]:NULL;
                $WEBSITE 				=   trim($hRowData["header"]["contact_website"]) !="" ? $hRowData["header"]["contact_website"]:NULL;
                $PHNO 					=   trim($hRowData["header"]["contact_phone_no"]) !="" ? $hRowData["header"]["contact_phone_no"]:NULL;
                $MONO 					=   trim($hRowData["header"]["contact_mobile_no"]) !="" ? $hRowData["header"]["contact_mobile_no"]:NULL;
                $CPNAME 				=   trim($hRowData["header"]["contact_contact_person"]) !="" ? $hRowData["header"]["contact_contact_person"]:NULL;
                $SKYPEID 				=   trim($hRowData["header"]["contact_skype"]) !="" ? $hRowData["header"]["contact_skype"]:NULL;

                $INDSID_REF 			=   trim($hRowData["header"]["statutory_industry_type_id"]) !="" ? $hRowData["header"]["statutory_industry_type_id"]:NULL;
                $INDSVID_REF 			=   trim($hRowData["header"]["statutory_industry_vertical_id"]) !="" ? $hRowData["header"]["statutory_industry_vertical_id"]:NULL;
                $DEALSIN 				=   trim($hRowData["header"]["statutory_deals_in"]) !="" ? $hRowData["header"]["statutory_deals_in"]:NULL;
                $GSTTYPE				=   trim($hRowData["header"]["statutory_gst_type_id"]) !="" ? $hRowData["header"]["statutory_gst_type_id"]:NULL;
                $DEFCRID_REF			=   trim($hRowData["header"]["statutory_default_currency_id"]) !="" ? $hRowData["header"]["statutory_default_currency_id"]:NULL;
                $GSTIN 					=   trim($hRowData["header"]["statutory_gstin"]) !="" ? $hRowData["header"]["statutory_gstin"]:NULL;
                $CREDITLIMIT 			=   trim($hRowData["header"]["statutory_credit_limit"]) !="" ? $hRowData["header"]["statutory_credit_limit"]:NULL;
                $CIN 					=   trim($hRowData["header"]["statutory_cin"]) !="" ? $hRowData["header"]["statutory_cin"]:NULL;
                $CREDITDAY 				=   trim($hRowData["header"]["statutory_credit_days"]) !="" ? $hRowData["header"]["statutory_credit_days"]:NULL;
                $PANNO 					=   trim($hRowData["header"]["statutory_pan_no"]) !="" ? $hRowData["header"]["statutory_pan_no"]:NULL;
                
                $EXE_GST 				=   ($hRowData["header"]["statutory_execeptional_for_gst"]=="1" || strtolower($hRowData["header"]["statutory_execeptional_for_gst"])=="true" ) ? 1 : 0 ; 
                $MSME_NO 			    =   trim($hRowData["header"]["statutory_msme_no"]) !="" ? $hRowData["header"]["statutory_msme_no"]:NULL;
                $FACTORY_ACT_NO 		=   trim($hRowData["header"]["statutory_factory_no"]) !="" ? $hRowData["header"]["statutory_factory_no"]:NULL;

                $TDS_APPLICABLE         =   ($hRowData["header"]["statutory_tds_applicable"]=="1" || strtolower($hRowData["header"]["statutory_tds_applicable"])=="true" ) ? 1 : 0 ; 
                $CERTIFICATE_NO 	    =   trim($hRowData["header"]["statutory_certificate_no"]) !="" ? $hRowData["header"]["statutory_certificate_no"]:NULL;
                
                $stu_exp_date = NULL;
                if(trim($hRowData["header"]["statutory_expiry_date"]) !=""){
                    $stu_exp_date  = Date('Y-m-d',strtotime(trim($hRowData["header"]["statutory_expiry_date"])));
                }
                $EXPIRY_DT 				=   $stu_exp_date;
                $ASSESSEEID_REF 		=   trim($hRowData["header"]["statutory_assessee_type_id"]) !="" ? $hRowData["header"]["statutory_assessee_type_id"]:NULL;
                $HOLDINGID_REF 			=   trim($hRowData["header"]["statutory_tds_id"])           !="" ? $hRowData["header"]["statutory_tds_id"]:NULL;

                $SAP_VENDOR_CODE 		=   trim($hRowData["header"]["alps_specific_sap_vendor_code"]) !="" ? $hRowData["header"]["alps_specific_sap_vendor_code"]:NULL;
                $SAP_VENDOR_NAME1 		=   trim($hRowData["header"]["alps_specific_sap_vendor_name1"]) !="" ? $hRowData["header"]["alps_specific_sap_vendor_name1"]:NULL;
                $SAP_VENDOR_NAME2 		=   trim($hRowData["header"]["alps_specific_sap_vendor_name2"]) !="" ? $hRowData["header"]["alps_specific_sap_vendor_name2"]:NULL;
                $SAP_VENDOR_NAME3 		=   trim($hRowData["header"]["alps_specific_sap_vendor_name3"]) !="" ? $hRowData["header"]["alps_specific_sap_vendor_name3"]:NULL;
                $SAP_CORPORATE_GROUP    =   trim($hRowData["header"]["alps_specific_sap_corporate_group"]) !="" ? $hRowData["header"]["alps_specific_sap_corporate_group"]:NULL;
                $SAP_ACCOUNT_GROUP      =  trim($hRowData["header"]["alps_specific_sap_account_group"]) !="" ? $hRowData["header"]["alps_specific_sap_account_group"]:NULL;                
                $SAP_ACCOUNT_GROUP_NAME =   trim($hRowData["header"]["alps_specific_sap_account_group_name"]) !="" ? $hRowData["header"]["alps_specific_sap_account_group_name"]:NULL;
                $SAP_TRADING_PARTNER    =   trim($hRowData["header"]["alps_specific_sap_trading_partner"]) !="" ? $hRowData["header"]["alps_specific_sap_trading_partner"]:NULL;
                $SAP_TRADING_PARTNER_NAME   =   trim($hRowData["header"]["alps_specific_sap_trading_partner_name"]) !="" ? $hRowData["header"]["alps_specific_sap_trading_partner_name"]:NULL;
                $SAP_INVOCING_PARTY         =   trim($hRowData["header"]["alps_specific_sap_invoicing_party"]) !="" ? $hRowData["header"]["alps_specific_sap_invoicing_party"]:NULL;
                $OUR_CODE_VBOOK             =  trim($hRowData["header"]["alps_specific_our_code_in_vendor_book"]) !="" ? $hRowData["header"]["alps_specific_our_code_in_vendor_book"]:NULL;

                //POINTOFCONTACT                
                $pocData =[];                
                foreach($hRowData["poc"] as $pindex=>$prow)
                {
                    if(trim($prow['poc_person_name'])!=""){
                        $pocData[$pindex]['NAME']        =   $prow["poc_person_name"];
                        $pocData[$pindex]['DESIG']       =   $prow["poc_designation"];
                        $pocData[$pindex]['MONO']        =   $prow["poc_mobile"];
                        $pocData[$pindex]['EMAIL']       =   $prow["poc_email"];
                        $pocData[$pindex]['LLNO']        =   $prow["poc_ll_no"];
                        $pocData[$pindex]['AUTHLEVEL']   =   $prow["poc_authority_level"];
                        $p_bir_date = NULL;
                        if(trim($prow["poc_birthday"]) !=""){
                            $p_bir_date  = Date('Y-m-d',strtotime(trim($prow["poc_birthday"])));                        
                        }
                        $pocData[$pindex]['DOB']         =   $p_bir_date;
                    }
                }                
                if(count($pocData)>0){            
                        $pocwrapped["POINTOFCONTACT"] = $pocData;    
                        $poc_xml = ArrayToXml::convert($pocwrapped);
                        $XMLPOC = $poc_xml; 
                }else{
                        $XMLPOC = NULL;
                }
                //dump($XMLPOC);
                //BANK
                $bankData= [];
                foreach($hRowData["bank"] as $bindex=>$brow){
                    if(trim($brow['bank_bank_name'])!=""){
                        $bankData[$bindex]['NAME']       =   $brow['bank_bank_name']; 
                        $bankData[$bindex]['IFSC']       =   $brow['bank_ifsc'];  
                        $bankData[$bindex]['BRANCH']     =   $brow['bank_branch']; 
                        $bankData[$bindex]['ACTYPE']     =   strtoupper($brow['bank_account_type']);  
                        $bankData[$bindex]['ACNO']       =   $brow['bank_account_no']; 
                        $bankData[$bindex]['BYDEFALUT']  =   $brow['bank_default_bank']; 
                    }  
                }
                if(count($bankData)>0){            
                    $bankwrapped["BANK"] = $bankData;    
                    $bank_xml = ArrayToXml::convert($bankwrapped);
                    $XMLBANK = $bank_xml;
                }else{
                    $XMLBANK = NULL;
                }
                //dump($XMLBANK);
                //LOCATION
                $locationData = [];
                foreach($hRowData["location"] as $lcindex=>$lcrow){
                    if(trim($lcrow['location_location_name'])!=""){
                        $locationData[$lcindex]['NAME']           =  $lcrow['location_location_name']; 
                        $locationData[$lcindex]['LADD']           =  $lcrow['location_location_address'];  
                        $locationData[$lcindex]['CTRYID_REF']     =  $lcrow['location_country_id']; 
                        $locationData[$lcindex]['STID_REF']       =  $lcrow['location_state_id'];
                        $locationData[$lcindex]['CITYID_REF']     =  $lcrow['location_city_id'];
                        $locationData[$lcindex]['PIN']            =  $lcrow['location_pin_code'];
                        $locationData[$lcindex]['GSTIN']          =  $lcrow['location_gstin'];
                        $locationData[$lcindex]['CPNAME']         =  $lcrow['location_contact_person_name'];
                        $locationData[$lcindex]['CPDESIGNATION']  =  $lcrow['location_designation'];
                        $locationData[$lcindex]['EMAIL']          =  $lcrow['location_email_id'];
                        $locationData[$lcindex]['MONO']           =  $lcrow['location_mobile_no'];
                        $locationData[$lcindex]['SPECIAL_INS']    =  $lcrow['location_special_instruction'];
                                                            
                        $locationData[$lcindex]['BILLTO']         =  ($lcrow['location_bill_to']=="1" || strtolower($lcrow['location_bill_to'])=="true" ) ? 1 : 0 ; 
                        $locationData[$lcindex]['DEFAULT_BILLING'] =  ($lcrow['location_default_billing']=="1" || strtolower($lcrow['location_default_billing'])=="true" ) ? 1 : 0 ; 
                        $locationData[$lcindex]['SHIPTO']         =  ($lcrow['location_ship_to']=="1" || strtolower($lcrow['location_ship_to'])=="true" ) ? 1 : 0 ;  
                        $locationData[$lcindex]['DEFAULT_SHIPPING'] =  ($lcrow['location_default_shipping']=="1" || strtolower($lcrow['location_default_shipping'])=="true" ) ? 1 : 0 ; 
                    }  
                }
                if(count($locationData)>0){            
                    $locationwrapped["LOCATION"] = $locationData;    
                    $location_xml = ArrayToXml::convert($locationwrapped);
                    $XMLLOCATION = $location_xml;
                }else{
                    $XMLLOCATION = NULL;
                }
                //dump($XMLLOCATION );

                // //UDF FIELDS
                $udffield_Data = [];
                foreach($hRowData["udf"] as $uindex=>$urow){
                    if(trim($urow['udf_udf_label'])!="")
                    {
                        $udffield_Data[$uindex]['UDFVENDORID_REF'] = $urow['udf_id'];  

                        if($urow['udf_valuetype']=="date"){
                            $udfval = Date("Y-m-d",strtotime($urow['udf_value']));
                        }else{
                            $udfval = $urow['udf_value'];
                        }
                        $udffield_Data[$uindex]['VALUE'] = $udfval; 
                    }
                }  

                if(count($udffield_Data)>0){            
                    $udffield_wrapped["UDF"] = $udffield_Data;  
                    $udffield__xml = ArrayToXml::convert($udffield_wrapped);
                    $XMLUDF = $udffield__xml;        

                }else{
                        $XMLUDF = NULL;
                }
                //dump($XMLUDF);
                $DEACTIVATED 			= 	'0';
                $DODEACTIVATED 			= 	NULL;
                
                $CYID_REF = Auth::user()->CYID_REF;
                $BRID_REF = Session::get('BRID_REF');
                $FYID_REF = Session::get('FYID_REF'); 
                $VTID 	= $this->vtid_ref;
                $USERID = Auth::user()->USERID;
                $UPDATE ="ADD";
                $UPDATE =  Date('Y-m-d');
                $UPTIME = Date('h:i:s.u');         
                $ACTION     =   "ADD";
                $IPADDRESS  =   $request->getClientIp();

                

                $save_data = [
                    $VCODE,         $NAME,          $VGID_REF,      $OLD_REFCODE,       $GLID_REF,
                    $REGADDL1,      $REGADDL2,      $REGCTRYID_REF, $REGSTID_REF,       $REGCITYID_REF,
                    $REGPIN,        $CORPADDL1,     $CORPADDL2,     $CORPCTRYID_REF,    $CORPSTID_REF, 
                    $CORPCITYID_REF,$CORPPIN,       $EMAILID,       $WEBSITE,           $PHNO,
                    $MONO,          $CPNAME,        $SKYPEID,       $INDSID_REF,        $INDSVID_REF,
                    $DEALSIN,       $DEFCRID_REF,   $GSTTYPE,       $GSTIN,             $CIN,
                    $PANNO,         $CREDITLIMIT,   $CREDITDAY,     $VENDOR_LEGAL_NAME, $EXE_GST,
                    $DEACTIVATED,   $DODEACTIVATED, $TDS_APPLICABLE,$ASSESSEEID_REF,    $HOLDINGID_REF,
                    $CERTIFICATE_NO,$EXPIRY_DT,     $MSME_NO,       $FACTORY_ACT_NO,    $SAP_VENDOR_CODE,
                    $SAP_VENDOR_NAME1,$SAP_VENDOR_NAME2,            $SAP_VENDOR_NAME3,  $SAP_CORPORATE_GROUP,
                    $SAP_ACCOUNT_GROUP  , $SAP_ACCOUNT_GROUP_NAME,  $SAP_TRADING_PARTNER, $SAP_TRADING_PARTNER_NAME,
                    $SAP_INVOCING_PARTY ,$OUR_CODE_VBOOK,
                    
                    $CYID_REF,      $BRID_REF,          $FYID_REF,
                    $XMLLOCATION,   $XMLBANK,       $XMLPOC,        $XMLUDF,            $VTID,
                    $USERID,        $UPDATE,        $UPTIME,        $ACTION,            $IPADDRESS,$CHA
                ];
                
               // dump($request->all());
              // dd($save_data);
                
                try {
                $sp_result = DB::select('EXEC SP_VENDOR_IN ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,? ,?', $save_data);

                } catch (\Throwable $th) {
                
                    $this->appendLogData($logfile," Vendor ".$hIndex.": There is some error. Please try after sometime. " );
                    fclose($logfile);
                    return redirect()->route("master",[48,"importdata"])->with("logerror",$logfile_path); 
            
                }

                if(Str::contains(strtoupper($sp_result[0]->RESULT), 'SUCCESS')){
                
                    $this->appendLogData($logfile,"Vendor ".$hIndex.": Record successfully inserted.","",1 );
                }else{
                    
                    $this->appendLogData($logfile," Vendor ".$hIndex.": Record not inserted. ".$sp_result[0]->RESULT );
                    fclose($logfile);
                    return redirect()->route("master",[48,"importdata"])->with("logerror",$logfile_path);                     
                }

            }
            ///----------------------------------------save data end    
            fclose($logfile);
            return redirect()->route("master",[48,"importdata"])->with("logsuccess",$logfile_path);      
            //echo "<br>-- excecution complete --";           
            //----------------------CHECK VALID DATA END

        //-----------------------------------      
        } //importexcelindb

        public function appendLogData($logfile, $label, $cellval="",$removeError=0){
            if($removeError==0){
                $txtstring = "Error:".$label." ".$cellval."\n"; 
            }else{
                $txtstring = $label." ".$cellval."\n"; 
            }
                
            echo "<br>".$txtstring;
            fwrite($logfile, $txtstring);
        }

        public function exlIsValidCode($strcode)
        {
            if(trim($strcode)=="" || !ctype_alnum(trim($strcode)) || strrpos($strcode, " "))
            {
                return false;
            }else{
                return true;
            }
        }

        public function exlGetVendorUDF($cyidRef)
        {
            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_VENDOR")->select('*')
                            ->whereIn('PARENTID',function($query) use ($cyidRef)
                                        {       
                                        $query->select('UDFVID')->from('TBL_MST_UDFFOR_VENDOR')
                                                        ->where('STATUS','=','A')
                                                        ->where('PARENTID','=',0)
                                                        ->where('DEACTIVATED','=',0)
                                                        ->where('CYID_REF','=',$cyidRef);
                                                                        
                            })->where('DEACTIVATED','=',0)
                            ->where('STATUS','<>','C')                    
                            ->where('CYID_REF','=',$cyidRef);     
                                
                    

            $objUdfVendData = DB::table('TBL_MST_UDFFOR_VENDOR')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$cyidRef)
                ->union($ObjUnionUDF)
                ->get()->toArray();  
                
              
            return $objUdfVendData;
            //

        }

        public function exlIsNum($strcode)
        {
        if(!is_numeric($strcode))
        {
            return false;
        }else{
            return true;
        }
        }

        public function exlIsOnlyDigit($strcode)
        {
        //no decimal value
        if(!ctype_digit($strcode))
        {
            return false;
        }else{
            return true;
        }
        }

        public function exlIsBlank($strcode)
        {
        if(trim($strcode)=="")
        {
            return false;
        }else{
            return true;
        }
        }

        public function exlIsDuplicateVCode($strcode){
            $code = strtoupper(trim($strcode));
            $CYID_REF       =   Auth::user()->CYID_REF;
            $ObjData = DB::select("SELECT top 1 VCODE FROM TBL_MST_VENDOR where CYID_REF = ".$CYID_REF." AND VCODE='".$code."' order by VCODE");   

                if(empty($ObjData)){
                    return true;
                }else{
                    
                    return false;
                }
        }

        // public function exlIsCodeFound($tblname,$colname, $strcode){
        //     $code = strtoupper(trim($strcode));
        //     echo "<br>code=".$code;
        //     $CYID_REF       =   Auth::user()->CYID_REF;
        //     $cur_date = date('Y-m-d');

        //     $DataFound = DB::select("SELECT  TOP 1 ".$colname." FROM ".$tblname." 
        //     where  (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null  or DODEACTIVATED='1900-01-01'  or DODEACTIVATED='1900-01-01' or DODEACTIVATED>='".$cur_date."') and CYID_REF = ".$CYID_REF." and ".$colname."='".$code."'  and STATUS = 'A' ");            

        //     if(!empty($DataFound)){
        //         return true;
        //     }else{
                
        //         return false;
        //     }
        // }

        public function exlIsVGROUPCodeExists($strcode){
            $code = strtoupper(trim($strcode));     
            $CYID_REF       =   Auth::user()->CYID_REF;
            
            $DataFound = DB::select("SELECT  TOP 1 VGID, VGCODE,DESCRIPTIONS FROM  TBL_MST_VENDORGROUP
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND  CYID_REF = ".$CYID_REF." 
            and lower(LTRIM(RTRIM(DESCRIPTIONS)))='".$code."' AND STATUS = 'A' ");            

            $resdata=[]; 
            if(!empty($DataFound)){
                $resdata["result"]=true; 
                $resdata["id"]=$DataFound[0]->VGID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=0; 
            }
            return $resdata;
        }


        public function exlIsGlExists($strcode){
            $code = strtoupper(trim($strcode));
            $CYID_REF       =   Auth::user()->CYID_REF;
            $cur_date = date('Y-m-d');

            $DataFound = DB::select("SELECT  TOP 1 GLID, GLCODE FROM  TBL_MST_GENERALLEDGER
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND  CYID_REF = ".$CYID_REF." 
            and GLCODE='".$code."' AND SUBLEDGER=1 AND STATUS = 'A' ");            

            $resdata=[]; 
            if(!empty($DataFound)){
                $resdata["result"]=true; 
                $resdata["id"]=$DataFound[0]->GLID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;
        }

        public function exlIsIndTyp($strcode){
            $code = strtolower(trim($strcode));
            $CYID_REF       =   Auth::user()->CYID_REF;
            
            $DataFound = DB::select("SELECT  TOP 1 INDSID, INDSCODE,DESCRIPTIONS FROM  TBL_MST_INDUSTRYTYPE
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND  CYID_REF = ".$CYID_REF." 
            and lower(LTRIM(RTRIM(DESCRIPTIONS)))='".$code."' AND STATUS = 'A' ");            

            $resdata=[]; 
            if(!empty($DataFound)){
                $resdata["result"]=true; 
                $resdata["id"]=$DataFound[0]->INDSID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;
        }

        public function exlIsIndVertical($strcode){
            $code = strtolower(trim($strcode));
            $CYID_REF       =   Auth::user()->CYID_REF;
           
            $DataFound = DB::select("SELECT  TOP 1 INDSVID, INDSVCODE,DESCRIPTIONS FROM  TBL_MST_INDUSTRYVERTICAL
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND CYID_REF = ".$CYID_REF." 
            and lower(LTRIM(RTRIM(DESCRIPTIONS)))='".$code."' AND STATUS = 'A' ");            

            $resdata=[]; 
            if(!empty($DataFound)){
                $resdata["result"]=true; 
                $resdata["id"]=$DataFound[0]->INDSVID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;
        }

        public function exlIsGSTTypeExists($strcode){
            $code = strtolower(trim($strcode));
            $CYID_REF       =   Auth::user()->CYID_REF;
           
            $DataFound = DB::select("SELECT  TOP 1 GSTID, GSTCODE,DESCRIPTIONS FROM  TBL_MST_GSTTYPE
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND lower(LTRIM(RTRIM(DESCRIPTIONS)))='".$code."' AND STATUS = 'A' ");            

            $resdata=[]; 
            if(!empty($DataFound)){
                $resdata["result"]=true; 
                $resdata["id"]=$DataFound[0]->GSTID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;
        }

        public function exlIsAsseTypeExists($strcode){
            $code = strtoupper(trim($strcode));
            $CYID_REF       =   Auth::user()->CYID_REF;
            $cur_date = date('Y-m-d');

            $DataFound = DB::select("SELECT  TOP 1 NOAID, NOA_CODE FROM  TBL_MST_NATUAREOF_ASSESSEE
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND CYID_REF = ".$CYID_REF." and NOA_CODE='".$code."' AND STATUS = 'A' ");            

            $resdata=[]; 
            if(!empty($DataFound)){
                $resdata["result"]=true; 
                $resdata["id"]=$DataFound[0]->NOAID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;
        }


        public function exlIsStTDSCode($asseid, $tdscode){

            $tdscode = strtoupper(trim($tdscode));
            $CYID_REF       =   Auth::user()->CYID_REF;
            $cur_date = date('Y-m-d');

            $DataFound = DB::select("SELECT  TOP 1 HOLDINGID, CODE FROM  TBL_MST_WITHHOLDING
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND CYID_REF = ".$CYID_REF." and ASSESSEEID_REF='".$asseid."' and CODE='".$tdscode."'" );            

            $resdata=[]; 
            if(!empty($DataFound)){
                $resdata["result"]=true; 
                $resdata["id"]=$DataFound[0]->HOLDINGID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;
        }



        public function exlIsDefCurrencyExists($strcode){
            $code = strtoupper(trim($strcode));
            $CYID_REF       =   Auth::user()->CYID_REF;
            $cur_date = date('Y-m-d');

            $DataFound = DB::select("SELECT  TOP 1 CRID, CRCODE FROM  TBL_MST_CURRENCY
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND  CRCODE='".$code."' AND STATUS = 'A' ");            

            $resdata=[]; 
            if(!empty($DataFound)){
                $resdata["result"]=true; 
                $resdata["id"]=$DataFound[0]->CRID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;
        }

        public function exlIsValidLen($strcode,$len)
        {
            if(strlen($strcode)>intval($len))
            {
                return false;
            }else{
                return true;
            }
        }

        public function exlIsValidDate($date, $format = 'j F, Y')
        {
            $d = DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) === $date;      

        }

        public function exlIsValidTime($timeStr){

            $dateObj = DateTime::createFromFormat('d.m.Y H:i', "10.10.2020 " . $timeStr);
            $dateObjOffset = DateTime::createFromFormat('d.m.Y H:i', "10.10.2020 " . '24:00');
        
            if($dateObjOffset <= $dateObj){
                return false;
            }
            if ($dateObj !== false) {
               return true;
            }
            else{
               return false;
            }
        }

        public function exlIsValidEmail($str) {
            if (filter_var($str, FILTER_VALIDATE_EMAIL)) {
                return true;
            } else {
                return false;
            }
            //return (!preg_match("/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i", $str)) ? false : true;
        }

        public function exlIsValidURL($str) {
            return (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $str)) ? false : true;

        }

        public function exlIsValidGSTIN($str) {
            return (!preg_match("/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/", $str)) ? false : true;
        }

        public function exlIsValidPAN($str) {
            return (!preg_match("/^[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}$/", $str)) ? false : true;
        }

        public function exlIsCountryExists($strcode){
            $code = strtolower(trim($strcode));
            
            $DataFound = DB::select("SELECT  TOP 1 CTRYID, CTRYCODE,NAME FROM  TBL_MST_COUNTRY
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND  lower(LTRIM(RTRIM(NAME)))='".$code."' AND STATUS = 'A' ");            

            $resdata=[]; 
            if(!empty($DataFound)){
                $resdata["result"]=true; 
                $resdata["id"]=$DataFound[0]->CTRYID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;

        }

        public function exlIsStateExists($ctryid, $statecode){
           
            $ctry_id =  trim($ctryid)==""? 0 : $ctryid;

            $state_name = strtolower(trim($statecode));
            $objState = DB::select("SELECT  TOP 1 STID,STCODE FROM  TBL_MST_STATE
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND CTRYID_REF='".$ctry_id."' and lower(LTRIM(RTRIM(NAME)))='".$state_name."' and STATUS = 'A' ");  

            $resdata=[]; 
            if(!empty($objState)){
                $resdata["result"]=true; 
                $resdata["id"]=$objState[0]->STID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;
        }

        public function exlIsCityExists($ctryid, $stateid, $cityname){
            
            $ctry_id =  trim($ctryid)==""? 0 : $ctryid;
            $stat_id =  trim($stateid)==""? 0 : $stateid;        
            $city_name = strtolower(trim($cityname));

            $objCity = DB::select("SELECT  TOP 1 CITYID,CITYCODE FROM  TBL_MST_CITY
            where  (DEACTIVATED=0 or DEACTIVATED is null) AND  CTRYID_REF='".$ctry_id."' and STID_REF='".$stat_id."'  and lower(LTRIM(RTRIM(NAME)))='".$city_name."' and STATUS = 'A' ");  

            $resdata=[]; 
            if(!empty($objCity)){
                $resdata["result"]=true; 
                $resdata["id"]=$objCity[0]->CITYID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;

        } 
//---------------- log end
//-------------------------------------------------------------------

    //display attachmentsform
    public function attachment($id){

        if(!is_null($id))
        {
            $objMst = DB::table("TBL_MST_VENDOR")
                        ->where('VID','=',$id)
                        ->select('*')
                        ->first();        

        

            $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
                        ->where('VTID','=',$this->vtid_ref)
                        ->select('VTID','VCODE','DESCRIPTIONS','INDATE')
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

                //dd( $objMst);

                return view('masters.purchase.vendormaster.mstfrm48attachment',compact(['objMst','objMstVoucherType','objAttachments']));
        }

    }


    public function add(){

        $cyidRef =  Auth::user()->CYID_REF;
        $bridRef = Session::get('BRID_REF');
        $fyidRef = Session::get('FYID_REF');
        $status  ='A';

        //------------
        $objDD = DB::table('TBL_MST_VENDORCODE')
                ->where('CYID_REF','=',$cyidRef)
                ->where('STATUS','=','A')
                ->select('TBL_MST_VENDORCODE.*')
                ->first();

        //dump($objDD);
        $objDOCNO ='';
        if(!empty($objDD)){
            if($objDD->SYSTEM_GRSR == "1")
            {               
                $objDOCNO = $objDD->PREFIX;              
                $objDOCNO = $objDOCNO.str_pad($objDD->LAST_RECORDNO+1, $objDD->MAX_DIGIT, "0", STR_PAD_LEFT);               
            }
        }   

       // dd($objDOCNO );
        //------------

        $account_type_data = explode(",",config("erpconst.bank.account_type"));

        $objAssesseeTypeList    =$this->AssesseeTypeList();

        $objGlList = DB::table('TBL_MST_GENERALLEDGER')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('STATUS','=','A')
        ->where('SUBLEDGER','=',1)
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('GLID','GLCODE','GLNAME')
        ->get();

        
        $objCountryList = DB::table('TBL_MST_COUNTRY')
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('CTRYID','CTRYCODE','NAME')
        ->get();

        $objIndTypeList = DB::table('TBL_MST_INDUSTRYTYPE')
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('INDSID','INDSCODE','DESCRIPTIONS')
        ->get();

        $objIndVerList = DB::table('TBL_MST_INDUSTRYVERTICAL')
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('INDSVID','INDSVCODE','DESCRIPTIONS')
        ->get();


        $GSTdata = ['GSTID','GSTCODE','DESCRIPTIONS'];
        $objGstTypeList       = Helper::getTableData('TBL_MST_GSTTYPE',$GSTdata,NULL,NULL, NULL,'GSTCODE','ASC');    

        $objCurrencyList = DB::table('TBL_MST_CURRENCY')
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('CRID','CRCODE','CRDESCRIPTION')
        ->get();

       

        $objCurrencyList =  DB::select('SELECT CRID,CRCODE,CRDESCRIPTION FROM TBL_MST_CURRENCY  WHERE  ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ? order by CRCODE ASC',['A']);

        $objUdfForVendor       = Helper::getUdfForVendor( $cyidRef);
        $objudfCount = count($objUdfForVendor);                
            if($objudfCount==0){
                $objudfCount=1;
            }

        $data1 = ['VGID','VGCODE','DESCRIPTIONS'];
        $objVendorGroupList       = Helper::getTableData('TBL_MST_VENDORGROUP',$data1,$cyidRef,NULL,NULL,'VGCODE','ASC');

        $TabSetting =   Helper::getAddSetting(Auth::user()->CYID_REF,'VENDOR_TAB_SETTING');

       
      return view('masters.purchase.vendormaster.mstfrm48add', compact([
            'objVendorGroupList',
            'objGlList',
            'objCountryList',
            'objIndTypeList',
            'objIndVerList',
            'objGstTypeList',
            'objCurrencyList',
            'objUdfForVendor',
            'objudfCount',
            'account_type_data',
            'objAssesseeTypeList',
            'objDD','objDOCNO',
            'TabSetting'
           
          ]));
       
   }

   public function getCountryWiseState(Request $request){

        $STdata = ['STID','STCODE','NAME'];
        $STwhere = 'CTRYID_REF='.$request['CTRYID_REF'];
        $objStateList   = Helper::getTableData2('TBL_MST_STATE',$STdata,NULL, NULL, NULL,$STwhere,'STCODE','ASC');

    
        if(!empty($objStateList)){
            foreach($objStateList as $state){
            
                echo '<tr >
                <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_STID_REF[]" id="stidref_'.$state->STID.'" class="cls_stidref" value="'.$state->STID.'" ></td>
                <td width="39%" class="ROW2">'.$state->STCODE.'
                <input type="hidden" id="txtstidref_'.$state->STID.'" data-desc="'.$state->STCODE.'-'.$state->NAME.'" value="'.$state->STID.'" />
                </td>
                <td width="39%" class="ROW3">'.$state->NAME.'</td>
                </tr>';
            }
        }
        else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
        }
        exit();
    }

    public function getStateWiseCity(Request $request){
        
        $CITYdata = ['CITYID','CITYCODE','NAME'];
        $CITYwhere = 'CTRYID_REF='.$request['CTRYID_REF'].' AND STID_REF='.$request['STID_REF'];
        $objCityList   = Helper::getTableData2('TBL_MST_CITY',$CITYdata,NULL, NULL, NULL,$CITYwhere,'CITYCODE','ASC');

        
        if(!empty($objCityList)){
            foreach($objCityList as $city){
            
                echo '<tr >
                <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_CITYID_REF[]" id="cityidref_'.$city->CITYID.'" class="cls_cityidref" value="'.$city->CITYID.'" ></td>
                <td width="39%" class="ROW2">'.$city->CITYCODE.'
                <input type="hidden" id="txtcityidref_'.$city->CITYID.'" data-desc="'.$city->CITYCODE.'-'.$city->NAME.'" value="'.$city->CITYID.'" />
                </td>
                <td width="39%" class="ROW3">'.$city->NAME.'</td>
                </tr>';
            }
        }
        else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
        }
    }

    public function getCorCountryWiseState(Request $request){
        
        $objStateList = DB::table('TBL_MST_STATE')
        //->where('DEACTIVATED','!=',1)
        ->where('STATUS','=','A')
        ->where('CTRYID_REF','=',$request['CTRYID_REF'])
        ->select('STID','NAME','STCODE')
        ->get();
    
        if(!empty($objStateList)){
            foreach($objStateList as $state){
            
                echo '<tr >
                <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_CORSTID_REF[]" id="cor_stidref_'.$state->STID.'" class="cls_cor_stidref" value="'.$state->STID.'" ></td>
                <td width="39%" class="ROW2">'.$state->STCODE.'
                <input type="hidden" id="txtcor_stidref_'.$state->STID.'" data-desc="'.$state->STCODE.'-'.$state->NAME.'" value="'.$state->STID.'" />
                </td>
                <td width="39%" class="ROW3">'.$state->NAME.'</td>
                </tr>';
            }
        }
        else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
        }
        exit();
    }

    public function getCorStateWiseCity(Request $request){
        
        $objCityList = DB::table('TBL_MST_CITY')
        //->where('DEACTIVATED','!=',1)
        ->where('STATUS','=','A')
        ->where('CTRYID_REF','=',$request['CTRYID_REF'])
        ->where('STID_REF','=',$request['STID_REF'])
        ->select('CITYID','CITYCODE','NAME')
        ->get();
        
        if(!empty($objCityList)){
            foreach($objCityList as $city){
            
                echo '<tr>
                <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_CORCITYID_REF[]"  id="cor_cityidref_'.$city->CITYID.'" class="cls_cor_cityidref" value="'.$city->CITYID.'" ></td>
                <td width="39%" class="ROW2">'.$city->CITYCODE.'
                <input type="hidden" id="txtcor_cityidref_'.$city->CITYID.'" data-desc="'.$city->CITYCODE.'-'.$city->NAME.'" value="'.$city->CITYID.'" />
                </td>
                <td width="39%" class="ROW3">'.$city->NAME.'</td>
                </tr>';
            }
        }
        else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
        }
    }

    public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $VCODE =  trim($request['VCODE']);
        
        $objLabel = DB::table('TBL_MST_VENDOR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('VCODE','=',$VCODE)
            ->select('VCODE')
            ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        exit();
    }

    public function save(Request $request){
        
       
        $VCODE          		=	strtoupper(trim($request['VCODE']));     
        $NAME           		=   trim($request['NAME']); 
		$VENDOR_LEGAL_NAME  	=   trim($request['VENDOR_LEGAL_NAME']); 
		$VGID_REF				=   trim($request['VGID_REF']);
		$OLD_REFCODE 			=   !is_null($request['OLD_REFCODE'])?trim($request['OLD_REFCODE']):NULL;  
		$GLID_REF				=   trim($request['GLID_REF']);
		$REGADDL1				=   trim($request['REGADDL1']);
		$REGADDL2 				=   !is_null($request['REGADDL2'])?trim($request['REGADDL2']):''; 
		$REGCTRYID_REF			=   trim($request['REGCTRYID_REF']);
		$REGSTID_REF			=   trim($request['REGSTID_REF']);
		$REGCITYID_REF			=   trim($request['REGCITYID_REF']);
		$REGPIN 				=   !is_null($request['REGPIN'])?trim($request['REGPIN']):NULL; 
		$CORPADDL1 				=   !is_null($request['CORPADDL1'])?trim($request['CORPADDL1']):NULL;
		$CORPADDL2 				=   !is_null($request['CORPADDL2'])?trim($request['CORPADDL2']):NULL;
		$CORPCTRYID_REF 		=   !is_null($request['CORPCTRYID_REF'])?trim($request['CORPCTRYID_REF']):NULL;
		$CORPSTID_REF 			=   !is_null($request['CORPSTID_REF'])?trim($request['CORPSTID_REF']):NULL;
		$CORPCITYID_REF 		=   !is_null($request['CORPCITYID_REF'])?trim($request['CORPCITYID_REF']):NULL;
		$CORPPIN 				=   !is_null($request['CORPPIN'])?trim($request['CORPPIN']):NULL;
		$EMAILID 				=   !is_null($request['EMAILID'])?trim($request['EMAILID']):NULL;
		$WEBSITE 				=   !is_null($request['WEBSITE'])?trim($request['WEBSITE']):NULL;
		$PHNO 					=   !is_null($request['PHNO'])?trim($request['PHNO']):NULL;
		$MONO 					=   !is_null($request['MONO'])?trim($request['MONO']):NULL;
		$CPNAME 				=   !is_null($request['CPNAME'])?trim($request['CPNAME']):NULL;
		$SKYPEID 				=   !is_null($request['SKYPEID'])?trim($request['SKYPEID']):NULL;
		$INDSID_REF 			=   !is_null($request['INDSID_REF'])?trim($request['INDSID_REF']):NULL;
		$INDSVID_REF 			=   !is_null($request['INDSVID_REF'])?trim($request['INDSVID_REF']):NULL;
		$DEALSIN 				=   !is_null($request['DEALSIN'])?trim($request['DEALSIN']):NULL;  // need to check saving or not
		$GSTTYPE				=   trim($request['GSTTYPE']);
		$DEFCRID_REF			=   trim($request['DEFCRID_REF']);
		$GSTIN 					=   !is_null($request['GSTIN'])?trim($request['GSTIN']):NULL;
		$CREDITLIMIT 			=   !is_null($request['CREDITLIMIT'])?trim($request['CREDITLIMIT']):NULL;
		$CIN 					=   !is_null($request['CIN'])?trim($request['CIN']):NULL;
		$CREDITDAY 				=   !is_null($request['CREDITDAY'])?trim($request['CREDITDAY']):NULL;
		$PANNO 					=   !is_null($request['PANNO'])?trim($request['PANNO']):NULL;
		$EXE_GST 				=   isset( $request['EXE_GST']) &&  (!is_null($request['EXE_GST']) ) ? $request['EXE_GST'] : 0; 

        
        $MSME_NO 					=   !is_null($request['MSME_NO'])?trim($request['MSME_NO']):NULL;
        $FACTORY_ACT_NO 					=   !is_null($request['FACTORY_ACT_NO'])?trim($request['FACTORY_ACT_NO']):NULL;
        $SAP_VENDOR_CODE 					=   !is_null($request['SAP_VENDOR_CODE'])?trim($request['SAP_VENDOR_CODE']):NULL;
        $SAP_VENDOR_NAME1 					=   !is_null($request['SAP_VENDOR_NAME1'])?trim($request['SAP_VENDOR_NAME1']):NULL;
        $SAP_VENDOR_NAME2 					=   !is_null($request['SAP_VENDOR_NAME2'])?trim($request['SAP_VENDOR_NAME2']):NULL;
        $SAP_VENDOR_NAME3 					=   !is_null($request['SAP_VENDOR_NAME3'])?trim($request['SAP_VENDOR_NAME3']):NULL;
        $SAP_CORPORATE_GROUP 					=   !is_null($request['SAP_CORPORATE_GROUP'])?trim($request['SAP_CORPORATE_GROUP']):NULL;
        $SAP_ACCOUNT_GROUP 					=   !is_null($request['SAP_ACCOUNT_GROUP'])?trim($request['SAP_ACCOUNT_GROUP']):NULL;
        $SAP_ACCOUNT_GROUP_NAME 					=   !is_null($request['SAP_ACCOUNT_GROUP_NAME'])?trim($request['SAP_ACCOUNT_GROUP_NAME']):NULL;
        $SAP_TRADING_PARTNER 					=   !is_null($request['SAP_TRADING_PARTNER'])?trim($request['SAP_TRADING_PARTNER']):NULL;
        $SAP_TRADING_PARTNER_NAME 					=   !is_null($request['SAP_TRADING_PARTNER_NAME'])?trim($request['SAP_TRADING_PARTNER_NAME']):NULL;
        $SAP_INVOCING_PARTY 					=   !is_null($request['SAP_INVOCING_PARTY'])?trim($request['SAP_INVOCING_PARTY']):NULL;
        $OUR_CODE_VBOOK 					=   !is_null($request['OUR_CODE_VBOOK'])?trim($request['OUR_CODE_VBOOK']):NULL;
        
		
        $DEACTIVATED 			= 	'0';
        $DODEACTIVATED 			= 	NULL;
        
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF'); 
          
        //POINTOFCONTACT
        $r_count1 = $request['Row_Count1'];
        $pocData =[];
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['POC_NAME_'.$i]) && !is_null($request['POC_NAME_'.$i]) && trim($request['POC_NAME_'.$i]!='')){
                $pocData[$i]['NAME']        =   isset( $request['POC_NAME_'.$i]) &&  (!is_null($request['POC_NAME_'.$i]) )? $request['POC_NAME_'.$i] : ''; 
                $pocData[$i]['DESIG']       =   isset( $request['POC_DESIG_'.$i]) &&  (!is_null($request['POC_DESIG_'.$i]) )? $request['POC_DESIG_'.$i] : ''; 
                $pocData[$i]['MONO']        =   isset( $request['POC_MONO_'.$i]) &&  (!is_null($request['POC_MONO_'.$i]) )? $request['POC_MONO_'.$i] : ''; 
                $pocData[$i]['EMAIL']       =   isset( $request['POC_EMAIL_'.$i]) &&  (!is_null($request['POC_EMAIL_'.$i]) )? $request['POC_EMAIL_'.$i] : ''; 
                $pocData[$i]['LLNO']        =   isset( $request['POC_LLNO_'.$i]) &&  (!is_null($request['POC_LLNO_'.$i]) )? $request['POC_LLNO_'.$i] : ''; 
                $pocData[$i]['AUTHLEVEL']   =   isset( $request['POC_AUTHLEVEL_'.$i]) &&  (!is_null($request['POC_AUTHLEVEL_'.$i]) )? $request['POC_AUTHLEVEL_'.$i] : '';  
                $pocData[$i]['DOB']         =   isset( $request['POC_DOB_'.$i]) &&  (!is_null($request['POC_DOB_'.$i]) )? $request['POC_DOB_'.$i] : NULL; 
            }
        }

        if(count($pocData)>0){            
                $pocwrapped["POINTOFCONTACT"] = $pocData;    
                $poc_xml = ArrayToXml::convert($pocwrapped);
                $XMLPOC = $poc_xml; 
        }else{
                $XMLPOC = NULL;
        }

        //BANK
        $r_count2 = $request['Row_Count2'];
        $bankData= [];
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['BANK_NAME_'.$i]) && !is_null($request['BANK_NAME_'.$i]) && trim($request['BANK_NAME_'.$i]!='')){

                $bankData[$i]['NAME']       =   isset( $request['BANK_NAME_'.$i]) &&  (!is_null($request['BANK_NAME_'.$i]) )? $request['BANK_NAME_'.$i] : ''; 
                $bankData[$i]['IFSC']       =   isset( $request['BANK_IFSC_'.$i]) &&  (!is_null($request['BANK_IFSC_'.$i]) )? $request['BANK_IFSC_'.$i] : ''; 
                $bankData[$i]['BRANCH']     =   isset( $request['BANK_BRANCH_'.$i]) &&  (!is_null($request['BANK_BRANCH_'.$i]) )? $request['BANK_BRANCH_'.$i] : '';  
                $bankData[$i]['ACTYPE']     =   isset( $request['BANK_ACTYPE_'.$i]) &&  (!is_null($request['BANK_ACTYPE_'.$i]) )? $request['BANK_ACTYPE_'.$i] : '';  
                $bankData[$i]['ACNO']       =   isset( $request['BANK_ACNO_'.$i]) &&  (!is_null($request['BANK_ACNO_'.$i]) ) ? $request['BANK_ACNO_'.$i] : '';  
                $bankData[$i]['BYDEFALUT']  =  isset( $request['BYDEFALUT_'.$i]) &&  (!is_null($request['BYDEFALUT_'.$i]) ) ? $request['BYDEFALUT_'.$i] : 0; 
            }  
        }
        if(count($bankData)>0){            
            $bankwrapped["BANK"] = $bankData;    
            $bank_xml = ArrayToXml::convert($bankwrapped);
            $XMLBANK = $bank_xml;
        }else{
            $XMLBANK = NULL;
        }

        //LOCATION
        $r_count3 = $request['Row_Count3'];
        $locationData = [];
        for ($i=0; $i<=$r_count3; $i++){
            if(isset($request['LOC_LADD_'.$i])){
                $locationData[$i]['NAME']       = isset( $request['LOC_NAME_'.$i]) &&  (!is_null($request['LOC_NAME_'.$i]) )? $request['LOC_NAME_'.$i] : ''; 
                $locationData[$i]['LADD']       = isset( $request['LOC_LADD_'.$i]) &&  (!is_null($request['LOC_LADD_'.$i]) )? $request['LOC_LADD_'.$i] : ''; 
                $locationData[$i]['CTRYID_REF']     =  $request['HDNLOC_CTRYID_REF_'.$i];
                $locationData[$i]['STID_REF']       =  $request['HDNLOC_STID_REF_'.$i];
                $locationData[$i]['CITYID_REF']     =  $request['HDNLOC_CITYID_REF_'.$i];
                $locationData[$i]['PIN']            =  $request['LOC_PIN_'.$i];
                $locationData[$i]['GSTIN']          =  $request['LOC_GSTIN_'.$i];
                $locationData[$i]['CPNAME']         =  $request['LOC_CPNAME_'.$i];
                $locationData[$i]['CPDESIGNATION']  =  $request['LOC_CPDESIGNATION_'.$i];
                $locationData[$i]['EMAIL']          =  $request['LOC_EMAIL_'.$i];
                $locationData[$i]['MONO']           =  $request['LOC_MONO_'.$i];
                $locationData[$i]['SPECIAL_INS']  =  $request['LOC_SPINSTRACTION_'.$i];
                $locationData[$i]['BILLTO']         =  isset( $request['LOC_BILLTO_'.$i]) &&  (!is_null($request['LOC_BILLTO_'.$i]) )? $request['LOC_BILLTO_'.$i] : 0; 
                $locationData[$i]['DEFAULT_BILLING'] =  isset( $request['LOC_DEFAULT_BILLTO_'.$i]) &&  (!is_null($request['LOC_DEFAULT_BILLTO_'.$i]) ) ? $request['LOC_DEFAULT_BILLTO_'.$i] : 0; 
                $locationData[$i]['SHIPTO']         =  isset( $request['LOC_SHIPTO_'.$i]) &&  (!is_null($request['LOC_SHIPTO_'.$i]) )? $request['LOC_SHIPTO_'.$i] : 0;  
                $locationData[$i]['DEFAULT_SHIPPING'] =  isset( $request['LOC_DEFAULT_SHIPTO_'.$i]) &&  (!is_null($request['LOC_DEFAULT_SHIPTO_'.$i]) )? $request['LOC_DEFAULT_SHIPTO_'.$i] : 0;  
            }  
        }

        if(count($locationData)>0){            
            $locationwrapped["LOCATION"] = $locationData;    
            $location_xml = ArrayToXml::convert($locationwrapped);
            $XMLLOCATION = $location_xml;
        }else{
            $XMLLOCATION = NULL;
        }

        //UDF FIELDS
        $r_count4 = $request['Row_Count4'];
        $udffield_Data = [];
        for ($i=0; $i<=$r_count4; $i++)
        {
            //$udffield_request = isset( $request['udffie_'.$i]) &&  (!is_null($request['udffie_'.$i]) )? $request['udffie_'.$i] : '';
            if(isset( $request['udffie_'.$i]))
             {
                $udffield_Data[$i]['UDFVENDORID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
            }
        }  
 
        if(count($udffield_Data)>0){            
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
 
        }else{
                $XMLUDF = NULL;
        }
 
        $VTID 	= $this->vtid_ref;
        $USERID = Auth::user()->USERID;
        $UPDATE ="ADD";
        $UPDATE =  Date('Y-m-d');
        $UPTIME = Date('h:i:s.u');
 
        $ACTION     =   "ADD";
        $IPADDRESS  =   $request->getClientIp();

        $TDS_APPLICABLE         =   (isset($request['TDS_APPLICABLE']) )? 1 : 0 ; 
        $CERTIFICATE_NO 	    =   isset($request['CERTIFICATE_NO']) && !is_null($request['CERTIFICATE_NO'])?trim($request['CERTIFICATE_NO']):NULL;
        $EXPIRY_DT 				=   isset($request['EXPIRY_DT']) && !is_null($request['EXPIRY_DT'])?trim($request['EXPIRY_DT']):NULL;
        $ASSESSEEID_REF 		=   isset($request['ASSESSEEID_REF']) && !is_null($request['ASSESSEEID_REF'])?trim($request['ASSESSEEID_REF']):NULL;
        $HOLDINGID_REF 			=   isset($request['HOLDINGID_REF']) && !is_null($request['HOLDINGID_REF'])?trim($request['HOLDINGID_REF']):NULL;
        $CHA                    =   (isset($request['CHA'])!="true" ? 0 : 1);
 
		
		$save_data = [
            $VCODE,         $NAME,          $VGID_REF,      $OLD_REFCODE,       $GLID_REF,
            $REGADDL1,      $REGADDL2,      $REGCTRYID_REF, $REGSTID_REF,       $REGCITYID_REF,
            $REGPIN,        $CORPADDL1,     $CORPADDL2,     $CORPCTRYID_REF,    $CORPSTID_REF, 
            $CORPCITYID_REF,$CORPPIN,       $EMAILID,       $WEBSITE,           $PHNO,
            $MONO,          $CPNAME,        $SKYPEID,       $INDSID_REF,        $INDSVID_REF,
            $DEALSIN,       $DEFCRID_REF,   $GSTTYPE,       $GSTIN,             $CIN,
            $PANNO,         $CREDITLIMIT,   $CREDITDAY,     $VENDOR_LEGAL_NAME, $EXE_GST,
            $DEACTIVATED,   $DODEACTIVATED, $TDS_APPLICABLE,$ASSESSEEID_REF,    $HOLDINGID_REF,
            $CERTIFICATE_NO,$EXPIRY_DT,    $MSME_NO,       $FACTORY_ACT_NO,     $SAP_VENDOR_CODE,
            $SAP_VENDOR_NAME1,$SAP_VENDOR_NAME2, $SAP_VENDOR_NAME3,             $SAP_CORPORATE_GROUP,
            $SAP_ACCOUNT_GROUP  , $SAP_ACCOUNT_GROUP_NAME,  $SAP_TRADING_PARTNER, $SAP_TRADING_PARTNER_NAME,
            $SAP_INVOCING_PARTY ,$OUR_CODE_VBOOK,
            
            $CYID_REF,      $BRID_REF,          $FYID_REF,
            $XMLLOCATION,   $XMLBANK,       $XMLPOC,        $XMLUDF,            $VTID,
            $USERID,        $UPDATE,        $UPTIME,        $ACTION,            $IPADDRESS, $CHA
        ];
        
       // dump($request->all());
       // dd($save_data);
		
	 		
      
       try {

            $sp_result = DB::select('EXEC SP_VENDOR_IN ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?', $save_data);
          // dd($sp_result);
            
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
    





   public function getAttValData(Request $request){
    
        $id = $request['id'];

        $ObjData =  DB::select('SELECT ATTVID,ATTID_REF,VALUE FROM TBL_MST_ATTRIBUTE_VAL
                                     WHERE ATTID_REF= ? ORDER BY VALUE ASC', [$id]);
        if(!empty($ObjData)){

           foreach ($ObjData as $index=>$dataRow){
           
            $row = '';
            $row = $row.'<tr id="attrvalue_'.$dataRow->ATTVID .'"  class="clsattrvalue"><td >'.$dataRow->VALUE;
            $row = $row.'<input type="hidden" id="txtattrvalue_'.$dataRow->ATTVID.'" data-desc="'.$dataRow->VALUE;
            $row = $row.'" value="'.$dataRow-> ATTVID.'"/></td></tr>';

            echo $row;
           }

        }else{
            echo '<tr><td> Record not found.</td></tr>';
        }
        exit();
    }

    public function getAttValDatasingle(Request $request){
    
        $id = $request['id'];

        $ObjData =  DB::table('TBL_MST_ATTRIBUTE_VAL')
                    ->where('TBL_MST_ATTRIBUTE_VAL.ATTVID','=',$id)
                    ->select('TBL_MST_ATTRIBUTE_VAL.VALUE')
                    ->first();
        echo ($ObjData->VALUE);
        exit();
    }

    public function getsubgroupsingle(Request $request){
    
        $id = $request['id'];

        $ObjData =  DB::table('TBL_MST_ITEMSUBGROUP')
                    ->where('TBL_MST_ITEMSUBGROUP.ISGID','=',$id)
                    ->select('TBL_MST_ITEMSUBGROUP.*')
                    ->first();
        echo ($ObjData->ISGCODE.' - '.$ObjData->DESCRIPTIONS);
        exit();
    }


   public function getsubgroup(Request $request){
    
        $id = $request['id'];

        $ObjData =  DB::select('SELECT ISGID, ITEMGID_REF, ISGCODE, DESCRIPTIONS FROM TBL_MST_ITEMSUBGROUP  
                    WHERE  ITEMGID_REF = ? order by ISGCODE ASC', [$id]);

        if(!empty($ObjData)){

           foreach ($ObjData as $index=>$dataRow){
           
            $row = '';
            $row = $row.'<tr id="itesubgrp_'.$dataRow->ISGID .'"  class="clsitesubgrp"><td >'.$dataRow->ISGCODE;
            $row = $row.'<input type="hidden" id="txtitesubgrp_'.$dataRow->ISGID.'" data-desc="'.$dataRow->ISGCODE .' - ';
            $row = $row.$dataRow->DESCRIPTIONS. '" value="'.$dataRow-> ISGID.'"/></td><td>'.$dataRow->DESCRIPTIONS.'</td></tr>';

            echo $row;
           }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();

    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    

     
     





    //uploads attachments files
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
        
        $image_path         =   "docs/company".$CYID_REF."/vendormst";     
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

                    //$filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."#_".$filenamewithextension;  
                    $filenametostore        =  $VTID.$ATTACH_DOCNO.date('YmdHis')."_".str_replace(' ', '', $filenamewithextension);  
                   

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
            return redirect()->route("master",[48,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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

            return redirect()->route("master",[48,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[48,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            return redirect()->route("master",[48,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   


   
   public function edit($id)
   {
         if(!is_null($id))
            {
                $status  ='A';
                $USERID     =   Auth::user()->USERID;
                $VTID       =   $this->vtid_ref;
                $CYID_REF   =   Auth::user()->CYID_REF;
                $BRID_REF   =   Session::get('BRID_REF');    
                $FYID_REF   =   Session::get('FYID_REF');

                $account_type_data = explode(",",config("erpconst.bank.account_type"));

                $objAssesseeTypeList    =$this->AssesseeTypeList();

                $sp_user_approval_req = [
                    $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
                ];        

                //get user approval data
                $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
                $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

                $objRights = DB::table('TBL_MST_USERROLMAP')
                ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
                ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
                ->where('TBL_MST_USERROLMAP.BRID_REF','=',$BRID_REF)
                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID)
                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                ->first();

                $objGlList = DB::table('TBL_MST_GENERALLEDGER')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('STATUS','=','A')
                ->where('SUBLEDGER','=',1)
                ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                ->select('GLID','GLCODE','GLNAME')
                ->get();
        
                
                $objCountryList = DB::table('TBL_MST_COUNTRY')
                ->where('STATUS','=','A')
                ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                ->select('CTRYID','CTRYCODE','NAME')
                ->get();
        
                $objIndTypeList = DB::table('TBL_MST_INDUSTRYTYPE')
                ->where('STATUS','=','A')
                ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                ->select('INDSID','INDSCODE','DESCRIPTIONS')
                ->get();
        
                $objIndVerList = DB::table('TBL_MST_INDUSTRYVERTICAL')
                ->where('STATUS','=','A')
                ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                ->select('INDSVID','INDSVCODE','DESCRIPTIONS')
                ->get();
        
        
                $GSTdata = ['GSTID','GSTCODE','DESCRIPTIONS'];
                $objGstTypeList       = Helper::getTableData('TBL_MST_GSTTYPE',$GSTdata,NULL, NULL, NULL,'GSTCODE','ASC');
            
        
                $objCurrencyList = DB::table('TBL_MST_CURRENCY')
                ->where('STATUS','=','A')
                ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                ->select('CRID','CRCODE','CRDESCRIPTION')
                ->get();
        
                $objCurrencyList =  DB::select('SELECT CRID,CRCODE,CRDESCRIPTION FROM TBL_MST_CURRENCY  WHERE  ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ? order by CRCODE ASC',['A']);
        
                $objUdfForVendor = Helper::getUdfForVendor(  Auth::user()->CYID_REF);

                $data1 = ['VGID','VGCODE','DESCRIPTIONS'];
                $objVendorGroupList       = Helper::getTableData('TBL_MST_VENDORGROUP',$data1, Auth::user()->CYID_REF, NULL, NULL,'VGCODE','ASC');
        

                $objMstCust = DB::table('TBL_MST_VENDOR')                    
                    ->where('TBL_MST_VENDOR.VID','=',$id)
                    ->where('TBL_MST_VENDOR.CYID_REF','=',$CYID_REF)
                    ->where('TBL_MST_VENDOR.BRID_REF','=',$BRID_REF)
                    ->select('TBL_MST_VENDOR.*')
                    ->first();

                if(strtoupper($objMstCust->STATUS)=="A" || strtoupper($objMstCust->STATUS)=="C"){
                    exit("Sorry, Only Un Approved record can edit.");
                }
                

                $objCusGro = DB::table('TBL_MST_VENDORGROUP')                    
                    ->where('VGID','=',$objMstCust->VGID_REF)
                    ->select('VGID','VGCODE','DESCRIPTIONS')
                    ->first();
                
                $objOldGlList = DB::table('TBL_MST_GENERALLEDGER')
                    ->where('GLID','=',$objMstCust->GLID_REF)
                    ->select('GLID','GLCODE','GLNAME')
                    ->first();
                
                $objRegCountry= DB::table('TBL_MST_COUNTRY')
                ->where('CTRYID','=',$objMstCust->REGCTRYID_REF)
                ->select('CTRYID','CTRYCODE','NAME')
                ->first();

                $objRegState = DB::table('TBL_MST_STATE')
                ->where('STID','=',$objMstCust->REGSTID_REF)
                ->select('STID','STCODE','NAME')
                ->first();

                $objRegCity = DB::table('TBL_MST_CITY')
                ->where('CITYID','=',$objMstCust->REGCITYID_REF)
                ->select('CITYID','CITYCODE','NAME')
                ->first();
                
                //corporate 
                $objCorpCountry= DB::table('TBL_MST_COUNTRY')
                ->where('CTRYID','=',$objMstCust->CORPCTRYID_REF)
                ->select('CTRYID','CTRYCODE','NAME')
                ->first();

                $objCorpState = DB::table('TBL_MST_STATE')
                ->where('STID','=',$objMstCust->CORPSTID_REF)
                ->select('STID','STCODE','NAME')
                ->first();

                $objCorpCity = DB::table('TBL_MST_CITY')
                ->where('CITYID','=',$objMstCust->CORPCITYID_REF)
                ->select('CITYID','CITYCODE','NAME')
                ->first();

                $objIndType = DB::table('TBL_MST_INDUSTRYTYPE')
                ->where('INDSID','=',$objMstCust->INDSID_REF)
                ->select('INDSID','INDSCODE','DESCRIPTIONS')
                ->first();

                $objIndVer = DB::table('TBL_MST_INDUSTRYVERTICAL')
                ->where('INDSVID','=',$objMstCust->INDSVID_REF)
                ->select('INDSVID','INDSVCODE','DESCRIPTIONS')
                ->first();

                $objPOC = DB::table('TBL_MST_VENDORPOC')                    
                ->where('VID_REF','=',$id)
                ->select('*')
                ->get()->toArray();
                $objPOCCount = count($objPOC);
                if($objPOCCount==0){
                    $objPOCCount=1;
                }

                $objBANK = DB::table('TBL_MST_VENDORBANK')                    
                ->where('VID_REF','=',$id)
                ->select('*')
                ->orderBy('VBANKID','ASC')
                ->get()->toArray();
                $objBANKCount = count($objBANK);
                if($objBANKCount==0){
                    $objBANKCount=1;
                }

                //LOCATION LIST
                $objLOC = DB::table('TBL_MST_VENDORLOCATION')                    
                ->where('TBL_MST_VENDORLOCATION.VID_REF','=',$id)
                ->leftJoin('TBL_MST_COUNTRY','TBL_MST_COUNTRY.CTRYID','=','TBL_MST_VENDORLOCATION.CTRYID_REF')                
                ->leftJoin('TBL_MST_STATE','TBL_MST_STATE.STID','=','TBL_MST_VENDORLOCATION.STID_REF')                
                ->leftJoin('TBL_MST_CITY','TBL_MST_CITY.CITYID','=','TBL_MST_VENDORLOCATION.CITYID_REF')                
                ->select('TBL_MST_VENDORLOCATION.*',
                'TBL_MST_COUNTRY.CTRYID AS COU_CTRYID','TBL_MST_COUNTRY.CTRYCODE AS COU_CTRYCODE','TBL_MST_COUNTRY.NAME AS COU_NAME',
                'TBL_MST_STATE.STID AS STA_STID','TBL_MST_STATE.STCODE AS STA_STCODE','TBL_MST_STATE.NAME AS STA_NAME',
                'TBL_MST_CITY.CITYID AS CIT_CITYID','TBL_MST_CITY.CITYCODE AS CIT_CITYCODE','TBL_MST_CITY.NAME AS CIT_NAME'
                )
                ->orderBy('TBL_MST_VENDORLOCATION.LID','ASC')
                ->get()->toArray();

                $objLOCCount = count($objLOC);
                if($objLOCCount==0){
                    $objLOCCount=1;
                }
               
                $objUDF = DB::table('TBL_MST_VENDOR_UDF')                    
                    ->where('TBL_MST_VENDOR_UDF.VID_REF','=',$id)
                    ->leftJoin('TBL_MST_UDFFOR_VENDOR','TBL_MST_UDFFOR_VENDOR.UDFVID','=','TBL_MST_VENDOR_UDF.UDFVENDORID_REF')                
                    ->select('TBL_MST_VENDOR_UDF.*','TBL_MST_UDFFOR_VENDOR.*')
                    ->orderBy('TBL_MST_VENDOR_UDF.VENDOR_UDFID','ASC')
                    ->get()->toArray();
                    $objudfCount = count($objUDF);                
                    if($objudfCount==0){
                        $objudfCount=1;
                    }

                    $TabSetting =   Helper::getAddSetting(Auth::user()->CYID_REF,'VENDOR_TAB_SETTING');
           
                //dd($objCusGro);

                return view('masters.purchase.vendormaster.mstfrm48edit',compact(['objMstCust','objRights','user_approval_level', 'objudfCount', 'objVendorGroupList', 'objGlList', 'objCountryList', 'objIndTypeList', 'objIndVerList', 'objGstTypeList', 'objCurrencyList',  'objUdfForVendor', 'objCusGro', 'objOldGlList','objRegCountry','objRegState','objRegCity', 'objCorpCountry','objCorpState','objCorpCity', 'objIndType','objIndVer','objPOC','objPOCCount','objBANK','objBANKCount','objLOC','objLOCCount','objUDF','account_type_data','objAssesseeTypeList','TabSetting'
                            ]));
            }

    }//edit function



    public function update(Request $request)
    {

        $VCODE          		=	strtoupper(trim($request['VCODE']));     
        $NAME           		=   trim($request['NAME']); 
		$VENDOR_LEGAL_NAME  	=   trim($request['VENDOR_LEGAL_NAME']); 
		$VGID_REF				=   trim($request['VGID_REF']);
		$OLD_REFCODE 			=   !is_null($request['OLD_REFCODE'])?trim($request['OLD_REFCODE']):NULL;  
		$GLID_REF				=   trim($request['GLID_REF']);
		$REGADDL1				=   trim($request['REGADDL1']);
		$REGADDL2 				=   !is_null($request['REGADDL2'])?trim($request['REGADDL2']):''; 
		$REGCTRYID_REF			=   trim($request['REGCTRYID_REF']);
		$REGSTID_REF			=   trim($request['REGSTID_REF']);
		$REGCITYID_REF			=   trim($request['REGCITYID_REF']);
		$REGPIN 				=   !is_null($request['REGPIN'])?trim($request['REGPIN']):NULL; 
		$CORPADDL1 				=   !is_null($request['CORPADDL1'])?trim($request['CORPADDL1']):NULL;
		$CORPADDL2 				=   !is_null($request['CORPADDL2'])?trim($request['CORPADDL2']):NULL;
		$CORPCTRYID_REF 		=   !is_null($request['CORPCTRYID_REF'])?trim($request['CORPCTRYID_REF']):NULL;
		$CORPSTID_REF 			=   !is_null($request['CORPSTID_REF'])?trim($request['CORPSTID_REF']):NULL;
		$CORPCITYID_REF 		=   !is_null($request['CORPCITYID_REF'])?trim($request['CORPCITYID_REF']):NULL;
		$CORPPIN 				=   !is_null($request['CORPPIN'])?trim($request['CORPPIN']):NULL;
		$EMAILID 				=   !is_null($request['EMAILID'])?trim($request['EMAILID']):NULL;
		$WEBSITE 				=   !is_null($request['WEBSITE'])?trim($request['WEBSITE']):NULL;
		$PHNO 					=   !is_null($request['PHNO'])?trim($request['PHNO']):NULL;
		$MONO 					=   !is_null($request['MONO'])?trim($request['MONO']):NULL;
		$CPNAME 				=   !is_null($request['CPNAME'])?trim($request['CPNAME']):NULL;
		$SKYPEID 				=   !is_null($request['SKYPEID'])?trim($request['SKYPEID']):NULL;
		$INDSID_REF 			=   !is_null($request['INDSID_REF'])?trim($request['INDSID_REF']):NULL;
		$INDSVID_REF 			=   !is_null($request['INDSVID_REF'])?trim($request['INDSVID_REF']):NULL;
		$DEALSIN 				=   !is_null($request['DEALSIN'])?trim($request['DEALSIN']):NULL;  // need to check saving or not
		$GSTTYPE				=   trim($request['GSTTYPE']);
		$DEFCRID_REF			=   trim($request['DEFCRID_REF']);
		$GSTIN 					=   !is_null($request['GSTIN'])?trim($request['GSTIN']):NULL;
		$CREDITLIMIT 			=   !is_null($request['CREDITLIMIT'])?trim($request['CREDITLIMIT']):NULL;
		$CIN 					=   !is_null($request['CIN'])?trim($request['CIN']):NULL;
		$CREDITDAY 				=   !is_null($request['CREDITDAY'])?trim($request['CREDITDAY']):NULL;
		$PANNO 					=   !is_null($request['PANNO'])?trim($request['PANNO']):NULL;
		$EXE_GST 				=   isset( $request['EXE_GST']) &&  (!is_null($request['EXE_GST']) ) ? $request['EXE_GST'] : 0; 

        $MSME_NO 					=   !is_null($request['MSME_NO'])?trim($request['MSME_NO']):NULL;
        $FACTORY_ACT_NO 					=   !is_null($request['FACTORY_ACT_NO'])?trim($request['FACTORY_ACT_NO']):NULL;
        $SAP_VENDOR_CODE 					=   !is_null($request['SAP_VENDOR_CODE'])?trim($request['SAP_VENDOR_CODE']):NULL;
        $SAP_VENDOR_NAME1 					=   !is_null($request['SAP_VENDOR_NAME1'])?trim($request['SAP_VENDOR_NAME1']):NULL;
        $SAP_VENDOR_NAME2 					=   !is_null($request['SAP_VENDOR_NAME2'])?trim($request['SAP_VENDOR_NAME2']):NULL;
        $SAP_VENDOR_NAME3 					=   !is_null($request['SAP_VENDOR_NAME3'])?trim($request['SAP_VENDOR_NAME3']):NULL;
        $SAP_CORPORATE_GROUP 					=   !is_null($request['SAP_CORPORATE_GROUP'])?trim($request['SAP_CORPORATE_GROUP']):NULL;
        $SAP_ACCOUNT_GROUP 					=   !is_null($request['SAP_ACCOUNT_GROUP'])?trim($request['SAP_ACCOUNT_GROUP']):NULL;
        $SAP_ACCOUNT_GROUP_NAME 					=   !is_null($request['SAP_ACCOUNT_GROUP_NAME'])?trim($request['SAP_ACCOUNT_GROUP_NAME']):NULL;
        $SAP_TRADING_PARTNER 					=   !is_null($request['SAP_TRADING_PARTNER'])?trim($request['SAP_TRADING_PARTNER']):NULL;
        $SAP_TRADING_PARTNER_NAME 					=   !is_null($request['SAP_TRADING_PARTNER_NAME'])?trim($request['SAP_TRADING_PARTNER_NAME']):NULL;
        $SAP_INVOCING_PARTY 					=   !is_null($request['SAP_INVOCING_PARTY'])?trim($request['SAP_INVOCING_PARTY']):NULL;
        $OUR_CODE_VBOOK 					=   !is_null($request['OUR_CODE_VBOOK'])?trim($request['OUR_CODE_VBOOK']):NULL;
		
        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;       

        $newDateString = NULL;
        $newdt = !(is_null($request['DODEACTIVATED']) || empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 
        if(!is_null($newdt) ){
            $newdt = str_replace( "/", "-",  $newdt ) ;
            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        $DODEACTIVATED = $newDateString;
        
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF'); 
          
        //POINTOFCONTACT
        $r_count1 = $request['Row_Count1'];
        $pocData =[];
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['POC_NAME_'.$i]) && !is_null($request['POC_NAME_'.$i]) && trim($request['POC_NAME_'.$i]!='')){
                $pocData[$i]['NAME']        =   isset( $request['POC_NAME_'.$i]) &&  (!is_null($request['POC_NAME_'.$i]) )? $request['POC_NAME_'.$i] : ''; 
                $pocData[$i]['DESIG']       =   isset( $request['POC_DESIG_'.$i]) &&  (!is_null($request['POC_DESIG_'.$i]) )? $request['POC_DESIG_'.$i] : ''; 
                $pocData[$i]['MONO']        =   isset( $request['POC_MONO_'.$i]) &&  (!is_null($request['POC_MONO_'.$i]) )? $request['POC_MONO_'.$i] : ''; 
                $pocData[$i]['EMAIL']       =   isset( $request['POC_EMAIL_'.$i]) &&  (!is_null($request['POC_EMAIL_'.$i]) )? $request['POC_EMAIL_'.$i] : ''; 
                $pocData[$i]['LLNO']        =   isset( $request['POC_LLNO_'.$i]) &&  (!is_null($request['POC_LLNO_'.$i]) )? $request['POC_LLNO_'.$i] : ''; 
                $pocData[$i]['AUTHLEVEL']   =   isset( $request['POC_AUTHLEVEL_'.$i]) &&  (!is_null($request['POC_AUTHLEVEL_'.$i]) )? $request['POC_AUTHLEVEL_'.$i] : '';  
                $pocData[$i]['DOB']         =   isset( $request['POC_DOB_'.$i]) &&  (!is_null($request['POC_DOB_'.$i]) )? $request['POC_DOB_'.$i] : NULL; 
            }
        }

        if(count($pocData)>0){            
                $pocwrapped["POINTOFCONTACT"] = $pocData;    
                $poc_xml = ArrayToXml::convert($pocwrapped);
                $XMLPOC = $poc_xml; 
        }else{
                $XMLPOC = NULL;
        }

        //BANK
        $r_count2 = $request['Row_Count2'];
        $bankData= [];
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['BANK_NAME_'.$i]) && !is_null($request['BANK_NAME_'.$i]) && trim($request['BANK_NAME_'.$i]!='')){

                $bankData[$i]['NAME']       =   isset( $request['BANK_NAME_'.$i]) &&  (!is_null($request['BANK_NAME_'.$i]) )? $request['BANK_NAME_'.$i] : ''; 
                $bankData[$i]['IFSC']       =   isset( $request['BANK_IFSC_'.$i]) &&  (!is_null($request['BANK_IFSC_'.$i]) )? $request['BANK_IFSC_'.$i] : ''; 
                $bankData[$i]['BRANCH']     =   isset( $request['BANK_BRANCH_'.$i]) &&  (!is_null($request['BANK_BRANCH_'.$i]) )? $request['BANK_BRANCH_'.$i] : '';  
                $bankData[$i]['ACTYPE']     =   isset( $request['BANK_ACTYPE_'.$i]) &&  (!is_null($request['BANK_ACTYPE_'.$i]) )? $request['BANK_ACTYPE_'.$i] : '';  
                $bankData[$i]['ACNO']       =   isset( $request['BANK_ACNO_'.$i]) &&  (!is_null($request['BANK_ACNO_'.$i]) ) ? $request['BANK_ACNO_'.$i] : '';  
                $bankData[$i]['BYDEFALUT']  =  isset( $request['BYDEFALUT_'.$i]) &&  (!is_null($request['BYDEFALUT_'.$i]) ) ? $request['BYDEFALUT_'.$i] : 0; 
            }  
        }
        if(count($bankData)>0){            
            $bankwrapped["BANK"] = $bankData;    
            $bank_xml = ArrayToXml::convert($bankwrapped);
            $XMLBANK = $bank_xml;
        }else{
            $XMLBANK = NULL;
        }

        //LOCATION
        $r_count3 = $request['Row_Count3'];
        $locationData = [];
        for ($i=0; $i<=$r_count3; $i++){
            if(isset($request['LOC_LADD_'.$i])){
                $locationData[$i]['NAME']       = isset( $request['LOC_NAME_'.$i]) &&  (!is_null($request['LOC_NAME_'.$i]) )? $request['LOC_NAME_'.$i] : ''; 
                $locationData[$i]['LADD']       = isset( $request['LOC_LADD_'.$i]) &&  (!is_null($request['LOC_LADD_'.$i]) )? $request['LOC_LADD_'.$i] : ''; 
                $locationData[$i]['CTRYID_REF']     =  $request['HDNLOC_CTRYID_REF_'.$i];
                $locationData[$i]['STID_REF']       =  $request['HDNLOC_STID_REF_'.$i];
                $locationData[$i]['CITYID_REF']     =  $request['HDNLOC_CITYID_REF_'.$i];
                $locationData[$i]['PIN']            =  $request['LOC_PIN_'.$i];
                $locationData[$i]['GSTIN']          =  $request['LOC_GSTIN_'.$i];
                $locationData[$i]['CPNAME']         =  $request['LOC_CPNAME_'.$i];
                $locationData[$i]['CPDESIGNATION']  =  $request['LOC_CPDESIGNATION_'.$i];
                $locationData[$i]['EMAIL']          =  $request['LOC_EMAIL_'.$i];
                $locationData[$i]['MONO']           =  $request['LOC_MONO_'.$i];
                $locationData[$i]['SPECIAL_INS']  =  $request['LOC_SPINSTRACTION_'.$i];
                $locationData[$i]['BILLTO']         =  isset( $request['LOC_BILLTO_'.$i]) &&  (!is_null($request['LOC_BILLTO_'.$i]) )? $request['LOC_BILLTO_'.$i] : 0; 
                $locationData[$i]['DEFAULT_BILLING'] =  isset( $request['LOC_DEFAULT_BILLTO_'.$i]) &&  (!is_null($request['LOC_DEFAULT_BILLTO_'.$i]) ) ? $request['LOC_DEFAULT_BILLTO_'.$i] : 0; 
                $locationData[$i]['SHIPTO']         =  isset( $request['LOC_SHIPTO_'.$i]) &&  (!is_null($request['LOC_SHIPTO_'.$i]) )? $request['LOC_SHIPTO_'.$i] : 0;  
                $locationData[$i]['DEFAULT_SHIPPING'] =  isset( $request['LOC_DEFAULT_SHIPTO_'.$i]) &&  (!is_null($request['LOC_DEFAULT_SHIPTO_'.$i]) )? $request['LOC_DEFAULT_SHIPTO_'.$i] : 0;  
            }  
        }

        if(count($locationData)>0){            
            $locationwrapped["LOCATION"] = $locationData;    
            $location_xml = ArrayToXml::convert($locationwrapped);
            $XMLLOCATION = $location_xml;
        }else{
            $XMLLOCATION = NULL;
        }

        //UDF FIELDS
        $r_count4 = $request['Row_Count4'];
        $udffield_Data = [];
        for ($i=0; $i<=$r_count4; $i++)
        {
            // $udffield_request = isset( $request['udffie_'.$i]) &&  (!is_null($request['udffie_'.$i]) )? $request['udffie_'.$i] : '';
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFVENDORID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
            }
        }  
 
        if(count($udffield_Data)>0){            
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
 
        }else{
                $XMLUDF = NULL;
        }
 
        $VTID 	= $this->vtid_ref;
        $USERID = Auth::user()->USERID;
        $UPDATE ="ADD";
        $UPDATE =  Date('Y-m-d');
        $UPTIME = Date('h:i:s.u');
 
        $ACTION     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();

        $TDS_APPLICABLE         =   (isset($request['TDS_APPLICABLE']) )? 1 : 0 ; 
        $CERTIFICATE_NO 	    =   isset($request['CERTIFICATE_NO']) && !is_null($request['CERTIFICATE_NO'])?trim($request['CERTIFICATE_NO']):NULL;
        $EXPIRY_DT 				=   isset($request['EXPIRY_DT']) && !is_null($request['EXPIRY_DT'])?trim($request['EXPIRY_DT']):NULL;
        $ASSESSEEID_REF 		=   isset($request['ASSESSEEID_REF']) && !is_null($request['ASSESSEEID_REF'])?trim($request['ASSESSEEID_REF']):NULL;
        $HOLDINGID_REF 			=   isset($request['HOLDINGID_REF']) && !is_null($request['HOLDINGID_REF'])?trim($request['HOLDINGID_REF']):NULL;
        $CHA                    =   (isset($request['CHA'])!="true" ? 0 : 1);

 
		$save_data = [
            $VCODE,         $NAME,          $VGID_REF,      $OLD_REFCODE,       $GLID_REF,
            $REGADDL1,      $REGADDL2,      $REGCTRYID_REF, $REGSTID_REF,       $REGCITYID_REF,
            $REGPIN,        $CORPADDL1,     $CORPADDL2,     $CORPCTRYID_REF,    $CORPSTID_REF, 
            $CORPCITYID_REF,$CORPPIN,       $EMAILID,       $WEBSITE,           $PHNO,
            $MONO,          $CPNAME,        $SKYPEID,       $INDSID_REF,        $INDSVID_REF,
            $DEALSIN,       $DEFCRID_REF,   $GSTTYPE,       $GSTIN,             $CIN,
            $PANNO,         $CREDITLIMIT,   $CREDITDAY,     $VENDOR_LEGAL_NAME, $EXE_GST,
            $DEACTIVATED,   $DODEACTIVATED, $TDS_APPLICABLE,$ASSESSEEID_REF,    $HOLDINGID_REF,
            $CERTIFICATE_NO,$EXPIRY_DT,   $MSME_NO,       $FACTORY_ACT_NO,     $SAP_VENDOR_CODE,
            $SAP_VENDOR_NAME1,$SAP_VENDOR_NAME2, $SAP_VENDOR_NAME3,             $SAP_CORPORATE_GROUP,
            $SAP_ACCOUNT_GROUP  , $SAP_ACCOUNT_GROUP_NAME,  $SAP_TRADING_PARTNER, $SAP_TRADING_PARTNER_NAME,
            $SAP_INVOCING_PARTY ,$OUR_CODE_VBOOK,
            
            $CYID_REF,      $BRID_REF,          $FYID_REF,
            $XMLLOCATION,   $XMLBANK,       $XMLPOC,        $XMLUDF,            $VTID,
            $USERID,        $UPDATE,        $UPTIME,        $ACTION,            $IPADDRESS, $CHA
        ];
        		
		$sp_result = DB::select('EXEC SP_VENDOR_UP ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?', $save_data);
				
  
				
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
           
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','resp'=>'duplicate']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }
        
        exit();    
              
    } // update function


     //singleApprove begin
    public function singleapprove(Request $request)
    {

        $VCODE          		=	strtoupper(trim($request['VCODE']));     
        $NAME           		=   trim($request['NAME']); 
		$VENDOR_LEGAL_NAME  	=   trim($request['VENDOR_LEGAL_NAME']); 
		$VGID_REF				=   trim($request['VGID_REF']);
		$OLD_REFCODE 			=   !is_null($request['OLD_REFCODE'])?trim($request['OLD_REFCODE']):NULL;  
		$GLID_REF				=   trim($request['GLID_REF']);
		$REGADDL1				=   trim($request['REGADDL1']);
		$REGADDL2 				=   !is_null($request['REGADDL2'])?trim($request['REGADDL2']):''; 
		$REGCTRYID_REF			=   trim($request['REGCTRYID_REF']);
		$REGSTID_REF			=   trim($request['REGSTID_REF']);
		$REGCITYID_REF			=   trim($request['REGCITYID_REF']);
		$REGPIN 				=   !is_null($request['REGPIN'])?trim($request['REGPIN']):NULL; 
		$CORPADDL1 				=   !is_null($request['CORPADDL1'])?trim($request['CORPADDL1']):NULL;
		$CORPADDL2 				=   !is_null($request['CORPADDL2'])?trim($request['CORPADDL2']):NULL;
		$CORPCTRYID_REF 		=   !is_null($request['CORPCTRYID_REF'])?trim($request['CORPCTRYID_REF']):NULL;
		$CORPSTID_REF 			=   !is_null($request['CORPSTID_REF'])?trim($request['CORPSTID_REF']):NULL;
		$CORPCITYID_REF 		=   !is_null($request['CORPCITYID_REF'])?trim($request['CORPCITYID_REF']):NULL;
		$CORPPIN 				=   !is_null($request['CORPPIN'])?trim($request['CORPPIN']):NULL;
		$EMAILID 				=   !is_null($request['EMAILID'])?trim($request['EMAILID']):NULL;
		$WEBSITE 				=   !is_null($request['WEBSITE'])?trim($request['WEBSITE']):NULL;
		$PHNO 					=   !is_null($request['PHNO'])?trim($request['PHNO']):NULL;
		$MONO 					=   !is_null($request['MONO'])?trim($request['MONO']):NULL;
		$CPNAME 				=   !is_null($request['CPNAME'])?trim($request['CPNAME']):NULL;
		$SKYPEID 				=   !is_null($request['SKYPEID'])?trim($request['SKYPEID']):NULL;
		$INDSID_REF 			=   !is_null($request['INDSID_REF'])?trim($request['INDSID_REF']):NULL;
		$INDSVID_REF 			=   !is_null($request['INDSVID_REF'])?trim($request['INDSVID_REF']):NULL;
		$DEALSIN 				=   !is_null($request['DEALSIN'])?trim($request['DEALSIN']):NULL;  // need to check saving or not
		$GSTTYPE				=   trim($request['GSTTYPE']);
		$DEFCRID_REF			=   trim($request['DEFCRID_REF']);
		$GSTIN 					=   !is_null($request['GSTIN'])?trim($request['GSTIN']):NULL;
		$CREDITLIMIT 			=   !is_null($request['CREDITLIMIT'])?trim($request['CREDITLIMIT']):NULL;
		$CIN 					=   !is_null($request['CIN'])?trim($request['CIN']):NULL;
		$CREDITDAY 				=   !is_null($request['CREDITDAY'])?trim($request['CREDITDAY']):NULL;
		$PANNO 					=   !is_null($request['PANNO'])?trim($request['PANNO']):NULL;
		$EXE_GST 				=   isset( $request['EXE_GST']) &&  (!is_null($request['EXE_GST']) ) ? $request['EXE_GST'] : 0; 

        $MSME_NO 					=   !is_null($request['MSME_NO'])?trim($request['MSME_NO']):NULL;
        $FACTORY_ACT_NO 					=   !is_null($request['FACTORY_ACT_NO'])?trim($request['FACTORY_ACT_NO']):NULL;
        $SAP_VENDOR_CODE 					=   !is_null($request['SAP_VENDOR_CODE'])?trim($request['SAP_VENDOR_CODE']):NULL;
        $SAP_VENDOR_NAME1 					=   !is_null($request['SAP_VENDOR_NAME1'])?trim($request['SAP_VENDOR_NAME1']):NULL;
        $SAP_VENDOR_NAME2 					=   !is_null($request['SAP_VENDOR_NAME2'])?trim($request['SAP_VENDOR_NAME2']):NULL;
        $SAP_VENDOR_NAME3 					=   !is_null($request['SAP_VENDOR_NAME3'])?trim($request['SAP_VENDOR_NAME3']):NULL;
        $SAP_CORPORATE_GROUP 					=   !is_null($request['SAP_CORPORATE_GROUP'])?trim($request['SAP_CORPORATE_GROUP']):NULL;
        $SAP_ACCOUNT_GROUP 					=   !is_null($request['SAP_ACCOUNT_GROUP'])?trim($request['SAP_ACCOUNT_GROUP']):NULL;
        $SAP_ACCOUNT_GROUP_NAME 					=   !is_null($request['SAP_ACCOUNT_GROUP_NAME'])?trim($request['SAP_ACCOUNT_GROUP_NAME']):NULL;
        $SAP_TRADING_PARTNER 					=   !is_null($request['SAP_TRADING_PARTNER'])?trim($request['SAP_TRADING_PARTNER']):NULL;
        $SAP_TRADING_PARTNER_NAME 					=   !is_null($request['SAP_TRADING_PARTNER_NAME'])?trim($request['SAP_TRADING_PARTNER_NAME']):NULL;
        $SAP_INVOCING_PARTY 					=   !is_null($request['SAP_INVOCING_PARTY'])?trim($request['SAP_INVOCING_PARTY']):NULL;
        $OUR_CODE_VBOOK 					=   !is_null($request['OUR_CODE_VBOOK'])?trim($request['OUR_CODE_VBOOK']):NULL;
		
        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;       

        $newDateString = NULL;
        $newdt = !(is_null($request['DODEACTIVATED']) || empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 
        if(!is_null($newdt) ){
            $newdt = str_replace( "/", "-",  $newdt ) ;
            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        $DODEACTIVATED = $newDateString;
        
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF'); 
          
        //POINTOFCONTACT
        $r_count1 = $request['Row_Count1'];
        $pocData =[];
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['POC_NAME_'.$i]) && !is_null($request['POC_NAME_'.$i]) && trim($request['POC_NAME_'.$i]!='')){
                $pocData[$i]['NAME']        =   isset( $request['POC_NAME_'.$i]) &&  (!is_null($request['POC_NAME_'.$i]) )? $request['POC_NAME_'.$i] : ''; 
                $pocData[$i]['DESIG']       =   isset( $request['POC_DESIG_'.$i]) &&  (!is_null($request['POC_DESIG_'.$i]) )? $request['POC_DESIG_'.$i] : ''; 
                $pocData[$i]['MONO']        =   isset( $request['POC_MONO_'.$i]) &&  (!is_null($request['POC_MONO_'.$i]) )? $request['POC_MONO_'.$i] : ''; 
                $pocData[$i]['EMAIL']       =   isset( $request['POC_EMAIL_'.$i]) &&  (!is_null($request['POC_EMAIL_'.$i]) )? $request['POC_EMAIL_'.$i] : ''; 
                $pocData[$i]['LLNO']        =   isset( $request['POC_LLNO_'.$i]) &&  (!is_null($request['POC_LLNO_'.$i]) )? $request['POC_LLNO_'.$i] : ''; 
                $pocData[$i]['AUTHLEVEL']   =   isset( $request['POC_AUTHLEVEL_'.$i]) &&  (!is_null($request['POC_AUTHLEVEL_'.$i]) )? $request['POC_AUTHLEVEL_'.$i] : '';  
                $pocData[$i]['DOB']         =   isset( $request['POC_DOB_'.$i]) &&  (!is_null($request['POC_DOB_'.$i]) )? $request['POC_DOB_'.$i] : NULL; 
            }
        }

        if(count($pocData)>0){            
                $pocwrapped["POINTOFCONTACT"] = $pocData;    
                $poc_xml = ArrayToXml::convert($pocwrapped);
                $XMLPOC = $poc_xml; 
        }else{
                $XMLPOC = NULL;
        }

        //BANK
        $r_count2 = $request['Row_Count2'];
        $bankData= [];
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['BANK_NAME_'.$i]) && !is_null($request['BANK_NAME_'.$i]) && trim($request['BANK_NAME_'.$i]!='')){

                $bankData[$i]['NAME']       =   isset( $request['BANK_NAME_'.$i]) &&  (!is_null($request['BANK_NAME_'.$i]) )? $request['BANK_NAME_'.$i] : ''; 
                $bankData[$i]['IFSC']       =   isset( $request['BANK_IFSC_'.$i]) &&  (!is_null($request['BANK_IFSC_'.$i]) )? $request['BANK_IFSC_'.$i] : ''; 
                $bankData[$i]['BRANCH']     =   isset( $request['BANK_BRANCH_'.$i]) &&  (!is_null($request['BANK_BRANCH_'.$i]) )? $request['BANK_BRANCH_'.$i] : '';  
                $bankData[$i]['ACTYPE']     =   isset( $request['BANK_ACTYPE_'.$i]) &&  (!is_null($request['BANK_ACTYPE_'.$i]) )? $request['BANK_ACTYPE_'.$i] : '';  
                $bankData[$i]['ACNO']       =   isset( $request['BANK_ACNO_'.$i]) &&  (!is_null($request['BANK_ACNO_'.$i]) ) ? $request['BANK_ACNO_'.$i] : '';  
                $bankData[$i]['BYDEFALUT']  =  isset( $request['BYDEFALUT_'.$i]) &&  (!is_null($request['BYDEFALUT_'.$i]) ) ? $request['BYDEFALUT_'.$i] : 0; 
            }  
        }
        if(count($bankData)>0){            
            $bankwrapped["BANK"] = $bankData;    
            $bank_xml = ArrayToXml::convert($bankwrapped);
            $XMLBANK = $bank_xml;
        }else{
            $XMLBANK = NULL;
        }

        //LOCATION
        $r_count3 = $request['Row_Count3'];
        $locationData = [];
        for ($i=0; $i<=$r_count3; $i++){
            if(isset($request['LOC_LADD_'.$i])){
                $locationData[$i]['NAME']       = isset( $request['LOC_NAME_'.$i]) &&  (!is_null($request['LOC_NAME_'.$i]) )? $request['LOC_NAME_'.$i] : ''; 
                $locationData[$i]['LADD']       = isset( $request['LOC_LADD_'.$i]) &&  (!is_null($request['LOC_LADD_'.$i]) )? $request['LOC_LADD_'.$i] : ''; 
                $locationData[$i]['CTRYID_REF']     =  $request['HDNLOC_CTRYID_REF_'.$i];
                $locationData[$i]['STID_REF']       =  $request['HDNLOC_STID_REF_'.$i];
                $locationData[$i]['CITYID_REF']     =  $request['HDNLOC_CITYID_REF_'.$i];
                $locationData[$i]['PIN']            =  $request['LOC_PIN_'.$i];
                $locationData[$i]['GSTIN']          =  $request['LOC_GSTIN_'.$i];
                $locationData[$i]['CPNAME']         =  $request['LOC_CPNAME_'.$i];
                $locationData[$i]['CPDESIGNATION']  =  $request['LOC_CPDESIGNATION_'.$i];
                $locationData[$i]['EMAIL']          =  $request['LOC_EMAIL_'.$i];
                $locationData[$i]['MONO']           =  $request['LOC_MONO_'.$i];
                $locationData[$i]['SPECIAL_INS']  =  $request['LOC_SPINSTRACTION_'.$i];
                $locationData[$i]['BILLTO']         =  isset( $request['LOC_BILLTO_'.$i]) &&  (!is_null($request['LOC_BILLTO_'.$i]) )? $request['LOC_BILLTO_'.$i] : 0; 
                $locationData[$i]['DEFAULT_BILLING'] =  isset( $request['LOC_DEFAULT_BILLTO_'.$i]) &&  (!is_null($request['LOC_DEFAULT_BILLTO_'.$i]) ) ? $request['LOC_DEFAULT_BILLTO_'.$i] : 0; 
                $locationData[$i]['SHIPTO']         =  isset( $request['LOC_SHIPTO_'.$i]) &&  (!is_null($request['LOC_SHIPTO_'.$i]) )? $request['LOC_SHIPTO_'.$i] : 0;  
                $locationData[$i]['DEFAULT_SHIPPING'] =  isset( $request['LOC_DEFAULT_SHIPTO_'.$i]) &&  (!is_null($request['LOC_DEFAULT_SHIPTO_'.$i]) )? $request['LOC_DEFAULT_SHIPTO_'.$i] : 0;  
            }  
        }

        if(count($locationData)>0){            
            $locationwrapped["LOCATION"] = $locationData;    
            $location_xml = ArrayToXml::convert($locationwrapped);
            $XMLLOCATION = $location_xml;
        }else{
            $XMLLOCATION = NULL;
        }

        //UDF FIELDS
        $r_count4 = $request['Row_Count4'];
        $udffield_Data = [];
        for ($i=0; $i<=$r_count4; $i++)
        {
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFVENDORID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
            }
        }  
 
        if(count($udffield_Data)>0){            
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
 
        }else{
                $XMLUDF = NULL;
        }
 
        $VTID 	= $this->vtid_ref;
        $USERID = Auth::user()->USERID;
        $UPDATE ="ADD";
        $UPDATE =  Date('Y-m-d');
        $UPTIME = Date('h:i:s.u');
 
        $sp_Approvallevel = [
            $USERID, $VTID, $CYID_REF,$BRID_REF,
            $FYID_REF
            
        ];
        
        $sp_listing_result = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?, ?', $sp_Approvallevel);
    
        if(!empty($sp_listing_result))
        {
            foreach ($sp_listing_result as $key=>$approw)
            {  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$approw->LAVELS;
            }
        }
 
        $ACTION     =  $Approvallevel;
        $IPADDRESS  =  $request->getClientIp();
 
		$TDS_APPLICABLE         =   (isset($request['TDS_APPLICABLE']) )? 1 : 0 ; 
        $CERTIFICATE_NO 	    =   isset($request['CERTIFICATE_NO']) && !is_null($request['CERTIFICATE_NO'])?trim($request['CERTIFICATE_NO']):NULL;
        $EXPIRY_DT 				=   isset($request['EXPIRY_DT']) && !is_null($request['EXPIRY_DT'])?trim($request['EXPIRY_DT']):NULL;
        $ASSESSEEID_REF 		=   isset($request['ASSESSEEID_REF']) && !is_null($request['ASSESSEEID_REF'])?trim($request['ASSESSEEID_REF']):NULL;
        $HOLDINGID_REF 			=   isset($request['HOLDINGID_REF']) && !is_null($request['HOLDINGID_REF'])?trim($request['HOLDINGID_REF']):NULL;
        $CHA                    =   (isset($request['CHA'])!="true" ? 0 : 1);

		$save_data = [
            $VCODE,         $NAME,          $VGID_REF,      $OLD_REFCODE,       $GLID_REF,
            $REGADDL1,      $REGADDL2,      $REGCTRYID_REF, $REGSTID_REF,       $REGCITYID_REF,
            $REGPIN,        $CORPADDL1,     $CORPADDL2,     $CORPCTRYID_REF,    $CORPSTID_REF, 
            $CORPCITYID_REF,$CORPPIN,       $EMAILID,       $WEBSITE,           $PHNO,
            $MONO,          $CPNAME,        $SKYPEID,       $INDSID_REF,        $INDSVID_REF,
            $DEALSIN,       $DEFCRID_REF,   $GSTTYPE,       $GSTIN,             $CIN,
            $PANNO,         $CREDITLIMIT,   $CREDITDAY,     $VENDOR_LEGAL_NAME, $EXE_GST,
            $DEACTIVATED,   $DODEACTIVATED, $TDS_APPLICABLE,$ASSESSEEID_REF,    $HOLDINGID_REF,
            $CERTIFICATE_NO,$EXPIRY_DT,     $MSME_NO,       $FACTORY_ACT_NO,     $SAP_VENDOR_CODE,
            $SAP_VENDOR_NAME1,$SAP_VENDOR_NAME2, $SAP_VENDOR_NAME3,             $SAP_CORPORATE_GROUP,
            $SAP_ACCOUNT_GROUP  , $SAP_ACCOUNT_GROUP_NAME,  $SAP_TRADING_PARTNER, $SAP_TRADING_PARTNER_NAME,
            $SAP_INVOCING_PARTY ,$OUR_CODE_VBOOK,           $CYID_REF,      $BRID_REF,          $FYID_REF,
            $XMLLOCATION,   $XMLBANK,       $XMLPOC,        $XMLUDF,            $VTID,
            $USERID,        $UPDATE,        $UPTIME,        $ACTION,            $IPADDRESS, $CHA
        ];
        		
		$sp_result = DB::select('EXEC SP_VENDOR_UP ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?', $save_data);
				
  
                
       // dd($sp_result);
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully approved.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
           
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','resp'=>'duplicate']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }
        
        exit();   

    }//singleApprove end
 
 
    public function view($id){

        if(!is_null($id))
        {
            $status  ='A';
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');

            $account_type_data = explode(",",config("erpconst.bank.account_type"));

            $objAssesseeTypeList    =$this->AssesseeTypeList();

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',$BRID_REF)
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $objGlList = DB::table('TBL_MST_GENERALLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('STATUS','=','A')
            ->where('SUBLEDGER','=',1)
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('GLID','GLCODE','GLNAME')
            ->get();
    
            
            $objCountryList = DB::table('TBL_MST_COUNTRY')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('CTRYID','CTRYCODE','NAME')
            ->get();
    
            $objIndTypeList = DB::table('TBL_MST_INDUSTRYTYPE')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('INDSID','INDSCODE','DESCRIPTIONS')
            ->get();
    
            $objIndVerList = DB::table('TBL_MST_INDUSTRYVERTICAL')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('INDSVID','INDSVCODE','DESCRIPTIONS')
            ->get();
    
    
            $GSTdata = ['GSTID','GSTCODE','DESCRIPTIONS'];
            $objGstTypeList       = Helper::getTableData('TBL_MST_GSTTYPE',$GSTdata,NULL, NULL, NULL,'GSTCODE','ASC');
        
    
            $objCurrencyList = DB::table('TBL_MST_CURRENCY')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('CRID','CRCODE','CRDESCRIPTION')
            ->get();
    
            $objCurrencyList =  DB::select('SELECT CRID,CRCODE,CRDESCRIPTION FROM TBL_MST_CURRENCY  WHERE  ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ? order by CRCODE ASC',['A']);
    
            $objUdfForVendor       = Helper::getUdfForVendor(  Auth::user()->CYID_REF);

            $data1 = ['VGID','VGCODE','DESCRIPTIONS'];
            $objVendorGroupList       = Helper::getTableData('TBL_MST_VENDORGROUP',$data1, Auth::user()->CYID_REF, NULL, NULL,'VGCODE','ASC');
    

            $objMstCust = DB::table('TBL_MST_VENDOR')                    
                ->where('TBL_MST_VENDOR.VID','=',$id)
                ->where('TBL_MST_VENDOR.CYID_REF','=',$CYID_REF)
                ->where('TBL_MST_VENDOR.BRID_REF','=',$BRID_REF)
                ->select('TBL_MST_VENDOR.*')
                ->first();
            

            $objCusGro = DB::table('TBL_MST_VENDORGROUP')                    
                ->where('VGID','=',$objMstCust->VGID_REF)
                ->select('VGID','VGCODE','DESCRIPTIONS')
                ->first();
            
            $objOldGlList = DB::table('TBL_MST_GENERALLEDGER')
                ->where('GLID','=',$objMstCust->GLID_REF)
                ->select('GLID','GLCODE','GLNAME')
                ->first();
            
            $objRegCountry= DB::table('TBL_MST_COUNTRY')
            ->where('CTRYID','=',$objMstCust->REGCTRYID_REF)
            ->select('CTRYID','CTRYCODE','NAME')
            ->first();

            $objRegState = DB::table('TBL_MST_STATE')
            ->where('STID','=',$objMstCust->REGSTID_REF)
            ->select('STID','STCODE','NAME')
            ->first();

            $objRegCity = DB::table('TBL_MST_CITY')
            ->where('CITYID','=',$objMstCust->REGCITYID_REF)
            ->select('CITYID','CITYCODE','NAME')
            ->first();
            
            //corporate 
            $objCorpCountry= DB::table('TBL_MST_COUNTRY')
            ->where('CTRYID','=',$objMstCust->CORPCTRYID_REF)
            ->select('CTRYID','CTRYCODE','NAME')
            ->first();

            $objCorpState = DB::table('TBL_MST_STATE')
            ->where('STID','=',$objMstCust->CORPSTID_REF)
            ->select('STID','STCODE','NAME')
            ->first();

            $objCorpCity = DB::table('TBL_MST_CITY')
            ->where('CITYID','=',$objMstCust->CORPCITYID_REF)
            ->select('CITYID','CITYCODE','NAME')
            ->first();

            $objIndType = DB::table('TBL_MST_INDUSTRYTYPE')
            ->where('INDSID','=',$objMstCust->INDSID_REF)
            ->select('INDSID','INDSCODE','DESCRIPTIONS')
            ->first();

            $objIndVer = DB::table('TBL_MST_INDUSTRYVERTICAL')
            ->where('INDSVID','=',$objMstCust->INDSVID_REF)
            ->select('INDSVID','INDSVCODE','DESCRIPTIONS')
            ->first();

            $objPOC = DB::table('TBL_MST_VENDORPOC')                    
            ->where('VID_REF','=',$id)
            ->select('*')
            ->get()->toArray();
            $objPOCCount = count($objPOC);
            if($objPOCCount==0){
                $objPOCCount=1;
            }

            $objBANK = DB::table('TBL_MST_VENDORBANK')                    
            ->where('VID_REF','=',$id)
            ->select('*')
            ->orderBy('VBANKID','ASC')
            ->get()->toArray();
            $objBANKCount = count($objBANK);
            if($objBANKCount==0){
                $objBANKCount=1;
            }

            //LOCATION LIST
            $objLOC = DB::table('TBL_MST_VENDORLOCATION')                    
            ->where('TBL_MST_VENDORLOCATION.VID_REF','=',$id)
            ->leftJoin('TBL_MST_COUNTRY','TBL_MST_COUNTRY.CTRYID','=','TBL_MST_VENDORLOCATION.CTRYID_REF')                
            ->leftJoin('TBL_MST_STATE','TBL_MST_STATE.STID','=','TBL_MST_VENDORLOCATION.STID_REF')                
            ->leftJoin('TBL_MST_CITY','TBL_MST_CITY.CITYID','=','TBL_MST_VENDORLOCATION.CITYID_REF')                
            ->select('TBL_MST_VENDORLOCATION.*',
            'TBL_MST_COUNTRY.CTRYID AS COU_CTRYID','TBL_MST_COUNTRY.CTRYCODE AS COU_CTRYCODE','TBL_MST_COUNTRY.NAME AS COU_NAME',
            'TBL_MST_STATE.STID AS STA_STID','TBL_MST_STATE.STCODE AS STA_STCODE','TBL_MST_STATE.NAME AS STA_NAME',
            'TBL_MST_CITY.CITYID AS CIT_CITYID','TBL_MST_CITY.CITYCODE AS CIT_CITYCODE','TBL_MST_CITY.NAME AS CIT_NAME'
            )
            ->orderBy('TBL_MST_VENDORLOCATION.LID','ASC')
            ->get()->toArray();

            $objLOCCount = count($objLOC);
            if($objLOCCount==0){
                $objLOCCount=1;
            }
           
            $objUDF = DB::table('TBL_MST_VENDOR_UDF')                    
                ->where('TBL_MST_VENDOR_UDF.VID_REF','=',$id)
                ->leftJoin('TBL_MST_UDFFOR_VENDOR','TBL_MST_UDFFOR_VENDOR.UDFVID','=','TBL_MST_VENDOR_UDF.UDFVENDORID_REF')                
                ->select('TBL_MST_VENDOR_UDF.*','TBL_MST_UDFFOR_VENDOR.*')
                ->orderBy('TBL_MST_VENDOR_UDF.VENDOR_UDFID','ASC')
                ->get()->toArray();
                $objudfCount = count($objUDF);                
                if($objudfCount==0){
                    $objudfCount=1;
                }

                $TabSetting =   Helper::getAddSetting(Auth::user()->CYID_REF,'VENDOR_TAB_SETTING');
       
            //dd($objCusGro);

            return view('masters.purchase.vendormaster.mstfrm48view',compact(['objMstCust','objRights','user_approval_level', 'objudfCount', 'objVendorGroupList', 'objGlList', 'objCountryList', 'objIndTypeList', 'objIndVerList', 'objGstTypeList', 'objCurrencyList',  'objUdfForVendor', 'objCusGro', 'objOldGlList','objRegCountry','objRegState','objRegCity', 'objCorpCountry','objCorpState','objCorpCity', 'objIndType','objIndVer','objPOC','objPOCCount','objBANK','objBANKCount','objLOC','objLOCCount','objUDF','account_type_data','account_type_data','objAssesseeTypeList','TabSetting'
                        ]));
        }
             
        
    }//view function 
    
  
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

           // dump($req_data);
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
            $TABLE      =   "TBL_MST_VENDOR";
            $FIELD      =   "VID";
            $ACTIONNAME     = $Approvallevel;
            $UPDATE     =   Date('Y-m-d');
            $UPTIME     =   Date('h:i:s.u');
            $IPADDRESS  =   $request->getClientIp();
        

        
        $log_data = [ 
            $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        ];

            
        $sp_result = DB::select('EXEC SP_MST_MULTIAPPROVAL_VENDOR ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
        
        

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
         $TABLE      =   "TBL_MST_VENDOR";
         $FIELD      =   "VID";
         $ID         =   $id;
         $UPDATE     =   Date('Y-m-d');
         $UPTIME     =   Date('h:i:s.u');
         $IPADDRESS  =   $request->getClientIp();

         

         $canceldata[0]=[
            'NT'  => 'TBL_MST_VENDORLOCATION',
        ];
        $canceldata[1]=[
            'NT'  => 'TBL_MST_VENDORBANK',
        ];
        $canceldata[2]=[
            'NT'  => 'TBL_MST_VENDORPOC',
        ];
        $canceldata[3]=[
            'NT'  => 'TBL_MST_VENDOR_UDF',
        ];

        $links["TABLES"] = $canceldata; 
        $cancelxml = ArrayToXml::convert($links);        
         
         
         
         $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME,$IPADDRESS,$cancelxml  ];
 
         
         $sp_result = DB::select('EXEC SP_MST_CANCEL  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);
         
         //dump($sp_result);
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


     public function AssesseeTypeList(){
        return  DB::table('TBL_MST_NATUAREOF_ASSESSEE')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('STATUS','=','A')
                ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                ->select('NOAID','NOA_CODE','NOA_NAME')
                ->get();
    }

    public function TdsCodeList(Request $request){
        
        $DataArr=DB::table('TBL_MST_WITHHOLDING')
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('ASSESSEEID_REF','=',$request['ASSESSEEID_REF'])
               // ->where('STATUS','=','A')
                ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
                ->select('HOLDINGID','CODE','CODE_DESC')
                ->get();

                $itemData=array();

                if(isset($request['VALUE']) && $request['VALUE'] !=""){
                    $itemData   =   explode(",",$request['VALUE']);
                }

            if(COUNT($DataArr) > 0){
                foreach($DataArr as $row){
                    $checked=in_array($row->HOLDINGID, $itemData)?'checked':'';
                
                    echo '<tr>
                    <td width="20%" align="center"><input type="checkbox" name="selectAll[]" class="cls_HOLDINGID_REF" value="HOLDINGID_REF_'.$row->HOLDINGID.'" '.$checked.' ></td>
                    <td width="40%">'.$row->CODE.'
                    <input type="hidden" id="txtHOLDINGID_REF_'.$row->HOLDINGID.'" data-desc="'.$row->CODE.' - '.$row->CODE_DESC.'" value="'.$row->HOLDINGID.'" />
                    </td>
                    <td width="40%">'.$row->CODE_DESC.'</td>
                    </tr>';

                   
                }
            }
            else{
                echo '<tr><td colspan="3">Record not found.</td></tr>';
            }
            exit();

    }

    public function amendment($id){
        if(!is_null($id)){

            $status     =   'A';
            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');

            $account_type_data = explode(",",config("erpconst.bank.account_type"));

            $objAssesseeTypeList    =$this->AssesseeTypeList();

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            //get user approval data
            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',$BRID_REF)
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $objGlList = DB::table('TBL_MST_GENERALLEDGER')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('STATUS','=','A')
            ->where('SUBLEDGER','=',1)
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('GLID','GLCODE','GLNAME')
            ->get();
    
                
            $objCountryList = DB::table('TBL_MST_COUNTRY')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('CTRYID','CTRYCODE','NAME')
            ->get();
        
            $objIndTypeList = DB::table('TBL_MST_INDUSTRYTYPE')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('INDSID','INDSCODE','DESCRIPTIONS')
            ->get();
        
            $objIndVerList = DB::table('TBL_MST_INDUSTRYVERTICAL')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('INDSVID','INDSVCODE','DESCRIPTIONS')
            ->get();
        
        
            $GSTdata = ['GSTID','GSTCODE','DESCRIPTIONS'];
            $objGstTypeList       = Helper::getTableData('TBL_MST_GSTTYPE',$GSTdata,NULL, NULL, NULL,'GSTCODE','ASC');
        
        
            $objCurrencyList = DB::table('TBL_MST_CURRENCY')
            ->where('STATUS','=','A')
            ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
            ->select('CRID','CRCODE','CRDESCRIPTION')
            ->get();
        
            $objCurrencyList =  DB::select('SELECT CRID,CRCODE,CRDESCRIPTION FROM TBL_MST_CURRENCY  WHERE  ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ? order by CRCODE ASC',['A']);
        
            $objUdfForVendor = Helper::getUdfForVendor(  Auth::user()->CYID_REF);

            $data1 = ['VGID','VGCODE','DESCRIPTIONS'];
            $objVendorGroupList       = Helper::getTableData('TBL_MST_VENDORGROUP',$data1, Auth::user()->CYID_REF, NULL, NULL,'VGCODE','ASC');
    

            $objMstCust = DB::table('TBL_MST_VENDOR')                    
            ->where('TBL_MST_VENDOR.VID','=',$id)
            ->where('TBL_MST_VENDOR.CYID_REF','=',$CYID_REF)
            ->where('TBL_MST_VENDOR.BRID_REF','=',$BRID_REF)
            ->select('TBL_MST_VENDOR.*')
            ->first();

            $objCusGro = DB::table('TBL_MST_VENDORGROUP')                    
            ->where('VGID','=',$objMstCust->VGID_REF)
            ->select('VGID','VGCODE','DESCRIPTIONS')
            ->first();
            
            $objOldGlList = DB::table('TBL_MST_GENERALLEDGER')
            ->where('GLID','=',$objMstCust->GLID_REF)
            ->select('GLID','GLCODE','GLNAME')
            ->first();
            
            $objRegCountry= DB::table('TBL_MST_COUNTRY')
            ->where('CTRYID','=',$objMstCust->REGCTRYID_REF)
            ->select('CTRYID','CTRYCODE','NAME')
            ->first();

            $objRegState = DB::table('TBL_MST_STATE')
            ->where('STID','=',$objMstCust->REGSTID_REF)
            ->select('STID','STCODE','NAME')
            ->first();

            $objRegCity = DB::table('TBL_MST_CITY')
            ->where('CITYID','=',$objMstCust->REGCITYID_REF)
            ->select('CITYID','CITYCODE','NAME')
            ->first();
            
            //corporate 
            $objCorpCountry= DB::table('TBL_MST_COUNTRY')
            ->where('CTRYID','=',$objMstCust->CORPCTRYID_REF)
            ->select('CTRYID','CTRYCODE','NAME')
            ->first();

            $objCorpState = DB::table('TBL_MST_STATE')
            ->where('STID','=',$objMstCust->CORPSTID_REF)
            ->select('STID','STCODE','NAME')
            ->first();

            $objCorpCity = DB::table('TBL_MST_CITY')
            ->where('CITYID','=',$objMstCust->CORPCITYID_REF)
            ->select('CITYID','CITYCODE','NAME')
            ->first();

            $objIndType = DB::table('TBL_MST_INDUSTRYTYPE')
            ->where('INDSID','=',$objMstCust->INDSID_REF)
            ->select('INDSID','INDSCODE','DESCRIPTIONS')
            ->first();

            $objIndVer = DB::table('TBL_MST_INDUSTRYVERTICAL')
            ->where('INDSVID','=',$objMstCust->INDSVID_REF)
            ->select('INDSVID','INDSVCODE','DESCRIPTIONS')
            ->first();

            $objPOC = DB::table('TBL_MST_VENDORPOC')                    
            ->where('VID_REF','=',$id)
            ->select('*')
            ->get()->toArray();
            $objPOCCount = count($objPOC);
            if($objPOCCount==0){
                $objPOCCount=1;
            }

            $objBANK = DB::table('TBL_MST_VENDORBANK')                    
            ->where('VID_REF','=',$id)
            ->select('*')
            ->orderBy('VBANKID','ASC')
            ->get()->toArray();
            $objBANKCount = count($objBANK);
            if($objBANKCount==0){
                $objBANKCount=1;
            }

            //LOCATION LIST
            $objLOC = DB::table('TBL_MST_VENDORLOCATION')                    
            ->where('TBL_MST_VENDORLOCATION.VID_REF','=',$id)
            ->leftJoin('TBL_MST_COUNTRY','TBL_MST_COUNTRY.CTRYID','=','TBL_MST_VENDORLOCATION.CTRYID_REF')                
            ->leftJoin('TBL_MST_STATE','TBL_MST_STATE.STID','=','TBL_MST_VENDORLOCATION.STID_REF')                
            ->leftJoin('TBL_MST_CITY','TBL_MST_CITY.CITYID','=','TBL_MST_VENDORLOCATION.CITYID_REF')                
            ->select('TBL_MST_VENDORLOCATION.*',
            'TBL_MST_COUNTRY.CTRYID AS COU_CTRYID','TBL_MST_COUNTRY.CTRYCODE AS COU_CTRYCODE','TBL_MST_COUNTRY.NAME AS COU_NAME',
            'TBL_MST_STATE.STID AS STA_STID','TBL_MST_STATE.STCODE AS STA_STCODE','TBL_MST_STATE.NAME AS STA_NAME',
            'TBL_MST_CITY.CITYID AS CIT_CITYID','TBL_MST_CITY.CITYCODE AS CIT_CITYCODE','TBL_MST_CITY.NAME AS CIT_NAME'
            )
            ->orderBy('TBL_MST_VENDORLOCATION.LID','ASC')
            ->get()->toArray();

            $objLOCCount = count($objLOC);
            if($objLOCCount==0){
                $objLOCCount=1;
            }
               
            $objUDF = DB::table('TBL_MST_VENDOR_UDF')                    
            ->where('TBL_MST_VENDOR_UDF.VID_REF','=',$id)
            ->leftJoin('TBL_MST_UDFFOR_VENDOR','TBL_MST_UDFFOR_VENDOR.UDFVID','=','TBL_MST_VENDOR_UDF.UDFVENDORID_REF')                
            ->select('TBL_MST_VENDOR_UDF.*','TBL_MST_UDFFOR_VENDOR.*')
            ->orderBy('TBL_MST_VENDOR_UDF.VENDOR_UDFID','ASC')
            ->get()->toArray();
            $objudfCount = count($objUDF);                
            if($objudfCount==0){
                $objudfCount=1;
            }

            $TabSetting =   Helper::getAddSetting(Auth::user()->CYID_REF,'VENDOR_TAB_SETTING');
           
            return view('masters.purchase.vendormaster.mstfrm48amendment',compact([
                'objMstCust','objRights','user_approval_level', 'objudfCount', 'objVendorGroupList', 'objGlList', 
                'objCountryList', 'objIndTypeList', 'objIndVerList', 'objGstTypeList', 'objCurrencyList',  
                'objUdfForVendor', 'objCusGro', 'objOldGlList','objRegCountry','objRegState','objRegCity', 'objCorpCountry',
                'objCorpState','objCorpCity', 'objIndType','objIndVer','objPOC','objPOCCount','objBANK','objBANKCount',
                'objLOC','objLOCCount','objUDF','account_type_data','objAssesseeTypeList','TabSetting'
            ]));
        }

    }

    public function check_transaction(Request $request){
   
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $USERID         =   Auth::user()->USERID;
        $VCODE          =   trim($_REQUEST['VCODE']);

        $DATA_COUNT1    =   DB::select("SELECT COUNT(T1.SLID_REF) AS TOTAL_COUNT
        FROM TBL_MST_VENDOR T1
        INNER JOIN TBL_TRN_PROR01_HDR T2 ON T1.SLID_REF=T2.VID_REF
        WHERE T1.VCODE='$VCODE' AND T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF'")[0]->TOTAL_COUNT;

        $DATA_COUNT2    =   DB::select("SELECT COUNT(T1.SLID_REF) AS TOTAL_COUNT
        FROM TBL_MST_VENDOR T1
        INNER JOIN TBL_TRN_IPO_HDR T2 ON T1.SLID_REF=T2.VID_REF
        WHERE T1.VCODE='$VCODE' AND T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF'")[0]->TOTAL_COUNT;

        $DATA_COUNT3    =   DB::select("SELECT COUNT(T1.SLID_REF) AS TOTAL_COUNT
        FROM TBL_MST_VENDOR T1
        INNER JOIN TBL_TRN_PAYMENT_HDR T2 ON T1.SLID_REF=T2.CUSTMER_VENDOR_ID AND  T2.PAYMENT_FOR='Vendor'
        WHERE T1.VCODE='$VCODE' AND T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF'")[0]->TOTAL_COUNT;

        $TOTAL_COUNT    =   intval($DATA_COUNT1)+intval($DATA_COUNT2)+intval($DATA_COUNT3);
        
        echo $TOTAL_COUNT;die;

    }

    public function saveamendment(Request $request){

        $VCODE          		=	strtoupper(trim($request['VCODE']));     
        $NAME           		=   trim($request['NAME']); 
		$VENDOR_LEGAL_NAME  	=   trim($request['VENDOR_LEGAL_NAME']); 
		$VGID_REF				=   trim($request['VGID_REF']);
		$OLD_REFCODE 			=   !is_null($request['OLD_REFCODE'])?trim($request['OLD_REFCODE']):NULL;  
		$GLID_REF				=   trim($request['GLID_REF']);
		$REGADDL1				=   trim($request['REGADDL1']);
		$REGADDL2 				=   !is_null($request['REGADDL2'])?trim($request['REGADDL2']):''; 
		$REGCTRYID_REF			=   trim($request['REGCTRYID_REF']);
		$REGSTID_REF			=   trim($request['REGSTID_REF']);
		$REGCITYID_REF			=   trim($request['REGCITYID_REF']);
		$REGPIN 				=   !is_null($request['REGPIN'])?trim($request['REGPIN']):NULL; 
		$CORPADDL1 				=   !is_null($request['CORPADDL1'])?trim($request['CORPADDL1']):NULL;
		$CORPADDL2 				=   !is_null($request['CORPADDL2'])?trim($request['CORPADDL2']):NULL;
		$CORPCTRYID_REF 		=   !is_null($request['CORPCTRYID_REF'])?trim($request['CORPCTRYID_REF']):NULL;
		$CORPSTID_REF 			=   !is_null($request['CORPSTID_REF'])?trim($request['CORPSTID_REF']):NULL;
		$CORPCITYID_REF 		=   !is_null($request['CORPCITYID_REF'])?trim($request['CORPCITYID_REF']):NULL;
		$CORPPIN 				=   !is_null($request['CORPPIN'])?trim($request['CORPPIN']):NULL;
		$EMAILID 				=   !is_null($request['EMAILID'])?trim($request['EMAILID']):NULL;
		$WEBSITE 				=   !is_null($request['WEBSITE'])?trim($request['WEBSITE']):NULL;
		$PHNO 					=   !is_null($request['PHNO'])?trim($request['PHNO']):NULL;
		$MONO 					=   !is_null($request['MONO'])?trim($request['MONO']):NULL;
		$CPNAME 				=   !is_null($request['CPNAME'])?trim($request['CPNAME']):NULL;
		$SKYPEID 				=   !is_null($request['SKYPEID'])?trim($request['SKYPEID']):NULL;
		$INDSID_REF 			=   !is_null($request['INDSID_REF'])?trim($request['INDSID_REF']):NULL;
		$INDSVID_REF 			=   !is_null($request['INDSVID_REF'])?trim($request['INDSVID_REF']):NULL;
		$DEALSIN 				=   !is_null($request['DEALSIN'])?trim($request['DEALSIN']):NULL;  // need to check saving or not
		$GSTTYPE				=   trim($request['GSTTYPE']);
		$DEFCRID_REF			=   trim($request['DEFCRID_REF']);
		$GSTIN 					=   !is_null($request['GSTIN'])?trim($request['GSTIN']):NULL;
		$CREDITLIMIT 			=   !is_null($request['CREDITLIMIT'])?trim($request['CREDITLIMIT']):NULL;
		$CIN 					=   !is_null($request['CIN'])?trim($request['CIN']):NULL;
		$CREDITDAY 				=   !is_null($request['CREDITDAY'])?trim($request['CREDITDAY']):NULL;
		$PANNO 					=   !is_null($request['PANNO'])?trim($request['PANNO']):NULL;
		$EXE_GST 				=   isset( $request['EXE_GST']) &&  (!is_null($request['EXE_GST']) ) ? $request['EXE_GST'] : 0; 

        $MSME_NO 					=   !is_null($request['MSME_NO'])?trim($request['MSME_NO']):NULL;
        $FACTORY_ACT_NO 					=   !is_null($request['FACTORY_ACT_NO'])?trim($request['FACTORY_ACT_NO']):NULL;
        $SAP_VENDOR_CODE 					=   !is_null($request['SAP_VENDOR_CODE'])?trim($request['SAP_VENDOR_CODE']):NULL;
        $SAP_VENDOR_NAME1 					=   !is_null($request['SAP_VENDOR_NAME1'])?trim($request['SAP_VENDOR_NAME1']):NULL;
        $SAP_VENDOR_NAME2 					=   !is_null($request['SAP_VENDOR_NAME2'])?trim($request['SAP_VENDOR_NAME2']):NULL;
        $SAP_VENDOR_NAME3 					=   !is_null($request['SAP_VENDOR_NAME3'])?trim($request['SAP_VENDOR_NAME3']):NULL;
        $SAP_CORPORATE_GROUP 					=   !is_null($request['SAP_CORPORATE_GROUP'])?trim($request['SAP_CORPORATE_GROUP']):NULL;
        $SAP_ACCOUNT_GROUP 					=   !is_null($request['SAP_ACCOUNT_GROUP'])?trim($request['SAP_ACCOUNT_GROUP']):NULL;
        $SAP_ACCOUNT_GROUP_NAME 					=   !is_null($request['SAP_ACCOUNT_GROUP_NAME'])?trim($request['SAP_ACCOUNT_GROUP_NAME']):NULL;
        $SAP_TRADING_PARTNER 					=   !is_null($request['SAP_TRADING_PARTNER'])?trim($request['SAP_TRADING_PARTNER']):NULL;
        $SAP_TRADING_PARTNER_NAME 					=   !is_null($request['SAP_TRADING_PARTNER_NAME'])?trim($request['SAP_TRADING_PARTNER_NAME']):NULL;
        $SAP_INVOCING_PARTY 					=   !is_null($request['SAP_INVOCING_PARTY'])?trim($request['SAP_INVOCING_PARTY']):NULL;
        $OUR_CODE_VBOOK 					=   !is_null($request['OUR_CODE_VBOOK'])?trim($request['OUR_CODE_VBOOK']):NULL;
		
        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;       

        $newDateString = NULL;
        $newdt = !(is_null($request['DODEACTIVATED']) || empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 
        if(!is_null($newdt) ){
            $newdt = str_replace( "/", "-",  $newdt ) ;
            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        $DODEACTIVATED = $newDateString;
        
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF'); 
          
        //POINTOFCONTACT
        $r_count1 = $request['Row_Count1'];
        $pocData =[];
        for ($i=0; $i<=$r_count1; $i++){
            if(isset($request['POC_NAME_'.$i]) && !is_null($request['POC_NAME_'.$i]) && trim($request['POC_NAME_'.$i]!='')){
                $pocData[$i]['NAME']        =   isset( $request['POC_NAME_'.$i]) &&  (!is_null($request['POC_NAME_'.$i]) )? $request['POC_NAME_'.$i] : ''; 
                $pocData[$i]['DESIG']       =   isset( $request['POC_DESIG_'.$i]) &&  (!is_null($request['POC_DESIG_'.$i]) )? $request['POC_DESIG_'.$i] : ''; 
                $pocData[$i]['MONO']        =   isset( $request['POC_MONO_'.$i]) &&  (!is_null($request['POC_MONO_'.$i]) )? $request['POC_MONO_'.$i] : ''; 
                $pocData[$i]['EMAIL']       =   isset( $request['POC_EMAIL_'.$i]) &&  (!is_null($request['POC_EMAIL_'.$i]) )? $request['POC_EMAIL_'.$i] : ''; 
                $pocData[$i]['LLNO']        =   isset( $request['POC_LLNO_'.$i]) &&  (!is_null($request['POC_LLNO_'.$i]) )? $request['POC_LLNO_'.$i] : ''; 
                $pocData[$i]['AUTHLEVEL']   =   isset( $request['POC_AUTHLEVEL_'.$i]) &&  (!is_null($request['POC_AUTHLEVEL_'.$i]) )? $request['POC_AUTHLEVEL_'.$i] : '';  
                $pocData[$i]['DOB']         =   isset( $request['POC_DOB_'.$i]) &&  (!is_null($request['POC_DOB_'.$i]) )? $request['POC_DOB_'.$i] : NULL; 
            }
        }

        if(count($pocData)>0){            
                $pocwrapped["POINTOFCONTACT"] = $pocData;    
                $poc_xml = ArrayToXml::convert($pocwrapped);
                $XMLPOC = $poc_xml; 
        }else{
                $XMLPOC = NULL;
        }

        //BANK
        $r_count2 = $request['Row_Count2'];
        $bankData= [];
        for ($i=0; $i<=$r_count2; $i++){
            if(isset($request['BANK_NAME_'.$i]) && !is_null($request['BANK_NAME_'.$i]) && trim($request['BANK_NAME_'.$i]!='')){

                $bankData[$i]['NAME']       =   isset( $request['BANK_NAME_'.$i]) &&  (!is_null($request['BANK_NAME_'.$i]) )? $request['BANK_NAME_'.$i] : ''; 
                $bankData[$i]['IFSC']       =   isset( $request['BANK_IFSC_'.$i]) &&  (!is_null($request['BANK_IFSC_'.$i]) )? $request['BANK_IFSC_'.$i] : ''; 
                $bankData[$i]['BRANCH']     =   isset( $request['BANK_BRANCH_'.$i]) &&  (!is_null($request['BANK_BRANCH_'.$i]) )? $request['BANK_BRANCH_'.$i] : '';  
                $bankData[$i]['ACTYPE']     =   isset( $request['BANK_ACTYPE_'.$i]) &&  (!is_null($request['BANK_ACTYPE_'.$i]) )? $request['BANK_ACTYPE_'.$i] : '';  
                $bankData[$i]['ACNO']       =   isset( $request['BANK_ACNO_'.$i]) &&  (!is_null($request['BANK_ACNO_'.$i]) ) ? $request['BANK_ACNO_'.$i] : '';  
                $bankData[$i]['BYDEFALUT']  =  isset( $request['BYDEFALUT_'.$i]) &&  (!is_null($request['BYDEFALUT_'.$i]) ) ? $request['BYDEFALUT_'.$i] : 0; 
            }  
        }
        if(count($bankData)>0){            
            $bankwrapped["BANK"] = $bankData;    
            $bank_xml = ArrayToXml::convert($bankwrapped);
            $XMLBANK = $bank_xml;
        }else{
            $XMLBANK = NULL;
        }

        //LOCATION
        $r_count3 = $request['Row_Count3'];
        $locationData = [];
        for ($i=0; $i<=$r_count3; $i++){
            if(isset($request['LOC_LADD_'.$i])){
                $locationData[$i]['NAME']       = isset( $request['LOC_NAME_'.$i]) &&  (!is_null($request['LOC_NAME_'.$i]) )? $request['LOC_NAME_'.$i] : ''; 
                $locationData[$i]['LADD']       = isset( $request['LOC_LADD_'.$i]) &&  (!is_null($request['LOC_LADD_'.$i]) )? $request['LOC_LADD_'.$i] : ''; 
                $locationData[$i]['CTRYID_REF']     =  $request['HDNLOC_CTRYID_REF_'.$i];
                $locationData[$i]['STID_REF']       =  $request['HDNLOC_STID_REF_'.$i];
                $locationData[$i]['CITYID_REF']     =  $request['HDNLOC_CITYID_REF_'.$i];
                $locationData[$i]['PIN']            =  $request['LOC_PIN_'.$i];
                $locationData[$i]['GSTIN']          =  $request['LOC_GSTIN_'.$i];
                $locationData[$i]['CPNAME']         =  $request['LOC_CPNAME_'.$i];
                $locationData[$i]['CPDESIGNATION']  =  $request['LOC_CPDESIGNATION_'.$i];
                $locationData[$i]['EMAIL']          =  $request['LOC_EMAIL_'.$i];
                $locationData[$i]['MONO']           =  $request['LOC_MONO_'.$i];
                $locationData[$i]['SPECIAL_INS']  =  $request['LOC_SPINSTRACTION_'.$i];
                $locationData[$i]['BILLTO']         =  isset( $request['LOC_BILLTO_'.$i]) &&  (!is_null($request['LOC_BILLTO_'.$i]) )? $request['LOC_BILLTO_'.$i] : 0; 
                $locationData[$i]['DEFAULT_BILLING'] =  isset( $request['LOC_DEFAULT_BILLTO_'.$i]) &&  (!is_null($request['LOC_DEFAULT_BILLTO_'.$i]) ) ? $request['LOC_DEFAULT_BILLTO_'.$i] : 0; 
                $locationData[$i]['SHIPTO']         =  isset( $request['LOC_SHIPTO_'.$i]) &&  (!is_null($request['LOC_SHIPTO_'.$i]) )? $request['LOC_SHIPTO_'.$i] : 0;  
                $locationData[$i]['DEFAULT_SHIPPING'] =  isset( $request['LOC_DEFAULT_SHIPTO_'.$i]) &&  (!is_null($request['LOC_DEFAULT_SHIPTO_'.$i]) )? $request['LOC_DEFAULT_SHIPTO_'.$i] : 0;  
            }  
        }

        if(count($locationData)>0){            
            $locationwrapped["LOCATION"] = $locationData;    
            $location_xml = ArrayToXml::convert($locationwrapped);
            $XMLLOCATION = $location_xml;
        }else{
            $XMLLOCATION = NULL;
        }

        //UDF FIELDS
        $r_count4 = $request['Row_Count4'];
        $udffield_Data = [];
        for ($i=0; $i<=$r_count4; $i++)
        {
            // $udffield_request = isset( $request['udffie_'.$i]) &&  (!is_null($request['udffie_'.$i]) )? $request['udffie_'.$i] : '';
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFVENDORID_REF'] = $request['udffie_'.$i]; 
                $udffield_Data[$i]['VALUE'] = isset( $request['udfvalue_'.$i]) &&  (!is_null($request['udfvalue_'.$i]) )? $request['udfvalue_'.$i] : '';
            }
        }  
 
        if(count($udffield_Data)>0){            
            $udffield_wrapped["UDF"] = $udffield_Data;  
            $udffield__xml = ArrayToXml::convert($udffield_wrapped);
            $XMLUDF = $udffield__xml;        
 
        }else{
                $XMLUDF = NULL;
        }
 
        $VTID 	= $this->vtid_ref;
        $USERID = Auth::user()->USERID;
        $UPDATE ="ADD";
        $UPDATE =  Date('Y-m-d');
        $UPTIME = Date('h:i:s.u');
 
        $ACTION     =   "AMENDMENT";
        $IPADDRESS  =   $request->getClientIp();

        $TDS_APPLICABLE         =   (isset($request['TDS_APPLICABLE']) )? 1 : 0 ; 
        $CERTIFICATE_NO 	    =   isset($request['CERTIFICATE_NO']) && !is_null($request['CERTIFICATE_NO'])?trim($request['CERTIFICATE_NO']):NULL;
        $EXPIRY_DT 				=   isset($request['EXPIRY_DT']) && !is_null($request['EXPIRY_DT'])?trim($request['EXPIRY_DT']):NULL;
        $ASSESSEEID_REF 		=   isset($request['ASSESSEEID_REF']) && !is_null($request['ASSESSEEID_REF'])?trim($request['ASSESSEEID_REF']):NULL;
        $HOLDINGID_REF 			=   isset($request['HOLDINGID_REF']) && !is_null($request['HOLDINGID_REF'])?trim($request['HOLDINGID_REF']):NULL;
        $CHA                    =   (isset($request['CHA'])!="true" ? 0 : 1);

 
		$save_data = [
            $VCODE,         $NAME,          $VGID_REF,      $OLD_REFCODE,       $GLID_REF,
            $REGADDL1,      $REGADDL2,      $REGCTRYID_REF, $REGSTID_REF,       $REGCITYID_REF,
            $REGPIN,        $CORPADDL1,     $CORPADDL2,     $CORPCTRYID_REF,    $CORPSTID_REF, 
            $CORPCITYID_REF,$CORPPIN,       $EMAILID,       $WEBSITE,           $PHNO,
            $MONO,          $CPNAME,        $SKYPEID,       $INDSID_REF,        $INDSVID_REF,
            $DEALSIN,       $DEFCRID_REF,   $GSTTYPE,       $GSTIN,             $CIN,
            $PANNO,         $CREDITLIMIT,   $CREDITDAY,     $VENDOR_LEGAL_NAME, $EXE_GST,
            $DEACTIVATED,   $DODEACTIVATED, $TDS_APPLICABLE,$ASSESSEEID_REF,    $HOLDINGID_REF,
            $CERTIFICATE_NO,$EXPIRY_DT,   $MSME_NO,       $FACTORY_ACT_NO,     $SAP_VENDOR_CODE,
            $SAP_VENDOR_NAME1,$SAP_VENDOR_NAME2, $SAP_VENDOR_NAME3,             $SAP_CORPORATE_GROUP,
            $SAP_ACCOUNT_GROUP  , $SAP_ACCOUNT_GROUP_NAME,  $SAP_TRADING_PARTNER, $SAP_TRADING_PARTNER_NAME,
            $SAP_INVOCING_PARTY ,$OUR_CODE_VBOOK,
            
            $CYID_REF,      $BRID_REF,          $FYID_REF,
            $XMLLOCATION,   $XMLBANK,       $XMLPOC,        $XMLUDF,            $VTID,
            $USERID,        $UPDATE,        $UPTIME,        $ACTION,            $IPADDRESS, $CHA
        ];
        		
		$sp_result = DB::select('EXEC SP_VENDOR_AMENDMENT ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?', $save_data);
				
  
				
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

        }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
           
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','resp'=>'duplicate']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }
        
        exit();    
              
    }



}
