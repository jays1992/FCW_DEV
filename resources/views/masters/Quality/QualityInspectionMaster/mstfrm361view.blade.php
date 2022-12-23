
@extends('layouts.app')
@section('content')
<!-- <form id="frm_trn_so" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >     -->

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Quality Inspection Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSO"  disabled="disabled" ><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove"  disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div>
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->
    <div class="container-fluid purchase-order-view filter">     
  <form id="frm_mst_view" method="POST"  > 
    @CSRF
   
    <div class="inner-form">
      <div class="row">
        <div class="col-lg-2 pl"><p>QIC No</p></div>
        <div class="col-lg-2 pl">
          <input disabled type="text" name="QICNO" id="QICNO" value="{{ isset($HDR->QICNO)?$HDR->QICNO:''}}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase">
        </div>
        
        <div class="col-lg-1 pl"><p>QIC Date</p></div>
        <div class="col-lg-2 pl">
          <input disabled type="date" name="QICDT" id="QICDT" value="{{ isset($HDR->QICDT)?$HDR->QICDT:''}}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
        </div>
        
        <div class="col-lg-1 pl"><p>Item</p></div>
        <div class="col-lg-2 pl">
          <input disabled type="text" name="popupITEMID" id="popupITEMID" value="{{ isset($HDR->ICODE)?$HDR->ICODE:''}} {{ isset($HDR->ITEM_NAME)? ' - '.$HDR->ITEM_NAME:''}}" class="form-control mandatory"  autocomplete="off" readonly/>
          <input type="hidden" name="ITEMID_REF" id="ITEMID_REF" value="{{ isset($HDR->ITEMID_REF)?$HDR->ITEMID_REF:''}}" class="form-control" autocomplete="off" />
          <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />                                                               
        </div>
      </div>

      <div class="row">
        <div class="col-lg-2 pl"><p>De-Activated</p></div>
        <div class="col-lg-1 pl pr">
        <input disabled type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{isset($HDR->DEACTIVATED) && $HDR->DEACTIVATED == 1 ? "checked" : ""}} value='{{isset($HDR->DEACTIVATED) && $HDR->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
        </div>
        
        <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
        <div class="col-lg-2 pl">
          <input disabled type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{isset($HDR->DEACTIVATED) && $HDR->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($HDR->DODEACTIVATED) && $HDR->DODEACTIVATED !="" && $HDR->DODEACTIVATED !="1900-01-01" ? $HDR->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
        </div>
      </div>


      <div class="row">

        <div id="Material" class="tab-pane fade in active">
                <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:400px;margin-top:10px;" >
                    <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                      <thead id="thead1"  style="position: sticky;top: 0">
                                                    
                        <tr>
                          <th>QCP Code</th>
                          <th>QCP Description</th>
                          <th>Unit Of Measurement (UOM)</th>
                          <th>Instrument Method</th>
                          <th>Type of Standard Value</th>
                          <th id="th_st_value" >Standard Value</th>
                          <th>Action <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" /></th>
                        </tr>
                      </thead>

                      <tbody>
                        @if(isset($MAT) && !empty($MAT))
                        @foreach($MAT as $key => $row)
                        <script>$(function () {getStandardValue('STANDARDVALUE_TYPE_{{$key}}','{{$row->STANDARDVALUE_TYPE}}','{{isset($row->STANDARD_VALUE)?$row->STANDARD_VALUE:''}}');});</script>

                        <tr  class="participantRow">
                          <td hidden><input type="hidden" id="{{$key}}" > </td>
                          <td><input disabled  type="text" name="txtQCP_popup_{{$key}}" id="txtQCP_popup_{{$key}}" value="{{isset($row->QCP_CODE)?$row->QCP_CODE:''}}" class="form-control"  autocomplete="off"  readonly/></td>
                          <td  hidden><input type="text" name="QCPID_REF_{{$key}}" id="QCPID_REF_{{$key}}" value="{{isset($row->QCPID_REF)?$row->QCPID_REF:''}}" class="form-control" autocomplete="off" /></td>
                          
                          <td><input disabled type="text" name="QCP_DESC_{{$key}}" id="QCP_DESC_{{$key}}" value="{{isset($row->QCP_DESC)?$row->QCP_DESC:''}}" class="form-control"  autocomplete="off"  readonly /></td>
                          
                          <td><input disabled type="text" name="txtUOMID_popup_{{$key}}"      id="txtUOMID_popup_{{$key}}"      value="{{isset($row->UOMCODE)?$row->UOMCODE:''}}"    class="form-control" autocomplete="off" readonly/></td>
                          <td hidden><input type="text" name="UOMID_REF_{{$key}}"             id="UOMID_REF_{{$key}}"           value="{{isset($row->UOMID_REF)?$row->UOMID_REF:''}}"    class="form-control" autocomplete="off" /></td>
                          <td><input disabled type="text" name="txtINTMNTID_popup_{{$key}}"   id="txtINTMNTID_popup_{{$key}}"   value="{{isset($row->INSTRUMENT_METHOD_NAME)?$row->INSTRUMENT_METHOD_NAME:''}}"    class="form-control" autocomplete="off" readonly/></td>
                          <td hidden><input type="text" name="INTMNTID_REF_{{$key}}"          id="INTMNTID_REF_{{$key}}"        value="{{isset($row->INSTRUMENT_METHOD_ID_REF)?$row->INSTRUMENT_METHOD_ID_REF:''}}"    class="form-control" autocomplete="off" /></td>

                          <td>
                            <select disabled name="STANDARDVALUE_TYPE_{{$key}}" id="STANDARDVALUE_TYPE_{{$key}}" class="form-control"  autocomplete="off" onchange="getStandardValue(this.id,this.value,'')" >
                              <option value="">Select</option>  
                              <option {{isset($row->STANDARDVALUE_TYPE) && $row->STANDARDVALUE_TYPE =='Numeric Value'?'selected="selected"':''}} value="Numeric Value" >Numeric Value</option>
                              <option {{isset($row->STANDARDVALUE_TYPE) && $row->STANDARDVALUE_TYPE =='Range In Value'?'selected="selected"':''}} value="Range In Value">Range In Value</option>
                              <option {{isset($row->STANDARDVALUE_TYPE) && $row->STANDARDVALUE_TYPE =='Range Percent'?'selected="selected"':''}} value="Range Percent">Range In %</option>
                              <option {{isset($row->STANDARDVALUE_TYPE) && $row->STANDARDVALUE_TYPE =='Logical'?'selected="selected"':''}} value="Logical">Logical</option>
                              <option {{isset($row->STANDARDVALUE_TYPE) && $row->STANDARDVALUE_TYPE =='Text'?'selected="selected"':''}} value="Text">Text</option>
                            </select>
                         
                          </td>
                          <td><input disabled type="text" name="STANDARD_VALUE_{{$key}}" id="STANDARD_VALUE_{{$key}}" value="{{isset($row->STANDARD_VALUE)?$row->STANDARD_VALUE:''}}" class="form-control"  autocomplete="off"/></td>

                          <td align="center" >
                            <button  class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                            <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                          </td>

                        </tr>
                        @endforeach 
								        @endif
                      </tbody>
                    </table>

                   

                  </div>	
                </div>        

      </div>  



    </div>
  </form>
</div>
@endsection
@section('alert')
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog" >
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
          <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;" onclick="getFocus()"><div id="alert-active" class="activeOk1"></div>OK</button>
          <input type="hidden" id="FocusId" >
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%;">
    <div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button></div>
      <div class="tablename" style="margin:15px;"><p>Item Details</p></div>
        <div class="modal-body">
	        
	        <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
            <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
              <thead>
                <tr id="none-select" class="searchalldata" hidden>  
                  <td> 
                    <input type="hidden" name="fieldid" id="hdn_ItemID"/>
                    <input type="hidden" name="fieldid2" id="hdn_ItemID2"/>
                    <input type="hidden" name="fieldid3" id="hdn_ItemID3"/>
                    <input type="hidden" name="fieldid4" id="hdn_ItemID4"/>
                    <input type="hidden" name="fieldid5" id="hdn_ItemID5"/>
                    <input type="hidden" name="fieldid6" id="hdn_ItemID6"/>
                    <input type="hidden" name="fieldid7" id="hdn_ItemID7"/>
                    <input type="hidden" name="fieldid8" id="hdn_ItemID8"/>
                    <input type="hidden" name="fieldid9" id="hdn_ItemID9"/>
                    <input type="hidden" name="fieldid10" id="hdn_ItemID10"/>
                    <input type="hidden" name="fieldid11" id="hdn_ItemID11"/>
                    <input type="hidden" name="fieldid12" id="hdn_ItemID12"/>
                    <input type="hidden" name="fieldid13" id="hdn_ItemID13"/>
                    <input type="hidden" name="fieldid14" id="hdn_ItemID14"/>
                    <input type="hidden" name="fieldid15" id="hdn_ItemID15"/>
                    <input type="hidden" name="fieldid16" id="hdn_ItemID16"/>
                    <input type="hidden" name="fieldid17" id="hdn_ItemID17"/>
                  </td>
                </tr>
                
                <tr>
                  <th style="width:8%;text-align:center;" id="all-check">Select</th>
                  <th style="width:10%;">Item Code</th>
                  <th style="width:10%;">Name</th>
                  <th style="width:8%;">Main UOM</th>
                  <th style="width:8%;">Main QTY</th>
                  <th style="width:8%;">Item Group</th>
                  <th style="width:8%;">Item Category</th>
                  <th style="width:8%;">Business Unit</th>
                  <th style="width:8%;">ALPS Part No.</th>
                  <th style="width:8%;">Customer Part No.</th>
                  <th style="width:8%;">OEM Part No.</th>
                  <th style="width:8%;">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th style="width:8%;text-align:center;">&#10004;</th>
                  <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" onkeyup="ItemCodeFunction()"></td>
                  <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" onkeyup="ItemNameFunction()"></td>
                  <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control" onkeyup="ItemUOMFunction()"></td>
                  <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control" onkeyup="ItemQTYFunction()"></td>
                  <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" onkeyup="ItemGroupFunction()"></td>
                  <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" onkeyup="ItemCategoryFunction()"></td>
                  <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" onkeyup="ItemBUFunction()"></td>
                  <td style="width:8%;"><input type="text" id="ItemAPNsearch" class="form-control" onkeyup="ItemAPNFunction()"></td>
                  <td style="width:8%;"><input type="text" id="ItemCPNsearch" class="form-control" onkeyup="ItemCPNFunction()"></td>
                  <td style="width:8%;"><input type="text" id="ItemOEMPNsearch" class="form-control" onkeyup="ItemOEMPNFunction()"></td>
                  <td style="width:8%;"><input type="text" id="ItemStatussearch" class="form-control" onkeyup="ItemStatusFunction()"></td>
                </tr>
              </tbody>
            </table>
            
            <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
              <thead id="thead2"></thead>
              <tbody id="tbody_ItemID"></tbody> 
            </table>
          </div>

		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="QCPpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='QCP_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>QCP Code</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="QCPTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden"  id="hdn_QCPid"/>
            <input type="hidden" id="hdn_QCPid2"/>
            <input type="hidden" id="hdn_QCPid3"/>
            </td>
          </tr>

      <tr>
        <th class="ROW1">Select</th> 
        <th class="ROW2">QCP Code</th>
        <th class="ROW3">Date</th>
      </tr>
    </thead>
    <tbody>

      <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="QCPcodesearch" class="form-control" onkeyup="QCPCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="QCPnamesearch" class="form-control" onkeyup="QCPNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="QCPTable2" class="display nowrap table  table-striped table-bordered"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_QCP">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Print -->
<div id="ReportView" class="modal" role="dialog"  data-backdrop="static"  >
  <div class="modal-dialog modal-md" style="width:90%; height:90%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ReportViewclosePopup' >&times;</button>          
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Quality Inspection Master Print</p></div>
        <div class="row">
          <div class="frame-container col-lg-12 pl text-center" >
                <button class="btn topnavbt" id="btnReport">
                    Print
                </button>
                <button class="btn topnavbt" id="btnPdf">
                    PDF
                </button>
                <button class="btn topnavbt" id="btnExcel">
                    Excel
                </button>
          </div>
        </div>
        
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <div class="inner-form">
              <div class="row">
                  <div class="frame-container col-lg-12 pl " >                      
                      <iframe id="iframe_rpt" width="100%" height="1500" >
                      </iframe>
                  </div>
              </div>
          </div>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Print-->
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
#CTIDDetTable2 {
  border-collapse: collapse;
  width: 1050px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#CTIDDetTable2 th{
    text-align: left;
    padding: 5px;
    
    font-size: 11px;
    
    color: #0f69cc;
    font-weight: 600;
}

#CTIDDetTable2 td {
  text-align: left;
    padding: 5px;
    font-size: 11px;
    
    font-weight: 600;
    width: 20%;
}
</style>
@endpush

@push('bottom-scripts')
<script>
  //================================== READY FUNCTION =================================

$(document).ready(function(e) {

var lastJWODT = <?php echo json_encode($HDR->QICDT); ?>;
var today = new Date(); 
var mrsdate = <?php echo json_encode($HDR->QICDT); ?>;

$('#QICDT').attr('min',lastJWODT);
$('#QICDT').attr('max',mrsdate);

});

var rcount = <?php echo json_encode($objCount); ?>;

$('#Row_Count1').val(rcount);

$('#QICDT').change(function( event ) {
var today = new Date();     
var d     = new Date($(this).val()); 
  today.setHours(0, 0, 0, 0) ;
  d.setHours(0, 0, 0, 0) ;
  var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;

  if (d < today) {
      $(this).val(sodate);
      $("#alert").modal('show');
      $("#AlertMessage").text('QIC Date cannot be less than Current date');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
      event.preventDefault();
  } 
});

//================================== SHORTING DETAILS =================================
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

//================================== ITEM DETAILS =================================

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

if(filter.length == 0)
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
else if(filter.length >= 3)
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

if(filter.length == 0)
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
else if(filter.length >= 3)
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
if(filter.length == 0)
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
else if(filter.length >= 3)
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
function ItemQTYFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemQTYsearch");
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

function ItemGroupFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemGroupsearch");
filter = input.value.toUpperCase();
if(filter.length == 0)
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
else if(filter.length >= 3)
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

function ItemCategoryFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemCategorysearch");
filter = input.value.toUpperCase();
if(filter.length == 0)
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
else if(filter.length >= 3)
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

function ItemBUFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemBUsearch");
filter = input.value.toUpperCase();
if(filter.length == 0)
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
else if(filter.length >= 3)
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
if(filter.length == 0)
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
else if(filter.length >= 3)
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
if(filter.length == 0)
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
else if(filter.length >= 3)
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
if(filter.length == 0)
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
else if(filter.length >= 3)
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

function ItemStatusFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemStatussearch");
filter = input.value.toUpperCase();
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

function loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART){

  $("#tbody_ItemID").html('');
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("master",[361,"getItemDetails"])}}',
    type:'POST',
    data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART},
    success:function(data) {
      $("#tbody_ItemID").html(data); 
      bindItemEvents();
    },
    error:function(data){
    console.log("Error: Something went wrong.");
    $("#tbody_ItemID").html('');                        
    },
  });
}


$('#popupITEMID').click(function(event){

var CODE  = ''; 
var NAME  = ''; 
var MUOM  = ''; 
var GROUP = ''; 
var CTGRY = ''; 
var BUNIT = ''; 
var APART = ''; 
var CPART = ''; 
var OPART = ''; 

loadItem('',CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART);

$("#ITEMIDpopup").show();
event.preventDefault();

});

$("#ITEMID_closePopup").click(function(event){
$("#ITEMIDpopup").hide();
});

function bindItemEvents(){
$('[id*="chkId"]').change(function(){

  var fieldid = $(this).parent().parent().attr('id');
  var txtval =   $("#txt"+fieldid+"").val();
  var texdesc =  $("#txt"+fieldid+"").data("desc");

  var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
  var txtname =  $("#txt"+fieldid2+"").val();
 

  $('#popupITEMID').val(texdesc+' - '+txtname);
  $('#ITEMID_REF').val(txtval);

  $("#ITEMIDpopup").hide();
  
  $("#Itemcodesearch").val('');
  $("#Itemnamesearch").val('');
  $("#ItemUOMsearch").val('');
  $("#ItemQTYsearch").val('');
  $("#ItemGroupsearch").val('');
  $("#ItemCategorysearch").val('');
  $("#ItemBUsearch").val('');
  $("#ItemAPNsearch").val('');
  $("#ItemCPNsearch").val('');
  $("#ItemOEMPNsearch").val(''); 
  $("#ItemStatussearch").val('');

  event.preventDefault();
});
}


//================================== QCP DETAILS =================================

let QCPTable2 = "#QCPTable2";
let QCPTable = "#QCPTable";
let QCPheaders = document.querySelectorAll(QCPTable + " th");

QCPheaders.forEach(function(element, i) {
element.addEventListener("click", function() {
  w3.sortHTML(QCPTable2, ".clssQCPid", "td:nth-child(" + (i + 1) + ")");
});
});

function QCPCodeFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("QCPcodesearch");
filter = input.value.toUpperCase();
table = document.getElementById("QCPTable2");
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

function QCPNameFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("QCPnamesearch");
filter = input.value.toUpperCase();
table = document.getElementById("QCPTable2");
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

$('#Material').on('click','[id*="txtQCP_popup"]',function(event){

$('#hdn_QCPid').val($(this).attr('id'));
$('#hdn_QCPid2').val($(this).parent().parent().find('[id*="QCPID_REF"]').attr('id'));
$('#hdn_QCPid3').val($(this).parent().parent().find('[id*="QCP_DESC"]').attr('id'));
var fieldid = $(this).parent().parent().find('[id*="QCPID_REF"]').attr('id');

var ITEMID_REF      =  $("#ITEMID_REF").val();

if(ITEMID_REF ===""){
  $("#FocusId").val('popupITEMID');
  $("#ProceedBtn").focus();
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please Select Item.');
  $("#alert").modal('show')
  $("#OkBtn1").focus();
}
else{

  $("#QCPpopup").show();
  $("#tbody_QCP").html('loading...');

  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  })

  $.ajax({
      url:'{{route("master",[361,"getQcpCode"])}}',
      type:'POST',
      data:{'fieldid':fieldid},
      success:function(data) {
        $("#tbody_QCP").html(data);
        BindSO();
        showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_QCP").html('');
      },
  });

  $(this).parent().parent().find('[id*="QCP_DESC"]').val('');
  $(this).parent().parent().find('[id*="STANDARDVALUE_TYPE"]').val('');
  $(this).parent().parent().find('[id*="STANDARD_VALUE"]').val('');
}

});

$("#QCP_closePopup").click(function(event){
$("#QCPpopup").hide();
});

function BindSO(){
$(".clssQCPid").click(function(){
  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");
  var texdesc1 =   $("#txt"+fieldid+"").data("desc1");

  var txtid   = $('#hdn_QCPid').val();
  var txt_id2 = $('#hdn_QCPid2').val();
  var txt_id3 = $('#hdn_QCPid3').val();

  var CheckExist  = []; 
  CheckExist.push('true');

  $('#example2').find('.participantRow').each(function(){

    var QCPID_REF = $(this).find('[id*="QCPID_REF"]').val();

    if(txtval){
      if(txtval == QCPID_REF){
        CheckExist.push('false');
        return false;
      }               
    }

  });


  if(jQuery.inArray("false", CheckExist) !== -1){
    $(this).find('[id*="txtQCP_popup"]').val();
    $(this).find('[id*="QCPID_REF"]').val();
    $(this).find('[id*="QCP_DESC"]').val();

    $("#FocusId").val(txtid);
    $("#alert").modal('show');
    $("#AlertMessage").text('QCP Code Already Exist.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
  }
  else{
    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $('#'+txt_id3).val(texdesc1);
  }

  $("#QCPpopup").hide();
  $("#QCPcodesearch").val(''); 
  $("#QCPnamesearch").val(''); 
  QCPCodeFunction();
  event.preventDefault();

});
}

//================================== VALIDATE FORM =================================

var formTrans = $("#frm_mst_view");
formTrans.validate();

$( "#btnSaveFormData" ).click(function() {
  if(formTrans.valid()){
    validateForm("fnSaveData");
  }
});


$( "#btnApprove" ).click(function() {
 
  if(formTrans.valid()){
    validateForm("fnApproveData");
  }
});

function validateForm(saveAction){
 
  $("#FocusId").val('');

var QICNO         =   $.trim($("#QICNO").val());
var QICDT         =   $.trim($("#QICDT").val());
var ITEMID_REF    =   $.trim($("#ITEMID_REF").val());
var DODEACTIVATED =   $.trim($("#DODEACTIVATED").val());

if(QICNO ===""){
  $("#FocusId").val('QICNO');        
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please Enter QIC No.');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
}
else if(QICDT ===""){
  $("#FocusId").val('QICDT');        
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please Select QIC Date.');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
}
else if(ITEMID_REF ===""){
  $("#FocusId").val('popupITEMID');    
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please Select Item.');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
}  
else if($("#deactive-checkbox_0").is(":checked") == true && DODEACTIVATED ==="") {
  $("#FocusId").val('DODEACTIVATED');    
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please Select De-Activated Date.');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
}
else{
  event.preventDefault();

  var RackArray = []; 
  var allblank1 = [];
  var allblank2 = [];
  var allblank3 = [];
  var allblank4 = [];
  var allblank5 = [];

  allblank1.push('true');
  allblank2.push('true');
  allblank3.push('true');
  allblank4.push('true');
  allblank5.push('true');

  var focustext1= "";
  var focustext2= "";
  var focustext3= "";
  var focustext4= "";
  var focustext5= "";

  $('#Material').find('.participantRow').each(function(){

    var STANDARDVALUE_TYPE  = $.trim($(this).find("[id*=STANDARDVALUE_TYPE]").val());
    var STANDARD_VALUE      = $.trim($(this).find("[id*=STANDARD_VALUE]").val());
    var STANDARD_ID         = $.trim($(this).find("[id*=STANDARD_VALUE]").val());

    if($.trim($(this).find("[id*=QCPID_REF]").val()) ===""){
      allblank1.push('false');
      focustext1 = $(this).find("[id*=txtQCP_popup]").attr('id');
    }
    else if($.trim($(this).find("[id*=STANDARDVALUE_TYPE]").val()) ===""){
      allblank2.push('false');
      focustext2 = $(this).find("[id*=STANDARDVALUE_TYPE]").attr('id');
    }
    else if($.trim($(this).find("[id*=STANDARD_VALUE]").val()) ===""){
      allblank3.push('false');
      focustext3 = $(this).find("[id*=STANDARD_VALUE]").attr('id');
    }
    else if (STANDARDVALUE_TYPE ==="Range In Value" && STANDARD_VALUE.indexOf(',') < 1) { 
      allblank4.push('false');
      focustext4 = $(this).find("[id*=STANDARD_VALUE]").attr('id'); 
    }
    else if (STANDARDVALUE_TYPE ==="Range In Value" && STANDARD_VALUE.indexOf(',') > -1 && STANDARD_VALUE.split(',').length !=2) { 
      allblank4.push('false');
      focustext4 = $(this).find("[id*=STANDARD_VALUE]").attr('id'); 
    }
    else if (STANDARDVALUE_TYPE ==="Range In Value" && STANDARD_VALUE.indexOf(',') > -1 && STANDARD_VALUE.split(',').length ==2 && STANDARD_VALUE.split(',').pop() =="") { 
      allblank4.push('false');
      focustext4 = $(this).find("[id*=STANDARD_VALUE]").attr('id'); 
    }
    else if (STANDARDVALUE_TYPE ==="Range In Value" && STANDARD_VALUE.indexOf(',') > -1 && STANDARD_VALUE.split(',').length ==2 && parseFloat(STANDARD_VALUE.split(',')[0]) > parseFloat(STANDARD_VALUE.split(',')[1])) { 
      allblank5.push('false');
      focustext5 = $(this).find("[id*=STANDARD_VALUE]").attr('id'); 
    }
    else if (STANDARDVALUE_TYPE ==="Range Percent" && STANDARD_VALUE.indexOf(',') < 1) { 
      allblank4.push('false');
      focustext4 = $(this).find("[id*=STANDARD_VALUE]").attr('id'); 
    }
    else if (STANDARDVALUE_TYPE ==="Range Percent" && STANDARD_VALUE.indexOf(',') > -1 && STANDARD_VALUE.split(',').length !=2) { 
      allblank4.push('false');
      focustext4 = $(this).find("[id*=STANDARD_VALUE]").attr('id'); 
    }
    else if (STANDARDVALUE_TYPE ==="Range Percent" && STANDARD_VALUE.indexOf(',') > -1 && STANDARD_VALUE.split(',').length ==2 && STANDARD_VALUE.split(',').pop() =="") { 
      allblank4.push('false');
      focustext4 = $(this).find("[id*=STANDARD_VALUE]").attr('id'); 
    }
    else if (STANDARDVALUE_TYPE ==="Range Percent" && STANDARD_VALUE.indexOf(',') > -1 && STANDARD_VALUE.split(',').length ==2 && parseFloat(STANDARD_VALUE.split(',')[0]) > parseFloat(STANDARD_VALUE.split(',')[1])) { 
      allblank5.push('false');
      focustext5 = $(this).find("[id*=STANDARD_VALUE]").attr('id'); 
    }

  });

  if(jQuery.inArray("false", allblank1) !== -1){
    $("#FocusId").val(focustext1);
    $("#alert").modal('show');
    $("#AlertMessage").text('Please Select QCP Code.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
  }
  else if(jQuery.inArray("false", allblank2) !== -1){
    $("#FocusId").val(focustext2);
    $("#alert").modal('show');
    $("#AlertMessage").text('Please Select Type of Standard Value.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
  }
  else if(jQuery.inArray("false", allblank3) !== -1){
    $("#FocusId").val(focustext3);
    $("#alert").modal('show');
    $("#AlertMessage").text('Please Enter Standard Value');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
  }
            else{

                $("#alert").modal('show');
                $("#AlertMessage").text('Do you want to save to record.');
                $("#YesBtn").data("funcname",saveAction);
                $("#OkBtn1").hide();
                $("#OkBtn").hide();
                $("#YesBtn").show();
                $("#NoBtn").show();
                $("#YesBtn").focus();
                highlighFocusBtn('activeYes');
            }
        
    }

}

//================================== SAVE/APPROVE DETAILS =================================

window.fnSaveData = function (){

    event.preventDefault();
    var trnsoForm = $("#frm_mst_view");
    var formData = trnsoForm.serialize();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{ route("master",[$FormId,"update"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
          
            if(data.errors) {
                $(".text-danger").hide();

                if(data.errors.JWONO){
                    showError('ERROR_JWONO',data.errors.JWONO);
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn1").show();
                            $("#AlertMessage").text('Please enter correct value in VQ NO.');
                            $("#alert").modal('show');
                            $("#OkBtn1").focus();
                }
              if(data.country=='norecord') {

                $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").show();

                  $("#AlertMessage").text(data.msg);

                  $("#alert").modal('show');
                  $("#OkBtn").focus();

              }
              if(data.save=='invalid') {

                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").show();

                  $("#AlertMessage").text(data.msg);

                  $("#alert").modal('show');
                  $("#OkBtn").focus();

              }
            }
            if(data.success) {                   
                console.log("succes MSG="+data.msg);
                
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text(data.msg);
                $(".text-danger").hide();
                $("#alert").modal('show');
                $("#OkBtn").focus();
            }
            else if(data.cancel) {                   
                console.log("cancel MSG="+data.msg);
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text(data.msg);
                $(".text-danger").hide();
                $("#alert").modal('show');
                $("#OkBtn1").focus();
            }
            else 
            {                   
                console.log("succes MSG="+data.msg);
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text(data.msg);
                $(".text-danger").hide();
                $("#alert").modal('show');
                $("#OkBtn1").focus();
            }
            
        },
        error:function(data){
            console.log("Error: Something went wrong.");
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Error: Something went wrong.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
        },
    });
} 

window.fnApproveData = function (){

    event.preventDefault();
    var trnsoForm = $("#frm_mst_view");
    var formData = trnsoForm.serialize();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{ route("master",[$FormId,"Approve"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
          
            if(data.errors) {
                $(".text-danger").hide();

                if(data.errors.JWONO){
                    showError('ERROR_JWONO',data.errors.JWONO);
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn1").show();
                            $("#AlertMessage").text('Please enter correct value in VQ NO.');
                            $("#alert").modal('show');
                            $("#OkBtn1").focus();
                }
              if(data.country=='norecord') {

                $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").show();

                  $("#AlertMessage").text(data.msg);

                  $("#alert").modal('show');
                  $("#OkBtn").focus();

              }
              if(data.save=='invalid') {

                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").show();

                  $("#AlertMessage").text(data.msg);

                  $("#alert").modal('show');
                  $("#OkBtn").focus();

              }
            }
            if(data.success) {                   
                console.log("succes MSG="+data.msg);
                
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text(data.msg);
                $(".text-danger").hide();
                $("#alert").modal('show');
                $("#OkBtn").focus();
            }
            else if(data.cancel) {                   
                console.log("cancel MSG="+data.msg);
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text(data.msg);
                $(".text-danger").hide();
                $("#alert").modal('show');
                $("#OkBtn1").focus();
            }
            else 
            {                   
                console.log("succes MSG="+data.msg);
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text(data.msg);
                $(".text-danger").hide();
                $("#alert").modal('show');
                $("#OkBtn1").focus();
            }
            
        },
        error:function(data){
            console.log("Error: Something went wrong.");
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Error: Something went wrong.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
        },
    });

}


//================================== ACTION BUTTON FUNCTION =================================

$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

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
});

window.fnUndoYes = function (){
  window.location.reload();
}

$("#NoBtn").click(function(){
    $("#alert").modal('hide');
});


$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("master",[$FormId,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
    $("#JWONO").focus();
    $(".text-danger").hide();
});


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

function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}

function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
  return false;
}


/*================================== ADD/REMOVE FUNCTION ==================================*/

$("#Material").on('click','.add', function() {
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
        $clone.find('[id*="STANDARDVALUE_TYPE"]').val('');
       
        $tr.closest('table').append($clone);         
        var rowCount1 = $('#Row_Count1').val();
		    rowCount1 = parseInt(rowCount1)+1;
        $('#Row_Count1').val(rowCount1);
        $clone.find('.remove').removeAttr('disabled');        
        
        event.preventDefault();
    });

    $("#Material").on('click', '.remove', function() {
        var rowCount = $(this).closest('table').find('.participantRow').length;
        if (rowCount > 1) {
         
            $(this).closest('.participantRow').remove();  
            var rowCount1 = $('#Row_Count1').val();
            rowCount1 = parseInt(rowCount1)-1;
            $('#Row_Count1').val(rowCount1);
        } 
        if (rowCount <= 1) { 
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('There is only 1 row. So cannot be remove.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk1');
               
               
              return false;
              event.preventDefault();
        }
        event.preventDefault();
    });


//================================== FUNCTION =================================

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

$(function () {
	
	var today = new Date(); 
    var dodeactived_date = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
    $('#DODEACTIVATED').attr('min',dodeactived_date);

	$('input[type=checkbox][name=DEACTIVATED]').change(function() {
		if ($(this).prop("checked")) {
		  $(this).val('1');
		  $('#DODEACTIVATED').removeAttr('disabled');
		}
		else {
		  $(this).val('0');
		  $('#DODEACTIVATED').prop('disabled', true);
		  $('#DODEACTIVATED').val('');
		  
		}
	});

});

function getStandardValue(id,type,data){

  var ROW_ID = id.split('_').pop();

  if(type ==="Range In Value" && data ==""){
    $("#th_st_value").html('Standard Value <br/><span style="color:red;">(Use comma for range value ex-(10,20)</span>');
  }
  else if(type ==="Range Percent" && data ==""){
    $("#th_st_value").html('Standard Value <br/><span style="color:red;">(Use comma for range value ex-(10,20)</span>');
  }
  else{
    $("#th_st_value").html('Standard Value');
  }

  if(type =="Logical"){
    var STANDARD_VALUE='<select disabled name="STANDARD_VALUE_'+ROW_ID+'" id="STANDARD_VALUE_'+ROW_ID+'" class="form-control" autocomplete="off" ><option value="">Select</option><option value="Yes">Yes</option><option value="No">No</option></select>';
    $("#STANDARD_VALUE_"+ROW_ID).replaceWith(STANDARD_VALUE);
    if(data !=""){
      $('#STANDARD_VALUE_'+ROW_ID+' option[value="'+data+'"]').attr("selected", "selected");
    }
  }
  else if(type =="Numeric Value"){
    var STANDARD_VALUE='<input type="text" name="STANDARD_VALUE_'+ROW_ID+'" id="STANDARD_VALUE_'+ROW_ID+'" value="'+data+'" class="form-control" onkeypress="return onlyNumberKey(event)" autocomplete="off">';
    $("#STANDARD_VALUE_"+ROW_ID).replaceWith(STANDARD_VALUE);
  }
  else{
    var STANDARD_VALUE='<input disabled type="text" name="STANDARD_VALUE_'+ROW_ID+'" id="STANDARD_VALUE_'+ROW_ID+'" value="'+data+'" class="form-control" autocomplete="off">';
    $("#STANDARD_VALUE_"+ROW_ID).replaceWith(STANDARD_VALUE);
  }

}
var objSO = <?php echo json_encode($HDR); ?>;

$('#btnPdf').on('click', function() {
  var QICID = objSO.QICID;
  var Flag = 'P';
  var formData = 'SO='+ QICID + '&QICID='+ QICID + '&Flag='+ Flag ;
  var consultURL = '{{route("master",[361,"ViewReport",":rcdId"])}}';
  consultURL = consultURL.replace(":rcdId",formData);
  window.location.href=consultURL;
  event.preventDefault();
}); 

$('#btnExcel').on('click', function() {
  var QICID = objSO.QICID;
  var Flag = 'E';
  var formData = 'SO='+ QICID + '&QICID='+ QICID + '&Flag='+ Flag ;
  var consultURL = '{{route("master",[361,"ViewReport",":rcdId"])}}';
  consultURL = consultURL.replace(":rcdId",formData);
  window.location.href=consultURL;
  event.preventDefault();
});

$('#btnPrint').on('click', function() {
    var QICID = objSO.QICID;
    var Flag = 'H';
    var formData = 'SO='+ QICID + '&QICID='+ QICID + '&Flag='+ Flag ;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("master",[361,"ViewReport"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
            $('#ReportView').show();
            var localS = data;
            document.getElementById('iframe_rpt').src = "data:text/html;charset=utf-8," + escape(localS);
            $('#btnPdf').show();
            $('#btnExcel').show();
            $('#btnPrint').show();
        },
        error:function(data){
            console.log("Error: Something went wrong.");
            var localS = "";
            document.getElementById('iframe_rpt').src = "data:text/html;charset=utf-8," + escape(localS);
            $('#btnPdf').hide();
            $('#btnExcel').hide();
            $('#btnPrint').hide();
        },
    });
    event.preventDefault();
  });

  $('#btnReport').on('click', function() {
    var QICID = objSO.QICID;
    var Flag = 'R';
    var formData = 'SO='+ QICID + '&QICID='+ QICID + '&Flag='+ Flag ;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("master",[361,"ViewReport"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
          printWindow = window.open('');
          printWindow.document.write(data);
          printWindow.print();
        },
        error:function(data){
          console.log("Error: Something went wrong.")
          printWindow = window.open('');
          printWindow.document.write("Error: Something went wrong.");
          printWindow.print();
        },
    });
    event.preventDefault();
});

$("#ReportViewclosePopup").click(function(event){
  $("#ReportView").hide();
  event.preventDefault();
});


function onlyNumberKey(evt) {
      var ASCIICode = (evt.which) ? evt.which : evt.keyCode
      if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
          return false;
      return true;
  }
</script>


@endpush