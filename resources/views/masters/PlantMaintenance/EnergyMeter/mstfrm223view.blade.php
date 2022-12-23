@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                  <a href="{{route('master',[223,'index'])}}" class="btn singlebt">Energy Meter</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button id="btnSave" disabled="disabled"  class="btn topnavbt" tabindex="4"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" disabled="disabled" id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" disabled="disabled" id="btnApprove"><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_edit" method="POST"  > 
          @CSRF
          
          <div class="inner-form">
          
                            
              <div class="row">
                    <div class="col-lg-2 pl"><p>Meter Code</p></div>
                    <div class="col-lg-2 pl">
                      <div class="col-lg-7 pl">
                        <label> {{$objResponse->METER_CODE}} </label>
                        <input type="hidden" name="ENERGYID" id="ENERGYID" value="{{ $objResponse->ENERGYID }}" />
                        <input type="hidden" name="METER_CODE" id="METER_CODE" value="{{ $objResponse->METER_CODE }}"   />
                        <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />                
                        <span class="text-danger" id="ERROR_METER_CODE"></span> 
                        
                      </div>
                    </div>

                    <div class="col-lg-2 pl"><p>Meter Description</p></div>
                    <div class="col-lg-3 pl">
                      <input type="text" name="METER_DESC" id="METER_DESC" class="form-control mandatory" disabled value="{{ old('METER_DESC',$objResponse->METER_DESC) }}" maxlength="200"  />
                      <span class="text-danger" id="ERROR_METER_DESC"></span> 
                    </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Starting Meter Reading (KWH)</p></div>
                <div class="col-lg-2 pl">                 
                      <input type="text" name="KWH" id="KWH" class="form-control mandatory" disabled value="{{ old('KWH',$objResponse->KWH) }}"  autocomplete="off" maxlength="50"/>                 
                </div>
                  <div class="col-lg-2 pl"><p>Starting Meter Reading (KVARH)</p></div>
                  <div class="col-lg-2 pl">                 
                    <input type="text" name="KVARH" id="KVARH" class="form-control "  disabled  value="{{ old('KVARH',$objResponse->KVARH) }}"  autocomplete="off" maxlength="50" />                 
                  </div>
              </div>
  
              <div class="row">
                  <div class="col-lg-2 pl"><p>Starting Meter Reading (KVAH)</p></div>
                  <div class="col-lg-2 pl">                 
                    <input type="text" name="KVAH" id="KVAH" class="form-control "  disabled value="{{ old('KVAH',$objResponse->KVAH) }}"  autocomplete="off" maxlength="50" />                 
                  </div>
                  <div class="col-lg-2 pl"><p>Starting Meter Reading (MD)</p></div>
                  <div class="col-lg-2 pl">
                    <input type="text" name="MD" id="MD" class="form-control "  disabled  value="{{ old('MD',$objResponse->MD) }}"  autocomplete="off" maxlength="50" />
                  </div>
              </div>
  
              <div class="row">
                  <div class="col-lg-2 pl"><p>Power Factor</p></div>
                  <div class="col-lg-2 pl">                 
                    <input type="text" name="POWER_FACTOR" id="POWER_FACTOR" class="form-control "  disabled value="{{ old('POWER_FACTOR',$objResponse->POWER_FACTOR) }}"   autocomplete="off" maxlength="100" />                 
                  </div>
                  <div class="col-lg-2 pl"><p>Date of Commissioning</p></div>
                <div class="col-lg-2 pl">
                  <input type="date" name="DOCOMMISSION" class="form-control " id="DOCOMMISSION"  disabled  value="{{isset($objResponse->DOCOMMISSION) && $objResponse->DOCOMMISSION !="" && $objResponse->DOCOMMISSION !="1900-01-01" ? $objResponse->DOCOMMISSION:''}}"  placeholder="dd/mm/yyyy"  />
                </div>
              </div>
  
              <div class="row">
                <div class="col-lg-2 pl"><p>Meter Company</p></div>
                <div class="col-lg-2 pl">                 
                  <input type="text" name="METER_COMPANY" id="METER_COMPANY" class="form-control "  disabled  value="{{ old('METER_COMPANY',$objResponse->METER_COMPANY) }}"   autocomplete="off" maxlength="100" />                 
                </div>
                <div class="col-lg-2 pl"><p>Brand</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="BRAND" id="BRAND" class="form-control "  disabled  value="{{ old('BRAND',$objResponse->BRAND) }}"  autocomplete="off" maxlength="100" />
                </div>
              </div>
  
  
  
            <div class="row">
              <div class="col-lg-2 pl"><p>Model</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="MODEL" id="MODEL" class="form-control "  disabled  value="{{ old('MODEL',$objResponse->MODEL) }}"  autocomplete="off" maxlength="100" />
              </div>
              <div class="col-lg-2 pl"><p>Serial No</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="SERIAL_NO" id="SERIAL_NO" class="form-control "  disabled   value="{{ old('SERIAL_NO',$objResponse->SERIAL_NO) }}"   autocomplete="off" maxlength="20" />
              </div>
            </div>
           
            <div class="row">
              <div class="col-lg-2 pl"><p>Load Sanction</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="SANCTION_LOAD" id="SANCTION_LOAD" class="form-control "  disabled  value="{{ old('SANCTION_LOAD',$objResponse->SANCTION_LOAD) }}"  autocomplete="off" maxlength="50" />
              </div>
              <div class="col-lg-2 pl"><p>Power Supply By</p></div>
              <div class="col-lg-2 pl">
                <input type="text" name="SUPPLY_BY" id="SUPPLY_BY" class="form-control "  disabled value="{{ old('SUPPLY_BY',$objResponse->SUPPLY_BY) }}"  autocomplete="off" maxlength="100" />
              </div>
            </div>
  
            <div class="row">
              <div class="col-lg-2 pl"><p>Remarks</p></div>
              <div class="col-lg-3 pl">
                <input type="text" name="REMARKS" id="REMARKS" class="form-control "  disabled  value="{{ old('REMARKS',$objResponse->REMARKS) }}"  autocomplete="off" maxlength="100" />
              </div>
            </div>

            {{-- deactive row --}}
            <div class="row">
              <div class="col-lg-2 pl"><p>De-Activated</p></div>
              <div class="col-lg-1 pl pr">
              <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
               value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"   disabled >
              </div>
              
              <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
              <div class="col-lg-2 pl">
                <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED"  disabled  {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
              </div>
            </div>
           {{-- deactive row end --}}
           
            <br/>
            <br/>
             
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
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;">
                <div id="alert-active1" class="activeOk1"></div>OK</button>  
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
      var viewURL = '{{route("master",[223,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formDataMst = $( "#frm_mst_edit" );
     formDataMst.validate();

    // 

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

        $("#OkBtn").hide();
        $("#OkBtn1").hide();

        if(formDataMst.valid()){
            //set function nane of yes and no btn 
            //---
            $("#FocusId").val('');
            var METER_CODE           =   $.trim($("#METER_CODE").val());
            var METER_DESC           =   $.trim($("#METER_DESC").val());
            var KWH                   =   $.trim($("#KWH").val());
           
         
            if(METER_CODE ===""){
                $("#METER_CODE").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please enter value in Meter Code.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            }
            else if(METER_DESC ===""){
               $("#METER_DESC").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please enter value in Meter Description.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if(KWH ===""){
                $("#KWH").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please enter value in Starting Meter Reading (KWH).');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            
            //--- 
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
            $("#YesBtn").focus();
            highlighFocusBtn('activeYes');

        }

    });//btnSave

    
    //validate and approve
    $("#btnApprove").click(function() {

        $("#OkBtn").hide();
        $("#OkBtn1").hide();

        
        if(formDataMst.valid()){
            //set function nane of yes and no btn 
           //---
           $("#FocusId").val('');
            var METER_CODE           =   $.trim($("#METER_CODE").val());
            var METER_DESC           =   $.trim($("#METER_DESC").val());
            var KWH                   =   $.trim($("#KWH").val());
           
         
            if(METER_CODE ===""){
                $("#METER_CODE").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please enter value in Meter Code.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            }
            else if(METER_DESC ===""){
               $("#METER_DESC").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please enter value in Meter Description.');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            else if(KWH ===""){
                $("#KWH").focus();        
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text('Please enter value in Starting Meter Reading (KWH).');
                $("#alert").modal('show');
                $("#OkBtn").focus();
                return false;
            } 
            
            //--- 
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

        $("#OkBtn").hide();
        $("#OkBtn1").hide();
    
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
            url:'{{route("mastermodify",[223,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.METER_DESC){
                        showError('ERROR_NAME',data.errors.METER_DESC);
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
              console.log("Error: Something went wrong.");
            },
        });

    };// fnSaveData


    // save and approve 
    window.fnApproveData = function (){
        
        $("#OkBtn").hide();
        $("#OkBtn1").hide();

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
            url:'{{route("mastermodify",[223,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.METER_DESC){
                        showError('ERROR_NAME',data.errors.METER_DESC);
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
                    $("#OkBtn1").show();

                    $("#AlertMessage").text(data.msg);

                    $(".text-danger").hide();
                    $("#frm_mst_edit").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn1").focus();

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

    }); ///ok button

    $("#OkBtn1").click(function(){

        $("#alert").modal('hide');

        $("#YesBtn").show();
        $("#NoBtn").show();
        $("#OkBtn").hide();

        $(".text-danger").hide();
        window.location.href = '{{route("master",[223,"index"]) }}';

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

      //$("#PLCODE").focus();

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
  //$("#DESCRIPTIONS").focus(); 
});




</script>


@endpush