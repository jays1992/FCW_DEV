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
use App\Exports\QualityInspectionRegister;
use Maatwebsite\Excel\Facades\Excel;














class QualityInspectionRegister implements FromCollection, WithHeadings
{


 function __construct($SGLID,$From_Date,$To_Date,$BranchGroup,$BranchName,$GRNID,$STATUS,$CYID_REF) {
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->GRNID = $GRNID;
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
        
         
        $BranchName=implode(",",$this->BranchName);      
        $GRNID=implode(",",$this->GRNID);
        $SGLID=implode(",",$this->SGLID);

       



      return collect( $data=DB::select("SELECT  TBL_TRN_IGRN02_HDR.GRN_NO, 
      TBL_TRN_IGRN02_HDR.GRN_DT,  
      BG.BG_DESC AS BRANCH_GROUP, 
      BR.BRNAME,     
      VG.DESCRIPTIONS AS VENDOR_GROUP,     
      V.VCODE AS VENDOR_CODE,      
      V.NAME AS VENDOR_NAME,
      V.SAP_VENDOR_CODE,
      V.SAP_VENDOR_NAME1, 
      TBL_MST_STATE.NAME AS State_Name, 
      TBL_MST_CITY.NAME AS City_Name, 
      TBL_TRN_IMGE01_HDR.GE_NO,
      TBL_MST_ITEMGROUP.GROUPNAME AS Item_Group_Name, 
      TBL_MST_ITEM.NAME AS Item_Name,
      TBL_MST_ITEM.ALPS_PART_NO,TBL_MST_ITEM.CUSTOMER_PART_NO,TBL_MST_ITEM.OEM_PART_NO,
      HSN.HSNCODE, 
      TBL_MST_UOM_1.DESCRIPTIONS AS ALT_UOM,
      TBL_TRN_IGRN02_MAT.RECEIVED_QTY_MU-CASE WHEN ISNULL(PO.BILL_QTY,ISNULL(IPI.BILL_QTY,0.000)) IS NULL 
            THEN '0.000' ELSE
            ISNULL(PO.BILL_QTY,ISNULL(IPI.BILL_QTY,0.000)) END AS PENDING_QTY,
            CASE WHEN ISNULL(PO.BILL_QTY,ISNULL(IPI.BILL_QTY,0.000)) IS NULL 
            THEN '0.000' ELSE
            ISNULL(PO.BILL_QTY,ISNULL(IPI.BILL_QTY,0.000)) END AS CONSUMED_QTY,
      TBL_TRN_IGRN02_MAT.RECEIVED_QTY_MU,
	    TBL_TRN_IGRN02_MAT.RATE,TBL_TRN_IGRN02_MAT.RECEIVED_QTY_MU*TBL_TRN_IGRN02_MAT.RATE AS AMOUNT,
      TBL_TRN_IGRN02_MAT.SHORT_QTY,
      TBL_TRN_IGRN02_MAT.REMARKS,
	  TBL_TRN_QIG_HDR.QIGNO,
      TBL_TRN_QIG_HDR.QI_PICK_QTY,
      TBL_TRN_QIG_HDR.REJECTED_QTY,
      TBL_TRN_QIG_HDR.QC_OK_QTY,          
          CASE
          WHEN TBL_TRN_QIG_HDR.STATUS ='A' THEN 'Approved'
          WHEN TBL_TRN_QIG_HDR.STATUS = 'N' THEN 'Not Approved'
          WHEN TBL_TRN_QIG_HDR.STATUS = 'c' THEN 'Cancelled'
          WHEN TBL_TRN_QIG_HDR.STATUS = 'R' THEN 'Closed'   
          END AS STATUS 			 	
          
          FROM      TBL_TRN_IGRN02_HDR LEFT OUTER JOIN
                    TBL_MST_SUBLEDGER (NOLOCK) ON TBL_TRN_IGRN02_HDR.VID_REF = TBL_MST_SUBLEDGER.SGLID LEFT OUTER JOIN 
                    TBL_MST_VENDOR AS V ON TBL_MST_SUBLEDGER.SGLID = V.SLID_REF LEFT OUTER JOIN
                    TBL_MST_VENDORGROUP AS VG ON V.VGID_REF = VG.VGID LEFT OUTER JOIN
                    TBL_MST_BRANCH AS BR WITH (NOLOCK) ON TBL_TRN_IGRN02_HDR.BRID_REF=BR.BRID LEFT OUTER JOIN
                        TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT OUTER JOIN
                        TBL_MST_COMPANY ON TBL_TRN_IGRN02_HDR.CYID_REF = TBL_MST_COMPANY.CYID LEFT OUTER JOIN
                        TBL_MST_STATE ON V.REGSTID_REF = TBL_MST_STATE.STID LEFT OUTER JOIN
                        TBL_MST_CITY ON V.REGCITYID_REF = TBL_MST_CITY.CITYID LEFT OUTER JOIN
                        TBL_TRN_IGRN02_MAT ON TBL_TRN_IGRN02_HDR.GRNID = TBL_TRN_IGRN02_MAT.GRNID_REF LEFT OUTER JOIN
                        TBL_TRN_QIG_HDR ON TBL_TRN_IGRN02_HDR.GRNID = TBL_TRN_QIG_HDR.GRNID_REF LEFT OUTER JOIN
                        TBL_TRN_IMGE01_HDR ON TBL_TRN_IGRN02_MAT.GEID_REF = TBL_TRN_IMGE01_HDR.GEID LEFT OUTER JOIN
            TBL_TRN_PRPB01_MAT AS PO   ON ISNULL(TBL_TRN_IGRN02_MAT.POID_REF,TBL_TRN_IGRN02_MAT.BPOID_REF) = PO.POID_REF 
						 AND  TBL_TRN_IGRN02_MAT.ITEMID_REF = PO.ITEMID_REF AND TBL_TRN_IGRN02_MAT.MAIN_UOMID_REF = PO.UOMID_REF 
						 AND TBL_TRN_IGRN02_MAT.MRSID_REF = PO.MRSID_REF AND TBL_TRN_IGRN02_MAT.PIID_REF = PO.PIID_REF AND TBL_TRN_IGRN02_MAT.RFQID_REF = PO.RFQID_REF 
						 AND TBL_TRN_IGRN02_MAT.VQID_REF = PO.VQID_REF  AND TBL_TRN_IGRN02_HDR.GE_TYPE IN('PO','BPO') 
						 LEFT OUTER JOIN TBL_TRN_PII_MAT AS IPI  ON TBL_TRN_IGRN02_MAT.GRNID_REF = IPI.GRNID_REF and  TBL_TRN_IGRN02_MAT.IPOID_REF = IPI.IPO_ID_REF
						 AND  TBL_TRN_IGRN02_MAT.ITEMID_REF = IPI.ITEMID_REF AND TBL_TRN_IGRN02_MAT.MAIN_UOMID_REF = IPI.MAIN_UOMID_REF
						 AND TBL_TRN_IGRN02_HDR.GE_TYPE ='IPO' LEFT OUTER JOIN
            TBL_MST_ITEM ON TBL_TRN_IGRN02_MAT.ITEMID_REF = TBL_MST_ITEM.ITEMID LEFT OUTER JOIN
            TBL_MST_HSN AS HSN WITH (NOLOCK) ON TBL_MST_ITEM.HSNID_REF=HSN.HSNID LEFT OUTER JOIN
                        TBL_MST_ITEMGROUP ON TBL_MST_ITEM.ITEMGID_REF = TBL_MST_ITEMGROUP.ITEMGID LEFT OUTER JOIN
                        TBL_MST_UOM ON TBL_MST_ITEM.MAIN_UOMID_REF = TBL_MST_UOM.UOMID LEFT OUTER JOIN
                        TBL_MST_UOM AS TBL_MST_UOM_1 ON TBL_MST_ITEM.ALT_UOMID_REF = TBL_MST_UOM_1 .UOMID
     
     WHERE TBL_TRN_QIG_HDR.QIGNO IS NOT NULL AND TBL_TRN_IGRN02_HDR.CYID_REF=$this->CYID AND TBL_TRN_IGRN02_HDR.BRID_REF IN ($BranchName) AND TBL_TRN_IGRN02_HDR.GRN_DT BETWEEN '$this->From_Date' AND '$this->To_Date' AND TBL_TRN_IGRN02_HDR.VID_REF IN ($SGLID) AND TBL_TRN_IGRN02_HDR.GRNID IN ($GRNID)
     AND TBL_TRN_QIG_HDR.STATUS='$this->STATUS'"));
         
      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
    

        return [
          'GRN No',
          'GRN No',
          'Branch Group',
          'Branch Name',
          'Vendor Group',
          'Vendor Code',
          'Vendor Name',
          'SAP Vendor Code',
          'SAP Vendor Name',
          'State Name',
          'City Name',
          'GE NO',          
          'Item Group',          
          'Item Name',          
           'Alps Part No',
           'Customer Part No',
           'OEM Part No',
          'HSN Code',
          'ALT UOM',
          'Pending Qty',
          'Consumed Qty',
          'Received Qty',
          'Rate',
          'Amount',
          'Short Qty',
          'Remarks',    
          'Quality Inspection No',    
          'Sample Qty',    
          'Rejected Qty',    
          'Accepted Qty',    
          'Status' 
          ];
          
   
    }
}





