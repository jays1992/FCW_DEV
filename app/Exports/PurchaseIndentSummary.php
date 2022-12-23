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
use App\Exports\PurchaseIndentSummary;
use Maatwebsite\Excel\Facades\Excel;














class PurchaseIndentSummary implements FromCollection, WithHeadings
{


 function __construct($From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMGID,$ITEMID,$STATUS,$CYID_REF,$DEPID,$STID) {
        $this->ITEMID = $ITEMID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->ITEMGID = $ITEMGID;
        $this->STATUS = $STATUS;
        $this->CYID = $CYID_REF;
        $this->DEPID = $DEPID;
        $this->STID = $STID;
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
        $DEPID=implode(",",$this->DEPID);
        $STID=implode(",",$this->STID);
       



      return collect( $data=DB::select("SELECT
      H.PI_NO,
      H.PI_DT,
      BG.BG_DESC AS BRANCH_GROUP,
      BR.BRNAME,
      S.STCODE,
      S.NAME AS STORE,
      D.DCODE,
      D.NAME AS DEPARTMENT,
      SUM(M.INDENT_QTY),
      CASE
      WHEN H.STATUS ='A' THEN 'Approved'
      WHEN H.STATUS = 'N' THEN 'Not Approved'
      WHEN H.STATUS = 'c' THEN 'Cancelled'
      WHEN H.STATUS = 'R' THEN 'Closed'			    
      END AS STATUS 
      
      
      FROM TBL_TRN_PRIN02_MAT M (NOLOCK)
      LEFT JOIN TBL_MST_ITEM I (NOLOCK)  ON M.ITEMID_REF=I.ITEMID
      LEFT JOIN TBL_MST_ITEMGROUP G (NOLOCK)  ON I.ITEMGID_REF=G.ITEMGID
      LEFT JOIN TBL_TRN_PRIN02_HDR H (NOLOCK) ON M.PIID_REF=H.PIID
      LEFT JOIN TBL_MST_STORE S (NOLOCK) ON S.STID=H.STID_REF
      LEFT JOIN TBL_MST_DEPARTMENT D (NOLOCK) ON D.DEPID=H.DEPID_REF
      LEFT JOIN TBL_MST_BUSINESSUNIT B (NOLOCK) ON I.BUID_REF=B.BUID
      LEFT JOIN TBL_MST_UOM U (NOLOCK) ON U.UOMID=M.UOMID_REF
      LEFT OUTER JOIN TBL_MST_BRANCH AS BR WITH (NOLOCK) ON H.BRID_REF=BR.BRID 
      LEFT OUTER JOIN TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID
      
      WHERE H.STID_REF IN ($STID) AND H.DEPID_REF IN ($DEPID)
      AND H.STATUS='$this->STATUS' AND H.CYID_REF=$this->CYID and H.BRID_REF in ($BranchName)
      AND (H.PI_DT BETWEEN '$this->From_Date' AND '$this->To_Date') AND M.ITEMID_REF IN($ITEMID)
      AND G.ITEMGID IN ($ITEMGID)
      GROUP BY 
        H.PI_NO,
        H.PI_DT,
        BG.BG_DESC,
        BR.BRNAME,
        S.STCODE,
        S.NAME,
        D.DCODE,
        D.NAME,
        H.STATUS     
      
      "));


      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
            'Purchase Indent No',
            'Purchase Indent Date',
            'Branch Group',
            'Branch Name',
            'Store Code',
            'Store Name',
            'Department Code',
            'Department Name',  
			'Indent Qty',
		    'Status',				
        ];
    }
}





