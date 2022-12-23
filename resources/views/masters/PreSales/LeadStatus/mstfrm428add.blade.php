@extends('layouts.app')
@section('content')

    <div class="container-fluid topnav">
      <div class="row">
          <div class="col-lg-2">
          <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Lead Status</a>
          </div>
          <div class="col-lg-10 topnav-pd">
          <button class="btn topnavbt" id="btnAdd" disabled="disabled" ><i class="fa fa-plus"></i> Add</button>
          <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
          <button class="btn topnavbt" id="btnSave"  tabindex="3"  ><i class="fa fa-save"></i> Save</button>
          <button class="btn topnavbt" id="btnView" disabled="disabled" ><i class="fa fa-eye"></i> View</button>
          <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
          <button class="btn topnavbt" id='btnUndo' ><i class="fa fa-undo"></i> Undo</button>
          <button class="btn topnavbt" id="btnCancel"  disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
          <button class="btn topnavbt" id="btnApprove"  disabled="disabled" ><i class="fa fa-lock"></i> Approved</button>
          <button class="btn topnavbt"  id="btnAttach"  disabled="disabled" ><i class="fa fa-link"></i> Attachment</button>
          <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
        </div>
      </div>
  </div>
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_add" method="POST"  > 
          @CSRF
          <div class="inner-form">
                <div class="row">
                  <div class="col-lg-2 pl"><p>Lead Status Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">
                        @if(!empty($objDD))
                        @if($objDD->SYSTEM_GRSR == "1")
                        <input type="text" name="LEAD_STATUSCODE" id="LEAD_STATUSCODE" value="{{ isset($objDOCNO)?$objDOCNO:'' }}" class="form-control mandatory" tabindex="1" maxlength="10" autocomplete="off" readonly style="text-transform:uppercase" autofocus >
                        @endif
                        @if($objDD->MANUAL_SR == "1")
                        <input type="text" name="LEAD_STATUSCODE" id="LEAD_STATUSCODE" value="{{ old('LEAD_STATUSCODE') }}" class="form-control mandatory"  maxlength="{{$objDD->MANUAL_MAXLENGTH}}" tabindex="1" autocomplete="off" style="text-transform:uppercase" autofocus >
                        @endif
                        @endif  
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Lead Status Name</p></div>
                  <div class="col-lg-5 pl">
                    <input type="text" name="LEAD_STATUSNAME" id="LEAD_STATUSNAME" class="form-control mandatory" value="{{ old('LEAD_STATUSNAME') }}" maxlength="200" tabindex="2"  />
                  </div>
                </div>
          </div>
        </form>
    </div>


@endsection
@section('alert')
<!-- Alert -->
<div id="alert" class="modal"  role="dialog"  data-backdrop="static">
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
                <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;">
                  <div id="alert-active" class="activeOk1"></div>OK</button>
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

    var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();

      $( "#LEAD_STATUSCODE" ).rules( "add", {
        required: true,
        nowhitespace: true,
        messages: {
        required: "Required field.",
        }
      });
      $( "#LEAD_STATUSNAME" ).rules( "add", {
        required: true,
        normalizer: function(value) {
         return $.trim(value);
        },
        messages: {
        required: "Required field."
        }
      });

    
  $( "#btnSave" ).click(function() {
      if(formResponseMst.valid()){
      var LEAD_STATUSCODE          =   $.trim($("#LEAD_STATUSCODE").val());
      if(LEAD_STATUSCODE ===""){
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").hide();  
        $("#OkBtn").show();              
        $("#AlertMessage").text('Please enter Lead Status Code.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        return false;
      }
        $("#alert").modal('show');
        $("#AlertMessage").text('Do you want to save to record.');
        $("#YesBtn").data("funcname","fnSaveData");
        $("#YesBtn").focus();
        highlighFocusBtn('activeYes');
      }
    });

    
      $("#YesBtn").click(function(){
        $("#alert").modal('hide');
        var customFnName = $("#YesBtn").data("funcname");
          window[customFnName]();
      });


      window.fnSaveData = function (){
        event.preventDefault();
        $("#OkBtn1").hide();
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
              if(data.errors.LEAD_STATUSCODE){
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text("Post Level Code is "+data.errors.LEAD_STATUSCODE);
                $("#alert").modal('show');
                $("#OkBtn").focus();
              }
              if(data.exist=='duplicate') {
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#OkBtn1").hide();
                $("#AlertMessage").text(data.msg);
                $("#alert").modal('show');
                $("#OkBtn").focus();
              }
            }
            if(data.success) {                   
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#OkBtn").hide();
              $("#AlertMessage").text(data.msg);
              $(".text-danger").hide();
              $("#frm_mst_add").trigger("reset");
              $("#alert").modal('show');
              $("#OkBtn1").focus();
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
          $("#OkBtn1").hide();
          $(".text-danger").hide();
          $("#LEAD_STATUSCODE").focus();
          });

    
          $("#btnUndo").click(function(){
              $("#AlertMessage").text("Do you want to erase entered information in this record?");
              $("#alert").modal('show');
              $("#YesBtn").data("funcname","fnUndoYes");
              $("#YesBtn").show();
              $("#NoBtn").data("funcname","fnUndoNo");
              $("#NoBtn").show();
              $("#OkBtn").hide();
              $("#OkBtn1").hide();
              $("#NoBtn").focus();
              highlighFocusBtn('activeNo');
              });

              $("#OkBtn1").click(function(){
              $("#alert").modal('hide');
              $("#YesBtn").show();
              $("#NoBtn").show(); 
              $("#OkBtn").hide();
              $("#OkBtn1").hide();
              $(".text-danger").hide();
              window.location.href = "{{route('master',[$FormId,'index'])}}";
              }); 

    
          $("#OkBtn").click(function(){
            $("#alert").modal('hide');
          });


        window.fnUndoYes = function (){
          window.location.href = "{{route('master',[$FormId,'add'])}}";
        }


      window.fnUndoNo = function (){
      $("#LEAD_STATUSCODE").focus();
      }

    function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       $("."+pclass+"").show();
    }

</script>
@endpush