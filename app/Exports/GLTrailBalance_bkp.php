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
use App\Exports\GLTrailBalance;
use Maatwebsite\Excel\Facades\Excel;














class GLTrailBalance implements FromCollection, WithHeadings
{


 function __construct($GLID,$AGID,$From_Date,$To_Date,$BranchName,$CYID_REF) {
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->CYID = $CYID_REF;
        $this->GLID = $GLID;
        $this->AGID = $AGID;
 
 }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        //dd($this->From_Date); 
             
        $BranchName=implode(",",$this->BranchName);          
        $GLID=implode(",",$this->GLID);
        $AGID=implode(",",$this->AGID);

      // dd($GLID);
		//dd($AGID);



      return collect( $data=DB::select("SELECT D.NOGNAME,D.AGNAME,D.ASGNAME,D.GLCODE AS GLCODE,D.GLNAME AS GLNAME,
      DBO.FN_GLODBL(D.GLID_REF,'$this->From_Date') AS OPENING_DR,
      DBO.FN_GLOCBL(D.GLID_REF,'$this->From_Date') AS OPENING_CR,
      SUM(D.DR_AMT) AS DR_AMT,SUM(D.CR_AMT) AS CR_AMT,
      SUM(SUM(ISNULL(D.DR_AMT,0))) OVER (PARTITION BY D.GLID_REF ORDER BY D.GLID_REF ROWS 500000 PRECEDING)+DBO.FN_GLODBL(D.GLID_REF,'$this->From_Date') AS DRCLOSING,
      SUM(SUM(ISNULL(D.CR_AMT,0))) OVER (PARTITION BY D.GLID_REF ORDER BY D.GLID_REF ROWS 500000 PRECEDING)+DBO.FN_GLOCBL(D.GLID_REF,'$this->From_Date') AS CRCLOSING
      FROM (
      select N.NOGNAME,AG.AGNAME,ASG.ASGNAME,
      G.GLCODE,G.GLNAME,
      IIF(A.SGLID_REF='S',DBO.FN_GLBYSL(A.GLID_REF),A.GLID_REF) AS GLID_REF,
      SUM(ISNULL(A.DR_AMT,0)) AS DR_AMT,
      SUM(ISNULL(A.CR_AMT,0)) AS CR_AMT
      from TBL_TRN_FJRV01_ACC A (NOLOCK)
      JOIN TBL_TRN_FJRV01_HDR H (NOLOCK) ON A.JVID_REF=H.JVID
      JOIN TBL_MST_GENERALLEDGER G (NOLOCK) ON IIF(A.SGLID_REF='S',DBO.FN_GLBYSL(A.GLID_REF),A.GLID_REF)=G.GLID
      left join TBL_MST_ACCOUNTSUBGROUP as ASG (NOLOCK) on ASG.ASGID=G.ASGID_REF
      left join TBL_MST_ACCOUNTGROUP as AG (NOLOCK) on AG.AGID=ASG.AGID_REF
      left join TBL_MST_NATUREOFGROUP as N (NOLOCK) ON N.NOGID=AG.NOGID_REF
      WHERE H.JV_DT BETWEEN '$this->From_Date' AND '$this->To_Date' AND H.CYID_REF=$this->CYID AND H.BRID_REF IN ($BranchName) AND H.STATUS = 'A' 
      AND G.GLID IN ($GLID) AND G.ASGID_REF IN ($AGID)
      GROUP BY G.GLCODE,G.GLNAME,A.GLID_REF,A.SGLID_REF,ASG.ASGNAME,AG.AGNAME,N.NOGNAME
      union
      select N.NOGNAME,AG.AGNAME,ASG.ASGNAME,
      G.GLCODE,G.GLNAME,
      G.GLID AS GLID_REF,
      0 AS DR_AMT,
      0 AS CR_AMT
      from TBL_MST_GENERALLEDGER G (NOLOCK) 
      left join TBL_MST_ACCOUNTSUBGROUP as ASG (NOLOCK) on ASG.ASGID=G.ASGID_REF
      left join TBL_MST_ACCOUNTGROUP as AG (NOLOCK) on AG.AGID=ASG.AGID_REF
      left join TBL_MST_NATUREOFGROUP as N (NOLOCK) ON N.NOGID=AG.NOGID_REF
      WHERE G.GLID not in (select IIF(A.SGLID_REF='S',DBO.FN_GLBYSL(A.GLID_REF),A.GLID_REF) from TBL_TRN_FJRV01_ACC A (NOLOCK)JOIN TBL_TRN_FJRV01_HDR H (NOLOCK) ON A.JVID_REF=H.JVID 
                        WHERE H.JV_DT BETWEEN '$this->From_Date' AND '$this->To_Date' AND H.CYID_REF=$this->CYID AND H.BRID_REF IN ($BranchName) AND H.STATUS = 'A' AND A.SGLID_REF = 'G')
      AND G.GLID IN (SELECT DISTINCT GLID_REF FROM TBL_MST_GLOPENING_LEDGER (NOLOCK) WHERE (GLDRBALANCE > '0.00' OR GLCRBALANCE > '0.00'))
      AND G.CYID_REF=$this->CYID AND G.BRID_REF IN ($BranchName) AND G.GLID IN ($GLID) AND G.ASGID_REF IN ($AGID)
      GROUP BY G.GLCODE,G.GLNAME,G.GLID,ASG.ASGNAME,AG.AGNAME,N.NOGNAME
      
      ) AS D GROUP BY D.GLID_REF,D.GLCODE,D.GLNAME,D.ASGNAME,D.AGNAME,D.NOGNAME"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
    

        return [
          'Nature of Group',
          'Account Group',
          'Account Sub Group',
          'General Ledger Code',
          'General Ledger Name',
          'Opening Debit',
          'Opening Credit',
          'Transaction Debit',
          'Transaction Credit',
         'Closing Debit',
         'Closing Credit',         
         ];
         
         
   
    }
}





