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















class SPIRegister implements FromCollection, WithHeadings
{


 function __construct($SGLID,$From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMGID,$ITEMID,$STATUS,$CYID,$SPOID,$SPIID) {
        $this->ITEMID = $ITEMID;
        $this->SGLID = $SGLID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->ITEMGID = $ITEMGID;
        $this->STATUS = $STATUS;
        $this->CYID = $CYID;
        $this->SPOID = $SPOID;
        $this->SPIID = $SPIID;
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
        $SPOID=implode(",",$this->SPOID);
        $SPIID=implode(",",$this->SPIID);
       



      return collect( $data=DB::select("SELECT H.SPI_NO, 
      H.SPI_DT,
      VG.VGCODE,
      VG.DESCRIPTIONS AS VENDER_GROUP,
      S.SGLCODE, 
      S.SLNAME,
      V.SAP_VENDOR_CODE,
      V.SAP_VENDOR_NAME1,
      I.NAME AS ITEM_NAME,
      SPO.SPO_NO,
      HSN.HSNCODE,
      MU.DESCRIPTIONS,
      B.BUNAME,
      I.ALPS_PART_NO,
      I.CUSTOMER_PART_NO,
      I.OEM_PART_NO,
      M.BILL_QTY,
      M.BILL_RATEPUOM,
      M.BILL_RATEPUOM*M.BILL_QTY as ITEM_TOTAL,
      case when M.DISCOUNT = '0.00' then convert(numeric(14,2),((M.BILL_QTY*M.BILL_RATEPUOM*M.DIS_PER)/100))
                  else  M.DISCOUNT end AS DISCOUNT,
            
                          (M.BILL_QTY*M.BILL_RATEPUOM)-(case when M.DISCOUNT = '0.00' then convert(numeric(14,2),((M.BILL_QTY*M.BILL_RATEPUOM*M.DIS_PER)/100))
                  else  M.DISCOUNT end) AS TAXABLE_AMOUNT,
      
                    ((((M.BILL_RATEPUOM*M.BILL_QTY)-(case when M.DISCOUNT = '0.00' then convert(numeric(14,2),((M.BILL_QTY*M.BILL_RATEPUOM*M.DIS_PER)/100))
                    else  M.DISCOUNT end))*(M.IGST)/100)) 	AS IGST,
                          
                 ((((M.BILL_RATEPUOM*M.BILL_QTY)-(case when M.DISCOUNT = '0.00' then convert(numeric(14,2),((M.BILL_QTY*M.BILL_RATEPUOM*M.DIS_PER)/100))
                    else  M.DISCOUNT end))*(M.CGST)/100)) 	AS CGST,
            
                 ((((M.BILL_RATEPUOM*M.BILL_QTY)-(case when M.DISCOUNT = '0.00' then convert(numeric(14,2),((M.BILL_QTY*M.BILL_RATEPUOM*M.DIS_PER)/100))
                    else  M.DISCOUNT end))*(M.SGST)/100)) 	AS SGST,
      
      
                    (((((M.BILL_RATEPUOM*M.BILL_QTY)-(case when M.DISCOUNT = '0.00' then convert(numeric(14,2),((M.BILL_QTY*M.BILL_RATEPUOM*M.DIS_PER)/100))
                    else  M.DISCOUNT end))*(M.IGST)/100))+((((M.BILL_RATEPUOM*M.BILL_QTY)-(case when M.DISCOUNT = '0.00' then convert(numeric(14,2),((M.BILL_QTY*M.BILL_RATEPUOM*M.DIS_PER)/100))
                    else  M.DISCOUNT end))*(M.CGST)/100))+((((M.BILL_RATEPUOM*M.BILL_QTY)-(case when M.DISCOUNT = '0.00' then convert(numeric(14,2),((M.BILL_QTY*M.BILL_RATEPUOM*M.DIS_PER)/100))
                    else  M.DISCOUNT end))*(M.SGST)/100))) AS TOTAL_TAX,     
           
      
                  CL.CT AS CALCULATION_TEMP_AMOUNT,
            
                      ((M.BILL_QTY*M.BILL_RATEPUOM)+(case when M.CGST IS not NULL then convert(numeric(14,2),(((M.BILL_QTY*M.BILL_RATEPUOM-case when M.DISCOUNT = '0.00' then convert(numeric(14,2),((M.BILL_QTY*M.BILL_RATEPUOM*M.DIS_PER)/100))
                  else  M.DISCOUNT end)*M.CGST)/100)) else  0 end)+(case when M.SGST IS not NULL then convert(numeric(14,2),(((M.BILL_QTY*M.BILL_RATEPUOM-case when M.DISCOUNT = '0.00' then convert(numeric(14,2),((M.BILL_QTY*M.BILL_RATEPUOM*M.DIS_PER)/100))
                  else  M.DISCOUNT end)*M.SGST)/100)) else  0 end)+(case when M.IGST IS not NULL then convert(numeric(14,2),(((M.BILL_QTY*M.BILL_RATEPUOM-case when M.DISCOUNT = '0.00' then convert(numeric(14,2),((M.BILL_QTY*M.BILL_RATEPUOM*M.DIS_PER)/100))
                  else  M.DISCOUNT end)*M.IGST)/100)) else  0 end)+
                (case when CL.CT IS not NULL then CL.CT else  0 end)+(case when TDS.TDS_AMT IS not NULL then TDS.TDS_AMT else  0 end))-(case when M.DISCOUNT = '0.00' then convert(numeric(14,2),((M.BILL_QTY*M.BILL_RATEPUOM*M.DIS_PER)/100)) else  M.DISCOUNT end)
                as BILL_AMOUNT,
            
                    CASE
                           WHEN H.STATUS ='A' THEN 'Approved'
                           WHEN H.STATUS = 'N' THEN 'Not Approved'
                           WHEN H.STATUS = 'c' THEN 'Cancelled'
                           WHEN H.STATUS = 'R' THEN 'Closed' 
                       END AS STATUS  
      
      
      
      FROM            TBL_TRN_PRPB02_SRV AS M WITH (NOLOCK) LEFT OUTER JOIN
      
                               TBL_TRN_PRPB02_HDR AS H WITH (NOLOCK) ON H.SPIID = M.SPIID_REF LEFT OUTER JOIN                        
                               TBL_MST_SUBLEDGER AS S WITH (NOLOCK) ON H.VID_REF = S.SGLID LEFT OUTER JOIN
                               TBL_MST_ITEM AS I WITH (NOLOCK) ON M.SRVID_REF = I.ITEMID LEFT OUTER JOIN
                               TBL_TRN_PROR04_HDR AS SPO WITH (NOLOCK) ON M.SPOID_REF = SPO.SPOID LEFT OUTER JOIN
                               TBL_MST_BUSINESSUNIT AS B WITH (NOLOCK) ON I.BUID_REF = B.BUID LEFT OUTER JOIN
                               TBL_MST_UOM AS MU WITH (NOLOCK) ON M.UOMID_REF = MU.UOMID LEFT OUTER JOIN
                               TBL_MST_ITEMGROUP AS G WITH (NOLOCK) ON I.ITEMGID_REF = G.ITEMGID LEFT JOIN						
                               TBL_MST_BRANCH AS BR WITH (NOLOCK) ON H.BRID_REF=BR.BRID LEFT JOIN
                               TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT JOIN
                               TBL_MST_HSN AS HSN WITH (NOLOCK) ON I.HSNID_REF=HSN.HSNID LEFT JOIN
                               TBL_MST_VENDOR AS V WITH (NOLOCK) ON H.VID_REF=V.SLID_REF LEFT JOIN
                               TBL_MST_VENDORGROUP AS VG WITH (NOLOCK) ON V.VGID_REF=VG.VGID LEFT JOIN
                               (SELECT K.SPIID_REF,SUM(ISNULL(K.VALUE,0)+(ISNULL(K.VALUE,0)*ISNULL(K.CGST,0)/100)+
                               (ISNULL(K.VALUE,0)*ISNULL(K.SGST,0)/100)+
                               (ISNULL(K.VALUE,0)*ISNULL(K.IGST,0)/100)) AS CT FROM TBL_TRN_PRPB02_CAL K GROUP BY K.SPIID_REF)  AS CL ON H.SPIID=CL.SPIID_REF
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
      
      
      WHERE   H.STATUS='$this->STATUS'
      AND  M.SPOID_REF IN ($SPOID) 
      AND H.SPIID IN ($SPIID) 
      AND (H.VID_REF IN ($SGLID)) 
      AND (I.ITEMGID_REF IN ($ITEMGID)) 
      AND (M.SRVID_REF IN ($ITEMID)) 
      AND (H.STATUS = '$this->STATUS') 
      AND (H.CYID_REF = $this->CYID) 
      AND (H.BRID_REF IN ($BranchName)) 
      AND (H.SPI_DT BETWEEN '$this->From_Date' AND '$this->To_Date') 
      
      "));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
            'SPI No',
            'SPI Date',
            'Vendor Group Code',
            'Vendor Group Name',
            'Vendor Code',
            'Vendor Name',
            'SP Vendor Code',
            'SP Vendor Name',
            'Service Name',
            'SPO No',
            'HSN Code',
            'HSN Descritpion',
            'Business Unit',
            'Alps Part No',
            'Customer Part No',
            'OEM Part No',
            'Bill Qty',
            'Bill Rate',
            'Item Total',
            'Discount',
            'Taxable Amount',
            'IGST',
            'CGST',
            'SGST',
            'Total Tax',
            'Other Charges (If Any)',
            'Bill Amount',
            'Status'
           ];
    }
}





