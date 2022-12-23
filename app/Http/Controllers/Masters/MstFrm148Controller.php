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

class MstFrm148Controller extends Controller
{
   
    protected $form_id = 148;
    protected $vtid_ref  = 222;  //voucher type id

    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

       
    }

    public function add(){

        $cyidRef =  Auth::user()->CYID_REF;
        $bridRef = Session::get('BRID_REF');
        $fyidRef = Session::get('FYID_REF');
        $status  ='A';
       
        $objPopup1List = DB::table('TBL_MST_ITEM')
            ->leftJoin('TBL_MST_UOM', 'TBL_MST_ITEM.MAIN_UOMID_REF','=','TBL_MST_UOM.UOMID')
            ->where('TBL_MST_ITEM.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_ITEM.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_ITEM.DEACTIVATED','!=',1)
            ->where('TBL_MST_ITEM.STATUS','=','A')
            ->select('TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.MAIN_UOMID_REF','TBL_MST_ITEM.ITEM_DESC','TBL_MST_ITEM.ITEM_SPECI','TBL_MST_ITEM.STDCOST AS ITEMCOST','TBL_MST_UOM.UOMID','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS')
            ->orderBy('TBL_MST_ITEM.ICODE','ASC')
            ->get();
       // dd(count($objPopup1List));

        $popup2Data = ['PLCID','PLCCODE','PLCDESCRIPTIONS'];
        $objPriceListCatList   = Helper::getTableData('TBL_MST_PRICELIST_CAT',$popup2Data,Auth::user()->CYID_REF, Session::get('BRID_REF'), Session::get('FYID_REF'),'PLCCODE','ASC');
    

        $objDD = DB::table('TBL_MST_DOCNO_DEFINITION')
                ->where('VTID_REF','=',$this->vtid_ref)
                ->where('CYID_REF','=',$cyidRef)
                ->where('BRID_REF','=',$bridRef)
                ->where('STATUS','=','A')
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
       
      return view('masters.Sales.PriceList.mstfrm148add', compact([
             'objPopup1List','objPriceListCatList','objDD','objDOCNO'
          ]));
       
   }

    public function getItems(Request $request){
        $IFROM = $request['LIST_FROM'];
        if($IFROM=="MRP"){

                    $objMrp = DB::table("TBL_MST_MRP_HDR")
                            ->where('CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('BRID_REF','=',Session::get('BRID_REF'))
                            ->where('DEACTIVATED','!=',1)
                            ->where('STATUS','=','A')
                            ->select('MRPID')
                            ->first();

                    if(!empty($objMrp) || !is_null($objMrp)){
                                $objPopup1List =  DB::table('TBL_MST_MRP_MAT')
                                ->leftJoin('TBL_MST_ITEM', 'TBL_MST_MRP_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                                ->leftJoin('TBL_MST_UOM', 'TBL_MST_MRP_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')
                                ->where('TBL_MST_ITEM.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_ITEM.BRID_REF','=',Session::get('BRID_REF'))
                                ->where('TBL_MST_ITEM.DEACTIVATED','!=',1)
                                ->where('TBL_MST_ITEM.STATUS','=','A')
                                ->where('TBL_MST_MRP_MAT.MRPID_REF','=',$objMrp->MRPID)
                                ->select('TBL_MST_MRP_MAT.*','TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.MAIN_UOMID_REF','TBL_MST_ITEM.ITEM_DESC','TBL_MST_ITEM.ITEM_SPECI','TBL_MST_MRP_MAT.MRP AS ITEMCOST','TBL_MST_UOM.UOMID','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS')
                                ->orderBy('TBL_MST_MRP_MAT.ITEMID_REF','ASC')
                                ->get();
                               
                                
        //                         <tr >
        //   <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_ITEMID_REF[]"  id="record_idref_{{ $listRow1->ITEMID }}" class="cls_popup1_idref" value="{{ $listRow1->ITEMID }}" ></td>
        //   <td class="ROW2" style="width: 34%">{{ $listRow1->ICODE }}
        //   <input type="hidden" id="txtrecord_idref_{{ $listRow1->ITEMID }}" data-desc="{{ $listRow1->ICODE }}" data-desc2="{{ $listRow1->NAME }}" data-desc6="{{ $listRow1->ITEM_SPECI }}"  data-itemcost="{{ $listRow1->ITEMCOST }}" data-id4='{{ $listRow1->MAIN_UOMID_REF }}' data-desc4="{{ $listRow1->UOMCODE }}"    value="{{ $listRow1->ITEMID }}"/>
        //   </td>
        //   <td class="ROW3" style="width: 34%">{{ $listRow1->NAME }}</td>
        //   <td class="ROW2" style="width: 20%" >{{ $listRow1->UOMCODE }}</td>
        // </tr>

                                foreach ($objPopup1List as $index=>$listRow1){
                                    // echo '<tr id="record_idref_'.$listRow1->ITEMID.'" class="cls_popup1_idref">'.
                                    //     '<td style="width: 140px">'.$listRow1->ICODE. '<input type="hidden" id="txtrecord_idref_'.$listRow1->ITEMID.'" data-desc="'.$listRow1->ICODE.'" data-desc2="'. $listRow1->NAME.' " data-desc6="'.$listRow1->ITEM_SPECI.'" data-id4="'.$listRow1->MAIN_UOMID_REF.'"data-desc4="'.$listRow1->UOMCODE.'"  data-itemcost="'.$listRow1->ITEMCOST.'"   value="'.$listRow1->ITEMID.'"/></td><td>'.$listRow1->NAME.'</td><td style="width: 79px;">'.$listRow1->UOMCODE.'</td></tr>';

                                    echo '<tr >'.
                                    '<td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_ITEMID_REF[]"  id="record_idref_'.$listRow1->ITEMID.'" class="cls_popup1_idref" value="{{ $listRow1->ITEMID }}" ></td>'.
                                    '<td class="ROW2" style="width: 34%">'.$listRow1->ICODE. '<input type="hidden" id="txtrecord_idref_'.$listRow1->ITEMID.'" data-desc="'.$listRow1->ICODE.'" data-desc2="'. $listRow1->NAME.' " data-desc6="'.$listRow1->ITEM_SPECI.'" data-id4="'.$listRow1->MAIN_UOMID_REF.'"data-desc4="'.$listRow1->UOMCODE.'"  data-itemcost="'.$listRow1->ITEMCOST.'"   value="'.$listRow1->ITEMID.'"/></td><td class="ROW3" style="width: 34%">'.$listRow1->NAME.'</td><td class="ROW2" style="width: 20%">'.$listRow1->UOMCODE.'</td></tr>';
                                }
                    }else{
                        echo '<tr><td colspan="3">Record not found.</td></tr>';
                    }
            exit();

        }else if($IFROM=="ITEM"){
            $objPopup1List =  DB::table('TBL_MST_ITEM')
                ->leftJoin('TBL_MST_UOM', 'TBL_MST_ITEM.MAIN_UOMID_REF','=','TBL_MST_UOM.UOMID')
                ->where('TBL_MST_ITEM.CYID_REF','=',Auth::user()->CYID_REF)
                ->where('TBL_MST_ITEM.BRID_REF','=',Session::get('BRID_REF'))
                ->where('TBL_MST_ITEM.DEACTIVATED','!=',1)
                ->where('TBL_MST_ITEM.STATUS','=','A')
                ->select('TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.MAIN_UOMID_REF','TBL_MST_ITEM.ITEM_DESC','TBL_MST_ITEM.ITEM_SPECI','TBL_MST_ITEM.STDCOST AS ITEMCOST','TBL_MST_UOM.UOMID','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS')
                ->orderBy('TBL_MST_ITEM.ICODE','ASC')
                ->get();

                if(!empty($objPopup1List)){
                    foreach ($objPopup1List as $index=>$listRow1){
                    //    echo '<tr id="record_idref_'.$listRow1->ITEMID.'" class="cls_popup1_idref">'.
                    //         '<td style="width: 140px">'.$listRow1->ICODE. '<input type="hidden" id="txtrecord_idref_'.$listRow1->ITEMID.'" data-desc="'.$listRow1->ICODE.'" data-desc2="'. $listRow1->NAME.' " data-desc6="'.$listRow1->ITEM_SPECI.'" data-id4="'.$listRow1->MAIN_UOMID_REF.'"data-desc4="'.$listRow1->UOMCODE.'"   data-itemcost="'.$listRow1->ITEMCOST.'"   value="'.$listRow1->ITEMID.'"/></td><td>'.$listRow1->NAME.'</td><td style="width: 79px;">'.$listRow1->UOMCODE.'</td></tr>';
                    echo '<tr >'.
                    '<td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_ITEMID_REF[]"  id="record_idref_'.$listRow1->ITEMID.'" class="cls_popup1_idref" value="{{ $listRow1->ITEMID }}" ></td>'.
                            '<td class="ROW2" style="width: 34%">'.$listRow1->ICODE. '<input type="hidden" id="txtrecord_idref_'.$listRow1->ITEMID.'" data-desc="'.$listRow1->ICODE.'" data-desc2="'. $listRow1->NAME.' " data-desc6="'.$listRow1->ITEM_SPECI.'" data-id4="'.$listRow1->MAIN_UOMID_REF.'"data-desc4="'.$listRow1->UOMCODE.'"   data-itemcost="'.$listRow1->ITEMCOST.'"   value="'.$listRow1->ITEMID.'"/></td><td class="ROW3" style="width: 34%">'.$listRow1->NAME.'</td><td class="ROW2" style="width: 20%">'.$listRow1->UOMCODE.'</td></tr>';

                    }

                }else{
                    echo '<tr><td colspan="3">Record not found.</td></tr>';
                }
            exit();
        }
        
    }//getItems

    public function codeduplicate(Request $request){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $PL_NO =  trim($request['PL_NO']);
        
        $objLabel = DB::table('TBL_MST_PRICELIST_HDR')
            ->where('CYID_REF','=',Auth::user()->CYID_REF)
            ->where('BRID_REF','=',Session::get('BRID_REF'))
            ->where('PL_NO','=',$PL_NO)
            ->select('PL_NO')
            ->first();
        
        if($objLabel){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        exit();
    }

    public function save(Request $request){

        $PL_NO          		=	strtoupper(trim($request['PL_NO']));     
        $PLCID_REF          	=	trim($request['PLCID_REF']);     
        $newdt = !(is_null($request['PL_DT']) || empty($request['PL_DT']) )=="true" ? $request['PL_DT'] : NULL;                 
        if(!is_null($newdt) ){
            $newdt = str_replace( "/", "-",  $newdt ) ;
            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
            $PL_DT = $newDateString;
        }else{
            $PL_DT = NULL;
        }

        $newdt2=!(is_null($request['PERIOD_FRDT']) || empty($request['PERIOD_FRDT']) )=="true" ? $request['PERIOD_FRDT'] : NULL;                 
        if(!is_null($newdt2) ){
            $newdt2 = str_replace( "/", "-",  $newdt2 ) ;
            $newDateString2 = Carbon::parse($newdt2)->format('Y-m-d');        
            $PERIOD_FRDT = $newDateString2;
        }else{
            $PERIOD_FRDT = NULL;
        }
        
        $newdt2=!(is_null($request['PERIOD_TODT']) || empty($request['PERIOD_TODT']) )=="true" ? $request['PERIOD_TODT'] : NULL;                 
        if(!is_null($newdt2) ){
            $newdt2 = str_replace( "/", "-",  $newdt2 ) ;
            $newDateString2 = Carbon::parse($newdt2)->format('Y-m-d');        
            $PERIOD_TODT = $newDateString2;
        }else{
            $PERIOD_TODT = NULL;
        }

        $MRP_APPLICABLE  = isset( $request['MRP_APPLICABLE']) &&  (!is_null($request['MRP_APPLICABLE']) ) ? $request['MRP_APPLICABLE'] : 0;                  
        $PL_TITLE              =   trim($request['PL_TITLE']); 
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF'); 

        $DEACTIVATED 			= 	'0';
        $DODEACTIVATED 			= 	NULL;
          
        $r_count3 = $request['Row_Count3'];
        $materialData = [];
        for ($i=0; $i<=$r_count3; $i++){
            if(isset($request['HDN_ITEMID_REF_'.$i]) && trim($request['HDN_ITEMID_REF_'.$i])!=""){
                $materialData[$i]['ITEMID_REF']  =  $request['HDN_ITEMID_REF_'.$i]; 
                $materialData[$i]['UOMID_REF']   =  $request['HDN_UOMID_REF_'.$i]; 
                $materialData[$i]['ITEMSPECI']   =  trim($request['ITEM_SPEC_'.$i]); 
                $materialData[$i]['LISTPRICE']   =  trim($request['LISTPRICE_'.$i]);  
                $materialData[$i]['CUSTOMER_PRICE']   =  isset($request['CUSTOMER_PRICE_'.$i]) ? trim($request['CUSTOMER_PRICE_'.$i]) :0;  
                $materialData[$i]['DEALER_PRICE']   =  isset($request['DEALER_PRICE_'.$i]) ? trim($request['DEALER_PRICE_'.$i]) :0;  
                $materialData[$i]['MRP']   =  isset($request['MRP_'.$i]) ? trim($request['MRP_'.$i]) : 0;  
                $materialData[$i]['MSP']   =  isset($request['MSP_'.$i]) ? trim($request['MSP_'.$i]) : 0;  
                $materialData[$i]['MRP_PER']     =  isset( $request['MRP_PER_'.$i]) &&  (!is_null($request['MRP_PER_'.$i]) )? $request['MRP_PER_'.$i] : 0;               
                $materialData[$i]['GST_IN_LP']   =  isset( $request['GST_IN_LP_'.$i]) &&  (!is_null($request['GST_IN_LP_'.$i]) )? $request['GST_IN_LP_'.$i] : 0;                  
                $materialData[$i]['REMARKS']     =  trim($request['REMARKS_'.$i]);                                
            }  
        }

        if(count($materialData)>0){            
            $wrapped1["MAT"] = $materialData;    
            $material_xml = ArrayToXml::convert($wrapped1);
            $XMLMAT = $material_xml;
        }else{
            $XMLMAT = NULL;
        }

         
        $VTID 	= $this->vtid_ref;
        $USERID = Auth::user()->USERID;
        $UPDATE ="ADD";
        $UPDATE =  Date('Y-m-d');
        $UPTIME = Date('h:i:s.u');
 
        $ACTION     =   "ADD";
        $IPADDRESS  =   $request->getClientIp();
 
        	
		$save_data = [
            $PL_NO,             $PL_DT,     $PLCID_REF,     $PERIOD_FRDT,   $PERIOD_TODT, 
            $MRP_APPLICABLE,    $PL_TITLE,  $DEACTIVATED,   $DODEACTIVATED, $CYID_REF,
            $BRID_REF,          $FYID_REF,  $XMLMAT,        $VTID,          $USERID,
            $UPDATE,            $UPTIME,    $ACTION,        $IPADDRESS
        ];
        
       // dd($save_data); 
        
        try {

            $sp_result = DB::select('EXEC SP_PRICELIST_IN ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $save_data);

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
    


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
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

        $objDataList=DB::select("SELECT * FROM TBL_MST_PRICELIST_HDR WHERE CYID_REF='$CYID_REF' AND BRID_REF='$BRID_REF' ORDER BY PLID DESC");


       return view('masters.Sales.PriceList.mstfrm148',compact(['objRights','objDataList']));
        
    }

     
     


    //display attachments form
    public function attachment($id){

        if(!is_null($id))
        {
            $objMst = DB::table("TBL_MST_PRICELIST_HDR")
                        ->where('PLID','=',$id)
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

                return view('masters.Sales.PriceList.mstfrm148attachment',compact(['objMst','objMstVoucherType','objAttachments']));
        }

    }




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
        
        $destinationPath =  storage_path()."/docs/company".$CYID_REF."/vendormst";

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
                    
                   // echo $filenametostore ;

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
            return redirect()->route("master",[148,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
        
    
        $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);
 
        if($sp_result[0]->RESULT=="SUCCESS"){

            if(trim($duplicate_files!="")){
                $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
            }

            if(trim($invlid_files!="")){
                $invlid_files =  " Invalid files -  ".$invlid_files;
            }

            return redirect()->route("master",[148,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


        }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
            return redirect()->route("master",[148,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
        }else{

            return redirect()->route("master",[148,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
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
    
                $popup2Data = ['PLCID','PLCCODE','PLCDESCRIPTIONS'];
                $objPriceListCatList   = Helper::getTableData('TBL_MST_PRICELIST_CAT',$popup2Data,Auth::user()->CYID_REF, Session::get('BRID_REF'), Session::get('FYID_REF'),'PLCCODE','ASC');

                $objMstResponse = DB::table('TBL_MST_PRICELIST_HDR')                    
                    ->where('PLID','=',$id)
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF)
                    ->select('*')
                    ->first();
                if(strtoupper($objMstResponse->STATUS)=="A" || strtoupper($objMstResponse->STATUS)=="C"){
                    exit("Sorry, Only Un Approved record can edit.");
                }
               
                $itemsArray = [];
                $objMrpItems = DB::table('TBL_MST_MRP_MAT')                    
                    ->leftJoin('TBL_MST_ITEM','TBL_MST_ITEM.ITEMID','=','TBL_MST_MRP_MAT.ITEMID_REF')                
                    ->leftJoin('TBL_MST_UOM','TBL_MST_UOM.UOMID','=','TBL_MST_MRP_MAT.UOMID_REF')                
                    ->select( 
                        'TBL_MST_MRP_MAT.MRPMATID',
                        'TBL_MST_MRP_MAT.MRPID_REF',
                        'TBL_MST_MRP_MAT.ITEMID_REF',
                        'TBL_MST_MRP_MAT.MRP',
                        'TBL_MST_ITEM.ITEMID',
                        'TBL_MST_ITEM.ICODE',
                        'TBL_MST_ITEM.NAME',
                    )
                    ->whereIn('TBL_MST_MRP_MAT.MRPID_REF',function($query) 
                    {
                        $query->select('MRPID')->from("TBL_MST_MRP_HDR")
                                ->where('STATUS','=','A')
                                ->where('DEACTIVATED','<>',1)->first();
                    })
                    ->orderBy('TBL_MST_MRP_MAT.MRPMATID','ASC')
                    ->get()->toArray();

                    foreach ($objMrpItems as $key => $row) {
                            $itemsArray[$row->ITEMID_REF] = $row->MRP;
                    }
                    
                   
               // dump($objMrpItems);
                $objList1 = DB::table('TBL_MST_PRICELIST_MAT')                    
                    ->where('TBL_MST_PRICELIST_MAT.PLID_REF','=',$id)
                    ->leftJoin('TBL_MST_ITEM','TBL_MST_PRICELIST_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                
                    ->leftJoin('TBL_MST_UOM','TBL_MST_PRICELIST_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                
                    ->select( 
                        'TBL_MST_PRICELIST_MAT.*',
                        'TBL_MST_ITEM.ITEMID',
                        'TBL_MST_ITEM.ICODE',
                        'TBL_MST_ITEM.NAME',
                        'TBL_MST_ITEM.STDCOST AS ITEMCOST',
                        'TBL_MST_UOM.UOMID',
                        'TBL_MST_UOM.UOMCODE'
                    )
                    ->orderBy('TBL_MST_PRICELIST_MAT.PLMATID','ASC')
                    ->get()->toArray();

                //dump($objList1);

                foreach($objList1 as $key=>$row){
                    $searchitem = $row->ITEMID;
                    if (array_key_exists($searchitem, $itemsArray)) {
                        $objList1[$key]->MRP_PRICE = $itemsArray[$searchitem];
                    }else{
                        $objList1[$key]->MRP_PRICE = 0.0;
                    }
                }    

                // dump($objList1);    

                $objList1Count = count($objList1);
                if($objList1Count==0){
                    $objList1Count=1;
                }

                $objPLCategory = DB::table('TBL_MST_PRICELIST_CAT')                    
                ->where('PLCID','=',$objMstResponse->PLCID_REF)
                ->select('PLCID','PLCCODE','PLCDESCRIPTIONS')
                ->first();

               // dump($objPLCategory);

                $IFROM = $objMstResponse->MRP_APPLICABLE;
                if($IFROM==1 || $IFROM==TRUE){
            
                        $objMrp = DB::table("TBL_MST_MRP_HDR")
                                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('BRID_REF','=',Session::get('BRID_REF'))
                                ->where('DEACTIVATED','!=',1)
                                ->where('STATUS','=','A')
                                ->select('MRPID')
                                ->first();
    
                        if(!empty($objMrp) || !is_null($objMrp)){
                                    $objPopup1List =  DB::table('TBL_MST_MRP_MAT')
                                    ->leftJoin('TBL_MST_ITEM', 'TBL_MST_MRP_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                                    ->leftJoin('TBL_MST_UOM', 'TBL_MST_MRP_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')
                                    ->where('TBL_MST_ITEM.CYID_REF','=',Auth::user()->CYID_REF)
                                    ->where('TBL_MST_ITEM.BRID_REF','=',Session::get('BRID_REF'))
                                    ->where('TBL_MST_ITEM.DEACTIVATED','!=',1)
                                    ->where('TBL_MST_ITEM.STATUS','=','A')
                                    ->where('TBL_MST_MRP_MAT.MRPID_REF','=',$objMrp->MRPID)
                                    ->select('TBL_MST_MRP_MAT.*','TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.MAIN_UOMID_REF',
                                    'TBL_MST_ITEM.ITEM_DESC','TBL_MST_ITEM.ITEM_SPECI','TBL_MST_MRP_MAT.MRP AS ITEMCOST','TBL_MST_UOM.UOMID','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS')
                                    ->orderBy('TBL_MST_MRP_MAT.ITEMID_REF','ASC')
                                    ->get();    
                        }else{
                            $objPopup1List=NULL;
                        }
            
                    }else{
                        $objPopup1List =  DB::table('TBL_MST_ITEM')
                            ->leftJoin('TBL_MST_UOM', 'TBL_MST_ITEM.MAIN_UOMID_REF','=','TBL_MST_UOM.UOMID')
                            ->where('TBL_MST_ITEM.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_ITEM.BRID_REF','=',Session::get('BRID_REF'))
                            ->where('TBL_MST_ITEM.DEACTIVATED','!=',1)
                            ->where('TBL_MST_ITEM.STATUS','=','A')
                            ->select('TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.MAIN_UOMID_REF','TBL_MST_ITEM.ITEM_DESC',
                            'TBL_MST_ITEM.ITEM_SPECI','TBL_MST_ITEM.STDCOST AS ITEMCOST','TBL_MST_UOM.UOMID','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS')
                            ->orderBy('TBL_MST_ITEM.ICODE','ASC')
                            ->get();
                }
                
                return view('masters.Sales.PriceList.mstfrm148edit', compact(['objMstResponse','objRights','user_approval_level','objPopup1List','objList1','objList1Count','objPriceListCatList','objPLCategory' ]));
            }

    }//edit function



    public function update(Request $request)
    {

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF'); 
        
        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;       

        $newDateString3 = NULL;
        $newdt3 = !(is_null($request['DODEACTIVATED']) || empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 
        if(!is_null($newdt3) ){
            $newdt3 = str_replace( "/", "-",  $newdt3 ) ;
            $newDateString3 = Carbon::parse($newdt3)->format('Y-m-d');        
        }
        $DODEACTIVATED = $newDateString3;

        $VTID 	= $this->vtid_ref;
        $USERID = Auth::user()->USERID;
        $UPDATE =  Date('Y-m-d');
        $UPTIME = Date('h:i:s.u');
        $ACTION     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();

        $PL_NO          		=	strtoupper(trim($request['PL_NO']));     
        $PLCID_REF          	=	trim($request['PLCID_REF']);     
        $newdt = !(is_null($request['PL_DT']) || empty($request['PL_DT']) )=="true" ? $request['PL_DT'] : NULL;                 
        if(!is_null($newdt) ){
            $newdt = str_replace( "/", "-",  $newdt ) ;
            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
            $PL_DT = $newDateString;
        }else{
            $PL_DT = NULL;
        }

        $newdt2=!(is_null($request['PERIOD_FRDT']) || empty($request['PERIOD_FRDT']) )=="true" ? $request['PERIOD_FRDT'] : NULL;                 
        if(!is_null($newdt2) ){
            $newdt2 = str_replace( "/", "-",  $newdt2 ) ;
            $newDateString2 = Carbon::parse($newdt2)->format('Y-m-d');        
            $PERIOD_FRDT = $newDateString2;
        }else{
            $PERIOD_FRDT = NULL;
        }
        
        $newdt2=!(is_null($request['PERIOD_TODT']) || empty($request['PERIOD_TODT']) )=="true" ? $request['PERIOD_TODT'] : NULL;                 
        if(!is_null($newdt2) ){
            $newdt2 = str_replace( "/", "-",  $newdt2 ) ;
            $newDateString2 = Carbon::parse($newdt2)->format('Y-m-d');        
            $PERIOD_TODT = $newDateString2;
        }else{
            $PERIOD_TODT = NULL;
        }

        $MRP_APPLICABLE = $request['MRP_APPLICABLE'];                  
        $PL_TITLE       = trim($request['PL_TITLE']); 
        $CYID_REF       = Auth::user()->CYID_REF;
        $BRID_REF       = Session::get('BRID_REF');
        $FYID_REF       = Session::get('FYID_REF'); 

        $r_count3 = $request['Row_Count3'];
        $materialData = [];
        for ($i=0; $i<=$r_count3; $i++){
            if(isset($request['HDN_ITEMID_REF_'.$i]) && trim($request['HDN_ITEMID_REF_'.$i])!=""){
                $materialData[$i]['ITEMID_REF']  =  $request['HDN_ITEMID_REF_'.$i]; 
                $materialData[$i]['UOMID_REF']   =  $request['HDN_UOMID_REF_'.$i]; 
                $materialData[$i]['ITEMSPECI']   =  trim($request['ITEM_SPEC_'.$i]); 
                $materialData[$i]['LISTPRICE']   =  trim($request['LISTPRICE_'.$i]);  
                $materialData[$i]['CUSTOMER_PRICE']   =  isset($request['CUSTOMER_PRICE_'.$i]) ? trim($request['CUSTOMER_PRICE_'.$i]) :0;  
                $materialData[$i]['DEALER_PRICE']   =  isset($request['DEALER_PRICE_'.$i]) ? trim($request['DEALER_PRICE_'.$i]) :0;  
                $materialData[$i]['MRP']   =  isset($request['MRP_'.$i]) ? trim($request['MRP_'.$i]) : 0;  
                $materialData[$i]['MSP']   =  isset($request['MSP_'.$i]) ? trim($request['MSP_'.$i]) : 0;  
                $materialData[$i]['MRP_PER']    = isset( $request['MRP_PER_'.$i]) &&  (!is_null($request['MRP_PER_'.$i]) )? $request['MRP_PER_'.$i] : 0;                   
                $materialData[$i]['GST_IN_LP'] = isset( $request['GST_IN_LP_'.$i]) && (!is_null($request['GST_IN_LP_'.$i]) )? $request['GST_IN_LP_'.$i] : 0;                  
                $materialData[$i]['REMARKS']     =  trim($request['REMARKS_'.$i]);                                
            }  
        }

        if(count($materialData)>0){            
            $wrapped1["MAT"] = $materialData;    
            $material_xml = ArrayToXml::convert($wrapped1);
            $XMLMAT = $material_xml;
        }else{
            $XMLMAT = NULL;
        }
        	
		$save_data = [
            $PL_NO,             $PL_DT,     $PLCID_REF,     $PERIOD_FRDT,   $PERIOD_TODT, 
            $MRP_APPLICABLE,    $PL_TITLE,  $DEACTIVATED,   $DODEACTIVATED, $CYID_REF,
            $BRID_REF,          $FYID_REF,  $XMLMAT,        $VTID,          $USERID,
            $UPDATE,            $UPTIME,    $ACTION,        $IPADDRESS
        ];
        
      
		$sp_result = DB::select('EXEC SP_PRICELIST_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $save_data);

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
        
        $VTID 	= $this->vtid_ref;
        $USERID = Auth::user()->USERID;
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF'); 

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
        $UPDATE =  Date('Y-m-d');
        $UPTIME = Date('h:i:s.u');
        $IPADDRESS  =  $request->getClientIp();
        
        $DEACTIVATED = (isset($request['DEACTIVATED']) )? 1 : 0 ;       
        $newDateString3 = NULL;
        $newdt3 = !(is_null($request['DODEACTIVATED']) || empty($request['DODEACTIVATED']) )=="true" ? $request['DODEACTIVATED'] : NULL; 
        if(!is_null($newdt3) ){
            $newdt3 = str_replace( "/", "-",  $newdt3 ) ;
            $newDateString3 = Carbon::parse($newdt3)->format('Y-m-d');        
        }
        $DODEACTIVATED = $newDateString3;


        $PL_NO          		=	strtoupper(trim($request['PL_NO']));     
        $PLCID_REF          	=	trim($request['PLCID_REF']);     
        $newdt = !(is_null($request['PL_DT']) || empty($request['PL_DT']) )=="true" ? $request['PL_DT'] : NULL;                 
        if(!is_null($newdt) ){
            $newdt = str_replace( "/", "-",  $newdt ) ;
            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
            $PL_DT = $newDateString;
        }else{
            $PL_DT = NULL;
        }

        $newdt2=!(is_null($request['PERIOD_FRDT']) || empty($request['PERIOD_FRDT']) )=="true" ? $request['PERIOD_FRDT'] : NULL;                 
        if(!is_null($newdt2) ){
            $newdt2 = str_replace( "/", "-",  $newdt2 ) ;
            $newDateString2 = Carbon::parse($newdt2)->format('Y-m-d');        
            $PERIOD_FRDT = $newDateString2;
        }else{
            $PERIOD_FRDT = NULL;
        }
        
        $newdt2=!(is_null($request['PERIOD_TODT']) || empty($request['PERIOD_TODT']) )=="true" ? $request['PERIOD_TODT'] : NULL;                 
        if(!is_null($newdt2) ){
            $newdt2 = str_replace( "/", "-",  $newdt2 ) ;
            $newDateString2 = Carbon::parse($newdt2)->format('Y-m-d');        
            $PERIOD_TODT = $newDateString2;
        }else{
            $PERIOD_TODT = NULL;
        }

        $MRP_APPLICABLE = $request['MRP_APPLICABLE'];                  
        $PL_TITLE       = trim($request['PL_TITLE']); 

        $r_count3 = $request['Row_Count3'];
        $materialData = [];
        for ($i=0; $i<=$r_count3; $i++){
            if(isset($request['HDN_ITEMID_REF_'.$i]) && trim($request['HDN_ITEMID_REF_'.$i])!=""){
                $materialData[$i]['ITEMID_REF']  =  $request['HDN_ITEMID_REF_'.$i]; 
                $materialData[$i]['UOMID_REF']   =  $request['HDN_UOMID_REF_'.$i]; 
                $materialData[$i]['ITEMSPECI']   =  trim($request['ITEM_SPEC_'.$i]); 
                $materialData[$i]['LISTPRICE']   =  trim($request['LISTPRICE_'.$i]);  
                $materialData[$i]['CUSTOMER_PRICE']   =  isset($request['CUSTOMER_PRICE_'.$i]) ? trim($request['CUSTOMER_PRICE_'.$i]) :0;  
                $materialData[$i]['DEALER_PRICE']   =  isset($request['DEALER_PRICE_'.$i]) ? trim($request['DEALER_PRICE_'.$i]) :0;  
                $materialData[$i]['MRP']   =  isset($request['MRP_'.$i]) ? trim($request['MRP_'.$i]) : 0;  
                $materialData[$i]['MSP']   =  isset($request['MSP_'.$i]) ? trim($request['MSP_'.$i]) : 0;  
                $materialData[$i]['MRP_PER']     = isset( $request['MRP_PER_'.$i]) &&  (!is_null($request['MRP_PER_'.$i]) )? $request['MRP_PER_'.$i] : 0;                 
                $materialData[$i]['GST_IN_LP']   =  isset( $request['GST_IN_LP_'.$i]) &&  (!is_null($request['GST_IN_LP_'.$i]) )? $request['GST_IN_LP_'.$i] : 0;                  
                $materialData[$i]['REMARKS']     =  trim($request['REMARKS_'.$i]);                                
            }  
        }

        if(count($materialData)>0){            
            $wrapped1["MAT"] = $materialData;    
            $material_xml = ArrayToXml::convert($wrapped1);
            $XMLMAT = $material_xml;
        }else{
            $XMLMAT = NULL;
        }
            
     

		$save_data = [
            $PL_NO,             $PL_DT,     $PLCID_REF,     $PERIOD_FRDT,   $PERIOD_TODT, 
            $MRP_APPLICABLE,    $PL_TITLE,  $DEACTIVATED,   $DODEACTIVATED, $CYID_REF,
            $BRID_REF,          $FYID_REF,  $XMLMAT,        $VTID,          $USERID,
            $UPDATE,            $UPTIME,    $ACTION,        $IPADDRESS
        ];
        
        // dump($request->all());
        // dd($save_data);
      
		$sp_result = DB::select('EXEC SP_PRICELIST_UP ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?', $save_data);        
                
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
    
                $popup2Data = ['PLCID','PLCCODE','PLCDESCRIPTIONS'];
                $objPriceListCatList   = Helper::getTableData('TBL_MST_PRICELIST_CAT',$popup2Data,Auth::user()->CYID_REF, Session::get('BRID_REF'), Session::get('FYID_REF'),'PLCCODE','ASC');

                $objMstResponse = DB::table('TBL_MST_PRICELIST_HDR')                    
                    ->where('PLID','=',$id)
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('BRID_REF','=',$BRID_REF)
                    ->select('*')
                    ->first();
               
                $itemsArray = [];
                $objMrpItems = DB::table('TBL_MST_MRP_MAT')                    
                    ->leftJoin('TBL_MST_ITEM','TBL_MST_ITEM.ITEMID','=','TBL_MST_MRP_MAT.ITEMID_REF')                
                    ->leftJoin('TBL_MST_UOM','TBL_MST_UOM.UOMID','=','TBL_MST_MRP_MAT.UOMID_REF')                
                    ->select( 
                        'TBL_MST_MRP_MAT.MRPMATID',
                        'TBL_MST_MRP_MAT.MRPID_REF',
                        'TBL_MST_MRP_MAT.ITEMID_REF',
                        'TBL_MST_MRP_MAT.MRP',
                        'TBL_MST_ITEM.ITEMID',
                        'TBL_MST_ITEM.ICODE',
                        'TBL_MST_ITEM.NAME',
                    )
                    ->whereIn('TBL_MST_MRP_MAT.MRPID_REF',function($query) 
                    {
                        $query->select('MRPID')->from("TBL_MST_MRP_HDR")
                                ->where('STATUS','=','A')
                                ->where('DEACTIVATED','<>',1)->first();
                    })
                    ->orderBy('TBL_MST_MRP_MAT.MRPMATID','ASC')
                    ->get()->toArray();

                    foreach ($objMrpItems as $key => $row) {
                            $itemsArray[$row->ITEMID_REF] = $row->MRP;
                    }
                    
                   
               // dump($objMrpItems);
                $objList1 = DB::table('TBL_MST_PRICELIST_MAT')                    
                    ->where('TBL_MST_PRICELIST_MAT.PLID_REF','=',$id)
                    ->leftJoin('TBL_MST_ITEM','TBL_MST_PRICELIST_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                
                    ->leftJoin('TBL_MST_UOM','TBL_MST_PRICELIST_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                
                    ->select( 
                        'TBL_MST_PRICELIST_MAT.*',
                        'TBL_MST_ITEM.ITEMID',
                        'TBL_MST_ITEM.ICODE',
                        'TBL_MST_ITEM.NAME',
                        'TBL_MST_ITEM.STDCOST AS ITEMCOST',
                        'TBL_MST_UOM.UOMID',
                        'TBL_MST_UOM.UOMCODE'
                    )
                    ->orderBy('TBL_MST_PRICELIST_MAT.PLMATID','ASC')
                    ->get()->toArray();

                //dump($objList1);

                foreach($objList1 as $key=>$row){
                    $searchitem = $row->ITEMID;
                    if (array_key_exists($searchitem, $itemsArray)) {
                        $objList1[$key]->MRP_PRICE = $itemsArray[$searchitem];
                    }else{
                        $objList1[$key]->MRP_PRICE = 0.0;
                    }
                }    

                // dump($objList1);    

                $objList1Count = count($objList1);
                if($objList1Count==0){
                    $objList1Count=1;
                }

                $objPLCategory = DB::table('TBL_MST_PRICELIST_CAT')                    
                ->where('PLCID','=',$objMstResponse->PLCID_REF)
                ->select('PLCID','PLCCODE','PLCDESCRIPTIONS')
                ->first();

               // dump($objPLCategory);

                // $IFROM = $objMstResponse->MRP_APPLICABLE;
                // if($IFROM==1 || $IFROM==TRUE){
            
                //         $objMrp = DB::table("TBL_MST_MRP_HDR")
                //                 ->where('CYID_REF','=',Auth::user()->CYID_REF)
                //                 ->where('BRID_REF','=',Session::get('BRID_REF'))
                //                 ->where('FYID_REF','=',Session::get('FYID_REF'))
                //                 ->where('DEACTIVATED','!=',1)
                //                 ->where('STATUS','=','A')
                //                 ->select('MRPID')
                //                 ->first();
    
                //         if(!empty($objMrp) || !is_null($objMrp)){
                //                     $objPopup1List =  DB::table('TBL_MST_MRP_MAT')
                //                     ->leftJoin('TBL_MST_ITEM', 'TBL_MST_MRP_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')
                //                     ->leftJoin('TBL_MST_UOM', 'TBL_MST_MRP_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')
                //                     ->where('TBL_MST_ITEM.CYID_REF','=',Auth::user()->CYID_REF)
                //                     ->where('TBL_MST_ITEM.BRID_REF','=',Session::get('BRID_REF'))
                //                     ->where('TBL_MST_ITEM.FYID_REF','=',Session::get('FYID_REF'))
                //                     ->where('TBL_MST_ITEM.DEACTIVATED','!=',1)
                //                     ->where('TBL_MST_ITEM.STATUS','=','A')
                //                     ->where('TBL_MST_MRP_MAT.MRPID_REF','=',$objMrp->MRPID)
                //                     ->select('TBL_MST_MRP_MAT.*','TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.MAIN_UOMID_REF',
                //                     'TBL_MST_ITEM.ITEM_DESC','TBL_MST_ITEM.ITEM_SPECI','TBL_MST_MRP_MAT.MRP AS ITEMCOST','TBL_MST_UOM.UOMID','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS')
                //                     ->orderBy('TBL_MST_MRP_MAT.ITEMID_REF','ASC')
                //                     ->get();    
                //         }else{
                //             $objPopup1List=NULL;
                //         }
            
                //     }else{
                //         $objPopup1List =  DB::table('TBL_MST_ITEM')
                //             ->leftJoin('TBL_MST_UOM', 'TBL_MST_ITEM.MAIN_UOMID_REF','=','TBL_MST_UOM.UOMID')
                //             ->where('TBL_MST_ITEM.CYID_REF','=',Auth::user()->CYID_REF)
                //             ->where('TBL_MST_ITEM.BRID_REF','=',Session::get('BRID_REF'))
                //             ->where('TBL_MST_ITEM.FYID_REF','=',Session::get('FYID_REF'))
                //             ->where('TBL_MST_ITEM.DEACTIVATED','!=',1)
                //             ->where('TBL_MST_ITEM.STATUS','=','A')
                //             ->select('TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.MAIN_UOMID_REF','TBL_MST_ITEM.ITEM_DESC',
                //             'TBL_MST_ITEM.ITEM_SPECI','TBL_MST_ITEM.STDCOST AS ITEMCOST','TBL_MST_UOM.UOMID','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS')
                //             ->orderBy('TBL_MST_ITEM.ICODE','ASC')
                //             ->get();
                // }
                
                return view('masters.Sales.PriceList.mstfrm148view', compact(['objMstResponse','objRights','user_approval_level','objList1','objList1Count','objPriceListCatList','objPLCategory' ]));
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
            $TABLE      =   "TBL_MST_PRICELIST_HDR";
            $FIELD      =   "PLID";
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
        $TABLE      =   "TBL_MST_PRICELIST_HDR";
        $FIELD      =   "PLID";
       
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $cancelData[0]['NT']   =  "TBL_MST_PRICELIST_MAT"; 
        $cancelWrapped["TABLES"] = $cancelData;    
        $XMLCANCEL = ArrayToXml::convert($cancelWrapped);
        
        $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME,$IPADDRESS,$XMLCANCEL ];
 
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

}
