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
use App\Exports\BPORegister;
use Maatwebsite\Excel\Facades\Excel;














class BPORegister implements FromCollection, WithHeadings
{


 function __construct($SGLID,$From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMGID,$ITEMID,$STATUS,$CYID_REF) {
        $this->ITEMID = $ITEMID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->ITEMGID = $ITEMGID;
        $this->STATUS = $STATUS;
        $this->CYID = $CYID_REF;
        $this->SGLID = $SGLID;
 
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
        $SGLID=implode(",",$this->SGLID);

       



      return collect( $data=DB::select(" SELECT    
      H.BPO_NO,
      H.BPO_DT,
      BR.BRNAME,					
      BG.BG_DESC AS BRANCH_GROUP,
      VG.VGCODE,
      VG.DESCRIPTIONS AS VENDOR_GROUP,
      S.SGLCODE,
      S.SLNAME,
      V.SAP_VENDOR_CODE,
      V.SAP_VENDOR_NAME1,
      I.NAME AS ITEM_NAME,
      HSN.HSNCODE,
      HSN.HSNDESCRIPTION AS HSN,
      MU.UOMCODE,
      B.BUNAME,
      I.ALPS_PART_NO AS ALPS_PART_NO,
      I.CUSTOMER_PART_NO AS CUSTOMER_PART_NO,
      I.OEM_PART_NO AS OEM_PART_NO,
      M.RATEP_UOM,

CASE
 WHEN H.STATUS ='A' THEN 'Approved'
 WHEN H.STATUS = 'N' THEN 'Not Approved'
 WHEN H.STATUS = 'c' THEN 'Cancelled'
 WHEN H.STATUS = 'R' THEN 'Closed'

END AS STATUS


FROM TBL_TRN_PROR03_MAT AS M (NOLOCK) LEFT OUTER JOIN
TBL_TRN_PROR03_HDR AS H (NOLOCK) ON H.BPOID = M.BPOID_REF  LEFT OUTER JOIN						
TBL_MST_SUBLEDGER AS S (NOLOCK) ON H.VID_REF = S.SGLID LEFT OUTER JOIN
TBL_MST_ITEM AS I (NOLOCK) ON M.ITEMID_REF = I.ITEMID LEFT OUTER JOIN
TBL_MST_BUSINESSUNIT AS B (NOLOCK) ON I.BUID_REF = B.BUID LEFT OUTER JOIN
TBL_MST_UOM AS MU (NOLOCK)  ON M.UOMID_REF = MU.UOMID LEFT OUTER JOIN
TBL_MST_VENDOR AS V (NOLOCK) ON H.VID_REF=V.SLID_REF LEFT JOIN                         
TBL_MST_VENDORLOCATION AS CL1 (NOLOCK) ON V.VID = CL1.LID AND CL1.SHIPTO=1 LEFT OUTER JOIN
TBL_MST_ITEMGROUP AS G (NOLOCK) ON I.ITEMGID_REF = G.ITEMGID LEFT JOIN
TBL_MST_BRANCH AS BR WITH (NOLOCK) ON H.BRID_REF=BR.BRID LEFT JOIN
TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT JOIN
TBL_MST_HSN AS HSN WITH (NOLOCK) ON I.HSNID_REF=HSN.HSNID LEFT JOIN						 
TBL_MST_VENDORGROUP AS VG WITH (NOLOCK) ON V.VGID_REF=VG.VGID
      
      
      
                               
WHERE(H.STATUS = '$this->STATUS')
AND (H.CYID_REF = $this->CYID) 
AND (H.BRID_REF in ( $BranchName)) 
AND (H.BPO_DT BETWEEN '$this->From_Date' AND '$this->To_Date')
AND (H.VID_REF in ( $SGLID))
AND (I.ITEMGID_REF in ( $ITEMGID))
AND (M.ITEMID_REF in ( $ITEMID))"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        		
 return [
    'Order No',
    'Order Date',
    'Branch Name',
    'Branch Group',
    'Vendor Group Code',
    'Vendor  Group',
    'Vendor Code',
    'Vendor Name',
    'SAP Vendor Code',
    'SAP Vendor Name',
    'Item Name',
    'HSN Code',
    'HSN Name',
    'Uom',
    'Business Unit',
    'ALPS Part No',            
    'Customer Part No',
    'OEM Part No',
    'RATE',
    'Status',		
    
];
    }
}





