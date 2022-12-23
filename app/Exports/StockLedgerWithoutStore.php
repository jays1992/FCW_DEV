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
use App\Exports\StockLedgerWithoutStore;
use Maatwebsite\Excel\Facades\Excel;














class StockLedgerWithoutStore implements FromCollection, WithHeadings
{


 function __construct($From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMGID,$ITEMID,$CYID) {
        $this->ITEMID = $ITEMID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->ITEMGID = $ITEMGID;
        

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
       
       



      return collect( $data=DB::select("SELECT DISTINCT BG.BG_DESC AS BRANCH_GROUP,BR.BRNAME,
      --TBL_MST_STORE.NAME AS STORE_NAME,
       TBL_MST_ITEMGROUP.GROUPNAME,I.ICODE,I.NAME AS ITEM,
       TBL_MST_UOM.DESCRIPTIONS,
       TBL_MST_BUSINESSUNIT.BUNAME,
       I.ALPS_PART_NO,I.CUSTOMER_PART_NO,I.OEM_PART_NO,S.OPENING_QTY,
		case when S.OPENING_QTY > '0.000' then cast(ROUND(ISNULL(S.OPENING_AMT,0)/ISNULL(S.OPENING_QTY,0),2) as numeric(12,5)) else 0.00000 end as OPENING_RATE,S.OPENING_AMT,
		S.IN_QTY,case when S.IN_QTY > '0.000' then cast(ROUND(ISNULL(S.IN_QTYAMT,0)/ISNULL(S.IN_QTY,0),2) as numeric(12,5)) else 0.00000 end  as INQTY_RATE,S.IN_QTYAMT,
		S.OUT_QTY,case when S.OUT_QTY > '0.000' then cast(ROUND(ISNULL(S.OUT_QTYAMT,0)/ISNULL(S.OUT_QTY,0),2) as numeric(12,5)) else 0.00000 end  as OUTQTY_RATE,S.OUT_QTYAMT, 
		(S.OPENING_QTY+S.IN_QTY-S.OUT_QTY) AS CLOSING_QTY,case when (S.OPENING_QTY+S.IN_QTY-S.OUT_QTY) <= '0.000' then 0.00000 else
		cast(ROUND(ISNULL((S.OPENING_AMT+S.IN_QTYAMT-S.OUT_QTYAMT),0)/ISNULL((S.OPENING_QTY+S.IN_QTY-S.OUT_QTY),0),2) as numeric(12,5)) end as CLOSING_RATE,
	    (S.OPENING_AMT+S.IN_QTYAMT-S.OUT_QTYAMT) AS CLOSING_QTYAMT		 
      FROM TBL_MST_ITEM I
      LEFT JOIN (
      SELECT ISNULL(A.ITEMID_REF,B.ITEMID_REF) AS ITEMID_REF,ISNULL(A.STID_REF,B.STID_REF) AS STID_REF,
		(ISNULL(A.OPQ,0)+ISNULL(B.OPENING_QTY,0)) AS OPENING_QTY,(ISNULL(A.OPQAMT,0)+ISNULL(B.OPENINGAMT,0)) AS OPENING_AMT,
		ISNULL(A.IQ,0) AS IN_QTY,ISNULL(A.IQAMT,0) AS IN_QTYAMT,ISNULL(A.OQ,0) AS OUT_QTY,ISNULL(A.OQAMT,0) AS OUT_QTYAMT FROM (
		SELECT C.ITEMID_REF,C.STID_REF,SUM(C.OPENING_QTY) AS OPQ, cast(ROUND(SUM(ISNULL(C.OPENING_QTY,0)*CASE WHEN BT.OPENING_QTY > '0.00' THEN  ISNULL(BT.RATE,0) ELSE 0 END),2) as numeric(14,2)) AS OPQAMT,
		SUM(C.IN_QTY) AS IQ, cast(ROUND(SUM(ISNULL(C.IN_QTY,0)*CASE WHEN BT.IN_QTY > '0.00' THEN  ISNULL(BT.RATE,0) ELSE 0 END),2) as numeric(14,2)) AS IQAMT,
		SUM(C.OUT_QTY) AS OQ,cast(ROUND(SUM(ISNULL(C.OUT_QTY,0)*CASE WHEN BT.OUT_QTY > '0.00' THEN  ISNULL(BT.RATE,0) ELSE 0 END),2) as numeric(14,2)) AS OQAMT
		FROM TBL_MST_STOCK_BATCH_HIS (NOLOCK) C  INNER JOIN TBL_MST_BATCH BT ON C.BATCHID_REF = BT.BATCHID AND C.ITEMID_REF = BT.ITEMID_REF
		AND C.STID_REF = BT.STID_REF AND C.UOMID_REF = BT.UOMID_REF 
      WHERE C.DATE BETWEEN '$this->From_Date' AND '$this->To_Date' AND C.CYID_REF=$this->CYID AND C.BRID_REF IN ($BranchName)  
      GROUP BY C.ITEMID_REF,C.STID_REF
      ) AS A FULL JOIN (
      SELECT D.ITEMID_REF,D.STID_REF,(SUM(ISNULL(D.OPENING_QTY,0))+SUM(ISNULL(D.IN_QTY,0))-SUM(ISNULL(D.OUT_QTY,0))) AS OPENING_QTY,
		cast(ROUND(SUM((ISNULL(D.OPENING_QTY,0)*CASE WHEN BT.OPENING_QTY > '0.00' THEN  ISNULL(BT.RATE,0) ELSE 0 END+ISNULL(D.IN_QTY,0)*CASE WHEN BT.IN_QTY > '0.00' THEN  ISNULL(BT.RATE,0) ELSE 0 END 
		-ISNULL(D.OUT_QTY,0)*CASE WHEN BT.OUT_QTY > '0.00' THEN  ISNULL(BT.RATE,0) ELSE 0 END)),2) as numeric(14,2)) AS OPENINGAMT
		FROM TBL_MST_STOCK_BATCH_HIS (NOLOCK) D LEFT OUTER JOIN TBL_MST_BATCH (NOLOCK) BT ON D.BATCHID_REF = BT.BATCHID AND D.STOCKID_REF = BT.STOCKID_REF
		AND D.ITEMID_REF = BT.ITEMID_REF AND D.STID_REF = BT.STID_REF AND D.UOMID_REF = BT.UOMID_REF 
		WHERE D.CYID_REF=$this->CYID AND D.BRID_REF IN ($BranchName) AND  D.DATE<'$this->From_Date'
      GROUP BY D.ITEMID_REF,D.STID_REF) AS B ON A.STID_REF=B.STID_REF AND A.ITEMID_REF=B.ITEMID_REF) AS S
      ON I.ITEMID=S.ITEMID_REF LEFT OUTER JOIN
      TBL_MST_STORE ON S.STID_REF = TBL_MST_STORE.STID LEFT OUTER JOIN 
      TBL_MST_BUSINESSUNIT ON I.BUID_REF = TBL_MST_BUSINESSUNIT.BUID LEFT OUTER JOIN
      TBL_MST_ITEMGROUP ON I.ITEMGID_REF = TBL_MST_ITEMGROUP.ITEMGID LEFT OUTER JOIN
      TBL_MST_BRANCH AS BR WITH (NOLOCK) ON TBL_MST_STORE.BRID_REF=BR.BRID LEFT OUTER JOIN
      TBL_MST_BRANCH_GROUP AS BG WITH (NOLOCK) ON BR.BGID_REF=BG.BGID LEFT OUTER JOIN
      TBL_MST_UOM ON I.MAIN_UOMID_REF = TBL_MST_UOM.UOMID 
      
      WHERE  I.ITEM_TYPE='I-Inventory'  AND  I.ITEMID IN (SELECT DISTINCT ITEMID_REF FROM TBL_MST_STOCK_BATCH_HIS WHERE CYID_REF=$this->CYID AND BRID_REF IN ($BranchName))
      AND I.ITEMID IN ($ITEMID) AND I.ITEMGID_REF IN ($ITEMGID) AND BR.BRNAME IS NOT NULL AND (ISNULL(S.OPENING_QTY,0) + ISNULL(S.IN_QTY,0) + 
	  ISNULL(S.OUT_QTY,0)) <> '0.000'
      "));



      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
          'Branch Group',
          'Branch Name',
         // 'Store Name',
          'Item Group',
          'Item Code',
          'Item Name',
           'UOM',
           'Business Unit',
           'Alps Part No',
           'Customer Part No',
           'OEM Part No',
           'Opening Qty',
		   'Opening Rate',
		   'Opening Amount',		   
          'IN Qty',
		  'IN Rate',
		  'IN Amount',
          'Out Qty',
		  'Out Rate',
		  'Out Amount',
          'Closing Qty',
		  'Closing Rate',
		  'Closing Amount'
          ];
    }
}





