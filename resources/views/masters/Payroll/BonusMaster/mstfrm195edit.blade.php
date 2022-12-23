@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[195,'index'])}}" class="btn singlebt">Bonus Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button id="btnSave"   class="btn topnavbt" tabindex="4"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt"  id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove"{{($objRights->APPROVAL1 || $objRights->APPROVAL2 || $objRights->APPROVAL3 || $objRights->APPROVAL4 || $objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-lock"></i> Approved</button>
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</a>
                        <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_edit" method="POST"  > 
          @CSRF
          {{isset($objResponse->BONUSID) ? method_field('PUT') : '' }}
          <div class="inner-form">
          







        

          <div class="row">
          <div class="col-lg-2 pl"><p>Bonus Code</p></div>
                  <div class="col-lg-2 pl">
                  <label> {{$objResponse->BONUS_CODE}} </label>
                    <input type="hidden" name="BONUSID" id="BONUSID" value="{{ $objResponse->BONUSID }}" />
                    <input type="hidden" name="BONUS_CODE" id="BONUS_CODE" value="{{ $objResponse->BONUS_CODE }}" autocomplete="off"  maxlength="20"   />
                    <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
                  </div>
                  <div class="col-lg-2 pl"><p>Description</p></div>
                  <div class="col-lg-5 pl">
                    <input type="text" name="BONUS_DESC" id="BONUS_DESC" class="form-control mandatory"  value="{{ $objResponse->BONUS_DESC }}" maxlength="200" tabindex="2"  />
                    <span class="text-danger" id="ERROR_BONUS_DESC"></span> 
                  </div>

                </div>


                <div class="row">
                  <div class="col-lg-2 pl"><p>Bonus Type</p></div>
                  <input type="radio"   name="BonusType"  id="bonus_type1" value="1" style=" margin-right: 10;" {{$objResponse->BONUS_TYPE == 1 ? "checked" : ""}}  >
                  <div class="col-lg-1 pl"><p>Bonus Rate (%)</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">
                        <input type="text" name="BONUS_RATE" id="BONUS_RATE"  value='{{$objResponse->BONUS_RATE == "null" ? "" : $objResponse->BONUS_RATE}}' class="form-control mandatory" autocomplete="off" maxlength="20" tabindex="1" />
                        <span class="text-danger" id="ERROR_BONUS_RATE"></span> 
                    </div>
                  </div>
                  <div class="col-lg-2 pl"><p>Maximum Basic Salary in a month</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">
                        <input type="text" name="BASIC_SALARY" id="BASIC_SALARY"   value='{{$objResponse->MAX_SALARY == "null" ? "" : $objResponse->MAX_SALARY}}' class="form-control mandatory" autocomplete="off" maxlength="20" tabindex="1"  />
                        <span class="text-danger" id="ERROR_BASIC_SALARY"></span> 
                    </div>
                  </div>
                  <div class="col-lg-1 pl"><p>Max Bonus</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                        <input type="text" name="MAX_BONUS" id="MAX_BONUS"  value='{{$objResponse->MAX_BONUS == "null" ? "" : $objResponse->MAX_BONUS}}' class="form-control mandatory" autocomplete="off" maxlength="20" tabindex="1"  />
                        <span class="text-danger" id="ERROR_MAX_BONUS"></span> 
                    </div>
                    
                  </div>
                  
                </div>

                <div class="row" style=" margin-left: 221px;">      
                  <input type="radio"   name="BonusType"  id="bonus_type2" value="2" style=" margin-right: 10;" {{$objResponse->BONUS_TYPE == 2 ? "checked" : ""}}  >
                  <div class="col-lg-1 pl"><p>Flat Bonus</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <input type="text" name="FLAT_BONUS" id="FLAT_BONUS"  value='{{$objResponse->BONUS_FLAT == "null" ? "" : $objResponse->BONUS_FLAT}}' class="form-control mandatory" autocomplete="off" maxlength="20" tabindex="1" style="width: 93px;
margin-left: 21px;" />
                        <span class="text-danger" id="ERROR_OT_RATE"></span> 
                    </div>
                  </div>                  
                </div>


          
             
              

              <div class="row">
                <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-2 pl pr">
                <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
                 value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
                </div>
                
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
                </div>
             </div>

          </div>
        </form>
    </div><!--purchase-order-view-->


@endsection
@section('alert')
<!-- Alert -->
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
            <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData">
              <div id="alert-active" class="activeYes"></div>Yes
            </button>
            <button class="btn alertbt" name='NoBtn' id="NoBtn"   data-funcname="fnUndoNo" >
              <div id="alert-active" class="activeNo"></div>No
            </button>
            <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">
              <div id="alert-active" class="activeOk"></div>OK</button>
              <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="margin-left: 90px;display:none;">
                <div id="alert-active" class="activeOk"></div>OK</button>
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Alert -->
@endsection
<!-- btnSave -->

@push('bottom-scripts')
<script>
$(document).ready(function(){

$("#OT_RATE").ForceNumericOnly();
  $('#OT_RATE').on('blur',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.00000')
          }
          event.preventDefault();
      });

});
$('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[195,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formDataMst = $( "#frm_mst_edit" );
     formDataMst.validate();

     $("#BONUS_CODE").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_BONUS_CODE").hide();
      validateSingleElemnet("BONUS_CODE");
         
    });

    $( "#BONUS_CODE" ).rules( "add", {
        required: true,
        nowhitespace: true,
        StringNumberRegex: true, //from custom.js
        messages: {
            required: "Required field.",
        }
    });

    $("#BONUS_DESC").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_BONUS_DESC").hide();
        validateSingleElemnet("BONUS_DESC");
    });

    $("#BONUS_DESC").keydown(function(){
        
        $("#ERROR_BONUS_DESC").hide();
        validateSingleElemnet("BONUS_DESC");
    });

    $( "#BONUS_DESC" ).rules( "add", {
        required: true,
        //StringRegex: true,  //from custom.js
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });





    $("#DODEACTIVATED").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_DODEACTIVATED").hide();
      validateSingleElemnet("DODEACTIVATED");
    });

    $( "#DODEACTIVATED" ).rules( "add", {
        required: true,
        DateValidate:true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field"
        }
    });

    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_edit" ).validate();
          validator.element( "#"+element_id+"" );
    }

    //validate
    $( "#btnSave" ).click(function() {

if(formDataMst.valid()){
    //set function nane of yes and no btn 
    if ($('input[name="BonusType"]:checked').length == 0) {
                 $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Please select Bonus Type.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();


          


              
    }else{

    $("#alert").modal('show');
    $("#AlertMessage").text('Do you want to save to record.');
    $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
    $("#YesBtn").focus();
    highlighFocusBtn('activeYes');

} }

});//btnSave

    
    //validate and approve
    $("#btnApprove").click(function() {
        
        if(formDataMst.valid()){
            //set function nane of yes and no btn 
            if ($('input[name="BonusType"]:checked').length == 0) {
                 $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Please select Bonus Type.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();


          


              
    } else{

    $("#alert").modal('show');
    $("#AlertMessage").text('Do you want to save to record.');
    $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name
    $("#YesBtn").focus();
    highlighFocusBtn('activeYes');

}

        }

    });//btnSave


    $("#YesBtn").click(function(){

      $("#alert").modal('hide');
      var customFnName = $("#YesBtn").data("funcname");
          window[customFnName]();

    }); //yes button

    
    window.fnSaveData = function (){
        
        //validate and save data
        event.preventDefault();

        var getDataForm = $("#frm_mst_edit");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[195,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.DESCRIPTIONS){
                        showError('ERROR_DESCRIPTIONS',data.errors.DESCRIPTIONS);
                    }
                   if(data.exist=='norecord') {

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
                    $("#frm_mst_edit").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn").focus();

                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });

    };// fnSaveData


    // save and approve 
    window.fnApproveData = function (){
        
        //validate and save data
        event.preventDefault();

        var getDataForm = $("#frm_mst_edit");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[195,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.DESCRIPTIONS){
                        showError('ERROR_DESCRIPTIONS',data.errors.DESCRIPTIONS);
                    }
                   if(data.exist=='norecord') {

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
                    $("#frm_mst_edit").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn").focus();

                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });

    };// fnApproveData

    //no button
    $("#NoBtn").click(function(){

      $("#alert").modal('hide');
      var custFnName = $("#NoBtn").data("funcname");
          window[custFnName]();

    }); //no button

   
    $("#OkBtn").click(function(){

        $("#alert").modal('hide');

        $("#YesBtn").show();
        $("#NoBtn").show();
        $("#OkBtn").hide();

        $(".text-danger").hide();
        window.location.href = '{{route("master",[195,"index"]) }}';

    }); ///ok button

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

    }); ////Undo button

   
    $("#OkBtn").click(function(){
      
        $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.reload();

   }//fnUndoYes


   window.fnUndoNo = function (){

      $("#BONUS_CODE").focus();

   }//fnUndoNo

   
   $("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $(".text-danger").hide();
});


    //
    function showError(pId,pVal){

      $("#"+pId+"").text(pVal);
      $("#"+pId+"").show();

    }  

    function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }

</script>
<script type="text/javascript">
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

$(function() { 
  //$("#DESCRIPTIONS").focus(); 
});





$(document).ready(function(){

$("#BONUS_RATE").ForceNumericOnly();
  $('#BONUS_RATE').on('blur',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.0000')
          }
          event.preventDefault();
      });
$("#BASIC_SALARY").ForceNumericOnly();
  $('#BASIC_SALARY').on('blur',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.00')
          }
          event.preventDefault();
      });
$("#MAX_BONUS").ForceNumericOnly();
  $('#MAX_BONUS').on('blur',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.00')
          }
          event.preventDefault();
      });
$("#FLAT_BONUS").ForceNumericOnly();
  $('#FLAT_BONUS').on('blur',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.00')
          }
          event.preventDefault();
      });
$("#FACTOR").ForceNumericOnly();
  $('#FACTOR').on('blur',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.0000')
          }
          event.preventDefault();
      });

      

$("#bonus_type1").click(function(){
$("#BONUS_RATE").attr("required", "true");
$("#BASIC_SALARY").attr("required", "true");
$("#MAX_BONUS").attr("required", "true");


$("#FLAT_BONUS").val('');
$("#FACTOR").val('');
$("#BONUS_RATE").prop('disabled', false);

$("#BASIC_SALARY").prop('disabled', false);
$("#MAX_BONUS").prop('disabled', false);
$("#FLAT_BONUS").prop('disabled', true);
$("#FACTOR").prop('disabled', true);
$(".FACTOR_TYPE").prop('disabled', true);
$(".FACTOR_TYPE").prop('checked', false);

});

$("#bonus_type2").click(function(){

$("#FLAT_BONUS").attr("required", "true");

$("#FLAT_BONUS").prop('disabled', false);
$("#BONUS_RATE").prop('disabled', true);
$("#BASIC_SALARY").prop('disabled', true);
$("#MAX_BONUS").prop('disabled', true);
$("#FACTOR").prop('disabled', true);
$(".FACTOR_TYPE").prop('disabled', true);
$(".FACTOR_TYPE").prop('checked', false);

$("#BONUS_RATE").val('');
$("#BASIC_SALARY").val('');
$("#MAX_BONUS").val('');
$("#FACTOR").val('');


});

$("#bonus_type3").click(function(){


$("#FLAT_BONUS").prop('disabled', true);

$("#BONUS_RATE").prop('disabled', true);
$("#BASIC_SALARY").prop('disabled', true);
$("#MAX_BONUS").prop('disabled', true);
$(".FACTOR_TYPE").prop('disabled', false);
$("#FACTOR").prop('disabled', false);
$("#FACTOR").attr("required", "true");

$("#BONUS_RATE").val('');
$("#BASIC_SALARY").val('');
$("#MAX_BONUS").val('');
$("#FLAT_BONUS").val('');


});


//for the checked bonus type 

if ($('#bonus_type1:checked').length != 0){
$("#BONUS_RATE").attr("required", "true");
$("#BASIC_SALARY").attr("required", "true");
$("#MAX_BONUS").attr("required", "true");


$("#FLAT_BONUS").val('');
$("#FACTOR").val('');
$("#BONUS_RATE").prop('disabled', false);

$("#BASIC_SALARY").prop('disabled', false);
$("#MAX_BONUS").prop('disabled', false);
$("#FLAT_BONUS").prop('disabled', true);
$("#FACTOR").prop('disabled', true);
$(".FACTOR_TYPE").prop('disabled', true);
$(".FACTOR_TYPE").prop('checked', false);

}

if ($('#bonus_type2:checked').length != 0){
  $("#FLAT_BONUS").attr("required", "true");

$("#FLAT_BONUS").prop('disabled', false);
$("#BONUS_RATE").prop('disabled', true);
$("#BASIC_SALARY").prop('disabled', true);
$("#MAX_BONUS").prop('disabled', true);
$("#FACTOR").prop('disabled', true);
$(".FACTOR_TYPE").prop('disabled', true);
$(".FACTOR_TYPE").prop('checked', false);

$("#BONUS_RATE").val('');
$("#BASIC_SALARY").val('');
$("#MAX_BONUS").val('');
$("#FACTOR").val('');

}


if ($('#bonus_type3:checked').length != 0){
  $("#FLAT_BONUS").prop('disabled', true);

$("#BONUS_RATE").prop('disabled', true);
$("#BASIC_SALARY").prop('disabled', true);
$("#MAX_BONUS").prop('disabled', true);
$(".FACTOR_TYPE").prop('disabled', false);
$("#FACTOR").prop('disabled', false);
$("#FACTOR").attr("required", "true");

$("#BONUS_RATE").val('');
$("#BASIC_SALARY").val('');
$("#MAX_BONUS").val('');
$("#FLAT_BONUS").val('');

}






});
</script>


@endpush