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
use App\Exports\SerialWiseStock;
use Maatwebsite\Excel\Facades\Excel;














class SerialWiseStock implements FromCollection, WithHeadings
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
		/* $BranchName = "'".$BranchName."'";
		$ITEMID = "'".$ITEMID."'"; */
		$ITEMID=implode(",",$this->ITEMID);
        $BranchName=implode(",",$this->BranchName);
       
       
       $sp_popup = [
            $this->From_Date,$this->To_Date,$this->CYID, $BranchName,$ITEMID
        ];



      return collect( $data=DB::select('EXEC  SP_TRN_SERIALWISE_STOCK_RPT ?,?,?,?,?', $sp_popup));



      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
          'Item Code',
          'Item Name',
		      'Size',
	        'GSM',
			'Color',
			'Thickness',
			'Packaging',
	        'In Qty',
			'In Qty in NOS',
			'Out Qty in NOS',
	        'Out Qty',
	        'Reel Weight',
          'Balance Reel / Bundel'
          ];
    }
}





