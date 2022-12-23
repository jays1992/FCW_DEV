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
use App\Exports\TDS_Customer_Detail;
use Maatwebsite\Excel\Facades\Excel;














class TDS_Customer_Detail implements FromCollection, WithHeadings
{


 function __construct($SLID,$From_Date,$To_Date,$BranchName,$CYID_REF) {
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->CYID = $CYID_REF;
        $this->SLID = $SLID;


 
 }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

      //dd($this->CYID); 
        //dd($this->From_Date); 
             
        $BranchName=implode(",",$this->BranchName);          
        $SLID=implode(",",$this->SLID);
    

      // dd($GLID);
		//dd($AGID);



      return collect( $data=DB::select("SELECT C.CCODE AS CODE, C.NAME AS VENDORNAME,B.SINO as Invoice_No, B.SIDT as Invoice_Date, SUM(A.ASSESSABLE_VL_TDS) AS Assessable_Value,
      CAST(SUM((A.ASSESSABLE_VL_TDS*A.TDS_RATE)/100) AS NUMERIC(14,2)) AS TDS_AMOUNT
      FROM TBL_TRN_SLSI01_TDS A INNER JOIN TBL_TRN_SLSI01_HDR B ON A.SIID_REF = B.SIID
      INNER JOIN TBL_MST_CUSTOMER C ON B.SLID_REF = C.SLID_REF
      INNER JOIN TBL_MST_BRANCH M(NOLOCK) ON B.BRID_REF = M.BRID
      WHERE B.CYID_REF = $this->CYID AND  B.BRID_REF IN ($BranchName) AND B.STATUS = 'A'
      AND B.SLID_REF IN ($SLID)  AND B.SIDT BETWEEN '$this->From_Date' AND '$this->To_Date'
      GROUP BY B.SLID_REF,C.CCODE, C.NAME,B.SINO, B.SIDT
      UNION
      SELECT C.VCODE AS CODE, C.NAME AS VENDORNAME,B.SSI_NO as Invoice_No, B.SSI_DT as Invoice_Date, SUM(A.ASSESSABLE_VL_TDS) AS Assessable_Value,
      CAST(SUM((A.ASSESSABLE_VL_TDS*A.TDS_RATE)/100) AS NUMERIC(14,2)) AS TDS_AMOUNT
      FROM TBL_TRN_SLSI02_TDS A INNER JOIN TBL_TRN_SLSI02_HDR B ON A.SSIID_REF = B.SSIID
      INNER JOIN TBL_MST_VENDOR C ON B.SGLID_REF = C.SLID_REF
      INNER JOIN TBL_MST_BRANCH M(NOLOCK) ON B.BRID_REF = M.BRID
      WHERE B.CYID_REF = $this->CYID AND  B.BRID_REF IN ($BranchName) AND B.STATUS = 'A'
      AND B.SGLID_REF IN ($SLID) AND B.SSI_DT BETWEEN '$this->From_Date' AND '$this->To_Date'
      GROUP BY B.SGLID_REF,C.VCODE, C.NAME,B.SSI_NO, B.SSI_DT
      UNION
      SELECT C.VCODE AS CODE, C.NAME AS VENDORNAME,B.RECEIPT_NO as Invoice_No, B.RECEIPT_DT as Invoice_Date, SUM(A.ASSESSABLE_VL_TDS) AS Assessable_Value,
      CAST(SUM((A.ASSESSABLE_VL_TDS*A.TDS_RATE)/100) AS NUMERIC(14,2)) AS TDS_AMOUNT
      FROM TBL_TRN_RECEIPT_TDS A INNER JOIN TBL_TRN_RECEIPT_HDR B ON A.RECEIPTID_REF = B.RECEIPTID
      INNER JOIN TBL_MST_VENDOR C ON B.CUSTMER_VENDOR_ID = C.SLID_REF
      INNER JOIN TBL_MST_BRANCH M(NOLOCK) ON B.BRID_REF = M.BRID
      WHERE B.CYID_REF = $this->CYID AND  B.BRID_REF IN ($BranchName) AND B.STATUS = 'A' AND B.RECEIPT_FOR = 'CUSTOMER'
      AND B.CUSTMER_VENDOR_ID IN ($SLID) AND B.RECEIPT_DT BETWEEN '$this->From_Date' AND '$this->To_Date'
      GROUP BY B.CUSTMER_VENDOR_ID,C.VCODE, C.NAME,B.RECEIPT_NO, B.RECEIPT_DT
      UNION
      SELECT C.VCODE AS CODE, C.NAME AS VENDORNAME,B.AR_DOC_NO as Invoice_No, B.AR_DOC_DT as Invoice_Date, SUM(A.ASSESSABLE_VL_TDS) AS Assessable_Value,
      CASE WHEN B.AR_TYPE = 'Credit Note' THEN -CAST(SUM((A.ASSESSABLE_VL_TDS*A.TDS_RATE)/100) AS NUMERIC(14,2)) ELSE CAST(SUM((A.ASSESSABLE_VL_TDS*A.TDS_RATE)/100) AS NUMERIC(14,2)) END AS TDS_AMOUNT
      FROM TBL_TRN_FNARDRCR_TDS A INNER JOIN TBL_TRN_FNARDRCR_HDR B ON A.ARDRCRID_REF = B.ARDRCRID
      INNER JOIN TBL_MST_VENDOR C ON B.SLID_REF = C.SLID_REF
      INNER JOIN TBL_MST_BRANCH M(NOLOCK) ON B.BRID_REF = M.BRID
      WHERE B.CYID_REF = $this->CYID  AND B.BRID_REF IN ($BranchName) AND B.STATUS = 'A'
      AND B.SLID_REF IN ($SLID) AND B.AR_DOC_DT BETWEEN '$this->From_Date' AND '$this->To_Date'
      GROUP BY B.SLID_REF,C.VCODE, C.NAME,B.AR_DOC_NO, B.AR_DOC_DT,B.AR_TYPE"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
    

        return [
          'Customer Code',
          'Customer Name',
          'Invoice No',
          'Invoice Date',
          'Assessable Amount',
          'TDS Amount',           
         ];
         
         
   
    }
}





