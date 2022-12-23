<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TblMstFrm70;
use DB;
use Response;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

use Spatie\ArrayToXml\ArrayToXml;
use Carbon\Carbon;


class MstFrm70Controller extends Controller{
   
    protected $form_id = 70;
    protected $vtid_ref   = 70;

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        $USERID     =   Auth::user()->USERID;
        $VTID       =   $this->vtid_ref;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');    
        $FYID_REF   =   Session::get('FYID_REF');
        $Status = 'A';

        $sp_user_approval_req = [
            $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
        ];        

        $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
        $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;


        $objRights = DB::table('TBL_MST_USERROLMAP')
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first(); 

        $OpeningStatus  =   NULL;
        $OpeningDate    =   NULL;

        $objData = DB::select("SELECT 
        TOP 1 OPENING_BL_DT AS DOOB,STATUS
        FROM TBL_MST_ITEM_OB_HDR 
        WHERE  CYID_REF ='$CYID_REF' AND BRID_REF = '$BRID_REF'  AND FYID_REF = '$FYID_REF' ");
        
        if(isset($objData) && !empty($objData)){
            $OpeningStatus  =   $objData[0]->STATUS;
            $OpeningDate    =   $objData[0]->DOOB;
        }

        $AlpsStatus =   $this->AlpsStatus();

        $Data_HDR  =   DB::table('TBL_MST_ITEM_OB_HDR')->where('CYID_REF','=',$CYID_REF)->where('BRID_REF','=',$BRID_REF)->where('FYID_REF','=',$FYID_REF)->first(); 
        
        if(isset($Data_HDR) && $Data_HDR->IOBID !=""){

            $TotalData  =   DB::table('TBL_MST_ITEM_OB_MAT')->where('IOBID_REF','=',$Data_HDR->IOBID)->count();  
        }
        else{
            $TotalData  =   0;
        }

        return view('masters.inventory.IOB.mstfrm70edit',compact(['objRights','user_approval_level','AlpsStatus','OpeningStatus','OpeningDate','TotalData']));

    }

    public function listing(){

        $USERID     =   Auth::user()->USERID;
        $VTID       =   $this->vtid_ref;
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');    
        $FYID_REF   =   Session::get('FYID_REF');
        $Status     =   'A';

        $DOOB       =   $_POST["DOOB"];
        $disabled   =   $_POST["OpeningStatus"] =='A'?'disabled':'';

        $start      =   $_POST["start"];
        $start      =   $_POST["start"];
        $limit      =   $_POST["limit"];
        $indexno    =   $_POST["indexno"];
        
        $cur_date = Date('Y-m-d');
        
        $objResponse = DB::select('select top 1 * from TBL_MST_ITEM_OB_HDR  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?)  and CYID_REF = ? and BRID_REF = ?', [$cur_date, $CYID_REF, $BRID_REF]);
                          
        $ObjItem =  DB::select("SELECT * 
        FROM TBL_MST_ITEM 
        WHERE CYID_REF = '$CYID_REF' 
        AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS ='A' 
        ORDER BY ITEMID 
        OFFSET $start ROWS
        FETCH NEXT  $limit ROWS ONLY");

        $tempobjitem = $ObjItem;    
              
        if(!empty($tempobjitem)){

            foreach ($tempobjitem as $index=>$dataRow){                    
                    
                if(!empty($objResponse)){
                    $ObjOBMat = DB::select('select top 1 * from TBL_MST_ITEM_OB_MAT  where IOBID_REF = ? and ITEMID_REF = ?  and UOMID_REF = ?', [$objResponse[0]->IOBID, $dataRow->ITEMID, $dataRow->MAIN_UOMID_REF]);

                    $ObjItem[$index]->OPENING_BL = isset($ObjOBMat[0]->OPENING_BL)? $ObjOBMat[0]->OPENING_BL : 0;                    
                    $ObjItem[$index]->OPENING_RATE = isset($ObjOBMat[0]->RATE)? $ObjOBMat[0]->RATE : 0;                    
                    $ObjItem[$index]->OPENING_VL = isset($ObjOBMat[0]->OPENING_VL)? $ObjOBMat[0]->OPENING_VL : 0;    
                   
                    if(!empty($ObjOBMat)){
                        $strdata = !is_null($ObjOBMat[0]->STID_REF) && !empty($ObjOBMat[0]->STID_REF) ? $ObjOBMat[0]->STID_REF : ''; 
                        $storeids = json_encode(explode(",",$strdata));
                        
                    }else{
                       $strdata = ''; 
                       $storeids = json_encode(explode(",",$strdata));
                       
                    }

          

                    $ObjItem[$index]->UNIQ_STORE_IDS =  $storeids;   
                   
                    $ObjItem[$index]->IOBID_REF = $objResponse[0]->IOBID;    
                    $ObjItem[$index]->IOBID_STATUS =  $objResponse[0]->STATUS;

                    $ObjOBStore = DB::table('TBL_MST_ITEM_OB_STORE')
                                    ->where('IOBID_REF','=',$objResponse[0]->IOBID)
                                    ->where('ITEMID_REF','=',$dataRow->ITEMID)
                                    ->where('MUOMID_REF','=',$dataRow->MAIN_UOMID_REF)
                                    ->select('*')
                                    ->get()->toArray(); 

                       
                    if(!empty($ObjOBStore)){
                        
                        $strdata = array();
                       
                        foreach ($ObjOBStore as $dindex => $value) {
                            $str1 = array();
                            $value->actiontype='system';
                            $str1[] =  $value;
                            $strdata[$dindex] =  $str1;
                        }

                       $ObjItem[$index]->STORE_DETAILS = json_encode($strdata);

                       
                    }else{
                        $ObjItem[$index]->STORE_DETAILS = json_encode('');   
                    }



                }else{

                    $ObjItem[$index]->OPENING_BL = 0;                    
                    $ObjItem[$index]->OPENING_RATE = 0;                    
                    $ObjItem[$index]->OPENING_VL = 0;    
                    $ObjItem[$index]->UNIQ_STORE_IDS = json_encode('');      
                    $ObjItem[$index]->IOBID_REF = 0;    
                    $ObjItem[$index]->IOBID_STATUS = 'N';    
                    $ObjItem[$index]->STORE_DETAILS = json_encode('');   
                }    

                

                $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                            WHERE  CYID_REF = ?  AND UOMID = ? 
                            AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                            [$CYID_REF, $dataRow->MAIN_UOMID_REF, 'A' ]);

                $ObjAltUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                            WHERE  CYID_REF = ?  AND UOMID = ? 
                            AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                            [$CYID_REF, $dataRow->ALT_UOMID_REF, $Status ]);
                
                $ObjAltQTY =  DB::select('SELECT TOP 1  * FROM TBL_MST_ITEM_UOMCONV  
                            WHERE  ITEMID_REF = ? AND TO_UOMID_REF = ? ',
                            [$dataRow->ITEMID,$dataRow->ALT_UOMID_REF ]);

              

                
                $ObjItem[$index]->MainUOMCode =  $ObjMainUOM[0]->UOMCODE;
                $ObjItem[$index]->MainUOMDesc =  $ObjMainUOM[0]->DESCRIPTIONS;
                
                $ObjItem[$index]->AltUOMCode =  $ObjAltUOM[0]->UOMCODE;
                $ObjItem[$index]->AltUOMDesc =  $ObjAltUOM[0]->DESCRIPTIONS;
                

                $ObjItem[$index]->FromQty =  isset($ObjAltQTY[0]->FROM_QTY)? $ObjAltQTY[0]->FROM_QTY : 0;
                $ObjItem[$index]->ToQty = isset($ObjAltQTY[0]->TO_QTY)? $ObjAltQTY[0]->TO_QTY : 0;

                $ObjItemCheckFlag =  DB::select('SELECT TOP 1  * FROM TBL_MST_ITEMCHECKFLAG WHERE ITEMID_REF = ? ', [$dataRow->ITEMID]);

                $ObjItem[$index]->BATCHNOA =  isset($ObjItemCheckFlag[0]->BATCHNOA)? $ObjItemCheckFlag[0]->BATCHNOA : 0;
                $ObjItem[$index]->SRNOA =  isset($ObjItemCheckFlag[0]->SRNOA)? $ObjItemCheckFlag[0]->SRNOA : 0;

               
               
            } 
            
        } 


        $objCount = count($ObjItem);
        $objItemCount = count($ObjItem);

        $objTemp = $objResponse;
        
        
        $date_arr = explode('-',$cur_date);
        
        $ObjData = DB::select('select * from TBL_MST_FYEAR  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?) 
                                and CYID_REF = ?
                                and  FYSTMONTH>=? and FYSTYEAR<=?
                                and FYENDMONTH<=? and FYENDYEAR=?
                                and STATUS = ?', [$cur_date, $CYID_REF, $date_arr[1], $date_arr[0], $date_arr[1], $date_arr[0],$Status]);
        
     
        if(!empty($ObjData)){
            $min_date = $ObjData[0]->FYSTYEAR.'-'.$ObjData[0]->FYSTMONTH.'-01';
        }else{
            $min_date = $cur_date;
        }
        $max_date = $cur_date;

        $opening_date = $cur_date;

        if(!empty($objResponse)){
            $opening_date=   isset($objResponse[0]->OPENING_BL_DT ) && !is_null($objResponse[0]->OPENING_BL_DT) && $objResponse[0]->OPENING_BL_DT!='' ? $objResponse[0]->OPENING_BL_DT  : $cur_date;  
        }
        

        //dd($ObjItem);

        $AlpsStatus =   $this->AlpsStatus();

        if(!empty($ObjItem)){
            $n=1;  
            $key=$indexno;    
           
            foreach($ObjItem as $row){

                $deactivate_date = '';
                $deactivate_date = '';

                


                $IOBID_STATUS=$row->IOBID_STATUS=='A'?'disabled':'';

                $ValueData  ="value='$row->STORE_DETAILS'}]]";

                echo '<tr class="participantRow">';
                echo '<td hidden><input  class="form-control " type="hidden" name="Row_Count[]" ></td>';
                echo '<td hidden><input  class="form-control " type="text" name="IOBID_REF_'.$key.'"   id ="HDNIOBID_REF_'.$key.'" value="'.$row->IOBID_REF.'" ></td>';
                echo '<td><input '.$disabled.' type="text" name="popupITEMID_'.$key.'" id="popupITEMID_'.$key.'" class="form-control" value="'.$row->ICODE.'"  autocomplete="off"  readonly/></td>';
                echo '<td hidden><input type="text" name="ITEMID_REF_'.$key.'" id="ITEMID_REF_'.$key.'" value="'.$row->ITEMID.'" class="form-control" autocomplete="off" /></td>';
                echo '<td><input '.$disabled.' type="text" name="ItemName_'.$key.'" id="ItemName_'.$key.'" class="form-control" value="'.$row->NAME.'"  autocomplete="off"  readonly/></td>';
                    
           
                echo '<td '.$AlpsStatus['hidden'].' ><input '.$disabled.' type="text" name="Alpspartno_'.$key.'" id="Alpspartno_'.$key.'" value="'.$row->ALPS_PART_NO.'" class="form-control"  autocomplete="off"  readonly/></td>';
                echo '<td '.$AlpsStatus['hidden'].' ><input '.$disabled.' type="text" name="Custpartno_'.$key.'" id="Custpartno_'.$key.'" value="'.$row->CUSTOMER_PART_NO.'" class="form-control"  autocomplete="off"  readonly/></td>';
                echo '<td '.$AlpsStatus['hidden'].' ><input '.$disabled.' type="text" name="OEMpartno_'.$key.'"  id="OEMpartno_'.$key.'" value="'.$row->OEM_PART_NO.'" class="form-control"  autocomplete="off"   readonly/></td>';


                echo '<td><input '.$disabled.' type="text" name="popupMUOM_'.$key.'" id="popupMUOM_'.$key.'" class="form-control" value="'.$row->MainUOMCode.' - '.$row->MainUOMDesc.'"  autocomplete="off"  readonly/></td>';
                echo '<td hidden><input type="text" name="MAINUOMID_REF_'.$key.'" id="MAINUOMID_REF_'.$key.'" value="'.$row->MAIN_UOMID_REF.'" class="form-control"  autocomplete="off" /></td>';
                    
                echo '<td align="center" ><button '.$disabled.' class="btn" id="FORMDTLBTN_'.$key.'" name="FORMDTLBTN_'.$key.'" type="button"><i class="fa fa-clone"></i></button></td>';
                    
                echo '<td><input '.$disabled.' type="text" name="OPEINING_BAL_QTY_'.$key.'" id="OPEINING_BAL_QTY_'.$key.'" value="'.$row->OPENING_BL.'" class="form-control three-digits" maxlength="13"  autocomplete="off"  readonly /></td>';
                echo '<td><input '.$disabled.' type="text" name="OPEINING_RATE_'.$key.'" id="OPEINING_RATE_'.$key.'" value="'.$row->OPENING_RATE.'" class="form-control five-digits" maxlength="13"   autocomplete="off" '.$IOBID_STATUS.' /></td>';
                echo '<td><input '.$disabled.' type="text" name="OPEINING_VALUE_'.$key.'" id="OPEINING_VALUE_'.$key.'" value="'.$row->OPENING_VL.'" class="form-control" maxlength="13"  autocomplete="off"  readonly/></td>';
                echo '<td hidden><input type="text" name="TotalHiddenQty_'.$key.'" id="TotalHiddenQty_'.$key.'" value="'.$row->OPENING_BL.'"  ></td>';
                echo '<td hidden><input type="text" name="HiddenRowId_'.$key.'" id="HiddenRowId_'.$key.'" '.$ValueData.' ></td>';
                echo '<td hidden><input type="text" name="BATCHNOA_'.$key.'" id="BATCHNOA_'.$key.'" value="'.$row->BATCHNOA.'" class="form-control"  autocomplete="off" /></td>';
                echo '<td hidden><input type="text" name="SRNOA_'.$key.'" id="SRNOA_'.$key.'" value="'.$row->SRNOA.'" class="form-control"  autocomplete="off" /></td>';
                echo '<td hidden><input type="text" name="UniqueStoreIds_'.$key.'" id="UniqueStoreIds_'.$key.'" value="'.$row->UNIQ_STORE_IDS.'" class="form-control"  autocomplete="off" /></td>';
                echo '</tr>';
               
                $n++;
                $key++;

            }
        }
        else{
            echo '';
        }
                                 
        exit();

     

    }

      
    public function add(){ 

        //return view('masters.accounts.CostCentre.mstfrm70add');   
    }


   public function save(Request $request){
    

        // $CYID_REF       =   Auth::user()->CYID_REF;
        // $BRID_REF       =   Session::get('BRID_REF');
        // $FYID_REF       =   Session::get('FYID_REF');       
        // $VTID           =   $this->vtid_ref;

        // $data     =array();
        // $existData=array();
        // $r_count = $request['Row_Count'];

        // for ($i=0; $i<=$r_count; $i++){

        //     if((isset($request['COSTCODE_'.$i]) && $request['COSTCODE_'.$i] !="")){
        //         $data[$i] = [
        //             'CCCODE' => strtoupper(trim($request['COSTCODE_'.$i])),
        //             'NAME' => trim($request['DESCRIPTIONS_'.$i]),
        //             'DEACTIVATED' => 0,
        //             'DODEACTIVATED' =>NULL,
        //         ];

        //         $existData[$i]=strtoupper(trim($request['COSTCODE_'.$i]));

        //     }
        // }

        // if(!empty($existData)){
        //     $counts     = array_count_values($existData);
        //     $NumVal     = max($counts);

        //     if( $NumVal > 1){
        //         return Response::json(['errors'=>true,'msg' => 'Duplicate Cost Centre Code.','save'=>'invalid']);
        //     }
        // }

        
        // foreach($data as $index=>$dataRow)
        // {
        //     $checkData =  DB::select("SELECT * FROM TBL_MST_COSTCENTER WHERE CYID_REF = $CYID_REF AND CCCODE=?",[$dataRow['CCCODE']] );
        //     if(!empty($checkData))
        //     {
        //         return Response::json(['errors'=>true,'msg' => $dataRow["CCCODE"].': Already used code in another category.','save'=>'invalid']);
        //     }
        // }
        
        // if(!empty($data)){ 
        //     $wrapped_links["COSTCENTER"] = $data; 
        //     $xml = ArrayToXml::convert($wrapped_links);
        // }
        // else{
        //     $xml = NULL; 
        // }  
       
        // $CCCATID_REF      =  trim($request['CCID_REF']);
      
        // $DEACTIVATED    =   0;  
        // $DODEACTIVATED  =   NULL;  

       
        // $USERID         =   Auth::user()->USERID;
        // $UPDATE         =   Date('Y-m-d');
        // $UPTIME         =   Date('h:i:s.u');
        // $ACTION         =   "ADD";
        // $IPADDRESS      =   $request->getClientIp();


        // $array_data   = [
        //                 $CCCATID_REF, $CYID_REF, $BRID_REF, $FYID_REF, 
        //                 $xml,         $VTID,     $USERID,   $UPDATE,
        //                 $UPTIME,      $ACTION,      $IPADDRESS
        //             ];

      
        // try {

        //     $sp_result = DB::select('EXEC SP_COSTCENTER_IN ?,?,?,?, ?,?,?,?, ?,?,?', $array_data);
        //     //dd($sp_result);
        
        // } catch (\Throwable $th) {
            
        //      return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);

        // }
    
        // if($sp_result[0]->RESULT=="SUCCESS"){

        //     return Response::json(['success' =>true,'msg' => 'Record successfully inserted.']);

        // }elseif($sp_result[0]->RESULT=="DUPLICATE RECORD"){
        
        //     return Response::json(['errors'=>true,'msg' => 'Duplicate record.','exist'=>'duplicate']);
            
        // }else{

        //     return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);
        // }
        
        exit();    
    }



    public function edit($id){

        if(!is_null($id))
        {
        
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
            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
            ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
            ->first(); 

            $objCatData =  DB::select("SELECT top 1 CCCATID_REF FROM TBL_MST_COSTCENTER WHERE CCID=$id");
           
            $objResponse = DB::table('TBL_MST_COSTCENTER')
                ->where('TBL_MST_COSTCENTER.CYID_REF','=',Auth::user()->CYID_REF)
                ->where('TBL_MST_COSTCENTER.CCCATID_REF','=',$objCatData[0]->CCCATID_REF)
                ->leftJoin('TBL_MST_CCCATEGORY',   'TBL_MST_CCCATEGORY.CCCATID','=',   'TBL_MST_COSTCENTER.CCCATID_REF')
                ->select('TBL_MST_COSTCENTER.*', 'TBL_MST_CCCATEGORY.CCCATID','TBL_MST_CCCATEGORY.CCCATCODE','TBL_MST_CCCATEGORY.CCCATNAME')
                ->orderBy('TBL_MST_COSTCENTER.CCID','ASC')
                ->get()->toArray();    
            


            $objCount = count($objResponse);
       

            return view('masters.accounts.CostCentre.mstfrm70edit',compact(['objResponse','user_approval_level','objRights','objCount']));
        }

    }

     
    public function update(Request $request)
    {


       

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID     =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   "EDIT";
        $IPADDRESS  =   $request->getClientIp();

        $newDateString = NULL;
        $newdt = !(is_null($request['DOOB']) ||empty($request['DOOB']) )=="true" ? $request['DOOB'] : NULL; 
        if(!is_null($newdt) ){
            
            $newdt = str_replace( "/", "-",  $newdt ) ;
            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        
        $OPENING_BL_DT = $newDateString;

        $DEACTIVATED = 0;
        $DODEACTIVATED= NULL;

        $OBID = 0;

        $objResponse = DB::select('select top 1 * from TBL_MST_ITEM_OB_HDR  where CYID_REF = ? and BRID_REF = ? and FYID_REF=? and OPENING_BL_DT=?', [$CYID_REF, $BRID_REF, $FYID_REF,  $OPENING_BL_DT]);
        
        
        if(!empty($objResponse)){
            $OBID = $objResponse[0]->IOBID;
        }
         //$ITEMROWID  =  json_decode($request['HiddenRowId_0']); 

        
        // dd($request->all());
        
       
        $storeArr = array();
        $storecnt =0;
        
        $unqStoreIds = '';

        $data      = array();
        $existData = array();
        $r_count   = count($request['Row_Count']);
       
        for ($i=0; $i<=$r_count; $i++){

            if(isset($request['ITEMID_REF_'.$i])){

                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $ITEMROWID  =  isset($request['HiddenRowId_'.$i]) && !is_null(json_decode($request['HiddenRowId_'.$i])) ? json_decode($request['HiddenRowId_'.$i]) : null  ;
                //dump($ITEMROWID);
                if(!is_null($ITEMROWID )&& !empty($ITEMROWID) ){
                    foreach($ITEMROWID as $kye=>$val){
                        $storeArr[$storecnt]= $val;
                        $storecnt++;
                    }
                }
                //dump($ITEMROWID);
                if(isset($request['UniqueStoreIds_'.$i]) && !is_null($request['UniqueStoreIds_'.$i]) && !empty($request['UniqueStoreIds_'.$i]) ){
                    $unstoreid = json_decode($request['UniqueStoreIds_'.$i]);
                    
                    //dump($request['UniqueStoreIds_'.$i]);
                       if(!empty($unstoreid)){
                        $unqStoreIds = implode(",", $unstoreid );
                       } else{
                        $unqStoreIds="";
                       }
                    
                }else{
                    $unqStoreIds="";
                }
              
                $data[$i] = [
                    'ITEMID_REF' => isset($request['ITEMID_REF_'.$i]) && $request['ITEMID_REF_'.$i] !=""?$request['ITEMID_REF_'.$i]:NULL,
                    'UOMID_REF' =>  isset($request['MAINUOMID_REF_'.$i]) && $request['MAINUOMID_REF_'.$i] !=""?$request['MAINUOMID_REF_'.$i]:NULL,
                    'STID_REF' => isset($unqStoreIds) && $unqStoreIds !=""?$unqStoreIds:NULL,  
                    'OPENING_BL' => isset($request['OPEINING_BAL_QTY_'.$i]) && $request['OPEINING_BAL_QTY_'.$i] !=""?$request['OPEINING_BAL_QTY_'.$i]:'0.00' ,  
                    'RATE' =>  isset($request['OPEINING_RATE_'.$i]) && $request['OPEINING_RATE_'.$i] !=""?$request['OPEINING_RATE_'.$i]:'0.00',
                    'OPENING_VL' => isset($request['OPEINING_VALUE_'.$i]) && $request['OPEINING_VALUE_'.$i] !=""?$request['OPEINING_VALUE_'.$i]:'0.00',
                    
                ];

            }
        }


       // dump($request->all());

      

        $storexml_data = array();
        foreach ($storeArr as $key => $value) {
            $storexml_data[$key] = [
                'ITEMID_REF' => trim($value[0]->ITEMID_REF),
                'STID_REF' => trim($value[0]->STID_REF),
                'BATCH_NO' => trim($value[0]->BATCH_NO), 
                'SERIAL_NO' => trim($value[0]->SERIAL_NO),   
                'MUOMID_REF' => trim($value[0]->MUOMID_REF), 
                'MOPENING_QTY' => trim($value[0]->MOPENING_QTY), 
                
            ];
        }

       
        
        if(!empty($data)){ 
            $wrapped_links["MAT"] = $data; 
            $xml = ArrayToXml::convert($wrapped_links);
        }
        else{
            $xml = NULL; 
        }  


        if(!empty($storexml_data)){ 
            $wrapped_links2["STORE"] = $storexml_data; 
            $xmlStore = ArrayToXml::convert($wrapped_links2);
        }
        else{
            $xmlStore = NULL; 
        }  

       
       
        $USERID         =   Auth::user()->USERID;
        $UPDATE         =   Date('Y-m-d');
        $UPTIME         =   Date('h:i:s.u');
        $ACTION         =   "EDIT";
        $IPADDRESS      =   $request->getClientIp();


        $array_data   = [
                        $OBID,          $OPENING_BL_DT, $CYID_REF,
                        $BRID_REF,      $FYID_REF,      $xml,           $xmlStore,
                        $VTID,          $USERID,        $UPDATE,        $UPTIME,
                        $ACTION,        $IPADDRESS
                    ];
        
        //DUMP($request->ALL());    
       //dump($array_data  );
    
      
    //    try {

            $sp_result = DB::select('EXEC SP_IOB_UP ?,?,?,?, ?,?,?,?, ?,?,?,?, ? ', $array_data);
            //dd($sp_result);
        
        //  } catch (\Throwable $th) {
          
        //       return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);

        //  }
    

        if($sp_result[0]->RESULT=="SUCCESS"){  

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);
        
        }elseif($sp_result[0]->RESULT=="NO RECORD FOUND"){
        
            return Response::json(['errors'=>true,'msg' => 'No record found.','exist'=>'norecord']);
            
        }else{

            return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);
        }
        
        exit();            
    } 

    //uploads attachments files
    public function docuploads(Request $request){

    //     $formData = $request->all();

    //     $allow_extnesions = explode(",",config("erpconst.attachments.allow_extensions"));
    //     $allow_size = config("erpconst.attachments.max_size") * 1020 * 1024;

    //     //echo '<br> c='."--".Config("erpconst.attachments.max_size");
        
    //     //get data
    //     $VTID           =   $formData["VTID_REF"]; 
    //     $ATTACH_DOCNO   =   $formData["ATTACH_DOCNO"]; 
    //     $ATTACH_DOCDT   =   $formData["ATTACH_DOCDT"]; 
    //     $CYID_REF       =   Auth::user()->CYID_REF;
    //     $BRID_REF       =   Session::get('BRID_REF');
    //     $FYID_REF       =   Session::get('FYID_REF');       
    //     // @XML	xml
    //     $USERID         =   Auth::user()->USERID;
    //     $UPDATE         =   Date('Y-m-d');
    //     $UPTIME         =   Date('h:i:s.u');
    //     $ACTION         =   "ADD";
    //     $IPADDRESS      =   $request->getClientIp();
        
	// 	$destinationPath = storage_path()."/docs/company".$CYID_REF."/CostCentre";

    //     if ( !is_dir($destinationPath) ) {
    //         mkdir($destinationPath, 0777, true);
    //     }

    //     $uploaded_data = [];
    //     $invlid_files = "";

    //     $duplicate_files="";

    //     foreach($formData["REMARKS"] as $index=>$row_val){

    //             if(isset($formData["FILENAME"][$index])){

    //                 $uploadedFile = $formData["FILENAME"][$index]; 
                    
    //                 //$filenamewithextension  = $formData["FILENAME"][$index]->getClientOriginalName();

    //                 $filenamewithextension  =   $uploadedFile ->getClientOriginalName();
    //                 $filesize               =   $uploadedFile ->getSize();  
    //                 $extension              =   strtolower( $uploadedFile ->getClientOriginalExtension() );

    //                 //$filenametostore        =   $filenamewithextension; 

    //                 $filenametostore        =  $VTID.$ATTACH_DOCNO.$USERID.$CYID_REF.$BRID_REF.$FYID_REF."#_".$filenamewithextension;  

    //                 if ($uploadedFile->isValid()) {

    //                     if(in_array($extension,$allow_extnesions)){
                            
    //                         if($filesize < $allow_size){

    //                             $filename = $destinationPath."/".$filenametostore;

    //                             if (!file_exists($filename)) {

    //                                $uploadedFile->move($destinationPath, $filenametostore);  //upload in dir if not exists
    //                                $uploaded_data[$index]["FILENAME"] =$filenametostore;
    //                                $uploaded_data[$index]["LOCATION"] = $destinationPath."/";
    //                                $uploaded_data[$index]["REMARKS"] = is_null($row_val) ? '' : trim($row_val);

    //                             }else{

    //                                 $duplicate_files = " ". $duplicate_files.$filenamewithextension. " ";
    //                             }
                                

                                
    //                         }else{
                                
    //                             $invlid_files = $invlid_files.$filenamewithextension." (invalid size)  "; 
    //                         } //invalid size
                            
    //                     }else{

    //                         $invlid_files = $invlid_files.$filenamewithextension." (invalid extension)  ";                             
    //                     }// invalid extension
                    
    //                 }else{
                            
    //                     $invlid_files = $invlid_files.$filenamewithextension." (invalid)"; 
    //                 }//invalid

    //             }

    //     }//foreach

      
    //     if(empty($uploaded_data)){
    //         return redirect()->route("master",[70,"attachment",$ATTACH_DOCNO])->with("success","No file uploaded");
    //     }
    //  //  dd($uploaded_data);

    //     $wrapped_links["ATTACHMENT"] = $uploaded_data;     //root node: <ATTACHMENT>
    //     $ATTACHMENTS_XMl = ArrayToXml::convert($wrapped_links);

    //     $attachment_data = [

    //         $VTID, 
    //         $ATTACH_DOCNO, 
    //         $ATTACH_DOCDT,
    //         $CYID_REF,
            
    //         $BRID_REF,
    //         $FYID_REF,
    //         $ATTACHMENTS_XMl,
    //         $USERID,

    //         $UPDATE,
    //         $UPTIME,
    //         $ACTION,
    //         $IPADDRESS
    //     ];
        
       
          
    //    // try {

    //          //save data
    //          $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);

    //        //  dd($sp_result[0]->RESULT);
      
    //   //  } catch (\Throwable $th) {
        
    //     //    return redirect()->route("master",[70,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");
    
    //   //  }
     
    //     if($sp_result[0]->RESULT=="SUCCESS"){

    //         if(trim($duplicate_files!="")){
    //             $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
    //         }

    //         if(trim($invlid_files!="")){
    //             $invlid_files =  " Invalid files -  ".$invlid_files;
    //         }

    //         return redirect()->route("master",[70,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    //     }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
       
    //         return redirect()->route("master",[70,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);
    
    //     }else{

    //         //return redirect()->route("master",[70,"attachment",$ATTACH_DOCNO])->with("error","There is some data error. Please try after sometime.  ");
    //         return redirect()->route("master",[70,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    //     }
      
        
    }   

  
    //singleApprove begin
    public function singleapprove(Request $request)
    {
      
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $VTID     =   $this->vtid_ref;
        $USERID     =   Auth::user()->USERID;
        $UPDATE     =   Date('Y-m-d');
        
        $UPTIME     =   Date('h:i:s.u');
        $ACTION     =   trim($request['user_approval_level']);   // user approval level value
        $IPADDRESS  =   $request->getClientIp();
        

        $newDateString = NULL;
        $newdt = !(is_null($request['DOOB']) ||empty($request['DOOB']) )=="true" ? $request['DOOB'] : NULL; 
        if(!is_null($newdt) ){
            
            $newdt = str_replace( "/", "-",  $newdt ) ;
            $newDateString = Carbon::parse($newdt)->format('Y-m-d');        
        }
        
        $OPENING_BL_DT = $newDateString;

        $DEACTIVATED = 0;
        $DODEACTIVATED= NULL;

        $OBID = 0;

        $objResponse = DB::select('select top 1 * from TBL_MST_ITEM_OB_HDR  where CYID_REF = ? and BRID_REF = ? and FYID_REF=? and OPENING_BL_DT=?', [$CYID_REF, $BRID_REF, $FYID_REF,  $OPENING_BL_DT]);
        
        
        if(!empty($objResponse)){
            $OBID = $objResponse[0]->IOBID;
        }
         //$ITEMROWID  =  json_decode($request['HiddenRowId_0']); 

        
        // dd($request->all());
        
       
        $storeArr = array();
        $storecnt =0;
        
        $unqStoreIds = '';

        $data      = array();
        $existData = array();
        $r_count   = count($request['Row_Count']);

        for ($i=0; $i<=$r_count; $i++){

            if(isset($request['ITEMID_REF_'.$i])){

                $ITEMID_REF =   $request['ITEMID_REF_'.$i];
                $ITEMROWID  =  isset($request['HiddenRowId_'.$i]) && !is_null($request['HiddenRowId_'.$i]) ? json_decode($request['HiddenRowId_'.$i]) : null  ;
                //dump($ITEMROWID);
                if(!is_null($ITEMROWID )&& !empty($ITEMROWID) ){
                    foreach($ITEMROWID as $kye=>$val){
                        $storeArr[$storecnt]= $val;
                        $storecnt++;
                    }
                }
                //dump($ITEMROWID);
                if(isset($request['UniqueStoreIds_'.$i]) && !is_null($request['UniqueStoreIds_'.$i]) && !empty($request['UniqueStoreIds_'.$i]) ){
                    $unstoreid = json_decode($request['UniqueStoreIds_'.$i]);
                    
                   // dump($request['UniqueStoreIds_'.$i]);
                       if(!empty($unstoreid)){
                        $unqStoreIds = implode(",", $unstoreid );
                       } else{
                        $unqStoreIds="";
                       }
                    
                }else{
                    $unqStoreIds="";
                }
              
                $data[$i] = [
                    'ITEMID_REF' => isset($request['ITEMID_REF_'.$i]) && $request['ITEMID_REF_'.$i] !=""?$request['ITEMID_REF_'.$i]:NULL,
                    'UOMID_REF' =>  isset($request['MAINUOMID_REF_'.$i]) && $request['MAINUOMID_REF_'.$i] !=""?$request['MAINUOMID_REF_'.$i]:NULL,
                    'STID_REF' => isset($unqStoreIds) && $unqStoreIds !=""?$unqStoreIds:NULL,  
                    'OPENING_BL' => isset($request['OPEINING_BAL_QTY_'.$i]) && $request['OPEINING_BAL_QTY_'.$i] !=""?$request['OPEINING_BAL_QTY_'.$i]:'0.00' ,  
                    'RATE' =>  isset($request['OPEINING_RATE_'.$i]) && $request['OPEINING_RATE_'.$i] !=""?$request['OPEINING_RATE_'.$i]:'0.00',
                    'OPENING_VL' => isset($request['OPEINING_VALUE_'.$i]) && $request['OPEINING_VALUE_'.$i] !=""?$request['OPEINING_VALUE_'.$i]:'0.00',
                    
                ];

            }
        }


       // dump($request->all());

      // dump($storeArr); 

        $storexml_data = array();
        foreach ($storeArr as $key => $value) {
            $storexml_data[$key] = [
                'ITEMID_REF' => trim($value[0]->ITEMID_REF),
                'STID_REF' => trim($value[0]->STID_REF),
                'BATCH_NO' => trim($value[0]->BATCH_NO), 
                'SERIAL_NO' => trim($value[0]->SERIAL_NO),   
                'MUOMID_REF' => trim($value[0]->MUOMID_REF), 
                'MOPENING_QTY' => trim($value[0]->MOPENING_QTY), 
                
            ];
        }

        
        
        if(!empty($data)){ 
            $wrapped_links["MAT"] = $data; 
            $xml = ArrayToXml::convert($wrapped_links);
        }
        else{
            $xml = NULL; 
        }  

        if(!empty($storexml_data)){ 
            $wrapped_links2["STORE"] = $storexml_data; 
            $xmlStore = ArrayToXml::convert($wrapped_links2);
        }
        else{
            $xmlStore = NULL; 
        }  

       
       
      

        $array_data   = [
                        $OBID,          $OPENING_BL_DT, $CYID_REF,
                        $BRID_REF,      $FYID_REF,      $xml,           $xmlStore,
                        $VTID,          $USERID,        $UPDATE,        $UPTIME,
                        $ACTION,        $IPADDRESS
                    ];
        
        //DUMP($request->ALL());    
      // dump($array_data  );
    //    @ID INT,@OPENING_BL_DT DATE,@CYID_REF INT,@BRID_REF INT,@FYID_REF INT,          
    //    @XMLMAT XML,@STORE XML,@VTID int,@USERID int,@UPDATE date,@UPTIME time,@ACTION varchar(30),@IPADDRESS varchar(30)                 
      
    //    try {

            $sp_result = DB::select('EXEC SP_IOB_UP ?,?,?,?, ?,?,?,?, ?,?,?,?, ? ', $array_data);
           // dd($sp_result);
        
        //  } catch (\Throwable $th) {
          
        //       return Response::json(['errors'=>true,'msg' => 'There is some data error. Please try after sometime.','save'=>'invalid']);

        //  }
                    
        if($sp_result[0]->RESULT=="SUCCESS"){  
 
             return Response::json(['success' =>true,'msg' => 'Record successfully approved.']);
         
         }elseif($sp_result[0]->RESULT=="NO RECORD FOUND"){
         
             return Response::json(['errors'=>true,'msg' => 'No record found.','exist'=>'norecord']);
             
         }else{
 
             return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'save'=>'invalid']);
         }
         
         exit();  

     }  //singleApprove end


    public function view($id){

        // if(!is_null($id))
        // {
        
        //     $USERID     =   Auth::user()->USERID;
        //     $VTID       =   $this->vtid_ref;
        //     $CYID_REF   =   Auth::user()->CYID_REF;
        //     $BRID_REF   =   Session::get('BRID_REF');    
        //     $FYID_REF   =   Session::get('FYID_REF');

        //     $sp_user_approval_req = [
        //         $USERID, $VTID, $CYID_REF, $BRID_REF, $FYID_REF
        //     ];        

        //     //get user approval data
        //     $user_approval_details = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?,?', $sp_user_approval_req);
        //     $user_approval_level = "APPROVAL".$user_approval_details[0]->LAVELS;


        //     $objRights = DB::table('TBL_MST_USERROLMAP')
        //     ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
        //     ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
        //     ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
        //     ->where('TBL_MST_USERROLMAP.FYID_REF','=',Session::get('FYID_REF'))
        //     ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
        //     ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
        //     ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
        //     ->first(); 

        //     $objCatData =  DB::select("SELECT top 1 CCCATID_REF FROM TBL_MST_COSTCENTER WHERE CCID=$id");
        //     $objResponse = DB::table('TBL_MST_COSTCENTER')
        //         ->where('TBL_MST_COSTCENTER.CYID_REF','=',Auth::user()->CYID_REF)
        //         ->where('TBL_MST_COSTCENTER.CCCATID_REF','=',$objCatData[0]->CCCATID_REF)
        //         ->leftJoin('TBL_MST_CCCATEGORY',   'TBL_MST_CCCATEGORY.CCCATID','=',   'TBL_MST_COSTCENTER.CCCATID_REF')
        //         ->select('TBL_MST_COSTCENTER.*', 'TBL_MST_CCCATEGORY.CCCATID','TBL_MST_CCCATEGORY.CCCATCODE','TBL_MST_CCCATEGORY.CCCATNAME')
        //         ->orderBy('TBL_MST_COSTCENTER.CCID','ASC')
        //         ->get()->toArray();    
        //    // dd($objResponse);    


        //     $objCount = count($objResponse);

        //     return view('masters.accounts.CostCentre.mstfrm70view',compact(['objResponse','user_approval_level','objRights','objCount']));
        // }

    }
  
    public function printdata(Request $request){
        //
        // $ids_data = [];
        // if(isset($request->records_ids)){
            
        //     $ids_data = explode(",",$request->records_ids);
        // }

        // $objResponse = TblMstFrm70::whereIn('ITEMGID',$ids_data)->get();
        
        // return view('masters.accounts.CostCentre.mstfrm70print',compact(['objResponse']));
   }//print


   
    

    
    //display attachments form
    public function attachment($id){

        // if(!is_null($id))
        // {
        //     //EXEC [SP_APPROVAL_LAVEL] 2,114,6,4,2
        //     $objResponse = TblMstFrm70::where('CCID','=',$id)->first();

        //     //select * from TBL_MST_VOUCHERTYPE where VTID=114

        //     $objMstVoucherType = DB::table("TBL_MST_VOUCHERTYPE")
        //             ->where('VTID','=',$this->vtid_ref)
        //              ->select('VTID','VCODE','DESCRIPTIONS')
        //             ->get()
        //             ->toArray();

            
        //             //uplaoded docs
        //             $objAttachments = DB::table('TBL_MST_ATTACHMENT')                    
        //                 ->where('TBL_MST_ATTACHMENT.VTID_REF','=',$this->vtid_ref)
        //                 ->where('TBL_MST_ATTACHMENT.ATTACH_DOCNO','=',$id)
        //                 ->where('TBL_MST_ATTACHMENT.CYID_REF','=',Auth::user()->CYID_REF)
        //                 ->where('TBL_MST_ATTACHMENT.BRID_REF','=',Session::get('BRID_REF'))
        //                 ->where('TBL_MST_ATTACHMENT.FYID_REF','=',Session::get('FYID_REF'))
        //                 ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
        //                 ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
        //                 ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
        //                 ->get()->toArray();

        //          // dump( $objAttachments);

        //     return view('masters.accounts.CostCentre.mstfrm70attachment',compact(['objResponse','objMstVoucherType','objAttachments']));
        // }

    }
    
    public function MultiApprove(Request $request){

        // $USERID_REF =   Auth::user()->USERID;
        // $VTID_REF   =   $this->vtid_ref;  //voucher type id
        // $CYID_REF   =   Auth::user()->CYID_REF;
        // $BRID_REF   =   Session::get('BRID_REF');
        // $FYID_REF   =   Session::get('FYID_REF');   

        // $sp_Approvallevel = [
        //     $USERID_REF, $VTID_REF, $CYID_REF,$BRID_REF,
        //     $FYID_REF
        // ];
        
        // $sp_listing_result = DB::select('EXEC SP_APPROVAL_LAVEL ?,?,?,?, ?', $sp_Approvallevel);

        //     if(!empty($sp_listing_result))
        //     {
        //         foreach ($sp_listing_result as $key=>$valueitem)
        //         {  
        //             $record_status = 0;
        //             $Approvallevel = "APPROVAL".$valueitem->LAVELS;
        //         }
        //     }
        

            
        //     $req_data =  json_decode($request['ID']);
        //     $m_array = [];
          
        //     foreach($req_data  as $index=>$row)
        //     {
        //         if (!in_array($row->ID, $m_array)){
        //             $m_array[$index] = $row->ID;                  
        //         }
        //     }
            
        //     $recordIds = implode(',', $m_array);
        //     $ObjData2 = DB::select("SELECT distinct CCCATID_REF  FROM TBL_MST_COSTCENTER where CCID in($recordIds)");

        //     $iddata = [];
        //     foreach($ObjData2 as $cindex=>$crow)
        //     {
        //         $iddata['APPROVAL'][]['ID'] =  $crow->CCCATID_REF;
        //     }

        //     $xml = ArrayToXml::convert($iddata);

        //     $USERID_REF =   Auth::user()->USERID;
        //     $VTID_REF   =   $this->vtid_ref;  //voucher type id
        //     $CYID_REF   =   Auth::user()->CYID_REF;
        //     $BRID_REF   =   Session::get('BRID_REF');
        //     $FYID_REF   =   Session::get('FYID_REF');       
        //     $TABLE      =   "TBL_MST_COSTCENTER";
        //     $FIELD      =   "CCCATID_REF";
        //     $ACTIONNAME     = $Approvallevel;
        //     $UPDATE     =   Date('Y-m-d');
        //     $UPTIME     =   Date('h:i:s.u');
        //     $IPADDRESS  =   $request->getClientIp();
            
        //     $log_data = [ 
        //         $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
        //     ];

        //     $sp_result = DB::select('EXEC SP_MST_MULTIAPPROVAL_CC ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
     
        //     if($sp_result[0]->RESULT=="All records approved"){

        //         return Response::json(['approve' =>true,'msg' => 'Records successfully Approved.']);

        //     }elseif($sp_result[0]->RESULT=="NO RECORD FOR APPROVAL"){
            
        //         return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','exist'=>'norecord']);
            
        //     }else{
        //         return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','exist'=>'Some Error']);
        //     }
        
            exit();    
        }


        //Cancel the data
   public function cancel(Request $request){

        // $id = $request->{0};
        
        // $objCatData =  DB::select("SELECT top 1 CCCATID_REF FROM TBL_MST_COSTCENTER WHERE CCID=$id");        
        // $cancel_id = $objCatData[0]->CCCATID_REF;
        
        // $USERID_REF =   Auth::user()->USERID;
        // $VTID_REF   =   $this->vtid_ref;  //voucher type id
        // $CYID_REF   =   Auth::user()->CYID_REF;
        // $BRID_REF   =   Session::get('BRID_REF');
        // $FYID_REF   =   Session::get('FYID_REF');       
        // $TABLE      =   "TBL_MST_COSTCENTER";
        // $FIELD      =   "CCCATID_REF";
        // $ID         =   $cancel_id;
        // $UPDATE     =   Date('Y-m-d');
        // $UPTIME     =   Date('h:i:s.u');
        // $IPADDRESS  =   $request->getClientIp();
        
        // $cancelData[0]= ['NT' =>'TBL_MST_COSTCENTER'];

        // $cancel_links["TABLES"] = $cancelData;
        // $cancelxml = ArrayToXml::convert($cancel_links);

        // $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$cancelxml];

        // $sp_result = DB::select('EXEC SP_MST_CANCEL_CC  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);
 
       
        // if($sp_result[0]->RESULT=="CANCELED"){  
        //   //  echo 'in cancel';
        //   return Response::json(['cancel' =>true,'msg' => 'Record successfully canceled.']);
        
        // }elseif($sp_result[0]->RESULT=="NO RECORD FOR CANCEL"){
        
        //     //echo "NO RECORD FOR CANCEL";
        //     return Response::json(['errors'=>true,'msg' => 'No record found.','norecord'=>'norecord']);
            
        // }else{
        //     //echo "--else--";
        //        return Response::json(['errors'=>true,'msg' => 'Error:'.$sp_result[0]->RESULT,'invalid'=>'invalid']);
        // }
        
        exit(); 
}


public function getforms(Request $request){

    // DUMP($request->ALL());
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       

        $IOBID_REF = $request["iobid_ref"];
        $ITEMID_REF = $request["itemid_ref"];
        $MAINUOMID_REF = $request["mainuomid_ref"];
        $BATCHNO = trim($request["batchnoa"]);
        $SERIALNO = trim($request["serialnoa"]);

        $STOREDTL = trim($request["storedtls"]);

        $HIDEBNO = ($BATCHNO==0) ? "hidden" : "";
        $HIDESRNONO = ($SERIALNO==0) ? "hidden" : "";

        $newArray = array();
        $actiontype = "";
        $STOREDTL2 = json_decode($STOREDTL);
        if(isset($STOREDTL2) && !empty($STOREDTL2)){
            $newArray = call_user_func_array('array_merge', $STOREDTL2);  //shift array one level up
        }   


        if(isset($newArray) && !empty($newArray) ){
            if(!empty($newArray) && $newArray[0]->actiontype=="user"){

                $actiontype =  $newArray[0]->actiontype;
            }
        }
   

        $objRes2 = DB::select('select top 1 * from TBL_MST_ITEM_OB_HDR  where CYID_REF = ? and BRID_REF = ? and FYID_REF=? and IOBID=?', [$CYID_REF, $BRID_REF, $FYID_REF, $IOBID_REF]);
        
        $DisableEntry = '';
        $StatusMode = 'N';
        if(!empty($objRes2) && $objRes2[0]->STATUS=='A'){
            $DisableEntry = 'disabled';
            $StatusMode = 'A';
        }

    $row1 = '<tr>
                <td >Store '.$actiontype.'</td>
                <td '.$HIDEBNO.'>Batch / Lot No</td>
                <td '.$HIDESRNONO.'>Serial No</td>
                <td>Main UoM (MU)</td>
                <td>Opening Qty (MU)</td>
                <td>Action</td>
            </tr>';

    if(strtolower($actiontype)=="user")
    {
        //dd($newArray);   
        echo  $row1;
        foreach ($newArray as $dindex2=>$dataRow2){      
                    
           // $ObjStore2 = DB::select('SELECT TOP 1 * FROM TBL_MST_STORE WHERE STID = ?',[$dataRow2->STID_REF]);
           // $ObjMUOM2 = DB::select('SELECT TOP 1 * FROM TBL_MST_UOM WHERE  UOMID = ?',[$dataRow2->MUOMID_REF]);

           $row = '';
            $row = $row.' <tr class="clsFORMid participantRow9">';
            $row = $row.'<td hidden><input type="text" name= "strStatus_'.$dindex2.'" id= "strstrStatus_'.$dindex2.'" class="form-control" value="'.$StatusMode.'" /></td>';
            $row = $row.'<td hidden><input type="text" name= "strITEMID_REF_'.$dindex2.'" id= "strITEMID_REF_'.$dindex2.'" class="form-control" value="'.$dataRow2->ITEMID_REF.'" /></td>';
            $row = $row.'<td><input type="text" name= "txtSO_popup_'.$dindex2.'" id= "txtSO_popup_'.$dindex2.'" style="100%;" class="form-control" value="'.$dataRow2->txtSO_popup.'" readonly '.$DisableEntry.'/></td>';
            $row = $row.'<td hidden><input type="text" name= "STID_REF_'.$dindex2.'" id= "STID_REF_'.$dindex2.'" class="form-control" value="'.$dataRow2->STID_REF.'"  /></td>';
            $row = $row.'<td hidden><input type="text" name= "BATCHNOismandatory_'.$dindex2.'" id= "BATCHNOismandatory_'.$dindex2.'"   maxlength="20" class="form-control" value="'.$BATCHNO.'" /></td>';
            $row = $row.'<td '.$HIDEBNO.'><input type="text" name= "strBATCH_NO_'.$dindex2.'" id= "strBATCH_NO_'.$dindex2.'"  BATCHNOismandatory="'.$BATCHNO.'"  maxlength="20" class="form-control" value="'.$dataRow2->BATCH_NO.'" /></td>';
            $row = $row.'<td hidden><input type="text" name= "SERIALNOismandatory_'.$dindex2.'" id= "SERIALNOismandatory_'.$dindex2.'"   maxlength="20" class="form-control" value="'.$SERIALNO.'" /></td>';
            $row = $row.'<td '.$HIDESRNONO.'><input type="text" name= "strSERIAL_NO_'.$dindex2.'" id= "strSERIAL_NO_'.$dindex2.'"  autocomplete="off" SERIALNOismandatory="'.$SERIALNO.'"  maxlength="20" class="form-control" value="'.$dataRow2->SERIAL_NO.'" /></td>';
            $row = $row.'<td hidden><input type="text" name= "MUOM_REF_'.$dindex2.'" id= "MUOM_REF_'.$dindex2.'" class="form-control" value="'.$dataRow2->MUOMID_REF.'" /></td>';
            $row = $row.'<td ><input type="text" name= "strMUOMID_REF_'.$dindex2.'" id= "strMUOMID_REF_'.$dindex2.'" class="form-control" value="'.$dataRow2->strMUOMID_REF.'"  readonly/></td>';
            $row = $row.'<td ><input type="text" name= "strMOPENING_QTY_'.$dindex2.'" id= "strMOPENING_QTY_'.$dindex2.'"  autocomplete="off"   class="form-control three-digits" maxlength="13" value="'.$dataRow2->MOPENING_QTY.'" /></td>';
            $row = $row.'<td align="center" width="100px"><button class="btn add" title="add" data-toggle="tooltip" style="width:30px" type="button" '.$DisableEntry.'><i class="fa fa-plus"></i></button>&nbsp;&nbsp;
            <button class="btn remove" id="delbtn" title="Delete" data-toggle="tooltip" type="button" style="width:30px" '.$DisableEntry.'><i class="fa fa-trash" ></i></button></td>';
            echo $row.'</tr>';


        }



    }//user end
    else
    {
        $ObjData = DB::select('SELECT * FROM TBL_MST_ITEM_OB_STORE  where IOBID_REF=? AND ITEMID_REF=? AND MUOMID_REF=?',[$IOBID_REF,$ITEMID_REF,$MAINUOMID_REF]);
       // dd($ObjData);       
        if(!empty($ObjData)){

                echo  $row1;
                foreach ($ObjData as $dindex=>$dataRow){      
                    $row = '';
                    
                    $ObjStore = DB::select('SELECT TOP 1 * FROM TBL_MST_STORE WHERE STID = ?',[$dataRow->STID_REF]);
                    $ObjMUOM = DB::select('SELECT TOP 1 * FROM TBL_MST_UOM WHERE  UOMID = ?',[$dataRow->MUOMID_REF]);

                    $row = $row.' <tr class="clsFORMid participantRow9">';
                    $row = $row.'<td hidden><input type="text" name="strStatus_'.$dindex.'" id= "strStatus_'.$dindex.'" class="form-control" value="'.$StatusMode.'" /></td>';
                    $row = $row.'<td hidden><input type="text" name="strITEMID_REF_'.$dindex.'" id= "strITEMID_REF_'.$dindex.'" class="form-control" value="'.$dataRow->ITEMID_REF.'" /></td>';
                    $row = $row.'<td><input type="text" name="txtSO_popup_'.$dindex.'" id= "txtSO_popup_'.$dindex.'" style="100%;" class="form-control" value="'.$ObjStore[0]->STCODE.'-'.$ObjStore[0]->NAME.'" readonly '.$DisableEntry.'/></td>';
                    $row = $row.'<td hidden><input type="text" name="STID_REF_'.$dindex.'" id= "STID_REF_'.$dindex.'" class="form-control" value="'.$dataRow->STID_REF.'" readonly /></td>';
                    $row = $row.'<td hidden><input type="text" name="BATCHNOismandatory_'.$dindex.'" id= "BATCHNOismandatory_'.$dindex.'"   maxlength="20" class="form-control" value="'.$BATCHNO.'" /></td>';
                    $row = $row.'<td '.$HIDEBNO.'><input type="text" name= "strBATCH_NO_'.$dindex.'" id= "strBATCH_NO_'.$dindex.'"  BATCHNOismandatory="'.$BATCHNO.'"  maxlength="20" class="form-control" value="'.$dataRow->BATCH_NO.'" '.$DisableEntry.'/></td>';
                    $row = $row.'<td hidden><input type="text" name= "SERIALNOismandatory_'.$dindex.'" id= "SERIALNOismandatory_'.$dindex.'"   maxlength="20" class="form-control" value="'.$SERIALNO.'" /></td>';
                    $row = $row.'<td '.$HIDESRNONO.'><input type="text" name= "strSERIAL_NO_'.$dindex.'" id= "strSERIAL_NO_'.$dindex.'"  autocomplete="off" SERIALNOismandatory="'.$SERIALNO.'"  maxlength="20" class="form-control" value="'.$dataRow->SERIAL_NO.'" '.$DisableEntry.' /></td>';
                    $row = $row.'<td hidden><input type="text" name= "MUOM_REF_'.$dindex.'" id= "MUOM_REF_'.$dindex.'" class="form-control" value="'.$dataRow->MUOMID_REF.'" /></td>';
                    $row = $row.'<td ><input type="text" name= "strMUOMID_REF_'.$dindex.'" id= "strMUOMID_REF_'.$dindex.'" class="form-control" value="'.$ObjMUOM[0]->UOMCODE.'-'.$ObjMUOM[0]->DESCRIPTIONS.'"  readonly/></td>';
                    $row = $row.'<td ><input type="text" name= "strMOPENING_QTY_'.$dindex.'" id= "strMOPENING_QTY_'.$dindex.'"  autocomplete="off" class="form-control three-digits" maxlength="13" value="'.$dataRow->MOPENING_QTY.'" '.$DisableEntry.' /></td>';
                    $row = $row.'<td align="center" width="100px"><button class="btn add" title="add" data-toggle="tooltip" style="width:30px" type="button" '.$DisableEntry.'><i class="fa fa-plus"></i></button>&nbsp;&nbsp;
                    <button class="btn remove" id="delbtn" title="Delete" data-toggle="tooltip" type="button" style="width:30px" '.$DisableEntry.'><i class="fa fa-trash" ></i></button></td>';
                    // $row = $row.'<tr id="_'.$dataRow->FORMID .'"  class="clsFORMid">
                    // <td width="50%">'.$dataRow->FORMCODE;
                    // $row = $row.'<input type="hidden" id="txtFORMcode_'.$dataRow->FORMID.'" data-desc="'.$dataRow->FORMCODE.'"  data-descdate="'.$dataRow->FORMNAME.'"
                    // value="'.$dataRow->FORMID.'"/></td><td>'.$dataRow->FORMNAME.'</td></tr>';
                    echo $row.'</tr>';
                    
                }
                
               
            }else{

                $ObjMUOM = DB::select('SELECT TOP 1 * FROM TBL_MST_UOM WHERE  UOMID = ?',[$MAINUOMID_REF]);
                
                $dindex = 0;

                $row = '';
                $row = $row.'<tr class="clsFORMid participantRow9">';
                $row = $row.'<td hidden><input type="text" name="strStatus_'.$dindex.'" id= "strStatus_'.$dindex.'" class="form-control" value="'.$StatusMode.'" /></td>';
                $row = $row.'<td hidden><input type="text" name= "strITEMID_REF_'.$dindex.'" id= "strITEMID_REF_'.$dindex.'" class="form-control" value="'.$ITEMID_REF.'" /></td>';
                $row = $row.'<td><input type="text" name= "txtSO_popup_'.$dindex.'" id= "txtSO_popup_'.$dindex.'" class="form-control" value="" style="width:100%;" readonly '.$DisableEntry.' /></td>';
                $row = $row.'<td hidden><input type="text" name= "STID_REF_'.$dindex.'" id= "STID_REF_'.$dindex.'" class="form-control" value="" readonly /></td>';
                $row = $row.'<td hidden><input type="text" name= "BATCHNOismandatory_'.$dindex.'" id= "BATCHNOismandatory_'.$dindex.'"   maxlength="20" class="form-control" value="'.$BATCHNO.'" /></td>';
                $row = $row.'<td '.$HIDEBNO.'><input type="text" name= "strBATCH_NO_'.$dindex.'" id= "strBATCH_NO_'.$dindex.'" maxlength="20" class="form-control" value="" /></td>';
                $row = $row.'<td hidden><input type="text" name= "SERIALNOismandatory_'.$dindex.'" id= "SERIALNOismandatory_'.$dindex.'"   maxlength="20" class="form-control" value="'.$SERIALNO.'" /></td>';
                $row = $row.'<td '.$HIDESRNONO.'><input type="text" name= "strSERIAL_NO_'.$dindex.'" id= "strSERIAL_NO_'.$dindex.'"  autocomplete="off" maxlength="20" class="form-control" value="" /></td>';
                $row = $row.'<td hidden><input type="text" name= "MUOM_REF_'.$dindex.'" id= "MUOM_REF_'.$dindex.'" class="form-control" value="'.$MAINUOMID_REF.'" /></td>';
                $row = $row.'<td ><input type="text" name= "strMUOMID_REF_'.$dindex.'" id= "strMUOMID_REF_'.$dindex.'" class="form-control" value="'.$ObjMUOM[0]->UOMCODE.'-'.$ObjMUOM[0]->DESCRIPTIONS.'" readonly/></td>';
                $row = $row.'<td ><input type="text" name= "strMOPENING_QTY_'.$dindex.'" id= "strMOPENING_QTY_'.$dindex.'"  autocomplete="off" class="form-control three-digits" maxlength="13"  value="" '.$DisableEntry.'/></td>';
                $row = $row.'<td align="center" width="100px"><button class="btn add" title="add" data-toggle="tooltip" type="button" style="width:30px" '.$DisableEntry.'><i class="fa fa-plus"></i></button>&nbsp;&nbsp;
                <button class="btn remove" id="delbtn" title="Delete" data-toggle="tooltip" type="button" style="width:30px" '.$DisableEntry.'><i class="fa fa-trash" ></i></button></td>';
                $row = $row.'</tr>';

                echo  $row1.$row;
            }
    }


        


            exit();
    }

    public function getformsCount(Request $request){

    // DUMP($request->ALL());
        $IOBID_REF = $request["iobid_ref"];
        $ITEMID_REF = $request["itemid_ref"];
        $MAINUOMID_REF = $request["mainuomid_ref"];

        $STOREDTL = trim($request["storedtls"]);
        $newArray = array();
        $actiontype = "";
        $STOREDTL2 = json_decode($STOREDTL);
        if(isset($STOREDTL2) && !empty($STOREDTL2)){
            $newArray = call_user_func_array('array_merge', $STOREDTL2);  //shift array one level up
        }   


        if(isset($newArray) && !empty($newArray) ){
            if(!empty($newArray) && $newArray[0]->actiontype=="user"){

                $actiontype =  $newArray[0]->actiontype;
            }
        }

        if(strtolower($actiontype)=="user")
        {
            $ObjDataCount2 = count($newArray);
            if($ObjDataCount2==0){
                echo '1';
            }else{
                echo($ObjDataCount2);
            }
        }
        else
        {
            
            $ObjData = DB::select('SELECT * FROM TBL_MST_ITEM_OB_STORE  where IOBID_REF=? AND ITEMID_REF=? AND MUOMID_REF=?',[$IOBID_REF,$ITEMID_REF,$MAINUOMID_REF]);
            $ObjDataCount = count($ObjData);
            if($ObjDataCount==0){
                echo '1';
            }else{
                echo($ObjDataCount);
            }

        }
   

        exit();        
    }

    public function getstores(Request $request){
        $Status = "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');    
        $id = $request['id'];
        $cur_date = Date('Y-m-d');
        $fieldid    = $request['fieldid'];

        $ObjData = DB::select('select * from TBL_MST_STORE  where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?) 
                            and CYID_REF = ? and BRID_REF=? and STATUS = ?', [$cur_date, $CYID_REF, $BRID_REF, $Status]);
    
            if(!empty($ObjData)){
    
            foreach ($ObjData as $index=>$dataRow){
            
                $row = '';
                $row = $row.'<tr >
                <td class="ROW1"> <input type="checkbox" name="SELECT[]" id="secode_'.$dataRow->STID .'"  class="clssoid" value="'.$dataRow->STID.'" ></td>
                <td class="ROW2">'.$dataRow->STCODE;
                $row = $row.'<input type="hidden" id="txtsecode_'.$dataRow->STID.'" data-desc="'.$dataRow->STCODE.'-'.$dataRow->NAME.'" value="'.$dataRow->STID.'"/></td>
                <td class="ROW3">'.$dataRow->NAME.'</td></tr>';
    
                echo $row;
            }
    
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
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


} //class
