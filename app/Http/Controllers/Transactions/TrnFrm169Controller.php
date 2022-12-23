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

class TrnFrm169Controller extends Controller
{
    protected $form_id = 169;
    protected $vtid_ref   = 165;  //voucher type id
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
                        ->where('TBL_MST_USERROLMAP.BRID_REF','=',Auth::user()->BRID_REF)
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

        $objDataList	=	DB::select("select hdr.JVID,hdr.JV_NO,hdr.JV_DT,hdr.REVERSE_DT,hdr.SOURCE_DOCNO,hdr.SOURCE_DOCDT,hdr.INDATE,hdr.SOURCE_DOCTYPE,
                            (
                            SELECT TOP 1 USR.DESCRIPTIONS FROM TBL_TRN_AUDITTRAIL AUD 
                            LEFT JOIN TBL_MST_USER USR ON AUD.USERID=USR.USERID 
                            WHERE  AUD.VID=hdr.JVID AND  AUD.CYID_REF=hdr.CYID_REF AND  AUD.BRID_REF=hdr.BRID_REF AND  
                            AUD.FYID_REF=hdr.FYID_REF AND  AUD.VTID_REF=hdr.VTID_REF AND AUD.ACTIONNAME='ADD'       
                            ) AS CREATED_BY,
                            hdr.STATUS,hdr.NARRATION,
                            case when a.ACTIONNAME = '$FANO' then 'Final Approved'
                            else case when a.ACTIONNAME = 'ADD' then 'Added'  
                                when a.ACTIONNAME = 'EDIT' then 'Edited'
                                when a.ACTIONNAME = 'APPROVAL1' then 'First Level Approved'
                                when a.ACTIONNAME = 'APPROVAL2' then 'Second Level Approved'
                                when a.ACTIONNAME = 'APPROVAL3' then 'Third Level Approved'
                                when a.ACTIONNAME = 'APPROVAL4' then 'Fourth Level Approved'
                                when a.ACTIONNAME = 'APPROVAL5' then 'Final Approved'
                            when a.ACTIONNAME = 'CANCEL' then 'Cancelled'
                            end end as STATUS_DESC,
                            CASE
                            WHEN hdr.SOURCE_DOCTYPE ='GRN AGAINST GE' THEN 
                            (select SGLCODE+'-'+SLNAME from TBL_MST_SUBLEDGER where SGLID =(select distinct VID_REF from TBL_TRN_IGRN02_HDR where GRN_NO = hdr.SOURCE_DOCNO))
                            WHEN hdr.SOURCE_DOCTYPE = 'SALES CHALLAN' THEN 
                            (select SGLCODE+'-'+SLNAME from TBL_MST_SUBLEDGER where SGLID =(select distinct SLID_REF from TBL_TRN_SLSC01_HDR where SCNO = hdr.SOURCE_DOCNO))
                            WHEN hdr.SOURCE_DOCTYPE = 'PURCHASE INVOICE' THEN 
                            (select SGLCODE+'-'+SLNAME from TBL_MST_SUBLEDGER where SGLID =(select distinct VID_REF from TBL_TRN_PRPB01_HDR where PB_DOCNO = hdr.SOURCE_DOCNO))
                            WHEN hdr.SOURCE_DOCTYPE = 'SALES INVOICE' THEN
                            (select SGLCODE+'-'+SLNAME from TBL_MST_SUBLEDGER where SGLID =(select distinct SLID_REF from TBL_TRN_SLSI01_HDR where SINO = hdr.SOURCE_DOCNO))
                            WHEN hdr.SOURCE_DOCTYPE = 'SALES SERVICE INVOICE' THEN
                            (select SGLCODE+'-'+SLNAME from TBL_MST_SUBLEDGER where SGLID =(select distinct SGLID_REF from TBL_TRN_SLSI02_HDR where SSI_NO = hdr.SOURCE_DOCNO))
                            WHEN hdr.SOURCE_DOCTYPE = 'SERVICE PURCHASE INVOICE' THEN
                            (select SGLCODE+'-'+SLNAME from TBL_MST_SUBLEDGER where SGLID =(select distinct VID_REF from TBL_TRN_PRPB02_HDR where SPI_NO = hdr.SOURCE_DOCNO))
                            WHEN hdr.SOURCE_DOCTYPE = 'PURCHASE RETURN' THEN
                            (select SGLCODE+'-'+SLNAME from TBL_MST_SUBLEDGER where SGLID =(select distinct VID_REF from TBL_TRN_PRRT01_HDR where PRR_NO = hdr.SOURCE_DOCNO))
                            WHEN hdr.SOURCE_DOCTYPE = 'SALES RETURN' THEN
                            (select SGLCODE+'-'+SLNAME from TBL_MST_SUBLEDGER where SGLID =(select distinct SLID_REF from TBL_TRN_SLSR01_HDR where SRNO = hdr.SOURCE_DOCNO))
                            END AS SLNAME 
                            from TBL_TRN_AUDITTRAIL a 
                            right join TBL_TRN_FJRV01_HDR hdr
                            on a.VID = hdr.JVID 
                            and a.VTID_REF = hdr.VTID_REF
                            and a.ACTID in (select max(ACTID) from TBL_TRN_AUDITTRAIL b where a.VTID_REF = b.VTID_REF and a.VID = b.VID)						 
							where hdr.CYID_REF = $CYID_REF
							and hdr.BRID_REF = $BRID_REF AND hdr.FYID_REF='$FYID_REF'
                            ORDER BY hdr.JVID DESC ");



                            foreach($objDataList as $key=>$objResponse){

                                $NARRATION      =   $objResponse->NARRATION;
                                $SOURCE_DOCNO   =   $objResponse->SOURCE_DOCNO; 
                                $REFERENCE_NO   =   NULL;
                                
                                switch ($NARRATION) {
                                    case "PURCHASE INVOICE":
                                    $DataRow        =   DB::select("SELECT VENDOR_INNO FROM TBL_TRN_PRPB01_HDR WHERE PB_DOCNO ='$SOURCE_DOCNO'");
                                    $REFERENCE_NO   =   isset($DataRow[0]->VENDOR_INNO) && $DataRow[0]->VENDOR_INNO !=''?$DataRow[0]->VENDOR_INNO:NULL;
                                    break;

                                    case "SERVICE PURCHASE INVOICE":
                                    $DataRow        =   DB::select("SELECT VENDOR_INNO FROM TBL_TRN_PRPB02_HDR WHERE SPI_NO ='$SOURCE_DOCNO'");
                                    $REFERENCE_NO   =   isset($DataRow[0]->VENDOR_INNO) && $DataRow[0]->VENDOR_INNO !=''?$DataRow[0]->VENDOR_INNO:NULL;
                                    break;

                                    case "IMPORT PURCHASE INVOICE":
                                    $DataRow        =   DB::select("SELECT VENDOR_INVOICE_NO AS VENDOR_INNO FROM TBL_TRN_PII_HDR WHERE PII_NO ='$SOURCE_DOCNO'");
                                    $REFERENCE_NO   =   isset($DataRow[0]->VENDOR_INNO) && $DataRow[0]->VENDOR_INNO !=''?$DataRow[0]->VENDOR_INNO:NULL;
                                    break;
                                }

                                $objDataList[$key]->REFERENCE_NO   =   $REFERENCE_NO;
                            }
                            
                           


                            $REQUEST_DATA   =   array(
                                'FORMID'    =>  $this->form_id,
                                'VTID_REF'  =>  $this->vtid_ref,
                                'USERID'    =>  Auth::user()->USERID,
                                'CYID_REF'  =>  Auth::user()->CYID_REF,
                                'BRID_REF'  =>  Session::get('BRID_REF'),
                                'FYID_REF'  =>  Session::get('FYID_REF'),
                            );
                            
		//dd($objDataList);
        return view('transactions.Accounts.JournalVoucher.trnfrm169',compact(['REQUEST_DATA','objRights','FormId','objDataList']));        
    }

    public function add(){       
        $Status = "A";
        $CYID_REF = Auth::user()->CYID_REF;
        $BRID_REF = Session::get('BRID_REF');
        $FYID_REF = Session::get('FYID_REF');

        $objlastJVDT = DB::select('SELECT MAX(JV_DT) JV_DT FROM TBL_TRN_FJRV01_HDR  
        WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
        [$CYID_REF, $BRID_REF,  $this->vtid_ref, 'A' ]);

        $doc_req    =   array(
            'VTID_REF'=>$this->vtid_ref,
            'HDR_TABLE'=>'TBL_TRN_FJRV01_HDR',
            'HDR_ID'=>'JVID',
            'HDR_DOC_NO'=>'JV_NO',
            'HDR_DOC_DT'=>'JV_DT'
        );
        $docarray   =   $this->getManualAutoDocNo(date('Y-m-d'),$doc_req);
        
        

        $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_JV")->select('*')
                    ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                {       
                                $query->select('UDFJVID')->from('TBL_MST_UDFFOR_JV')
                                                ->where('STATUS','=','A')
                                                ->where('PARENTID','=',0)
                                                ->where('DEACTIVATED','=',0)
                                                ->where('CYID_REF','=',$CYID_REF);
                                                                  
                    })->where('DEACTIVATED','=',0)
                    ->where('STATUS','<>','C')                    
                    ->where('CYID_REF','=',$CYID_REF);
                                   
                   

        $objUdfJVData = DB::table('TBL_MST_UDFFOR_JV')
            ->where('STATUS','=','A')
            ->where('PARENTID','=',0)
            ->where('DEACTIVATED','=',0)
            ->where('CYID_REF','=',$CYID_REF)
           
            ->union($ObjUnionUDF)
            ->get()->toArray();   
        $objCountUDF = count($objUdfJVData);
        $objCostCenter = DB::table('TBL_MST_COSTCENTER')
        ->where('CYID_REF','=',$CYID_REF)
        ->where('STATUS','=','A')
        ->select('TBL_MST_COSTCENTER.*')
        ->get();

    return view('transactions.Accounts.JournalVoucher.trnfrm169add',
        compact(['objlastJVDT','objUdfJVData','objCountUDF','objCostCenter','doc_req','docarray']));       
   }

   

   public function getglsl(Request $request){
    $CYID_REF = Auth::user()->CYID_REF;
    $BRID_REF = Session::get('BRID_REF');
    $SL = $request['SL'];
    $fieldid    = $request['fieldid'];

    $log_data = [ 
        $SL, $CYID_REF, $BRID_REF
    ];
    
    $sp_result = DB::select('EXEC SP_GET_GLSL ?,?,?', $log_data); 

        if(!empty($sp_result)){

        foreach ($sp_result as $index=>$dataRow){
        
            $row = '';
            $row = $row.'<tr >
            <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="glsl_'.$dataRow->ID .'"  class="clsglid" value="'.$dataRow->ID.'" ></td>
            <td class="ROW2">'.$dataRow->CODE;
            $row = $row.'<input type="hidden" id="txtglsl_'.$dataRow->ID.'" data-desc="'.$dataRow->CODE .'" data-desc2="'.$dataRow->NAME.'" ';
            $row = $row.' data-desc3="'.$dataRow->FLAG.'" value="'.$dataRow->ID.'"/></td>
            
            <td class="ROW3">'.$dataRow->NAME.'</td></tr>';

            echo $row;
        }

        }else{
            echo '<tr><td colspan="2">Record not found.</td></tr>';
        }
        exit();

    }

    public function getcostcenterglwise(Request $request){
        
        $JVID = $request['JVID'];
        $glid = $request['glid'];
        $glcode = $request['glcode'];
        $glflag = $request['glflag'];
        $gldesc = $request['gldesc'];
        $CYID_REF = Auth::user()->CYID_REF;
              
        
        $log_data = [ 
            $JVID, $glid, $CYID_REF
        ];
        
        $sp_result = DB::select('EXEC SP_GET_JV_COSTCENTER ?,?,?', $log_data); 
        if(!empty($sp_result)){
            foreach ($sp_result as $index=>$dataRow){
            
                $row = '';
                $row = $row.'<tr class="participantRow2">
                <td style="width:105px;">
                <input type="text" name="ppGLID_'.$index.'" id="ppGLID_'.$index.'" class="form-control" value="'.$glcode.'"  autocomplete="off"  readonly/></td>
                <td hidden><input type="hidden" name="hdnGLID_'.$index.'" id="hdnGLID_'.$index.'" class="form-control" value="'.$glid.'" autocomplete="off" /></td>
                <td hidden><input type="hidden" name="hdnflag_'.$index.'" id="hdnflag_'.$index.'" class="form-control" value="'.$glflag.'" autocomplete="off" /></td>
                <td style="width:150px;"><input type="text" name="hdnDescription_'.$index.'" id="hdnDescription_'.$index.'" value="'.$gldesc.'" class="form-control"  autocomplete="off"  readonly/></td>
                <td style="width:105px;"><input type="text" name="CostCenter_'.$index.'" id="CostCenter_'.$index.'" value="'.$dataRow->CCCODE.'" class="form-control" maxlength="20"  autocomplete="off" readonly  /></td>
                <td hidden><input type="hidden" name="hdnCCID_'.$index.'" id="hdnCCID_'.$index.'" class="form-control" value="'.$dataRow->CCID_REF.'" autocomplete="off" /></td>
                <td style="width:105px;"><input type="text" name="hdnDRAMT_'.$index.'" id="hdnDRAMT_'.$index.'" value="'.$dataRow->DR_AMT.'" class="form-control two-digits" maxlength="15"  autocomplete="off"  /></td>
                <td style="width:105px;"><input type="text" name="hdnCRAMT_'.$index.'" id="hdnCRAMT_'.$index.'" value="'.$dataRow->CR_AMT.'" class="form-control two-digits" maxlength="15"  autocomplete="off"  /></td>
                <td style="width:70px;"><button class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                <button class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
              </tr>';

                echo $row;
            }

            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
        exit();
    }

    public function getcostcenterglwise2(Request $request){
        
        $JVID = $request['JVID'];
        $glid = $request['glid'];
        $glcode = $request['glcode'];
        $glflag = $request['glflag'];
        $gldesc = $request['gldesc'];
        $CYID_REF = Auth::user()->CYID_REF;
              
        
        $log_data = [ 
            $JVID, $glid, $CYID_REF
        ];
        
        $sp_result = DB::select('EXEC SP_GET_JV_COSTCENTER ?,?,?', $log_data); 
        if(!empty($sp_result)){
            foreach ($sp_result as $index=>$dataRow){
            
                $row = '';
                $row = $row.'<tr class="participantRow2">
                <td style="width:105px;">
                <input type="text" name="ppGLID_'.$index.'" id="ppGLID_'.$index.'" class="form-control" value="'.$glcode.'"  autocomplete="off"  readonly/></td>
                <td hidden><input type="hidden" name="hdnGLID_'.$index.'" id="hdnGLID_'.$index.'" class="form-control" value="'.$glid.'" autocomplete="off" /></td>
                <td hidden><input type="hidden" name="hdnflag_'.$index.'" id="hdnflag_'.$index.'" class="form-control" value="'.$glflag.'" autocomplete="off" /></td>
                <td style="width:150px;"><input type="text" name="hdnDescription_'.$index.'" id="hdnDescription_'.$index.'" value="'.$gldesc.'" class="form-control"  autocomplete="off"  readonly/></td>
                <td style="width:105px;"><input type="text" name="CostCenter_'.$index.'" id="CostCenter_'.$index.'" value="'.$dataRow->CCCODE.'" class="form-control" maxlength="20"  autocomplete="off" readonly  /></td>
                <td hidden><input type="hidden" name="hdnCCID_'.$index.'" id="hdnCCID_'.$index.'" class="form-control" value="'.$dataRow->CCID_REF.'" autocomplete="off" readonly/></td>
                <td style="width:105px;"><input type="text" name="hdnDRAMT_'.$index.'" id="hdnDRAMT_'.$index.'" value="'.$dataRow->DR_AMT.'" class="form-control two-digits" maxlength="15"  autocomplete="off" readonly  /></td>
                <td style="width:105px;"><input type="text" name="hdnCRAMT_'.$index.'" id="hdnCRAMT_'.$index.'" value="'.$dataRow->CR_AMT.'" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly /></td>
                <td style="width:70px;"><button class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                <button class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
              </tr>';

                echo $row;
            }

            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
        exit();
    }

    public function getCostCenter(Request $request){
        
        $customid = $request['customid'];
        $CYID_REF = Auth::user()->CYID_REF;
        $fieldid    = $request['fieldid'];
              
        
        $sp_result = DB::table('TBL_MST_COSTCENTER')
        ->where('CYID_REF','=',$CYID_REF)
        ->where('STATUS','=','A')
        ->select('TBL_MST_COSTCENTER.*')
        ->get(); 
        if(!empty($sp_result)){
            foreach ($sp_result as $index=>$dataRow){
            
                $row = '';
                $row = $row.' <tr>
                <td class="ROW1"> <input type="checkbox" name="SELECT_'.$fieldid.'[]" id="cccode_'.$dataRow->CCID .'"  class="clscccd" value="'.$dataRow->CCID.'" ></td>
                <td class="ROW2">'.$dataRow->CCCODE;
                $row = $row.' <input type="hidden" id="txtcccode_'.$dataRow->CCID.'" data-desc="'.$dataRow->CCCODE .'"  ';
                $row = $row.' value="'.$dataRow->CCID.'"/></td><td class="ROW3" >'.$dataRow->NAME.'</td></tr>';

                echo $row;
            }

            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
        exit();
    }

   //display attachments form
   public function attachment($id){

    if(!is_null($id))
    {
        $objSalesenquiry = DB::table("TBL_TRN_FJRV01_HDR")
                        ->where('JVID','=',$id)
                        ->select('TBL_TRN_FJRV01_HDR.*')
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
                        ->where('TBL_MST_ATTACHMENT.BRID_REF','=',Auth::user()->BRID_REF)
                        ->where('TBL_MST_ATTACHMENT.FYID_REF','=',Auth::user()->FYID_REF)
                        ->leftJoin('TBL_MST_ATTACHMENT_DET', 'TBL_MST_ATTACHMENT.ATTACHMENTID','=','TBL_MST_ATTACHMENT_DET.ATTACHMENTID_REF')
                        ->select('TBL_MST_ATTACHMENT.*', 'TBL_MST_ATTACHMENT_DET.*')
                        ->orderBy('TBL_MST_ATTACHMENT.ATTACHMENTID','ASC')
                        ->get()->toArray();

                 // dump( $objAttachments);

            return view('transactions.Accounts.JournalVoucher.trnfrm169attachment',compact(['objSalesenquiry','objMstVoucherType','objAttachments']));
    }

}

    
   public function save(Request $request) {

        // dd($request->all());
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];

            for ($i=0; $i<=$r_count1; $i++)
            {
                if(isset($request['GLID_REF_'.$i]))
                {
                    $req_data[$i] = [
                        'GLID_REF'          => $request['GLID_REF_'.$i],
                        'SGLID_REF'         => $request['txtflag_'.$i],
                        'DR_AMT'            => (!is_null($request['DR_AMT_'.$i]) ? $request['DR_AMT_'.$i] : 0),
                        'CR_AMT'            => (!is_null($request['CR_AMT_'.$i]) ? $request['CR_AMT_'.$i] : 0),
                        'NARRATION'         => $request['NARRATION_'.$i],
                        'CCID_REF'          => $request['CCID_REF_'.$i],
                    ];
                }
            }
        
            $wrapped_links["ACCOUNT"] = $req_data; 
            $XMLACC = ArrayToXml::convert($wrapped_links);

            for ($i=0; $i<=$r_count2; $i++)
            {
                if(isset($request['UDFJVID_REF_'.$i]) && !is_null($request['UDFJVID_REF_'.$i]))
                {
                    $reqdata2[$i] = [
                        'UDFJVID_REF'   => $request['UDFJVID_REF_'.$i],
                        'VALUE'         => $request['udfvalue_'.$i],
                    ];
                }
            }

            if(isset($reqdata2))
            { 
                $wrapped_links2["UDF"] = $reqdata2; 
                $XMLUDF = ArrayToXml::convert($wrapped_links2);
            }
            else
            {
                $XMLUDF = NULL; 
            }

            for ($i=0; $i<=$r_count3; $i++)
            {
                if(isset($request['GLID_'.$i]) && !is_null($request['GLID_'.$i]))
                {
                    $reqdata3[$i] = [
                        'GLID_REF'   => $request['GLID_'.$i],
                        'CCID_REF'   => $request['CCID_'.$i],
                        'DR_AMT'     => (!is_null($request['D_AMT_'.$i]) ? $request['D_AMT_'.$i] : 0),
                        'CR_AMT'     => (!is_null($request['C_AMT_'.$i]) ? $request['C_AMT_'.$i] : 0),
                    ];
                }
            }

            if(isset($reqdata3))
            { 
                $wrapped_links3["CCD"] = $reqdata3; 
                $XMLCCD = ArrayToXml::convert($wrapped_links3);
            }
            else
            {
                $XMLCCD = NULL; 
            }
            // dd($XMLCCD);
            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'ADD';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF = Session::get('BRID_REF');
            $FYID_REF = Session::get('FYID_REF');
            $JV_NO = $request['JV_NO'];
            $JV_DT = $request['JV_DT'];
            $REVERSE = ((isset($request['REVERSE']) && $request['REVERSE'] == 'on') ? 1 : 0 );
            $REVERSE_DT = $request['REVERSE_DT'];
            $REMARKS = $request['REMARKS'];
            $SOURCE_DOCNO = $request['SOURCE_DOCNO'];
            $SOURCE_DOCDT = $request['SOURCE_DOCDT'];
            $NARRATION = $request['NARRATION'];
           

            $log_data = [ 
                $JV_NO, $JV_DT, $REVERSE, $REVERSE_DT,$REMARKS,$SOURCE_DOCNO,$SOURCE_DOCDT,$NARRATION, 
                $CYID_REF, $BRID_REF, $FYID_REF, $VTID_REF,$XMLACC,$XMLCCD, $XMLUDF, $USERID, Date('Y-m-d'), 
                Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];

            //dd($log_data);
           


            
            $sp_result = DB::select('EXEC SP_JV_IN ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
           // dd($sp_result);
            
        
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
       
            if(!is_null($id)){

                $objJV = DB::table('TBL_TRN_FJRV01_HDR')
                        ->where('TBL_TRN_FJRV01_HDR.FYID_REF','=',$FYID_REF)
                        ->where('TBL_TRN_FJRV01_HDR.CYID_REF','=',$CYID_REF)
                        ->where('TBL_TRN_FJRV01_HDR.BRID_REF','=',$BRID_REF)
                        ->where('TBL_TRN_FJRV01_HDR.JVID','=',$id)
                        ->select('TBL_TRN_FJRV01_HDR.*')
                        ->first();

                $log_data = [ 
                    $id
                ];
                
                $objJVACC = DB::select('EXEC SP_GET_JV_ACCOUNTS ?', $log_data);
                            
                                // DD($objSEMAT); 
                $objCount1 = count($objJVACC);  
                
                $objJVUDF = DB::table('TBL_TRN_FJRV01_UDF')                    
                                ->where('TBL_TRN_FJRV01_UDF.JVID_REF','=',$id)
                                ->get()->toArray();
                
                $objCount2 = count($objJVUDF);

                $objJVCCD = DB::table('TBL_TRN_FJRV01_CCD')                    
                                ->where('TBL_TRN_FJRV01_CCD.JVID_REF','=',$id)
                                ->get()->toArray();
                
                $objCount3 = count($objJVCCD);
        
                $objRights = DB::table('TBL_MST_USERROLMAP')
                                ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                                ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                                ->where('TBL_MST_USERROLMAP.BRID_REF','=',Auth::user()->BRID_REF)
                              
                                ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                                ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                                ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                                ->first();
                                $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_JV")->select('*')
                                ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                            {       
                                            $query->select('UDFJVID')->from('TBL_MST_UDFFOR_JV')
                                                            ->where('STATUS','=','A')
                                                            ->where('PARENTID','=',0)
                                                            ->where('DEACTIVATED','=',0)
                                                            ->where('CYID_REF','=',$CYID_REF);
                                                                              
                                })->where('DEACTIVATED','=',0)
                                ->where('STATUS','<>','C')                    
                                ->where('CYID_REF','=',$CYID_REF);
                                              
                                
            
                    $objUdfJVData = DB::table('TBL_MST_UDFFOR_JV')
                        ->where('STATUS','=','A')
                        ->where('PARENTID','=',0)
                        ->where('DEACTIVATED','=',0)
                        ->where('CYID_REF','=',$CYID_REF)
                       
                        ->union($ObjUnionUDF)
                        ->get()->toArray();   
                    $objCountUDF = count($objUdfJVData);

                    $objlastJVDT = DB::select('SELECT MAX(JV_DT) JV_DT FROM TBL_TRN_FJRV01_HDR  
                    WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
                    [$CYID_REF, $BRID_REF,  169, 'A' ]);

                    $objCostCenter = DB::table('TBL_MST_COSTCENTER')
                    ->where('CYID_REF','=',$CYID_REF)
                    ->where('STATUS','=','A')
                    ->select('TBL_MST_COSTCENTER.*')
                    ->get();

                    $ActionStatus   =   "";

            return view('transactions.Accounts.JournalVoucher.trnfrm169edit',compact(['objJV','objRights','objCount1',
            'objJVACC','objJVUDF','objCount2','objUdfJVData','objCountUDF','objlastJVDT','objJVCCD','objCount3','objCostCenter','ActionStatus']));
            }
     
       }

    public function amendment($id){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
		$FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
       // DD($CYID_REF.$BRID_REF.$FYID_REF.$id);
        
        if(!is_null($id))
        {
            $objJV = DB::table('TBL_TRN_FJRV01_HDR')
                    ->where('TBL_TRN_FJRV01_HDR.FYID_REF','=',$FYID_REF)
                    ->where('TBL_TRN_FJRV01_HDR.CYID_REF','=',$CYID_REF)
                    ->where('TBL_TRN_FJRV01_HDR.BRID_REF','=',$BRID_REF)
                    ->where('TBL_TRN_FJRV01_HDR.JVID','=',$id)
                    ->select('TBL_TRN_FJRV01_HDR.*')
                    ->first();

            $log_data = [ 
                $id
            ];
            
            $objJVACC = DB::select('EXEC SP_GET_JV_ACCOUNTS ?', $log_data);
                           
                            // DD($objSEMAT); 
            $objCount1 = count($objJVACC);  
            
            $objJVUDF = DB::table('TBL_TRN_FJRV01_UDF')                    
                             ->where('TBL_TRN_FJRV01_UDF.JVID_REF','=',$id)
                             ->get()->toArray();
            
            $objCount2 = count($objJVUDF);
     
            $objRights = DB::table('TBL_MST_USERROLMAP')
                             ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                             ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                             ->where('TBL_MST_USERROLMAP.BRID_REF','=',Auth::user()->BRID_REF)
                             ->where('TBL_MST_USERROLMAP.FYID_REF','=',Auth::user()->FYID_REF)
                             ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                             ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                             ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                             ->first();
                             $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_JV")->select('*')
                             ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                         {       
                                         $query->select('UDFJVID')->from('TBL_MST_UDFFOR_JV')
                                                         ->where('STATUS','=','A')
                                                         ->where('PARENTID','=',0)
                                                         ->where('DEACTIVATED','=',0)
                                                         ->where('CYID_REF','=',$CYID_REF)
                                                         ->where('BRID_REF','=',$BRID_REF)
                                                         ->where('FYID_REF','=',$FYID_REF);                       
                             })->where('DEACTIVATED','=',0)
                             ->where('STATUS','<>','C')                    
                             ->where('CYID_REF','=',$CYID_REF)
                             ->where('BRID_REF','=',$BRID_REF)
                             ->where('FYID_REF','=',$FYID_REF) ;                   
                            
         
                 $objUdfJVData = DB::table('TBL_MST_UDFFOR_JV')
                     ->where('STATUS','=','A')
                     ->where('PARENTID','=',0)
                     ->where('DEACTIVATED','=',0)
                     ->where('CYID_REF','=',$CYID_REF)
                     ->where('BRID_REF','=',$BRID_REF)
                     ->where('FYID_REF','=',$FYID_REF)
                     ->union($ObjUnionUDF)
                     ->get()->toArray();   
                 $objCountUDF = count($objUdfJVData);

                 $objlastJVDT = DB::select('SELECT MAX(JV_DT) JV_DT FROM TBL_TRN_FJRV01_HDR  
                WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
                [$CYID_REF, $BRID_REF,  169, 'A' ]);

        return view('transactions.Accounts.JournalVoucher.trnfrm169edit',compact(['objJV','objRights','objCount1',
           'objJVACC','objJVUDF','objCount2','objUdfJVData','objCountUDF','objlastJVDT']));
        }
     
       }
     
       public function view($id=NULL){
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
		$FYID_REF   =   Session::get('FYID_REF'); 
        $Status     =   'A';
   
        if(!is_null($id)){

            $objJV = DB::table('TBL_TRN_FJRV01_HDR')
                    ->where('TBL_TRN_FJRV01_HDR.FYID_REF','=',$FYID_REF)
                    ->where('TBL_TRN_FJRV01_HDR.CYID_REF','=',$CYID_REF)
                    ->where('TBL_TRN_FJRV01_HDR.BRID_REF','=',$BRID_REF)
                    ->where('TBL_TRN_FJRV01_HDR.JVID','=',$id)
                    ->select('TBL_TRN_FJRV01_HDR.*')
                    ->first();
			//dd($objJV);
            $log_data = [ 
                $id
            ];
            
            $objJVACC = DB::select('EXEC SP_GET_JV_ACCOUNTS ?', $log_data);
                        
                            // DD($objSEMAT); 
            $objCount1 = count($objJVACC);  
            
            $objJVUDF = DB::table('TBL_TRN_FJRV01_UDF')                    
                            ->where('TBL_TRN_FJRV01_UDF.JVID_REF','=',$id)
                            ->get()->toArray();
            
            $objCount2 = count($objJVUDF);

            $objJVCCD = DB::table('TBL_TRN_FJRV01_CCD')                    
                            ->where('TBL_TRN_FJRV01_CCD.JVID_REF','=',$id)
                            ->get()->toArray();
            
            $objCount3 = count($objJVCCD);
    
            $objRights = DB::table('TBL_MST_USERROLMAP')
                            ->where('TBL_MST_USERROLMAP.USERID_REF','=',Auth::user()->USERID)
                            ->where('TBL_MST_USERROLMAP.CYID_REF','=',Auth::user()->CYID_REF)
                            ->where('TBL_MST_USERROLMAP.BRID_REF','=',Auth::user()->BRID_REF)
                          
                            ->leftJoin('TBL_MST_ROLEDETAILS', 'TBL_MST_USERROLMAP.ROLLID_REF','=','TBL_MST_ROLEDETAILS.ROLLID_REF')
                            ->where('TBL_MST_ROLEDETAILS.VTID_REF','=',$this->vtid_ref)
                            ->select('TBL_MST_USERROLMAP.*', 'TBL_MST_ROLEDETAILS.*')
                            ->first();
                            $ObjUnionUDF = DB::table("TBL_MST_UDFFOR_JV")->select('*')
                            ->whereIn('PARENTID',function($query) use ($CYID_REF,$BRID_REF,$FYID_REF)
                                        {       
                                        $query->select('UDFJVID')->from('TBL_MST_UDFFOR_JV')
                                                        ->where('STATUS','=','A')
                                                        ->where('PARENTID','=',0)
                                                        ->where('DEACTIVATED','=',0)
                                                        ->where('CYID_REF','=',$CYID_REF);
                                                                          
                            })->where('DEACTIVATED','=',0)
                            ->where('STATUS','<>','C')                    
                            ->where('CYID_REF','=',$CYID_REF);
                                          
                            
        
                $objUdfJVData = DB::table('TBL_MST_UDFFOR_JV')
                    ->where('STATUS','=','A')
                    ->where('PARENTID','=',0)
                    ->where('DEACTIVATED','=',0)
                    ->where('CYID_REF','=',$CYID_REF)
                   
                    ->union($ObjUnionUDF)
                    ->get()->toArray();   
                $objCountUDF = count($objUdfJVData);

                $objlastJVDT = DB::select('SELECT MAX(JV_DT) JV_DT FROM TBL_TRN_FJRV01_HDR  
                WHERE  CYID_REF = ? AND BRID_REF = ?   AND VTID_REF = ? AND STATUS = ?', 
                [$CYID_REF, $BRID_REF,  169, 'A' ]);

                $objCostCenter = DB::table('TBL_MST_COSTCENTER')
                ->where('CYID_REF','=',$CYID_REF)
                ->where('STATUS','=','A')
                ->select('TBL_MST_COSTCENTER.*')
                ->get();

                $ActionStatus   =   "disabled";

        return view('transactions.Accounts.JournalVoucher.trnfrm169view',compact(['objJV','objRights','objCount1',
        'objJVACC','objJVUDF','objCount2','objUdfJVData','objCountUDF','objlastJVDT','objJVCCD','objCount3','objCostCenter','ActionStatus']));
        }
 
   }

    //update the data
   public function saveamendment(Request $request){
     
        
        exit();   
    }



    public function update(Request $request){
        //   dd($request->all());
          
        $r_count1 = $request['Row_Count1'];
        $r_count2 = $request['Row_Count2'];
        $r_count3 = $request['Row_Count3'];

            for ($i=0; $i<=$r_count1; $i++)
            {
                if(isset($request['GLID_REF_'.$i]))
                {
                    $req_data[$i] = [
                        'GLID_REF'          => $request['GLID_REF_'.$i],
                        'SGLID_REF'         => $request['txtflag_'.$i],
                        'DR_AMT'            => (!is_null($request['DR_AMT_'.$i]) ? $request['DR_AMT_'.$i] : 0),
                        'CR_AMT'            => (!is_null($request['CR_AMT_'.$i]) ? $request['CR_AMT_'.$i] : 0),
                        'NARRATION'         => $request['NARRATION_'.$i],
                        'CCID_REF'          => $request['CCID_REF_'.$i],
                    ];
                }
            }
        
            $wrapped_links["ACCOUNT"] = $req_data; 
            $XMLACC = ArrayToXml::convert($wrapped_links);

            for ($i=0; $i<=$r_count2; $i++)
            {
                if(isset($request['UDFJVID_REF_'.$i]) && !is_null($request['UDFJVID_REF_'.$i]))
                {
                    $reqdata2[$i] = [
                        'UDFJVID_REF'   => $request['UDFJVID_REF_'.$i],
                        'VALUE'         => $request['udfvalue_'.$i],
                    ];
                }
            }

            if(isset($reqdata2))
            { 
                $wrapped_links2["UDF"] = $reqdata2; 
                $XMLUDF = ArrayToXml::convert($wrapped_links2);
            }
            else
            {
                $XMLUDF = NULL; 
            }

            for ($i=0; $i<=$r_count3; $i++)
            {
                if(isset($request['GLID_'.$i]) && !is_null($request['GLID_'.$i]))
                {
                    $reqdata3[$i] = [
                        'GLID_REF'   => $request['GLID_'.$i],
                        'CCID_REF'   => $request['CCID_'.$i],
                        'DR_AMT'     => (!is_null($request['D_AMT_'.$i]) ? $request['D_AMT_'.$i] : 0),
                        'CR_AMT'     => (!is_null($request['C_AMT_'.$i]) ? $request['C_AMT_'.$i] : 0),
                    ];
                }
            }

            if(isset($reqdata3))
            { 
                $wrapped_links3["CCD"] = $reqdata3; 
                $XMLCCD = ArrayToXml::convert($wrapped_links3);
            }
            else
            {
                $XMLCCD = NULL; 
            }

            $VTID_REF     =   $this->vtid_ref;
            $VID = 0;
            $USERID = Auth::user()->USERID;   
            $ACTIONNAME = 'EDIT';
            $IPADDRESS = $request->getClientIp();
            $CYID_REF = Auth::user()->CYID_REF;
            $BRID_REF   =   Session::get('BRID_REF');
			$FYID_REF   =   Session::get('FYID_REF'); 
            $JV_NO = $request['JV_NO'];
            $JV_DT = $request['JV_DT'];
            $REVERSE =  ((isset($request['REVERSE']) && $request['REVERSE'] == 'on') ? 1 : 0 );
            $REVERSE_DT = $request['REVERSE_DT'];
            $REMARKS = $request['REMARKS'];
            $SOURCE_DOCNO = $request['SOURCE_DOCNO'];
            $SOURCE_DOCDT = $request['SOURCE_DOCDT'];
            $NARRATION = $request['NARRATION'];
           

            $log_data = [ 
                $JV_NO, $JV_DT, $REVERSE, $REVERSE_DT,$REMARKS,$SOURCE_DOCNO,$SOURCE_DOCDT,$NARRATION, 
                $CYID_REF, $BRID_REF, $FYID_REF, $VTID_REF,$XMLACC, $XMLCCD, $XMLUDF, $USERID, Date('Y-m-d'), 
                Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
            ];

            //dd($log_data);
           


            
            $sp_result = DB::select('EXEC SP_JV_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
           // dd($sp_result);
            
        
           $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
           if($contains){
               return Response::json(['success' =>true,'msg' => $JV_NO. ' Sucessfully Updated.']);
           
           }else{
               return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
           }
            exit();   
      }

    //update the data

    public function Approve(Request $request){
        // dd($request->all());
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
                foreach ($sp_listing_result as $key=>$journalvoucheritem)
            {  
                $record_status = 0;
                $Approvallevel = "APPROVAL".$journalvoucheritem->LAVELS;
            }
            }

          
            $r_count1 = $request['Row_Count1'];
            $r_count2 = $request['Row_Count2'];
            $r_count3 = $request['Row_Count3'];

                for ($i=0; $i<=$r_count1; $i++)
                {
                    if(isset($request['GLID_REF_'.$i]))
                    {
                        $req_data[$i] = [
                            'GLID_REF'          => $request['GLID_REF_'.$i],
                            'SGLID_REF'         => $request['txtflag_'.$i],
                            'DR_AMT'            => (!is_null($request['DR_AMT_'.$i]) ? $request['DR_AMT_'.$i] : 0),
                            'CR_AMT'            => (!is_null($request['CR_AMT_'.$i]) ? $request['CR_AMT_'.$i] : 0),
                            'NARRATION'         => $request['NARRATION_'.$i],
                            'CCID_REF'          => $request['CCID_REF_'.$i],
                        ];
                    }
                }
            
                $wrapped_links["ACCOUNT"] = $req_data; 
                $XMLACC = ArrayToXml::convert($wrapped_links);

                for ($i=0; $i<=$r_count2; $i++)
                {
                    if(isset($request['UDFJVID_REF_'.$i]) && !is_null($request['UDFJVID_REF_'.$i]))
                    {
                        $reqdata2[$i] = [
                            'UDFJVID_REF'   => $request['UDFJVID_REF_'.$i],
                            'VALUE'         => $request['udfvalue_'.$i],
                        ];
                    }
                }

                if(isset($reqdata2))
                { 
                    $wrapped_links2["UDF"] = $reqdata2; 
                    $XMLUDF = ArrayToXml::convert($wrapped_links2);
                }
                else
                {
                    $XMLUDF = NULL; 
                }

                for ($i=0; $i<=$r_count3; $i++)
                {
                    if(isset($request['GLID_'.$i]) && !is_null($request['GLID_'.$i]))
                    {
                        $reqdata3[$i] = [
                            'GLID_REF'   => $request['GLID_'.$i],
                            'CCID_REF'   => $request['CCID_'.$i],
                            'DR_AMT'     => (!is_null($request['D_AMT_'.$i]) ? $request['D_AMT_'.$i] : 0),
                            'CR_AMT'     => (!is_null($request['C_AMT_'.$i]) ? $request['C_AMT_'.$i] : 0),
                        ];
                    }
                }

                if(isset($reqdata3))
                { 
                    $wrapped_links3["CCD"] = $reqdata3; 
                    $XMLCCD = ArrayToXml::convert($wrapped_links3);
                }
                else
                {
                    $XMLCCD = NULL; 
                }
    
                $VTID_REF     =   $this->vtid_ref;
                $VID = 0;
                $USERID = Auth::user()->USERID;   
                $ACTIONNAME = $Approvallevel;
                $IPADDRESS = $request->getClientIp();
                $CYID_REF = Auth::user()->CYID_REF;
                $BRID_REF   =   Session::get('BRID_REF');
				$FYID_REF   =   Session::get('FYID_REF'); 
                $JV_NO = $request['JV_NO'];
                $JV_DT = $request['JV_DT'];
                $REVERSE = ((isset($request['REVERSE']) && $request['REVERSE'] == 'on') ? 1 : 0 );
                $REVERSE_DT = $request['REVERSE_DT'];
                $REMARKS = $request['REMARKS'];
                $SOURCE_DOCNO = $request['SOURCE_DOCNO'];
                $SOURCE_DOCDT = $request['SOURCE_DOCDT'];
                $NARRATION = $request['NARRATION'];
               
    
                $log_data = [ 
                    $JV_NO, $JV_DT, $REVERSE, $REVERSE_DT,$REMARKS,$SOURCE_DOCNO,$SOURCE_DOCDT,$NARRATION, 
                    $CYID_REF, $BRID_REF, $FYID_REF, $VTID_REF,$XMLACC, $XMLCCD, $XMLUDF, $USERID, Date('Y-m-d'), 
                    Date('h:i:s.u'),$ACTIONNAME, $IPADDRESS
                ];
    
                //dd($log_data);
               
    
    
                
                $sp_result = DB::select('EXEC SP_JV_UP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $log_data);       
               // dd($sp_result);
                
            
               $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
               if($contains){
                   return Response::json(['success' =>true,'msg' => $JV_NO. ' Sucessfully Approved.']);
               
               }else{
                   return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
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
                    foreach ($sp_listing_result as $key=>$journalvoucheritem)
                    {  
                        $record_status = 0;
                        $Approvallevel = "APPROVAL".$journalvoucheritem->LAVELS;
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
                $TABLE      =   "TBL_TRN_FJRV01_HDR";
                $FIELD      =   "JVID";
                $ACTIONNAME     = $Approvallevel;
                $UPDATE     =   Date('Y-m-d');
                $UPTIME     =   Date('h:i:s.u');
                $IPADDRESS  =   $request->getClientIp();
            
            
            
            // dd($xml);
            
            $log_data = [ 
                $USERID_REF, $VTID_REF, $TABLE, $FIELD, $xml, $ACTIONNAME, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS
            ];
    
                
            $sp_result = DB::select('EXEC SP_TRN_MULTIAPPROVAL_JV ?,?,?,?,?,?,?,?,?,?,?,?',  $log_data);       
            
            
    
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
    //  dd($request->{0});  

   //save data
        $id = $request->{0};

        $USERID_REF =   Auth::user()->USERID;
        $VTID_REF   =   $this->vtid_ref;  //voucher type id
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');       
        $TABLE      =   "TBL_TRN_FJRV01_HDR";
        $FIELD      =   "JVID";
        $ID         =   $id;
        $UPDATE     =   Date('Y-m-d');
        $UPTIME     =   Date('h:i:s.u');
        $IPADDRESS  =   $request->getClientIp();

        $req_data[0]=[
         'NT'  => 'TBL_TRN_FJRV01_HDR',
        ];
   
        $req_data[1]=[
         'NT'  => 'TBL_TRN_FJRV01_ACC',
        ];

        $req_data[2]=[
            'NT'  => 'TBL_TRN_FJRV01_CCD',
        ];

        $req_data[3]=[
            'NT'  => 'TBL_TRN_FJRV01_UDF',
        ];
   
        
        $wrapped_links["TABLES"] = $req_data; 
        $XMLTAB = ArrayToXml::convert($wrapped_links);
        
        $journalvoucher_cancel_data = [ $USERID_REF, $VTID_REF, $TABLE, $FIELD, $ID, $CYID_REF, $BRID_REF,$FYID_REF,$UPDATE,$UPTIME, $IPADDRESS,$XMLTAB ];

        $sp_result = DB::select('EXEC SP_TRN_CANCEL_JV  ?,?,?,?, ?,?,?,?, ?,?,?,?', $journalvoucher_cancel_data);
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
    
    $image_path         =   "docs/company".$CYID_REF."/JournalVoucher";     
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
        return redirect()->route("transaction",[169,"attachment",$ATTACH_DOCNO])->with("success","Already exists. No file uploaded");
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
    
       return redirect()->route("transaction",[169,"attachment",$ATTACH_DOCNO])->with("error","There is some error. Please try after sometime");

   }
 
    if($sp_result[0]->RESULT=="SUCCESS"){

        if(trim($duplicate_files!="")){
            $duplicate_files =  " System ignored duplicated files -  ".$duplicate_files;
        }

        if(trim($invlid_files!="")){
            $invlid_files =  " Invalid files -  ".$invlid_files;
        }

        return redirect()->route("transaction",[169,"attachment",$ATTACH_DOCNO])->with("success","Files successfully attached. ".$duplicate_files.$invlid_files);


    }        elseif($sp_result[0]->RESULT=="Duplicate file for same records"){
   
        return redirect()->route("transaction",[169,"attachment",$ATTACH_DOCNO])->with("success","Duplicate file name. ".$invlid_files);

    }else{

        return redirect()->route("transaction",[169,"attachment",$ATTACH_DOCNO])->with($sp_result[0]->RESULT);
    }
  
    
}

    public function codeduplicate(Request $request){

        $JV_NO  =   trim($request['JV_NO']);
        $objLabel = DB::table('TBL_TRN_FJRV01_HDR')
        ->where('CYID_REF','=',Auth::user()->CYID_REF)
        ->where('BRID_REF','=',Session::get('BRID_REF'))
        ->where('FYID_REF','=',Session::get('FYID_REF'))
        ->where('JV_NO','=',$JV_NO)
        ->select('JVID')->first();

        if($objLabel){  
            return Response::json(['exists' =>true,'msg' => 'Duplicate No']);
        }else{
            return Response::json(['not exists'=>true,'msg' => 'Ok']);
        }
        
        exit();
    }
    
}
