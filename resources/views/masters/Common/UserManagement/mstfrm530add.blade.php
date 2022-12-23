
@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Reset Password</a></div>
    
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
  </div>
</div>
   
<form id="add_trn_form" method="POST"  >

  
        
    @csrf
    <div class="container-fluid filter">

      <div class="inner-form">


      <div class="row">
            <div class="col-lg-2 pl"><p>User Code</p></div>
            <div class="col-lg-2 pl">
              <label for="">{{$UserDetails["USER_CODE"]}}</label>
              </div>
            
            <div class="col-lg-2 pl"><p>User Name</p> </div>
            <div class="col-lg-2 pl">
            <label for="">{{$UserDetails["USER_NAME"]}}</label>
            </div>

      
        </div>
      
        <div class="row">
         <!--   <div class="col-lg-2 pl"><p>Current Password</p></div>
            <div class="col-lg-2 pl">
                <input type="password" name="CURRENT_PASSWORD" id="CURRENT_PASSWORD" class="form-control mandatory" maxlength="50" autocomplete="off"  autofocus >
            </div>-->
            
            <div class="col-lg-2 pl"><p>New Password      <i class="fa fa-info-circle" data-toggle="tooltip"  
            title="8 to 15 Characters, One lowercase letter,  One Uppercase letter, One numeric digit, One special character."></i>    </p> </div>
            <div class="col-lg-2 pl">
            <input type="password" name="NEW_PASSWORD" id="NEW_PASSWORD" class="form-control mandatory" maxlength="15" autocomplete="off"   >
            </div>

            <div class="col-lg-2 pl"><p>Confirm New Password</p></div>
			      <div class="col-lg-2 pl">
            <input type="password" name="CONFIRM_NEW_PASSWORD" id="CONFIRM_NEW_PASSWORD" class="form-control mandatory" maxlength="15" autocomplete="off"   >
              
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
          <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;"><div id="alert-active" class="activeOk"></div>OK</button>
          <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;" onclick="getFocus()"><div id="alert-active" class="activeOk1"></div>OK</button>
          <input type="hidden" id="FocusId" >
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>


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



//================================== ONLOAD FUNCTION ==================================



$(document).ready(function(e) {

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
    window.location.href = "{{route('master',[$FormId,'add'])}}";
  }

});

</script>

@endpush

@push('bottom-scripts')
<script>

var formTrans = $("#add_trn_form");
formTrans.validate();

$( "#btnSaveFormData" ).click(function() {
 
  if(formTrans.valid()){
    validateForm();
  }
});

function validateForm(){
 
  $("#FocusId").val('');

  var password_format            =  /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,15}$/;
  //var CURRENT_PASSWORD           = $.trim($("#CURRENT_PASSWORD").val());
  var NEW_PASSWORD               = $.trim($("#NEW_PASSWORD").val());
  var CONFIRM_NEW_PASSWORD       = $.trim($("#CONFIRM_NEW_PASSWORD").val());
 
  // if(CURRENT_PASSWORD ===""){
  //   $("#FocusId").val('CURRENT_PASSWORD');        
  //   $("#YesBtn").hide();
  //   $("#NoBtn").hide();
  //   $("#OkBtn1").show();
  //   $("#AlertMessage").text('Please Enter Current Password.');
  //   $("#alert").modal('show');
  //   $("#OkBtn1").focus();
  //   return false;
  // }
  // if(CURRENT_PASSWORD !='{{$UserDetails["USER_PASSWORD"]}}'){
  //   $("#FocusId").val('CURRENT_PASSWORD');        
  //   $("#YesBtn").hide();
  //   $("#NoBtn").hide();
  //   $("#OkBtn1").show();
  //   $("#AlertMessage").text('Please Enter a Valid Current Password.');
  //   $("#alert").modal('show');
  //   $("#OkBtn1").focus();
  //   return false;
  // }
  // else 
  if(NEW_PASSWORD ===""){
    $("#FocusId").val('NEW_PASSWORD');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter New Password');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if (!NEW_PASSWORD.match(password_format)){
    $("#FocusId").val('NEW_PASSWORD');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('New Password should contain 8 to 15 characters, at least one lowercase letter, one uppercase letter, one numeric digit, and one special character.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(CONFIRM_NEW_PASSWORD ===""){
    $("#FocusId").val('CONFIRM_NEW_PASSWORD');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Confirm New Password');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  
  else if(NEW_PASSWORD != CONFIRM_NEW_PASSWORD){
    $("#FocusId").val('CONFIRM_NEW_PASSWORD');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('New Password and confirm New Password does not match please check.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }else{
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
        
  

}



$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

});

window.fnSaveData = function (){


event.preventDefault();

    var trnsoForm = $("#add_trn_form");
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
    url:'{{ route("master",[$FormId,"save"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $(".buttonload").hide(); 
      $("#btnSaveFormData").show();   
      $("#btnApprove").prop("disabled", false);
       
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.PMSL_NO){
                showError('ERROR_PMSL_NO',data.errors.PMSL_NO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in Schedule NO.');
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
        $(".buttonload").hide(); 
        $("#btnSaveFormData").show();   
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

//================================== USER DEFINE FUNCTION ==================================

</script>
@endpush