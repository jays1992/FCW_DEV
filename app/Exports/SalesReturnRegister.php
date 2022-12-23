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
use App\Exports\SalesReturnRegister;
use Maatwebsite\Excel\Facades\Excel;














class SalesReturnRegister implements FromCollection, WithHeadings
{

 function __construct($SGLID,$From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMID,$STATUS,$CYID,$SRID) {
        $this->ITEMID = $ITEMID;
        $this->SGLID = $SGLID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->STATUS = $STATUS;
        $this->CYID = $CYID;
        $this->SRID = $SRID;
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
        $SRID=implode(",",$this->SRID);
       



      return collect( $data=DB::select("		SELECT
      TBL_TRN_SLSR01_HDR.SRNO,
      TBL_TRN_SLSR01_HDR.SRDT, 
      TBL_MST_GENERALLEDGER.GLNAME,
      BG.BG_DESC AS BRANCH_GROUP,
      BR.BRNAME,
      CG.DESCRIPTIONS AS CUSTOMER_GROUP,
      C.CCODE AS CUSTOMER_CODE, 
      C.NAME AS CUSTOMER_NAME,
      C.SAP_CUSTOMER_CODE,
      C.SAP_CUSTOMER_NAME,
      TBL_MST_STATE.NAME AS STATE,
      TBL_MST_ITEMGROUP.GROUPNAME, 
      TBL_MST_ITEM.ALPS_PART_NO,
      TBL_MST_ITEM.CUSTOMER_PART_NO,
      TBL_MST_ITEM.OEM_PART_NO,
      TBL_MST_ITEM.ICODE,
      HSN.HSNCODE,
      TBL_MST_ITEM.NAME,
      TBL_MST_CUSTOMERLOCATION.CADD AS ShipTo,
      TBL_MST_UOM.DESCRIPTIONS AS UOM,
      TBL_TRN_SLSR01_MAT.SRQTY, 
      TBL_TRN_SLSR01_MAT.SRRATE,	 
      (TBL_TRN_SLSR01_MAT.SRRATE*TBL_TRN_SLSR01_MAT.SRQTY) AS AMOUNT,	
      TBL_TRN_SLSR01_MAT.IGST,	
      (((TBL_TRN_SLSR01_MAT.SRRATE*TBL_TRN_SLSR01_MAT.SRQTY)*(TBL_TRN_SLSR01_MAT.CGST)/100)+(case when CT.AMOUNT IS not NULL then CT.AMOUNT else  0 end)) AS CGST,
      (((TBL_TRN_SLSR01_MAT.SRRATE*TBL_TRN_SLSR01_MAT.SRQTY)*(TBL_TRN_SLSR01_MAT.CGST)/100)+(case when CT.AMOUNT IS not NULL then CT.AMOUNT else  0 end)) AS SGST,

       (((TBL_TRN_SLSR01_MAT.SRRATE * TBL_TRN_SLSR01_MAT.SRQTY)*(TBL_TRN_SLSR01_MAT.CGST)/100)+ ((TBL_TRN_SLSR01_MAT.SRRATE * TBL_TRN_SLSR01_MAT.SRQTY)*(TBL_TRN_SLSR01_MAT.CGST)/100)+(case when CT.AMOUNT IS not NULL then CT.AMOUNT else  0 end)) as TotalTax,

       ((TBL_TRN_SLSR01_MAT.SRRATE*TBL_TRN_SLSR01_MAT.SRQTY)+(case when TBL_TRN_SLSR01_MAT.CGST IS not NULL then convert(numeric(14,2),((TBL_TRN_SLSR01_MAT.SRQTY*TBL_TRN_SLSR01_MAT.SRRATE*TBL_TRN_SLSR01_MAT.CGST)/100)) else  0 end)+
       (case when TBL_TRN_SLSR01_MAT.SGST IS not NULL then convert(numeric(14,2),((TBL_TRN_SLSR01_MAT.SRQTY*TBL_TRN_SLSR01_MAT.SRRATE*TBL_TRN_SLSR01_MAT.SGST)/100)) else  0 end)+
       (case when TBL_TRN_SLSR01_MAT.IGST IS not NULL then convert(numeric(14,2),((TBL_TRN_SLSR01_MAT.SRQTY*TBL_TRN_SLSR01_MAT.SRRATE*TBL_TRN_SLSR01_MAT.IGST)/100)) else  0 end)+
       (case when CT.AMOUNT IS not NULL then CT.AMOUNT else  0 end)) AS  AMOUNTAFTERTAX,
      CT.AMOUNT AS CALCULATION,		

      ((TBL_TRN_SLSR01_MAT.SRRATE*TBL_TRN_SLSR01_MAT.SRQTY)+(case when CT.AMOUNT IS not NULL then CT.AMOUNT else  0 end)) AS TotalOrderValue,
      
      CASE
      WHEN TBL_TRN_SLSR01_HDR.STATUS ='A' THEN 'Approved'
      WHEN TBL_TRN_SLSR01_HDR.STATUS = 'N' THEN 'Not Approved'
      WHEN TBL_TRN_SLSR01_HDR.STATUS = 'c' THEN 'Cancelled'
      WHEN TBL_TRN_SLSR01_HDR.STATUS = 'R' THEN 'Closed'
              
  END AS STATUS 




      FROM              
      TBL_TRN_SLSR01_HDR LEFT OUTER JOIN
      TBL_TRN_SLSR01_MAT ON TBL_TRN_SLSR01_HDR.SRID = TBL_TRN_SLSR01_MAT.SRID_REF LEFT OUTER JOIN
      TBL_MST_GENERALLEDGER ON TBL_TRN_SLSR01_HDR.GLID_REF = TBL_MST_GENERALLEDGER.GLID LEFT OUTER JOIN  
      TBL_MST_SUBLEDGER (NOLOCK) ON TBL_TRN_SLSR01_HDR.SLID_REF = TBL_MST_SUBLEDGER.SGLID LEFT OUTER JOIN 
      TBL_MST_CUSTOMER AS C ON TBL_MST_SUBLEDGER.SGLID = C.SLID_REF LEFT OUTER JOIN
      TBL_MST_CUSTOMERGROUP AS CG ON C.CGID_REF = CG.CGID LEFT OUTER JOIN
      TBL_MST_BRANCH AS BR WITH (NOLOCK) ON TBL_TRN_SLSR01_HDR.BRID_REF=BR.BRID LEFT OUTER JOIN
      TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT OUTER JOIN
      TBL_MST_CUSTOMER ON TBL_MST_SUBLEDGER.SGLID = TBL_MST_CUSTOMER.SLID_REF LEFT OUTER JOIN
      TBL_MST_CITY ON TBL_MST_CUSTOMER.REGCITYID_REF = TBL_MST_CITY.CITYID LEFT OUTER JOIN
      TBL_MST_STATE ON TBL_MST_CUSTOMER.REGSTID_REF = TBL_MST_STATE.STID LEFT OUTER JOIN
      TBL_MST_CUSTOMERLOCATION AS CL ON TBL_TRN_SLSR01_HDR.BILL_TO = CL.CLID LEFT OUTER JOIN
      TBL_MST_CUSTOMERLOCATION ON TBL_TRN_SLSR01_HDR.SHIP_TO = TBL_MST_CUSTOMERLOCATION.CLID LEFT OUTER JOIN
      TBL_MST_ITEM ON TBL_TRN_SLSR01_MAT.ITEMID_REF = TBL_MST_ITEM.ITEMID LEFT OUTER JOIN
      TBL_MST_HSN AS HSN WITH (NOLOCK) ON TBL_MST_ITEM.HSNID_REF=HSN.HSNID LEFT OUTER JOIN
      TBL_MST_ITEMGROUP ON TBL_MST_ITEM.ITEMGID_REF = TBL_MST_ITEMGROUP.ITEMGID LEFT OUTER JOIN
      TBL_MST_UOM ON TBL_MST_ITEM.MAIN_UOMID_REF = TBL_MST_UOM.UOMID LEFT OUTER JOIN

      (SELECT SRID_REF, SUM(VALUE) + SUM(VALUE * IGST / 100) + SUM(VALUE * CGST / 100) + SUM(VALUE * SGST / 100) AS AMOUNT FROM TBL_TRN_SLSR01_CAL
      GROUP BY SRID_REF) AS CT ON TBL_TRN_SLSR01_MAT.SRID_REF = CT.SRID_REF

      

WHERE TBL_TRN_SLSR01_HDR.STATUS='$this->STATUS' AND TBL_TRN_SLSR01_HDR.SLID_REF IN ($SGLID)  AND TBL_MST_ITEM.ITEMID IN ($ITEMID)
AND TBL_TRN_SLSR01_HDR.SRID IN ($SRID) AND TBL_TRN_SLSR01_HDR.CYID_REF=$this->CYID AND TBL_TRN_SLSR01_HDR.BRID_REF IN ($BranchName) AND 
(TBL_TRN_SLSR01_HDR.SRDT BETWEEN '$this->From_Date' AND '$this->To_Date')
"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
            'Sales Return No',
            'Sales Return Date',
            'GL Name ',
            'Branch Group',
            'Branch Name',
            'Customer Group',
            'Customer Code',
            'Customer Name',
            'SAP Customer Code',
            'SAP Customer Name',
            'State',
            'Item ',
			'ALPS Part No',
			'Customer Part No',
			'OEM Part No',
			' Item Code',
			'HSN/SAC Code',
			'Item Name',
			'Shipping Address',
			'UOM',
			'SR QTY',
			'Rate Per UOM',
			'Amount',
			'IGST',
			'CGST',
			'SGST',
			'Total TAX ',
			'Amount After TAX',
			'Calculation',
			'Total Order Value',
			'Status',
        ];
    }
}





