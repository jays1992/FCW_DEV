@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Fixed Deposit</a></div>
    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
      <button class="btn topnavbt" id="btnSaveFormData" onclick="saveAction('save')" ><i class="fa fa-floppy-o"></i> Save</button>
      <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" disabled="disabled" onclick="saveAction('approve')"><i class="fa fa-thumbs-o-up"></i> Approved</button>
      <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
      <button class="btn topnavbt" id="btnExit" onclick="return  window.location.href='{{route('home')}}'" ><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>

<form id="transaction_form" method="POST" >
  @csrf
  <div class="container-fluid filter">
    <div class="inner-form">

      <div class="row">
        <div class="col-lg-2 pl"><p>FD Code*</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="FD_CODE" id="FD_CODE" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
          <script>docMissing(@json($docarray['FY_FLAG']));</script>
        </div>
                          
        <div class="col-lg-2 pl"><p>FD Date*</p></div>
        <div class="col-lg-2 pl">
          <input type="date" name="FD_DATE" id="FD_DATE" value="{{date('Y-m-d')}}" onchange='checkPeriodClosing("{{$FormId}}",this.value,1),getDocNoByEvent("FD_CODE",this,@json($doc_req))' class="form-control" autocomplete="off" placeholder="dd/mm/yyyy" >
        </div>
      </div>

      <div class="row">               
        <div class="col-lg-2 pl"><p>Bank Account*</p></div>
        <div class="col-lg-2 pl">
          <input type="text"   name="BANK_AC_TEXT" id="BANK_AC_TEXT"  class="form-control" autocomplete="off" onclick="getAccountMaster('')" readonly/>
          <input type="hidden" name="BANK_AC" id="BANK_AC"            class="form-control" autocomplete="off" />  
        </div>
        
        <div class="col-lg-2 pl"><p>Account Number</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="BANK_AC_NO" id="BANK_AC_NO" class="form-control" autocomplete="off" readonly />
        </div>
      </div>

      <div class="row">               
        <div class="col-lg-2 pl"><p>FD Issued Bank Name*</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="FD_BANK_AC_TEXT" id="FD_BANK_AC_TEXT" class="form-control "  autocomplete="off" onclick="getAccountMaster('FD')" readonly/>
          <input type="hidden" name="FD_BANK_AC" id="FD_BANK_AC" class="form-control" autocomplete="off" />  
        </div>
        
        <div class="col-lg-2 pl"><p>Account Number</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="FD_BANK_AC_NO" id="FD_BANK_AC_NO" class="form-control" autocomplete="off" readonly />
        </div>
      </div>

      <div class="row">
        <div class="col-lg-2 pl"><p>Maturity Date</p></div>
        <div class="col-lg-2 pl">
          <input type="date" name="MATURITY_DATE" id="MATURITY_DATE" class="form-control" autocomplete="off" />
        </div>
        
        <div class="col-lg-2 pl"><p>In-Favour</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="IN_FAVOUR" id="IN_FAVOUR" class="form-control" autocomplete="off" />
        </div>
      </div>
                     
      <div class="row">
        <div class="col-lg-2 pl"><p>BG number Applicabe Y/N</p></div>
        <div class="col-lg-2 pl">
          <select  name="BG_NO_APPLICABLE" id="BG_NO_APPLICABLE" class="form-control" autocomplete="off" onchange="getBgAppl(this.value)" >
            <option value="">Select</option>
            <option value="YES">YES</option>
            <option value="NO">NO</option>
          </select>
        </div>

        <div class="col-lg-2 pl"><p>BG Number</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="BG_NO" id="BG_NO" class="form-control" autocomplete="off" />
        </div>

        <div class="col-lg-2 pl"><p>Principal Amount</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="PRINCIPLE_AMOUNT" id="PRINCIPLE_AMOUNT" class="form-control" autocomplete="off" onkeypress="return isNumberDecimalKey(event,this)" />
        </div>
      </div>  

      <div class="row">
        <div class="col-lg-2 pl"><p>Rate of Interest base</p></div>
        <div class="col-lg-2 pl">
          <select  name="RATE_OF_INTEREST_BASE" id="RATE_OF_INTEREST_BASE" class="form-control" autocomplete="off" >
            <option value="">Select</option>
            <option value="Quarterly">Quarterly</option>
            <option value="Monthly">Monthly</option>
            <option value="Yearly">Yearly</option>
            <option value="On-Maturity">On-Maturity</option>
          </select>
        </div>

        <div class="col-lg-2 pl"><p>Rate of Interest</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="RATE_OF_INTEREST" id="RATE_OF_INTEREST" class="form-control" autocomplete="off" onkeypress="return isNumberDecimalKey(event,this)" />
        </div>

        <div class="col-lg-2 pl"><p>Remarks</div>
        <div class="col-lg-2 pl">
          <input type="text" name="REMARKS" id="REMARKS" class="form-control" autocomplete="off" />
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

function getAccountMaster(ACTYPE){
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getAccountMaster"])}}',
    type:'POST',
    data:{ACTYPE:ACTYPE},
    success:function(data) {
      var html = '';

      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr>';
          html +='<td style="width:20%;" ><input type="checkbox" id="key_'+key+'" value="'+value.BID+'" onChange="getCheckedData(this)" data-code="'+value.BCODE+'" data-desc="'+value.NAME+'" data-acno="'+value.ACNO+'"  data-type="'+ACTYPE+'"  ></td>';
          html +='<td style="width:40%;" >'+value.BCODE+'</td>';
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

  $("#modal_title").text('Account Details');
  $("#modal_th1").text('Account Code');
  $("#modal_th2").text('Account Name');
  $("#modal").show();
}

function getCheckedData(data){
  var code  = $("#"+data.id).data("code");
  var desc  = $("#"+data.id).data("desc");
  var acno  = $("#"+data.id).data("acno");
  var type  = $("#"+data.id).data("type");

  if(type ==='FD'){
    $("#FD_BANK_AC").val(data.value);
    $("#FD_BANK_AC_TEXT").val(code+' - '+desc);
    $("#FD_BANK_AC_NO").val(acno);
  }
  else{
    $("#BANK_AC").val(data.value);
    $("#BANK_AC_TEXT").val(code+' - '+desc);
    $("#BANK_AC_NO").val(acno);
  }

  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  $("#modal").hide(); 
}

var formTrans = $("#transaction_form");
formTrans.validate();

function saveAction(action){
  if(formTrans.valid()){
    validateForm(action);
  }
}

function validateForm(action){

  if($.trim($("#FD_CODE").val()) ===""){
    $("#FocusId").val('FD_CODE');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter value in FD Code.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#FD_DATE").val()) ===""){
    $("#FocusId").val('FD_DATE');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select FD  Date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else if($.trim($("#BANK_AC").val()) ===""){
    $("#FocusId").val('BANK_AC_TEXT');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select bank account.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else if($.trim($("#FD_BANK_AC").val()) ===""){
    $("#FocusId").val('FD_BANK_AC_TEXT');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select FD issued bank name.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }   
  else if($.trim($("#MATURITY_DATE").val()) ===""){
    $("#FocusId").val('MATURITY_DATE');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Maturity Date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else if($.trim($("#BG_NO_APPLICABLE").val()) ===""){
    $("#FocusId").val('BG_NO_APPLICABLE');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select BG number applicabe Y/N.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }  
  else if($.trim($("#BG_NO_APPLICABLE").val()) ==="YES" && $.trim($("#BG_NO").val()) ===""){
    $("#FocusId").val('BG_NO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select BG number.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else if($.trim($("#PRINCIPLE_AMOUNT").val()) ===""){
    $("#FocusId").val('PRINCIPLE_AMOUNT');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter principal amount.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#RATE_OF_INTEREST_BASE").val()) ===""){
    $("#FocusId").val('RATE_OF_INTEREST_BASE');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select rate of interest base.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }  
  else if($.trim($("#RATE_OF_INTEREST").val()) ===""){
    $("#FocusId").val('RATE_OF_INTEREST');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter Rate of Interest.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }   
  else if(checkPeriodClosing('{{$FormId}}',$("#FD_DATE").val(),0) ==0){
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
  var trnsoForm = $("#transaction_form");
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

$(document).ready(function(){
  var lastdt = <?php echo json_encode($lastDocDate[0]->FD_DATE); ?>;
  var today = new Date(); 
  var current_date = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
  $('#FD_DATE').attr('min',lastdt);
  $('#FD_DATE').attr('max',current_date);
});

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}

function getBgAppl(value){
  if(value ==="YES"){
    $("#BG_NO").prop('readonly',false);
    $("#BG_NO").val('');
  }
  else{
    $("#BG_NO").prop('readonly',true);
    $("#BG_NO").val('');
  }
}
</script>
@endpush