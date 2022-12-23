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
use App\Exports\VendorLedger;
use Maatwebsite\Excel\Facades\Excel;


class VendorLedger implements FromCollection, WithHeadings
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
       
       



      return collect( $data=DB::select("SELECT V.VCODE,
      V.NAME,
      V.SAP_VENDOR_CODE,
      V.SAP_VENDOR_NAME1,
      BG.BG_DESC AS BRANCH_GROUP,
      BR.BRNAME,
      DBO.FN_VODBL(A.GLID_REF,'$this->From_Date') AS OPENING_DR,
      DBO.FN_VOCBL(A.GLID_REF,'$this->From_Date') AS OPENING_CR, 
	  SUM(ISNULL(A.DR_AMT,0)) AS DR_AMT,
      SUM(ISNULL(A.CR_AMT,0)) AS CR_AMT,
	  IIF((DBO.FN_VODBL(A.GLID_REF,'$this->From_Date')-DBO.FN_VOCBL(A.GLID_REF,'$this->From_Date')+SUM(ISNULL(A.DR_AMT,0))-SUM(ISNULL(A.CR_AMT,0)))>0,
      (DBO.FN_VODBL(A.GLID_REF,'$this->From_Date')-DBO.FN_VOCBL(A.GLID_REF,'$this->From_Date')+SUM(ISNULL(A.DR_AMT,0))-SUM(ISNULL(A.CR_AMT,0))),0) AS CLOSING_DR,
      IIF((DBO.FN_VODBL(A.GLID_REF,'$this->From_Date')-DBO.FN_VOCBL(A.GLID_REF,'$this->From_Date')+SUM(ISNULL(A.DR_AMT,0))-SUM(ISNULL(A.CR_AMT,0)))<0,
      ABS((DBO.FN_VODBL(A.GLID_REF,'$this->From_Date')-DBO.FN_VOCBL(A.GLID_REF,'$this->From_Date')+SUM(ISNULL(A.DR_AMT,0))-SUM(ISNULL(A.CR_AMT,0)))),0) AS CLOSING_CR
FROM (
SELECT A.GLID_REF, SUM(ISNULL(A.DR_AMT,0)) AS DR_AMT,
      SUM(ISNULL(A.CR_AMT,0)) AS CR_AMT,A.BRID_REF
FROM(
SELECT A.GLID_REF, SUM(ISNULL(A.DR_AMT,0)) AS DR_AMT,
      SUM(ISNULL(A.CR_AMT,0)) AS CR_AMT,H.BRID_REF
FROM TBL_TRN_FJRV01_ACC A  INNER JOIN TBL_TRN_FJRV01_HDR H ON A.JVID_REF=H.JVID
WHERE A.SGLID_REF='S' 
AND (H.SOURCE_DOCTYPE IN ('AP DEBIT CREDIT NOTE','PURCHASE INVOICE','SERVICE PURCHASE INVOICE','PURCHASE RETURN','DSV','PAYMENT','IMPORT PURCHASE INVOICE')
OR H.SOURCE_DOCTYPE IS NULL)
AND H.JV_DT  BETWEEN '$this->From_Date' AND '$this->To_Date' and H.CYID_REF = $this->CYID and H.BRID_REF in ($BranchName) 
AND H.STATUS = 'A'
AND A.GLID_REF in ($SGLID)
GROUP BY A.GLID_REF,H.BRID_REF 
UNION ALL
SELECT A.GLID_REF, SUM(ISNULL(A.DR_AMT,0)) AS DR_AMT,
      SUM(ISNULL(A.CR_AMT,0)) AS CR_AMT,H.BRID_REF
FROM TBL_TRN_MJRV01_ACC A  INNER JOIN TBL_TRN_MJRV01_HDR H ON A.MJVID_REF=H.MJVID
WHERE A.SGLID_REF='S' 
AND H.SOURCE_DOCTYPE IS NULL
AND H.MJV_DT  BETWEEN '$this->From_Date' AND '$this->To_Date' and H.CYID_REF = $this->CYID and H.BRID_REF in ($BranchName) 
AND H.STATUS = 'A'
AND A.GLID_REF in ($SGLID)
GROUP BY A.GLID_REF,H.BRID_REF
) AS A
GROUP BY A.GLID_REF,A.BRID_REF
UNION ALL
SELECT O.SGLID_REF AS GLID_REF, 0 AS DR_AMT, 0 AS CR_AMT,O.BRID_REF
FROM TBL_MST_SLOPENING_LEDGER O WITH (NOLOCK)
WHERE O.SGLID_REF NOT IN (SELECT GLID_REF FROM  TBL_TRN_FJRV01_ACC A LEFT JOIN TBL_TRN_FJRV01_HDR H ON A.JVID_REF=H.JVID 
							WHERE  A.SGLID_REF='S' AND H.CYID_REF = $this->CYID AND H.BRID_REF in ($BranchName) AND H.STATUS = 'A'
AND (SOURCE_DOCTYPE IN ('AP DEBIT CREDIT NOTE','PURCHASE INVOICE','SERVICE PURCHASE INVOICE','PURCHASE RETURN','DSV','PAYMENT','IMPORT PURCHASE INVOICE')
OR SOURCE_DOCTYPE IS NULL) AND H.JV_DT  BETWEEN '$this->From_Date' AND '$this->To_Date'
UNION
		SELECT GLID_REF FROM  TBL_TRN_MJRV01_ACC A LEFT JOIN TBL_TRN_MJRV01_HDR H ON A.MJVID_REF=H.MJVID 
							WHERE  A.SGLID_REF='S' AND H.CYID_REF = $this->CYID AND H.BRID_REF in ($BranchName) AND H.STATUS = 'A'
AND SOURCE_DOCTYPE IS NULL AND H.MJV_DT  BETWEEN '$this->From_Date' AND '$this->To_Date')
AND  O.CYID_REF = $this->CYID AND O.BRID_REF in ($BranchName) AND O.Status = 'A'
AND (O.SLCRBALANCE > '0.00' OR O.SLDRBALANCE > '0.00')
and O.SGLID_REF in ($SGLID)
GROUP BY O.SGLID_REF,O.BRID_REF
)AS A
INNER JOIN TBL_MST_VENDOR V ON A.GLID_REF=V.SLID_REF
INNER JOIN TBL_MST_VENDORGROUP VG ON V.VGID_REF = VG.VGID
INNER JOIN TBL_MST_BRANCH AS BR WITH (NOLOCK) ON A.BRID_REF=BR.BRID 
INNER JOIN TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID
WHERE A.GLID_REF in ($SGLID) and V.Status = 'A' and (V.DEACTIVATED = 0 or V.DEACTIVATED IS NULL)
and V.CYID_REF = $this->CYID and A.BRID_REF IN ($BranchName) AND V.VGID_REF in ($VENDORGROUP)
GROUP BY V.VCODE,V.NAME,V.SAP_VENDOR_CODE,V.SAP_VENDOR_NAME1,BG.BG_DESC,BR.BRNAME,A.GLID_REF"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
    
        return [
      
          'Vendor  Code',
          'Vendor  Name',
          'SAP Vendor Code',
          'SAP Vendor Name',  
          'Branch Group',
          'Branch Name',
          'Opening Debit',
          'Opening Credit',
          'Transaction Debit',
          'Transaction Credit',
          'Closing Debit',
          'Closing Credit',
    
        ];
    }
}





