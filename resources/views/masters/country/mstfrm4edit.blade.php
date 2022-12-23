@extends('layouts.app')
@section('content')
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[4,'index'])}}" class="btn singlebt">Country Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <a href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</a>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button id="btnSaveCountry"   class="btn topnavbt" tabindex="9"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt"  id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApproved" ><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</button>
                        <a href="{{route('home')}}" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_country" method="POST"  > 
          @CSRF
          {{isset($objCountry->CTRYID) ? method_field('PUT') : '' }}
          <div class="inner-form">
          
              
                <div class="row">
                  <div class="col-lg-2 pl"><p>Country Code</p></div>
                  <div class="col-lg-3 pl">
                  
                    <label> {{$objCountry->CTRYCODE}} </label>
                    <input type="hidden" name="CTRYID" id="CTRYID" value="{{ $objCountry->CTRYID }}" />
                    <input type="hidden" name="CTRYCODE" id="CTRYCODE" value="{{ $objCountry->CTRYCODE }}" autocomplete="off" minlength="3"  maxlength="3"   />
                    <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
                  
                  </div>
                
                  <div class="col-lg-2 pl col-md-offset-1"><p>Country Name</p></div>
                  <div class="col-lg-3 pl">
                    <input type="text" name="COUNTRY_NAME" id="COUNTRY_NAME" class="form-control mandatory" value="{{ old('NAME',$objCountry->NAME) }}" maxlength="100" tabindex="2"  />
                    <span class="text-danger" id="ERROR_COUNTRY_NAME"></span> 
                  </div>
                </div>
          
          
              <div class="row">
                <div class="col-lg-2 pl"><p>ISD Code</p></div>
                <div class="col-lg-3 pl">
                    <input type="text" name="ISDCODE" id="ISDCODE" class="form-control" value="{{ old('ISDCODE',$objCountry->ISDCODE) }}" maxlength="4"  tabindex="3" >
                  </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Language</p></div>
                <div class="col-lg-3 pl ">
                  <input type="text" name="LANG" id="LANG" class="form-control" value="{{ old('LANG',$objCountry->LANG) }}"  maxlength="50" tabindex="4" >
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Continental</p></div>
                <div class="col-lg-3 pl">
                  <input type="text" name="CONTINENTAL" id="CONTINENTAL" class="form-control" value="{{ old('CONTINENTAL', $objCountry->CONTINENTAL) }}" maxlength="50" tabindex="5" >
                </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Capital</p></div>
                <div class="col-lg-3 pl">
                  <input type="text" name="CAPITAL" id="CAPITAL" class="form-control"  value="{{ old('CAPITAL',$objCountry->CAPITAL) }}"   maxlength="50" tabindex="6" >
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-3 pl pr">
                <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objCountry->DEACTIVATED == 1 ? "checked" : ""}}
                 value='{{$objCountry->DEACTIVATED == 1 ? 1 : 0}}' tabindex="7"  >
                </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Date of De-Activated</p></div>
                <div class="col-lg-3 pl">
                <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" placeholder="dd/mm/yyyy" 
                {{$objCountry->DEACTIVATED == 1 ? "" : "disabled"}}
                   tabindex="8" value="{{ (is_null($objCountry->DODEACTIVATED) || $objCountry->DODEACTIVATED=='1900-01-01' )?'':
                  \Carbon\Carbon::parse($objCountry->DODEACTIVATED)->format('Y-m-d')   }}" />
				</div>
             </div>

          </div>
        </form>
    </div><!--purchase-order-view-->


@endsection
@section('alert')
<!-- Alert -->
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog" style="position:relative;top:82px;left:273px;"  >
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
<!-- btnSaveCountry -->

@push('bottom-scripts')
<script>

 var formCountryMst = $( "#frm_mst_country" );
     formCountryMst.validate();

   
    //country name
    $("#COUNTRY_NAME").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_COUNTRY_NAME").hide();
        validateSingleElemnet("COUNTRY_NAME");

    });
    $("#COUNTRY_NAME").keydown(function(){
        $("#ERROR_COUNTRY_NAME").hide();
        validateSingleElemnet("COUNTRY_NAME");

    });

    $( "#COUNTRY_NAME" ).rules( "add", {
        required: true,
        StringRegex: true,  //from custom.js
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Country name is required."
        }
    });

    //ISD CODE
    $( "#ISDCODE" ).rules( "add", {
        required: false,
        nowhitespace: true,
        OnlyNumberRegex: true, //from custom.js
    });

    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_country" ).validate();
          validator.element( "#"+element_id+"" );
    }

    //validate
    $( "#btnSaveCountry" ).click(function() {

        if(formCountryMst.valid()){

          //set function nane of yes and no btn 
          $("#alert").modal('show');
          $("#AlertMessage").text('Do you want to update the record.');
          $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
          $("#YesBtn").focus();
          highlighFocusBtn('activeYes');

        }

    });//btnSaveCountry

    
    //validate and approve
    $( "#btnApproved" ).click(function() {

        if(formCountryMst.valid()){
            //set function nane of yes and no btn 
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to approve the record.');
            $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name of approval
            $("#YesBtn").focus();
            highlighFocusBtn('activeYes');

        }

    });//btnSaveCountry


    $("#YesBtn").click(function(){

      $("#alert").modal('hide');
      var customFnName = $("#YesBtn").data("funcname");
          window[customFnName]();

    }); //yes button

    
    window.fnSaveData = function (){
        
        //validate and save data
        event.preventDefault();

        var countryForm = $("#frm_mst_country");
        var formData = countryForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[4,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.COUNTRY_NAME){
                        showError('ERROR_COUNTRY_NAME',"Country name is required.");
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
                    $("#frm_mst_country").trigger("reset");

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

        var countryForm = $("#frm_mst_country");
        var formData = countryForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[4,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.COUNTRY_NAME){
                        showError('ERROR_COUNTRY_NAME',data.errors.COUNTRY_NAME);
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
                    $("#frm_mst_country").trigger("reset");

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
        window.location.href = '{{route("master",[4,"index"]) }}';

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

      $("#CTRYCODE").focus();

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
  //  $('#DODEACTIVATED').datepicker({
  //   dateFormat: "dd/mm/yy",
  //   changeMonth: true,
  //   changeYear: true
  //  });

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
      $('#DODEACTIVATED').val('dd/mm/yyyy');
      
    }
  });

});

$(function() { $("#COUNTRY_NAME").focus(); });
</script>


@endpush