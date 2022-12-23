@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[388,'index'])}}" class="btn singlebt">Template Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button id="btnSave"   class="btn topnavbt" tabindex="4"><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt"  id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove"{{($objRights->APPROVAL1 || $objRights->APPROVAL2 || $objRights->APPROVAL3 || $objRights->APPROVAL4 || $objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</a>
                        <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_edit" method="POST"  > 
          @CSRF
          {{isset($objResponse->TEMPLATEID) ? method_field('PUT') : '' }}
          <div class="inner-form">
          
              
              <div class="row">
                  <div class="col-lg-2 pl"><p>Template Name</p></div>

                  <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">
                      <input type="hidden" name="TEMPLATEID" id="TEMPLATEID" value="{{ $objResponse->TEMPLATEID }}" />
                        <input type="text" name="TEMPLATE_NAME" id="TEMPLATE_NAME" value="{{ $objResponse->TEMPLATE_NAME }}" class="form-control mandatory" autocomplete="off" maxlength="20" tabindex="1" />
                        <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
                        <span class="text-danger" id="ERROR_TEMPLATE_NAME"></span> 
                    </div>
                  </div>
                  
                <div class="col-lg-2 pl"><p>Voucher Type</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">                        
                      <select name="TEMPLATE_FOR" id="TEMPLATE_FOR" class="form-control selectpicker" autocomplete="off" data-live-search="true">
                        <option value="">Select</option>  
                        <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'VENDOR_QUOTATION'?'selected="selected"':''}}              value="VENDOR_QUOTATION">Vendor Quotation</option>
                        <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'VENDOR_QUOTATION_COMPARISION'?'selected="selected"':''}}  value="VENDOR_QUOTATION_COMPARISION">Vendor Quotation Comparision</option>
                        <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'PURCHASE_ORDER'?'selected="selected"':''}}                value="PURCHASE_ORDER">Purchase Order</option>
                        <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'BLANKET_PURCHASE_ORDER'?'selected="selected"':''}}        value="BLANKET_PURCHASE_ORDER">Blanket Purchase Order</option>
                        <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'SERVICE_PURCHASE_ORDER'?'selected="selected"':''}}        value="SERVICE_PURCHASE_ORDER">Service Purchase Order</option>
                        <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'PURCHASE_RETURN'?'selected="selected"':''}}               value="PURCHASE_RETURN">Purchase Return</option>
                        <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'PURCHASE_BILL/INVOICE'?'selected="selected"':''}}         value="PURCHASE_BILL/INVOICE">Purchase Bill/ Invoice</option>
                        <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'SERVICE_PURCHASE_INVOICE'?'selected="selected"':''}}      value="SERVICE_PURCHASE_INVOICE">Service Purchase Invoice</option>
                        <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'IMPORT_PURCHASE_ORDER'?'selected="selected"':''}}         value="IMPORT_PURCHASE_ORDER">Import Purchase Order</option>
                        <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'IMPORT_PURCHASE_INVOICE'?'selected="selected"':''}}       value="IMPORT_PURCHASE_INVOICE">Import Purchase Invoice</option>
                        <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'SALES_QUOTATION'?'selected="selected"':''}}               value="SALES_QUOTATION">Sales Quotation</option>
                        <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'SALES_ORDER'?'selected="selected"':''}}                   value="SALES_ORDER">Sales Order</option>
                        <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'OPEN_SALES_ORDER'?'selected="selected"':''}}              value="OPEN_SALES_ORDER">Open Sales Order</option>
                        <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'SALES_INVOICE'?'selected="selected"':''}}                 value="SALES_INVOICE">Sales Invoice</option>
                        <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'SALES_RETURN'?'selected="selected"':''}}                  value="SALES_RETURN">Sales Return</option>
                        <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'SALES_SERVICE_ORDER'?'selected="selected"':''}}           value="SALES_SERVICE_ORDER">Sales Service Order</option>
                        <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'SALES_SERVICE_INVOICE'?'selected="selected"':''}}         value="SALES_SERVICE_INVOICE">Sales Service Invoice</option>
                      </select>
                    </div>
                  </div>
                </div>     

              {{-- <div class="row">
                <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-1 pl pr">
                <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
                 value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
                </div>
                
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
                </div>
             </div> --}}          

            <div class="row">
              <div class="col-lg-2 pl"><p>Template Description</p></div>
              <div class="col-lg-6 pl" id="temdes">
                <textarea name="TEMPLATE" id="TEMPLATE" cols="118" rows="10" >{{isset($objResponse->TEMPLATE) && $objResponse->TEMPLATE !=''?$objResponse->TEMPLATE:''}}</textarea>
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
$('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[388,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formDataMst = $( "#frm_mst_edit" );
     formDataMst.validate();

    $("#TEMPLATE_FOR").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_TEMPLATE_FOR").hide();
        validateSingleElemnet("TEMPLATE_FOR");

    });

    $( "#TEMPLATE_FOR" ).rules( "add", {
        required: true,
        //StringRegex: true,  //from custom.js
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

        for ( instance in CKEDITOR.instances ) {
            CKEDITOR.instances.TEMPLATE.updateElement();
        }
        
        var getDataForm = $("#frm_mst_edit");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[388,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.TEMPLATE_FOR){
                        showError('ERROR_TEMPLATE_FOR',data.errors.TEMPLATE_FOR);
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
            url:'{{route("mastermodify",[388,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.TEMPLATE_FOR){
                        showError('ERROR_TEMPLATE_FOR',data.errors.TEMPLATE_FOR);
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
        window.location.href = '{{route("master",[388,"index"]) }}';

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

      $("#TEMPLATE_NAME").focus();

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
  //$("#FORMNAME").focus(); 
});
</script>

<script>
  $(document).ready(function(){
      $("#select_all").change(function(){ 
          $(".provider").prop('checked', $(this).prop("checked")); 
      });
      CKEDITOR.replace( 'TEMPLATE' );
      $('.provider').change(function(){
          
          if(false == $(this).prop("checked")){ 
              $("#select_all").prop('checked', false); 
          }
          
          if ($('.provider:checked').length == $('.provider').length ){
              $("#select_all").prop('checked', true);
          }
      });
  });

</script>


@endpush