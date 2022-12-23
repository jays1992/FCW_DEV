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

class TrnFrm384Controller extends Controller{
    protected $form_id  =   384;
    protected $vtid_ref =   470;
    protected $view     =   "transactions.Common.ManualTransactionClose.trnfrm";
   
    protected   $messages = [
        'LABEL.unique' => 'Duplicate Code'
    ];

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){  
        
        $objRights  =   DB::table('TBL_MST_USERROLMAP')
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
        
        $REQUEST_DATA   =   array(
            'FORMID'    =>  $this->form_id,
            'VTID_REF'  =>  $this->vtid_ref,
            'USERID'    =>  Auth::user()->USERID,
            'CYID_REF'  =>  Auth::user()->CYID_REF,
            'BRID_REF'  =>  Session::get('BRID_REF'),
            'FYID_REF'  =>  Session::get('FYID_REF'),
        );


        return view($this->view.$FormId,compact(['REQUEST_DATA','objRights','FormId']));
    }


    public function getTransactionNo(Request $request) {    

        $Status     =   "A";
        $CYID_REF   =   Auth::user()->CYID_REF;
        $BRID_REF   =   Session::get('BRID_REF');
        $FYID_REF   =   Session::get('FYID_REF');
        $TYPE       =   $request['tran_type'];
 
        if($TYPE == 'PO'){
            $objtran    =   DB::select("SELECT T1.POID,T1.PO_NO,T1.PO_DT,T2.VCODE,T2.NAME,T1.VENDOR_REF_NO as ReferenceNo
                            FROM TBL_TRN_PROR01_HDR T1
                            LEFT JOIN TBL_MST_VENDOR T2 ON T1.VID_REF=T2.SLID_REF
                            WHERE T1.CYID_REF='$CYID_REF' 
                            AND T1.BRID_REF='$BRID_REF' 
                            AND T1.FYID_REF='$FYID_REF' 
                            AND T1.STATUS='A'
                            AND (SELECT SUM(PENDING_QTY) AS PENDING_QTY FROM TBL_TRN_PROR01_MAT WHERE POID_REF=POID) > 0 ");
        }
		else if($TYPE == 'IPO'){
            $objtran    =    DB::select("SELECT T1.IPO_ID as POID,T1.IPO_NO AS PO_NO,T1.IPO_DT AS PO_DT,T2.VCODE,T2.NAME,T1.SALE_ORDER_NO as ReferenceNo 
                            FROM TBL_TRN_IPO_HDR T1
                            LEFT JOIN TBL_MST_VENDOR T2 ON T1.VID_REF=T2.SLID_REF
                            WHERE T1.CYID_REF='$CYID_REF' 
                            AND T1.BRID_REF='$BRID_REF' 
                            AND T1.FYID_REF='$FYID_REF' 
                            AND T1.STATUS='A'
                            AND (SELECT SUM(PENDING_QTY) AS PENDING_QTY FROM TBL_TRN_IPO_MAT WHERE IPO_ID_REF=T1.IPO_ID) > 0 ");
        }
		else if($TYPE == 'SPO'){
            $objtran    =    DB::select("SELECT T1.SPOID as POID,T1.SPO_NO AS PO_NO,T1.SPO_DT AS PO_DT,T2.VCODE,T2.NAME,T1.VENDOR_REFNO as ReferenceNo
                            FROM TBL_TRN_PROR04_HDR T1
                            LEFT JOIN TBL_MST_VENDOR T2 ON T1.VID_REF=T2.SLID_REF
                            WHERE T1.CYID_REF='$CYID_REF' 
                            AND T1.BRID_REF='$BRID_REF' 
                            AND T1.FYID_REF='$FYID_REF' 
                            AND T1.STATUS='A'");
        }
		else if($TYPE == 'BPO'){
            $objtran    =    DB::select("SELECT T1.BPOID as POID,T1.BPO_NO AS PO_NO,T1.BPO_DT AS PO_DT,T2.VCODE,T2.NAME, NULL as ReferenceNo
                            FROM TBL_TRN_PROR03_HDR T1
                            LEFT JOIN TBL_MST_VENDOR T2 ON T1.VID_REF=T2.SLID_REF
                            WHERE T1.CYID_REF='$CYID_REF' 
                            AND T1.BRID_REF='$BRID_REF' 
                            AND T1.FYID_REF='$FYID_REF' 
                            AND T1.STATUS='A'");
        }
		else if($TYPE == 'SO'){
            $objtran    =   DB::select("SELECT T1.SOID AS POID,T1.SONO AS PO_NO,T1.SODT AS PO_DT,T2.CCODE AS VCODE,T2.NAME,CUSTOMERPONO as ReferenceNo
                            FROM TBL_TRN_SLSO01_HDR T1
                            LEFT JOIN TBL_MST_CUSTOMER T2 ON T1.SLID_REF=T2.SLID_REF
                            WHERE T1.CYID_REF='$CYID_REF' 
                            AND T1.BRID_REF='$BRID_REF' 
                            AND T1.FYID_REF='$FYID_REF' 
                            AND T1.STATUS='A'
                            AND (SELECT SUM(PENDING_QTY) AS PENDING_QTY FROM TBL_TRN_SLSO01_MAT WHERE SOID_REF=T1.SOID) > 0 ");
        }
		else if($TYPE == 'SSO'){
            $objtran    =   DB::select("SELECT T1.SSOID AS POID,T1.SSO_NO AS PO_NO,T1.SSO_DT AS PO_DT,T2.CCODE AS VCODE,T2.NAME,CUSTOMER_ONO as ReferenceNo
                            FROM TBL_TRN_SLSO04_HDR T1
                            LEFT JOIN TBL_MST_CUSTOMER T2 ON T1.SLID_REF=T2.SLID_REF
                            WHERE T1.CYID_REF='$CYID_REF' 
                            AND T1.BRID_REF='$BRID_REF' 
                            AND T1.FYID_REF='$FYID_REF' 
                            AND T1.STATUS='A'
                            AND (SELECT SUM(PENDING_QTY) AS PENDING_QTY FROM TBL_TRN_SLSO04_MAT WHERE SSOID_REF=T1.SSOID) > 0 ");
        }
		else if($TYPE == 'OSO'){
            $objtran    =   DB::select("SELECT T1.OSOID AS POID,T1.OSONO AS PO_NO,T1.OSODT AS PO_DT,T2.CCODE AS VCODE,T2.NAME,CUSTOMERPONO as ReferenceNo
                            FROM TBL_TRN_SLSO03_HDR T1
                            LEFT JOIN TBL_MST_CUSTOMER T2 ON T1.SLID_REF=T2.SLID_REF
                            WHERE T1.CYID_REF='$CYID_REF' 
                            AND T1.BRID_REF='$BRID_REF' 
                            AND T1.FYID_REF='$FYID_REF' 
                            AND T1.STATUS='A'");
        }
        else{
            $objtran    =   DB::select("SELECT T1.SOID AS POID,T1.SONO AS PO_NO,T1.SODT AS PO_DT,T2.CCODE AS VCODE,T2.NAME,CUSTOMERPONO as ReferenceNo
                            FROM TBL_TRN_SLSO01_HDR T1
                            LEFT JOIN TBL_MST_CUSTOMER T2 ON T1.SLID_REF=T2.SLID_REF
                            WHERE T1.CYID_REF='$CYID_REF' 
                            AND T1.BRID_REF='$BRID_REF' 
                            AND T1.FYID_REF='$FYID_REF' 
                            AND T1.STATUS='A'
                            AND (SELECT SUM(PENDING_QTY) AS PENDING_QTY FROM TBL_TRN_SLSO01_MAT WHERE SOID_REF=T1.SOID) > 0 ");
        }

        if(!empty($objtran)){        
            foreach ($objtran as $index=>$dataRow){

                $row = '';
                $row = $row.'<tr ><td style="width:10%;text-align:center;">';
                $row = $row.'<input type="checkbox" name="machine[]"  id="pocode_'.$dataRow->POID.'" class="clstranid" value="'.$dataRow->POID.'"/>             
                </td> <td style="width:20%">'.$dataRow->PO_NO;
                $row = $row.'<input type="hidden" id="txtpocode_'.$dataRow->POID.'"  data-desc="'.$dataRow->PO_NO.'" value="'.$dataRow->POID.'"/></td>
    
                <td style="width:20%">'.$dataRow->PO_DT.'</td><td style="width:30%">'.$dataRow->VCODE.'-'.$dataRow->NAME.'</td>
				<td style="width:20%">'.$dataRow->ReferenceNo.'</td>
    
            </tr>';
                echo $row;
            }
    
            }else{
                echo '<tr><td colspan="2">Record not found.</td></tr>';
            }
    
            exit();
    
    }


    
    
    


    public function save(Request $request) {    
       
           $VTID_REF     =   $this->vtid_ref;
           $VID = 0;
           $USERID = Auth::user()->USERID;   
           $ACTIONNAME = 'CLOSE';
           $IPADDRESS = $request->getClientIp();
           $CYID_REF = Auth::user()->CYID_REF;
           $BRID_REF = Session::get('BRID_REF');
           $FYID_REF = Session::get('FYID_REF');

           $tran_type   =   $request['tran_type'];
           $TransID_REF =   $request['TransID_REF'];
           $REMARKS     =   $request['REMARKS'];

     
        $log_data = [$tran_type,$TransID_REF,$CYID_REF,$BRID_REF,$FYID_REF,$USERID,Date('Y-m-d'), Date('h:i:s.u'),
        $ACTIONNAME,$IPADDRESS,$REMARKS];

        

        $sp_result = DB::select('EXEC SP_TRN_MANUAL_CLOSE_TRAN ?,?,?,?,?, ?,?,?,?,?,?', $log_data); 
    
        $contains = Str::contains($sp_result[0]->RESULT, 'SUCCESS');
    
        if($contains){
            return Response::json(['success' =>true,'msg' => $sp_result[0]->RESULT]);

        }else{
            return Response::json(['errors'=>true,'msg' =>  $sp_result[0]->RESULT]);
        }

        exit();   
    }

    
    

    
}
