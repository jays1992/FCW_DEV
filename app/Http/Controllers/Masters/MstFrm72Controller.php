<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use App\Models\Master\TblMstFrm4;
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

class MstFrm72Controller extends Controller
{
   
    protected $form_id = 72;
    protected $vtid_ref   = 72;  //voucher type id

    //validation messages
    protected   $messages = [
                    'ICODE.required' => 'Required field',
                    'NAME.required' => 'Required field',
                    'CLASSID_REF.required' => 'Required field',
                    'MAIN_UOMID_REF.required' => 'Required field',
                    'ALT_UOMID_REF.required' => 'Required field',
                    'ITEMGID_REF.required' => 'Required field',
                    'ISGID_REF.required' => 'Required field',
                    'ICID_REF.required' => 'Required field',
                    'STID_REF.required' => 'Required field',
                    'HSNID_REF.required' => 'Required field'
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


    public function index(){

        $objRights = DB::table('TBL_MST_USERROLMAP')
        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)       
        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
		->leftJoin('TBL_MST_USER_BRANCH_MAP', 'TBL_MST_USERROLMAP.USERID_REF','=','TBL_MST_USER_BRANCH_MAP.USERID_REF')
        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
		->where('TBL_MST_USER_BRANCH_MAP.MAPBRID_REF','=',Session::get('BRID_REF'))
        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        ->first();

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');  

        $CompanyLoginStatus =   $this->CompanyLoginStatus();
        $TabSetting         =   Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
        $AlpsStatus         =   $this->AlpsStatus();

        $TotalRow    =   DB::select("SELECT count(T1.ITEMID) AS TOTAL_ROW
        FROM TBL_MST_ITEM T1        
        INNER JOIN TBL_MST_AUDITTRAIL T2 ON T1.ITEMID=T2.VID AND T1.CYID_REF=T2.CYID_REF AND T1.BRID_REF=T2.BRID_REF AND T2.VTID_REF = '$this->vtid_ref' AND T2.ACTID IN (SELECT max(ACTID) FROM TBL_MST_AUDITTRAIL A WHERE T2.VTID_REF = A.VTID_REF AND T2.VID = A.VID)
        WHERE T1.CYID_REF='$CYID_REF'")[0]->TOTAL_ROW;

       return view('masters.inventory.itemmasters.mstfrm72',compact(['TotalRow','objRights','CompanyLoginStatus','TabSetting','AlpsStatus']));
        
    }

    public function getListingData(Request $request){
   
        $CYID_REF           =   Auth::user()->CYID_REF;
        $BRID_REF           =   Session::get('BRID_REF');
        $USERID             =   Auth::user()->USERID;
        $AlpsStatus         =   $this->AlpsStatus();
        
        $ICODE              =   $_REQUEST['ICODE'];
        $NAME               =   $_REQUEST['NAME'];
        $ITEM_DESC          =   $_REQUEST['ITEM_DESC'];
        $ALPS_PART_NO       =   $_REQUEST['ALPS_PART_NO'];
        $CUSTOMER_PART_NO   =   $_REQUEST['CUSTOMER_PART_NO'];
        $OEM_PART_NO        =   $_REQUEST['OEM_PART_NO'];
        $STATUS             =   $_REQUEST['STATUS'];

        
        $start              =   $_POST["start"];
        $limit              =   $_POST["limit"];
        $indexno            =   $_POST["indexno"];
       
        $W_ICODE            =   $ICODE !=''?"AND T1.ICODE LIKE '%$ICODE%'":'';
        $W_NAME             =   $NAME !=''?"AND T1.NAME LIKE '%$NAME%'":'';
        $W_ITEM_DESC        =   $ITEM_DESC !=''?"AND T1.ITEM_DESC LIKE '%$ITEM_DESC%'":'';
        $W_ALPS_PART_NO     =   $ALPS_PART_NO !=''?"AND T1.ALPS_PART_NO LIKE '%$ALPS_PART_NO%'":'';
        $W_CUSTOMER_PART_NO =   $CUSTOMER_PART_NO !=''?"AND T1.CUSTOMER_PART_NO LIKE '%$CUSTOMER_PART_NO%'":'';
        $W_OEM_PART_NO      =   $OEM_PART_NO !=''?"AND T1.OEM_PART_NO LIKE '%$OEM_PART_NO%'":'';
        $W_STATUS           =   $STATUS !=''?"AND T1.STATUS ='$STATUS'":'';
       
        $WHERE_FIELD        =   trim("$W_ICODE $W_NAME $W_ITEM_DESC $W_ALPS_PART_NO $W_CUSTOMER_PART_NO $W_OEM_PART_NO $W_STATUS"); 
        
        $REQUEST_DATA       =   array(
            'FORMID'    =>  $this->form_id,
            'VTID_REF'  =>  $this->vtid_ref,
            'USERID'    =>  Auth::user()->USERID,
            'CYID_REF'  =>  Auth::user()->CYID_REF,
            'BRID_REF'  =>  Session::get('BRID_REF'),
            'FYID_REF'  =>  Session::get('FYID_REF'),
        );
        
        $DATA_STATUS    =	Helper::get_user_level($REQUEST_DATA);
        $USER_LEVEL     =   $DATA_STATUS['USER_LEVEL'];

        $objDataList    =   DB::select("SELECT '$USER_LEVEL' AS USER_LEVEL,T1.*,T2.ACTIONNAME,U.DESCRIPTIONS AS CREATED_BY,T3.BUNAME AS BUNIT_NAME 
        FROM TBL_MST_ITEM T1        
        INNER JOIN TBL_MST_AUDITTRAIL T2 ON T1.ITEMID=T2.VID AND T1.CYID_REF=T2.CYID_REF AND T1.BRID_REF=T2.BRID_REF AND T2.VTID_REF = '$this->vtid_ref' AND T2.ACTID IN (SELECT max(ACTID) FROM TBL_MST_AUDITTRAIL A WHERE T2.VTID_REF = A.VTID_REF AND T2.VID = A.VID)
        LEFT JOIN TBL_MST_BUSINESSUNIT T3 ON T1.BUID_REF=T3.BUID
        LEFT JOIN TBL_MST_USER U ON T2.USERID=U.USERID
        WHERE T1.CYID_REF='$CYID_REF' $WHERE_FIELD  
        ORDER BY T1.ITEMID DESC  OFFSET $start ROWS FETCH NEXT  $limit ROWS ONLY");

        if(!empty($objDataList)){          
            foreach($objDataList as $key => $val){

                $app_status = isset($DATA_STATUS[$val->STATUS])?$DATA_STATUS[$val->STATUS]:0;
                $DataStatus = $val->USER_LEVEL == $val->ACTIONNAME?$DATA_STATUS['APPROVAL5']:$DATA_STATUS[$val->ACTIONNAME];
           
                $ICODE              =   isset($val->ICODE) && $val->ICODE !=''?$val->ICODE:'';
                $BUNIT_NAME         =   isset($val->BUNIT_NAME) && $val->BUNIT_NAME !=''?$val->BUNIT_NAME:'';
                $NAME               =   isset($val->NAME) && $val->NAME !=''?$val->NAME:'';
                $ALPS_PART_NO       =   isset($val->ALPS_PART_NO) && $val->ALPS_PART_NO !=''?$val->ALPS_PART_NO:'';
                $CUSTOMER_PART_NO   =   isset($val->CUSTOMER_PART_NO) && $val->CUSTOMER_PART_NO !=''?$val->CUSTOMER_PART_NO:'';
                $OEM_PART_NO        =   isset($val->OEM_PART_NO) && $val->OEM_PART_NO !=''?$val->OEM_PART_NO:'';
                $ITEM_DESC          =   isset($val->ITEM_DESC) && $val->ITEM_DESC !=''?$val->ITEM_DESC:'';
                $INDATE             =   isset($val->INDATE) && $val->INDATE !='' && $val->INDATE !='1900-01-01' ? date('d-m-Y',strtotime($val->INDATE)):'';
                $CREATED_BY         =   isset($val->CREATED_BY) && $val->CREATED_BY !=''?$val->CREATED_BY:'';
                $DEACTIVATED_STATUS =   isset($val->DEACTIVATED) && $val->DEACTIVATED =='1'?'Yes':'No';
                $DEACTIVATED_DATE   =   isset($val->DODEACTIVATED) && $val->DODEACTIVATED !='' && $val->DODEACTIVATED !='1900-01-01' ? date('d-m-Y',strtotime($val->DODEACTIVATED)):'';
        
                echo '<tr class="participantRow">
                    <td><input type="checkbox" name="selectAll[]" id="chkId'.$val->ITEMID.'" value="'.$val->ITEMID.'" class="js-selectall1" data-rcdstatus="'.$app_status.'"></td>
                    <td>'.$ICODE.'</td>
                    <td>'.$NAME.'</td>
                    <td>'.$ITEM_DESC.'</td>
                    <td>'.$BUNIT_NAME.'</td>
                    <td '.$AlpsStatus['hidden'].'>'.$ALPS_PART_NO.'</td>
                    <td '.$AlpsStatus['hidden'].'>'.$CUSTOMER_PART_NO.'</td>
                    <td '.$AlpsStatus['hidden'].'>'.$OEM_PART_NO.'</td>
                    <td>'.$INDATE.'</td>
                    <td>'.$CREATED_BY.'</td>
                    <td hidden>'.$DEACTIVATED_STATUS.'</td>
                    <td hidden>'.$DEACTIVATED_DATE.'</td>
                    <td>'.$DataStatus.'</td>
                </tr>';
            }    
        }
        else{
            echo '';
        }

       

        exit();
    }


    public function downloadexcelsamplefile(){
        $excelfile_path         =   "docs/importsamplefiles/itemmaster/item_master_import_sample_format.xlsx";     
        $custfilename   =   str_replace('\\', '/', public_path($excelfile_path));
       
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load($custfilename);
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="item_master_import_sample_format.xlsx"');
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
        return view('masters.inventory.itemmasters.mstfrm72importexcel',compact(['objMstVoucherType']));
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

                $excelfile_path         =   "docs/company".$CYID_REF."/itemmaster/importexcel";     
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
                                $exlwrapped["ITEM"] = $excelAlldata;    
                                $exl_xml = ArrayToXml::convert($exlwrapped);
                                $XMLEXCEL = $exl_xml;
                            }else{
                                $XMLEXCEL = NULL;
                            }

                        } catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                            //die('Error loading file: '.$e->getMessage());
                            return redirect()->route("master",[72,"importdata"])->with("error","Error loading file: ".$e->getMessage());
                        }

                        //-------------

                    }
                    else
                    {
                        return redirect()->route("master",[72,"importdata"])->with("error","There is some file uploading error. Please try again.");
                    } // file exists
                    

                        
                    }else{
                        
                        return redirect()->route("master",[72,"importdata"])->with("error","Invalid size - Please check.");
                    } //invalid size
                    
                }else{

                    return redirect()->route("master",[72,"importdata"])->with("error","Invalid file extension - Please check.");                      
                }// invalid extension
            
            }else{
                    
                return redirect()->route("master",[72,"importdata"])->with("error","Invalid file - Please check.");  
            }//invalid

        }else{
            return redirect()->route("master",[72,"importdata"])->with("error","File not found. - Please check.");  
        }

        $logfile_path = $excelfile_path."/".$logfile_name;     

        //dd($logfile_path);

        if(!$logfile = fopen($logfile_path, "a") ){

            return redirect()->route("master",[72,"importdata"])->with("error","Log creating file error.");     //create or open log file
        }
        //----------------------CHECK VALID DATA
            $validationErr = false;

            $headerArr = []; 
            foreach($excelAlldata as $eIndex=>$eRowData){
                //dd($eRowData);
                $hkey = trim($eRowData["item_code"]);
                if($hkey!="")
                {
                        if (!array_key_exists($hkey, $headerArr)) {        
                            $headerArr[$eRowData["item_code"]]["header"]["item_code"]                       = $eRowData["item_code"];
                            $headerArr[$eRowData["item_code"]]["header"]["item_name"]                       = $eRowData["item_name"];
                            $headerArr[$eRowData["item_code"]]["header"]["item_part_no"]                    = $eRowData["item_part_no"];
                            $headerArr[$eRowData["item_code"]]["header"]["item_drawing_no"]                 = $eRowData["item_drawing_no"];
                            $headerArr[$eRowData["item_code"]]["header"]["inventory_class_name"]            = $eRowData["inventory_class_name"];
                            $headerArr[$eRowData["item_code"]]["header"]["inventory_class_id"]              = "";
                            $headerArr[$eRowData["item_code"]]["header"]["main_uom_code"]                   = $eRowData["main_uom_code"];
                            $headerArr[$eRowData["item_code"]]["header"]["main_uom_id"]                     = "";
                            $headerArr[$eRowData["item_code"]]["header"]["alt_uom_code"]                    = $eRowData["alt_uom_code"];
                            $headerArr[$eRowData["item_code"]]["header"]["alt_uom_id"]                      = "";
                            $headerArr[$eRowData["item_code"]]["header"]["item_type_code"]                  = $eRowData["item_type_code"];
                            $headerArr[$eRowData["item_code"]]["header"]["material_type_code_or_gl_code"]   = $eRowData["material_type_code_or_gl_code"];
                            //$headerArr[$eRowData["item_code"]]["header"]["material_type_code_or_gl_id"]     = "";
                            $headerArr[$eRowData["item_code"]]["header"]["item_description"]                = $eRowData["item_description"];
                            $headerArr[$eRowData["item_code"]]["header"]["item_group_name"]                 = $eRowData["item_group_name"];
                            $headerArr[$eRowData["item_code"]]["header"]["item_group_id"]                   = "";
                            $headerArr[$eRowData["item_code"]]["header"]["item_sub_group_name"]             = $eRowData["item_sub_group_name"];
                            $headerArr[$eRowData["item_code"]]["header"]["item_sub_group_id"]               = "";
                            $headerArr[$eRowData["item_code"]]["header"]["item_category_name"]              = $eRowData["item_category_name"];
                            $headerArr[$eRowData["item_code"]]["header"]["item_category_id"]                = "";
                            $headerArr[$eRowData["item_code"]]["header"]["default_store_name"]              = $eRowData["default_store_name"];
                            $headerArr[$eRowData["item_code"]]["header"]["default_store_id"]                = "";
                            $headerArr[$eRowData["item_code"]]["header"]["hsn_code"]                        = $eRowData["hsn_code"];
                            $headerArr[$eRowData["item_code"]]["header"]["hsn_id"]                          = "";
                            $headerArr[$eRowData["item_code"]]["header"]["standard_custom_duty_rate_percentage"]    = $eRowData["standard_custom_duty_rate_percentage"];
                            $headerArr[$eRowData["item_code"]]["header"]["inventory_valuation_method"]  = $eRowData["inventory_valuation_method"];
                            $headerArr[$eRowData["item_code"]]["header"]["business_unit_name"]          = $eRowData["business_unit_name"];
                            $headerArr[$eRowData["item_code"]]["header"]["business_unit_id"]            = "";
                            $headerArr[$eRowData["item_code"]]["header"]["standard_rate"]               = $eRowData["standard_rate"];
                            $headerArr[$eRowData["item_code"]]["header"]["standard_sws_rate_percentage"]= $eRowData["standard_sws_rate_percentage"];
                            $headerArr[$eRowData["item_code"]]["header"]["minimum_level"]               = $eRowData["minimum_level"];
                            $headerArr[$eRowData["item_code"]]["header"]["reorder_level"]               = $eRowData["reorder_level"];
                            $headerArr[$eRowData["item_code"]]["header"]["maximum_level"]               = $eRowData["maximum_level"];
                            $headerArr[$eRowData["item_code"]]["header"]["item_specification"]          = $eRowData["item_specification"];                           
                            //check flag tab
                            $headerArr[$eRowData["item_code"]]["header"]["check_flag_qc_applicable"]                = $eRowData["check_flag_qc_applicable"];                            
                            $headerArr[$eRowData["item_code"]]["header"]["check_flag_serial_no_applicable"]         = $eRowData["check_flag_serial_no_applicable"];
                            $headerArr[$eRowData["item_code"]]["header"]["check_flag_batch_no_lot_no_applicable"]   = $eRowData["check_flag_batch_no_lot_no_applicable"];
                            $headerArr[$eRowData["item_code"]]["header"]["check_flag_inventory_maintain"]           = $eRowData["check_flag_inventory_maintain"];

                            $headerArr[$eRowData["item_code"]]["header"]["check_flag_barcode_applicable"]           = $eRowData["check_flag_barcode_applicable"];
                            $headerArr[$eRowData["item_code"]]["header"]["check_flag_serialno_mode"]                = $eRowData["check_flag_serialno_mode"];
                            $headerArr[$eRowData["item_code"]]["header"]["check_flag_serialno_prefix"]              = $eRowData["check_flag_serialno_prefix"];
                            $headerArr[$eRowData["item_code"]]["header"]["check_flag_serialno_starts_from"]         = $eRowData["check_flag_serialno_starts_from"];
                            $headerArr[$eRowData["item_code"]]["header"]["check_flag_serialno_suffix"]              = $eRowData["check_flag_serialno_suffix"];
                            $headerArr[$eRowData["item_code"]]["header"]["check_flag_serialno_max_length"]          = $eRowData["check_flag_serialno_max_length"];

                            //ALPS SPECIFIC
                            $headerArr[$eRowData["item_code"]]["header"]["alps_specific_sap_customer_code"]        = $eRowData["alps_specific_sap_customer_code"];
                            $headerArr[$eRowData["item_code"]]["header"]["alps_specific_sap_customer_name"]         = $eRowData["alps_specific_sap_customer_name"];
                            $headerArr[$eRowData["item_code"]]["header"]["alps_specific_sap_part_number"]           = $eRowData["alps_specific_sap_part_number"];
                            $headerArr[$eRowData["item_code"]]["header"]["alps_specific_sap_part_description"]      = $eRowData["alps_specific_sap_part_description"];
                            $headerArr[$eRowData["item_code"]]["header"]["alps_specific_sap_customer_part_no"]      = $eRowData["alps_specific_sap_customer_part_no"];
                            $headerArr[$eRowData["item_code"]]["header"]["alps_specific_sap_market_set_code"]       = $eRowData["alps_specific_sap_market_set_code"];
                            $headerArr[$eRowData["item_code"]]["header"]["alps_specific_rounding_value_lot_size_qty"]   = $eRowData["alps_specific_rounding_value_lot_size_qty"];
                            $headerArr[$eRowData["item_code"]]["header"]["alps_specific_alps_part_no"]              = $eRowData["alps_specific_alps_part_no"];
                            $headerArr[$eRowData["item_code"]]["header"]["alps_specific_customer_part_no"]          = $eRowData["alps_specific_customer_part_no"];
                            $headerArr[$eRowData["item_code"]]["header"]["alps_specific_oem_part_no"]               = $eRowData["alps_specific_oem_part_no"];
                            //attribute tab
                            $headerArr[$eRowData["item_code"]]["attribute"][$eIndex]["attribute_attribute_code"]    = $eRowData["attribute_attribute_code"];
                            $headerArr[$eRowData["item_code"]]["attribute"][$eIndex]["attribute_attribute_id"]      = "";
                            $headerArr[$eRowData["item_code"]]["attribute"][$eIndex]["attribute_value"]             = $eRowData["attribute_value"];
                            $headerArr[$eRowData["item_code"]]["attribute"][$eIndex]["attribute_value_id"]          = "";
                            //technical tab
                            $headerArr[$eRowData["item_code"]]["ts"][$eIndex]["technical_specification_ts_type"]     = $eRowData["technical_specification_ts_type"];                            
                            $headerArr[$eRowData["item_code"]]["ts"][$eIndex]["technical_specification_value"]       = $eRowData["technical_specification_value"];
                            //UOM conversion                            
                            $headerArr[$eRowData["item_code"]]["uomcon"][$eIndex]["uom_conversion_to_uom_code"] = $eRowData["uom_conversion_to_uom_code"];
                            $headerArr[$eRowData["item_code"]]["uomcon"][$eIndex]["uom_conversion_to_uom_id"]   = "";
                            $headerArr[$eRowData["item_code"]]["uomcon"][$eIndex]["uom_conversion_qty"]         = $eRowData["uom_conversion_qty"];
                            //UDF
                            $headerArr[$eRowData["item_code"]]["udf"][$eIndex]["udf_udf_label"] = $eRowData["udf_udf_label"];
                            $headerArr[$eRowData["item_code"]]["udf"][$eIndex]["udf_value"]     = $eRowData["udf_value"];
                            $headerArr[$eRowData["item_code"]]["udf"][$eIndex]["udf_id"]        = "";
                            $headerArr[$eRowData["item_code"]]["udf"][$eIndex]["udf_valuetype"]        = "";

                        }else{
                        // echo "The ".$hkey." element is in the array ";
                        $dif_result=array_diff($headerArr[$eRowData["item_code"]]["header"], $eRowData); 
                            //dump($dif_result);
                            if(!empty($dif_result)){
                                foreach($dif_result as $dfkey=>$dfval){
                                    //for log
                                $this->appendLogData($logfile,"Column Name=".strtoupper($dfkey)." value is different. Data must be same for same item (".$hkey.")");
                                $validationErr=true;                                  
                                }
                                break 1;

                            }else{  //if found valid item data except grid's data
                            
                                //attribute tab
                                $headerArr[$eRowData["item_code"]]["attribute"][$eIndex]["attribute_attribute_code"]    = $eRowData["attribute_attribute_code"];
                                $headerArr[$eRowData["item_code"]]["attribute"][$eIndex]["attribute_attribute_id"]      = "";
                                $headerArr[$eRowData["item_code"]]["attribute"][$eIndex]["attribute_value"]             = $eRowData["attribute_value"];
                                $headerArr[$eRowData["item_code"]]["attribute"][$eIndex]["attribute_value_id"]          = "";
                                //technical tab
                                $headerArr[$eRowData["item_code"]]["ts"][$eIndex]["technical_specification_ts_type"]       = $eRowData["technical_specification_ts_type"];                            
                                $headerArr[$eRowData["item_code"]]["ts"][$eIndex]["technical_specification_value"]         = $eRowData["technical_specification_value"];
                                //UOM conversion                            
                                $headerArr[$eRowData["item_code"]]["uomcon"][$eIndex]["uom_conversion_to_uom_code"] = $eRowData["uom_conversion_to_uom_code"];
                                $headerArr[$eRowData["item_code"]]["uomcon"][$eIndex]["uom_conversion_to_uom_id"]   = "";
                                $headerArr[$eRowData["item_code"]]["uomcon"][$eIndex]["uom_conversion_qty"]         = $eRowData["uom_conversion_qty"];
                                //UDF
                                $headerArr[$eRowData["item_code"]]["udf"][$eIndex]["udf_udf_label"] = $eRowData["udf_udf_label"];
                                $headerArr[$eRowData["item_code"]]["udf"][$eIndex]["udf_value"]     = $eRowData["udf_value"];
                                $headerArr[$eRowData["item_code"]]["udf"][$eIndex]["udf_id"]        = "";
                                $headerArr[$eRowData["item_code"]]["udf"][$eIndex]["udf_valuetype"]        = "";

                            }
                        dump($dif_result); // invalid values                            
                        }
                    }else{
                        echo "<br>Invalid Row or Blank Item Code in Row no ".$eIndex++;
                        $this->appendLogData($logfile,"Invalid Row or Blank Item Code in Row no",$eIndex++);
                        $validationErr=true;
                        break 1;
                    }        
                
            } // $excelAlldata foreach end
           
            //dd($headerArr);  // after preaper all data

            $dbUDFData =   $this->exlGetItemUDF(Auth::user()->CYID_REF);  //get all udf data
            //echo "--all db udf --";
            //dump($dbUDFData);
            $udfexllabel =[];
            $udfDbLabel=[];
            foreach ($dbUDFData as $ukey2 => $urow2) {
                $udfDbLabel[] = $urow2->LABEL;
            }
        
            //-----------check validation begin
            foreach($headerArr as $hIndex=>$hRowData){
                foreach($hRowData["header"] as $key1=>$val1){
                    if($key1=="item_code"){
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid: Blank Item Code");
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidCode($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Item Code ".$val1." Space not allowed or Only Alpha Numeric allowed" );
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidLen($val1,20)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Length: Item Code ".$val1." can have max 20 character.");
                            $validationErr=true;
                            break 2;                            
                        }
                        //check duplicate code
                        if($this->exlIsDuplicateItemCode($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Item ".$hIndex.": Duplicate Item Code ".$val1);
                            $validationErr=true;
                            break 2; 
                        }
                        
                    } //item_code
                    if($key1=="item_name"){
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Item ".$hIndex.": Blank Item Name" );
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidLen($val1,200)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Item ".$hIndex.": Item Name (".$val1.") can have max 200 character.");
                            $validationErr=true;
                            break 2;                            
                        }
                        
                    } //item_name
                
                    if($key1=="item_part_no"){
                            if($this->exlIsValidLen($val1,20)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Invalid Item ".$hIndex.": Part No (".$val1.") can have max 20 character.");
                                $validationErr=true;
                                break 2;                            
                            }                        
                    }// item_part_no

                    if($key1=="item_drawing_no"){
                            if($this->exlIsValidLen($val1,20)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Invalid Item ".$hIndex.": Drawing No (".$val1.") can have max 20 character.");
                                $validationErr=true;
                                break 2;                            
                            }                        
                    }// item_drawing_no

                    $fdgdg = $key1=="item_drawing_no";

                   // dd($fdgdg);



                    if($key1=="inventory_class_name")
                    {
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Item ".$hIndex.": Blank Inventory Class Name" );
                            $validationErr=true;
                            break 2;                            
                        }
                        // if($this->exlIsValidCode($val1)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Item ".$hIndex."- Invalid Inventory Class Name ".$val1.",  Space not allowed or Only Alpha Numeric allowed. " );
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        // if($this->exlIsValidLen($val1,20)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Inventory Class Name ".$val1." can have max 20 character.");
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        //check code
                        $resgl = $this->exlIsInvClassNameExists($val1);

                        ///dd($resgl);

                        if($resgl["result"]==true){
                            $validationErr=false; 
                            $headerArr[$hIndex]["header"]["inventory_class_id"]=$resgl["id"];
                        
                        }else {
                            $validationErr=true;
                            $headerArr[$hIndex]["header"]["inventory_class_id"]="";
                            $this->appendLogData($logfile,"Item ".$hIndex.": Inventory Class Name ".$val1." not found.");
                            break 2; 
                        }
                    }// inventory_class_name

                    if($key1=="main_uom_code")
                    {

                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Item ".$hIndex.": Blank Main UoM Code" );
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidCode($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."- Invalid Main UoM Code ".$val1.",  Space not allowed or Only Alpha Numeric allowed. " );
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidLen($val1,20)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Main UoM Code ".$val1." can have max 20 character.");
                            $validationErr=true;
                            break 2;                            
                        }
                        //check code
                        $resgl = $this->exlIsMUOMExists($val1);
                        if($resgl["result"]==true){
                            $validationErr=false; 
                            $headerArr[$hIndex]["header"]["main_uom_id"]=$resgl["id"];
                        
                        }else {
                            $validationErr=true;
                            $headerArr[$hIndex]["header"]["main_uom_id"]="";
                            $this->appendLogData($logfile,"Item ".$hIndex.": Main UoM Code ".$val1." not found.");
                            break 2; 
                        }
                    }// main_uom_code

                    if($key1=="alt_uom_code")
                    {
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Item ".$hIndex.": Blank ALT UoM Code" );
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidCode($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."- Invalid ALT UoM Code ".$val1.",  Space not allowed or Only Alpha Numeric allowed. " );
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidLen($val1,20)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: ALT UoM Code ".$val1." can have max 20 character.");
                            $validationErr=true;
                            break 2;                            
                        }
                        //check code
                        $resgl = $this->exlIsMUOMExists($val1);
                        if($resgl["result"]==true){
                            $validationErr=false; 
                            $headerArr[$hIndex]["header"]["alt_uom_id"]=$resgl["id"];
                        
                        }else {
                            $validationErr=true;
                            $headerArr[$hIndex]["header"]["alt_uom_id"]="";
                            $this->appendLogData($logfile,"Item ".$hIndex.": ALT UoM Code ".$val1." not found.");
                            break 2; 
                        }
                        // $muom = strtoupper($headerArr[$hIndex]["header"]["main_uom_code"]);
                        // if(strtoupper($val1)!=$muom){
                        //     $validationErr=false; 
                        // }else{
                        //     $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Main UoM and ALT UoM Code ".$val1." can not be same.");
                        //     $validationErr=true;
                        //     break 2;      
                        // }



                    }// alt_uom_code
                    if($key1=="item_type_code")
                    {
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Invalid Item ".$hIndex.": Blank Item Type Code" );
                            $validationErr=true;
                            break 2;                            
                        }
                        if(trim($val1)=="I-Inventory" || trim($val1)=="S-Service" || trim($val1)=="O-Other" || trim($val1)=="A-Assets"){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."- Invalid Item Type Code ".$val1.", Allowed value is I-Inventory/S-Service/O-Other/A-Assets. " );
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidLen($val1,11)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Item Type Code ".$val1." can have max 11 character.");
                            $validationErr=true;
                            break 2;                            
                        }
                        
                    }// item_type_code

                    
                    if($key1=="material_type_code_or_gl_code"){

                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            //$this->appendLogData($logfile,"Item ".$hIndex."-Invalid: Blank Material Type");
                            $validationErr=false;
                          //  break 2;                            
                        }

                        $itemtype = $headerArr[$hIndex]["header"]["item_type_code"];
                        if(trim($itemtype)=="I-Inventory" || trim($itemtype)=="O-Other"){

                            if(trim($val1)=="FG-Finish Good" || trim($val1)=="SFG- Semi Finish Good" || trim($val1)=="RM-Raw Material" || trim($val1)=="PM-Packing Material" || trim($val1)=="O-Other" || trim($val1)=="TG-Trading Good"){
                                $validationErr=false; 
                                $headerArr[$hIndex]["header"]["material_type_code_or_gl_code"]=trim($val1); 

                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Material Type ".$val1.". Allow values are FG-Finish Good/SFG- Semi Finish Good/RM-Raw Material/PM-Packing Material/O-Other/TG-Trading Good" );                               
                                $validationErr=true;
                                $headerArr[$hIndex]["header"]["material_type_code_or_gl_code"]=""; 
                                break 2;                            
                            }  

                        } else if(trim($itemtype)=="S-Service"){

                            if($this->exlIsBlank($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Invalid Item ".$hIndex.": Blank GL Code of Material Type." );
                                $validationErr=true;
                                break 2;                            
                            }
                            if($this->exlIsValidCode($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid GL Code of Material Type ".$val1.",  Space not allowed or Only Alpha Numeric allowed. " );
                                $validationErr=true;
                                break 2;                            
                            }
                            if($this->exlIsValidLen($val1,20)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: GL Code of Material Type ".$val1." can have max 20 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                            //check code
                            $resgl = $this->exlIsGlExists($val1);
                            if($resgl["result"]==true){
                                $validationErr=false; 
                                $headerArr[$hIndex]["header"]["material_type_code_or_gl_code"]=$resgl["id"];                            
                            }else {
                                $validationErr=true;
                                $headerArr[$hIndex]["header"]["material_type_code_or_gl_code"]="";
                                $this->appendLogData($logfile,"Item ".$hIndex.": Material Type-GL Code ".$val1." not found.");
                                break 2; 
                            }

                        }
						else if(trim($itemtype)=="A-Assets"){

                            if($this->exlIsBlank($val1)==true){
                                $validationErr=false; 
                            }else {
                                $validationErr=false;                            
                            }
                        }else{
                            $validationErr=true;
                            $headerArr[$hIndex]["header"]["material_type_code_or_gl_code"]="";
                            $this->appendLogData($logfile,"Item ".$hIndex.": Material Type Code ".$val1." not found.");
                            break 2; 
                        }
                    
                    } //material_type_code_or_gl_code
                    
                    if($key1=="item_description"){
                        if(trim($val1)!=""){
                            if($this->exlIsBlank($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Invalid Item ".$hIndex.": Blank Item Description" );
                                $validationErr=true;
                                break 2;                            
                            }
                            if($this->exlIsValidLen($val1,200)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Invalid Item ".$hIndex.": Item Description (".$val1.") can have max 200 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    } //item_description 

                    if($key1=="item_group_name"){
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."-Invalid: Blank Item Group Name");
                            $validationErr=true;
                            break 2;                            
                        }
                        // if($this->exlIsValidCode($val1)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Item Group Name ".$val1." Space not allowed or Only Alpha Numeric allowed" );
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        // if($this->exlIsValidLen($val1,10)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Length: Item Group Name ".$val1." can have max 10 character.");
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        //----------------
                        //check code
                        $resig = $this->exlIsItemGroup($val1);
                        if($resig["result"]==true){
                            $validationErr=false; 
                            $headerArr[$hIndex]["header"]["item_group_id"]=$resig["id"];
                        
                        }else {
                            $validationErr=true;
                            $headerArr[$hIndex]["header"]["item_group_id"]="";
                            $this->appendLogData($logfile,"Item ".$hIndex.": Item Group Name ".$val1." not found.");
                            break 2; 
                        }
                    } //item_group_name


                    if($key1=="item_sub_group_name"){
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."-Invalid: Blank Sub Group Name");
                            $validationErr=true;
                            break 2;                            
                        }
                        // if($this->exlIsValidCode($val1)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Item ".$hIndex."- Invalid:Sub Group Code ".$val1." Space not allowed or Only Alpha Numeric allowed" );
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        // if($this->exlIsValidLen($val1,10)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Length:Sub Group Code ".$val1." can have max 10 character.");
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        //----------------
                        //check code
                        $itemgid = $headerArr[$hIndex]["header"]["item_group_id"];
                        $resdata = $this->exlISubGroupExists($itemgid,$val1);
                        if($resdata["result"]==true){
                            $validationErr=false; 
                            $headerArr[$hIndex]["header"]["item_sub_group_id"]=$resdata["id"];
                        
                        }else {
                            $validationErr=true;
                            $headerArr[$hIndex]["header"]["item_sub_group_id"]="";
                            $this->appendLogData($logfile,"Item ".$hIndex.": Sub Group Name ".$val1." not found.");
                            break 2; 
                        }
                    } //item_sub_group_name

                    if($key1=="item_category_name"){
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."-Invalid: Blank Item Category Name");
                            $validationErr=true;
                            break 2;                            
                        }
                        // if($this->exlIsValidCode($val1)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Item Category Name ".$val1." Space not allowed or Only Alpha Numeric allowed" );
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        // if($this->exlIsValidLen($val1,10)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Length: Item Category Name ".$val1." can have max 10 character.");
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        //----------------
                        //check code
                        $resdata = $this->exlIsItemCategory($val1);
                        if($resdata["result"]==true){
                            $validationErr=false; 
                            $headerArr[$hIndex]["header"]["item_category_id"]=$resdata["id"];
                        
                        }else {
                            $validationErr=true;
                            $headerArr[$hIndex]["header"]["item_category_id"]="";
                            $this->appendLogData($logfile,"Item ".$hIndex.": Item Category Name ".$val1." not found.");
                            break 2; 
                        }
                    } //item_category_name

                    if($key1=="default_store_name"){
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."-Invalid: Blank Default Store Name");
                            $validationErr=true;
                            break 2;                            
                        }
                        // if($this->exlIsValidCode($val1)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Default Store Name ".$val1." Space not allowed or Only Alpha Numeric allowed" );
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        // if($this->exlIsValidLen($val1,10)==true){
                        //     $validationErr=false; 
                        // }else {
                        //     $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Length: Default Store Name ".$val1." can have max 10 character.");
                        //     $validationErr=true;
                        //     break 2;                            
                        // }
                        //----------------
                        //check code
                        $resdata = $this->exlIsDefStore($val1);
                        if($resdata["result"]==true){
                            $validationErr=false; 
                            $headerArr[$hIndex]["header"]["default_store_id"]=$resdata["id"];
                        
                        }else {
                            $validationErr=true;
                            $headerArr[$hIndex]["header"]["default_store_id"]="";
                            $this->appendLogData($logfile,"Item ".$hIndex.": Default Store Name ".$val1." not found.");
                            break 2; 
                        }
                    } //default_store_name

                    if($key1=="hsn_code"){
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."-Invalid: Blank HSN Code");
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidCode($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: HSN Code ".$val1." Space not allowed or Only Alpha Numeric allowed" );
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidLen($val1,10)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Length: HSN Code ".$val1." can have max 10 character.");
                            $validationErr=true;
                            break 2;                            
                        }
                        //----------------
                        //check code
                        $resdata = $this->exlIsHSN($val1);
                        if($resdata["result"]==true){
                            $validationErr=false; 
                            $headerArr[$hIndex]["header"]["hsn_id"]=$resdata["id"];
                        
                        }else {
                            $validationErr=true;
                            $headerArr[$hIndex]["header"]["hsn_id"]="";
                            $this->appendLogData($logfile,"Item ".$hIndex.": HSN Code ".$val1." not found.");
                            break 2; 
                        }
                    } //hsn_code

                    if($key1=="standard_custom_duty_rate_percentage")
                    {
                        if(trim($val1)!=""){

                            if($this->exlIsValidLen($val1,8)==true){
                                        $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Standard Custom Duty Rate % =".$val1." can have max 8 character.");
                                $validationErr=true;
                                break 2;                            
                            }                             
                            $tmpcdrate_per = explode(".",$val1);
                            if(isset($tmpcdrate_per[0])){
                                if($this->exlIsValidLen($tmpcdrate_per[0],3)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Standard Custom Duty Rate % =".$val1." can have max 3 character before decimal.");
                                    $validationErr=true;
                                    break 2;                            
                                } 
                            }
                            
                            $tmpcdrate_per = explode(".",$val1);
                            if(isset($tmpcdrate_per[1])){
                                if($this->exlIsValidLen($tmpcdrate_per[1],4)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Standard Custom Duty Rate % =".$val1." can have max 4 character after decimal.");
                                    $validationErr=true;
                                    break 2;                            
                                } 
                            }                                
                            if($this->exlIsNum($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid Standard Custom Duty Rate % = ".$val1.",  Only Number allowed. " );
                                $validationErr=true;
                                break 2;                            
                            }
                        }        
                    }// standard_custom_duty_rate_percentage

                    if($key1=="inventory_valuation_method")
                    {                        
                        if(trim($val1)=="FIFO" || trim($val1)=="Weighted Average" ){
                            $validationErr=false; 
                            
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Material Type ".$val1.". Allow values are FIFO/Weighted Average " );                               
                            $validationErr=true;
                            break 2;                            
                        }       
                            
                    }// inventory_valuation_method

                    if($key1=="business_unit_name"){                        
                        if(trim($val1)!=""){
                            // if($this->exlIsValidCode($val1)==true){
                            //     $validationErr=false; 
                            // }else {
                            //     $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Business Unit Name ".$val1." Space not allowed or Only Alpha Numeric allowed" );
                            //     $validationErr=true;
                            //     break 2;                            
                            // }
                            // if($this->exlIsValidLen($val1,10)==true){
                            //     $validationErr=false; 
                            // }else {
                            //     $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Length: Business Unit Name ".$val1." can have max 10 character.");
                            //     $validationErr=true;
                            //     break 2;                            
                            // }
                            //----------------
                            //check code
                            $resdata = $this->exlIsBUnit($val1);
                            if($resdata["result"]==true){
                                $validationErr=false; 
                                $headerArr[$hIndex]["header"]["business_unit_id"]=$resdata["id"];
                            
                            }else {
                                $validationErr=true;
                                $headerArr[$hIndex]["header"]["business_unit_id"]="";
                                $this->appendLogData($logfile,"Item ".$hIndex.": Business Unit Name ".$val1." not found.");
                                break 2; 
                            }


                        }                        
                    } //business_unit_name

                    if($key1=="standard_rate")
                    {
                        if(trim($val1)!=""){
                            if($this->exlIsValidLen($val1,13)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Standard Rate =".$val1." can have max 13 character.");
                                $validationErr=true;
                                break 2;                            
                            } 
                            $tmpstd_rate = explode(".",$val1);
                            if($this->exlIsValidLen($tmpstd_rate[0],7)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Standard Rate =".$val1." can have max 13 character and 7 digit before decimal.");
                                $validationErr=true;
                                break 2;                            
                            } 
                            if($this->exlIsNum($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid Standard Rate = ".$val1.",  Only Number allowed. " );
                                $validationErr=true;
                                break 2;                            
                            }
                        }        
                    }// standard_rate

                    if($key1=="standard_sws_rate_percentage")
                    {
                        if(trim($val1)!=""){
                            if($this->exlIsValidLen($val1,8)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Standard SWS Rate % =".$val1." can have max 8 character.");
                                $validationErr=true;
                                break 2;                            
                            } 
                            $tmpssrate_per = explode(".",$val1);
                            if(isset($tmpssrate_per[0])){
                                if($this->exlIsValidLen($tmpssrate_per[0],3)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Standard SWS Rate % =".$val1." can have max 8 character and 3 digit before decimal.");
                                    $validationErr=true;
                                    break 2;                            
                                } 
                            }                                
                            if(isset($tmpssrate_per[1])){
                                if($this->exlIsValidLen($tmpssrate_per[1],4)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Standard SWS Rate % =".$val1." can have max 8 character and 4 digit after decimal.");
                                    $validationErr=true;
                                    break 2;                            
                                } 
                            }    
                            if($this->exlIsNum($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid Standard SWS Rate % = ".$val1.",  Only Number allowed. " );
                                $validationErr=true;
                                break 2;                            
                            }
                        }        
                    }// standard_sws_rate_percentage            

                    if($key1=="minimum_level")
                    {
                        if(trim($val1)!=""){
                            if($this->exlIsValidLen($val1,13)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Minimum Level =".$val1." can have max 13 character.");
                                $validationErr=true;
                                break 2;                            
                            } 
                            $tmpmin_lavel = explode(".",$val1);
                            if($this->exlIsValidLen($tmpmin_lavel[0],9)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Minimum Level =".$val1." can have max 13  character and 9 digit before decimal.");
                                $validationErr=true;
                                break 2;                            
                            } 
                            if($this->exlIsNum($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid Minimum Level = ".$val1.",  Only Number allowed. " );
                                $validationErr=true;
                                break 2;                            
                            }
                        }        
                    }// minimum_level

                    if($key1=="reorder_level")
                    {
                        if(trim($val1)!=""){
                            if($this->exlIsValidLen($val1,13)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Reorder Level =".$val1." can have max 13 character.");
                                $validationErr=true;
                                break 2;                            
                            } 
                            $tmpreord_lavel = explode(".",$val1);
                            if($this->exlIsValidLen($tmpreord_lavel[0],9)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Minimum Level =".$val1." can have max 13  character and 9 digit before decimal.");
                                $validationErr=true;
                                break 2;                            
                            } 
                            if($this->exlIsNum($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid Reorder Level = ".$val1.",  Only Number allowed. " );
                                $validationErr=true;
                                break 2;                            
                            }
                        }        
                    }// reorder_level

                    if($key1=="maximum_level")
                    {
                        if(trim($val1)!=""){
                            if($this->exlIsValidLen($val1,13)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Maximum Level =".$val1." can have max 13 character.");
                                $validationErr=true;
                                break 2;                            
                            } 
                            $tmpmax_lavel = explode(".",$val1);
                            if($this->exlIsValidLen($tmpmax_lavel[0],9)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Minimum Level =".$val1." can have max 13  character and 9 digit before decimal.");
                                $validationErr=true;
                                break 2;                            
                            } 
                            if($this->exlIsNum($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid Maximum Level = ".$val1.",  Only Number allowed. " );
                                $validationErr=true;
                                break 2;                            
                            }
                        }        
                    }// maximum_level

                    if($key1=="item_specification")
                    {
                        if(trim($val1)!=""){
                            if($this->exlIsValidLen($val1,200)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Item Specification =".$val1." can have max 200 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }        
                    }// item_specification

                    if($key1=="check_flag_qc_applicable")
                    {
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."-Invalid: Blank QC Applicable");
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidLen($val1,1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: QC Applicable value= ".$val1.", can have max 1 character.");
                            $validationErr=true;
                            break 2;                            
                        } 
                        if(trim(strtolower($val1))=="1" || trim(strtolower($val1))=="0"){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: QC Applicablevalue= ".$val1.", can be 1 or 0.");
                            $validationErr=true;
                            break 2;                            
                        }                              
                    }// check_flag_qc_applicable

                    if($key1=="check_flag_serial_no_applicable")
                    {
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."-Invalid: Blank Serial No Applicable ");
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidLen($val1,1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Serial No Applicable  value= ".$val1.", can have max 1 character.");
                            $validationErr=true;
                            break 2;                            
                        } 
                        if(trim(strtolower($val1))=="1" || trim(strtolower($val1))=="0"){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Serial No Applicable value= ".$val1.", can be 1 or 0.");
                            $validationErr=true;
                            break 2;                            
                        }                              
                    }// check_flag_serial_no_applicable

                    if($key1=="check_flag_batch_no_lot_no_applicable")
                    {
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."-Invalid: Blank Batch No / Lot No Applicable ");
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidLen($val1,1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Batch No / Lot No Applicable  value= ".$val1.", can have max 1 character.");
                            $validationErr=true;
                            break 2;                            
                        } 
                        if(trim(strtolower($val1))=="1" || trim(strtolower($val1))=="0"){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Batch No / Lot No Applicable value= ".$val1.", can be 1 or 0.");
                            $validationErr=true;
                            break 2;                            
                        }                              
                    }// check_flag_batch_no_lot_no_applicable

                    if($key1=="check_flag_inventory_maintain")
                    {
                        if($this->exlIsBlank($val1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."-Invalid: Blank Inventory Maintain ");
                            $validationErr=true;
                            break 2;                            
                        }
                        if($this->exlIsValidLen($val1,1)==true){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Inventory Maintain  value= ".$val1.", can have max 1 character.");
                            $validationErr=true;
                            break 2;                            
                        } 
                        if(trim(strtolower($val1))=="1" || trim(strtolower($val1))=="0"){
                            $validationErr=false; 
                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Inventory Maintain value= ".$val1.", can be 1 or 0.");
                            $validationErr=true;
                            break 2;                            
                        }                              
                    }// check_flag_inventory_maintain

                    if($key1=="alps_specific_sap_customer_code")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: ALPS Specific - SAP Customer Code ".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_sap_customer_code   

                    if($key1=="alps_specific_sap_customer_name")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,100)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: ALPS Specific - SAP Customer Name ".$val1." can have max 100 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_sap_customer_name     

                    if($key1=="alps_specific_sap_part_number")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: ALPS Specific - SAP Part Number ".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_sap_part_number   
                   
                    if($key1=="alps_specific_sap_part_description")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,100)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: ALPS Specific - SAP Part Description ".$val1." can have max 100 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_sap_part_description   
                    
                    if($key1=="alps_specific_sap_customer_part_no")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: ALPS Specific - SAP Customer Part No - ".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_sap_customer_part_no   
                    
                    if($key1=="alps_specific_sap_market_set_code")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: ALPS Specific - SAP Market & Set Code - ".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_sap_market_set_code  

                    if($key1=="alps_specific_rounding_value_lot_size_qty")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,15)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: ALPS Specific - Rounding Value/LOT Size Qty - ".$val1." can have max 15 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                            $tmpmax_lavel = explode(".",$val1);
                            if($this->exlIsValidLen($tmpmax_lavel[0],12)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Minimum Level =".$val1." can have max 15  character and 12 digit before decimal.");
                                $validationErr=true;
                                break 2;                            
                            } 
                            if($this->exlIsNum($val1)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid ALPS Specific - Rounding Value/LOT Size Qty= ".$val1.",  Only Number allowed. " );
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_rounding_value_lot_size_qty 

                    if($key1=="alps_specific_alps_part_no")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: ALPS Specific - ALPS Part No - ".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_alps_part_no   

                    if($key1=="alps_specific_customer_part_no")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: ALPS Specific - Customer Part No - ".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_customer_part_no   
                    
                    if($key1=="alps_specific_oem_part_no")
                    {
                        if(trim($val1)!=""){                            
                            if($this->exlIsValidLen($val1,30)==true){
                                $validationErr=false; 
                            }else {
                                $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: ALPS Specific - OEM Part No - ".$val1." can have max 30 character.");
                                $validationErr=true;
                                break 2;                            
                            }
                        }
                    }// alps_specific_oem_part_no                       
                                        

                //------------------  
                }//--- header foreach endloop
               
                //-------------ATTRIBUTE BEGIN
                foreach($hRowData["attribute"] as $atindex => $attrow){
                    //dump($attrow);
                    foreach ($attrow as $attkey1 => $attval1) {
                        //validation
                        if($attkey1=="attribute_attribute_code")
                        {
                            if(trim($attval1)!=""){                            
                               
                                if($this->exlIsValidCode($attval1)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Attribute - Attribute Code ".$attval1." Space not allowed or Only Alpha Numeric allowed" );
                                    $validationErr=true;
                                    break 3;                            
                                }
                                if($this->exlIsValidLen($attval1,20)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Attribute - Attribute Code ".$attval1." can have max 20 character.");
                                    $validationErr=true;
                                    break 3;                            
                                }
                                //check code
                                $resdata = $this->exlIsAttCode($attval1);
                                if($resdata["result"]==true){
                                    $validationErr=false; 
                                    $headerArr[$hIndex]["attribute"][$atindex]["attribute_attribute_id"]=$resdata["id"];
                                }else {
                                    $validationErr=true;
                                    $headerArr[$hIndex]["attribute"][$atindex]["attribute_attribute_id"]="";
                                    $this->appendLogData($logfile,"Item ".$hIndex.": Invalid: Attribute - Attribute Code ".$attval1." not found.");
                                    break 3; 
                                }
                            }
                        }// attribute_attribute_code  

                        $attcode = $headerArr[$hIndex]["attribute"][$atindex]["attribute_attribute_code"];
                        $attid = $headerArr[$hIndex]["attribute"][$atindex]["attribute_attribute_id"];
                        if($attkey1=="attribute_value"){                            
                            if(trim($attcode)!=""){
                                if($this->exlIsBlank($attval1)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Invalid Item ".$hIndex.": Blank Attribute- Value" );
                                    $validationErr=true;
                                    break 3;                            
                                }
                                
                                if($this->exlIsValidCode($attval1)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Attribute- Value= ".$attval1." Space not allowed or Only Alpha Numeric allowed" );
                                    $validationErr=true;
                                    break 3;                            
                                }
                                if($this->exlIsValidLen($attval1,50)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Length: Attribute- Value= ".$attval1." can have max 50 character.");
                                    $validationErr=true;
                                    break 3;                            
                                }

                                //check code
                                $resdata = $this->exlIsAttValue($attid,$attval1);
                                if($resdata["result"]==true){
                                    $validationErr=false;                                     
                                    $headerArr[$hIndex]["attribute"][$atindex]["attribute_value_id"]=$resdata["id"];
                                    
                                }else {
                                    $validationErr=true;
                                    $headerArr[$hIndex]["attribute"][$atindex]["attribute_value_id"]="";
                                    $this->appendLogData($logfile,"Item ".$hIndex.": Attribute- Value= ".$attval1." not found.");
                                    break 3; 
                                } 
                            } // not blank                       
                        } //attribute_value
                    }//att row end-loop
                }//att grid loop
                //-------------ATTRIBUTE END
                //-------------TECHNICAL SPEC. BEGIN
                foreach($hRowData["ts"] as $tsindex => $tsrow){
                    foreach ($tsrow as $tskey1 => $tsval1) {
                        //validation
                        if($tskey1=="technical_specification_ts_type")
                        {
                            if(trim($tsval1)!=""){             
                                if($this->exlIsValidLen($tsval1,50)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Technical Specification - TS Type=".$tsval1." can have max 50 character.");
                                    $validationErr=true;
                                    break 3;                            
                                }                                
                            }
                        }// technical_specification_ts_type  

                        $tscode =trim($headerArr[$hIndex]["ts"][$tsindex]["technical_specification_ts_type"]);
                        if($tskey1=="technical_specification_value"){                            
                            if(trim($tscode)!=""){
                                if($this->exlIsBlank($tsval1)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Invalid Item ".$hIndex.": Blank Technical Specification- Value" );
                                    $validationErr=true;
                                    break 3;                            
                                }
                                if($this->exlIsValidLen($tsval1,100)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: Length: Technical Specification- Value= ".$tsval1." can have max 100 character.");
                                    $validationErr=true;
                                    break 3;                            
                                }
                            } // not blank  
                        } //technical_specification_value
                    }//att row end-loop
                }//ts grid loop
                //-------------TECHNICAL SPEC END
                //------------------------
                //-------------UOM Conversion BEGIN
                $anyToUOM = false;
                foreach($hRowData["uomcon"] as $tuomindex=>$tuomrow){                
                    //--one To Uom row is mandatory
                    if(trim($tuomrow["uom_conversion_to_uom_code"])!="")
                    {
                        $anyToUOM = true;   
                    }
                    //check to uom and qty 
                    if(trim($tuomrow["uom_conversion_to_uom_code"])!=""){                    
                        if(trim($tuomrow["uom_conversion_to_uom_code"])!="" && trim($tuomrow["uom_conversion_qty"])!="" ){
                            $validationErr=false; 

                        }else {
                            $this->appendLogData($logfile,"Item ".$hIndex."- Invalid UOM Conversion Details: To UOM -".$tuomrow["uom_conversion_to_uom_code"].". Please insert the value for Qty.");
                            $validationErr=true;
                            break 2;                            
                        }
                    }
                    //--------------------          
                    foreach ($tuomrow as $tukey => $tuval) {

                        if(trim($headerArr[$hIndex]["uomcon"][$tuomindex]["uom_conversion_to_uom_code"])!="")
                        {
                            if($tukey=="uom_conversion_to_uom_code")
                            {                                
                                if($this->exlIsValidCode($tuval)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Item ".$hIndex."- UOM Conversion Details Invalid - To UOM Code= ".$tuval.",  Space not allowed or Only Alpha Numeric allowed. " );
                                    $validationErr=true;
                                    break 2;                            
                                }
                                if($this->exlIsValidLen($tuval,20)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: UOM Conversion Details - To UOM Code= ".$tuval." can have max 20 character.");
                                    $validationErr=true;
                                    break 2;                            
                                }
                                // $muom = strtoupper($headerArr[$hIndex]["header"]["main_uom_code"]);
                                // if(strtoupper($tuval)!=$muom){
                                //     $validationErr=false; 
                                // }else{
                                //     $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: UOM Conversion Details - Main UoM and To UOM Code ".$tuval." can not be same.");
                                //     $validationErr=true;
                                //     break 2;      
                                // }
                                //check code
                                $resdata = $this->exlIsMUOMExists($tuval);
                                if($resdata["result"]==true){
                                    $validationErr=false; 
                                    $headerArr[$hIndex]["uomcon"][$tuomindex]["uom_conversion_to_uom_id"]=$resdata["id"];
                                
                                }else {
                                    $validationErr=true;
                                    $headerArr[$hIndex]["uomcon"][$tuomindex]["uom_conversion_to_uom_id"]="";
                                    $this->appendLogData($logfile,"Item ".$hIndex.": UOM Conversion Details - To UOM Code ".$tuval." not found.");
                                    break 2; 
                                }
                                
                            }// uom_conversion_to_uom_code 

                            if($tukey=="uom_conversion_qty")
                            {
                                if(trim($tuval)!=""){
                                    if($this->exlIsValidLen($tuval,13)==true){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: UOM Conversion Details - Qty ".$tuval." can have max 13 character.");
                                        $validationErr=true;
                                        break 2;                            
                                    }  
                                    $tmpum_qty = explode(".",$tuval);
                                    if($this->exlIsValidLen($tmpum_qty[0],9)==true){
                                              $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: UOM Conversion Details - Qty =".$tuval." can have max 13 character and 9 digit before decimal.");
                                        $validationErr=true;
                                        break 2;                            
                                    } 

                                    if($this->exlIsNum($tuval)==true){
                                        $validationErr=false; 
                                    }else {
                                        $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: UOM Conversion Details - Qty = ".$tuval.",  Only Number allowed. " );
                                        $validationErr=true;
                                        break 2;                            
                                    }                                           
                                }
                                
                            }// uom_conversion_qty  
                        ///------------------------------------
                        }
                    }//uomcon foreach row end-loop
                }//uomcon all rows grid loop
                            
                //To UOM  
                if($anyToUOM==true){
                    $validationErr=false; 
                }else {
                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid UOM Conversion Details - To UOM is blank. Please enter To UOM data in at least one row.");
                    $validationErr=true;
                    break 1;                            
                }
                //-------------UOM CONV END
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
                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid :  UDF Data. ");
                    $validationErr=true;
                    break 1; 
                }
                // DUMP($udfDbLabel);
                $dif_udfs=array_diff($udfDbLabel,$exlUdfLabel); // check if Item has same db udf label data
                //($dif_udfs);
                if(!empty($dif_udfs)){
                    foreach($dif_udfs as $dkey=>$dval){
                        $this->appendLogData($logfile,"Item ".$hIndex." - ".$dval." - UDF Field not found. (Case sensitive)");
                        $validationErr=true;                                  
                    }
                    break 1;
                }else{                    
                    $validationErr=false; 
                }
                //check udf value type
                foreach($hRowData["udf"] as $udfkey=>$udfrow){
                        $exlUdfLabel[] = strtoupper(trim($udfrow["udf_udf_label"]));  //fetch excel udf label
                }

                /////----------------------------------------
                foreach($hRowData["udf"] as $udfIndex=>$udfrow2){
                    foreach ($dbUDFData as $dbukey => $dburow) {
                        if( strtoupper($udfrow2["udf_udf_label"])==strtoupper($dburow->LABEL) )
                        {  
                            $headerArr[$hIndex]["udf"][$udfIndex]["udf_id"]=$dburow->UDFITEMID;                            
                            $headerArr[$hIndex]["udf"][$udfIndex]["udf_valuetype"]=strtoupper($dburow->VALUETYPE);  
                            
                            if(strtoupper($dburow->ISMANDATORY)=="1"){
                                if( $this->exlIsBlank(trim($udfrow2["udf_value"]))==true){
                                    $validationErr=false; 
                                }else{
                                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid, Blank UDF value, Lable=".$udfrow2["udf_udf_label"]." value can not be left blank.");
                                    $validationErr=true;
                                    break 3;                                     
                                }
                            }
                            
                            //dd($udfrow2["udf_value"]);
                            if(strtoupper($dburow->VALUETYPE)=="NUMERIC"){
                                if(is_numeric($udfrow2["udf_value"])){
                                    $validationErr=false; 
                                }else{
                                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: UDF Numeric type, Lable=".$udfrow2["udf_udf_label"]." value=".$udfrow2["udf_value"]);
                                    $validationErr=true;
                                    break 3;                                     
                                }
                            }
                            if(strtoupper($dburow->VALUETYPE)=="BOOLEAN"){
                                if(is_bool(boolval($udfrow2["udf_value"]))){
                                    $validationErr=false;
                                }else{
                                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: UDF Boolean type,  Lable=".$udfrow2["udf_udf_label"].", value=".$udfrow2["udf_value"]);
                                    $validationErr=true;
                                    break 3; 
                                }
                            }
                            
                            if(strtoupper($dburow->VALUETYPE)=="DATE"){
                                if($this->exlIsValidDate($udfrow2["udf_value"])==true){
                                    $validationErr=false;
                                    $headerArr[$hIndex]["udf"][$udfIndex]["udf_value"]= Date('Y-m-d',strtotime($udfrow2["udf_value"]));
                                }else{
                                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid :  UDF Date type,  Lable=".$udfrow2["udf_udf_label"].", value=".$udfrow2["udf_value"]);
                                    $validationErr=true;
                                    break 3; 
                                }
                            }      
                            if(strtoupper($dburow->VALUETYPE)=="TIME"){
                                if($this->exlIsValidTime($udfrow2["udf_value"])==true){
                                    $validationErr=false;
                                }else{
                                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid :  UDF Time type,  Lable=".$udfrow2["udf_udf_label"].", value=".$udfrow2["udf_value"]);
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
                                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid :  UDF Combo value,  Lable=".$udfrow2["udf_udf_label"].", value=".$udfrow2["udf_value"]);
                                    $validationErr=true;
                                    break 3; 
                                }
                            }                        
                            
                        }
                    }
                    
                    foreach ($udfrow2 as $ukey3 => $uvalue3) 
                    {  
                        if(trim($udfrow2["udf_udf_label"])!=""){

                            if($ukey3=="udf_value"){   
                                
                                if(strtoupper($dburow->ISMANDATORY)=="1"){
                                    if( $this->exlIsBlank(trim($uvalue3))==true){
                                        $validationErr=false; 
                                    }else{
                                        $this->appendLogData($logfile,"Item ".$hIndex."- Invalid, Blank UDF value, Lable=".$udfrow2["udf_udf_label"]." value can not be left blank.");
                                        $validationErr=true;
                                        break 3;                                     
                                    }
                                }    

                                if($this->exlIsValidLen($uvalue3,100)==true){
                                    $validationErr=false; 
                                }else {
                                    $this->appendLogData($logfile,"Item ".$hIndex."- Invalid: UDF Value ".$uvalue3." can have max 100 character.");
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
        
        

        if($validationErr){
                fclose($logfile);
                //echo "<br>--valiation error--";
                return redirect()->route("master",[72,"importdata"])->with("logerror",$logfile_path);  
        }
        //dump($headerArr);
        //DUMP("--validation executiaon complete");
            ///----------------------------------------save data begin
            foreach($headerArr as $hIndex=>$hRowData)
            {
                
                $ICODE          =  strtoupper(trim($hRowData["header"]["item_code"]));      
                $NAME           =  trim($hRowData["header"]["item_name"]);  
                $PARTNO         =  trim($hRowData["header"]["item_part_no"]);             
                $DRAWINGNO      =  trim($hRowData["header"]["item_drawing_no"]) !="" ? $hRowData["header"]["item_drawing_no"]:NULL;
                $CLASSID_REF    =  intval($hRowData["header"]["inventory_class_id"]); 
                
                $SAP_CUSTOMER_CODE      = trim($hRowData["header"]["alps_specific_sap_customer_code"]) !="" ? $hRowData["header"]["alps_specific_sap_customer_code"]:NULL;
                
                $SAP_CUSTOMER_NAME      = trim($hRowData["header"]["alps_specific_sap_customer_name"]) !="" ? $hRowData["header"]["alps_specific_sap_customer_name"]:NULL;  
                $SAP_PART_NO            = trim($hRowData["header"]["alps_specific_sap_part_number"]) !="" ? $hRowData["header"]["alps_specific_sap_part_number"]:NULL;   
                $SAP_PART_DESC          = trim($hRowData["header"]["alps_specific_sap_part_description"]) !="" ? $hRowData["header"]["alps_specific_sap_part_description"]:NULL;   
                $SAP_CUST_PARTNO        = trim($hRowData["header"]["alps_specific_sap_customer_part_no"]) !="" ? $hRowData["header"]["alps_specific_sap_customer_part_no"]:NULL;   
                $SAP_MARTKET_SETCODE    = trim($hRowData["header"]["alps_specific_sap_market_set_code"]) !="" ? $hRowData["header"]["alps_specific_sap_market_set_code"]:NULL; 
                $LOTSIZEQTY             = trim($hRowData["header"]["alps_specific_rounding_value_lot_size_qty"]) !="" ? floatval($hRowData["header"]["alps_specific_rounding_value_lot_size_qty"]):NULL;
                $ALPS_PART_NO           = trim($hRowData["header"]["alps_specific_alps_part_no"]) !="" ? $hRowData["header"]["alps_specific_alps_part_no"]:NULL; 
                $CUSTOMER_PART_NO       = trim($hRowData["header"]["alps_specific_customer_part_no"]) !="" ? $hRowData["header"]["alps_specific_customer_part_no"]:NULL;   
                $OEM_PART_NO            = trim($hRowData["header"]["alps_specific_oem_part_no"]) !="" ? $hRowData["header"]["alps_specific_oem_part_no"]:NULL; 
                
                $MAIN_UOMID_REF =  intval($hRowData["header"]["main_uom_id"]);   
                $ALT_UOMID_REF =   intval($hRowData["header"]["alt_uom_id"]);   
                $ITEM_DESC =      trim($hRowData["header"]["item_description"]) !="" ? $hRowData["header"]["item_description"]:'';   
                $ITEMGID =       intval($hRowData["header"]["item_group_id"]);       
                $ISGID =         intval( $hRowData["header"]["item_sub_group_id"] );   

                $ICID =      intval( $hRowData["header"]["item_category_id"]);   // item category 
                $STID =      intval( $hRowData["header"]["default_store_id"]);     //store id
                $HSNID =     trim($hRowData["header"]["hsn_id"]) !="" ? intval($hRowData["header"]["hsn_id"]):NULL;    
                $IVM =        $hRowData["header"]["inventory_valuation_method"];  
                $BUID_REF =  trim($hRowData["header"]["business_unit_id"]) !="" ? intval($hRowData["header"]["business_unit_id"]):NULL;   

                $STDCOST =   trim($hRowData["header"]["standard_rate"]) !="" ? floatval($hRowData["header"]["standard_rate"]):0;    
                $MINLEVEL =  trim($hRowData["header"]["minimum_level"]) !="" ? floatval($hRowData["header"]["minimum_level"]):0;             
                $REORDER =   trim($hRowData["header"]["reorder_level"]) !="" ? floatval($hRowData["header"]["reorder_level"]):0;   
                $MAXLEVEL =  trim($hRowData["header"]["maximum_level"]) !="" ? floatval($hRowData["header"]["maximum_level"]):0;    
                $ITEM_SPECI = trim($hRowData["header"]["item_specification"]) !="" ? $hRowData["header"]["item_specification"]:'';  

                $DEACTIVATED = 0;
                $DODEACTIVATED = NULL;
                
                $CUSTOM_DUTY_RATE = trim($hRowData["header"]["standard_custom_duty_rate_percentage"]) !="" ? floatval($hRowData["header"]["standard_custom_duty_rate_percentage"]):0;  
                $STD_SWS_RATE = trim($hRowData["header"]["standard_sws_rate_percentage"]) !="" ? floatval($hRowData["header"]["standard_sws_rate_percentage"]):0;   

                $CYID_REF = intval(Auth::user()->CYID_REF);
                $BRID_REF = intval(Session::get('BRID_REF'));
                $FYID_REF = intval( Session::get('FYID_REF') ); 
                    
                $QCA =       trim($hRowData["header"]["check_flag_qc_applicable"]) !="1" ? 0:1;
                $SRNO =      trim($hRowData["header"]["check_flag_serial_no_applicable"]) !="1" ? 0:1; 
                $BATCHNO =   trim($hRowData["header"]["check_flag_batch_no_lot_no_applicable"]) !="1" ? 0:1;  
                $INV =       trim($hRowData["header"]["check_flag_inventory_maintain"]) !="1" ? 0:1;     
                // $TCS =       trim($request['TCS']); 
                $TCS =       0;  


                $BARCODE_APPLICABLE     =   isset($hRowData["header"]["check_flag_barcode_applicable"])?trim($hRowData["header"]["check_flag_barcode_applicable"]):NULL; 
                $SERIALNO_MODE          =   isset($hRowData["header"]["check_flag_serialno_mode"])?trim($hRowData["header"]["check_flag_serialno_mode"]):NULL; 
                $SERIALNO_PREFIX        =   isset($hRowData["header"]["check_flag_serialno_prefix"])?trim($hRowData["header"]["check_flag_serialno_prefix"]):NULL; 
                $SERIALNO_STARTS_FROM   =   isset($hRowData["header"]["check_flag_serialno_starts_from"])?trim($hRowData["header"]["check_flag_serialno_starts_from"]):NULL; 
                $SERIALNO_SUFFIX        =   isset($hRowData["header"]["check_flag_serialno_suffix"])?trim($hRowData["header"]["check_flag_serialno_suffix"]):NULL; 
                $SERIALNO_MAX_LENGTH    =   isset($hRowData["header"]["check_flag_serialno_max_length"])?trim($hRowData["header"]["check_flag_serialno_max_length"]):NULL; 



                //ATTRIBUTES
                $attrData=[];
                foreach($hRowData["attribute"] as $attrindex=>$attrrow)
                {
                    if(trim($attrrow['attribute_attribute_id'])!=""){
                        $attrData[$attrindex]['ATTRIBUTECODE']  =  intval($attrrow["attribute_attribute_id"]);
                        $attrData[$attrindex]['VALUE']          =  intval( $attrrow["attribute_value_id"] );
                    }
                }

                if(!empty($attrData)){
                    $attrwrapped["ATTRIBUTE"] = $attrData;    
                    $attr_xml = ArrayToXml::convert($attrwrapped);
                    $ATTRIBUTEXML = $attr_xml;
                }
                else{
                    $ATTRIBUTEXML = NULL;
                }

                //TECHNICAL SPECIFICATION  
                $techspec_Data=[];
                foreach($hRowData["ts"] as $techsindex=>$techsrow)
                {
                    if(trim($techsrow['technical_specification_ts_type'])!=""){
                        $techspec_Data[$techsindex]['TSTYPE']  =   $techsrow["technical_specification_ts_type"];
                        $techspec_Data[$techsindex]['VALUE']   =   $techsrow["technical_specification_value"];
                    }
                }
                
                if(count($techspec_Data)>0){
                    $techspec_wrapped["TECHNICALSPECIFICATION"] = $techspec_Data;        
                    $techspec__xml = ArrayToXml::convert($techspec_wrapped);
                    $TECHNICALXML  = $techspec__xml;        

                }else{

                    $TECHNICALXML = NULL;
                }

                $IMAGEXML = 'NULL';

                //UOM CONVERSION
                $uomconv_Data = [];
                foreach($hRowData["uomcon"] as $ucindex=>$ucrow)
                {
                    if(trim($ucrow['uom_conversion_to_uom_code'])!=""){
                        $uomconv_Data[$ucindex]['FROMUOM']  =  intval( $hRowData["header"]["main_uom_id"]);
                        $uomconv_Data[$ucindex]['FROMQTY']  =   1;
                        $uomconv_Data[$ucindex]['TOUOM']    =  intval($ucrow["uom_conversion_to_uom_id"]);
                        $uomconv_Data[$ucindex]['TOQTY']    =  floatval($ucrow["uom_conversion_qty"]);
                    }
                }
                
                if($MAIN_UOMID_REF != $ALT_UOMID_REF){
                    $uomconv_wrapped["UOM"] = $uomconv_Data;        
                    $uomconv__xml = ArrayToXml::convert($uomconv_wrapped);
                    $XMLUOM  = $uomconv__xml;
                }
                else{
                    $XMLUOM  = NULL; 
                }
               
                ////UDF FIELDS
                $udffield_Data = [];
                foreach($hRowData["udf"] as $uindex=>$urow){
                    if(trim($urow['udf_udf_label'])!="")
                    {
                        $udffield_Data[$uindex]['UDFFIELDS'] = $urow['udf_id'];  

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

                $VTID = $this->vtid_ref;
                $USERID = Auth::user()->USERID;
                $UPDATE ="ADD";
                $UPDATE =  Date('Y-m-d');
                $UPTIME = Date('h:i:s.u');

                $ACTION     =   "ADD";
                $IPADDRESS  =   $request->getClientIp();  //S-Service
                $EXPIRY_APPLICABLE=0;
                $INCA=0;
                $BIN=0;
                $WARA=0;
                $LEAD_DAYS=0;
                $SHELF_LIFE=0;
                $WARA_MONTH=0;
                
                $ITEM_TYPE = trim($headerArr[$hIndex]["header"]["item_type_code"]);               
                if(trim($ITEM_TYPE)=="S-Service"){
                    $MATERIAL_TYPE 	=   NULL;
                    $GLID_REF 		=  intval(trim($headerArr[$hIndex]["header"]["material_type_code_or_gl_code"]));   
                                   
                }else{
                    $MATERIAL_TYPE 	=   trim($headerArr[$hIndex]["header"]["material_type_code_or_gl_code"]); 
                    $GLID_REF 		=   NULL;   
                }
                   // $ITEM_TYPE 	    =   isset($request['ITEM_TYPE']) && !is_null($request['ITEM_TYPE'])?trim($request['ITEM_TYPE']):NULL;
                   // $MATERIAL_TYPE 	=   isset($request['MATERIAL_TYPE']) && !is_null($request['MATERIAL_TYPE'])?trim($request['MATERIAL_TYPE']):NULL;
                $SAP_CUSTOMER_PARTNO    =   NULL;
                $SAP_MARKET_SETCODE     =   NULL;
                $ROUNDING_VALUE         =   0;

                        
                $save_data = [

                $ICODE,                 $NAME,                  $PARTNO,            $DRAWINGNO,         $CLASSID_REF,       $MAIN_UOMID_REF,        $ALT_UOMID_REF,         $ITEM_DESC,             $ITEMGID,               $ISGID,
                $ICID,                  $STID,                  $HSNID,             $IVM,               $BUID_REF,          $STDCOST,               $MINLEVEL,              $REORDER,               $MAXLEVEL,              $ITEM_SPECI,
                $DEACTIVATED,           $DODEACTIVATED,         $CUSTOM_DUTY_RATE,  $STD_SWS_RATE,      $CYID_REF,          $BRID_REF,              $FYID_REF,              $QCA,                   $SRNO,                  $BATCHNO,
                $INV,                   $TCS,                   $ITEM_TYPE,         $MATERIAL_TYPE,     $GLID_REF,          $SAP_CUSTOMER_CODE,     $SAP_CUSTOMER_NAME,     $SAP_PART_NO,           $SAP_PART_DESC,         $SAP_CUSTOMER_PARTNO,
                $SAP_MARKET_SETCODE,    $ROUNDING_VALUE,        $ATTRIBUTEXML,      $TECHNICALXML,      $XMLUOM,            $XMLUDF,                $VTID,                  $USERID,                $UPDATE,                $UPTIME,            
                $ACTION,                $IPADDRESS,             $ALPS_PART_NO,      $CUSTOMER_PART_NO,  $OEM_PART_NO,       $BARCODE_APPLICABLE,    $SERIALNO_MODE,         $SERIALNO_PREFIX,       $SERIALNO_STARTS_FROM,  $SERIALNO_SUFFIX,   
                $SERIALNO_MAX_LENGTH,   $INCA,                  $BIN,               $WARA,              $LEAD_DAYS,         $SHELF_LIFE,            $WARA_MONTH ];

                
            try {

                $sp_result = DB::select('EXEC SP_ITEM_IN ?,?,?,?,?,?,?,?,?,?,   ?,?,?,?,?,?,?,?,?,?,  ?,?,?,?,?,?,?,?,?,?,  ?,?,?,?,?,?,?,?,?,?,    ?,?,?,?,?,?,?,?,?,?,   ?,?,?,?,?,?,?,?,?,?,  ?,?,?,?,?,?,?', $save_data);
                

            } catch (\Throwable $th) {
                
                    $this->appendLogData($logfile," Item ".$hIndex.": There is some error. Please try after sometime. " );
                    fclose($logfile);
                    return redirect()->route("master",[72,"importdata"])->with("logerror",$logfile_path); 
            
            }

                if(Str::contains(strtoupper($sp_result[0]->RESULT), 'SUCCESS')){
                
                    $this->appendLogData($logfile,"Item ".$hIndex.": Record successfully inserted.","",1 );
                }else{
                    
                    $this->appendLogData($logfile," Item ".$hIndex.": Record not inserted. ".$sp_result[0]->RESULT );
                    fclose($logfile);
                    return redirect()->route("master",[72,"importdata"])->with("logerror",$logfile_path);                     
                }

            }
            ///----------------------------------------save data end    
            fclose($logfile);
            return redirect()->route("master",[72,"importdata"])->with("logsuccess",$logfile_path);      
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
            if(trim($strcode)=="" || !ctype_alnum(trim($strcode)) || strrpos($strcode, " ") || str_contains(trim($strcode),' '))
            {
                return false;
            }else{
                return true;
            }
        }

        public function exlGetItemUDF($cyidRef)
        {
            
            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_ITEM")->select('*')
                        ->whereIn('PARENTID',function($query) use ($cyidRef)
                                    {       
                                    $query->select('UDFITEMID')->from('TBL_MST_UDFFOR_ITEM')
                                                    ->where('STATUS','=','A')
                                                    ->where('PARENTID','=',0)
                                                    ->where('DEACTIVATED','=',0)
                                                    ->where('CYID_REF','=',$cyidRef);
                                                                        
                        })->where('DEACTIVATED','=',0)
                        ->where('STATUS','<>','C')                    
                        ->where('CYID_REF','=',$cyidRef);                                 
                    

            $objUdfItemData = DB::table('TBL_MST_UDFFOR_ITEM')
                ->where('STATUS','=','A')
                ->where('PARENTID','=',0)
                ->where('DEACTIVATED','=',0)
                ->where('CYID_REF','=',$cyidRef)
                ->union($ObjUnionUDF)
                ->get()->toArray();    

            return $objUdfItemData;
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

        public function exlIsDuplicateItemCode($strcode){
            $code = strtoupper(trim($strcode));
            $CYID_REF       =   Auth::user()->CYID_REF;
            $ObjData = DB::select("SELECT top 1 ICODE FROM TBL_MST_ITEM where CYID_REF = ".$CYID_REF." AND ICODE='".$code."' order by ICODE");   

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

        public function exlIsInvClassNameExists($strcode){
            $code = strtolower(trim($strcode));                

            $DataFound = DB::select("SELECT  TOP 1 CLASSID, CLASS_CODE,CLASS_DESC FROM  TBL_MST_INVENTORY_CLASS
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND  lower(LTRIM(RTRIM(CLASS_DESC)))='".$code."' AND STATUS = 'A' ");            

            $resdata=[]; 
            if(!empty($DataFound)){
                $resdata["result"]=true; 
                $resdata["id"]=$DataFound[0]->CLASSID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=0; 
            }
            return $resdata;
        }


        public function exlIsMUOMExists($strcode){
            $code = strtoupper(trim($strcode));
            $CYID_REF       =   Auth::user()->CYID_REF;
            $cur_date = date('Y-m-d');

            $DataFound = DB::select("SELECT  TOP 1 UOMID, UOMCODE FROM  TBL_MST_UOM
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND  CYID_REF = ".$CYID_REF." 
            and UOMCODE='".$code."' AND STATUS = 'A' ");            

            $resdata=[]; 
            if(!empty($DataFound)){
                $resdata["result"]=true; 
                $resdata["id"]=$DataFound[0]->UOMID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;
        }

        public function exlIsAttCode($strcode){
            $code = strtoupper(trim($strcode));
            $CYID_REF       =   Auth::user()->CYID_REF;
            $cur_date = date('Y-m-d');

            $DataFound = DB::select("SELECT  TOP 1 ATTID, ATTCODE FROM  TBL_MST_ATTRIBUTE
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND  CYID_REF = ".$CYID_REF." 
            and ATTCODE='".$code."' AND STATUS = 'A' ");            

            $resdata=[]; 
            if(!empty($DataFound)){
                $resdata["result"]=true; 
                $resdata["id"]=$DataFound[0]->ATTID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;
        }

        public function exlIsGlExists($strcode){
            $code = strtoupper(trim($strcode));
            $CYID_REF       =   Auth::user()->CYID_REF;
            $cur_date = date('Y-m-d');

            $DataFound = DB::select("SELECT  TOP 1 GLID, GLCODE FROM  TBL_MST_GENERALLEDGER
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND  CYID_REF = ".$CYID_REF." 
            and GLCODE='".$code."' AND SUBLEDGER<>1 AND STATUS = 'A' ");            

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

        public function exlIsItemGroup($strcode){
            $code = strtolower(trim($strcode));
            $CYID_REF       =   Auth::user()->CYID_REF;
            
            $DataFound = DB::select("SELECT  TOP 1 ITEMGID, GROUPCODE,GROUPNAME FROM  TBL_MST_ITEMGROUP
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND  CYID_REF = ".$CYID_REF." 
            and lower(LTRIM(RTRIM(GROUPNAME)))='".$code."' AND STATUS = 'A' ");            

            $resdata=[]; 
            if(!empty($DataFound)){
                $resdata["result"]=true; 
                $resdata["id"]=$DataFound[0]->ITEMGID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;
        }

        public function exlIsItemCategory($strcode){
            $code = strtolower(trim($strcode));
            $CYID_REF       =   Auth::user()->CYID_REF;
            
            $DataFound = DB::select("SELECT  TOP 1 ICID, ICCODE, DESCRIPTIONS FROM  TBL_MST_ITEMCATEGORY
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND  CYID_REF = ".$CYID_REF." and lower(LTRIM(RTRIM(DESCRIPTIONS)))='".$code."' AND STATUS = 'A' ");            

            $resdata=[]; 
            if(!empty($DataFound)){
                $resdata["result"]=true; 
                $resdata["id"]=$DataFound[0]->ICID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;
        }

        public function exlIsDefStore($strcode){
            $code = strtolower(trim($strcode));
            $CYID_REF       =   Auth::user()->CYID_REF;
           
            $DataFound = DB::select("SELECT  TOP 1 STID, STCODE,NAME FROM  TBL_MST_STORE
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND  CYID_REF = ".$CYID_REF." and lower(LTRIM(RTRIM(NAME)))='".$code."' AND STATUS = 'A' ");            

            $resdata=[]; 
            if(!empty($DataFound)){
                $resdata["result"]=true; 
                $resdata["id"]=$DataFound[0]->STID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;
        }

        public function exlIsHSN($strcode){
            $code = strtoupper(trim($strcode));
            $CYID_REF       =   Auth::user()->CYID_REF;
            $cur_date = date('Y-m-d');

            $DataFound = DB::select("SELECT  TOP 1 HSNID,HSNCODE FROM  TBL_MST_HSN
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND  CYID_REF = ".$CYID_REF." and HSNCODE='".$code."' AND STATUS = 'A' ");            

            $resdata=[]; 
            if(!empty($DataFound)){
                $resdata["result"]=true; 
                $resdata["id"]=$DataFound[0]->HSNID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;
        }

        public function exlIsBUnit($strcode){
            $code = strtolower(trim($strcode));
            $CYID_REF       =   Auth::user()->CYID_REF;
            
            $DataFound = DB::select("SELECT  TOP 1 BUID, BUCODE,BUNAME FROM  TBL_MST_BUSINESSUNIT
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND  CYID_REF = ".$CYID_REF." and lower(LTRIM(RTRIM(BUNAME)))='".$code."' AND STATUS = 'A' ");            

            $resdata=[]; 
            if(!empty($DataFound)){
                $resdata["result"]=true; 
                $resdata["id"]=$DataFound[0]->BUID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;
        }

        

        public function exlIsIndTyp($strcode){
            $code = strtoupper(trim($strcode));
            $CYID_REF       =   Auth::user()->CYID_REF;
            $cur_date = date('Y-m-d');

            $DataFound = DB::select("SELECT  TOP 1 INDSID, INDSCODE FROM  TBL_MST_INDUSTRYTYPE
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND CYID_REF = ".$CYID_REF." 
            and INDSCODE='".$code."' AND STATUS = 'A' ");            

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
            $code = strtoupper(trim($strcode));
            $CYID_REF       =   Auth::user()->CYID_REF;
            $cur_date = date('Y-m-d');

            $DataFound = DB::select("SELECT  TOP 1 INDSVID, INDSVCODE FROM  TBL_MST_INDUSTRYVERTICAL
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND CYID_REF = ".$CYID_REF." 
            and INDSVCODE='".$code."' AND STATUS = 'A' ");            

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
            $cur_date = date('Y-m-d');

            $DataFound = DB::select("SELECT  TOP 1 GSTID, GSTCODE FROM  TBL_MST_GSTTYPE
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
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND  CYID_REF = ".$CYID_REF." and NOA_CODE='".$code."' AND STATUS = 'A' ");            

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
            where  (DEACTIVATED=0 or DEACTIVATED is null) AND   CYID_REF = ".$CYID_REF." and ASSESSEEID_REF='".$asseid."' and CODE='".$tdscode."' AND STATUS = 'A' ");            

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
            $code = strtoupper(trim($strcode));
            $cur_date = date('Y-m-d');

            $DataFound = DB::select("SELECT  TOP 1 CTRYID, CTRYCODE FROM  TBL_MST_COUNTRY
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND CTRYCODE='".$code."' AND STATUS = 'A' ");            

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

        public function exlISubGroupExists($itemgid, $gsubname){
            $item_gid =  trim($itemgid)==""? 0 : $itemgid;
            $subg_name = strtolower(trim($gsubname));
            $objSG = DB::select("SELECT  TOP 1 ISGID, ITEMGID_REF, ISGCODE,DESCRIPTIONS FROM  TBL_MST_ITEMSUBGROUP
            where  ITEMGID_REF='".$item_gid."' and lower(LTRIM(RTRIM(DESCRIPTIONS)))='".$subg_name."' ");  

            $resdata=[]; 
            if(!empty($objSG)){
                $resdata["result"]=true; 
                $resdata["id"]=$objSG[0]->ISGID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;
        }

        public function exlIsAttValue($attrid, $strcode){
            $cur_date = date('Y-m-d');
            $attr_id =  trim($attrid)==""? 0 : $attrid;

            $attr_val = strtoupper(trim($strcode));
            $objdata = DB::select("SELECT  TOP 1 ATTVID,ATTID_REF,VALUE FROM  TBL_MST_ATTRIBUTE_VAL
            where  ATTID_REF=".$attr_id." and VALUE='".$attr_val."' ");  

            $resdata=[]; 
            if(!empty($objdata)){
                $resdata["result"]=true; 
                $resdata["id"]=$objdata[0]->ATTVID; 
            }else{
                $resdata["result"]=false; 
                $resdata["id"]=""; 
            }
            return $resdata;
        }

        public function exlIsStateExists($ctryid, $statecode){
            $cur_date = date('Y-m-d');
            $ctry_id =  trim($ctryid)==""? 0 : $ctryid;

            $state_code = strtoupper(trim($statecode));
            $objState = DB::select("SELECT  TOP 1 STID,STCODE FROM  TBL_MST_STATE
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND  CTRYID_REF='".$ctry_id."' and STCODE='".$state_code."' and STATUS = 'A' ");  

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

        public function exlIsCityExists($ctryid, $stateid, $citycode){
            $cur_date = date('Y-m-d');

            $ctry_id =  trim($ctryid)==""? 0 : $ctryid;
            $stat_id =  trim($stateid)==""? 0 : $stateid;        
            $city_code = strtoupper(trim($citycode));

            $objCity = DB::select("SELECT  TOP 1 CITYID,CITYCODE FROM  TBL_MST_CITY
            where  (DEACTIVATED=0 or DEACTIVATED is null ) AND CTRYID_REF='".$ctry_id."' and STID_REF='".$stat_id."'  and CITYCODE='".$city_code."' and STATUS = 'A' ");  

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


    //display attachments form
   public function attachment($id){

    if(!is_null($id))
    {
        $objItem = DB::table("TBL_MST_ITEM")
                            ->where('ITEMID','=',$id)
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
                        ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                        ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                        ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                        ->get()->toArray();

              //dd( $objItem);

            return view('masters.inventory.itemmasters.mstfrm72attachment',compact(['objItem','objMstVoucherType','objAttachments']));
    }

}


    public function add(){

        $cyidRef =  Auth::user()->CYID_REF;
        $bridRef = Session::get('BRID_REF');
        $fyidRef = Session::get('FYID_REF');
        $status  ='A';
       
        // $objListing = DB::table('TBL_MST_ATTRIBUTE')
        //     ->where('TBL_MST_ATTRIBUTE.CYID_REF','=',$cyidRef)
        //     ->where('TBL_MST_ATTRIBUTE.BRID_REF','=',$bridRef)
        //     ->where('TBL_MST_ATTRIBUTE.FYID_REF','=', $fyidRef)
        //     ->leftJoin('TBL_MST_ATTRIBUTE_VAL', 'TBL_MST_ATTRIBUTE_VAL.ATTID_REF','=','TBL_MST_ATTRIBUTE.ATTID')
        //     ->select('TBL_MST_ATTRIBUTE.*', 'TBL_MST_ATTRIBUTE_VAL.*')
        //     ->get();



       // SELECT * from TBL_MST_ATTRIBUTE

     //  dump($objListing );
        $ObjMstInventoryClass   = Helper::getMstInventoryClass( $cyidRef, $bridRef, $fyidRef);
        $ObjMstUOM              = Helper::getMstUOM( $cyidRef, $bridRef, $fyidRef);
        $ObjMstItemGroup        = Helper::getMstItemGroup( $cyidRef, $bridRef, $fyidRef);
        $ObjMstItemCategory     = Helper::getMstItemCategory( $cyidRef, $bridRef, $fyidRef);
        $ObjMstStore            = Helper::getMstStore( $cyidRef, $bridRef, $fyidRef);
        $ObjMstHSN              = Helper::getMstHSN( $cyidRef, $bridRef, $fyidRef);
        $ObjMstBusinessUnit     = Helper::getMstBusinessUnit( $cyidRef, $bridRef, $fyidRef);        
        $ObjMstAttribute        = Helper::getMstAttribute( $cyidRef, $bridRef, $fyidRef);
        $objUdfForItems       = Helper::getUdfForItems( $cyidRef);
        $objudfCount = count($objUdfForItems);                
        if($objudfCount==0){
            $objudfCount=1;
        }

     
    $objCOMPANY =    $this->getcompanyname();
    $objGlList =    $this->GlList();

    //------------
    $objDD = DB::table('TBL_MST_ITEMCODE')
    ->where('CYID_REF','=',$cyidRef)
    ->where('STATUS','=','A')
    ->select('TBL_MST_ITEMCODE.*')
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

    $CompanyLoginStatus  =   $this->CompanyLoginStatus();


   

    $TabSetting     =   Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
    $checkCompany   =	Helper::checkCompany(Auth::user()->CYID_REF,'alps');
    $check_company   =   $this->checkCompany('Accurate'); 
    //dd($check_company); 


    return view('masters.inventory.itemmasters.mstfrm72add', compact([
            'ObjMstInventoryClass',
            'ObjMstUOM',
            'ObjMstItemGroup',
            'ObjMstItemCategory',
            'ObjMstStore',
            'ObjMstHSN',
            'ObjMstBusinessUnit',
            'ObjMstAttribute',
            'objUdfForItems',
            'objudfCount',
            'objGlList','objCOMPANY',
            'objDD','objDOCNO',
            'CompanyLoginStatus',
            'TabSetting',
            'checkCompany',
            'check_company'
          ]));
       
   }

   public function getAttValData(Request $request){
    
        $id = $request['id'];

        $ObjData =  DB::select('SELECT ATTVID,ATTID_REF,VALUE FROM TBL_MST_ATTRIBUTE_VAL
                                     WHERE ATTID_REF= ? ORDER BY VALUE ASC', [$id]);
        if(!empty($ObjData)){

           foreach ($ObjData as $index=>$dataRow){
           
            // $row = '';
            // $row = $row.'<tr id="attrvalue_'.$dataRow->ATTVID .'"  class="clsattrvalue"><td >'.$dataRow->VALUE;
            // $row = $row.'<input type="hidden" id="txtattrvalue_'.$dataRow->ATTVID.'" data-desc="'.$dataRow->VALUE;
            // $row = $row.'" value="'.$dataRow-> ATTVID.'"/></td></tr>';

            $row = '';
            $row = $row.'<tr >
            <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_ATTVID_REF[]" id="attrvalue_'.$dataRow->ATTVID.'"  class="clsattrvalue" value="'.$dataRow->ATTVID.'" ></td>
            <td width="88%" class="ROW2">'.$dataRow->VALUE;
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
           
            // $row = '';
            // $row = $row.'<tr id="itesubgrp_'.$dataRow->ISGID .'"  class="clsitesubgrp"><td >'.$dataRow->ISGCODE;
            // $row = $row.'<input type="hidden" id="txtitesubgrp_'.$dataRow->ISGID.'" data-desc="'.$dataRow->ISGCODE .' - ';
            // $row = $row.$dataRow->DESCRIPTIONS. '" value="'.$dataRow-> ISGID.'"/></td><td>'.$dataRow->DESCRIPTIONS.'</td></tr>';
            // echo $row;
            $row = '';
            $row = $row.'<tr>
            <td width="12%" class="ROW1" align="center"> <input type="checkbox" name="SELECT_ISGID_REF[]"  id="itesubgrp_'.$dataRow->ISGID .'"  class="clsitesubgrp" value="'.$dataRow->ISGID.'" /></td>
            <td width="39%" class="ROW2">'.$dataRow->ISGCODE;
            $row = $row.'<input type="hidden" id="txtitesubgrp_'.$dataRow->ISGID.'" data-desc="'.$dataRow->ISGCODE .' - ';
            $row = $row.$dataRow->DESCRIPTIONS. '" value="'.$dataRow-> ISGID.'"/></td><td  width="39%" class="ROW3">'.$dataRow->DESCRIPTIONS.'</td></tr>';
            echo $row;

           }

        }else{
            echo '<tr><td colspan="3">Record not found.</td></tr>';
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
        
        $destinationPath = storage_path()."/docs/company".$CYID_REF."/itemmst";

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
            return redirect()->route("master",[72,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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

            return redirect()->route("master",[72,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[72,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            return redirect()->route("master",[72,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
        }
      
        
    }   


   

   public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $ICODE =  trim($request['ICODE']);
        
        $objLabel = DB::table('TBL_MST_ITEM')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('ICODE','=',$ICODE)
            ->select('ICODE')
            ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        exit();
   }

   public function codedupeditmode(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $ICODE =  trim($request['ICODE']);
        $RCDID =  trim($request['RECORDID']);
        
        $objLabel = DB::table('TBL_MST_ITEM')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('ICODE','=',$ICODE)
            ->where('ITEMID','<>',$RCDID)
            ->select('ICODE')
            ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        exit();
    }

    public function namedupeditmode(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $NAME =  trim($request['NAME']);
        $RCDID =  trim($request['RECORDID']);
        

        $objLabel = DB::table('TBL_MST_ITEM')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('NAME','=',$NAME)
            ->where('ITEMID','<>',$RCDID)
            ->select('NAME')
            ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }

   public function nameduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $NAME =  trim($request['NAME']);
       

        $objLabel = DB::table('TBL_MST_ITEM')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('NAME','=',$NAME)
            ->select('NAME')
            ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }

    

    public function nameduplicatealps (Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $NAME =  trim($request['NAME']);
        $BUID_REF =  trim($request['BUID_REF']);

        $objLabel = DB::table('TBL_MST_ITEM')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('NAME','=',$NAME)
            ->where('BUID_REF','=',$BUID_REF)
            ->select('NAME')
            ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }

    public function nameduplicatealpseditmode (Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $NAME =  trim($request['NAME']);
        $BUID_REF =  trim($request['BUID_REF']);
        $OLDITEMID_REF =  trim($request['olditemid']);


        $objLabel = DB::table('TBL_MST_ITEM')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('NAME','=',$NAME)
            ->where('BUID_REF','=',$BUID_REF)
            ->where('ITEMID','<>',$OLDITEMID_REF)
            ->select('NAME')
            ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }

    

    
   public function save(Request $request)
   {

       $ICODE =    strtoupper(trim($request['ICODE']));     
       $NAME =     trim($request['NAME']);  
       $PARTNO =    !is_null($request['PARTNO'])?trim($request['PARTNO']):0;              
       $DRAWINGNO =  !is_null($request['DRAWINGNO'])?trim($request['DRAWINGNO']):0; 
       $CLASSID_REF = trim($request['invcls_id']);  

       $SAP_CUSTOMER_CODE = $request['SAP_CUSTOMER_CODE'];  
       $SAP_CUSTOMER_NAME = $request['SAP_CUSTOMER_NAME'];  
       $SAP_PART_NO = $request['SAP_PART_NO'];  
       $SAP_PART_DESC = $request['SAP_PART_DESC'];  
       $SAP_CUST_PARTNO = $request['SAP_CUST_PARTNO'];  
       $SAP_MARTKET_SETCODE = $request['SAP_MARTKET_SETCODE'];  
       $LOTSIZEQTY = $request['LOTSIZEQTY'];  
       $ALPS_PART_NO = $request['ALPS_PART_NO'];  
       $CUSTOMER_PART_NO = $request['CUSTOMER_PART_NO'];  
       $OEM_PART_NO =   $request['OEM_PART_NO']; 

       $MAIN_UOMID_REF = trim($request['maiuomref_id']);   
       $ALT_UOMID_REF =  trim($request['altuomref_id']);   
       $ITEM_DESC =       !is_null($request['ITEM_DESC'])?trim($request['ITEM_DESC']):'';      
       $ITEMGID =        trim($request['itegrp_id']);      
       $ISGID =          trim($request['itesubgrp_id']);   

       $ICID =      trim($request['itecat_id']);   // item category
       $STID =      trim($request['defsto_id']);    //store id
       $HSNID =      !is_null($request['hsn_id'])?trim($request['hsn_id']):NULL;   
       $IVM =       trim($request['IVM']);  
       $BUID_REF =  !is_null($request['busuni_id'])? $request['busuni_id']:NULL;   

       $STDCOST =   !is_null($request['STDCOST'])?trim($request['STDCOST']):0; 
       $MINLEVEL =  !is_null($request['MINLEVEL'])?trim($request['MINLEVEL']):0;             
       $REORDER =   !is_null($request['REORDERLEVEL'])?trim($request['REORDERLEVEL']):0;  
       $MAXLEVEL =   !is_null($request['MAXLEVEL'])?trim($request['MAXLEVEL']):0;    
       $ITEM_SPECI =   !is_null($request['ITEM_SPECI'])?trim($request['ITEM_SPECI']):'';  

       $DEACTIVATED = '0';
       $DODEACTIVATED = NULL;
       
       $CUSTOM_DUTY_RATE =  !is_null($request['SCDRate'])?trim($request['SCDRate']):0;  
       $STD_SWS_RATE = !is_null($request['SSRate'])?trim($request['SSRate']):0;   

       $CYID_REF = Auth::user()->CYID_REF;
       $BRID_REF = Session::get('BRID_REF');
       $FYID_REF = Session::get('FYID_REF'); 
         
       $QCA =       trim($request['QCA']); 
       $SRNO =      trim($request['SRNOA']); 
       $BATCHNO =   trim($request['BATCHNOA']); 
       $INV =       trim($request['INVMANTAIN']);   
       $EXPIRY_APPLICABLE =       trim($request['EXPIRY_APPLICABLE']);   
      // $TCS =       trim($request['TCS']); 

      $LEAD_DAYS   =   $request['LEAD_DAYS'];
      $SHELF_LIFE  =   $request['SHELF_LIFE']; 
      $INCA        =   trim($request['INCA']); 
      $BIN         =   trim($request['BIN']); 
      $WARA        =   trim($request['WARA']); 
      $WARA_MONTH  =   trim($request['WARA_MONTH']);

       $TCS =       0;  
       
       //ATTRIBUTES
       $attrData=array();
        $r_count1 = $request['Row_Count1'];
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['attrcode_'.$i]) && $request['attrcode_'.$i] !=""){
                $attrData[$i]['ATTRIBUTECODE'] =  $request['attrcode_'.$i];
                $attrData[$i]['VALUE'] =    $request['attrvalue_'.$i];
            }
        }

        if(!empty($attrData)){
            $attrwrapped["ATTRIBUTE"] = $attrData;    
            $attr_xml = ArrayToXml::convert($attrwrapped);
            $ATTRIBUTEXML = $attr_xml;
        }
        else{
            $ATTRIBUTEXML = NULL;
        }

       //TECHNICAL SPECIFICATION     //CHECKING FOR SINGLE ROW BLANK
       $r_count2 = $request['Row_Count2'];
       $techspec_Data=[];
       for ($i=0; $i<=$r_count2; $i++)
       {
            $techspec_request = isset( $request['TSTYPE_'.$i]) &&  (!is_null($request['TSTYPE_'.$i]) )? $request['TSTYPE_'.$i] : '';
           if(trim($techspec_request)!='')
           {
               $techspec_Data[$i]['TSTYPE'] =  $request['TSTYPE_'.$i];
               $techspec_Data[$i]['VALUE'] =   isset( $request['TSVALUE_'.$i]) &&  (!is_null($request['TSVALUE_'.$i]) )? $request['TSVALUE_'.$i] : '';
           }
       }  
  
       if(count($techspec_Data)>0){
            $techspec_wrapped["TECHNICALSPECIFICATION"] = $techspec_Data;        
            $techspec__xml = ArrayToXml::convert($techspec_wrapped);
            $TECHNICALXML  = $techspec__xml;        

        }else{

            $TECHNICALXML = NULL;
        }


       $IMAGEXML = 'NULL';

       //UOM CONVERSION
       $r_count3 = $request['Row_Count3'];
       for ($i=0; $i<=$r_count3; $i++)
       {
           if(isset($request['hdntxt_from_uomid_'.$i]))
           {
               $uomconv_Data[$i]['FROMUOM'] =    $request['hdntxt_from_uomid_'.$i];
               $uomconv_Data[$i]['FROMQTY'] =    $request['FROM_QTY_'.$i];
               $uomconv_Data[$i]['TOUOM'] =      $request['touom_'.$i];  
               $uomconv_Data[$i]['TOQTY'] =      $request['TO_QTY_'.$i]; 
           }
       }  

       if($MAIN_UOMID_REF != $ALT_UOMID_REF){
        $uomconv_wrapped["UOM"] = $uomconv_Data;        
        $uomconv__xml = ArrayToXml::convert($uomconv_wrapped);
        $XMLUOM  = $uomconv__xml;
       }
       else{
        $XMLUOM  = NULL; 
       }


       //UDF FIELDS
       $r_count4 = $request['Row_Count4'];
       $udffield_Data = [];
       for ($i=0; $i<=$r_count4; $i++)
       {
           //$udffield_request = isset( $request['udffie_'.$i]) &&  (!is_null($request['udffie_'.$i]) )? $request['udffie_'.$i] : '';
           if(isset( $request['udffie_'.$i]))
           {
               $udffield_Data[$i]['UDFFIELDS'] = $request['udffie_'.$i]; 
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


       $VTID = $this->vtid_ref;
       $USERID = Auth::user()->USERID;
       $UPDATE ="ADD";
       $UPDATE =  Date('Y-m-d');
       $UPTIME = Date('h:i:s.u');

       $ACTION     =   "ADD";
       $IPADDRESS  =   $request->getClientIp();

       //server validation begin

       $rules = [
        'ICODE' => 'required',
        'NAME' => 'required',
        'CLASSID_REF' => 'required',
        'MAIN_UOMID_REF' => 'required',          
        'ALT_UOMID_REF' => 'required',          
        'ITEMGID_REF' => 'required',          
        'ISGID_REF' => 'required',          
        'ICID_REF' => 'required',          
        'STID_REF' => 'required',          
        'HSNID_REF' => 'required',          
    ];

    $req_data = [
        'ICODE' => $ICODE ,
        'NAME' => $NAME,
        'CLASSID_REF' =>  $CLASSID_REF,
        'MAIN_UOMID_REF' => $MAIN_UOMID_REF,          
        'ALT_UOMID_REF' => $ALT_UOMID_REF,          
        'ITEMGID_REF' => $ITEMGID,          
        'ISGID_REF' => $ISGID,          
        'ICID_REF' =>  $ICID,          
        'STID_REF' =>  $STID,          
        'HSNID_REF' => $HSNID,       
    ]; 

    $validator = Validator::make( $req_data, $rules, $this->messages);

    if ($validator->fails())
    {
       return Response::json(['errors' => $validator->errors(),'form'=>'invalid']);	
    }


    $ITEM_TYPE 	    =   isset($request['ITEM_TYPE']) && !is_null($request['ITEM_TYPE'])?trim($request['ITEM_TYPE']):NULL;
    $MATERIAL_TYPE 				=   isset($request['MATERIAL_TYPE']) && !is_null($request['MATERIAL_TYPE'])?trim($request['MATERIAL_TYPE']):NULL;
    $GLID_REF 		=   isset($request['GLID_REF']) && !is_null($request['GLID_REF'])?trim($request['GLID_REF']):NULL;

    $BARCODE_APPLICABLE     =   isset($request['BARCODE_APPLICABLE'])?trim($request['BARCODE_APPLICABLE']):NULL; 
    $SERIALNO_MODE          =   isset($request['SERIALNO_MODE'])?trim($request['SERIALNO_MODE']):NULL; 
    $SERIALNO_PREFIX        =   isset($request['SERIALNO_PREFIX'])?trim($request['SERIALNO_PREFIX']):NULL; 
    $SERIALNO_STARTS_FROM   =   isset($request['SERIALNO_STARTS_FROM'])?trim($request['SERIALNO_STARTS_FROM']):NULL; 
    $SERIALNO_SUFFIX        =   isset($request['SERIALNO_SUFFIX'])?trim($request['SERIALNO_SUFFIX']):NULL; 
    $SERIALNO_MAX_LENGTH    =   isset($request['SERIALNO_MAX_LENGTH'])?trim($request['SERIALNO_MAX_LENGTH']):NULL; 
    
    $save_data = [
    
       $ICODE,  $NAME, 
       $PARTNO,    
       $DRAWINGNO, $CLASSID_REF, $MAIN_UOMID_REF,    $ALT_UOMID_REF,
       $ITEM_DESC,
       $ITEMGID,
       $ISGID,

       $ICID,
       $STID,
       $HSNID,
       $IVM,
       $BUID_REF,

       $STDCOST,
       $MINLEVEL,
       $REORDER,
       $MAXLEVEL,
       $ITEM_SPECI,

       $DEACTIVATED,
       $DODEACTIVATED,
       
       $CUSTOM_DUTY_RATE,
       $STD_SWS_RATE,

       $CYID_REF,
       $BRID_REF,
       $FYID_REF,

       $QCA,
       $SRNO,
       $BATCHNO,
       $INV,
       $EXPIRY_APPLICABLE,
       $TCS,

       $ITEM_TYPE,
       $MATERIAL_TYPE,
       $GLID_REF,

       $SAP_CUSTOMER_CODE,
        $SAP_CUSTOMER_NAME,
        $SAP_PART_NO,
        $SAP_PART_DESC,
        $SAP_CUST_PARTNO,
        $SAP_MARTKET_SETCODE,
        $LOTSIZEQTY,

       $ATTRIBUTEXML,
       
       $TECHNICALXML,
       //$IMAGEXML,
       $XMLUOM,
       $XMLUDF,       

       $VTID,
       $USERID,
       $UPDATE,
       $UPTIME,
       
       $ACTION,
       $IPADDRESS,
       $ALPS_PART_NO,  
       $CUSTOMER_PART_NO,
       $OEM_PART_NO,
       $BARCODE_APPLICABLE,$SERIALNO_MODE,$SERIALNO_PREFIX,$SERIALNO_STARTS_FROM,$SERIALNO_SUFFIX,$SERIALNO_MAX_LENGTH,
       $INCA,$BIN,$WARA,$LEAD_DAYS,$SHELF_LIFE,$WARA_MONTH
    ];
    
    // dd($save_data);
     //DB::enableQueryLog();
       try {

            //save data
           $sp_result = DB::select('EXEC SP_ITEM_IN 
                                            ?,?,?,?,?, 
                                            ?,?,?,?,?,
                                            ?,?,?,?,?,
                                            ?,?,?,?,?, 
                                            ?,?,?,?,?,?, 
                                            ?,?,?,?,?,?,?,
                                            ?,?,?,?,?,?,
                                            ?,?,?,?,?,?,
                                            ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,
                                            ?,?,?,?,?,?,?', $save_data);

                                    
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

   public function edit($id)
   {
         if(!is_null($id))
            {
                //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
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

                $objRights = DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();

                $ObjMstInventoryClass   = Helper::getMstInventoryClass( $CYID_REF, $BRID_REF, $FYID_REF);
                $ObjMstUOM              = Helper::getMstUOM( $CYID_REF, $BRID_REF, $FYID_REF);
                $ObjMstItemGroup        = Helper::getMstItemGroup( $CYID_REF, $BRID_REF, $FYID_REF);
                $ObjMstItemCategory     = Helper::getMstItemCategory( $CYID_REF, $BRID_REF, $FYID_REF);
                $ObjMstStore            = Helper::getMstStore( $CYID_REF, $BRID_REF, $FYID_REF);
                $ObjMstHSN              = Helper::getMstHSN( $CYID_REF, $BRID_REF, $FYID_REF);
                $ObjMstBusinessUnit     = Helper::getMstBusinessUnit( $CYID_REF, $BRID_REF, $FYID_REF);        
                $ObjMstAttribute        = Helper::getMstAttribute( $CYID_REF, $BRID_REF, $FYID_REF);
                $objUdfForItems         = Helper::getUdfForItems( $CYID_REF);

                $objItem = DB::table('TBL_MST_ITEM')                    
                ->where('TBL_MST_ITEM.ITEMID','=',$id)
                ->where('TBL_MST_ITEM.CYID_REF','=',$CYID_REF)
                ->select('TBL_MST_ITEM.*')
                ->first();
                /* if(strtoupper($objItem->STATUS)=="A" || strtoupper($objItem->STATUS)=="C"){
                    exit("Sorry, Only Un Approved record can edit.");
                } */


                $objItemAttribute = DB::table('TBL_MST_ITEMATTRIBUTE')                    
                ->where('TBL_MST_ITEMATTRIBUTE.ITEMID_REF','=',$id)
                ->select('TBL_MST_ITEMATTRIBUTE.*')
                ->get()->toArray();
                $objattCount = count($objItemAttribute);

              
                $subgroupid = $objItem->ISGID_REF;
                $Objsubgroup =  DB::table('TBL_MST_ITEMSUBGROUP')
                        ->where('TBL_MST_ITEMSUBGROUP.ISGID','=',$subgroupid)
                        ->select('TBL_MST_ITEMSUBGROUP.*')
                        ->first();

                $objItemCheckFlag = DB::table('TBL_MST_ITEMCHECKFLAG')                    
                ->where('TBL_MST_ITEMCHECKFLAG.ITEMID_REF','=',$id)
                ->select('TBL_MST_ITEMCHECKFLAG.*')
                ->first();

                $objItemTechSpecification = DB::table('TBL_MST_TECH_SPECIFICATION')                    
                ->where('TBL_MST_TECH_SPECIFICATION.ITEMID_REF','=',$id)
                ->select('TBL_MST_TECH_SPECIFICATION.*')
                ->get()->toArray();
                $objspecCount = count($objItemTechSpecification);
                if($objspecCount==0){
                    $objspecCount=1;
                }

                $objItemUOMConv = DB::table('TBL_MST_ITEM_UOMCONV')                    
                ->where('TBL_MST_ITEM_UOMCONV.ITEMID_REF','=',$id)
                ->select('TBL_MST_ITEM_UOMCONV.*')
                ->get()->toArray();
                $objuomCount = count($objItemUOMConv);

                 $objItemUDF = DB::table('TBL_MST_ITEM_UDF')                    
                 ->where('TBL_MST_ITEM_UDF.ITEMID_REF','=',$id)
                 ->select('TBL_MST_ITEM_UDF.*')
                 ->get()->toArray();

                $objItemUDF = DB::table('TBL_MST_ITEM_UDF')                    
                ->where('TBL_MST_ITEM_UDF.ITEMID_REF','=',$id)
                ->leftJoin('TBL_MST_UDFFOR_ITEM','TBL_MST_UDFFOR_ITEM.UDFITEMID','=','TBL_MST_ITEM_UDF.UDFITEMID_REF')                
                ->select('TBL_MST_ITEM_UDF.*','TBL_MST_UDFFOR_ITEM.*')
                ->orderBy('TBL_MST_ITEM_UDF.ITEM_UDFID','ASC')
                ->get()->toArray();
                $objudfCount = count($objItemUDF);

                if($objudfCount==0){
                    $objudfCount=1;
                }
           
               // dump($objItemUDF);

               $objGlList =    $this->GlList();

               $objOldGlList=$this->OldGlList($objItem->GLID_REF);

               $objFlage = DB::table('TBL_MST_ITEMCHECKFLAG')                    
                ->where('TBL_MST_ITEMCHECKFLAG.ITEMID_REF','=',$objItem->ITEMID)
                ->select('TBL_MST_ITEMCHECKFLAG.*')
                ->first();

                $objCOMPANY =    $this->getcompanyname();

                $CompanyLoginStatus  =   $this->CompanyLoginStatus();
                $TabSetting =   Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
                $checkCompany   =	Helper::checkCompany(Auth::user()->CYID_REF,'alps');
                $check_company   =   $this->checkCompany('Accurate'); 

                return view('masters.inventory.itemmasters.mstfrm72edit',compact(['objItem','objItemAttribute','objItemCheckFlag','objItemTechSpecification',
                                'objItemUOMConv','objItemUDF','objRights','user_approval_level','ObjMstInventoryClass',
                                'ObjMstUOM',
                                'ObjMstItemGroup',
                                'ObjMstItemCategory',
                                'ObjMstStore',
                                'ObjMstHSN',
                                'ObjMstBusinessUnit',
                                'ObjMstAttribute',
                                'objUdfForItems','objattCount','objudfCount','objuomCount','objspecCount','Objsubgroup','objGlList','objOldGlList','objFlage','objCOMPANY','CompanyLoginStatus','TabSetting','checkCompany','check_company']));
            }

    }


    public function copy($id)
   {
         if(!is_null($id))
            {
                //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
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

                $objRights = DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();

                $ObjMstInventoryClass   = Helper::getMstInventoryClass( $CYID_REF, $BRID_REF, $FYID_REF);
                $ObjMstUOM              = Helper::getMstUOM( $CYID_REF, $BRID_REF, $FYID_REF);
                $ObjMstItemGroup        = Helper::getMstItemGroup( $CYID_REF, $BRID_REF, $FYID_REF);
                $ObjMstItemCategory     = Helper::getMstItemCategory( $CYID_REF, $BRID_REF, $FYID_REF);
                $ObjMstStore            = Helper::getMstStore( $CYID_REF, $BRID_REF, $FYID_REF);
                $ObjMstHSN              = Helper::getMstHSN( $CYID_REF, $BRID_REF, $FYID_REF);
                $ObjMstBusinessUnit     = Helper::getMstBusinessUnit( $CYID_REF, $BRID_REF, $FYID_REF);        
                $ObjMstAttribute        = Helper::getMstAttribute( $CYID_REF, $BRID_REF, $FYID_REF);
                $objUdfForItems         = Helper::getUdfForItems( $CYID_REF);

                $objItem = DB::table('TBL_MST_ITEM')                    
                ->where('TBL_MST_ITEM.ITEMID','=',$id)
                ->where('TBL_MST_ITEM.CYID_REF','=',$CYID_REF)
                ->select('TBL_MST_ITEM.*')
                ->first();
                // if(strtoupper($objItem->STATUS)=="A" || strtoupper($objItem->STATUS)=="C"){
                //     exit("Sorry, Only Un Approved record can edit.");
                // }


                $objItemAttribute = DB::table('TBL_MST_ITEMATTRIBUTE')                    
                ->where('TBL_MST_ITEMATTRIBUTE.ITEMID_REF','=',$id)
                ->select('TBL_MST_ITEMATTRIBUTE.*')
                ->get()->toArray();
                $objattCount = count($objItemAttribute);

              
                $subgroupid = $objItem->ISGID_REF;
                $Objsubgroup =  DB::table('TBL_MST_ITEMSUBGROUP')
                        ->where('TBL_MST_ITEMSUBGROUP.ISGID','=',$subgroupid)
                        ->select('TBL_MST_ITEMSUBGROUP.*')
                        ->first();

                $objItemCheckFlag = DB::table('TBL_MST_ITEMCHECKFLAG')                    
                ->where('TBL_MST_ITEMCHECKFLAG.ITEMID_REF','=',$id)
                ->select('TBL_MST_ITEMCHECKFLAG.*')
                ->first();

                $objItemTechSpecification = DB::table('TBL_MST_TECH_SPECIFICATION')                    
                ->where('TBL_MST_TECH_SPECIFICATION.ITEMID_REF','=',$id)
                ->select('TBL_MST_TECH_SPECIFICATION.*')
                ->get()->toArray();
                $objspecCount = count($objItemTechSpecification);
                if($objspecCount==0){
                    $objspecCount=1;
                }

                $objItemUOMConv = DB::table('TBL_MST_ITEM_UOMCONV')                    
                ->where('TBL_MST_ITEM_UOMCONV.ITEMID_REF','=',$id)
                ->select('TBL_MST_ITEM_UOMCONV.*')
                ->get()->toArray();
                $objuomCount = count($objItemUOMConv);

                 $objItemUDF = DB::table('TBL_MST_ITEM_UDF')                    
                 ->where('TBL_MST_ITEM_UDF.ITEMID_REF','=',$id)
                 ->select('TBL_MST_ITEM_UDF.*')
                 ->get()->toArray();

                $objItemUDF = DB::table('TBL_MST_ITEM_UDF')                    
                ->where('TBL_MST_ITEM_UDF.ITEMID_REF','=',$id)
                ->leftJoin('TBL_MST_UDFFOR_ITEM','TBL_MST_UDFFOR_ITEM.UDFITEMID','=','TBL_MST_ITEM_UDF.UDFITEMID_REF')                
                ->select('TBL_MST_ITEM_UDF.*','TBL_MST_UDFFOR_ITEM.*')
                ->orderBy('TBL_MST_ITEM_UDF.ITEM_UDFID','ASC')
                ->get()->toArray();
                $objudfCount = count($objItemUDF);

                if($objudfCount==0){
                    $objudfCount=1;
                }
           
               // dump($objItemUDF);

               $objGlList =    $this->GlList();

               $objOldGlList=$this->OldGlList($objItem->GLID_REF);

               $objFlage = DB::table('TBL_MST_ITEMCHECKFLAG')                    
                ->where('TBL_MST_ITEMCHECKFLAG.ITEMID_REF','=',$objItem->ITEMID)
                ->select('TBL_MST_ITEMCHECKFLAG.*')
                ->first();

                $objCOMPANY =    $this->getcompanyname();

                $CompanyLoginStatus  =   $this->CompanyLoginStatus();

                //------------
                $objDD = DB::table('TBL_MST_ITEMCODE')
                ->where('CYID_REF','=',$CYID_REF)
                ->where('STATUS','=','A')
                ->select('TBL_MST_ITEMCODE.*')
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

                $TabSetting =   Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
                $checkCompany   =	Helper::checkCompany(Auth::user()->CYID_REF,'alps');
                $check_company   =   $this->checkCompany('Accurate'); 

                return view('masters.inventory.itemmasters.mstfrm72copy',compact(['objItem','objItemAttribute','objItemCheckFlag','objItemTechSpecification',
                                'objItemUOMConv','objItemUDF','objRights','user_approval_level','ObjMstInventoryClass',
                                'ObjMstUOM',
                                'ObjMstItemGroup',
                                'ObjMstItemCategory',
                                'ObjMstStore',
                                'ObjMstHSN',
                                'ObjMstBusinessUnit',
                                'ObjMstAttribute','objDD','objDOCNO',
                                'objUdfForItems','objattCount','objudfCount','objuomCount','objspecCount','Objsubgroup','objGlList','objOldGlList','objFlage','objCOMPANY','CompanyLoginStatus','TabSetting','checkCompany','check_company']));
            }

    }//edit function



   public function update(Request $request)
   {
     
    $ICODE =    strtoupper(trim($request['ICODE']));     
    $NAME =     trim($request['NAME']);  
    $PARTNO =    !is_null($request['PARTNO'])?trim($request['PARTNO']):0;              
    $DRAWINGNO =  !is_null($request['DRAWINGNO'])?trim($request['DRAWINGNO']):0; 
    $CLASSID_REF = trim($request['invcls_id']);  

    $MAIN_UOMID_REF = trim($request['maiuomref_id']);   
    $ALT_UOMID_REF =  trim($request['altuomref_id']);   
    $ITEM_DESC =       !is_null($request['ITEM_DESC'])?trim($request['ITEM_DESC']):'';      
    $ITEMGID =        trim($request['itegrp_id']);      
    $ISGID =          trim($request['itesubgrp_id']);   


    $SAP_CUSTOMER_CODE = $request['SAP_CUSTOMER_CODE'];  
    $SAP_CUSTOMER_NAME = $request['SAP_CUSTOMER_NAME'];  
    $SAP_PART_NO = $request['SAP_PART_NO'];  
    $SAP_PART_DESC = $request['SAP_PART_DESC'];  
    $SAP_CUST_PARTNO = $request['SAP_CUST_PARTNO'];  
    $SAP_MARKET_SETCODE = $request['SAP_MARTKET_SETCODE'];  
    $ROUNDING_VALUE = $request['LOTSIZEQTY'];  
    $ALPS_PART_NO = $request['ALPS_PART_NO'];  
    $CUSTOMER_PART_NO = $request['CUSTOMER_PART_NO'];  
    $OEM_PART_NO = $request['OEM_PART_NO'];  

    $ICID =      trim($request['itecat_id']);   // item category
    $STID =      trim($request['defsto_id']);    //store id
    $HSNID =      !is_null($request['hsn_id'])?trim($request['hsn_id']):NULL;   
    $IVM =       trim($request['IVM']);  
    $BUID_REF =  !is_null($request['busuni_id'])? $request['busuni_id']:NULL;   

    $STDCOST =   !is_null($request['STDCOST'])?trim($request['STDCOST']):0; 
    $MINLEVEL =  !is_null($request['MINLEVEL'])?trim($request['MINLEVEL']):0;             
    $REORDER =   !is_null($request['REORDERLEVEL'])?trim($request['REORDERLEVEL']):0;  
    $MAXLEVEL =   !is_null($request['MAXLEVEL'])?trim($request['MAXLEVEL']):0;    
    $ITEM_SPECI =   !is_null($request['ITEM_SPECI'])?trim($request['ITEM_SPECI']):'';  

    $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;       

    $newDateString = NULL;
    $newdt = !(is_null($request['DODEACTIVATED']) || empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 
    if(!is_null($newdt) ){
        $newdt = str_replace( "/", "-",  $newdt ) ;
        $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
    }
    $DODEACTIVATED = $newDateString;
    
    $CUSTOM_DUTY_RATE =  !is_null($request['SCDRate'])?trim($request['SCDRate']):0;  
    $STD_SWS_RATE = !is_null($request['SSRate'])?trim($request['SSRate']):0;   

    $CYID_REF = Auth::user()->CYID_REF;
    $BRID_REF = Session::get('BRID_REF');
    $FYID_REF = Session::get('FYID_REF'); 
      
    $QCA =       trim($request['QCA']); 
    $SRNO =      trim($request['SRNOA']); 
    $BATCHNO =   trim($request['BATCHNOA']); 
    $INV =       trim($request['INVMANTAIN']);   
    $EXPIRY_APPLICABLE =       trim($request['EXPIRY_APPLICABLE']);   

    //$TCS =       trim($request['TCS']); 

    $LEAD_DAYS   =   $request['LEAD_DAYS'];
    $SHELF_LIFE  =   $request['SHELF_LIFE']; 
    $INCA        =   trim($request['INCA']); 
    $BIN         =   trim($request['BIN']); 
    $WARA        =   trim($request['WARA']); 
    $WARA_MONTH  =   trim($request['WARA_MONTH']);

    $TCS =       0;   
    
    //ATTRIBUTES
    $attrData=array();
     $r_count1 = $request['Row_Count1'];
     for ($i=0; $i<=$r_count1; $i++)
     {
        if(isset($request['attrcode_'.$i]) && $request['attrcode_'.$i] !=""){
             $attrData[$i]['ATTRIBUTECODE'] =  $request['attrcode_'.$i];
             $attrData[$i]['VALUE'] =    $request['attrvalue_'.$i];
         }
     }

    if(!empty($attrData)){
        $attrwrapped["ATTRIBUTE"] = $attrData;    
        $attr_xml = ArrayToXml::convert($attrwrapped);
        $ATTRIBUTEXML = $attr_xml;
    }
    else{
        $ATTRIBUTEXML = NULL;
    }

    //TECHNICAL SPECIFICATION     //CHECK FOR SINGLE ROW BLANK
    $r_count2 = $request['Row_Count2'];
    $techspec_Data=[];
    for ($i=0; $i<=$r_count2; $i++)
    {
         $techspec_request = isset( $request['TSTYPE_'.$i]) &&  (!is_null($request['TSTYPE_'.$i]) )? $request['TSTYPE_'.$i] : '';
        if(trim($techspec_request)!='')
        {
            $techspec_Data[$i]['TSTYPE'] =  $request['TSTYPE_'.$i];
            $techspec_Data[$i]['VALUE'] =   isset( $request['TSVALUE_'.$i]) &&  (!is_null($request['TSVALUE_'.$i]) )? $request['TSVALUE_'.$i] : '';
        }
    }  

    if(count($techspec_Data)>0){
         $techspec_wrapped["TECHNICALSPECIFICATION"] = $techspec_Data;        
         $techspec__xml = ArrayToXml::convert($techspec_wrapped);
         $TECHNICALXML  = $techspec__xml;        

     }else{

         $TECHNICALXML = NULL;
     }


    $IMAGEXML = 'NULL';

    //UOM CONVERSION
    $r_count3 = $request['Row_Count3'];
    for ($i=0; $i<=$r_count3; $i++)
    {
        if(isset($request['hdntxt_from_uomid_'.$i]))
        {
            $uomconv_Data[$i]['FROMUOM'] =    $request['hdntxt_from_uomid_'.$i];
            $uomconv_Data[$i]['FROMQTY'] =    $request['FROM_QTY_'.$i];
            $uomconv_Data[$i]['TOUOM'] =      $request['touom_'.$i];  
            $uomconv_Data[$i]['TOQTY'] =      $request['TO_QTY_'.$i]; 
        }
    }  

    if($MAIN_UOMID_REF != $ALT_UOMID_REF){
        $uomconv_wrapped["UOM"] = $uomconv_Data;        
        $uomconv__xml = ArrayToXml::convert($uomconv_wrapped);
        $XMLUOM  = $uomconv__xml;
    }
    else{
        $XMLUOM  = NULL; 
    }


    //UDF FIELDS
    $r_count4 = $request['Row_Count4'];
       $udffield_Data = [];
       for ($i=0; $i<=$r_count4; $i++)
       {
           //$udffield_request = isset( $request['udffie_'.$i]) &&  (!is_null($request['udffie_'.$i]) )? $request['udffie_'.$i] : '';
           if(isset( $request['udffie_'.$i]))
           {
               $udffield_Data[$i]['UDFFIELDS'] = $request['udffie_'.$i]; 
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

            $VTID = $this->vtid_ref;
            $USERID = Auth::user()->USERID;
            $UPDATE =  Date('Y-m-d');
            $UPTIME = Date('h:i:s.u');

            $ACTION     =   "EDIT";
            $IPADDRESS  =   $request->getClientIp();

            //server validation begin
            $rules = [
            'ICODE' => 'required',
            'NAME' => 'required',
            'CLASSID_REF' => 'required',
            'MAIN_UOMID_REF' => 'required',          
            'ALT_UOMID_REF' => 'required',          
            'ITEMGID_REF' => 'required',          
            'ISGID_REF' => 'required',          
            'ICID_REF' => 'required',          
            'STID_REF' => 'required',          
            'HSNID_REF' => 'required',          
        ];


        $req_data = [
            'ICODE' => $ICODE ,
            'NAME' => $NAME,
            'CLASSID_REF' =>  $CLASSID_REF,
            'MAIN_UOMID_REF' => $MAIN_UOMID_REF,          
            'ALT_UOMID_REF' => $ALT_UOMID_REF,          
            'ITEMGID_REF' => $ITEMGID,          
            'ISGID_REF' => $ISGID,          
            'ICID_REF' =>  $ICID,          
            'STID_REF' =>  $STID,          
            'HSNID_REF' => $HSNID,       
        ]; 


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
            return Response::json(['errors' => $validator->errors(),'form'=>'invalid']);	
        }

        //server validation begin

        $ITEM_TYPE 	    =   isset($request['ITEM_TYPE']) && !is_null($request['ITEM_TYPE'])?trim($request['ITEM_TYPE']):NULL;
        $MATERIAL_TYPE 				=   isset($request['MATERIAL_TYPE']) && !is_null($request['MATERIAL_TYPE'])?trim($request['MATERIAL_TYPE']):NULL;
        $GLID_REF 		=   isset($request['GLID_REF']) && !is_null($request['GLID_REF'])?trim($request['GLID_REF']):NULL;

        $BARCODE_APPLICABLE     =   isset($request['BARCODE_APPLICABLE'])?trim($request['BARCODE_APPLICABLE']):NULL; 
        $SERIALNO_MODE          =   isset($request['SERIALNO_MODE'])?trim($request['SERIALNO_MODE']):NULL; 
        $SERIALNO_PREFIX        =   isset($request['SERIALNO_PREFIX'])?trim($request['SERIALNO_PREFIX']):NULL; 
        $SERIALNO_STARTS_FROM   =   isset($request['SERIALNO_STARTS_FROM'])?trim($request['SERIALNO_STARTS_FROM']):NULL; 
        $SERIALNO_SUFFIX        =   isset($request['SERIALNO_SUFFIX'])?trim($request['SERIALNO_SUFFIX']):NULL; 
        $SERIALNO_MAX_LENGTH    =   isset($request['SERIALNO_MAX_LENGTH'])?trim($request['SERIALNO_MAX_LENGTH']):NULL; 

            
        $save_data = [
            $ICODE,                 $NAME,              $PARTNO,            $DRAWINGNO,             $CLASSID_REF,           $MAIN_UOMID_REF,            $ALT_UOMID_REF,             $ITEM_DESC,                 $ITEMGID,               $ISGID,
            $ICID,                  $STID,              $HSNID,             $IVM,                   $BUID_REF,              $STDCOST,                   $MINLEVEL,                  $REORDER,                   $MAXLEVEL,              $ITEM_SPECI,
            $DEACTIVATED,           $DODEACTIVATED,     $CUSTOM_DUTY_RATE,  $STD_SWS_RATE,          $CYID_REF,              $BRID_REF,                  $FYID_REF,                  $QCA,                       $SRNO,                  $BATCHNO,
            $INV,                   $EXPIRY_APPLICABLE,	$TCS,               $ITEM_TYPE,         $MATERIAL_TYPE,         $GLID_REF,              $SAP_CUSTOMER_CODE,         $SAP_CUSTOMER_NAME,         $SAP_PART_NO,               $SAP_PART_DESC,         $SAP_CUST_PARTNO,   
            $SAP_MARKET_SETCODE,    $ROUNDING_VALUE,    $ATTRIBUTEXML,      $TECHNICALXML,          $XMLUOM,                $XMLUDF,                    $VTID,                      $USERID,                    $UPDATE,                $UPTIME,                
            $ACTION,                $IPADDRESS,         $ALPS_PART_NO,      $CUSTOMER_PART_NO,      $OEM_PART_NO,           $BARCODE_APPLICABLE,        $SERIALNO_MODE,             $SERIALNO_PREFIX,           $SERIALNO_STARTS_FROM,  $SERIALNO_SUFFIX,
            $SERIALNO_MAX_LENGTH,   $INCA,              $BIN,               $WARA,                  $LEAD_DAYS,             $SHELF_LIFE,                $WARA_MONTH   ];

       // dd($save_data);

        $sp_result = DB::select('EXEC SP_ITEM_UP   ?,?,?,?,?,?,?,?,?,?,  ?,?,?,?,?,?,?,?,?,?,   ?,?,?,?,?,?,?,?,?,?,    ?,?,?,?,?,?,?,?,?,?,    ?,?,?,?,?,?,?,?,?,?,    ?,?,?,?,?,?,?,?,?,?,    ?,?,?,?,?,?,?,?', $save_data);
  
     //dd($sp_result);

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
           
     $ICODE =    strtoupper(trim($request['ICODE']));     
     $NAME =     trim($request['NAME']);  
     $PARTNO =    !is_null($request['PARTNO'])?trim($request['PARTNO']):0;              
     $DRAWINGNO =  !is_null($request['DRAWINGNO'])?trim($request['DRAWINGNO']):0; 
     $CLASSID_REF = trim($request['invcls_id']);  
 
     $MAIN_UOMID_REF = trim($request['maiuomref_id']);   
     $ALT_UOMID_REF =  trim($request['altuomref_id']);   
     $ITEM_DESC =       !is_null($request['ITEM_DESC'])?trim($request['ITEM_DESC']):'';      
     $ITEMGID =        trim($request['itegrp_id']);      
     $ISGID =          trim($request['itesubgrp_id']); 
     
     $SAP_CUSTOMER_CODE = $request['SAP_CUSTOMER_CODE'];  
     $SAP_CUSTOMER_NAME = $request['SAP_CUSTOMER_NAME'];  
     $SAP_PART_NO = $request['SAP_PART_NO'];  
     $SAP_PART_DESC = $request['SAP_PART_DESC'];  
     $SAP_CUST_PARTNO = $request['SAP_CUST_PARTNO'];  
     $SAP_MARKET_SETCODE = $request['SAP_MARTKET_SETCODE'];  
     $ROUNDING_VALUE = $request['LOTSIZEQTY'];  
     $ALPS_PART_NO = $request['ALPS_PART_NO'];  
     $CUSTOMER_PART_NO = $request['CUSTOMER_PART_NO'];  
     $OEM_PART_NO = $request['OEM_PART_NO'];  
     
 
     $ICID =      trim($request['itecat_id']);   // item category
     $STID =      trim($request['defsto_id']);    //store id
     $HSNID =      !is_null($request['hsn_id'])?trim($request['hsn_id']):NULL;   
     $IVM =       trim($request['IVM']);  
     $BUID_REF =  !is_null($request['busuni_id'])? $request['busuni_id']:NULL;   
 
     $STDCOST =   !is_null($request['STDCOST'])?trim($request['STDCOST']):0; 
     $MINLEVEL =  !is_null($request['MINLEVEL'])?trim($request['MINLEVEL']):0;             
     $REORDER =   !is_null($request['REORDERLEVEL'])?trim($request['REORDERLEVEL']):0;  
     $MAXLEVEL =   !is_null($request['MAXLEVEL'])?trim($request['MAXLEVEL']):0;    
     $ITEM_SPECI =   !is_null($request['ITEM_SPECI'])?trim($request['ITEM_SPECI']):'';  
 
     $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;       

     $newDateString = NULL;
     $newdt = !(is_null($request['DODEACTIVATED']) || empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 
     if(!is_null($newdt) ){
         $newdt = str_replace( "/", "-",  $newdt ) ;
         $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
     }
     $DODEACTIVATED = $newDateString;
     
     $CUSTOM_DUTY_RATE =  !is_null($request['SCDRate'])?trim($request['SCDRate']):0;  
     $STD_SWS_RATE = !is_null($request['SSRate'])?trim($request['SSRate']):0;   
 
     $CYID_REF = Auth::user()->CYID_REF;
     $BRID_REF = Session::get('BRID_REF');
     $FYID_REF = Session::get('FYID_REF'); 
     $USERID_REF = Auth::user()->USERID;
     $VTID = $this->vtid_ref;
       
     $QCA =       trim($request['QCA']); 
     $SRNO =      trim($request['SRNOA']); 
     $BATCHNO =   trim($request['BATCHNOA']); 
     $INV =       trim($request['INVMANTAIN']);   
     $EXPIRY_APPLICABLE =       trim($request['EXPIRY_APPLICABLE']);   
     //$TCS =       trim($request['TCS']);  

      $LEAD_DAYS   =   $request['LEAD_DAYS'];
      $SHELF_LIFE  =   $request['SHELF_LIFE']; 
      $INCA        =   trim($request['INCA']); 
      $BIN         =   trim($request['BIN']); 
      $WARA        =   trim($request['WARA']); 
      $WARA_MONTH  =   trim($request['WARA_MONTH']);

     $TCS =       0;


     $sp_Approvallevel = [
        $USERID_REF, $VTID, $CYID_REF,$BRID_REF,
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
     
     //ATTRIBUTES
     $attrData=array();
     $r_count1 = $request['Row_Count1'];
     for ($i=0; $i<=$r_count1; $i++)
     {
        if(isset($request['attrcode_'.$i]) && $request['attrcode_'.$i] !=""){
             $attrData[$i]['ATTRIBUTECODE'] =  $request['attrcode_'.$i];
             $attrData[$i]['VALUE'] =    $request['attrvalue_'.$i];
         }
     }

    if(!empty($attrData)){
        $attrwrapped["ATTRIBUTE"] = $attrData;    
        $attr_xml = ArrayToXml::convert($attrwrapped);
        $ATTRIBUTEXML = $attr_xml;
    }
    else{
        $ATTRIBUTEXML = NULL;
    }
 
    //TECHNICAL SPECIFICATION     //CHECK FOR SINGLE ROW BLANK
    $r_count2 = $request['Row_Count2'];
    $techspec_Data=[];
    for ($i=0; $i<=$r_count2; $i++)
    {
         $techspec_request = isset( $request['TSTYPE_'.$i]) &&  (!is_null($request['TSTYPE_'.$i]) )? $request['TSTYPE_'.$i] : '';
        if(trim($techspec_request)!='')
        {
            $techspec_Data[$i]['TSTYPE'] =  $request['TSTYPE_'.$i];
            $techspec_Data[$i]['VALUE'] =   isset( $request['TSVALUE_'.$i]) &&  (!is_null($request['TSVALUE_'.$i]) )? $request['TSVALUE_'.$i] : '';
        }
    }  

    if(count($techspec_Data)>0){
         $techspec_wrapped["TECHNICALSPECIFICATION"] = $techspec_Data;        
         $techspec__xml = ArrayToXml::convert($techspec_wrapped);
         $TECHNICALXML  = $techspec__xml;        

     }else{

         $TECHNICALXML = NULL;
     }
 
     $IMAGEXML = 'NULL';
 
     //UOM CONVERSION
     $r_count3 = $request['Row_Count3'];
    for ($i=0; $i<=$r_count3; $i++)
    {
        if(isset($request['hdntxt_from_uomid_'.$i]))
        {
            $uomconv_Data[$i]['FROMUOM'] =    $request['hdntxt_from_uomid_'.$i];
            $uomconv_Data[$i]['FROMQTY'] =    $request['FROM_QTY_'.$i];
            $uomconv_Data[$i]['TOUOM'] =      $request['touom_'.$i];  
            $uomconv_Data[$i]['TOQTY'] =      $request['TO_QTY_'.$i]; 
        }
    }  

    if($MAIN_UOMID_REF != $ALT_UOMID_REF){
        $uomconv_wrapped["UOM"] = $uomconv_Data;        
        $uomconv__xml = ArrayToXml::convert($uomconv_wrapped);
        $XMLUOM  = $uomconv__xml;
    }
    else{
        $XMLUOM  = NULL; 
    }
 
 
     //UDF FIELDS
     $r_count4 = $request['Row_Count4'];
     $udffield_Data = [];
     for ($i=0; $i<=$r_count4; $i++)
     {
         //$udffield_request = isset( $request['udffie_'.$i]) &&  (!is_null($request['udffie_'.$i]) )? $request['udffie_'.$i] : '';
         if(isset( $request['udffie_'.$i]))
         {
             $udffield_Data[$i]['UDFFIELDS'] = $request['udffie_'.$i]; 
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
 
     
     $USERID = Auth::user()->USERID;
     $UPDATE =  Date('Y-m-d');
     $UPTIME = Date('h:i:s.u');
 
     $ACTION     =   $Approvallevel;
     $IPADDRESS  =   $request->getClientIp();
 
     //server validation begin
     $rules = [
      'ICODE' => 'required',
      'NAME' => 'required',
      'CLASSID_REF' => 'required',
      'MAIN_UOMID_REF' => 'required',          
      'ALT_UOMID_REF' => 'required',          
      'ITEMGID_REF' => 'required',          
      'ISGID_REF' => 'required',          
      'ICID_REF' => 'required',          
      'STID_REF' => 'required',          
      'HSNID_REF' => 'required',          
  ];
 
 
  $req_data = [
      'ICODE' => $ICODE ,
      'NAME' => $NAME,
      'CLASSID_REF' =>  $CLASSID_REF,
      'MAIN_UOMID_REF' => $MAIN_UOMID_REF,          
      'ALT_UOMID_REF' => $ALT_UOMID_REF,          
      'ITEMGID_REF' => $ITEMGID,          
      'ISGID_REF' => $ISGID,          
      'ICID_REF' =>  $ICID,          
      'STID_REF' =>  $STID,          
      'HSNID_REF' => $HSNID,       
  ]; 
 
 
  $validator = Validator::make( $req_data, $rules, $this->messages);
 
  if ($validator->fails())
  {
     return Response::json(['errors' => $validator->errors(),'form'=>'invalid']);	
  }
 
  //server validation begin
 
    $ITEM_TYPE 	    		=   isset($request['ITEM_TYPE']) && !is_null($request['ITEM_TYPE'])?trim($request['ITEM_TYPE']):NULL;
    $MATERIAL_TYPE 			=   isset($request['MATERIAL_TYPE']) && !is_null($request['MATERIAL_TYPE'])?trim($request['MATERIAL_TYPE']):NULL;
    $GLID_REF 				=   isset($request['GLID_REF']) && !is_null($request['GLID_REF'])?trim($request['GLID_REF']):NULL;
    $BARCODE_APPLICABLE     =   isset($request['BARCODE_APPLICABLE'])?trim($request['BARCODE_APPLICABLE']):NULL; 
    $SERIALNO_MODE          =   isset($request['SERIALNO_MODE'])?trim($request['SERIALNO_MODE']):NULL; 
    $SERIALNO_PREFIX        =   isset($request['SERIALNO_PREFIX'])?trim($request['SERIALNO_PREFIX']):NULL; 
    $SERIALNO_STARTS_FROM   =   isset($request['SERIALNO_STARTS_FROM'])?trim($request['SERIALNO_STARTS_FROM']):NULL; 
    $SERIALNO_SUFFIX        =   isset($request['SERIALNO_SUFFIX'])?trim($request['SERIALNO_SUFFIX']):NULL; 
    $SERIALNO_MAX_LENGTH    =   isset($request['SERIALNO_MAX_LENGTH'])?trim($request['SERIALNO_MAX_LENGTH']):NULL; 

      
    $save_data = [
        $ICODE,                 $NAME,              $PARTNO,            $DRAWINGNO,             $CLASSID_REF,           $MAIN_UOMID_REF,            $ALT_UOMID_REF,             $ITEM_DESC,                 $ITEMGID,               $ISGID,
        $ICID,                  $STID,              $HSNID,             $IVM,                   $BUID_REF,              $STDCOST,                   $MINLEVEL,                  $REORDER,                   $MAXLEVEL,              $ITEM_SPECI,
        $DEACTIVATED,           $DODEACTIVATED,     $CUSTOM_DUTY_RATE,  $STD_SWS_RATE,          $CYID_REF,              $BRID_REF,                  $FYID_REF,                  $QCA,                       $SRNO,                  $BATCHNO,
        $INV,                   $EXPIRY_APPLICABLE, $TCS,               $ITEM_TYPE,         $MATERIAL_TYPE,         $GLID_REF,              $SAP_CUSTOMER_CODE,         $SAP_CUSTOMER_NAME,         $SAP_PART_NO,               $SAP_PART_DESC,         $SAP_CUST_PARTNO,   
        $SAP_MARKET_SETCODE,    $ROUNDING_VALUE,    $ATTRIBUTEXML,      $TECHNICALXML,          $XMLUOM,                $XMLUDF,                    $VTID,                      $USERID,                    $UPDATE,                $UPTIME,                
        $ACTION,                $IPADDRESS,         $ALPS_PART_NO,      $CUSTOMER_PART_NO,      $OEM_PART_NO,           $BARCODE_APPLICABLE,        $SERIALNO_MODE,             $SERIALNO_PREFIX,           $SERIALNO_STARTS_FROM,  $SERIALNO_SUFFIX,
        $SERIALNO_MAX_LENGTH,   $INCA,              $BIN,               $WARA,                  $LEAD_DAYS,             $SHELF_LIFE,                $WARA_MONTH   ];

    // dd($save_data);

    $sp_result = DB::select('EXEC SP_ITEM_UP   ?,?,?,?,?,?,?,?,?,?,  ?,?,?,?,?,?,?,?,?,?,   ?,?,?,?,?,?,?,?,?,?,    ?,?,?,?,?,?,?,?,?,?,    ?,?,?,?,?,?,?,?,?,?,?,    ?,?,?,?,?,?,?,?,?,?,    ?,?,?,?,?,?,?', $save_data);

       
       
      if($sp_result[0]->RESULT=="SUCCESS"){
 
          return Response::json(['success' =>true,'msg' => 'Record successfully approved.']);
 
      }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
         
          return Response::json(['errors'=>true,'msg' => 'Duplicate record.','resp'=>'duplicate']);
          
      }else{
 
          return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
      }
      
      exit(); 
    }  //singleApprove end
 
 
   public function view($id){
 
                if(!is_null($id))
                {
                    //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
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
        
                    $objRights = DB::table('TBL_MST_USERROLMAP')
                                    ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
                                    ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
                                    ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                    ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID)
                                    ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                    ->first();
        
                    $ObjMstInventoryClass   = Helper::getMstInventoryClass( $CYID_REF, $BRID_REF, $FYID_REF);
                    $ObjMstUOM              = Helper::getMstUOM( $CYID_REF, $BRID_REF, $FYID_REF);
                    $ObjMstItemGroup        = Helper::getMstItemGroup( $CYID_REF, $BRID_REF, $FYID_REF);
                    $ObjMstItemCategory     = Helper::getMstItemCategory( $CYID_REF, $BRID_REF, $FYID_REF);
                    $ObjMstStore            = Helper::getMstStore( $CYID_REF, $BRID_REF, $FYID_REF);
                    $ObjMstHSN              = Helper::getMstHSN( $CYID_REF, $BRID_REF, $FYID_REF);
                    $ObjMstBusinessUnit     = Helper::getMstBusinessUnit( $CYID_REF, $BRID_REF, $FYID_REF);        
                    $ObjMstAttribute        = Helper::getMstAttribute( $CYID_REF, $BRID_REF, $FYID_REF);
                    $objUdfForItems         = Helper::getUdfForItems( $CYID_REF);
        
                    $objItem = DB::table('TBL_MST_ITEM')                    
                    ->where('TBL_MST_ITEM.ITEMID','=',$id)
                    ->where('TBL_MST_ITEM.CYID_REF','=',$CYID_REF)
                    ->select('TBL_MST_ITEM.*')
                    ->first();
                    
                    $objItemAttribute = DB::table('TBL_MST_ITEMATTRIBUTE')                    
                    ->where('TBL_MST_ITEMATTRIBUTE.ITEMID_REF','=',$id)
                    ->select('TBL_MST_ITEMATTRIBUTE.*')
                    ->get()->toArray();
                    $objattCount = count($objItemAttribute);

                    $subgroupid = $objItem->ISGID_REF;
                    $Objsubgroup =  DB::table('TBL_MST_ITEMSUBGROUP')
                            ->where('TBL_MST_ITEMSUBGROUP.ISGID','=',$subgroupid)
                            ->select('TBL_MST_ITEMSUBGROUP.*')
                            ->first();
        
                    $objItemCheckFlag = DB::table('TBL_MST_ITEMCHECKFLAG')                    
                    ->where('TBL_MST_ITEMCHECKFLAG.ITEMID_REF','=',$id)
                    ->select('TBL_MST_ITEMCHECKFLAG.*')
                    ->first();
                    
                    $objItemTechSpecification = DB::table('TBL_MST_TECH_SPECIFICATION')                    
                    ->where('TBL_MST_TECH_SPECIFICATION.ITEMID_REF','=',$id)
                    ->select('TBL_MST_TECH_SPECIFICATION.*')
                    ->get()->toArray();
                    $objspecCount = count($objItemTechSpecification);
                    if($objspecCount==0){
                        $objspecCount=1;
                    }
        
                           
                    $objItemUOMConv = DB::table('TBL_MST_ITEM_UOMCONV')                    
                    ->where('TBL_MST_ITEM_UOMCONV.ITEMID_REF','=',$id)
                    ->leftJoin('TBL_MST_UOM','TBL_MST_UOM.UOMID','=','TBL_MST_ITEM_UOMCONV.FROM_UOMID_REF')  
                    ->select('TBL_MST_ITEM_UOMCONV.*','TBL_MST_UOM.*')
                    ->get()->toArray();

                    $objItemUOMConvList=[];               
                    foreach ($objItemUOMConv as $index => $row) {
                        $objLabel = DB::table('TBL_MST_UOM')
                            ->where('UOMID','=',$row->TO_UOMID_REF)
                            ->select('UOMCODE','DESCRIPTIONS','UOMID')
                            ->first();
                         
                        $objItemUOMConvList[$index]['from_label'] = $row->UOMCODE.'-'.$row->DESCRIPTIONS;
                        $objItemUOMConvList[$index]['to_qty'] =  $row->TO_QTY;
                        $objItemUOMConvList[$index]['to_label'] = $objLabel->UOMCODE.'-'.$objLabel->DESCRIPTIONS;
                        $objItemUOMConvList[$index]['from_qty'] =  $row->FROM_QTY;
                    }

                    $objuomCount = count($objItemUOMConv);

                    
                    $objItemUDF = DB::table('TBL_MST_ITEM_UDF')                    
                    ->where('TBL_MST_ITEM_UDF.ITEMID_REF','=',$id)
                    ->leftJoin('TBL_MST_UDFFOR_ITEM','TBL_MST_UDFFOR_ITEM.UDFITEMID','=','TBL_MST_ITEM_UDF.UDFITEMID_REF')                
                    ->select('TBL_MST_ITEM_UDF.*','TBL_MST_UDFFOR_ITEM.*')
                    ->orderBy('TBL_MST_ITEM_UDF.ITEM_UDFID','ASC')
                    ->get()->toArray();
                    $objudfCount = count($objItemUDF);

                    if($objudfCount==0){
                        $objudfCount=1;
                    }

                    $objGlList =    $this->GlList();

               $objOldGlList=$this->OldGlList($objItem->GLID_REF);

               $CompanyLoginStatus  =   $this->CompanyLoginStatus();
               $TabSetting =   Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
               $check_company   =   $this->checkCompany('Accurate'); 
                    
                    return view('masters.inventory.itemmasters.mstfrm72view',compact(['objItem','objItemAttribute','objItemCheckFlag','objItemTechSpecification',
                                    'objItemUDF','objRights','user_approval_level','ObjMstInventoryClass',
                                    'ObjMstUOM',
                                    'ObjMstItemGroup',
                                    'ObjMstItemCategory',
                                    'ObjMstStore',
                                    'ObjMstHSN',
                                    'ObjMstBusinessUnit',
                                    'ObjMstAttribute',
                                    'objUdfForItems','objattCount','objudfCount','objuomCount','objspecCount','Objsubgroup','objItemUOMConvList','objGlList','objOldGlList','CompanyLoginStatus','TabSetting','check_company']));
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
            $TABLE      =   "TBL_MST_ITEM";
            $FIELD      =   "ITEMID";
            $ACTIONNAME     = $Approvallevel;
            $UPDATE     =   Date('Y-m-d');
            $UPTIME     =   Date('h:i:s.u');
            $IPADDRESS  =   $request->getClientIp();
        

        
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
         $TABLE      =   "TBL_MST_ITEM";
         $FIELD      =   "ITEMID";
         $ID         =   $id;
         $UPDATE     =   Date('Y-m-d');
         $UPTIME     =   Date('h:i:s.u');
         $IPADDRESS  =   $request->getClientIp();
         
         $cancelData[0]= ['NT' =>'TBL_MST_ITEMATTRIBUTE'];
         $cancelData[1]= ['NT' =>'TBL_MST_TECH_SPECIFICATION'];
         $cancelData[2]= ['NT' =>'TBL_MST_ITEM_UOMCONV'];
         $cancelData[3]= ['NT' =>'TBL_MST_ITEM_UDF'];
         $cancel_links["TABLES"] = $cancelData;
         $cancelxml = ArrayToXml::convert($cancel_links);
         
         $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$cancelxml];
         
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


    public function GlList(){
        return  DB::table('TBL_MST_GENERALLEDGER')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('STATUS','=','A')
        ->where('SUBLEDGER','=',0)
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('GLID','GLCODE','GLNAME')
        ->get();
    }

    public function getcompanyname(){
        return  DB::table('TBL_MST_COMPANY')
        ->where('CYID','=',Auth::user()->CYID_REF)
        ->where('STATUS','=','A')
        ->whereRaw("(DEACTIVATED=0 or DEACTIVATED is null)")
        ->select('NAME')
        ->get();
    }


    public function OldGlList($GLID_REF){
        return  DB::table('TBL_MST_GENERALLEDGER')
        ->where('GLID','=',$GLID_REF)
        ->select('GLID','GLCODE','GLNAME')
        ->first();
    }


    public function CompanyLoginStatus(){

        $COMPANY_NAME   =   DB::table('TBL_MST_COMPANY')->where('STATUS','=','A')->where('CYID','=',Auth::user()->CYID_REF)->select('TBL_MST_COMPANY.NAME')->first()->NAME;
    
        //$COMPANY_NAME   =   "ZEP India Pvt Ltd";
	    //$COMPANY_NAME   =   "ALPS India Pvt Ltd";
      //  $COMPANY_NAME   =   "Bsquare India Pvt Ltd";

        $COMPANY_LOGIN  =   "";

        if(strpos($COMPANY_NAME,"ZEP")!== false){
            $COMPANY_LOGIN="ZEP";
        }
        else if(strpos($COMPANY_NAME,"ALPS")!== false){
            $COMPANY_LOGIN="ALPS";
        }

        return array('COMPANY_LOGIN'=>$COMPANY_LOGIN);
    
    }

    public function getAttitionalTabSetting($CYID_REF){
        return  DB::table('TBL_MST_GENERALLEDGER')
        ->where('GLID','=',$GLID_REF)
        ->where('GLID','=',$GLID_REF)
        ->select('GLID','GLCODE','GLNAME')
        ->first();
    }


    public function amendment($id){

        if(!is_null($id)){

            $USERID     =   Auth::user()->USERID;
            $VTID       =   $this->vtid_ref;
            $CYID_REF   =   Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');    
            $FYID_REF   =   Session::get('FYID_REF');

            $sp_user_approval_req = [
                $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
            ];        

            $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
            $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;

            $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',$USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',$CYID_REF)
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$VTID)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first();

            $ObjMstInventoryClass   = Helper::getMstInventoryClass( $CYID_REF, $BRID_REF, $FYID_REF);
            $ObjMstUOM              = Helper::getMstUOM( $CYID_REF, $BRID_REF, $FYID_REF);
            $ObjMstItemGroup        = Helper::getMstItemGroup( $CYID_REF, $BRID_REF, $FYID_REF);
            $ObjMstItemCategory     = Helper::getMstItemCategory( $CYID_REF, $BRID_REF, $FYID_REF);
            $ObjMstStore            = Helper::getMstStore( $CYID_REF, $BRID_REF, $FYID_REF);
            $ObjMstHSN              = Helper::getMstHSN( $CYID_REF, $BRID_REF, $FYID_REF);
            $ObjMstBusinessUnit     = Helper::getMstBusinessUnit( $CYID_REF, $BRID_REF, $FYID_REF);        
            $ObjMstAttribute        = Helper::getMstAttribute( $CYID_REF, $BRID_REF, $FYID_REF);
            $objUdfForItems         = Helper::getUdfForItems( $CYID_REF);

            $objItem = DB::table('TBL_MST_ITEM')                    
            ->where('TBL_MST_ITEM.ITEMID','=',$id)
            ->where('TBL_MST_ITEM.CYID_REF','=',$CYID_REF)
            ->select('TBL_MST_ITEM.*')
            ->first();

            $objItemAttribute = DB::table('TBL_MST_ITEMATTRIBUTE')                    
            ->where('TBL_MST_ITEMATTRIBUTE.ITEMID_REF','=',$id)
            ->select('TBL_MST_ITEMATTRIBUTE.*')
            ->get()->toArray();
            $objattCount = count($objItemAttribute);

            $subgroupid = $objItem->ISGID_REF;
            $Objsubgroup =  DB::table('TBL_MST_ITEMSUBGROUP')
            ->where('TBL_MST_ITEMSUBGROUP.ISGID','=',$subgroupid)
            ->select('TBL_MST_ITEMSUBGROUP.*')
            ->first();

            $objItemCheckFlag = DB::table('TBL_MST_ITEMCHECKFLAG')                    
            ->where('TBL_MST_ITEMCHECKFLAG.ITEMID_REF','=',$id)
            ->select('TBL_MST_ITEMCHECKFLAG.*')
            ->first();

            $objItemTechSpecification = DB::table('TBL_MST_TECH_SPECIFICATION')                    
            ->where('TBL_MST_TECH_SPECIFICATION.ITEMID_REF','=',$id)
            ->select('TBL_MST_TECH_SPECIFICATION.*')
            ->get()->toArray();
            $objspecCount = count($objItemTechSpecification);
            if($objspecCount==0){
                $objspecCount=1;
            }

            $objItemUOMConv = DB::table('TBL_MST_ITEM_UOMCONV')                    
            ->where('TBL_MST_ITEM_UOMCONV.ITEMID_REF','=',$id)
            ->select('TBL_MST_ITEM_UOMCONV.*')
            ->get()->toArray();
            $objuomCount = count($objItemUOMConv);

            $objItemUDF = DB::table('TBL_MST_ITEM_UDF')                    
            ->where('TBL_MST_ITEM_UDF.ITEMID_REF','=',$id)
            ->select('TBL_MST_ITEM_UDF.*')
            ->get()->toArray();

            $objItemUDF = DB::table('TBL_MST_ITEM_UDF')                    
            ->where('TBL_MST_ITEM_UDF.ITEMID_REF','=',$id)
            ->leftJoin('TBL_MST_UDFFOR_ITEM','TBL_MST_UDFFOR_ITEM.UDFITEMID','=','TBL_MST_ITEM_UDF.UDFITEMID_REF')                
            ->select('TBL_MST_ITEM_UDF.*','TBL_MST_UDFFOR_ITEM.*')
            ->orderBy('TBL_MST_ITEM_UDF.ITEM_UDFID','ASC')
            ->get()->toArray();
            $objudfCount = count($objItemUDF);

            if($objudfCount==0){
                $objudfCount=1;
            }
          
            $objGlList      =   $this->GlList();
            $objOldGlList   =   $this->OldGlList($objItem->GLID_REF);

            $objFlage = DB::table('TBL_MST_ITEMCHECKFLAG')                    
            ->where('TBL_MST_ITEMCHECKFLAG.ITEMID_REF','=',$objItem->ITEMID)
            ->select('TBL_MST_ITEMCHECKFLAG.*')
            ->first();

            $objCOMPANY =    $this->getcompanyname();

            $CompanyLoginStatus =   $this->CompanyLoginStatus();
            $TabSetting         =   Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
            $checkCompany       =   Helper::checkCompany(Auth::user()->CYID_REF,'alps');
            $check_company   =   $this->checkCompany('Accurate'); 

            return view('masters.inventory.itemmasters.mstfrm72amendment',compact([
                'objItem','objItemAttribute','objItemCheckFlag','objItemTechSpecification',
                'objItemUOMConv','objItemUDF','objRights','user_approval_level','ObjMstInventoryClass',
                'ObjMstUOM',
                'ObjMstItemGroup',
                'ObjMstItemCategory',
                'ObjMstStore',
                'ObjMstHSN',
                'ObjMstBusinessUnit',
                'ObjMstAttribute',
                'objUdfForItems','objattCount','objudfCount','objuomCount','objspecCount','Objsubgroup',
                'objGlList','objOldGlList','objFlage','objCOMPANY','CompanyLoginStatus','TabSetting','checkCompany','check_company'
            ]));

        }

    }

    public function check_transaction(Request $request){
   
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $USERID         =   Auth::user()->USERID;
        $ICODE          =   trim($_REQUEST['ICODE']);
        $OBJ_ITEMID     =   DB::table('TBL_MST_ITEM')
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('ICODE','=',$ICODE)
                            ->select('ITEMID')->first();

        $TRANS_ROW      =   0;
        $BATCH_ROW      =   0;

        if(isset($OBJ_ITEMID->ITEMID) && $OBJ_ITEMID->ITEMID !=''){

            $ITEMID_REF =   $OBJ_ITEMID->ITEMID;

            $TRANS_ROW1 =   DB::select("SELECT COUNT(T1.SOID) AS TOTAL_COUNT 
            FROM TBL_TRN_SLSO01_HDR T1
            INNER JOIN TBL_TRN_SLSO01_MAT T2 ON T1.SOID=T2.SOID_REF
            WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.STATUS='A' AND T2.ITEMID_REF='$ITEMID_REF'")[0]->TOTAL_COUNT;

            $TRANS_ROW2 =   DB::select("SELECT COUNT(T1.POID) AS TOTAL_COUNT 
            FROM TBL_TRN_PROR01_HDR T1
            INNER JOIN TBL_TRN_PROR01_MAT T2 ON T1.POID=T2.POID_REF
            WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.STATUS='A' AND T2.ITEMID_REF='$ITEMID_REF'")[0]->TOTAL_COUNT;

            $BATCH_ROW1 =   DB::select("SELECT COUNT(BATCHID) AS TOTAL_COUNT FROM TBL_MST_BATCH WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND ITEMID_REF='$ITEMID_REF'")[0]->TOTAL_COUNT;
            $QIG_ROW1   =   DB::select("SELECT COUNT(QIGID) AS TOTAL_COUNT FROM TBL_TRN_QIG_HDR WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' AND STATUS='A' AND ITEMID_REF='$ITEMID_REF'")[0]->TOTAL_COUNT;
    
            $BAR_ROW1   =   DB::select("SELECT COUNT(T1.BRCID) AS TOTAL_COUNT 
            FROM TBL_TRN_BARCODE_HDR T1
            INNER JOIN TBL_TRN_BARCODE_MAT T2 ON T1.BRCID=T2.BRCID_REF
            WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.STATUS='A' AND T2.ITEMID_REF='$ITEMID_REF'")[0]->TOTAL_COUNT;

            $TRANS_ROW  =   intval($TRANS_ROW1)+intval($TRANS_ROW2);
            $BATCH_ROW  =   intval($BATCH_ROW1);
            $QIG_ROW    =   intval($QIG_ROW1);
            $BAR_ROW    =   intval($BAR_ROW1);
        }

        echo $TRANS_ROW.'_'.$BATCH_ROW.'_'.$QIG_ROW.'_'.$BAR_ROW;die;

    }

    public function check_uom_transaction(Request $request){
   
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $USERID         =   Auth::user()->USERID;
        $ICODE          =   trim($_REQUEST['ICODE']);
        $ALT_UOM        =   trim($_REQUEST['ALT_UOM']);
        $OBJ_ITEMID     =   DB::table('TBL_MST_ITEM')
                            ->where('CYID_REF','=',$CYID_REF)
                            ->where('ICODE','=',$ICODE)
                            ->select('ITEMID')->first();

        $TRANS_ROW      =   0;
        
        if(isset($OBJ_ITEMID->ITEMID) && $OBJ_ITEMID->ITEMID !=''){

            $ITEMID_REF =   $OBJ_ITEMID->ITEMID;

            $TRANS_ROW1 =   DB::select("SELECT COUNT(T1.SOID) AS TOTAL_COUNT 
            FROM TBL_TRN_SLSO01_HDR T1
            INNER JOIN TBL_TRN_SLSO01_MAT T2 ON T1.SOID=T2.SOID_REF
            WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.STATUS='A' AND T2.ITEMID_REF='$ITEMID_REF' AND T2.ALT_UOMID_REF='$ALT_UOM'")[0]->TOTAL_COUNT;

            $TRANS_ROW2 =   DB::select("SELECT COUNT(T1.POID) AS TOTAL_COUNT 
            FROM TBL_TRN_PROR01_HDR T1
            INNER JOIN TBL_TRN_PROR01_MAT T2 ON T1.POID=T2.POID_REF
            WHERE T1.CYID_REF='$CYID_REF' AND T1.BRID_REF='$BRID_REF' AND T1.STATUS='A' AND T2.ITEMID_REF='$ITEMID_REF' AND T2.ALTUOMID_REF='$ALT_UOM'")[0]->TOTAL_COUNT;

            $TRANS_ROW  =   intval($TRANS_ROW1)+intval($TRANS_ROW2);
        }

        echo $TRANS_ROW;die;

    }

    public function saveamendment(Request $request){
     
        $ICODE                  =   strtoupper(trim($request['ICODE']));     
        $NAME                   =   trim($request['NAME']);  
        $PARTNO                 =   !is_null($request['PARTNO'])?trim($request['PARTNO']):0;              
        $DRAWINGNO              =   !is_null($request['DRAWINGNO'])?trim($request['DRAWINGNO']):0; 
        $CLASSID_REF            =   trim($request['invcls_id']);  

        $MAIN_UOMID_REF         =   trim($request['maiuomref_id']);   
        $ALT_UOMID_REF          =   trim($request['altuomref_id']);   
        $ITEM_DESC              =   !is_null($request['ITEM_DESC'])?trim($request['ITEM_DESC']):'';      
        $ITEMGID                =   trim($request['itegrp_id']);      
        $ISGID                  =   trim($request['itesubgrp_id']);   

        $SAP_CUSTOMER_CODE      =   $request['SAP_CUSTOMER_CODE'];  
        $SAP_CUSTOMER_NAME      =   $request['SAP_CUSTOMER_NAME'];  
        $SAP_PART_NO            =   $request['SAP_PART_NO'];  
        $SAP_PART_DESC          =   $request['SAP_PART_DESC'];  
        $SAP_CUST_PARTNO        =   $request['SAP_CUST_PARTNO'];  
        $SAP_MARTKET_SETCODE    =   $request['SAP_MARTKET_SETCODE'];  
        $LOTSIZEQTY             =   $request['LOTSIZEQTY'];  
        $ALPS_PART_NO           =   $request['ALPS_PART_NO'];  
        $CUSTOMER_PART_NO       =   $request['CUSTOMER_PART_NO'];  
        $OEM_PART_NO            =   $request['OEM_PART_NO'];  

        $ICID                   =   trim($request['itecat_id']);
        $STID                   =   trim($request['defsto_id']);
        $HSNID                  =   !is_null($request['hsn_id'])?trim($request['hsn_id']):NULL;   
        $IVM                    =   trim($request['IVM']);  
        $BUID_REF               =   !is_null($request['busuni_id'])? $request['busuni_id']:NULL;   

        $STDCOST                =   !is_null($request['STDCOST'])?trim($request['STDCOST']):0; 
        $MINLEVEL               =   !is_null($request['MINLEVEL'])?trim($request['MINLEVEL']):0;             
        $REORDER                =   !is_null($request['REORDERLEVEL'])?trim($request['REORDERLEVEL']):0;  
        $MAXLEVEL               =   !is_null($request['MAXLEVEL'])?trim($request['MAXLEVEL']):0;    
        $ITEM_SPECI             =   !is_null($request['ITEM_SPECI'])?trim($request['ITEM_SPECI']):'';  

        $DEACTIVATED            =   (isset($request['DEACTIVATED']) )? 1 : 0 ;       

        $newDateString          =   NULL;
        $newdt = !(is_null($request['DODEACTIVATED']) || empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 
        if(!is_null($newdt) ){
            $newdt          = str_replace( "/", "-",  $newdt ) ;
            $newDateString  = Carbon::parse($newdt)->format('Y-m-d');        
        }

        $DODEACTIVATED      =   $newDateString;
        $CUSTOM_DUTY_RATE   =   !is_null($request['SCDRate'])?trim($request['SCDRate']):0;  
        $STD_SWS_RATE       =   !is_null($request['SSRate'])?trim($request['SSRate']):0;   

        $CYID_REF           =   Auth::user()->CYID_REF;
        $BRID_REF           =   Session::get('BRID_REF');
        $FYID_REF           =   Session::get('FYID_REF'); 
      
        $QCA                =   trim($request['QCA']); 
        $SRNO               =   trim($request['SRNOA']); 
        $BATCHNO            =   trim($request['BATCHNOA']); 
        $INV                =   trim($request['INVMANTAIN']); 
        $EXPIRY_APPLICABLE  =   trim($request['EXPIRY_APPLICABLE']); 
        
        $LEAD_DAYS   =   $request['LEAD_DAYS'];
        $SHELF_LIFE  =   $request['SHELF_LIFE']; 
        $INCA        =   trim($request['INCA']); 
        $BIN         =   trim($request['BIN']); 
        $WARA        =   trim($request['WARA']); 
        $WARA_MONTH  =   trim($request['WARA_MONTH']);

        $TCS                =   0;   
    
        //ATTRIBUTES
        $attrData=array();
        $r_count1 = $request['Row_Count1'];
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['attrcode_'.$i]) && $request['attrcode_'.$i] !=""){
                $attrData[$i]['ATTRIBUTECODE'] =  $request['attrcode_'.$i];
                $attrData[$i]['VALUE'] =    $request['attrvalue_'.$i];
            }
        }

        if(!empty($attrData)){
            $attrwrapped["ATTRIBUTE"] = $attrData;    
            $attr_xml = ArrayToXml::convert($attrwrapped);
            $ATTRIBUTEXML = $attr_xml;
        }
        else{
            $ATTRIBUTEXML = NULL;
        }

        //TECHNICAL SPECIFICATION     //CHECK FOR SINGLE ROW BLANK
        $r_count2 = $request['Row_Count2'];
        $techspec_Data=[];
        for ($i=0; $i<=$r_count2; $i++)
        {
            $techspec_request = isset( $request['TSTYPE_'.$i]) &&  (!is_null($request['TSTYPE_'.$i]) )? $request['TSTYPE_'.$i] : '';
            if(trim($techspec_request)!='')
            {
                $techspec_Data[$i]['TSTYPE'] =  $request['TSTYPE_'.$i];
                $techspec_Data[$i]['VALUE'] =   isset( $request['TSVALUE_'.$i]) &&  (!is_null($request['TSVALUE_'.$i]) )? $request['TSVALUE_'.$i] : '';
            }
        }  

        if(count($techspec_Data)>0){
            $techspec_wrapped["TECHNICALSPECIFICATION"] = $techspec_Data;        
            $techspec__xml = ArrayToXml::convert($techspec_wrapped);
            $TECHNICALXML  = $techspec__xml;        

        }else{

            $TECHNICALXML = NULL;
        }


        $IMAGEXML = 'NULL';

        //UOM CONVERSION
        $r_count3 = $request['Row_Count3'];
        for ($i=0; $i<=$r_count3; $i++)
        {
            if(isset($request['hdntxt_from_uomid_'.$i]))
            {
                $uomconv_Data[$i]['FROMUOM'] =    $request['hdntxt_from_uomid_'.$i];
                $uomconv_Data[$i]['FROMQTY'] =    $request['FROM_QTY_'.$i];
                $uomconv_Data[$i]['TOUOM'] =      $request['touom_'.$i];  
                $uomconv_Data[$i]['TOQTY'] =      $request['TO_QTY_'.$i]; 
            }
        }  

        if($MAIN_UOMID_REF != $ALT_UOMID_REF){
            $uomconv_wrapped["UOM"] = $uomconv_Data;        
            $uomconv__xml = ArrayToXml::convert($uomconv_wrapped);
            $XMLUOM  = $uomconv__xml;
        }
        else{
            $XMLUOM  = NULL; 
        }


        //UDF FIELDS
        $r_count4 = $request['Row_Count4'];
        $udffield_Data = [];
        for ($i=0; $i<=$r_count4; $i++)
        {
            
            if(isset( $request['udffie_'.$i]))
            {
                $udffield_Data[$i]['UDFFIELDS'] = $request['udffie_'.$i]; 
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

        $VTID = $this->vtid_ref;
        $USERID = Auth::user()->USERID;
        $UPDATE =  Date('Y-m-d');
        $UPTIME = Date('h:i:s.u');

        $ACTION     =   "AMENDMENT";
        $IPADDRESS  =   $request->getClientIp();

        //server validation begin
        $rules = [
            'ICODE' => 'required',
            'NAME' => 'required',
            'CLASSID_REF' => 'required',
            'MAIN_UOMID_REF' => 'required',          
            'ALT_UOMID_REF' => 'required',          
            'ITEMGID_REF' => 'required',          
            'ISGID_REF' => 'required',          
            'ICID_REF' => 'required',          
            'STID_REF' => 'required',          
            'HSNID_REF' => 'required',          
        ];


        $req_data = [
            'ICODE' => $ICODE ,
            'NAME' => $NAME,
            'CLASSID_REF' =>  $CLASSID_REF,
            'MAIN_UOMID_REF' => $MAIN_UOMID_REF,          
            'ALT_UOMID_REF' => $ALT_UOMID_REF,          
            'ITEMGID_REF' => $ITEMGID,          
            'ISGID_REF' => $ISGID,          
            'ICID_REF' =>  $ICID,          
            'STID_REF' =>  $STID,          
            'HSNID_REF' => $HSNID,       
        ]; 


        $validator = Validator::make( $req_data, $rules, $this->messages);

        if ($validator->fails())
        {
            return Response::json(['errors' => $validator->errors(),'form'=>'invalid']);	
        }

        //server validation begin

        $ITEM_TYPE 	    =   isset($request['ITEM_TYPE']) && !is_null($request['ITEM_TYPE'])?trim($request['ITEM_TYPE']):NULL;
        $MATERIAL_TYPE 				=   isset($request['MATERIAL_TYPE']) && !is_null($request['MATERIAL_TYPE'])?trim($request['MATERIAL_TYPE']):NULL;
        $GLID_REF 		=   isset($request['GLID_REF']) && !is_null($request['GLID_REF'])?trim($request['GLID_REF']):NULL;

        $BARCODE_APPLICABLE     =   isset($request['BARCODE_APPLICABLE'])?trim($request['BARCODE_APPLICABLE']):NULL; 
        $SERIALNO_MODE          =   isset($request['SERIALNO_MODE'])?trim($request['SERIALNO_MODE']):NULL; 
        $SERIALNO_PREFIX        =   isset($request['SERIALNO_PREFIX'])?trim($request['SERIALNO_PREFIX']):NULL; 
        $SERIALNO_STARTS_FROM   =   isset($request['SERIALNO_STARTS_FROM'])?trim($request['SERIALNO_STARTS_FROM']):NULL; 
        $SERIALNO_SUFFIX        =   isset($request['SERIALNO_SUFFIX'])?trim($request['SERIALNO_SUFFIX']):NULL; 
        $SERIALNO_MAX_LENGTH    =   isset($request['SERIALNO_MAX_LENGTH'])?trim($request['SERIALNO_MAX_LENGTH']):NULL; 

            
        $save_data = [
            $ICODE,$NAME,$PARTNO, $DRAWINGNO,$CLASSID_REF,$MAIN_UOMID_REF,$ALT_UOMID_REF,
            $ITEM_DESC,
            $ITEMGID,
            $ISGID,
            $ICID,
            $STID,
            $HSNID,
            $IVM,
            $BUID_REF,
            $STDCOST,
            $MINLEVEL,
            $REORDER,
            $MAXLEVEL,
            $ITEM_SPECI,
            $DEACTIVATED,
            $DODEACTIVATED,
            $CUSTOM_DUTY_RATE,
            $STD_SWS_RATE,
            $CYID_REF,
            $BRID_REF,
            $FYID_REF,
            $QCA,
            $SRNO,
            $BATCHNO,
            $INV,
            $EXPIRY_APPLICABLE,
            $TCS,

            $ITEM_TYPE,
            $MATERIAL_TYPE,
            $GLID_REF,

            

            $SAP_CUSTOMER_CODE,
            $SAP_CUSTOMER_NAME,
            $SAP_PART_NO,
            $SAP_PART_DESC,
            $SAP_CUST_PARTNO,
            $SAP_MARTKET_SETCODE,
            $LOTSIZEQTY,

            $ATTRIBUTEXML,
            $TECHNICALXML,
            $XMLUOM,
            $XMLUDF, 
            $VTID,
            $USERID,
            $UPDATE,
            $UPTIME,
            $ACTION,
            $IPADDRESS,
            $ALPS_PART_NO,  
            $CUSTOMER_PART_NO,  
            $OEM_PART_NO,
            $BARCODE_APPLICABLE,$SERIALNO_MODE,$SERIALNO_PREFIX,$SERIALNO_STARTS_FROM,$SERIALNO_SUFFIX,$SERIALNO_MAX_LENGTH,
            $INCA,$BIN,$WARA,$LEAD_DAYS,$SHELF_LIFE,$WARA_MONTH
        ];

        

        $sp_result = DB::select('EXEC SP_ITEM_AMENDMENT 
                                        ?,?,?,?,?, 
                                        ?,?,?,?,?,
                                        ?,?,?,?,?,
                                        ?,?,?,?,?, 
                                        ?,?,?,?,?,?, 
                                        ?,?,?,?,?,?,?,
                                        ?,?,?,?,?,?,
                                        ?,?,?,?,?,?,
                                        ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,
                                        ?,?,?,?,?,?,?', $save_data);
   
        if($sp_result[0]->RESULT=="SUCCESS"){
            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);
        }
        elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
            return Response::json(['errors'=>true,'msg' => 'Duplicate record.','resp'=>'duplicate']);
        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        }

        exit();             
    }


    public function checkCompany($str){
        $COMPANY_NAME   =   DB::table('TBL_MST_COMPANY')->where('STATUS','=','A')->where('CYID','=',Auth::user()->CYID_REF)->select('TBL_MST_COMPANY.NAME')->first()->NAME;
        //$COMPANY_NAME ='ff';
        $hidden         =   strpos($COMPANY_NAME,$str)!== false?'':'hidden';
        return $hidden;
    }
   

}
