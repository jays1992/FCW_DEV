@extends('layouts.app')
@section('content')
   
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                  <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Purchase Indent (PI)</a>
                </div><!--col-2-->
                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveFormData" ><i class="fa fa-floppy-o"></i> Save</button>
                        <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div><!--row-->
    </div>
    <form id="form_data"  method="POST"  > 
      @CSRF 
    <div class="container-fluid filter">

	<div class="inner-form">
	
		<div class="row">
			<div class="col-lg-2 pl"><p>PI NO*</p></div>
			<div class="col-lg-2 pl">
            <input type="text" name="PI_NO" id="PI_NO" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
          <script>docMissing(@json($docarray['FY_FLAG']));</script>
        
			</div>
			
			<div class="col-lg-2 pl"><p>PI Date*</p></div>
			<div class="col-lg-2 pl">
				    <input type="date" name="PI_DT" id="PI_DT" onchange='checkPeriodClosing("{{$FormId}}",this.value,1),getDocNoByEvent("PI_NO",this,@json($doc_req))'  class="form-control mandatory"  placeholder="dd-mm-yyyy" >
      </div>
			
			<div class="col-lg-2 pl"><p>From Store*</p></div>
			<div class="col-lg-2 pl">
                <input type="text" name="store_popup" id="txtstore_popup" class="form-control mandatory"  autocomplete="off" readonly/>
                <input type="hidden" name="STID_REF" id="STID_REF" class="form-control" autocomplete="off" />
			</div>
			
		
    </div>		
    
    <div class="row">

      <div class="col-lg-2 pl"><p>To Department*</p></div>
			<div class="col-lg-2 pl">
                <input type="text" name="dept_popup" id="txtdept_popup" class="form-control mandatory"  autocomplete="off" readonly/>
                <input type="hidden" name="DEPID_REF" id="DEPID_REF" class="form-control" autocomplete="off" />
                <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />   
			</div>

      <div class="col-lg-2 pl"><p>Direct PI</p></div>
        <div class="col-lg-2 pl">
        <input type="checkbox" name="DIRECT_PI" id="DIRECT_PI" value="1" />
      </div>

      <div class="col-lg-2 pl"><p>Remarks</p></div>
			<div class="col-lg-2 pl">
         <input type="text" name="REMARKS" id="txtREMARKS" class="form-control"  autocomplete="off" />
			</div>
    </div>  
		
	</div>

	<div class="container-fluid">

		<div class="row">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
			</ul>
			
			
			
			<div class="tab-content">

				<div id="Material" class="tab-pane fade in active">
					<div class="table-responsive table-wrapper-scroll-y" style="height:500px;margin-top:10px;" >
						<table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
							<thead id="thead1"  style="position: sticky;top: 0">
								  <tr>
									<th>MRS No<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="1"></th>
									<th>Item Code</th>
									<th>Item Name</th>
									<th>UOM</th>
									<th>Item Specification</th>
                  <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                  <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                  <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
									<th>Stock in Hand</th>
									<th>Balance MRS Qty</th>
									<th>Indent Qty</th>
									<th>EDA</th>
									<th>Approx Value</th>
									<th>Remarks</th>
									<th>Action</th>
								  </tr>
							</thead>
							<tbody>
								  <tr  class="participantRow">
                    <td hidden><input type="text" name="recordId_0" id="txtrecordId_0" class="form-control"  autocomplete="off"  readonly/></td>
                    
                    <td hidden><input type="text" name="MrsID_0" id="HDNMrsID_0" class="form-control" autocomplete="off" /></td>
                    <td><input type="text" name="MRS_popup_0" id="txtMRS_popup_0" class="form-control CLS_MRS"  autocomplete="off"  readonly/></td>
                    
                    <td  hidden><input type="text" name="ITEMID_REF_0" id="ITEMID_REF_0" class="form-control" autocomplete="off" /></td>
                    <td><input type="text" name="popupITEMID_0" id="popupITEMID_0" class="form-control"  autocomplete="off"  readonly/></td>                    
                    
                    <td><input type="text" name="ItemName_0" id="ItemName_0" class="form-control"  autocomplete="off"  readonly/></td>
                    <td><input type="text" name="popupMUOM_0" id="popupMUOM_0" class="form-control"  autocomplete="off"  readonly/></td>
                    <td  hidden><input type="text" name="MAIN_UOMID_REF_0" id="MAIN_UOMID_REF_0" class="form-control"  autocomplete="off" /></td>
                    <td><input type="text" name="Itemspec_0" id="Itemspec_0" class="form-control"  autocomplete="off"  /></td>
                    <td {{$AlpsStatus['hidden']}}  ><input type="text" name="Alpspartno_0" id="Alpspartno_0" class="form-control"  autocomplete="off" value="{{$objCOMPANY->NAME}}"  readonly  /></td>
                    <td {{$AlpsStatus['hidden']}}  ><input type="text" name="Custpartno_0" id="Custpartno_0" class="form-control"  autocomplete="off"  readonly  /></td>
                    <td {{$AlpsStatus['hidden']}}  ><input type="text" name="OEMpartno_0" id="OEMpartno_0" class="form-control"  autocomplete="off"  readonly /></td>
                    
                    <td><input type="text" name="STOCK_QTY_0" id="txtSTOCK_QTY_0" class="form-control three-digits" maxlength="13"  autocomplete="off"  readonly /></td>
                    <td ><input type="text" name="BAL_MRSQTY_0" id="BAL_MRSQTY_0" class="form-control three-digits" maxlength="13"  autocomplete="off"  readonly/></td>

                    <td ><input type="text" name="INDENT_QTY_0" id="INDENT_QTY_0" class="form-control three-digits" maxlength="13"  autocomplete="off"  /></td>

                    <td ><input type="date" name="EDA_0" id="EDA_0" class="form-control"  placeholder="dd-mm-yyyy" /></td>

                    <td ><input type="text" name="APPROX_VAL_0" id="APPROX_VAL_0" value="0.000" class="form-control three-digits" maxlength="13"  autocomplete="off"  /></td>

                    <td><input type="text" name="REMARKS_0" id="REMARKS_0" class="form-control"  autocomplete="off"  /></td>
                   
                    <td align="center" ><button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                    <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
								  </tr>
							</tbody>
					  </table>
					</div>	
				</div>

				
				
			</div>
		</div>
		
	</div>
	
</div>

<!-- </div> -->
</form>
@endsection
@section('alert')
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog"  >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='closePopup' >&times;</button>
        <h4 class="modal-title">System Alert Message</h4>
      </div>
      <div class="modal-body">
	      <h5 id="AlertMessage" ></h5>
        <div class="btdiv">
            <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData"><div id="alert-active" class="activeYes"></div>Yes</button>
            <button class="btn alertbt" name='NoBtn' id="NoBtn"   data-funcname="fnUndoNo" ><div id="alert-active" class="activeNo"></div>No</button>
            <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk"></div>OK</button>
            <button onclick="getFocus()" class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk1"></div>OK</button>
            <input type="hidden" id="FocusId" >
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- Alert -->
<!-- GLID Dropdown -->
<div id="storeidpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='store_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Store Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="StoreCodeTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Description</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="storecodesearch" class="form-control" autocomplete="off" onkeyup="StoreCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="storenamesearch" class="form-control" autocomplete="off" onkeyup="StoreNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="StoreCodeTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
          <!-- <tr>
            <th>GLCode</th>
            <th>GLName</th>
          </tr> -->
          
        </thead>
        <tbody>
        @foreach ($objStore as $index=>$glRow)
        <tr>
        <td class="ROW1"> <input type="checkbox" name="SELECT_STID_REF[]" id="storeidcode_{{ $index }}" class="clsstoreid" value="{{ $glRow-> STID }}" ></td>
        <td class="ROW2">{{ $glRow-> STCODE }} <input type="hidden" id="txtstoreidcode_{{ $index }}" data-desc="{{ $glRow-> STCODE }}-{{ $glRow-> NAME }}"  value="{{ $glRow-> STID }}"/></td>
          <td class="ROW3">{{ $glRow-> NAME }}</td>
        </tr>
        @endforeach
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- GLID Dropdown-->
<!-- Department Dropdown -->
<div id="deptpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='dept_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Department</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="deptTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Description</th>
    </tr>
    </thead>
    <tbody>

    <tr>
      <th class="ROW1"><span class="check_th">&#10004;</span></th>
      <td class="ROW2"><input type="text" id="deptcodesearch" class="form-control" autocomplete="off" onkeyup="deptCodeFunction()"></td>
      <td class="ROW3"><input type="text" id="deptnamesearch" class="form-control" autocomplete="off" onkeyup="deptNameFunction()"></td>
    </tr>

    </tbody>
    </table>
      <table id="deptTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_deptacct">       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Department Dropdown-->
<!-- Sales Order Dropdown -->
<div id="MRSPopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='MRS_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>MRS Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="MRSPTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" >
            
            <td> <input type="hidden" name="fieldid" id="hdn_mrsid"/>
            <input type="hidden" name="fieldid2" id="hdn_mrsid2"/></td>
          </tr>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">No</th>
      <th class="ROW3">Date</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="MRScodesearch" class="form-control" autocomplete="off" onkeyup="MRSCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="MRSnamesearch" class="form-control" autocomplete="off" onkeyup="MRSNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="MRSPTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_SO">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Sales Order Dropdown-->
<!-- Item Code Dropdown -->
<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
    <thead>
      <tr id="none-select" class="searchalldata" text>
        <td hidden> 
            <input type="text" name="fieldid" id="hdn_ItemID"/>
            <input type="text" name="fieldid2" id="hdn_ItemID2"/>
            <input type="text" name="fieldid3" id="hdn_ItemID3"/>
            <input type="text" name="fieldid4" id="hdn_ItemID4"/>
            <input type="text" name="fieldid5" id="hdn_ItemID5"/>
            <input type="text" name="fieldid6" id="hdn_ItemID6"/>
            <input type="text" name="fieldid7" id="hdn_ItemID7"/>
            <input type="text" name="fieldid8" id="hdn_ItemID8"/>
            <input type="text" name="fieldid9" id="hdn_ItemID9"/>
            <input type="text" name="fieldid10" id="hdn_ItemID10"/>
            <input type="text" name="fieldid11" id="hdn_ItemID11"/>
            <input type="text" name="fieldid12" id="hdn_ItemID12"/>
            <input type="text" name="fieldid13" id="hdn_ItemID13"/>
        </td>
      </tr>
      <!-- <tr class="topnav"><button class="btn topnavbt" id="btnOk"><i class="fa fa-check"></i> OK</button></tr> -->
      <tr>
            <th style="width:10%;text-align:center;"id="all-check">Select</th>
            <th style="width:9%;">Item Code</th>
            <th style="width:9%;">Name</th>
            <th style="width:9%;">Main UOM</th>
            <th style="width:9%;">Item Specification</th>
            <th style="width:9%;">Stock in Hand</th>
            <th style="width:9%;">Balance MRS Qty</th>
            <th style="width:9%;">Business Unit</th>
            <th style="width:9%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
            <th style="width:9%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
            <th style="width:9%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
      </tr>
    </thead>
    <tbody>
    <tr>
    <td style="width:10%;text-align:center;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
    <td style="width:9%;">
    <input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction()" >
    </td>
    <td style="width:9%;">
    <input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemNameFunction()" >
    </td>
    <td style="width:9%;">
    <input type="text" id="ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="ItemUOMFunction()" >
    </td>
    <td style="width:9%;">
      <input type="text" id="ItemSpecsearch" class="form-control" autocomplete="off" onkeyup="ItemSpecFunction()" >
      </td>
    <td style="width:9%;">
    <input type="text" id="ItemStockQTYsearch" class="form-control" autocomplete="off" onkeyup="StockQTYFunction()" >
    </td>
   <td style="width:9%;">
    <input type="text" id="ItemBalSoQtysearch" class="form-control" autocomplete="off" onkeyup="ItemBalSoQtyFunction()" >
    </td>

    <td style="width:9%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction()"></td>
    <td style="width:9%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="ItemAPNFunction()"></td>
    <td style="width:9%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="ItemCPNFunction()"></td>
    <td style="width:9%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="ItemOEMPNFunction()"></td>

    </tr>
    </tbody>
    </table>
      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_ItemID" style="font-size:12px;">     
          
          
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Item Code Dropdown-->


@endsection


@push('bottom-css')
<style>
#ItemIDcodesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}
#ItemIDnamesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}

#ItemIDTable {
  border-collapse: collapse;
  width: 950px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#ItemIDTable th {
    text-align: center;
    padding: 5px;
   
    font-size: 11px;
    
    color: #0f69cc;
    font-weight: 600;
}

#ItemIDTable td {
    text-align: center;
    padding: 5px;
    font-size: 11px;
    
    font-weight: 600;
}

#ItemIDTable2 {
  border-collapse: collapse;
  width: 1050px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#ItemIDTable2 th{
    text-align: left;
    padding: 5px;
   
    font-size: 11px;
   
    color: #0f69cc;
    font-weight: 600;
}

#ItemIDTable2 td {
  text-align: left;
    padding: 5px;
    font-size: 11px;
    
    font-weight: 600;
    width: 16%;
}

</style>
@endpush
@push('bottom-scripts')
<script>

//------------------------
  //Store
  let tid = "#StoreCodeTable2";
      let tid2 = "#StoreCodeTable";
      let headers = document.querySelectorAll(tid2 + " th");

      // Sort the table element when clicking on the table headers
      headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tid, ".clsstoreid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function StoreCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("storecodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("StoreCodeTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[1];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
        }
      }

  function StoreNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("storenamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("StoreCodeTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[2];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
    }
  }

  $('#txtstore_popup').click(function(event){
         showSelectedCheck($("#STID_REF").val(),"SELECT_STID_REF");
         $("#storeidpopup").show();
         event.preventDefault();
      });

      $("#store_closePopup").click(function(event){
        $("#storeidpopup").hide();
        event.preventDefault();
      });

      $(".clsstoreid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");

        $('#txtstore_popup').val(texdesc);
        $('#STID_REF').val(txtval);
        $("#storeidpopup").hide();
        $("#storecodesearch").val(''); 
        $("#storenamesearch").val(''); 
      
        
        $('[id*="txtMRS_popup"]').each(function(){

          if($(this).parent().parent().find('[id*="HDNMrsID"]').attr('id'))
          $("#"+$(this).attr('id')).val('');

          var mrs_val = $("#"+$(this).parent().parent().find('[id*="HDNMrsID"]').attr('id')).val();

          if($.trim(mrs_val)!=""){

            $("#"+$(this).parent().parent().find('[id*="HDNMrsID"]').attr('id')).val('');
            $("#"+$(this).parent().parent().find('[id*="txtrecordId"]').attr('id')).val('');
            $("#"+$(this).parent().parent().find('[id*="popupITEMID"]').attr('id')).val('');
            $("#"+$(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id')).val('');
            $("#"+$(this).parent().parent().find('[id*="ItemName"]').attr('id')).val('');
            $("#"+$(this).parent().parent().find('[id*="popupMUOM"]').attr('id')).val('');
            $("#"+$(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id')).val('');
            $("#"+$(this).parent().parent().find('[id*="Itemspec"]').attr('id')).val('');
            $("#"+$(this).parent().parent().find('[id*="txtSTOCK_QTY"]').attr('id')).val('');
            $("#"+$(this).parent().parent().find('[id*="BAL_MRSQTY"]').attr('id')).val('');
            $("#"+$(this).parent().parent().find('[id*="INDENT_QTY"]').attr('id')).val('');
            $("#"+$(this).parent().parent().find('[id*="EDA"]').attr('id')).val('');
            $("#"+$(this).parent().parent().find('[id*="APPROX_VAL"]').attr('id')).val('0.000');
            $("#"+$(this).parent().parent().find('[id*="REMARKS"]').attr('id')).val('');

          }
          
        });

        event.preventDefault();
      });

      

  //Store Ends
//------------------------
//Department Starts
//------------------------

      let sgltid = "#deptTable2";
      let sgltid2 = "#deptTable";
      let sglheaders = document.querySelectorAll(sgltid2 + " th");

      // Sort the table element when clicking on the table headers
      sglheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sgltid, ".clsdept", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function deptCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("deptcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("deptTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[1];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
        }
      }

  function deptNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("deptnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("deptTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[2];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
    }
  }

$("#txtdept_popup").click(function(event){
    
     $("#deptpopup").show();

          $('#tbody_deptacct').html('<tr><td colspan="2">Please wait..</td></tr>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[$FormId,"getdepartment"])}}',
                type:'POST',
                success:function(data) {
                    $('#tbody_deptacct').html(data);
                    bindDepartmentEvents();
                    showSelectedCheck($("#DEPID_REF").val(),"SELECT_DEPID_REF");
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#tbody_deptacct').html('');
                },
            });        
     
     
     event.preventDefault();
  });

$("#dept_closePopup").on("click",function(event){ 
    $("#deptpopup").hide();
    event.preventDefault();
});

function bindDepartmentEvents(){
        $('.clsdept').click(function(){
    
            var id = $(this).attr('id');
            var txtval =    $("#txt"+id+"").val();
            var texdesc =   $("#txt"+id+"").data("desc");
            var oldSLID =   $("#DEPID_REF").val();
           // var MaterialClone = $('#hdnmaterial').val();
            $("#txtdept_popup").val(texdesc);
            $("#txtdept_popup").blur();
            $("#DEPID_REF").val(txtval);
            // if (txtval != oldSLID)
            // {
            //     $('#Material').html(MaterialClone);
            //     $('#Row_Count1').val('1');
            // }
            $("#deptpopup").hide();
            $("#deptcodesearch").val(''); 
            $("#deptnamesearch").val(''); 
       
              
        });
  }
//Department Ends
//------------------------
  //Sales Order Dropdown
  let sotid = "#MRSPTable2";
      let sotid2 = "#MRSPTable";
      let salesOrderheaders = document.querySelectorAll(sotid2 + " th");

      // Sort the table element when clicking on the table headers
      salesOrderheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sotid, ".clssqid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function MRSCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("MRScodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("MRSPTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[1];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
        }
      }

  function MRSNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("MRSnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("MRSPTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[2];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
    }
  }

   $('#Material').on('click','[id*="txtMRS_popup"]',function(event){

        if($.trim($("#STID_REF").val())==""){
            $("#FocusId").val('txtstore_popup');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please select Store first.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
            return false;
        }
        
        if($("#DIRECT_PI").prop("checked")) {
          return false;
        }
        

        var customid = $.trim($("#STID_REF").val());
        var fieldid = $(this).parent().parent().find('[id*="HDNMrsID"]').attr('id');

              if(customid!=''){
                  $("#tbody_SO").html('');
                  $("#tbody_SO").html('<tr><td colspan="2">Please wait...</td></tr>');
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                  })
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"getMRS"])}}',
                      type:'POST',
                      data:{'id':customid,'fieldid':fieldid},
                      success:function(data) {
                        $("#tbody_SO").html(data);
                        BindMRS();
                        showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid); 
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_SO").html('');
                      },
                  });
              }
            
            event.preventDefault();

        $("#MRSPopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="HDNMrsID"]').attr('id');

        $('#hdn_mrsid').val(id);
        $('#hdn_mrsid2').val(id2);
      });

      $("#MRS_closePopup").click(function(event){
        $("#MRSPopup").hide();
      });

      function BindMRS(){

        $(".clssmrsid").click(function(){
            var fieldid = $(this).attr('id');
            var txtval =    $("#txt"+fieldid+"").val();
            var texdesc =   $("#txt"+fieldid+"").data("desc");
            
            var txtid= $('#hdn_mrsid').val();
            var txt_id2= $('#hdn_mrsid2').val();

            $('#'+txtid).val(texdesc);
            $('#'+txt_id2).val(txtval);

            //---------------------clear
            var clrfld1 = $('#'+txtid).parent().parent().find('[id*="txtrecordId"]').attr('id');
             $("#"+clrfld1).val('');

            var clrfld2 = $('#'+txtid).parent().parent().find('[id*="popupITEMID"]').attr('id');
             $("#"+clrfld2).val('');

            var clrfld3 = $('#'+txtid).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
             $("#"+clrfld3).val('');

            var clrfld4 = $('#'+txtid).parent().parent().find('[id*="ItemName"]').attr('id');
             $("#"+clrfld4).val('');

            var clrfld5 = $('#'+txtid).parent().parent().find('[id*="popupMUOM"]').attr('id');
             $("#"+clrfld5).val('');
            
            var clrfld6 = $('#'+txtid).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
             $("#"+clrfld6).val('');

            var clrfld7 = $('#'+txtid).parent().parent().find('[id*="Itemspec"]').attr('id');
             $("#"+clrfld7).val('');

            var clrfld8 = $('#'+txtid).parent().parent().find('[id*="txtSTOCK_QTY"]').attr('id');
             $("#"+clrfld8).val('');

            var clrfld9 = $('#'+txtid).parent().parent().find('[id*="BAL_MRSQTY"]').attr('id');
             $("#"+clrfld9).val('');

            var clrfld10 = $('#'+txtid).parent().parent().find('[id*="INDENT_QTY"]').attr('id');
             $("#"+clrfld10).val('');
           
            var clrfld11 = $('#'+txtid).parent().parent().find('[id*="EDA"]').attr('id');
             $("#"+clrfld11).val('');
           
            var clrfld12 = $('#'+txtid).parent().parent().find('[id*="APPROX_VAL"]').attr('id');
             $("#"+clrfld12).val('0.000');
            
            var clrfld13 = $('#'+txtid).parent().parent().find('[id*="REMARKS"]').attr('id');
             $("#"+clrfld13).val('');

              
              // $("#"+$(this).parent().parent().find('[id*=""]').attr('id')).val('');
              // $("#"+$(this).parent().parent().find('[id*=""]').attr('id')).val('');
              // $("#"+$(this).parent().parent().find('[id*=""]').attr('id')).val('');
              // $("#"+$(this).parent().parent().find('[id*=""]').attr('id')).val('');
              // $("#"+$(this).parent().parent().find('[id*=""]').attr('id')).val('');
              // $("#"+$(this).parent().parent().find('[id*=""]').attr('id')).val('');
              // $("#"+$(this).parent().parent().find('[id*=""]').attr('id')).val('');
              // $("#"+$(this).parent().parent().find('[id*=""]').attr('id')).val('');           
            //--------------------

            $("#MRSPopup").hide();
            
            $("#MRScodesearch").val(''); 
            $("#MRSnamesearch").val(''); 
          
            event.preventDefault();
          });
      }

  //Sales Order Dropdown Ends
//------------------------ 
//-----------so popup end
//------------------------
  //Item ID Dropdown



let itemtid = "#ItemIDTable2";
let itemtid2 = "#ItemIDTable";
let itemtidheaders = document.querySelectorAll(itemtid2 + " th");

itemtidheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(itemtid, ".clsitemid", "td:nth-child(" + (i + 1) + ")");
  });
});

function ItemCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Itemcodesearch");
  filter = input.value.toUpperCase();

  if(filter.length == 0 && $("#DIRECT_PI").prop("checked")==true)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3 && $("#DIRECT_PI").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = filter; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[1];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }       
    }
  }
}

function ItemNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Itemnamesearch");
  filter = input.value.toUpperCase();

  if(filter.length == 0 && $("#DIRECT_PI").prop("checked")==true ) 
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3 && $("#DIRECT_PI").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = filter; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[2];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }       
    }
  }
}

function ItemUOMFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemUOMsearch");
  filter = input.value.toUpperCase();  
  if(filter.length == 0 && $("#DIRECT_PI").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3 && $("#DIRECT_PI").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = filter; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[3];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }       
    }
  }
}

function ItemSpecFunction() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("ItemSpecsearch");
    filter = input.value.toUpperCase();
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[4];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }       
    }
  }

function StockQTYFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemStockQTYsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[5];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
        }
      }

      function ItemBalSoQtyFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemBalSoQtysearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[6];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }       
        }
      }
/*
function ItemGroupFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemGroupsearch");
  filter = input.value.toUpperCase();
  if(filter.length == 0 && $("#DIRECT_PI").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3 && $("#DIRECT_PI").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = filter; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[5];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }       
    }
  }
}
*/
/*
function ItemCategoryFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemCategorysearch");
  filter = input.value.toUpperCase();
  if(filter.length == 0 && $("#DIRECT_PI").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3 && $("#DIRECT_PI").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = filter; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[6];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }       
    }
  }
}
*/
function ItemBUFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemBUsearch");
filter = input.value.toUpperCase();
if(filter.length == 0 && $("#DIRECT_PI").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3 && $("#DIRECT_PI").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = filter; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[7];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }       
    }
  }
}

function ItemAPNFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemAPNsearch");
  filter = input.value.toUpperCase();
  if(filter.length == 0 && $("#DIRECT_PI").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3 && $("#DIRECT_PI").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = filter; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[8];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }       
    }
  }
}

function ItemCPNFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemCPNsearch");
  filter = input.value.toUpperCase();
  if(filter.length == 0 && $("#DIRECT_PI").prop("checked")==true)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3 && $("#DIRECT_PI").prop("checked")==true)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = filter; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART);
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[9];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }       
    }
  }
}

function ItemOEMPNFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemOEMPNsearch");
  filter = input.value.toUpperCase();
  if(filter.length == 0 && $("#DIRECT_PI").prop("checked")==true)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART);
  }
  else if(filter.length >= 3 && $("#DIRECT_PI").prop("checked")==true)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = filter; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART);
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[10];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }       
    }
  }
}


function loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART){

    
		$("#tbody_ItemID").html('');
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
      url:'{{route("transaction",[$FormId,"getMstItems"])}}',
			type:'POST',
			data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART,'STID_REF':$("#STID_REF").val()},
			success:function(data) {
			$("#tbody_ItemID").html(data); 
			bindItemEvents(); 
      $('.js-selectall').prop('disabled', true); 
      $('.js-selectall').prop("checked", false); 
			},
			error:function(data){
			console.log("Error: Something went wrong.");
			$("#tbody_ItemID").html('');                        
			},
		});

}

      

  $('#Material').on('click','[id*="popupITEMID"]',function(event){
    
                
                var mrs_id = $.trim($(this).parent().parent().find('[id*="HDNMrsID_"]').val());
                var store_id = $("#STID_REF").val();
                var DEPID_REF = $("#DEPID_REF").val();

                if(store_id==""){
                      $("#FocusId").val('txtstore_popup');
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").hide();
                      $("#OkBtn1").show();
                      $("#AlertMessage").text('Please select Store first.');
                      $("#alert").modal('show');
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;
                }
                else if(DEPID_REF==""){
                      $("#FocusId").val('txtdept_popup');
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").hide();
                      $("#OkBtn1").show();
                      $("#AlertMessage").text('Please select To Department.');
                      $("#alert").modal('show');
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;
                }
                else if(mrs_id=="" && $("#DIRECT_PI").prop("checked")==false){
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").hide();
                      $("#OkBtn1").show();
                      $("#AlertMessage").text('Please select MRS No.');
                      $("#alert").modal('show');
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;
                }

                


                if(mrs_id==""){

                    var CODE = ''; 
                    var NAME = ''; 
                    var MUOM = ''; 
                    var GROUP = ''; 
                    var CTGRY = ''; 
                    var BUNIT = ''; 
                    var APART = ''; 
                    var CPART = ''; 
                    var OPART = ''; 
                    var FORMID = "{{$FormId}}";
                    loadItem('',CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART);

                     
                }else{

                    $("#tbody_ItemID").html('');
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url:'{{route("transaction",[$FormId,"getItemDetails"])}}',
                        type:'POST',
                        data:{'mrsid':mrs_id,'storeid':store_id},
                        success:function(data) {
                          $("#tbody_ItemID").html(data);    
                          bindItemEvents(); 
                          $('.js-selectall').prop('disabled', false); 
                          $('.js-selectall').prop("checked", false);   
                                               
                        },
                        error:function(data){
                          console.log("Error: Something went wrong.");
                          $("#tbody_ItemID").html('');                        
                        },
                    }); 


                }


              
                
        

        $("#ITEMIDpopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="Itemspec"]').attr('id');
        var id5 = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
        var id6 = $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
        var id7 = $(this).parent().parent().find('[id*="txtSTOCK_QTY"]').attr('id');
        var id8 = $(this).parent().parent().find('[id*="BAL_MRSQTY"]').attr('id');
        var id9 = $(this).parent().parent().find('[id*="txtrecordId"]').attr('id');
        
        



        $('#hdn_ItemID').val(id);
        $('#hdn_ItemID2').val(id2);
        $('#hdn_ItemID3').val(id3);
        $('#hdn_ItemID4').val(id4);
        $('#hdn_ItemID5').val(id5);
        $('#hdn_ItemID6').val(id6);
        $('#hdn_ItemID7').val(id7);
        $('#hdn_ItemID8').val(id8);
        $('#hdn_ItemID9').val(id9);
      

        event.preventDefault();
  });

  $("#ITEMID_closePopup").click(function(event){
    $("#ITEMIDpopup").hide();
  });

    function bindItemEvents(){

      $('#ItemIDTable2').off(); 

      // Multiple Selection

      $('.js-selectall').change(function(){
        var isChecked = $(this).prop("checked");
        var selector = $(this).data('target');
        $(selector).prop("checked", isChecked);
        
        $('#ItemIDTable2').find('.clsitemid').each(function(){

          var fieldid = $(this).attr('id');
          var txtval =   $("#txt"+fieldid+"").val();
          var texdesc =  $("#txt"+fieldid+"").data("desc");

          var recordid = $(this).find('[id*="recordId"]').attr('id');
          var txtrecordidval =  $("#"+recordid+"").val();

          var icodeid = $(this).find('[id*="itemcode"]').attr('id');
          var txticodeval =  $("#txt"+icodeid+"").val();


          var fieldid2 = $(this).find('[id*="itemname"]').attr('id');
          var txtname =  $("#txt"+fieldid2+"").val();
        

          
          var fieldid3 = $(this).find('[id*="itemuom"]').attr('id');
          var txtmuomid =  $("#txt"+fieldid3+"").val();
          var apartno =  $("#txt"+fieldid3+"").data("desc2");
          var cpartno =  $("#txt"+fieldid3+"").data("desc3");
          var opartno =  $("#txt"+fieldid3+"").data("desc4");
          var txtmuom =  $(this).find('[id*="itemuom"]').text();


          var fieldid4 = $(this).find('[id*="itemspec"]').attr('id');
          var txtspec =  $("#txt"+fieldid4+"").val();

          var stockqtyid = $(this).find('[id*="stockqty"]').attr('id');
          var txtstockqtyval =  $("#txt"+stockqtyid+"").val();

          var balmrsqtyid = $(this).find('[id*="balmrsqty"]').attr('id');
          var txtbalmrsqtyval =  $("#txt"+balmrsqtyid+"").val();


       
        
        if($(this).find('[id*="chkId"]').is(":checked") == true) 
       {
            $('#example2').find('.participantRow').each(function()
            {
              var itemid = $(this).find('[id*="txtrecordId"]').val();
              if(txtrecordidval)
              {
                    if(txtrecordidval == itemid)
                    {
                        $("#ITEMIDpopup").hide();
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Item already exists.');
                        $("#alert").modal('show');
                        $("#OkBtn1").focus();
                        highlighFocusBtn('activeOk1');
                        $('#hdn_ItemID').val('');
                        $('#hdn_ItemID2').val('');
                        $('#hdn_ItemID3').val('');
                        $('#hdn_ItemID4').val('');
                        $('#hdn_ItemID5').val('');
                        $('#hdn_ItemID6').val('');
                        $('#hdn_ItemID7').val('');
                        $('#hdn_ItemID8').val('');
                        $('#hdn_ItemID9').val('');
                       
                        txtval = '';
                        texdesc = '';
                        txtname = '';
                        txtspec = '';
                        txtmuom = '';
                        stockqtyid = '';
                        txtstockqtyval = '';
                        balmrsqtyid = '';
                        txtbalmrsqtyval='';

                        recordid='';
                        txtrecordidval='';
                      
                      
                        return false;
                    }               
              }          
            });  

            if($('#hdn_ItemID').val() == "" && txtrecordidval != '')
            {
                var txtid= $('#hdn_ItemID').val();
                var txt_id2= $('#hdn_ItemID2').val();
                var txt_id3= $('#hdn_ItemID3').val();
                var txt_id4= $('#hdn_ItemID4').val();
                var txt_id5= $('#hdn_ItemID5').val();
                var txt_id6= $('#hdn_ItemID6').val();
                var txt_id7= $('#hdn_ItemID7').val();
                var txt_id8= $('#hdn_ItemID8').val();
                var txt_id9= $('#hdn_ItemID9').val();
                 var txt_id10= $('#hdn_ItemID10').val();
               

                var $tr = $('.material').closest('table');
                var allTrs = $tr.find('.participantRow').last();
                var lastTr = allTrs[allTrs.length-1];
                var $clone = $(lastTr).clone();

                $clone.find('td').each(function(){
                  var el = $(this).find(':first-child');
                  var id = el.attr('id') || null;
                    if(id){
                        var idLength = id.split('_').pop();
                        var i = id.substr(id.length-idLength.length);
                        var prefix = id.substr(0, (id.length-idLength.length));
                        el.attr('id', prefix+(+i+1));
                    }
                    var name = el.attr('name') || null;
                  if(name){
                    var nameLength = name.split('_').pop();
                    var i = name.substr(name.length-nameLength.length);
                    var prefix1 = name.substr(0, (name.length-nameLength.length));
                    el.attr('name', prefix1+(+i+1));
                  }
                });

                $clone.find('.remove').removeAttr('disabled'); 

                $clone.find('[id*="recordId"]').val(txtrecordidval);               
                $clone.find('[id*="popupITEMID"]').val(texdesc);
                $clone.find('[id*="ITEMID_REF"]').val(txtval);
                $clone.find('[id*="ItemName"]').val(txtname);
                $clone.find('[id*="Itemspec"]').val(txtspec);  
                $clone.find('[id*="Alpspartno"]').val(apartno);
                $clone.find('[id*="Custpartno"]').val(cpartno);
                $clone.find('[id*="OEMpartno"]').val(opartno);
                $clone.find('[id*="popupMUOM"]').val(txtmuom);
                $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
                $clone.find('[id*="STOCK_QTY"]').val(txtstockqtyval);
                $clone.find('[id*="BAL_MRSQTY"]').val(txtbalmrsqtyval);
               
                
                $tr.closest('table').append($clone);   
                var rowCount = $('#Row_Count1').val();
                  rowCount = parseInt(rowCount)+1;
                  $('#Row_Count1').val(rowCount);
                
                event.preventDefault();
            }
            else
            {
                var txtid= $('#hdn_ItemID').val();
                var txt_id2= $('#hdn_ItemID2').val();
                var txt_id3= $('#hdn_ItemID3').val();
                var txt_id4= $('#hdn_ItemID4').val();
                var txt_id5= $('#hdn_ItemID5').val();
                var txt_id6= $('#hdn_ItemID6').val();
                var txt_id7= $('#hdn_ItemID7').val();
                var txt_id8= $('#hdn_ItemID8').val();
                var txt_id9= $('#hdn_ItemID9').val();
               
              
                if($.trim(txtid)!=""){
                  $('#'+txtid).val(texdesc);
                }
                if($.trim(txt_id2)!=""){
                  $('#'+txt_id2).val(txtval);
                }
                if($.trim(txt_id3)!=""){
                  $('#'+txt_id3).val(txtname);
                }
                if($.trim(txt_id4)!=""){
                  $('#'+txt_id4).val(txtspec);
                }
                if($.trim(txt_id5)!=""){
                  $('#'+txt_id5).val(txtmuom);
                }
                
                if($.trim(txt_id6)!=""){
                  $('#'+txt_id6).val(txtmuomid);
                }
                
                if($.trim(txt_id7)!=""){
                  $('#'+txt_id7).val(txtstockqtyval);
                }
                if($.trim(txt_id8)!=""){
                  $('#'+txt_id8).val(txtbalmrsqtyval);
                }
                
                if($.trim(txt_id9)!=""){
                  $('#'+txt_id9).val(txtrecordidval);
                }
                $('#'+txtid).parent().parent().find('[id*="Alpspartno"]').val(apartno);
                $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
                $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);

                $('#hdn_ItemID').val('');
                $('#hdn_ItemID2').val('');
                $('#hdn_ItemID3').val('');
                $('#hdn_ItemID4').val('');
                $('#hdn_ItemID5').val('');
                $('#hdn_ItemID6').val('');
                $('#hdn_ItemID7').val('');
                $('#hdn_ItemID8').val('');
                $('#hdn_ItemID9').val('');
               
                     
            }
            // $("#ITEMIDpopup").hide();
            // event.preventDefault();
       }
       
        $("#Itemcodesearch").val(''); 
        $("#Itemnamesearch").val(''); 
        $("#ItemUOMsearch").val(''); 
        $("#ItemSpecsearch").val(''); 
        $('.remove').removeAttr('disabled'); 
       
        event.preventDefault();
        });

        $('.js-selectall').prop("checked", false);   
        $("#ITEMIDpopup").hide();
        
      });

      


      $('[id*="chkId"]').change(function(){
    
        var fieldid = $(this).parent().parent().attr('id');
        var txtval =   $("#txt"+fieldid+"").val();
        var texdesc =  $("#txt"+fieldid+"").data("desc");

       
        var recordid = $(this).parent().children('[id*="recordId"]').attr('id');
        var txtrecordidval =  $("#"+recordid+"").val();

        var icodeid = $(this).parent().children('[id*="itemcode"]').attr('id');
        var txticodeval =  $("#txt"+icodeid+"").val();


        var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
        var txtname =  $("#txt"+fieldid2+"").val();
      

        
        var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
        var txtmuomid =  $("#txt"+fieldid3+"").val();
        var apartno =  $("#txt"+fieldid3+"").data("desc2");
        var cpartno =  $("#txt"+fieldid3+"").data("desc3");
        var opartno =  $("#txt"+fieldid3+"").data("desc4");
      
        var txtmuom =  $(this).parent().parent().children('[id*="itemuom"]').text();


        var fieldid4 = $(this).parent().parent().children('[id*="itemspec"]').attr('id');
        var txtspec =  $("#txt"+fieldid4+"").val();

        var stockqtyid = $(this).parent().parent().children('[id*="stockqty"]').attr('id');
        var txtstockqtyval =  $("#txt"+stockqtyid+"").val();

        var balmrsqtyid = $(this).parent().parent().children('[id*="balmrsqty"]').attr('id');
        var txtbalmrsqtyval =  $("#txt"+balmrsqtyid+"").val();


       
        
        
       if($(this).is(":checked") == true) 
       {
            $('#example2').find('.participantRow').each(function()
            {
              var itemid = $(this).find('[id*="txtrecordId"]').val();
              if(txtrecordidval)
              {
                    if(txtrecordidval == itemid)
                    {
                        $("#ITEMIDpopup").hide();
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Item already exists.');
                        $("#alert").modal('show');
                        $("#OkBtn1").focus();
                        highlighFocusBtn('activeOk1');
                        $('#hdn_ItemID').val('');
                        $('#hdn_ItemID2').val('');
                        $('#hdn_ItemID3').val('');
                        $('#hdn_ItemID4').val('');
                        $('#hdn_ItemID5').val('');
                        $('#hdn_ItemID6').val('');
                        $('#hdn_ItemID7').val('');
                        $('#hdn_ItemID8').val('');
                        $('#hdn_ItemID9').val('');
                       
                        txtval = '';
                        texdesc = '';
                        txtname = '';
                        txtspec = '';
                        txtmuom = '';
                        stockqtyid = '';
                        txtstockqtyval = '';
                        balmrsqtyid = '';
                        txtbalmrsqtyval='';

                        recordid='';
                        txtrecordidval='';
                      
                      
                        return false;
                    }               
              }          
            });  

            if($('#hdn_ItemID').val() == "" && txtrecordidval != '')
            {
                var txtid= $('#hdn_ItemID').val();
                var txt_id2= $('#hdn_ItemID2').val();
                var txt_id3= $('#hdn_ItemID3').val();
                var txt_id4= $('#hdn_ItemID4').val();
                var txt_id5= $('#hdn_ItemID5').val();
                var txt_id6= $('#hdn_ItemID6').val();
                var txt_id7= $('#hdn_ItemID7').val();
                var txt_id8= $('#hdn_ItemID8').val();
                var txt_id9= $('#hdn_ItemID9').val();
                 var txt_id10= $('#hdn_ItemID10').val();
               

                var $tr = $('.material').closest('table');
                var allTrs = $tr.find('.participantRow').last();
                var lastTr = allTrs[allTrs.length-1];
                var $clone = $(lastTr).clone();

                $clone.find('td').each(function(){
                  var el = $(this).find(':first-child');
                  var id = el.attr('id') || null;
                    if(id){
                        var idLength = id.split('_').pop();
                        var i = id.substr(id.length-idLength.length);
                        var prefix = id.substr(0, (id.length-idLength.length));
                        el.attr('id', prefix+(+i+1));
                    }
                    var name = el.attr('name') || null;
                  if(name){
                    var nameLength = name.split('_').pop();
                    var i = name.substr(name.length-nameLength.length);
                    var prefix1 = name.substr(0, (name.length-nameLength.length));
                    el.attr('name', prefix1+(+i+1));
                  }
                });

                $clone.find('.remove').removeAttr('disabled'); 

                $clone.find('[id*="recordId"]').val(txtrecordidval);               
                $clone.find('[id*="popupITEMID"]').val(texdesc);
                $clone.find('[id*="ITEMID_REF"]').val(txtval);
                $clone.find('[id*="ItemName"]').val(txtname);
                $clone.find('[id*="Itemspec"]').val(txtspec);  
                $clone.find('[id*="Alpspartno"]').val(apartno);
                $clone.find('[id*="Custpartno"]').val(cpartno);
                $clone.find('[id*="OEMpartno"]').val(opartno);
                $clone.find('[id*="popupMUOM"]').val(txtmuom);
                $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
                $clone.find('[id*="STOCK_QTY"]').val(txtstockqtyval);
                $clone.find('[id*="BAL_MRSQTY"]').val(txtbalmrsqtyval);
               
                
                $tr.closest('table').append($clone);   
                var rowCount = $('#Row_Count1').val();
                  rowCount = parseInt(rowCount)+1;
                  $('#Row_Count1').val(rowCount);
                
                event.preventDefault();
            }
            else
            {
                var txtid= $('#hdn_ItemID').val();
                var txt_id2= $('#hdn_ItemID2').val();
                var txt_id3= $('#hdn_ItemID3').val();
                var txt_id4= $('#hdn_ItemID4').val();
                var txt_id5= $('#hdn_ItemID5').val();
                var txt_id6= $('#hdn_ItemID6').val();
                var txt_id7= $('#hdn_ItemID7').val();
                var txt_id8= $('#hdn_ItemID8').val();
                var txt_id9= $('#hdn_ItemID9').val();
               
              
                if($.trim(txtid)!=""){
                  $('#'+txtid).val(texdesc);
                }
                if($.trim(txt_id2)!=""){
                  $('#'+txt_id2).val(txtval);
                }
                if($.trim(txt_id3)!=""){
                  $('#'+txt_id3).val(txtname);
                }
                if($.trim(txt_id4)!=""){
                  $('#'+txt_id4).val(txtspec);
                }
                if($.trim(txt_id5)!=""){
                  $('#'+txt_id5).val(txtmuom);
                }
                
                if($.trim(txt_id6)!=""){
                  $('#'+txt_id6).val(txtmuomid);
                }
                
                if($.trim(txt_id7)!=""){
                  $('#'+txt_id7).val(txtstockqtyval);
                }
                if($.trim(txt_id8)!=""){
                  $('#'+txt_id8).val(txtbalmrsqtyval);
                }
               
                
                if($.trim(txt_id9)!=""){
                  $('#'+txt_id9).val(txtrecordidval);
                }
                $('#'+txtid).parent().parent().find('[id*="Alpspartno"]').val(apartno);
                $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
                $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);

                $('#hdn_ItemID').val('');
                $('#hdn_ItemID2').val('');
                $('#hdn_ItemID3').val('');
                $('#hdn_ItemID4').val('');
                $('#hdn_ItemID5').val('');
                $('#hdn_ItemID6').val('');
                $('#hdn_ItemID7').val('');
                $('#hdn_ItemID8').val('');
                $('#hdn_ItemID9').val('');
               
                     
            }
            // $("#ITEMIDpopup").hide();
            // event.preventDefault();
       }
       else if($(this).is(":checked") == false) 
       {
         var id = txtrecordidval;
         var r_count = $('#Row_Count1').val();
         $('#example2').find('.participantRow').each(function()
         {
           var itemid = $(this).find('[id*="txtrecordId"]').val();
           if(id == itemid)
           {
              var rowCount = $('#Row_Count1').val();
              if (rowCount > 1) {
                $(this).closest('.participantRow').remove(); 
                rowCount = parseInt(rowCount)-1;
              $('#Row_Count1').val(rowCount);
              }
              else 
              {
                $(document).find('.dmaterial').prop('disabled', true);  
                $("#ITEMIDpopup").hide();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('There is only 1 row. So cannot be remove.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                return false;

              }
                event.preventDefault(); 
           }
        });
       }
        $("#Itemcodesearch").val(''); 
        $("#Itemnamesearch").val(''); 
        $("#ItemUOMsearch").val(''); 
        $("#ItemSpecsearch").val(''); 
        $('.remove').removeAttr('disabled'); 
      
        event.preventDefault();
      });
    }


  //Item ID Dropdown Ends
//------------------------

  var formItemMst = $( "#form_data" );
  formItemMst.validate();
  

$(document).ready(function(e) {


  var Material = $("#Material").html(); 
  $('#hdnmaterial').val(Material);

  var lastPI_DT = <?php echo json_encode($objlastPIDT[0]->PI_DT); ?>;
  var today = new Date(); 
  var docdate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
  $('#PI_DT').attr('min',lastPI_DT);
  $('#PI_DT').attr('max',docdate);

  $('#PI_DT').val(docdate);

 $('[id*="EDA"]').attr('min',lastPI_DT);


  $('#EDA_0').val(docdate);
  $("#Row_Count1").val(1);


  $('#PI_DT').change(function() {
    var mindate  = $(this).val();
    $('[id*="EDA"]').attr('min',mindate);
});  


  
 
      $('#INDENT_QTY').focusout(function(e){
        var cvalue = $(this).val();
        if(intRegex.test(cvalue)){
          cvalue = cvalue +'.000';
            }
        $(this).val(cvalue);
      });

      $('#APPROX_VAL').focusout(function(e){
        var cvalue = $(this).val();
        if(intRegex.test(cvalue)){
          cvalue = cvalue +'.000';
            }
        $(this).val(cvalue);
      });

  


});


$('#Material').on('focusout',"[id*='INDENT_QTY']",function()
{
  if(intRegex.test($(this).val())){
    $(this).val($(this).val()+'.000')
    }
    
    event.preventDefault();
});


$('#Material').on('focusout',"[id*='APPROX_VAL']",function()
{
  if(intRegex.test($(this).val())){
    $(this).val($(this).val()+'.000')
    }

    $my_val = $(this).val();

    if(parseFloat($my_val)<parseFloat(0.000)){
            $(this).val('');
            $("#alert").modal('show');
            $("#AlertMessage").text('Invaild Approx Value. Please enter again.');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            $("#OkBtn").hide();
            highlighFocusBtn('activeOk1');
    }
    event.preventDefault();
});

$('#Material').on('keyup', '.three-digits', function() {
    if ($(this).val().indexOf('.') != -1) {
        if ($(this).val().split(".")[1].length > 3) {
            $(this).val('');
            $("#alert").modal('show');
            $("#AlertMessage").text('Enter value till three decimal only.');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            $("#OkBtn").hide();
            highlighFocusBtn('activeOk1');
        }
    }
    return this; //for chaining
});

$('#Material').on('blur', '.three-digits', function() {
    if ($(this).val().indexOf('.') != -1) {
        if ($(this).val().split(".")[1].length > 3) {
            $(this).val('');
            $("#alert").modal('show');
            $("#AlertMessage").text('Enter value till three decimal only.');
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            $("#OkBtn").hide();
            highlighFocusBtn('activeOk1');
        }
    }
    return this; //for chaining
});

$('#btnAdd').on('click', function() {
  var viewURL = '{{route("transaction",[$FormId,"add"])}}';
              window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
});

$('#PI_DT').change(function() {
    var mindate  = $(this).val();
    $('[id*="EDD"]').attr('min',mindate);
});   



$("#btnUndo").on("click", function() {
    $("#AlertMessage").text("Do you want to erase entered information in this record?");
    $("#alert").modal('show');

    $("#YesBtn").data("funcname","fnUndoYes");
    $("#YesBtn").show();

    $("#NoBtn").data("funcname","fnUndoNo");
    $("#NoBtn").show();
    
    $("#OkBtn").hide();
    $("#NoBtn").focus();
    event.preventDefault();
});

    

window.fnUndoYes = function (){
    //reload form
  window.location.reload();
}//fnUndoYes


window.fnUndoNo = function (){
  
}//fnUndoNo

$("#btnSaveFormData").click(function() {
    
  if(formItemMst.valid()){

          event.preventDefault();

          var today = new Date(); 

              $("#FocusId").val('');

              var PI_NO          =  $.trim($("#PI_NO").val());
              var PI_DT          =   $.trim($("#PI_DT").val());
              var STID_REF       =   $.trim($("#STID_REF").val());
              var DEPID_REF       =   $.trim($("#DEPID_REF").val());
              var HDNMrsID       =   $.trim($("#HDNMrsID").val());
             
              if(PI_NO ===""){
                  $("#FocusId").val('PI_NO');
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text('Please enter value in PI No.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
              }
              else if(PI_DT ===""){
                  $("#FocusId").val('PI_DT');
                  $("#PI_DT").val(today);  
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text('Please select PI Date.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;

              }else if(STID_REF ===""){
                  $("#FocusId").val('txtstore_popup');
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text('Please Select From Store.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk1');
                  return false;

              }else if(DEPID_REF ===""){
                $("#FocusId").val('txtdept_popup');
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text('Please Select To Department.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk1');
                  return false;
              } 
      
              var allblank1 = [];  
              var allblank2 = [];  
              var allblank3 = [];  
              var allblank4 = [];  
              var allblank5 = [];  
              var allblank6 = [];  
              var allblank7 = [];  
              var allblank8 = [];  
              var allblank9 = [];  
              var allblank10 = [];  

              var focustext1= "";
              var focustext2= "";
              var focustext3= "";
              var focustext4= "";
              var focustext5= "";
              var focustext6= "";
              var focustext7= "";
              var focustext8= "";
              var focustext9= "";
              var focustext10= "";
              var focustext11= "";
              var focustext12= "";
              

              if(!$("#DIRECT_PI").prop("checked")) {
                $("[id*=HDNMrsID]").each(function(){
                  var strid = $(this).attr("id")
                  if (strid.toLowerCase().indexOf("error") == -1){
                    if( $.trim( $(this).val()) == "" )
                    {
                        allblank1.push('true');
                        focustext1 = $(this).parent().parent().find("[id*=txtMRS_popup]").attr('id');
                    }
                    else
                    {
                      allblank1.push('false');
                      
                    }
                  }
                });

              }  

              $("[id*=ITEMID_REF]").each(function(){
                var strid = $(this).attr("id")
                if (strid.toLowerCase().indexOf("error") == -1){
                  if( $.trim( $(this).val()) == "" )
                  {
                      allblank2.push('true');
                      focustext2 = $(this).parent().parent().find("[id*=popupITEMID]").attr('id');
                  }else
                  {
                    allblank2.push('false');
                    
                  }
                }
              });


              $("[id*=INDENT_QTY]").each(function(){
                var strid = $(this).attr("id");
                if (strid.toLowerCase().indexOf("error") == -1){

                    if( $.trim( $(this).val()) == "" )
                    {
                        allblank3.push('true');
                        focustext3 = $(this).attr("id");
                    }else
                    {
                      allblank3.push('false');
                      
                    }

                    if( $.trim($(this).val()) != "" && !$.isNumeric($(this).val()) ){
                      allblank4.push('true');
                      focustext4 = $(this).attr("id");
                    }else
                    {
                      allblank4.push('false');
                      
                    }

                    if( $.trim($(this).val()) != "" && $.isNumeric($(this).val()) ){
                      if($(this).val()<0.000){
                        allblank5.push('true');
                        focustext5 = $(this).attr("id");
                      }else
                      {
                        allblank5.push('false');
                        
                      }

                      var mrsfield_val = $.trim($(this).parent().parent().find('[id*="HDNMrsID_"]').val());
                      if(mrsfield_val!=""){

                        var balmrsqty = $.trim($(this).parent().parent().find('[id*="BAL_MRSQTY_"]').val())
                        var indqty = parseFloat($(this).val());

                        if( indqty > parseFloat(balmrsqty) ){
                          allblank10.push('true');
                          focustext10 = $(this).attr("id");
                        }else{
                          allblank10.push('false');
                          
                        }

                      }

                    }


                }
                 
              });

              //-----
              $("[id*=APPROX_VAL]").each(function(){
                var strid = $(this).attr("id");
                if (strid.toLowerCase().indexOf("error") == -1){

                    if( $.trim( $(this).val()) == "" )
                    {
                        allblank6.push('true');
                        focustext6 = $(this).attr("id");
                    }else
                    {
                      allblank6.push('false');  
                    }

                    if( $.trim($(this).val()) != "" && !$.isNumeric($(this).val()) ){
                      allblank7.push('true');
                      focustext7 = $(this).attr("id");
                    }else
                    {
                      allblank7.push('false');
                      
                    }

                    if( $.trim($(this).val()) != "" && $.isNumeric($(this).val()) ){
                      if($(this).val()<0.000){
                        allblank8.push('true');
                        focustext8 = $(this).attr("id");
                      }else
                      {
                        allblank8.push('false');
                        
                      }
                    }


                }
                 
              });

           
              $("[id*=EDA]").each(function(){
                var strid = $(this).attr("id")
                if (strid.toLowerCase().indexOf("error") == -1){

                    if( $.trim( $(this).val()) == "" )
                    {
                        allblank9.push('true');
                        focustext9 = $(this).attr("id");
                    }else
                    {
                      allblank9.push('false');
                      
                    }

                }
                 
              });

              if(jQuery.inArray("true", allblank1) !== -1){
                    
                    $("#FocusId").val(focustext1);
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Please select MRS No in Material Tab.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;
                
                }

               else if(jQuery.inArray("true", allblank2) !== -1){
                $("#FocusId").val(focustext2);
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Please select Item in Material Tab.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;
                
                }else if(jQuery.inArray("true", allblank3) !== -1){
                  $("#FocusId").val(focustext3);
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Please enter valid Indent Qty in Material Tab.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;

                }else if(jQuery.inArray("true", allblank4) !== -1){
                  $("#FocusId").val(focustext4);
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Please enter valid Indent Qty in Material Tab.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;

                }else if(jQuery.inArray("true", allblank5) !== -1){
                  $("#FocusId").val(focustext5);
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Indent Qty value must be greater than 0.000 in Material Tab.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;

                }
                else if(jQuery.inArray("true", allblank8) !== -1){
                  $("#FocusId").val(focustext8);
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Approx Value Qty value must be greater than 0.000 in Material Tab.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;

                }else if(jQuery.inArray("true", allblank10) !== -1){
                  $("#FocusId").val(focustext10);
                    $("#alert").modal('show');
                    $("#AlertMessage").text('MRS Indent Qty value must be less than or equal to Balance MRS Qty in Material Tab.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;

                }else if(jQuery.inArray("true", allblank9) !== -1){
                  $("#FocusId").val(focustext9);
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Please enter the value of EDA in Material Tab.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;
                  
                }
                else if(checkPeriodClosing('{{$FormId}}',$("#PI_DT").val(),0) ==0){
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text(period_closing_msg);
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                }
                else{
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Do you want to save to Record.');
                    $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                    $("#OkBtn1").hide();
                    $("#OkBtn").hide();
                    $("#YesBtn").show();
                    $("#NoBtn").show();
                    $("#YesBtn").focus();
                    highlighFocusBtn('activeYes');
                }   
          //return false;
    }        
//----------------------------
});//btnSaveFormData

    


$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName = $("#YesBtn").data("funcname");
      window[customFnName]();

}); //yes button

window.fnSaveData = function (){

      //validate and save data
      event.preventDefault();

          var trnseForm = $("#form_data");
          var formData = trnseForm.serialize();
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $("#btnSaveFormData").hide(); 
      $(".buttonload").show(); 
      $("#btnApprove").prop("disabled", true);
      $.ajax({
          url:'{{ route("transaction",[$FormId,"save"])}}',
          type:'POST',
          data:formData,
          success:function(data) {
            $(".buttonload").hide(); 
            $("#btnSaveFormData").show();   
            $("#btnApprove").prop("disabled", false);
                    
                    if(data.errors) {
                        $(".text-danger").hide();

                        console.log("error MSG="+data.msg);
                        if(data.resp=='duplicate') {
                          $("#YesBtn").hide();
                          $("#NoBtn").hide();
                          $("#OkBtn").hide();
                          $("#OkBtn1").show();
                          $("#AlertMessage").text(data.msg);
                          $("#alert").modal('show');
                          $("#OkBtn1").focus();
                          highlighFocusBtn('activeOk1');
                          return false;

                        }

                        if(data.save=='invalid') {
                          $("#YesBtn").hide();
                          $("#NoBtn").hide();
                          $("#OkBtn").hide();
                          $("#OkBtn1").show();
                          $("#AlertMessage").text(data.msg);
                          $("#alert").modal('show');
                          $("#OkBtn").focus();
                          return false;
                        }

                        if(data.form=='invalid') {

                          $("#YesBtn").hide();
                          $("#NoBtn").hide();
                          $("#OkBtn").hide();
                          $("#OkBtn1").show();
                          $("#AlertMessage").text("Invalid form data please required fields.");
                          $("#alert").modal('show');
                          $("#OkBtn").focus();
                          return false;
                        }

                    }
                    
                    if(data.success) {                   
                        $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn1").hide();
                      $("#OkBtn").show();  
                      highlighFocusBtn('activeOk');
                      
                      $("#AlertMessage").text(data.msg);
                      $("#alert").modal('show');

                      $("#OkBtn").focus();
                      $(".text-danger").hide();
                    }
                    
                },
                error:function(data){
                  $(".buttonload").hide(); 
                  $("#btnSaveFormData").show();   
                  $("#btnApprove").prop("disabled", false);
                  console.log("Error: Something went wrong.");
                },
      });
}

//no button
$("#NoBtn").click(function(){
    $("#alert").modal('hide');
    $("#LABEL").focus();
});

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("transaction",[$FormId,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
    // $("[id*=txtlabel]").focus();
    $(".text-danger").hide();
});

//
function showError(pId,pVal){
    $("#"+pId+"").text(pVal);
    $("#"+pId+"").show();
}
function getFocus(){
    var FocusId=$("#FocusId").val();
    $("#"+FocusId).focus();
    $("#closePopup").click();
}
function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }


    $('#table3').on('blur','[id*="IDMRP"]',function(event){
  if(intRegex.test($(this).val())){
    $(this).val($(this).val() + '.00000') ;
  }
});

          

$("#Material").on('click', '.add', function() {
    
    var $tr = $(this).closest('table');
    var allTrs = $tr.find('.participantRow').last();
    var lastTr = allTrs[allTrs.length-1];
    var $clone = $(lastTr).clone();

    $clone.find('td').each(function(){
      var el = $(this).find(':first-child');
      var id = el.attr('id') || null;
        if(id){
            var idLength = id.split('_').pop();
            var i = id.substr(id.length-idLength.length);
            var prefix = id.substr(0, (id.length-idLength.length));
            el.attr('id', prefix+(+i+1));
        }
        var name = el.attr('name') || null;
      if(name){
        var nameLength = name.split('_').pop();
        var i = name.substr(name.length-nameLength.length);
        var prefix1 = name.substr(0, (name.length-nameLength.length));
        el.attr('name', prefix1+(+i+1));
      }
    });

    $clone.find('input:text').val('');
    $clone.find('input:hidden').val('');
    $clone.find('.remove').removeAttr('disabled'); 


    $clone.find('[id*="APPROX_VAL"]').val('0.000');

    $clone.find('input:checkbox').prop('checked',false);;

    $tr.closest('table').append($clone);   
    var rowCount = $('#Row_Count1').val();
      rowCount = parseInt(rowCount)+1;
      $('#Row_Count1').val(rowCount);
      event.preventDefault();
}); 

$("#Material").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('.participantRow').length;
    
    if (rowCount > 1) {
      $(this).closest('.participantRow').remove();
      rowCount = parseInt(rowCount)-1;
          $('#Row_Count1').val(rowCount);
    }
    if (rowCount <= 1) {
      $(document).find('.remove').prop('disabled', true);
    }
    event.preventDefault();
});

//popup library 

"use strict";
	var w3 = {};
  w3.getElements = function (id) {
    if (typeof id == "object") {
      return [id];
    } else {
      return document.querySelectorAll(id);
    }
  };
	w3.sortHTML = function(id, sel, sortvalue) {
    var a, b, i, ii, y, bytt, v1, v2, cc, j;
    a = w3.getElements(id);
    for (i = 0; i < a.length; i++) {
      for (j = 0; j < 2; j++) {
        cc = 0;
        y = 1;
        while (y == 1) {
          y = 0;
          b = a[i].querySelectorAll(sel);
          for (ii = 0; ii < (b.length - 1); ii++) {
            bytt = 0;
            if (sortvalue) {
              v1 = b[ii].querySelector(sortvalue).innerText;
              v2 = b[ii + 1].querySelector(sortvalue).innerText;
            } else {
              v1 = b[ii].innerText;
              v2 = b[ii + 1].innerText;
            }
            v1 = v1.toLowerCase();
            v2 = v2.toLowerCase();
            if ((j == 0 && (v1 > v2)) || (j == 1 && (v1 < v2))) {
              bytt = 1;
              break;
            }
          }
          if (bytt == 1) {
            b[ii].parentNode.insertBefore(b[ii + 1], b[ii]);
            y = 1;
            cc++;
          }
        }
        if (cc > 0) {break;}
      }
    }
  };
       

let tidp1 = '';
let tidp2 = '';
let clsname = '';          
let p_headers = '';
function doSorting(ptable1,ptable2,pclass){


     tidp1 = "#"+ptable1;
     tidp2 = "#"+ptable2;
     clsname = "."+pclass;          
     p_headers = document.querySelectorAll(tidp1 + " th");

    // Sort the table element when clicking on the table headers
    p_headers.forEach(function(element, i) {
      element.addEventListener("click", function() {
        w3.sortHTML(tidp2, clsname, "td:nth-child(" + (i + 1) + ")");
      });
    });

}

let input, filter, table, tr, td, i, txtValue;
function colSearch(ptable,ptxtbox,pcolindex) {
  //var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById(ptxtbox);
  filter = input.value.toUpperCase();
  table = document.getElementById(ptable);
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[pcolindex];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
function colSearchClear(ptable1,pclsname) {
  //clear text box value
  $('#'+ptable1+' input[type="text"]').each(function () {
      $(this).val('');
   });
  
  //clear row 
  $('.'+pclsname).each(function () {
      $(this).removeAttr("style");
   });
}
//popup library end

  $('input[type=checkbox][name=DIRECT_PI]').change(function() {
      if($("#DIRECT_PI").prop("checked")) {
          $("#DIRECT_PI").val('1');
          $("#Material").html($('#hdnmaterial').val());
          $('.CLS_MRS').attr('disabled', 'disabled');
		  
      }
      else {
        $("#DIRECT_PI").val('0');
        $("#Material").html($('#hdnmaterial').val());
        $('.CLS_MRS').removeAttr('disabled');
		
      }      
  });


function showSelectedCheck(hidden_value,selectAll){

  var divid ="";

  if(hidden_value !=""){

      var all_location_id = document.querySelectorAll('input[name="'+selectAll+'[]"]');
      
      for(var x = 0, l = all_location_id.length; x < l;  x++){
      
          var checkid=all_location_id[x].id;
          var checkval=all_location_id[x].value;
      
          if(hidden_value == checkval){
          divid = checkid;
          }

          $("#"+checkid).prop('checked', false);
          
      }
  }

  if(divid !=""){
      $("#"+divid).prop('checked', true);
  }
}
</script>

@endpush