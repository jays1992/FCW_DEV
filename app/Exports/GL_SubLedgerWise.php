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
use App\Exports\GL_SubLedgerWise;
use Maatwebsite\Excel\Facades\Excel;














class GL_SubLedgerWise implements FromCollection, WithHeadings
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



      return collect( $data=DB::select("SELECT A.NOGNAME,
      A.AGNAME,
      A.ASGNAME,
      A.GLCODE,
      A.GLNAME,
      A.SGLCODE,
      A.SLNAME,
      A.SOURCE_DOCTYPE,
      A.JV_NO,
      A.JV_DT,
      A.NARRATION,
      A.SOURCE_DOCNO,
      A.SOURCE_DOCDT,
	  CASE WHEN A.OPENING_DR > A.OPENING_CR THEN (A.OPENING_DR - A.OPENING_CR) ELSE (A.OPENING_CR - A.OPENING_DR) END  AS OPENING_BALANCE,
	  CASE WHEN A.OPENING_DR > A.OPENING_CR THEN 'DR' ELSE 'CR' END  AS OPENING_TYPE,
      A.DR_AMT,
      A.CR_AMT
      FROM(
      SELECT D.NOGNAME,D.AGNAME,D.ASGNAME,
      D.GLID_REF AS GLID,D.GLCODE AS GLCODE,D.GLNAME AS GLNAME,
      D.SGLID_REF,D.SGLCODE,D.SLNAME,
      D.SOURCE_DOCTYPE,D.JV_NO, D.JV_DT,D.NARRATION, D.SOURCE_DOCNO,D.SOURCE_DOCDT,
      CASE WHEN D.SGLID_REF IS NULL THEN DBO.FN_GLODBL(D.GLID_REF,'$this->From_Date') ELSE 0.00 END AS OPENING_DR,
      CASE WHEN D.SGLID_REF IS NULL THEN DBO.FN_GLOCBL(D.GLID_REF,'$this->From_Date') ELSE 0.00 END AS OPENING_CR,
      SUM(D.DR_AMT) AS DR_AMT,SUM(D.CR_AMT) AS CR_AMT
      FROM (
      select N.NOGNAME,AG.AGNAME,ASG.ASGNAME,
      G.GLCODE,G.GLNAME,H.SOURCE_DOCTYPE,H.JV_NO, H.JV_DT,H.NARRATION, H.SOURCE_DOCNO,H.SOURCE_DOCDT,
      IIF(A.SGLID_REF='S',DBO.FN_GLBYSL(A.GLID_REF),A.GLID_REF) AS GLID_REF,
      IIF(A.SGLID_REF='S',A.GLID_REF,NULL) AS SGLID_REF, SL.SGLCODE,SL.SLNAME,
      ISNULL(A.DR_AMT,0.00) AS DR_AMT,
      ISNULL(A.CR_AMT,0.00) AS CR_AMT
      from TBL_TRN_FJRV01_ACC A (NOLOCK)
      JOIN TBL_TRN_FJRV01_HDR H (NOLOCK) ON A.JVID_REF=H.JVID
      JOIN TBL_MST_SUBLEDGER SL (NOLOCK) ON IIF(A.SGLID_REF='S',A.GLID_REF,NULL)=SL.SGLID
      JOIN TBL_MST_GENERALLEDGER G (NOLOCK) ON SL.GLID_REF=G.GLID
      left join TBL_MST_ACCOUNTSUBGROUP as ASG (NOLOCK) on ASG.ASGID=G.ASGID_REF
      left join TBL_MST_ACCOUNTGROUP as AG (NOLOCK) on AG.AGID=ASG.AGID_REF
      left join TBL_MST_NATUREOFGROUP as N (NOLOCK) ON N.NOGID=AG.NOGID_REF
      WHERE H.JV_DT BETWEEN '$this->From_Date' AND '$this->To_Date' AND H.CYID_REF=$this->CYID AND H.BRID_REF IN ($BranchName) AND H.STATUS = 'A' AND A.SGLID_REF = 'S'
      AND G.GLID IN ($GLID) AND G.ASGID_REF IN ($AGID)
      UNION ALL
	  select N.NOGNAME,AG.AGNAME,ASG.ASGNAME,
      G.GLCODE,G.GLNAME,H.SOURCE_DOCTYPE,H.MJV_NO AS JV_NO, H.MJV_DT AS JV_DT,H.NARRATION, H.SOURCE_DOCNO,H.SOURCE_DOCDT,
      IIF(A.SGLID_REF='S',DBO.FN_GLBYSL(A.GLID_REF),A.GLID_REF) AS GLID_REF,
      IIF(A.SGLID_REF='S',A.GLID_REF,NULL) AS SGLID_REF, SL.SGLCODE,SL.SLNAME,
      ISNULL(A.DR_AMT,0.00) AS DR_AMT,
      ISNULL(A.CR_AMT,0.00) AS CR_AMT
      from TBL_TRN_MJRV01_ACC A (NOLOCK)
      JOIN TBL_TRN_MJRV01_HDR H (NOLOCK) ON A.MJVID_REF=H.MJVID
      JOIN TBL_MST_SUBLEDGER SL (NOLOCK) ON IIF(A.SGLID_REF='S',A.GLID_REF,NULL)=SL.SGLID
      JOIN TBL_MST_GENERALLEDGER G (NOLOCK) ON SL.GLID_REF=G.GLID
      left join TBL_MST_ACCOUNTSUBGROUP as ASG (NOLOCK) on ASG.ASGID=G.ASGID_REF
      left join TBL_MST_ACCOUNTGROUP as AG (NOLOCK) on AG.AGID=ASG.AGID_REF
      left join TBL_MST_NATUREOFGROUP as N (NOLOCK) ON N.NOGID=AG.NOGID_REF
      WHERE H.MJV_DT BETWEEN '$this->From_Date' AND '$this->To_Date' AND H.CYID_REF=$this->CYID AND H.BRID_REF IN ($BranchName) AND H.STATUS = 'A' AND A.SGLID_REF = 'S'
      AND G.GLID IN ($GLID) AND G.ASGID_REF IN ($AGID)
      UNION ALL
      select N.NOGNAME,AG.AGNAME,ASG.ASGNAME,
      G.GLCODE,G.GLNAME,H.SOURCE_DOCTYPE,H.JV_NO, H.JV_DT,H.NARRATION, H.SOURCE_DOCNO,H.SOURCE_DOCDT,
      IIF(A.SGLID_REF='S',DBO.FN_GLBYSL(A.GLID_REF),A.GLID_REF) AS GLID_REF,
      NULL AS SGLID_REF, NULL AS SGLCODE, NULL AS SLNAME,
      ISNULL(A.DR_AMT,0.00) AS DR_AMT,
      ISNULL(A.CR_AMT,0.00) AS CR_AMT
      from TBL_TRN_FJRV01_ACC A (NOLOCK)
      JOIN TBL_TRN_FJRV01_HDR H (NOLOCK) ON A.JVID_REF=H.JVID
      JOIN TBL_MST_GENERALLEDGER G (NOLOCK) ON IIF(A.SGLID_REF='S',DBO.FN_GLBYSL(A.GLID_REF),A.GLID_REF)=G.GLID
      left join TBL_MST_ACCOUNTSUBGROUP as ASG (NOLOCK) on ASG.ASGID=G.ASGID_REF
      left join TBL_MST_ACCOUNTGROUP as AG (NOLOCK) on AG.AGID=ASG.AGID_REF
      left join TBL_MST_NATUREOFGROUP as N (NOLOCK) ON N.NOGID=AG.NOGID_REF
      WHERE H.JV_DT BETWEEN '$this->From_Date' AND '$this->To_Date' AND H.CYID_REF=$this->CYID AND H.BRID_REF IN ($BranchName) AND H.STATUS = 'A' AND A.SGLID_REF = 'G'
      AND G.GLID IN ($GLID) AND G.ASGID_REF IN ($AGID)
      UNION ALL
	  select N.NOGNAME,AG.AGNAME,ASG.ASGNAME,
      G.GLCODE,G.GLNAME,H.SOURCE_DOCTYPE,H.MJV_NO AS JV_NO, H.MJV_DT AS JV_DT,H.NARRATION, H.SOURCE_DOCNO,H.SOURCE_DOCDT,
      IIF(A.SGLID_REF='S',DBO.FN_GLBYSL(A.GLID_REF),A.GLID_REF) AS GLID_REF,
      NULL AS SGLID_REF, NULL AS SGLCODE, NULL AS SLNAME,
      ISNULL(A.DR_AMT,0.00) AS DR_AMT,
      ISNULL(A.CR_AMT,0.00) AS CR_AMT
      from TBL_TRN_MJRV01_ACC A (NOLOCK)
      JOIN TBL_TRN_MJRV01_HDR H (NOLOCK) ON A.MJVID_REF=H.MJVID
      JOIN TBL_MST_GENERALLEDGER G (NOLOCK) ON IIF(A.SGLID_REF='S',DBO.FN_GLBYSL(A.GLID_REF),A.GLID_REF)=G.GLID
      left join TBL_MST_ACCOUNTSUBGROUP as ASG (NOLOCK) on ASG.ASGID=G.ASGID_REF
      left join TBL_MST_ACCOUNTGROUP as AG (NOLOCK) on AG.AGID=ASG.AGID_REF
      left join TBL_MST_NATUREOFGROUP as N (NOLOCK) ON N.NOGID=AG.NOGID_REF
      WHERE H.MJV_DT BETWEEN '$this->From_Date' AND '$this->To_Date' AND H.CYID_REF=$this->CYID AND H.BRID_REF IN ($BranchName) AND H.STATUS = 'A' AND A.SGLID_REF = 'G'
      AND G.GLID IN ($GLID) AND G.ASGID_REF IN ($AGID)
	  UNION ALL
      select N.NOGNAME,AG.AGNAME,ASG.ASGNAME,
      G.GLCODE,G.GLNAME,'' AS SOURCE_DOCTYPE,'' AS JV_NO, NULL AS JV_DT,'' AS NARRATION, '' AS SOURCE_DOCNO,NULL AS SOURCE_DOCDT,
      G.GLID AS GLID_REF,
      NULL AS SGLID_REF, NULL AS SGLCODE, NULL AS SLNAME,
      0.00 AS DR_AMT,
      0.00 AS CR_AMT
      from TBL_MST_GENERALLEDGER G (NOLOCK) 
      left join TBL_MST_ACCOUNTSUBGROUP as ASG (NOLOCK) on ASG.ASGID=G.ASGID_REF
      left join TBL_MST_ACCOUNTGROUP as AG (NOLOCK) on AG.AGID=ASG.AGID_REF
      left join TBL_MST_NATUREOFGROUP as N (NOLOCK) ON N.NOGID=AG.NOGID_REF
      WHERE G.GLID not in (select IIF(A.SGLID_REF='S',DBO.FN_GLBYSL(A.GLID_REF),A.GLID_REF) from TBL_TRN_FJRV01_ACC A (NOLOCK)JOIN TBL_TRN_FJRV01_HDR H (NOLOCK) ON A.JVID_REF=H.JVID 
            WHERE H.JV_DT BETWEEN '$this->From_Date' AND '$this->To_Date' AND H.CYID_REF=$this->CYID AND H.BRID_REF IN ($BranchName) AND H.STATUS = 'A' AND A.SGLID_REF='G'
			UNION
			select IIF(A.SGLID_REF='S',DBO.FN_GLBYSL(A.GLID_REF),A.GLID_REF) from TBL_TRN_MJRV01_ACC A (NOLOCK)JOIN TBL_TRN_MJRV01_HDR H (NOLOCK) ON A.MJVID_REF=H.MJVID 
            WHERE H.MJV_DT BETWEEN '$this->From_Date' AND '$this->To_Date' AND H.CYID_REF=$this->CYID AND H.BRID_REF IN ($BranchName) AND H.STATUS = 'A' AND A.SGLID_REF='G')
      AND G.GLID IN (SELECT DISTINCT GLID_REF FROM TBL_MST_GLOPENING_LEDGER (NOLOCK) WHERE (GLDRBALANCE > '0.00' OR GLCRBALANCE > '0.00'))
      AND G.GLID not in (SELECT GLID_REF FROM TBL_MST_SUBLEDGER WHERE BELONGS_TO IN ('CUSTOMER','VENDOR'))
      AND G.CYID_REF=$this->CYID AND G.BRID_REF IN ($BranchName) AND G.GLID IN ($GLID) AND G.ASGID_REF IN ($AGID)
      ) AS D GROUP BY D.GLID_REF,D.GLCODE,D.GLNAME,D.ASGNAME,D.AGNAME,D.NOGNAME,D.SGLID_REF,D.SGLCODE,D.SLNAME,D.SOURCE_DOCTYPE,D.JV_NO, D.JV_DT,D.NARRATION, D.SOURCE_DOCNO,D.SOURCE_DOCDT
      ) AS A
      "));

      
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
          'Sub Ledger Code',
          'Sub Ledger Name',
          'Source Doc Type',
          'JV No',
          'JV Date',
          'Narration',
          'Source Doc No',
          'Source Doc Date',
		  'Opening Balance',
		  'Opening Type',
          'Debit Amount',
          'Credit Amount',  
              
         ];
         
         
   
    }
}





