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
use App\Exports\NRGPRegister;
use Maatwebsite\Excel\Facades\Excel;














class NRGPRegister implements FromCollection, WithHeadings
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
       



      return collect( $data=DB::select("SELECT TBL_TRN_NRGP01_HDR.NRGP_NO,     
      TBL_TRN_NRGP01_HDR.NRGP_DT,       
      BG.BG_DESC AS BRANCH_GROUP, 
      BR.BRNAME,      
      VG.DESCRIPTIONS AS VENDOR_GROUP,
      V.VCODE AS VENDOR_CODE, 
      V.NAME AS VENDOR_NAME,
      V.SAP_VENDOR_CODE,
      V.SAP_VENDOR_NAME1,
      HSN.HSNCODE,
      TBL_MST_ITEMGROUP.GROUPNAME,
      TBL_MST_ITEM.ICODE, 
      TBL_MST_ITEM.NAME,
      B.BUNAME,
      TBL_MST_ITEM.ALPS_PART_NO,
      TBL_MST_ITEM.CUSTOMER_PART_NO,
      TBL_MST_ITEM.OEM_PART_NO,
      TBL_TRN_NRGP01_MAT.NRGP_QTY,
      TBL_TRN_NRGP01_MAT.REASON_FOR_NRGP, 
      TBL_TRN_NRGP01_HDR.PURPOSE, 
      
      
      
           CASE
           WHEN TBL_TRN_NRGP01_HDR.STATUS ='A' THEN 'Approved'
           WHEN TBL_TRN_NRGP01_HDR.STATUS = 'N' THEN 'Not Approved'
           WHEN TBL_TRN_NRGP01_HDR.STATUS = 'c' THEN 'Cancelled'
           WHEN TBL_TRN_NRGP01_HDR.STATUS = 'R' THEN 'Closed'
         
           END AS STATUS 	
      
                  
      FROM                     TBL_TRN_NRGP01_HDR LEFT OUTER JOIN
                               TBL_TRN_NRGP01_MAT ON TBL_TRN_NRGP01_HDR.NRGPID = TBL_TRN_NRGP01_MAT.NRGPID_REF LEFT OUTER JOIN
                               TBL_MST_ITEM ON TBL_TRN_NRGP01_MAT.ITEMID_REF = TBL_MST_ITEM.ITEMID LEFT OUTER JOIN
                   TBL_MST_BUSINESSUNIT AS B ON B.BUID=TBL_MST_ITEM.BUID_REF LEFT jOIN 
                   TBL_MST_HSN AS HSN WITH (NOLOCK) ON TBL_MST_ITEM.HSNID_REF=HSN.HSNID LEFT OUTER JOIN
                               TBL_MST_UOM ON TBL_MST_ITEM.MAIN_UOMID_REF = TBL_MST_UOM.UOMID LEFT OUTER JOIN
                   TBL_MST_ITEMGROUP ON TBL_MST_ITEM.ITEMGID_REF = TBL_MST_ITEMGROUP.ITEMGID LEFT OUTER JOIN
                    TBL_MST_SUBLEDGER (NOLOCK) ON TBL_TRN_NRGP01_HDR.VID_REF = TBL_MST_SUBLEDGER.SGLID LEFT OUTER JOIN 
                   TBL_MST_VENDOR AS V ON TBL_MST_SUBLEDGER.SGLID = V.SLID_REF LEFT OUTER JOIN
                   TBL_MST_VENDORGROUP AS VG ON V.VGID_REF = VG.VGID LEFT OUTER JOIN
                   TBL_MST_BRANCH AS BR WITH (NOLOCK) ON TBL_TRN_NRGP01_HDR.BRID_REF=BR.BRID LEFT OUTER JOIN
                               TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT OUTER JOIN
                               TBL_MST_COMPANY ON TBL_TRN_NRGP01_HDR.CYID_REF = TBL_MST_COMPANY.CYID
      WHERE					TBL_TRN_NRGP01_HDR.STATUS='$this->STATUS' 
                    AND TBL_MST_ITEM.ITEMID IN ($ITEMID) 
                    AND TBL_MST_ITEM.ITEMGID_REF IN ($ITEMGID) 
                    AND V.SLID_REF IN ($SGLID)
                    AND TBL_TRN_NRGP01_HDR.CYID_REF=$this->CYID 
                    AND TBL_TRN_NRGP01_HDR.BRID_REF IN ($BranchName)
                    AND (TBL_TRN_NRGP01_HDR.NRGP_DT BETWEEN '$this->From_Date' AND '$this->To_Date')
      "));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
          'NRGP No',
          'NRGP Date',
          'Branch Group',
          'Branch Name',
          'Vendor Group',
          'Vendor Code',
          'Vendor Name',
          'SAP Vendor Code',
          'SAP Vendor Name',
          'HSN Code',
          'Item Group',
          'Item Code',
          'Item Name',
          'Business Unit',    
          'ALPS Part No',
          'Customer Part No',
          'OEM Part No',
          'NRGP Qty',
          'Reason for NRGP',
          'Purpose',
          'Status',				
      ];
    }
}





