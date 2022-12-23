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
use App\Exports\RFQSummary;
use Maatwebsite\Excel\Facades\Excel;














class RFQSummary implements FromCollection, WithHeadings
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

       



      return collect( $data=DB::select("SELECT
      H.RFQ_NO,
      H.RFQ_DT, 
      BG.BG_DESC AS BRANCH_GROUP,
      BR.BRNAME,
      V.VCODE AS VENDOR_CODE,
      V.NAME AS VENDOR_NAME,
      V.SAP_VENDOR_CODE,
      V.SAP_VENDOR_NAME1 AS SAP_VENDOR_NAME,     
      SUM(M.RFQ_QTY),      
      CASE
          WHEN H.STATUS ='A' THEN 'Approved'
          WHEN H.STATUS = 'N' THEN 'Not Approved'
          WHEN H.STATUS = 'c' THEN 'Cancelled'
          WHEN H.STATUS = 'R' THEN 'Closed'
                      
          END AS STATUS
      
      FROM
      TBL_TRN_RQFQ01_MAT AS M WITH (NOLOCK) LEFT OUTER JOIN
      TBL_TRN_RQFQ01_HDR AS H WITH (NOLOCK) ON H.RFQID = M.RFQID_REF LEFT OUTER JOIN                         
      TBL_MST_SUBLEDGER AS S WITH (NOLOCK) ON H.VID_REF = S.SGLID LEFT OUTER JOIN
      TBL_MST_VENDOR AS V WITH (NOLOCK) ON V.SLID_REF = S.SGLID LEFT OUTER JOIN
      TBL_MST_ITEM AS I WITH (NOLOCK) ON M.ITEMID_REF = I.ITEMID LEFT OUTER JOIN
      TBL_MST_BUSINESSUNIT AS B WITH (NOLOCK) ON I.BUID_REF = B.BUID LEFT OUTER JOIN
      TBL_MST_UOM AS MU WITH (NOLOCK) ON M.UOMID_REF = MU.UOMID LEFT OUTER JOIN
      TBL_MST_BRANCH AS BR WITH (NOLOCK) ON H.BRID_REF=BR.BRID LEFT OUTER JOIN
      TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT OUTER JOIN
      TBL_MST_ITEMGROUP AS G WITH (NOLOCK) ON I.ITEMGID_REF = G.ITEMGID   

      WHERE
      H.STATUS='$this->STATUS' 
      AND H.CYID_REF = $this->CYID
      AND H.BRID_REF IN ($BranchName) 
      AND (H.RFQ_DT BETWEEN '$this->From_Date' AND '$this->To_Date')
      AND V.SLID_REF IN ($SGLID) 
      AND I.ITEMGID_REF IN ($ITEMGID)
      AND M.ITEMID_REF IN ($ITEMID)

	  GROUP BY 
	  H.RFQ_NO,
      H.RFQ_DT, 
      BG.BG_DESC,
      BR.BRNAME,
      V.VCODE,
      V.NAME,
      V.SAP_VENDOR_CODE,
      V.SAP_VENDOR_NAME1,
      H.STATUS"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
            'RFQ No',
            'RFQ Date',
            'Branch Group',
            'Branch Name',
            'Vendor Code',
            'Vendor Name',
            'SAP Vendor Code',
            'SAP Vendor Name',
          	'RFQ Qty',			
			'Status',		
			
        ];
    }
}





