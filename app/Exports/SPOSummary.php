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
use App\Exports\SPOSummary;
use Maatwebsite\Excel\Facades\Excel;














class SPOSummary implements FromCollection, WithHeadings
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
       



      return collect( $data=DB::select("SELECT TBL_TRN_PROR04_HDR.SPO_NO,   
      TBL_TRN_PROR04_HDR.SPO_DT,
      BG.BG_DESC AS BRANCH_GROUP,
      BR.BRNAME,
      TBL_MST_VENDOR.VCODE AS VENDOR_CODE,
      TBL_MST_VENDOR.NAME AS VENDOR_NAME,
      TBL_MST_VENDOR.SAP_VENDOR_CODE,
      TBL_MST_VENDOR.SAP_VENDOR_NAME1,
	  CL.NAME,    
     SUM(TBL_TRN_PROR04_MAT.SPO_QTY*TBL_TRN_PROR04_MAT.SPO_QTY) AS GROSS_AMOUNT,
      
      SUM(((TBL_TRN_PROR04_MAT.SPO_QTY*TBL_TRN_PROR04_MAT.SPO_RATE)+(case when TBL_TRN_PROR04_MAT.CGST IS not NULL then convert(numeric(14,2),((TBL_TRN_PROR04_MAT.SPO_QTY*TBL_TRN_PROR04_MAT.SPO_RATE*TBL_TRN_PROR04_MAT.CGST)/100)) else  0 end)+
      (case when TBL_TRN_PROR04_MAT.SGST IS not NULL then convert(numeric(14,2),((TBL_TRN_PROR04_MAT.SPO_QTY*TBL_TRN_PROR04_MAT.SPO_RATE*TBL_TRN_PROR04_MAT.SGST)/100)) else  0 end)+
      (case when TBL_TRN_PROR04_MAT.IGST IS not NULL then convert(numeric(14,2),((TBL_TRN_PROR04_MAT.SPO_QTY*TBL_TRN_PROR04_MAT.SPO_RATE*TBL_TRN_PROR04_MAT.IGST)/100)) else  0 end)+
      (case when CT.AMOUNT IS not NULL then CT.AMOUNT else  0 end))-(case when TBL_TRN_PROR04_MAT.DIS_AMT = '0.00' then convert(numeric(14,2),((TBL_TRN_PROR04_MAT.SPO_QTY*TBL_TRN_PROR04_MAT.SPO_RATE*TBL_TRN_PROR04_MAT.DISCOUNT_PER)/100)) else  TBL_TRN_PROR04_MAT.DIS_AMT end))
      as NET_AMOUNT,
              CASE
          WHEN TBL_TRN_PROR04_HDR.STATUS ='A' THEN 'Approved'
          WHEN TBL_TRN_PROR04_HDR.STATUS = 'N' THEN 'Not Approved'
          WHEN TBL_TRN_PROR04_HDR.STATUS = 'c' THEN 'Cancelled'
          WHEN TBL_TRN_PROR04_HDR.STATUS = 'R' THEN 'Closed'
                      
          END AS STATUS 
      FROM            
      TBL_TRN_PROR04_MAT LEFT OUTER JOIN
      TBL_TRN_PROR04_HDR ON TBL_TRN_PROR04_HDR.SPOID = TBL_TRN_PROR04_MAT.SPOID_REF LEFT OUTER JOIN
      TBL_MST_VENDOR ON TBL_TRN_PROR04_HDR.VID_REF = TBL_MST_VENDOR.SLID_REF LEFT OUTER JOIN
      TBL_MST_EMPLOYEE ON TBL_TRN_PROR04_HDR.VTID_REF = TBL_MST_EMPLOYEE.EMPID LEFT OUTER JOIN
      TBL_MST_ITEM ON TBL_TRN_PROR04_MAT.SERVICECODE = TBL_MST_ITEM.ITEMID LEFT OUTER JOIN						 
      TBL_MST_UOM AS MU ON TBL_TRN_PROR04_MAT.UOMID_REF = MU.UOMID LEFT OUTER JOIN
      TBL_MST_VENDORLOCATION AS CL ON TBL_TRN_PROR04_HDR.BILL_TO = CL.LID LEFT OUTER JOIN
      TBL_MST_VENDORLOCATION ON TBL_TRN_PROR04_HDR.SHIP_TO = TBL_MST_VENDORLOCATION.LID LEFT OUTER JOIN
      TBL_MST_ITEMGROUP ON TBL_MST_ITEM.ITEMGID_REF = TBL_MST_ITEMGROUP.ITEMGID LEFT OUTER JOIN
      TBL_MST_BRANCH AS BR WITH (NOLOCK) ON TBL_TRN_PROR04_HDR.BRID_REF=BR.BRID LEFT JOIN
      TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT JOIN
      TBL_MST_BUSINESSUNIT ON TBL_MST_ITEM.BUID_REF = TBL_MST_BUSINESSUNIT.BUID LEFT OUTER JOIN
      (SELECT        SPOID_REF, SUM(VALUE) + SUM(VALUE * IGST / 100) + SUM(VALUE * CGST / 100) + SUM(VALUE * SGST / 100) AS AMOUNT
      FROM            TBL_TRN_PROR04_CAL
      GROUP BY SPOID_REF) AS CT ON TBL_TRN_PROR04_MAT.SPOID_REF = CT.SPOID_REF
      WHERE 
      (TBL_TRN_PROR04_HDR.STATUS = '$this->STATUS') 
      AND (TBL_TRN_PROR04_HDR.CYID_REF = $this->CYID) 
      AND (TBL_TRN_PROR04_HDR.BRID_REF in( $BranchName)) 
      AND (TBL_TRN_PROR04_HDR.SPO_DT BETWEEN '$this->From_Date' AND '$this->To_Date')
      AND (TBL_TRN_PROR04_HDR.VID_REF in ( $SGLID))
      AND (TBL_MST_ITEM.ITEMGID_REF in ( $ITEMGID))
      AND (TBL_TRN_PROR04_MAT.SERVICECODE in ( $ITEMID))
	  GROUP BY 
	  TBL_TRN_PROR04_HDR.SPO_NO,   
      TBL_TRN_PROR04_HDR.SPO_DT,
      BG.BG_DESC,
      BR.BRNAME,
      TBL_MST_VENDOR.VCODE,
      TBL_MST_VENDOR.NAME,
      TBL_MST_VENDOR.SAP_VENDOR_CODE,
      TBL_MST_VENDOR.SAP_VENDOR_NAME1,
	  CL.NAME,
	  TBL_TRN_PROR04_HDR.STATUS"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
            'SPO No',
            'SPO Date',
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





