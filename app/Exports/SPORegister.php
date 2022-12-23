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















class SPORegister implements FromCollection, WithHeadings
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
       



      return collect( $data=DB::select("SELECT     
      H.SPO_NO, 
      H.SPO_DT,
      BG.BG_DESC AS BRANCH_GROUP,
      BR.BRNAME,
      VG.DESCRIPTIONS AS VENDER_GROUP,
         V.VCODE AS VENDOR_CODE,
            V.NAME AS VENDOR_NAME,
            V.SAP_VENDOR_CODE,
            V.SAP_VENDOR_NAME1,
            I.NAME AS ITEM_NAME,
             HSN.HSNCODE,
              MU.DESCRIPTIONS,
              B.BUNAME,
               I.ALPS_PART_NO, I.CUSTOMER_PART_NO, I.OEM_PART_NO,
               M.PENDING_QTY,
                   M.SPO_QTY-M.PENDING_QTY AS CONSUMED_QTY,
                  M.SPO_QTY,
                  M.SPO_RATE,
                  M.SPO_QTY*M.SPO_RATE AS ITEM_TOTAL,			
      
      
                  case when M.DIS_AMT = '0.00' then convert(numeric(14,2),((M.SPO_QTY*M.SPO_RATE*M.DISCOUNT_PER)/100))
            else  M.DIS_AMT end AS DISCOUNT,
      
                    (M.SPO_QTY*M.SPO_RATE)-(case when M.DIS_AMT = '0.00' then convert(numeric(14,2),((M.SPO_QTY*M.SPO_RATE*M.DISCOUNT_PER)/100))
            else  M.DIS_AMT end) AS TAXABLE_AMOUNT,

              ((((M.SPO_RATE*M.SPO_QTY)-(case when M.DIS_AMT = '0.00' then convert(numeric(14,2),((M.SPO_QTY*M.SPO_RATE*M.DISCOUNT_PER)/100))
              else  M.DIS_AMT end))*(M.IGST)/100)) 	AS IGST,
                    
           ((((M.SPO_RATE*M.SPO_QTY)-(case when M.DIS_AMT = '0.00' then convert(numeric(14,2),((M.SPO_QTY*M.SPO_RATE*M.DISCOUNT_PER)/100))
              else  M.DIS_AMT end))*(M.CGST)/100)) 	AS CGST,
      
           ((((M.SPO_RATE*M.SPO_QTY)-(case when M.DIS_AMT = '0.00' then convert(numeric(14,2),((M.SPO_QTY*M.SPO_RATE*M.DISCOUNT_PER)/100))
              else  M.DIS_AMT end))*(M.SGST)/100)) 	AS SGST,


			  (((((M.SPO_RATE*M.SPO_QTY)-(case when M.DIS_AMT = '0.00' then convert(numeric(14,2),((M.SPO_QTY*M.SPO_RATE*M.DISCOUNT_PER)/100))
              else  M.DIS_AMT end))*(M.IGST)/100))+((((M.SPO_RATE*M.SPO_QTY)-(case when M.DIS_AMT = '0.00' then convert(numeric(14,2),((M.SPO_QTY*M.SPO_RATE*M.DISCOUNT_PER)/100))
              else  M.DIS_AMT end))*(M.CGST)/100))+((((M.SPO_RATE*M.SPO_QTY)-(case when M.DIS_AMT = '0.00' then convert(numeric(14,2),((M.SPO_QTY*M.SPO_RATE*M.DISCOUNT_PER)/100))
              else  M.DIS_AMT end))*(M.SGST)/100))) AS TOTAL_TAX,     
     

            CL.CT AS CALCULATION_TEMP_AMOUNT,
      
                ((M.SPO_QTY*M.SPO_RATE)+(case when M.CGST IS not NULL then convert(numeric(14,2),(((M.SPO_QTY*M.SPO_RATE-case when M.DIS_AMT = '0.00' then convert(numeric(14,2),((M.SPO_QTY*M.SPO_RATE*M.DISCOUNT_PER)/100))
            else  M.DIS_AMT end)*M.CGST)/100)) else  0 end)+(case when M.SGST IS not NULL then convert(numeric(14,2),(((M.SPO_QTY*M.SPO_RATE-case when M.DIS_AMT = '0.00' then convert(numeric(14,2),((M.SPO_QTY*M.SPO_RATE*M.DISCOUNT_PER)/100))
            else  M.DIS_AMT end)*M.SGST)/100)) else  0 end)+(case when M.IGST IS not NULL then convert(numeric(14,2),(((M.SPO_QTY*M.SPO_RATE-case when M.DIS_AMT = '0.00' then convert(numeric(14,2),((M.SPO_QTY*M.SPO_RATE*M.DISCOUNT_PER)/100))
            else  M.DIS_AMT end)*M.IGST)/100)) else  0 end)+
          (case when CL.CT IS not NULL then CL.CT else  0 end))-(case when M.DIS_AMT = '0.00' then convert(numeric(14,2),((M.SPO_QTY*M.SPO_RATE*M.DISCOUNT_PER)/100)) else  M.DIS_AMT end)
          as BILL_AMOUNT,
      
              CASE
                     WHEN H.STATUS ='A' THEN 'Approved'
                     WHEN H.STATUS = 'N' THEN 'Not Approved'
                     WHEN H.STATUS = 'c' THEN 'Cancelled'
                     WHEN H.STATUS = 'R' THEN 'Closed' 
                 END AS STATUS  
      
      
      FROM            TBL_TRN_PROR04_MAT AS M WITH (NOLOCK) LEFT OUTER JOIN
                               TBL_TRN_PROR04_HDR AS H WITH (NOLOCK) ON H.SPOID = M.SPOID_REF LEFT OUTER JOIN                        
                               TBL_MST_SUBLEDGER AS S WITH (NOLOCK) ON H.VID_REF = S.SGLID LEFT OUTER JOIN
                               TBL_MST_ITEM AS I WITH (NOLOCK) ON M.SERVICECODE = I.ITEMID LEFT OUTER JOIN
                               TBL_MST_BUSINESSUNIT AS B WITH (NOLOCK) ON I.BUID_REF = B.BUID LEFT OUTER JOIN
                               TBL_MST_UOM AS MU WITH (NOLOCK) ON M.UOMID_REF = MU.UOMID LEFT OUTER JOIN
                               TBL_MST_ITEMGROUP AS G WITH (NOLOCK) ON I.ITEMGID_REF = G.ITEMGID LEFT JOIN						
                               TBL_MST_BRANCH AS BR WITH (NOLOCK) ON H.BRID_REF=BR.BRID LEFT JOIN
                               TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT JOIN
                               TBL_MST_HSN AS HSN WITH (NOLOCK) ON I.HSNID_REF=HSN.HSNID LEFT JOIN
                               TBL_MST_VENDOR AS V WITH (NOLOCK) ON H.VID_REF=V.SLID_REF LEFT JOIN
                               TBL_MST_VENDORGROUP AS VG WITH (NOLOCK) ON V.VGID_REF=VG.VGID LEFT JOIN
                               (SELECT K.SPOID_REF,SUM(ISNULL(K.VALUE,0)+(ISNULL(K.VALUE,0)*ISNULL(K.CGST,0)/100)+
                               (ISNULL(K.VALUE,0)*ISNULL(K.SGST,0)/100)+
                               (ISNULL(K.VALUE,0)*ISNULL(K.IGST,0)/100)) AS CT FROM TBL_TRN_PROR04_CAL K GROUP BY K.SPOID_REF)  AS CL ON H.SPOID=CL.SPOID_REF
      
      
      
      
      WHERE   H.STATUS='$this->STATUS'
      and (H.VID_REF IN ($SGLID)) 
      AND (I.ITEMGID_REF IN ($ITEMGID)) 
      AND (M.SERVICECODE IN ( $ITEMID))
      AND (H.CYID_REF = $this->CYID) 
      AND (H.BRID_REF IN ($BranchName))
      AND (H.SPO_DT BETWEEN '$this->From_Date' AND '$this->To_Date')"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
            'SPO No',
            'SPO Date',
            'Branch Group',
            'Branch Name',
            'Vendor Group',
            'Vendor Code',
            'Vendor Name',
            'SAP Vendor Code',
            'SAP Vendor Name', 		
			'Item Name',
		    'HSN Code',
            'UOM',
            'Business Unit',
			'ALPS Part No',
			'Customer Part No',
			'OEM Part No',
		    'Pending Qty',			
            'Consumed Qty',
            'Actual Qty',
            'SPO Rate',
            'Item Total',
            'Discount',
            'Taxable Amount',
            'IGST',
            'CGST',
            'SGST',
            'Total GST/Tax',
            'Other Charges (If Any)',
            'Bill Total',
            'Status', 
		
        ];
    }
}





