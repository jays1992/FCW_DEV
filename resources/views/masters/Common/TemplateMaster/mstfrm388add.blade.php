@extends('layouts.app')
@section('content')



    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[388,'index'])}}" class="btn singlebt">Template Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled" ><i class="fa fa-plus"></i> Add</button>
                <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                <button class="btn topnavbt" id="btnSave"  tabindex="3"  ><i class="fa fa-floppy-o"></i> Save</button>
                <button class="btn topnavbt" id="btnView" disabled="disabled" ><i class="fa fa-eye"></i> View</button>
                <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                <button class="btn topnavbt" id='btnUndo' ><i class="fa fa-undo"></i> Undo</button>
                <button class="btn topnavbt" id="btnCancel"  disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                <button class="btn topnavbt" id="btnApprove"  disabled="disabled" ><i class="fa fa-thumbs-o-up"></i> Approved</button>
                <button class="btn topnavbt"  id="btnAttach"  disabled="disabled" ><i class="fa fa-link"></i> Attachment</button>
                <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>

              </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_add" method="POST"  > 
          @CSRF
          <div class="inner-form">
              
                <div class="row">
                  <div class="col-lg-2 pl"><p>Template Name</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">
                        <input type="text" name="TEMPLATE_NAME" id="TEMPLATE_NAME" class="form-control mandatory" autocomplete="off" maxlength="20" tabindex="1" />
                        <span class="text-danger" id="ERROR_TEMPLATE_NAME"></span> 
                    </div>
                  </div>

                  <div class="col-lg-2 pl"><p>Voucher Type</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">                        
                      <select name="TEMPLATE_FOR" id="TEMPLATE_FOR" class="form-control selectpicker" autocomplete="off" data-live-search="true">
                        <option value="">Select</option>  
                        <option value="VENDOR_QUOTATION">Vendor Quotation</option>
                        <option value="VENDOR_QUOTATION_COMPARISION">Vendor Quotation Comparision</option>
                        <option value="PURCHASE_ORDER">Purchase Order</option>
                        <option value="BLANKET_PURCHASE_ORDER">Blanket Purchase Order</option>
                        <option value="SERVICE_PURCHASE_ORDER">Service Purchase Order</option>
                        <option value="PURCHASE_RETURN">Purchase Return</option>
                        <option value="PURCHASE_BILL/INVOICE">Purchase Bill/ Invoice</option>
                        <option value="SERVICE_PURCHASE_INVOICE">Service Purchase Invoice</option>
                        <option value="IMPORT_PURCHASE_ORDER">Import Purchase Order</option>
                        <option value="IMPORT_PURCHASE_INVOICE">Import Purchase Invoice</option>
                        <option value="SALES_QUOTATION">Sales Quotation</option>
                        <option value="SALES_ORDER">Sales Order</option>
                        <option value="OPEN_SALES_ORDER">Open Sales Order</option>
                        <option value="SALES_INVOICE">Sales Invoice</option>
                        <option value="SALES_RETURN">Sales Return</option>
                        <option value="SALES_SERVICE_ORDER">Sales Service Order</option>
                        <option value="SALES_SERVICE_INVOICE">Sales Service Invoice</option>
                      </select>
                    </div>
                  </div>
                </div> 

                <div class="row">
                  <div class="col-lg-2 pl"><p>Template Description</p></div>
                  <div class="col-lg-6 pl" id="temdes">
                    <textarea name="TEMPLATE" id="TEMPLATE" cols="118" rows="10" ></textarea>
                  </div>
                </div>              
          </div>
        </form>
    </div><!--purchase-order-view-->
@endsection
@section('alert')
<!-- Alert -->
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

 var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();

    $("#TEMPLATE_NAME").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_TEMPLATE_NAME").hide();
      validateSingleElemnet("TEMPLATE_NAME");
         
    });

    $( "#TEMPLATE_NAME" ).rules( "add", {
        required: true,
        //nowhitespace: true,
        //StringNumberRegex: true, //from custom.js
        messages: {
            required: "Required field.",
        }
    });

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
            required: "Required field."
        }
    });

    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_add" ).validate();
         if(validator.element( "#"+element_id+"" )){
            //check duplicate code
          if(element_id=="TEMPLATE_NAME" || element_id=="formcode" ) {
            checkDuplicateCode();
          }

         }
    }

    // //check duplicate exist code
    function checkDuplicateCode(){
        
        //validate and save data
        var getDataForm = $("#frm_mst_add");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[388,"codeduplicate"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_TEMPLATE_NAME',data.msg);
                    $("#TEMPLATE_NAME").focus();
                }                                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
    }

    //validate
    $( "#btnSave" ).click(function() {
        if(formResponseMst.valid()){

            //set function nane of yes and no btn 
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
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
        
        var getDataForm = $("#frm_mst_add");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[388,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.TEMPLATE_NAME){
                        showError('ERROR_TEMPLATE_NAME',data.errors.TEMPLATE_NAME);
                    }
                    if(data.errors.FORMNAME){
                        showError('ERROR_FORMNAME',data.errors.FORMNAME);
                    }
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
                    window.location.href='{{ route("master",[388,"index"])}}';
                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
      
   } // fnSaveData



    
    $("#NoBtn").click(function(){
    
      $("#alert").modal('hide');
      var custFnName = $("#NoBtn").data("funcname");
          window[custFnName]();

    }); //no button
   
    
    $("#OkBtn").click(function(){

        $("#alert").modal('hide');

        $("#YesBtn").show();  //reset
        $("#NoBtn").show();   //reset
        $("#OkBtn").hide();

        $(".text-danger").hide();
        $("#TEMPLATE_NAME").focus();
        
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
      window.location.href = "{{route('master',[388,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#TEMPLATE_NAME").focus();
   }//fnUndoNo


    function showError(pId,pVal){

      $("#"+pId+"").text(pVal);
      $("#"+pId+"").show();

    }//showError

    function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }



    $(function() { $("#TEMPLATE_NAME").focus(); });
    

</script>


<script> 

$(document).ready(function(){
  CKEDITOR.replace( 'TEMPLATE' );
});

</script>


<script>
$(function() {
  $('.selectpicker').selectpicker();
});
</script>

@endpush