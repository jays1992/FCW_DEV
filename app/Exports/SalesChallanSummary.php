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
use App\Exports\SalesChallanSummary;
use Maatwebsite\Excel\Facades\Excel;














class SalesChallanSummary implements FromCollection, WithHeadings
{


   

 function __construct($SGLID,$From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMGID,$ITEMID,$STATUS,$CYID_REF) {
        $this->ITEMID = $ITEMID;
        $this->SGLID = $SGLID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;     
        $this->ITEMGID = $ITEMGID;
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
        $SGLID=implode(",",$this->SGLID);
        $BranchName=implode(",",$this->BranchName);
        $ITEMGID=implode(",",$this->ITEMGID);


      return collect( $data=DB::select("SELECT TBL_TRN_SLSC01_HDR.SCNO AS DOC_NO, 
      TBL_TRN_SLSC01_HDR.SCDT AS DOC_DT,
      BG.BG_DESC AS BRANCH_GROUP,
      BR.BRNAME,
      C.CCODE AS CUSTOMER_CODE, 
      C.NAME AS CUSTOMER_NAME, 
      C.SAP_CUSTOMER_CODE,
      C.SAP_CUSTOMER_NAME,    
	  CL.NAME AS BILLTO,
      SUM(TBL_TRN_SLSC01_MAT.CHALLAN_MAINQTY) AS ITEM_QTY,
      CASE
                WHEN TBL_TRN_SLSC01_HDR.STATUS ='A' THEN 'Approved'
                WHEN TBL_TRN_SLSC01_HDR.STATUS = 'N' THEN 'Not Approved'
                WHEN TBL_TRN_SLSC01_HDR.STATUS = 'c' THEN 'Cancelled'
                WHEN TBL_TRN_SLSC01_HDR.STATUS = 'R' THEN 'Closed' 
            END AS STATUS          
      FROM TBL_TRN_SLSC01_MAT (NOLOCK) 
      LEFT OUTER JOIN TBL_TRN_SLSC01_HDR (NOLOCK) ON TBL_TRN_SLSC01_HDR.SCID = TBL_TRN_SLSC01_MAT.SCID_REF  
      LEFT OUTER JOIN TBL_MST_SUBLEDGER (NOLOCK) ON TBL_TRN_SLSC01_HDR.SLID_REF = TBL_MST_SUBLEDGER.SGLID 
      LEFT OUTER JOIN TBL_MST_CUSTOMER AS C ON TBL_MST_SUBLEDGER.SGLID = C.SLID_REF 
      LEFT OUTER JOIN TBL_MST_ITEM (NOLOCK) ON TBL_TRN_SLSC01_MAT.ITEMID_REF = TBL_MST_ITEM.ITEMID 
      LEFT OUTER JOIN TBL_MST_BUSINESSUNIT (NOLOCK) ON TBL_MST_ITEM.BUID_REF = TBL_MST_BUSINESSUNIT.BUID 
      LEFT OUTER JOIN TBL_MST_UOM (NOLOCK) AS MU ON TBL_TRN_SLSC01_MAT.MAINUOMID_REF = MU.UOMID 
      LEFT OUTER JOIN TBL_MST_CUSTOMERLOCATION (NOLOCK) AS CL ON TBL_TRN_SLSC01_HDR.BILLTO = CL.CLID 
      LEFT OUTER JOIN TBL_MST_BRANCH AS BR WITH (NOLOCK) ON TBL_TRN_SLSC01_HDR.BRID_REF=BR.BRID 
      LEFT OUTER JOIN TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID 
      LEFT OUTER JOIN TBL_MST_CUSTOMERLOCATION (NOLOCK) ON TBL_TRN_SLSC01_HDR.SHIPTO = TBL_MST_CUSTOMERLOCATION.CLID 
      LEFT OUTER JOIN TBL_MST_ITEMGROUP (NOLOCK) ON TBL_MST_ITEM.ITEMGID_REF = TBL_MST_ITEMGROUP.ITEMGID  
      WHERE (TBL_TRN_SLSC01_HDR.STATUS ='$this->STATUS') 
      AND (TBL_TRN_SLSC01_HDR.CYID_REF = $this->CYID) 
      AND (TBL_TRN_SLSC01_HDR.BRID_REF in( $BranchName)) 
      AND (TBL_TRN_SLSC01_HDR.SCDT BETWEEN '$this->From_Date' AND '$this->To_Date')
      AND (TBL_TRN_SLSC01_HDR.SLID_REF in ( $SGLID))
      AND (TBL_MST_ITEM.ITEMGID_REF in ( $ITEMGID))
      AND (TBL_TRN_SLSC01_MAT.ITEMID_REF in ($ITEMID))
	  GROUP BY TBL_TRN_SLSC01_HDR.SCNO, 
      TBL_TRN_SLSC01_HDR.SCDT,
      BG.BG_DESC,
      BR.BRNAME,
      C.CCODE, 
      C.NAME, 
      C.SAP_CUSTOMER_CODE,
      C.SAP_CUSTOMER_NAME,    
	  CL.NAME,
      TBL_TRN_SLSC01_MAT.CHALLAN_MAINQTY,
	  TBL_TRN_SLSC01_HDR.STATUS
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
            'Customer Code',
            'Customer Name',
            'SAP Customer Code',
            'SAP Customer Name',
            'Shipping Address',          
            'Actual Qty',
            'Status',
        ];
    }
}





