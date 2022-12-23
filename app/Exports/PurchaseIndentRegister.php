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
use App\Exports\PurchaseIndentRegister;
use Maatwebsite\Excel\Facades\Excel;














class PurchaseIndentRegister implements FromCollection, WithHeadings
{


 function __construct($From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMGID,$ITEMID,$STATUS,$CYID_REF,$PI_NO) {
        $this->ITEMID = $ITEMID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->ITEMGID = $ITEMGID;
        $this->STATUS = $STATUS;
        $this->CYID = $CYID_REF;
        $this->PI_NO = $PI_NO;
      
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
        $PI_NO=implode(",",$this->PI_NO);
 
       



      return collect( $data=DB::select("SELECT TBL_TRN_PRIN02_HDR.PI_NO,    
      TBL_TRN_PRIN02_HDR.PI_DT,  
      BG.BG_DESC AS BRANCH_GROUP,
      BR.BRNAME,
      TBL_TRN_MRQS01_HDR.MRS_NO,
      TBL_MST_ITEMGROUP.GROUPNAME,
      TBL_MST_ITEM.ICODE,
      TBL_MST_ITEM.NAME,
      B.BUNAME,
      TBL_MST_ITEM.ALPS_PART_NO,
      TBL_MST_ITEM.CUSTOMER_PART_NO,
      TBL_MST_ITEM.OEM_PART_NO,
      TBL_MST_UOM.DESCRIPTIONS AS UOM, 
      TBL_TRN_PRIN02_MAT.PENDING_QTY,
      TBL_TRN_PRIN02_MAT.INDENT_QTY-TBL_TRN_PRIN02_MAT.PENDING_QTY AS CONSUMED_QTY,
      TBL_TRN_PRIN02_MAT.INDENT_QTY,
      TBL_MST_DEPARTMENT.NAME AS DEPARTMENT_NAME,
      TBL_TRN_PRIN02_MAT.EDA,
       TBL_TRN_PRIN02_MAT.REMARKS, 
      
       CASE
      WHEN TBL_TRN_PRIN02_HDR.STATUS ='A' THEN 'Approved'
      WHEN TBL_TRN_PRIN02_HDR.STATUS = 'N' THEN 'Not Approved'
      WHEN TBL_TRN_PRIN02_HDR.STATUS = 'c' THEN 'Cancelled'
      WHEN TBL_TRN_PRIN02_HDR.STATUS = 'R' THEN 'Closed'		    
      END AS STATUS 
      
      FROM                     TBL_TRN_PRIN02_HDR LEFT OUTER JOIN
                               TBL_TRN_PRIN02_MAT ON TBL_TRN_PRIN02_HDR.PIID = TBL_TRN_PRIN02_MAT.PIID_REF LEFT OUTER JOIN
                               TBL_MST_ITEM ON TBL_TRN_PRIN02_MAT.ITEMID_REF = TBL_MST_ITEM.ITEMID LEFT OUTER JOIN
                               TBL_MST_BUSINESSUNIT B (NOLOCK) ON TBL_MST_ITEM.BUID_REF=B.BUID LEFT JOIN 
                               TBL_TRN_MRQS01_HDR ON TBL_TRN_PRIN02_MAT.MRSNO = TBL_TRN_MRQS01_HDR.MRSID LEFT OUTER JOIN
                               TBL_MST_UOM ON TBL_MST_ITEM.MAIN_UOMID_REF = TBL_MST_UOM.UOMID LEFT OUTER JOIN
                               TBL_MST_ITEMGROUP ON TBL_MST_ITEM.ITEMGID_REF = TBL_MST_ITEMGROUP.ITEMGID LEFT OUTER JOIN
                               TBL_MST_DEPARTMENT ON TBL_TRN_PRIN02_HDR.DEPID_REF = TBL_MST_DEPARTMENT.DEPID LEFT OUTER JOIN
                               TBL_MST_BRANCH AS BR WITH (NOLOCK) ON TBL_TRN_PRIN02_HDR.BRID_REF=BR.BRID LEFT OUTER JOIN
                               TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT OUTER JOIN
                               TBL_MST_COMPANY ON TBL_TRN_PRIN02_HDR.CYID_REF = TBL_MST_COMPANY.CYID
      WHERE					 TBL_TRN_PRIN02_HDR.STATUS='$this->STATUS' 
                               AND TBL_TRN_PRIN02_HDR.PIID IN ($PI_NO)                               
                               AND TBL_TRN_PRIN02_HDR.CYID_REF =$this->CYID
                               AND TBL_TRN_PRIN02_HDR.BRID_REF IN ($BranchName)
                               AND (TBL_TRN_PRIN02_HDR.PI_DT BETWEEN '$this->From_Date' AND '$this->To_Date')
                               AND TBL_MST_ITEM.ITEMID IN ($ITEMID) 
                               AND TBL_MST_ITEM.ITEMGID_REF IN ($ITEMGID) 
                               "));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
            'PI No',
            'PI Date',
            'Branch Group',
            'Branch Name',
            'MRS No',		
			'Item Group Name',
			'Item Code',
			'Item Name',
	        'Business Unit',
			'ALPS Part No',
			'Customer Part No',
			'OEM Part No',	
			'UOM',
			'Pending Qty',
			'Consumed Qty',
			'Actual Qty',
		    'Department',
			'EDA',
			'Remarks',			
			'Status',
			
        ];
    }
}





