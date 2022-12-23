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
use App\Exports\SalesInvoiceDetail;
use Maatwebsite\Excel\Facades\Excel;














class SalesInvoiceDetail implements FromCollection, WithHeadings
{


    protected $itemid;

 function __construct($SGLID,$From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMGID,$ITEMID,$STATUS,$CYID_REF) {
    $this->ITEMID = $ITEMID;
    $this->SGLID = $SGLID;
    $this->From_Date = $From_Date;
    $this->To_Date = $To_Date;
    $this->BranchName = $BranchName;
    $this->ITEMGID = $ITEMGID;
    $this->STATUS = $STATUS;
    $this->CYID_REF = $CYID_REF;
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
       



      return collect( $data=DB::select("SELECT TBL_TRN_SLSI01_HDR.SINO, TBL_TRN_SLSI01_HDR.SIDT,BG.BG_DESC AS BRANCH_GROUP, BR.BRNAME,
      TBL_MST_CUSTOMER.CCODE AS CUSTOMER_CODE,TBL_MST_CUSTOMER.NAME AS CUSTOMER_NAME, TBL_MST_CUSTOMER.SAP_CUSTOMER_CODE,TBL_MST_CUSTOMER.SAP_CUSTOMER_NAME,
      TBL_MST_ITEMGROUP.GROUPNAME,  TBL_MST_ITEM.NAME AS ITEM_NAME,  MU.DESCRIPTIONS,  TBL_MST_BUSINESSUNIT.BUNAME,
       TBL_MST_ITEM.ALPS_PART_NO,TBL_MST_ITEM.CUSTOMER_PART_NO,TBL_MST_ITEM.OEM_PART_NO,  TBL_TRN_SLSI01_MAT.CURRENT_QTY AS PENDING_QTY,
       TBL_TRN_SLSI01_MAT.SIMAIN_QTY-TBL_TRN_SLSI01_MAT.CURRENT_QTY AS CONSUMED_QTY,  TBL_TRN_SLSI01_MAT.SIMAIN_QTY AS ACTUAL_QTY,
       TBL_TRN_SLSI01_MAT.RATEPUOM, TBL_TRN_SLSI01_MAT.RATEPUOM*TBL_TRN_SLSI01_MAT.SIMAIN_QTY GROSS_AMOUNT,
     
         ((TBL_TRN_SLSI01_MAT.SIMAIN_QTY*TBL_TRN_SLSI01_MAT.RATEPUOM)+(case when TBL_TRN_SLSI01_MAT.CGST IS not NULL then convert(numeric(14,2),((TBL_TRN_SLSI01_MAT.SIMAIN_QTY*TBL_TRN_SLSI01_MAT.RATEPUOM*TBL_TRN_SLSI01_MAT.CGST)/100)) else  0 end)+
         (case when TBL_TRN_SLSI01_MAT.SGST IS not NULL then convert(numeric(14,2),((TBL_TRN_SLSI01_MAT.SIMAIN_QTY*TBL_TRN_SLSI01_MAT.RATEPUOM*TBL_TRN_SLSI01_MAT.SGST)/100)) else  0 end)+
         (case when TBL_TRN_SLSI01_MAT.IGST IS not NULL then convert(numeric(14,2),((TBL_TRN_SLSI01_MAT.SIMAIN_QTY*TBL_TRN_SLSI01_MAT.RATEPUOM*TBL_TRN_SLSI01_MAT.IGST)/100)) else  0 end)+
         (case when CT.AMOUNT IS not NULL then CT.AMOUNT else  0 end))-(case when TBL_TRN_SLSI01_MAT.DISCOUNT_AMT = '0.00' then convert(numeric(14,2),((TBL_TRN_SLSI01_MAT.SIMAIN_QTY*TBL_TRN_SLSI01_MAT.RATEPUOM*TBL_TRN_SLSI01_MAT.DISPER)/100)) else  TBL_TRN_SLSI01_MAT.DISCOUNT_AMT end)
         as NET_AMOUNT,
     
      CASE
               WHEN TBL_TRN_SLSI01_HDR.STATUS ='A' THEN 'Approved'
               WHEN TBL_TRN_SLSI01_HDR.STATUS = 'N' THEN 'Not Approved'
               WHEN TBL_TRN_SLSI01_HDR.STATUS = 'c' THEN 'Cancelled'
               WHEN TBL_TRN_SLSI01_HDR.STATUS = 'R' THEN 'Closed' 
           END AS STATUS       
     FROM            TBL_TRN_SLSI01_MAT LEFT OUTER JOIN
                              TBL_TRN_SLSI01_HDR ON TBL_TRN_SLSI01_HDR.SIID = TBL_TRN_SLSI01_MAT.SIID_REF LEFT OUTER JOIN  
                              TBL_MST_SUBLEDGER ON TBL_TRN_SLSI01_HDR.SLID_REF = TBL_MST_SUBLEDGER.SGLID LEFT OUTER JOIN
                              TBL_MST_CUSTOMER ON TBL_TRN_SLSI01_HDR.SLID_REF = TBL_MST_CUSTOMER.CID LEFT OUTER JOIN
                              TBL_MST_EMPLOYEE ON TBL_TRN_SLSI01_HDR.SLID_REF = TBL_MST_EMPLOYEE.EMPID LEFT OUTER JOIN
                              TBL_MST_ITEM ON TBL_TRN_SLSI01_MAT.ITEMID_REF = TBL_MST_ITEM.ITEMID LEFT OUTER JOIN						 
                              TBL_MST_UOM AS MU ON TBL_TRN_SLSI01_MAT.MAIN_SIUOMID_REF = MU.UOMID LEFT OUTER JOIN
                              TBL_MST_UOM AS AU ON TBL_TRN_SLSI01_MAT.ALT_SIUOMID_REF = AU.UOMID LEFT OUTER JOIN
                              TBL_MST_CUSTOMERLOCATION AS CL ON TBL_TRN_SLSI01_HDR.BILLTO = CL.CLID LEFT OUTER JOIN
                              TBL_MST_CUSTOMERLOCATION ON TBL_TRN_SLSI01_HDR.SHIPTO = TBL_MST_CUSTOMERLOCATION.CLID LEFT OUTER JOIN
                              TBL_MST_ITEMGROUP ON TBL_MST_ITEM.ITEMGID_REF = TBL_MST_ITEMGROUP.ITEMGID LEFT OUTER JOIN
                              TBL_MST_BRANCH AS BR WITH (NOLOCK) ON TBL_TRN_SLSI01_HDR.BRID_REF=BR.BRID LEFT JOIN
                              TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT JOIN
                              TBL_MST_BUSINESSUNIT ON TBL_MST_ITEM.BUID_REF = TBL_MST_BUSINESSUNIT.BUID LEFT OUTER JOIN
                                  (SELECT        SIID_REF, SUM(VALUE) + SUM(VALUE * IGST / 100) + SUM(VALUE * CGST / 100) + SUM(VALUE * SGST / 100) AS AMOUNT
                                    FROM            TBL_TRN_SLSI01_CAL
                                    GROUP BY SIID_REF) AS CT ON TBL_TRN_SLSI01_MAT.SIID_REF = CT.SIID_REF
     WHERE (TBL_TRN_SLSI01_HDR.STATUS = '$this->STATUS') 
     AND (TBL_TRN_SLSI01_HDR.CYID_REF =  $this->CYID_REF) 
     AND (TBL_TRN_SLSI01_HDR.BRID_REF in( $BranchName)) 
     AND (TBL_TRN_SLSI01_HDR.SIDT BETWEEN '$this->From_Date' AND '$this->To_Date')
     AND (TBL_TRN_SLSI01_HDR.SLID_REF in ( $SGLID))
     AND (TBL_MST_ITEM.ITEMGID_REF in ( $ITEMGID))
     AND (TBL_TRN_SLSI01_MAT.ITEMID_REF in ( $ITEMID))"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
            'Sales Invoice No',
            'Sales Invoice Date',
            'Branch Group',
            'Branch Name',
            'Customer Code',
            'Customer Name',
            'SAP Customer Code',
            'SAP Customer Name',
            'Item Group',
            'Item Name',
            'Uom',
            'Business Unit',
            'ALPS Part No',
            'Customer Part No',
            'OEM Part No',
            'Pending Qty',
            'Consumed Qty',
            'Actual Qty',
            'Rate',
            'Gross Amount',
            'Net Amount',
            'Status',
        ];
    }
}





