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
use App\Exports\MRS_Register;
use Maatwebsite\Excel\Facades\Excel;














class MRS_Register implements FromCollection, WithHeadings
{


 function __construct($MRSID,$From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMGID,$ITEMID,$STATUS,$CYID_REF) {
        $this->ITEMID = $ITEMID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->ITEMGID = $ITEMGID;
        $this->STATUS = $STATUS;
        $this->CYID = $CYID_REF;
        $this->MRSID = $MRSID;
 
 }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        //dd($this->From_Date); 
        
        $ITEMID=implode(",",$this->ITEMID);        
        $BranchName=implode(",",$this->BranchName);      
        $ITEMGID=implode(",",$this->ITEMGID);
        $ITEMGID=implode(",",$this->ITEMGID);
        $MRSID=implode(",",$this->MRSID);

       



      return collect( $data=DB::select("     SELECT
      TBL_TRN_MRQS01_HDR.MRS_NO,
      TBL_TRN_MRQS01_HDR.MRS_DT,
      BG.BG_DESC AS BRANCH_GROUP,
      BR.BRNAME,
      TBL_TRN_MRQS01_MAT.EXP_DATE,
      TBL_MST_ITEMGROUP.GROUPNAME,
      TBL_MST_ITEM.ICODE,
      TBL_MST_ITEM.NAME,
      HSN.HSNCODE,
      TBL_MST_UOM.DESCRIPTIONS AS UOM,
      TBL_MST_BUSINESSUNIT.BUNAME,
      TBL_MST_ITEM.ALPS_PART_NO,
      TBL_MST_ITEM.CUSTOMER_PART_NO,
      TBL_MST_ITEM.OEM_PART_NO,
      TBL_MST_DEPARTMENT.NAME AS DEPARTMENT_NAME,				
      TBL_TRN_MRQS01_MAT.PENDING_QTY,
      (TBL_TRN_MRQS01_MAT.QTY-TBL_TRN_MRQS01_MAT.PENDING_QTY) AS CONSUMED_QTY,
      TBL_TRN_MRQS01_MAT.QTY,		
  
      CASE
          WHEN TBL_TRN_MRQS01_HDR.STATUS ='A' THEN 'Approved'
          WHEN TBL_TRN_MRQS01_HDR.STATUS = 'N' THEN 'Not Approved'
          WHEN TBL_TRN_MRQS01_HDR.STATUS = 'c' THEN 'Cancelled'
          WHEN TBL_TRN_MRQS01_HDR.STATUS = 'R' THEN 'Closed'
            
        END AS STATUS
  
      FROM TBL_TRN_MRQS01_HDR LEFT OUTER JOIN
        TBL_TRN_MRQS01_MAT ON TBL_TRN_MRQS01_MAT.MRSID_REF = TBL_TRN_MRQS01_HDR.MRSID LEFT OUTER JOIN
        TBL_MST_ITEM ON TBL_TRN_MRQS01_MAT.ITEMID_REF = TBL_MST_ITEM.ITEMID LEFT OUTER JOIN
        TBL_MST_HSN AS HSN WITH (NOLOCK) ON TBL_MST_ITEM.HSNID_REF=HSN.HSNID LEFT OUTER JOIN
        TBL_MST_BUSINESSUNIT ON TBL_MST_ITEM.BUID_REF = TBL_MST_BUSINESSUNIT.BUID LEFT OUTER JOIN
        TBL_MST_BRANCH AS BR WITH (NOLOCK) ON TBL_TRN_MRQS01_HDR.BRID_REF=BR.BRID LEFT OUTER JOIN
        TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT OUTER JOIN
        TBL_MST_UOM ON TBL_MST_ITEM.MAIN_UOMID_REF = TBL_MST_UOM.UOMID LEFT OUTER JOIN
        TBL_MST_ITEMGROUP ON TBL_MST_ITEM.ITEMGID_REF = TBL_MST_ITEMGROUP.ITEMGID LEFT OUTER JOIN
        TBL_MST_DEPARTMENT ON TBL_TRN_MRQS01_HDR.DEPID_REF = TBL_MST_DEPARTMENT.DEPID LEFT OUTER JOIN
        TBL_MST_COMPANY ON TBL_TRN_MRQS01_HDR.CYID_REF = TBL_MST_COMPANY.CYID
  
  
      WHERE  TBL_MST_ITEM.ITEMID IN ($ITEMID) AND 
      TBL_MST_ITEM.ITEMGID_REF IN ($ITEMGID) AND 
      (TBL_TRN_MRQS01_HDR.STATUS = '$this->STATUS') AND 
      TBL_TRN_MRQS01_HDR.MRSID IN ($MRSID)
      AND TBL_TRN_MRQS01_HDR.CYID_REF=$this->CYID
      AND TBL_TRN_MRQS01_HDR.BRID_REF IN ($BranchName)
      AND (TBL_TRN_MRQS01_HDR.MRS_DT BETWEEN '$this->From_Date' AND '$this->To_Date')
      
  "));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
								
        return [

          'MRS No',
          ' MRS Date',
          'Branch Group',
          'Branch Name',
          'MRS Expected Date',
          'Item Group Name',
          'Item Code',
          'Item Name',
          'HSN/SAC Code',
          'UOM',
          'Business Unit',
          'ALPS Part No',
          'Customer Part No',
          'OEM Part No',
          'To Department',
          'Pending Qty',
          'Consumed Qty',
          'MRS QTY',
          'Status',
      
        ];


         
   
    }
}





