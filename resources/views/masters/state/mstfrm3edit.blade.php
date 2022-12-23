@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[3,'index'])}}" class="btn singlebt">State Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled" ><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button id="btnSaveCountry"   class="btn topnavbt" tabindex="10"><i class="fa fa-floppy-o"></i> Save</button>
                        <a class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</a>
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-print"></i> Print</a>
                        <button class="btn topnavbt"  id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
                        <a href="#" class="btn topnavbt" disabled="disabled"><i class="fa fa-times"></i> Cancel</a>
                        
                        <button class="btn topnavbt" id="btnApproved"{{($objRights->APPROVAL1 || $objRights->APPROVAL2 || $objRights->APPROVAL3 || $objRights->APPROVAL4 || $objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</a>
                        <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_state" method="POST"  > 
          @CSRF
          {{isset($objCountry->CTRYID) ? method_field('PUT') : '' }}
          <div class="inner-form">
				
				<div class="row">
					<div class="col-lg-1 pl"><p>Country Code</p></div>
					<div class="col-lg-1 pl">
						<select name="CTRYID_REF" id="CTRYID_REF" class="form-control mandatory" onchange="getCountryName(this.value)" tabindex="1"  >
							<option value="" selected >Select</option>
							@foreach($objCountryList as $countryList)
							<option {{isset($objCountry->CTRYID_REF) && $objCountry->CTRYID_REF ==$countryList->CTRYID?"selected='selected'":""}}  value="{{$countryList->CTRYID}}">{{$countryList->CTRYCODE}}</option>
							@endforeach
						</select>
					</div>
				
					<div class="col-lg-1 pl col-md-offset-2"><p>Country Name</p></div>
					<div class="col-lg-4 pl">
						<input type="text" id="COUNTRYNAME" class="form-control"  maxlength="100" readonly >
					</div>
				</div>
              
                <div class="row">
                  <div class="col-lg-1 pl"><p>State Code</p></div>
                  <div class="col-lg-1 pl">
                  
                    <label> {{$objCountry->STCODE}} </label>
                    <input type="hidden" name="STID" id="STID" value="{{ $objCountry->STID }}" />
                    <input type="hidden" name="STCODE" id="STCODE" value="{{ $objCountry->STCODE }}" autocomplete="off" minlength="3"  maxlength="3"   />
                    <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
                  
                  </div>
                
                  <div class="col-lg-1 pl col-md-offset-2"><p>State Name</p></div>
                  <div class="col-lg-4 pl">
                    <input type="text" name="STATE_NAME" id="STATE_NAME" class="form-control mandatory" value="{{ old('NAME',$objCountry->NAME) }}" maxlength="100" tabindex="2"  />
                    <span class="text-danger" id="ERROR_COUNTRY_NAME"></span> 
                  </div>
                </div>
          
          
              <div class="row">
                <div class="col-lg-1 pl"><p>STD Code</p></div>
                <div class="col-lg-1 pl">
                    <input type="text" name="STDCODE" id="STDCODE" class="form-control" value="{{ old('STDCODE',$objCountry->STDCODE) }}" maxlength="4"  tabindex="3" >
                  </div>

                
                <div class="col-lg-1 pl col-md-offset-2"><p>Language</p></div>
                <div class="col-lg-2 pl ">
                  <input type="text" name="LANG" id="LANG" class="form-control" value="{{ old('LANG',$objCountry->LANG) }}"  maxlength="50" style="width:285px;" tabindex="4" >
                </div>
              </div>

              <div class="row">
				<div class="col-lg-1 pl"><p>NEWSC India</p></div>
                <div class="col-lg-1 pl">
					
                  <input type="text" name="NEWSC" id="NEWSC" class="form-control" value="{{ old('NEWSC', $objCountry->NEWSC) }}" maxlength="10" tabindex="5" >
               
				</div>
				
                <div class="col-lg-1 pl col-md-offset-2"><p>Capital</p></div>
                <div class="col-lg-3 pl">
                  <input type="text" name="CAPITAL" id="CAPITAL" class="form-control"  value="{{ old('CAPITAL',$objCountry->CAPITAL) }}"   maxlength="50" style="width:285px;" tabindex="6" >
                </div>
              </div>
			  
			  <div class="row">
					<div class="col-lg-1 pl"><p>State / UT</p></div>
					<div class="col-lg-2 pl">
						
						<select name="STTYPE" id="STTYPE" class="form-control mandatory" tabindex="7" >
							<option {{isset($objCountry->STTYPE) && $objCountry->STTYPE =="STATE"?"selected='selected'":""}} value="STATE">STATE</option>
							<option {{isset($objCountry->STTYPE) && $objCountry->STTYPE =="UT"?"selected='selected'":""}} value="UT">UT</option>
						</select>
						
					</div>
				</div>

              <div class="row">
                <div class="col-lg-1 pl"><p>De-Activated</p></div>
                <div class="col-lg-1 pl pr">
                <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objCountry->DEACTIVATED == 1 ? "checked" : ""}}
                 value='{{$objCountry->DEACTIVATED == 1 ? 1 : 0}}' tabindex="8"  >
                </div>
                
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                <div class="col-lg-8 pl">
                <input type="text" name="DODEACTIVATED" class="form-control" id="txtDate1" placeholder="dd/mm/yyyy" 
                {{$objCountry->DEACTIVATED == 1 ? "" : "disabled"}}
                   tabindex="9" value="{{ (is_null($objCountry->DODEACTIVATED) || $objCountry->DODEACTIVATED=='1900-01-01' )?'':
                  \Carbon\Carbon::parse($objCountry->DODEACTIVATED)->format('d/m/Y')   }}" />
                </div>
                </div>
             </div>

          </div>
        </form>
    </div><!--purchase-order-view-->


@endsection
@section('alert')
<!-- Alert -->
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog">
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

 var formStateMst = $( "#frm_mst_state" );
     formStateMst.validate();
	
	$("#CTRYID_REF").blur(function(){
		$(this).val($.trim( $(this).val() ));
		$("#ERROR_CTRYID_REF").hide();
		validateSingleElemnet("CTRYID_REF");
         
    });
	
	$( "#CTRYID_REF" ).rules( "add", {
        required: true,
        nowhitespace: true,
        StringNumberRegex: true, //from custom.js
        messages: {
            required: "Required field.",
            minlength: jQuery.validator.format("min {0} char")
        }
    });
   
    //country name
    $("#STATE_NAME").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_COUNTRY_NAME").hide();
        validateSingleElemnet("STATE_NAME");

    });

    $( "#STATE_NAME" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "State name is required."
        }
    });

    //ISD CODE
    $( "#STDCODE" ).rules( "add", {
        required: false,
        nowhitespace: true,
        OnlyNumberRegex: true, //from custom.js
    });

    $("#txtDate1").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_txtDate1").hide();
      validateSingleElemnet("txtDate1");
    });

    $( "#txtDate1" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field"
        }
    });

    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_state" ).validate();
          validator.element( "#"+element_id+"" );
    }

    //validate
    $( "#btnSaveCountry" ).click(function() {

        if(formStateMst.valid()){
            //set function nane of yes and no btn 
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
            $("#YesBtn").focus();
            highlighFocusBtn('activeYes');

        }

    });//btnSaveCountry

    
    //validate and approve
    $( "#btnApproved" ).click(function() {

        if(formStateMst.valid()){
            //set function nane of yes and no btn 
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
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

        var countryForm = $("#frm_mst_state");
        var formData = countryForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[3,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.STATE_NAME){
                        showError('ERROR_COUNTRY_NAME',data.errors.STATE_NAME);
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
                    $("#frm_mst_state").trigger("reset");

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

        var countryForm = $("#frm_mst_state");
        var formData = countryForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[3,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.STATE_NAME){
                        showError('ERROR_COUNTRY_NAME',data.errors.STATE_NAME);
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
                    $("#frm_mst_state").trigger("reset");

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
        window.location.href = '{{route("master",[3,"index"]) }}';

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
   $('#txtDate1').datepicker({
    dateFormat: "dd/mm/yy",
    changeMonth: true,
    changeYear: true
   });

   $('input[type=checkbox][name=DEACTIVATED]').change(function() {
    if ($(this).prop("checked")) {
      $(this).val('1');
      $('#txtDate1').removeAttr('disabled');
    }
    else {
      $(this).val('0');
      $('#txtDate1').prop('disabled', true);
      $('#txtDate1').val('dd/mm/yyyy');
      
    }
  });

});


function getCountryName(CTRYID){
		$("#COUNTRYNAME").val('');
		
		$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
		
        $.ajax({
            url:'{{route("master",[3,"getCountryName"])}}',
            type:'POST',
            data:{CTRYID:CTRYID},
            success:function(data) {
               $("#COUNTRYNAME").val(data);                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });	
  }
  
  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[3,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });


$(function() { 
	$("#CTRYID_REF").focus(); 
	getCountryName('{{$objCountry->CTRYID_REF}}')
});

</script>


@endpush