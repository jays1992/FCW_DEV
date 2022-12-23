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
use App\Exports\StockOverHead;
use Maatwebsite\Excel\Facades\Excel;














class StockOverHead implements FromCollection, WithHeadings
{


 function __construct($From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMGID,$ITEMID,$CYID,$STOREID,$ICID,$ISGID) {
        $this->ITEMID = $ITEMID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->ITEMGID = $ITEMGID;
        $this->STOREID = $STOREID;
		$this->ICID = $ICID;
		$this->ISGID = $ISGID;
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
        $ITEMGID=implode(",",$this->ITEMGID);
        $STOREID=implode(",",$this->STOREID);
		$ICID=implode(",",$this->ICID);
        $ISGID=implode(",",$this->ISGID);
       
		$sp_popup = [
            $this->From_Date,$this->To_Date,$this->CYID, $BranchName,$ITEMID,$ITEMGID,$STOREID,$ISGID,$ICID
        ];


      return collect( $data=DB::select('EXEC  SP_TRN_GET_STOCK_OVERHEAD ?,?,?,?,?,?,?,?,?', $sp_popup));



      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
          'Store Name',
          'Item Name',
          'Item Code',
		  'Alps Part No',
           'Customer Part No',
           'OEM Part No',
		   'Business Unit',
		   'Sap Customer Name',
		   'Item ID',
		   'Store ID',
		   'HSN',
           'UOM',
           'Opening Qty',		   
           'IN Qty',
		   'Purchase Qty',
		   'Scrap In Qty',
		   'GRNRGP In Qty',
		   'RGP Out Qty',
		  'Outward Qty',
          'Sales Qty',
		  'Scrap Out Qty',
		  'Closing Qty',
		  'Opening Amount',
          'Purchase Amount',
		  'Scrap In Amount',
		  'Inward Amount',
		  'GRNRGP In Amount',
		  'Custom Duty',
		  'Clearing & Forwarding Duty',
		  'Freight & Transportation In Duty',
		  'Port Charges Duty',
		  'Other Duty',
		  'Outward Amount',
		  'RGP Out Amount',
		  'Sales Challan Amount',
		  'Scrap Out Amount',
		  'Sold Out Amount',
		  'Sold Out Custom Duty Amount',
		  'Sold Out Clearing & Forwarding Duty Amount',
		  'Sold Out Freight & Transportation IN Duty Amount',
		  'Sold Out Port Charges Duty Amount',
		  'Sold Out Other Duty Amount',
		  'Cost Value',
		  'Cost Per Unit',
		  'Sales Price'
          ];
    }
}





