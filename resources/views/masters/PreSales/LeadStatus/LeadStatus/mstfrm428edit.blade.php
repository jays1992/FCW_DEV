@extends('layouts.app')
@section('content')

            <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Lead Status</a>
                </div>
                  <div class="col-lg-10 topnav-pd">
                    <button href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                    <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                    <button id="btnSave" onclick="submitData('fnSaveData')" class="btn topnavbt" tabindex="4"><i class="fa fa-save"></i> Save</button>
                    <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
                    <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                    <button class="btn topnavbt"  id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
                    <button class="btn topnavbt" id="btnCancel" disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                    <button class="btn topnavbt" id="btnApprove" onclick="submitData('fnApproveData')"><i class="fa fa-lock"></i> Approved</button>
                    <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</a>
                    <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
                  </div>
              </div>
            </div>
   
      <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_edit" method="POST"  > 
          @CSRF
          {{isset($objResponse->ID) ? method_field('PUT') : '' }}
          <div class="inner-form">
              <div class="row">
                  <div class="col-lg-2 pl"><p>Lead Status Code</p></div>
                  <div class="col-lg-2 pl">
                    <label> {{$objResponse->LEAD_STATUSCODE}} </label>
                    <input type="hidden" name="LEAD_STATUSCODE" id="LEAD_STATUSCODE" value="{{ $objResponse->ID }}" />
                    <input type="hidden" name="LEAD_STATUSCODE" id="LEAD_STATUSCODE" value="{{ $objResponse->LEAD_STATUSCODE }}" autocomplete="off"  maxlength="20"   />
                </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Lead Status Name</p></div>
                  <div class="col-lg-5 pl">
                    <input {{$ActionStatus}} type="text" name="LEAD_STATUSNAME" id="LEAD_STATUSNAME" class="form-control mandatory" value="{{ old('LEAD_STATUSNAME',$objResponse->LEAD_STATUSNAME) }}" maxlength="200" tabindex="1"  />
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>De-Activated</p></div>
                  <div class="col-lg-1 pl pr">
                  <input {{$ActionStatus}} type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
                   value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
                  </div>
                  
                  <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                  <div class="col-lg-2 pl">
                    <input {{$ActionStatus}} type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
                  </div>
               </div>
          </div>
        </form>
    </div>


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

  $('#btnAdd').on('click', function() {
  var viewURL = '{{route("master",[$FormId,"add"])}}';
  window.location.href=viewURL;
  });


  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });


    var formDataMst = $( "#frm_mst_edit" );
     formDataMst.validate();
      $("#LEAD_STATUSNAME").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_DESCRIPTIONS").hide();
        validateSingleElemnet("LEAD_STATUSNAME");
      });

        $("#LEAD_STATUSNAME").keydown(function(){
        $("#ERROR_DESCRIPTIONS").hide();
        validateSingleElemnet("LEAD_STATUSNAME");
      });

        $( "#LEAD_STATUSNAME" ).rules( "add", {
        required: true,
        normalizer: function(value) {
          return $.trim(value);
        },
        messages: {
        required: "Required field"
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


        function validateSingleElemnet(element_id){
        var validator =$("#frm_mst_edit" ).validate();
        validator.element( "#"+element_id+"" );
        }


function submitData(type){
  if(formDataMst.valid()){
    $("#alert").modal('show');
    $("#AlertMessage").text('Do you want to save to record.');
    $("#YesBtn").data("funcname",type);
    $("#YesBtn").focus();
    highlighFocusBtn('activeYes');
  }
}

window.fnSaveData = function (){
  submitForm('update');
};

window.fnApproveData = function (){
  submitForm('approve');
}
    
function submitForm(requestType){

  var getDataForm = $("#frm_mst_edit");
  var formData = getDataForm.serialize() + "&requestType=" + requestType ;
  //var formData = getDataForm.append(requestType);
  
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{route("mastermodify",[$FormId,"update"])}}',
      type:'POST',
      data:formData,
      success:function(data) {

        if(data.errors) {
        $(".text-danger").hide();
        if(data.errors.LEAD_STATUSNAME){
        showError('ERROR_DESCRIPTIONS',data.errors.LEAD_STATUSNAME);
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

}



$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName = $("#YesBtn").data("funcname");
  window[customFnName]();
});

    

      


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
        window.location.href = '{{route("master",[$FormId,"index"]) }}';

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

      $("#LEAD_STATUSCODE").focus();

   }//fnUndoNo


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
  //$("#LEAD_STATUSNAME").focus(); 
});
</script>


@endpush