@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[180,'index'])}}" class="btn singlebt">Shift Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled" ><i class="fa fa-plus"></i> Add</button>
                <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                <button class="btn topnavbt" id="btnSave"  tabindex="20"  ><i class="fa fa-save"></i> Save</button>
                <button class="btn topnavbt" id="btnView" disabled="disabled" ><i class="fa fa-eye"></i> View</button>
                <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                <button class="btn topnavbt" id='btnUndo' ><i class="fa fa-undo"></i> Undo</button>
                <button class="btn topnavbt" id="btnCancel"  disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                <button class="btn topnavbt" id="btnApprove"  disabled="disabled" ><i class="fa fa-lock"></i> Approved</button>
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
                  <div class="col-lg-2 pl"><p>Shift Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">
                        {{-- <input type="text" name="SHIFT_CODE" id="SHIFT_CODE" onkeypress="return AlphaNumaric(event,this)" value="{{ old('SHIFT_CODE') }}" class="form-control mandatory" autocomplete="off" maxlength="50" tabindex="1" style="text-transform:uppercase" required/> --}}
                        @if(!empty($objDD))
                            @if($objDD->SYSTEM_GRSR == "1")
                                <input type="text" name="SHIFT_CODE" id="SHIFT_CODE" value="{{ $objDOCNO }}" class="form-control mandatory" tabindex="1" maxlength="10" autocomplete="off" readonly style="text-transform:uppercase" autofocus >
                            @endif
                            @if($objDD->MANUAL_SR == "1")
                                <input type="text" name="SHIFT_CODE" id="SHIFT_CODE" value="{{ old('SHIFT_CODE') }}" class="form-control mandatory"  maxlength="{{$objDD->MANUAL_MAXLENGTH}}" tabindex="1" autocomplete="off" style="text-transform:uppercase" autofocus >
                            @endif
                        @endif  
                        <span class="text-danger" id="ERROR_SHIFT_CODE"></span> 
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Shift Name</p></div>
                  <div class="col-lg-4 pl">
                    <input type="text" name="SHIFT_NAME" id="SHIFT_NAME" class="form-control mandatory" value="{{ old('SHIFT_NAME') }}" maxlength="200" tabindex="2"  required/>
                    <span class="text-danger" id="ERROR_SHIFT_NAME"></span> 
                  </div>
                </div>
               
                <div class="row">
                    <div class="col-lg-2 pl"><p>Start Time</p></div>
                    <div class="col-lg-2 pl">
                      <input type="time" name="START_TIME" id="START_TIME" class="form-control mandatory" value="{{ old('START_TIME') }}"  tabindex="3"  required/> 
                      <span class="text-danger" id="ERROR_START_TIME"></span> 
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-2 pl"><p>End Time</p></div>
                    <div class="col-lg-2 pl">
                      <input type="time" name="END_TIME" id="END_TIME" class="form-control mandatory" value="{{ old('END_TIME') }}" tabindex="4" required />                        
                      <span class="text-danger" id="ERROR_END_TIME"></span>
                    </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Total Hours</p></div>
                  <div class="col-lg-1 pl">
                    <input name="TOTAL_HOURS" id="TOTAL_HOURS" class="form-control mandatory" value="0.00" tabindex="5" readonly  style="width:300px"/>                        
                    <span class="text-danger" id="ERROR_TOTAL_HOURS"></span>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Full Day Min Hours</p></div>
                  <div class="col-lg-1 pl">
                      <select class="form-control" name="MIN_HOURS_FULL" id="MIN_HOURS_FULL" tabindex="6" required>
                        <option value="00" selected>00 Hours</option>
                        <option value="01">01 hours</option>
                        <option value="02">02 hours</option>
                        <option value="03">03 hours</option>
                        <option value="04">04 hours</option>
                        <option value="05">05 hours</option>
                        <option value="06">06 hours</option>
                        <option value="07">07 hours</option>
                        <option value="08">08 hours</option>
                        <option value="09">09 hours</option>
                        <option value="10">10 hours</option>
                        <option value="11">11 hours</option>
                        <option value="12">12 hours</option>
                      </select>
                  </div>  
                  <div class="col-lg-1 pl">    
                  <select class="form-control" name="MIN_MINS_FULL" id="MIN_MINS_FULL" tabindex="7" required>
                        @for ($i = 0; $i <60; $i++)
                          @php
                          $keycode='';
                              if ($i<10) {
                                $keycode = '0'.$i;
                              }else {
                                $keycode = $i;
                              }
                          @endphp
                          <option value="{{$keycode}}" {{ $i==0?'selected':'' }}>{{ $keycode }}  mins</option>
                        @endfor
                      </select>
                  </div>
                  <div class="col-lg-2 pl"><p>Half Day Min Hours</p></div>
                  <div class="col-lg-1 pl">
                    <select class="form-control" name="MIN_HOURS_HALF" id="MIN_HOURS_HALF" tabindex="8" required>
                      <option value="00" selected>00 Hours</option>
                      <option value="01">01 hours</option>
                      <option value="02">02 hours</option>
                      <option value="03">03 hours</option>
                      <option value="04">04 hours</option>
                      <option value="05">05 hours</option>
                      <option value="06">06 hours</option>
                      <option value="07">07 hours</option>
                      <option value="08">08 hours</option>
                      <option value="09">09 hours</option>
                      <option value="10">10 hours</option>
                      <option value="11">11 hours</option>
                      <option value="12">12 hours</option>
                    </select>
                </div>  
                <div class="col-lg-1 pl">    
                <select class="form-control" name="MIN_MINS_HALF" id="MIN_MINS_HALF" tabindex="9" required>
                      @for ($i = 0; $i <60; $i++)
                        @php
                        $keycode='';
                            if ($i<10) {
                              $keycode = '0'.$i;
                            }else {
                              $keycode = $i;
                            }
                        @endphp
                        <option value="{{$keycode}}" {{ $i==0?'selected':'' }}>{{ $keycode }}  mins</option>
                      @endfor
                    </select>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Grace Time for In-Time</p></div>
                <div class="col-lg-1 pl">
                    <select class="form-control" name="GRACE_TIME_IN_HOURS" id="GRACE_TIME_IN_HOURS" tabindex="10" required>
                      <option value="00" selected>00 Hours</option>
                      <option value="01">01 hours</option>
                      <option value="02">02 hours</option>
                      <option value="03">03 hours</option>
                      <option value="04">04 hours</option>
                      <option value="05">05 hours</option>
                      <option value="06">06 hours</option>
                      <option value="07">07 hours</option>
                      <option value="08">08 hours</option>
                      <option value="09">09 hours</option>
                      <option value="10">10 hours</option>
                      <option value="11">11 hours</option>
                      <option value="12">12 hours</option>
                    </select>
                </div>  
                <div class="col-lg-1 pl">    
                <select class="form-control" name="GRACE_TIME_IN_MINS" id="GRACE_TIME_IN_MINS" tabindex="11" required>
                      @for ($i = 0; $i <60; $i++)
                        @php
                        $keycode='';
                            if ($i<10) {
                              $keycode = '0'.$i;
                            }else {
                              $keycode = $i;
                            }
                        @endphp
                        <option value="{{$keycode}}" {{ $i==0?'selected':'' }}>{{ $keycode }}  mins</option>
                      @endfor
                    </select>
                </div>
                <div class="col-lg-2 pl"><p>Break Hour Time</p></div>
                <div class="col-lg-1 pl">
                  <select class="form-control" name="BREAK_TIME_HOURS" id="BREAK_TIME_HOURS" tabindex="12" required>
                    <option value="00" selected>00 Hours</option>
                    <option value="01">01 hours</option>
                    <option value="02">02 hours</option>
                    <option value="03">03 hours</option>
                    <option value="04">04 hours</option>
                    <option value="05">05 hours</option>
                    <option value="06">06 hours</option>
                    <option value="07">07 hours</option>
                    <option value="08">08 hours</option>
                    <option value="09">09 hours</option>
                    <option value="10">10 hours</option>
                    <option value="11">11 hours</option>
                    <option value="12">12 hours</option>
                  </select>
              </div>  
              <div class="col-lg-1 pl">    
              <select class="form-control" name="BREAK_TIME_MINS" id="BREAK_TIME_MINS" tabindex="13" required>
                    @for ($i = 0; $i <60; $i++)
                      @php
                      $keycode='';
                          if ($i<10) {
                            $keycode = '0'.$i;
                          }else {
                            $keycode = $i;
                          }
                      @endphp
                      <option value="{{$keycode}}" {{ $i==0?'selected':'' }}>{{ $keycode }}  mins</option>
                    @endfor
                  </select>
              </div>
            </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Date Change in same Shift</p></div>
                <div class="col-lg-2 pl">
                <label class="radio-inline">
                    <input   type="radio" name="SHIFT_DTCHANGE_YES" id="RADIO_SHIFT_DTCHANGE_YES" value="1" tabindex="14" />    Yes
                </label>
                <label class="radio-inline">
                    <input   type="radio" name="SHIFT_DTCHANGE_YES" id="RADIO_SHIFT_DTCHANGE_NO" value="0" tabindex="14" checked/>   No
                </label>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>OT Start time</p></div>
                <div class="col-lg-2 pl">
                  <input type="time" name="START_TIME_OT" id="START_TIME_OT" class="form-control " tabindex="15" />   
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
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;">
                <div id="alert-active" class="activeOk1"></div>OK</button>
            <button class="btn alertbt" name='OkBtn2' id="OkBtn2" style="display:none;margin-left: 90px;">
                <div id="alert-active" class="activeOk2"></div>OK</button>

                
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
      var viewURL = '{{route("master",[180,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();

    $("#SHIFT_CODE").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_PERIOD_CODE").hide();
      validateSingleElemnet("SHIFT_CODE");
         
    });

    $( "#SHIFT_CODE" ).rules( "add", {
        required: true,
        nowhitespace: true,
        //StringNumberRegex: true, //from custom.js
        messages: {
            required: "Required field.",
        }
    });

    $("#SHIFT_NAME").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_SHIFT_NAME").hide();
        validateSingleElemnet("SHIFT_NAME");
    });

    $( "#SHIFT_NAME" ).rules( "add", {
        required: true,
        //StringRegex: true,  //from custom.js
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#START_TIME").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_START_TIME").hide();
        validateSingleElemnet("START_TIME");
    });

    $( "#START_TIME" ).rules( "add", {
        required: true,
        //StringRegex: true,  //from custom.js
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });
    $("#END_TIME").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_END_TIME").hide();
        validateSingleElemnet("END_TIME");
    });

    $( "#END_TIME" ).rules( "add", {
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
          if(element_id=="SHIFT_CODE" || element_id=="SHIFT_CODE" ) {
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
            url:'{{route("master",[180,"codeduplicate"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_SHIFT_CODE',data.msg);
                    $("#SHIFT_CODE").focus();
                }else{
                  $(".text-danger").hide();
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

          var SHIFT_CODE          =   $.trim($("#SHIFT_CODE").val());
          if(SHIFT_CODE ===""){
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").hide();  
            $("#OkBtn2").hide();
            $("#OkBtn").show();              
            $("#AlertMessage").text('Please enter Shift Code.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            return false;
          }

          
          var START_TIME           =   $.trim($("#START_TIME").val());
          var END_TIME           =   $.trim($("#END_TIME").val());
          var START_TIME_OT           =   $.trim($("#START_TIME_OT").val());

          var MIN_HOURS_FULL           =   $.trim($("#MIN_HOURS_FULL").val());
          var MIN_MINS_FULL           =   $.trim($("#MIN_MINS_FULL").val());

          var MIN_HOURS_HALF           =   $.trim($("#MIN_HOURS_HALF").val());
          var MIN_MINS_HALF           =   $.trim($("#MIN_MINS_HALF").val());         

           START_TIME = START_TIME.replace(":",'');
           END_TIME = END_TIME.replace(":",'');
            
            // if(parseInt(START_TIME) >= parseInt(END_TIME)){
            //   $("#FocusId").val($("#START_TIME"));
            //   $("#ProceedBtn").focus();
            //   $("#YesBtn").hide();
            //   $("#NoBtn").hide();
            //   $("#OkBtn1").show();
            //   $("#AlertMessage").text('Start Time should be less than End Time');
            //   $("#alert").modal('show');
            //   $("#OkBtn1").focus();
            //   return false;

            // }else 
            if(START_TIME_OT !==""){

                START_TIME_OT = START_TIME_OT.replace(":",'');
                if(parseInt(END_TIME) >= parseInt(START_TIME_OT)){ 
                  $("#FocusId").val($("#START_TIME"));
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").show();
                  $("#OkBtn").hide();
                  $("#OkBtn2").hide();
                  $("#AlertMessage").text('OT Start time should be greater than End Time');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
                }

            }

            if(MIN_HOURS_FULL=="00" && MIN_MINS_FULL=="00" ){
                $("#FocusId").val($("#MIN_HOURS_FULL"));
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#OkBtn").hide();
                $("#OkBtn2").hide();
                $("#AlertMessage").text('Please select Full Day Min Hours');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                return false;
            }

            if(MIN_HOURS_FULL=="00" && MIN_MINS_FULL=="00" ){
                $("#FocusId").val($("#MIN_HOURS_FULL"));
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#OkBtn").hide();
                $("#OkBtn2").hide();
                $("#AlertMessage").text('Please select Full Day Min Hours.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                return false;
            }
            
  
            if(MIN_HOURS_HALF=="00" && MIN_MINS_HALF=="00" ){
                $("#FocusId").val($("#MIN_HOURS_FULL"));
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#OkBtn").hide();
                $("#OkBtn2").hide();
                $("#AlertMessage").text('Please select Half Day Min Hours.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                return false;
            }
            
              
              $("#OkBtn").hide();
              $("#OkBtn1").hide();
              $("#OkBtn2").hide();
              $("#YesBtn").show();
              $("#NoBtn").show();
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

        var getDataForm = $("#frm_mst_add");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[180,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.SHIFT_CODE){
                        showError('ERROR_SHIFT_CODE',data.errors.SHIFT_CODE);
                    }
                    if(data.errors.SHIFT_NAME){
                        showError('ERROR_SHIFT_NAME',data.errors.SHIFT_NAME);
                    }
                    if(data.errors.START_TIME){
                        showError('ERROR_START_TIME',data.errors.START_TIME);
                    }
                    if(data.errors.END_TIME){
                        showError('ERROR_END_TIME',data.errors.END_TIME);
                    }
                   if(data.exist=='duplicate') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn1").hide();
                      $("#OkBtn2").hide();
                      $("#OkBtn").show();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

                   }
                   if(data.save=='invalid') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn1").hide();
                      $("#OkBtn2").hide();
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
                    $("#OkBtn2").show();
                    $("#OkBtn1").hide();
                    $("#OkBtn").hide();

                    $("#AlertMessage").text(data.msg);

                    $(".text-danger").hide();
                    $("#frm_mst_add").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn2").focus();

                  //  window.location.href='{{ route("master",[180,"index"])}}';
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
        $("#OkBtn1").hide();
        $("#OkBtn2").hide();

        $(".text-danger").hide();
        $("#SHIFT_CODE").focus();
        
    }); ///ok button

    
    
    $("#btnUndo").click(function(){

        $("#AlertMessage").text("Do you want to erase entered information in this record?");
        $("#alert").modal('show');

        $("#YesBtn").data("funcname","fnUndoYes");
        $("#YesBtn").show();

        $("#NoBtn").data("funcname","fnUndoNo");
        $("#NoBtn").show();
        
        $("#OkBtn").hide();
        $("#OkBtn1").hide();
        $("#OkBtn2").hide();
        $("#NoBtn").focus();
        highlighFocusBtn('activeNo');
        
    }); ////Undo button

    $("#OkBtn2").click(function(){

    $("#alert").modal('hide');
    $("#YesBtn").show();  //reset
    $("#NoBtn").show();   //reset
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#OkBtn2").hide();
    $(".text-danger").hide();
    window.location.href = "{{route('master',[180,'index'])}}";

    }); 


    
    $("#OkBtn").click(function(){
      $("#alert").modal('hide');
      $("#OkBtn").hide();
      $("#OkBtn1").hide();
      $("#OkBtn2").hide();
      $("#YesBtn").show();
      $("#NoBtn").show();
    });////ok button

    $("#OkBtn1").click(function(){
      $("#alert").modal('hide');
      $("#OkBtn").hide();
      $("#OkBtn1").hide();
      $("#OkBtn2").hide();
      $("#YesBtn").show();
      $("#NoBtn").show();
      // $("#OkBtn").hide();
      // $("#OkBtn1").hide();
      // $("#"+$(this).data('focusname')).focus();
      // $("#SONO").focus();
      // $(".text-danger").hide();
    });



   window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('master',[180,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#SHIFT_CODE").focus();
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



    $(function() {
      
       $("#SHIFT_CODE").focus(); });
    

</script>

<script>
function AlphaNumaric(e, t) {
try {
  if (window.event) {
  var charCode = window.event.keyCode;
  }
  else if (e) {
  var charCode = e.which;
  }
  else { return true; }
  if ((charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122))
  return true;
  else
  return false;
  }
  catch (err) {
  alert(err.Description);
  }
}


$(document).ready(function() {

  $('#END_TIME').on('focusout',function(){

        var END_TIME = $.trim($(this).val());
        var START_TIME = $.trim($("#START_TIME").val());

        END_TIME = END_TIME.replace(":",'');
        START_TIME = START_TIME.replace(":",'');

       
        if( END_TIME == "" || START_TIME ==""){
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Start Time and End Time can not be left blank.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;

        }
        // else if(parseInt(START_TIME) >= parseInt(END_TIME)){ 
          
        //   $("#ProceedBtn").focus();
        //   $("#YesBtn").hide();
        //   $("#NoBtn").hide();
        //   $("#OkBtn1").show();
        //   $("#OkBtn").hide();
        //   $("#AlertMessage").text('Start time should be less than End Time');
        //   $("#alert").modal('show');
        //   $("#OkBtn1").focus();
        //   return false;

        // }
         else if( END_TIME != "" && START_TIME!=""){

            // var date1 = new Date("2021/11/01 " + $('#START_TIME').val()).getTime();
            // var date2 = new Date("2021/11/01 " + $('#END_TIME').val()).getTime();
            // var msec = date2 - date1;
            // var mins = Math.floor(msec / 60000);
            // var hrs = Math.floor(mins / 60);
            // var days = Math.floor(hrs / 24);
            // //var yrs = Math.floor(days / 365);

            // mins = mins % 60;
            // $("#TOTAL_HOURS").val(hrs + " hours, " + mins + " mins");

            calTime();
         
        }
  });

    $('#START_TIME').on('focusout',function(){

       var START_TIME = $.trim($(this).val());
       var END_TIME = $.trim($("#END_TIME").val());

        END_TIME = END_TIME.replace(":",'');
        START_TIME = START_TIME.replace(":",'');

      if( END_TIME != "" && START_TIME!=""){
          calTime();
      }
    });


}); //ready

function calTime(){


    var START_TIME           =   $.trim($("#START_TIME").val());
    var END_TIME           =   $.trim($("#END_TIME").val());
  
    START_TIME = START_TIME.replace(":",'');
    END_TIME = END_TIME.replace(":",'');
    
    if(parseInt(START_TIME) >= parseInt(END_TIME)){
        var date2 = new Date("2021/11/02 " + $('#END_TIME').val()).getTime();
    }else {
        var date2 = new Date("2021/11/01 " + $('#END_TIME').val()).getTime();
    }

    var date1 = new Date("2021/11/01 " + $('#START_TIME').val()).getTime();
    //var date2 = new Date("2021/11/01 " + $('#END_TIME').val()).getTime();
    var msec = date2 - date1;
    var mins = Math.floor(msec / 60000);
    var hrs = Math.floor(mins / 60);
    var days = Math.floor(hrs / 24);
    //var yrs = Math.floor(days / 365);

    mins = mins % 60;

    // if(hrs<0){
    //   hrs = parseInt(hrs)+24;
    // }

    $("#TOTAL_HOURS").val(hrs + " hours, " + mins + " mins");

}

window.onload = function(){
      var strdd = <?php echo json_encode($objDD); ?>;
      if($.trim(strdd)==""){     
        $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").show();
          $("#AlertMessage").text('Please contact to administrator for creating document numbering.');
          $("#alert").modal('show');
          $("#OkBtn").focus();
          highlighFocusBtn('activeOk');
      } 
    };

</script>

@endpush