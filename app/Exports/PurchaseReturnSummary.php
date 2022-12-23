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
use App\Exports\PurchaseReturnSummary;
use Maatwebsite\Excel\Facades\Excel;














class PurchaseReturnSummary implements FromCollection, WithHeadings
{


 function __construct($From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMGID,$ITEMID,$STATUS,$CYID_REF,$SGLID) {
        $this->ITEMID = $ITEMID;
        $this->ITEMGID = $ITEMGID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->STATUS = $STATUS;
        $this->CYID = $CYID_REF;
        $this->SGLID = $SGLID;
      
 }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        //dd($this->From_Date); 
        
        $ITEMGID=implode(",",$this->ITEMGID);        
        $ITEMID=implode(",",$this->ITEMID);        
        $BranchName=implode(",",$this->BranchName);       
        $SGLID=implode(",",$this->SGLID);


      return collect( $data=DB::select("SELECT
      H.PRR_NO,
      H.PRR_DT,
      BG.BG_DESC AS BRANCH_GROUP,  
      BR.BRNAME,
      V.VCODE AS VENDOR_CODE,
      V.NAME AS VENDOR_NAME,
      V.SAP_VENDOR_CODE,
      V.SAP_VENDOR_NAME1,
	  CL.NAME,
  
      SUM((M.RATEPUOM_MU * M.RETURN_QTY_MU)) AS GROSS_AMOUNT,
       
      SUM(((M.RETURN_QTY_MU*M.RATEPUOM_MU)+(case when M.CGST_RATE IS not NULL then convert(numeric(14,2),((M.RETURN_QTY_MU*M.RATEPUOM_MU*M.CGST_RATE)/100)) else  0 end)+
          (case when M.SGST_RATE IS not NULL then convert(numeric(14,2),((M.RETURN_QTY_MU*M.RATEPUOM_MU*M.SGST_RATE)/100)) else  0 end)+
          (case when M.IGST_RATE IS not NULL then convert(numeric(14,2),((M.RETURN_QTY_MU*M.RATEPUOM_MU*M.IGST_RATE)/100)) else  0 end))) AS NET_AMOUNT,
      
          
          CASE
          WHEN H.STATUS ='A' THEN 'Approved'
          WHEN H.STATUS = 'N' THEN 'Not Approved'
          WHEN H.STATUS = 'c' THEN 'Cancelled'
          WHEN H.STATUS = 'R' THEN 'Closed'
                       
          END AS STATUS 
           
      FROM
      TBL_TRN_PRRT01_MAT AS M (NOLOCK) LEFT OUTER JOIN
      TBL_TRN_PRRT01_HDR AS H (NOLOCK) ON H.PRRID = M.PRRID_REF  LEFT OUTER JOIN
      TBL_MST_SUBLEDGER AS S (NOLOCK) ON H.VID_REF = S.SGLID LEFT OUTER JOIN     
      TBL_MST_VENDOR AS V ON S.SGLID = V.SLID_REF LEFT OUTER JOIN
      TBL_MST_ITEM AS I (NOLOCK) ON M.ITEMID_REF = I.ITEMID LEFT OUTER JOIN
      TBL_MST_BUSINESSUNIT AS B (NOLOCK) ON I.BUID_REF = B.BUID LEFT OUTER JOIN
      TBL_MST_UOM AS MU (NOLOCK)  ON M.MAIN_UOMID_REF = MU.UOMID LEFT OUTER JOIN
      TBL_MST_VENDORLOCATION AS CL  (NOLOCK) ON H.BILLTO = CL.LID LEFT OUTER JOIN
      TBL_MST_VENDORLOCATION AS CL1 (NOLOCK) ON H.SHIPTO = CL1.LID LEFT OUTER JOIN
      TBL_MST_ITEMGROUP AS G (NOLOCK) ON I.ITEMGID_REF = G.ITEMGID  LEFT OUTER JOIN
      TBL_MST_BRANCH AS BR WITH (NOLOCK) ON H.BRID_REF=BR.BRID LEFT OUTER JOIN
      TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT OUTER JOIN
      (SELECT PRRID_REF, SUM(VALUE) + SUM(VALUE * IGST / 100) + SUM(VALUE * CGST / 100) + SUM(VALUE * SGST / 100) AS AMOUNT FROM TBL_TRN_PRRT01_CAL (NOLOCK)
      GROUP BY PRRID_REF) AS CT ON M.POID_REF = CT.PRRID_REF
	  
      WHERE (H.STATUS = '$this->STATUS') 
      AND (H.CYID_REF = $this->CYID) 
      AND (H.BRID_REF in ( $BranchName)) 
      AND (H.PRR_DT BETWEEN '$this->From_Date' AND '$this->To_Date')
      AND (V.SLID_REF in ( $SGLID))
      AND (I.ITEMGID_REF in ( $ITEMGID))
      AND (M.ITEMID_REF in ( $ITEMID))  

	  GROUP BY 
	  H.PRR_NO,
      H.PRR_DT,
      BG.BG_DESC,  
      BR.BRNAME,
      V.VCODE,
      V.NAME,
      V.SAP_VENDOR_CODE,
      V.SAP_VENDOR_NAME1,
	  CL.NAME,
	  H.STATUS   "));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
            'Purchase Return No',
            'Purchase Return Date',
            'Branch Group',
            'Branch Name',
            'Vendor Code',
            'Vendor Name',
            'SAP Vendor Code',
            'SAP Vendor Name',
           'Billing Address',
			'Gross Amount',
			'Net Amount',
			'Status',		
			
        ];
    }
}





