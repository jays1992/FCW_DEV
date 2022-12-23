
@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2">
    <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Salary Structure Master</a>
    </div>

    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
      <button class="btn topnavbt" id="btnSaveFormData" ><i class="fa fa-floppy-o"></i> Save</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
      <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
      <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>
   
<div class="container-fluid purchase-order-view filter">     
  <form id="frm_mst_add" method="POST"  > 
    @CSRF
    <div class="inner-form">
        
      <div class="row">
        <div class="col-lg-2 pl"><p>Salary Structure No</p></div>
        <div class="col-lg-2 pl">
          @if($objSON->SYSTEM_GRSR == "1")
            <input type="text" name="SALARY_STRUC_NO" id="SALARY_STRUC_NO" value="{{ $objAutoGenNo }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" >
          @endif
          @if($objSON->MANUAL_SR == "1")
            <input type="text" name="SALARY_STRUC_NO" id="SALARY_STRUC_NO" class="form-control mandatory" maxlength="{{$objSON->MANUAL_MAXLENGTH}}" autocomplete="off" style="text-transform:uppercase" >
          @endif
        
        </div>
        
        <div class="col-lg-2 pl"><p>Description</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="SALARY_STRUC_DESC" id="SALARY_STRUC_DESC" class="form-control mandatory" autocomplete="off" >
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
                    <th>Action <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="1"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr  class="participantRow1">

                    <td>
                      <select name="HEAD_TYPE_0" id="HEAD_TYPE_0" class="form-control"  autocomplete="off" onchange="select_head_type(this.id)" >
                        <option value="">Select</option> 
                        <option value="EARNING">EARNING</option>
                        <option value="DEDUCTION">DEDUCTION</option>
                      </select>
                    </td>
                  
                    <td><input  type="text" name="EARNING_HEADID_CODE_0" id="EARNING_HEADID_CODE_0" class="form-control"  autocomplete="off" onclick="select_data(this.id)"  readonly  /></td>
                    <td hidden><input type="text" name="EARNING_HEADID_REF_0" id="EARNING_HEADID_REF_0"   class="form-control"  autocomplete="off" /></td>
                    
                    <td><input type="text" name="EARNING_HEADID_DESC_0" id="EARNING_HEADID_DESC_0" class="form-control"  autocomplete="off"  readonly /></td>

                    <td><input  type="text" name="EARNING_TYPEID_CODE_0" id="EARNING_TYPEID_CODE_0" class="form-control"  autocomplete="off" readonly  /></td>
                    <td hidden><input type="text" name="EARNING_TYPEID_REF_0" id="EARNING_TYPEID_REF_0"   class="form-control"  autocomplete="off" /></td>

                    <td><input  type="text" name="SQ_NO_0" id="SQ_NO_0" value="1" class="form-control"  autocomplete="off" readonly style="width:50px;" /></td>

                    <td>
                      <select name="AMT_FORMULA_0" id="FOR_TYPE_0" class="form-control"  autocomplete="off" onchange="select_formula(this.id,this.value)" >
                        <option value="">Select</option>  
                        <option value="FORMULA">FORMULA</option>
                        <option value="AMOUNT">AMOUNT</option>
                      </select>
                    </td>

                    <td><input type="text" name="FORMULA_0" id="FORMULA_0" class="form-control"  autocomplete="off" readonly /></td>
                    <td><input type="text" name="AMOUNT_0" id="AMOUNT_0" class="form-control"  autocomplete="off" onkeypress="return isNumberDecimalKey(event,this)" readonly  /></td>
                    <td><input type="text" name="REMARKS_0" id="REMARKS_0" class="form-control"  autocomplete="off"/></td>

                    <td align="center" >
                      <button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                      <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                    </td>

                  </tr>
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
$( "#btnSaveFormData" ).click(function() {
  var formTrans = $("#frm_mst_add").validate();
  if(formTrans.valid()){
    validateForm();
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
  window.location.href = "{{route('master',[$FormId,'add'])}}";
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

function validateForm(){
 
  $("#FocusId").val('');

  var SALARY_STRUC_NO   = $.trim($("#SALARY_STRUC_NO").val());
  var SALARY_STRUC_DESC = $.trim($("#SALARY_STRUC_DESC").val());

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
      else if(FOR_TYPE =="FORMULA" && FORMULA ===""){
        allblank5.push('false');
        focustext5 = $(this).find("[id*=FORMULA]").attr('id'); 
      }
      else if(FOR_TYPE =="AMOUNT" && AMOUNT ===""){
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
      $("#AlertMessage").text('Please Enter Formula');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank6) !== -1){
      $("#FocusId").val(focustext6);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Enter Amount');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else{
      checkDuplicateCode();
    }
        
  }

}

//====================// CHECK DUPLICATE //====================//

function checkDuplicateCode(){
    
    var getDataForm = $("#frm_mst_add");
    var formData = getDataForm.serialize();
  
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
  
    $.ajax({
        url:'{{route("master",[$FormId,"codeduplicate"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
  
            if(data.exists) {
              $("#FocusId").val('SALARY_STRUC_NO');        
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Duplicate Record.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              return false;
  
            }
            else{
              $("#alert").modal('show');
              $("#AlertMessage").text('Do you want to save to record.');
              $("#YesBtn").data("funcname","fnSaveData");
              $("#OkBtn1").hide();
              $("#OkBtn").hide();
              $("#YesBtn").show();
              $("#NoBtn").show();
              $("#YesBtn").focus();
              highlighFocusBtn('activeYes');
            }                                
        },
        error:function(data){
          console.log("Error: Something went wrong.");
        },
    });
  }

//====================// SAVE DETAILS //====================//

window.fnSaveData = function (){

  event.preventDefault();
  var trnsoForm = $("#frm_mst_add");
  var formData = trnsoForm.serialize();

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
    url:'{{ route("master",[$FormId,"save"])}}',
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
</script>
@endpush