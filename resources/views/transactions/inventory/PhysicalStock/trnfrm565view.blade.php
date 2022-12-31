@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Physical Stcok</a></div>
    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
      <button class="btn topnavbt" id="btnSaveFormData" disabled="disabled"><i class="fa fa-save"></i> Save</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
      <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
      <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>

<form id="form_data" method="POST"  >
  <div class="container-fluid purchase-order-view">    
    @csrf
    <div class="container-fluid filter">
      <div class="inner-form"> 
        
        <div class="row">
          <div class="col-lg-2 pl"><p>Document No*</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="hidden"  name="DOC_ID"  id="DOC_ID" value="{{isset($HDR->DGID)?$HDR->DGID:''}}" >
            <input {{$ActionStatus}} type="text"    name="DOC_NO"  id="DOC_NO"  value="{{isset($HDR->DOCNO)?$HDR->DOCNO:''}}"  class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
          </div>
                            
          <div class="col-lg-2 pl"><p>Document Date*</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="date" name="DOC_DATE" id="DOC_DATE" value="{{isset($HDR->DOCDT)?$HDR->DOCDT:''}}"  class="form-control" autocomplete="off" placeholder="dd/mm/yyyy" readonly >
          </div>

     

        </div>

     
      </div>

      <div class="container-fluid purchase-order-view">
        <div class="row">
          <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#Detail" id="DETAIL_TAB">Details</a></li>
          </ul>
                                              
          <div class="tab-content">
           
          <div id="Payment" class="tab-pane fade in active ">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px; width: 1400px"  >
              <table id="example3" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0">
                  <tr>
                    <th>Document Type	</th>
                    <th>Document No	</th>             
                    <th>Document Date	</th>             
                    <th>Transporter Name</th>             
                    <th>Docket No</th>             
                    <th>Dispatch Date</th>             
                    <th>Mode</th>
                    <th>Remarks</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                @if(isset($DET) && !empty($DET))
                  @foreach($DET as $key=>$row)
                  <tr class="participantRow2">
                  <td>
                      <select {{$ActionStatus}} name="DOCUMENT_TYPE[]" id="DOCUMENT_TYPE_{{$key}}" class="form-control" onchange="getDocType(this.id);">
                        <option value="">Select</option>
                        <option {{isset($row->DOCUMENT_TYPE) && $row->DOCUMENT_TYPE =='SI'?'selected="selected"':''}} value="SI">Sales Invoice</option>
                        <option {{isset($row->DOCUMENT_TYPE) && $row->DOCUMENT_TYPE =='RGP'?'selected="selected"':''}} value="RGP">Returnable Gate Pass</option>
                        <option {{isset($row->DOCUMENT_TYPE) && $row->DOCUMENT_TYPE =='NRGP'?'selected="selected"':''}} value="NRGP">Non Returnable Gate Pass</option> 
                      </select>
                    </td>

                  <td><input type="text" {{$ActionStatus}} name="DOCUMENT_NO[]" id="DOCUMENT_NO_{{$key}}"   class="form-control" readonly  autocomplete="off"  onclick="getDocument(this.id)"  value="{{$row->DOC_NO}}"/></td>
                    <td hidden><input type="hidden" name="DOCUMENTID_REF[]" id="DOCUMENTID_REF_{{$key}}"     value="{{$row->DOCID_REF}}"   class="form-control"  autocomplete="off" /></td>

                    <td> <input type="text" {{$ActionStatus}} readonly name="DOCUMENT_DT[]" id="DOCUMENT_DT_{{$key}}" value="{{$row->DOCID_DT}}" class="form-control" ></td>

                    <td><input type="text" {{$ActionStatus}} name="TRANSPORTER_NAME[]" id="TRANSPORTER_NAME_{{$key}}"   class="form-control" readonly  autocomplete="off"  onclick="getTransporterMaster(this.id)" value="{{$row->TRANSPORTER_NAME}}" /></td>
                    <td hidden><input type="hidden" name="TRANSPORTERID_REF[]" id="TRANSPORTERID_REF_{{$key}}"      class="form-control"  autocomplete="off" value="{{$row->TRANSPORTERID_REF}}" /></td>
                   
                    <td> <input type="text" {{$ActionStatus}} style="text-transform: uppercase" name="DOCKET_NO[]" id="DOCKET_NO_{{$key}}" class="form-control" value="{{$row->DOCKET_NO}}"  ></td>
                    <td> <input type="date"  {{$ActionStatus}} name="DISPATCH_DT[]" id="DISPATCH_DT_{{$key}}" class="form-control" value="{{$row->DISPATCH_DT}}"></td>
                    
                    <td>
                      <select name="MODE[]" id="MODE_{{$key}}" class="form-control" {{$ActionStatus}} >
                        <option value="">Select</option>
                        <option {{isset($row->MODE) && $row->MODE =='By Air'?'selected="selected"':''}} value="By Air">By Air</option>
                        <option {{isset($row->MODE) && $row->MODE =='By Hand'?'selected="selected"':''}} value="By Hand">By Hand</option>
                        <option {{isset($row->MODE) && $row->MODE =='By Surfance'?'selected="selected"':''}} value="By Surfance">By Surfance</option> 
                        <option {{isset($row->MODE) && $row->MODE =='By Truck'?'selected="selected"':''}} value="By Truck">By Truck</option>
                        <option {{isset($row->MODE) && $row->MODE =='Self'?'selected="selected"':''}} value="Self">Self</option>
                      </select>
                    </td>

                    <td> <input type="text" {{$ActionStatus}} name="REMARKS[]" id="REMARKS_{{$key}}" value="{{$row->REMARKS}}" class="form-control"></td>
                             
                    <td align="center" >
                      <button class="btn add ainvoice" title="add" {{$ActionStatus}}  data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                      <button class="btn remove dinvoice" title="Delete" {{$ActionStatus}}  data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
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
      </div>
    </div>
  </div>
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
          <button class="btn alertbt" name='OkBtn1' id="OkBtn1" onclick="getFocus()" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk1"></div>OK</button>
          <input type="hidden" id="FocusId" >
        </div>
		  <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="modal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:50%;" >
    <div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" onclick="closeEvent('modal')" >&times;</button></div>
      <div class="modal-body">
	      <div class="tablename"><p id='modal_title'></p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="modal_table1" class="display nowrap table  table-striped table-bordered" >
            <thead>
              <tr>
                <th style="width:10%;">Select</th> 
                <th style="width:45%;" id='modal_th1'></th>
                <th style="width:45%;" id='modal_th2'></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th style="width:10%;"></th>
                <td style="width:45%;"><input type="text" id="text1" class="form-control" autocomplete="off" onkeyup="searchData(1)"></td>
                <td style="width:45%;"><input type="text" id="text2" class="form-control" autocomplete="off" onkeyup="searchData(2)"></td>
              </tr>
            </tbody>
          </table>

          <table id="modal_table2" class="display nowrap table  table-striped table-bordered" >
            <tbody id="modal_body" style="font-size:14px;"></tbody>
          </table>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('bottom-scripts')
<script>
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

let tid1    = "#modal_table1";
let tid2    = "#modal_table2";
let headers = document.querySelectorAll(tid1 + " th");

headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(tid2, ".clsipoid", "td:nth-child(" + (i + 1) + ")");
  });
});

function searchData(cno){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById('text'+cno);
  filter = input.value.toUpperCase();
  table = document.getElementById("modal_table2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[cno];
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

function closeEvent(id){
  $("#"+id).hide();
}

$("#Payment").on('click', '.dinvoice', function(){
    var rowCount = $(this).closest('table').find('.participantRow2').length;
    if (rowCount > 1) {
    $(this).closest('.participantRow2').remove();   
    
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
    }
    event.preventDefault();
});

$("#Payment").on('click', '.ainvoice', function(){
  var $tr = $(this).closest('table');
  var allTrs = $tr.find('.participantRow2').last();
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

  });

  $clone.find('input:text').val('');
  $clone.find('input:hidden').val('');

  $tr.closest('table').append($clone);         
  $clone.find('.dinvoice').removeAttr('disabled'); 
  event.preventDefault();
});

function saveAction(action){
  validateForm(action);
}
function validateForm(action){

var flag_exist    = [];
var flag_status   = [];
var flag_focus    = '';
var flag_message  = '';
var flag_tab_type = '';


for (var i = 0; i < document.getElementsByName('DOCUMENT_TYPE[]').length; i++) {
  var payment_type = $.trim(document.getElementsByName('DOCUMENT_TYPE[]')[i].value);
  if(payment_type ===""){
    flag_status.push('false');
    flag_focus    = document.getElementsByName('DOCUMENT_TYPE[]')[i].id;
    flag_message  = 'Please Select Document Type';
    flag_tab_type = 'DETAIL_TAB';
  }
  else if($.trim(document.getElementsByName('DOCUMENT_NO[]')[i].value) ===""){
    flag_status.push('false');
    flag_focus    = document.getElementsByName('DOCUMENT_NO[]')[i].id;
    flag_message  = 'Please Select Document No';
    flag_tab_type = 'DETAIL_TAB';
  }
  else if($.trim(document.getElementsByName('TRANSPORTER_NAME[]')[i].value) ===""){
    flag_status.push('false');
    flag_focus    = document.getElementsByName('TRANSPORTER_NAME[]')[i].id;
    flag_message  = 'Please Select Transporter Name';
    flag_tab_type = 'DETAIL_TAB';
  }
   
  else if($.trim(document.getElementsByName('DOCKET_NO[]')[i].value) ===""){
    flag_status.push('false');
    flag_focus    = document.getElementsByName('DOCKET_NO[]')[i].id;
    flag_message  = 'Please Enter Docket No';
    flag_tab_type = 'DETAIL_TAB';
  }
  else if($.trim(document.getElementsByName('DISPATCH_DT[]')[i].value) ===""){
    flag_status.push('false');
    flag_focus    = document.getElementsByName('DISPATCH_DT[]')[i].id;
    flag_message  = 'Please Select Dispatch Date';
    flag_tab_type = 'DETAIL_TAB';
  }
  else if($.trim(document.getElementsByName('MODE[]')[i].value) ===""){
    flag_status.push('false');
    flag_focus    = document.getElementsByName('MODE[]')[i].id;
    flag_message  = 'Please Select Mode';
    flag_tab_type = 'DETAIL_TAB';
  }
 
}




if($.trim($("#DOC_NO").val()) ===""){
  $("#FocusId").val('DOC_NO');        
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please Enter Service Invoice No.');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
}
else if($.trim($("#DOC_DATE").val()) ===""){
  $("#FocusId").val('DOC_DATE');        
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please Select Invoice Date.');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
}

else if(jQuery.inArray("false", flag_status) !== -1){
  $("#"+flag_tab_type).click();
  $("#FocusId").val(flag_focus);        
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text(flag_message);
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
}  

else{
  $("#alert").modal('show');
  $("#AlertMessage").text('Do you want to '+action+' to record.');
  $("#YesBtn").data("funcname","fnSaveData");
  $("#YesBtn").data("action",action);
  $("#OkBtn1").hide();
  $("#OkBtn").hide();
  $("#YesBtn").show();
  $("#NoBtn").show();
  $("#YesBtn").focus();
  highlighFocusBtn('activeYes');
}
}



$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName  = $("#YesBtn").data("funcname");
  var action        = $("#YesBtn").data("action");

  if(action ==="save"){
    window[customFnName]('{{route("transaction",[$FormId,"save"])}}');
  }
  else if(action ==="update"){
    window[customFnName]('{{route("transaction",[$FormId,"update"])}}');
  }
  else if(action ==="approve"){
    window[customFnName]('{{route("transaction",[$FormId,"Approve"])}}');
  }
  else{
    window.location.href = '{{route("transaction",[$FormId,"index"]) }}';
  }
});

window.fnSaveData = function (path){

  event.preventDefault();
  var trnsoForm = $("#form_data");
  var formData = trnsoForm.serialize();

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $("#btnSaveFormData").hide(); 
  $(".buttonload").show(); 
  $("#btnApprove").prop("disabled", true);

  $.ajax({
    url:path,
    type:'POST',
    data:formData,
    success:function(data) {
      $(".buttonload").hide(); 
      $("#btnSaveFormData").show();   
      $("#btnApprove").prop("disabled", false);
       
      if(data.success){                   
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(data.msg);
        $(".text-danger").hide();
        $("#alert").modal('show');
        $("#OkBtn").focus();
      }
      else{                   
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(data.msg);
        $(".text-danger").hide();
        $("#alert").modal('show');
        $("#OkBtn1").focus();
      } 
    },
    error: function (request, status, error){
      $(".buttonload").hide(); 
      $("#btnSaveFormData").show();   
      $("#btnApprove").prop("disabled", false);
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
    },
  });
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
  window.location.href = '{{route("transaction",[$FormId,"index"]) }}';
});

$("#OkBtn1").click(function(){
  $("#alert").modal('hide');
  $("#YesBtn").show();
  $("#NoBtn").show();
  $("#OkBtn").hide();
  $("#OkBtn1").hide();
  $("#"+$(this).data('focusname')).focus();
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

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}

function isNumberKey(e,t){
    try {
        if (window.event) {
            var charCode = window.event.keyCode;
        }
        else if (e) {
            var charCode = e.which;
        }
        else { return true; }
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {         
        return false;
        }
         return true;

    }
    catch (err) {
        alert(err.Description);
    }
}





function getDocType(id){
var rowid = id.split('_').pop(0);
$("#DOCUMENT_NO_"+rowid).val("");
$("#DOCUMENTID_REF_"+rowid).val("");
$("#DOCUMENT_DT_"+rowid).val("");
$("#TRANSPORTER_NAME_"+rowid).val("");
$("#TRANSPORTERID_REF_"+rowid).val("");
$("#DOCKET_NO_"+rowid).val("");
$("#DISPATCH_DT_"+rowid).val("");
$("#MODE_"+rowid).val("");
$("#REMARKS_"+rowid).val("");

}

function dataDec(data,no){
  var text_value  = data.value !=''?parseFloat(data.value).toFixed(no):'';
  $("#"+data.id).val(text_value);
}




function getDocument(id){


if($('#DOCUMENT_TYPE_'+id.split("_").pop(0)).val() ==""){
    
    $("#FocusId").val('DOCUMENT_TYPE_'+id.split("_").pop(0) );        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select document type.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }

var DOCUMENT_TYPE=$("#DOCUMENT_TYPE_"+id.split('_').pop(0)).val(); 

$.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$.ajax({
  url:'{{route("transaction",[$FormId,"getDocument"])}}',
  type:'POST',
  data:{DOCUMENT_TYPE:DOCUMENT_TYPE},

  success:function(data) {
    var html = '';
    if(data.length > 0){
      $.each(data, function(key, value) {
        html +='<tr>';
        html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DOCID+'" onChange="bindDocument(this)" data-code="'+value.DOCNO+'" data-desc="'+value.DOCDT+'" data-rowid="'+id+'" ></td>';
        html +='<td style="width:45%;" >'+value.DOCNO+'</td>';
        html +='<td style="width:45%;" >'+value.DOCDT+'</td>';
        html +='</tr>';
      });
    }
    else{
      html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
    }

    $("#modal_body").html(html);
  },
  error: function (request, status, error) {
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").show();
    $("#AlertMessage").text(request.responseText);
    $("#alert").modal('show');
    $("#OkBtn").focus();
    highlighFocusBtn('activeOk');
    $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
  },
});

$("#modal_title").text('Document List');
$("#modal_th1").text('Document No');
$("#modal_th2").text('Document Date');
$("#modal").show();
}

function bindDocument(data){

var code    = $("#"+data.id).data("code");
var desc    = $("#"+data.id).data("desc"); 
var rowid   = $("#"+data.id).data("rowid");

var CheckExist_documentid = [];

$('#example3').find('.participantRow2').each(function(){

  if($(this).find('[id*="DOCUMENTID_REF"]').val() != ''){

    var docid  = $(this).find('[id*="DOCUMENTID_REF"]').val();

      if(docid!=''){
        CheckExist_documentid.push(docid);
      }

  }
});

if($.inArray(data.value, CheckExist_documentid) !== -1 ){    
  $("#DOCUMENT_NO_"+rowid.split('_').pop(0)).val('');
  $("#DOCUMENTID_REF_"+rowid.split('_').pop(0)).val('');
  $("#FocusId").val("#DOCUMENT_NO_"+rowid);
  $("#alert").modal('show');
  $("#AlertMessage").text('Document Master already exist.');
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
  $("#modal").hide(); 
  return false;
}
else{
  $("#DOCUMENTID_REF_"+rowid.split('_').pop(0)).val(data.value);
  $("#DOCUMENT_NO_"+rowid.split('_').pop(0)).val(code);
  $("#DOCUMENT_DT_"+rowid.split('_').pop(0)).val(desc);
}

$("#text1").val(''); 
$("#text2").val(''); 
$("#modal_body").html('');  
var CheckExist_documentid = [];
$("#modal").hide(); 
}



//Transporter popup starts here 

function getTransporterMaster(id){
if($('#DOCUMENT_TYPE_'+id.split("_").pop(0)).val() ==""){
  
  $("#FocusId").val('DOCUMENT_TYPE_'+id.split("_").pop(0) );        
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please select document type.');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
}



$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

$.ajax({
url:'{{route("transaction",[$FormId,"getTransporterMaster"])}}',
type:'POST',

success:function(data) {
  var html = '';
  if(data.length > 0){
    $.each(data, function(key, value) {
      html +='<tr>';
      html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DOCID+'" onChange="bindTransporterMaster(this)" data-code="'+value.DOCNO+'" data-desc="'+value.DOCDT+'" data-rowid="'+id+'" ></td>';
      html +='<td style="width:45%;" >'+value.DOCNO+'</td>';
      html +='<td style="width:45%;" >'+value.DOCDT+'</td>';
      html +='</tr>';
    });
  }
  else{
    html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
  }

  $("#modal_body").html(html);
},
error: function (request, status, error) {
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn").show();
  $("#AlertMessage").text(request.responseText);
  $("#alert").modal('show');
  $("#OkBtn").focus();
  highlighFocusBtn('activeOk');
  $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
},
});

$("#modal_title").text('Document List');
$("#modal_th1").text('Document No');
$("#modal_th2").text('Document Date');
$("#modal").show();
}

function bindTransporterMaster(data){

var code    = $("#"+data.id).data("code");
var desc    = $("#"+data.id).data("desc"); 
var rowid   = $("#"+data.id).data("rowid");

$("#TRANSPORTERID_REF_"+rowid.split('_').pop(0)).val(data.value);
$("#TRANSPORTER_NAME_"+rowid.split('_').pop(0)).val(code+'-'+desc);

$("#text1").val(''); 
$("#text2").val(''); 
$("#modal_body").html('');  
$("#modal").hide(); 
}

</script>
@endpush