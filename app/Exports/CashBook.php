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
use App\Exports\CashBook;
use Maatwebsite\Excel\Facades\Excel;














class CashBook implements FromCollection, WithHeadings
{


 function __construct($BANKID,$From_Date,$To_Date,$BranchGroup,$BranchName,$CYID) {

        $this->BANKID = $BANKID;
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
        
    
        $BANKID=implode(",",$this->BANKID);
        $BranchName=implode(",",$this->BranchName);      

       



      return collect( $data=DB::select("	SELECT 
      G.GLCODE,
      G.GLNAME,
      BG.BG_DESC AS BRANCH_GROUP,	    
      BR.BRNAME, 
      H.SOURCE_DOCDT,
      H.NARRATION,


      SUM(ISNULL(A.DR_AMT, 0)) AS DR_AMT, 
      SUM(ISNULL(A.CR_AMT, 0)) AS CR_AMT, 
      IIF((DBO.FN_GODBL(G.GLID, '$this->From_Date') - DBO.FN_GOCBL(G.GLID, '$this->From_Date') + SUM(ISNULL(A.DR_AMT, 0)) - SUM(ISNULL(A.CR_AMT, 0))) > 0, (DBO.FN_GODBL(G.GLID, '$this->From_Date') - DBO.FN_GOCBL(G.GLID, '$this->From_Date') + SUM(ISNULL(A.DR_AMT, 0))- SUM(ISNULL(A.CR_AMT, 0))), 0) AS CLOSING_DR, 
      IIF((DBO.FN_GODBL(G.GLID, '$this->From_Date') - DBO.FN_GOCBL(G.GLID, '$this->From_Date') + SUM(ISNULL(A.DR_AMT, 0)) - SUM(ISNULL(A.CR_AMT, 0))) < 0, 
      ABS((DBO.FN_GODBL(G.GLID, '$this->From_Date') - DBO.FN_GOCBL(G.GLID, '$this->From_Date') + SUM(ISNULL(A.DR_AMT, 0)) - SUM(ISNULL(A.CR_AMT, 0)))), 0) AS CLOSING_CR
      
      FROM 
      TBL_TRN_FJRV01_ACC A LEFT JOIN
      TBL_TRN_FJRV01_HDR H ON A.JVID_REF = H.JVID JOIN
      TBL_MST_BRANCH AS BR WITH (NOLOCK) ON H.BRID_REF=BR.BRID LEFT JOIN
      TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT JOIN 
      TBL_MST_GENERALLEDGER G ON A.GLID_REF = G.GLID LEFT JOIN 
      TBL_MST_BANK B ON B.GLID_REF=G.GLID
    
      WHERE     
      A.SGLID_REF = 'G' AND H.SOURCE_DOCTYPE IN ('RECEIPT', 'PAYMENT') AND H.JV_DT BETWEEN '$this->From_Date' AND '$this->To_Date' AND B.BANK_CASH='C' and H.CYID_REF=$this->CYID and H.BRID_REF IN ($BranchName) AND B.BID IN ($BANKID)
      
      GROUP BY 
      B.BID,G.GLCODE, G.GLNAME,B.NAME,H.SOURCE_DOCTYPE, H.SOURCE_DOCDT, H.SOURCE_DOCNO, G.GLID, H.NARRATION,BR.BRNAME,BG.BG_DESC
    "));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
    
        return [
          ' Voucher Type',
          ' Voucher No',
          ' Branch Group',
          ' Branch Name',
          ' Voucher Date',
          ' Narration ',
          ' DR_AMT',
          ' CR_AMT',
          ' CLOSING_DR',
          ' CLOSING_CR',
          
        ];
         
    }
}





