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
use App\Exports\IPORegister;
use Maatwebsite\Excel\Facades\Excel;














class IPORegister implements FromCollection, WithHeadings
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
      H.IPO_NO, 
      H.IPO_DT,
      VG.VGCODE,
      VG.DESCRIPTIONS AS VENDOR_GROUP ,
      S.SGLCODE, S.SLNAME, 
      V.SAP_VENDOR_CODE,
       V.SAP_VENDOR_NAME1,
       BG.BG_DESC AS BRANCH_GROUP, 
        BR.BRNAME,
        I.NAME AS ITEM_NAME,
        HSN.HSNCODE,
        MU.DESCRIPTIONS,
        B.BUNAME,
        I.ALPS_PART_NO AS ALPS_PART_NO,
        I.CUSTOMER_PART_NO AS CUSTOMER_PART_NO,
        I.OEM_PART_NO AS OEM_PART_NO,
        M.RATE_ASP_MU AS RATE,
        M.PENDING_QTY,
        (M.IPO_MAIN_QTY-M.PENDING_QTY) AS CONSUMED_QTY,
        M.IPO_MAIN_QTY, 
        (M.IPO_MAIN_QTY*M.RATE_ASP_MU) AS ITEM_AMOUNT, 
            case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end AS DISCOUNT,
      
                     (M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end) AS AMOUNT_AFTER_DISCOUNT,
            M.FREIGHT_AMT,
            M.INSURANCE_AMT,
      
      
            
      
            (M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end) AS Assessable_Amount,
      
            
            ((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*(case when M.CUSTOME_DUTY_RATE IS not NULL then M.CUSTOME_DUTY_RATE/100 else  0 end) AS CUSTOM_DUTY,
      
          
          ((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*(case when M.CUSTOME_DUTY_RATE IS not NULL then M.CUSTOME_DUTY_RATE/100 else  0 end)*(case when M.SWS_RATE IS not NULL then M.SWS_RATE/100 else  0 end) AS SWS_RATE,
      
          ((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*(case when M.CUSTOME_DUTY_RATE IS not NULL then M.CUSTOME_DUTY_RATE/100 else  0 end)*(case when M.SWS_RATE IS not NULL then M.SWS_RATE/100 else  0 end)+((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*(case when M.CUSTOME_DUTY_RATE IS not NULL then M.CUSTOME_DUTY_RATE/100 else  0 end) AS TOTAL_CUSTOM_DUTY,
      
                  ((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end)+((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*(case when M.CUSTOME_DUTY_RATE IS not NULL then M.CUSTOME_DUTY_RATE/100 else  0 end)*(case when M.SWS_RATE IS not NULL then M.SWS_RATE/100 else  0 end)+((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*(case when M.CUSTOME_DUTY_RATE IS not NULL then M.CUSTOME_DUTY_RATE/100 else  0 end)) as TAXABLE_AMOUNTS,

      
      (((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end)+((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*(case when M.CUSTOME_DUTY_RATE IS not NULL then M.CUSTOME_DUTY_RATE/100 else  0 end)*(case when M.SWS_RATE IS not NULL then M.SWS_RATE/100 else  0 end)+((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*(case when M.CUSTOME_DUTY_RATE IS not NULL then M.CUSTOME_DUTY_RATE/100 else  0 end))*(case when M.IGST IS not NULL then M.IGST/100 else  0 end)) AS IGST,
      
      
            ((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end)+((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*(case when M.CUSTOME_DUTY_RATE IS not NULL then M.CUSTOME_DUTY_RATE/100 else  0 end)*(case when M.SWS_RATE IS not NULL then M.SWS_RATE/100 else  0 end)+((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*(case when M.CUSTOME_DUTY_RATE IS not NULL then M.CUSTOME_DUTY_RATE/100 else  0 end)+((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end)+((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*(case when M.CUSTOME_DUTY_RATE IS not NULL then M.CUSTOME_DUTY_RATE/100 else  0 end)*(case when M.SWS_RATE IS not NULL then M.SWS_RATE/100 else  0 end)+((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*(case when M.CUSTOME_DUTY_RATE IS not NULL then M.CUSTOME_DUTY_RATE/100 else  0 end))*(case when M.IGST IS not NULL then M.IGST/100 else  0 end)) AS TOTAL_AMOUNT,
      
            CT.CT,
            TDS.TDS,
      
              (((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end)+((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*(case when M.CUSTOME_DUTY_RATE IS not NULL then M.CUSTOME_DUTY_RATE/100 else  0 end)*(case when M.SWS_RATE IS not NULL then M.SWS_RATE/100 else  0 end)+((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*(case when M.CUSTOME_DUTY_RATE IS not NULL then M.CUSTOME_DUTY_RATE/100 else  0 end)+((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end)+((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*(case when M.CUSTOME_DUTY_RATE IS not NULL then M.CUSTOME_DUTY_RATE/100 else  0 end)*(case when M.SWS_RATE IS not NULL then M.SWS_RATE/100 else  0 end)+((M.IPO_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.IPO_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
                  else  M.DISC_AMT end)+(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*(case when M.CUSTOME_DUTY_RATE IS not NULL then M.CUSTOME_DUTY_RATE/100 else  0 end))*(case when M.IGST IS not NULL then M.IGST/100 else  0 end) +  (case when CT.CT IS not NULL then CT.CT else  0 end))-(case when TDS.TDS IS not NULL then TDS.TDS else  0 end)) AS BILL_TOTAL,
				H.REQ_DELIVERY_DATE,COM.CINV_STATUS,
               CASE
                WHEN H.STATUS ='A' THEN 'Approved'
                WHEN H.STATUS = 'N' THEN 'Not Approved'
                WHEN H.STATUS = 'c' THEN 'Cancelled'
                WHEN H.STATUS = 'R' THEN 'Closed'                      
                END AS STATUS
      
      
      FROM            TBL_TRN_IPO_MAT AS M (NOLOCK) LEFT OUTER JOIN
                               TBL_TRN_IPO_HDR AS H (NOLOCK) ON H.IPO_ID = M.IPO_ID_REF  LEFT OUTER JOIN	
							   TBL_TRN_COM_INV_HDR AS COM (NOLOCK) ON H.IPO_ID = COM.IPO_ID_REF LEFT OUTER JOIN
                               TBL_MST_SUBLEDGER AS S (NOLOCK) ON H.VID_REF = S.SGLID LEFT OUTER JOIN
                               TBL_MST_ITEM AS I (NOLOCK) ON M.ITEMID_REF = I.ITEMID LEFT OUTER JOIN
                   TBL_MST_BUSINESSUNIT AS B (NOLOCK) ON I.BUID_REF = B.BUID LEFT OUTER JOIN
                               TBL_MST_UOM AS MU (NOLOCK)  ON M.MAIN_UOMID_REF = MU.UOMID LEFT OUTER JOIN
                               TBL_MST_VENDORLOCATION AS CL  (NOLOCK) ON H.BILL_TO = CL.LID LEFT OUTER JOIN
                               TBL_MST_VENDORLOCATION AS CL1 (NOLOCK) ON H.SHIP_TO = CL1.LID LEFT OUTER JOIN
                               TBL_MST_ITEMGROUP AS G (NOLOCK) ON I.ITEMGID_REF = G.ITEMGID LEFT JOIN
                   TBL_MST_BRANCH AS BR WITH (NOLOCK) ON H.BRID_REF=BR.BRID LEFT JOIN
                   TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT JOIN
                   TBL_MST_HSN AS HSN WITH (NOLOCK) ON I.HSNID_REF=HSN.HSNID LEFT JOIN
                   TBL_MST_VENDOR AS V WITH (NOLOCK) ON H.VID_REF=V.SLID_REF LEFT JOIN
                   TBL_MST_VENDORGROUP AS VG WITH (NOLOCK) ON V.VGID_REF=VG.VGID LEFT JOIN
                   (SELECT K.IPO_ID_REF,SUM(ISNULL(K.VALUE,0)+(ISNULL(K.VALUE,0)*ISNULL(K.IGST,0)/100)) AS CT 
                   FROM TBL_TRN_IPO_CAL K GROUP BY K.IPO_ID_REF)  AS CT ON H.IPO_ID=CT.IPO_ID_REF	LEFT JOIN
                   (SELECT IPO.IPO_ID_REF,
                  SUM(IIF(W.TDS_EXEMP_LIMIT<IPO.ASSESSABLE_VL_TDS AND IPO.TDS_RATE>0,IPO.ASSESSABLE_VL_TDS-W.TDS_EXEMP_LIMIT,0)*IPO.TDS_RATE+
                  IIF(W.SURCHARGE_EXEMP_LIMIT<IPO.ASSESSABLE_VL_SURCHAPGE AND IPO.SURCHAPGE_RATE>0,IPO.ASSESSABLE_VL_SURCHAPGE-W.SURCHARGE_EXEMP_LIMIT,0)*IPO.SURCHAPGE_RATE+
                  IIF(W.SP_CESS_EXEMP_LIMIT<IPO.ASSESSABLE_VL_SPCESS AND IPO.SPCESS_RATE>0,IPO.ASSESSABLE_VL_SPCESS-W.SP_CESS_EXEMP_LIMIT,0)*IPO.SPCESS_RATE+
                  IIF(W.CESS_EXEMP_LIMIT<IPO.ASSESSABLE_VL_CESS AND IPO.CESS_RATE>0,IPO.ASSESSABLE_VL_CESS-W.CESS_EXEMP_LIMIT,0)*IPO.CESS_RATE) AS TDS
                  FROM TBL_TRN_IPO_TDS IPO JOIN TBL_MST_WITHHOLDING W ON IPO.TDSID_REF=W.HOLDINGID GROUP BY IPO.IPO_ID_REF) AS TDS
                  ON H.IPO_ID=TDS.IPO_ID_REF
      WHERE        (H.STATUS = '$this->STATUS') 
      AND (H.CYID_REF = $this->CYID) 
      AND (H.BRID_REF in (  $BranchName)) 
      AND (H.IPO_DT BETWEEN '$this->From_Date' AND '$this->To_Date')
      AND (H.VID_REF in ( $SGLID))
      AND (I.ITEMGID_REF in ( $ITEMGID))
      AND (M.ITEMID_REF in ( $ITEMID))"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
    

        return [
          'IPO No',
          'IPO Date',
          'Vendor Group Code',
          'Vendor Group Name',
          'Vendor Code',
          'Vendor Name',
          'SP Vendor Code',
          'SP Vendor Name',
         'Branch Group',
         'Branch Name',
         'Item Name',
          'HSN Code',
          'HSN Descritpion',
          'Business Unit',
          'Alps Part No',
          'Customer Part No',
          'OEM Part No',
          'Rate',
         'Pending Qty',
         'Consumed Qty',
         'Actual Qty',
         'Item Amount',
         'Discount',
         'Amount After Amount',
         'Freight Amount',
         'Insurance Amount',
         'Assessable Amount',
         'Custom Duty',
         'SWS Rate',
         'Total Custom Duty',
         'Taxable Amount',
           'IGST',
         'Total Amount',
         'Other Charges(If Any)',
         'TDS',
         'Bill Total' ,
		 'Estimated Arrival Date' ,
		 'Commercial Invoice Status',
          'Status'
         ];
         
         
   
    }
}





