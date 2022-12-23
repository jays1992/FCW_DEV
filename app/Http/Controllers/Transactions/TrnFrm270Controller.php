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


class TrnFrm270Controller extends Controller{
    protected $form_id = 270;
    
    protected $vtid_ref   = 360;  //voucher type id
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
                        // $objDataList    =   DB::table('TBL_TRN_STORE_TO_STORE_HDR')
                        //     ->where('CYID_REF','=',Auth::user()->CYID_REF)
                        //     ->where('BRID_REF','=',Session::get('BRID_REF'))
                        //     ->orderByDesc('STTOSTID')
                        //     ->get();       
                      
                           
        $CYID_REF   	=   Auth::user()->CYID_REF;
        $BRID_REF   	=   Session::get('BRID_REF');
        $FYID_REF   	=   Session::get('FYID_REF');     


        $objFinalAppr = DB::select("select dbo.FN_APRL('$this->vtid_ref','$CYID_REF','$BRID_REF','$FYID_REF') as FA_NO");
        $FANO = "APPROVAL".$objFinalAppr[0]->FA_NO;

        $objDataList	=	DB::select("select hdr.STTOSTID,hdr.STTOST_DOCNO,hdr.STTOST_DOCDT,hdr.INDATE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.STTOSTID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
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
                            inner join TBL_TRN_STORE_TO_STORE_HDR hdr
                            on a.VID = hdr.STTOSTID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF' AND hdr.FYID_REF='$FYID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.STTOSTID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );
                           
        
        return view('transactions.inventory.StoretoStoreTransafer.trnfrm270',compact(['REQUEST_DATA','objRights','FormId','objDataList']));        
    }

    public function add(){       

                    $Status = "A";
                    $CYID_REF = Auth::user()->CYID_REF;
                    $BRID_REF = Session::get('BRID_REF');
                    $FYID_REF = Session::get('FYID_REF');


        $objItemCategory = DB::table('TBL_MST_ITEMCATEGORY')
                ->where('STATUS','=',$Status)
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Auth::user()->BRID_REF)
                ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
                ->select('TBL_MST_ITEMCATEGORY.*')
                ->get();
              //  dd($objItemCategory);
                

        $objItemgroup = DB::table('TBL_MST_ITEMGROUP')
                ->where('STATUS','=',$Status)
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Auth::user()->BRID_REF)
                ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
                ->select('TBL_MST_ITEMGROUP.*')
                ->get();

                // dd($objItemgroup); 


        $objStore = DB::table('TBL_MST_STORE')
                ->where('STATUS','=',$Status)
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Auth::user()->BRID_REF)
                ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
                ->select('TBL_MST_STORE.*')
                ->get();
                

        $objlastSTSDT = DB::select('SELECT MAX(STTOST_DOCDT) STTOST_DOCDT FROM TBL_TRN_STORE_TO_STORE_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  270, 'A' ]);

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_STORE_TO_STORE_HDR',
            'HDR_ID'=>'STTOSTID',
            'HDR_DOC_NO'=>'STTOST_DOCNO',
            'HDR_DOC_DT'=>'STTOST_DOCDT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);


            

       $AlpsStatus =   $this->AlpsStatus();
       $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

        
    return view('transactions.inventory.StoretoStoreTransafer.trnfrm270add', compact(['objItemgroup','objlastSTSDT','objItemCategory','objStore','AlpsStatus','TabSetting','doc_req','docarray']));       
   }


    

   //display attachments form
   public function attachment($id){

    if(!is_null($id))
    {
        $objstore = DB::table("TBL_TRN_STORE_TO_STORE_HDR")
                        ->where('STTOSTID','=',$id)
                        ->select('TBL_TRN_STORE_TO_STORE_HDR.*')
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

            return view('transactions.inventory.StoretoStoreTransafer.trnfrm270attachment',compact(['objstore','objMstVoucherType','objAttachments']));
    }

}




    
    public function get_items(Request $request) {    

        $Status         =   "A";
        $CYID_REF       =   Auth::user()->CYID_REF;
        $BRID_REF       =   Session::get('BRID_REF');
        $FYID_REF       =   Session::get('FYID_REF');
        $ITEMCATEGORY   =   $request['ITEMCAT'];
        $ITEMGROUP_REF  =   $request['ITEMGID'];
        $AlpsStatus     =   $this->AlpsStatus();

        $objitem = DB::table('TBL_MST_ITEM')
        ->where('TBL_MST_ITEM.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_MST_ITEM.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_MST_ITEM.ITEMGID_REF','=',$ITEMGROUP_REF)
        ->where('TBL_MST_ITEM.ICID_REF','=',$ITEMCATEGORY)
        ->where('TBL_MST_ITEM.STATUS','=',$Status)
        ->leftJoin('TBL_MST_UOM', 'TBL_MST_ITEM.MAIN_UOMID_REF','=','TBL_MST_UOM.UOMID')  
        ->leftJoin('TBL_MST_ITEMCHECKFLAG', 'TBL_MST_ITEM.ITEMID','=','TBL_MST_ITEMCHECKFLAG.ITEMID_REF')  
        ->select('TBL_MST_ITEM.ITEMID','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.DRAWINGNO','TBL_MST_ITEM.MAIN_UOMID_REF','TBL_MST_ITEM.ALT_UOMID_REF','TBL_MST_ITEM.DRAWINGNO','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','TBL_MST_ITEMCHECKFLAG.BATCHNOA','TBL_MST_ITEM.ALPS_PART_NO','TBL_MST_ITEM.CUSTOMER_PART_NO','TBL_MST_ITEM.OEM_PART_NO')
        ->get()    
        ->toArray();

        if(!empty($objitem)){        
            foreach ($objitem as $index=>$dataRow){

                $ItemRowData =  DB::select('SELECT top 1 * FROM TBL_MST_ITEM  WHERE ITEMID = ? ', [$dataRow->ITEMID]);

                if(!is_null($ItemRowData[0]->BUID_REF)){
                    $ObjBusinessUnit =  DB::select('SELECT TOP 1  * FROM TBL_MST_BUSINESSUNIT  
                    WHERE  CYID_REF = ? AND BRID_REF = ?  AND BUID = ?', 
                    [$CYID_REF, $BRID_REF, $ItemRowData[0]->BUID_REF]);
                }
                else{
                    $ObjBusinessUnit = NULL;
                }
                    
                $BusinessUnit       =   isset($ObjBusinessUnit) && $ObjBusinessUnit != NULL ? $ObjBusinessUnit[0]->BUCODE.'-'.$ObjBusinessUnit[0]->BUNAME : '';
                $ALPS_PART_NO       =   $ItemRowData[0]->ALPS_PART_NO;
                $CUSTOMER_PART_NO   =   $ItemRowData[0]->CUSTOMER_PART_NO;
                $OEM_PART_NO        =   $ItemRowData[0]->OEM_PART_NO;


                $row = '';
                $row = $row.'<tr ><td style="text-align:center; width:12%">';
                $row = $row.'<input type="checkbox"  id="mainitemidcode_substitute_'.$dataRow->ITEMID.'" class="item_click" 
                value="'.$dataRow->ITEMID.'"/>             
                </td>           
                <td style="width:11%;">'.$dataRow->ICODE;
                $row = $row.'<input type="hidden" id="txtmainitemidcode_substitute_'.$dataRow->ITEMID.'" data-code="'.$dataRow->ICODE.'"   data-uomno="'.$dataRow->MAIN_UOMID_REF.'" data-alt_uomno="'.$dataRow->ALT_UOMID_REF.'"  data-name="'.$dataRow->NAME.'"  data-uom="'.$dataRow->UOMCODE.'-'.$dataRow->DESCRIPTIONS.'"   data-batchnoa="'.$dataRow->BATCHNOA.'"  data-alps_partno="'.$ALPS_PART_NO.'"  data-customer_partno="'.$CUSTOMER_PART_NO.'"  data-oem_partno="'.$OEM_PART_NO.'"
                value="'.$dataRow->ITEMID.'"/></td>

                <td style="width:11%;">'.$dataRow->NAME.'</td>
                <td style="width:11%;">'.$dataRow->UOMCODE.' '.$dataRow->DESCRIPTIONS.'</td>
                <td style="width:11%;">'.$dataRow->DRAWINGNO.'</td>
                <td style="width:11%;">'.$BusinessUnit.'</td>
                <td style="width:11%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                <td style="width:11%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                <td style="width:11%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>

            </tr>';
                echo $row;
            }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }

        exit();

   }


   public function save(Request $request) {  
       //dd($request->all()); 
   
        $r_count1 = count($request['rowscount1']);
        $r_count3 = $request['Row_Count3'];
     
        for ($i=0; $i<=$r_count1; $i++)
        {
                if(isset($request['ITEMG_CODE_'.$i]) && !is_null($request['ITEMG_CODE_'.$i]))
                {
                   
                        $reqdata2[$i] = [                          
                            'ITEMGID_REF'       => $request['ITEMGID_REF_'.$i],
                            'ITEMID_REF'        => $request['MainItemId1_Ref_'.$i],
                            'UOMID_REF'         => (!empty($request['UOM_REF_'.$i]) ? $request['UOM_REF_'.$i] : 0),
                            'ALT_UOMID_REF'     => (!empty($request['ALTUOMID_REF_'.$i]) ? $request['ALTUOMID_REF_'.$i] : 0),
                            'QTY'               =>  (!empty($request['ITEM_QTY_'.$i]) ? $request['ITEM_QTY_'.$i] : 0),
                            'BATCHID'           =>  NULL,
                            'RATE'              =>   (!empty($request['RATE_'.$i]) ? $request['RATE_'.$i] : 0),
                            'REMARKS'           =>   $request['REMARKS_'.$i],
                            'BATCH_QTY'         => $request['HiddenRowId_'.$i],                    
                        ];
                    
                }
            
        }



        for ($i=1; $i<$r_count3; $i++)
        {
            if($request['DISPATCH_MAIN_QTY_'.$i] !="" && $request['DISPATCH_MAIN_QTY_'.$i] > 0){
                $req_data3[$i] = [
             
                    'BATCHID_REF'       => $request['BATCHID_REF_'.$i] ,
                    'MAIN_UOMID_REF'    => $request['STRMUOMID_REF_'.$i],
                    'TRANSFER_QTY'      => $request['DISPATCH_MAIN_QTY_'.$i],
                    'ALT_UOMID_REF'     => $request['STRAUOMID_REF_'.$i],
                    'ITEMID_REF'        => $request['STRITEM_REF_'.$i],
                    'FROM_STID_REF'     => $request['FROMSTOREID_REF'],
                    'TO_STID_REF'       => $request['TOSTOREID_REF'],
              
                ];
            }
            // else
            // {
            //     $req_data3 = [];
            // }
        }


        //dd($req_data3); 
        
      
        if(isset($req_data3))
        { 
            $wrapped_links2["STORE"] = $req_data3; 
            $XMLSTORE = ArrayToXml::convert($wrapped_links2);
        }
        else
        {
            $XMLSTORE = NULL; 
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

            $STTOST_DOCNO  = strtoupper($request['DOCNO']);
            $STTOST_DOCDT  = $request['STSDT'];
            $ICID_REF      = $request['ITEMCAT_REF'];
            $FROM_STID_REF = $request['FROMSTOREID_REF'];
            $TO_STID_REF   = $request['TOSTOREID_REF'];
           
            $DEACTIVATED   =   NULL;  
            $DODEACTIVATED =   NULL;  

           

            $log_data = [ 
                $STTOST_DOCNO,$STTOST_DOCDT,$ICID_REF,$FROM_STID_REF,$TO_STID_REF,$CYID_REF, $BRID_REF,$FYID_REF,$XMLMAT,$XMLSTORE,$VTID_REF,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];
         //dd($log_data); 
    
            $sp_result = DB::select('EXEC SP_STORE_TO_STORE_IN ?,?,?,? ,?,?,?,? ,?,?,?,?, ?,?,?,?', $log_data);    

            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            exit();  
     }


     public function get_tax_details(Request $request){

    
        $HSNID_REF=$request->hsnid_ref;
            $ObjTax =  DB::table('TBL_MST_HSNNORMAL')  
                    ->select('NRATE')
                    ->whereIn('TAXID_REF',function($query) 
                                {       
                                $query->select('TAXID')->from('TBL_MST_TAXTYPE')
                                                ->where('STATUS','=','A')
                                                ->where('FOR_PURCHASE','=',1)
                                                ->where('OUTOFSTATE','=',1);                       
                    })->where('HSNNID','=',$HSNID_REF) 
                    ->get()->toArray();
               

                    if($ObjTax){
                    echo  $tax=$ObjTax[0]->NRATE;
                    }else{
                    echo $tax='0.0000';

                    }   
     
     }


    public function edit($id=NULL){
       
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id))
        {
            $objSTORETOTSOTE = DB::table('TBL_TRN_STORE_TO_STORE_HDR')
                             ->where('TBL_TRN_STORE_TO_STORE_HDR.FYID_REF','=',Session::get('FYID_REF'))
                             ->where('TBL_TRN_STORE_TO_STORE_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_TRN_STORE_TO_STORE_HDR.BRID_REF','=',Session::get('BRID_REF'))                         
                             ->where('TBL_TRN_STORE_TO_STORE_HDR.STTOSTID','=',$id)
                             ->select('TBL_TRN_STORE_TO_STORE_HDR.*')
                             ->first();
                    
            $objFromStore=[];              
            if(isset($objSTORETOTSOTE->FROM_STID_REF) && $objSTORETOTSOTE->FROM_STID_REF !=""){
            $objFromStore= DB::table('TBL_MST_STORE')
                            ->where('TBL_MST_STORE.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_STORE.BRID_REF','=',Session::get('BRID_REF'))                         
                            ->where('TBL_MST_STORE.STID','=',$objSTORETOTSOTE->FROM_STID_REF)
                            ->select('TBL_MST_STORE.*')
                            ->first();
            }
                     

            $objToStore=[];
            if(isset($objSTORETOTSOTE->TO_STID_REF) && $objSTORETOTSOTE->TO_STID_REF !=""){
            $objToStore= DB::table('TBL_MST_STORE')
                            ->where('TBL_MST_STORE.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_STORE.BRID_REF','=',Session::get('BRID_REF'))                         
                            ->where('TBL_MST_STORE.STID','=',$objSTORETOTSOTE->TO_STID_REF)
                            ->select('TBL_MST_STORE.*')
                            ->first();
            }


            $objCategory=[];
            if(isset($objSTORETOTSOTE->ICID_REF) && $objSTORETOTSOTE->ICID_REF !=""){
            $objCategory= DB::table('TBL_MST_ITEMCATEGORY')
                            ->where('TBL_MST_ITEMCATEGORY.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_ITEMCATEGORY.BRID_REF','=',Session::get('BRID_REF'))                         
                            ->where('TBL_MST_ITEMCATEGORY.ICID','=',$objSTORETOTSOTE->ICID_REF)
                            ->select('TBL_MST_ITEMCATEGORY.*')
                            ->first();
            }
                           

            $objSTORETOTSOTEMAT = DB::table('TBL_TRN_STORE_TO_STORE_MAT')                    
                             ->where('TBL_TRN_STORE_TO_STORE_MAT.STTOSTID_REF','=',$id)
                             ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_STORE_TO_STORE_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                                              
                             ->leftJoin('TBL_MST_ITEMGROUP', 'TBL_TRN_STORE_TO_STORE_MAT.ITEMGID_REF','=','TBL_MST_ITEMGROUP.ITEMGID')     
                             ->leftJoin('TBL_MST_ITEMCHECKFLAG', 'TBL_MST_ITEM.ITEMID','=','TBL_MST_ITEMCHECKFLAG.ITEMID_REF')   
                             ->leftJoin('TBL_MST_UOM', 'TBL_TRN_STORE_TO_STORE_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                                        
                             ->orderBy('TBL_TRN_STORE_TO_STORE_MAT.STTOST_MATID','ASC')
                             ->select('TBL_TRN_STORE_TO_STORE_MAT.*','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ALPS_PART_NO','TBL_MST_ITEM.CUSTOMER_PART_NO','TBL_MST_ITEM.OEM_PART_NO','TBL_MST_ITEMGROUP.GROUPNAME','TBL_MST_ITEMGROUP.GROUPCODE','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','TBL_MST_ITEMCHECKFLAG.BATCHNOA')
                             ->get()->toArray();

                            // dd($objSTORETOTSOTEMAT); 

                          

            
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();

            $objItemCategory = DB::table('TBL_MST_ITEMCATEGORY')
                ->where('STATUS','=',$Status)
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Auth::user()->BRID_REF)
                ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
                ->select('TBL_MST_ITEMCATEGORY.*')
                ->get();
              //  dd($objItemCategory);
                

        $objItemgroup = DB::table('TBL_MST_ITEMGROUP')
                ->where('STATUS','=',$Status)
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Auth::user()->BRID_REF)
                ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
                ->select('TBL_MST_ITEMGROUP.*')
                ->get();

                // dd($objItemgroup); 


        $objStore = DB::table('TBL_MST_STORE')
                ->where('STATUS','=',$Status)
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Auth::user()->BRID_REF)
                ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
                ->select('TBL_MST_STORE.*')
                ->get();
                

 

                $SP_PARAMETERS = [$id];
               // $objSCSTORE = DB::select('EXEC SP_GET_STTOST_STOREDETAILS ?', $SP_PARAMETERS);

                $objSTORE = DB::table('TBL_TRN_STORE_TO_STORE_STORE')                    
                ->where('TBL_TRN_STORE_TO_STORE_STORE.STTOSTID_REF','=',$id)
                ->leftJoin('TBL_MST_BATCH', 'TBL_TRN_STORE_TO_STORE_STORE.BATCHID_REF','=','TBL_MST_BATCH.BATCHID')                                              
                                               
                ->orderBy('TBL_TRN_STORE_TO_STORE_STORE.STTOST_STOREID','ASC')
                ->select('TBL_TRN_STORE_TO_STORE_STORE.*','TBL_MST_BATCH.BATCH_CODE')
                ->get()->toArray();
//DD($objSTORE); 


                $objCount3 = count($objSTORE);

                $AlpsStatus =   $this->AlpsStatus();
                $ActionStatus   =   "";

                $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');




                     
            
    return view('transactions.inventory.StoretoStoreTransafer.trnfrm270edit',compact(['objSTORETOTSOTE','objRights','objSTORETOTSOTEMAT', 'objItemCategory','objItemCategory','objItemgroup','objStore','objFromStore','objToStore','objCategory','objSTORE','objCount3','AlpsStatus','ActionStatus','TabSetting']));
        }
     
       }

     
       public function view($id=NULL){
       
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        
        if(!is_null($id))
        {
            $objSTORETOTSOTE = DB::table('TBL_TRN_STORE_TO_STORE_HDR')
                             ->where('TBL_TRN_STORE_TO_STORE_HDR.FYID_REF','=',Session::get('FYID_REF'))
                             ->where('TBL_TRN_STORE_TO_STORE_HDR.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_TRN_STORE_TO_STORE_HDR.BRID_REF','=',Session::get('BRID_REF'))                         
                             ->where('TBL_TRN_STORE_TO_STORE_HDR.STTOSTID','=',$id)
                             ->select('TBL_TRN_STORE_TO_STORE_HDR.*')
                             ->first();
                    
            $objFromStore=[];              
            if(isset($objSTORETOTSOTE->FROM_STID_REF) && $objSTORETOTSOTE->FROM_STID_REF !=""){
            $objFromStore= DB::table('TBL_MST_STORE')
                            ->where('TBL_MST_STORE.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_STORE.BRID_REF','=',Session::get('BRID_REF'))                         
                            ->where('TBL_MST_STORE.STID','=',$objSTORETOTSOTE->FROM_STID_REF)
                            ->select('TBL_MST_STORE.*')
                            ->first();
            }
                     

            $objToStore=[];
            if(isset($objSTORETOTSOTE->TO_STID_REF) && $objSTORETOTSOTE->TO_STID_REF !=""){
            $objToStore= DB::table('TBL_MST_STORE')
                            ->where('TBL_MST_STORE.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_STORE.BRID_REF','=',Session::get('BRID_REF'))                         
                            ->where('TBL_MST_STORE.STID','=',$objSTORETOTSOTE->TO_STID_REF)
                            ->select('TBL_MST_STORE.*')
                            ->first();
            }


            $objCategory=[];
            if(isset($objSTORETOTSOTE->ICID_REF) && $objSTORETOTSOTE->ICID_REF !=""){
            $objCategory= DB::table('TBL_MST_ITEMCATEGORY')
                            ->where('TBL_MST_ITEMCATEGORY.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_ITEMCATEGORY.BRID_REF','=',Session::get('BRID_REF'))                         
                            ->where('TBL_MST_ITEMCATEGORY.ICID','=',$objSTORETOTSOTE->ICID_REF)
                            ->select('TBL_MST_ITEMCATEGORY.*')
                            ->first();
            }
                           

            $objSTORETOTSOTEMAT = DB::table('TBL_TRN_STORE_TO_STORE_MAT')                    
                             ->where('TBL_TRN_STORE_TO_STORE_MAT.STTOSTID_REF','=',$id)
                             ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_STORE_TO_STORE_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                                              
                             ->leftJoin('TBL_MST_ITEMGROUP', 'TBL_TRN_STORE_TO_STORE_MAT.ITEMGID_REF','=','TBL_MST_ITEMGROUP.ITEMGID')     
                             ->leftJoin('TBL_MST_ITEMCHECKFLAG', 'TBL_MST_ITEM.ITEMID','=','TBL_MST_ITEMCHECKFLAG.ITEMID_REF')   
                             ->leftJoin('TBL_MST_UOM', 'TBL_TRN_STORE_TO_STORE_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID')                                        
                             ->orderBy('TBL_TRN_STORE_TO_STORE_MAT.STTOST_MATID','ASC')
                             ->select('TBL_TRN_STORE_TO_STORE_MAT.*','TBL_MST_ITEM.ICODE','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ALPS_PART_NO','TBL_MST_ITEM.CUSTOMER_PART_NO','TBL_MST_ITEM.OEM_PART_NO','TBL_MST_ITEMGROUP.GROUPNAME','TBL_MST_ITEMGROUP.GROUPCODE','TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','TBL_MST_ITEMCHECKFLAG.BATCHNOA')
                             ->get()->toArray();

                            // dd($objSTORETOTSOTEMAT); 

                          

            
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();

            $objItemCategory = DB::table('TBL_MST_ITEMCATEGORY')
                ->where('STATUS','=',$Status)
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Auth::user()->BRID_REF)
                ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
                ->select('TBL_MST_ITEMCATEGORY.*')
                ->get();
              //  dd($objItemCategory);
                

        $objItemgroup = DB::table('TBL_MST_ITEMGROUP')
                ->where('STATUS','=',$Status)
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Auth::user()->BRID_REF)
                ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
                ->select('TBL_MST_ITEMGROUP.*')
                ->get();

                // dd($objItemgroup); 


        $objStore = DB::table('TBL_MST_STORE')
                ->where('STATUS','=',$Status)
                ->where('CYID_REF','=',Auth::user()->CYID_REF)
                ->where('BRID_REF','=',Auth::user()->BRID_REF)
                ->whereRaw("(DEACTIVATED=0 or DODEACTIVATED is null)")
                ->select('TBL_MST_STORE.*')
                ->get();
                

 

                $SP_PARAMETERS = [$id];
               // $objSCSTORE = DB::select('EXEC SP_GET_STTOST_STOREDETAILS ?', $SP_PARAMETERS);

                $objSTORE = DB::table('TBL_TRN_STORE_TO_STORE_STORE')                    
                ->where('TBL_TRN_STORE_TO_STORE_STORE.STTOSTID_REF','=',$id)
                ->leftJoin('TBL_MST_BATCH', 'TBL_TRN_STORE_TO_STORE_STORE.BATCHID_REF','=','TBL_MST_BATCH.BATCHID')                                              
                                               
                ->orderBy('TBL_TRN_STORE_TO_STORE_STORE.STTOST_STOREID','ASC')
                ->select('TBL_TRN_STORE_TO_STORE_STORE.*','TBL_MST_BATCH.BATCH_CODE')
                ->get()->toArray();
//DD($objSTORE); 


                $objCount3 = count($objSTORE);

                $AlpsStatus =   $this->AlpsStatus();
                $ActionStatus   =   "disabled";



                $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');
                     
            
    return view('transactions.inventory.StoretoStoreTransafer.trnfrm270view',compact(['objSTORETOTSOTE','objRights','objSTORETOTSOTEMAT', 'objItemCategory','objItemCategory','objItemgroup','objStore','objFromStore','objToStore','objCategory','objSTORE','objCount3','AlpsStatus','ActionStatus','TabSetting']));
        }
     
       }

    //update the data
   public function update(Request $request){

         //  dd($request->all()); 
   
           $r_count1 = count($request['rowscount1']);
           $r_count3 = $request['Row_Count3'];
        
           for ($i=0; $i<=$r_count1; $i++)
           {
                   if(isset($request['ITEMG_CODE_'.$i]) && !is_null($request['ITEMG_CODE_'.$i]))
                   {
                      
                           $reqdata2[$i] = [                          
                               'ITEMGID_REF'       => $request['ITEMGID_REF_'.$i],
                               'ITEMID_REF'        => $request['MainItemId1_Ref_'.$i],
                               'UOMID_REF'         => (!empty($request['UOM_REF_'.$i]) ? $request['UOM_REF_'.$i] : 0),
                               'ALT_UOMID_REF'     => (!empty($request['ALTUOMID_REF_'.$i]) ? $request['ALTUOMID_REF_'.$i] : 0),
                               'QTY'               =>  (!empty($request['ITEM_QTY_'.$i]) ? $request['ITEM_QTY_'.$i] : 0),
                               'BATCHID'           =>  NULL,
                               'RATE'              =>   (!empty($request['RATE_'.$i]) ? $request['RATE_'.$i] : 0),
                               'REMARKS'           =>   $request['REMARKS_'.$i],
                               'BATCH_QTY'         => $request['HiddenRowId_'.$i],                    
                           ];
                       
                   }
               
           }
   
   

           for ($i=1; $i<$r_count3; $i++)
           {
               if($request['DISPATCH_MAIN_QTY_'.$i] !="" && $request['DISPATCH_MAIN_QTY_'.$i] > 0){
                   $req_data3[$i] = [
                
                       'BATCHID_REF'    => $request['BATCHID_REF_'.$i] ,
                       'MAIN_UOMID_REF' => $request['STRMUOMID_REF_'.$i],
                       'TRANSFER_QTY'   => $request['DISPATCH_MAIN_QTY_'.$i],
                       'ALT_UOMID_REF'  => $request['STRAUOMID_REF_'.$i],
                       'ITEMID_REF'     => $request['STRITEM_REF_'.$i],
                       'FROM_STID_REF'  => $request['FROMSTOREID_REF'],
                       'TO_STID_REF'    => $request['TOSTOREID_REF'],
                 
                   ];
               }
               // else
               // {
               //     $req_data3 = [];
               // }
           }
   
           
         
           if(isset($req_data3))
           { 
               $wrapped_links2["STORE"] = $req_data3; 
               $XMLSTORE = ArrayToXml::convert($wrapped_links2);
           }
           else
           {
               $XMLSTORE = NULL; 
           }
   
       
  // dd($reqdata2); 
   
              if(isset($reqdata2))
              { 
               $wrapped_links2["MAT"] = $reqdata2;
               $XMLMAT = ArrayToXml::convert($wrapped_links2);
              }
              else
              {
               $XMLMAT = NULL; 
              }   
         
           //dd($XMLMAT);
               $VTID_REF     =   $this->vtid_ref;
               $USERID       = Auth::user()->USERID;   
               $ACTIONNAME   = 'EDIT';
               $IPADDRESS    = $request->getClientIp();
               $CYID_REF     = Auth::user()->CYID_REF;
               $BRID_REF     = Session::get('BRID_REF');
               $FYID_REF     = Session::get('FYID_REF');
   
               $STTOST_DOCNO   = strtoupper($request['DOCNO']);
               $STTOST_DOCDT   = $request['STSDT'];
               $ICID_REF       = $request['ITEMCAT_REF'];
               $FROM_STID_REF  = $request['FROMSTOREID_REF'];
               $TO_STID_REF    = $request['TOSTOREID_REF'];
              
               $DEACTIVATED    =   NULL;  
               $DODEACTIVATED  =   NULL;  
   
              
   
               $log_data = [ 
                   $STTOST_DOCNO,$STTOST_DOCDT,$ICID_REF,$FROM_STID_REF,$TO_STID_REF,$CYID_REF, $BRID_REF,$FYID_REF,$XMLMAT,$XMLSTORE,$VTID_REF,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
               ];
           // dd($log_data); 
       
               $sp_result = DB::select('EXEC SP_STORE_TO_STORE_UP ?,?,?,? ,?,?,?,? ,?,?,?,?, ?,?,?,?', $log_data);   
              // dd($sp_result); 
    
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


                        $r_count1 = count($request['rowscount1']);
                        $r_count3 = $request['Row_Count3'];
                     
                        for ($i=0; $i<=$r_count1; $i++)
                        {
                                if(isset($request['ITEMG_CODE_'.$i]) && !is_null($request['ITEMG_CODE_'.$i]))
                                {
                                   
                                        $reqdata2[$i] = [                          
                                            'ITEMGID_REF'    => $request['ITEMGID_REF_'.$i],
                                            'ITEMID_REF'    => $request['MainItemId1_Ref_'.$i],
                                            'UOMID_REF'         => (!empty($request['UOM_REF_'.$i]) ? $request['UOM_REF_'.$i] : 0),
                                            'ALT_UOMID_REF'         => (!empty($request['ALTUOMID_REF_'.$i]) ? $request['ALTUOMID_REF_'.$i] : 0),
                                            'QTY'  =>  (!empty($request['ITEM_QTY_'.$i]) ? $request['ITEM_QTY_'.$i] : 0),
                                            'BATCHID'    =>  NULL,
                                            'RATE'    =>   (!empty($request['RATE_'.$i]) ? $request['RATE_'.$i] : 0),
                                            'REMARKS'    =>   $request['REMARKS_'.$i],
                                            'BATCH_QTY'     => $request['HiddenRowId_'.$i],                    
                                        ];
                                    
                                }
                            
                        }
                
                
                
                        for ($i=1; $i<$r_count3; $i++)
                        {
                            if($request['DISPATCH_MAIN_QTY_'.$i] !="" && $request['DISPATCH_MAIN_QTY_'.$i] > 0){
                                $req_data3[$i] = [
                             
                                    'BATCHID_REF' => $request['BATCHID_REF_'.$i] ,
                                    'MAIN_UOMID_REF' => $request['STRMUOMID_REF_'.$i],
                                    'TRANSFER_QTY' => $request['DISPATCH_MAIN_QTY_'.$i],
                                    'ALT_UOMID_REF' => $request['STRAUOMID_REF_'.$i],
                                    'ITEMID_REF' => $request['STRITEM_REF_'.$i],
                                    'FROM_STID_REF' => $request['FROMSTOREID_REF'],
                                    'TO_STID_REF' => $request['TOSTOREID_REF'],
                              
                                ];
                            }
                            // else
                            // {
                            //     $req_data3 = [];
                            // }
                        }
                
                        
                      
                        if(isset($req_data3))
                        { 
                            $wrapped_links2["STORE"] = $req_data3; 
                            $XMLSTORE = ArrayToXml::convert($wrapped_links2);
                        }
                        else
                        {
                            $XMLSTORE = NULL; 
                        }
                
                    
               // dd($reqdata2); 
                
                           if(isset($reqdata2))
                           { 
                            $wrapped_links2["MAT"] = $reqdata2;
                            $XMLMAT = ArrayToXml::convert($wrapped_links2);
                           }
                           else
                           {
                            $XMLMAT = NULL; 
                           }   
                      
                        //dd($XMLMAT);
                            $VTID_REF     =   $this->vtid_ref;
                            $USERID = Auth::user()->USERID;   
                            $ACTIONNAME = $Approvallevel;
                            $IPADDRESS = $request->getClientIp();
                            $CYID_REF = Auth::user()->CYID_REF;
                            $BRID_REF = Session::get('BRID_REF');
                            $FYID_REF = Session::get('FYID_REF');
                
                            $STTOST_DOCNO = strtoupper($request['DOCNO']);
                            $STTOST_DOCDT = $request['STSDT'];
                            $ICID_REF = $request['ITEMCAT_REF'];
                            $FROM_STID_REF = $request['FROMSTOREID_REF'];
                            $TO_STID_REF = $request['TOSTOREID_REF'];
                           
                            $DEACTIVATED   =   NULL;  
                            $DODEACTIVATED =   NULL;  
                
                           
                
            $log_data = [ 
                        $STTOST_DOCNO,$STTOST_DOCDT,$ICID_REF,$FROM_STID_REF,$TO_STID_REF,$CYID_REF, $BRID_REF,$FYID_REF,$XMLMAT,$XMLSTORE,$VTID_REF,$USERID, Date('Y-m-d'), Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
                        ];
                        // dd($log_data); 
                    
                $sp_result = DB::select('EXEC SP_STORE_TO_STORE_UP ?,?,?,? ,?,?,?,? ,?,?,?,?, ?,?,?,?', $log_data);

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
                $TABLE      =   "TBL_TRN_STORE_TO_STORE_HDR";
                $FIELD      =   "STTOSTID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
            // dd($xml);
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_STTOST ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
    
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
        $TABLE      =   "TBL_TRN_STORE_TO_STORE_HDR";
        $FIELD      =   "STTOSTID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();       

        $req_data[0]=[
            'NT'  => 'TBL_TRN_STORE_TO_STORE_MAT',
        ];
        $req_data[1]=[
            'NT'  => 'TBL_TRN_STORE_TO_STORE_STORE',
        ];
     
    
      
        $wrapped_links["TABLES"] = $req_data; 
        
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
     

        $mst_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS ,$XMLTAB];
       // dd($mst_cancel_data);
        $sp_result = DB::select('EXEC SP_TRN_CANCEL_STTOST  ?,?,?,?, ?,?,?,?, ?,?,?,?', $mst_cancel_data);
      
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
    
    $image_path         =   "docs/company".$CYID_REF."/Storetostore";     
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
        return redirect()->route("transaction",[270,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
    
       return redirect()->route("transaction",[270,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("transaction",[270,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("transaction",[270,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("transaction",[270,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

    public function checkdocno(Request $request){

        // dd($request->LABEL_0);
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $STTOST_DOCNO = $request->DOCNO;
      
        
        $objSO = DB::table('TBL_TRN_STORE_TO_STORE_HDR')
        ->where('TBL_TRN_STORE_TO_STORE_HDR.CYID_REF','=',Auth::user()->CYID_REF)
        ->where('TBL_TRN_STORE_TO_STORE_HDR.BRID_REF','=',Session::get('BRID_REF'))
        ->where('TBL_TRN_STORE_TO_STORE_HDR.FYID_REF','=',Session::get('FYID_REF'))
        ->where('TBL_TRN_STORE_TO_STORE_HDR.STTOST_DOCNO','=',$STTOST_DOCNO)
        ->select('TBL_TRN_STORE_TO_STORE_HDR.STTOSTID')
        ->first();
        
        if($objSO){  

            return Response::json(['exists' =>true,'msg' => 'Duplicate DOCNO']);
        
        }else{

            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();

    }



    public function getItemwiseStoreDetails(Request $request){

        $Status = "A";
        $itemid = $request['itemid'];
        $uomid = $request['muomid'];
        $auomid = $request['auomid'];
        $soqty = $request['soqty'];
        $storeid = $request['storeid'];
        $qtyid = $request['qtyid'];
        //dd($storeid); 

        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $ITEMROWID  = $request['ITEMROWID'];
        $STORE_ID  = $request['STORE_ID'];
        //dd(ITEMROWID);

		
		$ACTION_TYPE    =   $request['ACTION_TYPE'] =="VIEW"?'disabled':'';
   
        $ObjData =  DB::select('SELECT * FROM TBL_MST_BATCH  
                WHERE STATUS= ? AND CYID_REF = ? AND BRID_REF = ? 
                AND ITEMID_REF = ? AND UOMID_REF = ? AND STID_REF = ?
                order by BATCH_CODE ASC', [$Status,$CYID_REF,$BRID_REF,$itemid,$uomid,$STORE_ID]);

                //dd($ObjData); 
      
        if(!empty($ObjData)){

            $dataArr    =   array();

            if($ITEMROWID !=""){
                $exp        =   explode(",",$ITEMROWID);
        
                foreach($exp as $val){
                    $keyid      =   explode("_",$val);
                    $batchid    =   $keyid[0];
                    $qty        =   $keyid[1];
                    $dataArr[$batchid]  =   $qty;
                }
            }
			
			
			
			

            $row1 = '';
            $row2 = '<input type="hidden" id="hdnItemQty" name="hdnItemQty" value="'.$soqty.'" /> 
                        <input type="hidden" id="hdnstoreid" name="hdnstoreid" value="'.$storeid.'" />
                        <input type="hidden" id="hdnqtyid" name="hdnqtyid" value="'.$qtyid.'" />  ';
            $row3 = '';

        foreach ($ObjData as $dindex=>$dRow){

            $ObjStore = DB::select('SELECT TOP 1 * FROM TBL_MST_STORE WHERE STATUS = ?
                        AND STID = ?',[$Status,$dRow->STID_REF]);
            
            $ObjMUOM = DB::select('SELECT TOP 1 * FROM TBL_MST_UOM WHERE STATUS = ?
                        AND UOMID = ?',[$Status,$dRow->UOMID_REF]);

            $ObjAUOM = DB::select('SELECT TOP 1 * FROM TBL_MST_UOM WHERE STATUS = ?
                        AND UOMID = ?',[$Status,$auomid]);

            $ObjConv = DB::select('SELECT TOP 1 * FROM TBL_MST_ITEM_UOMCONV WHERE ITEMID_REF = ?
                        AND TO_UOMID_REF = ?',[$itemid,$auomid]);

                        //dd($ObjConv); 

            $StoreRowId = $itemid.$dRow->BATCHID.$dRow->UOMID_REF.$dRow->STID_REF.$dRow->STOCKID_REF;

            $qtyvalue   =   array_key_exists($StoreRowId, $dataArr)?$dataArr[$StoreRowId]:'';
			
			
            $mqty       =   $ObjConv[0]->FROM_QTY;
            $aqty       =   $ObjConv[0]->TO_QTY;

            if($qtyvalue !=""){
                $daltqty    =   ($qtyvalue * $aqty)/$mqty;
            }
            else{
                $daltqty    =   "";
            }

            if($request['ACTION_TYPE'] =="ADD"){
                $CURRENT_QTY=$dRow->CURRENT_QTY;
            }
            else{
                $CURRENT_QTY=(floatval($dRow->CURRENT_QTY)+floatval($qtyvalue));
            }
                    
            $row = '';
            $row = $row.' <tr class="clsstrid">';
            $row = $row.'<td hidden><input type="text" name= "strITEMID_REF_'.$dindex.'" id= "strITEMID_REF_'.$dindex.'" class="form-control" value="'.$itemid.'" /></td>';
            $row = $row.'<td><input '.$ACTION_TYPE.' type="text" name= "strBATCHNO_'.$dindex.'" id= "strBATCHNO_'.$dindex.'" class="form-control" value="'.$dRow->BATCH_CODE.'" readonly /></td>';
            $row = $row.'<td hidden><input type="text" name= "strBATCHID_'.$dindex.'" id= "strBATCHID_'.$dindex.'" class="form-control" value="'.$StoreRowId.'"  /></td>';            
            $row = $row.'<td hidden><input type="text" name= "strBT_REF_'.$dindex.'" id= "strBT_REF_'.$dindex.'" class="form-control" value="'.$dRow->BATCHID.'"  /></td>';            
        
            $row = $row.'<td><input '.$ACTION_TYPE.' type="text" name= "STORE_REF_'.$dindex.'" id= "STORE_REF_'.$dindex.'" class="form-control" value="'.$ObjStore[0]->NAME.'" readonly /></td>';



            $row = $row.'<td hidden><input '.$ACTION_TYPE.' type="text"  name= "strrate_'.$dindex.'" id= "strrate_'.$dindex.'" class="form-control" value="'.$dRow->RATE.'"  /></td>';

            $row = $row.'<td hidden><input type="text" name= "strSTID_REF_'.$dindex.'" id= "strSTID_REF_'.$dindex.'" class="form-control" value="'.$dRow->STID_REF.'" /></td>';

            $row = $row.'<td hidden><input type="text" name= "MUOM_REF_'.$dindex.'" id= "MUOM_REF_'.$dindex.'" class="form-control" value="'.$dRow->UOMID_REF.'" /></td>';
            $row = $row.'<td><input '.$ACTION_TYPE.' type="text" name= "strMAINUOMID_REF_'.$dindex.'" id= "strMAINUOMID_REF_'.$dindex.'" value="'.$ObjMUOM[0]->UOMCODE.'-'.$ObjMUOM[0]->DESCRIPTIONS.'" class="form-control" readonly /></td>';
            $row = $row.'<td><input '.$ACTION_TYPE.' type="text" name= "strSOTCK_'.$dindex.'" id= "strSOTCK_'.$dindex.'" class="form-control three-digits" value="'.$CURRENT_QTY.'" readonly /></td>';
            $row = $row.'<td><input '.$ACTION_TYPE.' type="text" name= "strDISPATCH_MAIN_QTY_'.$dindex.'" id= "strDISPATCH_MAIN_QTY_'.$dindex.'" value="'.$qtyvalue.'" class="form-control three-digits" maxlength="13" onkeypress="return isNumberDecimalKey(event,this)" autocomplete="off"   /></td>';
            $row = $row.'<td hidden><input type="text" name= "CONV_MAIN_QTY_'.$dindex.'" id= "CONV_MAIN_QTY_'.$dindex.'" class="form-control three-digits" value="'.$ObjConv[0]->FROM_QTY.'"  maxlength="13"   /></td>';
            $row = $row.'<td hidden><input type="text" name= "CONV_ALT_QTY_'.$dindex.'" id= "CONV_ALT_QTY_'.$dindex.'" class="form-control three-digits" value="'.$ObjConv[0]->TO_QTY.'"  maxlength="13"   /></td>';
            $row = $row.'<td hidden><input type="text" name= "AUOM_REF_'.$dindex.'" id= "AUOM_REF_'.$dindex.'" class="form-control" value="'.$auomid.'" /></td>';
            $row = $row.'<td><input '.$ACTION_TYPE.' type="text" name= "strALTUOMID_REF_'.$dindex.'" id= "strALTUOMID_REF_'.$dindex.'" value="'.$ObjAUOM[0]->UOMCODE.'-'.$ObjAUOM[0]->DESCRIPTIONS.'" class="form-control" readonly /></td>';
            $row = $row.'<td><input '.$ACTION_TYPE.' type="text" name= "DISPATCH_ALT_QTY_'.$dindex.'" id= "DISPATCH_ALT_QTY_'.$dindex.'" class="form-control three-digits"  value="'.$daltqty.'" readonly /></td>';
            $row = $row.'</tr>';
            $row1 = $row1.$row;
            
        }

        $row3 = $row2.$row1;
        echo $row3;

        }else{
            echo '<tr><td colspan="7">Record not found.</td></tr>';
        }
        exit();

    }


    
    
}
