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
use App\Exports\NonMovingReportQty;
use Maatwebsite\Excel\Facades\Excel;














class NonMovingReportQty implements FromCollection, WithHeadings
{

 function __construct($ITEMGROUP,$ITEMSUBGROUP,$ITEMCATEGORY,$STORE,$From_Date,$To_Date,$BranchGroup,$BranchName,$CYID_REF) {
        $this->ITEMGROUP = $ITEMGROUP;
        $this->ITEMSUBGROUP = $ITEMSUBGROUP;
        $this->ITEMCATEGORY = $ITEMCATEGORY;
        $this->STORE = $STORE;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;   
        $this->CYID_REF = $CYID_REF;

        
       
 }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        //dd($this->From_Date); 
        
        $ITEMGROUP=implode(",",$this->ITEMGROUP);
        $ITEMSUBGROUP=implode(",",$this->ITEMSUBGROUP);
        $ITEMCATEGORY=implode(",",$this->ITEMCATEGORY);
        $STORE=implode(",",$this->STORE);
        $BranchName=implode(",",$this->BranchName);

       
      return collect( $data=DB::select("SELECT 
      I.ICODE,I.NAME,I.ALPS_PART_NO,
      I.CUSTOMER_PART_NO,I.OEM_PART_NO,IC.DESCRIPTIONS AS ITEM_CATEGORY,IG.GROUPNAME AS ITEM_GROUP,ISG.DESCRIPTIONS AS ITEM_SUBGROUP,S.NAME AS STORE_NAME,
      SUM(B.CURRENT_QTY) AS CURRENT_STOCK 
      FROM TBL_MST_BATCH B
      LEFT JOIN TBL_MST_STORE S ON S.STID=B.STID_REF
      LEFT JOIN TBL_MST_ITEM I ON I.ITEMID=B.ITEMID_REF
      LEFT JOIN TBL_TRN_SLSC01_MAT M ON M.ITEMID_REF=B.ITEMID_REF
      LEFT JOIN TBL_TRN_SLSC01_HDR H ON H.SCID=M.SCID_REF
      LEFT JOIN TBL_MST_ITEMCATEGORY IC ON IC.ICID=I.ICID_REF
      LEFT JOIN TBL_MST_ITEMGROUP IG ON IG.ITEMGID=I.ITEMGID_REF
      LEFT JOIN TBL_MST_ITEMSUBGROUP ISG ON ISG.ITEMGID_REF=I.ITEMGID_REF AND I.ISGID_REF = ISG.ISGID
      WHERE B.CYID_REF=$this->CYID_REF AND B.BRID_REF IN ($BranchName) AND 
      B.ITEMID_REF 
      NOT IN (SELECT DISTINCT M.ITEMID_REF FROM TBL_TRN_SLSC01_MAT M 
      LEFT JOIN TBL_TRN_SLSC01_HDR H ON H.SCID=M.SCID_REF WHERE H.STATUS='A' AND H.CYID_REF=$this->CYID_REF AND H.BRID_REF IN ($BranchName)
      AND H.SCDT BETWEEN '$this->From_Date' AND '$this->To_Date'
      ) AND B.STID_REF IN ($STORE) AND I.ITEMGID_REF IN ($ITEMGROUP) AND I.ICID_REF IN ($ITEMCATEGORY) AND ISG.ISGID IN ($ITEMSUBGROUP)
      GROUP BY I.ICODE,I.NAME,I.SAP_MARKET_SETCODE,I.ROUNDING_VALUE,I.ALPS_PART_NO,
      I.CUSTOMER_PART_NO,I.OEM_PART_NO,S.NAME ,IC.DESCRIPTIONS,IG.GROUPNAME ,ISG.DESCRIPTIONS")
  
    );

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
            'Item Code',
            'Item Name',
            isset(Session::get('report_dynamic_cols')->FIELD8) ? Session::get('report_dynamic_cols')->FIELD8 :"" ,
            isset(Session::get('report_dynamic_cols')->FIELD9) ? Session::get('report_dynamic_cols')->FIELD9 :"",
            isset(Session::get('report_dynamic_cols')->FIELD10) ? Session::get('report_dynamic_cols')->FIELD10 :"",
            'Item Group',
            'Item Sub Group',
            'Reorder Level Qty',
            'Store',
            'Available Stock Qty'                  
        ];
    }
}




