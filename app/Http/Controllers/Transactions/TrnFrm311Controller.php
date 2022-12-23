<?php

namespace App\Http\Controllers\Transactions;
use App\Helpers\Helper;
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

class TrnFrm311Controller extends Controller
{
    protected $form_id = 311;
    
    protected $vtid_ref   = 399;  //voucher type id
    // //validation messages
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){  
       // phpinfo();  
        $objRights = DB::table('TBL_MST_USERROLMAP')
                        ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                        ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))                        
                        ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                        ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                        ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                        ->first();

        $FormId         =   $this->form_id;     
        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');     


        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList	=	DB::select("select hdr.PSEID,hdr.PSENO,hdr.PSEDT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.PSEID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                            ) AS CREATED_BY,
                            hdr.STATUS,
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
                            from TBL_TRN_AUDITTRAIL a 
                            inner join TBL_TRN_PSE_HDR hdr
                            on a.VID = hdr.PSEID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.PSEID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );
                           
        
        return view('transactions.inventory.PhysicalStockEntry.trnfrm311',compact(['REQUEST_DATA','objRights','FormId','objDataList']));        
    }

    public function add(){       

            $Status = "A";
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $objItemGroup = DB::table('TBL_MST_ITEMGROUP')
                        ->where('STATUS','=',$Status)
                        ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        ->where('BRID_REF','=',Auth::user()->BRID_REF)
                        ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
                        ->select('TBL_MST_ITEMGROUP.*')
                        ->get();
            //  dd($objItemCategory);      


        $objStore = DB::table('TBL_MST_STORE')
                    ->where('STATUS','=',$Status)
                    ->where('CYID_REF','=',Auth::user()->CYID_REF)
                    ->where('BRID_REF','=',Auth::user()->BRID_REF)
                    ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
                    ->select('TBL_MST_STORE.*')
                    ->get();
                

        $objlastPSEDT  = DB::select('SELECT MAX(PSEDT) PSEDT FROM TBL_TRN_PSE_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?  AND FYID_REF = ? AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF, $FYID_REF, 311, 'A' ]);

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_PSE_HDR',
            'HDR_ID'=>'PSEID',
            'HDR_DOC_NO'=>'PSENO',
            'HDR_DOC_DT'=>'PSEDT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

          


       // dd($objPSENOS); 

       $AlpsStatus =   $this->AlpsStatus();
       $FormId = $this->form_id;

       $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        
    return view('transactions.inventory.PhysicalStockEntry.trnfrm311add', compact(['objlastPSEDT','objItemGroup','objStore','AlpsStatus','FormId','TabSetting','doc_req','docarray']));       
   }


    

   //display attachments form
   public function attachment($id){

    if(!is_null($id))
    {
        $objstore = DB::table("TBL_TRN_PSE_HDR")
                        ->where('PSEID','=',$id)
                        ->select('TBL_TRN_PSE_HDR.*')
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
                        ->where('TBL_MST_ATTACHMENT.FYID_REF','=',Session::get('FYID_REF'))
                        ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                        ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                        ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                        ->get()->toArray();

                 // dump( $objAttachments);

            return view('transactions.inventory.PhysicalStockEntry.trnfrm311attachment',compact(['objstore','objMstVoucherType','objAttachments']));
    }

}






   
   public function get_items(Request $request) {    
 
     $Status = 'A';
     $CYID_REF = Auth::user()->CYID_REF;
     $BRID_REF = Session::get('BRID_REF');
     $FYID_REF = Session::get('FYID_REF');
     $STID = $request['STID'];
     $ITEMGID = $request['ITEMGID'];
  
 
     $AlpsStatus =   $this->AlpsStatus();
 
     $sp_popup = [
         $CYID_REF, $BRID_REF, $FYID_REF,$ITEMGID,$STID
     ]; 
 
         $objitem = DB::select('EXEC sp_get_items_popup_enquiry3 ?,?,?,?,?', $sp_popup);
 
 
         $alps=$this->AlpsStatus();
     if(!empty($objitem)){        
         foreach ($objitem as $index=>$dataRow){
 

            $stock=$this->StockInHand($dataRow->ITEMID); 

            $row = '';
            $row = $row.'<tr ><td style="text-align:center; width:10%">';
            $row = $row.'<input type="checkbox"  id="mainitemidcode_substitute_'.$dataRow->ITEMID.'" class="item_click" 
            value="'.$dataRow->ITEMID.'"/>             
            </td>           
            <td style="width:15%;">'.$dataRow->ICODE;
            $row = $row.'<input type="hidden" id="txtmainitemidcode_substitute_'.$dataRow->ITEMID.'" data-code="'.$dataRow->ICODE.'"   data-name="'.$dataRow->NAME.'"  data-uom="'.$dataRow->Main_UOM.'" data-uomno="'.$dataRow->MAIN_UOMID_REF.'" data-alps_partno="'.$dataRow->ALPS_PART_NO.'"     data-cutomer_partno="'.$dataRow->CUSTOMER_PART_NO.'"  data-oem_partno="'.$dataRow->OEM_PART_NO.'"  data-stockinhand="'.$stock.'"
            value="'.$dataRow->ITEMID.'"/></td>

            <td style="width:15%;">'.$dataRow->NAME.'</td>
            <td style="width:15%;">'.$dataRow->Main_UOM.' </td>
            <td '.$alps['hidden'].' style="width:15%;">'.$dataRow->ALPS_PART_NO.'</td>
            <td '.$alps['hidden'].' style="width:15%;">'.$dataRow->CUSTOMER_PART_NO.'</td>
            <td '.$alps['hidden'].' style="width:15%;">'.$dataRow->OEM_PART_NO.'</td>

           </tr>';
            echo $row;
         }
 
         }else{
             echo '<tr><td colspan="2">Record not found.</td></tr>';
         }
 
         exit();
 
 
 
    }


   

   public function save(Request $request) {  
        $r_count1 = $request['Row_Count1'];

     
     
        for ($i=0; $i<=$r_count1; $i++)
        {
                if(isset($request['MainItemId1_Ref_'.$i]) && !is_null($request['MainItemId1_Ref_'.$i]))
                {
                   
            $reqdata2[$i] = [                          
                'ITEMID_REF'             => $request['MainItemId1_Ref_'.$i],
                'UOMID_REF'              => (!empty($request['UOM_REF_'.$i]) ? $request['UOM_REF_'.$i] : 0),
                'STOCK_IN_HAND_QTY'      => (!empty($request['STOCK_IN_HAND_'.$i]) ? $request['STOCK_IN_HAND_'.$i] : 0),
                'STOCK_IN_HAND_VALUE'    =>   0,
                'PHYSICAL_STOCK_QTY'     =>   (!empty($request['PHYSICAL_'.$i]) ? $request['PHYSICAL_'.$i] : 0),
                'PHYSICAL_STOCK_VALUE'   =>   0,
                'REASON_DISCREPANCY'     =>   $request['REASON_'.$i],                          
            ];
                    
                }
            
        }

           if(isset($reqdata2))
           { 
            $wrapped_links2["MAT"] = $reqdata2;
            $XMLMAT = ArrayToXml::convert($wrapped_links2);
           }
           else
           {
            $XMLMAT = NULL; 
           }   

      
        
            $VTID_REF     =   $this->vtid_ref;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS  = $request->getClientIp();
            $CYID_REF   = Auth::user()->CYID_REF;
            $BRID_REF   = Session::get('BRID_REF');
            $FYID_REF   = Session::get('FYID_REF');
            $PSENO  = strtoupper($request['DOCNO']);
            $PSEDT  = $request['PSEDT'];
            $TIME  = $request['TIME'];
            $TAKING_BY      = $request['TAKING_BY'];
            $FROM_STID_REF = $request['FROMSTOREID_REF'];
            $ITEMGID_REF   = $request['ITEMGID_REF'];           
            $ICID_REF   =   NULL;  

            $log_data = [ 
                $PSENO,$PSEDT,$TIME,$FROM_STID_REF,$ICID_REF,$TAKING_BY,$ITEMGID_REF,$CYID_REF, $BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];
        // dd($log_data); 
    
            $sp_result = DB::select('EXEC SP_PSE_IN ?,?,?,? ,?,?,?,? ,?,?,?,?, ?,?,?,?,?', $log_data);    

            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            exit();  
     }


 


    public function edit($id=NULL){
       
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
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

            $objPSE = DB::table('TBL_TRN_PSE_HDR')
                ->where('TBL_TRN_PSE_HDR.FYID_REF','=',Session::get('FYID_REF'))
                ->where('TBL_TRN_PSE_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                ->where('TBL_TRN_PSE_HDR.BRID_REF','=',Session::get('BRID_REF'))                         
                ->where('TBL_TRN_PSE_HDR.PSEID','=',$id)
                ->leftJoin('TBL_MST_STORE', 'TBL_TRN_PSE_HDR.STID_REF','=','TBL_MST_STORE.STID')    
                ->leftJoin('TBL_MST_ITEMGROUP', 'TBL_TRN_PSE_HDR.ITEMGID_REF','=','TBL_MST_ITEMGROUP.ITEMGID')    
                ->select('TBL_TRN_PSE_HDR.*','TBL_MST_STORE.STCODE','TBL_MST_STORE.NAME','TBL_MST_ITEMGROUP.GROUPCODE','TBL_MST_ITEMGROUP.GROUPNAME')
                ->first();

              // dd($objPSE);   
              
              
              if(isset($objPSE->PSETIME)){
                $TIME_DATA=explode(':',$objPSE->PSETIME);
                $TIME=$TIME_DATA[0].':'.$TIME_DATA[1];
                }else{
                $TIME='';
                }            

    $objPSEMAT = DB::table('TBL_TRN_PSE_MAT')                    
                ->where('TBL_TRN_PSE_MAT.PSEID_REF','=',$id)
                ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_PSE_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID') 
                ->leftJoin('TBL_MST_UOM', 'TBL_TRN_PSE_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                                        
                ->orderBy('TBL_TRN_PSE_MAT.PSE_MATID','ASC')
                ->select('TBL_TRN_PSE_MAT.*','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','TBL_MST_ITEM.ALPS_PART_NO','TBL_MST_ITEM.CUSTOMER_PART_NO','TBL_MST_ITEM.OEM_PART_NO')
                ->get()->toArray();

    $objItemGroup = DB::table('TBL_MST_ITEMGROUP')
                ->where('STATUS','=',$Status)
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Auth::user()->BRID_REF)
                ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
                ->select('TBL_MST_ITEMGROUP.*')
                ->get();

    $objStore = DB::table('TBL_MST_STORE')
                ->where('STATUS','=',$Status)
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Auth::user()->BRID_REF)
                ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
                ->select('TBL_MST_STORE.*')
                ->get();
                $AlpsStatus =   $this->AlpsStatus();
                $ActionStatus   =   "";

                $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
                     
            
    return view('transactions.inventory.PhysicalStockEntry.trnfrm311edit',compact(['objPSE','objRights','objPSEMAT', 'objItemGroup','AlpsStatus','ActionStatus','TIME','objStore','TabSetting']));
        }
     
       }



    public function view($id=NULL){
       
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
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

            $objPSE = DB::table('TBL_TRN_PSE_HDR')
                ->where('TBL_TRN_PSE_HDR.FYID_REF','=',Session::get('FYID_REF'))
                ->where('TBL_TRN_PSE_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                ->where('TBL_TRN_PSE_HDR.BRID_REF','=',Session::get('BRID_REF'))                         
                ->where('TBL_TRN_PSE_HDR.PSEID','=',$id)
                ->leftJoin('TBL_MST_STORE', 'TBL_TRN_PSE_HDR.STID_REF','=','TBL_MST_STORE.STID')    
                ->leftJoin('TBL_MST_ITEMGROUP', 'TBL_TRN_PSE_HDR.ITEMGID_REF','=','TBL_MST_ITEMGROUP.ITEMGID')    
                ->select('TBL_TRN_PSE_HDR.*','TBL_MST_STORE.STCODE','TBL_MST_STORE.NAME','TBL_MST_ITEMGROUP.GROUPCODE','TBL_MST_ITEMGROUP.GROUPNAME')
                ->first();

              // dd($objPSE);   
              
              
              if(isset($objPSE->PSETIME)){
                $TIME_DATA=explode(':',$objPSE->PSETIME);
                $TIME=$TIME_DATA[0].':'.$TIME_DATA[1];
                }else{
                $TIME='';
                }            

    $objPSEMAT = DB::table('TBL_TRN_PSE_MAT')                    
                ->where('TBL_TRN_PSE_MAT.PSEID_REF','=',$id)
                ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_PSE_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID') 
                ->leftJoin('TBL_MST_UOM', 'TBL_TRN_PSE_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                                        
                ->orderBy('TBL_TRN_PSE_MAT.PSE_MATID','ASC')
                ->select('TBL_TRN_PSE_MAT.*','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','TBL_MST_ITEM.ALPS_PART_NO','TBL_MST_ITEM.CUSTOMER_PART_NO','TBL_MST_ITEM.OEM_PART_NO')
                ->get()->toArray();

    $objItemGroup = DB::table('TBL_MST_ITEMGROUP')
                ->where('STATUS','=',$Status)
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Auth::user()->BRID_REF)
                ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
                ->select('TBL_MST_ITEMGROUP.*')
                ->get();

    $objStore = DB::table('TBL_MST_STORE')
                ->where('STATUS','=',$Status)
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Auth::user()->BRID_REF)
                ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
                ->select('TBL_MST_STORE.*')
                ->get();
                $AlpsStatus =   $this->AlpsStatus();
                $ActionStatus   =   "disabled";
                     

                $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

    return view('transactions.inventory.PhysicalStockEntry.trnfrm311view',compact(['objPSE','objRights','objPSEMAT', 'objItemGroup','AlpsStatus','ActionStatus','TIME','objStore','TabSetting']));
        }
     
       }

     


    //update the data
   public function update(Request $request){

    $r_count1 = $request['Row_Count1'];
     
    for ($i=0; $i<=$r_count1; $i++)
    {
            if(isset($request['MainItemId1_Ref_'.$i]) && !is_null($request['MainItemId1_Ref_'.$i]))
            {
               
        $reqdata2[$i] = [                          
            'ITEMID_REF'             => $request['MainItemId1_Ref_'.$i],
            'UOMID_REF'              => (!empty($request['UOM_REF_'.$i]) ? $request['UOM_REF_'.$i] : 0),
            'STOCK_IN_HAND_QTY'      => (!empty($request['STOCK_IN_HAND_'.$i]) ? $request['STOCK_IN_HAND_'.$i] : 0),
            'STOCK_IN_HAND_VALUE'    =>   0,
            'PHYSICAL_STOCK_QTY'     =>   (!empty($request['PHYSICAL_'.$i]) ? $request['PHYSICAL_'.$i] : 0),
            'PHYSICAL_STOCK_VALUE'   =>   0,
            'REASON_DISCREPANCY'     =>   $request['REASON_'.$i],                          
        ];                
            }        
    }



       if(isset($reqdata2))
       { 
        $wrapped_links2["MAT"] = $reqdata2;
        $XMLMAT = ArrayToXml::convert($wrapped_links2);
       }
       else
       {
        $XMLMAT = NULL; 
       }   
    
        $VTID_REF     =   $this->vtid_ref;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS  = $request->getClientIp();
        $CYID_REF   = Auth::user()->CYID_REF;
        $BRID_REF   = Session::get('BRID_REF');
        $FYID_REF   = Session::get('FYID_REF');

        $PSENO  = strtoupper($request['DOCNO']);
        $PSEDT  = $request['PSEDT'];
        $TIME  = $request['TIME'];
        $TAKING_BY      = $request['TAKING_BY'];
        $FROM_STID_REF = $request['FROMSTOREID_REF'];
        $ITEMGID_REF   = $request['ITEMGID_REF'];           
        $ICID_REF   =   NULL;  




        $log_data = [ 
            $PSENO,$PSEDT,$TIME,$FROM_STID_REF,$ICID_REF,$TAKING_BY,$ITEMGID_REF,$CYID_REF, $BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
        ];

        //dd($log_data); 


        $sp_result = DB::select('EXEC SP_PSE_UP ?,?,?,? ,?,?,?,? ,?,?,?,?, ?,?,?,?,?', $log_data);   
         
    
        if($sp_result[0]->RESULT=="SUCCESS"){

            return Response::json(['success' =>true,'msg' => 'Record successfully updated.']);

        }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
        }
        exit();  
    }
  

    //update the data
   public function Approve(Request $request){


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
                                if(isset($request['MainItemId1_Ref_'.$i]) && !is_null($request['MainItemId1_Ref_'.$i]))
                                {
                                   
                            $reqdata2[$i] = [                          
                                'ITEMID_REF'             => $request['MainItemId1_Ref_'.$i],
                                'UOMID_REF'              => (!empty($request['UOM_REF_'.$i]) ? $request['UOM_REF_'.$i] : 0),
                                'STOCK_IN_HAND_QTY'      => (!empty($request['STOCK_IN_HAND_'.$i]) ? $request['STOCK_IN_HAND_'.$i] : 0),
                                'STOCK_IN_HAND_VALUE'    =>   0,
                                'PHYSICAL_STOCK_QTY'     =>   (!empty($request['PHYSICAL_'.$i]) ? $request['PHYSICAL_'.$i] : 0),
                                'PHYSICAL_STOCK_VALUE'   =>   0,
                                'REASON_DISCREPANCY'     =>   $request['REASON_'.$i],                          
                            ];                
                                }        
                        }
                    
                    
                    
                           if(isset($reqdata2))
                           { 
                            $wrapped_links2["MAT"] = $reqdata2;
                            $XMLMAT = ArrayToXml::convert($wrapped_links2);
                           }
                           else
                           {
                            $XMLMAT = NULL; 
                           }   
                        
                            $VTID_REF     =   $this->vtid_ref;
                            $USERID = Auth::user()->USERID;   
                            $ACTIONNAME = $Approvallevel;
                            $IPADDRESS  = $request->getClientIp();
                            $CYID_REF   = Auth::user()->CYID_REF;
                            $BRID_REF   = Session::get('BRID_REF');
                            $FYID_REF   = Session::get('FYID_REF');
                    
                            $PSENO  = strtoupper($request['DOCNO']);
                            $PSEDT  = $request['PSEDT'];
                            $TIME  = $request['TIME'];
                            $TAKING_BY      = $request['TAKING_BY'];
                            $FROM_STID_REF = $request['FROMSTOREID_REF'];
                            $ITEMGID_REF   = $request['ITEMGID_REF'];           
                            $ICID_REF   =   NULL;  
                    
                            $log_data = [ 
                                $PSENO,$PSEDT,$TIME,$FROM_STID_REF,$ICID_REF,$TAKING_BY,$ITEMGID_REF,$CYID_REF, $BRID_REF,$FYID_REF,$VTID_REF,$XMLMAT,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
                            ];
                    
                            //dd($log_data); 
                    
                    
                            $sp_result = DB::select('EXEC SP_PSE_UP ?,?,?,? ,?,?,?,? ,?,?,?,?, ?,?,?,?,?', $log_data);   
                             
                       
                //DD($sp_result); 
            
                if($sp_result[0]->RESULT=="SUCCESS"){
        
                    return Response::json(['success' =>true,'msg' => 'Record successfully approved.']);
        
                }else{
                    return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please check the data.']);
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
                $TABLE      =   "TBL_TRN_PSE_HDR";
                $FIELD      =   "PSEID";
                $ACTIONNAME = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();         
            // dd($xml);
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
    
            if($sp_result[0]->RESULT=="All records approved"){
    
            return Response::json(['approve' =>true,'msg' => 'Record successfully Approved.']);
    
            }elseif($sp_result[0]->RESULT=="NO RECORD FOR APPROVAL"){
            
            return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','storetostore'=>'norecord']);
            
            }else{
            return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','storetostore'=>'Some Error']);
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
        $TABLE      =   "TBL_TRN_PSE_HDR";
        $FIELD      =   "PSEID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();       

        $req_data[0]=[
            'NT'  => 'TBL_TRN_PSE_MAT',
        ];
      
        $wrapped_links["TABLES"] = $req_data; 
        
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
     

        $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$XMLTAB];
       // dd($mst_cancel_data);
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
    
    $image_path         =   "docs/company".$CYID_REF."/PhysicalStockEntry";     
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
        return redirect()->route("transaction",[311,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
         $sp_result = DB::select('EXEC SP_ATTACHMENT_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $attachment_data);
         

   } catch (\Throwable $th) {
    
       return redirect()->route("transaction",[311,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("transaction",[311,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("transaction",[311,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("transaction",[311,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

public function codeduplicate(Request $request){
    $DOCNO  =   trim($request['DOCNO']);    
    $objLabel = DB::table('TBL_TRN_PSE_HDR')
    ->where('CYID_REF','=',Auth::user()->CYID_REF)
    ->where('BRID_REF','=',Session::get('BRID_REF'))
    ->where('FYID_REF','=',Session::get('FYID_REF'))
    ->where('PSENO','=',$DOCNO)
    ->select('PSEID')->first();

  // dd($objLabel); 

    if($objLabel){  
        return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
    }else{
        return Response::json(['not exists'=>true,'msg' => 'Ok']);
    }
    
    exit();
}




    public function AlpsStatus(){

      //  $COMPANY_NAME   =   "ALPS India Pvt Ltd";
        $COMPANY_NAME   =   DB::table('TBL_MST_COMPANY')->where('STATUS','=','A')->where('CYID','=',Auth::user()->CYID_REF)->select('TBL_MST_COMPANY.NAME')->first()->NAME;
    
        $disabled       =   strpos($COMPANY_NAME,"ALPS")!== false?'disabled':'';
        $hidden         =   strpos($COMPANY_NAME,"ALPS")!== false?'':'hidden';
        $colspan        =   strpos($COMPANY_NAME,"ALPS")!== false?'7':'4';
    
        return  $ALPS_STATUS=array(
            'hidden'=>$hidden,
            'disabled'=>$disabled,
            'colspan'=>$colspan
        );
    
    }


    public function StockInHand($ITEMID_REF){

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $Stock   =   DB::table('TBL_MST_BATCH')
        ->where('BRID_REF','=',$BRID_REF)
        ->where('CYID_REF','=',$CYID_REF)
        ->where('ITEMID_REF','=',$ITEMID_REF)
        ->select('TBL_MST_BATCH.CURRENT_QTY')
        ->first()->CURRENT_QTY;         
        
        $stockInHand       =   ($Stock!=''? $Stock :'0');   
    
        return  $stockInHand; 
    
    }

    
}
