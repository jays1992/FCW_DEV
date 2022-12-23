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
use App\Exports\VendorLedgerDocWise;
use Maatwebsite\Excel\Facades\Excel;














class VendorLedgerDocWise implements FromCollection, WithHeadings
{


 function __construct($SGLID,$VENDORGROUP,$From_Date,$To_Date,$BranchGroup,$BranchName,$CYID) {
        $this->SGLID = $SGLID;
        $this->VENDORGROUP = $VENDORGROUP;
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
        $VENDORGROUP=implode(",",$this->VENDORGROUP);
        $BranchName=implode(",",$this->BranchName);      
       
       



      return collect( $data=DB::select("      SELECT 
      H.NARRATION,
      H.JV_NO,
      H.JV_DT,
      BG.BG_DESC AS BRANCH_GROUP,
      BR.BRNAME,
      V.VCODE,
      V.NAME,
      V.SAP_VENDOR_CODE,
      V.SAP_VENDOR_NAME1,
      H.SOURCE_DOCNO,
      H.SOURCE_DOCDT,
	  CASE WHEN  B.NARRATION='CHA' THEN A.CR_AMT ELSE
		case when H.NARRATION = 'PURCHASE RETURN' then ISNULL(B.DR_AMT,0) when H.NARRATION = 'AP DEBIT' then ISNULL(B.DR_AMT,0)
		when H.NARRATION = 'PAYMENT FOR Vendor' then ISNULL(B.DR_AMT,0) else ISNULL(B.CR_AMT,0) end END
		AS SOURCE_DOCAMT,
      
      --DBO.FN_CODBL(V.SLID_REF,'$this->From_Date') AS OPENING_DR,
      
      DBO.FN_COCBL(V.SLID_REF,'$this->From_Date') AS OPENING_CR,

	  B.DR_AMT,B.CR_AMT,
      
      (((case 
      when H.NARRATION = 'PURCHASE INVOICE' then ISNULL(B.CR_AMT,0) 
      when H.NARRATION = 'AP CREDIT' then ISNULL(B.CR_AMT,0)
      when H.NARRATION = 'IMPORT PURCHASE INVOICE' then ISNULL(B.CR_AMT,0)
      when H.NARRATION = 'SERVICE PURCHASE INVOICE' then ISNULL(B.CR_AMT,0)
      when H.NARRATION = 'AP INVOICE' then ISNULL(B.CR_AMT,0)
      else 0
      end)-(case 
      when H.NARRATION = 'PAYMENT FOR Vendor' then ISNULL(B.CR_AMT,0) 
      when H.NARRATION = 'AP DEBIT' then ISNULL(B.CR_AMT,0)
      when H.NARRATION = 'PURCHASE RETURN' then ISNULL(B.CR_AMT,0)
      else 0
      end))+DBO.FN_COCBL(V.SLID_REF,'$this->From_Date')) AS CLOSING_BALANCE
	  FROM 
		TBL_TRN_FJRV01_ACC A LEFT JOIN TBL_TRN_FJRV01_HDR H ON A.JVID_REF=H.JVID
		LEFT JOIN TBL_MST_BRANCH AS BR WITH (NOLOCK) ON H.BRID_REF=BR.BRID
		LEFT JOIN TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID 
		LEFT JOIN TBL_MST_SUBLEDGER S ON A.GLID_REF=S.SGLID AND A.SGLID_REF='S'
		LEFT JOIN TBL_MST_VENDOR V ON V.SLID_REF=S.SGLID 
		LEFT JOIN TBL_MST_VENDORGROUP VG ON VG.VGID=V.VGID_REF
		LEFT JOIN TBL_TRN_FJRV01_ACC B ON B.JVID_REF=H.JVID AND B.NARRATION = H.NARRATION

WHERE A.SGLID_REF='S' AND H.SOURCE_DOCTYPE IN ('AP DEBIT CREDIT NOTE','PURCHASE INVOICE','IMPORT PURCHASE INVOICE','SERVICE PURCHASE INVOICE','PURCHASE RETURN','DSV','PAYMENT')
AND H.JV_DT BETWEEN '$this->From_Date' AND '$this->To_Date' and H.CYID_REF =$this->CYID and H.BRID_REF in ($BranchName) and V.Status = 'A' and (V.DEACTIVATED = 0 or V.DEACTIVATED IS NULL)
and V.VGID_REF in ($VENDORGROUP)
and S.SGLID in ($SGLID) 

UNION
SELECT 
'' AS NARRATION,
'' AS JV_NO, 
'' AS JV_DT,
BR.BRNAME,
BG.BG_DESC AS BRANCH_GROUP,
V.VCODE,
V.NAME,
V.SAP_VENDOR_CODE,
V.SAP_VENDOR_NAME1,
'' AS SOURCE_DOCNO,
'' AS SOURCE_DOCDT,
0 AS SOURCE_DOCAMT,
DBO.FN_COCBL(S.SGLID,'$this->From_Date') AS OPENING_CR,0 AS DR_AMT,0 AS CR_AMT,
0 AS CLOSING_BALANCE

FROM TBL_MST_SLOPENING_LEDGER O WITH (NOLOCK)
LEFT JOIN TBL_MST_BRANCH AS BR WITH (NOLOCK) ON O.BRID_REF=BR.BRID
LEFT JOIN TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID 
LEFT JOIN TBL_MST_SUBLEDGER S ON O.SGLID_REF=S.SGLID 
LEFT JOIN TBL_MST_VENDOR V ON V.SLID_REF=S.SGLID 
LEFT JOIN TBL_MST_VENDORGROUP D ON D.VGID=V.VGID_REF
WHERE O.SGLID_REF NOT IN (SELECT GLID_REF FROM  TBL_TRN_FJRV01_ACC A LEFT JOIN TBL_TRN_FJRV01_HDR H ON A.JVID_REF=H.JVID 
				WHERE  A.SGLID_REF='S' AND H.CYID_REF=S.CYID_REF AND H.BRID_REF = S.BRID_REF)
AND  S.CYID_REF = $this->CYID AND S.BRID_REF in ($BranchName) AND S.Status = 'A' AND (S.DEACTIVATED = 0 or S.DEACTIVATED IS NULL)
AND (O.SLCRBALANCE > '0.00' OR O.SLDRBALANCE > '0.00')
AND V.VGID_REF in ($VENDORGROUP)
AND S.SGLID in ($SGLID)


UNION
SELECT 
H.NARRATION, '' AS JV_NO,'' AS JV_DT,
BR.BRNAME,BG.BG_DESC AS BRANCH_GROUP,
V.VCODE,V.NAME,V.SAP_VENDOR_CODE,V.SAP_VENDOR_NAME1,
'' AS SOURCE_DOCNO,'' AS SOURCE_DOCDT,
 case when ISNULL(A.DR_AMT,0) > '0.00' THEN ISNULL(A.DR_AMT,0) ELSE ISNULL(A.CR_AMT,0) end AS SOURCE_DOCAMT,
 DBO.FN_COCBL(V.SLID_REF,'$this->From_Date') AS OPENING_CR,A.DR_AMT,A.CR_AMT,

   (((case 
      when H.NARRATION = 'PURCHASE INVOICE' then ISNULL(A.CR_AMT,0) 
      when H.NARRATION = 'AP CREDIT' then ISNULL(A.CR_AMT,0)
      when H.NARRATION = 'IMPORT PURCHASE INVOICE' then ISNULL(A.CR_AMT,0)
      when H.NARRATION = 'SERVICE PURCHASE INVOICE' then ISNULL(A.CR_AMT,0)
      when H.NARRATION = 'AP INVOICE' then ISNULL(A.CR_AMT,0)
      else 0
      end)-(case 
      when H.NARRATION = 'PAYMENT FOR Vendor' then ISNULL(A.CR_AMT,0) 
      when H.NARRATION = 'AP DEBIT' then ISNULL(A.CR_AMT,0)
      when H.NARRATION = 'PURCHASE RETURN' then ISNULL(A.CR_AMT,0)
      else 0
      end))+DBO.FN_COCBL(V.SLID_REF,'$this->From_Date')) AS CLOSING_BALANCE


FROM 
TBL_TRN_FJRV01_ACC A LEFT JOIN TBL_TRN_FJRV01_HDR H ON A.JVID_REF=H.JVID
LEFT JOIN TBL_MST_BRANCH AS BR WITH (NOLOCK) ON H.BRID_REF=BR.BRID
LEFT JOIN TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID 
LEFT JOIN TBL_MST_SUBLEDGER S ON A.GLID_REF=S.SGLID AND A.SGLID_REF='S'
LEFT JOIN TBL_MST_VENDOR V ON V.SLID_REF=S.SGLID 
LEFT JOIN TBL_MST_VENDORGROUP VG ON VG.VGID=V.VGID_REF
WHERE A.SGLID_REF='S' AND H.SOURCE_DOCTYPE IS NULL
AND H.JV_DT BETWEEN '$this->From_Date' AND '$this->To_Date' and H.CYID_REF =$this->CYID and H.BRID_REF in ($BranchName) and V.Status = 'A' and (V.DEACTIVATED = 0 or V.DEACTIVATED IS NULL)
and V.VGID_REF in ($VENDORGROUP)
and S.SGLID in ($SGLID) 
      "));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
    
return [
  'Voucher Type',
  'Voucher No',
  'Voucher Date',
  'Branch Group',
  'Branch Name',
  'Vendor Code',
  'Vendor Name',
  'SAP Vendor Code',
  'SAP Vendor Name',
  'Source Doc No',
  'Source Doc Date',
  'Source Doc Amount',
  'Opening Balance',
  'Debit Amount',
  'Credit Amount',
  'Closing Balance'
];
    }
}





