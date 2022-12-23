@extends('layouts.app')
@section('content')

<div class="container-fluid topnav">
    <div class="row">
        <div class="col-lg-2">
        <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Budget Master</a>
        </div>

        <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-floppy-o" ></i> Save</button>
                <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
        </div>
    </div>
</div>

<form id="master_form"  method="POST">   
  @csrf
  <div class="container-fluid filter">
	  <div class="inner-form">

      <div class="row">
        <div class="col-lg-2 pl"><p>Budget No</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="BUG_NO" id="BUG_NO" value="{{isset($objResponse->BUG_NO)?$objResponse->BUG_NO:''}}" class="form-control mandatory" readonly  autocomplete="off"  style="text-transform:uppercase"  >
          <input type="hidden" name="BUGID" id="BUGID" value="{{isset($objResponse->BUGID)?$objResponse->BUGID:''}}" >
        </div>
              
        <div class="col-lg-2 pl"><p>Budget Date</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="date" name="BUG_DATE" id="BUG_DATE" value="{{isset($objResponse->BUG_DATE)?$objResponse->BUG_DATE:''}}" class="form-control BUG_DATE" autocomplete="off">
        </div>

        <div class="col-lg-2 pl"><p>Financial Year</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text"    name="FYDESC"   id="FYDESC"   value="{{isset($objResponse->FYDESCRIPTION)?$objResponse->FYDESCRIPTION:''}}"  class="form-control" autocomplete="off" onclick="getFinancialYear()" readonly >
          <input type="hidden"  name="BUG_FYID" id="BUG_FYID" value="{{isset($objResponse->BUG_FYID)?$objResponse->BUG_FYID:''}}"  class="form-control" autocomplete="off" >
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row">
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#Material">Details</a></li>
        </ul>
			
			  <div class="tab-content">
				  <div id="Material" class="tab-pane fade in active">
					  <div class="table-responsive table-wrapper-scroll-y"  >
              <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" style="height:10px !important;">
                <thead id="thead1"  style="position: sticky;top: 0">
                  <tr>
                    <th>GL Code</th>
                    <th>Name</th>
                    <th>Cost Center</th>
                    @foreach($months as $val)
                    <th>{{$val['month']}}</th>
                    @endforeach
                    <th>Total</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                @if(isset($objMAT) && !empty($objMAT))
                @foreach($objMAT as $key=>$row)
                <tr  class="participantRow">
                  <td><input {{$ActionStatus}}  type="text" name="GLCODE[]" id="GLCODE_{{$key}}" value="{{isset($row->GLCODE)?$row->GLCODE:''}}"  onclick="getGlMaster(this.id)" class="form-control"  autocomplete="off"  readonly/></td>
                  <td hidden><input type="hidden" name="GLID_REF[]" id="GLID_REF_{{$key}}" value="{{isset($row->GLID_REF)?$row->GLID_REF:''}}" class="form-control" autocomplete="off" /></td>
                  <td><input {{$ActionStatus}} type="text" name="GLNAME[]" id="GLNAME_{{$key}}" value="{{isset($row->GLNAME)?$row->GLNAME:''}}" class="form-control"  autocomplete="off"  readonly  /></td>
                  <td><input {{$ActionStatus}}  type="text" name="CCCODE[]" id="CCCODE_{{$key}}" value="{{isset($row->CCCODE)?$row->CCCODE:''}} - {{isset($row->NAME)?$row->NAME:''}}" onclick="getCostMaster(this.id)" class="form-control"  autocomplete="off"  readonly/></td>
                  <td hidden><input type="hidden" name="CCID_REF[]" id="CCID_REF_{{$key}}" value="{{isset($row->CCID_REF)?$row->CCID_REF:''}}" class="form-control" autocomplete="off" /></td>
                  
                  @foreach($months as $index=>$val)
                  @php
                    $index        = $index+1;
                    $month_field  = 'MONTH'.$index;
                    $month_name   = number_format($row->$month_field, 2, '.', '');
                  @endphp
                  <td><input {{$ActionStatus}} type="text" name="MONTH{{$index}}[]" id="MONTH{{$index}}_{{$key}}" value="{{$month_name}}"  onkeyup="calculateAmount(this.id)" onfocusout="dataDec(this,2)" onkeypress="return isNumberDecimalKey(event,this)" class="form-control finance_amount" autocomplete="off" /></td>
                  @endforeach
              
                  <td><input {{$ActionStatus}} type="text" name="TOTAL_AMOUNT[]" id="TOTAL_AMOUNT_{{$key}}" value="{{isset($row->TOTAL_AMOUNT)?$row->TOTAL_AMOUNT:''}}" class="form-control finance_amount" autocomplete="off" readonly /></td>
                  <td align="center" >
                    <button {{$ActionStatus}} class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button> 
                    <button {{$ActionStatus}} class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
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
</form>

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
                <th style="width:20%;">Select</th> 
                <th style="width:40%;" id='modal_th1'></th>
                <th style="width:40%;" id='modal_th2'></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th style="width:20%;"></th>
                <td style="width:40%;"><input type="text" id="text1" class="form-control" autocomplete="off" onkeyup="searchData(1)"></td>
                <td style="width:40%;"><input type="text" id="text2" class="form-control" autocomplete="off" onkeyup="searchData(2)"></td>
              </tr>
            </tbody>
          </table>

          <table id="modal_table2" class="display nowrap table  table-striped table-bordered" >
            <tbody id="modal_body"></tbody>
          </table>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('bottom-css')
<style>
</style>
@endpush
@push('bottom-scripts')
<script>
var month_array =@JSON($months);

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

$('#btnAdd').on('click', function() {
  var viewURL = '{{route("master",[$FormId,"add"])}}';
  window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
  window.location.href=viewURL;
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

$("#btnSave").click(function() {
  var formReqData = $("#master_form");
  if(formReqData.valid()){
    validateForm('fnSaveData','update');
  }
});

$("#btnApprove").click(function(){
  var formReqData = $("#master_form");
  if(formReqData.valid()){
    validateForm('fnApproveData','approve');
  }
});

$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName = $("#YesBtn").data("funcname");
  window[customFnName]();
});

$("#NoBtn").click(function(){
  $("#alert").modal('hide');
  $("#LABEL").focus();
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
  $(".text-danger").hide();
});

function showError(pId,pVal){
  $("#"+pId+"").text(pVal);
  $("#"+pId+"").show();
}

function getFocus(){
  $("#"+$("#FocusId").val()).focus();
  $("#closePopup").click();
}

function highlighFocusBtn(pclass){
  $(".activeYes").hide();
  $(".activeNo").hide();
  $("."+pclass+"").show();
}

window.fnSaveData = function (){

  event.preventDefault();

  var trnFormReq  = $("#master_form");
  var formData    = trnFormReq.serialize();

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $("#btnSave").hide(); 
  $(".buttonload").show(); 
  $("#btnApprove").prop("disabled", true);
  $.ajax({
    url:'{{ route("master",[$FormId,"update"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $(".buttonload").hide(); 
      $("#btnSave").show();   
      $("#btnApprove").prop("disabled", false);

      if(data.errors) {
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(data.msg);
        $("#alert").modal('show');
        $("#OkBtn").focus();
        $(".text-danger").show();
      }
      else if(data.success) {                   
        console.log("succes MSG="+data.msg);
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(data.msg);
        $("#alert").modal('show');
        $("#OkBtn").focus();
        $(".text-danger").hide();
      }
      
    },
    error:function(data){
        $(".buttonload").hide(); 
        $("#btnSave").show();   
        $("#btnApprove").prop("disabled", false);
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

  var trnFormReq  = $("#master_form");
  var formData    = trnFormReq.serialize();

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $("#btnApprove").hide(); 
  $(".buttonload_approve").show();  
  $("#btnSave").prop("disabled", true);
  $.ajax({
    url:'{{ route("master",[$FormId,"Approve"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $("#btnApprove").show();  
      $(".buttonload_approve").hide();  
      $("#btnSave").prop("disabled", false);

      if(data.errors) {
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(data.msg);
        $("#alert").modal('show');
        $("#OkBtn").focus();
        $(".text-danger").show();
      }
      else if(data.success) {                   
        console.log("succes MSG="+data.msg);
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(data.msg);
        $("#alert").modal('show');
        $("#OkBtn").focus();
        $(".text-danger").hide();
      }
      
    },
    error:function(data){
        $("#btnApprove").show();  
        $(".buttonload_approve").hide();  
        $("#btnSave").prop("disabled", false);
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

function validateForm(actionType,actionMsg){
 
  if($.trim($("#BUG_NO").val()) ===""){
    $("#FocusId").val('BUG_NO');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter budget no.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#BUG_DATE").val()) ===""){
    $("#FocusId").val('BUG_DATE');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select budget date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#BUG_FYID").val()) ===""){
    $("#FocusId").val('FYDESC');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select financial year.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }  
  else{
    var flag_status = true;
    var focus_id    = '';
    var input1      = document.getElementsByName('GLCODE[]');

    for (var i = 0; i < input1.length; i++) {
      if(input1[i].value ===''){
        flag_status = false;
        focus_id    = input1[i].id;
        break;
      }
    }

    if(flag_status ==false){
      $("#FocusId").val(focus_id);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select GL Code');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else{
      $("#alert").modal('show');
      $("#AlertMessage").text('Do you want to '+actionMsg+' to record.');
      $("#YesBtn").data("funcname",actionType);
      $("#YesBtn").focus();
      $("#OkBtn").hide();
      highlighFocusBtn('activeYes');
    }
  }
}

$("#Material").on('click', '.add', function() {
  var $tr     = $(this).closest('table');
  var allTrs  = $tr.find('.participantRow').last();
  var lastTr  = allTrs[allTrs.length-1];
  var $clone  = $(lastTr).clone();

  $clone.find('td').each(function(){
    var el  = $(this).find(':first-child');
    var id  = el.attr('id') || null;

    if(id) {
      var i = id.substr(id.length-1);
      var prefix = id.substr(0, (id.length-1));
      el.attr('id', prefix+(+i+1));
    }

  });

  $clone.find('input:text').val('');
  $clone.find('input:hidden').val('');
  $clone.find('[class*="finance_amount"]').val('0.00');
  
  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count1').val();
  rowCount1     = parseInt(rowCount1)+1;
  $('#Row_Count1').val(rowCount1);
  $clone.find('.remove').removeAttr('disabled'); 
  event.preventDefault();
});

$("#Material").on('click', '.remove', function() {
  var rowCount = $(this).closest('table').find('.participantRow').length;
  if (rowCount > 1) {
    $(this).closest('.participantRow').remove();     
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

function isNumberDecimalKey(evt){
  var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
  return false;

  return true;
}

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

function getFinancialYear(){
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
    url:'{{route("master",[$FormId,"getFinancialYear"])}}',
    type:'POST',
    success:function(data) {
      var html = '';

      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr>';
          html +='<td style="width:20%;" ><input type="checkbox" id="key_'+key+'" value="'+value.FYID+'" onChange="bindFinancialYear(this)" data-field1="'+value.FYDESCRIPTION+'" data-field2="'+value.FYSTMONTH+'"data-field3="'+value.FYSTYEAR+'"data-field4="'+value.FYENDMONTH+'"data-field5="'+value.FYENDYEAR+'" ></td>';
          html +='<td style="width:40%;" >'+value.FYCODE+'</td>';
          html +='<td style="width:40%;" >'+value.FYDESCRIPTION+'</td>';
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

  $("#modal_title").text('Financial Year');
  $("#modal_th1").text('Code');
  $("#modal_th2").text('Description');
  $("#modal").show();
}

function bindFinancialYear(data){
  $("#BUG_FYID").val(data.value);
  $("#FYDESC").val($("#"+data.id).data("field1"));

  var first_month = $("#"+data.id).data("field2");
  var first_year  = $("#"+data.id).data("field3");
  var last_month  = $("#"+data.id).data("field4");
  var last_year   = $("#"+data.id).data("field5");

  bindDetails(first_month,first_year,last_month,last_year);
  resetModal();
}

function bindDetails(first_month,first_year,last_month,last_year){

  var start_date  = first_year+'-'+first_month+'-01';
  var end_date    = last_year+'-'+last_month+'-01'; 

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("master",[$FormId,"getMonthsInRange"])}}',
    type:'POST',
    data:{start_date:start_date,end_date:end_date},
    success:function(data) {
      var html        = '';
      var html_th     = '';
      var html_td     = '';
      
      if(data.length > 0){
        month_array     = data;
        $.each(data, function(key, value) {
          var key = key+1;

          html_th +='<th>'+value.month+'</th>';
          html_td +='<td><input type="text" name="MONTH'+key+'[]" id="MONTH'+key+'_0" value="0.00" onkeyup="calculateAmount(this.id)" onfocusout="dataDec(this,2)" onkeypress="return isNumberDecimalKey(event,this)" class="form-control finance_amount" autocomplete="off" /></td>';
        });

        html +='<thead id="thead1"  style="position: sticky;top: 0">';
        html +='<tr>';
        html +='<th>GL Code</th>';
        html +='<th>Name</th>';
        html +='<th>Cost Center</th>';
        html +=html_th;
        html +='<th>Total</th>';
        html +='<th>Action</th>';
        html +='</tr>';
        html +='<tbody>';
        html +='<tr  class="participantRow">';
        html +='<td><input  type="text" name="GLCODE[]" id="GLCODE_0" onclick="getGlMaster(this.id)" class="form-control"  autocomplete="off"  readonly/></td>';
        html +='<td hidden><input type="hidden" name="GLID_REF[]" id="GLID_REF_0" class="form-control" autocomplete="off" /></td>';
        html +='<td><input type="text" name="GLNAME[]" id="GLNAME_0" class="form-control"  autocomplete="off"  readonly  /></td>';
        html +='<td><input  type="text" name="CCCODE[]" id="CCCODE_0" onclick="getCostMaster(this.id)" class="form-control"  autocomplete="off"  readonly/></td>';
        html +='<td hidden><input type="hidden" name="CCID_REF[]" id="CCID_REF_0" class="form-control" autocomplete="off" /></td>';
        html +=html_td;
        html +='<td><input type="text" name="TOTAL_AMOUNT[]" id="TOTAL_AMOUNT_0" value="0.00" class="form-control finance_amount" autocomplete="off" readonly /></td>';
        html +='<td align="center" >';
        html +='<button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button> ';
        html +='<button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>';
        html +='</td>';
        html +='</tr>';
        html +='</tbody>';
      }
      else{
        html +='<thead><tr><th>&nbsp;</th></tr></thead>';
      }

      $("#example2").html(html);
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

}

function getGlMaster(id){
  var rowno = id.split('_').pop();

  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
    url:'{{route("master",[$FormId,"getGlMaster"])}}',
    type:'POST',
    success:function(data) {
      var html = '';

      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr>';
          html +='<td style="width:20%;" ><input type="checkbox" id="key_'+key+'" value="'+value.GLID+'" onChange="bindGlMaster(this,'+rowno+')" data-field1="'+value.GLCODE+'" data-field2="'+value.GLNAME+'" ></td>';
          html +='<td style="width:40%;" >'+value.GLCODE+'</td>';
          html +='<td style="width:40%;" >'+value.GLNAME+'</td>';
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

  $("#modal_title").text('GL Details');
  $("#modal_th1").text('Code');
  $("#modal_th2").text('Description');
  $("#modal").show();
}

function bindGlMaster(data,rowno){

  var value1      = data.value;;
  var value2      = $("#CCID_REF_"+rowno).val();
  var check_exist = checkGlExist(value1,value2);

  if(check_exist ==false){
    $("#GLID_REF_"+rowno).val(data.value);
    $("#GLCODE_"+rowno).val($("#"+data.id).data("field1"));
    $("#GLNAME_"+rowno).val($("#"+data.id).data("field2") );
    resetModal();
  }
  else{
    resetModal();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('This GL Code already exist.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
  }
}

function getCostMaster(id){
  var rowno = id.split('_').pop();

  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
    url:'{{route("master",[$FormId,"getCostMaster"])}}',
    type:'POST',
    success:function(data) {
      var html = '';

      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr>';
          html +='<td style="width:20%;" ><input type="checkbox" id="key_'+key+'" value="'+value.CCID+'" onChange="bindCostMaster(this,'+rowno+')" data-field1="'+value.CCCODE+'" data-field2="'+value.NAME+'" ></td>';
          html +='<td style="width:40%;" >'+value.CCCODE+'</td>';
          html +='<td style="width:40%;" >'+value.NAME+'</td>';
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

  $("#modal_title").text('Cost Center Details');
  $("#modal_th1").text('Code');
  $("#modal_th2").text('Description');
  $("#modal").show();
}

function bindCostMaster(data,rowno){

  var value1      = $("#GLID_REF_"+rowno).val();
  var value2      = data.value;
  var check_exist = checkGlExist(value1,value2);

  if(check_exist ==false){
    $("#CCID_REF_"+rowno).val(data.value);
    $("#CCCODE_"+rowno).val($("#"+data.id).data("field1")+' - '+$("#"+data.id).data("field2") );
    resetModal();
  }
  else{
    resetModal();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('This GL Code/Cost Center already exist.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
  }

}

function resetModal(){
  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html(''); 
  $("#modal").hide(); 
}

function checkGlExist(value1,value2){

  var input1  = document.getElementsByName('GLID_REF[]');
  var input2  = document.getElementsByName('CCID_REF[]');

  var status  = false;
  for (var i = 0; i < input1.length; i++) {
    var a1 = input1[i];
    var a2 = input2[i];

    if(a1.value == value1 && a2.value == value2){
      status = true;
      break;
    }
  }

  return status;
}

function calculateAmount(textid){
  var textid        = textid.split('_').pop();
  var amount_array  = [];
  $.each(month_array, function(key, value) {
    var key     = key+1;
    var amount  = $("#MONTH"+key+'_'+textid).val();
    var amount  = amount !=''?parseFloat(amount):0;
    amount_array.push(amount);
  });

  var total_amount  = getArraySum(amount_array);
  $("#TOTAL_AMOUNT_"+textid).val(parseFloat(total_amount).toFixed(2));
}

function getArraySum(a){
    var total=0;
    for(var i in a) { 
        total += a[i];
    }
    return total;
}

function dataDec(data,no){
  var text_value  = data.value !=''?data.value:0;
  $("#"+data.id).val(parseFloat(text_value).toFixed(no));
}
</script>
@endpush