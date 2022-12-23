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
use App\Exports\GateEntry;
use Maatwebsite\Excel\Facades\Excel;














class GateEntry implements FromCollection, WithHeadings
{


 function __construct($SGLID,$From_Date,$To_Date,$BranchGroup,$BranchName,$GETGYPE,$STATUS,$CYID) {
        $this->GETGYPE = $GETGYPE;
        $this->SGLID = $SGLID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->STATUS = $STATUS;
        $this->CYID = $CYID;
 }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
      $count=count($this->GETGYPE); 

      $row4 = '';
      if(!empty($this->GETGYPE)){
          foreach ($this->GETGYPE as $key=>$cRow)
          {          
              if($key==$count-1){
                $sep=''; 
              }else{
                $sep=','; 
              }           

                  $row3 = "'";

                  $row3 = $row3.$cRow.$row3.$sep;
                  $row4 =  $row4 .$row3;
             


          }}   

        //dd($this->From_Date); 
        
        $GETGYPE=implode(",",$this->GETGYPE);
        $SGLID=implode(",",$this->SGLID);
        $BranchName=implode(",",$this->BranchName);   
        
        
        //dd($GETGYPE); 




				
      

      return collect( $data=DB::select(" SELECT     
      TBL_TRN_IMGE01_HDR.GE_NO, 
      TBL_TRN_IMGE01_HDR.GE_DT,
      BG.BG_DESC AS BRANCH_GROUP,
      BR.BRNAME,
      V.VCODE, 
      V.NAME AS Vendor_Name,
      V.SAP_VENDOR_CODE,
      V.SAP_VENDOR_NAME1,
      TBL_MST_STATE.NAME AS State_Name, 
      TBL_MST_CITY.NAME AS City_Name,
       dbo.FN_DOCNO_USING_DOCTYPE(TBL_TRN_IMGE01_HDR.PO_NO, 
                       TBL_TRN_IMGE01_HDR.GETYPE) AS PO_NO,
               TBL_TRN_IMGE01_HDR.VENDOR_BILLNO,
               TBL_TRN_IMGE01_HDR.VENDOR_BILLDT,
                TBL_TRN_IMGE01_HDR.VENDOR_CHNO,
                TBL_TRN_IMGE01_HDR.VENDOR_CHDT,
                TBL_TRN_IMGE01_VCL.GROSS_WEIGHT, 
                       TBL_TRN_IMGE01_VCL.TARE_WEIGHT, 
               TBL_TRN_IMGE01_VCL.NET_WEIGHT,
                TBL_TRN_IMGE01_HDR.SECURITY_GUARD,  
                TBL_TRN_IMGE01_HDR.REMARKS,
      CASE
          WHEN TBL_TRN_IMGE01_HDR.STATUS ='A' THEN 'Approved'
          WHEN TBL_TRN_IMGE01_HDR.STATUS = 'N' THEN 'Not Approved'
          WHEN TBL_TRN_IMGE01_HDR.STATUS = 'c' THEN 'Cancelled'
          WHEN TBL_TRN_IMGE01_HDR.STATUS = 'R' THEN 'Closed'
                      
          END AS STATUS
          
FROM              TBL_TRN_IMGE01_HDR LEFT OUTER JOIN
      TBL_MST_COMPANY ON TBL_TRN_IMGE01_HDR.CYID_REF = TBL_MST_COMPANY.CYID LEFT OUTER JOIN
TBL_MST_SUBLEDGER AS S (NOLOCK) ON TBL_TRN_IMGE01_HDR.VID_REF = S.SGLID  LEFT OUTER JOIN  
TBL_MST_VENDOR AS V ON S.SGLID = V.SLID_REF LEFT OUTER JOIN
      TBL_MST_VENDOR ON TBL_TRN_IMGE01_HDR.VID_REF = TBL_MST_VENDOR.SLID_REF LEFT OUTER JOIN
      TBL_MST_STATE ON TBL_MST_VENDOR.REGSTID_REF = TBL_MST_STATE.STID LEFT OUTER JOIN
      TBL_MST_CITY ON TBL_MST_VENDOR.REGCITYID_REF = TBL_MST_CITY.CITYID LEFT OUTER JOIN
TBL_MST_BRANCH AS BR WITH (NOLOCK) ON TBL_TRN_IMGE01_HDR.BRID_REF=BR.BRID LEFT OUTER JOIN
      TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT OUTER JOIN
      TBL_TRN_IMGE01_VCL ON TBL_TRN_IMGE01_HDR.GEID = TBL_TRN_IMGE01_VCL.GEID_REF
     WHERE             TBL_TRN_IMGE01_HDR.STATUS='$this->STATUS'
             AND TBL_TRN_IMGE01_HDR.CYID_REF=$this->CYID
             AND TBL_TRN_IMGE01_HDR.BRID_REF IN ($BranchName)
             AND (TBL_TRN_IMGE01_HDR.GE_DT BETWEEN '$this->From_Date' AND '$this->To_Date')
             AND TBL_TRN_IMGE01_HDR.VID_REF IN ($SGLID) 
             AND TBL_TRN_IMGE01_HDR.GETYPE IN ($row4)"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
          'GE No',
          'GE Date',
          'Branch Group',
          'Branch Name',
          'Vendor Code',
          'Vendor Name',
          'SAP Vendor Code',
          'SAP Vendor Name',
          'State Name',
          'City Name',
          'PO No',    
          'Vendor Bill No',
          'Vendor Bill Date',
          'Vendor Challan No',
          'Vendor Challan Date',
          'Gross Weight',
          'Tare Weight',
          'Net Weight',
          'Security Guard',
          'Remarks',
          'Status'				
      ];
    }
}





