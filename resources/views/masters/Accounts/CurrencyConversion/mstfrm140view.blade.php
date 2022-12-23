@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[140,'index'])}}" class="btn singlebt">Currency Conversion</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_edit" method="POST"  > 
          @CSRF
          {{isset($objResponse->CRCOID) ? method_field('PUT') : '' }}
          <div class="inner-form">
          
              
          <div class="row">
              <div class="col-lg-1 pl"><p>Effective Date</p></div>
              <div class="col-lg-2 pl">
                    <input type="hidden" name="CRCOID" id="CRCOID" value="{{ $objResponse->CRCOID }}" />
                    <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
                    
                    <input type="date" name="EFFDATE" id="EFFDATE" readonly value="{{isset($objResponse->EFFDATE) && $objResponse->EFFDATE !="" && $objResponse->EFFDATE !="1900-01-01" ? $objResponse->EFFDATE:''}}" class="form-control mandatory" autocomplete="off" tabindex="1" placeholder="dd/mm/yyyy" />
                    <span class="text-danger" id="ERROR_EFFDATE"></span>
              </div>
              
              <!-- <div class="col-lg-1 pl col-md-offset-1"><p>End Date</p></div>
              <div class="col-lg-2 pl">
                    <input type="date" name="ENDDATE" id="ENDDATE" readonly  value="{{isset($objResponse->ENDDATE) && $objResponse->ENDDATE !="" && $objResponse->ENDDATE !="1900-01-01" ? $objResponse->ENDDATE:''}}" class="form-control mandatory" autocomplete="off" tabindex="2" placeholder="dd/mm/yyyy"  />
                    <span class="text-danger" id="ERROR_ENDDATE"></span> 
              </div> -->
            </div>

            <div class="row">
              <div class="col-lg-3 pl"><p>From</p></div>
              <div class="col-lg-3 pl col-md-offset-1"><p>To</p></div>
            </div>

            <div class="row">
			<div class="col-lg-1 pl"><p>Currency</p></div>
			<div class="col-lg-2 pl">

      <input type="hidden" name="FROMCRID_REF" id="FROMCRID_REF" value="{{ $objResponse->FROMCRID_REF }}" />

				<select  class="form-control mandatory" disabled tabindex="3" >
					<option value="" selected >Select</option>
          @foreach($objCurList as $Cur)
          <option {{isset($objResponse->FROMCRID_REF) && $objResponse->FROMCRID_REF==$Cur->CRID?'selected="selected"':'' }} value="{{$Cur->CRID}}">{{$Cur->CRCODE.' - '.$Cur->CRDESCRIPTION}}</option>
          @endforeach
				</select>
        <span class="text-danger" id="ERROR_FROMCRID_REF"></span>
			</div>
			
			<div class="col-lg-1 pl col-md-offset-1"><p>Currency</p></div>
			<div class="col-lg-2 pl ">
      <input type="hidden" name="TOCRID_REF" id="TOCRID_REF" value="{{ $objResponse->TOCRID_REF }}" />

				<select  class="form-control mandatory" disabled tabindex="4" >
					<option value="" selected >Select</option>
          @foreach($objCurList1 as $Cur)
          <option {{isset($objResponse->TOCRID_REF) && $objResponse->TOCRID_REF==$Cur->CRID?'selected="selected"':'' }} value="{{$Cur->CRID}}">{{$Cur->CRCODE.' - '.$Cur->CRDESCRIPTION}}</option>
          @endforeach
				</select>
        <span class="text-danger" id="ERROR_TOCRID_REF"></span>
			</div>
		</div>	
		
		<div class="row">
			<div class="col-lg-1 pl"><p>Amount</p></div>
			<div class="col-lg-1 pl">
				<input type="text" name="FRAMOUNT" id="FRAMOUNT" class="form-control mandatory" readonly value="{{ old('FRAMOUNT',$objResponse->FRAMOUNT) }}"  maxlength="9" tabindex="5" >
        <span class="text-danger" id="ERROR_FRAMOUNT"></span>
      </div>
			
			<div class="col-lg-1 pl col-md-offset-2"><p>Amount</p></div>
			<div class="col-lg-1 pl ">
				<input type="text" name="TOAMOUNT" id="TOAMOUNT" class="form-control mandatory" readonly value="{{ old('TOAMOUNT',$objResponse->TOAMOUNT) }}"  maxlength="9" tabindex="6" >
        <span class="text-danger" id="ERROR_TOAMOUNT"></span>
      </div>
		</div>

              

              <div class="row">
                <div class="col-lg-1 pl"><p>De-Activated</p></div>
                <div class="col-lg-1 pl pr">
                <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
                 value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="7"  >
                </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="8" placeholder="dd/mm/yyyy"  />
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
//date validate
$.validator.addMethod("ToDateValidate", function(value,element) {
var fdate=$("#EFFDATE").val();
var today = new Date(fdate); 
var d = new Date(value); 
today.setHours(0, 0, 0, 0) ;
d.setHours(0, 0, 0, 0) ;

if(this.optional(element) || d < today){
    return false;
}
else {
    return true;
}
}, "Less date not allow");

$('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[140,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formDataMst = $( "#frm_mst_edit" );
     formDataMst.validate();

     $("#EFFDATE").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_EFFDATE").hide();
      validateSingleElemnet("EFFDATE");
         
    });

    $( "#EFFDATE" ).rules( "add", {
        required: true,
        messages: {
            required: "Required field.",
        }
    });


    $("#FROMCRID_REF").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_FROMCRID_REF").hide();
        validateSingleElemnet("FROMCRID_REF");
    });

    $( "#FROMCRID_REF" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#TOCRID_REF").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_TOCRID_REF").hide();
        validateSingleElemnet("TOCRID_REF");
    });

    $( "#TOCRID_REF" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#FRAMOUNT").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_FRAMOUNT").hide();
        validateSingleElemnet("FRAMOUNT");
    });

    $( "#FRAMOUNT" ).rules( "add", {
        required: true,
        OnlyNumberDec:true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#TOAMOUNT").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_TOAMOUNT").hide();
        validateSingleElemnet("TOAMOUNT");
    });

    $( "#TOAMOUNT" ).rules( "add", {
        required: true,
        OnlyNumberDec:true,
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
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
            $("#YesBtn").focus();
            highlighFocusBtn('activeYes');

        }

    });//btnSave

    
    //validate and approve
    $("#btnApprove").click(function() {
        
        if(formDataMst.valid()){
            //set function nane of yes and no btn 
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name of approval
            $("#YesBtn").focus();
            highlighFocusBtn('activeYes');

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
            url:'{{route("mastermodify",[140,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.ENDDATE){
                        showError('ERROR_ENDDATE',data.errors.ENDDATE);
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
            url:'{{route("mastermodify",[140,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.ENDDATE){
                        showError('ERROR_ENDDATE',data.errors.ENDDATE);
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
        window.location.href = '{{route("master",[140,"index"]) }}';

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

      $("#EFFDATE").focus();

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
  //$("#ENDDATE").focus(); 
});
</script>


@endpush