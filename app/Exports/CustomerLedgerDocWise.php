<?php

namespace App\Exports;
use DB;
use Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Admin\TblMstUser;


use Session;
use Response;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Chartblocks;
use App\Exports\CustomerLedgerDocWise;
use Maatwebsite\Excel\Facades\Excel;














class CustomerLedgerDocWise implements FromCollection, WithHeadings
{


 function __construct($SGLID,$CUSTOMERGROUP,$From_Date,$To_Date,$BranchGroup,$BranchName,$CYID) {
        $this->SGLID = $SGLID;
        $this->CUSTOMERGROUP = $CUSTOMERGROUP;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;   
        $this->CYID = $CYID;
 }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        //dd($this->From_Date); 
        
       
        $SGLID=implode(",",$this->SGLID);
        $CUSTOMERGROUP=implode(",",$this->CUSTOMERGROUP);
        $BranchName=implode(",",$this->BranchName);      
       
       



      return collect( $data=DB::select("	   SELECT
      H.SOURCE_DOCTYPE,
      H.JV_NO,
      H.JV_DT,
      H.NARRATION, 
	  B.DR_AMT,B.CR_AMT,
      H.SOURCE_DOCNO,
      H.SOURCE_DOCDT,
      
      case when H.NARRATION = 'SALES RETURN' then ISNULL(B.CR_AMT,0) 
         when H.NARRATION = 'AR CREDIT' then ISNULL(B.CR_AMT,0)
         when H.NARRATION = 'RECEIPT FOR Customer' then ISNULL(B.CR_AMT,0) else ISNULL(B.DR_AMT,0) end AS SOURCE_DOCAMT
      
      FROM 
      TBL_TRN_FJRV01_ACC A LEFT JOIN TBL_TRN_FJRV01_HDR H ON A.JVID_REF=H.JVID
      LEFT JOIN TBL_MST_BRANCH AS BR WITH (NOLOCK) ON H.BRID_REF=BR.BRID
      LEFT JOIN TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID 
      LEFT JOIN TBL_MST_SUBLEDGER S ON A.GLID_REF=S.SGLID AND A.SGLID_REF='S'
      LEFT JOIN TBL_MST_CUSTOMER C ON C.SLID_REF=S.SGLID 
      LEFT JOIN TBL_MST_CUSTOMERGROUP D ON D.CGID=C.CGID_REF
      LEFT JOIN TBL_TRN_FJRV01_ACC B ON B.JVID_REF=H.JVID AND B.NARRATION = H.NARRATION
      
      WHERE A.SGLID_REF='S' AND H.SOURCE_DOCTYPE IN ('AR DEBIT CREDIT NOTE','SALES INVOICE','SALES SERVICE INVOICE','SALES RETURN','CSV','RECEIPT','PAYMENT')
      AND H.JV_DT BETWEEN '$this->From_Date' AND '$this->To_Date' and H.CYID_REF = $this->CYID and H.BRID_REF in ($BranchName) and C.Status = 'A' and (C.DEACTIVATED = 0 or C.DEACTIVATED IS NULL)
      and C.CGID_REF in ($CUSTOMERGROUP)
      and S.SGLID in ($SGLID)
      
      --ORDER BY C.CID,C.CCODE,C.SLID_REF,H.JV_NO, H.JV_DT,H.SOURCE_DOCNO,H.SOURCE_DOCDT
      
      union
      SELECT '' AS SOURCE_DOCTYPE,'' AS JV_NO, '' AS JV_DT,'' AS NARRATION,0 AS DR_AMT,0 AS CR_AMT, '' AS SOURCE_DOCNO,'' AS SOURCE_DOCDT, 0 AS SOURCE_DOCAMT
      
      FROM TBL_MST_SLOPENING_LEDGER O WITH (NOLOCK)
      LEFT JOIN TBL_MST_BRANCH AS BR WITH (NOLOCK) ON O.BRID_REF=BR.BRID
      LEFT JOIN TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID 
      LEFT JOIN TBL_MST_SUBLEDGER S ON O.SGLID_REF=S.SGLID 
      LEFT JOIN TBL_MST_CUSTOMER C ON C.SLID_REF=S.SGLID 
      LEFT JOIN TBL_MST_CUSTOMERGROUP D ON D.CGID=C.CGID_REF
      
      WHERE O.SGLID_REF NOT IN (SELECT GLID_REF FROM  TBL_TRN_FJRV01_ACC A LEFT JOIN TBL_TRN_FJRV01_HDR H ON A.JVID_REF=H.JVID 
      WHERE  A.SGLID_REF='S' AND H.CYID_REF=S.CYID_REF AND H.BRID_REF = S.BRID_REF)
      AND  S.CYID_REF = $this->CYID AND S.BRID_REF in ($BranchName) AND S.Status = 'A' AND (S.DEACTIVATED = 0 or S.DEACTIVATED IS NULL)
      AND (O.SLCRBALANCE > '0.00' OR O.SLDRBALANCE > '0.00')
      AND C.CGID_REF in ($CUSTOMERGROUP)
      AND S.SGLID in ($SGLID)

      UNION 
SELECT 
'' AS SOURCE_DOCTYPE,
H.JV_NO, 
H.JV_DT,
H.NARRATION,
 A.DR_AMT, A.CR_AMT,
'' AS SOURCE_DOCNO,
'' AS SOURCE_DOCDT,
case when ISNULL(A.DR_AMT,0) > '0.00' THEN ISNULL(A.DR_AMT,0) ELSE ISNULL(A.CR_AMT,0) end AS SOURCE_DOCAMT

FROM 
TBL_TRN_FJRV01_ACC A LEFT JOIN TBL_TRN_FJRV01_HDR H ON A.JVID_REF=H.JVID
LEFT JOIN TBL_MST_BRANCH AS BR WITH (NOLOCK) ON H.BRID_REF=BR.BRID
LEFT JOIN TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID 
LEFT JOIN TBL_MST_SUBLEDGER S ON A.GLID_REF=S.SGLID AND A.SGLID_REF='S'
LEFT JOIN TBL_MST_CUSTOMER C ON C.SLID_REF=S.SGLID 
LEFT JOIN TBL_MST_CUSTOMERGROUP D ON D.CGID=C.CGID_REF
LEFT JOIN TBL_TRN_FJRV01_ACC B ON B.JVID_REF=H.JVID AND B.NARRATION = H.NARRATION
WHERE A.SGLID_REF='S' AND H.SOURCE_DOCTYPE IS NULL
AND H.JV_DT BETWEEN '$this->From_Date' AND '$this->To_Date' and H.CYID_REF = $this->CYID and H.BRID_REF in ($BranchName) and C.Status = 'A' and (C.DEACTIVATED = 0 or C.DEACTIVATED IS NULL)
and C.CGID_REF in ($CUSTOMERGROUP)
and S.SGLID in ($SGLID)"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
    
return [
  'Voucher Type',
  'Voucher No',
  'Voucher Date',
  'Narration',
  'Debit Amount',
  'Credit Amount',
  'Document No',
  'Document Date',
  'Amount'
];
    }
}





