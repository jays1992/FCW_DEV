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
use App\Exports\Vendor_Quotation_Register;
use Maatwebsite\Excel\Facades\Excel;














class Vendor_Quotation_Register implements FromCollection, WithHeadings
{


 function __construct($SGLID,$From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMGID,$ITEMID,$STATUS,$CYID) {
        $this->ITEMID = $ITEMID;
        $this->SGLID = $SGLID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->ITEMGID = $ITEMGID;
        $this->STATUS = $STATUS;
        $this->CYID = $CYID;
 }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        //dd($this->From_Date); 
        
        $ITEMID=implode(",",$this->ITEMID);
        $SGLID=implode(",",$this->SGLID);
        $BranchName=implode(",",$this->BranchName);      
        $ITEMGID=implode(",",$this->ITEMGID);
       



      return collect( $data=DB::select("	SELECT
      H.VQ_NO,
      H.VQ_DT,
      BG.BG_DESC AS BRANCH_GROUP,
      BR.BRNAME,
      VG.DESCRIPTIONS AS VENDOR_GROUP,
      V.VCODE AS VENDOR_CODE,
      V.NAME AS VENDOR_NAME,
      V.SAP_VENDOR_CODE,
      V.SAP_VENDOR_NAME1,
      I.NAME AS ITEM_NAME,
      HSN.HSNCODE,
      MU.UOMCODE,
      B.BUNAME,
      I.ALPS_PART_NO, 
      I.CUSTOMER_PART_NO, 
      I.OEM_PART_NO,
      CONCAT(R.RQF_DOC_NO,P.PI_NO) AS RFQNO,
      CONCAT(CAST(R.RQF_DOC_DT AS varchar),CAST(P.PI_DT AS varchar)) AS RFQDT,
      M.RATEP_UOM,
      M.RFQ_QTY,
      M.PENDING_QTY,
      (M.RFQ_QTY-m.PENDING_QTY) AS CONSUMED_QTY,
      M.QUOTATION_QTY,
      (M.RATEP_UOM*m.QUOTATION_QTY) AS ItemTotal,

      (case when M.DISCOUNT_AMT = '0.00' then convert(numeric(14,2),((M.QUOTATION_QTY*M.RATEP_UOM*M.DISCOUNT_PER)/100))
        else  M.DISCOUNT_AMT end) AS Discount,			  

        (M.RATEP_UOM*M.QUOTATION_QTY)-(case when M.DISCOUNT_AMT = '0.00' then convert(numeric(14,2),((M.QUOTATION_QTY*M.RATEP_UOM*M.DISCOUNT_PER)/100))
        else  M.DISCOUNT_AMT end) AS TaxableAmount,


       ((((M.RATEP_UOM*M.QUOTATION_QTY)-(case when M.DISCOUNT_AMT = '0.00' then convert(numeric(14,2),((M.QUOTATION_QTY*M.RATEP_UOM*M.DISCOUNT_PER)/100))
        else  M.DISCOUNT_AMT end))*(M.IGST)/100)) AS IGST,

        ((((M.RATEP_UOM*M.QUOTATION_QTY)-(case when M.DISCOUNT_AMT = '0.00' then convert(numeric(14,2),((M.QUOTATION_QTY*M.RATEP_UOM*M.DISCOUNT_PER)/100))
        else  M.DISCOUNT_AMT end))*(M.CGST)/100)) AS CGST,

        ((((M.RATEP_UOM*M.QUOTATION_QTY)-(case when M.DISCOUNT_AMT = '0.00' then convert(numeric(14,2),((M.QUOTATION_QTY*M.RATEP_UOM*M.DISCOUNT_PER)/100))
        else  M.DISCOUNT_AMT end))*(M.SGST)/100)) AS SGST,			  
        

        ((((M.RATEP_UOM*M.QUOTATION_QTY)-(case when M.DISCOUNT_AMT = '0.00' then convert(numeric(14,2),((M.QUOTATION_QTY*M.RATEP_UOM*M.DISCOUNT_PER)/100)) else  M.DISCOUNT_AMT end))*(M.IGST)/100)+
        (((M.RATEP_UOM*M.QUOTATION_QTY)-(case when M.DISCOUNT_AMT = '0.00' then convert(numeric(14,2),((M.QUOTATION_QTY*M.RATEP_UOM*M.DISCOUNT_PER)/100)) else  M.DISCOUNT_AMT end))*(M.CGST)/100)+
        ((M.RATEP_UOM*M.QUOTATION_QTY)-(case when M.DISCOUNT_AMT = '0.00' then convert(numeric(14,2),((M.QUOTATION_QTY*M.RATEP_UOM*M.DISCOUNT_PER)/100)) else  M.DISCOUNT_AMT end))*(M.SGST)/100) AS TotalGST_TAX,
                

      (M.RATEP_UOM*M.QUOTATION_QTY)-(case when M.DISCOUNT_AMT = '0.00' then convert(numeric(14,2),((M.QUOTATION_QTY*M.RATEP_UOM*M.DISCOUNT_PER)/100)) else  M.DISCOUNT_AMT end)+((((M.RATEP_UOM*M.QUOTATION_QTY)-(case when M.DISCOUNT_AMT = '0.00' then convert(numeric(14,2),((M.QUOTATION_QTY*M.RATEP_UOM*M.DISCOUNT_PER)/100)) else  M.DISCOUNT_AMT end))*(M.IGST)/100)+
        (((M.RATEP_UOM*M.QUOTATION_QTY)-(case when M.DISCOUNT_AMT = '0.00' then convert(numeric(14,2),((M.QUOTATION_QTY*M.RATEP_UOM*M.DISCOUNT_PER)/100)) else  M.DISCOUNT_AMT end))*(M.CGST)/100)+
        ((M.RATEP_UOM*M.QUOTATION_QTY)-(case when M.DISCOUNT_AMT = '0.00' then convert(numeric(14,2),((M.QUOTATION_QTY*M.RATEP_UOM*M.DISCOUNT_PER)/100)) else  M.DISCOUNT_AMT end))*(M.SGST)/100) AS TotalAmount,
                                  
         CL.CT,
             
  (M.RATEP_UOM*M.QUOTATION_QTY)-(case when M.DISCOUNT_AMT = '0.00' then convert(numeric(14,2),((M.QUOTATION_QTY*M.RATEP_UOM*M.DISCOUNT_PER)/100)) else  M.DISCOUNT_AMT end)+
  ((((M.RATEP_UOM*M.QUOTATION_QTY)-(case when M.DISCOUNT_AMT = '0.00' then convert(numeric(14,2),((M.QUOTATION_QTY*M.RATEP_UOM*M.DISCOUNT_PER)/100)) else  M.DISCOUNT_AMT end))*(M.IGST)/100)+
        (((M.RATEP_UOM*M.QUOTATION_QTY)-(case when M.DISCOUNT_AMT = '0.00' then convert(numeric(14,2),((M.QUOTATION_QTY*M.RATEP_UOM*M.DISCOUNT_PER)/100)) else  M.DISCOUNT_AMT end))*(M.CGST)/100)+
        ((M.RATEP_UOM*M.QUOTATION_QTY)-(case when M.DISCOUNT_AMT = '0.00' then convert(numeric(14,2),((M.QUOTATION_QTY*M.RATEP_UOM*M.DISCOUNT_PER)/100)) else  M.DISCOUNT_AMT end))*(M.SGST)/100)+(case when CL.CT IS not NULL then CL.CT else  0 end) AS BillTotal,
                    
                                                     
        
          CASE
              WHEN H.STATUS ='A' THEN 'Approved'
              WHEN H.STATUS = 'N' THEN 'Not Approved'
              WHEN H.STATUS = 'c' THEN 'Cancelled'
              WHEN H.STATUS = 'R' THEN 'Closed'
          
          END AS STATUS 
      
      
      FROM            
      TBL_TRN_VDQT01_MAT AS M WITH (NOLOCK) LEFT OUTER JOIN
      TBL_TRN_VDQT01_HDR AS H WITH (NOLOCK) ON H.VQID = M.VQID_REF LEFT OUTER JOIN                        
      TBL_MST_SUBLEDGER (NOLOCK) ON H.VID_REF = TBL_MST_SUBLEDGER.SGLID LEFT OUTER JOIN 
      TBL_MST_VENDOR AS V ON TBL_MST_SUBLEDGER.SGLID = V.SLID_REF LEFT OUTER JOIN
      TBL_MST_VENDORGROUP AS VG ON V.VGID_REF = VG.VGID LEFT OUTER JOIN
      TBL_MST_BRANCH AS BR WITH (NOLOCK) ON H.BRID_REF=BR.BRID LEFT OUTER JOIN
      TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT OUTER JOIN
      TBL_MST_ITEM AS I WITH (NOLOCK) ON M.ITEMID_REF = I.ITEMID LEFT OUTER JOIN
      TBL_MST_HSN AS HSN WITH (NOLOCK) ON I.HSNID_REF=HSN.HSNID LEFT OUTER JOIN
      TBL_MST_BUSINESSUNIT AS B WITH (NOLOCK) ON I.BUID_REF = B.BUID LEFT OUTER JOIN
      TBL_MST_UOM AS MU WITH (NOLOCK) ON M.UOMID_REF = MU.UOMID LEFT OUTER JOIN
      TBL_MST_ITEMGROUP AS G WITH (NOLOCK) ON I.ITEMGID_REF = G.ITEMGID LEFT JOIN
      TBL_TRN_RQFA01_HDR AS R WITH (NOLOCK) ON R.RQFID=M.RFQNO LEFT JOIN
      TBL_TRN_PRIN02_HDR AS P WITH (NOLOCK) ON P.PIID=M.PIID_REF LEFT JOIN
      (SELECT K.VQID_REF,SUM(ISNULL(K.VALUE,0)+(ISNULL(K.VALUE,0)*ISNULL(K.CGST,0)/100)+(ISNULL(K.VALUE,0)*ISNULL(K.SGST,0)/100)+(ISNULL(K.VALUE,0)*ISNULL(K.IGST,0)/100)) AS CT FROM TBL_TRN_VDQT01_CAL K GROUP BY K.VQID_REF)  AS CL ON H.VQID=CL.VQID_REF

  

  

      WHERE   (H.STATUS='$this->STATUS')
      and (H.VID_REF IN ($SGLID)) 
      AND (I.ITEMGID_REF IN ($ITEMGID)) 
      AND (M.ITEMID_REF IN ($ITEMID)) 
      AND (H.CYID_REF = $this->CYID) 
      AND (H.BRID_REF IN ($BranchName)) 
      AND (H.VQ_DT BETWEEN '$this->From_Date' AND '$this->To_Date')"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
       		
 return [
    'Quotation No',
    'Quotation Date',
    'Branch Group',
    'Branch Name',
    'Vendor Group',
    'Vendor Code',
    'Vendor Name',
    'SAP Vendor Code',
    'SAP Vendor Name',
    'Item Name',
    'HSN/SAC Code',
    'Uom',
    'Business Unit',
    'ALPS Part No',
    'Customer Part No',
    'OEM Part No',
    'RFQ NO/PI NO',
    'RFQ Date/PI  Date',
    'RATEP UOM',
    'RFQ/PI Quantity',
    'Pending Qty',
    'Consumed Qty',
    'Actual Qty',
    'Item Total',
    'Discount',
    'Taxable Amount',
    'IGST',
    'CGST',
    'SGST',
    'Total GST/TAX',
    'Total Amount',
    'Others Charges (If Any)',
    'Bill Total',
    'Status',			
    
];
    }
}





