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
use App\Exports\DebitNoteRegister;
use Maatwebsite\Excel\Facades\Excel;














class DebitNoteRegister implements FromCollection, WithHeadings
{


 function __construct($SGLID,$DSVID,$From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMGID,$ITEMID,$CYID) {
        $this->ITEMID = $ITEMID;
        $this->SGLID = $SGLID;
        $this->DSVID = $DSVID;
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
        $DSVID=implode(",",$this->DSVID);
        $BranchName=implode(",",$this->BranchName);      
        $ITEMGID=implode(",",$this->ITEMGID);
       



      return collect( $data=DB::select("	SELECT
      TBL_TRN_DRSV01_HDR.DSV_NO,
      BG.BG_DESC AS BRANCH_GROUP,
      BR.BRNAME,
      VG.DESCRIPTIONS AS VENDOR_GROUP,
      V.VCODE AS VENDOR_CODE,
      V.NAME AS VENDOR_NAME,
      V.SAP_VENDOR_CODE,
      V.SAP_VENDOR_NAME1,				
      HSN.HSNCODE,
      TBL_TRN_DRSV01_HDR.DSV_DT,
      TBL_MST_SUBLEDGER.SLNAME,
      TBL_MST_ITEMGROUP.GROUPNAME, 
      TBL_MST_ITEM.ICODE,
      TBL_MST_ITEM.NAME,
      TBL_MST_UOM.DESCRIPTIONS AS UOM,
      TBL_MST_BUSINESSUNIT.BUNAME,
      TBL_MST_ITEM.ALPS_PART_NO,
      TBL_MST_ITEM.CUSTOMER_PART_NO,
      TBL_MST_ITEM.OEM_PART_NO,
      TBL_TRN_DRSV01_MAT.DR_NOTE_QTY,
      TBL_TRN_DRSV01_MAT.DR_NOTE_RATE,
      TBL_TRN_DRSV01_MAT.DR_NOTE_AMT,
      
    (TBL_TRN_DRSV01_MAT.DR_NOTE_AMT)*(case when TBL_TRN_DRSV01_MAT.IGST IS not NULL then convert(numeric(14,2),(TBL_TRN_DRSV01_MAT.IGST)/100) else  0 end) AS IGST,
    (TBL_TRN_DRSV01_MAT.DR_NOTE_AMT)*(case when TBL_TRN_DRSV01_MAT.CGST IS not NULL then convert(numeric(14,2),(TBL_TRN_DRSV01_MAT.CGST)/100) else  0 end) AS CGST,
    (TBL_TRN_DRSV01_MAT.DR_NOTE_AMT)*(case when TBL_TRN_DRSV01_MAT.SGST IS not NULL then convert(numeric(14,2),(TBL_TRN_DRSV01_MAT.SGST)/100) else  0 end) AS SGST,			
      
    ((case when TBL_TRN_DRSV01_MAT.IGST IS not NULL then convert(numeric(14,2),(TBL_TRN_DRSV01_MAT.DR_NOTE_AMT*TBL_TRN_DRSV01_MAT.IGST)/100) else  0 end)+
    (case when TBL_TRN_DRSV01_MAT.CGST IS not NULL then convert(numeric(14,2),(TBL_TRN_DRSV01_MAT.DR_NOTE_AMT*TBL_TRN_DRSV01_MAT.CGST)/100) else  0 end)+
    (case when TBL_TRN_DRSV01_MAT.SGST IS not NULL then convert(numeric(14,2),(TBL_TRN_DRSV01_MAT.DR_NOTE_AMT*TBL_TRN_DRSV01_MAT.SGST)/100) else  0 end)) AS TOTAL_TAX,
     

    ((TBL_TRN_DRSV01_MAT.DR_NOTE_AMT)+(case when TBL_TRN_DRSV01_MAT.IGST IS not NULL then convert(numeric(14,2),(TBL_TRN_DRSV01_MAT.DR_NOTE_AMT*TBL_TRN_DRSV01_MAT.IGST)/100) else  0 end)+
    (case when TBL_TRN_DRSV01_MAT.CGST IS not NULL then convert(numeric(14,2),(TBL_TRN_DRSV01_MAT.DR_NOTE_AMT*TBL_TRN_DRSV01_MAT.CGST)/100) else  0 end)+
    (case when TBL_TRN_DRSV01_MAT.SGST IS not NULL then convert(numeric(14,2),(TBL_TRN_DRSV01_MAT.DR_NOTE_AMT*TBL_TRN_DRSV01_MAT.SGST)/100) else  0 end)) AS TOTAL_DEBIT_NOTE_AMOUNT,
    
    TBL_TRN_DRSV01_HDR.REASON_DR_NOTE, 
    TBL_TRN_DRSV01_HDR.NARRATION	 
      


      FROM    TBL_TRN_DRSV01_HDR LEFT OUTER JOIN
          TBL_TRN_DRSV01_MAT ON TBL_TRN_DRSV01_HDR.DSVID = TBL_TRN_DRSV01_MAT.DSVID_REF LEFT OUTER JOIN				  
          TBL_MST_SUBLEDGER (NOLOCK) ON TBL_TRN_DRSV01_HDR.SGLID_REF = TBL_MST_SUBLEDGER.SGLID LEFT OUTER JOIN 
          TBL_MST_VENDOR AS V ON TBL_MST_SUBLEDGER.SGLID = V.SLID_REF LEFT OUTER JOIN
          TBL_MST_VENDORGROUP AS VG ON V.VGID_REF = VG.VGID LEFT OUTER JOIN
          TBL_MST_BRANCH AS BR WITH (NOLOCK) ON TBL_TRN_DRSV01_HDR.BRID_REF=BR.BRID LEFT OUTER JOIN
          TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT OUTER JOIN
          TBL_MST_ITEM ON TBL_TRN_DRSV01_MAT.ITEMID_REF = TBL_MST_ITEM.ITEMID LEFT OUTER JOIN
          TBL_MST_HSN AS HSN WITH (NOLOCK) ON TBL_MST_ITEM.HSNID_REF=HSN.HSNID LEFT OUTER JOIN
          TBL_MST_BUSINESSUNIT ON TBL_MST_ITEM.BUID_REF = TBL_MST_BUSINESSUNIT.BUID LEFT OUTER JOIN
          TBL_MST_ITEMGROUP ON TBL_MST_ITEM.ITEMGID_REF = TBL_MST_ITEMGROUP.ITEMGID LEFT OUTER JOIN
          TBL_MST_UOM ON TBL_MST_ITEM.MAIN_UOMID_REF = TBL_MST_UOM.UOMID


        WHERE   TBL_MST_ITEM.ITEMID IN ($ITEMID) AND 
            TBL_MST_ITEM.ITEMGID_REF IN ($ITEMGID)
            AND TBL_TRN_DRSV01_HDR.CYID_REF=$this->CYID
            AND TBL_TRN_DRSV01_HDR.BRID_REF IN ($BranchName)
            AND (TBL_TRN_DRSV01_HDR.DSV_DT BETWEEN '$this->From_Date' AND '$this->To_Date')
            AND TBL_TRN_DRSV01_HDR.DSVID IN ($DSVID)
            AND TBL_TRN_DRSV01_HDR.SGLID_REF IN ($SGLID)"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [

					'DSV NO',
					'Branch Group',
					'Branch Name',
					'Vendor Group',
					'Vendor Code',
					'Vendor Name',
					'SAP Vendor Code ',
					'SAP Vendor Name',
					'HSN/SAC Code',
					'DSV Date',
					'Vendor Name',
					'Item Group Name',
					' Item Code',
					' Item Name',
					'UOM',
					'Business Unit',
					'ALPS Part No',
					'Customer Part No',
					' OEM Part No',
					'Debit Note QTY',
					'Debit Note Rate',
					'Debit Note Amount',
					'IGST',
					'CGST',
					'SGST',
					'Total TAX',
					'Total Debit Note Amount',
					'Reason of Debit Note',
					'Common Narration',

				];
    }
}





