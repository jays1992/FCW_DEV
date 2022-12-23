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
use App\Exports\PurchaseReturnRegister;
use Maatwebsite\Excel\Facades\Excel;














class PurchaseReturnRegister implements FromCollection, WithHeadings
{


 function __construct($From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMID,$STATUS,$CYID_REF,$PRRID,$SGLID) {
        $this->ITEMID = $ITEMID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->STATUS = $STATUS;
        $this->CYID = $CYID_REF;
        $this->PRRID = $PRRID;
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
        $PRRID=implode(",",$this->PRRID);
        $SGLID=implode(",",$this->SGLID);
 
       



      return collect( $data=DB::select("SELECT   
      TBL_TRN_PRRT01_HDR.PRR_NO, 
      TBL_TRN_PRRT01_HDR.PRR_DT,
      BG.BG_DESC AS BRANCH_GROUP,
      BR.BRNAME,
      VG.DESCRIPTIONS AS VENDOR_GROUP,
      V.VCODE AS VENDOR_CODE,
      V.NAME AS VENDOR_NAME,
      V.SAP_VENDOR_CODE,
      V.SAP_VENDOR_NAME1,
      TBL_MST_STATE.NAME AS STATE,
      TBL_MST_ITEMGROUP.GROUPNAME,
      TBL_MST_ITEM.ICODE, 
      TBL_MST_ITEM.NAME AS ITEM_NAME,
      TBL_MST_ITEM.ALPS_PART_NO,
      TBL_MST_ITEM.CUSTOMER_PART_NO,
      TBL_MST_ITEM.OEM_PART_NO,
      VL. NAME,
      HSN.HSNCODE,
      TBL_MST_BUSINESSUNIT.BUNAME,      
      TBL_MST_UOM.DESCRIPTIONS AS UOM,      
      TBL_TRN_PRRT01_MAT.RETURN_QTY_MU AS ACTUAL_QTY,
      TBL_TRN_PRRT01_MAT.RATEPUOM_MU,
      ((TBL_TRN_PRRT01_MAT.RATEPUOM_MU*TBL_TRN_PRRT01_MAT.RETURN_QTY_MU))as Amount,
      
	  ((TBL_TRN_PRRT01_MAT.RATEPUOM_MU*TBL_TRN_PRRT01_MAT.RETURN_QTY_MU)*(TBL_TRN_PRRT01_MAT.IGST_RATE)/100)
          as IGST_RATE,
              
      ((TBL_TRN_PRRT01_MAT.RATEPUOM_MU*TBL_TRN_PRRT01_MAT.RETURN_QTY_MU)*(TBL_TRN_PRRT01_MAT.CGST_RATE)/100)
          as CGST_RATE,
           
        ((TBL_TRN_PRRT01_MAT.RATEPUOM_MU*TBL_TRN_PRRT01_MAT.RETURN_QTY_MU)*(TBL_TRN_PRRT01_MAT.SGST_RATE)/100)
          as SGST_RATE,

		   ((TBL_TRN_PRRT01_MAT.RATEPUOM_MU*TBL_TRN_PRRT01_MAT.RETURN_QTY_MU)*(TBL_TRN_PRRT01_MAT.IGST_RATE)/100)+ ((TBL_TRN_PRRT01_MAT.RATEPUOM_MU*TBL_TRN_PRRT01_MAT.RETURN_QTY_MU)*(TBL_TRN_PRRT01_MAT.CGST_RATE)/100)+ ((TBL_TRN_PRRT01_MAT.RATEPUOM_MU*TBL_TRN_PRRT01_MAT.RETURN_QTY_MU)*(TBL_TRN_PRRT01_MAT.SGST_RATE)/100) AS TotalTax,
              
    
               
      ((TBL_TRN_PRRT01_MAT.RATEPUOM_MU*TBL_TRN_PRRT01_MAT.RETURN_QTY_MU)+(case when TBL_TRN_PRRT01_MAT.CGST_RATE IS not NULL then convert(numeric(14,2),((TBL_TRN_PRRT01_MAT.RETURN_QTY_MU*TBL_TRN_PRRT01_MAT.RATEPUOM_MU*TBL_TRN_PRRT01_MAT.CGST_RATE)/100)) else  0 end)+
      (case when TBL_TRN_PRRT01_MAT.SGST_RATE IS not NULL then convert(numeric(14,2),((TBL_TRN_PRRT01_MAT.RETURN_QTY_MU*TBL_TRN_PRRT01_MAT.RATEPUOM_MU*TBL_TRN_PRRT01_MAT.SGST_RATE)/100)) else  0 end)+
      (case when TBL_TRN_PRRT01_MAT.IGST_RATE IS not NULL then convert(numeric(14,2),((TBL_TRN_PRRT01_MAT.RETURN_QTY_MU*TBL_TRN_PRRT01_MAT.RATEPUOM_MU*TBL_TRN_PRRT01_MAT.IGST_RATE)/100)) else  0 end)+
      (case when CT.AMOUNT IS not NULL then CT.AMOUNT else  0 end))
      as AmtafterTax,
      
      CT.AMOUNT AS CALCULATION,          
      CASE
      WHEN TBL_TRN_PRRT01_HDR.STATUS ='A' THEN 'Approved'
      WHEN TBL_TRN_PRRT01_HDR.STATUS = 'N' THEN 'Not Approved'
      WHEN TBL_TRN_PRRT01_HDR.STATUS = 'c' THEN 'Cancelled'
      WHEN TBL_TRN_PRRT01_HDR.STATUS = 'R' THEN 'Closed'
                      
      END AS STATUS 
      FROM              TBL_TRN_PRRT01_HDR LEFT OUTER JOIN
                        TBL_TRN_PRRT01_MAT ON TBL_TRN_PRRT01_HDR.PRRID = TBL_TRN_PRRT01_MAT.PRRID_REF LEFT OUTER JOIN
                        TBL_MST_SUBLEDGER (NOLOCK) ON TBL_TRN_PRRT01_HDR.VID_REF = TBL_MST_SUBLEDGER.SGLID LEFT OUTER JOIN 
                        TBL_MST_VENDOR AS V ON TBL_MST_SUBLEDGER.SGLID = V.SLID_REF LEFT OUTER JOIN
                        TBL_MST_VENDORGROUP AS VG ON V.VGID_REF = VG.VGID LEFT OUTER JOIN
                        TBL_MST_BRANCH AS BR WITH (NOLOCK) ON TBL_TRN_PRRT01_HDR.BRID_REF=BR.BRID LEFT OUTER JOIN
                        TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT OUTER JOIN
                        TBL_MST_VENDOR ON TBL_MST_SUBLEDGER.SGLID = TBL_MST_VENDOR.SLID_REF LEFT OUTER JOIN
                        TBL_MST_CITY ON TBL_MST_VENDOR.REGCITYID_REF = TBL_MST_CITY.CITYID LEFT OUTER JOIN
                        TBL_MST_STATE ON TBL_MST_VENDOR.REGSTID_REF = TBL_MST_STATE.STID LEFT OUTER JOIN
                        TBL_MST_VENDORLOCATION AS VL ON TBL_TRN_PRRT01_HDR.BILLTO = VL.LID LEFT OUTER JOIN
                        TBL_MST_ITEM ON TBL_TRN_PRRT01_MAT.ITEMID_REF = TBL_MST_ITEM.ITEMID LEFT OUTER JOIN
                        TBL_MST_BUSINESSUNIT ON TBL_MST_ITEM.BUID_REF = TBL_MST_BUSINESSUNIT.BUID LEFT OUTER JOIN
                        TBL_MST_HSN AS HSN WITH (NOLOCK) ON TBL_MST_ITEM.HSNID_REF=HSN.HSNID LEFT OUTER JOIN
                        TBL_MST_ITEMGROUP ON TBL_MST_ITEM.ITEMGID_REF = TBL_MST_ITEMGROUP.ITEMGID LEFT OUTER JOIN
                        TBL_MST_UOM ON TBL_MST_ITEM.MAIN_UOMID_REF = TBL_MST_UOM.UOMID LEFT OUTER JOIN
      
                            (SELECT     PRRID_REF, SUM(VALUE) + SUM(VALUE * IGST / 100) + SUM(VALUE * CGST / 100) + SUM(VALUE * SGST / 100) AS AMOUNT
                             FROM        TBL_TRN_PRRT01_CAL
                             GROUP BY PRRID_REF) AS CT ON TBL_TRN_PRRT01_MAT.PRRID_REF = CT.PRRID_REF
      WHERE       TBL_TRN_PRRT01_HDR.STATUS='$this->STATUS' 
      AND TBL_TRN_PRRT01_HDR.CYID_REF=$this->CYID
      AND TBL_TRN_PRRT01_HDR.BRID_REF IN ($BranchName)
      AND (TBL_TRN_PRRT01_HDR.PRR_DT BETWEEN '$this->From_Date' AND '$this->To_Date')
      AND TBL_TRN_PRRT01_HDR.VID_REF IN ($SGLID)
      AND TBL_MST_ITEM.ITEMID IN ( $ITEMID)
      AND TBL_TRN_PRRT01_HDR.PRRID IN ($PRRID)  "));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
            'Purchase Return No',
            'Purchase Return Date',
            'Branch Group',
            'Branch Name',
            'Vendor Group',
            'Vendor Code',
            'Vendor Name',
            'SAP Vendor Code',
            'SAP Vendor Name',
            'State', 		
			'Item Group Name',
			'Item Code',
			'Item Name',
			'ALPS Part No',
			'Customer Part No',
			'OEM Part No',
			'Billing Address',			
			'HSN/SAC Code',
			'Business Unit',		
		    'UOM',		
			'Return Qty',
			'Rate Per UOM',
			'Amount',
			'IGST',
			'CGST',
			'SGST',
			'Total TAX ',
			'Amount After TAX',
			'Calculation',		
			'Status'			
        ];
    }
}





