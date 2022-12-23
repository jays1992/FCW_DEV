@extends('layouts.app')
@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="petientform   panel panel-default billingfont">
      <div class="form45 panel-heading" >
        <h3>Create Appointment</h3>
        <div class="buttonaddremove" >
          @if($res =="2")
          <button class="btn btn-success" id="btnSave"> Submit </button>
          <button class="btn btn-danger" id="btnUndo" > Reset </button>
          @endif
          <button id="btnList" class="btn btn-danger"  onclick="return window.history.back()"> Back </button>
        </div>
      </div>
      <form id="frm_trn_appointment" method="POST" class="needs-validation"> 
        @csrf
        <div class="container-fluid purchase-order-view filter"> 

        @if($res =="0")
          <div class="alert alert-danger" role="alert">
              Please update payment mode before book appointment in  profile !
          </div>
        @elseif($res =="1")
          <div class="alert alert-danger" role="alert">
              Please update insurance details before book appointment in profile insurance tab !
          </div>
        @else

    <div class="row" id="modal_default">
      <div class="col-xs-12 col-sm-12 col-md-12">
      <input type="hidden" id="APPOINTMENT_TRNID" name="APPOINTMENT_TRNID" /> 

          <div class="col-sm-12">
            <div class="row min-h">
              <div class="col-sm-2">
                <label>Appointment Type </label>
              </div>
              <div class="col-sm-2">
                <label>Office Visit</label>
                <input type="radio" name="APPOINTMENT_TYPE" value="Office Visit" checked  />
              </div>
              <div class="col-sm-2">
                <label>Video</label>
                <input type="radio" name="APPOINTMENT_TYPE" value="Video" />
              </div>
              <div class="col-sm-2">
                <label>Tele Consult</label>
                <input type="radio"  name="APPOINTMENT_TYPE" value="Tele Consult" />
              </div>
              
            </div>
          </div>




    <div class="col-sm-12">
      <div class="row min-h">
        <div class="col-sm-2">
          <label>Patient Name</label>
        </div>
        <div class="col-sm-4">
          <input type="text" id="txtpatientname" name="txtpatientname" value="{{ $objPatient->FIRST_NAME}} {{$objPatient->LAST_NAME}}" readonly autocomplete="off" placeholder="Search Patient name " class="form-control left-border" tabindex= 4 >
          <input type="hidden" id="PATIENTID_REF" name="PATIENTID_REF" value="{{ $objPatient->PATIENTID  }}"  />
        </div>
        </div>
      </div>
 

    <div class="col-sm-12">
      <div class="row min-h">
        <div class="col-sm-2">
          <label>Date</label>
        </div>

        <div class="col-sm-2">         
          <input type="text" name="Appoint_Date" id="Appoint_Date" value="{{date('M/d/Y')}}"  class="form-control mrgnbtm left-border" placeholder="MM/DD/YYYY" />
        </div>

        <div class="col-sm-2">         
          <input type="text" name="Appoint_Time" id="Appoint_Time" value=""  placeholder="TIME"  class="form-control mrgnbtm left-border" />
        </div>

        <div class="col-sm-2">
          <label>Duration</label>
        </div>
        <div class="col-sm-2">
          <select id="drpEndtime" name="drpEndtime" class="form-control mrgnbtm left-border" tabindex="10">
            
            <option value="0">Duration</option>
           
            <option value="15">15 min</option>
            
            <option value="20">20 min</option>
            <option value="30">30 min</option>
            <option value="45">45 min</option>
            <option value="60">1 hr</option>
            <option value="75">1:15 hr</option>
            <option value="90">1:30 hr</option>
           
          </select>
        </div>
      </div>
    </div>

    <div class="col-sm-12" id="div_visit" style="display:none;">
      <div class="row min-h">
        <div class="col-sm-2">
          <label>Visit Type</label>
        </div>
        <div class="col-sm-10" id="div_visitbody">
         
        </div>         
      </div>
    </div>

    <div class="col-sm-12">
      <div class="row min-h">
        <div class="col-sm-2">
          <label>Facility</label>
        </div>
        <div class="col-sm-4">
          <select id="GOID_REF" name="GOID_REF" class="form-control left-border" onchange="getProvider(this.value)" tabindex="12" >
            <option value="">Select</option>
            @if($objfacility)
              @foreach($objfacility as $findex=>$fRow)
                <option value="{{$fRow->GOID}}" selected>{{$fRow->PRACTICE_GROUPID}} - {{$fRow->PRACTICE_GROUPNAME}}</option>
              @endforeach
            @endif
          </select>
        </div>

        
        <div class="col-sm-2">
          <label>Provider</label>
        </div>
        <div class="col-sm-4" id="div_provider">
          <select id="PROVIDERID_REF" name="PROVIDERID_REF" class="form-control left-border" tabindex="13" >
            <option value="">Select</option>
          </select>
        </div>
      </div>
    </div>
    <div class="col-sm-12">
      <div class="row min-h">
      <div class="col-sm-2">
          <label>Billing Profile</label>
        </div>
        <div class="col-sm-4" id="div_billing">
          <select id="BPID_REF" name="BPID_REF" class="form-control left-border" tabindex="14" >
            <option>Select</option>
          </select>
        </div>
        <div class="col-sm-2">
          <label>Rooms</label>
        </div>
        <div class="col-sm-4" id="div_room">
          <select id="ROOMID_REF" name="ROOMID_REF" class="form-control" tabindex="15" >
            <option>Select</option>
          </select>
        </div>        
      </div>
    </div>


    <div class="col-sm-12">
      <div class="row min-h">
        <div class="col-sm-2">
          <label>Reason</label>
        </div>
        <div class="col-sm-4">
        <textarea id="REASON" name="REASON" class="form-control" tabindex="9" maxlength="200" lines="3" ></textarea>
        </div>
        <div class="col-sm-2">
          <label>Notes</label>
        </div>
        <div class="col-sm-4">
        <textarea id="NOTES" name="NOTES" class="form-control" tabindex="9" maxlength="200" lines="3" ></textarea>
        </div>
      </div>
    </div>
    <div class="col-sm-12">
      <div class="row min-h">
        <div class="col-sm-2">
          <label>Total Amount</label>
        </div>
        <div class="col-sm-4">
          <input type="text" id="TOTAL_AMT" name="TOTAL_AMT" class="form-control" tabindex="18" value="0.00" readonly />
        </div>        
      </div>
    </div>


</div>

@endif
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('alert')
<!-- Alert -->
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog"   >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='closePopup' >&times;</button>
        <h4 class="modal-title">{{Session::get('ALERT_MESSAGE_HEADING')}}</h4>
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

@push('bottom-scripts')
<script>
$(document).ready(function() {
    $('#div_billing').on('change','#BPID_REF',function(){
        var BPID = $(this).val();
        if(BPID != '')
        {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[545,"getBillingTotal"])}}',
            type:'POST',
            data:{'BPID':BPID},
            success:function(data) {
              $('#TOTAL_AMT').val(data);
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
          });
        }
      });
  
      $('#div_provider').on('change','#PROVIDERID_REF',function(e){
      var PROVIDERID_REF = $(this).val();
      var Appoint_Date          =  moment($('#Appoint_Date').val()).format('DD/MM/YYYY');
      var Appoint_Time          =   $('#Appoint_Time').val();
        if(PROVIDERID_REF!=''){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[545,"GetProviderSchedule"])}}',
            type:'POST',
            data:{'PROVIDERID_REF':PROVIDERID_REF,'Appoint_Date':Appoint_Date,'Appoint_Time':Appoint_Time},
            success:function(data) {
                if(data.exists) {                   
                    console.log("cancel MSG="+data.msg);
                    $("#FocusId").val($("#PROVIDERID_REF"));
                    $('#PROVIDERID_REF').val('');
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").html(data.msg);
                    $(".text-danger").hide();
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                }   
            },
            error:function(data){
                console.log("Error: Something went wrong.");
            },
        }); 
        $.ajax({
            url:'{{route("transaction",[545,"codeduplicate2"])}}',
            type:'POST',
            data:{'PROVIDERID_REF':PROVIDERID_REF,'Appoint_Date':Appoint_Date,'Appoint_Time':Appoint_Time},
            success:function(data) {
                if(data.exists) {                   
                    console.log("cancel MSG="+data.msg);
                    $("#FocusId").val($("#PROVIDERID_REF"));
                    $('#PROVIDERID_REF').val('');
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").hide();
                    $("#OkBtn").show();
                    $("#AlertMessage").text(data.msg);
                    $(".text-danger").hide();
                    $("#alert").modal('show');
                    $("#OkBtn").focus();
                    highlighFocusBtn('activeOk1');
                    $('#modal_default').hide();
                }   
            },
            error:function(data){
                console.log("Error: Something went wrong.");
            },
        }); 
        }
    });
    

  $('#modal_default').on('change','#drpEndtime',function(e){

    var PATIENTID_REF   =   $('#PATIENTID_REF').val(); 
    var Appoint_Date    =   $('#Appoint_Date').val();
    var Appoint_Time    =   $('#Appoint_Time').val();

    if(Appoint_Date ===""){
        $("#FocusId").val('Appoint_Date');
        $('#drpEndtime').val('');
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select date.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
      }
      else if(Appoint_Time ===""){
        $("#FocusId").val('Appoint_Time');
        $('#drpEndtime').val('');
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select time.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
      }
      else{
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{route("transaction",[545,"codeduplicate"])}}',
          type:'POST',
          data:{PATIENTID_REF:PATIENTID_REF,Appoint_Date:Appoint_Date,Appoint_Time:Appoint_Time},
          success:function(data) {
              if(data.exists) {                   
                  console.log("cancel MSG="+data.msg);
                  $('#Appoint_Date').val('');
                  $('#drpEndtime').val('');
                  $("#FocusId").val($("#Appoint_Date"));
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text(data.msg);
                  $(".text-danger").hide();
                  $("#frm_trn_appointment").trigger("reset");
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk1');
              }   
          },
          error:function(data){
              console.log("Error: Something went wrong.");
          },
      }); 

        var GOID = $('#GOID_REF').val();
          if(GOID != '')
          {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transaction",[545,"getProvider"])}}',
                type:'POST',
                data:{'GOID':GOID},
                success:function(data) {
                  $("#div_provider").html(data);   
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                },
            });
            $.ajax({
                url:'{{route("transaction",[545,"getRooms"])}}',
                type:'POST',
                data:{'GOID':GOID},
                success:function(data) {
                  $("#div_room").html(data);   
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                },
            }); 
          }
    }
    
  });

});

$( "#btnSave" ).click(function() {

    var formCreateAppointment = $("#frm_trn_appointment");
    if(formCreateAppointment.valid()){

      $("#FocusId").val('');
  // var PATIENTID_REF         =   $("#PATIENTID_REF").val();
  var Appoint_Date          =   $("#Appoint_Date").val();
  var Appoint_Time          =   $("#Appoint_Time").val();
  var DURATION              =   $("#drpEndtime").val();
  var REASON                =   $("#REASON").val();
  var GOID_REF              =   $("#GOID_REF").val();
  var PROVIDERID_REF        =   $("#PROVIDERID_REF").val();
  var BPID_REF              =   $("#BPID_REF").val();
  var ROOMID_REF            =   $("#ROOMID_REF").val();
  var Appoint_Date1         =  moment($('#Appoint_Date').val()).format('DD/MM/YYYY');
  var Appoint_Time1         =   $('#Appoint_Time').val();

  if(PROVIDERID_REF!=''){
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{route("transaction",[545,"GetProviderSchedule"])}}',
          type:'POST',
          data:{'PROVIDERID_REF':PROVIDERID_REF,'Appoint_Date':Appoint_Date1,'Appoint_Time':Appoint_Time1},
          success:function(data) {
              if(data.exists) {                   
                  console.log("cancel MSG="+data.msg);
                  $("#FocusId").val($("#PROVIDERID_REF"));
                  $('#PROVIDERID_REF').val('');
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").html(data.msg);
                  $(".text-danger").hide();
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk1');
                  return false;
              }   
          },
          error:function(data){
              console.log("Error: Something went wrong.");
          },
      }); 
      $.ajax({
          url:'{{route("transaction",[545,"codeduplicate2"])}}',
          type:'POST',
          data:{'PROVIDERID_REF':PROVIDERID_REF,'Appoint_Date':Appoint_Date,'Appoint_Time':Appoint_Time},
          success:function(data) {
              if(data.exists) {                   
                  console.log("cancel MSG="+data.msg);
                  $("#FocusId").val($("#PROVIDERID_REF"));
                  $('#PROVIDERID_REF').val('');
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").hide();
                  $("#OkBtn").show();
                  $("#AlertMessage").text(data.msg);
                  $(".text-danger").hide();
                  $("#alert").modal('show');
                  $("#OkBtn").focus();
                  highlighFocusBtn('activeOk1');
                  $('#modal_default').hide();
                  return false;
              }   
          },
          error:function(data){
              console.log("Error: Something went wrong.");
          },
      }); 
  }

  if(Appoint_Date ===""){
    $("#FocusId").val('Appoint_Date');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(Appoint_Time ===""){
    $("#FocusId").val('Appoint_Time');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select time.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(DURATION ==="0"){
    $("#FocusId").val('drpEndtime');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select duration.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(GOID_REF ===""){
    $("#FocusId").val('GOID_REF');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select facility.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(REASON ===""){
    $("#FocusId").val('REASON');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter  reason.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(PROVIDERID_REF ===""){
    $("#FocusId").val('PROVIDERID_REF');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Provider.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(BPID_REF ===""){
    $("#FocusId").val('BPID_REF');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Billing Profile.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  // else if(ROOMID_REF ===""){
  //   $("#FocusId").val('ROOMID_REF');
  //   $("#ProceedBtn").focus();
  //   $("#YesBtn").hide();
  //   $("#NoBtn").hide();
  //   $("#OkBtn").hide();
  //   $("#OkBtn1").show();
  //   $("#AlertMessage").text('Please select Room.');
  //   $("#alert").modal('show');
  //   $("#OkBtn1").focus();
  //   return false;
  // }
  else{
            // $("#alert").modal('show');
            // $("#AlertMessage").text('Do you want to save to record.');
            // $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
            // $("#YesBtn").focus();
            // $("#OkBtn").hide();
            // $("#OkBtn1").hide();
            // $("#YesBtn").show();
            // $("#NoBtn").show();
            // highlighFocusBtn('activeYes');
            $('#YesBtn').trigger('click');

      }
  }
});

window.fnSaveData = function (){
event.preventDefault();

var formCreateAppointment = $("#frm_trn_appointment");
var formData = formCreateAppointment.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$("[id*=btnSave]").attr("disabled", true);
$.ajax({
    url:'{{ route("transaction",[545,"save"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $("[id*=btnSave]").attr("disabled", false);
       
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.LABEL){
                showError('ERROR_LABEL',data.errors.LABEL);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in Label.');
                        $("#alert").modal('show');
                        $("#OkBtn1").focus();
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
            $("#OkBtn1").hide();
            $("#AlertMessage").text(data.msg);
            $(".text-danger").hide();
            $("#alert").modal('show');
            $("#OkBtn").focus();
        }
        else if(data.cancel) {                   
            console.log("cancel MSG="+data.msg);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#OkBtn").hide();
            $("#AlertMessage").text(data.msg);
            $(".text-danger").hide();
            $("#alert").modal('show');
            $("#OkBtn1").focus();
        }
        else 
        {                   
            console.log("succes MSG="+data.msg);
            $("#OkBtn").hide();
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
        $("[id*=btnSave]").attr("disabled", false);
        console.log("Error: Something went wrong.");
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#OkBtn").hide();
        $("#AlertMessage").text('Error: Something went wrong.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk1');
    },
});
}

$("#btnUndo").on("click", function() {
    $("#AlertMessage").text("Do you want to erase entered information in this record?");
    $("#alert").modal('show');

    $("#YesBtn").data("funcname","fnUndoYes");
    $("#YesBtn").show();

    $("#NoBtn").data("funcname","fnUndoNo");
    $("#NoBtn").show();
    
    $("#OkBtn").hide();
    $("#NoBtn").focus();
});



window.fnUndoYes = function (){
    window.location.reload();
}

$("#YesBtn").click(function(){
$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();
});

$("#NoBtn").click(function(){
    $("#alert").modal('hide');
    $("#txtpatientname").focus();
});

$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("transaction",[44,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
    $(".text-danger").hide();
});

function showError(pId,pVal){
  $("#"+pId+"").text(pVal);
  $("#"+pId+"").show();
}

function getFocus(){
  var FocusId=$("#FocusId").val();
  $("#"+FocusId).focus();
  $("#closePopup").click();
}

function highlighFocusBtn(pclass){
  $(".activeYes").hide();
  $(".activeNo").hide();
  
  $("."+pclass+"").show();
}

function getProvider(GOID){
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
      url:'{{route("transaction",[545,"getProvider"])}}',
      type:'POST',
      data:{'GOID':GOID,VALUE:'',TYPE:'ADD'},
      success:function(data) {
        $("#PROVIDERID_REF").html(data);   
      },
      error:function(data){
        console.log("Error: Something went wrong.");
      },
  });
}

function getVisitStatus(){
  var PATIENTID_REF =   $('#PATIENTID_REF').val();
  var Appoint_Date  =   $("#Appoint_Date").val();
  var date          =   new Date(Appoint_Date);
  var AppointDate   =   date.toISOString().slice(0,10);

  $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("transaction",[545,"GetPatientVisitStatus"])}}',
        type:'POST',
        data:{'PATIENTID_REF':PATIENTID_REF,'AppointDate':AppointDate},
        success:function(data) {
            $("#div_visitbody").html(data);
            $('#div_visit').show();
        },
        error:function(data){
            console.log("Error: Something went wrong.");
        },
    });   
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("transaction",[545,"getBillingProfile"])}}',
        type:'POST',
        data:{'PATIENTID_REF':PATIENTID_REF,'AppointDate':AppointDate},
        success:function(result) {
        $("#div_billing").html(result);
        },
        error:function(result){
        console.log("Error: Something went wrong.");
        },
    });           
}
</script>

<link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet" type="text/css"/>  
<link  href="{{ asset('css/offline/jquery.timepicker.min.css') }}" rel="stylesheet">
<script src="{{ asset('js/offline/jquery.timepicker.min.js') }}"></script> 

<script type="text/javascript">

$(function(){
  $("#Appoint_Date").datepicker({ dateFormat: "MM/dd/yy" }).val();
  $("#Appoint_Time").timepicker({ 
    timeFormat: 'HH:mm p',
		use24hours: true,
		interval: 15
    }).val();
  getVisitStatus();    
}); 

</script> 
@endpush
