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
use App\Exports\SalesChallanRegister;
use Maatwebsite\Excel\Facades\Excel;














class SalesChallanRegister implements FromCollection, WithHeadings
{


    protected $itemid;

 function __construct($SGLID,$From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMID,$STATUS,$CYID_REF,$SCID) {
        $this->ITEMID = $ITEMID;
        $this->SCID = $SCID;
        $this->SGLID = $SGLID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;     
         $this->STATUS = $STATUS;
        $this->CYID = $CYID_REF;
 }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        //dd($this->From_Date); 
        
        $ITEMID=implode(",",$this->ITEMID);
        $SCID=implode(",",$this->SCID);
        $SGLID=implode(",",$this->SGLID);
        $BranchName=implode(",",$this->BranchName);
 
       



      return collect( $data=DB::select("SELECT 
      TBL_TRN_SLSC01_HDR.SCNO,        
      TBL_TRN_SLSC01_HDR.SCDT,  
      BG.BG_DESC AS BRANCH_GROUP,   
      BR.BRNAME,       
      CG.DESCRIPTIONS AS CUSTOMER_GROUP,
      C.CCODE AS CUSTOMER_CODE, C.NAME AS CUSTOMER_NAME,C.SAP_CUSTOMER_CODE,C.SAP_CUSTOMER_NAME,
      TBL_MST_CITY.NAME AS City,
      TBL_MST_STATE.NAME AS STATE, 
      TBL_TRN_SLSO01_HDR.SONO,
      TBL_MST_ITEMGROUP.GROUPNAME,
      TBL_MST_ITEM.ICODE, TBL_MST_ITEM.NAME AS Item_Name, 
      TBL_MST_ITEM.ALPS_PART_NO,
      TBL_MST_ITEM.CUSTOMER_PART_NO,
      TBL_MST_ITEM.OEM_PART_NO,
      HSN.HSNCODE,
      TBL_MST_UOM.DESCRIPTIONS, 
      TBL_MST_BUSINESSUNIT.BUNAME,
      TBL_TRN_SLSO01_MAT.PENDING_QTY,
      TBL_TRN_SLSC01_MAT.PENDING_QTY AS Pendig_Challan_Qty,
      TBL_TRN_SLSC01_MAT.CHALLAN_MAINQTY-TBL_TRN_SLSC01_MAT.PENDING_QTY AS CONSUMED_QTY,
      TBL_TRN_SLSC01_MAT.CHALLAN_MAINQTY,
            CASE
                      WHEN TBL_TRN_SLSC01_HDR.STATUS ='A' THEN 'Approved'
                      WHEN TBL_TRN_SLSC01_HDR.STATUS = 'N' THEN 'Not Approved'
                      WHEN TBL_TRN_SLSC01_HDR.STATUS = 'c' THEN 'Cancelled'
                      WHEN TBL_TRN_SLSC01_HDR.STATUS = 'R' THEN 'Closed' 
                  END AS STATUS      
                          
                               
                               
      
      FROM                     TBL_TRN_SLSC01_HDR LEFT OUTER JOIN
                               
                               TBL_MST_SUBLEDGER (NOLOCK) ON TBL_TRN_SLSC01_HDR.SLID_REF = TBL_MST_SUBLEDGER.SGLID LEFT OUTER JOIN 
                               TBL_MST_CUSTOMER AS C ON TBL_MST_SUBLEDGER.SGLID = C.SLID_REF LEFT OUTER JOIN
                               TBL_MST_CUSTOMERGROUP AS CG ON C.CGID_REF = CG.CGID LEFT OUTER JOIN
                               TBL_MST_BRANCH AS BR WITH (NOLOCK) ON TBL_TRN_SLSC01_HDR.BRID_REF=BR.BRID LEFT OUTER JOIN
                               TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT OUTER JOIN
                               TBL_MST_CUSTOMERLOCATION ON TBL_TRN_SLSC01_HDR.BILLTO = TBL_MST_CUSTOMERLOCATION.CLID LEFT OUTER JOIN
                               TBL_MST_STATE ON TBL_MST_CUSTOMERLOCATION.STID_REF = TBL_MST_STATE.STID LEFT OUTER JOIN
                               TBL_MST_CITY ON TBL_MST_CUSTOMERLOCATION.CITYID_REF = TBL_MST_CITY.CITYID LEFT OUTER JOIN
                               TBL_TRN_SLSC01_MAT ON TBL_TRN_SLSC01_HDR.SCID = TBL_TRN_SLSC01_MAT.SCID_REF LEFT OUTER JOIN
                               TBL_TRN_SLSO01_HDR ON TBL_TRN_SLSC01_MAT.SO = TBL_TRN_SLSO01_HDR.SOID LEFT OUTER JOIN
                               TBL_MST_ITEM ON TBL_TRN_SLSC01_MAT.ITEMID_REF = TBL_MST_ITEM.ITEMID LEFT OUTER JOIN
                               TBL_MST_BUSINESSUNIT ON TBL_MST_ITEM.BUID_REF = TBL_MST_BUSINESSUNIT.BUID LEFT OUTER JOIN
                               TBL_MST_HSN AS HSN WITH (NOLOCK) ON TBL_MST_ITEM.HSNID_REF=HSN.HSNID LEFT JOIN
                               TBL_MST_ITEMGROUP ON TBL_MST_ITEM.ITEMGID_REF = TBL_MST_ITEMGROUP.ITEMGID LEFT OUTER JOIN
                               TBL_MST_UOM ON TBL_MST_ITEM.MAIN_UOMID_REF = TBL_MST_UOM.UOMID LEFT OUTER JOIN
                               TBL_TRN_SLSO01_MAT ON TBL_TRN_SLSC01_MAT.SO = TBL_TRN_SLSO01_MAT.SOID_REF AND TBL_TRN_SLSC01_MAT.ITEMID_REF = TBL_TRN_SLSO01_MAT.ITEMID_REF AND 
                               TBL_TRN_SLSC01_MAT.SEID_REF = TBL_TRN_SLSO01_MAT.SEQID_REF AND TBL_TRN_SLSC01_MAT.SQID_REF = TBL_TRN_SLSO01_MAT.SQA
      WHERE                    TBL_TRN_SLSC01_HDR.STATUS='$this->STATUS' and C.SLID_REF IN ($SGLID)  AND TBL_MST_ITEM.ITEMID IN ($ITEMID) 
                               AND TBL_TRN_SLSC01_HDR.CYID_REF=$this->CYID AND TBL_TRN_SLSC01_HDR.BRID_REF IN ($BranchName) AND TBL_TRN_SLSC01_HDR.SCID IN ($SCID) AND (TBL_TRN_SLSC01_HDR.SCDT BETWEEN '$this->From_Date' AND '$this->To_Date')
      
                  "));
      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
            'Sales Challan No',
            'Sales Challan Date',
            'Branch Group',
            'Branch Name',
            'Customer Group',
            'Customer Code',
            'Customer Name',
            'SAP Customer Code',
            'SAP Customer Name',
            'City',
            'State',
            'Sales Order No',
            'Item Group',
            'Item Code',
            'Item Name',
            'ALPS Part No',
            'Customer Part No',
            'OEM Part No',
            'HSN Code',
            'Uom',
            'Business Unit',
            'SO Balance',    
            'Pending Qty',
            'Consumed Qty',
            'Actual Qty',
            'Status',
        ];
    }
}





