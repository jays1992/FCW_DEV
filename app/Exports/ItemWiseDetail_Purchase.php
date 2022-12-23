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
use App\Exports\ItemWiseDetail_Purchase;
use Maatwebsite\Excel\Facades\Excel;














class ItemWiseDetail_Purchase implements FromCollection, WithHeadings
{


 function __construct($From_Date,$To_Date,$BranchGroup,$BranchName,$ITEMGID,$ITEMID,$CYID) {
        $this->ITEMID = $ITEMID;
        $this->From_Date = $From_Date;
        $this->To_Date = $To_Date;
        $this->BranchName = $BranchName;
        $this->ITEMGID = $ITEMGID;
		$this->BranchGroup = $BranchGroup;
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
		$BranchGroup=implode(",",$this->BranchGroup); 
        $ITEMGID=implode(",",$this->ITEMGID);
       



      return collect( $data=DB::select("SELECT G.BG_DESC,F.BRNAME,C.NAME AS STORENAME,A.SOURCEDOCNO, A.SOURCEDOCDT, H.GROUPNAME,A.ITEM,A.STID,A.UOMID,B.ICODE,B.NAME,D.UOMCODE, E.BUNAME, B.ALPS_PART_NO,
        B.CUSTOMER_PART_NO,B.OEM_PART_NO,B.SAP_CUSTOMER_NAME,ISNULL(A.INQTY,0.000) AS INQTY,ISNULL(A.OUTQTY,0.000) AS OUTQTY,A.RATE,A.INAMOUNT, A.OUTAMOUNT
		FROM (
		SELECT B.GRN_NO AS SOURCEDOCNO, B.GRN_DT AS SOURCEDOCDT,A.ITEMID_REF AS ITEM,A.STID_REF AS STID,A.MAIN_UOMID_REF AS UOMID,SUM(A.RECEIVED_QTYM) AS INQTY, 0.000 AS OUTQTY,
		0.000 AS OPENING_QTY,B.BRID_REF,C.RATE,0.00 AS OPENINGAMOUNT,CAST((SUM(A.RECEIVED_QTYM)*C.RATE) AS NUMERIC(14,2)) AS INAMOUNT,
        0.00 AS OUTAMOUNT
		FROM TBL_TRN_IGRN02_MULTISTORE A(NOLOCK) INNER JOIN TBL_TRN_IGRN02_HDR B(NOLOCK) ON A.GRNID_REF = B.GRNID 
		INNER JOIN TBL_TRN_IGRN02_MAT C(NOLOCK) ON A.GRNID_REF = C.GRNID_REF AND  A.ITEMID_REF = C.ITEMID_REF AND A.MAIN_UOMID_REF = C.MAIN_UOMID_REF
		AND ISNULL(A.POID_REF,'') = ISNULL(C.POID_REF,'') AND ISNULL(A.BPOID_REF,'') = ISNULL(C.BPOID_REF,'') AND ISNULL(A.MRSID_REF,'') = ISNULL(C.MRSID_REF,'')
        AND ISNULL(A.PIID_REF,'') = ISNULL(C.PIID_REF,'') AND ISNULL(A.RFQID_REF,'') = ISNULL(C.RFQID_REF,'')
        AND ISNULL(A.VQID_REF,'') = ISNULL(C.VQID_REF,'') AND ISNULL(A.IPOID_REF,'') = ISNULL(C.IPOID_REF,'')
		WHERE B.GRN_DT BETWEEN '$this->From_Date' AND '$this->To_Date'  AND B.STATUS = 'A' AND B.CYID_REF = $this->CYID AND B.BRID_REF IN ($BranchName)
		GROUP BY B.GRN_NO, B.GRN_DT,A.ITEMID_REF,A.STID_REF,A.MAIN_UOMID_REF,B.BRID_REF,C.RATE
		UNION
		SELECT B.PRR_NO AS SOURCEDOCNO, B.PRR_DT AS SOURCEDOCDT,A.ITEMID_REF AS ITEM,A.STID_REF AS STID,A.MAIN_UOMID_REF AS UOMID,0.000 AS INQTY, SUM(A.RETURN_QTYM) AS OUTQTY,
		0.000 AS OPENING_QTY,B.BRID_REF,D.RATE,0.00 AS OPENINGAMOUNT,0.00 INAMOUNT, D.AMOUNT AS OUTAMOUNT
		FROM TBL_TRN_PRRT01_MULTISTORE A(NOLOCK) INNER JOIN TBL_TRN_PRRT01_HDR B(NOLOCK) ON A.PRRID_REF = B.PRRID
        INNER JOIN TBL_MST_BATCH C (NOLOCK) ON A.ITEMID_REF = C.ITEMID_REF AND A.MAIN_UOMID_REF = C.UOMID_REF AND A.STID_REF = C.STID_REF
        AND A.BATCH_NO = C.BATCH_CODE INNER JOIN TBL_MST_STOCK_BATCH_HIS D ON A.ITEMID_REF = D.ITEMID_REF AND A.MAIN_UOMID_REF = D.UOMID_REF
        AND A.STID_REF = D.STID_REF AND A.PRRID_REF = D.RCID AND C.BATCHID = D.BATCHID_REF
		WHERE B.PRR_DT BETWEEN '$this->From_Date' AND '$this->To_Date'  AND B.STATUS = 'A' AND B.CYID_REF = $this->CYID AND B.BRID_REF IN ($BranchName)
        AND D.VTID = 95
		GROUP BY B.PRR_NO, B.PRR_DT,A.ITEMID_REF,A.STID_REF,A.MAIN_UOMID_REF,B.BRID_REF,D.RATE,D.AMOUNT
		) AS A	INNER JOIN TBL_MST_ITEM B (NOLOCK) ON A.ITEM = B.ITEMID
				INNER JOIN TBL_MST_ITEMGROUP H (NOLOCK) ON B.ITEMGID_REF = H.ITEMGID
				LEFT OUTER JOIN TBL_MST_STORE C (NOLOCK) ON A.STID = C.STID
				LEFT OUTER JOIN TBL_MST_UOM D (NOLOCK) ON A.UOMID = D.UOMID
				LEFT OUTER JOIN TBL_MST_BUSINESSUNIT E(NOLOCK) ON B.BUID_REF = E.BUID
				INNER JOIN TBL_MST_BRANCH F (NOLOCK) ON A.BRID_REF = F.BRID
				INNER JOIN TBL_MST_BRANCH_GROUP G (NOLOCK) ON G.BGID = F.BGID_REF
		WHERE   A.ITEM IN ($ITEMID) AND B.ITEMGID_REF IN ($ITEMGID) AND A.BRID_REF IN ($BranchName) AND F.BGID_REF IN ($BranchGroup)"));

      
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
          'Branch Group',
          'Branch Name',
          'Store Name',
		  'Source Doc No',
		  'Source Doc Date',
          'Item Group',
		  'Item ID',
		  'Store ID',
		  'UOM ID',
          'Item Code',
          'Item Name',
           'UOM',
           'Business Unit',
           'Alps Part No',
           'Customer Part No',
           'OEM Part No',
		   'Sap Customer Name',
          'IN Qty',
          'Out Qty',
		  'Rate',
		  'IN Qty Amount',
		  'OUT Qty Amount'
          ];
    }
}





