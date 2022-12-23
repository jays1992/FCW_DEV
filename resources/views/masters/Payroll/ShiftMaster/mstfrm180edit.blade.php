@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[180,'index'])}}" class="btn singlebt">Shift Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button id="btnSave"   class="btn topnavbt" tabindex="7"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt"  id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove"{{($objRights->APPROVAL1 || $objRights->APPROVAL2 || $objRights->APPROVAL3 || $objRights->APPROVAL4 || $objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_edit" method="POST"  > 
          @CSRF
          {{isset($objResponse->SHIFTID) ? method_field('PUT') : '' }}
          <div class="inner-form">
          
              <div class="row">
              <div class="col-lg-2 pl"><p>Shift Code</p></div>
              <div class="col-lg-2 pl">                  
                <label> {{$objResponse->SHIFT_CODE}} </label>
                <input type="hidden" name="SHIFTID" id="SHIFTID" value="{{ $objResponse->SHIFTID }}" />
                <input type="hidden" name="SHIFT_CODE" id="SHIFT_CODE" value="{{ $objResponse->SHIFT_CODE }}" autocomplete="off"  maxlength="20"   />
                <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
            </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Shift Name</p></div> 
              <div class="col-lg-4 pl">
                <input type="text" name="SHIFT_NAME" id="SHIFT_NAME" class="form-control mandatory" value="{{ old('SHIFT_NAME',$objResponse->SHIFT_NAME) }}" maxlength="200" tabindex="2"  required/>
                <span class="text-danger" id="ERROR_SHIFT_NAME"></span> 
              </div>
            </div>

            <div class="row">
                <div class="col-lg-2 pl"><p>Start Time</p></div>
                <div class="col-lg-2 pl">
                  @php
                    $starr = explode(".",$objResponse->START_TIME);
                  @endphp
                  <input type="time" name="START_TIME" id="START_TIME" class="form-control mandatory" value="{{isset($starr[0]) ? $starr[0] : '' }}"  tabindex="3"  required/> 
                  <span class="text-danger" id="ERROR_START_TIME"></span> 
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2 pl"><p>End Time</p></div>
                <div class="col-lg-2 pl">
                  @php
                      $endarr = explode(".",$objResponse->END_TIME);
                  @endphp
                  <input type="time" name="END_TIME" id="END_TIME" class="form-control mandatory" value="{{isset($endarr[0]) ? $endarr[0] : '' }}" tabindex="4" required />                        
                  <span class="text-danger" id="ERROR_END_TIME"></span>
                </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Total Hours</p></div>
              <div class="col-lg-1 pl">
                <input name="TOTAL_HOURS" id="TOTAL_HOURS" class="form-control mandatory" value="0.00" tabindex="5" readonly />                        
                <span class="text-danger" id="ERROR_TOTAL_HOURS"></span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Full Day Min Hours</p></div>
              <div class="col-lg-1 pl">
                @php
                  $minhrs_full = explode(":",$objResponse->MIN_HOURS_FULL);    
                @endphp
                
                  <select class="form-control" name="MIN_HOURS_FULL" id="MIN_HOURS_FULL" tabindex="6" required>
                    <option value="00" {{ $minhrs_full[0]=="00"? 'selected': '' }}>00 Hours</option>
                    <option value="01" {{ $minhrs_full[0]=="01"? 'selected': '' }}>01 hours</option>
                    <option value="02" {{ $minhrs_full[0]=="02"? 'selected': '' }}>02 hours</option>
                    <option value="03" {{ $minhrs_full[0]=="03"? 'selected': '' }} >03 hours</option>
                    <option value="04" {{ $minhrs_full[0]=="04"? 'selected': '' }}>04 hours</option>
                    <option value="05" {{ $minhrs_full[0]=="05"? 'selected': '' }}>05 hours</option>
                    <option value="06" {{ $minhrs_full[0]=="06"? 'selected': '' }}>06 hours</option>
                    <option value="07" {{ $minhrs_full[0]=="07"? 'selected': '' }}>07 hours</option>
                    <option value="08" {{ $minhrs_full[0]=="08"? 'selected': '' }}>08 hours</option>
                    <option value="09" {{ $minhrs_full[0]=="09"? 'selected': '' }}>09 hours</option>
                    <option value="10" {{ $minhrs_full[0]=="10"? 'selected': '' }}>10 hours</option>
                    <option value="11" {{ $minhrs_full[0]=="11"? 'selected': '' }}>11 hours</option>
                    <option value="12" {{ $minhrs_full[0]=="12"? 'selected': '' }}>12 hours</option>
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
                      <option value="{{$keycode}}" {{ $i==$minhrs_full[1] ? 'selected':'' }}>{{ $keycode }}  mins</option>
                    @endfor
                  </select>
              </div>
              <div class="col-lg-2 pl"><p>Half Day Min Hours</p></div>
              <div class="col-lg-1 pl">
                @php
                  $minhrs_half = explode(":",$objResponse->MIN_HOURS_HALF);    
                @endphp
                <select class="form-control" name="MIN_HOURS_HALF" id="MIN_HOURS_HALF" tabindex="8" required>
                  <option value="00" {{ $minhrs_half[0]=="00"? 'selected': '' }}>00 Hours</option>
                  <option value="01" {{ $minhrs_half[0]=="01"? 'selected': '' }}>01 hours</option>
                  <option value="02" {{ $minhrs_half[0]=="02"? 'selected': '' }}>02 hours</option>
                  <option value="03" {{ $minhrs_half[0]=="03"? 'selected': '' }} >03 hours</option>
                  <option value="04" {{ $minhrs_half[0]=="04"? 'selected': '' }}>04 hours</option>
                  <option value="05" {{ $minhrs_half[0]=="05"? 'selected': '' }}>05 hours</option>
                  <option value="06" {{ $minhrs_half[0]=="06"? 'selected': '' }}>06 hours</option>
                  <option value="07" {{ $minhrs_half[0]=="07"? 'selected': '' }}>07 hours</option>
                  <option value="08" {{ $minhrs_half[0]=="08"? 'selected': '' }}>08 hours</option>
                  <option value="09" {{ $minhrs_half[0]=="09"? 'selected': '' }}>09 hours</option>
                  <option value="10" {{ $minhrs_half[0]=="10"? 'selected': '' }}>10 hours</option>
                  <option value="11" {{ $minhrs_half[0]=="11"? 'selected': '' }}>11 hours</option>
                  <option value="12" {{ $minhrs_half[0]=="12"? 'selected': '' }}>12 hours</option>
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
                    <option value="{{$keycode}}" {{ $i==$minhrs_half[1] ?'selected':'' }}>{{ $keycode }}  mins</option>
                  @endfor
                </select>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-2 pl"><p>Grace Time for In-Time</p></div>
            <div class="col-lg-1 pl">
                @php
                  $grace_intime = explode(":",$objResponse->GRACE_TIME_IN);    
                @endphp
              
                <select class="form-control" name="GRACE_TIME_IN_HOURS" id="GRACE_TIME_IN_HOURS" tabindex="10" required>
                  <option value="00" {{ $grace_intime[0]=="00"? 'selected': '' }}>00 Hours</option>
                  <option value="01" {{ $grace_intime[0]=="01"? 'selected': '' }}>01 hours</option>
                  <option value="02" {{ $grace_intime[0]=="02"? 'selected': '' }}>02 hours</option>
                  <option value="03" {{ $grace_intime[0]=="03"? 'selected': '' }} >03 hours</option>
                  <option value="04" {{ $grace_intime[0]=="04"? 'selected': '' }}>04 hours</option>
                  <option value="05" {{ $grace_intime[0]=="05"? 'selected': '' }}>05 hours</option>
                  <option value="06" {{ $grace_intime[0]=="06"? 'selected': '' }}>06 hours</option>
                  <option value="07" {{ $grace_intime[0]=="07"? 'selected': '' }}>07 hours</option>
                  <option value="08" {{ $grace_intime[0]=="08"? 'selected': '' }}>08 hours</option>
                  <option value="09" {{ $grace_intime[0]=="09"? 'selected': '' }}>09 hours</option>
                  <option value="10" {{ $grace_intime[0]=="10"? 'selected': '' }}>10 hours</option>
                  <option value="11" {{ $grace_intime[0]=="11"? 'selected': '' }}>11 hours</option>
                  <option value="12" {{ $grace_intime[0]=="12"? 'selected': '' }}>12 hours</option>
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
                    <option value="{{$keycode}}" {{ $i==$grace_intime[1] ?'selected':'' }}>{{ $keycode }}  mins</option>
                  @endfor
                </select>
            </div>
            <div class="col-lg-2 pl"><p>Break Hour Time</p></div>
            <div class="col-lg-1 pl">
              @php
                $break_time = explode(":",$objResponse->BREAK_TIME);    
              @endphp
              <select class="form-control" name="BREAK_TIME_HOURS" id="BREAK_TIME_HOURS" tabindex="12" required>
                <option value="00" {{ $break_time[0]=="00"? 'selected': '' }}>00 Hours</option>
                <option value="01" {{ $break_time[0]=="01"? 'selected': '' }}>01 hours</option>
                <option value="02" {{ $break_time[0]=="02"? 'selected': '' }}>02 hours</option>
                <option value="03" {{ $break_time[0]=="03"? 'selected': '' }} >03 hours</option>
                <option value="04" {{ $break_time[0]=="04"? 'selected': '' }}>04 hours</option>
                <option value="05" {{ $break_time[0]=="05"? 'selected': '' }}>05 hours</option>
                <option value="06" {{ $break_time[0]=="06"? 'selected': '' }}>06 hours</option>
                <option value="07" {{ $break_time[0]=="07"? 'selected': '' }}>07 hours</option>
                <option value="08" {{ $break_time[0]=="08"? 'selected': '' }}>08 hours</option>
                <option value="09" {{ $break_time[0]=="09"? 'selected': '' }}>09 hours</option>
                <option value="10" {{ $break_time[0]=="10"? 'selected': '' }}>10 hours</option>
                <option value="11" {{ $break_time[0]=="11"? 'selected': '' }}>11 hours</option>
                <option value="12" {{ $break_time[0]=="12"? 'selected': '' }}>12 hours</option>
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
                  <option value="{{$keycode}}" {{ $i==$break_time[1] ?'selected':'' }}>{{ $keycode }}  mins</option>
                @endfor
              </select>
          </div>
        </div>

          <div class="row">
            <div class="col-lg-2 pl"><p>Date Change in same Shift</p></div>
            <div class="col-lg-2 pl">
            <label class="radio-inline">
                <input   type="radio" name="SHIFT_DTCHANGE_YES" id="RADIO_SHIFT_DTCHANGE_YES" value="1" tabindex="14" {{$objResponse->SHIFT_DTCHANGE_YES == 1 ? "checked" : ""}}/>    Yes
            </label>
            <label class="radio-inline">
                <input   type="radio" name="SHIFT_DTCHANGE_YES" id="RADIO_SHIFT_DTCHANGE_NO" value="0" tabindex="14" {{$objResponse->SHIFT_DTCHANGE_YES == 0 ? "checked" : ""}}/>   No
            </label>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-2 pl"><p>OT Start time</p></div>
            <div class="col-lg-2 pl">
              @php
                  $otarr = explode(".",$objResponse->START_TIME_OT);
              @endphp
              <input type="time" name="START_TIME_OT" id="START_TIME_OT" value="{{isset($otarr[0]) ? $otarr[0] : '' }}" class="form-control " tabindex="15" />   
            </div>
          </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>De-Activated</p></div>
              <div class="col-lg-2 pl pr">
              <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
                value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="4"  >
              </div>
              
              <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
              <div class="col-lg-2 pl">
                <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="5" placeholder="dd/mm/yyyy"  />
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
// $('#btnAdd').on('click', function() {
//       var viewURL = '{{route("master",[180,"add"])}}';
//       window.location.href=viewURL;
//   });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formDataMst = $( "#frm_mst_edit" );
     formDataMst.validate();

     $("#SHIFT_CODE").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_PERIOD_CODE").hide();
      validateSingleElemnet("SHIFT_CODE");
         
    });

    $( "#SHIFT_CODE" ).rules( "add", {
        required: true,
        nowhitespace: true,
        StringNumberRegex: true, //from custom.js
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
                $("#AlertMessage").text('Please select Half Day Min Hours.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                return false;
            }
            
              
              $("#OkBtn").hide();
              $("#OkBtn1").hide();
              $("#YesBtn").show();
              $("#NoBtn").show();
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
                $("#AlertMessage").text('Please select Half Day Min Hours.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                return false;
            }
            
              
              $("#OkBtn").hide();
              $("#OkBtn1").hide();
              $("#YesBtn").show();
              $("#NoBtn").show();
              $("#alert").modal('show');
              $("#AlertMessage").text('Do you want to save to record.');
              $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name
              $("#YesBtn").focus();
              highlighFocusBtn('activeYes');   

        }

    });//btn approve


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
            url:'{{route("mastermodify",[180,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
              if(data.errors) {
                    $(".text-danger").hide();                  
                   if(data.exist=='norecord') {
                    $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

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
            url:'{{route("mastermodify",[180,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();                  
                   if(data.exist=='norecord') {
                    $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

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
        window.location.href = '{{route("master",[180,"index"]) }}';

    }); ///ok button

    $("#OkBtn1").click(function(){
      $("#alert").modal('hide');
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

    }); ////Undo button

   
    $("#OkBtn").click(function(){
      
        $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.reload();

   }//fnUndoYes


   window.fnUndoNo = function (){

      $("#PLCODE").focus();

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
		  $('#DODEACTIVATED').prop('required',true);
		}
		else {
		  $(this).val('0');
		  $('#DODEACTIVATED').prop('disabled', true);
      $('#DODEACTIVATED').removeAttr('required');
		  $('#DODEACTIVATED').val('');
		  $('#DODEACTIVATED').removeClass("error");
		  $('#DODEACTIVATED-error').hide();
		  
		}
	});

});


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
  // else if(parseInt(START_TIME) >= parseInt(END_TIME))
  // { 
    
  //   $("#ProceedBtn").focus();
  //   $("#YesBtn").hide();
  //   $("#NoBtn").hide();
  //   $("#OkBtn").hide();
  //   $("#OkBtn1").show();
  //   $("#AlertMessage").text('Start time should be less than End Time');
  //   $("#alert").modal('show');
  //   $("#OkBtn1").focus();
  //   return false;

  // } 
  else if( END_TIME != "" && START_TIME!=""){

      // var shiftend =   $.trim($("#END_TIME").val());
      // var endTime = shiftend.split(":");
      
      // var shiftstart =   $.trim($("#START_TIME").val());
      // var startTime = shiftstart.split(":");
      
      // var total_hrs = parseInt(endTime[0]) - parseInt(startTime[0] );
      // var total_mins = parseInt(endTime[1]) - parseInt(startTime[1]);

      // $("#TOTAL_HOURS").val(total_hrs+ " hrs "+total_mins+' mins' );
      calTime();
  
  }
  });

  $('#START_TIME').on('focusout',function(){

        var START_TIME = $.trim($(this).val());
        var END_TIME = $.trim($("#END_TIME").val());

        END_TIME = END_TIME.replace(":",'');
        START_TIME = START_TIME.replace(":",'');

        if( END_TIME != "" && START_TIME!=""){

          // var shiftend =   $.trim($("#END_TIME").val());
          // var endTime = shiftend.split(":");
          
          // var shiftstart =   $.trim($("#START_TIME").val());
          // var startTime = shiftstart.split(":");
          
          // var total_hrs = parseInt(endTime[0]) - parseInt(startTime[0] );
          // var total_mins = parseInt(endTime[1]) - parseInt(startTime[1]);

          // $("#TOTAL_HOURS").val(total_hrs+ " hrs "+total_mins+' mins' );
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
    var msec = date2 - date1;
    var mins = Math.floor(msec / 60000);
    var hrs = Math.floor(mins / 60);
    var days = Math.floor(hrs / 24);
    //var yrs = Math.floor(days / 365);

    mins = mins % 60;
    $("#TOTAL_HOURS").val(hrs + " hours, " + mins + " mins");

}

window.onload = function(){

  calTime();

}
</script>


@endpush