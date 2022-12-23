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
use App\Exports\NRGPSummary;
use Maatwebsite\Excel\Facades\Excel;














class NRGPSummary implements FromCollection, WithHeadings
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
       



      return collect( $data=DB::select("SELECT 
      H.NRGP_NO,
      H.NRGP_DT,
      BG.BG_DESC AS BRANCH_GROUP,
      BR.BRNAME,
      V.VCODE,
      V.NAME AS VENDOR,
      V.SAP_VENDOR_CODE,
      SAP_VENDOR_NAME1,
	  LADD,     
      SUM(M.NRGP_QTY),
      
              CASE
                WHEN H.STATUS ='A' THEN 'Approved'
                WHEN H.STATUS = 'N' THEN 'Not Approved'
                WHEN H.STATUS = 'c' THEN 'Cancelled'
                WHEN H.STATUS = 'R' THEN 'Closed'                      
                END AS STATUS
     
      FROM TBL_TRN_NRGP01_MAT M (NOLOCK) LEFT JOIN TBL_TRN_NRGP01_HDR H (NOLOCK) ON H.NRGPID=M.NRGPID_REF
      LEFT OUTER JOIN TBL_MST_SUBLEDGER ON H.VID_REF = TBL_MST_SUBLEDGER.SGLID 
      LEFT OUTER JOIN TBL_MST_VENDOR AS V ON TBL_MST_SUBLEDGER.SGLID = V.SLID_REF 
      LEFT JOIN (SELECT DISTINCT VID_REF,LADD FROM TBL_MST_VENDORLOCATION WHERE DEFAULT_BILLING=1) AS A ON A.VID_REF=V.VID
      LEFT JOIN TBL_MST_ITEM AS I ON M.ITEMID_REF=I.ITEMID
      LEFT JOIN TBL_MST_ITEMGROUP AS G (NOLOCK) ON I.ITEMGID_REF = G.ITEMGID 
      LEFT JOIN TBL_MST_BRANCH AS BR WITH (NOLOCK) ON H.BRID_REF=BR.BRID
      LEFT JOIN TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID 
      LEFT JOIN TBL_MST_UOM AS U ON M.MAIN_UOMID_REF=U.UOMID
      LEFT jOIN TBL_MST_BUSINESSUNIT AS B ON B.BUID=I.BUID_REF
      
      WHERE H.CYID_REF=$this->CYID
      AND H.STATUS='$this->STATUS'
      AND H.BRID_REF IN ($BranchName) 
      AND M.ITEMID_REF IN ($ITEMID)  
      AND (I.ITEMGID_REF in ( $ITEMGID)) 
      AND (H.NRGP_DT BETWEEN '$this->From_Date' AND '$this->To_Date')
      AND V.SLID_REF IN ($SGLID)

	  GROUP BY 
	  H.NRGP_NO,
      H.NRGP_DT,
      BG.BG_DESC,
      BR.BRNAME,
      V.VCODE,
      V.NAME,
      V.SAP_VENDOR_CODE,
      SAP_VENDOR_NAME1,
	  LADD,
	  H.STATUS"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
            'NRGP No',
            'NRGP Date',
            'Branch Group',
            'Branch Name',
            'Vendor Code',
            'Vendor Name',
            'SAP Vendor Code',
            'SAP Vendor Name',
            'Billing Address',
            'NRGP Qty',
            'Status',				
        ];
    }
}





