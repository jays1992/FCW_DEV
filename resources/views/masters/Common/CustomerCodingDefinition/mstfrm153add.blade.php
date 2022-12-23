@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2">
      <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Customer Coding Definition</a>
    </div>

    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled" ><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
      <button class="btn topnavbt" id="btnSave"  tabindex="7"  ><i class="fa fa-floppy-o"></i> Save</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled" ><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id='btnUndo' ><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel"  disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove"  disabled="disabled" ><i class="fa fa-thumbs-o-up"></i> Approved</button>
      <button class="btn topnavbt"  id="btnAttach"  disabled="disabled" ><i class="fa fa-link"></i> Attachment</button>
      <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>
   
<div class="container-fluid purchase-order-view filter">     
  <form id="frm_mst_add" method="POST"  > 
    @CSRF
    <div class="inner-form">
      <div class="row" >
        <div class="col-lg-2 pl"><p>Manual Series</p></div>
        <div class="col-lg-1 pl">
          <input type="checkbox" name="MANUAL_SR" id="MANUAL_SR" value="1" onchange="SeriesType('MANUAL_SR');" tabindex="1" >
        </div>
        <div class="col-lg-1 pl"><p>OR</p></div>
        <div class="col-lg-2 pl"><p>System generated</p></div>
        <div class="col-lg-1 pl">
          <input type="checkbox" name="SYSTEM_GRSR" id="SYSTEM_GRSR" value="1" checked onchange="SeriesType('SYSTEM_GRSR');" tabindex="2" >
        </div>
      </div>

      <div class="row"><div class="col-lg-2 pl"><p style="text-decoration: underline;font-weight:bold;font-size:13px;">Pattern for Manual Code</p></div></div>

      <div class="row"  >
        <div class="col-lg-2 pl"><p>Maximum Length</p></div>
        <div class="col-lg-1 pl col-md-offset-1">
            <input type="text" name="MANUAL_MAXLENGTH" id="MANUAL_MAXLENGTH"  class="form-control mandatory" autocomplete="off" onkeypress="return isNumberKey(event,this)"  maxlength="9" disabled tabindex="3" >
            <span class="text-danger error" id="ERROR_MANUAL_MAXLENGTH"></span>
        </div>
      </div>

      <div class="row"><div class="col-lg-2 pl"><p style="text-decoration: underline;font-weight:bold;font-size:13px;">Pattern for Auto Series</p></div></div>

      <div class="row">
          <div class="col-lg-2 pl"><p>Customer Code max digit</p></div>
          
          <div class="col-lg-1 pl col-md-offset-1">
            <input type="text" name="MAX_DIGIT" id="MAX_DIGIT" class="form-control mandatory" autocomplete="off"  maxlength="8"  onkeypress="return isNumberKey(event,this)" tabindex="4" >
          </div>

          <div class="col-lg-2 pl"><p>Number Series Start from</p> </div>
          <div class="col-lg-1 pl">
            <input type="text" name="NO_START" id="NO_START" class="form-control mandatory" autocomplete="off"  maxlength="8" onkeypress="return isNumberKey(event,this)" tabindex="5" >
            <span class="text-danger error" id="ERROR_NO_START"></span>
          </div>
          
          <div class="col-lg-2 pl"><p>Customer Code (Prefix)</p></div>
          <div class="col-lg-1 pl">
            <input type="text" name="PREFIX" id="PREFIX" class="form-control mandatory" autocomplete="off"  maxlength="4" onkeypress="return AlphaNumaric(event,this)" style="text-transform:uppercase" tabindex="6" >
            <span class="text-danger error" id="ERROR_PREFIX"></span>
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
            <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData">
              <div id="alert-active" class="activeYes"></div>Yes
            </button>
            <button class="btn alertbt" name='NoBtn' id="NoBtn"   data-funcname="fnUndoNo" >
              <div id="alert-active" class="activeNo"></div>No
            </button>
            <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">
                <div id="alert-active" class="activeOk"></div>OK</button>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('bottom-scripts')
<script>
$('#btnAdd').on('click', function() {
    var viewURL = '{{route("master",[$FormId,"add"])}}';
    window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
});

var formResponseMst = $( "#frm_mst_add" );
formResponseMst.validate();

$("#MANUAL_MAXLENGTH").blur(function(){
    $(this).val($.trim( $(this).val() ));
    $("#ERROR_MANUAL_MAXLENGTH").hide();
    validateSingleElemnet("MANUAL_MAXLENGTH");
});

$( "#MANUAL_MAXLENGTH" ).rules( "add", {
    required: true,
    OnlyNumberRegex:true,
    normalizer: function(value) {
        return $.trim(value);
    },
    messages: {
        required: "Required field"
    }
});

$("#MAX_DIGIT").blur(function(){
    $(this).val($.trim( $(this).val() ));
    $("#ERROR_MAX_DIGIT").hide();
    validateSingleElemnet("MAX_DIGIT");
});

$( "#MAX_DIGIT" ).rules( "add", {
    required: true,
    OnlyNumberRegex:true,
    normalizer: function(value) {
        return $.trim(value);
    },
    messages: {
        required: "Required field"
    }
});

$("#NO_START").blur(function(){
    $(this).val($.trim( $(this).val() ));
    $("#ERROR_NO_START").hide();
    validateSingleElemnet("NO_START");
});

$( "#NO_START" ).rules( "add", {
    required: true,
    OnlyNumberRegex:true,
    normalizer: function(value) {
        return $.trim(value);
    },
    messages: {
        required: "Required field"
    }
});

$("#PREFIX").blur(function(){
    $(this).val($.trim( $(this).val() ));
    $("#ERROR_PREFIX").hide();
    validateSingleElemnet("PREFIX");
});

$( "#PREFIX" ).rules( "add", {
    required: true,
    normalizer: function(value) {
        return $.trim(value);
    },
    messages: {
        required: "Required field"
    }
});

function validateSingleElemnet(element_id){
  var validator =$("#frm_mst_add" ).validate();
  if(validator.element( "#"+element_id+"" )){
        
  }
}

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
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").show();
              $("#AlertMessage").text(data.msg);
              $("#alert").modal('show');
              $("#OkBtn").focus();
            }  
            else{
              $("#alert").modal('show');
              $("#AlertMessage").text('Do you want to save to record.');
              $("#YesBtn").data("funcname","fnSaveData");  
              $("#YesBtn").focus();
              highlighFocusBtn('activeYes');
            }                                   
        },
        error:function(data){
          console.log("Error: Something went wrong.");
        },
    });
}
   
$( "#btnSave" ).click(function() {
    if(formResponseMst.valid()){

      var MANUAL_MAXLENGTH  =   $.trim($("#MANUAL_MAXLENGTH").val());
      
      var MAX_DIGIT_LEN = 0;
      if($("#SYSTEM_GRSR").prop("checked")==true){
        MAX_DIGIT_LEN = $.trim($("#MAX_DIGIT").val());
      }

      var PREFIX_LEN = 0;
      if( $("#SYSTEM_GRSR").prop("checked")==true ){
        PREFIX_LEN =  $.trim($("#PREFIX").val()).length;
      }

      TOTAL_SYSGEN_LEN =  + parseInt(MAX_DIGIT_LEN) + parseInt(PREFIX_LEN);

      if( $("#MANUAL_SR").prop("checked")==true && parseInt(MANUAL_MAXLENGTH)<1){
               
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text("In Manual Series, Pattern for Manual Code - Maximum Length should be greater than 0");
        $("#alert").modal('show');
        $("#OkBtn").focus();
        return false;
             
      }
      else if( $("#MANUAL_SR").prop("checked")==true && parseInt(MANUAL_MAXLENGTH)>10){
               
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text("In Manual Series, Pattern for Manual Code - Maximum Length can not be greater than 10");
        $("#alert").modal('show');
        $("#OkBtn").focus();
        return false;
      
      }
      if( $("#SYSTEM_GRSR").prop("checked")==true && parseInt(MAX_DIGIT_LEN)<1){
               
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text("In System generated, Vendor Code max digit should be greater than 0");
        $("#alert").modal('show');
        $("#OkBtn").focus();
        return false;
            
      }
      else if( $("#SYSTEM_GRSR").prop("checked")==true && parseInt(TOTAL_SYSGEN_LEN)>10){
               
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text("In System generated, Total length of System Generated No can not be greater than 10");
        $("#alert").modal('show');
        $("#OkBtn").focus();
        return false;
      
      }
      checkDuplicateCode();
    }
});

$("#YesBtn").click(function(){
    $("#alert").modal('hide');
    var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();
});


window.fnSaveData = function (){

    event.preventDefault();

    var getDataForm = $("#frm_mst_add");
    var formData = getDataForm.serialize();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("master",[$FormId,"save"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
            
            if(data.errors) {
                $(".text-danger").hide();

                if(data.exist=='duplicate') {

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
                $("#frm_mst_add").trigger("reset");

                $("#alert").modal('show');
                $("#OkBtn").focus();

            }
            
        },
        error:function(data){
          console.log("Error: Something went wrong.");
        },
    });
  
}
 
$("#NoBtn").click(function(){
  $("#alert").modal('hide');
  var custFnName = $("#NoBtn").data("funcname");
  window[custFnName]();
}); 
   
    
$("#OkBtn").click(function(){
    $("#alert").modal('hide');

    $("#YesBtn").show();  
    $("#NoBtn").show();   
    $("#OkBtn").hide();
    $(".text-danger").hide();
}); 

$("#btnUndo").click(function(){

    $("#AlertMessage").text("Do you want to erase entered information in this record?");
    $("#alert").modal('show');

    $("#YesBtn").data("funcname","fnUndoYes");
    $("#YesBtn").show();

    $("#NoBtn").data("funcname","fnUndoNo");
    $("#NoBtn").show();
    
    $("#OkBtn").hide();
    $("#NoBtn").focus();
    highlighFocusBtn('activeNo');
    
});
    
$("#OkBtn").click(function(){
  $("#alert").modal('hide');
});

window.fnUndoYes = function (){
  window.location.href = "{{route('master',[$FormId,'add'])}}";
}

window.fnUndoNo = function (){
  //$("#CGID_REF").focus();
}

function showError(pId,pVal){
  $("#"+pId+"").text(pVal);
  $("#"+pId+"").show();
}

function highlighFocusBtn(pclass){
    $(".activeYes").hide();
    $(".activeNo").hide();
    
    $("."+pclass+"").show();
}


function SeriesType(type){
  if(type =="MANUAL_SR"){
    if($("#MANUAL_SR").prop("checked") == true){
      $("#SYSTEM_GRSR").prop("checked", false);
      $("#MANUAL_MAXLENGTH").attr('disabled', false);
      $("#MANUAL_MAXLENGTH").val('');
      AutoSeriesEnableDisable(true);
    }
    else{
      $("#SYSTEM_GRSR").prop("checked", true);
      $("#MANUAL_MAXLENGTH").attr('disabled', true);
      $("#MANUAL_MAXLENGTH").val('');
      AutoSeriesEnableDisable(false);
    }
  }
  else if(type =="SYSTEM_GRSR"){
    if($("#SYSTEM_GRSR").prop("checked") == true){
      $("#MANUAL_SR").prop("checked", false);
      $("#MANUAL_MAXLENGTH").attr('disabled', true);
      $("#MANUAL_MAXLENGTH").val('');
      AutoSeriesEnableDisable(false);
    }
    else{
      $("#MANUAL_SR").prop("checked", true);
      $("#MANUAL_MAXLENGTH").attr('disabled', false);
      $("#MANUAL_MAXLENGTH").val('');
      AutoSeriesEnableDisable(true);
    }
  }
}

function AutoSeriesEnableDisable(type){
  $("#MAX_DIGIT").attr('disabled', type);
  $("#MAX_DIGIT").val('');
  $("#NO_START").attr('disabled', type);
  $("#NO_START").val('');
  $("#PREFIX").attr('disabled', type);
  $("#PREFIX").val('');
}
</script>

@endpush