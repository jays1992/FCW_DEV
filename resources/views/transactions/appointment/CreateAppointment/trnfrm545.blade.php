@extends('layouts.app')
@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/css/bootstrap-colorpicker.min.css" rel="stylesheet">
<link href="{{ asset('fullcalendar/fullcalendar.min.css') }}" rel='stylesheet' /> 
<link href="{{ asset('fullcalendar/fullcalendar.print.css') }}" rel='stylesheet' media='print' />
<link href="{{ asset('fullcalendar/scheduler.min.css') }}" rel='stylesheet' />
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/js/bootstrap-colorpicker.min.js"></script>
<script src="{{ asset('js/nicEdit-latest.js') }}"></script>
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.js') }}"></script>
<script src="{{ asset('fullcalendar/jquery.min.js') }}"></script>
<script src="{{ asset('fullcalendar/jquery-ui.min.js') }}"></script>
<script src="{{ asset('fullcalendar/moment.min.js') }}"></script>
<script src="{{ asset('fullcalendar/fullcalendar.min.js') }}"></script>
<script src="{{ asset('fullcalendar/scheduler.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script> 
<script src="{{ asset('selectsearch/selectize.min.js') }}" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('js/bootstrapValidator.min.js') }}"></script>
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/additional-methods.min.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/select-table.js') }}"></script>
<script src="{{ asset('js/menu.js') }}"></script>
<script src="{{ asset('js/common.js') }}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.js"></script>

<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Add Appointments</a></div>
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
      <button id="divpopup" data-toggle="modal" style="display:none"  data-target="#modal_default" ></button>
      <div id="calendar"></div>
    </div>
  </div>
</div>
<div id="loading" class="loadings" ></div>
@endsection

<style>
.modal-body .row{
  margin-top:10px !important;
}
</style>

@section('alert')
<form id="frm_trn_appointment" method="POST" class="needs-validation">  
  <div id="modal_default" class="modal fade">
    <div class="modal-dialog" style="width:60%;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" onclick="resetLCD();">&times;</button>
          <div class="row">
            <div class="col-sm-6"> <h6 class="modal-title">Create Appointment  </h6> </div>
            <div class="col-sm-6" ><div class="loader" style="position:relative;top:-15px;display:none;"></div></div>
          </div>
        </div>

        <div class="modal-body">

          <div class="row">
            <input type="hidden" id="txtAppId" name="txtAppId"  /> 
            <div class="col-sm-2"><label>Franchise*</label></div>
            <div class="col-sm-4">
              <select id="GOID_REF" name="GOID_REF" class="form-control" tabindex="1" >
                <option>Select</option>
                @if($objfacility)
                  @foreach($objfacility as $findex=>$fRow)
                    <option value="{{$fRow->BRID}}" >{{$fRow->BRNAME}}</option>
                  @endforeach
                @endif
              </select>
            </div>
          </div>

          <div class="row">     
            <div class="col-sm-2"><label>Visit Type*</label></div>
            <div class="col-sm-4">
              <label>Office Visit</label>
              <input type="checkbox" id="APPOINTMENT_TYPE_1" name="APPOINTMENT_TYPE_1" tabindex="2" checked  />
            </div>
            <input type="hidden" id="APPOINTMENT_TYPE" name="APPOINTMENT_TYPE" value="Office Visit"  />
          </div>

          <div class="row">   
            <div class="col-sm-2"><label>Customer Name*</label></div>
            <div class="col-sm-4">
              <div id="targetDiv">
                <input type="text" id="txtpatientname" name="txtpatientname" autocomplete="off" placeholder="Search Customer name " class="form-control" tabindex= "3" >
                
              </div>
              <input type="hidden" id="PATIENTID_REF" name="PATIENTID_REF"  />
              <div id="dvoption" class="mrgnbtm" style="display:none;"></div>
            </div>
            <div class="col-sm-2">
              <a id="addPatient" name="addPatient" class="btn topnavbt pull-right"><i class="fa fa-plus"></i> Add Customer</a>

              
            </div>
            
          </div>

          <div class="row">     
            <div class="col-sm-2"><label>Date/Time*</label></div>
            <div class="col-sm-2">
              <input type="text" name="Appoint_Date" id="Appoint_Date" value="{{ old('Appoint_Date') }}"  class="form-control mrgnbtm date" tabindex= "4" placeholder="dd/mm/yyyy" />
            </div>
            <div class="col-sm-2">
              <input type="text" name="Appoint_Time" id="Appoint_Time" value="" placeholder="TIME" class="form-control mrgnbtm timepicker" tabindex= "5"  >
            </div>
          </div>

          <div class="row">  
            <div class="col-sm-2"><label>Type of Service*</label></div>
            <div class="col-sm-4" id="div_billing">
              <select id="BPID_REF" name="BPID_REF" class="form-control" tabindex="6" >
                <option>Select</option>
                @if($sp_provider)
                @foreach($sp_provider as $findex=>$fRow)
                <option value="{{$fRow->ITEMID}}" >{{$fRow->NAME}}</option>
                @endforeach
                @endif
              </select>
            </div>
          </div>

          <div class="row"> 
            <div class="col-sm-2"><label>Notes</label></div>
            <div class="col-sm-4">
              <textarea id="NOTES" name="NOTES" class="form-control" tabindex="7" maxlength="200" lines="5" ></textarea>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <a id="btnCount" class="btn btn-info" style="display:none">Go To Pill Count</a>
          <a id="btnDetails" class="btn btn-info" style="display:none">Go To Consultation Page</a>
          
          <button type="submit" id="btnSave"  class="btn topnavbt pull-right"><i class="fa fa-floppy-o"></i> Save</button> 
          <button type="button" class="btn topnavbt pull-right" data-dismiss="modal" onclick="resetLCD();" >Cancel</button> 
          <button type="button" class="btn topnavbt pull-right" id="print_appointment" style="display:none;" >Print</button> 

          <input type="hidden" id="hdnview" name="hdnview" />
          <input type="hidden" id="txtstartDate" name="txtstartDate" />
        </div>

      </div>
    </div>
  </div>
</form>

<!-- End Popup -->
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog modified_alert" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='closePopup' >&times;</button>
        <h4 class="modal-title">Alert Message</h4>
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
                <div id="alert-active" class="activeOk1"></div>OK</button>
			<button class="btn alertbt" name='OkBtn2' id="OkBtn2" style="margin-left: 90px;display:none;">
                <div id="alert-active" class="activeOk2"></div>OK</button>
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Alert -->

<!-- Print -->
<div id="ReportView" class="modal" role="dialog"  data-backdrop="static"  >
  <div class="modal-dialog modal-md" style="width:50%; height:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ReportViewclosePopup' >&times;</button>          
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Book Appointment Print</p></div>
        <div class="row">
          <div class="frame-container col-lg-12 pl text-center" >
                <button class="btn topnavbt" id="btnReport">
                    Print
                </button>
                <button class="btn topnavbt" id="btnPdf">
                    PDF
                </button>
                <button class="btn topnavbt" id="btnExcel">
                    Excel
                </button>
          </div>
        </div>
        
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <div class="inner-form">
              <div class="row">
                  <div class="frame-container col-lg-12 pl " >                      
                      <iframe id="iframe_rpt" width="100%" height="1000" >
                      </iframe>
                  </div>
              </div>
          </div>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Print-->
@endsection

@push('bottom-css')

<style>
.frame-container {
  position: relative;
}
.iframe-button {
  display: none;
  position: absolute;
  top: 15px;
  left: 850px;
  width:75px;
}
.iframe-button2 {
  display: none;
  position: absolute;
  top: 15px;
  left: 1025px;
  width:75px;
}
.my-custom-scrollbar {
    position: relative;
    height: 600px;
    overflow: auto;
}
.iframe-button3 {
  display: none;
  position: absolute;
  top: 15px;
  left: 750px;
  width:50px;
}
  #custom_dropdown, #custlist_filter {
      display: inline-table;
      margin-left: 15px;
  }
  .dataTables_wrapper .row:nth-child(1) .col-sm-6:nth-child(2){text-align:right;}
  #filtercolumn{color: #555;
      background-color: #fff;
      background-image: none;
      border: 1px solid #ccc;
      }
</style>

<style>
a:hover,a:focus{color:#23527c;text-decoration:underline}
a:focus{outline:5px auto -webkit-focus-ring-color;outline-offset:-2px}
.clinicinfo {
        float: left;
        margin: 0 0 0 80px;
        font-size: 14px;
    }
.fc-event, .fc-event:hover, .ui-widget .fc-event {
    color: #fff;
    text-decoration: none;
}
.fc-event {
    position: relative;
    display: block;
    font-size: .85em;
    line-height: 1.3;
    border-radius: 3px;
    border: 1px solid #3a87ad;
    background-color: #3a87ad;
    font-weight: 400;
}
.datetimepicker table tr td span {
    display: block;
    width: 23%;
    height: 40px;
    line-height: 40px;
    float: left;
    margin: 1%;
    cursor: pointer;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
}
 #dvoption {float:left;width:500px;
    }
    .loadings {
      margin:auto;
    position:absolute;
    top: 50%;
    left: 50%;
    width:13em;
    height:9em;
    margin-top: -9em; /*set to a negative number 1/2 of your height*/
    margin-left: -15em; /*set to a negative number 1/2 of your width*/
  /*  border: 1px solid #ccc;
    background-color: #f3f3f3;*/
}
    
#targetUL
{    width:80%;list-style: none;position: absolute;
background: #fff;
border: #bfbfbf solid 1px; height:80%; overflow:auto;
}
#targetUL li
{
margin-left: -40px;
border-bottom:0 solid silver;
height:24px;
padding-left:5px;
cursor:pointer; line-height:24px;
}
#targetUL li:hover
{  border-top:1px solid silver;
border-bottom:1px solid silver; background:#eee;
}
#li_cld a {
    background: #03a9f4;
} 
.fc-event-container a {
        z-index: 8 !important;
    }

.targetLI .autcname {
  width: 28%;
  float: left;
  padding: 1px 1px;
  border-right: #eee solid 1px;
}
 </style>

<style>
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

    function check()
    {
        var AppId = $('#txtAppId').val();
        var pname = $('#txtpatienname').val();
        var pId = $('#PATIENTID_REF').val();
        var enddate = $('#drpEndtime').val();
        var clinicId = $('#GOID_REF').val();
        var doctorId = $('#PROVIDERID_REF').val();
        var notes = $('#NOTES').val();
        var reason = $('#REASON').val();
        // var dt = $('#txtDatetime').val();

        $("#txtstartDate").val(dt);
       
        if (pId == "") {
            alert('Please select patient');
            $('#txtpatienname').focus();
            return false;
        }
        if (clinicId == "0") {
            alert('Please select clinic');
            $('#GOID_REF').focus();
            return false;
        }
        if (doctorId == "0") {
            alert('Please select doctor');
            $('#PROVIDERID_REF').focus();
            return false;
        }
        
        if (enddate == "0") {
            alert('Please select duration');
            $('#drpEndtime').focus();
            return false;
        }        
    }
    $(document).ready(function () {

 


      $('#TOTAL_AMT').ForceNumericOnly();
      $('#TOTAL_AMT').focusout(function(){
        var amt = $(this).val();
        if(intRegex.test(amt)){
          amt = amt+'.00';
        }
        $(this).val(amt);
      });

      $('#addPatient').on('click', function() {
          var viewURL = '{{route("master",[5,"add"])}}';
          window.location.href=viewURL;
      });

     
        //  Auto Complete Patient name Start
        $("#form1").submit(function (e) {

            $("#btnSave").attr("disabled", true);

            
            return true;

        });
        var target = $("#ddlpatient");
        target.empty();
     

   
        //  Auto Complete Patient name Start
    $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
        options.async = true;
    });
      
    var docid = '@ViewBag.doctorid';
        var clinicid = '@ViewBag.clinicid';

       /*  $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        $.ajax({
            type: "POST",
            url: '{{route("transaction",[545,"GetResources"])}}',
            contentType: "application/json; charset=utf-8",
            async: false,
            success: function (data) {
                if (data != null) {
                var opts = $.parseJSON(data);
                $('#doclist').show();
                $('#ddldoc').append(" <option value='0'>Select Doctor</option>");
                $.each(opts, function(i, d) {
                    // var doc = data[item];  
                    //  $('#doclist').append("<li><a href='#'>" + d.title + " " + d.lname + "<div class='doccolorbx' style='background:" + d.eventColor + "'></div></a></li>");
                    // $('#ddldoc').append(" <option  value='" + doc.id + "'>" + doc.title + " " + doc.lname + "</option>");
                  });
                }
                $('#ddldoc').val(docid);
            },
            fail: function (x, c) {
                alert("error");
            }
        }); */  
});
    $(document).ready(function () {

      $('#btnDetails').on('click', function() {
          var PatientId = $('#PATIENTID_REF').val();
          var AppointId = $('#txtAppId').val();
          var consultURL = '{{route("transaction",[545,"add",":rcdId"]) }}';
          var formdata = 'id='+ PatientId + '&AppointId='+ AppointId ;
          consultURL = consultURL.replace(":rcdId",formdata);
          window.location.href=consultURL;
          event.preventDefault();
      });
       
        $("#liAppoint").addClass("open");
        $("#liAppoint ul").show();
        
        $("#txtpatienname").keyup(function () {
            var query = $(this).val();
            getItems(query);
        });

        function getItems(query) {

            $.ajax({
                url: '@Url.Action( "RemoteData", "appointment")',
                data: { "query": query },
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    if (response.Data != null) {
                        if ($("#targetUL") != undefined) {
                            $("#targetUL").remove();
                        }
                        data = response.Data;
                        
                        $("#targetDiv").append($("<ul id='targetUL'></ul>"));
                        
                        $("#targetUL").find("li").remove();
                        
                        $.each(data, function (i, value) {
                            if (value.pid != 25278) {
                                
                                $("#targetUL").append($("<li class='targetLI' onclick='javascript:appendTextToTextBox(this," + value.pid + ")'><div class='autcname'>" + value.pname + "</div> <div class='autcname'> Ph:" + value.phone + "</div> <div class='autcname'>UID:" + value.uid + "</div></li>"));
                            }
                        });
                    }
                    else {
                        
                        $("#targetUL").find("li").remove();
                        $("#targetUL").remove();
                    }
                },
                error: function (xhr, status, error) {
                }
            });
        }
    });

    //This method appends the text oc clicked li element to textbox.
    function appendTextToTextBox(e,id) {
        //Getting the text of selected li element.
        var textToappend = e.innerText;
        var finaltext = textToappend.split(/Ph:/);
        $("#txtpatienId").val('');
        $("#txtpatienId").val(id);

        //setting the value attribute of textbox with selected li element.
        $("#txtpatienname").val(finaltext[0].trim());
        //Removing the ul element once selected element is set to textbox.
        $("#targetUL").remove();
        getOptions(id);

        $.ajax({
            type: "POST",
            url: '{{route("transaction",[545,"GetLastConsultingDoctor"])}}',
            // beforeSend: function () { $("#dvoption").show(); $("#dvoption").html("<img id='imgoption' src='/images/option.gif' />") },
            data: { 'PatientID': id },
            success: function (response) {
              if (response != null) {
                $("#tbl1").empty();
                var opts = $.parseJSON(response);                
                $.each(opts, function(i, d) {
                  $('#tbl1').append('<tr><td>' + d.Doctor+ '</td><td>' + d.cnt + ' time(s)</td></tr>');
                });
                $('#tbl1').css("visibility", " visible");
              }
            },
            fail: function (x, c) {
                alert("error");
            }
        });
    }


    function getOptions(id)
    {  
      // var date = $('#txtDatetime').val();  
      
        $.ajax({
            type: "POST",
            url: "/appointment/GetOption",
            beforeSend: function () { $("#dvoption").show(); $("#dvoption").html("<img id='imgoption' src='/images/option.gif' />") },
            data: { 'PatientID': id, 'AppDate': date },
            success: function (response) {
               // $("#dvoption").show();
                var option = "<b>Visit Type</b> : &nbsp; &nbsp; &nbsp; <input type='radio' checked  name='rdstatus' value='" + response.pat_status + "' />&nbsp;" + response.pat_status + " &nbsp; &nbsp;  &nbsp; ";

                if (response == "Follow-Up")
                {
                    option = option + "<input type='radio' name='rdstatus' value='Re-Entry' />&nbsp;Re-Entry &nbsp; &nbsp;  &nbsp;";
                }
                else if (response == "Re-Entry") {
                    option = option + "<input type='radio' name='rdstatus' value='Follow-Up' />&nbsp;Follow-Up &nbsp; &nbsp;  &nbsp;";
                }
                option = option + "<input type='radio' name='rdstatus' value='Test' />&nbsp;Lab Test &nbsp; &nbsp;  &nbsp;";
                option = option + "<input type='radio' name='rdstatus' value='PillCount' />&nbsp;Pill Count";
                $("#dvoption").html(option);
                $("#ddldoc").val(response.doc_slno);
                $('#btnSave').removeAttr("disabled");
            },
            fail: function (x, c) {
                alert("error");
            }
        });       
    }

    
       
    function resetLCD() {
        $('#tbl1').empty();
        $('#tbl1').css("visibility", " hidden");
        window.location.reload();
    }
    function PrintElem(elem) {
        Popup(jQuery(elem).html());
    }

    function Popup(data) {

        var mywindow = window.open('', 'my div', 'height=500,width=700');
        mywindow.document.write('<html><head><style>table,th,td{border:1px solid black;} table{border-collapse:collapse;} th:nth-child(9),td:nth-child(9),th:nth-child(11),td:nth-child(11),td:nth-child(12),th:nth-child(12){display: none }</style>');
        mywindow.document.write("</head><body><h1>" + $("#bdate").html() + "</h1></br>");
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.document.close();
        mywindow.print();
    }
  
</script>
 <script>
    $(document).ready(function() {
     
    $(document).on('click', 'li', function(){  
      var pname = $(this).find('#pname').text();
      var pid = $(this).find('#pid').text();
        $('#txtpatientname').val(pname);  
        $('#PATIENTID_REF').val(pid);  
        $('#dvoption').fadeOut();  

        var PATIENTID_REF = $('#PATIENTID_REF').val();
        var AppointDate   = moment($('#Appoint_Date').val()).format('DD/MM/YYYY');
            if(PATIENTID_REF != '')
            {
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
              /* $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
              });
              $.ajax({
                  url:'{{route("transaction",[545,"getBillingProfile"])}}',
                  type:'POST',
                  data:{'AppointDate':AppointDate},
                  success:function(result) {
                    $("#div_billing").html(result);
                  },
                  error:function(result){
                    console.log("Error: Something went wrong.");
                  },
                }); */
				
            }
    });
            $('.form_datetime').datetimepicker({
              weekStart: 1,
              todayBtn:  1,
              autoclose: 1,
              todayHighlight: 1,
              startView: 2,
              forceParse: 0,
              showMeridian: 1
          });
    });
    $(function () {
            function ini_events(ele) {
            ele.each(function () {
            var eventObject = {
            title: $.trim($(this).text()) // use the element's text as the event title
            };
            // store the Event Object in the DOM element so we can get to it later
            $(this).data('eventObject', eventObject);
            // make the event draggable using jQuery UI
            $(this).draggable({
            zIndex: 1070,
            revert: true, // will cause the event to go back to its
            revertDuration: 0  //  original position after the drag
            });
            });
            }
            ini_events($('#external-events div.external-event'));
            /* initialize the calendar
            ----------------------------------------------------*/
            //Date for the calendar events (dummy data)
            var date = new Date();
            var d = date.getDate(),
            m = date.getMonth()+1,
            y = date.getFullYear();

            var dfdate = y + "/" + m + "/" + d;
            var dc = '@TempData["date"]';
              
              if (dc == "")
              {
                dc = dfdate;
              }

              var sview = "agendaWeek";
              var view = '@TempData["View"]';

              if (view == "")
              {
                view = sview;
              }

        // page is now ready, initialize the calendar
        $('#calendar').fullCalendar({
          header: {
          left: 'prev,next today',
          center: 'title',
          right: 'agendaDay,agendaWeek,month'
          },
          defaultView: 'agendaWeek',
          // defaultDate: dc,
          editable: true,
          eventLimit: true, // allow "more" link when too many events
          schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
          views: {
              
          },
          timeFormat: {
              agenda: 'h:mm A',
              month: 'h:mm A'
              //h:mm{ - h:mm}'
          },
          slotDuration: '00:15:00',

          allDaySlot: false,

          ignoreTimezone: true,

          minTime: '08:00',
          maxTime: '23:30',

          viewDisplay: function (view) {    
              if (view.name == 'agendaWeek' || view.name == 'agendaDay') {
                  initFeature();
              }
          },
          events: '{{route("transaction",[545,"GetEvents"])}}',
    //      resources: '{{route("transaction",[545,"GetResources"])}}',
          loading: function (bool) {
              if (bool)
                  $('#loading').show();
              else
                  $('#loading').hide();
          },
          eventClick: function (event, jsEvent, view) {
              $("#txtAppId").val(event.id);
              ShowAppoint(event.id);
            
          },
          eventMouseover: function(calEvent, jsEvent) {  
              var durationTime = moment(calEvent.start).format('hh:mm A') + " - " + moment(calEvent.end).format('hh:mm A');
              var tooltip = '<div class="tooltipevent caltooltp"><b>' + durationTime + '</b><br/><table><tr><td style="width:55px;">Status</td><td>' + calEvent.Status + '</td></tr><tr><td>Provider</td><td>' + calEvent.DoctorName + '</td></tr><tr><td colspan="2">&nbsp;</td></tr><tr><td>Patient</td><td>' + calEvent.title + '</td></tr><tr><td>Phone</td><td>' + calEvent.Phone + '</td></tr><tr><td>Mobile</td><td>' + calEvent.Mobile + '</td></tr></table></div>';
              $("body").append(tooltip);
              $(this).mouseover(function(e) {
                  $(this).css('z-index', 10000);
                  $('.tooltipevent').fadeIn('500');
                  $('.tooltipevent').fadeTo('10', 1.9);
              }).mousemove(function(e) {
                  $('.tooltipevent').css('top', e.pageY + 10);
                  $('.tooltipevent').css('left', e.pageX - 172);
              });
          },

          eventMouseout: function(calEvent, jsEvent) {
              $(this).css('z-index', 8);
              $('.tooltipevent').remove();
          },

          select: function (start, end, allDay) {
              var title = prompt('Event Title:');
              if (title) {
                  calendar.fullCalendar('renderEvent',
                      {
                          title: title,
                          start: start,
                          end: end, 
                          allDay: allDay
                      },
                      true 
                  );
              }
              calendar.fullCalendar('unselect');
          },
            dayClick: function (date, jsEvent, view) {
              $("#targetUL").remove();
              $('#txtpatientname').val('');
              $('#hdnview').val(view.name);
              var nowDate = new Date();   
              $("#dvoption").html('');
              $("#txtAppId").val("");
              $('#PATIENTID_REF').val("");
            var groupoffice = <?php echo json_encode($objfacility[0]->BRID); ?>;   
			
            var divID = '#GOID_REF option[value='+groupoffice+']';
            $(divID).attr('selected', 'selected');              
              $('#PROVIDERID_REF').val("Select");
              $('#ROOMID_REF').val("Select");
              $("#dvoption").html('');
              $("#drpEndtime").val(120);
              $('#NOTES').val("");
              $('#REASON').val("");
			  $('#Appoint_Date').val(moment(date).format('MMM/DD/YYYY'));
			  $('#Appoint_Time').val(moment(date).format('HH:mm'));
              $('#divpopup').click();  
              $('#btnSave').removeAttr("disabled");
          },
          editable: false,
          droppable: false,
          });
  });
        
</script> 
<script>
var target = $("#drpevents");

function UpdateEvent(id, startdate, enddate, resId)
{

    var sdate = moment(startdate).format('MMM/DD/YYYY-h:mm a');
    var edate = moment(enddate).format('MMM/DD/YYYY-h:mm a');
    var RequestData = new FormData();
    RequestData.append("Appid", id);
    RequestData.append("StartDate", sdate);
    RequestData.append("EndDate", edate);
    RequestData.append("resId", resId);
	  $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
              });
      $.ajax({
        type: "POST",
        url: "/appointment/UpdateEvent",
        contentType: false,
        processData: false,
        data: RequestData,
         success: function (response) {
          
        },
        fail: function (x, c) {
            alert("error");
        }
    });

}

function ShowAppoint(id)
{
    $("#targetUL").remove();
    $('#txtpatienname').val('');
	$.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
              });
    $.ajax({
        type: "POST",
        url: '{{route("transaction",[545,"GetAppoint"])}}',        
        data: { 'Appid': id },            
        success: function (response) {
                if (response != null) {
                var opts = $.parseJSON(response);                
                $.each(opts, function(i, d) {


                  
                $("#print_appointment").hide();
                if(d.AppSlno !=''){
                  $("#print_appointment").show();
                }
                  

                $('#txtAppId').val(d.AppSlno);
                $('#PATIENTID_REF').val(d.PatientSlno);
                $('#txtpatientname').val(d.Patient_Name);
                // $('#txtDatetime').val(d.AptDateTime);
                $('#Appoint_Date').val(d.AppointmentDate);
                $('#Appoint_Time').val(moment(d.AptDateTime).format('HH:mm'));
				$('#BPID_REF').val(d.ITEMID_REF).attr("selected", "selected");
                $('#GOID_REF').val(d.Clinic_Name).attr("selected", "selected");
                $('#NOTES').val(d.Notes);
                $('#APPOINTMENT_TYPE').val(d.AptType);
                
                $('#btnDetails').hide();
                
                if(d.AptType == "OFFICE VISIT")
                {
                  $('#APPOINTMENT_TYPE_1').prop('checked',true);
                }else
                {
                  $('#APPOINTMENT_TYPE_1').prop('checked',false);
                }
                $("#dvoption").html('');
                // EditOptions(doc.Status);
                
				
            
                $('#btnSave').removeAttr("disabled");
           
            $('#divpopup').click();
            var GOID = d.Clinic_Name;
            var AppointID = d.AppSlno;
            
            
            });
          }
        },
        fail: function (x, c) {
            alert("error");
        }
    });

   var pid=  $('#PATIENTID_REF').val()
    /* $.ajax({
        type: "POST",
        url: '{{route("transaction",[545,"GetLastConsultingDoctor"])}}',
        data: { 'PatientID': pid },
        success: function (response) {
              if (response != null) {
                $("#tbl1").empty();
                var opts = $.parseJSON(response);                
                $.each(opts, function(i, d) {
                  $('#tbl1').append('<tr><td>' + d.Doctor+ '</td><td>' + d.cnt + ' time(s)</td></tr>');
                });
                $('#tbl1').css("visibility", " visible");
              }
        },
        fail: function (x, c) {
            alert("error");
        }
    }); */
}
</script>
<script>
$(document).ready(function() {
/*   $('#modal_default').on('change','#BPID_REF',function(){
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
      
      $('#modal_default').on('change','#APPOINTMENT_TYPE_1',function(e){
        if($(this).is(":checked") == true)
        {
          $('#APPOINTMENT_TYPE_2').prop('checked',false);
          $('#APPOINTMENT_TYPE_3').prop('checked',false);
          $('#APPOINTMENT_TYPE').val('Office Visit');
        }
      });
      $('#modal_default').on('change','#APPOINTMENT_TYPE_2',function(e){
        if($(this).is(":checked") == true)
        {
          $('#APPOINTMENT_TYPE_1').prop('checked',false);
          $('#APPOINTMENT_TYPE_3').prop('checked',false);
          $('#APPOINTMENT_TYPE').val('Video');
        }
      });
      $('#modal_default').on('change','#APPOINTMENT_TYPE_3',function(e){
        if($(this).is(":checked") == true)
        {
          $('#APPOINTMENT_TYPE_2').prop('checked',false);
          $('#APPOINTMENT_TYPE_1').prop('checked',false);
          $('#APPOINTMENT_TYPE').val('Tele Consult');
        }
      });
      $('#modal_default').on('change','#Visit_TYPE_1',function(e){
        if($(this).is(":checked") == true)
        {
          $('#Visit_TYPE_2').prop('checked',false);
          $('#Visit_TYPE_3').prop('checked',false);
          $('#Visit_TYPE_4').prop('checked',false);
          $('#VISIT_TYPE').val('New');
        }
      });
      $('#modal_default').on('change','#Visit_TYPE_2',function(e){
        if($(this).is(":checked") == true)
        {
          $('#Visit_TYPE_1').prop('checked',false);
          $('#Visit_TYPE_3').prop('checked',false);
          $('#Visit_TYPE_4').prop('checked',false);
          $('#VISIT_TYPE').val('Re-Entry');
        }
      });
      $('#modal_default').on('change','#Visit_TYPE_3',function(e){
        if($(this).is(":checked") == true)
        {
          $('#Visit_TYPE_1').prop('checked',false);
          $('#Visit_TYPE_2').prop('checked',false);
          $('#Visit_TYPE_4').prop('checked',false);
          $('#VISIT_TYPE').val('Follow-Up');
        }
      });
      $('#modal_default').on('change','#Visit_TYPE_4',function(e){
        if($(this).is(":checked") == true)
        {
          $('#Visit_TYPE_1').prop('checked',false);
          $('#Visit_TYPE_2').prop('checked',false);
          $('#Visit_TYPE_3').prop('checked',false);
          $('#VISIT_TYPE').val('Test');
        }
      });*/
      $('#modal_default').on('keyup','#txtpatientname',function(e){
        var query = $(this).val();
		query = query.toLowerCase();
        if(query != '')
        {
          $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
          $.ajax({
          url:'{{route("transaction",[545,"getpatientdetails"])}}',
          method:"POST",
          data:{'query':query},
          success:function(data){
            $('#dvoption').fadeIn();  
                    $('#dvoption').html(data);
          }
          });
        }
    });



    



    
    $('#modal_default').on('change','#BPID_REF',function(e){
      //alert("aaa"); 
          var GOID = $("#GOID_REF").val();
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
        });
    $('#modal_default').on('change','#PROVIDERID_REF',function(e){
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
					$("#OkBtn2").hide();
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
					$("#OkBtn2").hide();
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
    // });
    $('#modal_default').on('change','#drpEndtime',function(e){
      var PATIENTID_REF = $('#PATIENTID_REF').val(); 
      var Appoint_Date          =  moment($('#Appoint_Date').val()).format('DD/MM/YYYY');
      var Appoint_Time          =   $('#Appoint_Time').val();
      var PROVIDERID_REF = $('#PROVIDERID_REF').val(); 
        if(PATIENTID_REF!=''){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[545,"codeduplicate"])}}',
            type:'POST',
            data:{'PATIENTID_REF':PATIENTID_REF,'Appoint_Date':Appoint_Date,'Appoint_Time':Appoint_Time},
            success:function(data) {
                if(data.exists) {                   
                    console.log("cancel MSG="+data.msg);
                    $("#FocusId").val($("#txtpatientname"));
                    $('#txtpatientname').val('');
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").hide();
					$("#OkBtn2").hide();
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
					$("#OkBtn2").hide();
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
					$("#OkBtn2").hide();
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
  });
$( "#btnSave" ).click(function() {
  event.preventDefault();
  var formCreateAppointment = $("#frm_trn_appointment");
  if(formCreateAppointment.valid()){
      $("#FocusId").val('');

        var PATIENTID_REF         =   $('#PATIENTID_REF').val(); 
        var Appoint_Date          =   moment($('#Appoint_Date').val()).format('DD/MM/YYYY');
        var Appoint_Time          =   $('#Appoint_Time').val();
     
      // event.preventDefault();
      
      var PATIENTID_REF          =   $("#PATIENTID_REF").val();
      
      var Appoint_Date          =   $('#Appoint_Date').val();
      var Appoint_Time          =   $('#Appoint_Time').val();
      var GOID_REF              =   $("#GOID_REF").val();

      if(PATIENTID_REF ===""){
          $("#FocusId").val($("#txtpatientname"));
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").hide();
          $("#OkBtn1").show();
		  $("#OkBtn2").hide();
          $("#AlertMessage").text('Please enter value in Customer Name.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
      }  
      else if(GOID_REF ==="Select"){
          $("#FocusId").val($("#GOID_REF"));
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please select Franchise Type.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
      } 
      else{

            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
            $("#YesBtn").focus();
            $("#OkBtn").hide();
			$("#OkBtn2").hide();
            $("#OkBtn1").hide();
            $("#YesBtn").show();
            $("#NoBtn").show();
            highlighFocusBtn('activeYes');
            $('#YesBtn').trigger('click');

             
      }
  }
});

window.fnSaveData = function (){
  event.preventDefault();
  var formCreateAppointment = $("#frm_trn_appointment");
  var formData = formCreateAppointment.serialize();
  $(".loader").show();

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $("[id*=btnSave]").attr("disabled", true);
  $.ajax({
      url:'{{ route("transaction",[545,"save"])}}',
      type:'POST',
      dataType: 'json',
      data: formData,
      success:function(data) {
        $("[id*=btnSave]").attr("disabled", false);

          $(".loader").hide();

          if(data.errors) {
              $(".text-danger").hide();
            if(data.country=='norecord') {
              $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").hide();
                $("#OkBtn").show();
				$("#OkBtn2").hide();
                $("#AlertMessage").text(data.msg);
                $("#alert").modal('show');
                $("#OkBtn1").focus();
            }
            if(data.save=='invalid') {
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").hide();
                $("#OkBtn").show();
				$("#OkBtn2").hide();
                $("#AlertMessage").text(data.msg);
                $("#alert").modal('show');
                $("#OkBtn").focus();
            }
          }
          if(data.success) {                   
              console.log("succes MSG="+data.msg);
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").hide();
			  $("#OkBtn").hide();
              $("#OkBtn2").show();
              $("#AlertMessage").text(data.msg);
              $(".text-danger").hide();
              $("#alert").modal('show');
              $("#OkBtn2").focus();
              highlighFocusBtn('activeOk2');
              $('#modal_default').hide();
          }
          else if(data.cancel) {                   
              console.log("cancel MSG="+data.msg);
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").hide();
              $("#OkBtn").show();
			  $("#OkBtn2").hide();
              $("#AlertMessage").text(data.msg);
              $(".text-danger").hide();
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk1');
              $('#modal_default').hide();
          }
          else 
          {                   
              console.log("succes MSG="+data.msg);
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").hide();
              $("#OkBtn").show();
			  $("#OkBtn2").hide();
              $("#AlertMessage").text(data.msg);
              $(".text-danger").hide();
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk1');
              $('#modal_default').hide();
          }
      },
      error:function(data){
        $("[id*=btnSave]").attr("disabled", false);
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
	$("#OkBtn2").hide();
    $("#OkBtn1").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("transaction",[545,"index"]) }}';
});

$("#OkBtn2").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
	$("#OkBtn2").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("transaction",[545,"view"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
	$("#OkBtn2").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
    $(".text-danger").hide();
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
<script type="text/javascript">
$(function(){
  $(".date").datepicker({ dateFormat: "M/dd/yy",minDate: 0 }).val();
  // $("#Appoint_Time").timepicker({ use24hours: true}).val();
  $('.timepicker').datetimepicker({

format: 'HH:mm'

}); 

}); 
</script> 


<script>
$('#print_appointment').on('click', function() {

  var APPOINTMENT_TRNID = $("#txtAppId").val();

  if(APPOINTMENT_TRNID ===''){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Appointment record not exist.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
  }
  else{

    var Flag = 'H';
    var formData = 'APPOINTMENT_TRNID='+ APPOINTMENT_TRNID + '&APPOINTMENT_TRNID='+ APPOINTMENT_TRNID + '&Flag='+ Flag ;

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      url:'{{route("transaction",[$FormId,"ViewReport"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
        $('#ReportView').show();
        var localS = data;
        document.getElementById('iframe_rpt').src = "data:text/html;charset=utf-8," + escape(localS);
        $('#btnPdf').show();
        $('#btnExcel').show();
        $('#btnPrint').show();
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        var localS = "";
        document.getElementById('iframe_rpt').src = "data:text/html;charset=utf-8," + escape(localS);
        $('#btnPdf').hide();
        $('#btnExcel').hide();
        $('#btnPrint').hide();
      },
    });
    event.preventDefault();
  }

});


$('#btnPdf').on('click', function() {
  var APPOINTMENT_TRNID = $("#txtAppId").val();

  if(APPOINTMENT_TRNID ===''){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Appointment record not exist.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
  }
  else{
    var Flag = 'P';
    var formData = 'APPOINTMENT_TRNID='+ APPOINTMENT_TRNID + '&APPOINTMENT_TRNID='+ APPOINTMENT_TRNID + '&Flag='+ Flag ;
    var consultURL = '{{route("transaction",[$FormId,"ViewReport",":rcdId"]) }}';
    consultURL = consultURL.replace(":rcdId",formData);
    window.location.href=consultURL;
    event.preventDefault();
  }
}); 

$('#btnExcel').on('click', function() {
  var APPOINTMENT_TRNID = $("#txtAppId").val();

  if(APPOINTMENT_TRNID ===''){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Appointment record not exist.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');
  }
  else{
    var Flag = 'E';
    var formData = 'APPOINTMENT_TRNID='+ APPOINTMENT_TRNID + '&APPOINTMENT_TRNID='+ APPOINTMENT_TRNID + '&Flag='+ Flag ;
    var consultURL = '{{route("transaction",[$FormId,"ViewReport",":rcdId"]) }}';
    consultURL = consultURL.replace(":rcdId",formData);
    window.location.href=consultURL;
    event.preventDefault();
  }
});

$('#btnReport').on('click', function() {
  var APPOINTMENT_TRNID = $("#txtAppId").val();

  if(APPOINTMENT_TRNID ===''){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Appointment record not exist.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');
  }
  else{
        
    var Flag = 'R';
    var formData = 'APPOINTMENT_TRNID='+ APPOINTMENT_TRNID + '&APPOINTMENT_TRNID='+ APPOINTMENT_TRNID + '&Flag='+ Flag ;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        url:'{{route("transaction",[$FormId,"ViewReport"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
            printWindow = window.open('');
            printWindow.document.write(data);
            printWindow.print();
        },
        error:function(data){
            console.log("Error: Something went wrong.")
            printWindow = window.open('');
            printWindow.document.write("Error: Something went wrong.");
            printWindow.print();
        },
    });
    event.preventDefault();
  }
});

$("#ReportViewclosePopup").click(function(event){
  $("#ReportView").hide();
  event.preventDefault();
});

function printFrame(id) {
  var frm = document.getElementById(id).contentWindow;
  frm.focus();// focus on contentWindow is needed on some ie versions
  frm.print();
  return false;
}
</script>


<script>
$(document).ready(function(){
  $("#alertsDropdown").click(function(){
    $(".navfonts").toggle();
  });
});
</script>
@endpush
