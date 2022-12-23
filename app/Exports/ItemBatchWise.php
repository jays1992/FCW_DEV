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
use App\Exports\ItemBatchWise;
use Maatwebsite\Excel\Facades\Excel;














class ItemBatchWise implements FromCollection, WithHeadings
{


 function __construct($BranchGroup,$BranchName,$ITEMGID,$ITEMID,$CYID_REF) {
        $this->ITEMID = $ITEMID;     
        $this->BranchName = $BranchName;
        $this->ITEMGID = $ITEMGID;  
        $this->CYID_REF = $CYID_REF;
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
       



      return collect( $data=DB::select("      SELECT
      BG.BG_DESC AS BRANCH_GROUP,
      BR.BRNAME,
      TBL_MST_ITEM.NAME AS ITEM_NAME,				
      TBL_MST_ITEM.ALPS_PART_NO,
      TBL_MST_ITEM.CUSTOMER_PART_NO,
      TBL_MST_ITEM.OEM_PART_NO,
      TBL_MST_BATCH.BATCH_CODE,
      TBL_MST_STORE.NAME AS Store_Name,
      TBL_MST_BATCH.OPENING_QTY, 
      TBL_MST_BATCH.IN_QTY, 
      TBL_MST_BATCH.OUT_QTY, 
      TBL_MST_BATCH.CURRENT_QTY


      FROM TBL_MST_BATCH LEFT OUTER JOIN
           TBL_MST_ITEM ON TBL_MST_BATCH.ITEMID_REF = TBL_MST_ITEM.ITEMID LEFT OUTER JOIN
           TBL_MST_BRANCH AS BR WITH (NOLOCK) ON TBL_MST_BATCH.BRID_REF=BR.BRID LEFT OUTER JOIN
           TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT OUTER JOIN
           TBL_MST_STORE ON TBL_MST_BATCH.STID_REF = TBL_MST_STORE.STID				


      WHERE TBL_MST_ITEM.ITEMID IN ($ITEMID) AND 
            TBL_MST_ITEM.ITEMGID_REF IN ($ITEMGID) AND 
            TBL_MST_BATCH.BRID_REF IN ($BranchName) AND 
            TBL_MST_BATCH.CYID_REF=$this->CYID_REF
"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
            'BRANCH GROUP',
            'BRNAME',
			' Item Name',
			'ALPS Part No',
			'ALPS Customer Part No',
			'ALPS OEM Part No',
			' Batch Code',
			'Store Name',
			'Opening Qty',
			'In Quantity',
			'Out Quantiry',
			'Available Stock',	
			
        ];
    }
}





