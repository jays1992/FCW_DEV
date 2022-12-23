
@extends('layouts.app')
@section('content')
<!-- <form id="frm_trn_so" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >     -->

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Salary Structure Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSO"  disabled="disabled" ><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove"  disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div>
    </div><!--topnav-->	
   

    <div class="container-fluid purchase-order-view filter">     
  <form id="frm_mst_view" method="POST"  > 
    @CSRF
    <div class="inner-form">
        
      <div class="row">
        <div class="col-lg-2 pl"><p>Salary Structure No</p></div>
        <div class="col-lg-2 pl">
          <input disabled  type="text" name="SALARY_STRUC_NO" id="SALARY_STRUC_NO" value="{{ isset($HDR->SALARY_STRUC_NO)?$HDR->SALARY_STRUC_NO:''}}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"> 
        </div>
        
        <div class="col-lg-2 pl"><p>Description</p></div>
        <div class="col-lg-2 pl">
          <input disabled type="text" name="SALARY_STRUC_DESC" id="SALARY_STRUC_DESC" value="{{ isset($HDR->SALARY_STRUC_DESC)?$HDR->SALARY_STRUC_DESC:''}}"  class="form-control mandatory" autocomplete="off" >
        </div>
      </div>

      <div class="row">
        <div class="col-lg-2 pl"><p>De-Activated</p></div>
        <div class="col-lg-2 pl pr">
        <input disabled type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{isset($HDR->DEACTIVATED) && $HDR->DEACTIVATED == 1 ? "checked" : ""}} value='{{isset($HDR->DEACTIVATED) && $HDR->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
        </div>
        
        <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
        <div class="col-lg-2 pl">
          <input disabled type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{isset($HDR->DEACTIVATED) && $HDR->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($HDR->DODEACTIVATED) && $HDR->DODEACTIVATED !="" && $HDR->DODEACTIVATED !="1900-01-01" ? $HDR->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
        </div>
      </div>

    </div>

    <div class="container-fluid purchase-order-view">
      <div class="row">

        <ul class="nav nav-tabs">
           <li class="active"><a data-toggle="tab" href="#EarningHead">Earning/Deduction Head</a></li>
        </ul>  
         
        <div class="tab-content">

          <div id="EarningHead" class="tab-pane fade in active">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar"  >
              <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0"> 

                  <tr>
                    <th>Earning/Deduction Type</th>
                    <th>Earning/Deduction Code</th>
                    <th>Earning/Deduction Description</th>
                    <th>Earning/Deduction Head Type</th>
                    <th>Sq No</th>
                    <th>Amount / Formula</th>
                    <th>Formula</th>
                    <th>Amount</th>
                    <th>Remarks</th>
                    <th>Action <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="{{count($MAT)}}"></th>
                  </tr>
                </thead>
                <tbody>
                  @if(isset($MAT) && !empty($MAT))
                  @foreach($MAT as $key => $row)
                  <tr  class="participantRow1">

                    <td>
                      <select disabled name="HEAD_TYPE_{{$key}}" id="HEAD_TYPE_{{$key}}" class="form-control"  autocomplete="off" onchange="select_head_type(this.id)" >
                        <option value="">Select</option> 
                        <option {{isset($row['HEAD_TYPE']) && $row['HEAD_TYPE'] =='EARNING'?'selected="selected"':''}} value="EARNING">EARNING</option>
                        <option {{isset($row['HEAD_TYPE']) && $row['HEAD_TYPE'] =='DEDUCTION'?'selected="selected"':''}} value="DEDUCTION">DEDUCTION</option>
                      </select>
                    </td>
                  
                    <td><input disabled  type="text" name="EARNING_HEADID_CODE_{{$key}}" id="EARNING_HEADID_CODE_{{$key}}" value="{{isset($row['HEADCODE'])?$row['HEADCODE']:''}}" class="form-control"  autocomplete="off" onclick="select_data(this.id)"  readonly  /></td>
                    <td hidden><input type="text" name="EARNING_HEADID_REF_{{$key}}" id="EARNING_HEADID_REF_{{$key}}" value="{{isset($row['EARNING_HEADID_REF'])?$row['EARNING_HEADID_REF']:''}}"   class="form-control"  autocomplete="off" /></td>
                    
                    <td><input disabled type="text" name="EARNING_HEADID_DESC_{{$key}}" id="EARNING_HEADID_DESC_{{$key}}" value="{{isset($row['HEADDESC'])?$row['HEADDESC']:''}}"  class="form-control"  autocomplete="off"  readonly /></td>

                    <td><input disabled  type="text" name="EARNING_TYPEID_CODE_{{$key}}" id="EARNING_TYPEID_CODE_{{$key}}" value="{{isset($row['TYPECODE'])?$row['TYPECODE']:''}} {{isset($row['TYPEDESC'])?'-'.$row['TYPEDESC']:''}}" class="form-control"  autocomplete="off" readonly  /></td>
                    <td hidden><input type="text" name="EARNING_TYPEID_REF_{{$key}}" id="EARNING_TYPEID_REF_{{$key}}" value="{{isset($row['EARNING_TYPEID_REF'])?$row['EARNING_TYPEID_REF']:''}}"   class="form-control"  autocomplete="off" /></td>

                    <td><input disabled  type="text" name="SQ_NO_{{$key}}" id="SQ_NO_{{$key}}" value="{{$key+1}}" class="form-control"  autocomplete="off" readonly style="width:50px;" /></td>

                    <td>
                      <select disabled name="AMT_FORMULA_{{$key}}" id="FOR_TYPE_{{$key}}" class="form-control"  autocomplete="off" onchange="select_formula(this.id,this.value)" >
                        <option value="">Select</option>  
                        <option {{isset($row['AMT_FORMULA']) && $row['AMT_FORMULA'] =='FORMULA'?'selected="selected"':''}} value="FORMULA">FORMULA</option>
                        <option {{isset($row['AMT_FORMULA']) && $row['AMT_FORMULA'] =='AMOUNT'?'selected="selected"':''}} value="AMOUNT">AMOUNT</option>
                      </select>
                    </td>

                    <td><input disabled type="text" name="FORMULA_{{$key}}" id="FORMULA_{{$key}}" value="{{isset($row['FORMULA'])?$row['FORMULA']:''}}" class="form-control"  autocomplete="off" readonly /></td>
                    <td><input disabled type="text" name="AMOUNT_{{$key}}" id="AMOUNT_{{$key}}" value="{{isset($row['AMOUNT'])?$row['AMOUNT']:''}}" class="form-control"  autocomplete="off" onkeypress="return isNumberDecimalKey(event,this)" readonly  /></td>
                    <td><input disabled type="text" name="REMARKS_{{$key}}" id="REMARKS_{{$key}}" value="{{isset($row['REMARKS'])?$row['REMARKS']:''}}" class="form-control"  autocomplete="off"/></td>

                    <td align="center" >
                      <button disabled class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                      <button disabled class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
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

<div id="modal_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='close_modal_popup' >&times;</button>
      </div>

      <div class="modal-body">
	      <div class="tablename"><p id="popup_title"></p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="first_table" class="display nowrap table  table-striped table-bordered" >
            <thead>
              <tr>
                <th class="ROW1">Select</th> 
                <th class="ROW2" id="popup_code"></th>
                <th class="ROW3" id="popup_desc"></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th class="ROW1"><span class="check_th">&#10004;</span></th>
                <td class="ROW2"><input type="text" id="first_input_search" class="form-control" onkeyup="first_input_search()"></td>
                <td class="ROW3"><input type="text" id="second_input_search" class="form-control" onkeyup="second_input_search()"></td>
              </tr>
            </tbody>
          </table>

          <table id="second_table" class="display nowrap table  table-striped table-bordered"  >
            <thead id="thead2">

            </thead>
            <tbody id="second_table_tbody">     
        
            </tbody>
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
//====================// ACTION BUTTON //====================//
$("#btnSaveFormData" ).click(function() {
  var formTrans = $("#frm_mst_view").validate();
  if(formTrans.valid()){
    validateForm("fnSaveData","update");
  }
});

$( "#btnApprove" ).click(function() {
  var formTrans = $("#frm_mst_view").validate();
  if(formTrans.valid()){
    validateForm("fnApproveData","approve");
  }
});

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
  $(".text-danger").hide();
});

function showError(pId,pVal){
  $("#"+pId+"").text(pVal);
  $("#"+pId+"").show();
}

function getFocus(){
  var FocusId = $("#FocusId").val();

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

//====================// ADD REMOVE //====================//
$("#EarningHead").on('click','.add', function() {
    var $tr = $(this).closest('table');
    var allTrs = $tr.find('.participantRow1').last();
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
    $tr.closest('table').append($clone);         
    var rowCount1 = $('#Row_Count1').val();
    rowCount1 = parseInt(rowCount1)+1;
    $('#Row_Count1').val(rowCount1);
    serialNo('EarningHead','participantRow1','SQ_NO');
    $clone.find('.remove').removeAttr('disabled');        
    
    event.preventDefault();
});

$("#EarningHead").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('.participantRow1').length;
    if (rowCount > 1) {

        $(this).closest('.participantRow1').remove();  
        var rowCount1 = $('#Row_Count1').val();
        rowCount1 = parseInt(rowCount1)-1;
        $('#Row_Count1').val(rowCount1);
        serialNo('EarningHead','participantRow1','SQ_NO');
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

function serialNo(table_id,row_id,input_id){
  var i=1;
  $('#'+table_id).find('.'+row_id).each(function(){
    var TextId = $(this).find("[id*="+input_id+"]").attr('id');
    $("#"+TextId).val(i);
    i++;
  });
}

//====================// SHORTING //====================//
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

//====================// EARNING/DEDUCTION //====================//

let first_table   = "#first_table";
let second_table  = "#second_table";
let table_header  = document.querySelectorAll(first_table + " th");

table_header.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(second_table, ".second_table_row", "td:nth-child(" + (i + 1) + ")");
  });
});

function first_input_search(){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("first_input_search");
  filter = input.value.toUpperCase();
  table = document.getElementById("second_table");
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

function second_input_search(){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("second_input_search");
  filter = input.value.toUpperCase();
  table = document.getElementById("second_table");
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
         
function select_head_type(id){
  var ROW_ID  = id.split('_').pop();

  $("#EARNING_HEADID_CODE_"+ROW_ID).val('');
  $("#EARNING_HEADID_REF_"+ROW_ID).val('');
  $("#EARNING_HEADID_DESC_"+ROW_ID).val('');
  $("#EARNING_TYPEID_CODE_"+ROW_ID).val('');
  $("#EARNING_TYPEID_REF_"+ROW_ID).val('');
  $("#FOR_TYPE_"+ROW_ID).val('');
  $("#FORMULA_"+ROW_ID).val('');
  $("#AMOUNT_"+ROW_ID).val('');
  $("#REMARKS_"+ROW_ID).val('');

}

function select_data(id){

  var ROW_ID      = id.split('_').pop();
  var HEAD_TYPE   = $("#HEAD_TYPE_"+ROW_ID).val();

  if(HEAD_TYPE =="EARNING"){
    get_data(ROW_ID,'{{route("master",[$FormId,"get_earning_deduction_head"])}}','Earning Head','Code','Description','EARNING');
  }
  else if(HEAD_TYPE =="DEDUCTION"){
    get_data(ROW_ID,'{{route("master",[$FormId,"get_earning_deduction_head"])}}','Deduction Head','Code','Description','DEDUCTION');
  }
}

function bind_data(fieldid,indexid,headtype){

  var desc1 = $.trim($(indexid).data("desc1"));
  var desc2 = $.trim($(indexid).data("desc2"));
  var desc3 = $.trim($(indexid).data("desc3"));
  var desc4 = $.trim($(indexid).data("desc4"));
  var desc5 = $.trim($(indexid).data("desc5"));
  var desc6 = $.trim($(indexid).data("desc6"));

  var CheckFocus  = "";
  var CheckExist  = []; 
  CheckExist.push('true');
  
  $('#EarningHead').find('.participantRow1').each(function(){

    var HEAD_TYPE           = $(this).find('[id*="HEAD_TYPE"]').val();
    var EARNING_HEADID_REF  = $(this).find('[id*="EARNING_HEADID_REF"]').val();

    if(headtype == HEAD_TYPE && desc1== EARNING_HEADID_REF){
      CheckExist.push('false');
      CheckFocus  = $(this).find("[id*=EARNING_HEADID_CODE]").attr('id');
      return false;
    }

  });

  if(jQuery.inArray("false", CheckExist) !== -1){
   
    $("#FocusId").val(CheckFocus);
    $("#alert").modal('show');
    $("#AlertMessage").text('Earning/Deduction Code Already Exist.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
  }
  else{

    $('#EARNING_HEADID_REF_'+fieldid).val(desc1);
    $('#EARNING_HEADID_CODE_'+fieldid).val(desc2);
    $('#EARNING_HEADID_DESC_'+fieldid).val(desc3);
    $('#EARNING_TYPEID_REF_'+fieldid).val(desc4);
    $('#EARNING_TYPEID_CODE_'+fieldid).val(desc5+' - '+desc6);

  }

  $("#modal_popup").hide();
  $("#first_input_search").val(''); 
  $("#second_input_search").val(''); 
  $("#second_table_tbody").html('');
  event.preventDefault();

}

$("#close_modal_popup").click(function(event){
  $("#modal_popup").hide();
});

function get_data(fieldid,path,title,code,desc,type){

  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  })

  $.ajax({
      url:path,
      type:'POST',
      data:{fieldid:fieldid,type:type},
      success:function(data) {
        $("#second_table_tbody").html(data);
        showSelectedCheck($("#EARNING_HEADID_REF_"+fieldid).val(),"SELECT_EARNING_HEADID_REF_"+fieldid);
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#second_table_tbody").html('');
      },
  });

  $("#popup_title").text(title);
  $("#popup_code").text(code);
  $("#popup_desc").text(desc);
  $("#modal_popup").show();

}

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

function select_formula(id,formula){
  var ROW_ID  = id.split('_').pop();

  $("#FORMULA_"+ROW_ID).val('');
  $("#AMOUNT_"+ROW_ID).val('');
  $("#REMARKS_"+ROW_ID).val('');

  if(formula =="FORMULA"){
    $("#FORMULA_"+ROW_ID).attr('readonly', false);
    $("#AMOUNT_"+ROW_ID).attr('readonly', true);
  }
  else{
    $("#AMOUNT_"+ROW_ID).attr('readonly', false);
    $("#FORMULA_"+ROW_ID).attr('readonly', true);
  }

}

//====================// NUMBER DECIMAL CHECK //====================//

function isNumberDecimalKey(evt){
  var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
  return false;

  return true;
}

//====================// VALIDATE FORM //====================//

function validateForm(saveAction,ActionMsg){
 
  $("#FocusId").val('');

  var SALARY_STRUC_NO   = $.trim($("#SALARY_STRUC_NO").val());
  var SALARY_STRUC_DESC = $.trim($("#SALARY_STRUC_DESC").val());
  var DODEACTIVATED     = $.trim($("#DODEACTIVATED").val());

  if(SALARY_STRUC_NO ===""){
    $("#FocusId").val('SALARY_STRUC_NO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Salary Structure No.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(SALARY_STRUC_DESC ===""){
    $("#FocusId").val('SALARY_STRUC_DESC');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Description.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else if($("#deactive-checkbox_0").is(":checked") == true && DODEACTIVATED ===""){
    $("#FocusId").val('DODEACTIVATED');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Date of De-Activated.');
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
    var allblank6 = [];

    allblank1.push('true');
    allblank2.push('true');
    allblank3.push('true');
    allblank4.push('true');
    allblank5.push('true');
    allblank6.push('true');

    var focustext1= "";
    var focustext2= "";
    var focustext3= "";
    var focustext4= "";
    var focustext5= "";
    var focustext6= "";

    $('#EarningHead').find('.participantRow1').each(function(){

      var HEAD_TYPE           = $.trim($(this).find("[id*=HEAD_TYPE]").val());
      var EARNING_HEADID_REF  = $.trim($(this).find("[id*=EARNING_HEADID_REF]").val());
      var EARNING_TYPEID_REF  = $.trim($(this).find("[id*=EARNING_TYPEID_REF]").val());
      var FOR_TYPE            = $.trim($(this).find("[id*=FOR_TYPE]").val());
      var FORMULA             = $.trim($(this).find("[id*=FORMULA]").val());
      var AMOUNT              = $.trim($(this).find("[id*=AMOUNT]").val());

      if(HEAD_TYPE ===""){
        allblank1.push('false');
        focustext1 = $(this).find("[id*=HEAD_TYPE]").attr('id');
      }
      else if(EARNING_HEADID_REF ===""){
        allblank2.push('false');
        focustext2 = $(this).find("[id*=EARNING_HEADID_CODE]").attr('id');
      }
      else if(EARNING_TYPEID_REF ===""){
        allblank3.push('false');
        focustext3 = $(this).find("[id*=EARNING_TYPEID_CODE]").attr('id');
      }
      else if(FOR_TYPE ===""){
        allblank4.push('false');
        focustext4 = $(this).find("[id*=FOR_TYPE]").attr('id'); 
      }
      else if(FOR_TYPE ==="FORMULA" && FORMULA ===""){
        allblank5.push('false');
        focustext5 = $(this).find("[id*=FORMULA]").attr('id'); 
      }
      else if(FOR_TYPE ==="AMOUNT" && AMOUNT ===""){
        allblank6.push('false');
        focustext6 = $(this).find("[id*=AMOUNT]").attr('id'); 
      }

    });

    if(jQuery.inArray("false", allblank1) !== -1){
      $("#FocusId").val(focustext1);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Earning/Deduction Type');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank2) !== -1){
      $("#FocusId").val(focustext2);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Earning/Deduction Code');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank3) !== -1){
      $("#FocusId").val(focustext3);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Earning/Deduction Head Type');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank4) !== -1){
      $("#FocusId").val(focustext4);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Amount / Formula');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank5) !== -1){
      $("#FocusId").val(focustext5);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Formula');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank6) !== -1){
      $("#FocusId").val(focustext6);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Amount');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else{

        $("#alert").modal('show');
        $("#AlertMessage").text('Do you want to '+ActionMsg+' to record.');
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

//====================// SAVE DETAILS //====================//

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

      if(data.success) {                   
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

//====================// APPROVE DETAILS //====================//

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

      if(data.success) {                   
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
</script>
@endpush