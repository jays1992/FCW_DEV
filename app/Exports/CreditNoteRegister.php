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
use App\Exports\CreditNoteRegister;
use Maatwebsite\Excel\Facades\Excel;














class CreditNoteRegister implements FromCollection, WithHeadings
{


 function __construct($SGLID,$CSVID,$From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMGID,$ITEMID,$CYID) {
        $this->ITEMID = $ITEMID;
        $this->SGLID = $SGLID;
        $this->CSVID = $CSVID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->ITEMGID = $ITEMGID;

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
        $CSVID=implode(",",$this->CSVID);
        $BranchName=implode(",",$this->BranchName);      
        $ITEMGID=implode(",",$this->ITEMGID);
       



      return collect( $data=DB::select("			SELECT 
			TBL_TRN_CRSV01_HDR.CSV_NO,
			TBL_TRN_CRSV01_HDR.CSV_DT,
			BG.BG_DESC AS BRANCH_GROUP,
			BR.BRNAME,
			CG.DESCRIPTIONS AS CUSTOMER_GROUP,
			C.CCODE AS CUSTOMER_CODE,    
			C.NAME AS CUSTOMER_NAME,
			C.SAP_CUSTOMER_CODE,
			C.SAP_CUSTOMER_NAME,
			HSN.HSNCODE,
			TBL_MST_ITEMGROUP.GROUPNAME,
			TBL_MST_ITEM.ICODE,
			TBL_MST_ITEM.NAME,
			TBL_MST_UOM.DESCRIPTIONS AS UOM,
			TBL_MST_BUSINESSUNIT.BUNAME,
			TBL_MST_ITEM.ALPS_PART_NO,
			TBL_MST_ITEM.CUSTOMER_PART_NO,
			TBL_MST_ITEM.OEM_PART_NO,
			TBL_TRN_CRSV01_MAT.CR_NOTE_QTY,
			TBL_TRN_CRSV01_MAT.CR_NOTE_RATE,
			TBL_TRN_CRSV01_MAT.CR_NOTE_AMT,			 		
			(TBL_TRN_CRSV01_MAT.CR_NOTE_AMT)*(case when TBL_TRN_CRSV01_MAT.IGST IS not NULL then convert(numeric(14,2),(TBL_TRN_CRSV01_MAT.IGST)/100) else  0 end) AS IGST,
			(TBL_TRN_CRSV01_MAT.CR_NOTE_AMT)*(case when TBL_TRN_CRSV01_MAT.CGST IS not NULL then convert(numeric(14,2),(TBL_TRN_CRSV01_MAT.CGST)/100) else  0 end) AS CGST,
			(TBL_TRN_CRSV01_MAT.CR_NOTE_AMT)*(case when TBL_TRN_CRSV01_MAT.SGST IS not NULL then convert(numeric(14,2),(TBL_TRN_CRSV01_MAT.SGST)/100) else  0 end) AS SGST,			
			
			((case when TBL_TRN_CRSV01_MAT.IGST IS not NULL then convert(numeric(14,2),(TBL_TRN_CRSV01_MAT.CR_NOTE_AMT*TBL_TRN_CRSV01_MAT.IGST)/100) else  0 end)+
			(case when TBL_TRN_CRSV01_MAT.CGST IS not NULL then convert(numeric(14,2),(TBL_TRN_CRSV01_MAT.CR_NOTE_AMT*TBL_TRN_CRSV01_MAT.CGST)/100) else  0 end)+
			(case when TBL_TRN_CRSV01_MAT.SGST IS not NULL then convert(numeric(14,2),(TBL_TRN_CRSV01_MAT.CR_NOTE_AMT*TBL_TRN_CRSV01_MAT.SGST)/100) else  0 end)) AS TOTAL_TAX,
			
			((TBL_TRN_CRSV01_MAT.CR_NOTE_AMT)+(case when TBL_TRN_CRSV01_MAT.IGST IS not NULL then convert(numeric(14,2),(TBL_TRN_CRSV01_MAT.CR_NOTE_AMT*TBL_TRN_CRSV01_MAT.IGST)/100) else  0 end)+
			(case when TBL_TRN_CRSV01_MAT.CGST IS not NULL then convert(numeric(14,2),(TBL_TRN_CRSV01_MAT.CR_NOTE_AMT*TBL_TRN_CRSV01_MAT.CGST)/100) else  0 end)+
			(case when TBL_TRN_CRSV01_MAT.SGST IS not NULL then convert(numeric(14,2),(TBL_TRN_CRSV01_MAT.CR_NOTE_AMT*TBL_TRN_CRSV01_MAT.SGST)/100) else  0 end)) AS TOTAL_CREDIT_NOTE_AMOUNT,
			
			TBL_TRN_CRSV01_HDR.REASON_CR_NOTE,
			TBL_TRN_CRSV01_HDR.NARRATION			

			FROM  TBL_TRN_CRSV01_HDR LEFT OUTER JOIN
				  TBL_TRN_CRSV01_MAT ON TBL_TRN_CRSV01_HDR.CSVID = TBL_TRN_CRSV01_MAT.CSVID_REF LEFT OUTER JOIN			  
				  TBL_MST_SUBLEDGER (NOLOCK) ON TBL_TRN_CRSV01_HDR.SGLID_REF = TBL_MST_SUBLEDGER.SGLID LEFT OUTER JOIN 
				  TBL_MST_CUSTOMER AS C ON TBL_MST_SUBLEDGER.SGLID = C.SLID_REF LEFT OUTER JOIN
				  TBL_MST_CUSTOMERGROUP AS CG ON C.CGID_REF = CG.CGID LEFT OUTER JOIN
				  TBL_MST_BRANCH AS BR WITH (NOLOCK) ON TBL_TRN_CRSV01_HDR.BRID_REF=BR.BRID LEFT OUTER JOIN
				  TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT OUTER JOIN
				  TBL_MST_ITEM ON TBL_TRN_CRSV01_MAT.ITEMID_REF = TBL_MST_ITEM.ITEMID LEFT OUTER JOIN
				  TBL_MST_HSN AS HSN WITH (NOLOCK) ON TBL_MST_ITEM.HSNID_REF=HSN.HSNID LEFT JOIN
				  TBL_MST_BUSINESSUNIT ON TBL_MST_ITEM.BUID_REF = TBL_MST_BUSINESSUNIT.BUID LEFT OUTER JOIN
				  TBL_MST_ITEMGROUP ON TBL_MST_ITEM.ITEMGID_REF = TBL_MST_ITEMGROUP.ITEMGID LEFT OUTER JOIN
				  TBL_MST_UOM ON TBL_MST_ITEM.MAIN_UOMID_REF = TBL_MST_UOM.UOMID



        WHERE   TBL_MST_ITEM.ITEMID IN ($ITEMID) AND 
            TBL_MST_ITEM.ITEMGID_REF IN ($ITEMGID)
            AND TBL_TRN_CRSV01_HDR.CYID_REF=$this->CYID
            AND TBL_TRN_CRSV01_HDR.BRID_REF IN ($BranchName)
            AND (TBL_TRN_CRSV01_HDR.CSV_DT BETWEEN '$this->From_Date' AND '$this->To_Date')
            AND TBL_TRN_CRSV01_HDR.CSVID IN ($CSVID)
            AND TBL_TRN_CRSV01_HDR.SGLID_REF IN ($SGLID)"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
          'CSV NO',
          ' CSV Date',
          'Branch Group',
          'Branch Name',
          'Customer Group',
          'Customer Code',
          'Customer Name',
          'SAP Customer Code',
          'SAP Customer Name',
          'HSN/SAC Code',
          'Item Group Name',
          ' Item Code',
          ' Item Name',
          'UOM',
          'Business Unit',
          'ALPS Part No',
          'Customer Part No',
          ' OEM Part No',
          'Credit Note QTY',
          'Credit Note Rate',
          'Credit Note Amount',
          'IGST',
          'CGST',
          'SGST',
          'Total TAX',
          'Total Credit Note Amount',
          'Reason of Credit Note',
          'Common Narration',
  
        ];
    }
}





