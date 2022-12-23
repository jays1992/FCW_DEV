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
use App\Exports\TDS_Vendor_Detail;
use Maatwebsite\Excel\Facades\Excel;














class TDS_Vendor_Detail implements FromCollection, WithHeadings
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



      return collect( $data=DB::select("SELECT C.VCODE AS CODE, C.NAME AS VENDORNAME,B.SPI_NO as Invoice_No, B.SPI_DT as Invoice_Date, SUM(A.ASSESSABLE_VL_TDS) AS Assessable_Value,
      CAST(SUM((A.ASSESSABLE_VL_TDS*A.TDS_RATE)/100) AS NUMERIC(14,2)) AS TDS_AMOUNT
      FROM TBL_TRN_PRPB02_TDS A INNER JOIN TBL_TRN_PRPB02_HDR B ON A.SPIID_REF = B.SPIID
      INNER JOIN TBL_MST_VENDOR C ON B.VID_REF = C.SLID_REF
      INNER JOIN TBL_MST_BRANCH M(NOLOCK) ON B.BRID_REF = M.BRID
      WHERE B.CYID_REF = $this->CYID AND  B.BRID_REF IN ($BranchName) AND B.STATUS = 'A'
      AND B.VID_REF IN ($SLID)  AND B.SPI_DT BETWEEN '$this->From_Date' AND '$this->To_Date'
      GROUP BY B.VID_REF,C.VCODE, C.NAME,B.SPI_NO, B.SPI_DT
      UNION
      SELECT C.VCODE AS CODE, C.NAME AS VENDORNAME,B.PB_DOCNO as Invoice_No, B.PB_DOCDT as Invoice_Date, SUM(A.ASSESSABLE_VL_TDS) AS Assessable_Value,
      CAST(SUM((A.ASSESSABLE_VL_TDS*A.TDS_RATE)/100) AS NUMERIC(14,2)) AS TDS_AMOUNT
      FROM TBL_TRN_PRPB01_TDS A INNER JOIN TBL_TRN_PRPB01_HDR B ON A.PBID_REF = B.PBID
      INNER JOIN TBL_MST_VENDOR C ON B.VID_REF = C.SLID_REF
      INNER JOIN TBL_MST_BRANCH M(NOLOCK) ON B.BRID_REF = M.BRID
      WHERE B.CYID_REF = $this->CYID AND  B.BRID_REF IN ($BranchName) AND B.STATUS = 'A'
      AND B.VID_REF IN ($SLID) AND B.PB_DOCDT BETWEEN '$this->From_Date' AND '$this->To_Date'
      GROUP BY B.VID_REF,C.VCODE, C.NAME,B.PB_DOCNO, B.PB_DOCDT
      UNION
      SELECT C.VCODE AS CODE, C.NAME AS VENDORNAME,B.PII_NO as Invoice_No, B.PII_DT as Invoice_Date, SUM(A.ASSESSABLE_VL_TDS) AS Assessable_Value,
      CAST(SUM((A.ASSESSABLE_VL_TDS*A.TDS_RATE)/100) AS NUMERIC(14,2)) AS TDS_AMOUNT
      FROM TBL_TRN_PII_TDS A INNER JOIN TBL_TRN_PII_HDR B ON A.PII_ID_REF = B.PII_ID
      INNER JOIN TBL_MST_VENDOR C ON B.VID_REF = C.SLID_REF
      INNER JOIN TBL_MST_BRANCH M(NOLOCK) ON B.BRID_REF = M.BRID
      WHERE B.CYID_REF = $this->CYID AND  B.BRID_REF IN ($BranchName) AND B.STATUS = 'A'
      AND B.VID_REF IN ($SLID) AND B.PII_DT BETWEEN '$this->From_Date' AND '$this->To_Date'
      GROUP BY B.VID_REF,C.VCODE, C.NAME,B.PII_NO, B.PII_DT
      UNION
      SELECT C.VCODE AS CODE, C.NAME AS VENDORNAME,B.AP_DOC_NO as Invoice_No, B.AP_DOC_DT as Invoice_Date, SUM(A.ASSESSABLE_VL_TDS) AS Assessable_Value,
      CASE WHEN B.AP_TYPE= 'Debit Note' THEN -CAST(SUM((A.ASSESSABLE_VL_TDS*A.TDS_RATE)/100) AS NUMERIC(14,2)) ELSE CAST(SUM((A.ASSESSABLE_VL_TDS*A.TDS_RATE)/100) AS NUMERIC(14,2)) END AS TDS_AMOUNT
      FROM TBL_TRN_FNAPDRCR_TDS A INNER JOIN TBL_TRN_FNAPDRCR_HDR B ON A.APDRCRID_REF = B.APDRCRID
      INNER JOIN TBL_MST_VENDOR C ON B.SLID_REF = C.SLID_REF
      INNER JOIN TBL_MST_BRANCH M(NOLOCK) ON B.BRID_REF = M.BRID
      WHERE B.CYID_REF = $this->CYID AND  B.BRID_REF IN ($BranchName) AND B.STATUS = 'A'
      AND B.SLID_REF IN ($SLID) AND B.AP_DOC_DT BETWEEN '$this->From_Date' AND '$this->To_Date'
      GROUP BY B.SLID_REF,C.VCODE, C.NAME,B.AP_DOC_NO, B.AP_DOC_DT,B.AP_TYPE
      UNION
      SELECT C.VCODE AS CODE, C.NAME AS VENDORNAME,B.PAYMENT_NO as Invoice_No, B.PAYMENT_DT as Invoice_Date, SUM(A.ASSESSABLE_VL_TDS) AS Assessable_Value,
      CAST(SUM((A.ASSESSABLE_VL_TDS*A.TDS_RATE)/100) AS NUMERIC(14,2)) AS TDS_AMOUNT
      FROM TBL_TRN_PAYMENT_TDS A INNER JOIN TBL_TRN_PAYMENT_HDR B ON A.PAYMENTID_REF = B.PAYMENTID
      INNER JOIN TBL_MST_VENDOR C ON B.CUSTMER_VENDOR_ID = C.SLID_REF
      INNER JOIN TBL_MST_BRANCH M(NOLOCK) ON B.BRID_REF = M.BRID
      WHERE B.CYID_REF = $this->CYID AND  B.BRID_REF IN ($BranchName) AND B.STATUS = 'A' AND B.PAYMENT_FOR = 'VENDOR'
      AND B.CUSTMER_VENDOR_ID IN ($SLID) AND B.PAYMENT_DT BETWEEN '$this->From_Date' AND '$this->To_Date'
      GROUP BY B.CUSTMER_VENDOR_ID,C.VCODE, C.NAME,B.PAYMENT_NO, B.PAYMENT_DT"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
    

        return [
          'Vendor Code',
          'Vendor Name',
          'Invoice No',
          'Invoice Date',
          'Assessable Amount',
          'TDS Amount',           
         ];
         
         
   
    }
}





