@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'view'])}}" class="btn singlebt">View Appointments</a></div>
    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
      <button class="btn topnavbt" id="btnSaveFormData" disabled="disabled" onclick="saveAction('save')" ><i class="fa fa-floppy-o"></i> Save</button>
      <button style="display:none" class="btn topnavbt buttonload" disabled="disabled"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" disabled="disabled" onclick="saveAction('approve')"><i class="fa fa-thumbs-o-up"></i> Approved</button>
      <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
      <button class="btn topnavbt" id="btnExit" onclick="return  window.location.href='{{route('home')}}'" ><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>


<div class="container-fluid filter">
  @csrf
  <div class="inner-form">
    <div class="row">
    <form id="frm_trn_viewappointment"   method="POST" class="needs-validation">
        <div id="div1" class="table-wrap appointmentsdetailsc">
        
          <div class="home-table table-responsive">
            <table class="table table-striped table-bordered" style="width:100%">
            <thead>
            <tr>
                <td>
                  <button type="button" class="btn btn-default btn-group-sm" id="btnrefresh">
                  <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
                  </button>
                </td>
                <td hidden>
                  <select name="GOID_REF" id="GOID_REF" class="form-control " style="display:inline-block;margin:0;">
                    <option value="">Select</option>
                  </select>
                </td>
                <td id="div_provider" hidden>
                  <select id="PROVIDERID_REF" name="PROVIDERID_REF" class="form-control " style="display:inline-block;margin:0;">
                      <option value="">Select</option>
                  </select>
                </td>
                
                <td colspan="2" align="right" >
                   <div hidden> <input type="date" id="hdndate" name="hdndate" VALUE="{{$DATE}}"/> </div>
                    <div class="btn-group bluetxt" role="group" aria-label="Basic example" style="margin:0;">
                        
                        <button type="button" class="btn btn-default trigger" style="z-index:1;"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></button>
                        <input type="text" id="datepicker" name="datepicker" class="Datepicker" hidden>
                        <button type="button" class="btn btn-default" id="btnprev" onclick="return ShowGrid('p')"><span class="glyphicon glyphicon-triangle-left" aria-hidden="true"></span></button>
                        <button type="button" class="btn btn-default" id="btnnext" onclick="return ShowGrid('n')"><span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span></button>
                        <button type="button" class="btn btn-default" id="btncurrent" onclick="return ShowGrid('c')">Today</button> 
                    </div>
                </td>
              </tr>
            </thead>
            </table>
            <table id="listing" class="table table-striped table-bordered" style="width:100%">
              <thead id="thead1">
              
              <!-- <tr><label> </label></tr> -->
              <tr>
                <th>Customer</th>
                <th>Date</th>
                <th>Time</th>
				<th>Appointment Type</th>
                <th>Notes</th>
                <!-- <th>Action</th> -->
        
              </tr> 
                        
              </thead>
              <tbody id="divdata"> 
              @if(!empty($sp_provider))           
              @foreach($sp_provider as $key => $val)
                <tr>
                  <td>
                  <button class="btn btn-editbutton" id="EditId_{{ $val->id }}" value="{{ $val->PatientId }}"> {{ $val->PatientName }} </button>
                  <br>
                  @if($val->Mobile !== '')
                  <a id="PatientCell{{ $val->id }}" value="{{$val->Mobile }}" ><i class="fa fa-mobile">&nbsp;</i>{{$val->Mobile }}</a><br/>
                  @else
                  <i class="fa fa-mobile">&nbsp;</i>{{$val->Mobile }}<br/>
                  @endif
                  

        
                <!--  <button class="btn btn-editbutton" id="Billbooking{{ $val->id }}" value="{{ $val->PatientId }}">
                  <i class="fa fa-edit"></i> Check In </button><br/>
                  <button class="btn btn-editbutton" id="MakePayment{{ $val->id }}" value="{{ $val->PatientId }}">
                  <i class="fa fa-edit"></i>Payment </button><br/>-->

                  <a href="javascript:void(0);" onclick="resendsmsemail('{{$val->id}}')" style="cursor:pointer;" >Resend Email</a><br/>
                  </td>
                  <td>{{$val->AppointDate}}</td>
                  <td>{{$val->AppointTime}}</td>
                  <td>{{$val->APPOINTMENT_TYPE}}</td>
                  <td>{{$val->Notes}}</td>

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
  <div class="modal-dialog">
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

@push('bottom-css')
<style>
.glyphicon {
    position: relative;
    top: 1px;
    display: inline-block;
    font-family: 'Glyphicons Halflings';
    font-style: normal;
    font-weight: normal;
    line-height: 1;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.loader {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 20px;
  height: 20px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
}
/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
@endpush

@push('bottom-scripts')
<script>
$(document).ready(function(){
    var mstresultTable =  $('#listing').DataTable({
      "lengthMenu": [ 10, 20, 30, 50, 100],
      "iDisplayLength": 100,
    });
});




function resendsmsemail(recordId){

  var result = confirm("Are you sure you want to resend email/sms?");
  if (result) {
        
      $(".loader").show();

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      $.ajax({
        url:'{{ route("transaction",[545,"resendsmsemail"])}}',
        type:'POST',
        data: {APPOINTMENT_TRNID:recordId},
        success:function(data) {

            $(".loader").hide();
            if(data.success) {                   
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").hide();
              $("#OkBtn").show();
              $("#AlertMessage").text(data.msg);
              $(".text-danger").hide();
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
            }
        },
        error:function(data){
            console.log("Error: Something went wrong.");
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").hide();
            $("#OkBtn").show();
            $("#OkBtn2").hide();
            $("#AlertMessage").text('Error: Something went wrong.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
        },
    });
  }
}


$(document).ready(function(e) {
  var today = new Date(); 
  var currentdate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
 
  var test = <?php echo json_encode($sp_provider); ?>;
  
  

  $(function () {
   $('.datepicker').datepicker({
    dateFormat: "dd/mm/yy",
    changeMonth: true,
    changeYear: true
   });
});

   $('#datepicker').datepicker({
    changeMonth: true,
    changeYear: true,
    onSelect: function (selected, evnt) {
      var today = new Date(selected); 
      var currentdate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
      $("#hdndate").val(currentdate);
      $("#datepicker").val('');
      ShowGrid("k");
    }
});

$(".trigger").click(function () { 
  $("#datepicker").show();
  $(".trigger").hide();
   });

var today = new Date(); 
var currentdate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
  // $('#hdndate').val(currentdate);
  
    

        


$("#fiterStatus").change(function () {
    $("#hdnstatus").val($(this).val());
    ShowGrid("k");
});

});
  function ShowGrid(type) {
        var pageid = '0';
     
        if (type == 'p') {
            var date = new Date($("#hdndate").val());
           
            date.setDate(date.getDate() - 1);
            var gt = date.getFullYear() + "-" + ("0" + (date.getMonth() + 1)).slice(-2) + "-" + ('0' + date.getDate()).slice(-2) ;
            $("#hdndate").val(gt);
            $("#fiterStatus").val("");
        }

        if (type == 'n') {
            var date = new Date($("#hdndate").val());
            date.setDate(date.getDate() + 1);
            var gt = date.getFullYear() + "-" + ("0" + (date.getMonth() + 1)).slice(-2) + "-" + ('0' + date.getDate()).slice(-2) ;
            $("#hdndate").val(gt);
            $("#fiterStatus").val("");
        }

        if (type == 'c') {
            var date = new Date();
            date.setDate(date.getDate());
            var gt = date.getFullYear() + "-" + ("0" + (date.getMonth() + 1)).slice(-2) + "-" + ('0' + date.getDate()).slice(-2) ;
            $("#hdndate").val(gt);
            $("#fiterStatus").val("");
        }
        if (type == 'k') {
            var date = new Date($("#hdndate").val());
            date.setDate(date.getDate());
            var gt = date.toDateString();
        }
      
        event.preventDefault();
            var viewappointment = $("#frm_trn_viewappointment");
            var hdndate = $("#hdndate").val();
            //var PROVIDERID_REF = $('#PROVIDERID_REF').val();
          //  var fiterStatus = $('#fiterStatus').val();
            var formData = 'hdndate='+ hdndate  ;
            // var formData = viewappointment.serialize();
            var editURL = '{{route("transaction",[545,"viewfilter",":rcdId"]) }}';
            editURL = editURL.replace(":rcdId",formData);
            window.location.href=editURL;
            event.preventDefault();
    }

    $("#YesBtn").click(function(){
$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();
}); //yes button



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
