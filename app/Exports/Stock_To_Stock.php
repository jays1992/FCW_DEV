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
use App\Exports\Stock_To_Stock;
use Maatwebsite\Excel\Facades\Excel;














class Stock_To_Stock implements FromCollection, WithHeadings
{


 function __construct($From_Date,$To_Date,$BranchName,$ITEMID,$CYID) {
        $this->ITEMID = $ITEMID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->CYID = $CYID;
 }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        //dd($this->From_Date); 
        
        $ITEMID=implode(",",$this->ITEMID);
        $BranchName=implode(",",$this->BranchName);      
       
       
       



      return collect( $data=DB::select("SELECT        TBL_TRN_STOCK_STOCK_HDR.ST_ST_DOCNO, TBL_TRN_STOCK_STOCK_HDR.ST_ST_DOCDT, TBL_TRN_STOCK_STOCK_HDR.ST_ST_TYPE, TBL_MST_ITEM.ICODE, TBL_MST_ITEM.NAME, 
			  TBL_MST_ITEM.ALPS_PART_NO,TBL_MST_ITEM.CUSTOMER_PART_NO,TBL_MST_ITEM.OEM_PART_NO,TBL_MST_BUSINESSUNIT.BUNAME,TBL_TRN_STOCK_STOCK_STORE.STOCK_TYPE,
			  TBL_MST_STORE.NAME AS Store_Name, TBL_MST_UOM.UOMCODE+'-'+TBL_MST_UOM.DESCRIPTIONS AS UOM, 
			  CASE WHEN TBL_TRN_STOCK_STOCK_STORE.STOCK_TYPE = 'IN' THEN TBL_TRN_STOCK_STOCK_MAT.QTY_IN ELSE  TBL_TRN_STOCK_STOCK_MAT.QTY END AS Quantity,
			  CASE WHEN TBL_TRN_STOCK_STOCK_STORE.STOCK_TYPE = 'IN' THEN TBL_TRN_STOCK_STOCK_MAT.RATE_IN ELSE TBL_TRN_STOCK_STOCK_MAT.RATE END AS TRANSFERRATE,
			  CASE WHEN TBL_TRN_STOCK_STOCK_STORE.STOCK_TYPE = 'IN' THEN  CAST(TBL_TRN_STOCK_STOCK_MAT.RATE_IN * TBL_TRN_STOCK_STOCK_MAT.QTY_IN AS NUMERIC(14,2)) 
			  ELSE CAST(TBL_TRN_STOCK_STOCK_MAT.RATE * TBL_TRN_STOCK_STOCK_MAT.QTY AS NUMERIC(14,2)) END AS AMT          
			  FROM            TBL_TRN_STOCK_STOCK_HDR LEFT OUTER JOIN
                         TBL_TRN_STOCK_STOCK_MAT ON TBL_TRN_STOCK_STOCK_HDR.ST_STID = TBL_TRN_STOCK_STOCK_MAT.ST_STID_REF LEFT OUTER JOIN
                         TBL_MST_ITEM ON TBL_TRN_STOCK_STOCK_MAT.ITEMID_REF = TBL_MST_ITEM.ITEMID LEFT OUTER JOIN
						 TBL_MST_UOM ON TBL_TRN_STOCK_STOCK_MAT.UOMID_REF = TBL_MST_UOM.UOMID LEFT OUTER JOIN
						 TBL_MST_BUSINESSUNIT ON TBL_MST_ITEM.BUID_REF = TBL_MST_BUSINESSUNIT.BUID LEFT OUTER JOIN
                         TBL_MST_COMPANY ON TBL_TRN_STOCK_STOCK_HDR.CYID_REF = TBL_MST_COMPANY.CYID LEFT OUTER JOIN
                         TBL_TRN_STOCK_STOCK_STORE ON TBL_TRN_STOCK_STOCK_MAT.ST_STID_REF = TBL_TRN_STOCK_STOCK_STORE.ST_STID_REF LEFT OUTER JOIN
                         TBL_MST_STORE ON TBL_TRN_STOCK_STOCK_STORE.STID_REF = TBL_MST_STORE.STID
WHERE        (TBL_TRN_STOCK_STOCK_HDR.CYID_REF = $this->CYID) AND (TBL_TRN_STOCK_STOCK_HDR.BRID_REF IN ($BranchName)) 
AND (TBL_TRN_STOCK_STOCK_HDR.STATUS = 'A') AND (TBL_TRN_STOCK_STOCK_MAT.ITEMID_REF IN ($ITEMID))
AND ( TBL_TRN_STOCK_STOCK_HDR.ST_ST_DOCDT BETWEEN '$this->From_Date' AND '$this->To_Date' )"));



      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
          'Stock To Stock Transfer No',
          'Stock To Stock Transfer Date',
          'Transfer Type',
          'Item Code',
          'Item Name',
		  'Alps Part No',
	   'Customer Part No',
	   'OEM Part No',
	   'Business Unit',
	   'Stock Type',
	   'Store Name',
           'UOM',
           'Quantity',
		   'Transfer Rate',
		   'Amount'
          ];
    }
}





