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

class TrnFrm230Controller extends Controller
{
    protected $form_id = 230;
    protected $vtid_ref   = 320;  //voucher type id
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

        $objDataList	=	DB::select("select hdr.*,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.DPPID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                            ) AS CREATED_BY,
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
                            inner join TBL_TRN_PDDPP_HDR hdr
                            on a.VID = hdr.DPPID 
                            and a.VTID_REF = hdr.VTID_REF 
                            and a.CYID_REF = hdr.CYID_REF 
                            and a.BRID_REF = hdr.BRID_REF
                            where a.VTID_REF = '$this->vtid_ref'
                            and hdr.CYID_REF='$CYID_REF' AND hdr.BRID_REF='$BRID_REF'
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)
                            ORDER BY hdr.DPPID DESC ");

                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );
        
        return view('transactions.Production.DailyProductionPlan.trnfrm230',compact(['REQUEST_DATA','objRights','FormId','objDataList']));        
    }

    public function add(){       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $cur_date = Date('Y-m-d');


        $objCustList = DB::select('select SLID_REF FROM TBL_MST_CUSTOMER where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?) and CYID_REF = ?  and BRID_REF=? and STATUS = ? ', [$cur_date,$CYID_REF,$BRID_REF,$Status]);

        $strCustIds = '';
        foreach($objCustList as $index=>$val){
            $strCustIds= $strCustIds.$val->SLID_REF.",";
        }
        
        $strCustIds = substr($strCustIds,0,-1);

        $objSLList = DB::select("select SGLID, SGLCODE, SLNAME, SALIAS FROM TBL_MST_SUBLEDGER where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>='".$cur_date."') and CYID_REF = ".$CYID_REF."   and STATUS = 'A' and  SGLID in (".$strCustIds.") ");
        
        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_PDDPP_HDR',
            'HDR_ID'=>'DPPID',
            'HDR_DOC_NO'=>'DPP_NO',
            'HDR_DOC_DT'=>'DPP_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);

         

        $objPriority = DB::select('select PRIORITYID, PRIORITYCODE,DESCRIPTIONS FROM TBL_MST_PRIORITY where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND     (DODEACTIVATED is null or DODEACTIVATED>=?) and STATUS = ? ', [$cur_date,'A']);

        $objlast_DT = DB::select('SELECT MAX(DPP_DT) DPP_DT FROM TBL_TRN_PDDPP_HDR  
                            WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
                            [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'N' ]);


        $AlpsStatus =   $this->AlpsStatus();

        $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

      
        return view('transactions.Production.DailyProductionPlan.trnfrm230add', compact(['AlpsStatus','objSLList','objlast_DT','objPriority','TabSetting','doc_req','docarray']));       
   }

   


   

    public function getItemDetails(Request $request){
        //dd($request->all()); 
        $Status = 'A';
        $subled_id = $request['SLIDREF'];
        $so_id = $request['SOIDREF'];
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $AlpsStatus =   $this->AlpsStatus();
       
        $FMODE= isset($request['mode']) ? $request['mode'] : '';
        $DPPID= isset($request['DPPID'])?$request['DPPID'] : '';


        $ObjItem = DB::table('TBL_TRN_SLSO01_MAT')                    
            ->where('TBL_TRN_SLSO01_MAT.SOID_REF','=',$so_id)
            ->leftJoin('TBL_MST_ITEM','TBL_TRN_SLSO01_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID')                
            ->leftJoin('TBL_MST_UOM','TBL_TRN_SLSO01_MAT.MAIN_UOMID_REF','=','TBL_MST_UOM.UOMID')                
            ->select( 
                'TBL_TRN_SLSO01_MAT.SOMATID',
                'TBL_TRN_SLSO01_MAT.SOID_REF',
                'TBL_TRN_SLSO01_MAT.SQA',
                'TBL_TRN_SLSO01_MAT.ITEMID_REF',
                'TBL_TRN_SLSO01_MAT.MAIN_UOMID_REF',
                'TBL_TRN_SLSO01_MAT.ITEMSPECI',
                'TBL_TRN_SLSO01_MAT.SO_QTY',
                'TBL_TRN_SLSO01_MAT.ALT_UOMID_REF',                							
                'TBL_TRN_SLSO01_MAT.SEQID_REF',                							
                'TBL_MST_ITEM.ITEMID',
                'TBL_MST_ITEM.ICODE',
                'TBL_MST_ITEM.NAME',
                'TBL_MST_ITEM.ITEMGID_REF',
                'TBL_MST_ITEM.ICID_REF',
                'TBL_MST_UOM.UOMID',
                'TBL_MST_UOM.UOMCODE',
                'TBL_MST_UOM.DESCRIPTIONS',
            )
            ->orderBy('TBL_TRN_SLSO01_MAT.SOMATID','ASC')
            ->get();
      

                if(!empty($ObjItem)){

                    foreach ($ObjItem as $index=>$dataRow){                    
                       
                        $ObjBalSOQTY =  DB::select('select SUM(PRO_PLAN_QTY) AS TOTAL_PRO_PLAN_QTY from  TBL_TRN_PDDPP_MAT where SOID_REF=? and ITEMID_REF=? and UOMID_REF=?',
                                        [$so_id, $dataRow->ITEMID_REF, $dataRow->MAIN_UOMID_REF]);    
                                    
                        if(!empty($ObjBalSOQTY)){
                                $tot_ppqty = !is_null($ObjBalSOQTY[0]->TOTAL_PRO_PLAN_QTY) ?  number_format(floatVal($ObjBalSOQTY[0]->TOTAL_PRO_PLAN_QTY),3,'.','') : 0;
                            
                                //subtract this record saved qty
                                if($FMODE=='edit'){
                                    $ObjOldQty =  DB::select('select top 1 PRO_PLAN_QTY from  TBL_TRN_PDDPP_MAT where DPPID_REF=? AND SLID_REF=? AND SOID_REF=? and ITEMID_REF=? and UOMID_REF=?  AND  (SQID_REF IS NULL OR  SQID_REF=?) AND (SEQID_REF IS NULL OR  SEQID_REF=?)',
                                                [$DPPID, $subled_id,  $so_id, $dataRow->ITEMID_REF, $dataRow->MAIN_UOMID_REF,$dataRow->SQA, $dataRow->SEQID_REF]);  
                                
                                    $oldpptQty = 0;
                                    if(!empty($ObjOldQty)){
                                        $oldpptQty =  !is_null($ObjOldQty[0]->PRO_PLAN_QTY) ?  number_format(floatVal($ObjOldQty[0]->PRO_PLAN_QTY),3,'.','') : 0;
                                        $tot_ppqty = number_format( floatVal($tot_ppqty) -  floatVal( $oldpptQty) , 3, '.','');
                                    }
                                }
                               
                        $ObjItem[$index]->totalppqty = $tot_ppqty;
                         
                        }else{
                            $ObjItem[$index]->totalppqty = 0;
                        }


                    $ObjMainUOM =  DB::select('SELECT TOP 1  UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                                WHERE  CYID_REF = ?  AND UOMID = ? 
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?',
                                [$CYID_REF, $dataRow->MAIN_UOMID_REF, 'A' ]);

                    $ObjAltUOM="";
                    $ObjAltQTY ="";
                    $TOQTY =  0; 
                    $FROMQTY = 0;  

                    $ObjItemCategory =  DB::select('SELECT TOP 1 ICCODE, DESCRIPTIONS FROM TBL_MST_ITEMCATEGORY  
                                WHERE  CYID_REF = ?  AND ICID = ?
                                AND ( DEACTIVATED IS NULL OR DEACTIVATED = 0 ) AND STATUS = ?', 
                                [$CYID_REF, $dataRow->ICID_REF, 'A' ]);

                   
                    $ItemRowData =  DB::select('SELECT top 1 * FROM TBL_MST_ITEM  WHERE ITEMID = ? ', [$dataRow->ITEMID]);

                    if(!is_null($ItemRowData[0]->BUID_REF)){
                        $ObjBusinessUnit =  DB::select('SELECT TOP 1  * FROM TBL_MST_BUSINESSUNIT  
                        WHERE  CYID_REF = ? AND BRID_REF = ?  AND BUID = ?', 
                        [$CYID_REF, $BRID_REF, $ItemRowData[0]->BUID_REF]);
                    }
                    else
                    {
                        $ObjBusinessUnit = NULL;
                    }
                    
                    $BusinessUnit       =   isset($ObjBusinessUnit) && $ObjBusinessUnit != NULL ? $ObjBusinessUnit[0]->BUCODE.'-'.$ObjBusinessUnit[0]->BUNAME : '';
                    $ALPS_PART_NO       =   $ItemRowData[0]->ALPS_PART_NO;
                    $CUSTOMER_PART_NO   =   $ItemRowData[0]->CUSTOMER_PART_NO;
                    $OEM_PART_NO        =   $ItemRowData[0]->OEM_PART_NO;

                 
                        $row = '';
                        //-------------------------------
                        $row = $row.'<tr id="item_'.$dataRow->ITEMID.'-'.$dataRow->SOID_REF.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'"  class="clsitemid"><td          style="width:10%; text-align: center;"><input type="checkbox" id="chkId'.$dataRow->ITEMID.'"  value="'.$dataRow->ITEMID.'" class="js-selectall1"  ></td>';

                            $row = $row.'<td style="width:20%;">'.$dataRow->ICODE;
                            $row = $row.'<input type="hidden" id="txtitem_'.$dataRow->ITEMID.'-'.$dataRow->SOID_REF.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'" data-desc="'.$dataRow->ICODE.'" data-subledid="'.$subled_id.'" data-soqty="'.$dataRow->SO_QTY.'" data-soid="'.$dataRow->SOID_REF.'" data-sqaid="'.$dataRow->SQA.'"  data-seqid="'.$dataRow->SEQID_REF.'" data-totalppqty="'.$ObjItem[$index]->totalppqty.'"  value="'.$dataRow->ITEMID.'"/></td>

                            <td style="width:20%" id="itemname_'.$dataRow->ITEMID.'-'.$dataRow->SOID_REF.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'" >'.$dataRow->NAME;
                            $row = $row.'<input type="hidden" id="txtitemname_'.$dataRow->ITEMID.'-'.$dataRow->SOID_REF.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'" data-desc="'.$dataRow->ITEMSPECI.'"  value="'.$dataRow->NAME.'"/></td>';

                            $row = $row.'<td style="width:10%" id="itemuom_'.$dataRow->ITEMID.'-'.$dataRow->SOID_REF.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'" ><input type="hidden" id="txtitemuom_'.$dataRow->ITEMID.'-'.$dataRow->SOID_REF.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'" data-desc="'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'"  value="'.$dataRow->MAIN_UOMID_REF.'"/>'.$ObjMainUOM[0]->UOMCODE.'-'.$ObjMainUOM[0]->DESCRIPTIONS.'</td>';
                        
                            $row = $row.'
                            <td style="width:10%;">'.$BusinessUnit.'</td>
                            <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$ALPS_PART_NO.'</td>
                            <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$CUSTOMER_PART_NO.'</td>
                            <td style="width:10%;" '.$AlpsStatus['hidden'].' >'.$OEM_PART_NO.'</td>

                            <td hidden><input type="hidde" id="addinfoitem_'.$dataRow->ITEMID.'-'.$dataRow->SOID_REF.'-'.$dataRow->SQA.'-'.$dataRow->SEQID_REF.'" data-desc101="'.$ALPS_PART_NO.'" data-desc102="'.$CUSTOMER_PART_NO.'" data-desc103="'.$OEM_PART_NO.'" ></td>
                            ';
                        
                            //-------------------------------
                        
                        $row = $row.'</tr>';
                        echo $row;    
                    } 
                    
                    
                }           
                else{
                 echo '<tr><td> Record not found.</td></tr>';
                }
        exit();
    }

    

    public function getaltuomqty(Request $request){
        $id = $request['id'];
        $itemid = $request['itemid'];
        $mqty = $request['mqty'];

    
        $ObjData =  DB::select('SELECT top 1 TO_QTY, FROM_QTY FROM TBL_MST_ITEM_UOMCONV  
                    WHERE  ITEMID_REF = ? AND TO_UOMID_REF =?', [$itemid,$id]);
    
         
                if(!empty($ObjData)){
                $auomqty = ($mqty/$ObjData[0]->FROM_QTY)*($ObjData[0]->TO_QTY);
                echo($auomqty);
    
                }else{
                    echo '0';
                }
                exit();
    
        }

    public function getAltUOM(Request $request){
        $id = $request['id'];

        $ObjData =  DB::select('SELECT TO_UOMID_REF FROM TBL_MST_ITEM_UOMCONV  
                WHERE ITEMID_REF= ?  order by IUCID ASC', [$id]);

        if(!empty($ObjData)){

        foreach ($ObjData as $index=>$dataRow){

            $ObjAltUOM =  DB::select('SELECT top 1 UOMCODE, DESCRIPTIONS FROM TBL_MST_UOM  
                WHERE UOMID= ?  ', [$dataRow->TO_UOMID_REF]);
        
            $row = '';
            $row = $row.'<tr id="altuom_'.$dataRow->TO_UOMID_REF .'"  class="clsaltuom"><td width="50%">'.$ObjAltUOM[0]->UOMCODE;
            $row = $row.'<input type="hidden" id="txtaltuom_'.$dataRow->TO_UOMID_REF.'" data-desc="'.$ObjAltUOM[0]->UOMCODE .' - ';
            $row = $row.$ObjAltUOM[0]->DESCRIPTIONS. '" value="'.$dataRow->TO_UOMID_REF.'"/></td><td>'.$ObjAltUOM[0]->DESCRIPTIONS.'</td></tr>';

            echo $row;
        }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();
    
    }

    


   //display attachments form
   public function attachment($id){

    $FormId = $this->form_id;
    if(!is_null($id))
    {
        $objMst = DB::table("TBL_TRN_PDDPP_HDR")
                    ->where('DPPID','=',$id)
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
                        ->where('TBL_MST_ATTACHMENT.FYID_REF','=',Session::get('FYID_REF'))
                        ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                        ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                        ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                        ->get()->toArray();
                        
            return view('transactions.Production.DailyProductionPlan.trnfrm230attachment',compact(['FormId','objMst','objMstVoucherType','objAttachments']));
    }

}

    
   public function save(Request $request) {
            
            $r_count1 = $request['Row_Count1'];

            for ($i=0; $i<=$r_count1; $i++)
            {
                if(isset($request['ITEMID_REF_'.$i]))
                {
                    $req_data[$i] = [
                        
                    'SLID_REF'        => $request['SLID_REF_'.$i],  //sub leader
                    'SOID_REF'        => $request['SOID_REF_'.$i],
                    'ITEMID_REF'      => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'       => $request['MAIN_UOMID_REF_'.$i],     
                    'SO_QTY'          => $request['SO_QTY_'.$i],     
                    'BL_SO_QTY'       => $request['BAL_SOQTY_'.$i],     
                    'PRO_PLAN_QTY'    => (!is_null($request['PRO_PLANQTY_'.$i]) && !empty($request['PRO_PLANQTY_'.$i]) )? $request['PRO_PLANQTY_'.$i] : 0,
                    'PRIORITYID_REF'  => $request['dpp_priority_'.$i],
                    'SEQID_REF'       => $request['SEQ_REFID_'.$i],
                    'SQID_REF'        => $request['SQA_REFID_'.$i]
                    
                ];
                }
            }
        
            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
            

            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $VTID_REF     =   $this->vtid_ref;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();


            $DPP_NO = trim( strtoupper($request['DPP_NO']) );
            $DPP_DT = $request['DPPDT'];
            
          
            $log_data = [ 
                $DPP_NO,        $DPP_DT,        $CYID_REF,      $BRID_REF,  
                $FYID_REF,      $VTID_REF,      $XMLMAT,        $USERID, 
                Date('Y-m-d'), Date('h:i:s.u'), $ACTIONNAME,    $IPADDRESS
            ];

           // @DPP_NO @DPP_DT @CYID_REF @BRID_REF @FYID_REF @VTID_REF @MAT @USERID_REF @UPDATE @UPTIME @ACTION @IPADDRESS      
            
            $sp_result = DB::select('EXEC SP_DPP_IN ?,?,?,?, ?,?,?,?, ?,?,?,?', $log_data);       
            
            $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
            if($contains){
                return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);
    
            }else{
                return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
            }
            exit(); 
     }



    public function edit($id){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        $cur_date = Date('Y-m-d');
        
        if(!is_null($id))
        {
            $objDPP = DB::table('TBL_TRN_PDDPP_HDR')
                             ->where('TBL_TRN_PDDPP_HDR.FYID_REF','=',$FYID_REF)
                             ->where('TBL_TRN_PDDPP_HDR.CYID_REF','=',$CYID_REF)
                             ->where('TBL_TRN_PDDPP_HDR.BRID_REF','=',$BRID_REF)
                             ->where('TBL_TRN_PDDPP_HDR.DPPID','=',$id)
                             ->select('TBL_TRN_PDDPP_HDR.*')
                             ->first();
            
            if(!empty($objDPP)){
                if(trim($objDPP->STATUS) !='N'){
                    exit('Sorry: Only "Not Approved" record can be edit.');
                }
            } 


            $objDPPMAT = DB::table('TBL_TRN_PDDPP_MAT')                    
                             ->where('TBL_TRN_PDDPP_MAT.DPPID_REF','=',$id)
                             ->leftJoin('TBL_TRN_SLSO01_HDR', 'TBL_TRN_PDDPP_MAT.SOID_REF','=','TBL_TRN_SLSO01_HDR.SOID') 
                             ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_PDDPP_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID') 
                             ->leftJoin('TBL_MST_UOM','TBL_TRN_PDDPP_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID') 
                             ->select('TBL_TRN_PDDPP_MAT.*','TBL_TRN_SLSO01_HDR.SOID','TBL_TRN_SLSO01_HDR.SONO','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ICODE','TBL_MST_UOM.UOMID',
                             'TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','TBL_MST_ITEM.ALPS_PART_NO','TBL_MST_ITEM.CUSTOMER_PART_NO','TBL_MST_ITEM.OEM_PART_NO')
                             ->orderBy('TBL_TRN_PDDPP_MAT.DPP_MATID','ASC')
                             ->get()->toArray();

         
            $objCount1 = count($objDPPMAT);            
            //----------------------------------------
                $tempObj = $objDPPMAT;
                foreach ($tempObj as $mindex => $mvalue) {

                    $objSLID = DB::select('SELECT top 1 SGLID,SGLCODE,SLNAME FROM TBL_MST_SUBLEDGER  WHERE SGLID=? AND CYID_REF=?', 
                                  [$mvalue->SLID_REF, $CYID_REF]);

                    $objDPPMAT[$mindex]->SL_CODE = $objSLID[0]->SGLCODE;           
                    
                    
                    $ObjBalSOQTY =  DB::select('select SUM(PRO_PLAN_QTY) AS TOTAL_PRO_PLAN_QTY from  TBL_TRN_PDDPP_MAT where SOID_REF=? and ITEMID_REF=? and UOMID_REF=?',
                    [$mvalue->SOID_REF, $mvalue->ITEMID_REF, $mvalue->UOMID_REF]);    
                                
                    if(!empty($ObjBalSOQTY)){

                       $tot_ppqty = !is_null($ObjBalSOQTY[0]->TOTAL_PRO_PLAN_QTY) ?  number_format(floatVal($ObjBalSOQTY[0]->TOTAL_PRO_PLAN_QTY),3,'.','') : 0;
                       $tot_ppqty = number_format( floatVal($tot_ppqty) -  floatVal( $mvalue->PRO_PLAN_QTY) , 3, '.',''); 
                       $objDPPMAT[$mindex]->totalppqty = $tot_ppqty;
                        
                    }else{
                        $objDPPMAT[$mindex]->totalppqty = 0;
                    }
                    

                } // foreach end

                $tempObj="";
            //----------------------------------------
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
  
            $objCustList = DB::select('select SLID_REF FROM TBL_MST_CUSTOMER where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?) and CYID_REF = ?  and BRID_REF=? and STATUS = ? ', [$cur_date,$CYID_REF,$BRID_REF,$Status]);

            $strCustIds = '';
            foreach($objCustList as $index=>$val){
                $strCustIds= $strCustIds.$val->SLID_REF.",";
            }
            
            $strCustIds = substr($strCustIds,0,-1);
    
            $objSLList = DB::select("select SGLID, SGLCODE, SLNAME, SALIAS FROM TBL_MST_SUBLEDGER where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>='".$cur_date."') and CYID_REF = ".$CYID_REF."   and STATUS = 'A' and  SGLID in (".$strCustIds.") ");
                

            $objPriority = DB::select('select PRIORITYID, PRIORITYCODE,DESCRIPTIONS FROM TBL_MST_PRIORITY where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND    (DODEACTIVATED is null or DODEACTIVATED>=?) and STATUS = ? ', [$cur_date,'A']);

          
           $objlast_DT = DB::select('SELECT MAX(DPP_DT) DPP_DT FROM TBL_TRN_PDDPP_HDR  
           WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
           [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'N' ]);

           $AlpsStatus =   $this->AlpsStatus();
           $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');


            return view('transactions.Production.DailyProductionPlan.trnfrm230edit',compact(['AlpsStatus','objDPP','objRights','objCount1', 'objDPPMAT','objSLList','objlast_DT','objPriority','TabSetting']));
        }
     
    }
     
    public function view($id){

        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
        $cur_date = Date('Y-m-d');
        
        if(!is_null($id))
        {
            $objDPP = DB::table('TBL_TRN_PDDPP_HDR')
                             ->where('TBL_TRN_PDDPP_HDR.FYID_REF','=',$FYID_REF)
                             ->where('TBL_TRN_PDDPP_HDR.CYID_REF','=',$CYID_REF)
                             ->where('TBL_TRN_PDDPP_HDR.BRID_REF','=',$BRID_REF)
                             ->where('TBL_TRN_PDDPP_HDR.DPPID','=',$id)
                             ->select('TBL_TRN_PDDPP_HDR.*')
                             ->first();
            
            
            $objDPPMAT = DB::table('TBL_TRN_PDDPP_MAT')                    
                             ->where('TBL_TRN_PDDPP_MAT.DPPID_REF','=',$id)
                             ->leftJoin('TBL_TRN_SLSO01_HDR', 'TBL_TRN_PDDPP_MAT.SOID_REF','=','TBL_TRN_SLSO01_HDR.SOID') 
                             ->leftJoin('TBL_MST_ITEM', 'TBL_TRN_PDDPP_MAT.ITEMID_REF','=','TBL_MST_ITEM.ITEMID') 
                             ->leftJoin('TBL_MST_UOM','TBL_TRN_PDDPP_MAT.UOMID_REF','=','TBL_MST_UOM.UOMID') 
                             ->select('TBL_TRN_PDDPP_MAT.*','TBL_TRN_SLSO01_HDR.SOID','TBL_TRN_SLSO01_HDR.SONO','TBL_MST_ITEM.NAME','TBL_MST_ITEM.ICODE','TBL_MST_UOM.UOMID',
                             'TBL_MST_UOM.UOMCODE','TBL_MST_UOM.DESCRIPTIONS','TBL_MST_ITEM.ALPS_PART_NO','TBL_MST_ITEM.CUSTOMER_PART_NO','TBL_MST_ITEM.OEM_PART_NO')
                             ->orderBy('TBL_TRN_PDDPP_MAT.DPP_MATID','ASC')
                             ->get()->toArray();
         
            $objCount1 = count($objDPPMAT);            
            //----------------------------------------
                $tempObj = $objDPPMAT;
                foreach ($tempObj as $mindex => $mvalue) {

                    $objSLID = DB::select('SELECT top 1 SGLID,SGLCODE,SLNAME FROM TBL_MST_SUBLEDGER  WHERE SGLID=? AND CYID_REF=?', 
                                  [$mvalue->SLID_REF, $CYID_REF]);

                    $objDPPMAT[$mindex]->SL_CODE = $objSLID[0]->SGLCODE;           
                    
                    
                    $ObjBalSOQTY =  DB::select('select SUM(PRO_PLAN_QTY) AS TOTAL_PRO_PLAN_QTY from  TBL_TRN_PDDPP_MAT where SOID_REF=? and ITEMID_REF=? and UOMID_REF=?',
                    [$mvalue->SOID_REF, $mvalue->ITEMID_REF, $mvalue->UOMID_REF]);    
                                
                    if(!empty($ObjBalSOQTY)){

                       $tot_ppqty = !is_null($ObjBalSOQTY[0]->TOTAL_PRO_PLAN_QTY) ?  number_format(floatVal($ObjBalSOQTY[0]->TOTAL_PRO_PLAN_QTY),3,'.','') : 0;
                       $tot_ppqty = number_format( floatVal($tot_ppqty) -  floatVal( $mvalue->PRO_PLAN_QTY) , 3, '.',''); 
                       $objDPPMAT[$mindex]->totalppqty = $tot_ppqty;
                        
                    }else{
                        $objDPPMAT[$mindex]->totalppqty = 0;
                    }
                    

                } // foreach end

                $tempObj="";
            //----------------------------------------
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Session::get('BRID_REF'))
                             
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
  
            $objCustList = DB::select('select  SLID_REF FROM TBL_MST_CUSTOMER where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>=?) and CYID_REF = ?  and BRID_REF=? and STATUS = ? ', [$cur_date,$CYID_REF,$BRID_REF,$Status]);

            $strCustIds = '';
            foreach($objCustList as $index=>$val){
                $strCustIds= $strCustIds.$val->SLID_REF.",";
            }
            
            $strCustIds = substr($strCustIds,0,-1);
    
            $objSLList = DB::select("select SGLID, SGLCODE, SLNAME, SALIAS FROM TBL_MST_SUBLEDGER where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND (DODEACTIVATED is null or DODEACTIVATED>='".$cur_date."') and CYID_REF = ".$CYID_REF."   and STATUS = 'A' and  SGLID in (".$strCustIds.") ");
                

            $objPriority = DB::select('select PRIORITYID, PRIORITYCODE,DESCRIPTIONS FROM TBL_MST_PRIORITY where (DEACTIVATED=0 or DEACTIVATED is null or DEACTIVATED=1) AND    (DODEACTIVATED is null or DODEACTIVATED>=?) and STATUS = ? ', [$cur_date,'A']);

          
           $objlast_DT = DB::select('SELECT MAX(DPP_DT) DPP_DT FROM TBL_TRN_PDDPP_HDR  
           WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
           [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'N' ]);

           $AlpsStatus =   $this->AlpsStatus();

           $TabSetting	=	Helper::getAddSetting(Auth::user()->CYID_REF,'ITEM_TAB_SETTING');

            return view('transactions.Production.DailyProductionPlan.trnfrm230view',compact(['AlpsStatus','objDPP','objRights','objCount1', 'objDPPMAT','objSLList','objlast_DT','objPriority','TabSetting']));
        }
        
        
    } //view

    


public function update(Request $request){
        
    
        $r_count1 = $request['Row_Count1'];  
        for ($i=0; $i<=$r_count1; $i++)
        {
            if(isset($request['ITEMID_REF_'.$i]))
            {
                $req_data[$i] = [
                    
                    'SLID_REF'        => $request['SLID_REF_'.$i],  //sub leader
                    'SOID_REF'        => $request['SOID_REF_'.$i],
                    'ITEMID_REF'      => $request['ITEMID_REF_'.$i],
                    'UOMID_REF'       => $request['MAIN_UOMID_REF_'.$i],     
                    'SO_QTY'          => $request['SO_QTY_'.$i],     
                    'BL_SO_QTY'       => $request['BAL_SOQTY_'.$i],     
                    'PRO_PLAN_QTY'    => (!is_null($request['PRO_PLANQTY_'.$i]) && !empty($request['PRO_PLANQTY_'.$i]) )? $request['PRO_PLANQTY_'.$i] : 0,
                    'PRIORITYID_REF'  => $request['dpp_priority_'.$i],
                    'SEQID_REF'       => $request['SEQ_REFID_'.$i],
                    'SQID_REF'        => $request['SQA_REFID_'.$i]
                

                ];
            }
        }

        $wrapped_links["MAT"] = $req_data; 
        $XMLMAT = ArrayToXml::convert($wrapped_links);
        
        $VTID_REF     =   $this->vtid_ref;
        $USERID = Auth::user()->USERID;   
        $ACTIONNAME = 'EDIT';
        $IPADDRESS = $request->getClientIp();
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');
        $DPP_NO = trim(strtoupper($request['DPP_NO']));
        $DPP_DT = $request['DPPDT'];
       
        $log_data = [ 
            $DPP_NO,        $DPP_DT,        $CYID_REF,      $BRID_REF,  
            $FYID_REF,      $VTID_REF,      $XMLMAT,        $USERID, 
            Date('Y-m-d'), Date('h:i:s.u'), $ACTIONNAME,    $IPADDRESS
        ];

       
        $sp_result = DB::select('EXEC SP_DPP_UP ?,?,?,?, ?,?,?,?, ?,?,?,?', $log_data);        
      
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
            //------------------------
            for ($i=0; $i<=$r_count1; $i++)
            {
                if(isset($request['ITEMID_REF_'.$i]))
                {
                    $req_data[$i] = [
                        'SLID_REF'        => $request['SLID_REF_'.$i],  //sub leader
                        'SOID_REF'        => $request['SOID_REF_'.$i],
                        'ITEMID_REF'      => $request['ITEMID_REF_'.$i],
                        'UOMID_REF'       => $request['MAIN_UOMID_REF_'.$i],     
                        'SO_QTY'          => $request['SO_QTY_'.$i],     
                        'BL_SO_QTY'       => $request['BAL_SOQTY_'.$i],     
                        'PRO_PLAN_QTY'    => (!is_null($request['PRO_PLANQTY_'.$i]) && !empty($request['PRO_PLANQTY_'.$i]) )? $request['PRO_PLANQTY_'.$i] : 0,
                        'PRIORITYID_REF'  => $request['dpp_priority_'.$i],
                        'SEQID_REF'       => $request['SEQ_REFID_'.$i],
                        'SQID_REF'        => $request['SQA_REFID_'.$i]
                    ];
                }
            }    
            //------------------------
            $ACTIONNAME = $Approvallevel;

            $wrapped_links["MAT"] = $req_data; 
            $XMLMAT = ArrayToXml::convert($wrapped_links);
            
            $VTID_REF     =   $this->vtid_ref;
            $USERID = Auth::user()->USERID;   
           
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $DPP_NO = trim(strtoupper($request['DPP_NO']));
            $DPP_DT = $request['DPPDT'];
   
            $log_data = [ 
                $DPP_NO,        $DPP_DT,        $CYID_REF,      $BRID_REF,  
                $FYID_REF,      $VTID_REF,      $XMLMAT,        $USERID, 
                Date('Y-m-d'), Date('h:i:s.u'), $ACTIONNAME,    $IPADDRESS
            ];

            $sp_result = DB::select('EXEC SP_DPP_UP ?,?,?,?, ?,?,?,?, ?,?,?,?', $log_data);        
            
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
                $TABLE      =   "TBL_TRN_PDDPP_HDR";
                $FIELD      =   "DPPID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            if($sp_result[0]->RESULT=="All records approved"){
    
                return Response::json(['approve' =>true,'msg' => 'Record successfully Approved.']);
    
            }elseif($sp_result[0]->RESULT=="NO RECORD FOR APPROVAL"){
            
                return Response::json(['errors'=>true,'msg' => 'No Record Found for Approval.','cancelresponse'=>'norecord']);
            
            }else{
                return Response::json(['errors'=>true,'msg' => 'There is some error in data. Please try after sometime.','cancelresponse'=>'Some Error']);
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
        $TABLE      =   "TBL_TRN_PDDPP_HDR";
        $FIELD      =   "DPPID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
            'NT'  => 'TBL_TRN_PDDPP_MAT',
           ];

        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $cancel_xmldata = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_DPP ?,?,?,?, ?,?,?,?, ?,?,?,?', $cancel_xmldata);
        //dd($sp_result); 

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
    
    $image_path         =   "docs/company".$CYID_REF."/DailyProductionPlan";     
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
        return redirect()->route("transaction",[230,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
    
       return redirect()->route("transaction",[230,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("transaction",[230,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("transaction",[230,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("transaction",[230,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

    
    public function getso(Request $request){

        $SLID = $request["SLID"];
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $cur_date = Date('Y-m-d');
        $ObjData = DB::select('select SOID,SONO,SODT FROM TBL_TRN_SLSO01_HDR where  CYID_REF = ?  and BRID_REF=?  and FYID_REF=? and STATUS = ?  and SLID_REF=?', [$CYID_REF,$BRID_REF,$FYID_REF,'A',$SLID]);

        
            if(!empty($ObjData)){

                foreach ($ObjData as $index=>$dataRow){               

                    $dtso = Date('d-M-Y',strtotime($dataRow->SODT));

                    $row = '';
                    $row = $row.'<tr id="FORMcode_'.$dataRow->SOID .'"  class="clsFORMid"><td width="50%">'.$dataRow->SONO;
                    $row = $row.'<input type="hidden" id="txtFORMcode_'.$dataRow->SOID.'" data-desc="'.$dataRow->SONO.'"  data-descdate="'.$dtso.'"
                    value="'.$dataRow->SOID.'"/></td><td>'.$dtso.'</td></tr>';
                    echo $row;

                }
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
            exit();
    }
    
    


    public function codeduplicate(Request $request){

            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $DPP_NO =   $request['DPP_NO'];
            
            $objLabel = DB::table('TBL_TRN_PDDPP_HDR')
            ->where('CYID_REF','=',$CYID_REF)
            ->where('BRID_REF','=',$BRID_REF)
            ->where('FYID_REF','=',$FYID_REF)
            ->where('DPP_NO','=',$DPP_NO)
            ->select('DPP_NO')
            ->first();
            
            if($objLabel){  

                return Response::json(['exists' =>true,'msg' => 'Duplicate record']);
            
            }else{

                return Response::json(['notexists'=>true,'msg' => 'Ok']);
            }
            
            exit();
    }

    
 
    
} //class
