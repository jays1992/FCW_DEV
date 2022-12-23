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
use App\Exports\PaymentRegister;
use Maatwebsite\Excel\Facades\Excel;














class PaymentRegister implements FromCollection, WithHeadings
{


 function __construct($BANKID,$From_Date,$To_Date,$BranchGroup,$BranchName,$PAYMENTID,$STATUS,$CYID) {
        $this->PAYMENTID = $PAYMENTID;
        $this->BANKID = $BANKID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName; 
        $this->STATUS = $STATUS;
        $this->CYID = $CYID;
 }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        //dd($this->From_Date); 
        
        $PAYMENTID=implode(",",$this->PAYMENTID);
        $BANKID=implode(",",$this->BANKID);
        $BranchName=implode(",",$this->BranchName);      

       



      return collect( $data=DB::select("					SELECT     
      TBL_TRN_PAYMENT_HDR.PAYMENT_NO,
TBL_TRN_PAYMENT_HDR.PAYMENT_DT,
BG.BG_DESC AS BRANCH_GROUP,
BR.BRNAME,
(case when TBL_MST_GENERALLEDGER.GLCODE IS NULL then TBL_MST_SUBLEDGER.SGLCODE
else  TBL_MST_GENERALLEDGER.GLCODE end) AS VENDOR_CUSTOMER_ACCOUNT_CODE,

(case when TBL_MST_GENERALLEDGER.GLNAME IS NULL then TBL_MST_SUBLEDGER.SLNAME
else  TBL_MST_GENERALLEDGER.GLNAME end) AS VENDOR_CUSTOMER_ACCOUNT_NAME,
TBL_MST_BANK.NAME,
TBL_TRN_PAYMENT_HDR.INSTRUMENT_TYPE,
TBL_TRN_PAYMENT_HDR.INSTRUMENT_NO,
TBL_TRN_PAYMENT_HDR.TOAL_AMOUNT,
ISNULL(AR.AR_DOC_NO,ISNULL(AP.AP_DOC_NO,ISNULL(SR.SRNO,ISNULL(SPI.SPI_NO,ISNULL(PB.PB_DOCNO,SI.SINO))))) AS DOC_NO,
              ISNULL(AR.AR_DOC_DT,ISNULL(AP.AP_DOC_DT,ISNULL(SR.SRDT,ISNULL(PB.PB_DOCDT,ISNULL(SPI.SPI_DT,SI.SIDT))))) AS DOC_DT,
dbo.FN_DOC_AMT(NULL,TBL_TRN_PAYMENT_INVOICE.DOCNO_ID,TBL_TRN_PAYMENT_INVOICE.DOC_TYPE) AS DOC_AMOUNT,
TBL_TRN_PAYMENT_HDR.REMARKS,
TBL_TRN_PAYMENT_HDR.NARRATION,
CASE
WHEN TBL_TRN_PAYMENT_HDR.STATUS ='A' THEN 'Approved'
WHEN TBL_TRN_PAYMENT_HDR.STATUS = 'N' THEN 'Not Approved'
WHEN TBL_TRN_PAYMENT_HDR.STATUS = 'c' THEN 'Cancelled'
WHEN TBL_TRN_PAYMENT_HDR.STATUS = 'R' THEN 'Closed'   
END AS STATUS 

FROM        TBL_TRN_PAYMENT_HDR LEFT OUTER JOIN
  TBL_TRN_PAYMENT_INVOICE ON TBL_TRN_PAYMENT_HDR.PAYMENTID = TBL_TRN_PAYMENT_INVOICE.PAYMENTID_REF LEFT OUTER JOIN 				 
TBL_TRN_FNARDRCR_HDR AS AR  ON TBL_TRN_PAYMENT_INVOICE.DOCNO_ID = AR.ARDRCRID AND TBL_TRN_PAYMENT_INVOICE.DOC_TYPE IN ('AR_CREDIT_NOTE','AR_DEBIT_NOTE') LEFT OUTER JOIN
TBL_TRN_FNAPDRCR_HDR AS AP  ON TBL_TRN_PAYMENT_INVOICE.DOCNO_ID = AP.APDRCRID AND TBL_TRN_PAYMENT_INVOICE.DOC_TYPE IN ('AP_CREDIT_NOTE','AP_DEBIT_NOTE') LEFT OUTER JOIN
TBL_TRN_PRPB01_HDR AS PB  ON TBL_TRN_PAYMENT_INVOICE.DOCNO_ID = PB.PBID AND TBL_TRN_PAYMENT_INVOICE.DOC_TYPE IN ('PURCHASE_INVOICE') LEFT OUTER JOIN
TBL_TRN_SLSI01_HDR AS SI  ON TBL_TRN_PAYMENT_INVOICE.DOCNO_ID = SI.SIID AND  TBL_TRN_PAYMENT_INVOICE.DOC_TYPE IN ('SALES_INVOICE') LEFT OUTER JOIN
TBL_TRN_SLSR01_HDR AS SR  ON TBL_TRN_PAYMENT_INVOICE.DOCNO_ID = SR.SRID AND TBL_TRN_PAYMENT_INVOICE.DOC_TYPE IN ('SALES_RETURN') LEFT OUTER JOIN		
TBL_TRN_PRPB02_HDR AS SPI  ON TBL_TRN_PAYMENT_INVOICE.DOCNO_ID = SPI.SPIID AND TBL_TRN_PAYMENT_INVOICE.DOC_TYPE IN ('SERVICE_PURCHASE_INVOICE') LEFT OUTER JOIN
  
TBL_TRN_PAYMENT_ACCOUNT ON TBL_TRN_PAYMENT_HDR.PAYMENTID = TBL_TRN_PAYMENT_ACCOUNT.PAYMENTID_REF  LEFT OUTER JOIN
TBL_MST_GENERALLEDGER ON TBL_TRN_PAYMENT_ACCOUNT.GLID_REF=TBL_MST_GENERALLEDGER.GLID LEFT OUTER JOIN 
TBL_MST_BANK ON TBL_TRN_PAYMENT_HDR.CASH_BANK_ID = TBL_MST_BANK.BID  LEFT OUTER JOIN
TBL_MST_BRANCH AS BR WITH (NOLOCK) ON TBL_TRN_PAYMENT_HDR.BRID_REF=BR.BRID LEFT OUTER JOIN
  TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT OUTER JOIN
TBL_MST_SUBLEDGER ON TBL_TRN_PAYMENT_HDR.CUSTMER_VENDOR_ID = TBL_MST_SUBLEDGER.SGLID

WHERE TBL_TRN_PAYMENT_HDR.CYID_REF=$this->CYID
AND TBL_TRN_PAYMENT_HDR.BRID_REF IN ($BranchName)
AND (TBL_TRN_PAYMENT_HDR.PAYMENT_DT BETWEEN '$this->From_Date' AND '$this->To_Date')
AND TBL_TRN_PAYMENT_HDR.PAYMENTID IN ($PAYMENTID)
AND TBL_TRN_PAYMENT_HDR.CASH_BANK_ID IN($BANKID)
AND TBL_TRN_PAYMENT_HDR.STATUS='$this->STATUS'"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
    
        return [
          'Payment No',
         'Payment Date',
         'Branch Group',
         'Branch Name',
         'Customer/Vendor/Account Code',
         'Description',
          'Bank/Cash',
          'Instrument Type',
         'Instrument No',
         'Total Payment Amount',
         'Doc No',
         'Doc Date ',
          'Doc Amount',
         'Remarks ',
          'Common Narration ' ,
         'Status',
         ];
         
    }
}





