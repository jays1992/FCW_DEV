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
use App\Exports\IPIRegister;
use Maatwebsite\Excel\Facades\Excel;














class IPIRegister implements FromCollection, WithHeadings
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

       



      return collect( $data=DB::select("      SELECT
      H.PII_NO, 
      H.PII_DT,
      GH.VENDOR_BILLNO,
      GH.VENDOR_BILLDT,
      GH.BOE_NO,
      GH.BOE_DATE,
      GH.COUNTRY_ORIGIN,
      GH.PORT_DETAIL,
      GH.AIRBILL_NO,
      GH.AIR_DATE,
      GH.FREIGHT_TERMS,
      GH.CARRIERF_AGENT,
      GH.EWAY_BILLNO,
      GH.EWAY_BILLDATE,
      GH.TOTAL_BOXES,
      GH.TRUCK_SEAL_NO,
      GRNH.GRN_NO,
      GRNH.GRN_DT,
      GH.GE_NO,
      GH.GE_DT,
	  GH.PO_NO,
      BG.BG_DESC AS BRANCH_GROUP,
      BR.BRNAME,
      VG.DESCRIPTIONS AS VENDOR_GROUP,
      S.SGLCODE,
      S.SLNAME,
      V.SAP_VENDOR_CODE,
      V.SAP_VENDOR_NAME1,
      I.NAME AS ITEM_NAME,
      HSN.HSNCODE,
      HSN.HSNDESCRIPTION AS HSN,
      MU.UOMCODE,
      B.BUNAME,
      I.ALPS_PART_NO AS ALPS_PART_NO,
      I.CUSTOMER_PART_NO AS CUSTOMER_PART_NO,
      I.OEM_PART_NO AS OEM_PART_NO,
      M.RATE_ASP_MU,
      M.RECEIVED_MAIN_QTY,
      (M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU) AS ItemTotal,

(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
      else  M.DISC_AMT end) AS Discount,
(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
else  M.DISC_AMT end) AS IPOAmount,
M.FREIGHT_AMT,
M.INSURANCE_AMT,      
((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100))
else  M.DISC_AMT end)+((case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end))+((case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))) AS AssessableAmount,

(((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*
(case when M.CUSTOME_DUTY_RATE IS not NULL then (( M.CUSTOME_DUTY_RATE)/100) else  0 end)) AS CUSTOMEDUTYRATE,

(((M.RATE_ASP_MU*M.RECEIVED_MAIN_QTY)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*
(case when M.CUSTOME_DUTY_RATE IS not NULL then (( M.CUSTOME_DUTY_RATE)/100) else  0 end)*
(case when M.SWS_RATE IS not NULL then (( M.SWS_RATE)/100) else  0 end)) AS SWSRATE,



(((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*
(case when M.CUSTOME_DUTY_RATE IS not NULL then (( M.CUSTOME_DUTY_RATE)/100) else  0 end) +
                  
((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*
(case when M.CUSTOME_DUTY_RATE IS not NULL then (( M.CUSTOME_DUTY_RATE)/100) else  0 end)*
(case when M.SWS_RATE IS not NULL then (( M.SWS_RATE)/100) else  0 end)) AS TotalCustomDuty,

(((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))+

((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*
(case when M.CUSTOME_DUTY_RATE IS not NULL then (( M.CUSTOME_DUTY_RATE)/100) else  0 end)+

((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*
(case when M.CUSTOME_DUTY_RATE IS not NULL then (( M.CUSTOME_DUTY_RATE)/100) else  0 end)*
(case when M.SWS_RATE IS not NULL then (( M.SWS_RATE)/100) else  0 end)) AS TaxableAmount,


((((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))+

((((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*
(case when M.CUSTOME_DUTY_RATE IS not NULL then (( M.CUSTOME_DUTY_RATE)/100) else  0 end))+
                  
((((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*
(case when M.CUSTOME_DUTY_RATE IS not NULL then (( M.CUSTOME_DUTY_RATE)/100) else  0 end))*
(case when M.SWS_RATE IS not NULL then (( M.SWS_RATE)/100) else  0 end))))*(case when M.IGST IS not NULL then (( M.IGST)/100) else  0 end)) AS IGST,				


((((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))+

((((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*
(case when M.CUSTOME_DUTY_RATE IS not NULL then (( M.CUSTOME_DUTY_RATE)/100) else  0 end))+
                  
((((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*
(case when M.CUSTOME_DUTY_RATE IS not NULL then (( M.CUSTOME_DUTY_RATE)/100) else  0 end))*
(case when M.SWS_RATE IS not NULL then (( M.SWS_RATE)/100) else  0 end))))+


((((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))+

((((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*
(case when M.CUSTOME_DUTY_RATE IS not NULL then (( M.CUSTOME_DUTY_RATE)/100) else  0 end))+

((((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*
(case when M.CUSTOME_DUTY_RATE IS not NULL then (( M.CUSTOME_DUTY_RATE)/100) else  0 end))*
(case when M.SWS_RATE IS not NULL then (( M.SWS_RATE)/100) else  0 end))))*(case when M.IGST IS not NULL then (( M.IGST)/100) else  0 end))) AS TotalAmount,					  			 		  		  		 	   		

CT.CT,
TDS.TDS,


((((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))+

((((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*
(case when M.CUSTOME_DUTY_RATE IS not NULL then (( M.CUSTOME_DUTY_RATE)/100) else  0 end))+
                  
((((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*
(case when M.CUSTOME_DUTY_RATE IS not NULL then (( M.CUSTOME_DUTY_RATE)/100) else  0 end))*
(case when M.SWS_RATE IS not NULL then (( M.SWS_RATE)/100) else  0 end))))+


((((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))+

((((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*
(case when M.CUSTOME_DUTY_RATE IS not NULL then (( M.CUSTOME_DUTY_RATE)/100) else  0 end))+

((((M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU)-(case when M.DISC_AMT = '0.00' then convert(numeric(14,2),(M.RECEIVED_MAIN_QTY*M.RATE_ASP_MU*M.DISC_PER)/100)else  M.DISC_AMT end)+
(case when M.FREIGHT_AMT IS not NULL then M.FREIGHT_AMT else  0 end)+(case when M.INSURANCE_AMT IS not NULL then M.INSURANCE_AMT else  0 end))*
(case when M.CUSTOME_DUTY_RATE IS not NULL then (( M.CUSTOME_DUTY_RATE)/100) else  0 end))*
(case when M.SWS_RATE IS not NULL then (( M.SWS_RATE)/100) else  0 end))))*(case when M.IGST IS not NULL then (( M.IGST)/100) else  0 end))+
(case when CT.CT IS not NULL then (( CT.CT)/100) else  0 end)-(case when TDS.TDS IS not NULL then (( TDS.TDS)/100) else  0 end)) AS BillTotal,							   				 			  

CASE
WHEN H.STATUS ='A' THEN 'Approved'
WHEN H.STATUS = 'N' THEN 'Not Approved'
WHEN H.STATUS = 'c' THEN 'Cancelled'
WHEN H.STATUS = 'R' THEN 'Closed'

END AS STATUS			


FROM  TBL_TRN_PII_MAT AS M (NOLOCK) LEFT OUTER JOIN
TBL_TRN_PII_HDR AS H (NOLOCK) ON H.PII_ID = M.PII_ID_REF  LEFT OUTER JOIN						
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
TBL_TRN_IGRN02_HDR AS GRNH (NOLOCK) ON GRNH.GRNID = M.GRNID_REF  LEFT OUTER JOIN						
(SELECT GT.GRNID_REF,GT.GEID_REF
       FROM TBL_TRN_IGRN02_MAT GT GROUP BY GT.GRNID_REF,GT.GEID_REF)  AS GT ON GRNH.GRNID=GT.GRNID_REF	LEFT JOIN
       TBL_TRN_IMGE01_HDR AS GH (NOLOCK) ON GH.GEID = GT.GEID_REF  LEFT OUTER JOIN

(SELECT K.PII_ID_REF,SUM(ISNULL(K.VALUE,0)+(ISNULL(K.VALUE,0)*ISNULL(K.IGST,0)/100)) AS CT 
FROM TBL_TRN_PII_CAL K GROUP BY K.PII_ID_REF)  AS CT ON H.PII_ID=CT.PII_ID_REF	LEFT JOIN
(SELECT PII.PII_ID_REF,
SUM(IIF(W.TDS_EXEMP_LIMIT<PII.ASSESSABLE_VL_TDS AND PII.TDS_RATE>0,PII.ASSESSABLE_VL_TDS-W.TDS_EXEMP_LIMIT,0)*PII.TDS_RATE+
IIF(W.SURCHARGE_EXEMP_LIMIT<PII.ASSESSABLE_VL_SURCHAPGE AND PII.SURCHAPGE_RATE>0,PII.ASSESSABLE_VL_SURCHAPGE-W.SURCHARGE_EXEMP_LIMIT,0)*PII.SURCHAPGE_RATE+
IIF(W.SP_CESS_EXEMP_LIMIT<PII.ASSESSABLE_VL_SPCESS AND PII.SPCESS_RATE>0,PII.ASSESSABLE_VL_SPCESS-W.SP_CESS_EXEMP_LIMIT,0)*PII.SPCESS_RATE+
IIF(W.CESS_EXEMP_LIMIT<PII.ASSESSABLE_VL_CESS AND PII.CESS_RATE>0,PII.ASSESSABLE_VL_CESS-W.CESS_EXEMP_LIMIT,0)*PII.CESS_RATE) AS TDS
FROM TBL_TRN_PII_TDS PII JOIN TBL_MST_WITHHOLDING W ON PII.TDSID_REF=W.HOLDINGID GROUP BY PII.PII_ID_REF) AS TDS
ON H.PII_ID=TDS.PII_ID_REF

WHERE (H.STATUS = '$this->STATUS') 
AND (H.CYID_REF = $this->CYID) 
AND (H.BRID_REF in ( $BranchName)) 
AND (H.PII_DT BETWEEN '$this->From_Date' AND '$this->To_Date')
AND (H.VID_REF in ( $SGLID))
AND (I.ITEMGID_REF in ( $ITEMGID))
AND (M.ITEMID_REF in ( $ITEMID))"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
    

        
								

								
        return [
            'Invoice No',
            'Invoice Date',
			'Vendor Bill No',
			'Vendor Bill Date',
			'BOE No',
			'BOE Date',
			'Coutry Origin',
			'Port Details',
			'Airbill No',
			'Airbill Date',
			'Freight Terms',
			'Carrier Agent',
			'Eway Bill No',
			'Eway Bill Date',
			'Total Boxes',
			'Truck Seal No',
			'GRN No',
			'GRN Date',
			'GE No',
			'GE Date',
			'PO NO',
            'Branch Group',
            'Branch Name',
            'Vendor Group',
            'Vendor Code',
            'Vendor Name',
            'SAP Vendor Code',
            'SAP Vendor Name',
            'Item Name',
            'HSN Code',
			'HSN Code',
			'Uom',
			'Business Unit',
			'ALPS Part No',
			'Customer Part No',
			'OEM Part No',
			'RATE',
			'Order Quantity',
			'Item Total',
			'Discount',
			'IPO Amount',
			'Freight Amount',
			'Insurance Amount',
			'Assessable Amount',
			'Custom Duty Amount',
			'SWS Amount',
			'Total Custom Duty',
			'Taxable Amount',
			'IGST',
			'Total Amount',
			'Others Charges (If Any)',
			'TDS',
			'Bill Total',
			'Status',		
			
        ];


         
   
    }
}





