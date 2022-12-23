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
use App\Exports\BankBook;
use Maatwebsite\Excel\Facades\Excel;














class BankBook implements FromCollection, WithHeadings
{


 function __construct($BANKID,$From_Date,$To_Date,$BranchGroup,$BranchName,$CYID) {

        $this->BANKID = $BANKID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName; 

        $this->CYID = $CYID;
 }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        //dd($this->From_Date); 
        
    
        $BANKID=implode(",",$this->BANKID);
        $BranchName=implode(",",$this->BranchName);      

       



      return collect( $data=DB::select("SELECT 
      A.BG_DESC,
      A.BRNAME,
      A.RECEIPT_PAYMENT,
      A.PAYMENT_NO, 
      A.VENDOR_CUSTOMER_NAME,
      A.PAYMENT_DT,
      A.INSTRUMENT_TYPE, 
      A.TRANSACTION_DT, 
      A.INSTRUMENT_NO,
      A.NARRATION, 
      SUM(A.DRAMT) AS DRAMT,
      SUM(A.CRAMT) AS CRAMT,
	  DBO.FN_GLODBL(A.GLID_REF,'$this->From_Date') AS OPENING_DR, 
      DBO.FN_GLOCBL(A.GLID_REF,'$this->From_Date') AS OPENING_CR,
      DBO.FN_GLODBL(A.GLID_REF,'$this->From_Date')+SUM(A.DRAMT) AS CLOSING_DR, 
      DBO.FN_GLOCBL(A.GLID_REF,'$this->From_Date')+SUM(A.CRAMT) AS CLOSING_CR
	  FROM (
	  SELECT 
      F.BG_DESC,
      E.BRNAME,
      'PAYMENT' AS RECEIPT_PAYMENT,
      A.PAYMENT_NO, 
      ISNULL(B.NAME, C.NAME) AS VENDOR_CUSTOMER_NAME,
      A.PAYMENT_DT,
      A.INSTRUMENT_TYPE, 
      A.TRANSACTION_DT, 
      A.INSTRUMENT_NO,
      A.NARRATION, 
      0.00 AS DRAMT,
      A.AMOUNT AS CRAMT,D.GLID_REF
      from TBL_TRN_PAYMENT_HDR A (NOLOCK) LEFT JOIN TBL_MST_VENDOR B (NOLOCK)
      ON A.CUSTMER_VENDOR_ID = B.SLID_REF  AND A.PAYMENT_FOR = 'Vendor'
      LEFT JOIN TBL_MST_CUSTOMER C (NOLOCK)
      ON A.CUSTMER_VENDOR_ID = B.SLID_REF  AND A.PAYMENT_FOR = 'Customer'
      INNER JOIN TBL_MST_BANK D (NOLOCK) ON A.CASH_BANK_ID = D.BID
      INNER JOIN TBL_MST_BRANCH E (NOLOCK) ON A.BRID_REF = E.BRID
      INNER JOIN TBL_MST_BRANCH_GROUP F (NOLOCK) ON E.BGID_REF = F.BGID
      where A.PAYMENT_TYPE = 'BANK' AND A.CYID_REF = $this->CYID AND A.BRID_REF IN ($BranchName) AND A.CASH_BANK_ID IN ($BANKID) 
	  AND A.PAYMENT_DT BETWEEN '$this->From_Date' AND '$this->To_Date'
      UNION
      SELECT
      F.BG_DESC,
      E.BRNAME,
      'RECEIPT'  AS RECEIPT_PAYMENT,
      A.RECEIPT_NO AS PAYMENT_NO,
      ISNULL(B.NAME, C.NAME) AS VENDOR_CUSTOMER_NAME,
      A.RECEIPT_DT AS PAYMENT_DT,
      A.INSTRUMENT_TYPE,
      A.TRANSACTION_DT,
      A.INSTRUMENT_NO,
      A.NARRATION, 
      A.AMOUNT AS DRAMT,
      0.00 AS CRAMT,D.GLID_REF
      from TBL_TRN_RECEIPT_HDR A (NOLOCK) LEFT JOIN TBL_MST_VENDOR B (NOLOCK)
      ON A.CUSTMER_VENDOR_ID = B.SLID_REF  AND A.RECEIPT_FOR = 'Vendor'
      LEFT JOIN TBL_MST_CUSTOMER C (NOLOCK)
      ON A.CUSTMER_VENDOR_ID = B.SLID_REF  AND A.RECEIPT_FOR = 'Customer'
      INNER JOIN TBL_MST_BANK D (NOLOCK) ON A.CASH_BANK_ID = D.BID
      INNER JOIN TBL_MST_BRANCH E (NOLOCK) ON A.BRID_REF = E.BRID
      INNER JOIN TBL_MST_BRANCH_GROUP F (NOLOCK) ON E.BGID_REF = F.BGID
      where A.RECEIPT_TYPE = 'BANK' AND A.CYID_REF = $this->CYID AND A.BRID_REF IN ($BranchName) AND A.CASH_BANK_ID IN ($BANKID) 
	  AND A.RECEIPT_DT BETWEEN '$this->From_Date' AND '$this->To_Date'
	  UNION
      SELECT
      F.BG_DESC,
      E.BRNAME,
      'Manual JV' AS RECEIPT_PAYMENT,
      B.MJV_NO AS PAYMENT_NO,
      NULL AS VENDOR_CUSTOMER_NAME,
      B.MJV_DT AS PAYMENT_DT,
      NULL AS INSTRUMENT_TYPE,
      B.MJV_DT AS TRANSACTION_DT,
      NULL AS INSTRUMENT_NO,
      A.NARRATION, 
      A.DR_AMT AS DRAMT,
      A.CR_AMT AS CRAMT,A.GLID_REF
      FROM TBL_TRN_MJRV01_ACC A LEFT JOIN TBL_TRN_MJRV01_HDR B ON A.MJVID_REF=B.MJVID
      INNER JOIN TBL_MST_BANK D (NOLOCK) ON A.GLID_REF = D.GLID_REF
      INNER JOIN TBL_MST_BRANCH E (NOLOCK) ON B.BRID_REF = E.BRID
      INNER JOIN TBL_MST_BRANCH_GROUP F (NOLOCK) ON E.BGID_REF = F.BGID
      where B.CYID_REF = $this->CYID AND B.BRID_REF IN ($BranchName) AND D.BID IN ($BANKID) 
	  AND A.MJV_DT BETWEEN '$this->From_Date' AND '$this->To_Date') AS A
	  GROUP BY A.GLID_REF,A.BG_DESC,A.BRNAME,A.RECEIPT_PAYMENT,A.PAYMENT_NO,A.VENDOR_CUSTOMER_NAME,A.PAYMENT_DT,A.INSTRUMENT_TYPE,A.TRANSACTION_DT,A.INSTRUMENT_NO,A.NARRATION"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
    
        return [
          'Branch Group',
          'Branch Name',
          'Voucher Type',
          'Voucher No',
          'Customer/Vendor Name',
          'Voucher Date',
          'Instrument Type',
          'Transaction Date',
          'Instrument No',
          'Narration',
          'Debit Amount',
          'Credit Amount',
          'Opening Debit',
          'Opening Credit',   
          'Closing Debit',
          'Closing Credit',  
        ];
         
    }
}





