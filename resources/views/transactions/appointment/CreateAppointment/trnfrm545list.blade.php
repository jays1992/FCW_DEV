@extends('layouts.app')
@section('content')    
<div class="row">
	<div class="col-sm-12">
		<div class="petientform   panel panel-default billingfont">
			<div class="form45 panel-heading">
			  <h3>View Appointments</h3>
        <div class="buttonaddremove">
          <button id="btnList" class="btn btn-danger"  onclick="return window.history.back()"> Back </button>
        </div>
			</div>
      <form id="frm_trn_viewappointment"   method="POST" class="needs-validation">
        <div id="div1" class="table-wrap appointmentsdetailsc">
        
          <div class="home-table table-responsive">
            
            <table id="example" class="table table-striped table-bordered" style="width:100%" >
              <thead id="thead1">
              <tr>
                <th>Status</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Time</th>
                <th>Appt.Type</th>
                <th>Notes</th>
                <th>Action</th>
              </tr> 
                        
              </thead>
              <tbody id="divdata"> 
              @if(!empty($sp_provider))           
              @foreach($sp_provider as $key => $val)
                <tr>
                  <td>{{$val->STATUS}}</td>
                  <td>
                    {{ $val->FIRST_NAME }} {{ $val->LAST_NAME }}
                    <br>
                    <i class="fa fa-calendar">&nbsp;</i>{{ isset($val->DOB) && $val->DOB !=""?date('m-d-Y',strtotime($val->DOB)):'' }}
                    
                    <br>
                    @if($val->APPOINTMENT_TYPE == 'TELE CONSULT')

                      @if(isset($val->TOTAL_AMT) && $val->TOTAL_AMT !="")
                        <a id="#" value="{{$val->CELL_PHONE }}" ><i class="fa fa-mobile">&nbsp;</i>{{$val->CELL_PHONE }}</a><br/>
                      @else
                        <i class="fa fa-mobile">&nbsp;</i>{{$val->CELL_PHONE }}<br/>
                      @endif

                    @else
                    <i class="fa fa-mobile">&nbsp;</i>{{$val->CELL_PHONE }}<br/>
                    @endif
                    
                    @if($val->APPOINTMENT_TYPE == 'VIDEO')
                      @if(isset($val->TOTAL_AMT) && $val->TOTAL_AMT !="")
                      <br>
                      <a onclick="videocall('{{base64_encode($val->APPOINTMENT_TRNID)}}')" style="cursor:pointer;" ><i class="fa fa-video">&nbsp;</i>Video Call</a><br/>
                      @endif
                    @endif

                  </td>
                  <td>{{$val->DoctorName}}</td>
                  <td> {{ isset($val->DATE) && $val->DATE !=""?date('m-d-Y',strtotime($val->DATE)):'' }}</b></td>
                  <td>{{isset($val->TIME) && $val->TIME !=""?date("H:i A",strtotime($val->TIME)):''}}</td>

                  <td>
                    {{$val->PROFILE_TYPE}} /
                    {{$val->APPOINTMENT_TYPE}}

                    @if($val->CONSTENT_FORM_REQUESTED == 1)

                      @if($val->CONSSENT_TRNID =="")
                        <br/><a href='{{route("transaction",[34,"add"])}}/id={{$val->PATIENTID_REF}}&AppointId={{$val->APPOINTMENT_TRNID}}' >Consent</a>
                      @else
                        <br/><a href='{{route("transaction",[34,"edit",$val->CONSSENT_TRNID])}}' >Consent</a>
                      @endif

                    @endif              
                  </td>

                  <td>{{$val->NOTES}}</td>
                  <td>
                    <input type="hidden" value="{{$val->STATUS}}" id="hdnstatus{{ $val->APPOINTMENT_TRNID }}" name="hdnstatus{{ $val->APPOINTMENT_TRNID }}" />
                    <button class="btn btn-editbutton" id="Approve{{ $val->APPOINTMENT_TRNID }}" value="{{ $val->APPOINTMENT_TRNID }}">
                    <i class="fa fa-edit"></i> Approve </button><br/>
                    <button class="btn btn-editbutton" id="Edit{{ $val->APPOINTMENT_TRNID }}" value="{{ $val->APPOINTMENT_TRNID }}">
                    <i class="fa fa-calendar"></i> Reschedule </button><br/>
                    <button class="btn btn-editbutton" id="Cancel{{ $val->APPOINTMENT_TRNID }}" value="{{ $val->APPOINTMENT_TRNID }}">
                    <i class="fa fa-trash"></i> Cancel </button>                    
                  </td>
              </tr>

              @endforeach 
              @endif
              </tbody>
            </table>
          </div>
        </div>
      </form>
		</div>
	</div>
</div>
@endsection
@section('alert')


<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog" style="position:relative;top:82px;left:273px;"  >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='closePopup' >&times;</button>
        <h4 class="modal-title">{{Session::get('ALERT_MESSAGE_HEADING')}}</h4>
      </div>
      <div class="modal-body">
	  <h5 id="AlertMessage" ></h5>
        <div class="btdiv">    
            <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData" style="display:none;"> 
                <div id="alert-active" class="activeYes"></div> Yes</button>
            <button class="btn alertbt" name='NoBtn' id="NoBtn"  data-funcname="fnUndoNo"  style="display:none;">
                <div id="alert-active" class="activeNo"></div>No</button>
            <button class="btn alertbt" name='OkBtn' id="OkBtn" style="margin-left: 90px;">
                <div id="alert-active" class="activeOk"></div>OK</button>
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="margin-left: 90px;display:none;">
                <div id="alert-active" class="activeOk"></div>OK</button>
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('bottom-scripts')
<script>
$('#div1').on('click','[id*="Approve"]', function() {
            var APPOINTMENT_TRNID = $(this).val();
            var viewURL = '{{route("transaction",[545,"approval",":rcdId"]) }}';
            viewURL = viewURL.replace(":rcdId",APPOINTMENT_TRNID);
            window.location.href=viewURL;
            event.preventDefault();
    });

$('#div1').on('click','[id*="Edit"]', function() {
            var APPOINTMENT_TRNID = $(this).val();
            var viewURL = '{{route("transaction",[545,"approval",":rcdId"]) }}';
            viewURL = viewURL.replace(":rcdId",APPOINTMENT_TRNID);
            window.location.href=viewURL;
            event.preventDefault();
});



    $('#div1').on('click','[id*="Cancel"]', function() {
      event.preventDefault();
          var APPOINTMENT_TRNID = $(this).val();
          var Status = $(this).parent().find('[id*="hdnstatus"]').val();

          if(Status == 'CANCEL')
          {
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Appointment already Cancelled.');
            $(".text-danger").hide();
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
            return false;
          }

          
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[545,"cancel"])}}',
                      type:'POST',
                      data:{'APPOINTMENT_TRNID':APPOINTMENT_TRNID},
                      success:function(data) {                        
                        console.log("succes MSG="+data.msg);
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn1").hide();
                            $("#OkBtn").show();
                            $("#AlertMessage").text(data.msg);
                            $(".text-danger").hide();
                            $("#alert").modal('show');
                            $("#OkBtn").focus();
                            highlighFocusBtn('activeOk');
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                      },
                  });
    });

//no button
$("#NoBtn").click(function(){
    $("#alert").modal('hide');
    $("#txtpatientname").focus();
});

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $(".text-danger").hide();
    window.location.reload();
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
    $(".text-danger").hide();
    // window.location.href = '{{route("transaction",[545,"index"]) }}';
});

//
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


</script>
@endpush
