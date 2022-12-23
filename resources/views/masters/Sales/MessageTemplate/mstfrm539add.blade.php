@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Message Template</a></div>
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

<form id="master_form" method="POST" >
  <div class="container-fluid filter"> 
    @csrf
    <div class="inner-form"> 
      <div class="row">
        <div class="col-lg-2 pl"><p>Template Code*</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="TEMPLATE_CODE" id="TEMPLATE_CODE" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
          <script>docMissing(@json($docarray['FY_FLAG']));</script>
        </div>

        <div class="col-lg-2 pl"><p>Template Name*</p></div>
        <div class="col-lg-2 pl">
          <input type="text"   name="TEMPLATE_NAME"     id="TEMPLATE_NAME"      class="form-control"  autocomplete="off" />
        </div>

        <div class="col-lg-2 pl"><p>Message Type*</p></div>
        <div class="col-lg-2 pl">
          <select name="MESSAGE_TYPE" id="MESSAGE_TYPE" class="form-control"  onchange="getSmsType(this.value)" >
            <option value="">Select</option>
            <option value="SMS">SMS</option>
            <option value="Whatsapp">Whatsapp</option>
            <option value="Mail">Mail</option>
          </select>
        </div>
      </div>     

      <div class="row">      
        <div class="col-lg-2 pl"><p>Header*</p></div>
        <div class="col-lg-10 pl">
          <textarea name="HEADER" id="Headereditor" cols="118" rows="10"></textarea>
        </div>       
      </div>

     <BR><BR>

      <div class="row">
        <div class="col-lg-2 pl"><p>Subject*</p></div>
        <div class="col-lg-10 pl">
          <input type="text"   name="SUBJECT"     id="SUBJECT"      class="form-control"  autocomplete="off" />
        </div>
      </div>

      <div class="row">      
        <div class="col-lg-2 pl"><p>Message Body*</p></div>
        <div class="col-lg-10 pl">
          <textarea name="MESSAGE_BODY" id="messagebody" cols="118" rows="10" ></textarea>
        </div>       
      </div>

      <div class="row">
        <div class="col-lg-2 pl"><p>Footer*</p></div>
        <div class="col-lg-10 pl">
          <textarea name="FOOTER" id="footereditor" cols="118" rows="10" ></textarea>
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

function saveAction(action){
  validateForm(action);
}

function validateForm(action){

  var flag_status   = [];
  var flag_focus    = '';
  var flag_message  = '';
  var flag_tab_type = '';
  
  if($.trim($("#TEMPLATE_CODE").val()) ===""){
    $("#FocusId").val('TEMPLATE_CODE');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter Template Code.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#TEMPLATE_NAME").val()) ===""){
    $("#FocusId").val('TEMPLATE_NAME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter Template Name.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#MESSAGE_TYPE").val()) ===""){
    $("#FocusId").val('MESSAGE_TYPE');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Message Type.');
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
    window[customFnName]('{{route("master",[$FormId,"save"])}}');
  }
  else if(action ==="update"){
    window[customFnName]('{{route("master",[$FormId,"update"])}}');
  }
  else if(action ==="approve"){
    window[customFnName]('{{route("master",[$FormId,"Approve"])}}');
  }
  else{
    window.location.href = '{{route("master",[$FormId,"index"]) }}';
  }
});

window.fnSaveData = function (path){

  event.preventDefault();
  var Headereditor = CKEDITOR.instances.Headereditor.getData();
  var messagebody  = CKEDITOR.instances.messagebody.getData();
  var footereditor = CKEDITOR.instances.footereditor.getData();

  var TemplateCode = $("#TEMPLATE_CODE").val();
  var TemplateName = $("#TEMPLATE_NAME").val();
  var MessageType  = $("#MESSAGE_TYPE").val();
  var subjectName  = $("#SUBJECT").val();

  var trnsoForm = $("#master_form");
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
    data: {TemplateCode: TemplateCode,TemplateName: TemplateName,MessageType: MessageType,subjectName: subjectName,HEADER: Headereditor,MESSAGE_BODY: messagebody,FOOTER: footereditor },
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


$(document).ready(function () {  
  CKEDITOR.replace('Headereditor');
  CKEDITOR.replace('messagebody');
  CKEDITOR.replace('footereditor');
  CKEDITOR.instances.Headereditor.config.readOnly = true;
  CKEDITOR.instances.messagebody.config.readOnly = true;
  CKEDITOR.instances.footereditor.config.readOnly = true;

});

function getSmsType(value) {
  if(value=='SMS'){
    CKEDITOR.instances.messagebody.setReadOnly(false);
    CKEDITOR.instances.Headereditor.setReadOnly(true);  
    CKEDITOR.instances.footereditor.setReadOnly(true);
  }else if(value=='Whatsapp'){
    CKEDITOR.instances.messagebody.setReadOnly(false);
    CKEDITOR.instances.Headereditor.setReadOnly(true);  
    CKEDITOR.instances.footereditor.setReadOnly(true);
  }else if(value== ''){
    CKEDITOR.instances.messagebody.setReadOnly(true);
    CKEDITOR.instances.Headereditor.setReadOnly(true);  
    CKEDITOR.instances.footereditor.setReadOnly(true);
  }else{
    CKEDITOR.instances.messagebody.setReadOnly(false);
    CKEDITOR.instances.Headereditor.setReadOnly(false);  
    CKEDITOR.instances.footereditor.setReadOnly(false);    
  } 
}


</script>
@endpush