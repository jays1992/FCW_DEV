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
use App\Exports\SPISummary;
use Maatwebsite\Excel\Facades\Excel;














class SPISummary implements FromCollection, WithHeadings
{


 function __construct($SGLID,$From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMGID,$ITEMID,$STATUS,$CYID_REF) {
        $this->ITEMID = $ITEMID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->ITEMGID = $ITEMGID;
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
        
        $ITEMID=implode(",",$this->ITEMID);        
        $BranchName=implode(",",$this->BranchName);      
        $ITEMGID=implode(",",$this->ITEMGID);
        $ITEMGID=implode(",",$this->ITEMGID);
        $SGLID=implode(",",$this->SGLID);

       



      return collect( $data=DB::select("SELECT 
      H.SPI_NO AS DOC_NO,
      H.SPI_DT AS DOC_DT,
      BG.BG_DESC AS BRANCH_GROUP,
      VG.DESCRIPTIONS AS VENDOR_GROUP,
      V.VCODE AS VENDOR_CODE,
      V.NAME AS VENDOR_NAME,
      V.SAP_VENDOR_CODE,
      V.SAP_VENDOR_NAME1,
	  CL.NAME,
      SUM(M.BILL_QTY*M.BILL_RATEPUOM) AS GROSS_AMOUNT,
      
      
      SUM(((M.BILL_QTY*M.BILL_RATEPUOM)+(case when M.CGST IS not NULL then convert(numeric(14,2),((M.BILL_QTY*M.BILL_RATEPUOM*M.CGST)/100)) else  0 end)+
      (case when M.SGST IS not NULL then convert(numeric(14,2),((M.BILL_QTY*M.BILL_RATEPUOM*M.SGST)/100)) else  0 end)+
      (case when M.IGST IS not NULL then convert(numeric(14,2),((M.BILL_QTY*M.BILL_RATEPUOM*M.IGST)/100)) else  0 end)+
      (case when CT.AMOUNT IS not NULL then CT.AMOUNT else  0 end)+(case when TDS.TDS_AMT IS not NULL then TDS.TDS_AMT else  0 end))-(case when M.DISCOUNT = '0.00' then convert(numeric(14,2),((M.BILL_QTY*M.BILL_RATEPUOM*M.DIS_PER)/100)) else  M.DISCOUNT end))
      as NET_AMOUNT,      
      
      
          CASE
                     WHEN H.STATUS ='A' THEN 'Approved'
                     WHEN H.STATUS = 'N' THEN 'Not Approved'
                     WHEN H.STATUS = 'c' THEN 'Cancelled'
                     WHEN H.STATUS = 'R' THEN 'Closed' 
                 END AS STATUS  
      
      FROM  TBL_TRN_PRPB02_SRV AS M WITH (NOLOCK) LEFT OUTER JOIN
      TBL_TRN_PRPB02_HDR AS H WITH (NOLOCK) ON H.SPIID = M.SPIID_REF LEFT OUTER JOIN
      TBL_MST_SUBLEDGER AS S (NOLOCK) ON H.VID_REF = S.SGLID LEFT OUTER JOIN     
      TBL_MST_VENDOR AS V ON S.SGLID = V.SLID_REF LEFT OUTER JOIN
        TBL_MST_VENDORGROUP AS VG ON V.VGID_REF = VG.VGID LEFT OUTER JOIN
      TBL_MST_ITEM AS I WITH (NOLOCK) ON M.SRVID_REF = I.ITEMID LEFT OUTER JOIN
      TBL_MST_HSN AS HSN WITH (NOLOCK) ON I.HSNID_REF=HSN.HSNID LEFT OUTER JOIN
      TBL_MST_BUSINESSUNIT AS B WITH (NOLOCK) ON I.BUID_REF = B.BUID LEFT OUTER JOIN
      TBL_MST_VENDORLOCATION AS CL  (NOLOCK) ON V.VID = CL.VID_REF LEFT OUTER JOIN
      TBL_MST_UOM AS MU WITH (NOLOCK) ON M.UOMID_REF = MU.UOMID LEFT OUTER JOIN
      TBL_MST_BRANCH AS BR WITH (NOLOCK) ON H.BRID_REF=BR.BRID LEFT OUTER JOIN
      TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT OUTER JOIN
      TBL_MST_ITEMGROUP AS G WITH (NOLOCK) ON I.ITEMGID_REF = G.ITEMGID LEFT OUTER JOIN
      (SELECT  SPIID_REF, SUM(VALUE) + SUM(VALUE * IGST / 100) + SUM(VALUE * CGST / 100) + SUM(VALUE * SGST / 100) AS AMOUNT
       FROM  TBL_TRN_PRPB02_CAL WITH (NOLOCK)
       GROUP BY SPIID_REF) AS CT ON M.SPIID_REF = CT.SPIID_REF
       LEFT OUTER JOIN(
       SELECT D.SPIID_REF,SUM(CASE WHEN (ISNULL(D.ASSESSABLE_VL_TDS,0) > ISNULL(E.TDS_EXEMP_LIMIT,0)) THEN         
      CONVERT(NUMERIC(14,2),(((ISNULL(D.ASSESSABLE_VL_TDS,0)-ISNULL(E.TDS_EXEMP_LIMIT,0)) * ISNULL(D.TDS_RATE,0))/100)) ELSE 0 END +   
      CASE WHEN (ISNULL(D.ASSESSABLE_VL_SURCHARGE,0) > ISNULL(E.SURCHARGE_EXEMP_LIMIT,0)) THEN           
      CONVERT(NUMERIC(14,2),(((ISNULL(D.ASSESSABLE_VL_SURCHARGE,0)-ISNULL(E.SURCHARGE_EXEMP_LIMIT,0)) * ISNULL(D.SURCHARGE_RATE,0))/100)) ELSE 0 END +   
      CASE WHEN (ISNULL(D.ASSESSABLE_VL_CESS,0) > ISNULL(E.CESS_EXEMP_LIMIT,0)) THEN          
      CONVERT(NUMERIC(14,2),(((ISNULL(D.ASSESSABLE_VL_CESS,0)-ISNULL(E.CESS_EXEMP_LIMIT,0)) * ISNULL(D.CESS_RATE,0))/100)) ELSE 0 END +  
      CASE WHEN (ISNULL(D.ASSESSABLE_VL_SPCESS,0) > ISNULL(E.SP_CESS_EXEMP_LIMIT,0)) THEN           
      CONVERT(NUMERIC(14,2),(((ISNULL(D.ASSESSABLE_VL_SPCESS,0)-ISNULL(E.SP_CESS_EXEMP_LIMIT,0)) * ISNULL(D.SPCESS_RATE,0))/100)) ELSE 0 END) AS TDS_AMT 
      FROM TBL_TRN_PRPB02_TDS D(NOLOCK)  INNER JOIN TBL_MST_WITHHOLDING E(NOLOCK) ON D.TDSID_REF = E.HOLDINGID 
      GROUP BY D.SPIID_REF
       )  AS TDS ON M.SPIID_REF = TDS.SPIID_REF
      

      WHERE V.SLID_REF IN ($SGLID) 
      AND I.ITEMGID_REF IN ($ITEMGID) 
      AND M.SRVID_REF IN ($ITEMID) 
      AND H.STATUS = '$this->STATUS' 
      AND H.CYID_REF = $this->CYID 
      AND H.BRID_REF IN ($BranchName) 
      AND (H.SPI_DT BETWEEN '$this->From_Date' AND '$this->To_Date') 
      AND CL.DEFAULT_BILLING=1

	  GROUP BY 
	  H.SPI_NO,
      H.SPI_DT,
      BG.BG_DESC,
      VG.DESCRIPTIONS,
      V.VCODE,
      V.NAME,
      V.SAP_VENDOR_CODE,
      V.SAP_VENDOR_NAME1,
	  CL.NAME,
	  H.STATUS
      "));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
            'SPI No',
            'SPI Date',
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





