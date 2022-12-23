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
use App\Exports\PurchaseBillSummary;
use Maatwebsite\Excel\Facades\Excel;







class PurchaseBillSummary implements FromCollection, WithHeadings
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
      TBL_TRN_PRPB01_HDR.PB_DOCNO AS DOC_NO,
      TBL_TRN_PRPB01_HDR.PB_DOCDT AS DOC_DT,
      BG.BG_DESC AS BRANCH_GROUP, 
      BR.BRNAME,
      V.VCODE AS VENDOR_CODE,
      V.NAME AS VENDOR_NAME,
      V.SAP_VENDOR_CODE,
      V.SAP_VENDOR_NAME1,
      CL.NAME,
      
      SUM((TBL_TRN_PRPB01_MAT.BILL_RATEPUOM * TBL_TRN_PRPB01_MAT.BILL_QTY)) AS GROSS_AMOUNT,
      
      SUM(((TBL_TRN_PRPB01_MAT.BILL_QTY*TBL_TRN_PRPB01_MAT.BILL_RATEPUOM)+(case when TBL_TRN_PRPB01_MAT.CGST IS not NULL then convert(numeric(14,2),((TBL_TRN_PRPB01_MAT.BILL_QTY*TBL_TRN_PRPB01_MAT.BILL_RATEPUOM*TBL_TRN_PRPB01_MAT.CGST)/100)) else  0 end)+
          (case when TBL_TRN_PRPB01_MAT.SGST IS not NULL then convert(numeric(14,2),((TBL_TRN_PRPB01_MAT.BILL_QTY*TBL_TRN_PRPB01_MAT.BILL_RATEPUOM*TBL_TRN_PRPB01_MAT.SGST)/100)) else  0 end)+
          (case when TBL_TRN_PRPB01_MAT.IGST IS not NULL then convert(numeric(14,2),((TBL_TRN_PRPB01_MAT.BILL_QTY*TBL_TRN_PRPB01_MAT.BILL_RATEPUOM*TBL_TRN_PRPB01_MAT.IGST)/100)) else  0 end)+
          (case when CT.AMOUNT IS not NULL then CT.AMOUNT else  0 end))-(case when TBL_TRN_PRPB01_MAT.DISC_AMT = '0.00' then convert(numeric(14,2),((TBL_TRN_PRPB01_MAT.BILL_QTY*TBL_TRN_PRPB01_MAT.BILL_RATEPUOM*TBL_TRN_PRPB01_MAT.DISCOUNT)/100)) else  TBL_TRN_PRPB01_MAT.DISC_AMT end)
          ) as NET_AMOUNT,
      
          
          CASE
          WHEN TBL_TRN_PRPB01_HDR.STATUS ='A' THEN 'Approved'
          WHEN TBL_TRN_PRPB01_HDR.STATUS = 'N' THEN 'Not Approved'
          WHEN TBL_TRN_PRPB01_HDR.STATUS = 'c' THEN 'Cancelled'
          WHEN TBL_TRN_PRPB01_HDR.STATUS = 'R' THEN 'Closed'
                      
          END AS STATUS 
      
      
      FROM TBL_TRN_PRPB01_MAT (NOLOCK)
      LEFT OUTER JOIN TBL_TRN_PRPB01_HDR (NOLOCK) ON TBL_TRN_PRPB01_HDR.PBID = TBL_TRN_PRPB01_MAT.PBID_REF  
      LEFT OUTER JOIN TBL_MST_SUBLEDGER AS S (NOLOCK) ON TBL_TRN_PRPB01_HDR.VID_REF = S.SGLID   
      LEFT OUTER JOIN TBL_MST_VENDOR AS V ON S.SGLID = V.SLID_REF 
      LEFT OUTER JOIN TBL_MST_ITEM (NOLOCK) ON TBL_TRN_PRPB01_MAT.ITEMID_REF = TBL_MST_ITEM.ITEMID 
      LEFT OUTER JOIN TBL_MST_ITEMGROUP (NOLOCK) ON TBL_MST_ITEM.ITEMGID_REF = TBL_MST_ITEMGROUP.ITEMGID  
      LEFT OUTER JOIN TBL_MST_BUSINESSUNIT (NOLOCK) ON TBL_MST_ITEM.BUID_REF = TBL_MST_BUSINESSUNIT.BUID
      LEFT OUTER JOIN TBL_MST_UOM (NOLOCK) AS MU ON TBL_TRN_PRPB01_MAT.UOMID_REF = MU.UOMID 
      LEFT OUTER JOIN TBL_MST_BRANCH AS BR WITH (NOLOCK) ON TBL_TRN_PRPB01_HDR.BRID_REF=BR.BRID 
      LEFT OUTER JOIN TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID 
      LEFT OUTER JOIN TBL_MST_VENDORLOCATION (NOLOCK) AS CL ON TBL_TRN_PRPB01_HDR.BILL_TO = CL.LID 
      LEFT OUTER JOIN TBL_MST_VENDORLOCATION (NOLOCK) ON TBL_TRN_PRPB01_HDR.SHIP_TO = TBL_MST_VENDORLOCATION.LID 
      LEFT OUTER JOIN (SELECT PBID_REF, SUM(VALUE) + SUM(VALUE * IGST / 100) + SUM(VALUE * CGST / 100) + SUM(VALUE * SGST / 100) AS AMOUNT FROM TBL_TRN_PRPB01_CAL (NOLOCK) GROUP BY PBID_REF) AS CT ON TBL_TRN_PRPB01_MAT.PBID_REF = CT.PBID_REF
      
      WHERE
      (TBL_TRN_PRPB01_HDR.STATUS = '$this->STATUS') AND 
      (TBL_TRN_PRPB01_HDR.CYID_REF = $this->CYID) AND 
      (TBL_TRN_PRPB01_HDR.BRID_REF in($BranchName)) AND 
      (TBL_TRN_PRPB01_HDR.PB_DOCDT BETWEEN '$this->From_Date' AND '$this->To_Date') AND 
      (TBL_TRN_PRPB01_HDR.VID_REF IN ($SGLID)) AND 
      (TBL_MST_ITEM.ITEMGID_REF	in ( $ITEMGID)) AND 
      (TBL_TRN_PRPB01_MAT.ITEMID_REF	in ( $ITEMID))


      GROUP BY 
    TBL_TRN_PRPB01_HDR.PB_DOCNO,
    TBL_TRN_PRPB01_HDR.PB_DOCDT,
    BG.BG_DESC, 
    BR.BRNAME,
    V.VCODE,
    V.NAME,
    V.SAP_VENDOR_CODE,
    V.SAP_VENDOR_NAME1,
    CL.NAME,
    TBL_TRN_PRPB01_HDR.STATUS
      "));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
 
        return [
            'Purchase Bill No',
            'Purchase Bill Date',
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





