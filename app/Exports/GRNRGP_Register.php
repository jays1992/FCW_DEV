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
use App\Exports\GRNRGP_Register;
use Maatwebsite\Excel\Facades\Excel;














class GRNRGP_Register implements FromCollection, WithHeadings
{


 function __construct($GRNID,$From_Date,$To_Date,$BranchGroup,$BranchName,$STATUS,$CYID) {
     
        $this->GRNID = $GRNID;
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
        
      
        $GRNID=implode(",",$this->GRNID);
        $BranchName=implode(",",$this->BranchName);      

       



      return collect( $data=DB::select("				  SELECT TBL_TRN_IGRN01_HDR.GRN_NO, 
      TBL_TRN_IGRN01_HDR.GRN_DT,
      BG.BG_DESC AS BRANCH_GROUP,
      BR.BRNAME,
      V.VCODE AS VENDOR_CODE,
      V.NAME AS VENDOR_NAME,
      V.SAP_VENDOR_CODE,
      SAP_VENDOR_NAME1,
      TBL_MST_ITEMGROUP.GROUPNAME AS ItemGroup_Name,
    TBL_MST_ITEM.ICODE AS Item_Code, 
    TBL_MST_ITEM.NAME AS Item_Name,				  
    TBL_MST_ITEM.ALPS_PART_NO, 
    TBL_MST_ITEM.CUSTOMER_PART_NO, 
    TBL_MST_ITEM.OEM_PART_NO,
    TBL_MST_UOM.DESCRIPTIONS AS UoM,
    TBL_TRN_IGRN01_MAT.RECEIVED_QTY_MU,
    TBL_TRN_IRGP01_HDR.RGP_NO, TBL_TRN_IRGP01_HDR.RGP_DT,
    TBL_TRN_IRGP01_MAT.ISSUE_QTY AS RGP_Qty,
     CASE
 WHEN TBL_TRN_IRGP01_HDR.STATUS ='A' THEN 'Approved'
 WHEN TBL_TRN_IRGP01_HDR.STATUS = 'N' THEN 'Not Approved'
 WHEN TBL_TRN_IRGP01_HDR.STATUS = 'c' THEN 'Cancelled'
 WHEN TBL_TRN_IRGP01_HDR.STATUS = 'R' THEN 'Closed'

 END AS STATUS 				  
      
      

FROM        TBL_TRN_IGRN01_HDR LEFT OUTER JOIN
              TBL_TRN_IGRN01_MAT ON TBL_TRN_IGRN01_HDR.GRNID = TBL_TRN_IGRN01_MAT.GRNID_REF LEFT OUTER JOIN
      TBL_MST_SUBLEDGER ON TBL_TRN_IGRN01_HDR.VID_REF = TBL_MST_SUBLEDGER.SGLID LEFT OUTER JOIN
              TBL_MST_VENDOR AS V ON TBL_MST_SUBLEDGER.SGLID = V.SLID_REF LEFT OUTER JOIN
      TBL_MST_BRANCH AS BR WITH (NOLOCK) ON TBL_TRN_IGRN01_HDR.BRID_REF=BR.BRID LEFT OUTER JOIN
      TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT OUTER JOIN
              TBL_TRN_IRGP01_HDR ON TBL_TRN_IGRN01_MAT.RGPID_REF = TBL_TRN_IRGP01_HDR.RGPID LEFT OUTER JOIN
              TBL_TRN_IRGP01_MAT ON TBL_TRN_IRGP01_HDR.RGPID = TBL_TRN_IRGP01_MAT.RGPID_REF LEFT OUTER JOIN
              TBL_MST_ITEM ON TBL_TRN_IGRN01_MAT.ITEMID_REF = TBL_MST_ITEM.ITEMID LEFT OUTER JOIN
              TBL_MST_ITEMGROUP ON TBL_MST_ITEM.ITEMGID_REF = TBL_MST_ITEMGROUP.ITEMGID LEFT OUTER JOIN
              TBL_MST_BUSINESSUNIT ON TBL_MST_ITEM.BUID_REF = TBL_MST_BUSINESSUNIT.BUID LEFT OUTER JOIN
              TBL_MST_UOM ON TBL_MST_ITEM.MAIN_UOMID_REF = TBL_MST_UOM.UOMID LEFT OUTER JOIN
              TBL_MST_COMPANY ON TBL_TRN_IGRN01_HDR.CYID_REF = TBL_MST_COMPANY.CYID

      WHERE TBL_TRN_IGRN01_HDR.STATUS='$this->STATUS'
        AND TBL_TRN_IGRN01_HDR.CYID_REF=$this->CYID
      AND TBL_TRN_IGRN01_HDR.BRID_REF IN ($BranchName)
      AND (TBL_TRN_IGRN01_HDR.GRN_DT BETWEEN '$this->From_Date' AND '$this->To_Date')
      AND TBL_TRN_IGRN01_HDR.GRNID IN ($GRNID)
      "));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
          'GRN No',
          'GRN Date',
          'Branch Group',
          'Branch Name',
          'Vendor Code',
          'Vendor Name',
          'SAP Vendor Code',
          'SAP Vendor Name',
          'Item Group',
          'Item Code',
          'Item Name',   
          'ALPS Part No',
          'Customer Part No',
          'OEM Part No',
          'UOM',
          'Received Qty',
          'RGP No',
          'RGP Date',
          'RGP Qty',
          'Status',				
      ];
    }
}





